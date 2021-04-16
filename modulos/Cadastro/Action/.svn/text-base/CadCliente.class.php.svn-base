<?php

/**
 * @file CadCliente.class.php
 * @author Keidi Nienkotter
 * @version 29/07/2013 11:00:28
 * @since 29/07/2013 11:00:28
 * @package SASCAR CadCliente.class.php 
 */

require 'modulos/Cadastro/DAO/CadClienteDAO.class.php';
require _MODULEDIR_ . 'Principal/Action/PrnDadosCobranca.php';
require_once _MODULEDIR_.'Principal/Action/PrnManutencaoFormaCobrancaCliente.php';
require_once _MODULEDIR_.'Principal/Action/PrnCliente.php';
require_once _MODULEDIR_.'Principal/DAO/PrnManutencaoFormaCobrancaClienteDAO.php';



class CadCliente {
    
    /*
    * Constantes
    */
     const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
     const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";
     const MENSAGEM_SUCESSO_ALTERACAO = "Registro alterado com sucesso.";
     const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
     const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    
    private $dao;
    private $clioid;
    private $retorno;
    private $arrayUF;
    private $cd_usuario;
    private $retCliente;
    private $fn_cadastra_cliente;
    private $fn_cliente_dados_cobranca;
    private $fn_acess_quad_obr_financ_cliente;
    private $fn_cliente_dados_fiscais;
    private $siggo;
    
    public function __construct() {
    	$this->arrayUF = array('AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO');
    	$this->clioid = isset($_POST['clioid']) ? $_POST['clioid'] : $_GET['clioid'];
        $this->dao = new CadClienteDAO();
        $this->retorno = array();
        $this->cd_usuario = $_SESSION["usuario"]["oid"];
              
        $this->retCliente = isset($_GET['retCliente']) ? $_GET['retCliente'] : '';

        $this->fn_cadastra_cliente              = $_SESSION['funcao']['cadastra_cliente'];
        $this->fn_cliente_dados_cobranca        = $_SESSION['funcao']['cliente_dados_cobranca'];
        $this->fn_acess_quad_obr_financ_cliente = $_SESSION['funcao']['acess_quad_obr_financ_cliente'];
        $this->fn_cliente_dados_fiscais         = $_SESSION['funcao']['cliente_dados_fiscais'];
        //Objeto de dados para a aba Siggo
        $this->siggo = new stdClass();
                
    }
    
    public function view($action, $resultadoPesquisa = array(), $layoutCompleto = true, $abas = true) {
        

        if(!$this->clioid && $action != 'pesquisar' && $action != 'index' && $action != 'endereco' && $action != 'principal' && $action != 'principalIframe'){
        	header('Location: ?acao=principal');
            exit;
    	   
        }else{

        	if($layoutCompleto)
        		include _MODULEDIR_.'Cadastro/View/cad_cliente/header.php';
        	
        	if($abas)
        		include _MODULEDIR_.'Cadastro/View/cad_cliente/abas.php';
    
        	if($action == 'pesquisar')
        		include _MODULEDIR_.'Cadastro/View/cad_cliente/index.php';
        
        	
        	    include _MODULEDIR_.'Cadastro/View/cad_cliente/'.$action.'.php';
        
        	if($layoutCompleto)
        		include _MODULEDIR_.'Cadastro/View/cad_cliente/footer.php';
    	}
    }
    
    public function index() {
    	
        $param['comboClassesCliente'] = $this->getClassesCliente();

    	$this->view('index', $param, true, false);
    }
    
    public function pesquisar() {
    	    	
    	$params = $this->populaValoresPost();

    	$params['cpf_busca'] = str_replace(".", "", $params['cpf_busca']);
    	$params['cpf_busca'] = str_replace(".", "", $params['cpf_busca']);
    	$params['cpf_busca'] = str_replace(".", "", $params['cpf_busca']);
    	$params['cpf_busca'] = str_replace("-", "", $params['cpf_busca']);
    	$params['cpf_busca'] = str_replace("/", "", $params['cpf_busca']);
    	
    	// chama dao para consulta
    	$param['dados'] = $this->dao->pesquisar($params);
        $param['comboClassesCliente'] = $this->getClassesCliente();
    	    	    	
    	$this->view('pesquisar', $param, true, false);
    	
    }
    
    public function beneficios() {
    	
    	$this->view('beneficios');
    }
    


    /**
    * Metodo principal da Aba SIGGO
    *
    * @return void
    */
     public function siggo() {

        $clioid = isset($_GET['clioid']) ? $_GET['clioid'] : '';

        if(!empty($clioid)){
            $this->siggo->dados = $this->dao->recuperarParticularidadeSiggo($clioid);
        } else {
            $this->siggo->dados = array();
        }


        //Trata os dados de retorno do banco
        $this->siggo->clippessoa_politicamente_exposta1 =  isset( $this->siggo->dados[0]->clippessoa_politicamente_exposta1) ?  $this->siggo->dados[0]->clippessoa_politicamente_exposta1 : '';
        $this->siggo->clippessoa_politicamente_exposta2 =  isset( $this->siggo->dados[0]->clippessoa_politicamente_exposta2) ?  $this->siggo->dados[0]->clippessoa_politicamente_exposta2 : '';
        $this->siggo->clippspsoid =  isset( $this->siggo->dados[0]->clippspsoid) ?  $this->siggo->dados[0]->clippspsoid : '';
        $this->siggo->cliptipo_segurado =  isset( $this->siggo->dados[0]->cliptipo_segurado) ?  $this->siggo->dados[0]->cliptipo_segurado : '';
        $this->siggo->combo_profissao = $this->dao->recuperarProfissoesSeguradora();

        $this->view('siggo');
    }

    /**
    * Metodo de persistencia (INSERT / UPDATE) de particularidades Cliente Siggo
    *
    * @return void
    */
    public function persistirParticularidadeSiggo() {

        $params = $this->populaValoresPost();
        $chaves = array(
                'clippessoa_politicamente_exposta1',
                'clippessoa_politicamente_exposta2',
                'clippspsoid',
                'cliptipo_segurado',
                );

        foreach ($params as $chave => $valor) {

            if($valor == '' && in_array($chave,$chaves)){

                $params[$chave] = 'NULL';
            }
        }

        if(isset($params['clioid']) && !empty($params['clioid'])){

            $isAtualizacao = $this->dao->isAtualizaParticularidadeSiggo($params['clioid']);

            try {

                $this->dao->begin();

                if($isAtualizacao){
                    //UPDATE
                    $retorno = $this->dao->atualizarParticularidadeSiggo($params);
                    $msg['mensagem'] = self::MENSAGEM_SUCESSO_ALTERACAO;
                } else {
                    //INSERT
                    $retorno = $this->dao->inserirParticularidadeSiggo($params);
                    $msg['mensagem'] = self::MENSAGEM_SUCESSO_INCLUIR;
                }

                if(!$retorno) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }

                $this->dao->commit();
                $msg['status'] = 'sucesso';

            } catch (Exception $e) {

                $this->dao->rollback();
                $this->siggo->dados = array();
                $msg['status'] = 'erro';
                $msg['mensagem'] = self::MENSAGEM_ERRO_PROCESSAMENTO;

            }

        } else {
            $this->siggo->dados = array();
            $msg['status'] = 'erro';
            $msg['mensagem'] = self::MENSAGEM_ERRO_PROCESSAMENTO;
        }

        $this->retorno['status'] = $msg['status'];
        $this->retorno['mensagem'] = $msg['mensagem'];

        $this->siggo->dados = $this->dao->recuperarParticularidadeSiggo($params['clioid']);

         //Trata os dados de retorno do banco
        $this->siggo->clippessoa_politicamente_exposta1 =  isset($this->siggo->dados[0]->clippessoa_politicamente_exposta1) ?  $this->siggo->dados[0]->clippessoa_politicamente_exposta1 : '';
        $this->siggo->clippessoa_politicamente_exposta2 =  isset($this->siggo->dados[0]->clippessoa_politicamente_exposta2) ?  $this->siggo->dados[0]->clippessoa_politicamente_exposta2 : '';
        $this->siggo->clippspsoid =  isset( $this->siggo->dados[0]->clippspsoid) ?  $this->siggo->dados[0]->clippspsoid : '';
        $this->siggo->cliptipo_segurado =  isset( $this->siggo->dados[0]->cliptipo_segurado) ?  $this->siggo->dados[0]->cliptipo_segurado : '';
        $this->siggo->combo_profissao = $this->dao->recuperarProfissoesSeguradora();

         $this->view('siggo');

    }


    public function operacoes($msg = array()) {
        
        $this->clienteEnderecos = array();
        if($this->clioid){
            $this->clienteEnderecos = $this->dao->getClienteEnderecos($this->clioid);
            $this->clienteOperacoes = $this->dao->getClienteOperacoes($this->clioid);            
        }
             	
    	$this->view('operacoes');
    }

     public function criaSelectClienteEndereco($clioid){
        $clioid = isset($_POST['clioid']) ? $_POST['clioid'] : '';
        $clienteEnderecos = $this->dao->getClienteEnderecos($clioid);
        echo utf8_encode(json_encode($clienteEnderecos));
        exit();
    }
    
    public function getClienteOperacoesById(){
        $params = $this->populaValoresPost();
        $clienteOperacaoId = $this->dao->getClienteOperacoesById($params['clioid'], $params['octoid']);
        
        echo utf8_encode(json_encode($clienteOperacaoId));
        exit();
    }

    public function validaIdOperacao(){
        $params = $this->populaValoresPost();
        $retorno = $this->dao->validaIdOperacao($params);
        
        echo utf8_encode(json_encode($retorno));
        exit();
    }

    public function validaCnpjOperacao(){
        $params = $this->populaValoresPost();
        $retorno = $this->dao->validaCnpjOperacao($params);
        
        echo utf8_encode(json_encode($retorno));
        exit();
    }

    public function getEnderecoClienteOperacoesById($octoid){
        $clienteOperacaoId = $this->dao->getEnderecoClienteOperacoesById($octoid);
        return $clienteOperacaoId;
    }

    
    public function setClienteOperacao() {
        
        
        $params = $this->populaValoresPost();        

        // CNPJ
        $params['octcnpj'] = str_replace(".", "", $params['octcnpj']);
        $params['octcnpj'] = str_replace(".", "", $params['octcnpj']);
        $params['octcnpj'] = str_replace("-", "", $params['octcnpj']);
        $params['octcnpj'] = str_replace("/", "", $params['octcnpj']);

        $this->retorno = $this->dao->setClienteOperacao($params);
         
        $this->operacoes();
        
    }
    
    public function excluirClienteOperacao() {
        
        $params = $this->populaValoresPost();
        
        $this->retorno = $this->dao->excluirClienteOperacao($params);

        $this->operacoes();
        
    }
    
    public function editarClienteOperacao() {
        
        $params = $this->populaValoresPost();        

        // CNPJ
        $params['octcnpj'] = str_replace(".", "", $params['octcnpj']);
        $params['octcnpj'] = str_replace(".", "", $params['octcnpj']);
        $params['octcnpj'] = str_replace("-", "", $params['octcnpj']);
        $params['octcnpj'] = str_replace("/", "", $params['octcnpj']);
        
        $this->retorno = $this->dao->editarClienteOperacao($params);
        
        $this->operacoes();
        
    }
    
    
    public function cobranca() {

    	$prnDadosCobranca = new PrnDadosCobranca();
    	$this->formaCobranca = $prnDadosCobranca->getFormaCobranca();
    	$this->buscarDiaCobranca = $prnDadosCobranca->getDiaCobranca();    	
    	
    	// alimenta combo classe do contrato
		$this->classeEquipamento = array();
		$this->classeEquipamento = $this->getClasseEquipamento();
		 
		$this->bancosOrderByNome = array();
		$this->bancosOrderByNome = $this->getBancos("bannome","");
		 
		$this->bancosOrderByCodigo = array();
		$this->bancosOrderByCodigo = $this->getBancos("bancodigo","");
		
		$this->bandeiraCartaoCredito = array();
		$this->bandeiraCartaoCredito = $this->dao->getBandeiraCartaoCredito();
		
		$this->notaFiscalSerie = array();
		$this->notaFiscalSerie = $this->dao->getNotaFiscalSerie();
		
		$this->historicoFaturamento = array();
		$this->historicoFaturamento = $this->dao->getHistoricoFaturamento($this->clioid);
		
		$this->prazoVencimento = array();
		$this->prazoVencimento = $this->dao->getPrazoVencimento();
		
    	$this->clienteFaturamento = array();
    	if($this->clioid){
    		//busca dados cliente_faturamento
    		$this->clienteFaturamento = $this->dao->getClienteFaturamento($this->clioid);
    		$this->dadosFaturamentoCliente = $this->dao->getDadosFaturamentoCliente($this->clioid);
    		
    		global $conn;
    		$prnManutencaoFormaCobrancaClienteDAO = new PrnManutencaoFormaCobrancaClienteDAO($conn);
    		$this->dadosCobranca = $prnManutencaoFormaCobrancaClienteDAO->getInformacoes($this->clioid);
    	}
    	
    	$formaCobranca = new PrnManutencaoFormaCobrancaCliente();
    	// $this->formasCobrancaCliente = $formaCobranca->buscarCobrancasPromocionais();

    	// Verifica se tem permissao alterar vencimento
    	$this->alteraVencimento = $formaCobranca->verificaPermissaoVencimento();
    	
		// Busca permissão financeira
    	$departamentoUsuario = $_SESSION['usuario']['depoid']; //$_SESSION['funcao']['nome_nova_funcao'];

    	
    	/**
    	 * Obrigação financeira
    	 */
    	$this->obrigacao_cli = $this->dao->getObrigacaoCliente();
    	$this->software_cli = $this->dao->getSoftwareCliente();
    	$this->autorizacao_cli = $this->dao->getAutorizacaoPor();
    	$this->obrigacoes = $this->dao->getObrigacao($this->clioid);
    	$this->chat = $this->dao->getChat($this->clioid);
    	
    	$this->view('cobranca');
    }
    
    public function excluirFaturamento(){
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->excluirFaturamento($params);
    	$this->cobranca();
    }
    
    public function inserirFaturamento(){
    	echo "teste";
    	$this->cobranca();
    }
    
    public function setFaturamento(){
    	$params = $this->populaValoresPost();
    	if($params['clioid']){
    		$this->retorno = $this->dao->setFaturamento($params);
    	}
    	$this->cobranca();
    }
    
    public function excluirObrigacao(){
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->excluirObrigacao($params);
    	$this->cobranca();
    }
    
    
    public function setObrigacao(){
    	$params = $this->populaValoresPost();
    	
    	if($params['clioid']){
    		$this->retorno = $this->dao->setObrigacao($params);
    	}
    	$this->cobranca();
    }
    
    public function setCobranca(){
        $params = $this->populaValoresPost();
        
// 		$params['forma_pagamento_clidia_vcto'];
// 		$params['hdanome_titular'];
// 		$params['cccnome_cartao'];
// 		$params['clidia_mes'];
// 		$params['clidia_semana'];
// 		$params['hdatipo'];
		
        if($params['clioid']){
            $this->retorno = $this->dao->setCobranca($params);
            
            if(!is_null($params['clic_periodo_emissao']) && $this->retorno['status'] == 'sucesso'){
                $this->retorno = $this->dao->setPeriodoEmissaoNF($params);
            }elseif($this->retorno['status'] == 'sucesso'){
                $params['clicdt_inicial'] = ' null ';
                $params['clicdt_final'] = ' null ';
                $this->retorno = $this->dao->setPeriodoEmissaoNF($params);                
            }
            
        }
        $this->cobranca();
    }
    
    
    public function setEndereco(){
    	$params = $this->populaValoresPost();
        // var_dump($this->cd_usuario);
    	// if($params['clioid']){
    		$this->retorno = $this->dao->setEndereco($params);
    	// }
    	$this->endereco();
    }

    public function setEnderecoEntrega(){
        $params = $this->populaValoresPost();
        // if($params['clioid']){
            $this->retorno = $this->dao->setEnderecoEntrega($params);
        // }
        //$this->endereco();
        echo utf8_encode(json_encode($this->retorno));
    }
    
    public function particularidades(){
    	
    	$this->particularidadesContratos = array();
    	if($this->clioid){
    		// contratos
    		$this->particularidadesContratos = $this->getParticularidadesContratos($this->clioid);
    	
    		//alimenta dados grid particularidades
    		$this->particularidadesPerfil = $this->getParticularidadesPerfil($this->clioid);
    	}
    	
    	//alimenta combo tipo
    	$this->particularidadesTipo = $this->getParticularidadesTipo();
    	
    	$this->view("particularidades");
    }
    
    public function gerenciadora(){
    	 
    	if($this->clioid){
			$_POST = $this->getClienteGerenciadora($this->clioid);
		}
		
		$this->gerenciadoras = array();
		$this->gerenciadoras = $this->getGerenciadoras();

		$this->view('gerenciadora');
    }

    
     public function setGerenciadora(){
    	
    	$params = $this->populaValoresPost();
	    $this->retorno = $this->dao->setGerenciadora($params, $this->clioid, $this->cd_usuario);
	    
	    $this->gerenciadora();
    }
    
    public function contatos() {
    	
    	$this->clienteContatos = array();
    	if($this->clioid){
    		$this->clienteContatos = $this->getClienteContatos($this->clioid);
    	}
    	
		$this->view("contatos");
    }

    public function endereco(){

		// Preenche campos do formulário com dados do cliente
		$getEndereco = $this->dao->getEndereco($this->clioid);
		
		// Verifica o tipo de cliente Fisica ou Juridica
		$this->tipoPessoa = $this->dao->getTipoPessoa($this->clioid);
		
		$this->getEndereco = $getEndereco[0];
		$this->getEndereco[0]['clicep_res'] = ($getEndereco[0]['clicep_res'] == null) ? '' : $this->getCEP($getEndereco[0]['clicep_res']);
		
		// Carrega endereços de entrega
		$this->getEndFavoritos = $this->dao->getEndFavoritos($this->clioid);
		$this->enderecosEntrega = $this->dao->getEnderecoEntrega($this->clioid);
		$this->view('endereco');
    }

    public function segmentacao() {
    	
		$this->view('segmentacao');
    }
    
    public function historico() {
    	
		$this->view('historico');
    }
    
    public function principal() {
    	
        if(!empty($this->retorno)) {
            $this->clioid = $this->retorno['clioid'];
            $_GET['clioid'] = $this->clioid;
        }
        

        
    	// carrega dados Cliente
    	if($this->clioid){
    		$_POST = $this->getDadosCliente($this->clioid);
    	}
    	
        $param['comboClassesCliente'] = $this->getClassesCliente();
    	$param['arrayUF'] = $this->arrayUF;
    	
		$this->view('principal', $param);
        
    }
    
    public function principalIframe(){    	

        if(!empty($this->retorno)) {
            $this->clioid = $this->retorno['clioid'];
            $_GET['clioid'] = $this->clioid;
        }
        
    	// carrega dados Cliente
    	if($this->clioid){
    		$_POST = $this->getDadosCliente($this->clioid);
    	}
    	
    	$param['arrayUF'] = $this->arrayUF;
    	
    	$getEndereco = $this->dao->getEndereco($this->clioid);
    	$this->getEndereco = $getEndereco[0];
    
		$this->view('principalIframe', $param, false, false);
        
    }

    public function setPrincipal () {
        
        $params = $this->populaValoresPost();
        
        // tratamento campos
        // CPF
        $params['clino_cpf'] = str_replace(".", "", $params['clino_cpf']);
        $params['clino_cpf'] = str_replace("-", "", $params['clino_cpf']);
        $params['clino_cpf'] = str_replace("/", "", $params['clino_cpf']);
        
        // CNPJ
        $params['clino_cgc'] = str_replace(".", "", $params['clino_cgc']);
        $params['clino_cgc'] = str_replace("-", "", $params['clino_cgc']);
        $params['clino_cgc'] = str_replace("/", "", $params['clino_cgc']);
        
        // DATAS
        if($params['clidt_emissao_rg']){
            $clidt_emissao_rg = explode('/', $params['clidt_emissao_rg']);
            $params['clidt_emissao_rg'] = $clidt_emissao_rg[2].'-'.$clidt_emissao_rg[1].'-'.$clidt_emissao_rg[0];
        }
        
        if($params['clidt_nascimento']){
            $clidt_nascimento = explode('/', $params['clidt_nascimento']);
            $params['clidt_nascimento'] = $clidt_nascimento[2].'-'.$clidt_nascimento[1].'-'.$clidt_nascimento[0];
        }
        
        if($params['clidt_fundacao']){        
            $clidt_fundacao = explode('/', $params['clidt_fundacao']);
            $params['clidt_fundacao'] = $clidt_fundacao[2].'-'.$clidt_fundacao[1].'-'.$clidt_fundacao[0];       
        }       
        
        
        $this->retorno = $this->dao->setPrincipal($params);
        
        if($params['iframe']!=""){
	        $this->principalIframe();
        }else{
	        $this->principal();
        }

    }

    public function setSegmentacao () {
        $params = $this->populaValoresPost();
            $this->retorno = $this->dao->setSegmentacao($params['clioid'], $params['clstsgoid']);
            $this->segmentacao();

    }

    public function downloadAnexoComprovanteEndereco () {
        $caminho = 'docs_comprovante_endereco';
        if ($this->clioid != '')
        {
            $arquivo = $this->dao->getAnexo($this->clioid);
            if ($arquivo->clicomprovante_endereco != '')
            {
                $comprovante = $this->dao->getAnexo($this->clioid);
                echo $this->dao->downloadAnexo($caminho, $comprovante->clicomprovante_endereco);
            } else {
                echo 'Anexo inexistente para este cliente';
            }
            
        } else {
            echo 'Arquivo Inválido';
        }
    }

    public function setAnexoComprovanteEndereco () {
            //pasta dentro de document root, onde será salva a imagem
            $caminho = '/docs_comprovante_endereco';
            //name do $_FILES de onde vem o upload
            $arquivo = 'anexo_comprovante_residencia';
            // array de mime types permitidos, pode ser omitido pra adotar o padrão
            $permitido = array('image/*', 'application/pdf');
            //classe de upload generica, retorna status => erro / success, message => nome do arquivo em caso de sucesso,
            //tipo do erro em caso de falha, caminho => caminho que estamos passando para que o ajax possa aproveitar
            echo utf8_encode(json_encode($this->dao->uploadAnexoClioid($caminho, $arquivo, $permitido, $this->clioid)));
    }

    public function excluirAnexo () 
    {
        echo utf8_encode(json_encode($this->dao->setAnexo($this->clioid, '')));
    }
    
    public function getClassesCliente() {
    	
        return $this->dao->getClassesCliente();
        
    }
    public function getHistorico($clioid) {

        return $this->dao->getHistorico($clioid);
        
    }
    public function getBeneficio($clioid) {

        return $this->dao->getBeneficio($clioid);
        
    }
    public function getSegmentacao($clioid, $tpscodigoslug) {

        return $this->dao->getSegmentacao($clioid, $tpscodigoslug);
        
    }
    
    public function getDadosCliente($clioid) {
    	    	
        return $this->dao->getDadosCliente($clioid);
        
    }
    
    public function getClienteContatos($clioid){
    	
    	return $this->dao->getClienteContatos($clioid);
    	
    }
   
    public function getParticularidadesContratos($clioid){
    	
    	return $this->dao->getParticularidadesContratos($clioid);
    	
    }
    
    public function getParticularidadesTipo(){
    	 
    	return $this->dao->getParticularidadesTipo();
    	 
    }
    
	public function getParticularidadesPerfil($clioid){
    	 
    	return $this->dao->getParticularidadesPerfil($clioid);
    	 
    }
    
    public function getGerenciadoras(){
    	
    	return $this->dao->getGerenciadoras();
    	
    }
    
    public function getClienteGerenciadora($clioid){
        
        return $this->dao->getClienteGerenciadora($clioid);
    }
    
    public function getPais(){
        
        return $this->dao->getPais();
    }
    
    public function getEstado($paisoid = 1){
        
        return $this->dao->getEstado($paisoid);
    }
    
    public function getCidade($estoid){
        
        return $this->dao->getCidade($estoid);
    }
    
    public function getBairro($clcoid){
        
        return $this->dao->getBairro($clcoid);
    }
    
    public function getByEndereco($endereco){
        
        return $this->dao->getByEndereco($endereco);
    }
    
    public function getByCep($cep){
        
        return $this->dao->getByCep($cep);
    }

    public function criaSelectEstado($paisoid){
        $paisoid = isset($_POST['paisoid']) ? $_POST['paisoid'] : '';
        $estados =  $this->getEstado($paisoid);
        $resultado = array();
        if (count($estados) == 0)
        {
            $resultado['ocorrencia'] = 0;
        } else {
            $resultado['ocorrencia'] = count($estados);
            $count = 0;
            foreach ($estados as $d => $v) {
            $resultado['estado'][$d] = $v;
                $count ++;
            }
        }

        echo utf8_encode(json_encode($resultado));
    }

    public function criaSelectCidade($estoid){
        $estoid = isset($_POST['estoid']) ? $_POST['estoid'] : '';
        $cidades =  $this->getCidade($estoid);
        $resultado = array();
        if (count($cidades) == 0)
        {
            $resultado['ocorrencia'] = 0;
        } else {
            $resultado['ocorrencia'] = count($cidades);
            $count = 0;
            foreach ($cidades as $d => $v) {
            $resultado['cidade'][$d] = utf8_encode($v);
                $count ++;
            }
        }

        echo utf8_encode(json_encode($resultado));
    }

    public function criaSelectBairro($clcoid){
        $clcoid = isset($_POST['clcoid']) ? $_POST['clcoid'] : '';
        $bairros =  $this->getBairro($clcoid);
        $resultado = array();
        if (count($bairros) == 0)
        {
            $resultado['ocorrencia'] = 0;
        } else {
            $resultado['ocorrencia'] = count($bairros);
            $count = 0;
            foreach ($bairros as $d) {
            $resultado['bairro'][$count] = utf8_encode($d);
                $count ++;
            }
        }

        echo utf8_encode(json_encode($resultado));
    }

    public function criaSelectCep(){
        $cep = isset($_POST['cep']) ? $_POST['cep'] : '';
        $dadosCep =  $this->getByCep($cep);
        $resultado = array();
        if (count($dadosCep) == 0)
        {
            $resultado['ocorrencia'] = 0;

        } else if (count($dadosCep) == 1) {
            $resultado['ocorrencia'] = 1;
            foreach ($dadosCep as $d) {
                $resultado['tipo'] = $d['clgtipo'];
                $resultado['uf'] = $d['clguf_sg'];
                $resultado['bairro'] = $d['cbanome'];
                $resultado['endereco'] = $d['clgnome'];
                $resultado['cidade'] = $d['clcnome'];
                $resultado['estadoid'] = $d['clcestoid'];
            }
        } else {
            $resultado['ocorrencia'] = count($dadosCep);
            $count = 0;
              foreach ($dadosCep as $d) {
                $resultado['enderecos'][$count] = array(
                    'tipo' => $d['clgtipo'],
                    'uf' => $d['clguf_sg'],
                    'bairro' => $d['cbanome'],
                    'endereco' => $d['clgnome'],
                    'cidade' => $d['clcnome'],
                    'estadoid' => $d['clcestoid']
                );
                $count ++;
            }
        }

        echo utf8_encode(json_encode($resultado));
    }
    public function criaSelectEndereco(){
        $endereco = isset($_POST['endereco']) ? $_POST['endereco'] : '';
        $dadosEndereco =  $this->getByEndereco($endereco);
        $resultado = array();
        if (count($dadosEndereco) == 0)
        {
            $resultado['ocorrencia'] = 0;

        } else {
            $resultado['ocorrencia'] = count($dadosEndereco);
            $count = 0;
              foreach ($dadosEndereco as $d) {
                $resultado['enderecos'][$count] = array(
                    'tipo' => $d['clgtipo'],
                    'uf' => $d['clguf_sg'],
                    'bairro' => $d['cbanome'],
                    'endereco' => $d['clgnome'],
                    'cidade' => $d['clcnome'],
                    'estadoid' => $d['clcestoid'],
                    'cep' => $d['clgcep'],
                    'descricao' => $d['descricao']
                );
                $count ++;
            }
        }

        echo utf8_encode(json_encode($resultado));
    }
        
    public function cadastroParticularidadesPerfil(){
    	
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->setParticularidadesPerfil($params);
    	$this->particularidades();
    	
    }
    
    public function excluirParticularidadePerfil(){
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->excluirParticularidadePerfil($params);
    	$this->particularidades();
    }
    
    public function cadastroClienteContato(){
    	
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->setClienteContato($params);
    	
    	$arrReplace = array(' ','(',')','-');
    	$clifone = str_replace($arrReplace, '',$params['clicfone']);
    	    	
    	    $dadosFone['clicoid']   = $this->retorno['clicoid'];
    	    $dadosFone['clicfone_array'] = array($clifone);
    	    $dadosFone['clictpfone_array'] = array('R');
    	
    	    $this->dao->setClienteContatoFone($dadosFone);
    	
    	
    	$this->contatos();
    	
    }
    
    public function excluirClienteContato(){
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->excluirClienteContato($params);
    	$this->contatos();
    }
    
    public function excluirEnderecoEntrega(){
    	$params = $this->populaValoresPost();
    	$this->retorno = $this->dao->excluirEnderecoEntrega($params);
    	$this->endereco();
    }
    
    public function getClasseEquipamento(){
    	
    	return $this->dao->getClasseEquipamento();
    	
    }
    
    public function getBancos($orderby, $clause){
         
        return $this->dao->getBancos($orderby, $clause);
         
    }
    
    public function validaCNPJ(){
        
        $params = $this->populaValoresPost();        
        
        $params['cnpj'] = preg_replace('/[^0-9]/','',$params['cnpj']);
        
        if($params['cnpj'] != '' && strlen($params['cnpj']) == 14) {
            
            $retorno = $this->dao->validaCNPJ($params['cnpj'], $params['clioid']);
            echo json_encode($retorno);
            exit();
            
        }else{
        
            echo json_encode('incorreto');
            exit();
        }
    }
    
    public function validaCPF(){
        
        $params = $this->populaValoresPost();        
        
        $params['cpf'] = preg_replace('/[^0-9]/','',$params['cpf']);
        
        if($params['cpf'] != '' && strlen($params['cpf']) == 11) {
            $retorno = $this->dao->validaCPF($params['cpf'], $params['clioid']);
            
            echo json_encode($retorno);
            exit();
            
        }else{
                        
            echo json_encode('incorreto');
            exit();
        }
         
    }
    
    public function excluirBeneficio(){

        $params = $this->populaValoresPost();
    	$this->retorno = $this->dao->excluirBeneficio($params['clboid']);
        $this->beneficios();
    }

    
    public function populaValoresPost($clearPost = false, $params = null) {
    	if(!is_null($params)):
    		$data = $params;
    	else:
    		$data = $_POST;
    	endif;
    
    	foreach($data as $key => $value):
    	if($clearPost === false) {
    		$this->$key = (is_string($value))?trim($value):$value;
    	} else
    		unset($this->$key);
    	endforeach;
    
    	return $data;
    }
    
    public function mascaraString($mascara,$string) {
        $string = str_replace(" ","",$string);
        for($i=0;$i<strlen($string);$i++) {
            $mascara[strpos($mascara,"#")] = $string[$i];
        }
        return $mascara;
    }

    public function getCPF ($cpf) {
        return str_pad($cpf, 11, 0, STR_PAD_LEFT);
    }

    public function getCNPJ ($cnpj) {
        return str_pad($cnpj, 14, 0, STR_PAD_LEFT);
    }
    
    public function getCEP ($cep) {
    	if($cep){
			return str_pad($cep, 8, 0, STR_PAD_LEFT);
    	}
    }
    
    public function verificaClienteCT($clioid) {
        if(isset($clioid)) {
            return $this->dao->verificaClienteCT($clioid);
        }
        return false;
    }

    public function getDadosClienteCT($clioid) {
        return $this->dao->getDadosClienteCT($clioid);
    }

    public function atualizaClienteCT($dados) {
        return $this->wsRest("PUT",_URL_ATUALIZA_CLIENTE_RF_NACIONAL_,$dados);
    }

    /**
     * mensagem de total de registros apresentados em grid
     */
    public function getMensagemTotalRegistros($total){
    	$arrMsg = array("0"=>"Nenhum registro encontrado", "1"=>"registro encontrado", "2"=>"registros encontrados");
    	if($total == 0){
    		return $arrMsg['0'];
    	}elseif($total == 1){
    		return $total." ".$arrMsg['1'];
    	}elseif($total > 1){
			return $total." ".$arrMsg['2'];
    	}
    }

    /**
     * Funcao para webservice REST
     * @param string $method
     * @param string $url
     * @param string $data
     */
    private function wsRest($method, $url, $data = false) {
        
        switch($method)
        {
            case 'GET':
                break;
            case 'POST':
                break;
            case 'PUT':
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    $json_data = 'w='.json_encode($data);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($json_data))
                    );
                }
                break;
        }
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        if($_SERVER['HTTP_HOST'] == '192.168.56.101' && _URLPROXY_ && _USERPROXY_){

            /* Utilizado Proxy no ambiente BRQ de desenvolvimento*/
            curl_setopt($curl, CURLOPT_PROXY, _URLPROXY_);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, _USERPROXY_);
            curl_setopt($curl, CURLOPT_PROXYPORT, 3128);          
        }       
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        
        $curl_response = curl_exec($curl);
        
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            return false;
        }

        curl_close($curl);
        $decoded = json_decode($curl_response,true);
        
        $dados = array();
        foreach ($data as $chave => $valor) {
            array_push($dados, $chave .'='. $valor);
        }

        $this->dao->gravaLOG($method,$url,implode(',',$dados),$curl_response);
        
        return $decoded;
    }
    

}