<?php
/**
 * Camada DAO da classe PrnContratoReativacao
 * 
 */
class PrnContratoReativacaoDAO {

	const MSG_ERRO_COND_PAG = "Houve um erro na busca da condicao de pagamento.";
	const MSG_ERRO_CONTRATO_PAGAMENTO = "Houve um erro ao atualizar dados de pagamento do contrato.";
    const MSG_ERRO_CONTRATO_SERVICO = "Houve um erro ao atualizar dados de servico do contrato.";
	const MSG_ERRO_BUSCA_CONTRATO_PAGAMENTO = "Houve um erro ao realizar busca dos dados de pagamento do contrato.";
	const MSG_ERRO_HISTORICO_TERMO = "Houve um erro ao atualizar o histórico do termo.";
	const MSG_ERRO_BUSCA_CONTRATO = "Houve um erro ao buscar dados do contrato.";
    const MSG_ERRO_BUSCA_OBRIGACAO = "Houve um erro ao buscar obrigações financeiras do tipo Locação Acessórios.";
	
	private $conn;

	public function __construct($conn) {
		$this->conn = $conn;
	}

	public function getConn() {
		return $this->conn;
	}

	/**
	 * Busca dados da tabela cond_pgto_venda
	 * @param  [array] $condicoes [array contendo filtros para a clausula WHERE]
	 * @return [type]            [description]
	 */
	public function buscaCondicaoPagamento($condicoes) {

		$sql = '';
		$retorno = new stdClass();

		try{

			if(!$where = $this->where($condicoes)) {
				throw new Exception(self::MSG_ERRO_COND_PAG);
			}

			$sql = "SELECT 
						*
					FROM 
						cond_pgto_venda ";

			$sql .= $where;
			$retorno->sql = $sql;

			$rs = pg_query($this->getConn(), $sql);

			if(!$rs) {
				throw new Exception(self::MSG_ERRO_COND_PAG);
			}

			$retorno->result = $rs;

		} catch (Exception $e) {
			$retorno->erro = $e->getMessage();
		}

		return $retorno;
	}

	/**
	 * Recupera os dados gravados na tabela contrato_pagamento
	 * @param  [type] $condicoes [description]
	 * @return [type]            [description]
	 */
	public function buscaDadosContratoPagamento($condicoes) {
		$sql = '';
		$retorno = new stdClass();

		try{

			if(!$where = $this->where($condicoes)) {
				throw new Exception(self::MSG_ERRO_BUSCA_CONTRATO_PAGAMENTO);
			}
			
			$sql = "SELECT 
						*
					FROM 
						contrato_pagamento ";

			$rs = pg_query($this->getConn(), $sql);

	        if(!$rs) {
				throw new Exception(self::MSG_ERRO_BUSCA_CONTRATO_PAGAMENTO);
			}

			$retorno->result = $rs;

        } catch (Exception $e) {
			$retorno->erro = $e->getMessage();
		}

		return $retorno;
	}

	/**
	 * Atualiza tabela contrato_pagamento
	 * @param  [type] $dados  [dados de entrada]
	 * @param  [type] $filtro [condicao where]
	 * @return [type]         [description]
	 */
	public function atualizaContratoPagamento($dados,$filtro) {

		$sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosUpdate = $this->update($dados)) {
    			throw new Exception(self::MSG_ERRO_CONTRATO_PAGAMENTO);
    		}

    		if(!$where = $this->where($filtro)) {
				throw new Exception(self::MSG_ERRO_CONTRATO_PAGAMENTO);
			}

    		$sql = "UPDATE
						contrato_pagamento
					SET " . $dadosUpdate . " " . $where;

			$retorno->sql = $sql;

			$rs = pg_query($this->getConn(), $sql);

			if(!$rs) {
				throw new Exception(self::MSG_ERRO_CONTRATO_PAGAMENTO);
			}

			$retorno->resultado = $rs;
    	} catch (Exception $e) {
    		$retorno->erro = $e->getMessage();
    	}

    	return $retorno;
	}

    /**
     * Atualiza tabela contrato_servico
     * @param  [type] $dados  [dados de entrada]
     * @param  [type] $filtro [condicao where]
     * @return [type]         [description]
     */
    public function atualizaContratoServico($dados,$filtro) {

        $sql = '';
        $retorno = new stdClass();

        try {

            if(!$dadosUpdate = $this->update($dados)) {
                throw new Exception(self::MSG_ERRO_CONTRATO_SERVICO);
            }

            if(!$where = $this->where($filtro)) {
                throw new Exception(self::MSG_ERRO_CONTRATO_SERVICO);
            }

            $sql = "UPDATE
                        contrato_servico
                    SET " . $dadosUpdate . " " . $where;
            $retorno->sql = $sql;

            $rs = pg_query($this->getConn(), $sql);

            if(!$rs) {
                throw new Exception(self::MSG_ERRO_CONTRATO_SERVICO);
            }

            $retorno->resultado = $rs;
        } catch (Exception $e) {
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    /**
     * Busca Obrigação Financeira do grupo 'Locação Acessórios'
     * @return [array com IDS]
     */
    public function obrigacaoFinanceiraLocacaoAcessorios() {

        $retorno = new stdClass();

        try {

            $sql = "SELECT obroid FROM obrigacao_financeira WHERE obrofgoid = 15"; //Id grupo Locação Acessórioss
            $rs = pg_query($this->getConn(), $sql);

            if(!$rs) {
                throw new Exception(self::MSG_ERRO_BUSCA_OBRIGACAO);
            }

            if(pg_num_rows($rs) > 0){
                $retorno->ids = pg_fetch_all($rs);
                foreach ($retorno->ids as $chave => $valor) {
                    $arrIds[] = $valor['obroid'];
                }

                $retorno->ids = $arrIds;
            }
  
        } catch (Exception $e) {
            $retorno->erro = $e->getMessage();
        } 

        return $retorno;
    }

	/**
	 * Atualiza histórico do termo
	 * @param  [int] $connumero     [numero do cotrato]
	 * @param  [int] $idUsuario     [id do usuario]
	 * @param  [string] $justificativa [string contendo a justificativa]
	 * @return [type]                [description]
	 */
	public function insereHistoricoTermo($connumero,$idUsuario,$justificativa) {
		$sql = '';
    	$retorno = new stdClass();

    	try {

    		$sql = "SELECT historico_termo_i(:numContrato, :idUsuario, ':textoJustificativa');";

    		$sql = str_replace(':numContrato', (int) $connumero, $sql);
    		$sql = str_replace(':idUsuario', (int) $idUsuario, $sql);
    		$sql = str_replace(':textoJustificativa', pg_escape_string($justificativa), $sql);

    		$retorno->sql = $sql;
			$rs = pg_query($this->getConn(), $sql);

			if(!$rs) {
				throw new Exception(self::MSG_ERRO_HISTORICO_TERMO);
			}

			$retorno->resultado = $rs;

    	} catch (Exception $e) {
    		$retorno->erro = $e->getMessage();
    	}

    	return $retorno;
	}


	/**
	 * busca dados do contrato
	 * @param  [type] $condicoes [description]
	 * @return [type]            [description]
	 */
	public function buscaDadosContrato($condicoes) {
		$sql = '';
    	$retorno = new stdClass();

    	try {
    		if(!$where = $this->where($condicoes)) {
				throw new Exception(self::MSG_ERRO_BUSCA_CONTRATO);
			}

			$sql = "SELECT 
						*
					FROM 
						contrato ";

			$sql .= $where;
			$retorno->sql = $sql;

			$rs = pg_query($this->getConn(), $sql);

			if(!$rs) {
				throw new Exception(self::MSG_ERRO_BUSCA_CONTRATO);
			}

			$retorno->result = $rs;
		} catch (Exception $e) {
    		$retorno->erro = $e->getMessage();
    	}

    	return $retorno;
	}

	/**
     * Monta string para UPDATE
     * @param  [type] $dadosArray [description]
     * @return [string/boolean]             [description]
     */
    private function update($dadosArray){
    	$dados = '';
        $strSeparador = '';

		foreach ($dadosArray as $key => $value) {
			$valor = pg_escape_string($value);

			if($value != 'NOW()' && $value != 'NULL') {
				$valor = "'" . $valor . "'";
			}

            $dados .= $strSeparador . $key." = ".$valor." ";
            $strSeparador = ', ';
        }

        if(strlen($dados) == 0) {
        	return false;
        }

        return $dados;
    }

    /**
     * Retorna array com os dados para realizar insert
     * @param  [array] $dados [Contém chave/valor de cada coluna no insert]
     * @return [array]        [description]
     */
    private function insert($dados) {

    	if(is_null($dados) || !is_array($dados) || count($dados) == 0) {
    		return false;
    	}

    	$valores = '';
    	$colunas = implode(", ", array_keys($dados));

    	foreach ($dados as $key => $value) {

    		$valor = pg_escape_string($value);

    		if(strlen($valores) > 0) {
				$valores .= ' , ';
			}

			if($valor != 'NULL') {
				$valor = "'" . $valor . "'";
			}

    		$valores .= " " . $valor . " ";
    	}

    	return array('columns' => $colunas, 'values' => $valores);
    }

	/**
     * monta filtro da condição WHERE
     * @param  [array] $filtro [array contendo os dados do filtro]
     * @return [string/boolean]         [description]
     */
    private function where($filtro) {

    	$where = '';

    	foreach ($filtro as $key => $value) {

    		if(!isset($value['condition']) || !isset($value['value'])) {
    			return false;
    		}

            $valor = is_string($value['value']) ? pg_escape_string($value['value']) : $value['value'];

			if(strlen($where) > 0) {
				$where .= " AND ";
			}

			if($valor != 'NULL' && $valor != 'TRUE') {

                 if($value['condition'] == 'IN'){
                    $valor = "(" . implode(',', $valor) . ")";
                } else {
                    $valor = "'" . $valor . "'";
                }

			}


			$where .= " " .$key . " ". $value['condition'] ." " . $valor . " " ;
		}

		if(strlen($where) == 0) {
			return false;
		}

		return " WHERE ". $where;
    }

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN;');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT;');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK;');
	}

	/** Submete uma query a execucao do SGBD */
    private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }

    /**
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

	/**
	* Aborta ações dentro de um bloco de ponto de salvamento
	* @param  $nome [alias para do savepoint]
	*/
    public function rollbackPoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }
}
?>