<?php 
/**
 * @file SeguroCotacao.class.php
 * @author marcioferreira
 * @version 31/10/2013 11:21:55
 * @since 31/10/2013 11:21:55
 * @package SASCAR SeguroCotacao.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

//manipula os dados no BD
require(_MODULEDIR_ . "Produto_Com_Seguro/DAO/SeguroCotacaoDAO.class.php");

//WS com a seguradora
require(_MODULEDIR_ . "Produto_Com_Seguro/Action/IntegracaoSeguradora.class.php");

/**
 * Responsável em validar os dados de entrada para enviar uma cotação para a seguradora e
 * retornar a resposta do WS. 
 *
 * @author marcioferreira
 */
class SeguroCotacao {
	
	/**
	 * Fornece acesso aos dados do BD necessários para o módulo
	 * @property SeguroCotacaoDAO
	 */
	private $dao;

	private $erro;
	private $erroTipo;
	private $id_envio_dados;
	
	private $cd_tipo_pessoa;
	private $nr_cpf_cnpj_cliente;
	private $nr_cep;
	private $cd_fipe;
	private $nr_ano_auto;
	private $dv_auto_zero;
	private $id_auto_combustivel;
	private $uso_veiculo;
	private $finalidade_uso_veiculo;
	private $classe_produto;
	private $id_seguradora;
	private $cd_categoria_tarifaria;
	private $id_revenda;
	private $nm_usuario;
	private $retornoXmlSeguro;
	private $identificador_corretor;
    private $id_produto_cobertura;
    private $valor_lmi_cobertura;
    private $valor_franquia_cobertura;
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
		
		$this->dao  = new SeguroCotacaoDAO($connSiggo);
		$this->daoProduto = new ProdutoComSeguroDAO($connSiggo);
	}
	
	/**
	 * Efetua validações dos dados de entrada, envia para o WS da seguradora e retorna o resultado
	 * 
	 * @return array
	 */
	public function processarCotacao(){
		try{
			
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
			
			
			if(empty($this->erro)){
				//seta o identificador do corretor
				$this->identificador_corretor = $this->getCorretor();

				//parâmetros de entrada do uso do veículo
				$codigoUsoVeiculo = Array(1,2,3);

				//verifica se os dados obrigatórios de entrada estão preenchidos
				$validaEntradaDados = $this->validarEntradaDados();
				
				//se houve um ou mais dados de entrada não informados, retorna um array com o status e a mensagem de erro
				if(count($validaEntradaDados) > 0){
						
					$resposta['status'] = "Erro";
					
					return array_merge($resposta, $validaEntradaDados);
						
				}else{

					//recupera categoria tarifária pelo cod Fipe informado
					$dadosModeloPorFipe = $this->dao->getCategoriaTarifariaCodFipe($this->getCodigo_fipe(), $this->getFinalidade_uso_veiculo());

					if(!empty($dadosModeloPorFipe)){
							
						//Se uso do veículo for igual a 1, 2 ou 3 retorna o campo mlocatbase_codigo da tabela modelo
						if(in_array($this->getUso_veiculo(), $codigoUsoVeiculo)){

							$this->cd_categoria_tarifaria = $dadosModeloPorFipe->tarifa_codigo;

						//Se não se o uso_veiculo for igual a 9 retorna o campo psctcodigo da tabela produto_seguro_categoriatarifaria
						}elseif($this->getUso_veiculo() == 9){

							$retorno =  $this->dao->getCategoriaTarifariaDadosModelo($this->getFinalidade_uso_veiculo(), $dadosModeloPorFipe->tarifa_procedencia);

							if(is_object($retorno)){
								
								$this->cd_categoria_tarifaria = $retorno->tarifa_codigo;
							}else{
								//seta mensagem de erro
								$this->erro['mensagem'][] = 'Categoria tarifária não encontrada para uso do veículo profissional';
							}
						
						}else{
							//seta mensagem de erro
							$this->erro['mensagem'][] = 'Uso do veiculo não encontrado';
						}
							
					}else{
						//seta mensagem de erro
						$this->erro['mensagem'][] = 'Categoria tarifária não encontrada para o código Fipe informado';
					}
					
					
					if(empty($this->erro)){
						
						//instancia o WS  da seguradora
						$integracaoSeguradora = new IntegracaoSeguradora();
						$integracaoSeguradora->idSeguradora = $this->id_seguradora;
						$integracaoSeguradora->setParametros();
						//$this->id_revenda = $integracaoSeguradora->id_revenda;
						$this->id_revenda = $this->getCorretor(); 
						$this->nm_usuario = $integracaoSeguradora->nm_usuario;
						
						//grava os dados de tentativas de envio no bd
						$dadosParaEnvio = new stdClass();
						$dadosParaEnvio->id_seguradora = $this->id_seguradora;
						//$dadosParaEnvio->id_revenda = $this->id_revenda;
						$dadosParaEnvio->id_revenda = $this->getCorretor();
						$dadosParaEnvio->nm_usuario = $this->nm_usuario;
						$dadosParaEnvio->tipo_pessoa = $this->getTipoPessoa();
						$dadosParaEnvio->cpf_cgc = $this->getCpf_cgc();
						$dadosParaEnvio->cep = $this->getCep();
						$dadosParaEnvio->cod_fipe = $this->getCodigo_fipe();
						$dadosParaEnvio->ano_veiculo = $this->getAno_modelo();
						$dadosParaEnvio->carro_zero = $this->getCarro_zero();
						$dadosParaEnvio->tipo_combustivel = $this->getTipo_combustivel();
						$dadosParaEnvio->categoria_tarifaria = $this->cd_categoria_tarifaria;
						$dadosParaEnvio->corretor = $this->getCorretor();                        
                        $dadosParaEnvio->id_produto_cobertura = $this->getIdProdutoCobertura();
                        $dadosParaEnvio->vl_lmi_cobertura = $this->getValorLmiCobertura();
                        $dadosParaEnvio->vl_franquia_cobertura = $this->getValorFranquiaCobertura();
						
						//seta o id na variável, recupera depois para update de retorno da seguradora ou de msg erro
						$this->id_envio_dados = $this->dao->setDadosEnvioWs($dadosParaEnvio);

						//recupera a classe do produto com beneficio seguro
						$classeProduto = $this->daoProduto->getClasseProduto($this->getClasseProduto(), $this->id_seguradora);

						//valida se a classe está relacionada com benefício seguro
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
					}
						
						
					if(empty($this->erro)){
								
						//recupera layout XML e o serviço a ser chamado no Ws de acordo a seguradora informada
						$layoutXmlEntradaDadosWs = $this->daoProduto->getLayoutXmlEntrada($this->id_seguradora, 'GERARCOTACAOAUTOCONFIGURAVEL','ENTRADA');
						
						if(is_array($layoutXmlEntradaDadosWs)){
								
							//faz a montagem do XML fora do padrão
							$xmlEntradaDados = $this->montarLayoutXml($layoutXmlEntradaDadosWs);
							
							$xmlEnvio  = new stdClass();
							$xmlEnvio->xml_envio = trim($xmlEntradaDados);
															
							//grava o xml gerado para envio
							$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($xmlEnvio, $this->id_envio_dados);
								
						} else{
							//seta mensagem de erro
							$this->erro['mensagem'][] = 'Seguradora não tem layout cadastrado';
						}							
							
						if(empty($this->erro)){
                            //Essa variavel é preenchida no momento que o layout é montado.
                            //function: montarLayoutXml()

							//envia dados para o WS da seguradora
							$retornoSeguradora = $integracaoSeguradora->enviarDadosWs($layoutXmlEntradaDadosWs[0]['servico'], $xmlEntradaDados);
							
                            //$this->id_revenda = $this->getCorretor();
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
                                    $cotacao_auto = get_object_vars($identificacao['gerar_cotacao_auto_configuravel']);
								
								$dadosRetorno->nr_cotacao_i4pro = (int) $cotacao_auto['@attributes']['nr_cotacao_i4pro'];
								$dadosRetorno->vl_premio_tarifario = (float) $cotacao_auto['@attributes']['vl_premio_tarifario'];
								$dadosRetorno->vl_lmi = $cotacao_auto['@attributes']['vl_lmi'];
								$dadosRetorno->vl_iof = $cotacao_auto['@attributes']['vl_iof'];
								$dadosRetorno->vl_franquia = $cotacao_auto['@attributes']['vl_franquia'];

								//transforma objeto em array
								$retorno = get_object_vars($retornoSeguradora['retorno']);
									
								//recupera os dados de retorno
								$dadosRetorno->cd_retorno = (int) $retorno['@attributes']['cd_retorno'];
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
									
									//efetiva transacao
									$this->dao->commit();
									
									return $resposta;
								}

								//atualiza a tabela produto_seguro_cotacao com os dados de retorno do WS se não houve erro de tipagem
								$insereDadosRetorno = $this->dao->atualizarDadosRetornoWs($dadosRetorno, $this->id_envio_dados);

								if($dadosRetorno->cd_retorno === 0){
										
									$resposta['status']   = "Sucesso";
									$resposta['mensagem'] = "Processamento efetuado com sucesso";
									$resposta['orcamento_numero'] = $dadosRetorno->nr_cotacao_i4pro;
									$resposta['valor_premio']     = $dadosRetorno->vl_premio_tarifario;
										
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
							
							$this->dao->commit();
							
							return $resposta;
						}
					}

				}
					
			}
			
			$resposta['status'] = 'Erro';
			
			$this->erro = array_merge($this->erro, $resposta);
			
			$this->dao->commit();
			
			return $this->erro;

		}catch (Exception $e){
			
			$this->dao->commit();
			
			return $e->getMessage();
		}
	}
	
	/**
	 * Valida os campos obrigatórios e retorna mensagem.
	 * 
	 * @return string
	 */
	private function validarEntradaDados(){
			
		if($this->getTipoPessoa() === NULL){
			$this->erro['mensagem'][] = 'Tipo de pessoa é obrigatório';
		}else{
			
			if(!is_numeric($this->getTipoPessoa())){
				$this->erro['mensagem'][] = 'Identificação do tipo de pessoa deve ser do tipo inteiro (envio)';
			}
		}
		
		if($this->getCpf_cgc() === NULL){
			$this->erro['mensagem'][] = 'CPF/CNPJ é obrigatório';
		}else{
			
			if(!is_numeric($this->getCpf_cgc())){
				$this->erro['mensagem'][] = 'CPF/CNPJ do cliente deve conter somente números';
			}
		}
			
		if($this->getCep() === NULL){
			$this->erro['mensagem'][] = 'CEP é obrigatório';
		}else{
		
			if(!is_numeric($this->getCep())){
				$this->erro['mensagem'][] = 'CEP do Cliente / Segurado deve ser do tipo inteiro (envio)';
			}
		}
			
		if($this->getCodigo_fipe() === NULL){
			$this->erro['mensagem'][] = 'Código FIPE é obrigatório';
		}
		
		if($this->getAno_modelo() === NULL){
			$this->erro['mensagem'][] = 'Ano do modelo do veículo é obrigatório';
		}else{
			
			if(!is_numeric($this->getAno_modelo())){
				$this->erro['mensagem'][] = 'Ano de fabricação do veiculo deve ser do tipo inteiro (envio)';
			}
		}
			
		if($this->getCarro_zero() === NULL){
			$this->erro['mensagem'][] = 'Indicador de carro zero km é obrigatório';
		}else{

			if(!is_numeric($this->getCarro_zero())){
				$this->erro['mensagem'][] = 'Identificação de veiculo zero km deve ser do tipo inteiro (envio)';
			}
		}
			
		if($this->getTipo_combustivel() === NULL){
			$this->erro['mensagem'][] = 'Tipo de combustível é obrigatório';
		}else{

			if(!is_numeric($this->getTipo_combustivel())){
				$this->erro['mensagem'][] = 'Identificação do combustível deve ser do tipo inteiro (envio)';
			}
		}
			
		if($this->getUso_veiculo() === NULL){
			$this->erro['mensagem'][] = 'Uso do veiculo é obrigatório';
		}else{
			
			if(!is_numeric($this->getUso_veiculo())){
				$this->erro['mensagem'][] = 'Uso do veiculo deve ser do tipo inteiro (envio)';
			}
		}
			
		if($this->getUso_veiculo() == 9 && $this->getFinalidade_uso_veiculo() === NULL){
			$this->erro['mensagem'][] = 'Finalidade do uso do veiculo é obrigatório';
		}else{
			
			if(!is_numeric($this->getUso_veiculo())){
				$this->erro['mensagem'][] = 'Finalidade do uso do veiculo deve ser do tipo inteiro (envio)';
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
		
		}elseif(!is_numeric($dadosRetorno->nr_cotacao_i4pro)){
			$this->erroTipo['mensagem'] = 'Numero da cotação deve ser do tipo inteiro (retorno)';
		
		}elseif(!is_numeric($dadosRetorno->vl_premio_tarifario)){
			$this->erroTipo['mensagem'] = 'Valor do premio tarifário deve ser do tipo inteiro (retorno).';
		
		}elseif(!is_numeric($dadosRetorno->cd_retorno)){
			$this->erroTipo['mensagem'] = 'Código da mensagem de retorno deve ser do tipo inteiro (retorno)';
		}

		return $this->erroTipo;
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
				throw new Exception('Os dados para montar o XML não foram encontrados');				
			}
            
			//montar string de dados (fora do padrão XML) para envio ao WS
			$xml = '';
			foreach ($arrayLayout as $dadosXml){
				//não envia a tag se não tiver dados (WS compreende como nulo)
				if($this->$dadosXml['tag'] != NULL){
                    //SIGGO TAXI
					if(strtolower($dadosXml['tag']) == 'nm_usuario' && in_array((int)$this->cd_categoria_tarifaria, array(80,81))){
						$this->$dadosXml['tag'] = "sascarauto";
					} elseif(strtolower($dadosXml['tag']) == 'nm_usuario'){
                        $this->$dadosXml['tag'] = "sascarauto";
                    }
                    
					$xml .= $dadosXml['tag'].'="'.$this->$dadosXml['tag'].'" ';
				}
			}
 
			# BEGIN - Hardcode - fasttrack 85728 - Siggo Seguro Taxi
			# NR_MES_PERIODO_VIGENCIA, CD_PRODUTO - estão sendo inseridos
			# forçadamente (Hardcode), para atender a demanda de tempo;
			$dadosParaEnvio = new stdClass();
			$dadosParaEnvio->cd_produto = $this->getCdProduto();
			$dadosParaEnvio->nr_mes_periodo_vigencia = '';
                        $arraySeguro = array(211, 205, 238, 241, 249, 239, 246, 244, 237, 235, 231, 228, 225, 185, 187, 222, 192, 198, 202, 204, 206, 208, 212, 214,                        221, 200, 223, 226, 227, 229, 182, 183, 247, 190, 194, 209, 219, 189, 236, 248, 233, 210, 207, 240, 196, 186, 203, 197, 191, 234, 243, 245,                         242, 230, 224, 216, 232, 201, 199, 195, 193, 188, 220, 184, 218, 217, 213, 215, 121);
            
			if (in_array((int)$this->getClasseProduto(), $arraySeguro) || in_array((int)$this->cd_categoria_tarifaria, array(80,81))) # Siggo seguro
			{
				$dadosParaEnvio->nr_mes_periodo_vigencia = 	'12';
                
                //SIGGO TAXI
                if(in_array((int)$this->cd_categoria_tarifaria, array(80,81))){
                    //$dadosParaEnvio->cd_produto = '88';
                } else{
                    //$dadosParaEnvio->cd_produto = '88';
                }
			}
            
            if(strpos($xml, 'dv_auto_zero') === false){
                $xml .= 'dv_auto_zero="'.$this->getCarro_zero().'" ';
            }
            
            if(strpos($xml, 'cd_categoria_tarifaria') === false){
                $cd_categoria_tarifaria = (int) $this->cd_categoria_tarifaria;
                $xml .= 'cd_categoria_tarifaria="'.$cd_categoria_tarifaria.'" ';
            }
            
						
			# END - Hardcode - fasttrack 85728 - Siggo Seguro Taxi
            
            $xml .= 'cd_produto="'.$dadosParaEnvio->cd_produto.'" ';
            $xml .= 'id_produto_cobertura="'.$this->getIdProdutoCobertura().'" ';
            $xml .= 'vl_lmi_cobertura="'.$this->getValorLmiCobertura().'" ';
            $xml .= 'vl_franquia_cobertura="'.$this->getValorFranquiaCobertura().'" ';
            $xml .= 'nr_mes_periodo_vigencia="'.$dadosParaEnvio->nr_mes_periodo_vigencia.'" ';
            
                $xml = '<i4proerp><cotacao_auto_configuravel ' . $xml . ' /></i4proerp> ';
            
			return $xml;
				
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
	}
	
	
    //sets e gets
	public function setTipoPessoa($valor){
		$this->cd_tipo_pessoa = $valor;
	}
	
	public function getTipoPessoa(){
		return $this->cd_tipo_pessoa;
	}
	
	public function setCpf_cgc($valor){
		$this->nr_cpf_cnpj_cliente = $valor;
	}
	
	public function getCpf_cgc(){
		return $this->nr_cpf_cnpj_cliente;
	}
	
	public function setCep($valor){
		$this->nr_cep = $valor;
	}
	
	public function getCep(){
		return $this->nr_cep;
	}
	
	public function setCodigo_fipe($valor){
		$this->cd_fipe = $valor;
	}
	
	public function getCodigo_fipe(){
		return $this->cd_fipe;
	}

	public function setAno_modelo($valor){
		$this->nr_ano_auto = $valor;
	}
	
	public function getAno_modelo(){
		return $this->nr_ano_auto;
	}
	
	public function setCarro_zero($valor){
		$this->dv_auto_zero = $valor;
	}
	
	public function getCarro_zero(){
		return $this->dv_auto_zero;
	}

	public function setTipo_combustivel($valor){
		$this->id_auto_combustivel = $valor;
	}
	
	public function getTipo_combustivel(){
		return $this->id_auto_combustivel;
	}

	public function setUso_veiculo($valor){
		$this->uso_veiculo = $valor;
	}
	
	public function getUso_veiculo(){
		return $this->uso_veiculo;
	}
	
	public function setFinalidade_uso_veiculo($valor){
		$this->finalidade_uso_veiculo = $valor;
	}
	
	public function getFinalidade_uso_veiculo(){
		return $this->finalidade_uso_veiculo;
	}
	
	public function setClasseProduto($valor){
		$this->classe_produto = $valor;
	}
	
	public function getClasseProduto(){
		return $this->classe_produto;
	}
	
	public function setCorretor($identificadorCorretor){
		$this->identificador_corretor = $identificadorCorretor;
	}
	
	public function getCorretor(){
		return $this->identificador_corretor;
	}
    
    public function setIdProdutoCobertura($id_produto_cobertura){
		$this->id_produto_cobertura = $id_produto_cobertura;
	}
	
	public function getIdProdutoCobertura(){
		return $this->id_produto_cobertura;
	}
    
    public function setValorLmiCobertura($valor_lmi_cobertura){
		$this->valor_lmi_cobertura = $valor_lmi_cobertura;
	}
	
	public function getValorLmiCobertura(){
		return $this->valor_lmi_cobertura;
	}
    
    public function setValorFranquiaCobertura($valor_franquia_cobertura){
		$this->valor_franquia_cobertura = $valor_franquia_cobertura;
	}
	
	public function getValorFranquiaCobertura(){
		return $this->valor_franquia_cobertura;
	}
    
    public function setCdProduto($cd_produto){
		$this->cd_produto = $cd_produto;
	}
	
	public function getCdProduto(){
		return $this->cd_produto;
	}
}
