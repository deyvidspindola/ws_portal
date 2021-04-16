<?php

/**
 * Classe CadGruposDeTestes.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   MARCELLO BORRMANN <marcello.b.ext@sascar.com.br>
 *
 */
class CadGruposDeTestes {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;	

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Grupo criado com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Grupo alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Grupo excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {


        $this->dao                   			= (is_object($dao)) ? $this->dao = $dao : NULL;
        $this->view                  			= new stdClass();
        $this->view->mensagemErro    			= '';
        $this->view->mensagemAlerta  			= '';
        $this->view->mensagemSucesso 			= '';
        $this->view->dados           			= null;
        $this->view->parametros      			= null;
        $this->view->status          			= false;
		$this->view->camposDestaque 			= null;
        $this->usuarioLogado         			= isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado.
     * @param
     * @throws Exception
     *         ErrorException
     * @return void
     */
    public function index() {
		
        try {
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            // Popula combos do formulario
            $this->popularFiltrosPesquisa();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

            //Verificar se a ação salvar
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'salvar' ) {
            	$this->salvar($this->view->parametros);
            }

            //Verificar se a ação editar 
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'editar' ) {
                $this->view->parametros->eproid_busca 	= $this->view->parametrosEdicao[0]->eproid_busca;
                $this->view->parametros->eqcoid_busca 	= $this->view->parametrosEdicao[0]->eqcoid_busca;
                $this->view->parametros->eveoid_busca 	= $this->view->parametrosEdicao[0]->eveoid_busca;
                $this->view->parametros->egtnome		= '';
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }
        
        //Incluir a view padrão
        require_once _MODULEDIR_ . "Cadastro/View/cad_grupos_de_testes/index.php";
    }


	/**
	 * Popula combos do formulario.
     * @param
     * @throws 
	 * @return [type] [description]
	 */
	private function popularFiltrosPesquisa() {
		
		$this->view->equipamentoProjetoList = $this->dao->getEquipamentoProjetoList();
		$this->view->equipamentoClasseList 	= $this->dao->getEquipamentoClasseList();
	}
	
	/**
	 * Retorna versões de equipamento de acordo com o projeto.
     * @param
     * @throws 
	 * @return array
	 */
	public function buscarVersoes() {
			
		$eproid = $_POST['eproid_busca'];
		$equipamentoVersaoList	= $this->dao->getEquipamentoVersaoList($eproid);
	  
		$retorno		= array(
				'erro'		=> false,
				'codigo'	=> 0,
				'retorno'	=> 	$equipamentoVersaoList
		);
 
		echo  json_encode($retorno) ;
		
	}
	
	/**
	 * Valida os campos obrigatórios na pesquisa.
     * @param
     * @throws Exception
	 * @return
	 */
	public function validarCamposBusca(stdClass $dados) {
	
		//Campos para destacar na view em caso de erro
		$camposDestaques = array();
	
		//Verifica se houve erro
		$error = false;
	
		// Verifica os campos obrigatórios
		if (!isset($dados->eproid_busca) || trim($dados->eproid_busca) == ''){
			$camposDestaques[] = array(
				'campo' => 'eproid_busca'
			);
			$error = true;
		}
	
		if ($error){
			$this->view->camposDestaque = $camposDestaques;
			throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
		}
	
	}

    /**
     * Método para validar se em um mesmo grupo 
	 * existem testes dependentes entre si.
     * @param stdClass $dados Dados a serem validados
     * @throws 
     * @return string
     */
    private function validarTestesDependentes(stdClass $dados) {
	
		$msgDependencia = '';
        
        $resultadoDependencia = $this->dao->validarTestesDependentes($dados);
        
        //Valida se houve resultado na pesquisa
        if (count($resultadoDependencia) > 0) {
        	
        	foreach ($resultadoDependencia as $resultado){
        		$msgDependencia.= " O teste '" . $resultado->descricao_teste . "' depende do teste '" . $resultado->descricao_dependente . "', não é possível agrupá-los no mesmo grupo. <br/>";
        	}
        }
        
        return $msgDependencia;
    }
	
    /**
     * Método para validar se o nome do grupo 
	 * já é utilizado para Projeto X Classe X 
	 * Versão.
     * @param stdClass $dados Dados a serem validados
     * @throws 
     * @return string 
     */
    private function validarNomeDuplicado(stdClass $dados) {
	
		$msgNomeDuplicado = '';
        
        $resultadoNomeDuplicado = $this->dao->validarNomeDuplicado($dados);
        
        //Valida se houve resultado na pesquisa
        if ($resultadoNomeDuplicado > 0) {
        	
        	$msgNomeDuplicado = "A relação Projeto X Classe X Versão já possui um grupo com o nome '<b>". trim( $dados->egtnome ) ."</b>'.";
        	
        }
        return $msgNomeDuplicado;
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros.
     * @param
     * @throws 
     * @return stdClass Parametros tradados
     */
	private function tratarParametros() {
	
		$retorno = new stdClass();
	
		if (count($_GET) > 0) {
			foreach ($_GET as $key => $value) {
	
				//Verifica se atributo ja existe e nao sobrescreve.
				if (!isset($retorno->$key)) {
	
					if(is_array($value)) {
	
						// Tratamento de GET com Arrays
						foreach ($value as $chave => $valor) {
							$value[$chave] = trim($valor);
						}
						$retorno->$key = isset($_GET[$key]) ? $_GET[$key] : array();
						 
					} else {
						$retorno->$key = isset($_GET[$key]) ? trim($value) : '';
					}
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
     * @param
     * @throws 
     * @return void
     */
    private function inicializarParametros() {
	
		//Verifica se os parametros existem, senão iniciliza todos
        foreach ($this->view->parametros as $key => $value) {
            
            if(is_array($value)) {
            
            	$this->view->parametros->$key = $value;
            
            } else {
            	
            	$this->view->parametros->$key = trim($value);
            	
            }

        }
        
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @throws Exception
     * @return array
     */
    private function pesquisar(stdClass $filtros) {
    	try{
	    	// Valida obrigatoriedade
	    	$this->validarCamposBusca($filtros);
	    	
	        $resultadoPesquisa = $this->dao->pesquisar($filtros);
	
	        //Valida se houve resultado na pesquisa
	        if (count($resultadoPesquisa) == 0) {
	            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
	        }
	
	        $this->view->status = TRUE;

        	return $resultadoPesquisa;
    		
    	} catch (Exception $e) {
    		
    		$this->view->mensagemAlerta = $e->getMessage();
    		
    	}
    	
    	//Incluir a view padrão
    	require_once _MODULEDIR_ . "Cadastro/View/cad_grupos_de_testes/index.php";
    }

    /**
     * Responsável por invocar o metodo para exibir o formulário de edição
     * @param 
     * @throws Exception
     * @return void
     */
    public function editarGrupo() {
    	
        $this->editar();
    }

    /**
     * Responsável por receber exibir o formulário de edição.
     * @param 
     * @throws Exception
     * @return void
     */
    public function editar() {
    	
        try {
            //Parametros
            $parametros = $this->tratarParametros();
            
            //Verifica se foi informado o id do cadastro
            if (isset($parametros->epcvoid) && intval($parametros->epcvoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->epcvoid = (int) $parametros->epcvoid;
				
                //Pesquisa comando e teste
                $dados = $this->dao->pesquisarPorID($parametros);
				
                if (!is_null($dados) || count($dados)>0) {
                	$this->view->parametrosEdicao = $dados;
                }

                //Pesquisa grupo de comandos e testes
                $dadosGrupo = $this->dao->pesquisarGrupo($parametros->epcvoid);
                
                if (!is_null($dadosGrupo) || count($dadosGrupo)>0) {
                	$this->view->parametrosGrupo = $dadosGrupo;
                }
                
                //Sem resultados
                if (count($dados) == 0 && count($dadosGrupo) == 0){
                	throw new Exception('Não foram encontrados Testes/ Comandos para combinação Projeto, Classe e Versão informada.');
                }
                
            } else {
                throw new Exception('Idenficador não encontrado.');
            }

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        $_GET['epcvoid']= $parametros->epcvoid;
        $_GET['egtoid'] = $parametros->egtoid;
        $_POST['acao'] 	= 'editar';
        $this->index();
    }

    /**
     * Grava os dados na base de dados.
     * @param stdClass $dados Dados a serem gravados
     * @throws Exception
     *         ErrorException
     * @return void
     */
    private function salvar(stdClass $dados) {

        try {
	    	// Atribui usuário logado
	    	$dados->egtusuoid_cadastro = $this->usuarioLogado;
	        
	        // Inicia a transação
	        $this->dao->begin();
	        
	        //$dados->arrayEptpoid = '68,50,51';
	        	        
	        // Validar testes dependentes
	        $dependencia = $this->validarTestesDependentes($dados);
	        if ($dependencia!='') {
	        	throw new Exception($dependencia);
	        }
	        
	        // Validar nome duplicado
	        $nomeDuplicado = $this->validarNomeDuplicado($dados);
	        if ($nomeDuplicado!='') {
	        	/* 
	        	$camposDestaques[] = array(
	        			'campo' => 'egtnome'
	        	);
	        	$this->view->camposDestaque = $camposDestaques;
	        	 */
	        	throw new Exception($nomeDuplicado);
	        }
			
	        // Edição
	        if ($dados->egtoid > 0) {
	        	
	            // Efetua a atualização do registro
	            if (! $this->dao->atualizarGrupo($dados)) {
	            	throw new ErrorException("Houve um erro ao atualizar Grupo.");
	            }
	            
	            // Desvincula Projeto X Classe X Versao, Teste Planejado e Grupo
	            if (! $this->dao->excluirProjetoClasseVersaoTesteGrupo($dados)) {
	            	throw new ErrorException("Houve um erro ao excluir Projeto Classe Versao Teste Grupo.");
	            }
	            
	            // Vincula Projeto X Classe X Versao, Teste Planejado e Grupo
	            foreach ($dados->check as $value){
	            	$dados->eptpoid = $value;
		            if (!$this->dao->inserirProjetoClasseVersaoTesteGrupo($dados)){
		            	throw new ErrorException("Houve um erro ao inserir Projeto Classe Versao Teste Grupo.");
		            }
	            } 
	
	            // Seta a mensagem de atualização
	            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
	        } 
	        // Cadastro
	        else {
	            // Efetua a inserção do registro
	            if (! $idGrupo = $this->dao->inserirGrupo($dados)) {
	            	throw new ErrorException("Houve um erro ao inserir Grupo.");
	            }

	            $dados->egtoid = $idGrupo;            
	            
	            foreach ($dados->check as $value){
	            	$dados->eptpoid = $value;
		            // Vincula Projeto X Classe X Versao, Teste Planejado e Grupo
		            if (!$this->dao->inserirProjetoClasseVersaoTesteGrupo($dados)){
		            	throw new ErrorException("Houve um erro ao inserir Projeto Classe Versao Teste Grupo.");
		            }
	            } 
	            
	            // Seta a mensagem de inserção
	            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
	        }	        
	        // Comita a transação
	        $this->dao->commit();
            //$this->dao->rollback();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
            // Reverte a transação
        	$this->dao->rollback();
        	
        } catch (Exception $e) {
			
        	$this->view->mensagemAlerta = $e->getMessage();
            // Reverte a transação
            $this->dao->rollback();
        }
        
        $_GET['epcvoid']= $dados->epcvoid;
        $_POST['egtoid']= null;
        $_POST['acao'] 	= 'editar';
        $this->editar();
    }
    
    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluirGrupo() {
        try {
            // Retorna os parametros
            $parametros = $this->tratarParametros();

            // Verifica se foi informado o id
            if (!isset($parametros->egtoid) || trim($parametros->egtoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            // Inicia a transação
            $this->dao->begin();

            // Realiza o CAST do parametro
            $parametros->egtoid = (int) $parametros->egtoid;

            // Atribui usuário logado
            $parametros->egtusuoid_exclusao = $this->usuarioLogado;

            // Remove o registro
            if (! $confirmacao = $this->dao->excluirGrupo($parametros)) {
            	throw new ErrorException("Houve um erro ao excluir Grupo.");
            }
	            
            // Desvincula Projeto X Classe X Versao, Teste Planejado e Grupo
            if (! $this->dao->excluirProjetoClasseVersaoTesteGrupo($parametros)) {
            	throw new ErrorException("Houve um erro ao excluir Projeto Classe Versao Teste Grupo.");
            }

            // Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
            }

        } catch (ErrorException $e) {

            // Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            // Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $_POST['egtoid']= null;
        $_POST['acao'] 	= 'editar';
        $this->editar();
    }


}

