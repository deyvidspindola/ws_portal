<?php

/**
 * Classe CadModeloDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 *
 */
class CadModeloVeiculoDAO extends DAO{

    public function recuperarMarcas($isAtiva) {

        $retorno = array();
        $isAtiva = ($isAtiva == 'I') ? 'FALSE' : 'TRUE';

        $sql = "SELECT
                    mcaoid,
                    UPPER(mcamarca) AS mcamarca
                FROM
                    marca
                WHERE
                    mcadt_exclusao IS NULL
                AND
                    mcastatus IS ". $isAtiva . "
                ORDER BY
                    mcamarca ASC;
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarModelos($idMarca, $isAtiva) {

        $retorno = array();
        $isAtiva = ($isAtiva == 'I') ? 'FALSE' : 'TRUE';
        $i = 0;

        $sql = "SELECT
                    mlooid,
                    UPPER(mlomodelo) AS mlomodelo
                FROM
                    modelo
                WHERE
                    mlodt_exclusao IS NULL
                AND
                    mlostatus IS ". $isAtiva . "
                AND
                    mlomcaoid = ". $idMarca ."
                ORDER BY
                    mlomodelo ASC;
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarMarcaModelo() {

        $retorno = array();

        $sql = "SELECT
                    ( UPPER(mcamarca) || ' - ' || UPPER(mlomodelo) ) AS marca_modelo,
                    mlooid
                FROM
                    marca
                INNER JOIN
                    modelo ON (mlomcaoid = mcaoid)
                WHERE
                    mcadt_exclusao IS NULL
                AND
                    mlodt_exclusao IS NULL
                AND
                    mlostatus IS TRUE
                AND
                    mcastatus IS TRUE
                ORDER BY
                    mcamarca, mlomodelo  ASC;
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function pesquisar(stdClass $parametros, $paginacao = NULL, $ordenacao = NULL){


        $retorno = array();

        $sql = ' SELECT ';

        if (is_null($paginacao)) {
            $sql .= " COUNT(mlooid) as total ";
        } else {
             $sql .="   mlooid,
                        UPPER(mlomodelo) as mlomodelo,
                        UPPER(mcamarca) as mcamarca,
                        mlostatus
                        ";
        }

        $sql .= "
                FROM
                    modelo
                INNER JOIN marca ON (mcaoid = mlomcaoid)
                WHERE
                    mlodt_exclusao IS NULL ";

        if ( isset($parametros->mlomcaoid) && !empty($parametros->mlomcaoid) ) {

            $sql .= " AND
                        mlomcaoid = " . $parametros->mlomcaoid ;
        }

        if ( isset($parametros->mlooid) && !empty($parametros->mlooid) ) {

            $sql .= " AND
                        mlooid = " . $parametros->mlooid ;
        }

        if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            } else {
                 $sql .=  ' ORDER BY mlomodelo';
            }

            $sql .= " LIMIT " . $paginacao->limite . " OFFSET " . $paginacao->offset;
        }

        $rs = $this->executarQuery($sql);

        if (is_null($paginacao)) {
            return pg_fetch_object($rs);
        } else {
            while($registro = pg_fetch_object($rs)){
                $retorno[] = $registro;
            }

            return $retorno;
        }


        return $retorno;
    }

    public function pesquisarPorID( $idModelo ){

        $retorno = new stdClass();

        $sql = "SELECT
                    mlooid,
                    UPPER(mlomodelo) as mlomodelo,
                    mlomcaoid,
                    mlostatus,
                    mlodica,
                    mlomcfoid,
                    mlotipveioid,
                    mlovalvula,
                    mloconversor,
                    mlosensor_volvo,
                    mlobloqueio,
                    mlosleep,
                    mlovlmoid1,
                    mlovlmoid2,
                    mlovlmoid3,
                    mcfmcaoid,
                    mlofipe_codigo,
                    mlocatbase_descricao,
                    mlocatbase_codigo,
                    mloprocedencia,
                    mlonumpassag,
                    paissigla AS mlopaisoid,
                    mlovstoid
                FROM
                    modelo
                LEFT JOIN
                    marca_familia ON (mcfmcaoid = mlomcaoid)
                LEFT JOIN
                    paises ON (paisoid = mlopaisoid)
                WHERE
                    mlooid =" . $idModelo . "";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function recuperarMarcaFamilia( $idMarca) {

        $retorno = array();


        $sql = "SELECT
                    mcfoid,
                    UPPER(mcffamilia) AS mcffamilia
                FROM
                    marca_familia
                WHERE
                    mcfdt_exclusao IS NULL
                AND
                    mcfmcaoid = ". intval($idMarca) ."
                ORDER BY
                    mcffamilia
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarTipoVeiculo() {

        $retorno = array();

        $sql = "SELECT
                    tipvoid,
                    UPPER(tipvdescricao) AS tipvdescricao
                FROM
                    tipo_veiculo
                WHERE
                    tipvexclusao IS NULL
                ORDER BY
                    tipvdescricao
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarSubTipoVeiculo( $idTipo) {

        $retorno = array();

        $sql = "SELECT
                    vstoid,
                    vstdescricao
                FROM
                    veiculo_subtipo
                LEFT JOIN
                    veiculo_tipo_subtipo ON (vtsvstoid = vstoid AND vstdt_exclusao IS NULL)
                LEFT JOIN
                    tipo_veiculo ON (vtstipvoid = tipvoid)
                WHERE
                    vtstipvoid =" . $idTipo . "";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarListaValvula() {

        $retorno = array();


        $sql = "SELECT vlmoid,
                        TRIM('VÁLVULA' FROM TRIM('VALVULA' FROM UPPER(vlmdescricao))) AS vlmdescricao
                FROM
                    valvula_modelo
                ORDER BY
                    vlmdescricao
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }


    public function recuperarListaAcessorio() {

        $retorno = array();

        $tipoObrigacaoFinanceira = array(
                    'Locacao_revenda_baixa_serial' => 3,
                    'Locacao_revenda_baixa_quantidade' => 4,
                    'Locacao_revenda_sem_baixa' => 5
                );

        $sql = "SELECT
                    obroid,
                    obrobrigacao
                FROM
                    obrigacao_financeira
                WHERE
                    obrdt_exclusao IS NULL
                AND
                    obroftoid IN (". implode(',', $tipoObrigacaoFinanceira) .")
                ORDER BY
                    obrobrigacao
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarListaAnos() {

        $anoAtual = intval( date('Y') ) + 1;
        $anoInicial = 1980;
        $listaAnos = array();

        for ($i=$anoAtual; $i >= 1980 ; $i--) {
            $listaAnos[$i] = $i;
        }

        return $listaAnos;
    }

    public function recuperarListaAcessorioModelo( $idModelo ) {

        $retorno = array();

        $sql = "SELECT    mlaioid,
                          mlaiobroid,
                          mlaiano_inicial,
                          mlaiano_final,
                          mlaiinstala_cliente,
                          mlaiinstala_seguradora,
                          obrobrigacao
                     FROM modelo_acessorio_instalacao
                LEFT JOIN obrigacao_financeira ON (mlaiobroid = obroid)
                    WHERE mlaimlooid = " . intval($idModelo) ."
                    AND mlaidt_exclusao IS NULL";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[$registro->mlaiobroid] = $registro;
        }

        return $retorno;
    }

    public function recuperarPaises() {

        $retorno = array();

        $sql = "SELECT
                    paisoid,
                    paissigla,
                    UPPER(paisnome) AS paisnome
                FROM
                    paises
                WHERE
                    paisexclusao IS NULL
                ORDER BY
                    paisnome";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function verificarDuplicidade(stdClass $dados) {

        if( ! empty($dados->mlooid) ) {
            $where = ' AND mlooid != ' . $dados->mlooid;
        } else {
            $where = '';
        }

        $sql = "SELECT EXISTS (
                        SELECT 1 FROM modelo
                        INNER JOIN marca ON (mcaoid = mlomcaoid)
                        WHERE mlodt_exclusao IS NULL
                        ". $where ."
                        AND mcaoid = ".$dados->mlomcaoid."
                        AND mlomodelo = '".trim($dados->mlomodelo)."'
                        ) AS existe";

        $rs = $this->executarQuery($sql);

        $registro = pg_fetch_object($rs);
        $retorno = ($registro->existe == 'f') ? FALSE : TRUE;

        return $retorno;
    }

    public function inserir(stdClass $dados){

        $sql = "INSERT INTO
                    modelo (
                        mlomodelo,
                        mlomcaoid,
                        mlotipveioid,
                        mloconversor,
                        mlovalvula,
                        mlosleep,
                        mlovlmoid1,
                        mlovlmoid2,
                        mlovlmoid3,
                        mlodica,
                        mlosensor_volvo,
                        mlobloqueio,
                        mlomcfoid,
                        mlostatus,
                        mlousuoid_inclusao,
                        mlofipe_codigo,
                        mlocatbase_descricao,
                        mlocatbase_codigo,
                        mloprocedencia,
                        mlonumpassag,
                        mlovstoid,
                        mlopaisoid
                        )
                VALUES
                    (
                        '" . pg_escape_string( $dados->mlomodelo ) . "',
                        " .  $dados->mlomcaoid. ",
                        " .  $dados->mlotipveioid. ",
                        '" . $dados->mloconversor . "',
                        '" . $dados->mlovalvula ."',
                        '" . $dados->mlosleep ."',
                        ".   $dados->mlovlmoid1 .",
                        ".   $dados->mlovlmoid2 .",
                        ".   $dados->mlovlmoid3 .",
                        '".  pg_escape_string( $dados->mlodica ) ."',
                        '".  $dados->mlosensor_volvo . "',
                        '".  $dados->mlobloqueio ."',
                        ".   $dados->mlomcfoid .",
                        '".  $dados->mlostatus ."',
                        " .  $this->usuarioLogado .",
                        '" . pg_escape_string( $dados->mlofipe_codigo ) . "',
                        '" . pg_escape_string( $dados->mlocatbase_descricao ) . "',
                        '" . pg_escape_string( $dados->mlocatbase_codigo ) . "',
                        '" . pg_escape_string( $dados->mloprocedencia ) . "',
                        " .  intval($dados->mlonumpassag) . ",
                        ".   $dados->mlovstoid.",
                        ".   $dados->mlopaisoid."

                    ) RETURNING mlooid";

        $rs = $this->executarQuery($sql);

        $registro = pg_fetch_object($rs);

        return $registro->mlooid;
    }

    public function inserirAcessorio( $idModelo, $listaAcessorio ) {

        $sql = "PREPARE prepare_statement AS INSERT INTO
                    modelo_acessorio_instalacao (
                    mlaimlooid,
                    mlaiobroid,
                    mlaiano_inicial,
                    mlaiano_final,
                    mlaiusuoid_cadastro,
                    mlaiinstala_cliente,
                    mlaiinstala_seguradora ) VALUES ($1, $2, $3, $4, $5, $6, $7)";

        $rs = $this->executarQuery($sql);
        $registro = pg_fetch_object($rs);

        foreach ($listaAcessorio as $key => $dados) {

            $sql = "EXECUTE prepare_statement
                    (
                        " . $idModelo . ",
                        " . $dados['mlaiobroid'] . ",
                        " . $dados['mlaiano_inicial'] . ",
                        " . $dados['mlaiano_final'] . ",
                        " . $this->usuarioLogado . ",
                        '" . $dados['mlaiinstala_cliente'] . "',
                        '" . $dados['mlaiinstala_seguradora'] . "'
                    )";

            $rs = $this->executarQuery($sql);
            $registro = pg_fetch_object($rs);

        }

        return TRUE;
    }

    public function atualizar(stdClass $dados){

        $sql = "UPDATE
                    modelo
                SET
                    mlomodelo            = '" . pg_escape_string( $dados->mlomodelo ) . "',
                    mlomcaoid            = " . $dados->mlomcaoid. ",
                    mlotipveioid         = " . $dados->mlotipveioid. ",
                    mloconversor         = '" . $dados->mloconversor . "',
                    mlovalvula           = '" . $dados->mlovalvula ."',
                    mlosleep             = '" . $dados->mlosleep ."',
                    mlovlmoid1           = ". $dados->mlovlmoid1 .",
                    mlovlmoid2           = ". $dados->mlovlmoid2 .",
                    mlovlmoid3           = ". $dados->mlovlmoid3 .",
                    mlodica              = '". pg_escape_string( $dados->mlodica ) ."',
                    mlosensor_volvo      = '". $dados->mlosensor_volvo . "',
                    mlobloqueio          = '". $dados->mlobloqueio ."',
                    mlomcfoid            = ". $dados->mlomcfoid .",
                    mlostatus            = '". $dados->mlostatus ."',
                    mlousuoid_inclusao   = " . $this->usuarioLogado .",
                    mlofipe_codigo       = '" . pg_escape_string( $dados->mlofipe_codigo ) . "',
                    mlocatbase_descricao = '" . pg_escape_string( $dados->mlocatbase_descricao ) . "',
                    mlocatbase_codigo    = '" . pg_escape_string( $dados->mlocatbase_codigo ) . "',
                    mloprocedencia       = '" . pg_escape_string( $dados->mloprocedencia ) . "',
                    mlonumpassag         = " . intval($dados->mlonumpassag) . ",
                    mlovstoid            = ". $dados->mlovstoid.",
                    mlopaisoid           = ". $dados->mlopaisoid.",
                    mlodt_alteracao      = NOW(),
                    mlousuoid_alteracao  = ". $this->usuarioLogado ."
                WHERE
                    mlooid = " . $dados->mlooid . "";

        $rs = $this->executarQuery($sql);

        return TRUE;
    }

    public function excluir( $idModelo ){

        $sql = "UPDATE
                    modelo
                SET
                    mlodt_exclusao = NOW(),
                    mlousuoid_inclusao = ". $this->usuarioLogado ."
                WHERE
                    mlooid = " . intval( $idModelo ) . "";

        $rs = $this->executarQuery($sql);

        $this->excluirAcessorios( $idModelo );

        return TRUE;
    }

    private function excluirAcessorios( $idModelo ) {

        $sql = "UPDATE
                    modelo_acessorio_instalacao
                SET
                    mlaidt_exclusao = NOW(),
                    mlaiusuoid_exclusao = ". $this->usuarioLogado ."
                WHERE
                    mlaimlooid = " . intval( $idModelo ) . "
                AND
                    mlaidt_exclusao IS NULL";

        $rs = $this->executarQuery($sql);

        return TRUE;
    }

    public function excluirAcessoriosPorID( $listaID, $idModelo ){

        $sql = "UPDATE
                    modelo_acessorio_instalacao
                SET
                    mlaidt_exclusao = NOW(),
                    mlaiusuoid_exclusao = ". $this->usuarioLogado ."
                WHERE
                    mlaimlooid = " . intval( $idModelo ) . "
                AND
                    mlaioid NOT IN (". implode(',', $listaID) .")";

        $rs = $this->executarQuery($sql);

        return TRUE;
    }

}
?>
