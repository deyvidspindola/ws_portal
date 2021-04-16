<?php
require _MODULEDIR_."/Manutencao/DAO/ManCorrecaoBaixaEstoqueDAO.php";
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * Ferramenta para correção de baixas de estoque incorretas
 * 
 * @author Marcello Borrmann
 * @since 28/08/2015
 */
class ManCorrecaoBaixaEstoque {

	/** Objeto DAO da classe */
	private $dao;
	
	/** propriedade para dados a serem utilizados na View. */
	private $view;
	
	/** propriedade para dados a serem utilizados na geração de CSV. */
	private $parametrosGeracaoCSV;
	
	/** Usuario logado */
	private $usuarioLogado;
	
	const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS 		= "Existem campos obrigatórios não preenchidos.";
	const MENSAGEM_SUCESSO_INCLUIR            		= "Registro(s) incluído(s) com sucesso.";
	const MENSAGEM_SUCESSO_ATUALIZAR          		= "Registro(s) alterado(s) com sucesso.";
	const MENSAGEM_SUCESSO_EXCLUIR            		= "Registro(s) excluído(s) com sucesso.";
	const MENSAGEM_NENHUM_REGISTRO            		= "Nenhum registro encontrado.";
	const MENSAGEM_ERRO_PROCESSAMENTO         		= "Houve um erro no processamento dos dados.";	
	
	/**
	 * Construtor
	 */
	public function __construct($dao = null) {
		
		$this->dao 						= (is_object($dao)) ? $this->dao = $dao : NULL;
		$this->view 					= new stdClass();
		$this->parametrosGeracaoCSV 	= new stdClass();
		$this->view->mensagemErro 		= '';
		$this->view->mensagemAlerta 	= '';
		$this->view->mensagemSucesso 	= '';
		$this->view->dados           	= null;
		$this->view->camposDestaque 	= null;
		$this->view->parametros 		= null;
		$this->view->status 			= false;
		$this->view->totalItens			= 0;
		$this->usuarioLogado 			= isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : 2750;
	}
	
	/**
	 * Página index
	 */
	public function index() {
		
		try {
			$this->view->parametros = $this->tratarParametros();
		
			// Inicializa os dados
			$this->inicializarParametros();

            // Popula combos do formulario
            $this->popularFiltrosFormulario();
			
			// Verifica se a ação pesquisar e executa pesquisa
			if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
				$this->view->dados = $this->pesquisar($this->view->parametros);
				
			}
			
		
		} catch (ErrorException $e) {
		
			$this->view->mensagemErro = $e->getMessage();
		
		} catch (Exception $e) {
		
			$this->view->mensagemAlerta = $e->getMessage();
		
		}
		
		// Verfica execução de relatório em andamento
		$dadosProcesso = (object) $this->verificarProcesso(false);
		if ($dadosProcesso->codigo == 2) {
			$this->view->processoExecutando = true;
			$this->view->mensagemRelatorio 	= $dadosProcesso->msg;
		} else {
			$this->view->processoExecutando = false;
		} 
		
		//Incluir a view padrão
		require_once _MODULEDIR_ . "/Manutencao/View/man_correcao_baixa_estoque/index.php";
	}


	/**
	 * Popula combos do formulario
	 * @return [type] [description]
	 */
	private function popularFiltrosFormulario() {

		$filtro = $this->montarFiltroRepresentante();
		
		$this->view->representanteResponsavelList	= $this->dao->getRepresentanteResponsavelList($filtro);
		$this->view->regiaoList						= $this->dao->getRegiaoList();
		$this->view->itemList						= array('A'=>'ACESSÓRIOS','E'=>'EQUIPAMENTO');
		$this->view->tipoList						= $this->dao->getTipoList();
		$this->view->classeContratoList				= $this->dao->getClasseContratoList();
		$this->view->modalidadeContratoList			= array('L'=>'LOCAÇÂO','R'=>'REVENDA');
		
		$this->view->parametros->dataInicial		= isset($this->view->parametros->dataInicial) ? $this->view->parametros->dataInicial : $this->CalcularDtIni();
		$this->view->parametros->dataFinal 			= isset($this->view->parametros->dataFinal) ? $this->view->parametros->dataFinal : date('d/m/Y');
	}
	
	
	/**
	 * Monta o filtro para pesquisa de representantes
	 * de acordo com características do usuário logado
	 * @return [type] [description]
	 */
	private function montarFiltroRepresentante() {
	
		$retorno = new stdClass();
		
		if (
			($_SESSION['usuario']['tipo'] =="REVENDA" || $_SESSION['usuario']['depapelido'] == "REPR_COMERCIAL" || 
			$_SESSION['usuario']['depoid']==9) && 
			$_SESSION['usuario']['refoid']>2){
			$refoid = $_SESSION['usuario']['refoid'];
		} 
		else {
			$refoid = 0;
		}
		
		if ($refoid>0){
		
			if ($refoid==234){
				$retorno = " 
					AND repoid IN(234, 289) ";
			}
			else {
				$retorno = " 
					AND repoid IN ( SELECT relrrepoid
									FROM relacionamento_representante
									WHERE relrrep_terceirooid = $refoid) ";
			}
		
		}
		else {
			$retorno = " 
					AND repoid NOT IN ( SELECT relrrep_terceirooid
										FROM relacionamento_representante
                                    	WHERE relrrepoid <> relrrep_terceirooid)
                	AND (reprevenda='t' OR repinstalacao='t' OR repassistencia='t') ";
		}
		
		return $retorno;
	}
	
	/**
	 * Retorna os instaladores de acordo com o representante
	 */
	public function buscarInstaladores() {
	
		ob_start();
		try {
			
			$repoid = $_POST['repoid_busca'];
			$instaladorList	= $this->dao->getInstaladorList($repoid);
		  
			$retorno		= array(
					'erro'		=> false,
					'codigo'	=> 0,
					'retorno'	=> 	$instaladorList
			);
	 
			echo  json_encode($retorno) ;
			ob_flush();
			exit;
		}
		catch (Exception $e) {
	
			ob_end_clean();
			$retorno		= array(
					'erro'		=> true,
					'codigo'	=> $e->getCode(),
					'retorno'	=> 	$e->getMessage()
			);
			echo json_encode($retorno);
			exit;
		}
	}


	/**
	 * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
	 *
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
	 * Calcula a data de início do período
	 * 
	 * @param 
	 * @return date
	 */
	private function CalcularDtIni(){
        // Data referência = data atual
        $dt_ref = date('Y-m-d');
        $timestamp_ref = strtotime($dt_ref);
        
        // A data de início será no mesmo dia do mês anterior 
        $timestamp_ini 	= strtotime('-1 months', $timestamp_ref);
        
        // Formata data
        $dt_ini = date('d/m/Y', $timestamp_ini);
        
		return $dt_ini;
	}
	
	
	/**
	 * Formata a data no padrão americano
	 *
	 * @param Data referência (Padrão BR)
	 * @return date
	 */
	private function FormatarDataUS($dt_ref){
		// Prepara a data de início
		$data = explode("/",$dt_ref);
		/* $dia = $data[0];
		$mes = $data[1];
		$ano = $data[2]; */
		$dt_format = $data[2]."-".$data[1]."-".$data[0];
		
		return $dt_format;
	}
	
	
	/**
	 * Calcula a qtde de dias do período
	 * 
	 * @param Data início, Data fim
	 * @return integer
	 */
	private function CalcularDiasPeriodo($dt_ini, $dt_fim){		
		
		$data1 = new DateTime( $this->FormatarDataUS($dt_ini) );
		$data2 = new DateTime( $this->FormatarDataUS($dt_fim) );
		
		$intervalo = $data1->diff( $data2 );
        
		return $intervalo->days;
	}
	
		
	/**
	 * Popula e trata os parametros bidirecionais entre view e action
	 * 
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
	 * Valida os campos obrigatórios na pesquisa.
	 *
	 */
	public function validarCamposBusca(stdClass $dados) {
	
		//Campos para destacar na view em caso de erro
		$camposDestaques = array();
	
		//Verifica se houve erro
		$error = false;
	
		// Verifica os campos obrigatórios
		if ((!isset($dados->dataInicial) || trim($dados->dataInicial) == '') ||
		(!isset($dados->dataFinal) || trim($dados->dataFinal) == '')){
			$camposDestaques[] = array(
					'campo' => 'dataInicial'
			);
			$camposDestaques[] = array(
					'campo' => 'dataFinal'
			);
			$error = true;
		}
		// O período de pesquisa é de no máximo 180 dias.
		elseif($this->CalcularDiasPeriodo($dados->dataInicial, $dados->dataFinal) > 180){
			$camposDestaques[] = array(
					'campo' => 'dataInicial'
			);
			$camposDestaques[] = array(
					'campo' => 'dataFinal'
			);
			$error2 = true;			
		}
		
		
	
		if ($error){
			$this->view->camposDestaque = $camposDestaques;
			throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
		}
		if ($error2){
			$this->view->camposDestaque = $camposDestaques;
			throw new Exception('O período deve ser inferior a 180 dias.');
		}
	
	}
	
	/**
	 * Atribui os filtros conforme informados 
	 * em tela.
	 * 
	 * @throws Exception
	 * @return text
	 */
	public function atribuirFiltro(stdClass $parametros, $tipo){
		
		// Período conclusão
		if ((isset($parametros->dataInicial) && trim($parametros->dataInicial) != '') &&
		(isset($parametros->dataFinal) && trim($parametros->dataFinal) != '')){
			$filtro.= "
					AND obidt_conclusao BETWEEN '".$parametros->dataInicial." 00:00:00' AND '".$parametros->dataFinal." 23:59:59' ";
		}
		// Usuário conclusão
		if (isset($parametros->nomeusuoid_concl_busca) && trim($parametros->nomeusuoid_concl_busca) != '' ){
			$filtro.= "
					AND obiusuoid_conclusao IN (SELECT cd_usuario FROM usuarios WHERE nm_usuario ILIKE '%".($parametros->nomeusuoid_concl_busca)."%') ";
		}
		// Representante responsável
		if (isset($parametros->repoid_busca) && trim($parametros->repoid_busca) != ''){
			$filtro.= "
					AND obirepoid = ".intval($parametros->repoid_busca)." ";
		}
		// Instalador
		if (isset($parametros->itloid_busca) && trim($parametros->itloid_busca) != ''){
			$filtro.= "
					AND obiitloid = ".intval($parametros->itloid_busca)." ";
		}
		// Região comercial
		if (isset($parametros->ftcoid_busca) && trim($parametros->ftcoid_busca) != ''){
			$filtro.= "
					AND obirepoid IN (SELECT repoid FROM representante WHERE repftcoid_tecnica = ".intval($parametros->ftcoid_busca).")";
		}
		// Tipo do item
		if (isset($parametros->otioid_busca) && trim($parametros->otioid_busca) != ''){
			$filtro.= "
					AND '".$parametros->otioid_busca."' IN (SELECT otitipo FROM os_tipo_item WHERE otioid IN (SELECT cmiotioid FROM comissao_instalacao WHERE cmiord_serv = obiordoid)) ";
		}
		// Tipo da ordem
		if (isset($parametros->ostoid_busca) && trim($parametros->ostoid_busca) != ''){
			$filtro.= "
					AND ".intval($parametros->ostoid_busca)." IN (SELECT otiostoid FROM os_tipo_item WHERE otioid IN (SELECT cmiotioid FROM comissao_instalacao WHERE cmiord_serv = obiordoid)) ";
		}
		// Classe do equipamento
		if (isset($parametros->eqcoid_busca) && trim($parametros->eqcoid_busca) != ''){
			$filtro.= "
					AND coneqcoid = ".intval($parametros->eqcoid_busca)." ";
		}
		// Modalidade do contrato
		if (isset($parametros->conmodalidade_busca) && trim($parametros->conmodalidade_busca) != ''){
			$filtro.= "
					AND conmodalidade = '".$parametros->conmodalidade_busca."' ";
		}

		return $filtro;
		
	}
	
	/**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros) {
	
    	try{
    		// Valida obrigatoriedade 
    		$this->validarCamposBusca($filtros);

    		// Atribui os filtros
    		$parametros = $this->atribuirFiltro($filtros, 'P');
	    	
    		// Realiza a pesquisa
	    	$resultadoPesquisa = $this->dao->pesquisar($parametros);
	
	        //Valida se houve resultado na pesquisa
	        if (count($resultadoPesquisa) == 0) {
	            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
	        }
	        
	        $resArray = array();
	        
	        // Atribui a quantidade total de itens retornados
	        $this->view->totalItens = count($resultadoPesquisa);
	        
	        foreach ($resultadoPesquisa as $resultado){
	        	
	        	if (is_array($resArray) && !in_array($resultado->ordoid, $resArray)){
	        		
	        		$resArray[$resultado->ordoid]['dt_ordem'] 		= $resultado->dt_ordem;
	        		$resArray[$resultado->ordoid]['connumero'] 		= $resultado->connumero;
	        		$resArray[$resultado->ordoid]['clinome'] 		= $resultado->clinome;
	        		$resArray[$resultado->ordoid]['repnome'] 		= $resultado->repnome;
	        		$resArray[$resultado->ordoid]['projeto'] 		= $resultado->eprnome;
	        		$resArray[$resultado->ordoid]['tipo_motivo'] 	= str_replace(",", ",<br>", $resultado->tipo_motivo);
	        		$resArray[$resultado->ordoid]['seguradora'] 	= $resultado->seguradora;
	        		$resArray[$resultado->ordoid]['tpcdescricao'] 	= $resultado->tpcdescricao;
	        		
	        	}
	        		
        		$resArray[$resultado->ordoid]['prdoid'][$resultado->prdoid] 				= $resultado->prdoid;
        		$resArray[$resultado->ordoid]['prdproduto'][$resultado->prdoid] 			= $resultado->prdproduto;
        		$resArray[$resultado->ordoid]['qtd_baixada'][$resultado->prdoid] 			= $resultado->obiqtd_baixada;
        		$resArray[$resultado->ordoid]['qtd_necessaria'][$resultado->prdoid] 		= $resultado->obiqtd_necessaria;
        		$resArray[$resultado->ordoid]['prdlimitador_minimo'][$resultado->prdoid] 	= $resultado->prdlimitador_minimo;
        		$resArray[$resultado->ordoid]['prdlimitador_maximo'][$resultado->prdoid] 	= $resultado->prdlimitador_maximo;
        		$resArray[$resultado->ordoid]['espqtde'][$resultado->prdoid] 				= $resultado->espqtde;
        		$resArray[$resultado->ordoid]['espqtde_trans'][$resultado->prdoid] 			= $resultado->espqtde_trans;
        		
	        }
	        
	        $this->view->status = TRUE;
	
	        //var_dump($resArray);
	        //exit;
	        return $resArray;
    		
    	} catch (Exception $e) {
    		
    		$this->view->mensagemAlerta = $e->getMessage();
    		
    	}
    	
    	//Incluir a view padrão
    	require_once _MODULEDIR_ . "/Manutencao/View/man_correcao_baixa_estoque/index.php";
    }
	
	/**
     * Responsável por gerar arquivos CSV, contendo 
     * relatórios, de Diferença da baixa ou Origem 
     * da baixa, em Background.
     * 
     * @param 
     * @return 
     */
    public function pesquisarBaixaIncorreta() {
    	
    	try {
    		$dados = $this->tratarParametros();
    		
    		// Valida obrigatoriedade
    		$this->validarCamposBusca($dados);
    		
    		// Inicia transição
    		$this->dao->begin();
    		
    		// Atribui demais parametros
    		$dados->idUsuario 	= $this->usuarioLogado; // Usuario logado
    		$dados->tipo_csv	= 'D'; // 'D'iferença da baixa 
    		$nome_arquivo = 'LOGdiferencaBaixa.txt'; // Nome arquivo
    		$desc_arquivo = 'DiferencaBaixa'; // Tipo da pesquisa
    		
    		// Insere registro de pesquisa em andamento
    		$this->dao->inserirDadosExecucao($dados);
    
    		// Gera diretório de LOG
    		if (!is_dir("/var/www/docs_temporario/baixaEstoqueIncorreta")) {
    			if (!mkdir("/var/www/docs_temporario/baixaEstoqueIncorreta", 0777)) {
    				throw new Exception('Falha ao criar diretório de log.');
    			}
    		}
    		// Atribui permissões
    		chmod("/var/www/docs_temporario/baixaEstoqueIncorreta", 0777);
			
    		$arquivo = fopen("/var/www/docs_temporario/baixaEstoqueIncorreta/$nome_arquivo", "w");
    		if ($arquivo) {
    
    			fputs($arquivo, "Relatório Iniciado - " . microtime(true) . "\r\n");
    			fclose($arquivo);
    			chmod("/var/www/docs_temporario/baixaEstoqueIncorreta/$nome_arquivo", 0777);
    
    			// Atribui origem
    			$httpHost = $_SERVER['HTTP_HOST'];
    			// Máquina local (Windows) 
    			if( $httpHost == "10.1.4.242"){
    				
    				exec('"C:/Program Files (x86)/Zend/ZendServer/bin/php.exe" C:/var/www/html/sistemaWeb/CronProcess/gerar_relatorio_baixa_incorreta.php >> C:/var/www/docs_temporario/baixaEstoqueIncorreta/' . $nome_arquivo . ' 2>&1 &');
					
    			}
    			// Servidores (Linux)
    			else {
    				
    				//echo "/usr/bin/php " . _SITEDIR_ . "CronProcess/gerar_relatorio_baixa_incorreta.php >> /var/www/docs_temporario/baixaEstoqueIncorreta/" . $nome_arquivo . " 2>&1 &";
    				passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/gerar_relatorio_baixa_incorreta.php >> /var/www/docs_temporario/baixaEstoqueIncorreta/" . $nome_arquivo . " 2>&1 &");
    				
    			}
    
    		} else {
    			throw new Exception('Falha ao criar arquivo de log.');
    		}
    		
    		// Finaliza transição
    		$this->dao->commit();
    
    		// unset($_POST);
    	} catch (Exception $e) {
    		$this->view->mensagemAlerta = $e->getMessage();
    
    		$this->dao->rollback();
    	} catch (ErrorException $e) {
    		$this->dao->finalizarProcesso('F');
    
    		$this->dao->rollback();
    	}
    
    	$this->index();
    }
	
	/**
     * Responsável por verficar a concorrência 
     * entre pocessos, da geração de arquivos 
     * CSV, de Diferença da baixa ou Origem da 
     * baixa.
     * 
     * @param var finalizado
     * @return array "codigo", "msg", "parametros"
     */
    public function verificarProcesso($finalizado) {
    
    	try {
    
    		// Verifica concorrência entre processos
    		$parametros = $this->dao->recuperarParametros($finalizado);
    		
    		$parametrosGeracaoCSV = explode("|", $parametros->erbiparametros);
    		
    		$msg = "Geração de arquivo CSV 'Diferença da Baixa' iniciada por " . $parametros->nm_usuario . " em " . $parametros->data_inicio . ".";
    
    		return array(
    				"codigo" => 2,
    				"msg" => $msg,
    				"parametros" => $parametros
    		);
    	} catch (Exception $e) {
    		return array(
    				"codigo" => 0,
    				"msg" => ''
    		);
    	} catch (ErrorException $e) {
    		return array(
    				"codigo" => 1,
    				"msg" => "Falha ao verificar concorrência. Tente novamente."
    		);
    	}
    }

    /**
     * Responsável buscar e atribuir parâmetros 
     * de consulta para geração de arqivo CSV. 
     * Chamada no arquivo do CRON.
     *
     * @param null
     * @return void
     */
    public function setarParametrosProcesso() {
    
    	try {
    		
			// Busca parâmetros de consulta para geração de arqivo CSV
    		if (!$parametros = $this->dao->recuperarParametros(false)) {
    			throw new Exception('Erro ao recuperar parâmetros de consulta para geração de arqivo CSV.');
    		}
    		
    		$parametrosGeracaoCSV = explode("|", $parametros->erbiparametros);
    		
    		// Atribui parâmetros de consulta para geração de arqivo CSV
    		$this->parametrosGeracaoCSV = new stdClass();
    		
    		$this->parametrosGeracaoCSV->tipo_csv 				= $parametrosGeracaoCSV[0];
    		$this->parametrosGeracaoCSV->dataInicial			= $parametrosGeracaoCSV[1];
    		$this->parametrosGeracaoCSV->dataFinal 				= $parametrosGeracaoCSV[2];
    		$this->parametrosGeracaoCSV->nomeusuoid_concl_busca = $parametrosGeracaoCSV[3];
    		$this->parametrosGeracaoCSV->repoid_busca			= $parametrosGeracaoCSV[4];
    		$this->parametrosGeracaoCSV->itloid_busca 			= $parametrosGeracaoCSV[5];
    		$this->parametrosGeracaoCSV->ftcoid_busca 			= $parametrosGeracaoCSV[6];
    		$this->parametrosGeracaoCSV->otioid_busca			= $parametrosGeracaoCSV[7];
    		$this->parametrosGeracaoCSV->eqcoid_busca 			= $parametrosGeracaoCSV[8];
    		$this->parametrosGeracaoCSV->conmodalidade_busca 	= $parametrosGeracaoCSV[9];
    		$this->parametrosGeracaoCSV->idUsuario 				= $parametrosGeracaoCSV[10];
    	}
    	catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }
    
    }

    /**
     * Recebe um array e grava no arquivo, 
     * no formato CSV, separado por ';'.
     *
     * @param array $dados
     */
    private function gravarDados($dados) {
        $dadosCsv = implode($dados, ';');
        $dadosCsv .= "\n";
        fwrite($this->arquivo, $dadosCsv);
    }
	
	/**
     * Responsável por gerar arquivos CSV, contendo 
     * relatórios, de Diferença da baixa ou Origem 
     * da baixa, em Background.
     * 
     * @param 
     * @return 
     */
    public function gerarRelatorio() {
    	
    	try {    		
    		// Inicia transação
    		$this->dao->begin();
    		
    		// Chama o método de busca e atribui nome/ tipo do arquivo de acordo com o tipo do CSV informado
    		if ($this->parametrosGeracaoCSV->tipo_csv == 'D') {
    			// Tipo da pesquisa
    			$desc_arquivo = 'DiferencaBaixa'; 
    			// Nome arquivo LOG
    			$nome_arq_log = 'LOGdiferencaBaixa.txt'; 			
    			// Atribui os filtros
    			$parametros = $this->atribuirFiltro($this->parametrosGeracaoCSV, 'D'); 
    			// Realiza pesquisa
    			$listaPendentes = $this->dao->gerarDiferencaCsv($parametros); 
    		} 
    		
    		$this->view->nomeArquivo = '/var/www/docs_temporario/rel' . $desc_arquivo . '.csv';
    		
    		$this->arquivo = fopen($this->view->nomeArquivo, "w");
    
    		$cabecalho = array(
    			'DATA',
		    	'CODIGO_OS',
		    	'CODIGO_REP',
		    	'REPRESENTANTE',
		    	'PRODUTO',
		    	'CODIGO_PROD',
		    	'QTD',
		    	'TIPO_OS - MOTIVO',
		    	'CLASSE_OS',
		    	'VERSAO',
		    	'MODALIDADE',
		    	'VALOR_UNIT',
		    	'VALOR_TOTAL');  
			
			$this->gravarDados($cabecalho);
			
			for ($i = 0; $i < count($listaPendentes); $i++) {
				$vlr_total 							= ($listaPendentes[$i]->qtde_corrigir * $listaPendentes[$i]->vlr_unitario);
				$listaPendentes[$i]->vlr_unitario 	= number_format($listaPendentes[$i]->vlr_unitario, 2, ',', '.');
				$listaPendentes[$i]->vlr_total 		= number_format($vlr_total, 2, ',', '.');
                    
				$this->gravarDados((array) $listaPendentes[$i]);
    
				// Salva no arquivo a cada 1000 registros
				if ($i % 1000 === 0) {
					fflush($this->arquivo);
				}
			}
    
            fclose($this->arquivo);
            
			// Indica finalização do processo no LOG
            file_put_contents("/var/www/docs_temporario/baixaEstoqueIncorreta/$nome_arq_log", "Relatório Finalizado - " . microtime(true) . "\r\n", FILE_APPEND);            
            // Indica término do processo em BD 'S'ucesso
			$this->dao->finalizarProcesso('S');
			// Finaliza transação
			$this->dao->commit();			
			// Envia email ao usuário que iniciou o resumo
			$this->enviarEmail($this->buscarDadosEmail(count($listaPendentes)));
            
        } catch (Exception $e) {
        	// Reverte ações na transação
			$this->dao->rollback();			
			// Indica término do processo em BD 'F'alha
            $this->dao->finalizarProcesso('F');            
            // Indica erro do processo no LOG
            file_put_contents("/var/www/docs_temporario/baixaEstoqueIncorreta/$nome_arq_log", $e->getMessage(), FILE_APPEND);
        }
    }

	/**
	 * Responsável por buscar e atribuir dados para montar o 
	 * conteúdo e o envio de email
	 * 
	 * @param 
	 * @return stdClass $email
	 */
	private function buscarDadosEmail($count) {
		
		try {
			$email = new stdClass();
			
			$nome_arquivo = ($this->parametrosGeracaoCSV->tipo_csv == 'D') ? 'Diferenca da Baixa' : 'Origem da Baixa'; // Nome da pesquisa
			
			// Busca dados referentes ao usuário logado
			$dadosEmail = $this->dao->buscarDadosEmail($this->parametrosGeracaoCSV->idUsuario);
			
			// Atribui dados para envio do email
			$email->remetente = "sascar@sascar.com.br";
			$email->cabecalho = "Relatorio " . $nome_arquivo;
			if ($count > 0) {
				
				$email->corpo 	= "
					<p>Prezado " . $dadosEmail->nm_usuario . ",</p>
					<p>&nbsp;</p>
					<p>O relatorio " . $nome_arquivo . ", em anexo, foi gerado com sucesso.</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p><img src=\"images/lg_sascar.gif\" alt=\"\" width=\"200\" height=\"46\" /></p>";
				$email->path 	= $this->view->nomeArquivo;
				
			} else {
				
				$email->corpo 	= "
					<p>Prezado " . $dadosEmail->nm_usuario . ",</p>
					<p>&nbsp;</p>
					<p>Nenhum registro encontrado para o relatorio " . $nome_arquivo . ".</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					<p><img src=\"images/lg_sascar.gif\" alt=\"\" width=\"200\" height=\"46\" /></p>";
				$email->path 	= '';
					
			}
			
			// Atribui destinatário conforme ambiente
			if ($_SESSION["servidor_teste"] == 1) {
				$email->destinatario = _EMAIL_TESTE_;
			} 
			else{
				$email->destinatario = $dadosEmail->usuemail;
			}
			
			return $email;
            
        } catch (Exception $e) {
        	
			$this->view->mensagemAlerta = $e->getMessage();
			
        }
	}

	/**
	 * Responsável por enviar email ao contato cadastrado
	 * 
	 * @param stdClass $email
	 * @return boolean
	 */
	private function enviarEmail(stdClass $email) {
		
		// Atribui as variáveis para envio
		$phpmailer = new PHPMailer();
		$phpmailer->isSmtp();
		$phpmailer->From = $email->remetente;
		$phpmailer->FromName = "Sascar";
		$phpmailer->ClearAllRecipients();
		$phpmailer->AddAddress($email->destinatario);
		$phpmailer->Subject = $email->cabecalho;
		$phpmailer->MsgHTML($email->corpo);
		$phpmailer->AddAttachment($email->path);
				
		if (!$phpmailer->Send()) {
			throw new Exception("Houve um erro ao enviar e-mail.");
		}
		return true;
	}

	/**
	 * Responsável por corrigir as baixas de materias incorretas
	 * das odens de seviço selecionadas
	 * 
	 * @param stdClass $arrayOS
	 * @return boolean
	 */
	public function corrigirBaixaIncorreta() {
	
		try {
			
			// Popula um objeto com os parametros
			$dados = $this->tratarParametros();
			
			// Atribui as OS selecionadas a um array
			$arrayOS = explode(",",$dados->arrayOS);
			
			// Inicia transação
			$this->dao->begin();

			// Cria a variável de total de itens corrigidos
			$totalCorrigidos = 0;
			
			// Percorre as OS
			foreach ($arrayOS as $key => $ordoid){
				
				// Busca dados correção
				$resultadoPesquisa = $this->dao->buscarDadosCorrecao($ordoid);
				
				/**
				 * ASM: 100055 - obirelroid (os_baixa_incorreta) = esprelroid (estoque_produto)
				 * Ao pesquisar as correções é feito um LEFT JOIN;
				 * Ao corregir é feito um INNER JOIN.
				 * Caso não exista esse vinculo, ao invés de abortar lançando Exceção todas as correções,
				 * efetua as que não possuem restrições, e depois apresenta o que não
				 * foi possivel corrigir, para que seja efetuada uma tratativa manual pelo
				 * usuário.
				 */
				if (count($resultadoPesquisa) == 0 || $resultadoPesquisa == null) {
					continue;
				}
				
				// Atribui observação de histórico do contrato
				$observ = "Materiais baixados do estoque (Intranet) \nOrdem de serviço número: " .$ordoid . "\n" ;
				// Cria/ limpa a variável de produtos
				$produtos 	= 0;
				// Cria/ limpa a variável de grupo
				$grupo		= '';
				
				// Percorre os produtos
				foreach ($resultadoPesquisa as $resultado){
					
					// Baixa
					if ($resultado->obitipo == 'B' && $resultado->obiqtdestoque > 0){
						// Atribui a quantidade a corrigir conforme estoque atual do representante							
						$qtdcorrigir = ($resultado->obiqtdcorrigir <= $resultado->obiqtdestoque) ? $resultado->obiqtdcorrigir : $resultado->obiqtdestoque;
						
						// Insere registro na ordem de serviço por produto
						$this->dao->inserirOSProduto($ordoid,$resultado->obiprdoid,$qtdcorrigir,$this->usuarioLogado);
						
						// Atualiza estoque do representante (retira)
						$this->dao->atualizarEstoqueRepresentante($resultado->obirelroid,$resultado->obiprdoid,'(-'.$qtdcorrigir.')');
						
						// Insere registro de movimentação do estoque 
						$this->dao->inserirmovimentacaoEstoque('E','O',$ordoid,$qtdcorrigir,8,$resultado->obiprdoid,'FC');

						// Atualiza a quantidade baixada (acrescenta)
						$this->dao->atualizarOrigemBaixa($ordoid,$resultado->obiprdoid,$qtdcorrigir,$this->usuarioLogado);
						
						// Concatena observação de histórico do contrato 
						if ($grupo !=  $resultado->obigrupo_material){
							$observ .=  "\n". $resultado->obigrupo_material . "\n";
						}
						$observ .= $qtdcorrigir . " - " . $resultado->obiproduto . " - ID: ". $resultado->obiprdoid ."\n";

						// Atribui variável para verificar a mudança de grupo material
						$grupo = $resultado->obigrupo_material;
						// Soma 1 aos produtos
						$produtos++;
						// Soma 1 ao total de itens corrigidos
						$totalCorrigidos++;
					}
					// Estorno
					elseif ($resultado->obitipo == 'E'){ 
						
						// Neste momento a quantidade a corrigir restante é o total a corrigir
						$qtdcorrigir_restante = $resultado->obiqtdcorrigir;
						
						// Busca dados dos produtos da OS
						$resultadoPesquisaOsp = $this->dao->buscarOSProduto($ordoid,$resultado->obiprdoid);
						
						if (count($resultadoPesquisaOsp) > 0) {
							// Percorre os produtos da OS
							foreach ($resultadoPesquisaOsp as $resultadoOsp){
								
								// Se a quantidade de produtos do registro é menor que a quantidade a corrigir restante	
								if ($resultadoOsp->ospqtde < $qtdcorrigir_restante){
									// Só corrige a quantidade de produtos do registro
									$qtdcorrigir = $resultadoOsp->ospqtde;
								} else {
									// Corrige toda a quantidade (quantidade restante)
									$qtdcorrigir = $qtdcorrigir_restante;
								}
								// Atualiza registro na ordem de serviço por produto
								$this->dao->atualizarOSProduto($resultadoOsp->ospoid,$qtdcorrigir);
								
								// Atualiza a quantidade a corrigir restante
								$qtdcorrigir_restante = $qtdcorrigir_restante - $qtdcorrigir;
								
							}
						}
						
						// Atualiza estoque do representante (acrescenta)
						$this->dao->atualizarEstoqueRepresentante($resultado->obirelroid,$resultado->obiprdoid,$resultado->obiqtdcorrigir);
						
						// Insere registro de movimentação do estoque 
						$this->dao->inserirmovimentacaoEstoque('S','O',$ordoid,$resultado->obiqtdcorrigir,19,$resultado->obiprdoid,'FC');
						
						// Atualiza a quantidade baixada (retira)
						$this->dao->atualizarOrigemBaixa($ordoid,$resultado->obiprdoid,'(-'.$resultado->obiqtdcorrigir.')',$this->usuarioLogado);
						
						// Soma 1 ao total de itens corrigidos
						$totalCorrigidos++;
					}
					
				}
				
				if ($produtos > 0){
					// Insere registro de histórico do contrato 
					$this->dao->inserirHistoricoTermo($resultado->obiconoid,$this->usuarioLogado,$observ);
				}
				
			}
    		
    		// Finaliza transação
    		$this->dao->commit();
		
    		// Atribui mensagem 
    		$totalNaoCorrigidos = ($dados->totalItens - $totalCorrigidos);
    		$this->view->mensagemSucesso = "Itens corrigidos:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$totalCorrigidos."; <br/>
    										Itens não corrigidos: ".$totalNaoCorrigidos.";";
    		
        } catch (Exception $e) {
        	
			$this->view->mensagemAlerta = $e->getMessage();
			
        	// Reverte ações na transação
			$this->dao->rollback();	
			
        }

        $this->index();
        
	}
	
}