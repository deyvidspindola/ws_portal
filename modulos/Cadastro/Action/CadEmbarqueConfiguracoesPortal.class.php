<?php
/**
* @author	Emanuel Pires Ferreira
* @email	epferreira@brq.com
* @since	11/01/2013
* */


require_once (_MODULEDIR_ . 'Cadastro/DAO/CadEmbarqueConfiguracoesPortalDAO.class.php');

/**
 * Trata requisições do módulo financeiro para efetuar pagamentos 
 * de títulos com forma de cobrança 'cartão de crédito' 
 */
class CadEmbarqueConfiguracoesPortal {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property CadEquipamentoProjetoDAO
	 */
	private $embarqueConfiguracoesPortalDAO;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct() 
    {
		global $conn;
        
        $this->embarqueConfiguracoesPortalDAO = new CadEmbarqueConfiguracoesPortalDAO($conn);
    }
    
    public function buscaGrupos()
    {
        return $this->embarqueConfiguracoesPortalDAO->buscaGrupos();
    }
    
    public function buscaEquipamentosProjeto()
    {
        return $this->embarqueConfiguracoesPortalDAO->buscaEquipamentosProjeto();
    }
    
    public function buscaEquipamentosClasse()
    {
        return $this->embarqueConfiguracoesPortalDAO->buscaEquipamentosClasse();
    }
    
    public function buscaEquipamentosVersao($encode)
    {
        return $this->embarqueConfiguracoesPortalDAO->buscaEquipamentosVersao($encode);
    }
    
    /**
     * Action de pesquisa dos equipamentos para teste
     */
    public function pesquisar()
    {
        return $this->embarqueConfiguracoesPortalDAO->pesquisar();
    }
    
    public function novo() 
    {
        
    }
    
    public function salvar()
    {
        return $this->embarqueConfiguracoesPortalDAO->salvar();
    }
    
    public function editar() 
    {
        $view = $this->embarqueConfiguracoesPortalDAO->editar();
        
        $view['comandosCadastrados'] = $this->embarqueConfiguracoesPortalDAO->listaComandosCadastradosGrupo(false);

        return $view;
    }
    
    public function buscaProdutos()
    {
        return $this->embarqueConfiguracoesPortalDAO->buscaProdutos();
    

    }
    
    public function listaComandosCadastrados()
    {
        return $this->embarqueConfiguracoesPortalDAO->listaComandosCadastrados();
    }
    
    public function listaComandosCadastradosGrupo()
    {
        return $this->embarqueConfiguracoesPortalDAO->listaComandosCadastradosGrupo();
    }
    
    public function verificaIntegridadeComando()
    {
        return $this->embarqueConfiguracoesPortalDAO->verificaIntegridadeComando();
    }
    
    public function excluiComando()
    {
        return $this->embarqueConfiguracoesPortalDAO->excluiComando();
    }
    
    public function salvarNovoComando()
    {
        return $this->embarqueConfiguracoesPortalDAO->salvarNovoComando();
    }
    
    public function excluiGrupo()
    {
        $eptcfoid = $_POST['eptcfoid'];

        //lista comandos existentes no teste
        $arrComandosCadastrados = $this->embarqueConfiguracoesPortalDAO->listaComandosCadastradosGrupo(false);
        
        //exclui todos os comandos do teste
        if(count($arrComandosCadastradosRef['comandos']) > 0) {
            foreach($arrComandosCadastradosRef['comandos'] as $comando) {
                $this->embarqueConfiguracoesPortalDAO->excluiComando($comando['epccoid']);
            }
        }
        
        return $this->embarqueConfiguracoesPortalDAO->excluiGrupo();
    }

}