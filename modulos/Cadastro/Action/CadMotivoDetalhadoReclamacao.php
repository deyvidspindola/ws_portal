<?php

/**
 * Classe CadMotivoDetalhadoReclamacao.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   Ricardo Bonfim <renato.bueno@meta.com.br>
 *
 */
class CadMotivoDetalhadoReclamacao {

    /**
     * Objeto DAO da classe.
     *
     * @var CadRequisicaoViagemDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Ocorreu um erro ao processar, entre em contato com o suporte de sistemas.";

    /**
     * Contém dados a serem utilizados na View.
     *
     * @var stdClass
     */
    private $view;

    /**
     * Método construtor.
     *
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

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

        $this->view->paginacao = null;

        $this->view->ordenacao = null;
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

            //Verificar se a ação pesquisar e executa pesquisa
            if (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar') {
                $this->view->dados = $this->pesquisar($this->view->parametros);
                $arrIdsVinculados = $this->pesquisarVinculos($this->view->parametros);
                
                if (count($arrIdsVinculados) > 0) {
                    $this->view->idVinculados = implode(', ', $arrIdsVinculados);
                }
            }
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->view->parametros->motivos_geral = $this->dao->buscarMotivosGeral();
        $this->view->parametros->detalhamentos_motivo = $this->dao->buscarMotivosDetalhado();

        require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/index.php";
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    private function tratarParametros($parametros = null) {
        if(is_null($parametros)) {
            $retorno = new stdClass();
        } else {
            $retorno = $parametros;
        }


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

    
    public function atualizar(){

        try {

            $this->view->parametros = $this->tratarParametros();

            $arrVinculados  = ($this->view->parametros->vinculados != '')  ?  explode(',', $this->view->parametros->vinculados)  : array();
            $arrMarcados    = ($this->view->parametros->marcados != '')    ?  explode(',', $this->view->parametros->marcados)    : array();
            $arrDesmarcados = ($this->view->parametros->desmarcados != '') ?  explode(',', $this->view->parametros->desmarcados) : array();

            if (count($arrMarcados) > 0) {

                $valoresParaVincular = array();
                foreach ($arrMarcados as $key => $value){

                    if (!in_array($value, $arrVinculados)) {
                        array_push($valoresParaVincular, $value);
                    }
                }

                if (count($valoresParaVincular) > 0) {

                    foreach ($valoresParaVincular as $key => $value) {

                        if ($this->view->parametros->tipo_pesquisa == 'motivo_geral') {

                            if ($this->dao->verificarVinculo($value, $this->view->parametros->id_motivo_geral)) {

                                $this->dao->atualizarVinculo($value, $this->view->parametros->id_motivo_geral);

                            } else {

                                $this->dao->inserirVinculo($value, $this->view->parametros->id_motivo_geral);
                            }

                        } else {

                            if ($this->dao->verificarVinculo($this->view->parametros->id_detalhamento_motivo, $value)) {

                                $this->dao->atualizarVinculo($this->view->parametros->id_detalhamento_motivo, $value);
                                
                            } else {

                                $this->dao->inserirVinculo($this->view->parametros->id_detalhamento_motivo, $value);
                            }

                        }
                    }
                }
            }

            if (count($arrDesmarcados) > 0) {

                foreach ($arrDesmarcados as $key => $value){

                    if (in_array($value, $arrVinculados)) {

                        if ($this->view->parametros->tipo_pesquisa == 'motivo_geral') {
                            $this->dao->atualizarVinculo($value, $this->view->parametros->id_motivo_geral, false);
                        } else {
                            $this->dao->atualizarVinculo($this->view->parametros->id_detalhamento_motivo, $value, false);
                        }
                    }
                }
            }

            $this->view->mensagemSucesso = 'Registro atualizado com sucesso.';

            $this->index();

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisar(stdClass $filtros) {   

        if ($filtros->motivo_geral) {
            $resultadoPesquisa = $this->dao->pesquisarPorMotivoGeral($filtros);
        } else if ($filtros->detalhamento_motivo) {
            $resultadoPesquisa = $this->dao->pesquisarPorMotivoDetalhado($filtros);
        } else {
            $resultadoPesquisa = $this->dao->pesquisaSemFiltro();
        }


        return $resultadoPesquisa;
    }

    private function pesquisarVinculos(stdClass $filtros) {   

        if ($filtros->motivo_geral) {
            $resultadoPesquisa = $this->dao->pesquisarPorMotivoGeral($filtros, false);
        } else if ($filtros->detalhamento_motivo){
            $resultadoPesquisa = $this->dao->pesquisarPorMotivoDetalhado($filtros, false);
        } 

        return $resultadoPesquisa;
    }

    public function novo() {

        $this->view->parametros->motivosDetalhadosSemVinculo = $this->dao->buscarMotivosDetalhadosSemVinculo();

        if (count($this->view->parametros->motivosDetalhadosSemVinculo) > 0) {
            $this->view->parametros->existeSemVinculo = true;
        }
        
        require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/cadastrar.php"; 
    }

    public function excluir() {

        $this->view->parametrosExcluir = $this->tratarParametros();

        $this->view->mensagemErro = "Houve um erro de processamento.";

        if ($this->view->parametrosExcluir->exclusao != '') {

            $this->dao->excluir($this->view->parametrosExcluir->exclusao);

            $this->view->mensagemSucesso = "Registro excluído com sucesso.";
            $this->view->mensagemErro = "";
        }

        $this->index();
    }

    public function cadastrar(){

        try {

            $this->view->parametrosCadastrar = $this->tratarParametros();

            if (trim($this->view->parametrosCadastrar->detalhamento_motivo) == "") {

                $camposDestaques[] = array(
                    'campo' => 'detalhamento_motivo'
                );

                throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
            } 

            $this->dao->cadastrar($this->view->parametrosCadastrar->detalhamento_motivo);

            $this->view->mensagemSucesso = "Detalhamento de motivo cadastrado com sucesso.";

            $this->index();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->dados = $camposDestaques;

            $this->view->mensagemAlerta = $e->getMessage();

            $this->novo();
        }

    }

   
}

