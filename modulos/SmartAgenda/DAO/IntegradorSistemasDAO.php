<?php

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

/**
 * Classe de persistência de banco
 * @author Andre L. Zilz
 */
class IntegradorSistemasDAO extends DAO {


    public function gravaLogComunicacao($api, $envRequest, $envResponse, $slchora_request) {

        $timestampResponse = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);

        $sql = "INSERT INTO
                    smartagenda_log_comunicacao
                    (
                        slcslctoid,
                        slcusuoid_inclusao,
                        slcrequest,
                        slchora_request,
                        slcresponse,
                        slchora_response
                    )
                    VALUES
                    (
                        (SELECT slctoid FROM smartagenda_log_comunicacao_tipo WHERE slctdescricao = '" .$api. "'),
                        ". $this->getUsuarioLogado() .",
                        '".addslashes(utf8_decode($envRequest))."',
                        '".$slchora_request."',
                        '".addslashes($envResponse)."',
                        '".$timestampResponse."'
                    );";

       $this->executarQuery($sql);
    }

    public function getParametroSmartAgenda( $parametro ) {

          $sql = "
              SELECT
                pcsidescricao
              FROM
                parametros_configuracoes_sistemas_itens
              WHERE
                pcsipcsoid = 'SMART_AGENDA'
              AND
                pcsidt_exclusao IS NULL
             AND
                pcsioid = '". $parametro ."'";

        $rs = $this->executarQuery($sql);

        $tupla = pg_fetch_object($rs);
        $retorno = isset($tupla->pcsidescricao) ? $tupla->pcsidescricao : '';

        return $retorno;
    }

}


?>