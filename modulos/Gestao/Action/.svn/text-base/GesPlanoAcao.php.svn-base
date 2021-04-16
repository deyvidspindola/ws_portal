<?php

/**
 * Ações.
 *
 * @package Gestão
 * @author  Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 */

class GesPlanoAcao{

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
    
    protected $recarregarArvore = false;

    public function __construct(GesPlanoAcaoDAO $dao, $layout){

		$this->dao = $dao;

        $this->layout = $layout;

		 /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();

        $this->param = new stdClass();
        
        // Dados
        $this->view->dados = null;

		$this->view->caminho = _MODULEDIR_ . 'Gestao/View/ges_plano_acao/';

		$this->tratarParametros();
       
	}

	public function index(){

        try{

            $this->tratarParametros();

            $this->view->metas          = $this->dao->buscarMetas($this->param->ano);
            $this->view->responsaveis   = $this->dao->buscarResponsaveis(intval($this->param->meta), $this->param->ano);

            $this->view->arrComboStatus = array(
                'A' => 'Em Execução',
                'N' => 'Cancelado',
                'C' => 'Concluído',
                'T' => 'Em Atraso',
                'I' => 'A Iniciar'
            );

         } catch (ErrorException $e) {

            $this->view->mensagem->erro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagem->alerta = $e->getMessage();

        }

		include $this->view->caminho . 'index.php';
	}

    public function confirmar() {

        try{

            $this->tratarParametros();

            $this->validarParametros();

            if (!isset($this->param->codigo) || trim($this->param->codigo) == '') {

                $this->dao->inserirPlanoAcao($this->param);
                $this->view->mensagem->sucesso = 'Registro incluído com sucesso';
                $this->recarregarArvore = true;

            } else {

                $this->dao->alterarPlanoAcao($this->param);
                $this->view->mensagem->sucesso = 'Registro alterado com sucesso';
                $this->recarregarArvore = true;
            }

            $this->param->titulo = "";
            $this->param->compartilhar = "";
            $this->param->data_inicio = "";
            $this->param->data_fim = "";
            $this->param->codigo = "";

        } catch (ErrorException $e) {
            $this->view->mensagem->erro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagem->alerta = $e->getMessage();
        }

        $this->index();

    }

    public function editar() {

        try{

            $this->tratarParametros();

            $dadosPlano = $this->dao->buscarPlanoPorId($this->param->plano);

            if (count($dadosPlano) == 0) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);                
            }

            foreach ($dadosPlano as $plano) {

                $this->param->codigo = $plano->codigo;
                $this->param->meta = $plano->meta;
                $this->param->responsavel = $plano->responsavel;
                $this->param->titulo = $plano->nome;
                $this->param->data_inicio = $plano->data_inicio;
                $this->param->data_fim = $plano->data_fim;
                $this->param->status = $plano->status;
                $this->param->compartilhar = $plano->compartilhar;

            }

            $this->view->metas          = $this->dao->buscarMetas($this->param->ano);
            $this->view->responsaveis   = $this->dao->buscarResponsaveis($this->param->meta, $this->param->ano);

            $this->view->arrComboStatus = array(
                'A' => 'Em Execução',
                'N' => 'Cancelado',
                'C' => 'Concluído',
                'T' => 'Em Atraso',
                'I' => 'A Iniciar'
            );

        } catch (ErrorException $e) {
            $this->view->mensagem->erro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagem->alerta = $e->getMessage();
        }

        $this->index();

    }

    public function visualizar() {

        try{

                $this->tratarParametros();

                $this->view->tela = 'visualizar';

                $arrAcoes = $this->dao->buscarAcoes($this->param->plano);


                $this->view->acoes['em-execucao']   = array('acao' => 'Em Execução');
                $this->view->acoes['a-iniciar']     = array('acao' => 'A Iniciar');
                $this->view->acoes['em-atraso']     = array('acao' => 'Em Atraso');
                $this->view->acoes['concluido']     = array('acao' => 'Concluídos');
                $this->view->acoes['cancelado']     = array('acao' => 'Cancelados');

                if (count($arrAcoes) > 0) {

                    foreach ($arrAcoes as $key => $acao) {

                        $this->view->plano_acao = $acao->plano_acao;

                        if ($acao->status == 'A') {

                            array_push(
                                    $this->view->acoes['em-execucao'], 
                                    array(
                                        'id_acao' => $acao->id_acao,
                                        'id_plano_acao' => $acao->id_plano_acao,
                                        'data' => $acao->data_inicio_previsto,
                                        'descricao' => $acao->descricao,
                                        'responsavel' => $acao->responsavel,
                                        'porcentagem' => $acao->porcentagem . "%"
                                    )
                            );

                        }

                        if ($acao->status == 'I') {

                            array_push(
                                    $this->view->acoes['a-iniciar'], 
                                    array(
                                        'id_acao' => $acao->id_acao,
                                        'id_plano_acao' => $acao->id_plano_acao,
                                        'data' => $acao->data_inicio_previsto,
                                        'descricao' => $acao->descricao,
                                        'responsavel' => $acao->responsavel,
                                        'porcentagem' => $acao->porcentagem . "%"
                                    )
                            );

                        }

                        if ($acao->status == 'T') {

                            array_push(
                                    $this->view->acoes['em-atraso'], 
                                    array(
                                        'id_acao' => $acao->id_acao,
                                        'id_plano_acao' => $acao->id_plano_acao,
                                        'data' => $acao->data_inicio_previsto,
                                        'descricao' => $acao->descricao,
                                        'responsavel' => $acao->responsavel,
                                        'porcentagem' => $acao->porcentagem . "%"
                                    )
                            );

                        }

                        if ($acao->status == 'C') {

                            array_push(
                                    $this->view->acoes['concluido'], 
                                    array(
                                        'id_acao' => $acao->id_acao,
                                        'id_plano_acao' => $acao->id_plano_acao,
                                        'data' => $acao->data_fim_realizado,
                                        'descricao' => $acao->descricao,
                                        'responsavel' => $acao->responsavel,
                                        'porcentagem' => $acao->porcentagem . "%"
                                    )
                            );

                        }

                        if ($acao->status == 'N') {

                            array_push(
                                    $this->view->acoes['cancelado'], 
                                    array(
                                        'id_acao' => $acao->id_acao,
                                        'id_plano_acao' => $acao->id_plano_acao,
                                        'data' => $acao->data_fim_realizado,
                                        'descricao' => $acao->descricao,
                                        'responsavel' => $acao->responsavel,
                                        'porcentagem' => $acao->porcentagem . "%"
                                    )
                            );

                        }

                    }
                }

        } catch (ErrorException $e) {

            $this->view->mensagem->erro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagem->alerta = $e->getMessage();

        }

        include $this->view->caminho . 'index.php';

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

        $erro = false;

		if (!isset($this->param->titulo) || trim($this->param->titulo) == '') {

            $camposDestacados[] = array(
                'campo'    => 'titulo'
            );

            $erro = true;
        }

        if (!isset($this->param->meta) || trim($this->param->meta) == '') {

            $camposDestacados[] = array(
                'campo'    => 'meta'
            );
            
            $erro = true;
        }

        if (!isset($this->param->responsavel) || trim($this->param->responsavel) == '') {
            
            $camposDestacados[] = array(
                'campo'    => 'responsavel'
            );
            
            $erro = true;
        }

        if (!isset($this->param->data_inicio) || trim($this->param->data_inicio) == '') {
            
            $camposDestacados[] = array(
                'campo'    => 'data_inicio'
            );
            
            $erro = true;
        }

        if (!isset($this->param->data_fim) || trim($this->param->data_fim) == '') {
            
            $camposDestacados[] = array(
                'campo'    => 'data_fim'
            );
            
            $erro = true;
        }

        if (!isset($this->param->status) || trim($this->param->status) == '') {
            
            $camposDestacados[] = array(
                'campo'    => 'status'
            );

            $erro = true;
        }

        if ($erro) {
            $this->view->destaque = $camposDestacados;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO);
        }
	}

    /*
     * Busca os possíveis responsáveis pelo Plano de Ação a partir da meta.
     * Método consumido por Ajax.
     *
     * @return void
     */
    public function buscarResponsaveis() {

        $this->tratarParametros();
        $this->view->responsaveis   = $this->dao->buscarResponsaveis(intval($this->param->meta), $this->param->ano);
        echo json_encode($this->view->responsaveis);
    }
}