<?php

/**
 * Classe padrão para Action
 *
 * @package Intranet
 */
class FinCreditoFuturo {

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
    private $step = array(
        '' => '',
        '' => ''
    );

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
            
            if (isset($_SESSION['flash_message']) && count($_SESSION['flash_message'])) {
                if ($_SESSION['flash_message']['tipo'] == 'sucesso') {
                    $this->view->mensagemSucesso = $_SESSION['flash_message']['mensagem'];
                }
                unset($_SESSION['flash_message']);
                $this->view->parametros = '';
            }
            
            if (isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar') {
                
                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);
                
            } else if ($_SESSION['pesquisa']['usarSessao'] && $_GET['acao'] == 'pesquisar') {
                
                $this->view->parametros = (object) $_SESSION['pesquisa'];
                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);
                
            } else {
                
                $this->limparSessaoPesquisa();
                
            }
            
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }
        $this->inicializarParametros();
        $this->view->parametros->verificarCadastroEmailAprovacao = $this->dao->verificarCadastroEmailAprovacao();

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/index.php";
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
                if (!is_array($_POST[$key])) {
                    $retorno->$key = isset($_POST[$key]) ? htmlentities(strip_tags($value)) : '';
                } else {
                    $retorno->$key = isset($_POST[$key]) ? $value : '';
                }
            }
            //Limpa o POST
            //unset($_POST);
        } else {
            $_SESSION['credito_futuro'] = array();
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    if (!is_array($_GET[$key])) {
                        $retorno->$key = isset($_GET[$key]) ? htmlentities(strip_tags($value)) : '';
                    } else {
                        $retorno->$key = isset($_GET[$key]) ? $value : '';
                    }
                }
            }
            //Limpa o GET
            //unset($_GET);
        }


        return $retorno;
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
                    if (is_array($_POST[$key])){
                        $temp[$key] = $_POST[$key];
                    } else {
                        $temp[$key] = trim($_POST[$key]);
                    }
                } elseif (isset($_SESSION['pesquisa'][$key])) {
                    
                    if (is_array($_SESSION['pesquisa'][$key])){
                        $temp[$key] = $_SESSION['pesquisa'][$key];
                    } else {
                        $temp[$key] = trim($_SESSION['pesquisa'][$key]);
                    }
                    
                }
                $_SESSION['pesquisa'][$key] = $temp[$key];
            }
        }

        $_SESSION['pesquisa']['usarSessao'] = TRUE;

        return (object) $_SESSION['pesquisa'];
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     * 
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
        // Ex. $this->view->parametros->excnome = isset($this->view->parametros->excnome) ? trim($this->view->parametros->excnome) : '';

        $this->view->parametros->cfoclioid = isset($this->view->parametros->cfoclioid) ? trim($this->view->parametros->cfoclioid) : '';
        $this->view->parametros->periodo_inclusao_ini = isset($this->view->parametros->periodo_inclusao_ini) ? trim($this->view->parametros->periodo_inclusao_ini) : '';
        $this->view->parametros->periodo_inclusao_fim = isset($this->view->parametros->periodo_inclusao_fim) ? trim($this->view->parametros->periodo_inclusao_fim) : '';
        $this->view->parametros->tipo_pessoa = isset($this->view->parametros->tipo_pessoa) ? trim($this->view->parametros->tipo_pessoa) : '';
        $this->view->parametros->razao_social = isset($this->view->parametros->razao_social) ? trim($this->view->parametros->razao_social) : '';
        $this->view->parametros->nome = isset($this->view->parametros->nome) ? trim($this->view->parametros->nome) : '';
        $this->view->parametros->cnpj = isset($this->view->parametros->cnpj) ? trim($this->view->parametros->cnpj) : '';
        $this->view->parametros->cpf = isset($this->view->parametros->cpf) ? trim($this->view->parametros->cpf) : '';
        $this->view->parametros->cfooid = isset($this->view->parametros->cfooid) ? trim($this->view->parametros->cfooid) : '';
        $this->view->parametros->cfoconnum_indicado = isset($this->view->parametros->cfoconnum_indicado) ? trim($this->view->parametros->cfoconnum_indicado) : '';
        $this->view->parametros->cfoancoid = isset($this->view->parametros->cfoancoid) ? trim($this->view->parametros->cfoancoid) : '';
        $this->view->parametros->cfocfcpoid = isset($this->view->parametros->cfocfcpoid) ? trim($this->view->parametros->cfocfcpoid) : '';
        $this->view->parametros->cfoobroid_desconto = isset($this->view->parametros->cfoobroid_desconto) ? trim($this->view->parametros->cfoobroid_desconto) : '';

        $this->view->parametros->forma_inclusao = isset($this->view->parametros->forma_inclusao) ? trim($this->view->parametros->forma_inclusao) : '';
        $this->view->parametros->cfostatus = isset($this->view->parametros->cfostatus) ? trim($this->view->parametros->cfostatus) : '';
        $this->view->parametros->registros_apenas = isset($this->view->parametros->registros_apenas) ? $this->view->parametros->registros_apenas : '';
        $this->view->parametros->cfotipo_desconto = isset($this->view->parametros->cfotipo_desconto) ? trim($this->view->parametros->cfotipo_desconto) : '';
        $this->view->parametros->cfopercentual_de = isset($this->view->parametros->cfopercentual_de) ? trim($this->view->parametros->cfopercentual_de) : '';
        $this->view->parametros->cfopercentual_ate = isset($this->view->parametros->cfopercentual_ate) ? trim($this->view->parametros->cfopercentual_ate) : '';

        $this->view->parametros->cfovalor_de = isset($this->view->parametros->cfovalor_de) ? trim($this->view->parametros->cfovalor_de) : '';
        $this->view->parametros->cfovalor_ate = isset($this->view->parametros->cfovalor_ate) ? trim($this->view->parametros->cfovalor_ate) : '';
        $this->view->parametros->cfosaldo = isset($this->view->parametros->cfosaldo) ? trim($this->view->parametros->cfosaldo) : '';

        $this->view->parametros->cfoforma_aplicacao = isset($this->view->parametros->cfoforma_aplicacao) ? trim($this->view->parametros->cfoforma_aplicacao) : '';

        $this->view->parametros->cfocfmcoid = isset($this->view->parametros->cfocfmcoid) ? $this->view->parametros->cfocfmcoid : '';

        $this->view->parametros->step = isset($this->view->parametros->step) ? $this->view->parametros->step : 'step_1';
        $this->view->parametros->voltar = isset($this->view->parametros->voltar) ? $this->view->parametros->voltar : '0';

        $this->view->parametros->parametracaoCreditoFuturo = $this->dao->obterParametrosCreditoFuturo();

        //viariavel para popular combo de obrigação Financeira de Desconto
        $this->view->parametros->obrigacaoFinanceiraDesconto = $this->dao->buscarObrigacaoFinanceiraDesconto();

        //variavel que seta usuario que icluiu crédito futuro
        $this->view->parametros->usuarioInclusaoCreditoFuturo = $this->dao->buscarUsuarioInclusaoCreditoFuturo();

        $this->view->parametros->motivoDoCredito = $this->dao->buscarMotivoDoCredito();

        
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {


        $this->validarCamposPesquisa($filtros);

        $resultadoPesquisa = $this->dao->pesquisar($filtros);
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
        try {
            
            //caso o usuario tente acessar a página diretamente pela url verifico
            // as configurações básicas, se  nao possui bloqueio o acesso a tela.
            if (!$this->dao->verificarCadastroEmailAprovacao()) {
                $this->index();
                exit;
            }

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros();

            //Verificar se foi submetido o formulário e grava o registro em banco de dados 
            if (isset($_POST) && count($_POST) && $this->view->parametros->voltar == '0') {

                if ($this->view->parametros->step == 'step_3') {
                    $registroGravadoStep3 = $this->gravarParametrosStep3($this->view->parametros);
                } else {
                    $registroGravado = $this->gravarParametros($this->view->parametros);
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

        //se o registro foi gravado na sessão, eu realizado o roteamento de views da tela de cadastro
        if ($registroGravado || $this->view->parametros->voltar == '1') {

            if ($this->view->parametros->voltar == '1') {
                if (isset($_SESSION['credito_futuro']) && count($_SESSION['credito_futuro'])) {

                    foreach ($_SESSION['credito_futuro'] as $step) {

                        foreach ($step as $campo => $valor) {
                            $this->view->parametros->$campo = $valor;
                        }
                    }
                }
            }

            if ($this->view->parametros->step == '') {
                $this->view->parametros->step = "step_1";
            } else if ($this->view->parametros->step == 'step_1') {
                $this->view->parametros->step = "step_2";
                $this->view->parametros->listaMotivoCredito = $this->dao->buscarMotivoCreditoFormaPgtCliente();
            } else if ($this->view->parametros->step == 'step_2') {
                $this->view->parametros->step = "step_3";
            }
        }
        
        
        //se o for o ultimo passo e o registro for gravado com sucesso, redireciono para tela de pesquisa e mostro a mensagem de sucesso.        
        if (isset($registroGravadoStep3) && $registroGravadoStep3) {
            
            $_SESSION['flash_message']['tipo'] = 'sucesso';
            $_SESSION['flash_message']['mensagem'] = 'Crédito futuro incluído com sucesso.';                
            header('Location:fin_credito_futuro.php?acao=pesquisar');
        }

        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar.php";
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * 
     * @return void
     */
    public function editar() {       
        try {

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
            
            //Parametros 
            $parametros = $this->tratarParametros();
            
            //Incializa os parametros
            $this->inicializarParametros();
            
             $this->view->parametros->cadastro = $this->dao->pesquisarPorID($parametros->id);
             
                         
            if (isset($_POST) && count($_POST)) {
                
                //atualizo nos paramentros da view no array de cadastro com os alguns dados que vem do formulario.
                foreach ($this->view->parametros->cadastro as $key => $value) {                    
                    if (isset($parametros->cadastro[$key])) {
                        $this->view->parametros->cadastro[$key] = $parametros->cadastro[$key];
                    }
                }
                
                $registroAtualizado = $this->atualizarParametros($parametros->cadastro, $this->view->parametros->cadastro);
                
                if ($registroAtualizado) {
                     $this->view->parametros->cadastro = $this->dao->pesquisarPorID($parametros->id);
                     $this->view->mensagemSucesso = 'Crédito atualizado com sucesso.';
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
        
        if (isset($_SESSION['flash_message']) && count($_SESSION['flash_message'])) {
            if ($_SESSION['flash_message']['tipo'] == 'erro') {
                $this->view->mensagemErro = $_SESSION['flash_message']['mensagem'];
            }
            unset($_SESSION['flash_message']);
        }
        
        $this->view->parametros->historico = $this->dao->buscarHistoricoCreditoFuturo($parametros->id);
        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/editar.php";
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

        if ($dados->cfoid > 0) {
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

        /**
         * Verifica os campos obrigatórios
         */
        /** Ex.:
          if (!isset($dados->excnome) || trim($dados->excnome) == '') {
          $camposDestaques[] = array(
          'campo' => 'excnome'
          );
          $error = true;
          }
         */
        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
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
        $obrigatorios = true;
        $valorAteMenor = true;
        $valorMenorIgualZero = true;
        $valorMaiorIgualCem = true;
        $percentual = false;
        $monetario = false;

        /**
         * Verifica os campos obrigatórios
         */
        //verifico se o periodo de inclusao de inicio foi informado
        if (!isset($dados->periodo_inclusao_ini) || trim($dados->periodo_inclusao_ini) == '') {
            $camposDestaques[] = array(
                'campo' => 'periodo_inclusao_ini'
            );
            $obrigatorios = false;
        }

        //verifico se o periodo de fim de inclusao foi informado
        if (!isset($dados->periodo_inclusao_fim) || trim($dados->periodo_inclusao_fim) == '') {
            $camposDestaques[] = array(
                'campo' => 'periodo_inclusao_fim'
            );
            $obrigatorios = false;
        }

        //verifico se tipo de desconto foi informado
        if (!isset($dados->cfotipo_desconto) || trim($dados->cfotipo_desconto) == '') {
            $camposDestaques[] = array(
                'campo' => 'cfotipo_desconto'
            );
            $obrigatorios = false;
        } else {

            //no caso de percentual
            if ($dados->cfotipo_desconto == "1") {

                if ((isset($dados->cfopercentual_ate) && trim($dados->cfopercentual_ate) != '') && (isset($dados->cfopercentual_de) && trim($dados->cfopercentual_de) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfopercentual_de'
                    );
                    $obrigatorios = false;
                }

                if ((isset($dados->cfopercentual_de) && trim($dados->cfopercentual_de) != '') && (isset($dados->cfopercentual_ate) && trim($dados->cfopercentual_ate) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfopercentual_ate'
                    );
                    $obrigatorios = false;
                }

                $percentualDePreenchido = isset($dados->cfopercentual_de) && trim($dados->cfopercentual_de) != '';
                $percentualAtePreenchido = isset($dados->cfopercentual_ate) && trim($dados->cfopercentual_ate) != '';

                if (($percentualDePreenchido || $percentualAtePreenchido) && $obrigatorios) {

                    $percentualDe = str_replace(',', '.', $dados->cfopercentual_de);
                    $percentualAte = str_replace(',', '.', $dados->cfopercentual_ate);

                    //verifico se o campo ate é menor que o campo de
                    if (($percentualDePreenchido && $percentualAtePreenchido) && (floatval($percentualAte) < floatval($percentualDe))) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_ate'
                        );
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_de'
                        );
                        $valorAteMenor = false;
                    }


                    //verifico se o campo é menor ou igual a zero
                    if ($percentualDePreenchido && floatval($percentualDe) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_de'
                        );
                        $percentual = true;
                        $valorMenorIgualZero = false;
                    }

                    //verifico se o campo é menor ou igual a zero
                    if ($percentualAtePreenchido && floatval($percentualAte) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_ate'
                        );
                        $percentual = true;
                        $valorMenorIgualZero = false;
                    }

                    //verifico se o campo é maior que 100
                    if ($percentualDePreenchido && floatval($percentualDe) > 100 && $valorMenorIgualZero) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_de'
                        );
                        $percentual = true;
                        $valorMaiorIgualCem = false;
                    }

                    //verifico se o campo é maior que 100
                    if ($percentualAtePreenchido && floatval($percentualAte) > 100 && $valorMenorIgualZero) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_ate'
                        );
                        $percentual = true;
                        $valorMaiorIgualCem = false;
                    }
                }
            }

            //no caso de valor monetario
            if ($dados->cfotipo_desconto == "2") {

                if ((isset($dados->cfovalor_ate) && trim($dados->cfovalor_ate) != '') && (isset($dados->cfovalor_de) && trim($dados->cfovalor_de) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfovalor_de'
                    );
                    $obrigatorios = false;
                }

                if ((isset($dados->cfovalor_de) && trim($dados->cfovalor_de) != '') && (isset($dados->cfovalor_ate) && trim($dados->cfovalor_ate) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfovalor_ate'
                    );
                    $obrigatorios = false;
                }

                $valorDePreenchido = isset($dados->cfovalor_de) && trim($dados->cfovalor_de) != '';
                $valorAtePreenchido = isset($dados->cfovalor_ate) && trim($dados->cfovalor_ate) != '';

                if (($valorDePreenchido || $valorAtePreenchido) && $obrigatorios) {

                    $valorDe = str_replace('R$', '', $dados->cfovalor_de);
                    $valorDe = str_replace('.', '', $valorDe);
                    $valorDe = str_replace(',', '.', $valorDe);
                    $valorDe = trim($valorDe);

                    $valorAte = str_replace('R$', '', $dados->cfovalor_ate);
                    $valorAte = str_replace('.', '', $valorAte);
                    $valorAte = str_replace(',', '.', $valorAte);
                    $valorAte = trim($valorAte);

                    //verifico se o campo ate é menor que o campo de
                    if (($valorDePreenchido && $valorAtePreenchido) && (floatval($valorAte) < floatval($valorDe))) {
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_ate'
                        );
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_de'
                        );
                        $valorAteMenor = false;
                    }

                    //verifico se o campo é menor ou igual a zero
                    if ($valorDePreenchido && floatval($valorDe) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_de'
                        );
                        $monetario = true;
                        $valorMenorIgualZero = false;
                    }

                    //verifico se o campo é menor ou igual a zero
                    if ($valorAtePreenchido && floatval($valorAte) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_ate'
                        );
                        $monetario = true;
                        $valorMenorIgualZero = false;
                    }
                }
            }
        }

        //verifico se saldo pendente foi infomardo
        if (!isset($dados->cfosaldo) || trim($dados->cfosaldo) == '') {
            $camposDestaques[] = array(
                'campo' => 'cfosaldo'
            );
            $obrigatorios = false;
        }


        if (!$obrigatorios) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if (!$valorMenorIgualZero) {

            $this->view->dados = $camposDestaques;

            if ($percentual) {
                throw new Exception("O percentual do desconto não pode ser igual a 0%.");
            }

            if ($monetario) {
                throw new Exception("O valor do desconto não pode ser igual a 0.");
            }
        }

        if (!$valorMaiorIgualCem) {

            $this->view->dados = $camposDestaques;

            if ($percentual) {
                throw new Exception("O percentual do desconto não pode ser maior que 100%.");
            }
        }

        if (!$valorAteMenor) {
            $this->view->dados = $camposDestaques;
            throw new Exception("O campo Até não pode ser menor que o campo De.");
        }
    }

    /**
     * Executa a exclusão de registro.
     * 
     * @return void 
     */
    public function excluir() {

        $parametros = $this->tratarParametros();
        
        try {

            //Retorna os parametros

            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);

            $creditoFuturoVo->id = $parametros->cfooid;
            $creditoFuturoVo->usuarioExclusao = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : ''; //cara da sessao
            $creditoFuturoVo->observacao = $parametros->justificativa;
            $creditoFuturoVo->origem = 1;


            $creditoExcluido = $creditoFuturoBo->excluir($creditoFuturoVo);

            if ($creditoExcluido == true) {
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'Crédito futuro excluído com sucesso.';
                header('Location:fin_credito_futuro.php?acao=pesquisar');
            } else {
                $_SESSION['flash_message']['tipo'] = 'erro';
                $_SESSION['flash_message']['mensagem'] = 'Houve um erro no processamento dos dados.';

                if (isset($parametros->excluir_listagem)) {
                    header('Location:fin_credito_futuro.php?acao=pesquisar');
                } else {
                    header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
                }
            }
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            if (isset($parametros->excluir_listagem)) {
                header('Location:fin_credito_futuro.php?acao=pesquisar');
            } else {
                header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
            }
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            if (isset($parametros->excluir_listagem)) {
                header('Location:fin_credito_futuro.php?acao=pesquisar');
            } else {
                header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
            }
        }
    }
    
    /**
     * Executa exclusão de crédito futuro em massa
     */
    public function excluirMassa() {
        
        $parametros = $this->tratarParametros();
        try {
            $this->dao->begin();
            
            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);
            
            $creditoFuturoVo->usuarioExclusao = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : '';//cara da sessao
            $creditoFuturoVo->observacao = $parametros->justificativa;
            $creditoFuturoVo->origem = 1;
            
            $excluidos = true;
            
            foreach ($parametros->excluir_item as $item) {                
                $creditoFuturoVo->id = $item;
                $excluidos = $creditoFuturoBo->excluir($creditoFuturoVo);                
            }
            
            if ($excluidos === true) {
                
                $this->dao->commit();
                
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'Crédito futuro excluído com sucesso.';                
                header('Location:fin_credito_futuro.php?acao=pesquisar');
                
            } else {
                
                $this->dao->rollback();

                $_SESSION['flash_message']['tipo'] = 'erro';
                $_SESSION['flash_message']['mensagem'] = 'Houve um erro no processamento dos dados.';
                header('Location:fin_credito_futuro.php?acao=pesquisar');
                
            }
           
            
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro.php?acao=pesquisar');
            
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro.php?acao=pesquisar');
        }
        
    }
    
    
    /**
     * Executa o encerramento do registro.
     * 
     * @return void 
     */
    public function encerrar() {
        
        $parametros = $this->tratarParametros();
        try {
            

            //Retorna os parametros
            
            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);
            
            $creditoFuturoVo->id = $parametros->cfooid;
            $creditoFuturoVo->usuarioEncerramento = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : '';//cara da sessao
            $creditoFuturoVo->observacao = $parametros->justificativa;
            $creditoFuturoVo->origem = 1;
            
            
            $creditoEncerrado = $creditoFuturoBo->encerrar($creditoFuturoVo);
            
            if ($creditoEncerrado == true) {
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'Crédito futuro encerrado com sucesso.';                
                header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
            } else {
                $_SESSION['flash_message']['tipo'] = 'erro';
                $_SESSION['flash_message']['mensagem'] = 'Houve um erro no processamento dos dados.';
                header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
            }
           
            
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
            
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('Location:fin_credito_futuro.php?acao=editar&id=' . $parametros->cfooid);
        }

    }
    
    
    public function buscarHistoricoPorId() {
        
        $parametros = $this->tratarParametros();
                
        $retorno  = $this->dao->buscarHistoricoPorId($parametros->cfhoid);
        
        echo json_encode($retorno);
        
        exit;
        
    }
    

    /**
     * Buscar cliente por nome sendo ele PJ || PF
     * 
     * @return array $retorno
     */
    public function buscarClienteNome() {

        $parametros = $this->tratarParametros();

        $parametros->tipo = trim($parametros->filtro) != '' ? trim($parametros->filtro) : '';
        $parametros->nome = trim($parametros->term) != '' ? trim($parametros->term) : '';

        $retorno = $this->dao->buscarClienteNome($parametros);

        echo json_encode($retorno);
        exit;
    }

    /**
     * Buscar cliente por documento (CPF/CNPJ)
     * 
     * @return array $retorno
     */
    public function buscarClienteDoc() {

        $parametros = $this->tratarParametros();

        $parametros->tipo = trim($parametros->filtro);
        $parametros->documento = preg_replace("/[^0-9]/", "", $parametros->term);

        $retorno = $this->dao->buscarClienteDoc($parametros);

        echo json_encode($retorno);
        exit;
    }

    /**
     * Buscar cliente por numero de contrato
     * 
     * @return array $retorno json_encode para requisoção ajax
     */
    public function buscarClienteContrato() {

        $parametros = $this->tratarParametros();

        $retorno = $this->dao->buscarClienteContrato($parametros);

        echo json_encode($retorno);
        exit;
    }

    /**
     * Método verificarProtocolo()
     * Verificar por ajax o protocolo informado no step 2 de cadastro de crédito futuro
     * no caso de motivo de crédito do tipo contestação de crédito.
     * 
     * @return void (json de resposta para requisição ajax)
     */
    public function verificarProtocolo() {

        $retorno = array();

        $parametros = $this->tratarParametros();

        $consulta = $this->dao->verificarProtocolo($parametros);

        if ($consulta != false) {
            $retorno['status'] = true;
            $retorno['valor'] = $consulta;
        } else {
            $retorno['status'] = false;
        }

        echo json_encode($retorno);
        exit;
    }

    /**
     * Método verificarProtocolo()
     * Verificar por ajax o protocolo informado no step 2 de cadastro de crédito futuro
     * no caso de motivo de crédito do tipo indicação de amigo.
     * 
     * @return void (json de resposta para requisição ajax)
     */
    public function verificarContrato() {

        $retorno = array();

        $parametros = $this->tratarParametros();

        $consulta = $this->dao->verificarContrato($parametros);

        if ($consulta != false) {
            $retorno['status'] = true;
            $retorno['valor'] = $consulta;
        } else {
            $retorno['status'] = false;
        }

        echo json_encode($retorno);
        exit;
    }

    private function verificarTipoRequisicao($string) {

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return utf8_encode($string);
        }

        return $string;
    }

    ////////////////////////////////////////////////////////////////////////////

    public function carregarhtml() {
        $parametros->conteudo = $_GET['conteudo'];
        $this->view->parametros->listaMotivoCredito = $this->dao->buscarMotivoCreditoFormaPgtCliente();
        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro/cadastrar/" . $parametros->conteudo . ".php";
        exit;
    }

    public function gravarParametrosStep3($parametros) {   
                
        $creditoFuturoCampanhaPromocionalVO = new CreditoFuturoCampanhaPromocionalVO();
        $creditoFuturoMotivoCreditoVO = new CreditoFuturoMotivoCreditoVO();
        $creditoFuturoVo = new CreditoFuturoVO();
        $creditoFuturoBo = new CreditoFuturo($this->dao);
        
        //credito campanha futuro promocional VO
        $creditoFuturoCampanhaPromocionalVO->cfcpoid = 'NULL';
        
        //credito futuro motivo credito
        $creditoFuturoMotivoCreditoVO->id = isset($_SESSION['credito_futuro']['step_2']['cfocfmcoid']) && trim($_SESSION['credito_futuro']['step_2']['cfocfmcoid']) != '' ? trim($_SESSION['credito_futuro']['step_2']['cfocfmcoid']) : NULL;
        $creditoFuturoMotivoCreditoVO->tipo = isset($_SESSION['credito_futuro']['step_2']['tipo_motivo']) && trim($_SESSION['credito_futuro']['step_2']['tipo_motivo']) != '' ? trim($_SESSION['credito_futuro']['step_2']['tipo_motivo']) : NULL;
        $creditoFuturoMotivoCreditoVO->descricao = isset($_SESSION['credito_futuro']['step_2']['motivo_descricao']) && trim($_SESSION['credito_futuro']['step_2']['motivo_descricao']) != '' ? trim($_SESSION['credito_futuro']['step_2']['motivo_descricao']) : NULL;
      
        $creditoFuturoVo->id = 'NULL';
        
        $creditoFuturoVo->cliente = isset($_SESSION['credito_futuro']['step_1']['cfoclioid']) && trim($_SESSION['credito_futuro']['step_1']['cfoclioid']) != '' ? trim($_SESSION['credito_futuro']['step_1']['cfoclioid']) : '';
        
        $creditoFuturoVo->tipoDesconto = isset($parametros->cadastro['cfotipo_desconto']) && trim($parametros->cadastro['cfotipo_desconto']) != '' ? trim($parametros->cadastro['cfotipo_desconto']) : '';
        
        $creditoFuturoVo->status = '';//vazio default
        
        if (isset($parametros->cadastro['cfovalor']) && trim($parametros->cadastro['cfovalor']) != '') {
            $valor = str_replace('.', '', $parametros->cadastro['cfovalor']);
            $valor = str_replace(',', '.', $valor);
        } else {
            $valor = '';
        }
        
        $creditoFuturoVo->valor = $valor;
        
        //print_r($creditoFuturoVO->valor);
        
        $creditoFuturoVo->formaAplicacao = isset($parametros->cadastro['cfoforma_aplicacao']) && trim($parametros->cadastro['cfoforma_aplicacao']) != '' ? $parametros->cadastro['cfoforma_aplicacao'] : '';
        
        $creditoFuturoVo->saldo = '' ;//vazio por default
        
        $creditoFuturoVo->aplicarDescontoSobre = isset($parametros->cadastro['cfoaplicar_desconto']) && trim($parametros->cadastro['cfoaplicar_desconto']) != '' ? $parametros->cadastro['cfoaplicar_desconto'] : '';//verificar                
        
        $creditoFuturoVo->observacao = isset($parametros->cadastro['cfoobservacao']) && trim($parametros->cadastro['cfoobservacao']) != '' ? $parametros->cadastro['cfoobservacao'] : '';//verificar
        
        $creditoFuturoVo->dataInclusao = '';//vazio default
        
        $creditoFuturoVo->dataExclusao = '';//vazio default
        
        $creditoFuturoVo->dataEncerramento = '';//vazio default
        
        $creditoFuturoVo->dataAvaliacao = '';//vazio default
        
        $creditoFuturoVo->usuarioInclusao = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : '';//cara da sessao
        
        $creditoFuturoVo->usuarioExclusao = '';
        
        $creditoFuturoVo->usuarioEncerramento = '';
        
        $creditoFuturoVo->usuarioAvaliador = '';
        
        $creditoFuturoVo->protocolo = isset($_SESSION['credito_futuro']['step_2']['cfoancoid']) && trim($_SESSION['credito_futuro']['step_2']['cfoancoid']) != '' ? trim($_SESSION['credito_futuro']['step_2']['cfoancoid']) : 'NULL'; //caso seja contestação senão null
        
        $creditoFuturoVo->contratoIndicado = isset($_SESSION['credito_futuro']['step_2']['cfoconnum_indicado']) && trim($_SESSION['credito_futuro']['step_2']['cfoconnum_indicado']) != '' ? trim($_SESSION['credito_futuro']['step_2']['cfoconnum_indicado']) : 'NULL'; //caso seja indicacao senão null
                
        $creditoFuturoVo->obrigacaoFinanceiraDesconto = isset($parametros->cadastro['cfoobroid_desconto']) && trim($parametros->cadastro['cfoobroid_desconto']) != '' ? $parametros->cadastro['cfoobroid_desconto'] : '';//verificar
        
        $creditoFuturoVo->formaInclusao = 1;// sempre manual 1- manual / 2- automatica
        
        $creditoFuturoVo->qtdParcelas = isset($parametros->cadastro['cfoqtde_parcelas']) && trim($parametros->cadastro['cfoqtde_parcelas']) != '' ? $parametros->cadastro['cfoqtde_parcelas'] : '1';//verificar
        
        $creditoFuturoVo->MotivoCredito = $creditoFuturoMotivoCreditoVO;
        
        $creditoFuturoVo->origem = 1;
        
    	$creditoFuturoVo->CampanhaPromocional = $creditoFuturoCampanhaPromocionalVO;
                
        return $creditoFuturoBo->incluir($creditoFuturoVo);        
        
    }

    /**
     * Método gravarParametros()
     * Responsável por gravar em sessão os dados submetidos do steps de cadastro.
     * 
     * @param object $parametros
     * @return boolean
     */
    public function gravarParametros($parametros) {

        
        $_SESSION['credito_futuro'][$parametros->step] = array();
        foreach ($parametros->cadastro as $campo => $valor) {
            $_SESSION['credito_futuro'][$parametros->step][$campo] = trim($valor) != '' ? trim($valor) : '';
        }

        if ($_SESSION['credito_futuro'][$parametros->step]) {
            return true;
        } else {
            return false;
        }
    }
    
    
    public function atualizarParametros($novosParametros,$atualParametros) {
                  
         switch ($atualParametros['tipo_motivo_credito']) {

            //contestação
            case '1':
                $validado = $this->validarCamposTipoContestacao($novosParametros);
                break;

            //indicação de amigo
            case '2':
                $validado = $this->validarCamposTipoIndicacao($novosParametros);
                break;

            //isenção
            case '3':
                $validado = $this->validarCamposTipoIsencao($novosParametros);
                break;

            //default
            default:
                $validado = $this->validarCamposTipoDefault($novosParametros);
                break;
        }
        
        if ($validado) {
            //monta vo e chama método de autualização
            $creditoFuturoVo = new CreditoFuturoVO();
            $creditoFuturoBo = new CreditoFuturo($this->dao);
            
            //credito futuro VO
            $creditoFuturoVo->id = $novosParametros['cfooid'];

            $creditoFuturoVo->tipoDesconto = isset($novosParametros['cfotipo_desconto']) ? trim($novosParametros['cfotipo_desconto']) :  '';

            if (isset($novosParametros['cfovalor']) && trim($novosParametros['cfovalor']) != '') {
                $valor = str_replace('.', '', $novosParametros['cfovalor']);
                $valor = str_replace(',', '.', $valor);
            } else {
                $valor = '';
            }

            $creditoFuturoVo->valor = $valor;

            $creditoFuturoVo->formaAplicacao = isset($novosParametros['cfoforma_aplicacao']) ? trim($novosParametros['cfoforma_aplicacao']) : '';

            $creditoFuturoVo->aplicarDescontoSobre = isset($novosParametros['cfoaplicar_desconto']) ? trim($novosParametros['cfoaplicar_desconto']) : ''; //verificar                

            $creditoFuturoVo->observacao = isset($novosParametros['cfoobservacao']) ? trim($novosParametros['cfoobservacao']) : ''; //verificar

            $creditoFuturoVo->usuarioInclusao = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? trim($_SESSION['usuario']['oid']) : ''; //cara da sessao
           
            $creditoFuturoVo->obrigacaoFinanceiraDesconto = isset($novosParametros['cfoobroid_desconto']) ? trim($novosParametros['cfoobroid_desconto']) : ''; //verificar

            $creditoFuturoVo->qtdParcelas = isset($novosParametros['cfoqtde_parcelas']) ? trim($novosParametros['cfoqtde_parcelas']) : '1'; //verificar

            $creditoFuturoVo->origem = 1;
            
            return $creditoFuturoBo->alterar($creditoFuturoVo);  
        }
    }

    /**
     * Método validarCamposStep1()
     * Válida regras do step 1 do cadastro de crédito futuro.
     * 
     * @param type $parametros
     * @return boolean
     * @throws Exception
     */
    public function validarCamposStep1($parametros) {

        $obrigatorios = true;

        if (trim($parametros->cadastro['cfoclioid']) == '') {
            $obrigatorios = false;
        }

        if (!$obrigatorios) {
            throw new Exception("Um cliente deve ser informado.");
        } else {
            return true;
        }
    }

    public function validarCamposTipoContestacao($dados) {
       
        $camposDestaques = array();
        
        $obrigatorios = true;
        $valorDiferenteZero = true;
        $parcelaDiferenteZero = true;
        
        //se valor for vazio
        if (trim($dados['cfovalor']) == '') {
            
            $obrigatorios = false;
            
            $camposDestaques[] = array(
                'campo' => 'valor_tipo_desconto'
            );
            
        }
        
        //se valor for diferente de vazio, mas valor for igual a zero
        if (trim($dados['cfovalor']) != '') {
            $dados['cfovalor'] = str_replace('.', '', $dados['cfovalor']);
            $dados['cfovalor'] = str_replace(',', '.', $dados['cfovalor']);
            
            if (floatval($dados['cfovalor']) == 0) {
                
                $valorDiferenteZero = false;
                
                $camposDestaques[] = array(
                    'campo' => 'valor_tipo_desconto'
                );
            }
        }
        
        //se a forma de aplicação for em parcelas verifico, se o valor da parcela é dierente de zero
        if ($dados['cfoforma_aplicacao'] == '2') {
            
            if (trim($dados['cfoqtde_parcelas']) == '') {
                
                $obrigatorios = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            } else if (intval($dados['cfoqtde_parcelas']) == 0) {
                
                $parcelaDiferenteZero = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            }
            
        }
        
        //verifico se o campo Obrigação financeira de desconto foi selecionado
        if (trim($dados['cfoobroid_desconto']) == '') {
            
            $obrigatorios = false;

            $camposDestaques[] = array(
                'campo' => 'cfoobroid_desconto'
            );
                
        }
        
        $this->view->dados = $camposDestaques;
        
        //lançamento de exceções
        if (!$obrigatorios) {
            
            throw new Exception("Informações obrigatórias não preenchidas.");
        }

        if (!$valorDiferenteZero) {

            throw new Exception("O valor do desconto não pode ser igual a 0.");
        }

        if (!$parcelaDiferenteZero) {

            throw new Exception("A quantidade de parcelas não pode ser igual a 0.");
        }
        
        return true;
        
    }
    
    public function validarCamposTipoIndicacao($dados) {
        
        $camposDestaques = array();
        
        $obrigatorios = true;
        $valorDiferenteZero = true;
        $percentualDiferenteZero = true;
        $percentualMaiorCem = true;
        $parcelaDiferenteZero = true;
        
        
        //Valida valor de desconto sendo ele percentual ou monetário
        if (trim($dados['cfovalor']) == '') {

            $obrigatorios = false;

            $camposDestaques[] = array(
                'campo' => 'valor_tipo_desconto'
            );
            
        } else {

            $dados['cfovalor'] = str_replace('.', '', $dados['cfovalor']);
            $dados['cfovalor'] = floatval(str_replace(',', '.', $dados['cfovalor']));

            //percentual
            if ($dados['cfotipo_desconto'] == '1') {
                
                if (floatval($dados['cfovalor']) > 100) {

                    $percentualMaiorCem = false;

                    $camposDestaques[] = array(
                        'campo' => 'valor_tipo_desconto'
                    );
                }
                
                if (floatval($dados['cfovalor']) == 0) {

                    $percentualDiferenteZero = false;

                    $camposDestaques[] = array(
                        'campo' => 'valor_tipo_desconto'
                    );
                }
                
            } else {
                //valor monetario
                if (floatval($dados['cfovalor']) == 0) {

                    $valorDiferenteZero = false;

                    $camposDestaques[] = array(
                        'campo' => 'valor_tipo_desconto'
                    );
                }
            }
        }
        
        //se a forma de aplicação for em parcelas verifico, se o valor da parcela é dierente de zero
        if ($dados['cfoforma_aplicacao'] == '2') {
            
            if (trim($dados['cfoqtde_parcelas']) == '') {
                
                $obrigatorios = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            } else if (intval($dados['cfoqtde_parcelas']) == 0) {
                
                $parcelaDiferenteZero = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            }
            
        }
        
        //verifico se o campo Obrigação financeira de desconto foi selecionado
        if (trim($dados['cfoobroid_desconto']) == '') {
            
            $obrigatorios = false;

            $camposDestaques[] = array(
                'campo' => 'cfoobroid_desconto'
            );
                
        }
        
        $this->view->dados = $camposDestaques;
        
        //lançamento de exceções
        if (!$obrigatorios) {

            throw new Exception("Informações obrigatórias não preenchidas.");
        }

        if (!$valorDiferenteZero) {

            throw new Exception("O valor do desconto não pode ser igual a 0.");
        }

        if (!$parcelaDiferenteZero) {

            throw new Exception("A quantidade de parcelas não pode ser igual a 0.");
        }
        
        if (!$percentualDiferenteZero) {
            
            throw new Exception("O percentual do desconto não pode ser igual a 0%.");
        }
        
        if (!$percentualMaiorCem) {
            
            throw new Exception("O percentual do desconto não pode ser maior que 100%.");
        }
        
        return true;
        
        
    }
    
    public function validarCamposTipoIsencao($dados) {
        
        $camposDestaques = array();
        
        $obrigatorios = true;
        $parcelaDiferenteZero = true;
        
        //se a forma de aplicação for em parcelas verifico, se o valor da parcela é dierente de zero
        
            
            if (trim($dados['cfoqtde_parcelas']) == '') {
                
                $obrigatorios = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            } else if (intval($dados['cfoqtde_parcelas']) == 0) {
                
                $parcelaDiferenteZero = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            }            
        
        
        //verifico se o campo Obrigação financeira de desconto foi selecionado
        if (trim($dados['cfoobroid_desconto']) == '') {
            
            $obrigatorios = false;

            $camposDestaques[] = array(
                'campo' => 'cfoobroid_desconto'
            );
                
        }
        
        $this->view->dados = $camposDestaques;
        
        //lançamento de exceções
        if (!$obrigatorios) {

            throw new Exception("Informações obrigatórias não preenchidas.");
        }

        if (!$parcelaDiferenteZero) {

            throw new Exception("A quantidade de parcelas não pode ser igual a 0.");
        }
        
        
        
        return true;
    }
    
    public function validarCamposTipoDefault($dados) {
        
        $camposDestaques = array();
        
        $obrigatorios = true;
        $valorDiferenteZero = true;
        $percentualDiferenteZero = true;
        $percentualMaiorCem = true;
        $parcelaDiferenteZero = true;
        
        
        //Valida valor de desconto sendo ele percentual ou monetário
        if (trim($dados['cfovalor']) == '') {

            $obrigatorios = false;

            $camposDestaques[] = array(
                'campo' => 'valor_tipo_desconto'
            );
            
        } else {

            $dados['cfovalor'] = str_replace('.', '', $dados['cfovalor']);
            $dados['cfovalor'] = floatval(str_replace(',', '.', $dados['cfovalor']));

            //percentual
            if ($dados['cfotipo_desconto'] == '1') {
                
                if (floatval($dados['cfovalor']) > 100) {

                    $percentualMaiorCem = false;

                    $camposDestaques[] = array(
                        'campo' => 'valor_tipo_desconto'
                    );
                }
                
                if (floatval($dados['cfovalor']) == 0) {

                    $percentualDiferenteZero = false;

                    $camposDestaques[] = array(
                        'campo' => 'valor_tipo_desconto'
                    );
                }
                
            } else {
                //valor monetario
                if (floatval($dados['cfovalor']) == 0) {

                    $valorDiferenteZero = false;

                    $camposDestaques[] = array(
                        'campo' => 'valor_tipo_desconto'
                    );
                }
            }
        }
        
        //se a forma de aplicação for em parcelas verifico, se o valor da parcela é dierente de zero
        if ($dados['cfoforma_aplicacao'] == '2') {
            
            if (trim($dados['cfoqtde_parcelas']) == '') {
                
                $obrigatorios = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            } else if (intval($dados['cfoqtde_parcelas']) == 0) {
                
                $parcelaDiferenteZero = false;
                
                $camposDestaques[] = array(
                    'campo' => 'cfoqtde_parcelas'
                );
                
            }
            
        }
        
        //verifico se o campo Obrigação financeira de desconto foi selecionado
        if (trim($dados['cfoobroid_desconto']) == '') {
            
            $obrigatorios = false;

            $camposDestaques[] = array(
                'campo' => 'cfoobroid_desconto'
            );
                
        }
        
        $this->view->dados = $camposDestaques;
        
        //lançamento de exceções
        if (!$obrigatorios) {

            throw new Exception("Informações obrigatórias não preenchidas.");
        }

        if (!$valorDiferenteZero) {

            throw new Exception("O valor do desconto não pode ser igual a 0.");
        }

        if (!$parcelaDiferenteZero) {

            throw new Exception("A quantidade de parcelas não pode ser igual a 0.");
        }
        
        if (!$percentualDiferenteZero) {
            
            throw new Exception("O percentual do desconto não pode ser igual a 0%.");
        }
        
        if (!$percentualMaiorCem) {
            
            throw new Exception("O percentual do desconto não pode ser maior que 100%.");
        }
        
        return true;
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


}

