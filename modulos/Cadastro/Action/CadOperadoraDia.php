<?php

require_once _MODULEDIR_ . 'Cadastro/DAO/CadOperadoraDiaDAO.php';

class CadOperadoraDia {

     private $dao;
     private $dados;

     public function __construct() {
         $this->dao = new CadOperadoraDiaDAO();
     }

     /*
      * Index, tela de pesquisa
      * @params $parametros
      */
     public function index() {
         
        $this->limparParametrosPesquisa();
        $this->comboOperadoras = $this->dao->buscarOperadoras();
        if (count($this->comboOperadoras) == 0) {
             $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
        }
        require_once _MODULEDIR_.'Cadastro/View/cad_operadores_dia/index.php';
     }

     /*
      * Método de cadastro
      */
     public function cadastrar() {

        $this->comboOperadoras = $this->dao->buscarOperadoras();
         if (count($this->comboOperadoras) == 0) {
              $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
         }

      if (isset($_POST) && !empty($_POST)) {

            $this->parametros = $dados = $this->montarParametros();

            if ($this->dao->validarPeriodo($dados)) {
                $this->adicionarMensagemAlerta('Registro já cadastrado para período informado.');
                require_once _MODULEDIR_.'Cadastro/View/cad_operadores_dia/cadastrar.php';
                exit;
            }

            if (isset($dados->opdoid) && !empty($dados->opdoid)) {

              $verificar = $this->dao->verificarOperadoraVigencia($dados->opdoid);

              if ($verificar === 'S'){
                if (!$this->dao->atualizar($dados)) {
                    $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
                }else{
                    $this->adicionarMensagemSucesso("O registro foi alterado com sucesso.");
                 }
              }else if ($verificar === 'N') {
                  $this->adicionarMensagemAlerta("Registro em uso ou já utilizado.");
              }else if ($verificar === FALSE) {
                  $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
              }
              
              $_GET['id'] = $dados->opdoid;
              $this->editar();exit;
              
            } else {
                $id_operadora_id = $this->dao->salvar($dados);
                if ($id_operadora_id === FALSE) {
                    $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
                }else{
                    $this->adicionarMensagemSucesso("O registro foi incluído com sucesso.");
                }
                
                if ($id_operadora_id !== FALSE) {
                    $_GET['id'] = $id_operadora_id;
                } 
                $this->editar();exit;
            }
      }else{
          require_once _MODULEDIR_.'Cadastro/View/cad_operadores_dia/cadastrar.php';
      }
      
     }

     /*
      * Método de edição
      */
     public function editar() {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];

            $this->parametros = $this->dao->buscarOperadora($id);
            if ($this->parametros === FALSE) {
                $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
            }

            $this->comboOperadoras = $this->dao->buscarOperadoras();
            if (count($this->comboOperadoras) == 0) {
                $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
            }

            $verificar = $this->dao->verificarOperadoraVigencia($id);
            $this->bloqueio = false;
            if ($verificar === 'N') {
                $this->bloqueio = true;
            }else if ($verificar === FALSE) {
                $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
            }
            require_once _MODULEDIR_.'Cadastro/View/cad_operadores_dia/cadastrar.php';

        } else {
            $this->pesquisar();
        }
     }

     /*
      * Método de delete
      */
     public function deletar() {
          if (isset($_GET['id']) && !empty($_GET['id'])) {
              $id = $_GET['id'];

              $verificar = $this->dao->verificarOperadoraVigencia($id);

              if ($verificar === 'S'){
                  if ($this->dao->deletar($id)) {
                      $this->adicionarMensagemSucesso("O registro foi excluído com sucesso.");
                  }
              }else if ($verificar === 'N') {
                  $this->adicionarMensagemAlerta("Registro em uso ou já utilizado.");
              }else if ($verificar === FALSE) {
                  $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
              }
          }
          $sessao = TRUE;
          
          $this->pesquisar($sessao);
     }

     /*
      * Método para tratamento de post
      * @return $parametros
      */
     private function montarParametros(){
         $parametros = new stdClass();
          foreach ($_POST as $key=>$item) {
               $parametros->$key = isset($_POST[$key]) && $_POST[$key] != '' ? $_POST[$key] : '';
          }
          return $parametros;
     }
     
     
     /*
      * Método para tratamento de post para pesquisa, gravando sessão
      * @return $parametros
      */
     private function montarParametrosPesquisar(){
        $parametros = new stdClass();

        $parametros->opdopeoid = '';
        $parametros->opddt_inivigencia = '';
        $parametros->opddt_fimvigencia = '';
        $parametros->acao = '';
        
        if (isset($_POST['opdopeoid'])) {
            $parametros->opdopeoid = $_POST['opdopeoid'];
        } else if (isset($_SESSION['pesquisa']['opdopeoid'])) {
            $parametros->opdopeoid = $_SESSION['pesquisa']['opdopeoid'];
        }
        
        if (isset($_POST['opddt_inivigencia'])) {
            $parametros->opddt_inivigencia = $_POST['opddt_inivigencia'];
        } else if (isset($_SESSION['pesquisa']['opddt_inivigencia'])) {
            $parametros->opddt_inivigencia = $_SESSION['pesquisa']['opddt_inivigencia'];
        }
        
        if (isset($_POST['opddt_fimvigencia'])) {
            $parametros->opddt_fimvigencia = $_POST['opddt_fimvigencia'];
        } else if (isset($_SESSION['pesquisa']['opddt_fimvigencia'])) {
            $parametros->opddt_fimvigencia = $_SESSION['pesquisa']['opddt_fimvigencia'];
        }
        
        if (isset($_POST['acao'])) {
            $parametros->acao = $_POST['acao'];
        } else if (isset($_SESSION['pesquisa']['acao'])) {
            $parametros->acao = $_SESSION['pesquisa']['acao'];
        }
        
        $_SESSION['pesquisa']['opdopeoid'] = $parametros->opdopeoid;
        $_SESSION['pesquisa']['opddt_inivigencia'] = $parametros->opddt_inivigencia;
        $_SESSION['pesquisa']['opddt_fimvigencia'] = $parametros->opddt_fimvigencia;
        $_SESSION['pesquisa']['acao'] = $parametros->acao;
        
        return (object) $_SESSION['pesquisa'];
     }
     
     
     private function limparParametrosPesquisa(){
         $_SESSION['pesquisa']['opdopeoid'] = '';
        $_SESSION['pesquisa']['opddt_inivigencia'] = '';
        $_SESSION['pesquisa']['opddt_fimvigencia'] = '';
        $_SESSION['pesquisa']['acao'] = '';
     } 

     /*
      * Método da pesquisa
      */
     public function pesquisar($sessao = '') {

        if (isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar'){
            $this->resultado = array();
            $parametros = $this->montarParametrosPesquisar();
            $this->resultado = $this->dao->pesquisar($parametros);
        }else if ( (isset($_GET['sessao']) || $sessao === TRUE) && $_SESSION['pesquisa']['acao'] == 'pesquisar') {
            $this->resultado = array();
            $parametros = (object) $_SESSION['pesquisa'];
            $this->resultado = $this->dao->pesquisar($parametros);
        }

        if ($this->resultado === FALSE){
            $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
        }
        
        $this->parametros = $parametros;
        
        $this->comboOperadoras = $this->dao->buscarOperadoras();
        if (count($this->comboOperadoras) == 0) {
            $this->adicionarMensagemErro("Houve um erro no processamento de dados.");
        }
        
        require_once _MODULEDIR_.'Cadastro/View/cad_operadores_dia/index.php';
          
        
     }

     /**
         * Cria mensagem de erro para a view ou retorno json
         * @param string $mensagem
         * @param string $divId
         */
        private function adicionarMensagemErro($mensagem, $divId='mensagens'){
        	$msg = new stdClass();
        	$msg->divMensagens = $divId;
        	$msg->tipo = 'erro';
        	$msg->mensagem = $mensagem;
        	$this->retorno->mensagens[] = $msg;
        }

        /**
         * Cria mensagem de sucesso para a view ou retorno json
         * @param string $mensagem
         * @param string $divId
         */
        private function adicionarMensagemSucesso($mensagem, $divId='mensagens'){
        	$msg = new stdClass();
        	$msg->divMensagens = $divId;
        	$msg->tipo = 'sucesso';
        	$msg->mensagem = $mensagem;
        	$this->retorno->mensagens[] = $msg;
        }

         /**
          * Cria mensagem de alerta para a view ou retorno json
          * @param string $mensagem
          * @param string $divId
          */
        private function adicionarMensagemAlerta($mensagem, $divId='mensagens'){
        	$msg = new stdClass();
        	$msg->divMensagens = $divId;
        	$msg->tipo = 'alerta';
        	$msg->mensagem = $mensagem;
        	$this->retorno->mensagens[] = $msg;
        }

        /**
         * Exibe mensagens do sistema
         * @return string
         */
        public function exibirMensagens($divId='mensagens'){
           	$mensagem = "<div id=\"$divId\" class=\"invisivel\">";
           	if(count($this->retorno->camposDestaque) > 0){
           		$mensagem .= "<script type=\"text/javascript\" >jQuery(document).ready(function() { showFormErros(".json_encode($this->retorno->camposDestaque)."); });</script>";
           	}
           	if(count($this->retorno->mensagens)>0){
           		foreach ($this->retorno->mensagens as $msg){
	        		if($msg->divMensagens == $divId){
	        			$mensagem .= "<div class=\"mensagem ".$msg->tipo."\">" . $msg->mensagem . "</div>";
	        		}
	        	}
           	}
           	$mensagem .= "<script type=\"text/javascript\" >jQuery(document).ready(function() { jQuery('#$divId').showMessage(); });</script></div>";
           	return $mensagem;
        }

}