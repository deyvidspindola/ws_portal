<?php

/**
 * Classe ManTempoMinimoServico.
 * Camada de regra de negócio.
 *
 * @package  Manutencao
 * @author   ANDRE LUIZ ZILZ <andre.zilz@sascar.com.br>
 *
 */
class ManTempoMinimoServico {


    private $dao;
    private $view;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    public function __construct($dao = NULL) {


        $this->dao                          = (is_object($dao)) ? $dao : NULL;
        $this->view                         = new stdClass();
        $this->view->mensagemErro           = '';
        $this->view->mensagemAlerta         = '';
        $this->view->mensagemSucesso        = '';
        $this->view->dados                  = NULL;
        $this->view->comboRepresentante     = $this->dao->pesquisarPrestador(FALSE);
        $this->view->comboAgrupamentoClasse = array();
        $this->view->comboTipoOrdem         = array();
        $this->view->parametros             = NULL;
    }

    public function index( $parametros = NULL ) {

        try {

            if(is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = new stdClass();
            }

            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
               $this->view->dados = $this->pesquisar($this->view->parametros);
               $dadosLog = $this->pesquisarLog( $this->view->dados );

               foreach ($this->view->dados as $chave => $registro) {

                  $listaLog = array();

                  foreach ($dadosLog as $id => $valor) {

                        if($valor->stmlstmoid == $registro->stmoid) {
                            $listaLog[] = $valor;
                        }
                  }

                  $registro->dados_log = $listaLog;
               }

            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Manutencao/View/man_tempo_minimo_servico/index.php";
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

        $this->view->parametros->stmoid          = isset($this->view->parametros->stmoid)          ? $this->view->parametros->stmoid          : "" ;
        $this->view->parametros->stmchave        = isset($this->view->parametros->stmchave)        ? $this->view->parametros->stmchave        : "" ;

        if( $this->view->parametros->stmoid == '' ) {
            $this->view->parametros->stmponto    = isset($this->view->parametros->stmponto)        ? $this->view->parametros->stmponto        : "" ;
        } else {
            $this->view->parametros->stmponto    = isset($this->view->parametros->stmponto)        ? $this->view->parametros->stmponto        : "A" ;
        }

        $this->view->parametros->stmrepoid       = isset($this->view->parametros->stmrepoid)       ? $this->view->parametros->stmrepoid       : "" ;
        $this->view->parametros->stmtempo_minimo = isset($this->view->parametros->stmtempo_minimo) ? $this->view->parametros->stmtempo_minimo : "" ;

        $this->view->parametros->ostgrupo        = isset($this->view->parametros->ostgrupo)        ? $this->view->parametros->ostgrupo        : "" ;
        $this->view->parametros->agccodigo       = isset($this->view->parametros->agccodigo)       ? $this->view->parametros->agccodigo       : "" ;
        $this->view->parametros->peso            = isset($this->view->parametros->peso)            ? $this->view->parametros->peso            : "" ;

        $this->view->parametros->stmtempo_minimo_original = isset($this->view->parametros->stmtempo_minimo_original) ? $this->view->parametros->stmtempo_minimo_original : "" ;

    }

    public function montarChaveTempo() {

        $this->view->parametros = $this->tratarParametros();

        $chave = $this->dao->montarChaveTempo( $this->view->parametros->ordem_servico, $this->view->parametros->stmponto );

        echo json_encode( $chave );

    }


    private function pesquisar(stdClass $filtros) {

        $ordenacao = array(
            ''                => 'Escolha',
            'stmchave'        => 'Chave de Serviço',
            'stmponto'        => 'Local Atendimento',
            'stmrepoid'       => 'Prestador de Serviço',
            'stmtempo_minimo' => 'Duração Mínima',
            'stmtempo_ofsc'   => 'Duração Sugerida OFSC'
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


    public function pesquisarChaveEspecifica() {

        $parametros = $this->tratarParametros();

        $resultadoPesquisa = $this->dao->pesquisar( $parametros );

        $resultadoPesquisa->total;

        echo json_encode( $resultadoPesquisa->total );
    }

    public function pesquisarLog( $dados ){

        $listaIDs = array();

        foreach ($dados as $key => $value) {
            $listaIDs[] = $value->stmoid;
        }

        $dadosLog = $this->dao->pesquisarLog( $listaIDs );

        return $dadosLog;

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


            //Se vier pela chave não cadatsrada gerada pela O.S.
            if( isset($_GET['stmchave']) ) {

                $this->view->parametros->ostgrupo  = substr($this->view->parametros->stmchave, 0, 2);
                $this->view->parametros->stmponto  = substr($this->view->parametros->stmchave, 2, 1);
                $this->view->parametros->agccodigo = substr($this->view->parametros->stmchave, 3, 3);
                $this->view->parametros->peso      = substr($this->view->parametros->stmchave, 6);

            } else {

                $this->view->parametros->stmchave = ( $this->view->parametros->ostgrupo . $this->view->parametros->stmponto .
                        $this->view->parametros->agccodigo . $this->view->parametros->peso );

            }

            if (isset($_POST) && !empty($_POST)) {

                $isCadastrado = $this->dao->existeRegistroCadastrado( $this->view->parametros );

                if( $isCadastrado ) {
                    throw new Exception('Chave já cadastrada. Tente outra combinação.');
                }

                $registroGravado = $this->inserirRegistro($this->view->parametros);
            }

        } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

            if ($registroGravado){
            $this->index( $this->view->parametros );
        } else {
            $this->view->comboAgrupamentoClasse = $this->dao->recuperarAgrupamentoClasse();
            $this->view->comboTipoOrdem        = $this->dao->recuperarTipoOrdemServico();
            require_once _MODULEDIR_ . "Manutencao/View/man_tempo_minimo_servico/cadastrar.php";
        }
    }

    public function editarRegistro() {

        try {

            $parametros = $this->tratarParametros();

            if (isset($parametros->stmoid) && intval($parametros->stmoid) > 0) {

                $dados = $this->dao->pesquisarPorID($parametros->stmoid);

                $dados->agccodigo = substr($dados->stmchave, 3, 3);
                $dados->ostgrupo  = substr($dados->stmchave, 0, 2);
                $dados->peso = substr($dados->stmchave, 6);

                $this->cadastrar($dados);

            } else {
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    private function inserirRegistro(stdClass $dados) {

        $this->validarCamposCadastro($dados);

        $this->dao->begin();

        $gravacao = null;

        if ( ! empty($dados->stmoid) ) {

            $gravacao = $this->dao->atualizar($dados);

            $this->dao->gravarLog($dados);

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

        if (!isset($dados->stmrepoid) || trim($dados->stmrepoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'stmrepoid'
            );
        }

        if (!isset($dados->stmtempo_minimo) || trim($dados->stmtempo_minimo) == '') {
            $camposDestaques[] = array(
                'campo' => 'stmtempo_minimo'
            );
        }

        if (!isset($dados->ostgrupo) || trim($dados->ostgrupo) == '') {
            $camposDestaques[] = array(
                'campo' => 'ostgrupo'
            );
        }

        if (!isset($dados->agccodigo) || trim($dados->agccodigo) == '') {
            $camposDestaques[] = array(
                'campo' => 'agccodigo'
            );
        }

        if (!isset($dados->peso) || trim($dados->peso) == '') {
            $camposDestaques[] = array(
                'campo' => 'peso'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    public function inativarRegistro() {

        try {

            $parametros = $this->tratarParametros();

            if (!isset($parametros->stmoid) || empty($parametros->stmoid) ) {
                echo 'ERRO';
                exit;
            }

            $this->dao->begin();

            $confirmacao = $this->dao->inativarRegistro( $parametros->stmoid );

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

