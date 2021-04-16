<?php

/**
 * Classe PrnRegraAberturaOsDAO.
 * Camada de modelagem de dados.
 *
 * @package  Principal
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class PrnRegraAberturaOsDAO {

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
                    osr.ordraoid,
                    osr.ordraostoid,
                    osr.ordrapermite_ordens_simultaneas,
                    osr.ordrapermite_tipo_motivo_distinto,
                    ost.ostdescricao
                FROM
                    ordem_servico_regra AS osr
                INNER JOIN 
                    os_tipo AS ost ON osr.ordraostoid = ost.ostoid
                WHERE
                    osr.ordradt_exclusao IS NULL";

        if ( isset($parametros->ordraostoid) && trim($parametros->ordraostoid) != '' ) {

            $sql .= " AND
                        osr.ordraostoid = " . intval( $parametros->ordraostoid ) . "";
        }

        $sql .= " ORDER BY
                    ost.ostdescricao";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){

            //faz a consulta dos resultados na tabela ordem_servico_regra_ordem_tipo
            $sql1 = "SELECT 
                        osrotostoid,
                        ost.ostdescricao,
                        osrotagendada,
                        osrotadzero
                    FROM
                        ordem_servico_regra_ordem_tipo
                    INNER JOIN 
                        os_tipo AS ost ON osrotostoid = ost.ostoid
                    WHERE 
                        osrotdt_exclusao IS NULL
                    AND
                        osrotordraoid = " . $registro->ordraoid . "
                    ORDER BY
                        ost.ostdescricao";

            $rs1 = $this->executarQuery($sql1);

            $registro->regraOrdemTipo = array();
            while($registro1 = pg_fetch_object($rs1)){
                $registro->regraOrdemTipo[] = $registro1;
            }

            //faz a consulta dos resultados na tabela ordem_servico_regra_motivo
            $sql2 = "SELECT 
                        osrmostoid,
                        ost.ostdescricao,
                        osrmagendada,
                        osrmadzero
                    FROM
                        ordem_servico_regra_motivo
                    INNER JOIN 
                        os_tipo AS ost ON osrmostoid = ost.ostoid
                    WHERE 
                        osrmdt_exclusao IS NULL
                    AND
                        osrmordraoid = " . $registro->ordraoid . "
                    ORDER BY
                        ost.ostdescricao";

            $rs2 = $this->executarQuery($sql2);

            $registro->regraMotivo = array();
            while($registro2 = pg_fetch_object($rs2)){
                $registro->regraMotivo[] = $registro2;
            }

            $retorno[] = $registro;
        }

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){

		$retorno = new stdClass();

		$sql = "SELECT 
					ordraoid, 
					ordraostoid, 
					ordrapermite_ordens_simultaneas, 
					ordrapermite_tipo_motivo_distinto, 
					ordradt_cadastro, 
					ordradt_exclusao, 
					ordrausuoid_inclusao, 
					ordrausuoid_exclusao
				FROM 
					ordem_servico_regra
				WHERE 
					ordraoid =" . intval( $id ) . "";

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

    /**
     * Busca se já exista a parametrizacao cadastrada
     * @param  stdClass $dados
     * @return id da parametrizacao
     */
    public function buscaParametrizacao(stdClass $dados){

        $retorno = new stdClass();

        $sql = "SELECT
                    osr.ordraoid,
                    osr.ordraostoid,
                    osr.ordrapermite_ordens_simultaneas,
                    osr.ordrapermite_tipo_motivo_distinto,
                    ost.ostdescricao
                FROM
                    ordem_servico_regra AS osr
                INNER JOIN
                    os_tipo AS ost ON osr.ordraostoid = ost.ostoid
                WHERE
                    osr.ordradt_exclusao IS NULL";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }else{
            return false;
        }

        return $retorno;
    }

    /**
     * Regras cadastradas
     * @param  $id
     * @return [type]     [description]
     */
    public function regrasCadastradas($id){

        $retorno = new stdClass();

        $sql = "SELECT
                    osrot.osrotoid,
                    osrot.osrotadzero,
                    osrot.osrotagendada,
                    ost.ostdescricao
                FROM  
                    ordem_servico_regra_ordem_tipo AS osrot
                INNER JOIN 
                    os_tipo AS ost ON ost.ostoid = osrot.osrotostoid
                WHERE 
                    osrot.osrotdt_exclusao IS NULL
                AND
                    osrot.osrotordraoid = $id";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            $retorno += pg_fetch_object($rs);
        }

        $sql = "SELECT 
                    osrm.osrmoid,
                    osrm.osrmadzero,
                    osrm.osrmagendada,
                    ost.ostdescricao
                FROM
                    ordem_servico_regra_motivo AS osrm
                INNER JOIN 
                    os_tipo AS ost ON ost.ostoid = osrm.osrmostoid
                WHERE 
                    osrm.osrmdt_exclusao IS NULL
                AND 
                    osrm.osrmordraoid = $id";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            $retorno += pg_fetch_object($rs);
        }

        return $retorno;
    }

    /**
     * Verifica se a parametrização para o TIPO de OS já está cadastrada
     * @param  [int] $id [ID TIPO OS (ostoid)]
     * @return bool
     */
    public function parametrizacaoExistente($id){

        $sql = " SELECT 
                    1 
                FROM
                    ordem_servico_regra
                WHERE 
                    ordraostoid = $id
                AND
                    ordradt_exclusao IS NULL
                ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }

    }

    public function simultaneaExistente(stdClass $dados){

        $sql = " SELECT 
                    1 
                FROM
                    ordem_servico_regra_ordem_tipo
                WHERE 
                    osrotdt_exclusao IS NULL
                    AND osrotordraoid = " . intval($dados->ordraoid)
                    . " AND osrotostoid = " . pg_escape_string($dados->simultanea_tipo_permitido)
                    . " AND osrotadzero::int = " . $dados->simultanea_situacao
                    . " AND osrotagendada::int = " . $dados->simultanea_agendada;

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }

    }

    public function motivoExistente(stdClass $dados){

        $sql = " SELECT 
                    1 
                FROM
                    ordem_servico_regra_motivo
                WHERE
                    osrmdt_exclusao IS NULL
                    AND osrmordraoid = " . intval($dados->ordraoid)
                    . " AND osrmostoid = " . pg_escape_string($dados->motivo_tipo_permitido)
                    . " AND osrmadzero::int = " . $dados->motivo_situacao
                    . " AND osrmagendada::int = " . $dados->motivo_agendada;

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }

    }


	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
    				ordem_servico_regra
    				(
    					ordraostoid,
                        ordrapermite_ordens_simultaneas,
    					ordrapermite_tipo_motivo_distinto,
    					ordradt_cadastro,
    					ordrausuoid_inclusao
    				)
				VALUES
					(
					   " . intval( $dados->ordraostoid ) . ",
					   '" . pg_escape_string( $dados->ordrapermite_ordens_simultaneas ) . "',
					   '" . pg_escape_string( $dados->ordrapermite_tipo_motivo_distinto ) . "',
					   NOW(),
					   " . intval( $this->usarioLogado ) . "
				    )
                RETURNING
                    ordraoid
                ";

        if($rs = $this->executarQuery($sql)){
            //busca o id do registro inserido
            $ordraoid = pg_fetch_result($rs, 0, 'ordraoid');

            return $ordraoid;
        }else{
            return false;
        }
	}

    public function editarParametrizacao(stdClass $dados){

        $sql = "UPDATE
                    ordem_servico_regra
                SET
                    ordrapermite_ordens_simultaneas = " . pg_escape_string( $dados->ordrapermite_ordens_simultaneas ) . ",
                    ordrapermite_tipo_motivo_distinto =  " . pg_escape_string( $dados->ordrapermite_tipo_motivo_distinto ) . "
                WHERE
                    ordradt_exclusao IS NULL
                    AND ordraostoid = " . intval($dados->ordraostoid);

        $rs = $this->executarQuery($sql);

        return true;
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserirRegraSimultanea(stdClass $dados){

        $sql = "INSERT INTO
                    ordem_servico_regra_ordem_tipo
                    (
                        osrotordraoid,
                        osrotostoid,
                        osrotadzero,
                        osrotagendada,
                        osrotdt_cadastro,
                        osrotusuoid_inclusao
                    )
                VALUES
                    (
                       " . intval( $dados->ordraoid ) . ",
                       '" . pg_escape_string( $dados->simultanea_tipo_permitido ) . "',
                       '" . pg_escape_string( $dados->simultanea_situacao ) . "',
                       '" . pg_escape_string( $dados->simultanea_agendada ) . "',
                       NOW(),
                       " . intval( $this->usarioLogado ) . "
                    )
                ";

        $this->executarQuery($sql);
            
        return true;
    }

    public function inserirMotivo(stdClass $dados){

        $sql = "INSERT INTO
                    ordem_servico_regra_motivo
                    (
                        osrmordraoid,
                        osrmostoid,
                        osrmadzero,
                        osrmagendada,
                        osrmusuoid_inclusao
                    )
                VALUES
                    (
                       " . intval( $dados->ordraoid ) . ",
                       '" . pg_escape_string( $dados->motivo_tipo_permitido ) . "',
                       '" . pg_escape_string( $dados->motivo_situacao ) . "',
                       '" . pg_escape_string( $dados->motivo_agendada ) . "',
                       " . intval( $this->usarioLogado ) . "
                    )
                ";

        $this->executarQuery($sql);
            
        return true;
    }

    public function recuperaParametrizacoesCadastradas($id) {
        $sql = "SELECT
                    osrotoid as id_parametro,
                    'O.S. Simultânea' as tipo,
                    osrot.osrotadzero as zero,
                    osrot.osrotagendada as agendada,
                    ost.ostdescricao as descricao
                FROM
                    ordem_servico_regra_ordem_tipo AS osrot
                INNER JOIN 
                    os_tipo AS ost
                ON ost.ostoid = osrot.osrotostoid
                WHERE 
                    osrot.osrotordraoid = $id
                AND
                    osrot.osrotdt_exclusao IS NULL
                UNION
                SELECT
                    osrmoid as id_parametro,
                    'Motivo Distinto' as tipo,
                    osrm.osrmadzero as zero,
                    osrm.osrmagendada as agendada, 
                    ost.ostdescricao as descricao
                FROM
                    ordem_servico_regra_motivo AS osrm
                INNER JOIN 
                    os_tipo AS ost
                ON ost.ostoid = osrm.osrmostoid
                WHERE 
                    osrm.osrmordraoid = $id
                AND 
                    osrm.osrmdt_exclusao IS NULL
                ORDER BY
                    tipo,
                    descricao";

        $rs = $this->executarQuery($sql);

        $retorno = array();
        while($registro = pg_fetch_assoc($rs)){
            $retorno[] = array_map('utf8_encode', $registro);
        }

        return $retorno;
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserirRegraMotivos(stdClass $dados){

        $sql = "INSERT INTO
                    ordem_servico_regra_motivo
                    (
                        osrmordraoid,
                        osrmostoid,
                        osrmadzero,
                        osrmagendada,
                        osrmdt_cadastro,
                        osrmusuoid_inclusao
                    )
                VALUES
                    (
                       " . intval( $dados->osrotordraoid ) . ",
                       '" . pg_escape_string( $dados->motivo_tipo_permitido ) . "',
                       '" . pg_escape_string( $dados->motivo_situacao ) . "',
                       '" . pg_escape_string( $dados->motivo_agendada ) . "',
                       NOW(),
                       " . intval( $this->usarioLogado ) . "
                    )
                ";

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
					ordem_servico_regra
				SET
					ordraostoid = " . intval( $dados->ordraostoid ) . ",
					ordrapermite_ordens_simultaneas = '" . pg_escape_string( $dados->ordrapermite_ordens_simultaneas ) . "',
					ordrapermite_tipo_motivo_distinto = '" . pg_escape_string( $dados->ordrapermite_tipo_motivo_distinto ) . "',
					ordradt_cadastro = '" . $dados->ordradt_cadastro . "',
					ordrausuoid_inclusao = " . intval( $dados->ordrausuoid_inclusao ) . ",
				WHERE 
					ordraoid = " . $dados->ordraoid . "";

		$rs = $this->executarQuery($sql);

		return true;
	}

    public function excluirSimultanea($id) {
        $sql = "UPDATE
                    ordem_servico_regra_ordem_tipo
                SET
                    osrotdt_exclusao = NOW(),
                    osrotusuoid_exclusao = " . $this->usarioLogado
                . " WHERE
                    osrotoid = $id";

        return $this->executarQuery($sql);
    }

    public function excluirMotivo($id) {
        $sql = "UPDATE
                    ordem_servico_regra_motivo
                SET 
                    osrmdt_exclusao = NOW(),
                    osrmusuoid_exclusao = " . $this->usarioLogado
                . " WHERE
                    osrmoid = $id";

        return $this->executarQuery($sql);
    }

    public function excluirTodasSimultaneas($id) {
        $sql = "UPDATE 
                    ordem_servico_regra_ordem_tipo
                SET
                    osrotdt_exclusao = NOW(),
                    osrotusuoid_exclusao = " . $this->usarioLogado
                . " WHERE
                    osrotordraoid = $id";

        return $this->executarQuery($sql);
    }

    public function excluirTodosMotivos($id, $ordraostoid) {
        $sql = "UPDATE 
                    ordem_servico_regra_motivo
                SET
                    osrmdt_exclusao = NOW(),
                    osrmusuoid_exclusao = " . $this->usarioLogado
                . " WHERE
                    osrmordraoid = $id
                    AND osrmostoid != $ordraostoid";

        return $this->executarQuery($sql);
    }

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir($id){

		$sql = "UPDATE
					ordem_servico_regra_ordem_tipo
				SET
					osrotdt_exclusao = NOW(),
                    osrotusuoid_exclusao = " . intval( $this->usarioLogado ) . "
				WHERE
					osrotordraoid = " . intval( $id ) . "";

		$rs = $this->executarQuery($sql);

        $sql = "UPDATE
                    ordem_servico_regra_motivo
                SET
                    osrmdt_exclusao = NOW(),
                    osrmusuoid_exclusao = " . intval( $this->usarioLogado ) . "
                WHERE
                    osrmordraoid = " . intval( $id ) . "";

        $rs = $this->executarQuery($sql);

        $sql = "UPDATE
                    ordem_servico_regra
                SET
                    ordradt_exclusao = NOW(),
                    ordrausuoid_exclusao = " . intval( $this->usarioLogado ) . "
                WHERE
                    ordraoid = " . intval( $id ) . "";

        $rs = $this->executarQuery($sql);

		return true;
	}

    /**
     * Popula campo Tipo no filtro de pesquisa
     * @return [array] 
     */
    public function getTipo(){

        $retorno = array();

        $sql = " SELECT 
                    ostoid,
                    ostdescricao
                FROM 
                    os_tipo 
                WHERE 
                    ostdt_exclusao IS NULL 
                ORDER BY 
                    ostdescricao ASC ";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Popula campo Tipo na tela de cadastro
     * Não deve aparecer tipos com regras já cadastradas
     * @return [array] 
     */
    public function getTipoCadastro($id = null){

        $retorno = array();

        $edit = "";

        if($id != null) {
            $edit = " AND ordraostoid != $id ";
        }

        $sql = " SELECT 
                    ostoid,
                    ostdescricao
                FROM 
                    os_tipo 
                WHERE 
                    ostdt_exclusao IS NULL 
                AND
                    ostoid NOT IN (SELECT ordraostoid FROM ordem_servico_regra WHERE ordradt_exclusao IS NULL $edit GROUP BY ordraostoid)
                ORDER BY 
                    ostdescricao ASC ";

        $rs = $this->executarQuery($sql);

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
