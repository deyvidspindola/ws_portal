<?php
require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';

/**
 * Classe abstrata de persistencia dos dados
 *
 * @author 	Andre Luiz Zilz <andre.zilz@sascar.com.br>
 * @version 02/10/2014
 * @package Atendimento
 */
class CronVinculoPerfilPortalDAO extends CronDAO {

	const USUARIO_AUTOMATICO = 2750;

	public function inativarVinculos() {

		$idsInativados = array();

		$sql = "
			UPDATE
				atendente_perfil_representante
			SET
				aprdt_exclusao = NOW(),
				aprusuoid_exclusao = " .self::USUARIO_AUTOMATICO ."
			WHERE
        		aprdt_exclusao IS NULL
			AND
        		aprdt_fim_vinculo < NOW()
			RETURNING
				aproid
			";

		$rs = $this->query($sql);

		while($retorno = pg_fetch_object($rs)){
			$idsInativados[] = $retorno;
		}

		return $idsInativados;

	}

}