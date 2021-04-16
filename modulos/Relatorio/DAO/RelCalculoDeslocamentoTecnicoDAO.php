<?php

/**
 * Classe RelCalculoDeslocamentoTecnicoDAO.
 * Camada de modelagem de dados.
 *
 * @package Relatorio
 * @author  Thiago Leal
 *
 */
class RelCalculoDeslocamentoTecnicoDAO{


    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	private $conn;

	public function __construct(){
		global $conn;
        $this->conn = $conn;
	}

    /**
     * Método que busca os atendimentos.
     *
     * @return Array
     * @throws ErrorException
     */
    public function pesquisar($params) {

        $retorno = array();

        $dataInicial = implode('-', array_reverse(explode('/', substr($params->dt_inicio, 0, 10)))).substr($params->dt_inicio, 10);
        $dataFinal = implode('-', array_reverse(explode('/', substr($params->dt_fim, 0, 10)))).substr($params->dt_fim, 10);

        $sql     = "SELECT
                        ordoid,
                        tipo,
                        osaoid,
                        TO_CHAR(data_atendimento, 'DD/MM/YYYY') AS data_atendimento,
                        TO_CHAR(hora_atendimento, 'HH24:MI') AS hora_atendimento,
                        id_tecnico,
                        id_representante,
                        representante,
                        tecnico,
                        abrangencia,
                        valor_km,
                        total_km,
                        total_pedagio,
                        retorno_km,
                        retorno_pedagio,
                        valor_total,
                        ((COALESCE(valor_km, 0) * COALESCE(retorno_km,0)) + COALESCE(retorno_pedagio, 0)) AS valor_total_retorno,
                        cep,
                        uf,
                        cidade,
                        UPPER(bairro) AS bairro,
                        UPPER(logradouro) AS logradouro,
                        status_deslocamento,
                        status_deslocamento_chegada,
                        UPPER(endereco_saida) AS endereco_saida,
                        UPPER(endereco_chegada) AS endereco_chegada,
                        (CASE WHEN orddt_asso_rep IS NULL THEN FALSE ELSE TRUE END) AS os_direcionada
                    FROM
                    (
                         (  SELECT  DISTINCT ON (ordoid) ordoid,
                            'VI' AS tipo,
                            MAX(osa.osaoid) AS osaoid,
                            MIN(ovicadastro)::date AS data_atendimento,
                            MIN(ovicadastro)::time AS hora_atendimento,
                            oviitloid AS id_tecnico,
                            ovirepoid AS id_representante,
                            repnome AS representante,
                            itlnome AS tecnico,
                            CASE
                                WHEN itlkm_abrangencia IS NULL
                                THEN 0
                                ELSE ROUND(itlkm_abrangencia)
                            END AS abrangencia,
                            ovivalorpor_km AS valor_km,
                            ROUND(oviquantidade_km) AS total_km,
                            ovivalor_pedagio AS total_pedagio,
                            ( COALESCE(ovivalor_km, 0) + COALESCE(ovivalor_pedagio, 0) ) AS valor_total,
                            osa.osacep AS cep,
                            osa.estuf AS uf,
                            osa.clcnome AS cidade,
                            osa.cbanome AS bairro,
                            osa.osaendereco AS logradouro,
                            ovidesloc_km_chegada AS retorno_km,
                            ovidesloc_pedagio_chegada AS retorno_pedagio,
                            ovidesloc_status AS status_deslocamento,
                            ovidesloc_status_chegada AS status_deslocamento_chegada,
                            ovidesloc_saida AS endereco_saida,
                            ovidesloc_chegada AS endereco_chegada,
                            orddt_asso_rep
                        FROM ordem_servico
                            JOIN ordem_servico_visita_improdutiva ON oviordoid = ordoid
                            left JOIN (
                                        SELECT osaoid, osaordoid, osadata, osacep, estuf, clcnome, cbanome,osaendereco
                                        FROM ordem_servico_agenda
                                        JOIN estado ON estoid = osaestoid
                                        JOIN correios_localidades ON clcoid = osaclcoid
                                        LEFT JOIN correios_bairros ON cbaoid = osacbaoid
                                        ) osa ON (osa.osaordoid = ordoid AND ovicadastro::DATE = osa.osadata)
                            JOIN representante ON (repoid = ovirepoid)
                            JOIN instalador ON itloid = oviitloid AND itlrepoid = repoid
                        WHERE ovicadastro::DATE BETWEEN '$dataInicial' AND '$dataFinal'
                            AND oviitloid IS NOT NULL
                            ".(!empty($params->repoid) ? " AND repoid = {$params->repoid} " : "" )."
                            ".(!empty($params->itloid) ? " AND oviitloid = {$params->itloid} " : "" )."
                        GROUP BY
                            ordoid,
                            ovicadastro,
                            oviitloid,
                            ovirepoid,
                            repnome,
                            itlnome,
                            itlkm_abrangencia,
                            ovivalorpor_km,
                            oviquantidade_km,
                            ovivalor_pedagio,
                            ovivalor_km,
                            ovidesloc_km_chegada,
                            ovidesloc_pedagio_chegada,
                            osa.osacep,
                            osa.estuf,
                            osa.clcnome,
                            osa.cbanome,
                            osa.osaendereco,
                            ovidesloc_status,
                            ovidesloc_status_chegada,
                            ovidesloc_saida,
                            ovidesloc_chegada

                        ) UNION (

                        SELECT DISTINCT ON (ordoid) ordoid,
                            'CI' AS tipo,
                            MAX(osa.osaoid) AS osaoid,
                            MIN(cmidata)::date AS data_atendimento,
                            MIN(cmidata)::time AS hora_atendimento,
                            cmiitloid AS id_tecnico,
                            repoid AS id_representante,
                            repnome AS representante,
                            itlnome AS tecnico,
                            CASE
                                WHEN itlkm_abrangencia IS NULL
                                THEN 0
                                ELSE ROUND(itlkm_abrangencia)
                            END AS abrangencia,
                            cmivl_unit_deslocamento AS valor_km,
                            ROUND(cmideslocamento) AS total_km,
                            cmivalor_pedagio AS total_pedagio,
                            ((COALESCE(cmivl_unit_deslocamento, 0) * COALESCE(cmideslocamento,0)) + COALESCE(cmivalor_pedagio, 0)) AS valor_total,
                            osa.osacep AS cep,
                            osa.estuf AS uf,
                            osa.clcnome AS cidade,
                            osa.cbanome AS bairro,
                            osa.osaendereco AS logradouro,
                            cmidesloc_km_chegada AS retorno_km,
                            cmidesloc_pedagio_chegada AS retorno_pedagio,
                            cmidesloc_status AS status_deslocamento,
                            cmidesloc_status_chegada AS status_deslocamento_chegada,
                            cmidesloc_saida AS endereco_saida,
                            cmidesloc_chegada AS endereco_chegada,
                            orddt_asso_rep
                        FROM ordem_servico
                            JOIN comissao_instalacao ON (cmiord_serv = ordoid)
                            LEFT JOIN (
                                        SELECT osaoid, osaordoid, osadata, osacep, estuf, clcnome, cbanome,osaendereco
                                        FROM ordem_servico_agenda
                                        JOIN estado ON estoid = osaestoid
                                        JOIN correios_localidades ON clcoid = osaclcoid
                                        LEFT JOIN correios_bairros ON cbaoid = osacbaoid
                                        ) osa ON (osa.osaordoid = ordoid)
                            JOIN relacionamento_representante ON (relroid = ordrelroid)
                            JOIN representante ON (repoid = relrrep_terceirooid)
                            JOIN instalador ON itloid = cmiitloid AND itlrepoid = repoid
                        WHERE cmidata::date BETWEEN '$dataInicial' AND '$dataFinal'
                            ".(!empty($params->repoid) ? " AND repoid = {$params->repoid} " : "" )."
                            ".(!empty($params->itloid) ? " AND cmiitloid = {$params->itloid} " : "" )."
                            AND cmiitloid IS NOT NULL
                            AND cmiexclusao IS NULL
                            AND cmiovioid IS NULL
                            AND ordstatus = 3 -- OS. Concluida

                            -- NAO CONSIDERA AS OSs ENCONTRADAS NAS VISITAS IMPRODUTIVAS
                            AND cmiord_serv NOT IN (
                                                    SELECT DISTINCT ordoid
                                                            FROM ordem_servico
                                                    JOIN ordem_servico_visita_improdutiva ON oviordoid = ordoid
                                                    left JOIN (
                                                                SELECT osaoid, osaordoid, osadata, osacep, estuf, clcnome, cbanome,osaendereco
                                                                FROM ordem_servico_agenda
                                                                JOIN estado ON estoid = osaestoid
                                                                JOIN correios_localidades ON clcoid = osaclcoid
                                                                LEFT JOIN correios_bairros ON cbaoid = osacbaoid
                                                                ) osa ON (osa.osaordoid = ordoid AND ovicadastro::DATE = osa.osadata)
                                                    JOIN representante ON (repoid = ovirepoid)
                                                    JOIN instalador ON itloid = oviitloid AND itlrepoid = repoid
                                                WHERE ovicadastro::DATE BETWEEN '$dataInicial' AND '$dataFinal'
                                                    AND oviitloid IS NOT NULL
                                                    ".(!empty($params->repoid) ? " AND repoid = {$params->repoid} " : "" )."
                                                    ".(!empty($params->itloid) ? " AND oviitloid = {$params->itloid} " : "" )."
                                                    )

                        GROUP BY
                            ordoid,
                            cmiitloid,
                            repoid,
                            repnome,
                            itlnome,
                            itlkm_abrangencia,
                            cmivl_unit_deslocamento,
                            cmideslocamento,
                            cmivalor_pedagio,
                            cmidesloc_km_chegada,
                            cmidesloc_pedagio_chegada,
                            osa.osacep,
                            osa.estuf,
                            osa.clcnome,
                            osa.cbanome,
                            osa.osaendereco,
                            cmidesloc_status,
                            cmidesloc_status_chegada,
                            cmidesloc_saida,
                            cmidesloc_chegada,
                            cmioid
                        ORDER BY ordoid, osaoid DESC, cmioid
                        )
                ) AS foo

                ORDER BY
                    foo.data_atendimento,
                    foo.representante,
                    foo.tecnico,
                    foo.hora_atendimento ";

       // echo"<pre>";var_dump($sql);echo"</pre>";exit();

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[$registro->data_atendimento][$registro->id_representante][$registro->id_tecnico][] = $registro;
        }

        return $retorno;
    }

	/**
     * Método que busca os Representates.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarRepresentates($repoid = null) {
        $retorno = array();
        $sql     = "SELECT
                        repoid, repnome
                    FROM
                        representante
                    WHERE
                        repexclusao IS NULL
                    AND
                        (repinstalacao IS TRUE OR repassistencia IS TRUE)
                    AND
                        repstatus = 'A'
                    ".(!empty($repoid) ? " AND repoid = $repoid " : "")."
                    ORDER BY repnome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Técnicos.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarTecnicos($param, $itloid = null) {
        $retorno = array();
        $sql     = "SELECT
                        itloid, itlnome
                    FROM
                        instalador
                    WHERE
                        itldt_exclusao IS NULL
                    AND
                        itlfuncao  = 'I'
                    AND
                        itlrepoid = {$param->repoid}
                        ".(!empty($itloid) ? " AND itloid = $itloid " : "")."
                    ORDER BY itlnome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }
}
?>
