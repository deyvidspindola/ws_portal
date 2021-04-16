<?php
require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';


/**
 * Classe abstrata de persistência dos dados de AtendimentoAutomaticoDAO, somente executa, não possui regras.
 * 
 * @author	Willian Menegali <willian.menegali@meta.com.br>
 * @version 04/04/2013
 * @since   04/04/2013
 * @package Cron
 */
class EmailCarenciaReinstalacaoDAO extends CronDAO {
	
	function buscarEmailsModelos() {
		$sql = "SELECT cli.clioid, cli.clinome, vei.veiplaca, cli.cliemail, con.connumero, ecr.ecrassunto, ecr.ecrmensagem, ecr.ecroid
				FROM contrato AS con
				INNER JOIN reativacao_cobranca_monitoramento rcm ON con.connumero = rcm.rcmconoid
				INNER JOIN veiculo AS vei ON con.conveioid = veioid
				INNER JOIN clientes AS cli ON con.conclioid = clioid
				INNER JOIN email_carencia_reinstalacao AS ecr ON (((rcm.rcmorddt_conclusao::date
																	+ ((SELECT CAST(pcrperiodo as varchar) FROM periodo_carencia_reinstalacao WHERE pcrdt_exclusao IS NULL) || ' days')::interval )::date
																	- (NOW())::date) = ecrfim_carencia AND ecrdt_exclusao IS NULL)
				WHERE con.condt_ini_vigencia IS NOT NULL
				AND con.concsioid = 1
				AND con.conequoid IS NULL
				AND rcm.rcmorddt_conclusao > (	SELECT pcrdt_vigencia 
												FROM periodo_carencia_reinstalacao 
												WHERE pcrdt_exclusao IS NULL)
				AND NOT EXISTS (	SELECT 1
									FROM envio_email_carencia
									WHERE eecconoid = con.connumero
									AND ecrfim_carencia = ((rcm.rcmorddt_conclusao::date
															+ ((SELECT CAST(pcrperiodo as varchar) FROM periodo_carencia_reinstalacao WHERE pcrdt_exclusao IS NULL) || ' days')::interval )::date
															- NOW()::date)
								)";
		
		$res = pg_query($this->conn, $sql);
		return $res;
	}
	
	function salvarEnvioEmailCarencia($id_contrato, $email_id) {
		$sql_salvar = "INSERT INTO envio_email_carencia (eecdt_envio, eecconoid, eececroid) VALUES (NOW(), '$id_contrato', '$email_id')";
		return pg_query($this->conn, $sql_salvar);
	}
		
}

?>