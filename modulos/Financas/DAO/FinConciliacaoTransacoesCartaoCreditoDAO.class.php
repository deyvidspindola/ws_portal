<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	18/12/2012 
 * @package Finanças
 */

/**
 * Fornece os dados necessarios para o módulo do módulo financeiro para 
 * efetuar pagamentos de títulos com forma de cobrança 'cartão de crédito' 
 * @author Emanuel Pires Ferreira
 */

class FinConciliacaoTransacoesCartaoCreditoDAO {
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $conn;
	private $cd_usuario;
	
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn)
	{
		$this->conn = $conn;
		$this->cd_usuario = 2750;//$_SESSION['usuario']['oid'];
	}
    
    /**
     * Recupera dados do histórico de pagamento pelo título de crédito para encontrar o título à receber que foi gerado
     * no momento do pagamento, se for parcelado, filtro o titulo pelo número da parcela que será baixada
     * 
     */
    public function buscaDadosHistorico($dados_arq){
        
        
        $dadosTitulo = Array();
        
        try {
            
            if(!empty($dados_arq->tituloCredito) && !empty($dados_arq->num_comprovante) && !empty($dados_arq->cod_autorizacao)){

                //busca o título e o cod do histórico pesquisando pelo título de crédito recuperado do arquivo CSV e pelo nsu
                $sql = "SELECT 
                            cli.clinome, 
                            t.titoid, 
                            c.ccchoid, 
                            t.titdt_vencimento, 
                            f.forcnome, 
                            t.titdt_pagamento,
                        CASE 
                            WHEN 
                                titdt_pagamento IS NOT NULL 
                            THEN 
                                'PAGO' 
                            WHEN 
                                titdt_vencimento < NOW() 
                            THEN 
                                'VENCIDO'
                            ELSE 
                                'A VENCER' 
                            END 
                        AS status
                        FROM 
                            cliente_cobranca_credito_historico as c
                        INNER JOIN 
                            titulo as t ON c.ccchtitoid = t.titoid
                        INNER JOIN 
                            forma_cobranca f ON t.titformacobranca = f.forcoid
                        INNER JOIN 
                            clientes cli ON c.ccchclioid = cli.clioid
                        WHERE 
                            t.titoid::INT = ".(int)$dados_arq->tituloCredito."
                        AND 
                            c.ccchnsu_autorizadora::INT = ".(int)$dados_arq->num_comprovante."
                        AND
                            c.ccchnumero_autorizacao = '".$dados_arq->cod_autorizacao."' ";

                if(!$result = pg_query($this->conn, $sql)){
                    throw new Exception('Falha ao pesquisar dados de histórico do título.');
                }
            
                 
                if(pg_num_rows($result) == 1){

                    $rsHistorico = pg_fetch_array($result);
                    $dadosTitulo['titoid']           = $rsHistorico['titoid'];
                    $dadosTitulo['ccchoid']          = $rsHistorico['ccchoid'];
                    $dadosTitulo['clinome']          = $rsHistorico['clinome'];
                    $dadosTitulo['status']           = $rsHistorico['status'];
                    $dadosTitulo['titdt_vencimento'] = $rsHistorico['titdt_vencimento'];
                    $dadosTitulo['titdt_pagamento']  = $rsHistorico['titdt_pagamento'];
                    $dadosTitulo['forcnome']         = $rsHistorico['forcnome'];
                    
                    //faz a busca pelo título herança(crédito) e pelo número da parcela
                    //para recuperar o título que será baixado
                    $sqlHeranca = " SELECT titoid
                                      FROM titulo
                                     WHERE tittitoid_heranca = ".$dadosTitulo['titoid']." ";

                    //faz apenas pesquisa por parcela quando o pagamento for parcelado
                    if($dados_arq->totalParcelas > 0){
                      $sqlHeranca .= " AND titno_parcela = '$dados_arq->num_parcela' ";
                    }

                      $sqlHeranca .= " AND titdt_cancelamento IS NULL 
                                       AND titdt_pagamento IS NULL
                                       AND titdt_credito IS NULL ";

                    if(!$res = pg_query($this->conn, $sqlHeranca)){
                        throw new Exception('Falha ao pesquisar dados do título de crédito.');
                    }
                    
                    if(pg_num_rows($res) == 1){
                        
                        $titulo = pg_fetch_array($res);
                        $dadosTitulo['titoid'] = $titulo['titoid'];
                        // inserido para mandar protheus 
                        $dadosTitulo['titoidFilho'] = $titulo['titoid'];
                        
                        return $dadosTitulo;
                    }
                    
                    return false;
                }
            }
            
            return false;
            
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
	
    
    /**
     * Verifica se os dados informados no layout 2 existe na base para buscar o título
     * 
     * @param unknown $dados_arq
     * @throws Exception
     * @return unknown|boolean
     */
    public function getDadosEstornoTitulo($dados_arq){
    	
        if(empty($dados_arq->valor_titulo)){
    		throw new Exception('O valor do título deve ser informado para buscar o título.');
    	}
    	
    	if(empty($dados_arq->num_parcial_cartao)){
    		throw new Exception('O número parcial do cartão deve ser informado para buscar o título.');
    	}
    	
    	if(empty($dados_arq->data_venda)){
    		throw new Exception('A data da venda deve ser informada para buscar o título.');
    	}
    	
    	$sql_titulo_pai = " SELECT DISTINCT arcctitoid
						      FROM arquivo_retorno_conciliacao_credito
						      WHERE arcclayout = '10'
						        AND arccnumero_cartao = '$dados_arq->num_parcial_cartao'
						    	AND arccdt_venda = '$dados_arq->data_venda' ";
    	
    	if(!$res_titulo_pai = pg_query($this->conn, $sql_titulo_pai)){
    		throw new Exception('Falha ao pesquisar título pai.');
    	}
    	 
    	//se encontrou o título pai prossegue com outras verificações
    	if (pg_num_rows($res_titulo_pai) > 0) {
    		
    		$titulo_pai = pg_fetch_all($res_titulo_pai);
    		//percorre os títulos pais econtrados (pode acontecer de no arquivo vir com dois 
    		//cancelamentos para o mesmo cartão com valores diferentes)
    		foreach ($titulo_pai as $titulo){
    			
	    		//verifica se bate o valor total e data da venda do título pai
	    		$sql_compara_valor = "  SELECT titoid, titvl_titulo
							    		  FROM titulo
							    		 WHERE titoid = ".$titulo['arcctitoid']."
							    		   AND titvl_titulo = $dados_arq->valor_titulo
							    		   AND titdt_inclusao::DATE = '$dados_arq->data_venda' 
	    								   AND titdt_pagamento IS NOT NULL
	    								   AND titdt_credito IS NOT NULL ";
	    		
	    		if(!$res_compara_valor = pg_query($this->conn, $sql_compara_valor)){
	    			throw new Exception('Falha ao comparar valor e data do título.');
	    		}
	    		
	    		//retornar os dados do título pai e dos títulos filhos encontrados
	    		if (pg_num_rows($res_compara_valor) > 0) {
	    		
	    			$sql_titulos = "SELECT titoid, 
									       arccoid,
									       clinome,
									       arccnumero_cartao,
									       arcctitoid,
									       titvl_titulo,
									       titno_parcela,
									       arccdt_venda,
									       arcccod_autorizacao
									  FROM titulo 
								INNER JOIN arquivo_retorno_conciliacao_credito ON tittitoid_heranca = arcctitoid AND arccnum_parcela = titno_parcela
								INNER JOIN clientes ON titclioid = clioid
								     WHERE arcclayout = '10'
								       AND tittitoid_heranca = ".$titulo['arcctitoid']."
								                               
								     UNION ALL
								
								    SELECT titoid,
								           NULL,
									       clinome,
									       '',
									       NULL,
									       titvl_titulo,
									       titno_parcela,
									       titdt_inclusao::DATE,
									       NULL
									  FROM titulo 
								INNER JOIN clientes ON titclioid = clioid
								     WHERE titoid = ".$titulo['arcctitoid']."
							      ORDER BY titoid ASC  ";
	    			
	    			
	    			if(!$res_titulos = pg_query($this->conn, $sql_titulos)){
	    				throw new Exception('Falha ao retornar dados dos títulos.');
	    			}
	    			
	    			//retornar os dados dos títulos 
	    			if (pg_num_rows($res_titulos) > 0) {
	    				
	    				$dados_layouts['dados']['dados_layout_10'] = pg_fetch_all($res_titulos);
	    				
	    				$sql_layout_2 = "SELECT arccoid,
							    				arcclayout,
							    				arccdt_ajuste,
							    				arccnumero_cartao,
							    				arcccod_autorizacao,
							    				arccnum_parcela,
							    				arcctotal_parcelas,
							    				arccvlr_bruto,
							    				arccdt_venda,
							    				arccvlr_liquido,
							    				arccpercentual_taxa_servico,
							    				arccvlr_comissao,
							    				arcccod_motivo_ajuste,
							    				arcccdesc_motivo_ajuste
							    		   FROM arquivo_retorno_conciliacao_credito
							    		  WHERE arcclayout = '2'
							    			AND arccnumero_cartao = '$dados_arq->num_parcial_cartao'
							    			AND arccdt_venda = '$dados_arq->data_venda' 
	    				                    AND arccvlr_bruto = $dados_arq->valor_titulo ";
	    				
	    				if(!$result_2 = pg_query($this->conn, $sql_layout_2)){
	    					throw new Exception('Falha ao pesquisar ajuste para estorno do título.');
	    				}
	    				
	    				if (pg_num_rows($result_2) > 0) {
	    					$dados_layouts['dados']['dados_layout_2']= pg_fetch_all($result_2);
	    				}
	    			}
	    		
	    		}//fim foreach títulos pais
    			
    			return $dados_layouts;
    		}
    	}
    	
  		return false;
    }
    
    
   /**
    * Estorna o título e grava log na tabela acionamento
    * 
    * @param unknown $dadosEstorno
    * @param unknown $titulo
    * @throws Exception
    * @return boolean
    */
    public function setEstornarTitulo($dadosEstorno, $titulo){
    	
    	$observacao = $dadosEstorno->cod_motivo.' - '.$dadosEstorno->desc_motivo;
    	
    	$sql_titulo = "  UPDATE titulo 
							SET titusuoid_alteracao    = $this->cd_usuario,
							    titvl_pagamento        = 0, 
							    titvl_desconto         = 0, 
							    titvl_juros            = 0, 
							    titvl_multa            = 0, 
							    titvl_tarifa_banco     = 0,
							    titcfbbanco            = NULL,
							    tittaxa_administrativa = NULL, 
							    titdt_pagamento        = NULL, 
							    titdt_credito          = NULL, 
							    titobs_recebimento     = NULL, 
							    titobs_estorno         = '$observacao' 
						  WHERE titoid = ".$titulo." ; ";
    	
    	if(!pg_query($this->conn, $sql_titulo)){
    		throw new Exception('Falha ao estornar título.');
    	}
    	
    	$sql_acionamento = " INSERT INTO titulo_acionamento (
    	                                           tiatitoid, 
    	                                           tiaacionamento, 
    	                                           tiausuoid ) 
    	                                   VALUES (
    	                                           ".$titulo.",
    	                                          '$observacao',
    	                                           27850 )";
    	
    	if(!pg_query($this->conn, $sql_acionamento)){
    		throw new Exception('Falha ao inserir título acionamento.');
    	}
    	
    	return true;
    }
    
    /**
     * Após realizar todos os procedimentos de pagamento e validar,
     * baixar o título ao qual o pagamento se refere
     * 
     * @param unknown $dataTitCredito
     * @param integer $titoid  - id do Título
     * @param integer $ccchoid - id da tabela cliente_cobranca_credito_historico
     * @param float $valor_titulo
     * @param float $valor_pago
     * @param float $taxa_adm
     * @param integer $cd_usuario - Usuário que executou ação.
     * @param string $status
     * @param integer $banco - id do banco tabela config_banco PK: forcoid
     * 
     * @throws Exception
     * @return boolean
     */
    public function confirmaPagamento($dataTitCredito, $titoid, $ccchoid, $valor_titulo, $valor_pago, $taxa_adm, $cd_usuario, $status, $banco){
    		
            $sql = "UPDATE titulo
                       SET  tittransacao_cartao = TRUE
                           ,titusuoid_alteracao = ".$this->cd_usuario."                           
                           ,titccchoid = $ccchoid
						   ,tittaxa_administrativa = ".$taxa_adm."
                           ,titvl_titulo = ".$valor_titulo." 
                           ,titvl_pagamento = ".$valor_pago." 
                    	   ,titdt_pagamento = '$dataTitCredito'
				           ,titdt_credito = '$dataTitCredito'
				           ,titobs_recebimento = 'Crédito do Cartão'
            			   ,titcfbbanco = '$banco' "; //Problema: 1582
                                      
            if($status === 'CANCELADO'){
            	
            	$sql .=" ,titdt_cancelamento = NOW()
            			 ,titobs_cancelamento = 'Cancelado pela Operadora de Cartões' ";
            }
            
            $sql .=" WHERE titoid = $titoid ";

            
           if(!$result = pg_query($this->conn, $sql)){
           	   throw new Exception('Falha ao baixar título.');
           }
          
           return true;
    }
    
    /**
     * Verifica se o dados da baixa já foram gravados
     * @param unknown $dados_arq
     * @throws Exception
     */
    public function getDadosGravados($dados_arq){
    	
    	$sql = " SELECT arccoid 
				   FROM arquivo_retorno_conciliacao_credito
				  WHERE arcclayout = '$dados_arq->tipo_registro'
				    AND arccnumero_cartao = '$dados_arq->num_parcial_cartao'
    			";
    	
    	if((int)$dados_arq->tipo_registro == 10){
    		//$where .= " AND arccnsu_sitef   = ".$dados_arq->nsusitef;
    		$sql .= " AND arcctitoid      = ".$dados_arq->tituloCredito;
    		$sql .= " AND arccnum_parcela = ".$dados_arq->num_parcela;
    	}
    	
    	if($dados_arq->tipo_registro == 2){
    		$sql .= " AND arccdt_ajuste = '".$dados_arq->data_ajuste."' ";
    		$sql .= " AND arccvlr_bruto = '".$dados_arq->valor_titulo."' ";
    	}
   
       
    	if(!$res = pg_query($this->conn, $sql)){
    		throw new Exception('Falha ao pesquisar dados gravados.');
    	}
    	
    	if (pg_num_rows($res) > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    
    /***
     * Grava os dados lidos dos layouts do arquivo de retorno detalhado
     * 
     * @throws Exception
     * @return boolean
     */
    public function setDadosArquivoConciliacao($dados_arq){
    	
    		$sql = " INSERT INTO arquivo_retorno_conciliacao_credito(
				            arcclayout, 
				            arcctitoid, 
				            arccdt_venda, 
				            arccnumero_resumo, 
				            arccnumero_comprovante, 
				            arccnsu_sitef, 
				            arccnumero_cartao, 
				            arccvlr_bruto, 
				            arcctotal_parcelas, 
				            arccvlr_liquido, 
				            arccdt_credito, 
				            arccnum_parcela, 
				            arcccod_banco, 
				            arcccod_agencia, 
				            arccconta_corrente, 
				            arccvlr_comissao, 
				            arccpercentual_taxa_servico, 
				            arcccod_autorizacao, 
				            arccnum_cupom_fiscal, 
				            arcccod_bandeira,
				            arccdt_ajuste, 
				            arcccod_motivo_ajuste, 
				            arcccdesc_motivo_ajuste,
    				        arccusuoid )
				            
				    VALUES ( ";
    		                 
		               $sql .= (!empty($dados_arq->tipo_registro)) ? " '$dados_arq->tipo_registro' ": " NULL " ;
		
		               $sql .= (!empty($dados_arq->tituloCredito)) ? ", $dados_arq->tituloCredito ": ", NULL " ;
		
		               $sql .= ($this->validarData($dados_arq->data_venda) == true) ? ", '$dados_arq->data_venda' ": ", NULL " ;
  		
		               $sql .= (!empty($dados_arq->num_resumo)) ? ", '$dados_arq->num_resumo' ": ", NULL " ;
		    		
			    	       $sql .= (!empty($dados_arq->num_comprovante)) ? ", '$dados_arq->num_comprovante' ": ", NULL " ;
			    		
			    	       $sql .= (!empty($dados_arq->nsusitef)) ? ", $dados_arq->nsusitef ": ", NULL " ;
			    		
			    	       $sql .= (!empty($dados_arq->num_parcial_cartao)) ? ", '$dados_arq->num_parcial_cartao' ": ", NULL " ;
			    		
			    	       $sql .= (!empty($dados_arq->valor_titulo)) ? ", $dados_arq->valor_titulo ": ", NULL " ;
			    		
			    	       $sql .= ($dados_arq->totalParcelas != '') ? ",".(int) $dados_arq->totalParcelas." ": ", NULL " ;
			    					    	
			    	       $sql .= (!empty($dados_arq->valor_pago)) ? ", $dados_arq->valor_pago ": ", NULL " ;
			    	       			    		
			    	       $sql .= ($this->validarData($dados_arq->titDtPagamento) == true) ? ", '$dados_arq->titDtPagamento' ": ", NULL " ;
			    		
			    	       $sql .= ($dados_arq->num_parcela != '') ? ", ".(int)$dados_arq->num_parcela." ": ", NULL " ;
			    		
			    	       $sql .= (!empty($dados_arq->bancoPagamento)) ? ", $dados_arq->bancoPagamento ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->cod_agencia)) ? ", $dados_arq->cod_agencia ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->numero_conta_corrente)) ? ", $dados_arq->numero_conta_corrente ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->vlr_comissao)) ? ", $dados_arq->vlr_comissao ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->vlr_taxa_servico)) ? ", $dados_arq->vlr_taxa_servico ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->cod_autorizacao)) ? ", '$dados_arq->cod_autorizacao' ": ", NULL " ;
			    	       
		    	         $sql .= (!empty($dados_arq->cupom_fiscal)) ? ", $dados_arq->cupom_fiscal ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->cod_bandeira)) ? ", $dados_arq->cod_bandeira ": ", NULL " ;
			    	       
			    	       $sql .= ($this->validarData($dados_arq->data_ajuste) == true) ? ", '$dados_arq->data_ajuste' ": ", NULL " ;
			    	      
			    	       $sql .= (!empty($dados_arq->cod_motivo_ajuste)) ? ", $dados_arq->cod_motivo_ajuste ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($dados_arq->desc_motivo_ajuste)) ? ", '$dados_arq->desc_motivo_ajuste' ": ", NULL " ;
			    	       
			    	       $sql .= (!empty($this->cd_usuario)) ? ", $this->cd_usuario ": ", NULL " ;
			    	       
				     $sql .="    ); ";
				
  
    		if(!pg_query($this->conn, $sql)){
    			throw new Exception('Falha ao inserir dados do arquivo de retorno detalhado crédito.');
    		}
    		
    		return true;
    }
	
	/**
	 * Valida se a data está em um formato válido
	 * 
	 * @param string $data        	
	 */
	public function validarData($data) {
		
		// a data deve estar no formado ANOMÊSDIA, ex: 20151230 (8 dígitos)
		if (strlen ( $data ) != 8) {
			return false;
		}
		
		$dia = substr ( $data, 6, 2 );
		$mes = substr ( $data, 4, 2 );
		$ano = substr ( $data, 0, 4 );
		
		// verifica se a data é realmente uma data válida
		if (! checkdate ( $mes, $dia, $ano )) {
			return false;
		}
		
		return true;
	}
    
    public function getSituacaoJobSoftexpressCartaoDeCredito(){
    	
    	$sql = " select pcsi.pcsidescricao
                 from parametros_configuracoes_sistemas pcs
                 inner join parametros_configuracoes_sistemas_itens pcsi on pcsi.pcsipcsoid = pcs.pcsoid
                 where pcsi.pcsipcsoid = 'SOFTEXPRESS_CARTAODECREDITO'
                 and   pcsi.pcsioid = 'PROCESSAMENTO_CARTAODECREDITO_AUTOMATICO' ";
        
    	if(!$res = pg_query($this->conn, $sql)){
    		throw new Exception('Falha ao pesquisar dados de PROCESSAMENTO_CARTAODECREDITO_AUTOMATICO.');
    	}
    	if (pg_num_rows($res) > 0) {
            $row=pg_fetch_object($res);
            return $row->pcsidescricao;
    	}else{
            return true;
        }
    	
    }
	
	public function getDadosGrupoEmail(){


        $sql = "SELECT pcsidescricao, pcsioid, pcsdescricao
  				FROM
					parametros_configuracoes_sistemas,
					parametros_configuracoes_sistemas_itens
 				WHERE
					pcsoid = pcsipcsoid
			    AND pcsdt_exclusao is null
				AND pcsidt_exclusao is null
				AND pcsipcsoid = 'EMAIL_CONTASARECEBER'
				AND pcsioid = 'ENVIO_EMAIL_CONTASARECEBER'";

        if (!$result = pg_query($this->conn, $sql)) {
            throw new Exception ("Falha ao recuperar email do usuario dio processo ");
        }

        if(count($result) > 0){
            return pg_fetch_all($result);
        }

        return false;
    }

    public function getEmailTeste(){

        $sql = "SELECT pcsidescricao, pcsioid
  				FROM
					parametros_configuracoes_sistemas,
					parametros_configuracoes_sistemas_itens
 				WHERE
					pcsoid = pcsipcsoid
			    AND pcsdt_exclusao is null
				AND pcsidt_exclusao is null
				AND pcsipcsoid = 'PARAMETROSAMBIENTETESTE'
				AND pcsioid = 'EMAIL' ";

        if (!$result = pg_query($this->conn, $sql)) {
            throw new Exception ("Falha ao recuperar email de teste ");
        }

        if(count($result) > 0){
            return pg_fetch_object($result);
        }

        return false;

    }	
	
    
    /**
     * inicia transação com o BD
     */
    public function begin()	{
    	$rs = pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * confirma alterações no BD
     */
    public function commit(){
    	$rs = pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * desfaz alterações no BD
     */
    public function rollback(){
    	$rs = pg_query($this->conn, "ROLLBACK;");
    }
    

}
