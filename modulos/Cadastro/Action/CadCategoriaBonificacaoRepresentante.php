<?php

/**
 * Classe CadCategoriaBonificacaoRepresentante.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   JORGE LUIS CUBAS <jorge.cubas@meta.com.br>
 *
 */

require_once (_MODULEDIR_ . 'Cadastro/DAO/CadCategoriaBonificacaoRepresentanteDAO.php');

class CadCategoriaBonificacaoRepresentante {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;
	
	private $resultadoPesquisa = "";

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ALERTA_DUPLICIDADE         = "Já existe uma categoria cadastrada com este nome.";

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct() {

    	global $conn;
        $this->dao                   = new CadCategoriaBonificacaoRepresentanteDAO($conn);
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado = (empty($this->usuarioLogado) ? 2750 : intval($this->usuarioLogado));
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        try {
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Cadastro/View/cad_categoria_bonificacao_representante/index.php";
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
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
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
		$this->view->parametros->bonrecatnome = isset($this->view->parametros->bonrecatnome) && !empty($this->view->parametros->bonrecatnome) ? trim($this->view->parametros->bonrecatnome) : ""; 
		$this->view->parametros->bonrecatoid = isset($this->view->parametros->bonrecatoid) && trim($this->view->parametros->bonrecatoid) != "" ? trim($this->view->parametros->bonrecatoid) : 0 ; 


    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
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
     * @param stdClass $parametros
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
          //  $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            //Rollback em caso de erro
           // $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_categoria_bonificacao_representante/cadastrar.php";
        }
    }
    
    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * @return void
     */
    public function editar($parametros = null) {
    	$registroGravado = FALSE;
    	try {
    		
    		if (is_null($parametros)) {
    			$this->view->parametros = $this->tratarParametros();
    		} else {
    			$this->view->parametros = $parametros;
    		}
    		
    		
    		//Verificar se foi submetido o formulário e grava o registro em banco de dados
    		if (isset($_POST) && !empty($_POST)) {
    			$registroGravado = $this->salvar($this->view->parametros);
    		}
    		
    		$resultadoPesquisa = $this->dao->pesquisarPorID($this->view->parametros->id);
    		
    		 
    		//Valida se houve resultado na pesquisa
    		if (count($resultadoPesquisa) == 0) {
    			throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
    		}
    		 
    		$this->view->status = TRUE;
    		 
    		$this->view->editar = $resultadoPesquisa;
    		
    	
    	} catch (ErrorException $e) {
    		$this->view->mensagemErro = $e->getMessage();
    		$this->index();
    	}
   		  
    	
    //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {

            require_once _MODULEDIR_ . "Cadastro/View/cad_categoria_bonificacao_representante/cadastrar.php";
        }
    }



    /**
     * Grava os dados na base de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     * @return void
     */
    private function salvar(stdClass $dados) {

    	//Campos para destacar na view em caso de erro
    	$camposDestaques = array();
    	
    	/**
    	 * Verifica os campos obrigatórios
    	*/
    	if (!isset($dados->bonrecatnome) || trim($dados->bonrecatnome) == '') {
    		$camposDestaques[] = array(
    				'campo' => 'bonrecatnome'
    		);
    	}
    	
    	if (!empty($camposDestaques)) {
    			$this->view->dados = $camposDestaques;
       			$this->view->mensagemAlerta = self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS;
    	}else {
    		
    		//Inicia a transação
    		$this->dao->begin();
    		
    		//Gravação
    		$gravacao = null;
    		
    		if ($dados->bonrecatoid > 0) {
    		
    			if(!$this->dao->pesquisarPorNome($dados)){
    				//Efetua a gravação do registro
    				$gravacao = $this->dao->atualizar($dados);
    				//Seta a mensagem de atualização
    				$this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
    			}else{
    				$this->view->mensagemErro = self::MENSAGEM_ALERTA_DUPLICIDADE;
    			}
    		
    		} else {
    		
    			//Efetua a inserção do registro
    		
    			if(!$this->dao->pesquisarPorNome($dados)){
    				$gravacao = $this->dao->inserir($dados);
    				$this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
    			}else{
    				$this->view->mensagemErro = self::MENSAGEM_ALERTA_DUPLICIDADE;
    			}
    		
    		}
    		
    		//Comita a transação
    		$this->dao->commit();
    		
    		return $gravacao;
    	}

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
        if (!isset($dados->bonrecatnome) || trim($dados->bonrecatnome) == '') {
            $camposDestaques[] = array(
                'campo' => 'bonrecatnome'
            );
        }

        if (!empty($camposDestaques)) {
            return $camposDestaques;
            
        }
        
        return ;
    }

    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluir() {

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->bonrecatoid) || trim($parametros->bonrecatoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->bonrecatoid = (int) $parametros->bonrecatoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->bonrecatoid);

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


}

