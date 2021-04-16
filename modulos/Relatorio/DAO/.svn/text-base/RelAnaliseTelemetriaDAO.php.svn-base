<?php

/**
 * Classe RelAnaliseTelemetriaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Leandro Alves Ivanaga <leandro.ivanaga@meta.com.br> <leandro.ivanaga.ext@sascar.com.br>
 *
 */


class RelAnaliseTelemetriaDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	/** Motoristas em Telemetria e Delta */
	private $motoristas;

	const MENSAGEM_PROBLEMA_CONECTAR_BASE = "Houve um problema ao conectar na base de dados.";
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
	const MENSAGEM_CLIENTE_NAO_LOCALIZADO = "Cliente não localizado.";
	const MENSAGEM_CLIENTE_SEM_BASE_TELEMETRIA = "Cliente sem base de telemetria.";

	public function __construct($conn) {
		//Seta a conexao na classe
		$this->conn = $conn;
		$this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		$this->motoristas = array();

		// Dados gerais
		$dadosGerais = $this->buscarDadosGerais($parametros);

		// Dados do cliente
		$dadosCliente = $this->buscarDadosCliente($parametros);

		// Dados base do cliente
		$baseCliente = $this->buscarBaseCliente($dadosCliente);

		// Base do cliente e periodo pesquisado
		$dadosGerais->baseCliente = $baseCliente->basnome;
		$dadosGerais->periodo = $parametros->periodo_data_inicial . ' até ' . $parametros->periodo_data_final;

		// Dados de telemetria
		$resultadoTelemetria = $this->buscarDadosTelemetria($parametros, $baseCliente, $dadosCliente);

		// Dados do delta
		$resultadoDelta = $this->buscarDadosDelta($parametros, $baseCliente, $dadosCliente);

		$this->motoristas = array_unique($this->motoristas);

		// Dados de Motoristas
		$resultadoMotoristas = $this->buscarDadosMotoristas($parametros, $baseCliente, $dadosCliente);

		// Resumo Mensal
		$resumoMensal = $this->buscarResumoMensal($parametros, $baseCliente, $dadosCliente);
		$resumoMensal->consulto_combustivel = $this->buscarResumoConsumoMensal($parametros, $baseCliente, $dadosCliente);

		// Reumo Mensal Eventos
		$resumoMensalEventos = $this->buscarResumoMensalEvento($parametros, $baseCliente, $dadosCliente);

		$retorno = new stdClass();
		$retorno->dadosGerais = $dadosGerais;
		$retorno->resultadoTelemetria = $resultadoTelemetria;
		$retorno->resultadoDelta = $resultadoDelta;
		$retorno->resultadoMotoristas = $resultadoMotoristas;
		$retorno->resumoMensal = $resumoMensal;
		$retorno->resumoMensalEventos = $resumoMensalEventos;

		return $retorno;
	}

	/**
	 * Busca dados gerais
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarDadosGerais(stdClass $parametros) {
		// CONECTAR NA BASE BDCENTRAL
		global $dbstring;
		$this->conectarBase($dbstring);

		// BUSCAR OS DADOS GERAIS
		$sql = "SELECT veioid, veiplaca, eqcdescricao, eveversao, conclioid, clinome, connumero as contrato, equesn, clicloid
				FROM contrato
				LEFT JOIN veiculo ON (conveioid = veioid)
				LEFT JOIN equipamento ON (conequoid=equoid)
				LEFT JOIN equipamento_versao ON (equeveoid = eveoid)
				LEFT JOIN equipamento_projeto ON (eveprojeto = eproid)
				LEFT JOIN clientes ON clioid = conclioid
				LEFT JOIN cliente_classe ON cliclicloid = clicloid
				LEFT JOIN equipamento_classe ON coneqcoid=eqcoid
				WHERE condt_exclusao IS NULL
				AND veidt_exclusao IS NULL
				AND veiplaca ilike '%" . $parametros->placa . "%';
			";

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_CLIENTE_NAO_LOCALIZADO);
		}

		return pg_fetch_object($rs);
	}

	/**
	 * Busca dados do cliente
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarDadosCliente(stdClass $parametros) {
		// CONECTAR NA BASE BDCENTRAL
		global $dbstring_bdcentral;
		$this->conectarBase($dbstring_bdcentral);

		// BUSCAR O CLIENTE
		$sql = "SELECT vscconclioid, vscveioid, csusu_usuario, csclicvisualizacao_web
				FROM veiculo_sincroniza
				JOIN cliente_sincroniza ON csclioid = vscconclioid
				WHERE vscveiplaca ILIKE '%" . $parametros->placa . "%' and vsctelemetria = 't';
				";
		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_CLIENTE_NAO_LOCALIZADO);
		}

		return pg_fetch_object($rs);
	}

	/**
	 * Busca dados da base cliente
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarBaseCliente(stdClass $dadosCliente) {
		// CONECTAR NA BASE TELEMETRIA
		global $dbstring_telemetria;
		$this->conectarBase($dbstring_telemetria);

		// BUSCAR A BASE DO CLIENTE
		$sql = "SELECT basnome, basclilogin, bashost
				FROM base
				JOIN base_cliente ON basclibasoid = basoid
				WHERE basclilogin = '". $dadosCliente->csusu_usuario ."';
				";

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_CLIENTE_SEM_BASE_TELEMETRIA . ' buscarBaseCliente');
		}

		return pg_fetch_object($rs);
	}

	/**
	 * Busca dados de telemetria
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarDadosTelemetria(stdClass $parametros, stdClass $baseCliente, stdClass $dadosCliente) {
		// CONECTAR NA BASE DO CLIENTE
		$dbstring_cliente = "host=". $baseCliente->bashost ." dbname=" . $baseCliente->basnome . " user=suporte password=etropus";
		$this->conectarBase($dbstring_cliente);

		// FORMATAR A DATA
		$datamodDe = explode("/", $parametros->periodo_data_inicial);
		$dataDe = $datamodDe[2] . "-" . $datamodDe[1] . "-" . $datamodDe[0];

		$datamodAte = explode("/", $parametros->periodo_data_final);
		$dataAte = $datamodAte[2] . "-" . $datamodAte[1] . "-" . $datamodAte[0];

		$fromTelemetria = 'dados_telemetria' . $datamodDe[2] . $datamodDe[1];

		$sql = "SELECT dadtdt_pacote, dadthorimetro, dadtodometro, dadtvelocidade, dadtrpm, dadtmotor_funcionando, dadtmotooid, dadtconsumo_combustivel
			FROM ". $fromTelemetria ."
			WHERE dadtveioid = " . $dadosCliente->vscveioid . "
				AND dadtdt_pacote BETWEEN '". $dataDe ." 00:00' AND '". $dataAte ." 23:59'
		";

		// Verificação se a busca começa e termina em meses diferentes e então fazer union, se for necessário
		if($datamodDe[1] != $datamodAte[1]) {
			$fromTelemetria = 'dados_telemetria' . $datamodAte[2] . $datamodAte[1];

			$sql .= "
				UNION
				SELECT dadtdt_pacote, dadthorimetro, dadtodometro, dadtvelocidade, dadtrpm, dadtmotor_funcionando, dadtmotooid, dadtconsumo_combustivel
				FROM ". $fromTelemetria ."
				WHERE dadtveioid = " . $dadosCliente->vscveioid . "
					AND dadtdt_pacote BETWEEN '". $dataDe ." 00:00' AND '". $dataAte ." 23:59'
			";
		}

		$sql .= "ORDER BY dadtdt_pacote DESC;";

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			return Array();
		}
		$resultadoTelemetria = Array();
		while ($row = pg_fetch_object($rs)) {

			$row->dadtdt_pacote = DateTime::createFromFormat('Y-m-d H:i:s-u', $row->dadtdt_pacote)->format('d/m/Y H:i:s');
			$resultadoTelemetria[] = $row;
			if ($row->dadtmotooid != '' && $row->dadtmotooid != '0') {
				$this->motoristas[] = $row->dadtmotooid;
			}
		}
		return $resultadoTelemetria;
	}

	/**
	 * Busca dados do delta
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarDadosDelta(stdClass $parametros, stdClass $baseCliente, stdClass $dadosCliente) {
		// CONECTAR NA BASE DO CLIENTE
		$dbstring_cliente = "host=". $baseCliente->bashost ." dbname=" . $baseCliente->basnome . " user=suporte password=etropus";
		$this->conectarBase($dbstring_cliente);

		// FORMATAR A DATA
		$datamodDe = explode("/", $parametros->periodo_data_inicial);
		$dataDe = $datamodDe[2] . "-" . $datamodDe[1] . "-" . $datamodDe[0];

		$datamodAte = explode("/", $parametros->periodo_data_final);
		$dataAte = $datamodAte[2] . "-" . $datamodAte[1] . "-" . $datamodAte[0];

		$fromDelta = 'delta' . $datamodDe[2] . $datamodDe[1];

		$sql = "SELECT (deldatapacote - (deldt_intervalo|| ' SECOND')::interval ) AS dt_inicio_jornada, deldatapacote AS dt_final_delta, deldt_movimento, deldt_parado, deldt_motor_giro, (delodometro - deldt_odometro) AS odometro_inicial, delodometro AS odometro_final, delmotooid, deldt_odometro, delconsumo_combustivel,

				(CASE WHEN deltipo_verme_tipo = '254' THEN 'Motor' WHEN deltipo_verme_tipo='255' THEN 'Motorista' END) AS tipo

				FROM ". $fromDelta ." 
				WHERE delveioid = " .  $dadosCliente->vscveioid . "
					AND deldatapacote BETWEEN '". $dataDe ." 00:00' AND '". $dataAte ." 23:59'
			";

		// Verificação se a busca começa e termina em meses diferentes e então fazer union, se for necessário
		if($datamodDe[1] != $datamodAte[1]) {
			$fromDelta = 'delta' . $datamodAte[2] . $datamodAte[1];

			$sql .= "UNION
				SELECT (deldatapacote - (deldt_intervalo|| ' SECOND')::interval ) AS dt_inicio_jornada, deldatapacote AS dt_final_delta, deldt_movimento, deldt_parado, deldt_motor_giro, (delodometro - deldt_odometro) AS odometro_inicial, delodometro AS odometro_final, delmotooid, deldt_odometro, delconsumo_combustivel,

				(CASE WHEN deltipo_verme_tipo = '254' THEN 'Motor' WHEN deltipo_verme_tipo='255' THEN 'Motorista' END) AS tipo

				FROM ". $fromDelta ." 
				WHERE delveioid = " .  $dadosCliente->vscveioid . "
					AND deldatapacote BETWEEN '". $dataDe ." 00:00' AND '". $dataAte ." 23:59'
			";
		}

		$sql .= "ORDER BY dt_inicio_jornada DESC;";

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			return Array();
		}
		$resultadoDelta = Array();
		while ($row = pg_fetch_object($rs)) {
			$row->dt_inicio_jornada = DateTime::createFromFormat('Y-m-d H:i:s-u', $row->dt_inicio_jornada)->format('d/m/Y H:i:s');
			$row->dt_final_delta = DateTime::createFromFormat('Y-m-d H:i:s-u', $row->dt_final_delta)->format('d/m/Y H:i:s');

			$resultadoDelta[] = $row;

			if ($row->dadtmotooid != '' && $row->dadtmotooid != '0') {
				$this->motoristas[] = $row->dadtmotooid;
			}

			$row->delconsumo_combustivel = ((int)$row->delconsumo_combustivel) / 1000;
		}

		return $resultadoDelta;
	}

	/**
	 * Busca dados de motoristas
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarDadosMotoristas(stdClass $parametros, stdClass $baseCliente, stdClass $dadosCliente) {
		// CONECTAR NA BASE DO CLIENTE
		$dbstring_cliente = "host=". $baseCliente->bashost ." dbname=" . $baseCliente->basnome . " user=suporte password=etropus";
		$this->conectarBase($dbstring_cliente);

		if (empty($this->motoristas) || count($this->motoristas) == 0) {
			return Array();
		}

		$sql = "SELECT motologin, motonome
				FROM motorista
				WHERE motoclioid = ". $dadosCliente->vscconclioid ." 
				AND motodt_exclusao IS NULL
				AND motologin IN (". implode(',', $this->motoristas) .");";

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			return Array();
		}
		$resultadoMotoristas = Array();
		while ($row = pg_fetch_object($rs)) {
			$resultadoMotoristas[] = $row;
		}

		return $resultadoMotoristas;
	}

	/**
	 * Busca resumo mensal
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarResumoMensal(stdClass $parametros, stdClass $baseCliente, stdClass $dadosCliente) {
		// CONECTAR NA BASE DO CLIENTE
		$dbstring_cliente = "host=". $baseCliente->bashost ." dbname=" . $baseCliente->basnome . " user=suporte password=etropus";
		$this->conectarBase($dbstring_cliente);

		// FORMATAR A DATA
		$datamodDe = explode("/", $parametros->periodo_data_inicial);
		$dataDe = $datamodDe[2] . "-" . $datamodDe[1] . "-" . $datamodDe[0];

		$datamodAte = explode("/", $parametros->periodo_data_final);
		$dataAte = $datamodAte[2] . "-" . $datamodAte[1] . "-" . $datamodAte[0];

		$fromTelemetria = 'dados_telemetria' . $datamodDe[2] . $datamodDe[1];

		$sql = "SELECT 
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . ") AS qtde_pacotes,
		(SELECT (SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0)) AS com_velo,
		(SELECT (SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade BETWEEN 1 AND 120)) AS com_velo0a120,
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 121) AS com_velo121,
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm = 0) AS rpm_zerado,
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm BETWEEN 1 AND 200) AS com_rpm0a200,
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm BETWEEN 201 AND 2400) AS com_rpm201a2400,
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm BETWEEN 2401 AND 4000) AS com_rpm2401a4000,
		(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm > 4001) AS com_rpm4001
		";

		// Verificação se a busca começa e termina em meses diferentes e então fazer union, se for necessário
		if($datamodDe[1] != $datamodAte[1]) {
			$fromTelemetria = 'dados_telemetria' . $datamodAte[2] . $datamodAte[1];

			$sql = "
				SELECT SUM(qtde_pacotes) AS qtde_pacotes, SUM(com_velo) AS com_velo, SUM(com_velo0a120) AS com_velo0a120, SUM(com_velo121) AS com_velo121, SUM(rpm_zerado) AS rpm_zerado, SUM(com_rpm0a200) AS com_rpm0a200, SUM(com_rpm201a2400) AS com_rpm201a2400, SUM(com_rpm2401a4000) AS com_rpm2401a4000, SUM(com_rpm4001) AS com_rpm4001
				FROM
				(
					$sql
					UNION
					(SELECT 
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . ") AS qtde_pacotes,
						(SELECT (SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0)) AS com_velo,
						(SELECT (SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade BETWEEN 1 AND 120)) AS com_velo0a120,
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 121) AS com_velo121,
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm = 0) AS rpm_zerado,
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm BETWEEN 1 AND 200) AS com_rpm0a200,
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm BETWEEN 201 AND 2400) AS com_rpm201a2400,
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm BETWEEN 2401 AND 4000) AS com_rpm2401a4000,
						(SELECT COUNT(1) FROM ". $fromTelemetria ." WHERE dadtveioid = " . $dadosCliente->vscveioid . " AND dadtvelocidade > 0 AND dadtrpm > 4001) AS com_rpm4001
					)
				) AS resumo
			";
		}

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			return Array();
		}

		return pg_fetch_object($rs);
	}

	/**
	 * Busca resumo consumo mensal
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function buscarResumoConsumoMensal(stdClass $parametros, stdClass $baseCliente, stdClass $dadosCliente) {
		// CONECTAR NA BASE DO CLIENTE
		$dbstring_cliente = "host=". $baseCliente->bashost ." dbname=" . $baseCliente->basnome . " user=suporte password=etropus";
		$this->conectarBase($dbstring_cliente);

		// FORMATAR A DATA
		$datamodDe = explode("/", $parametros->periodo_data_inicial);
		$dataDe = $datamodDe[2] . "-" . $datamodDe[1] . "-" . $datamodDe[0];

		$datamodAte = explode("/", $parametros->periodo_data_final);
		$dataAte = $datamodAte[2] . "-" . $datamodAte[1] . "-" . $datamodAte[0];

		$fromDelta = 'delta' . $datamodDe[2] . $datamodDe[1];

		$sql = "SELECT SUM(delconsumo_combustivel) AS delconsumo_combustivel
				FROM ". $fromDelta ." 
				WHERE delveioid = " .  $dadosCliente->vscveioid . "
				AND deltipo_verme_tipo = '254'
				";

		// Verificação se a busca começa e termina em meses diferentes e então fazer union, se for necessário
		if($datamodDe[1] != $datamodAte[1]) {
			$fromDelta = 'delta' . $datamodAte[2] . $datamodAte[1];

			$sql = "
				SELECT SUM(delconsumo_combustivel) AS delconsumo_combustivel
				FROM
				(
					$sql
					UNION
					SELECT SUM(delconsumo_combustivel) AS delconsumo_combustivel
					FROM ". $fromDelta ." 
					WHERE delveioid = " .  $dadosCliente->vscveioid . "
					AND deltipo_verme_tipo = '254'
				) AS resumo
			";
		}

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			return 0;
		} else {
			$result = pg_fetch_object($rs);
			return $result->delconsumo_combustivel / 1000;
		}
	}

	private function buscarResumoMensalEvento(stdClass $parametros, stdClass $baseCliente, stdClass $dadosCliente) {
		// CONECTAR NA BASE DO CLIENTE
		$dbstring_cliente = "host=". $baseCliente->bashost ." dbname=" . $baseCliente->basnome . " user=suporte password=etropus";
		$this->conectarBase($dbstring_cliente);

		/**
			Não exibir os seguintes eventos
			vetoid evetdescr
			219 - PRÉ-INFRAÇÃO DE ROTOGRAMA FALADO SECO
			220 - PRÉ-INFRAÇÃO DE ROTOGRAMA FALADO CHUVA
			221 - PRÉ-INFRAÇÃO DE EXCESSO DE VELOCIDADE SECO
			222 - PRÉ-INFRAÇÃO DE EXCESSO DE VELOCIDADE COM CHUVA
		*/
		$eventosIgnorados = Array(219, 220, 221, 222);

		// FORMATAR A DATA
		$datamodDe = explode("/", $parametros->periodo_data_inicial);
		$dataDe = $datamodDe[2] . "-" . $datamodDe[1] . "-" . $datamodDe[0];

		$datamodAte = explode("/", $parametros->periodo_data_final);
		$dataAte = $datamodAte[2] . "-" . $datamodAte[1] . "-" . $datamodAte[0];

		$fromEventos = 'ocorrencia_evento' . $datamodDe[2] . $datamodDe[1];

		$sql = "SELECT evetdescr, COUNT(1) AS qtd
				FROM ". $fromEventos ."
				LEFT JOIN evento_telemetria ON evetoid = ocoetevetoid
				WHERE ocoeveioid = ". $dadosCliente->vscveioid ."
				AND evetoid NOT IN (". implode(',', $eventosIgnorados) .")
				GROUP BY evetdescr
		";

		// Verificação se a busca começa e termina em meses diferentes e então fazer union, se for necessário
		if($datamodDe[1] != $datamodAte[1]) {
			$fromEventos = 'ocorrencia_evento' . $datamodAte[2] . $datamodAte[1];

			$sql = "
				SELECT evetdescr, SUM(qtd) AS qtd
				FROM
				(
					$sql
					UNION
					(SELECT evetdescr, COUNT(1) AS qtd
					FROM ". $fromEventos ."
					LEFT JOIN evento_telemetria ON evetoid = ocoetevetoid
					WHERE ocoeveioid = ". $dadosCliente->vscveioid ."
					AND evetoid NOT IN (". implode(',', $eventosIgnorados) .")
					GROUP BY evetdescr)
				) AS eventos
				GROUP BY evetdescr
			";
		}

		$sql .= "ORDER BY qtd DESC;";

		$rs = $this->executarQuery($sql);
		if (pg_num_rows($rs) == 0) {
			return Array();
		}

		$resultadoResumoEventos = Array();
		while ($row = pg_fetch_object($rs)) {
			$resultadoResumoEventos[] = $row;
		}

		return $resultadoResumoEventos;
	}

	/**
	 * Submete uma query a execucao do SGBD
	 * @param  [string] $query
	 * @return [bool]
	 */
	private function executarQuery($query) {

		if(!$rs = pg_query($this->conn, $query)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return $rs;
	}

	/**
	 * Conectar a uma base especifica
	 * @param  [string] $dbstring
	 * @return [bool]
	 */
	private function conectarBase($dbstring) {

		if (!$this->conn = pg_connect ($dbstring)) {
			throw new ErrorException(self::MENSAGEM_PROBLEMA_CONECTAR_BASE);
		}
	}
}
?>
