<?php
/**
 * @author  Emanuel Pires Ferreira
 * @email   epferreira@brq.com
 * @since   28/05/2013
 */

/**
 * Fornece os dados necessarios para o módulo principal para
 * efetuar ações referentes aos boletos do pré cadastro
 */
class PrnBoletoSecoDAO {

    /**
     * Link de conexão com o banco
     * @property resource
     */
    public $conn;


    /**
     * Construtor
     * @param resource $conn - Link de conexão com o banco
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function recuperaDadosFatura($titoid, $tipo)
    {
    	if ($tipo == "titulos_oficiais"){
    		$select_aux = " titvl_titulo AS titvl_titulo, ";
    		$tabela = " titulo ";
	    	
    	}else{
    		$select_aux = " titvl_titulo_retencao AS titvl_titulo, ";
    		$tabela = " titulo_retencao ";
    	}
    	
        $str = "SELECT 
                       COALESCE(titvl_ir,0) AS titvl_ir, 
                       COALESCE(titvl_iss,0) AS titvl_iss, 
                       COALESCE(titvl_piscofins,0) AS titvl_piscofins, 
                       COALESCE(titvl_desconto,0) AS titvl_desconto,
                       $select_aux
                       TO_CHAR(titdt_referencia, 'DD/MM/YYYY') AS titdt_referencia,
                       TO_CHAR(titdt_vencimento, 'DD/MM/YYYY') AS titdt_vencimento,
                       clinome AS cliente,
                       (CASE
                           WHEN clitipo = 'F'
                           THEN (COALESCE(clirua_res,'')||' '||COALESCE(clino_res::text,'')||' '||COALESCE(clicompl_res,'')||' '||COALESCE(clibairro_res,''))
                           ELSE COALESCE(clirua_com,'')||' '||COALESCE(clino_com::text,'')||' '||COALESCE(clicompl_com,'')||' '||COALESCE(clibairro_com,'') END
                       ) AS endereco,        
                       (CASE WHEN clitipo = 'F' THEN clicidade_res ELSE clicidade_com END) AS cidade,           
                       (CASE WHEN clitipo = 'F' THEN cliuf_res ELSE cliuf_com END) AS uf,      
        			   (CASE WHEN clitipo = 'F' THEN
		 					(CASE WHEN clino_cep_res IS NOT NULL THEN clino_cep_res::text ELSE clino_cep_res::text END)
		 				ELSE
		 					(CASE WHEN clino_cep_com IS NOT NULL THEN clino_cep_com::text ELSE clino_cep_com::text END)
                       END) AS cep,
                       (CASE WHEN clitipo = 'F' THEN clino_cpf ELSE clino_cgc END) AS cpf_cnpj,
                       cliemail,
                       titoid,
                       TO_CHAR(titdt_inclusao, 'DD/MM/YYYY') AS titdt_inclusao,
                       titno_parcela,
        				titnumero_registro_banco AS nosso_numero,
        				TO_CHAR(titdt_vencimento, 'DD/MM/YYYY') AS titdt_vencimento
                  FROM $tabela
            INNER JOIN clientes 
                    ON titclioid=clioid 
                 WHERE titoid = ".$titoid;

        $sql = pg_query($this->conn, $str);

        $dadosFatura = array();

        while( $resul = pg_fetch_array($sql) ){

            // Variáveis de Composição do Boleto
            $dadosFatura['clinome'] = $resul['cliente'];
            $dadosFatura['outras_deducoes'] = $resul['titvl_ir'] + $resul['titvl_iss'] + $resul['titvl_piscofins'];
            $dadosFatura['valor_titulo_retencao'] = $resul['titvl_titulo_retencao'];
            $dadosFatura['valor_desconto'] = $resul['titvl_desconto'];

            $dadosFatura['valor'] = $resul['titvl_titulo'];

            $dadosFatura['cliemail'] = $resul['cliemail'];

            $dadosFatura['endereco'] = $resul['endereco'];
            $dadosFatura['cidade'] = $resul['cidade'];
            $dadosFatura['uf'] = $resul['uf'];
            $dadosFatura['cep'] = $resul['cep'];
            $dadosFatura['cpf'] = $resul['cpf_cnpj'];

            $dadosFatura['sacado'] = $resul['cliente'];
            $dadosFatura['end_sacado'] = $resul['endereco'];
            $dadosFatura['estado'] = $resul['uf'];
            $dadosFatura['cpf_cnpj'] = $dadosFatura['cpf'];

            $dadosFatura['titoid'] = $resul['titoid'];
            $dadosFatura['data_doc'] = $resul['titdt_inclusao'];
            $dadosFatura['data_ref'] = $resul['titdt_referencia'];
            $dadosFatura['titno_parcela'] = $resul['titno_parcela'];

            $dadosFatura['nosso_numero'] = $resul['nosso_numero'];

            $dadosFatura['titdt_vencimento'] = $resul['titdt_vencimento'];
            
        }

        return $dadosFatura;
    }

    public function recuperaDadosProposta($contrato)
    {
    	 
    	$str = "
    			SELECT 
    				ppagadesao_parcela 
    			FROM 
    				proposta_pagamento
				JOIN 
    				proposta ON prpoid = ppagprpoid
				WHERE 
    				prptermo = {$contrato}
    			";
    
    	$sql = pg_query($this->conn, $str);

    	$dadosProposta = array();

    	while( $resul = pg_fetch_array($sql) ){
    		$dadosProposta['parcelas'] = $resul['ppagadesao_parcela'];
    	}

    	return $dadosProposta;
    }

    public function dadosProposta($prpoid) {
    	// identificar layout $prpoid
    	$sql = "
	    	SELECT
	    		prptppoid, tppoid_supertipo, conno_tipo
	    	FROM
	    		proposta
	    	INNER JOIN contrato ON prptermo = connumero
	    	LEFT JOIN tipo_proposta ON tppoid = prptppoid
	    	WHERE
    			prpoid = $prpoid";
    	 
    	$ret = pg_query($this->conn, $sql);
    	 
    	if(pg_num_rows($ret) > 0) {
    		$dadosContratoLayout = pg_fetch_object($ret);
    	}
    	
    	return $dadosContratoLayout;
    }
    
    public function recuperaLayout($dados)
    {

    	$tipoProposta 	 = ($dados->tppoid_supertipo) ? $dados->tppoid_supertipo : $dados->prptppoid ;
    	$subTipoProposta = ($dados->tppoid_supertipo != '') ? $dados->prptppoid : '' ;
    	$tipoContrato = ($dados->conno_tipo != '') ? $dados->conno_tipo : '' ;

    	
        $sql = "SELECT lwkassunto_email, lwklayout, lconsrvoid
                  FROM layout_lconfiguracao
            INNER JOIN tipo_config_layout
                    ON tcloid = lcontcloid
            INNER JOIN layout_welcome_kit
                    ON lconflwkoid = lwkoid
                 WHERE tclcodigo = 2
                 AND lconfdt_exclusao IS NULL
        		AND lconftppoid = $tipoProposta";

        if($subTipoProposta == '')
          $sql .= " AND lconftppoid_sub IS NULL ";
        else
				  $sql .= " AND lconftppoid_sub = $subTipoProposta ";

				if($tipoContrato == '')
          $sql .= " AND lconftpcoid IS NULL ";
        else
          $sql .= " AND lconftpcoid = $tipoContrato ";
        
        $res = pg_query($this->conn, $sql);
        
        // Caso não tenha encontrado o layout, busca sem o tipo de contrato
        if(pg_num_rows($res) == 0) {
	        $sql = "SELECT lwkassunto_email, lwklayout, lconsrvoid
		        FROM layout_lconfiguracao
		        INNER JOIN tipo_config_layout
		        ON tcloid = lcontcloid
		        INNER JOIN layout_welcome_kit
		        ON lconflwkoid = lwkoid
		        WHERE tclcodigo = 2
		        AND lconfdt_exclusao IS NULL
		        AND lconftppoid = $tipoProposta";
	        
	        if($subTipoProposta == '')
	        	$sql .= " AND lconftppoid_sub IS NULL ";
	        else
	        	$sql .= " AND lconftppoid_sub = $subTipoProposta ";
	        
	        
         $res = pg_query($this->conn, $sql);
        }
         
         if(pg_num_rows($res) == 0) {

         	/*$sqlPadrao = "SELECT lwklayout, lconsrvoid
         	FROM layout_lconfiguracao
         	INNER JOIN tipo_config_layout
         	ON tcloid = lcontcloid
         	INNER JOIN layout_welcome_kit
         	ON lconflwkoid = lwkoid
         	WHERE tclcodigo = 2
         	AND lconfdt_exclusao IS NULL
         	AND lwkpadrao is true";   */

          $sqlPadrao = "SELECT lwkassunto_email, lwklayout, (SELECT srvoid
                                              FROM servidor_email
                                              WHERE srvpadrao is true
                                              AND srvtecoid = 1
                                              LIMIT 1) AS lconsrvoid
                        FROM layout_welcome_kit
                        WHERE lwkpadrao is true";


         	$res = pg_query($this->conn, $sqlPadrao);
         }

         $layout['html'] = pg_fetch_result($res,0,"lwklayout");
         $layout['server'] = pg_fetch_result($res,0,"lconsrvoid");
         $layout['assunto'] = pg_fetch_result($res,0,"lwkassunto_email");
         
         return $layout;
    }

    // Função para busca o layout referente ao parcelamento, enviado no processo Cron de envio de parcelas
    public function recuperaLayoutParcelamento($dados)
    {
    
    	$tipoProposta 	 = ($dados->tppoid_supertipo) ? $dados->tppoid_supertipo : $dados->prptppoid ;
    	$subTipoProposta = ($dados->tppoid_supertipo != '') ? $dados->prptppoid : '' ;
    	$tipoContrato = ($dados->conno_tipo != '') ? $dados->conno_tipo : '' ;
    
    	$tipoParcelado = ($dados->tipoLayout != '') ? $dados->tipoLayout : '' ;
    	 
    	$sql = "
    		SELECT lwkassunto_email, lwklayout, lconsrvoid
    		FROM layout_lconfiguracao
    		INNER JOIN tipo_config_layout
    		ON tcloid = lcontcloid
    		INNER JOIN layout_welcome_kit
    		ON lconflwkoid = lwkoid
    		WHERE tclcodigo = 2
    		AND lconfdt_exclusao IS NULL
    		AND lconftppoid = $tipoProposta
    		AND lwkdescricao ilike '%PARCELADO%'";
    		 
    	if($subTipoProposta == '')
    		$sql .= " AND lconftppoid_sub IS NULL ";
    	else
    		$sql .= " AND lconftppoid_sub = $subTipoProposta ";
    		
    	if($tipoContrato == '')
    		$sql .= " AND lconftpcoid IS NULL ";
    	else
    		$sql .= " AND lconftpcoid = $tipoContrato ";
    		 
    	$res = pg_query($this->conn, $sql);
    	
    	
    	// Caso não tenha encontrado o layout, busca sem o tipo de contrato
    	if(pg_num_rows($res) == 0) {
    		$sql = "
	    		SELECT lwkassunto_email, lwklayout, lconsrvoid
	    		FROM layout_lconfiguracao
	    		INNER JOIN tipo_config_layout
	    		ON tcloid = lcontcloid
	    		INNER JOIN layout_welcome_kit
	    		ON lconflwkoid = lwkoid
	    		WHERE tclcodigo = 2
	    		AND lconfdt_exclusao IS NULL
	    		AND lconftppoid = $tipoProposta
	    		AND lwkdescricao ilike '%PARCELADO%'";
    		 
    		if($subTipoProposta == '')
    			$sql .= " AND lconftppoid_sub IS NULL ";
    		else
    			$sql .= " AND lconftppoid_sub = $subTipoProposta ";
    		 
    		 
    		$res = pg_query($this->conn, $sql);
    	}
    	
    	// Se encontrou algum layout
    	if(pg_num_rows($res) > 0) {
         $layout['html'] = pg_fetch_result($res,0,"lwklayout");
         $layout['server'] = pg_fetch_result($res,0,"lconsrvoid");
         $layout['assunto'] = pg_fetch_result($res,0,"lwkassunto_email");
    	}
    
         return $layout;
    }
    
    
    public function verificaStatusBoleto($contrato) {
    	
    	$sqlControleEnvio = "
	    	SELECT 
	    		* 
	    	FROM 
	    		titulo_controle_envio
	    	WHERE 
	    		tceconoid = '{$contrato}'
	    	AND tcetipo = 'boleto_seco'
	    	ORDER BY tceoid DESC
	    	LIMIT 1;
	    	";
    	
    	$res = pg_query($this->conn, $sqlControleEnvio);
    	if (pg_num_rows($res) > 0) {
    		$controleTitulo['titulo'] = pg_fetch_result($res,0,"tcetitoid");
    		$controleTitulo['contrato'] = pg_fetch_result($res,0,"tceconoid");
    		$controleTitulo['tipo'] = pg_fetch_result($res,0,"tcetipo");
    		$controleTitulo['status'] = pg_fetch_result($res,0,"tcestatus_envio");
    	}
    	
    	return $controleTitulo;
    }
    
    public function boletoEnviado($tituloTaxaInstalacao) {
    	 
    	$sqlControleEnvio = "
	    	UPDATE 
    			titulo_controle_envio
    		SET 
    			tcestatus_envio = 'true',
    			tcedata_envio = NOW()
    		WHERE
    			tcetitoid = '{$tituloTaxaInstalacao}';
    	";
    	
    	if (pg_query($this->conn, $sqlControleEnvio))
    	{
    		return true;
    	}
    }
    
    public function parcelasEnviadas($tituloParcelas) {   	
    	
    	$sqlControleEnvio = "
    		UPDATE
    			titulo_controle_envio
	    	SET
	    		tcestatus_envio = 'true',
	    		tcedata_envio = NOW()
	    	WHERE
	    		tcetitoid in ({$tituloParcelas})
	    	AND tcetipo = 'titulos_oficiais';
    	";

    	if (pg_query($this->conn, $sqlControleEnvio))
    	{
    		return true;
    	}
    }
    
    public function verificaIsBoleto($tituloTaxaInstalacao) {
    
    	$sqlControleEnvio = "
	    	SELECT 
	    		* 
	    	FROM 
	    		titulo_controle_envio
	    	WHERE 
	    		tcetitoid = '{$tituloTaxaInstalacao}'
	    	ORDER BY tceoid DESC
	    	LIMIT 1;
	    	";
    	
    	$res = pg_query($this->conn, $sqlControleEnvio);
    	if (pg_num_rows($res) > 0) {
    		$controleTitulo['titulo'] = pg_fetch_result($res,0,"tcetitoid");
    		$controleTitulo['contrato'] = pg_fetch_result($res,0,"tceconoid");
    		$controleTitulo['tipo'] = pg_fetch_result($res,0,"tcetipo");
    		$controleTitulo['status'] = pg_fetch_result($res,0,"tcestatus_envio");

    		if ($controleTitulo['status'] == "f" && $controleTitulo['tipo'] == "boleto_seco") {
    			return true;
    		}
    	}
    	
    	return false;
    }
    
    public function verificaIsParcela($tituloParcela) {
    
    	$sqlControleEnvio = "
	    	SELECT
	    		*
	    	FROM
	    		titulo_controle_envio
	    	WHERE
	    		tcetitoid = '{$tituloParcela}'
	    	ORDER BY tceoid DESC
	    	LIMIT 1;
    	";
    	 
    	$res = pg_query($this->conn, $sqlControleEnvio);
    	if (pg_num_rows($res) > 0) {
    		$controleTitulo['titulo'] = pg_fetch_result($res,0,"tcetitoid");
    		$controleTitulo['contrato'] = pg_fetch_result($res,0,"tceconoid");
    		$controleTitulo['tipo'] = pg_fetch_result($res,0,"tcetipo");
    		$controleTitulo['status'] = pg_fetch_result($res,0,"tcestatus_envio");

    		if ($controleTitulo['status'] == "f" && $controleTitulo['tipo'] == "titulos_oficiais") {
    			return true;
    		}
    	}
    		
    	return false;
    }
    
}