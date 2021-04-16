<?php

/**
 * Classe CadCidadeMapeadaBairroDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   MARCIO SAMPAIO FERREIRA <marcioferreira@brq.com>
 *
 */
class CadCidadeMapeadaBairroDAO {

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
	public function pesquisar(stdClass $parametros, $paginacao = null, $ordenacao = null){

		$retorno = array();

		if(!isset($paginacao)) {
			$select = "COUNT(cmboid) AS total_registros";
			$orderBy = "";
		
		}else{
			
			$select = "cmboid,
				       estuf AS cmbestoid, 
				       clcnome AS cmbclcoid, 
				       cbanome AS cmbcbaoid ";
			
			
			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "estuf, clcnome, cbanome";
		}
		
		$sql = "SELECT 
				        ".$select."   
				  FROM cidade_mapeada_bairro
		    INNER JOIN estado ON estoid = cmbestoid
		    INNER JOIN correios_localidades ON clcoid = cmbclcoid
	        INNER JOIN correios_bairros ON cbaoid = cmbcbaoid
				 WHERE 1 = 1
				   AND cmbdt_exclusao IS NULL ";

        if ( isset($parametros->cmbestoid) && trim($parametros->cmbestoid) != '' && $parametros->cmbestoid != 0  ) {
            $sql .= " AND cmbestoid = " . intval( $parametros->cmbestoid ) . "";
        }

        if ( isset($parametros->cmbclcoid) && trim($parametros->cmbclcoid) != '' && $parametros->cmbclcoid != 0 ) {
            $sql .= " AND cmbclcoid = " . intval( $parametros->cmbclcoid ) . "";
        }

        if ( isset($parametros->cmbcbaoid) && trim($parametros->cmbcbaoid) != '' && $parametros->cmbcbaoid != 0) {
            $sql .= " AND cmbcbaoid = " . intval( $parametros->cmbcbaoid ) . "";
        }
        
        $sql .= $orderBy;
        
        if (isset($paginacao->limite) && isset($paginacao->offset)) {
        	$sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
        }
        
		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}
	
	/**
	 * Pesquisa se já existe um registro na base
	 * 
	 * @param stdClass $parametros
	 * @return multitype:object
	 */
	public function pesquisarDuplicados(stdClass $parametros){
		
		$retorno = array();
		
		$sql = " SELECT count(cmboid)
				   FROM cidade_mapeada_bairro
				  WHERE cmbestoid = " . intval( $parametros->cmbestoid ) . "
	                AND cmbclcoid = " . intval( $parametros->cmbclcoid ) . "
		            AND cmbcbaoid = " . intval( $parametros->cmbcbaoid ) . "
		            AND cmbusuoid_exclusao IS NULL";
		
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
					cmboid, 
					cmbestoid, 
					cmbclcoid, 
					cmbcbaoid, 
					cmbdt_cadastro, 
					cmbusuoid_cadastro, 
					cmbdt_exclusao, 
					cmbusuoid_exclusao
				FROM 
					cidade_mapeada_bairro
				WHERE 
					cmboid =" . intval( $id ) . "";

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
					cidade_mapeada_bairro
					(
					cmbestoid,
					cmbclcoid,
					cmbcbaoid,
				    cmbdt_cadastro,
				    cmbusuoid_cadastro
					)
				VALUES
					(
					" . intval( $dados->cmbestoid ) . ",
					" . intval( $dados->cmbclcoid ) . ",
					" . intval( $dados->cmbcbaoid ) . ",
					NOW(),
					".intval($this->usarioLogado)."
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
					cidade_mapeada_bairro
				SET
					cmbestoid = " . intval( $dados->cmbestoid ) . ",
					cmbclcoid = " . intval( $dados->cmbclcoid ) . ",
					cmbcbaoid = " . intval( $dados->cmbcbaoid ) . "
				WHERE 
					cmboid = " . intval($dados->cmboid) . "";

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
					cidade_mapeada_bairro
				SET
					cmbdt_exclusao = NOW(), 
				    cmbusuoid_exclusao = ".intval($this->usarioLogado)." 
				WHERE
					cmboid = " . intval( $id ) . "";

		$rs = pg_query($this->conn,$sql);

		return true;
	}
	
	
	/**
	 * Retorna os dados dos estados brasileiros
	 * 
	 * @return Ambigous <stdClass, object>
	 */
	public function getEstados(){
		
		$retorno = array();
		
		$sql =" SELECT 
	            	estoid, estuf, estnome 
	            FROM 
	            	estado 
	            WHERE 
	            	estpaisoid = 1 
	            AND
	            	estnome IS NOT NULL
	            ORDER BY estuf ";
		
		$rs = pg_query($this->conn,$sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
		
	}
	
	
	/**
	 * Retorna as cidades do estado informado
	 * 
	 * @param int $idEstado
	 * @return multitype:object
	 */
	public function getCidades($idEstado){
	
		$retorno = array();
	
		$sql =" SELECT clcoid,
                       UPPER(clcnome) AS clcnome
                  FROM correios_localidades
                 WHERE clcestoid = ".intval($idEstado)."  
		           AND clcnome IS NOT NULL
		      ORDER BY clcnome ASC";
	
		$rs = pg_query($this->conn,$sql);
	
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
	
		return $retorno;
	
	}
	
	
	
	/**
	 * Retorna os bairros da cidade informada
	 *
	 * @param int $idEstado
	 * @return multitype:object
	 */
	public function getBairros($idCidade){
	
		$retorno = array();
	
		$sql =" SELECT cbaoid,
		               cbanome 
  				  FROM correios_bairros 
                 WHERE cbaclcoid = ".intval($idCidade)."  
		           AND cbanome IS NOT NULL
		      ORDER BY cbanome ASC";
	
		$rs = pg_query($this->conn,$sql);
	
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
	
		return $retorno;
	
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
