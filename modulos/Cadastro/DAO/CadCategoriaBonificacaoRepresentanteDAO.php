<?php

/**
 * Classe CadCategoriaBonificacaoRepresentanteDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   JORGE LUIS CUBAS <jorge.cubas@meta.com.br>
 *
 */
class CadCategoriaBonificacaoRepresentanteDAO {

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
					bonrecatoid, 
					bonrecatnome,
                    CASE
                        WHEN (SELECT TRUE FROM bonificacao_representante WHERE bonrebonrecatoid = bonrecatoid AND bonredt_exclusao IS NULL LIMIT 1) THEN
                            TRUE
                        ELSE
                            FALSE
                    END AS utilizado
				FROM 
					bonificacao_representante_categoria
				WHERE 
					1 = 1
				AND 
					bonrecatdt_exclusao IS NULL ";

        if ( isset($parametros->bonrecatnome) && !empty($parametros->bonrecatnome) ) {
        
            $sql .= "AND
                        bonrecatnome ilike '%$parametros->bonrecatnome%' ";
                
        }

        $sql .= "ORDER BY
                    bonrecatnome";


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
					bonrecatoid, 
					bonrecatnome, 
					bonrecatdt_exclusao
				FROM 
					bonificacao_representante_categoria
				WHERE 
					bonrecatoid =" . intval( $id ) . "";

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}
	
	//* Método para realizar a pesquisa de se já existe cadastro de categoria
	public function pesquisarPorNome(stdClass $parametros){
	
		$nome = mb_strtoupper($parametros->bonrecatnome);
		
		$sql = "SELECT
					bonrecatoid,
					bonrecatnome
				FROM
					bonificacao_representante_categoria
				WHERE
					 to_ascii(UPPER(bonrecatnome))  = to_ascii('$nome')
				AND
					bonrecatdt_exclusao IS NULL ";

		   $rs = $this->executarQuery($sql);
		   
		   if(pg_num_rows($rs) > 0) {
		   		return true;
		   }
		   
		   return false;
		}


	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					bonificacao_representante_categoria
					(
					bonrecatnome
					)
				VALUES
					(
					'" . pg_escape_string( $dados->bonrecatnome ) . "'
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
					bonificacao_representante_categoria
				SET
					bonrecatnome = '" . pg_escape_string( $dados->bonrecatnome ) . "'
				WHERE 
					bonrecatoid = " . $dados->bonrecatoid . "";



		$this->executarQuery($sql);
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
					bonificacao_representante_categoria
				SET
					bonrecatdt_exclusao = NOW() 
				WHERE
					bonrecatoid = " . intval( $id ) . "";

		$this->executarQuery($sql);


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
}
?>
