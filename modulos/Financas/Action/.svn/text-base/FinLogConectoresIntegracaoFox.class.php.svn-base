<?php

/**
 * ES ? Log Integração Fox
 *
 * @file    log_conectores_integracao_fox.php
 * @author  Diego C. Ribeiro (BRQ)
 * @since   05/11/2012
 * @version 05/11/2012
 * 
 * Script responsável por consultar o status dos protocolos enviados e atualizar os protocolos
 * que ainda não foram consultados.
 */

require 'modulos/Financas/DAO/FinLogConectoresIntegracaoFoxDAO.class.php';
require 'lib/funcoes.php';


class FinLogConectoresIntegracaoFox {
    
    private $dao;
    public $arrPesquisar = array();
    public $mensagem = null;
    public $arrDados = array();
    public $protocolosAtualizados = false;
    
    /**
     * Construtor
     * Define as ações que serão realizadas
     */
    public function __construct() {        
              
        global $conn;		
        $this->dao = new FinLogConectoresIntegracaoFoxDAO($conn);        
        
        // Ação do botão Atualizar
        if(isset($_POST['bt_atualizar'])) {
            $this->atualizar();
            // Se antes de clicar no botão atualizar, havia uma consulta, repete a consulta anterior
            if( (   isset($_SESSION['pesquisaLog']['pesq_dt_ini']) and $_SESSION['pesquisaLog']['pesq_dt_ini'] != '') 
                    or (isset($_SESSION['pesquisaLog']['pesq_dt_fim']) and $_SESSION['pesquisaLog']['pesq_dt_fim'] != '')
                    or (isset($_SESSION['pesquisaLog']['protocolo']) and $_SESSION['pesquisaLog']['protocolo'] != '')
                    or (isset($_SESSION['pesquisaLog']['somenteProtocolo']) and $_SESSION['pesquisaLog']['somenteProtocolo'] != '')){
                $this->pesquisar();
            }  
            
        // Ação do botão Limpar    
        }elseif(isset($_POST['bt_limpar'])) {
             if(isset($_SESSION['pesquisaLog'])){
                unset($_SESSION['pesquisaLog']);
            }
        
        // Ação do botão Pesquisar
        }elseif(isset($_POST['bt_pesquisar'])){

            if(isset($_SESSION['pesquisaLog'])){
                unset($_SESSION['pesquisaLog']);
            }
            foreach ($_POST as $key => $value) {
                $_SESSION['pesquisaLog'][$key] = $value;
            }
            $this->pesquisar();
            
        // Exibe os detalhes do log selecionado
        }else if(isset($_POST['acao']) and $_POST['acao'] == 'detalhes') { 
            $this->detalhes();
        
        // Pesquisa com base nos dados armazenados na sessão ao carregar a página
        }else if(isset($_SESSION['pesquisaLog'])){
            $this->pesquisar();                    
        }        
    }
    
    /**
     * Pesquisa os Logs de acordo com o formulário de pesquisa
     * @return boolean
     */
    private function pesquisar(){        
                
        // Validações
        
        // Formada a data inicial
        if(isset($_SESSION['pesquisaLog']['pesq_dt_ini'])){
            $pesq_dt_ini            = $_SESSION['pesquisaLog']['pesq_dt_ini'];
            $pesq_dt_ini            = formata_data('Ymd', $pesq_dt_ini);
        }else{
            $pesq_dt_ini = null;
        }
        
        // Formada a data final
        if(isset($_SESSION['pesquisaLog']['pesq_dt_fim'])){
            $pesq_dt_fim = $_SESSION['pesquisaLog']['pesq_dt_fim'];
            $pesq_dt_fim = formata_data('Ymd', $pesq_dt_fim);
        }else{
            $pesq_dt_fim = null;
        }
        
        // Verifica se a data final é maior ou igual a data inical
        if(isset($pesq_dt_ini) and isset($pesq_dt_fim) and ($pesq_dt_fim < $pesq_dt_ini)){            
            
            $this->mensagem = "A data final deve ser maior ou igual a data inicial";
            return false;
        }        
        
        if(isset($_SESSION['pesquisaLog']['somenteProtocolo'])){
            $this->dao->somenteProtocolo = $_SESSION['pesquisaLog']['somenteProtocolo'];
        }              
        
        $this->dao->conector    = $_SESSION['pesquisaLog']['conector'];        
        $this->dao->pesq_dt_ini = $pesq_dt_ini;
        $this->dao->pesq_dt_fim = $pesq_dt_fim;       
        $this->dao->protocolo   = $_SESSION['pesquisaLog']['protocolo']; 
        $this->dao->status      = $_SESSION['pesquisaLog']['status']; 
        $this->arrPesquisar     = $this->dao->pesquisar();    
        
        if(is_array($this->arrPesquisar) and count($this->arrPesquisar)>0){
            
            foreach($this->arrPesquisar as $key => $row){
                if(!empty($row['lifdt_inicio'])){                    
                    $data = date_create($row['lifdt_inicio']);
                    $data = date_format($data, 'd/m/Y H:i:s');
                    $row['lifdt_inicio'] = $data;                    
                    $this->arrPesquisar[$key] = $row;
                }
                if(!empty($row['lifdt_fim'])){                    
                    $data = date_create($row['lifdt_fim']);
                    $data = date_format($data, 'd/m/Y H:i:s');
                    $row['lifdt_fim'] = $data;                    
                    $this->arrPesquisar[$key] = $row;
                }
            }                        
        }
    }
    
    /**
     * Atualiza os status de todos os protocolos
     */
    private function atualizar(){
    	
        // require 'modulos/Cron/Lib/logEBS.class.php';       
    	// $log = new logEBS();
		// $log->exibirErros = false;
        
        // if(is_object($client)){        
            // if($log->consultarAtualizarProtocolosEnviados() == true){
				// if(!empty($log->mensagemErro)){
					// $this->mensagem = "Erro na atualização de alguns protocolos:" . $log->mensagemErro;
				// }else{
					// $this->mensagem = "Situação dos protocolos atualizada com sucesso!";
				// }
                $this->protocolosAtualizados = true;
            // }else{
                // $this->mensagem = "Não foram encontrados protocolos pendentes para atualizar.";
                // $this->protocolosAtualizados = false;
            // }  
        // }else{
            $this->mensagem = "Não foi possível conectar ao canal de integração para atualizar os conectores.";
        // }                
    }   
    
    /**
     * Retorna os detalhes do conector selecionado
     */
    private function detalhes(){
        
        // Armazena os dados submetidos na sessao
        foreach ($_POST as $key => $value) {
            $_SESSION['pesquisaLog'][$key] = $value;
        }
        
        $this->dao->conector    = $_SESSION['pesquisaLog']['conector'];  
        $this->dao->codigo      = $_SESSION['pesquisaLog']['codigo'];                 
        $this->arrDados         =  $this->dao->detalhes();
    }        
}

?>