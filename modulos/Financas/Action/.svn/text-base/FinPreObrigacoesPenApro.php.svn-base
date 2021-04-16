<?php

/**
 * APROVAÇÃO DE TAG SASWEB – PRÉ OBRIGAÇÃO FINANCEIRA
 *
 * @file FinPreObrigacoesPenApro.php
 * @author Ernando de Castro <ernando.castro.ext@sascar.com.br>
 * @version 1.0
 * @since 06/08/2015 08:46
 */

class FinPreObrigacoesPenApro {

    /**
     * Objeto DAO da classe.
     *
     * @var FinPreObrigacoesPenAproDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Informe os campos obrigatórios em destaque.";
    
    const MENSAGEM_ALERTA_CAMPOS_DATA_OBRIGATORIOS = "Data inicial é maior que a data final.";

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
     *
     * @return void
     */
    public function index() {
    	
    	
        try {
        	// Tratamento dos parametros
            $this->view->parametros = $this->tratarParametros();

            if(!empty($this->view->parametros->acao) && $this->view->parametros->acao =="prepararPrevisao"){
	             // Vaidação dos campos
	            $this->validarCamposGeracao($this->view->parametros);
	            //validar data se e maior inicial e maior q data final.
	            $this->validaData($this->view->parametros);
	            //Inicializa os dados
	            $this->inicializarParametros();	            
	            // busca a lista de status para combo
	            $dadosPesquisa = (object) $this->buscarDadosPesquisa($this->view->parametros);
	                       
	            if (!empty($dadosPesquisa)) {
	            	 // busca a lista de dados
		            $this->view->status = true;
		            $this->view->dados = $dadosPesquisa;
	            }
	            
            }
            
        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        }        
       
        // busca a lista de status para combo
         $dadosStatus = (object) $this->verificarStatus();
        
        // busca a lista de tipo para o combo
         $dadosTipo = (object) $this->verificarTipo();
        
        //Incluir a view padrão
        require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/index.php";
    }

    /**
     * Método de consulta.
     */   
    public function visualizar(){

    	// Tratamento dos parametros
    	$this->view->parametros = $this->tratarParametros();
    	       	// status P = Pacote e F = Funcionalidade
    	if ($this->view->parametros->status == "P"){
    		$this->view->status = true;
    		$var = "Pacote";
    		$visualizarDetalhe = $this->visualizarDetalhePacote($this->view->parametros->rastoid);
    		$this->view->dados = $this->visualizarDetalheFuncionalidadesPacote($this->view->parametros->rastoid);
 
    	}elseif ($this->view->parametros->status == "F"){
    		$this->view->status = false;    		
    		$var = "Funcionalidade";
    		$visualizarDetalhe = $this->visualizarDetalheFuncionalidade($this->view->parametros->rastoid);
    	}
    	
        //Incluir a view consulta
        require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/visualizar.php";
    
    }
    
    public function historico() {
        $this->view->parametros = $this->tratarParametros();

        $dadosHistorico = $this->dao->consultaHistorico($this->view->parametros->rastoid);

        require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/historico.php";
    }

    /**
     * metodo editar
     */
    public function editar(){
    
    	try {
    	
	    	// Tratamento dos parametros
	    	$this->view->parametros = $this->tratarParametros();
	    	
            if(!empty($this->view->parametros->acaoEditar) && $this->view->parametros->acaoEditar =="alterarStatus"){
                $this->dao->alterarStatus($this->view->parametros->rastoid, $this->view->parametros->cbb_status);

                $this->dao->insereHistorico(null, $this->view->parametros, 'Aprovação de Atualização de Status');

                $dadosStatus = (object) $this->verificarStatus();

                $this->view->mensagemSucesso = 'Status alterado com sucesso.';

                require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/index.php";

                return;
            }
            else if(!empty($this->view->parametros->acaoEditar) && $this->view->parametros->acaoEditar =="editarPrevisao"){
	    		// Vaidação dos campos
	    		$this->validarCamposEditar($this->view->parametros);
	    		
	    		//Inicializa os dados
	    		$this->inicializarParametrosEditar();
	    		if ($this->view->parametros->status == "F"){
	    		   $this->inserirCriacaoObrigacao($this->view->parametros);
	    		}  
	    		
	    		if ($this->view->parametros->status == "P"){
	    		$valor = $this->inserirCriacaoObrigacaoPacote($this->view->parametros);
	    		} 
	    		$this->index();
	    		exit();
	    	}
    	} catch (Exception $e) {    	
    		$this->view->mensagemAlerta = $e->getMessage();
    	
    	} catch (ErrorException $e) {    	
    		$this->view->mensagemErro = $e->getMessage();
    	
    	}catch (Exception $e) {
            $this->view->mensagemSucesso = $e->getMessage();
          
        }
        
    	// status P = Pacote e F = Funcionalidade
    	if ($this->view->parametros->status == "P"){
    		$var = "Pacote";
    	    $recuperarDados = $this->recuperarDadosPacote($this->view->parametros->rastoid);
    	}elseif ($this->view->parametros->status == "F"){
    		$var = "Funcionalidade";
    		$recuperarDados = $this->recuperarDadosFuncionalidade($this->view->parametros->rastoid);
    	}    	

    	// busca a lista de tipo para o combo
    	$dadosTipo = (object) $this->verificarTipo();
    	// Código do Serviço
    	$codigoServico = (object) $this->codigoServico();
    	// Grupo de Faturamento
    	$grupoFaturamento = (object) $this->grupoFaturamento();
    	//Combo com as opções Pro-Rata
    	$proRata = (object) $this->proRata();
    	 
    	
    	// Incluir a view editar
    	require_once _MODULEDIR_ . "Financas/View/fin_pre_obrigacoes_pen_apro/editar.php";
    
    }

    /**
     * 
     *
     * @return void
     */
    private function inicializarParametros() {
    
        //Verifica se os parametros existem, senão inicializa todos
    	$this->view->parametros->obrigacaoFinPenApro = isset($this->view->parametros->obrigacaoFinPenApro) && !empty($this->view->parametros->obrigacaoFinPenApro) ? trim($this->view->parametros->obrigacaoFinPenApro) : "";
    	$this->view->parametros->status = isset($this->view->parametros->status) && !empty($this->view->parametros->status) ? trim($this->view->parametros->status) : "";
        $this->view->parametros->dtInicio = isset($this->view->parametros->dtInicio) && !empty($this->view->parametros->dtInicio) ? trim($this->view->parametros->dtInicio) : "";
	    $this->view->parametros->dtFim = isset($this->view->parametros->dtFim) && !empty($this->view->parametros->dtFim) ? trim($this->view->parametros->dtFim) : "";
        $this->view->parametros->tag = isset($this->view->parametros->tag) && !empty($this->view->parametros->tag) ? trim($this->view->parametros->tag) : "";
        $this->view->parametros->descricao = isset($this->view->parametros->descricao) && !empty($this->view->parametros->descricao) ? trim($this->view->parametros->descricao) : "";
        $this->view->parametros->tipo = isset($this->view->parametros->tipo) && !empty($this->view->parametros->tipo) ? trim($this->view->parametros->tipo) : "";
        $this->view->parametros->obrigacao_unica = isset($this->view->parametros->obrigacao_unica) && !empty($this->view->parametros->obrigacao_unica) ? trim($this->view->parametros->obrigacao_unica) : "";

    }
    
    /**
     *
     *
     * @return void
     */
    private function inicializarParametrosEditar() {
        	
    	//Verifica se os parametros existem, senão inicializa todos
    	$this->view->parametros->classificacao = isset($this->view->parametros->classificacao) && !empty($this->view->parametros->classificacao) ? trim($this->view->parametros->classificacao) : "";
    	$this->view->parametros->tipo = isset($this->view->parametros->tipo) && !empty($this->view->parametros->tipo) ? trim($this->view->parametros->tipo) : "";
    	$this->view->parametros->obrigFinanceira = isset($this->view->parametros->obrigFinanceira) && !empty($this->view->parametros->obrigFinanceira) ? trim($this->view->parametros->obrigFinanceira) : "";
    	$this->view->parametros->codServico = isset($this->view->parametros->codServico) && !empty($this->view->parametros->codServico) ? trim($this->view->parametros->codServico) : "";
    	$this->view->parametros->grupoFaturamento = isset($this->view->parametros->grupoFaturamento) && !empty($this->view->parametros->grupoFaturamento) ? trim($this->view->parametros->grupoFaturamento) : "";
    	$this->view->parametros->proRata = isset($this->view->parametros->proRata) && !empty($this->view->parametros->proRata) ? trim($this->view->parametros->proRata) : "";
    	$this->view->parametros->valor = isset($this->view->parametros->valor) && !empty($this->view->parametros->valor) ? trim($this->view->parametros->valor) : "";
        $this->view->parametros->obrigacao_unica = isset($this->view->parametros->obrigacao_unica) && !empty($this->view->parametros->obrigacao_unica) ? trim($this->view->parametros->obrigacao_unica) : "";
        $this->view->parametros->cbb_status = isset($this->view->parametros->cbb_status) && !empty($this->view->parametros->cbb_status) ? trim($this->view->parametros->cbb_status) : "";
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
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
     * Valida os campos obrigatórios.
     *
     * @return
     */
    public function validarCamposGeracao(stdClass $dados) {
    	
        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;
        if (trim($dados->status) == '' && trim($dados->tag) == '' && trim($dados->descricao) == '' && trim($dados->tipo) == '') {
		        // Verifica os campos obrigatórios
		        if (!isset($dados->dtInicio) || trim($dados->dtInicio) == '') {
		        	$camposDestaques[] = array(
		        			'campo' => 'dtInicio'
		        	);
		        	$error = true;
		        }
		        
		        // Verifica os campos obrigatórios
		        if (!isset($dados->dtFim) || trim($dados->dtFim) == '') {
		        	$camposDestaques[] = array(
		        			'campo' => 'dtFim'
		        	);
		        	$error = true;
		        }   

        }
        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

    } 
    
    /**
     * Data inicial é maior que a data final
     *
     * @return
     */
    public function validaData(stdClass $dados) {
    	 
    	//Campos para destacar na view em caso de erro
    	$camposDestaques = array();
    
    	//Verifica se houve erro
    	$error = false;
    
    		$data_i = explode('/', $dados->dtInicio);
    		$data_f = explode('/', $dados->dtFim);
    
    		$data_a = $data_i['2'].$data_i['1'].$data_i['0'];
    		$data_b = $data_f['2'].$data_f['1'].$data_f['0'];
    
    		if($data_a > $data_b){
    			 
    			$camposDestaques[] = array(
    					'campo' => 'dtInicio'
    			);
    			$camposDestaques[] = array(
    					'campo' => 'dtFim'
    			);
    			$error = true;
    			 
    		}
    
    	if ($error) {
    		$this->view->dados = $camposDestaques;
    		throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_DATA_OBRIGATORIOS);
    	}
    
    }
    
    
    /**
     * Valida os campos obrigatórios.
     *
     * @return
     */
    public function validarCamposEditar(stdClass $dados) {
    
    	//Campos para destacar na view em caso de erro
    	$camposDestaques = array();
   
    	//Verifica se houve erro
    	$error = false;
    	// Verifica os campos obrigatórios
    	if (!isset($dados->classificacao) || trim($dados->classificacao) == '') {
    		
    		$camposDestaques[] = array(
    				'campo' => 'classificacao'
    		);
    		$error = true;
    		
    	}
    	// Verifica os campos obrigatórios
    	if (!isset($dados->tipo) || trim($dados->tipo) == '' ) {
    		$camposDestaques[] = array(
    				'campo' => 'tipo'
    		);
    		$error = true;
    	
    	}
    	// Verifica os campos obrigatórios
    	if (!isset($dados->obrigFinanceira) || trim($dados->obrigFinanceira) == '') {
    		
    		$camposDestaques[] = array(
    				'campo' => 'obrigFinanceira'
    		);
    		$error = true;
    	
    	}
    	// Verifica os campos obrigatórios
    	if (!isset($dados->codServico) || trim($dados->codServico) == '') {
    		
    		$camposDestaques[] = array(
    				'campo' => 'codServico'
    		);
    		$error = true;
    	
    	}
    	// Verifica os campos obrigatórios
    	if (!isset($dados->grupoFaturamento) || trim($dados->grupoFaturamento) == '') {
    		
    		$camposDestaques[] = array(
    				'campo' => 'grupoFaturamento'
    		);
    		$error = true;
    	
    	}
    	/* Verifica os campos obrigatórios
    	if (!isset($dados->proRata) || trim($dados->proRata) == '') {
    		
    		$camposDestaques[] = array(
    				'campo' => 'proRata'
    		);
    		$error = true;
    	
    	}
    	*/
    	// Verifica os campos obrigatórios
    	if (!isset($dados->valor) || trim($dados->valor) == '' ) {
    		$camposDestaques[] = array(
    				'campo' => 'valor'
    		);
    		$error = true;
    		 
    	}
    	
    	if ($error) {
    		$this->view->dados = $camposDestaques;
    		throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
    	}
    
    }
 
	
    /**
     * Método que verifica concorrência entre processos.
     * @return array
     */
    public function verificarTipo() {
    
    	try {
    		
    		return $this->dao->recuperarParametrosTipo();   	
    	
    	} catch (ErrorException $e) {
    		
    	}
    }
    /**
     * metodo responsavel para retorna todos status.
     */
    
    public function verificarStatus(){
    	
    	try {
    		
    		return  $this->dao->recuperarParametrosStatus();    		
    		
    	} catch (Exception $e) {
    		
    	}
    }
    
    /**
     * Busca a lista de acordo com filtro dos parametros
     * @param unknown $parametros
     */
    public function buscarDadosPesquisa($parametros){
    	
    	try {
    		
    		return  $this->dao->buscarDadosPesquisa($parametros);
    		
    	} catch (Exception $e) {
    		
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    }
    
    /**
     * Combo com as opções Código do Serviço
     */
    public function codigoServico() {
        try {
    		
    		return  $this->dao->codigoServico();    		
    		
    	} catch (Exception $e) {
    		
    	}
    }
    
    /**
     * Combo com as opções Grupo de Faturamento
     */
   public function grupoFaturamento() {
        try {
    		
    		return  $this->dao->grupoFaturamento();    		
    		
    	} catch (Exception $e) {
    		
    	}
    }
    /**
     * Combo com as opções Pro-Rata
     */
    public function proRata() {
    	try {
    	
    		return  $this->dao->proRata();
    	
    	} catch (Exception $e) {
    	
    	}
    }
    /**
     * INSERT para criação da Obrigação Financeira de FUNCIONALIDADE
     * @param unknown $parametros
     */
    public function inserirCriacaoObrigacao($parametros) {
    	
    try {

    		$rest = $this->dao->validarTag($parametros);
    		
    		if($rest){
	    		$this->dao->begin();
	    		
	    		$obroid = $this->dao->inserirCriacaoObrigacao($parametros);
	    				
	    		$this->dao->inserirFuncionalidadePacote($obroid[0]['obroid']);
	    		
	    		/** SPED (STI 83616) - TODA OBRIGACAO FINANCEIRA IRÁ SE TORNAR UM PRODUTO - TIPO SERVICO */
	    		$produtoNovo = $this->dao->inserirProdutoParaObrigacao($obroid[0]['obroid'], $parametros->obrigFinanceira, $_SESSION['usuario']['oid']);
	    		 
	    		/** SPED (STI 83616) - VINCULA OBRIGAÇÃO COM PRODUTO */
	    		$this->dao->vincularProdutoParaObrigacao($produtoNovo, $obroid[0]['obroid']);
	    		
	    		$this->dao->processarPrevisao($parametros->rastoid,$obroid[0]['obroid'],$_SESSION['usuario']['oid']);

                $this->dao->insereHistorico($obroid[0]['obroid'], $parametros, 'Aprovação de Pré-Obrigação');
	    		
	    		$this->view->mensagemSucesso = "Obrigação Financeira cadastrada com sucesso.";
	    		
	    		$this->dao->commit();
    		}
    		
    	} catch (Exception $e) {
    		
    		$this->dao->rollback();
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    	
    	
    }
    
    /**
     * INSERT para criação da Obrigação Financeira de FUNCIONALIDADE
     * @param unknown $parametros
     */
    public function inserirCriacaoObrigacaoPacote($parametros) {
    	 
    	try {
    
    		$rest = $this->dao->validarTag($parametros);
    
    		if($rest){
    			$this->dao->begin();
    	   
    			$obroid = $this->dao->inserirObrigacaoFinanceiraPacote($parametros);
    			
    			/** SPED (STI 83616) - TODA OBRIGACAO FINANCEIRA IRÁ SE TORNAR UM PRODUTO - TIPO SERVICO */
	    		$produtoNovo = $this->dao->inserirProdutoParaObrigacao($obroid[0]['obroid'], $parametros->obrigFinanceira, $_SESSION['usuario']['oid']);
	    		
	    		/** SPED (STI 83616) - VINCULA OBRIGAÇÃO COM PRODUTO */
	    		$this->dao->vincularProdutoParaObrigacao($produtoNovo, $obroid[0]['obroid']);    			
    			
    			$this->dao->inserirFuncionalidadesPacote($obroid[0]['obroid']);
    	   
    			$this->dao->processarAlteraStatusTagAprovada($parametros->rastoid,$obroid[0]['obroid'],$_SESSION['usuario']['oid']);

                $this->dao->insereHistorico($obroid[0]['obroid'], $parametros, 'Aprovação de Pré-Obrigação');
    	   
    			$this->view->mensagemSucesso = "Obrigação Financeira cadastrada com sucesso.";
    	   
    			$this->dao->commit();
    		}
    
    	} catch (Exception $e) {
    
    		$this->dao->rollback();
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    	 
    	 
    }
    
    
    /**
     * Visualizar  detalhe da Funcionalidade
     * @param unknown $parametros
     */
    public function visualizarDetalheFuncionalidade($rastoid) {
       try {
    	
    		return  $this->dao->consultarDetalheFuncionalidade($rastoid);
    	
    	} catch (Exception $e) {
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    }
    
    /**
     * Visualizar  detalhe do Pacote
     * @param unknown $rastoid
     */
    public function visualizarDetalhePacote($rastoid) {
       try {
    	
    		return  $this->dao->consultarDetalhePacote($rastoid);
    	
    	} catch (Exception $e) {
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    }
    
  /**
   * Funcionalidades do Pacote
   * @param unknown $rastoid
   */
    
    public function visualizarDetalheFuncionalidadesPacote($rastoid) {
    	try {
    		 
    		return  $this->dao->consultarDetalheFuncionalidadesPacote($rastoid);
    		 
    	} catch (Exception $e) {
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    }
    /**
     * 
     * @param unknown $rastoid
     */
	public function recuperarDadosFuncionalidade($rastoid) {
    	try {
    		 
    		return  $this->dao->recuperarDadosFuncionalidade($rastoid);
    		 
    	} catch (Exception $e) {
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    }
    /**
     * SQL para apresentar dados do Pacote
     * @param unknown $rastoid
     */
    public function recuperarDadosPacote($rastoid) {
    	try {
    		 
    		return  $this->dao->recuperarDadosPacote($rastoid);
    		 
    	} catch (Exception $e) {
    		$this->view->mensagemAlerta = $e->getMessage();
    	}
    }
}