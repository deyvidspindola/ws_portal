<?php

/**
 * Classe CadModelo.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 *
 */
class CadModeloVeiculo {

    private $dao;
    private $view;
    private $usuarioLogado;
    private $permissaoCadastro;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    public function __construct($dao = NULL) {

        $this->dao                      = (is_object($dao)) ? $dao : NULL;
        $this->view                     = new stdClass();
        $this->view->mensagemErro       = '';
        $this->view->mensagemAlerta     = '';
        $this->view->mensagemSucesso    = '';
        $this->view->dados              = NULL;
        $this->view->parametros         = NULL;
        $this->usuarioLogado            = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->permissaoCadastro        = ($_SESSION['funcao']['cadastro_modelo'] == 1 ? TRUE : FALSE);
        $this->url_pagina               = "modelo.php";
        $this->view->listaMarcas        = $this->dao->recuperarMarcas('A');
    }

    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();

            $this->inicializarParametros();

            //Valida se o departamento e cargo do susuário logado pussuem permissão para a pagina acessada
            if (!validarPermissaoPagina($this->usuarioLogado, $this->url_pagina) ){
                header('Location: acesso_invalido.php');
            }

            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }
        require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_veiculo/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
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
     */
    private function inicializarParametros() {

        $this->view->parametros->acao      = isset($this->view->parametros->acao)      ? trim($this->view->parametros->acao) : 'index';

        $this->view->parametros->mlomodelo      = isset($this->view->parametros->mlomodelo)      ? trim($this->view->parametros->mlomodelo) : '';
        $this->view->parametros->mlooid         = isset($this->view->parametros->mlooid)         ? $this->view->parametros->mlooid          : 0;
        $this->view->parametros->mlomcaoid      = isset($this->view->parametros->mlomcaoid)      ? $this->view->parametros->mlomcaoid       : 0;

        $this->view->parametros->marca_inativa  = isset($this->view->parametros->marca_inativa)  ? $this->view->parametros->marca_inativa   : 'A';
        $this->view->parametros->modelo_inativo = isset($this->view->parametros->modelo_inativo) ? $this->view->parametros->modelo_inativo  : 'A';


        $this->view->parametros->retModelo      = isset($this->view->parametros->retModelo)      ? $this->view->parametros->retModelo       : '';
        $this->view->parametros->url_retorno    = isset($this->view->parametros->url_retorno)    ? $this->view->parametros->url_retorno     : '';

        if( ! empty($this->view->parametros->mlomcaoid) ) {
            $this->view->listaMarcas       = $this->dao->recuperarMarcas($this->view->parametros->marca_inativa);
            $this->view->listaModelos      = $this->dao->recuperarModelos($this->view->parametros->mlomcaoid, $this->view->parametros->modelo_inativo);
        }

        if($this->view->parametros->acao == 'cadastrar' || $this->view->parametros->acao == 'editar') {

            $this->view->listaMarcaFamilia   = $this->dao->recuperarMarcaFamilia( $this->view->parametros->mlomcaoid );
            $this->view->listaTipoVeiculo    = $this->dao->recuperarTipoVeiculo();
            $this->view->listaValvula        = $this->dao->recuperarListaValvula();
            $this->view->listaListaAcessorio = $this->dao->recuperarListaAcessorio();
            $this->view->listaAnos           = $this->dao->recuperarListaAnos();
            $this->view->listaPaises         = $this->dao->recuperarPaises();
            $this->view->listaMarcaModelo    = $this->dao->recuperarMarcaModelo();

            $this->view->parametros->mlomcfoid       = isset($this->view->parametros->mlomcfoid)       ? $this->view->parametros->mlomcfoid       : 0;
            $this->view->parametros->mlopaisoid      = isset($this->view->parametros->mlopaisoid)      ? $this->view->parametros->mlopaisoid      : 'BR';
            $this->view->parametros->mlotipveioid    = isset($this->view->parametros->mlotipveioid)    ? $this->view->parametros->mlotipveioid    : 0;
            $this->view->parametros->mlovstoid       = isset($this->view->parametros->mlovstoid)       ? $this->view->parametros->mlovstoid       : 0;
            $this->view->parametros->mlodica         = isset($this->view->parametros->mlodica)         ? $this->view->parametros->mlodica         : '';
            $this->view->parametros->mlostatus       = isset($this->view->parametros->mlostatus)       ? $this->view->parametros->mlostatus       : 't';
            $this->view->parametros->mloconversor    = isset($this->view->parametros->mloconversor)    ? $this->view->parametros->mloconversor    : 'f';
            $this->view->parametros->mlovalvula      = isset($this->view->parametros->mlovalvula)      ? $this->view->parametros->mlovalvula      : 'f';
            $this->view->parametros->mlosensor_volvo = isset($this->view->parametros->mlosensor_volvo) ? $this->view->parametros->mlosensor_volvo : 'f';
            $this->view->parametros->mlobloqueio     = isset($this->view->parametros->mlobloqueio)     ? $this->view->parametros->mlobloqueio     : 'f';
            $this->view->parametros->mlosleep        = isset($this->view->parametros->mlosleep)        ? $this->view->parametros->mlosleep        : 'f';
            $this->view->parametros->mlovlmoid1      = isset($this->view->parametros->mlovlmoid1)      ? $this->view->parametros->mlovlmoid1      : 0;
            $this->view->parametros->mlovlmoid2      = isset($this->view->parametros->mlovlmoid2)      ? $this->view->parametros->mlovlmoid2      : 0;
            $this->view->parametros->mlovlmoid3      = isset($this->view->parametros->mlovlmoid3)      ? $this->view->parametros->mlovlmoid3      : 0;

            $this->view->parametros->mlofipe_codigo       = isset($this->view->parametros->mlofipe_codigo)       ? $this->view->parametros->mlofipe_codigo       : '';
            $this->view->parametros->mlocatbase_descricao = isset($this->view->parametros->mlocatbase_descricao) ? $this->view->parametros->mlocatbase_descricao : '';
            $this->view->parametros->mlocatbase_codigo    = isset($this->view->parametros->mlocatbase_codigo)    ? $this->view->parametros->mlocatbase_codigo    : '';
            $this->view->parametros->mloprocedencia       = isset($this->view->parametros->mloprocedencia)       ? $this->view->parametros->mloprocedencia       : '';
            $this->view->parametros->mlonumpassag         = isset($this->view->parametros->mlonumpassag)         ? $this->view->parametros->mlonumpassag         : 0;

            if( ! empty($this->view->parametros->mlooid) ) {
                $this->view->dados_itens = $this->dao->recuperarListaAcessorioModelo($this->view->parametros->mlooid);
            } else {
                $this->view->dados_itens = array();
            }

            if( $this->view->parametros->mlotipveioid > 0 ) {
                $this->view->listaSubTipoVeiculo = $this->dao->recuperarSubTipoVeiculo($this->view->parametros->mlotipveioid);
            } else {
                $this->view->listaSubTipoVeiculo = array();
            }

        }
    }

    public function recuperarAcessoriosAJAX() {

        $parametros = $this->tratarParametros();
        $dados = array();

        $retorno = $this->dao->recuperarListaAcessorioModelo( $parametros->mlooid );

        $retorno = array_values($retorno);

        foreach ($retorno as $i => $value) {
            $dados[$i]['mlaioid']          = $value->mlaioid;
            $dados[$i]['acessorio_id']     = $value->mlaiobroid;
            $dados[$i]['ano_inicial']      = $value->mlaiano_inicial;
            $dados[$i]['ano_final']        = $value->mlaiano_final;
            $dados[$i]['valor_cliente']    = $value->mlaiinstala_cliente;
            $dados[$i]['valor_seguradora'] = $value->mlaiinstala_seguradora;
            $dados[$i]['acessorio']        = utf8_encode($value->obrobrigacao);

        }

        echo json_encode($dados);
        exit;
    }

    public function recuperarModelosAjax() {

        $parametros = $this->tratarParametros();
        $dados = array();

        $retorno = $this->dao->recuperarModelos($parametros->mlomcaoid, $parametros->modelo_inativo);

        foreach ($retorno as $i => $value) {
            $dados[$i]['chave'] = $value->mlooid;
            $dados[$i]['valor'] = utf8_encode($value->mlomodelo);
        }

        echo json_encode($dados);
        exit;
    }

    public function recuperarMarcasAjax() {

        $parametros = $this->tratarParametros();
        $dados = array();

        $retorno = $this->dao->recuperarMarcas($parametros->marca_inativa);

        foreach ($retorno as $i => $value) {
            $dados[$i]['chave'] = $value->mcaoid;
            $dados[$i]['valor'] = utf8_encode($value->mcamarca);
        }

        echo json_encode($dados);
        exit;
    }

    public function recuperarMarcaFamiliaAjax() {

        $parametros = $this->tratarParametros();
        $dados = array();

        $retorno = $this->dao->recuperarMarcaFamilia($parametros->mlomcaoid);

        foreach ($retorno as $i => $value) {
            $dados[$i]['chave'] = $value->mcfoid;
            $dados[$i]['valor'] = utf8_encode($value->mcffamilia);
        }

        echo json_encode($dados);
        exit;
    }

    public function recuperarSubTipoVeiculoAjax() {

        $parametros = $this->tratarParametros();
        $dados = array();

        $retorno = $this->dao->recuperarSubTipoVeiculo( $parametros->mlotipveioid );

        foreach ($retorno as $i => $value) {
            $dados[$i]['chave'] = $value->vstoid;
            $dados[$i]['valor'] = utf8_encode($value->vstdescricao);
        }

        echo json_encode($dados);
        exit;
    }

    private function pesquisar(stdClass $filtros) {

       $ordenacao = array(
            ''                => 'Escolha',
            'mcamarca'        => 'Marca',
            'mlomodelo'        => 'Modelo',
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
            unset($_POST, $_GET);
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_veiculo/cadastrar.php";
        }
    }

    public function editar() {

        try {
            $parametros = $this->tratarParametros();

            if (isset($parametros->mlooid) && intval($parametros->mlooid) > 0) {
                $parametros->mlooid = (int) $parametros->mlooid;

               $dados = $this->dao->pesquisarPorID( $parametros->mlooid );

               $dados->retModelo = $parametros->retModelo;
               $dados->acao =  $parametros->acao;

               $this->cadastrar($dados);
            } else {
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    public function verificarDuplicidadeAJAX() {

        try {

            $parametros = $this->tratarParametros();
            $isExisteDuplicidade = $this->dao->verificarDuplicidade($parametros);

            if ($isExisteDuplicidade) {
                echo 'S';
                exit;
            } else {
                echo 'N';
                exit;
            }

        } catch (ErrorException $e) {
            echo 'ERRO';
            exit;

        } catch (Exception $e) {
            echo 'ERRO';
            exit;
        }
    }

    private function salvar(stdClass $dados) {

        $this->dao->begin();
        $gravacao = NULL;

        $dados = $this->tratarDadosBanco( $dados );
        $listaAcessorio = $this->montarListaAcessorio( $dados );

        if ($dados->mlooid > 0) {

            $idModelo = $dados->mlooid;
            $gravacao = $this->dao->atualizar($dados);

            $listaTela    = array(0);

            foreach ($listaAcessorio as $key => $value) {

                if( $value['mlaioid'] > 0 ){
                    $listaTela[] = $value['mlaioid'];
                    unset($listaAcessorio[$key]);
                }
            }

            $gravacao = $this->dao->excluirAcessoriosPorID( $listaTela, $idModelo );
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

        } else {

            $idModelo = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        if( count($listaAcessorio) > 0 ) {
            $gravacao = $this->dao->inserirAcessorio( $idModelo, $listaAcessorio );
        } else {
            $gravacao = TRUE;
        }

        $this->dao->commit();

        return $gravacao;
    }

    private function tratarDadosBanco($dados) {

        $campos = array('mlomcfoid', 'mlovlmoid1', 'mlovlmoid2', 'mlovlmoid3');

        foreach ($dados as $key => $value) {
            if( in_array($key, $campos) && empty($dados->$key) ) {
                $dados->$key = 'NULL';
            }
        }

        return $dados;
    }

    private function montarListaAcessorio ( $dados ){

        $listaAcessorio = array();

        if( is_array($dados->mlaiobroid) ) {
            foreach ($dados->mlaiobroid as $key => $value) {
                $listaAcessorio[$key]['mlaiobroid']             = intval($value);
                $listaAcessorio[$key]['mlaiano_inicial']        = ($dados->mlaiano_inicial[$key] == '') ? 'NULL' : intval($dados->mlaiano_inicial[$key]);
                $listaAcessorio[$key]['mlaiano_final']          = ($dados->mlaiano_final[$key]   == '') ? 'NULL' : intval($dados->mlaiano_final[$key]);
                $listaAcessorio[$key]['mlaioid']                = intval( $dados->mlaioid[$key] );
                $listaAcessorio[$key]['mlaiinstala_cliente']    = $dados->mlaiinstala_cliente[$key];
                $listaAcessorio[$key]['mlaiinstala_seguradora'] = $dados->mlaiinstala_seguradora[$key];
            }
        }

        return $listaAcessorio;
    }

    public function excluir() {

       try {

            $parametros = $this->tratarParametros();

            if (!isset($parametros->mlooid) || empty($parametros->mlooid) ) {
                echo 'ERRO';
                exit;
            }

            $this->dao->begin();

            $confirmacao = $this->dao->excluir( $parametros->mlooid );

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

