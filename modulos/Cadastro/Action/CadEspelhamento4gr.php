<?php
header("Content-Type: application/json");
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * Classe CadEspelhamento4gr.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   Rafael Araújo <rafael.araujo.ext@sascar.com.br>
 *
 */
class CadEspelhamento4gr {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;
	
    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

		$this->dao = $dao;
		$this->view = new stdClass();

		// Dados
		$this->view->dados    = new stdClass();
		$this->view->mensagem = new stdClass();

		$this->param = new stdClass();

		foreach($_POST as $key => $value){
			$this->param->{$key} = $value;
		}

		$this->view->gerenciadoras = $dao->listarGerenciadoras();
		$this->view->clientes = null;
	}

    public function index() {
        
        try {
            $this->view->parametros = $this->tratarParametros();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }
		require_once _MODULEDIR_ . "Cadastro/View/cad_espelhamento_4gr/index.php";
		
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
 
				 //Verifica se atributo ja existe e nao sobrescreve.
				 if (!isset($retorno->$key)) {
					 
					 if(is_array($value)) {
						 
						 // Tratamento de GET com Arrays
						 foreach ($value as $chave => $valor) {
							 $value[$chave] = trim($valor);
						 }
						 $retorno->$key = isset($_GET[$key]) ? $_GET[$key] : array();
					 
					 } else {
						 $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
					 }
				 }
			 }
		 }
 
		 if (count($_POST) > 0) {
			 foreach ($_POST as $key => $value) {
 
				 if(is_array($value)) {
 
					 //Tratamento de POST com Arrays
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
 
				//Verifica se atributo já existe e não sobrescreve.
				if (!isset($retorno->$key)) {
					 $retorno->$key = isset($_FILES[$key]) ? $value : '';
				}
			}
		 }
 
		 return $retorno;
	}

    public function listarClientes() {

        $parametros = $this->tratarParametros();

		$retorno = $this->dao->listarClientes($parametros->search);
		
		echo json_encode($retorno);
        exit;
	}
	
	public function cadastrar4GR() {

        $parametros = $this->tratarParametros();

		$retorno = $this->dao->cadastrar4GR($parametros);
		
		echo json_encode($retorno);
        exit;
	}
	
	public function consultar4GR() {

        $parametros = $this->tratarParametros();

		$retorno = $this->dao->consultar4GR($parametros);

		echo json_encode($retorno);
		
        exit;
	}
	
	public function pegar4GR() {

        $parametros = $this->tratarParametros();

		$retorno = $this->dao->pegar4GR($parametros);

		echo json_encode($retorno);
		
        exit;
	}
	
	public function salvar4GR() {

        $parametros = $this->tratarParametros();

		$retorno = $this->dao->salvar4GR($parametros);

		echo json_encode($retorno);
		
        exit;
	}

	public function remover4GR() {

        $parametros = $this->tratarParametros();

		$retorno = $this->dao->remover4GR($parametros);
		
		echo json_encode($retorno);
        exit;
	}
}