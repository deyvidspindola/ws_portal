<?php

/**
 * Classe FinPrevisaoArrecadacaoVivo.
 * Camada de regra de negocio.
 *
 * @package  Financas
 * @author   Marcello Borrmann <marcello.borrmann@meta.com.br>
 *
 */
class FinPrevisaoArrecadacaoVivo {

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
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        }
        
        $dadosProcesso = (object) $this->verificarProcesso(false);
        if ($dadosProcesso->codigo == 2) {
        	$this->view->processoExecutando = true;
        	$this->view->mensagemPrevisao = $dadosProcesso->msg;
        	//$this->view->parametrosProcesso = $dadosProcesso->parametros;
        } else {
        	$this->view->processoExecutando = false;
        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Financas/View/fin_previsao_arrecadacao_vivo/index.php";
    }

    /**
     * Método de consulta.
     *
     *
     * @return void
     */    
    public function consulta(){

        //Incluir a view consulta
        require_once _MODULEDIR_ . "Financas/View/fin_previsao_arrecadacao_vivo/consulta.php";
    
    }

    /**
     * 
     *
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametros existem, senão inicializa todos
        $this->view->parametros->dataReferencia = isset($this->view->parametros->dataReferencia) && !empty($this->view->parametros->dataReferencia) ? trim($this->view->parametros->dataReferencia) : "";

        $this->view->parametros->nomeCliente = isset($this->view->parametros->nomeCliente) && !empty($this->view->parametros->nomeCliente) ? trim($this->view->parametros->nomeCliente) : "";

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
        
        // Verifica os campos obrigatórios
        if (!isset($dados->dataReferencia) || trim($dados->dataReferencia) == '') {
        	$camposDestaques[] = array(
        			'campo' => 'dataReferencia'
        	);
        	$error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

    } 
    
    /**
     * Valida os campos obrigatórios.
     *
     * @return
     */
    public function validarCamposConsulta(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;
        
        // Verifica os campos obrigatórios
        if (!isset($dados->dataReferencia_psq) || trim($dados->dataReferencia_psq) == '') {
        	$camposDestaques[] = array(
        			'campo' => 'dataReferencia_psq'
        	);
        	$error = true;
        }
        if (!isset($dados->opcao_psq) || trim($dados->opcao_psq) == '') {
        	$camposDestaques[] = array(
        			'campo' => 'opcao_psq'
        	);
        	$error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

    } 
    
    /**
     * Método que registra o processamento, cria arquivo de LOG e dispara o processamento em backgorund
     *
     *
     *
     * @return void
     */
    public function prepararPrevisao() {
    	try {
    		$this->dao->begin();
    
    		$this->view->parametros = $this->tratarParametros();
    
    		$this->validarCamposGeracao($this->view->parametros);
    
    		$this->view->parametros->idUsuario = $_SESSION['usuario']['oid'];
    
    		$this->dao->inserirDadosExecucao('A', $this->view->parametros);
    
    		$this->dao->commit();
    
    		if (!is_dir(_SITEDIR_ . "faturamento")) {
    			if (!mkdir(_SITEDIR_ . "faturamento", 0777)) {
    				throw new Exception('Falha ao criar arquivo de log.');
    			}
    		}
    
    		chmod(_SITEDIR_ . "faturamento", 0777);
    
    		$arquivo = fopen(_SITEDIR_ . "faturamento/geracao_previsao_arrecadacao_vivo.txt", "w");
    		if ($arquivo) {
    
    			fputs($arquivo, "Resumo Iniciado\r\n");
    			fclose($arquivo);
    			chmod(_SITEDIR_ . "faturamento/geracao_previsao_arrecadacao_vivo.txt", 0777);
    
    			$httpHost = $_SERVER['HTTP_HOST'];
    			// Windows
    			if( $httpHost == "10.1.4.242"){
    				 
    				exec('"C:/Program Files (x86)/Zend/ZendServer/bin/php.exe" C:/var/www/html/sistemaWeb/CronProcess/gerar_previsao_arrecadacao_vivo.php >> C:/var/www/html/sistemaWeb/faturamento/geracao_previsao_arrecadacao_vivo.txt 2>&1 &');
    
    			}
    			// Linux
    			else {
    
    				passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/gerar_previsao_arrecadacao_vivo.php >> " . _SITEDIR_ . "faturamento/geracao_previsao_arrecadacao_vivo.txt 2>&1 &");
    				 
    			}
    
    		} else {
    			throw new Exception('Falha ao criar arquivo de log.');
    		}
    
    		unset($_POST);
    		
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
     * 
     *
     * @return void
     */    
    public function setarParametrosProcesso() {
    
    	$parametros = $this->dao->recuperarParametros(false);
    
    	$parametrosArrecadacao 	= explode("|", $parametros->epvparametros);
    	
    	$this->parametrosArrecadacao = new stdClass();
    
    	$this->parametrosArrecadacao->dataReferencia 	= $parametrosArrecadacao[0];
    	$this->parametrosArrecadacao->nomeCliente		= $parametrosArrecadacao[1];
    	$this->parametrosArrecadacao->idUsuario 		= $parametrosArrecadacao[2];

    	$parametrosReferencia 	= explode("/", $parametrosArrecadacao[0]);
    	
    	$this->parametrosArrecadacao->mes 				= $parametrosReferencia[1];
    	$this->parametrosArrecadacao->ano 				= $parametrosReferencia[2];
    	 
    }
    
    /**
     * Método que gera a previsão de arrecadação de taxas de monitoramento e instalação de contratos VIVO.
     *
     * 
     *
     * @return void
     */
    public function gerarPrevisao() {
        
    	try {
    
    		$this->dao->begin();
			
    		$arquivo = fopen("faturamento/geracao_previsao_arrecadacao_vivo.txt", "a");
    		
    		$idProcesso = $this->dao->buscarIdProcessoBancoDados();
    		
    		if ($idProcesso > 0) {
    			file_put_contents('faturamento/pidProcessoVivoBD.txt', $idProcesso);
    		} 
    		
    		//sleep (25);
    		
    		// Gerar previsão de arrecadação pró-rata de monitoramento
    		$this->dao->inserirPrevisaoArrecadacaoProRataMonitoramento($this->parametrosArrecadacao);    
    		fputs($arquivo, "inserirPrevisaoArrecadacaoProRataMonitoramento finalizada - " . microtime(true) . "\r\n");
    		
    		// Gerar previsão de arrecadação de monitoramento
    		$this->dao->inserirPrevisaoArrecadacaoMonitoramento($this->parametrosArrecadacao);
    		fputs($arquivo, "inserirPrevisaoArrecadacaoMonitoramento finalizada - " . microtime(true) . "\r\n");
    		
    		// Gerar previsão de arrecadação de instalação
    		$this->dao->inserirPrevisaoArrecadacaoInstalacao($this->parametrosArrecadacao);
    		fputs($arquivo, "inserirPrevisaoArrecadacaoInstalacao finalizada - " . microtime(true) . "\r\n");
    		
    		$this->dao->finalizarProcesso('S');
    		
    		fputs($arquivo, "gerarPrevisao finalizada - " . microtime(true) . "\r\n");
    		
    		fclose($arquivo);
    		
    		$this->dao->commit();
    		
    	} catch (Exception $e) {
    		
    		$this->dao->rollback();
    		
    		$this->view->mensagemAlerta = $e->getMessage();
    
    		$this->dao->finalizarProcesso('F');
    
    		file_put_contents('faturamento/geracao_previsao_arrecadacao_vivo.txt', $e->getMessage());
    		
    	} catch (ErrorException $e) {

            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
            
    		$this->dao->finalizarProcesso('F');
    
    		file_put_contents('faturamento/geracao_previsao_arrecadacao_vivo.txt', $e->getMessage());
        }
        
    }
	
    /**
     * Método que verifica concorrência entre processos.
     *
     *
     *
     * @return array
     */
    public function verificarProcesso($finalizado) {
    
    	try {
    		
    		$parametros = $this->dao->recuperarParametros($finalizado);
    		
    		if ($parametros->epvtipo_processo == 'F') {
    			$msg = "Faturamento iniciado por " .
    					$parametros->nm_usuario . " às " . $parametros->inicio . " &nbsp;&nbsp;-&nbsp;&nbsp;  " .
    					number_format($parametros->epvporcentagem, 1, ',', '.') . " % concluído.";
    		} else {
    			$msg = "Resumo iniciado por " . $parametros->nm_usuario . " às " . $parametros->inicio;
    		}
    		
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
     * Método que busca registros inseridos na previsão.
     *
     *
     *
     * @return boolean
     */
    public function consultarPrevisao() {    	
    	
    	try {
    			
    		$this->view->parametros = $this->tratarParametros();
    			
	    	$this->validarCamposConsulta($this->view->parametros);
	    		
	   		$parametrosReferencia = explode("/", $this->view->parametros->dataReferencia_psq);
	    		 
	   		$this->view->parametros->mes = $parametrosReferencia[0];
	   		$this->view->parametros->ano = $parametrosReferencia[1];		

    		$this->view->dados = $this->dao->consultarPrevisao($this->view->parametros);
    		
    		$this->gerarPlanilha($this->view->dados);
    		
        	$this->view->status = true;
    		
    	} catch (Exception $e) {
    		
    		$this->view->mensagemAlerta = $e->getMessage();
    		
    	}
    	$this->consulta();

    }

    /**
     * Recebe um array e grava no arquivo no formato CSV separado por ';'
     *
     * @param array $dados
     */
    private function gravarDados($dados) {
        $dadosCsv = implode($dados, ';');
        $dadosCsv .= "\n";
        fwrite($this->arquivo, $dadosCsv);
    }

    /**
     * Monta arquivo no formato CSV 
     *
     * @param array $dados
     */
    public function gerarPlanilha($dados) {
    	try { 
    
    		$this->view->nomeArquivo = '/var/www/docs_temporario/rel_previsao_arrecadacao_vivo.csv';
    		$this->arquivo = fopen($this->view->nomeArquivo, "w");
    
    		$cabecalho = array(
    				"Previsão",
    				"Dt. Previsão",
    				"Contrato",
    				"CPF/ CNPJ",
    				"Cliente",
    				"Placa",
    				"Subscription",
    				"Item",
    				"Valor",
    				"Desconto",
    				"Mês/ Ano",
    				"Início Vigência",
    				"Vencimento",
    				"Ciclo",
		    		"Processado",
		    		"Status Vivo");     		
    		
            $titulo = array_fill(0, count($cabecalho), '');
            $titulo[0] = "Previsão de Arrecadação Vivo";
    
            $this->gravarDados($titulo);
            $this->gravarDados(array_fill(0, count($cabecalho), ''));
            $this->gravarDados($cabecalho);
    
            $total = 0;
  		
    		for ($i = 0; $i < count($dados); $i++){
	
   				$total += $dados[$i]->pavvl_previsao;
   				$dados[$i]->pavvl_previsao =  number_format($dados[$i]->pavvl_previsao, 2, ',', '.');
   				$dados[$i]->pavvl_desconto =  number_format($dados[$i]->pavvl_desconto, 2, ',', '.');
	    			
   				$this->gravarDados((array) $dados[$i]);
	    
            	// Salva no arquivo a cada 1000 registros
                if ($i % 1000 === 0) {
                    fflush($this->arquivo);
                }
            }
    
            $rodape = array_fill(0, count($cabecalho), '');
            $rodape[count($cabecalho) - 9] = "Total:";
            $rodape[count($cabecalho) - 8] = number_format($total, 2, ',', '.');
    
            $this->gravarDados($rodape);
    
            fclose($this->arquivo);

            $this->view->total = number_format($total, 2, ',', '.');
                
        } catch (Exception $e) {
            	
            $this->view->mensagemAlerta = $e->getMessage();
                
        }
    }

    /**
     * Método que processa os registros inseridos na previsão de acordo com os filtroa informados.
     *
     *
     * @return boolean
     */
    public function processarPrevisao() {
        	 
      	try {
    
    		$this->dao->begin();
        		 
       		$this->view->parametros = $this->tratarParametros();
        		 
       		$this->validarCamposConsulta($this->view->parametros);
        		 
       		$parametrosReferencia = explode("/", $this->view->parametros->dataReferencia_psq);
        
       		$this->view->parametros->mes = $parametrosReferencia[0];
       		$this->view->parametros->ano = $parametrosReferencia[1];
       		
       		$this->view->parametros->idUsuario = $_SESSION['usuario']['oid'];
        
       		$this->view->dados = $this->dao->processarPrevisao($this->view->parametros);
       		
       		$this->view->mensagemSucesso = "Dados de previsão processados com sucesso.";
       		
       		$this->dao->commit();
        
       	} catch (Exception $e) {

            $this->dao->rollback();
        
       		$this->view->mensagemAlerta = $e->getMessage();
        
       	}
       	$this->consulta();
        
    }

    /**
     * Método que deleta os registros, não processados, inseridos na previsão de acordo com os filtroa informados.
     *
     *
     * @return boolean
     */
    public function excluirPrevisao() {
        	 
      	try {
        		 
       		$this->view->parametros = $this->tratarParametros();
        		 
       		$this->validarCamposConsulta($this->view->parametros);
        		 
       		$parametrosReferencia = explode("/", $this->view->parametros->dataReferencia_psq);
        
       		$this->view->parametros->mes = $parametrosReferencia[0];
       		$this->view->parametros->ano = $parametrosReferencia[1];

       		$this->view->parametros->idUsuario = $_SESSION['usuario']['oid'];
        
       		$this->view->dados = $this->dao->excluirPrevisao($this->view->parametros);
       		
       		$this->view->mensagemSucesso = "Dados de previsão excluídos com sucesso.";

       		$this->dao->commit();
        
       	} catch (Exception $e) {

            $this->dao->rollback();
        
       		$this->view->mensagemAlerta = $e->getMessage();
        
       	}
       	$this->consulta();
        
    }

}