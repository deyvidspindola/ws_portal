<?php

/**
 * Usuários.
 *
 * @package Gestão
 * @author  João Paulo Tavares da Silva <joao.silva@meta.com.br>
 */

class GesUsuarios{

	/**
     * Mensagem de alerta - campos obrigatórios.
     *
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";

    const MENSAGEM_ALERTA_SEM_REGISTRO = "Para o departamento/cargo selecionado(s) não foram localizados funcionários.";

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    private $dao;

	private $view;

    private $layout;

	public function __construct(GesUsuariosDAO $dao, $layout){

		$this->dao = $dao;

        $this->layout = $layout;

		 /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();

        $this->param = new stdClass();

        $this->view->status = true;
        
        // Dados
        $this->view->dados = null;

		$this->view->caminho = _MODULEDIR_ . 'Gestao/View/ges_usuarios/';

		$this->tratarParametros();
	}

	public function index(){

		$this->view->dados->departamentos = $this->dao->buscarDepartamentos();
		$this->view->dados->cargos        = array();

		if (isset($this->param->depoid) and !empty($this->param->depoid)) {
            $this->view->dados->cargos = $this->dao->buscarCargos($this->param);
        }

        if (isset($this->param->acao) && $this->param->acao != 'index') {
            $this->validarParametros();

            if($this->view->status){
            	if($this->param->acao == 'pesquisar'){

                    $funcionarios = $this->dao->buscarFuncionarios($this->param);
                    if(count($funcionarios) > 0){
   		        	     $this->view->dados->funcionarios = $funcionarios;	
                    }else{
                        $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
                    }
            	}
            }
        }

		include $this->view->caminho . 'index.php';
	}

     /**
     * Método chamado por ajax que atualiza as Permissoes.
     *
     * @return Void
     */
	public function atualizar(){
        $dados = new stdClass();

		$this->tratarParametros();

        if($this->validaParametrosAtualizacao()){
    		$this->dao->begin();
            try{
    			foreach($this->param->atualizacao as $funoid => $permissoes){
    				$this->dao->atualizarPermissoes($funoid, $permissoes);
    			}
                $dados->mensagem->tipo = "sucesso";
                $dados->mensagem->texto = "Registro(s) atualizados(s) com sucesso.";
    			$this->dao->commit();
    		}catch(ErrorException $e){
    			$this->dao->rollback();
                $dados->mensagem->tipo = "erro";
                $dados->mensagem->texto = $e->getMessage();
    		}
        }else{
            $dados->mensagem->tipo = "erro";
            $dados->mensagem->texto = self::MENSAGEM_ERRO_PROCESSAMENTO;
        }
	 	
		echo json_encode($dados);
	}

    /**
     * Método que verifica se foi enviado algum valor invalido
     * na atualização de Permissões.
     *
     * @return boolean
     */
    private function validaParametrosAtualizacao(){
        foreach($this->param->atualizacao as $permissoes){
            foreach($permissoes as $permissao){
                 if($permissao != 0 AND $permissao != 1){
                    return false;
                 }  
            }
        }
        return true;
    }

	
	 /**
     * Método que retorna os Cargos por Ajax.
     *
     * @return Void
     */
    public function buscarCargos() {
        $dados = new stdClass();
        $dados->status          = true;
        $dados->html            = null;
        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;

        try {
            
            if(!empty($this->param->depoid)){
                $this->view->dados->cargos = $this->dao->buscarCargos($this->param);
            }

            ob_start();

            require_once $this->view->caminho.'ajax_cargos.php';

            $dados->html = utf8_encode(ob_get_clean());
        } catch (ErrorException $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'erro';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        }

        echo json_encode($dados);
    }

	 /**
     * Método que instância os dados do $_POST e $_GET.
     *
     * @return Void
     */
    private function tratarParametros() {
        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->param->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (!isset($this->param->$key)) {
                    $this->param->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
    }

     /**
     * Método que verifica campos obrigatórios.
     *
     * @return Void
     */
    private function validarParametros(){

        $camposDestacados = array();

		 if (empty($this->param->prhoid)) {
            $camposDestacados[] = array(
                'campo'    => 'prhoid',
                'mensagem' => null
            );
            $this->view->status   = false;
        }
         if (empty($this->param->depoid)) {
            $camposDestacados[] = array(
                'campo'    => 'depoid',
                'mensagem' => null
            );
            $this->view->status   = false;
        }

        $this->view->destaque = $camposDestacados;

        if (!$this->view->status) {
            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
        }
	}
}