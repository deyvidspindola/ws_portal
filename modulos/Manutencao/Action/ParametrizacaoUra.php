<?php

require_once _MODULEDIR_ . 'Manutencao/DAO/ParametrizacaoUraDAO.php';
require 'lib/Components/CampoBuscaX.class.php';

class ParametrizacaoUra
{
	protected $_dao;
	protected $_viewPath;
	protected $_aba;
	public $component_busca_cliente;
	
	public function __construct()
	{
		$this->_dao = new ParametrizacaoUraDAO();
		$this->_viewPath = _MODULEDIR_ . 'Manutencao/View/parametrizacao_ura/';
	}
	
	protected function setAba($default='panico'){
		$this->_aba = $default;
	}
	
	protected function getAba() {
		return $this->_aba;
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
    
    /**
     * View Helpers
     */
    public function isChecked($elm)
    {
        if (!in_array($elm, array('f', 'false')))
        {
            echo 'checked="checked"';
        }
    }
    
    public function checkArray($elm, $arr)
    {
        if ($arr && in_array($elm, $arr))
        {
            echo 'checked="checked"';
        }
    }
    
    /**
     * Actions - Estatística 
     */
    public function estatistica()
    {
    	
    	$params = array(
    			'id' 			=> 'cliente_id',
    			'name'			=> 'cliente_nome',
    			'btnFind'   	=> true,
    			'btnFindText'	=> 'Pesquisar',
    			'data'			=> array(
    					'table'           => 'clientes',
    					'fieldFindByText' => 'clinome',
    					'fieldFindById'   => 'clioid',
    					'fieldLabel'      => 'clinome',
    					'fieldReturn'     => 'clioid'
    			)
    	);
    		
    	/*
    	 * Componente resposável por gerar o campo de pesquisa por cliente
    	* */
    
    	$this->component_busca_cliente = new CampoBuscaX($params);
    	
    	
    	// Busca dados do formulário
    	
    	
    	$tiposOrdemServico       = $this->_dao->findTiposOrdemServico();   
        $tiposDefeitoAlegado	 = $this->_dao->findDefeitoAlegado();
    	$tiposContrato      	 = $this->_dao->findTiposContrato();
    	$statusOrdemServico  	 = $this->_dao->findStatusOrdemServico();
    	$statusContrato      	 = $this->_dao->findStatusContratos();
    	$ocorrenciasStatus  	 = $this->_dao->findOcorrenciasStatus();
    	$tiposAcao			     = $this->_dao->findTiposAcao();
    	$tipoStatusEstatistica   = $this->_dao->findTipoStatusEstatistica();
        $buscaClientes            = $this->_dao->findBuscaClientes(); 
            	
    	
    	// Busca o último registro salvo
    	$form = $this->_dao->findLastEst($form);    	
    	
    	$form['pueostoid'] 					 = $this->buildArray($form['pueostoid']);
    	$form['pueitem'] 				     = $this->buildArray($form['pueitem']);    	
    	$form['pueossoid']   				 = $this->buildArray($form['pueossoid']);
    	$form['puetpcoid']   				 = $this->buildArray($form['puetpcoid']);
    	$form['puecsioid'] 					 = $this->buildArray($form['puecsioid']);
    	$form['pueocostatus'] 				 = $this->buildArray($form['pueocostatus']);
    	$form['pueegaoid'] 				     = $this->buildArray($form['pueegaoid']);
    	$form['puestatus'] 				   	 = $this->buildArray($form['puestatus']);
    
    	
    	

    	$this->setAba('estatistica');
        require_once $this->_viewPath . 'estatistica.php';
    }
    
    
    public function estatisticaSalvar()
      {
      	try {
      		
      		
      		$usuario = Sistema::getUsuarioLogado();      		
      	
      		$parametros['pueostoid']	  = $_POST['pueostoid'];
      		$parametros['pueitem']		  = $_POST['pueitem'];
      		$parametros['pueossoid']	  = $_POST['pueossoid'];
      		$parametros['puetpcoid']	  = $_POST['puetpcoid'];
      		$parametros['puecsioid'] 	  = $_POST['puecsioid'];
      		$parametros['pueocostatus']   = $_POST['pueocostatus'];
      		$parametros['pueegaoid']	  = $_POST['pueegaoid'];
      		$parametros['puestatus'] 	  = $_POST['puestatus'];
      		$parametros['puecliente_frota'] 	= $_POST['puecliente_frota'];
      		$parametros['pueperiodo_atualizacao'] 	= $_POST['pueperiodo_atualizacao'];
      		$parametros['puependencia_financeira'] 	= $_POST['puependencia_financeira'];
      		$parametros['pueperiodo_lavacar'] 	= $_POST['pueperiodo_lavacar'];
      		$parametros['pueled_bloqueio'] 	= $_POST['pueled_bloqueio'];
      		$parametros['pueperiodo_manutencao'] 	= $_POST['pueperiodo_manutencao'];
      		
      		$this->_dao->insertEstatistica($usuario->cd_usuario, $parametros);
      		$this->flashMessage('Operação realizada com sucesso!');
      	} catch (Exception $ex) {
      		
      		$mensagemInformativa = "";
      	}
      	 
      	$this->estatistica();
     }

 
    public function estatisticaDefault()
    {
    	$this->_dao->insertDefaultEst();
    	$this->flashMessage('Operação realizada com sucesso!');
    	$this->estatistica();
    }
    
    // Hack para IEca
    public function PredefinidosEst()
    {
    	$this->estatisticaDefault();
    }
    
 
    
    
    /**
     * Actions - Pânico
     */
    public function panico()
    {    
        // Busca dados do formulário
        $tiposPanico         = $this->_dao->findTiposPanico();
        $tiposContrato       = $this->_dao->findTiposContrato();
        $statusContrato      = $this->_dao->findStatusContratos();
        $tiposOrdemServico   = $this->_dao->findTiposOrdemServico();
        $statusOrdemServico  = $this->_dao->findStatusOrdemServico();
        $tiposDefeitoAlegado = $this->_dao->findDefeitoAlegado();
        $ocorrenciasStatus   = $this->_dao->findOcorrenciasStatus();
        
        // Busca o último registro salvo
        $form = $this->_dao->findLast();
        
        $form['puppantoid']   = $this->buildArray($form['puppantoid']);
        $form['puptpcoid']    = $this->buildArray($form['puptpcoid']);
        $form['pupcsioid']    = $this->buildArray($form['pupcsioid']);
        $form['pupostoid']    = $this->buildArray($form['pupostoid']);
        $form['pupitem']      = $this->buildArray($form['pupitem']);
        $form['pupossoid']    = $this->buildArray($form['pupossoid']);
        $form['pupotdoid']    = $this->buildArray($form['pupotdoid']);
        $form['pupocostatus'] = $this->buildArray($form['pupocostatus']);
        $form['pupporta_panico'] = $this->buildArray($form['pupporta_panico']);        
    
       ;
        

       $this->setAba('panico');
       require_once $this->_viewPath . 'panico.php';
    }
    
    public function assistencia()
    {
    	try {
    	$resOsTipo = $this->_dao->findOsTipo();//Busca tipos de OS
    	$resOsStatus = $this->_dao->findOsStatus();//Busca status de OS
    	$resTipoContrato = $this->_dao->findTipoContrato();//Busca status de OS
    	$resStatusContrato = $this->_dao->findContratoStatus();//Busca status de OS
    	$resAcaoOS = $this->_dao->findAcaoOS();
    	$tiposDefeitoAlegado = $this->_dao->findDefeitoAlegado();
    	} catch (Exception $e) {
    		$mensagemInformativa = "Erro ao carregar dados";
    	}
    	
    	$usuario = Sistema::getUsuarioLogado();
    	//$usuario->cd_usuario;
    	// Busca o último registro salvo
    	$form = $this->_dao->findLastAssistencia($usuario->cd_usuario);

    	$form['tipoOS']     	= $this->buildArray($form['puaostoid']);
    	$form['itemOS']    		= $this->buildArray($form['puaitem']);
    	$form['statusOS']    	= $this->buildArray($form['puaossoid']);
    	$form['tipoContrato']   = $this->buildArray($form['puatpcoid']);
    	$form['statusContrato'] = $this->buildArray($form['puacsioid']);
    	$form['tipoDefeitoOS']  = $this->buildArray($form['puaotdoid']);    
    	$form['acaoOS']    		= $this->buildArray($form['puaacao']);
    	

    	$this->setAba('assistencia');
    	require_once $this->_viewPath . 'assistencia.php';
    }
    
    public function index()
    {
    	require_once $this->_viewPath . 'index.php';
    }
    
    public function assistenciaBuscarDefeitos() 
    {
    	try {
	    	$resDefeitoAlegado = $this->_dao->findDefeitoAlegado();//Busca defeito alegado
	    	$defeitos = array();
	    	foreach($resDefeitoAlegado as $defeito) {
				$var = utf8_encode($defeito['otddescricao']);
				
				$defeitos[] = array(
						'id' => $defeito['otdoid'],
						'descricao' => $var
						);
				
	    	}
	    	echo json_encode($defeitos);
    	} catch (Exception $e) {
    		$mensagemInformativa = "Erro ao carregar defeitos";
    	}
    }
    
    public function assistenciaSalvar()
    {
    	try {
	    	$usuario = Sistema::getUsuarioLogado();
	    	//$usuario->cd_usuario;

	    	$parametros['tipoOS'] = $_POST['os_tipo_id'];
	    	$parametros['itemOS'] = $_POST['itens_os'];
	    	$parametros['acaoOS'] = $_POST['os_acao_id'];
	    	$parametros['statusOS'] = $_POST['os_status_id'];
	    	$parametros['tipoContrato'] = $_POST['tipo_contrato_id'];
	    	$parametros['statusContrato'] = $_POST['status_contrato_id'];
	    	$parametros['tipoDefeitoOS'] = $_POST['defeito_id'];
	    	$parametros['agendaOS'] = $_POST['os_agendada_data_posterior'];
	    	
	    	$this->_dao->assistenciaSalvar($usuario->cd_usuario, $parametros);
	    	$this->flashMessage('Operação realizada com sucesso!');
	    
	    	} catch (Exception $ex) {
	    	
    	} catch (Exception $ex) {
    		$mensagemInformativa = "";
    	}
    	
    	$this->assistencia();
    }

    public function predefinidosSalvar()
    {
    	try {
    		$usuario = Sistema::getUsuarioLogado();
    		//$usuario->cd_usuario;
    		$this->_dao->predefinidosSalvar($usuario->cd_usuario);
    		$this->flashMessage('Operação realizada com sucesso!');
    		
    	} catch (Exception $ex) {
    		$mensagemInformativa = "";
    	}

    	$this->assistencia();
    }
       
    
    /**
     * Transforma um array do Postgres em um array do PHP
     */
    public function buildArray($string)
    {
        if (strlen($string))
        {
            return explode(',', preg_replace('/\{|\}/', '', $string));
        }
        
        return array();
    }
    
    public function panicoSalvar()
    {
        $r = $this->_dao->insert($_POST);
        $this->flashMessage('Operação realizada com sucesso!');
        //$this->_redirect('man_parametrizacao_ura.php&acao=panico');
        $this->panico();
    }
    
    public function panicoDefault()
    {
        $this->_dao->insertDefault();
        $this->flashMessage('Operação realizada com sucesso!');
        //$this->_redirect('man_parametrizacao_ura.php&acao=panico');
        $this->panico();
    }
    
    
    public function Salvar()
    {
    	$this->panicoSalvar();
    }
    
    
    
    // Hack para IEca
    public function Predefinidos()
    {
        $this->panicoDefault();
    }

	/**
	 * Index da Aba de parâmetros do CRON
	 */
	public function cron() {
		$statusCron = $this->_dao->buscarInformacoesCron();

		foreach($statusCron as $cron) {

			switch((int)$cron['cuaoid']) {
				case 1 :
					$form['panico']['envio']     = $cron['cuacronenvio'];
					$form['panico']['insucesso'] = $cron['cuacroninsucesso'];
					$form['panico']['adicional'] = $cron['cuacronadicional'];
					$form['panico']['reenvio']   = $cron['cuacronreenvio'];
					break;
				case 2 :
					$form['assistencia']['envio']     = $cron['cuacronenvio'];
					$form['assistencia']['insucesso'] = $cron['cuacroninsucesso'];
					$form['assistencia']['adicional'] = $cron['cuacronadicional'];
					$form['assistencia']['reenvio']   = $cron['cuacronreenvio'];
					break;
				case 3 :
					$form['estatistica']['envio']     = $cron['cuacronenvio'];
					$form['estatistica']['insucesso'] = $cron['cuacroninsucesso'];
					$form['estatistica']['adicional'] = $cron['cuacronadicional'];
					$form['estatistica']['reenvio']   = $cron['cuacronreenvio'];
					break;
			}

		}
		
		$this->setAba('cron');
		require_once $this->_viewPath . 'cron.php';
	}

	/**
	 * Inicia processo de atualização das campanhas no banco.
	 */
	public function cronSalvar() {
		$retorno = false;
		
		$assistencia = array();
		$estatistica = array();
		$panico      = array();
		
		try {
			$this->_dao->abrirTransacao();
			
			$assistencia['envio']     = isset($_POST['cron_assistencia_envio'])     ? $_POST['cron_assistencia_envio']     : 'A';
			$assistencia['insucesso'] = isset($_POST['cron_assistencia_insucesso']) ? $_POST['cron_assistencia_insucesso'] : 'A';
			$assistencia['adicional'] = isset($_POST['cron_assistencia_adicional']) ? $_POST['cron_assistencia_adicional'] : 'A';
			$assistencia['reenvio']   = isset($_POST['cron_assistencia_reenvio'])   ? $_POST['cron_assistencia_reenvio']   : 'A';
			
			$estatistica['envio']     = isset($_POST['cron_estatistica_envio'])     ? $_POST['cron_estatistica_envio']     : 'A';
			$estatistica['insucesso'] = isset($_POST['cron_estatistica_insucesso']) ? $_POST['cron_estatistica_insucesso'] : 'A';
			$estatistica['adicional'] = isset($_POST['cron_estatistica_adicional']) ? $_POST['cron_estatistica_adicional'] : 'A';
			$estatistica['reenvio']   = isset($_POST['cron_estatistica_reenvio'])   ? $_POST['cron_estatistica_reenvio']   : 'A';
			
			$panico['envio']     = isset($_POST['cron_panico_envio'])     ? $_POST['cron_panico_envio']     : 'A';
			$panico['insucesso'] = isset($_POST['cron_panico_insucesso']) ? $_POST['cron_panico_insucesso'] : 'A';
			$panico['adicional'] = isset($_POST['cron_panico_adicional']) ? $_POST['cron_panico_adicional'] : 'A';
			$panico['reenvio']   = isset($_POST['cron_panico_reenvio'])   ? $_POST['cron_panico_reenvio']   : 'A';
			
			$retorno = $this->_dao->atualizarInformacoesCron($assistencia, 'assistencia');
			
			if($retorno) {
				$retorno = $this->_dao->atualizarInformacoesCron($estatistica, 'estatistica');
				
				if($retorno) {
					$retorno = $this->_dao->atualizarInformacoesCron($panico, 'panico');
				}
			}
			
			if($retorno) {
				$this->flashMessage('Operação realizada com sucesso!');
			}
			
			$this->_dao->fecharTransacao();
		} catch(Exception $erro) {
			$mensagemInformativa = "";
			
			$this->_dao->abortarTransacao();
    	}
    	
    	$this->cron();
	}

}