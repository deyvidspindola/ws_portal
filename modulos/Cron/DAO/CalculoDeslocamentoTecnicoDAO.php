<?php

class CalculoDeslocamentoTecnicoDAO {

    const STATUS_OS_CONCLUIDA = 3;
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    protected $conn;
    private $debugQuery = FALSE;

    public function __construct($conn) {
        if(is_null($conn)){
            Global $conn;
        }
        $this->conn = $conn;
    }

    public function beginTransaction(){
        $resultado = pg_query($this->conn, 'BEGIN');
        if (false === $resultado) {
            throw new Exception('Houve um erro ao iniciar a transação');
        }
    }

    public function commitTransaction(){
        $resultado = pg_query($this->conn, 'COMMIT');
        if (false === $resultado) {
            throw new Exception('Houve um erro ao commitar a transação');
        }
        pg_query($this->conn, 'COMMIT');
    }

    public function rollbackTransaction(){
        $resultado = pg_query($this->conn, 'ROLLBACK');
        if (false === $resultado) {
            throw new Exception('Houve um erro ao executar o rollback a transação');
        }
    }

    public function affected_rows($result){
        $resultado = pg_affected_rows($result);
        if (false === $resultado) {
            throw new Exception('Houve um erro ao retornar o número de linhas da transação');
        }
        return $resultado;
    }

    public function setDebugQuery($debug) {
        $this->debugQuery = $debug;
    }

    public function executarQuery($query){

        if( $this->debugQuery === TRUE ) {
            echo $query . "<hr/>";
        }

        if(!$rs = pg_query($this->conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
            }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }

    public function getIdHistorico(){
        $id = null;

        $query = "SELECT mhcoid FROM motivo_hist_corretora WHERE mhcdescricao = 'Deslocamento - Pedágio'";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $objeto = pg_fetch_object($result);
            $id = $objeto->mhcoid;
        }

        return $id;
    }

    public function verificaVisitaImprodutiva($data, $tecnicoTeste = null){

        $retorno = array();

        $query = "SELECT DISTINCT *, 'VI' as tipo FROM(
                        SELECT DISTINCT ON (ordoid) ordoid,
                            ovicadastro::date as data_atendimento,
                            TO_CHAR( ovicadastro::TIME, 'HH24:MI') as hora_atendimento,
                            EXTRACT(DOW FROM ovicadastro) AS diaSemana,
                            oviitloid AS id_tecnico,
                            (CASE WHEN orddt_asso_rep IS NULL THEN FALSE ELSE TRUE END) AS os_direcionada
                        FROM ordem_servico
                            JOIN ordem_servico_visita_improdutiva ON oviordoid = ordoid
                        WHERE ovicadastro::date = '$data'
                        AND ovifaturamento IS FALSE
                        AND oviitloid IS NOT NULL";

                    if( ! is_null($tecnicoTeste) ){
                         $query .= " AND oviitloid = " . intval($tecnicoTeste);
                    }

                    $query .="
                                GROUP BY
                                ordoid,
                                ovicadastro,
                                oviitloid
                            ORDER BY
                                ordoid, oviitloid
                            ) AS foo
                    ORDER BY foo.id_tecnico";

        $result = $this->executarQuery($query);

        while ($fch = pg_fetch_object($result)) {
            $retorno[$fch->id_tecnico][] = $fch;
        }

        return $retorno;
    }

    public function verificaComissaoInstalacao($ordensElegiveis, $data, &$ordensVI, $tecnicoTeste = null){

        $ordens  = array();

        //Agrupa as OSs encontradas na Visita Improdutiva para desconsidera-las
        foreach ($ordensElegiveis as $idTecnico => $agendamentos) {
            foreach ($agendamentos as $chave => $ordem) {
                $ordens[] = $ordem->ordoid;
            }
        }
        if (count($ordens) > 0) {
            $ordensVI = implode(",", $ordens);
        }

        $query = "SELECT DISTINCT *, 'CI' as tipo FROM(
                    SELECT
                        DISTINCT ON (ordoid) ordoid,
                        MIN(cmidata)::date AS data_atendimento,
                        TO_CHAR( MIN(cmidata)::time, 'HH24:MI' ) AS hora_atendimento,
                        EXTRACT(DOW FROM cmidata) AS diaSemana,
                        cmiitloid AS id_tecnico,
                        (CASE WHEN orddt_asso_rep IS NULL THEN FALSE ELSE TRUE END) AS os_direcionada
                    FROM ordem_servico
                        JOIN comissao_instalacao ON cmiord_serv = ordoid
                    WHERE cmidata::date = '$data'
                    AND cmiitloid IS NOT NULL";

        if( ! is_null($tecnicoTeste) ){
             $query .= " AND cmiitloid = " . intval($tecnicoTeste);
        }

        $query .= "
                    AND cmiexclusao IS NULL
                    AND cmiovioid IS NULL
                    AND cmiord_serv NOT IN ($ordensVI)
                    AND ordstatus = ".self::STATUS_OS_CONCLUIDA."
                    GROUP BY
                        ordoid,
                        cmidata,
                        cmiitloid
                        ) AS foo
                ORDER BY
                    foo.id_tecnico";

        $result = $this->executarQuery($query);

        while ($fch = pg_fetch_object($result)) {
            $ordensElegiveis[$fch->id_tecnico][] = $fch;
        }

        return $ordensElegiveis;
    }

    public function getUf($estado) {

        $retorno = null;

        $estado = strtolower($estado);

        $query = "SELECT estuf AS uf,
                    estnome AS nome
                FROM estado
                WHERE LOWER(TRANSLATE(estnome, 'áàãâéêíìóôõúüç','aaaaeeiiooouuc')) = TRANSLATE('$estado', 'áàãâéêíìóôõúüç','aaaaeeiiooouuc')";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $retorno = pg_fetch_object($result);
        }

        return $retorno;
    }

    public function getCidade($cidade) {

        $retorno = null;

        $cidade = strtolower($cidade);

        $query = "SELECT clcnome AS nome
                FROM correios_localidades
                WHERE LOWER(TRANSLATE(clcnome, 'áàãâéêíìóôõúüç','aaaaeeiiooouuc')) = TRANSLATE('$cidade', 'áàãâéêíìóôõúüç','aaaaeeiiooouuc')";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $retorno = pg_fetch_object($result);
        }

        return $retorno;
    }

    public function getTecnicoRepresentante($idTecnico) {

        $retorno = null;

        $query = "SELECT itlnome AS instalador
                    , repnome AS representante
                    , repoid
                FROM instalador
                    JOIN representante ON repoid = itlrepoid
                WHERE
                    itloid = $idTecnico";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $retorno = pg_fetch_object($result);
        }

        return $retorno;
    }

    public function ordenaSequenciaAtendimentos($atendimento, $ordensVI){

        $retorno = array();

        $query = "SELECT tipo,
                    ordoid,
                    TO_CHAR(hora_atendimento::time, 'HH24:MI') AS hora,
                    hora_atendimento::date AS data,
                    ( SELECT
                        (CASE WHEN orddt_asso_rep IS NULL THEN FALSE ELSE TRUE END)
                      FROM
                        ordem_servico os
                      WHERE
                          os.ordoid = foo.ordoid
                    ) AS os_direcionada
                FROM
                    (
                    SELECT 'VI' AS tipo,
                        oviordoid AS ordoid,
                        MIN(ovicadastro) AS hora_atendimento
                    FROM ordem_servico_visita_improdutiva
                    WHERE ovicadastro::date = '$atendimento->data'
                        AND oviordoid IN ($atendimento->ordens)
                        AND oviitloid = $atendimento->idTecnico
                    GROUP BY oviordoid

                    UNION

                    SELECT 'CI' AS tipo,
                        cmiord_serv AS ordoid,
                        MIN (cmidata) AS hora_atendimento
                    FROM comissao_instalacao
                    WHERE TRUE
                        AND cmiexclusao IS NULL
                        AND cmidata::date = '$atendimento->data'
                        AND cmiord_serv NOT IN ($ordensVI)
                        AND cmiord_serv IN ($atendimento->ordens)
                        AND cmiitloid = $atendimento->idTecnico
                    GROUP BY cmiord_serv
                ) AS foo ORDER BY hora_atendimento";

        $result = $this->executarQuery($query);

        while ($fch = pg_fetch_object($result)) {
            $retorno[] = $fch;
        }

        return $retorno;
    }

    public function getEnderecoRepresentante($idTecnico){

        $enderecoPS = null;

        $query = "SELECT
                    CONCAT(endvrua,', ',endvnumero) AS logradouro,
                    endvnumero AS numero,
                    endvcomplemento AS complemento,
                    endvbairro AS bairro,
                    endvcidade AS cidade,
                    endvcep AS cep,
                    UPPER(estnome) AS estado,
                    estuf AS uf
                FROM instalador
                    JOIN endereco_representante ON endvrepoid = itlrepoid
                    JOIN estado ON estuf = endvuf
                WHERE itloid = $idTecnico";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $enderecoPS = pg_fetch_object($result);
        }

        return $enderecoPS;
    }

    public function getEnderecoAgendamento($ordem, $data){

        $endereco = new stdClass();
        $where  =  ($ordem->tipo == 'VI') ? " AND osadata = '$data'" : '';

        $query = "SELECT
                    osaoid
                    ,osacep AS cep
                    , UPPER(estnome) AS estado
                    , estuf AS uf
                    , clcnome AS cidade
                    , cbanome AS bairro
                    , osaendereco AS logradouro
                    , osacoordx
                    , osacoordy
                    , osaordoid as ordoid
                    , '$data' AS data
                    , osadata
                    , osahora
                    , osatipo_atendimento
                    , COALESCE(itlkm_abrangencia, 0) AS itlkm_abrangencia
                    , COALESCE(itlkm_litro, 0) AS itlkm_litro
                FROM ordem_servico_agenda
                    JOIN estado ON estoid = osaestoid
                    JOIN correios_localidades ON clcoid = osaclcoid
                    LEFT JOIN correios_bairros ON cbaoid = osacbaoid
                    JOIN instalador ON itloid = ".intval($ordem->id_tecnico) ."
                WHERE osaordoid = ". intval($ordem->ordoid)."
                ".$where."
                ORDER BY osaoid DESC LIMIT 1";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $endereco = pg_fetch_object($result);
        }

        return $endereco;
    }

    public function getEnderecoAgendamentoDirecionado($ordem, $data){

        $endereco = null;

        $query = "SELECT
                    '' AS osaoid
                    , '' AS cep
                    , '' AS estado
                    , '' AS uf
                    , '' AS cidade
                    , '' AS bairro
                    , '' AS logradouro
                    , 0 AS osacoordx
                    , 0 AS osacoordy
                    , ". $ordem->ordoid ." AS ordoid
                    , '$data' AS data
                    , '$data' AS osadata
                    , '00:00:00' AS osahora
                    ,  '' AS osatipo_atendimento
                    , COALESCE(itlkm_abrangencia, 0) AS itlkm_abrangencia
                    , COALESCE(itlkm_litro, 0) AS itlkm_litro
                FROM
                    instalador
                WHERE
                    itloid = ".intval($ordem->id_tecnico);

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $endereco = pg_fetch_object($result);
        }

        return $endereco;
    }


    public function gravarHistorico($historico){

        $query = "INSERT INTO ordem_situacao (
                    orsordoid
                    , orsusuoid
                    , orsdt_agenda
                    , orshr_agenda
                    , orsstatus
                    , orssituacao
                ) VALUES (
                    $historico->orsordoid
                    , 2750
                    , '$historico->orsdt_agenda'
                    , '$historico->orshr_agenda'
                    , $historico->orsstatus
                    , '$historico->orssituacao'
                )";

        $retorno = new stdClass();

        try {

            $this->executarQuery($query);

            $retorno->status = true;
            $retorno->msg    = "Inserido com sucesso.";

        } catch (Exception $e) {
            $retorno->status = false;
            $retorno->msg = "ERRO: ".$e->getMessage()." \n QUERY: ".$query;
        }

        return $retorno;
    }

    public function atualizarOrdemServico($ordemServico) {

        if ($ordemServico->retorno) {
            $update = " orddesloc_autoriz         = ROUND($ordemServico->orddesloc_autoriz)
                      , orddesloc_pedagio         = $ordemServico->orddesloc_pedagio
                      , orddesloc_liberado        = '$ordemServico->orddesloc_liberado' ";

            $where = "";

        } else {

            $update = " orddesloc_autoriz         = ROUND($ordemServico->orddesloc_autoriz)
                      , orddesloc_pedagio         = $ordemServico->orddesloc_pedagio
                      , orddesloc_valorkm         = $ordemServico->orddesloc_valorkm
                      , orddesloc_origem          = '$ordemServico->orddesloc_origem'
                      , orddesloc_destino         = '$ordemServico->orddesloc_destino'
                      , orddesloc_liberado        = '$ordemServico->orddesloc_liberado'
                      ";

            $where = "AND (orddesloc_autoriz IS NULL OR orddesloc_autoriz = 0)
                      AND (orddesloc_pedagio IS NULL OR orddesloc_pedagio = 0) ";
        }

        $query = "UPDATE ordem_servico SET
                    $update
                WHERE
                    ordoid = $ordemServico->ordoid
                    $where ";

        $retorno = new stdClass();

        try {

            $result = $this->executarQuery($query);

            $retorno->status = true;
            $retorno->msg    = "Atualizado com sucesso.";
            $retorno->nr_linhas_afetadas = $this->affected_rows($result);

        } catch (Exception $e) {
            $retorno->status = false;
            $retorno->msg = "ERRO: ".$e->getMessage()." \n QUERY: ".$query;
        }

        return $retorno;
    }

    public function buscaComissaoInstalacao($comissaoInstalacao) {

        $comissaoInstalacaoAnterior = null;

        $query = "SELECT cmioid
                        , cmideslocamento
                        , cmivalor_pedagio
                        , cmidesloc_pedagio_chegada
                        , cmidesloc_km_chegada
                FROM comissao_instalacao
                WHERE TRUE
                    AND cmiexclusao IS NULL
                    AND cmiord_serv   = $comissaoInstalacao->cmiord_serv
                    AND cmiitloid     = $comissaoInstalacao->cmiitloid
                    AND cmidata::date = '$comissaoInstalacao->data'
                ORDER BY cmidata, cmioid
                LIMIT 1";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $comissaoInstalacaoAnterior = pg_fetch_object($result);
        }

        return $comissaoInstalacaoAnterior;
    }

    public function atualizarComissaoInstalacao($comissaoInstalacao) {


        if ( $comissaoInstalacao->ponto == 'chegada' ) {

            $update = "
                         cmideslocamento              = ROUND( $comissaoInstalacao->cmidesloc_km_chegada + cmideslocamento )
                         , cmivalor_pedagio           = $comissaoInstalacao->cmidesloc_pedagio_chegada + cmivalor_pedagio
                         , cmivl_unit_deslocamento    = $comissaoInstalacao->cmivl_unit_deslocamento
                         , cmidesloc_status_chegada   = $comissaoInstalacao->cmidesloc_status_chegada
                         , cmidesloc_chegada          = '$comissaoInstalacao->cmidesloc_chegada'
                         , cmidesloc_pedagio_chegada  = $comissaoInstalacao->cmidesloc_pedagio_chegada
                         , cmidesloc_km_chegada       = ROUND($comissaoInstalacao->cmidesloc_km_chegada)
                       ";

            $where = "";

        } else if ( $comissaoInstalacao->ponto == 'partida' ) {

            $update = "  cmideslocamento            = ROUND($comissaoInstalacao->cmideslocamento)
                         , cmivalor_pedagio         = $comissaoInstalacao->cmivalor_pedagio
                         , cmivl_unit_deslocamento  = $comissaoInstalacao->cmivl_unit_deslocamento
                         , cmidesloc_status         = $comissaoInstalacao->cmidesloc_status
                         , cmidesloc_saida          = '$comissaoInstalacao->cmidesloc_saida' ";

            $where = "AND (cmideslocamento IS NULL OR cmideslocamento = 0)
                    AND (cmivalor_pedagio IS NULL OR cmivalor_pedagio = 0)";

        } else {

            $update = "
                         cmideslocamento              = ROUND($comissaoInstalacao->cmideslocamento)
                         , cmivalor_pedagio           = $comissaoInstalacao->cmivalor_pedagio
                         , cmidesloc_status           = $comissaoInstalacao->cmidesloc_status
                         , cmivl_unit_deslocamento    = $comissaoInstalacao->cmivl_unit_deslocamento
                         ";

            $where = "AND (cmideslocamento IS NULL OR cmideslocamento = 0)
                    AND (cmivalor_pedagio IS NULL OR cmivalor_pedagio = 0)";
        }

        $query = "UPDATE comissao_instalacao SET
                    $update
                WHERE TRUE
                    AND cmiexclusao IS NULL
                    AND cmiord_serv   = $comissaoInstalacao->cmiord_serv
                    AND cmiitloid     = $comissaoInstalacao->cmiitloid
                    AND cmidata::date = '$comissaoInstalacao->data'
                    AND cmioid        = $comissaoInstalacao->cmioid
                    $where ";

        $retorno = new stdClass();

        try {

            $result = $this->executarQuery($query);

            $retorno->status = true;
            $retorno->msg    = "Atualizado com sucesso.";
            $retorno->nr_linhas_afetadas = $this->affected_rows($result);

        } catch (Exception $e) {
            $retorno->status = false;
            $retorno->msg = "ERRO: ".$e->getMessage()." \n QUERY: ".$query;
        }

        return $retorno;
    }

    public function buscaVisitaImprodutiva($visitaImprodutiva) {

        $visitaImprodutivaAnterior = null;

        $query = "SELECT ovioid
                        , ovidesloc_pedagio_chegada
                        , ovidesloc_km_chegada
                        , ovivalor_pedagio
                        , oviquantidade_km
                        , ovivalor_km
                FROM ordem_servico_visita_improdutiva
                WHERE
                    ovifaturamento IS FALSE
                    AND ovicadastro::date = '$visitaImprodutiva->data'
                    AND oviordoid         = $visitaImprodutiva->oviordoid
                    AND oviitloid         = $visitaImprodutiva->oviitloid
                ORDER BY ovicadastro
                LIMIT 1";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $visitaImprodutivaAnterior = pg_fetch_object($result);
        }

        return $visitaImprodutivaAnterior;
    }

    public function atualizarVisitaImprodutiva($visitaImprodutiva) {


        if ( $visitaImprodutiva->ponto == 'chegada' ) {

            $update = "
                     ovidesloc_status_chegada  = $visitaImprodutiva->ovidesloc_status_chegada
                    , ovidesloc_chegada         = '$visitaImprodutiva->ovidesloc_chegada'
                    , ovidesloc_pedagio_chegada  = $visitaImprodutiva->ovidesloc_pedagio_chegada
                    , ovidesloc_km_chegada    = ROUND($visitaImprodutiva->ovidesloc_km_chegada)
                    , ovivalor_km             = (ROUND($visitaImprodutiva->ovidesloc_km_chegada + oviquantidade_km) * $visitaImprodutiva->ovivalorpor_km)
                    , ovivalor_pedagio        = ($visitaImprodutiva->ovidesloc_pedagio_chegada + ovivalor_pedagio)
                    , oviquantidade_km        = ROUND($visitaImprodutiva->ovidesloc_km_chegada + oviquantidade_km)
                    ";

            $where = "";



        } else  if ( $visitaImprodutiva->ponto == 'partida' ) {

            $update = "   ovivalor_pedagio          = $visitaImprodutiva->ovivalor_pedagio
                        , ovivalorpor_km            = $visitaImprodutiva->ovivalorpor_km
                        , oviquantidade_km          = ROUND($visitaImprodutiva->oviquantidade_km)
                        , ovivalor_km               = ROUND($visitaImprodutiva->oviquantidade_km) * $visitaImprodutiva->ovivalorpor_km
                        , ovidesloc_status          = $visitaImprodutiva->ovidesloc_status
                        , ovidesloc_saida           = '$visitaImprodutiva->ovidesloc_saida' ";

            $where = "AND (oviquantidade_km IS NULL OR oviquantidade_km = 0)
                      AND (ovivalor_pedagio IS NULL OR ovivalor_pedagio = 0) ";

        } else {

            $update = "
                      ovivalor_pedagio        = $visitaImprodutiva->ovivalor_pedagio
                    , ovivalorpor_km          = $visitaImprodutiva->ovivalorpor_km
                    , oviquantidade_km        = ROUND($visitaImprodutiva->oviquantidade_km)
                    , ovivalor_km             = ROUND($visitaImprodutiva->oviquantidade_km) * $visitaImprodutiva->ovivalorpor_km
                    , ovidesloc_status        = $visitaImprodutiva->ovidesloc_status
                    ";

            $where = "AND (oviquantidade_km IS NULL OR oviquantidade_km = 0)
                      AND (ovivalor_pedagio IS NULL OR ovivalor_pedagio = 0) ";

        }

        $query = "UPDATE ordem_servico_visita_improdutiva SET
                    $update
                WHERE
                    ovifaturamento IS FALSE
                    AND ovicadastro::date = '$visitaImprodutiva->data'
                    AND oviordoid         = $visitaImprodutiva->oviordoid
                    AND oviitloid         = $visitaImprodutiva->oviitloid
                    AND ovioid            = $visitaImprodutiva->ovioid
                    $where ";


        $retorno = new stdClass();

        try {

            $result = $this->executarQuery($query);

            $retorno->status = true;
            $retorno->msg    = "Atualizado com sucesso.";
            $retorno->nr_linhas_afetadas = $this->affected_rows($result);

        } catch (Exception $e) {
            $retorno->status = false;
            $retorno->msg = "ERRO: ".$e->getMessage()." \n QUERY: ".$query;
        }

        return $retorno;
    }


    public function atualizarComissaoVisitaImprodutiva($visitaImprodutiva) {


        if ( $visitaImprodutiva->ponto == 'chegada' ) {

            $update = "
                         cmideslocamento              = ROUND($visitaImprodutiva->ovidesloc_km_chegada + $visitaImprodutiva->oviquantidade_km)
                         , cmivalor_pedagio           = ($visitaImprodutiva->ovidesloc_pedagio_chegada + $visitaImprodutiva->ovivalor_pedagio)
                         , cmivl_unit_deslocamento    = $visitaImprodutiva->ovivalorpor_km
                         , cmidesloc_status_chegada   = $visitaImprodutiva->ovidesloc_status_chegada
                         , cmidesloc_chegada          = '$visitaImprodutiva->ovidesloc_chegada'
                         , cmidesloc_pedagio_chegada  = $visitaImprodutiva->ovidesloc_pedagio_chegada
                         , cmidesloc_km_chegada       = ROUND($visitaImprodutiva->ovidesloc_km_chegada)
                       ";

            $where = "";

        } else if ( $visitaImprodutiva->ponto == 'partida' ) {

            $update = "  cmideslocamento            = ROUND($visitaImprodutiva->oviquantidade_km)
                         , cmivalor_pedagio         = $visitaImprodutiva->ovivalor_pedagio
                         , cmivl_unit_deslocamento  = $visitaImprodutiva->ovivalorpor_km
                         , cmidesloc_status         = $visitaImprodutiva->ovidesloc_status
                         , cmidesloc_saida          = '$visitaImprodutiva->ovidesloc_saida'
                         ";

            $where = "AND (cmideslocamento IS NULL OR cmideslocamento = 0)
                    AND (cmivalor_pedagio IS NULL OR cmivalor_pedagio = 0)";

        } else {

            $update = "
                         cmideslocamento              = ROUND($visitaImprodutiva->oviquantidade_km)
                         , cmivalor_pedagio           = $visitaImprodutiva->ovivalor_pedagio
                         , cmidesloc_status           = $visitaImprodutiva->ovidesloc_status
                         , cmivl_unit_deslocamento    = $visitaImprodutiva->ovivalorpor_km
                         ";

            $where = "AND (cmideslocamento IS NULL OR cmideslocamento = 0)
                    AND (cmivalor_pedagio IS NULL OR cmivalor_pedagio = 0)";
        }

        $query = "UPDATE comissao_instalacao SET
                    $update
                WHERE TRUE
                    AND cmiexclusao IS NULL
                    AND cmiord_serv   = $visitaImprodutiva->oviordoid
                    AND cmiitloid     = $visitaImprodutiva->oviitloid
                    AND cmidata::date = '$visitaImprodutiva->data'
                    AND cmiovioid     = $visitaImprodutiva->ovioid
                    $where
                    ";

        $retorno = new stdClass();

        try {

            $result = $this->executarQuery($query);

            $retorno->status = true;
            $retorno->msg    = "Atualizado com sucesso.";
            $retorno->nr_linhas_afetadas = $this->affected_rows($result);

        } catch (Exception $e) {
            $retorno->status = false;
            $retorno->msg = "ERRO: ".$e->getMessage()." \n QUERY: ".$query;
        }


        return $retorno;
    }

    public function getDiasCalculoDeslocamento() {

        $diasCalculoDeslocamento = '0';

        $query = "SELECT pcsidescricao
                FROM parametros_configuracoes_sistemas_itens
                WHERE pcsipcsoid = 'MAPLINK'
                AND pcsioid = 'DIAS_CALCULO_DESLOCAMENTO'";

        $result = $this->executarQuery($query);

        if (pg_num_rows($result) > 0){
            $diasCalculoDeslocamento = pg_fetch_object($result);
            $diasCalculoDeslocamento = $diasCalculoDeslocamento->pcsidescricao;
        }

        return $diasCalculoDeslocamento;
    }
}

?>