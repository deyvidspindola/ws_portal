<?php

/**
 * Classe GesEstruturaArvore.
 * Camada de regra de negócio.
 *
 * @package  Gestao
 * @author   João Paulo Tavares da Silva <joao.silva@meta.com.br>
 * 
 */
class GesEstruturaArvore {

    /**
     * Objeto DAO da classe.
     * 
     * @var GesEstruturaArvoreDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de sucesso para inserção do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";

    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Registro alterado com sucesso.";

    /**
     * Mensagem de sucesso para exclusão do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
    
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    /**
     * Contém dados a serem utilizados na View.
     * 
     * @var stdClass 
     */
    private $view;


    private $layout;
    
    protected $recarregarArvore = false;

    /**
     * Método construtor.
     * 
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null, $layout) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        $this->layout = $layout;

        //Cria objeto da view
        $this->view = new stdClass();
        //Mensagem
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = null;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Status de uma transação 
        $this->view->status = false;
    }

    /**
     * Método padrão da classe. 
     * 
     * Reponsável também por realizar a pesquisa invocando o método privado
     * 
     * @return void
     */
    public function index() {
        try {

           
            $this->view->parametros = $this->tratarParametros();
            
            //Inicializa os dados
            $this->inicializarParametros();

            $this->view->departamentos = $this->dao->buscarDepartamentos();
            $this->view->cargos        = array();
            $this->view->funcionarios  = array();
            $this->view->listaAnos      = $this->listaAnos();

            if(isset($this->view->parametros->camposPreenchidos) and !is_null($_SESSION['parametros']->camposPreenchidos)){
                $_SESSION['parametros']->camposPreenchidos = null;
                $this->view->parametros = $_SESSION['parametros'];
                if(empty($this->view->parametros->gmaano)){
                    $this->view->parametros->acao = null;
                }
            }

            if (isset($this->view->parametros->gmadepoid) and !empty($this->view->parametros->gmadepoid)) {
                $this->view->cargos = $this->dao->buscarCargos($this->view->parametros);
            }

            if (isset($this->view->parametros->gmaprhoid) and !empty($this->view->parametros->gmaprhoid)) {
                $this->view->funcionarios = $this->dao->buscarFuncionarios($this->view->parametros);
            }

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar') {
                
                $_SESSION['parametros'] = $this->view->parametros;
                $_SESSION['parametros']->camposPreenchidos = null;
                $this->validarCamposPesquisa($this->view->parametros);
                $this->view->dados = $this->pesquisar($this->view->parametros);
           
            }else if( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'excluir'){
                $_SESSION['parametros'] = $this->view->parametros;
                $_SESSION['parametros']->camposPreenchidos = null;
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }
            
        } catch (ErrorException $e) {
		
            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/index.php";
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Parametros tradados
     * 
     * @retrun stdClass
     */
    private function tratarParametros() {
        $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        }
        
        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                
                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
        return $retorno;
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     * 
     * @return void
     */
    private function inicializarParametros() {
        
        //Verifica se os parametro existem, senão iniciliza todos
		//$this->view->parametros->gmaano = isset($this->view->parametros->gmaano) && trim($this->view->parametros->gmaano) != "" ? trim($this->view->parametros->gmaano) : 0 ; 
		$this->view->parametros->gmanome = isset($this->view->parametros->gmanome) && !empty($this->view->parametros->gmanome) ? trim($this->view->parametros->gmanome) : ""; 
		$this->view->parametros->gmafunoid = isset($this->view->parametros->gmafunoid) && !empty($this->view->parametros->gmafunoid) ? trim($this->view->parametros->gmafunoid) : ""; 
		$this->view->parametros->gmadepoid = isset($this->view->parametros->gmadepoid) && !empty($this->view->parametros->gmadepoid) ? trim($this->view->parametros->gmadepoid) : ""; 
		$this->view->parametros->gmaprhoid = isset($this->view->parametros->gmaprhoid) && !empty($this->view->parametros->gmaprhoid) ? trim($this->view->parametros->gmaprhoid) : ""; 
		//$this->view->parametros->gmanivel = isset($this->view->parametros->gmanivel) && trim($this->view->parametros->gmanivel) != "" ? trim($this->view->parametros->gmanivel) : 0 ; 
		//$this->view->parametros->gmasubnivel = isset($this->view->parametros->gmasubnivel) && trim($this->view->parametros->gmasubnivel) != "" ? trim($this->view->parametros->gmasubnivel) : 0 ; 
		$this->view->parametros->gmafunoid_superior = isset($this->view->parametros->gmafunoid_superior) && !empty($this->view->parametros->gmafunoid_superior) ? trim($this->view->parametros->gmafunoid_superior) : ""; 
    }
    

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;
        
        return $resultadoPesquisa;
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     * 
     * @param stdClass $parametros Dados do cadastro, para edição (opcional)
     * 
     * @return void
     */
    public function cadastrar($parametros = null, $refresh = false) {

         $_SESSION['parametros']->camposPreenchidos = true;
        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try{
            
            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            if ($refresh) {
                $this->view->parametros->gmafunoid = $_POST['gmaoid'];
                $this->view->parametros->gmafunoid = $_POST['gmafunoid'];
                $this->view->parametros->gmadepoid = $_POST['gmadepoid'];
                $this->view->parametros->gmaprhoid = $_POST['gmaprhoid'];
                unset($_POST);
            } else {
                 //Incializa os parametros
                $this->inicializarParametros();
            }

            $this->view->departamentos = $this->dao->buscarDepartamentos();
            $this->view->cargos        = array();
            $this->view->funcionarios  = array(); 
            $this->view->listaAnos     = $this->listaAnos();
            $this->view->superior      = $this->dao->buscarTodosFuncionarios();
            
            if (isset($this->view->parametros->gmadepoid) and !empty($this->view->parametros->gmadepoid)) {
                $this->view->cargos = $this->dao->buscarCargos($this->view->parametros);
            }

            if (isset($this->view->parametros->gmaprhoid) and !empty($this->view->parametros->gmaprhoid)) {
                $this->view->funcionarios = $this->dao->buscarFuncionarios($this->view->parametros);
            }
            
            //Verificar se foi submetido o formulário e grava o registro em banco de dados 
            if (isset($_POST) && !empty($_POST)) { 
                $registroGravado = $this->salvar($this->view->parametros);
                $this->view->parametros = null;
                unset($_POST);
                $this->index();
                exit;
            } 
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();

            if (!empty($this->view->parametros->gmaoid)) {
                $this->editar(true);
            }
        }
        
        if($registroGravado){
            $this->recarregarArvore = true;
        }
      
            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/cadastrar.php";
    }
    
    
        public function editar($refresh = false) {
      
            try {
                //Parametros 
                $parametros = $this->tratarParametros();

                //Verifica se foi informado o id do cadastro
                if (isset($parametros->gmaoid) && intval($parametros->gmaoid) > 0) {
                    //Realiza o CAST do parametro
                    $parametros->gmaoid = (int) $parametros->gmaoid;

                    //Pesquisa o registro para edição
                    $dados = $this->dao->pesquisarPorID($parametros->gmaoid);
                    
                    $dados->editar = true;

                    //Chama o metodo para edição passando os dados do registro por parametro.
                    $this->cadastrar($dados, $refresh);
                } else {
                   $this->index();
                }

            } catch (ErrorException $e) {
                $this->view->mensagemErro = $e->getMessage();
                $this->index();
            }
        }
    
    /**
     * Grava os dados na base de dados.
     * 
     * @param stdClass $dados Dados a serem gravados
     * 
     * @return void
     */
    private function salvar(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastro($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;
        
        if (isset($dados->gmaoid) && intval($dados->gmaoid) > 0){
            
            $dadosAntigos = $this->dao->pesquisarPorID($dados->gmaoid);
            
            $atualizaArvore = $dados->gmafunoid != $dadosAntigos->gmafunoid;
            
            //Efetua a atualização do registro
            $gravacao = $this->dao->atualizar($dados);
            
            
            if ($atualizaArvore){
                $this->dao->atualizarFuncionarioArvore($dadosAntigos->gmafunoid, $dados->gmafunoid);
                $this->dao->atualizarFuncionarioPlanos($dadosAntigos->gmafunoid, $dados->gmafunoid);
                $this->dao->atualizarFuncionarioMetas($dadosAntigos->gmafunoid, $dados->gmafunoid);
                $this->dao->atualizarFuncionarioAcoes($dadosAntigos->gmafunoid, $dados->gmafunoid);
            }
            
            unset($dadosAntigos);

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }


        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     * 
     * @param stdClass $dados Dados a serem validados
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->gmaoid) || intval($dados->gmaoid) == 0) {
            if (!isset($dados->gmaano) || trim($dados->gmaano) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmaano'
                );
                $error = true;
            }

            if (!isset($dados->gmanivel) || trim($dados->gmanivel) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmanivel'
                );
                $error = true;
            }
            if (!isset($dados->gmasubnivel) || trim($dados->gmasubnivel) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmasubnivel'
                );
                $error = true;
            }
            if (!isset($dados->gmanome) || trim($dados->gmanome) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmanome'
                );
                $error = true;
            }
        }

        if (!isset($dados->gmadepoid) || trim($dados->gmadepoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmadepoid'
            );
            $error = true;
        }
        if (!isset($dados->gmaprhoid) || trim($dados->gmaprhoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmaprhoid'
            );
            $error = true;
        }
        if (!isset($dados->gmafunoid) || trim($dados->gmafunoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmafunoid'
            );
            $error = true;
        }


        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

       /**
     * Validar os campos obrigatórios da pesquisa.
     * 
     * @param stdClass $dados Dados a serem validados
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function validarCamposPesquisa(stdClass $dados) {
        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;
        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->gmaano) || trim($dados->gmaano) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmaano'
            );
            $error = true;
        }

        if ($error) {

            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    public function validarExclusao(){
          $parametros = $this->tratarParametros();
        
        $dados = new stdClass();
        $dados->status          = true;
        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;

        try {
            $dados->confirmacao = $this->dao->validarExclusao($parametros);    
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
     * Executa a exclusão de registro.
     * 
     * @return void 
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();
            
            //Verifica se foi informado o id
            if (!isset($parametros->gmaoid) || trim($parametros->gmaoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
             
            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->gmaoid = (int) $parametros->gmaoid;
            
            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->gmaoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
                
                $this->recarregarArvore = true;
            }
            
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }
        $this->index();
    }
    /**
     * Método que retorna os Cargos por Ajax.
     *
     * @return Void
     */
    public function buscarCargos() {
        
        $this->view->parametros = $this->tratarParametros();
        
        $dados = new stdClass();
        $dados->status          = true;
        $dados->html            = null;
        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;

        try {

            if(!empty($this->view->parametros->gmadepoid)){

                $this->view->cargos = $this->dao->buscarCargos($this->view->parametros);
            }

            ob_start();

            require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/ajax_cargos.php";

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

    public function buscarFuncionarios(){
        
        $this->view->parametros = $this->tratarParametros();
        
        $dados = new stdClass();
        $dados->status          = true;
        $dados->html            = null;
        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;

        try {
            $this->view->funcionarios = $this->dao->buscarFuncionarios($this->view->parametros);

            ob_start();

            require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/ajax_funcionarios.php";

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

    private function listaAnos(){
        $anos = array();
        $anos[] = 2014;
        $anoAtual = date('Y');
        for($ano = 2014; $ano <= $anoAtual; $ano++){
            $anos[] = $ano+1;
        }
        if($anoAtual == 2013){
            $anos[] = 2015;
        }
        sort($anos);
        return $anos;
    }
}

 

