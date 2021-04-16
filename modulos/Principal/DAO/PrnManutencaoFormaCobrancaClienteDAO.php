<?php

/**
 * PrnManutencaoFormaCobrancaClienteDAO.php
 * 
 * Classe de persistência dos dados
 * 
 * @author Diego C. Ribeiro
 * @email dcribeiro@brq.com
 * @since 17/10/2012
 * @STI 80219
 * @package Principal
 *
 */

//require_once _MODULEDIR_ . 'eSitef/Action/IntegracaoSoftExpress.class.php';

class PrnManutencaoFormaCobrancaClienteDAO {

    private $conn;
    
    /**
     * Armazena o ID da transacao que foi iniciada
     * @var integer
     */
    private $idTransacao;
    
    /**
     * Construtor
     * @param object $conn
     */
    public function __construct($conn) {  
//        $dbstring = "dbname=sascar_homologacao host=10.1.101.14 user=nobody";
//        $conn = pg_connect($dbstring);
        
// paulopinto: file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'antes classe construct PrnManutencaoFormaCobrancaClienteDAO:' . pg_transaction_status($conn), FILE_APPEND);
//            if(!pg_transaction_status($conn) == 2){
//                $dbstring = "dbname=sascar_homologacao host=10.1.101.14 user=nobody";
//                $connec = pg_connect($dbstring);
//                $this->conn = $connec;
//file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'connec:' . pg_transaction_status($connec), FILE_APPEND);
//                
//            }else{
                $this->conn = $conn;
//            }
//file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'depois classe construct PrnManutencaoFormaCobrancaClienteDAO:' . pg_transaction_status($this->conn), FILE_APPEND);
        
    }

    /**
     * Retorna as opções de pagamento
     * @return multitype:string
     */
    public function getInformacoes($clioid) {
    	
    	$prnCliente = new PrnCliente();
    	$arrInformacoes['clientes']  = $prnCliente->getDadosCliente($clioid);
    	
    	$prnDadosCobranca = new PrnDadosCobranca();
    	$arrInformacoes['formaPagamento']      = $prnDadosCobranca->getFormaCobranca();
    	
    	$dadosCobrancaAtual =  $prnDadosCobranca->getFormaCobrancaAtual($clioid);    
    	
    	$formaPagamentoAtual['forcoid']                     = $dadosCobrancaAtual->forcoid;
    	$formaPagamentoAtual['forcnome']                    = utf8_encode($dadosCobrancaAtual->forcnome);
    	$formaPagamentoAtual['forccobranca_cartao_credito']	= $dadosCobrancaAtual->forccobranca_cartao_credito;
    	$formaPagamentoAtual['forcdebito_conta']            = $dadosCobrancaAtual->forcdebito_conta;
    	$formaPagamentoAtual['forccobranca_registrada']     = $dadosCobrancaAtual->forccobranca_registrada;
    	$formaPagamentoAtual['sufixo']                      = $dadosCobrancaAtual->cccsufixo;
    	$formaPagamentoAtual['cccativo']                    = $dadosCobrancaAtual->cccativo;
    	
    	$arrInformacoes['formaPagamentoAtual'] = $formaPagamentoAtual;    	
    	
    	return $arrInformacoes;
    
    }       
    
	/**
	 * Para toda transação de inclusão de cartão de crédito, 
     * inserir um registro na tabela controle_transacao_cartao, 
     * onde o campo ctctipotransacao (tipo da transação realizada)  será igual a "C";
     * 
	 * @param integer $clioid - Id do Cliente
	 * @param boolean $statusTransacao - Status da transação, recebe TRUE ou FALSE.
	 */
    public function incluirTransacaoCartao($clioid, $statusTransacao = false, $dadosTransacao = NULL, $motivo = NULL){
    	
    	if($statusTransacao == true){
    		$statusTransacao = 'TRUE';
    	}else{
    		$statusTransacao = 'FALSE';
    	}
// paulopinto:   	       $rs12 = fopen(_SITEDIR_.'arq_financeiro/log_incluirTransacaoCartao.txt', 'a+');
//	       fwrite($rs12, ' cliente:'.$clioid.' -> incluirTransacaoCartao:'.$this->idTransacao.' statusTransacao:'.$statusTransacao . ' Conn:'.pg_connection_status($this->conn));
//	       fclose($rs12);

    	try {
    		
            // Inicia a Transação
            if(empty($this->idTransacao)){

                $sql = "INSERT INTO controle_transacao_cartao 
                                    (ctcclioid, ctcdt_inclusao, ctctipotransacao, ctcsucesso, ctcmotivo)
                             VALUES ($clioid, NOW(), 'C', $statusTransacao,'Armazenamento de Cartão')
                          RETURNING ctcoid";
                
               if (!$result = pg_query($this->conn, $sql)) {
//paulopinto:    	       $rs13 = fopen(_SITEDIR_.'arq_financeiro/log_incluirTransacaoCartao_falha.txt', 'a+');
//	       fwrite($rs13, 'Falha de inclusao cliente:'.$clioid.' -> incluirTransacaoCartao:'.$this->idTransacao.' statusTransacao:'.$statusTransacao . ' Conn:'.pg_connection_status($this->conn).' sql:'.$sql);
//	       fclose($rs13);

                    throw new Exception('ERRO: <b>Falha ao inserir controle transacao do cartao.</b>');
                }
                
                $id_transacao = pg_fetch_result($result, 0, "ctcoid");

                $this->idTransacao = $id_transacao;
                
    	       $rs14 = fopen(_SITEDIR_.'arq_financeiro/log_incluirTransacaoCartao_sucesso.txt', 'a+');
	       fwrite($rs14, 'cliente:'.$clioid.' -> incluirTransacaoCartao:'.$this->idTransacao.' statusTransacao:'.$statusTransacao . ' Conn:'.pg_connection_status($this->conn).' Transacao:'.$id_transacao.' sql:'.$sql);
	       fclose($rs14);

                
                
                return true;
            }

            // Atualiza a transacao caso ja exista uma transacao em aberto 
            else{

            	$sql = "UPDATE controle_transacao_cartao
		            	   SET ctcdt_inclusao = NOW() 
            	              ,ctcsucesso = $statusTransacao ";
            	
	            if(!empty($dadosTransacao->storeResponse->status)){
	                  $sql .=",ctcstatus =  '".$dadosTransacao->storeResponse->status."' ";
	            }

	            if(!empty($motivo)){
	            	$sql .=", ctcmotivo =  '".$motivo."' ";
	            }
	            
		        $sql .=" WHERE ctcoid = $this->idTransacao ";
// paulopinto:    	       $rs19 = fopen(_SITEDIR_.'arq_financeiro/log_incluirTransacaoCartao_falha_idTransacao.txt', 'a+');
//	       fwrite($rs19, 'Falha de inclusao cliente:'.$clioid.' -> incluirTransacaoCartao:'.$this->idTransacao.' statusTransacao:'.$statusTransacao . ' Conn:'.pg_connection_status($this->conn).' Transacao:'.$id_transacao.' sql:'.$sql);
//	       fclose($rs19);

            	if (!pg_query($this->conn, $sql)) {
//paulopinto:    	       $rs15 = fopen(_SITEDIR_.'arq_financeiro/log_incluirTransacaoCartao_falha_update.txt', 'a+');
//	       fwrite($rs15, 'Falha de inclusao cliente:'.$clioid.' -> incluirTransacaoCartao:'.$this->idTransacao.' statusTransacao:'.$statusTransacao . ' Conn:'.pg_connection_status($this->conn).' Transacao:'.$id_transacao);
//	       fclose($rs15);

                    throw new Exception('ERRO: <b>Falha ao atualizar controle transacao do cartao.</b>');
            	}
//paulopinto:    	       $rs16 = fopen(_SITEDIR_.'arq_financeiro/log_incluirTransacaoCartao_sucesso_update.txt', 'a+');
//	       fwrite($rs16, 'Falha de inclusao cliente:'.$clioid.' -> incluirTransacaoCartao:'.$this->idTransacao.' statusTransacao:'.$statusTransacao . ' Conn:'.pg_connection_status($this->conn).' Transacao:'.$id_transacao);
//	       fclose($rs16);

            	return true;
            }
            		    			    
    	}catch (Exception $e){
			return $e->getMessage();
		}	
    }

    
    public function getIdTransacao(){
    	
    	return $this->idTransacao;
    }
    
	/**
	 * Retorna se a forma de cobrança é do tipo cartão de crédito
	 * @return string|boolean
	 */
	public function verificaFormaCobrancaCartao(){
		
        $forcoid = (isset($_POST['forcoid'])) ? $_POST['forcoid'] : null;

        try{

            if(!empty($forcoid)){				

                    $sql = "SELECT forccobranca_cartao_credito, forcdebito_conta
                              FROM forma_cobranca 
                             WHERE forcoid = $forcoid";
   	
                    if (!$result = pg_query($this->conn, $sql)) {
                    	throw new Exception('ERRO: <b>Falha ao retornar forma de cobranca cartao.</b>');
                    }
                    
                    $alterarFormaPagamento = null;
                    if(pg_num_rows($result)==1){
                        $rsFormaPagamento = pg_fetch_array($result);
                        
                        $alterarFormaPagamento['forccobranca_cartao_credito'] = $rsFormaPagamento['forccobranca_cartao_credito'];
                        $alterarFormaPagamento['forccobranca_debito'] = $rsFormaPagamento['forcdebito_conta'];
                    }

                    return $alterarFormaPagamento;

            }else{
                    return false;
            }
            
        }catch (Exception $e){
			return $e->getMessage();
		} 
	}
    
    /**
	 * Retorna se a forma de cobrança é do tipo cartão de crédito
	 * @return string|boolean
	 */
	public function verificaFormaCobrancaBoleto($forcoid) {
        try {
            if (!empty($forcoid)) {
                $sql = "SELECT forccobranca_cartao_credito, forcdebito_conta
                          FROM forma_cobranca 
                         WHERE forcoid = $forcoid";

                if (!$result = pg_query($this->conn, $sql)) {
                    throw new Exception('ERRO: <b>Falha ao retornar forma de cobranca .</b>');
                }
                if (pg_num_rows($result) > 0){
                    $resultado = pg_fetch_object($result,0);
                    if ($resultado->forccobranca_cartao_credito == 't' || $resultado->forcdebito_conta == 't'){
                        return 'debito_cartao';
                    } else {
                        return 'outros';
                    }
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
	 * Remove os dados do Cartão de Crédito
	 */
	public function removerCobrancaClienteCredito($clioid)
	{
        try {

        	if(!empty($clioid)){
        		
	            $sql = "UPDATE cliente_cobranca_credito
	                       SET cccativo = FALSE
	                     WHERE cccclioid = $clioid";
	
	            if(!pg_query($this->conn, $sql)){
	           	    throw new Exception('Falha ao remover cobranca cliente credito.');
	            }
        	}
            
           return true;
           
        }catch (Exception $e) {
           return $e->getMessage();  
        } 
	}
	
	/**
	 * Inclui os dados do Cartão de Crédito para cobrança
	 */	
	public function incluirCobrancaClienteCredito($dadosCobranca, $dadosConfirma, $dataValidadeCartao){
        
		$idIntegradora    = 1;   // Id da empresa integradora	
        $codAutorizadora  = $dadosCobranca->storeResponse->authorizerId;// Código da autorizadora
        $dataInclusao     = 'now()';// Data de inclusão do cartão de crédito	
        $idUsuarioLogado  = $dadosConfirma->id_usuario; // Usuário que efetuou o processo
        $msgRetorno       = $dadosCobranca->storeResponse->message;// Mensagem de Retorno obtida da Integradora
        $statusRetorno    = $dadosCobranca->storeResponse->status;// Status do cartão de crédito na Integradora	
        $nita             = $dadosCobranca->storeResponse->nita;// Código nita criptografado retornado pela Integradora			
        $numeroSequencial = $dadosCobranca->storeResponse->nsua;// Número sequencial retornado pela Integradora	
        $hash             = $dadosCobranca->storeResponse->cardHash;// Hash do cartão de crédito retornado pela Integradora		
        $sufixoCartao     = $dadosCobranca->storeResponse->cardSuffix;// Sufixo do cartão de crédito retornado pela Integradora	
        $nomePortador     = $dadosConfirma->nomePortador;// Nome do portador do cartão
        $numAuxiliar      = "''";// Valor auxiliar para identificar transação		
        $obsTransacao     = "''";// Observações da transação		
        $statusCartao     = 'TRUE';// Status do Cartão (ture/false)		
        $dataAlteracao    = 'now()';// Data da alteração do cartão			

        try {
            $sql = "INSERT INTO 
                        cliente_cobranca_credito
                            (
                                cccclioid,
                                ccciccoid,
                                cccaccoid, 			
                                cccdt_inclusao, 			
                                cccusuoid_inclusao, 			
                                cccmensagem_retorno, 		
                                cccstatus, 				
                                cccnita, 					
                                cccnsu, 					
                                ccchash, 					
                                cccsufixo, 				
                                cccnumero_auxiliar, 			
                                cccobs, 					
                                cccativo,					
                                cccdt_alteracao,
                                cccdt_validade, 
                                cccnome_cartao
                            )
                    VALUES 
                            (
                                $dadosConfirma->id_cliente, 
                                $idIntegradora, 
                                $codAutorizadora,
                                $dataInclusao, 
                                $idUsuarioLogado,
                                '$msgRetorno',
                                '$statusRetorno',
                                '$nita', 
                                '$numeroSequencial', 
                                '$hash',
                                $sufixoCartao,
                                $numAuxiliar,
                                $obsTransacao, 
                                $statusCartao,
                                $dataAlteracao,
								'$dataValidadeCartao',
                                '$nomePortador'
                    );";
            
            /*  paulopinto:
            $rs = fopen(_SITEDIR_ . 'arq_financeiro/log_incluirCobrancaClienteCredito.txt', 'w+');
            fwrite($rs, $sql);
            fclose($rs);
            file_put_contents(_SITEDIR_ . 'arq_financeiro/log_siggo', PHP_EOL . 'incluirCobrancaClienteCredito:' . pg_transaction_status($this->conn), FILE_APPEND);            
            
            if ($this->conn != 2){
                  $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg2.txt', 'a+');
                fwrite($r, 'conexao realizada com sucesso. Banco:'.$this->conn->dbname.' usuario:'.$this->conn->user.' host:'.$this->conn->host.' status:'.pg_transaction_status($this->conn) );
                fwrite($r, pg_errormessage($this->conn));
                fclose($r);

                $dbstring = "dbname=sascar_homologacao host=10.1.101.14 user=nobody";
                $conn3 = pg_connect($dbstring);
                if($this->conn != 2){
                    pg_close($this->conn);
                    $this->conn = $conn3;

                    $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg_reconnect2.txt', 'a+');
                    fwrite($r, 'conexao recriada:'.$this->conn->dbname.' usuario:'.$this->conn->user.' host:'.$this->conn->host.' status:'.pg_transaction_status($this->conn) );
                    fclose($r);
                }
            }else{
                $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg2.txt', 'a+');
                fwrite($r, 'conexao realizada com sucesso. Banco:'.$this->conn->dbname.' usuario:'.$this->conn->user.' host:'.$this->conn->host.' status:'.pg_transaction_status($this->conn) );
                fclose($r);
                
            }
*/



            if (!pg_query($this->conn, $sql)) {
//paulopinto:                $rs = fopen(_SITEDIR_ . 'arq_financeiro/log_incluirCobrancaClienteCredito.txt', 'w+');
//                fwrite($rs, 'Falha ao incluir forma de cobranca credito:'.$sql);
//                fclose($rs);                
                
            	throw new Exception('ERRO: <b>Falha ao incluir forma de cobranca credito.</b>');
            }
            	
            return true;

        }catch (Exception $e) {
           return $e->getMessage();  
        } 
	}

    /**
     * Verifica se tipo de cobrança é Cartão de Credito
     */
    public function isCartaoCredito($forcoid) 
    {
        try {
            $sql = "SELECT forccobranca_cartao_credito
                      FROM forma_cobranca
                     WHERE forcoid = $forcoid";
            
            if (!$result = pg_query($this->conn, $sql)) {
            	throw new Exception('ERRO: <b>Falha ao verificar cobranca cartao.</b>');
            }
            
            if(pg_num_rows($result) == 1){
            
                $rsForc = pg_fetch_array($result);
                
                return ($rsForc['forccobranca_cartao_credito'] == 'f')?false:true;
            }
            
            return false;
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * Verifica se tipo de cobrança é Débito Automático
     */
    public function isDebito($forcoid)
    {
    	try{
	    	$sql =" SELECT forcdebito_conta
			    	FROM forma_cobranca
			    	WHERE forcvenda IS TRUE
			    	AND forcexclusao IS NULL
			    	AND forccobranca_cartao_credito IS FALSE
			    	AND forcdebito_conta IS TRUE
			    	AND forcoid = $forcoid ";
	
	    	if (!$result = pg_query($this->conn, $sql)) {
	    		throw new Exception('ERRO: <b>Falha ao verificar debito automatico.</b>');
	    	}
	
	    	if(pg_num_rows($result) == 1){
	
	    		$rsForc = pg_fetch_array($result);
	    		return ($rsForc['forcdebito_conta'] == 't') ? true : false;
	    	}
	
	    	return false;
    	
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    /**
     * Busca Autorizadora de Cartão de Crédito 
     */
    public function buscaAutorizadora($forcoid) 
    {
        try {
            $sql = "SELECT forcaccoid
                      FROM forma_cobranca
                     WHERE forcoid = $forcoid";
            
        $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora.txt', 'a+');
        fwrite($r, $sql);
        fclose($r);
/*            
            if ($this->conn != 2){
                  $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg.txt', 'a+');
            fwrite($r, 'conexao realizada com sucesso. Banco:'.$this->conn->dbname.' usuario:'.$this->conn->user.' host:'.$this->conn->host.' status:'.pg_transaction_status($this->conn) );
            fwrite($r, pg_errormessage($this->conn));
            fclose($r);

            $dbstring = "dbname=sascar_homologacao host=10.1.101.14 user=nobody";
            if($this->conn != 2){
                pg_close($this->conn);
                $conn2 = pg_connect($dbstring);
                
                $this->conn = $conn2;

                $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg_reconnect.txt', 'a+');
                fwrite($r, 'conexao recriada:'.$this->conn->dbname.' usuario:'.$this->conn->user.' host:'.$this->conn->host.' status:'.pg_transaction_status($this->conn) );
                fclose($r);
            }

            
            }else{
                  $r = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg.txt', 'a+');
            fwrite($r, 'conexao realizada com sucesso. Banco:'.$this->conn->dbname.' usuario:'.$this->conn->user.' host:'.$this->conn->host.' status:'.pg_transaction_status($this->conn) );
            fclose($r);
                
            }
*/
            
//   paulopinto:         $result44 = pg_query($conn2, $sql);
//            $rs = pg_fetch_array($result44);
/*             
            $re = fopen(_SITEDIR_.'arq_financeiro/buscaAutorizadora_msg_query.txt', 'a+');
            $retxt  = 'teste de query forcaccoid:'.$rs['forcaccoid']."\r\n";
            $retxt .= ' Banco:'.$dbstring."\r\n";
            $retxt .= 'erro $conn:'. pg_last_error($conn)."\r\n";
            $retxt .= 'erro $conn:'. pg_last_error($conn)."\r\n";
            $retxt .= 'erro $conn:'.pg_errormessage($conn)."\r\n";
            $retxt .= ' - pg_transaction_status $conn:'.pg_transaction_status($conn)."\r\n";
            $retxt .= 'erro $conn:'. pg_last_error($this->conn)."\r\n";
            $retxt .= ' - pg_transaction_status:'.pg_transaction_status($this->conn)."\r\n";
            $retxt .= 'erro $this->conn:'.pg_errormessage($this->conn)."\r\n";
            fwrite($re,$retxt);
            fclose($re);
  */      
            //if (!$result = pg_query($this->conn, $sql)) {
            if (!$result = pg_query($this->conn, $sql)) {
            	throw new Exception('ERRO:<b>Falha ao retornar autorizadora****.</b>--->pg_errormessage:'.pg_errormessage($this->conn).' pg_last_error:'. pg_last_error().'pg_result_error:'.pg_result_error($result) );
            }
            
            if(pg_num_rows($result) == 1){
            
                $rsForc = pg_fetch_array($result);
                
                return $rsForc['forcaccoid'];
            }
            
            return false;
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    
    /**
     * Busca todos os títulos pendentes de pagamento que terão suas datas alteradas
     */
    public function buscaTitulosAlterar($dadosConfirma)
    {

        try {
            // filtra à partir do dia 1º do próximo mês
			// Codigo comentado. No ultimo dia do ems estava pegando dois meses para frente. Verificação passada para dentro do SELECT
            //$dataVencimento = ">= '". date('01/m/Y', strtotime("+1 month"))."' ";
        	
            $sql =" SELECT titoid, titdt_vencimento
                      FROM titulo
                      JOIN forma_cobranca ON forcoid = titformacobranca
                     WHERE titclioid = $dadosConfirma->id_cliente
                       AND titdt_cancelamento IS NULL 
                       AND titobs_cancelamento IS NULL 
					   AND titdt_vencimento >= date_trunc('month',current_date) + INTERVAL'1 month'
                       AND forccobranca = TRUE ";

            if (!$result = pg_query($this->conn, $sql)) {
            	throw new Exception('ERRO: <b>Falha ao retornar titulos para alteracao de data.</b>');
            }
            
            if(pg_num_rows($result) > 0){
                $i=0;
                while($rsTits = pg_fetch_array($result)) {
                    $titAlterar[$i]['titoid'] = $rsTits['titoid'];
                    $titAlterar[$i]['titven'] = $rsTits['titdt_vencimento'];
                    $i++;
                }
                
                return $titAlterar;
            }
            
            return false;
                       
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }
    
    /**
     * Efetua as alterações do título em aberto 
     * 
     * @param int     $titoid
     * @param date    $dt_vencimento_nova
     * @param object  $dadosConfirma
     * @param boolean $permissaoAlteraDataVencimento
     * @throws Exception
     * @return boolean
     */
    public function alterarTitulos($titoid, $dt_vencimento_nova, $dadosConfirma, $permissaoAlteraDataVencimento)
    {
        try{
        	
        	$banco = (!empty($dadosConfirma->debitoBanco)) ? $dadosConfirma->debitoBanco : 'null';
        	$conta_corrente = (!empty($dadosConfirma->debitoConta)) ? $dadosConfirma->debitoConta : 'null';
        	
        	//atualiza os títulos 
        	$sqlTituloVenda =" UPDATE titulo_venda
	        			         SET titformacobranca    = ".$dadosConfirma->forma_cobranca_posterior.",";

        	//verifica se o usuário tem permissão para alterar a data de vencimento do título e se 
            // o checkbox -> Alterar a data de vencimento dos títulos em aberto? foi marcado para alterar
            if(($permissaoAlteraDataVencimento && $dadosConfirma->dataVencimentoPermissao === true)){
                $sqlTituloVenda .= "titdt_vencimento    = '".$dt_vencimento_nova."',";
            }
	        				   
		        $sqlTituloVenda .="  titusuoid_alteracao = ".$dadosConfirma->id_usuario." ,
		        				     titcfbbanco = $banco,
		        				     titconta_corrente = $conta_corrente
		        			   WHERE titdt_credito IS NULL
		        			   AND	(titnumero_registro_banco IS NULL OR (titemissao IS NULL AND titno_remessa IS NULL))
		        			   AND titoid = ".$titoid ;
        	
        	if (!pg_query($this->conn, $sqlTituloVenda)) {
        		throw new Exception('ERRO: <b>Falha ao alterar titulos venda.</b>');
            }
        	
        	$sqlTitulo =" UPDATE titulo
	        			   SET titformacobranca    = ".$dadosConfirma->forma_cobranca_posterior.",";
        	
        	//verifica se o usuário tem permissão para alterar a data de vencimento do título e se 
            // o checkbox -> Alterar a data de vencimento dos títulos em aberto? foi marcado para alterar
        	if(($permissaoAlteraDataVencimento && $dadosConfirma->dataVencimentoPermissao === true)){
                $sqlTitulo .= "titdt_vencimento    = '".$dt_vencimento_nova."',";
            }     
                                  
	        $sqlTitulo .= "    titusuoid_alteracao = ".$dadosConfirma->id_usuario." ,
	        				   titcfbbanco = $banco,
	        				   titconta_corrente = $conta_corrente
	        			WHERE titdt_credito IS NULL
	        			AND	(titnumero_registro_banco IS NULL OR (titemissao IS NULL AND titno_remessa IS NULL))
	        			AND titoid = ".$titoid ;
            if (!pg_query($this->conn, $sqlTitulo)) {
            	throw new Exception('ERRO: <b>Falha ao alterar titulos.</b>');
            }
            
            return true;
            
        }catch (Exception $e) {
            return $e->getMessage();  
        } 
    }
    
    /**
     * Calcula próxima data de pagamento para alterar nos títulos
     */
    public function retornaDataVencimento($dt_venc, $idDataVencimento)
    {
    	$prnDadosCobranca = new PrnDadosCobranca();
    	
        $dias_vcto = $prnDadosCobranca->getDiaCobranca();
        
        foreach($dias_vcto as $dia) {
            if($dia['codigo'] == $idDataVencimento)
                $novoDia = $dia['dia_pagamento'];
        }
        
        list($vAno,$vMes,$vDia) = explode('-', $dt_venc);
        
        return date('d/m/Y',mktime(0,0,0,$vMes,$novoDia,$vAno));
    }
    
    
    /**
    * Busca os dados do usuario pelo id
    *
    */
    public function getDadosUsuario($id_usuario) {

    	try{
	    	$sql = "SELECT nm_usuario as nome_usuario
			    	FROM usuarios
			    	WHERE cd_usuario = $id_usuario ";
	    	
	    	if (!$rs = pg_query($this->conn, $sql)) {
	    		throw new Exception('ERRO: <b>Falha ao buscar dados do usuario.</b>');
	    	}
	
	    	return pg_fetch_object($rs);
    	
    	}catch (Exception $e){
    		return $e->getMessage();
    	}
    }
    
    
    /**
    * Busca os dados do banco (nome = Itau, Bradesco, etc)
    *
    */
    public function getDadosBanco($banco = null) {

    	try{
	    	if ($banco) {
	    		$where = " WHERE bancodigo = $banco ";
	    	}
	
	    	$sql = "SELECT
				    	bancodigo,
				    	bannome as nome_banco
			    	FROM banco
			    	$where
			    	ORDER BY nome_banco ";
	
	    	if (!$rs = pg_query($this->conn, $sql)) {
	    		throw new Exception('ERRO: <b>Falha ao buscar dados do banco.</b>');
	    	}
	
	    	$cont = 0;
	    	while ($rbanco = pg_fetch_assoc($rs)) {
	
	    		$resultado[$cont]['bancodigo'] = $rbanco['bancodigo'];
	    		$resultado[$cont]['nome_banco'] = utf8_encode($rbanco['nome_banco']);
	    		$cont++;
	    	}
	
	    	return $resultado;
	    	
    	}catch (Exception $e){
    		return $e->getMessage();
    	}
    }
    
    /**
    * Atualiza dados das propostas, relacionadas aos contratos ativos do cliente
    *
    */
    public function atualizarPropostas($historicoProposta, $dadosConfirma, $agencia, $conta_corrente) {
    	 
    	try{
	    	$sql = "UPDATE
			    		proposta
			    	SET
				    	prpforcoid 		   = $dadosConfirma->forma_cobranca_posterior,
				    	prpdia_vcto_boleto = $dadosConfirma->diaVencimentoProposta,
				    	prpdebito_agencia  = $agencia,
				    	prpdebito_cc 	   = $conta_corrente
			    	WHERE
			    	prptermo IN( SELECT
						    	 	connumero
						    	 FROM contrato
						    	 INNER JOIN proposta ON prptermo = connumero
						    	 WHERE	concsioid = 1
						    	 AND conclioid = $dadosConfirma->id_cliente ) ";
	    	
	    	if (!pg_query($this->conn, $sql)) {
	    		throw new Exception('ERRO: <b>Falha ao atualizar propostas.</b>');
	    	}
	    	
	    	//insere histórico de alteração de proposta
	    	$sqlHistorico = " INSERT INTO 
	    						proposta_historico (prphprpoid, prphusuoid, prphobs)
							  				 SELECT prpoid ,$dadosConfirma->id_usuario , '$historicoProposta->texto_alteracao'
							 				 FROM contrato
							  				 INNER JOIN proposta ON prptermo = connumero
							  				 WHERE	concsioid = 1
							  				 AND conclioid = $dadosConfirma->id_cliente ";
	    	
	    	if (!pg_query($this->conn, $sqlHistorico)) {
	    		throw new Exception('ERRO: <b>Falha ao inserir historico de alteracao da proposta.</b>');
	    	}
	    	
	    	return true;
    	
    	}catch (Exception $e){
    		return $e->getMessage();
    	}
    }
    

    /**
     * Atualizar dados das propostas de pagamento relacionadas as propostas e aos contratos ativos do cliente
     *
     */
     public function atualizarPropostasPagamento($dadosConfirma, $banco, $agencia, $conta_corrente) {
    
     	try{
  	    
	    	$sql = "UPDATE
			    		proposta_pagamento
			    	SET
				    	ppagforcoid 	   = $dadosConfirma->forma_cobranca_posterior,
				    	ppagbancodigo	   = $banco,
				    	ppagdebito_agencia = $agencia,
				    	ppagdebito_cc 	   = $conta_corrente
			    	WHERE
				    	ppagprpoid in (	SELECT
									    	prpoid
								    	FROM proposta
								    	INNER JOIN contrato ON prptermo = connumero
								    	WHERE concsioid = 1
								    	AND conclioid = $dadosConfirma->id_cliente	) ";
	    		
	    	if (!pg_query($this->conn, $sql)) {
	    		throw new Exception('ERRO: <b>Falha ao atualizar proposta pagamento</b>');
	    	}
	    	
	    	return true;
	    	
     	}catch (Exception $e){
    		return $e->getMessage();
    	}
    }
    
    /**
     * Atualiza dados dos contratos de pagamentos relacionados ao cliente
     *
     */
    public function atualizarContratosPagamento($dadosConfirma, $banco, $agencia, $conta_corrente) {

    	try{
	    	$sql = "UPDATE
			    		contrato_pagamento
			    	SET
				    	cpagforcoid 		= $dadosConfirma->forma_cobranca_posterior,
				    	cpagusuoid			= $dadosConfirma->id_usuario,
				    	cpagbancodigo 		= $banco,
				    	cpagdebito_agencia  = $agencia,
				    	cpagdebito_cc 		= $conta_corrente,
				    	cpagobs             = '".utf8_decode($dadosConfirma->obsContratoPagamento)."'
			    	WHERE
			    	cpagconoid IN( SELECT connumero
							       FROM contrato
							       WHERE concsioid = 1
							       AND conclioid = $dadosConfirma->id_cliente ) ";
	    	 
	    	if (!pg_query($this->conn, $sql)) {
	    		throw new Exception('ERRO: <b>Falha ao atualizar contrato pagamento.</b>');
	    	}
	    	
	    	return true;
	    	
    	}catch (Exception $e){
    		return $e->getMessage();
    	}
    }
    
    public function atualizarDadosPagamentoContrato($dados) {
    	
    	try{
    		
    		$sql = "UPDATE
		    			contrato_pagamento
		    		SET
		    			cpaganuidade 			= $dados->anuidade,
						cpagrenovacao 			= $dados->renovacao,
						cpagvalvula 			= $dados->valvula,
						cpagsleep 				= $dados->sleep,
						cpagconversor 			= $dados->conversor,
						cpagmonitoramento 		= $dados->monitoramento,
						cpagtransf_titularidade = '$dados->transf_titularidade',
						cpaghabilitacao 		= $dados->habilitacao,
						cpagnum_parcela			= $dados->numero_parcela
		    		WHERE
		    			cpagconoid = $dados->contrato";
    		 
    		if (!pg_query($this->conn, $sql)) {
    			throw new Exception('ERRO: <b>Falha ao atualizar dados de pagamento referente ao contrato.</b>');
    		}
    	
    		return true;
    	
    	}catch (Exception $e){
    		return false;
    	}
    	
    }

    
    /**
     *  Busca Cobranças Promocionais vigentes
     * @return array
     */
    public function buscarCobrancasPromocionais($tipo_forma){
    
    $resultadoPesquisa = array();
        
    $where = "";
    
    //Verificar forma cliente 
    if ($tipo_forma == 'debito_cartao'){
        $where .= " AND cfmctipo NOT IN(4,5) ";
    }
    
    
	    $sql = "SELECT
					to_char(cfcpdt_inicio_vigencia, 'DD/MM/YYYY') as cfcpdt_inicio_vigencia,
					to_char(cfcpdt_fim_vigencia, 'DD/MM/YYYY') as cfcpdt_fim_vigencia,
					cftpdescricao,
					cfmcdescricao,
					CASE 
						WHEN cfcptipo_desconto = 'P'
						     THEN  'Percentual'
						WHEN cfcptipo_desconto = 'V'     
						     THEN 'Valor'  
					END  AS tipo_desconto,				
					cfcpdesconto,				
					CASE 
					WHEN cfcpaplicacao = 'P'
					     THEN  'Parcela'					
					ELSE 'Integral'       
					END  AS forma_aplicacao,				
					cfcpqtde_parcelas,
					nm_usuario
				FROM
					credito_futuro_campanha_promocional
					INNER JOIN credito_futuro_tipo_campanha ON cfcpcftpoid= cftpoid	
					INNER JOIN credito_futuro_motivo_credito ON cfcpcfmccoid= cfmcoid
					INNER JOIN usuarios ON cfcpusuoid_inclusao= cd_usuario
				WHERE
				  now() BETWEEN (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp AND (to_char(cfcpdt_fim_vigencia, 'YYYY-MM-DD')||' 23:59:59')::timestamp
                  " . $where . "
				  AND cfcpdt_exclusao is null
	    		ORDER BY 
	    				credito_futuro_campanha_promocional.cfcpdt_inicio_vigencia,
	    				credito_futuro_campanha_promocional.cfcpdt_fim_vigencia";

      	if ($resultado = pg_query($sql)) {
    		if (pg_num_rows($resultado) > 0) {
    			while ($objeto = pg_fetch_object($resultado)) {
    				$resultadoPesquisa[] = $objeto;
    			}
    		}
    	}
    	return $resultadoPesquisa;
    }
    
    /**
     *  Busca Cobranças Promocionais vigentes
     * @return array
     */
    public function buscarTotalCampanhasVigentes(){
    
	    $sql = "SELECT
					COUNT(1) AS total
				FROM
					credito_futuro_campanha_promocional
					INNER JOIN credito_futuro_tipo_campanha ON cfcpcftpoid= cftpoid	
					INNER JOIN credito_futuro_motivo_credito ON cfcpcfmccoid= cfmcoid
					INNER JOIN usuarios ON cfcpusuoid_inclusao= cd_usuario
				WHERE
				  now() BETWEEN (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp AND (to_char(cfcpdt_fim_vigencia, 'YYYY-MM-DD')||' 23:59:59')::timestamp
				  AND cfcpdt_exclusao is null
	    		";

      	if ($resultado = pg_query($sql)) {
    		if (pg_num_rows($resultado) > 0) {
                $obj = pg_fetch_object($resultado,0);
                return $obj->total;
    		}
    	}
    	return 0;
    }

    /**
     * Busca campanha vigente para este tipo de crédito
     *
     * @param object $formaCobranca Objeto com os dados da forma de cobranca
     *
     * @return object Campanha vigente ou falso se não houver
     */
    public function buscarCampanhaVigente($tipoCredito){

        $sql = "
            SELECT
                *,
                (SELECT cftpdescricao FROM credito_futuro_tipo_campanha WHERE cftpoid = cfcpcftpoid) as descricao_tipo_campanha
            FROM
                credito_futuro_campanha_promocional
            WHERE
                cfcpdt_exclusao IS NULL
                AND date(NOW()) BETWEEN (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp AND (to_char(cfcpdt_fim_vigencia, 'YYYY-MM-DD')||' 23:59:59')::timestamp
                AND cfcpcfmccoid in (SELECT cfmcoid FROM credito_futuro_motivo_credito WHERE cfmctipo = " . $tipoCredito . ")
            ORDER BY
                cfcpdt_inclusao
            LIMIT 1";

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_object($resultado);
        } else {
            return false;
        }
    }

    /**
     * Verifica se cliente teve algum crédito com as mesmas configurações a menos de seis meses
     *
     * @param int $idCliente Id do cliente
     *
     * @return boolean true se o cliente teve crédito futuro com data de inclusão a menos de seis meses,
     * com registro de movimentação.
     */
    public function verificarLimiteSeisMeses($idCliente) {

        $sql = "
            SELECT
                true
            FROM
                credito_futuro
                INNER JOIN credito_futuro_movimento ON cfooid = cfmcfooid
            WHERE
                cfoclioid = " . $idCliente . "
                AND AGE( cfodt_inclusao, NOW()) > interval '-6 months'
                AND cfocfmcoid in ( SELECT cfmcoid FROM credito_futuro_motivo_credito WHERE cfmctipo in (4,5) )
                AND cfoforma_inclusao = 2
                AND cfmdt_exclusao IS NULL";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Busca dados da última forma de pagamento do cliente
     *
     * @param int $idCliente id do Cliente que está alterando a forma de pagamento
     *
     * @return object Dados da última forma de pagamento do cliente
     */
    public function buscarUltimaFormaPagamento($idCliente) {

        $sql = "
            SELECT
                forma_cobranca.*
            FROM
                titulo
                INNER JOIN nota_fiscal ON ( titnfloid = nfloid
                AND nflserie = 'A'
                AND nfldt_cancelamento IS NULL
                AND nflnatureza like 'PRESTA__O DE SERVI_OS')
                INNER JOIN forma_cobranca ON ( titformacobranca = forcoid )
            WHERE
                titdt_cancelamento IS NULL
                AND titdt_pagamento IS NOT NULL
                AND titclioid = " . $idCliente . "
            ORDER BY
                titdt_inclusao DESC
            LIMIT
                1";

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) == 1) {
            $formaCobranca = pg_fetch_object($resultado);

            if($formaCobranca->forcdebito_conta == 't') {
                $formaCobranca->tipoCredito = 4;
            } else if ($formaCobranca->forccobranca_cartao_credito == 't') {
                $formaCobranca->tipoCredito = 5;
            } else {
                $formaCobranca->tipoCredito = 0;
            }
            return $formaCobranca;
        } else {
            $formaCobranca = new stdClass();
            $formaCobranca->tipoCredito = 0;
            return $formaCobranca;
        }
    }

    /**
     * Busca id do crédito futuro com os seguintes parametros:
     *  - ativo,
     *  - incluído automaticamente,
     *  - sem movimentação e
     *  - motivo do crédito = (débito automatico ou cartão de crédito)
     * do cliente passado como parametro
     *
     * @param int $idCliente Id do cliente que está alterando a forma de cobrança
     *
     * @return int Id do crédito futuro ativo e sem movimentação
     */
    public function buscarCreditoFuturoAtivoSemMovimentacao($idCliente) {

        $sql = "
            SELECT
                cfooid
            FROM
                credito_futuro
                INNER JOIN credito_futuro_motivo_credito ON cfocfmcoid = cfmcoid
            WHERE
                cfodt_exclusao IS NULL
        		AND cfodt_encerramento IS NULL
                AND cfoforma_inclusao = 2
                AND cfmctipo in (4,5)
                AND (SELECT COUNT(1) FROM credito_futuro_movimento WHERE cfmcfooid = cfooid AND cfmdt_exclusao IS NULL) = 0
                AND cfoclioid = " . $idCliente;

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) > 0) {
            $idCreditoFuturo = pg_fetch_result($resultado, 0, "cfooid");
            return $idCreditoFuturo;
        } else {
            return 0;
        }
    }

    /**
     * Retorna ID do usuário "AUTOMATICO"
     *
     * @return int Id do usuário automatico
     */
    public function buscarIdUsuarioAutomatico() {

        $sql = "
            SELECT
                cd_usuario
            FROM
                usuarios
            WHERE
                ds_login = 'AUTOMATICO'";

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_result($resultado, 0, "cd_usuario");
        } else {
            return 0;
        }
    }

    /**
     * Busca dados do motivo de crédito de determinada campanha
     *
     * @param int $idMotivoCreditoCampanha
     *
     * @return \CreditoFuturoCampanhaPromocionalVO Dados do motivo do crédito
     */
    public function buscarMotivoCredito($idMotivoCreditoCampanha) {

        $creditoFuturoMotivoCreditoVo = new CreditoFuturoMotivoCreditoVO();

        $sql = "SELECT
                    *
                FROM
                    credito_futuro_motivo_credito
                WHERE
                    cfmcoid = " . $idMotivoCreditoCampanha;

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) > 0) {
            $creditoFuturoMotivoCreditoVo->id = intval(pg_fetch_result($resultado, 0, "cfmcoid"));
            $creditoFuturoMotivoCreditoVo->tipo = intval(pg_fetch_result($resultado, 0, "cfmctipo"));
            $creditoFuturoMotivoCreditoVo->descricao = pg_fetch_result($resultado, 0, "cfmcdescricao");
        }

        return $creditoFuturoMotivoCreditoVo;

    }

    /**
     * Retorna dados da Forma de cobrança
     *
     * @param int $idFormaCobranca Id da forma de cobrança
     *
     * @return object Dados da forma de cobrança ou false
     */
    public function buscarDadosFormaCobranca($idFormaCobranca) {

        $sql = "
            SELECT
                *
            FROM
                forma_cobranca
            WHERE
                forcoid = " . $idFormaCobranca;

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) > 0) {
            $formaCobranca = pg_fetch_object($resultado);

            if($formaCobranca->forcdebito_conta == 't') {
                $formaCobranca->tipoCredito = 4;
            } else if($formaCobranca->forccobranca_cartao_credito == 't') {
                $formaCobranca->tipoCredito = 5;
            } else {
                $formaCobranca->tipoCredito = 0;
            }
            return $formaCobranca;
        } else {
            return false;
        }
    }

    
}    
//fim arquivo
?>