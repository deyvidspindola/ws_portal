<?php

/**
 * Classe CadAgrupamentoClasseDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class CadTiposVeiculoDAO {

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
                    tipvoid,
                    tipvcategoria,
                    tipvcarreta,
					tipvdescricao
				FROM 
					tipo_veiculo
				WHERE 
					tipvexclusao IS NULL
                AND
                    tipvstatus = true";

        if ( isset($parametros->tipvdescricao) && !empty($parametros->tipvdescricao) ) {
        
            $sql .= " AND
                        tipvdescricao ILIKE '%" . pg_escape_string( $parametros->tipvdescricao ) . "%'";
                
        }

        if ( isset($parametros->tipvcategoria) && !empty($parametros->tipvcategoria) ) {
        
            $sql .= " AND
                        tipvcategoria = '" . $parametros->tipvcategoria . "'";
                
        }

        if ( isset($parametros->tipvcarreta) && $parametros->tipvcarreta == 'on' ) {
        
            $sql .= " AND
                        tipvcarreta = true";
                
        }

        $sql .= " ORDER BY tipvdescricao ASC";

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
                    tipvoid,
                    tipvcategoria,
                    tipvcarreta,
                    tipvdescricao
                FROM 
                    tipo_veiculo
                WHERE 
                    tipvexclusao IS NULL
                AND
                    tipvstatus = true
				AND 
					tipvoid =" . intval( $id ) . "";

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
					tipo_veiculo
					(
					tipvcategoria,
					tipvdescricao,
                    tipvcarreta,
                    tipvusuoid_inclusao,
                    tipvdt_cadastro,
                    tipvstatus
					)
				VALUES
					(
					'" . pg_escape_string( strtoupper($dados->tipvcategoria) ) . "',
					'" . pg_escape_string( strtoupper($dados->tipvdescricao) ) . "',
                    '" . ($dados->tipvcarreta == 'on' ? 't' : 'f') . "',
                    "  . $this->usarioLogado . ",
                    NOW(),
                    TRUE
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
					tipo_veiculo
				SET
					tipvcategoria = '" . pg_escape_string( strtoupper($dados->tipvcategoria) ) . "',
					tipvdescricao = '" . pg_escape_string( strtoupper($dados->tipvdescricao) ) . "',
                    tipvcarreta = '" . ($dados->tipvcarreta == 'on' ? 't' : 'f') . "',
                    tipvusuoid_inclusao = "  . $this->usarioLogado . ",
                    tipvdt_alteracao = NOW()
				WHERE 
					tipvoid = " . $dados->tipvoid . "";

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
					tipo_veiculo
				SET
					tipvexclusao = NOW(),
                    tipvusuoid_excl = " . intval($this->usarioLogado) . ",
                    tipvstatus = false
				WHERE
					tipvoid = " . intval($dados->tipvoid) . "";

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
                    tipo_veiculo
                WHERE 
                    tipvexclusao IS NULL
                AND
                    tipvstatus = true
                AND
                    TRANSLATE(tipvdescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC') ILIKE TRANSLATE('" . $parametros->tipvdescricao . "', 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC')
                ";

        // caso seja editar
        if($acao == 2){
            if ( isset($parametros->tipvoid) && !empty($parametros->tipvoid) ) {
                $sql .= " AND tipvoid <> " . $parametros->tipvoid . " ";
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
