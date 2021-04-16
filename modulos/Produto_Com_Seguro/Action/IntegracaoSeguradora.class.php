<?php
/**
 * @file IntegracaoSeguradora.class.php
 * @author marcioferreira
 * @version 01/11/2013 14:08:14
 * @since 01/11/2013 14:08:14
 * @package SASCAR IntegracaoSeguradora.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

ini_set("soap.wsdl_cache_enabled", "0"); //Limpa o cache

/*
* Verifica se existe sessa de usuario para identificar se
* a chamada e da Intranet ou de WS
*/
if(isset($_SESSION['usuario']['oid'])) {
	require_once _SITEDIR_ .'lib/nusoap.php';
}

//manipula os dados no BD
require(_MODULEDIR_ . "Produto_Com_Seguro/DAO/IntegracaoSeguradoraDAO.class.php");


class IntegracaoSeguradora{

	/**
	 * @var $id_revenda inteiro
	 */
	public  $id_revenda;
	
	/**
	 * @var $id_revenda string
	 */
	public $nm_usuario;
	
	/**
	 * Recebe a url do Web Service
	 * @var string
	 */
	public $servicoSeguradora;
	
	
	/**
	 * Reve o id da seguradora para buscar a url 
	 * @var inteiro
	 */
	public $idSeguradora;
	
	
	public function __construct(){
		
		global $dbstringSiggo;
		
		try{
			
			$connSiggo = pg_connect($dbstringSiggo);
		
		}catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		$this->dao  = new IntegracaoSeguradoraDAO($connSiggo);
		$this->daoProduto = new ProdutoComSeguroDAO($connSiggo);
		
	}
	
	/**
	 * Intancia o Web Service e envia os dados de acordo o nome do serviço e o conteúdo da string XML passados
	 * no parâmetro 
	 * 
	 * @param string $servico
	 * @param string_xml $xmlEntradaDados
	 * @return Ambigous <string, boolean>|string
	 */
	public function enviarDadosWs($servico, $xmlEntradaDados){
		
		$metodoExecutar = false;

		if(empty($servico)){
			$resposta['status']   = "Erro";
			$resposta['mensagem'] = "O nome do serviço do Ws deve ser informado";
			$resposta['cod_msg'] = 20;
			return $resposta;
		}
		
		if($xmlEntradaDados == ''){
			$resposta['status']   = "Erro";
			$resposta['mensagem'] = "O Xml não pode ser vazio";
			$resposta['cod_msg'] = 21;
			return $resposta;
		}
		
		//popula os atributos com os dados de acesso ao WS
		$parametrosWs = $this->setParametros();
		
		if(is_array($parametrosWs)){
			
			$resposta['status']   = "Erro";
			$resposta['mensagem'] = $parametrosWs['mensagem'];
			$resposta['cod_msg']  = $parametrosWs['cod_msg'];
				
			return $resposta;
		}
		
		//instancia um objeto para retornar os dados do WS
		$dadosRetornados = new stdClass();
		
		//inicia comunicação do WS
		$servicoInstanciado = $this->startWebService();
		
		if(is_object($servicoInstanciado)){
			
			//envia os dados para seguradora
			try{
				//inicia a contagem do tempo para o timeout
				$iniciaTempoTimeOut = time();
				
				if(strtolower($servico) == 'gerarpropostaautoconfiguravel') {
					//invoca o metodo que receberá os dados (no caso usando método da seguradora UseBens)
					$servicoInstanciadoResult = $servicoInstanciado->ExecutarRow(array('Servico'=>"$servico", 'conteudoXML'=>"$xmlEntradaDados"));
				} else {
					//invoca o metodo que receberá os dados (no caso usando método da seguradora UseBens)
					$servicoInstanciadoResult = $servicoInstanciado->Executar(array('Servico'=>"$servico", 'conteudoXML'=>"$xmlEntradaDados"));
					$metodoExecutar = true;
				}

				//finaliza e contabiliza o tempo de timeout
				$verificaTempoTimeOut = $this->verificaTempoTimeOut($iniciaTempoTimeOut);

				//se não houver resposta do Executar, grava a mensagem na tabela e retorna para o chamador
				if($metodoExecutar && ($verificaTempoTimeOut === true || !isset($servicoInstanciadoResult->ExecutarResult)) ){
					
					//busca mensagem de/para
					$msg_de_para = $this->daoProduto->getMensagem(505);//Serviço indisponível, o tempo de resposta foi excedido.
					$resposta['status']   = "Erro";
					$resposta['mensagem'] = $msg_de_para->msg_seguradora;
					$resposta['cod_msg']  = 505;
					
					return $resposta;
				} else if($metodoExecutar == false) {

					if($verificaTempoTimeOut === true || !isset($servicoInstanciadoResult->ExecutarRowResult)) {

						//busca mensagem de/para
						$msg_de_para = $this->daoProduto->getMensagem(505);//Serviço indisponível, o tempo de resposta foi excedido.
						$resposta['status']   = "Erro";
						$resposta['mensagem'] = $msg_de_para->msg_seguradora;
						$resposta['cod_msg']  = 505;

					} else if(isset($servicoInstanciadoResult->ExecutarRowResult)) {

						$xmlExecutarRow = simplexml_load_string($servicoInstanciadoResult->ExecutarRowResult);

						if(isset($xmlExecutarRow->erro)) {
							
							/*$resposta['status'] = "Erro";
							if(isset($xmlExecutarRow->erro->attributes()["nm_retorno"])) {
								$resposta['mensagem'] = 'Mensagem seguradora: ' . $xmlExecutarRow->erro->attributes()["nm_retorno"];
							} else {
								$resposta['mensagem'] = 'Falha de comunicacao com a seguradora.';
							}
							$resposta['cod_msg']  = 505;*/
						}
					}
				}
				
			}catch (Exception $e){
					
				//busca mensagem de/para
				$msg_de_para = $this->daoProduto->getMensagem(301);//Falha no retorno do serviço da Seguradora.
				$resposta['status']   = "Erro";
				$resposta['mensagem'] = $msg_de_para->msg_seguradora.' | '.$e->faultstring;
				$resposta['cod_msg']  = 301;
		
				return $resposta;
			}
			
			//verifica se houve retorno
			if(is_object($servicoInstanciadoResult)){
				
				//recupera o resultado retornado (no caso usando método da seguradora UseBens)
				if($metodoExecutar == true) {
					$dadosResultXML = $servicoInstanciadoResult->ExecutarResult;
				} else if($metodoExecutar == false) {
					$dadosResultXML = $servicoInstanciadoResult->ExecutarRowResult;
				}
				
				return $dadosResultXML;
						
			}else{

				$resposta['status']   = "Erro";
			    $resposta['mensagem'] = 'Não houve retorno do Web Service';
			    $resposta['cod_msg']  = 22;
			}

		}else{
			
			$resposta['status']   = "Erro";
			$resposta['mensagem'] = 'Web Service não instanciado';
			$resposta['cod_msg']  = 23;
		}
		
		return $resposta;
	}
	
	
	
	public function setParametros(){
		
		//caso ambiente local, seta proxy para saída de conexão
		if($_SERVER['HTTP_HOST'] == 'localhost') {

			$this->params = array(  'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                                   'trace' => 1,
                                   'exceptions' => 1,
                                   'soap_version' => SOAP_1_1,
                                   'style' => SOAP_DOCUMENT,
                                   'use' => SOAP_LITERAL,
                                   'encoding' => 'UTF-8',
									//'proxy_host' => '10.2.57.200',//kennedy
									//'proxy_host' => '10.2.8.200',//marechal
                                    //'proxy_host' => '10.0.110.50',//sascar
									//'proxy_port' => 3128,
									'connection_timeout' => 90,
									//'proxy_login' => 'servidores',
									//'proxy_password' => 'P4ssword'
                                 );

		//outros ambiente que não necessitam de proxy 
        }else{

			$this->params = array( 'trace' => 1,
							       'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                                    'trace' => 1,
                                    'exceptions' => 1,
                                    'soap_version' => SOAP_1_1,
                                    'style' => SOAP_DOCUMENT,
                                    'use' => SOAP_LITERAL,
                                    'encoding' => 'UTF-8',
					               'connection_timeout' => 90
                                );
		}

		
	    //ambiente de testes
		if($_SERVER["SERVER_ADDR"] == '172.16.2.57' || $_SESSION["servidor_teste"] == 1 ||
		   $_SERVER['HTTP_HOST'] ==  '192.168.56.101' || strstr($_SERVER['HTTP_HOST'], 'localhost') ||
		   (strstr($_SERVER['HTTP_HOST'], 'teste.intranet.sascar.com.br') || 
		   	strstr($_SERVER['REQUEST_URI'], 'desenvolvimento/')	|| 
		   	       $_SERVER['HTTP_HOST'] == 'hom1.intranet.sascar.com.br')){

            //seta variável para buscar na tabela de parâmetros
            $filtroAmbiente = 'WEBSERVICE_SEGURADORA_TESTE';

		}else{

			//seta variável para buscar na tabela de parâmetros em produção
			$filtroAmbiente = 'WEBSERVICE_SEGURADORA';
		}

		//busca os dados do webservice para instanciar o client 
		$dadosAcessoSeguradora =  $this->dao->getDadosAcessoSeguradora($this->idSeguradora, $filtroAmbiente);
		
		if(is_array($dadosAcessoSeguradora)){
			//percorre e atribui os dados de acesso nos atributos
			foreach ($dadosAcessoSeguradora as $dados){
				$this->atribuirDadosWs($dados);
			}
		
		}else{
		
			//retorna msg informando que não foi econtrado a url
			$resposta['mensagem'] = 'Url do WS da seguradora não está cadastrado na tabela de parâmetros do sistema.';
			$resposta['cod_msg']  = 24;
			
	    	return $resposta;
		}
		
		return true;
		
	} 
	
	
	/**
	 * Atribui os dados recuperados na tabela de parâmetros nos atributos correspondentes
	 * 
	 * @param array $dadosAcessoWs
	 */
	private function atribuirDadosWs($dadosAcessoWs){
		
		if($dadosAcessoWs['nome_param'] == 'url'){
			$this->servicoSeguradora = $dadosAcessoWs['valor_param'];
		}
		
		if($dadosAcessoWs['nome_param'] == 'id_revenda'){
			$this->id_revenda = $dadosAcessoWs['valor_param'];
		}
		
	    if($dadosAcessoWs['nome_param'] == 'nm_usuario'){
			$this->nm_usuario = $dadosAcessoWs['valor_param'];
		}
		
	}
	

	/**
	 * Método responsável pela chamada do webservice retornando o objeto instanciado
	 */
	private function startWebService(){
		
		try {

			if($this->servicoSeguradora){

				$client = new SoapClient($this->servicoSeguradora, $this->params);
				
				return $client;
				
			}
			
			return false;
			
		}catch (SoapFault $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Helper para calcular o tempo consumido em um processo
	 *
	 * @param int $iniciaTempoTimeOut - valor (em segundos) em formato inteiro
	 * @return boolean
	 */
	private function verificaTempoTimeOut($iniciaTempoTimeOut){
		 
		//calcula o tempo da transação
		$tempoFinal = time() - $iniciaTempoTimeOut;
	
		if($tempoFinal >= 90){
			return true;
		}else{
			return false;
		}
	}

	
}