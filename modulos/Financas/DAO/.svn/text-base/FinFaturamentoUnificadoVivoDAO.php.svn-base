<?php

/**
 * Classe FinFaturamentoUnificadoVivoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Financas
 * @author   Ricardo Bonfim <ricardo.bonfim@meta.com.br>
 *
 */
class FinFaturamentoUnificadoVivoDAO {

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

    public function inserirDadosExecucao($tipo, stdClass $dados) {

        $parametros = array(
            "01/" . $dados->dataReferencia,
            $dados->tipoContrato,
            $dados->servicosFaturados,
            $dados->tipoPessoa,
            $dados->nome,
            $dados->documentoCliente,
            str_replace('-', '', $dados->placa),
            $dados->idUsuario,
            $dados->obrigacoesFinanceiras
        );

        $parametrosFormatados = implode('|', $parametros);

        // Inicia controle de faturamento concorrente
        $sql = "INSERT INTO execucao_faturamento_vivo (
                    efvusuoid,
                    efvtipo_processo,
                    efvporcentagem,
                    efvparametros
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
            throw new ErrorException('Falha ao preparar faturamento.');
        }
    }

    /**
     * Formatar dados (CPF||CNPJ)
     *
     * @param string $tipo  tipo doc
     * @param string $valor valor do doc
     *
     * @return string $valor
     */
    public function formatarDados($tipo, $valor) {

        if ($tipo == "cpf" && $valor != "") {
            $valor = str_pad($valor, 11, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 3) . "." . substr($valor, 3, 3) . "." . substr($valor, 6, 3) . "-" . substr($valor, 9, 2);
        }

        if ($tipo == "cnpj" && $valor != "") {
            $valor = str_pad($valor, 14, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 2) . "." . substr($valor, 2, 3) . "." . substr($valor, 5, 3) . "/" . substr($valor, 8, 4) . "-" . substr($valor, 12, 2);
        }
    }

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

    public function consultarResumo($parametros) {

        $retorno = array();

        $sql = "
            SELECT
                obroid AS id_obrigacao,
                obrobrigacao AS nome_obrigacao,
                COUNT(prfvconnumero) AS quantidade_contratos,
                SUM(prfvvalor) AS valor_faturamento
            FROM
                previsao_faturamento_vivo
                INNER JOIN obrigacao_financeira on obroid = prfvobroid
            WHERE
                1 = 1";

        /* 	
        if (!empty($parametros->dataReferencia)) {
            $dataReferencia = "01/" . $parametros->dataReferencia;
            $sql .= " AND EXTRACT (MONTH FROM prfvdt_referencia) = EXTRACT (MONTH FROM '" . $dataReferencia . "'::TIMESTAMP)";
            $sql .= " AND EXTRACT (YEAR FROM prfvdt_referencia) = EXTRACT (YEAR FROM '" . $dataReferencia . "'::TIMESTAMP)";
        }
		 */
        
        if (!empty($parametros->nome)) {
            $sql .= " AND prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
        }

        if (!empty($parametros->documentoCliente)) {
            $sql .= " AND prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }

        if (!empty($parametros->placa)) {
            $placa = str_replace('-', '', $parametros->placa);
            $sql .= " AND prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $placa . "')";
        }

        $sql .= "
            GROUP BY
                obroid,
                obrobrigacao
            ORDER BY
                obrobrigacao";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }
        } else {
            throw new Exception("Nenhum registro encontrado.");
        }

        return $retorno;
    }

    public function deletarPrevisoes($parametros = null) {

        $sql = "
            DELETE FROM
                previsao_faturamento_vivo";

        if ($parametros != NULL && is_object($parametros)) {
            $sql .= "
                    WHERE
                        1 = 1";

            if (!empty($parametros->dataReferencia)) {
                $sql .= " AND EXTRACT (MONTH FROM prfvdt_referencia) = EXTRACT (MONTH FROM '" . $parametros->dataReferencia . "'::TIMESTAMP)";
                $sql .= " AND EXTRACT (YEAR FROM prfvdt_referencia) = EXTRACT (YEAR FROM '" . $parametros->dataReferencia . "'::TIMESTAMP)";
            }

            if (!empty($parametros->nome)) {
                $sql .= " AND prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
            }

            if (!empty($parametros->documentoCliente)) {
                $sql .= " AND prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
            }

            if (!empty($parametros->placa)) {
                $sql .= " AND prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $parametros->placa . "')";
            }
        }

        $resultado = pg_query($this->conn, $sql);

        if ($resultado) {
            return true;
        } else {
            throw new Exception("Falha ao deletar previsão.");
        }
    }
    


    public function inserirPrevisaoProRataLocacoesEquipamentos($parametros) {
    
    	$sql_prorata = "
			INSERT INTO previsao_faturamento_vivo (
                prfvconnumero
                ,prfvobroid
                ,prfvclioid
                ,prfvvalor
                ,prfvdt_referencia
                ,prfvtipo_obrigacao
                ,prfvsubscription
                ,prfvconta
                ,prfvciclo
            )
            SELECT DISTINCT
                connumero AS contrato,
    
                obrprorata,
    
                conclioid AS cliente,
    
                -- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
                CASE
					WHEN EXISTS(SELECT parfvl_cobrado
						FROM parametros_faturamento
						WHERE parfconoid = connumero
						AND parfobroid = obroid
						AND parfdt_exclusao IS NULL
						AND parfativo = 't'
						AND parfnivel = 1
						LIMIT 1)
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfconoid = connumero
										AND parfobroid = obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										LIMIT 1)::numeric
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfconoid = connumero
											AND parfobroid = obroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 1
											LIMIT 1)::numeric
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parfconoid = connumero
												AND parfobroid = obroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 1
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1)
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfconoid = connumero
															AND parfobroid = obroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 1
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0)
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)
    
					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = conclioid
                        AND parftpcoid = tpcoid
                        AND parfobroid = obroid
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 4
                        LIMIT 1)
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = conclioid
										AND parftpcoid = tpcoid
										AND parfobroid = obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 4
										LIMIT 1)::numeric
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = conclioid
											AND parftpcoid = tpcoid
											AND parfobroid = obroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											LIMIT 1)::numeric
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parfclioid = conclioid
												AND parftpcoid = tpcoid
												AND parfobroid = obroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1)
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = conclioid
															AND parftpcoid = tpcoid
															AND parfobroid = obroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 4
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0)
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)
    
					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = conclioid
                        AND parfobroid_multiplo && array [obroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        LIMIT 1)
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = conclioid
										AND parfobroid_multiplo && array [obroid]
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										LIMIT 1)::numeric
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = conclioid
											AND parfobroid_multiplo && array [obroid]
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											LIMIT 1)::numeric
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parfclioid = conclioid
												AND parfobroid_multiplo && array [obroid]
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 2
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1)
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = conclioid
															AND parfobroid_multiplo && array [obroid]
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 2
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0)
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)
    
					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT 1
                        FROM parametros_faturamento
                        WHERE parftpcoid = tpcoid
                        AND parfobroid = obroid
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 3
                        LIMIT 1)
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parftpcoid = tpcoid
										AND parfobroid = obroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 3
										LIMIT 1)::numeric
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parftpcoid = tpcoid
											AND parfobroid = obroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 3
											LIMIT 1)::numeric
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parftpcoid = tpcoid
												AND parfobroid = obroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 3
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1)
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parftpcoid = tpcoid
															AND parfobroid = obroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 3
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0)
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)
					ELSE
						ROUND(
							(
								(
								CASE
									WHEN COALESCE(cpagvl_servico, 0::numeric) > 0::numeric
									THEN
										-- Valor com desconto (Se completou vigência)
										CASE -- RN39
											WHEN ('" . $parametros->dataReferencia . "'::date > (condt_ini_vigencia + ('1 MONTH'::INTERVAL * conprazo_contrato))::date)
											THEN
												COALESCE(cpagvl_servico, 0) - (COALESCE(cpagvl_servico,0) * COALESCE((SELECT cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
											ELSE
												COALESCE(cpagvl_servico, 0)
										END
									ELSE
										-- Valor com desconto (Se completou vigência)
										CASE -- RN39
											WHEN ('" . $parametros->dataReferencia . "'::date > (condt_ini_vigencia + ('1 MONTH'::INTERVAL * conprazo_contrato))::date)
											THEN
												COALESCE(cpaghabilitacao, 0) - (COALESCE(cpaghabilitacao,0) * COALESCE((SELECT cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
											ELSE
												COALESCE(cpaghabilitacao, 0)
										END
								END)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)
                END AS valor,
    
                '" . $parametros->dataReferencia . "'::date AS data_referencia,
    
                'L' AS tipo_obrigacao,
    
                vppasubscription AS subscription_id,
    
                vppaconta AS conta,
    
                vppaciclo AS ciclo
    
            FROM
                contrato con
                INNER JOIN contrato_pagamento cpag ON cpagconoid = connumero
                INNER JOIN cond_pgto_venda cpv ON cpagcpvoid = cpvoid
                INNER JOIN tipo_contrato tpc ON tpcoid = conno_tipo
                INNER JOIN equipamento_classe eqc ON eqcoid = coneqcoid
                INNER JOIN clientes cli ON clioid = conclioid
                LEFT JOIN veiculo vei ON veioid = conveioid
                INNER JOIN obrigacao_financeira obr ON obroid = eqcobroid
                INNER JOIN veiculo_pedido_parceiro vppa ON vppaconoid = connumero AND vppasubscription != ''
				INNER JOIN ciclo_faturamento_parceiro cfp ON vppaciclo = cfpciclo
            WHERE
                conno_tipo = 844 -- Tipo Contrato VIVO -> BR1
                AND conmodalidade = 'L' -- Locação -> BR1 -> BR2a
                AND condt_exclusao IS NULL -- Não excluídos -> BR1
                AND concsioid = 1 -- Ativo -> BR1
				AND ('01' || '/' || EXTRACT('month' FROM condt_ini_vigencia) || '/' || EXTRACT('year' FROM condt_ini_vigencia))::date <= '" . $parametros->dataReferencia . "'::date -- Data de inicio de vigência menor/igual ao mês/ano de refrência -> BR1
                AND connumero NOT IN (	SELECT
                                                nfi2.nficonoid
                                            FROM
                                                nota_fiscal_item nfi2
                                                INNER JOIN nota_fiscal nfl2 ON (nfl2.nflno_numero = nfi2.nfino_numero AND nfl2.nflserie = nfi2.nfiserie)
                                            WHERE
                                                nfi2.nficonoid = connumero
                                                AND nfi2.nfiobroid = obrprorata
                                                AND nfi2.nfidt_referencia = '" . $parametros->dataReferencia . "'
                                                AND nfl2.nfldt_cancelamento IS NULL) -- Não deve possuir Contrato faturado para a mesma Obrigação Financeira -> BR1 -> BR2d -> BR2g
				AND obrprorata > 0 -- Obrigação Financeira vinculada -> BR2c
				AND (condt_ini_vigencia::DATE >= (TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY') - (INTERVAL '1 month'))::DATE) -- Regra enviada via Skype
				AND ((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE) > 0 -- Não permitir valores negativos
                AND conequoid > 0 -- ??? -> BR?
    			";

        if (!empty($parametros->documentoCliente)) {
            $sql_prorata .= " AND (clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }
        if (!empty($parametros->nome)) {
            $sql_prorata .= " AND clinome ILIKE '%" . $parametros->nome . "%'";
        }

        if (!empty($parametros->placa)) {
            $sql_prorata .= " AND conveioid IN ( SELECT veioid FROM veiculo WHERE veiplaca ilike '" . $parametros->placa . "')";
        }
        
		//echo '1 P-R -><br/>'.$sql_prorata;
        //exit;
        
        if (!pg_query($this->conn, $sql_prorata)) {
            throw new Exception("Falha ao atualizar Pró-Rata de Locacoes Equipamentos.");
        }
    }

    public function inserirPrevisaoLocacoesEquipamentos($parametros) {

    	$sql = "
            INSERT INTO previsao_faturamento_vivo (
                prfvconnumero
                ,prfvobroid
                ,prfvclioid
                ,prfvvalor
                ,prfvdt_referencia
                ,prfvtipo_obrigacao
                ,prfvsubscription
                ,prfvconta
                ,prfvciclo
            )
            SELECT DISTINCT
                con.connumero AS contrato,

                eqc.eqcobroid AS obrigacao_financeira,

                conclioid AS cliente,

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
                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
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
                                        AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
                                            LIMIT 1)::numeric, 0) / 100)

                                END
                                ), 2)

                -- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
                WHEN EXISTS(	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfobroid_multiplo && array [obr.obroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        LIMIT 1) THEN

                    -- Seleciona o valor parametrizado
                    ROUND((	SELECT parfvl_cobrado
                            FROM parametros_faturamento
                            WHERE parfclioid = con.conclioid
                            AND parfobroid_multiplo && array [obr.obroid]
                            AND parfdt_exclusao IS NULL
                            AND parfativo = 't'
                            AND parfnivel = 2
                            LIMIT 1)::numeric -

                    -- Aplica o desconto
                    ((	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfobroid_multiplo && array [obr.obroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        LIMIT 1)::numeric *

                    CASE
                    -- Desconto de 100 % se estiver isento de cobrança
                    WHEN EXISTS (	SELECT 1
                            FROM parametros_faturamento
                            WHERE parfclioid = con.conclioid
                            AND parfobroid_multiplo && array [obr.obroid]
                            AND parfdt_exclusao IS NULL
                            AND parfativo = 't'
                            AND parfnivel = 2
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
                            LIMIT 1) THEN
                        1
                    -- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
                    ELSE
                        (	COALESCE((	SELECT parfdesconto
                                    FROM parametros_faturamento
                                    WHERE parfclioid = con.conclioid
                                    AND parfobroid_multiplo && array [obr.obroid]
                                    AND parfdt_exclusao IS NULL
                                    AND parfativo = 't'
                                    AND parfnivel = 2
                                    AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
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
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
                                LIMIT 1), 0)::numeric / 100)
                    END
                    ), 2)
                ELSE
                    ROUND(	CASE
                        WHEN COALESCE(cpag.cpagvl_servico, 0::numeric) > 0::numeric THEN
                            -- Valor com desconto (Se completou vigência)
                            CASE
                            WHEN ('" . $parametros->dataReferencia . "'::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
                                COALESCE(cpag.cpagvl_servico, 0) - (COALESCE(cpag.cpagvl_servico,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
                            ELSE
                                COALESCE(cpag.cpagvl_servico, 0)
                            END
                        ELSE
                            -- Valor com desconto (Se completou vigência)
                            CASE
                            WHEN ('" . $parametros->dataReferencia . "'::date > (con.condt_ini_vigencia + ('1 MONTH'::INTERVAL * con.conprazo_contrato))::date) THEN -- RN39
                                COALESCE(cpag.cpaghabilitacao, 0) - (COALESCE(cpag.cpaghabilitacao,0) * COALESCE((SELECT cpag.cpagpercentual_desconto_locacao FROM contrato_pagamento WHERE cpagconoid=connumero LIMIT 1) / 100, 0))
                            ELSE
                                COALESCE(cpag.cpaghabilitacao, 0)
                            END
                        END, 2)
                END AS valor,

                '" . $parametros->dataReferencia . "'::date AS data_referencia,

                'L' AS tipo_obrigacao,

                vppasubscription AS subscription_id,

                vppaconta AS conta,

                vppaciclo AS ciclo

            FROM
                contrato con
                INNER JOIN contrato_pagamento cpag ON cpag.cpagconoid = con.connumero
                INNER JOIN cond_pgto_venda cpv ON cpagcpvoid = cpvoid
                INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
                INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
                INNER JOIN clientes cli ON cli.clioid = con.conclioid
                LEFT JOIN veiculo vei ON vei.veioid = con.conveioid
                INNER JOIN obrigacao_financeira obr ON obr.obroid = eqc.eqcobroid
                INNER JOIN veiculo_pedido_parceiro ON vppaconoid = connumero AND vppasubscription != ''
				INNER JOIN ciclo_faturamento_parceiro cfp ON vppaciclo = cfpciclo
            WHERE
                conno_tipo = 844
                AND con.condt_ini_vigencia < '" . $parametros->dataReferencia . "'::date
                AND con.conmodalidade = 'L' -- Locação Fixo por enquanto
                AND con.condt_exclusao IS NULL
                AND con.concsioid = 1 -- Ativo
                AND (con.conequoid > 0
                        OR EXISTS (	SELECT 1
                                    FROM reativacao_cobranca_monitoramento
                                    WHERE rcmconoid = con.connumero
                                    AND  rcmorddt_conclusao >= (SELECT pcrdt_vigencia
                                                                FROM periodo_carencia_reinstalacao
                                                                WHERE pcrdt_exclusao IS NULL)

                                    AND (rcmorddt_conclusao + ((	SELECT cast(pcrperiodo as varchar)
                                                                    FROM periodo_carencia_reinstalacao
                                                                    WHERE pcrdt_exclusao IS NULL) || ' days')::interval
                                        )::date <= '" . $parametros->dataReferencia . "'::date
                                    )
                    )
                AND con.connumero NOT IN (	SELECT
                                                nfi2.nficonoid
                                            FROM
                                                nota_fiscal_item nfi2
                                                INNER JOIN nota_fiscal nfl2 ON (nfl2.nflno_numero = nfi2.nfino_numero AND nfl2.nflserie = nfi2.nfiserie)
                                            WHERE
                                                nfi2.nficonoid = con.connumero
                                                AND nfi2.nfiobroid = obr.obroid
                                                AND nfi2.nfidt_referencia = '" . $parametros->dataReferencia . "'
                                                AND nfl2.nfldt_cancelamento IS NULL)
                AND cpvparcela > (	SELECT
                                        count(nfloid)
                                    FROM
                                        nota_fiscal_item nfi4, nota_fiscal nfl4
                                    WHERE
                                        nfl4.nflserie = 'V'
                                        AND nfl4.nflno_numero = nfi4.nfino_numero
                                        AND nfl4.nflserie = nfi4.nfiserie
                                        AND nfi4.nficonoid = con.connumero
                                        AND nfi4.nfiobroid = obr.obroid
                                        AND nfl4.nfldt_cancelamento IS NULL)
                AND (condt_ini_vigencia::DATE < (TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY')- (INTERVAL '1 month'))::DATE)";
                                                		
        if (!empty($parametros->documentoCliente)) {
            $sql .= " AND (clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }
        if (!empty($parametros->nome)) {
            $sql .= " AND clinome ILIKE '%" . $parametros->nome . "%'";
        }

        if (!empty($parametros->placa)) {
            $sql .= " AND conveioid IN ( SELECT veioid FROM veiculo WHERE veiplaca ilike '" . $parametros->placa . "')";
        }
        
		//echo '2 <br/>'.$sql;
        //exit;
        
        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao atualizar Locacoes Equipamentos.");
        }
    }

    public function inserirPrevisaoProRataLocacoesAcessorios($parametros) {

        $sql_prorata = "
            INSERT INTO previsao_faturamento_vivo (
                prfvconnumero
                ,prfvobroid
                ,prfvclioid
                ,prfvvalor
                ,prfvdt_referencia
                ,prfvtipo_obrigacao
                ,prfvsubscription
                ,prfvconta
                ,prfvciclo
            )
            SELECT DISTINCT
                connumero AS contrato,
    
                obrprorata AS obrigacao_financeira,

                conclioid AS cliente,

                -- Busca valor parametrizado para o contrato (Finanças -> Parametros Faturamento)
                CASE
					WHEN EXISTS(SELECT parfvl_cobrado
							FROM parametros_faturamento
							WHERE parfconoid = connumero
							AND parfobroid = consobroid
							AND parfdt_exclusao IS NULL
							AND parfativo = 't'
							AND parfnivel = 1
							LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfconoid = connumero
										AND parfobroid = consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 1
										LIMIT 1)::numeric 
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfconoid = connumero
											AND parfobroid = consobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 1
											LIMIT 1)::numeric 
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parfconoid = connumero
												AND parfobroid = consobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 1
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1) 
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(	
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfconoid = connumero
															AND parfobroid = consobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 1
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0) 
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato e classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = conclioid
                        AND parftpcoid = tpcoid
                        AND parfeqcoid = coneqcoid
                        AND parfobroid = consobroid
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 4
                        LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = conclioid
										AND parftpcoid = tpcoid
										AND parfeqcoid = coneqcoid
										AND parfobroid = consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 4
										LIMIT 1)::numeric 
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = conclioid
											AND parftpcoid = tpcoid
											AND parfeqcoid = coneqcoid
											AND parfobroid = consobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 4
											LIMIT 1)::NUMERIC 
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parfclioid = conclioid
												AND parftpcoid = tpcoid
												AND parfeqcoid = coneqcoid
												AND parfobroid = consobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1) 
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(	
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = conclioid
															AND parftpcoid = tpcoid
															AND parfeqcoid = coneqcoid
															AND parfobroid = consobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 4
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0) 
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)

					-- Busca valor parametrizado para o cliente associado ao tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = conclioid
                        AND parftpcoid = tpcoid
                        AND parfobroid = consobroid
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 4
                        AND parfeqcoid IS NULL
                        LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = conclioid
										AND parftpcoid = tpcoid
										AND parfobroid = consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 4
										AND parfeqcoid IS NULL
										LIMIT 1)::numeric 
										-- Aplica o desconto
										-
										(
											(SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parfclioid = conclioid
												AND parftpcoid = tpcoid
												AND parfobroid = consobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 4
												AND parfeqcoid IS NULL
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (	SELECT 1
														FROM parametros_faturamento
														WHERE parfclioid = conclioid
														AND parftpcoid = tpcoid
														AND parfobroid = consobroid
														AND parfdt_exclusao IS NULL
														AND parfativo = 't'
														AND parfnivel = 4
														AND parfeqcoid IS NULL
														AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
														LIMIT 1) 
												THEN
													1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE
													(
														COALESCE(
															(SELECT parfdesconto
																FROM parametros_faturamento
																WHERE parfclioid = conclioid
																AND parftpcoid = tpcoid
																AND parfobroid = consobroid
																AND parfdt_exclusao IS NULL
																AND parfativo = 't'
																AND parfnivel = 4
																AND parfeqcoid IS NULL
																AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
																LIMIT 1)::numeric
														, 0) 
													/ 100)
											END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)

					-- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = conclioid
                        AND parfeqcoid = coneqcoid
                        AND parfobroid_multiplo && array [consobroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = conclioid
										AND parfeqcoid = coneqcoid
										AND parfobroid_multiplo && array [consobroid]
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										LIMIT 1)::numeric
									-- Aplica o desconto 
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = conclioid
											AND parfeqcoid = coneqcoid
											AND parfobroid_multiplo && array [consobroid]
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											LIMIT 1)::numeric 
										*

										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (	SELECT 1
													FROM parametros_faturamento
													WHERE parfclioid = conclioid
													AND parfeqcoid = coneqcoid
													AND parfobroid_multiplo && array [consobroid]
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 2
													AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(	
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = conclioid
															AND parfeqcoid = coneqcoid
															AND parfobroid_multiplo && array [consobroid]
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 2
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0) 
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)

					-- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = conclioid
                        AND parfobroid_multiplo && array [consobroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        AND parfeqcoid IS NULL
                        LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parfclioid = conclioid
										AND parfobroid_multiplo && array [consobroid]
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 2
										AND parfeqcoid IS NULL
										LIMIT 1)::numeric 
									-- Aplica o desconto
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parfclioid = conclioid
											AND parfobroid_multiplo && array [consobroid]
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 2
											AND parfeqcoid IS NULL
											LIMIT 1)::numeric 
										*
										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parfclioid = conclioid
												AND parfobroid_multiplo && array [consobroid]
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 2
												AND parfeqcoid IS NULL
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1) 
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(	
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parfclioid = conclioid
															AND parfobroid_multiplo && array [consobroid]
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 2
															AND parfeqcoid IS NULL
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::numeric
													, 0) 
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)

					-- Busca valor parametrizado para o tipo de contrato associado a classe de equipamento (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT 1
                        FROM parametros_faturamento
                        WHERE parftpcoid = tpcoid
                        AND parfeqcoid = coneqcoid
                        AND parfobroid = consobroid
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 3
                        LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parftpcoid = tpcoid
										AND parfeqcoid = coneqcoid
										AND parfobroid = consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 3
										LIMIT 1)::numeric 
										-- Aplica o desconto
										-
										(
											(SELECT parfvl_cobrado
												FROM parametros_faturamento
												WHERE parftpcoid = tpcoid
												AND parfeqcoid = coneqcoid
												AND parfobroid = consobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 3
												LIMIT 1)::numeric 
											*
											CASE
												-- Desconto de 100 % se estiver isento de cobrança
												WHEN EXISTS (SELECT 1
													FROM parametros_faturamento
													WHERE parftpcoid = tpcoid
													AND parfeqcoid = coneqcoid
													AND parfobroid = consobroid
													AND parfdt_exclusao IS NULL
													AND parfativo = 't'
													AND parfnivel = 3
													AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
													LIMIT 1) 
												THEN
													1
												-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
												ELSE
													(	
														COALESCE(
															(SELECT parfdesconto
																FROM parametros_faturamento
																WHERE parftpcoid = tpcoid
																AND parfeqcoid = coneqcoid
																AND parfobroid = consobroid
																AND parfdt_exclusao IS NULL
																AND parfativo = 't'
																AND parfnivel = 3
																AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
																LIMIT 1)::NUMERIC
														, 0) 
													/ 100)
											END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)

					-- Busca valor parametrizado para o tipo de contrato (Finanças -> Parametros Faturamento)
					WHEN EXISTS(SELECT 1
                        FROM parametros_faturamento
                        WHERE parftpcoid = tpcoid
                        AND parfobroid = consobroid
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 3
                        AND parfeqcoid IS NULL
                        LIMIT 1) 
					THEN
						-- Seleciona o valor parametrizado
						ROUND(
							(
								(
									(SELECT parfvl_cobrado
										FROM parametros_faturamento
										WHERE parftpcoid = tpcoid
										AND parfobroid = consobroid
										AND parfdt_exclusao IS NULL
										AND parfativo = 't'
										AND parfnivel = 3
										AND parfeqcoid IS NULL
										LIMIT 1)::numeric
									-- Aplica o desconto 
									-
									(
										(SELECT parfvl_cobrado
											FROM parametros_faturamento
											WHERE parftpcoid = tpcoid
											AND parfobroid = consobroid
											AND parfdt_exclusao IS NULL
											AND parfativo = 't'
											AND parfnivel = 3
											AND parfeqcoid IS NULL
											LIMIT 1)::NUMERIC 
										*

										CASE
											-- Desconto de 100 % se estiver isento de cobrança
											WHEN EXISTS (SELECT 1
												FROM parametros_faturamento
												WHERE parftpcoid = tpcoid
												AND parfobroid = consobroid
												AND parfdt_exclusao IS NULL
												AND parfativo = 't'
												AND parfnivel = 3
												AND parfeqcoid IS NULL
												AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
												LIMIT 1) 
											THEN
												1
											-- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
											ELSE
												(	
													COALESCE(
														(SELECT parfdesconto
															FROM parametros_faturamento
															WHERE parftpcoid = tpcoid
															AND parfobroid = consobroid
															AND parfdt_exclusao IS NULL
															AND parfativo = 't'
															AND parfnivel = 3
															AND parfeqcoid IS NULL
															AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
															LIMIT 1)::NUMERIC
													, 0) 
												/ 100)
										END
									)
								)
							-- Calcula o valor diário
							/30)
							*
							-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
							((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)
						, 2)
                ELSE
                    -- Valor com desconto (Se completou vigência)
                    ROUND(
						(
							COALESCE(consvalor, 0)
							-- Calcula o valor diário
						/30)
						*
						-- Calcula o valor  a ser cobrado de acordo com o início da vigência (PRO-RATA)
						((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE)	
					, 2)
                END AS valor,

                '" . $parametros->dataReferencia . "'::date AS data_referencia,

                'L' AS tipo_obrigacao,

                vppasubscription AS subscription_id,

                vppaconta AS conta,

                vppaciclo AS ciclo
            FROM
                contrato con
                INNER JOIN clientes cli ON clioid = conclioid
                INNER JOIN veiculo vei ON veioid = conveioid
                INNER JOIN tipo_contrato tpc ON tpcoid = conno_tipo
                INNER JOIN contrato_servico cons ON consconoid = connumero
                INNER JOIN obrigacao_financeira obr ON obroid = consobroid
                INNER JOIN equipamento_classe eqc ON eqcoid = coneqcoid
                INNER JOIN veiculo_pedido_parceiro ON vppaconoid = connumero AND vppasubscription != ''
				INNER JOIN ciclo_faturamento_parceiro cfp ON vppaciclo = cfpciclo
            WHERE
                conno_tipo = 844 -- Tipo Contrato VIVO -> BR1
                AND condt_exclusao IS NULL -- Não excluídos -> BR1
                AND concsioid = 1 -- Ativo -> BR1
                AND conequoid > 0 -- Equipamento Vinculado -> BR1
                AND consinstalacao > '01/02/2013'::date
                AND conssituacao = 'L' -- Locação -> BR1
                AND consiexclusao IS NULL -- Não excluídos -> BR1
                AND obroid != 90
				AND obrprorata > 0 -- Obrigação Financeira vinculada -> BR3c
				AND ('01' || '/' || EXTRACT('month' FROM condt_ini_vigencia) || '/' || EXTRACT('year' FROM condt_ini_vigencia))::date <= '" . $parametros->dataReferencia . "'::date -- Data de inicio de vigência menor/igual ao mês/ano de refrência -> BR1
                AND ('" . $parametros->dataReferencia . "'::date - condt_ini_vigencia::date) > 0
                AND NOT EXISTS (SELECT
                                    nfi.nficonoid
                                FROM
                                    nota_fiscal_item nfi
                                    INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
                                WHERE
                                    (nfi.nficonoid = connumero OR nfi.nficonoid = connumero_antigo)
                                    AND nfi.nfiobroid = obrprorata
                                    AND nfi.nfidt_referencia = '" . $parametros->dataReferencia . "'
                                    AND nfl.nfldt_cancelamento IS NULL) -- Não deve possuir Contrato faturado para a mesma Obrigação Financeira -> BR1 -> BR3d -> BR3g
				AND (condt_ini_vigencia::DATE >= (TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY') - (INTERVAL '1 month'))::DATE) -- Regra enviada via Skype
				AND ((TO_DATE(cfpdia_corte || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY'))::DATE - condt_ini_vigencia::DATE) > 0 -- Não permitir valores negativos";

        if (!empty($parametros->documentoCliente)) {
            $sql_prorata .= " AND (clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }
        if (!empty($parametros->nome)) {
            $sql_prorata .= " AND clinome ILIKE '%" . $parametros->nome . "%'";
        }

        if (!empty($parametros->placa)) {
            $sql_prorata .= " AND conveioid IN ( SELECT veioid FROM veiculo WHERE veiplaca ilike '" . $parametros->placa . "')";
        }

        //echo '3  P-R -><br/>'.$sql_prorata;
        //exit;
        
        if (!pg_query($this->conn, $sql_prorata)) {
            throw new Exception("Falha ao atualizar Locacoes Acessorios.");
        }
    }

    public function inserirPrevisaoLocacoesAcessorios($parametros) {
    
    	$sql = "
            INSERT INTO previsao_faturamento_vivo (
                prfvconnumero
                ,prfvobroid
                ,prfvclioid
                ,prfvvalor
                ,prfvdt_referencia
                ,prfvtipo_obrigacao
                ,prfvsubscription
                ,prfvconta
                ,prfvciclo
            )
            SELECT DISTINCT
                con.connumero AS contrato,
    
                obr.obroid AS obrigacao_financeira,
    
                con.conclioid AS cliente,
    
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
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                    AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
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
                        LIMIT 1)::NUMERIC *
    
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
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
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
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
                                LIMIT 1)::numeric, 0) / 100)
    
                    END
                    ), 2)
    
                -- Busca valor parametrizado para o cliente associado a classe de equipamento (Finanças -> Parametros Faturamento)
                WHEN EXISTS(	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfeqcoid = con.coneqcoid
                        AND parfobroid_multiplo && array [cons.consobroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        LIMIT 1) THEN
    
                    -- Seleciona o valor parametrizado
                    ROUND((	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfeqcoid = con.coneqcoid
                        AND parfobroid_multiplo && array [cons.consobroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        LIMIT 1)::numeric -
    
                    -- Aplica o desconto
                    ((	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfeqcoid = con.coneqcoid
                        AND parfobroid_multiplo && array [cons.consobroid]
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
                            AND parfobroid_multiplo && array [cons.consobroid]
                            AND parfdt_exclusao IS NULL
                            AND parfativo = 't'
                            AND parfnivel = 2
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
                            LIMIT 1) THEN
                        1
                    -- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
                    ELSE
                        (	COALESCE((	SELECT parfdesconto
                                    FROM parametros_faturamento
                                    WHERE parfclioid = con.conclioid
                                    AND parfeqcoid = con.coneqcoid
                                    AND parfobroid_multiplo && array [cons.consobroid]
                                    AND parfdt_exclusao IS NULL
                                    AND parfativo = 't'
                                    AND parfnivel = 2
                                    AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
                                    LIMIT 1), 0)::numeric / 100)
                    END
                    ), 2)
    
                -- Busca valor parametrizado para o cliente (Finanças -> Parametros Faturamento)
                WHEN EXISTS(	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfobroid_multiplo && array [cons.consobroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        AND parfeqcoid IS NULL
                        LIMIT 1) THEN
    
                    -- Seleciona o valor parametrizado
                    ROUND((	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfobroid_multiplo && array [cons.consobroid]
                        AND parfdt_exclusao IS NULL
                        AND parfativo = 't'
                        AND parfnivel = 2
                        AND parfeqcoid IS NULL
                        LIMIT 1)::numeric -
    
                    -- Aplica o desconto
                    ((	SELECT parfvl_cobrado
                        FROM parametros_faturamento
                        WHERE parfclioid = con.conclioid
                        AND parfobroid_multiplo && array [cons.consobroid]
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
                            AND parfobroid_multiplo && array [cons.consobroid]
                            AND parfdt_exclusao IS NULL
                            AND parfativo = 't'
                            AND parfnivel = 2
                            AND parfeqcoid IS NULL
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
                            LIMIT 1) THEN
                        1
                    -- Ou o desconto parametrizado (0.0 caso não haja desconto parametrizado para o período)
                    ELSE
                        (	COALESCE((	SELECT parfdesconto
                                    FROM parametros_faturamento
                                    WHERE parfclioid = con.conclioid
                                    AND parfobroid_multiplo && array [cons.consobroid]
                                    AND parfdt_exclusao IS NULL
                                    AND parfativo = 't'
                                    AND parfnivel = 2
                                    AND parfeqcoid IS NULL
                                    AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
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
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
                                LIMIT 1), 0)::NUMERIC / 100)
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
                        LIMIT 1)::NUMERIC *
    
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
                            AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca
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
                                AND '" . $parametros->dataReferencia . "' BETWEEN parfdt_ini_desconto AND parfdt_fin_desconto
                                LIMIT 1), 0)::NUMERIC / 100)
                    END
                    ), 2)
                ELSE
                    -- Valor com desconto (Se completou vigência)
                    ROUND(COALESCE(cons.consvalor, 0), 2)
                END AS valor,
    
                '" . $parametros->dataReferencia . "'::date AS data_referencia,
    
                'L' AS tipo_obrigacao,
    
                vppasubscription AS subscription_id,
    
                vppaconta AS conta,
    
                vppaciclo AS ciclo
            FROM
                contrato con
                INNER JOIN clientes cli ON cli.clioid = con.conclioid
                INNER JOIN veiculo vei ON vei.veioid = con.conveioid
                INNER JOIN tipo_contrato tpc ON tpc.tpcoid = con.conno_tipo
                INNER JOIN contrato_servico cons ON cons.consconoid = con.connumero
                INNER JOIN obrigacao_financeira obr ON obr.obroid = cons.consobroid
                INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid
                INNER JOIN veiculo_pedido_parceiro ON vppaconoid = connumero AND vppasubscription != ''
				INNER JOIN ciclo_faturamento_parceiro cfp ON vppaciclo = cfpciclo
            WHERE
                conno_tipo = 844
                AND cons.consinstalacao > '01/02/2013'::date
                AND cons.conssituacao = 'L' -- Locação Fixo por enquanto
                AND con.concsioid = 1
                AND cons.consiexclusao IS NULL
                AND obr.obroid != 90
                AND NOT EXISTS (SELECT
                                    nfi.nficonoid
                                FROM
                                    nota_fiscal_item nfi
                                    INNER JOIN nota_fiscal nfl ON (nfl.nflno_numero = nfi.nfino_numero AND nfl.nflserie = nfi.nfiserie)
                                WHERE
                                    (nfi.nficonoid = con.connumero OR nfi.nficonoid = con.connumero_antigo)
                                    AND nfi.nfiobroid = obr.obroid
                                    AND nfi.nfidt_referencia = '" . $parametros->dataReferencia . "'
                                    AND nfl.nfldt_cancelamento IS NULL)
                AND (condt_ini_vigencia::DATE < (TO_DATE((cfpdia_corte + 1) || '-' || EXTRACT('month' FROM ('" . $parametros->dataReferencia . "'::date)) || '-' || EXTRACT('year' FROM ('" . $parametros->dataReferencia . "'::date)),'DD-MM-YYYY')- (INTERVAL '1 month'))::DATE)";
    
    	if (!empty($parametros->documentoCliente)) {
    		$sql .= " AND (clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
    	}
    	if (!empty($parametros->nome)) {
    		$sql .= " AND clinome ILIKE '%" . $parametros->nome . "%'";
    	}
    
    	if (!empty($parametros->placa)) {
    		$sql .= " AND conveioid IN ( SELECT veioid FROM veiculo WHERE veiplaca ilike '" . $parametros->placa . "')";
    	}
    
    	//echo '4 <br/>'.$sql;
    	//exit;
    
    	if (!pg_query($this->conn, $sql)) {
    		throw new Exception("Falha ao atualizar Locacoes Acessorios.");
    	}
    }    

    public function faturar(stdClass $parametros) {

        $this->prepararQueries($parametros);

        $clientesFaturar = $this->buscarClientesFaturar($parametros);

        $quantidadeClientesFaturar = $this->buscarQuantidadeClientesFaturar($parametros);

        // Seleciona o valor mínimo para faturar
        $valorMinimoFaturamento = $this->buscarValorMinimoFaturamento();

        // Inicio do processo de geração de notas e títulos para
        $clientesFaturados = 0;
        $transacao = 0;

        foreach ($clientesFaturar as $cliente) {

            if (!$transacao) {
                $this->begin();
                $transacao = 1;
            }

            // Se o cliente atingiu valor mínimo, gera faturamento
            if ($cliente->total >= $valorMinimoFaturamento) {

                $formaCobrancaCliente = $this->buscarFormaCobrancaCliente($cliente->prfvclioid);
				
                // Insere a nota fiscal
                $nflnatureza 			= '';
                $nfltransporte 			= '';
                $nflclioid 				= $cliente->prfvclioid;
                $nflno_numero 			= $this->gerarNumeroSerieV();                
                $nfldt_vencimento_txt 	= $this->buscarDataVencimento($cliente->ciclo, $formaCobrancaCliente->clitipo); 
                $nflusuoid 				= $parametros->idUsuario;
                $nflvl_total 			= $cliente->total;
                $vlr_iss 				= ($formaCobrancaCliente->cliret_iss == 't') ? $formaCobrancaCliente->cliret_iss_perc / 100 * $nflvl_total : 0;
                $nflvlr_pis 			= $formaCobrancaCliente->cliret_pis_perc / 100 * $nflvl_total;
                $nflvlr_cofins 			= $formaCobrancaCliente->cliret_cofins_perc / 100 * $nflvl_total;
                $nflvlr_csll 			= $formaCobrancaCliente->cliret_csll_perc / 100 * $nflvl_total;

                $sql = "
                    EXECUTE inserir_nota_fiscal (
                        '" . $nflnatureza . "',
                        '" . $nfltransporte . "',
                        " . $nflclioid . ",
                        " . $nflno_numero . ",
                        '" . $parametros->dataReferencia . "'::date,
                        '" . $nfldt_vencimento_txt . "',
                        " . $nflusuoid . ",
                        " . $nflvl_total . ",
                        " . $vlr_iss . ",
                        " . $nflvlr_pis . ",
                        " . $nflvlr_cofins . ",
                        " . $nflvlr_csll . "
                    )";

                $resultado = pg_query($this->conn, $sql);

                if ($resultado && pg_num_rows($resultado) > 0) {
                    $dadosNota = pg_fetch_object($resultado);
                    $nfloid = ($dadosNota->nfloid > 0 ? $dadosNota->nfloid : 0);
                } else {
                    $this->rollback();
                    throw new Exception("Falha ao Gerar Faturamento (Inserção de nota para o cliente " . $nflclioid . ")");
                }


                $obrigacoesRecuperadas = $this->recuperarObrigacoes($cliente->prfvclioid, $cliente->ciclo);

                foreach ($obrigacoesRecuperadas as $obrigacao) {
                    // Insere as obrigações (itens da nota fiscal)
                    $nfino_numero = $nflno_numero;
                    $nfiserie = 'V';
                    $nficonoid = $obrigacao->prfvconnumero;
                    $nfiobroid = $obrigacao->prfvobroid;
                    $nfids_item = $obrigacao->obrobrigacao;
                    $nfivl_item = $obrigacao->prfvvalor;
                    $nfidt_referencia = $obrigacao->prfvdt_referencia;
                    $nfidesconto = 0;
                    $nfiveioid = empty($obrigacao->conveioid) ? 'NULL' : $obrigacao->conveioid;
                    $nfinfloid = $nfloid;
                    $nfitipo = $obrigacao->prfvtipo_obrigacao;

                    $sql = "
                        EXECUTE inserir_item_nota (
                            " . $nfino_numero . ",
                            '" . $nfiserie . "',
                            " . $nficonoid . ",
                            " . $nfiobroid . ",
                            '" . $nfids_item . "',
                            " . $nfivl_item . ",
                            '" . $nfidt_referencia . "',
                            " . $nfidesconto . ",
                            " . $nfiveioid . ",
                            " . $nfinfloid . ",
                            '" . $nfitipo . "'
                        )";

                    $resultado = pg_query($this->conn, $sql);

                    if (!$resultado) {
                        $this->rollback();
                        throw new Exception("Falha ao Gerar Faturamento (Inserção do item '" . $obrigacao->obrobrigacao . "' para o cliente " . $cliente->prfvclioid . ") " . pg_last_error());
                    }


                    /*
                     * 
                     * 
                    BLOCO COMENTADO (17/07/2014), AGUARDANDO IMPLEMENTAÇÃO CARGO TRACK(???) 
                    
                    //Atualiza histórico de reajuste com o NFLOID
                    //$this->atualizarHistoricoReajuste($nfloid, $nficonoid, $nfiobroid, $nfidt_referencia);

                    // Atualiza data de termino da contrato_obrigacao_financeira para obrtipo_nota = D
                    $sql = "
                        SELECT
                            obrtipo_nota
                        FROM
                            obrigacao_financeira
                        WHERE
                            obroid = '" . $nfiobroid . "'";

                    $resultado = pg_query($this->conn, $sql);

                    if ($resultado) {

                        $tipo = pg_fetch_result($resultado, 0, 'obrtipo_nota');

                        if ($tipo == "D") {
                            $sql = "UPDATE
											contrato_obrigacao_financeira
										SET
											cofdt_termino = NOW()
										WHERE
											cofconoid = '" . $nficonoid . "'
										AND cofobroid = '" . $nfiobroid . "'
										AND cofdt_termino IS NULL";

                            if (!pg_query($this->conn, $sql)) {
                                $this->rollback();
                                throw new Exception("Falha ao atualizar contrato_obrigacao_financeira (Atualização da obrigação '" . $nfiobroid . "' para o contrato " . $nficonoid . ") " . pg_last_error());
                            }
                        }
                    }

                    // atualiza status_contrato_ct
                    $sql = "
                        SELECT
                            conctoid
                        FROM
                            contrato_ct
                        WHERE
                            conctconnumero = " . $nficonoid;

                    $resultado = pg_query($this->conn, $sql);

                    if ($resultado && pg_num_rows($resultado) > 0) {
                        $conctoid = pg_fetch_result($resultado, 0, 'conctoid');
                        $this->atualizarStatusContratoCt($conctoid, 18);
                    } 
                     * 
                     *
                    */

		            // Calcula imposto (lei da transparência)
                    FinFaturamentoUnificadoDAO::calcularImposto($nfloid, $this->conn);   
                    
                } 

                // Busca o percentual de comissionamento parametrizado para a VIVO
                $sql ="
					SELECT 
                		parvpercentual_comissao
                	FROM 
                		parametros_vivo;
                	";
                
                $resultado = pg_query($this->conn, $sql);
                
                if($resultado && pg_num_rows($resultado) > 0) {
                	$parvpercentual_comissao = pg_fetch_result($resultado, 0, 'parvpercentual_comissao');                	
                }
                
                // Insere o título
                $titclioid = $cliente->prfvclioid;
                $titdt_vencimento = $nfldt_vencimento_txt;
                $titvl_titulo = $nflvl_total;
                $titvl_desconto = 0;
                $titformacobranca = ($formaCobrancaCliente->cliformacobranca != '' ? $formaCobrancaCliente->cliformacobranca : 73);
                $titusuoid_alteracao = $parametros->idUsuario;
                $titcobrterc_comissao = ($titvl_titulo * $parvpercentual_comissao)/100;


                $sql = "
                    EXECUTE inserir_titulo (
                        " . $titclioid . ",
                        '" . $titdt_vencimento . "',
                        " . $titvl_titulo . ",
                        " . $titvl_desconto . ",
                        " . $titformacobranca . ",
                        " . $titusuoid_alteracao . ",
                        " . $nfloid . ",
                        " . $titcobrterc_comissao ."

                    )";

                if (!pg_query($this->conn, $sql)) {
                    $this->rollback();
                    throw new Exception("Falha ao Gerar Faturamento (Inserção de título para o cliente " . $nflclioid . ")");
                }

                // Exclui as obrigações do cliente da previsão
                $sql = "
                    EXECUTE excluir_cliente (
                        " . $cliente->prfvclioid . ",
                		'" . $cliente->ciclo . "'
                    );";
                $sql .= "
                    EXECUTE excluir_cliente_taxas (
                        " . $cliente->prfvclioid . "
                    );";

                if (!pg_query($this->conn, $sql)) {
                    $this->rollback();
                    throw new Exception("Falha ao Gerar Faturamento (Deletando previsao do cliente " . $cliente->prfvclioid . ")");
                }
            }

            $clientesFaturados++;

            // Atualiza porcetagem processada e commita atualizações
            if (($clientesFaturados % 500) == 0) {
                $sql = "
                    EXECUTE atualizar_andamento (
                        " . round(($clientesFaturados / $quantidadeClientesFaturar) * 100, 1) . "
                    );";

                if (!pg_query($this->conn, $sql)) {
                    $this->rollback();
                    throw new Exception("Falha ao atualizar andamento da execução");
                }

                $this->commit();
                $transacao = 0;
            }
        }

        if ($transacao) {
            $this->commit();
        }
    }

    private function buscarFormaCobrancaCliente($idCliente) {

        // Pesquisa dados tributários e forma de cobrança do cliente
        $sql = "
                SELECT
        			clitipo,
                    cliformacobranca,
                    clidia_vcto,
                    cliret_iss_perc,
                    cliret_iss,
                    cliret_pis_perc,
                    cliret_cofins_perc,
                    cliret_csll_perc,
                    clicdias_uteis,
                    clicdia_mes,
                    clicdia_semana,
                    clicdias_prazo
                FROM
                    clientes
                    LEFT JOIN cliente_cobranca ON clicclioid = clioid
                WHERE
                    clioid = " . $idCliente . "
                    AND clicexclusao IS NULL
                LIMIT 1 ";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_object($resultado);
        } else {
            throw new Exception("Falha ao Gerar Faturamento (Pesquisa de parametros de clientes)");
        }
    }

    private function recuperarObrigacoes($idCliente, $ciclo) {

        $retorno = array();

        // Seleciona obrigações financeiras do cliente
        $sql = "
            EXECUTE recuperar_obrigacoes (
                " . $idCliente . ",
                '" . $ciclo . "'
            )";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
        	while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }
        } else {
            $this->rollback();
            throw new Exception("Falha ao Gerar Faturamento (Seleção de itens da nota fiscal)");
        }

        return $retorno;
    }

    /**
     * Atualiza o histórico de Reajustes com o OID da Nota Fiscal
     *
     * @param int $nfloid
     * @param int $connumero
     * @param int $obroid
     * @param String $dataReferencia
     * @throws Exception
     */
    private function atualizarHistoricoReajuste($nfloid, $connumero, $obroid, $dataReferencia) {

        $sql = "
            UPDATE obrigacao_financeira_reajuste_historico SET
               ofrhnfloid = " . intval($nfloid) . "
            WHERE
                ofrhconnumero = " . intval($connumero) . "
                AND ofrhobroid = " . intval($obroid) . "
                AND ofrhdt_referencia = '" . $dataReferencia . "'::DATE";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao atualizar Histórico de Reajuste IGPM/INPC", 0);
        }

        return true;
    }

    private function atualizarStatusContratoCt($stconctconctoid, $stconctstoid) {

        try {
            $this->begin();

            $sql = "
                DELETE FROM
                    status_contrato_ct
                WHERE
                    stconctconctoid = " . $stconctconctoid . "
                    AND stconctstoid IN (17, 18)";

            if (!pg_query($this->conn, $sql)) {
                throw new Exception("Erro ao deletar status.");
            }

            $sql = "
                INSERT INTO status_contrato_ct (
                    stconctstoid,
                    stconctconctoid
                ) VALUES (
                    " . $stconctstoid . ",
                    " . $stconctconctoid . "
                )";

            if (!pg_query($this->conn, $sql)) {
                throw new Exception("Erro ao adicionar status.");
            }

            $this->commit();
        } catch (Exception $e) {

            $this->rollback();
        }
    }

    private function gerarNumeroSerieV() {
        $sql = "
            SELECT
				COALESCE(MAX(nflno_numero),0) + 1
			FROM
				nota_fiscal
			WHERE
				nflserie = 'V'
			LIMIT 1; ";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_result($resultado, 0, 0);
        } else {
            throw new Exception("Falha ao gerar numero de série da nota fiscal");
        }
    }

    private function buscarClientesFaturar(stdClass $parametros) {

        $retorno = array();

        $clausulasComPrefixo = '';
        $clausulasSemPrefixo = '';

        if (!empty($parametros->nome)) {
            $clausulasComPrefixo .= " AND prfv.prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
            $clausulasSemPrefixo .= " AND prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
        }

        if (!empty($parametros->documentoCliente)) {
            $clausulasComPrefixo .= " AND prfv.prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
            $clausulasSemPrefixo .= " AND prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }

        if (!empty($parametros->placa)) {
            $placa = str_replace('-', '', $parametros->placa);
            $clausulasComPrefixo .= " AND prfv.prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $placa . "')";
            $clausulasSemPrefixo .= " AND prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $placa . "')";
        }

        if (!empty($parametros->obrigacoesFinanceiras)) {
            $clausulasComPrefixo .= " AND prfv.prfvobroid IN (" . $parametros->obrigacoesFinanceiras . ")";
            $clausulasSemPrefixo .= " AND prfvobroid IN (" . $parametros->obrigacoesFinanceiras . ")";
        }

        /* BLOCO COMENTADO - 05/12/2014 - MARCELLO
         * A consulta dos campos tipo, L_TOTAL e M_TOTAL não faz sentido uma vez que o 
         * faturamento VIVO abrange apenas LOCAÇÂO. O resultado ($cliente->tipo, 
         * $cliente->L_TOTAL, $cliente->M_TOTAL) não está sendo utilizado.
         */
        /* $sql = "SELECT DISTINCT ON (cliente)
                    cliente AS prfvclioid,
                    total, 
                    CASE WHEN tipo = 'L' THEN valor
                    WHEN (lead(tipo,1) OVER (PARTITION BY cliente)) = 'L' THEN lead(valor,1) OVER (PARTITION BY cliente)
                    ELSE 0
                    END AS L_TOTAL, 
                    CASE WHEN tipo = 'M' THEN valor
                    WHEN tipo = 'D' THEN valor
                    WHEN (lead(tipo,1) OVER (PARTITION BY cliente)) = 'M' THEN lead(valor,1) OVER (PARTITION BY cliente)
                    WHEN (lead(tipo,1) OVER (PARTITION BY cliente)) = 'D' THEN lead(valor,1) OVER (PARTITION BY cliente)
                    ELSE 0
                    END AS M_TOTAL,                     
        			tipo,
        			ciclo
                FROM
                (
                    SELECT
                        total,
                        cliente,
                        sum(s.valor) as valor,
                        tipo,
        				ciclo
                    FROM
                    (
                        SELECT
                            sum(prfv.prfvvalor) as total,
                            prfv.prfvclioid AS cliente,
                            sub_totais.valor AS valor,
                            sub_totais.prfvtipo_obrigacao AS tipo,
        					prfv.prfvciclo AS ciclo
                        FROM
                            previsao_faturamento_vivo AS prfv
                        inner JOIN
                            obrigacao_financeira as obr ON prfv.prfvobroid = obroid
                        INNER JOIN (
                                    SELECT
                                        SUM(prfvvalor) AS valor,
                                        prfvclioid,
                                        prfvtipo_obrigacao,
        								prfvciclo
                                    FROM
                                        previsao_faturamento_vivo
                                    INNER JOIN
                                        obrigacao_financeira ON prfvobroid = obroid
                                    WHERE
                                        1 = 1
                                        " . $clausulasSemPrefixo . "
                                    GROUP BY
                                        prfvclioid,
                                        prfvtipo_obrigacao,
										prfvciclo
                                    ) sub_totais ON sub_totais.prfvclioid = prfv.prfvclioid
                        WHERE
                            1 = 1
                            " . $clausulasComPrefixo . "
                        GROUP BY
                            prfv.prfvclioid,
                            sub_totais.valor,
                            sub_totais.prfvtipo_obrigacao,
                            prfv.prfvciclo
                    ) s
                    GROUP BY
                        s.total,
                        s.cliente,
                        s.tipo,
						s.ciclo
                ) sub; "; */

        $sql = "
			SELECT
				prfvclioid,
				SUM(prfvvalor) AS total,
				prfvciclo AS ciclo
			FROM
				previsao_faturamento_vivo
			WHERE
				1 = 1
				" . $clausulasSemPrefixo . "
			GROUP BY
				prfvclioid,
				prfvciclo
			ORDER BY 
				prfvclioid; ";
        
        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }

            return $retorno;
        } else {
            throw new Exception("Falha ao Gerar Faturamento (Seleção de clientes)");
        }
    }

    private function prepararQueries(stdClass $parametros) {

        $filtros = '';

        if (!empty($parametros->nome)) {
            $filtros .= " AND prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
        }

        if (!empty($parametros->documentoCliente)) {
            $filtros .= " AND prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }

        if (!empty($parametros->placa)) {
            $placa = str_replace('-', '', $parametros->placa);
            $filtros .= " AND prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $placa . "')";
        }

        if (!empty($parametros->obrigacoesFinanceiras)) {
            $filtros .= " AND prfvobroid IN (" . $parametros->obrigacoesFinanceiras . ")";
        }


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

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Seleção de cliente)");
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
                    'V',
                    NULL,
                    $5,
                    $6,
                    $7,
                    $8,
                    $9,
                    $10,
                    $11,
                    $12,
                    '0.0')
                RETURNING nfloid, nflno_numero, nflserie";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Inserção de nota fiscal)");
        }

        $sql = "PREPARE inserir_titulo(integer, date, numeric(10,2), numeric(10,2), integer, integer, integer, numeric(12,2)) AS
                INSERT INTO titulo (
                        titclioid,
                        titdt_vencimento,
                        titvl_titulo,
                        titvl_desconto,
                        titformacobranca,
                        titusuoid_alteracao,
                        titnfloid,
        				titcobrterc_comissao,
                        titfatura_unica)
                VALUES (
                        $1,
                        $2,
                        $3,
                        $4,
                        $5,
                        $6,
                        $7,
        				$8,
                        't')";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Inserção de título)");
        }

        $sql = "PREPARE recuperar_obrigacoes(integer, character varying(15)) AS
                SELECT
                    prfvconnumero,
                    prfvobroid,
                    obrobrigacao,
                    prfvvalor,
                    prfvdt_referencia,
                    prfvtipo_obrigacao,
                    futoid
                FROM
                (
                    SELECT
                        prfvconnumero,
                        prfvobroid,
                        obrobrigacao,
                        prfvvalor,
                        prfvdt_referencia,
                        prfvtipo_obrigacao,
                        NULL AS futoid
                    FROM
                        previsao_faturamento_vivo
                        INNER JOIN obrigacao_financeira ON obroid = prfvobroid
                        LEFT JOIN contrato ON connumero = prfvconnumero
                    WHERE
                        1 = 1
                        " . $filtros . "
                        AND prfvclioid = $1
                        AND prfvciclo = $2

                    UNION ALL

                    SELECT
                        prfvconnumero,
                        prfvobroid,
                        obrobrigacao,
                        prfvvalor,
                        prfvdt_referencia,
                        'M' AS prfvtipo_obrigacao, -- taxas entra como monitoramento na nota.
                        futoid -- quando for taxa, é preciso update no registro de faturamento_unificado_taxas.
                    FROM
                        (
                            SELECT
                                futoid,
                                futdt_referencia AS prfvdt_referencia,
                                futclioid AS prfvclioid,
                                futobroid AS prfvobroid,
                                futvalor::numeric AS prfvvalor,
                                futconnumero AS prfvconnumero,
                                futstatus,
                                futdt_faturado
                            FROM faturamento_unificado_taxas
                            WHERE futstatus='P'
                        ) previsao_faturamento_vivo
                        INNER JOIN obrigacao_financeira ON obroid = prfvobroid
                        LEFT JOIN contrato ON connumero = prfvconnumero
                    WHERE
                        1 = 1
                        " . $filtros . "
                        AND prfvclioid = $1
                ) a;";
        
        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Seleção de obrigações financeiras)");
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

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
        }

        $sql = "PREPARE excluir_cliente(integer, character varying(15)) AS
                DELETE FROM
                    previsao_faturamento_vivo
                WHERE
                    1 = 1
                    " . $filtros . "
                    AND prfvclioid = $1
                    AND prfvciclo = $2;
                    ";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
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
                                                futdt_referencia AS prfvdt_referencia,
                                                futclioid AS prfvclioid,
                                                futobroid AS prfvobroid,
                                                futvalor::numeric AS prfvvalor,
                                                futconnumero AS prfvconnumero,
                                                futstatus,
                                                futdt_faturado
                                            FROM faturamento_unificado_taxas
                                            WHERE futstatus='P'
                                        ) previsao_faturamento_vivo
                                    WHERE 1=1
                                        " . $filtros . "
                                        AND prfvclioid = $1
                                );
                    ";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
        }

        $sql = "PREPARE atualizar_andamento(double precision) AS
                UPDATE
                    execucao_faturamento
                SET
                    exfporcentagem = $1
                WHERE
                    exfdt_termino is null";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao preparar consulta (Inserção de itens da nota fiscal)");
        }
    }

    private function buscarQuantidadeClientesFaturar(stdClass $parametros) {

        // Seleciona a quantidade de clientes para faturar
        $sql = "SELECT
                    COUNT(DISTINCT prfvclioid) AS quantidade
                FROM (
                    SELECT
                        DISTINCT prfvclioid
                    FROM
                        previsao_faturamento_vivo
                    WHERE
                        1 = 1";

        if (!empty($parametros->nome)) {
            $sql .= " AND prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
        }

        if (!empty($parametros->documentoCliente)) {
            $sql .= " AND prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }

        if (!empty($parametros->placa)) {
            $placa = str_replace('-', '', $parametros->placa);
            $sql .= " AND prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $placa . "')";
        }

        if (!empty($parametros->obrigacoesFinanceiras)) {
            $sql .= " AND prfvobroid IN (" . $parametros->obrigacoesFinanceiras . ")";
        }

        $sql .= " ) qt";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            $row = pg_fetch_object($resultado);

            return $row->quantidade;
        } else {
            throw new Exception('Falha ao calcular quantidade de clientes para faturamento.');
        }
    }

    private function buscarValorMinimoFaturamento() {

        $sql = "SELECT
					vmfvl_faturamento_minimo as valor_minimo_faturamento
				FROM
					valores_minimos_faturamento
				WHERE
					vmfdt_exclusao IS NULL
				LIMIT 1";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            $row = pg_fetch_object($resultado);

            return $row->valor_minimo_faturamento;
        } else {
            throw new Exception('Falha ao buscar valores mínimos para faturamento');
        }
    }

    public function finalizarProcesso($resultado) {

        $sql = "
            UPDATE execucao_faturamento_vivo SET
                efvporcentagem = 100,
                efvdt_termino = NOW(),
                efvresultado = '" . $resultado . "'
            WHERE
                efvdt_termino IS NULL";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas.");
        }

        return true;
    }

    public function recuperarParametros($finalizado) {

        // Recupera os parâmetros salvos pelo resumo
        $sql = "SELECT
					nm_usuario,
					usuemail,
					efvoid,
					efvusuoid,
					TO_CHAR(efvdt_inicio, 'HH24:MI:SS') as inicio,
					TO_CHAR(efvdt_termino, 'HH24:MI:SS') as termino,
					TO_CHAR(efvdt_inicio, 'DD/MM/YYYY HH24:MI:SS') as data_inicio,
					TO_CHAR(efvdt_termino, 'DD/MM/YYYY HH24:MI:SS') as data_termino,
					efvtipo_processo,
					efvporcentagem,
					efvparametros
				FROM
					execucao_faturamento_vivo
                    INNER JOIN usuarios on cd_usuario = efvusuoid";

        if ($finalizado) {
            $sql .= "
                ORDER BY
                    efvdt_termino DESC";
        } else {
            $sql .= "
                AND
                    efvdt_termino IS NULL";
        }

        $sql .= " LIMIT 1";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado) {
            if (pg_num_rows($resultado) > 0) {
                return pg_fetch_object($resultado);
            } else {
                throw new Exception('Não foi encontrado parâmetros.');
            }
        } else {
            throw new ErrorException('Falha ao recuperar parâmetros.');
        }
    }

    public function pararResumo() {

        $sql = "UPDATE
    				execucao_faturamento_vivo
    			SET
    				efvcancelado = TRUE,
    				efvdt_termino = NOW()
    			WHERE
    				efvoid = (
    							SELECT
    								efvoid
    							FROM
    								execucao_faturamento_vivo
								WHERE
									efvtipo_processo = 'R'
								AND
									efvdt_termino IS NULL
								ORDER BY
									efvdt_inicio DESC
								LIMIT 1
							)";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_affected_rows($resultado) > 0) {
            return true;
        } else {
            throw new ErrorException('Faturamento finalizado.');
        }
    }

    public function gerarPlanilhaCsv($obrigacoesFinanceiras) {

        $retorno = array();

        $sql = "
            SELECT
                nome_cliente,
                numero_contrato,
                tipo_contrato,
                TO_CHAR(data_inicio_vigencia, 'DD/MM/YYYY'),
        		ciclo_faturamento,
                situacao_contrato,
                placa_veiculo,
                nome_equipamento,
                subscription_id,
                conta,
                obrigacao_financeira,
                SUM(valor_total) AS valor_total
                FROM
                    (
                        SELECT
                            clinome AS nome_cliente,
                            prfvconnumero AS numero_contrato,
                            tpcdescricao AS tipo_contrato,
                            condt_ini_vigencia AS data_inicio_vigencia,
        					prfvciclo AS ciclo_faturamento,
                            csidescricao AS situacao_contrato,
                            veiplaca AS placa_veiculo,
                            eqcdescricao as nome_equipamento,
                            prfvsubscription AS subscription_id,
                            prfvconta AS conta,
                            (SELECT obrobrigacao FROM obrigacao_financeira WHERE obroid=prfvobroid) AS obrigacao_financeira,
                            SUM(prfvvalor) AS valor_total
                        FROM
                            previsao_faturamento_vivo prfv
                            INNER JOIN clientes cli ON clioid = prfvclioid
                            INNER JOIN contrato ON connumero = prfvconnumero
                            INNER JOIN tipo_contrato ON conno_tipo = tpcoid
                            INNER JOIN contrato_situacao ON concsioid = csioid
                            INNER JOIN veiculo ON conveioid = veioid
                            INNER JOIN equipamento_classe ON eqcoid = coneqcoid
                        WHERE
                            prfvobroid IN (" . $obrigacoesFinanceiras . ")
                        GROUP BY
                            clinome,
                            prfvconnumero,
                            tpcdescricao,
                            data_inicio_vigencia,
        					ciclo_faturamento,
                            csidescricao,
                            veiplaca,
                            eqcdescricao,
                            prfvsubscription,
                            prfvconta,
                    		obrigacao_financeira
                    ) pendencias
                GROUP BY
                    nome_cliente,
                    numero_contrato,
                    tipo_contrato,
                    data_inicio_vigencia,
        			ciclo_faturamento,
                    situacao_contrato,
                    placa_veiculo,
                    nome_equipamento,
                    subscription_id,
                    conta,
                    obrigacao_financeira
                ORDER BY
                    nome_cliente,
                    numero_contrato";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }
        } else {
            throw new exception('Falha ao Verificar Pendências');
        }

        return $retorno;
    }

    public function gerarRelatorioPreFaturamento($parametros) {

        $retorno = array();

        $sql = "
            SELECT
                clinome AS nome_cliente,
                connumero AS numero_contrato,
                tpcdescricao AS tipo_contrato,
                to_char(condt_ini_vigencia, 'DD/MM/YYYY') AS data_inicio_vigencia,
        		prfvciclo AS ciclo_faturamento,
                csidescricao AS situacao_contrato,
                veiplaca AS placa_veiculo,
                eqcdescricao AS nome_equipamento,
                prfvsubscription AS subscription_id,
                prfvconta AS conta,
                obrobrigacao AS nome_obrigacao,
                prfvvalor AS valor_total
            FROM
                previsao_faturamento_vivo
                INNER JOIN clientes ON prfvclioid = clioid
                LEFT JOIN contrato ON connumero = prfvconnumero
                LEFT JOIN tipo_contrato ON tpcoid = conno_tipo
                LEFT JOIN contrato_situacao ON concsioid = csioid
                LEFT JOIN veiculo ON conveioid = veioid
                LEFT JOIN equipamento_classe ON eqcoid = coneqcoid
                LEFT JOIN obrigacao_financeira ON obroid = prfvobroid
            WHERE
                1 = 1";

        if (!empty($parametros->nome)) {
            $sql .= " AND prfvclioid IN ( SELECT clioid FROM clientes WHERE TRUE AND clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%')";
        }

        if (!empty($parametros->documentoCliente)) {
            $sql .= " AND prfvclioid IN (SELECT clioid FROM clientes WHERE clino_cgc = " . $parametros->documentoCliente . " OR clino_cpf = " . $parametros->documentoCliente . ")";
        }

        if (!empty($parametros->placa)) {
            $placa = str_replace('-', '', $parametros->placa);
            $sql .= " AND prfvclioid IN (SELECT conclioid FROM contrato INNER JOIN veiculo ON veioid = conveioid AND veiplaca ilike '" . $placa . "')";
        }

        if (!empty($parametros->obrigacoesFinanceiras)) {
            $sql .= " AND prfvobroid IN (" . $parametros->obrigacoesFinanceiras . ")";
        }

        $sql .= "
            ORDER BY
                nome_cliente,
                numero_contrato";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($linha = pg_fetch_object($resultado)) {
                array_push($retorno, $linha);
            }
        } else {
            throw new exception('Falha ao gerar Relatório do Pré-Faturamento');
        }

        return $retorno;
    }

    /**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @param stdClass $parametros parametros para busca.
     *
     * @return array $retorno
     */
    public function buscarClienteNome($parametros) {

        $retorno = array();

        $parametros->nome = html_entity_decode($parametros->nome);

        $parametros->nome = urldecode($parametros->nome);

        $parametros->nome = utf8_decode($parametros->nome);


        if (trim($parametros->nome) === '') {
            echo json_encode($retorno);
            exit;
        }

        $sql = "
            SELECT
                clioid,
                clinome,
                CASE
                    WHEN clitipo = 'J'
                        THEN clino_cgc
                    ELSE
                        clino_cpf
                END AS doc,
                clitipo AS tipo
            FROM
                clientes
            WHERE
                clidt_exclusao IS NULL ";

        if (trim($parametros->tipo) != '') {
            $sql .= "
                AND
                    clitipo = '" . pg_escape_string($parametros->tipo) . "' ";
        }

        $sql .= "
                AND
                    clinome ILIKE '%" . pg_escape_string($parametros->nome) . "%'
                ORDER BY
                    clinome
                LIMIT
                    100";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            $objeto = pg_fetch_object($resultado);
            for ($i = 0; $objeto; $i++) {
                $retorno[$i]['id'] = $objeto->clioid;
                $retorno[$i]['label'] = utf8_encode($objeto->clinome);
                $retorno[$i]['value'] = utf8_encode($objeto->clinome);
                $retorno[$i]['tipo'] = utf8_encode($objeto->tipo);
                if ($objeto->tipo == 'J') {
                    $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                } else if ($objeto->tipo == 'F') {
                    $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                }
                $objeto = pg_fetch_object($resultado);
            }
        }

        return $retorno;
    }


    /**
     * Buscar a data de vencimento de títulos e notas fiscais de acordo com o
     * ciclo de faturamento e tipo de pessoa (Física/ Jurídica) relacionado. 
     *
     * @param 	varchar(15) $ciclo 
     * 		  	char		$tipo
     *
     * @return 	date		$retorno
     */
    public function buscarDataVencimento($ciclo, $tipo) {
		
    	$diaVctoCiclo = ($tipo == 'F') ? 'cfpdia_v1' : 'cfpdia_v2';
    	   	
    	$sql = "
    			SELECT 
					CASE 
					-- Dia do Corte maior que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data no mesmo Mês
						WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND ($diaVctoCiclo >= cfpdia_corte)
						THEN TO_DATE($diaVctoCiclo || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY')
					-- Dia do Corte maior que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para o Mês seguinte
						WHEN (EXTRACT('Day' FROM NOW()) <  cfpdia_corte) AND ($diaVctoCiclo < cfpdia_corte)
						THEN to_date($diaVctoCiclo || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
					-- Dia do Corte menor que Hoje e dia de Vencimento maior que dia do Corte : Fatura com data para o Mês seguinte
						WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND ($diaVctoCiclo >= cfpdia_corte)
						THEN TO_DATE($diaVctoCiclo || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '1 month'
					-- Dia do Corte menor que Hoje e dia de Vencimento menor que dia do Corte : Fatura com data para e meses depois
						WHEN (EXTRACT('Day' FROM NOW()) >=  cfpdia_corte) AND ($diaVctoCiclo < cfpdia_corte)
						THEN TO_DATE($diaVctoCiclo || '-' || EXTRACT('Month' FROM NOW()) || '-' || EXTRACT('Year' FROM NOW()),'DD-MM-YYYY') + INTERVAL '2 month'
					-- Ciclo não cadastrado
						ELSE NOW()              
					END AS data_vencimento   
				FROM 
					ciclo_faturamento_parceiro
				WHERE 
					cfpciclo = '".$ciclo."'; ";
    	
    	$resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
        	$row = pg_fetch_object($resultado);
            $retorno = $row->data_vencimento;
        } 
        elseif (pg_num_rows($resultado) == 0){
        	$retorno = date('Y-m-d');
        } 
        else {
            throw new Exception('Falha ao buscar ciclo de faturamento');
        }
	           	
    	return $retorno;
    }
     

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

?>