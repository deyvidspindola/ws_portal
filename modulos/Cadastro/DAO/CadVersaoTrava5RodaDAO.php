<?php

/**
 * Classe CadVersaoTrava5RodaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   ERIK VANDERLEI <erik.vanderlei@sascar.com.br>
 *
 */
class CadVersaoTrava5RodaDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usuarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usuarioLogado)) {
            $this->usuarioLogado = 2750;
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

		$sql = "SELECT 		trvoid,
                                        to_char(trvcadastro,'dd/mm/yyyy hh24:mi:ss') as trvcadastro, 
					trvdescricao 				
				FROM 
					trava_5roda_versao
				WHERE 				
					trvexclusao IS NULL ";

                if ( isset($parametros->trvdescricao) && !empty($parametros->trvdescricao) ) {

                    $sql .= "AND
                                trvdescricao ilike '%" . pg_escape_string( $parametros->trvdescricao ) . "%'";

                }
        
		$rs = pg_query($this->conn,$sql);

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
					trvoid, 
					trvcadastro, 
					trvdescricao, 
					trvexclusao, 
					trvusuoid_excl
				FROM 
					trava_5roda_versao
				WHERE 
					trvoid =" . intval( $id ) . "";

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
					trava_5roda_versao (trvdescricao)
				VALUES
					('" . pg_escape_string( $dados->trvdescricao ) . "')";

		$rs = pg_query($this->conn,$sql);

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
					trava_5roda_versao
				SET					
					trvdescricao = '" . pg_escape_string( $dados->trvdescricao ) . "'				
				WHERE 
					trvoid = " . $dados->trvoid . "";

		$rs = pg_query($this->conn,$sql);

		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir($id){

		$sql = "UPDATE
					trava_5roda_versao
				SET
					trvexclusao = NOW(),
                                        trvusuoid_excl = ". intval($this->usuarioLogado) ."
				WHERE
					trvoid = " . intval( $id ) . "";

		$rs = pg_query($this->conn,$sql);

		return true;
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

	/** Submete uma query a execucao do SGBD */
	private function executarQuery($query) {

        if(!$rs = pg_query($query)) {
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
