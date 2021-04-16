<?php

/**
 * Classe CadAgrupamentoClasseDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class CadModeloEbsDAO {

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
	public function pesquisar(stdClass $parametros, $paginacao = null){

		$retorno = array();

		$sql = "SELECT 
                    modeoid,
					modedescricao,
                    modemmeoid,
                    mmedescricao,
                    modeobroid,
                    obrobrigacao
				FROM 
					modelo_ebs
                INNER JOIN
                    marca_modelo_ebs ON mmeoid = modemmeoid
                LEFT JOIN
                    obrigacao_financeira ON obroid = modeobroid
				WHERE 
					modedt_exclusao IS NULL";

        if ( isset($parametros->modedescricao) && !empty($parametros->modedescricao) ) {
        
            $sql .= " AND
                        modedescricao ILIKE '%" . pg_escape_string( $this->trataString($parametros->modedescricao) ) . "%'";
                
        }

        if ( isset($parametros->modemmeoid) && !empty($parametros->modemmeoid) ) {
        
            $sql .= " AND
                        modemmeoid = " . (int) $parametros->modemmeoid . "";
                
        }

        $sql .= " ORDER BY modedescricao ASC";

        if (isset($paginacao->limite) && isset($paginacao->offset)) {
            
            $sql .= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";

        }

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
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){

		$retorno = new stdClass();

		$sql = "SELECT 
                    modeoid,
                    modedescricao,
                    modemmeoid,
                    mmedescricao,
                    modeobroid,
                    obrobrigacao as modeobroid_autocomplete
                FROM 
                    modelo_ebs
                INNER JOIN
                    marca_modelo_ebs ON mmeoid = modemmeoid
                LEFT JOIN
                    obrigacao_financeira ON obroid = modeobroid
                WHERE 
                    modedt_exclusao IS NULL
				AND 
					modeoid =" . intval( $id ) . "";

        $rs = pg_query($this->conn,$sql);

        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
	}

    /**
     * Método para buscar marcas cadastradas
     *
     * @return stdClass
     * @throws ErrorException
     */
    public function getMarcas(){

        $retorno = array();

        $sql = "SELECT 
                    DISTINCT
                    mmeoid,
                    mmedescricao
                FROM 
                    marca_modelo_ebs
                WHERE 
                    mmedt_exclusao IS NULL
                AND
                    mmedescricao <> ''";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
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
					modelo_ebs
					(
					modedescricao,
                    modemmeoid,
                    modeobroid,
                    modeusuoid_inclusao,
                    modedt_inclusao
					)
				VALUES
					(
					'" . pg_escape_string( strtoupper($this->trataString($dados->modedescricao)) ) . "',
                    '" . (int) $dados->modemmeoid . "',
                    '" . (int) $dados->modeobroid . "',
                    "  . $this->usarioLogado . ",
                    NOW()
				)";

		$this->executarQuery($sql);

		return true;
	}


    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserirMarca(stdClass $dados){

        $sql = "INSERT INTO
                    marca_modelo_ebs
                    (
                    mmedescricao,
                    mmeusuoid_inclusao,
                    mmedt_inclusao
                    )
                VALUES
                    (
                    '" . pg_escape_string( strtoupper($this->trataString($dados->mmedescricao)) ) . "',
                    "  . $this->usarioLogado . ",
                    NOW()
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
					modelo_ebs
				SET
					modedescricao = '" . pg_escape_string( strtoupper($this->trataString($dados->modedescricao)) ) . "',
                    modemmeoid = " . (int)$dados->modemmeoid . ",
                    modeobroid = " . (int)$dados->modeobroid . "
				WHERE 
					modeoid = " . $dados->modeoid . "";

		$this->executarQuery($sql);

		return true;
	}


    /**
     * Responsável por atualizar os registros
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizarMarca(stdClass $dados){

        $sql = "UPDATE
                    marca_modelo_ebs
                SET
                    mmedescricao = '" . pg_escape_string( strtoupper($this->trataString($dados->mmedescricao)) ) . "',
                    modeusuoid_inclusao = "  . $this->usarioLogado . "
                WHERE 
                    mmeoid = " . $dados->mmeoid . "";

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
					modelo_ebs
				SET
					modedt_exclusao = NOW(),
                    modeusuoid_excl = " . intval($this->usarioLogado) . "
				WHERE
					modeoid = " . intval($dados->modeoid) . "";

		$this->executarQuery($sql);

		return true;
	}

    /**
     * Exclui (UPDATE) um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluirMarca(stdClass $dados){

        $sql = "UPDATE
                    marca_modelo_ebs
                SET
                    mmedt_exclusao = NOW(),
                    mmeusuoid_excl = " . intval($this->usarioLogado) . "
                WHERE
                    mmeoid = " . intval($dados->mmeoid) . "";

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
                    modelo_ebs
                WHERE 
                    modedt_exclusao IS NULL
                AND
                    TRANSLATE(modedescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC') ILIKE TRANSLATE('" . $this->trataString($parametros->modedescricao) . "', 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC')
                AND
                    modemmeoid = " . (int) $parametros->modemmeoid . "
                ";

        // caso seja editar
        if($acao == 2){
            if ( isset($parametros->modeoid) && !empty($parametros->modeoid) ) {
     
                $sql .= " AND modeoid <> " . $parametros->modeoid . " ";
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

    /**
     * Método para realizar a pesquisa de duplicidade antes de cadastrar/editar
     * @param stdClass $parametros
     * @param int $acao - 1 = cadastrar, 2 = Editar
     * @return boolean
     * @throws ErrorException
     */
    public function verificaDuplicidadeMarca(stdClass $parametros, $acao = 1){

        $sql = "SELECT 
                    1
                FROM 
                    marca_modelo_ebs
                WHERE 
                    mmedt_exclusao IS NULL
                AND
                    TRANSLATE(mmedescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC') ILIKE TRANSLATE('" . $this->trataString($parametros->mmedescricao) . "', 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC')
                ";

        // caso seja editar
        if($acao == 2){
            if ( isset($parametros->mmeoid) && !empty($parametros->mmeoid) ) {
                $sql .= " AND mmeoid <> " . $parametros->mmeoid . " ";
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


    /**
     * valida se marca pode ser excluida, onde não pode ter modelo relacionado a marca a ser excluida
     * @return bool
     */
    public function verificaExclusaoMarca(stdClass $parametros){

        $sql = "SELECT 
                    1
                FROM 
                    modelo_ebs
                WHERE 
                    modedt_exclusao IS NULL
                AND
                    modemmeoid = " . (int) $parametros->mmeoid . "
                ";

        $sql .= "LIMIT 1";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return false;
        }else{
            return true;
        }
    }


    public function getObrigacaoFinanceira($termo)
    {
        $retorno = array();
        $sql = "SELECT 
                    DISTINCT
                    obroid, 
                    obrobrigacao 
                FROM 
                    obrigacao_financeira 
                INNER JOIN 
                    os_tipo_item ON otiobroid = obroid 
                WHERE 
                    obrdt_exclusao IS NULL
                AND
                    otidt_exclusao IS NULL
                AND
                    obrobrigacao ILIKE '{$termo}%'
                ORDER BY
                    obrobrigacao ASC
                LIMIT 15";

        $query = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($query)){
            $obrobrigacao = removeAcentos($linha->obrobrigacao);
            $retorno[] = array(
                'id' => $linha->obroid,
                'label' => $obrobrigacao,
                'value' => $obrobrigacao,
            );
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


    /**
     * Trata chars da string
     * @param  string $str
     * @return string
     */
    public function trataString($str){         
        $busca     = array("'", '"', "%", "‡", "“", "<", ">" );
        $substitui = array( "" ,"" ,"" ,"" ,"" , "" , "");
         
        $str       = str_replace($busca,$substitui,$str);
        return $str;
    }
}
?>
