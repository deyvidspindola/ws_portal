<?php

/**
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 *
 */
class CadMotivoTesteParcial {

    private $dao;
    private $view;
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
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();

            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_teste_parcial/index.php";
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
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
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

        return $retorno;
    }

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

        $this->view->parametros->mtpdescricao    = isset($this->view->parametros->mtpdescricao)    ? trim($this->view->parametros->mtpdescricao) : '';
        $this->view->parametros->mtpoid          = isset($this->view->parametros->mtpoid)          ? $this->view->parametros->mtpoid             : '';
    }


    private function pesquisar(stdClass $filtros) {

       $ordenacao = array(
            ''                => 'Escolha',
            'mtpdescricao'        => 'Descrição'
        );

        $quantidade = array(10, 25, 50, 100);

        $resultadoPesquisa = $this->dao->pesquisar( $filtros );

        if ( $resultadoPesquisa->total == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

        $paginacao = new PaginacaoComponente();
        $paginacao->setarCampos($ordenacao);
        $paginacao->setQuantidadesArray($quantidade);
        $this->view->ordenacao = $paginacao->gerarOrdenacao();
        $this->view->paginacao = $paginacao->gerarPaginacao($resultadoPesquisa->total);
        $this->view->totalResultados = $resultadoPesquisa->total;

        $resultadoPesquisa = $this->dao->pesquisar(
            $filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao()
        );

        return $resultadoPesquisa;
    }


   public function cadastrar($parametros = null) {


       $registroGravado = FALSE;

        try{

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            $this->inicializarParametros();

            if (isset($_POST) && !empty($_POST) ) {
                $registroGravado = $this->salvar($this->view->parametros);
            }

        } catch (ErrorException $e) {

           $this->dao->rollback();
           $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

           $this->dao->rollback();
           $this->view->mensagemAlerta = $e->getMessage();
        }

        if ( $registroGravado ) {
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_teste_parcial/cadastrar.php";
        }

    }

    public function editar() {

        try {
            $parametros = $this->tratarParametros();

            if (isset($parametros->mtpoid) && intval($parametros->mtpoid) > 0) {
                $parametros->mtpoid = (int) $parametros->mtpoid;

               $dados = $this->dao->pesquisarPorID( $parametros->mtpoid );

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

        $this->validarCamposCadastro($dados);

        $isExisteDuplicidade = $this->dao->verificarDuplicidade($dados);

        if( $isExisteDuplicidade ) {
            throw new Exception('Já existe um motivo com essa descrição.');
        }

        $this->dao->begin();
        $gravacao = null;

        if ($dados->mtpoid > 0) {
            $gravacao = $this->dao->atualizar($dados);

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        $this->dao->commit();

        return $gravacao;
    }

    private function validarCamposCadastro(stdClass $dados) {

        $camposDestaques = array();

        if (!isset($dados->mtpdescricao) || trim($dados->mtpdescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'mtpdescricao'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    public function excluir() {

       try {

            $parametros = $this->tratarParametros();

            if (!isset($parametros->mtpoid) || empty($parametros->mtpoid) ) {
                echo 'ERRO';
                exit;
            }

            $this->dao->begin();

            $confirmacao = $this->dao->excluir( $parametros->mtpoid );

            if ($confirmacao) {

                $this->dao->commit();

                echo 'OK';
                exit;

            }

        } catch (ErrorException $e) {
            $this->dao->rollback();
            echo 'ERRO';
            exit;

        } catch (Exception $e) {
            $this->dao->rollback();
            echo 'ERRO';
            exit;
        }
    }

}

