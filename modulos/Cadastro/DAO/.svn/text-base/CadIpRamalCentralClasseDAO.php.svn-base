<?php

/**
 * Classe CadIpRamalCentralClasseDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Harry Luiz Janz <harry.janz@sascar.com.br>
 *
 */
class CadIpRamalCentralClasseDAO {


	/** Conexão com o banco de dados */
	private $conn;
    private $conn_call;
    private $usuarioLogado;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn,$conn_call) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->conn_call = $conn_call;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

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

		$sql = "
                SELECT 
                    oid, 
                    ripramal, -- Ramal da maquina cliente
                    ripip, -- IP da maquina cliente
                    TO_CHAR(ripdt_cadastro, 'DD/MM/YYYY') AS ripdt_cadastro, -- Data de cadastro do registro 
                    TO_CHAR(ripdt_exclusao, 'DD/MM/YYYY') AS ripdt_exclusao, -- Data de exclusao do registro
                    ripdescricao,
                    CASE 
                        WHEN ripponto_roteamento = 'True' THEN 'SIM'
                        ELSE 'NÃO'
                    END AS ripponto_roteamento -- Define se este ramal é um ponto de roteamento utilizado pelo CCI
				FROM 
					ramal_ip
				WHERE 
				    ripdt_exclusao IS NULL ";

        if ( isset($parametros->ripramal) && !empty($parametros->ripramal) ) {
        
            $sql .= " AND
                        ripramal ILIKE '%" . pg_escape_string( $parametros->ripramal ) . "%'";
                
        }

        if ( isset($parametros->ripip) && !empty($parametros->ripip) ) {
        
            $sql .= " AND
                        ripip ILIKE '%" . $parametros->ripip . "%'";
                
        }

        $sql .= " ORDER BY ripramal ASC"; 

        /*echo("<pre>");
        echo($sql);
        echo("</pre>");
        exit;*/

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
                    oid, * 
				FROM 
					ramal_ip 
				WHERE 
                    ripdt_exclusao IS NULL 
					AND oid = '" . intval( $id ) . "'";

		$rs = pg_query($this->conn_call,$sql);

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
					ramal_ip
					(
    					ripramal,
    					ripip,
                        ripdt_cadastro,
                        ripdescricao, 
                        ripponto_roteamento 
					)
				VALUES
					(
    					'" . pg_escape_string( strtoupper($dados->ripramal) ) . "',
    					'" . pg_escape_string( $dados->ripip ) . "',
                        'NOW()',
                        '" . pg_escape_string( $dados->ripdescricao ) . "',
                        '" . pg_escape_string( $dados->ripponto_roteamento ) . "' 
				    )
                ";

        /*echo("<pre>");
        echo($sql);
        echo("</pre>");
        exit;*/

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
					ramal_ip
				SET
					ripramal = '" . pg_escape_string( strtoupper($dados->ripramal) ) . "',
					ripip = '" . pg_escape_string( $dados->ripip ) . "', 
                    ripdescricao = '" . pg_escape_string( $dados->ripdescricao ) . "', 
                    ripponto_roteamento = '" . pg_escape_string( $dados->ripponto_roteamento ) . "' 
				WHERE 
					oid = " . $dados->oid . "";

        /*echo("<pre>");
        echo($sql);
        echo("</pre>");
        exit;*/

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
					ramal_ip
				SET
					ripdt_exclusao = NOW()
				WHERE
					oid = " . intval($dados->oid) . "";

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
                    ramal_ip
                WHERE 
                    ripdt_exclusao IS NULL
                ";

        // caso seja editar
        if($acao == 2){
            if(isset($parametros->oid) && !empty($parametros->oid)) { 
                $sql .= "AND ripramal != '9087' AND oid != ".$parametros->oid." AND (ripramal = '".$parametros->ripramal."' OR ripip = '".$parametros->ripip."') ";
            }
        }else{
            $sql .= "AND ripramal != '9087' AND (ripramal = '".$parametros->ripramal."' OR ripip = '".$parametros->ripip."') ";
        }

        $sql .= "LIMIT 1";

        /*echo("<pre>");
        echo($sql);
        echo("</pre>");
        exit;*/

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }
    }

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn_call, 'BEGIN');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn_call, 'COMMIT');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn_call, 'ROLLBACK');
	}

	/** 
     * Submete uma query a execucao do SGBD
     * @param  [string] $query 
     * @param  [int] $tpConn -- 1 = conn, 2 = conn_call;
     * @return [bool]
     */
	private function executarQuery($query,$tpConn=2) {

        if($tpConn == 2) {
            if(!$rs = pg_query($this->conn_call, $query)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        }elseif($tpConn == 1) {
            if(!$rs = pg_query($this->conn, $query)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        }

        return $rs;
    }

    public function validarPermissaoPagina(){

        $sql = "SELECT 
                    1
                FROM 
                    usuarios
                JOIN 
                    pagina_permissao_cargo ON ppccargooid = usucargooid
                JOIN 
                    pagina_permissao_depto ON ppddepoid = usudepoid
                JOIN 
                    pagina ON pagoid = ppdpagoid AND pagoid = ppcpagoid
                WHERE 
                    cd_usuario = ". $this->usuarioLogado ."
                    AND pagurl LIKE '%cad_ip_ramal_central.php' ";

        $rs = $this->executarQuery($sql,1);

        /*echo("<pre>");
        echo(pg_num_rows($rs));
        echo($sql);
        echo("</pre>");
        exit;*/

        if (pg_num_rows($rs) > 0){
            return true;
        } else {
            return false;
        }

    }

    /**
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn_call, 'SAVEPOINT ' . $nome);
    }

     /**
     * Aborta ações dentro de um bloco de ponto de salvamento
     * @param  $nome [alias para do savepoint]
     */
    public function rollbackPoint($nome){
        pg_query($this->conn_call, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }
}
?>
