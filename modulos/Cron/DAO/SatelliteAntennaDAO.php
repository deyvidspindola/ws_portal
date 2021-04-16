<?php
define("ACTIVE"                 ,   "A"             );
define("INACTIVE"               ,   "I"             );
define("ACTIVATION_FAILED"      ,   "F"             );
define("ERROR_ON_ACTIVATION"    ,   "E"             );
define("PENDING_DEACTIVATION"   ,   "D"             );
define("READY_ACTIVATION"       ,   "'INSTALADA'"   );
define("READY_TEST"             ,   "'EM TESTE'"    );
define("LIMIT_SEARCH_RESULTS"   ,   "100000"           );

require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';

class SatelliteAntennaDAO extends CronDAO {

    public function getConfig() {

        $query = "
        SELECT
        pcsioid AS key,
        pcsidescricao AS value
        FROM public.parametros_configuracoes_sistemas_itens
        WHERE pcsipcsoid = 'SKYWAVE';
        ";

        return $this->fetchObjects($this->query($query));
    }

    public function getStatus() {
        return $this->fetchObjects($this->query(
            "
            SELECT
            assoid AS id,
            assdescricao as status
            FROM public.antena_satelital_status;
            "
        ));
    }

    public function getAntennaInstalled($types) {
        return $this->fetchObjects($this->query(
            "
            SELECT
            atn.asatno_serie,
            atn.asatstatus_fornecedor,
            atn.asatoid,
            atn.asatassoid,
            atnst.assoid,
            vapp.asvsp,
            vapp.asvgateway
            FROM public.antena_satelital AS atn
            LEFT JOIN antena_satelital_vapp as vapp on asvasatoid = asatno_serie
            INNER JOIN contrato AS con ON (con.connumero = atn.asatconoid)
            INNER JOIN contrato_situacao AS const ON (const.csioid = con.concsioid AND const.csiativa_antena = true)
            INNER JOIN public.antena_satelital_status AS atnst ON (atnst.assoid = atn.asatassoid AND atnst.assdescricao IN (".READY_ACTIVATION."))
            WHERE atn.asatstatus_fornecedor NOT IN ('".ACTIVE."')
            AND atn.asatmooid in (".$types.")
            LIMIT ".LIMIT_SEARCH_RESULTS.";
            "
        ));        
    }

    public function getAntennaRemoved($types) {
        return $this->fetchObjects($this->query(
            "
            SELECT * FROM
            (
            SELECT
            atn.asatno_serie,
            atn.asatstatus_fornecedor,
            atn.asatoid,
            atn.asatassoid,
            atnst.assoid,
            vapp.asvsp,
            vapp.asvgateway
            FROM public.antena_satelital AS atn
            LEFT JOIN antena_satelital_vapp as vapp on asvasatoid = asatno_serie
            INNER JOIN public.antena_satelital_status AS atnst
            ON (atnst.assoid = atn.asatassoid
            AND atnst.assdescricao NOT IN (".READY_ACTIVATION.", ".READY_TEST."))
            WHERE atn.asatstatus_fornecedor NOT IN ('".INACTIVE."')
            AND atn.asatmooid in (".$types.")

            UNION

            SELECT
            atn.asatno_serie,
            atn.asatstatus_fornecedor,
            atn.asatoid,
            atn.asatassoid,
            atnst.assoid,
            vapp.asvsp,
            vapp.asvgateway
            FROM public.antena_satelital AS atn
            LEFT JOIN antena_satelital_vapp as vapp on asvasatoid = asatno_serie
            INNER JOIN contrato AS con ON (con.connumero = atn.asatconoid)
            INNER JOIN contrato_situacao AS const ON (const.csioid = con.concsioid AND const.csiativa_antena = false)
            INNER JOIN public.antena_satelital_status AS atnst
            ON (atnst.assoid = atn.asatassoid
            AND atnst.assdescricao IN (".READY_ACTIVATION."))
            WHERE atn.asatstatus_fornecedor NOT IN ('".INACTIVE."')
            AND atn.asatmooid in (".$types.")
            ) AS TEMP
            LIMIT ".LIMIT_SEARCH_RESULTS."
            "
        ));
    }

    public function setAntennaActivationStatus($serial, $status) {
        pg_affected_rows($this->query(
            "
            UPDATE
            public.antena_satelital 
            set
            asatstatus_fornecedor = '{$status}' 
            where asatno_serie = '{$serial}';
            "
        ));
    } 
}