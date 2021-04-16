<?php

/**
 * Classe de persistencia de dados
 *
 * @package  Atendimento
 * @author   Andre L. Zilz <andre.zilz@sascar.com.br>
 *
 */
class AteVinculoPerfilPortalDAO {

    /**
     * propriedades
     */
    private $conn;

    /**
    * Construtor da classe
    * @param $conn | conector do banco de dados
    */
    public function __construct($conn) {

        $this->conn = $conn;
    }

    /**
    * Recupera o historicvo de vinculos entre atendente e representante
    *
    * @param stdClass $params
    * @return array
    */
    public function recuperarVinculos($params) {

        $retorno = array();

        $sql = "
                SELECT
                    COUNT(aproid) OVER() AS total_registros,
                    aproid,
                    TO_CHAR(aprdt_cadastro, 'dd/mm/yyyy HH24:MI') AS data_cadastro,
                     (
                        CASE WHEN  aprdt_exclusao IS NULL THEN
                            'A'
                        ELSE
                            'I'
                        END
                    ) AS status,
                    aprmotivo AS motivo,
                    repnome AS representante,
                    nm_usuario AS atendente,
                    (SELECT (nm_usuario || ' | ' || ds_login) FROM usuarios WHERE cd_usuario = aprusuoid_instalador) AS instalador
                FROM
                    atendente_perfil_representante
                INNER JOIN
                    representante ON (repoid = aprrepoid)
                INNER JOIN
                    usuarios ON (cd_usuario = aprusuoid_atendente)
                WHERE
                    TRUE";

        if($params->registros == "A") {
            $sql .= " AND aprdt_exclusao IS NULL";
        } else if ($params->registros == "I"){
             $sql .= " AND aprdt_exclusao IS NOT NULL";
        }

        if(!empty($params->usuoid_psq)) {
            $sql .= " AND aprusuoid_atendente =" . intval($params->usuoid_psq);;
        }

        if(!empty($params->repnome_psq)) {
            $sql .= " AND aprrepoid =" . intval($params->aprrepoid);;
        }

        if(!empty($params->data_inicial) && !empty($params->data_final)) {
            $sql .= " AND aprdt_cadastro BETWEEN '".$params->data_inicial." 00:00:01' AND '".$params->data_final." 23:59:59'";
        }

        $sql .= " ORDER BY aprdt_cadastro DESC";

        //echo "<pre>";print_r($sql);exit;

        if($rs = pg_query($this->conn, $sql)) {

            while ($tupla = pg_fetch_object($rs)) {
                $retorno[] = $tupla;
            }
        }

        return $retorno;
    }


    /**
    * recupa os dados de representante
    *
    * @param stdClass | dados do formulario
    * @return array
    */
    public function recuperarRepresentante($params) {

        $retorno = array();

        $sql = "
            SELECT
                repoid,
                repnome
            FROM
                representante
            WHERE
                repexclusao IS NULL
            AND
                repnome ILIKE '%". $params->nome ."%'
            ORDER BY
                repnome
            LIMIT
                10
            ";

        if ($rs = pg_query($this->conn, $sql)) {

            $i = 0;
            while ($tupla = pg_fetch_object($rs)) {
                $retorno[$i]['id'] = $tupla->repoid;
                $retorno[$i]['label'] = utf8_encode($tupla->repnome);
                $retorno[$i]['value'] = utf8_encode($tupla->repnome);
                $i++;
            }

        }

        return $retorno;
    }

    /**
    * recupa os dados de Atendentes
    *
    * @param int $usuoid
    * @return array
    */
    public function recuperarAtendente($usuoid = '') {

        $retorno = array();

        if(!empty($usuoid)){
            $where = " AND cd_usuario =" . intval($usuoid);
        }

        $sql = "
            SELECT
                cd_usuario AS id,
                nm_usuario AS nome
            FROM
                usuarios
            INNER JOIN
                departamento ON (usudepoid = depoid)
            INNER JOIN
                funcao_permissao_depto ON (fpddepoid = depoid)
            INNER JOIN
                funcao ON (funcoid = fpdfuncoid)
            WHERE
                dt_exclusao IS NULL
            AND
                funcnome = 'vincular_atendente_instalador'
            ".$where."
            ORDER BY
                nm_usuario
            ";

        if ($rs = pg_query($this->conn, $sql)) {

            while ($tupla = pg_fetch_object($rs)) {
                $retorno[] = $tupla;
            }

        }

        return $retorno;
    }

    /**
    * recupa os dados de instalador
    *
    * @param stdClass | dados do formulario
    * @return array
    */
    public function recuperarInstalador($aprrepoid) {

        $retorno = array();

        $sql = "
            SELECT
                cd_usuario,
                nm_usuario,
                ds_login
            FROM
                usuarios
            WHERE
                usurefoid = ".intval($aprrepoid)."
            AND
                dt_exclusao IS NULL
            AND
                usudepoid = 9 -- depto Instalacao
            ";

        if ($rs = pg_query($this->conn, $sql)) {

            $i = 0;
            while ($tupla = pg_fetch_object($rs)) {
                $retorno[$i]['id'] = $tupla->cd_usuario;
                $retorno[$i]['nome'] = utf8_encode($tupla->nm_usuario);
                $retorno[$i]['login'] = utf8_encode($tupla->ds_login);
                $i++;
            }

        }

        return $retorno;
    }

    /**
    * Realiza a inclusao do vinculo do atendente com o representante / instalador
    *
    * @param stdClass $params
    * @return boolean
    */
    public function incluirPerfil($params){

        $retorno = 0;

        $sql = "
            INSERT INTO
                atendente_perfil_representante
                (
                    aprusuoid_atendente,
                    aprrepoid,
                    aprusuoid_instalador,
                    aprusuoid_cadastro,
                    aprdt_fim_vinculo,
                    aprmotivo
                )
            VALUES
            (
                ".intval($params->usuoid).",
                ".intval($params->aprrepoid).",
                ".intval($params->aprusuoid_instalador).",
                ".intval($params->usuario).",
                (SELECT NOW() + INTERVAL '1 hour'),
                '".$params->aprmotivo."'
            )
            RETURNING aproid
            ";

        //print_r($sql);exit;

         if ($rs = pg_query($this->conn, $sql)) {

            if(pg_affected_rows($rs) > 0) {
                $id = pg_fetch_object($rs);
                $retorno = $id->aproid;
            }
        }

        return $retorno;

    }

    /**
    * Inativa um registro de vícnulo
    *
    * @param stdClass $params
    */
    public function inativarPerfil($params) {

        $sql = "
            UPDATE
                atendente_perfil_representante
            SET
                aprdt_exclusao = NOW(),
                aprusuoid_exclusao = ".intval($params->usuario)."
            WHERE
                aproid = ".intval($params->aproid)."
            ";

        //print_r($sql);exit;

         if ($rs = pg_query($this->conn, $sql)) {

            if(pg_affected_rows($rs) > 0) {
                return true;
            }
        }

        return false;

    }

    /**
    * Verifica se existe um perfil ativo
    *
    * @param stdClass $params
    * @return boolean
    */
    public function validarInclusaoPerfil($params) {

        $retorno = true;

        $sql = "
        SELECT EXISTS (
                SELECT 1 FROM
                    atendente_perfil_representante
                WHERE
                   aprusuoid_atendente = ".intval($params->usuoid)."
                AND
                    aprdt_exclusao IS NULL
                ) AS existe";

        //print_r($sql);exit;

        if ($rs = pg_query($this->conn, $sql)) {
            $tupla =  pg_fetch_object($rs);
            $retorno =  $tupla->existe == 't' ? true : false;
        }

        return $retorno;

    }


    /**
     * Abre a transacao
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transacao
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transacao
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

?>
