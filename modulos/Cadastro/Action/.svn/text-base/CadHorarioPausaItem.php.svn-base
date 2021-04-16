<?php

/**
 * Classe padrão para Action
 *
 * @since   version 20/08/2013
 * @package Cadastro
 * @category Action
 */
class CadHorarioPausaItem {

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
     * Mensagem de alerta para horário inválido
     * @const String
     */
    const MENSAGEM_ALERTA_HORARIO_INVALIDO = "Horário inválido.";
    
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
     * Mensagem de alerta para sobreposição de pausas
     * @const string
     */
    const MENSAGEM_ALERTA_SOBREPOSICAO_PAUSA = "O horário da pausa sobrepõe uma pausa já cadastrada para este atendente.";
	
	/**
	 * Mensagem de alerta para registro duplicado
	 * @const string
	 */
	const MENSAGEM_ALERTA_REGISTRO_DUPLICADO = "Já existe uma pausa desse tipo cadastrada para esse atendente.";
    
    /**
     * Mensagem de alerta para intervalo de horario
     * @const string
     */
    const MENSAGEM_ALERTA_INTERVALO_HORARIO = "O tempo deve corresponder ao intervalo de horários.";
    	    
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

        //permissão de acesso
        $this->view->acesso = $this->verificarAcessoUsuario();
    }

    /**
     * Método padrão da classe.
     *
     * Reponsável também por realizar a pesquisa invocando o método privado
     *
     * @return void
     */
    public function index() {
        
        $this->view->parametros = $this->tratarParametros();

        //Inicializa os dados
        $this->inicializarParametros();
        
        if(!$this->view->acesso) {
            
            try {
                
                $this->view->parametros->hrpiatendente = $_SESSION['usuario']['oid'];
                
                $this->view->dados = $this->pesquisar($this->view->parametros);
            } catch (ErrorException $e) {
                $this->view->mensagemErro = $e->getMessage();
            } catch (Exception $e) {
                $this->view->mensagemAlerta = $e->getMessage();
            }
        } else {
        
            try {

                $retorno = array();

                //Verificar se a ação pesquisar e executa pesquisa
                if (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar') {
                    $this->view->dados = $this->pesquisar($this->view->parametros);

                    ob_start();
                    require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/resultado_pesquisa.php";
                    $this->view->resultadoPesquisa = ob_get_contents();
                    ob_end_clean();

                    $retorno = array(
                        'status' => $this->view->status,
                        'resultado' => utf8_encode($this->view->resultadoPesquisa)
                    );

                    if ($this->view->registroGravado) {
                        $retorno['mensagemSucesso'] = utf8_encode(self::MENSAGEM_SUCESSO_INCLUIR);
                    }

                    echo json_encode($retorno);

                    return;
                }
            } catch (ErrorException $e) {
                $this->view->status = false;
                $this->view->mensagemErro = $e->getMessage();

                echo json_encode(
                        array(
                            'status' => $this->view->status,
                            'mensagemErro' => $this->view->mensagemErro
                    )
                );

                return;
            } catch (Exception $e) {
                $this->view->status = false;
                $this->view->mensagemAlerta = $e->getMessage();

                echo json_encode(
                        array(
                            'status' => $this->view->status,
                            'dados' => $this->view->dados,
                            'mensagemAlerta' => $this->view->mensagemAlerta
                    )
                );

                return;
            }
        }

        //Inclir a view padrão
        require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/index.php";
    }

    /**
     * Verifica a permissão de acesso do usuário logado
     * @return boolean
     */
    private function verificarAcessoUsuario() {

        $idUsuario = isset($_SESSION['usuario']['oid']) ? (int) $_SESSION['usuario']['oid'] : 0;

        if (!empty($idUsuario)) {
           return $this->dao->validarAcessoUsuario($idUsuario);
        }
        	
        return false;

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
            //Limpa o POST
            unset($_POST);
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
            //Limpa o GET
            unset($_GET);
        }
        
        /* 
         *Força o parâmetro horario_final que não virá por POST ou GET.
         */
        $retorno->horario_final = '';
        
        if (!empty($retorno->horario_inicial) && isset($retorno->hrpitempo) && trim($retorno->hrpitempo) != '') {
            if ($timestamp = strtotime('1980-01-01 '.$retorno->horario_inicial.':00')) {
                $timestamp+= $retorno->hrpitempo * 60;
                
                $retorno->horario_final = date('H:i', $timestamp);
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
        $this->view->parametros->gtroid = isset($this->view->parametros->gtroid) ? trim($this->view->parametros->gtroid) : '';
        $this->view->parametros->motaoid = isset($this->view->parametros->motaoid) ? trim($this->view->parametros->motaoid) : '';
        $this->view->parametros->gtuusoid = isset($this->view->parametros->gtuusoid) ? trim($this->view->parametros->gtuusoid) : '';
        $this->view->parametros->hrpitempo = isset($this->view->parametros->hrpitempo) ? trim($this->view->parametros->hrpitempo) : '';
        $this->view->parametros->tolerancia = isset($this->view->parametros->tolerancia) ? trim($this->view->parametros->tolerancia) : '';
        $this->view->parametros->horario_inicial = isset($this->view->parametros->horario_inicial) ? trim($this->view->parametros->horario_inicial) : '';
        $this->view->parametros->horario_final = isset($this->view->parametros->horario_final) ? trim($this->view->parametros->horario_final) : '';
        $this->view->parametros->filtro_horario = isset($this->view->parametros->filtro_horario) ? trim($this->view->parametros->filtro_horario) : '';
        $this->view->parametros->hrpiusuoid = isset($_SESSION['usuario']['oid']) ? (int) $_SESSION['usuario']['oid'] : 0;
        $this->view->parametros->hrpioid = isset($this->view->parametros->hrpioid) ? trim($this->view->parametros->hrpioid) : array();

        //Popula a combo Grupo de Trabalho (demais combos regras no JS)
        $this->view->parametros->comboGrupoTrabalho = $this->dao->buscarGrupoTrabalho();
    }

    /**
     * Popula a combo Tipo Pausa
     * @return void
     */
    public function carregarComboTipoPausa() {

        $parametros = $this->tratarParametros();

        try {

            $retorno = $this->dao->buscarTipoPausa($parametros->gtroid);

            echo json_encode($retorno);
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }
    }

    /**
     * Popula a combo Atendente
     * @return void
     */
    public function carregarComboAtendente() {

        $parametros = $this->tratarParametros();

        try {

            $retorno = $this->dao->buscarAtendente($parametros->gtroid);

            echo json_encode($retorno);
        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();
        }
    }
    
    /**
     * Popula o tempo e tolerancia, de acordo com a tela de parametros
     * @return json
     */
    public function buscarParametrosPausa() {

        $parametros = $this->tratarParametros();

        try {

            $retorno = $this->dao->buscarParametrosPausa($parametros);

            echo json_encode($retorno);
            
        } catch (ErrorException $e) {
            
            echo json_encode(
                    array(
                        'status' => false,
                        'mensagemErro' => $e->getMessage()
                    )
                );
            
        } catch (Exception $e) {
            echo json_encode(
                    array(
                        'status' => false,
                        'mensagemAlerta' => $e->getMessage()
                    )
                );
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
        
        if(!empty($filtros->horario_inicial) && !empty($filtros->horario_final)) {
            /**
             * Validação para os horários
            */
            $arrayHorarioInicial = explode(':', $filtros->horario_inicial);
            $arrayHorarioFinal = explode(':', $filtros->horario_final);

            //Validação dos horários
            $horarioValido = $this->validarIntervaloHoras($filtros->horario_inicial, $filtros->horario_final);

            //Horarios são iguais ou invalidos
            if (!$horarioValido) {

                $camposDestaques[] = array(
                    'campo' => 'horario_inicial'
                );

                $camposDestaques[] = array(
                    'campo' => 'horario_final'
                );

                $this->view->dados = $camposDestaques;

                throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_HORARIO_INVALIDO));
            }

            if((int)$arrayHorarioInicial[0] > 23 || (int)$arrayHorarioInicial[1] > 59) {
                $camposDestaques[] = array(
                    'campo' => 'horario_inicial',
                    'mensagem' => utf8_encode('Inválido.')
                );

                $error = true;
            }

            if((int)$arrayHorarioFinal[0] > 23 || (int)$arrayHorarioFinal[1] > 59) {
                $camposDestaques[] = array(
                    'campo' => 'horario_final',
                    'mensagem' => utf8_encode('Inválido.')
                );
                $error = true;
            }

            if ($error) {
                $this->view->dados = $camposDestaques;
                throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_HORARIO_INVALIDO));
            }
            
        }
        
        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = true;

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
        //Inicia a transação
        $this->dao->begin();
        
        try {
            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros();

            $this->view->registroGravado = $this->salvar($this->view->parametros);

            $this->view->dados = $this->pesquisar($this->view->parametros);
            ob_start();
            require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/resultado_pesquisa.php";
            $this->view->resultadoPesquisa = ob_get_contents();
            ob_end_clean();

            $mensagem = self::MENSAGEM_SUCESSO_INCLUIR;
            
            if ($this->view->registroGravado) {
                $mensagem = $this->view->mensagemSucesso;
            }
            
            $retorno = array(
                'status' => $this->view->status,
                'mensagemSucesso' => utf8_encode($mensagem),
                'resultado' => utf8_encode($this->view->resultadoPesquisa)
            );

            echo json_encode($retorno);
            
            //Comita a transação
            $this->dao->commit();
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->status = false;
            $this->view->mensagemErro = $e->getMessage();

            echo json_encode(
                    array(
                        'status' => $this->view->status,
                        'mensagemErro' => $this->view->mensagemErro
                )
            );
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->status = false;
            $this->view->mensagemAlerta = $e->getMessage();

            echo json_encode(
                    array(
                        'status' => $this->view->status,
                        'dados' => $this->view->dados,
                        'mensagemAlerta' => $this->view->mensagemAlerta
                )
            );
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

        //Gravação
        $gravacao = null;

        if (!empty($dados->hrpioid)) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        return $gravacao;
    }

    /**
     * Inibição da mensagem no sistema de atendimento
     * @return void
     */
    public function inibir() {
        //Inicia a transação
        $this->dao->begin();
        
        try{
        
            $this->view->parametros = $this->tratarParametros();
            
            //Incializa os parametros
            $this->inicializarParametros();
            
            $this->view->registroAlterado = $this->dao->atualizar($this->view->parametros);

            if(count($this->view->parametros->inibir) > 1) {
                $this->view->mensagemSucesso = 'Registros alterados com sucesso. ';
            } else {
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
            }
            
            //Comita a transação
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
        
        $this->index();
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
        //Grupo Trabalho
        if (!isset($dados->gtroid) || trim($dados->gtroid) == '') {

            $camposDestaques[] = array(
                'campo' => 'gtroid',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        //Tipo Pausa
        if (!isset($dados->motaoid) || trim($dados->motaoid) == '') {

            $camposDestaques[] = array(
                'campo' => 'motaoid',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        //Atendente
        if (!isset($dados->hrpiatendente) || trim($dados->hrpiatendente) == '') {

            $camposDestaques[] = array(
                'campo' => 'hrpiatendente',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        //Tempo
        if (!isset($dados->hrpitempo) || trim($dados->hrpitempo) == '') {

            $camposDestaques[] = array(
                'campo' => 'hrpitempo',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        //Tolerancia
        if (!isset($dados->tolerancia) || trim($dados->tolerancia) == '') {

            $camposDestaques[] = array(
                'campo' => 'tolerancia',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        //Horario De
        if (!isset($dados->horario_inicial) || trim($dados->horario_inicial) == '') {

            $camposDestaques[] = array(
                'campo' => 'horario_inicial',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        //Horário até
        if (!isset($dados->horario_final) || trim($dados->horario_final) == '') {

            $camposDestaques[] = array(
                'campo' => 'horario_final',
                'mensagem' => utf8_encode('Campo obrigatório.')
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS));
            
            return;
        }

        /**
         * Validação para os horários
         */
        $arrayHorarioInicial = explode(':', $dados->horario_inicial);
        $arrayHorarioFinal = explode(':', $dados->horario_final);
        
        //Validação dos horários
        $horarioValido = $this->validarIntervaloHoras($dados->horario_inicial, $dados->horario_final);
        
        //Diferença em minutos
        $intervaloMinutos = $this->buscarIntervaloHoras($dados->horario_inicial, $dados->horario_final);
        
        //Horarios são iguais ou invalidos
        if (!$horarioValido) {

            $camposDestaques[] = array(
                'campo' => 'horario_inicial'
            );

            $camposDestaques[] = array(
                'campo' => 'horario_final'
            );

            $this->view->dados = $camposDestaques;

            throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_HORARIO_INVALIDO));
            
            return;
        }
        
        if((int)$arrayHorarioInicial[0] > 23 || (int)$arrayHorarioInicial[1] > 59) {
            $camposDestaques[] = array(
                'campo' => 'horario_inicial',
                'mensagem' => utf8_encode('Inválido.')
            );

            $error = true;
        }

        if((int)$arrayHorarioFinal[0] > 23 || (int)$arrayHorarioFinal[1] > 59) {
            $camposDestaques[] = array(
                'campo' => 'horario_final',
                'mensagem' => utf8_encode('Inválido.')
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_HORARIO_INVALIDO));
            
             return;
        }
        
        if($intervaloMinutos != (int)$dados->hrpitempo) {
            
            $camposDestaques[] = array(
                'campo' => 'hrpitempo'
            );
            
            $camposDestaques[] = array(
                'campo' => 'horario_inicial'
            );
            
            $camposDestaques[] = array(
                'campo' => 'horario_final'
            );
            
            $this->view->dados = $camposDestaques;
            
            throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_INTERVALO_HORARIO));
            
            return;
        }

        // Verifica conflitos de tipo e usuário
        if ($this->dao->verificarTiposCadastrados($dados)) {
            throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_REGISTRO_DUPLICADO));
        }
        
        // Verifica conflitos de horários com pausas
        if($this->dao->verificarIntervaloDisponivel($dados)){
        	 
        	throw new Exception(utf8_encode(self::MENSAGEM_ALERTA_SOBREPOSICAO_PAUSA));
        }
    }

    /**
     * Calcula a diferença entre dois horários
     *
     * @param string $horaInicial
     * @param string $horaFinal
     * @return boolean
     */
    private function validarIntervaloHoras($horaInicial, $horaFinal) {
        $horaInicial = strtotime("1/1/1980 ". $horaInicial ."");
        $horaFinal = strtotime("1/1/1980 ". $horaFinal ."");
        
        return $horaFinal > $horaInicial;
    }
    
    /**
     * Calcula a diferença entre dois horários e retorna a diferença em minutos
     *
     * @param string $horaInicial
     * @param string $horaFinal
     * @return number
     */
    private function buscarIntervaloHoras($horaInicial, $horaFinal) {
        $horaInicial = strtotime("1/1/1980 ". $horaInicial ."");
        $horaFinal = strtotime("1/1/1980 ". $horaFinal ."");
        
        $resultado = ($horaFinal - $horaInicial) / 60;
        
        return (int)$resultado;
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
            if (!isset($parametros->hrpioid) || empty($parametros->hrpioid)) {

                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->hrpioid = (int) $parametros->hrpioid;

            //Remove o registro
            $this->dao->excluir($parametros->hrpioid);

            //Comita a transação
            $this->dao->commit();

            $this->view->dados = $this->pesquisar($parametros);
            ob_start();
            require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/resultado_pesquisa.php";
            $this->view->resultadoPesquisa = ob_get_contents();
            ob_end_clean();

            $retorno = array(
                'status' => $this->view->status,
                'mensagemSucesso' => utf8_encode(self::MENSAGEM_SUCESSO_EXCLUIR),
                'resultado' => utf8_encode($this->view->resultadoPesquisa)
            );

            echo json_encode($retorno);
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->status = false;
            $this->view->mensagemErro = $e->getMessage();

            echo json_encode(
                    array(
                        'status' => $this->view->status,
                        'mensagemErro' => $this->view->mensagemErro
                )
            );
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->status = false;
            $this->view->mensagemAlerta = $e->getMessage();

            echo json_encode(
                    array(
                        'status' => $this->view->status,
                        'mensagemAlerta' => $this->view->mensagemAlerta
                )
            );
        }
    }

    /**
     * Verifica se existem pausas obrigatorias não cadastradas para um atendente
     *
     * @throws Exception
     *
     * @return void
     */
    public function validarPausaObrigatoria() {

        $dados = $this->tratarParametros();

        $pausasObrigatoriasFaltando = $this->dao->verificarPausaObrigatoria($dados);

        if (count($pausasObrigatoriasFaltando)) {

            $erroFaltamPausasObrigatorias = "Existem horários a serem lançados: <br /><ul style=\"margin: 13px; padding: 0;\">";
            foreach ($pausasObrigatoriasFaltando as $pausa) {
                $erroFaltamPausasObrigatorias .= '<li>' . $pausa->motamotivo . "</li>";
            }
            
            $erroFaltamPausasObrigatorias .= '</ul>';
            
            $this->view->mensagemAlerta = utf8_encode($erroFaltamPausasObrigatorias);
            
            echo json_encode(array('mensagemAlerta' => $this->view->mensagemAlerta));
        }
    }

    /**
     * Recupera as informações de um horário de pausa.
     * 
     * @return Void
     */
    public function carregarDadosPausa() {
        $retorno = new stdClass();
        $retorno->dados  = null;
        $retorno->status = true;
        $retorno->mensagem->tipo  = null;
        $retorno->mensagem->texto = null;
        
        try {
            $parametros = $this->tratarParametros();
            
            if (!$retorno->dados = $this->dao->pesquisarPorID($parametros->hrpioid)) {
                $retorno->status = false;
                $retorno->mensagem->tipo  = 'erro';
                $retorno->mensagem->texto = utf8_encode(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        } catch (ErrorException $e) {
            $retorno->status = false;
            $retorno->mensagem->tipo  = 'erro';
            $retorno->mensagem->texto = $e->getMessage();
        } catch (Exception $e) {
            $retorno->status = false;
            $retorno->mensagem->tipo  = 'alerta';
            $retorno->mensagem->texto = $e->getMessage();
        }
        
        echo json_encode($retorno);
    }
    
}

