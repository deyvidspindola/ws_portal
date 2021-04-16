<?php

/**
 * Classe RelEnvioSmsRetorno.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   MARCELLO BORRMANN <marcello.b.ext@sascar.com.br>
 *
 */
class RelEnvioSmsRetorno {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {
		
        $this->dao                   = is_object($dao) ? $this->dao = $dao : NULL;
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
     * Reponsável também por realizar a pesquisa invocando o método privado
     * 
     * @return void
     */
    public function index() {
		
        try {
            
        	$this->view->parametros = $this->tratarParametros();
            
            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação Pesquisar e executar pesquisa
            if (isset($this->view->parametros->acao) && ($this->view->parametros->acao == 'pesquisar')) {
            	$this->view->dados = $this->pesquisar();
            }

            //Verificar se a ação Gerar CSV e executar pesquisa
            if (isset($this->view->parametros->acao) && ($this->view->parametros->acao == 'gerar_csv')) {
            	$this->view->nome_arquivo = $this->gerarCSV($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_envio_sms_retorno/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     * 
     * @return stdClass Parametros tradados
     */
    public function tratarParametros() {

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
        
        return $retorno;
    }

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * 
     * @return void
     */
    public function inicializarParametros() {
		
        //Verifica se os parametro existem, senão iniciliza 
		$this->view->parametros->dt_ini_busca			= isset($this->view->parametros->dt_ini_busca) && trim($this->view->parametros->dt_ini_busca) != "" ? trim($this->view->parametros->dt_ini_busca) : NULL; // date("d/m/Y")
		$this->view->parametros->dt_fim_busca			= isset($this->view->parametros->dt_fim_busca) && trim($this->view->parametros->dt_fim_busca) != "" ? trim($this->view->parametros->dt_fim_busca) : NULL; // date("d/m/Y")
		$this->view->parametros->dt_ref_busca			= isset($this->view->parametros->dt_ref_busca) && trim($this->view->parametros->dt_ref_busca) != "" ? trim($this->view->parametros->dt_ref_busca) : 0; 
		$this->view->parametros->hsecodigo_retorno_busca= isset($this->view->parametros->hsecodigo_retorno_busca) && trim($this->view->parametros->hsecodigo_retorno_busca) != "" ? trim($this->view->parametros->hsecodigo_retorno_busca) : NULL ; 
		$this->view->parametros->ordoid_busca			= isset($this->view->parametros->ordoid_busca) && trim($this->view->parametros->ordoid_busca) != "" ? trim($this->view->parametros->ordoid_busca) : NULL ;
		$this->view->parametros->clinome_busca			= isset($this->view->parametros->clinome_busca) && trim($this->view->parametros->clinome_busca) != "" ? trim($this->view->parametros->clinome_busca) : NULL ; 
		$this->view->parametros->endno_ddd_busca		= isset($this->view->parametros->endno_ddd_busca) && trim($this->view->parametros->endno_ddd_busca) != "" ? trim($this->view->parametros->endno_ddd_busca) : NULL ; 
		$this->view->parametros->endno_cel_busca		= isset($this->view->parametros->endno_cel_busca) && trim($this->view->parametros->endno_cel_busca) != "" ? trim($this->view->parametros->endno_cel_busca) : NULL ; 
		$this->view->parametros->veiplaca_busca			= isset($this->view->parametros->veiplaca_busca) && trim($this->view->parametros->veiplaca_busca) != "" ? trim($this->view->parametros->veiplaca_busca) : NULL ; 
		
    }

    /**
     * Validar os campos obrigatórios na pesquisa.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    public function validarCamposPesquisa(stdClass $dados = null) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error  = false;
        $error1 = false;
        $error2 = false;
        
        // Verifica os campos obrigatórios 
        // Nenhum campo preenchido
        if (
        (!isset($dados->dt_ini_busca) || trim($dados->dt_ini_busca) == '') &&
        (!isset($dados->dt_fim_busca) || trim($dados->dt_fim_busca) == '') &&
        (!isset($dados->hsecodigo_retorno_busca) || trim($dados->hsecodigo_retorno_busca) == '') &&
        (!isset($dados->ordoid_busca) || trim($dados->ordoid_busca) == '') &&
        (!isset($dados->clinome_busca) || trim($dados->clinome_busca) == '') &&
        (!isset($dados->endno_ddd_busca) || trim($dados->endno_ddd_busca) == '') &&
        (!isset($dados->endno_cel_busca) || trim($dados->endno_cel_busca) == '') &&
        (!isset($dados->veiplaca_busca) || trim($dados->veiplaca_busca) == '')
        ) {
        	$camposDestaques[] = array(
        			'campo' => 'dt_ini_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'dt_fim_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'hsecodigo_retorno_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'ordoid_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'clinome_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'endno_ddd_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'endno_cel_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'veiplaca_busca'
        	);
        	$error = true;
        }
        // Dt Inicial não preenchida
        elseif ((!isset($dados->dt_ini_busca) || trim($dados->dt_ini_busca) == '') && (isset($dados->dt_fim_busca) || trim($dados->dt_fim_busca) != '')) {
            $camposDestaques[] = array(
                'campo' => 'dt_ini_busca'
            );
        	$error1 = true;
        }
        // Dt Final não preenchida
        elseif ((isset($dados->dt_ini_busca) || trim($dados->dt_ini_busca) != '') && (!isset($dados->dt_fim_busca) || trim($dados->dt_fim_busca) == '')) {
            $camposDestaques[] = array(
                'campo' => 'dt_fim_busca'
            );
        	$error1 = true;
        }
        // Dt Referência não preenchida
        elseif (
        ( isset($dados->dt_ini_busca) || trim($dados->dt_ini_busca) != '') && 
        ( isset($dados->dt_fim_busca) || trim($dados->dt_fim_busca) != '') && 
        (!isset($dados->dt_ref_busca) || trim($dados->dt_ref_busca) == '')
        ) {
            $camposDestaques[] = array(
                'campo' => 'dt_ref_busca'
            );
        	$error1 = true;
        }
        // Valida a quantidade de dias do período
        elseif ( 
        (isset($dados->dt_ini_busca) || trim($dados->dt_ini_busca) != '') && 
        (isset($dados->dt_fim_busca) || trim($dados->dt_fim_busca) != '') 
        ) {
			// Trata Dt Inicial
	        $arrBuscai = explode('/',$dados->dt_ini_busca);
	        $dia_i = $arrBuscai[0];
	        $mes_i = $arrBuscai[1];
	        $ano_i = $arrBuscai[2];	        
	        $mktime_ini = mktime(0,0,0,$mes_i,$dia_i,$ano_i);
	        
			// Trata Dt Final 
	        $arrBuscaf = explode('/',$dados->dt_fim_busca);
	        $dia_f = $arrBuscaf[0];
	        $mes_f = $arrBuscaf[1];
	        $ano_f = $arrBuscaf[2];
	        $mktime_fim = mktime(0,0,0,$mes_f,$dia_f,$ano_f);
	        
	        // Calcula a quantidade de segundos em 90 dias
	        $periodo_valido = (60*60*24*90);
	        
	        // Período maior que 90 dias
	        if ($mktime_fim - $mktime_ini > $periodo_valido) {	        	
	        	$camposDestaques[] = array(
                	'campo' => 'dt_ini_busca'
            	);
	        	$camposDestaques[] = array(
	        		'campo' => 'dt_fim_busca'
	        	);
	        	$error2 = true;
	        }
        
        }
        
       	if ($error) {
       		$this->view->camposDestaque = $camposDestaques;
       		throw new Exception("É obrigatório preencher ao menos um campo para realizar a pesquisa.");
       	}
       	elseif ($error1) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
       	elseif ($error2) {
            $this->view->dados = $camposDestaques;
            throw new Exception("O Período de busca não pode ser superior a 90 dias.");
        } 
    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * @throws Exception
     * @return array
     */
    public function pesquisar() {

    	try{

			$this->view->parametros = $this->tratarParametros();
	
	    	//Inicializa os dados
	    	$this->inicializarParametros();
	    	
	    	//Valida campos obrigatórios
	    	$this->validarCamposPesquisa($this->view->parametros);
			
	    	//Executa pesquisa
	        $this->view->dados = $this->dao->pesquisar($this->view->parametros);
	        
	        //Qtd de registros
	        $this->view->totalResultados = count($this->view->dados);
		    
		    if ($this->view->totalResultados == 0) {
		       	throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
		    }
    		
    	} catch (Exception $e) {
    		
    		$this->view->mensagemAlerta = $e->getMessage();
    		
    	}
		
    	$this->view->status = TRUE;
		
        //Incluir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_envio_sms_retorno/index.php";
        
        return $this->view->dados;
    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * @throws Exception
     * @return array
     */
    public function gerarCSV(stdClass $filtros = null) {
    	
    	//Valida campos obrigatórios
    	$this->validarCamposPesquisa($filtros);
		
    	//Executa pesquisa
        $resultadoCSV = $this->dao->pesquisar($filtros);

        $caminho = '/var/www/docs_temporario/';
        $nome_arquivo = 'arquivo_retorno_SMS_'.date("Y_m_d").'.csv';
        $arquivo = false;
    	
    	if (file_exists($caminho)) {
    
    		//$this->arrayRelatorio = pg_fetch_all($resultadoCSV['resource']);
    		$this->countRelatorio = count($resultadoCSV);
    		
    		if ( $this->countRelatorio == 0 ){
    			throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
    		}
    		
    		ob_start();
    
    		// Gera CSV
    		$csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '' );
    
    		// Cabeçalho
    		$csvWriter->addLine(
    			array(
   					'Dt Agenda',
   					'Dt Envio SMS',
   					'Nº O.S.',
					'Tipo da O.S.',
   					'Cliente',
   					'Placa',
   					'Nº Celular',
   					'Cód. Cancelamento'
    			)
    		);
    
   			foreach($resultadoCSV as $dados){
   				// Corpo
   				$csvWriter->addLine(
    				array(
    					$dados->dt_agenda,
    					$dados->dt_envio,
    					$dados->ordoid,
						$dados->ostdescricao,
    					$dados->clinome,
    					$dados->veiplaca,
    					$dados->hsetelefone,
    					$dados->hsecodigo_retorno
    				)
    			);
   			}
    
    		$arquivo = $csvWriter->writeToFile( $caminho.$nome_arquivo );
    		ob_end_clean();
    	}
    	
    	if ($arquivo === false){
    		throw new Exception("Houve um erro ao gerar o arquivo.");
    	}
    	elseif ($this->countRelatorio > 0){
    		$this->view->mensagemSucesso = "Arquivo gerado com sucesso.";
    	}

    	return $nome_arquivo;
    }

}