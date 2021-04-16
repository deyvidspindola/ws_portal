<?php

/**
 * Classe CadItensEssenciaisDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class CadItensEssenciaisDAO {

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
                    iesoid,
                    ostdescricao,
                    eqcdescricao,
                    eprnome,
                    eveversao,
                    otidescricao,
                    mcamarca,
                    mlomodelo,
                    otitipo
                FROM
                    item_essencial_servico
                LEFT JOIN
                    os_tipo ON ostoid = iesostoid
                LEFT JOIN
                    os_tipo_item ON otioid = iesotioid
                LEFT JOIN
                    equipamento_classe ON eqcoid = ieseqcoid
                LEFT JOIN
                    equipamento_classe_instalacao ON eqcieqcoid = eqcoid
                LEFT JOIN
                    equipamento_projeto ON eproid = ieseproid
                LEFT JOIN
                    equipamento_versao ON eveoid = ieseveoid
                LEFT JOIN
                    marca ON mcaoid = iesmcaoid
                LEFT JOIN
                    modelo ON mlooid = iesmlooid
                WHERE
                    1 = 1";

        if ( isset($parametros->iesoid) && trim($parametros->iesoid) != '' && trim($parametros->acao) != 'pesquisar') {

            $sql .= " AND
                        iesoid = " . intval( $parametros->iesoid ) . "";

        }

        if ( isset($parametros->iesostoid) && trim($parametros->iesostoid) != '' ) {

            $sql .= " AND
                        iesostoid = " . intval( $parametros->iesostoid ) . "";

        }

        if ( isset($parametros->ieseqcoid) && trim($parametros->ieseqcoid) != '' ) {

            $sql .= " AND
                        ieseqcoid = " . intval( $parametros->ieseqcoid ) . "";

        }

        if ( isset($parametros->ieseproid) && trim($parametros->ieseproid) != '' ) {

            $sql .= " AND
                        ieseproid = " . intval( $parametros->ieseproid ) . "";

        }

        if ( isset($parametros->ieseveoid) && trim($parametros->ieseveoid) != '' ) {

            $sql .= " AND
                        ieseveoid = " . intval( $parametros->ieseveoid ) . "";

        }

        if ( isset($parametros->iesotioid) && trim($parametros->iesotioid) != '' ) {

            $sql .= " AND
                        iesotioid = " . intval( $parametros->iesotioid ) . "";

        }

        if ( isset($parametros->iesmcaoid) && trim($parametros->iesmcaoid) != '' ) {

            $sql .= " AND
                        iesmcaoid = " . intval( $parametros->iesmcaoid ) . "";

        }

        if ( isset($parametros->iesmlooid) && trim($parametros->iesmlooid) != '' ) {

            $sql .= " AND
                        iesmlooid = " . intval( $parametros->iesmlooid ) . "";

        }

        $sql .= "ORDER BY ostdescricao, otidescricao, eqcdescricao, eprnome, eveversao, mcamarca, mlomodelo";

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

            //busca produtos
            $sqlProdutos = "SELECT
                                iespprdoid,
                                iespquantidade,
                                prdproduto,
                                (CASE WHEN prdpremium IS TRUE THEN 'SIM' ELSE 'NÃO' END) AS premium
                            FROM
                                item_essencial_servico_produto
                            INNER JOIN
                                produto ON prdoid = iespprdoid
                            WHERE
                                iespiesoid = ".$registro->iesoid."
                            ORDER BY
                                prdproduto ASC
                            ";

            $rsProdutos = $this->executarQuery($sqlProdutos);
            while ($regProdutos = pg_fetch_object($rsProdutos)) {
                $registro->iespiesoid[] = $regProdutos;
            }

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
					iesoid,
					iesostoid,
					iesotioid,
					ieseqcoid,
					ieseproid,
					ieseveoid,
					iesmcaoid,
					iesmlooid,
					iesdt_cadastro,
					iesusuoid_cadastro,
					iesdt_alteracao,
					iesusuoid_alteracao,
                    otitipo AS iesotitipo
				FROM
					item_essencial_servico
                INNER JOIN
                    os_tipo_item
                    ON  iesotioid = otioid
				WHERE
					iesoid =" . intval( $id ) . "";

		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

        //busca Materiais
        $sqlMateriais = "SELECT
                                iespprdoid,
                                iespquantidade,
                                (
                                    CASE WHEN prdpremium IS TRUE
                                    THEN prdproduto
                                    ELSE prdproduto  || ' * não pode instalar em cliente premium'
                                    END
                                ) AS prdproduto
                            FROM
                                item_essencial_servico_produto
                            INNER JOIN
                                produto ON prdoid = iespprdoid
                            WHERE
                                iespiesoid = ".$retorno->iesoid;

        $rsMateriais = $this->executarQuery($sqlMateriais);
        while ($regMateriais = pg_fetch_object($rsMateriais)) {
            $retorno->iespiesoid[] = $regMateriais;
            $retorno->materiaisCadastrados[$regMateriais->iespprdoid] = true;
        }

		return $retorno;
	}


    /**
     * Pesquisar se existe registro igual na base de dados
     * @param  stdClass $parametros
     * @return [type] ID do registro
     */
    public function pesquisarExistente(stdClass $parametros){

        $retorno = array();

        $sql = " SELECT
                    iesoid
                FROM
                    item_essencial_servico
                LEFT JOIN
                    os_tipo ON ostoid = iesostoid
                LEFT JOIN
                    os_tipo_item ON otioid = iesotioid
                LEFT JOIN
                    equipamento_classe ON eqcoid = ieseqcoid
                LEFT JOIN
                    equipamento_classe_instalacao ON eqcieqcoid = eqcoid
                LEFT JOIN
                    equipamento_projeto ON eproid = ieseproid
                LEFT JOIN
                    equipamento_versao ON eveoid = ieseveoid
                LEFT JOIN
                    marca ON mcaoid = iesmcaoid
                LEFT JOIN
                    modelo ON mlooid = iesmlooid
                WHERE
                    1 = 1";

        if ( isset($parametros->iesostoid) && trim($parametros->iesostoid) != '' ) {

            $sql .= " AND
                        iesostoid = " . intval( $parametros->iesostoid ) . "";

        }

        if ( isset($parametros->ieseqcoid) && trim($parametros->ieseqcoid) != '' ) {

            $sql .= " AND
                        ieseqcoid = " . intval( $parametros->ieseqcoid ) . "";

        }

        if ( isset($parametros->ieseproid) && trim($parametros->ieseproid) != '' ) {

            $sql .= " AND
                        ieseproid = " . intval( $parametros->ieseproid ) . "";

        }

        if ( isset($parametros->ieseveoid) && trim($parametros->ieseveoid) != '' ) {

            $sql .= " AND
                        ieseveoid = " . intval( $parametros->ieseveoid ) . "";

        }

        if ( isset($parametros->iesotioid) && trim($parametros->iesotioid) != '' ) {

            $sql .= " AND
                        iesotioid = " . intval( $parametros->iesotioid ) . "";

        }

        if ( isset($parametros->iesmcaoid) && trim($parametros->iesmcaoid) != '' ) {

            $sql .= " AND
                        iesmcaoid = " . intval( $parametros->iesmcaoid ) . "";

        }

        if ( isset($parametros->iesmlooid) && trim($parametros->iesmlooid) != '' ) {

            $sql .= " AND
                        iesmlooid = " . intval( $parametros->iesmlooid ) . "";

        }

        $rs = $this->executarQuery($sql);

        //se não encontrar resultados
        if(pg_num_rows($rs) == 0){
            return $retorno;
        }


        while($registro = pg_fetch_object($rs)){

            //busca produtos
            $sqlProdutos = " SELECT
                                iespprdoid,
                                iespquantidade
                            FROM
                                item_essencial_servico_produto
                            WHERE
                                iespiesoid = ".$registro->iesoid."
                            ";

            $rsProdutos = $this->executarQuery($sqlProdutos);
            while ($regProdutos = pg_fetch_array($rsProdutos)) {
                $arrProdutos[$regProdutos['iespprdoid']] = $regProdutos['iespquantidade'];
            }

            //FAZ AS COMPARACOES

            //se ambos forem não existir materiais
            if( count($parametros->iespprdoid) == 0 && count($arrProdutos) == 0 ){

                $registro->iespiesoid = $arrProdutos;
                return $registro;
            }

            //se ambos possuem a mesma quantidade
            if( count($parametros->iespprdoid) == count($arrProdutos) ){

                //se ambos não possui diferencas
                if( count( array_diff_key($arrProdutos, $parametros->iespprdoid) ) == 0 ){

                    $registro->iespiesoid = $arrProdutos;
                    return $registro;
                }
            }

            unset($arrProdutos);
        }

        return $retorno;

    }


    /**
     * Busca Tipos de Ordem de Servico
     * @return objeto
     */
    public function getTipoOrdemServico(){

        $retorno = array();

        $sql = " SELECT
                    ostoid,
                    ostdescricao
                FROM
                    os_tipo
                WHERE
                    ostdt_exclusao IS NULL
                AND
                    ostconsidera_essencial IS TRUE
                ORDER BY
                    ostdescricao ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Busca Mortivo da Ordem de Sefviço
     * @param [int] [tipoOS] [Tipo de ordem Servico]
     * @return array
     */
 public function getMotivoOrdemServico($iesoid = 0,$tipoOS = 0,$item){

        $retorno = array();

        if($iesoid > 0 && $item == "") {
            $sqlAux = " AND
                    otitipo != 'K'
                AND
                    otiostoid = $tipoOS";
        } else{
            $sqlAux = "  AND
                    otitipo != 'K'
                AND
                    otiostoid = $tipoOS
                AND
                    otitipo = UPPER('$item') ";
        }

       $sql = " SELECT
                    otioid,
                    otidescricao
                FROM
                    os_tipo_item
                WHERE
                    otidt_exclusao IS NULL
                $sqlAux
                ORDER BY
                    otidescricao ASC";


        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_assoc($rs)){
            $retorno[] = array_map("utf8_encode", $registro);
        }

        return $retorno;
    }

    /**
     * Busca classes do equipamento
     * @return objeto
     */
    public function getClasseEquipamento(){

        $retorno = array();

        $sql = " SELECT
                    eqcoid,
                    eqcdescricao
                FROM
                    equipamento_classe
                WHERE
                    eqcinativo IS NULL
                ORDER BY
                    eqcdescricao ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Busca equipamento
     * @param [int] [eqcoid] [Classe do Equipamento]
     * @return array
     */
    public function getEquipamento($eqcoid = 0){

        $retorno = array();

        $sqlProjetos = " SELECT
                            eqciprojeto
                        FROM
                            equipamento_classe_instalacao
                        WHERE
                            eqciexclusao IS NULL
                        AND
                            eqcieqcoid = $eqcoid";

        $rsProjetos = $this->executarQuery($sqlProjetos);
        $projetos = pg_fetch_all($rsProjetos);
        $removeChars = array('{', '}');
        $substituiChars = array('', '');

        $arrProjetos = str_replace($removeChars, $substituiChars, $projetos[0]['eqciprojeto']);

        if(strlen(trim($arrProjetos)) > 0) {

            $sqlEquipamentos = "SELECT
                                    eproid,
                                    eprnome
                                FROM
                                    equipamento_projeto
                                WHERE
                                    eproid IN (" . $arrProjetos . ")
                                AND
                                    eprnome <> ''
                                ORDER BY
                                    eprnome";

            $rsEquipamentos = $this->executarQuery($sqlEquipamentos);
            while($registro = pg_fetch_assoc($rsEquipamentos)){
                $retorno[] = array_map("utf8_encode", $registro);
            }
        }

        return $retorno;
    }

    /**
     * Busca Versão do equipamento
     * @param [int] [ieseproid] [Equipamento]
     * @return array
     */
    public function getVersao($ieseproid = 0){

        $retorno = array();

        $sql = " SELECT
                    eveoid,
                    eveversao
                FROM
                    equipamento_versao
                WHERE
                    evedt_exclusao IS NULL
                AND
                    eveprojeto = $ieseproid
                AND
                    eveversao <> ''
                ORDER BY
                    eveversao ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_assoc($rs)){
            $retorno[] = array_map("utf8_encode", $registro);
        }

        return $retorno;
    }

    /**
     * BUsca Materiais / Acessórios
     * prdptioid: 1=imobilizado; 2=estoque
     * prdgrmoid: 7=MATERIAL DE INSTALAÇÃO; 10=PUBLICIDADE; 41=MATERIAL MANUTENÇAO DE EQUIPAMENTOS; 34=IMOBILIZADO PARA LOCAÇÃO
     * @return objeto
     */
    public function getMateriais(){

        $retorno = array();

        $sql = " SELECT
                    prdoid,
                    (
                        CASE WHEN prdpremium IS TRUE
                        THEN prdproduto
                        ELSE prdproduto  || ' * não pode instalar em cliente premium'
                        END
                    ) AS prdproduto
                FROM
                    produto
                WHERE
                    prddt_exclusao IS NULL
                AND
                    prdptioid in (1, 2)
                AND
                    prdgrmoid IN (7, 10, 41, 34)
                AND
                    prdproduto <> ''
                ORDER BY
                    prdproduto ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Busca Marca do Veículo
     * @return objeto
     */
    public function getMarcaVeiculo(){

        $retorno = array();

        $sql = " SELECT
                    mcaoid,
                    mcamarca
                FROM
                    marca
                WHERE
                    mcadt_exclusao IS NULL
                AND
                    mcamarca <> ''
                ORDER BY
                    mcamarca ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Busca Modelo do Veículo
     * @param [int] [iesmcaoid] [Marca do Veículo]
     * @return objeto
     */
    public function getModeloVeiculo($iesmcaoid = 0){

        $retorno = array();

        $sql = " SELECT
                    mlooid,
                    mlomodelo
                FROM
                    modelo
                WHERE
                    mlodt_exclusao IS NULL
                AND
                    mlomcaoid = $iesmcaoid
                AND
                    mlomodelo <> ''
                ORDER BY
                    mlomodelo ASC";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_assoc($rs)){
            $retorno[] = array_map("utf8_encode", $registro);
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
                    item_essencial_servico
                    (
                        iesostoid,
                        ieseqcoid,
                        ieseproid,
                        ieseveoid,
                        iesotioid,
                        iesmcaoid,
                        iesmlooid,
                        iesusuoid_cadastro
                    )
                VALUES
                    (
                        " . ( (trim($dados->iesostoid) == '' ) ? 'NULL' : intval( $dados->iesostoid ) ) . ",
                        " . ( (trim($dados->ieseqcoid) == '' ) ? 'NULL' : intval( $dados->ieseqcoid ) ) . ",
                        " . ( (trim($dados->ieseproid) == '' ) ? 'NULL' : intval( $dados->ieseproid ) ) . ",
                        " . ( (trim($dados->ieseveoid) == '' ) ? 'NULL' : intval( $dados->ieseveoid ) ) . ",
                        " . ( (trim($dados->iesotioid) == '' ) ? 'NULL' : intval( $dados->iesotioid ) ) . ",
                        " . ( (trim($dados->iesmcaoid) == '' ) ? 'NULL' : intval( $dados->iesmcaoid ) ) . ",
                        " . ( (trim($dados->iesmlooid) == '' ) ? 'NULL' : intval( $dados->iesmlooid ) ) . ",
                        " . $this->usarioLogado . "
                    )
                RETURNING
                    iesoid
                ";

        $rs = $this->executarQuery($sql);

        //busca o id do registro inserido
        $iesoid = pg_fetch_result($rs, 0, 'iesoid');

        //se não existir Materiais / Acessórios encerra
        if(count($dados->iespprdoid) == 0){
            return true;
        }

        //insere Materiais/Acessorios
        $arrProduto = array();
        foreach ($dados->iespprdoid as $item => $qtd) {
            $arrProduto[] = "(". $iesoid .",". $item .",". $qtd .")";
        }
        $strProdutos = implode(",", $arrProduto);

        $insertProdutos = "INSERT INTO item_essencial_servico_produto (iespiesoid, iespprdoid, iespquantidade) VALUES " . $strProdutos;

        if($this->executarQuery($insertProdutos)){
            return true;
        }else{
            return false;
        }
	}

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizar(stdClass $dados){

		$sql = "UPDATE
					item_essencial_servico
				SET
                    iesostoid = " . ( (trim($dados->iesostoid) == '' ) ? 'NULL' : intval( $dados->iesostoid ) ) . ",
                    ieseqcoid = " . ( (trim($dados->ieseqcoid) == '' ) ? 'NULL' : intval( $dados->ieseqcoid ) ) . ",
                    ieseproid = " . ( (trim($dados->ieseproid) == '' ) ? 'NULL' : intval( $dados->ieseproid ) ) . ",
                    ieseveoid = " . ( (trim($dados->ieseveoid) == '' ) ? 'NULL' : intval( $dados->ieseveoid ) ) . ",
                    iesotioid = " . ( (trim($dados->iesotioid) == '' ) ? 'NULL' : intval( $dados->iesotioid ) ) . ",
                    iesmcaoid = " . ( (trim($dados->iesmcaoid) == '' ) ? 'NULL' : intval( $dados->iesmcaoid ) ) . ",
                    iesmlooid = " . ( (trim($dados->iesmlooid) == '' ) ? 'NULL' : intval( $dados->iesmlooid ) ) . ",
                    iesusuoid_alteracao = " . $this->usarioLogado . ",
                    iesdt_alteracao = NOW()
				WHERE
					iesoid = " . intval( $dados->iesoid ) . "";

		$this->executarQuery($sql);

        //Remove Materiais/ACessorios atuais
        $deleteProdutos = "DELETE FROM item_essencial_servico_produto WHERE iespiesoid =  " . intval( $dados->iesoid );
        $this->executarQuery($deleteProdutos);

        //se não existir Materiais / Acessórios encerra
        if(count($dados->iespprdoid) == 0){
            return true;
        }

        //insere Materiais/Acessorios
        $arrProduto = array();
        foreach ($dados->iespprdoid as $item => $qtd) {
            $arrProduto[] = "(". $dados->iesoid .",". $item .",". $qtd .")";
        }
        $strProdutos = implode(",", $arrProduto);

        $insertProdutos = "INSERT INTO item_essencial_servico_produto (iespiesoid, iespprdoid, iespquantidade) VALUES " . $strProdutos;

        if($this->executarQuery($insertProdutos)){
            return true;
        }else{
            return false;
        }
	}

    /**
     * Exclui um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir(stdClass $dados){

        $sql = "DELETE
                    FROM
                       item_essencial_servico
                    WHERE
                        iesoid = " . intval( $dados->iesoid ) . "";

        $this->executarQuery($sql);

        return true;
    }


    /**
     * Utilizado para "IMPORTAÇÂO"
     * Busca IDS dos registros de cada linha do arquivo CSV
     * [n][0] = Tipo de ordem de serviço
     * [n][1] = Motivo da ordem de serviço
     * [n][2] = Classe do equipamento
     * [n][3] = Equipamento
     * [n][4] = Versão do equipamento
     * [n][5] = Modelo do veículo
     * [n][6] = Marca do veículo
     * [n][7] = Exclusão
     * [n][8] = Quantidade
     * [n][col >= 9] = Materiais / Acessórios
     * @param  stdClass $dados
     * @return [type]               [description]
     */
    public function insereImportacao($dados){

        //verifica a linha com maior quantidade de colunas, pois não tem limite de materiais/acessórios
        $numCol = 0;
        foreach ($dados as $key => $value) {
            if(count($value) > $numCol){
                $numCol = count($value);
            }
        }

        //verifica se existe colunas de materiais/acessórios
        $colMateriais = $numCol - 10; //9 é a quantidade de colunas obrigatórias

        //cria colunas conforma quantidade de materiais/acessórios
        $colMateriaisTabela = "";
        $colMateriaisInsert = "";
        for ($i=1; $i <= $colMateriais; $i++) {
            $colMateriaisTabela .= ", material" . $i . " varchar(60)";
            $colMateriaisInsert .= ", material" . $i;
        }

        //cria tabela temporaria para receber dados
        $sqlTabela = "
            DROP TABLE
                IF EXISTS
                    temp_importacao_materiais_essenciais;
                CREATE TEMPORARY TABLE
                    temp_importacao_materiais_essenciais
                (
                    num_linha   int,
                    item_os     varchar(60),
                    tipo_os     varchar(60),
                    motivo_os   varchar(60),
                    classe_eq   varchar(60),
                    equipamento varchar(60),
                    versao_eq   varchar(60),
                    modelo_veic varchar(60),
                    marca_veic  varchar(60),
                    exclusao    varchar(60),
                    quantidade  varchar(60)
                    " . $colMateriaisTabela . ")";

        //echo $sqlTabela."<br><br>";

        if(!$this->executarQuery($sqlTabela)){
            return false;
        }

        //realiza insert dos dados na tabela temporaria
        foreach ($dados as $chave => $valor) {

            $sqlInsert .= "INSERT INTO
                                temp_importacao_materiais_essenciais(
                                    num_linha,
                                    item_os,
                                    tipo_os,
                                    motivo_os,
                                    classe_eq,
                                    equipamento,
                                    versao_eq,
                                    modelo_veic,
                                    marca_veic,
                                    exclusao,
                                    quantidade
                                " . $colMateriaisInsert . "
                                )VALUES(
                                     " . intval($chave) . ",
                                    '" . ((trim($valor[0]) == '') ? "#" : strtoupper(substr( $valor[0],0,1)) ) . "',
                                    '" . ((trim($valor[1]) == '') ? "#" : $valor[1]) . "',
                                    '" . ((trim($valor[2]) == '') ? "#" : $valor[2]) . "',
                                    '" . ((trim($valor[3]) == '') ? "#" : $valor[3]) . "',
                                    '" . ((trim($valor[4]) == '') ? "#" : $valor[4]) . "',
                                    '" . ((trim($valor[5]) == '') ? "#" : $valor[5]) . "',
                                    '" . ((trim($valor[6]) == '') ? "#" : $valor[6]) . "',
                                    '" . ((trim($valor[7]) == '') ? "#" : $valor[7]) . "',
                                    '" . ((trim($valor[8]) == '') ? "#" : $valor[8]) . "',
                                    '" . intval($valor[9]) . "'";

            //inclui dados dos materiais/acessórios
            if( $colMateriais > 0 ){
                for ($i=10; $i < $numCol; $i++) {
                    $string = trim(str_replace("\xc2\xa0","",$valor[$i]));
                    $sqlInsert .= ",'" . ($string == '' ? "#" : $string) . "'";
                }
            }

            $sqlInsert .= ");";
        }

        //executa insert
        if(!$this->executarQuery($sqlInsert)){
            return false;
        }

        return true;
    }

    /**
     * Utilizado para "IMPORTAÇÂO"
     * Busca IDS dos registros de cada linha do arquivo CSV
     * @param  stdClass $dados
     * @return [type]               [description]
     */
    public function getIdsImportacao(){

        //busca colunas de materiais e acessórios da tabela temporária
        $sqlInfo = "SELECT column_name FROM information_schema.columns WHERE table_name = 'temp_importacao_materiais_essenciais' AND column_name ILIKE 'material%' ";

        if($rsInfo = $this->executarQuery($sqlInfo)){
            $colMateriais = pg_fetch_all($rsInfo);
        }else{
            return false;
        }

        //faz a busca substituindo pelos IDS
        $sqlSelect = "
        SELECT num_linha,
                otitipo,
                ostoid,
                otioid,
                eqcoid,
                eproid,
                eveoid,
                mlooid,
                mcaoid,
                exclusao,
                quantidade,
                duplicados";

        //insere colunas de materiais/acessórios
        $aux1 = 1;
        foreach ($colMateriais as $coluna) {
            $sqlSelect .= "
                , item" . $aux1;
            $aux1 ++;
        }

        $sqlSelect .= "
                FROM (
                        SELECT
                        num_linha,
                        (
                            CASE WHEN item_os = '#' THEN '#'
                            ELSE item_os::VARCHAR
                            END
                        ) AS otitipo,
                        (
                            CASE WHEN tipo_os = '#' THEN '#'
                            ELSE ostoid::VARCHAR
                            END
                        ) AS ostoid,
                        (
                            CASE WHEN motivo_os = '#' THEN '#'
                            ELSE otioid::VARCHAR
                            END
                        ) AS otioid,
                        (
                            CASE WHEN classe_eq = '#' THEN '#'
                            ELSE eqcoid::VARCHAR
                            END
                        ) AS eqcoid,
                        (
                            CASE WHEN equipamento = '#' THEN '#'
                            ELSE eproid::VARCHAR
                            END
                        ) AS eproid,
                        (
                            CASE WHEN versao_eq = '#' THEN '#'
                            ELSE eveoid::VARCHAR
                            END
                        ) AS eveoid,
                        (
                            CASE WHEN modelo_veic = '#' THEN '#'
                            ELSE mlooid::VARCHAR
                            END
                        ) AS mlooid,
                        (
                            CASE WHEN marca_veic = '#' THEN '#'
                            ELSE mcaoid::VARCHAR
                            END
                        ) AS mcaoid,
                        exclusao,
                        quantidade,
                        count(*)
                            OVER (PARTITION BY
                                otitipo,
                                ostoid,
                                otioid,
                                eqcoid,
                                eqcieqcoid,
                                eproid,
                                eveoid,
                                mlooid,
                                mcaoid,
                                exclusao,
                                quantidade";
        //insere colunas de materiais/acessórios
        foreach ($colMateriais as $coluna) {
            $sqlSelect .= ", ". $coluna['column_name'];

        }
            $sqlSelect .= " ) AS duplicados";

        //insere colunas de materiais/acessórios
        $aux1 = 1;
        foreach ($colMateriais as $coluna) {
            $sqlSelect .= "
                        , (
                            CASE WHEN " . $coluna['column_name'] . " = '#' THEN '#'
                            ELSE mat" . $aux1 . ".prdoid::VARCHAR
                            END
                        ) AS item" . $aux1;
            $aux1 ++;
        }

        $sqlSelect .= "
                    FROM
                        temp_importacao_materiais_essenciais
                    LEFT JOIN
                        os_tipo ON ostdescricao = tipo_os AND ostdt_exclusao IS NULL AND ostconsidera_essencial IS TRUE
                    LEFT JOIN
                        os_tipo_item ON otidescricao = motivo_os AND otidt_exclusao IS NULL AND otitipo LIKE item_os AND otiostoid = ostoid
                    LEFT JOIN
                        equipamento_classe ON eqcdescricao = classe_eq AND eqcinativo IS NULL
                    LEFT JOIN
                        equipamento_classe_instalacao ON eqcieqcoid = eqcoid
                    LEFT JOIN
                        equipamento_projeto ON eprnome = equipamento AND eprnome <> ''
                    LEFT JOIN
                        equipamento_versao ON eveversao = versao_eq AND evedt_exclusao IS NULL AND eveversao <> ''
                    LEFT JOIN
                        marca ON mcamarca = marca_veic AND mcadt_exclusao IS NULL AND mcamarca <> ''
                    LEFT JOIN
                        modelo ON mlomodelo = modelo_veic AND mlodt_exclusao IS NULL AND mlomodelo <> '' AND mlomcaoid = mcaoid";

        //insere colunas de materiais/acessórios
        $aux2 = 1;
        foreach ($colMateriais as $coluna) {
            $sqlSelect .= "
                    LEFT JOIN
                        produto as mat" . $aux2 . " ON " . $coluna['column_name'] . " LIKE TRIM(mat" . $aux2 . ".prdproduto) AND mat" . $aux2 . ".prddt_exclusao IS NULL AND mat" . $aux2 . ".prdptioid IN (1, 2) AND mat" . $aux2 . ".prdgrmoid IN (7, 10, 41, 34) AND mat" . $aux2 . ".prdproduto <> ''";
            $aux2 ++;

        }

        $sqlSelect .= "
                    ORDER BY num_linha
                ) AS foo
            WHERE mlooid <> ''";

        if($rs = $this->executarQuery($sqlSelect)){
            $arrIds = pg_fetch_all($rs);

            return $arrIds;
        }else{
            return false;
        }

    }


    /**
     * Valida relacionamento de dados por linha do arquivo
     */
    public function validaRelacionamento($valores){

        //cria array de erros
        $logErros = array(
            1 => 0,
            2 => 0,
            3 => 0
            );

        // VALIDAÇÂO 1
        //valida Tipo Ordem Servico / Motivo de Ordem Serviço
        if(is_numeric($valores['otioid']) && is_numeric($valores['ostoid'])){
            $sql1 = " SELECT
                        1
                    FROM
                        os_tipo_item
                    WHERE
                        otidt_exclusao IS NULL
                    AND
                        otitipo != 'K'
                    AND
                        otiostoid = ". $valores['ostoid'] ."
                    AND
                        otioid = ". $valores['otioid'] ."
                    ";

            $rs1 = $this->executarQuery($sql1);

            if (pg_num_rows($rs1) == 0){
                $logErros[1] = 1;
            }
        }

        // VALIDAÇÂO 2
        //valida Versão do Equipamento / Equipamento
        if(is_numeric($valores['eproid']) && is_numeric($valores['eveoid'])){
            $sql2 = " SELECT
                        1
                    FROM
                        equipamento_versao
                    WHERE
                        evedt_exclusao IS NULL
                    AND
                        eveprojeto = ". $valores['eproid'] ."
                    AND
                        eveoid = ". $valores['eveoid'] ."
                    AND
                        eveversao <> ''
                    ";

            $rs2 = $this->executarQuery($sql2);

            if (pg_num_rows($rs2) == 0){
                $logErros[2] = 1;
            }
        }

        // VALIDAÇÂO 3
        //valida Marca Veículo / Modelo Veículo
        if(is_numeric($valores['mcaoid']) && is_numeric($valores['mlooid'])){
            $sql3 = " SELECT
                    1
                FROM
                    modelo
                WHERE
                    mlodt_exclusao IS NULL
                AND
                    mlomcaoid = ". $valores['mcaoid'] ."
                AND
                    mlooid = ". $valores['mlooid'] ."
                AND
                    mlomodelo <> ''
                    ";

            $rs3 = $this->executarQuery($sql3);

            if (pg_num_rows($rs3) == 0){
                $logErros[3] = 1;
            }
        }


        return $logErros;

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
            throw new ErrorException($query);
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
