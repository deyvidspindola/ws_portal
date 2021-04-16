<?php

namespace infra;

use infra\ComumDAO;

class ParametroDAO extends ComumDAO {


	public function __construct($dominio){

		parent::__construct();
		$this->dominio = $dominio;

	}

	public function getParametro($parametro){

		$sql = "
		SELECT
			pcsidescricao
		FROM
			parametros_configuracoes_sistemas_itens
		WHERE
			pcsipcsoid = '". $this->dominio ."'
			AND
			pcsioid = '". $parametro ."'";

		$result = $this->queryExec($sql);

		if ($this->getNumRows($result) > 0) {
			return $this->getObject(0, $result)->pcsidescricao;
		} else {
			return null;
		}
	}

}