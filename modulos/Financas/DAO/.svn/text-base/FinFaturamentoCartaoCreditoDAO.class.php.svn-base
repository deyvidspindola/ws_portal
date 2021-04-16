<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	10/12/2012 
 */

/**
 * Fornece os dados necessarios para o módulo do módulo financeiro para 
 * efetuar pagamentos de títulos com forma de cobrança 'cartão de crédito' 
 * @author Emanuel Pires Ferreira
 */
class FinFaturamentoCartaoCreditoDAO {
	
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
    
    /**
     * Função que retorna o próximo dia útil, de acordo com 
     * a função dia_util do banco de dados
     * 
     * @return date
     */
    public function retornaProximoDiaUtil()
    {
        //contador que aumenta um dia caso data anterior não seja útil
        $auxDia = 0;
        
        //flag de validação caso o dia informado seja útil
        $isUtil = false;
        
        try {
            //executa a rotina enquanto o dia informado não for dia útil
            while(!$isUtil) {
                //soma X dias da data atual conforme incremento da variável $auxDia
                $dia = date('d/m/Y', strtotime("+$auxDia days"));
                
                //consulta se dia é útil
                $sql = "SELECT dia_util('".$dia."') as retorno";

                $result = pg_query($this->conn, $sql);
                
                $rsUtil = pg_fetch_array($result);
                
                //caso retorno seja false (dia não é útil)
                if($rsUtil['retorno'] == 'false') {
                    //aumenta um dia na data para continuar a verificação
                    $auxDia++;
                } else {
                    //seta $isUtil como true para finalizar execução
                    $isUtil = true;
                    
                    //retorna o próximo dia útil
                    return $dia;
                }
            }
        } catch(Exception $e) {
            die('Erro '.$e->getMessage());
        }
    }
	
    /**
     * Função que retorna todas as formas de 
     * cobrança que são cartão de crédito
     * 
     * @return Array
     */
	public function retornaFormasCobrancaCartaoCredito() 
    {
        try {
            $sql = "SELECT forcoid 
                      FROM forma_cobranca
                      JOIN autorizacao_cartao_credito
                        ON forcaccoid = accoid 
                     WHERE forcexclusao IS NULL 
                       AND forccobranca_cartao_credito = TRUE 
                       AND accstatus = TRUE";
 
            $result = pg_query($this->conn, $sql);
            
            //preenche o Array de retorno
            while($rsForCob = pg_fetch_array($result)) {
                $formasCobranca[] = $rsForCob['forcoid'];
            }
            
            //retorna array das formas de cobrança que são 'cartão de crédito'
            return $formasCobranca;
            
        } catch(Exception $e) {
            die('Erro '.$e->getMessage());
        }    

    }
    
    /**
     * Função que retorna todos os títulos abertos de 
     * acordo com a forma de cobrança e vencimento exceto os títulos cancelados
     * 
     * @param Array $formasCobranca - Formas de cobrança que são cartão de crédito
     * @param int $tentativasPagamento  - Quantidade de tentativas efetuadas nas transações de pagamento
     * 
     * @return Array or boolean
     */
    public function buscaTitulosAbertos($formasCobranca, $tentativasPagamento)
    {
        try {
            $sql = "SELECT titoid, titclioid, titvl_titulo,
		            	   ( titvl_titulo 
							+ titvl_multa 
							+ titvl_juros 
							- titvl_desconto 
							- (CASE WHEN titvl_ir IS NULL THEN 0.00 ELSE titvl_ir END) 
							- (CASE WHEN titvl_iss IS NULL THEN 0.00 ELSE titvl_iss END) 
							- (CASE WHEN titvl_piscofins IS NULL THEN 0.00 ELSE titvl_piscofins END)) as valor_corrigido
                      FROM titulo 
                     WHERE titdt_pagamento IS NULL 
                       AND titformacobranca IN (".implode(",", $formasCobranca).")
                       AND titdt_vencimento <= NOW()
					   AND titdt_cancelamento IS NULL 
					   AND titobs_cancelamento IS NULL 
        			   AND ( SELECT count(ctcoid) 
			           		   FROM controle_transacao_cartao 
					   		  WHERE ctctitoid = titoid ) < ".$tentativasPagamento." ";
            
            $result = pg_query($this->conn, $sql);
            
            //se houver títulos, preenche array para retorno
            if(pg_num_rows($result) > 0){
                $i=0;
                while($rsTits = pg_fetch_array($result)) {
                    $titulos[$i]['titoid'] = $rsTits['titoid'];
                    $titulos[$i]['clioid'] = $rsTits['titclioid'];
                    $titulos[$i]['valort'] = $rsTits['valor_corrigido'];
                    $i++;
                }
                
                //retorna array de títulos abertos
                return $titulos;
            }
            
            return false;
        
        } catch(Exception $e) {
            die('Erro '.$e->getMessage());
        }

    }
    
    /**
     * Recupera a quantidade de transações efetuadas na data corrente:
     * -> Não enviadas
     * -> Pendentes de pagamento
     * -> Recebidas
     *  
     * @return array
     */
    public function retornarTransacoes(){
    	
    	try {
    		
    		$sql =" SELECT COUNT(ctcoid) AS quant
					  FROM clientes
					 INNER JOIN controle_transacao_cartao ON clioid = ctcclioid
					 INNER JOIN titulo ON titoid = ctctitoid
					 INNER JOIN forma_cobranca ON titformacobranca = forcoid
					 WHERE titformacobranca IN (24,25)
					   AND titdt_cancelamento IS NULL 
					   AND titobs_cancelamento IS NULL
					   AND ctcoid = (SELECT MAX(ctc1.ctcoid) AS ctcoid 
    				                 FROM controle_transacao_cartao ctc1 
    				                 WHERE ctc1.ctctitoid = titoid)
    				   AND ctcdt_inclusao::DATE = NOW()::DATE ";
    		
 			//Não enviadas	   		
    		$sqlNaoEnviadas   = $sql." AND (ctcccchoid = 0 OR ctcccchoid IS NULL 
									   AND ctcstatus IS NULL) ";
    		
    		$resNaoEnviadas   = pg_query($this->conn, $sqlNaoEnviadas);
    		$result['naoEnviadas']= pg_fetch_result($resNaoEnviadas, 0, "quant");
    		
    		
    		//Pendentes de pagamento
    		$sqlPendPagamento = $sql." AND titdt_pagamento IS NULL 
								       AND titdt_credito IS NULL 
								       AND ctcstatus <> 'CON' ";
    		
    		$resPendPagamento = pg_query($this->conn, $sqlPendPagamento);
    		$result['pendentePagamento']= pg_fetch_result($resPendPagamento, 0, "quant");
    		
    		
    		//Recebidas
    		$sqlRecebidas     = $sql." AND titdt_pagamento IS NOT NULL 
									   AND titdt_credito IS NOT NULL 
									   AND ctcstatus IN ('CON') 
									   AND ctcccchoid IS NOT NULL ";
    		
    		$resRecebidas     = pg_query($this->conn, $sqlRecebidas);
    		$result['recebidas']= pg_fetch_result($resRecebidas, 0, "quant");
    	    	
    	    return $result;
    	
    	} catch(Exception $e) {
    		die('Erro '.$e->getMessage());
    	}
    	
    }
    
	
    /**
     * Para toda transação de inclusão ou remoção de cartão de crédito, 
     * inserir um registro na tabela controle_transacao_cartao, 
     * onde o campo ctctipotransacao (tipo da transação realizada)  será igual a "C";
     * 
     * @param integer $clioid          - Id do Cliente
     * @param integer $titoid          - Id do Título
     * @param boolean $statusTransacao - Status da transação, recebe TRUE ou FALSE.
     * 
     * @return integer
     */
    public function incluirTransacaoCartao($clioid, $titoid, $idTransacao, $statusTransacao = false, $ccchoid, $nit = '', $transactionStatus = '')
    {
        $statusTransacao = ($statusTransacao == true)?'TRUE':'FALSE';
        
        try {
            // Inicia a Transação
            if($idTransacao == 0){

                $sql = "INSERT INTO controle_transacao_cartao 
                                    (ctcclioid, ctcdt_inclusao, ctctipotransacao, ctctitoid, ctcsucesso, ctcnit)
                             VALUES ($clioid, NOW(), 'T', $titoid, $statusTransacao, '$nit')
                          RETURNING ctcoid";
                
                $result = pg_query($this->conn, $sql);
                $id_transacao = pg_fetch_result($result, 0, "ctcoid");

                return $id_transacao;
                
            } else {
                
                // Atualiza a transacao caso ja exista uma transacao em aberto e o status foi positivo
                if($statusTransacao == 'TRUE') {

                    $sql = "UPDATE controle_transacao_cartao
                               SET ctcdt_inclusao = NOW(), 
                                   ctcsucesso = $statusTransacao,
                                   ctcccchoid = $ccchoid,
                                   ctcnit = '$nit',
                                   ctcstatus = '$transactionStatus'
                             WHERE ctcoid = $idTransacao ";

                    $result = pg_query($this->conn, $sql);

                    return $idTransacao;
                }

            }                               
        } catch (Exception $e) {
            echo $e->getMessage();  
            exit();  
        }       
    }
    
    /**
     * Registra o erro na tabela controle_transacao_cartao
     * 
     * @param integer $idTransacao - Id do Histórico
     * @param boolean $motivo      - Motivo do erro
     * @param string  $nit         - Código de transação  
     * 
     * @return integer
     */
    public function incluirTransacaoCartaoErro($idTransacao, $motivo, $nit = '', $status = '')
    {
        try {

            $sql = "UPDATE controle_transacao_cartao
                       SET ctcdt_inclusao = NOW(), 
                           ctcsucesso = false,
                           ctcmotivo = '$motivo'";
            
             if(!empty($nit)){
            	 $sql .=" ,ctcnit = '".$nit."' ";
             }
            
             if(!empty($status)){
                 $sql .=" ,ctcstatus = '$status' ";
             }
                 
                 $sql .=" WHERE ctcoid = $idTransacao ";
            
            $result = pg_query($this->conn, $sql);

            return $idTransacao;

        } catch (Exception $e) {
            echo $e->getMessage();  
            exit();  
        }       
    }

    /**
     * Recupera informações de cadastro do cartão atual
     * 
     * @param integer $clioid - id do cliente
     * 
     * @return Array
     */
    public function buscaDadosCartao($clioid)
    {
        try{
            $sql = "SELECT cccoid, ccchash, cccaccoid, clicformacobranca, cccnome_cartao
                      FROM cliente_cobranca_credito
                INNER JOIN cliente_cobranca ON cccclioid = clicclioid
                     WHERE cccclioid = $clioid 
                       AND cccativo = TRUE 
                       AND clicexclusao IS NULL	
                  ORDER BY cccoid DESC
					 LIMIT 1 ";

            $qryCartao = pg_query($this->conn, $sql);
            
            if(pg_num_rows($qryCartao) == 1){
                
                $rsCartao = pg_fetch_array($qryCartao);
                
                $infoCartao['cccoid']         = $rsCartao['cccoid'];
                $infoCartao['hashcartao']     = utf8_encode($rsCartao['ccchash']);
                $infoCartao['autorizadora']   = $rsCartao['cccaccoid'];
                $infoCartao['forma_cobranca'] = $rsCartao['clicformacobranca'];
                $infoCartao['nome_cartao']    = strtoupper($rsCartao['cccnome_cartao']);
                
            } else {
                return false;
            }
            
            return $infoCartao;
            
        }catch (Exception $e) {
            echo $e->getMessage();  
            exit();  
        } 
            
    }
    
    /**
     * Inclui o registro na tabela cliente_cobranca_credito_historico em caso de confirmação 
     * dos dados do cartão e aprovação pela autorizadora. 
     * 
     * @param integer $clioid      - Id do Cliente
     * @param integer $idTransacao - Id da Transação 
     * @param integer $titoid      - Id do Título
     * @param array   $his         - Array de Retorno com os Dados do Pagamento
     * 
     * @return integer
     */
    public function incluiHistoricoPagamento($clioid, $idTransacao, $titoid, $hist)
    {
        try {
            $sql = "INSERT INTO cliente_cobranca_credito_historico(
                                ccchclioid,
                                ccchcccoid,
                                ccchdt_inclusao,
                                ccchtitoid,
                                ccchpedido,
                                ccchcupom_cliente,
                                ccchcupom_estabelecimento,
                                ccchautorizadora,
                                ccchtipopagamento,
                                ccchnumero_autorizacao,
                                ccchnumero_seq_estabelecimento,
                                ccchnsu_esitef,
                                ccchnsu_sitef,
                                ccchnsu_autorizadora,
                                ccchtid,
                                cccheci,
                                ccchstatustransacao,
                                ccchstatusanalise,
                                ccchcodigo_analise, 
                                ccchmsg_analise,
                                ccchcodigo_resposta,
                                ccchcodigo_resposta_sitef,
                                ccchvalortransacao,
                                ccchtipotransacao,
                                ccchmensagem,
                                ccchdt_resposta,
                                ccchdt_resposta_sitef,
                                ccchdt_extra 
                        ) VALUES (
                                $clioid,
                                $idTransacao,
                                'now()',
                                $titoid ";
                        //ccchpedido     
                        if(!empty($hist->orderId)){
                            $sql .=", '".$hist->orderId."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchcupom_cliente    
                        if(!empty($hist->customerReceipt)){
                            $sql .=", '".$hist->customerReceipt."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchcupom_estabelecimento
                        if(!empty($hist->merchantReceipt)){
                            $sql .=", '".$hist->merchantReceipt."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchautorizadora
                        if(!empty($hist->authorizerId)){
                            $sql .=", ".$hist->authorizerId." ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchtipopagamento
                        if(!empty($hist->acquirer)){
                            $sql .=", '".$hist->acquirer."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchnumero_autorizacao
                        if(!empty($hist->authorizationNumber)){
                            $sql .=", '".$hist->authorizationNumber."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchnumero_seq_estabelecimento
                        if(!empty($hist->merchantUSN)){
                            $sql .=", '".$hist->merchantUSN."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchnsu_esitef
                        if(!empty($hist->esitefUSN)){
                            $sql .=", '".$hist->esitefUSN."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchnsu_sitef
                        if(!empty($hist->sitefUSN)){
                            $sql .=", '".$hist->sitefUSN."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchnsu_autorizadora
                        if(!empty($hist->hostUSN)){
                            $sql .=", '".$hist->hostUSN."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchtid
                        if(isset($hist->tid) && !empty($hist->tid)){
                            $sql .=", '".$hist->tid."' ";
                        }else{
                            $sql .=", NULL ";
                        } 
                        //cccheci
                        $sql .=", NULL ";
                        //ccchstatustransacao
                        if(!empty($hist->transactionStatus)){
                            $sql .=", '".$hist->transactionStatus."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchstatusanalis
                        //ccchcodigo_analise
                        //ccchmsg_analis
                        $sql .=", ''
                                , '' 
                                , '' ";
                        //ccchcodigo_resposta
                        if(!empty($hist->responseCode)){    
                            $sql .=", ".$hist->responseCode." ";
                        }else{
                            $sql .=", NULL ";
                        } 
                        //ccchcodigo_resposta_sitef
                        if(!empty($hist->sitefDateResponseCode)){
                            $sql .=", '".$hist->sitefResponseCode."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchvalortransacao
                        if(!empty($hist->amount)){
                            $sql .=", '".$hist->amount."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchtipotransacao
                        if(!empty($hist->paymentType)){
                            $sql .=", '".$hist->paymentType."' ";
                        }else{
                            $sql .=", NULL ";
                        }  
                        //ccchmensagem
                        if(!empty($hist->message)){
                            $sql .=", '".$hist->message."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchdt_resposta
                        if(!empty($hist->date)){
                            $sql .=", '".$hist->date."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchdt_resposta_sitef
                        if(!empty($hist->sitefDate)){
                            $sql .=", '".$hist->sitefDate."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                        //ccchdt_extra
                        if(!empty($hist->extraField)){
                            $sql .=", '".$hist->extraField."' ";
                        }else{
                            $sql .=", NULL ";
                        }
                           
                        $sql .=" ) RETURNING ccchoid ";
                             
            $result  = pg_query($this->conn, $sql);
            
            if(is_resource($result)) {
                $obj = pg_fetch_object($result);
                $ccchoid = $obj->ccchoid;
            }
            
            return $ccchoid;
        } catch (Exception $e) {
            die($e->getMessage());  
        } 
    }
    
    /**
     * Após realizar todos os procedimentos de pagamento e validar,
     * baixar o título ao qual o pagamento se refere
     * 
     * @param integer $titoid     - id do Título
     * @param integer $ccchoid    - id da tabela cliente_cobranca_credito_historico
     * @param float   $valor_pago - Valor autorizado para cobrança pela operadora
     * @param integer $cd_usuario - Usuário responsável pela alteração do registro
     * 
     * @return boolean
     */
    public function confirmaPagamento($titoid, $ccchoid, $valor_pago, $cd_usuario)
    {
        
        try {
        	
            $sql = "UPDATE titulo
                       SET tittransacao_cartao = TRUE,
                           titccchoid = $ccchoid,
                           titdt_pagamento = 'now()',
                           titvl_pagamento = ".$valor_pago.", 
                           titdt_credito = 'now()', 
                           titobs_recebimento = 'Substituição de título', 
                           titusuoid_alteracao = ".$cd_usuario."
                     WHERE titoid = $titoid";

            if(!pg_query($this->conn, $sql)){
            	throw new Exception('Falha ao atualizar titulo.');
            }
            
            return true;
            
        } catch (Exception $e) {
            die($e->getMessage());  
        } 
        
    }
    
    /**
     * 
     */
    public function insereTituloCredito($titoid, $dt_venc, $modo = NULL, $quant_parcelas = NULL)
    {
        try {
            $sql = "SELECT ( titvl_titulo 
							+ titvl_multa 
							+ titvl_juros 
							- titvl_desconto 
							- (CASE WHEN titvl_ir IS NULL THEN 0.00 ELSE titvl_ir END) 
							- (CASE WHEN titvl_iss IS NULL THEN 0.00 ELSE titvl_iss END) 
							- (CASE WHEN titvl_piscofins IS NULL THEN 0.00 ELSE titvl_piscofins END)) as valor_corrigido, *
                      FROM titulo 
                     WHERE titoid = $titoid ";
            
            $result  = pg_query($this->conn, $sql);
            
            //se houver títulos, preenche array para retorno
            if(pg_num_rows($result) == 1){
                
                $titulo = pg_fetch_array($result);
                
                $forcoid = $this->_retornaFormaCobranca($titulo['titformacobranca']);
                
                $titdt_inclusao          = "now()";
                $titvl_titulo            = $titulo['valor_corrigido'];
                $titvl_desconto          = "";//$titulo['titvl_desconto'];
                $titmdescoid             = "";//$titulo['titmdescoid'];
                $titvl_juros             = "";//$titulo['titvl_juros'];
                $titvl_multa             = "";//$titulo['titvl_multa'];
                $titvl_acrescimo         = "";//$titulo['titvl_acrescimo'];
                $titvl_pagamento         = "";
                $titdt_pagamento         = "";
                $titdt_credito           = "";
                $titclioid               = $titulo['titclioid'];
                $titformacobranca        = $forcoid;
                $titbanco_cheque         = $titulo['titbanco_cheque'];
                $titagencia_cheque       = $titulo['titagencia_cheque'];
                $titno_cheque            = $titulo['titno_cheque'];
                $titemissao              = $titulo['titemissao'];
                $titdev_cheque           = $titulo['titdev_cheque'];
                $titcfbbanco             = $titulo['titcfbbanco'];
                $titno_cartao            = 'S/N';
                $titvl_ir                = "";//$titulo['titvl_ir'];
                $titvl_piscofins         = "";//$titulo['titvl_piscofins'];
                $titvl_iss               = "";//$titulo['titvl_iss'];
                $titnota_promissoria     = $titulo['titnota_promissoria'];
                $titdt_cancelamento      = "";//$titulo['titdt_cancelamento'];
                $titobs_cancelamento     = "";//$titulo['titobs_cancelamento'];
                $titautoriz_cartao       = $titulo['titautoriz_cartao'];
                $titdt_vencimento        = $dt_venc;
                $titconta_corrente       = $titulo['titconta_corrente'];
                $titno_parcela           = $titulo['titno_parcela'];
                $titno_avulso            = 0;//$titulo['titno_avulso'];
                $inc_titulo_avulso       = 0;
                $titvlr_comissao_ch_terc = 0;//$titulo['titvlr_comissao_ch_terc'];
                $tittittoid              = $titulo['tittittoid'];
                $titsubstituido          = $titoid;
                
	            if($modo === 'parcelado'){
	            	
	            	//calcula as parcelas, inclusive a diferença de centavos na primeira parcela 
	            	$retornoCalculoParcelas = $this->calcularParcelas($quant_parcelas, $titvl_titulo);
	            	
	            	$data = date('d/m/Y');
	            	//transforma a data explodida em array
	            	list($dia, $mes, $ano) = explode("/", $data);
	            	
	            	foreach ($retornoCalculoParcelas as $num_parcela => $valorParcela){
	            		
	            		//resolve a posição zero que retorna da função calcularParcelas,
	            		//para que o 1º vencimento não seja na data corrente, mas daqui 30 dias
	            		$num_parcela ++;
		            	
	            		//calcula data de vencimento de acordo o número da parcela   
	            		$titdt_vencimento = date('d-m-Y', mktime(0,0,0, $mes + $num_parcela, $dia , $ano));
	            		
	            		$campos_inclusao = " \"$titdt_inclusao\" \"$valorParcela\" \"$titvl_desconto\" \"$titmdescoid\" \"$titvl_juros\" \"$titvl_multa\" \"$titvl_acrescimo\" \"$titvl_pagamento\" \"$titdt_pagamento\" \"$titdt_credito\" \"$titclioid\" \"$titformacobranca\" \"$titbanco_cheque\" \"$titagencia_cheque\" \"$titno_cheque\" \"$titemissao\" \"$titdev_cheque\" \"$titcfbbanco\" \"$titno_cartao\" \"$titvl_ir\" \"$titvl_piscofins\" \"$titvl_iss\" \"$titnota_promissoria\" \"$titdt_cancelamento\" \"$titobs_cancelamento\" \"$titautoriz_cartao\" \"$titdt_vencimento\" \"$titconta_corrente\" \"$num_parcela\" \"$titno_avulso\" \"$inc_titulo_avulso\" \"$titvlr_comissao_ch_terc\" \"$tittittoid\" \"$titsubstituido\" ";
			            
	            		$query = "SELECT substituicao_titulo_i('$campos_inclusao');";
			            $result = pg_query($this->conn, $query);
	            	}
	            	
	            }else{
	            	$campos_inclusao = " \"$titdt_inclusao\" \"$titvl_titulo\" \"$titvl_desconto\" \"$titmdescoid\" \"$titvl_juros\" \"$titvl_multa\" \"$titvl_acrescimo\" \"$titvl_pagamento\" \"$titdt_pagamento\" \"$titdt_credito\" \"$titclioid\" \"$titformacobranca\" \"$titbanco_cheque\" \"$titagencia_cheque\" \"$titno_cheque\" \"$titemissao\" \"$titdev_cheque\" \"$titcfbbanco\" \"$titno_cartao\" \"$titvl_ir\" \"$titvl_piscofins\" \"$titvl_iss\" \"$titnota_promissoria\" \"$titdt_cancelamento\" \"$titobs_cancelamento\" \"$titautoriz_cartao\" \"$titdt_vencimento\" \"$titconta_corrente\" \"$titno_parcela\" \"$titno_avulso\" \"$inc_titulo_avulso\" \"$titvlr_comissao_ch_terc\" \"$tittittoid\" \"$titsubstituido\" ";
	            	$query = "SELECT substituicao_titulo_i('$campos_inclusao');";
	            	$result = pg_query($this->conn, $query);
	            }
	            
	            return true;
            
            }
            
        }catch (Exception $e) {
            die($e->getMessage());  
        } 
        
    }

    /**
     * Retorna nova forma de cobrança para títulos de substituição
     * 
     * @param integer $forcoid - forma de cobranca atual
     * 
     * @return integer $cfcforma_destino - nova forma de cobrança
     */
    private function _retornaFormaCobranca($forcoid)
    {
    	try{
    		 
    		$sql = "SELECT cfcforma_destino
    				FROM conciliacao_forma_cobranca
    				WHERE cfcforma_atual = ".$forcoid;

    		$result  = pg_query($this->conn, $sql);

    		if(is_resource($result)) {
    			$obj = pg_fetch_object($result);
    			$cfcforma_destino = $obj->cfcforma_destino;
    		} else {
    			throw new Exception('Para a forma de cobranca informada, nao existe credito a receber cadastrado.');
    		}

    		return $cfcforma_destino;

    	}catch (Exception $e){
    		die($e->getMessage());
    	}
    }
    
    
    /**
     * Recupera informações de cadastro de transação de cartão
     *
     * @param integer $clioid - id do cliente
     * @param integer $titoid - id do título
     *
     * @return Array
     */
    public function pesquisarNit($clioid, $titoid){

    	try {

    		$sql = "SELECT ctcnit, ctcoid, ctcstatus
		    		FROM controle_transacao_cartao
		    		WHERE ctcclioid = $clioid
		    		AND ctctitoid = $titoid
		    		AND ctcsucesso IS FALSE
		    		AND ctcnit <> ''
		    		ORDER BY ctcoid DESC
		    		LIMIT 1 ";
    		 
    		$qryNit = pg_query($this->conn, $sql);

    		if(pg_num_rows($qryNit) > 0){
    			$nit['nit'] =  pg_fetch_result($qryNit,0,'ctcnit');
    			$nit['idTransacao'] = pg_fetch_result($qryNit,0,'ctcoid');
    			$nit['status'] = pg_fetch_result($qryNit,0,'ctcstatus');
    		}

    		return $nit;

    	} catch (Exception $e) {
    		die($e->getMessage());
    	}
    	 
    }
    
    /**
     * Recupera informações de cadastro co cliente
     *
     * @param integer $clioid - id do cliente
     *
     * @return Array
     */
    
    public function buscaDadosCliente($clioid){

    	try {
    		 
    		$sql =" SELECT
		    		clinome,
		    		clitipo,
		    		clino_cpf,
		    		clino_cgc
		    		FROM clientes
		    		WHERE clioid = $clioid " ;
    		 
    		$dados = pg_query($this->conn, $sql);
    		 
    		if(pg_num_rows($dados) > 0){
    			$dadosCliente['clinome']   = pg_fetch_result($dados,0,'clinome');
    			$dadosCliente['clitipo']   = pg_fetch_result($dados,0,'clitipo');
    			$dadosCliente['clino_cpf'] = pg_fetch_result($dados,0,'clino_cpf');
    			$dadosCliente['clino_cgc'] = pg_fetch_result($dados,0,'clino_cgc');
    		}
    		 
    		return $dadosCliente;
    		 
    	} catch (Exception $e) {
    		die($e->getMessage());
    	}
    }
    
    
    /**
     * Calcula os valores e retorna o resultado da primeira parcela com a diferença de centavos (se houver),
     * retorna no mesmo array a primeira (no índice [0]) e as demais parcelas calculadas    
     * 
     * @author Márcio Sampaio ferreira <marcioferreira@brq.com>
     * @param int $quantParcelas
     * @param float $valorTotalFatura   no formato 99.99
     * @return multitype:number |boolean
     */
    private function calcularParcelas($quantParcelas, $valorTotalFatura){
    
    	try{
    
    		if($quantParcelas == '' || $valorTotalFatura == ''){
    			throw new Exception('Informe a quantidade de parcelas e o valor total para calcular o valor das parcelas');
    		}
    		
    		if($quantParcelas <= 0){
    			throw new Exception('Nao e possivel a divisao por 0(zero)');
    		}
    
    		$somaParcelas = 0;
    		$arrayParcelas = Array();
    
    		//faz o tratamento da vírgula trocando para ponto
    		$valorTotalFatura = str_replace(',', '.', $valorTotalFatura);
    
    		//efetua a divisão do valor total bruto pela quantidade de parcelas deixando duas casas decimais após o ponto
    		$valorPorParcela = intval(($valorTotalFatura / $quantParcelas) * 100)/ 100;
    
    		//explode para pegar os centavos depois do ponto
    		$vParcela = explode('.',$valorPorParcela);
    
    		//faz o cálculo das outras parcelas SEM A PRIMEIRA PARCELA
    		for($i = 1; $i < $quantParcelas ; $i++){
    			//faz a soma das parcelas
    			$somaParcelas+=$valorPorParcela;
    			//armazena o cálculo das parcelas para retornar
    			$arrayParcelas[$i] = number_format($valorPorParcela,2,'.','');
    		}
    
    		//valor total de todas a parcelas
    		$totalTodasParcelas = ($somaParcelas+$valorPorParcela);
    
    		//pega o valor bruto total da fatura (com os milésimos), subtrai com o total de todas as parcelas
    		//ECONTRANDO A DIFERENÇA DOS CENTAVOS e arredonda
    		$diffCentavos = round($valorTotalFatura - $totalTodasParcelas, 2);
    		$valorPrimeiraParcela = ($valorPorParcela + $diffCentavos);
    
    		//insere a primeira parcela (valor calculado e formatado) na primeira posição do array
    		array_unshift($arrayParcelas, number_format($valorPrimeiraParcela, 2,'.',''));
    
    		return $arrayParcelas;
    
    	}catch (Exception $e){
    		echo $e->getMessage();
    		return false;
    	}
    
    }
    
    /**
     * Método para buscar o corpo do email, o e-mail será montado e enviado para o usuário
     * O tipo de corpo do e-mail será retornado de acordo o parâmetro do campos: se.seedescricao, se.seecabecalho, se.seedescrica
     * que devem possuir cadastro no bd
     *
     * @param string $seetdescricao
     * @author Márcio Sampaio Ferreira
     * @return array
     */
    public function getDadosCorpoEmail($paramLayout){
    		
    	try{
    			
    		if(empty($paramLayout)){
    			throw new Exception("A descricao para a busca nao pode ser vazia");
    		}
    			
    		$sql = "  SELECT   sf.seefdescricao                      AS funcionalidade
							 , se.seecorpo                           AS corpo_email
							 , se.seecabecalho                       AS assunto_email
							 , seepadrao
							 , srvlocalizador                        AS servidor
    						 , TO_CHAR(NOW(), 'dd-mm-YYYY')          AS data_atual
	                         , TO_CHAR(CURRENT_TIMESTAMP, 'HH24:MI') AS hora_atual
						  FROM servico_envio_email se
						  JOIN servico_envio_email_funcionalidade sf ON sf.seefoid = se.seeseefoid
						  JOIN servico_envio_email_titulo st ON st.seetoid = se.seeseetoid
						  JOIN servidor_email ON srvoid = seesrvoid
						 WHERE sf.seefdescricao = 'Transacoes de Cartao de Credito'
						   AND st.seetdescricao = 'Resultado de Cobranca por Cartao de Credito'
						   AND se.seedescricao = '".trim($paramLayout)."'
						   AND se.seedt_exclusao IS NULL
						   AND sf.seefdt_exclusao IS NULL  ";
    		
    		
    		if (!$result = pg_query($this->conn, $sql)) {
    			throw new Exception("Erro ao recuperar layout para envio de e-mails.");
    		}
    
    		if (pg_num_rows($result) > 0) {
    			return pg_fetch_object($result);
    		}
    			
    		return false;
    
    	}catch (Exception $e){
    		echo $e->getMessage();
    		exit;
    	}
    }
    
    
    /**
     * Recupera o email do usário que receberá o relatório
     *
     * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
     * 27/09/2013
     *
     * @return Object
     */
    public function getEmailEnvioRelatorio(){
    
    	try{
    
    		$sql = "SELECT pcsidescricao, pcsioid
					FROM
						parametros_configuracoes_sistemas,
						parametros_configuracoes_sistemas_itens
					WHERE
						pcsoid = pcsipcsoid
					AND pcsdt_exclusao is null
					AND pcsidt_exclusao is null
					AND pcsipcsoid = 'ENVIORELATORIOTRANSACAOESCARTAO'
					AND pcsioid = 'EMAIL'
					LIMIT 1  ";
    
    		if (!$result = pg_query($this->conn, $sql)) {
    			throw new Exception ("Falha ao recuperar email de teste ");
    		}
    
    		if(count($result) > 0){
    			return pg_fetch_object($result);
    		}
    
    	}catch(Exception $e){
    		return $e->getMessage();
    	}
    }
    
    
    /**
     * Recupera o email de testes
     *
     * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
     * 14/06/2013
     *
     * @return Object
     */
    public function getEmailTeste(){
    
    	try{
    
    		$sql = "SELECT pcsidescricao, pcsioid
	  				FROM
						parametros_configuracoes_sistemas,
						parametros_configuracoes_sistemas_itens
	 				WHERE
						pcsoid = pcsipcsoid
				    AND pcsdt_exclusao is null
					AND pcsidt_exclusao is null
					AND pcsipcsoid = 'PARAMETROSAMBIENTETESTE'
					AND pcsioid = 'EMAIL'
					LIMIT 1 ";
    
    		if (!$result = pg_query($this->conn, $sql)) {
    			throw new Exception ("Falha ao recuperar email de teste ");
    		}
    
    		if(count($result) > 0){
    			return pg_fetch_object($result);
    		}
    
    	}catch(Exception $e){
    		return $e->getMessage();
    	}
    }
    
    
    /**
     * inicia transação com o BD
     */
    public function begin()
    {
        $rs = pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * confirma alterações no BD
     */
    public function commit()
    {
        $rs = pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * desfaz alterações no BD
     */
    public function rollback()
    {
        $rs = pg_query($this->conn, "ROLLBACK;");
    }
    
}