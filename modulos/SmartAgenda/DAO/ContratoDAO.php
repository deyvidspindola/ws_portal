<?php

/**
 * Classe ContratoDAO.
 * Camada de persistencia de dados para entidades de Contrato
 *
 */
 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class ContratoDAO extends DAO{


    public function isContratoSiggo($connumero) {

        $sql = "
            SELECT EXISTS(
                        SELECT
                            1
                        FROM
                            proposta
                        INNER JOIN
                                tipo_proposta ON (prptppoid = tppoid)
                        WHERE
                                tppoid_supertipo = 12
                        AND
                            (
                                prptermo = ".intval($connumero)."
                                OR
                                prpoid = (SELECT pritprpoid FROM proposta_item WHERE pritprptermo = ".intval($connumero).")
                            )
                        ) AS is_siggo
            ";

        $rs = $this->executarQuery($sql);

        $row = pg_fetch_object($rs);

        return ($row->is_siggo == 't') ? TRUE : FALSE;
    }

    public function getContratoOS($ordoid){

        $sql =" SELECT ordconnumero
                  FROM ordem_servico
            INNER JOIN os_tipo ON ordostoid = ostoid
                 WHERE ordoid = $ordoid" ;

        $rs = $this->executarQuery($sql);

        return pg_num_rows($rs) ? pg_fetch_all($rs) : array();

    }

    public function getEquipamentoContrato($ordoid) {
        $sql =  " SELECT ec.eqcoid, ec.eqcdescricao 
                    FROM ordem_servico os
                  INNER JOIN contrato c on os.ordconnumero = c.connumero
                  INNER JOIN equipamento_classe ec on c.coneqcupgoid = ec.eqcoid
                    WHERE ordoid = $ordoid";

        $rs = $this->executarQuery($sql);

        return pg_num_rows($rs) ? pg_fetch_all($rs) : array();

    }

}
