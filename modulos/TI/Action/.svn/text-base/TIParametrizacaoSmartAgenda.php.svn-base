<?php

/**
 * Classe TIParametrizacaoSmartAgenda.
 * Camada de regra de negócio.
 *
 * @package  TI
 *
 */
class TIParametrizacaoSmartAgenda {

    private $dao;
    private $view;
    private $ambiente;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
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
        $this->ambiente              = $this->definirPrefixoAmbiente();

    }

    private function definirPrefixoAmbiente() {

        $lista = array();

        switch (_AMBIENTE_) {
            case 'PRODUCAO':
                $ambiente = 'PROD';
                $tipoUsuario = 'user_type_externo';
                break;
            case 'HOMOLOGACAO':
                $ambiente = 'HOMOLOG';
                $tipoUsuario = 'user_type_teste';
                break;
            case 'TESTE':
                $ambiente = 'TESTE';
                $tipoUsuario = 'user_type_teste';
                break;
            case 'DESENVOLVIMENTO':
                $ambiente = 'DESENV';
                $tipoUsuario = 'user_type_teste';
                break;
            default:
                $ambiente = 'DESENV';
                $tipoUsuario = 'user_type_teste';
                break;
        }

        $lista['up_case'] = $ambiente;
        $lista['low_case'] = strtolower($ambiente);
        $lista['low_user_type'] = $tipoUsuario;
        $lista['up_user_type'] = strtoupper($tipoUsuario);

        return $lista;
    }

    public function index() {

        try {

            if ( !$this->dao->validarPermissaoPagina() ) {
                header('Location: acesso_invalido.php');
            }
            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();

            if( isset($this->view->parametros->pagina) ) {
                $this->view->parametros->acao = 'log';
            }

            if( isset($this->view->parametros->acao) && $this->view->parametros->acao !='log' ) {
                $this->view->dados = $this->pesquisar();
            } else {
                 $this->view->log = $this->pesquisarLog();
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "TI/View/ti_parametrizacao_smart_agenda/index.php";
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

        $this->view->parametros->acao = isset($this->view->parametros->acao) ? $this->view->parametros->acao  : 'pesquisar';
    }


    private function pesquisar() {

        $resultadoPesquisa = $this->dao->pesquisar();

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        return $resultadoPesquisa;
    }

    private function pesquisarLog() {

        $ordenacao = array(
            ''                => 'Escolha',
            'pcslparametro'   => 'Parâmetro',
            'pcsldt_cadastro' => 'Data',
            'pcslusuoid_alteracao'   => 'Usuário'
        );

        $quantidade = array(10, 25, 50, 100);

        $resultadoPesquisa = $this->dao->pesquisarLog();

        require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

        $paginacao = new PaginacaoComponente();
        $paginacao->setarCampos($ordenacao);
        $paginacao->setQuantidadesArray($quantidade);
        $this->view->ordenacao = $paginacao->gerarOrdenacao();
        $this->view->paginacao = $paginacao->gerarPaginacao($resultadoPesquisa->total);
        $this->view->totalResultados = $resultadoPesquisa->total;

        $resultadoPesquisa = $this->dao->pesquisarLog(
            $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao()
        );

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

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

            $registroGravado = $this->salvar($this->view->parametros);


        } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

         $this->index();

    }


    private function salvar(stdClass $dados) {

        $this->dao->begin();

        $gravacao = FALSE;

        $listaParametros = $this->dao->getListaParametros();

        $parametrosOriginais = $this->dao->pesquisar();

        foreach ($dados as $chave => $valor) {

            $chave = strtoupper($chave);

            if( in_array( $chave, $listaParametros) ) {

                $gravacao = $this->dao->atualizarParametros( $chave, $valor );

                if( is_array($parametrosOriginais[$chave]['valor']) ) {
                    $valorOriginal = implode(',', $parametrosOriginais[$chave]['valor']);
                } else {
                    $valorOriginal = $parametrosOriginais[$chave]['valor'];
                }

                if( $valorOriginal !=  $valor){
                    $this->dao->gravarLog( $chave, $valorOriginal, $valor );
                }

            }
        }

        $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

        $this->dao->commit();

        return $gravacao;
    }

}

