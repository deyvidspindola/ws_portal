<?php

/**
 * Classe FinPrevisaoArrecadacaoVivoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Financas
 * @author   Marcello Borrmann <marcello.borrmann@meta.com.br>
 *
 */
class FinPrevisaoArrecadacaoVivoDAO {

	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;

	/**
	 * Mensagem de erro para o processamentos dos dados
	 * @const String
	 */
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";


	public function __construct($conn) {
		//Seta a conexão na classe
		$this->conn = $conn;
	}
	

	// Gera registro de início de execução
	public function inserirDadosExecucao($tipo, stdClass $dados) {
	
		$parametros = array(
				"01/" . $dados->dataReferencia,
				$dados->nomeCliente,
				$dados->idUsuario
		);
	
		$parametrosFormatados = implode('|', $parametros);
	
		// Inicia controle de previsão concorrente
		$sql = "INSERT INTO execucao_previsao_vivo (
                    epvusuoid,
                    epvtipo_processo,
                    epvporcentagem,
                    epvparametros
                ) VALUES (
                    " . $dados->idUsuario . ",
                    '" . $tipo . "',
                    " . "0" . ",
                    '" . $parametrosFormatados . "'
                )";
		
		$resultado = pg_query($this->conn, $sql);
	
		if ($resultado) {
			return true;
		} else {
			throw new ErrorException('Falha ao preparar previsão.');
		}
	}

	// Busca código do processo em execução
	public function buscarIdProcessoBancoDados() {
	
		$idProcesso = 0;
	
		$sql = "
                SELECT
                    pg_backend_pid() AS pid";
	
		$resultado = pg_query($this->conn, $sql);
	
		if ($resultado && pg_num_rows($resultado) > 0) {
			$idProcesso = pg_fetch_result($resultado, 0, 'pid');
		}
	
		return $idProcesso;
	}

    // Atualiza registro indicando fim da execução
    public function finalizarProcesso($resultado) {

        $sql = "
            UPDATE execucao_previsao_vivo SET
                epvporcentagem = 100,
                epvdt_termino = NOW(),
                epvresultado = '" . $resultado . "'
            WHERE
                epvdt_termino IS NULL";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas.");
        }

        return true;
    }

    // Verifica concorrência entre processos
    public function verificarProcesso($finalizado) {
    
    	try {
    
    		$parametros = $this->dao->recuperarParametros($finalizado);
    
    		if ($parametros->epvtipo_processo == 'F') {
    			$msg = "Faturamento iniciado por " .
    					$parametros->nm_usuario . " às " . $parametros->inicio . " &nbsp;&nbsp;-&nbsp;&nbsp;  " .
    					number_format($parametros->epvporcentagem, 1, ',', '.') . " % concluído.";
    		} else {
    			$msg = "Resumo iniciado por " . $parametros->nm_usuario . " às " . $parametros->inicio;
    		}
    
    		return array(
    				"codigo" => 2,
    				"msg" => $msg,
    				"parametros" => $parametros
    		);
    	} catch (Exception $e) {
    		return array(
    				"codigo" => 0,
    				"msg" => ''
    		);
    	} catch (ErrorException $e) {
    		return array(
    				"codigo" => 1,
    				"msg" => "Falha ao verificar concorrência. Tente novamente."
    		);
    	}
    }
    
    // Recupera os parâmetros salvos pela previsão
    public function recuperarParametros($finalizado) {
    
    	
    	$sql = "SELECT
					nm_usuario,
					usuemail,
					epvoid,
					epvusuoid,
					TO_CHAR(epvdt_inicio, 'HH24:MI:SS') as inicio,
					TO_CHAR(epvdt_termino, 'HH24:MI:SS') as termino,
					TO_CHAR(epvdt_inicio, 'DD/MM/YYYY HH24:MI:SS') as data_inicio,
					TO_CHAR(epvdt_termino, 'DD/MM/YYYY HH24:MI:SS') as data_termino,
					epvtipo_processo,
					epvporcentagem,
					epvparametros
				FROM
					execucao_previsao_vivo
                    INNER JOIN usuarios on cd_usuario = epvusuoid";
    
    	if ($finalizado) {
    		$sql .= "
                ORDER BY
                    epvdt_termino DESC";
    	} else {
    		$sql .= "
                AND
                    epvdt_termino IS NULL";
    	}
    
    	$sql .= " LIMIT 1";
    
    	$resultado = pg_query($this->conn, $sql);
    
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    			return pg_fetch_object($resultado);
    		} else {
    			throw new Exception('Não foram encontrados parâmetros.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar parâmetros.');
    	}
    }

	
	// Gerar previsão de arrecadação pró-rata de monitoramento
	public function inserirPrevisaoArrecadacaoProRataMonitoramento($parametros) {
		
		$sql_prorata = "	
			INSERT INTO previsao_arrecadacao_vivo(
				pavano_referencia, -- ano selecionado pelo usuário
				pavmes_referencia, -- mês selecionado pelo usuário
				pavconoid, -- contrato
				pavobroid, -- (Códigos válidos: 1, 9 ou 23)
				pavusuoid, -- id_usuario logado
				pavsubscription, -- subscription
				pavdt_previsao, -- now()
				pavdt_vencimento, -- Calculada conforme a data de corte
				pavvl_previsao, -- Calculado
				pavvl_desconto, -- 0 (Zero)
				pavflag_processado, -- FALSE - Não processado
				pavstatus -- '0' (Zero) - Não Informado
				)	
				SELECT DISTINCT
					$parametros->ano,
					$parametros->mes,
					connumero,
					9,
					$parametros->idUsuario,
					vppasubscription,
					NOW(),	
					CASE 
						WHEN clitipo = 'J'
						THEN 
							CASE 
							-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v2 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
							-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v2 < cfpdia_corte)
								THEN to_date(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v2 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v2 < cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
							-- Ciclo não cadastrado
								ELSE NOW()              
							END 
						ELSE
							CASE 
							-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v1 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
							-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v1 < cfpdia_corte)
								THEN to_date(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v1 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v1 < cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
							-- Ciclo não cadastrado
								ELSE NOW()              
							END
					END AS data_vencimento, 					
					(
						(
							CASE
								-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfconoid = con.connumero
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 1
									LIMIT 1) 
								THEN
									-- Seleciona o valor parametrizado
									ROUND(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parfconoid = con.connumero
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 1
												LIMIT 1)::numeric 
											-
											-- Aplica o desconto
											(
												(	
													SELECT parfvl_cobrado
													FROM parametros_faturamento
													WHERE parfconoid = con.connumero
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 1
													LIMIT 1	)::numeric 
												*
												CASE
													-- Desconto de 100 % se estiver isento de cobrança
													WHEN EXISTS (	
														SELECT 1
														FROM parametros_faturamento
														WHERE parfconoid = con.connumero
														AND parfobroid = cof.cofobroid
														AND parfdt_exclusao IS NULL
														AND parfativo = 't'
														AND parfnivel = 1
														AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
														LIMIT 1
													) 
													THEN 1
													-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
													ELSE (
														COALESCE(
															(
																SELECT parfdesconto
																FROM parametros_faturamento
																WHERE parfconoid = con.connumero
																AND parfobroid = cof.cofobroid
																AND parfdt_exclusao IS NULL
																AND parfativo = 't'
																AND parfnivel = 1
																AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
																LIMIT 1)::numeric, 0
														) / 100
													)
												END
											), 2
										)
		
								-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									LIMIT 1) 
								THEN
									-- Seleciona o valor parametrizado
									ROUND(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parftpcoid = tpc.tpcoid
											AND parfeqcoid = con.coneqcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											LIMIT 1)::numeric 
										-
										-- Aplica o desconto
										(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parfclioid = con.conclioid
												AND parftpcoid = tpc.tpcoid
												AND parfeqcoid = con.coneqcoid
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	
													SELECT 1
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parftpcoid = tpc.tpcoid
													AND parfeqcoid = con.coneqcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 4
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN 1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE(	
													COALESCE(
														(
															SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = con.conclioid
															AND parftpcoid = tpc.tpcoid
															AND parfeqcoid = con.coneqcoid
															AND parfobroid = cof.cofobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 4
															AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric, 0
													) / 100
												)
											END
										), 2
									)
		
								-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									LIMIT 1) 
								THEN
									-- Seleciona o valor parametrizado
									ROUND(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parftpcoid = tpc.tpcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											LIMIT 1)::numeric 
										-
										-- Aplica o desconto
										(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parfclioid = con.conclioid
												AND parftpcoid = tpc.tpcoid
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	
													SELECT 1
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parftpcoid = tpc.tpcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 4
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN 1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE(	
													COALESCE(
														(	
															SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = con.conclioid
															AND parftpcoid = tpc.tpcoid
															AND parfobroid = cof.cofobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 4
															AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric, 0
													) / 100
												)
											END
										), 2
									)
		
								-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									LIMIT 1) 
								THEN
									-- Seleciona o valor parametrizado
									ROUND(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parfeqcoid = con.coneqcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											LIMIT 1)::numeric 
										-
										-- Aplica o desconto
										(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parfclioid = con.conclioid
												AND parfeqcoid = con.coneqcoid
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 2
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	
													SELECT 1
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parfeqcoid = con.coneqcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 2
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN 1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE(	
													COALESCE(
														(	
															SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = con.conclioid
															AND parfeqcoid = con.coneqcoid
															AND parfobroid = cof.cofobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 2
															AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric, 0
													) / 100
												)
											END
										), 2
									)
		
								-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									LIMIT 1) 
								THEN
									-- Seleciona o valor parametrizado
									ROUND(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											LIMIT 1)::numeric 
										-
										-- Aplica o desconto
										(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parfclioid = con.conclioid
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 2
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	
													SELECT 1
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 2
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN 1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE(	
													COALESCE(
														(	
															SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = con.conclioid
															AND parfobroid = cof.cofobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 2
															AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric, 0
													) / 100
												)
											END
										), 2
									)
		
								-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT 1
									FROM parametros_faturamento
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									LIMIT 1) 
								THEN
		
									-- Seleciona o valor parametrizado
									ROUND(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parftpcoid = tpc.tpcoid
											AND parfeqcoid = con.coneqcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 3
											LIMIT 1)::numeric 
										-
										-- Aplica o desconto
										(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parftpcoid = tpc.tpcoid
												AND parfeqcoid = con.coneqcoid
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 3
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	
													SELECT 1
													FROM parametros_faturamento
													WHERE parftpcoid = tpc.tpcoid
													AND parfeqcoid = con.coneqcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 3
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN 1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE(	
													COALESCE(
														(	
															SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parftpcoid = tpc.tpcoid
															AND parfeqcoid = con.coneqcoid
															AND parfobroid = cof.cofobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 3
															AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric, 0
													) / 100
												)
											END
										), 2
									)
		
								-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
								WHEN EXISTS(	
									SELECT 1
									FROM parametros_faturamento
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									LIMIT 1) 
								THEN
									-- Seleciona o valor parametrizado
									ROUND(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parftpcoid = tpc.tpcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 3
											LIMIT 1)::numeric 
										-
										-- Aplica o desconto
										(
											(	
												SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parftpcoid = tpc.tpcoid
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 3
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	
													SELECT 1
													FROM parametros_faturamento
													WHERE parftpcoid = tpc.tpcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 3
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN 1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE(		
													COALESCE(
														(	
															SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parftpcoid = tpc.tpcoid
															AND parfobroid = cof.cofobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 3
															AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric, 0
													) / 100
												)
											END
										), 2
									)
								ELSE
									ROUND(COALESCE (cofvl_obrigacao, 0) ,2)
							END
	    				)
    				/ 30) 
    				*
					((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE) AS valor,
					0,
					FALSE,
					'0'
					
				FROM
					contrato con
					INNER JOIN contrato_obrigacao_financeira cof ON cof.cofconoid = con.connumero
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cof.cofobroid
					INNER JOIN veiculo_pedido_parceiro vppa ON vppaconoid = connumero 
					INNER JOIN ciclo_faturamento_parceiro cfp ON vppaciclo = cfpciclo
					
				WHERE 
					TRUE
					AND concsioid = 1 
					AND conno_tipo = 844
					AND conmodalidade = 'L'
					AND condt_exclusao IS NULL
					AND condt_ini_vigencia IS NOT NULL
					AND ('01' || '-' || EXTRACT('month' FROM condt_ini_vigencia) || '-' || EXTRACT('year' FROM condt_ini_vigencia))::date <= ' $parametros->dataReferencia'::date
					AND condt_ini_vigencia::date >= ((TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('$parametros->dataReferencia'::date)) || '-' || EXTRACT('year' FROM ('$parametros->dataReferencia'::date)),'DD-MM-YYYY')-(INTERVAL '1 month'))::date)
					AND ((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('$parametros->dataReferencia'::date)) || '-' || EXTRACT('year' FROM ('$parametros->dataReferencia'::date)),'DD-MM-YYYY'))::date - condt_ini_vigencia::date) > 0
					AND (	
						conequoid > 0 
						OR EXISTS (	
							SELECT 1
							FROM reativacao_cobranca_monitoramento
							WHERE rcmconoid = con.connumero                                
							AND  rcmorddt_conclusao >= (SELECT pcrdt_vigencia
														FROM periodo_carencia_reinstalacao 
														WHERE pcrdt_exclusao IS NULL)
							
							AND (rcmorddt_conclusao + ((	SELECT cast(pcrperiodo as varchar)
															FROM periodo_carencia_reinstalacao 
															WHERE pcrdt_exclusao IS NULL) || ' days')::interval
							)::date <= '$parametros->dataReferencia'::date
						)
					)					
					AND connumero NOT IN (
						SELECT pavconoid
						FROM previsao_arrecadacao_vivo
						WHERE pavconoid = connumero
							AND pavmes_referencia = $parametros->mes
							AND pavano_referencia = $parametros->ano
						GROUP BY 1
						)
					AND cofdt_termino IS NULL
					AND cofobroid = 1
					AND tpcgera_faturamento IS TRUE
					AND vppasubscription IS NOT NULL
				";
		
    	if (!empty($parametros->nomeCliente)) {
    		$sql_prorata .= " AND clinome ILIKE '%" . $parametros->nomeCliente . "%'";
    	}
		
    	//echo "Pró-Rata Monitoramento -> <br/><br/>". $sql_prorata;
        
        if (!pg_query($this->conn, $sql_prorata)) {
            throw new Exception("Falha ao inserir previsão (Arrecadação de Pró-Rata de Monitoramento).");
        }
		
	}

	// Gerar previsão de arrecadação de monitoramento
	public function inserirPrevisaoArrecadacaoMonitoramento($parametros) {
		
		$sql_monitor = "	
			INSERT INTO previsao_arrecadacao_vivo(
				pavano_referencia, -- ano selecionado pelo usuário
				pavmes_referencia, -- mês selecionado pelo usuário
				pavconoid, -- contrato
				pavobroid, -- (Códigos válidos: 1, 9 ou 23)
				pavusuoid, -- id_usuario logado
				pavsubscription, -- subscription
				pavdt_previsao, -- now()
				pavdt_vencimento, -- Calculada conforme a data de corte
				pavvl_previsao, -- Calculado
				pavvl_desconto, -- 0 (Zero)
				pavflag_processado, -- FALSE - Não processado
				pavstatus -- '0' (Zero) - Não Informado
				)	
				SELECT DISTINCT
					$parametros->ano,
					$parametros->mes,
					connumero,
					obroid,
					$parametros->idUsuario,
					vppasubscription,
					NOW(),	
					CASE 
						WHEN clitipo = 'J'
						THEN 
							CASE 
							-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v2 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
							-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v2 < cfpdia_corte)
								THEN to_date(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v2 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v2 < cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
							-- Ciclo não cadastrado
								ELSE NOW()              
							END 
						ELSE
							CASE 
							-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v1 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
							-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v1 < cfpdia_corte)
								THEN to_date(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v1 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v1 < cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
							-- Ciclo não cadastrado
								ELSE NOW()              
							END
					END AS data_vencimento, 					
					CASE
						-- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfconoid = con.connumero
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) 
						THEN
							-- Seleciona o valor parametrizado
							ROUND(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfconoid = con.connumero
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										LIMIT 1)::numeric 
									-
									-- Aplica o desconto
									(
										(	
											SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfconoid = con.connumero
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 1
											LIMIT 1	)::numeric 
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (	
												SELECT 1
												FROM parametros_faturamento
												WHERE parfconoid = con.connumero
												AND parfobroid = cof.cofobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 1
												AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1
											) 
											THEN 1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE (
												COALESCE(
													(
														SELECT parfdesconto
														FROM parametros_faturamento
														WHERE parfconoid = con.connumero
														AND parfobroid = cof.cofobroid
														AND parfdt_exclusao IS NULL
														AND parfativo = 't'
														AND parfnivel = 1
														AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
														LIMIT 1)::numeric, 0
												) / 100
											)
										END
									), 2
								)

						-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) 
						THEN
							-- Seleciona o valor parametrizado
							ROUND(
								(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									LIMIT 1)::numeric 
								-
								-- Aplica o desconto
								(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = con.conclioid
										AND parftpcoid = tpc.tpcoid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 4
										LIMIT 1)::numeric 
									*
									CASE
										-- Desconto de 100 % se estiver isento de cobrança
										WHEN EXISTS (	
											SELECT 1
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parftpcoid = tpc.tpcoid
											AND parfeqcoid = con.coneqcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) 
										THEN 1
										-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
										ELSE(	
											COALESCE(
												(
													SELECT parfdesconto
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parftpcoid = tpc.tpcoid
													AND parfeqcoid = con.coneqcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 4
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0
											) / 100
										)
									END
								), 2
							)

						-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 4
							LIMIT 1) 
						THEN
							-- Seleciona o valor parametrizado
							ROUND(
								(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 4
									LIMIT 1)::numeric 
								-
								-- Aplica o desconto
								(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = con.conclioid
										AND parftpcoid = tpc.tpcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 4
										LIMIT 1)::numeric 
									*
									CASE
										-- Desconto de 100 % se estiver isento de cobrança
										WHEN EXISTS (	
											SELECT 1
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parftpcoid = tpc.tpcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) 
										THEN 1
										-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
										ELSE(	
											COALESCE(
												(	
													SELECT parfdesconto
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parftpcoid = tpc.tpcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 4
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0
											) / 100
										)
									END
								), 2
							)

						-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) 
						THEN
							-- Seleciona o valor parametrizado
							ROUND(
								(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									LIMIT 1)::numeric 
								-
								-- Aplica o desconto
								(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = con.conclioid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										LIMIT 1)::numeric 
									*
									CASE
										-- Desconto de 100 % se estiver isento de cobrança
										WHEN EXISTS (	
											SELECT 1
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parfeqcoid = con.coneqcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) 
										THEN 1
										-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
										ELSE(	
											COALESCE(
												(	
													SELECT parfdesconto
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parfeqcoid = con.coneqcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 2
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0
											) / 100
										)
									END
								), 2
							)

						-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfclioid = con.conclioid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 2
							LIMIT 1) 
						THEN
							-- Seleciona o valor parametrizado
							ROUND(
								(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parfclioid = con.conclioid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 2
									LIMIT 1)::numeric 
								-
								-- Aplica o desconto
								(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = con.conclioid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										LIMIT 1)::numeric 
									*
									CASE
										-- Desconto de 100 % se estiver isento de cobrança
										WHEN EXISTS (	
											SELECT 1
											FROM parametros_faturamento
											WHERE parfclioid = con.conclioid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) 
										THEN 1
										-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
										ELSE(	
											COALESCE(
												(	
													SELECT parfdesconto
													FROM parametros_faturamento
													WHERE parfclioid = con.conclioid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 2
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0
											) / 100
										)
									END
								), 2
							)

						-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfeqcoid = con.coneqcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) 
						THEN

							-- Seleciona o valor parametrizado
							ROUND(
								(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parftpcoid = tpc.tpcoid
									AND parfeqcoid = con.coneqcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									LIMIT 1)::numeric 
								-
								-- Aplica o desconto
								(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parftpcoid = tpc.tpcoid
										AND parfeqcoid = con.coneqcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 3
										LIMIT 1)::numeric 
									*
									CASE
										-- Desconto de 100 % se estiver isento de cobrança
										WHEN EXISTS (	
											SELECT 1
											FROM parametros_faturamento
											WHERE parftpcoid = tpc.tpcoid
											AND parfeqcoid = con.coneqcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 3
											AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) 
										THEN 1
										-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
										ELSE(	
											COALESCE(
												(	
													SELECT parfdesconto
													FROM parametros_faturamento
													WHERE parftpcoid = tpc.tpcoid
													AND parfeqcoid = con.coneqcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 3
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0
											) / 100
										)
									END
								), 2
							)

						-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
						WHEN EXISTS(	
							SELECT 1
							FROM parametros_faturamento
							WHERE parftpcoid = tpc.tpcoid
							AND parfobroid = cof.cofobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 3
							LIMIT 1) 
						THEN
							-- Seleciona o valor parametrizado
							ROUND(
								(	
									SELECT parfvl_cobrado
									FROM parametros_faturamento
									WHERE parftpcoid = tpc.tpcoid
									AND parfobroid = cof.cofobroid
									AND parfdt_exclusao IS NULL
									AND parfativo = 't'
									AND parfnivel = 3
									LIMIT 1)::numeric 
								-
								-- Aplica o desconto
								(
									(	
										SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parftpcoid = tpc.tpcoid
										AND parfobroid = cof.cofobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 3
										LIMIT 1)::numeric 
									*
									CASE
										-- Desconto de 100 % se estiver isento de cobrança
										WHEN EXISTS (	
											SELECT 1
											FROM parametros_faturamento
											WHERE parftpcoid = tpc.tpcoid
											AND parfobroid = cof.cofobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 3
											AND '$parametros->dataReferencia' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
											LIMIT 1) 
										THEN 1
										-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
										ELSE(		
											COALESCE(
												(	
													SELECT parfdesconto
													FROM parametros_faturamento
													WHERE parftpcoid = tpc.tpcoid
													AND parfobroid = cof.cofobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 3
													AND '$parametros->dataReferencia' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
													LIMIT 1)::numeric, 0
											) / 100
										)
									END
								), 2
							)
						ELSE
							ROUND(COALESCE (cofvl_obrigacao, 0) ,2)
					END AS valor,
					0,
					FALSE,
					'0'
					
				FROM
					contrato con
					INNER JOIN contrato_obrigacao_financeira cof ON cof.cofconoid = con.connumero
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cof.cofobroid
					INNER JOIN veiculo_pedido_parceiro vppa ON vppaconoid = connumero 
					INNER JOIN ciclo_faturamento_parceiro cfp ON vppaciclo = cfpciclo
					
				WHERE 
					TRUE
					AND concsioid = 1 
					AND conno_tipo = 844
					AND conmodalidade = 'L'
					AND condt_exclusao IS NULL
					AND condt_ini_vigencia IS NOT NULL
					AND condt_ini_vigencia::date < ((TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('$parametros->dataReferencia'::date)) || '-' || EXTRACT('year' FROM ('$parametros->dataReferencia'::date)),'DD-MM-YYYY')-(INTERVAL '1 month'))::date)
					AND (	
						conequoid > 0 
						OR EXISTS (	
							SELECT 1
							FROM reativacao_cobranca_monitoramento
							WHERE rcmconoid = con.connumero                                
							AND  rcmorddt_conclusao >= (SELECT pcrdt_vigencia
														FROM periodo_carencia_reinstalacao 
														WHERE pcrdt_exclusao IS NULL)
							
							AND (rcmorddt_conclusao + ((	SELECT cast(pcrperiodo as varchar)
															FROM periodo_carencia_reinstalacao 
															WHERE pcrdt_exclusao IS NULL) || ' days')::interval
							)::date <= '$parametros->dataReferencia'::date
						)
					)					
					AND connumero NOT IN (
						SELECT pavconoid
						FROM previsao_arrecadacao_vivo
						WHERE pavconoid = connumero
							AND pavmes_referencia = $parametros->mes
							AND pavano_referencia = $parametros->ano
						GROUP BY 1
						)
					AND cofdt_termino IS NULL
					AND ((cofobroid = 1) OR (obrofgoid = 21))
					AND tpcgera_faturamento IS TRUE
					AND vppasubscription IS NOT NULL
				";
		
    	if (!empty($parametros->nomeCliente)) {
    		$sql_monitor .= " AND clinome ILIKE '%" . $parametros->nomeCliente . "%'";
    	}
    	
    	//echo "Monitoramento -> <br/><br/>". $sql_monitor;
        
        if (!pg_query($this->conn, $sql_monitor)) {
            throw new Exception("Falha ao inserir previsão (Arrecadação de Monitoramento).");
        }
		
	}

	// Gerar previsão de arrecadação de instalação
	public function inserirPrevisaoArrecadacaoInstalacao($parametros) {
		
		$sql_instal = "
			INSERT INTO previsao_arrecadacao_vivo(
				pavano_referencia, -- ano selecionado pelo usuário
				pavmes_referencia, -- mês selecionado pelo usuário
				pavconoid, -- contrato
				pavobroid, -- (Códigos válidos: 1, 9 ou 23)
				pavusuoid, -- id_usuario logado
				pavsubscription, -- subscription
				pavdt_previsao, -- now()
				pavdt_vencimento, -- Calculada conforme a data de corte
				pavvl_previsao, -- Calculado
				pavvl_desconto, -- 0 (Zero)
				pavflag_processado, -- FALSE - Não processado
				pavstatus -- '0' (Zero) - Não Informado
				)	
				SELECT DISTINCT
					$parametros->ano,
					$parametros->mes,
					connumero,
					obroid,
					$parametros->idUsuario,
					vppasubscription,
					NOW(),	
					CASE 
						WHEN clitipo = 'J'
						THEN 
							CASE 
							-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v2 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
							-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v2 < cfpdia_corte)
								THEN to_date(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v2 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v2 < cfpdia_corte)
								THEN TO_DATE(cfpdia_v2 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
							-- Ciclo não cadastrado
								ELSE NOW()              
							END 
						ELSE
							CASE 
							-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v1 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
							-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND (cfpdia_v1 < cfpdia_corte)
								THEN to_date(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v1 >= cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
							-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
								WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND (cfpdia_v1 < cfpdia_corte)
								THEN TO_DATE(cfpdia_v1 || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
							-- Ciclo não cadastrado
								ELSE NOW()              
							END
					END AS data_vencimento, 
					(SELECT COALESCE(ppagtvltx_instalacao,0) FROM proposta_pagamento WHERE ppagprpoid IN (SELECT prpoid FROM proposta WHERE prptermo=connumero))AS valor,
					0,
					FALSE,
					'0'
				FROM
					contrato con
					INNER JOIN clientes cli ON cli.clioid = con.conclioid
					INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
					INNER JOIN veiculo_pedido_parceiro vppa ON vppa.vppaconoid = con.connumero 
					INNER JOIN ciclo_faturamento_parceiro cfp ON vppa.vppaciclo = cfp.cfpciclo
					INNER JOIN ordem_servico ord ON ord.ordconnumero = con.connumero
					INNER JOIN comissao_instalacao cmi ON cmi.cmiord_serv = ord.ordoid
					INNER JOIN comissao_padrao_tecnica cpt ON cpt.cptotioid = cmi.cmiotioid
					INNER JOIN obrigacao_financeira obr ON obr.obroid = cpt.cptobroid
				WHERE 
					TRUE
					AND concsioid = 1 
					AND conno_tipo = 844
					AND conmodalidade = 'L'
					AND condt_exclusao IS NULL
					AND condt_ini_vigencia IS NOT NULL
					AND (	
						conequoid > 0 
						OR EXISTS (	
							SELECT 1
							FROM reativacao_cobranca_monitoramento
							WHERE rcmconoid = con.connumero                                
							AND  rcmorddt_conclusao >= (SELECT pcrdt_vigencia
														FROM periodo_carencia_reinstalacao 
														WHERE pcrdt_exclusao IS NULL)
							
							AND (rcmorddt_conclusao + ((	SELECT cast(pcrperiodo as varchar)
															FROM periodo_carencia_reinstalacao 
															WHERE pcrdt_exclusao IS NULL) || ' days')::interval
							)::date <= '$parametros->dataReferencia'::date
						)
					)					
					AND connumero NOT IN (
						SELECT pavconoid
						FROM previsao_arrecadacao_vivo
						WHERE pavconoid = connumero
							AND pavmes_referencia = $parametros->mes
							AND pavano_referencia = $parametros->ano
						GROUP BY 1
						)
					AND obroid = 23
					AND tpcgera_faturamento IS TRUE
					AND vppasubscription IS NOT NULL
					AND ordstatus = 3  
					AND cmiexclusao IS NULL					
					AND cmidata BETWEEN 
						((TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('$parametros->dataReferencia'::date)) || '-' || EXTRACT('year' FROM ('$parametros->dataReferencia'::date)),'DD-MM-YYYY')-(INTERVAL '1 month'))::date)
					AND 
						((TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY'))::date)
				";
		
    	if (!empty($parametros->nomeCliente)) {
    		$sql_instal .= " AND clinome ILIKE '%" . $parametros->nomeCliente . "%'";
    	}

		//echo "Instalacao -> <br/><br/>". $sql_instal;
		
        if (!pg_query($this->conn, $sql_instal)) {
            throw new Exception("Falha ao inserir previsão (Arrecadação de Instalação).");
        }
		
	}	

	// Consultar previsões
	public function consultarPrevisao($parametros) {
		
		$retorno = array();	
	
		$sql = "
            SELECT DISTINCT
                pavoid,
				TO_CHAR(pavdt_previsao, 'dd/mm/yyyy') AS dt_previsao,
				pavconoid,
				CASE 
					WHEN clitipo = 'J' THEN clino_cgc 
					ELSE clino_cpf
				END AS cli_docto,
				clinome,
				veiplaca,
				pavsubscription,
				obrobrigacao,
				pavvl_previsao,
				pavvl_desconto,
				lpad(pavmes_referencia::text, 2, '0')||'/'||pavano_referencia AS referencia,
				TO_CHAR(condt_ini_vigencia, 'dd/mm/yyyy') AS dt_ini_vigencia,
				TO_CHAR(pavdt_vencimento, 'dd/mm/yyyy') AS dt_vencimento,
				vppaciclo,				
				CASE 
					WHEN pavflag_processado = TRUE THEN 'Sim' 
					ELSE 'Não'
				END AS pavflag_processado,
				vpesstatus
            FROM
				previsao_arrecadacao_vivo
				INNER JOIN contrato ON connumero = pavconoid
				INNER JOIN clientes ON conclioid = clioid
				INNER JOIN veiculo ON conveioid = veioid
				INNER JOIN obrigacao_financeira ON obroid = pavobroid
				INNER JOIN veiculo_pedido_parceiro ON vppaconoid = pavconoid
                INNER JOIN veiculo_parceiro_evento_status ON vpescodigo = pavstatus
            WHERE
                TRUE";
		
		if (!empty($parametros->ano)) {
			$sql .= " AND pavano_referencia = $parametros->ano ";
		}
	
		if (!empty($parametros->mes)) {
			$sql .= " AND pavmes_referencia = $parametros->mes ";
		}
		
		if (!empty($parametros->nomeCliente_psq)) {
			$sql .= " AND clinome ILIKE '" . pg_escape_string($parametros->nomeCliente_psq) . "%' ";
		}
	
		if ($parametros->opcao_psq != 'NULL') {
			$sql .= " AND pavflag_processado = $parametros->opcao_psq ";
		}
	
		$sql .= "
            ORDER BY
                pavoid, clinome, pavconoid; ";
		
		//echo "Consulta -> <br/><br/>". $sql;

		if (!$resultado = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao consultar previsão.");
		}
	
		if (pg_num_rows($resultado) > 0) {
			while ($linha = pg_fetch_object($resultado)) {
				array_push($retorno, $linha);
			}
		} else {
			throw new Exception("Nenhum registro encontrado.");
		}
	
		return $retorno;
	}	
	
	//Processar previsões
	public function processarPrevisao($parametros) {
		
		$sql = "
            UPDATE 
				previsao_arrecadacao_vivo
            SET
				pavflag_processado = TRUE,
				pavusuoid = $parametros->idUsuario
            WHERE
                TRUE";
		
		if (!empty($parametros->ano)) {
			$sql .= " AND pavano_referencia = $parametros->ano ";
		}
	
		if (!empty($parametros->mes)) {
			$sql .= " AND pavmes_referencia = $parametros->mes ";
		}
		
		if (!empty($parametros->nomeCliente_psq)) {
			$sql .= " AND pavconoid IN ( 
						SELECT 
							connumero
						FROM
							contrato
						WHERE
							conclioid IN (
								SELECT 
									clioid
								FROM
									clientes
								WHERE
									clinome = '" . pg_escape_string($parametros->nomeCliente_psq) . "'
							)
						) ";
		}
		
		//echo "Update -> <br/><br/>". $sql;
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao processar previsão.");
		}
		if (pg_affected_rows($res) == 0){
			throw new Exception("Nenhum registro alterado. Verifique os filtros informados.");
		}		
		
	}

	//Excluir previsões
	public function excluirPrevisao($parametros) {
	
		$sql = "
			DELETE
			FROM
				previsao_arrecadacao_vivo
			WHERE
				pavflag_processado <> TRUE";
	
		if (!empty($parametros->ano)) {
		$sql .= " AND pavano_referencia = $parametros->ano ";
		}
	
		if (!empty($parametros->mes)) {
		$sql .= " AND pavmes_referencia = $parametros->mes ";
		}
	
		if (!empty($parametros->nomeCliente_psq)) {
		$sql .= " 
			AND pavconoid IN (
				SELECT
					connumero
				FROM
					contrato
				WHERE
					conclioid IN (
						SELECT
							clioid
						FROM
							clientes
						WHERE
							clinome = '" . pg_escape_string($parametros->nomeCliente_psq) . "'
					)
				) ";
		}
	
		//echo "Delete -> <br/><br/>". $sql;
	
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao excluir previsão.");
		}
		if (pg_affected_rows($res) == 0){
			throw new Exception("Nenhum registro excluído. Verifique os filtros informados.");
		}
	
	}

	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transação
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transação
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}


}