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
        $this->cedente  = new Agente('Sascar - Tecnologia e Seguran&ccedil;a Automotiva S/A', '03.112.879/0001-51', 'Av Marte, 537, Alphaville', '06.541-005', 'Santana de Parnaiba', 'SP');
    }
    
    /**
     * Gera o boleto conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 09/09/2016
     * @param array $params Array com os dados para gerar o boleto
     *                    
                array(
                    // Par�metros obrigat�rios 
                    'forcoid' => 84, //ID da Forma de cobran�a - caso seja informado o forcoid, o cfbbanco deve ser 0 (zero)
                    'cfbbanco' => 33, //C�digo do banco - caso seja informado o cfbbanco, o forcoid deve ser 0 (zero)
                    'clioid' => 307743,
                    'dataVencimento' => date('Y-m-d'), //Susbtituir date('Y-m-d') pela data de vencimento no formato 'Y-m-d'
                    'valor' => 59.90, //Valor do documento
                    'sequencial' => 12345678901, // At� 12 d�gitos - Numero do t�tulo                    
                    'carteira' => 101,
                    'ios' => 0, // Apenas para o Santander; IOS � Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)
                    
                    // Par�metros opcionais
                    'numeroDocumento' => '',
                    'descontosAbatimentos' => 0, // (-) Desconto
                    'outrasDeducoes' => 0, // (-) Abatimento
                    'moraMulta' => 0,    
                    'outrosAcrescimos' => 0,
                    'valorCobrado' => 0, //Soma do valor com multas e acrescimos
     *          )
     * @param string $banco Nome do banco (minusculo, sem espa�o. Ex: santander)
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
                    if($cliente->dados['clitipo'] == 'F'){//Pessoa f�sica
                        $nome     = Mascara::removeAcento($cliente->dados['clinome']);
                        $cpf_cnpj = $cliente->dados['clino_cpf'];
                        $endereco = Mascara::removeAcento($cliente->dados['clirua_res']).', '.$cliente->dados['clino_res'].', '.Mascara::removeAcento($cliente->dados['clibairro_res']);
                        $cep      = str_pad($cliente->dados['clino_cep_res'], 8, '0', STR_PAD_LEFT);
                        $cidade   = Mascara::removeAcento($cliente->dados['clicidade_res']);
                        $uf       = $cliente->dados['cliuf_res'];                    
                    } else{//Pessoa jur�dica
                        $nome     = Mascara::removeAcento($cliente->dados['clinome']);
                        $cpf_cnpj = $cliente->dados['clino_cgc'];
                        $endereco = Mascara::removeAcento($cliente->dados['clirua_com']).', '.$cliente->dados['clino_com'].', '.Mascara::removeAcento($cliente->dados['clibairro_com']);
                        $cep      = str_pad($cliente->dados['clino_cep_com'], 8, '0', STR_PAD_LEFT);
                        $cidade   = Mascara::removeAcento($cliente->dados['clicidade_com']);
                        $uf       = $cliente->dados['cliuf_com'];
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
                        
                        //Codigo do cedente
                        $cfbcodigo_cedente   = $dadosBancarios->dados['cfbcodigo_cedente'];
                        
                        //Instrucoes
                        $txtInstrucao = array();
                        $instrucoes   = $this->getInstrucoes();
                        
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
                                            'sequencial' => $params['sequencial'], // At� 13 d�gitos - Numero do t�tulo
                                            'carteira' => $params['carteira'], // 101, 102, 201
                                            'ios' => $params['ios'], // Apenas para o Santander; IOS � Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)
                                            'numeroDocumento' => $params['numeroDocumento'],                                            
                                            'cedente'   => $this->cedente,
                                            'agencia'   => $agencia, // At� 4 d�gitos                                             
                                            'conta'     => $cfbcodigo_cedente, // C�digo do cedente: At� 7 d�gitos - Santander
                                            //'agenciaDv' => $agenciaDv,
                                            //'contaDv'   => $contaDv,
                                            //'logoPath'  => _PROTOCOLO_ . _SITEURL_ . 'modulos/core/module/Boleto/Project/resources/images/sascar.png',
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
                                            // Par�metros obrigat�rios
                                            'sacado'  => $sacado,
                                            'cedente' => $this->cedente,                                            
                                            'dataVencimento' => new DateTime(date('Y-m-d', strtotime(date('Y-m-d'). ' + 30 days'))),
                                            'valor' => 12.90,
                                            'sequencial' => 1, // At� 11 d�gitos
                                            'carteira' => 6, // 3, 6 ou 9
                                            'agencia' => $agencia, // At� 4 d�gitos                                            
                                            'conta' => $conta, // At� 7 d�gitos                                            
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
    
    /**
     * Retorna true se o boleto est� registrado no banco.
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param int $titulo N�mero do t�tulo
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
     * Retorna o "Nosso N�mero" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param array $params Array com os dados para o nosso n�mero
     * @param int $cod_banco C�digo do banco (Santander: 33)
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
                    //Busca dados banc�rios do cedente - pelo ID da forma de cobran�a ou pelo C�digo do banco
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];

                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
                        
                        $cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
                        $boleto = new \stdClass();
                                    
                        switch($cod_banco){
                            case 33:
                                //Santander
                                $boleto = new Santander(
                                    array(
                                        'dataVencimento' => new DateTime($params['dataVencimento']),
                                        'valor' => $params['valor'],
                                        'sequencial' => $params['sequencial'], // At� 13 d�gitos - Numero do t�tulo
                                        'carteira' => $params['carteira'], // 101, 102, 201
                                        'ios' => $params['ios'], // Apenas para o Santander; IOS � Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)                                     
                                        'conta' => $cfbcodigo_cedente, // C�digo do cedente: At� 7 d�gitos - Santander
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
     * Retorna a "Linha Digit�vel" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param array $params Array com os dados para a linha digit�vel
     * @param int $cod_banco C�digo do banco (Santander: 33)
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
                    //Busca dados banc�rios do cedente - pelo ID da forma de cobran�a ou pelo C�digo do banco
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];

                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
                        
                        $cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
                        $boleto = new \stdClass();
                        
                        switch($cod_banco){
                            case 33:
                                //Santander
                                $boleto = new Santander(
                                    array(
                                        'dataVencimento' => new DateTime($params['dataVencimento']),
                                        'valor' => $params['valor'],
                                        'sequencial' => $params['sequencial'], // At� 12 d�gitos - Numero do t�tulo
                                        'carteira' => $params['carteira'], // 101, 102, 201
                                        'ios' => $params['ios'], // Apenas para o Santander; IOS � Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)                                     
                                        'conta' => $cfbcodigo_cedente, // C�digo do cedente: At� 7 d�gitos - Santander
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
     * Retorna o "C�digo de barras" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 07/10/2016
     * @param array $params Array com os dados do codigo de barras
     * @param int $cod_banco C�digo do banco (Santander: 33)
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
                    //Busca dados banc�rios do cedente - pelo ID da forma de cobran�a ou pelo C�digo do banco
                    $dadosBancarios = $this->getDadosBancarios($params['forcoid'], $params['cfbbanco']);
                    
                    if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
                        $agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
                        $agenciaDv = $agencia[1];
                        $agencia   = $agencia[0];

                        $conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
                        $contaDv = $conta[1];
                        $conta   = $conta[0];
                        
                        $cfbcodigo_cedente = $dadosBancarios->dados['cfbcodigo_cedente'];
                        $boleto = new \stdClass();
                        
                        switch($cod_banco){
                            case 33:
                                //Santander
                                $boleto = new Santander(
                                    array(
                                        'dataVencimento' => new DateTime($params['dataVencimento']),
                                        'valor' => $params['valor'],
                                        'sequencial' => $params['sequencial'], // At� 12 d�gitos - Numero do t�tulo
                                        'carteira' => $params['carteira'], // 101, 102, 201
                                        'ios' => $params['ios'], // Apenas para o Santander; IOS � Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)                                     
                                        'conta' => $cfbcodigo_cedente, // C�digo do cedente: At� 7 d�gitos - Santander
                                    )
                                );                           
                            break;
                        }
                        
                        //Recuperando o c�digo que compoem a imagem do c�digo de barras
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
     * @param int $forcoid ID da forma de cobran�a
     * @param int $cfbbanco ID do Banco
     * @return object response
     */
    private function getDadosBancarios($forcoid=0, $cfbbanco=0){
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
    private function getInstrucoes(){
        $result = $this->model->getInstrucoes();
        
        if(is_array($result) && !empty($result)){
            $this->response->setResult($result, 0);
        } else{
            $this->response->setResult(false, 'INF003');
        }
        
        return $this->response;
    }
}