<?php

/**
 * Classe CadParametrizacaoRsCalculoRepasse.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 *
 */
class CadParametrizacaoRsCalculoRepasse {

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
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            $this->view->dados = $this->pesquisar($this->view->parametros);


        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/index.php";
    }

    public function historico() {

        try {
            $this->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->parametros->acaoHistorico) && $this->parametros->acaoHistorico == 'pesquisar' ) {
                $this->view->dados = $this->pesquisarHistorico($this->parametros);
            }


        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/historico.php";
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
    	if (!isset($this->view->parametros)){
    		$this->view->parametros = new stdClass();
    	} 
        $this->view->parametros->dataUltimoHistorico = $this->dao->buscarDataUltimoHistorico();
        $this->view->parametros->usuoid = $_SESSION['usuario']['oid'];

        $this->view->parametros->prscrfaixa_inicial = isset($this->view->parametros->prscrfaixa_inicial) && trim($this->view->parametros->prscrfaixa_inicial) != '' ? number_format($this->view->parametros->prscrfaixa_inicial, 0, '', '.') : '';
        $this->view->parametros->prscrfaixa_final = isset($this->view->parametros->prscrfaixa_final) && trim($this->view->parametros->prscrfaixa_final) != '' ? number_format($this->view->parametros->prscrfaixa_final, 0, '', '.') : '';
        $this->view->parametros->prscrrevenue_share_vivo = isset($this->view->parametros->prscrrevenue_share_vivo) && trim($this->view->parametros->prscrrevenue_share_vivo) != '' ? number_format(str_replace(',', '.', $this->view->parametros->prscrrevenue_share_vivo), 2, ',', '.') : '';
        $this->view->parametros->prscrrevenue_share_sascar = isset($this->view->parametros->prscrrevenue_share_sascar) && trim($this->view->parametros->prscrrevenue_share_sascar) != '' ? number_format(str_replace(',', '.', $this->view->parametros->prscrrevenue_share_sascar), 2, ',', '.') : '';
        $this->view->parametros->prscrpreco_minimo = isset($this->view->parametros->prscrpreco_minimo) && trim($this->view->parametros->prscrpreco_minimo) != '' ? number_format(str_replace(',', '.', $this->view->parametros->prscrpreco_minimo), 2, ',', '.') : '';
        $this->view->parametros->prscrincremento_valor = isset($this->view->parametros->prscrincremento_valor) && trim($this->view->parametros->prscrincremento_valor) != '' ? number_format(str_replace(',', '.', $this->view->parametros->prscrincremento_valor), 2, ',', '.') : '';

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisarHistorico(stdClass $filtros) {

        //Validar os campos
        $this->validarCamposPesquisa($filtros);

        $resultadoPesquisa = $this->dao->pesquisarHistorico($filtros);

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
            //$this->inicializarParametros();
            $this->view->parametros->usuoid = $_SESSION['usuario']['oid'];

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

        if($registroGravado) {
            unset($_POST);
            $this->index();
        } else {
            require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/index.php";
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
            $acao = $parametros->acao;

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->prscroid) && intval($parametros->prscroid) > 0) {
                //Realiza o CAST do parametro
                $prscroid = (int) $parametros->prscroid;

                //Pesquisa o registro para edição
                $this->view->parametros = $this->dao->pesquisarPorID($prscroid);
                $this->view->parametros->prscroid = $prscroid;
                $this->view->parametros->acao = $acao;

                $this->inicializarParametros();

                //Chama o metodo para edição passando os dados do registro por parametro.
                require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/editar.php";
            } else {
                throw new Exception("É necessário informar um registro para a edição");

            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        } catch (Exception $e) {
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

        if ($dados->prscroid > 0) {
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

        if (!isset($dados->prscrfaixa_inicial) || trim($dados->prscrfaixa_inicial) == '') {
            $camposDestaques[] = array(
                'campo' => 'prscrfaixa_inicial'
            );
            $error = true;
        }

        if (!isset($dados->prscrfaixa_final) || trim($dados->prscrfaixa_final) == '') {
            $camposDestaques[] = array(
                'campo' => 'prscrfaixa_final'
            );
            $error = true;
        }

        if (!isset($dados->prscrrevenue_share_vivo) || trim($dados->prscrrevenue_share_vivo) == '') {
            $camposDestaques[] = array(
                'campo' => 'prscrrevenue_share_vivo'
            );
            $error = true;
        }


        if (!isset($dados->prscrrevenue_share_sascar) || trim($dados->prscrrevenue_share_sascar) == '') {
            $camposDestaques[] = array(
                'campo' => 'prscrrevenue_share_sascar'
            );
            $error = true;
        }

        if (!isset($dados->prscrpreco_minimo) || trim($dados->prscrpreco_minimo) == '') {
            $camposDestaques[] = array(
                'campo' => 'prscrpreco_minimo'
            );
            $error = true;
        }

        if (!isset($dados->prscrincremento_valor) || trim($dados->prscrincremento_valor) == '') {
            $camposDestaques[] = array(
                'campo' => 'prscrincremento_valor'
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        /* if((int)$dados->prscrrevenue_share_sascar > 100) {
            $camposDestaques[] = array(
                'campo' => 'prscrrevenue_share_sascar'
            );
            $error = true;
        }

        if((int)$dados->prscrrevenue_share_vivo > 100) {
            $camposDestaques[] = array(
                'campo' => 'prscrrevenue_share_vivo'
            );
            $error = true;
        } */

        if((str_replace(',', '.', $dados->prscrrevenue_share_vivo) + str_replace(',', '.', $dados->prscrrevenue_share_sascar)) != 100) {
        	$camposDestaques[] = array(
                'campo' => 'prscrrevenue_share_sascar'
        	);
        	$camposDestaques[] = array(
                'campo' => 'prscrrevenue_share_vivo'
        	);
        	$error = true;
        }


        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception('O resultado da soma, de Revenue Share VIVO e SASCAR, deve ser 100%.');
        }

        if(empty($dados->prscroid)) {

            if((int) str_replace('.', '', $dados->prscrfaixa_final) < (int) str_replace('.', '', $dados->prscrfaixa_inicial)) {
                $camposDestaques[] = array(
                    'campo' => 'prscrfaixa_final'
                );

                $camposDestaques[] = array(
                    'campo' => 'prscrfaixa_inicial'
                );

                $error = true;
            }

            if ($error) {
                $this->view->dados = $camposDestaques;
                throw new Exception('O Valor Final não pode ser menor que o Valor Inicial.');
            }

            $ultimoRegistro = $this->dao->buscarUltimoRegistro();

            if((int) str_replace('.', '', $dados->prscrfaixa_inicial) < (int) $ultimoRegistro->prscrfaixa_final) {

                $camposDestaques[] = array(
                    'campo' => 'prscrfaixa_inicial'
                );

                $error = true;
            }

            if ($error) {
                $this->view->dados = $camposDestaques;
                throw new Exception('O Valor Inicial não pode ser menor que o último Valor Final incluído.');
            }

            if(($dados->prscrfaixa_inicial > 0) && ((double)$ultimoRegistro->prscrfaixa_final + 1) > ((int) str_replace('.', '', $dados->prscrfaixa_inicial))) {

                $camposDestaques[] = array(
                    'campo' => 'prscrfaixa_inicial'
                );

                $error = true;
            }

            if ($error) {
                $this->view->dados = $camposDestaques;
                throw new Exception('O Valor Inicial deve ser [maior valor final + 1].');
            }

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

        if ((!isset($dados->data_inicial) || trim($dados->data_inicial) == '') || (!isset($dados->data_final) || trim($dados->data_final) == ''))  {
            $camposDestaques[] = array(
                'campo' => 'data_inicial'
            );

             $camposDestaques[] = array(
                'campo' => 'data_final'
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

            //Inicializa os dados
            $this->inicializarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->prscroid) || trim($parametros->prscroid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->prscroid = (int) $parametros->prscroid;

            $dados = $this->dao->pesquisarPorID($parametros->prscroid);
            $dados->usuoid = $this->view->parametros->usuoid;
            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->prscroid, $dados);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
                unset($_POST);
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


}

