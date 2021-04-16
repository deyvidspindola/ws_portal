<?php
include_once _SITEDIR_.'lib/Components/PgDataList.php';
//include_once(_SITEDIR_.'lib/html2pdf/html2pdf.class.php');

include_once (_SITEDIR_.'lib/tcpdf_php4/config/lang/eng.php');
include_once (_SITEDIR_.'lib/tcpdf_php4/tcpdf.php');


/**
 * Classe RelOcorrenciaNovoIrvCongelado.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 */
class RelOcorrenciaNovoIrvCongelado {

    /**
     * Objeto DAO da classe.
     *
     * @var CadExemploDAO
     */
    private $dao;


    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de sucesso para inserção do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";

    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Registro alterado com sucesso.";

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

        $this->view->resultados = true;

        //Mensagem
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = '';

        //Filtros/parametros utlizados na view
        $this->view->parametros = '';

        //Status de uma transação
        $this->view->status = false;

        $this->view->zonas = array(
                                0       => ' ',
                                1       => 'LESTE',
                                2       => 'OESTE',
                                3       => 'NORTE',
                                4       => 'SUL',
                                5       => 'CENTRAL',
                                6       => 'SEM REFERÊNCIA'
                            );

        $this->view->parametros->arquivo_csv = '';

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

            $this->mensagens();

            if (isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar') {

                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);

            } else if ($_SESSION['pesquisa']['usarSessao'] && $_GET['acao'] == 'pesquisar') {
                $this->view->parametros = (object) $_SESSION['pesquisa'];
                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);

            }  else {
                $this->limparSessaoPesquisa();
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Inicializa os dados
        $this->inicializarParametros();
        unset($_SESSION['congelamento']['item']);

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/index.php";
    }


    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    public function tratarParametrosPesquisa() {

        $temp = array();

        if (isset($_POST['acao']) && $_POST['acao'] = 'pesquisar') {
            foreach ($_POST as $key => $value) {
                if (isset($_POST[$key])) {
                    $temp[$key] = trim($_POST[$key]);
                } elseif (isset($_SESSION['pesquisa'][$key])) {
                    $temp[$key] = trim($_SESSION['pesquisa'][$key]);
                }
                $_SESSION['pesquisa'][$key] = $temp[$key];
            }
        }

        $_SESSION['pesquisa']['usarSessao'] = TRUE;

        return (object) $_SESSION['pesquisa'];
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    private function tratarParametros() {
        $retorno = new stdClass();
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
		$this->view->parametros->ococdtipo_relatorio  = isset($this->view->parametros->ococdtipo_relatorio) && !empty($this->view->parametros->ococdtipo_relatorio) ? trim($this->view->parametros->ococdtipo_relatorio) : "";
        $this->view->parametros->ococdperiodo_inicial = isset($this->view->parametros->ococdperiodo_inicial) && !empty($this->view->parametros->ococdperiodo_inicial) ? trim($this->view->parametros->ococdperiodo_inicial) : "";
        $this->view->parametros->ococdperiodo_final   = isset($this->view->parametros->ococdperiodo_final) && !empty($this->view->parametros->ococdperiodo_final) ? trim($this->view->parametros->ococdperiodo_final) : "";

        $this->view->parametros->item                 = isset($_SESSION['congelamento']['item']) && !empty($_SESSION['congelamento']['item']) ? $_SESSION['congelamento']['item'] : array();
        $this->view->parametros->filtrar_motivo       = isset($this->view->parametros->filtrar_motivo ) && !empty($this->view->parametros->filtrar_motivo) ? trim($this->view->parametros->filtrar_motivo) : '';
        $this->view->parametros->filtrar_status       = isset($this->view->parametros->filtrar_status) && !empty($this->view->parametros->filtrar_status) ? trim($this->view->parametros->filtrar_status) : '';
        $this->view->parametros->filtrar_tipo_veiculo = isset($this->view->parametros->filtrar_tipo_veiculo) && !empty($this->view->parametros->filtrar_tipo_veiculo) ? trim($this->view->parametros->filtrar_tipo_veiculo) : '';
        $this->view->parametros->filtrar_estado       = isset($this->view->parametros->filtrar_estado) && !empty($this->view->parametros->filtrar_estado) ? trim($this->view->parametros->filtrar_estado) : '';
        $this->view->parametros->filtrar_marca        = isset($this->view->parametros->filtrar_marca) && !empty($this->view->parametros->filtrar_marca) ? trim($this->view->parametros->filtrar_marca) : '';
        $this->view->parametros->filtrar_modelo       = isset($this->view->parametros->filtrar_modelo) && !empty($this->view->parametros->filtrar_modelo) ? trim($this->view->parametros->filtrar_modelo) : '';
        $this->view->parametros->filtrar_classe_equipamento = isset($this->view->parametros->filtrar_classe_equipamento) && !empty($this->view->parametros->filtrar_classe_equipamento) ? trim($this->view->parametros->filtrar_classe_equipamento) : '';
        $this->view->parametros->filtrar_classe_contrato    = isset($this->view->parametros->filtrar_classe_contrato) && !empty($this->view->parametros->filtrar_classe_contrato) ? trim($this->view->parametros->filtrar_classe_contrato) : '';
        $this->view->parametros->filtrar_tipo_ocorrencia    = isset($this->view->parametros->filtrar_tipo_ocorrencia) && !empty($this->view->parametros->filtrar_tipo_ocorrencia) ? trim($this->view->parametros->filtrar_tipo_ocorrencia) : '';
        $this->view->parametros->filtrar_forma_notificacao  = isset($this->view->parametros->filtrar_forma_notificacao) && !empty($this->view->parametros->filtrar_forma_notificacao) ? trim($this->view->parametros->filtrar_forma_notificacao) : '';
        $this->view->parametros->filtrar_modalidade_contrato= isset($this->view->parametros->filtrar_modalidade_contrato) && !empty($this->view->parametros->filtrar_modalidade_contrato) ? trim($this->view->parametros->filtrar_modalidade_contrato) : '';
        $this->view->parametros->filtrar_regiao             = isset($this->view->parametros->filtrar_regiao) && !empty($this->view->parametros->filtrar_regiao) ? trim($this->view->parametros->filtrar_regiao) : '';
        $this->view->parametros->filtrar_classe_grupo       = isset($this->view->parametros->filtrar_classe_grupo) && !empty($this->view->parametros->filtrar_classe_grupo) ? trim($this->view->parametros->filtrar_classe_grupo) : '';
        $this->view->parametros->filtrar_atendente          = isset($this->view->parametros->filtrar_atendente) && !empty($this->view->parametros->filtrar_atendente) ? trim($this->view->parametros->filtrar_atendente) : '';
        $this->view->parametros->filtrar_equipamento_projeto    = isset($this->view->parametros->filtrar_equipamento_projeto) && !empty($this->view->parametros->filtrar_equipamento_projeto) ? trim($this->view->parametros->filtrar_equipamento_projeto) : '';
        $this->view->parametros->filtrar_motivo_equ_sem_contato = isset($this->view->parametros->filtrar_motivo_equ_sem_contato) && !empty($this->view->parametros->filtrar_motivo_equ_sem_contato) ? trim($this->view->parametros->filtrar_motivo_equ_sem_contato) : '';
        $this->view->parametros->filtrar_apoio              = isset($this->view->parametros->filtrar_apoio) && !empty($this->view->parametros->filtrar_apoio) ? trim($this->view->parametros->filtrar_apoio) : '';
        $this->view->parametros->filtrar_instalado_cargo_track =isset($this->view->parametros->filtrar_instalado_cargo_track) && !empty($this->view->parametros->filtrar_instalado_cargo_track) ? trim($this->view->parametros->filtrar_instalado_cargo_track) : '';
        $this->view->parametros->filtrar_veiculo_carregado  = isset($this->view->parametros->filtrar_veiculo_carregado) && !empty($this->view->parametros->filtrar_veiculo_carregado) ? trim($this->view->parametros->filtrar_veiculo_carregado) : '';
        $this->view->parametros->filtrar_tipo_periodo       = isset($this->view->parametros->filtrar_tipo_periodo) && !empty($this->view->parametros->filtrar_tipo_periodo) ? trim($this->view->parametros->filtrar_tipo_periodo) : '';
        $this->view->parametros->filtrar_estado_recuperacao = isset($this->view->parametros->filtrar_estado_recuperacao) && !empty($this->view->parametros->filtrar_estado_recuperacao) ? trim($this->view->parametros->filtrar_estado_recuperacao) : '';
        $this->view->parametros->filtrar_recuperado_apoio   = isset($this->view->parametros->filtrar_recuperado_apoio) && !empty($this->view->parametros->filtrar_recuperado_apoio) ? trim($this->view->parametros->filtrar_recuperado_apoio) : '';
        $this->view->parametros->filtrar_tipo_pessoa        = isset($this->view->parametros->filtrar_tipo_pessoa) && !empty($this->view->parametros->filtrar_tipo_pessoa) ? trim($this->view->parametros->filtrar_tipo_pessoa) : '';
        $this->view->parametros->filtrar_tipo_cidade        = isset($this->view->parametros->filtrar_tipo_cidade) && !empty($this->view->parametros->filtrar_tipo_cidade) ? trim($this->view->parametros->filtrar_tipo_cidade) : '';
        $this->view->parametros->filtrar_classe             = isset($this->view->parametros->filtrar_classe) && !empty($this->view->parametros->filtrar_classe) ? trim($this->view->parametros->filtrar_classe) : '';
        $this->view->parametros->filtrar_cliente            = isset($this->view->parametros->filtrar_cliente) && !empty($this->view->parametros->filtrar_cliente) ? trim($this->view->parametros->filtrar_cliente) : '';
        $this->view->parametros->filtrar_placa              = isset($this->view->parametros->filtrar_placa) && !empty($this->view->parametros->filtrar_placa) ? trim($this->view->parametros->filtrar_placa) : '';
        $this->view->parametros->filtrar_corretora          = isset($this->view->parametros->filtrar_corretora) && !empty($this->view->parametros->filtrar_corretora) ? trim($this->view->parametros->filtrar_corretora) : '';
        $this->view->parametros->filtrar_chassi             = isset($this->view->parametros->filtrar_chassi) && !empty($this->view->parametros->filtrar_chassi) ? trim($this->view->parametros->filtrar_chassi) : '';
        $this->view->parametros->filtrar_valor_carga_condicao  = isset($this->view->parametros->filtrar_valor_carga_condicao) && !empty($this->view->parametros->filtrar_valor_carga_condicao) ? trim($this->view->parametros->filtrar_valor_carga_condicao) : '';
        $this->view->parametros->filtrar_valor_carga        = isset($this->view->parametros->filtrar_valor_carga) && !empty($this->view->parametros->filtrar_valor_carga) ? trim($this->view->parametros->filtrar_valor_carga) : '';
        $this->view->parametros->filtrar_cpf_cnpj           = isset($this->view->parametros->filtrar_cpf_cnpj) && !empty($this->view->parametros->filtrar_cpf_cnpj) ? trim($this->view->parametros->filtrar_cpf_cnpj) : '';
        $this->view->parametros->filtrar_bo                 = isset($this->view->parametros->filtrar_bo) && !empty($this->view->parametros->filtrar_bo) ? trim($this->view->parametros->filtrar_bo) : '';
        $this->view->parametros->filtrar_exibir_endereco    = isset($this->view->parametros->filtrar_exibir_endereco) && !empty($this->view->parametros->filtrar_exibir_endereco) ? trim($this->view->parametros->filtrar_exibir_endereco) : '';





        $this->view->parametros->idsCongelados =  array();
        if(isset($this->view->parametros->item) && count($this->view->parametros->item)) {
            foreach ($this->view->parametros->item  as $id => $tipo) {
                $this->view->parametros->idsCongelados[]=$id;

                if(empty($this->view->parametros->ococdtipo_relatorio))
                    $this->view->parametros->ococdtipo_relatorio=trim($tipo);
            }
        }

        if((empty($this->view->parametros->ococdperiodo_inicial)
           || empty($this->view->parametros->ococdperiodo_final))
           && $this->view->parametros->congeladosID) {
            $periodo = $this->dao->pesquisarPeriodoAgrupado($this->view->parametros->congeladosID);
            if($periodo->ococdtipo_relatorio) {
                $this->view->parametros->ococdperiodo_inicial = $periodo->ococdperiodo_inicial;
                $this->view->parametros->ococdperiodo_final   = $periodo->ococdperiodo_final;
                $this->view->parametros->ococdtipo_relatorio = trim($periodo->ococdtipo_relatorio);
            }
        }

        $motivosMacro = false;
        if ($this->view->parametros->ococdtipo_relatorio == 'M') {
            $motivosMacro = true;
        }

        /**
         * Array de mapeamento dos tipos de relatório
         */
        $this->view->tiposRelatorio = array(
        		'A'	=> "analitico",
        		'P'	=> "apoio",
        		'D'	=> "apoio_detalhado",
        		'M'	=> "macro",
        		'S'	=> "sintetico",
				'R'	=> "sintetico_resumido"
        );


        /**
         * Array de mapeamento dos titulos de relatório
         */
        $this->view->titulosRelatorio = array(
                'A' => "Acionamentos de Apoio Analítico",
                'P' => "Acionamentos de Apoio",
                'D' => "Acionamentos de Apoio Detalhado",
                'M' => "Acionamentos de Apoio Macro",
                'S' => "Acionamentos de Apoio Sintético",
                'R' => "Acionamentos de Apoio Sintético Resumido"
        );

    }

    public function inicializarFormFiltros() {

        $this->view->parametros->listarMotivos = $this->dao->buscarMotivos($motivosMacro);
        $this->view->parametros->listarMarcas  = $this->dao->buscarMarcas();
        $this->view->parametros->listarModelos = $this->dao->buscarModelos($this->view->parametros->filtrar_marca);
        $this->view->parametros->listarCidades = $this->dao->buscarCidades($this->view->parametros->filtrar_estado);
        $this->view->parametros->listarSeguradorasTipoContrato  = (array) $this->dao->buscarSeguradorasTipoContrato();
        $this->view->parametros->listarSeguradorasSeguradora    = (array) $this->dao->buscarSeguradorasSeguradora();
        $this->view->parametros->listarClassesEquipamento       = (array) $this->dao->buscarClassesEquipamento();
        $this->view->parametros->listarTiposProposta            = (array) $this->dao->buscarTipoPropostas();
        $this->view->parametros->buscarTipoContratos            = (array) $this->dao->buscarTipoContratos();

        $this->view->parametros->listarTiposOcorrencia          = array(
            'O' => "OCORRÊNCIA",
            'A' => "ACIONAMENTO CERCA"
        );
        $this->view->parametros->listarFormaNotificacao         = (array) $this->dao->buscarFormaNotificacao();
        $this->view->parametros->listarModalidadeContrato       = array(
            'L' => "Locação",
            'V' => "Revenda"
        );
        $this->view->parametros->listarRegiao                   = array(
            "Centro-Oeste"  => "Centro-Oeste",
            "Nordeste"      => "Nordeste",
            "Norte"         => "Norte",
            "Sudeste"       => "Sudeste",
            "Sul"           => "Sul",
        );
        $this->view->parametros->listarClasseGrupo              = (array) $this->dao->buscarClasseGrupo();
        $this->view->parametros->listarAtendentes               = (array) $this->dao->buscarAtendentes();
        $this->view->parametros->listarEquipamentoProjeto       = (array) $this->dao->buscarEquipamentoProjetos();
        $this->view->parametros->listarMotivoEqupSemContato     = (array) $this->dao->buscarMotivoEquipSemContato();
        $this->view->parametros->listarApoio                    = (array) $this->dao->buscarListaApoio();
        $this->view->parametros->listarVeiculoCarregado         = array(
            't' => "Sim",
            'f' => "Não"
        );
        $this->view->parametros->listarInstaladoCargoTrack      = array(
            't' => "Sim",
            'f' => 'Não'
        );
        $this->view->parametros->listarTipoPeriodo              = array(
            'C' => "Data da Comunicação",
            'E' => "Data do Evento",
            'R' => "Data da Recuperação"
        );
        $this->view->parametros->listarRecuperadoApoio          = array(
            't' => "Sim",
            'f' => 'Não'
        );
        $this->view->parametros->listarTipoPessoa               = array(
            'F' => "PF",
            'J' => 'PJ'
        );
        $this->view->parametros->listarTipoResidencia           = array(
            'RC'=> "Residência Cliente",
            'E' => "Evento",
            'F' => "Recuperação"
        );

        $this->view->parametros->idsCongelados =  array();
        if(isset($this->view->parametros->item) && count($this->view->parametros->item)) {
            foreach ($this->view->parametros->item  as $id => $tipo) {
                $this->view->parametros->idsCongelados[]=$id;

                if(empty($this->view->parametros->ococdtipo_relatorio))
                    $this->view->parametros->ococdtipo_relatorio=trim($tipo);
            }
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

       //var_dump($filtros);exit;

        //if ($this->validarCamposPesquisa($filtros)) {
            $resultadoPesquisa = $this->dao->pesquisar($filtros);
       // }

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
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
        try{

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros();


            //Verificar se foi submetido o formulário e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST)) {
                $registroGravado = $this->salvar($this->view->parametros);
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

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {

            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/cadastrar.php";
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

            //Verifica se foi informado o ococdoid do cadastro
            if (isset($parametros->ococdoid) && intval($parametros->ococdoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->ococdoid = (int) $parametros->ococdoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->ococdoid);

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

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->ococdoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios da pesquisa.
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


        if (!isset($dados->ococdperiodo_inicial) || trim($dados->ococdperiodo_inicial) == '') {
            $camposDestaques[] = array(
                'campo' => 'ococdperiodo_inicial'
            );
            $error = true;
        }

        if (!isset($dados->ococdperiodo_final) || trim($dados->ococdperiodo_final) == '') {
            $camposDestaques[] = array(
                'campo' => 'ococdperiodo_final'
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        return true;
    }


    /**
     * Validar os campos obrigatórios da pesquisa.
     *
     * @param stdClass $dados Dados a serem validados
     *
     * @throws Exception
     *
     * @return void
     */
    private function validarCamposPesquisaCongelado(stdClass $dados) {


        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        if (!isset($dados->ococdperiodo_inicial) || trim($dados->ococdperiodo_inicial) == '') {
            $camposDestaques[] = array(
                'campo' => 'ococdperiodo_inicial'
            );
            $error = true;
        }

        if (!isset($dados->ococdperiodo_final) || trim($dados->ococdperiodo_final) == '') {
            $camposDestaques[] = array(
                'campo' => 'ococdperiodo_final'
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        return !$error;
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

            //Verifica se foi informado o ococdoid
            if (!isset($parametros->ococdoid) || trim($parametros->ococdoid) == '') {
                $this->redirect('erro', self::MENSAGEM_ERRO_PROCESSAMENTO,'pesquisar');
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->ococdoid = (int) $parametros->ococdoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->ococdoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {
                $this->redirect('sucesso','Relatório excluído com sucesso.','pesquisar');
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->redirect('erro', $e->getMessage(),'pesquisar');

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->redirect('erro', $e->getMessage(),'pesquisar');
        }
    }

    /**
     * Executa a reativação do registro.
     *
     * @return void
     */
    public function reativar() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o ococdoid
            if (!isset($parametros->ococdoid) || trim($parametros->ococdoid) == '') {
                $this->redirect('erro', self::MENSAGEM_ERRO_PROCESSAMENTO,'pesquisar');
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->ococdoid = (int) $parametros->ococdoid;

            //Remove o registro
            $confirmacao = $this->dao->reativar($parametros->ococdoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {
                $this->redirect('sucesso','Relatório reativado com sucesso.','pesquisar');
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->redirect('erro', $e->getMessage(),'pesquisar');

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->redirect('erro', $e->getMessage(),'pesquisar');
        }
    }

    /**
     * Trata parametros para visualização de relatorios congelados.
     *
     * @return void
     */
    public function visualizarRelatorios() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();
            $_SESSION['congelamento']['item'] = $parametros->item;

            if (count($parametros->item) > 1) {
                if (count(array_count_values($parametros->item)) > 1) {
                    $this->redirect('alerta', 'Selecione apenas um tipo de relatório.','pesquisar');
                    exit;
                }
            }

            $congeladosID = implode(",", array_keys($parametros->item));

            $this->limparSessaoPesquisa();
            $this->redirect('','','visualizacaoRelatorioCongelado&id=' . $congeladosID);

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->redirect('erro', $e->getMessage(),'pesquisar');

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->redirect('erro', $e->getMessage(),'pesquisar');
        }

    }


    public function visualizacaoRelatorioCongelado() {

        try {


            $this->mensagens();

            $this->view->parametros->congeladosID = isset($_GET['id']) && trim($_GET['id']) != '' ? trim ($_GET['id']) : '';

            //Inicializa os dados
            $this->inicializarParametros();


            if (isset($_POST['sub_acao'])){

                if (in_array(trim($_POST['sub_acao']), array('pesquisarCongelados','gerarPdf','gerar_csv') )) {

                    $this->limparSessaoPesquisa();
                    $this->view->parametros = $this->tratarParametrosPesquisa();

                    if($this->validarCamposPesquisaCongelado($this->view->parametros)) {
                        //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                        $this->view->dados = $this->pesquisarCongelados($this->view->parametros);
                        if (($this->view->parametros->ococdtipo_relatorio == 'R' )
                            || ($this->view->parametros->ococdtipo_relatorio == 'S')) {

                            $this->view->dadosResumo = $this->dao->buscarDadosSinteticoResumido($this->view->parametros);
                        }
                    }
                }
            } else if ($_SESSION['pesquisa']['usarSessao']) {

                if (in_array(trim($_POST['sub_acao']), array('pesquisarCongelados','gerarPdf','gerar_csv') )) {

                     $this->view->parametros = (object) $_SESSION['pesquisa'];

                    if($this->validarCamposPesquisaCongelado($this->view->parametros)) {
                        //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                        $this->view->dados = $this->pesquisarCongelados($this->view->parametros);
                        if (($this->view->parametros->ococdtipo_relatorio == 'R' )
                            || ($this->view->parametros->ococdtipo_relatorio == 'S')) {

                            $this->view->dadosResumo = $this->dao->buscarDadosSinteticoResumido($this->view->parametros);
                        }
                    }

                }

            } else {

                $this->view->parametros->congeladosID = isset($_GET['id']) && trim($_GET['id']) != '' ? trim ($_GET['id']) : '';
                //if($this->validarCamposPesquisaCongelado($this->view->parametros)) {
                    $this->view->dados = $this->pesquisarCongelados($this->view->parametros);
                    if (($this->view->parametros->ococdtipo_relatorio == 'R' )
                            || ($this->view->parametros->ococdtipo_relatorio == 'S')) {

                            $this->view->dadosResumo = $this->dao->buscarDadosSinteticoResumido($this->view->parametros);
                        }
               // }
                $this->limparSessaoPesquisa();
            }


            if (count($this->view->dados) == 0) {
            	throw new Exception("Nenhum registro encontrado.");
            }

            if($this->view->dados instanceof PgDataList){
                $dadosView = array();
                foreach($this->view->dados as $dados){
                    $dadosView[] = $dados;
                }

                if ($this->view->parametros->ococdtipo_relatorio == 'M') {
                    $this->view->dadosRelMacro = $this->formatarDados($dadosView);
                    $dadosView = $this->view->dadosRelMacro;
                }

                if ($this->view->parametros->ococdtipo_relatorio == 'P' 
                    || $this->view->parametros->ococdtipo_relatorio == 'D') {

                        $dadosView = $this->calcularTempoApoio($dadosView);
            }
                //echo "<pre>";print_r($this->view->parametros->ococdtipo_relatorio);exit;

            }


            if(trim($_POST['sub_acao'])=='gerar_csv') {

                if (($this->view->parametros->ococdtipo_relatorio == 'R' )
                        || ($this->view->parametros->ococdtipo_relatorio == 'S')) {

                    $dadosView['resumo'] = $this->view->dadosResumo;
                }

                $this->view->parametros->arquivo_csv = $this->gerarArquivoCSV($dadosView, $this->view->parametros);

                $this->view->resultados = false;


            } else if(trim($_POST['sub_acao'])=='gerarPdf'){
                $this->view->parametros->arquivo_pdf = $this->gerarPDf();
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        $this->inicializarFormFiltros();

        require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/formulario_pesquisa_congelados.php";
    }

    public function gerarPdf(){
        set_time_limit(0);

        //<a href=\"download.php?arquivo=/var/www/docs_temporario/$nomeArquivoCsv\" target=\"_self\">$nomeArquivoCsv</a>
        $viewRelatorio = $this->view->tiposRelatorio[$this->view->parametros->ococdtipo_relatorio];
        $arquivo = "docs_temporario/resultado_pesquisa_".$viewRelatorio.".pdf";
        $arquivo_resultado =  _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/resultado_pesquisa_".$viewRelatorio.".php";

        ob_start();
         include_once($arquivo_resultado);
        $html= ob_get_clean();


        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));//PDF_FONT_SIZE_MAIN
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));//PDF_FONT_SIZE_DATA

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        //$pdf->setLanguageArray($l);

        // Setando pagina para paisagem
        $pdf->setPageOrientation('L');

        $pdf->SetHeaderData(false, PDF_HEADER_LOGO_WIDTH, 'Relatório Ocorrências Novo IRV - Congelado.', $this->view->titulosRelatorio[$this->view->parametros->ococdtipo_relatorio] );

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('Arial', '', 8);

        // add a page
        $pdf->AddPage();

        $pdf->writeHTML($html, true, 0, true, 0);

        // reset pointer to the last page
        $pdf->lastPage();

        // ---------------------------------------------------------

        //Close and output PDF document
       $pdf->Output("/var/www/".$arquivo, 'F');
       return $arquivo;
    }


    public function listarMotivosAjax() {
        $motivosMacro = $_POST['motivos_macro'] == 1 ? true : false;
        echo $this->dao->buscarMotivos($motivosMacro);
    }

    public function listarModelosAjax() {
        $marcaId = intval($_POST['marca_id']);
        echo $this->dao->buscarModelos($marcaId);
    }

    public function listarCidadesAjax() {
        $uf = $_POST['uf'];
        echo $this->dao->buscarCidades($uf);
    }


    public function pesquisarCongelados($parametros) {

    	$resultado = null;
        $dataList = null;

       // if (!in_array($parametros->ococdtipo_relatorio, array('S','R'))) {
            $resultado = $this->dao->pesquisarCongelados($parametros->congeladosID, $parametros, true);
            $dataList = ($resultado!=null) ? new PgDataList($resultado) : array();
       // }
		return $dataList;
    }


    /**
    * Método responsável por limpar sessão de pesquisa
    *
    * @return void
    */
    public function limparSessaoPesquisa() {
        if (isset($_SESSION['pesquisa']) && is_array($_SESSION['pesquisa'])) {
            foreach ($_SESSION['pesquisa'] as $key => $value) {
                $_SESSION['pesquisa'][$key] = '';
            }
        }
    }

    public function mensagens() {
        if (isset($_SESSION['flash_message']) && count($_SESSION['flash_message'])) {

            if ($_SESSION['flash_message']['tipo'] == 'sucesso') {
                $this->view->mensagemSucesso = $_SESSION['flash_message']['mensagem'];
            }

            if ($_SESSION['flash_message']['tipo'] == 'erro') {
                $this->view->mensagemErro = $_SESSION['flash_message']['mensagem'];
            }

            if ($_SESSION['flash_message']['tipo'] == 'alerta') {
                $this->view->mensagemAlerta = $_SESSION['flash_message']['mensagem'];
            }

            unset($_SESSION['flash_message']);
            $this->view->parametros = '';
        }
    }


    public function redirect($tipoMensagem = "", $mensagem = "", $acao = "") {
        if (trim($tipoMensagem) != '' && trim($mensagem) != '') {
            $_SESSION['flash_message']['tipo'] = $tipoMensagem;
            $_SESSION['flash_message']['mensagem'] = $mensagem;
        }

        if (trim($acao) != '') {
            header('Location:rel_ocorrencia_novo_irv_congelado.php?acao=' . $acao);
        }

    }

    /**
     * Cria um arquivo CSV
     *
     * @return String
     * @throws Exception
     */
    public function gerarArquivoCSV($dados, $parametros) {

        require_once "lib/Components/CsvWriter.php";
		$diretorio = '/var/www/docs_temporario/';
        $equipe=""; //ococtelefone_emergencia
        $cliente=""; //ococtelefone_ococcliente
        $tempoApoio = "";
        $dadosLinha = array();

         if ( (count($dados) > 0) ) {

              $tipoRelatorio = $dados[0]->ococtipo_filtro;

              /*
               * Definições de acordo com o tipo de relatório
               */
              switch ($tipoRelatorio) {
                  case 'P':
                      $nomeRelatorio = "apoio";
                      $header = array(
                        'Data Acionamento',
                        'Placa',
                        'Cliente',
                        'Motivo',
                        'Forma Notificação',
                        'Status do Equipamento',
                        'Status',
                        'Recuperado',
                        'Contato',
                        'Cidade',
                        'Usuário'
                        );
                      break;

                   case 'D':
                      $nomeRelatorio = "apoio_detalhado";
                       $header = array(
                        'Data Acionamento',
                        'Placa',
                        'Cliente',
                        'Motivo',
                        'Forma Notificação',
                        'Status do Equipamento',
                        'Status',
                        'Recuperado',
                        'Contato',
                        'Cidade',
                        'Usuário'
                        );
                      break;

                   case 'A':
                       $nomeRelatorio = "analitico";
                       $header = array(
                            'Data Comunicação',
                            'Placa',
                            'Classe',
                            'Projeto',
                            'Cliente',
                            'DDD - Fone',
                            'Seguradora',
                            'Tipo Termo',
                            'Motivo',
                            'Forma Notificação',
                            'Status do Equipamento',
                            'Atendente',
                            'Nº BO',
                            'Tempo Aviso',
                            'Status',
                            'Concluído',
                            'Valor Veiculo',
                            'Tipo de Carga',
                            'Tipo de Proposta'
                            );
                      break;

                   case 'M':
                       $nomeRelatorio = "macro";
                       $header = array(
                            'Placa',
                            'Chassi',
                            'Tipo de Veículo',
                            'Cor',
                            'Ano',
                            'Marca',
                            'Modelo',
                            'Valor FIPE',
                            'Carregado?',
                            'Valor da Carga',
                            'Carga',
                            'Tipo de Carga',
                            'Embarcador',
                            'Seguradora da Carga',
                            'Cliente',
                            'CPF/CNPJ',
                            'Tipo Pessoa',
                            'Tipo Contrato',
                            'Cidade do Cliente',
                            'UF do Cliente',
                            'Classe',
                            'Instalado CARGO TRACCK?',
                            'Serial CT',
                            'Equipamento',
                            'Local de Instalação do Equipamento',
                            'Técnico Instalador',
                            'Motivo da Ocorrência',
                            'Forma da Notificação',
                            'Status',
                            'Forma de Recuperação',
                            'Status do Equipamento',
                            'Lat/Long Última Posição',
                            'Data Comunicação',
                            'Data de Roubo',
                            'Tempo de Aviso',
                            'Data/Hora Recuperado',
                            'Local do Evento',
                            'Bairro do Evento',
                            'Zona do Evento',
                            'Cidade do Evento',
                            'UF do Evento',
                            'Lat/Long do Evento',
                            'Local Recuperado',
                            'Bairro Recuperado',
                            'Zona Recuperado',
                            'Cidade Recuperado',
                            'Estado Recuperado',
                            'Lat/Long Recuperação',
                            'Equipe de Apoio Acionada',
                            'Recuperado pelo Apoio?',
                            'Tempo de Chegada de Apoio',
                            'Nr. B.O.'
                         );
                      break;

                   case 'S':
                       $nomeRelatorio = "sintetico";
                       $header = array();
                      break;

                   case 'R':
                       $nomeRelatorio = "sintetico_resumido";
                       $header = array();
                      break;
              }

              $arquivo = "rel_irv_congelado_". $nomeRelatorio."_".date('Ymd').".csv";


            try {
                if (is_dir($diretorio) && is_writable($diretorio)) {

                    // Gera CSV
                    $csvWriter = new CsvWriter($diretorio.$arquivo, ';', '', true);

                    //Cabeçalho
                    if(!empty($header)) {
                        $csvWriter->addLine($header);
                    }


                    $totalRegistros = count($dados);
                    $contarIteracao = 1;

                    switch ($tipoRelatorio) {
                        case 'P':

                            $hora = 0;
                            $minuto = 0;
                            $segundo = 0;
                            //Dados
                             foreach ($dados as $linha) {


                                 if (($cliente != $linha->ococcliente) || ($equipe != $linha->ococtelefone_emergencia)) {

                                     if (!empty($cliente)){
                                          $csvWriter->addLine("Tempo de apoio: " . $tempoApoio);

                                        $temp = explode(':',$tempoApoio);
                                        $hora += $temp[0];
                                        $minuto += $temp[1];
                                        $segundo += $temp[2];   
                                     }

                                     $cliente = $linha->ococcliente;

                                 }

                                 if($equipe != $linha->ococtelefone_emergencia) {

                                     $equipe=$linha->ococtelefone_emergencia;
                                     $csvWriter->addLine($equipe);
                                 }

                                 $dadosLinha[0] = $linha->data_comunicacao;
                                 $dadosLinha[1] = $linha->ococplaca;
                                 $dadosLinha[2] = $linha->ococcliente;
                                 $dadosLinha[3] = $linha->ococmotivo;
                                 $dadosLinha[4] = $linha->ocococorrencia_forma_notificacao;
                                 $dadosLinha[5] = $linha->ocococorrencia_motivo_equip_sem_contato;
                                 $dadosLinha[6] = $linha->ococstatus;

                                 if(!empty($linha->ococrecuperado)) {
                                     $dadosLinha[7] = ($linha->ococrecuperado == 't') ? 'Sim' : 'Não';
                                 } else {
                                     $dadosLinha[7] = '';
                                }

                                 $dadosLinha[8] = $linha->ococcontato;
                                 $dadosLinha[9] = $linha->ococcidade;
                                 $dadosLinha[10] = $linha->ococusuario;

                                 $csvWriter->addLine($dadosLinha);

                                 if ($contarIteracao == $totalRegistros) {
                                      $csvWriter->addLine("Tempo de apoio: " . $linha->tempo_apoio);
                                 }

                                 $tempoApoio = $linha->tempo_apoio;

                                 $contarIteracao++;

                             } // end foreach

                            $temp = explode(':',$tempoApoio);
                            $hora += $temp[0];
                            $minuto += $temp[1];
                            $segundo += $temp[2];

                            $segundoFinal = ($segundo%60);
                            $minutoFinal = floor($segundo/60) + $minuto;
                            $minutoFinal = ($minutoFinal%60);
                            $horaFinal = floor($minuto/60) + $hora;

                            $segundoFinal = (strlen("$segundoFinal") > 1) ? "$segundoFinal" : "0"."$segundoFinal";
                            $minutoFinal = (strlen("$minutoFinal") > 1) ? "$minutoFinal" : "0"."$minutoFinal";
                            $horaFinal = (strlen("$horaFinal") > 1) ? "$horaFinal" : "0"."$horaFinal";

                             $csvWriter->addLine("TEMPO TOTAL DE APOIO " . $horaFinal . ':' . $minutoFinal . ':' . $segundoFinal);

                            break;

                        case 'D':

                            $hora = 0;
                            $minuto = 0;
                            $segundo = 0;
                            //Dados
                             foreach ($dados as $linha) {


                                 if (($cliente != $linha->ococcliente) || ($equipe != $linha->ococtelefone_emergencia)) {

                                     if (!empty($cliente)){
                                          $csvWriter->addLine("Tempo de apoio: " . $tempoApoio);

                                            $temp = explode(':',$tempoApoio);
                                            $hora += $temp[0];
                                            $minuto += $temp[1];
                                            $segundo += $temp[2]; 
                                     }

                                     $cliente = $linha->ococcliente;

                                 }

                                 if($equipe != $linha->ococtelefone_emergencia) {

                                     $equipe=$linha->ococtelefone_emergencia;
                                     $csvWriter->addLine($equipe);
                                 }

                                 $dadosLinha[0] = $linha->data_comunicacao;
                                 $dadosLinha[1] = $linha->ococplaca;
                                 $dadosLinha[2] = $linha->ococcliente;
                                 $dadosLinha[3] = $linha->ococmotivo;
                                 $dadosLinha[4] = $linha->ocococorrencia_forma_notificacao;
                                 $dadosLinha[5] = $linha->ocococorrencia_motivo_equip_sem_contato;
                                 $dadosLinha[6] = $linha->ococstatus;

                                 if(!empty($linha->ococrecuperado)) {
                                     $dadosLinha[7] = ($linha->ococrecuperado == 't') ? 'Sim' : 'Não';
                                 } else {
                                     $dadosLinha[7] = '';
                                }

                                 $dadosLinha[8] = $linha->ococcontato;
                                 $dadosLinha[9] = $linha->ococcidade;
                                 $dadosLinha[10] = $linha->ococusuario;

                                 $csvWriter->addLine($dadosLinha);

                                 if ($contarIteracao == $totalRegistros) {
                                      $csvWriter->addLine("Tempo de apoio: " . $linha->tempo_apoio);
                                 }

                                 $tempoApoio = $linha->tempo_apoio;

                                 $contarIteracao++;

                             } // end foreach

                            $temp = explode(':',$tempoApoio);
                            $hora += $temp[0];
                            $minuto += $temp[1];
                            $segundo += $temp[2];

                            $segundoFinal = ($segundo%60);
                            $minutoFinal = floor($segundo/60) + $minuto;
                            $minutoFinal = ($minutoFinal%60);
                            $horaFinal = floor($minuto/60) + $hora;

                            $segundoFinal = (strlen("$segundoFinal") > 1) ? "$segundoFinal" : "0"."$segundoFinal";
                            $minutoFinal = (strlen("$minutoFinal") > 1) ? "$minutoFinal" : "0"."$minutoFinal";
                            $horaFinal = (strlen("$horaFinal") > 1) ? "$horaFinal" : "0"."$horaFinal";

                            $csvWriter->addLine("TEMPO TOTAL DE APOIO " . $horaFinal . ':' . $minutoFinal . ':' . $segundoFinal);
                            
                            break;

                        case 'A':
                            //Dados
                             foreach ($dados as $linha) {
                                $dadosLinha[0] = $linha->data_comunicacao;
                                $dadosLinha[1] = $linha->ococplaca;
                                $dadosLinha[2] = $linha->ococecclasse_termo;
                                $dadosLinha[3] = $linha->ococequipamento_projeto;
                                $dadosLinha[4] = $linha->ococcliente;
                                $dadosLinha[5] = $linha->ococfone;
                                $dadosLinha[6] = $linha->ococseguradora;
                                $dadosLinha[7] = $linha->ococtipo_contrato;
                                $dadosLinha[8] = $linha->ococmotivo;
                                $dadosLinha[9] = $linha->ocococorrencia_forma_notificacao;
                                $dadosLinha[10] = $linha->ocococorrencia_motivo_equip_sem_contato;
                                $dadosLinha[11] = $linha->ococatendente;
                                $dadosLinha[12] = $linha->ococnumero_bo;
                                $dadosLinha[13] = $linha->ococtempo_aviso;
                                $dadosLinha[14] = $linha->ococstatus;
                                $dadosLinha[15] = $linha->ococconcluido;
                                $dadosLinha[16] = "R$".number_format($linha->ococvalor_veiculo, 2, ',', '.');
                                $dadosLinha[17] = $linha->ococtipo_carga;
                                $dadosLinha[18] = $linha->ococsub_tipo_proposta;
                                $csvWriter->addLine($dadosLinha);
                                $contarIteracao++;
                             } // end foreach

                             break;
                        case 'M':

                            $i = 0;
                            foreach ($dados as $linha) {

                                $chave = 0;

                                $dadosMacro[$chave] = $linha->ococplaca;
                                $dadosMacro[++$chave] = $linha->ococveiculo_chassi;
                                $dadosMacro[++$chave] = $linha->ococtipo_veiculo;
                                $dadosMacro[++$chave] = $linha->ococveiculo_cor;
                                $dadosMacro[++$chave] = $linha->ococveiculo_ano;
                                $dadosMacro[++$chave] = $linha->ococmarca_veiculo;
                                $dadosMacro[++$chave] = $linha->ococmodelo_veiculo;
                                $dadosMacro[++$chave] = $linha->ococvalor_fipe;
                                $dadosMacro[++$chave] = $linha->ococcarregado;
                                $dadosMacro[++$chave] = $linha->ococvalor_carga;
                                $dadosMacro[++$chave] = $linha->ococcarga;
                                $dadosMacro[++$chave] = $linha->ococtipo_carga;
                                $dadosMacro[++$chave] = $linha->ococembarcador;
                                $dadosMacro[++$chave] = $linha->ococseguradora_carga;
                                $dadosMacro[++$chave] = $linha->ococcliente;
                                $dadosMacro[++$chave] = $linha->ococcnpj_cpf;
                                $dadosMacro[++$chave] = $linha->ococtipo_pessoa;
                                $dadosMacro[++$chave] = $linha->ococtipo_termo;
                                $dadosMacro[++$chave] = $linha->ococcidade;
                                $dadosMacro[++$chave] = $linha->ococuf;
                                $dadosMacro[++$chave] = $linha->ococecclasse_termo;
                                $dadosMacro[++$chave] = $linha->ococinstalado_cargo_track;
                                $dadosMacro[++$chave] = $linha->ococserial_cargo_track;
                                $dadosMacro[++$chave] = $linha->ococequipamento;
                                $dadosMacro[++$chave] = $linha->ococlocal_instalacao_equipamento;
                                $dadosMacro[++$chave] = $linha->ococtecnico_instalacao;
                                $dadosMacro[++$chave] = $linha->ococmotivo;
                                $dadosMacro[++$chave] = $linha->ocococorrencia_forma_notificacao;
                                $dadosMacro[++$chave] = $linha->ococstatus;
                                $dadosMacro[++$chave] = $linha->ococforma_recuperacao;
                                $dadosMacro[++$chave] = $linha->ocococorrencia_motivo_equip_sem_contato;
                                $dadosMacro[++$chave] = $linha->ococlatitude_longitude_recuperado;
                                $dadosMacro[++$chave] = $linha->data_comunicacao;
                                $dadosMacro[++$chave] = $linha->ococdata_roubo;
                                $dadosMacro[++$chave] = $linha->ococtempo_aviso;
                                $dadosMacro[++$chave] = $linha->ococdata_recuperacao;
                                $dadosMacro[++$chave] = $linha->ococlocal_evento;
                                $dadosMacro[++$chave] = $linha->ococbairro_evento;
                                $dadosMacro[++$chave] = $linha->ococzona_evento;
                                $dadosMacro[++$chave] = $linha->ococcidade_evento;
                                $dadosMacro[++$chave] = $linha->ococuf_evento;
                                $dadosMacro[++$chave] = $linha->ococlatitude_longitude_evento;
                                $dadosMacro[++$chave] = $linha->ococlocal_recuperado;
                                $dadosMacro[++$chave] = $linha->ococbairro_recuperado;
                                $dadosMacro[++$chave] = $linha->ococzona_recuperado;
                                $dadosMacro[++$chave] = $linha->ococcidade_recuperado;
                                $dadosMacro[++$chave] = $linha->ococuf_recuperado;
                                $dadosMacro[++$chave] = $linha->ococlatitude_longitude_recuperado;
                                $dadosMacro[++$chave] = $linha->ococequipe_apoio;
                                $dadosMacro[++$chave] = $linha->ococrecuperado_apoio;
                                $dadosMacro[++$chave] = $linha->ococtempo_chegada_apoio;
                                $dadosMacro[++$chave] = $linha->ococnumero_bo;

                                $totais[0] = $linha->total_fipe;
                                $totais[1] = $linha->media_aviso;
                                $totais[2] = $linha->media_chegada;

                                $csvWriter->addLine($dadosMacro);
                            }

                            $mediaAvido = "Média de Aviso: " . $totais[1];
                                $csvWriter->addLine($mediaAvido);
                            $mediaChegada = "Média de Tempo Chegada Apoio: " . $totais[2];
                                $csvWriter->addLine($mediaChegada);
                            $totalFipe = "Total Veículo FIPE: " .$totais[0];
                                $csvWriter->addLine($totalFipe);

                            unset($dadosMacro);
                            unset($totais);

                             break;
                        case 'S':

                            $blocos = array();

                            /*
                             * Bloco: Resumo mensal
                             */
                            $arrayMeses = array();
                            $arrayMeses[1] = "Jan";
                            $arrayMeses[2] = "Fev";
                            $arrayMeses[3] = "Mar";
                            $arrayMeses[4] = "Abr";
                            $arrayMeses[5] = "Mai";
                            $arrayMeses[6] = "Jun";
                            $arrayMeses[7] = "Jul";
                            $arrayMeses[8] = "Ago";
                            $arrayMeses[9] = "Set";
                            $arrayMeses[10] = "Out";
                            $arrayMeses[11] = "Nov";
                            $arrayMeses[12] = "Dez";

                            $ultimoDataHead = array();
                            $ultimaSeguradora = '#';
                            $totais = array();

                            foreach($dados['resumo']->resumo_mensal as $dadosSeguradora) {

                                foreach ($dadosSeguradora as $dataOcorrencia => $totaisRecup) {

                                    $mes = explode("-", $dataOcorrencia);
                                    $dataHead = $arrayMeses[intval($mes[1])] . "/" . substr($mes[0], 2,2);

                                    if(!in_array($dataHead,$ultimoDataHead)) {

                                        $headerBloco[] = "Seguradora";
                                        $headerBloco[] .= $dataHead;
                                        $headerBloco[] .= "Rec";
                                        $headerBloco[] .= "NRec";
                                        $ultimoDataHead[] = $dataHead;
                                    }
                                }
                            }
                            $csvWriter->addLine($headerBloco);

                            $linha = array();

                            foreach($dados['resumo']->resumo_mensal as $seguradora => $dadosSeguradora) {

                                if ($ultimaSeguradora != $seguradora) {
                                    $bloco = array($seguradora);
                                    $ultimaSeguradora = $seguradora;
                                }

                                foreach ($dadosSeguradora as $dataOcorrencia => $totaisRecup) {

                                    $linha[0] = ($totaisRecup->total_recup + $totaisRecup->total_nao_recup);
                                    $linha[1] = $totaisRecup->total_recup;
                                    $linha[2] = $totaisRecup->total_nao_recup;

                                    $bloco = array_merge($bloco, $linha);

                                    $totais[$dataOcorrencia]['total_mes'] += ($totaisRecup->total_recup + $totaisRecup->total_nao_recup);
                                    $totais[$dataOcorrencia]['total_nrec'] += $totaisRecup->total_nao_recup;
                                    $totais[$dataOcorrencia]['total_rec'] += $totaisRecup->total_recup;

                                }

                                $csvWriter->addLine($bloco);
                            }

                            $bloco = array("Total");

                            foreach($totais as $key => $total) {

                                $linha[0] = $total['total_mes'];
                                $linha[1] = $total['total_rec'];
                                $linha[2] = $total['total_nrec'];
                                $bloco = array_merge($bloco, $linha);

                            }

                            $csvWriter->addLine($bloco);

                            /*
                             * Bloco Índice de Ocorrências Comunicadas e/ou Recuperadas
                             */
                            $titulo = "\nÍndice de Ocorrências Comunicadas e/ou Recuperadas no Período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $bloco = $this->formatarSinteticoResumidoCSV($dados['resumo']);
                            $csvWriter->addLine($bloco);

                            /*
                             * Bloco Por Classe de Equipamento
                             */
                            $titulo = "\nPor Classe de Equipamento no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Classe",
                                "Recuperados",
                                "Não Recuperados"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_equipamento') {

                                     $csvWriter->addLine(array(
                                       $ocorrencia->coluna1,
                                       $ocorrencia->recuperados,
                                       $ocorrencia->nao_recuperados
                                    ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                }
                            }

                            $csvWriter->addLine(array(
                                       "Total Geral",
                                       $totalRecup,
                                       $totalNaoRecup
                                    ));

                            /*
                             * Bloco Por Modelo de Veículo
                             */
                            $titulo = "\nPor Modelo de Veículo no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Marca/Modelo Veículo",
                                "Recuperados",
                                "Não Recuperados",
                                "Total"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;
                            $totalGeral = 0;

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_modelo_veiculo') {

                                     $csvWriter->addLine(array(
                                       $ocorrencia->coluna1,
                                       $ocorrencia->recuperados,
                                       $ocorrencia->nao_recuperados,
                                       (intval($ocorrencia->recuperados) + intval($ocorrencia->nao_recuperados))
                                    ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    $totalGeral +=  (intval($ocorrencia->recuperados) + intval($ocorrencia->nao_recuperados));
                                }
                            }

                            $csvWriter->addLine(array(
                                       "Total Geral",
                                       $totalRecup,
                                       $totalNaoRecup,
                                        $totalGeral
                                    ));


                            /*
                             * Bloco Por Estado
                             */
                            $titulo = "\nPor Estado no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Estado",
                                "Recuperados",
                                "Não Recuperados"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_estado') {

                                     $csvWriter->addLine(array(
                                       $ocorrencia->coluna1,
                                       $ocorrencia->recuperados,
                                       $ocorrencia->nao_recuperados
                                    ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                }
                            }

                            $csvWriter->addLine(array(
                                       "Total Geral",
                                       $totalRecup,
                                       $totalNaoRecup
                                    ));

                            /*
                             * Bloco Por Estado/Cidade/Marca/Modelo/Veículo
                             */
                            $titulo = "\nPor Estado/Cidade/Marca/Modelo/Veículo Tipo no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Cidade",
                                "Marca/Modelo",
                                "Veículo Tipo",
                                "Recuperados",
                                "Não Recuperados"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;
                            $subTotalRecup = 0;
                            $subTotalNaoRecup = 0;
                            $subTotalTipoRecup = 0;
                            $subTotalTipoNaoRecup = 0;
                            $uf = "";
                            $tipo = "";

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_est_cidade_modelo_tipo') {

                                     if($tipo !=  $ocorrencia->coluna2 || $uf !=  $ocorrencia->coluna1) {

                                          if($tipo != "") {

                                                $csvWriter->addLine(array(
                                                        "Subtotal " .$uf ."-" . $tipo,
                                                        " ",
                                                        " ",
                                                        $subTotalTipoRecup,
                                                        $subTotalTipoNaoRecup
                                                     ));

                                                $subTotalTipoRecup = 0;
                                                $subTotalTipoNaoRecup = 0;
                                          }
                                          $tipo = $ocorrencia->coluna2;
                                     }

                                     if($uf !=  $ocorrencia->coluna1) {

                                         if($uf != "") {

                                             $csvWriter->addLine(array(
                                                        "Subtotal " . $uf,
                                                        " ",
                                                        " ",
                                                        $subTotalRecup,
                                                        $subTotalNaoRecup
                                                     ));

                                            $subTotalRecup = 0;
                                            $subTotalNaoRecup = 0;
                                         }

                                          $csvWriter->addLine($ocorrencia->coluna1);
                                          $uf = $ocorrencia->coluna1;
                                     }

                                    $csvWriter->addLine(array(
                                                      $ocorrencia->coluna9,
                                                      $ocorrencia->coluna5,
                                                      $ocorrencia->coluna2,
                                                      $ocorrencia->recuperados,
                                                      $ocorrencia->nao_recuperados
                                                   ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    $subTotalRecup += $ocorrencia->recuperados;
                                    $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    $subTotalTipoRecup += $ocorrencia->recuperados;
                                    $subTotalTipoNaoRecup += $ocorrencia->nao_recuperados;
                                }
                            }

                            if($tipo != "") {
                                $csvWriter->addLine(array(
                                       "Subtotal " . $uf . "-" . $tipo,
                                        " ",
                                        " ",
                                       $subTotalTipoRecup,
                                       $subTotalTipoNaoRecup
                                    ));
                                $subTotalTipoRecup = 0;
                                $subTotalTipoNaoRecup = 0;
                            }

                            if($uf != "") {
                                $csvWriter->addLine(array(
                                       "Total " . $uf,
                                        " ",
                                        " ",
                                       $subTotalRecup,
                                       $subTotalNaoRecup
                                    ));
                                $subTotalRecup = 0;
                                $subTotalNaoRecup = 0;
                            }

                             $csvWriter->addLine(array(
                                       "Total Geral",
                                        " ",
                                        " ",
                                       $totalRecup,
                                       $totalNaoRecup
                                    ));


                            /*
                             * Bloco Por Estado/Horário Ocorrência
                             */
                            $titulo = "\nPor Estado/Horário Ocorrência no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Estado",
                                "Horário",
                                "Recuperados",
                                "Não Recuperados",
                                "Total"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;
                            $subTotalRecup = 0;
                            $subTotalNaoRecup = 0;
                            $uf = "";
                            $estado = "";

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_estado_horario') {

                                     if($uf != $ocorrencia->coluna1 && $uf != "") {

                                         $totalLinha = ($subTotalRecup + $subTotalNaoRecup);

                                         $csvWriter->addLine(array(
                                                            "Total " . $uf,
                                                             " ",
                                                            $subTotalRecup,
                                                            $subTotalNaoRecup,
                                                            $totalLinha
                                                         ));


                                         $subTotalRecup=0;
                                         $subTotalNaoRecup=0;
                                     }

                                     if($uf != $ocorrencia->coluna1) {
                                            $estado = $ocorrencia->coluna1;
                                            $uf=$ocorrencia->coluna1;
                                     }

                                     $csvWriter->addLine(array(
                                                            $estado,
                                                            $ocorrencia->coluna2,
                                                            $ocorrencia->recuperados,
                                                            $ocorrencia->nao_recuperados,
                                                            ($ocorrencia->recuperados + $ocorrencia->nao_recuperados)
                                                         ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $subTotalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    $estado = "";

                                }
                            }

                             if($uf != "") {

                                 $totalLinha = ($subTotalRecup + $subTotalNaoRecup);

                                  $csvWriter->addLine(array(
                                                    "Total" . $uf,
                                                    " ",
                                                    $subTotalRecup,
                                                    $subTotalNaoRecup,
                                                    $totalLinha
                                                 ));


                                   $subTotalRecup=0;
                                   $subTotalNaoRecup=0;
                             }

                             $csvWriter->addLine(array(
                                                    "Total Geral",
                                                    " ",
                                                    $totalRecup,
                                                    $totalNaoRecup,
                                                    ($totalNaoRecup + $totalRecup)
                                                 ));


                            /*
                             * Bloco Por Estado/Dia da Semana
                             */
                            $titulo = "\nPor Estado/Dia da Semana no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Estado",
                                "Dia da Semana",
                                "Recuperados",
                                "Não Recuperados",
                                "Total"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;
                            $subTotalRecup = 0;
                            $subTotalNaoRecup = 0;
                            $uf = "";
                            $estado = "";

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_estado_dia_semana') {

                                     if($uf != $ocorrencia->coluna1 && $uf != "") {

                                         $totalLinha = ($subTotalRecup + $subTotalNaoRecup);

                                         $csvWriter->addLine(array(
                                                            "Total " . $uf,
                                                            " ",
                                                            $subTotalRecup,
                                                            $subTotalNaoRecup,
                                                            $totalLinha
                                                         ));


                                         $subTotalRecup=0;
                                         $subTotalNaoRecup=0;
                                     }

                                     if($uf != $ocorrencia->coluna1) {
                                            $estado = $ocorrencia->coluna1;
                                            $uf=$ocorrencia->coluna1;
                                     }

                                     $csvWriter->addLine(array(
                                                            $estado,
                                                            $ocorrencia->coluna2,
                                                            $ocorrencia->recuperados,
                                                            $ocorrencia->nao_recuperados,
                                                            ($ocorrencia->recuperados + $ocorrencia->nao_recuperados)
                                                         ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $subTotalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    $estado = "";

                                }
                            }

                             if($uf != "") {

                                 $totalLinha = ($subTotalRecup + $subTotalNaoRecup);

                                  $csvWriter->addLine(array(
                                                    "Total" . $uf,
                                                    " ",
                                                    $subTotalRecup,
                                                    $subTotalNaoRecup,
                                                    $totalLinha
                                                 ));


                                   $subTotalRecup=0;
                                   $subTotalNaoRecup=0;
                             }

                             $csvWriter->addLine(array(
                                                    "Total Geral",
                                                     " ",
                                                    $totalRecup,
                                                    $totalNaoRecup,
                                                    ($totalNaoRecup + $totalRecup)
                                                 ));

                            /*
                             * Bloco Por Estado/Veículo Tipo
                             */
                            $titulo = "\nPor Estado/Veículo Tipo no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Estado",
                                "Veículo Tipo",
                                "Recuperados",
                                "Não Recuperados",
                                "Total"
                            ));

                            $totalRecup = 0;
                            $totalNaoRecup = 0;
                            $subTotalRecup = 0;
                            $subTotalNaoRecup = 0;
                            $uf = "";
                            $estado = "";

                            foreach($dados as $ocorrencia) {

                                if($ocorrencia->tipo == 'por_estado_veiculo_tipo') {

                                     if($uf != $ocorrencia->coluna1 && $uf != "") {

                                         $totalLinha = ($subTotalRecup + $subTotalNaoRecup);

                                         $csvWriter->addLine(array(
                                                            "Total " . $uf,
                                                            " ",
                                                            $subTotalRecup,
                                                            $subTotalNaoRecup,
                                                            $totalLinha
                                                         ));


                                         $subTotalRecup=0;
                                         $subTotalNaoRecup=0;
                                     }

                                     if($uf != $ocorrencia->coluna1) {
                                            $estado = $ocorrencia->coluna1;
                                            $uf=$ocorrencia->coluna1;
                                     }

                                     $csvWriter->addLine(array(
                                                            $estado,
                                                            $ocorrencia->coluna2,
                                                            $ocorrencia->recuperados,
                                                            $ocorrencia->nao_recuperados,
                                                            ($ocorrencia->recuperados + $ocorrencia->nao_recuperados)
                                                         ));

                                    $totalRecup += $ocorrencia->recuperados;
                                    $subTotalRecup += $ocorrencia->recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    $estado = "";

                                }
                            }

                             if($uf != "") {

                                 $totalLinha = ($subTotalRecup + $subTotalNaoRecup);

                                  $csvWriter->addLine(array(
                                                    "Total" . $uf,
                                                    " ",
                                                    $subTotalRecup,
                                                    $subTotalNaoRecup,
                                                    $totalLinha
                                                 ));


                                   $subTotalRecup=0;
                                   $subTotalNaoRecup=0;
                             }

                             $csvWriter->addLine(array(
                                                    "Total Geral",
                                                     " ",
                                                    $totalRecup,
                                                    $totalNaoRecup,
                                                    ($totalNaoRecup + $totalRecup)
                                                 ));

                            /*
                             * Bloco Recuperações
                             */
                            $titulo = "\nRecuperações no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Cliente",
                                "Placa",
                                "Ano",
                                "Chassi",
                                "Veículo",
                                "CNPJ/CPF",
                                "Data do Evento",
                                "Data da Comunicação",
                                "Cidade de Ocorrência",
                                "Data recuperação",
                                "Cidade de Recuperação"
                            ));

                            foreach($dados as $ocorrencia) {

                                 if($ocorrencia->tipo == 'detalhado' && $ocorrencia->recuperados > 0) {

                                     $csvWriter->addLine(array(
                                                    $ocorrencia->coluna1,
                                                    $ocorrencia->coluna2,
                                                    $ocorrencia->coluna3,
                                                    $ocorrencia->coluna4,
                                                    $ocorrencia->coluna5,
                                                    $ocorrencia->coluna6,
                                                    $ocorrencia->coluna7,
                                                    $ocorrencia->coluna8 . ":00",
                                                    $ocorrencia->coluna9,
                                                    $ocorrencia->coluna10,
                                                    $ocorrencia->coluna11
                                                ));

                                 }
                            }

                            /*
                             * Bloco Veículos não Recuperados
                             */
                            $titulo = "\nVeículos não Recuperados no período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $csvWriter->addLine(array(
                                "Cliente",
                                "Placa",
                                "Ano",
                                "Chassi",
                                "Veículo",
                                "CNPJ/CPF",
                                "Data do Evento",
                                "Data da Comunicação",
                                "Cidade de Ocorrência"
                            ));

                            foreach($dados as $ocorrencia) {

                                 if($ocorrencia->tipo == 'detalhado' && $ocorrencia->nao_recuperados > 0) {

                                     $csvWriter->addLine(array(
                                                    $ocorrencia->coluna1,
                                                    $ocorrencia->coluna2,
                                                    $ocorrencia->coluna3,
                                                    $ocorrencia->coluna4,
                                                    $ocorrencia->coluna5,
                                                    $ocorrencia->coluna6,
                                                    $ocorrencia->coluna7,
                                                    $ocorrencia->coluna8 . ":00",
                                                    $ocorrencia->coluna9
                                                ));

                                 }
                            }

                             break;
                        case 'R':
                            $titulo = "Índice de Ocorrências Comunicadas e/ou Recuperadas no Período de ";
                            $titulo .=  $parametros->ococdperiodo_inicial ." à " . $parametros->ococdperiodo_final;
                            $csvWriter->addLine($titulo);
                            $linhas = $this->formatarSinteticoResumidoCSV($dados['resumo']);
                            $csvWriter->addLine($linhas);

                            break;

                    } //Switch

                    unset($dados);

                    //Verifica se o arquivo foi gerado
                    $arquivoGerado = file_exists( $diretorio.$arquivo);
                    if ($arquivoGerado === false) {
                        return '';
                    } else {
                        return $diretorio.$arquivo;
                    }

                } else {

                     throw new ErrorException("Erro ao gravar o arquivo" . " " . $diretorio);
                }

            } catch (Exception $e){
                throw new Exception($e->getMessage());
            }
        } //if ( count($dados) > 0 )

    }

    /**
     * Formata e organiza os dados do relatório Sintético Resumido
     * para aplicação no arquivo CSV
     *
     * @param array $dados
     * @return array
     */
    private function formatarSinteticoResumidoCSV($dados) {

        //Variáveis de controle
        $totalComContato = 0;
        $totalSemContato = 0;
        $pesados = 0;
        $leves = 0;
        $motos = 0;
        $linha = array();
        $linha2 = array();
        $linha3 = array();
        $linha4 = array();
        $linha5 = array();
        $linha6 = array();
        $linhas = array();


        foreach ($dados->recuperacoes as $ocorrencia) {
             if($ocorrencia->tipo == 'recuperados' && $ocorrencia->motivo_ocorrencia == '') {
                $totalComContato += intval($ocorrencia->veiculos);
            } else if ($ocorrencia->tipo == 'recuperados') {
                $totalSemContato += intval($ocorrencia->veiculos);
            }
        }

        $linha[0] = "Equipamentos Instalados";
        $linha[1] = $dados->total_equipamentos;
        $linha[2] = "\nOcorrências em Andamento";
        $linha[3] = $dados->total_andamento;
        $linha[4] = "\nOcorrências Atendidas (no período)";
        $linha[5] = $dados->atendidas_periodo;
        $linha[6] = "\nOcorrências Anteriores Recuperadas no período";
        $linha[7] = $dados->recuperadas_anterior;
        $linha[8] = "\nTotal de Ocorrências Anteriores Recuperadas e as Atendidas no período ";
        $linha[9] = (intval($this->view->dadosResumo->atendidas_periodo) + intval($this->view->dadosResumo->recuperadas_anterior));
        $linha[10] = "\nVeículos Recuperados";
        $totalRecuperados = $this->view->dadosResumo->total_veiculos_recuperados;
        $linha[11] = $totalRecuperados;

        if($totalRecuperados == 0){
            $divisorNaoRecuperados = 1;
        } else {
            $divisorRecuperados = $totalRecuperados;
        }

        $linha[12] = "\nCom Contato";
        $linha[13] = $totalComContato . " (";
        $linha[13] .= number_format((intval(((intval($totalComContato *100)) / $divisorRecuperados)*100)/100),2,',','.');
        $linha[13] .= "%)";
        $linha[14] = "\nSem Contato";
        $linha[15] = $totalSemContato . " (";
        $linha[15] .= number_format((intval(((intval($totalSemContato *100)) / $divisorRecuperados)*100)/100),2,',','.');
        $linha[15] .= "%)";

        foreach ($dados->recuperacoes as $ocorrencia) {
           if($ocorrencia->tipo == 'recuperados' && $ocorrencia->motivo_ocorrencia != '') {

               $linha2[] = "\n" . $ocorrencia->motivo_ocorrencia . ";";
               $linhaLoop = $ocorrencia->veiculos . " (";
               $linhaLoop .= number_format((intval(((intval($ocorrencia->veiculos *100)) / $divisorRecuperados)*100)/100),2,',','.');
               $linha2[] .= $linhaLoop ."%)";
           }
        }

        //Variáveis de controle
        $totalComContato = 0;
        $totalSemContato = 0;

        foreach ($dados->recuperacoes as $ocorrencia) {
            if($ocorrencia->tipo == 'nao_recuperados' && $ocorrencia->motivo_ocorrencia == '') {
                $totalComContato += intval($ocorrencia->veiculos);
            } else if ($ocorrencia->tipo == 'nao_recuperados') {
                $totalSemContato += intval($ocorrencia->veiculos);
            }
        }

        $totalOcorrencia += ($totalSemContato + $totalComContato);

        $linha3[0] =  "\nVeículos Não Recuperados";
        $totalNaoRecuperados = $this->view->dadosResumo->total_veiculos_nao_recuperados;
        $linha3[1] = $totalNaoRecuperados;
        if($totalNaoRecuperados == 0){
            $divisorNaoRecuperados = 1;
        } else {
            $divisorNaoRecuperados = $totalNaoRecuperados;
        }

        $linha3[2] = "\nEquipamento Com Contato";
        $linha3[3] = $totalComContato . " (";
        $linha3[3] .= number_format((intval(((intval($totalComContato *100)) / $divisorNaoRecuperados)*100)/100),2,',','.');
        $linha3[3] .= "%)";
        $linha3[4] = "\nEquipamento Sem Contato";
        $linha3[5] = $totalSemContato . " (";
        $linha3[5] .= number_format((intval(((intval($totalSemContato *100)) / $divisorNaoRecuperados)*100)/100),2,',','.');
        $linha3[5] .= "%)";

        foreach($dados->recuperacoes as $ocorrencia) {
            if(($ocorrencia->tipo == 'nao_recuperados') && ($ocorrencia->motivo_ocorrencia != '')) {

                $linha4[] = "\n" . $ocorrencia->motivo_ocorrencia . ";";
                $linhaLoop = $ocorrencia->veiculos . " (";
                $linhaLoop .= number_format((intval(((intval($ocorrencia->veiculos *100)) / $divisorNaoRecuperados)*100)/100),2,',','.');
                $linha4[] .= $linhaLoop ."%)";
            }
        }

        $linha5[0] = "\nÍndice Percentual de Recuperação";
        $linha5[1] = number_format((($totalRecuperados *100) / $this->view->dadosResumo->atendidas_periodo),1,',','.') . "%";
        $linha5[2] = "\nÍndice Percentual de Não Recuperação";
        $linha5[3] = number_format((($totalNaoRecuperados *100) / $this->view->dadosResumo->total_ocorrencias),1,',','.') . "%";

        foreach($dados->tipo_veiculo as $ocorrencia) {

            switch ($ocorrencia->tipo_veiculo) {
                case 'Pesado':
                    $pesados = $ocorrencia->total;
                    break;
                case 'Leve':
                    $leves = $ocorrencia->total;
                    break;
                case 'Moto':
                    $motos = $ocorrencia->total;
                    break;
            }
        }

        $linha6[0] = "\nVeículos Pesados Recuperados";
        $linha6[1] = $pesados;
        $linha6[2] = "\nVeículos Leves Recuperados";
        $linha6[3] = $leves;
        $linha6[4] = "\nVeículos Motos Recuperados";
        $linha6[5] = $motos;
        $linha6[6] = "\nInformado que havia rastreador";
        $linha6[7] = $this->view->dadosResumo->total_rastreador . " (";
        $linha6[7] .= number_format((($this->view->dadosResumo->total_rastreador *100) / $totalRecuperados),1,',','.');
        $linha6[7] .= "%)";

        $linhas = array_merge($linhas, $linha);
        $linhas = array_merge($linhas, $linha2);
        $linhas = array_merge($linhas, $linha3);
        $linhas = array_merge($linhas, $linha4);
        $linhas = array_merge($linhas, $linha5);
        $linhas = array_merge($linhas, $linha6);

        return $linhas;

    }

    /**
     * Formata dado retornado do banco
     * @param string $dados
     * @return string
     */
    private function formatarDados($dados) {

        $totalFipe = 0;
        $totalTempoParcial = 0;
        $totalChegadaParcial = 0;
        $totalDados = count($dados);

        foreach($dados as $dado) {

            foreach($dado as $key => $valor) {

                switch ($key) {
                    case 'ococtempo_aviso':

                        if(!empty($valor)) {

                            $dias = intval(substr($valor, 0, strpos($valor, '-')));

                            if(!empty($dias)){
                                $horaMinuto = substr($valor, (strpos($valor, '-') + 1));
                            }else {
                                $horaMinuto = $valor;
                            }

                            $partes = explode(":", $horaMinuto);
                            if($partes[0] == '00' && $partes[1] == '00'){
                                $valor = $dias . " dias ";
                            } else {

                                if(empty($dias)) {
                                    $valor = $horaMinuto;
                                } else {
                                    $valor = $dias . " dias " . $horaMinuto;
                                }
                            }
                            $dado->$key = $valor;

                            $hora = ($dias*24);
                            $hora = ($hora * 60);
                            $totalTempoParcial += ($hora + (intval($partes[0]) * 60) + intval($partes[1]));
                        }

                        break;

                    case 'ococzona_recuperado':
                    case 'ococzona_evento':
                        $dado->$key = $this->view->zonas[intval($valor)];
                        break;

                    case 'ococvalor_fipe':
                        $dado->$key = "R$ " . number_format($valor, 2,  ',', '.');
                        $totalFipe += $valor;
                        break;
                    case 'ococvalor_carga':
                    case 'ococvalor_veiculo':
                         $dado->$key = "R$ " . number_format($valor, 2,  ',', '.');
                        break;
                     case 'ococtempo_chegada_apoio':

                        if(!empty($valor)) {

                            $dias = intval(substr($valor, 0, strpos($valor, '-')));

                            if(!empty($dias)){
                                $horaMinuto = substr($valor, (strpos($valor, '-') + 1));
                            }else {
                                $horaMinuto = $valor;
                            }

                            $partes = explode(":", $horaMinuto);

                            if($partes[0] == '00' && $partes[1] == '00'){
                                $valor = $dias . " dias ";
                            } else {

                                if(empty($dias)) {
                                    $valor = $horaMinuto;
                                } else {
                                    $valor = $dias . " dias " . $horaMinuto;
                                }
                            }
                            $dado->$key = $valor;

                            $hora = ($dias * 24);
                            $hora = ($hora * 60);
                            $totalChegadaParcial += ($hora + (intval($partes[0]) * 60) + intval($partes[1]));
                        }

                         break;
                }//Switch

            }//foreach $dado

            $dado->total_fipe = number_format($totalFipe, 2,  ',', '.');

            $totalTempo = ($totalTempoParcial / $totalDados);
            $totalDias = floor(($totalTempo/60)/24);
            $dado->media_aviso = ($totalDias>0 ? $totalDias." dia(s) " : "").sprintf('%02d:%02dh', floor(($totalTempo/60)%24), $totalTempo%60);

            $totalTempo2 = ($totalChegadaParcial / $totalDados);
            $totalDias2 = floor(($totalTempo2/60)/24);
            $dado->media_chegada = ($totalDias2>0 ? $totalDias2." dia(s) " : "").sprintf('%02d:%02dh', floor(($totalTempo2/60)%24), $totalTempo2%60);

        }//foreach $dados

        return $dados;
    }

    private function isUsuarioComercial(){
        $a = array(31,73,27,200,66,71,67,162,176,177,74,75,79,46);
        return in_array($_SESSION[usuario][depoid],$a);
    }

    /**
    * Realzia o calculo do tempo médio por grupo de clientes / Emergencia
    * @param array $dados 
    * @return array
    *
    */
    private function calcularTempoApoio($dados) {

        $cliente = '';
        $i = 0;
        $maiorDataInicio = '';
        $maiorDataChegada = '';

        foreach($dados as $dado){

             if($cliente != $dado->ococcliente) {
                
                $i++;
                $cliente = $dado->ococcliente;                
                $maiorDataChegada = '';
                $maiorDataInicio = '';
            } 


            //Verifica qual é a maior data de chegada
            if(empty($maiorDataChegada)){

                if(!is_null($dado->data_chegada)){

                    $maiorDataChegada = $dado->data_chegada;
                }                

            } else {

                if(!is_null($dado->data_chegada) &&  $maiorDataChegada < $dado->data_chegada){

                   $maiorDataChegada = $dado->data_chegada;
                } 

            }               
            

            //Verifica qual é a maior data de início
            if(empty($maiorDataInicio)){

                if(!is_null($dado->data_inicio)){

                    $maiorDataInicio = $dado->data_inicio;
                }                

            } else {

                if(!is_null($dado->data_inicio) &&  $maiorDataInicio < $dado->data_inicio){

                   $maiorDataInicio = $dado->data_inicio;
                } 

            } 

            if(!empty($maiorDataChegada) && !empty($maiorDataInicio)) {
                $dtChegada = mktime(substr($maiorDataChegada,11,2),substr($maiorDataChegada,14,2),substr($maiorDataChegada,17,2),substr($maiorDataChegada,5,2),substr($maiorDataChegada,8,2),substr($maiorDataChegada,0,4));
                $dtInicio = mktime(substr($maiorDataInicio,11,2),substr($maiorDataInicio,14,2),substr($maiorDataInicio,17,2),substr($maiorDataInicio,5,2),substr($maiorDataInicio,8,2),substr($maiorDataInicio,0,4));

                $horas =  floor( (($dtChegada-$dtInicio) / 86400 * 24) ) ;
                $minutos =  floor( (($dtChegada-$dtInicio) / 86400 * 24 * 60) - ($horas * 60) ) ;
                $segundos = ($dtChegada-$dtInicio)  - floor($minutos*60) - floor($horas*60*60);

                if(strlen($horas) < 2){
                    $horas = "0".$horas;
                }
                if(strlen($minutos) < 2){
                    $minutos = "0".$minutos;
                }
                if(strlen($segundos) < 2){
                    $segundos = "0".$segundos;
                }

                $horas      = ($horas == 0)    ? "00" : $horas;
                $minutos    = ($minutos == 0)  ? "00" : $minutos;
                $segundos   = ($segundos == 0) ? "00" : $segundos;

                $dado->tempo_apoio = $horas . ":" . $minutos . ":" . $segundos;
            } else {
                 $dado->tempo_apoio = '00:00:00';
            }

        }

        //echo "<pre>";print_r($dados);exit;

        return $dados;        

    }
}