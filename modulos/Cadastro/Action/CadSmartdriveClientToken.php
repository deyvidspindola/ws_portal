<?php
/**
 * Classe CadSmartdriveClientToken.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
  *
 */
class CadSmartdriveClientToken {
    private $dao;
    private $view;
    private $urlPagina;
	private $comp_cliente;
	private $comp_cliente_params = array (
			'id' => 'cliente_id',
			'name' => 'cliente_nome',
			'cpf' => 'cliente_cpf',
			'cnpj' => 'cliente_cnpj',
			'tipo_pessoa' => 'tipo_pessoa',
			'btnFind' => true,
			'btnFindText' => 'Pesquisar',
			'data' => array (
					'table' => 'clientes',
					'where' => 'clidt_exclusao is null',
					'fieldFindByText' => 'clinome',
					'fieldFindById' => 'clioid',
					'fieldFindByCPF' => 'clino_cpf',
					'fieldFindByTipoPessoa' => 'clitipo',
					'fieldFindByCNPJ' => 'clino_cgc',
					'fieldLabel' => 'clinome',
					'fieldReturn' => 'clioid',
					'fieldReturnCPF' => 'clino_cpf',
					'fieldReturnCNPJ' => 'clino_cgc' 
			) 
	);
	private $usuarioLogado;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    public function __construct($dao = null) {
        $this->dao                   = (is_object($dao)) ? $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;
        $this->urlPagina             = 'cad_smartdrive_client_token' . 'php';
		$this->comp_cliente 		 = new ComponenteCliente ( $this->comp_cliente_params );
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
        $this->dt_atual              = date('d/m/Y H:i:s');

    } 

    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();
            //Inicializa os dados
            $this->inicializarParametros();

            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }
        //Incluir a view padrão
        require_once _MODULEDIR_ . "Cadastro/View/cad_smartdrive_client_token/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

	   $retorno = new stdClass();

       if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
                }

            }
        }

        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {
               if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }

        return $retorno;
    }

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

		$this->view->parametros->tokenid = isset($this->view->parametros->tokenid) && trim($this->view->parametros->tokenid) != "" ? trim($this->view->parametros->tokenid) : 0;
		$this->view->parametros->clioid = isset($this->view->parametros->cpx_valor_cliente_nome) && trim($this->view->parametros->cpx_valor_cliente_nome) != "" ? trim($this->view->parametros->cpx_valor_cliente_nome) : 0;
		$this->view->parametros->tipo_pessoa = isset($this->view->parametros->cpx_valor_tipo_pessoa) && !empty($this->view->parametros->cpx_valor_tipo_pessoa) ? trim($this->view->parametros->cpx_valor_tipo_pessoa) : ""; 
		$this->view->parametros->token = isset($this->view->parametros->token) && !empty($this->view->parametros->token) ? trim($this->view->parametros->token) : ""; 
		$this->view->parametros->site_name = isset($this->view->parametros->site_name) && !empty($this->view->parametros->site_name) ? trim($this->view->parametros->site_name) : ""; 
		$this->view->parametros->created_on = isset($this->view->parametros->created_on) && !empty($this->view->parametros->created_on) ? trim($this->view->parametros->created_on) : ""; 
		$this->view->parametros->changed_on = isset($this->view->parametros->changed_on) && !empty($this->view->parametros->changed_on) ? trim($this->view->parametros->changed_on) : ""; 
		$this->view->parametros->deleted_on = isset($this->view->parametros->deleted_on) && !empty($this->view->parametros->deleted_on) ? trim($this->view->parametros->deleted_on) : ""; 
		$this->view->parametros->expires_on= isset($this->view->parametros->expires_on) && !empty($this->view->parametros->expires_on) ? trim($this->view->parametros->expires_on) : ""; 

    }

    private function pesquisar(stdClass $filtros) {
    	//Valida campos de pesquisa 	 
    	//$this->validarCamposPesquisa($filtros);

		//Seta tokenAPI sessão
		$this->initApiSession();

		//Converte objeto stdClass em Array
		$filtros = $this->objectToArray($filtros);
		
		//Atribui uri pesquisa
		$uri = "/v1/smartdrivetoken"; 
		
		//Atribui filtro id cliente
		if (isset($filtros['clioid']) && trim($filtros['clioid']) > 0) {
			$uri = $uri . "/clienteId=" . $filtros['clioid'];
		} 

		//Chama serviço de pesquisa
		$responseMS = $this->executeApi($serviceInstance, 'GET', $uri, $filtros, false);
		
        //Requisição bem sucedida
        if ($responseMS['curl_info']['http_code']==200) {

			//Transforma a string result em objeto
			$resultadoMS = json_decode($responseMS['result']);

			//Atribui array de objetos a listagem
			$resultadoPesquisa = $resultadoMS->lista;

			//Valida qtde de registros
	        if (count($resultadoPesquisa) == 0) {
	            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
	        }

			//Inclui nome cliente 
			foreach ($resultadoPesquisa as $key => $value){
				//Busca nome cliente pelo id
				$resultadoCliente = $this->dao->pesquisarCliente($value->clienteId);
	        	//Atribui ao elemento cliente do objeto
	        	$resultadoPesquisa[$key]->cliente 	= $resultadoCliente->clinome;
	        	//Trata caracteres especiais
	        	$resultadoPesquisa[$key]->siteName 	= utf8_decode($resultadoPesquisa[$key]->siteName);
	        	$resultadoPesquisa[$key]->token 	= utf8_decode($resultadoPesquisa[$key]->token);
			}

	        $this->view->status = TRUE;
			
			//Limpa tokenAPI sessão
			$this->setToken('', 0);

	        return $resultadoPesquisa;

        }
        else{
			//echo $_SESSION['tokenAPI']['id'] . '</br></br></br>';
			//var_dump($responseMS);
	        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
	    
    	//Exibe a tela principal
        $this->index();

    }


    /**
     * Valida os campos de pesquisa.
     *
     * @return
     */
    public function validarCamposPesquisa(stdClass $dados) {
    
    	//Campos para destacar na view em caso de erro
    	$camposDestaques = array();
    
    	//Verifica se houve erro
    	$error 		= false;
    
    	if (!isset($dados->cpx_valor_cliente_nome) || trim($dados->cpx_valor_cliente_nome) == '') {
    		$camposDestaques[] = array('campo' => 'cpx_pesquisa_cliente_nome_label');
    		$camposDestaques[] = array('campo' => 'cpx_valor_cliente_cnpj');
    		$camposDestaques[] = array('campo' => 'cpx_valor_cliente_cpf');
    		$camposDestaques[] = array('campo' => 'cpx_valor_tipo_pessoa');
    		$error = true;
    	}
    
    	if ($error) {
    		$this->view->dados = $camposDestaques;    		
    		throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
    	}
    
    }

    public function cadastrar($parametros = null) {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try{

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }
				//var_dump($this->view->parametros);
				//echo "</br></br></br>";

            //Incializa os parametros
            $this->inicializarParametros();

            //Verificar se foi submetido o formulário e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST)) {

                $registroGravado = $this->salvar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_smartdrive_client_token/cadastrar.php";
        }
    }

    public function editar() {

        try {
            //Parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->tokenid) && intval($parametros->tokenid) > 0) {
                //Realiza o CAST do parametro
                $parametros->tokenid = (int) $parametros->tokenid;

				//Seta tokenAPI sessão
				$this->initApiSession();
				
				//Atribui uri pesquisa
				$uri = "/v1/smartdrivetoken"; 
		
				//Atribui filtro id
				$uri = $uri . "/id=" . $parametros->tokenid;

				//Pesquisa o registro para edição
				$responseMS = $this->executeApi($serviceInstance, 'GET', $uri, $filtros, false);
				
				//Transforma a string result em objeto
				$result = json_decode($responseMS['result']);

				$data = $result->smartDriveToken;

				//Busca dados cliente pelo id
				$resultadoCliente = $this->dao->pesquisarCliente($data->clienteId);

				//Trata a qtde de caracteres qdo CNPJ
				if (isset($resultadoCliente->clitipo) && $resultadoCliente->clitipo == 'J'){
					$clino_cgc  = $resultadoCliente->clino_cgc;
					if (strlen($total) < 14) {
					    $clino_cgc = str_repeat("0", 14-strlen($clino_cgc)).$clino_cgc;
					}
					else {
					    $clino_cgc = $clino_cgc;
					}
					$resultadoCliente->clino_cgc = $clino_cgc;
				}

				//Atribui os resultados aos respectivos campos
				$dados = new stdClass();
				$dados->tokenid 				= $data->id;
				$dados->clioid 					= $data->clienteId;
				$dados->cpx_valor_cliente_nome 	= $data->clienteId;
				$dados->clitipo 				= $resultadoCliente->clitipo;
				$dados->clinome 				= $resultadoCliente->clinome;
				$dados->clino_cpf 				= $resultadoCliente->clino_cpf;
				$dados->clino_cgc 				= $resultadoCliente->clino_cgc;
				$dados->site_name 				= utf8_decode(trim($data->siteName));
				$dados->token 					= utf8_decode(($data->token));  
				$dados->dt_expiracao 			= $data->dataExpiracao;

		        //Requisição mal sucedida
		        if ($responseMS['curl_info']['http_code']!=200) {
					//var_dump($responseMS);
					//echo "</br></br></br>";
		        	throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
		        }

				//Limpa tokenAPI sessão
				$this->setToken('', 0);

                //Chama o metodo para edição passando os dados do registro por parametro.
                $this->cadastrar($dados); 

            } else {
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    private function salvar(stdClass $dados) {
    	//Validação ocorre no js
        //$this->validarCamposCadastro($dados);

        $responseMS = null;
		
		//Seta tokenAPI sessão
		$this->initApiSession();

		//Cria um array com o conteúdo dos campos
		$data = array(
			"siteName" => utf8_encode(trim($dados->site_name)),
			"token" => utf8_encode(trim($dados->token)),
			"dataExpiracao" => $dados->dt_expiracao." 23:59:59",
			"userId" => $this->usuarioLogado,
			"aplicacao" => "Intranet"
		);

		//Atribui uri
		$uri = "/v1/smartdrivetoken";

        if (isset($dados->tokenid) && intval($dados->tokenid) > 0) {
        	//PUT
			$data['id'] = $dados->tokenid;
        	$data['clienteId'] = $dados->clioid;

			//Transforma em um objeto
			$filtros['data'] = json_encode($data);

			//Chama serviço de alteração
			$responseMS = $this->executeApi($serviceInstance, 'PUT', $uri, $filtros, false);

			//Atribui msg sucesso
			$mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

        } 
        else {
        	//POST
        	$data['dataEfetivacao'] = $this->dt_atual;
        	$data['clienteId'] = $dados->clioid;
			
			//Transforma em um objeto
			$filtros['data'] = json_encode($data); 
			
			//Chama serviço de inclusão
			$responseMS = $this->executeApi($serviceInstance, 'POST', $uri, $filtros, false);			
			
			//Atribui msg sucesso
			$mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        
        }

        //Requisição bem sucedida
        if ($responseMS['curl_info']['http_code']==200) {
        	
			//Trata o resultado da requisição
			$result = json_decode($responseMS['result']);
			
        	//Micro serviço não executou função
			if ($result->ok===false && $result->mensagem != null) {
				$this->view->mensagemInfo = null; 
				$this->view->mensagemAlerta = utf8_decode($result->mensagem);
            	require_once _MODULEDIR_ . "Cadastro/View/cad_smartdrive_client_token/cadastrar.php";
            	exit;
			}
			else{
				$this->view->mensagemSucesso = $mensagemSucesso;
			}
        }
        else{
			//var_dump($responseMS);
        	$this->view->mensagemErro = self::MENSAGEM_ERRO_PROCESSAMENTO;
        }
		//Limpa tokenAPI sessão
		$this->setToken('', 0);

        return $responseMS;
    }

    public function validarCamposCadastro(stdClass $dados) {
    
    	//Campos para destacar na view em caso de erro
    	$camposDestaques = array();
    
    	//Verifica se houve erro
    	$error = false;
    
    	// Verifica os campos obrigatórios
    	if (!isset($dados->token) || trim($dados->token) == '') {
    		$camposDestaques[] = array(
    			'campo' => 'token'
    		);
    		$error = true;
    	}
    	if (!isset($dados->dt_expiracao) || trim($dados->dt_expiracao) == '') {
    		$camposDestaques[] = array(
    			'campo' => 'dt_expiracao'
    		);
    		$error = true;
    	}
    	if (!isset($dados->site_name) || trim($dados->site_name) == '') {
    		$camposDestaques[] = array(
    			'campo' => 'site_name'
    		);
    		$error = true;
    	}
    	if (!isset($dados->cpx_valor_cliente_nome) || trim($dados->cpx_valor_cliente_nome) == '') {
    		$camposDestaques[] = array('campo' => 'cpx_pesquisa_cliente_nome_label');
    		$camposDestaques[] = array('campo' => 'cpx_valor_cliente_cnpj');
    		$camposDestaques[] = array('campo' => 'cpx_valor_cliente_cpf');
    		$camposDestaques[] = array('campo' => 'cpx_valor_tipo_pessoa');
    		$error = true;
    	}
    
    	if ($error) {
    		$this->view->dados = $camposDestaques;    		
    		throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
    	}
    
    }

    public function excluir() { 

        $parametros = $this->tratarParametros(); 

        $parametros->tokenid = (int)$parametros->tokenid;

        if (!isset($parametros->tokenid) || intval($parametros->tokenid) <= 0) {
            echo 'ERRO';
            exit;
        }

        $responseMS = null; 

		//Seta tokenAPI sessão
		$this->initApiSession();

		//Cria um array contendo o ID do token
		$filtros['data'] = array(
			"id" => $parametros->tokenid,
			"aplicacao" => "Intranet"
		); 

		//Atribui uri exclusão
		$uri = "/v1/smartdrivetoken";

		//Chama serviço de exclusão
		$responseMS = $this->executeApi($serviceInstance, 'DELETE', $uri, $filtros, false);

        //Requisição bem sucedida
        if ($responseMS['curl_info']['http_code']==200) {
			//Limpa tokenAPI sessão
			$this->setToken('', 0);
			echo 'OK';
            exit;
        }
        else{
			//var_dump($responseMS);
            echo 'ERRO';
            exit;
        }

    }


    /***** API GATEWAY *****/

    public function getToken() {
        $this->initApiSession();
		return $_SESSION['tokenAPI']['id'];
    }

    protected function setToken($token, $expiration = 3600) {
        $_SESSION['tokenAPI']['id'] = $token;
        $_SESSION['tokenAPI']['expires'] = $expiration;
    }

    public function checkExpiredSession($httpCode) {
		return ($httpCode == 401 || $httpCode == 403 );
	}

    public function initApiSession() {
	
        $data = array(
            'client_id' => _APIGEE_CLIENT_ID_,
            'client_secret' => _APIGEE_CLIENT_SECRET_
        );
		
        $curlOptions = array(
            'CURLOPT_HTTPHEADER' => array('Content-Type: application/x-www-form-urlencoded'),
        );
		
        $options = array(
            'data' => http_build_query($data),
            'curl_options' => $curlOptions
        );
		
        $authUrl = 'oauth/token?grant_type=client_credentials';
        
		$apiUrl = _APIGEE_BASE_URL_ . $authUrl;
		
        $resRequest =  $this->requestApi('POST', $apiUrl, $options);
		
        $httpCode = $resRequest['curl_info']['http_code'];
		
        if($httpCode < 400) {
            $result = json_decode($resRequest['result']);                 
            $this->setToken($result->access_token, $result->expires_in);
        }
		
    }

    public function executeApi($serviceInstance, $method, $uri, $options = array(), $recursion = false) {

		$headers = array(
            'Authorization: Bearer ' . $_SESSION['tokenAPI']['id'],
			'Content-Language: "pt-br"',
			'Plataforma: ENT_INT'
        );

        if($method != 'GET') {
            array_push($headers, 'Content-Type: application/json');
        }

        $options['curl_options'] = array('CURLOPT_HTTPHEADER' => $headers);
    
        $parsedUrl = parse_url($uri);
        $isFullUrl = isset($parsedUrl['scheme']);
        $uri_full = $isFullUrl ? $uri : _APIGEE_BASE_URL_ . $uri; 
        $res =   $this->requestApi($method, $uri_full, $options);

        $httpCode = $res['curl_info']['http_code'];

        if( $this->checkExpiredSession($httpCode) && !$recursion){
            $this->initApiSession();
            $res = $this->executeApi($serviceInstance, $method, $uri, $options, true);
        }

        return $res;

    }

    public function requestApi($method, $uri, $options = array()) {

        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $data = (isset($options['data']) && count($options['data']) > 0) ? $options['data'] : array();
        $additionalOptions = (isset($options['curl_options']) && count($options['curl_options']) > 0) ? $options['curl_options'] : array();

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POST, true);
            	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            	curl_setopt($ch, CURLOPT_HEADER, true);
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                break;
        }

        foreach ($additionalOptions as $option => $value) {
            curl_setopt($ch, constant($option), $value);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        return array(
            'result' => $result,
            'curl_info' => $curlInfo
        );

    }

    /***** API GATEWAY *****/

    function objectToArray($object) {
	    $result = (array)$object;

	    foreach($result as &$value) {

	        if ($value instanceof stdClass) {
	            $value = objectToArray($value);
	        }

	    }
	    return $result;
	}

}