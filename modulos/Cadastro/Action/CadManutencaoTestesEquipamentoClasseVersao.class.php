<?php
/**
* @author	Emanuel Pires Ferreira
* @email	epferreira@brq.com
* @since	11/01/2013
* */


require_once (_MODULEDIR_ . 'Cadastro/DAO/CadManutencaoTestesEquipamentoClasseVersaoDAO.class.php');

/**
 * Trata requisições do módulo financeiro para efetuar pagamentos 
 * de títulos com forma de cobrança 'cartão de crédito' 
 */
class CadManutencaoTestesEquipamentoClasseVersao {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property VisualizarTransacoesDAO
	 */
	private $manutencaoEquipamentosDAO;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct() 
    {
		global $conn;
        
        $this->manutencaoEquipamentosDAO = new CadManutencaoTestesEquipamentoClasseVersaoDAO($conn);
    }
    
    public function buscaEquipamentosProjeto()
    {
        return $this->manutencaoEquipamentosDAO->buscaEquipamentosProjeto();
    }
    
    public function buscaEquipamentosClasse()
    {
        return $this->manutencaoEquipamentosDAO->buscaEquipamentosClasse();
    }
    
    public function buscaEquipamentosVersao($encode)
    {
        return $this->manutencaoEquipamentosDAO->buscaEquipamentosVersao($encode);
    }

    /**
     * Action de pesquisa dos equipamentos para teste
     */
    public function pesquisar()
    {
        return $this->manutencaoEquipamentosDAO->pesquisar();
    }
    
    public function novo() 
    {
        
    }
    
    public function editar() 
    {
        $view = $this->manutencaoEquipamentosDAO->editar();
        
        $view['comandos']            = $this->manutencaoEquipamentosDAO->listaComandos(false);
        $view['comandosCadastrados'] = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false);
        $view['alertas']             = $this->manutencaoEquipamentosDAO->listaAlertasPanico(false);
        $view['alertasCadastrados']  = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false);
        $view['testes']              = $this->manutencaoEquipamentosDAO->listaTestes(false);
        $view['dependentes']         = $this->manutencaoEquipamentosDAO->listaTestesDependentes(false);
        
        return $view;
    }
    
    public function copiar()
    {
    	$view = $this->manutencaoEquipamentosDAO->copiar();
    	
    	return $view;
    }
    
    public function cadastraNovoTeste()
    {
        return $this->manutencaoEquipamentosDAO->cadastraNovoTeste();
    }
    
    public function salvarNovoComando()
    {
        return $this->manutencaoEquipamentosDAO->salvarNovoComando();
    }
    
    public function salvarNovoAlertaPanico()
    {
        return $this->manutencaoEquipamentosDAO->salvarNovoAlertaPanico();
    }
    
    public function listaComandos()
    {
        return $this->manutencaoEquipamentosDAO->listaComandos();
    }
    
    public function listaTestes()
    {
        return $this->manutencaoEquipamentosDAO->listaTestes();
    }
    
    public function listaTestesDependentes()
    {
        return $this->manutencaoEquipamentosDAO->listaTestesDependentes();
    }
    
    public function listaComandosCadastrados()
    {
        return $this->manutencaoEquipamentosDAO->listaComandosCadastrados();
    }
    
    public function listaAlertasPanico()
    {
        return $this->manutencaoEquipamentosDAO->listaAlertasPanico();
    }
    
    public function listaAlertasPanicoCadastrados()
    {
        return $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados();
    }
    
    public function excluiComando()
    {
        return $this->manutencaoEquipamentosDAO->excluiComando();
    }
    
    public function verificaDependenciaComando()
    {
        return $this->manutencaoEquipamentosDAO->verificaDependenciaComando();
    }
    
    public function excluiAlertaPanico()
    {
        return $this->manutencaoEquipamentosDAO->excluiAlertaPanico();
    }
    
    public function verificaIntegridadeTeste()
    {
        return $this->manutencaoEquipamentosDAO->verificaIntegridadeTeste();
    }
    
    public function substituirTeste()
    {
        $epcvoid_ref = $_POST['epcvoid_ref'];
        $epcvoid     = $_POST['epcvoid'];
        
        //lista comandos cadastrados no teste existente
        $arrComandosCadastrados = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false);
        
        //apaga comandos existentes no teste
        if(count($arrComandosCadastrados['comandos']) > 0) {
            foreach($arrComandosCadastrados['comandos'] as $comando) {
                $this->manutencaoEquipamentosDAO->excluiComando($comando['ecmtoid']);
            }
        }
        
        //lista alertas cadastrados no teste existente
        $arrAlertasCadastrados = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false);
        
        //apaga alertas existentes no teste
        if(count($arrAlertasCadastrados['alertas']) > 0) {
            foreach($arrAlertasCadastrados['alertas'] as $alerta) {
                $this->manutencaoEquipamentosDAO->excluiAlertaPanico($alerta['epntoid']);
            }
        }
        
        //lista comandos existentes no teste de referência
        $arrComandosCadastradosRef = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false, $epcvoid_ref);
        
        //copia para o teste existente os comandos do teste de referência
        if(count($arrComandosCadastradosRef['comandos']) > 0) {
            foreach($arrComandosCadastradosRef['comandos'] as $comando) {
                $this->manutencaoEquipamentosDAO->salvarNovoComando($epcvoid, $comando['cmdoid'], $comando['eptpoid']);
            }
        }
        
        //lista alertas existentes no teste de referência
        $arrAlertasCadastradosRef = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false, $epcvoid_ref);
                
        //copia para o teste existente os alertas do teste de referência3
        if(count($arrAlertasCadastradosRef['alertas']) > 0) {
            foreach($arrAlertasCadastradosRef['alertas'] as $alerta) {
                $this->manutencaoEquipamentosDAO->salvarNovoAlertaPanico($epcvoid, $alerta['pantoid'], $alerta['eptpoid']);
            }
        }
        
        return "ok";
    }

    public function copiarTeste()
    {
        $epcvoid_ref = $_POST['epcvoid_ref'];
        $epcvoid     = $_POST['epcvoid'];
        
        //lista comandos existentes no teste de referência
        $arrComandosCadastradosRef = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false, $epcvoid_ref);
        
        //copia para o teste existente os comandos do teste de referência
        if(count($arrComandosCadastradosRef['comandos']) > 0) {
            foreach($arrComandosCadastradosRef['comandos'] as $comando) {
                $this->manutencaoEquipamentosDAO->salvarNovoComando($epcvoid, $comando['cmdoid'], $comando['eptpoid']);
            }
        }
        
        //lista alertas existentes no teste de referência
        $arrAlertasCadastradosRef = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false, $epcvoid_ref);
                
        //copia para o teste existente os alertas do teste de referência3
        if(count($arrAlertasCadastradosRef['alertas']) > 0) {
            foreach($arrAlertasCadastradosRef['alertas'] as $alerta) {
                $this->manutencaoEquipamentosDAO->salvarNovoAlertaPanico($epcvoid, $alerta['pantoid'], $alerta['eptpoid']);
            }
        }
        
        return "ok";
    }
    
    public function excluiTeste()
    {
    	$epcvoid = $_POST['epcvoid'];

    	//lista comandos existentes no teste
    	$arrComandosCadastrados = $this->manutencaoEquipamentosDAO->listaComandosCadastrados();
    	
    	//exclui todos os comandos do teste
    	if(count($arrComandosCadastradosRef['comandos']) > 0) {
        	foreach($arrComandosCadastradosRef['comandos'] as $comando) {
        		$this->manutencaoEquipamentosDAO->excluiComando($comando['ecmtoid']);
        	}
        }
    	
    	//lista alertas existentes no teste
    	$arrAlertasCadastrados = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados();
    	
    	//exclui todos os alertas do teste
    	if(count($arrAlertasCadastradosRef['alertas']) > 0) {
        	foreach($arrAlertasCadastradosRef['alertas'] as $alerta) {
        		$this->manutencaoEquipamentosDAO->excluiAlertaPanico($alerta['epntoid']);
        	}
        }
    	
    	return $this->manutencaoEquipamentosDAO->excluiTeste();
    }

}