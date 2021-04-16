<?php

class CadInfoControleFalhasDAO
{
	protected $_adapter;
	
	public function __construct()
	{
		global $conn;		
		$this->_adapter = $conn;
	}
	
	/**
	 * Executa uma query
	 * @param	string		$sql		SQL a ser executado
	 * @return	resource
	 */
	protected function _query($sql)
	{
		return pg_query($this->_adapter, $sql);
	}
	
	/**
	 * Conta os resultados de uma consulta
	 * @param	resource	$results
	 * @return	int
	 */
	protected function _count($results)
	{
		return pg_num_rows($results);
	}
	
	/**
	 * Retorna os resultados de uma consulta num array associativo (hash-like)
	 * @param	resource	$results
	 * @return	array
	 */
	protected function _fetchAll($results)
	{
		return pg_fetch_all($results);
	}
	
	/**
	 * Retorna o resultado de uma coluna num array associativo (hash-like)
	 * @param	resource	$results
	 * @return	array
	 */
	protected function _fetchAssoc($result)
	{
		return pg_fetch_assoc($result);
	}
	
	/**
	 * Insere valores numa tabela
	 * @param	string	$table
	 * @param	array	$values
	 * @return	boolean
	 */
	protected function _insert($table, $arr)
	{
		return pg_insert($this->_adapter, $table, $arr);
	}
	
	/**
	 * Escapa os elementos de um vetor
	 * @param	array	$arr
	 * @return	array
	 */
	protected function _escapeArray($arr)
	{
		/* array_walk($arr, function(&$item, $key) {
			$item = pg_escape_string($item);
		}); */
		
		return $arr;
	}
	
	
	
	
	
	
	
	
	/*
	 * Código real abaixo, código boilerplate acima. Poderia ser abstraído em
	 * com herança.
	 */
	
	
	
	
	
	/**
	 * Insere um novo item de controle de falha
	 * @param	array	$arr
	 * @return 	boolean
	 */
	public function inserir($arr)
	{
		// item_falha_acao
		if ($arr['item_falha_id'] == 1)
		{
			$arr = array(
				'ifaeproid' 		=> $arr['item_produto_id'],
				'ifadescricao' 	=> $arr['item_descricao'],
				'ifadt_cadastro'	=> 'NOW()'
			);
			
			$flag = $this->_insert('item_falha_acao', $arr);
		}
		// item_falha_componente
		else if ($arr['item_falha_id'] == 2)
		{
			$arr = array(
				'ifceproid' 		=> $arr['item_produto_id'],
				'ifcdescricao' 	=> $arr['item_descricao'],
				'ifcdt_cadastro'	=> 'NOW()'
			);
			
			$flag = $this->_insert('item_falha_componente', $arr);
		}
		// item_falha_defeito
		else if ($arr['item_falha_id'] == 3)
		{
			$arr = array(
				'ifdeproid' 		=> $arr['item_produto_id'],
				'ifddescricao' 	=> $arr['item_descricao'],
				'ifddt_cadastro'	=> 'NOW()'
			);
			
			$flag = $this->_insert('item_falha_defeito', $arr);
		}
		
		if ($flag)
		{
			return $flag;
		}
		else
		{
			throw new Exception('Erro ao inserir registro.');
		}
	}
	
	/**
	 * Busca lista de equipamentos
	 * @return	array
	 */
	public function getListaEquipamentos()
	{
		$sql = "SELECT eproid, eprnome
				FROM equipamento_projeto
				ORDER BY eprnome ASC";
		
		$query = $this->_query($sql);
		
		return $this->_fetchAll($query);
	}
	
	/**
	 * Busca lista de falhas
	 * @return	array
	 */
	public function getListaFalhas()
	{
		$falhas = array(
			1	=> 'Ação Lab.',
			2	=> 'Componente Afetado',
			3	=> 'Defeito Lab.'				
		);
		
		return $falhas;
	}
	
	/**
	 * Busca lista de elementos de controle de falhas cadastrados
	 * @param	array	$arr
	 * @return	array
	 */
	public function getListaControleFalhas($arr)
	{
		$arr = $this->_escapeArray($arr);				
		
		if (!strlen($arr['item_falha_id']))
		{
			throw new Exception('Deve ser informado o item falha.');
		}
		
		if (!strlen($arr['item_produto_id']))
		{
			throw new Exception('Deve ser informado o equipamento.');
		}
		
		// item_falha_acao
		if ($arr['item_falha_id'] == 1)
		{
			$sql = "
				SELECT
					ifaoid AS item_id,
					ifadescricao AS item_descricao
				FROM item_falha_acao
				WHERE
					ifaeproid = {$arr['item_produto_id']}
					AND ifadt_exclusao IS NULL ";
				
			if (strlen($arr['item_descricao']))
			{
				$sql .= "AND ifadescricao ILIKE '%{$arr['item_descricao']}%'";
			}
		}
		// item_falha_componente (lab)
		else if ($arr['item_falha_id'] == 2)
		{
			$sql = "
				SELECT
					ifcoid AS item_id,
					ifcdescricao AS item_descricao
				FROM item_falha_componente
				WHERE
					ifceproid = {$arr['item_produto_id']}
					AND ifcdt_exclusao IS NULL ";
			
			if (strlen($arr['item_descricao']))
			{
				$sql .= "AND ifcdescricao ILIKE '%{$arr['item_descricao']}%'";
			}
		}
		// Se for um defeito lab selecionado
		else if ($arr['item_falha_id'] == 3)
		{
			$sql = "
				SELECT
					ifdoid AS item_id,
					ifddescricao AS item_descricao
				FROM item_falha_defeito
				WHERE
					ifdeproid = {$arr['item_produto_id']}
					AND ifddt_exclusao IS NULL ";
				
			if (strlen($arr['item_descricao']))
			{
				$sql .= "AND ifddescricao ILIKE '%{$arr['item_descricao']}%'";
			}
		}		
		
		if (($results = $this->_query($sql)) == false)
		{
			throw new Exception('Erro ao pesquisar registro.');
		}
		
		return $this->_fetchAll($results);
	}
	
	/**
	 * Deleta itens de uma lista de IDs
	 * @param	array	$ids
	 * @return	boolean
	 */
	public function deletarItem($ids, $itemFalhaId)
	{
		if (is_array($ids))
		{
			$ids = implode(',', $ids);
		}
		
		// Seta os dados da tabela a ser atualizada
		if ($itemFalhaId == 1)
		{
			$data = array(
				'table'		=> 'item_falha_acao',
				'col_exclusao'	=> 'ifadt_exclusao',
				'col_oid'		=> 'ifaoid'
			);
		}
		else if ($itemFalhaId == 2)
		{
			$data = array(
				'table'		=> 'item_falha_componente',
				'col_exclusao'	=> 'ifcdt_exclusao',
				'col_oid'		=> 'ifcoid'
			);
		}
		else if ($itemFalhaId == 3)
		{
			$data = array(
				'table'		=> 'item_falha_defeito',
				'col_exclusao'	=> 'ifddt_exclusao',
				'col_oid'		=> 'ifdoid'
			);
		}
		
		// Substitui variáveis na query
		$sql = "UPDATE {$data['table']}
				SET {$data['col_exclusao']} = NOW() 
				WHERE {$data['col_oid']} IN ($ids)";
		
		if ($this->_query($sql))
		{
			return true;
		}
		else
		{
			throw new Exception('Erro ao excluir registro(s).');
		}
	}
	
	/**
	 * Busca a descrição do item_falha selecionado
	 * @param	int		$id
	 * @return	array
	 */
	public function getItemFalha($id)
	{
		$falhas = $this->getListaFalhas();
		
		return $falhas[$id];
	}
}