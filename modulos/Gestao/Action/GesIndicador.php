<?php

/**
 * Regras de negócio das ações para Indicadores
 *
 * @package Gestão
 * @author  André Luiz Zilz <andre.zilz@meta.com.br>
 */

class GesIndicador{

    const MENSAGEM_ALERTA_SEM_REGISTRO = "Nenhum registro encontrado.";

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /*
     * Objeto de referência para a classe DAO
     */
    private $dao;

    /*
     * Objeto de referência para o layout filho
     */
	private $view;

    /*
     * Objeto de referência para o layout Pai
     */
    private $layout;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de sucesso
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";

    const MENSAGEM_SUCESSO_ALTERADO = "Registro alterado com sucesso.";

    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";    

    const MENSAGEM_SUCESSO_COPIA = "Registro copiado com sucesso.";


	public function __construct(GesIndicadorDAO $dao, $layout){

		$this->dao = $dao;

        $this->layout = $layout;

		$this->view = new stdClass();

        $this->param = new stdClass();

        // Dados
        $this->view->dados = null;

        $this->view->dados->resultado = array();

        $this->view->dados->metricas = array(
                                    'V' => 'Vlr',
                                    'P' => '%',
                                    'M' => '$'
                                    );
        $this->view->dados->tipos = array(
                                    'D' => 'Diário',
                                    'M' => 'Mensal',
                                    'B' => 'Bimestral',
                                    'T' => 'Trimestal',
                                    'Q' => 'Quadrimestral',
                                    'S' => 'Semestral',
                                    'A' => 'Anual'
                                   );
         $this->view->dados->tipos_indicador = array(
                                    'I' => 'Indicador',
                                    'M' => 'Meta'
                                    );

		$this->view->caminho = _MODULEDIR_ . 'Gestao/View/ges_indicador/';

		$this->tratarParametros();
	}

    /**
     * Prepara e exibe o formulario inicial
     *
     * @return void
     */
	public function index(){

        $this->inicializarParametros();

		include $this->view->caminho . 'index.php';
	}



    public function pesquisar($param = null) {

        $this->inicializarParametros($param);

        $this->view->dados->resultado = $this->dao->pesquisarIndicadores($this->param);

        if (count($this->view->dados->resultado) == 0) {
            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
        } else {
            $this->view->mensagem->alerta = '';
        }
        include $this->view->caminho . 'index.php';
    }

    /**
     * Trata os parâmetros utilizados em tela
     *
     * @return void
     */
    private function inicializarParametros($param = null) {
        
        if (is_null($param)){
            $this->tratarParametros();
        } else {
            $this->param = $param;
        }

        $this->view->dados->comboNome = $this->dao->pesquisarDadosIndicadores('gminome');
        $this->view->dados->comboCodigo = $this->dao->pesquisarDadosIndicadores('gmicodigo');
        $this->param->gmioid = isset($this->param->gmioid) ? $this->param->gmioid : '';
        $this->param->gmioid_nome = isset($this->param->gmioid_nome) ? $this->param->gmioid_nome : '';
        $this->param->gmicodigo = isset($this->param->gmicodigo) ? $this->param->gmicodigo : '';
        $this->param->gmistatus = isset($this->param->gmistatus) ? $this->param->gmistatus : '';
        $this->param->acao = isset($this->param->acao) ? $this->param->acao : '';
    }

	 /**
     * Recupera os dados enviados pelo formulário
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
        //var_dump(isset($this->param->gmioid));
        if (isset($this->param->gmioid) && intval($this->param->gmioid) > 0) {
            $this->param->editar = true;
        }
    }

    /**
     * Validar os campos obrigatórios
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
        if (!isset($dados->gminome) || trim($dados->gminome) == '') {
            $camposDestaques[] = array(
                'campo' => 'gminome'
            );
            $error = true;
        }



        if (!isset($dados->gmitipo_indicador) || trim($dados->gmitipo_indicador) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmitipo_indicador'
            );
            $error = true;
        }

        if (!isset($dados->gmimetrica) || trim($dados->gmimetrica) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmimetrica'
            );
            $error = true;
        }


        if (!isset($dados->editar)){
            if (!isset($dados->gmicodigo) || trim($dados->gmicodigo) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmicodigo'
                );
                $error = true;
            }

            if (!isset($dados->gmitipo) || trim($dados->gmitipo) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmitipo'
                );
                $error = true;
            }
        }

        if ($error) {
            $this->view->destaque = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Metodo para chamaro template de cadastro
     * @return void
     */
    public function novo() {

        /*
         * Combobox Tipo
         */
        $this->view->comboTipo = array(
            'D' => 'Diário',
            'M' => 'Mensal',
            'B' => 'Bismestral',
            'T' => 'Trimestral',
            'Q' => 'Quadrimestral',
            'S' => 'Semestral',
            'A' => 'Anual',
        );

        /*
         * Combobox Tipo Indicador
         */
        $this->view->comboMetrica = array(
            'V' => 'Vlr',
            'P' => '%',
            'M' => '$'
        );

        /*
         * Combobox Metrica
         */
        $this->view->comboTipoIndicador = array(
            'I' => 'Indicador',
            'M' => 'Meta'
        );

        include $this->view->caminho . 'cadastrar.php';
    }


/**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     *
     * @return void
     */
    public function editar($id = NULL) {

        try {
            //Verifica se o id foi passado por parametro
            if (is_null($id)) {
                //Busca os parametros do POST/GET
                $this->tratarParametros();
            } else {
                $this->param->gmioid = $id;
            }


            //Define os parametro para pesquisa
            $parametros = new stdClass();
            $parametros->gmioid = $this->param->gmioid;


            //Verifica se foi informado o id do cadastro
            if (isset($parametros->gmioid) && intval($parametros->gmioid) > 0) {
                //Realiza o CAST do parametro
                $parametros->gmioid = (int) $parametros->gmioid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarIndicadores($parametros);


                //Verifica se retornou registro na pesquisa
                if (isset($dados[0])){
                    $this->view->paramatrosCadastro = $dados[0]; 
                    $this->view->paramatrosCadastro->editar = true;                   

                    //Chama o metodo para edição passando os dados do registro por parametro.
                    $this->novo();
                } else {
                    $this->index();
                }

            } else {
                $this->index();
            }
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }


    /**
     * Metodo para salvar/cadastrar
     *
     */
    public function cadastrar() {

        try {

            $this->view->paramatrosCadastro = $this->param;

            $this->validarCamposCadastro($this->param);

            if (intval($this->param->gmioid) > 0){
                $this->dao->atualizarIndicador($this->param);    
                $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_ALTERADO;
            } else {

                if ($this->dao->validaCodigoExistente($this->param) > 0) {
                    throw new Exception("Já existe um indicador com o código informado.");   
                }

                $this->dao->cadastrarIndicador($this->param);
                $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_INCLUIR;
            }

            $this->param->acao = 'pesquisar';            

            $this->index();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

            $this->novo();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

            $this->novo();

        }

    }

    /**
     * Executa a ação
     *
     * @return void
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($this->param->gmioid) || trim($this->param->gmioid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->iniciarTransacao();

            //Filtro para exclusão
            $dados = new stdClass();

            //Realiza o CAST do parametro
            $dados->gmioid = (int) $this->param->gmioid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($dados);

            //Comita a transação
            $this->dao->comitarTransacao();

            if ($confirmacao) {

                $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
            }
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->reverterTransacao();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->reverterTransacao();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->param->acao = 'pesquisar';
        $this->param->gmioid = '';

        $this->pesquisar($this->param);
    }


    /**
     * Executa a ação
     *
     * @return void
     */
    public function copiar() {
        try {

            //Retorna os parametros
            $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($this->param->gmioid) || trim($this->param->gmioid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->iniciarTransacao();


            //Remove o registro
            $confirmacao = $this->dao->copiarIndicador($this->param->gmioid);

            //Comita a transação
            $this->dao->comitarTransacao();

            if ($confirmacao) {

                $this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_COPIA;
            }
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->reverterTransacao();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->reverterTransacao();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->param->acao = 'pesquisar';
        $this->param->gmioid = '';

        $this->pesquisar($this->param);
    }

}