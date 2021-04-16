<?php

/** 
 *
 * @author Ricardo Rojo Bonfim
 */
class AtivacaoEquipamentoIvecoClienteDAO {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getEquipamento( $id ) {

        $sql = "SELECT
                    connumero,
                    equoid,
                    equno_serie AS numero_serie,
                    equno_ddd::TEXT || equno_fone::TEXT AS telefone,
                    opsoperadora AS operadora,
                    lincid AS iccid
                FROM
                    equipamento
                    INNER JOIN linha ON linnumero = equno_fone AND equno_ddd = (SELECT arano_ddd from area where araoid = linaraoid)
                    INNER JOIN operadora_linha ON linoploid = oploid
                    INNER JOIN operadora_sms ON oplopsoid = opsoid
                    INNER JOIN contrato ON connumero = (SELECT CASE WHEN conequoid IS NOT NULL THEN connumero ELSE hieconnumero END FROM equipamento LEFT JOIN contrato ON conequoid = equoid LEFT JOIN historico_equipamento ON hieequoid = equoid AND hieeqsoid = 10 WHERE equoid = " . (int) $id . " ORDER BY hiedt_historico DESC LIMIT 1)
                    INNER JOIN equipamento_classe ON coneqcoid = eqcoid
                WHERE
                    equdt_exclusao IS NULL
                    AND linexclusao IS NULL
                    AND eqcproprietario IS TRUE
                    AND opsoperadora IN ('Tim', 'Claro', 'Oi', 'Vivo')
                    AND equoid = " . (int) $id;

        $resultado = pg_query( $this->conn, $sql );

        if ( $resultado && pg_num_rows($resultado) > 0 ) {
            return pg_fetch_object($resultado);
        }
        return false;
    }

    public function salvarHistorico(array $parametros) {

        if ( ( (int) $parametros['connumero'] < 1 ) ||
             ( (int) $parametros['usuoid'] < 1 ) ||
             ( $parametros['obs'] == '') ) {
            throw new Exception('Parametros invalidos para funcao salvarHistorico.');
        }

        $sql = "SELECT historico_termo_i (
                    " . (int) $parametros['connumero'] . ",
                    " . (int) $parametros['usuoid'] . ",
                    '" . $parametros['obs'] . "');";

        $resultado = pg_query( $this->conn, $sql );

        if ( $resultado ) {
            return pg_fetch_object($resultado);
        }
        return false;
    }

}