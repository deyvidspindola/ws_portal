<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
 * @version 09/09/2016
 * @since 09/09/2016
 * @package Core
 * @subpackage Classe Controlador do Boleto
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Boleto;

use infra\ComumController,
	infra\Helper\Response,
	infra\Helper\Mascara,
    module\Cliente\ClienteService,
    module\Boleto\BoletoModel,
    module\Boleto\Agente,
    module\Boleto\Santander,
    module\Boleto\Itau,
    module\Boleto\Caixa;

class BoletoController extends ComumController{
    
    public $response;
    private $cedente;
    private $model;
    

	/**
	 * Contrutor da classe
	 * 
	 * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 09/09/2016
	 * @param none
	 * @return none
     */
    public function __construct(){
        $this->response = new Response();
        $this->model    = new BoletoModel();
        $this->cedente  = new Agente('Sascar - Tecnologia e Seguran&ccedil;a Automotiva S/A', '03.112.879/0001-51', ' Alameda Araguaia, 2.104-11&#186; andar - Alphaville Comercial','Barueri','SP', 'CEP 06455-000');
    }
    
    /**
     * Gera o boleto conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 09/09/2016
     * @param array $params Array com os dados para gerar o boleto
     *                    
                array(
                    // Parâmetros obrigatórios 
                    'forcoid' => 84, //ID da Forma de cobrança - caso seja informado o forcoid, o cfbbanco deve ser 0 (zero)
                    'cfbbanco' => 33, //Código do banco - caso seja informado o cfbbanco, o forcoid deve ser 0 (zero)
                    'clioid' => 307743,
                    'dataVencimento' => date('Y-m-d'), //Susbtituir date('Y-m-d') pela data de vencimento no formato 'Y-m-d'
                    'valor' => 59.90, //Valor do documento
                    'sequencial' => 12345678901, // Até 12 dígitos - Numero do título                    
                    'carteira' => 101,
                    'ios' => 0, // Apenas para o Santander; IOS – Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)
                    
                    // Parâmetros opcionais
                    'numeroDocumento' => '',
                    'descontosAbatimentos' => 0, // (-) Desconto
                    'outrasDeducoes' => 0, // (-) Abatimento
                    'moraMulta' => 0,    
                    'outrosAcrescimos' => 0,
                    'valorCobrado' => 0, //Soma do valor com multas e acrescimos
     *          )
     * @param string $banco Nome do banco (minusculo, sem espaço. Ex: santander)
     * @return response ($response->dados = HTML/false)
     */
    public function gerarBoleto($params=array(), $banco=''){
        try{
            $obrigatorio = array('forcoid', 'cfbbanco', 'clioid', 'dataVencimento', 'valor', 'sequencial', 'carteira', 'ios');
            $exists = false;
            
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $params);
            
            if($exists){
                $banco   = trim(strtolower($banco));
                $cliente = ClienteService::clienteGetDados($params['clioid']);
                
                if(is_array($cliente->dados) && !empty($cliente->dados)){
                    if($cliente->dados['clitipo'] == 'F'){//Pessoa física
                        $nome     = $cliente->dados['clinome'];
                        $cpf_cnpj = $this->formata_cgc_cpf($cliente->dados['clino_cpf']);
                        
                        $endereco = Mascara::removeAcento($cliente->dados['endlogradouro']).', '.$cliente->dados['endno_numero'].', '.Mascara::removeAcento($cliente->dados['endbairro']);
                        $cep      = str_pad($cliente->dados['endcep'], 8, '0', STR_PAD_LEFT);
                        $cidade   = Mascara::removeAcento($cliente->dados['endcidade']);
                        $uf       = $cliente->dados['enduf'];                     
                    
                    } else{//Pessoa jurídica
                        $nome     = Mascara::removeAcento($cliente->dados['clinome']);
                        $cpf_cnpj = $this->formata_cgc_cpf($cliente->dados['clino_cgc']);
                        
                        $endereco = Mascara::removeAcento($cliente->dados['endlogradouro']).', '.$cliente->dados['endno_numero'].', '.Mascara::removeAcento($cliente->dados['endbairro']);
                        $cep      = str_pad($cliente->dados['endcep'], 8, '0', STR_PAD_LEFT);
                        $cidade   = Mascara::removeAcento($cliente->dados['endcidade']);
                        $uf       = $cliente->dados['enduf'];
                    }
                    
                    $sacado = new Agente($nome, $cpf_cnpj, $endereco, $cep, $cidade, $uf);
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];
                        
                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
		
						//Busca forma de registro do boleto (CNAB ou XML)
						$formaRegistro = $this->getformaRegistro($params['sequencial']);
                        
                        //Codigo do cedente
						//OffLine
						if($formaRegistro == 'CNAB'){
							$cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
						}
						//OnLine
						elseif($formaRegistro == 'XML'){
							$cfbcodigo_cedente = '8528748';
						}
                        
                        //Instrucoes
                        $txtInstrucao = array();
                        $instrucoes   = $this->getInstrucoes($this->model->isTituloRetencao($params['sequencial']), $params['dataVencimento']);
                        
                        if(is_array($instrucoes->dados) && !empty($instrucoes->dados)){
                            $txtInstrucao = array(utf8_encode($instrucoes->dados['pcsidescricao']));
                        }
                        
                        if($banco != '' && !empty($params)){
                            switch($banco){
                                case 'santander':
                                    $boleto = new Santander(
                                        array(
                                            'sacado' => $sacado,
                                            'dataVencimento' => new DateTime($params['dataVencimento']),
                                            'valor' => $params['valor'],
                                            'sequencial' => $params['sequencial'], // Até 13 dígitos - Numero do título
                                            'carteira' => $params['carteira'], // 101, 102, 201
                                            'ios' => $params['ios'], // Apenas para o Santander; IOS – Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)
                                            'numeroDocumento' => $params['numeroDocumento'],                                            
                                            'cedente'   => $this->cedente,
                                            'agencia'   => $agencia, // Até 4 dígitos                                             
                                            'conta'     => $cfbcodigo_cedente, // Código do cedente: Até 7 dígitos - Santander
                                            //'agenciaDv' => $agenciaDv,
                                            //'contaDv'   => $contaDv,
                                            //'logoPath'  => _PROTOCOLO_ . _SITEURL_ . 'core/module/Boleto/Project/resources/images/sascar.png',
                                            'instrucoes' => $txtInstrucao,                                            
                                            'descontosAbatimentos' => $params['descontosAbatimentos'], // (-) Desconto
                                            'outrasDeducoes' => $params['outrasDeducoes'], // (-) Abatimento
                                            'moraMulta' => $params['moraMulta'],    
                                            'outrosAcrescimos' => $params['outrosAcrescimos'],
                                            'valorCobrado' => $params['valorCobrado'],                                            
                                        )
                                    );
                                
                                    $this->response->setResult($boleto->getOutput(), '0');
                                break;
                                case 'bradesco':
                                    $boleto = new Bradesco(
                                        array(
                                            // Parâmetros obrigatórios
                                            'sacado'  => $sacado,
                                            'cedente' => $this->cedente,                                            
                                            'dataVencimento' => new DateTime(date('Y-m-d', strtotime(date('Y-m-d'). ' + 30 days'))),
                                            'valor' => 12.90,
                                            'sequencial' => 1, // Até 11 dígitos
                                            'carteira' => 6, // 3, 6 ou 9
                                            'agencia' => $agencia, // Até 4 dígitos                                            
                                            'conta' => $conta, // Até 7 dígitos                                            
                                            'descontosAbatimentos' => 0, // (-) Desconto
                                            'outrasDeducoes' => 0, // (-) Abatimento
                                            'moraMulta' => 0,    
                                            'outrosAcrescimos' => 0,
                                            'valorCobrado' => 0,
                                        )
                                    );
                                    $this->response->setResult($boleto->getOutput(), '0');
                                break;
                            }
                        } else{
                            $this->response->setResult(false, 'INF003');
                        }
                    } else{
                        $this->response->setResult(false, $dadosBancarios->codigo);
                    }    
                } else{
                    $this->response->setResult(false, $cliente->codigo);
                }
            } else{
                $this->response->setResult(false, 'INF005');
            }            
        } catch(Exception $e){
    		$this->response->setResult($e, 'EXCEPTION');
    	}
        
        return $this->response;
    }
    
    
    private function formata_cgc_cpf($numero){
    	$retorno="";
    	if(strlen($numero)<=11){
    		if(strlen($numero)<11){
    			$buf=str_repeat("0",11-strlen($numero)).$numero;
    		}
    		else{
    			$buf=$numero;
    		}
    		$retorno=substr($buf,0,3).".".substr($buf,3,3).".".substr($buf,6,3)."-".substr($buf,9,2);
    	}else{
    		if(strlen($numero)<14){
    			$buf=str_repeat("0",14-strlen($numero)).$numero;
    		}else{
    			$buf=$numero;
    		}
    		$retorno=substr($buf,0,2).".".substr($buf,2,3).".".substr($buf,5,3)."/".substr($buf,8,4)."-".substr($buf,12,2);
    	}
    	return $retorno;
    }
    
    
    /**
     * Retorna true se o boleto está registrado no banco.
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param int $titulo Número do título
     * @param string $tipo Define em qual tabela realiza a consulta - Tipo do titulo: titulo; consolidado; retencao;
     * @throw
     * @return boolean
     */
    public function consultarRegistroBoleto($titulo=0, $tipo='titulo'){
        try{
            if($titulo > 0 && !empty($tipo)){
                $this->response->setResult($this->model->consultarRegistroBoleto($titulo, $tipo), '0');
            } else{
                throw new Exception('INF006');
            }
        } catch(Exception $e){
            $this->response->setResult('', $e->getMessage());
        }
        
        return $this->response;
    }
    
    /**
     * Retorna o "Nosso Número" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param array $params Array com os dados para o nosso número
     * @param int $cod_banco Código do banco (Santander: 33)
     * @return object response
     */
    public function getNossoNumero($params=array(), $cod_banco=0){
        try{
            $obrigatorio = array('forcoid', 'cfbbanco', 'dataVencimento', 'valor', 'sequencial', 'carteira', 'ios');
            $exists = false;
            
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $params);
            
            if($exists){
                if(!empty($params) && $cod_banco > 0){
                    //Busca dados bancários do cedente - pelo ID da forma de cobrança ou pelo Código do banco
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];

                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
		
						//Busca forma de registro do boleto (CNAB ou XML)
						$formaRegistro = $this->getformaRegistro($params['sequencial']);
						
						//Codigo do cedente
						//OffLine
						if($formaRegistro == 'CNAB'){
							$cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
						}
						//OnLine
						elseif($formaRegistro == 'XML'){
							$cfbcodigo_cedente = '8528748';
						}
						
						$boleto = new \stdClass();
                                    
                        switch($cod_banco){
                            case 33:
                                //Santander
                                $boleto = new Santander(
                                    array(
                                        'dataVencimento' => new DateTime($params['dataVencimento']),
                                        'valor' => $params['valor'],
                                        'sequencial' => $params['sequencial'], // Até 13 dígitos - Numero do título
                                        'carteira' => $params['carteira'], // 101, 102, 201
                                        'ios' => $params['ios'], // Apenas para o Santander; IOS – Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)                                     
                                        'conta' => $cfbcodigo_cedente, // Código do cedente: Até 7 dígitos - Santander
                                    )
                                );                           
                            break;
                        }
                        
                        $this->response->setResult($boleto->getNossoNumero(), '0');
                    } else{
                        throw new Exception('TAX008');
                    }
                } else{
                    throw new Exception('INF005');
                }
            } else{
                $this->response->setResult(false, 'INF005');
            }
        } catch(Exception $e){
            $this->response->setResult('', $e->getMessage());
        }

        return $this->response;
    }
    
    /**
     * Retorna a "Linha Digitável" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param array $params Array com os dados para a linha digitável
     * @param int $cod_banco Código do banco (Santander: 33)
     * @return object response
     */
    public function getLinhaDigitavel($params=array(), $cod_banco=0){
        try{
            $obrigatorio = array('forcoid', 'cfbbanco', 'dataVencimento', 'valor', 'sequencial', 'carteira', 'ios');
            $exists = false;
            
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $params);
            
            if($exists){
                if(!empty($params) && $cod_banco > 0){
                    //Busca dados bancários do cedente - pelo ID da forma de cobrança ou pelo Código do banco
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];

                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
		
						//Busca forma de registro do boleto (CNAB ou XML)
						$formaRegistro = $this->getformaRegistro($params['sequencial']);
                        
                        //Codigo do cedente
						//OffLine
						if($formaRegistro == 'CNAB'){
							$cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
						}
						//OnLine
						elseif($formaRegistro == 'XML'){
							$cfbcodigo_cedente = '8528748';
						}
						
                        $boleto = new \stdClass();
                        
                        switch($cod_banco){
                            case 33:
                                //Santander
                                $boleto = new Santander(
                                    array(
                                        'dataVencimento' => new DateTime($params['dataVencimento']),
                                        'valor' => $params['valor'],
                                        'sequencial' => $params['sequencial'], // Até 12 dígitos - Numero do título
                                        'carteira' => $params['carteira'], // 101, 102, 201
                                        'ios' => $params['ios'], // Apenas para o Santander; IOS – Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)                                     
                                        'conta' => $cfbcodigo_cedente, // Código do cedente: Até 7 dígitos - Santander
                                    )
                                );                           
                            break;
                        }
                        
                        $this->response->setResult($boleto->getLinhaDigitavel(), '0');
                    } else{
                        throw new Exception('TAX008');
                    }
                } else{
                    throw new Exception('INF005');
                }
            } else{
                $this->response->setResult(false, 'INF005');
            }
        } catch(Exception $e){
            $this->response->setResult('', $e->getMessage());
        }

        return $this->response;
    }
    
    /**
     * Retorna o "Código de barras" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 07/10/2016
     * @param array $params Array com os dados do codigo de barras
     * @param int $cod_banco Código do banco (Santander: 33)
     * @return object response
     */
    public function getCodigoBarras($params=array(), $cod_banco=0){
        try{
            $obrigatorio = array('forcoid', 'cfbbanco', 'dataVencimento', 'valor', 'sequencial', 'carteira', 'ios');
            $exists = false;
            
            //Verificando se os campos obrigatorios existem
            $exists = $this->verificaCampos($obrigatorio, $params);
            
            if($exists){
                if(!empty($params) && $cod_banco > 0){
                    //Busca dados bancários do cedente - pelo ID da forma de cobrança ou pelo Código do banco
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];

                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
		
						//Busca forma de registro do boleto (CNAB ou XML)
						$formaRegistro = $this->getformaRegistro($params['sequencial']);
                        
                        //Codigo do cedente
						//OffLine
						if($formaRegistro == 'CNAB'){
							$cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
						}
						//OnLine
						elseif($formaRegistro == 'XML'){
							$cfbcodigo_cedente = '8528748';
						}
						
                        $boleto = new \stdClass();
                        
                        switch($cod_banco){
                            case 33:
                                //Santander
                                $boleto = new Santander(
                                    array(
                                        'dataVencimento' => new DateTime($params['dataVencimento']),
                                        'valor' => $params['valor'],
                                        'sequencial' => $params['sequencial'], // Até 12 dígitos - Numero do título
                                        'carteira' => $params['carteira'], // 101, 102, 201
                                        'ios' => $params['ios'], // Apenas para o Santander; IOS – Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)                                     
                                        'conta' => $cfbcodigo_cedente, // Código do cedente: Até 7 dígitos - Santander
                                    )
                                );                           
                            break;
                        }
                        
                        //Recuperando o código que compoem a imagem do código de barras
                        $codigo = $boleto->getNumeroFebraban();
                        
                        if(strlen($codigo) % 2 != 0){
                            $codigo = "0" . $codigo;
                        }
                        
                        $this->response->setResult($codigo, '0');
                    } else{
                        throw new Exception('TAX008');
                    }
                } else{
                    throw new Exception('INF005');
                }
            } else{
                $this->response->setResult(false, 'INF005');
            }
        } catch(Exception $e){
            $this->response->setResult('', $e->getMessage());
        }

        return $this->response;
    }
    
    /**
     * Retorna os dados bancarios.
     * Caso for informado forcoid - busca na tabela forma_cobranca;
     * Caso for informado cfbbanco - busca na tabela config_banco;
     *
     * @param int $forcoid ID da forma de cobrança
     * @param int $cfbbanco ID do Banco
     * @return object response
     */
    public function getDadosBancarios($forcoid=0, $cfbbanco=0){
        if($forcoid > 0 || $cfbbanco > 0){
            $result = $this->model->getDadosBancarios($forcoid, $cfbbanco);
            
            if(is_array($result) && !empty($result)){
                $this->response->setResult($result, 0);
            } else{
                $this->response->setResult(false, 'INF003');
            }
        } else{
            $this->response->setResult(false, 'INF006');
        }
        
        return $this->response;
    }
    
    /**
     * Retorna as instru��es para apresentar no boleto.
     * @return object response
     */
     public function getInstrucoes($isBoletoSeco = false, $dataExpiracao = null) {
        $tipoInstrucao = $isBoletoSeco ? 'INSTRUCOES_BOLETO_SECO' : 'INSTRUCOES_BOLETO';
        $result = $this->model->getInstrucoes($tipoInstrucao);

        if (!empty($result['pcsidescricao']) && strpos($result['pcsidescricao'], '%date') > -1) {
            if ($isBoletoSeco) {
                $dias = $this->model->getDiasBaixaDevolucaoBoletoSeco();
                $dataExpiracao = date('d/m/Y', strtotime("$dataExpiracao+ {$dias} days"));
            }
            
            $result['pcsidescricao'] = str_replace('%date', $dataExpiracao, $result['pcsidescricao']);
        }

        if(is_array($result) && !empty($result)){
            $this->response->setResult($result, 0);
        } else{
            $this->response->setResult(false, 'INF003');
        }

        return $this->response;
    }

    /**
     * Retorna a data de vencimento de um boleto
     * @return string
     */
    public function getDataVencimento($titoid){
        $result = $this->model->getDataVencimento($titoid);
        return isset($result['titdt_vencimento']) ? $result['titdt_vencimento'] : false;
    }
    
    /**
     * Retorna os prazso estipulados pela Febraban com o limite de valor e datas para registro de boletos
     * @return array|\infra\Array
     */
    public function getPrazosFebraban($valor, $dtEmissao){
    	
    	if($valor != '' && $dtEmissao != ''){
    	
	    	//recupera os valores e prazos da tabela de par㮥tros
	    	$result = $this->model->getPrazosFebraban();
    	
	    	if(is_array($result) && !empty($result)){
	    		
	    		$prazos = explode('/',$result['pcsidescricao']);
	    		
	    		foreach ($prazos as $datas){
	    			
	    			$dados = '';
	    			
	    			$dados = explode(':',$datas);
	    			
					// se o valor do tlo for maior ou igual ao valor do par㮥tro
					if ($valor >= $dados [0]) {
						//data corrente
						$dataHoje = new DateTime(date('d-m-Y'));
						//data parametrizada
						$dataParam = new DateTime($dados[1]);
                        
                        //data da emiss�o do t�tulo (segundo o Santander t�tulos emitidos dentro do praxo Febrandan devem estar com registro)
                        $dataEmissaoTitulo = new DateTime($dtEmissao);
                        
                        // verifica se a data do par㮥tro 顡 atual
                        if ($dataHoje >= $dataParam ) {

							//se a data da emiss�o do t�tulo for maior ou igual o prazo da Febraban
							if($dataEmissaoTitulo >= $dataParam){
								
								// retorna 1 se o prazo coincide com o da febraban que estᡮa tabela
								$this->response->setResult ( $result = 1, 0 );
								
								return $this->response;
							
							}else{
								// retorna 0 se o prazo n䯠coincidir com o da febraban que estᡮa tabela
								$this->response->setResult ( $result = 0, 0 );
							
						    }
							
						} else {
							// retorna 0 se o prazo n䯠coincidir com o da febraban que estᡮa tabela
							$this->response->setResult ( $result = 0, 0 );
						}
					} else {
						// retorna 0 se o valor n䯠coincidir com o da febraban que estᡮa tabela
						$this->response->setResult ( $result = 0, 0 );
					}
				}
	    		
	    	} else{
	    		$this->response->setResult(false, 'INF003');
	    	}
    	
    	} else{
    		$this->response->setResult(false, 'INF006');
    	}
    	
    	return $this->response;
    	
    }
    
    /**
     * Verifica se o id de t�tulo fornecido � de reten��o (boleto seco).
     * @return bool
     */
     public function isBoletoSeco($titoid) {
        return $this->model->isTituloRetencao($titoid);
    }
    
    /**
     * Retorna a forma de registro do Boleto no banco (XML ou CNAB)
     * @return string
     */
    public function getformaRegistro($titoid){
        return $this->model->getformaRegistro($titoid);
    }
    
    
    public function getTabelaTitulo($titoid){
    	return $this->model->getTabelaTitulo($titoid);
    }
    
}