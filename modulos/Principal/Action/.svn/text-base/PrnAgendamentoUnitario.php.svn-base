<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING ^ E_NOTICE);
//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/agendamento_unitario_'.date('d-m-Y').'.txt');


require_once _MODULEDIR_ . "SmartAgenda/Action/EstoqueAgenda.php";
require_once _MODULEDIR_ . "SmartAgenda/Action/Activity.php";
require_once _MODULEDIR_ . "SmartAgenda/Action/OrdemServico.php";
require_once _MODULEDIR_ . "SmartAgenda/Action/ComunicacaoEmailsSMS.php";
require_once _MODULEDIR_ . "SmartAgenda/Action/SmartAgenda.php";
require_once _MODULEDIR_ . "SmartAgenda/Action/Agenda.php";
include_once _MODULEDIR_ . 'SmartAgenda/Action/AgendamentoVO.php';
include_once _MODULEDIR_ . 'SmartAgenda/Action/ControleConsumo.php';
include_once _MODULEDIR_ . 'SmartAgenda/Action/Contrato.php';
include_once _MODULEDIR_ . 'SmartAgenda/Action/Error.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/lib/Components/Enderecos/core/componenteEnderecoDAO.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/intranet/lib/Components/Enderecos/core/componenteEnderecoAction.php';
require_once _SITEDIR_ . '/lib/Components/Enderecos/core/componenteEnderecoDAO.php';
require_once _SITEDIR_ . '/lib/Components/Enderecos/core/componenteEnderecoAction.php';

/**
 * Classe PrnAgendamentoUnitario.
 * Camada de regra de negócio.
 *
 */
class PrnAgendamentoUnitario {

    private $dadosEndereco;

    private $dao;
    private $view;
    public $usuarioLogado;
    private $estoqueAgenda;
    private $smartAgenda;
    private $ordemServico;
    private $agenda;
    private $error;
    private $controleConsumo;
    private $controleCapacidade;
    private $integracao = false;
    protected $totalSemanasCalendario;
    protected $totalSemanasIntervalo;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_SUCESSO_BACKOFFICE         = "Solicitação enviada com sucesso.";
    const MENSAGEM_SUCESSO_NOTIFICAR_OS       = "Notificação enviada com sucesso.";
    const MENSAGEM_SUCESSO_AGENDAMENTO        = "Agendamento concluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_SUCESSO_CANCELAMENTO       = "Cancelamento do Agendamento concluído com sucesso.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados. ";
    const MENSAGEM_FALTA_ESTOQUE              = "Impossibilidade de agendamento.<br>Motivo: Indisponibilidade de estoque de equipamentos e materiais nas proximidades do endereço de atendimento informado e também no Centro de Distribuição.";
    const MENSAGEM_FALTA_PRESTADOR            = "Impossibilidade de agendamento.<br>Motivo: Não existem prestadores de serviços nas proximidades do endereço de atendimento informado.<br><b>Por favor, informe outro endereço. </b>";
    const MENSAGEM_SEM_AGENDA_SKILL           = "Impossibilidade de agendamento.<br>Favor direcionar para operações de campo liberar quota do representante OU mapear técnico com habilidade necessária para o serviço.";
    const MENSAGEM_ALERTA_PERIODO_DATA        = "Data inicial não pode ser maior que a data final.";
    const MENSAGEM_DATA_INDISPONIVEL          = "A data selecionada não está mais disponível para o prestador.";
    const MENSAGEM_ESTOQUE_INDISPONIVEL       = "O estoque do(s) produto(s) não estão mais disponíveis para o prestador.";
    const MENSAGEM_AGENDA_INDISPONIVEL        = "Agenda indisponível pelos próximos %s dias para este endereço.";
    const MENSAGEM_HORA_INDISPONIVEL          = "O horário selecionado não está mais disponível para o prestador.";
    const MENSAGEM_CLIENTE_PREMIUM            = "Impossibilidade de agendamento.<br>Motivo:Não há produtos configurados para atender o cliente Especial / Premium.";


    public function __construct($dao = null) {


        $this->dao                   = is_object($dao) ? $this->dao = $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;
        $this->view->diasSemana      = array();
        $this->view->agenda          = array();
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->estoqueAgenda         = new EstoqueAgenda( $this->dao->getConn() );
        $this->ordemServico          = new OrdemServico( $this->dao->getConn() );
        $this->smartAgenda           = new smartAgenda( $this->dao->getConn() );
        $this->agenda                = new Agenda( $this->dao->getConn() );
        $this->error                 = new Error();
        $this->controleConsumo       = new ControleConsumo( $this->dao->getConn() );
        $this->contrato              = new Contrato( $this->dao->getConn() );
        $enderecosAction             = new ComponenteEnderecoAction($this->dao->getConn());
        $enderecosAction->isBloquearComboPais(true);
        $enderecosAction->isDigitacaoLivre(true);
        $enderecosAction->setUrlBase('prn_agendamento_unitario.php');

        $this->view->enderecosAction = $enderecosAction;
    }

    public function index() {

        try {

            $this->view->parametros                     = $this->tratarParametros();
            $this->view->tiposServicos                  = $this->ordemServico->getTiposServicos();
            $this->view->estados                        = $this->dao->getEstados();
            $this->view->motivosCancelamentoAgendamento = $this->getMotivoCancelamentoAgendamento();
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {

                unset($_SESSION['agendamentos']);
                $this->view->dados = $this->pesquisar($this->view->parametros);

            } else if (isset($_SESSION['msgAgendamentoUnitario'])) {

                $this->view->mensagemSucesso = $_SESSION['msgAgendamentoUnitario'];
                unset($_SESSION['msgAgendamentoUnitario']);

            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/index.php";
    }

    public function setUsuarioLogado($CodigoUsuario) {
        $this->usuarioLogado = $CodigoUsuario;
    }

    /**
     * @param $parametros stdClass com os dados da requisicao
     * Metodo utilizado para carregas as informacoes necessarias para reutilizar este objeto fora do projeto
     *
     */
    public function integracao($parametros)
    {
        $this->integracao = true;
        $this->view->parametros                     = $parametros;
        $this->view->tiposServicos                  = $this->ordemServico->getTiposServicos();
        $this->view->estados                        = $this->dao->getEstados();
        $this->view->motivosCancelamentoAgendamento = $this->getMotivoCancelamentoAgendamento();
        $this->inicializarParametros();
        $this->atualizarParametros();
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
        $this->view->parametros->cmp_cliente            = isset($this->view->parametros->cmp_cliente) && !empty($this->view->parametros->cmp_cliente) ? trim($this->view->parametros->cmp_cliente) : "";
        $this->view->parametros->cmp_cpf_cnpj           = isset($this->view->parametros->cmp_cpf_cnpj) && !empty($this->view->parametros->cmp_cpf_cnpj) ? trim($this->view->parametros->cmp_cpf_cnpj) : "";
        $this->view->parametros->cmp_numero_os          = isset($this->view->parametros->cmp_numero_os) && !empty($this->view->parametros->cmp_numero_os) ? trim($this->view->parametros->cmp_numero_os) : "";
        $this->view->parametros->cmp_data_os            = isset($this->view->parametros->cmp_data_os) && !empty($this->view->parametros->cmp_data_os) ? trim($this->view->parametros->cmp_data_os) : "";
        $this->view->parametros->cmp_tipo_servico       = isset($this->view->parametros->cmp_tipo_servico) && trim($this->view->parametros->cmp_tipo_servico) != "" ? trim($this->view->parametros->cmp_tipo_servico) : 0 ;
        $this->view->parametros->cmp_contrato           = isset($this->view->parametros->cmp_contrato) && !empty($this->view->parametros->cmp_contrato) ? trim($this->view->parametros->cmp_contrato) : "";
        $this->view->parametros->cmp_placa              = isset($this->view->parametros->cmp_placa) && !empty($this->view->parametros->cmp_placa) ? trim($this->view->parametros->cmp_placa) : "";
        $this->view->parametros->cmp_chassi             = isset($this->view->parametros->cmp_chassi) && !empty($this->view->parametros->cmp_chassi) ? trim($this->view->parametros->cmp_chassi) : "";
        $this->view->parametros->cpm_cep                = isset($this->view->parametros->cpm_cep) && !empty($this->view->parametros->cpm_cep) ? trim($this->view->parametros->cpm_cep) : "";
        $this->view->parametros->cmp_uf                 = isset($this->view->parametros->cmp_uf) && !empty($this->view->parametros->cmp_uf) ? trim($this->view->parametros->cmp_uf) : 0;
        $this->view->parametros->cmp_cidade             = isset($this->view->parametros->cmp_cidade) && !empty($this->view->parametros->cmp_cidade) ? trim($this->view->parametros->cmp_cidade) : 0;
        $this->view->parametros->cmp_data_inicio        = isset($this->view->parametros->cmp_data_inicio) ? $this->view->parametros->cmp_data_inicio : "" ;
        $this->view->parametros->cmp_data_fim           = isset($this->view->parametros->cmp_data_fim) ? $this->view->parametros->cmp_data_fim : "" ;
        $this->view->parametros->cmp_agendamento_aberto = isset($this->view->parametros->cmp_agendamento_aberto) ? $this->view->parametros->cmp_agendamento_aberto : 0;
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     */
    private function pesquisar(stdClass $filtros)
    {
        $ordenacao = array(
            ''            => 'Escolha',
            'veiplaca'    => 'Placa',
            'data_agenda' => 'Data Agendamento',
            'data_os'     => 'Data O.S',
            'clinome'     => 'Nome de Cliente'
        );

        $quantidade = array(10, 25, 50, 100, 500);

        //Validar os campos
        $this->validarCampos($filtros);

        $totalRegistros = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if ($totalRegistros->total == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

        $paginacao = new PaginacaoComponente();
        $paginacao->setarCampos($ordenacao);
        $paginacao->setQuantidadesArray($quantidade);
        $this->view->ordenacao = $paginacao->gerarOrdenacao();
        $this->view->paginacao = $paginacao->gerarPaginacao($totalRegistros->total);
        $this->view->totalResultados = $totalRegistros->total;
        $this->view->status = TRUE;

        $resultadoPesquisa = $this->dao->pesquisar(
            $filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao()
        );

        return $resultadoPesquisa;
    }


    private function validarCampos(stdClass $dados) {

        $camposDestaques = array();

        if ( trim($dados->cmp_data_inicio) != '' && trim($dados->cmp_data_fim) != '' ) {

            if($this->validarPeriodo($dados->cmp_data_inicio, $dados->cmp_data_fim)){

                $camposDestaques[] = array('campo' => 'cmp_data_inicio');
                $camposDestaques[] = array('campo' => 'cmp_data_fim');
            }

        }

        if (!empty($camposDestaques)) {
            $this->view->validacao = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_PERIODO_DATA);
        }
    }

    public function getMotivoCancelamentoAgendamento() {

        try{
            $retorno =  $this->dao->getMotivosNoShow();
            return $retorno;
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    public function cancelarAgendamentoAjax() {

        try {
            //armazena as OS que serão canceladas
            $cancelar_OS = array();

            $parametros = $this->tratarParametros();

            if( empty($parametros->ordoid) || (empty($parametros->obs)) ){
                throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
            }

            $parametros->obs = utf8_decode($parametros->obs);


            $this->dao->begin();

            //Busca contrato da OS
            $contrato_os = $this->contrato->getContratoOS($parametros->ordoid);

            //Verifica se contem OS com relacionamento
            $agendamentoExtra = $this->dao->buscarOSAdicionalAjax($contrato_os[0]['ordconnumero'], $parametros->ordoid);

            $ordoid_1 = $parametros->ordoid;
            $ordoid_2 = 0;
            $osTipo_1 = '';
            $osTipo_2 = '';
            $ostioid_1 = 0;
            $cancelar_OS = array( $parametros->ordoid );

            foreach ($agendamentoExtra as $valor) {

                if($valor->tipo == '1') {
                    $osTipo_1 = utf8_encode($valor->descricao);
                    $ostioid_1 = $valor->ordostoid;
                } else {
                    $osTipo_2 = utf8_encode($valor->descricao);
                    $ordoid_2 = $valor->osaordoid;
                }
            }

            //se for de retirada, verifica se tem os de instalação agendada
            if( (count($agendamentoExtra) > 1) && ($ostioid_1 == 3) ){

                if($parametros->param == 'false'){

                    //chama modal de confirmação
                    $msg = '';
                    echo json_encode(array("codigo" => 3,
                        "msg" => $msg,
                        "os_relacionada" => $ordoid_2,
                        "tipo_os_1" => $osTipo_1,
                        "tipo_os_2" => $osTipo_2));
                    exit;

                    //efetiva o cancelamento
                } elseif($parametros->param == 'true'){
                    //agrupa as OSs
                    $cancelar_OS[1] = $ordoid_2;

                    $this->cancelarLinkTarefas($cancelar_OS);

                    //cancela todas as OS
                    foreach ($cancelar_OS as $num_os){
                        $this->efetivarCancelamento($num_os, $parametros);
                    }

                    //cancela somente a OS de retirada
                }elseif($parametros->param == 'cancela_os'){

                    $this->efetivarCancelamento($parametros->ordoid, $parametros);
                }

            } else {
                $this->efetivarCancelamento( $parametros->ordoid, $parametros);
            }

            $this->dao->commit();

            $_SESSION['msgAgendamentoUnitario'] = self::MENSAGEM_SUCESSO_CANCELAMENTO;

            echo json_encode ( array (
                "codigo" => 1,
                "msg" => utf8_encode(self::MENSAGEM_SUCESSO_CANCELAMENTO)
            ) );

            exit ();
        } catch (ErrorException $e) {
            $this->dao->rollback();
            echo json_encode(array("codigo" => 0, "msg" => utf8_encode($e->getMessage())));
            exit;
        } catch (Exception $e) {
            $this->dao->rollback();
            echo json_encode(array("codigo" => 0, "msg" => utf8_encode($e->getMessage())));
            exit;
        }
    }

    /**
     * Efetiva o cancelamento no BD e no OFSC
     */
    private function efetivarCancelamento($ordoid, $parametros, $cancelarOFSC = true, $isReagendamento = false) {

        //faz o tratamento das informações no campo observação inseridas pelo usuário
        $observacao = trim(nl2br(strip_tags(addslashes($parametros->obs))));

        //obtem os dados do agendamento que vai ser cancelado
        $agenda = $this->agenda->getDadosAgendamento($ordoid, 'ORDEM_SERVICO');

        //Cancela o registro de agendamento
        $id_atividade = $this->agenda->setExcluirAgendamento($ordoid, $this->usuarioLogado, $observacao);

        //Remove o prestador de servico na ordem de servico
        if($this->ordemServico->atualizarRepresentante($ordoid, NULL) == false){
            throw new Exception( $this->error->getErro('0001') );
        }

        //Remove o instalador na ordem de servico
        if($this->ordemServico->atualizarInstalador($ordoid, NULL) == false){
            throw new Exception( $this->error->getErro('0003') );
        }

        //limpa local de instalacao
        $this->ordemServico->excluirLocalInstalacao($ordoid);

        //obtem o motivo (descricao) do cancelamento
        $motivoCancelamento = $this->getMotivoCancelamentoAgendamento();
        $motivo = (isset($motivoCancelamento[$parametros->motivo])) ? $motivoCancelamento[$parametros->motivo]->omndescricao : '';

        if ($this->integracao) {
            if (!empty($parametros->motivo)) {
                $motivo = $parametros->motivo;
                $observacao = '';
            }
        }

        if(!$isReagendamento) {
            $observacao = "Agendamento cancelado. " . $motivo . ". " . $observacao;
            $statusAcao = 'Agendamento Cancelado';
            $dataAgenda = $agenda['osadata'];
            $horaAgenda = $agenda['osahora'];

        } else{
            $statusAcao = 'Inst/Assist. Agendada';
            $dataAgenda = $agenda['osadata'];
            $horaAgenda = $agenda['osahora'];
        }

        $idMotivoCancelamento = $this->ordemServico->retornoHistoricoCorretora( $statusAcao);

        //grava o histórico de cancelamento
        $res_historico = $this->ordemServico->salvaHistorico(
            $ordoid,
            $this->usuarioLogado,
            $observacao,
            $dataAgenda,
            $horaAgenda,
            $idMotivoCancelamento
        );

        if(!$res_historico){
            throw new Exception( $this->error->getErro('0002') );
        }

        //Cacnelar Reserva de estoque
        $this->estoqueAgenda->setNumeroOrdemServico($ordoid);
        $resultado = $this->estoqueAgenda->setCancelarReserva();

        if ($resultado['status'] != 'erro') {
            //Cancela solicitacao
            $resultado = $this->estoqueAgenda->setCancelarSolicitacaoProduto();

            if ($resultado['status'] == 'erro') {
                throw new Exception( $this->error->getErro('0013') );
            }

        } else {
            throw new Exception( $this->error->getErro('0012') );
        }

        // Excluir Agendamento
        $this->controleConsumo->setIdOrdemServico(array($ordoid));
        $this->controleConsumo->removerAgenda();

        // se retornar com o ID da atividade, cancela no OFSC
        if ( (count ( $id_atividade ) > 0) && ($cancelarOFSC) ) {

            $activityClass = new Activity();

            $activityClass->setActivityId($id_atividade);
            //busca status atual da atividade
            $statusAtividade = $activityClass->ConsultarAtividade();

            $dataAgendada = strtotime($agenda['osadata']);
            $dataAtual = strtotime(date('Y-m-d'));
            $isDataPassada = ($dataAgendada < $dataAtual) ? true : false;

            if( (isset($statusAtividade->status)) && ($statusAtividade->status == 'pending') && !$isDataPassada ) {
                $res = $activityClass->cancelarAtividade();

                if( isset($res->error_msg) ){
                    throw new Exception( $this->error->getErro('0011') );
                }
            }
        }

        $this->enviarNotificacao($ordoid, 'AGENDAMENTO_CANCELADO', NULL, $agenda['osaoid']);

        return true;

    }

    protected function cancelarLinkTarefas(array $idOrdemServicos) {

        $idAtividades = $this->dao->buscarIdAgendamentos($idOrdemServicos);
        $activityClass = new Activity();

        $activityClass->setActivityId( $idAtividades['RT'] );
        $activityClass->setLinkedActivityId( $idAtividades['RI'] );
        $activityClass->setLinkType( 'atividade_2_reinstalacao' );
        $retorno = $activityClass->cancelarRelacionamentoAtividade();

        if( isset($retorno->error_msg) ){
            throw new Exception( $this->error->getErro('0011') );
        }
    }

    public function buscarClientes() {

        $resultado = array();
        $parametros = $this->tratarParametros();

        if (strlen($parametros->term) > 2) {
            $resultado = $this->dao->getNomeClientes($parametros->term);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    public function buscarCidades() {

        $resultado = array();
        $parametros = $this->tratarParametros();

        if (!empty($parametros->idEstado)) {
            $resultado = $this->dao->getCidades($parametros->idEstado);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    protected function atualizarParametros() {

        $dados = $this->smartAgenda->parametrosSmartAgenda(array('SEMANAS_CALENDARIO', 'SEMANAS_LIMITE_PESQUISA'));

//        ($this->integracao) ? $this->totalSemanasCalendario = 2 : $this->totalSemanasCalendario = $dados['SEMANAS_CALENDARIO'];
        $this->totalSemanasCalendario = $dados['SEMANAS_CALENDARIO'];
        $this->totalSemanasIntervalo =  $dados['SEMANAS_LIMITE_PESQUISA'];
    }

    public function cancelarRedirecionamento() {

        $json = array('resultado' => false);
        $parametros = $this->tratarParametros();

        if (!empty($parametros->idOrdemServico)) {
            $representante = $this->dao->getNomeRepresentante(
                $parametros->idOrdemServico
            );

            if (count($representante)) {
                $this->dao->begin();

                try {
                    $this->dao->cancelarRedirecionamento(
                        $parametros->idOrdemServico
                    );

                    $this->ordemServico->salvaHistorico(
                        $parametros->idOrdemServico,
                        $_SESSION['usuario']['oid'],
                        "O direcionamento para o Prestador de Serviços \"{$representante['repnome']}\" foi excluído.",
                        null, null, 91
                    );

                    $this->dao->commit();
                    $json['resultado'] = true;
                } catch (Exception $e) {
                    $this->dao->rollback();
                    $json['mensagem'] = self::MENSAGEM_ERRO_PROCESSAMENTO."(".$e->getMessage().")";
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function detalhe() {

        $parametros = $this->tratarParametros();
        $parametros->etapa = isset($parametros->etapa) ? $parametros->etapa : 'info';

        if(isset($parametros->retirada_reinstalacao)) {

            $parametros->retirada_reinstalacao = ($parametros->retirada_reinstalacao == '1') ? true : false;

        } else {
            $parametros->retirada_reinstalacao  = false;

        }

        $avancar = true;

        // Busca as informações básicas da(s) ordem de serviço
        $this->detalheOrdemServico($parametros);

        if (in_array($parametros->etapa, array('agenda', 'salvar'))) {
            // Atualiza os parametros de configuração do SmartAgenda
            $this->atualizarParametros();

            //  Checa se executa a verificação de disponibilidade
            if (isset($parametros->cmp_disponibilidade)) {
                $avancar = $this->detalheDisponibilidadeEndereco($parametros);
            }

            // Calcula a agenda
            if ($avancar && $parametros->etapa != 'salvar') {
                $this->detalheAgenda($parametros);
            }

            if ($parametros->etapa == 'salvar') {
                $this->salvar($parametros);
            }
        }
        $this->view->parametros = $parametros;

        require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/formulario_detalhe_os.php";
    }

    public function detalheOrdemServico($parametros) {

        // Busca os dados da ordem de serviço
        $ordemServicoPrincipal = $this->dao->getOrdemServico($parametros->id);

        if (!count($ordemServicoPrincipal)) {
            $this->view->mensagemErro = 'Nenhuma informação encontrada para os '
                . 'parâmetros de busca informado';
            if($this->integracao) {
                return array('erro' => $this->view->mensagemErro);
            }
            $this->index();
            exit;
        }

        // Flag para informar que foi essa OS que o usuário selecionou
        $ordemServicoPrincipal['principal'] = true;

        // Buscas os serviços a serem executados
        $ordemServicoPrincipal['servicos'] = $this->dao->getServicosOS(
            $parametros->id
        );

        // Adiciona a ordem de serviço principal
        $ordemServicos[] = $ordemServicoPrincipal;
        $chaveOSPrincipal = 0;

        // Quando o tipo de serviço ser retirada ou reinstalação procura OS adicional
        if( in_array($ordemServicoPrincipal['ostoid'], array(2, 3, 9)) &&
            ($parametros->retirada_reinstalacao || $parametros->etapa == 'info') ) {

            $agendamentoExtra = $this->dao->buscarOSAdicional(
                $ordemServicoPrincipal['ordconnumero'],
                $ordemServicoPrincipal['ordoid'],
                ($parametros->operacao == 'reagendar')
            );


            if (count($agendamentoExtra)) {
                // Buscas os dados principais da OS
                $ordemServicoAdicional = $this->dao->getOrdemServico(
                    $agendamentoExtra['ordoid']
                );

                // Buscas os serviços a serem executados
                $ordemServicoAdicional['servicos'] = $this->dao->getServicosOS(
                    $agendamentoExtra['ordoid']
                );

                if ($ordemServicoAdicional['ostoid'] == 3) {
                    array_unshift($ordemServicos, $ordemServicoAdicional);
                    $chaveOSPrincipal++;
                } else {
                    array_push($ordemServicos, $ordemServicoAdicional);
                }
            }
        }

        if( $parametros->etapa == 'info' ) {
            // Verifica Retirada e reinstalação no mesmo dia
            $parametros->checkRetiradaReinstalacao = $this->definirRetiradaReinstacao($ordemServicos);
        }

        $this->view->parametros = $parametros;
        $this->view->ordemServicos = $ordemServicos;
        $this->view->chaveOSPrincipal = $chaveOSPrincipal;
    }

    /**
     * Busca os dados da Ordem de Serviço
     */
    public function obterOrdemServico($parametros) {

        // Busca os dados da ordem de serviço
        $ordemServicoPrincipal = $this->dao->getOrdemServico($parametros->id);
        $ordemServicos[] = $ordemServicoPrincipal;

        return $ordemServicos;
    }

    /**
     * Define as regras para o checkbox de retira com reinstalacao mesmo dia
     */
    private function definirRetiradaReinstacao($ordemServicos) {

        $retorno = array(
            'checked' => false,
            'readonly' => false,
            'isRetiradaReinstalacao' => true
        );

        $idOrdemServicos = array();
        $idOSPrincipal = 0;
        $semAgendamento = false;
        $qtdOrdemServicos = count($ordemServicos);

        if($qtdOrdemServicos < 2) {
            $retorno['isRetiradaReinstalacao'] = false;
            return $retorno;
        }

        foreach ($ordemServicos as $key => $os) {
            $idOrdemServicos[] = $os['ordoid'];

            if($os['principal']){
                $duasOS['principal']['ordoid'] = $os['ordoid'];
                $duasOS['principal']['ostoid'] = $os['ostoid'];
                $duasOS['principal']['data'] = '';
            } else {
                $duasOS['secundaria']['ordoid'] = $os['ordoid'];
                $duasOS['secundaria']['ostoid'] = $os['ostoid'];
                $duasOS['secundaria']['data'] = '';
            }

        }

        $dadosAgendamento = $this->dao->verificaSituacaoAgendamento($idOrdemServicos);

        if(empty($dadosAgendamento)){
            $semAgendamento =  true;
        } else {

            foreach ($dadosAgendamento as $valor) {

                if($valor->osaordoid == $duasOS['principal']['ordoid']) {
                    $duasOS['principal']['data'] = $valor->osadata;

                } else if ($valor->osaordoid == $duasOS['secundaria']['ordoid']){
                    $duasOS['secundaria']['data'] = $valor->osadata;
                }

            }
        }


        if($semAgendamento) {

            // OS Principal Retirada, OS Secundaria Reinstalacao, OS Secundaria NAO AGENDADA
            if ( $duasOS['principal']['ostoid'] == 3 && ($duasOS['secundaria']['ostoid'] == 2 || $duasOS['secundaria']['ostoid'] == 9) ) {
                $retorno['checked'] = false;
                $retorno['readonly'] = false;
            }

            // OS Principal Reinstalacao, OS Secundaria Retirada, OS Secundaria NAO AGENDADA
            if ( ($duasOS['principal']['ostoid'] == 2 || $duasOS['principal']['ostoid'] == 9) && $duasOS['secundaria']['ostoid'] == 3) {
                $retorno['checked'] = true;
                $retorno['readonly'] = true;
            }

        } else {

            // OS Principal Retirada, OS Secundaria Reinstalacao, OS Secundaria AGENDADA
            if ($duasOS['principal']['ostoid'] == 3
                && ($duasOS['secundaria']['ostoid'] == 2 || $duasOS['secundaria']['ostoid'] == 9)
                && $duasOS['secundaria']['data'] != '') {

                // AGENDADA mesmo dia
                if($duasOS['principal']['data'] == $duasOS['secundaria']['data']){
                    $retorno['checked'] = true;
                    $retorno['readonly'] = true;
                } else {

                    if($duasOS['principal']['data'] != ''){
                        $retorno['checked'] = true;
                        $retorno['readonly'] = true;
                    } else{
                        $retorno['checked'] = true;
                        $retorno['readonly'] = false;
                    }

                }
            }

            // OS Principal Reinstalacao, OS Secundaria Retirada, OS Secundaria AGENDADA
            if ( ($duasOS['principal']['ostoid'] == 2 || $duasOS['principal']['ostoid'] == 9)
                && $duasOS['secundaria']['ostoid'] == 3
                && $duasOS['secundaria']['data'] != '') {

                // AGENDADA mesmo dia
                if($duasOS['principal']['data'] == $duasOS['secundaria']['data']){
                    $retorno['checked'] = true;
                    $retorno['readonly'] = false;
                } else {
                    $retorno['checked'] = false;
                    $retorno['readonly'] = false;
                }
            }

        }

        return $retorno;

    }

    public function detalheDisponibilidadeEndereco($parametros)
    {
        $pesquisaMelhorData = true;
        $dataBase = new DateTime();

        $agendamentoVO = new AgendamentoVO();
        $agendamentoVO->addWorkSkill($this->smartAgenda->getWorkSkills($parametros->id,false))
            ->addWorkSkillConsumo($this->smartAgenda->getWorkSkills($parametros->id,true))
            ->setCodigoEstado($parametros->comp_end_estado)
            ->setIdEstado($parametros->comp_end_id_estado)
            ->setCodigoCidade(str_pad($parametros->comp_end_id_cidade, 8, '0', STR_PAD_LEFT))
            ->setCidade(removeAcentos($parametros->comp_end_cidade))
            ->setCodigoBairro($this->smartAgenda->getBairroMapeado($parametros->comp_end_id_bairro, $parametros->comp_end_id_cidade, $parametros->comp_end_id_estado))
            ->setIdBairro($parametros->comp_end_id_bairro)
            ->setBairro(removeAcentos($parametros->comp_end_bairro))
            ->setCEP($parametros->comp_end_cep)
            ->setLogradouro(removeAcentos($parametros->comp_end_logradouro))
            ->setNumero($parametros->comp_end_numero)
            ->setComplemento(removeAcentos($parametros->comp_end_complemento))
            ->setReferencia(removeAcentos($parametros->comp_end_referencia))
            ->setSemanasCalendario($this->totalSemanasCalendario)
            ->setAtendimentoEmergencial(isset($parametros->atendimento_emergencial))
            ->setHorainicioAgendamento(date('H:i'));


        // Adiciona as OSs
        foreach ($this->view->ordemServicos as $key => $ordemServico) {
            $ordemServicoPeso = 0;

            foreach ($ordemServico['servicos'] as $key => $servico) {
                $ordemServicoPeso += (int)$servico['otipeso'];
            }

            // O OFSC não aceita Peso de atividade 0
            $ordemServicoPeso = ($ordemServicoPeso) ? $ordemServicoPeso : 1;

            if($parametros->retirada_reinstalacao) {
                $agendamentoVO->addOrdemServico($ordemServico['ordoid'], $ordemServico['ostgrupo'], $ordemServico['agccodigo'], $ordemServicoPeso);

            } else {


                if (isset($ordemServico['principal'])) {

                    $agendamentoVO->addOrdemServico($ordemServico['ordoid'], $ordemServico['ostgrupo'], $ordemServico['agccodigo'], $ordemServicoPeso);

                    if( $ordemServico['ostgrupo'] == 'RI') {

                        $isReagendamento = ($parametros->operacao == 'reagendar');

                        $agendamentoExtra = $this->dao->buscarOSAdicional($ordemServico['ordconnumero'], $ordemServico['ordoid'], $isReagendamento);

                        if(!empty($agendamentoExtra['ordoid'])) {

                            //verificar agendamento da retirada
                            $dadosAgenda = $this->agenda->getDadosAgendamento($agendamentoExtra['ordoid'], 'ORDEM_SERVICO');

                            //Quando nao nao for no memso dia, a Reinstalacao deve ocorrer um dia apos a Retirada
                            $dataBase = date('Y-m-d', strtotime($dadosAgenda["osadata"] . "+1 day" ) );
                            $dataBase = new DateTime($dataBase);
                        }

                    }

                } else {

                    //verificar agendamento da retirada
                    $dadosAgenda = $this->agenda->getDadosAgendamento($ordemServico['ordoid'], 'ORDEM_SERVICO');

                    //Quando nao for no mesmo dia, a Reinstalacao deve ocorrer um dia apos a Retirada
                    $dataBase = date('Y-m-d', strtotime($dadosAgenda["osadata"] . "+1 day" ) );
                    $dataBase = new DateTime($dataBase);

                }
            }

            // Adiciona o representante (direcionado)
            if(isset($ordemServico['principal']) && !empty($ordemServico['id_representante'])) {
                $agendamentoVO->setPrestadorDirecionado($ordemServico['id_representante']);
                $pesquisaMelhorData = false;
            }
        }

        $agendamentoVO->setDataInicialAgenda($dataBase);

        // Verifica se é atendimento emergencial
        if(isset($parametros->atendimento_emergencial)) {
            $agendamentoVO->setAtendimentoEmergencial(true);
            $pesquisaMelhorData = false;
        }

        // Verifica se existem 2 OSs (1 de retirada e 1 de reinstalação)
        if($parametros->retirada_reinstalacao === 'false') $parametros->retirada_reinstalacao = false;
        $agendamentoVO->setRetiradaReinstalacao($parametros->retirada_reinstalacao);

        // Pesquisa melhor data
        if($pesquisaMelhorData) {
            $melhorData = $this->dao->getMelhorData($parametros->comp_end_cidade);
            if(count($melhorData) > 0) {
                $agendamentoVO->setMelhorDia($melhorData);
            }
        }

        try {
            $dataInicial = clone $agendamentoVO->getDataInicialAgenda();
            $this->analisaPrestadorEstoque($agendamentoVO);


            //se a data inicial for maior que a data do estoque, assume a maior data
            if($dataInicial > $agendamentoVO->getDataInicialAgenda()){
                $agendamentoVO->setDataInicialAgenda($dataInicial);
            }


        } catch (Exception $e) {
            if($this->integracao) {
                return array('erro' => self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            $this->view->mensagemErro = self::MENSAGEM_ERRO_PROCESSAMENTO."(".$e->getMessage().")";
        }

        $_SESSION['agendamentos'][$parametros->id] = serialize($agendamentoVO);

        if (isset($this->view->mensagemAlerta) && !empty($this->view->mensagemAlerta)) {
            $parametros->etapa = 'info';
            if($this->integracao) {
                return array('erro' => $this->view->mensagemAlerta);
            }
            return false;
        }
        return true;
    }

    /**
     * Consulta prestador pelo skill e estoque com data inicial agenda
     */
    private function analisaPrestadorEstoque($agendamentoVO){

        $existeRepresentante = true;
        $encontradoEstoque   = false;
        $semAgendaSkill      = false;
        $dataInicial         = strtotime(date('Y-m-d'));


        // Consulta capacidade no OFSC
        $capacidadeOFSC = $this->getCapacidadeOFSC($agendamentoVO, $this->totalSemanasIntervalo);

        if( !empty($capacidadeOFSC['erro']) && $capacidadeOFSC['erro'] == 14 ) {
            $existeRepresentante = false;
        }

        if (count($capacidadeOFSC['slots']) == 0) {
            $semAgendaSkill = true;
        }

        if (!$existeRepresentante) {
            $this->view->requisicaoBackoffice = true;
            $this->view->mensagemAlerta = self::MENSAGEM_FALTA_PRESTADOR;

        } else if ($semAgendaSkill) {
            $this->view->mensagemAlerta = self::MENSAGEM_SEM_AGENDA_SKILL;

        } else {

            if ($agendamentoVO->isAtendimentoEmergencial()) {
                $encontradoEstoque = true;

            } else {

                $dataDisponivel             = strtotime( date('Y-m-d') );
                $dataOrigem                 = strtotime( date('Y-m-d') );
                $capacidadeDatas            = array();
                $isPrimeiraData             = true;

                $estoqueDisponivelPrestador = $this->getDisponibilidadeEstoque($agendamentoVO, $capacidadeOFSC);
                $agendamentoVO->setEstoqueDisponivelPrestador($estoqueDisponivelPrestador);

                if( empty($estoqueDisponivelPrestador) ) {
                    $encontradoEstoque = false;
                } else {

                    $encontradoEstoque = true;

                    //Definir data de inicio do calendario com base na data de disponibilidade de estoque
                    foreach ($capacidadeOFSC['slots'] as $slot) {

                        $idPrestador = preg_replace('/\D+/', '', $slot->location);

                        if( isset($capacidadeDatas[$slot->date][$idPrestador]) ){
                            continue;
                        }

                        $capacidadeDatas[$slot->date][$idPrestador] = $idPrestador;
                        $dataDisponivel = $estoqueDisponivelPrestador[$idPrestador]['data'];
                        $dataInicial = ($isPrimeiraData) ? $dataDisponivel : $dataInicial;
                        $isPrimeiraData = false;

                        if ($dataDisponivel >= $dataOrigem) {
                            if($dataDisponivel <=  $dataInicial) {
                                $dataInicial = $dataDisponivel;
                            }
                        }
                    }
                }
            }

            if ($encontradoEstoque) {
                $data = new DateTime();
                $data->setTimestamp($dataInicial);
                $agendamentoVO->setDataInicialAgenda($data);

            } else {
                $this->view->notificarOS = true;
                $this->estoqueAgenda->solicitacaoCritica();
                $this->view->mensagemAlerta = self::MENSAGEM_FALTA_ESTOQUE;
                if($this->integracao) {
                    return array('erro' => self::MENSAGEM_FALTA_ESTOQUE);
                }
            }
        }

    }

    private function getDisponibilidadeEstoque($agendamentoVO, $capacidade) {

        $totalFaltaCritica          = 0;
        $listaPrestadores           = array();
        $estoqueDisponivelPrestador = array();

        /*
            Recupera os prestadores retornados pelo OFSC
         */
        if(  isset($capacidade['slots']) ) {

            foreach ($capacidade['slots'] as $slot) {

                $idPrestador = preg_replace('/\D+/', '', $slot->location);

                if( !in_array($idPrestador, $listaPrestadores) ){
                    $listaPrestadores[] = $idPrestador;
                }
            }

        } else {


            foreach ($capacidade as $data => $dados) {

                if (!$dados['permite_agendamento']) {
                    continue;
                }

                foreach ($dados['prestadores'] as $prestador) {
                    $idPrestador = $prestador['repoid'];

                    if( !in_array($idPrestador, $listaPrestadores) ){
                        $listaPrestadores[] = $idPrestador;
                    }
                }
            }
        }

        foreach ($listaPrestadores as $key => $codigoPrestador) {

            foreach ($agendamentoVO->getOrdemServico() as $ordemServico) {

                //Ordem de ReTirada nao considera estoque
                if($ordemServico['tipo'] == 'RT'){
                    $estoqueDisponivelPrestador[$codigoPrestador]['data'] = strtotime( date('Y-m-d') );
                    continue;
                }

                $this->estoqueAgenda->setNumeroOrdemServico( $ordemServico['id'] );
                $this->estoqueAgenda->setCodigoPrestador( $codigoPrestador );
                $resultado = $this->estoqueAgenda->getEstoqueDisponivel();

                if( $resultado['status'] == 'alerta_cliente_premium' ){
                    throw new Exception( 'alerta_cliente_premium');
                } else if ($resultado['status'] == 'falta_critica') {
                    $totalFaltaCritica++;
                } else if ($resultado['status'] == 'erro') {
                    throw new Exception( $this->error->getErro('0009') );
                } else {
                    $estoqueDisponivelPrestador[$codigoPrestador]['data'] = strtotime($resultado['data']);
                }
            }
        }

        if ( count($listaPrestadores) === $totalFaltaCritica ) {
            $estoqueDisponivelPrestador = array();
        }

        return $estoqueDisponivelPrestador;
    }


    public function analiseBackoffice() {

        $parametros = $this->tratarParametros();

        // Quando não existe agendamento configurado
        if (!isset($parametros->id) && !isset($_SESSION['agendamentos'][$parametros->id])) {
            exit('ERRO');
        }

        // Recupera o VO de agendamento
        $agendamentoVO = unserialize($_SESSION['agendamentos'][$parametros->id]);

        // Busca os dados da ordem de serviço
        $ordemServico = $this->dao->getOrdemServico($parametros->id);

        $dadosBackoffice = array(
            'bacclioid' => $ordemServico['clioid'],
            'bacplaca' => $ordemServico['veiplaca'],
            'bacusuoid_atendente' => $this->usuarioLogado,
            'bactpcoid' => $ordemServico['conno_tipo'],
            'bacfone' => ($ordemServico['clitipo'] == 'J') ? $ordemServico['clifone_com'] : $ordemServico['clifone_res'],
            'baccpf_cnpj' => ($ordemServico['clitipo'] == 'J') ? $ordemServico['clino_cgc'] : $ordemServico['clino_cpf'],
            'bacbmsoid' => 23,
            'bacdetalhamento_solicitacao' => "Cidade e bairro não mapeado pela Sascar para atendimento da ordem de serviço {$parametros->id}",
            'bacestoid' => $agendamentoVO->getIdEstado(),
            'bacclcoid' => (int) $agendamentoVO->getCodigoCidade(),
        );

        include_once _MODULEDIR_ . 'SmartAgenda/Action/Agenda.php';

        $agenda = new Agenda();

        try {
            $agenda->salvarAnaliseBackoffice($dadosBackoffice);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_BACKOFFICE;
            unset($_SESSION['agendamentos'][$parametros->id]);
        } catch (Exception $e) {
            $this->view->mensagemErro = $this->error->getErro('0004');
        }
        $this->detalhe();
    }


    public function notificarOS() {

        $parametros = $this->tratarParametros();

        // Quando não existe agendamento configurado
        if (!isset($parametros->id) && !isset($_SESSION['agendamentos'][$parametros->id])) {
            exit('ERRO');
        }

        // Recupera o VO de agendamento
        $agendamentoVO = unserialize($_SESSION['agendamentos'][$parametros->id]);

        foreach ($agendamentoVO->getOrdemServico() as $ordemServico) {

            if ($ordemServico['tipo'] != 'RT') {
                $idOrdemServico = $ordemServico['id'];
            }
        }

        $statusHistorico = $this->ordemServico->retornoHistoricoCorretora('Falta equipamento/acessório');

        $this->ordemServico->salvaHistorico(
            $idOrdemServico,
            $this->usuarioLogado,
            'Falha em agendamento para o usuário na data de '.date('d/m/Y').'. Motivo: Falta de estoque.',
            null, null, $statusHistorico
        );
        $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_NOTIFICAR_OS;
        $_POST['retirada_reinstalacao'] = $parametros->marcar;
        $this->detalhe();
    }


    public function detalheAgenda($parametros) {

        // Quando não existe agendamento configurado
        if (!isset($_SESSION['agendamentos'][$parametros->id])) {
            $parametros->etapa = 'info';
            return;
        }

        // Recupera o VO de agendamento
        $agendamentoVO = unserialize($_SESSION['agendamentos'][$parametros->id]);

        // Determina em qual página a agenda deve ser exibida
        $pagina = isset($parametros->pagina) ? $parametros->pagina : 1;
        $pagina = ($pagina < 1) ? 1 : $pagina;

        // Verifica se deve atualizar a página
        if ($agendamentoVO->getPaginaAgenda() != $pagina) {
            $agendamentoVO->setPaginaAgenda($pagina);
            $_SESSION['agendamentos'][$parametros->id] = serialize($agendamentoVO);
        }

        try {

            if ($agendamentoVO->isAtendimentoEmergencial()) {
                //Calendario baseado no minimo de semanas
                $capacidadeOFSC      = $this->getCapacidadeOFSC($agendamentoVO, $this->totalSemanasCalendario);
                $capacidadeCalculada = $this->getCapacidadeCalculada($capacidadeOFSC, $agendamentoVO);
                $capacidadeOrdenada  = $this->getCapacidadeOrdenada($capacidadeCalculada, $agendamentoVO);
                $capacidadeFinal     = $capacidadeOrdenada;
            } else {

                /*
                    calcular Calendario baseado no maximo de semanas para fins de disponibilidade
                    por todo range de datas
                 */
                $capacidadeOFSCmaximo      = $this->getCapacidadeOFSC($agendamentoVO, $this->totalSemanasIntervalo);
                $capacidadeCalculadaMaximo = $this->getCapacidadeCalculada($capacidadeOFSCmaximo, $agendamentoVO);
                $capacidadeOrdenadaMaximo  = $this->getCapacidadeOrdenada($capacidadeCalculadaMaximo, $agendamentoVO);
                $isCotaDisponivel          = $this->controleCapacidade->verificarDisponiblidadeCota($capacidadeOrdenadaMaximo, NULL);

                if( ! $isCotaDisponivel ) {
                    if($this->integracao) {
                        return array('erro' => sprintf (self::MENSAGEM_AGENDA_INDISPONIVEL, ($this->totalSemanasIntervalo * 7) ));
                    }
                    $parametros->etapa = 'info';
                    $this->view->mensagemAlerta = sprintf (self::MENSAGEM_AGENDA_INDISPONIVEL, ($this->totalSemanasIntervalo * 7) );
                    return;
                }

                /* Problema 2576 - Erro ao retornar os dados do calendário - Primeira data indisponível */
                $nrMaxTentativas = round($this->totalSemanasIntervalo / 2);
                $nrTentativas    = 0;
                do {
                    $nrTentativas++;
                    $capacidadeOFSC = $this->getCapacidadeOFSC($agendamentoVO, $this->totalSemanasCalendario);

                    //Verifica se retornou cota
                    $isCotaDisponivel = false;
                    if (count($capacidadeOFSC['slots']) > 0){
                        $isCotaDisponivel = true;
                    }

                    //Se não retornou pula para a próxima página (add +14 dias)
                    if( ! $isCotaDisponivel ) {
                        $data = $agendamentoVO->getDataInicialAgenda();
                        $data->add(new DateInterval("P14D"));

                        $agendamentoVO->setDataInicialAgenda($data);
                        $_SESSION['agendamentos'][$parametros->id] = serialize($agendamentoVO);
                    }

                } while (!$isCotaDisponivel && $nrTentativas <= $nrMaxTentativas);

                //$capacidadeOFSC            = $this->getCapacidadeOFSC($agendamentoVO, $this->totalSemanasCalendario);
                $capacidadeCalculada       = $this->getCapacidadeCalculada($capacidadeOFSC, $agendamentoVO);
                $capacidadeOrdenada        = $this->getCapacidadeOrdenada($capacidadeCalculada, $agendamentoVO);
                $consideraEstoque    = 0;

                foreach ($agendamentoVO->getOrdemServico() as $ordemServico) {
                    //Ordem de ReTirada nao considera estoque
                    if($ordemServico['tipo'] == 'RT'){
                        continue;
                    }
                    $consideraEstoque++;
                }

                if($consideraEstoque > 0) {
                    $testeCapacidadeEstoqueMaximo = $this->getEstoqueCalculado($capacidadeOrdenadaMaximo, $agendamentoVO);

                    if( empty($testeCapacidadeEstoqueMaximo) ){
                        $parametros->etapa = 'info';
                        return;
                    }

                    $capacidadeEstoqueValidado = $this->getEstoqueCalculado($capacidadeOrdenada, $agendamentoVO);


                } else {
                    $capacidadeEstoqueValidado = $capacidadeOrdenada;
                }

                $capacidadeFinal = $this->filtraDataDisponivel($capacidadeEstoqueValidado);

                foreach ($agendamentoVO->getMelhorDia() as $prestadorMelhorDia => $timestamps) {
                    foreach ($timestamps as $timestamp) {
                        $data = date('Y-m-d', $timestamp);
                        if (isset($capacidadeFinal[$data]) && $capacidadeFinal[$data]['permite_agendamento'] && $capacidadeFinal[$data]['prestadores']['PS'.$prestadorMelhorDia]) {
                            $capacidadeFinal[$data]['melhor_data'] = true;
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $parametros->etapa = 'info';

            if( $e->getMessage() == 'alerta_cliente_premium' ) {
                $this->view->mensagemAlerta = self::MENSAGEM_CLIENTE_PREMIUM;
                $this->view->mensagemErro = '';
            } else {
                $this->view->mensagemErro = self::MENSAGEM_ERRO_PROCESSAMENTO."(".$e->getMessage().")";
            }

            if($this->integracao) {
                return array('erro' => self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            return;
        }

        if($this->integracao) {
            $diasSemana = $this->getDiasSemana(
                clone $agendamentoVO->getDataCalculoAgenda()
            );
            return array(
                'diasSemana' => $diasSemana,
                'tempoAtividade' => $this->view->tempoAtividade,
                'agenda' => $capacidadeFinal
            );
        }

        $this->view->pagina = $pagina;
        $this->view->agenda = array_chunk($capacidadeFinal, 7, true);
        $this->view->diasSemana = $this->getDiasSemana(
            clone $agendamentoVO->getDataCalculoAgenda()
        );
        $this->view->contato = $this->dao->getDadosEmailSms($parametros->id);
    }


    protected function getDiasSemana($data){
        $listaDiasSemana = array(
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            6 => 'Sábado',
            7 => 'Domingo'
        );

        for ($i = 0; $i < 7; $i++) {
            $diasSemana[] = $listaDiasSemana[$data->format('N')];
            $data->add(new DateInterval('P1D'));
        }
        return $diasSemana;
    }


    protected function getCapacidadeOFSC($agendamentoVO, $semanas) {

        include_once _MODULEDIR_ . 'SmartAgenda/Action/Capacity.php';

        $capacidade = array(
            'atividades' => array(),
            'slots' => array(),
            'dataInicial' => clone $agendamentoVO->getDataCalculoAgenda(),
            'erro' => 0
        );

        // Define o tamanho da consulta de capacidade
        $totalDiasAgenda = $agendamentoVO->isAtendimentoEmergencial() ? 2 : ($semanas * 7);

        // STI 86505 - Pega parametrizações relativas ao tempo de atividade do OFSC
        $paramDuracao = $this->smartAgenda->parametrosSmartAgenda(array(
            'DURACAO_PADRAO_ATIVIDADE_OFSC',
            'CONSIDERA_TEMPO_ATIVIDADE_OFSC',
            'FATOR_CALCULO_TEMPO_PESO'));

        foreach ($agendamentoVO->getOrdemServico() as $ordemServico) {
            // Verifica se deve buscar o conjunto completo de dias
            $processarSlots = !$agendamentoVO->isRetiradaReinstalacao()
                || $agendamentoVO->isAtendimentoEmergencial()
                || ($ordemServico['tipo'] == 'RI');

            $capacity = new Capacity();
            $capacity->setXA_COUNTRY_CODE('BR');
            $capacity->setXA_WO_TYPE($ordemServico['tipo']);
            $capacity->setXA_WO_GROUP($ordemServico['grupo']);
            $capacity->setXA_WO_REASON($ordemServico['dificuldade']);
            $capacity->setDate($agendamentoVO->getDataCalculoAgenda()->format('Y-m-d'));

            $prestadorAgendamento = $agendamentoVO->getPrestadorAgendamento();
            $prestadorDirecionado = $agendamentoVO->getPrestadorDirecionado();

            //Quando re-consulta na confirmacao do agendamento
            if( !is_null($prestadorAgendamento) ) {
                $capacity->setLocation('PS' . $prestadorAgendamento);
            }

            if ( !is_null($prestadorDirecionado) ) {
                $capacity->setLocation('PS' .$prestadorDirecionado);
                $capacity->setXA_STATE_CODE('XX');
                $capacity->setXA_CITY_CODE('99999999');
                $capacity->setXA_NEIGHBORHOOD_CODE('99999999');

            } else {
                $capacity->setXA_STATE_CODE($agendamentoVO->getCodigoEstado());
                $capacity->setXA_CITY_CODE($agendamentoVO->getCodigoCidade());
                $capacity->setXA_NEIGHBORHOOD_CODE($agendamentoVO->getCodigoBairro());
            }

            foreach ($agendamentoVO->getWorkSkill() as $workSkill) {
                $capacity->setWorkSkill($workSkill);
            }


            if ($processarSlots) {
                $data = clone $agendamentoVO->getDataCalculoAgenda();

                for ($contador=1; $contador<$totalDiasAgenda; $contador++) {
                    $data->add(new DateInterval('P1D'));
                    $capacity->setDate($data->format('Y-m-d'));
                }
            }

            $requisicao = $capacity->getCapacity();

            if (isset($requisicao['erro'])) {
                if (in_array($requisicao['detail']->errorCode, array(14))) {
                    $capacidade['erro'] = 14;
                    return $capacidade;
                }
                throw new Exception( $this->error->getErro('0010') );
            }

            //problema 2668
            if(is_null($prestadorAgendamento) && isset($requisicao['dados']['activity_duration'])) {
                // Seta o tempo inicial retornado pelo OFSC (activity_duration)
                $agendamentoVO->setDuracaoInicialAtividade($requisicao['dados']['activity_duration']);
                $agendamentoVO->setTempoDeslocamentoInicial($requisicao['dados']['activity_travel_time']);
            } else if (!is_null($prestadorAgendamento)) {
                $requisicao['dados']['activity_duration'] = !is_null($agendamentoVO->getDuracaoInicialAtividade()) ? $agendamentoVO->getDuracaoInicialAtividade() : $requisicao['dados']['activity_duration'];
                $requisicao['dados']['activity_travel_time'] = !is_null($agendamentoVO->getTempoDeslocamentoInicial()) ? $agendamentoVO->getTempoDeslocamentoInicial() : $requisicao['dados']['activity_travel_time'];
            }
            //fim problema 2668

            //STI 86505
            $activityDuration = $this->smartAgenda->duracaoAtividade(
                $paramDuracao,
                $ordemServico,
                $requisicao['dados']['activity_duration']
            );

            $capacidade['atividades'][$ordemServico['id']] = array(
                'activity_duration' => $activityDuration,
                'activity_travel_time' => $requisicao['dados']['activity_travel_time']
            );
            //FIM STI 86505

            if ($processarSlots) {
                if (isset($requisicao['dados']['capacity'])) {
                    foreach ($requisicao['dados']['capacity'] as $slot) {
                        $capacidade['slots'][] = (object) $slot;
                    }
                }
            }
        }

        return $capacidade;
    }


    public function getCapacidadeCalculada($capacidade, $agendamentoVO) {

        include_once _MODULEDIR_ . 'SmartAgenda/Action/ControleCapacidade.php';

        $this->controleCapacidade = new ControleCapacidade($this->dao->getConn());
        $this->controleCapacidade->setIntervalo($this->totalSemanasCalendario*7);
        $this->controleCapacidade->setAtendimentoEmergencial($agendamentoVO->isAtendimentoEmergencial());
        $this->controleCapacidade->setDataInicial($capacidade['dataInicial']);
        $this->controleCapacidade->setAtividades($capacidade['atividades']);
        $this->controleCapacidade->setSlots($capacidade['slots']);
        $this->controleCapacidade->setHorainicioAgendamento($agendamentoVO->getHorainicioAgendamento());

        // Envia para a view o tempo total de atividades
        $this->view->tempoAtividade = date(
            'H:i', mktime(0, $this->controleCapacidade->getAtividadeDuracao())
        );

        return $this->controleCapacidade->getCapacidade();
    }

    /**
     * Ordena os slots seguindo a configuração de ordenação, para ordenar os
     * resultados utiliza um algoritmo de ordenação por score, a regra é:
     *
     * - Quanto menor o score do slot, mais no topo ele vai aparecer
     *
     * Para implementar essa lógica foi levado em consideração que quanto maior
     * a prioridade de um tipo de ordenação (Melhor Data, Ponto Fixo,
     * Ponto Móvel CLT e Ponto Móvel Terceiro) menos pontos são somados ao
     * score dos slots que se encaixarem nesses tipos.
     */
    public function getCapacidadeOrdenada(array $capacidade, $agendamentoVO) {

        $ordenacao = array();
        $melhorData = array();
        $agenda = array();
        $representantes = array();

        $tipoAtendimento = ($agendamentoVO->isAtendimentoEmergencial()) ? 'E' : 'N';

        // Organiza as formas de ordenação
        foreach ($this->smartAgenda->getOrdenacaoPrestadores('U', $tipoAtendimento) as $chave => $ordem) {
            $ordenacao[$ordem['ottchave']] = ($chave * 10000);
        }

        // Organiza a melhor data
        foreach ($agendamentoVO->getMelhorDia() as $prestador => $timestamps) {
            foreach ($timestamps as $timestamp) {
                $melhorData["PS{$prestador}"][date('Y-m-d', $timestamp)] = true;
            }
        }

        // Pega as informações de todos os prestadores disponíveis
        foreach ($capacidade as $data => $prestadores) {
            $agenda[$data] = array(
                'permite_agendamento' => false,
                'data' => new DateTime($data)
            );
            foreach ($prestadores as $prestador => $tipos) {
                if (isset($representantes[$prestador])) {
                    continue;
                }

                // Busca as informalções do prestador no banco de dados
                $representantes[$prestador] = $this->dao->getTipoRepresentante(
                    preg_replace('/[\D]/', '', $prestador)
                );
            }
        }

        // Percorre slots para fazer a organização dos slot e calcular o score
        foreach ($capacidade as $data => $prestadores) {
            foreach ($prestadores as $prestador => $tipos) {
                foreach ($tipos as $tipo => $slots) {
                    preg_match_all(
                        '/(?<grupo>.*)\s(?<ponto>FIXO|MOVEL)/', $tipo, $matches
                    );

                    $grupo = trim(current($matches['grupo']));
                    $ponto = trim(current($matches['ponto']));

                    foreach ($slots as $hora => $info) {
                        // Redefine o score para o slot
                        $score = 0;

                        // Determina o tipo de atedimento, FIXO ou MOVEL
                        if ($ponto == 'FIXO') {
                            $score += $ordenacao['PF'];
                        } else {
                            // Determina se o prestador é CLT ou TERCEIRO
                            if ($representantes[$prestador]['reptiproid'] == 4) {
                                $score += $ordenacao['PMCLT'];
                            } else {
                                $score += $ordenacao['PMTERC'];
                            }

                            // Verifica se já existe melhor data
                            if (isset($melhorData[$prestador][$data])) {
                                $score -= $score;
                            }
                        }

                        // Soma o horário do slot ao score
                        $score += (int) preg_replace('/[\D]/', '', $hora);

                        // Guarda o resultado do score no slot e adiciona a agenda
                        $info['grupo'] = $grupo;
                        $info['ponto'] = $ponto;
                        $info['info_prestador'] = $representantes[$prestador];
                        $info['score'] = $score;

                        $agenda[$data]['prestadores'][$prestador] = $representantes[$prestador];
                        $agenda[$data]['slots'][] = $info;

                        if ($info['permite_agendamento']) {
                            $agenda[$data]['permite_agendamento'] = true;
                        }
                    }
                }
            }
        }


        // Ordena os resultados dos slots por dia
        foreach ($agenda as $data => $dados) {
            if (isset($dados['slots'])) {
                usort($agenda[$data]['slots'], 'ordenarResultadoSlots');
            }
        }

        return $agenda;
    }


    public function getEstoqueCalculado($capacidade, $agendamentoVO) {

        $dataDisponivel  = strtotime( date('Y-m-d') );
        $retornoFuncao   = array();

        $estoqueDisponivelPrestador = $this->getDisponibilidadeEstoque($agendamentoVO, $capacidade);

        if( empty($estoqueDisponivelPrestador) ) {

            foreach ($capacidade as $data => $dados) {
                $capacidade[$data]['permite_agendamento'] = false;
                unset($capacidade[$data]['prestadores'], $capacidade[$data]['slots']);
            }

            $this->view->notificarOS = true;
            $this->estoqueAgenda->solicitacaoCritica();
            $this->view->mensagemAlerta = self::MENSAGEM_FALTA_ESTOQUE;

        } else {

            foreach ($capacidade as $data => $dados) {

                $dataAtual = strtotime($data);

                if (!$dados['permite_agendamento']) {
                    continue;
                }

                foreach ($dados['prestadores'] as $prestador) {

                    $codigoPrestador     = $prestador['repoid'];
                    $codidoPrestadorOFSC = 'PS' . $codigoPrestador;
                    $dataDisponivel      = $estoqueDisponivelPrestador[$codigoPrestador]['data'];

                    if( ($dataAtual < $dataDisponivel) || empty($dataDisponivel) ) {

                        //Remover todos Time-slots do prestador
                        foreach ($dados['slots'] as $chave => $slot) {
                            if ( $slot['representante'] == $codidoPrestadorOFSC ) {
                                unset($capacidade[$data]['slots'][$chave]);
                            }
                        }
                        //Remover prestador
                        unset($capacidade[$data]['prestadores'][$codidoPrestadorOFSC]);
                    }

                }
            }

            $retornoFuncao = $capacidade;

        }

        return $retornoFuncao;
    }


    private function filtraDataDisponivel($capacidadeEstoqueValidado) {

        foreach ($capacidadeEstoqueValidado as $data => $dados) {

            $contadorDataSlot = 0;
            $contadorSlotDisponivel = 0;

            if( isset($dados['slots']) ){

                foreach ($dados['slots'] as $chave => $slot) {

                    $contadorDataSlot++;

                    if ( $slot['permite_agendamento'] == false ) {
                        $contadorSlotDisponivel++;
                    }
                }
                if(strtotime($data) < strtotime($this->estoqueAgenda->dataLimiteEstoqueTempoPreparacao)) {
                    $capacidadeEstoqueValidado[$data]['permite_agendamento'] = true;
                }
                if( $contadorDataSlot === $contadorSlotDisponivel ) {
                    $capacidadeEstoqueValidado[$data]['permite_agendamento'] = false;
                    unset($capacidadeEstoqueValidado[$data]['prestadores'], $capacidadeEstoqueValidado[$data]['slots']);
                }

            }

        }

        return $capacidadeEstoqueValidado;
    }


    public function salvar($parametros) {
        // Quando não existe agendamento configurado
        if (!isset($_SESSION['agendamentos'][$parametros->id])) {
            $parametros->etapa = 'info';
            return;
            // Quando não foi selecionado um timeslot
        } else if (!isset($parametros->cmp_time_slot)) {
            $parametros->etapa = 'agenda';
            return;
        }


        // Mapeia o valor do slot escolhido
        list($data, $timeslot, $prestador, $categoria, $ponto) = explode(
            '/', $parametros->cmp_time_slot
        );

        $agendamentoVO        = unserialize($_SESSION['agendamentos'][$parametros->id]);
        $isEmergencial        = $agendamentoVO->isAtendimentoEmergencial();
        $idPrestador          = preg_replace('/[\D]/', '', $prestador);
        $prestadorDirecionado = $agendamentoVO->getPrestadorDirecionado();
        $totalOrdens          = count($this->view->ordemServicos);
        $totalVO              = count($agendamentoVO->getOrdemServico());
        $statusHistorico      = $this->ordemServico->retornoHistoricoCorretora('Inst/Assist. Agendada');
        $workSkillconsumo     = $agendamentoVO->getWorkSkillConsumo();

        $agendamentoVO->setPrestadorAgendamento( $idPrestador );

        /*
            Revalida cota e estoque do prestador selecionado
         */
        $capacidadeOFSC      = $this->getCapacidadeOFSC($agendamentoVO, $this->totalSemanasIntervalo);
        $capacidadeCalculada = $this->getCapacidadeCalculada($capacidadeOFSC, $agendamentoVO);
        $capacidadeOrdenada  = $this->getCapacidadeOrdenada($capacidadeCalculada, $agendamentoVO);

        if ( ! $isEmergencial ) {

            $isCotaDisponivel = $this->controleCapacidade->verificarDisponiblidadeCota($capacidadeOrdenada, $data);

            if( ! $isCotaDisponivel ) {
                $parametros->etapa = 'info';
                if($this->integracao) {
                    return self::MENSAGEM_DATA_INDISPONIVEL;
                }
                $this->view->mensagemAlerta = self::MENSAGEM_DATA_INDISPONIVEL;;
                return;
            }

            // Pega os horarios dispóniveis por dia
            $capacidadeEstoqueValidado = $this->getEstoqueCalculado($capacidadeOrdenada, $agendamentoVO);

            if( empty($capacidadeEstoqueValidado) ){
                $parametros->etapa = 'info';
                if($this->integracao) {
                    return self::MENSAGEM_ESTOQUE_INDISPONIVEL;
                }
                $this->view->mensagemAlerta = self::MENSAGEM_ESTOQUE_INDISPONIVEL;
                return;
            }

        }

        // Retorna as informações do slot(horário no dia) selecionado
        $slot = $this->controleCapacidade->getSlot(
            $data, $prestador, $categoria, $timeslot
        );

        // Busca as informações do prestador no banco de dados
        $representante = $this->dao->getTipoRepresentante(
            preg_replace('/[\D]/', '', $prestador)
        );

        if( $totalOrdens > $totalVO ){
            //Reverte o array para sincronizar os indices dos arrays nos varios metodos das classes
            $this->view->ordemServicos = array_reverse($this->view->ordemServicos);
        }

        foreach ($agendamentoVO->getOrdemServico() as $chave => $ordemServico) {

            // Pega a distribuição do agendamento
            $agendamento = $slot['agendamento'][$chave];

            //Verifica se Time-Slot selecionado ainda esta disponivel
            if( empty($agendamento['slot_agendamento_ofsc']) ) {
                $parametros->etapa = 'info';
                if($this->integracao) {
                    return self::MENSAGEM_HORA_INDISPONIVEL;
                }
                $this->view->mensagemAlerta = self::MENSAGEM_HORA_INDISPONIVEL;
                return;
            }

            // Determina o horário inicial e final do timeslot
            $horario = $this->getHorariosTimeslot(
                $agendamento['slot_agendamento_ofsc']
            );

            $dadosEndereco = array();

            if($ponto == 'FIXO') {
                $dadosEndereco = $this->smartAgenda->revalidarEnderecoAtendimento($idPrestador);
            } else {

                //WORK ZONE
                $dadosEndereco['XA_STATE_CODE']        = $agendamentoVO->getCodigoEstado();
                $dadosEndereco['XA_CITY_CODE']         = $agendamentoVO->getCodigoCidade();
                $dadosEndereco['XA_NEIGHBORHOOD_CODE'] = $agendamentoVO->getCodigoBairro();

                //Endereco Atendimento
                $dadosEndereco['XA_NEIGHBORHOOD_NAME'] = $agendamentoVO->getBairro();
                $dadosEndereco['XA_ADDRESS_REFERENCE'] = $agendamentoVO->getReferencia();
                $dadosEndereco['XA_ADDRESS_2']         = $agendamentoVO->getComplemento();
                $dadosEndereco['address']              = $agendamentoVO->getLogradouro() . ', ' . $agendamentoVO->getNumero();
                $dadosEndereco['city']                 = $agendamentoVO->getCidade();
                $dadosEndereco['state']                = $agendamentoVO->getCodigoEstado();
                $dadosEndereco['zip']                  = $agendamentoVO->getCEP();

                $dadosEndereco['id_estado']            = (int) $agendamentoVO->getIdEstado();
                $dadosEndereco['id_cidade']            = (int) $agendamentoVO->getCodigoCidade();
                $dadosEndereco['id_bairro']            = ( $agendamentoVO->getIdBairro() === '' ? NULL : $agendamentoVO->getIdBairro() );
            }

            if ( !is_null( $prestadorDirecionado ) ) {
                //WORK ZONE
                $dadosEndereco['XA_STATE_CODE']        = 'XX';
                $dadosEndereco['XA_CITY_CODE']         = '99999999';
                $dadosEndereco['XA_NEIGHBORHOOD_CODE'] = '99999999';
            }

            $agendamentos[$chave] = array(
                'osaordoid'           => $ordemServico['id'],
                'osadata'             => $data,
                'osahora'             => $horario['inicial'],
                'osausuoid_incl'      => $this->usuarioLogado,
                'osaplaca'            => $this->view->ordemServicos[$chave]['veiplaca'],
                'osachassi'           => $this->view->ordemServicos[$chave]['veichassi'],
                'osaendereco'         => $dadosEndereco['address'],
                'osaend_complemento'  => $dadosEndereco['XA_ADDRESS_2'],
                'osaend_referencia'   => $dadosEndereco['XA_ADDRESS_REFERENCE'],
                'osaobservacao'       => empty($parametros->cmp_observacoes) ? null : utf8_decode(utf8_encode($parametros->cmp_observacoes)),
                'osatelefone'         => empty($parametros->cmp_contato_celular) ? null : $parametros->cmp_contato_celular,
                'osahora_final'       => $horario['final'],
                'osatime_slot'        => $agendamento['slot_agendamento_ofsc'],
                'osarepoid'           => $idPrestador,
                'osaasaoid'           => 2,
                'osaemergencial'      => $agendamentoVO->isAtendimentoEmergencial(),
                'osaclioid'           => $this->view->ordemServicos[$chave]['clioid'],
                'osaestoid'           => $dadosEndereco['id_estado'],
                'osaclcoid'           => $dadosEndereco['id_cidade'],
                'osacbaoid'           => $dadosEndereco['id_bairro'],
                'osacep'              => $dadosEndereco['zip'],
                'osatipo_atendimento' => ($ponto == 'FIXO') ? 'F' : 'M',
                'osareagendamento'    => ($parametros->operacao == 'reagendar'),
                'osatipo_agendamento' => 'U'
            );

            $enderecos[$chave] = array(
                'osiordoid'         => $ordemServico['id'],
                'osiempresa'        => $this->view->ordemServicos[$chave]['clinome'],
                'osilocioid'        => 4,
                'osichefe_oficina'  => NULL,
                'osiconcessionaria' => NULL,
                'ositelefone'       => NULL,
                'osiender'          => $dadosEndereco['address'],
                'osiptref'          => $dadosEndereco['XA_ADDRESS_REFERENCE'],
                'osiresponsavel'    => empty($parametros->cmp_responsavel) ? null : $parametros->cmp_responsavel,
                'ositelefone_inst'  => empty($parametros->cmp_responsavel_celular) ? null : preg_replace('/\D/', '', $parametros->cmp_responsavel_celular),
                'osicidoid'         => NULL,
                'osibaioid'         => NULL,
                'osizonoid'         => NULL,
                'osiestoid'         => $dadosEndereco['id_estado'],
                'osiclcoid'         => $dadosEndereco['id_cidade'],
                'osicbaoid'         => $dadosEndereco['id_bairro'],
                'osicep'            => $dadosEndereco['zip']
            );

            $contato[$chave] = array(
                'id_os'   => $ordemServico['id'],
                'nome'    => $parametros->cmp_contato,
                'celular' => $parametros->cmp_contato_celular,
                'email'   => $parametros->cmp_contato_email
            );

            $property = array(
                array(
                    'label' => 'XA_WO_NUMBER',
                    'value' => $ordemServico['id']
                ),
                array(
                    'label' => 'XA_COUNTRY_CODE',
                    'value' => 'BR'
                ),
                array(
                    'label' => 'XA_STATE_CODE',
                    'value' => $dadosEndereco['XA_STATE_CODE']
                ),
                array(
                    'label' => 'XA_CITY_CODE',
                    'value' => $dadosEndereco['XA_CITY_CODE']
                ),
                array(
                    'label' => 'XA_NEIGHBORHOOD_CODE',
                    'value' => $dadosEndereco['XA_NEIGHBORHOOD_CODE']
                ),
                array(
                    'label' => 'XA_ADDRESS_2',
                    'value' => $dadosEndereco['XA_ADDRESS_2']
                ),
                array(
                    'label' => 'XA_NEIGHBORHOOD_NAME',
                    'value' => $dadosEndereco['XA_NEIGHBORHOOD_NAME']
                ),
                array(
                    'label' => 'XA_ADDRESS_REFERENCE',
                    'value' => $dadosEndereco['XA_ADDRESS_REFERENCE']
                ),
                array(
                    'label' => 'XA_COUNTRY',
                    'value' => 'Brasil'
                ),
                array(
                    'label' => 'XA_WO_TYPE',
                    'value' => $ordemServico['tipo']
                ),
                array(
                    'label' => 'XA_WO_GROUP',
                    'value' => $ordemServico['grupo']
                ),
                array(
                    'label' => 'XA_WO_REASON',
                    'value' => $ordemServico['dificuldade']
                ),
                array(
                    'label' => 'XA_CONTRACT',
                    'value' => $this->view->ordemServicos[$chave]['ordconnumero']
                ),
                array(
                    'label' => 'XA_CONTRACT_CLASS',
                    'value' => $this->view->ordemServicos[$chave]['eqcoid']
                ),
                array(
                    'label' => 'XA_CONTRACT_CLASS_GROUP',
                    'value' => $this->view->ordemServicos[$chave]['eqcecgoid']
                ),
                array(
                    'label' => 'XA_CONTRACT_MODALITY',
                    'value' => $this->view->ordemServicos[$chave]['conmodalidade']
                ),
                array(
                    'label' => 'XA_CONTRACT_DATE',
                    'value' => date("d/m/Y", strtotime($this->view->ordemServicos[$chave]['condt_cadastro']))
                ),
                array(
                    'label' => 'XA_CONTRACT_EFFECTIVE_DATE',
                    'value' => date("d/m/Y", strtotime($this->view->ordemServicos[$chave]['condt_ini_vigencia']))
                ),
                array(
                    'label' => 'XA_VEHICLE_PLATE',
                    'value' => $this->view->ordemServicos[$chave]['veiplaca']
                ),
                array(
                    'label' => 'XA_VEHICLE_CHASSI',
                    'value' => $this->view->ordemServicos[$chave]['veichassi']
                ),
                array(
                    'label' => 'XA_VEHICLE_MODEL',
                    'value' => $this->view->ordemServicos[$chave]['mlomodelo']
                ),
                array(
                    'label' => 'XA_VEHICLE_YEAR',
                    'value' => $this->view->ordemServicos[$chave]['veino_ano']
                ),
                array(
                    'label' => 'XA_VEHICLE_COLOR',
                    'value' => $this->view->ordemServicos[$chave]['veicor']
                ),
                array(
                    'label' => 'XA_VEHICLE_RENAVAM',
                    'value' => $this->view->ordemServicos[$chave]['veino_renavan']
                ),
                array(
                    'label' => 'XA_CEL_NOTIFICATION',
                    'value' => $parametros->cmp_contato_celular
                ),
                array(
                    'label' => 'XA_EMAIL_NOTIFICATION',
                    'value' => $parametros->cmp_contato_email
                ),
                array(
                    'label' => 'XA_CONTACT_NAME',
                    'value' => $parametros->cmp_contato
                ),
                array(
                    'label' => 'XA_CONTACT_CELL_PHONE',
                    'value' => $parametros->cmp_contato_celular
                ),
                array(
                    'label' => 'XA_SCHEDULING_TYPE',
                    'value' => ($ponto == 'FIXO') ? 'F' : 'M'
                ),
                array(
                    'label' => 'XA_GENERAL_NOTES',
                    'value' => $parametros->cmp_observacoes
                ),
            );

            foreach ($this->view->ordemServicos[$chave]['servicos'] as $numero => $servico) {
                // Adiciona mais um ao número para evitar o zero
                $numero++;

                $propertyItem = array(
                    array(
                        'label' => "XA_SERVICE_ITEM_{$numero}",
                        'value' => $servico['codigo_tipo_os']
                    ),
                    array(
                        'label' => "XA_SERVICE_TYPE_{$numero}",
                        'value' => $servico['id_tipo_os']
                    ),
                    array(
                        'label' => "XA_SERVICE_REASON_{$numero}",
                        'value' => $servico['id_tipo_servico']
                    ),
                    array(
                        'label' => "XA_ALLEGED_DEFECT_{$numero}",
                        'value' => $servico['id_tipo_defeito_alegado']
                    ),
                    array(
                        'label' => "XA_SERVICE_NOTE_{$numero}",
                        'value' => $servico['status']
                    )
                );
                $property = array_merge($property, $propertyItem);
            }

            foreach($property as $key => $value) {
                $property[$key]['value'] = removeAcentos($value['value']);
            }

            if ($agendamentoVO->isAtendimentoEmergencial()) {
                $property[] = array(
                    'label' => 'XA_PRIORITY',
                    'value' => '1'
                );
            }

            $capAtividades = $this->controleCapacidade->getAtividades();
            // STI 86505
            $activityDuration = isset($capAtividades[$chave]['activity_duration']) ?
                $capAtividades[$chave]['activity_duration'] : '';

            if($this->view->ordemServicos[$chave]['ostgrupo'] == 'RI'){
                $labelTipoOrdemServico = 'REINSTALACAO';

            } else {
                $labelTipoOrdemServico = removeAcentos($this->view->ordemServicos[$chave]['ostdescricao']);
            }


            $ofsc[$chave] = array(
                'date'        => $data,
                'type'        => 'update_activity',
                'external_id' => $prestador,
                'appointment' => array(
                    'appt_number'     => 0,
                    'customer_number' => ($this->view->ordemServicos[$chave]['clitipo'] == 'J') ? $this->view->ordemServicos[$chave]['clino_cgc'] : $this->view->ordemServicos[$chave]['clino_cpf'],
                    'duration'        => $activityDuration,
                    'worktype_label'  => $labelTipoOrdemServico,
                    'time_slot'       => $agendamento['slot_agendamento_ofsc'],
                    'name'            => $this->view->ordemServicos[$chave]['clinome'],
                    'address'         => $dadosEndereco['address'],
                    'city'            => $dadosEndereco['city'],
                    'state'           => $dadosEndereco['state'],
                    'zip'             => ($dadosEndereco['zip'] != "")? $this->mask($dadosEndereco['zip'], '#####-###') : "",
                    'properties'      => array(
                        'property'    => $property
                    )
                )
            );

            // Cria os dados que serão salvos para o histórico
            $historico[$chave] = array(
                'idOrdemServico'  => $ordemServico['id'],
                'idUsuario'       => $this->usuarioLogado,
                'mensagem'        => "{$parametros->cmp_observacoes} - Agendado "
                    . "para o prestador de serviço {$representante['repnome']} "
                    . "para ".date_format(date_create($data),"d/m/Y")." às {$horario['inicial']}.",

                'dataAgendamento' => $data,
                'horaAgendamento' => $horario['inicial'],
                'status'          => $statusHistorico
            );
            // Adiciona o link entre as tarefas
            if ($agendamentoVO->isRetiradaReinstalacao()
                && ($this->view->ordemServicos[$chave]['ostgrupo'] == 'RI')) {
                $ofsc[$chave]['appointment']['links'] = array(
                    'link' => array(
                        'appt_number' => 0,
                        'link_type' => 'atividade_2_reinstalacao'
                    )
                );
            }

            $tipoOrdemServico[$chave] = $ordemServico['tipo'];
        }

        $this->dadosEndereco = $dadosEndereco;

        // Iniciando a transação no banco de dados
        $this->dao->begin();

        try {

            if( $parametros->operacao == 'reagendar'){

                $this->cancelarAgendamento($agendamentos, $parametros);
            }

            $this->agendamentoInserir(
                $agendamentos, $enderecos, $ofsc, $historico, $slot, $contato, $agendamentoVO->isAtendimentoEmergencial(), $workSkillconsumo, $tipoOrdemServico, $data
            );

            $this->dao->commit();


        } catch (Exception $e) {
            $this->view->mensagemErroAgenda = self::MENSAGEM_ERRO_PROCESSAMENTO."(".$e->getMessage().")";
            $this->dao->rollback();
        }

        if (!isset($e)) {

            $contexto = ($parametros->operacao == 'reagendar') ? 'REAGENDAMENTO' : 'AGENDAMENTO';

            foreach ($this->view->ordemServicos as $key => $os) {

                $this->enviarNotificacao($os['ordoid'], $contexto, $parametros);

                if ($ponto != 'FIXO') {
                    $this->enviarNotificacao($os['ordoid'], 'DADOS_TECNICOS', $parametros);
                }
            }

            $_SESSION['msgAgendamentoUnitario'] = self::MENSAGEM_SUCESSO_AGENDAMENTO;
            if($this->integracao) {
                return array('status' => 'ok');
            } else {
                header('Location: '._PROTOCOLO_ . _SITEURL_ .'prn_agendamento_unitario.php');
            }
        } else {
            if($this->integracao) {
                return array('erro' => $e->getMessage());
            }
        }
    }


    protected function agendamentoInserir(  $agendamentos,
                                            $enderecos,
                                            $ofsc,
                                            $historico,
                                            $slot,
                                            $contato,
                                            $atendimento_emergencial,
                                            $workSkillconsumo,
                                            $tipoOrdemServico,
                                            $data) {

        include_once _MODULEDIR_ . 'SmartAgenda/Action/Agenda.php';
        include_once _MODULEDIR_ . 'SmartAgenda/Action/Inbound.php';

        $agenda = new Agenda();
        $inbound = new Inbound();

        // Salva o agendamento na intranet
        $idAgendamentos = $agenda->salvarAgendamento(
            $agendamentos, $enderecos, $contato
        );

        foreach ($agendamentos as $valor) {
            //Atualiza o representante
            $retornoAtualizarRepresentante = $this->ordemServico->atualizarRepresentante($valor['osaordoid'], $valor['osarepoid']);

            if (!$retornoAtualizarRepresentante) {
                throw new Exception( $this->error->getErro('0001') );
            }

            $retornoOrdemDirecionada = $this->ordemServico->atualizarDirecionamento($valor['osaordoid']);
        }

        // Salva o agendamento no OFSC
        $inbound->date = date('Y-m-d');

        foreach ($ofsc as $chave => $agendamento) {
            // Atualiza o número do agendamento na intranet
            $agendamento['appointment']['appt_number'] = $idAgendamentos[$chave];

            // Atualiza o número do apontamento da tarefa linkada
            if (isset($agendamento['appointment']['links'])) {
                $agendamento['appointment']['links']['link']['appt_number'] = $idAgendamentos[0];
            }
            $inbound->setCommands($this->converterUTF8OFSC($agendamento));
        }

        $resultado = $inbound->entrada();

        if (!$resultado['resultado']) {
            throw new Exception( $this->error->getErro('0007') );
        }

        // Atualiza o agendamento e cria o histórico
        foreach ($resultado['command'] as $chave => $comando) {

            $agenda->atualizarAgendamento(
                $idAgendamentos[$chave], $comando['aid']
            );

            if ( !$atendimento_emergencial && ($tipoOrdemServico[$chave] != 'RT') ) {

                $this->estoqueAgenda->setNumeroOrdemServico($agendamentos[$chave]['osaordoid']);
                $this->estoqueAgenda->setCodigoPrestador($agendamentos[$chave]['osarepoid']);
                $this->estoqueAgenda->setAgendamentoID($idAgendamentos[$chave]);
                $this->estoqueAgenda->setDataAgendamento($data);
                $resultado = $this->estoqueAgenda->setPedirProduto();

                if ($resultado['status'] == 'erro') {
                    throw new Exception( $this->error->getErro('0008') );
                } else if($resultado['status'] == 'falta_critica') {
                    throw new Exception( $this->error->getErro('0014') );
                }
            }

            $this->ordemServico->salvaHistorico(
                $historico[$chave]['idOrdemServico'],
                $historico[$chave]['idUsuario'],
                $historico[$chave]['mensagem'],
                $historico[$chave]['dataAgendamento'],
                $historico[$chave]['horaAgendamento'],
                $historico[$chave]['status']
            );
        }


        // Atualiza o consumo na tabelas de controle da intranet
        $this->controleConsumo->setIdsAgendamento($idAgendamentos);
        $this->controleConsumo->setSlot($slot);
        $this->controleConsumo->setTipoAtendimento($agendamentos[0]['osatipo_atendimento']);
        $this->controleConsumo->setWorkSkillConsumo($workSkillconsumo);
        $this->controleConsumo->cadastrarOrdemServico();
    }


    public function cancelarAgendamento($agendamentos, $parametros) {
        $idsOS = array();

        $parametros->obs = 'Reagendamento da O.S. realizado com sucesso.';

        foreach ($agendamentos as $agendamento) {

            $permitirCancelar = true;

            $dadosAgenda = $this->agenda->getDadosAgendamento($agendamento['osaordoid'], 'ORDEM_SERVICO');

            if( count($dadosAgenda) == 0 ){
                throw new Exception( $this->error->getErro('0015') );
            }

            //Tratamento para nao cancelar a atividade no OFSC em D-1
            if( strtotime($dadosAgenda['osadata']) < strtotime(date('Y-m-d')) ) {
                $permitirCancelar = false;
            }

            $idsOS[] = $agendamento['osaordoid'];

            try {
                $isReagendamento = true;
                if($this->integracao) {
                    $isReagendamento = false;
                }

                //Acoes comuns de cancelamento
                $this->efetivarCancelamento(
                    $agendamento["osaordoid"],
                    $parametros,
                    $permitirCancelar,
                    $isReagendamento
                );
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

        }

        //cancelamento dos links entre atividades no OFSC
        if ( $parametros->retirada_reinstalacao ){
            $this->cancelarLinkTarefas($idsOS);
        }

    }

    public function buscaCEP() {
        $params = $this->tratarParametros();
        $response = $this->dao->buscaCEP($params->cep);
        print json_encode($response);
        exit;
    }

    protected function getHorariosTimeslot($timeslot) {

        list($horaInicial, $horaFinal) = explode('-', $timeslot);

        return array(
            'inicial' => date('H:i', mktime(0, $horaInicial*60)),
            'final' => date('H:i', mktime(0, $horaFinal*60))
        );
    }

    protected function converterUTF8($array) {
        array_map('convertUTF8', $array);
        return $array;
    }

    protected function converterUTF8OFSC($array)
    {
        array_walk_recursive($array, function(&$item, $key){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    private function alteraClasseOS($ordemServicos) {
        // Verifica se a OS tem motivo UPGRADE
        // Se tiver retorna a nova classe de migração
        foreach ($ordemServicos as $key => $ordemServico) {
            foreach ($ordemServico['servicos'] as $key => $servico) {
                if(strcmp($servico['motivo'], 'UPGRADE') == 0){
                    $equipamentoMigracao = $this->contrato->getEquipamentoContrato($ordemServico['ordoid']);
                }
            }
        }

        // Se retornar classe de migração é atualizado a OS
        if(count($equipamentoMigracao) > 0) {
            $ordemServicos[0]['eqcoid'] = $equipamentoMigracao[0]['eqcoid'];
            $ordemServicos[0]['eqcdescricao'] = $equipamentoMigracao[0]['eqcdescricao'];
        }
        return $ordemServicos;
    }

    /**
     * Validar o periodo entre datas
     */
    private function validarPeriodo($dataInicial, $dataFinal, $maiorIgual = false){

        $dataInicial = implode('-', array_reverse(explode('/', substr($dataInicial, 0, 10)))).substr($dataInicial, 10);
        $dataFinal = implode('-', array_reverse(explode('/', substr($dataFinal, 0, 10)))).substr($dataFinal, 10);

        if($maiorIgual) {
            if($dataInicial >= $dataFinal) {
                return true;
            }
        } else {

            if($dataInicial > $dataFinal) {
                return true;
            }
        }

        return false;
    }


    protected function enviarNotificacao($idOrdemServico, $contexto, $parametros = null, $osaoid = null) {

        if (!is_null($parametros)) {
            $dados = array(
                'osecnome'    => $parametros->cmp_contato,
                'osecemail'   => $parametros->cmp_contato_email,
                'oscccelular' => $parametros->cmp_contato_celular,
            );
        } else {
            // Busca os dados necessários para enviar a notificação
            $dados = $this->dao->getDadosEmailSms($idOrdemServico);
        }

        if(empty($osaoid)){
            // Busca o ID do agendamento
            $agendamento = $this->agenda->getDadosAgendamento($idOrdemServico,'ORDEM_SERVICO');

            if(count($agendamento == 0)){
                $agendamento = $this->agenda->getDadosAgendamento($idOrdemServico,'REAGENDAMENTO');
            }

            $osaoid = $agendamento['osaoid'];
        }


        $comunicacaoEmailsSMS = new ComunicacaoEmailsSMS();

        $comunicacaoEmailsSMS->id             = $osaoid;
        $comunicacaoEmailsSMS->contexto       = $contexto;
        $comunicacaoEmailsSMS->nomeContato    = $dados['osecnome'];
        $comunicacaoEmailsSMS->emailDestino   = $dados['osecemail'];
        $comunicacaoEmailsSMS->celularDestino = $dados['oscccelular'];
        $comunicacaoEmailsSMS->setTipoAgendamento('U');
        $comunicacaoEmailsSMS->dadosEndereco  = $this->dadosEndereco;
        $resultadoEnvio = $comunicacaoEmailsSMS->EnviaEmailSms();

        if ($resultadoEnvio == false) {
            //vai buscar o id do tipo de agendamento passando o motivo agendamento
            if($contexto == 'AGENDAMENTO_CANCELADO') {
                $statusMotivo = 'Agendamento Cancelado';
            } else {
                $statusMotivo = 'Agendamento Unitário';
            }
            $idTipoAgendamento = $this->ordemServico->retornoHistoricoCorretora($statusMotivo);
            $resumo = array(
                'id' => $comunicacaoEmailsSMS->id,
                'contexto' => $comunicacaoEmailsSMS->contexto,
                'nomeContato' => $comunicacaoEmailsSMS->nomeContato,
                'emailDestino' => $comunicacaoEmailsSMS->emailDestino,
                'celularDestino' => $comunicacaoEmailsSMS->celularDestino,
                'tipoAgendamento' => $comunicacaoEmailsSMS->tipoAgendamento,
                'dadosEndereco' => $comunicacaoEmailsSMS->dadosEndereco
            );

            $texto = "Problema ao realizar envio e-mail e/ou sms, informações: " . json_encode($resumo);
            //grava o histórico de falha no envio do email/sms
            $this->ordemServico->salvaHistorico(
                $idOrdemServico,
                $this->usuarioLogado,
                $texto,
                null,
                null,
                $idTipoAgendamento
            );
        }
    }

    /**
     * Método que aplica qualquer máscara
     *
     * @return mask
     */
    public function mask($val, $mask) {

        $maskared = '';
        $k = 0;

        for($i = 0; $i<=strlen($mask)-1; $i++) {

            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

}

function convertUTF8($item, $key = 0){
    if(is_string($item))
        $item = utf8_encode($item);
    return $item;
}

function ordenarResultadoSlots($a, $b)
{
    if ($a["score"] == $b["score"]) {
        return 0;
    }
    return ($a["score"] < $b["score"]) ? -1 : 1;
}
