<?php

/**
 * Classe CadGrupoControleDocumento.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   Robson Aparecido Trizotte da Silva <robson.silva@meta.com.br>
 * 
 */
class CadGrupoControleDocumento {

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

            if (isset($_SESSION['flash_message']) && count($_SESSION['flash_message'])) {
                if ($_SESSION['flash_message']['tipo'] == 'sucesso') {
                    $this->view->mensagemSucesso = $_SESSION['flash_message']['mensagem'];
                }

                if ($_SESSION['flash_message']['tipo'] == 'alerta') {
                    $this->view->mensagemAlerta = $_SESSION['flash_message']['mensagem'];
                }

                if ($_SESSION['flash_message']['tipo'] == 'erro') {
                    $this->view->mensagemErro = $_SESSION['flash_message']['mensagem'];
                }
                
                $this->view->parametros = '';
                unset($_SESSION['flash_message']);
            }
            

            if (isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar') {

                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);
            } else if ($_SESSION['pesquisa_pendentes']['usarSessao'] && $_GET['acao'] == 'pesquisar') {

                $this->view->parametros = (object) $_SESSION['pesquisa_pendentes'];
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
        
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_grupo_controle_documento/index.php";
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
		$this->view->parametros->itsedescricao = isset($this->view->parametros->itsedescricao) && !empty($this->view->parametros->itsedescricao) ? trim($this->view->parametros->itsedescricao) : ""; 
		$this->view->parametros->itseoid = isset($this->view->parametros->itseoid) && trim($this->view->parametros->itseoid) != "" ? trim($this->view->parametros->itseoid) : 0 ; 


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

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('LOCATION: cad_grupo_controle_documento.php?acao=pesquisar');

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){

            header('LOCATION: cad_grupo_controle_documento.php?acao=pesquisar');

        } else {
            
            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Cadastro/View/cad_grupo_controle_documento/cadastrar.php";
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
            if (isset($parametros->itseoid) && intval($parametros->itseoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->itseoid = (int) $parametros->itseoid;
                
                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->itseoid);
				
                //Chama o metodo para edição passando os dados do registro por parametro.
                $this->cadastrar($dados);
            } else {
                header('LOCATION: cad_grupo_controle_documento.php?acao=pesquisar');
            }
            
        } catch (ErrorException $e) {

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
            header('LOCATION: cad_grupo_controle_documento.php?acao=pesquisar');
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

        if ($dados->itseoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);
            
            $_SESSION['flash_message']['tipo'] = 'sucesso';
            $_SESSION['flash_message']['mensagem'] = 'O registro foi alterado com sucesso.';

        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);

            $_SESSION['flash_message']['tipo'] = 'sucesso';
            $_SESSION['flash_message']['mensagem'] = 'O registro foi incluído com sucesso.';
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
        $grupoExistenteErro = false;

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

        if (!isset($dados->itsedescricao) || trim($dados->itsedescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'itsedescricao'
            );
            $error = true;
        } else if ($this->dao->verificarExistenciaGrupo($dados)) {

            $camposDestaques[] = array(
                'campo' => 'itsedescricao'
            );

            $grupoExistenteErro = true;

        }

        $this->view->dados = $camposDestaques;

        if ($error) {            
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if ($grupoExistenteErro) {
            throw new Exception('Registro já cadastrado');
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
            if (!isset($parametros->itseoid) || trim($parametros->itseoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            
            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->itseoid = (int) $parametros->itseoid;
            
            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->itseoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'O registro foi excluído com sucesso.';
            }
            
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();


        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();
        }
        
        header('LOCATION: cad_grupo_controle_documento.php?acao=pesquisar');
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
                } elseif (isset($_SESSION['pesquisa_pendentes'][$key])) {
                    $temp[$key] = trim($_SESSION['pesquisa_pendentes'][$key]);
                }
                $_SESSION['pesquisa_pendentes'][$key] = $temp[$key];
            }
        }

        $_SESSION['pesquisa_pendentes']['usarSessao'] = TRUE;

        return (object) $_SESSION['pesquisa_pendentes'];
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

