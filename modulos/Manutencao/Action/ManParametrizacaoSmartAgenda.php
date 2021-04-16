<?php

/**
 * Classe ManParametrizacaoSmartAgenda.
 * Camada de regra de negócio.
 *
 * @package  Manutencao
 *
 */
class ManParametrizacaoSmartAgenda {

    private $dao;
    private $view;

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

            $this->view->listaStatusItem = $this->dao->getListaStatusItem();

            $this->view->listaPrestador = $this->dao->recuperarPrestadores();
            $this->view->listaStatusOrdemServico = $this->dao->recuperarStatusOrdemServico();

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

        require_once _MODULEDIR_ . "Manutencao/View/man_parametrizacao_smart_agenda/index.php";
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

        $this->view->parametros->acao                            = isset($this->view->parametros->acao)                           ? $this->view->parametros->acao                           : 'pesquisar';
        $this->view->parametros->duracao_padrao_atividade_ofsc   = isset($this->view->parametros->duracao_padrao_atividade_ofsc)  ? $this->view->parametros->duracao_padrao_atividade_ofsc  : '';
        $this->view->parametros->considera_tempo_atividade_ofsc  = isset($this->view->parametros->considera_tempo_atividade_ofsc) ? $this->view->parametros->considera_tempo_atividade_ofsc : '';
        $this->view->parametros->fator_calculo_tempo_peso        = isset($this->view->parametros->fator_calculo_tempo_peso)       ? $this->view->parametros->fator_calculo_tempo_peso       : '';
        $this->view->parametros->semanas_limite_pesquisa         = isset($this->view->parametros->semanas_limite_pesquisa)        ? $this->view->parametros->semanas_limite_pesquisa        : '';
        $this->view->parametros->semanas_calendario              = isset($this->view->parametros->semanas_calendario)             ? $this->view->parametros->semanas_calendario             : '';
        $this->view->parametros->status_os_pesquisa              = isset($this->view->parametros->status_os_pesquisa)             ? $this->view->parametros->status_os_pesquisa             : '';
        $this->view->parametros->status_item_os                  = isset($this->view->parametros->status_item_os)                 ? $this->view->parametros->status_item_os                 : '';
        $this->view->parametros->periodo_dzero_manha_inicio      = isset($this->view->parametros->periodo_dzero_manha_inicio)     ? $this->view->parametros->periodo_dzero_manha_inicio     : '';
        $this->view->parametros->periodo_dzero_manha_fim         = isset($this->view->parametros->periodo_dzero_manha_fim)        ? $this->view->parametros->periodo_dzero_manha_fim        : '';
        $this->view->parametros->periodo_dzero_manha_agenda      = isset($this->view->parametros->periodo_dzero_manha_agenda)     ? $this->view->parametros->periodo_dzero_manha_agenda     : '';
        $this->view->parametros->periodo_dzero_tarde_inicio      = isset($this->view->parametros->periodo_dzero_tarde_inicio)     ? $this->view->parametros->periodo_dzero_tarde_inicio     : '';
        $this->view->parametros->periodo_dzero_tarde_fim         = isset($this->view->parametros->periodo_dzero_tarde_fim)        ? $this->view->parametros->periodo_dzero_tarde_fim        : '';
        $this->view->parametros->periodo_dzero_tarde_agenda      = isset($this->view->parametros->periodo_dzero_tarde_agenda)     ? $this->view->parametros->periodo_dzero_tarde_agenda     : '';
        $this->view->parametros->periodo_dzero_noite_inicio      = isset($this->view->parametros->periodo_dzero_noite_inicio)     ? $this->view->parametros->periodo_dzero_noite_inicio     : '';
        $this->view->parametros->periodo_dzero_noite_fim         = isset($this->view->parametros->periodo_dzero_noite_fim)        ? $this->view->parametros->periodo_dzero_noite_fim        : '';
        $this->view->parametros->periodo_dzero_noite_agenda      = isset($this->view->parametros->periodo_dzero_noite_agenda)     ? $this->view->parametros->periodo_dzero_noite_agenda     : '';
        $this->view->parametros->repoid_solicitacao_falsa        = isset($this->view->parametros->repoid_solicitacao_falsa)       ? $this->view->parametros->repoid_solicitacao_falsa       : '';
        $this->view->parametros->tempo_preparacao_remessa        = isset($this->view->parametros->tempo_preparacao_remessa)       ? $this->view->parametros->tempo_preparacao_remessa       : '';
        $this->view->parametros->tempo_recebimento_remessa       = isset($this->view->parametros->tempo_recebimento_remessa)      ? $this->view->parametros->tempo_recebimento_remessa      : '';

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

        $dados->status_os_pesquisa  = implode(',', $dados->status_os_pesquisa);
        $dados->status_item_os      = implode(',', $dados->status_item_os);
        $dados->periodo_dzero_manha = $dados->periodo_dzero_manha_inicio . ';' . $dados->periodo_dzero_manha_fim . ';' . $dados->periodo_dzero_manha_agenda;
        $dados->periodo_dzero_tarde = $dados->periodo_dzero_tarde_inicio . ';' . $dados->periodo_dzero_tarde_fim . ';' . $dados->periodo_dzero_tarde_agenda;
        $dados->periodo_dzero_noite = $dados->periodo_dzero_noite_inicio . ';' . $dados->periodo_dzero_noite_fim . ';' . $dados->periodo_dzero_noite_agenda;

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

