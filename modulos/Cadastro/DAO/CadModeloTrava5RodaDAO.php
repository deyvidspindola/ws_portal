<?php

/**
 * Classe CadModeloTrava5RodaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   ERIK VANDERLEI <erik.vanderlei@sascar.com.br>
 *
 */
class CadModeloTrava5RodaDAO {

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

		$sql = "SELECT 
					tmooid, 
					tmodescricao, 
					prdproduto,
                                        to_char(tmodt_cadastro,'dd/mm/yyyy hh24:mi:ss') AS tmodt_cadastro
				FROM 
					trava_5roda_modelo
                                INNER JOIN produto ON tmoprdoid=prdoid
				WHERE 
					tmodt_exclusao IS NULL ";

        if ( isset($parametros->tmodescricao) && !empty($parametros->tmodescricao) ) {
        
            $sql .= "AND
                        tmodescricao ilike '%" . pg_escape_string( $parametros->tmodescricao ) . "%'";
                
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
					tmooid, 
					tmodescricao, 
					tmoprdoid
				FROM 
					trava_5roda_modelo
				WHERE 
					tmooid =" . intval( $id ) . "";

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
					trava_5roda_modelo
					(
					tmodescricao,
					tmoprdoid
					)
				VALUES
					(
					'" . pg_escape_string( $dados->tmodescricao ) . "',
					" . intval( $dados->tmoprdoid ) . "
				)";

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
					trava_5roda_modelo
				SET
					tmodescricao = '" . pg_escape_string( $dados->tmodescricao ) . "',
					tmoprdoid = " . intval( $dados->tmoprdoid ) . "
				WHERE 
					tmooid = " . $dados->tmooid . "";

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
					trava_5roda_modelo
				SET
					tmodt_exclusao = NOW(),
                                        tmousuoid_excl = ". intval($this->usuarioLogado) ."
				WHERE
					tmooid = " . intval( $id ) . "";

		$rs = pg_query($this->conn,$sql);

		return true;
	}
	
	/**
	 * Verifica se uma trava já foi cadastrada
	 */
	public function verificaExistencia(stdClass $dados){
		
		$tmodescricao = $dados->tmodescricao;
		$tmoprdoid = $dados->tmoprdoid;
		
		$sql = "SELECT 
					tmooid 
				FROM 
					trava_5roda_modelo
				WHERE 
					tmousuoid_excl IS NULL
					AND tmodescricao = '" . $tmodescricao . "'";
		
		$rs = pg_query($this->conn,$sql);
		
		if (pg_num_rows($rs) > 0){
			return pg_fetch_result($rs, 0, 0);
		} else{
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
    
     /**
     * Busca Produtos
     * @param [int] [tmoprdoid] [Produto]
     * @return array
     */
    public function buscarProduto($tmoprdoid = 0){

        $retorno = array();

        $sql = " SELECT
                    prdoid,
                    prdproduto
                FROM
                    produto
                WHERE
                    prddt_exclusao IS NULL
                AND
                    prdgrmoid = 34                
                ORDER BY
                    prdproduto ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_assoc($rs)){
            $retorno[] = array_map("utf8_encode", $registro);
        }

        return $retorno;
    }
}
?>
