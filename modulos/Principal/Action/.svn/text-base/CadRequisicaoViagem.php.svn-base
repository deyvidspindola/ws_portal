<?php

require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * Classe CadRequisicaoViagem.
 * Camada de regra de negócio.
 *
 * @package  Principal
 * @author   Ricardo Bonfim <ricardo.bonfim@meta.com.br>
 *
 */
class CadRequisicaoViagem {

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
     * Mensagem de alerta para data inválida
     * @const String
     */
    const MENSAGEM_ALERTA_DATA_INVALIDA = "Data informada não pode ser maior que a data atual.";
    const MENSAGEM_ALERTA_DATA_INVALIDA_MENOR = "Data informada não pode ser menor que a data atual.";

    /**
     * Mensagem de alerta para justificativa com menos de 11 caracteres
     * @const String
     */	
    const MENSAGEM_ALERTA_JUSTIFICATIVA_INVALIDA = "O campo Justificativa deve conter mais de 10 caracteres.";

    /**
     * Mensagem de alerta para usuário sem fornecedor com conta cadastrada
     * @const String
     */
    const MENSAGEM_ALERTA_USUARIO_INVALIDO = "O solicitante não possui conta bancária cadastrada no cadastro de fornecedores, favor cadastrar antes de fazer um adiantamento, caso tenha duvidas, envie um e-mail para \"financeiro@sascar.com.br\".";
    const MENSAGEM_ALERTA_USUARIO_INVALIDO2 = "O solicitante não possui fornecedor cadastrado no sistema, favor cadastrar antes de fazer um adiantamento, caso tenha duvidas, envie um e-mail para \"financeiro@sascar.com.br\".";
    /**
     * Mensagem de sucesso para inserção do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Requisição [idRequisicao] cadastrada com sucesso, aguarde a aprovação do responsável pelo centro de custo.";

    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Requisição [idRequisicao] alterada com sucesso.";

    /**
     * Mensagem de sucesso para exclusão do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

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

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar') {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->view->parametros->todasEmpresas = $this->dao->buscarEmpresas();

        if(!isset($this->view->parametros->empresa) || trim($this->view->parametros->empresa) == '') {
            $this->view->parametros->empresa = $_SESSION['usuario']['tecoid'];
        }

        require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/index.php";
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

    /**
     * Popula os arrays para os combos de estados e cidades
     *
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->idRequisicao = isset($this->view->parametros->idRequisicao) ? $this->view->parametros->idRequisicao : "";

        /*
         * Parametros Formulario: Principal
         */
        $this->view->parametros->solicitante = isset($this->view->parametros->solicitante) ? $this->view->parametros->solicitante : "";
        $this->view->parametros->empresa = isset($this->view->parametros->empresa) ? $this->view->parametros->empresa : "";
        $this->view->parametros->centroCusto = isset($this->view->parametros->centroCusto) ? $this->view->parametros->centroCusto : "";
        $this->view->parametros->justificativa = isset($this->view->parametros->justificativa) ? pg_escape_string(trim($this->view->parametros->justificativa)) : "";
        $this->view->parametros->tipoRequisicao = isset($this->view->parametros->tipoRequisicao) ? trim($this->view->parametros->tipoRequisicao) : "";

        $this->view->parametros->projeto = isset($this->view->parametros->projeto) ? $this->view->parametros->projeto : "";
        $this->view->parametros->idaVolta = isset($this->view->parametros->idaVolta) ? trim($this->view->parametros->idaVolta) : "";
        $this->view->parametros->dtPartida = isset($this->view->parametros->dtPartida) ? trim($this->view->parametros->dtPartida) : "";
        $this->view->parametros->dtRetorno = isset($this->view->parametros->dtRetorno) ? trim($this->view->parametros->dtRetorno) : "";

        $this->view->parametros->placaVeiculo = isset($this->view->parametros->placaVeiculo) ? trim($this->view->parametros->placaVeiculo) : "";
        $this->view->parametros->estadoOrigem = isset($this->view->parametros->estadoOrigem) ? trim($this->view->parametros->estadoOrigem) : "";
        $this->view->parametros->cidadeOrigem = isset($this->view->parametros->cidadeOrigem) ? trim($this->view->parametros->cidadeOrigem) : "";
        $this->view->parametros->estadoDestino = isset($this->view->parametros->estadoDestino) ? trim($this->view->parametros->estadoDestino) : "";
        $this->view->parametros->cidadeDestino = isset($this->view->parametros->cidadeDestino) ? trim($this->view->parametros->cidadeDestino) : "";
        $this->view->parametros->distancia = isset($this->view->parametros->distancia) ? $this->view->parametros->distancia : "";

        $this->view->parametros->valorAdiantamento = isset($this->view->parametros->valorAdiantamento) ? trim($this->view->parametros->valorAdiantamento) : "";
        $this->view->parametros->dataCredito = isset($this->view->parametros->dataCredito) ? trim($this->view->parametros->dataCredito) : "";

        $this->view->parametros->aprovador = isset($this->view->parametros->aprovador) ? $this->view->parametros->aprovador : "";

        /*
         * Parametros Formulario: Aprovação da Requisição
         */
        $this->view->parametrosAprovacao = new stdClass();
        $this->view->parametrosAprovacao->valorAprovacaoRequisicao = isset($this->view->parametros->valorAprovacaoRequisicao) ? $this->view->parametros->valorAprovacaoRequisicao : "";
        $this->view->parametrosAprovacao->observacoesAprovacaoRequisicao = isset($this->view->parametros->observacoesAprovacaoRequisicao) ? $this->view->parametros->observacoesAprovacaoRequisicao : "";
        $this->view->parametrosAprovacao->statusAprovacaoRequisicao = isset($this->view->parametros->statusAprovacaoRequisicao) ? $this->view->parametros->statusAprovacaoRequisicao : "";

        /*
         * Parametros Formulario: Prestacao de Contas
         */
        $this->view->parametros->adigvalor_unitario = isset($this->view->parametros->adigvalor_unitario) ? $this->view->parametros->adigvalor_unitario : "";
        $this->view->parametros->adigdt_despesa = isset($this->view->parametros->adigdt_despesa) ? $this->view->parametros->adigdt_despesa : "";
        $this->view->parametros->adigtdpoid = isset($this->view->parametros->adigtdpoid) ? $this->view->parametros->adigtdpoid : "";
        $this->view->parametros->adignota = isset($this->view->parametros->adignota) ? $this->view->parametros->adignota : "";
        $this->view->parametros->adigobs = isset($this->view->parametros->adigobs) ? $this->view->parametros->adigobs : "";
    }

    private function inicializarParametrosConferencia(stdClass $dados) {

        $dados->idEstabelecimento = $this->dao->buscarEstabelecimentoUsuario($dados->solicitante);

        $dados->dadosDespesa = $this->dao->buscarDadosDespesa($dados->idRequisicao);

        /*
         * Se tipo da requisicao for Reembolso, geramos nota fiscal e titulo
         */
        if ($this->view->parametrosConferencia->tipoRequisicao == 'L') {

            $dados->valorParcela = $dados->dadosDespesa->valorDespesas;
            $dados->numeroParcelas = 1;
            $dados->vencimentoParcela = $this->buscarProximaQuintaUtil();

        } else {

            if($dados->dadosDespesa->valorDespesas > $dados->valorAdiantamento) {
                $dados->numeroParcelas = 2;
                $dados->valorSegundaParcela = $dados->dadosDespesa->valorDespesas - $dados->valorAdiantamento;
                $dados->vencimentoSegundaParcela = $this->buscarProximaQuintaUtil();
                $dados->valorPrimeiraParcela = $dados->valorAdiantamento;
            } else {
                $dados->numeroParcelas = 1;
                $dados->valorPrimeiraParcela = $dados->dadosDespesa->valorDespesas;
            }
        }

        $dados->fornecedor = $this->dao->buscarDadosFornecedorUsuario($dados->solicitante);
        $dados->dadosProdutos = $this->dao->buscarDadosProdutos($dados->idRequisicao);

        list($dia, $mes, $ano) = explode('/', $dados->dataChegadaRelatorio);
        $dados->dataChegadaRelatorio = implode('-', array($ano, $mes, $dia));

        return $dados;
    }


    private function inicializarParametrosTipoRequisicaoReembolso(stdClass $dados) {

        $dados->idEstabelecimento = $this->dao->buscarEstabelecimentoUsuario($dados->solicitante);

        $dados->dadosDespesa = $this->dao->buscarDadosDespesa($dados->idRequisicao);

        $dados->numeroParcelas = 1;
        $dados->valorPrimeiraParcela = $dados->dadosDespesa->valorDespesas;
        $dados->vencimentoParcela = $this->buscarProximaQuintaUtil();
        
        $dados->fornecedor = $this->dao->buscarDadosFornecedorUsuario($dados->solicitante);
        $dados->dadosProdutos = $this->dao->buscarDadosProdutos($dados->idRequisicao);

        return $dados;
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisar(stdClass $filtros) {
        $paginacao = new PaginacaoComponente();

        $this->validarCamposPesquisa($filtros);

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        } else {
            $campos = array(
                'adivalor' => 'Valor Solicitado',
                'adittipo_solicitacao' => 'Tipo Solicitação',
                'forfornecedor' => 'Solicitante',
                'adistatus_solicitacao' => 'Status',
                'adicadastro' => 'Data'
            );

            if ($paginacao->setarCampos($campos)) {
                $this->view->ordenacao = $paginacao->gerarOrdenacao('adicadastro');
                $this->view->paginacao = $paginacao->gerarPaginacao(count($resultadoPesquisa));
            }

            $resultadoPesquisa = $this->dao->pesquisar($filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());
        }

        /*
         * Permissionamento
         */
        $requisicao = new stdClass();
        if ($_SESSION['funcao']['visualizar_requisicao_viagem'] == 1) {

            $requisicao->permissaoEdicao = 'Solicitante';

        } else {

            foreach ($resultadoPesquisa as $requisicao) {
                $requisicao->permissaoEdicao = $this->buscarTipoPermissao($requisicao->cd_usuario, $requisicao->adicntoid);
            }
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     *
     * @param stdClass $parametros Dados do cadastro, para edição (opcional)
     *
     * @return void
     */
    public function cadastrar($parametros = null) {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try {

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros();

            if ($this->view->parametros->tipoRequisicao == 'L') {

                $this->validarCamposCadastro($this->view->parametros);

                $dadosUsuario = $this->dao->buscarDadosUsuario($this->view->parametros->solicitante);

                $this->view->parametros->nomeSolicitante = $dadosUsuario->nm_usuario;

                $this->view->parametros->nomeCentroCusto = $this->buscarNomeCentroCusto($this->view->parametros->centroCusto);

                $registroGravado = false;

                if (!$this->view->parametros->idRequisicao) {
                    $this->view->parametros->statusRequisicao = 'S';
                }

                $this->view->parametros->aprovadores = $this->dao->buscarAprovadoresCentroCusto($this->view->parametros->centroCusto);

                if ($_SESSION['prestacao_contas']['id_temp_requisicao'] == '') {
                    $_SESSION['prestacao_contas']['id_temp_requisicao'] = rand(10000000, 11000000);
                    $this->view->parametros->idRequisicao = $_SESSION['prestacao_contas']['id_temp_requisicao'];
                } else {
                    if (!$this->view->parametros->idRequisicao) {
                        $this->view->parametros->idRequisicao = $_SESSION['prestacao_contas']['id_temp_requisicao'];
                    }
                }

                
            } else {

                unset($_SESSION['prestacao_contas']['id_temp_requisicao']);

                //Verificar se foi submetido o formulário e grava o registro em banco de dados
                if (isset($_POST) && !empty($_POST)) {
                    $registroGravado = $this->salvar($this->view->parametros);
                }

            }
            
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $_POST = array();

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado) {

            $this->index();
        } else {

            // Dados para preencher as combos no cadastro
            $this->view->parametros->solicitantes = $this->buscarSolicitantes();
            $this->view->parametros->todasEmpresas = $this->dao->buscarEmpresas();
            $this->view->parametros->todosProjetos = $this->dao->buscarProjetos();
            $this->view->parametros->todosEstados = $this->dao->buscarEstados();
            $this->view->parametros->tipoDespesa = $this->dao->buscarTipoDespesa();

            if(!isset($this->view->parametros->permissaoEdicao)) {
                $this->view->parametros->permissaoEdicao = 'Solicitante';
            }

            if($this->view->parametros->statusRequisicao == 'F') {
                $this->view->parametros->dadosDespesa = $this->dao->buscarDadosDespesa($this->view->parametros->idRequisicao);
            }

            if(!isset($this->view->parametros->solicitante) || trim($this->view->parametros->solicitante) == '') {
                $this->view->parametros->solicitante = $_SESSION['usuario']['oid'];
            }

            if(!isset($this->view->parametros->empresa) || trim($this->view->parametros->empresa) == '') {
                $this->view->parametros->empresa = $_SESSION['usuario']['tecoid'];
            }

            require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/cadastrar.php";

        }
    }

    
    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     *
     * @return void
     */
    public function editar() {

        try {


            //Parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->idRequisicao) && intval($parametros->idRequisicao) > 0) {
                //Realiza o CAST do parametro
                $parametros->idRequisicao = (int) $parametros->idRequisicao;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->idRequisicao);
                
                $dados->permissaoEdicao = $this->buscarTipoPermissao($dados->solicitante, $dados->centroCusto);
                
                if ($_SESSION['funcao']['visualizar_requisicao_viagem'] == 1 && $dados->permissaoEdicao != 'Solicitante') {
                    $dados->permissaoEdicao = 'Aprovador';
                } else {
                     $dados->permissaoEdicao = $this->buscarTipoPermissao($dados->solicitante, $dados->centroCusto);
                    if($dados->permissaoEdicao == false) {
                        throw new ErrorException("Você não tem acesso a esta requisição.");
                    }
                }

               
                
                /*
                 * Status Pendente de Aprovação Reembolso
                 * Usuario logado Aprovador
                 */
                $dados->permissaoAprovacaoReembolso = false;
                if($dados->statusRequisicao == "R" && $dados->permissaoEdicao == 'Aprovador') {
                	$dados->permissaoAprovacaoReembolso = true;
                }

                if ( !isset( $parametros->alterarItens ) ) {
                    $this->setaSessaoPrestacaoContas($dados->idRequisicao);                    
                }

                // Setando id temporaria para nao criar uma nova id para a requisicao
                $_SESSION['prestacao_contas']['id_temp_requisicao'] = true;
                /*
                 * Parte de Prestacao de contas
                 */
                if($dados->statusRequisicao == 'S' || $dados->statusRequisicao == 'F' || $dados->statusRequisicao == 'R' || ($dados->statusRequisicao == 'A' && $dados->tipoRequisicao == 'A' )){
                	
                	$dados->aprovadores = $this->dao->buscarAprovadoresCentroCusto($dados->centroCusto);
                	
                	if ($_SESSION['prestacao_contas'][$parametros->idRequisicao]['adicionar_despesa'] != 1) {
                        
                		$this->setaSessaoPrestacaoContas($dados->idRequisicao);
                		$_SESSION['prestacao_contas'][$dados->idRequisicao]['valor_total_adiantamento'] = $dados->valorAdiantamento;
                		$_SESSION['prestacao_contas'][$dados->idRequisicao]['centro_custo'] = $dados->centroCusto;
                		$this->somarValoresItens($dados->idRequisicao);
                		$_SESSION['prestacao_contas'][$parametros->idRequisicao]['adicionar_despesa'] = 1;
                	}
                	
                	$dados->desabilitarCamposPrestacaoContas = "disabled='disabled'";
                	if ($dados->permissaoEdicao == 'Solicitante' && $dados->statusRequisicao == 'S') {
                		$dados->desabilitarCamposPrestacaoContas = '';
                	} else if ($_SESSION['funcao']['visualizar_requisicao_viagem'] == 1 && $dados->statusRequisicao == 'F')  { 
                        $dados->desabilitarCamposPrestacaoContas = '';
                        $dados->permissaoEdicao == 'Solicitante';
                    }
                	
                } else {
                	$dados->desabilitarCamposPrestacaoContas = "disabled='disabled'";
                }
                          
                $dados = $this->tratarParametros($dados);

                //Chama o metodo para edição passando os dados do registro por parametro.
                $this->cadastrar($dados);
            } else {
                $this->index();
            }
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Grava os dados na base de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     *
     * @return void
     */
    private function salvar(stdClass $dados) {
        
        //Validar os campos
        $this->validarCamposCadastro($dados);

        $dados->fornecedor = $this->dao->buscarDadosFornecedorUsuario($dados->solicitante);

        if($dados->tipoRequisicao == 'A') {
            if(isset($dados->dataCredito) && $dados->dataCredito != '') {
                list($dia, $mes, $ano) = explode('/', $dados->dataCredito);
                $dados->dataCredito = date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) );
            }
            
            $dados->dtPartida = '';
            $dados->dtRetorno = '';
        } else if($dados->tipoRequisicao == 'C') {
            $dadosConsumo = $this->dao->buscarDadosConsumoCombustivel();
            $dados->quantidadeLitros = ($dados->distancia / $dadosConsumo->acckmlitro);
            $dados->valorAdiantamento = ceil($dados->quantidadeLitros * $dadosConsumo->accvalorlitro);

            if(isset($dados->dtPartida) && $dados->dtPartida != '') {
                list($dia, $mes, $ano) = explode('/', $dados->dtPartida);
                $dados->dtPartida = date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) );
            }
            if(isset($dados->dtRetorno) && $dados->dtRetorno != '') {
                list($dia, $mes, $ano) = explode('/', $dados->dtRetorno);
                $dados->dtRetorno = date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) );
            }

            $dados->dataCredito = '';
        }
        if(isset($dados->valorAdiantamento) && $dados->valorAdiantamento != '') {
            $dados->valorAdiantamento = number_format($dados->valorAdiantamento, 2, '.', '');
        }

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->idRequisicao > 0) {
            //Efetua a gravação do registro
            if($this->dao->atualizarRequisicao($dados)) {
                $gravacao = $this->dao->atualizarTipoRequisicao($dados);
                //Seta a mensagem de atualização
                $this->view->mensagemSucesso = str_replace('[idRequisicao]', $dados->idRequisicao, self::MENSAGEM_SUCESSO_ATUALIZAR);
            } else {
                $gravacao = false;
            }
        } else {
            //Efetua a inserção do registro
            $dados->idRequisicao = $this->dao->inserirRequisicao($dados);
            if(intval($dados->idRequisicao) > 0) {
                $gravacao = $this->dao->inserirTipoRequisicao($dados);
            } else {
                $gravacao = false;
            }
            $this->enviarEmailAprovacao($dados);
            $this->view->mensagemSucesso = str_replace('[idRequisicao]', $dados->idRequisicao, self::MENSAGEM_SUCESSO_INCLUIR);

        }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios na pesquisa
     *
     * @param stdClass $dados Dados a serem validados
     *
     * @throws Exception
     *
     * @return void
     */
    private function validarCamposPesquisa(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->empresa) || trim($dados->empresa) == '') {
            $camposDestaques[] = array(
                'campo' => 'empresa'
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Validar os campos obrigatórios do cadastro.
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

        //Verifica se data é maior que hoje
        $dataInvalida = false;

        //Verifica se justificativa tem mais que 10 caracteres
        $justificativaInvalida = false;

        //Verifica se usuário possui fornecedor com conta cadastrada
        $usuarioSemFornecedor = false;

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->solicitante) || trim($dados->solicitante) == '') {
            $camposDestaques[] = array(
                'campo' => 'solicitante'
            );
            $error = true;
        } else {
            $dadosFornecedor = $this->dao->buscarDadosFornecedorUsuario($dados->solicitante);
            
            if ($dados->tipoRequisicao != 'C' && (!isset($dadosFornecedor->forconta) && $dadosFornecedor->forconta == NULL)) {
                $camposDestaques[] = array(
                    'campo' => 'solicitante'
                );
                $usuarioSemFornecedor = true;
            } elseif ($dados->tipoRequisicao == 'C' && (!isset($dadosFornecedor->foroid) && $dadosFornecedor->foroid == NULL)){
                $camposDestaques[] = array(
                    'campo' => 'solicitante'
                );
                $usuarioSemFornecedor = true;
                $usuarioSemFornecedor2 = true;
            }
                
        }

        if (!isset($dados->empresa) || trim($dados->empresa) == '') {
            $camposDestaques[] = array(
                'campo' => 'empresa'
            );
            $error = true;
        }

        if (!isset($dados->centroCusto) || trim($dados->centroCusto) == '') {
            $camposDestaques[] = array(
                'campo' => 'centroCusto'
            );
            $error = true;
        }

        if (!isset($dados->justificativa) || trim($dados->justificativa) == '') {
            $camposDestaques[] = array(
                'campo' => 'justificativa'
            );
            $error = true;
        } else if (strlen($dados->justificativa) <= 10) {
            $camposDestaques[] = array(
                'campo' => 'justificativa'
            );
            $justificativaInvalida = true;
        }

        if (!isset($dados->tipoRequisicao) || trim($dados->tipoRequisicao) == '') {
            $camposDestaques[] = array(
                'campo' => 'tipoRequisicao'
            );
            $error = true;
        } else {

            if ($dados->tipoRequisicao != 'L') {

                if (!isset($dados->aprovador) || trim($dados->aprovador) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'aprovador'
                    );
                    $error = true;
                }
            }

            if ($dados->tipoRequisicao == 'A') {

                if (!isset($dados->valorAdiantamento) || trim($dados->valorAdiantamento) == '' || trim($dados->valorAdiantamento) == '0,00') {
                    $camposDestaques[] = array(
                        'campo' => 'valorAdiantamento'
                    );
                    $error = true;
                } else {
                    $dados->valorAdiantamento = str_replace('.', '', $dados->valorAdiantamento);
                }

                if (!isset($dados->dataCredito) || trim($dados->dataCredito) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'dataCredito'
                    );
                    $error = true;
                } else {
                    list($dia, $mes, $ano) = explode('/', $dados->dataCredito);

                    if (strtotime(date('Y-m-d')) > strtotime( date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) ) ) ) {
                        $camposDestaques[] = array(
                            'campo' => 'dataCredito'
                        );
                        $dataInvalida = true;
                    }
                }
            } else if ($dados->tipoRequisicao == 'C') {

                if (!isset($dados->idaVolta) || trim($dados->idaVolta) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'idaVolta'
                    );
                    $error = true;
                } else {

                    if (!isset($dados->dtPartida) || trim($dados->dtPartida) == '') {
                        $camposDestaques[] = array(
                            'campo' => 'dtPartida'
                        );
                        $error = true;
                    } else {
                        list($dia, $mes, $ano) = explode('/', $dados->dtPartida);

                        if (strtotime(date('Y-m-d')) > strtotime(date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) ) ) ) {
                            $camposDestaques[] = array(
                                'campo' => 'dtPartida'
                            );
                            $dataInvalida = true;
                        }
                    }

                    if ($dados->idaVolta == 'I') {
                        if (!isset($dados->dtRetorno) || trim($dados->dtRetorno) == '') {
                            $camposDestaques[] = array(
                                'campo' => 'dtRetorno'
                            );
                            $error = true;
                        } else {
                            list($dia, $mes, $ano) = explode('/', $dados->dtRetorno);

                            if (strtotime(date('Y-m-d')) > strtotime(date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) ) ) ) {
                                $camposDestaques[] = array(
                                    'campo' => 'dtRetorno'
                                );
                                $dataInvalida = true;
                            }
                        }
                    }
                }

                if (!isset($dados->placaVeiculo) || trim($dados->placaVeiculo) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'placaVeiculo'
                    );
                    $error = true;
                }

                if (!isset($dados->estadoOrigem) || trim($dados->estadoOrigem) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'estadoOrigem'
                    );
                    $error = true;
                }

                if (!isset($dados->cidadeOrigem) || trim($dados->cidadeOrigem) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'cidadeOrigem'
                    );
                    $error = true;
                }

                if (!isset($dados->estadoDestino) || trim($dados->estadoDestino) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'estadoDestino'
                    );
                    $error = true;
                }

                if (!isset($dados->cidadeDestino) || trim($dados->cidadeDestino) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'cidadeDestino'
                    );
                    $error = true;
                }

                if (!isset($dados->distancia) || trim($dados->distancia) == '') {
                    $camposDestaques[] = array(
                        'campo' => 'distancia'
                    );
                    $error = true;
                }
            }
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if ($dataInvalida) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_DATA_INVALIDA_MENOR);
        }

        if ($justificativaInvalida) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_JUSTIFICATIVA_INVALIDA);
        }

        if ($usuarioSemFornecedor) {
            $this->view->dados = $camposDestaques;
            if($usuarioSemFornecedor2)
                throw new Exception(self::MENSAGEM_ALERTA_USUARIO_INVALIDO2);
            else
                throw new Exception(self::MENSAGEM_ALERTA_USUARIO_INVALIDO);
        }
    }

    private function validarCamposAprovacaoRequisicao(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        if (!isset($dados->valorAprovacaoRequisicao) || trim($dados->valorAprovacaoRequisicao) == '' || trim($dados->valorAprovacaoRequisicao) == '0,00') {
            $camposDestaques[] = array(
                'campo' => 'valorAprovacaoRequisicao'
            );
            $error = true;
        }

        if (!isset($dados->observacoesAprovacaoRequisicao) || trim($dados->observacoesAprovacaoRequisicao) == '') {
            $camposDestaques[] = array(
                'campo' => 'observacoesAprovacaoRequisicao'
            );
            $error = true;
        }

        if (!isset($dados->statusAprovacaoRequisicao) || trim($dados->statusAprovacaoRequisicao) == '') {
            $camposDestaques[] = array(
                'campo' => 'statusAprovacaoRequisicao'
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    private function validarCamposConferencia(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        //Verifica se data é maior que hoje
        $dataInvalida = false;

        //Verifica se justificativa tem mais que 10 caracteres
        $justificativaInvalida = false;

        if (!isset($dados->statusConferencia) || trim($dados->statusConferencia) == '') {
            $camposDestaques[] = array(
                'campo' => 'statusConferencia'
            );
            $error = true;
        }

        if (!isset($dados->dataChegadaRelatorio) || trim($dados->dataChegadaRelatorio) == '') {
            $camposDestaques[] = array(
                'campo' => 'dataChegadaRelatorio'
            );
            $error = true;
        } else {
            list($dia, $mes, $ano) = explode('/', $dados->dataChegadaRelatorio);

            if (strtotime( date('Y-m-d', strtotime( implode('-', array($ano, $mes, $dia) ) ) ) ) > strtotime(date('Y-m-d'))) {
                $camposDestaques[] = array(
                    'campo' => 'dataChegadaRelatorio'
                );
                $dataInvalida = true;
            }
        }

        if (isset($dados->valorReembolso) && ( trim($dados->valorReembolso) == '' || trim($dados->valorReembolso) == '0,00') ) {
            $camposDestaques[] = array(
                'campo' => 'valorReembolso'
            );
            $error = true;
        }

        if (isset($dados->valorDevolucao) && ( trim($dados->valorDevolucao) == '' || trim($dados->valorDevolucao) == '0,00') ) {
            $camposDestaques[] = array(
                'campo' => 'valorDevolucao'
            );
            $error = true;
        }

        if (!isset($dados->justificativaConferencia) || trim($dados->justificativaConferencia) == '') {
            $camposDestaques[] = array(
                'campo' => 'justificativaConferencia'
            );
            $error = true;
        } else if (strlen($dados->justificativaConferencia) <= 10) {
            $camposDestaques[] = array(
                'campo' => 'justificativaConferencia'
            );
            $justificativaInvalida = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if ($dataInvalida) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_DATA_INVALIDA);
        }

        if ($justificativaInvalida) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_JUSTIFICATIVA_INVALIDA);
        }
    }
    
    private function validarCamposAprovacaoReembolso(stdClass $dados) {
    
    	//Campos para destacar na view em caso de erro
    	$camposDestaques = array();
    	
    	//Verifica se houve erro
    	$error = false;
    
    	if (!isset($dados->valorAprovacaoReembolso) || trim($dados->valorAprovacaoReembolso) == '' || trim($dados->valorAprovacaoReembolso) == '0,00') {
    		$camposDestaques[] = array(
    				'campo' => 'valorAprovacaoReembolso'
    		);
    		$error = true;
    	}
    
    	if (!isset($dados->observacoesAprovacaoReembolso) || trim($dados->observacoesAprovacaoReembolso) == '') {
    		$camposDestaques[] = array(
    				'campo' => 'observacoesAprovacaoReembolso'
    		);
    		$error = true;
    	}
    
    	if (!isset($dados->statusAprovacaoReembolso) || trim($dados->statusAprovacaoReembolso) == '') {
    		$camposDestaques[] = array(
    				'campo' => 'statusAprovacaoReembolso'
    		);
    		$error = true;
    	}

    	if ($error) {
    		$this->view->dados = $camposDestaques;
    		throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
    	}
    
    }



    /**
     * Executa a exclusão de registro.
     *
     * @return void
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->idRequisicao) || trim($parametros->idRequisicao) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->idRequisicao = (int) $parametros->idRequisicao;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->idRequisicao);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
            }
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->index();
    }

    public function salvarAprovacaoRequisicao() {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try {

            $this->dao->begin();

            $this->view->parametrosAprovacao = $this->tratarParametros();

            $this->validarCamposAprovacaoRequisicao($this->view->parametrosAprovacao);

            if($this->view->parametrosAprovacao->statusAprovacaoRequisicao == 'A') {
                if($this->view->parametrosAprovacao->tipoRequisicao == 'A') {
                    // Pendente de prestação de contas
                    $this->view->parametrosAprovacao->statusSolicitacao = 'S';
                } else {
                    // Finalizada
                    $this->view->parametrosAprovacao->statusSolicitacao = 'A';
                }
            } else {
                // Reprovada
                $this->view->parametrosAprovacao->statusSolicitacao = 'C';
            }

            $valorAdiantamento = preg_replace('/\./', '', $this->view->parametrosAprovacao->valorAdiantamento);
            $this->view->parametrosAprovacao->valorSolicitado = number_format($valorAdiantamento, 2, '.', '');

            $valorAprovacaoRequisicao = preg_replace('/\./', '', $this->view->parametrosAprovacao->valorAprovacaoRequisicao);
            $valorAprovacaoRequisicao = number_format($valorAprovacaoRequisicao, 2, '.', '');
            $this->view->parametrosAprovacao->valorAprovacaoRequisicao = $valorAprovacaoRequisicao;
            $this->view->parametrosAprovacao->valorAdiantamento = $valorAprovacaoRequisicao;

            $registroGravado = $this->dao->inserirAprovacao($this->view->parametrosAprovacao);

            if($this->view->parametrosAprovacao->statusAprovacaoRequisicao == 'A') {

                if($this->view->parametrosAprovacao->tipoRequisicao == 'A') {
                    //Tipo da Requisicao = Adiantamento
                    $dadosEmail = $this->formatarEmailAprovacaoAdiantamento($this->view->parametrosAprovacao);
                    $this->gerarContaPagar($this->view->parametrosAprovacao);
                } else {
                    // Tipo da Requisicao = Combustível - ticket car
                    $dadosEmail = $this->formatarEmailAprovacaoCombustivel($this->view->parametrosAprovacao);
                }

                $this->view->mensagemSucesso = "A requisição " . $this->view->parametrosAprovacao->idRequisicao . " foi aprovada.";
            } else {
                // Status Reprovado
                $dadosEmail = $this->formatarEmailRequisicaoReprovada($this->view->parametrosAprovacao);

                $this->view->mensagemSucesso = "A requisição " . $this->view->parametrosAprovacao->idRequisicao . " foi reprovada.";
            }

            $this->enviarEmail($dadosEmail);

            $this->dao->commit();


        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }


        $_POST = array();

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado) {
            $this->index();
        } else {
            $this->editar();
        }
    }


    public function salvarConferenciaPrestacaoContas() {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try {

            $this->dao->begin();

            $this->view->parametrosConferencia = $this->tratarParametros();

            $this->validarCamposConferencia($this->view->parametrosConferencia);

            $dados = $this->inicializarParametrosConferencia($this->view->parametrosConferencia);

            $dados->tipoRequisicao = $this->view->parametrosConferencia->tipoRequisicao;

            // ID DO REGISTRO 
            // parametrizacao esquema de dominio, registro e valor
            if ($dados->tipoRequisicao == 'A') {
                $id_registro = 4;
            } else if ($dados->tipoRequisicao == 'L') {
                $id_registro = 5;
            }

            $dados->condicao_pagamento = $this->dao->buscarCondicaoPagamento($id_registro);

            if($dados->statusConferencia == 'A') {

                $dados->statusRequisicao = 'A';

                /*
                 * Se tipo da requisicao for Reembolso, geramos nota fiscal e titulo
                 */
                if ($this->view->parametrosConferencia->tipoRequisicao == 'L') {

                     // Criar nova entrada de nota fiscal
                    $retorno = $this->dao->gerarEntrada($dados);
                    $dados->idEntrada      = $retorno->entoid;         

                    // Cria itens da entrada agrupado pela despesa (10 refeições se tornam uma com o valor de cada uma somado)
                    foreach($dados->dadosProdutos as $produto) {
                        if(trim($produto->plano_contabil) == ''){
                            throw new ErrorException('Atenção Produto '.$produto->id_produto.' sem Plano Contabil.');
                        } else{
                            $this->dao->gerarEntradaItem($dados, $produto);
                        }
                    }

                    $dados->centroCusto = $this->view->parametrosConferencia->centroCusto;
                    $dados->contaContabil = $this->dao->buscarContaContabil($dados->empresa, "Contas a Pagar")->plcoid;
                   //$this->dao->inserirParcelaTipoReembolso($dados);

                } else {

                    // Criar nova entrada de nota fiscal
                    $retorno = $this->dao->gerarEntrada($dados);
                    $dados->idEntrada      = $retorno->entoid;
                    
                    // Cria itens da entrada agrupado pela despesa (10 refeições se tornam uma com o valor de cada uma somado)
                    foreach($dados->dadosProdutos as $produto) {                        
                        if(trim($produto->plano_contabil) == ''){
                            throw new ErrorException('Atenção Produto '.$produto->id_produto.' sem Plano Contabil.');
                        } else{
                            $this->dao->gerarEntradaItem($dados, $produto);
                        }
                    }

                    // Cria conta a pagar (ATENÇÃO NA CONTA CONTÁBIL, A SEGUNDA PARCELA TEM UMA CONTA CONTABIL DIFERENTE)
                    if($dados->numeroParcelas == 2) {
                        $dados->contaContabil = $this->dao->buscarContaContabil($dados->empresa, "Adiantamentos para Viagens")->plcoid;
                        //$this->dao->inserirPrimeiraParcela($dados);

                        $dados->contaContabil = $this->dao->buscarContaContabil($dados->empresa, "Contas a Pagar")->plcoid;
                        //$this->dao->inserirSegundaParcela($dados);
                    } else {
                        $dados->contaContabil = $this->dao->buscarContaContabil($dados->empresa, "Adiantamentos para Viagens")->plcoid;
                        $this->dao->inserirPrimeiraParcela($dados);
                    }

                }

                // Altera o status para "Concluido"
                $this->dao->inserirDadosConferencia($dados);

            } else {
                // Reprovada

                // Retorna status da requisição para Prestação de Contas
                $dados->statusRequisicao = 'S';

                // Parametriza os dados do email a ser enviado
                $email = $this->formatarEmailConferenciaReprovada($dados);

                $this->dao->inserirDadosConferencia($dados);

                // Envia email
                $this->enviarEmail($email);

            }

            $this->view->parametrosConferencia = $dados;

            $this->dao->commit();

            // Seta mensagem de sucesso
            $this->view->mensagemSucesso = "Operação realizada com sucesso.";

            $registroGravado = true;


        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $_POST = array();

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado) {
            $this->index();
        } else {
            $this->editar();
        }
    }

    private function buscarProximaQuintaUtil() {

        $dataInvalida = false;

        $feriados = $this->dao->buscarFeriados();
        foreach($feriados as $key => $value) {
             list($dia, $mes, $ano) = explode('/', $value);
             $feriados[$key] = strtotime( implode('-', array($ano, $mes, $dia) ) );
        }

        $dia = getdate(strtotime(date('Ymd')) + 86400);
        
        $quinta = $this->buscarProximaQuinta($dia);
        if(in_array($quinta[0], $feriados) ) {
            $dataInvalida = true;
        }

        while($dataInvalida) {
            $quinta = $this->buscarProximaQuinta(getdate($quinta[0] + 86400));
            if(in_array($quinta[0], $feriados) ) {
                $dataInvalida = true;
            } else {
                $dataInvalida = false;
            }
        }
 
        $proximaQuintaUtil = $quinta['year'] . '-' . $quinta['mon'] . '-' . $quinta['mday'];
        return $proximaQuintaUtil;
    }

    private function buscarProximaQuinta($dia) {
        //Busca próxima quinta, contando a partir de data atual +1 dia

        while($dia['weekday'] != 'Thursday') {
            $dia = getdate($dia[0] + 86400);
        }
        return $dia;
    }

    private function buscarSolicitantes() {
        $idCentroCusto = $this->dao->buscarCentroCustoUsuario($_SESSION['usuario']['depoid']);
        $aprovador = $this->verificaAprovadorCentroCusto($_SESSION['usuario']['oid'], $idCentroCusto);
        if ($aprovador) {
            return $this->dao->buscarUsuariosCentroCusto($idCentroCusto);
        } else {
            $solicitantes = array();
            array_push($solicitantes, $this->dao->buscarDadosUsuario($_SESSION['usuario']['oid']));
            return $solicitantes;
        }
    }

    private function buscarTipoPermissao($idSolicitante, $idCentroCusto) {
        $idUsuarioLogado = $_SESSION['usuario']['oid'];

        $aprovadorRequisicao = $this->verificaAprovadorCentroCusto($idUsuarioLogado, $idCentroCusto);

        if($idUsuarioLogado == $idSolicitante) {
            return 'Solicitante';
        } else if ($aprovadorRequisicao) {
            return 'Aprovador';
        }
        return false;
    }

    private function verificaAprovadorCentroCusto($idUsuario, $idCentroCusto) {
        $aprovador = false;

        $aprovadoresCentroCusto = $this->dao->buscarAprovadoresCentroCusto($idCentroCusto);

        foreach ($aprovadoresCentroCusto as $aprovadorCentroCusto) {
            if ($aprovadorCentroCusto->cd_usuario == $idUsuario) {
                $aprovador = true;
            }
        }

        return $aprovador;
    }

    private function gerarContaPagar(stdClass $dados) {

        try{
            $tipoConta = $this->dao->buscarTipoContaPagar();
            $dados->tipoConta = $tipoConta->tctoid;

            $plcdescricao = $dados->tipoRequisicao == 'L' ? 'Contas a Pagar' : 'Adiantamentos para Viagens';

            $contaContabil = $this->dao->buscarContaContabil($dados->empresa, $plcdescricao);
            $dados->contaContabil = $contaContabil->plcoid;

            $dadosSolicitante = $this->dao->buscarDadosFornecedorUsuario($dados->solicitante);
            $dados->idFornecedor = $dadosSolicitante->foroid;

            $this->dao->inserirContaPagar($dados);

        } catch(Exception $ex) {
            throw $ex;
        }

    }

    private function enviarEmailAprovacao(stdClass $dados) {

        $dadosEmail = $this->formatarEmailAprovacaoRequisicao($dados);

        if (!empty($dadosEmail->corpoEmail)) {
            $headers = "From: \"Sascar\" <sascar@bbturismo.com.br>\n";
            $headers .= "Content-type: text/html\n";

            $this->enviarEmail($dadosEmail);
        }
    }

    private function formataNumero($numero,$nivel,$dig1,$dig2,$dig3,$dig4,$dig5,$dig6){
    
        $tamTotal = $dig1 + $dig2 + $dig3 + $dig4 + $dig5 + $dig6;
        
        if(strlen($numero)<$tamTotal){
            $buf=$numero.str_repeat("0",$tamTotal-strlen($numero));
        }else{ 
            $buf=$numero;
        }
        
        if($nivel==1){
            $buf2 = substr($buf,0,$dig1);
        }elseif($nivel==2){
            $buf2 = substr($buf,0,$dig1).".".substr($buf,$dig1,$dig2);
        }elseif($nivel==3){
            $buf2 = substr($buf,0,$dig1).".".substr($buf,$dig1,$dig2).".".substr($buf,($dig1+$dig2),$dig3);
        }elseif($nivel==4){
            $buf2 = substr($buf,0,$dig1).".".substr($buf,$dig1,$dig2).".".substr($buf,($dig1+$dig2),$dig3).".".substr($buf,($dig1+$dig2+$dig3),$dig4);
        }elseif($nivel==5){
            $buf2 = substr($buf,0,$dig1).".".substr($buf,$dig1,$dig2).".".substr($buf,($dig1+$dig2),$dig3).".".substr($buf,($dig1+$dig2+$dig3),$dig4).".".substr($buf,($dig1+$dig2+$dig3+$dig4),$dig5);
        }elseif($nivel==6){
            $buf2 = substr($buf,0,$dig1).".".substr($buf,$dig1,$dig2).".".substr($buf,($dig1+$dig2),$dig3).".".substr($buf,($dig1+$dig2+$dig3),$dig4).".".substr($buf,($dig1+$dig2+$dig3+$dig4),$dig5).".".substr($buf,($dig1+$dig2+$dig3+$dig4+$dig5),$dig6);
        }            

        return $buf2;
            
    }

    private function formatarEmailAprovacaoRequisicao(stdClass $dados) {
        $email = new stdClass();

        // busca dados do aprovador
        $dadosAprovador = $this->dao->buscarDadosUsuario($dados->aprovador);
        $email->destinatario = $dadosAprovador->usuemail;

        // busca dados do usuário do solicitante
        $dadosSolicitante = $this->dao->buscarDadosUsuario($dados->solicitante);

        if ($dados->tipoRequisicao == 'A') {
            $email->assunto = "Requisição de Adiantamento de " . $dadosSolicitante->nm_usuario;
        } else if ($dados->tipoRequisicao == 'C') {
            $email->assunto = "Requisição de Combustível - ticket Car de " . $dadosSolicitante->nm_usuario;
        } else {
            $email->assunto = '';
        }

        // busca nome da empresa
        $todasEmpresas = $this->dao->buscarEmpresas();
        $nomeEmpresa = '';
        foreach ($todasEmpresas as $empresa) {
            if ($empresa->tecoid == $dados->empresa) {
                $nomeEmpresa = $empresa->tecrazao;
            }
        }

        // busca nome do centro de custo
        $todosCentrosDeCusto = $this->dao->buscarCentrosCusto($dados->empresa);
        $nomeCentroCusto = '';

        foreach ($todosCentrosDeCusto as $centroDeCusto) {
            if ($centroDeCusto->cntoid == $dados->centroCusto) {
                $nomeCentroCusto = $centroDeCusto->cntconta;
            }
        }

        if($dados->tipoRequisicao == 'C') {
            // buscar nome do projeto
            $todosProjetos = $this->dao->buscarProjetos();
            $nomeProjeto = '';
            foreach ($todosProjetos as $projeto) {
                if ($projeto->rproid == $dados->projeto) {
                    $nomeProjeto = $projeto->rprnome;
                }
            }
            
            // buscar nome do estado origem
            $todosEstados = $this->dao->buscarEstados();
            $nomeEstadoOrigem = '';
            foreach ($todosEstados as $estado) {
                if ($estado->estoid == $dados->estadoOrigem) {
                    $nomeEstadoOrigem = $estado->estnome;
                }
            }

            // buscar nome da cidade origem
            $todasCidadesOrigem = $this->dao->buscarCidadesEstado($dados->estadoOrigem);
            $nomeCidadeOrigem = '';
            foreach ($todasCidadesOrigem as $cidade) {
                if ($cidade->cidoid == $dados->cidadeOrigem) {
                    $nomeCidadeOrigem = $cidade->ciddescricao;
                }
            }

            // buscar nome do estado destino
            $nomeEstadoDestino = '';
            foreach ($todosEstados as $estado) {
                if ($estado->estoid == $dados->estadoDestino) {
                    $nomeEstadoDestino = $estado->estnome;
                }
            }

            // buscar nome da cidade origem
            $todasCidadesDestino = $this->dao->buscarCidadesEstado($dados->estadoDestino);
            $nomeCidadeDestino = '';
            foreach ($todasCidadesDestino as $cidade) {
                if ($cidade->cidoid == $dados->cidadeDestino) {
                    $nomeCidadeDestino = $cidade->ciddescricao;
                }
            }
        }

        $email->corpoEmail = "
            <table>
                <tr>
                    <td style=\"width: 250px;\">Solicitante</td>
                    <td>" . $dadosSolicitante->nm_usuario . "</td>
                </tr>
                <tr>
                    <td>Empresa</td>
                    <td>" . $nomeEmpresa . "</td>
                </tr>
                <tr>
                    <td>Centro de Custo</td>
                    <td>" . $nomeCentroCusto . "</td>
                </tr>
                <tr>
                    <td>Justificativa</td>
                    <td>" . wordwrap($dados->justificativa, 60, "<br />", true) . "</td>
                </tr>
                <tr>
                    <td>Tipo de Requisição</td>
                    <td>" . ($dados->tipoRequisicao == 'A' ? 'Adiantamento' : 'Combustível - ticket car') . "</td>
                </tr>";

        if ($dados->tipoRequisicao == 'A') {
            $email->corpoEmail .= "
                <tr>
                    <td>Valor</td>
                    <td>" . $dados->valorAdiantamento . "</td>
                </tr>
                <tr>
                    <td>Solicitar Aprovação Para</td>
                    <td>" . $dadosAprovador->nm_usuario . "</td>
                </tr>
                <tr>
                    <td>Solicitar Crédito Para</td>
                    <td>" . date("d/m/Y", strtotime($dados->dataCredito)) . "</td>
                </tr>
            </table>";
        } else if ($dados->tipoRequisicao == 'C') {
            $email->corpoEmail .= "
                <tr>
                    <td>Projeto</td>
                    <td>" . $nomeProjeto . "</td>
                </tr>
                <tr>
                    <td>Ida/Volta</td>
                    <td>" . ($dados->idaVolta == 'I' ? 'Ida e Volta' : 'Somente Ida') . "</td>
                </tr>
                <tr>
                    <td>Data Partida</td>
                    <td>" . date("d/m/Y", strtotime($dados->dtPartida)) . "</td>
                </tr>";

            if ($dados->idaVolta == 'I') {
                $email->corpoEmail .= "
                    <tr>
                        <td>Data Retorno</td>
                        <td>" . date("d/m/Y", strtotime($dados->dtRetorno)) . "</td>
                    </tr>";
            }

            $email->corpoEmail .= "
                <tr>
                    <td>Placa do Veículo</td>
                    <td>" . $dados->placaVeiculo . "</td>
                </tr>
                <tr>
                    <td>Estado Origem</td>
                    <td>" . $nomeEstadoOrigem . "</td>
                </tr>
                <tr>
                    <td>Cidade Origem</td>
                    <td>" . $nomeCidadeOrigem . "</td>
                </tr>
                <tr>
                    <td>Estado Destino</td>
                    <td>" . $nomeEstadoDestino . "</td>
                </tr>
                <tr>
                    <td>Cidade Destino</td>
                    <td>" . $nomeCidadeDestino . "</td>
                </tr>
                <tr>
                    <td>Distância (em KM)</td>
                    <td>" . $dados->distancia . "</td>
                </tr>
                <tr>
                    <td>Litros</td>
                    <td>" . $dados->quantidadeLitros . "</td>
                </tr>
                <tr>
                    <td>Crédito</td>
                    <td>R$ " . number_format($dados->valorAdiantamento, 2, ',', '.') . "</td>
                </tr>
                <tr>
                    <td>Solicitar Aprovação Para</td>
                    <td>" . $dadosAprovador->nm_usuario . "</td>
                </tr>
            </table>";
        } else {
            $email->corpoEmail = '';
        }

        return $email;
    }

    private function formatarEmailAprovacaoAdiantamento(stdClass $dados) {

        $dadosEmail = new stdClass();

        // busca dados do solicitante
        $dadosSolicitante = $this->dao->buscarDadosUsuario($dados->solicitante);
        $dadosEmail->destinatario = $dadosSolicitante->usuemail;

        $dadosEmail->assunto = "Status Requisição " . $dados->idRequisicao;

        $dadosAprovador = $this->dao->buscarDadosUsuario($_SESSION['usuario']['oid']);
        $dadosEmail->corpoEmail = "Sua requisição foi aprovada por " . $dadosAprovador->nm_usuario .
                " com as seguintes observações: \"" . $dados->observacoesAprovacaoRequisicao . "\".";

        return $dadosEmail;
    }

    private function formatarEmailAprovacaoCombustivel(stdClass $dados) {

        $dadosEmail = new stdClass();

        // busca dados do aprovador
        $dadosSolicitante = $this->dao->buscarDadosUsuario($dados->solicitante);

        $dadosEmail->destinatario = "infra@sascar.com.br";
        $dadosEmail->destinatario_cc = $dadosSolicitante->usuemail;

        $dadosEmail->assunto = "Requisição de Crédito de Combustível Ticket Car";

        $dadosAprovadorRequisicao = $this->dao->buscarDadosUsuario($_SESSION['usuario']['oid']);

        // buscar nome do projeto
        $todosProjetos = $this->dao->buscarProjetos();
        $nomeProjeto = '';
        foreach($todosProjetos as $projeto) {
            if($projeto->rproid == $dados->projeto){
                $nomeProjeto = $projeto->rprnome;
            }
        }

        // buscar nome do estado origem
        $todosEstados = $this->dao->buscarEstados();
        $nomeEstadoOrigem = '';
        foreach($todosEstados as $estado) {
            if($estado->estoid == $dados->estadoOrigem){
                $nomeEstadoOrigem = $estado->estnome;
            }
        }

        // buscar nome da cidade origem
        $todasCidadesOrigem = $this->dao->buscarCidadesEstado($dados->estadoOrigem);
        $nomeCidadeOrigem = '';
        foreach($todasCidadesOrigem as $cidade) {
            if($cidade->cidoid == $dados->cidadeOrigem){
                $nomeCidadeOrigem = $cidade->ciddescricao;
            }
        }

        // buscar nome do estado destino
        $nomeEstadoDestino = '';
        foreach($todosEstados as $estado) {
            if($estado->estoid == $dados->estadoDestino){
                $nomeEstadoDestino = $estado->estnome;
            }
        }

        // buscar nome da cidade origem
        $todasCidadesDestino = $this->dao->buscarCidadesEstado($dados->estadoDestino);
        $nomeCidadeDestino = '';
        foreach($todasCidadesDestino as $cidade) {
            if($cidade->cidoid == $dados->cidadeDestino){
                $nomeCidadeDestino = $cidade->ciddescricao;
            }
        }

        $dadosConsumo = $this->dao->buscarDadosConsumoCombustivel();

        $dadosEmail->corpoEmail = "
            <table>
                <tr>
                    <td style=\"width: 250px;\">Solicitante</td>
                    <td>" . $dadosSolicitante->nm_usuario . "</td>
                </tr>
                <tr>
                    <td>E-mail Solicitante</td>
                    <td>" . $dadosSolicitante->usuemail . "</td>
                </tr>
                <tr>
                    <td>Placa do Veículo</td>
                    <td>" . $dados->placaVeiculo . "</td>
                </tr>
                <tr>
                    <td>Crédito</td>
                    <td>R$ " . number_format($dados->valorAdiantamento, 2, ',', '.') . "</td>
                </tr>
                <tr>
                    <td>Aprovado por</td>
                    <td>" . $dadosAprovadorRequisicao->nm_usuario . "</td>
                </tr>
                <tr>
                    <td>Projeto</td>
                    <td>" . $nomeProjeto . "</td>
                </tr>
                <tr>
                    <td>Ida/Volta</td>
                    <td>" . ($dados->idaVolta == 'I' ? 'Ida e Volta' : 'Somente Ida') . "</td>
                </tr>
                <tr>
                    <td>Data Partida</td>
                    <td>" . $dados->dtPartida . "</td>
                </tr>";

        if($dados->idaVolta == 'I') {
            $dadosEmail->corpoEmail .= "
            <tr>
                <td>Data Retorno</td>
                <td>" . $dados->dtRetorno . "</td>
            </tr>";
        }

            $dadosEmail->corpoEmail .= "
                <tr>
                    <td>Estado Origem</td>
                    <td>" . $nomeEstadoOrigem . "</td>
                </tr>
                <tr>
                    <td>Cidade Origem</td>
                    <td>" . $nomeCidadeOrigem . "</td>
                </tr>
                <tr>
                    <td>Estado Destino</td>
                    <td>" . $nomeEstadoDestino . "</td>
                </tr>
                <tr>
                    <td>Cidade Destino</td>
                    <td>" . $nomeCidadeDestino . "</td>
                </tr>
                <tr>
                    <td>Distância (em KM)</td>
                    <td>" . $dados->distancia . "</td>
                </tr>
                <tr>
                    <td>Litros</td>
                    <td>" . number_format(floatval($dados->distancia/$dadosConsumo->acckmlitro), 2, ',', '') . "</td>
                </tr>
            </table>";

        return $dadosEmail;
    }

    private function formatarEmailConferenciaReprovada(stdClass $dados) {

        $dadosEmail = new stdClass();

        // busca dados do aprovador
        $dadosSolicitante = $this->dao->buscarDadosUsuario($dados->solicitante);

        $dadosEmail->destinatario = $dadosSolicitante->usuemail;

        $dadosEmail->assunto = "Prestação de Contas " . $dados->idRequisicao;

        $dadosAprovador = $this->dao->buscarDadosUsuario($_SESSION['usuario']['oid']);

        $dadosEmail->corpoEmail = "Prezado, a prestação de contas da sua requisição foi reprovada pelo departamento responsável, ";
        $dadosEmail->corpoEmail .= "a avaliação foi feita pelo usuário " . $dadosAprovador->nm_usuario;
        $dadosEmail->corpoEmail .= " que encontrou os seguintes problemas: \"" . $dados->justificativaConferencia . "\".";

        return $dadosEmail;
    }

    private function enviarEmail(stdClass $email) {
        $phpmailer = new PHPMailer();
        $phpmailer->isSmtp();
        $phpmailer->From = "sistema@sascar.com.br";
        $phpmailer->FromName = "Sascar";
        $phpmailer->ClearAllRecipients();

        if ($_SESSION["servidor_teste"] == 1) {
            $email->destinatario = _EMAIL_TESTE_;
            if (isset($email->destinatario_cc)) {
                $email->destinatario_cc = _EMAIL_TESTE_;
            }
        }

        if (isset($email->destinatario_cc)) {
            $phpmailer->AddCC($email->destinatario_cc);
        }

        $phpmailer->AddAddress($email->destinatario);
        $phpmailer->Subject = $email->assunto;
        $phpmailer->MsgHTML($email->corpoEmail);

        $phpmailer->Send();
    }

    private function formatarEmailRequisicaoReprovada(stdClass $dados) {

        $dadosEmail = new stdClass();

        // busca dados do aprovador
        $dadosSolicitante = $this->dao->buscarDadosUsuario($dados->solicitante);
        $dadosEmail->destinatario = $dadosSolicitante->usuemail;

        $dadosEmail->assunto = "Status Requisição " . $dados->idRequisicao;

        $dadosAprovador = $this->dao->buscarDadosUsuario($_SESSION['usuario']['oid']);
        $dadosEmail->corpoEmail = "Sua requisição foi reprovada por " . $dadosAprovador->nm_usuario .
                " pelo seguinte motivo: " . $dados->observacoesAprovacaoRequisicao . ".";

        return $dadosEmail;

    }

    private function formatarEmailReembolsoReprovado(stdClass $dados) {

        $dadosEmail = new stdClass();

        // busca dados do aprovador
        $dadosSolicitante = $this->dao->buscarDadosUsuario($dados->solicitante);
        $dadosEmail->destinatario = $dadosSolicitante->usuemail;

        $dadosEmail->assunto = "Status Requisição " . $dados->idRequisicao;

        $dadosAprovador = $this->dao->buscarDadosUsuario($_SESSION['usuario']['oid']);
        $dadosEmail->corpoEmail = "Sua requisição de reembolso foi reprovada por " . $dadosAprovador->nm_usuario .
                " pelo seguinte motivo: " . $dados->observacoesAprovacaoReembolso . ".";

        return $dadosEmail;
    }

    private function buscarNomeCentroCusto($cntoid) {

        try {
            $centroCusto = $this->dao->buscarCentroCusto( $cntoid );
            $numeroCentroCusto = $this->formataNumero($centroCusto->cntno_centro, $centroCusto->nivel, $centroCusto->dig1, $centroCusto->dig2, $centroCusto->dig3, $centroCusto->dig4, $centroCusto->dig5, $centroCusto->dig6);
            $nomeCentroCusto = $centroCusto->cntconta . ' - ' . $numeroCentroCusto;

            return $nomeCentroCusto;
            
        } catch (Exception $e) {
            throw $e;
        }

    }


    /**
     * Funções Ajax
     */
    public function ajaxBuscarCentrosCusto() {
        $idEmpresa = $_POST['idEmpresa'];
        $centrosCusto = $this->dao->buscarCentrosCusto($idEmpresa);
        foreach($centrosCusto as $centroCusto) {
            $numero_centro_custo = $this->formataNumero($centroCusto->cntno_centro, $centroCusto->nivel, $centroCusto->dig1, $centroCusto->dig2, $centroCusto->dig3, $centroCusto->dig4, $centroCusto->dig5, $centroCusto->dig6);
            $centroCusto->cntconta = utf8_encode($centroCusto->cntconta) . ' - ' . $numero_centro_custo;
        }
        echo json_encode($centrosCusto);
        exit();
    }

    public function ajaxBuscarAprovadoresCentroCusto() {
        $idCentroCusto = $_POST['idCentroCusto'];
        $aprovadoresCentroCusto = $this->dao->buscarAprovadoresCentroCusto($idCentroCusto);
        foreach($aprovadoresCentroCusto as $aprovador) {
            $aprovador->nm_usuario = utf8_encode($aprovador->nm_usuario);
        }
        echo json_encode($aprovadoresCentroCusto);
        exit();
    }

    public function ajaxBuscarFeriados() {
        echo json_encode($this->dao->buscarFeriados());
        exit();
    }

    public function ajaxBuscarCidadesEstado() {
        $idEstado = $_POST['idEstado'];
        $cidades = $this->dao->buscarCidadesEstado($idEstado);
        foreach($cidades as $cidade) {
            $cidade->ciddescricao = utf8_encode($cidade->ciddescricao);
        }
        
        echo json_encode($cidades);
        exit();
    }

    public function ajaxBuscarDadosConsumo() {
        echo json_encode($this->dao->buscarDadosConsumoCombustivel());
        exit();
    }
    
    public function salvarAprovacaoReembolso() {
    	    	
    	//identifica se o registro foi gravado
    	$registroGravado = false;
    	
    	try {
    
    		$this->dao->begin();
    
    		$this->view->parametrosAprovacao = $this->tratarParametros();

    		$this->validarCamposAprovacaoReembolso($this->view->parametrosAprovacao);
    		
    		if($this->view->parametrosAprovacao->statusAprovacaoReembolso == "A") {
    			$this->view->parametrosAprovacao->statusSolicitacao = "F";
    		} else {
    			$this->view->parametrosAprovacao->statusSolicitacao = "S";
    		}

    		$this->dao->inserirAprovacaoReembolso($this->view->parametrosAprovacao);
    		
    		if($this->view->parametrosAprovacao->statusAprovacaoReembolso == "R") {
                $dadosEmail = $this->formatarEmailReembolsoReprovado($this->view->parametrosAprovacao);
    			$this->enviarEmail($dadosEmail);
    		}
    		
    		$this->dao->commit();
    		
    		$registroGravado = true;
    		
    		$this->view->mensagemSucesso = "Operação realizada com sucesso.";
    
    	} catch (ErrorException $e) { 
    
    		//Rollback em caso de erro
    		$this->dao->rollback();
    
    		$this->view->mensagemErro = $e->getMessage();
    	} catch (Exception $e) {
    
    		//Rollback em caso de erro
    		$this->dao->rollback();
    
    		$this->view->mensagemAlerta = $e->getMessage();
    	}

        $_POST = array();
    	
    	//Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
    	if ($registroGravado) {
    		$this->index();
    	} else {
    		$this->editar();
    	}
    }
    
    public function inserirItemSessao() {
    	
    	$chave = isset($_POST['chave']) ? $_POST['chave'] : null;
    	$adioid = isset($_POST['adioid']) ? $_POST['adioid'] : null;
    	$data_despesa = isset($_POST['data_despesa']) ? $_POST['data_despesa'] : null;
    	$id_tipo_despesa = isset($_POST['id_tipo_despesa']) ? $_POST['id_tipo_despesa'] : null;
    	$tipo_despesa = isset($_POST['tipo_despesa']) ? $_POST['tipo_despesa'] : null;
    	$valor_despesa = isset($_POST['valor_despesa']) ? $this->formataValorParaCalculo($_POST['valor_despesa']) : null;
    	$numero_nota = isset($_POST['numero_nota']) ? $_POST['numero_nota'] : null;
    	$observacao_prestacao_contas = isset($_POST['observacao_prestacao_contas']) ? $_POST['observacao_prestacao_contas'] : null;
    	$solicitar_reembolso_para = isset($_POST['solicitar_reembolso_para']) ? $_POST['solicitar_reembolso_para'] : null;
        $tipoRequisicao = isset($_POST['tipoRequisicao']) ? $_POST['tipoRequisicao'] : null;
        
        $_SESSION['prestacao_contas'][$adioid]['tipoRequisicao'] = $tipoRequisicao;

    	if ($chave == '') {
    	
    		$_SESSION['prestacao_contas'][$adioid]['itens'][] = array(
    				'data_despesa' => $data_despesa,
    				'tipo_despesa' => utf8_decode($tipo_despesa),
    				'chave_tipo_despesa' => $id_tipo_despesa,
    				'valor_despesa' => $valor_despesa,
    				'numero_nota' => $numero_nota,
    				'observacao_prestacao_contas' => $observacao_prestacao_contas
    		);
    	
            $_SESSION['prestacao_contas'][$adioid]['mensagem_prestacao_de_contas'] = "Despesa adicionada.";
        } else {
        
            //Edicao
            $_SESSION['prestacao_contas'][$adioid]['itens'][$chave] = array(
                    'data_despesa' => $data_despesa,
                    'tipo_despesa' => utf8_decode($tipo_despesa),
                    'chave_tipo_despesa' => $id_tipo_despesa,
                    'valor_despesa' => $valor_despesa,
                    'numero_nota' => $numero_nota,
                    'observacao_prestacao_contas' => $observacao_prestacao_contas
            );

    	   $_SESSION['prestacao_contas'][$adioid]['mensagem_prestacao_de_contas'] = "Despesa alterada.";
        }

    	$this->somarValoresItens($adioid);
    	
    	exit;
    }
    
    public function salvarPrestacaoContas(){
    	
    	try {
    		
    		$this->dao->begin();
    		
	    	$adioid = (isset($_POST['adioid'])) ? $_POST['adioid'] : null;
	    	$email_aprovador = (isset($_POST['email_aprovador'])) ? $_POST['email_aprovador'] : null;
	    	$flag_registro_bd = (isset($_POST['flag_registro_bd'])) ? $_POST['flag_registro_bd'] : null;


            $tipoRequisicao = (isset($_POST['tipoRequisicao'])) ? $_POST['tipoRequisicao'] : null;
            $idSolicitante = (isset($_POST['idSolicitante'])) ? $_POST['idSolicitante'] : null;
            $idEmpresa = (isset($_POST['idEmpresa'])) ? $_POST['idEmpresa'] : null;
            $centroCusto = (isset($_POST['centroCusto'])) ? $_POST['centroCusto'] : null;
            $justificativa = (isset($_POST['justificativa'])) ? $_POST['justificativa'] : null;
            
            /*
             * Se o tipo da requisicao for Reembolso, insere um adiantamento
             */
            $adioidInserido = "";
            if ($tipoRequisicao == 'L' && $flag_registro_bd != 1) {

                $dadosFornecedor = $this->dao->buscarDadosFornecedorUsuario($idSolicitante);

                $dados = new stdClass();
                $dados->valorAdiantamento               = "'0.00'";
                $dados->fornecedor = new stdClass();
                $dados->fornecedor->forfornecedor       = $dadosFornecedor->forfornecedor;
                $dados->fornecedor->fordocto            = $dadosFornecedor->fordocto;
                $dados->justificativa                   = $justificativa;
                $dados->fornecedor->forbanco            = $dadosFornecedor->forbanco;
                $dados->fornecedor->foragencia          = $dadosFornecedor->foragencia;
                $dados->fornecedor->fordigito_agencia   = $dadosFornecedor->fordigito_agencia;
                $dados->fornecedor->forconta            = $dadosFornecedor->forconta;
                $dados->fornecedor->fordigito_conta     = $dadosFornecedor->fordigito_conta;
                $dados->solicitante                     = $idSolicitante;
                $dados->fornecedor->fortipo             = $dadosFornecedor->fortipo;
                $dados->dtPartida                       = ''; 
                $dados->dtRetorno                       = ''; 
                $dados->distancia                       = '';
                $dados->tipoRequisicaoReembolso         = true; //SETA O STATUS COMO APROVACAO DE REEMBOLSO
                $dados->fornecedor->foroid              = $dadosFornecedor->foroid;
                $dados->fornecedor->usudepoid           = $dadosFornecedor->usudepoid;
                $dados->empresa                         = $idEmpresa;
                $dados->centroCusto                     = $centroCusto;
                $dados->dataCredito                     = '';
                $dados->aprovador                       = 'NULL';

                $adioidInserido = $this->dao->inserirRequisicao($dados);

                $dadosTipoRequisicao = new stdClass();

                $dadosTipoRequisicao->idRequisicao = $adioidInserido;
                $dadosTipoRequisicao->tipoRequisicao = $tipoRequisicao;
                $dadosTipoRequisicao->dtPartida = '';
                $dadosTipoRequisicao->dtRetorno = '';
                $dadosTipoRequisicao->cidadeOrigem = '';
                $dadosTipoRequisicao->cidadeDestino = '';
                $dadosTipoRequisicao->placaVeiculo = '';
                $dadosTipoRequisicao->fornecedor = new stdClass();
                $dadosTipoRequisicao->fornecedor->forbanco = $dadosFornecedor->forbanco;
                $dadosTipoRequisicao->fornecedor->foragencia = $dadosFornecedor->foragencia;
                $dadosTipoRequisicao->fornecedor->fordigito_agencia = $dadosFornecedor->fordigito_agencia;
                $dadosTipoRequisicao->fornecedor->forconta = $dadosFornecedor->forconta;
                $dadosTipoRequisicao->fornecedor->fordigito_conta = $dadosFornecedor->fordigito_conta;
                $dadosTipoRequisicao->idaVolta = '';
                $dadosTipoRequisicao->distancia = '';
                $dadosTipoRequisicao->projeto = '';

                $this->dao->inserirTipoRequisicao($dadosTipoRequisicao);

                $_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] = 0;

            }

            if (!$adioidInserido) {
                $adioidInserido = $adioid;
            }
	    	
	    	if ($_SESSION['prestacao_contas'][$adioid]['itens']) {
	    	
	    		//alteracao de escopo no fim do desenvolvimento, por isso essa g-a-m-b-i
	    		if ($flag_registro_bd) {
	    			$this->dao->deletarItens($adioid);
	    		}
	    	
	    		foreach ($_SESSION['prestacao_contas'][$adioid]['itens'] as $key => $item) {
	    			$this->dao->inserirItensPrestacaoContas($adioidInserido, $item);
	    		}
	    	}
	    	
	    	$dados = new stdClass();
	    	
	    	// ENVIA EMAIL USUARIO
	    	$dados = $this->dao->buscaDadosusuario(intval($_SESSION['usuario']['oid']));
	    	
	    	//$dados->valor_reembolso = $_SESSION['prestacao_contas'][$adioid]['valor_total_receber'];
	    	$dados->valor_reembolso = ($_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] - $_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento']);
	    	//$dados->valor_devolucao = $_SESSION['prestacao_contas'][$adioid]['valor_total_devolver'];
	    	$dados->valor_devolucao = ($_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento'] - $_SESSION['prestacao_contas'][$adioid]['valor_total_despesas']);
	    	$dados->requisicao      = $adioidInserido;
	    	
            if ($tipoRequisicao == 'L' && $flag_registro_bd != 1) {

                $status = "R";
                
                //ENVIA EMAIL APROVADOR
                $dados->email = $email_aprovador;

                $dados->tipo_requisicao = 'reembolso';

                $this->enviarEmailPrestacaoContas($dados);

            } else {

    	    	if ($_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] > $_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento']) {
    	    	
    	    		// ALTERA STATUS DA REQUISICAO PARA "PENDENTE APROVACAO REEMBOLSO"
    	    		$status = "R";

                    // SE O APROVADOR ALTERA A PRESTACAO DE CONTAS DURANTE A CONFERENCIA DE PRESTACAO DE CONTAS
                    // ENTAO DEVE PERMANECER COM O STATUS DE CONFERENCIA DE PRESTACAO DE CONTAS
                    $objRequisicao = $this->dao->pesquisarPorID($adioid);
                    if ($_SESSION['funcao']['visualizar_requisicao_viagem'] == 1 && $objRequisicao->statusRequisicao == 'F') {
                        $status = "F";
                    }
    	    	
    	    		//ENVIA EMAIL APROVADOR
    	    		$dados->email = $email_aprovador;
    	    		$dados->tipo_email = 'reembolso';
    	    	
    	    		$this->enviarEmailPrestacaoContas($dados);
    	    	
    	    	} else if ($_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] == $_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento']) {
    	    	
    	    		// ALTERA STATUS DA REQUISICAO PARA "pendente conferencia de prestação de contas"
    	    		$status = "F";
    	    	
    	    	} else if ($_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] < $_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento']) {
    	    	
    	    		// ALTERA STATUS DA REQUISICAO PARA "pendente conferencia de prestação de contas"
    	    		$status = "F";
    	    	
    	    		$dados->tipo_email = 'devolucao';
    	    	
    	    		$this->enviarEmailPrestacaoContas($dados);
    	    	
    	    	}
            }

	    	// ATUALIZA O STATUS DA REQUISICAO
	    	$this->dao->alterarStatusSolicitacao($adioidInserido, $status);
	    	
	    	$this->setaSessaoPrestacaoContas($adioid);
	    	
	    	$_SESSION['prestacao_contas'][$adioid]['mensagem_prestacao_de_contas'] = "Prestação de contas incluída com sucesso.";
	    	
	    	$this->dao->commit();
	    	
	    	//$_POST = array();
	    	$this->view->mensagemSucesso = 'Prestação incluída com sucesso.';

            $redirecionamento = "";
            if ($tipoRequisicao == 'L' && $flag_registro_bd != 1) {
                $redirecionamento = _PROTOCOLO_ . _SITEURL_ . 'cad_requisicao_viagem.php?acao=editar&idRequisicao='.$adioidInserido.'&reembolso=1';
            } else {
                $redirecionamento = _PROTOCOLO_ . _SITEURL_ . 'cad_requisicao_viagem.php?acao=editar&idRequisicao='.$adioidInserido;
            }
	    	
	    	
	    } catch (Exception $e){
	    	
	    	$this->dao->rollback();
	    	
	    	$this->view->mensagemAlerta = $e->getMessage();
	    }

        echo json_encode($redirecionamento);
	    
	    exit;
    }
    
    public function setaSessaoPrestacaoContas($adioid) {
    	
    	$rs = $this->dao->buscarPrestacaoContas($adioid);
    	
    	if ($rs) {
    
    		$_SESSION['prestacao_contas'][$adioid]['registro_inserido'] = 0;
    		$_SESSION['prestacao_contas'][$adioid]['flag_registro_bd'] = 0;
    
    		if (pg_num_rows($rs) > 0) {
    
    			$_SESSION['prestacao_contas'][$adioid]['itens'] = "";
    
    			while ($row = pg_fetch_assoc($rs)) {
    
    				$_SESSION['prestacao_contas'][$adioid]['itens'][] = array(
    						'data_despesa'                  => $row['data_despesa'],
    						'tipo_despesa'                  => $row['tipo_despesa'],
    						'chave_tipo_despesa'            => $row['id_tipo_despesa'] ,
    						'valor_despesa'                 => $row['valor_despesa'],
    						'numero_nota'                   => $row['numero_nota'],
    						'observacao_prestacao_contas'   => trim($row['observacao'])
    				);
    
    				$_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento'] = $row['valor_adiantamento'];
    				$_SESSION['prestacao_contas'][$adioid]['status_solicitacao'] = $row['status_solicitacao'];
    			}
    
    			$this->somarValoresItens($adioid);
    
    			if ($_SESSION['prestacao_contas'][$adioid]['status_solicitacao'] == 'S') {
    				$_SESSION['prestacao_contas'][$adioid]['registro_inserido'] = 0;
    			} else {
    				$_SESSION['prestacao_contas'][$adioid]['registro_inserido'] = 1;
    			}
    
    			$_SESSION['prestacao_contas'][$adioid]['flag_registro_bd'] = 1;
    		}
    	}
    
    }
    
    public function enviarEmailPrestacaoContas($dados){
    	
    		require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
    	
    		$mail = new PHPMailer();
    		$mail->IsSMTP();
    		$mail->From = "sistema@sascar.com.br";
    		$mail->FromName = "Intranet SASCAR";
    	
    		$mail->ClearAllRecipients();
    	
    		if ($dados->tipo_email == 'reembolso') {
    	
    			$subject =  'Solicitação de Reembolso de ' . $dados->solicitante;
    			$corpo = "O usuário " . $dados->solicitante . ", solicita sua aprovação para o reembolso no valor de R$ " . number_format($dados->valor_reembolso, 2, ',', '.') . ", referente a requisição de viagem numero " . $dados->requisicao . ".";
    	
    		} else if ($dados->tipo_email == 'devolucao') {
    	
    			$subject = 'Devolução de Adiantamento Para Viagem';
    			$corpo = "Prezado " . $dados->solicitante . ", solicitamos que seja devolvido o valor de R$ " . number_format($dados->valor_devolucao, 2, ',', '.') . ", que conforme sua prestação de contas, não foi utilizado.
                  A devolução deverá ser feita via depósito Identificado com o seu CPF seguido do numero da requisição de viagem, o comprovante de depósito deverá ser enviado ao financeiro junto com seu relatório de despesas.
                  O depósito deverá ser feito na seguinte conta:
                  Banco: Itaú.
                  Ag: 0942.
                  C/C: 30087-2.";
    	
    		} else if ($dados->tipo_requisicao == 'reembolso') {

                $subject =  'Requisição de Reembolso de ' . $dados->solicitante;

                $corpo = $this->imprimirPrestacaoContas($dados->requisicao, true);

            }
    	
    	
    		if( $_SESSION['servidor_teste'] == 1 ){
    			$dados->email = _EMAIL_TESTE_;
    		}
    	
    		$mail->AddAddress($dados->email);
    		$mail->Subject = $subject;
    		$mail->MsgHTML($corpo);
    	
    		$mail->Send();
    	 
    }
    
    public function excluirItemSessao(){
    	
    	$chave = isset($_POST['chave']) ? $_POST['chave'] : null;
    	$adioid = isset($_POST['adioid']) ? $_POST['adioid'] : null;
    	
    	unset($_SESSION['prestacao_contas'][$adioid]['itens'][$chave]);
    	
    	$this->somarValoresItens($adioid);
    	
    	$_SESSION['prestacao_contas'][$adioid]['mensagem_prestacao_de_contas'] = "Despesa excluida.";
    	
    	exit;
    }
    
    public function formataValorParaCalculo($valor){
    
    	if (intval($valor) > 0) {
    		$valor = str_replace('.', '', $valor);
    		$valor = str_replace(',', '.', $valor);
    	} else {
    		$valor = str_replace(',', '.', $valor);
    	}
    
    	return $valor;    
    }
    
    /*
     * Funcao para somar os valores do itens da prestacao de contas
    */
    public function somarValoresItens($adioid) {
    
    	if ($_SESSION['prestacao_contas'][$adioid]['itens']) {
    
    		$_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] = 0;
    
    		foreach ($_SESSION['prestacao_contas'][$adioid]['itens'] as $itens) {
    			$_SESSION['prestacao_contas'][$adioid]['valor_total_despesas'] += ($itens['valor_despesa']);
    		}
    
    		$total = (($_SESSION['prestacao_contas'][$adioid]['valor_total_despesas']) - ($_SESSION['prestacao_contas'][$adioid]['valor_total_adiantamento']));
    
    		$_SESSION['prestacao_contas'][$adioid]['valor_total_receber'] = 0;
    		$_SESSION['prestacao_contas'][$adioid]['valor_total_devolver'] = 0;
    
    		if ($total > 0) {
    
    			$_SESSION['prestacao_contas'][$adioid]['valor_total_receber'] = $total;
    
    		} else {
    
    			$_SESSION['prestacao_contas'][$adioid]['valor_total_devolver'] = ($total * (-1));
    		}
    	}
    }
    
    /*
     * Imprimir prestacao de contas
     */
    public function imprimirPrestacaoContas($idRequisicao = null, $isReembolso = false) {
    	
    	$adioid = (isset($_POST['adioid'])) ? $_POST['adioid'] : null;

        if ($isReembolso) {
            $adioid = $idRequisicao;
        }
    	
    	if ($adioid) {
    	
    		$rs = $this->dao->buscarPrestacaoContasParaImprimir($adioid);
    	
    		if ($rs && pg_num_rows($rs) > 0) {



                $numero_centro_custo = $this->formataNumero(pg_fetch_result($rs, 0, 'cntno_centro'), pg_fetch_result($rs, 0, 'nivel'), pg_fetch_result($rs, 0, 'dig1'), pg_fetch_result($rs, 0, 'dig2'), pg_fetch_result($rs, 0, 'dig3'), pg_fetch_result($rs, 0, 'dig4'), pg_fetch_result($rs, 0, 'dig5'), pg_fetch_result($rs, 0, 'dig6'));
    	
    			$nome_solicitante   = utf8_encode(pg_fetch_result($rs, 0, 'nome_solicitante'));
    			$cpf                = pg_fetch_result($rs, 0, 'cpf');
    			$motivo             = utf8_encode(pg_fetch_result($rs, 0, 'motivo'));
    			$data_solicitacao   = pg_fetch_result($rs, 0, 'data_solicitacao');
    			$centro_custo       = $numero_centro_custo . ' - ' . utf8_encode(pg_fetch_result($rs, 0, 'centro_custo'));
    			$valor_recebido     = pg_fetch_result($rs, 0, 'valor_recebido');
                $empresa            = pg_fetch_result($rs, 0, 'empresa');
    		}
    	
    		$rs = $this->dao->somarValoresItens($adioid);    		
    	
    		if ($rs && pg_num_rows($rs) > 0) {
    			$valor_itens  = pg_fetch_result($rs, 0, 'valor_itens');
    		}
    	
    		$total = (($valor_itens) - ($valor_recebido));
    	
    		if ($total > 0) {
    			$a_receber = $total;
    			$a_receber_a_devolver = "A Receber: " . number_format($a_receber, 2, ',', '.');
    		} else {
    			$a_receber = ($total * (-1));
    			$a_receber_a_devolver = "A Devolver: " . number_format($a_receber, 2, ',', '.');
    		}

            $this->setaSessaoPrestacaoContas($adioid);
    	
    	
    		ob_start();
    	
    		?>
            <html>
            <head>
    	    <style>
	    	   @CHARSET "ISO-8859-1";
				
				body {
				    margin: 20px 0px;
				    padding: 0px;
				    font-family: Verdana, Arial, Helvetica, sans-serif;
				}
				
				a {
				    color: #3b5ca1;
				    text-decoration: none;
				}
				
				a:hover {
				    text-decoration: underline;
				}
				
				button {
				    cursor: pointer;
				    margin: 0px 5px;
				    padding: 1px 5px;
				    font-size: 12px;
				    border: 1px solid #999;
				    background: url('../../../images/fd_tr_principal.gif');
				}
				
				fieldset {
				    background: none !important;
				    float: left;
				    margin: 5px 10px 5px 0;
				    padding: 5px 10px 10px 10px;
				    border: 1px solid #999;
				    width: auto !important;
				}
				
				fieldset.erro {
				    color: #a47e3c;
				    border: 1px solid #a47e3c;
				}
				
				fieldset.erro legend {
				    color: #a47e3c;
				}
				
				fieldset label {
				    display: inline;
				    vertical-align: 1px;
				}
				
				fieldset legend {
				    margin: 0px;
				    padding: 0px 5px;
				    font-size: 11px;
				}
				
				fieldset span {
				    display: block;
				    margin: 5px 0px 0px 0px;
				    padding: 0px;
				    font-size: 10px;
				    font-weight: bold;
				    color: #a47e3c;
				}
				
				form {
				    margin: 0px;
				    padding: 0px;
				}
				
				label {
				    display: block;
				    font-size: 11px;
				}
				
				label.erro {
				    color: #a47e3c;
				}
				
				select {
				    margin: 2px 0px 0px 0px;
				    height: 20px;
				    font-family: Verdana, Arial, Helvetica, sans-serif;
				    font-size: 12px;
				    line-height: 20px;
				    vertical-align: middle;
				    border: 1px solid #999;
				}
				
				select[multiple="multiple"] {
				    height: auto !important;
				}
				
				textarea {
				    margin: 2px 0px 0px 0px;
				    padding: 5px;
				    font-family: Verdana, Arial, Helvetica, sans-serif;
				    font-size: 12px;
				    border: 1px solid #999999;
				    resize: none;
				}
				
				div.modulo_titulo {
				    margin: 0px 20px;
				    padding: 0px 10px;
				    height: 30px;
				    line-height: 30px;
				    font-size: 14px;
				    font-weight: bold;
				    vertical-align: middle;
				    border: 1px solid #94adc2;
				    border-bottom: none!important;
				    background: url('../../../images/fundo.gif');
				}
				
				div.modulo_conteudo {
				    margin: 0px 20px;
				    padding: 20px 0px;
				    border: 1px solid #94adc2;
				    border-top: 1px solid #94adc2;
				    border-bottom: 1px solid #94adc2;
				}
				
				div.bloco_titulo {
				    margin: 0px 20px;
				    padding: 0px 10px;
				    height: 25px;
				    line-height: 25px;
				    font-size: 12px;
				    font-weight: bold;
				    vertical-align: middle;
				    border: 1px solid #94adc2;
				    border-bottom: none!important;
				    background: #e6eaee;
				}
				
				div.bloco_conteudo {
				    margin: 0px 20px;
				    padding: 0px;
				    border: 1px solid #94adc2;
				}

				
				div.bloco_rodape {
				    margin: 0px 20px;
				    padding: 0px;
				    height: 1px;
				    background: #94adc2;
				}
				
				div.bloco_acoes, div.bloco_mensagens {
				    margin: 0px 20px;
				    padding: 5px 0px;
				    text-align: center;
				    border: 1px solid #94adc2;
				    border-top: none!important;
				    background: #bad0e5;
				}
				
				div.bloco_acoes p, div.bloco_mensagens p {
				    margin: 0px;
				    padding: 0px;
				    font-size: 11px;
				}
				
				div.conteudo {
				    margin: 0px;
				    padding: 5px 20px;
				    font-size: 11px;
				}
				
				div.conteudo fieldset {
				    float: none;
				    margin: 15px 0px;
				}
				
				div.conteudo table {
				    width: 100%;
				    font-size: 11px;
				    border-collapse: collapse;
				}
				
				div.conteudo table td {
				    padding: 5px;
				    border: 1px solid #FFFFFF;
				}
				
				div.conteudo table td.label {
				    font-weight: bold;
				}
				
				div.conteudo ul {
				    margin: 0px;
				    padding: 0px;
				    list-style: none;
				}
				
				div.conteudo ul li {
				    display: inline;
				    margin: 0px 20px 0px 0px;
				    padding: 0px;
				}
				
				div.formulario  {
				    margin: 0px;
				    padding: 20px;
				}
				
				div.antigo .item-label,  div.antigo .item {
				    float: left;
				    width: 250px;
				    min-height: 24px;
				    margin: 5px 10px 5px 0px;
				    padding: 0px;
				    line-height: 24px;
				    text-align: right;
				}
				
				div.antigo .item-label {
				    width: 175px;
				}
				
				div.antigo .item input.campo, div.antigo .item select, div.antigo .item textarea {
				    width: 250px;
				}
				
				div.grafico {
				    display: block;
				    overflow: auto;
				    margin: 20px;
				    padding: 0px;
				    text-align: center;
				}
				
				div.listagem {
				    margin: 0px;
				    padding: 0px;
				}
				
				div.listagem table {
				    width: 100%;
				    font-size: 11px;
				    border-collapse: collapse;
				}
				
				div.listagem table input {
				    float: none;
				}
				
				div.listagem table input.centro {
				    text-align: center;
				}
				
				div.listagem table input.direita {
				    text-align: right;
				}
				
				div.listagem table input.mini {
				    width: 38px!important;
				}
				
				div.listagem table th {
				    text-align: center;
				}
				
				div.listagem table th.selecao {
				    width: 25px;
				}
				
				div.listagem table th.mini, div.listagem table th.acao {
				    width: 75px;
				}
				
				div.listagem table td.menor {
				    width: 100px;
				}
				
				div.listagem table td.medio {
				    width: 300px;
				}
				
				div.listagem table td.maior {
				    width: 500px;
				}
				
				div.listagem table th.esquerda {
				    text-align: left;
				}
				
				div.listagem table th.direita {
				    text-align: right;
				}
				
				div.listagem table th, div.listagem table td {
				    padding: 5px;
				    border: 1px solid #FFFFFF;
				}
				
				div.listagem table thead tr {
				    background: #bad0e5;
				}
				
				div.listagem table tfoot td {
				    background: #bad0e5;
				    font-weight: bold;
				    text-align: center;
				}
				
				div.listagem table tr.impar {
				    background: #ffffff;
				}
				
				div.listagem table tr.par {
				    background: #dee6f6;
				}
				
				div.listagem table td.agrupamento {
				    font-weight: bold;
				    text-align: center;
				    background: #bad0e5;
				}
				
				div.listagem table td.topo {
				    vertical-align: baseline;
				}
				
				div.listagem table td.esquerda {
				    text-align: left;
				}
				
				div.listagem table td.centro {
				    text-align: center;
				}
				
				div.listagem table td.direita {
				    text-align: right;
				}
				
				div.listagem table div.listagem {
				    margin: 10px;
				}
				
				div.carregando {
				    margin: 20px;
				    padding: 0px;
				    height: 51px;
				    background: url('../../../modulos/web/images/loading.gif') no-repeat top center;
				}
				
				div.mensagem {
				    margin: 0px 20px 20px 20px;
				    padding: 12px 10px 12px 35px;
				    font-size: 12px;
				}
				
				div.alerta {
				    color: #a47e3c;
				    border: 1px solid #a47e3c;
				    background: #fcf8e3 url('../../../images/icon_alert.png') 10px 11px no-repeat;
				}
				
				div.erro {
				    color: #953b39;
				    border: 1px solid #953b39;
				    background: #f2dede url('../../../images/icon_error.png') 10px 11px no-repeat;
				}
				
				div.info {
				    color: #2d6987;
				    border: 1px solid #2d6987;
				    background: #d9edf7 url('../../../images/icon_info.png') 10px 11px no-repeat;;
				}
				
				div.sucesso {
				    color: #356635;
				    border: 1px solid #356635;
				    background: #dff0d8  url('../../../images/icon_success.png') 10px 11px no-repeat;;
				}
				
				div.texto {
				    color: #646464;
				    border: 1px solid #646464;
				    background: #e6e6e6;
				}
				
				div.ordenacao {
				    margin: 20px;
				    padding: 0px;
				}
				
				div.ordenacao .campo {
				    float: right;
				    margin: 5px 0px 5px 10px;
				}
				
				div.paginacao {
				    margin: 20px;
				    padding: 0px;
				    text-align: center;
				}
				
				div.paginacao ul {
				    margin: 0px;
				    padding: 0px;
				    list-style: none;
				}
				
				div.paginacao ul li {
				    display: inline-block;
				    margin: 0px;
				    padding: 5px 10px;
				    font-size: 12px;
				    border: 1px solid #94adc2;
				    background: #ffffff;
				}
				
				div.paginacao ul li.atual {
				    background: #e6eaee;
				}
				
				div.paginacao ul li.texto {
				    border: 1px solid #ffffff;
				}
				
				div.paginacao ul li a:hover {
				    text-decoration: none;
				}
				
				div.separador {
				    margin: 0px;
				    padding: 0px;
				    width: 20px;
				    height: 20px;
				    font-size: 1px;
				    line-height: 1px;
				}
				
				div.clear {
				    margin: 0;
				    padding: 0;
				    clear: both;
				}
				
				/*fieldset.medio {
				        width: 227px !important;
				}*/
				
				fieldset.medio {
				    width: 225px !important;
				}
				
				/*fieldset.maior {
				        width: 356px !important;
				}*/
				
				fieldset.maior {
				    width: 358px !important;
				}
				
				fieldset.maior input.mini {
				    width: 38px !important;
				}
				
				fieldset.opcoes-display-block {
				    padding-top: 10px;
				}
				
				fieldset.opcoes-display-block input {
				    float: left;
				    clear: left;
				}
				
				fieldset.opcoes-display-block label {
				    float: left;
				    clear: right;
				    margin: 3px 0 6px 0;
				}
				
				img.icone {
				    margin: 0px 1px;
				    width: 16px;
				    text-decoration: none;
				}
				
				img.carregando {
				    position: absolute;
				    margin: 4px 0px 0px -40px;
				}
				
				img.carregando-input {
				    position: absolute;
				    top: 18px;
				    right: 9px;
				}
				
				input.campo {
				    margin: 2px 0px 0px 0px;
				    padding: 0px 5px;
				    height: 20px;
				    font-family: Verdana, Arial, Helvetica, sans-serif;
				    font-size: 12px;
				    line-height: 20px;
				    vertical-align: middle;
				    border: 1px solid #999;
				}
				
				input.carregando, select.carregando, textarea.carregando {
				    position: relative;
				}
				
				input.erro, select.erro, textarea.erro {
				    color: #a47e3c;
				    border: 1px solid #a47e3c;
				    background-color: #fcf8e3;
				}
				
				ul.bloco_opcoes {
				    display: block;
				    list-style: none;
				    margin: 0px 20px;
				    padding: 0px;
				}
				
				ul.bloco_opcoes li {
				    display: inline-block;
				    margin: 0px -3px 0px 0px;
				    height: 29px;
				    line-height: 29px;
				    font-size: 10px;
				    text-align: center;
				    vertical-align: middle;
				    border: 1px solid #94adc2;
				    border-bottom: none!important;
				    background: url('../../../images/fundo.gif');
				}
				
				ul.bloco_opcoes li.ativo {
				    margin-bottom: -1px;
				    height: 30px;
				    background: #e6eaee;
				}
				
				ul.bloco_opcoes li a {
				    display: block;
				    padding: 0px 20px;
				    text-decoration: none;
				}
				
				.campo {
				    float: left;
				    margin: 5px 10px 5px 0;
				    padding: 0px;
				    position: relative;
				}
				
				.campo span {
				    margin: 0px;
				    padding: 0px;
				    font-size: 10px;
				    font-weight: bold;
				    color: #a47e3c;
				    width: 118px;
				    display: block;
				}
				
				.label-periodo {
				    font-size: 11px;
				    padding: 0;
				    left: 121px;
				    position: absolute !important;
				    top: 13px;
				}
				
				/*.menor {
				        width: 120px;
				}*/
				
				.menor {
				    width: 120px;
				}
				
				.menor input.campo, .menor select {
				    width: 120px;
				}
				
				/*.menor select {
				        width: 118px;
				}*/
				
				.medio {
				    width: 250px;
				}
				
				.medio textarea, .medio input.campo, .medio select {
				    width: 250px;
				}
				
				/*.medio select {
				        width: 248px;
				}*/
				
				/*.maior {
				        width: 380px;
				}*/
				
				.maior {
				    width: 380px;
				}
				
				.maior textarea, .maior input.campo, .maior select {
				    width: 380px;
				}
				
				/*.maior select {
				        width: 378px;
				}*/
				
				.data, .mes_ano {
				    width: 120px;
				}
				
				.data input.campo, .mes_ano input.campo {
				    width: 90px;
				    padding: 0;
				}
				
				/*.periodo {
				    width: 250px;
				    position: relative;
				}*/
				
				.periodo {
				    width: 250px;
				    position: relative;
				}
				
				.periodo img {
				    float: left !important;
				    margin-left: 5px;
				}
				
				.periodo .inicial {
				    float: left;
				    margin-right: 32px;
				}
				
				.periodo .final {
				    float: left;
				}
				
				.periodo input.campo {
				    width: 79px !important;
				}
				
				.data img, .mes_ano img {
				    float: right;
				    margin-top: 2px;
				    padding: 0px;
				    width: 25px;
				    height: 20px;
				    border: 0px;
				}
				
				.pesquisa {
				    width: 250px;
				}
				
				.pesquisa input.campo {
				    width: 159px;
				    margin-right: 4px;
				    float: left;
				}
				
				.pesquisa button {
				    margin: 1px 0 0;
				    vertical-align: middle;
				    float: right;
				}
				
				.pesquisaMaior {
				    width: 380px;
				}
				
				.pesquisaMaior input.campo {
				    width: 299px;
				    margin-right: 10px;
				}
				
				.pesquisaMaior button {
				    margin: 1px 0 0;
				    vertical-align: middle;
				    width: 71px;
				}
				
				.centro {
				    text-align: center;
				}
				
				.desabilitado {
				    color: #777777;
				    background-color: #eee;
				    border-color: #999;
				    cursor: not-allowed;
				}
				
				.invisivel {
				    display: none;
				}
				
				img {
				    border: none;
				}
				
				img.meio {
				    vertical-align: text-bottom;
				}
				
				.negrito {
				    font-weight: bold;
				}
				
				.no-float {
				    float: none !important;
				}
				
				/* jQuery UI */
				.ui-tooltip {
				    font-size: 12px!important;
				}
				
				.ui-datepicker {
				    font-size: 11px!important;
				}
				
				.ui-autocomplete-loading {
				    background: white url('../../../modulos/web/images/ajax-loader-circle.gif') right center no-repeat;
				}
				
				.ui-widget {
				    font: 12px Arial !important;
				}
				
				.ui-menu .ui-menu-item a {
				    cursor: pointer;
				}
				
				.ui-helper-hidden-accessible {
				    display: none !important;
				}
				
				.campo span {
				    width: 241px !important;
				}
				
				div.campo {
				    position: relative;
				}
				
				ul.ui-autocomplete {
				    width: 250px !important;
				}
    	 </style>
    	   
            </head>
    	       <body style="padding: 0px !important; margin: 0px !important; border: 0px important;">
    	            

                        <?php
                        if (!$isReembolso) {
                        ?>
                            <div class="bloco_conteudo" style="width: 743px !important;">
                            <div class="bloco_titulo" style="padding: 0px important; margin: 0px !important;">Dados Principais</div>
                            <div class="listagem">
                                <table>
                                    <tbody>
						                <tr class="impar">
						                    <td class="menor esquerda"><?php echo utf8_encode('Número da Requisição') ?>:</th>
					                    	<td class="medio esquerda"><?php echo $adioid ?></th>
					                    </tr>
					                     <tr class="par">
					                    	<td class="menor esquerda">Nome:</th>
					                    	<td class="medio esquerda"><?php echo $nome_solicitante ?></th>
					                    </tr>
					                     <tr class="impar">
					                    	<td class="menor esquerda">CPF:</th>
					                    	<td class="medio esquerda"><?php echo $cpf ?></th>
					                    </tr>
					                    <tr class="par">
					                    	<td class="menor esquerda">Motivo:</th>
					                    	<td class="medio esquerda"><?php echo $motivo ?></th>
					                    </tr>
					                    <tr class="impar">
					                    	<td class="menor esquerda"><?php echo utf8_encode('Data de Solicitação') ?>:</th>
					                    	<td class="medio esquerda"><?php echo $data_solicitacao ?></th>
					                    </tr>
					                    <tr class="par">
					                    	<td class="menor esquerda">Centro de Custo: </th>
					                    	<td class="medio esquerda"><?php echo $centro_custo ?></th>
					                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="bloco_conteudo" style="width: 743px !important;">
                        <div class="bloco_titulo" style="padding: 0px important; margin: 0px !important;"><?php echo utf8_encode('Relatório de Viagem') ?></div>
                        <div class="listagem">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="centro">Data</th>
                                        <th class="medio centro">Tipo</th>
                                        <th class="medio centro">Nota</th>
                                        <th class="medio centro">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    if ($_SESSION['prestacao_contas'][$adioid]['itens']) :
                                    
                                        foreach ($_SESSION['prestacao_contas'][$adioid]['itens'] as $item) : 
                                        
                                        $class = ($class == 'impar') ? 'par' : 'impar'; ?>
                                        
                                        <tr class="<?php echo $class?>">
                                        <td class="centro"><?php echo $item['data_despesa'] ?></td>
                                        <?php if($isReembolso) { ?>
                                        <td><?php echo $item['tipo_despesa'] ?></td>
                                        <?php } else { ?>
                                        <td><?php echo utf8_encode($item['tipo_despesa']) ?></td>
                                        <?php } ?>
                                        <td class="direita"><?php echo trim($item['numero_nota']) ?></td>
                                        <td class="direita"><?php echo trim(number_format($item['valor_despesa'], 2, ',', '.')) ?></td>
                                    </tr>
                                 <?php
                                        endforeach;
                                    endif; ?>  
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            Recebido: <?php echo number_format($valor_recebido, 2, ',', '.') ?> &nbsp;&nbsp;&nbsp;
                                            Gasto: <?php echo number_format($valor_itens, 2, ',', '.') ?> &nbsp;&nbsp;&nbsp;
                                            <?php echo $a_receber_a_devolver ?> &nbsp;&nbsp;&nbsp;
                                        </td>
                                    </tr>
                                </tfoot>
                                </table>
                                </div>
                            </div>

                            <br /><br /><br />
                            <table align="center" border="0" width="90%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="label_fr" align="left" style="font-size: 12px;">
                                        Data: _____/_____/________
                                    </td>
                                    <td class="label_fr" align="right" style="font-size: 12px;">
                                        ASS. DO GESTOR:_____________________________________________
                                    </td>
                                </tr>
                            </table>

                        <?php
                        } ?>  
			            
                    <?php if ($isReembolso) { ?>
                        <div class="bloco_conteudo">
                        <div class="listagem">
                            <table border="0">
                                <tr class="impar">
                                    <td class="menor esquerda">Solicitante:</td>
                                    <td class="medio esquerda"><?php echo $nome_solicitante ?></td>
                                </tr>
                                <tr class="par">
                                    <td class="menor esquerda">Empresa: </td>
                                    <td class="medio esquerda"><?php echo $empresa ?></td>
                                </tr>
                                <tr class="impar">
                                    <td class="menor esquerda">Centro de Custo: </td>
                                    <td class="medio esquerda"><?php echo $centro_custo ?></td>
                                </tr>
                                <tr class="par">
                                    <td class="menor esquerda">Justificativa:</td>
                                    <td class="medio esquerda"><?php echo $motivo ?></td>
                                </tr>
                                <tr class="impar">
                                    <td class="menor esquerda">Tipo de Solicitação:</td>
                                    <td class="medio esquerda">Reembolso</td>
                                </tr>
                            </table>
                            <table width="500" style="border: 0px !important;">
                                <thead>
                                    <tr>
                                        <td class="centro">Data</td>
                                        <td class="medio centro">Tipo</td>
                                        <td class="medio centro">Nota</td>
                                        <td class="medio centro">Valor</td>
                                    </tr>
                                </thead>
                                <?php
                                    if ($_SESSION['prestacao_contas'][$adioid]['itens']) :
                                    
                                        foreach ($_SESSION['prestacao_contas'][$adioid]['itens'] as $item) : 
                                        
                                        $class = ($class == 'impar') ? 'par' : 'impar'; ?>
                                        
                                        <tr class="<?php echo $class?>">
                                        <td class="centro"><?php echo $item['data_despesa'] ?></td>
                                        <?php if($isReembolso) { ?>
                                        <td><?php echo $item['tipo_despesa'] ?></td>
                                        <?php } else { ?>
                                        <td><?php echo utf8_encode($item['tipo_despesa']) ?></td>
                                        <?php } ?>
                                        <td class="direita"><?php echo trim($item['numero_nota']) ?></td>
                                        <td class="direita"><?php echo trim(number_format($item['valor_despesa'], 2, ',', '.')) ?></td>
                                    </tr>
                                 <?php
                                    endforeach;
                                endif; ?> 
                                <tfoot> 
                                    <tr>
                                        <td colspan="4">
                                            Total das Despesas: <?php echo number_format($valor_itens, 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                 </tfoot>
                            </table>
                        </div>
                    </div>
                    <?php
                    } ?>
	           
            </body>
        </html>

        <?php

            $relatorio_viagem = ob_get_contents();

            ob_end_clean();

            if ($isReembolso) {
                return $relatorio_viagem;
            }

            echo $relatorio_viagem;
            exit;
    	}
    }

}

