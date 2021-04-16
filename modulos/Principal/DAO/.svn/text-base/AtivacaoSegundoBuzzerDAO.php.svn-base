<?php

class AtivacaoSegundoBuzzerDAO {

	private $conn;

	public function __construct($conn) {
		$this->conn = $conn;
	}

	public function obrigacaoFinanceiraParametrizada() {

		$sql = "SELECT 
				valvalor 
			FROM 
				dominio as dom
			INNER JOIN 
				registro AS reg
				ON reg.regdomoid = dom.domoid
			INNER JOIN
				valor AS val
				ON val.valregoid = reg.regoid
			WHERE 
				domoid = 30
			AND domativo = 1
			AND regoid = 150
			AND valoid = 350 --id fixo com a obrigacao financeira (BUZZER PRE-EVENTO)";

		$res = pg_query($this->conn, $sql);

	    if($res && pg_num_rows($res) > 0) {
	        return pg_fetch_object($res);
	    }

	    return false;
	}

	/**
	 * Devolve ID do projeto MTC parametrizado
	 * @return [type] [description]
	 */
	public function parametrizacaoEquipamentoProjeto () {
		$sql = "SELECT 
				valvalor 
			FROM 
				dominio as dom
			INNER JOIN 
				registro AS reg
				ON reg.regdomoid = dom.domoid
			INNER JOIN
				valor AS val
				ON val.valregoid = reg.regoid
			WHERE 
				domoid = 30
			AND domativo = 1
			AND regoid = 150
			AND valoid = 350 --id fixo com o(s) id(s) do equipamento_projeto";

		$res = pg_query($this->conn, $sql);

	    if($res && pg_num_rows($res) > 0) {
	        return pg_fetch_object($res);
	    }

	    return false;
	}

	/**
	 * Verifica se a OS possui servico com a obrigacao financeira parametrizada
	 * @param  [integer] $ordoid [id da ordem de serviço]
	 * @param  [integer] $obroid [id da obrigacao financeira parametrizada]
	 * @return [type]         [description]
	 */
	public function verificaLocacaoBuzzer($ordoid, $obroid) {
		$ordoid = (int) $ordoid;
	    $obroid = (int) $obroid;

	    $sql = "SELECT 
					1
				FROM
					ordem_servico AS os
				INNER JOIN contrato_servico AS cs
					ON cs.consconoid = os.ordconnumero
				WHERE 
					os.ordoid = :ordoid
				AND cs.consobroid = :obroid
				AND cs.consinstalacao IS NOT NULL";

	    $sql = str_replace(':ordoid', $ordoid, $sql);
	    $sql = str_replace(':obroid', $obroid, $sql);

	    $res = pg_query($this->conn, $sql);

	    if($res && pg_num_rows($res) > 0) {
			return pg_fetch_object($res);
	    }

	    return false;
	}

	/**
	 * Verifica obrigação financeira de um determinado serviço cadastrado na contrato_servicos
	 * @param  [integer] $consoid [id do serviço (tabela contrato_serviços)]
	 * @param  [integer] $obroid  [id da obrigação financeira]
	 * @return [type]          [description]
	 */
	public function verificaLocacaoBuzzerContrato($consoid,$obroid) {
		$consoid = (int) $consoid;
		$obroid = (int) $obroid;

		$sql = "SELECT 
					consoid
				FROM 
					contrato_servico
				WHERE
					consoid = :consoid
				AND
					consobroid = :obroid";

		$sql = str_replace(':consoid', $consoid, $sql);
	    $sql = str_replace(':obroid', $obroid, $sql);

	    $res = pg_query($this->conn, $sql);

	    if($res && pg_num_rows($res) > 0) {
	    	return pg_fetch_object($res);
	    }

	    return false;
	}

	public function serialEquipamento($ordoid) {

		$ordoid = (int) $ordoid;

		$sql = "SELECT
					eqpto.equesn
				FROM 
					ordem_servico AS os
				INNER JOIN contrato AS con
					ON os.ordconnumero = con.connumero
				INNER JOIN equipamento AS eqpto
					ON con.conequoid = eqpto.equoid
				WHERE
					os.ordoid = :ordoid";

		$sql = str_replace(':ordoid', $ordoid, $sql);

		$res = pg_query($this->conn, $sql);

	    if($res && pg_num_rows($res) > 0) {
			return pg_fetch_object($res);
	    }

	    return false;
	}

	public function versaoEquipamento() {

		$sql = "SELECT 
					eqpv.*
				FROM 
					contrato as con
				--INNER JOIN contrato_servico AS cons
				--	ON con.connumero = cons.consconoid
				INNER JOIN equipamento AS eqpto
					ON con.conequoid = eqpto.equoid
				INNER JOIN equipamento_versao AS eqpv
					ON eqpv.eveoid = eqpto.equeveoid
				WHERE connumero = 1050437878";

		if($res && pg_num_rows($res) > 0) {
			return pg_fetch_object($res);
	    }

	    return false;
	}

	/**
	 * Consulta realizada para verificar o equipamento (No caso, se é MTC700)
	 * @param  [string/integer] $eproid  [ids da tabela equipamento_projeto]
	 * @param  [integer] $consoid [id da tabela contrato_servicos]
	 * @param  [integer] $ordoid  [id da tabela ordem_servico]
	 * @return [type]          [description]
	 */
	public function verificaEquipamentoMTC($eproid,$consoid,$ordoid) {

		$ordoid = (int) $ordoid;
		$consoid = (int) $consoid;

		$sql ="SELECT
					os.ordoid,
					os.ordconnumero,
					epr.eproid,
					epr.eprnome,
					eve.eveversao,
					COALESCE(epregtoid,0) AS epregtoid,
					consoid
				FROM 
					ordem_servico AS os
				INNER JOIN
					equipamento AS eqpto
					ON os.ordequoid = eqpto.equoid
				INNER JOIN 
					equipamento_versao AS eve
					ON (eve.eveoid = eqpto.equeveoid)
				INNER JOIN 
					equipamento_projeto AS epr
					ON (epr.eproid = eve.eveprojeto)
				INNER JOIN contrato AS con
					ON con.connumero = os.ordconnumero
				INNER JOIN contrato_servico AS cons
					ON cons.consconoid = con.connumero
				WHERE
					eproid IN (:eproid)
				AND consoid = :consoid
				AND ordoid = :ordoid";

		$sql = str_replace(':ordoid', $ordoid, $sql);
	    $sql = str_replace(':consoid', $consoid, $sql);
	    $sql = str_replace(':eproid', $eproid, $sql);

	    $res = pg_query($this->conn, $sql);

		if($res && pg_num_rows($res) > 0) {
			return pg_fetch_object($res);
	    }

	    return false;
	}

	/**
	 * Recupera o ID de um Grupo Tecnologia referente a um Equipamento
	 * @param  [integer] $equoid [id do equipamento]
	 * @return [type]         [description]
	 */
	function recuperaGrupoTecnologia($equoid) {
		$equoid = (int) $equoid;

		$sql = "
			SELECT
				COALESCE(epregtoid,0) AS epregtoid
			FROM 
				equipamento
			INNER JOIN 
				equipamento_versao ON (eveoid = equeveoid)
			INNER JOIN 
				equipamento_projeto on (eproid = eveprojeto)
			WHERE
				equoid = :equoid
			LIMIT 1			
			";

		$res = pg_query($this->conn, $sql);
		$sql = str_replace(':equoid', $equoid, $sql);

		if($res && pg_num_rows($res) > 0) {
			return pg_fetch_object($res);
	    }

	    return false;
	}
}