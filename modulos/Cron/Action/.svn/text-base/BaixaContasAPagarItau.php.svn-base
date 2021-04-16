<?php

/**
 * Classe responsável por ler o 
 * arquivo de retorno recebido pelo 
 * ITAÚ UNIBANCO STCP OFTP Client, 
 * bem como realizar a baixa automática 
 * de títulos pagos e disponibilizar 
 * títulos rejeitados para tratamento.
 * 
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 13/01/2017
 * @category Class
 * @package BaixaContasAPagarItau
 */

require_once _MODULEDIR_ . 'Cron/DAO/BaixaContasAPagarItauDAO.php';

class BaixaContasAPagarItau {

    private $dao;
    private $ambiente;
	
	/**
	 * Realiza as ações de ...
	 * 
	 * @param
	 * @return boolean
	 */	
	public function baixarContasAPagarItau() {
		try{
			
			//Verifica se pasta específica contém arquivos para leitura
			$caminho 			= '';
			$arquivo 			= '';
			$caminhoArq			= '';
			$parametros 		= new stdClass;
			$email 				= new stdClass;
			$dadosHeaderArq		= new stdClass;
			$dadosDetalhe		= new stdClass; 
			$dadosStatus 		= new stdClass;
            $arquivosBaixados   = array(); 
            $msgException		= '';            
            
            //Atribui parametros FTP
            $parametrosFTP = new stdClass;

            $parametrosFTP->caminhoLocal = "/var/www/docs_temporario/";

            //Atribui parametros de busca
            $parametros = new stdClass;
            $parametros->codigoParametro = 'CONTAS_A_PAGAR';
            
            //Busca caminho da pasta retorno/ remessa Itaú
            $parametros->codigoItemParametro = 'PASTA_RETORNO_REMESSA_ITAU';
            if ($caminho = $this->dao->buscaParametros($parametros)) { 
                $parametrosFTP->destino = trim($caminho[0]->pcsidescricao);
            }

            //Busca caminho da pasta retorno/processado remessa Itaú
            $parametros->codigoItemParametro = 'PASTA_MOVIDOS_PROCESSADOS';
            if ($caminhoProcessado = $this->dao->buscaParametros($parametros)) { 
                $parametrosFTP->caminhoProcessado = trim($parametrosFTP->destino.substr($caminhoProcessado[0]->pcsidescricao,1) );
            }
            
            //Busca servidor da pasta retorno/ remessa Itaú
            $parametros->codigoItemParametro = 'SERVIDOR_STCP_'.$this->ambiente;
            if ($servidor = $this->dao->buscaParametros($parametros)) { 
                $parametrosFTP->servidor = trim($servidor[0]->pcsidescricao);
            }

            //Busca usuario FTP
            $parametros->codigoItemParametro = 'USUARIO_STCP_'.$this->ambiente;
            if ($usuario = $this->dao->buscaParametros($parametros)) { 
                $parametrosFTP->usuario = trim($usuario[0]->pcsidescricao);
            }

            //Busca senha FTP
            $parametros->codigoItemParametro = 'PASSWORD_STCP_'.$this->ambiente;
            if ($senha = $this->dao->buscaParametros($parametros)) { 
                $parametrosFTP->senha = trim($senha[0]->pcsidescricao);
            }

            $arquivosRetorno = $this->buscarArquivoFTP($parametrosFTP);

			
			//Arquivo(s) encontrado(s)
			if ( is_array($arquivosRetorno) && count($arquivosRetorno) > 0 ) {
				foreach ($arquivosRetorno as $keyFile => $file) {

					//Abre arquivo p/ leitura
                	$linhas = fopen($parametrosFTP->caminhoLocal.$file, "r"); 

                    $parametrosFTP->nomeArquivo = $file;

                	if ($linhas) {                		
						//Atribui variáveis
						$arrayTitulos_nao_vinculados = array();
						$arrayDtPgto 		= array();
						$arrayOcorrencia 	= array();
						$numeroLinha 		= 0;
						$ocorrencia 		= '';
						//$processada 		= 'f';
						$noRemessa 			= '';
						$numeroRemessa 		= null; 
						$tipoRegistro 		= null;
						$bancoPagador 		= null;
						$codigoStatus 		= null;
						$codigoOcorrencia 	= null;
						
						//Percorre o arquivo linha a linha
						while (!feof($linhas)) { 

                            //Inicia transação
                            $this->dao->begin();
							
							$this->arrErros = array();
							//Armazena o conteúdo da linha
							$linha = fgets($linhas); 

							$tipoRegistro = intval(trim(substr($linha, 7, 1))); //TIPO DE REGISTRO
							
							//Busca informações do cabeçalho
							if ($tipoRegistro == 0) {
								$dadosHeaderArq->agencia 		= trim(intval(substr($linha, 52, 5))); //AGÊNCIA
								$dadosHeaderArq->contaCorrente 	= trim(intval(substr($linha, 58, 12))."-".intval(substr($linha, 71, 1))); //CONTA-DAC        
							}

							//Lê somente o lote que contém ocorrências;
							if ($numeroLinha > 1 && $tipoRegistro == 3) {
								$ocorrencia = trim(substr($linha, 230, 10));
								
								if ($ocorrencia == '') {
									$this->arrErros = 'erroArquivo';
								} 
							} 
								
							if (count($this->arrErros) == 0 && $numeroLinha > 1 && $tipoRegistro == 3) {
								//Identifica o segmento
								$dadosDetalhe->segmento = substr($linha, 13, 1);
								$nome_fornecedor = "";
								
								//Pagamentos através de cheque, OP, DOC, TED e crédito em conta corrente
								//Pagamentos através de Nota Fiscal – Liquidação Eletrônica
								if ($dadosDetalhe->segmento == 'A') {
									$dadosDetalhe->numeroTitulo 	= substr($linha, 73, 20); //SEU NÚMERO 
									$dadosDetalhe->nossoNumero 		= substr($linha, 134, 15); //NOSSO NÚMERO 
									$arrayDtPgto 					= str_split(substr($linha, 154, 8), 2); //DATA EFETIVA 
									$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
									$dadosDetalhe->valorRealPago 	= substr($linha, 162, 13).','.substr($linha, 175, 2); //VALOR EFETIVO 
									$arrayOcorrencia				= str_split(trim(substr($linha, 230, 10)), 2); //OCORRÊNCIAS 
									$nome_fornecedor 				= substr($linha, 43, 29);
								}
								//Liquidação de títulos (boletos) em cobrança no Itaú e em outros Bancos
								elseif ($dadosDetalhe->segmento == 'J') {
									$dadosDetalhe->numeroTitulo 	= substr($linha, 182, 20); //SEU NÚMERO 
									$dadosDetalhe->nossoNumero 		= substr($linha, 215, 15); //NOSSO NÚMERO  
									$arrayDtPgto 					= str_split(substr($linha, 144, 8), 2); //DATA PAGAMENTO 
									$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA  
									$dadosDetalhe->valorRealPago 	= substr($linha, 152, 13).'.'.substr($linha, 165, 2); //VALOR PAGAMENTO 
									$arrayOcorrencia				= str_split(trim(substr($linha, 230, 10)), 2); //OCORRÊNCIAS 
									$nome_fornecedor 				= substr($linha, 61, 29);
								}
								//Pagamento de Contas de Concessionárias e Tributos com código de barras
								elseif ($dadosDetalhe->segmento == 'O') {
									$dadosDetalhe->numeroTitulo 	= substr($linha, 174, 20); //SEU NÚMERO 
									$dadosDetalhe->nossoNumero 		= substr($linha, 215, 15); //NOSSO NÚMERO 
									$arrayDtPgto 					= str_split(substr($linha, 136, 8), 2); //DATA PAGAMENTO 
									$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
									$dadosDetalhe->valorRealPago 	= substr($linha, 144, 13).','.substr($linha, 157, 2); //VALOR PAGO 
									$arrayOcorrencia				= str_split(trim(substr($linha, 230, 10)), 2); //OCORRÊNCIAS
									$nome_fornecedor 				= substr($linha, 65, 29); 
								}
								//Pagamento de Tributos sem código de barras e FGTS-GRF/GRRF/GRDE com código de barras
								elseif ($dadosDetalhe->segmento == 'N') {
									$dadosDetalhe->numeroTitulo 	= substr($linha, 195, 20); //SEU NÚMERO 
									$dadosDetalhe->nossoNumero 		= substr($linha, 215, 15); //NOSSO NÚMERO
									$dadosDetalhe->codigoTributo 	= substr($linha, 17, 2); //TRIBUTO
									$nome_fornecedor 				= substr($linha, 165, 29);
									//GPS
									if ($dadosDetalhe->codigoTributo == 01) {
										$arrayDtPgto 					= str_split(substr($linha, 99, 8), 2); //DATA ARRECADAÇÃO 
										$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
										$dadosDetalhe->valorRealPago 	= substr($linha, 85, 12).','.substr($linha, 97, 2); //VALOR ARRECADADO 
									}
									//DARF //DARF SIMP
									elseif ($dadosDetalhe->codigoTributo == 02 || $dadosDetalhe->codigoTributo == 03) {
										$arrayDtPgto 					= str_split(substr($linha, 127, 8), 2); //DATA PAGAMENTO 
										$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
										$dadosDetalhe->valorRealPago 	= substr($linha, 105, 12).','.substr($linha, 117, 2); //VALOR TOTAL 
									}
									//ICMS
									elseif ($dadosDetalhe->codigoTributo == 05) {
										$arrayDtPgto 					= str_split(substr($linha, 146, 8), 2); //DATA PAGAMENTO 
										$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
										$dadosDetalhe->valorRealPago 	= substr($linha, 124, 12).','.substr($linha, 136, 2); //VALOR PAGAMENTO 
									}
									//IPVA //DPVAT
									elseif ($dadosDetalhe->codigoTributo == 07 || $dadosDetalhe->codigoTributo == 08) {
										$arrayDtPgto 					= str_split(substr($linha, 116, 8), 2); //DATA PAGAMENTO 
										$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
										$dadosDetalhe->valorRealPago 	= substr($linha, 94, 12).','.substr($linha, 106, 2); //VALOR PAGAMENTO 
									}
									//FGTS
									elseif ($dadosDetalhe->codigoTributo == 11) {
										$nome_fornecedor 				= substr($linha, 113, 29);
										$arrayDtPgto 					= str_split(substr($linha, 143, 8), 2); //DATA PAGAMENTO 
										$dadosDetalhe->dtEfetivaPgto 	= $arrayDtPgto[0].'/'.$arrayDtPgto[1].'/'.$arrayDtPgto[2].$arrayDtPgto[3]; //DDMMAAAA 
										$dadosDetalhe->valorRealPago 	= substr($linha, 151, 12).','.substr($linha, 163, 2); //VALOR PAGAMENTO 
									}
									else{
										$dadosDetalhe->dtEfetivaPgto 	= null; //??? 
										$dadosDetalhe->valorRealPago 	= 0.00; //??? 
									} 
									$arrayOcorrencia 				= str_split(trim(substr($linha, 230, 10)), 2); //OCORRÊNCIAS 
								}
								
								$dadosDetalhe->numeroTitulo = intval($dadosDetalhe->numeroTitulo);
								if(!$this->dao->isRemessaVinculadaTituloAPagar($dadosDetalhe->numeroTitulo)){
									$forma_pagamento = $dadosDetalhe->formaPgto;
									
									$arrayTitulos_nao_vinculados[$dadosDetalhe->numeroTitulo] = array(
										"nome_fornecedor" => $nome_fornecedor,
										"valor" => ltrim($dadosDetalhe->valorRealPago, 0),
										"numero_titulo" => $dadosDetalhe->numeroTitulo,
										"data_agdo_pagto" => $dadosDetalhe->dtEfetivaPgto,
									);
								}else{
									$dadosHeaderArq->apgoid = $dadosDetalhe->numeroTitulo;

									//Recupera o banco pagador
									$bancoPagador = $this->dao->buscaBancoPagador($dadosHeaderArq);
									if (!$bancoPagador || count($bancoPagador) == 0) { 
										$msgException.= 'Código do banco pagador não recuperado. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
										$this->dao->rollback();
										continue;
									}
									else{
										//Atribui o código do banco pagador
										$dadosDetalhe->bancoPagador = intval($bancoPagador[0]->abbancodigo);
										
									}
									
									//Recupera o número da remessa
									$noRemessa = $this->dao->buscaNoRemessa($dadosHeaderArq);
									if (!$noRemessa || count($noRemessa) == 0) {
										$msgException.= 'Número de remessa não recuperado. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
										$this->dao->rollback();
										continue;
									}
									else{
										//Atribui o número da remessa
										$numeroRemessa = intval($noRemessa[0]->apgno_remessa);

									}

									//Verifica se já houve movimentação bancaria para este título
									$movimentcaoBancaria = $this->dao->verificaMovimentacaoBancaria($dadosHeaderArq->apgoid);
									if ($movimentcaoBancaria == 't') {
										$msgException.= 'O título já possui movimentação bancária. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
										$this->dao->rollback();
										continue;
									}

									//Título pago com sucesso 
									if ($arrayOcorrencia[0] == '00') {
										//Recupera a forma de pagamento
										$formaPagamento = $this->dao->buscaFormaPagamento($dadosHeaderArq);
										if (!$formaPagamento || count($formaPagamento) == 0) {
											$msgException.= 'Forma de pagamento não recuperada. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
											$this->dao->rollback();
											continue;
										}
										else{
											//Atribui o código da forma de pagamento 
											$dadosDetalhe->formaPgto = intval($formaPagamento[0]->apgforcoid);

											//Efetua a baixa do título 
											if (!$this->dao->efetuaBaixaTitulo($dadosDetalhe)) { 
												$msgException.= 'Não foi possível efetuar baixa do título. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
												$this->dao->rollback();
												continue;
											}
										}
										//Atribui variáveis
										$dadosStatus->codigoBanco 		= $dadosDetalhe->bancoPagador;
										$dadosStatus->tipo 				= 'Retorno';
										$dadosStatus->codStatus 		= 31; //Pago Itaú
										$dadosStatus->baixaAutomatica 	= 't';
										$dadosStatus->apgnosso_numero 	= $dadosDetalhe->nossoNumero;
										$dadosStatus->apgoid 			= $dadosDetalhe->numeroTitulo;
									}
									//Título com problema
									else{
										//Atribui variáveis
										$dadosStatus->codigoBanco 		= $dadosDetalhe->bancoPagador;
										$dadosStatus->tipo 				= 'Retorno';
										$dadosStatus->codStatus 		= 41; //Aguardando Tratativa Itaú
										$dadosStatus->baixaAutomatica 	= 'f';
										$dadosStatus->apgnosso_numero 	= 'null';
										$dadosStatus->apgoid 			= $dadosDetalhe->numeroTitulo;
									}
										
									//Busca código do status
									$codigoStatus = $this->dao->buscaCodigoStatus($dadosStatus);
									if (!$codigoStatus || count($codigoStatus) == 0) {
										$msgException.= 'Código do status não recuperado. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
										$this->dao->rollback();
										continue;
									}
									else{
										//Atribui o código do status 
										$dadosStatus->apgsoid = intval($codigoStatus[0]->apgsoid);
										
										//Atualiza o status do título
										if ($this->dao->atualizaStatusTitulo($dadosStatus) == 'f') {
											$msgException.= 'Não foi possível atualizar o status do título. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
											$this->dao->rollback();
											continue;
										}
									}
										
									foreach ($arrayOcorrencia as $value) {
										$dadosDetalhe->codigoOcorrencia = $value;
									
										//Busca código da ocorrência 
										$codigoOcorrencia = $this->dao->buscaCodigoOcorrencia($dadosDetalhe->codigoOcorrencia);
										if (!$codigoStatus || count($codigoStatus) == 0) {
											$msgException.= 'Código da ocorrência não recuperado. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
											$this->dao->rollback();
											continue;
										}
										else{
											//Atribui o código do ocorrência 
											$dadosStatus->apgooid = intval($codigoOcorrencia[0]->apgooid);
											
											//Insere histórico de contas a pagar
											if ($this->dao->inserirHistoricoAPagar($dadosStatus) == 'f') {
												$msgException.= 'Não foi possível inserir histórico de contas a pagar. Cód.Título->'.$dadosHeaderArq->apgoid.'.'.PHP_EOL;
												$this->dao->rollback();
												continue;
											} 

										} 
									}
										
									if (trim($numeroRemessa) != '') {
										
										//Atribui variáveis
										$dadosStatus->codigoBanco 		= $dadosDetalhe->bancoPagador;
										$dadosStatus->tipo 				= 'Retorno';
										$dadosStatus->codStatus 		= '31,41' ; //Pago Itaú e Aguardando Tratativa Itaú
										
										//Busca códigos de status de títulos processsados 
										$arrayStatus = $this->dao->buscaCodigoStatus($dadosStatus);

										if (!$arrayStatus || count($arrayStatus) == 0) {
											$msgException.= 'Códigos de status processados não recuperado. No.Remessa->'.$numeroRemessa.'.'.PHP_EOL;
											$this->dao->rollback();
											continue;
										}
										else{
											//Atribui objeto em string separada por vírgulas
											foreach ($arrayStatus as $value){
												$listaStatus.= $value->apgsoid.",";
											}
											//Retira a última vírgula
											$listaStatus = substr($listaStatus, 0, -1);
											
											//Recupera quantidades de títulos
											$retornoQtdes = $this->dao->buscaQuantidades($numeroRemessa,$listaStatus);
											if (!$retornoQtdes || count($retornoQtdes) == 0) {
												$msgException.= 'Quantidades de títulos processados não recuperadas. No.Remessa->'.$numeroRemessa.'.'.PHP_EOL;
												$this->dao->rollback();
												continue;
											}
											else{
												//Atribui o código do ocorrência 
												$qtd_total 		= intval($retornoQtdes[0]->qtd_total);
												$qtd_processada = intval($retornoQtdes[0]->qtd_processada);
												
												//Todos os títulos foram processados
												if ($qtd_total === $qtd_processada) {
													//Atualiza a data de retorno (status) da remessa 
													if ($this->dao->setaDtRetornoRemessa($numeroRemessa) == 'f') {
														$msgException.= 'Não foi possível atribuir a data de retorno da remessa. No.Remessa->'.$numeroRemessa.'.'.PHP_EOL;
													}
												}
											}
										}
									}
								}
							} 

                            //Finaliza transação
                            $this->dao->commit();

							$numeroLinha ++;
						} //Fim do while de linhas

                	} //Fim if linhas

                    //Move arquivos processados
                    $this->manipulaArquivoProcessado($parametrosFTP);

                    $arquivosBaixados[] = $parametrosFTP->nomeArquivo;
                	
                	//Atribui variáveis
					$email->assunto = "Contas a Pagar - Resultado do processo de baixa automática";

					$corpo_email = "";

					if(sizeof($arrayTitulos_nao_vinculados) > 0){
						$corpo_email .= "Durante o processamento da remessa, os títulos da lista abaixo não foram baixados: <br><br>";
						$corpo_email .= "
							<table cellspacing='0'>
								<thead>
									<tr>
										<th>Nome Fornecedor</th>
										<th>Valor</th>
										<th>Num. do Título</th>
										<th>Data do Agdo/Pagto</th>
									</tr>
								</thead>
								<tbody>
						";
						foreach($arrayTitulos_nao_vinculados as $res){
							$corpo_email .= 
									"<tr>
										<td>".$res["nome_fornecedor"]."</td>
										<td>".$res["valor"]."</td>
										<td>".$res["numero_titulo"]."</td>
										<td>".$res["data_agdo_pagto"]."</td>
									<tr>";
						}
						$corpo_email .=  "
								</tbody>
							</table>
							<style> 
								th, td {
									border: 1px solid;
									text-align: center;
								}
							</style>
						";
					}else{
						$corpo_email .="O arquivo de retorno da remessa ".$numeroRemessa." foi processado com sucesso.";
					}

                	$email->corpo = $corpo_email;
                	$email->anexo = null;
                	
                	//Envia email sucesso
                	if($msgSend = $this->enviarEmail($email)){
                		$msgException.= $msgSend.'Não foi possível enviar e-mail de arquivo processado com sucesso. No.Remessa->'.$numeroRemessa.'.'.PHP_EOL;
                	}
                	
		    		//Gera arquivo de LOG se houverem exceptions 
					if ($msgException!=''){ 
						$msgHeader = 'LOG do processamento do arquivo '.$parametrosFTP->nomeArquivo.', em '.date("d/m/Y H:i:s").'.'.PHP_EOL.PHP_EOL;
						$this->gerarLOG($msgHeader.$msgException, $parametrosFTP->nomeArquivo);
					}
                	
				} //Fim do foreach de arquivos
			}

            $retorno = $arquivosBaixados;
		
		}
		catch(Exception $e) {
			//Reverte ações na transação
            echo $e->getMessage();
			$retorno = null;
    	}
    	
		return $retorno;
		
	}

	/**
	 * Envio do email
	 *
	 * @param stdClass $resultado
	 * @return boolean
	 */
	private function enviarEmail(stdClass $email) {
		
		$parametros = new stdClass;
		$msgSend	= '';
		
		//Verifica o ambiente
		if($_SESSION['servidor_teste'] == 1) {
			//Atribui parametros de busca
			$parametros->codigoParametro = 'PARAMETROSAMBIENTETESTE';
			$parametros->codigoItemParametro = 'EMAIL';
		}
		else {
			//Atribui parametros de busca
			$parametros->codigoParametro = 'CONTAS_A_PAGAR';
			$parametros->codigoItemParametro = 'EMAIL_NOTIFICACAO';
		}

		//Recupera destinatário e-mail
		$destinatario = $this->dao->buscaParametros($parametros);
		if (!$destinatario || count($destinatario) == 0) {
			$msgSend.= 'Destinatário e-mail não recuperado. ';
		}
		else{
			//Atribui destinatário e-mail
			$destinatarioEmail = $destinatario[0]->pcsidescricao;
			
			//Atribui destinatário à um Array
			$arrayDestinatario = explode (';',$destinatarioEmail); 
		
			//Atribui variáveis
			$phpmailer = new PHPMailer();
			$phpmailer->isSmtp();
			$phpmailer->From = "sascar@sascar.com.br";
			$phpmailer->FromName = "sistema@sascar.com.br";
			$phpmailer->ClearAllRecipients();
			$phpmailer->Subject = $email->assunto;
			$phpmailer->MsgHTML($email->corpo);
			$phpmailer->AddAttachment($email->anexo);
		
			//Percorre Array de destinatários
			foreach($arrayDestinatario as $destinatario){
				//Atribui destinatário
				$phpmailer->AddAddress($destinatario);
				
				//Envio
				if (!$phpmailer->Send()) {
					$msgSend.= 'Houve um erro no envio de e-mail. ';
				}
			}
		}
		
		return $msgSend;
	}

	/**
	 * Gera log da baixa de contas a pagar Itaú. 
	 *
	 * @param $msg
	 * @return boolean
	 */
	private function gerarLOG($msg,$arquivo) {
		
		$fp = fopen("/var/www/log/LOG".$arquivo.".txt", 'w+');
		fwrite($fp, $msg);
		fclose($fp);
			
	}

    /**
     *  Metodo para buscar arquivo via ftp
     *  @param  class $parametros
     *  @param  class $parametros->servidor
     *  @param  class $parametros->usuario
     *  @param  class $parametros->senha
     *  @param  class $parametros->nomeArquivo
     *  @param  class $parametros->caminhoLocal
     *  @param  class $parametros->destino
     *  @return fase or array
     */
    private function buscarArquivoFTP($parametros){

        $conecta = ftp_connect($parametros->servidor,21);
        if(!$conecta){
            throw new Exception("Erro ao conectar com o servidor FTP");
        }
  
        /* Autenticar no servidor */
        $login = ftp_login($conecta, $parametros->usuario, $parametros->senha);
        if(!$login){ 
            throw new Exception("Erro ao autenticar com o servidor FTP");
        }

        //Conexão passiva
        ftp_pasv($conecta, TRUE);

        //Consulta arquivos que constam na pasta
        $arquivosRetornados = ftp_nlist($conecta, $parametros->destino);

        foreach ($arquivosRetornados as $chave => $arquivo) {

            //somente arquivos de retorno
            if( substr($arquivo, -4) != ".RET"){
                unset($arquivosRetornados[$chave]);
                continue;
            }
            $arquivosRetornados[$chave] = str_replace($parametros->destino, "", $arquivo);

            $retorno = ftp_get($conecta, $parametros->caminhoLocal.$arquivosRetornados[$chave], $parametros->destino.$arquivosRetornados[$chave], FTP_BINARY); 
            if(!$retorno){ 
                throw new Exception("Erro ao buscar o arquivo no servidor FTP");
            }
        }

        ftp_close($conecta);

        if(count($arquivosRetornados) == 0){
            return false;
        }

        return $arquivosRetornados;
    }

    /**
     *  Metodo para mover arquivo processado
     *  @param  class $parametros
     *  @param  class $parametros->servidor
     *  @param  class $parametros->usuario
     *  @param  class $parametros->senha
     *  @param  class $parametros->nomeArquivo
     *  @param  class $parametros->caminhoLocal
     *  @param  class $parametros->caminhoProcessado
     *  @param  class $parametros->destino
     *  @return true
     */
    public function manipulaArquivoProcessado($parametros){

        /**
         * FTP
         */
        
        $conecta = ftp_connect($parametros->servidor,21);
        if(!$conecta){
            throw new Exception("Erro ao conectar com o servidor FTP");
        }
  
        /* Autenticar no servidor */
        $login = ftp_login($conecta, $parametros->usuario, $parametros->senha);
        if(!$login){ 
            throw new Exception("Erro ao autenticar com o servidor FTP");
        }

        //Conexão passiva
        ftp_pasv($conecta, TRUE);

        //Consulta arquivos que constam na pasta
        $arquivosRetornados = ftp_nlist($conecta, $parametros->destino);

        //Verifica se existe a pasta PROCESSADOS        
        if ( array_search(substr($parametros->caminhoProcessado, 0, -1), $arquivosRetornados) === false ){
            //se não existir é criada
            ftp_mkdir ( $conecta , $parametros->caminhoProcessado );
        }

        $envio = ftp_put($conecta, $parametros->caminhoProcessado.$parametros->nomeArquivo, $parametros->caminhoLocal.$parametros->nomeArquivo, FTP_BINARY); 
        if(!$envio){ 
            throw new Exception("Erro ao mover o arquivo");
        }

        //remove arquivos
        ftp_delete ( $conecta , $parametros->destino.$parametros->nomeArquivo );
        unlink($parametros->caminhoLocal.$parametros->nomeArquivo);

        ftp_close($conecta);

        return true;
    }
	
    /**
     * Metodo Construtor
     */
    public function __construct() {
        $this->dao = new BaixaContasAPagarItauDAO();

        $this->ambiente = ( _AMBIENTE_ == "PRODUCAO" ? "PRODUCAO" : "TESTE" );
    }

}