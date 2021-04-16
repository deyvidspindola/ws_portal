<?php

/**
 * Classe padrão para Action
 *
 * @package Cadastro
 * @since   22/08/2013 
 * @category Action
 */
class CadCondicaoPagamento {

    /**
     * Objeto DAO da classe.
     * 
     * @var CadCondicaoPagamento
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
     * Mensagem de sucesso para exclusão do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";
    
    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Registro alterado com sucesso.";
  
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
     * Mensagem de existência de uma condiçãod e pagamento com os mesmos vencimentos.
     * @CONST STRING
     */
    const MENSAGEM_ALERTA_CONDICAO_JA_EXISTE = 'Já existe condição de pagamento cadastrada com os vencimentos informados.';
    
    /**
     * Mensgame de validação dos vencimentos
     * @const string
     */
    const MENSAGEM_VECIMENTO_INVALIDO = 'Vencimentos inválidos.';
    
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
            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {
		
            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_condicao_pagamento/index.php";
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
        return $retorno;
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     * 
     * @return void
     */
    private function inicializarParametros() {
        
        //Verifica se os parametro existem, senão iniciliza todos
		$this->view->parametros->cpgoid 		= isset($this->view->parametros->cpgoid) ? intval($this->view->parametros->cpgoid) : 0;
		$this->view->parametros->cpgvencimentos = isset($this->view->parametros->cpgvencimentos) ? trim($this->view->parametros->cpgvencimentos) : '';
		$this->view->parametros->cpgnumparcelas = isset($this->view->parametros->cpgnumparcelas) ? intval($this->view->parametros->cpgnumparcelas) : 0;
		$this->view->parametros->cpgdescricao 	= isset($this->view->parametros->cpgdescricao) ? strip_tags(trim($this->view->parametros->cpgdescricao)) : '';
		
    }
    
    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        //Inicializa os dados
        $this->inicializarParametros();
        
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
            if (isset($this->view->parametros->bt_salvar)) {
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
        if ($registroGravado) {
            $this->index();
        } else {
            
            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Cadastro/View/cad_condicao_pagamento/cadastrar.php";
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
            if (isset($parametros->cpgoid) && intval($parametros->cpgoid) > 0) {
            	
                //Realiza o CAST do parametro
                $parametros->cpgoid = (int) $parametros->cpgoid;
                
                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->cpgoid);
				
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
       
        $dados->vencimentos = str_replace(';', ',', $dados->cpgvencimentos);
             
        if (($dados->cpgoid > 0) && (!$this->dao->verificarExistenciaVencimentos($dados))) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);
            
            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else if (!$this->dao->verificarExistenciaVencimentos($dados)) {
        	
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        } else {
        	//Se já existe registro com os mesmos verncimentos lança exceção        	
        	throw new Exception(self::MENSAGEM_ALERTA_CONDICAO_JA_EXISTE);        	
        	
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
        if (!isset($dados->cpgdescricao) || trim($dados->cpgdescricao) == '') {
            $camposDestaques[] = array(
                'campo' => 'cpgdescricao'
            );
            $error = true;
        }
        
        if (!isset($dados->cpgvencimentos) || trim($dados->cpgvencimentos) == '') {
        	$camposDestaques[] = array(
        		'campo' => 'cpgvencimentos'
        	);
        	$error = true;
        }
		
        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
		
        if (!count($this->validarVencimentos($dados->cpgvencimentos))) {
        	$camposDestaques[] = array(
        		'campo' => 'cpgvencimentos'
        	);
            $this->view->dados = $camposDestaques;
			
			throw new Exception(self::MENSAGEM_VECIMENTO_INVALIDO);      
        }
    }   
    
    /**
     * Valida se os dados de vencimento são válidos.
     * 
     * @param string $cpgvencimentos   
     * @return array:
     */
    private function validarVencimentos($cpgvencimentos) {
    	
    	$vencimentos = array();
    	
    	if (!empty($cpgvencimentos)) {
    		
    		$vencimentos = explode(';', $cpgvencimentos);    		
    		$menorVencimento = 0;
    		
    		
    		foreach ($vencimentos as $vencimento) {
    			
				if (empty($vencimento)) {
					return array();
				}
								
    			$isNumero = preg_match('/^[0-9]*$/', $vencimento);
				if (!$isNumero) {
					return array();
				}
				
				if (intval($vencimento) > 999) {
					return array();
				}
				
				if ($vencimento <= $menorVencimento) {
					return array();
				}
				
				$menorVencimento = $vencimento;
							
    		}    		
    	}
    	
    	return $vencimentos;    	
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
    		if (!isset($parametros->cpgoid) || trim($parametros->cpgoid) == '') {
    			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    		}
    
    		//Inicia a transação
    		$this->dao->begin();
    
    		//Realiza o CAST do parametro
    		$parametros->cpgoid = (int) $parametros->cpgoid;
    
    		//Remove o registro
    		$confirmacao = $this->dao->excluir($parametros->cpgoid);
    
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

