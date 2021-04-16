<?php

//STI 81231 - Crédito Futuro
require_once _MODULEDIR_ ."/Financas/DAO/FinCreditoFuturoDAO.php";

//carrega BO
require_once _MODULEDIR_ ."/Financas/Action/CreditoFuturo.php";

//carrega VO
require_once _MODULEDIR_ ."/Financas/VO/CreditoFuturoVO.php";
require_once _MODULEDIR_ ."/Financas/VO/CreditoFuturoParcelaVO.php";
require_once _MODULEDIR_ ."/Financas/VO/CreditoFuturoMotivoCreditoVO.php";
require_once _MODULEDIR_ ."/Financas/VO/CreditoFuturoHistoricoVO.php";


//ORGMKTOTVS-792 - [ERP - Inativar a geração de títulos no faturamento unificado]
require_once _MODULEDIR_ . 'core/infra/autoload.php';
use module\Parametro\ParametroIntegracaoTotvs;
define('INTEGRACAO_TOTVS_ATIVA', ParametroIntegracaoTotvs::getIntegracaoTotvsAtiva());
//FIM - ORGMKTOTVS-792 - [ERP - Inativar a geração de títulos no faturamento unificado]



//STI 81231 - Crédito Futuro

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
		$creditoFuturoDAO =  new FinCreditoFuturoDAO($this->conn);
		$this->bo = new CreditoFuturo($creditoFuturoDAO);
		
		global $dbstring;

		$this->connSecundaria = pg_connect($dbstring);
		
		if($_REQUEST['debug']  == 3 ){
			print_r($dbstring);
			#exit($this->connSecundaria);
		}
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
		
		if (!empty($this->cliente)){ 		
			$clausulasCli .= " AND clinome ILIKE('%$this->cliente%')";
		}
		
		if (!empty($this->tipo_contrato)){
			$clausulasCon .= " AND conno_tipo=$this->tipo_contrato";
		}
		
		if (!empty($this->contrato)){
			$clausulasCon .= " AND connumero=$this->contrato";
		}
		
		if (!empty($this->placa)){
			$clausulasCon .= " AND conveioid IN (SELECT veioid FROM veiculo WHERE veiplaca='$this->placa')";
		}
		
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
		
		if (!empty($this->cliente)){
			$this->filtros .= " AND clinome ILIKE('%$this->cliente%')";
		}
			
		if (!empty($this->tipo_contrato)){
			$this->filtros .= " AND tpcoid = $this->tipo_contrato";
		}
		
		if (!empty($this->contrato)){
			$this->filtros .= " AND connumero = $this->contrato";
		}
		
		if (!empty($this->placa)){
			$this->filtros .= " AND conveioid IN (SELECT veioid FROM veiculo WHERE veiplaca='$this->placa')";
	}
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
		
		//STI - 81607  - 81608
		$this->aplicarReajusteIgpmInpc();
		
		// STI 86605
		$this->pesquisarProRataLocacoesEquipamentos();
		$this->pesquisarProRataLocacoesAcessorios();

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
		
		if ($this->debug) {
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
		
		pg_query($this->conn, "END");
		
		// STI 83078
		$this->aplicarDescontoPrevisaoFaturamento();
		
		
		$this->delZerados();
		
	}
	
	/**
	 * STI 81607 - 81608 
	 * 
	 * Aplica reajuste de IGPM ou INPC 
	 * 
	 * @return boolean
	 */
	public function aplicarReajusteIgpmInpc(){
		
		$data = $this->data;
		$dia = substr($data,0,2);
		$mes = substr($data,3,2);
		$ano = substr($data,6,4);
		
		# mes "ano passado"
		$anopassado = date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $ano));
		# mes "6 meses atrás"
		$semestre = date("d/m/Y", mktime(0, 0, 0, $mes-6, $dia, $ano));
		# mes "1 mês atrás"
		$mes = date("d/m/Y", mktime(0, 0, 0, $mes-1, $dia, $ano));
		
		// Ajustar valores pelo IGPM
		$igpmvl_referencia = $this->valorReferenciaIGPM();
		
		// Ajustar valores pelo INPC
		$inpcvl_referencia = $this->valorReferenciaINPC();
		
		if ($igpmvl_referencia || $inpcvl_referencia) {

			$igpm = 1 + ($igpmvl_referencia / 100);
			$inpc = 1 + ($inpcvl_referencia / 100);

			$this->reajustarIgpmInpc($igpm, $inpc, $anopassado, $semestre, $mes);
		}
		
		return true;
		
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
		}else{
			$this->MensagemLog("Deletado a previsao");
		}
	}
	
	
	public function delTodasPrevisoes(){

		//deleta todos os registros e reinicia o id da tabela
		$sql = " DELETE FROM previsao_faturamento;
				 SELECT setval('previsao_faturamento_prefoid_seq', 1);  ";
				
		if (!pg_query($this->conn, $sql)) {
			
			//pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao limpar o resumo",1);
			
		}else{
			
			$this->MensagemLog("Deletado todas as previsoes");
		}
	}

	
	public function delZerados() {
		
		$sqlDel = "
				DELETE FROM
					previsao_faturamento
				WHERE
					prefvalor <= 0.01";
		
		if (!$this->send_query($sqlDel)) {
			//pg_query($this->conn, "ROLLBACK"); // Função foi movida para depois do END
			throw new exception("Falha ao deletar valores zerados",1);
		}else{
			$this->MensagemLog("Deletados todos os valores zerados");
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
		}else{
			$this->MensagemLog("Deletado todos os acionamentos indevidos");
		}
	}
	
	
	
	/**
	 * Aplica descontos ou isenções de acordo a tabela parametros_faturamento 
	 * 
	 * 83078 - Melhorias Parâmetros do Faturamento 
	 * @throws exception
	 */
	public function aplicarDescontoPrevisaoFaturamento(){
	
		$sql = " WITH  prev AS (
		
				       SELECT prefoid,valor  FROM (
											         SELECT prev.prefoid,
										   CASE WHEN pf.parfisento IS TRUE AND prev.prefdt_referencia::DATE BETWEEN pf.parfdt_ini_cobranca AND pf.parfdt_fin_cobranca 
											    THEN  0 
										   ELSE 
										      CASE WHEN pf.parfdesconto IS NOT NULL AND  prev.prefdt_referencia::DATE BETWEEN pf.parfdt_ini_desconto AND pf.parfdt_fin_desconto 
												   THEN
													  CASE WHEN pf.parfvl_cobrado IS NOT NULL AND pf.parfvl_cobrado <> 0 AND prev.prefdt_referencia::date BETWEEN pf.parfdt_ini_valor AND pf.parfdt_fin_valor
													       THEN 
														     ROUND(pf.parfvl_cobrado::NUMERIC - pf.parfvl_cobrado::NUMERIC * COALESCE((pf.parfdesconto)::NUMERIC, 0) / 100, 2)
													       ELSE
														     ROUND(prev.prefvalor::NUMERIC - prev.prefvalor::NUMERIC * COALESCE((pf.parfdesconto)::NUMERIC, 0) / 100, 2)
													  END
												ELSE
													CASE WHEN pf.parfvl_cobrado IS NOT NULL AND pf.parfvl_cobrado <> 0 AND prev.prefdt_referencia::date BETWEEN pf.parfdt_ini_valor AND pf.parfdt_fin_valor
													     THEN 
														   ROUND(pf.parfvl_cobrado::NUMERIC, 2)
													     ELSE
														   ROUND(prev.prefvalor::NUMERIC, 2)
													     END
												    END
											END AS valor, 
										
											RANK() OVER ( PARTITION BY connumero,prev.prefobroid ORDER BY 
											    CASE
											      WHEN parfnivel=1 THEN 1
											      WHEN parfnivel=2 THEN 2
											      WHEN parfnivel=3 THEN 3
											    END
											) AS rank
										 
							            FROM previsao_faturamento AS prev
								  INNER JOIN contrato AS con ON con.connumero = prev.prefconnumero
								  INNER JOIN obrigacao_financeira ON obroid = prev.prefobroid
								  INNER JOIN parametros_faturamento pf ON ( (pf.parfnivel = 1 AND prev.prefconnumero = pf.parfconoid AND (pf.parfobroid = prev.prefobroid OR prev.prefobroid = ANY(pf.parfobroid_multiplo))
																			) OR 
										                                    (pf.parfnivel = 3 AND conno_tipo = pf.parftpcoid AND (pf.parfobroid = prev.prefobroid OR prev.prefobroid = ANY(pf.parfobroid_multiplo))
																			) OR 
										                                    (pf.parfnivel = 2 AND prev.prefclioid = pf.parfclioid AND (pf.parfobroid = prev.prefobroid OR prev.prefobroid = ANY(pf.parfobroid_multiplo))
																			))  
										
									   WHERE pf.parfdt_exclusao IS NULL
						   ) x
				         WHERE rank =1 )
		
				
				UPDATE previsao_faturamento as prev_update
				   SET prefcontrol_desc = 't', prefvalor = prev.valor
				  FROM prev
				 WHERE prev_update.prefcontrol_desc = 'f' AND prev.prefoid = prev_update.prefoid ;
								
				";
		
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        aplicarDescontoPrevisaoFaturamento ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM aplicarDescontoPrevisaoFaturamento ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		if (!pg_query($this->conn, $sql)) {
			
			throw new exception("Falha ao aplicar desconto(s) na previsão de faturamento.",1);
			
		}else{
			$this->MensagemLog("Aplicado desconto(s) na previsão de faturamento");
		}
		
		return true;

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
				
		 			ROUND(COALESCE((COALESCE(cpag.cpagvl_servico, 0) / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - condt_ini_vigencia::date), 0) ,2) AS valor, 
				
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
																				and obrdt_exclusao is null
																				AND msuboid = con.conmsuboid LIMIT 1)) > 0 THEN
																			  (SELECT obroid
																			   FROM motivo_substituicao
																			   JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
																			   AND msubeqcoid = obreqcoid
																			   WHERE msubeqcoid IS NOT NULL
																			   and obrdt_exclusao is null
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
                    AND 
                        (
                            con.conequoid > 0
                        OR
                            con.consem_equipamento = TRUE
                        )
					AND con.connumero NOT IN (	
											SELECT
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
													and obrdt_exclusao is null
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
				
												AND nfl2.nfldt_cancelamento IS NULL
										) 
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
					-- Condicao STI 86605
					AND obrofgoid IN (2,11,12,24)
					
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)
		";
		
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        pesquisarProRataLocacoesEquipamentos ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataLocacoesEquipamentos ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pro-Rata Locacoes Equipamentos",1);
		}else{
			$this->MensagemLog("Atualizado Pro-Rata Locacoes Equipamentos");
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarProRataLocacoesEquipamentos: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}
	
	
	
	public function pesquisarLocacoesEquipamentos() {
	
		$time_start = $this->microtime_float(true);
		
		$sql = "DROP TABLE IF EXISTS tabela_temp;

				CREATE TEMPORARY TABLE tabela_temp AS
				SELECT
					DISTINCT con.connumero AS contrato,
					CASE
					WHEN (
                            (
                                SELECT 
                                    count(msuboid) AS COUNT
                                FROM
                                    motivo_substituicao
                                INNER JOIN 
                                    obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
                                AND 
                                    msubeqcoid = obreqcoid
                                WHERE 
                                    msubeqcoid IS NOT NULL
								and obrdt_exclusao is null
                                AND 
                                    msuboid = con.conmsuboid LIMIT 1
                            )
                        ) > 0 
                        THEN
                            (
                                SELECT 
                                    obroid
                                FROM 
                                    motivo_substituicao
                                INNER JOIN 
                                    obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
                                AND 
                                    msubeqcoid = obreqcoid
                                WHERE 
                                    msubeqcoid IS NOT NULL
								and obrdt_exclusao is null
                                AND 
                                    msuboid = con.conmsuboid LIMIT 1
                            )
					ELSE 
						CASE
                                WHEN (	
                                    (
                                        SELECT 
                                            COUNT(msuboid) AS COUNT
                                        FROM 
                                            motivo_substituicao
                                        WHERE 
                                            msuboid = con.conmsuboid
                                        AND 
                                            msubeqcoid IS NULL
                                        AND 
                                            msubtrans_titularidade IS TRUE LIMIT 1
                                    )  
                                ) > 0 
                                THEN 
							25
						ELSE 
							eqc.eqcobroid
						END
					END AS obroid,

					-- RN56 - Cliente Pagador
					CASE 
                        WHEN 
                            tpc.tpccliente_pagador_locacao IS NULL 
                        THEN 
						conclioid 
					ELSE
						tpc.tpccliente_pagador_locacao
					END AS cliente,

					ROUND(	
                        CASE 
                            WHEN 
                                COALESCE(cpag.cpagvl_servico, 0::numeric) > 0::numeric 
                            THEN 
								-- Valor com desconto (Se completou vigência)
								CASE 
                                    WHEN (
                                        '$this->data'::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) 
                                    THEN
									COALESCE(cpag.cpagvl_servico, 0) - (COALESCE(cpag.cpagvl_servico,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
								ELSE
									COALESCE(cpag.cpagvl_servico, 0)
								END
							ELSE 
								-- Valor com desconto (Se completou vigência)
								CASE 
                                    WHEN (
                                            '$this->data'::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) 
                                    THEN
									COALESCE(cpag.cpaghabilitacao, 0) - (COALESCE(cpag.cpaghabilitacao,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
								ELSE
									COALESCE(cpag.cpaghabilitacao, 0)
								END
                        END, 2
                    ) AS valor,

					'$this->data'::date AS data_referencia,

					'L' as tipo_obrigacao,

					condt_inicio_parcela,
					cpvoid

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
                                                                                    (
                                                                                        SELECT 
                                                                                            count(msuboid) AS COUNT
                                                                                        FROM 
                                                                                            motivo_substituicao
                                                                                        INNER JOIN 
                                                                                            obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
                                                                                        AND 
                                                                                            msubeqcoid = obreqcoid
                                                                                        WHERE 
                                                                                            msubeqcoid IS NOT NULL
																						and obrdt_exclusao is null
                                                                                        AND 
                                                                                            msuboid = con.conmsuboid LIMIT 1
                                                                                    )
                                                                                ) > 0 
                                                                            THEN
                                                                                (
                                                                                    SELECT 
                                                                                        obroid
                                                                                    FROM 
                                                                                        motivo_substituicao
                                                                                    INNER JOIN 
                                                                                        obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig
                                                                                    AND 
                                                                                        msubeqcoid = obreqcoid
                                                                                    WHERE 
                                                                                        msubeqcoid IS NOT NULL
																					and obrdt_exclusao is null
                                                                                    AND 
                                                                                        msuboid = con.conmsuboid LIMIT 1
                                                                                )
																		ELSE 
																			CASE
                                                                                    WHEN (	
                                                                                            (
                                                                                                SELECT 
                                                                                                    COUNT(msuboid) AS COUNT
																					            FROM 
                                                                                                    motivo_substituicao
																					            WHERE 
                                                                                                    msuboid = con.conmsuboid
                                                                                                AND 
                                                                                                    msubeqcoid IS NULL
                                                                                                AND 
                                                                                                    msubtrans_titularidade IS TRUE LIMIT 1
                                                                                            )
                                                                                        ) > 0
                                                                                    THEN 
																				25
																			ELSE 
																				eqc.eqcobroid
																			END
																		END
				WHERE TRUE

					$this->filtros

                    AND conno_tipo != 844
					AND con.conmodalidade = 'L' 
                AND 
                    (
                        con.conequoid > 0
                    OR
                        con.consem_equipamento = TRUE
                    )
					AND con.connumero NOT IN (	SELECT
										nfi2.nficonoid
									FROM
										nota_fiscal_item nfi2
                                            INNER JOIN 
                                                nota_fiscal nfl2 ON (nfl2.nflno_numero = nfi2.nfino_numero AND nfl2.nflserie = nfi2.nfiserie)
									WHERE 
										nfi2.nficonoid = con.connumero
                                            AND 
                                                nfi2.nfiobroid = obr.obroid
                                            AND 
                                                nfi2.nfidt_referencia = '$this->data'
                                            AND nfl2.nfldt_cancelamento IS NULL
                                        ) 
				AND (	
                        con.condt_exclusao IS NULL 
                    OR  
                        con.condt_exclusao IS NOT NULL 
                    AND 
                        to_char(con.condt_exclusao, 'mm/yyyy'::text) <> to_char(con.condt_ini_vigencia, 'mm/yyyy'::text)
                    ) 
					AND con.consem_custo IS FALSE 
					AND con.concsioid = 1 
					AND (con.conmsuboid IS NULL OR con.conmsuboid <> 59)
				AND (	
                        tpc.tpcseguradora IS NOT TRUE 
                    OR 
                        tpc.tpccorretora IS TRUE 
                    AND (	
                            SELECT 
                                msubeqcoid
                            FROM 
                                motivo_substituicao
                            WHERE 
                                msuboid = con.conmsuboid
                    ) > 0
                ) 
				AND (	
                        tpc.tpccorretora IS NOT TRUE 
					OR 
                        tpc.tpccorretora IS TRUE 
                    AND 
                        ( 	
                            SELECT 
                                msubeqcoid
                            FROM 
                                motivo_substituicao
                            WHERE 
                                msuboid = con.conmsuboid
                    ) > 0
                )
				AND ( 
                        coneqcupgoid IS NULL 
                    OR (
                            coneqcupgoid > 0  
                        AND 
                            connumero IN (
                                            SELECT 
                                                cmiconoid 
                                            FROM 
                                                comissao_instalacao
                                            INNER JOIN 
                                                os_tipo_item on otioid = cmiotioid
                                            INNER JOIN 
                                                os_tipo on ostoid = otiostoid
                                            WHERE 
                                                cmiconoid = connumero
				                            AND 
                                                cmidata BETWEEN condt_substituicao AND ('$this->data'::date + '1 month'::interval)::date - 1
				                            AND 
                                                ostoid = 1 -- ID Instalação
				                            AND 
                                                cmiexclusao IS NULL
				                            AND 
                                                otioid IN (209,77)
                                        )
                    )
                )

                AND (
                        (
                            ( con.condt_ini_vigencia > '01/02/2013'::date OR con.condt_substituicao > '01/01/2009'::date OR con.condt_inicio_parcela > '01/02/2013'::date )
                        AND
                            (
                                NOT EXISTS( 
                                    SELECT 
								1
							FROM 
                                        nota_fiscal_item AS nfi3
                                    INNER JOIN 
                                        nota_fiscal AS nfl3 ON nfl3.nflno_numero = nfi3.nfino_numero AND nfl3.nflserie = nfi3.nfiserie
							WHERE 
								(nfl3.nflserie = ANY (ARRAY['F'::bpchar, 'SL'::bpchar])) 
                                    AND 
                                        nfi3.nficonoid = con.connumero
                                    AND 
                                        nfi3.nfiobroid = obr.obroid
                                    AND 
                                        nfl3.nfldt_cancelamento IS NULL
                                )
                            )
                        )
                    OR
                        -- Condicao adicionada para reativacao de parcelas de locacao
                        (EXISTS (SELECT 1 FROM cond_pgto_venda WHERE cpvoid = cpagcpvoid AND cpvdescricao = '999 x' AND cpvexclusao IS NULL) )
                    )
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
											AND nfl4.nfldt_cancelamento IS NULL
											AND nfi4.nfidt_inclusao >= con.condt_inicio_parcela
											)
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND (('$this->data'::date - con.condt_ini_vigencia::date) > 0);


				INSERT INTO previsao_faturamento
				(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
					,prefparcela
					,prefcpvoid
				)
				SELECT 
					DISTINCT 	
					tp.contrato,
					tp.obroid,
					tp.cliente,
					tp.valor,
					tp.data_referencia,
					tp.tipo_obrigacao,
					(
                        SELECT 
                            CASE 
                                WHEN 
                                    (max(nfiparcela) > 0) IS TRUE 
                                THEN
                                    max(nfiparcela) + 1
                                ELSE
                                    1
                            END AS parcela_atual
                        FROM
                            nota_fiscal_item
                        INNER JOIN 
                                nota_fiscal ON nflno_numero = nfino_numero AND nfiserie = nflserie
                        WHERE
                            nficonoid = tp.contrato
                            AND nfidt_inclusao >= condt_inicio_parcela::date
                            AND nfldt_cancelamento IS NULL
                            AND nfiobroid = tp.obroid
                    ) AS parcela_atual,
					tp.cpvoid					
				FROM 
					tabela_temp tp
				LEFT JOIN 
					nota_fiscal_item ON tp.contrato = nficonoid AND nfidt_inclusao >= condt_inicio_parcela::date;";
		
		 
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        pesquisarLocacoesEquipamentos ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		 
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarLocacoesEquipamentos ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
        if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Locacoes Equipamentos",1);
		}else{
			$this->MensagemLog("Atualizado  Locacoes Equipamentos");
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarLocacoesEquipamentos: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}

	/**
	 * STI 86605 - Pro-rata de locacao de acessorios
	 * @return [type] [description]
	 */
	public function pesquisarProRataLocacoesAcessorios() {

		$time_start = $this->microtime_float(true);

		$sql = " INSERT INTO previsao_faturamento
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
					
					CASE WHEN tpc.tpccliente_pagador_locacao IS NULL THEN 
						con.conclioid 
					ELSE
						tpc.tpccliente_pagador_locacao
					END AS cliente, 
					
					ROUND(COALESCE((COALESCE(consvalor, 0) / (':data_vigencia'::date - (':data_vigencia'::date - '1 month'::interval)::date)) * (':data_vigencia'::date - condt_ini_vigencia::date), 0) ,2) AS valor,
					
					':data_vigencia'::date AS data_referencia,
					
					'L'::CHAR as tipo_obrigacao				        
				FROM
					contrato con
					INNER JOIN contrato_pagamento cpag ON cpag.cpagconoid = con.connumero
					INNER JOIN tipo_contrato tpc        ON tpc.tpcoid = con.conno_tipo
					INNER JOIN equipamento_classe eqc   ON eqc.eqcoid = con.coneqcoid
					INNER JOIN clientes cli             ON cli.clioid = con.conclioid
					INNER JOIN veiculo vei              ON vei.veioid = con.conveioid
					INNER JOIN contrato_servico cons    ON cons.consconoid = con.connumero
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cons.consobroid
					
				WHERE
					TRUE
					$this->filtros
				AND 
					conno_tipo != 844
				AND 
					con.consem_custo IS FALSE 
				AND 
					con.concsioid = 1 
				AND 
					con.coneqcupgoid IS NULL 
				AND 
					con.condt_ini_vigencia::date < ':data_vigencia'::date
				AND 
					(':data_vigencia'::date - condt_ini_vigencia::date) > 0

				AND NOT EXISTS (SELECT 1
						FROM nota_fiscal_item nfi
						INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
						WHERE nfi.nficonoid = con.connumero
						AND nfi.nfiobroid = obr.obroid
						AND nfi.nfidt_referencia = ':data_vigencia'
						AND nfl.nfldt_cancelamento IS NULL
						
						UNION
						
						SELECT 1
						FROM nota_fiscal_item nfi
						INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
						WHERE nfi.nficonoid = con.connumero_antigo
						AND nfi.nfiobroid = obr.obroid
						AND nfi.nfidt_referencia = ':data_vigencia'
						AND nfl.nfldt_cancelamento IS NULL
						)
				AND NOT EXISTS (
						SELECT 
							1
						FROM 
							nota_fiscal_item nfi3
						INNER JOIN
							nota_fiscal nfl3 ON (nfl3.nflno_numero = nfi3.nfino_numero AND nfl3.nflserie = nfi3.nfiserie)
						WHERE 
						nfl3.nfldt_cancelamento IS NULL
						AND (nfl3.nflserie = ANY (ARRAY['F', 'SL']))                 
						AND nfi3.nficonoid = cons.consconoid
						AND nfi3.nfiobroid = cons.consobroid
						AND nfi3.nfidt_inclusao >= con.condt_inicio_parcela

						UNION

						SELECT 
							1
						FROM 
							nota_fiscal_item nfi3
						INNER JOIN
							nota_fiscal nfl3 ON (nfl3.nflno_numero = nfi3.nfino_numero AND nfl3.nflserie = nfi3.nfiserie)
						WHERE 
						nfl3.nfldt_cancelamento IS NULL
						AND (nfl3.nflserie = ANY (ARRAY['F', 'SL']))                 
						AND nfi3.nficonoid = con.connumero_antigo
						AND nfi3.nfiobroid = cons.consobroid
						AND nfi3.nfidt_inclusao >= con.condt_inicio_parcela
						)

				AND tpc.tpcseguradora IS FALSE
				AND cons.conscpvoid IS NOT NULL ---novo
				AND (cons.consinstalacao > '01/02/2013'::date OR con.condt_inicio_parcela > '01/02/2013'::date)
				AND cons.consiexclusao IS NULL 
				AND cons.consinstalacao IS NOT NULL 
				AND cons.conssituacao = 'L' 
				-- Condicao STI 86605
				AND obrofgoid IN (15,11,12,24)
				AND (':data_vigencia'::date - con.condt_ini_vigencia::date) <= (':data_vigencia'::date - (':data_vigencia'::date - '1 month'::interval)::date)

				AND EXISTS (	
					SELECT 
						1 
					FROM obrigacao_financeira obr2
					WHERE obr2.obroid = obr.obroid 
					AND obrprorata > 0
				)
					
				----------------------------------------------------------------------------------
				AND (
					--numero parcelas
					(
					SELECT cpvparcela 
					FROM cond_pgto_venda 
					WHERE cpvoid = cons.conscpvoid
					)
					-- numero de notas
					> 
					(	
						(SELECT count(distinct nflno_numero)
						FROM nota_fiscal_item nfi
						INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
						WHERE (nfi.nficonoid = con.connumero OR nfi.nficonoid = con.connumero_antigo)
						AND nfi.nfiobroid = obr.obroid
						AND nfl.nflserie = 'A'
						AND nfl.nfldt_cancelamento IS NULL
						AND nfi.nfidt_inclusao >= con.condt_inicio_parcela)
					)
				    )
					AND obr.obroid <> 90;";

		$sql = str_replace(':data_vigencia', $this->data, $sql);

		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        pesquisarProRataLocacoesAcessorios ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		 
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataLocacoesAcessorios ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;

		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pro-Rata Locacoes de Acessorios",1);
		}else{
			$this->MensagemLog("Atualizado Pro-Rata Locacoes de Acessorios");
		}
		
		if ($this->debug) {
			echo "\r\n --pesquisarProRataLocacoesAcessorios: \r\n\r\n" .$sql. "</pre>";
			$time_end = $this->microtime_float(true);
			echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
		}
	}

	public function pesquisarLocacoesAcessorios() {
		
		$time_start = $this->microtime_float(true);
		
		$sql = "

				DROP TABLE IF EXISTS tabela_temp_acessorios;

				CREATE TEMPORARY TABLE tabela_temp_acessorios AS
				SELECT	
				        con.connumero AS contrato,
				        obr.obroid AS obrigacao_financeira,
				        CASE WHEN tpc.tpccliente_pagador_locacao IS NULL THEN 
				                con.conclioid 
				        ELSE
				                tpc.tpccliente_pagador_locacao
				        END AS cliente, 
				        ROUND(COALESCE(cons.consvalor, 0), 2) AS valor,
				        '$this->data'::date AS data_referencia,
				        'L'::CHAR as tipo_obrigacao,
				        consoid,
				        condt_inicio_parcela,
				        conscpvoid  				        
				FROM
				        contrato con
				        INNER JOIN clientes cli             ON cli.clioid = con.conclioid
				        INNER JOIN veiculo vei              ON vei.veioid = con.conveioid
				        INNER JOIN tipo_contrato tpc        ON tpc.tpcoid = con.conno_tipo
				        INNER JOIN contrato_servico cons    ON cons.consconoid = con.connumero
				        INNER JOIN obrigacao_financeira obr ON obr.obroid = cons.consobroid
				        INNER JOIN equipamento_classe eqc   ON eqc.eqcoid = con.coneqcoid
				WHERE
				        TRUE
		        $this->filtros
				AND 
					conno_tipo != 844
				AND 
					con.consem_custo IS FALSE 
				AND 
					con.concsioid = 1 
				AND 
					con.coneqcupgoid IS NULL 
				AND 
					con.condt_ini_vigencia::date < '$this->data'
				AND 
					('$this->data' - condt_ini_vigencia::date) > 0

				AND NOT EXISTS (SELECT 1
				                FROM nota_fiscal_item nfi
				                INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
				                WHERE nfi.nficonoid = con.connumero
				                AND nfi.nfiobroid = obr.obroid
				                AND nfi.nfidt_referencia = '$this->data'
				                AND nfl.nfldt_cancelamento IS NULL
				                
				                UNION
				                
				                SELECT 1
				                FROM nota_fiscal_item nfi
				                INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
				                WHERE nfi.nficonoid = con.connumero_antigo
				                AND nfi.nfiobroid = obr.obroid
				                AND nfi.nfidt_referencia = '$this->data'
				                AND nfl.nfldt_cancelamento IS NULL
				                )
                AND (
                        (
				            NOT EXISTS (
				                SELECT 
				                        1
				                FROM 
				                        nota_fiscal_item nfi3
				                INNER JOIN
				                        nota_fiscal nfl3 ON (nfl3.nflno_numero = nfi3.nfino_numero AND nfl3.nflserie = nfi3.nfiserie)
				                WHERE 
				                nfl3.nfldt_cancelamento IS NULL
				                AND (nfl3.nflserie = ANY (ARRAY['F', 'SL']))                 
				                AND nfi3.nficonoid = cons.consconoid
				                AND nfi3.nfiobroid = cons.consobroid
				                AND nfi3.nfidt_inclusao >= con.condt_inicio_parcela

				                UNION

				                SELECT 
				                        1
				                FROM 
				                        nota_fiscal_item nfi3
				                INNER JOIN
				                        nota_fiscal nfl3 ON (nfl3.nflno_numero = nfi3.nfino_numero AND nfl3.nflserie = nfi3.nfiserie)
				                WHERE 
				                nfl3.nfldt_cancelamento IS NULL
				                AND (nfl3.nflserie = ANY (ARRAY['F', 'SL']))                 
				                AND nfi3.nficonoid = con.connumero_antigo
				                AND nfi3.nfiobroid = cons.consobroid
				                AND nfi3.nfidt_inclusao >= con.condt_inicio_parcela
				                )
                            AND 
                            (
                                cons.consinstalacao > '01/02/2013'::date OR con.condt_inicio_parcela > '01/02/2013'::date
                            )
                        )
                        OR
                            -- Condicao adicionada para reativacao de parcelas de locacao
                            (EXISTS (SELECT 1 FROM cond_pgto_venda WHERE cpvoid = conscpvoid AND cpvdescricao = '999 x' AND cpvexclusao IS NULL) )
                    )
				AND tpc.tpcseguradora IS FALSE
				AND cons.conscpvoid IS NOT NULL ---novo
				AND cons.consiexclusao IS NULL 
				AND cons.consinstalacao IS NOT NULL 
				AND cons.conssituacao = 'L' 
				----------------------------------------------------------------------------------
				AND (
				        --numero parcelas
				        (
				        SELECT cpvparcela 
				        FROM cond_pgto_venda 
				        WHERE cpvoid = cons.conscpvoid
				        )
				        -- mumero de notas
				        > 
				        (	
				                (SELECT count(distinct nflno_numero)
				                FROM nota_fiscal_item nfi
				                INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
				                WHERE (nfi.nficonoid = con.connumero OR nfi.nficonoid = con.connumero_antigo)
				                AND nfi.nfiobroid = obr.obroid
				                AND nfl.nflserie = 'A'
				                AND nfl.nfldt_cancelamento IS NULL
				                AND nfi.nfidt_inclusao >= con.condt_inicio_parcela)
				        )
				    )
					AND obr.obroid <> 90;

				INSERT INTO previsao_faturamento
				(
					prefconnumero
					,prefobroid
					,prefclioid
					,prefvalor
					,prefdt_referencia
					,preftipo_obrigacao
					,prefparcela
					,prefconsoid
					,prefcpvoid
				)
                SELECT DISTINCT
                    tp.contrato,
                    tp.obrigacao_financeira,
                    tp.cliente,
                    tp.valor,
                    tp.data_referencia,
                    tp.tipo_obrigacao,
                    CASE
                        -- Calculo da parcela para acessorio do contrato
                        WHEN (SELECT DISTINCT TRUE FROM nota_fiscal_item INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nfiserie = nflserie WHERE nfldt_cancelamento IS NULL AND contrato_servico.constadoid IS NULL AND nfidt_inclusao >= condt_inicio_parcela::date AND tp.consoid = nficonsoid) IS TRUE THEN
                            (SELECT COALESCE(MAX(nfiparcela), 0) + 1 FROM nota_fiscal_item INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nfiserie = nflserie WHERE nfldt_cancelamento IS NULL AND nfidt_inclusao >= condt_inicio_parcela::date AND tp.consoid = nficonsoid)

                        -- Calculo da parcela para acessorio do termo aditivo
                        WHEN (SELECT DISTINCT TRUE FROM nota_fiscal_item INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nfiserie = nflserie WHERE nfldt_cancelamento IS NULL AND constadoid IS NOT NULL AND tp.consoid = nficonsoid) IS TRUE THEN
                            (SELECT COALESCE(MAX(nfiparcela), 0) + 1 FROM nota_fiscal_item INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nfiserie = nflserie WHERE nfldt_cancelamento IS NULL AND constadoid IS NOT NULL AND tp.consoid = nficonsoid)
                        
                        ELSE
                            1
                    END AS parcela_atual,
                    tp.consoid,
                    tp.conscpvoid
                FROM
                    tabela_temp_acessorios tp
                    INNER JOIN contrato_servico ON contrato_servico.consoid = tp.consoid";
		

		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
	echo 'pesquisarLocacoesAcessorios';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
			
		print_r($sql);

		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarLocacoesAcessorios ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		

		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Locacoes Acessorios",1);
		}else{
			$this->MensagemLog("Atualizado  Locacoes de Acessorios");
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

		            ROUND(COALESCE((COALESCE(cofvl_obrigacao, 0) / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - condt_ini_vigencia::date), 0) ,2) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
					AND 
                        (
                            con.conequoid > 0
                        OR
                            con.consem_equipamento = TRUE
                        )
					AND condt_exclusao IS NULL
					AND cofdt_termino IS NULL
					AND condt_ini_vigencia::date < '$this->data'::date
					AND condt_ini_vigencia is not null
					AND cofobroid = 1
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";


		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarProRataMonitoramentoVeiculos';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;

		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataMonitoramentoVeiculos ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		

		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Monitoramento Veiculos - pesquisarProRataMonitoramentoVeiculos",1);
		}else{
			$this->MensagemLog("Atualizado  Monitoramento Veiculos");
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

		            ROUND(cofvl_obrigacao ,2) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844) 
					AND NOT EXISTS (SELECT 1
									FROM nota_fiscal_item 
									WHERE nficonoid = con.connumero
									AND nfiobroid = obr.obroid and nfidt_referencia is not null
									AND nfidt_referencia = '$this->data' and exists (	SELECT 1
																						FROM nota_fiscal
																						where (nflno_numero = nfino_numero AND nflserie = nfiserie)
																						AND nfldt_cancelamento IS NULL))
					AND tpc.tpcgera_faturamento IS TRUE 
					AND 
                        (	
                            conequoid > 0 
						OR 
                            EXISTS (	SELECT 1
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
                        OR
                            consem_equipamento = TRUE
						)
					AND condt_exclusao IS NULL 
					AND ((cofobroid = 1) OR (obrofgoid = 21))
					AND cofdt_termino IS NULL 
					AND condt_ini_vigencia::date < '$this->data'::date
					AND condt_ini_vigencia is not null
					AND ('$this->data'::date - condt_ini_vigencia::date) > 0";
		
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarMonitoramentoVeiculos';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarMonitoramentoVeiculos ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		if (!$res = pg_query($this->conn,$sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Monitoramento Veiculos - pesquisarMonitoramentoVeiculos : " . $sql,1);
		}else{
			$this->MensagemLog("Atualizou Monitoramento Veiculos");
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
				    AND TO_CHAR(futdt_referencia,'MM/YYYY') = TO_CHAR('$this->data'::DATE,'MM/YYYY') 
				" . $whereBuscaFatUnificado;


		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        pesquisarTaxas ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sqlTaxas);

		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarTaxas ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		if (!$rsTaxas = pg_query($this->conn,$sqlTaxas)) {
			
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao buscar taxas",1);
		}else{
			$this->MensagemLog("Atualizado  busca de taxas");
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
			}else{
				$this->MensagemLog("Inseriu taxas");
			}

		}		

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

					COALESCE(cpt.cptvlr_desc_cliente, 0) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
		
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarServicos';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarServicos ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		if (!$res = pg_query($this->conn,$sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Servicos",1);
		}else{
			$this->MensagemLog("Atualizou Servicos");
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

				ROUND(COALESCE(cpag.cpagrenovacao,0), 2) AS valor,

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
		
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
	  echo 'pesquisarRenovacao';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
	  
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarRenovacao ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
	  
		if (!$res = pg_query($this->conn,$sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Renovacao",1);
		}else{
			$this->MensagemLog("Atualizou Renovacao");
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

				COALESCE((SELECT vmfvl_acionamento FROM valores_minimos_faturamento WHERE vmfdt_exclusao IS NULL LIMIT 1),0) AS valor,

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
				AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
        
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarAcionamentoIndevido';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
        
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarAcionamentoIndevido ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Acionamento Indevido",1);
		}else{
			$this->MensagemLog("Atualizou Acionamento Indevido");
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

					ROUND(COALESCE(vmfvl_bloqueio_solicitado ,0), 2) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
        
			
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
			echo 'pesquisarBloqueioSolicitado';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
			
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarBloqueioSolicitado ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
			
			
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Bloqueio Solicitado",1);
		}else{
			$this->MensagemLog("Atualizou Bloqueio Solicitada");
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

					ROUND(COALESCE(vmfvl_localizacao_web, 0), 2) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
 
        
        echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		  echo 'pesquisarLocalizacaoWeb';
        echo PHP_EOL.'*************************************************** '.PHP_EOL;
		  
        print_r($sql);
 
        echo PHP_EOL.' *************************************************** '.PHP_EOL;
        echo '        FIM pesquisarLocalizacaoWeb ';
        echo PHP_EOL.' *************************************************** '.PHP_EOL;
        
 
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Localizacao Web",1);
		}else{
			$this->MensagemLog("Atualizou Locacao Web");
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

				ROUND(COALESCE(vmf.vmfvl_localizacao_solicitada ,0), 2) AS valor,

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
				AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
        
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo 'pesquisarLocalizacaoSolicitada';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
        
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarLocalizacaoSolicitada ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Localizacao Solicitada",1);
		}else{
			$this->MensagemLog("Atualizou Localizacao Solicitada");
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
					
					ROUND(((obrvl_obrigacao / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date))::numeric, 2)  AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
					AND NOT EXISTS (SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE nficonoid = con.connumero
							AND (nfiobroid = 64 OR nfiobroid = 829)
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
					AND con.conmsuboid IN (59,61,63) 
					AND con.condt_exclusao IS NULL 
                    AND 
                        (
                            con.conequoid > 0
                        OR
                            con.consem_equipamento = TRUE
                        )
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		 
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarProRataVisualizacaoGSMGPS1';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		 
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataVisualizacaoGSMGPS1 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		 
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pró-rata VisualizacaoGSMGPS1",1);
		}else{
			$this->MensagemLog("Atualizou Pró-rata VisualizacaoGSMGPS1");
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
					
					ROUND(COALESCE(obrvl_obrigacao,0), 2) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
					AND NOT EXISTS (SELECT nficonoid
							FROM nota_fiscal_item
							INNER JOIN nota_fiscal ON (nflno_numero=nfino_numero AND nflserie=nfiserie)
							WHERE nficonoid = con.connumero
							AND nfiobroid = 64
							AND nfidt_referencia = '$this->data'
							AND nfldt_cancelamento IS NULL) 
					AND con.conmsuboid IN (59,61,63) 
					AND con.condt_exclusao IS NULL 
                    AND 
                        (
                            con.conequoid > 0
                        OR
                            con.consem_equipamento = TRUE
                        )
					AND con.condt_ini_vigencia::date < '$this->data'::date";
        
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarVisualizacaoGSMGPS1';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		 
		print_r($sql);
        
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarVisualizacaoGSMGPS1 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar VisualizacaoGSMGPS1",1);
		}else{
			$this->MensagemLog("Atualizou VisualizacaoGSMGPS1");
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

				ROUND(((cvvvalor / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - cadi.cadicadastro::date))::numeric, 2) AS valor,

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
				AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
				AND (cadiviaoriginal IS TRUE OR cadiviafax IS TRUE)
				AND eqcecgoid = 1 
				AND condt_exclusao IS NULL 
                AND 
                    (
                        con.conequoid > 0
                    OR
                        con.consem_equipamento = TRUE
                    )
				AND condt_ini_vigencia::date < '$this->data'::date
				AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo 'pesquisarProRataVisualizacaoGSMGPS2';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataVisualizacaoGSMGPS2 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar VisualizacaoGSMGPS2",1);
		}else{
			$this->MensagemLog("Atualizou VisualizacaoGSMGPS2");
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

				CASE 
                     WHEN EXISTS ( SELECT cvvvalor FROM contrato_valor_visualizacao  WHERE cvvconoid = con.connumero LIMIT 1) 
   		                   THEN
						ROUND(COALESCE(cvvvalor, 0), 2) 
					ELSE
						ROUND(COALESCE(obrvl_obrigacao, 0), 2)
															
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
				AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
				AND (cadiviaoriginal IS TRUE OR cadiviafax IS TRUE)
				AND eqcecgoid = 1 
				AND condt_exclusao IS NULL 
                AND 
                    (
                        con.conequoid > 0
                    OR
                        con.consem_equipamento = TRUE
                    )
				AND condt_ini_vigencia::date < '$this->data'::date";
        
	   
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
	   echo 'pesquisarVisualizacaoGSMGPS2';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
        
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarVisualizacaoGSMGPS2 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
        
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar VisualizacaoGSMGPS2",1);
		}else{
			$this->MensagemLog("Atualizou VisualizacaoGSMGPS2");
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

				ROUND((SELECT (obrvl_obrigacao / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - con.condt_ini_vigencia::date) AS parfvl_cobrado)::numeric, 2) AS valor,

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
				AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
				AND NOT EXISTS (SELECT 1
						FROM nota_fiscal_item
						INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
						WHERE nficonoid = con.connumero
						AND (nfiobroid = 65 OR nfiobroid = 830)
						AND nfidt_referencia = '$this->data'
						AND nfldt_cancelamento IS NULL) 
				AND conmsuboid in (60,62) 
				AND condt_exclusao IS NULL 
                AND 
                    (
                        con.conequoid > 0
                    OR
                        con.consem_equipamento = TRUE
                    )
				AND condt_ini_vigencia::date < '$this->data'::date
				AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo 'pesquisarProRataVisualizacaoCarga1';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataVisualizacaoCarga1 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Pró-rata Visualizacao Carga 1",1);
		}else{
			$this->MensagemLog("Atualizou  Pró-rata Visualizacao Carga 1");
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

				ROUND(COALESCE(obrvl_obrigacao,0), 2) AS valor,

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
				AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
				AND NOT EXISTS (SELECT 1
						FROM nota_fiscal_item
						INNER JOIN nota_fiscal ON (nflno_numero = nfino_numero AND nflserie = nfiserie)
						WHERE nficonoid = con.connumero
						AND nfiobroid = 65
						AND nfidt_referencia = '$this->data'
						AND nfldt_cancelamento IS NULL) 
				AND conmsuboid in (60,62) 
				AND condt_exclusao IS NULL 
                AND 
                    (
                        con.conequoid > 0
                    OR
                        con.consem_equipamento = TRUE
                    )
				AND condt_ini_vigencia::date < '$this->data'::date";
        
		
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo 'pesquisarVisualizacaoCarga1';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		
		print_r($sql);
        
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarVisualizacaoCarga1 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Visualizacao Carga 1",1);
		}else{
			$this->MensagemLog("Atualizou  Visualizacao Carga 1");
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

					ROUND(((cvvvalor / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - cadi.cadicadastro::date))::numeric, 2)  AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
					AND eqc.eqcecgoid <> 1
					AND con.condt_exclusao IS NULL 
                    AND 
                        (
                            con.conequoid > 0
                        OR
                            con.consem_equipamento = TRUE
                        )
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND gct.gctgctpoid = 2
					AND cadi.cadiexclusao is null
					AND ('$this->data'::date - con.condt_ini_vigencia::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)";
        

		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarProRataVisualizacaoCarga2';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		 
		print_r($sql);
		 
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarProRataVisualizacaoCarga2 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		 
		 
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Visualizacao Carga 2",1);
		}else{
			$this->MensagemLog("Atualizou  Visualizacao Carga 2");
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

					ROUND(COALESCE(cvvvalor,0), 2) AS valor,

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
					AND (con.conmodalidade <> 'L' OR conno_tipo <> 844)
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
					AND eqc.eqcecgoid <> 1
					AND con.condt_exclusao IS NULL 
                    AND 
                        (
                            con.conequoid > 0
                        OR
                            con.consem_equipamento = TRUE
                        )
					AND con.condt_ini_vigencia::date < '$this->data'::date
					AND gct.gctgctpoid = 2
					AND cadi.cadiexclusao is null";
        
		 
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		 echo 'pesquisarVisualizacaoCarga2';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;
		 
		print_r($sql);
		 
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM pesquisarVisualizacaoCarga2 ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		
		 
		if (!$this->send_query($sql)) {
			pg_query($this->conn, "ROLLBACK");
			throw new exception("Falha ao atualizar Visualizacao Carga 2",1);
		}else{
			$this->MensagemLog("Atualizou  Visualizacao Carga 2");
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
					WHEN clio.clioobroid = 2509 THEN
						2519	
					ELSE
						848
					END as obrigacao_financeira, 

					cli.clioid AS cliente, 

					0 AS contrato, 

					ROUND(COALESCE(((cliovl_obrigacao / ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)) * ('$this->data'::date - clio.cliodt_inicio::date)::numeric)::numeric, 0), 2) AS valor, 

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
									AND (nfiobroid = 828 OR nfiobroid = 406 OR nfiobroid = 2509)
									AND nfidt_referencia = '$this->data'
									AND nfldt_cancelamento IS NULL) 
					AND clio.cliocortesia IS FALSE
					AND clio.cliodt_inicio >= '01/01/2009'
					AND clio.clioobroid IN (466, 406, 50, 2509)
					-- AND EXISTS (SELECT 1 FROM tipo_contrato WHERE tpcoid = 0 AND tpcgera_faturamento IS TRUE)
					-- AND ('$this->data'::date - clio.cliodt_inicio::date) <= ('$this->data'::date - ('$this->data'::date - '1 month'::interval)::date)
					";
			
			
			echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
			echo 'pesquisarProRataServicoSoftware';
			echo PHP_EOL.'*************************************************** '.PHP_EOL;
			
			print_r($sql);
			
			echo PHP_EOL.' *************************************************** '.PHP_EOL;
			echo '        FIM pesquisarProRataServicoSoftware ';
			echo PHP_EOL.' *************************************************** '.PHP_EOL;
			
			
			if (!$this->send_query($sql)) {
				pg_query($this->conn, "ROLLBACK");
				throw new exception("Falha ao atualizar Servicos Software",1);
			}else{
				$this->MensagemLog("Atualizou Pro Rata Servicos Software");
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

					COALESCE(cliovl_obrigacao,0) AS valor, 
							
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
					AND clio.clioobroid IN (466, 406, 50, 2509)
					AND clio.cliodt_inicio >= '01/01/2009'
					-- AND EXISTS (SELECT 1 FROM tipo_contrato WHERE tpcoid = 0 AND tpcgera_faturamento IS TRUE)
					";
			
			
			echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
			echo 'pesquisarServicoSoftware';
			echo PHP_EOL.'*************************************************** '.PHP_EOL;
			
			print_r($sql);
				
			echo PHP_EOL.' *************************************************** '.PHP_EOL;
			echo '        FIM pesquisarServicoSoftware ';
			echo PHP_EOL.' *************************************************** '.PHP_EOL;
			
			
			if (!$this->send_query($sql)) {
				pg_query($this->conn, "ROLLBACK");
				throw new exception("Falha ao atualizar Servicos Software",1);
			}else{
				$this->MensagemLog("Atualizou  Servicos Software");
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
		}else{
			$this->MensagemLog("verificou pendências com sucesso");
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
		}else{
			$this->MensagemLog("Verificou pendências CSV com sucesso");
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
		}else{
			$this->MensagemLog("Verificou Relatorio Pendências CSV com sucesso");
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
		}else{
			$this->MensagemLog("Gerou relatório pré-faturamento");
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
		}else{
			$this->MensagemLog("Buscou valores mínimos para faturamento"); 
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
		}else{
			$this->MensagemLog("Preparou faturamento");
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
		}else{
			$this->MensagemLog("Recuperou parametros");
		}
		
		return $res;
	}
        
	public function buscarPrazoVencimentoCliente($clioid = NULL){
		
		try {
			
			$sql = " select pf.parfprazo_vencimento
                                   from parametros_faturamento pf
                                   where true 
                                    
                                     and   pf.parfdt_exclusao is null ";
			
			if(!empty($clioid)){
				$sql .= " AND pf.parfclioid = $clioid ";
			}
			
			$sql .= " limit 1 ";
                        
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao buscar tipo de contrato.');
			}
			$vcto = pg_fetch_object($rs);
			if (pg_num_rows($rs) > 0) {
                            return $vcto->parfprazo_vencimento ;
			}else{
                            return 0;
                        }
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
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
		}else{
			$this->MensagemLog("Preparou consulta (Seleção de cliente)");
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
				RETURNING nfloid, nflno_numero, nflserie";
				
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de nota fiscal)");
		}else{
			$this->MensagemLog("Preparou consulta (Seleção de nota fiscal)");
		}
		
		
		//ORGMKTOTVS-792 - [ERP - Inativar a geração de títulos no faturamento unificado]
		if(INTEGRACAO_TOTVS_ATIVA == false){

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
			}else{
				$this->MensagemLog("Preparou consulta (Inserção de título)");
			}
		}
		//Fim - ORGMKTOTVS-792 - [ERP - Inativar a geração de títulos no faturamento unificado]




		$sql = "PREPARE recuperar_obrigacoes(integer) AS
					SELECT
						prefconnumero,
						prefobroid,
						obrobrigacao,
						prefvalor,
						prefdt_referencia,
						preftipo_obrigacao,
						NULL AS futoid,
						prefparcela AS parcela,
						prefcpvoid AS cpvoid,
						prefconsoid AS consoid
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
		}else{
			$this->MensagemLog("Preparou consulta (Seleção de obrigações financeiras)");
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
					nfitipo,
					nfiparcela,
					nficpvoid,
					nficonsoid
					)
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
					$11,
					$12,
					$13,
					$14
					)";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
		}else{
			$this->MensagemLog("Preparou consulta (Inserção de itens da nota fiscal)");
		}
		//Atualizar nota fiscal no historico de reajuste
		//$this->atualizarHistoricoReajuste($nfloid, $nficonoid, $nfiobroid, $nfidt_referencia);
		$sql = "PREPARE excluir_cliente(integer) AS
				DELETE FROM
					previsao_faturamento
				WHERE 1=1
					$this->clausulasPrevSemData
					AND prefclioid = $1;
					";			
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
		}else{
			$this->MensagemLog("Preparou excluir cliente previsao faturamento");
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
										AND $this->clausulasPrev
										AND prefclioid = $1
								);
					";
				
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao atualizar (excluir_cliente_taxas)");
		}else{
			$this->MensagemLog("Preparou atualizar (excluir_cliente_taxas)");
		}

		$sql = "PREPARE atualizar_andamento(double precision) AS
				UPDATE 
					execucao_faturamento 
				SET 
					exfporcentagem = $1 
				WHERE 
					exfdt_termino is null";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao preparar (atualizar_andamento)");
		}else{
			$this->MensagemLog("Preparou (atualizar_andamento)");
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
		}else{
			$this->MensagemLog("Calculou quantidade de clientes para faturamento");
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
					tipo/*,
					parcela,
					cpvoid,
					consoid*/
				FROM 
				(
					SELECT 
						total, 
						cliente, 
						sum(s.valor) as valor, 
						tipo/*,
						parcela,
						cpvoid,
						consoid*/
					FROM
					(			
				
						SELECT
							sum(pref.prefvalor) as total,
							pref.prefclioid AS cliente,
							sub_totais.valor AS valor,
							sub_totais.preftipo_obrigacao AS tipo/*,
							pref.prefparcela AS parcela,
							pref.prefcpvoid AS cpvoid,
							pref.prefconsoid AS consoid*/
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
							/*pref.prefparcela,
							pref.prefcpvoid,
							pref.prefconsoid,*/
							sub_totais.valor,
							sub_totais.preftipo_obrigacao
					) s
					GROUP BY 
						s.total, 
						s.cliente, 
						s.tipo/*,
						s.parcela,
						s.cpvoid,
						s.consoid*/
				) sub";

		//echo "\r\n".$sql."\r\n";
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao Gerar Faturamento (Seleção de clientes)");
		}else{
			$this->MensagemLog("Gerou Faturamento (Seleção de clientes)");
		}
		
		// Inicio do processo de geração de notas e títulos para 
		$cli_faturado = 0;
		$transaction = 0;
		$row = '';
		
		$cliente = '';
		$creditosFuturos = '';
		$arrayCreditosFuturo = '';
		
		while ($row = pg_fetch_array($res)) {
			
			unset($cliente);
			unset($creditosFuturos);
			unset($arrayCreditosFuturo);
			
			if (!$transaction) {
				pg_query($this->conn, "BEGIN");
				$transaction = 1;
			}
			
			//$time_start = $this->microtime_float(true);
			
			//crio o array de totais agrupados por tipo (Monitoramento/locação) e total geral.
			$totais['M']['valor_total'] = floatval($row['m_total']);
			$totais['L']['valor_total'] = floatval($row['l_total']);
			$totais['valor_total'] 		= floatval($row['total']);
			//$obrgrupo_nota 				= $row['obrgrupo_nota'];
					
			
			$cliente = $row['prefclioid'];

			// Se o cliente atingiu valor mínimo, gera faturamento
			if ($row['total'] >= $valor_minimo) {
				
				//Busca os creditos futuros
				$creditosFuturos = $this->buscarCreditosConceder($cliente,$totais);
				
				if (count($creditosFuturos)) {
				
					$arrayCreditosFuturo = array();
				
					foreach ($creditosFuturos as $key => $credito) {
				
				
						$creditoFuturoObj = new CreditoFuturoVO();
						$creditoFuturoParcelaObj = new CreditoFuturoParcelaVO();
						$creditoFuturoMotivoObj = new CreditoFuturoMotivoCreditoVO();
				
						$creditoFuturoObj->id = $credito['credito_id'];
						$creditoFuturoObj->contratoIndicado = $credito['connumero'];
						$creditoFuturoObj->aplicarDescontoSobre = $credito['cfoaplicar_desconto'];
						$creditoFuturoObj->valor = $credito['valor'];
						$creditoFuturoObj->obrigacaoFinanceiraDesconto = $credito['obrigacao_id'];
						$creditoFuturoObj->tipoDesconto = $credito['cfotipo_desconto'];
						$creditoFuturoObj->origem = 2;
						$creditoFuturoObj->usuarioInclusao = 2750;
				
						$creditoFuturoMotivoObj->id = $credito['motivo_credito_id'];
						$creditoFuturoMotivoObj->tipo = $credito['tipo_motivo_credito'];
						$creditoFuturoMotivoObj->descricao = $credito['cfmcdescricao'];
				
						$creditoFuturoParcelaObj->id = $credito['parcela_id'];
						$creditoFuturoParcelaObj->numero = $credito['parcela_numero'];
				
						$creditoFuturoObj->Parcelas = $creditoFuturoParcelaObj;
						$creditoFuturoObj->MotivoCredito = $creditoFuturoMotivoObj;
				
						$arrayCreditosFuturo[] = $creditoFuturoObj;
						# code...
					}
				
				}
				
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
				}else{
					$this->MensagemLog("Gerou Faturamento (Pesquisa de parametros de clientes)");
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
				//$nfldt_vencimento_txt = date("d/m/Y", $vencimento);
				//[ORGMKTOTVS-3585] Paulo Sergio
                                (int) $prazoVcto = $this->buscarPrazoVencimentoCliente($row['prefclioid']);
                                if($prazoVcto > 0){   
                                   $nfldt_vencimento_txt = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")+$prazoVcto, date("Y"))) ;
                                }else{
				   $nfldt_vencimento_txt = date("d/m/Y", $vencimento);
                                }
				//[ORGMKTOTVS-3585]
                                
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
				}else{
					$this->MensagemLog("Gerou Faturamento (Inserção de nota para o cliente $nflclioid)");
				}
				//echo "Inseriu nota! \r\n";
				
				$nota = pg_fetch_assoc($res3);
				
				$nfloid = pg_fetch_result($res3,0,0);
				$nfloid = ($nfloid>0) ? $nfloid : 0;
				

				$notaBaixadaAutomaticamente = false;
				
				//lanço os itens de descontos e atualizo o valor total da nota fical.
				if ( isset($arrayCreditosFuturo) && count($arrayCreditosFuturo) ) {
				
					$retorno = $this->bo->processarDesconto($arrayCreditosFuturo, $totais, $nota, true);
				
					//verifico se foi aplicado algum credito
					if (isset($retorno['creditos']) && count($retorno['creditos'])) {
				
						//preparo e insiro os itens de desconto
						foreach ($retorno['creditos'] as $key => $itemDesconto) {
				
							if ($itemDesconto->aplicarDescontoSobre == '1') {
								$itemDesconto->nfitipo = 'M';
							} else {
								$itemDesconto->nfitipo = 'L';
							}
				
							$itemDesconto->connumero = '';
				
				
							$itemDesconto->tpcdescricao = '';
				
				
							//Seleciona a obrigação financeira
							$sql = "SELECT
										*
									FROM
										obrigacao_financeira
									WHERE obroid = {$itemDesconto->obrigacaoFinanceiraDesconto}";
							$obrigacao = pg_fetch_assoc(pg_query($this->conn, $sql));
				
							$itemDesconto->obrobrigacao = $obrigacao['obrobrigacao'];
				
				
				
							$sql = "SELECT
										*
									FROM
										contrato_obrigacao_financeira
									WHERE cofconoid = '".intval($itemDesconto->connumero)."'
									AND cofobroid = '".intval($itemDesconto->obrigacaoFinanceiraDesconto)."'
									AND cofdt_termino is null";
				
							$obrigacao_valor = pg_fetch_assoc(pg_query($this->conn, $sql));
							$obrigacao_valor['cofvl_obrigacao'] = floatval($obrigacao_valor['cofvl_obrigacao']) > 0 ? floatval($obrigacao_valor['cofvl_obrigacao']) : floatval($obrigacao['obrvl_obrigacao']);
				
				
							$sql = "INSERT INTO nota_fiscal_item (
																	nfino_numero
																	, nfiserie
																	, nficonoid
																	, nfiobroid
																	, nfivl_item
																	, nfidt_referencia
																	, nfidesconto
																	, nfidt_inclusao
																	, nfinfloid
																	, nfivl_obrigacao
																	, nfids_item
																	, nfitipo
																	, nfinota_ant																	
																	) VALUES (
																	{$nota['nflno_numero']}
																	, '{$nota['nflserie']}'
																	, ".(trim($itemDesconto->connumero) ? $itemDesconto->connumero : "null")."
																	, {$itemDesconto->obrigacaoFinanceiraDesconto}
																	, -".floatval($itemDesconto->desconto_aplicado)."
																	, 'NOW()'
																	, 0.00
																	, NOW()
																	, {$nota['nfloid']}
																	, ".$obrigacao_valor['cofvl_obrigacao']."
																	, '".$obrigacao['obrobrigacao']."'
																	, '{$itemDesconto->nfitipo}'
																	, {$nota['nflno_numero']}																	
															)";
				
							if (!$resCredito = pg_query($this->conn, $sql)) {
								pg_query($this->conn, "ROLLBACK");
								throw new Exception ("Falha ao Gerar Faturamento (Inserção do item '".$row4['obrobrigacao']."' para o cliente ".$row['prefclioid'].") ".pg_last_error());
							}
						}
					}
				
					//apos aplicar os itens de desconto, atualizo o valor total da NF.
					$nflvl_total = floatval($retorno['total']);
				
					$sql = "UPDATE
								nota_fiscal
							SET
								nflvl_total = ". $nflvl_total ."
							WHERE
								nfloid = {$nota['nfloid']}";
				
					if (!$resUpdate = pg_query($this->conn, $sql)) {
						pg_query($this->conn, "ROLLBACK");
						throw new Exception ("Falha ao atualizar o valor total da NF".pg_last_error());
					}
																				
					$totalVerificacao = number_format($retorno['total'],2,'.','');
																				
					$notaBaixadaAutomaticamente = floatval($totalVerificacao) == 0.01 ? true : false;
				
				}
				
				// Insere o título
				$titclioid = $row['prefclioid'];
				$titdt_vencimento = $nfldt_vencimento_txt;
				$titvl_titulo = $nflvl_total;
				$titvl_desconto = 0;
				$titformacobranca = ($cliente['cliformacobranca'] != '' ? $cliente['cliformacobranca'] : 73);
				$titusuoid_alteracao = $this->usuario;
				
				if ($notaBaixadaAutomaticamente == true) {
				
					$sqlUpdateNf = "UPDATE
					nota_fiscal
					SET
					nflvl_desconto = 0.01
					WHERE
					nfloid = $nfloid
					";
					$rsUpdateNf = pg_query($this->conn, $sqlUpdateNf);
				}
				

					//ORGMKTOTVS-792 - [ERP - Inativar a geração de títulos no faturamento unificado]
					if(INTEGRACAO_TOTVS_ATIVA == false){

						if ($notaBaixadaAutomaticamente == true) {

									$sql = "INSERT INTO titulo (
									titnfloid
									, titno_parcela
									, titvl_titulo
									, titdt_pagamento
									, titdt_credito
									, titobs_cancelamento
									, titformacobranca
									, titdt_inclusao
									, tittaxa_administrativa
									, titmdescoid
									, titcfbbanco
									, tittaxa_cobrterc
									, titobs_historico
									, titvlr_comissao_ch_terc
									, titdt_referencia
									, titdt_vencimento
									, titemissao
									, titclioid
									, titvl_ir
									, titvl_iss
									, titvl_piscofins
									, titvl_desconto
									) VALUES (
									{$nfloid}
									, 0
									, 0.01
									, 'NOW()'
									, 'NOW()'
									, 'baixa automática'
									, 6
									, '01/" . date('m/Y') . "'
									, 0.00
									, 60
									, 991
									, 0.00
									, 'baixa automática'
									, 0.00
									, '01/" . date('m/Y') . "'
									, '{$titdt_vencimento}'
									, 'NOW()'
									, ". $titclioid ."
									, 0.00
									, 0.00
									, 0.00
									, 0.01
								)";

							} else{

								$sql = "EXECUTE inserir_titulo (
								$titclioid,
								'$titdt_vencimento',
								$titvl_titulo,
								$titvl_desconto,
								$titformacobranca,
								$titusuoid_alteracao,
								$nfloid
							)";
						}

						//echo "\r\n".$sql."\r\n";

						if (!$res4 = pg_query($this->conn, $sql)) {
							pg_query($this->conn, "ROLLBACK");
							throw new Exception ("Falha ao Gerar Faturamento (Inserção de título para o cliente $nflclioid)");
						}
					}	
					//Fim - ORGMKTOTVS-792 - [ERP - Inativar a geração de títulos no faturamento unificado]


				// Seleciona obrigações financeiras do cliente
				$sql = " EXECUTE recuperar_obrigacoes(".$row['prefclioid'].")";
				
				if (!$res5 = pg_query($this->conn, $sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Seleção de itens da nota fiscal)");
				}else{
					$this->MensagemLog("Gerou Faturamento (Seleção de itens da nota fiscal)");
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

					$parcela = !empty($row4['parcela']) ? $row4['parcela'] : 'NULL';
					$cpvoid = !empty($row4['cpvoid']) ? $row4['cpvoid'] : 'NULL';
					$consoid = !empty($row4['consoid']) ? $row4['consoid'] : 'NULL';
					
					
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
								'$nfitipo',
								$parcela,
								$cpvoid,
								$consoid
							)";
					
					//echo "\r\n".$sql."\r\n";
					
					if (!$res6 = pg_query($this->conn, $sql)) {
						pg_query($this->conn, "ROLLBACK");
						throw new Exception ("Falha ao Gerar Faturamento (Inserção do item '".$row4['obrobrigacao']."' para o cliente ".$row['prefclioid'].") ".pg_last_error());
					}else{
						$this->MensagemLog("Gerou Faturamento (Inserção do item '".$row4['obrobrigacao']."' para o cliente ".$row['prefclioid'].")");
					}
				}
				
				// Exclui as obrigações do cliente da previsão
				$sql = " EXECUTE excluir_cliente(".$row['prefclioid'].");".
					   " EXECUTE excluir_cliente_taxas(".$row['prefclioid'].")";
				
				if (!$res7 = pg_query($this->conn, $sql)) {
					pg_query($this->conn, "ROLLBACK");
					throw new Exception ("Falha ao Gerar Faturamento (Deletando previsao do cliente ".$row['prefclioid'].")");
				}else{
					$this->MensagemLog("Deletado previsao do cliente\r\n");
				}
			}
			
			//$time_end = $this->microtime_float(true);
			//echo "\r\n".($time_end - $time_start)."</pre>";
			$cli_faturado++;
			
			// Atualiza porcetagem processada e commita atualizações
			if (($cli_faturado % 500) == 0) {
				$sql = "EXECUTE atualizar_andamento(".round(($cli_faturado/$cli_total) * 100, 1).");";
				$msg = "Porcentagem do Faturamento: ".round(($cli_faturado/$cli_total) * 100, 1);
				$res8 = pg_query($this->conn, $sql);
				pg_query($this->conn, "END");
				
				$this->MensagemLog($msg);			
			
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
		}else{
			$this->MensagemLog("Gerou numero de série da nota fiscal");
		}
		return pg_fetch_result($res,0,0);
	}
    
    public function getObrigacaoFinanceira($obroid) {
		
		if (!$obroid){                
			throw new exception("Falha ao pesquisar uma obrigação financeira.",1);                
		}else{
			$this->MensagemLog("Pesquisou uma obrigação financeira");
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
            }else{
				$this->MensagemLog("Pesquisou contratos");
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
        	}else{
				$this->MensagemLog("Pesquisou tipos de contrato");
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
        
	public function valorReferenciaIGPM() {
		$sql = "select igpvl_referencia from igpm where igpdt_referencia = ('$this->data'::date - '1 month'::interval)";
		
		$rs = pg_query($this->conn, $sql);
		
		if (!$rs) {
			throw new Exception('Falha na pesquisa valor Referencia IGPM.');
		} elseif (pg_num_rows($rs) > 0) {
			$this->MensagemLog("Pesquisou valor Referencia IGPM");
            return pg_fetch_result($rs,0,0);
			
        } else {
            return 0;
        }
	} 
	
	
	public function valorReferenciaINPC() {
		$sql = "select inpvl_referencia from inpc where inpdt_referencia = ('$this->data'::date - '1 month'::interval) and inpdt_exclusao is null";
		
		$rs = pg_query($this->conn, $sql);
		
		if (!$rs) {
			throw new Exception('Falha na pesquisa valor Referencia INPC.');
		} elseif (pg_num_rows($rs) > 0) {
			$this->MensagemLog("Pesquisou valor Referencia INPC");
            return pg_fetch_result($rs,0,0);
				
        } else {
            return 0;
        }
	} 

		

    /**
	 * Seleciona os contratos elegíveis de reajuste filtrando pela obrigação financeira
     *
	 * @param string $obrigacao  (x,x,x)
	 * @return multitype:object
     */
	private function buscarContratosReajuste($obrigacao){

		$retorno = array();
        
		$sql = " SELECT cofoid,
					    conreajuste,
					    clioid,
					    cofconoid
				   FROM contrato_obrigacao_financeira cof_up
			 INNER JOIN contrato ON  connumero = cofconoid AND condt_exclusao IS NULL
			 INNER JOIN clientes ON conclioid = clioid
				   JOIN tipo_contrato ON  tpcoid = conno_tipo 
			  LEFT JOIN parametros_faturamento prf_up ON prf_up.parfconoid = cof_up.cofconoid AND prf_up.parfobroid = cof_up.cofobroid AND prf_up.parfdt_exclusao IS NULL AND prf_up.parfperiodicidade_reajuste IS NOT NULL
				  WHERE cofdt_termino IS NULL 
				    AND contrato.condt_exclusao IS NULL
				    AND conveioid IS NOT NULL
					AND cofvl_obrigacao > 0 
				    AND
					    CASE
						    WHEN ( prf_up.parfconoid IS NOT NULL )  THEN
						      TO_CHAR(COALESCE(cofdt_ult_referencia, cofdt_inicio),'MM/YYYY') = TO_CHAR(('".$this->data."'::DATE) - CONCAT(prf_up.parfperiodicidade_reajuste::TEXT || 'month')::INTERVAL,'MM/YYYY')
						    ELSE
						      TO_CHAR(COALESCE( cofdt_ult_referencia, cofdt_inicio ),'MM/YYYY')  = TO_CHAR(('".$this->data."'::DATE - INTERVAL '12 MONTH'),'MM/YYYY')
					    END	";
		
	    if($obrigacao){
		   $sql .=" AND cof_up.cofobroid IN ($obrigacao)";
		}
		
		if($this->filtros)
			$sql .= ' '.$this->filtros.' ';
        		
		echo 'buscarContratosReajuste <br/>';
		print_r($sql);
        		
		$rs = pg_query($this->conn, $sql);

		while($cofoid = pg_fetch_object($rs)){
			$retorno[] = $cofoid;
		}
        		
		return $retorno;

	}
        		
   /**
    * STI 86606
    * [buscaObrigacoesLocacao description]
    * @return [type] [description]
    */
   public function buscaObrigacoesLocacao() {
	                        		
   		$arrObrigacoes = array();

   		$sql = "SELECT 
					obroid 
				FROM
					obrigacao_financeira
				WHERE
					EXISTS(:condicao)
				AND 
					obrofgoid IN (
						2,	-- Locacao equipamento
						11,	-- Transferência de titularidade
						12,	-- Upgrade
						15,	-- Locacao acessórios
						24	-- Locacao
					)
				;";

		$condEqpto = "SELECT 
							1
						FROM
							contrato_pagamento
						WHERE
							cpagobroid_servico = obroid
						AND
							cpagvl_servico > 0.01
						AND
							cpagstatus <> 'C'";

		$condAcessorios = "SELECT 
								1
							FROM
								contrato_servico
							WHERE
								consobroid = obroid
							AND
								consvalor > 0.01
							AND
								consiexclusao IS NULL";

		$sqlEqpto = str_replace(':condicao', $condEqpto, $sql);
		$sqlAcessorios = str_replace(':condicao', $condAcessorios, $sql);

		$rsEqpto = pg_query($this->conn, $sqlEqpto);

		if($rsEqpto &&  (pg_num_rows($rsEqpto) > 0)) {
			while($linha = pg_fetch_object($rsEqpto)) {
				array_push($arrObrigacoes, $linha->obroid);
			}
		}

		$rsAcessorios = pg_query($this->conn, $sqlAcessorios);

		if($rsAcessorios && (pg_num_rows($rsAcessorios) > 0)) {
			while($linha = pg_fetch_object($rsAcessorios)) {
				array_push($arrObrigacoes, $linha->obroid);
			}
		}

		return $arrObrigacoes;
   }     

	/**
     *  STI'S 81607 - 81608
	 * 
	 * @param FLOAT $igpm
	 * @param FLOAT $inpc
	 * @param DATE $anopassado
	 * @param DATE $semestre
	 * @param DATE $mes
	 * @throws Exception
	 * @return boolean
	 */
	public function reajustarIgpmInpc($igpm, $inpc, $anopassado, $semestre, $mes) {

		$obrigacoes = '1,148,139';
		$obrLocacao =  $this->buscaObrigacoesLocacao();

		if(count($obrLocacao) > 0) {
			$obrigacoes .= ',' . implode(",", $obrLocacao);
		}

		//busca os contratos que sofrerão reajuste no valor, filtrando pela obrigação financeira
		$update1 = $this->buscarContratosReajuste($obrigacoes); 

		// 	1   - TRATAMENTO DE INFORMACOES DE BENS MOVEIS RASTREADO
		// 	148 - LOCAÇÃO SENSOR TEMPERATURA CÂMARA FRIA
		// 	139 - TRATAMENTO DE INFORMACOES DE BENS MOVEIS RASTREADO,

		//aplica o reajuste alterando o valor e a data da última referência e grava histórico 
        if(!empty($update1)){
        	$this->aplicarReajusteObrigacaoFinanceira($update1, $igpm, $inpc);
        	$this->MensagemLog("Aplicou Reajustes Contratos Obrigacao Financeira IGPM/INPC");
        	$this->aplicarReajusteLocacaoEquipamento($update1,$igpm,$inpc);
        	$this->MensagemLog("Aplicou Reajustes de equipamento IGPM/INPC");
        	$this->aplicarReajusteContratoServico($update1, $igpm, $inpc);
        	$this->MensagemLog("Aplicou Reajustes de serviços IGPM/INPC");
    	}else{			
        	$this->MensagemLog("Nâo há contratos para aplicar Contratos Reajuste Obrigacao Financeira IGPM/INPC ");
		}
		
        
		# Gravar a data da ult referencia para os registros que entram na condição do cliente_faturamento.
        $update2 = " UPDATE
						contrato_obrigacao_financeira
					SET
						cofdt_ult_referencia = date_trunc('month','today'::date)
					FROM
						contrato 
					WHERE
        					cofobroid IN (64,65) -- 64 - SERV.VISUALIZ.GSM/GPS /   65 - SERV.VISUALIZ.CARGA
						AND cofdt_termino IS NULL
						AND contrato.connumero=cofconoid
						AND contrato.condt_exclusao IS NULL
        				AND ((to_char('$anopassado'::date,'MM/YYYY') = to_char (cofdt_inicio,'MM/YYYY') AND cofdt_ult_referencia IS NULL) OR to_char('$anopassado'::date,'MM/YYYY') = to_char (cofdt_ult_referencia,'MM/YYYY'))";
		
		$res2 = pg_query($this->conn,$update2);
		if (!$res2) {
        	throw new Exception ("Falha ao gravar a data da ultima referencia cliente_faturamento",0);
    	}else{			
        	$this->MensagemLog("Atualizou data da ultima referencia cliente_faturamento");
		} 
		
        
        # Reajustar mensalidade do modem  - por enquanto corrigir só o IGPM
        $update3 = "  UPDATE 
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date),
						cliovl_obrigacao = (cliovl_obrigacao::float4 * $igpm)::numeric
					WHERE 
       						 (clioobroid=33) -- LOCAÇÃO MODEM GPS
						AND cliodt_termino IS NULL
						AND ((to_char('$anopassado'::date,'MM/YYYY') = to_char (cliodt_inicio,'MM/YYYY')
						AND cliodt_ult_referencia IS NULL)
						OR to_char('$anopassado'::date,'MM/YYYY') = to_char (cliodt_ult_referencia,'MM/YYYY'))";

		$res3 = pg_query($this->conn,$update3);
		if (!$res3) {
        	throw new Exception ("Falha ao reajustar mensalidade do modem ",0);
    	}else{			
        	$this->MensagemLog("Gerou reajuste IGPM / INPC da  mensalidade do modem ");
		}
		
        
		# Colocar data de ultima referência para cobrança de semestralidade do software de sascarga
		# a cobrança é de 6 em 6 meses mas não será reajustada(por enquanto)
		
		# ATUALIZA DATA DE ULTIMA REFERENCIA PARA CLIENTES QUE FAZEM PAGAMENTO MENSAL
        $query_ultima_mes = " UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date)
					WHERE
						cliono_periodo_mes = 1
        						AND (clioobroid IN (50, 406, 2509)) -- 50 - LOCAção SEMESTRALIDADE SOFTWARE SASCARGA / 406 - SERVICO DE LICENCA DE USO DO SOFTWARE SASGC
        						AND cliodt_termino IS NULL
						AND (( to_char('$mes'::date,'MM/YYYY') = to_char(cliodt_inicio,'MM/YYYY') 
						AND cliodt_ult_referencia is null) 
						OR to_char('$mes'::date,'MM/YYYY') = to_char(cliodt_ult_referencia,'MM/YYYY'));";
		
		$res4 = pg_query($this->conn,$query_ultima_mes);
        
		if (!$res4) {
        	throw new Exception ("Falha ao atualizar data ultima referencia para clientes com pagamento mensal",0);
    	}else{			
        	$this->MensagemLog("Atualizou data ultima referencia para clientes com pagamento mensal");
		}
		 
		#ATUALIZA DATA DE ULTIMA REFERENCIA PARA CLIENTES QUE FAZEM PAGAMENTO SEMESTRAL
        $query_ultima_semestral = " UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date)
					WHERE
						cliono_periodo_mes = 6
        							   AND (clioobroid IN (50,406, 2509)) -- 50 - LOCAção SEMESTRALIDADE SOFTWARE SASCARGA / 406 - SERVICO DE LICENCA DE USO DO SOFTWARE SASGC
						AND cliodt_termino is null
						AND ( ( to_char ('$semestre'::date,'MM/YYYY') = to_char(cliodt_inicio,'MM/YYYY') 
						AND cliodt_ult_referencia is null) 
						OR to_char('$semestre'::date,'MM/YYYY') = to_char(cliodt_ult_referencia,'MM/YYYY'));";

		$res5 = pg_query($this->conn,$query_ultima_semestral);
		if (!$res5) {
        	throw new Exception ("Falha ao atualizar data ultima referencia para clientes com pagamento semestral ",0);
    	}else{			
        	$this->MensagemLog("Atualizou data ultima referencia para clientes com pagamento semestral");
		}
		
		#ATUALIZA DATA DE ULTIMA REFERENCIA PARA CLIENTES QUE FAZEM PAGAMENTO ANUAL
        $query_ultima_anual = " UPDATE
						cliente_obrigacao_financeira
					SET
						cliodt_ult_referencia = date_trunc('month','today'::date)
					WHERE
						cliono_periodo_mes = 12
        						  AND (clioobroid IN (50,406, 2509)) -- 50 - LOCAção SEMESTRALIDADE SOFTWARE SASCARGA / 406 - SERVICO DE LICENCA DE USO DO SOFTWARE SASGC
						AND cliodt_termino is null
						AND ( ( to_char ('$anopassado'::date,'MM/YYYY') = to_char(cliodt_inicio,'MM/YYYY') 
						AND cliodt_ult_referencia is null) 
						OR to_char('$anopassado'::date,'MM/YYYY') = to_char(cliodt_ult_referencia,'MM/YYYY'));";
		
		$res6 = pg_query($this->conn,$query_ultima_anual);
		if (!$res6) {
        	throw new Exception ("Falha ao atualizar data ultima referencia para clientes com pagamento anual",0);
    	}else{			
        	$this->MensagemLog("Atualizou data ultima referencia para clientes com pagamento anual");
		}
        
        return true;
        
	}

    /**
     *  STI'S 81607 - 81608
     * 
     * Aplica reajuste de IGPM / INPC de acordo os dados informados nos parâmetros 
     * 
     * Insere histórico do reajuste
     * 
     * @param ARRAY $contratos
     * @param FLOAT $igpm    
     * @param FLOAT $inpc
     * @throws Exception
     * @return boolean
     */
	public function aplicarReajusteObrigacaoFinanceira($contratos, $igpm, $inpc){

		if(empty($contratos)){
			throw new Exception ("O número do contrato deve ser informado para aplicar o reajuste",0);
		}
		
		// prepara no banco o insert do histórico
		$sqlPrepare = "
                PREPARE
                    insert_historico_reajuste
                    (
                        TIMESTAMP,
                        DATE,
                        INTEGER,
                        INTEGER,
                        INTEGER,
                        CHARACTER,
                        DOUBLE PRECISION,
                        INTEGER,
                        INTEGER,
                        NUMERIC(12,2),
                        NUMERIC(12,2),
                        DATE,
                        DATE
                    )
                AS
                INSERT INTO
                    obrigacao_financeira_reajuste_historico
                    (
                        ofrhdt_inclusao,
                        ofrhdt_referencia,
                        ofrhusuoid_cadastro,
                        ofrhclioid,
                        ofrhconnumero,
                        ofrhtipo_reajuste,
                        ofrhvl_referencia,
                        ofrhobroid,
                        ofrhnfloid,
                        ofrhvalor_anterior,
                        ofrhvalor_reajustado,
                        ofrhdt_inicio_cobranca,
                        ofrhdt_fim_cobranca
                    )
                    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)
                     ";
		
		if (!$rs = pg_query($this->conn,$sqlPrepare)) {
			throw new Exception ("Falha ao preparar insert_historico_reajuste IGPM/INPC",0);
		}else{
			$this->MensagemLog("Preparou insert_historico_reajuste IGPM/INPC");
		}
		
		
		foreach($contratos as $cofoid){
		
			$percentualReajuste = (intval($cofoid->conreajuste) == 1) ? $igpm : $inpc;
		
		     $sql = "  UPDATE
                            contrato_obrigacao_financeira
                        SET
                            cofdt_ult_referencia = date_trunc('month','today'::date),
                            cofvl_obrigacao = cofvl_obrigacao * " . $percentualReajuste . "
                        WHERE
                            cofoid = ".intval($cofoid->cofoid)."
                        RETURNING
                            cofvl_obrigacao AS valor_reajustado,
                            cofconoid,
                            cofobroid,
                            '".$cofoid->conreajuste."' AS conreajuste,
                            ".$cofoid->clioid." AS clioid,
                            " . $percentualReajuste . " AS valor_referencia,
                            TRUNC( cofvl_obrigacao / " . $percentualReajuste . ",2) AS valor_anterior ";
		
		     echo '<pre>';
		     print_r($sql);
		     echo '\n';
		     
			if (!$res = pg_query($this->conn,$sql)) {
				throw new Exception ("Falha ao atualizar reajuste contrato_obrigacao_financeira IGPM/INPC",0);
			}
			
				
			$dadosHistorico = pg_fetch_object($res);
		
			//Dados complementares para histórico
			$rsParametros = $this->recuperarParametros(false);
			$parametros = pg_fetch_object($rsParametros);
			$param = explode("|",$parametros->exfparametros);
			$dataReferencia = $param[0];
		
		
			//                 verificar isso aqui também
			//                 e se essas colunas  ->
			//                 ofrhdt_inicio_cobranca,
			//                 ofrhdt_fim_cobranca
		
			//                 devem existir na obrigacao_financeira_reajuste_historico
		
			// dados vinham da tabela obrigacao_financeira_reajuste (excluida, será substituida por parametros_faturamento)
			$datasReajuste->ofrdt_inicio_cobranca = "NOW()"; // not null
			$datasReajuste->ofrdt_fim_cobranca = "NULL";
		
			//Inserir o histórico do Reajuste
			$sqlExecute = "
                        EXECUTE
                            insert_historico_reajuste
                            (
                                NOW(),
                                '".$dataReferencia."',
                                ".intval($this->usuario).",
                                ".$dadosHistorico->clioid.",
                                ".$dadosHistorico->cofconoid.",
                                '".$dadosHistorico->conreajuste."',
                                ".$dadosHistorico->valor_referencia.",
                                ".$dadosHistorico->cofobroid.",
                                NULL,
                                ".$dadosHistorico->valor_anterior.",
                                ".$dadosHistorico->valor_reajustado.",
                                '".$datasReajuste->ofrdt_inicio_cobranca."',
                                ".$datasReajuste->ofrdt_fim_cobranca."
                            )
                    ";
		
			if (!pg_query($this->conn,$sqlExecute)) {
				throw new Exception ("Falha ao Inserir Histórico de Reajuste IGPM/INPC",0);
			}
		}
		
		return true;
		
	}
	
	public function aplicarReajusteLocacaoEquipamento($contratos,$igpm,$inpc) {
    
		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        aplicarReajusteLocacaoEquipamento ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;

		//Dados complementares para histórico
		$rsParametros = $this->recuperarParametros(false);
		$parametros = pg_fetch_object($rsParametros);
		$param = explode("|",$parametros->exfparametros);
		$dataReferencia = $param[0];
		//$dataReferencia = explode('/', $this->data);
		//$dataReferencia = $dataReferencia[2].'-'.$dataReferencia[1].'-'.$dataReferencia[0];

		$sql = "UPDATE 
					contrato_pagamento
				SET 
					cpagvl_servico = cpagvl_servico * :percentual_reajuste
				WHERE 
				cpagconoid = :connumero
				AND cpagstatus <> 'C'
				AND cpagvl_servico > 0.01
				RETURNING 
					cpagoid,
					cpagvl_servico,
					cpagobroid_servico,
					TRUNC( cpagvl_servico / :percentual_reajuste ,2) AS valor_anterior;";

		$sqlHistorico = "INSERT INTO
							contrato_pagamento_reajuste_historico
							(
								cprhdt_inclusao,
								cprhdt_referencia,
								cprhusuoid_cadastro,
								cprhtipo_reajuste,
								cprhvl_referencia,
								cprhcpagoid,
								cprhnfloid,
								cprhvalor_anterior,
								cprhvalor_reajustado,
								cprhdt_inicio_cobranca,
								cprhdt_fim_cobranca
							) VALUES (
								NOW(),
								':data_referencia',
								:usuario_cadastro,
								:tipo_reajuste,
								:valor_referencia,
								:cpagoid,
								NULL,
								:valor_anterior,
								:valor_reajustado,
								NOW(),
								NULL
							)"; 

		foreach ($contratos as $contrato) {

			$percentualReajuste = (intval($contrato->conreajuste) == 1) ? $igpm : $inpc;

			// Realiza update nos serviços de locação do contrato
			$sqlTemp = str_replace(':percentual_reajuste', $percentualReajuste, $sql);
			$sqlTemp = str_replace(':connumero', $contrato->cofconoid, $sqlTemp);

			echo '<pre>'; print_r($sqlTemp); echo '</pre>';

			if ($rs = pg_query($this->conn,$sqlTemp)) {

				//executa insert do histórico
				if(pg_num_rows($rs) > 0) {

					while($linha = pg_fetch_object($rs)) {

						$sqlTempHistorico = str_replace(':data_referencia', $dataReferencia, $sqlHistorico);
						$sqlTempHistorico = str_replace(':usuario_cadastro', intval($this->usuario), $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':tipo_reajuste', intval($contrato->conreajuste), $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':valor_referencia', $percentualReajuste, $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':cpagoid', $linha->cpagoid, $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':valor_anterior', $linha->valor_anterior, $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':valor_reajustado', $linha->cpagvl_servico, $sqlTempHistorico);

						echo '<pre>'; print_r($sqlTempHistorico); echo '</pre>';

						if (!pg_query($this->conn,$sqlTempHistorico)) {
							throw new Exception ("Falha ao Inserir Histórico de Reajuste IGPM/INPC do serviço de locação.",0);
						}

					}

				}
			} else {
				throw new Exception ("Falha ao realizar reajuste IGPM/INPC do serviço de locação",0);
			}
		}

		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM aplicarReajusteLocacaoEquipamento ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;

	}
	
	public function aplicarReajusteContratoServico($contratos,$igpm,$inpc) {

		echo PHP_EOL.PHP_EOL.' ***************************************************'.PHP_EOL;
		echo '        aplicarReajusteContratoServico ';
		echo PHP_EOL.'*************************************************** '.PHP_EOL;

		//Dados complementares para histórico
		$rsParametros = $this->recuperarParametros(false);
		$parametros = pg_fetch_object($rsParametros);
		$param = explode("|",$parametros->exfparametros);
		$dataReferencia = $param[0];
		//$dataReferencia = explode('/', $this->data);
		//$dataReferencia = $dataReferencia[2].'-'.$dataReferencia[1].'-'.$dataReferencia[0];

		$sql = "UPDATE 
					contrato_servico
				SET 
					consvalor = consvalor * :percentual_reajuste
				WHERE 
				consconoid = :connumero
				AND conssituacao = 'L'
				AND consiexclusao IS NULL
				AND consinstalacao IS NOT NULL
				AND consvalor > 0.01
				RETURNING 
					consoid,
					consvalor,
					consobroid,
					TRUNC( consvalor / :percentual_reajuste ,2) AS valor_anterior;";

		$sqlHistorico = "INSERT INTO
							contrato_servico_reajuste_historico
							(
								csrhdt_inclusao,
								csrhdt_referencia,
								csrhusuoid_cadastro,
								csrhtipo_reajuste,
								csrhvl_referencia,
								csrhconsoid,
								csrhnfloid,
								csrhvalor_anterior,
								csrhvalor_reajustado,
								csrhdt_inicio_cobranca,
								csrhdt_fim_cobranca
							) VALUES (
								NOW(),
								':data_referencia',
								:usuario_cadastro,
								:tipo_reajuste,
								:valor_referencia,
								:consoid,
								NULL,
								:valor_anterior,
								:valor_reajustado,
								NOW(),
								NULL
							)"; 

		foreach ($contratos as $contrato) {

			$percentualReajuste = (intval($contrato->conreajuste) == 1) ? $igpm : $inpc;

			// Realiza update nos serviços de locação do contrato
			$sqlTemp = str_replace(':percentual_reajuste', $percentualReajuste, $sql);
			$sqlTemp = str_replace(':connumero', $contrato->cofconoid, $sqlTemp);

			echo '<pre>'; print_r($sqlTemp); echo '</pre>';

			if ($rs = pg_query($this->conn,$sqlTemp)) {
				//executa insert do histórico
				if(pg_num_rows($rs) > 0) {

					while($linha = pg_fetch_object($rs)) {

						$sqlTempHistorico = str_replace(':data_referencia', $dataReferencia, $sqlHistorico);
						$sqlTempHistorico = str_replace(':usuario_cadastro', intval($this->usuario), $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':tipo_reajuste', intval($contrato->conreajuste), $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':valor_referencia', $percentualReajuste, $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':consoid', $linha->consoid, $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':valor_anterior', $linha->valor_anterior, $sqlTempHistorico);
						$sqlTempHistorico = str_replace(':valor_reajustado', $linha->consvalor, $sqlTempHistorico);

						echo '<pre>'; print_r($sqlTempHistorico); echo '</pre>';

						if (!pg_query($this->conn,$sqlTempHistorico)) {
							throw new Exception ("Falha ao Inserir Histórico de Reajuste IGPM/INPC dos serviços do contrato.",0);
						}

					}

				}
			} else {
				throw new Exception ("Falha ao realizar reajuste IGPM/INPC dos serviços",0);
			}
		}
		
		echo PHP_EOL.' *************************************************** '.PHP_EOL;
		echo '        FIM aplicarReajusteContratoServico ';
		echo PHP_EOL.' *************************************************** '.PHP_EOL;

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
		}else{			
			$this->MensagemLog("Pesquisou Cliente");			
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
	
	   // pg_query($this->conn, "BEGIN");
	
	    $this->delTodasPrevisoes();
	
	    if ($this->debug) {
	        $time_end = $this->microtime_float(true);
	        echo "\r\n\r\nTempo de execução:".($time_end - $time_start)." segundos</pre>";
	    }
	
	    //pg_query($this->conn, "END");
	}
	

	public function buscarCreditosConceder($clienteId, $totais) {
	
	
	
		$sql = "SELECT
                    cfooid AS credito_id,
                    cfoconnum_indicado AS connumero,
                    tpcdescricao,
                    cfmctipo AS tipo_motivo_credito,
                    cfmcdescricao,
                    cfocfmcoid AS motivo_credito_id,
                    cfoobroid_desconto AS obrigacao_id,
                    obrobrigacao,
                    cfoaplicar_desconto AS cfoaplicar_desconto,
                    -- 1 - percentual / 2 - valor
                    cfotipo_desconto,
                    CASE WHEN cfoaplicar_desconto = 1 THEN 'Monitoramento'
                             WHEN cfoaplicar_desconto = 2 THEN 'Locação'
                        END AS aplicar_desconto_descricao,
                    (SELECT
                        A.cfpvalor - (SELECT COALESCE(SUM(cfmvalor),SUM(cfmvalor), 0) FROM credito_futuro_movimento cfm WHERE cfm.cfmcfpoid = A.cfpoid AND cfm.cfmdt_exclusao IS NULL) AS valor_deferido
	
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = cfooid
                    AND
                        A.cfpativo = true
                    ORDER BY
                        A.cfpnumero ASC
                    LIMIT 1) AS valor,
	
                    (SELECT
                        cfpoid
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = cfooid
                    AND
                        A.cfpativo = true
                    ORDER BY
                        A.cfpnumero ASC
                    LIMIT 1) AS parcela_id,
	
                    (SELECT
                        cfpnumero
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = cfooid
                    AND
                        A.cfpativo = true
                    ORDER BY
                        A.cfpnumero ASC
                    LIMIT 1) AS parcela_numero
	
                FROM
                    credito_futuro
                INNER JOIN
                    credito_futuro_motivo_credito ON (cfmcoid = cfocfmcoid)
                INNER JOIN
                    credito_futuro_ordem_aplicacao ON (cfoatipo_motivo = cfmctipo)
                INNER JOIN
                    obrigacao_financeira ON (obroid = cfoobroid_desconto)
                LEFT JOIN
                    contrato ON (connumero = cfoconnum_indicado)
                LEFT JOIN
                    tipo_contrato ON (tpcoid = conno_tipo)
                WHERE
                    --status igual a aprovado
                    cfostatus = 1
                AND
                    --saldo maio que 0 ou com parcelas ativas
                    (
                        credito_futuro.cfosaldo > 0
                    OR
                        (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) > 0
                    )
                AND
                    --que não esteja excluido
                    cfodt_exclusao IS NULL
                AND
                    --que não esteja encerrado
                    cfodt_encerramento IS NULL
                AND
                    --do cliente em questão
                    cfoclioid = " . intval($clienteId) . "                
                AND
                    (
                        (cfmctipo = 1)
                        OR (
                            cfmctipo <> 1
                            AND (EXISTS (
                                        SELECT
                                            cfmctipo
                                        FROM
                                            credito_futuro cf,
                                            credito_futuro_motivo_credito cfm
                                        WHERE
                                            cf.cfocfmcoid = cfm.cfmcoid
                                        AND
                                            cf.cfoclioid = " . intval($clienteId) . "
                                        AND
                                            cfostatus = 1
                                        AND
                                            --saldo maio que 0 ou com parcelas ativas
                                            (
                                                cf.cfosaldo > 0
                                            OR
                                                (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) > 0
                                            )
                                        AND
                                            --que não esteja excluido
                                            cfodt_exclusao IS NULL
                                        AND
                                            --que não esteja encerrado
                                            cfodt_encerramento IS NULL
                                        AND 
                                            cfmctipo != 1	
                                    )
                              )
                        )
                    )
                    AND (
							(
								(cfmctipo = 4)
								AND (
									SELECT 
									CASE WHEN (forcdebito_conta = TRUE AND titdt_pagamento IS NOT NULL) THEN TRUE ELSE FALSE END 
									FROM nota_fiscal
									INNER JOIN titulo ON titnfloid = nfloid
									INNER JOIN forma_cobranca ON forcoid = titformacobranca
									WHERE nflclioid = " . intval($clienteId) . "
									ORDER BY nfldt_inclusao DESC
									LIMIT 1
								) = TRUE
							)
							OR (
								(cfmctipo = 5)
								AND (	
									SELECT 
									CASE WHEN (forccobranca_cartao_credito = TRUE AND titdt_pagamento IS NOT NULL) THEN TRUE ELSE FALSE END 
									FROM nota_fiscal 
									INNER JOIN titulo ON titnfloid = nfloid
									INNER JOIN forma_cobranca ON forcoid = titformacobranca
									WHERE nflclioid = " . intval($clienteId) . "
									ORDER BY nfldt_inclusao DESC
									LIMIT 1
								) = TRUE
							)
							OR (cfmctipo = 1)
							OR (cfmctipo = 2)
							OR (cfmctipo = 3)
					)
	
                ORDER BY
                    --ordenando pela ordem de aplicação de tipo de desconto
                    cfoaordem_aplicacao ASC;
                ";
	
		if (!$rs = pg_query($this->conn,$sql)) {
			throw new Exception('Falha ao buscar créditos Futuros do cliente.');
		}
	
		$retorno = array();
	
		$valorTotalNota = array();
		$valorTotalNota['M'] = $totais['M']['valor_total'];
		$valorTotalNota['L'] = $totais['L']['valor_total'];
	
		while ($row = pg_fetch_assoc($rs)) {
	
	
			$row['porcentagem_desconto'] = '';
	
			//se for porcentagem e monitoração
			if ( $row['cfotipo_desconto'] == '1' && $row['cfoaplicar_desconto'] == '1') {
				$row['porcentagem_desconto'] = $row['valor'] / 100;
				$row['valor'] = ($row['porcentagem_desconto'] * $valorTotalNota['M']);
	
				if ($row['porcentagem_desconto'] != 1 ) {
					$row['valor'] = floor($row['valor'] * 100) / 100;
				}
	
			}
	
			//se for porcentagem e locação
			if ( $row['cfotipo_desconto'] == '1' && $row['cfoaplicar_desconto'] == '2') {
				$row['porcentagem_desconto'] = $row['valor'] / 100;
				$row['valor'] = ($row['porcentagem_desconto'] * $valorTotalNota['L']);
	
				if ($row['porcentagem_desconto'] != 1 ) {
					$row['valor'] = floor($row['valor'] * 100) / 100;
				}
	
			}
	
			if ($row['cfotipo_desconto'] != '1') {
				$row['valor'] = floor($row['valor'] * 100) / 100;
			}
	
			$row['valor_formatado'] = number_format($row['valor'],2,',','.');
			//$this->vo->addCredito($row);
			$retorno[] = $row;
		}
	
	
		return $retorno;
	
	
	}
	
	public function getByContrato($connumero) {
		
		$sql = "SELECT
					coneqcoid, conno_tipo
				FROM
					contrato
				WHERE
					connumero  = $connumero";

		$res = pg_query($this->conn,$sql);
		$return = pg_fetch_object($res);

		if(pg_num_rows($res) > 0){
			$return->coneqcoid = ($return->coneqcoid == '') ? 'null' :  $return->coneqcoid;
			$return->conno_tipo = ($return->conno_tipo == '') ? 'null' :  $return->conno_tipo;
			return $return;
    }
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
    	
    	return true;
    }
    
    /**
     * 
     * Mata processo do BD
     *
     * @author Rafael Dias <rafael.dias@sascar.com.br>
     * @version 23/07/2014
     * @param integer $pid
     */
	public function killProcessDB($pid){
    	
    	global $dbstring;
    	
    	$conn2 = pg_connect($dbstring);
		if($_REQUEST['debug']  == 2 ){
			print_r($dbstring);
		}
    			
    	if ($pid > 0) {
		
			$sql = "SELECT count(1) FROM pg_stat_activity WHERE pid = $pid";
			$res = pg_query($conn2,$sql);
			$pid_exists = (pg_num_rows($res) > 0) ? true : false;
		
			if ($pid_exists){
				$sql = "SELECT mata_processo($pid);";
				$res = pg_query($conn2,$sql);
				$return = pg_fetch_result($res,0,0);
	    	} else{
				$return = 't';
			}
			
			if ($return != 't'){
				pg_close($conn2);
	    		return false;
	    	}
	    	pg_close($conn2);
	    	return true;
	    	
    	} else{
    		return false;
    	}
    }

    /*
     * Metodo para verificar se a conexao esta ocupada
     */
    private function send_query($sql) {
    	
    	return pg_query($this->conn, $sql);
    }
    

     /**
	 * STI 83807
	 * Calcula o valor do imposto para ser incluído na nota fiscal (lei da transparência)
	 * Atualiza a nota fiscal com o valor do imposto, aliquota e código do serviço
	 *
	 * @param int $nfloid
	 */
    public static function calcularImposto($nfloid, $conn) {
        // Inicializar variáveis
        $notaFiscal = array();
        $itemNotaFiscal = array();
        $obrigacaoFinanceira = array();
        $aliquotasObrigacaoFinanceira = array();
        $prioridadeAliquota = array();
        $aliquotaServico = array();
        $aliquotaPrioritaria = array();
        $logErrors = array();

        // Selecionar informações da nota_fiscal
        $queryNF = 
            "SELECT 
                nflno_numero, 
                nflserie,
                nflvl_total,
                nflvlr_imposto 
            FROM 
                nota_fiscal 
            WHERE 
                nfloid = $nfloid";

        // Salvar mensagem de erro se ocorrer algum problema na query
        if(!$resultNF = pg_query($conn, $queryNF)) {
            $logErrors[] = "Erro ao buscar nota fiscal. " . pg_last_error();
        }
        else {
        	// Receber o resultado da query
            $notaFiscal = pg_fetch_array($resultNF);

            // Calcular imposto somente se ainda não tiver sido calculado 
            if($notaFiscal['nflvlr_imposto'] == '') {

            	$nflnoNumero = $notaFiscal['nflno_numero'];
				$nflSerie = $notaFiscal['nflserie'];

	            // Selecionar informações da nota_fiscal_item
	            $queryItensNF = 
	                "SELECT 
	                    nfiobroid 
	                FROM 
	                    nota_fiscal_item 
	                WHERE 
	                    nfino_numero = $nflnoNumero
	                AND
	                	nfiserie = '$nflSerie'";

	            // Salvar mensagem de erro se ocorrer algum problema na query
	            if(!$resultItensNF = pg_query($conn, $queryItensNF)) {
	                $logErrors[] = "Erro ao buscar os itens da nota fiscal. " . pg_last_error();
	            }
	            else {
	                // Para cada item da nota fiscal
	                while ($itemNotaFiscal = pg_fetch_assoc($resultItensNF)) {
	                    
	                    // Se o item da nota fiscal possuir obrigação financeira 
	                    if($itemNotaFiscal['nfiobroid'] != '') {
	                    
	                        // Selecionar aliquota da obrigacao_financeira
	                        $queryObrigacaoFinanceira = 
	                            "SELECT 
	                                obraliseroid 
	                            FROM 
	                                obrigacao_financeira 
	                            WHERE 
	                                obroid = " . $itemNotaFiscal['nfiobroid'];

	                        // Salvar mensagem de erro se ocorrer algum problema na query
	                        if(!$resultObrigacaoFinanceira = pg_query($conn, $queryObrigacaoFinanceira)) {
	                            $logErrors[] = "Erro ao buscar a obrigação financeira. " . pg_last_error();
	                        }  
	                        else {              

	                            // Receber o resultado da query
	                            $obrigacaoFinanceira = pg_fetch_array($resultObrigacaoFinanceira);

	                            // Inserir a aliquota da obrigação financeira, caso a mesma não esteja no array
	                            if(!in_array($obrigacaoFinanceira['obraliseroid'], $aliquotasObrigacaoFinanceira)) {    
	                                $aliquotasObrigacaoFinanceira[] = $obrigacaoFinanceira['obraliseroid'];
	                            } 
	                        }
	                    }
	                }

	                if(!empty($aliquotasObrigacaoFinanceira)) {

		                // Selecionar a última prioridade de aliquota
		                $queryPrioridade = 
		                    "SELECT 
		                        max(aliserprioridade) 
		                    FROM 
		                        aliquota_servico";
		                
		                // Salvar mensagem de erro se ocorrer algum problema na query
		                if(!$resultPrioridade = pg_query($conn, $queryPrioridade)) {
		                    $logErrors[] = "Erro ao buscar prioridade da aliquota. " . pg_last_error();
		                }
		                else {
		                    // Receber o resultado da query
		                    $prioridadeAliquota = pg_fetch_array($resultPrioridade);
		                    
		                    // Salvar prioridade máxima
		                    $prioridadeAtual = $prioridadeAliquota['max'];

		                    // Para cada aliquota da Obrigação Financeira
		                    foreach($aliquotasObrigacaoFinanceira as $aliquota) {
		                        
		                        // Selecionar informações da aliquota_servico
		                        $queryAliquotaServico = 
		                            "SELECT 
		                                alisercodigoservico,
		                                aliseratividade, 
		                                aliserprioridade, 
		                                aliseraliquota
		                            FROM 
		                                aliquota_servico 
		                            WHERE 
		                                aliseroid = $aliquota";
		                        
		                        // Salvar mensagem de erro se ocorrer algum problema na query
		                        if(!$resultAliquotaServico = pg_query($conn, $queryAliquotaServico)) {
		                            $logErrors[] = "Erro ao buscar a aliquota serviço. " . pg_last_error();
		                        }
		                        else {

		                            // Receber o resultado da query
		                            $aliquotaServico = pg_fetch_array($resultAliquotaServico);

		                            // Setar a aliquota prioritaria, caso a prioridade corrente for menor ou igual que a prioridade atual
		                            if($aliquotaServico['aliserprioridade'] <= $prioridadeAtual) {
		                                $aliquotaPrioritaria = $aliquotaServico;
		                                $prioridadeAtual = $aliquotaServico['aliserprioridade'];
		                            }
		                        }
		                    }

		                    if(!empty($aliquotaPrioritaria)) {

			                    // Calcular valor do imposto
			                    $valorImposto = $notaFiscal['nflvl_total'] * ($aliquotaPrioritaria['aliseraliquota'] / 100);
			                    
			                    // Formatar valor do imposto
			                    $valorImposto = number_format($valorImposto, 2, '.', '');
			                    
			                    // Definir aliquota imposto
			                    $aliquotaImposto = $aliquotaPrioritaria['aliseraliquota'];
			                    
			            		// Definir código serviço
			                    $codigoServico = $aliquotaPrioritaria['alisercodigoservico'];

			                    // Atualizar nota fiscal
			                    $queryNotaFiscal = 
			                        "UPDATE
			                            nota_fiscal
			                        SET
			                            nflvlr_imposto      = $valorImposto,
			                            nflaliquota_imposto = $aliquotaImposto,
			                            nflcodigo_servico   = '$codigoServico'
			                        WHERE 
			                            nfloid = $nfloid";

			                    // Salvar mensagem de erro se ocorrer algum problema na query
			                    if (!pg_query($conn, $queryNotaFiscal)) {
			                        $logErrors[] = "Erro ao gravar imposto na nota fiscal. " . pg_last_error();
			                    }
			                }
		                }
		            }
		            else {
		            	$logErrors[] = "Não foi encontrada obrigação financeira para os itens da nota fiscal";
		            }
	            }
	        }
	        else {
	        	$logErrors[] = "O imposto  da Nota Fiscal $nfloid já foi calculado";
	        }
        }
        // Caso tenha erros
        if(!empty($logErrors)) {
	    	
	    	// Gravar arquivo de log
			$data_processamento = date("Ymd"); 
			$fp = fopen(_SITEDIR_."faturamento/log_calculo_imposto_$data_processamento","a");
			chmod(_SITEDIR_."faturamento/log_calculo_imposto_$data_processamento", 0777);

			foreach($logErrors as $error) {
				fwrite($fp, $error . "\r\n"); 
			}

			fclose($fp);
			
	    }
    }
	
	public function MensagemLog($msg){ 
		$hora_atual    = date("H:i:s)");
		$data_processamento = date("Ymd"); 
		$fp = fopen(_SITEDIR_."faturamento/log_faturamento_unificado_$data_processamento","a");
		chmod(_SITEDIR_."faturamento/log_faturamento_unificado_$data_processamento", 0777);
		fputs ($fp,"$hora_atual - $msg\n");  
		fclose($fp);
	}
	
}
