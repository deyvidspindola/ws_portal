<?php 
/**
 * @file SeguroProposta.class.php
 * @author marcioferreira
 * @version 31/10/2013 11:22:17
 * @since 31/10/2013 11:22:17
 * @package SASCAR SeguroProposta.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

//manipula os dados no BD
require(_MODULEDIR_ . "Produto_Com_Seguro/DAO/SeguroPropostaDAO.class.php");


class SeguroProposta {

	/**
	 * Fornece acesso aos dados do BD necessários para o módulo
	 * @property SeguroPropostaDAO
	 */
	private $dao;
	
	/**
	 * @var $erro Array
	 */
	private $erro;
	private $erroTipo;
	private $id_envio_dados;
	
	private $nr_cotacao_i4pro;
	private $contrato_numero;
	private $nm_pessoa;
	private $id_sexo;
	private $id_estado_civil;
	private $cd_profissao;
	private $dt_nascimento;
	private $nm_resp1;
	private $nm_resp2;
	private $nr_ddd_res;
	private $nm_fone_res;
	private $nr_ddd_cel;
	private $nm_fone_cel;
	private $nm_email;
	private $nm_endereco;
	private $nr_endereco;
	private $nm_complemento;
	private $nm_cidade;
	private $cd_uf;
	private $nm_placa;
	private $nm_chassis;
	private $id_auto_utilizacao;
	private $dv_segurado_proprietario;
	private $cd_forma_pagamento_pparcela;
    private $id_produto_parc_premio;
	
	private $forma_pagamento;
	
	private $classe_produto;
	private $id_seguradora;
	
	private $cod_cotacao;
	private $uf;
	
	private $retornoXmlSeguro;

	private $corretor_indicador;
	
    private $vigencia;

    private $cd_produto;


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
		$erroTipo  = Array();
		$this->retornoXmlSeguro = false;
		
		$this->produtoComSeguro = new ProdutoComSeguro();
		$this->dao  = new SeguroPropostaDAO($connSiggo);
		$this->daoProduto = new ProdutoComSeguroDAO($connSiggo);

	}


	public function processarProposta($clioid = 0){

		try{
			$idRevendaCorretor = 0;
			
			//inicia transação do banco de dados
			$this->dao->begin();
			
		    //recupera id da seguradora com benefício == 'SEGURO'
			$seguradora = $this->daoProduto->getSeguradora();
			
			if(is_array($seguradora)){

				foreach ($seguradora as $dadosSeguradora){
					$this->id_seguradora = $dadosSeguradora['emboid'];
				}

			}else{
				//seta mensagem de erro
				$this->erro['mensagem'][] = 'Não existe seguradora cadastrada com beneficio Seguro';
			}
			
			
			//verifica se os dados obrigatórios de entrada estão preenchidos
			$validaEntradaDados = $this->validarEntradaDados();
			
			//se houve um ou mais dados de entrada não informados, retorna um array com o status e a mensagem de erro
			if(count($validaEntradaDados) > 0){
			
				$resposta['status'] = "Erro";
					
				return array_merge($resposta, $validaEntradaDados);
			
			}else{

				//Verifica se já existe uma proposta para a cotação recebida como parâmetro.
				//A proposta existente deve ser para o mesmo contrato, chassi e mesma placa do veiculo
				$dadosProposta = new stdClass();
				$dadosProposta->seguradora    = $this->id_seguradora;
				$dadosProposta->num_contrato  = $this->getContratoNumero();
				$dadosProposta->num_cotacao   = $this->getCotacaoNumero();
				$dadosProposta->placa         = $this->getVeiculoPlaca();
				$dadosProposta->chassi        = $this->getVeiculoChassi();
				$codigoCorretor = '';
				
				$existeProposta = $this->dao->verificarPropostaExistente($dadosProposta);
				
				if(is_object($existeProposta)){
						
					//retorna com os dados encontrados
					$resposta['status'] = "Sucesso";
					$resposta['mensagem'] = $existeProposta->pspretdescricao;
					$resposta['proposta_numero'] = $existeProposta->pspretproposta;
						
					return $resposta;
				}

				//recupera e valida codigo da UF
				$cod_uf = $this->dao->getCodigoUf($this->getClienteUf());
				
				if(is_object($cod_uf)){
					
					$this->cd_uf = $cod_uf->cod_uf;
								
				}else{
					$this->erro['mensagem'][] = "Código de UF não encontrado";
				}

				//recupera e valida forma de pagamento
				$forma_pagamento = $this->dao->getCodigoFormaPagamento($this->getFormaPagamento());
                $fp = fopen("apu.txt","w+");
                fwrite($fp,$this->getFormaPagamento());
                fclose($fp);
				if(is_object($forma_pagamento)){

					$this->cd_forma_pagamento_pparcela = $forma_pagamento->cod_forma_seguradora;

				}else{
					$this->erro['mensagem'][] = "Código da forma de pagamento não encontrado";
				}


				if($this->getCorretor() == ''){
					//Se o indicador vier do CRM como vazio, o sistema
					//Recupera o corretor que estiver cadastrado como padrão para enviar à seguradora destino

					$codigoCorretor = $this->getCorretorDefault();
				}
				else{
					$codigoCorretor = $this->getCorretor();
				}

				if(empty($this->erro)){
					$idRevendaCorretor = $this->daoProduto->buscaIdRevendaCorretor($codigoCorretor);

					if($idRevendaCorretor){

						$resultRevenda = pg_fetch_object($idRevendaCorretor);
						
						$idRevendaCorretor = (int) $resultRevenda->psccodseg;

					}
					else{
						$this->erro['mensagem'][] = "Nao foi possivel recuperar o corretor da seguradora.";
					}
				}
				
				if(empty($this->erro)){
					//verifica se o cod da cotação existe 
					$cod_cotacao = $this->daoProduto->getCodigoCotacao($this->getCotacaoNumero(),$idRevendaCorretor);
					


					if($cod_cotacao){
						if (pg_num_rows($cod_cotacao) > 0) {

							$result = pg_fetch_object($cod_cotacao);

							$resultCotacao = $this->daoProduto->getCodigoCotacaoCorretor($this->getCotacaoNumero(),$idRevendaCorretor);

							if(pg_num_rows($resultCotacao) > 0) {

								$this->cod_cotacao = $result->cod_cotacao;
							}else if(pg_num_rows($resultCotacao) == 0) {
								$this->erro['mensagem'][] = "O corretor informado é diferente do corretor da cotação";
							}
							
						}else{
							$this->erro['mensagem'][] = "Código da cotação não encontrado";
						}

					}else{
						$this->erro['mensagem'][] = "Código da cotação não encontrado";
					}
				}
				//verifica se o número do contrato existe
				//$verifica_contrato = $this->dao->validarContratoExistente($this->getContratoNumero()); 
				
				/*if(!is_object($verifica_contrato)){
					
					$this->erro['mensagem'][] = "Número de contrato não encontrado";
				}*/
				

				if(empty($this->erro)){
						
					//instancia a o WS  da seguradora
					$integracaoSeguradora = new IntegracaoSeguradora();
					$integracaoSeguradora->idSeguradora = $this->id_seguradora;
					$integracaoSeguradora->setParametros();
					//$this->id_revenda = $integracaoSeguradora->id_revenda;
					$this->id_revenda = $idRevendaCorretor;
					$this->nm_usuario = $integracaoSeguradora->nm_usuario;

					//grava os dados de tentativas de envio no bd
					$dadosParaEnvio = new stdClass();
						
					$dadosParaEnvio->cod_cotacao   = $this->cod_cotacao;
					$dadosParaEnvio->seguradora    = $this->id_seguradora;
					$dadosParaEnvio->num_contrato  = $this->getContratoNumero();
					$dadosParaEnvio->id_revenda	   = $idRevendaCorretor;	
					//$dadosParaEnvio->id_revenda    = $this->id_revenda;
					$dadosParaEnvio->nm_usuario    = $this->nm_usuario;
					$dadosParaEnvio->tipo_seguro   = $this->getClienteSeguroTipo();
					$dadosParaEnvio->cliente_nome  = $this->getClienteNome();
					$dadosParaEnvio->cliente_sexo  = $this->getClienteSexo();
					$dadosParaEnvio->estado_civil  = $this->getClienteEstadoCivil();
					$dadosParaEnvio->dt_nasc       = $this->getClienteDataNascimento();
					$dadosParaEnvio->pep1          = $this->getClientePep1();
					$dadosParaEnvio->pep2          = $this->getClientePep2();
					$dadosParaEnvio->ddd_fone_res  = $this->getClienteResidencialDdd();
					$dadosParaEnvio->fone_res      = $this->getClienteResidencialFone();
					$dadosParaEnvio->ddd_celular   = $this->getClienteCelularDdd();
					$dadosParaEnvio->num_celular   = $this->getClienteCelularFone();
					$dadosParaEnvio->cliente_email = $this->getClienteEmail();
					$dadosParaEnvio->cliente_end   = $this->getClienteEndereco();
					$dadosParaEnvio->end_numero    = $this->getClienteEnderecoNumero();
					$dadosParaEnvio->complemento   = $this->getClienteComplemento();
					$dadosParaEnvio->cidade        = $this->getClienteCidade();
					$dadosParaEnvio->cd_uf         = $this->cd_uf;
					$dadosParaEnvio->profissao     = $this->getClienteProfissao();
					$dadosParaEnvio->placa         = $this->getVeiculoPlaca();
					$dadosParaEnvio->chassi        = $this->getVeiculoChassi();
					$dadosParaEnvio->veiculo_util  = $this->getVeiculoUtilizacao();
					$dadosParaEnvio->forma_pag     = $this->cd_forma_pagamento_pparcela;

					
					
					//seta o id na variável, recupera depois para update de retorno da seguradora ou de msg erro
					$this->id_envio_dados = $this->dao->setDadosEnvioWs($dadosParaEnvio);
		
					//recupera a classe do produto com beneficio seguro
					$classeProduto = $this->daoProduto->getClasseProduto($this->getClasseProduto(), $this->id_seguradora);
						
					if(!is_array($classeProduto)){

						//seta mensagem de erro
						$msgClasse = 'Classe do produto não está relacionada com beneficio Seguro';
							
						$this->erro['mensagem'][] = $msgClasse;

						//recupera mensagem de erro na tabela produto_seguro_mensagens e grava no log
						$msg_log = $this->daoProduto->getMensagem(504);

						if(is_object($msg_log)){

							$dadosRetornoWs = new stdClass();
							$dadosRetornoWs->cd_retorno = $msg_log->msg_cod;
							$dadosRetornoWs->nm_retorno = $msgClasse;

							//atualiza a tabela produto_seguro_cotacao com a mensagem
							$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($dadosRetornoWs, $this->id_envio_dados);
						}
					}
				
                    $cd_produto = $this->getCdProduto();
                    $retornoSeguradora = $integracaoSeguradora->enviarDadosWs('PesquisaParcelamento', "<i4proerp><parcelamento cd_produto='$cd_produto'/></i4proerp>");
                    $xmlRetorno = new stdClass();
                    $xmlRetorno->xml_retorno = trim(preg_replace("/(\'|'')/",'-', $retornoSeguradora));        
                    $objXML = new SimpleXMLElement($retornoSeguradora);
                    $retornoSeguradora = get_object_vars($objXML);
                    $retorno = get_object_vars($retornoSeguradora['retorno']);
                    $cod_retorno = $retorno['@attributes']['cd_retorno'] + 0;
                    
                    //print_r($parcelamento);
                    //throw new Exception('PesquisaParcelamento');
                    //exit;
                    
                    //Nao é um retorno com sucesso
                    if($cod_retorno != 0){
                        $this->erro['mensagem'][] = 'Houve algum erro ao chamar o serviço: PesquisaParcelamento. Entre em contato com o Administrador do sistema.';
                    } else{
                        $indice = 0;
                        $parcelamento = $retorno['parcelamento'];
                        $count  = count($parcelamento);
                        
                        if($count > 0){
                            $indice = $count - 1;
                        }                       
                        
                        $attributes = get_object_vars($parcelamento[$indice]);
                        $this->setIdProdutoParcPremio($attributes['@attributes']['id_produto_parc_premio']);
                        }
                    }
				
                //print_r($this->id_produto_parcela_premio);
                //throw new Exception('PesquisaParcelamento');
                //exit;

				if(empty($this->erro)){
					//recupera layout XML e o serviço a ser chamado no Ws de acordo a seguradora informada
					$layoutXmlEntradaDadosWs = $this->daoProduto->getLayoutXmlEntrada($this->id_seguradora, 'gerarpropostaautoconfiguravel','ENTRADA');
					
					if(is_array($layoutXmlEntradaDadosWs)){

						//faz a montagem do XML fora do padrão
						$xmlEntradaDados = $this->montarLayoutXml($layoutXmlEntradaDadosWs);
												
						$xmlEnvio  = new stdClass();
						$xmlEnvio->xml_envio = trim($xmlEntradaDados);
						
						//grava o xml gerado para envio
						$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($xmlEnvio, $this->id_envio_dados);
						
						
					}else{
						//seta mensagem de erro
						$this->erro['mensagem'][] = 'Seguradora não possui layout cadastrado';
					}
					//print("aqui");exit;

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
                                $proposta_auto = get_object_vars($identificacao['gerar_proposta_auto_configuravel']);
                            
							$dadosRetorno->nr_cotacao_i4pro = $proposta_auto['@attributes']['nr_cotacao_i4pro'];
							$dadosRetorno->id_proposta = $proposta_auto['@attributes']['id_proposta'];
							$dadosRetorno->id_endosso = $proposta_auto['@attributes']['id_endosso'];
							
							//transforma objeto em array
							$retorno = get_object_vars($retornoSeguradora['retorno']);

							//recupera os dados de retorno
							$dadosRetorno->cd_retorno = (int)$retorno['@attributes']['cd_retorno'];
							$dadosRetorno->nm_retorno = utf8_decode($retorno['@attributes']['nm_retorno']);
							
							$this->erroTipo = $this->validarRetornoDadosWs($dadosRetorno);
							
							//se houve erro de tipagem  grava a mensagem na tabela e retorna a mensagem de erro
							if($this->erroTipo['mensagem']){
									
								$dadosRetorno = "";
								$dadosRetorno->cd_retorno = NULL;
								$dadosRetorno->nm_retorno = $this->erroTipo['mensagem'];
									
								//atualiza a tabela produto_seguro_cotacao com os dados de retorno do WS
								$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($dadosRetorno, $this->id_envio_dados);
									
								$resposta['status']   = 'Erro';
								$resposta['mensagem'] = $this->erroTipo['mensagem'];
								
								//fecha transação do banco de dados
								$this->dao->commit();
									
								return $resposta;
							}
							
							//atualiza a tabela produto_seguro_cotacao com os dados de retorno do WS
							$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($dadosRetorno, $this->id_envio_dados);
							
							if($dadosRetorno->cd_retorno === 0){
							
								$resposta['status']   = "Sucesso";
								$resposta['mensagem'] = "Processamento efetuado com sucesso";
								$resposta['proposta_numero'] = $dadosRetorno->id_proposta;
												
							}else{
									
								if(isset($dadosRetorno->cd_retorno)){
							
									$resposta['status'] = "Erro";
							
									//busca mensagem de/para
									$msg_de_para = $this->daoProduto->getMensagem($dadosRetorno->cd_retorno);
									
									if(is_object($msg_de_para)){
										$resposta['mensagem']  = $msg_de_para->msg_seguradora;
									}else{
										$resposta['mensagem']  = 'Negado pela seguradora';
									}
					
								}		
							}
							
						}elseif(isset($retornoSeguradora['status'])){
								
							$resposta['status']   = $retornoSeguradora['status'];
							$resposta['mensagem'] = $retornoSeguradora['mensagem'];
								
						}else{

							$resposta['status']   = 'Erro';
							$resposta['mensagem'] = "Retorno Ws desconhecido";
						}
						
						//fecha transação do banco de dados
						$this->dao->commit();

						return $resposta;
					}
				}

			}
			
			$resposta['status'] = 'Erro';
			
			//insere status de erro na primeira posição do array de erros para verificação
			$this->erro = array_merge($this->erro, $resposta);
			
			//fecha transação do banco de dados
			$this->dao->commit();
			
			return $this->erro;
			
		}catch (Exception $e){
			
			//fecha transação do banco de dados
			$this->dao->commit();
			
			return $e->getMessage();
		}
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
				
			//montar string de dados (fora do padrão XML) para envio ao WS
			$xml = '';
			foreach ($arrayLayout as $dadosXml){
				//não envia a tag se não tiver dados (WS compreende como nulo)
				if($this->$dadosXml['tag'] != NULL){
					$xml .= $dadosXml['tag'].'="'.$this->$dadosXml['tag'].'" ';
					}
				}

            //Novos campos conforme manual da Usebens
                $dadosParaEnvio = new stdClass();
                $dadosParaEnvio->id_banco = '';
                $dadosParaEnvio->id_tipo_conta = '';
                $dadosParaEnvio->num_agencia = '';
                $dadosParaEnvio->num_dg_agencia = '';
                $dadosParaEnvio->num_conta = '';
                $dadosParaEnvio->num_dg_conta = '';
            $dadosParaEnvio->id_produto_parc_premio = $this->getIdProdutoParcPremio();//Codigo da Usebens: <parcelamento id_produto_parc_premio="686" ds_parcelamento="1+11"/>
                $xml .= 'id_banco="' . $dadosParaEnvio->id_banco .'" ';
                $xml .= 'id_tipo_conta="' .$dadosParaEnvio->id_tipo_conta .'" ';
                $xml .= 'num_agencia="' .$dadosParaEnvio->num_agencia .'" ';
                $xml .= 'num_dg_agencia="' .$dadosParaEnvio->num_dg_agencia .'" ';
                $xml .= 'num_conta="' .$dadosParaEnvio->num_conta .'" ';
                $xml .= 'num_dg_conta="' .$dadosParaEnvio->num_dg_conta .'" ';
                $xml .= 'id_produto_parc_premio="' .$dadosParaEnvio->id_produto_parc_premio .'" ';
            
                $xml = '<i4proerp><proposta_auto_configuravel ' . $xml . ' /></i4proerp> ';
			
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
	
		
		if($this->getCotacaoNumero() === NULL){
			$this->erro['mensagem'][] = 'Número da cotação é obrigatório';
		}else{
				
			if(!is_numeric($this->getCotacaoNumero())){
				$this->erro['mensagem'][] = 'Código da cotação deve ser do tipo inteiro (envio)';
			}
		}
	
		if($this->getContratoNumero() === NULL){
			$this->erro['mensagem'][] = 'Número do contrato é obrigatório';
		}else{
			
			if(!is_numeric($this->getContratoNumero())){
				$this->erro['mensagem'][] = 'Código do contrato deve ser do tipo inteiro (envio)';
			}
		}
		
		if($this->getClienteNome() === NULL || $this->getClienteNome() == ""){
			$this->erro['mensagem'][] = 'Nome do cliente é obrigatório';
		}
		
		if($this->getClienteSexo() === NULL){
			$this->erro['mensagem'][] = 'Sexo do cliente / Segurado é obrigatório';
		}else{
			
			if(!is_numeric($this->getClienteSexo())){
				$this->erro['mensagem'][] = 'Sexo do Cliente / Segurado deve ser do tipo inteiro (envio)';
			}	
		}
	
		if($this->getClienteEstadoCivil() === NULL){
			$this->erro['mensagem'][] = 'Estado civil do cliente é obrigatório';
		}else{
			
			if(!is_numeric($this->getClienteEstadoCivil())){
				$this->erro['mensagem'][] = 'Codigo de identificação de estado civil deve ser do tipo inteiro (envio).';
			}	
		}
		
		if($this->getClienteProfissao() === NULL){
			$this->erro['mensagem'][] = 'Profissão do cliente é obrigatório';
		}else{
			
			if(!is_numeric($this->getClienteProfissao())){
				$this->erro['mensagem'][] = 'Código da profissão do segurado deve ser do tipo inteiro (envio)';
			}elseif(strlen($this->getClienteProfissao()) > 4){
				$this->erro['mensagem'][] = 'Código da profissão inválida';
			}
					
		}
		
		if($this->getClienteDataNascimento() === NULL){
			$this->erro['mensagem'][] = 'Data de nascimento do cliente é obrigatório';
		}else{
			
			//validar formato da data
			$dataValida = $this->produtoComSeguro->validarData($this->getClienteDataNascimento());
				
			if(empty($dataValida)){
				$this->erro['mensagem'][] = 'Data de nascimento inválida. A data deve estar nos formatos: xx-xx-xxxx ou xx/xx/xxxx';
			}else{
				$this->setClienteDataNascimento($dataValida);
			}
		}
		
		if($this->getClientePep1() === NULL || $this->getClientePep1() == ""){
			$this->erro['mensagem'][] = 'Nome da 1ª pessoa exposta politicamente é obrigatório';
		}
		
		if($this->getClientePep2() === NULL || $this->getClientePep2() == ""){
			$this->erro['mensagem'][] = 'Nome da 2ª pessoa exposta politicamente é obrigatório';
		}
		
		if($this->getClienteResidencialDdd() === NULL){
			$this->erro['mensagem'][] = 'DDD do telefone residencial do cliente é obrigatório';
		}else{
			
			if(!is_numeric($this->getClienteResidencialDdd())){
				$this->erro['mensagem'][] = 'Número de DDD do telefone residencial deve ser do tipo inteiro (envio)';
			}	
		}
		
		if($this->getClienteResidencialFone() === NULL){
			$this->erro['mensagem'][] = 'Telefone residencial do cliente é obrigatório';
		}
		
		if($this->getClienteCelularDdd() === NULL){
			$this->erro['mensagem'][] = 'DDD do telefone celular do cliente é obrigatório';
		}else{
			
			if(!is_numeric($this->getClienteCelularDdd())){
				$this->erro['mensagem'][] = 'Número de DDD do telefone celular deve ser do tipo inteiro (envio)';
			}	
		}
		
		if($this->getClienteCelularFone() === NULL){
			$this->erro['mensagem'][] = 'Telefone celular do cliente é obrigatório';
		}
		
		if($this->getClienteEmail() === NULL || $this->getClienteEmail() == ""){
			$this->erro['mensagem'][] = 'E-mail do cliente é obrigatório';
		}
		
		if($this->getClienteEndereco() === NULL || $this->getClienteEndereco() == ""){
			$this->erro['mensagem'][] = 'Endereço do cliente é obrigatório';
		}
				
		if($this->getClienteEnderecoNumero() === NULL || $this->getClienteEnderecoNumero() == ""){
			$this->erro['mensagem'][] = 'Número do endereço do cliente é obrigatório';
		}
		
		if($this->getClienteComplemento() === NULL || $this->getClienteComplemento() == ""){
			$this->erro['mensagem'][] = 'Complemento do endereço do cliente é obrigatório';
		}
		
		if($this->getClienteCidade() === NULL || $this->getClienteCidade() == ""){
			$this->erro['mensagem'][] = 'Cidade do endereço do cliente é obrigatório';
		}
		
		if($this->getClienteUf() === NULL || $this->getClienteUf() == ""){
			$this->erro['mensagem'][] = 'UF do endereço do cliente é obrigatório';
		}
		
		if($this->getVeiculoPlaca() === NULL || $this->getVeiculoPlaca() == ""){
			$this->erro['mensagem'][] = 'Placa do veículo é obrigatório';
		}
		
		if($this->getVeiculoChassi() === NULL || $this->getVeiculoChassi() == ""){
			$this->erro['mensagem'][] = 'Chassi do veículo é obrigatório';
		}
		
		if($this->getVeiculoUtilizacao() === NULL){
			$this->erro['mensagem'][] = 'Utilização do veículo é obrigatório';
		}else{
			
			if(!is_numeric($this->getVeiculoUtilizacao())){
				$this->erro['mensagem'][] = 'Código de utilização do veiculo deve ser do tipo inteiro (envio)';
			}	
		}
		
		if($this->getClienteSeguroTipo() === NULL){
			$this->erro['mensagem'][] = 'Tipo de segurado é obrigatório';
		}else{
			
			if(!is_numeric($this->getClienteSeguroTipo())){
				$this->erro['mensagem'][] = 'Identificação do tipo de segurado deve ser do tipo inteiro (envio)';
			}	
		}
		
		if($this->getFormaPagamento() === NULL){
			$this->erro['mensagem'][] = 'Forma de pagamento é obrigatório';
		}else{
			
			if(!is_numeric($this->getFormaPagamento())){
				$this->erro['mensagem'][] = 'Forma de pagamento deve ser do tipo inteiro (envio)';
			}	
		}
		
		if($this->getClasseProduto() === NULL){
			$this->erro['mensagem'][] = 'Classe do produto é obrigatório';
		}else{
			
			if(!is_numeric($this->getClasseProduto())){
				$this->erro['mensagem'][] = 'Classe do produto deve ser do tipo inteiro (envio)';
			}	
		}
		
		return $this->erro;
	
	}
	
	
	/**
	 * Verifica se os dados de retorno do WS são compatíveis com a tipagem dos campos da tabela,
	 * Seta mensagem de erro no atributo
	 *
	 * @param object $dadosRetorno
	 */
	private function validarRetornoDadosWs($dadosRetorno){
	
		//validar tipagem das variáveis de retorno dos dados do WS
		if(!is_numeric($dadosRetorno->id_revenda)){
			$this->erroTipo['mensagem'] = 'Identificação do Canal de vendas deve ser do tipo inteiro (retorno)';
	
		}elseif(!is_numeric($dadosRetorno->id_proposta)){
			$this->erroTipo['mensagem'] = 'Numero da proposta deve ser do tipo inteiro (retorno)';
	
		}elseif(!is_numeric($dadosRetorno->id_endosso)){
			$this->erroTipo['mensagem'] = 'Identificação de endosso da proposta deve ser do tipo inteiro (retorno)';
	
		}elseif(!is_numeric($dadosRetorno->cd_retorno)){
			$this->erroTipo['mensagem'] = 'Código da mensagem de retorno deve ser do tipo inteiro (retorno)';
		}
	
		return $this->erroTipo;
	}
	

    //sets e gets
	public function setCotacaoNumero($valor){
		$this->nr_cotacao_i4pro = $valor;
	}
	public function getCotacaoNumero(){
		return $this->nr_cotacao_i4pro;
	}
	
	public function setContratoNumero($valor){
		$this->contrato_numero = $valor;
	}
	public function getContratoNumero(){
		return $this->contrato_numero;
	}
	
	public function setClienteNome($valor){
		$this->nm_pessoa = $valor;
	}
	public function getClienteNome(){
		return $this->nm_pessoa;
	}
	
	public function setClienteSexo($valor){
		$this->id_sexo = $valor;
	}
	public function getClienteSexo(){
		return $this->id_sexo;
	}
	
	public function setClienteEstadoCivil($valor){
		$this->id_estado_civil = $valor;
	}
	public function getClienteEstadoCivil(){
		return $this->id_estado_civil;
	}
	
	public function setClienteProfissao($valor){
		$this->cd_profissao = $valor;
	}
	public function getClienteProfissao(){
		return $this->cd_profissao;
	}
	
	public function setClienteDataNascimento($valor){
		$this->dt_nascimento = $valor;
	}
	public function getClienteDataNascimento(){
		return $this->dt_nascimento;
	}
	
	public function setClientePep1($valor){
		$this->nm_resp1 = $valor;
	}
	public function getClientePep1(){
		return $this->nm_resp1;
	}
	
	public function setClientePep2($valor){
		$this->nm_resp2 = $valor;
	}
	public function getClientePep2(){
		return $this->nm_resp2;
	}
	
	public function setClienteResidencialDdd($valor){
		$this->nr_ddd_res = $valor;
	}
	public function getClienteResidencialDdd(){
		return $this->nr_ddd_res;
	}
	
	public function setClienteResidencialFone($valor){
		$this->nm_fone_res = $valor;
	}
	public function getClienteResidencialFone(){
		return $this->nm_fone_res;
	}

	public function setClienteCelularDdd($valor){
		$this->nr_ddd_cel = $valor;
	}
	public function getClienteCelularDdd(){
		return $this->nr_ddd_cel;
	}
	
	public function setClienteCelularFone($valor){
		$this->nm_fone_cel = $valor;
	}
	public function getClienteCelularFone(){
		return $this->nm_fone_cel;
	}
	
	public function setClienteEmail($valor){
		$this->nm_email = $valor;
	}
	public function getClienteEmail(){
		return $this->nm_email;
	}
	
	public function setClienteEndereco($valor){
		$this->nm_endereco = $valor;
	}
	public function getClienteEndereco(){
		return $this->nm_endereco;
	}
	
	public function setClienteEnderecoNumero($valor){
		$this->nr_endereco = $valor;
	}
	public function getClienteEnderecoNumero(){
		return $this->nr_endereco;
	}
	
	public function setClienteComplemento($valor){
		 $this->nm_complemento = $valor;
	}
	public function getClienteComplemento(){
		return $this->nm_complemento;
	}
	
    public function setClienteCidade($valor){
		 $this->nm_cidade = $valor;
	}
	public function getClienteCidade(){
		return $this->nm_cidade;
	}
	
	public function setClienteUf($valor){
		$this->uf = $valor;
	}
	public function getClienteUf(){
		return $this->uf;
	}
	
	public function setVeiculoPlaca($valor){
		$this->nm_placa = $valor;
	}
	public function getVeiculoPlaca(){
		return $this->nm_placa;
	}
	
	public function setVeiculoChassi($valor){
		$this->nm_chassis = $valor;
	}
	public function getVeiculoChassi(){
		return $this->nm_chassis;
	}
	
	public function setVeiculoUtilizacao($valor){
		$this->id_auto_utilizacao = $valor;
	}
	public function getVeiculoUtilizacao(){
		return $this->id_auto_utilizacao;
	}
	
	public function setClienteSeguroTipo($valor){
		$this->dv_segurado_proprietario = $valor;
	}
	public function getClienteSeguroTipo(){
		return $this->dv_segurado_proprietario;
	}
	
	public function setFormaPagamento($valor){
		$this->forma_pagamento = $valor;
	}
	public function getFormaPagamento(){
		return $this->forma_pagamento;
	}
	
	public function setClasseProduto($valor){
		$this->classe_produto = $valor;
	}
	public function getClasseProduto(){
		return $this->classe_produto;
	}
	
	public function getCorretor(){
		return $this->corretor_indicador;
	}

	public function setCorretor($corretor){
		$this->corretor_indicador = $corretor;
	}
    
    public function getVigenciaContrato(){
		return $this->vigencia;
	}

	public function setVigenciaContrato($vigencia){
		$this->vigencia = $vigencia;
	}
    
    public function getCdProduto(){
		return $this->cd_produto;
	}

	public function setCdProduto($cd_produto){
		$this->cd_produto = $cd_produto;
	}
    
    public function setIdProdutoParcPremio($id_produto_parc_premio){
        $this->id_produto_parc_premio = $id_produto_parc_premio;
    }
    
    public function getIdProdutoParcPremio(){
        return $this->id_produto_parc_premio;
    }
}