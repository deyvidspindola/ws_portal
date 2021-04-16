<?php
/**
 * @file EnviaEmailSmsDAO.php
 */

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class ComunicacaoEmailsSMSDAO extends DAO{

	public function salvarHistoricoSMS($dados){

		try{

			$sql = "INSERT INTO
        			historico_sms_envio (hseconnumero, hseordoid, hseusuoid_cadastro, hsetipo, hsetexto,hseseeoid,hsestatus,hsetelefone)
        			VALUES (
                            ".  $dados['hseconnumero'] .",
                            ".  $dados['hseordoid'] .",
                            ".  $dados['hseusuoid_cadastro'] .",
                            '". $dados['hsetipo'] ."',
                            '". $dados['hsetexto'] ."',
                            ".  $dados['hseseeoid'] .",
                            '". $dados['hsestatus'] ."',
                            '". $dados['hsetelefone'] ."'
                            )";

			$rs = $this->executarQuery($sql);
			$retorno = (!$rs) ? false : true;


		}catch (Exception $e) {
		throw new Exception("Problemas no dados historico sms ". $e->getMessage());
		}

		return $retorno;
	}

	public function recuperaInformacoesServidorEmail($srvoid) {

		$sql = "SELECT * FROM servidor_email WHERE srvoid = " . (int) $srvoid ;

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {
			return pg_fetch_assoc($rs);
		}

		return NULL;
	}

    public function dadosInstalador($idPrestador){

        $sql = "SELECT
                    itlnome,
                    itlno_cpf
                FROM instalador
                WHERE itlrepoid = ".intval($idPrestador)."
                AND itldt_exclusao IS NULL
                AND itlhabilitado IS TRUE
                AND itlfuncao = 'I'";

        $rs = $this->executarQuery($sql);

        return pg_fetch_all($rs);
    }



    public function recuperarDadosPrestador( $idPrestador ) {

        $registros = new stdClass();

        $sql = "SELECT
                    initcap(repnome) as repnome,
                    endvddd,
                   REGEXP_REPLACE(endvfone, '[^0-9]', '', 'gi') AS endvfone
                FROM representante
                INNER JOIN endereco_representante ON (endvrepoid = repoid)
                WHERE repoid = " . $idPrestador;

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0) {
            $registros = pg_fetch_object($rs);
        }
        return $registros;

    }

}