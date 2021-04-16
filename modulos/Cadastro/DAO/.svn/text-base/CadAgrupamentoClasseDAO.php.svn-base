<?php

/**
 * Classe CadAgrupamentoClasseDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class CadAgrupamentoClasseDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT 
                    agcoid,
					agccodigo, 
					agcdescricao
				FROM 
					agrupamento_classe
				WHERE 
					agcdt_exclusao IS NULL";

        if ( isset($parametros->agccodigo) && !empty($parametros->agccodigo) ) {
        
            $sql .= " AND
                        agccodigo ILIKE '%" . pg_escape_string( $parametros->agccodigo ) . "%'";
                
        }

        if ( isset($parametros->agcdescricao) && !empty($parametros->agcdescricao) ) {
        
            $sql .= " AND
                        agcdescricao ILIKE '%" . $parametros->agcdescricaoConsulta . "%'";
                
        }

        $sql .= " ORDER BY agcdescricao ASC";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){

		$retorno = new stdClass();

		$sql = "SELECT 
					agcoid, 
					agccodigo, 
					agcdescricao, 
					adcdt_cadastro, 
					agcusuoid_cadastro, 
					agcdt_exclusao, 
					agcusuoid_exclusao
				FROM 
					agrupamento_classe
				WHERE 
					agcoid =" . intval( $id ) . "";

		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					agrupamento_classe
					(
					agccodigo,
					agcdescricao,
                    agcusuoid_cadastro
					)
				VALUES
					(
					'" . pg_escape_string( strtoupper($dados->agccodigo) ) . "',
					'" . pg_escape_string( $dados->agcdescricao ) . "',
                    "  . $this->usarioLogado . "
				)";

		$this->executarQuery($sql);

		return true;
	}

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizar(stdClass $dados){

		$sql = "UPDATE
					agrupamento_classe
				SET
					agccodigo = '" . pg_escape_string( strtoupper($dados->agccodigo) ) . "',
					agcdescricao = '" . pg_escape_string( $dados->agcdescricao ) . "'
				WHERE 
					agcoid = " . $dados->agcoid . "";

		$this->executarQuery($sql);

		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir(stdClass $dados){

		$sql = "UPDATE
					agrupamento_classe
				SET
					agcdt_exclusao = NOW(),
                    agcusuoid_exclusao = " . intval($this->usarioLogado) . "
				WHERE
					agcoid = " . intval($dados->agcoid) . "";

		$this->executarQuery($sql);

		return true;
	}


    /**
     * Método para realizar a pesquisa de duplicidade antes de cadastrar/editar
     * @param stdClass $parametros
     * @param int $acao - 1 = cadastrar, 2 = Editar
     * @return boolean
     * @throws ErrorException
     */
    public function verificaDuplicidade(stdClass $parametros, $acao = 1){

        $sql = "SELECT 
                    1
                FROM 
                    agrupamento_classe
                WHERE 
                    agcdt_exclusao IS NULL
                AND
                    (
                        agccodigo ILIKE '" . pg_escape_string( $parametros->agccodigo ) . "' 
                    OR
                        TRANSLATE(agcdescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC') ILIKE '" . $parametros->agcdescricaoConsulta . "' 
                    )
                ";

        // caso seja editar
        if($acao == 2){
            if ( isset($parametros->agcoid) && !empty($parametros->agcoid) ) {
                $sql .= " AND agcoid <> " . $parametros->agcoid . " ";
            }
        }

        $sql .= "LIMIT 1";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }
    }

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

	/** 
     * Submete uma query a execucao do SGBD
     * @param  [string] $query
     * @return [bool]
     */
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
