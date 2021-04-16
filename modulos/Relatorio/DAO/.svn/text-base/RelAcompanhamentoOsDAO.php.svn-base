<?php
/**
 * Acesso a dados do relatório de acompanhamento de ordens de serviço
 * @author Gabriel Luiz Pereira
 * @since 08/11/2012
 */
class RelAcompanhamentoOsDAO {

	/**
	 * Link de conexão
	 * @var resource
	 */
	private $conn;


	/**
	 * Construtor
	 * @param resource $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}

	/**
	 * Retorna a lista de status que uma ordem de serviço pode ter
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getStatusList() {

		$return = array();

		$sql = "
		SELECT
			ossoid 			AS id,
			ossdescricao 	AS descricao
		FROM
			ordem_servico_status
		WHERE
			ossexclusao IS NULL
		ORDER BY
			ossdescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de status da ordem de serviço");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}


	/**
	 * Retorna a lista de tipos de solicitação que uma ordem de serviço pode ter
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getTipoSolicitacaoList() {

		$return = array();

		$sql = "
		SELECT
			otsoid 			AS id,
			otsdescricao	AS descricao
		FROM
			os_tipo_solicitacao
		WHERE
			otscexclusao IS NULL
		ORDER BY
			otsdescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de tipos de solicitação");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}


	/**
	 * Retorna a lista de tipos de ordem de serviço
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getOSTipoList() {

		$return = array();

		$sql = "
		SELECT
			ostoid			AS id,
			ostdescricao	AS descricao
		FROM
			os_tipo
		WHERE
			ostdt_exclusao IS NULL
		ORDER BY
			ostdescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de tipos de ordem de serviço");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}


	/**
	 * Retorna a lista de classes de contrato
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getEquipamentoClasseList() {

		$return = array();

		$sql = "
		SELECT
			eqcoid 		AS id,
			eqcdescricao	AS descricao
		FROM
			equipamento_classe
		WHERE
			eqcinativo IS NULL
		ORDER BY
			eqcdescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de tipos de classes de contrato");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}


	/**
	 * Retorna a lista de tipos de contrato
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getTipoContratoList() {

		$return = array();

		$sql = "
		SELECT
			tpcoid 			AS id,
			tpcdescricao 	AS descricao
		FROM
			tipo_contrato
		WHERE
			tpcativo = 't'
		ORDER BY
			tpcdescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de tipos de contrato");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}


	/**
	 * Retorna a lista de versão de equipamento
	 * @param integer $modelo
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getVersaoEquipamentoList($modelo) {

		$return = array();

		$sql = "
			SELECT
				eveoid			AS id,
				eveversao 		AS descricao
			FROM
				equipamento_versao
			WHERE
				evedt_exclusao IS NULL
				AND eveversao IS NOT NULL
				AND evemovoid = $modelo
			ORDER BY
				descricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de versão de equipamento");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[] = array(
					'id'=>$row->id,
					'descricao'=>$row->descricao
				);
			}

			return $return;
		}
		else {
			return null;
		}
	}

	/**
	 * Retorna a lista de versões de um determinado modelo
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getModeloVersaoList() {

		$return = array();

		$sql = "
		SELECT
			movoid 			AS id,
			movdescricao	AS descricao
		FROM
			modelo_versao
		WHERE
			movexclusao IS NULL
		GROUP BY
			movoid
		ORDER BY
			movdescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de versão de equipamento");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}

	/**
	 * Retorna a lista de defeitos alegados
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getDefeitosList() {

		$return = array();
		/*
		 *
		SELECT
			osdfoid			AS id,
			osdfdescricao	AS descricao
		FROM
			ordem_servico_defeito
		WHERE
			osdfexclusao IS NULL
		ORDER BY
			osdfdescricao
		 */
		$sql = "
		SELECT
			otdoid			AS id,
			otddescricao	AS descricao
		FROM
			os_tipo_defeito
		WHERE
			otddt_exclusao IS NULL
		ORDER BY
			otddescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de defeitos alegados");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->descricao;
			}

			return $return;
		}
		else {
			return null;
		}
	}

	/**
	 * Retorna a lista de usuários
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getUsuariosList() {

		$return = array();

		$sql = "
		SELECT
			cd_usuario 	AS id,
			nm_usuario	AS nome
		FROM
			usuarios
		WHERE
			dt_exclusao IS NULL
		ORDER BY
			nm_usuario
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de defeitos alegados");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[$row->id] = $row->nome;
			}

			return $return;
		}
		else {
			return null;
		}
	}

	/**
	 * Retorna a lista de motivos
	 * @param integer $item
	 * @throws Exception
	 * @return multitype:array |NULL
	 */
	public function getMotivosList($item, $tipoOs) {

		if (empty($item)) {
			throw new Exception("Item não informado");
		}

		if (empty($tipoOs)) {
			throw new Exception("Tipo de ordem de serviço não informado");
		}

		$return = array();

		$sql = "
		SELECT
			otioid			AS id,
			otidescricao	AS descricao
		FROM
			os_tipo_item
		WHERE
			otitipo = '$item'
			AND otiostoid = $tipoOs
			AND otidt_exclusao IS NULL
		ORDER BY
			otidescricao
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar a lista de motivos");
		}

		if (pg_num_rows($result) > 0) {

			while($row = pg_fetch_object($result)) {

				$return[] = array(
					'id'=>$row->id,
					'descricao'=>$row->descricao
				);
			}

			return $return;
		}
		else {

			return null;
		}
	}

	/**
	 * Gera a massa de dados Analítica
	 * @param array $filtros
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getAnalitico($filtros) {

		$filtro = "";

		// Validações
		if (!isset($filtros['data_inicio']) || !isset($filtros['data_fim']) || empty($filtros['data_inicio']) || empty($filtros['data_fim'])) {
			throw new Exception("O período não informado");
		}

		$filtro .= " ordem_servico.orddt_ordem::date BETWEEN '".$filtros['data_inicio']."'::date AND '".$filtros['data_fim']."'::date ";

		$temp = explode('/', $filtros['data_inicio']);
		$data_base = strtotime($temp[2].'-'.$temp[1].'-'.$temp[0]);
		$data_base = strtotime('-1 year', $data_base);
		$data_base = date ('d/m/Y', $data_base);

		// Status
		if (isset($filtros['status']) && !empty($filtros['status'])) {
			$filtro .= " AND ordem_servico.ordstatus = ".$filtros['status']." ";
		}

		// Modelo
		if (isset($filtros['modelo']) && !empty($filtros['modelo'])) {
			$filtro .= " AND modelo_versao.movoid = ".$filtros['modelo']." ";
		}

		// Versão
		if (isset($filtros['versao']) && !empty($filtros['versao'])) {
			$filtro .= " AND equipamento_versao.eveoid = ".$filtros['versao']." ";
		}

		// Tipo solicitação
		if (isset($filtros['tipo_solicitacao']) && !empty($filtros['tipo_solicitacao'])) {
			$filtro .= " AND ordem_servico.ordotsoid = ".$filtros['tipo_solicitacao']." ";
		}

		// Tipo contrato
		if (isset($filtros['tipo_contrato'])) {
			$tpContrato = "";
			if (is_array($filtros['tipo_contrato'])) {
				if ($filtros['tipo_contrato'][0] > 0) {
					$tpContrato = implode(',', $filtros['tipo_contrato']);

					if ($tpContrato{0} == ",") {
						$tpContrato = substr($tpContrato,1);
					}

					$tpContrato = str_replace(",,", ",", $tpContrato);

					if(strlen($tpContrato) > 0){
						$filtro .= " AND contrato.conno_tipo IN (". $tpContrato .") ";
					}
				}
			}
		}

		// Defeito alegado
		if (isset($filtros['defeito_alegado']) && !empty($filtros['defeito_alegado'])) {
			$filtro .= " AND defeito_alegado.osdfotdoid = ".$filtros['defeito_alegado']." ";
		}

		// Defeito constatado
		if (isset($filtros['defeito_constatado']) && !empty($filtros['defeito_constatado'])) {
			$filtro .= " AND defeito_constatado.osdfotdoid = ".$filtros['defeito_constatado']." ";
		}

		// Classe do contrato
		if (isset($filtros['classe_contrato']) && !empty($filtros['classe_contrato'])) {
			$filtro .= " AND contrato.coneqcoid = ".$filtros['classe_contrato']." ";
		}

		// Cliente
		if (isset($filtros['cliente']) && !empty($filtros['cliente'])) {
			$filtro .= " AND clientes.clinome ILIKE '".pg_escape_string($filtros['cliente'])."%' ";
		}

		// Placa
		if (isset($filtros['placa']) && !empty($filtros['placa'])) {
			$filtro .= " AND veiculo.veiplaca ILIKE '".pg_escape_string($filtros['placa'])."%' ";
		}

		// Responsável Abertura
		if (isset($filtros['responsavel_abertura']) && !empty($filtros['responsavel_abertura'])) {
			$filtro .= " AND ordem_servico.ordusuoid = ".$filtros['responsavel_abertura']." ";
		}

		// Responsável Autorização
		if (isset($filtros['responsavel_autorizacao']) && !empty($filtros['responsavel_autorizacao'])) {
			$filtro .= " AND (ordem_situacao.orsusuoid = ".$filtros['responsavel_autorizacao']." AND ordem_situacao.orsstatus = 42) ";
		}

		// Responsável Cancelamento
		if (isset($filtros['responsavel_cancelamento']) && !empty($filtros['responsavel_cancelamento'])) {
			$filtro .= " AND (ordem_situacao.orsusuoid = ".$filtros['responsavel_cancelamento']." AND ordem_situacao.orssituacao ILIKE 'Ordem de serviço cancelada. Observação:%') ";
		}

		// Responsável Conclusão
		if (isset($filtros['responsavel_conclusao']) && !empty($filtros['responsavel_conclusao'])) {
			$filtro .= " AND (ordem_situacao.orsusuoid = ".$filtros['responsavel_conclusao']." AND ordem_situacao.orsstatus = 43) ";
		}

		// Item
		if (isset($filtros['item']) && !empty($filtros['item'])) {
			$filtro .= " AND os_tipo_item.otitipo = '".$filtros['item']."' ";
		}

		// Tipo
		if (isset($filtros['tipo']) && !empty($filtros['tipo'])) {
			$filtro .= " AND os_tipo_item.otiostoid = ".$filtros['tipo']." ";
		}
		// Motivo
		if (isset($filtros['motivo']) && !empty($filtros['motivo'])) {
		    $filtro .= " AND otioid = ".$filtros['motivo']." ";
		}

		$sql = "
		SELECT DISTINCT
			TO_CHAR(ordem_servico.orddt_ordem, 'dd/mm/YYYY') AS data,
			ordem_servico.ordoid AS ordem_servico,
			clientes.clinome AS cliente,
			veiculo.veiplaca AS placa,
			(
				SELECT
					COUNT(DISTINCT ordem_recorrencia.ordoid) + 1
				FROM
					ordem_servico_item ordem_item_recorrencia
						INNER JOIN ordem_servico ordem_recorrencia ON ordem_item_recorrencia.ositordoid = ordem_recorrencia.ordoid
						INNER JOIN veiculo veiculo_recorrencia ON ordem_recorrencia.ordveioid = veiculo_recorrencia.veioid
				WHERE
					ordem_item_recorrencia.ositosdfoid_alegado = defeito_alegado.osdfoid
				AND
					ordem_item_recorrencia.ositexclusao IS NULL
				AND
					ordem_recorrencia.ordoid != ordem_servico.ordoid
				AND
					ordem_item_recorrencia.ositstatus != 'X'
				AND
					ordem_recorrencia.orddt_ordem BETWEEN '".$data_base."'::date AND '".$filtros['data_fim']."'::date
				AND
					veiculo_recorrencia.veioid = veiculo.veioid
			) AS recorrencia,
			ossdescricao AS status,
			(
				CASE
					WHEN os_tipo_item.otitipo = 'E' THEN 'EQUIPAMENTO'
					WHEN os_tipo_item.otitipo = 'A' THEN 'ACESSÓRIO'
				END
			) AS item,
			os_tipo.ostdescricao AS tipo,
			os_tipo_item.otidescricao AS motivo,
			tipo_contrato.tpcdescricao AS tipo_contrato,
			equipamento_classe.eqcdescricao AS classe_contrato,
			modelo_versao.movdescricao AS modelo,
			equipamento_versao.eveversao AS versao,
			defeito_alegado.osdfdescricao AS defeito_alegado,
			defeito_constatado.osdfdescricao AS defeito_constatado,
			responsavel_abertura.nm_usuario AS responsavel_abertura,
			(
				SELECT
					responsavel_autorizacao.nm_usuario
				FROM
					ordem_situacao ordem_autorizacao
						INNER JOIN usuarios responsavel_autorizacao ON ordem_autorizacao.orsusuoid = responsavel_autorizacao.cd_usuario
				WHERE
					ordem_autorizacao.orsordoid = ordem_servico.ordoid
				AND
					ordem_autorizacao.orsstatus = 42
				LIMIT 1
			) AS responsavel_autorizacao,
			(
				SELECT
					responsavel_cancelamento.nm_usuario
				FROM
					ordem_situacao ordem_cancelamento
						INNER JOIN usuarios responsavel_cancelamento ON ordem_cancelamento.orsusuoid = responsavel_cancelamento.cd_usuario
				WHERE
					ordem_cancelamento.orsordoid = ordem_servico.ordoid
				AND
					ordem_cancelamento.orssituacao ILIKE 'Ordem de serviço cancelada. Observação:%'
				LIMIT 1
			) AS responsavel_cancelamento,
			(
				SELECT
					responsavel_conclusao.nm_usuario
				FROM
					ordem_situacao ordem_conclusao
						INNER JOIN usuarios responsavel_conclusao ON ordem_conclusao.orsusuoid = responsavel_conclusao.cd_usuario
				WHERE
					ordem_conclusao.orsordoid = ordem_servico.ordoid
				AND
					ordem_conclusao.orsstatus = 43
				LIMIT 1
			) AS responsavel_conclusao
		FROM
			ordem_servico
				LEFT JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
				INNER JOIN ordem_servico_status ON ordem_servico.ordstatus = ordem_servico_status.ossoid
				LEFT JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
				INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
				INNER JOIN contrato ON ordem_servico.ordconnumero = contrato.connumero
				INNER JOIN clientes ON contrato.conclioid = clientes.clioid
				INNER JOIN equipamento_classe ON contrato.coneqcoid = equipamento_classe.eqcoid
				INNER JOIN tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
				INNER JOIN usuarios responsavel_abertura ON ordem_servico.ordusuoid = responsavel_abertura.cd_usuario
				LEFT JOIN veiculo ON ordem_servico.ordveioid = veiculo.veioid
				LEFT JOIN equipamento ON ordem_servico.ordequoid = equipamento.equoid
				LEFT JOIN equipamento_versao ON equipamento.equeveoid = equipamento_versao.eveoid
				LEFT JOIN modelo_versao ON equipamento_versao.evemovoid = modelo_versao.movoid
				LEFT JOIN ordem_servico_defeito defeito_alegado ON ordem_servico_item.ositotioid = defeito_alegado.osdfotioid
					AND ordem_servico_item.ositosdfoid_alegado = defeito_alegado.osdfoid
				LEFT JOIN ordem_servico_defeito defeito_constatado ON ordem_servico_item.ositotioid = defeito_constatado.osdfotioid
					AND ordem_servico_item.ositosdfoid_analisado = defeito_constatado.osdfoid
				LEFT JOIN ordem_situacao ON ordem_servico.ordoid = ordem_situacao.orsordoid
		WHERE
			".$filtro."
			--AND ositstatus != 'X' --MANTIS 2750 - deve mostrar os cancelada
		ORDER BY
			ordem_servico
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao efetuar pesquisa " . $sql);
		}
		if (pg_num_rows($result) > 0) {
			return $result;
		}
		else {
			return null;
		}
	}

	/**
	 * Gera a massa de dados Sintetica
	 * @param array $filtros
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getSintetico($filtros) {

		$filtro = "";

		// Validações
		if (!isset($filtros['data_inicio']) || !isset($filtros['data_fim']) || empty($filtros['data_inicio']) || empty($filtros['data_fim'])) {
			throw new Exception("O período não informado");
		}

		$filtro .= " orddt_ordem::date BETWEEN '".$filtros['data_inicio']."'::date AND '".$filtros['data_fim']."'::date ";

		// Status
		if (isset($filtros['status']) && !empty($filtros['status'])) {
			$filtro .= " AND ordstatus = ".$filtros['status']." ";
		}

		// Modelo
		if (isset($filtros['modelo']) && !empty($filtros['modelo'])) {
			$filtro .= " AND movoid = ".$filtros['modelo']." ";
		}

		// Versão
		if (isset($filtros['versao']) && !empty($filtros['versao'])) {
			$filtro .= " AND eveoid = ".$filtros['versao']." ";
		}

		// Tipo solicitação
		if (isset($filtros['tipo_solicitacao']) && !empty($filtros['tipo_solicitacao'])) {
			$filtro .= " AND ordotsoid = ".$filtros['tipo_solicitacao']." ";
		}

		// Tipo contrato
		if (isset($filtros['tipo_contrato'])) {
			$tpContrato = "";
			if (is_array($filtros['tipo_contrato'])) {
				if ($filtros['tipo_contrato'][0] > 0) {
					$tpContrato = implode(',', $filtros['tipo_contrato']);

					if ($tpContrato{0} == ",") {
						$tpContrato = substr($tpContrato,1);
					}

					$tpContrato = str_replace(",,", ",", $tpContrato);

					if(strlen($tpContrato) > 0){
						$filtro .= " AND contrato.conno_tipo IN (". $tpContrato .") ";
					}
				}
			}
		}

		// Defeito alegado
		if (isset($filtros['defeito_alegado']) && !empty($filtros['defeito_alegado'])) {
			$filtro .= " AND alegado.osdfotdoid = ".$filtros['defeito_alegado']." ";
		}

		// Defeito constatado
		if (isset($filtros['defeito_constatado']) && !empty($filtros['defeito_constatado'])) {
			$filtro .= " AND constatado.osdfotdoid = ".$filtros['defeito_constatado']." ";
		}

		// Classe do contrato
		if (isset($filtros['classe_contrato']) && !empty($filtros['classe_contrato'])) {
			$filtro .= " AND coneqcoid = ".$filtros['classe_contrato']." ";
		}

		// Cliente
		if (isset($filtros['cliente']) && !empty($filtros['cliente'])) {
			$filtro .= " AND clinome ILIKE '".pg_escape_string($filtros['cliente'])."%' ";
		}

		// Placa
		if (isset($filtros['placa']) && !empty($filtros['placa'])) {
			$filtro .= " AND veiplaca = '".pg_escape_string($filtros['placa'])."' ";
		}

		// Responsável Abertura
		if (isset($filtros['responsavel_abertura']) && !empty($filtros['responsavel_abertura'])) {
			$filtro .= " AND ordusuoid = ".$filtros['responsavel_abertura']." ";
		}

		// Responsável Autorização
		if (isset($filtros['responsavel_autorizacao']) && !empty($filtros['responsavel_autorizacao'])) {
			$filtro .= " AND (orsusuoid = ".$filtros['responsavel_autorizacao']." AND orsstatus = 42) ";
		}

		// Responsável Cancelamento
		if (isset($filtros['responsavel_cancelamento']) && !empty($filtros['responsavel_cancelamento'])) {
			$filtro .= " AND (orsusuoid = ".$filtros['responsavel_cancelamento']." AND orsstatus = 80) ";
		}

		// Responsável Conclusão
		if (isset($filtros['responsavel_conclusao']) && !empty($filtros['responsavel_conclusao'])) {
			$filtro .= " AND (orsusuoid = ".$filtros['responsavel_conclusao']." AND orsstatus = 43) ";
		}

		// Item
		if (isset($filtros['item']) && !empty($filtros['item'])) {
			$filtro .= " AND otitipo = '".$filtros['item']."' ";
		}

			// Tipo
		if (isset($filtros['tipo']) && !empty($filtros['tipo'])) {
			$filtro .= " AND otiostoid = ".$filtros['tipo']." ";
		}
		// Motivo
		if (isset($filtros['motivo']) && !empty($filtros['motivo'])) {
		    $filtro .= " AND otioid = ".$filtros['motivo']." ";
		}

		$sql = "
		SELECT
			status,
			COUNT(status) OVER (PARTITION BY status) AS total_status,
			classe_contrato,
			COUNT(classe_contrato) OVER (PARTITION BY classe_contrato) AS total_classe_contrato,
			versao,
			COUNT(versao) OVER (PARTITION BY versao) AS total_versao,
			defeito_alegado,
			COUNT(defeito_alegado) OVER (PARTITION BY defeito_alegado) AS total_defeito_alegado,
			defeito_constatado,
			COUNT(defeito_constatado) OVER (PARTITION BY defeito_constatado) AS total_defeito_constatado,
			tipo,
			COUNT(tipo) OVER (PARTITION BY tipo) AS total_tipo,
			motivo,
			COUNT(motivo) OVER (PARTITION BY motivo) AS total_motivo,
			tipo_solicitacao,
			COUNT(tipo_solicitacao) OVER (PARTITION BY tipo_solicitacao) AS total_tipo_solicitacao
		FROM
		(
			SELECT DISTINCT ON (ordoid)
				to_char(orddt_ordem, 'dd/mm/YYYY') 										AS data,
				ordoid																	AS ordem_servico,
				clinome																	AS cliente,
				veiplaca																AS placa,

				(
					SELECT
						COUNT(*)
					FROM
						ordem_servico_item s
					WHERE
						s.ositordoid = ordoid
						AND ositosdfoid_alegado IS NOT NULL and s.ositexclusao IS NULL
					) 	AS recorrencia,

				ossdescricao															AS status,
				otitipo																	AS item,
				ostdescricao															AS tipo,
				-- Mantis (1750) : Ajuste para desconsiderar itens com defeito ALEGADO
				(
					SELECT
						a.otidescricao
					FROM
						os_tipo_item a
					WHERE
						a.otioid = ositotioid
						AND ositosdfoid_alegado IS NULL and otidt_exclusao IS NULL
				)	AS motivo,
				tpcdescricao															AS tipo_contrato,
				eqcdescricao															AS classe_contrato,
				movdescricao															AS modelo,
				eveversao																AS versao,
				otsdescricao															AS tipo_solicitacao,
				(
					SELECT
						osdfdescricao
					FROM
						ordem_servico_defeito
					WHERE
						osdfoid = ositosdfoid_alegado and osdfexclusao IS NULL
					LIMIT  1
				)	AS defeito_alegado,
				(
					SELECT
						osdfdescricao
					FROM
						ordem_servico_defeito
					WHERE
						osdfoid = ositosdfoid_analisado and osdfexclusao IS NULL
					LIMIT  1
				)	AS defeito_constatado,
				(
					SELECT
						nm_usuario
					FROM
						usuarios
					WHERE
						cd_usuario = ordusuoid
					LIMIT  1
				)	AS responsavel_abertura,
				(
					SELECT
						rc.nm_usuario
					FROM
						usuarios rc,
						ordem_situacao ors
					WHERE
						ors.orsordoid = ordoid
						AND rc.cd_usuario = orsusuoid
						AND ors.orsstatus = 42
					LIMIT 1
				)	AS responsavel_autorizacao,
				(
					SELECT
						rc.nm_usuario
					FROM
						usuarios rc,
						ordem_situacao ors
					WHERE
						ors.orsordoid = ordoid
						AND rc.cd_usuario = orsusuoid
						AND ors.orsstatus = 80
					LIMIT 1
				)	AS responsavel_cancelamento,
				(
					SELECT
						rc.nm_usuario
					FROM
						usuarios rc,
						ordem_situacao ors
					WHERE
						ors.orsordoid = ordoid
						AND rc.cd_usuario = orsusuoid
						AND ors.orsstatus = 43
					LIMIT 1
				)	AS responsavel_conclusao
			FROM
				ordem_servico
				INNER JOIN ordem_servico_item	ON ositordoid 	= ordoid
				LEFT JOIN os_tipo_solicitacao	ON otsoid		= ordotsoid
				INNER JOIN os_tipo_item			ON otioid 		= ositotioid
				INNER JOIN os_tipo				ON ostoid 		= otiostoid
				INNER JOIN contrato				ON connumero 	= ordconnumero
				INNER JOIN clientes				ON clioid 		= conclioid
				INNER JOIN tipo_contrato		ON conno_tipo 	= tpcoid
				INNER JOIN equipamento_classe	ON eqcoid  		= coneqcoid
				INNER JOIN ordem_servico_status ON ossoid 	 	= ordstatus
				LEFT JOIN ordem_servico_defeito alegado		ON alegado.osdfotioid		= ositotioid AND alegado.osdfoid 	= ositosdfoid_alegado
				LEFT JOIN ordem_servico_defeito constatado	ON constatado.osdfotioid	= ositotioid AND constatado.osdfoid	= ositosdfoid_analisado
				LEFT JOIN ordem_situacao		ON orsordoid 	= ordoid
				LEFT JOIN veiculo				ON veioid 	 	= ordveioid
				LEFT JOIN equipamento 			ON equoid 	 	= ordequoid
				LEFT JOIN equipamento_versao 	ON eveoid 	 	= equeveoid
				LEFT JOIN modelo_versao			ON movoid 	 	= evemovoid
			WHERE
				$filtro
			    --AND ositstatus != 'X' --MANTIS 2750 - deve mostrar os cancelada
			    --AND ositexclusao IS NULL --MANTIS 2750 - deve mostrar os cancelada
			    AND veidt_exclusao IS NULL  AND equdt_exclusao IS NULL
			    AND condt_exclusao IS NULL AND clidt_exclusao IS NULL			    
			    AND ostdt_exclusao IS NULL AND otidt_exclusao IS NULL	
			    AND evedt_exclusao IS NULL		   
			) analitico
		";

		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao efetuar pesquisa");
		}

		if (pg_num_rows($result) > 0) {
			return $result;
		}
		else {
			return null;
		}
	}
}