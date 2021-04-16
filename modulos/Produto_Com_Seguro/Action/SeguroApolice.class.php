<?php
/**
 * @file SeguroApolice.class.php
 * @author marcioferreira
 * @version 31/10/2013 11:22:36
 * @since 31/10/2013 11:22:36
 * @package SASCAR SeguroApolice.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

//manipula os dados no BD
require(_MODULEDIR_ . "Produto_Com_Seguro/DAO/SeguroApoliceDAO.class.php");


class SeguroApolice {

	/**
	 * Fornece acesso aos dados do BD necessários para o módulo
	 * @property SeguroApoliceDAO
	 */
	private $dao;

	private $erro;
	private $id_envio_dados;
	
	private $contrato_numero; // da tabela contrato
	private $classe_produto;
	
	//atributos da montagem das tags XML
	private $id_seguradora;
	private $id_revenda;
	private $nm_usuario;
	private $nr_cotacao_i4pro;
	private $id_proposta;
	private $dt_instala_rastreador;
	private $dt_ativa_rastreador;
	private $dt_ativa_rastreador_calculo;
	private $dt_inicio_vig_comodato;
	private $dt_fim_vig_comodato;
	
	private $cod_cotacao; //id da tabela produto_seguro_cotacao
	private $cod_proposta; //id da tabela produto_seguro_proposta
	
	private $data_cadastro_proposta;
	private $dias_validade_proposta;
	
	private $cod_representante;
	private $usuario_logado;
	private $numero_ordem_servico;
	private $nome_cliente;
	private $email_cliente;
	private $retornoXmlSeguro;
	private $origem_sistema; //Intranet, Portal
	private $origem_chamada; //local ou aplicação que está solicitando os serviços ex: rel_produto_com_seguro *sem extensão do arquivo

	// Mantis 7005 - flag para indicar se deve atualizar o inicio de vigencia do contrato
	private $atualiza_vigencia;

	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
	public function __construct(){
		
		global $dbstringSiggo;
		
		try{
			
			$connSiggo = pg_connect($dbstringSiggo);
		
		}catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		$erro = Array();
		$this->retornoXmlSeguro = false;
			
		$this->produtoComSeguro = new ProdutoComSeguro();
		$this->dao  = new SeguroApoliceDAO($connSiggo);
		$this->daoProduto = new ProdutoComSeguroDAO($connSiggo);
	}
	
	public function processarApolice(){

		try{
			
			
			//inicia transação do banco de dados
			$this->dao->begin();
			
			// verifica se contrato é produto com seguro
			$produtoSeguro = $this->dao->getProdutoSeguro($this->getContratoNumero());

			//verifica se para o contrato já existe um apólice ativa
			$satusApolice = $this->dao->getApoliceStatus($this->getContratoNumero());


			if($produtoSeguro == false){ 
				//seta o erros encontrados
				$resposta['status'] = 'Erro';
				$this->erro['cod_msg'][] = 28; //'Apólice já ativada. Ação cancelada.'

				return false;
			}

			//se for um objeto, então, a apólice do contrato já está ativa, não permite o envio de outra
			if(is_object($satusApolice)){
				
				//seta o erros encontrados
				$resposta['status'] = 'Erro';
				$this->erro['cod_msg'][] = 27; //'Apólice já ativada. Ação cancelada.'
				
				//insere status de erro na primeira posição do array de erros para verificação
				$this->erro = array_merge($this->erro, $resposta);
	
				if($this->getOrigemChamada() === 'rel_produto_com_seguro'){
					
					return $this->erro;
				}
				
				if($this->getOrigemChamada() === 'chamadas_seguro_fake_CRM'){
					
					return $this->erro;
				}
	
				return false;
			}
			
			//verifica se o tipo e os dados obrigatórios de entrada estão preenchidos
			$validaEntradaDados = $this->validarEntradaDados();
			
			$dadosParaEnvio = new stdClass();

			$dadosParaEnvio->num_contrato      = $this->getContratoNumero() ? $this->getContratoNumero(): 'NULL';
			$dadosParaEnvio->usuario_logado    = $this->getCodUsuarioLogado() ? $this->getCodUsuarioLogado(): 'NULL';
			$dadosParaEnvio->cod_representante = $this->getCodigoRepresentante() ? $this->getCodigoRepresentante() : 'NULL';
			$dadosParaEnvio->ordem_servico     = $this->getNumOrdemServico() ? $this->getNumOrdemServico() : 'NULL';
			$dadosParaEnvio->data_instalacao   = $this->getDataInstalacaoEquipamento() ? $this->getDataInstalacaoEquipamento(): '';
			$dadosParaEnvio->data_ativacao     = $this->getDataAtivacaoEquipamento() ? $this->getDataAtivacaoEquipamento() :'';
			$dadosParaEnvio->data_ini_comodato = $this->getDataAtivacaoEquipamento() ? $this->getDataAtivacaoEquipamento() : '';
			$dadosParaEnvio->data_fim_comodato = $this->calcularData($this->dt_ativa_rastreador_calculo,0,0, 1) ? $this->calcularData($this->dt_ativa_rastreador_calculo,0,0, 1) : '';
			$dadosParaEnvio->origem_chamada    = $this->getOrigemChamada() ? $this->getOrigemChamada() : 'NULL';
			$dadosParaEnvio->origem_sistema    = $this->getOrigemSistema() ? $this->getOrigemSistema() : 'NULL';
			
			//valida usuário logado
			$usuario_logado_existe = $this->dao->verificarUsuarioLogado($dadosParaEnvio->usuario_logado);
			
			if(!is_object($usuario_logado_existe)){
				$dadosParaEnvio->usuario_logado = 'NULL';
			}
			
			//valida cod do representante
			$cod_representante_existe = $this->dao->verificarCodRepresentante($dadosParaEnvio->cod_representante);
			
			if(!is_object($cod_representante_existe)){
				$dadosParaEnvio->cod_representante = 'NULL';
			}
			
			//valida número da ordem de serviço
			$ordem_servico_existe = $this->dao->verificarNumeroOrdermServico($dadosParaEnvio->ordem_servico);
			
			if(!is_object($ordem_servico_existe)){
				$dadosParaEnvio->ordem_servico = 'NULL';
			}
			
			if($this->getContratoNumero() != NULL){
			
				//verifica se o contrato informado existe na tabela contrato
				$dadosContrato = $this->dao->verificarContrato($this->getContratoNumero());
				
				if(is_object($dadosContrato)){
					//seta o nome do cliente para envio no e-mail
					$this->nome_cliente = $dadosContrato->nome_cliente;
					$this->email_cliente = $dadosContrato->email_cliente;
						
				}else{
					
					//seta mensagem de erro
					$this->erro['cod_msg'][] = 2;// 'Numero de contrato não encontrado. A ativação da apólice não foi efetivada.';
					$dadosParaEnvio->num_contrato = 'NULL';
					$this->setContratoNumero(NULL);
				}
			}
			
			//seta o id na variável, recupera depois para update de retorno da seguradora ou de msg erro
			$this->id_envio_dados = $this->dao->setDadosEnvioWs($dadosParaEnvio);
			
			//recupera id da seguradora com benefício == 'SEGURO'
			$seguradora = $this->daoProduto->getSeguradora();
				
			if(is_array($seguradora)){
			
				foreach ($seguradora as $dadosSeguradora){
					$this->id_seguradora = $dadosSeguradora['emboid'];
				}
			
			}else{
				//seta mensagem de erro
				$this->erro['cod_msg'][] = 1;//'Não existe seguradora cadastrada com beneficio Seguro';
			}
				
			//verifica se a entrada de dados são válidos
			if(count($validaEntradaDados) == 0 && empty($this->erro)){
				
				if($this->getContratoNumero() != NULL){

					//verifica se existe e retorna dados da proposta para o contrato informado
					$dadosProposta = $this->dao->getPropostaContrato($this->getContratoNumero());

					if(is_object($dadosProposta)){

						//popula outras tags para gerar o XML
						$this->cod_proposta             = $dadosProposta->cod_proposta;
						$this->id_proposta              = $dadosProposta->numero_proposta;
						$this->cod_cotacao              = $dadosProposta->cod_cotacao;
						$this->nr_cotacao_i4pro         = $dadosProposta->numero_cotacao;
						$this->data_cadastro_proposta   = $dadosProposta->data_cadastro_proposta;
						$this->dt_inicio_vig_comodato   = $this->getDataAtivacaoEquipamento() ? $this->getDataAtivacaoEquipamento() : '';
						$this->dt_fim_vig_comodato      = $this->produtoComSeguro->validarData($this->calcularData($this->dt_ativa_rastreador_calculo,0,0, 1) ? $this->calcularData($this->dt_ativa_rastreador_calculo,0,0, 1) : '');  
							
						//instancia o WS  da seguradora
						$integracaoSeguradora = new IntegracaoSeguradora();
						$integracaoSeguradora->idSeguradora = $this->id_seguradora;
						$integracaoSeguradora->setParametros();
						//$this->id_revenda = $integracaoSeguradora->id_revenda;
						$this->id_revenda = $dadosProposta->corretor_indicador;
						$this->nm_usuario = $integracaoSeguradora->nm_usuario;

						//atualiza outros dados na tabela antes de enviar os dados para seguradora
						$atualizaDadosParaEnvio = new stdClass();
						$atualizaDadosParaEnvio->seguradora        = $this->id_seguradora;
						$atualizaDadosParaEnvio->cod_cotacao       = $this->cod_cotacao;
						$atualizaDadosParaEnvio->cod_proposta      = $this->cod_proposta;
						$atualizaDadosParaEnvio->id_revenda        = $this->id_revenda;
						$atualizaDadosParaEnvio->nm_usuario        = $this->nm_usuario;

						/**
						 * Seta corretor para envio à seguradora
						 * @author Vinicius Senna <vsenna@brq.com>
						 * 
						 */
						$atualizaDadosParaEnvio->corretor_indicador	= $dadosProposta->corretor_indicador;


						//atualiza os dados
						$this->dao->atualizarDadosEnvioWS($atualizaDadosParaEnvio, $this->id_envio_dados);
							
						//verificar validade da proposta
						$validadeProposta = $this->getValidadeProposta($this->data_cadastro_proposta);

						//se o retorno for falso verifica se houve erro
						if($validadeProposta != 1){
							//se o erro tiver vazio, então, retorna falso, a proposta está fora da validade
							if(empty($this->erro)){
								//seta mensagem de erro
								$this->erro['cod_msg'][] = 3;//'Proposta fora do prazo de validade. Ativação da apólice não foi efetivada';
							}
						}

					}else{
						//seta mensagem de erro
						$this->erro['cod_msg'][] = 4;// 'Proposta com a seguradora não foi encontrada. Ativação da apólice não foi efetivada';
					}
				}
			
				if(empty($this->erro)){
					
					//recupera a classe do produto com beneficio seguro
					$classeProduto = $this->daoProduto->getClasseProduto($this->getClasseProduto(), $this->id_seguradora);
					
					if(!is_array($classeProduto)){
					
						//seta código de erro
						$this->erro['cod_msg'][] = 504;//PRODUTO NÃO TEM SEGURO
					
						//recupera mensagem de erro na tabela produto_seguro_mensagens e grava no log
						$msg_log = $this->daoProduto->getMensagem(504);
					
						if(is_object($msg_log)){
					
							$dadosRetornoWs = new stdClass();
							$dadosRetornoWs->msg_id = $msg_log->msg_id;
												
							//atualiza a tabela produto_seguro_cotacao com a mensagem
							$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($dadosRetornoWs, $this->id_envio_dados);
						}
					}
	
					if(empty($this->erro)){

						//recupera layout XML e o serviço a ser chamado no Ws de acordo a seguradora informada
					    $layoutXmlEntradaDadosWs = $this->daoProduto->getLayoutXmlEntrada($this->id_seguradora, 'efetivarvendaautoconfiguravel','ENTRADA');
					    
						if(is_array($layoutXmlEntradaDadosWs)){
							
							//faz a montagem do XML fora do padrão
							$xmlEntradaDados = $this->montarLayoutXml($layoutXmlEntradaDadosWs);
							
							$xmlEnvio  = new stdClass();
							$xmlEnvio->xml_envio = trim($xmlEntradaDados);
								
							//grava o xml gerado para envio
							$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($xmlEnvio, $this->id_envio_dados);
							
						} else{
							//seta mensagem de erro
							$this->erro['cod_msg'][] = 5;//'Seguradora não possui layout cadastrado';
						}
						
						if(empty($this->erro)){
							
							//envia dados para o WS da seguradora
                            $retornoSeguradora = $integracaoSeguradora->enviarDadosWs($layoutXmlEntradaDadosWs[0]['servico'], $xmlEntradaDados);
							
							if(!is_array($retornoSeguradora)){
							
								$xmlRetorno  = new stdClass();
								$xmlRetorno->xml_retorno = trim(preg_replace("/(\'|'')/",'-', $retornoSeguradora));
											
								//grava o xml retornado da seguradora
								$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($xmlRetorno, $this->id_envio_dados);
							
								$objXML = new SimpleXMLElement($retornoSeguradora);
							
								//transforma objeto XML retornado em array
								$retornoSeguradora = get_object_vars($objXML);
								
								$this->retornoXmlSeguro = true;
							}
					
							if(is_array($retornoSeguradora) && isset($retornoSeguradora['identificacao']) && $this->retornoXmlSeguro){

								$dadosRetorno = new stdClass();

								//transforma objeto em array
								$identificacao = get_object_vars($retornoSeguradora['identificacao']);

								//recupera os dados de identificação
								$dadosRetorno->id_revenda = $identificacao['@attributes']['id_revenda'];
								$dadosRetorno->nm_usuario = $identificacao['@attributes']['nm_usuario'];

								//recupera os dados da cotação
                                    $proposta_auto = get_object_vars($identificacao['efetivar_venda_auto_configuravel']);
                                
                                $dadosRetorno->id_apolice = $proposta_auto['@attributes']['id_apolice'];
								$dadosRetorno->cd_apolice = $proposta_auto['@attributes']['cd_apolice'];
								$dadosRetorno->id_endosso = $proposta_auto['@attributes']['id_endosso'];
									
								//transforma objeto em array
								$retorno = get_object_vars($retornoSeguradora['retorno']);

								//recupera os dados de retorno
								$dadosRetorno->cd_retorno = (int)$retorno['@attributes']['cd_retorno'];
								$dadosRetorno->nm_retorno =  utf8_decode($retorno['@attributes']['nm_retorno']);
								
								//verificar se o código de retorno do WS existe na tabela de mensagens, 
								$dados_mensagem = $this->daoProduto->getMensagem($dadosRetorno->cd_retorno);
								
								//se existir pega o código, 
								if(is_object($dados_mensagem)){
									$dadosRetorno->msg_id = $dados_mensagem->msg_id;
								
								//se não, faz insert e pega o código
								}else{
									$insereMensagem = $this->daoProduto->setMensagem($dadosRetorno->cd_retorno, $dadosRetorno->nm_retorno);
									$dadosRetorno->msg_id = $insereMensagem;
								}
								
								//atualiza a tabela produto_seguro_cotacao com os dados de retorno do WS
								$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($dadosRetorno, $this->id_envio_dados);
								
								if($dadosRetorno->cd_retorno === 0){
								
									//atualiza a data de inicio vigência do contrato Sascar e a data de instalação do equipamento 
									//também o inicio e o fim da vigência da apólice 
									
									// Adicionado terceiro parametro no mantis 7005
									$this->dao->setDataVigenciaInstalacao($this->getContratoNumero(), $this->id_envio_dados,$this->getFlagVigencia());
																	
									if($this->getOrigemChamada() === 'rel_produto_com_seguro'){
										$cod_status = 10;//"Ativada manualmente"
									}else{
										(int)$cod_status = 0; //"Ativada via sistema"
									}
									
									//pesquisa o ID do status
									$id_status = $this->dao->getStatus($cod_status);
									
									$status = new stdClass();
									$status->id_status = $id_status->id_status;
									
									//atualiza status da apólice
									$this->dao->atualizarDadosRetornoWs($status, $this->id_envio_dados);
									
									//envia e-mail de sucesso para o cliente
									$dadosSucesso = new stdClass();
									$dadosSucesso->nm_retorno = $dadosRetorno->nm_retorno;
									$dadosSucesso->cd_apolice = $dadosRetorno->cd_apolice;
									
									$this->enviarEmailInformativo($dadosSucesso, 'sucesso');
									
									//finaliza transação do banco de dados
									$this->dao->commit();
									
									return true;
								
								}else{
								
									//seta cod de erro retornado do WS
									$this->erro['cod_msg'][] = $dadosRetorno->cd_retorno;
									
								}
									
									
							}elseif(isset($retornoSeguradora['status'])){

								// caso venha o faultstring do WS
								if($retornoSeguradora['cod_msg'] == 301){
	
									//pega somente o erro retornado o WS
									$msgSeguradora = explode("|", $retornoSeguradora['mensagem']);
									
									$this->erro['msg'][] = $msgSeguradora[1];
								}
								
								$this->erro['cod_msg'][] = $retornoSeguradora['cod_msg'];

							}else{

								$this->erro['cod_msg'][] = 19; // "Retorno Ws desconhecido";
							}
						}
					}
				}
			}
			
			
			# Atualiza Status da apólice
			//pesquisa o ID do status
			$id_status = $this->dao->getStatus(2);//"Erro ao ativar."
			$status = new stdClass();
			$status->id_status = $id_status->id_status;
			//atualiza status da apólice
			$this->dao->atualizarDadosRetornoWs($status, $this->id_envio_dados);
			
			//seta o erros encontrados
			$resposta['status'] = 'Erro';
			//insere status de erro na primeira posição do array de erros para verificação
			$this->erro = array_merge($this->erro, $resposta);
			//grava as mensagens de erro
			$grava_msg_erro = $this->setMensagemProcesso($this->erro);
			
			//finaliza transação do banco de dados
			$this->dao->commit();			
			
			//envia email com os erros
			$this->enviarEmailInformativo($this->erro, 'erro');
			
			if($this->getOrigemChamada() === 'rel_produto_com_seguro'){
				return $this->erro;
			}

			if($this->getOrigemChamada() === 'chamadas_seguro_fake_CRM'){
				return $this->erro;
			}
					
			return true;
			
		}catch (Exception $e){
			
			//finaliza transação do banco de dados
			$this->dao->commit();
			
			return $e->getMessage();
		}
	}

	/**
	 * Retorno nome e e-mail do cliente pelo número do contrato informado
	 * 
	 * @param int $num_contrato
	 * @return Ambigous <object, boolean>
	 */
	public function getDadosClienteContrato($num_contrato){
		
		return $this->dao->verificarContrato($num_contrato);
		
	}
	
	
	public function getLayoutEmail($tituloLayout, $cabecalho){
		
		//busca layout do e-mail
		$dadosEmailErro = $this->dao->getLayoutEmail($tituloLayout, $cabecalho);
		
		return $dadosEmailErro;
		
	}
	
	
	
	/**
	 * $data -> Data de parâmetro para o cálculo
	 * $dias -> Dias que será incluído na $data
	 * $meses -> Meses que será incluído na $data
	 * $anos -> Anos que será incluído na $data
	 * 
	 * @param date $data
	 * @param int $dias
	 * @param int $meses
	 * @param in $anos
	 * @return boolean|unknown
	 */
	private  function calcularData($data, $dias = NULL, $meses = NULL, $anos = NULL){
		
		if(strpos($data, '-')){
		
			list($dia, $mes, $ano) = explode("-", $data);
		
			$nova_data = date("d-m-Y", mktime(0, 0, 0, $mes + $meses, $dia + $dias, $ano + $anos));
				
		}elseif(strpos($data, '/')){
		
			list($dia, $mes, $ano) = explode("/", $data);
		
			$nova_data = date("d-m-Y", mktime(0, 0, 0, $mes + $meses, $dia + $dias, $ano + $anos));
		
		}else{
		
			return false;
		}
		
		return $nova_data;
	}
	
	/**
	 * Grava mensagens pelo código 
	 *
	 * @param array $array_msg
	 * @return boolean
	 */
	private function setMensagemProcesso($array_cod_msg){
	
		if(count($array_cod_msg) > 0){
			
			$dados_adicionais = "";
			
			foreach ($array_cod_msg['cod_msg'] as $value) {

				//busca o id do cod da mensagem
				$dados_mensagem = $this->daoProduto->getMensagem($value);

				if(!is_object($dados_mensagem)){
					$dados_adicionais .= 'O código da mensagem informado não foi econtrado';
				}

				//se caso tem que gravar mensagem adicional de erro
				if(isset($array_cod_msg['msg'])){
					$dados_adicionais .= $array_cod_msg['msg'][0];
				}
					
				$this->dao->setMensagensProcesso($dados_mensagem->msg_id, $this->id_envio_dados, $dados_adicionais);
			}
			
			return true;
		}
	
		return false;
	}
	
	/**
	 * Recupera a quantidade de dias da validade da proposta da tabela
	 * Verifica se a proposta entá no prazo de validade
	 * 
	 * @param date $data
	 * @return boolean|multitype:
	 */
	private function getValidadeProposta($data){
		
		//ambiente de testes
		if($_SERVER["SERVER_ADDR"] == '172.16.2.57' || $_SESSION["servidor_teste"] == 1 ||
		   $_SERVER['HTTP_HOST'] ==  '192.168.56.101' ||
		   (strstr($_SERVER['REQUEST_URI'], 'teste/') || 
		   	strstr($_SERVER['REQUEST_URI'], 'desenvolvimento/')	|| 
		   	       $_SERVER['HTTP_HOST'] == 'homologacao.sascar.com.br')){
		
			//seta variável para buscar na tabela de parâmetros
			$filtroAmbiente = 'WEBSERVICE_SEGURADORA_TESTE';
		
		}else{
				
			//seta variável para buscar na tabela de parâmetros em produção
			$filtroAmbiente = 'WEBSERVICE_SEGURADORA';
		}
		

		//pesquisar a quantidade de dias para validar a vigência da proposta
		$diasValidadeProposta = $this->dao->getDiasValidadeProposta($filtroAmbiente);
		
		if(is_object($diasValidadeProposta)){
			
			$this->dias_validade_proposta = $diasValidadeProposta->pcsidescricao;
			
		}else{
			//seta mensagem de erro
			$this->erro['cod_msg'][] = 6; //'Prazo de validade da proposta não cadastrado.';
		}
		
		if(empty($this->erro)){
			
			list($proposta_ano, $proposta_mes, $proposta_dia) = explode("-", $this->data_cadastro_proposta);
			list($hoje_ano, $hoje_mes, $hoje_dia ) = explode("/", date('Y/m/d'));
			
			$data_proposta = mktime(0,0,0,$proposta_mes, $proposta_dia, $proposta_ano);
			$data_hoje = mktime(0,0,0,$hoje_mes, $hoje_dia, $hoje_ano);
			
			$total_dias = ($data_proposta - $data_hoje) / 86400;
			
			if($total_dias <= $this->dias_validade_proposta){
				return true;
			}else{
				return false;
			}
		}
		
		return $this->erro;
	}

	
	/**
	 * Monta o xml pelos dados passados no parâmetro
	 * O dados são populados referenciando o nome da tag que vem do banco com nome do atributo criado, logo,
	 * os atributos devem ser criados de acordo o nome que vem do banco
	 *
	 * @param array $arrayLayout
	 * @throws Exception
	 * @return string
	 */
	private function montarLayoutXml($arrayLayout){
	
		try {
	
			if(empty($arrayLayout)){
				throw new Exception('Os dados para montar o XML para gerar proposta não foram encontrados');
			}
	
                $xml = '<i4proerp><venda_auto_configuravel ';
			
                foreach ($arrayLayout as $dadosXml){
                   //não envia a tag se não tiver dados (WS compreende como nulo)
                    if($this->$dadosXml['tag'] != NULL){
                        $xml .= $dadosXml['tag'].'="'.$this->$dadosXml['tag'].'" ';
                    }
                }
			
			$xml .='/></i4proerp> ';
	
			return $xml;
	
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Valida os campos obrigatórios e retorna mensagem.
	 *
	 * @return string
	 */
	private function validarEntradaDados(){
			
		if($this->getContratoNumero() === NULL){
			$this->erro['cod_msg'][] = 7;//'Número do contrato é obrigatório';
		}else{
				
			if(!is_numeric($this->getContratoNumero())){
				$this->erro['cod_msg'][] = 8;//'Código do contrato deve ser do tipo inteiro (envio)';
				$this->setContratoNumero(NULL);
			}
		}

		if($this->getDataInstalacaoEquipamento() === NULL){
			$this->erro['cod_msg'][] = 9;//'A data de instalação do equipamento é obrigatória';
		}else{
				
			//validar formato da data
			$dataValida = $this->produtoComSeguro->validarData($this->getDataInstalacaoEquipamento());
		
			if(empty($dataValida)){
				$this->erro['cod_msg'][] = 10;//'Data de instalação inválida. A data deve estar nos formatos: xx-xx-xxxx ou xx/xx/xxxx';
				$this->setDataInstalacaoEquipamento(NULL);
			}else{
				$this->setDataInstalacaoEquipamento($dataValida);
			}
		}
		
		if($this->getDataAtivacaoEquipamento() === NULL){
			$this->erro['cod_msg'][] = 11; //'A data de ativação do equipamento é obrigatória';
		}else{
		
			//validar formato da data
			$dataValida = $this->produtoComSeguro->validarData($this->getDataAtivacaoEquipamento());
		
			if(empty($dataValida)){
				$this->erro['cod_msg'][] = 12;//'Data de ativação inválida. A data deve estar nos formatos: xx-xx-xxxx ou xx/xx/xxxx';
				$this->setDataAtivacaoEquipamento(NULL);
			}else{
				$this->dt_ativa_rastreador_calculo = $this->getDataAtivacaoEquipamento();
				$this->setDataAtivacaoEquipamento($dataValida);
			}
		}		
		
		if($this->getClasseProduto() === NULL){
			$this->erro['cod_msg'][] = 13;//'Classe do produto é obrigatório';
		}else{
				
			if(!is_numeric($this->getClasseProduto())){
				$this->erro['cod_msg'][] = 14;//'Classe do produto deve ser do tipo inteiro (envio)';
				$this->setClasseProduto(NULL);
			}
		}
		
		if($this->getOrigemChamada() === NULL){
			$this->erro['cod_msg'][] = 25;//'ORIGEM DA CHAMADA É OBRIGATÓRIO'
		}
		
		if($this->getOrigemSistema() === NULL){
			$this->erro['cod_msg'][] = 26;//'SISTEMA DISPARADOR DA CHAMADA É OBRIGATÓRIO'
		}
		
		return $this->erro;
	}

	
	/**
	 * Envia email com informação de erro ou sucesso
	 * 
	 * @param object/array $dados_msg
	 * @param string $tipoEnvio  erro/sucesso
	 */
	private function enviarEmailInformativo($dados_msg = NULL, $tipoEnvio){
		
	    //ambiente de testes
		if($_SERVER["SERVER_ADDR"] == '172.16.2.57' || $_SESSION["servidor_teste"] == 1 ||
		   $_SERVER['HTTP_HOST'] ==  '192.168.56.101' ||
		   (strstr($_SERVER['REQUEST_URI'], 'teste/') || 
		   	strstr($_SERVER['REQUEST_URI'], 'desenvolvimento/')	|| 
		   	       $_SERVER['HTTP_HOST'] == 'homologacao.sascar.com.br')){
		
			//seta variável para buscar na tabela de parâmetros
			$filtroAmbiente = 'WEBSERVICE_SEGURADORA_TESTE';
		
		}else{
				
			//seta variável para buscar na tabela de parâmetros em produção
			$filtroAmbiente = 'WEBSERVICE_SEGURADORA';
		}
		
		if($tipoEnvio == 'erro'){
			
			$listaEmailsErro = $this->dao->getEmailEnvioErro($this->id_seguradora, $filtroAmbiente);
			
			if($listaEmailsErro){
				
				$tituloLayout = "Falha ao efetivar apolice de seguro";
				$cabecalho    = "Falha ao efetivar apolice de seguro";

				//busca layout do e-mail
				$dadosEmailErro = $this->dao->getLayoutEmail($tituloLayout, $cabecalho)	;

				if(is_array($dadosEmailErro)){

					$htmlEmail = str_replace('$connumero', $this->getContratoNumero(),  $dadosEmailErro[0]['corpo_email']);
					$htmlEmail = str_replace('$ordemservico', $this->getNumOrdemServico(), $htmlEmail);
					$htmlEmail = str_replace('$usuario', $this->getCodUsuarioLogado(), $htmlEmail);
					$htmlEmail = str_replace('$repoid', $this->getCodigoRepresentante(), $htmlEmail);
					$htmlEmail = str_replace('$clientenome', $this->nome_cliente, $htmlEmail);
					$htmlEmail = str_replace('$dtinstalacao', $this->produtoComSeguro->inverterData($this->getDataInstalacaoEquipamento()), $htmlEmail);
					$htmlEmail = str_replace('$dtativacao', $this->produtoComSeguro->inverterData($this->getDataAtivacaoEquipamento()), $htmlEmail);
					$htmlEmail = str_replace('$proposta', $this->id_proposta, $htmlEmail);
					$htmlEmail = str_replace('$cotacao', $this->nr_cotacao_i4pro, $htmlEmail);
						
					if(is_array($dados_msg)){
							
						//percorre os código de erro para buscar a mensagem
						foreach ($dados_msg['cod_msg'] as $key=> $cod){
							
							//recupera mensagem de erro na tabela produto_seguro_mensagens 
							$msg_log = $this->daoProduto->getMensagem($cod);
								
							if(is_object($msg_log)){
							
								$dadosRetornoWs = new stdClass();
								$mensagemErro .=  $msg_log->msg_sascar."<br/>";
							}
						}

						$htmlEmail = str_replace('$mensagem', $mensagemErro, $htmlEmail);
							
						//percorre a lista de e-mails
						foreach ($listaEmailsErro as $email_erro) {
							// envia o email
							$this->produtoComSeguro->enviarEmail($email_erro['email'], $htmlEmail, $dadosEmailErro[0]['assunto_email'], $dadosEmailErro[0]['servidor'] );
						}
					}
					
				}else{
					$erro['cod_msg'][] = 15; //'Layout de e-mail "'.$tituloLayout.'" não cadastrado. Responsável(eis) pelo processo não serão informados de falha na execução do processo via e-mail.';
				}
				
			}else{
				$erro['cod_msg'][] = 16; //'Endereço de e-mail não cadastrado. Responsável(eis) pelo processo não serão informados de falha na execução do processo via e-mail';
			}
			
			
			if(!empty($erro)){
				//grava as mensagens de erro
				$grava_msg_erro = $this->setMensagemProcesso($erro);
			}
		}
		
		//envia e-mail em caso de sucesso
		if($tipoEnvio == 'sucesso'){
			
			$tituloLayout = 'Apolice de Seguro';
			$cabecalho    = 'Apolice de Seguro';
			
			//busca layout do e-mail
			$dadosEmail = $this->dao->getLayoutEmail($tituloLayout, $cabecalho)	;
			
			if(is_array($dadosEmail)){
				
				if(!empty($this->email_cliente)){

					$htmlEmail = $this->substituirDadosCienteEmail($this->nome_cliente,  $dadosEmail[0]['corpo_email'], $dados_msg->cd_apolice);
					
					$this->produtoComSeguro->enviarEmail($this->email_cliente, $htmlEmail, $dadosEmail[0]['assunto_email'], $dadosEmail[0]['servidor'] );
				
				}else{
					
					$erro['cod_msg'][] = 17;//'Endereço de e-mail não cadastrado. O cliente não receberá o e-mail com o número da apólice';
				}
				
			}else{
				$erro['cod_msg'][] = 18; //'Layout de e-mail "'.$tituloLayout.'" não cadastrado. O cliente não receberá o e-mail com o número da apólice';
			}
			
		    if(!empty($erro)){
		    	
		    	if($this->getOrigemChamada() === 'rel_produto_com_seguro'){
		    		$cod_status = 11;//"Ativada manualmente - Retorno de mensagem de alerta."
		    	}else{
		    		$cod_status = 1; //"Ativada via sistema - Retorno de mensagem de alerta."
		    	}
		    	
		    	//pesquisa o ID do status
		    	$id_status = $this->dao->getStatus($cod_status);
		    	
		    	$status = new stdClass();
		    	$status->id_status = $id_status->id_status;
		    	
		    	//atualiza status da apólice
		    	$this->dao->atualizarDadosRetornoWs($status, $this->id_envio_dados);
		    	
				//grava as mensagens de erro
				$grava_msg_erro = $this->setMensagemProcesso($erro);
			}
			
		}
		
		return true;
	} 
	
	
	public function substituirDadosCienteEmail($nomeCliente, $corpoEmail, $cd_apolice){
		
		$htmlEmail = str_replace('$clientenome', $nomeCliente , $corpoEmail);
		$htmlEmail = str_replace('$apolice', $cd_apolice, $htmlEmail);
		
		return $htmlEmail;
		
	}
	
   //sets e gets apólice
	public function setContratoNumero($valor){
		$this->contrato_numero = $valor;
	}
	public function getContratoNumero(){
		return $this->contrato_numero;
	}
	
	public function setDataInstalacaoEquipamento($valor){
		$this->dt_instala_rastreador = $valor;
	}
	public function getDataInstalacaoEquipamento(){
		return $this->dt_instala_rastreador;
	}
	
	public function setDataAtivacaoEquipamento($valor){
		$this->dt_ativa_rastreador = $valor;
	}
	public function getDataAtivacaoEquipamento(){
		return $this->dt_ativa_rastreador;
	}
	
	public function setClasseProduto($valor){
		$this->classe_produto = $valor;
	}
	public function getClasseProduto(){
		return $this->classe_produto;
	}
	
	public function setCodigoRepresentante($valor){
		$this->cod_representante = $valor;
	}
	public function getCodigoRepresentante(){
		return $this->cod_representante;
	}
	
	public function setCodUsuarioLogado($valor){
		$this->usuario_logado = $valor;
	}
	public function getCodUsuarioLogado(){
		return $this->usuario_logado;
	}
	
	public function setNumOrdemServico($valor){
		$this->numero_ordem_servico = $valor;
	}
	public function getNumOrdemServico(){
		return $this->numero_ordem_servico;
	}

	public function setOrigemChamada($valor){
		$this->origem_chamada = $valor;
	}
	public function getOrigemChamada(){
		return $this->origem_chamada;
	}
		
	public function setOrigemSistema($valor){
		$this->origem_sistema = $valor;
	}
	public function getOrigemSistema(){
		return $this->origem_sistema;
	}

	// Mantis 7005
	public function setFlagVigencia($valor){
		$this->atualiza_vigencia = $valor;
	}

	public function getFlagVigencia(){
		return $this->atualiza_vigencia;
	}

}