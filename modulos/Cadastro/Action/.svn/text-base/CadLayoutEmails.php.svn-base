<?php

require_once _MODULEDIR_ . 'Cadastro/DAO/CadLayoutEmailsDAO.php';

class CadLayoutEmails
{
	protected $_dao;
	protected $_viewPath;

	public function __construct()
	{
		$this->_dao = new CadLayoutEmailsDAO();
		$this->_viewPath = _MODULEDIR_ . 'Cadastro/View/cad_layout_emails/';
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

	/**
	 * Pesquisa de layouts de email
	 */
	public function index()
	{
		// Popula campos do formulário
		$funcionalidades = $this->_dao->getListaFuncionalidades();
		$tipoProposta    = $this->_dao->getListaTipoProposta();
		$tipoContrato    = $this->_dao->getListaTipoContrato();
		$servidores      = $this->_dao->getListaServidores();

		$funcionalidade    = $this->_getGetParam('seeseefoid');
		$titulo            = $this->_getGetParam('seeseetoid');
		$usuario           = $this->_getGetParam('usuario');
		$tipoproposta      = $this->_getGetParam('tppoid');
		$subtipoproposta   = $this->_getGetParam('lconftppoid_sub');
		$tipocontrato      = $this->_getGetParam('tpcoid');
		$servidor          = $this->_getGetParam('srvoid');
		$seetipo	       = $this->_getGetParam('seetipo');

		$resultado = null;
		if ($this->_hasParam('bt_pesquisar'))
		{
			try
			{
				$resultado = $this->_dao->getLayoutEmails(array(
					'seeseefoid'	    => $funcionalidade,
					'seeseetoid'	    => $titulo,
					'usuario' 		    => $usuario,
					'tipoproposta'  	=> $tipoproposta,
					'subtipoproposta' 	=> $subtipoproposta,
					'tipocontrato' 		=> $tipocontrato,
					'servidor' 			=> $servidor,
					'seetipo'		    => $seetipo
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
	 * Criação de um novo layout
	 */
	public function novo()
	{
		// Popula campos do formulário
		$funcionalidades = $this->_dao->getListaFuncionalidades();
		$tipoProposta    = $this->_dao->getListaTipoProposta();
		$tipoContrato    = $this->_dao->getListaTipoContrato();
		$servidores      = $this->_dao->getListaServidores();

		$form['seetipo']	     = $this->_getPostParam('seetipo');
		$form['seepadrao']	     = $this->_getPostParam('seepadrao');
		$form['seecabecalho']	 = $this->_getPostParam('seecabecalho');

		$form['seeobjetivo']     = $this->_getPostParam('seeobjetivo');
		$form['seeseetoid']	 	 = $this->_getPostParam('seeseetoid');
		$form['seeseefoid']		 = $this->_getPostParam('seeseefoid');
		$form['seeimagem_anexo'] = $this->_getPostParam('seeimagem_anexo');
        $form['seeimagem_anexo'] = !empty($form['seeimagem_anexo']) ? $form['seeimagem_anexo'] : 'f';

		$form['seecorpo']		 = ($form['seetipo'] == 'E') ? $this->_getPostParam('seecorpo') : $this->_getPostParam('seecorpoSms');



		$form['seeusuoid_cadastro'] = $_SESSION['usuario']['oid'];

		$tipocontrato            = $this->_getPostParam('tpcoid');
		$form['seetpcoid'] 		 = ($tipocontrato != '') ? $tipocontrato : 'null' ;

		$form['seesrvoid'] 		 = $this->_getPostParam('srvoid');

		$form['seesrvoid']	 = ($this->_getPostParam('srvoid')) ? $this->_getPostParam('srvoid') : 'null' ;

		$tipoproposta 			 = $this->_getPostParam('tppoid');
		$form['seetppoid']    	 = ($tipoproposta) ? $tipoproposta : 'null' ;

		$subtipoproposta 		 = $this->_getPostParam('lconftppoid_sub');
		$form['seetlconftppoid_sub']  = ($subtipoproposta) ? $subtipoproposta : 'null' ;

		// Se foi informado o remetente
		$form['seeremetente']	 = ($this->_getPostParam('seeremetente')) ? $this->_getPostParam('seeremetente') : '' ;
		$form['seeremetente'] = $this->_getPostParam('seeremetente');
		if ($this->_isPost())
		{
			try
			{
				// Upload da imagem
				$form['seeimagem'] = $this->_uploadImagem();

				$this->_dao->insereLayoutEmailPorId($form);

				$this->flashMessage('Registro cadastrado com êxito.');
				return $this->_redirect('cad_layout_emails.php');
			}
			catch (Exception $e)
			{
				if (preg_match('/imagem/', $e->getMessage()))
				{
					$this->flashMessage($e->getMessage());
				}
				elseif ($e->getMessage())
				{
					$this->flashMessage($e->getMessage());
				}
				else
				{
					$this->flashMessage('Houve um erro ao cadastrar o registro.');
				}
			}
		}

		require_once $this->_viewPath . 'novo.php';
	}

	/**
	 * Edição de layout
	 */
	public function editar()
	{
        // Popula campos do formulário
		$funcionalidades = $this->_dao->getListaFuncionalidades();
		$tipoProposta    = $this->_dao->getListaTipoProposta();
		$tipoContrato    = $this->_dao->getListaTipoContrato();
		$servidores      = $this->_dao->getListaServidores();

		$form['seeoid'] = $this->_getRequestParam('seeoid');

        try
        {
            $form = $this->_dao->getLayoutEmailPorId($form['seeoid']);
        }
        catch (Exception $e)
        {
            $this->flashMessage('Houve um erro ao buscar dados do layout.');
            return $this->_redirect('cad_layout_emails.php');

        }

		if ($this->_isPost())
		{
			$form['seetipo']	     = $this->_getPostParam('seetipo');
			$form['seepadrao']	     = $this->_getPostParam('seepadrao');
			$form['seecabecalho']	 = $this->_getPostParam('seecabecalho');
			$form['seeobjetivo']     = $this->_getPostParam('seeobjetivo');
			$form['seeseetoid']	 	 = $this->_getPostParam('seeseetoid');
			$form['seeseefoid']		 = $this->_getPostParam('seeseefoid');
			$form['seeimagem_anexo'] = $this->_getPostParam('seeimagem_anexo');
            $form['seeimagem_anexo'] = !empty($form['seeimagem_anexo']) ? $form['seeimagem_anexo'] : 'f';
			$form['seecorpo']		 = ($form['seetipo'] == 'E') ? $this->_getPostParam('seecorpo') : $this->_getPostParam('seecorpoSms');

			$tipocontrato            = $this->_getPostParam('tpcoid');
			$form['seetpcoid'] 		 = ($tipocontrato != '') ? $tipocontrato : 'null' ;

			$form['seesrvoid']		 = $this->_getPostParam('srvoid');
			$form['seesrvoid']	 = ($this->_getPostParam('srvoid')) ? $this->_getPostParam('srvoid') : 'null' ;

			$tipoproposta 			 = $this->_getPostParam('tppoid');
			$form['seetppoid']    	 = ($tipoproposta) ? $tipoproposta : 'null' ;

			$subtipoproposta 		 = $this->_getPostParam('lconftppoid_sub');
			$form['seetlconftppoid_sub']  = ($subtipoproposta) ? $subtipoproposta : 'null' ;

			// Se foi informado o remetente
			$form['seeremetente']	 = ($this->_getPostParam('seeremetente')) ? $this->_getPostParam('seeremetente') : '' ;
			$form['seeremetente'] = $this->_getPostParam('seeremetente');

			try
			{
				// Upload da imagem
				$form['seeimagem'] = $this->_uploadImagem($form['seeimagem']);

				$this->_dao->atualizaLayoutEmailPorId($form);

				$this->flashMessage('Registro atualizado com êxito.');
				return $this->_redirect('cad_layout_emails.php');
			}
			catch (Exception $e)
			{
				if (preg_match('/imagem/', $e->getMessage()))
				{
					$this->flashMessage($e->getMessage());
				}
				elseif ($e->getMessage())
				{
					$this->flashMessage($e->getMessage());
				}
				else
				{
					$this->flashMessage('Houve um erro ao atualizar o registro.');
				}
			}
		}

		require_once $this->_viewPath . 'editar.php';
	}

    /**
     * Exclui uma imagem via AJAX
     */
    public function excluirImagem()
    {
        $seeoid = (int) $_POST['seeoid'];

        if (!$seeoid)
        {
            echo 'erro!';
        }

        $layout    = $this->_dao->getLayoutEmailPorId($seeoid);
        $imagePath = _SITEDIR_ . 'images/layout_email/' . $layout['seeimagem'];

        if (!unlink($imagePath))
        {
            echo 'erro!';
        }

        $layout['seeimagem'] = '';
        $this->_dao->atualizaLayoutEmailPorId($layout);
    }

    /**
     * Renderiza o layout para visualização
     */
    public function visualizar()
    {
        $seeoid = (int) $_GET['seeoid'];

        $layout = $this->_dao->getLayoutEmailPorId($seeoid);

        $imageSrc = '<img src="images/layout_email/' . $layout['seeimagem'] . '" />';
        $corpo = str_replace('[IMAGEM]', $imageSrc, $layout['seecorpo']);

        require_once $this->_viewPath . 'visualizar.php';
    }

	/**
	 * Faz o upload de uma imagem
	 * @param	string	$nome	Nome da imagem
	 * @return	string  Nome da imagem
	 */
	protected function _uploadImagem($filename = null)
	{

        if (isset($_FILES['seeimagem']) && strlen($_FILES['seeimagem']['tmp_name']))
        {
            $uploadPath = _SITEDIR_ . 'images/layout_email/';
            chmod($uploadPath, 0777);

			// Se não existir nome de arquivo, gera um novo
			if (!$filename)
			{
				$name = sha1(time() . rand(0, 100));
			}

			$arquivo = $_FILES['seeimagem'];

			$matches = array();
			// Se não for dos formatos permitidos, não faz o upload
			if (preg_match('/(gif|jp(e)?g|png|bmp)/i', $arquivo['name'], $matches))
			{
				// Monta string do novo arquivo
				if (!$filename)
				{
					$filename = $name . '.' . strtolower($matches[0]);
				}

				// Move para a pasta
				move_uploaded_file($arquivo['tmp_name'], $uploadPath . $filename);
			}
			else
			{
				if (strlen($arquivo['name']))
				{
					throw new Exception('O arquivo da imagem deve possuir extensão: PNG, BMP, GIF, JPG ou JPEG.');
				}

				return null;
			}
		}

		return $filename;
	}

	/**
	 * Exclui um registro
	 */
	public function excluir()
	{
		try
		{
			$seeoid = $this->_getPostParam('seeoid');

			// Remove arquivo do FS
			/*$layout = $this->_dao->getLayoutEmailPorId($seeoid);
			unlink(_SITEDIR_ . 'images/layout_email/' . $layout['seeimagem']);*/

			// Faz a exclusão lógica
			$this->_dao->deletarItem($seeoid);

			$this->flashMessage('Registro excluído com êxito.');
		}
		catch (Exception $e)
		{
			$this->flashMessage('Houve um erro ao excluir o registro.');
		}
	}

	/**
	 * Lista Titulos e-mail
	 */
	public function listaTitulosEmail()
	{

		$seefoid = (isset($_POST["seefoid"])) ? trim($_POST["seefoid"]): (int) $_GET['seefoid'];

		$tituloLayout = $this->_dao->getListaTituloLayout($seefoid);

		echo json_encode($tituloLayout);
		exit();

	}

	/**
	 * Lista Subtipo Proposta
	 */
	public function listaSubtipoProposta()
	{

		$tppoid = (isset($_POST["tppoid"])) ? trim($_POST["tppoid"]): (int) $_GET['tppoid'];
		$subTipoProposta = $this->_dao->getListaSubtipoProposta($tppoid);

		echo json_encode($subTipoProposta);
		exit();

	}
}