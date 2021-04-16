<?php

/**
 * @file CadLayoutKitBoasVindas.class.php
 * @author Keidi Nienkotter
 * @version 16/01/2013 10:56:40
 * @since 16/01/2013 10:56:40
 * @package SASCAR CadLayoutKitBoasVindas.class.php 
 */

require 'modulos/Cadastro/DAO/CadLayoutKitBoasVindasDAO.class.php';

class CadLayoutKitBoasVindas {
    
    private $dao;
    
    public function __construct() {
        
        $this->dao = new CadLayoutKitBoasVindasDAO();
    }
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }
    
    public function index() {
        
		cabecalho();
		
        $this->comboConfiguracoes = $this->dao->getConfiguracoes();
        $this->comboPropostas = $this->dao->getPropostas();
        $this->comboContratos = $this->dao->getContratos();
        $this->comboLayouts = $this->dao->getLayouts();
        $this->comboTipoLayouts = $this->dao->getTipoLayouts();
        
        $this->comboClasses = $this->dao->getClasses();
        $this->comboServidores = $this->dao->getServidores();
        
		include(_MODULEDIR_.'Cadastro/View/cad_layout_kit_boas_vindas/index.php');
    }
    
    /**
     * Função: Listar os arquivos que estão no diretório de anexos.
     * Diretório: Definido no arquivo config.php
     */
    public function listarArquivoDiretorio(){
    	try{
    		//DIRETORIO onde os arquivos estão guardados
    		$diretorio = _WELCOMEKITDIR_; // Configurado em "lib/config.php";
    		$arquivos = "";
    		if (is_dir($diretorio)) {
    			if ($ponteiro = opendir($diretorio)){
	    			while ($nome_itens = readdir($ponteiro)) {
	    				$itens[] = $nome_itens;
	    			}
	    			sort($itens);
	    			foreach ($itens as $arq) {
	    				if ($arq!="." && $arq!=".."){
	    					$arquivos[]=$arq;
	    				}
	    			}
    			}else{
    				throw new Exception("Erro ao listar arquivos.");
    			}
    		}else{
    			throw new Exception("Diretório de arquivos não localizado.");
    		}

    		echo json_encode($arquivos);
    		exit();
    	}catch (Exception $e){
    		$retorno = array(
    					"error" => 1,
    					"msg"	=> utf8_encode($e->getMessage())
    				);
    		
    		echo json_encode($retorno);
    		exit;
    	}
    }
    
    /**
     *  Função: Verificar se o anexo existe na pasta antes de tentar visualiza-lo (Download)
     */
    public function verAnexo() {
    	//DIRETORIO onde os arquivos foram guardados
    	$diretorio = _WELCOMEKITDIR_; // Configurado em "lib/config.php";
    	$anexo = $_POST['anexo'];
    	    	
    	if(isset($anexo) && file_exists("{$diretorio}/".$anexo))
    	{
    		$retorno = array(
    			"error" => 0
    		);
    	}else{
    		$retorno = array(
    			"error" => 1,
    			"msg"	=> utf8_encode("Anexo não encontrado no diretório")
    		);
    	}
    	echo json_encode($retorno);
    	exit;
    }
    
    /**
     * Função: Visualizar o anexo (Download)
     */ 
    public function visualizarAnexo() {
    
    	//DIRETORIO onde os arquivos foram guardados
    	$diretorio = _WELCOMEKITDIR_; // Configurado em "lib/config.php";
    	
    	// Novo do arquivo
    	$anexo = $_GET['anexo'];
    	// Novo nome do arquivo -> Retirando espaços
        $anexoDown = str_replace(" ","_",$anexo);
    	
    	$tipo = filetype("{$diretorio}{$anexo}"); //pega o tipo do arquivo que deseja fazer o download
    	$size = filesize("{$diretorio}{$anexo}"); // pega o tamanho do arquivo
    	
    	header("Content-Description: File Transfer"); //descriptografando para fazer o download
    	header("Content-Type: ($tipo)");  // pegando o tipo
    	header("Content-lenght: ($size)"); // pegando o tamanho
    	header("Content-Disposition: attachment; filename=$anexoDown"); //verificando e inicializando o downlaod
    	readfile("{$diretorio}/{$anexo}"); //fazendo o download
    	exit;
    }
    
    public function formCadLayout() {
        
		cabecalho();
				
        $this->comboLayouts = $this->dao->getLayouts();
		
		include(_MODULEDIR_.'Cadastro/View/cad_layout_kit_boas_vindas/form_cad_layout.php');
    }
    
    public function buscarSubProposta() {
        $subPropostas = $this->dao->getSubPropostas();
        echo json_encode($subPropostas);
        exit();
    }
    
    public function carregaHtmlLayout() {
        $htmlLayout = $this->dao->getHtmlLayout();
        echo json_encode($htmlLayout);
        exit();
    }
    
    public function carregaValoresConfig() {
        $htmlLayout = $this->dao->getValoresConfig();
        echo json_encode($htmlLayout);
        exit();
    }

    public function excluiConfiguracao() {
        echo json_encode($this->deletaConfiguracao());
        exit();
    }

    public function incluiConfiguracao() {
    	$this->return = $this->gravaConfiguracao();

    	$this->index();
    }
    
    public function carregaDadosLayout(){        
        echo json_encode($this->dao->getLayout());
        exit();
    }
    
    public function excluiLayout(){        
        echo json_encode($this->deletaLayout());
        exit();
    }
    
    public function validaLayout(){                
        echo json_encode($this->validaPadrao());
        exit();
    }
    
    public function incluiLayout(){                
        echo json_encode($this->gravaPadrao());
        exit();
    }
    
    private function deletaConfiguracao() {

        $arrRetorno = array();

        try {
            $retorno = $this->dao->deletaConfiguracao($_POST['idConfig']);
            
            if($retorno === true){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("Cadastro excluído com sucesso.")
                );
            }
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        }
        return $arrRetorno;
    }

    private function gravaConfiguracao() {
        $arrRetorno = array();

        try {        	        	
            $retorno = $this->dao->gravaConfiguracao($_POST['comboConfiguracao'],$_POST['comboPropostas'],$_POST['comboSubpropostas'],$_POST['comboContratos'],$_POST['comboClasse'], $_POST['comboServidor'], $_POST['comboLayout'], $_POST['comboTipoLayout'], $_POST['arqAnexo']);
            
            if($retorno == 'insert'){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("Cadastro realizado com sucesso.")
                );
            }
            if($retorno == 'update'){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("Cadastro alterado com sucesso.")
                );
            }
            
            // Ocorreu erro
            if($retorno['error'] == 1){
            	throw new Exception($retorno['msg'],1);
            }
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_decode($e->getMessage())
            );
        }
        return $arrRetorno;
    }    
    
    private function deletaLayout() {

        $arrRetorno = array();

        try {
            $retorno = $this->dao->deletaLayout($_POST['idLayout']);
            
            if($retorno === true){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("Cadastro excluído com sucesso.")
                );
            }
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        }
        return $arrRetorno;
    }
    
    public function validaPadrao() {

        $arrRetorno = array();

        try {
            
            $padrao = false;
            if($_POST['padraoLayout'] && $_POST['padraoLayout'] == 'padrao'){
                $padrao = true;                
            }
            
            $retorno = $this->dao->validaPadrao($_POST['idLayout'],$_POST['nomeLayout'],$_POST['htmlLayoutEdicao'],$padrao);
        
            if($retorno == 'true'){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("validou layout.")
                );
            }
            
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        }
        return $arrRetorno;
    }
    
    public function gravaPadrao() {

        $arrRetorno = array();

        try {
            
            $padrao = false;
            if($_POST['padraoLayout'] && $_POST['padraoLayout'] == 'padrao'){
                $padrao = true;                
            }
            
            $retorno = $this->dao->gravaPadrao($_POST['idLayout'],$_POST['nomeLayout'],$_POST['assuntoLayout'],$_POST['htmlLayoutEdicao'],$padrao,$_POST['definePadrao']);
        
            if($retorno == 'insert'){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("Cadastro realizado com sucesso.")
                );
            }
            if($retorno == 'update'){
                $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg" => utf8_encode("Cadastro alterado com sucesso.")
                );
            }
            
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        }
        return $arrRetorno;
    }
    
}