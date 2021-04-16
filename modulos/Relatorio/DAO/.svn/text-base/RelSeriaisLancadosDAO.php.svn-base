<?php

class RelSeriaisLancadosDAO {

	private $conn;

	public function __construct($conn) {
		$this->conn = $conn;
	}
	
	/**
	 * [Retorna lista de estados]
	 * @return [type] [description]
	 */
	public function estados() {

		$rs = null;
		try{
			$sql = "SELECT 
						ufoid, 
						ufuf
                    FROM 
                    	uf
                    ORDER BY ufuf";
			
			if (!$rs = pg_query( $this->conn, $sql )) {
				throw new Exception ("Erro ao efetuar a consulta dos estados");
			}

			return pg_fetch_all($rs);
		}catch(Exception $e){
			throw new Exception ("Problemas para efetuar consulta de estados");
		}
	}
	
	/**
	 * [Busca as cidades passando a sigla do estado]
	 * @param  [type] $estado [description]
	 * @return [type]         [description]
	 */
	public function buscaCidadesSiglaEstado($estado){

		try{
	
			$sql = "SELECT
						cidoid,
						ciddescricao
					FROM
						cidade
					WHERE
						ciduf = '$estado'
					ORDER BY ciddescricao";

			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception ("Erro ao efetuar a consulta de cidades");
			}

			return pg_fetch_all($rs);

		}catch(Exception $e){
			throw new Exception ("Erro ao efetuar a consulta de cidades");
		}
	}
	
	/**
	 * [Retorna lista de representantes]
	 * @return [type] [description]
	 */
	public function representantes(){

		$result = null;

		try{
			$sql = "SELECT 
						repoid,
						repnome
             	 	FROM 
             	 		representante
         	 		WHERE 
         	 			repexclusao IS NULL
					ORDER BY repnome; ";
		
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception ("Erro ao efetuar a consulta de representantes");
			}

			return pg_fetch_all($result);
		}catch(Exception $e){
			throw new Exception ("Problemas para efetuar consulta de representantes");
		}
	}
	
	/**
	 * [Retorna dados da pesquisa]
	 * @param  [type] $parametros [description]
	 * @param  [type] $paginacao  [description]
	 * @return [type]             [description]
	 */
	public function pesquisar($parametros,$paginacao = null){
		
		try{
			
			$select = " produto.prdproduto,
						imobilizado.imobserial,
						st1.imsdescricao,
						st2.imsdescricao,
						imobilizado.imobimotoid,
						inventario.invdt_ajuste,
						representante.repnome,
						representante.repoid,
						relacionamento_representante.relroid,
						imobilizado_tipo.imottabela_modelo,
						imobilizado_tipo.imotcampo_modelo,
						imobilizado_tipo.imottabela_secundaria,
						imobilizado_tipo.imotprefixo_tabela_secundaria,
						imobilizado_tipo.imotcampo_serial,
						imobilizado_tipo.imotmodelo,
						inventario.invoid,
						produto.prdoid,
						produto_custo_medio.pcmcusto_medio ";

			$filtro = '';

			if(!isset($paginacao)) {
				$select = " COUNT(invpoid) AS total_registros ";
			}

			if($parametros->data_inicial != '' && $parametros->data_final != '') {
				if($filtro != '') {
					$filtro .= ' AND ';
				}

				$filtro .= " inventario.invdt_ajuste BETWEEN '".$parametros->data_inicial." 00:00:00' AND  '".$parametros->data_final." 23:59:59' ";
			}
				
			if($parametros->representante != null && $parametros->representante != ''){
				
				if($filtro != '') {
					$filtro .= ' AND ';
				}

				$filtro .= " representante.repoid = '".$parametros->representante."' ";
			}	
			
			if($parametros->seriaisDuplicados != null && $parametros->seriaisDuplicados != '') {
				if($filtro != '') {
					$filtro .= ' AND ';
				}
				$filtro .= " ipsserial IN (".$parametros->seriaisDuplicados.") ";
			}

			if($parametros->estado != null && $parametros->estado != '') {
				if($filtro != '') {
					$filtro .= ' AND ';
				}

				$filtro .= " endereco_representante.endvuf = '".$parametros->estado."' "; 
			}
			
			if($parametros->cidade != ''){
				if($filtro != '') {
					$filtro .= ' AND ';
				}
				$filtro .= " endereco_representante.endvcidade = '".$parametros->cidade."' ";
			}

			$sql = "SELECT ".$select;

			$sql .= " FROM   
						inventario_produto 
					INNER JOIN inventario 
					ON         invpinvoid = invoid
					INNER JOIN representante 
					ON         invrepoid = repoid
					LEFT JOIN  endereco_representante 
					ON         repoid = endvrepoid
				 	INNER JOIN produto 
					ON         prdoid=invpprdoid
					INNER JOIN inventario_produto_serial 
					ON         (ipsinvpoid=invpoid AND ipsexclusao IS NULL)
					INNER JOIN imobilizado 
					ON         (imobserial = ipsserial AND prdoid = imobprdoid)  
					LEFT JOIN  inventario_estoque_serial 
					ON         (iesinvoid = invpinvoid AND iesimoboid = imoboid)
					LEFT JOIN  imobilizado_status st1 
					ON         st1.imsoid = iesstatus 
					LEFT JOIN  imobilizado_status st2 
					ON         st2.imsoid = imobimsoid 
					LEFT JOIN  imobilizado_tipo 
					ON         imotoid=prdimotoid
				 	LEFT JOIN  relacionamento_representante 
					ON         relroid = imobrelroid 
					LEFT JOIN  produto_custo_medio 
					ON         pcmprdoid = prdoid 
					AND        pcmdt_referencia = 
					           ( 
				                    SELECT   pcmdt_referencia 
				                    FROM     produto_custo_medio 
				                    WHERE    pcmprdoid = prdoid 
                                                        AND pcmdt_exclusao IS NULL 
                                                        AND pcmusuoid_exclusao IS NULL
				                    ORDER BY pcmdt_referencia DESC limit 1) 
					WHERE
	    				$filtro ";

			if((isset($paginacao->limite) && isset($paginacao->offset)) || $paginacao === false) {
				$sql .= " ORDER BY inventario.invdt_ajuste, imobilizado.imobserial, representante.repnome ASC ";
			}
			
			if (isset($paginacao->limite) && isset($paginacao->offset)) {
	    	
	            $sql.= "
	                LIMIT
	                    " . intval($paginacao->limite) . "
	                OFFSET
	                    " . intval($paginacao->offset) . "
	            ";

	        }

			if (!$rs = pg_query ($this->conn, $sql)) {
				throw new Exception ("Erro ao efetuar a consulta de seriais lançados.");
			}

			while($row = pg_fetch_object($rs)){
				$retorno[] = $row;
			}

			return $retorno;

		}catch(Exception $e){
			throw new Exception ("Problema ao efetuar a consulta de seriais lançados.");
		}
		
		return pg_fetch_all($result);
	}
	
	/**
	 * [Realiza consulta ao modelo de imobilizado]
	 * @param  [type] $parametros [description]
	 * @return [type]             [description]
	 */
	public function modeloImobilizado($parametros){

		$sql = "SELECT 
					".$parametros->imotcampo_modelo." AS modelo_imob
				FROM 
					".$parametros->imottabela_secundaria."
				INNER JOIN 
					".$parametros->imottabela_modelo." 
					ON ".$parametros->imotmodelo." 
				WHERE
					".$parametros->imotcampo_serial." = '".$parametros->imobserial."' ";

		if (!$rs = pg_query ($this->conn, $sql)) {
			throw new Exception ("Erro ao efetuar a consulta do modelo de imobilizado.");
		}

		return pg_fetch_object($rs);
	}

	/**
	 * [Obtem estoque atual]
	 * @param  [type] $parametros [description]
	 * @return [type]             [description]
	 */
	public function estoqueAtual($parametros){

		$sql = "SELECT 
					repnome 
				FROM 
					" . $parametros->imottabela_secundaria . " AS tbl_sec
				INNER JOIN
					relacionamento_representante
					ON tbl_sec.". $parametros->imotprefixo_tabela_secundaria . "relroid = relroid
				INNER JOIN
					representante
					ON relrrepoid = repoid
				WHERE
					relroid = ".$parametros->relroid." AND 
					".$parametros->imotcampo_serial ." = '".$parametros->imobserial."' ";

		if (!$rs = pg_query ($this->conn, $sql)) {
			throw new Exception ("Erro ao efetuar a consulta do estoque atual.");
		}

		return pg_fetch_object($rs);
	}
	/**
	 * [Obtem estoque atual Imobilizado]
	 * @param  [type] $parametros [description]
	 * @return [type]             [description]
	 */
	public function estoqueAtualImobilizado($parametros){
	
		$sql = "SELECT
					repnome
				FROM
					imobilizado
				INNER JOIN
					relacionamento_representante ON imobrelroid = relroid
				INNER JOIN
					representante ON relrrepoid = repoid
				WHERE
					relroid = ".$parametros->relroid." AND
					imobserial = '".$parametros->imobserial."' ";
				
		if (!$rs = pg_query ($this->conn, $sql)) {
			throw new Exception ("Erro ao efetuar a consulta do estoque atual.");
		}
	
		return pg_fetch_object($rs);
	}	
	
}