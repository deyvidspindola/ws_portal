<?php

/**
 * Classe CadAcaoOS.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   FABIO ANDREI LORENTZ <fabio.lorentz@sascar.com.br>
 *
 */
class CadAcaoOS {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ALERTA_ESPACOS_BRANCOS     = "O campo não pode ser preenchido somente com espaços em branco.";
    const MENSAGEM_ALERTA_DUPLICIDADE         = "Essa ação já existe.";

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {


        $this->dao                   = (is_object($dao)) ? $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
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
                    $retorno->$key = isset($_POST[$key]) ? trim(preg_replace('/\s+/', ' ', $value)) : '';
                }

            }
        }

        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {

               //Verifica se atributo já existe e não sobrescreve.
               if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }

        return $retorno;
    }

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
		$this->view->parametros->mhcdescricao = isset($this->view->parametros->mhcdescricao) && !empty($this->view->parametros->mhcdescricao) ? trim($this->view->parametros->mhcdescricao) : ""; 

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros = null) {

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
     * @param stdClass $parametros
     * @return void
     */
    public function cadastrar($parametros = null) {

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try{

            // verifica descrição com apenas espaços em branco
            if(ctype_space($_POST["mhcdescricao"])) {
                throw new Exception(self::MENSAGEM_ALERTA_ESPACOS_BRANCOS);
            }

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

        try {
            $this->view->acoes = $this->pesquisar();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = ($this->view->mensagemAlerta) ? $this->view->mensagemAlerta : $e->getMessage();
        }

        require_once _MODULEDIR_ . "Cadastro/View/cad_acao_os/cadastrar.php";
    }

    /**
     * Grava os dados na base de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     * @return void
     */
    private function salvar(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastro($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        //remove acentos para consulta
        $dados->mhcdescricaoConsulta = $this->removerAcentos($dados->mhcdescricao);

        //verifica duplicidade
        if($this->dao->verificaDuplicidade($dados)) {
            throw new Exception(self::MENSAGEM_ALERTA_DUPLICIDADE);
        }      

        if ((int)$dados->mhcoid > 0) {

            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);
            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

        } else {

            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            //Seta a mensagem de inclusão
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
     * @throws Exception
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->mhcdescricao) || trim($dados->mhcdescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'mhcdescricao'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluir() {

        $retorno = "OK";

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->mhcoid) || trim($parametros->mhcoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->mhcoid = (int) $parametros->mhcoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->mhcoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
            } else {
                $retorno = "ERRO";
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

            $retorno = "ERRO";
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();

            $retorno = "ERRO";
        }

        echo $retorno;

        exit;
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa de vínculo de Ação vs Departamento.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    public function pesquisarVinculos() {

        $depoid = (int)$_GET["id"];

        $resultadoPesquisa = $this->dao->pesquisarVinculos($depoid);

        print json_encode($resultadoPesquisa);
    }

    /**
     * Responsável por exibir/receber o formulário de vínculo de Ação vs Departamento ou invocar
     * o metodo para salvar os dados
     * @param stdClass $parametros
     * @return void
     */
    public function vincular($parametros = null) {

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
                $registroGravado = $this->salvarVinculo($this->view->parametros);
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

        $this->view->departamentos = $this->dao->pesquisarDepartamentos();

        require_once _MODULEDIR_ . "Cadastro/View/cad_acao_os/vincular.php";
    }

    /**
     * Grava os dados na base de dados do vinculo de Ação vs Departamento.
     *
     * @param stdClass $dados Dados a serem gravados
     * @return void
     */
    private function salvarVinculo(stdClass $dados) {

        //Validar os campos
        $this->validarCamposVinculo($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        //Exclui os vínculos pré-existentes
        $gravacao = $this->dao->excluirVinculos($dados->depoid);
        
        //Insere os novos vínculos
        foreach ($dados->acoes_vinc as $key => $mhcoid) {
            $gravacao = $this->dao->inserirVinculo($mhcoid, $dados->depoid);
        }

        $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios do vinculo de Ação vs Departamento.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCamposVinculo(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->depoid) || (int)trim($dados->depoid) == 0) {
            $camposDestaques[] = array(
                'campo' => 'depoid'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Remove acentos da string
     * @param  string $str
     * @return string
     */
    public function removerAcentos($str){
         
        $busca     = array("à","á","ã","â","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","õ","ô","ö","ù","ú","û","ü","ç", "'", '"', "%");
        $substitui = array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","c", "\'" , '\"', "\\\%");
         
        $str       = str_replace($busca,$substitui,$str);
         
        $busca     = array("À","Á","Ã","Â","Ä","È","É","Ê","Ë","Ì","Í","Î","Ï","Ò","Ó","Õ","Ô","Ö","Ù","Ú","Û","Ü","Ç","‡","“", "<", ">" );
        $substitui = array("A","A","A","A","A","E","E","E","E","I","I","I","I","O","O","O","O","O","U","U","U","U","C", ""  ,"" , "" , "");
         
        $str       = str_replace($busca,$substitui,$str);
        return $str;
    }
}

