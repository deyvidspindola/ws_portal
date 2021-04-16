<?php
/**
 * @file RelProdutoComSeguro.php
 * @author marcioferreira
 * @version 11/12/2013 09:41:40
 * @since 11/12/2013 09:41:40
 * @package SASCAR RelProdutoComSeguro.php 
 */

//manipula os dados no BD
require(_MODULEDIR_ . "Relatorio/DAO/RelProdutoComSeguroDAO.php");

//classe com os métodos de envio de cotação, proposta e apólice
require(_MODULEDIR_ . "Produto_Com_Seguro/Action/ProdutoComSeguro.php");


class RelProdutoComSeguro{
	
	private $id_usuario;
	
   /**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct()  {
		
    	global $conn;
        
		$this->dao  = new RelProdutoComSeguroDAO($conn);
		$this->produtoComSeguro = new ProdutoComSeguro();
		$this->seguroApolice = new SeguroApolice();
		$this->id_usuario = $_SESSION['id_usuario'];

    }
	
	/**
	 * Retorna um array com todos os status possíveis de uma apólice
	 * 
	 * @return Ambigous <multitype:, boolean, multitype:>|boolean
	 */
	public function getStatusApolice(){

		$listaStatus = $this->dao->getStatusApolice();
		
		if(is_array($listaStatus)){
			
			return $listaStatus;
			
		}
		
		return false;
		
	}
	
	/**
	 * Retorna um  array com os dados de uma pesquisa filtrando pelos dados recebidos do post
	 * 
	 * @return Ambigous <multitype:, boolean, multitype:>|boolean
	 */	
	public function pesquisar(){
				
		$data_ini     = isset($_POST["data_ini"])       ? $_POST["data_ini"] : "";
		$data_fim     = isset($_POST["data_fim"])       ? $_POST["data_fim"] : "";
		$cpf_cnpj     = isset($_POST["documento"])      ? $_POST["documento"] : "";
		$num_contrato = isset($_POST["num_contrato"])   ? $_POST["num_contrato"] : "";
		$id_status    = isset($_POST["status_apolice"]) ? $_POST["status_apolice"] : "";
		$vei_placa    = isset($_POST["placa"])          ? $_POST["placa"] : "";
	
		if( 
			( !empty($id_status) && (!empty($data_ini) && !empty($data_fim))) ||
			  !empty($cpf_cnpj) || 
			  !empty($num_contrato) ||  
			  !empty($vei_placa) ||
			  !empty($data_ini) && !empty($data_fim)	 
			) {
			
            //deixa somente os números caso passe pela validação do js
			$cpf_cnpj     = $this->somenteNumeros($cpf_cnpj);
			$num_contrato = $this->somenteNumeros($num_contrato);
			
			$dadosBusca = new stdClass();

			$dadosBusca->data_ini     = $data_ini; 
			$dadosBusca->data_fim     = $data_fim;
			$dadosBusca->cpf_cnpj     = $cpf_cnpj;
			$dadosBusca->num_contrato = $num_contrato;
			$dadosBusca->id_status    = $id_status;
			$dadosBusca->vei_placa    = $vei_placa;
			
			$dias_validade_proposta = $this->getValidadeProposta();
			
			$retornoBuscaReenvio = $this->dao->pesquisaDadosEnvio($dadosBusca, $dias_validade_proposta);
			
			if(is_array($retornoBuscaReenvio)){
				return $retornoBuscaReenvio;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Recupera a quantidade de dias para estabelecer prazo de validade da proposta
	 * 
	 * @return boolean
	 */
	public function getValidadeProposta(){
		
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
			return $diasValidadeProposta->pcsidescricao;
		}
		
		return false;
	}

	
	 
	/**
	 * Retorna todos os erros gerados durante a ativação de uma apólice pelo id informado via post
	 * 
	 * @return Ambigous <multitype:, boolean, multitype:>|boolean
	 */
	public function detalharDadosErro(){
		
		$id_apolice   = isset($_POST["id_apolice"])  ? $_POST["id_apolice"] : "";
	
		if(!empty($id_apolice)){
			
			$dadosDetalhes =  $this->dao->detalharDadosErro($id_apolice);
			
			if(is_array($dadosDetalhes)){
				//$dadosDetalhes['tipo'] = 'detalhesErro';
				return $dadosDetalhes;
			}
		}
		
		return false;
	}
	
	
	/**
	 * Retorna dados de uma apólice ativada com sucesso
	 *
	 * @return Ambigous <multitype:, boolean, multitype:>|boolean
	 */
	public function detalharDadosSucesso(){
	
		$id_apolice   = isset($_POST["id_apolice"])  ? $_POST["id_apolice"] : "";
		
		if(!empty($id_apolice)){
				
			$dadosDetalhes =  $this->dao->detalharDadosSucesso($id_apolice);
				
			if(is_array($dadosDetalhes)){
				//$dadosDetalhes['tipo'] = 'detalhesSucesso';
				return $dadosDetalhes;
			}
		}
	
		return false;
	}
	
	
	/**
	 * Ativa ou reenvia uma apólce para a seguradora,
	 * retorna sucesso ou erro
	 * 
	 * @throws Exception
	 */
	public function ativarApolice(){
		
		try {
			
			$num_contrato = !empty($_POST['contrato_cli']) ? $_POST['contrato_cli'] : NULL;
			
			if(empty($num_contrato)){
				throw new Exception('Obrigatório informar o número do contrato do cliente.');
			}
			
			//busca os dados para envio
			$dadosContratoEnvio = $this->dao->getDadosEnvioApolice($num_contrato);
			
			if(!is_array($dadosContratoEnvio)){
				throw new Exception('Dados de ennvio não encontrados para o contrato informado.');
			}
			
			$this->produtoComSeguro->setContratoNumero($num_contrato);
			
			$this->produtoComSeguro->setDataInstalacaoEquipamento($dadosContratoEnvio[0]['dt_instalacao']);
			$this->produtoComSeguro->setDataAtivacaoEquipamento($dadosContratoEnvio[0]['dt_ativacao']);
			
			$this->produtoComSeguro->setClasseProduto($dadosContratoEnvio[0]['id_classe']);
			$this->produtoComSeguro->setCodUsuarioLogado($this->id_usuario);

			$this->produtoComSeguro->setOrigemChamada('rel_produto_com_seguro');
			$this->produtoComSeguro->setOrigemSistema('Intranet');
			
			$retorno = $this->produtoComSeguro->processarApolice();
					
			if($retorno['status'] == 'Erro'){
				
				//percorre os código de erro para buscar as mensagens
				foreach ($retorno['cod_msg'] as $key=> $cod){

					//recupera mensagem de erro na tabela produto_seguro_mensagens
					$msg_log = $this->produtoComSeguro->getMensagem($cod);
						
					if(is_object($msg_log)){
						$mensagem .=  $msg_log->msg_sascar."\r\n";
					}
				}
					
				echo $mensagem;
				
				exit;
			}
			
			print(1);
			exit;
			
		} catch (Exception $e) {
		
			//echo json_encode(utf8_encode($e->getMessage()));
			echo $e->getMessage();
			
			exit;
		}
		
	}
	
	/**
	 * Caso a apólice esteja ativa, o usuário tem a opção de reenviar o e-mail para o cliente com o nome do cliente e o número da apólie  
	 * 
	 * @throws Exception
	 */
	public function reenviarMail(){

		try {

			$num_contrato   = isset($_POST["contrato_cli"])  ? $_POST["contrato_cli"] : "";
			$cd_apolice     =  isset($_POST["num_apolice"])  ? $_POST["num_apolice"] : "";
			
			if(!empty($num_contrato)){
					
				$dadosCliente = $this->seguroApolice->getDadosClienteContrato($num_contrato);
					
				if(is_object($dadosCliente)){

					//ambiente de testes
					if($_SESSION['servidor_teste'] == 1){
						//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
						$emailTeste = $this->produtoComSeguro->getEmailTeste();
						$dadosCliente->email_cliente = $emailTeste->pcsidescricao;
					}
					
					$emailCliente = $this->validarEmail($dadosCliente->email_cliente);
					
					if($emailCliente === 3){
						throw new Exception("E-mail do cliente não está cadastrado");
					} 
					
					if(!$emailCliente){
						throw new Exception("E-mail do cliente inválido");
					}

					$tituloLayout = 'Apolice de Seguro';
			        $cabecalho    = 'Apolice de Seguro';
					
					//busca layout do email
					$layoutEmail = $this->seguroApolice->getLayoutEmail($tituloLayout, $cabecalho);
					
					if(!is_array($layoutEmail)){
						throw new Exception("Layout de e-mail não cadastrado");
					}

					$htmlEmail = $this->seguroApolice->substituirDadosCienteEmail($dadosCliente->nome_cliente, $layoutEmail[0]['corpo_email'], $cd_apolice);
					
					$envioEmail = $this->produtoComSeguro->enviarEmail($emailCliente, $htmlEmail, $layoutEmail[0]['assunto_email'] , $layoutEmail[0]['servidor']);
										
					if($envioEmail){
						
						print(1);
					
					}else{
						throw new Exception('Não foi possível enviar e-mail');
					}
				}
			}
				
			exit;

		} catch (Exception $e) {

			echo json_encode(utf8_encode($e->getMessage()));
			exit;
		}
	}
	
	
	/**
	 * Retorna somente números de uma string
	 * 
	 * @param string $valor
	 * @return mixed
	 */
	public function somenteNumeros($valor){
		return preg_replace("([^0-9])","",$valor);
	}
	
	
	/**
	 * Retorna CPF ou CNPJ com máscara
	 * @param int $numero
	 * @return string
	 */
	public function aplicarMascaraCPF_CNPJ($numero){

		//CPF
		if(strlen($numero)<=11){
			$mascara = @str_repeat("0",11-strlen($numero)).$numero;
			$mascara = substr($mascara,0,3).".".substr($mascara,3,3).".".substr($mascara,6,3)."-".substr($mascara,9,2);
		
		//CNPJ	
		}else{
			$mascara = @str_repeat("0",14-strlen($numero)).$numero;
			$mascara = substr($mascara,0,2).".".substr($mascara,2,3).".".substr($mascara,5,3)."/".substr($mascara,8,4)."-".substr($mascara,12,2);
		}
		
		return $mascara;
		
	}
	
	
	/**
	 * Verifica se o e-mail é válido
	 * @param string $email
	 * @return boolean
	 */
	private function validarEmail($email){
	
		$valida = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
	
		if(empty($email)){
	
			return 3;
				
		}elseif (preg_match($valida, $email)){
				
			return true;
				
		} else {
				
			return false;
		}
	}
	
    
}