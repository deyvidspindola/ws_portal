<?php
class FinFaturamentoUnificadoDAO{
	public $conn;
	public $data;
	public $doc;
	public $tipo;
	public $cliente;
	public $tipo_contrato;
	public $contrato;
	public $placa;
	public $usuario;
	public $acao;
	public $obroids;
	public $debug;
    public $debug_sql;
    public $descartavel;
	private $clausulasPrev;
	private $clausulasPrevSemData;
	private $filtros;
	private $connSecundaria;
	
	public function FinFaturamentoUnificadoDAO($conn) {
		$this->debug = false;
		$this->conn = $conn;
		

		global $dbstring;

		$this->connSecundaria = pg_connect($dbstring);
	}
	
	function microtime_float() { 
		list($usec, $sec) = explode(" ", microtime()); 
		return ((float)$usec + (float)$sec);  
	}
	
	public function setarFiltros() {
		
		
		$clausulasCli = "";
		$clausulasCon = "";
		
		if($this->descartavel){
			$this->clausulasPrev = "
					EXTRACT (DAY FROM prefdt_referencia) = EXTRACT (DAY FROM '".$this->data."'::timestamp) AND 
					EXTRACT (MONTH FROM prefdt_referencia) = EXTRACT (MONTH FROM '".$this->data."'::timestamp) AND 
					EXTRACT (YEAR FROM prefdt_referencia) = EXTRACT (YEAR FROM '".$this->data."'::timestamp)";
		}else{
			$this->clausulasPrev = "
					EXTRACT (MONTH FROM prefdt_referencia) = EXTRACT (MONTH FROM '".$this->data."'::timestamp) AND 
					EXTRACT (YEAR FROM prefdt_referencia) = EXTRACT (YEAR FROM '".$this->data."'::timestamp)";
		}
		$this->clausulasPrevSemData = "";
		
		if (!empty($this->doc)) {
			$doc = str_replace('.', '', $this->doc);
			$doc = str_replace(',', '', $doc);
			$doc = str_replace('-', '', $doc);
			$doc = str_replace('/', '', $doc);
			$clausulasCli .= " AND (clino_cgc=$doc OR clino_cpf=$doc)";
		}
		
		if (!empty($this->cliente)) 		$clausulasCli .= " AND clinome ILIKE('%$this->cliente%')";
		if (!empty($this->tipo_contrato)) 	$clausulasCon .= " AND conno_tipo=$this->tipo_contrato";
		if (!empty($this->contrato)) 		$clausulasCon .= " AND connumero=$this->contrato";
		if (!empty($this->placa)) 			$clausulasCon .= " AND conveioid IN (SELECT veioid FROM veiculo WHERE veiplaca='$this->placa')";
		if (!empty($clausulasCli)) {
			$tmp = " AND prefclioid IN (SELECT clioid FROM clientes WHERE TRUE $clausulasCli) ";
			$this->clausulasPrev .= $tmp;
			$this->clausulasPrevSemData .= $tmp;
		}
		if (!empty($clausulasCon)) {
			$tmp = " AND prefconnumero IN (SELECT connumero FROM contrato WHERE TRUE $clausulasCon) ";
			$this->clausulasPrev .= $tmp;
			$this->clausulasPrevSemData .= $tmp;
		}
		//echo $this->acao;
		if ($this->acao!="prepararResumo" && $this->acao!="consultarResumo" && $this->acao!="verificarPendencias" && $this->obroids!=0) {
			$tmp = " AND prefobroid IN ($this->obroids) ";
			$this->clausulasPrev .= $tmp;
			$this->clausulasPrevSemData .= $tmp;
		}
		
		//REGRAS DE NEGOCIOS
		$this->filtros = "";
		if (!empty($this->doc)) {
			
			$doc = str_replace('.', '', $this->doc);
			$doc = str_replace(',', '', $doc);
			$doc = str_replace('-', '', $doc);
			$doc = str_replace('/', '', $doc);
			$this->filtros .= " AND (clino_cgc=$doc OR clino_cpf=$doc)";
		}
		
		if (!empty($this->cliente))
			$this->filtros .= " AND clinome ILIKE('%$this->cliente%')";
		if (!empty($this->tipo_contrato))
			$this->filtros .= " AND tpcoid = $this->tipo_contrato";
		if (!empty($this->contrato))
			$this->filtros .= " AND connumero = $this->contrato";
		if (!empty($this->placa))
			$this->filtros .= " AND conveioid IN (SELECT veioid FROM veiculo WHERE veiplaca='$this->placa')";
	}
	
	public function gerarPrevisoes(){
		$start = $this->microtime_float(true);
		
		pg_query($this->conn, "BEGIN");
		
		$sql = "select pg_backend_pid() AS pid";
		$rs = pg_query($this->conn, $sql);
		
		if ($rs && pg_num_rows($rs)) {
			file_put_contents(_SITEDIR_ . 'faturamento/pidProcessoBD.txt', pg_fetch_result($rs, 0, 'pid'));
		}
		
		$this->delPrevisoes();
		
		// Ajustar valores pelo IGPM
		$igpvl_referencia = $this->valorReferencia($this->data);
		
		if ($igpvl_referencia) {
			$igpm = 1 + ($igpvl_referencia / 100);
			$data = $this->data;
			$dia = substr($data,0,2);
			$mes = substr($data,3,2);
			$ano = substr($data,6,4);
			
			# mes "ano passado"
			$anopassado=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $ano-1));
			# mes "6 meses atrás"
			$semestre=date("d/m/Y", mktime(0, 0, 0, $mes-6, $dia, $ano));
			# mes "1 mês atrás"
			$mes=date("d/m/Y", mktime(0, 0, 0, $mes-1, $dia, $ano));
			
			$this->igpmReajuste($igpm, $anopassado, $semestre, $mes);
		}
		
		// Inserir valores na previsão
		$this->pesquisarLocacoesEquipamentos();
		$this->pesquisarLocacoesAcessorios();
		$this->pesquisarProRataMonitoramentoVeiculos();
		$this->pesquisarMonitoramentoVeiculos();
		$this->pesquisarServicos(); //instalacao, reinstalacao, deslocamento, visita improdutiva
		//$this->pesquisarRenovacao(); //valor em contrato_pagamento //valor ou estara parametrizado ou obrigacao_f ou nao cobra
		$this->pesquisarAcionamentoIndevido();
		$this->pesquisarBloqueioSolicitado();
		$this->pesquisarLocalizacaoWeb();
		$this->pesquisarLocalizacaoSolicitada();
		$this->pesquisarProRataVisualizacaoGSMGPS1();
		$this->pesquisarVisualizacaoGSMGPS1();
		$this->pesquisarProRataVisualizacaoGSMGPS2();
		$this->pesquisarVisualizacaoGSMGPS2();
		$this->pesquisarProRataVisualizacaoCarga1();
		$this->pesquisarVisualizacaoCarga1();
		$this->pesquisarProRataVisualizacaoCarga2();
		$this->pesquisarVisualizacaoCarga2();
		$this->pesquisarProRataServicoSoftware();
		$this->pesquisarServicoSoftware();
        //pesquisarTaxas
        $this->pesquisarTaxas();

		$this->delAcionamentoIndevido();
		$this->delZerados();
		
		if ($this->debug) {
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
		
		pg_query($this->conn, "END");
	}
	
	public function delPrevisoes() {
		$sqlDel = "
				DELETE FROM
					previsao_faturamento
				WHERE
					$this->clausulasPrev";
		
		if (!$resDel = pg_query($this->conn,$sqlDel)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao deletar previsão",1);
		}
	}
	
	public function delTodasPrevisoes(){
		$sqlDel = "
				DELETE FROM
					previsao_faturamento ";

		if (!$resDel = pg_query($this->conn,$sqlDel)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao limpar o resumo",1);
		}
	}

	
	public function delZerados() {
		$sqlDel = "
				DELETE FROM
					previsao_faturamento
				WHERE
					prefvalor <= 0.01";
		
		if (!$this->send_query($sqlDel)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao deletar valores zerados",1);
		}
	}
	
	public function delAcionamentoIndevido() {
		$sqlDel = "
				DELETE FROM
					previsao_faturamento
				WHERE
					prefclioid IN (	SELECT contagem.prefclioid  
									FROM (	SELECT 
											COUNT(*) as acionamentos, prefclioid
											FROM previsao_faturamento
											WHERE prefobroid = 5
											GROUP BY prefclioid) AS contagem
									WHERE contagem.acionamentos < (	SELECT vmfqtd_min_acionamento
																	FROM valores_minimos_faturamento
																	WHERE vmfdt_exclusao IS NULL
																	LIMIT 1)
									GROUP BY prefclioid)
					AND prefobroid = 5";
		if (!$this->send_query($sqlDel)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao deletar acionamentos indevidos",1);
		}
	}
	
	
	public function pesquisarProRataLocacoesEquipamentos() {

		$time_start = $this->microtime_float(true);
		$sql = "
			  INSERT INTO previsao_faturamento
			  (
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT
					DISTINCT con.connumero AS contrato,
					
					(SELECT obrprorata 
						FROM obrigacao_financeira obr2
							WHERE obr2.obroid = obr.obroid 
							AND obrprorata > 0
					) as obroid,
				
					-- RN56 - Cliente Pagador
					CASE WHEN tpc.tpccliente_pagador_locacao IS NULL THEN 
						conclioid 
					ELSE
						tpc.tpccliente_pagador_locacao
					END AS cliente,
				
					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN
				
					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 
				
						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 
				
							CASE
							-- Desconto de 100 % se estiver isento de cobrança
							WHEN EXISTS (	SELECT 1
									FROM parametros_faturamento
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
									LIMIT 1) THEN
										1
									-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
									ELSE
										(	COALESCE((	SELECT parfdesconto 
													FROM parametros_faturamento 
													WHERE parfconoid = con.connumero
													AND parfobroid = obr.obroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 1
													AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0) / 100)
									END
									), 2)
				
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN
				
						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 
				
								-- Aplica o desconto
								((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									LIMIT 1)::numeric * 
				
									CASE
									-- Desconto de 100 % se estiver isento de cobrança
									WHEN EXISTS (	SELECT 1
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parftpcoid = tpc.tpcoid
											AND parfobroid = obr.obroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) THEN
										1
									-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
									ELSE
									
									(	COALESCE((	SELECT parfdesconto 
												FROM parametros_faturamento 
												WHERE parfclioid = con.conclioid
												AND parftpcoid = tpc.tpcoid
												AND parfobroid = obr.obroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
												LIMIT 1)::numeric, 0) / 100)
				
									END
									), 2)
				
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN
				
						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								LIMIT 1)::numeric - 
				
						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 
				
						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN
				
						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 
				
						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 
				
						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					ELSE
						ROUND(COALESCE((COALESCE(cpag.cpagvl_servico, 0) / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - condt_ini_vigencia::date), 0) ,2) 
					END AS valor,
				
					'$this->data'::date AS data_referencia,
				
					'L' as tipo_obrigacao
				
				FROM
					contrato con
					INNER JOIN contrato_pagamento cpag ON cpag.cpagconoid = con.connumero
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					LEFT JOIN veiculo vei ON vei.veioid = con.conveioid
					INNER JOIN obrigacao_financeira obr ON obr.obroid = CASE
																		WHEN (
																			   (SELECT count(msuboid) AS COUNT
																				FROM motivo_substituicao
																				JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
																				AND msubeqcoid = obreqcoid
																				WHERE msubeqcoid IS NOT NULL
																				AND msuboid = con.conmsuboid LIMIT 1)) > 0 THEN
																			  (SELECT obroid
																			   FROM motivo_substituicao
																			   JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
																			   AND msubeqcoid = obreqcoid
																			   WHERE msubeqcoid IS NOT NULL
																			   AND msuboid = con.conmsuboid LIMIT 1)
																		ELSE 
																			CASE
																			WHEN (	(SELECT COUNT(msuboid) AS COUNT
																					 FROM motivo_substituicao
																					 WHERE msuboid = con.conmsuboid
																					 AND msubeqcoid IS NULL
																					 AND msubtrans_titularidade IS TRUE LIMIT 1)) > 0 THEN 
																				25
																			ELSE 
																				eqc.eqcobroid
																			END
																		END
				WHERE TRUE
					$this->filtros
					AND (con.condt_ini_vigencia > '01/02/2013'::date OR con.condt_substituicao > '01/02/2013'::date)
					AND con.conmodalidade = 'L' 
					AND con.conequoid > 0 
					AND con.connumero NOT IN (	SELECT
										nfi2.nficonoid
									FROM
										nota_fiscal_item nfi2
										INNER JOIN nota_fiscal nfl2 ON (nfl2.nflno_numero = nfi2.nfino_numero AND nfl2.nflserie = nfi2.nfiserie)
									WHERE 
										nfi2.nficonoid = con.connumero
										AND nfi2.nfiobroid = obr.obroid
										AND CASE
											WHEN ((	SELECT count(msuboid) AS COUNT
													FROM motivo_substituicao
													JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
													AND msubeqcoid = obreqcoid
													WHERE msubeqcoid IS NOT NULL
													AND msuboid = con.conmsuboid LIMIT 1)) > 0 THEN
												TRUE
											ELSE 
												CASE
												WHEN ((	SELECT COUNT(msuboid) AS COUNT
														FROM motivo_substituicao
														WHERE msuboid = con.conmsuboid
														AND msubeqcoid IS NULL
														AND msubtrans_titularidade IS TRUE LIMIT 1)) > 0 THEN 
													TRUE
												ELSE 
													nfi2.nfidt_referencia = '$this->data'
												END
											END
				
										AND nfl2.nfldt_cancelamento IS NULL) 
					AND (	con.condt_exclusao IS NULL 
							OR  con.condt_exclusao IS NOT NULL 
							AND to_char(con.condt_exclusao, 'mm/yyyy'::text) <> to_char(con.condt_ini_vigencia, 'mm/yyyy'::text)) 
					AND con.consem_custo IS FALSE 
					AND con.concsioid = 1 
					AND (con.conmsuboid IS NULL OR con.conmsuboid <> 59)
					AND (	tpc.tpcseguradora IS NOT TRUE 
							OR tpc.tpccorretora IS TRUE AND (	SELECT msubeqcoid
																FROM motivo_substituicao
																WHERE msuboid = con.conmsuboid) > 0) 
					AND (	tpc.tpccorretora IS NOT TRUE 
							OR tpc.tpccorretora IS TRUE AND ( 	SELECT msubeqcoid
																FROM motivo_substituicao
																WHERE msuboid = con.conmsuboid) > 0)
					AND con.coneqcupgoid IS NULL
					AND NOT EXISTS(	SELECT 
								1
							FROM 
								nota_fiscal_item nfi3, nota_fiscal nfl3
							WHERE 
								(nfl3.nflserie = ANY (ARRAY['F'::bpchar, 'SL'::bpchar])) 
								AND nfl3.nflno_numero = nfi3.nfino_numero 
								AND nfl3.nflserie = nfi3.nfiserie
								AND nfi3.nficonoid = con.connumero
								AND nfi3.nfiobroid = obr.obroid
								AND nfl3.nfldt_cancelamento IS NULL)
				
					AND EXISTS (	SELECT 
								1 
							FROM obrigacao_financeira obr2
							WHERE obr2.obroid = obr.obroid 
							AND obrprorata > 0
					)
					
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)
		";
		
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pro-Rata Locacoes Equipamentos",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarProRataLocacoesEquipamentos: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}
	
	
	
	
	

	
	
	public function pesquisarLocacoesEquipamentos() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
				INSERT INTO previsao_faturamento
				(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT
					DISTINCT con.connumero AS contrato,
					
					CASE
					WHEN (
						   (SELECT count(msuboid) AS COUNT
							FROM motivo_substituicao
							JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
							AND msubeqcoid = obreqcoid
							WHERE msubeqcoid IS NOT NULL
							AND msuboid = con.conmsuboid LIMIT 1)) > 0 THEN
						  (SELECT obroid
						   FROM motivo_substituicao
						   JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
						   AND msubeqcoid = obreqcoid
						   WHERE msubeqcoid IS NOT NULL
						   AND msuboid = con.conmsuboid LIMIT 1)
					ELSE 
						CASE
						WHEN (	(SELECT COUNT(msuboid) AS COUNT
								 FROM motivo_substituicao
								 WHERE msuboid = con.conmsuboid
								 AND msubeqcoid IS NULL
								 AND msubtrans_titularidade IS TRUE LIMIT 1)) > 0 THEN 
							25
						ELSE 
							eqc.eqcobroid
						END
					END AS obroid,

					-- RN56 - Cliente Pagador
					CASE WHEN tpc.tpccliente_pagador_locacao IS NULL THEN 
						conclioid 
					ELSE
						tpc.tpccliente_pagador_locacao
					END AS cliente,

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

							CASE
							-- Desconto de 100 % se estiver isento de cobrança
							WHEN EXISTS (	SELECT 1
									FROM parametros_faturamento
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
									LIMIT 1) THEN
										1
									-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
									ELSE
										(	COALESCE((	SELECT parfdesconto 
													FROM parametros_faturamento 
													WHERE parfconoid = con.connumero
													AND parfobroid = obr.obroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 1
													AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0) / 100)
									END
									), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

								-- Aplica o desconto
								((	SELECT parfvl_cobrado 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									LIMIT 1)::numeric * 

									CASE
									-- Desconto de 100 % se estiver isento de cobrança
									WHEN EXISTS (	SELECT 1
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parftpcoid = tpc.tpcoid
											AND parfobroid = obr.obroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) THEN
										1
									-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
									ELSE
									
									(	COALESCE((	SELECT parfdesconto 
												FROM parametros_faturamento 
												WHERE parfclioid = con.conclioid
												AND parftpcoid = tpc.tpcoid
												AND parfobroid = obr.obroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
												LIMIT 1)::numeric, 0) / 100)

									END
									), 2)

					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(	CASE 
							WHEN COALESCE(cpag.cpagvl_servico, 0::numeric) > 0::numeric THEN 
								-- Valor com desconto (Se completou vigência)
								CASE 
								WHEN ('$this->data'::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
									COALESCE(cpag.cpagvl_servico, 0) - (COALESCE(cpag.cpagvl_servico,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
								ELSE
									COALESCE(cpag.cpagvl_servico, 0)
								END
							ELSE 
								-- Valor com desconto (Se completou vigência)
								CASE 
								WHEN ('$this->data'::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
									COALESCE(cpag.cpaghabilitacao, 0) - (COALESCE(cpag.cpaghabilitacao,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
								ELSE
									COALESCE(cpag.cpaghabilitacao, 0)
								END
							END, 2)
					END AS valor,

					'$this->data'::date AS data_referencia,

					'L' as tipo_obrigacao

				FROM
					contrato con
					INNER JOIN contrato_pagamento cpag ON cpag.cpagconoid = con.connumero
					INNER JOIN cond_pgto_venda cpv ON cpagcpvoid = cpvoid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					LEFT JOIN veiculo vei ON vei.veioid = con.conveioid
					INNER JOIN obrigacao_financeira obr ON obr.obroid = CASE
																		WHEN (
																			   (SELECT count(msuboid) AS COUNT
																				FROM motivo_substituicao
																				JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
																				AND msubeqcoid = obreqcoid
																				WHERE msubeqcoid IS NOT NULL
																				AND msuboid = con.conmsuboid LIMIT 1)) > 0 THEN
																			  (SELECT obroid
																			   FROM motivo_substituicao
																			   JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
																			   AND msubeqcoid = obreqcoid
																			   WHERE msubeqcoid IS NOT NULL
																			   AND msuboid = con.conmsuboid LIMIT 1)
																		ELSE 
																			CASE
																			WHEN (	(SELECT COUNT(msuboid) AS COUNT
																					 FROM motivo_substituicao
																					 WHERE msuboid = con.conmsuboid
																					 AND msubeqcoid IS NULL
																					 AND msubtrans_titularidade IS TRUE LIMIT 1)) > 0 THEN 
																				25
																			ELSE 
																				eqc.eqcobroid
																			END
																		END
				WHERE TRUE
					$this->filtros
                    AND conno_tipo != 844
					AND (con.condt_ini_vigencia > '01/02/2013'::date OR con.condt_substituicao > '01/02/2013'::date)
					AND con.conmodalidade = 'L' 
					AND con.conequoid > 0 
					AND con.connumero NOT IN (	SELECT
										nfi2.nficonoid
									FROM
										nota_fiscal_item nfi2
										INNER JOIN nota_fiscal nfl2 ON (nfl2.nflno_numero = nfi2.nfino_numero AND nfl2.nflserie = nfi2.nfiserie)
									WHERE 
										nfi2.nficonoid = con.connumero
										AND nfi2.nfiobroid = obr.obroid
										AND nfi2.nfidt_referencia = '$this->data'
										AND nfl2.nfldt_cancelamento IS NULL) 
					AND (	con.condt_exclusao IS NULL 
							OR  con.condt_exclusao IS NOT NULL 
							AND to_char(con.condt_exclusao, 'mm/yyyy'::text) <> to_char(con.condt_ini_vigencia, 'mm/yyyy'::text)) 
					AND con.consem_custo IS FALSE 
					AND con.concsioid = 1 
					AND (con.conmsuboid IS NULL OR con.conmsuboid <> 59)
					AND (	tpc.tpcseguradora IS NOT TRUE 
							OR tpc.tpccorretora IS TRUE AND (	SELECT msubeqcoid
																FROM motivo_substituicao
																WHERE msuboid = con.conmsuboid) > 0) 
					AND (	tpc.tpccorretora IS NOT TRUE 
							OR tpc.tpccorretora IS TRUE AND ( 	SELECT msubeqcoid
																FROM motivo_substituicao
																WHERE msuboid = con.conmsuboid) > 0)
					AND con.coneqcupgoid IS NULL
					AND NOT EXISTS(	SELECT 
								1
							FROM 
								nota_fiscal_item nfi3, nota_fiscal nfl3
							WHERE 
								(nfl3.nflserie = ANY (ARRAY['F'::bpchar, 'SL'::bpchar])) 
								AND nfl3.nflno_numero = nfi3.nfino_numero 
								AND nfl3.nflserie = nfi3.nfiserie
								AND nfi3.nficonoid = con.connumero
								AND nfi3.nfiobroid = obr.obroid
								AND nfl3.nfldt_cancelamento IS NULL)
					AND cpvparcela > (	SELECT 
											count(nfloid)
										FROM 
											nota_fiscal_item nfi4, nota_fiscal nfl4
										WHERE 
											nfl4.nflserie = 'A' 
											AND nfl4.nflno_numero = nfi4.nfino_numero 
											AND nfl4.nflserie = nfi4.nfiserie
											AND nfi4.nficonoid = con.connumero
											AND nfi4.nfiobroid = obr.obroid
											AND nfl4.nfldt_cancelamento IS NULL)
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND (('$this->data'::date - con.condt_ini_vigencia::date) > 0 OR ('$this->data'::date - con.condt_substituicao::date) > 0)";
		
        if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Locacoes Equipamentos",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarLocacoesEquipamentos: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}

	public function pesquisarLocacoesAcessorios() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
				INSERT INTO previsao_faturamento
				(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT
					con.connumero AS contrato,

					obr.obroid AS obrigacao_financeira,

					CASE WHEN tpc.tpccliente_pagador_locacao IS NULL THEN 
						con.conclioid 
					ELSE
						tpc.tpccliente_pagador_locacao
					END AS cliente,

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = cons.consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cons.consobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = cons.consobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = cons.consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = cons.consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cons.consobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cons.consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = cons.consobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = cons.consobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						-- Valor com desconto (Se completou vigência)
						ROUND(COALESCE(cons.consvalor, 0), 2) 
					END AS valor, 

					'$this->data'::date AS data_referencia,

					'L' as tipo_obrigacao
				FROM
					contrato con
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN veiculo vei ON vei.veioid = con.conveioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN contrato_servico cons ON cons.consconoid = con.connumero
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cons.consobroid
					INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
				WHERE
					TRUE
					$this->filtros
                    AND conno_tipo != 844
					AND cons.consinstalacao > '01/02/2013'::date
					AND cons.conssituacao = 'L' 
					AND tpc.tpcseguradora IS FALSE 
					AND NOT EXISTS (SELECT nfi.nficonoid
									FROM nota_fiscal_item nfi
									INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
									WHERE (nfi.nficonoid = con.connumero OR nfi.nficonoid = con.connumero_antigo)
									AND nfi.nfiobroid = obr.obroid
									AND nfi.nfidt_referencia = '$this->data'
									AND nfl.nfldt_cancelamento IS NULL) 
					AND con.consem_custo IS FALSE 
					AND con.concsioid = 1 
					AND con.coneqcupgoid IS NULL 
					AND cons.consiexclusao IS NULL 
					AND cons.consinstalacao IS NOT NULL 
					AND cons.consinstalacao IS NOT NULL AND cons.conssituacao = 'L' 
					AND obr.obroid <> 90
					AND NOT EXISTS (SELECT 
										1
									FROM 
										nota_fiscal_item nfi3, nota_fiscal nfl3
									WHERE 
										(nfl3.nflserie = ANY (ARRAY['F'::bpchar, 'SL'::bpchar])) 
										AND nfl3.nflno_numero = nfi3.nfino_numero 
										AND nfl3.nflserie = nfi3.nfiserie
										AND (nfi3.nficonoid = cons.consconoid OR nfi3.nficonoid = con.connumero_antigo)
										AND nfi3.nfiobroid = cons.consobroid
										AND nfl3.nfldt_cancelamento IS NULL)


					AND (CASE WHEN ( 	SELECT cond_pgto_venda.cpvparcela
								FROM cond_pgto_venda
								WHERE cond_pgto_venda.cpvoid = (SELECT ( 	SELECT termo_aditivo.tadcpvoid
																			FROM termo_aditivo
																			WHERE termo_aditivo.tadoid = termo_aditivo_item.taitadoid 
																			AND termo_aditivo.tadexclusao IS NULL)
																FROM termo_aditivo_item
																WHERE termo_aditivo_item.taiconnumero = con.connumero 
																AND cons.consconoid = termo_aditivo_item.taiconnumero 
																AND termo_aditivo_item.taitadoid = cons.constadoid 
																AND termo_aditivo_item.taiobroid = cons.consobroid 
																AND termo_aditivo_item.taisituacao = cons.conssituacao 
																AND termo_aditivo_item.taidt_exclusao IS NULL
																LIMIT 1)) > 0 THEN ( 	SELECT cond_pgto_venda.cpvparcela
																						FROM cond_pgto_venda
																						WHERE cond_pgto_venda.cpvoid = (SELECT ( 	SELECT termo_aditivo.tadcpvoid
																																	FROM termo_aditivo
																																	WHERE termo_aditivo.tadoid = termo_aditivo_item.taitadoid 
																																	AND termo_aditivo.tadexclusao IS NULL
																																	LIMIT 1)
																										FROM termo_aditivo_item
																										WHERE termo_aditivo_item.taiconnumero = con.connumero 
																										AND cons.consconoid = termo_aditivo_item.taiconnumero 
																										AND termo_aditivo_item.taitadoid = cons.constadoid 
																										AND termo_aditivo_item.taiobroid = cons.consobroid 
																										AND termo_aditivo_item.taisituacao = cons.conssituacao 
																										AND termo_aditivo_item.taisituacao = cons.conssituacao 
																										AND termo_aditivo_item.taidt_exclusao IS NULL
																										LIMIT 1))
					ELSE ( 	SELECT 
							CASE WHEN contrato_pagamento.cpagnum_parcela > 0 THEN 
								contrato_pagamento.cpagnum_parcela
							ELSE ( 	SELECT cond_pgto_venda.cpvparcela
								FROM cond_pgto_venda
								WHERE cond_pgto_venda.cpvoid = contrato_pagamento.cpagcpvoid)
							END AS case
						FROM contrato_pagamento
						WHERE contrato_pagamento.cpagconoid = cons.consconoid
						LIMIT 1)
					END) > (SELECT count(nfloid)
							FROM nota_fiscal_item nfi
							INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
							WHERE (nfi.nficonoid = con.connumero OR nfi.nficonoid = con.connumero_antigo)
							AND nfi.nfiobroid = obr.obroid
							AND nfl.nflserie = 'A'
							AND nfl.nfldt_cancelamento IS NULL) 
					
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0";

		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Locacoes Acessorios",1);
		}
		
		if ($this->debug) { 
			echo "\r\n --pesquisarLocacoesAcessórios: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
        }
	}
	
	public function pesquisarProRataMonitoramentoVeiculos() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
				INSERT INTO previsao_faturamento
				(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT
					DISTINCT connumero AS contrato,

					9 AS obrigacao_financeira,

					CASE WHEN tpccliente_pagador_monitoramento  IS NULL THEN
						conclioid
					ELSE
						tpccliente_pagador_monitoramento
					END AS cliente,

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE
					WHEN EXISTS(	SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfconoid = con.connumero
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								LIMIT 1)::numeric -

								-- Aplica o desconto
								((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfconoid = con.connumero
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
										FROM parametros_faturamento
										WHERE parfconoid = con.connumero
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
										LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto
										FROM parametros_faturamento
										WHERE parfconoid = con.connumero
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric -

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE

						(	COALESCE((	SELECT parfdesconto
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric -

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE

						(	COALESCE((	SELECT parfdesconto
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric -

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto
										FROM parametros_faturamento
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric -

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto
										FROM parametros_faturamento
										WHERE parfclioid = con.conclioid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric -

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE

						(	COALESCE((	SELECT parfdesconto
									FROM parametros_faturamento
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric -

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric *

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE

						(	COALESCE((	SELECT parfdesconto
									FROM parametros_faturamento
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(COALESCE((COALESCE(cofvl_obrigacao, 0) / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - condt_ini_vigencia::date), 0) ,2)
					END AS valor,

					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM
					contrato con
					INNER JOIN contrato_obrigacao_financeira cof ON cof.cofconoid = con.connumero
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cof.cofobroid
				WHERE
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL)
					AND NOT EXISTS (	SELECT 1
        								FROM nota_fiscal_item
        								INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
        								WHERE nficonoid = con.connumero
        								AND (   CASE WHEN obr.obroid = 1 THEN
        								            (nfiobroid = 1 OR nfiobroid = 9)
        								        ELSE
        								            nfiobroid = (   SELECT obrprorata
                                        					        FROM obrigacao_financeira obr3
                                        					        WHERE obr3.obroid = obr.obroid AND obrprorata > 0
                                        					     )
												end)
        								AND nfidt_referencia = '$this->data'
        								AND nfldt_cancelamento IS NULL)
					AND tpc.tpcgera_faturamento IS TRUE
					AND conequoid > 0
					AND condt_exclusao IS NULL
					AND cofdt_termino IS NULL
					AND condt_ini_vigencia::date < '$this->data'::date
					AND condt_ini_vigencia is not null
					AND cofobroid = 1
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";


		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Monitoramento Veiculos - pesquisarProRataMonitoramentoVeiculos",1);
		}

		if ($this->debug) {
			echo "\r\n --pesquisarMonitoramentoVeículos: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}

    public function pesquisarMonitoramentoVeiculos() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
				INSERT INTO previsao_faturamento
				(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT
					DISTINCT connumero AS contrato,

					obr.obroid AS obrigacao_financeira,

					CASE WHEN tpccliente_pagador_monitoramento  IS NULL THEN 
						conclioid 
					ELSE 
						tpccliente_pagador_monitoramento 
					END AS cliente,

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
								FROM parametros_faturamento 
								WHERE parfconoid = con.connumero
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								LIMIT 1)::numeric - 

								-- Aplica o desconto
								((	SELECT parfvl_cobrado 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
										FROM parametros_faturamento
										WHERE parfconoid = con.connumero
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
										LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = cof.cofobroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(cofvl_obrigacao ,2) 
					END AS valor,

					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM
					contrato con
					INNER JOIN contrato_obrigacao_financeira cof ON cof.cofconoid = con.connumero
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cof.cofobroid
				WHERE
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL) 
					AND NOT EXISTS (SELECT 1
									FROM nota_fiscal_item 
									WHERE nficonoid = con.connumero
									AND nfiobroid = obr.obroid and nfidt_referencia is not null
									AND nfidt_referencia = '$this->data' and exists (	SELECT 1
																						FROM nota_fiscal
																						where (nflno_numero = nfino_numero AND nflserie = nfiserie)
																						AND nfldt_cancelamento IS NULL))
					AND tpc.tpcgera_faturamento IS TRUE 
					AND (	conequoid > 0 
							OR EXISTS (	SELECT 1
                                        FROM reativacao_cobranca_monitoramento
                                        WHERE rcmconoid = con.connumero                                
                                        AND  rcmorddt_conclusao >= (SELECT pcrdt_vigencia
                                                                    FROM periodo_carencia_reinstalacao 
                                                                    WHERE pcrdt_exclusao IS NULL)
										
										AND (rcmorddt_conclusao + ((	SELECT cast(pcrperiodo as varchar)
                                                                        FROM periodo_carencia_reinstalacao 
                                                                        WHERE pcrdt_exclusao IS NULL) || ' days')::interval
											)::date <= '$this->data'::date
										)
						)
					AND condt_exclusao IS NULL 
					AND ((cofobroid = 1) OR (obrofgoid = 21))
					AND cofdt_termino IS NULL 
					AND condt_ini_vigencia::date < '$this->data'::date
					AND condt_ini_vigencia is not null
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0";
		
		if (!$res = pg_query($this->conn,$sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Monitoramento Veiculos - pesquisarMonitoramentoVeiculos : " . $sql,1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarMonitoramentoVeículos: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}
	

	public function pesquisarTaxas() {

		$time_start = $this->microtime_float(true);


		$whereBuscaFatUnificado = str_replace('prefconnumero', 'futconnumero', $this->clausulasPrevSemData);
		$whereBuscaFatUnificado = str_replace('prefobroid', 'futobroid', $whereBuscaFatUnificado);
		$whereBuscaFatUnificado = str_replace('prefclioid', 'futclioid', $whereBuscaFatUnificado);

		//realizado a busca das taxas pendentes conforme parametros.
		$sqlTaxas = "SELECT 
					futoid, 
					futdt_referencia AS prefdt_referencia,
					futclioid AS prefclioid, 
					futobroid AS prefobroid, 
					futvalor AS prefvalor,
					futconnumero AS prefconnumero, 
					futstatus, 
					futdt_faturado
				FROM 
					faturamento_unificado_taxas
				WHERE
					futstatus='P'
				" . $whereBuscaFatUnificado;


		if (!$rsTaxas = pg_query($this->conn,$sqlTaxas)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao buscar taxas",1);
		}


		while ($taxa = pg_fetch_object($rsTaxas)) {

			//realizado inserção das taxas na previsao_faturamento para serem faturadas.
			$sqlInsereTaxa = "INSERT INTO previsao_faturamento
								(
								  	prefconnumero
									,prefobroid
									,prefclioid
									,prefvalor
									,prefdt_referencia
									,preftipo_obrigacao
								)
								VALUES 
								(
									" . $taxa->prefconnumero . ",
									" . $taxa->prefobroid . ",
									" . $taxa->prefclioid . ",
									" . $taxa->prefvalor . ",
									'" . $taxa->prefdt_referencia . "',
									'M'
								)";

			if (!$rsInsereTaxa = pg_query($this->conn,$sqlInsereTaxa)) {
				pg_query($this->conn, "ROLLBACK");
				throw new exception("Falha inserir taxas",1);
			}

		}		

		// if ($this->debug) {
		// 	echo "\r\n --pesquisarServiços: \r\n\r\n" .$sql. "</pre>";
		// 	$time_end = $this->microtime_float(true);
		// 	echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		// }

	}

	public function pesquisarServicos() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
				INSERT INTO previsao_faturamento
				(
				  	prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT 
					DISTINCT con.connumero AS contrato,
					
					cpt.cptobroid as obrigacao_financeira,
					
					CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
						con.conclioid 
					ELSE 
						tpc.tpccliente_pagador_monitoramento 
					END AS cliente,

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						COALESCE(cpt.cptvlr_desc_cliente, 0) 
					END AS valor,
					
					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM 
					comissao_instalacao cmi
					INNER JOIN contrato con ON cmi.cmiconoid = con.connumero
					INNER JOIN ordem_servico ord ON ord.ordconnumero = con.connumero AND ord.ordstatus = 3
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN veiculo vei ON vei.veioid = con.conveioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN comissao_padrao_tecnica cpt ON cpt.cptotioid = cmi.cmiotioid
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cpt.cptobroid
				WHERE 
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL) 
					AND tpc.tpcseguradora IS FALSE 
					AND NOT EXISTS (SELECT 1
									FROM nota_fiscal_item
									INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
									WHERE nficonoid = con.connumero
									AND nfiobroid = obr.obroid
									AND nfidt_referencia = '$this->data'
									AND nfldt_cancelamento IS NULL) 
					AND tpc.tpcgera_faturamento IS TRUE 
					AND cpt.cptdesc_cliente IS TRUE 
					AND cmi.cmidata::date >= ('$this->data'::timestamp - '1 month'::reltime )::date AND cmi.cmidata::date < '$this->data'
					AND ( CASE WHEN ( SELECT otitipo FROM os_tipo_item WHERE otioid = cmi.cmiotioid ) = 'E' THEN ( cpt.cpteqcoid = con.coneqcoid ) ELSE TRUE END )
					AND con.condt_exclusao IS NULL 
					AND cpt.cptdt_exclusao is null
					AND con.condt_ini_vigencia::date < '$this->data'::date";
		
		if (!$res = pg_query($this->conn,$sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Servicos",1);
		}
		
		if ($this->debug) { 
			echo "\r\n --pesquisarServiços: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}
	
	public function pesquisarRenovacao() {
		$time_start = $this->microtime_float(true);
				
		$sql = "
				INSERT INTO previsao_faturamento
				(
					prefobroid
					,prefclioid
					,prefconnumero
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
				)
				SELECT
				obr.obroid as obrigacao_financeira,
				
				CASE
					WHEN tpc.tpccliente_pagador_monitoramento IS NULL THEN 
						con.conclioid 
					ELSE
						tpc.tpccliente_pagador_monitoramento
					END AS cliente,

				con.connumero AS contrato,

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
					ROUND(COALESCE(cpag.cpagrenovacao,0), 2) 
				END AS valor,
				
				'$this->data'::date AS data_referencia,
				
				'L' as tipo_obrigacao
			FROM
				contrato con
				INNER JOIN contrato_pagamento cpag ON cpag.cpagconoid = con.connumero
				INNER JOIN clientes cli ON cli.clioid = con.conclioid
				INNER JOIN veiculo vei ON vei.veioid = con.conveioid
				INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
				INNER JOIN obrigacao_financeira obr ON obr.obroid = 20
			WHERE
				TRUE
				$this->filtros
				AND (con.concsioid in (1) OR con.concsioid IS NULL)
				AND tpc.tpcseguradora IS FALSE
				AND tpc.tpcgera_faturamento IS TRUE
				AND condt_exclusao IS NULL
				AND (
					(((EXTRACT(YEAR FROM con.condt_ini_vigencia)+1)*100)+(EXTRACT(MONTH FROM con.condt_ini_vigencia))
					<
					((EXTRACT(YEAR FROM '$this->data'::date))*100)+(EXTRACT(MONTH FROM '$this->data'::date))
					)
					
					OR 

					EXISTS (	SELECT
									1
								FROM
									nota_fiscal_item
									INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
								WHERE 
									nficonoid = con.connumero
									AND nfiobroid = obr.obroid
									AND nfidt_referencia >= ('$this->data'::timestamp + '1 year'::reltime )::date
									AND nfldt_cancelamento IS NULL)
				)
			ORDER BY obroid";
		
		if (!$res = pg_query($this->conn,$sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Renovacao",1);
		}
		
		if ($this->debug) { 
			echo "\r\n --pesquisarRenovações: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
        }
	}
	
	public function pesquisarAcionamentoIndevido() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
			INSERT INTO previsao_faturamento
			(
					prefobroid
					,prefclioid
					,prefconnumero
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
			)
			SELECT 
				5 as obrigacao_financeira,
				CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
					con.conclioid 
				ELSE 
					tpc.tpccliente_pagador_monitoramento 
				END AS cliente,
				
				coa.coaconoid AS contrato, 

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
				CASE 
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)
					END
					), 2)

				-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)
				
				-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)

				-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado  
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)

				-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				ELSE
					COALESCE((SELECT vmfvl_acionamento FROM valores_minimos_faturamento WHERE vmfdt_exclusao IS NULL LIMIT 1),0) 
				END AS valor,

				'$this->data'::date AS data_referencia,
				
				'M' as tipo_obrigacao
			FROM 
				contrato con
				INNER JOIN contrato_atendimento coa ON coa.coaconoid = con.connumero 
				INNER JOIN clientes cli ON cli.clioid = con.conclioid 
				INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo 
				INNER JOIN obrigacao_financeira obr ON obr.obroid = 5
			WHERE 
				TRUE
				$this->filtros
				AND (con.concsioid in (1) OR con.concsioid IS NULL) 
				AND NOT EXISTS (	SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
							WHERE nficonoid = con.connumero
							AND nfiobroid = 5
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
				AND coa.coadt_ocorrencia::date >= ('$this->data'::timestamp - '1 month'::reltime )::date 
				AND (con.condt_exclusao IS NULL OR (con.condt_exclusao is not null and con.condt_substituicao is not null))
				AND con.condt_ini_vigencia::date < '$this->data'::date
				AND coa.coaocorrencia ILIKE '%ACIONAMENTO INDEVIDO%';";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Acionamento Indevido",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarAcionamentoIndevido: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarBloqueioSolicitado() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
                INSERT INTO previsao_faturamento
                (
                        prefobroid
                        ,prefclioid
                        ,prefconnumero
                        ,prefvalor
                        ,prefdt_referencia
						,preftipo_obrigacao
                )
				SELECT 
					6 as obrigacao_financeira,
					
					CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
						con.conclioid 
					ELSE 
						tpc.tpccliente_pagador_monitoramento 
					END AS cliente,

					con.connumero AS contrato,  

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(COALESCE(vmfvl_bloqueio_solicitado ,0), 2) 
					END AS valor,

					'$this->data'::date AS data_referencia,
					
					'M' as tipo_obrigacao
				FROM 
					contrato con
					INNER JOIN contrato_atendimento coa ON coa.coaconoid = con.connumero
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN valores_minimos_faturamento vmf ON vmf.vmfdt_exclusao IS NULL 
					INNER JOIN obrigacao_financeira obr ON obr.obroid = 6
				WHERE 
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL) 
					AND NOT EXISTS (SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
							WHERE nficonoid = con.connumero
							AND nfiobroid = 6
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL)
					AND coa.coadt_ocorrencia::date BETWEEN ('$this->data'::timestamp - '1 month'::reltime)::date AND ('$this->data')::date
					AND (con.condt_exclusao is null or (con.condt_exclusao is not null and con.condt_substituicao is not null))
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND (coa.coaocorrencia ILIKE '%BLOQUEIO SOLICITADO%' OR coa.coaocorrencia ILIKE '%SOLICITAÇÃO DE BLOQUEIO%');";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Bloqueio Solicitado",1);
		}
		
		if ($this->debug) { 
			echo "\r\n --pesquisarBloqueioSolicitado: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
    
	public function pesquisarLocalizacaoWeb() {
		$time_start = $this->microtime_float(true);
				
        $sql = "
                INSERT INTO previsao_faturamento
                (
                        prefconnumero
                        ,prefobroid
                        ,prefclioid
                        ,prefvalor
                        ,prefdt_referencia
						,preftipo_obrigacao
                )
                SELECT 
					DISTINCT connumero AS contrato,

					obr.obroid AS obrigacao_financeira,

					CASE
					WHEN tpc.tpccliente_pagador_monitoramento IS NULL THEN 
						conclioid 
					ELSE
						tpc.tpccliente_pagador_monitoramento
					END AS cliente,

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(COALESCE(vmfvl_localizacao_web, 0), 2) 
					END AS valor,
					
					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM 
					contrato con
					INNER JOIN localizacao loc ON loc.locconoid = con.connumero
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo    
					INNER JOIN valores_minimos_faturamento vmf ON vmf.vmfdt_exclusao IS NULL 
					INNER JOIN obrigacao_financeira obr ON obr.obroid = 34         
				WHERE 
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL)
					AND NOT EXISTS (SELECT
								1
							FROM
								nota_fiscal_item
								INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE 
								nficonoid = con.connumero
								AND nfiobroid = obr.obroid
								AND nfidt_referencia = '$this->data'
								AND nfldt_cancelamento IS NULL)
					AND loc.locdata::date >= ('$this->data'::timestamp - '1 month'::reltime )::date 
					AND loc.locdata::date < '$this->data' 
					AND loc.loctipo = 1
					AND loc.locsucesso IS TRUE
					AND (con.condt_exclusao is null or (con.condt_exclusao is not null and con.condt_substituicao is not null))
					AND con.condt_ini_vigencia::date < '$this->data'::date";
 
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Localizacao Web",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarLocalizaçãoWeb: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    } 
	
	public function pesquisarLocalizacaoSolicitada() {
		$time_start = $this->microtime_float(true);
		
        
		$sql = "
			INSERT INTO previsao_faturamento
			(
					prefobroid
					,prefclioid
					,prefconnumero
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
			)
			SELECT 
				7 as obrigacao_financeira,
				
				CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
					con.conclioid 
				ELSE 
					tpc.tpccliente_pagador_monitoramento 
				END AS cliente,

				con.connumero AS contrato,  

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
				CASE 
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)
					END
					), 2)

				-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)
				
				-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)

				-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado  
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)

				-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				ELSE
					ROUND(COALESCE(vmf.vmfvl_localizacao_solicitada ,0), 2) 
				END AS valor,

				'$this->data'::date AS data_referencia,
				
				'M' as tipo_obrigacao
			FROM 
				contrato con
				INNER JOIN contrato_atendimento coa ON coa.coaconoid = con.connumero
				INNER JOIN clientes cli ON cli.clioid = con.conclioid
				INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
				INNER JOIN valores_minimos_faturamento vmf ON vmf.vmfdt_exclusao IS NULL
				INNER JOIN obrigacao_financeira obr ON obr.obroid = 7
			WHERE 
				TRUE
				$this->filtros
				AND (con.concsioid in (1) OR con.concsioid IS NULL) 
				AND NOT EXISTS (	SELECT 1
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
							WHERE nficonoid = con.connumero
							AND nfiobroid = 7
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
				AND (coa.coaocorrencia ILIKE '%SOLICITAÇÃO DE LOCALIZAÇÃO%' OR coa.coaocorrencia ILIKE '%LOCALIZAÇÃO SOLICITADA%') 
				AND coa.coadt_ocorrencia::date >= ('$this->data'::timestamp - '1 month'::reltime )::date 
				AND coa.coadt_ocorrencia::date < '$this->data' 
				AND (con.condt_exclusao is null or (con.condt_exclusao is not null and con.condt_substituicao is not null))
				AND con.condt_ini_vigencia::date < '$this->data'::date";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Localizacao Solicitada",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarLocalizaçãoSolicitada: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarProRataVisualizacaoGSMGPS1() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
                INSERT INTO previsao_faturamento
                (
                        prefobroid
                        ,prefclioid
                        ,prefconnumero
                        ,prefvalor
                        ,prefdt_referencia
						,preftipo_obrigacao
                )
				SELECT 
					829 as obrigacao_financeira,

					CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
						con.conclioid 
					ELSE 
						tpc.tpccliente_pagador_monitoramento 
					END AS cliente, 
					
					con.connumero AS contrato, 
					
					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(((obrvl_obrigacao / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date))::numeric, 2)
					END AS valor,

					'$this->data'::date AS data_referencia,
					
					'M' as tipo_obrigacao
				FROM 
					contrato con
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					LEFT JOIN obrigacao_financeira obr ON obr.obroid = 64
					
				WHERE 
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL) 
					AND NOT EXISTS (SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE nficonoid = con.connumero
							AND (nfiobroid = 64 OR nfiobroid = 829)
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
					AND tpcgera_faturamento IS TRUE
					AND con.conmsuboid IN (59,61,63) 
					AND con.condt_exclusao IS NULL 
					AND con.conequoid > 0 
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pró-rata VisualizacaoGSMGPS1",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoGSMGPS1: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarVisualizacaoGSMGPS1() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
                INSERT INTO previsao_faturamento
                (
                        prefobroid
                        ,prefclioid
                        ,prefconnumero
                        ,prefvalor
                        ,prefdt_referencia
						,preftipo_obrigacao
                )
				SELECT 
					64 as obrigacao_financeira,

					CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
						con.conclioid 
					ELSE 
						tpc.tpccliente_pagador_monitoramento 
					END AS cliente, 
					
					con.connumero AS contrato, 
					
					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(COALESCE(obrvl_obrigacao,0), 2) 
					END AS valor,

					'$this->data'::date AS data_referencia,
					
					'M' as tipo_obrigacao
				FROM 
					contrato con
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					LEFT JOIN obrigacao_financeira obr ON obr.obroid = 64
					
				WHERE 
					TRUE
					$this->filtros
					AND (con.concsioid in (1) OR con.concsioid IS NULL) 
					AND NOT EXISTS (SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE nficonoid = con.connumero
							AND nfiobroid = 64
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
					AND tpcgera_faturamento IS TRUE
					AND con.conmsuboid IN (59,61,63) 
					AND con.condt_exclusao IS NULL 
					AND con.conequoid > 0 
					AND con.condt_ini_vigencia::date < '$this->data'::date";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar VisualizacaoGSMGPS1",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoGSMGPS1: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarProRataVisualizacaoGSMGPS2() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
			INSERT INTO previsao_faturamento
			(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
			)
			SELECT 
				DISTINCT connumero AS contrato,

				829 as obrigacao_financeira,

				CASE WHEN tpccliente_pagador_monitoramento  IS NULL THEN 
					conclioid 
				ELSE 
					tpccliente_pagador_monitoramento 
				END AS cliente,   

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
				CASE 
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)
					END
					), 2)

				-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)
				
				-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)

				-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)

				-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				ELSE
					ROUND(((cvvvalor / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - cadi.cadicadastro::date))::numeric, 2) 
				END AS valor,

				'$this->data'::date AS data_referencia,
				
				'M' as tipo_obrigacao
			FROM 
				contrato con
				INNER JOIN contrato_aditivo_item ON caiconoid = connumero
				INNER JOIN contrato_aditivo cadi ON caicadioid = cadioid
				INNER JOIN gerador_contrato_texto ON cadigctoid = gctoid
				INNER JOIN clientes ON clioid = conclioid
				INNER JOIN tipo_contrato tpc ON tpcoid = conno_tipo
				INNER JOIN equipamento_classe ON eqcoid = coneqcoid
				INNER JOIN contrato_valor_visualizacao ON cvvconoid = connumero
				INNER JOIN obrigacao_financeira obr ON obroid = 64
			WHERE
				TRUE
				$this->filtros
				AND cadiexclusao IS NULL
				AND (conmsuboid not in (59, 61, 63, 60, 62) OR conmsuboid IS NULL)
				AND concsioid in (1)
				AND connumero NOT IN (	SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE nficonoid = connumero
							AND (nfiobroid = 64 OR nfiobroid = 829)
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
				AND tpcseguradora IS TRUE
				AND gctgctpoid = 2
				AND tpcgera_faturamento IS TRUE
				AND (cadiviaoriginal IS TRUE OR cadiviafax IS TRUE)
				AND eqcecgoid = 1 
				AND condt_exclusao IS NULL 
				AND conequoid > 0 
				AND condt_ini_vigencia::date < '$this->data'::date
				AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar VisualizacaoGSMGPS2",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoGSMGPS2: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarVisualizacaoGSMGPS2() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
			INSERT INTO previsao_faturamento
			(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
			)
			SELECT 
				DISTINCT connumero AS contrato,

				64 as obrigacao_financeira,

				CASE WHEN tpccliente_pagador_monitoramento  IS NULL THEN 
					conclioid 
				ELSE 
					tpccliente_pagador_monitoramento 
				END AS cliente,   

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
				CASE 
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)
					END
					), 2)

				-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)
				
				-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)

				-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado  
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)

				-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				ELSE
					CASE
					WHEN EXISTS	( 	SELECT cvvvalor 
								FROM contrato_valor_visualizacao 
								WHERE cvvconoid = con.connumero
								LIMIT 1) THEN
						ROUND(COALESCE(cvvvalor, 0), 2) 
					ELSE
						ROUND(COALESCE(obrvl_obrigacao, 0), 2)
					END
				END AS valor,

				'$this->data'::date AS data_referencia,
				
				'M' as tipo_obrigacao
			FROM 
				contrato con
				INNER JOIN contrato_aditivo_item ON caiconoid = connumero
				INNER JOIN contrato_aditivo ON caicadioid = cadioid
				INNER JOIN gerador_contrato_texto ON cadigctoid = gctoid
				INNER JOIN clientes ON clioid = conclioid
				INNER JOIN tipo_contrato tpc ON tpcoid = conno_tipo
				INNER JOIN equipamento_classe ON eqcoid = coneqcoid
				LEFT JOIN contrato_valor_visualizacao ON cvvconoid = connumero
				INNER JOIN obrigacao_financeira obr ON obroid = 64
			WHERE
				TRUE
				$this->filtros
				AND cadiexclusao IS NULL
				AND (conmsuboid not in (59, 61, 63, 60, 62) OR conmsuboid IS NULL)
				AND concsioid in (1)
				AND connumero NOT IN (	SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE nficonoid = connumero
							AND nfiobroid = 64
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
				AND tpcseguradora IS TRUE
				AND gctgctpoid = 2
				AND tpcgera_faturamento IS TRUE
				AND (cadiviaoriginal IS TRUE OR cadiviafax IS TRUE)
				AND eqcecgoid = 1 
				AND condt_exclusao IS NULL 
				AND conequoid > 0 
				AND condt_ini_vigencia::date < '$this->data'::date";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar VisualizacaoGSMGPS2",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoGSMGPS2: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarProRataVisualizacaoCarga1() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
			INSERT INTO previsao_faturamento
			(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
			)
			SELECT 
				DISTINCT con.connumero AS contrato, 
				
				830 as obrigacao_financeira,

				CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
					con.conclioid 
				ELSE
					tpc.tpccliente_pagador_monitoramento 
				
				END AS cliente,  

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
				CASE 
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)
					END
					), 2)

				-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)
				
				-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)

				-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)

				-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				ELSE
					ROUND((SELECT (obrvl_obrigacao / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado)::numeric, 2) 
				END AS valor,

				'$this->data'::date AS data_referencia,
				
				'M' as tipo_obrigacao
			FROM 
				contrato con
				INNER JOIN clientes cli ON cli.clioid = con.conclioid
				INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
				INNER JOIN obrigacao_financeira obr ON obr.obroid = 65
			WHERE 
				TRUE
				$this->filtros
				AND (con.concsioid in (1) OR con.concsioid IS NULL) 
				AND NOT EXISTS (SELECT 1
						FROM nota_fiscal_item
						INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
						WHERE nficonoid = con.connumero
						AND (nfiobroid = 65 OR nfiobroid = 830)
						AND nfidt_referencia = '$this->data'
						AND nfldt_cancelamento IS NULL) 
				AND conmsuboid in (60,62) 
				AND condt_exclusao IS NULL 
				AND conequoid > 0 
				AND tpcgera_faturamento IS TRUE
				AND condt_ini_vigencia::date < '$this->data'::date
				AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pró-rata Visualizacao Carga 1",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoCarga1: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
		
		$sql = "UPDATE execucao_faturamento set exfporcentagem = 95 where exfdt_termino is null";
		$res = pg_query($this->conn, $sql);
    }
	
	public function pesquisarVisualizacaoCarga1() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
			INSERT INTO previsao_faturamento
			(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
			)
			SELECT 
				DISTINCT con.connumero AS contrato, 
				
				65 as obrigacao_financeira,

				CASE WHEN tpc.tpccliente_pagador_monitoramento  IS NULL THEN 
					con.conclioid 
				ELSE
					tpc.tpccliente_pagador_monitoramento 
				
				END AS cliente,  

				-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
				CASE 
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfconoid = con.connumero
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfconoid = con.connumero
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)
					END
					), 2)

				-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)
				
				-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 4
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1)::numeric, 0) / 100)

					END
					), 2)

				-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado  
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parfclioid = con.conclioid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 2
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
					END
					), 2)

				-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfeqcoid = con.coneqcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				
				-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
				WHEN EXISTS(	SELECT 1 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1) THEN

					-- Seleciona o valor parametrizado
					ROUND((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric - 

					-- Aplica o desconto
					((	SELECT parfvl_cobrado 
						FROM parametros_faturamento 
						WHERE parftpcoid = tpc.tpcoid
						AND parfobroid = obr.obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 3
						AND parfeqcoid IS NULL
						LIMIT 1)::numeric * 

					CASE
					-- Desconto de 100 % se estiver isento de cobrança
					WHEN EXISTS (	SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
							LIMIT 1) THEN
						1
					-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
					ELSE
					
					(	COALESCE((	SELECT parfdesconto 
								FROM parametros_faturamento 
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
								LIMIT 1), 0)::numeric / 100)
					END
					), 2)
				ELSE
					ROUND(COALESCE(obrvl_obrigacao,0), 2) 
				END AS valor,

				'$this->data'::date AS data_referencia,
				
				'M' as tipo_obrigacao
			FROM 
				contrato con
				INNER JOIN clientes cli ON cli.clioid = con.conclioid
				INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
				INNER JOIN obrigacao_financeira obr ON obr.obroid = 65
			WHERE 
				TRUE
				$this->filtros
				AND (con.concsioid in (1) OR con.concsioid IS NULL) 
				AND NOT EXISTS (SELECT 1
						FROM nota_fiscal_item
						INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
						WHERE nficonoid = con.connumero
						AND nfiobroid = 65
						AND nfidt_referencia = '$this->data'
						AND nfldt_cancelamento IS NULL) 
				AND conmsuboid in (60,62) 
				AND condt_exclusao IS NULL 
				AND conequoid > 0 
				AND tpcgera_faturamento IS TRUE
				AND condt_ini_vigencia::date < '$this->data'::date";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Visualizacao Carga 1",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoCarga1: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
		
		$sql = "UPDATE execucao_faturamento set exfporcentagem = 95 where exfdt_termino is null";
		$res = pg_query($this->conn, $sql);
    }
	
	public function pesquisarProRataVisualizacaoCarga2() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
                INSERT INTO previsao_faturamento
                (
                        prefobroid
                        ,prefclioid
                        ,prefconnumero
                        ,prefvalor
                        ,prefdt_referencia
						,preftipo_obrigacao
                )
                SELECT 
					DISTINCT 

					830 as obrigacao_financeira,

					CASE WHEN tpc.tpccliente_pagador_monitoramento IS NULL THEN 
						con.conclioid 
					ELSE 
						tpc.tpccliente_pagador_monitoramento 
					END AS cliente, 

					con.connumero AS contrato, 

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(((cvvvalor / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - cadi.cadicadastro::date))::numeric, 2) 
					END AS valor,

					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM 
					contrato con
					INNER JOIN contrato_aditivo_item cai ON cai.caiconoid = con.connumero
					INNER JOIN contrato_aditivo cadi ON cai.caicadioid = cadi.cadioid
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
					INNER JOIN gerador_contrato_texto gct ON cadi.cadigctoid = gct.gctoid
					INNER JOIN obrigacao_financeira obr ON obr.obroid = 65
					LEFT JOIN contrato_valor_visualizacao cvv ON cvv.cvvconoid = con.connumero
				WHERE 
					TRUE
					$this->filtros
					AND con.conmsuboid not in (60, 62, 59, 61, 63)
					AND con.concsioid in (1)
					AND NOT EXISTS (SELECT 1
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
							WHERE nficonoid = con.connumero
							AND (nfiobroid = 65 OR nfiobroid = 830)
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
					AND tpc.tpcseguradora IS TRUE
					AND (cadi.cadiviaoriginal IS TRUE OR cadi.cadiviafax IS TRUE)
					AND tpcgera_faturamento IS TRUE
					AND eqc.eqcecgoid <> 1
					AND con.condt_exclusao IS NULL 
					AND con.conequoid > 0 
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND gct.gctgctpoid = 2
					AND cadi.cadiexclusao is null
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Visualizacao Carga 2",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoCarga2: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarVisualizacaoCarga2() {
		$time_start = $this->microtime_float(true);
		
		$sql = "
                INSERT INTO previsao_faturamento
                (
                        prefobroid
                        ,prefclioid
                        ,prefconnumero
                        ,prefvalor
                        ,prefdt_referencia
						,preftipo_obrigacao
                )
                SELECT 
					DISTINCT 65 as obrigacao_financeira,

					CASE WHEN tpc.tpccliente_pagador_monitoramento IS NULL THEN 
						con.conclioid 
					ELSE 
						tpc.tpccliente_pagador_monitoramento 
					END AS cliente, 

					con.connumero AS contrato, 

					-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfconoid = con.connumero
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfconoid = con.connumero
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 1
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfconoid = con.connumero
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1)::numeric, 0) / 100)
						END
						), 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)
					
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 4
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1)::numeric, 0) / 100)

						END
						), 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = con.conclioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = con.conclioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = con.conclioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfeqcoid = con.coneqcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(	SELECT 1 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							AND parfeqcoid IS NULL
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parftpcoid = tpc.tpcoid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 3
								AND parfeqcoid IS NULL
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
						
						(	COALESCE((	SELECT parfdesconto 
									FROM parametros_faturamento 
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = obr.obroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									AND parfeqcoid IS NULL
									AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
									LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(COALESCE(cvvvalor,0), 2) 
					END AS valor,

					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM 
					contrato con
					INNER JOIN contrato_aditivo_item cai ON cai.caiconoid = con.connumero
					INNER JOIN contrato_aditivo cadi ON cai.caicadioid = cadi.cadioid
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
					INNER JOIN gerador_contrato_texto gct ON cadi.cadigctoid = gct.gctoid
					INNER JOIN obrigacao_financeira obr ON obr.obroid = 65
					LEFT JOIN contrato_valor_visualizacao cvv ON cvv.cvvconoid = con.connumero
				WHERE 
					TRUE
					$this->filtros
					AND con.conmsuboid not in (60,62,59,61,63)
					AND con.concsioid in (1)
					AND NOT EXISTS (SELECT 1
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
							WHERE nficonoid = con.connumero
							AND nfiobroid = 65
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
					AND tpc.tpcseguradora IS TRUE
					AND (cadi.cadiviaoriginal IS TRUE OR cadi.cadiviafax IS TRUE)
					AND tpcgera_faturamento IS TRUE
					AND eqc.eqcecgoid <> 1
					AND con.condt_exclusao IS NULL 
					AND con.conequoid > 0 
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND gct.gctgctpoid = 2
					AND cadi.cadiexclusao is null";
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Visualizacao Carga 2",1);
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarVisualizaçãoCarga2: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
    }
	
	public function pesquisarProRataServicoSoftware() {
        $time_start = $this->microtime_float(true);
		
		if (!strpos($this->filtros, 'AND con') && !strpos($this->filtros, 'AND tpc')) {
			$sql = "
				INSERT INTO previsao_faturamento
				(
						prefobroid
						,prefclioid
						,prefconnumero
						,prefvalor
						,prefdt_referencia
						,preftipo_obrigacao
				)
				SELECT 
					CASE WHEN clio.clioobroid = 466 THEN
						847
					WHEN clio.clioobroid = 406 THEN
						846
					ELSE
						848
					END as obrigacao_financeira, 

					cli.clioid AS cliente, 

					0 AS contrato, 

					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(SELECT parfvl_cobrado
							FROM parametros_faturamento 
							WHERE parfclioid = cli.clioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN

						-- Seleciona o valor parametrizado
						ROUND((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - clio.cliodt_inicio::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = cli.clioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 

						-- Aplica o desconto
						((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - clio.cliodt_inicio::date) AS parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = cli.clioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 

						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = cli.clioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT (parfvl_cobrado / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - clio.cliodt_inicio::date) AS parfvl_cobrado 
										FROM parametros_faturamento 
										WHERE parfclioid = cli.clioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						ROUND(COALESCE(((cliovl_obrigacao / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - clio.cliodt_inicio::date)::numeric)::numeric, 0), 2)
					END AS valor, 

					'$this->data'::date AS data_referencia,

					'M' as tipo_obrigacao
				FROM 
					cliente_obrigacao_financeira clio
					INNER JOIN clientes cli ON cli.clioid = clio.clioclioid 
					INNER JOIN obrigacao_financeira obr ON obr.obroid = clio.clioobroid 
				WHERE 
					TRUE
					$this->filtros
					AND cli.clidt_exclusao IS NULL
					AND clio.cliodt_termino IS NULL
					AND (
						SELECT
							COUNT(contrato.conclioid)
						FROM
							contrato
								INNER JOIN
									tipo_contrato
										ON
											contrato.conno_tipo = tipo_contrato.tpcoid
						WHERE
							contrato.conclioid = clioid
						AND
							contrato.condt_exclusao IS NULL
						AND
							tipo_contrato.tpcgera_faturamento IS TRUE
					) > 0
					AND clio.cliodemonstracao IS FALSE
					AND obr.obroftoid = 8
					AND NOT EXISTS (SELECT 1
									FROM nota_fiscal_item
									INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
									WHERE nflclioid = cli.clioid
									AND (nfiobroid = 828 OR nfiobroid = 406)
									AND nfidt_referencia = '$this->data'
									AND nfldt_cancelamento IS NULL) 
					AND clio.cliocortesia IS FALSE
					AND clio.cliodt_inicio >= '01/01/2009'
					AND clio.clioobroid IN (466, 406, 50)
					-- AND EXISTS (SELECT 1 FROM tipo_contrato WHERE tpcoid = 0 AND tpcgera_faturamento IS TRUE)
					AND ('$this->data'::date - clio.cliodt_inicio::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)
					";
			
			if (!$this->send_query($sql)) {
				pg_query($this->conn, "ROLLBACK");
				throw new exception("Falha ao atualizar Servicos Software",1);
			} 
				
			if ($this->debug) {
				echo "\r\n --pesquisarServicoSoftware: \r\n\r\n" .$sql. "</pre>";
				$time_end = $this->microtime_float(true);
				echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
			}
		}
    }
	
	public function pesquisarServicoSoftware() {
        $time_start = $this->microtime_float(true);
		
		if (!strpos($this->filtros, 'AND con') &&  !strpos($this->filtros, 'AND tpc')) {
			$sql = "
				INSERT INTO previsao_faturamento
				(
						prefobroid
						,prefclioid
						,prefconnumero
						,prefvalor
						,prefdt_referencia
						,preftipo_obrigacao
				)
				SELECT 
					clio.clioobroid as obrigacao_financeira, 

					cli.clioid AS cliente, 

					0 AS contrato, 

					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					CASE 
					WHEN EXISTS(	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = cli.clioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) THEN
							
						-- Seleciona o valor parametrizado
						ROUND((	SELECT parfvl_cobrado  
							FROM parametros_faturamento 
							WHERE parfclioid = cli.clioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric - 
							
						-- Aplica o desconto
						((	SELECT parfvl_cobrado 
							FROM parametros_faturamento 
							WHERE parfclioid = cli.clioid
							AND parfobroid = obr.obroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1)::numeric * 
							
						CASE
						-- Desconto de 100 % se estiver isento de cobrança
						WHEN EXISTS (	SELECT 1
								FROM parametros_faturamento
								WHERE parfclioid = cli.clioid
								AND parfobroid = obr.obroid
								AND parfdt_exclusao IS NULL
								AND parfativo = 't'
								AND parfnivel = 2
								AND '$this->data' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
								LIMIT 1) THEN
							1
						-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
						ELSE
							(	COALESCE((	SELECT parfdesconto 
										FROM parametros_faturamento 
										WHERE parfclioid = cli.clioid
										AND parfobroid = obr.obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND '$this->data' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
										LIMIT 1), 0)::numeric / 100)
						END
						), 2)
					ELSE
						COALESCE(cliovl_obrigacao,0) 
					END AS valor, 
					
					'$this->data'::date AS data_referencia,
					
					'M' as tipo_obrigacao
				FROM 
					cliente_obrigacao_financeira clio
					INNER JOIN clientes cli ON cli.clioid = clio.clioclioid 
					INNER JOIN obrigacao_financeira obr ON obr.obroid = clio.clioobroid 
				WHERE 
					TRUE
					$this->filtros
					AND cli.clidt_exclusao IS NULL
					AND clio.cliodt_termino IS NULL
					AND (
						SELECT
							COUNT(contrato.conclioid)
						FROM
							contrato
								INNER JOIN
									tipo_contrato
										ON
											contrato.conno_tipo = tipo_contrato.tpcoid
						WHERE
							contrato.conclioid = clioid
						AND
							contrato.condt_exclusao IS NULL
						AND
							tipo_contrato.tpcgera_faturamento IS TRUE
					) > 0
					AND clio.cliodemonstracao IS FALSE
					AND obr.obroftoid = 8
					AND (((	SELECT 
							MAX(nfidt_referencia) 
							FROM nota_fiscal 
							INNER JOIN nota_fiscal_item ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
							WHERE nflclioid = cli.clioid 
							AND nfiobroid = clio.clioobroid 
							AND nfldt_cancelamento IS NULL) + CAST(clio.cliono_periodo_mes || ' month' AS interval))::date <= '$this->data'
							
							OR NOT EXISTS (	SELECT 1
											FROM nota_fiscal 
											INNER JOIN nota_fiscal_item ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
											WHERE nflclioid = cli.clioid 
											AND nfiobroid = clio.clioobroid 
											AND nfldt_cancelamento IS NULL))
					AND NOT EXISTS (SELECT 1
									FROM nota_fiscal_item
									INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
									WHERE nflclioid = cli.clioid
									AND nfiobroid = obr.obroid
									AND nfidt_referencia = '$this->data'
									AND nfldt_cancelamento IS NULL) 
					AND clio.cliocortesia IS FALSE
					AND clio.clioobroid IN (466, 406, 50)
					AND clio.cliodt_inicio >= '01/01/2009'
					-- AND EXISTS (SELECT 1 FROM tipo_contrato WHERE tpcoid = 0 AND tpcgera_faturamento IS TRUE)
					";
			
			if (!$this->send_query($sql)) {
				pg_query($this->conn, "ROLLBACK");
				throw new exception("Falha ao atualizar Servicos Software",1);
			} 
				
			if ($this->debug) {
				echo "\r\n --pesquisarServicoSoftware: \r\n\r\n" .$sql. "</pre>";
				$time_end = $this->microtime_float(true);
				echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
			}
		}
    }
	
	public function finalizarProcesso($resultado) {
	
		$sql = "UPDATE execucao_faturamento set exfporcentagem = 100, exfdt_termino = now(), exfresultado = '$resultado' where exfdt_termino is null";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas.",1);
		}
	}
	
	public function verificarPendencias($todos) {
		$time_start = $this->microtime_float(true);
		
		$sql = "SELECT
					prefobroid,
					prefconnumero,
					prefvalor,
					obrobrigacao
				FROM 
					previsao_faturamento
				INNER JOIN obrigacao_financeira ON obroid = prefobroid";
		
		if (!$todos) {
			$sql .= " 
				WHERE
					TRUE
					$this->clausulasPrevSemData";
		}
		$sql .= "
				ORDER BY 
					obrobrigacao";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception("Falha ao verificar pendências",1);
		} 
		
		if ($this->debug) {
			echo "\r\n --verificarPendências: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
		return $res;
	}
	
	public function gerarCSVPendencias() {
		$time_start = $this->microtime_float(true);
		
		$sql = "SELECT
					prefobroid,
					prefclioid,
					prefconnumero,
					prefvalor,
					obrobrigacao,
					to_char(prefdt_referencia, 'DD/MM/YYYY') as dt_referencia,
					clinome,
					NULL AS futoid
				FROM 
					previsao_faturamento
					INNER JOIN obrigacao_financeira ON obroid = prefobroid
					INNER JOIN clientes on clioid = prefclioid
				
				ORDER BY 
					clinome, obrobrigacao";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception("Falha ao verificar pendências",1);
		} 
		
		if ($this->debug) {
			echo "\r\n --verificarPendências: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
		
		return $res;
	}
	
	public function gerarRelatorioPendenciasCSV() {
		
		$retorno = array();
		
		$sql = "SELECT clino_cpf AS cpf,
				clino_cgc AS cnpj,
				clitipo AS tipo,
				clinome AS cliente,
					     (SELECT sum(prefvalor)
					      FROM previsao_faturamento
					      WHERE prefclioid = cli.clioid
					        AND preftipo_obrigacao = 'M') AS valor_monitoramento,				
					     (SELECT sum(prefvalor)
					      FROM previsao_faturamento
					      WHERE prefclioid = cli.clioid
					        AND preftipo_obrigacao = 'L') AS valor_locacao,
				          NULL AS valor_taxas,
		                  sum(prefvalor) AS valor_total
				   FROM previsao_faturamento pref
				 		INNER JOIN clientes cli ON clioid = prefclioid
				   GROUP BY clino_cgc,
				            clinome,
				            clioid
				";
				
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception('Falha ao Verificar Pendências');
		}
		
		return $res;
	}
	
	public function gerarRelatorioPreFaturamento() {
		
		$sql = "select
					clinome,
					connumero,
					tpcdescricao,
					to_char(condt_ini_vigencia, 'DD/MM/YYYY') as condt_ini_vigencia,
					csidescricao,
					veiplaca,
					eqcdescricao,
					obrobrigacao,
							prefvalor,
							1 as origem
						from
					previsao_faturamento
					inner join clientes on prefclioid = clioid
					left join contrato on connumero = prefconnumero
					left join tipo_contrato on tpcoid = conno_tipo
					left join contrato_situacao ON concsioid = csioid
					left join veiculo on conveioid = veioid
					left join equipamento_classe on eqcoid = coneqcoid
					left join obrigacao_financeira on obroid = prefobroid
				where
					TRUE
							$this->clausulasPrevSemData					
						
			";

		if (!$res = pg_query($this->conn, $sql)) {
			throw new exception ("Erro ao gerar relatório pré-faturamento", 1);
		}
		
		return $res;
	}
	
	public function getValorMinimoFaturamento() {
		$result = array();
		
		$sql = "SELECT 
					vmfvl_faturamento_minimo
				FROM 
					valores_minimos_faturamento 
				WHERE
					vmfdt_exclusao IS NULL
				LIMIT 1";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao buscar valores mínimos para faturamento');
		}
		$row = pg_fetch_assoc($res, 0);
		
		return $row['vmfvl_faturamento_minimo'];
	}
	
	public function prepararFaturamento($tipo) {
		
		// Inicia controle de faturamento concorrente
		$sql = "INSERT INTO execucao_faturamento(exfusuoid, exftipo_processo, exfporcentagem, exfparametros) 
				VALUES(".$this->usuario.", '".$tipo."', 0, '".$this->data."|".$this->doc."|".$this->tipo."|".$this->cliente."|".$this->tipo_contrato."|".$this->contrato."|".$this->placa."|".$this->obroids."|".$this->usuario."')";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao preparar faturamento.');
		}
	}
	
	public function recuperarParametros($finalizado) {
		
		if (!$finalizado) {
			$filtro = "AND exfdt_termino IS NULL";
		} else {
			$filtro = "ORDER BY exfdt_termino DESC";
		}
		
		// Recupera os parâmetros salvos pelo resumo
		$sql = "SELECT 
					nm_usuario,
					usuemail,
					exfoid serial,
					exfusuoid,
					TO_CHAR(exfdt_inicio, 'HH24:MI:SS') as inicio,
					TO_CHAR(exfdt_termino, 'HH24:MI:SS') as termino,
					TO_CHAR(exfdt_inicio, 'DD/MM/YYYY HH24:MI:SS') as data_inicio,
					TO_CHAR(exfdt_termino, 'DD/MM/YYYY HH24:MI:SS') as data_termino,
					exftipo_processo,
					exfporcentagem,
					exfparametros 
				FROM 
					execucao_faturamento
				INNER JOIN usuarios on cd_usuario = exfusuoid
				$filtro
				LIMIT 1";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao recuperar parâmetros.');
		}
		
		return $res;
	}
	
	public function faturar() {
		// Prepara as querys que serão executadas no faturamento
		$sql = "PREPARE recuperar_cliente(integer) AS
				SELECT
					cliformacobranca,
					clidia_vcto,
					cliret_iss_perc,
					cliret_iss,
					cliret_pis_perc,
					cliret_cofins_perc,
					cliret_csll_perc
				FROM
					clientes 
				WHERE
					clioid = $1
				LIMIT 1 ";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Seleção de cliente)");
		}
		
		$sql = "PREPARE inserir_nota_fiscal(text, text, integer, integer, timestamp, timestamp, integer, numeric(12,2), numeric(12,2), numeric(12,2), numeric(12,2), numeric(12,2)) AS
				INSERT INTO nota_fiscal (
					nfldt_nota,
					nflnatureza,
					nfltransporte,
					nflclioid,
					nflno_numero,
					nflserie,
					nfldt_emissao,
					nfldt_referencia,
					nfldt_vencimento,
					nfldt_faturamento,
					nflusuoid,
					nflvl_total,
					nflvlr_iss,
					nflvlr_pis,
					nflvlr_cofins,
					nflvlr_csll,
					nflvlr_ir) 								
				VALUES (
					now()::date,
					'PRESTACAO DE SERVICOS', 
					$2, 
					$3, 
					$4, 
					'A',
					now()::date,
					$5, 
					$6, 
					now()::date, 
					$7, 
					$8,
					$9, 
					$10,
					$11,
					$12,
					'0.0')
				RETURNING nfloid";
				
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de nota fiscal)");
		}
		
		$sql = "PREPARE inserir_titulo(integer, date, numeric(10,2), numeric(10,2), integer, integer, integer) AS
				INSERT INTO titulo (
						titclioid,
						titdt_vencimento,
						titvl_titulo,
						titvl_desconto,
						titformacobranca,
						titusuoid_alteracao,
						titnfloid,
						titfatura_unica) 
				VALUES (
						$1,
						$2,
						$3,
						$4,
						$5,
						$6,
						$7,
						't')";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de título)");
		}
		
		$sql = "PREPARE recuperar_obrigacoes(integer) AS
					SELECT
						prefconnumero,
						prefobroid,
						obrobrigacao,
						prefvalor,
						prefdt_referencia,
						preftipo_obrigacao,
						NULL AS futoid 
					FROM
						previsao_faturamento
					INNER JOIN obrigacao_financeira ON obroid = prefobroid
					LEFT JOIN contrato ON connumero = prefconnumero
					WHERE
					TRUE
					$this->clausulasPrevSemData
						AND prefclioid = $1;";

		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Seleção de obrigações financeiras)");
		}
		
		$sql = "PREPARE inserir_item_nota(integer, character(2), integer, integer, text, numeric(10,2), date, real, integer, integer, character(1)) AS
				INSERT INTO nota_fiscal_item (
					nfino_numero,
					nfiserie,
					nficonoid,
					nfiobroid,
					nfids_item,
					nfivl_item,
					nfidt_referencia,
					nfidesconto,
					nfiveioid,
					nfinfloid,
					nfitipo)
				VALUES (
					$1,
					$2,
					$3,
					$4,
					$5,
					$6,
					$7,
					$8,
					$9,
					$10,
					$11)";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
		}
		
		$sql = "PREPARE excluir_cliente(integer) AS
				DELETE FROM
					previsao_faturamento
				WHERE 1=1
					$this->clausulasPrevSemData
					AND prefclioid = $1;
					";			
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
		}
		
		$sql = "PREPARE excluir_cliente_taxas(integer) AS
				UPDATE faturamento_unificado_taxas
				SET futstatus='F', futdt_faturado=NOW()
				WHERE futclioid = $1
				AND futoid in (
									SELECT
										futoid
									FROM
										(
											SELECT 
												futoid, 
												futdt_referencia AS prefdt_referencia, 
												futclioid AS prefclioid, 
												futobroid AS prefobroid, 
												futvalor::numeric AS prefvalor, 
												futconnumero AS prefconnumero, 
												futstatus, 
												futdt_faturado
											FROM faturamento_unificado_taxas
											WHERE futstatus='P'
										) previsao_faturamento
									WHERE 1=1
										$this->clausulasPrevSemData
										AND prefclioid = $1
								);
					";
				
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
		}

		$sql = "PREPARE atualizar_andamento(double precision) AS
				UPDATE 
					execucao_faturamento 
				SET 
					exfporcentagem = $1 
				WHERE 
					exfdt_termino is null";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
		}
		
		// Seleciona a quantidade de clientes para faturar
		$sql = "SELECT COUNT(DISTINCT prefclioid) AS quantidade 
				FROM (
					SELECT
						DISTINCT prefclioid
					FROM
					previsao_faturamento
				WHERE 1=1
						{$this->clausulasPrevSemData}
			
					
				) qt		
		";
		
		//echo "\r\n".$sql."\r\n";
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao calcular quantidade de clientes para faturamento.');
		}
		$row = pg_fetch_assoc($res, 0);
		
		$cli_total = $row['quantidade'];
		
		// Seleciona o valor mínimo para faturar
		$valor_minimo = $this->getValorMinimoFaturamento();
		
		// Soma os valores de cada cliente
		// echo "<pre>" . $sql = "SELECT
		// 			sum(prefvalor) as total, prefclioid,
		// 			(SELECT SUM(prefvalor) FROM previsao_faturamento WHERE preftipo_obrigacao = 'M' AND 1=1 $this->clausulasPrevSemData GROUP BY prefclioid) AS M_TOTAL,
		// 			(SELECT SUM(prefvalor) FROM previsao_faturamento WHERE preftipo_obrigacao = 'L' AND 1=1 $this->clausulasPrevSemData GROUP BY prefclioid) AS L_TOTAL
		// 		FROM
		// 			previsao_faturamento
		// 		WHERE 1=1
		// 			$this->clausulasPrevSemData
		// 		GROUP BY
		// 			prefclioid";

		$whereBusca = str_replace('prefclioid', 'pref.prefclioid', $this->clausulasPrevSemData);
		$whereBusca = str_replace('prefconnumero', 'pref.prefconnumero', $whereBusca);
		$whereBusca = str_replace('prefobroid', 'pref.prefobroid', $whereBusca);

		$sql = "SELECT DISTINCT ON (cliente)
					cliente AS prefclioid,
					total,
									
					CASE WHEN tipo = 'L' THEN
						valor
					WHEN (lead(tipo,1) OVER (PARTITION BY cliente)) = 'L' THEN
						lead(valor,1) OVER (PARTITION BY cliente)
					ELSE
						0
					END AS L_TOTAL,
				
					CASE WHEN tipo = 'M' THEN
						valor
					WHEN (lead(tipo,1) OVER (PARTITION BY cliente)) = 'M' THEN
						lead(valor,1) OVER (PARTITION BY cliente)
					ELSE
						0
					END AS M_TOTAL,
					tipo
				FROM 
				(
					SELECT 
						total, 
						cliente, 
						sum(s.valor) as valor, 
						tipo
					FROM
					(			
				
						SELECT
							sum(pref.prefvalor) as total,
							pref.prefclioid AS cliente,
							sub_totais.valor AS valor,
							sub_totais.preftipo_obrigacao AS tipo
						FROM
							previsao_faturamento AS pref
							INNER JOIN (
								SELECT
									SUM(prefvalor) AS valor,
									prefclioid,
									preftipo_obrigacao
								FROM
									previsao_faturamento
								INNER JOIN
									obrigacao_financeira ON prefobroid = obroid
								WHERE
									1=1
									{$this->clausulasPrevSemData}
								GROUP BY
									prefclioid,
									preftipo_obrigacao
								ORDER BY
									preftipo_obrigacao ASC
							) sub_totais ON sub_totais.prefclioid = pref.prefclioid
						WHERE 1=1
							$whereBusca
						GROUP BY
							pref.prefclioid,
							sub_totais.valor,
							sub_totais.preftipo_obrigacao
					) s
					GROUP BY 
						s.total, 
						s.cliente, 
						s.tipo 
				) sub";

		//echo "\r\n".$sql."\r\n";
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao Gerar Faturamento (Seleção de clientes)");
		}
		
		// Inicio do processo de geração de notas e títulos para 
		$cli_faturado = 0;
		$transaction = 0;
		
		while ($row = pg_fetch_array($res)) {
			if (!$transaction) {
				pg_query($this->conn, "BEGIN");
				$transaction = 1;
			}
			
			//$time_start = $this->microtime_float(true);
			
			$cliente = $row['prefclioid'];
			
			// Se o cliente atingiu valor mínimo, gera faturamento
			if ($row['total'] >= $valor_minimo) {
				// Pesquisa dados tributários e forma de cobrança do cliente
				$sql = "
						SELECT
							cliformacobranca,
							clidia_vcto,
							cliret_iss_perc,
							cliret_iss,
							cliret_pis_perc,
							cliret_cofins_perc,
							cliret_csll_perc
						FROM
							clientes 
						WHERE
							clioid = ".$row['prefclioid']."
						LIMIT 1 ";
				//echo "\r\n".$sql."\r\n";
				if (!$res2 = pg_query($this->conn, $sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Pesquisa de parametros de clientes)");
				}
				$cliente = pg_fetch_assoc($res2);

				// Insere a nota fiscal
				$nflnatureza = '';
				$nfltransporte = '';
				$nflclioid = $row['prefclioid'];
				$nflno_numero = $this->getSequencerSerieA();
				$nfldt_vencimento = ($cliente['clidia_vcto']>0) ? $cliente['clidia_vcto'] : 16;
				
				// Cálculo de vencimento
				$emissao     = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
				$vencimento  = mktime(0, 0, 0, date("m"), $nfldt_vencimento, date("Y"));
				
				$diff 	= ($vencimento - $emissao) / 86400;
				$mes 	= date("m");
				$ano 	= date("Y");

				while (intval($diff) < 10) {
					$mes++;
					if ($mes == 13) {
						$mes = 1;
						$ano++;
					}
					
					$vencimento = mktime(0, 0, 0, $mes, $nfldt_vencimento, $ano);
					$diff 		= ($vencimento - $emissao) / 86400;
				}
				
				//$nfldt_vencimento_txt = $vencimento->format('Y-m-d');
				$nfldt_vencimento_txt = date("d/m/Y", $vencimento);
				
				$nflusuoid = $this->usuario;
				$nflvl_total = $row['total'];
				$vlr_iss = ($cliente['cliret_iss'] == 't') ? $cliente['cliret_iss_perc']/100 * $nflvl_total : 0;				
				$nflvlr_pis = $cliente['cliret_pis_perc']/100 * $nflvl_total;
				$nflvlr_cofins = $cliente['cliret_cofins_perc']/100 * $nflvl_total;
				$nflvlr_csll = $cliente['cliret_csll_perc']/100 * $nflvl_total;
				
				$sql = "EXECUTE inserir_nota_fiscal (
							'$nflnatureza',
							'$nfltransporte',
							$nflclioid,
							$nflno_numero,
							'$this->data',
							'$nfldt_vencimento_txt',
							$nflusuoid,
							$nflvl_total,
							$vlr_iss,
							$nflvlr_pis,
							$nflvlr_cofins,
							$nflvlr_csll
						)";
				//echo "\r\n".$sql."\r\n";
				if (!$res3 = pg_query($this->conn,$sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Inserção de nota para o cliente $nflclioid)");
				}
				//echo "Inseriu nota! \r\n";
				
				$nfloid = pg_fetch_result($res3,0,0);
				$nfloid = ($nfloid>0) ? $nfloid : 0;
				
				// Insere o título
				$titclioid = $row['prefclioid'];
				$titdt_vencimento = $nfldt_vencimento_txt;
				$titvl_titulo = $nflvl_total;
				$titvl_desconto = 0;
				$titformacobranca = ($cliente['cliformacobranca'] != '' ? $cliente['cliformacobranca'] : 73);
				$titusuoid_alteracao = $this->usuario;
				
				$sql = "EXECUTE inserir_titulo (
							$titclioid,
							'$titdt_vencimento',
							$titvl_titulo,
							$titvl_desconto,
							$titformacobranca,
							$titusuoid_alteracao,
							$nfloid
						)";
				//echo "\r\n".$sql."\r\n";
				
				if (!$res4 = pg_query($this->conn, $sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Inserção de título para o cliente $nflclioid)");
				}
				
				// Seleciona obrigações financeiras do cliente
				$sql = " EXECUTE recuperar_obrigacoes(".$row['prefclioid'].")";
				
				if (!$res5 = pg_query($this->conn, $sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Seleção de itens da nota fiscal)");
				}
				
				while ($row4 = pg_fetch_assoc($res5)) {
					// Insere as obrigações (itens da nota fiscal)
					$nfino_numero = $nflno_numero;
					$nfiserie = 'A';
					$nficonoid = $row4['prefconnumero'];
					$nfiobroid = $row4['prefobroid'];
					$nfids_item = $row4['obrobrigacao'];
					$nfivl_item = $row4['prefvalor'];
					$nfidt_referencia = $row4['prefdt_referencia'];
					$nfidesconto = 0;
					$nfiveioid = empty($row4['conveioid']) ? 'NULL' : $row4['conveioid'];
					$nfinfloid = $nfloid;
					$nfitipo = $row4['preftipo_obrigacao'];
					
					
					$sql = "EXECUTE inserir_item_nota (
								$nfino_numero,
								'$nfiserie',
								$nficonoid,
								$nfiobroid,
								'$nfids_item',
								$nfivl_item,
								'$nfidt_referencia',
								$nfidesconto,
								$nfiveioid,
								$nfinfloid,
								'$nfitipo'
							)";
					
					//echo "\r\n".$sql."\r\n";
					
					if (!$res6 = pg_query($this->conn, $sql)) {
						pg_query($this->conn, "ROLLBACK");
						throw new Exception ("Falha ao Gerar Faturamento (Inserção do item '".$row4['obrobrigacao']."' para o cliente ".$row['prefclioid'].") ".pg_last_error());
					}
				}
				
				// Exclui as obrigações do cliente da previsão
				$sql = " EXECUTE excluir_cliente(".$row['prefclioid'].");".
					   " EXECUTE excluir_cliente_taxas(".$row['prefclioid'].")";
				
				if (!$res7 = pg_query($this->conn, $sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Deletando previsao do cliente ".$row['prefclioid'].")");
				}
			}
			
			//$time_end = $this->microtime_float(true);
			//echo "\r\n".($time_end - $time_start)."</pre>";
			$cli_faturado++;
			
			// Atualiza porcetagem processada e commita atualizações
			if (($cli_faturado % 500) == 0) {
				$sql = "EXECUTE atualizar_andamento(".round(($cli_faturado/$cli_total) * 100, 1).");";
				$res8 = pg_query($this->conn, $sql);
				pg_query($this->conn, "END");
				$transaction = 0;
			}
		}
		
		if ($transaction) {
			pg_query($this->conn, "END");
		}
	}
	
	public function getSequencerSerieA() {
		$sql = "SELECT MAX(nflno_numero)+1 FROM nota_fiscal WHERE nflserie = 'A' LIMIT 1";
		$res = pg_query($this->conn,$sql);
		if (!$res) {
			throw new Exception ("Falha ao gerar numero de série da nota fiscal");
		}
		return pg_fetch_result($res,0,0);
	}
    
    public function getObrigacaoFinanceira($obroid) {
		
		if (!$obroid){                
			throw new exception("Falha ao pesquisar uma obrigação financeira.",1);                
		}
		
		$sql = "
			SELECT
				obrobrigacao AS obrigacao_descricao
			FROM
				obrigacao_financeira
			WHERE
				obroid = $obroid
		";
		$result = pg_query($this->conn, $sql);
		
		return $result;            
	}
        
    public function getContrato($connumero){
            
            if (!$connumero){                
                throw new exception("Falha ao pesquisar contratos",1);                
            }
            
            $sql = "
                SELECT
                    to_char(condt_ini_vigencia, 'DD/MM/YYYY') AS data_instalacao,                     
                    tpcdescricao AS tipo,
                    csidescricao AS status, 
                    veiplaca AS placa,
                    eqcdescricao AS classe
                FROM contrato
                LEFT JOIN tipo_contrato ON conno_tipo = tpcoid
                LEFT JOIN contrato_situacao ON concsioid = csioid
                LEFT JOIN veiculo ON conveioid = veioid
                LEFT JOIN equipamento_classe ON coneqcoid = eqcoid
                WHERE 
                    connumero = $connumero                    
            ";
             $result = pg_query($this->conn, $sql);
            
            return $result;
        }
    
    public function tiposContratosAtivos() {
        	 
        	$sql = '
			SELECT
		    	t.tpcoid, t.tpcdescricao
    		FROM
    			tipo_contrato AS t
    		WHERE
    			t.tpcativo IS TRUE
    		ORDER BY
    			t.tpcdescricao';
        	
        	$rs = pg_query($this->conn, $sql);
        	 
        	if (!$rs) {
        		throw new Exception('Falha na pesquisa dos tipos de contrato.');
        	}
        	 
        	$tipos = array();
        	while($tipo = pg_fetch_object($rs)) {
        		$voTipo = new stdClass();
        		$voTipo->key = $tipo->tpcoid;
        		$voTipo->value = $tipo->tpcdescricao;
        
        		$tipos[] = $voTipo;
        	}
        	 
        	return $tipos;
        }
        
	public function valorReferencia($dataRef) {
		$sql = "select igpvl_referencia from igpm where igpdt_referencia = ('$this->data'::date - '1 month'::interval)";
		
		$rs = pg_query($this->conn, $sql);
		
		if (!$rs) {
			throw new Exception('Falha na pesquisa valor Referencia.');
		} elseif (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs,0,0);
        } else {
            return 0;
        }
	} 

	public function igpmReajuste($igpm, $anopassado, $semestre, $mes) {
		
		$update1 = "
					UPDATE
						contrato_obrigacao_financeira
					SET
						cofdt_ult_referencia = date_trunc('month','today'::date),
						cofvl_obrigacao = (cofvl_obrigacao::float4 * $igpm)::numeric
					FROM
						contrato
					WHERE
						cofobroid in (1,2,20)
						AND cofdt_termino IS NULL
						AND contrato.connumero = cofconoid
						AND contrato.condt_exclusao IS NULL
						AND ((to_char('$anopassado'::date,'MM/YYYY') = to_char (cofdt_inicio,'MM/YYYY')
						AND cofdt_ult_referencia is null)
						OR to_char('$anopassado'::date,'MM/YYYY') = to_char(cofdt_ult_referencia,'MM/YYYY'))";
		
		$res1 = pg_query($this->conn,$update1);
		if (!$res1) {
			throw new Exception ("Falha ao Gerar Reajustar IGPM (1)",0);
		}
		
		# Gravar a data da ult referencia para os registros que entram na condição do cliente_faturamento.
		$update2 = "
					UPDATE
						contrato_obrigacao_financeira
					SET
						cofdt_ult_referencia = date_trunc('month','today'::date)
					FROM
						contrato 
					WHERE
						cofobroid IN (1,2,20,64,65)
						AND cofdt_termino IS NULL
						AND contrato.connumero=cofconoid
						AND contrato.condt_exclusao IS NULL
						AND ((to_char('$anopassado'::date,'MM/YYYY') = to_char (cofdt_inicio,'MM/YYYY')
						AND cofdt_ult_referencia is null)
						OR to_char('$anopassado'::date,'MM/YYYY') = to_char (cofdt_ult_referencia,'MM/YYYY'))";
		
		$res2 = pg_query($this->conn,$update2);
		if (!$res2) {
			throw new Exception ("Falha ao Gerar Reajustar IGPM (2)",0);
		} 
		
		# Reajustar mensalidade do modem
		$update3 = "
					UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date),
						cliovl_obrigacao = (cliovl_obrigacao::float4 * $igpm)::numeric
					WHERE 
						(clioobroid=33)
						AND cliodt_termino IS NULL
						AND ((to_char('$anopassado'::date,'MM/YYYY') = to_char (cliodt_inicio,'MM/YYYY')
						AND cliodt_ult_referencia IS NULL)
						OR to_char('$anopassado'::date,'MM/YYYY') = to_char (cliodt_ult_referencia,'MM/YYYY'))";

		$res3 = pg_query($this->conn,$update3);
		if (!$res3) {
			throw new Exception ("Falha ao Gerar Reajustar IGPM (3)",0);
		}
		
		# Colocar data de ultima referência para cobrança de semestralidade do software de sascarga
		# a cobrança é de 6 em 6 meses mas não será reajustada(por enquanto)
		
		# ATUALIZA DATA DE ULTIMA REFERENCIA PARA CLIENTES QUE FAZEM PAGAMENTO MENSAL
		$query_ultima_mes="
					UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date)
					WHERE
						cliono_periodo_mes = 1
						AND (clioobroid IN (50, 406))
						AND cliodt_termino is null
						AND (( to_char('$mes'::date,'MM/YYYY') = to_char(cliodt_inicio,'MM/YYYY') 
						AND cliodt_ult_referencia is null) 
						OR to_char('$mes'::date,'MM/YYYY') = to_char(cliodt_ult_referencia,'MM/YYYY'));";
		
		$res4 = pg_query($this->conn,$query_ultima_mes);
		if (!$res4) {
			throw new Exception ("Falha ao Gerar Reajustar IGPM (4)",0);
		}
		 
		#ATUALIZA DATA DE ULTIMA REFERENCIA PARA CLIENTES QUE FAZEM PAGAMENTO SEMESTRAL
		$query_ultima_semestral="
					UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date)
					WHERE
						cliono_periodo_mes = 6
						AND ( clioobroid IN (50,406) )
						AND cliodt_termino is null
						AND ( ( to_char ('$semestre'::date,'MM/YYYY') = to_char(cliodt_inicio,'MM/YYYY') 
						AND cliodt_ult_referencia is null) 
						OR to_char('$semestre'::date,'MM/YYYY') = to_char(cliodt_ult_referencia,'MM/YYYY'));";

		$res5 = pg_query($this->conn,$query_ultima_semestral);
		if (!$res5) {
			throw new Exception ("Falha ao Gerar Reajustar IGPM (5)",0);
		}
		
		#ATUALIZA DATA DE ULTIMA REFERENCIA PARA CLIENTES QUE FAZEM PAGAMENTO ANUAL
		$query_ultima_anual="
					UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date)
					WHERE
						cliono_periodo_mes = 12
						AND ( clioobroid IN (50,406))
						AND cliodt_termino is null
						AND ( ( to_char ('$anopassado'::date,'MM/YYYY') = to_char(cliodt_inicio,'MM/YYYY') 
						AND cliodt_ult_referencia is null) 
						OR to_char('$anopassado'::date,'MM/YYYY') = to_char(cliodt_ult_referencia,'MM/YYYY'));";
		
		$res6 = pg_query($this->conn,$query_ultima_anual);
		if (!$res6) {
			throw new Exception ("Falha ao Gerar Reajustar IGPM (6)",0);
		}
	}

	public function pesquisarCliente($desc) {
		$retorno = array();
		
		if($desc != ''){
			$where .= " AND clinome ilike '%$desc%' ";
		}
		
		$sql = "
				SELECT 
                    clioid AS codigo, 
                    clinome AS descricao
                FROM 
                    clientes
                WHERE 
                    clidt_exclusao IS NULL 
                    $where
                ORDER BY clinome
		";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new exception("Falha ao Pesquisar Cliente");
		}
		
		while ($arr = pg_fetch_assoc($res)) {
			$retorno[] = array(
					"codigo"	=>	$arr['codigo'],
					"descricao"	=>	utf8_encode($arr['descricao'])
					);
		}
		
		return $retorno;
	}
	
	public function limparResumo(){
	    
	    $start = $this->microtime_float(true);
	
	    pg_query($this->conn, "BEGIN");
	
	
	    $this->delTodasPrevisoes();
	
	    if ($this->debug) {
	        $time_end = $this->microtime_float(true);
	        echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
	    }
	
	    pg_query($this->conn, "END");
	}
	
	public function atualizaValorPrevisaoFaturamento(){
		$sql = "SELECT * FROM atualiza_valor_previsao_faturamento('{890,891}')";
		$this->send_query($sql);
    }
	
   /*
     * Metodo para atalizar a data de cancelamento da execucao
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     */
    function atualizarExecucaoFaturamento() {

    	$sql = "UPDATE
    				execucao_faturamento
    			SET
    				exfcancelado = TRUE,
    				exfdt_termino = NOW()
    			WHERE
    				exfoid = (
    							SELECT 
    								exfoid 
    							FROM 
    								execucao_faturamento 
								WHERE 
									exftipo_processo = 'R' 
								AND 
									exfdt_termino IS NULL 
								ORDER BY 
									exfdt_inicio DESC
								LIMIT 1
							)";
    	if (!pg_query($this->connSecundaria, $sql)) {
    		return false;
    	}
    	
    	pg_query($this->conn, 'ROLLBACK');
    	
    	return true;
    }

    /*
     * Metodo para verificar se a conexao esta ocupada
     */
    public function send_query($sql){
    	return pg_query($this->conn, $sql);
    }
    
}