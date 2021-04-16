<?php
/**
 * Relatório Consulta Nota Fiscal Vivo
 *
 * @package Finanças
 * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 */
class FinConsultaNotaFiscalVivoDAO {


    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     *
     * @param resource $conn conexão
     */
    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }


    /**
     * Método consultarNotaFiscalVivo()
     *
     * @param array $parametros =>  Parâmetros para pesquisa.
     *
     * @return array $retorno
     */
    public function consultarNotaFiscalVivo(stdClass $parametros) {

        $retorno = array();

        $sql = "    
                SELECT DISTINCT
                    nf_serie,
                    codigo_cliente,
                    cliente,
                    vencimento,
                    parcela,
                    ciclo,
                    retorno_vivo,
                    conta,
                    valor_nf,
                    pago,
                    status_sascar,
                    status_vivo,
                    nfloid,
                    data_referencia, 
                    nflno_numero, 
                    nflserie
                FROM 
                    (
                        (
                        SELECT DISTINCT
                            (nflno_numero || '/' || nflserie) AS nf_serie,
                            clioid AS codigo_cliente,
                            clinome AS cliente,
                            to_char(titdt_vencimento, 'dd/mm/yyyy') AS vencimento,
                            titno_parcela AS parcela,
                            vppaciclo AS ciclo,
                            to_char(vpedt_evento, 'dd/mm/yyyy') AS retorno_vivo,
                            vppaconta AS conta,
                            nflvl_total AS valor_nf,
                            titvl_pagamento AS pago,
                            CASE
                                WHEN nfldt_cancelamento IS NOT NULL THEN 'Cancelada'
                                WHEN titvl_titulo_venda = 0 THEN ''
                                ELSE CASE
                                         WHEN titvl_pagamento > 0 THEN 'Pago'
                                         ELSE CASE
                                                  WHEN ('now'::date-titdt_vencimento) > 0 THEN 'Vencido'
                                                  ELSE 'À vencer'
                                         END
                                     END
                            END AS status_sascar,
                            vpesstatus AS status_vivo,
                            nfloid,
                            TO_CHAR(nfldt_referencia,'mm/YYYY') AS data_referencia,
                            nflno_numero, 
                            nflserie
                        FROM nota_fiscal_venda
                        INNER JOIN nota_fiscal_item_venda ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
                        INNER JOIN titulo_venda ON (titnfloid = nfloid)
                        INNER JOIN clientes ON (titclioid = clioid)
                        INNER JOIN contrato ON (connumero = nficonoid AND conno_tipo = 844)
                        INNER JOIN veiculo ON (veioid = conveioid)
                        INNER JOIN obrigacao_financeira ON (obroid = nfiobroid)
                        INNER JOIN veiculo_pedido_parceiro ON (vppaconoid = connumero)";

        $sql .= $this->montarFiltroPesquisa($parametros);

        //Filtro por Status Sascar (Não obrigatório)
        if ( isset($parametros->status_sascar) && trim($parametros->status_sascar) != '') {
            switch(strtoupper(trim($parametros->status_sascar))) {
                case "C":
                    $sql .= " AND nfldt_cancelamento IS NOT NULL ";
                    break;
                case "P":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo_venda > 0
                            AND titvl_pagamento > 0 ";
                    break;
                case "V":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo_venda > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) > 0 ";
                    break;
                case "A":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo_venda > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) <= 0 ";
                    break;

            }

        }

        //Filtro por Status Vivo (Não obrigatório)
        if (!empty($parametros->status_vivo)) {
            if (!in_array('-1', $parametros->status_vivo)) {
                $sql .= "AND (";
                foreach($parametros->status_vivo AS $key => $itemArray) {
                    if ($key > 0) {
                        $sql .= " OR ";
                    }
                    $sql.= " vpescodigo = '" . strtoupper($itemArray) . "' ";
                }
                $sql .= " ) ";
            }
        }
        //Filtro por cliente (Não obrigatório)
        if ( isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%". trim($parametros->cliente) ."%' ";
        }

        //Filtro por cpf-cnpj (Não obrigatório)
        if ( isset($parametros->cpfcnpj) && trim($parametros->cpfcnpj) != '') {
            $sql .= "AND ( clino_cgc = " . intval($parametros->cpfcnpj) . " OR clino_cpf= " . intval($parametros->cpfcnpj) . ") ";
        }

        //Filtro por nota fiscal (Não obrigatório)
        if ( isset($parametros->nota_fiscal) && trim($parametros->nota_fiscal) != '') {
            $sql .= "AND nflno_numero = " . intval($parametros->nota_fiscal) . " ";
        }

        //Filtro por serie (Não obrigatório)
        if ( isset($parametros->serie) && trim($parametros->serie) != '') {
            $sql .= "AND nflserie = '" . $parametros->serie . "' ";
        }

        $sql .= " ";

        //============================ Notas Locação =======================================================
        $sql .= "
                        )
                        UNION ALL 
                        (
                        SELECT DISTINCT
                            (nflno_numero || '/' || nflserie) AS nf_serie,
                            clioid AS codigo_cliente,
                            clinome AS cliente,
                            to_char(titdt_vencimento, 'dd/mm/yyyy') AS vencimento,
                            titno_parcela AS parcela,
                            vppaciclo AS ciclo,
                            to_char(vpedt_evento, 'dd/mm/yyyy') AS retorno_vivo,
                            vppaconta AS conta,
                            nflvl_total AS valor_nf,
                            titvl_pagamento AS pago,
                            CASE
                                WHEN nfldt_cancelamento IS NOT NULL THEN 'Cancelada'
                                WHEN titvl_titulo = 0 THEN ''
                                ELSE CASE
                                         WHEN titvl_pagamento > 0 THEN 'Pago'
                                         ELSE CASE
                                                  WHEN ('now'::date-titdt_vencimento) > 0 THEN 'Vencido'
                                                  ELSE 'À vencer'
                                         END
                                     END
                            END AS status_sascar,
                            vpesstatus AS status_vivo,
                            nfloid,
                            TO_CHAR(nfldt_referencia,'mm/YYYY') AS data_referencia,
                            nflno_numero, 
                            nflserie
                        FROM nota_fiscal
                        INNER JOIN nota_fiscal_item ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
                        INNER JOIN titulo ON (titnfloid = nfloid)
                        INNER JOIN clientes ON (titclioid = clioid)
                        INNER JOIN contrato ON (connumero = nficonoid AND conno_tipo = 844 AND conmodalidade = 'L')
                        INNER JOIN veiculo ON (veioid = conveioid)
                        INNER JOIN obrigacao_financeira ON (obroid = nfiobroid)
                        INNER JOIN veiculo_pedido_parceiro ON (vppaconoid = connumero)";

        $sql .= $this->montarFiltroPesquisa($parametros);


        //Filtro por Status Sascar (Não obrigatório)
        if ( isset($parametros->status_sascar) && trim($parametros->status_sascar) != '') {
            switch(strtoupper(trim($parametros->status_sascar))) {
                case "C":
                    $sql .= " AND nfldt_cancelamento IS NOT NULL ";
                    break;
                case "P":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo > 0
                            AND titvl_pagamento > 0 ";
                    break;
                case "V":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) > 0 ";
                    break;
                case "A":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) <= 0 ";
                    break;

            }

        }

        //Filtro por Status Vivo (Não obrigatório)
        if (!empty($parametros->status_vivo)) {
            if (!in_array('-1', $parametros->status_vivo)) {
                $sql .= "AND (";
                foreach($parametros->status_vivo AS $key => $itemArray) {
                    if ($key > 0) {
                        $sql .= " OR ";
                    }
                    $sql.= " vpescodigo = '" . strtoupper($itemArray) . "' ";
                }
                $sql .= " ) ";
            }
        }
        //Filtro por cliente (Não obrigatório)
        if ( isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%". trim($parametros->cliente) ."%' ";
        }

        //Filtro por cpf-cnpj (Não obrigatório)
        if ( isset($parametros->cpfcnpj) && trim($parametros->cpfcnpj) != '') {
            $sql .= "AND ( clino_cgc = " . intval($parametros->cpfcnpj) . " OR clino_cpf= " . intval($parametros->cpfcnpj) . ") ";
        }

        //Filtro por nota fiscal (Não obrigatório)
        if ( isset($parametros->nota_fiscal) && trim($parametros->nota_fiscal) != '') {
            $sql .= "AND nflno_numero = " . intval($parametros->nota_fiscal) . " ";
        }

        //Filtro por serie (Não obrigatório)
        if ( isset($parametros->serie) && trim($parametros->serie) != '') {
            $sql .= "AND nflserie = '" . $parametros->serie . "' ";
        }

        $sql .= "
                        )
                    ) AS p
                ORDER BY nflserie, nflno_numero, parcela, conta, ciclo ASC; ";
        
        //echo "<pre>".$sql;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception(MENSAGEM_ERRO_PROCESSAMENTO . ' - Pesquisar Consulta NF Vivo.');
        }

        while ($row = pg_fetch_object($rs)) {

            $retorno[] = $row;

        }
        //echo "<pre>";print_r($retorno); echo "</pre>";
        return $retorno;

    }

    /**
     * Método consultarNotaFiscalVivoPlaca()
     *
     * @param array $parametros =>  Parâmetros para pesquisa.
     *
     * @return array $retorno
     */
    public function consultarNotaFiscalVivoPlaca(stdClass $parametros) {

        $retorno = array();

        $sql = "
                SELECT DISTINCT
                    nf_serie,
                    codigo_cliente,
                    cliente,
                    vencimento,
                    parcela,
                    ciclo,
                    retorno_vivo,
                    contrato,
                    placa,
                    valor_item_nf,
                    valor_vivo,
                    status_sascar,
                    status_vivo,
                    obrigacao_financeira,
                    data_referencia,
                    nflno_numero, 
                    nflserie
                FROM 
                    (
                        (
                        SELECT DISTINCT
                            (nflno_numero || '/' || nflserie) AS nf_serie,
                            clioid AS codigo_cliente,
                            clinome AS cliente,
                            to_char(titdt_vencimento, 'dd/mm/yyyy') AS vencimento,
                            titno_parcela AS parcela,
                            vppaciclo AS ciclo,
                            to_char(vpedt_evento, 'dd/mm/yyyy') AS retorno_vivo,
                            nficonoid AS contrato,
                            veiplaca AS placa,
                            nfivl_item AS valor_item_nf,
                            vpevl_bruto AS valor_vivo,
                            CASE
                                WHEN nfldt_cancelamento IS NOT NULL THEN 'Cancelada'
                                WHEN titvl_titulo_venda = 0 THEN ''
                                ELSE CASE
                                         WHEN titvl_pagamento > 0 THEN 'Pago'
                                         ELSE CASE
                                                  WHEN ('now'::date-titdt_vencimento) > 0 THEN 'Vencido'
                                                  ELSE 'À vencer'
                                         END
                                     END
                            END AS status_sascar,
                            vpesstatus AS status_vivo,
                            obrobrigacao AS obrigacao_financeira,
                            TO_CHAR(nfldt_referencia,'mm/YYYY') AS data_referencia,
                            nflno_numero, 
                            nflserie
                        FROM nota_fiscal_venda
                        INNER JOIN nota_fiscal_item_venda ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
                        INNER JOIN titulo_venda ON (titnfloid = nfloid)
                        INNER JOIN clientes ON (titclioid = clioid)
                        INNER JOIN contrato ON (connumero = nficonoid AND conno_tipo = 844)
                        INNER JOIN veiculo ON (veioid = conveioid)
                        INNER JOIN obrigacao_financeira ON (obroid = nfiobroid)
                        INNER JOIN veiculo_pedido_parceiro ON (vppaconoid = connumero)";

        $sql .= $this->montarFiltroPesquisa($parametros);

        //Filtro por Status Sascar (Não obrigatório)
        if ( isset($parametros->status_sascar) && trim($parametros->status_sascar) != '') {
            switch(strtoupper(trim($parametros->status_sascar))) {
                case "C":
                    $sql .= " AND nfldt_cancelamento IS NOT NULL ";
                    break;
                case "P":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo_venda > 0
                            AND titvl_pagamento > 0 ";
                    break;
                case "V":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo_venda > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) > 0 ";
                    break;
                case "A":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo_venda > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) <= 0 ";
                    break;

            }

        }

        //Filtro por Status Vivo (Não obrigatório)
        if (!empty($parametros->status_vivo)) {
            if (!in_array('-1', $parametros->status_vivo)) {
                $sql .= "AND (";
                foreach($parametros->status_vivo AS $key => $itemArray) {
                    if ($key > 0) {
                        $sql .= " OR ";
                    }
                    $sql.= " vpescodigo = '" . strtoupper($itemArray) . "' ";
                }
                $sql .= " ) ";
            }
        }
        //Filtro por cliente (Não obrigatório)
        if ( isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%". trim($parametros->cliente) ."%' ";
        }

        //Filtro por cpf-cnpj (Não obrigatório)
        if ( isset($parametros->cpfcnpj) && trim($parametros->cpfcnpj) != '') {
            $sql .= "AND ( clino_cgc = " . intval($parametros->cpfcnpj) . " OR clino_cpf= " . intval($parametros->cpfcnpj) . ") ";
        }

        //Filtro por nota fiscal (Não obrigatório)
        if ( isset($parametros->nota_fiscal) && trim($parametros->nota_fiscal) != '') {
            $sql .= "AND nflno_numero = " . intval($parametros->nota_fiscal) . " ";
        }

        //Filtro por serie (Não obrigatório)
        if ( isset($parametros->serie) && trim($parametros->serie) != '') {
            $sql .= "AND nflserie = '" . $parametros->serie . "' ";
        }

        //Filtro por placa (Não obrigatório)
        if ( isset($parametros->placa) && trim($parametros->placa) != '') {
            $sql .= "AND upper(veiplaca) = '" . strtoupper($parametros->placa) . "' ";
        }

        //Filtro por nfloid (Link)
        if ( isset($parametros->nfloid) && trim($parametros->nfloid) != '') {
            $sql .= "AND nfloid = " . intval($parametros->nfloid) . " ";
        }

        $sql .= " ";

        //============================ Notas Locação =======================================================

        $sql .= "
                        ) 
                        UNION ALL 
                        (
                        SELECT DISTINCT
                            (nflno_numero || '/' || nflserie) AS nf_serie,
                            clioid AS codigo_cliente,
                            clinome AS cliente,
                            to_char(titdt_vencimento, 'dd/mm/yyyy') AS vencimento,
                            titno_parcela AS parcela,
                            vppaciclo AS ciclo,
                            to_char(vpedt_evento, 'dd/mm/yyyy') AS retorno_vivo,
                            nficonoid AS contrato,
                            veiplaca AS placa,
                            nfivl_item AS valor_item_nf,
                            vpevl_bruto AS valor_vivo,
                            CASE
                                WHEN nfldt_cancelamento IS NOT NULL THEN 'Cancelada'
                                WHEN titvl_titulo = 0 THEN ''
                                ELSE CASE
                                         WHEN titvl_pagamento > 0 THEN 'Pago'
                                         ELSE CASE
                                                  WHEN ('now'::date-titdt_vencimento) > 0 THEN 'Vencido'
                                                  ELSE 'À vencer'
                                         END
                                     END
                            END AS status_sascar,
                            vpesstatus AS status_vivo,
                            obrobrigacao AS obrigacao_financeira,
                            TO_CHAR(nfldt_referencia,'mm/YYYY') AS data_referencia,
                            nflno_numero, 
                            nflserie
                        FROM nota_fiscal
                        INNER JOIN nota_fiscal_item ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
                        INNER JOIN titulo ON (titnfloid = nfloid)
                        INNER JOIN clientes ON (titclioid = clioid)
                        INNER JOIN contrato ON (connumero = nficonoid AND conno_tipo = 844 AND conmodalidade = 'L')
                        INNER JOIN veiculo ON (veioid = conveioid)
                        INNER JOIN obrigacao_financeira ON (obroid = nfiobroid)
                        INNER JOIN veiculo_pedido_parceiro ON (vppaconoid = connumero)";

        $sql .= $this->montarFiltroPesquisa($parametros);

        //Filtro por Status Sascar (Não obrigatório)
        if ( isset($parametros->status_sascar) && trim($parametros->status_sascar) != '') {
            switch(strtoupper(trim($parametros->status_sascar))) {
                case "C":
                    $sql .= " AND nfldt_cancelamento IS NOT NULL ";
                    break;
                case "P":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo > 0
                            AND titvl_pagamento > 0 ";
                    break;
                case "V":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) > 0 ";
                    break;
                case "A":
                    $sql .= " AND nfldt_cancelamento IS NULL
                            AND titvl_titulo > 0
                            AND titvl_pagamento = 0
                            AND ('now'::date-titdt_vencimento) <= 0 ";
                    break;

            }

        }

        //Filtro por Status Vivo (Não obrigatório)
        if (!empty($parametros->status_vivo)) {
            if (!in_array('-1', $parametros->status_vivo)) {
                $sql .= "AND (";
                foreach($parametros->status_vivo AS $key => $itemArray) {
                    if ($key > 0) {
                        $sql .= " OR ";
                    }
                    $sql.= " vpescodigo = '" . strtoupper($itemArray) . "' ";
                }
                $sql .= " ) ";
            }
        }
        //Filtro por cliente (Não obrigatório)
        if ( isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%". trim($parametros->cliente) ."%' ";
        }

        //Filtro por cpf-cnpj (Não obrigatório)
        if ( isset($parametros->cpfcnpj) && trim($parametros->cpfcnpj) != '') {
            $sql .= "AND ( clino_cgc = " . intval($parametros->cpfcnpj) . " OR clino_cpf= " . intval($parametros->cpfcnpj) . ") ";
        }

        //Filtro por nota fiscal (Não obrigatório)
        if ( isset($parametros->nota_fiscal) && trim($parametros->nota_fiscal) != '') {
            $sql .= "AND nflno_numero = " . intval($parametros->nota_fiscal) . " ";
        }

        //Filtro por serie (Não obrigatório)
        if ( isset($parametros->serie) && trim($parametros->serie) != '') {
            $sql .= "AND nflserie = '" . $parametros->serie . "' ";
        }

        //Filtro por placa (Não obrigatório)
        if ( isset($parametros->placa) && trim($parametros->placa) != '') {
            $sql .= "AND upper(veiplaca) = '" . strtoupper($parametros->placa) . "' ";
        }

        //Filtro por nfloid (Link)
        if ( isset($parametros->nfloid) && trim($parametros->nfloid) != '') {
            $sql .= "AND nfloid = " . intval($parametros->nfloid) . " ";
        }

        $sql .= " 
                        )
                    )AS p
                ORDER BY nflserie, nflno_numero, ciclo ASC; ";

        //echo "<pre>";print_r($sql);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception(MENSAGEM_ERRO_PROCESSAMENTO . ' - Pesquisar Consulta NF Vivo.');
        }

        while ($row = pg_fetch_object($rs)) {

            $retorno[] = $row;

        }
        //echo "<pre>";print_r($retorno); echo "</pre>";
        return $retorno;

    }

    /**
     * Buscar status
     *
     * @return array $retorno
     */
    public function buscarEventoStatus() {

        $sql = "SELECT
                    vpescodigo,
                    vpesstatus
                FROM
                    veiculo_parceiro_evento_status
                ORDER BY
                    vpesstatus ASC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = array();
        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Buscar status
     *
     * @return array $retorno
     */
    public function buscarSerieNotaFiscal() {

        $sql = "SELECT
                    nfsserie
                FROM
                    nota_fiscal_serie
                WHERE
                    nfsdt_exclusao IS NULL
                ORDER BY
                    nfsserie ASC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = array();
        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    private function montarFiltroPesquisa ($parametros) {

        $sql = "";

        if($parametros->tipo_periodo == "geracao_faturamento") {

            $sql .= "
                        LEFT JOIN veiculo_parceiro_evento ON (upper(vpesubscription) = upper(vppasubscription) AND vpeno_parcela = titno_parcela)
                        LEFT JOIN veiculo_parceiro_evento_status ON (upper(vpescodigo) = upper(vpevpescodigo))
                        WHERE
                            nfldt_inclusao BETWEEN '" . $parametros->dt_evento_de . " 00:00:00' AND '" . $parametros->dt_evento_ate . " 23:59:59'                                		
                            AND CASE WHEN vpeoid IS NOT NULL
				            	THEN vpeoid = (
				            		SELECT a.vpeoid
				            		FROM veiculo_parceiro_evento a
				            		WHERE a.vpenfioid IN (SELECT b.nfioid FROM nota_fiscal_item b WHERE b.nfino_numero = nflno_numero AND b.nfiserie = nflserie)
				            		ORDER BY a.vpeoid DESC LIMIT 1)
			            		ELSE TRUE
				            	END
                    ";
            
        } else {
            $sql .= "
                        INNER JOIN veiculo_parceiro_evento ON (upper(vpesubscription) = upper(vppasubscription) AND vpeno_parcela = titno_parcela)
                        INNER JOIN veiculo_parceiro_evento_status ON (upper(vpescodigo) = upper(vpevpescodigo))
                        WHERE 
                            vpedt_evento BETWEEN '" . $parametros->dt_evento_de . " 00:00:00' AND '" . $parametros->dt_evento_ate . " 23:59:59'                                		
                            AND vpeoid = (
                            	SELECT a.vpeoid 
								FROM veiculo_parceiro_evento a 
								WHERE a.vpenfioid IN (SELECT b.nfioid FROM nota_fiscal_item b WHERE b.nfino_numero = nflno_numero AND b.nfiserie = nflserie) 
								ORDER BY a.vpeoid DESC LIMIT 1)
                    ";
        }

        return $sql;

    }

}