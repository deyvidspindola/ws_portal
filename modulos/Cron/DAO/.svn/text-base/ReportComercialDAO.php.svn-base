<?php
/**
 * Report Comercial
 *
 * @package Cron
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class ReportComercialDao {

    /**
     * Objeto Parâmetros.
     *
     * @var stdClass
     */
    private $param;

    /**
     * Método construtor.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Void
     */
    public function __construct(stdClass $param) {
        $this->param = $param;

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $dataReferencia = strtotime($this->param->rpcdt_referencia.' 00:00:00');
            $dataInicial    = strtotime('-1 month', $dataReferencia);
            $dataFinal      = strtotime('-1 second', strtotime('+1 month', $dataReferencia));

            $this->param->rpcdt_referencia_mes = date('m', $dataInicial);
            $this->param->rpcdt_referencia_ano = date('Y', $dataInicial);

            $this->param->rpcdt_referencia_inicial = date('Y-m-d H:i:s', $dataInicial);
            $this->param->rpcdt_referencia_final   = date('Y-m-d H:i:s', $dataFinal);
            $this->param->rpcnm_referencia_inicial = date('Ym', $dataInicial);
            $this->param->rpcnm_referencia_final   = date('Ym', $dataFinal);

            $dataInicial = $dataReferencia;
            $dataFinal   = strtotime('-1 second', strtotime('+1 month', $dataReferencia));

            $this->param->rpcdt_mes_atual_inicial = date('Y-m-d H:i:s', $dataInicial);
            $this->param->rpcdt_mes_atual_final   = date('Y-m-d H:i:s', $dataFinal);

            $dataInicial = strtotime('-1 month', $dataReferencia);
            $dataFinal   = strtotime('-1 second', $dataReferencia);

            $this->param->rpcdt_mes_anterior_inicial = date('Y-m-d H:i:s', $dataInicial);
            $this->param->rpcdt_mes_anterior_final   = date('Y-m-d H:i:s', $dataFinal);
        }
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_agg_monitoramento.
     *
     * @return String
     */
    public function criarAggMonitoramento() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_agg_monitoramento;

            CREATE TEMPORARY TABLE
                tb_tmp_agg_monitoramento
            AS
                SELECT
                    contrato.connumero AS nr_termo,
                    clientes.clinome AS cliente,
                    COALESCE(
                        SUM(
                            CASE
                                WHEN contrato_pagamento.cpagmonitoramento IS NULL THEN
                                    tipo_contrato_faturamento.tcfvalor_monitoramento
                                ELSE
                                    contrato_pagamento.cpagmonitoramento
                                END
                        ), 0
                    ) AS valor,
                    regiao_comercial_zona.rczcd_zona AS dmv
                FROM
                    contrato
                INNER JOIN
                    clientes ON contrato.conclioid = clientes.clioid
                INNER JOIN
                    comissao_instalacao ON contrato.connumero = comissao_instalacao.cmiconoid
                LEFT JOIN
                    contrato_pagamento ON contrato_pagamento.cpagconoid = contrato.connumero
                LEFT JOIN
                    proposta ON contrato.connumero = proposta.prptermo
                LEFT JOIN
                    proposta_pagamento ON proposta.prpoid = proposta_pagamento.ppagprpoid
                LEFT JOIN
                    cond_pgto_venda ON proposta_pagamento.ppagcpvoid = cond_pgto_venda.cpvoid
                LEFT JOIN
                    regiao_comercial_zona ON contrato.conrczoid = regiao_comercial_zona.rczoid
                LEFT JOIN
                    tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                LEFT JOIN
                    tipo_contrato_faturamento ON
                        (
                            contrato.conno_tipo = tipo_contrato_faturamento.tcftpcoid
                        AND
                            contrato.coneqcoid = tipo_contrato_faturamento.tcfeqcoid
                        )
                WHERE
                    comissao_instalacao.cmiotioid = 3
                /*
                AND
                    comissao_instalacao.cmiotioid IN
                        (
                            SELECT
                                os_tipo_item.otioid
                            FROM
                                os_tipo_item
                            WHERE
                                os_tipo_item.otitipo = 'E'
                            AND
                                os_tipo_item.otiostoid = 1
                        )
                */
                AND
                    comissao_instalacao.cmiexclusao IS NULL
                AND
                    tipo_contrato.tpcdescricao NOT ILIKE 'EX-%'
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    comissao_instalacao.cmidata BETWEEN ('".$this->param->rpcdt_mes_atual_inicial."'::DATE - INTERVAL '6 MONTH') AND '".$this->param->rpcdt_mes_atual_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                GROUP BY
                    contrato.connumero,
                    clientes.clioid,
                    regiao_comercial_zona.rczoid;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_agrupamento_saida.
     *
     * @return String
     */
    public function criarAgrupamentoSaida() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_agrupamento_saida;

            CREATE TEMPORARY TABLE
                tb_tmp_agrupamento_saida
            AS
                SELECT
                    tb_tmp_dados_saida.dmv,
                    COUNT(1) AS qtd_contrato,
                    SUM(tb_tmp_agg_monitoramento.valor) as valor
                FROM
                    tb_tmp_dados_saida
                        INNER JOIN
                            tb_tmp_agg_monitoramento ON tb_tmp_dados_saida.nr_termo = tb_tmp_agg_monitoramento.nr_termo
                WHERE
                    tb_tmp_dados_saida.dmv IS NOT NULL
                GROUP BY
                    tb_tmp_dados_saida.dmv;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_agrupamento_saida.
     *
     * @return String
     * @todo Rever o SQL.
     */
    public function criarBaseAtiva() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_base_ativa;

            CREATE TEMPORARY TABLE
                tb_tmp_base_ativa
            AS
                SELECT
                    REPLACE(REPLACE(clinome, CHR(13), ''), CHR(10), '') AS cliente,
                    CASE
                        WHEN clitipo = 'J' THEN
                            clino_cgc
                        WHEN clitipo = 'F' THEN
                            clino_cpf
                    END AS documento,
                    CAST(condt_cadastro AS DATE) AS dt_cadastro,
                    CAST(condt_ini_vigencia AS DATE) AS dt_vigencia,
                    connumero AS nr_termo,
                    tpcdescricao AS tp_contrato,
                    CASE
                        WHEN clitipo = 'J' THEN
                            'PJ'
                        WHEN clitipo = 'F' THEN
                            'PF'
                    END AS tp_cliente,
                    tipvdescricao AS tp_veiculo,
                    CAST(condt_exclusao as date) as dt_exclusao,
                    CASE WHEN eqcdescricao NOT ilike 'SASMOBILE IMÃ' and (tpcseguradora is false and tpcassociacao is false and tpcdescricao not IN
								('AAPVAI','ABES - Associação Brasileira de Empreendimentos Solidarios','ACO CAR ','ACTRO','AESPOL','AGS','ALTERNATIVA',
								 'AMPARO','APM - ASSOCIACAO DE PROTECAO A MOTOS','APOIO CAR','APROAUTO COMODATO','APROAUTO VENDA','APROMOTOS ',
								 'Aprosul','APROVEL','APVS-ASSOCIAÇÃO DE PROTEÇÃO VEICULAR E SERVIÇOS SOCIAIS','ASCAM OMINI CAR','ASCAR','ASCOBOM',
								 'ASCOBOM MOTO','ASCOBOM VENDA','ASCOTRANS','ASPRAN','ASSOCIAÇÃO ALIANÇA','Associação Baiana dos Transportes de Carga',
								 'ASSOCIAÇÃO IMPERIAL','ASSUTRAN','ATAC SP','ATMS .ASSOCIACAO DOS TRANSPORTADORES DE MINERIO DE SARZEDO','ATRAC',
								 'AUTO TRUCK','AUTO TRUCK COMODATO','AUTO TRUCK VENDA','AUTOVALOR','AUTOVEP TRUCK','AVAP',
								 'BENVEL . ASSOCIAÇÃO BRASILEIRA DE BENEFICIOS E ASSISTENCIA AOS AMIGOS','BRASILCAR','CBC BRASIL','CLUB CAR',
								 'Edemar Angelo Poletti','FENACAM','INOVTEC ACONTRANS','MASTTERCAR','MEGA ASSOCIAÇÃO','NACIONAL PROTECAO',
								 'NOSSA','OKA','PREV CAR','PREV TRUCK','PREV TRUCK (SBTEC)','PROTEAUTO','PROTVEL','Protvel - SP','PROTVEL COMODATO',
								 'PROTVEL ESPECIAL','PROTVEL VENDA','PROVEL','REDE VEICULOS','RODOBRASIL','RODOSAT CLIENTE','RODOSAT COMODATO',
								 'RONDAVE','RONDAVE COMODATO','RONDAVE VENDA','SBTEC/RONDAVE','SUPER TRUCK','Truckvale','TRUST','Unipropas Moto',
								 'UNIPROPASS')) and clitipo = 'F' and (
			((select tipvdescricao from veiculo inner join modelo on veimlooid=mlooid inner join tipo_veiculo on mlotipveioid=tipvoid
				where conveioid_antigo=veioid and conveioid IS NULL)IN ('VAM','CAMINHAO','CARRETA','MICROONIBUS','ONIBUS','PERUA')) OR
			((select tipvdescricao from veiculo inner join modelo on veimlooid=mlooid inner join tipo_veiculo on mlotipveioid=tipvoid
				where conveioid=veioid )IN ('VAM','CAMINHAO','CARRETA','MICROONIBUS','ONIBUS','PERUA'))) then 'FRETEIRO'

			else '' end AS segmentacao,
		rczcd_zona as dmv,
		case when rczcd_zona IN ('DMV1101','DMV1102','DMV1107','DMV1201','DMV1202','DMV1207') THEN 'DMV1100'
			 when rczcd_zona IN('DMV1103','DMV1104','DMV1105','DMV1203','DMV1204','DMV1205') THEN 'DMV1200'
			 when rczcd_zona IN('DMV1106','DMV1206','DMV1301','DMV1302','DMV1303','DMV1304','DMV4203') THEN 'DMV1300'
			 when rczcd_zona IN('DMV3101','DMV3102','DMV3103','DMV3104','DMV3105','DMV3106','DMV3107') THEN 'DMV3100'
			 when rczcd_zona IN('DMV3201','DMV3202','DMV3203','DMV3204','DMV3205') THEN 'DMV3200'
			 when rczcd_zona IN('DMV3301','DMV3302','DMV3303','DMV3304','DMV3305') THEN 'DMV3300'
			 when rczcd_zona IN('DMV4102','DMV4103','DMV4104','DMV4201','DMV4202','DMV4208') THEN 'DMV4200'
			 when rczcd_zona IN('DMV2101','DMV2102','DMV2103','DMV2104','DMV2105','DMV2106','DMV2107','DMV2108','DMV2109') THEN 'DMV2100'
			 when rczcd_zona IN('DMV5102','DMV5104','DMV5105','DMV5108','DMV5109') THEN 'DMV5100'
			 when rczcd_zona IN('DMV5101','DMV5106','DMV5107','DMV5103') THEN 'DMV5300'
			 when rczcd_zona IN('DMV5201','DMV5202','DMV5203') THEN 'DMV5200'
			 when rczcd_zona IN('DMV4101','DMV4204','DMV4205','DMV4207','DMV4106','DMV4107','DMV4206') THEN 'DMV4100'
			 when rczcd_zona = 'DMV6100' THEN 'DMV6100'
			 when rczcd_zona = 'DMV6200' THEN 'DMV6200'
			 else '' END AS supervisao,

		case when rczcd_zona IN ('DMV1000','DMV1100','DMV1101','DMV1107','DMV1102','DMV1201','DMV1202','DMV1207','DMV1200','DMV1103',
			'DMV1104','DMV1105','DMV1203','DMV1204','DMV1205') Then 'REGIONAL SP'
			when rczcd_zona IN ('DMV7000','DMV1300','DMV1106','DMV1206','DMV1301','DMV1302','DMV1303','DMV1304','DMV4203') then 'REGIONAL SP INT'
			when rczcd_zona IN ('DMV3000','DMV3100','DMV3101','DMV3102','DMV3103','DMV3104','DMV3105','DMV3106','DMV3107','DMV3200',
			'DMV3201','DMV3202','DMV3203','DMV3204','DMV3205','DMV3300','DMV3301','DMV3302','DMV3303','DMV3304','DMV3305') then 'REGIONAL SUL'
			when rczcd_zona IN ('DMV4000','DMV2100','DMV2101','DMV2102','DMV2103','DMV2104','DMV2105','DMV2109','DMV2106','DMV2107',
			'DMV2108','DMV4200','DMV4102','DMV4103','DMV4104','DMV4201','DMV4202','DMV4208') then 'REGIONAL MG/RJ/ES'
			when rczcd_zona IN ('DMV5000','DMV5100','DMV5102','DMV5104','DMV5105','DMV5108','DMV5300','DMV5101','DMV5106','DMV5109','DMV5107','DMV5103',
			'DMV5200','DMV5201','DMV5202','DMV5203','DMV4100','DMV4101','DMV4204','DMV4205','DMV4206','DMV4207','DMV4106','DMV4107') then 'REGIONAL NO/NE/CO'
			when rczcd_zona IN ('DMV6000','DMV6100','DMV6200') then 'CANAL SEGURADORAS'
			when rczcd_zona IN ('DMV4300','DMV4301','DMV4302') then 'SBTEC'
			else 'OUTROS' END AS regional,
		csidescricao AS status,
		connumero_migrado AS nr_termo_migrado,
		connumero_antigo AS nr_termo_anterior,
		msubdescricao AS motivo,
		TO_CHAR(contrato.condt_ini_vigencia, 'mm/yyyy') AS dt_base
	FROM
		contrato
	INNER JOIN
		clientes ON conclioid=clioid
	LEFT JOIN
		tipo_contrato ON tpcoid=conno_tipo
	LEFT JOIN
		regiao_comercial_zona ON conrczoid=rczoid
	LEFT JOIN
		equipamento_classe ON coneqcoid=eqcoid
	LEFT JOIN
		contrato_situacao ON csioid = concsioid
	LEFT JOIN
		veiculo ON conveioid=veioid
	LEFT JOIN
		modelo on veimlooid=mlooid
	LEFT JOIN
		tipo_veiculo on mlotipveioid=tipvoid
	LEFT JOIN
		motivo_substituicao on conmsuboid = msuboid
	WHERE
		condt_exclusao IS NULL
	and
		eqcdescricao NOT IN ('SASMOBILE ALÇA','SASMOBILE IMÃ')
	and
		csidescricao NOT IN ('Rescisão por inadimplencia', 'Rescisão')
	and
		tpcdescricao not ilike 'Ex-%'
	and
		condt_ini_vigencia IS NOT NULL
	and
		tpcoid NOT IN (890, 891)
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    contrato.condt_ini_vigencia BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                ;
            ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_base_ativa_mes_[anterior][atual].
     *
     * @param String $mes Mês a ser processado.
     *
     * @return String
     */
    public function criarBaseAtivaMesEspecifico($mes) {
        $sql = "";

        if (in_array($mes, array('anterior', 'atual'))) {
            $sql.= "
                DROP TABLE IF EXISTS
                    tb_tmp_base_ativa_mes_$mes;

                CREATE TEMPORARY TABLE
                    tb_tmp_base_ativa_mes_$mes
                AS
                    SELECT
                        tb_tmp_base_ativa.*
                    FROM
                        tb_tmp_base_ativa
            ";

            if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
                $varInicial = "rpcdt_mes_".$mes."_inicial";
                $varFinal   = "rpcdt_mes_".$mes."_final";

                $sql.= "
                    WHERE
                        tb_tmp_base_ativa.dt_vigencia BETWEEN '".$this->param->$varInicial."' AND '".$this->param->$varFinal."'
                ";
            }

            $sql.= "
                ;
            ";
        }

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_base_nf_detalhada.
     *
     * @return String
     */
    public function criarBaseNfDetalhada() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_base_nf_detalhada;

            CREATE TEMPORARY TABLE
                tb_tmp_base_nf_detalhada
            AS
                SELECT
                    base_nf_detalhada.*
                FROM
                    base_nf_detalhada
                WHERE
                    0 = 0
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    base_nf_detalhada.emissao BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    base_nf_detalhada.codigo_do_cliente = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                ;
            ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_calculo_entrada.
     *
     * @return String
     */
    public function criarCalculoEntrada() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_calculo_entrada;

            CREATE TEMPORARY TABLE
                tb_tmp_calculo_entrada
            AS
                SELECT
                    tb_tmp_dados_entrada.dmv,
                    tb_tmp_dados_entrada.cliente,
                    ROUND(
                        SUM(
                            CASE
                                WHEN TO_CHAR(tb_tmp_dados_entrada.data, 'yyyymm') = '".$this->param->rpcnm_referencia_inicial."' THEN
                                    tb_tmp_dados_entrada.monitoramento
                            ELSE
                                0
                            END
                        )::NUMERIC, 2
                    ) AS valor_monitoramento_mes_anterior,
                    ROUND(
                        SUM(
                            CASE
                                WHEN TO_CHAR(tb_tmp_dados_entrada.data, 'yyyymm') = '".$this->param->rpcnm_referencia_final."' THEN
                                    (30 - CAST(TO_CHAR(tb_tmp_dados_entrada.data, 'dd') AS INT)) * ROUND(tb_tmp_dados_entrada.monitoramento::NUMERIC / 30, 2)
                            ELSE
                                0
                            END
                        )::NUMERIC, 2
                    ) AS valor_monitoramento_mes_atual
                FROM
                    tb_tmp_dados_entrada
                WHERE
                    tb_tmp_dados_entrada.dmv IS NOT NULL
                GROUP BY
                    tb_tmp_dados_entrada.dmv,
                    tb_tmp_dados_entrada.cliente;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_contrato_agg.
     *
     * @return String
     */
    public function criarContratoAgg() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_contrato_agg;

            CREATE TEMPORARY TABLE
                tb_tmp_contrato_agg
            AS
                SELECT
                    tb_tmp_contrato_mes_todos.nr_termo,
                    SUM(
                        CASE
                            WHEN tb_tmp_contrato_mes_todos.periodo = 'anterior' THEN
                                tb_tmp_contrato_mes_todos.valor
                        ELSE
                            0
                        END
                    ) AS valor_anterior,
                    SUM(
                        CASE
                            WHEN tb_tmp_contrato_mes_todos.periodo = 'atual' THEN
                                tb_tmp_contrato_mes_todos.valor
                        ELSE
                            0
                        END
                    ) AS valor_atual
                FROM
                    tb_tmp_contrato_mes_todos
                GROUP BY
                    tb_tmp_contrato_mes_todos.nr_termo;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_contrato_faturado.
     *
     * @return String
     */
    public function criarContratoFaturado() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_contrato_faturado;

            CREATE TEMPORARY TABLE
                tb_tmp_contrato_faturado
            AS
                SELECT
                    simulacao_por_grupo.*,
                    CASE
                        WHEN simulacao_por_grupo.nr_termo = base_ativa.nr_termo THEN
                            TRUE
                    ELSE
                        FALSE
                    END AS base_ativa
                FROM
                    tb_tmp_simulacao_por_grupo AS simulacao_por_grupo
                        LEFT JOIN tb_tmp_base_ativa_mes_atual AS base_ativa ON simulacao_por_grupo.nr_termo = base_ativa.nr_termo
                WHERE
                    simulacao_por_grupo.valor_atual > 0;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_contrato_mes_[anterior][atual][todos].
     *
     * @param String $mes Mês a ser processado.
     *
     * @return String
     */
    public function criarContratoMesEspecifico($mes) {
        $sql = "";

        if (in_array($mes, array('anterior', 'atual'))) {
            $sql.= "
                DROP TABLE IF EXISTS
                    tb_tmp_contrato_mes_$mes;

                CREATE TEMPORARY TABLE
                    tb_tmp_contrato_mes_$mes
                AS
                    SELECT
                        receita_monitoramento.nr_termo,
                        SUM(receita_monitoramento.valor) AS valor,
                        CASE
                            WHEN receita_monitoramento.nr_termo = base_ativa.nr_termo AND base_ativa.dmv NOT IN ('DMV4301', 'DMV4302') THEN
                                'Freteiro'
                            WHEN receita_monitoramento.nr_termo = base_ativa.nr_termo AND base_ativa.dmv IN ('DMV4301', 'DMV4302') THEN
                                'Associação'
                        ELSE
                            'Corporativo'
                        END AS tipo,
                        base_ativa.dmv,
                        COUNT(1) AS quantidade
                    FROM
                        tb_tmp_receita_monitoramento_mes_$mes AS receita_monitoramento
                            LEFT JOIN
                                tb_tmp_base_ativa_mes_$mes base_ativa ON receita_monitoramento.nr_termo = base_ativa.nr_termo
                    GROUP BY
                        receita_monitoramento.nr_termo,
                        base_ativa.nr_termo,
                        base_ativa.dmv;
            ";
        } elseif ($mes == 'todos') {
            $sql.= "
                DROP TABLE IF EXISTS
                    tb_tmp_contrato_mes_$mes;

                CREATE TEMPORARY TABLE
                    tb_tmp_contrato_mes_$mes
                AS
                    SELECT
                        *,
                        'anterior'::VARCHAR AS periodo
                    FROM
                        tb_tmp_contrato_mes_anterior

                    UNION

                    SELECT
                        *,
                        'atual'::VARCHAR AS periodo
                    FROM
                        tb_tmp_contrato_mes_atual;
            ";
        }

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_dados_entrada.
     *
     * @return String
     */
    public function criarDadosEntrada() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_dados_entrada;

            CREATE TEMPORARY TABLE
                tb_tmp_dados_entrada
            AS
                SELECT
                    contrato.connumero AS nr_termo,
                    clientes.clioid AS cliente,
                    comissao_instalacao.cmidata AS data,
                    contrato_pagamento.cpagvl_servico AS locacao,
                    CASE
                        WHEN contrato_pagamento.cpagmonitoramento IS NULL THEN
                            tipo_contrato_faturamento.tcfvalor_monitoramento
                    ELSE
                        contrato_pagamento.cpagmonitoramento
                    END AS monitoramento,
                    proposta.prpprazo_contrato AS prazo,
                    cond_pgto_venda.cpvparcela AS parcela,
                    regiao_comercial_zona.rczcd_zona AS dmv
                FROM
                    contrato
                INNER JOIN
                    clientes ON contrato.conclioid = clientes.clioid
                INNER JOIN
                    comissao_instalacao ON contrato.connumero = comissao_instalacao.cmiconoid
                LEFT JOIN
                    contrato_pagamento ON contrato.connumero = contrato_pagamento.cpagconoid
                LEFT JOIN
                    proposta ON contrato.connumero = proposta.prptermo
                LEFT JOIN
                    proposta_pagamento ON proposta.prpoid = proposta_pagamento.ppagprpoid
                LEFT JOIN
                    cond_pgto_venda ON proposta_pagamento.ppagcpvoid = cond_pgto_venda.cpvoid
                LEFT JOIN
                    regiao_comercial_zona ON contrato.conrczoid = regiao_comercial_zona.rczoid
                LEFT JOIN
                    tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                LEFT JOIN
                    tipo_contrato_faturamento ON
                        (
                            contrato.conno_tipo = tipo_contrato_faturamento.tcftpcoid
                        AND
                            contrato.coneqcoid = tipo_contrato_faturamento.tcfeqcoid
                        )
                WHERE
                    comissao_instalacao.cmiexclusao IS NULL
                AND
                    comissao_instalacao.cmiotioid = 3
                /*
                AND
                    comissao_instalacao.cmiotioid IN
                        (
                            SELECT
                                os_tipo_item.otioid
                            FROM
                                os_tipo_item
                            WHERE
                                os_tipo_item.otitipo = 'E'
                            AND
                                os_tipo_item.otiostoid = 1
                        )
                */
                AND
                    tipo_contrato.tpcdescricao NOT ILIKE 'EX-%'
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    comissao_instalacao.cmidata BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                UNION

                SELECT
                    contrato.connumero AS nr_termo,
                    clientes.clioid AS cliente,
                    contrato.condt_ini_vigencia AS data,
                    contrato_pagamento.cpagvl_servico AS locacao,
                    CASE
                        WHEN contrato_pagamento.cpagmonitoramento IS NULL THEN
                            tipo_contrato_faturamento.tcfvalor_monitoramento
                    ELSE
                        contrato_pagamento.cpagmonitoramento
                    END AS monitoramento,
                    proposta.prpprazo_contrato AS prazo,
                    cond_pgto_venda.cpvparcela AS parcela,
                    regiao_comercial_zona.rczcd_zona AS dmv
                FROM
                    contrato
                INNER JOIN
                    clientes ON contrato.conclioid = clientes.clioid
                INNER JOIN
                    comissao_instalacao ON contrato.connumero = comissao_instalacao.cmiconoid
                LEFT JOIN
                    contrato_pagamento ON contrato.connumero = contrato_pagamento.cpagconoid
                LEFT JOIN
                    proposta ON contrato.connumero = proposta.prptermo
                LEFT JOIN
                    proposta_pagamento ON proposta.prpoid = proposta_pagamento.ppagprpoid
                LEFT JOIN
                    cond_pgto_venda ON proposta_pagamento.ppagcpvoid = cond_pgto_venda.cpvoid
                LEFT JOIN
                    regiao_comercial_zona ON contrato.conrczoid = regiao_comercial_zona.rczoid
                LEFT JOIN
                    tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                LEFT JOIN
                    tipo_contrato_faturamento ON
                        (
                            contrato.conno_tipo = tipo_contrato_faturamento.tcftpcoid
                        AND
                            contrato.coneqcoid = tipo_contrato_faturamento.tcfeqcoid
                        )
                WHERE
                    contrato.connumero_antigo IS NULL
                AND
                    contrato.connumero_migrado IS NOT NULL
                AND
                    tpcoid NOT IN (890, 891)
                AND
                    tpcdescricao NOT ILIKE 'Ex-%'
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    contrato.condt_migracao BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                UNION

                SELECT
                    contrato.connumero AS nr_termo,
                    clientes.clioid AS cliente,
                    contrato.condt_ini_vigencia AS data,
                    contrato_pagamento.cpagvl_servico AS locacao,
                    CASE
                        WHEN contrato_pagamento.cpagmonitoramento IS NULL THEN
                            tipo_contrato_faturamento.tcfvalor_monitoramento
                    ELSE
                        contrato_pagamento.cpagmonitoramento
                    END AS monitoramento,
                    proposta.prpprazo_contrato AS prazo,
                    cond_pgto_venda.cpvparcela AS parcela,
                    regiao_comercial_zona.rczcd_zona AS dmv
                FROM
                    contrato
                INNER JOIN
                    clientes ON contrato.conclioid = clientes.clioid
                INNER JOIN
                    comissao_instalacao ON contrato.connumero = comissao_instalacao.cmiconoid
                LEFT JOIN
                    contrato_pagamento ON contrato.connumero = contrato_pagamento.cpagconoid
                LEFT JOIN
                    contrato_situacao ON contrato.concsioid = contrato_situacao.csioid
                LEFT JOIN
                    equipamento_classe ON contrato.coneqcoid = equipamento_classe.eqcoid
                LEFT JOIN
                    motivo_substituicao ON contrato.conmsuboid = motivo_substituicao.msuboid
                LEFT JOIN
                    proposta ON contrato.connumero = proposta.prptermo
                LEFT JOIN
                    proposta_pagamento ON proposta.prpoid = proposta_pagamento.ppagprpoid
                LEFT JOIN
                    cond_pgto_venda ON proposta_pagamento.ppagcpvoid = cond_pgto_venda.cpvoid
                LEFT JOIN
                    regiao_comercial_zona ON contrato.conrczoid = regiao_comercial_zona.rczoid
                LEFT JOIN
                    tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                LEFT JOIN
                    tipo_contrato_faturamento ON
                        (
                            contrato.conno_tipo = tipo_contrato_faturamento.tcftpcoid
                        AND
                            contrato.coneqcoid = tipo_contrato_faturamento.tcfeqcoid
                        )
                LEFT JOIN
                    veiculo ON contrato.conveioid = veiculo.veioid
                LEFT JOIN
                    modelo ON veiculo.veimlooid = modelo.mlooid
                LEFT JOIN
                    tipo_veiculo ON modelo.mlotipveioid = tipo_veiculo.tipvoid
                WHERE
                    contrato.condt_exclusao IS NULL
                AND
                    equipamento_classe.eqcdescricao IN ('SASMOBILE ALÇA', 'SASMOBILE IMÃ')
                AND
                    contrato_situacao.csidescricao NOT IN ('Rescisão', 'Rescisão por inadimplencia')
                AND
                    motivo_substituicao.msubdescricao IS NULL
                AND
                    tipo_contrato.tpcoid NOT IN (890, 891)
                AND
                    tipo_contrato.tpcdescricao NOT ILIKE 'Ex-%'
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    contrato.condt_cadastro BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                GROUP BY
                    nr_termo,
                    cliente,
                    data,
                    locacao,
                    monitoramento,
                    prazo,
                    parcela,
                    dmv;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_dados_saida.
     *
     * @return string
     */
    public function criarDadosSaida() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_dados_saida;

            CREATE TEMPORARY TABLE
                tb_tmp_dados_saida
            AS
                SELECT
                    contrato.connumero AS nr_termo,
                    TO_CHAR(contrato.condt_exclusao, 'dd/mm/yyyy') AS data,
                    REGEXP_REPLACE(clientes.clinome, '\r|\n', '', 'g') AS cliente,
                    contrato_situacao.csidescricao AS status,
                    regiao_comercial_zona.rczcd_zona AS dmv
                FROM
                    contrato
                INNER JOIN
                    clientes ON contrato.conclioid = clientes.clioid
                LEFT JOIN
                    contrato_situacao ON contrato.concsioid = contrato_situacao.csioid
                LEFT JOIN
                    regiao_comercial_zona ON contrato.conrczoid = regiao_comercial_zona.rczoid
                LEFT JOIN
                    tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                WHERE
                    contrato.condt_ini_vigencia IS NOT NULL
                AND
                    tipo_contrato.tpcdescricao NOT ILIKE 'Ex-%'
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    contrato.condt_exclusao BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                UNION

                SELECT
                    contrato.connumero AS nr_termo,
                    TO_CHAR(contrato.condt_exclusao, 'dd/mm/yyyy') AS data,
                    REPLACE(REPLACE(clientes.clinome, CHR(13), ''), CHR(10), '') AS cliente,
                    contrato_situacao.csidescricao AS status,
                    regiao_comercial_zona.rczcd_zona AS dmv
                FROM
                    contrato
                INNER JOIN
                    clientes ON contrato.conclioid = clientes.clioid
                LEFT JOIN
                    contrato_situacao ON contrato.concsioid = contrato_situacao.csioid
                LEFT JOIN
                    log_contrato_status ON contrato.connumero = lcsconnumero
                -- LEFT JOIN
                --     pre_rescisao ON contrato.connumero = pre_rescisao.presconoid
                LEFT JOIN
                    regiao_comercial_zona ON contrato.conrczoid = regiao_comercial_zona.rczoid
                LEFT JOIN
                    tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                WHERE
                    contrato.condt_ini_vigencia IS NOT NULL
                AND
                    contrato.connumero NOT IN
                        (
                            SELECT
                                sub_contrato.connumero
                            FROM
                                contrato AS sub_contrato
                            LEFT JOIN
                                contrato_situacao AS sub_contrato_situacao ON sub_contrato.concsioid = sub_contrato_situacao.csioid
                            LEFT JOIN
                                tipo_contrato AS sub_tipo_contrato ON sub_contrato.conno_tipo = sub_tipo_contrato.tpcoid
                            WHERE
                                sub_contrato.condt_ini_vigencia IS NOT NULL
                            AND
                                sub_contrato.condt_exclusao IS NULL
                            AND
                                sub_contrato_situacao.csidescricao NOT IN ('Rescisão', 'Rescisão por inadimplencia')
                            AND
                                sub_tipo_contrato.tpcoid NOT IN (890, 891)
                            AND
                                sub_tipo_contrato.tpcdescricao NOT ILIKE 'Ex-%'
                        )
                AND
                    contrato_situacao.csioid = 6
                AND
                    tipo_contrato.tpcoid NOT IN (890, 891)
                AND
                    tipo_contrato.tpcdescricao NOT ILIKE 'Ex-%'
        ";

        if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
            $sql.= "
                AND
                    log_contrato_status.lcsdt_alteracao BETWEEN '".$this->param->rpcdt_referencia_inicial."' AND '".$this->param->rpcdt_referencia_final."'
            ";
        }

        if (isset($this->param->clioid) && trim($this->param->clioid) != '') {
            $sql.= "
                AND
                    clientes.clioid = ".intval($this->param->clioid)."
            ";
        }

        $sql.= "
                GROUP BY
                    nr_termo,
                    data,
                    cliente,
                    status,
                    dmv;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_freteiro_mes_[anterior][atual].
     *
     * @param String $mes Mês a ser processado.
     *
     * @return String
     */
    public function criarFreteiroMesEspecifico($mes) {
        $sql = "";

        if (in_array($mes, array('anterior', 'atual'))) {
            $sql.= "
                DROP TABLE IF EXISTS
                    tb_tmp_freteiro_mes_$mes;

                CREATE TEMPORARY TABLE
                    tb_tmp_freteiro_mes_$mes
                AS
                    SELECT
                        tb_tmp_base_ativa.*
                    FROM
                        tb_tmp_base_ativa
                    WHERE
                        (
                            tb_tmp_base_ativa.segmentacao = 'FRETEIRO'
                        OR
                            tb_tmp_base_ativa.dmv IN ('DMV4301', 'DMV4302')
                        )
            ";

            if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
                $varInicial = "rpcdt_mes_".$mes."_inicial";
                $varFinal   = "rpcdt_mes_".$mes."_final";

                $sql.= "
                    AND
                        tb_tmp_base_ativa.dt_vigencia BETWEEN '".$this->param->$varInicial."' AND '".$this->param->$varFinal."'
                ";
            }

            $sql.= "
                ;
            ";
        }

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_rateio.
     *
     * @return String
     */
    public function criarRateio() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_rateio;

            CREATE TEMPORARY TABLE
                tb_tmp_rateio
            AS
                SELECT
                    ROUND(SUM(tb_tmp_contrato_faturado.valor_atual::NUMERIC), 2) AS valor
                FROM
                    tb_tmp_contrato_faturado
                WHERE
                    tb_tmp_contrato_faturado.base_ativa = FALSE;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_receita_monitoramento_mes_[anterior][atual].
     *
     * @param String $mes Mês a ser processado.
     *
     * @return String
     */
    public function criarReceitaMonitoramentoMesEspecifico($mes) {
        $sql = "";

        if (in_array($mes, array('anterior', 'atual'))) {
            $sql.= "
                DROP TABLE IF EXISTS
                    tb_tmp_receita_monitoramento_mes_$mes;

                CREATE TEMPORARY TABLE
                    tb_tmp_receita_monitoramento_mes_$mes
                AS
                    SELECT
                        tb_tmp_base_nf_detalhada.contrato AS nr_termo,
                        SUM(tb_tmp_base_nf_detalhada.valor_liquido) AS valor
                    FROM
                        tb_tmp_base_nf_detalhada
                    WHERE
                        tb_tmp_base_nf_detalhada.grupo_item_faturado = 'Monitoramento'
                    AND
                        tb_tmp_base_nf_detalhada.natureza IN ('PRESTACAO DE SERVICOS', 'Provisão monitoramento', 'Reversão Provisão monitoramento', 'SBTEC')
                    AND
                        tb_tmp_base_nf_detalhada.tipo = 'CLIENTE'
            ";

            if (isset($this->param->rpcdt_referencia) && trim($this->param->rpcdt_referencia) != '') {
                $varInicial = "rpcdt_mes_".$mes."_inicial";
                $varFinal   = "rpcdt_mes_".$mes."_final";

                $sql.= "
                    AND
                        tb_tmp_base_nf_detalhada.dt_emissao BETWEEN '".$this->param->$varInicial."' AND '".$this->param->$varFinal."'
                ";
            }

            $sql.= "
                    GROUP BY
                        tb_tmp_base_nf_detalhada.contrato;
            ";
        }

        return $sql;
    }

    public function criarReportComercial() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_report_comercial;

            CREATE TEMPORARY TABLE
                tb_tmp_report_comercial
            AS
                SELECT
                    simulacao.dmv,
                    clientes.clinome AS cliente,
                    simulacao.valor_base + simulacao.valor_downsell + (simulacao.valor_upsell - simulacao.valor_upsell_diff) AS vl_base,
                    simulacao.valor_upsell_diff AS vl_up,
                    simulacao.valor_reativacao AS vl_rtvc,
                    simulacao.valor_downsell_diff * -1 AS vl_down,
                    simulacao.valor_outros AS vl_outros,
                    COALESCE(ROUND((agrupamento_saida.valor::NUMERIC * -1), 2), 0) AS vl_churn,
                    COALESCE(ROUND(COALESCE(calculo_entrada.valor_monitoramento_mes_anterior::NUMERIC, 0), 2), 0) AS vl_inst_mes_antr,
                    COALESCE(ROUND(calculo_entrada.valor_monitoramento_mes_atual::NUMERIC, 2), 0) AS vl_inst_mes_atual
                FROM
                    tb_tmp_simulacao AS simulacao
                        LEFT JOIN
                            tb_tmp_agrupamento_saida AS agrupamento_saida ON simulacao.dmv = agrupamento_saida.dmv
                        LEFT JOIN
                            tb_tmp_calculo_entrada AS calculo_entrada ON simulacao.dmv = calculo_entrada.dmv
                        LEFT JOIN
                            clientes ON calculo_entrada.cliente = clientes.clioid;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_simulacao.
     *
     * @param Boolean $freteiro Com ou sem freteiro.
     *
     * @return String
     */
    public function criarSimulacaoFreteiro($freteiro) {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_simulacao".($freteiro ? '_freteiro' : '').";

            CREATE TEMPORARY TABLE
                tb_tmp_simulacao".($freteiro ? '_freteiro' : '')."
            AS
                SELECT
                    base_ativa.dmv,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'up' THEN
                                    simulacao_por_grupo.valor_atual::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_upsell,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'up' THEN
                                    (simulacao_por_grupo.valor_atual - simulacao_por_grupo.valor_anterior)::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_upsell_diff,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'down' THEN
                                    simulacao_por_grupo.valor_atual::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_downsell,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'down' THEN
                                    (simulacao_por_grupo.valor_anterior - simulacao_por_grupo.valor_atual)::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_downsell_diff,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'mesmo_valor' THEN
                                    simulacao_por_grupo.valor_atual::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_base,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'reativacao' THEN
                                    simulacao_por_grupo.valor_atual::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_reativacao,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'pro_rata' THEN
                                    simulacao_por_grupo.valor_atual::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_pro_rata,
                    ROUND(
                        SUM(
                            CASE
                                WHEN simulacao_por_grupo.grupo = 'outros' THEN
                                    simulacao_por_grupo.valor_atual::NUMERIC
                            ELSE
                                0
                            END
                        ), 2
                    ) AS valor_outros
                FROM
                    tb_tmp_base_ativa_mes_atual AS base_ativa
                        LEFT JOIN
                            tb_tmp_simulacao_por_grupo AS simulacao_por_grupo ON base_ativa.nr_termo = simulacao_por_grupo.nr_termo
                WHERE
                    base_ativa.dmv IS NOT NULL
                and
                    base_ativa.segmentacao ".($freteiro ? '=' : '<>')." 'FRETEIRO'
                GROUP BY
                    base_ativa.dmv;
        ";

        return $sql;
    }

    /**
     * Método que determina o comando da tabela: tb_tmp_simulacao_por_grupo.
     *
     * @return String
     */
    public function criarSimulacaoPorGrupo() {
        $sql = "
            DROP TABLE IF EXISTS
                tb_tmp_simulacao_por_grupo;

            CREATE TEMPORARY TABLE
                tb_tmp_simulacao_por_grupo
            AS
                SELECT
                    contrato_agg.nr_termo,
                    CASE
                        WHEN
                            (
                                EXTRACT(YEAR FROM base_ativa.dt_vigencia) = ".$this->param->rpcdt_referencia_ano."
                            AND
                                EXTRACT(MONTH FROM base_ativa.dt_vigencia) = ".$this->param->rpcdt_referencia_mes."
                            )
                        THEN
                            'pro_rata'
                        WHEN
                            (
                                contrato_agg.valor_atual > 0
                            AND
                                contrato_agg.valor_anterior > 0
                            AND
                                contrato_agg.valor_atual > contrato_agg.valor_anterior
                            )
                        THEN
                            'up'
                        WHEN
                            (
                                contrato_agg.valor_atual > 0
                            AND
                                contrato_agg.valor_anterior > 0
                            AND
                                contrato_agg.valor_atual < contrato_agg.valor_anterior
                            )
                        THEN
                            'down'
                        WHEN
                            (
                                contrato_agg.valor_atual > 0
                            AND
                                contrato_agg.valor_anterior > 0
                            AND
                                contrato_agg.valor_atual = contrato_agg.valor_anterior
                            )
                        THEN
                            'mesmo_valor'
                        WHEN
                            (
                                contrato_agg.valor_atual > 0
                            AND
                                (
                                    contrato_agg.valor_anterior IS NULL
                                OR
                                    contrato_agg.valor_anterior = 0
                                )
                            AND
                                base_ativa.dt_vigencia::TIMESTAMP - base_ativa.dt_base::TIMESTAMP > '3 months'::INTERVAL
                            )
                        THEN
                            'reativacao'
                    ELSE
                        'outros'
                    END AS grupo,
                    SUM(contrato_agg.valor_anterior) AS valor_anterior,
                    SUM(contrato_agg.valor_atual) AS valor_atual
                FROM
                    tb_tmp_contrato_agg AS contrato_agg
                        LEFT JOIN
                            tb_tmp_base_ativa_mes_atual AS base_ativa ON contrato_agg.nr_termo = base_ativa.nr_termo
                GROUP BY
                    contrato_agg.nr_termo,
                    grupo;
        ";

        return $sql;
    }

}