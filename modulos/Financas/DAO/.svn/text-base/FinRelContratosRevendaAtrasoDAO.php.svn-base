<?php

/**
 * PrnDebitoAutomaticoDAO.php
 * 
 * Classe de persistência dos dados
 * 
 * @author Renato Teixeira Bueno
 * @email renato.bueno@meta.com.br
 * @since 20/09/2012
 * @package Principal
 *
 */
class FinRelContratosRevendaAtrasoDAO {

    private $conn;
    
    public $clinome;
    
    /*
     * Construtor
     * 
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br
     */
    public function FinRelContratosRevendaAtrasoDAO($conn) {

        $this->conn = $conn;
    }
    
    
    /*
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Array com os tipos de contrato ativos
     * */
    public function buscarTiposContrato($conno_tipo=null) {
        
        $where = "";
        
        if (!empty($conno_tipo)){
            $where .= " AND tpcoid = $conno_tipo";
        }
        
        $sql = "
            SELECT
                tpcoid,
                tpcdescricao    
            FROM
                tipo_contrato
            WHERE
                tpcativo = TRUE
                $where
            ORDER BY tpcdescricao
        ";

        $rs = pg_query($this->conn, $sql);
        return $rs; 
    }
    
    
    /*
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Array com os contratos de revenda em atraso
     * */
    public function buscarContratosRevandaAtraso() {
        
        $where = "";
        
        if ($this->clinome){
            $where .= " AND clinome ILIKE '%$this->clinome%' ";
        }        
        if ($this->connumero){
            $where .= " AND connumero = $this->connumero ";
        }
        if (!$this->diasatraso){
            $this->diasatraso = 1;
        }
        if (isset($this->conno_tipo) && $this->conno_tipo != ""){
            $where .= " AND conno_tipo = $this->conno_tipo ";
        }
        if ($this->nfldt_emissao_ini && $this->nfldt_emissao_fin){
            $where .= " AND nfldt_emissao BETWEEN '$this->nfldt_emissao_ini' AND '$this->nfldt_emissao_fin' ";
        }
        if ($this->rczcd_zona){
            $where .= " AND rczcd_zona = '$this->rczcd_zona' ";
        }

        $sql = "
            SELECT
                connumero,                                      -- Numero Contrato
                condt_cadastro,                                 -- Data Cadastro
                condt_ini_vigencia,                             -- Data Vigencia
                conclioid,                                      -- Codigo Cliente
                clinome,                                        -- Nome Cliente
                cliemail,                                       -- E-mail do Cliente
                CASE clitipo
                    WHEN 'F' THEN clifone_res
                    WHEN 'J' THEN clifone_com
                END AS clifone,
                clifone_com,                                    -- Telefone do Cliente
                tpcoid,                                         -- ID Tipo Contrato
                tpcdescricao,                                   -- Tipo Contrato
                nfloid,                                         -- ID Nota Fiscal
                nflno_numero,                                   -- Numero Nota Fiscal
                nflserie,                                       -- Serie da NF	
                nfldt_emissao,                                  -- Data Emissao Nota 
                nflvl_total,                                    -- Valor Nota
                nfldt_faturamento,                              -- Data Faturamento
                titoid,                                         -- ID do Titulo
                titdt_vencimento,                               -- Vencimento do Titulo
                titno_parcela,                                  -- Numero da Parcela
                titvl_titulo,                                   -- Valor do Titulo
                titvl_pagamento,                                -- Valor do Pagamento
                titdt_pagamento,                                -- Data do pagamento
                rczdescricao,                                   -- Nome Zona Comercial
                rczcd_zona AS DMV                               -- DMV Zona Comercial
            FROM 
                contrato
                INNER JOIN tipo_contrato ON conno_tipo = tpcoid
                INNER JOIN clientes ON clioid = conclioid
                INNER JOIN nota_fiscal_item ON connumero = nficonoid
                INNER JOIN nota_fiscal ON nfloid = nfinfloid
                INNER JOIN titulo ON titnfloid = nfloid
                LEFT JOIN regiao_comercial_zona ON rczoid = conrczoid
            WHERE 	
                conmodalidade = 'V'                         -- Modalidade Revenda (R2)                
                AND condt_exclusao IS NULL                  -- Contrato não excluído (R2)
                AND concsioid = 1                           -- Contrato com status Ativo (R2)
                AND nfldt_cancelamento IS NULL              -- Nota fiscal não cancelada (R3)                
                AND titno_parcela = 1                       -- Titulo com parcela numero 1 (R3)
                AND titdt_pagamento IS NULL                 -- Titulo sem data de pagamento (R3)
				AND titdt_credito IS NULL                   -- Titulo sem data de credito (R3)
                AND titdt_cancelamento IS NULL              -- Titulo sem data de cancelamento (R3)
                AND ('now'::date - titdt_vencimento) >= $this->diasatraso    -- Parâmetro de Dias em Atraso (R3)  
                $where
            GROUP BY 
                connumero, 
                condt_cadastro, 
                condt_ini_vigencia, 
                conclioid, 
                clinome, 
                cliemail,
                clitipo,
                clifone_res,
                clifone_com,
                tpcoid, 
                tpcdescricao, 
                nfloid, 
                nflno_numero, 
                nflserie, 
                nfldt_inclusao, 
                nfldt_nota, 
                nfldt_emissao, 
                nflvl_total, 
                nfldt_faturamento, 
                titoid, 
                titdt_vencimento, 
                titno_parcela, 
                titvl_titulo, 
                titvl_pagamento, 
                titdt_pagamento,
                rczdescricao,
                rczcd_zona
        ";
        $rs = pg_query($this->conn, $sql);
        return $rs;

    } 
    
}

?>
