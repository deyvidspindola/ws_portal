<?php
/**
 * Classe responsável em remover o cartão de crédito do cliente via Webservice da SoftExpress
 *  
 * @file RemoverCartao.class.php
 * @author marcioferreira
 * @version 27/05/2013 11:02:58
 * @since 27/05/2013 11:02:58
 * @package SASCAR RemoverCartao.class.php
 */

// Report all PHP errors
//error_reporting(E_ALL);

require_once (_MODULEDIR_ . 'eSitef/Action/IntegracaoSoftExpress.class.php');
require_once (_MODULEDIR_ . 'eSitef/DAO/RemoverCartaoDAO.php');


class RemoverCartao{
	
	private $hashCartao;
	private $integracaoSoftExpress;
	private $tentativas;
	
	// Construtor
	public function __construct() {
	
		global $conn;
		
		$this->conn = $conn;
	
		// Objeto  - DAO
		$this->dao = new RemoverCartaoDAO($conn);

		// instância da classe de Integração com e-Sitef
		$this->integracaoSoftExpress = new IntegracaoSoftExpress();
		
		//quantidade de tentativas para cosultar no banco os dados recebidos do POST
		$this->tentativas = 6; // cada consulta aguarda 15 segundos, máximo de 90 segundos   15 * 6 = 90
	}
	
	
	/**
	 * Consulta os dados do cartão pelo id do cliente e envia os dados para o método beginRemoveStoredCard
	 * do Webservice SoftExpress
	 * 
	 * @param $clioid int
	 * @return $wsRetorno array
	 */
	public function processarRemocaoCartao($clioid, $transacaoID){
		
		try{
			
			if(empty($clioid)){
				throw new Exception('O id do cliente deve ser informado.');
			}
			
			if(empty($transacaoID)){
				throw new Exception('ID da transação deve ser informado.');
			}
			
			$dadosTransacao = new stdClass();
			
			$dadosTransacao->cliente = $clioid;
			
			//recupera o hash do cartão na tabela cliente_cobranca_credito
			$dadosHash = $this->dao->getDadosCartao($clioid, "");
			
			if(!empty($dadosHash)){

				$rs = pg_query($this->conn, "BEGIN;");
				
				//Inica o processo de remoção na SE
				//retorna uma mensagem de texto simples indicando o resultado status=OK para sucesso ou	 outra mensagem indicando o erro.
				$wsRetorno = $this->integracaoSoftExpress->beginRemoveStoredCard($dadosHash->ccchash, $dadosHash->ctcoid);
				
				if($wsRetorno->status != 'OK'){
						
					//como ainda não existe códigos de retorno de erros e todos os erros são retornados na atributo 'status'
					// então, é verificado pela string, nos casos abaixo o processo não é interrompido
					if(strstr($wsRetorno->status , 'Card not found' ) || strstr($wsRetorno->status, 'ERROR: Card already removed' )){

						//insere um log com o erro retornado da SE
						$atualizaTransacao = $this->dao->atualizarTransacaoCartao($transacaoID, $statusTransacao = 't', $dadosTransacao, $wsRetorno->status );
						
						return true;

					}else{
							
						throw new Exception('Falha ao solicitar remoção do cartão de crédito ->.'. $wsRetorno->status .'');
					}

				}else{
						
					//fez o POST na página recuperaTransacao.php
					
					//variáveis para controlar a consulta dos dados do POST gravados no BD
					$novaPesquisa = false;
					$consultas    = 0;
					
					do{
						//se não encontrou os dados recebidos do POST no banco dá mais um tempo para o sistema processar o POST
						if($novaPesquisa){
							sleep(15);
						}
						
						//consulta os dados persistidos em banco recebidos via POST para confirmar o cancelamento
						$dadosParaConfirmacao = $this->dao->getDadosConfirmacaoRemocaoCartao($dadosTransacao->cliente, $dadosHash->ctcoid, $status = 'NOV');
						
						if(is_object($dadosParaConfirmacao)){
							
							//envia dados para confirmar a remoção do cartão
							$retornoConfirmacao = $this->confirmarRemocaoCartao($transacaoID, $dadosHash->ctcoid, $dadosParaConfirmacao);
													
							if($retornoConfirmacao == 1){
																
								return true;

							}else{
									
								throw new Exception($retornoConfirmacao);
							}
							
						}else{

							$novaPesquisa = true;
							
							if($consultas == $this->tentativas){
								
								throw new Exception('Dados para confirmação de remoção não encontrados [POST]');
							}
							
							$consultas++;
						}

					}while ($novaPesquisa && $consultas <= $this->tentativas);
					
					$rs = pg_query($this->conn, "COMMIT;");
					
					return true;
					
				}
				
			}else{
				throw new Exception('Dados do cartão não foram encontrados para solicitar remoção');
			}
			
		}catch(Exception $e){

			//atualiza a transação
			$atualizaTransacao = $this->dao->atualizarTransacaoCartao($transacaoID, $statusTransacao = 'f', $dadosTransacao, $e->getMessage());
			
			$rs = pg_query($this->conn, "COMMIT;");
			
			return $e->getMessage();
		}
	}
	
	
	/**
	 * 
	 * Persiste os dados retornados via POST da SE para confirmar remoção de um cartão solicitado antes
	 * pelo método beginRemoveStoreCard
	 * 
	 * @param object $dadosRetornoPost
	 * @return boolean
	 */
	public function inserirDadosPostRemocaoCartao($dadosRetornoPost){
		
		try{
			
			if(!is_object($dadosRetornoPost)){
				throw new Exception('Dados do POST não pode ser vazio');
			}
			
			//se for do tipo cancelamento do cartão, grava os dados no BD
			if(isset($dadosRetornoPost->cancelamentoCartao)){
				
				if($dadosRetornoPost->cancelamentoCartao === 'true'){
	
					//para evitar erro de banco, pois há casos em que o cliente está retornando com valor =  ? (interrogação)
					if(is_numeric($dadosRetornoPost->cliente)){
	
						if($dadosRetornoPost->status === 'CON' || $dadosRetornoPost->status === 'NOV'){
							$statusTransacao = 't';
						}else{
							$statusTransacao = 'f';
						}
	
						//insere dados da SE recebidos no POST
						$retorno = $this->dao->inserirDadosPostRemocaoCartao($dadosRetornoPost, $statusTransacao);
							
						if($retorno){
							return true;
						}else{
							throw new Exception($retorno);
						}
					}
				}
			}
			
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
		
	/**
	 * Confirma a remoção do cartão após o envio os dados para SoftExpress que 
	 * retorna os dados de remocao via post
	 * 
	 * @param object $dadosTransacao
	 * @throws Exception
	 * @return boolean
	 */
	
	public function confirmarRemocaoCartao($transacaoID, $ctcoid, $dadosParaConfirmacao){

		try {
			
			
			if(!is_object($dadosParaConfirmacao)){
				throw new Exception('Faltam dados para processar confirmação de exclusão do cartão de crédito');
			}
			
			if(empty($dadosParaConfirmacao->nita)){
				throw new Exception('Nita não encontrado para processar confirmação de exclusão do cartão de crédito');
			}
			
			if(empty($dadosParaConfirmacao->cliente)){
				throw new Exception('Informe o id do cliente para confirmar a remoção do cartão de crédito');
			}
			
			if(empty($dadosParaConfirmacao->hash)){
				throw new Exception('Hash do cartão de crédito não encontrado');
			}
			
			//envia para SE a confirmação de remoção do cartão 
			$confirmaRemocao = $this->integracaoSoftExpress->doRemoveStoredCard($dadosParaConfirmacao->hash, $dadosParaConfirmacao->nita);
			
			if($confirmaRemocao->status != 'OK'){
				
				//como ainda não existe códigos de retorno de erros e todos os erros são retornados na atributo 'status'
				// então, é verificado pela string, nos casos abaixo o processo não é interrompido
				if(strstr($confirmaRemocao->status , 'Card not found' ) || strstr($confirmaRemocao->status, 'ERROR: Card already removed' )){
				
					//atualiza a transação
					$atualizaTransacao = $this->dao->atualizarTransacaoCartao($transacaoID, $statusTransacao = 't', $dadosParaConfirmacao, $confirmaRemocao->status );
					
					return true;
				
				}else{
				
					throw new Exception('Falha ao confirmar remoção do cartão de crédito -> '.$confirmaRemocao->status.' ');
				}
				
			}else{
				
				//invoca o método callStatus para verificar se o cartão foi removido
				$verificaRemocao = $this->integracaoSoftExpress->callStatus($dadosParaConfirmacao->nita);
				 	
				if($verificaRemocao->status != 'OK'){
			
					throw new Exception('Falha ao consultar Status do cartão de crédito -> '.$verificaRemocao->status.' ');
					
				}else{
					
					//variáveis para controlar a consulta dos dados do POST gravados no BD
					$novaPesquisa = false;
					$consultas    = 0;
						
					do{
						//se não encontrou os dados recebidos do POST no banco dá mais um tempo para o sistema processar o POST
						if($novaPesquisa){
							sleep(15);
						}
					
						//consulta os dados persistidos em banco recebidos via POST para confirmar o cancelamento
						$dadosConfirmacao = $this->dao->getDadosConfirmacaoRemocaoCartao($dadosParaConfirmacao->cliente, $ctcoid, $status = 'CON');
												
						if(is_object($dadosConfirmacao)){
							
							//atualiza a transação
							$dadosAtualiza = new stdClass();
							//passa somente o id do cliente requerido pelo método
							$dadosAtualiza->cliente = $dadosParaConfirmacao->cliente;
							
							$atualizaTransacao = $this->dao->atualizarTransacaoCartao($transacaoID, $statusTransacao = 't', $dadosAtualiza, NULL );
							
							return true;
								
						}else{
							
							$novaPesquisa = true;
								
							if($consultas == $this->tentativas){
					
								throw new Exception('Dados para retornar a confirmação de remoção não encontrados [POST]');
							}
								
							$consultas++;
						}
					
					}while ($novaPesquisa && $consultas <= $this->tentativas);
				
				}//fim do else $verificaRemocao->status != 'OK'
				
			}//fim do else $confirmaRemocao->status != 'OK'

		}catch (Exception $e){
			return  $e->getMessage();
		}
	}
	
	/**
	 * Inclui uma transação do tipo remoção de cartão de crédito
	 * 
	 * @param unknown $clioid
	 * @return string
	 */
	
	public function iniciarTransacaoRemocaoCartaoCredito($clioid){
		
		return $this->dao->incluirTransacaoCartao($clioid);
		
	}
	
	/**
	 * Atualiza informações das transações durante o processo de remoção de um cartão de crédito
	 * 
	 * @param int $transacaoID
	 * @param string $statusTransacao
	 * @param object $dadosTransacao
	 * @return boolean
	 */
	public function atualizarTransacaoRemocaoCartaoCredito($transacaoID, $statusTransacao, $dadosTransacao, $motivo = null){
		
		return $this->dao->atualizarTransacaoCartao($transacaoID, $statusTransacao, $dadosTransacao, $motivo);
		
	}
	
	
	
}

