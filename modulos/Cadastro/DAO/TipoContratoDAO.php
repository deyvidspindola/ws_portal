<?php
class TipoContratoDAO {
	private $conn;
	
	public function TipoContratoDAO($conn) {
		$this->conn = $conn;
	}
	
	public function pesquisarClientes($clinome, $clidoc, $clitipo) {
		
		$clidoc = (preg_replace("/[^0-9]/", "",$clidoc))*1;
		$clinome = preg_replace("/[^\d\w\s]/", "",$clinome);
		
		$retorno = array();
		
		$sql = "
				SELECT
					clioid,
					clinome,
					CASE
						WHEN clitipo='F' THEN clino_cpf
						WHEN clitipo='J' THEN clino_cgc	
					END AS clidoc,
					clitipo			
				FROM
					clientes
				WHERE
					clitipo = '$clitipo'
				";
		if (!empty($clinome)) $sql .= "AND clinome ILIKE '%$clinome%' ";
		if (!empty($clidoc)) $sql .= "AND clino_cgc='$clidoc' OR clino_cpf='$clidoc' ";
		if (empty($clinome) && empty($clidoc)) $sql .= "AND FALSE ";
		
		$sql .= " LIMIT 50";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception("Falha ao pesquisar clientes");
		}
		
		while ($row = pg_fetch_assoc($res)) {
			if ($row['clitipo']=="F") {
				$clidoc = str_pad($row['clidoc'], 11, "0", STR_PAD_LEFT);
			} else {
				$clidoc = str_pad($row['clidoc'], 14, "0", STR_PAD_LEFT);
			}
			
			$retorno[] = array(
					"id"	=>	$row['clioid'],
					"nome"	=>	utf8_decode($row['clinome']),
					"doc"	=>	$clidoc,
					"tipo"	=>	$row['clitipo']
					);
		}
		
		return $retorno;
	}
	
	public function atributosCliente($clioid) {
		$retorno = array();
		
		$sql = "
				SELECT
					clioid,
					clinome,
					CASE
						WHEN clitipo='F' THEN clino_cpf
						WHEN clitipo='J' THEN clino_cgc
					END AS clidoc,
					clitipo
				FROM
					clientes
				WHERE clioid=$clioid ";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception("Falha ao pesquisar cliente");
		}
		
		$row = pg_fetch_assoc($res);
		
		if ($row['clitipo']=="F") {
			$clidoc = str_pad($row['clidoc'], 11, "0", STR_PAD_LEFT);
		} else {
			$clidoc = str_pad($row['clidoc'], 14, "0", STR_PAD_LEFT);
		}
			
		$retorno = array(
				"id"	=>	$row['clioid'],
				"nome"	=>	utf8_decode($row['clinome']),
				"doc"	=>	$clidoc,
				"tipo"	=>	$row['clitipo']
		);
		
		return $retorno;
	}

	public function getEquipamentoProjeto() {
		$retorno = array();
		
		$sql = "SELECT 
					eproid, eprnome
				FROM equipamento_projeto
				ORDER BY eprnome";
		
		$res = pg_query($this->conn,$sql);
		
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['eproid']] = $linha['eprnome'];
		}
		
		return $retorno;
	}

	public function getEquipamentoRestricao($tpcoid) {
		$tpcoid = (int) $tpcoid;
		$retorno = array();
		
		$sql = "SELECT 
					eproid, eprnome
				FROM 
					equipamento_projeto
				INNER JOIN tipo_contrato_instalacao
				ON eproid = tpcieproid
				AND tpcitpcoid = $tpcoid 
				AND tpciusuoid_exclusao IS NULL
				ORDER BY eprnome";
		
		$res = pg_query($this->conn,$sql);
		
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['eproid']] = $linha['eprnome'];
		}
		
		return $retorno;
	}

	public function addRestricaoEquipamentoProjeto($tpcitpcoid, $tpcieproid, $tpciusuoid_cadastro) {
		$sql = sprintf("SELECT * FROM tipo_contrato_instalacao WHERE tpcitpcoid = '%s' AND tpcieproid = '%s' AND tpciusuoid_exclusao IS NULL",
						$tpcitpcoid,
						$tpcieproid);

		$res = pg_query($this->conn,$sql);

		if(pg_num_rows($res) > 0) {
			throw new Exception("O projeto selecionado já foi cadastrado");
		}

		$sql = sprintf("INSERT INTO tipo_contrato_instalacao (tpcitpcoid, tpcieproid, tpciusuoid_cadastro) VALUES ('%s','%s','%s')",
						$tpcitpcoid,
						$tpcieproid,
						$tpciusuoid_cadastro);
		
		$res = pg_query($this->conn,$sql);
		
		if (!$res) {
			throw new Exception("Erro ao processar a operação");
		}
	}

	public function delRestricaoEquipamentoProjeto($tpcitpcoid, $tpcieproids, $tpciusuoid_exclusao) {
		//BEGIN
		pg_query($this->conn, "BEGIN");

		foreach ($tpcieproids as $idx => $tpcieproid) {
			$sql = sprintf("UPDATE tipo_contrato_instalacao 
							SET tpcidt_exclusao = NOW(), tpciusuoid_exclusao = '%s' 
							WHERE tpcitpcoid = '%s'
							AND tpcieproid = '%s'",
							$tpciusuoid_exclusao,
							$tpcitpcoid,
							$tpcieproid);

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				// ROLLBACK
				pg_query($this->conn, "ROLLBACK");

				throw new Exception("Erro ao processar a operação");
			}
		}

		//COMMMIT
		pg_query($this->conn, "COMMIT");		
	}
}