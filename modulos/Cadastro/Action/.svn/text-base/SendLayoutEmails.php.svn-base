<?php
@header('Content-Type: text/html; charset=ISO-8859-1');

require_once _MODULEDIR_ . 'Cadastro/DAO/CadLayoutEmailsDAO.php';
require_once _MODULEDIR_ . 'Cadastro/DAO/SendLayoutEmailsDAO.php';
require_once _MODULEDIR_ . 'Principal/Action/ServicoEnvioEmail.php';

class SendLayoutEmails
{
	protected $_dao;
	protected $_daoCon;
	protected $_viewPath;
	protected $params;

	protected $origemFunctions = array( "RO" => "Envio de emails em lote - Relat%rio de Ocorr%ncias" );

	public function __construct()
	{
		$this->_dao = new CadLayoutEmailsDAO();
		$this->_daoCon = new SendLayoutEmailsDAO();
		$this->_viewPath = _MODULEDIR_ . 'Cadastro/View/send_layout_emails/';
		$this->_Email	 = new ServicoEnvioEmail();
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

	protected function _getFuncionalidade(){
		$k = $this->_getGetParam('origem');
		if($this->origemFunctions[$k]){
			$_SESSION['origemF'] = $k;
		} else if($_SESSION['origemF']) {
			$k = $_SESSION['origemF'];
		}
		return ( isset($this->origemFunctions[$k]) ? $this->origemFunctions[$k] : false );
	}

	protected function _setIdsOcorrencias($oco){
		$_SESSION['oco'] = explode(",", $_GET['oco']);
	}

	protected function _getIdsOcorrencias(){
		return implode(",", $_SESSION['oco']);
	}

	protected function _getChaveEdicao(){
    	return md5($this->_getIdsOcorrencias().$this->_getIdLayout().$this->_getFuncionalidade());
    }

    protected function _setIdLayout($seeoid){
    	$_SESSION['seeoid']=$seeoid;
    }

    protected function _getIdLayout(){
    	if($this->_getGetParam('seeoid')||$this->_getPostParam('seeoid'))
    		$this->_setIdLayout(( $this->_getGetParam('seeoid') ? $this->_getGetParam('seeoid') : $this->_getPostParam('seeoid')));

    	return $_SESSION['seeoid'];
    }

    protected function _setCorpoEmail($htmlCorpo){
    	$k=$this->_getChaveEdicao();
    	$_SESSION['seecorpo'][$k]=$htmlCorpo;
    }

    protected function _setCabecalhoEmail($cabecalho){
    	$k=$this->_getChaveEdicao();
    	$_SESSION['seecabecalho'][$k]=$cabecalho;
    }

    protected function _getCorpoEmail(){
    	$k=$this->_getChaveEdicao();
    	return $_SESSION['seecorpo'][$k];
    }

    protected function _getCabecalhoEmail(){
    	$k=$this->_getChaveEdicao();
    	return $_SESSION['seecabecalho'][$k];
    }

	/**
	 * Recupera um parâmetro recebido em determinado tipo de requisição
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

	/**
	 * Pesquisa de layouts de email
	 */
	public function index()
	{
		// Popula campos do formulário
		$funcionalidades = $this->_dao->getListaFuncionalidades();
		$funcionalidade = $this->_getGetParam('seeseefoid');
		$titulo  = $this->_getGetParam('seecabecalho');
		$usuario = $this->_getGetParam('usuario');

		$resultado = null;
		if ($this->_hasParam('bt_pesquisar'))
		{
			try
			{
				$resultado = $this->_dao->getLayoutEmails(array(
					'seeseefoid'	=> $funcionalidade,
					'seecabecalho'	=> $titulo,
					'usuario' 		=> $usuario
				));

				$listTitulos = $this->_dao->getLayoutEmails(array(
						'seeseefoid'	=> $funcionalidade,
						'usuario' 		=> $usuario
				));
			}
			catch (Exception $e)
			{
				$this->flashMessage('Houve um erro ao realizar a pesquisa.');
			}
		}

		require_once $this->_viewPath . 'index.php';
	}

	/**
	 * Visualização de layout STI - 81456
	 */
	public function view()
	{
		try
		{
			$TipoId = array();
			$ocorrencia = explode (',', $_GET['oco']);

			$_SESSION['ocorrencias'] = $ocorrencia;

			// $filtro['seeseetoid'] = $this->_dao->getTipoIdseetoid('Envio Email Relatorio');
			$filtro['seeseetoid'] = $this->_getIdLayout();
			$filtro['seeseefoid'] = $this->_dao->getTipoIdseefoid('Relatório novo IRV');

			/*if($filtro['seeseefoid'] == null || $filtro['seeseetoid'] == null){
				die('Título e Funcionalidade não cadastrados.');
			}*/

			$seeoidView = $this->_dao->getListaTituloLayout($filtro['seeseefoid']);

			for($for=0; $for<count($ocorrencia); $for++){

				$TipoId = $this->_dao->getTipoIdByOcorrencia($ocorrencia[$for]);

				$TipoId['usuario']  = $this->_getGetParam('usuario');
				$TipoId['servidor'] = $this->_getGetParam('srvoid');
				$TipoId['seeseetoid'] = $filtro['seeseetoid'];
				$TipoId['seeseefoid'] = $filtro['seeseefoid'];

				$getLayoutEmail = $this->buscaLayoutEmail($TipoId);
				$seeoidKey[] = $getLayoutEmail['seeoid'];
			}

			$_SESSION['arrayLayout'] = $seeoidKey;
		}
		catch (Exception $e)
		{
			$this->flashMessage('Houve um erro ao realizar a pesquisa.');
		}

		require_once $this->_viewPath . '_formView.php';
	}

	/**
	 * Busca layout para o envio de email
	 * @param type $TipoId - Array
	 * @return	Array
	 */
	function buscaLayoutEmail($TipoId)
	{
		$funcionalidade	 = $TipoId['seeseefoid'];
		$titulo			 = $TipoId['seeseetoid'];
		$usuario		 = $TipoId['usuario'];
		$tipoproposta	 = $TipoId['supertipo'];
		$subtipoproposta = $TipoId['prptppoid'];
		$tipocontrato	 = $TipoId['prptpcoid'];
		$servidor		 = $TipoId['servidor'];
        $tipoEnvio       = $TipoId['seetipo'];

		$seeoidPadrao = $this->_dao->verificaExistePadrao($TipoId);

		$getEmail = array(
			'seeseefoid'	    => $funcionalidade,
			'seeseetoid'	    => $titulo,
			'usuario' 		    => $usuario,
			'tipoproposta'  	=> $tipoproposta,
			'subtipoproposta' 	=> $subtipoproposta,
			'tipocontrato' 		=> $tipocontrato,
			'servidor' 			=> $servidor,
            'seetipo'           => $tipoEnvio);

		$seeoidPadrao   = $this->_dao->verificaExistePadrao($TipoId);
		$getLayoutEmail = $this->_dao->getLayoutEmails($getEmail);


		if(!$getLayoutEmail){
			$getEmail['subtipoproposta'] = false;
			$getLayoutEmail = $this->_dao->getLayoutEmails($getEmail);
		}

		if(!$getLayoutEmail){
			$getEmail['tipoproposta'] = 'isnull';
			$getEmail['tipocontrato'] = $tipocontrato;
			$getEmail['subtipoproposta'] = false;
			$getLayoutEmail = $this->_dao->getLayoutEmails($getEmail);
		}

		if(!$getLayoutEmail){
			$getEmail['tipocontrato'] = 'isnull';
			$getEmail['tipoproposta'] = $tipoproposta;
			$getEmail['subtipoproposta'] = $subtipoproposta;
			$getLayoutEmail = $this->_dao->getLayoutEmails($getEmail);
		}

		if(!$getLayoutEmail){
			$getEmail['tipocontrato'] = 'isnull';
			$getEmail['tipoproposta'] = $tipoproposta;
			$getEmail['subtipoproposta'] = false;
			$getLayoutEmail = $this->_dao->getLayoutEmails($getEmail);
		}

		if(!$getLayoutEmail){

			if($seeoidPadrao){
				$getEmail['padrao'] = 'true';
				$getEmail['tipoproposta'] = false;
				$getEmail['tipocontrato'] = false;
				$getEmail['subtipoproposta'] = false;
                $getEmail['usuario'] = '';

				$getLayoutEmail = $this->_dao->getLayoutEmails($getEmail);
			}
		}

		$retorno['seeoid'] = $getLayoutEmail[0]['seeoid'];
		$retorno['seetdescricao'] = $getLayoutEmail[0]['seetdescricao'];
        $retorno['seetipo'] = $getLayoutEmail[0]['seetipo'];

		return $retorno;
	}


	protected function _replaceTags($texto, $registro){
		//prepara visualização
		$time_recuperacao = strtotime( $registro['dtrecuperacao']." ".$registro['hrrecuperacao']);
		$texto = str_replace("[NOMECLIENTE]", $registro['nomecliente'], $texto);
		$texto = str_replace("[MARCA]", $registro['marca'], $texto);
		$texto = str_replace("[MODELO]", $registro['modelo'], $texto);
		$texto = str_replace("[PLACA]", $registro['placa'], $texto);
		$texto = str_replace("[CHASSI]", $registro['chassi'], $texto);
		$texto = str_replace("[DTRECUPERACAO]", date("d/m/Y",$time_recuperacao), $texto);
		$texto = str_replace("[HRRECUPERACAO]", date("H:i",$time_recuperacao), $texto);
		$texto = str_replace("[LCRECUPERACAO]", $registro['lcrecuperacao'], $texto);
		$texto = str_replace("[VLRECUPERACAO]", "R$".number_format($registro['vlrecuperacao'],2,",","."), $texto);

        /*
         * Mudança dos nomes de algumas TAGs. Mantido as já existentes para não haver impacto
         * em outras telas que possam usar este método. Abaixo aplicado as novas TAGs.
         */
        $texto = str_replace("[VALOR_RECUPERADO]", "R$".number_format($registro['vlrecuperacao'],2,",","."), $texto);
        $texto = str_replace("[DIA]", date("d/m/Y",$time_recuperacao), $texto);
		$texto = str_replace("[HORA]", date("H:i",$time_recuperacao), $texto);

		return $texto;
	}

	/**
	 * Edição de layout Veículo Recuperado.
	 */
	public function emailOcorrencia(){

		$_GET['oco'] = substr($_GET['oco'], 1);

		if(isset($_GET['oco'])) {
			$this->_setIdsOcorrencias($_GET['oco']);

			try{
				if($this->_getGetParam('reset')=='t'){
					$this->_setIdLayout(null);
					$fields = $this->_dao->getLayoutEmailPadrao($this->_getFuncionalidade());
					//if(!$fields) $this->flashMessage('Layout padrão não cadastrado.');
					$this->_setIdLayout($fields['seeoid']);
				}
			} catch (Exception $e) {
				$this->flashMessage('Houve um erro ao pesquisar dados do layout.');
				require_once $this->_viewPath . 'editar.php';
				exit;
			}

			$this->_setCabecalhoEmail(null);
			$this->_setCorpoEmail(null);
		}

		$this->view();
	}

	/**
	 * Exibe modelo de e-mail em tela.
	 */
	public function emailOcorrenciaHTML(){

		$ocorrencia = $_SESSION['ocorrencias'];

		$blank = ($this->_getGetParam('blank') ? $this->_getGetParam('blank') :
						($this->_getPostParam('blank') ? $this->_getPostParam('blank') : ''));

		$reset = ($this->_getGetParam('reset') ? $this->_getGetParam('reset') :
						($this->_getPostParam('reset') ? $this->_getPostParam('reset') : ''));

		for($for=0; $for<count($ocorrencia); $for++){

			$getLayout = $this->_dao->getTipoIdByOcorrencia($ocorrencia[$for]);

			$getLayout['usuario']  = $this->_getGetParam('usuario');
			$getLayout['servidor'] = $this->_getGetParam('srvoid');

			$getLayout['seeseefoid'] = $this->_dao->getTipoIdseefoid('Relatório novo IRV');
			$getLayout['seeseetoid'] = $this->_getIdLayout();

			$getLayoutEmail = $this->buscaLayoutEmail($getLayout);
			$seeoidKey[] = $getLayoutEmail['seeoid'];

			$dadosEmail = $this->_daoCon->getOcorrencias($ocorrencia[$for]);

			$registro['nomecliente'] = $dadosEmail[0]['nomecliente'];
			$registro['marca']  = $dadosEmail[0]['marca'];
			$registro['modelo'] = $dadosEmail[0]['modelo'];
			$registro['placa']  = $dadosEmail[0]['placa'];
			$registro['chassi'] = $dadosEmail[0]['chassi'];
			$registro['lcrecuperacao'] = $dadosEmail[0]['lcrecuperacao'];
			$registro['vlrecuperacao'] = $dadosEmail[0]['vlrecuperacao'];

			$IdLayout = $seeoidKey[0];
			$fields = $this->_dao->getLayoutEmailPorId($IdLayout);

			$fields['seecorpo'] = $this->_replaceTags($fields['seecorpo'], $registro);

			$fields['seedescricao'] = utf8_encode($fields['seedescricao']);
			$fields['seecabecalho'] = utf8_encode($fields['seecabecalho']);
			$fields['seeobjetivo']  = utf8_encode($fields['seeobjetivo']);
			$fields['seecorpo']     = utf8_encode($fields['seecorpo']);
		}

		$_SESSION['arrayLayout'] = $seeoidKey;

		if($blank!=''){
			$this->_setCorpoEmail("");
			$this->_setIdLayout(null);
			$this->_setCabecalhoEmail(null);
		}

		if($IdLayout == null){
			$msg = array("status"=>"erro", "message"=> 'ERRO', "redirect"=>"");
			echo json_encode($msg);
			exit;
		}

		if($this->_isAjax()){
			$msg = array("status"=>"success", "message"=> $fields, "redirect"=>"");
			echo json_encode($msg);
			return;
		}

		require_once $this->_viewPath.'_html.php';
	}


	/**
	 * Exibe modelo de e-mail em tela.
	 */
	public function enviaEmailOcorrencia(){

		$erro	 = array();
		$sucesso = array();
		$msg 	 = array("status"=>"success", "message"=>"");

		$getIdsOcorrencias = explode(',', $this->_getIdsOcorrencias());

		$registros = $this->_daoCon->getOcorrencias($this->_getIdsOcorrencias());

		for($for=0; $for<count($registros);$for++){

			$fields = $this->_dao->getLayoutEmailPorId($_SESSION['arrayLayout'][$for]);

			$dadosEmail = $this->_daoCon->getOcorrencias($getIdsOcorrencias[$for]);

			$registro['nomecliente'] = $dadosEmail[0]['nomecliente'];
			$registro['marca']  = $dadosEmail[0]['marca'];
			$registro['modelo'] = $dadosEmail[0]['modelo'];
			$registro['placa']  = $dadosEmail[0]['placa'];
			$registro['chassi'] = $dadosEmail[0]['chassi'];
			$registro['lcrecuperacao'] = $dadosEmail[0]['lcrecuperacao'];
			$registro['vlrecuperacao'] = $dadosEmail[0]['vlrecuperacao'];

			$fields['seecorpo'] = $this->_replaceTags($fields['seecorpo'], $registro);

			$email = ($registros[0]['cliemail'] == '') ? $registros[0]['cliemail_nfe'] : $registros[0]['cliemail'];
			$email = strtolower($email);

  			if ($_SESSION['servidor_teste'] == 1){	$email = _EMAIL_TESTE_;  }

			if($email){

				$corpo_envio  = $fields['seecorpo'];
				$destinatario = $email;
				$copia_envio  = $registros[0]['cliemail_nfe'];
				$server_envio = $fields['seesrvoid'];
				$remetente = ($fields['seeremetente'] == '') ? null : $fields['seeremetente'];

				if(trim($copia_envio) == trim($destinatario)){
					$copia_envio = false;
				}

				$envio = $this->_Email->enviarEmail($destinatario,
													$fields['seecabecalho'],
													$corpo_envio,
													null,
													$copia_envio,
													null,
													$server_envio,
													'teste_desenv@sascar.com.br',
													null,
													null,
													null,
													null,
													$remetente);

				$envio = ($envio['erro']) ? false : true;


				$this->_daoCon->registrarLogOcorrencia($registros[0]['ocooid'], $envio);

				if ($envio) {
					$sucesso[] = $registros[0]['nomecliente'];
				}
				else {
					$erro[] = $registros[0]['nomecliente'];
				}
			}
			else {
				$this->_daoCon->registrarLogOcorrencia($registros[0]['ocooid'], false);
				$erro[] = $registros[0]['nomecliente'];
			}
		}

		if(count($sucesso)){
			$msg['message'].="Email enviado com sucesso para o(s) cliente(s): ".implode(", ", $sucesso).".";
		}

		if(count($erro)){
			$msg['message'] .= (count($sucesso) ? "<br />&nbsp;" : "" ) . "Houve um erro no envio do email para o(s) cliente(s): ".implode(", ", $erro).".";
		}

		echo json_encode($msg);
	}

	
	/**
	 * Busca o codigo do titulo e da funcionalidade de acordo com o nome do titulo passado
	 */
	
	public function getTituloFuncionalidade($titulo){
			
		return $this->_dao->getTituloFuncionalidade($titulo);
	}
	
	/**
	 * Busca o layout de acordo com o ID
	 */
	public function getLayoutEmailPorId($codigoLayout){
			
		return $this->_dao->getLayoutEmailPorId($codigoLayout);
	}
}