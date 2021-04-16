<?php 
/** 
 * @file ProdutoComSeguro.php
 * @author marcioferreira
 * @version 31/10/2013 16:18:23
 * @since 31/10/2013 16:18:23
 * @package SASCAR ProdutoComSeguro.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

//usado para pegar conexão  ->> global $connSiggo  (exclusiva para as dao's relacionadas de produto com seguro)

//se não for portal, então, inclui config da intranet
if(_SISTEMA_NOME_ != "PORTAL_SERVICOS"){
	require_once(_SITEDIR_."lib/config.php");
}

//gerencia Cotação (Orçamento)
require(_MODULEDIR_ . "Produto_Com_Seguro/Action/SeguroCotacao.class.php");

//gerencia Proposta
require(_MODULEDIR_ . "Produto_Com_Seguro/Action/SeguroProposta.class.php");

//gerencia Apólice
require(_MODULEDIR_ . "Produto_Com_Seguro/Action/SeguroApolice.class.php");

//manipula os dados no BD
require(_MODULEDIR_ . "Produto_Com_Seguro/DAO/ProdutoComSeguroDAO.class.php");

//classe reponsável em enviar os e-mails
require_once (_MODULEDIR_ .'Principal/Action/ServicoEnvioEmail.php');


/**
 * Classe responsável em gerenciar as chamadas do fluxo desde a inclusão de uma cotação ou proposta, até a geração de uma apólice
 *  - Pode se instanciada dentro do ambiente da intranet(SASCAR)
 *  - Atualmente será usada pelo CRM
 *
 */
class ProdutoComSeguro{

	/**
	 * Fornece acesso aos dados do BD necessários para o módulo
	 * @property ProdutoComSeguroDAO
	 */
	private $dao;
	
   //cotação
	private $tipo_pessoa;
	private $cpf_cgc;
	private $cep;
	private $codigo_fipe;
	private $ano_modelo;
	private $carro_zero;
	private $tipo_combustivel;
	private $uso_veiculo;
	private $finalidade_uso_veiculo;
	private $classe_produto;	
	private $identificador_corretor;    
    private $id_produto_cobertura;
    private $valor_lmi_cobertura;
    private $valor_franquia_cobertura;
    
	//proposta
	private $cotacao_numero;
	private $contrato_numero;
	private $cliente_nome;
	private $cliente_sexo;
	private $cliente_estadocivil;
	private $cliente_profissao;
	private $cliente_dtnasc;
	private $cliente_pep1;
	private $cliente_pep2;
	private $cliente_residencialddd;
	private $cliente_residencialfone;
	private $cliente_celularddd;
	private $cliente_celularfone;
	private $cliente_email;
	private $cliente_endereco;
	private $cliente_endereco_num;
	private $cliente_complemento;
	private $cliente_cidade;
	private $cliente_uf;
	private $veiculo_placa;
	private $veiculo_chassi;
	private $veiculo_utilizacao;
	private $cliente_segurotipo;
	private $forma_pagamento;

	private $identificador_corretor_default;
	
	//apólice
	private $dt_instalacao_equipamento;
	private $dt_ativacao_equipamento;
	private $cod_representante;
	private $usuario_logado;
	private $numero_ordem_servico;
	private $origem_sistema; //Intranet, Portal
	private $origem_chamada; //local ou aplicação que está solicitando os serviços ex: rel_produto_com_seguro *sem extensão do arquivo

	private $flagVigencia; // Mantis 7005
	
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
		
		$this->dao  = new ProdutoComSeguroDAO($connSiggo);
		
		//instância de classe de configurações de servidores para envio de email
		$this->servicoEnvioEmail = new ServicoEnvioEmail();
	}
	
	
	/**
	 * Método responsável em gerenciar a inclusão de uma cotação e retornar a resposta do WS
	 * 
	 * @return Ambigous <string, multitype:string >
	 */
	public function processarCotacao(){
		
		$seguroCotacao = new SeguroCotacao();
		
		$seguroCotacao->setTipoPessoa($this->getTipoPessoa());
		$seguroCotacao->setCpf_cgc($this->removerAcentos($this->getCpf_cgc()));
		$seguroCotacao->setCep($this->getCep());
		$seguroCotacao->setCodigo_fipe($this->removerAcentos($this->getCodigo_fipe()));
		$seguroCotacao->setAno_modelo($this->getAno_modelo());
		$seguroCotacao->setCarro_zero($this->getCarro_zero());
		$seguroCotacao->setTipo_combustivel($this->getTipo_combustivel());
		$seguroCotacao->setUso_veiculo($this->getUso_veiculo());
		$seguroCotacao->setFinalidade_uso_veiculo($this->getFinalidade_uso_veiculo());
		$seguroCotacao->setClasseProduto($this->getClasseProduto());
		$seguroCotacao->setCorretor($this->getCorretor());        
        $seguroCotacao->setIdProdutoCobertura($this->getIdProdutoCobertura());
        $seguroCotacao->setValorLmiCobertura($this->getValorLmiCobertura());
        $seguroCotacao->setValorFranquiaCobertura($this->getValorFranquiaCobertura());
        $seguroCotacao->setCdProduto($this->getCdProduto());
        
		$retorno = $seguroCotacao->processarCotacao();

		return $retorno;
	}
		
	/**
	 * Método responsável em gerenciar a inclusão de uma proposta e retornar a resposta do WS
	 * 
	 * @return Ambigous <string, multitype:string >
	 */
	public function processarProposta(){
		
		$seguroProposta = new SeguroProposta();
		
		$seguroProposta->setCotacaoNumero($this->getCotacaoNumero());
		$seguroProposta->setContratoNumero($this->getContratoNumero());
		$seguroProposta->setClienteNome($this->removerAcentos($this->getClienteNome()));
		$seguroProposta->setClienteSexo($this->getClienteSexo());
		$seguroProposta->setClienteEstadoCivil($this->getClienteEstadoCivil());
		$seguroProposta->setClienteProfissao($this->getClienteProfissao());
		$seguroProposta->setClienteDataNascimento($this->getClienteDataNascimento());
		$seguroProposta->setClientePep1($this->removerAcentos($this->getClientePep1()));
		$seguroProposta->setClientePep2($this->removerAcentos($this->getClientePep2()));
		$seguroProposta->setClienteResidencialDdd($this->getClienteResidencialDdd());
		$seguroProposta->setClienteResidencialFone($this->getClienteResidencialFone());
		$seguroProposta->setClienteCelularDdd($this->getClienteCelularDdd());
		$seguroProposta->setClienteCelularFone($this->getClienteCelularFone());
		$seguroProposta->setClienteEmail($this->removerAcentos($this->getClienteEmail()));
		$seguroProposta->setClienteEndereco($this->removerAcentos($this->getClienteEndereco()));
		$seguroProposta->setClienteEnderecoNumero($this->getClienteEnderecoNumero());		
		$seguroProposta->setClienteComplemento($this->removerAcentos($this->getClienteComplemento()));
		$seguroProposta->setClienteCidade($this->removerAcentos($this->getClienteCidade()));
		$seguroProposta->setClienteUf($this->getClienteUf());
		$seguroProposta->setVeiculoPlaca($this->getVeiculoPlaca());
		$seguroProposta->setVeiculoChassi($this->getVeiculoChassi());
		$seguroProposta->setVeiculoUtilizacao($this->getVeiculoUtilizacao());
		$seguroProposta->setClienteSeguroTipo($this->getClienteSeguroTipo());
		$seguroProposta->setFormaPagamento($this->getFormaPagamento());
		$seguroProposta->setClasseProduto($this->getClasseProduto());
		$seguroProposta->setCorretor($this->getCorretor());
        $seguroProposta->setVigenciaContrato($this->getVigenciaContrato());
        $seguroProposta->setCdProduto($this->getCdProduto());
		
		$retorno = $seguroProposta->processarProposta();
		
		return $retorno;
		
	}

	/**
	 * Método responsável em gerenciar a inclusão de uma apólice e retornar a resposta do WS
	 * 
	 * @return Ambigous <string, multitype:string >
	 */
	public function processarApolice(){
		
		$seguroApolice = new SeguroApolice();
		
		$seguroApolice->setContratoNumero($this->getContratoNumero());
		$seguroApolice->setDataInstalacaoEquipamento($this->getDataInstalacaoEquipamento());
		$seguroApolice->setDataAtivacaoEquipamento($this->getDataAtivacaoEquipamento());
		$seguroApolice->setClasseProduto($this->getClasseProduto());
		$seguroApolice->setCodUsuarioLogado($this->getCodUsuarioLogado());
		$seguroApolice->setCodigoRepresentante($this->getCodigoRepresentante());
		$seguroApolice->setNumOrdemServico($this->getNumOrdemServico());
		$seguroApolice->setOrigemChamada($this->getOrigemChamada());
		$seguroApolice->setOrigemSistema($this->getOrigemSistema());

		// Mantis 7005
		$seguroApolice->setFlagVigencia($this->getFlagVigencia());
		
		$retorno = $seguroApolice->processarApolice();
		
		return $retorno;
	}
	
	
	/**
	 * Retorno objeto com as mensagens de/para ou de erros 
	 * @param int $cod_msg
	 * @return object
	 */
	public function getMensagem($cod_msg){
		
		return $this->dao->getMensagem($cod_msg);
		
	}
	
	
	/**
	 * Retorna e-mail de teste da tabela de parâmetros 
	 * @return object
	 */
	public function getEmailTeste(){
		return $this->dao->getEmailTeste();
	}
	
	
	/**
	 * Remove acentuação de string.
	 * @param String $str
	 * @return String
	 */
	public function removerAcentos($str){
		 
		$busca     = array("à","á","ã","â","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","õ","ô","ö","ù","ú","û","ü","ç", "'", '"','º','ª','°', '&');
		$substitui = array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","c", "" , "" ,'' ,'' ,'', '');
		 
		$str       = str_replace($busca,$substitui,$str);
		 
		$busca     = array("À","Á","Ã","Â","Ä","È","É","Ê","Ë","Ì","Í","Î","Ï","Ò","Ó","Õ","Ô","Ö","Ù","Ú","Û","Ü","Ç","‡","“", "<", ">" );
		$substitui = array("A","A","A","A","A","E","E","E","E","I","I","I","I","O","O","O","O","O","U","U","U","U","C", ""  ,"" , "" , "");
		 
		$str       = str_replace($busca,$substitui,$str);
		return $str;
	}
	
	
	/**
	 * Valida se a data contém formato válido xx-xx-xxxx ou xx/xx/xxxx
	 * Retirar o hifen (-)  ou barra (/) da data e inverte o dia, mes e ano (01122001) para ano, mes e dia (20011201)
	 *
	 * @param string $data
	 * @return string
	 */
	public function validarData($data){
			
		if(strpos($data, '-')){
	
			list($dia, $mes, $ano) = explode("-", $data);
	
			if (!checkdate($mes, $dia, $ano)){
				return false;
			}
			
			if(strlen($ano) != 4){
				return false;
			}
				
			$nova_data = $ano.$mes.$dia;
	
		}elseif(strpos($data, '/')){
			
			list($dia, $mes, $ano) = explode("/", $data);
				
			if (!checkdate($mes, $dia, $ano)){
				return false;
			}
			
			if(strlen($ano) != 4){
				return false;
			}
				
			$nova_data = $ano.$mes.$dia;
	
		}else{
				
			return false;
		}
	
		return $nova_data;
	
	}
	
	/**
	 * Recebe data no formato 20111215 e retorna 15/12/2011
	 * 
	 * @param date $data
	 * @return string
	 */
	public function inverterData($data){
		
        $data = substr($data,0,4) . "/" . substr($data,4,2) . "/" . substr($data,6,2);
        
        $nova_data = date('d/m/Y', strtotime($data));
         
        return $nova_data;
    }
		

	/**
	 * Envia e-mail de acordo os parâmetros passados, não retornar excessões para o processo continuar
	 * Em ambiente de teste a variável $endereco_email e substituída por endereço de e-mail da tabela  parametros_configuracoes_sistemas
	 * 
	 * @param string $endereco_email
	 * @param string $corpo_mail
	 * @param string $assunto_mail
	 * @param int $servidor
	 * @return boolean
	 */
	public function enviarEmail($endereco_email, $corpo_mail, $assunto_mail, $servidor){

		//ambiente de testes
		if($_SERVER["SERVER_ADDR"] == '172.16.2.57' || $_SESSION["servidor_teste"] == 1 ||
		   $_SERVER['HTTP_HOST'] ==  '192.168.56.101' ||
		  (strstr($_SERVER['REQUEST_URI'], 'teste/') ||
		   strstr($_SERVER['REQUEST_URI'], 'desenvolvimento/')	||
		   $_SERVER['HTTP_HOST'] == 'homologacao.sascar.com.br')){
		
			//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
			$emailTeste = $this->dao->getEmailTeste();

			$endereco_email = $emailTeste->pcsidescricao;
		}

		//envia o email
		$envio_email = $this->servicoEnvioEmail->enviarEmail(

				$endereco_email,
				$assunto_mail,
				$corpo_mail,
				$arquivo_anexo = null,
				$email_copia = null,
				$email_copia_oculta = null,
				$servidor,
				$emailTeste->pcsidescricao//$email_desenvolvedor = null
		);


		//imprime email que será enviado para o cliente em ambiente de testes
		/*if(strstr($_SERVER['HTTP_HOST'], $_SERVER['SERVER_ADDR'])){
			print($corpo_mail);
			print('<br/><br/>');
		}*/

		return true;

	}
	
	
	//sets e gets Cotação
	public function setTipoPessoa($valor){
		$this->tipoPessoa = $valor;
	}
	
	public function getTipoPessoa(){
		return $this->tipoPessoa;
	}
	
	public function setCpf_cgc($valor){
		$this->cpf_cgc = $valor;
	}
	
	public function getCpf_cgc(){
		return $this->cpf_cgc;
	}
	
	public function setCep($valor){
		$this->cep = $valor;
	}
	
	public function getCep(){
		return $this->cep;
	}
	
	public function setCodigo_fipe($valor){
		$this->codigo_fipe = $valor;
	}
	
	public function getCodigo_fipe(){
		return $this->codigo_fipe;
	}

	public function setAno_modelo($valor){
		$this->ano_modelo = $valor;
	}
	
	public function getAno_modelo(){
		return $this->ano_modelo;
	}
	
	public function setCarro_zero($valor){
		$this->carro_zero = $valor;
	}
	
	public function getCarro_zero(){
		return $this->carro_zero;
	}

	public function setTipo_combustivel($valor){
		$this->tipo_combustivel = $valor;
	}
	
	public function getTipo_combustivel(){
		return $this->tipo_combustivel;
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
	
	
	//set e gets Proposta
	public function setCotacaoNumero($valor){
		$this->cotacao_numero = $valor;
	}
	public function getCotacaoNumero(){
		return $this->cotacao_numero;
	}
	
	public function setContratoNumero($valor){
		$this->contrato_numero = $valor;
	}
	public function getContratoNumero(){
		return $this->contrato_numero;
	}
	
	public function setClienteNome($valor){
		$this->cliente_nome = $valor;
	}
	public function getClienteNome(){
		return $this->cliente_nome;
	}
	
	public function setClienteSexo($valor){
		$this->cliente_sexo = $valor;
	}
	public function getClienteSexo(){
		return $this->cliente_sexo;
	}
	
	public function setClienteEstadoCivil($valor){
		$this->cliente_estadocivil = $valor;
	}
	public function getClienteEstadoCivil(){
		return $this->cliente_estadocivil;
	}
	
	public function setClienteProfissao($valor){
		$this->cliente_profissao = $valor;
	}
	public function getClienteProfissao(){
		return $this->cliente_profissao;
	}
	
	public function setClienteDataNascimento($valor){
		$this->cliente_dtnasc = $valor;
	}
	public function getClienteDataNascimento(){
		return $this->cliente_dtnasc;
	}
	
	public function setClientePep1($valor){
		$this->cliente_pep1 = $valor;
	}
	public function getClientePep1(){
		return $this->cliente_pep1;
	}
	
	public function setClientePep2($valor){
		$this->cliente_pep2 = $valor;
	}
	public function getClientePep2(){
		return $this->cliente_pep2;
	}
	
	public function setClienteResidencialDdd($valor){
		$this->cliente_residencialddd = $valor;
	}
	public function getClienteResidencialDdd(){
		return $this->cliente_residencialddd;
	}
	
	public function setClienteResidencialFone($valor){
		$this->cliente_residencialfone = $valor;
	}
	public function getClienteResidencialFone(){
		return $this->cliente_residencialfone;
	}

	public function setClienteCelularDdd($valor){
		$this->cliente_celularddd = $valor;
	}
	public function getClienteCelularDdd(){
		return $this->cliente_celularddd;
	}
	
	public function setClienteCelularFone($valor){
		$this->cliente_celularfone = $valor;
	}
	public function getClienteCelularFone(){
		return $this->cliente_celularfone;
	}
	
	public function setClienteEmail($valor){
		$this->cliente_email = $valor;
	}
	public function getClienteEmail(){
		return $this->cliente_email;
	}
	
	public function setClienteEndereco($valor){
		$this->cliente_endereco = $valor;
	}
	public function getClienteEndereco(){
		return $this->cliente_endereco;
	}
	
	public function setClienteEnderecoNumero($valor){
		$this->cliente_endereco_num = $valor;
	}
	public function getClienteEnderecoNumero(){
		return $this->cliente_endereco_num;
	}
	
	public function setClienteComplemento($valor){
		 $this->cliente_complemento = $valor;
	}
	public function getClienteComplemento(){
		return $this->cliente_complemento;
	}
	
    public function setClienteCidade($valor){
		 $this->cliente_cidade = $valor;
	}
	public function getClienteCidade(){
		return $this->cliente_cidade;
	}
	
	public function setClienteUf($valor){
		$this->cliente_uf = $valor;
	}
	public function getClienteUf(){
		return $this->cliente_uf;
	}
	
	public function setVeiculoPlaca($valor){
		$this->veiculo_placa = $valor;
	}
	public function getVeiculoPlaca(){
		return $this->veiculo_placa;
	}
	
	public function setVeiculoChassi($valor){
		$this->veiculo_chassi = $valor;
	}
	public function getVeiculoChassi(){
		return $this->veiculo_chassi;
	}
	
	public function setVeiculoUtilizacao($valor){
		$this->veiculo_utilizacao = $valor;
	}
	public function getVeiculoUtilizacao(){
		return $this->veiculo_utilizacao;
	}
	
	public function setClienteSeguroTipo($valor){
		$this->cliente_segurotipo = $valor;
	}
	public function getClienteSeguroTipo(){
		return $this->cliente_segurotipo;
	}
	
	public function setFormaPagamento($valor){
		$this->forma_pagamento = $valor;
	}
	public function getFormaPagamento(){
		return $this->forma_pagamento;
	}
	
	//sets e gets apólice
	public function setDataInstalacaoEquipamento($valor){
		$this->dt_instalacao_equipamento = $valor;
	}
	public function getDataInstalacaoEquipamento(){
		return $this->dt_instalacao_equipamento;
	}
	
	public function setDataAtivacaoEquipamento($valor){
		$this->dt_ativacao_equipamento = $valor;
	}
	public function getDataAtivacaoEquipamento(){
		return $this->dt_ativacao_equipamento;
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

	public function setCorretorDefault($identificadorCorretor){
		$this->identificador_corretor_default = $identificadorCorretor;
	}
	
	public function getCorretorDefault(){
		return $this->identificador_corretor_default;
	}

	// Mantis 7005
	public function setFlagVigencia($flagVigencia){
		$this->flagVigencia = $flagVigencia;
	}

	public function getFlagVigencia(){
		return isset($this->flagVigencia) ? $this->flagVigencia : true;
	}
    
    public function setVigenciaContrato($vigencia){
		$this->vigencia = $vigencia;
}
	
	public function getVigenciaContrato(){
		return $this->vigencia;
	}
    
    public function setCdProduto($cd_produto){
		$this->cd_produto = $cd_produto;
}
	
	public function getCdProduto(){
		return $this->cd_produto;
	}
}
