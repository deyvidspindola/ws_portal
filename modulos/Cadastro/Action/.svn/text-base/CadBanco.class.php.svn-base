<?php
/**
* @author	Emanuel Pires Ferreira
* @email	epferreira@brq.com
* @since	13/03/2013
* */


require_once (_MODULEDIR_ . 'Cadastro/DAO/CadBancoDAO.class.php');

/**
 * Trata requisições do módulo cadastro para efetuar ações relacionadas aos Bancos 
 */
class CadBanco {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property cadBancoDAO
	 */
	private $cadBancoDAO;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct() 
    {
		global $conn;
        
        $this->cadBancoDAO = new CadBancoDAO($conn);
    }
    
    public function buscaEmpresas()
    {
        return $this->cadBancoDAO->buscaEmpresas();
    }
    
    public function buscaPlanosContabeis($encode)
    {
        return $this->cadBancoDAO->buscaPlanosContabeis($encode);
    }
    
    /**
     * Action de pesquisa dos equipamentos para teste
     */
    public function pesquisar()
    {
        return $this->cadBancoDAO->pesquisar();
    }
    
    public function novo() 
    {
        
    }
    
    public function salvar()
    {
        return $this->cadBancoDAO->salvar();
    }
    
    public function editar() 
    {
        $view = $this->cadBancoDAO->editar();
        
        return $view;
    }
    
    public function verificaIntegridade()
    {
        return $this->cadBancoDAO->verificaIntegridade();
    }
    
    public function excluiComando()
    {
        return $this->embarqueConfiguracoesPortalDAO->excluiComando();
    }
}