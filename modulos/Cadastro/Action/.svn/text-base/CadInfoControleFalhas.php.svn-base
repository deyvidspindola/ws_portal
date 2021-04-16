<?php

/**
 * @author Gustavo H Mascarenhas Machado <gustavo.machado@meta.com.br>
 */

/**
 * Arquivo DAO responsável pelas requisições ao banco de dados
 */
require _MODULEDIR_ . 'Cadastro/DAO/CadInfoControleFalhasDAO.php';

class CadInfoControleFalhas
{
	protected $_dao;
	protected $_viewPath;
	
	public function __construct()
	{	
		$this->_dao = new CadInfoControleFalhasDAO();
		$this->_viewPath = _MODULEDIR_ . 'Cadastro/View/cad_info_controle_falhas/';
	}
	
	/**
	 *  Recupera parâmetro recebido via POST
	 * @param	string		$param
	 * @return	string
	 */
	protected function _getPostParam($param)
	{
		return $this->_getParamFromRequest($param, $_POST);
	}
	
	/**
	 *  Recupera parâmetro recebido via GET
	 * @param	string		$param
	 * @return	string
	 */
	protected function _getGetParam($param)
	{
		return $this->_getParamFromRequest($param, $_GET);
	}
	
	/**
	 *  Recupera parâmetro recebido na requisição ($_REQUEST)
	 * @param	string		$param
	 * @return	string
	 */
	protected function _getRequestParam($param)
	{
		return $this->_getParamFromRequest($param, $_REQUEST);
	}
	
	/**
	 *  Recupera um parâmetro recebido em determinado tipo de requisição
	 * @param	string		$param
	 * @param	string		$param	Requisição: $_POST, $_GET, $_REQUEST
	 * @return	string
	 */
	protected function _getParamFromRequest($param, $requestType)
	{
		return isset($requestType[$param]) ? $requestType[$param] : '';
	}
	
	/**
	 * Verifica se parâmetro existe na requisição
	 * @param	string	$param
	 * @return	boolean
	 */
	protected function _hasParam($param)
	{
		$value = $this->_getRequestParam($param);
		return (bool) (strlen($value));
	}
	
	/**
	 * Verifica se uma requisição foi efetuada via POST
	 * @return	boolean
	 */
	protected function _isPost()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}
	
	/**
	 * Verifica se uma requisição foi efetuada via GET
	 * @return	boolean
	 */
	protected function _isGet()
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}
	
	/**
	 * Verifica se uma requisição foi efetuada via AJAX
	 * @return	boolean
	 */
	protected function _isAjax()
	{
		return ($_SERVER['HTTP_X_REQUESTED_WITH']
				&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	
	/**
	 * Redireciona para uma página do sistema
	 * @param	string	$target
	 * @return	void
	 */
	protected function _redirect($target)
	{
		// Recupera o protocolo utilizado (HTTP ou HTTPS)
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off")
						? "https://" : "http://";
		
		// Recupera o endereço do servidor (IP ou URI)
		$server = $_SERVER['HTTP_HOST'] . '/';
		
		// Gambi para requisição local
		if (preg_match('/sistemaWeb/', $_SERVER['REQUEST_URI']))
		{
			$server .= 'sistemaWeb/';
		}
		
		$location = $protocol . $server . $target;
		
		header("Location: {$location}");
	}
	
	/**
	 * Guarda ou recupera uma flash message da sessão
	 * @param	array	$message
	 * @return 	string|void
	 */
	public function flashMessage($message = null)
	{
		if ($message)
		{
			$_SESSION['flash_message'] = $message;			
		}
		else
		{
			$message = $_SESSION['flash_message'];
			unset($_SESSION['flash_message']);
			
			return $message;
		}
	}
	
	/**
	 * Verifica se há uma flash message guardada na sessão
	 * @return	boolean
	 */
	public function hasFlashMessage()
	{
		return (isset($_SESSION['flash_message'])
					&& strlen($_SESSION['flash_message']));
	}
	
	
	
	
	
	
	
	
	
	/*
	 * Código real abaixo, código boilerplate acima. Poderia ser abstraído em
	 * com herança.
	 */
	
	
	
	
	
	/**
	 * Ação inicial e de pesquisa
	 */
	public function index()
	{		
		// Popula comboboxes do formulário
		$equipamentos = $this->_dao->getListaEquipamentos();
		$falhas = $this->_dao->getListaFalhas();
		
		$resultado = null;
		if ($this->_hasParam('item_falha_id'))
		{
			try
			{
				$resultado = $this->_dao->getListaControleFalhas(array(
					'item_produto_id'	=> $this->_getRequestParam('item_produto_id'),
					'item_falha_id'		=> $this->_getRequestParam('item_falha_id'),
					'item_descricao'	=> $this->_getRequestParam('item_descricao')
				));			
			
				$itemFalha = $falhas[$this->_getRequestParam('item_falha_id')];
				
				$itemFalhaId = $this->_getRequestParam('item_falha_id');
			}
			catch (Exception $e)
			{
				$this->flashMessage($e->getMessage());
			}
		}
		
		require_once $this->_viewPath . 'index.php';
	}
	
	/**
	 * Ação de cadastro de novo item
	 */
	public function novo()
	{
		$arr = array(
			'item_produto_id'	=> $this->_getRequestParam('item_produto_id'),
			'item_falha_id'		=> $this->_getRequestParam('item_falha_id'),
			'item_descricao'	=> $this->_getRequestParam('item_descricao')
		);
		
		try
		{
			if ($this->_dao->inserir($arr))
			{
				$this->flashMessage('Registro Incluído.');
				
				// Monta query string da busca
				$queryString = http_build_query(array(
					'item_produto_id' 	=> $arr['item_produto_id'],
					'item_falha_id'		=> $arr['item_falha_id']
				));
				
				$this->_redirect('cad_info_controle_falhas.php?' . $queryString);
			}
			else
			{
				$this->_redirect('cad_info_controle_falhas.php');
			}
		}
		catch (Exception $e)
		{
			$this->flashMessage($e->getMessage());
			$this->_redirect('cad_info_controle_falhas.php');
		}
	}
	
	/**
	 * Ação de exclusão de um item
	 */
	public function excluir()
	{
		try
		{
			$this->_dao->deletarItem(
				$this->_getPostParam('item_id_del'),
				$this->_getPostParam('item_falha_id')
			);
			
			$this->flashMessage('Registro Excluído.');
		}
		catch (Exception $e)
		{
			$this->flashMessage($e->getMessage());
		}
	}
}