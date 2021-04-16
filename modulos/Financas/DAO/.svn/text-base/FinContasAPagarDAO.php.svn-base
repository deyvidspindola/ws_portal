<?php

// Inclui a classe de funções
include_once "{$libdir}/funcoes.php";

/**
 * Classe responsável pela persistencia de dados.
 *
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 16/01/2017
 * @category Class
 * @package FinContasAPagarDAO
 */

class FinContasAPagarDAO { 
	
    /** Conexão com o banco de dados */
    private $conn;

    /** Usuario logado */
    private $usarioLogado;

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn) {

        //Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
    }

	
    /**
     * Método para buscar dados parametrizados  
	 * (parametros_configuracoes_sistemas).
     *
     * @param codigoParametro, codigoItemParametro
     * @return object
     */
    public function buscaParametros($parametro1,$parametro2){
		
		$sql = "SELECT 
					pcsidescricao
				FROM 
					parametros_configuracoes_sistemas_itens 
				WHERE 
					pcsipcsoid = '".$parametro1."' 
					AND pcsioid = '".$parametro2."'; 
				";
		
		//echo $sql."</br>";
		//exit;
		$rs = pg_query($this->conn,$sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
    	
    }
    
    /**
     * 
     * Retorna as informações da empresa de endereco, agencia de banco, para criar o header do arquivo
     * @param  $tecoid
     * @param  $banco
     */
    public function buscaInformacoesEmpresa($tecoid,$banco){
    			
		$SqlInformacoesEmpresa = "SELECT 
                        				tecoid, 
                        				teccnpj,
                                        tecinscr,
                        				abagencia,
                        				abconta_corrente,
                        				UPPER(formata_str(tecrazao)) AS tecrazao,
                        				UPPER(formata_str(tecendereco)) AS tecendereco,
                        				UPPER(formata_str(tecbairro)) AS tecbairro,
                        				UPPER(formata_str(teccidade)) AS teccidade,
                        				formata_str(teccep) AS cep,
                        				tecuf
                        			FROM 
                        				tectran
                        			INNER JOIN 
                                        apagar_banco ON abtecoid = tecoid 
                        			WHERE 
                        				tecexclusao IS NULL 
                        				AND abdt_exclusao IS NULL
                        				AND abtecoid  = $tecoid 
                        				AND abbancodigo = $banco 
                        			ORDER BY 
                        				tecrazao";

		if (! $resuInfEmpr = pg_query ( $this->conn, $SqlInformacoesEmpresa )) {
			throw new Exception ( "Erro ao efetuar a consulta de busca das informações da empresa" );
		}

        while($registro = pg_fetch_object($resuInfEmpr)){
            $retorno = $registro;
        }

		return $retorno;
	
    }

    /**
     * Busca dados dos título.
     * @return objeto
     */
    public function dadosTitulo($apgoid){

        $retorno = array();

        $sql = "SELECT
                    apgoid, 
                    apgforma_recebimento, 
                    apgtipo_docto, 
                    fortipo, 
                    fordocto, 
                    forbanco, 
                    foragencia,
                    forconta,
                    fordigito_conta,
                    forfornecedor, 
                    fortipo_conta, 
                    apgcodigo_barras, 
                    apglinha_digitavel,
                    (CASE
                        WHEN 
                            (apgdt_pagamento IS NULL)
                        THEN   
                            TO_CHAR(apgdt_vencimento,'ddmmyyyy')
                        ELSE
                            TO_CHAR(apgdt_pagamento,'ddmmyyyy')
                        END
                    ) AS apgdt_pagamento,
                    TO_CHAR(apgdt_vencimento,'ddmmyyyy')                AS apgdt_vencimento, 
                    TO_CHAR(apgperiodo_referencia,'mmyyyy')             AS mesano_referencia, 
                    TO_CHAR(apgperiodo_referencia,'ddmmyyyy')           AS apgperiodo_referencia, 
                    REPLACE( apgvl_apagar::text,'.','' )                AS apgvl_apagar,
                    REPLACE( apgvalor_receita_bruta::text,'.','' )      AS apgvalor_receita_bruta,
                    REPLACE( apgpercentual_receita_bruta::text,'.','' ) AS apgpercentual_receita_bruta,
                    REPLACE( apgvalor_entidades::text,'.','' )          AS apgvalor_entidades,
                    REPLACE( apgvl_desconto::text,'.','' )              AS apgvl_desconto,
                    REPLACE( apgvl_juros::text,'.','' )                 AS apgvl_juros,
                    REPLACE( apgvl_multa::text,'.','' )                 AS apgvl_multa,
                    REPLACE( apgvl_ir::text,'.','' )                    AS apgvl_ir,
                    REPLACE( apgvl_inss::text,'.','' )                  AS apgvl_inss,
                    REPLACE( apgvl_csll::text,'.','' )                  AS apgvl_csll,
                    REPLACE( apgvl_pis::text,'.','' )                   AS apgvl_pis,
                    REPLACE( apgvl_cofins::text,'.','' )                AS apgvl_cofins,
                    REPLACE( apgcsrf::text,'.','' )                     AS apgcsrf,
                    REPLACE( apgvl_iss::text,'.','' )                   AS apgvl_iss,
                    REPLACE( apgvl_tarifa_bancaria::text,'.','')        AS apgvl_tarifa_bancaria,
                    /*REPLACE(
                            (
                            CASE
                                WHEN (apgdt_limite_desconto >= now() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento >= NOW() )) 
                                THEN
                                    (((apgvl_apagar-COALESCE(apgvl_desconto,0))+COALESCE(apgvl_juros,0)+COALESCE(apgvl_multa,0)
                                    -COALESCE(apgvl_ir,0)-COALESCE(apgvl_inss,0)-COALESCE(apgvl_iss,0)-COALESCE(apgvl_csll,0)
                                    -COALESCE(apgvl_pis,0)-COALESCE(apgvl_cofins,0)-COALESCE(apgcsrf,0)))
                                ELSE 
                                    ((apgvl_apagar+COALESCE(apgvl_juros,0)+COALESCE(apgvl_multa,0)
                                    -COALESCE(apgvl_ir,0)-COALESCE(apgvl_inss,0)-COALESCE(apgvl_iss,0)-COALESCE(apgvl_csll,0)
                                    -COALESCE(apgvl_pis,0)-COALESCE(apgvl_cofins,0)-COALESCE(apgvl_desconto,0)-COALESCE(apgcsrf,0)))
                                END
                        )::text,'.',''
                    ) AS valor_documento, --valor total*/
                    REPLACE(
                        (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) + COALESCE(apgvl_tarifa_bancaria,0) ) - (
                                COALESCE(apgvl_desconto,0)
                                + COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)                                    
                            )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                COALESCE(apgvl_desconto,0)
                                + COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '09' ) THEN -- BOLETO FGTS
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                COALESCE(apgvl_desconto,0)
                                + COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '10' ) THEN -- BOLETO GNRE
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                COALESCE(apgvl_desconto,0)
                                + COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '07' ) THEN -- BOLETO GPS
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (                                    
                                  COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )                        
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (                                    
                                  COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )
                        END)::text,'.','' ) AS valor_documento, -- valor Total
                    REPLACE (
                        (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                            (apgvl_apagar + COALESCE(apgvl_tarifa_bancaria,0) ) - ( 
                                COALESCE(apgvl_ir,0)        
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                COALESCE(apgvl_desconto,0)
                                + COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '09' )) THEN -- BOLETO FGTS, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '10' )) THEN -- GNRE, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                            (apgvl_apagar) - (
                                COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )           

                        END)::text,'.','' ) AS valor_titulo_equal_boleto, -- valor igual ao do boleto bancário
                    apgno_notafiscal,
                    apgidentificador_gps,
                    (
                        CASE
                            WHEN apgidentificador_gps IS NOT NULL
                            THEN (SELECT forfornecedor FROM fornecedores WHERE fordocto = apgidentificador_gps and fordocto <> '0' and fordocto <> '' and fordt_exclusao IS NULL LIMIT 1)
                            ELSE ''
                        END
                    ) AS contribuinte_gps,
                    apgno_remessa,
                    apgcodigo_receita,
                    apgnumero_referencia,
                    apgnum_parcela,
                    apgdivida_ativa,
                    apgidentificador_fgts,
                    apginscricao_estadual,
                    apgcnpj_contribuinte
                FROM 
                    apagar 
                INNER JOIN 
                    fornecedores ON apgforoid = foroid
                WHERE
                    apgoid IN ($apgoid) 
                ORDER BY
                    apgforma_recebimento,
                    apgtipo_docto;
                ";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }
    
    
    //Retorna os tipos de querys dos arquivos
    public function retornaQueryArquivos($query){
    		
        if (!$result4 = pg_query ( $this->conn, $query )) {
			throw new Exception ( "ERRO ao efetuar pesquisa para criar o arquivo" );
		} 
		
		return  pg_fetch_object($result4);
    }

	//Retorna o apenas o result  de querys dos arquivos
	public function retornaResultQueryArquivos($query){
		
		if (!$result4 = pg_query ( $this->conn, $query )) {
			throw new Exception ( "ERRO ao efetuar pesquisa para criar o arquivo" );
		}
			
		return  $result4;
	}

	//Retorna o numero de linhas tipos de querys dos arquivos
	public function retornaNumLinhasQueryArquivos($query){
		
		if (!$result4 = pg_query ( $this->conn, $query )) {
			throw new Exception ( "ERRO ao efetuar pesquisa para criar o arquivo" );
		}
			
		return  pg_num_rows($result4);
	}

	//Retorna o numero de linhas tipos de querys dos arquivos
	public function retornaNumColunasQueryArquivos($query){
		
		if (!$result4 = pg_query ( $this->conn, $query )) {
			throw new Exception ( "ERRO ao efetuar pesquisa para criar o arquivo" );
		} 
		
		return  pg_num_fields($result4);
	}
    


    public function retornoTabelaSistema(){
    
    	try{
    		
    	    	$query = "SELECT sis_seq_sispag FROM sistema";
          
                                        
    
        if (!$result2 = pg_query ( $this->conn, $query)) {
			throw new Exception ( "ERRO ao buscar tabela sistema" );
		} 

	
    	$sis_seq_sispag = pg_fetch_result($result2, 0, "sis_seq_sispag");

    	}catch ( Exception $e ) {
			throw new Exception ( "ERRO ao buscar tabela sistema" );
		}
		
		return  $sis_seq_sispag;
    
    }
    
    public function UpdateTabelaSistema(){
    
    	$retorno = false;
    	try{
    		
    	  $sql = "UPDATE sistema SET sis_seq_sispag=(coalesce(sis_seq_sispag,0)+1) ";
          
                                        
    
        if (!pg_query ( $this->conn, $sql)) {
			throw new Exception ( "Erro ao atualizar a tabela sistema" );
		} else{
			$retorno = true;
		}


    	}catch ( Exception $e ) {
			throw new Exception ( "Erro ao atualizar a tabela sistema" );
		}
		
		return  $retorno;
    
    }
    
	public function UpdateTabelaApagar($lista_apgoid,$sis_seq_sispag){
	
		$retorno = false;
		
		$query = "
			UPDATE 
				apagar
			SET 
				apgno_remessa = '$sis_seq_sispag'
			WHERE 
				apgoid IN ($lista_apgoid); ";
		
		if (!pg_query ( $this->conn, $query)) {
			throw new Exception ( "Erro ao executar atualização de registros tabela apagar" );
		}
		else{
			$retorno = true;
		}
			
		return $retorno;
	}
   
   public function retornaTodasEmpresas(){

    $retorno = array();

    $sql = "SELECT 
                tecoid,
                tecrazao
            FROM 
                tectran
            WHERE 
                tecexclusao IS NULL
            AND 
                tecoid NOT IN (2,5)
            ORDER BY 
                tecrazao";
            
    $rs = $this->executarQuery($sql);

    while($registro = pg_fetch_object($rs)){
        $retorno[] = $registro;
    }

    return $retorno;
   }
   
    public function retornaBanco(){

        $retorno = array();

        $sql = "SELECT 
                    bancodigo, 
                    bannome
                FROM 
                    banco
                WHERE 
                    bancodigo IN (341)";

    $rs = $this->executarQuery($sql);

    while($registro = pg_fetch_object($rs)){
        $retorno[] = $registro;
    }

    return $retorno;
   }
   
	public function retornaStatus(){
		
		$retorno = array();
		
		$sql = "
			SELECT
				apgsoid,
				apgsdescricao
			FROM
				apagar_status
			WHERE
				apgsbancoid = 341 
			AND
				apgsdt_exclusao IS NULL
			ORDER BY 
				apgsdescricao ";
		
		$rs = $this->executarQuery($sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}

    public function retornaPesquisaGeraArquivo($parametros){

        $retorno = array();

        $sql = "SELECT 
                    tecoid,
                    tecrazao,
                    apgoid,
                    FO.fordocto,
                    FO.fortipo,
                    forfornecedor,
                    apgentoid,
                    entno_parcela,
                    apgforma_recebimento,
                    apgno_remessa,
                    CASE WHEN apgforma_recebimento = 0 THEN ''||FO.forfornecedor||'' ELSE FO.forfornecedor END AS fornecedor,
                    CASE WHEN apgentoid IS NULL THEN apgno_notafiscal::text ELSE (EN.entnota||'/'||coalesce(EN.entserie,'')) END AS doc,
                    TO_CHAR(apgdt_vencimento,'dd/mm/yyyy') AS apgdt_vencimento,
                    TO_CHAR(apgdt_entrada,'dd/mm/yyyy') AS apgdt_entrada,
                    (CASE
                        WHEN 
                            (apgdt_pagamento IS NULL)
                        THEN   
                            TO_CHAR(apgdt_vencimento,'dd/mm/yyyy')
                        ELSE
                            TO_CHAR(apgdt_pagamento,'dd/mm/yyyy')
                        END
                        ) AS apgdt_pagamento,
                    apgvl_desconto,
                    apgvl_juros,
                    apgvl_multa,
                    SUM((((entivlr_unit)* entiqtde)+ COALESCE(entivlr_ipi, 0) - COALESCE(entidesconto, 0)) + COALESCE(entivl_icms_st, 0)) AS valor_item,
                    count(*),
                    apgvl_apagar,
                    apgvl_ir,
                    apgvl_pis,
                    apgvl_iss,
                    apgvl_inss,
                    apgvl_cofins,
                    apgvl_csll,
                    apgcsrf,
                    apgvl_tarifa_bancaria,
                    apgcodigo_barras,
                    (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) + COALESCE(apgvl_tarifa_bancaria,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)                                    
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '09' ) THEN -- BOLETO FGTS
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '10' ) THEN -- BOLETO GNRE
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '07' ) THEN -- BOLETO GPS
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (                                    
                                  COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )                         
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (                                    
                                      COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                    END) AS valor_pagamento, -- valor Total
                   (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar + COALESCE(apgvl_tarifa_bancaria,0)) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '09' )) THEN -- BOLETO FGTS, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '10' )) THEN -- GNRE, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )           
                        
                    END) AS valor_titulo_equal_boleto, -- valor igual ao do boleto bancário

                   (CASE WHEN apgdt_exclusao IS NOT NULL THEN 1 ELSE 0 END) AS excluido,
                   apgautorizado,
                   (CASE WHEN apgprevisao THEN 1 ELSE 0 END) AS apgprevisao,
                   (CASE WHEN ((apgdt_limite_desconto < now() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento < NOW())) AND apgvl_desconto > 0)
                       THEN 'FF0000'
                       ELSE '000000'
                   END) AS cor_desconto,
                   (CASE WHEN AP.apgentoid IS NULL
                       THEN
                           (CASE WHEN apgplcoid IS NOT NULL
                               THEN (SELECT plcdescricao FROM plano_contabil WHERE plcoid=apgplcoid ORDER BY plcconta)
                           END)
                       ELSE PC.plcdescricao
                   END) AS descricao,
                   (CASE WHEN AP.apgentoid IS NULL
                       THEN
                           (CASE WHEN apgplcoid IS NOT NULL
                               THEN (SELECT plcconta FROM plano_contabil WHERE plcoid=apgplcoid ORDER BY plcconta)
                           END)
                       ELSE PC.plcconta
                   END) AS plcconta,
                   CASE WHEN (
                       CASE WHEN AP.apgentoid IS NULL
                           THEN 
                               (CASE WHEN apgplcoid IS NOT NULL
                                   THEN (SELECT plcconta FROM plano_contabil WHERE plcoid=apgplcoid ORDER BY plcconta)
                               END)
                           ELSE PC.plcconta
                   END) IS NULL THEN 1 ELSE 0 END AS ordem,
                   (CASE WHEN apgdt_cadastro < '2009-11-03' AND apgplcoid IS NULL
                       THEN (SELECT tctdescricao FROM tipo_ctpagar WHERE tctoid = apgtctoid )
                           WHEN apgplcoid IS NULL
                               THEN (SELECT tctdescricao FROM tipo_ctpagar WHERE tctoid = apgtctoid )
                               ELSE (SELECT 'CONTA- ' || plcconta || ' - ' || plcdescricao FROM plano_contabil WHERE plcoid = apgplcoid )
                           END) AS tipo_contas,
                   (SELECT COUNT(DISTINCT entiplcoid) FROM entrada_item WHERE entientoid=apgentoid) AS qtd_contas,
                   apgtipo_docto,
                   apgapgsoid
              FROM apagar AP
				INNER JOIN fornecedores FO ON FO.foroid=AP.apgforoid
         		LEFT JOIN entrada EN ON EN.entoid = AP.apgentoid
         		LEFT JOIN entrada_item IT ON EN.entoid = IT.entientoid
         		LEFT JOIN plano_contabil PC ON PC.plcoid = IT.entiplcoid
              	JOIN tectran ON tectran.tecoid = AP.apgtecoid              
             WHERE AP.apgdt_pgto IS NULL
               	AND (apgdt_exclusao IS NULL OR apgno_remessa IS NOT NULL) ";

        if (isset($parametros->periodo_inicial_busca) && !empty($parametros->periodo_inicial_busca) &&
            isset($parametros->periodo_final_busca) && !empty($parametros->periodo_final_busca)) {

            $sql .= " AND $parametros->consultar_busca::DATE BETWEEN '" . $parametros->periodo_inicial_busca . "' AND '" . $parametros->periodo_final_busca . "'";
        }
        if ( isset($parametros->tiponf_busca) && !empty($parametros->tiponf_busca) ) {
            $sql .= " AND apgentoid IS $parametros->tiponf_busca ";
        }
        if ( isset($parametros->retencao) && !empty($parametros->retencao) ) {
            if ($parametros->retencao == 'NAO') {
                $sql .= " AND apgentoid IS NOT NULL AND apgforoid <> 106 AND apgforoid <> 52 AND apgforoid <> 3312 AND apgforoid <> 3923 ";
            }
        }
        if ( isset($parametros->tecoid) && !empty($parametros->tecoid) ) {
            $sql .= " AND tectran.tecoid = ".$parametros->tecoid;
        }

        $sql .= "
            GROUP BY
                tecoid,
                tecrazao,
                apgoid,
                FO.fordocto,
                FO.fortipo,
                forfornecedor,
                apgentoid,
                entno_parcela,
                fornecedor,
                doc,
                apgdt_vencimento,
                apgdt_entrada,
                apgvl_desconto,
                apgvl_juros,
                apgvl_multa,
                apgvl_apagar,
                apgvl_ir,
                apgvl_pis,
                apgvl_iss,
                apgvl_inss,
                apgvl_cofins,
                apgvl_csll,
                apgvl_tarifa_bancaria,
                valor_pagamento,
                excluido,
                apgautorizado,
                apgforma_recebimento,
                apgprevisao,
                cor_desconto,
                descricao,
                plcconta,
                ordem,
                tipo_contas,
                apgtipo_docto
            ORDER BY 
                tecoid, tecrazao,ordem, doc, apgoid, plcconta, tipo_contas";

        //echo "<pre>";var_dump($sql);echo "</pre>";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }
   
    public function resultadoTitulosPagos($parametros){

        $retorno = array();
   	
        $sql = "SELECT
                    tecoid,
                    tecrazao, 
                    apgoid,
                    CASE
                        WHEN apgforma_recebimento = 0
                            THEN '<font color=red>'||forfornecedor||''
                            ELSE forfornecedor
                        END
                    AS forfornecedor,
                    fordocto,
                    fortipo,
                    CASE
                        WHEN apgentoid IS NULL
                            THEN apgno_notafiscal::TEXT
                            ELSE (SELECT entnota||'/'||COALESCE(entserie,'') FROM entrada where entoid=apgentoid)
                        END
                    AS doc,
                    TO_CHAR(apgdt_vencimento,'dd/mm/yyyy') AS apgdt_vencimento,
                    TO_CHAR(apgdt_entrada,'dd/mm/yyyy') AS apgdt_entrada,
                    TO_CHAR(apgdt_pgto,'dd/mm/yyyy') AS apgdt_pgto, -- data que efetivou o pagamento

                    apgvl_desconto, apgvl_juros, apgvl_multa, apgvl_apagar, apgvl_ir, apgcsrf, apgvl_pis, apgvl_iss, apgvl_inss, apgvl_cofins, apgvl_csll, apgvl_tarifa_bancaria,
                    /*CASE
                        WHEN (apgdt_limite_desconto >= now() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento >= NOW()))
                            THEN (((apgvl_apagar-COALESCE(apgvl_desconto,0))+COALESCE(apgvl_juros,0)+COALESCE(apgvl_multa,0)
                            -COALESCE(apgvl_ir,0)-COALESCE(apgvl_inss,0)-COALESCE(apgvl_iss,0)-COALESCE(apgvl_csll,0)
                            -COALESCE(apgvl_pis,0)-COALESCE(apgvl_cofins,0)+COALESCE(apgvl_tarifa_bancaria,0)-COALESCE(apgcsrf,0)))
                            ELSE ((apgvl_apagar+COALESCE(apgvl_juros,0)+COALESCE(apgvl_multa,0)-COALESCE(apgvl_ir,0)-COALESCE(apgvl_inss,0)
                            -COALESCE(apgvl_iss,0)-COALESCE(apgvl_csll,0)-COALESCE(apgvl_pis,0)-COALESCE(apgvl_cofins,0)
                            -COALESCE(apgvl_desconto,0)+COALESCE(apgvl_tarifa_bancaria,0)-COALESCE(apgcsrf,0)))
                        END
                    AS valor_pagamento, */                    
                    (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar + COALESCE(apgvl_tarifa_bancaria,0) ) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)                                    
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '09' )) THEN -- BOLETO FGTS, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '10' )) THEN -- GNRE, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )           
                        
                    END) AS valor_titulo_equal_boleto, -- valor igual ao do boleto bancário
                    (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)                                    
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '09' ) THEN -- BOLETO FGTS
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '10' ) THEN -- BOLETO GNRE
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '07' ) THEN -- BOLETO GPS
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (                                    
                                  COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )                         
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (                                    
                                      COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                    END) AS valor_pagamento, -- valor Total
                    CASE
                        WHEN apgdt_exclusao IS NOT NULL
                            THEN 1
                            ELSE 0
                        END
                    AS excluido,
                    apgautorizado, apgforma_recebimento,
                    CASE
                        WHEN apgprevisao
                            THEN 1
                            ELSE 0
                        END
                    AS apgprevisao,
                    CASE
                        WHEN ((apgdt_limite_desconto < now() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento < NOW())) and apgvl_desconto > 0)
                            THEN 'FF0000'
                            ELSE '000000'
                        END
                    AS cor_desconto,
                    (CASE
                        WHEN apgplcoid IS NULL
                            THEN (SELECT (plcconta || ' - '|| plcdescricao) AS descricao
                                    FROM plano_contabil
                                    WHERE plcoid = (SELECT i.entiplcoid FROM entrada e INNER JOIN entrada_item i ON e.entoid = i.entientoid WHERE e.entoid = apgentoid LIMIT 1) ORDER BY plcconta)
                            ELSE (SELECT (plcconta || ' - '|| plcdescricao)
                                    FROM plano_contabil
                                    WHERE plcoid=apgplcoid ORDER BY plcconta)
                        END)
                    AS descricao,
                    (CASE
                        WHEN apgplcoid IS NULL
                            THEN (SELECT plcconta
                                    FROM plano_contabil
                                    WHERE plcoid = (SELECT i.entiplcoid FROM entrada e INNER JOIN entrada_item i ON e.entoid = i.entientoid WHERE e.entoid = apgentoid LIMIT 1) ORDER BY plcconta)
                            ELSE (SELECT plcconta
                                    FROM plano_contabil
                                    WHERE plcoid=apgplcoid ORDER BY plcconta)
                        END )
                    AS plcconta,
                    CASE
                        WHEN apgdt_cadastro < '2009-11-03'
                            THEN (SELECT tctdescricao FROM tipo_ctpagar WHERE tctoid = apgtctoid )
                        WHEN apgplcoid IS NULL THEN (SELECT tctdescricao FROM tipo_ctpagar WHERE tctoid = apgtctoid ) else
                            (SELECT plcconta || ' - ' || plcdescricao
                                FROM plano_contabil
                                WHERE plcoid = (SELECT e.entplcoidcontra_partida FROM entrada e WHERE e.entoid = apgentoid ) ORDER BY plcconta)
                        END
                    AS tipo_contas,
                    apgtipo_docto,
                    apgapgsoid,
                    apgcodigo_barras,
                    apgapgsoid,
                    fordocto,
                    fortipo
                    FROM apagar
                    JOIN tectran ON tecoid = apgtecoid
                    INNER JOIN fornecedores ON foroid=apgforoid
                    WHERE apgdt_pgto IS NOT NULL  --AND apgforma_recebimento IS NOT NULL
                    AND apgdt_vencimento IS NOT NULL
                    AND (apgdt_exclusao IS NULL OR apgno_remessa IS NOT NULL) AND apgtctoid<>28 ";
    
        if (isset($parametros->periodo_inicial_busca) && !empty($parametros->periodo_inicial_busca) &&
            isset($parametros->periodo_final_busca) && !empty($parametros->periodo_final_busca)) {

            //altera para o campo da data que realizou efetivamente o pagamentio
            if($parametros->consultar_busca == 'apgdt_pagamento'){
                $parametros->consultar_busca = 'apgdt_pgto';
            }

            $sql .= " AND $parametros->consultar_busca::DATE BETWEEN '" . $parametros->periodo_inicial_busca . "' AND '" . $parametros->periodo_final_busca . "'";
        }
        if ( isset($parametros->tiponf_busca) && !empty($parametros->tiponf_busca) ) {
            $sql .= " AND apgentoid is $parametros->tiponf_busca ";
        }
        if ( isset($parametros->retencao) && !empty($parametros->retencao) ) {
            if ($parametros->retencao == 'NAO') {
                $sql .= " AND apgentoid IS NOT NULL AND apgforoid <> 52 AND apgforoid<>106 AND apgforoid <> 3312 AND apgforoid <> 3923 ";
            }
        }
        if ( isset($parametros->tecoid) && !empty($parametros->tecoid) ) {
            $sql .= " AND tectran.tecoid = ".$parametros->tecoid;
        }
        
        $sql .= " ORDER BY tecoid, tecrazao, plcconta, tipo_contas";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
   }
   
    public function resultadoTitulosAdiantamentoFornecedor($parametros){

   	    $retorno = array();
   		
        $sql = "SELECT 
                    tecoid,
                    tecrazao, 
                    apgoid,
                    CASE
                        WHEN apgforma_recebimento = 0
                        THEN '<font color=red>'||forfornecedor||''
                        ELSE forfornecedor
                    END AS forfornecedor,
                    fordocto,
                    fortipo,
                    CASE
                        WHEN apgentoid IS NULL
                        THEN apgno_notafiscal::TEXT
                        ELSE (SELECT entnota||'/'||COALESCE(entserie,'')
                                FROM entrada
                               WHERE entoid=apgentoid)
                    END AS doc,
                    TO_CHAR(apgdt_vencimento,'dd/mm/yyyy') AS apgdt_vencimento,
                    TO_CHAR(apgdt_entrada,'dd/mm/yyyy') AS apgdt_entrada,
                    apgvl_desconto, 
                    apgvl_juros, 
                    apgvl_multa, 
                    apgvl_apagar, 
                    apgvl_ir, 
                    apgvl_pis, 
                    apgvl_iss, 
                    apgvl_inss, 
                    apgvl_cofins, 
                    apgvl_csll,apgcsrf,
                    /*CASE
                        WHEN (apgdt_limite_desconto >= now() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento >= NOW()))
                        THEN (((apgvl_apagar-COALESCE(apgvl_desconto,0))+COALESCE(apgvl_juros,0)+COALESCE(apgvl_multa,0)
                             -COALESCE(apgvl_ir,0)-COALESCE(apgvl_inss,0)-COALESCE(apgvl_iss,0)-COALESCE(apgvl_csll,0)
                             -COALESCE(apgvl_pis,0)-COALESCE(apgvl_cofins,0)-COALESCE(apgcsrf,0)))
                        ELSE ((apgvl_apagar+COALESCE(apgvl_juros,0)+COALESCE(apgvl_multa,0)-COALESCE(apgvl_ir,0)
                             -COALESCE(apgvl_inss,0)-COALESCE(apgvl_iss,0)-COALESCE(apgvl_csll,0)-COALESCE(apgvl_pis,0)-COALESCE(apgvl_cofins,0)
                             -COALESCE(apgvl_desconto,0)-COALESCE(apgcsrf,0)))
                    END AS valor_pagamento,*/
                    (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)                                    
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '09' ) THEN -- BOLETO FGTS
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '10' ) THEN -- BOLETO GNRE
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '07' ) THEN -- BOLETO GPS
                            (
                            (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (                                    
                                  COALESCE(apgvl_ir,0)
                                + COALESCE(apgvl_pis,0)
                                + COALESCE(apgvl_cofins,0)
                                + COALESCE(apgvl_csll,0)
                                + COALESCE(apgvl_inss,0)
                                + COALESCE(apgvl_iss,0)
                                + COALESCE(apgcsrf,0)
                            )
                            )                        
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (                                    
                                      COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                    END) AS valor_pagamento, -- valor Total
                    (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar + COALESCE(apgvl_tarifa_bancaria,0) ) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '09' )) THEN -- BOLETO FGTS, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '10' )) THEN -- GNRE, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )           
                        
                    END) AS valor_titulo_equal_boleto, -- valor igual ao do boleto bancário
                    CASE
                        WHEN apgdt_exclusao IS NOT NULL
                        THEN 1
                        ELSE 0
                    END AS excluido,
                    apgautorizado, 
                    apgforma_recebimento,
                    CASE
                        WHEN apgprevisao
                        THEN 1
                        ELSE 0
                    END AS apgprevisao,
                    CASE
                        WHEN ((apgdt_limite_desconto < NOW() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento < now())) AND apgvl_desconto > 0)
                        THEN 'FF0000'
                        ELSE '000000'
                    END AS cor_desconto,
                    (CASE
                        WHEN apgplcoid IS NULL
                        THEN (SELECT (plcconta || ' - '|| plcdescricao) AS descricao
                                FROM plano_contabil
                               WHERE plcoid = (SELECT i.entiplcoid FROM entrada e INNER JOIN entrada_item i ON e.entoid = i.entientoid WHERE e.entoid = apgentoid LIMIT 1) ORDER BY plcconta)
                        ELSE (SELECT (plcconta || ' - '|| plcdescricao)
                                FROM plano_contabil
                               WHERE plcoid=apgplcoid ORDER BY plcconta)
                    END) AS descricao,
                    (CASE
                        WHEN apgplcoid IS NULL
                        THEN (SELECT plcconta
                           FROM plano_contabil
                           WHERE plcoid = (SELECT i.entiplcoid FROM entrada e INNER JOIN entrada_item i ON e.entoid = i.entientoid WHERE e.entoid = apgentoid LIMIT 1) ORDER BY plcconta)
                        ELSE (SELECT plcconta
                           FROM plano_contabil
                           WHERE plcoid=apgplcoid ORDER BY plcconta)
                    END ) AS plcconta,
                    (SELECT tctdescricao 
                        FROM tipo_ctpagar 
                        WHERE tctoid = apgtctoid ) AS tipo_contas,
                    apgtipo_docto,
                    apgapgsoid
                FROM apagar
          INNER JOIN fornecedores 
                  ON foroid = apgforoid
                JOIN tectran 
                  ON tecoid = apgtecoid
               WHERE apgdt_pgto IS NOT NULL --AND apgforma_recebimento IS NOT NULL
                 AND apgdt_vencimento IS NOT NULL
                 AND (apgdt_exclusao IS NULL or apgno_remessa IS NOT NULL) 
                 AND apgtctoid=28";

        if (isset($parametros->periodo_inicial_busca) && !empty($parametros->periodo_inicial_busca) &&
            isset($parametros->periodo_final_busca) && !empty($parametros->periodo_final_busca)) {

            $sql .= " AND $parametros->consultar_busca::DATE BETWEEN '" . $parametros->periodo_inicial_busca . "' AND '" . $parametros->periodo_final_busca . "'";
        }
        if ( isset($parametros->tiponf_busca) && !empty($parametros->tiponf_busca) ) {
            $sql .= " AND apgentoid is $parametros->tiponf_busca ";
        }
        if ( isset($parametros->retencao) && !empty($parametros->retencao) ) {
            if ($parametros->retencao == 'NAO') {
                $sql .= " AND apgentoid IS NOT NULL AND apgforoid <> 52 AND apgforoid<>106 AND apgforoid <> 3312 AND apgforoid <> 3923 ";
            }
        }
        if ( isset($parametros->tecoid) && !empty($parametros->tecoid) ) {
            $sql .= " AND tectran.tecoid = ".$parametros->tecoid;
        }

        $sql .= " ORDER BY tecoid, tecrazao, plcconta, tipo_contas"; 
		   
        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }
   
    /**
     * Altera status autorização dos títulos
     * @param  array de Ids
     * @param  usu Id
     * @param  banco
     * @return bool
     */
    public function autorizarTitulos($arrayApgoid,$cd_usuario,$bancoid){

        $retorno = false;

        $this->begin();

        foreach ($arrayApgoid as $chave => $valor) {
        
            $sql = "UPDATE 
                        apagar
                    SET 
                        apgautorizado      = true,
                        apgdt_autorizacao  = now(),
                        apgusu_autorizacao = $cd_usuario,
                        apgapgsoid = (SELECT 
                                            apgsoid
                                        FROM 
                                            apagar_status
                                        WHERE 
                                            apgsbancoid = $bancoid
                                        AND 
                                            apgstipo = 'Remessa' 
                                        AND 
                                            apgscodigo = 10
                    )
                    WHERE 
                        apgoid = $valor";

            $rs = $this->executarQuery($sql);

            if(!$rs){
                $this->rollback();
            }
            
        }

        $this->commit();

		return true;
   }
   
    /**
     * Libera títulos para reenvio
     * @param  array de Ids
     * @param  usu Id
     * @return bool
     */
    public function liberarReenvio($arrayApgoid,$cd_usuario){

        $ids = implode(",", $arrayApgoid);

        $retorno = false;
   	    
		$sql = "UPDATE 
    				apagar 
    			SET 
    				apgapgsoid = (
    					SELECT 
    						apgsoid
    					FROM 
    						apagar_status 
    					WHERE 
    						apgsbancoid = 341 
    						AND apgsdt_exclusao IS NULL
    					    AND apgscodigo = 51
    					LIMIT 1
    				),
                    apgno_remessa = NULL
    			WHERE
    				apgoid IN ($ids); "; 

		$rs = $this->executarQuery($sql);

        if($rs){
            $retorno = true;            
        }

		return $retorno;
   }
   
   
	//busca os dados da proposta transferencia passando os dados nos filtros desejado  e retorna a paginacao
	public function retornoEnvioArquivos($dados,$paginacao= null){

		if (isset($paginacao->limite) && isset($paginacao->offset)) {
			$paginas = "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
		}

		try{
			$sql = "SELECT 
						apceoid,
						apceapgno_remessa,
						apcenome_arquivo,
						apcedt_envio,
						apcedt_retorno,
						tecrazao AS empresa,
						bannome AS banco
					FROM 
						apagar_controle_arquivo 
					INNER JOIN
						tectran ON tecoid = apcetecoid
					INNER JOIN
						banco ON bancodigo = apcebancoid
					 $paginas";

			if (! $result = pg_query ( $this->conn, $sql )) {
				throw new Exception ( "Erro ao efetuar a consulta de busca de envio de arquivos" );
				}
				
		}catch ( Exception $e ) {
				throw new Exception ( "Erro ao efetuar a consulta de busca de envio de arquivos" );
		}
	
	
		
		while ($row = pg_fetch_object($result)) {
			$retorno[] = $row;
		}
		
		return $retorno;
			
	}
	
	//Insere registro de controle de arquivos
	public function insereRegistroControleArquivo($dados) {
		 
		$retorno = false;
			 
		$query = "INSERT INTO 
				        apagar_controle_arquivo (
					       apcetecoid,
					       apcebancoid,
					       apceapgno_remessa,
					       apcenome_arquivo,
					       apceusuoid_cadastro)
				    VALUES
                        (
					       ".$dados->apcetecoid.",
					       ".$dados->apcebancoid.",
					       ".$dados->apceapgno_remessa.",
					       '".$dados->apcenome_arquivo."',
					       ".$dados->apceusuoid_cadastro."
                        ); ";

		if (!pg_query ( $this->conn, $query)) {
			throw new Exception ( "Erro ao inserir registro de controle de arquivos" );
		}
		else{
			$retorno = true;
		}
	
		return $retorno;
	}	

    public function pesquisarEnvioArquivos(stdClass $parametros){

        $retorno = array();
        
        $sql = "SELECT 
    				apceoid,
    				apceapgno_remessa,
    				apcenome_arquivo,
    				apcedt_envio,
    				apcedt_retorno,
    				apcetecoid,
    				tecrazao AS empresa,
    				apcebancoid,
    				bannome AS banco
    			FROM 
    				apagar_controle_arquivo 
    			    INNER JOIN tectran ON tecoid = apcetecoid
    			    INNER JOIN banco ON bancodigo = apcebancoid
    			WHERE
    				TRUE ";

        if ( isset($parametros->num_remessa) && !empty($parametros->num_remessa) ) {
            $sql .= " 
                AND apceapgno_remessa = " . (int) $parametros->num_remessa . "";
        }else{

            if (isset($parametros->periodo_inicial_busca) && !empty($parametros->periodo_inicial_busca) &&
                isset($parametros->periodo_final_busca) && !empty($parametros->periodo_final_busca)) {

                $dataInicial = DateTime::createFromFormat('d/m/Y', $parametros->periodo_inicial_busca);
                $dataFinal = DateTime::createFromFormat('d/m/Y', $parametros->periodo_final_busca);

                $sql .= " 
                    AND apcedt_envio BETWEEN '" . $dataInicial->format('Y-m-d') . " 00:00:00' AND '" . $dataFinal->format('Y-m-d') . " 23:59:59' ";
            }

            if ( isset($parametros->tecoid) && !empty($parametros->tecoid) ) {
                $sql .= " 
                    AND tecoid = " . $parametros->tecoid . "";
            }

            if ( isset($parametros->banco) && !empty($parametros->banco) ) {
                $sql .= " 
                    AND apcebancoid = " . $parametros->banco . "";
            }

            if ( isset($parametros->status) && !empty($parametros->status) ) {
                //Aguardando Processamento
                if($parametros->status == 2){
                    $sql .= " 
                    AND apcedt_retorno IS NULL";
                //Processado
                }elseif($parametros->status == 3){
                    $sql .= " 
                    AND apcedt_retorno IS NOT NULL";
                }
            }
        }

        $rs = $this->executarQuery($sql);
		
		while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }
        return $retorno;
    }

    public function getNomeFornecedor($termo)
    {
        $retorno = array();
        $sql = "SELECT
                    foroid,
                    forfornecedor 
                FROM
                  fornecedores
                WHERE
                  fordt_exclusao IS NULL AND
                  --forfornecedor ILIKE '{$termo}%'
                  to_ascii(forfornecedor,'LATIN1') ilike to_ascii('%{$termo}%', 'LATIN1')
                ORDER BY
                  forfornecedor ASC
                LIMIT 10";

        $query = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($query)){
            $nomeFornecedor = removeAcentos($linha->forfornecedor);
            $retorno[] = array(
                'id' => $linha->foroid,
                'label' => $nomeFornecedor,
                'value' => $nomeFornecedor,
            );
        }
        return $retorno;
    }

	public function pesquisarTitulosProcessados(stdClass $parametros){

		$retorno = array();
		
			$sql = "SELECT   
    					apgoid,
                        apgcodigo_barras,
    					apgvl_desconto, 
    					apgvl_juros, 
    					apgvl_multa, 
    					apgvl_apagar, 
    					apgvl_ir, 
    					apgvl_pis, 
    					apgvl_iss, 
    					apgvl_inss, 
    					apgvl_cofins, 
    					apgvl_csll, 
    					apgcsrf, 
    					apgvl_tarifa_bancaria, 
    					apgtipo_docto, 
    					apgno_remessa,
    					apgscodigo, 
    					apgsdescricao, 
    					apgforma_recebimento,
    					TO_CHAR(apgdt_vencimento,'dd/mm/yyyy') AS apgdt_vencimento, 
    					TO_CHAR(apgdt_entrada,'dd/mm/yyyy') AS apgdt_entrada, 
    					CASE WHEN apgforma_recebimento = 0 THEN ''||forfornecedor||'' ELSE forfornecedor END AS fornecedor, 
    					CASE WHEN apgentoid IS NULL THEN apgno_notafiscal::text ELSE (entnota||'/'||coalesce(entserie,'')) END AS doc, 
    					/*CASE 
    						WHEN (apgdt_limite_desconto >= NOW() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento >= NOW() )) 
    							THEN (((
    								apgvl_apagar-COALESCE(apgvl_desconto,0))
    								+ COALESCE(apgvl_juros,0)
    								+ COALESCE(apgvl_multa,0)
    								-COALESCE(apgvl_ir,0)
    								-COALESCE(apgvl_inss,0)
    								-COALESCE(apgvl_iss,0)
    								-COALESCE(apgvl_csll,0)
    								-COALESCE(apgvl_pis,0)
    								-COALESCE(apgvl_cofins,0)
    								+COALESCE(apgvl_tarifa_bancaria,0)
    								-COALESCE(apgcsrf,0)))
    						ELSE ((
    							apgvl_apagar+COALESCE(apgvl_juros,0)
    							+COALESCE(apgvl_multa,0)
    							+COALESCE(apgvl_tarifa_bancaria,0)
    							-COALESCE(apgvl_ir,0)
    							-COALESCE(apgvl_inss,0)
    							-COALESCE(apgvl_iss,0)
    							-COALESCE(apgvl_csll,0)
    							-COALESCE(apgvl_pis,0)
    							-COALESCE(apgvl_cofins,0)
    							-COALESCE(apgvl_desconto,0)
    							-COALESCE(apgcsrf,0)))
    						END AS valor_pagamento,*/
                        (CASE 
                            WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                                (
                                    (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (
                                        COALESCE(apgvl_desconto,0)
                                        + COALESCE(apgvl_ir,0)
                                        + COALESCE(apgvl_pis,0)
                                        + COALESCE(apgvl_cofins,0)
                                        + COALESCE(apgvl_csll,0)
                                        + COALESCE(apgvl_inss,0)
                                        + COALESCE(apgvl_iss,0)
                                        + COALESCE(apgcsrf,0)                                    
                                    )
                                )
                            WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                                (
                                    (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                        COALESCE(apgvl_desconto,0)
                                        + COALESCE(apgvl_ir,0)
                                        + COALESCE(apgvl_pis,0)
                                        + COALESCE(apgvl_cofins,0)
                                        + COALESCE(apgvl_csll,0)
                                        + COALESCE(apgvl_inss,0)
                                        + COALESCE(apgvl_iss,0)
                                        + COALESCE(apgcsrf,0)
                                    )
                                )
                            WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '09' ) THEN -- BOLETO FGTS
                                (
                                    (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                        COALESCE(apgvl_desconto,0)
                                        + COALESCE(apgvl_ir,0)
                                        + COALESCE(apgvl_pis,0)
                                        + COALESCE(apgvl_cofins,0)
                                        + COALESCE(apgvl_csll,0)
                                        + COALESCE(apgvl_inss,0)
                                        + COALESCE(apgvl_iss,0)
                                        + COALESCE(apgcsrf,0)
                                    )
                                )
                            WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '10' ) THEN -- BOLETO GNRE
                                (
                                    (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                        COALESCE(apgvl_desconto,0)
                                        + COALESCE(apgvl_ir,0)
                                        + COALESCE(apgvl_pis,0)
                                        + COALESCE(apgvl_cofins,0)
                                        + COALESCE(apgvl_csll,0)
                                        + COALESCE(apgvl_inss,0)
                                        + COALESCE(apgvl_iss,0)
                                        + COALESCE(apgcsrf,0)
                                    )
                                )
                            WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '07' ) THEN -- BOLETO GPS
                                (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0) ) - (                                    
                                      COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                                )                        
                            ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                                (
                                    (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (                                    
                                          COALESCE(apgvl_ir,0)
                                        + COALESCE(apgvl_pis,0)
                                        + COALESCE(apgvl_cofins,0)
                                        + COALESCE(apgvl_csll,0)
                                        + COALESCE(apgvl_inss,0)
                                        + COALESCE(apgvl_iss,0)
                                        + COALESCE(apgcsrf,0)
                                    )
                                )
                        END) AS valor_pagamento, -- valor Total
                        (CASE 
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '05') THEN -- BOLETO OUTROS
                            (
                                (apgvl_apagar  + COALESCE(apgvl_tarifa_bancaria,0) ) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND apgtipo_docto = '11' ) THEN -- BOLETO CONCESSIONARIA
                            (
                                (apgvl_apagar + COALESCE(apgvl_juros,0) + COALESCE(apgvl_multa,0)) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '09' )) THEN -- BOLETO FGTS, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        WHEN (apgforma_recebimento = 31 AND ( apgtipo_docto = '10' )) THEN -- GNRE, ....
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_desconto,0)
                                    + COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )
                        ELSE -- DEMAIS TITULOS - OUTROS FORMAS CONTA CORRENTE ...
                            (
                                (apgvl_apagar) - (
                                    COALESCE(apgvl_ir,0)
                                    + COALESCE(apgvl_pis,0)
                                    + COALESCE(apgvl_cofins,0)
                                    + COALESCE(apgvl_csll,0)
                                    + COALESCE(apgvl_inss,0)
                                    + COALESCE(apgvl_iss,0)
                                    + COALESCE(apgcsrf,0)
                                )
                            )           
                        
                    END) AS valor_titulo_equal_boleto, -- valor igual ao do boleto bancário
    					CASE WHEN apgprevisao THEN 1 ELSE 0 END AS apgprevisao, 
    					CASE 
    						WHEN ((apgdt_limite_desconto < NOW() OR (apgdt_limite_desconto IS NULL AND apgdt_vencimento < NOW())) AND apgvl_desconto > 0) 
    							THEN 'FF0000' 
    						ELSE '000000' 
    						END AS cor_desconto, 
    					CASE 
    						WHEN apgentoid IS NULL 
    							THEN 
    								CASE 
    									WHEN apgplcoid IS NOT NULL 
    										THEN (SELECT plcdescricao FROM plano_contabil WHERE plcoid=apgplcoid ORDER BY plcconta) 
    								END 
    						ELSE plcdescricao 
    						END AS descricao, 
    					CASE 
    						WHEN apgentoid IS NULL 
    							THEN 
    								CASE  
    									WHEN apgplcoid IS NOT NULL 
    										THEN (SELECT plcconta FROM plano_contabil WHERE plcoid=apgplcoid ORDER BY plcconta) 
    									END 
    						ELSE plcconta 
    						END AS plcconta, 
    					CASE 
    						WHEN apgdt_cadastro < '2009-11-03' AND apgplcoid IS NULL 
    							THEN (SELECT tctdescricao FROM tipo_ctpagar WHERE tctoid = apgtctoid ) 
    						WHEN apgplcoid IS NULL 
    							THEN (SELECT tctdescricao FROM tipo_ctpagar WHERE tctoid = apgtctoid ) 
    						ELSE (SELECT 'CONTA- ' || plcconta || ' - ' || plcdescricao FROM plano_contabil WHERE plcoid = apgplcoid ) 
    						END AS tipo_contas,
    					bannome,
    					entno_parcela, 
    					SUM((((entivlr_unit)* entiqtde)+ COALESCE(entivlr_ipi, 0) - COALESCE(entidesconto, 0)) + COALESCE(entivl_icms_st, 0)) AS valor_item, 
    					(SELECT COUNT(DISTINCT entiplcoid) FROM entrada_item WHERE entientoid=apgentoid) AS qtd_contas, 
    					fordocto,
    					fortipo,
    					tecrazao,
                        apgapgsoid,
                        apcedt_envio
    				FROM 
    					apagar 
    				INNER JOIN fornecedores ON foroid = apgforoid 
                    INNER JOIN tectran ON tecoid = apgtecoid
                    LEFT JOIN apagar_controle_arquivo ON apceapgno_remessa = apgno_remessa 
    				LEFT JOIN entrada ON entoid = apgentoid 
    				LEFT JOIN entrada_item ON entoid = entientoid 
    				LEFT JOIN plano_contabil ON plcoid = entiplcoid 
    				LEFT JOIN banco ON bancodigo = forbanco 
            		LEFT JOIN apagar_status ON apgsoid = apgapgsoid 
    				WHERE 
    					(apgdt_exclusao IS NULL or apgno_remessa IS NOT NULL)";

        if ( isset($parametros->num_remessa) && !empty($parametros->num_remessa) ) {
            $sql .= " 
                    AND apgno_remessa = " . (int) $parametros->num_remessa . "";
        }else{

            if (isset($parametros->periodo_inicial_busca) && !empty($parametros->periodo_inicial_busca) &&
                isset($parametros->periodo_final_busca) && !empty($parametros->periodo_final_busca)) {
                
                $sql .= " 
                        AND $parametros->consultar_busca::DATE BETWEEN '" . $parametros->periodo_inicial_busca . "' AND '" . $parametros->periodo_final_busca . "'";
            }
        }

		if ( isset($parametros->tecoid) && !empty($parametros->tecoid) ) {
			$sql .= " 
					AND tecoid = " . $parametros->tecoid . "";
		}
		
		if ( isset($parametros->cmp_fornecedor) && !empty($parametros->cmp_fornecedor) ) {
			$sql .= " 
					AND apgforoid = " . $parametros->cmp_fornecedor . "";
		}

        if ( isset($parametros->banco) && !empty($parametros->banco) ) {        
            $sql .= " 
            		AND apcebancoid = " . $parametros->banco . " 
                    AND apgsbancoid = " . $parametros->banco . " ";
        }
		
		if ( isset($parametros->status) && !empty($parametros->status) ) {
			$sql .= " 
					AND apgsoid = " . $parametros->status . "";
		}
		else{
			$sql .= "
					AND apgsdt_exclusao IS NULL ";
		}

        $sql .= "
            GROUP BY 
                apgoid,
                apgentoid,
                apgdt_vencimento,
                apgdt_entrada,
                apgvl_desconto,
                apgvl_juros,
                apgvl_multa,
                apgvl_apagar,
                apgvl_ir,
                apgvl_pis,
                apgvl_iss,
                apgvl_inss,
                apgvl_cofins,
                apgvl_csll,
                apgvl_tarifa_bancaria,
                apgforma_recebimento,
                apgprevisao,
                apgtipo_docto,
        		apgno_remessa,
        		apgscodigo,
        		apgsdescricao,
        		bannome,
        		entnota,
                entno_parcela,
        		entserie,
                fordocto,
                fortipo,
                forfornecedor,
                plcconta,
        		plcdescricao,
                tecrazao,
                apcedt_envio
            ORDER BY  
        		apgoid, 
        		plcconta";
		
		//echo "<pre>";var_dump($sql);echo "</pre>";
		
		$rs = $this->executarQuery($sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
        return $retorno;
	}
	
    /**
     * Método para buscar códigos de erro  
	 * (apagar_ocorrencia).
     *
     * @param $apgoid
     * @return array
     */
    public function buscaOcorrencias($apgoid){

        $retorno = array();
		
		$sql = "SELECT
    				(apgocodigo||' - '||apgodescricao) AS codigo_erro,
    				apgocodigo
    			FROM
    				apagar_ocorrencia
    			WHERE
    				apgooid IN (
                        SELECT
                            DISTINCT apghapgooid
                        FROM
                            apagar_historico
                        INNER JOIN 
                            apagar ON apgoid = apghapgoid AND apghapgno_remessa = apgno_remessa
                        WHERE
                            apghapgoid = $apgoid
    				);";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new Exception("Erro ao buscar códigos de erro.");
		}
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}		
		
		return $retorno;
    }

    /** Abre a transação */
    public function begin(){
        pg_query($this->conn, 'BEGIN');
    }

    /** Finaliza um transação */
    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }

    /** Aborta uma transação */
    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
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
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

     /**
     * Aborta ações dentro de um bloco de ponto de salvamento
     * @param  $nome [alias para do savepoint]
     */
    public function rollbackPoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }

}
