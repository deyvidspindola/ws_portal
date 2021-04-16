<?php

/**
 * Ações.
 *
 * @package Gestão
 * @author  Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 */

class GesAcoes{

	/**
     * Mensagem de alerta - campos obrigatórios.
     *
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";

    const MENSAGEM_ALERTA_SEM_REGISTRO = "Para o departamento/cargo selecionado(s) não foram localizados funcionários.";

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	const MENSAGEM_ALERTA_PLANO_ACAO_NAO_SELECIONADO = "Nenhum plano de ação selecionado.";

    const MENSAGEM_ALERTA_DATA_INI_FIM = "Data Inicio Realizado deve ser informado antes de indicar a data de fim.";

    private $dao;

	private $view;

    private $layout;

    protected $recarregarArvore = false;

    public function __construct(GesAcoesDAO $dao, $layout){

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

		$this->view->caminho = _MODULEDIR_ . 'Gestao/View/ges_acoes/';

        
        if (!empty($_GET['ano'])) {
            $this->param->ano = $_GET['ano']; 
        }
        
		$this->tratarParametros();
	}

	public function index(){

        try{

            $this->tratarParametros();

            $this->view->planos         = $this->dao->buscarPlanosAcao($this->param->ano);
            $this->view->responsaveis   = $this->dao->buscarResponsaveis(intval($this->param->meta), $this->param->ano);

            $this->view->arrComboTipo = array(
                'P' => 'Planejada',
                'R' => 'Preventiva',
                'C' => 'Contramedida'
            );

            $this->view->arrComboAndamento = array(
                'C' => 'Cancelar',
                'F' => 'Finalizar'
            );

            $this->view->arrComboStatus = array(
                'A' => 'Em Andamento',
                'N' => 'Cancelado',
                'C' => 'Concluído',
                'T' => 'Em Atraso',
                'I' => 'A Iniciar'
            );


         } catch (ErrorException $e) {

            $this->view->mensagem->erro = $e->getMessage();

        } catch (Exception $e) {

            if ($e->getCode() != 999) {
                $this->view->mensagem->alerta = $e->getMessage();
            } 
        }

        include $this->view->caminho . 'index.php';
    }

    /*
     * Busca os possíveis responsáveis pela Ação.
     * Método consumido por Ajax.
     *
     * @return void
     */
    public function buscarResponsaveis() {

        $this->tratarParametros();
        $idResponsavel = $this->dao->buscarResponsavelPlanoAcao($this->param->meta, $this->param->plano);
        $this->view->responsaveis   = $this->dao->buscarResponsaveis(intval($this->param->meta), $this->param->ano, $idResponsavel);
        echo json_encode($this->view->responsaveis);

    }

    public function confirmar() {

        try{

            $this->tratarParametros();

            $this->validarParametros();

            if ($this->param->id_acao == '') {
                $retorno = $this->dao->inserirAcao($this->param);
                $this->param->id_acao = $retorno->gmaoid;
                $this->view->mensagem->sucesso = 'Registro incluido com sucesso.';
                $this->recarregarArvore = true;
            } else {
                $this->dao->editarAcao($this->param);
                $this->view->mensagem->sucesso = 'Registro atualizado com sucesso.';
                $this->recarregarArvore = true;
            }
/*
            $this->param->inicio_previsto = "";
            $this->param->fim_previsto = "";
            $this->param->inicio_realizado = "";
            $this->param->fim_realizado = "";
            $this->param->andamento = "";
            $this->param->percentual = "";
            $this->param->nome_acao = "";
            $this->param->fato_causa = "";
            $this->param->motivo_cancelamento = "";
            */

        } catch (ErrorException $e) {

            $this->view->mensagem->erro = $e->getMessage();

        } catch (Exception $e) {

             if ($e->getCode() != 999) {
                $this->view->mensagem->alerta = $e->getMessage();
            } 

        }

        $this->editar();
    }

    public function editar() {

        try {

            $this->tratarParametros();

            $this->view->planos          = $this->dao->buscarPlanosAcao($this->param->ano);
            $this->view->responsaveis   = $this->dao->buscarResponsaveis('', $this->param->ano);

            $this->view->arrComboTipo = array(
                'P' => 'Planejada',
                'R' => 'Preventiva',
                'C' => 'Contramedida'
            );

            $this->view->arrComboAndamento = array(
                'C' => 'Cancelar',
                'F' => 'Finalizar'
            );

            $this->view->arrComboStatus = array(
                'A' => 'Em Andamento',
                'N' => 'Cancelado',
                'C' => 'Concluído',
                'T' => 'Em Atraso',
                'I' => 'A Iniciar'
            );

            if (intval($this->param->id_acao) > 0) {

                $acao = $this->dao->buscarAcaoPorId($this->param->id_acao);

                foreach ($acao as $dadosAcao) {

                    $this->param->id_acao = $dadosAcao->id_acao;
                    $this->param->plano = $dadosAcao->id_plano_acao;
                    $this->param->nome_acao = $dadosAcao->nome;
                    $this->param->responsavel = $dadosAcao->responsavel;
                    $this->param->tipo = $dadosAcao->tipo;
                    $this->param->fato_causa = $dadosAcao->fato_causa;
                    $this->param->inicio_previsto = $dadosAcao->data_inicio_previsto;
                    $this->param->fim_previsto = $dadosAcao->data_fim_previsto;
                    $this->param->inicio_realizado = $dadosAcao->data_inicio_realizado;
                    $this->param->fim_realizado = $dadosAcao->data_fim_realizado;
                    $this->param->percentual = $dadosAcao->percentual;
                    $this->param->andamento = $dadosAcao->andamento;
                    $this->param->motivo_cancelamento = $dadosAcao->motivo_cancelamento;
                    $this->param->compartilhar = $dadosAcao->compartilhar;
                    $this->param->status = $dadosAcao->status;

                    if($dadosAcao->status == 'C' || $dadosAcao->status == 'N') {
                        $this->param->bloqueio = true;
                    } else {
                        $this->param->bloqueio = false;
                    }


                }
            }

        } catch (ErrorException $e) {

            $this->view->mensagem->erro = $e->getMessage();

        } catch (Exception $e) {

            if ($e->getCode() != 999) {
               $this->view->mensagem->alerta = $e->getMessage();
            } 

        }

        include $this->view->caminho . 'index.php';
    }

    public function incluirItemAcao() {

        $this->tratarParametros();

        $erro = $this->dao->inserirItemAcao($this->param);

        $retorno = array('erro' => 1);

        if (!$erro) {
            $retorno = array('erro' => 0);
        }

        echo json_encode($retorno);
        exit;
    }

    public function buscarItemAcao() {

        $retorno = $this->dao->buscarItemAcao($this->param);

        $itens = array();

        $array_retorno = array('erro' => 0, 'dados' => $itens);

        if (!$retorno['erro']) {

            if (count($retorno['dados']) > 0) {

                foreach ($retorno['dados'] as $key => $item) {

                    $item = array(
                        'data'      => $item->data_cadastro,
                        'descricao' => utf8_encode($item->descricao),
                        'usuario'   => $item->usuario
                    );

                    array_push($itens, $item);
                }

                $array_retorno = array('erro' => 0, 'dados' => $itens);
            }

        } else {

            $array_retorno = array('erro' => 1, 'dados' => $itens);

        }

        /*
        $itens = array(
            0 => array(
                'data' => '18/01/2013',
                'descricao' => utf8_encode('Descrição Teste'),
                'usuario' => 'RENATO TEIXEIRA BUENO'
            ),
            1 => array(
                'data' => '11/05/2005',
                'descricao' => utf8_encode('Descrição Teste 1'),
                'usuario' => 'CESAR FICK'
            )
        ); */

        echo json_encode($array_retorno);
        exit;
    }

    public function gerarTabelaAcoesStatus($idUsuario) {

        $idFuncionario = $this->dao->buscarIdFuncionario ($idUsuario);

        $totalizadores = $this->dao->buscarTotalizadoresTabelaStatus ($idFuncionario);

        $this->prepararTabelaAcoesStatus($totalizadores);

        require_once _MODULEDIR_ . 'Gestao/View/ges_acoes/tabela_acoes_status.php';

    }

    private function prepararTabelaAcoesStatus ($totalizadores) {

        $labelLinhasTabela = array (
            'TOTAL DE AÇÕES A INICIAR',
            'TOTAL DE AÇÕES CONCLUÍDAS',
            'TOTAL DE AÇÕES ABERTAS',
            'TOTAL DE AÇÕES ATRASADAS');

        foreach ($labelLinhasTabela as $label) {
            $this->view->dados[$label]['minhas'] = 0;
            $this->view->dados[$label]['subordinados'] = 0;
        }

        foreach ($totalizadores as $value) {
            $this->view->dados[$value->status][$value->responsavel] = intval($value->quantidade);
        }

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

        if (!isset($this->param->plano) || trim($this->param->plano) == '') {

            $camposDestacados[] = array(
                'campo'    => 'plano'
            );

            $erro = true;
        }

        if (!isset($this->param->nome_acao) || trim($this->param->nome_acao) == '') {

            $camposDestacados[] = array(
                'campo'    => 'nome_acao'
            );

            $erro = true;
        }

        if (!isset($this->param->responsavel) || trim($this->param->responsavel) == '') {

            $camposDestacados[] = array(
                'campo'    => 'responsavel'
            );

            $erro = true;
        }

        if (!isset($this->param->tipo) || trim($this->param->tipo) == '') {

            $camposDestacados[] = array(
                'campo'    => 'tipo'
            );

            $erro = true;
        }

        if ($this->param->tipo == 'C') {

            if (!isset($this->param->fato_causa) || trim($this->param->fato_causa) == '') {

                $camposDestacados[] = array(
                    'campo'    => 'fato_causa'
                );

                $erro = true;
            }

        }

        if (!isset($this->param->inicio_previsto) || trim($this->param->inicio_previsto) == '') {

            $camposDestacados[] = array(
                'campo'    => 'inicio_previsto'
            );

            $erro = true;
        }

        if (!isset($this->param->inicio_previsto) || trim($this->param->inicio_previsto) == '') {

            $camposDestacados[] = array(
                'campo'    => 'inicio_previsto'
            );

            $erro = true;
        }

        if (!isset($this->param->fim_previsto) || trim($this->param->fim_previsto) == '') {

            $camposDestacados[] = array(
                'campo'    => 'fim_previsto'
            );

            $erro = true;
        }

        if (!isset($this->param->percentual) || trim($this->param->percentual) == '') {

            $camposDestacados[] = array(
                'campo'    => 'percentual'
            );

            $erro = true;
        }

        if ($this->param->status == 'N') {

            if (!isset($this->param->motivo_cancelamento) || trim($this->param->motivo_cancelamento) == '') {

                $camposDestacados[] = array(
                    'campo'    => 'motivo_cancelamento'
                );

                $erro = true;
            }

        }
        if (!isset($this->param->status) || trim($this->param->status) == '') {

            $camposDestacados[] = array(
                'campo'    => 'status'
            );

            $erro = true;
        }

        $erroData = false;
        if (empty($this->param->inicio_realizado) && !empty($this->param->fim_realizado)) {
            $erroData = true;
            $camposDestacados[] = array(
                'campo'    => 'inicio_realizado'
            );
        }


        if ($erroData) {
            $this->view->destaque = $camposDestacados;
            $this->view->mensagem->alerta[] = self::MENSAGEM_ALERTA_DATA_INI_FIM;
        }

        if ($erro) {
            $this->view->destaque = $camposDestacados;
            $this->view->mensagem->alerta[] = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
            throw new Exception("", 999);
        }
    }
}