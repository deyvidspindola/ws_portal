<?php

/*
 * require para persistência de dados - classe DAO 
 */
/*
 * require para persistência de dados - classe DAO 
 */
/*
 * require para persistência de dados - classe DAO 
 */
require_once(_MODULEDIR_ . 'Financas/DAO/FinGeraNfBoletoGraficaDAO.php');
require_once(_MODULEDIR_ . 'Financas/DAO/FinBoleto.php');
require_once _SITEDIR_."lib/Components/CsvWriter.php";
require_once _MODULEDIR_ ."/Financas/DAO/FinFaturamentoUnificadoDAO.php"; //STI 83807
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';
/**
 * @FinGeraNfBoletoGrafica.php
 * 
 * Classe para geração de arquivo com informções de nota fiscal e boleto referente a mensalidade do serviço de monitoramento e da locação do equipamento.
 * 
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @since 07/11/2012
 * @package Financas
 * 
 */
class FinGeraNfBoletoGrafica extends FinGeraNfBoletoGraficaAction {

    const MAX_CONNUMERO = 2147483647;
    const FILE_PATH = '/var/www/faturamento/arquivo_grafica/';
    const FILE_NAME_PREFIX = 'SERIEA-';
    const FILE_EXTENSION = 'xml';
    const MSG_VALIDATE_REFERENCIA = 'Informe a Data de Referência';
    const MSG_VALIDATE_TIPO = 'Informe o Tipo';
    const MSG_VALIDATE_MAX_CONNUMERO = 'O contrato não pode ser maior que ';
    const MSG_VALIDATE_MIN_CONNUMERO = 'O contrato deve ser maior que zero';
    const MSG_CONTRATO_INATIVO = 'Contrato inativo';
    const MSG_CONTRATO_NAO_EXISTE = 'Contrato não encontrado';
    const MSG_VEICULO_INATIVO = 'Veículo inativo';
    const MSG_CONFIRMA_GERACAO = 'Confirma a geração do arquivo?';
    const MSG_NENHUM_RESULTADO = 'Não há nota fiscal na data de referêcia informada';
    const MSG_NENHUMA_NF_SELECIONADA = 'Nenhuma NF selecionada';
    const MSG_FALHA_PESQUISA = 'Falha ao pesquisar notas';
    const MSG_FALHA_GERAR_ARQUIVO = 'Falha ao gerar arquivo';
    const MSG_SUCESSO = 'Arquivo gerado com sucesso';

    /**
     * Vo para o formulário de pesquisa
     * @property FinGeraNfBoletoGraficaVo
     */
    private $voPesquisa;
    private $notasIgnoradas;

    /**
     * @var FinGeraNfBoletoGraficaArquivoXml
     */
    private $file;

    /*
     * @var array $request
     * @return void
     */

    public function __construct($parametro) {

        parent::__construct($parametro);

      //  $this->voPesquisa = new FinGeraNfBoletoGraficaVo($this->request);

     //   $this->view->voPesquisa = $this->voPesquisa;
        $this->view->notas = array();
        $this->view->mensagensBoleto = array();
        $this->view->tiposContrato = array();
        $this->view->msgValidacao = ($this->request['msgValidacao']) ? $this->request['msgValidacao'] : '';

        try {
            $this->dao = new FinGeraNfBoletoGraficaDAO($this->conn);

            $this->view->mensagensBoleto = $this->dao->getMensagensBoleto();
            $this->view->tiposContrato = $this->dao->tiposContratosAtivos();

            $this->setViewConstantesMessages();
        } catch (Exception $e) {
            $this->view->msg = $e->getMessage();
        }
    }
    
    public function setRetornaView(){
    	return $this->view;
    }

    private function setViewConstantesMessages() {

        $reflect = new ReflectionClass(get_class($this));

        $constants = $reflect->getConstants();
        foreach ($constants as $constant => $value) {
            $this->view->$constant = $value;
        }
    }

    /**
     * Action da tela inicial de pesquisa
     * 
     * @return FinFaturamentoView
     */
    public function index() {

    	include (_MODULEDIR_ . 'Financas/View/Fin_Gera_Nf_Boleto_Grafica/index' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
       //return $this->view;
    }
    
    /**
     * Retorna os parametros do email data inicio e fim e o descricao do status
     * da tabela execucao_arquivo_grafica  , essa função vai ser chamada pelo cron.
     */
    public function retornoParametros(){
	
    	$paramentros = $this->dao->recuperarParametros(true);
    
    	while ($tipo = pg_fetch_object($paramentros)) {
    		$email = $tipo->usuemail;
    		$dataInicio = $tipo->data_inicio;
    		$dataTermino = $tipo->data_termino;
    		$status = $tipo->eardesc_status;
    		$param  = $tipo->earparametros;
    	}
    
    	return array(
    			"email"	=> $email,
    			"dataInicio"=>$dataInicio,
    			"dataTermino"=> $dataTermino,
    			"status" => $status
    	);
    
    }
    
    /**
     *  Metodo para retornar caminho da pasta no servidor
     *  @return String
     */
    public function RetornaCaminhoServidor(){
    	$res = $this->dao->getCaminhoServidor();
    	$PATH =  _SITEDIR_ .$res[1];
    	return $PATH;
    }
    
    /**
     *  Metodo para retornar informações de usuario, servidor, senha do ftp
     *  @return array
     */
    public function RetornaInformacoesFPT() {
    	 
    	$res = $this->dao->getInformacoesFPT();
    
    	return $res;
    }
    
    

    /**
     * Lista os arquivos e armazena no array para retorna para view
     * @return array
     */
    public function listaArquivos() {
    	$dir = $this->RetornaCaminhoServidor();
		
    	if($handles = opendir($dir)) {
    	
    		while(false !== ($entry = readdir($handles))) {
    		
    			if(empty($entry) || $entry == '..' || $entry == '.') continue;
    			
    			if($entry != "svn") {
    				$arquivos[]['nome'] = $entry;
    			}
    		}
    		closedir($handles);
    	}
    	
    	if(count($arquivos) > 0 || !empty($arquivos)) {
 		 rsort($arquivos);
    	}

 	 return $arquivos;

    }
    
    /**
     * Função para ver o tamanho do arquivo 
     * @return String
     */
	public function tamanhoArquivo($arquivo) {
		$tamanhoarquivo = filesize($arquivo);
	
		/* Medidas */
		$medidas = array('KB', 'MB', 'GB', 'TB');
		
		/* Se for menor que 1KB arredonda para 1KB */
		if($tamanhoarquivo < 999){
			$tamanhoarquivo = 1000;
		}
		
		for ($i = 0; $tamanhoarquivo > 999; $i++){
			$tamanhoarquivo /= 1024;
		}
		
		return round($tamanhoarquivo) ." ".$medidas[$i - 1];
	}
	
	/**
	 * Retorna os parametros do banco dos parametros passados da pesquisa
	 *
	 */
	public function Setarfiltro(){
	
		$res = pg_fetch_assoc($this->dao->recuperarParametros(false));
	
		$param = explode("|", $res['earparametros']);

		$datas = explode("/", $param[0]);
	
		$dataFormatada = $datas[2]."-".$datas[1]."-".$datas[0];
		
		$parametros =  array(
				"frm_data"=>$dataFormatada,
				"frm_doc"=>$param[1],
				"frm_tipo"=>$param[2],
				"frm_cliente"=>$param[3],
				"frm_tipo_contrato"=>$param[4],
				"frm_contrato"=>$param[5],
				"frm_placa"=>$param[6]
		);
	
		 
		$this->voPesquisa = new FinGeraNfBoletoGraficaVo($parametros);
		 
	
	}
	
	/**
	 * Metodo que chama o cron para envir arquivo  ftp
	 * @throws String
	 */
	public function prepararArquivoFTP(){
		$params = '';
		$res = $this->verificarProcesso(false);
		$nomeArquivo = $_POST['arquivo'];
		if($res['codigo'] == 0){
	
	
			try{
				$cd_usuario = $_SESSION['usuario']['oid'];
				$this->dao->prepararGeracaoNotaBoleto($cd_usuario,23,$params,$nomeArquivo);
				 
				if(!is_dir(_SITEDIR_."faturamento")) {
					if(!mkdir(_SITEDIR_."faturamento" , 0777)) {
						throw new Exception('Falha ao criar arquivo de log.');
						echo utf8_encode("Falha ao criar arquivo de log.");
					}
				}
	
				chmod(_SITEDIR_."faturamento",0777);
	
				if (!$handle = fopen(_SITEDIR_."faturamento/geracao_nota_boleto_grafica_previa", "w")) {
					throw new Exception('Falha ao criar arquivo de log.');
					echo utf8_encode("Falha ao criar arquivo de log.");
				}
					
				fputs($handle, "Arquivo FTP  de nota e boletos da gráfica Iniciado\r\n");
				fclose($handle);
	
				chmod(_SITEDIR_."faturamento/geracao_nota_boleto_grafica_previa",0777);
	
				passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/envia_arquivo_ftp_nf_boleto_grafica.php >> " . _SITEDIR_ . "faturamento/geracao_nota_boleto_grafica_previa 2>&1 &");
	
				$res = $this->verificarProcesso(false);
	
				echo utf8_encode($res['msg']);
			}catch (Exception $e) {
	
				$this->dao->finalizarProcesso($e->getMessage(),23);
				echo utf8_encode($e->getMessage());
			}
		}
	
	}
	
	/**
	 * Metodo que chama o cron para arquivo prévia
	 * @throws String
	 */
	public function prepararPrevia() {
		// Verifica concorrêcia entre processos
		$res = $this->verificarProcesso(false);
	
	
		$this->voPesquisa = new FinGeraNfBoletoGraficaVo($_POST);
		 
		if($res['codigo'] == 0){
			 
			try{
	
				$this->validarPesquisa();
	
				$params .= $this->voPesquisa->frm_data."|";
				$params .= $this->voPesquisa->frm_doc."|";
				$params .= $this->voPesquisa->frm_tipo."|";
				$params .= $this->voPesquisa->frm_cliente."|";
				$params .= $this->voPesquisa->frm_tipo_contrato."|";
				$params .= $this->voPesquisa->frm_contrato."|";
				$params .= $this->voPesquisa->frm_placa."|";
				$cd_usuario = $_SESSION['usuario']['oid'];
				$this->dao->prepararGeracaoNotaBoleto($cd_usuario,13,$params);
	
				if(!is_dir(_SITEDIR_."faturamento")) {
					if(!mkdir(_SITEDIR_."faturamento" , 0777)) {
						throw new Exception('Falha ao criar arquivo de log.');
						echo utf8_encode("Falha ao criar arquivo de log.");
					}
				}
	
				chmod(_SITEDIR_."faturamento",0777);
	
				if (!$handle = fopen(_SITEDIR_."faturamento/geracao_nota_boleto_grafica_previa", "w")) {
					throw new Exception('Falha ao criar arquivo de log.');
					echo utf8_encode("Falha ao criar arquivo de log.");
				}
					
				fputs($handle, "Prévia de nota e boletos da gráfica Iniciado\r\n");
				fclose($handle);
					
				chmod(_SITEDIR_."faturamento/geracao_nota_boleto_grafica_previa",0777);
	
				passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/previa_geracao_nf_boleto_grafica.php >> " . _SITEDIR_ . "faturamento/geracao_nota_boleto_grafica_previa 2>&1 &");
	
				$res = $this->verificarProcesso(false);
	
				echo utf8_encode($res['msg']);
	
	
	
			}catch (Exception $e) {
	
				$this->dao->finalizarProcesso($e->getMessage(),13);
	
				/*	return array(
				 "codigo"	=> 1,
						"msg"		=>  $e->getMessage(),
						"retorno"	=> array()
				);*/
				 
				echo utf8_encode($e->getMessage());
			}
		} else {
			echo utf8_encode($res['msg']);
		}
		 
		 
	}
	
	/**
	 * Metodo que chama o cron para arquivo gráfica
	 * @throws String
	 */
	public function prepararArquivo(){
	
		// Verifica concorrêcia entre processos
		$res = $this->verificarProcesso(false);
	
		if (count($_POST)) {
			$this->voPesquisa = new FinGeraNfBoletoGraficaVo($_POST);
	
			if($res['codigo'] == 0){
	
				try{
					 
					 
					$this->validarPesquisa();
					 
					$params .= $this->voPesquisa->frm_data."|";
					$params .= $this->voPesquisa->frm_doc."|";
					$params .= $this->voPesquisa->frm_tipo."|";
					$params .= $this->voPesquisa->frm_cliente."|";
					$params .= $this->voPesquisa->frm_tipo_contrato."|";
					$params .= $this->voPesquisa->frm_contrato."|";
					$params .= $this->voPesquisa->frm_placa."|";
					$cd_usuario = $_SESSION['usuario']['oid'];
	
					$this->dao->prepararGeracaoNotaBoleto($cd_usuario,14,$params);
					 
					if(!is_dir(_SITEDIR_."faturamento")) {
						if(!mkdir(_SITEDIR_."faturamento" , 0777)) {
							throw new Exception('Falha ao criar arquivo de log.');
							echo utf8_encode("Falha ao criar arquivo de log.");
						}
					}
					 
					chmod(_SITEDIR_."faturamento",0777);
					 
					if (!$handle = fopen(_SITEDIR_."faturamento/geracao_nota_boleto_grafica", "w")) {
						throw new Exception('Falha ao criar arquivo de log.');
						echo utf8_encode("Falha ao criar arquivo de log.");
					}
	
					fputs($handle, "Geração de nota e boletos da gráfica Iniciado\r\n");
					fclose($handle);
	
					chmod(_SITEDIR_."faturamento/geracao_nota_boleto_grafica",0777);
					 
					passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/geracao_nf_boleto_grafica.php >> " . _SITEDIR_ . "faturamento/geracao_nota_boleto_grafica 2>&1 &");
					 
					$res = $this->verificarProcesso(false);
					echo utf8_encode($res['msg']);
					 
					 
					 
				}catch (Exception $e) {
					 
					$this->dao->finalizarProcesso($e->getMessage(),14);
					 
					/*	return array(
					 "codigo"	=> 1,
							"msg"		=>  $e->getMessage(),
							"retorno"	=> array()
					);*/
	
					echo utf8_encode($e->getMessage());
				}
			} else {
				echo utf8_encode($res['msg']);
			}
		}else {
			echo "Erro ao enviar arquivo para gráfica";
		}
	}
	
    /**
     *  Metodo para chama o metodo gerarPrevia  quando finalizado retorna true ou false para o cron
     *
     */
    public function retornoPrevia(){
    		try {
				$retorno = false;		
				 
				$this->Setarfiltro();

			/*	$params .= $this->voPesquisa->frm_data."|";
				$params .= $this->voPesquisa->frm_doc."|";
				$params .= $this->voPesquisa->frm_tipo."|";
				$params .= $this->voPesquisa->frm_cliente."|";
				$params .= $this->voPesquisa->frm_tipo_contrato."|";
				$params .= $this->voPesquisa->frm_contrato."|";
				$params .= $this->voPesquisa->frm_placa."|";
				
				//$this->dao->prepararGeracaoNotaBoleto('P',$params);*/
				
				$res = $this->dao->recuperarParametros(false);
				
    			$notas = $this->dao->notasFiscaisPorDataReferencia($this->voPesquisa);
    		
    			$this->view->pathArquivoPrevia = "";
   

    			if (!count($notas) || count($notas) == 0) {
    				
    				$msg = self::MSG_NENHUM_RESULTADO;
    				$this->dao->finalizarProcesso($msg,13);
    				$retorno = true;
    				
    			}else{
    				$this->view->pathArquivoPrevia = $this->gerarPrevia($notas);
    				if (empty($this->view->pathArquivoPrevia)) {
    					$this->view->msg = "Falha ao gerar prévia de arquivo.";
    					$this->dao->finalizarProcesso("Falha ao gerar prévia de arquivo.",13);
    					$retorno = false;
    				} else {
    					$this->dao->finalizarProcesso("Prévia gerada com sucesso.",13);
    					$this->view->msg = "Prévia gerada com sucesso.";
    					$retorno = true;
    				}
    			}
    	
    			// aqui será gerado o arquivo de previa conforme o resultado.
    			//$arquivo_previa = $this->gerarPrevia($this->view->notas);
    	
    		} catch (ExceptionValidation $e) {
    			$this->dao->finalizarProcesso($e->getMessage(),13);
    			$this->view->msg = $e->getMessage();
    			$retorno = false;
    			
    		} catch (Exception $e) {
    			$msg = self::MSG_FALHA_PESQUISA;
    			$retorno = false;
    		}

    		return $retorno;
    }
    
    /**
     *  Metodo para chama o metodo gerar arquivo quando finalizado retorna true ou false para o cron
     *
     */
    public function retornoGrafica(){
    	try {
    		$retornoGerar = false;
    		 
    		$this->view->pathArquivoGerado = "";
    		 
    		$this->Setarfiltro();
    
    		$quantidadeNotas = $this->dao->contarNotas($this->voPesquisa);
  
    		if (!count($quantidadeNotas) || count($quantidadeNotas) == 0) {
    			$msg = self::MSG_NENHUM_RESULTADO;
    			$this->dao->finalizarProcesso($msg,14);
    			$retornoGerar = true;
    		
    		}else{

	    		$retorno = $this->gerar($quantidadeNotas);
	    
	    		if (!$retorno) {
	    			$this->view->msg = "Falha ao gerar arquivo da gráfica.";
	    			$this->dao->finalizarProcesso("Falha ao gerar arquivo para gráfica.",14);
	    			$retornoGerar = false;
	    	
	    		} else {
	    			//$this->dao->finalizarProcesso("Prévia gerada com sucesso.");
	    			$this->dao->finalizarProcesso("Arquivo gerado com sucesso.",14);
	    			$retornoGerar = true;
	    	
	    		}
    		}
    
    	} catch (ExceptionValidation $e) {
    		$this->dao->finalizarProcesso($e->getMessage(),14);
    		$this->view->msg = $e->getMessage();
    		$retornoGerar = false;
  
    	} catch (Exception $e) {
    		 
    		$msg = self::MSG_FALHA_PESQUISA;
    		$this->dao->finalizarProcesso($msg,14);
    		$retornoGerar = false;
  
    	}
    	
    	return $retornoGerar;
    
    }
    
    /**
     *  Metodo para excluir o  arquivo no servidor
     *  
     */
    public function excluir(){

    	$caminho = $this->RetornaCaminhoServidor();
    	$nome_arquivo = $_POST['arquivo'];
    	// Apaga os arquivos .xml e deixa apenas o ZIP na pasta
    	if (file_exists($caminho . $pathSig)) {
    		if (is_file($caminho . $nome_arquivo)) {
	    		if(unlink($caminho . $nome_arquivo)){
	    			echo "O Arquivo $nome_arquivo foi excluido com sucesso";
	    		}
    		}else {
    			echo "Acesso negado - Não pode excluir o arquivo!";
    		}
    	}else {
    		echo "O arquivo $nome_arquivo não existe";
    	}
    }

    /**
     * Cria arquivo zip
     * @param  [string] $caminho [caminho do diretorio]
     * @param  [string] $arquivo [nome do arquivo]
     * @return [boolean/string]          [string(caminho)/false(erro)]
     */
    public function criaArquivoZip($caminho,$arquivo) {

    	$zip = new ZipArchive();

    	$nomeArquivo = str_replace('.xml','',$arquivo);

        if($zip->open($caminho.$nomeArquivo.'.zip', ZIPARCHIVE::CREATE)) {

        	$flagArquivoAdicionado = $zip->addFile($caminho.$arquivo,$arquivo);

        	$zip->close();

        	if($flagArquivoAdicionado) {
        		return $caminho.$nomeArquivo.'.zip';
        	}
        }
        
        return false;
    }

    /**
     *  Metodo para enviar arquivo via ftp para gráfica 
     *  @return true ou false
     */
    public function EnviarArquivoFTP(){
   
    	
    	$caminho = $this->RetornaCaminhoServidor();
    	
    	$infFtp = $this->RetornaInformacoesFPT();
    	
    	ksort($infFtp);

    	$servidor = $infFtp[0];
    	$usuario = $infFtp[1];
     	$senha = $infFtp[2];
     	$sascar = $infFtp[3];
     	$siggo = $infFtp[4];
     	
     	$resposta = true;
     	$retorno = false;

     	
     	$res = pg_fetch_assoc($this->dao->recuperarParametros(false));
     	
     	$param = $res['earnomearquivo'];
     	
    	$conecta = ftp_connect($servidor,21);

    	if(!$conecta){ 
    		$this->dao->finalizarProcesso("Erro ao conectar com o servidor FTP",23);
    		$retorno =  false;
    		die('Erro ao conectar com o servidor');}
  
    	/* Autenticar no servidor */
    	$login = ftp_login($conecta,$usuario,$senha);
    	
    	if(!$login){ 
    		finalizarProcesso("Erro ao autenticar com o servidor FTP",23);
    		$retorno =  false;
    		die('Erro ao autenticar');
    	}
    	
    	ftp_pasv($conecta, TRUE);
    	// get contents of the current directory
    	$Diretorio=ftp_pwd($ftp); //Devolve rota atual p.e. "/home/willy"
    
    	if (class_exists('ZipArchive')) {
    		$zip = new ZipArchive();
		
    		//$nome_arquivo = $_POST['arquivo'];
    		// recebe o nome do arquivo zip enviado via ajax
    		$nome_arquivo = $param;
    		
    	//	echo "O nome do arquivo é " .$nome_arquivo;
    		
    	//	$this->dao->finalizarProcesso("Arquivo finalizado com sucesso",23);
    		
    	//	return true;
    
    		//recebe o caminho dos arquivos no servidor
    		
    	
    		//abre o arquivo zip passando o caminho e o nome
    		$zip->open($caminho.$nome_arquivo);
    		
    		// Listando os nomes dos elementos
    		for ($i = 0; $i < $zip->numFiles; $i++) {
    		
    			// Obtendo informacoes do indice $i
    			$stat = $zip->statIndex($i);
    		
    			// Obtendo apenas o nome do indice $i
    			$nome = $zip->getNameIndex($i);
  
    			//faz o substr para verificar se o começo do nome do arquivo tem sascar e o else para verificar se tem siggo
    			if(substr($nome,0,6) == "SASCAR") {
    				$nomeSascar = $nome;
    			}else if(substr($nome,0,5) == "SIGGO"){
    				$nomeSiggo = $nome;
    			}else {
    				unlink($caminho . $nome);
    				$resposta = false;
    			}

    		}
    		
    		//extrai o arquivo passando o caminho aonde vai ter um retorno true ou false
    		if($zip->extractTo($caminho)== TRUE){
    			
    			//verifica se a variavel sascar foi inicia 
    			if(isset($nomeSascar) || !empty($nomeSascar)) {
    				
    				// Define variáveis para o envio de arquivo
    				$local_arquivo = $caminho.$nomeSascar; // Localização (local)
    				$ftp_pasta = $sascar."/"; // Pasta (externa)
    				$ftp_arquivo = $nomeSascar; // Nome do arquivo (externo)

    				// FASTTRACK MANTIS 6646
    				$local_arquivo = $this->criaArquivoZip($caminho,$nomeSascar);

    				unlink($caminho . $nomeSascar);

    				// Retorno: true / false
    				if($local_arquivo) {

    					$arquivoZip = str_replace('.xml','.zip',$ftp_arquivo);

    					$envio = ftp_put($conecta, $Diretorio.$ftp_pasta.$arquivoZip, $local_arquivo, FTP_ASCII); 

	    				if($envio){
	    					$resposta = true;
	    				}else{
							$resposta = false;
	    				}

	    				unlink($local_arquivo);
    				}

    				
    				
    			}
    			
    			//verifica se a nomeSiggo sascar foi inicia
    		
    			if(isset($nomeSiggo) || !empty($nomeSiggo)) {
 
    		
    				if($resposta) {
    					$local_arquivo = $caminho.$nomeSiggo; // Localização (local)
    					$ftp_pasta = $siggo."/"; // Pasta (externa)
    					$ftp_arquivo = $nomeSiggo; // Nome do arquivo (externo)
    					
    					// FASTTRACK MANTIS 6646
    					$local_arquivo = $this->criaArquivoZip($caminho,$nomeSiggo);

    					unlink($caminho . $nomeSiggo);

    					if($local_arquivo) {
    						$arquivoZip = str_replace('.xml','.zip',$ftp_arquivo);

    						$enviar = ftp_put($conecta, $Diretorio.$ftp_pasta.$arquivoZip, $local_arquivo, FTP_ASCII);
    					
	    					if($enviar){
	    						$resposta = true;
	    					}else{
	    						$resposta = false;
	    					}

	    					unlink($local_arquivo);
    					}

    				}
    			   
    			}
    			
    
    			
    			if($resposta) {
    				$this->dao->finalizarProcesso("Arquivos enviados para o ftp com sucesso",23);
    				$retorno =  true;
    			}else{
    				$this->dao->finalizarProcesso("Problemas para enviar arquivo para o ftp",23);
    				$retorno = false;
    			}
    		}else{
    			$this->dao->finalizarProcesso("Problemas para extrair  arquivos para enviar paro o ftp",23);
    			$retorno = false;
    		}
    		//fecha o zip
    		$zip->close();
    	}else {
    		$this->dao->finalizarProcesso("Problemas para ler o arquivo zip para envio do ftp: Não foi possivel encontrar a classe ZipArchive",23);
    		$retorno = false;
    	}

  
    	ftp_close($conecta);

    	return $retorno;
    }
    

 /**
 * Trata os parametros do POST/GET. Preenche um objeto com os parametros
 * do POST e/ou GET.
 *
 * @return stdClass Parametros tradados
 *
 * @retrun stdClass
 */
 public function tratarParametros(){
 	$retorno = new stdClass();
 	
 	if(count($_POST) > 0 ) {
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
     * Método verificarProcesso
     * Verifica se já tem alguma processo rodando de gerar previa ou gerar aquivo
     * @param bolean $finalizado
     */
    public function verificarProcesso($finalizado){
    	
    	try {
    	
    		// Verifica concorrÃªncia entre processos
    		$res = $this->dao->recuperarParametros($finalizado);
    		
    		if(pg_num_rows($res) > 0){
    			
    			$param = pg_fetch_assoc($res);
    			
    			if($param['eartipo_processo'] != 23) {
    				
    				$msg = "Preparação de Arquivo para gráfica foi iniciado => ".$param['nm_usuario']." ás ".$param['data_inicio'];
    			}else{
    				$msg = "Enviando Arquivo FTP para gráfica foi iniciado => ".$param['nm_usuario']." ás ".$param['data_inicio'];
    			}
    			return array(
    					"codigo"	=> 2,
    					"msg"		=>	$msg,
    					"retorno"	=>	$param
    			);
    		}else {
    			
    			return array(
    					"codigo"	=> 0,
    					"msg"		=>	'',
    					"retorno"	=>	$param
    			);
    		}
    		
    		
    	}catch(Exception $e){
    		
    		return array(
    			"codigo" => 1,
    			"msg" => "Falha ao verificar concorrência. Tente novamente.",
    			"retorno" => array()
    		);	 
    	}
    }

    /**
     * Método gerarPrevia()
     * Processa/Cria arquivo de prévia e retorna o caminho.
     * 
     * @param stdClass $dados
     * @return string $arquivo_previa
     */
    private function gerarPrevia($dados) {

       
        $caminho = $this->RetornaCaminhoServidor();

        //Nome do arquivo
        $nome_arquivo = 'PREVIA_NF_' . date('d-m-Y') . '.csv';

        if (is_file($caminho . $nome_arquivo)) {
            unlink($caminho . $nome_arquivo);
        }

        if (file_exists($caminho)) {

            //cria o arquivo CSV
            $csvWriter = new CsvWriter($caminho . $nome_arquivo, ';', '', true);

            //Seta os cabeÃ§alhos
            $cabecalho = array(
                "ID Cliente",
                "Cliente",
                "Nota Fiscal",
                "Valor",
                "Vcto",
                "Forma Cobrança",
                "CEP"
            );

            //Adiciona o Cabeçalho
            $csvWriter->addLine($cabecalho);

            foreach ($dados as $dado) {

                $clienteId = trim($dado->clioid) != '' ? trim($dado->clioid) : '';

                $cliente = !empty($dado->clinome) ? trim($dado->clinome) : '';

                //dados para montar o valor de nota fiscal
                $nNotaFiscal = trim($dado->nflno_numero) != '' ? trim($dado->nflno_numero) : '';
                $sNotaFiscal = trim($dado->nflserie) != '' ? trim($dado->nflserie) : '';

                $notaFiscal = $nNotaFiscal . ' ' . $sNotaFiscal;

                $valor = trim($dado->titvl_titulo) != '' ? number_format($dado->titvl_titulo, 2, ',', '.') : '';

                $vencimento = !empty($dado->titdt_vencimento) ? date('d/m/Y', strtotime($dado->titdt_vencimento)) : '';

                $formaCobranca = !empty($dado->forcnome) ? trim($dado->forcnome) : '';

                $cep = trim($dado->clicep_com) != '' ? FinGeraNfBoletoGraficaUtil::applyMask($dado->clicep_com, '99999-999') : '';

                // Corpo do CSV
                $csvWriter->addLine(
                        array(
                            $clienteId,
                            $cliente,
                            $notaFiscal,
                            $valor,
                            $vencimento,
                            $formaCobranca,
                            $cep
                        )
                );
            }
        }

        $arquivo_previa = "";
        if (is_file($caminho . $nome_arquivo)) {
            $arquivo_previa = $caminho . $nome_arquivo;
        }

        return $arquivo_previa;
    }

    /**
     * Validação do formulário de pesquisa
     *
     * @return void
     */
    private function validarPesquisa() {
        if (empty($this->voPesquisa->frm_data)) {
            throw new ExceptionValidation(self::MSG_VALIDATE_REFERENCIA);
        }

        if (empty($this->voPesquisa->frm_tipo)) {
            throw new ExceptionValidation(self::MSG_VALIDATE_TIPO);
        }

        if (!empty($this->voPesquisa->frm_contrato) and $this->dao->isContratoNaoExiste($this->voPesquisa->frm_contrato)) {
            throw new ExceptionValidation(self::MSG_CONTRATO_NAO_EXISTE);
        }

        if (!empty($this->voPesquisa->frm_contrato) and $this->dao->isContratoInativo($this->voPesquisa->frm_contrato)) {
            throw new ExceptionValidation(self::MSG_CONTRATO_INATIVO);
        }

        if (!empty($this->voPesquisa->frm_placa) and $this->dao->isVeiculoInativo($this->voPesquisa->frm_placa)) {
            throw new ExceptionValidation(self::MSG_VEICULO_INATIVO);
        }
    }

   
    
    /**
     * Action da geraçao de arquivo
     * 
     * @return FinFaturamentoView
     */
    public function gerar($quantidadeNotas) {

    		$retornoGrafica = true;
    		$caminho = $this->RetornaCaminhoServidor();
    		
            $this->dao->transactionBegin();
           
            try {
            	
           /*	$teste =  array(
            			"frm_data"	=> '01/09/2014',
            			"frm_doc"=>'',
            			"frm_cliente"=> '',
            			"frm_tipo_contrato" => '',
            			"frm_contrato" => '',
            			"frm_placa" => ''
            	);
            	
            	$this->voPesquisa = new FinGeraNfBoletoGraficaVo($teste);
            	
            	$params .= $this->voPesquisa->frm_data."|";
            	$params .= $this->voPesquisa->frm_doc."|";
            	$params .= $this->voPesquisa->frm_tipo."|";
            	$params .= $this->voPesquisa->frm_cliente."|";
            	$params .= $this->voPesquisa->frm_tipo_contrato."|";
            	$params .= $this->voPesquisa->frm_contrato."|";
            	$params .= $this->voPesquisa->frm_placa."|";
            	 
            	$this->dao->prepararGeracaoNotaBoleto(14,$params);*/
            	$this->Setarfiltro();
                $this->validarGerar();
            	
                // Calcula imposto (lei da transparência) - STI 83807
            /*    $notasFiscaisImposto = $this->dao->notasFiscaisCalculoImposto($this->voPesquisa);

               foreach($notasFiscaisImposto as $key => $notaFiscal) {
                    FinFaturamentoUnificadoDAO::calcularImposto($notaFiscal->nfloid, $this->conn);
                }*/

                $rs = $this->dao->itensNotasFiscaisPorDataReferencia($this->voPesquisa);

                /**
                 * INICIO
                 */
                $numero_ultima_nota = 0;
                $ultimo_item_escrito = 0;
                $nota_validada = 0;
                $arquivoSascar = false;
                $arquivoVarejo = false;
                $xmlRowSascar = 0;
                $xmlRowVarejo = 0;
                
               
                //Notas a serem editadas
                $notasAtualizarData = array();
                

                while ($row = pg_fetch_object($rs)) {

                    $numero_nota_atual = $row->nflno_numero;

                    if ($numero_nota_atual != $numero_ultima_nota) {
                        
                        // Verifica Ãºltimo item escrito
                        if ($ultimo_item_escrito == 'Sig' && $nota_validada) {
                            
                            // Fechamento de Nota Fiscal (Quando Ãºltima nota Ã© diferente da nota atual)
                            fwrite($handleSiggo, '        </arr>');
                            fwrite($handleSiggo, '    </row>');
                        } else if ($ultimo_item_escrito == 'Sas' && $nota_validada) {
                            
                            fwrite($handleSascar, '        </arr>');
                            fwrite($handleSascar, '    </row>');
                        }
                        
                        $msgBoletoInserida = false;

                        $mensagemNum = 1;
                        foreach ($this->voPesquisa->frm_mensagens as $mensagem) {
                            $mensagemVar = 'Mensagem' . $mensagemNum;
                            $mensagens[$mensagemVar] = $mensagem;

                            $mensagemNum++;
                        }
                        
                        $resultadoValidacao = $this->dao->verificarNota($row);

                        if ($resultadoValidacao['itemOK']) {
                            
                            //Adiciona a nota para atualizada
                            if (count($notasAtualizarData) == 1000){
                                $this->dao->atualizarStatusArquivoGerado($notasAtualizarData);
                                $notasAtualizarData = array();
                            }
                            $notasAtualizarData[] = $row->nfloid;
                            
                            
                            $nota_validada = 1;
                            
                            $voBoleto = new stdClass();

                            $voBoleto->nossoNumero      = $resultadoValidacao['item']->nosso_numero;
                            $voBoleto->linhaDigitavel   = $resultadoValidacao['item']->linha_digitavel;
                            $voBoleto->codigoBarras     = $resultadoValidacao['item']->codigo_barras;
                                                                                   
                            $voBoleto->nossoNumeroDv = '';
                            
                            $arrayOutrasCobrancas = array(2, 3, 4, 7, 8, 10, 12, 13);
                            $arrayDebitoAutomatico = array(2, 3, 4, 12, 13);
                            $arrayCartaoCredito = array(7, 8, 10, 24, 25, 26, 27);
                            
                            global $autenticacaoSistemaUsuarios;
                            
                            if ($resultadoValidacao['item']->banco == '341' && $autenticacaoSistemaUsuarios == "PUBLIC" && !in_array($resultadoValidacao['item']->dados_forma_cobranca, $arrayOutrasCobrancas)) {
                            	$resultadoValidacao['item']->cfbagencia = '2938';
                            	$resultadoValidacao['item']->cfbconta_corrente = '11459';
                            } elseif ($resultadoValidacao['item']->banco == '341' && $autenticacaoSistemaUsuarios == "SBTEC" && !in_array($resultadoValidacao['item']->dados_forma_cobranca, $arrayOutrasCobrancas)) {
                            	$resultadoValidacao['item']->cfbagencia = '2938';
                            	$resultadoValidacao['item']->cfbconta_corrente = '13436';
                            } elseif ($resultadoValidacao['item']->banco == '341' && $autenticacaoSistemaUsuarios == "PUBLIC" && in_array($resultadoValidacao['item']->dados_forma_cobranca, $arrayDebitoAutomatico)) {
                            	$resultadoValidacao['item']->cfbagencia = '2938';
                            	$resultadoValidacao['item']->cfbconta_corrente = '11459';
                            } elseif ($resultadoValidacao['item']->banco == '341' && $autenticacaoSistemaUsuarios == "SBTEC" && in_array($resultadoValidacao['item']->dados_forma_cobranca, $arrayDebitoAutomatico)) {
                            	$resultadoValidacao['item']->cfbagencia = '2938';
                            	$resultadoValidacao['item']->cfbconta_corrente = '13436';
                            } elseif ($resultadoValidacao['item']->banco == '341' && $autenticacaoSistemaUsuarios == "PUBLIC" && in_array($resultadoValidacao['item']->dados_forma_cobranca, $arrayCartaoCredito)) {
                            	$resultadoValidacao['item']->cfbagencia = '2938';
                            	$resultadoValidacao['item']->cfbconta_corrente = '11459';
                            }
                            
                            if (!is_null($resultadoValidacao['item']->banco) && $resultadoValidacao['item']->banco == '341' && !is_null($voBoleto->nossoNumero)) {
                            	$voBoleto->nossoNumeroDv = '109' . '/' . $resultadoValidacao['item']->nosso_numero . '-' . modulo_10($resultadoValidacao['item']->cfbagencia . $resultadoValidacao['item']->cfbconta_corrente . '109' . $resultadoValidacao['item']->nosso_numero);
                            } else if(!is_null($voBoleto->nossoNumero)){
                            	$voBoleto->nossoNumeroDv = str_pad( $resultadoValidacao['item']->nosso_numero, 13, '0', STR_PAD_LEFT);
                            }
                           
                            if (strlen($row->obrobs_boleto) > 0 && !$msgBoletoInserida) {
                                $mensagemVar = 'Mensagem' . $mensagemNum;
                                $mensagens[$mensagemVar] = $row->obrobs_boleto;
                                $msgBoletoInserida = true;
                            }
                            
                            // Verificar se Ã© Varejo ou Sascar
                            if($row->tppoid == 12 || $row->tppoid_supertipo == 12) {
                                
                                $tipoVarejo = strtoupper($this->dao->nomeParametrizacao('NOVAMARCAVAREJO'));
                                
                                if (!$arquivoVarejo) {
                                    
                                    // Abre o arquivo
                                    $pathSig = $caminho . $tipoVarejo.'-' . self::FILE_NAME_PREFIX . date('Y-m-dHis') . '.' . self::FILE_EXTENSION;
                                    $handleSiggo = fopen($pathSig, 'a');
                                   
                                    
                                    // Escreve o cabeÃ§alho
                                    fwrite($handleSiggo, '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
                                    fwrite($handleSiggo, '<job cliente="dd" template="dd" dataGeracao="' . date('Y-m-d') . '" numRows="' . $quantidadeNotas['VAREJO'] . '">'."\n"); // pendente nÃºmero de linhas de notas dentro do arquivo
                                    
                                    $arquivoVarejo = true;
                                }
                                
                                $xmlRowVarejo++;
                                $meses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

                                $time = strtotime($row->nfldt_referencia);
                                $ano = date('y', $time);
                                $mes = (int) date('m', $time);
                                //$mes = utf8_encode(strtoupper($meses[$mes]));
                                $mes = strtoupper($meses[$mes]);

                                $referencia = $mes . '/' . $ano;

								                //busca mensagens do boleto
								                $mensagensBoleto = $this->dao->getMensagensBoleto(intval($row->titformacobranca));

								                // $mensagensBoleto = explode('</BR>', $mensagensBoleto[0]);
								                
								                $mensagens = array();
								               
								                $mensagens['Mensagem1'] = isset($mensagensBoleto[0]) ? $mensagensBoleto[0] : '';
								                $mensagens['Mensagem2'] = isset($mensagensBoleto[1]) ? $mensagensBoleto[1] : '';
								                $mensagens['Mensagem3'] = isset($mensagensBoleto[2]) ? $mensagensBoleto[2] : '';
								                $mensagens['Mensagem4'] = isset($mensagensBoleto[3]) ? $mensagensBoleto[3] : '';
								                $mensagens['Mensagem5'] = 'Parc.:1';
								                $mensagens['Mensagem6'] = 'MONITORAMENTO-A cobrança é antecipada e o vencimento é sempre dia 16 de cada mês. Ex.: você paga dia 16 de outubro a  monitoramento referen-';
								                $mensagens['Mensagem7'] = ' te ao período de 01/10 a 30/10. Mas atenção, no 1º monitoramento pode haver cobrança de Pro-rata, dependendo da data de instalação do SASCAR.';
								                $mensagens['Mensagem8'] = 'PRO-RATA-Cobrança dos dias do mês anterior ao mês atual cobrado, ou seja, dos dias referente ao mês de instalação. Ex. foi instalado  dia';
								                $mensagens['Mensagem9'] = ' 21/09/02, é cobrado os dias de monitoramento 21/09 a 30/09/02, mais o mês de outubro/02. (10 dias + mensalidade)';
								                $mensagens['Mensagem10'] = 'ROAMING-Deslocamento de um telefone celular, para fora da área de cobertura da operadora do celular, instalado no veículo. O deslocamento';
								                $mensagens['Mensagem11'] = ' é cobrado pela operadora e repassado ao cliente SASCAR, conforme previsto na cláusula 4 do contrato em vigor.';


                                // susbstituir texto mensagem	        			
                                $mensagens['Mensagem7'] = str_replace(' SASCAR', ' ' . $tipoVarejo, $mensagens['Mensagem7']);
                                $mensagens['Mensagem11'] = str_replace(' SASCAR', ' ' . $tipoVarejo, $mensagens['Mensagem11']);

                                fwrite($handleSiggo, '        <row id="' . $xmlRowVarejo . '">'."\n");
                                fwrite($handleSiggo, '        <int name="NumeroNF">' . $row->nflno_numero . '</int>'."\n");
                                fwrite($handleSiggo, '        <str name="NomeSacado">' . $row->clinome . '</str>'."\n");
                                if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSiggo, '    <str name="Endereco">' . $row->log_fiscal . ', '.$row->num_fiscal.' </str>'."\n");
                                }else{
                                	fwrite($handleSiggo, '    <str name="Endereco">' . $row->log_cor . '</str>'."\n");
                                }
                               
                               if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSiggo, '    <str name="Bairro">' . $row->bairro_fiscal .'</str>'."\n");
                                }else{
                                	fwrite($handleSiggo, '    <str name="Bairro">' . $row->bairro_cor . '</str>'."\n");
                                }
                                if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSiggo, '    <str name="Cidade">' . $row->cidade_fiscal .'</str>'."\n");
                                }else{
                                	fwrite($handleSiggo, '    <str name="Cidade">' . $row->cidade_cor . '</str>'."\n");
                                }
                                if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSiggo, '    <str name="CEP">' . trim($row->cep_fiscal).'</str>'."\n");
                                }else{
                                	fwrite($handleSiggo, '    <str name="CEP">' .trim($row->cep_cor).'</str>'."\n");
                                }
                                fwrite($handleSiggo, '        <str name="Endereco_NF">' . $row->log_fiscal . ', '.$row->num_fiscal.' </str>'."\n");
                                fwrite($handleSiggo, '        <str name="Bairro_NF">' . $row->bairro_fiscal . '</str>'."\n"); 
                                fwrite($handleSiggo, '        <str name="Cidade_NF">' . $row->cidade_fiscal . '</str>'."\n");
                                fwrite($handleSiggo, '        <str name="CEP_NF">' . $row->cep_fiscal . '</str>'."\n");
                                fwrite($handleSiggo, '        <str name="CNPJ">' . $row->clino_doc . '</str>'."\n");
                                fwrite($handleSiggo, '        <int name="NumeroContrato"></int>'."\n");
                                fwrite($handleSiggo, '        <str name="Referencia">' . $referencia . '</str>'."\n");
                                fwrite($handleSiggo, '        <date name="DataEmissao">' . $row->nfldt_emissao . '</date>'."\n");
                                fwrite($handleSiggo, '        <str name="Impressao">ANALITICO</str>'."\n");
                                fwrite($handleSiggo, '        <str name="EnviaCliente">SIM</str>'."\n");
                                fwrite($handleSiggo, '        <date name="Vencimento">' . $row->titdt_vencimento . '</date>'."\n");
                                fwrite($handleSiggo, '        <str name="NossoNumero">' . $voBoleto->nossoNumeroDv . '</str>'."\n");
                                fwrite($handleSiggo, '        <str name="NumeroDocumento">' . $row->titoid . '</str>'."\n");
                                fwrite($handleSiggo, '        <dec name="ValorPagar">' . $row->titvl_titulo . '</dec>'."\n");
                                fwrite($handleSiggo, '        <int name="TipoMensagem">0</int>'."\n");
                                fwrite($handleSiggo, '        <dec name="ValorFatura">' . $row->titvl_titulo . '</dec>'."\n");
                                fwrite($handleSiggo, '        <dec name="ValorTitulo">' . $row->titvl_titulo . '</dec>'."\n");
                                fwrite($handleSiggo, '        <dec name="ValorDesconto">' . $row->nflvl_desconto . '</dec>'."\n");
                                fwrite($handleSiggo, '        <dec name="ValorDescontoNF">' . $row->nflvl_desconto . '</dec>'."\n");
                                fwrite($handleSiggo, '        <dec name="ValorOutrasDeducoes">0</dec>'."\n");
                                fwrite($handleSiggo, '        <str name="InformacaoImposto">Valor Aproximado dos Tributos: R$' . number_format($row->nflvlr_imposto, 2, ',', '.') . ' (' . number_format($row->nflaliquota_imposto, 2, ',', '.') . '%) Fonte: IBPT</str>'."\n"); //STI 83807
                                fwrite($handleSiggo, '        <str name="Boleto">SIM</str>'."\n");
                                fwrite($handleSiggo, '        <str name="LinhaDigitavel">' . $voBoleto->linhaDigitavel . '</str>'."\n");
                                fwrite($handleSiggo, '        <str name="CodigoBarras">' . $voBoleto->codigoBarras . '</str>'."\n");
                                fwrite($handleSiggo, '        <str name="TipoEmpenho">N</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Empenho"></str>'."\n");
                                fwrite($handleSiggo, '        <str name="FormaPagamento">'.$resultadoValidacao['item']->dados_forma_cobranca.'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem1">'.$mensagens['Mensagem1'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem2">'.$mensagens['Mensagem2'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem3">'.$mensagens['Mensagem3'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem4">'.$mensagens['Mensagem4'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem5">'.$mensagens['Mensagem5'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem6">'.$mensagens['Mensagem6'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem7">'.$mensagens['Mensagem7'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem8">'.$mensagens['Mensagem8'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem9">'.$mensagens['Mensagem9'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem10">'.$mensagens['Mensagem10'].'</str>'."\n");
                                fwrite($handleSiggo, '        <str name="Mensagem11">'.$mensagens['Mensagem11'].'</str>'."\n");
                                
                                // Abertura de novo Item
                                fwrite($handleSiggo, '        <arr name="historico">'."\n");

                                // Item
                                fwrite($handleSiggo, '            <row>'."\n");
                                fwrite($handleSiggo, '                <str name="Qtde">1</str>'."\n");
                                fwrite($handleSiggo, '                <str name="Placa">'.$row->veiplaca.'</str>'."\n");
                                fwrite($handleSiggo, '                <str name="Descricao">'.$row->obrobrigacao.'</str>'."\n");
                                fwrite($handleSiggo, '                <dec name="Valor">'.$row->nfivl_item.'</dec>'."\n");
                                fwrite($handleSiggo, '            </row>'."\n");
                                
                                $ultimo_item_escrito = 'Sig';
                                
                            } else {
                                if (!$arquivoSascar) {
         
                                    // Abre o arquivo
                            	  $pathSas =$caminho . 'SASCAR-' . self::FILE_NAME_PREFIX . date('Y-m-dHis') . '.' . self::FILE_EXTENSION;
                               
                                    $handleSascar = fopen($pathSas, 'a');
                                    
                                    // Escreve o cabeçalho
                                    fwrite($handleSascar, '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
                                    fwrite($handleSascar, '<job cliente="dd" template="dd" dataGeracao="' . date('Y-m-d') . '" numRows="' . $quantidadeNotas['SASCAR'] . '">'."\n"); // pendente nÃºmero de linhas de notas dentro do arquivo
                                    
                                    $arquivoSascar = true;
                                }
                                $xmlRowSascar++;
                                $meses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

                                $time = strtotime($row->nfldt_referencia);
                                $ano = date('y', $time);
                                $mes = (int) date('m', $time);
                                //$mes = utf8_encode(strtoupper($meses[$mes]));
                                $mes = strtoupper($meses[$mes]);

                                $referencia = $mes . '/' . $ano;


								                //busca mensagens do boleto
								                $mensagensBoleto = $this->dao->getMensagensBoleto(intval($row->titformacobranca));

								                // $mensagensBoleto = explode('</BR>', $mensagensBoleto[0]);
								                
								                $mensagens = array();
								               
								                $mensagens['Mensagem1'] = isset($mensagensBoleto[0]) ? $mensagensBoleto[0] : '';
								                $mensagens['Mensagem2'] = isset($mensagensBoleto[1]) ? $mensagensBoleto[1] : '';
								                $mensagens['Mensagem3'] = isset($mensagensBoleto[2]) ? $mensagensBoleto[2] : '';
								                $mensagens['Mensagem4'] = isset($mensagensBoleto[3]) ? $mensagensBoleto[3] : '';
								                $mensagens['Mensagem5'] = 'Parc.:1';
								                $mensagens['Mensagem6'] = 'MONITORAMENTO-A cobrança é antecipada e o vencimento é sempre dia 16 de cada mês. Ex.: você paga dia 16 de outubro a  monitoramento referen-';
								                $mensagens['Mensagem7'] = ' te ao período de 01/10 a 30/10. Mas atenção, no 1º monitoramento pode haver cobrança de Pro-rata, dependendo da data de instalação do SASCAR.';
								                $mensagens['Mensagem8'] = 'PRO-RATA-Cobrança dos dias do mês anterior ao mês atual cobrado, ou seja, dos dias referente ao mês de instalação. Ex. foi instalado  dia';
								                $mensagens['Mensagem9'] = ' 21/09/02, é cobrado os dias de monitoramento 21/09 a 30/09/02, mais o mês de outubro/02. (10 dias + mensalidade)';
								                $mensagens['Mensagem10'] = 'ROAMING-Deslocamento de um telefone celular, para fora da área de cobertura da operadora do celular, instalado no veículo. O deslocamento';
								                $mensagens['Mensagem11'] = ' é cobrado pela operadora e repassado ao cliente SASCAR, conforme previsto na cláusula 4 do contrato em vigor.';


                                // susbstituir texto mensagem	        			
                                $mensagens['Mensagem7'] = str_replace(' SASCAR', ' ' . $tipoVarejo, $mensagens['Mensagem7']);
                                $mensagens['Mensagem11'] = str_replace(' SASCAR', ' ' . $tipoVarejo, $mensagens['Mensagem11']);

                                fwrite($handleSascar, '    <row id="' . $xmlRowSascar . '">'."\n");
                                fwrite($handleSascar, '        <int name="NumeroNF">' . $row->nflno_numero . '</int>'."\n"); 
                                fwrite($handleSascar, '        <str name="NomeSacado">' . $row->clinome . '</str>'."\n");
                                    if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSascar, '    <str name="Endereco">' . $row->log_fiscal . ', '.$row->num_fiscal.' </str>'."\n");
                                }else{
                                	fwrite($handleSascar, '    <str name="Endereco">' . $row->log_cor . '</str>'."\n");
                                }
                               
                               if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSascar, '    <str name="Bairro">' . $row->bairro_fiscal .' </str>'."\n");
                                }else{
                                	fwrite($handleSascar, '    <str name="Bairro">' . $row->bairro_cor . '</str>'."\n");
                                }
                                if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSascar, '    <str name="Cidade">' . $row->cidade_fiscal .'</str>'."\n");
                                }else{
                                	fwrite($handleSascar, '    <str name="Cidade">' . $row->cidade_cor . '</str>'."\n");
                                }
                                if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
                                	fwrite($handleSascar, '    <str name="CEP">' .trim($row->cep_fiscal).'</str>'."\n");
                                }else{
                                	fwrite($handleSascar, '    <str name="CEP">'.trim($row->cep_cor). '</str>'."\n");
                                }
                                fwrite($handleSascar, '        <str name="Endereco_NF">' . $row->log_fiscal . ', '.$row->num_fiscal.' </str>'."\n");
                                fwrite($handleSascar, '        <str name="Bairro_NF">' . $row->bairro_fiscal . '</str>'."\n");
                                fwrite($handleSascar, '        <str name="Cidade_NF">' . $row->cidade_fiscal . '</str>'."\n");
                                fwrite($handleSascar, '        <str name="CEP_NF">' . $row->cep_fiscal . '</str>'."\n");
                                fwrite($handleSascar, '        <str name="CNPJ">' . $row->clino_doc . '</str>'."\n");
                                fwrite($handleSascar, '        <int name="NumeroContrato"></int>'."\n");
                                fwrite($handleSascar, '        <str name="Referencia">' . $referencia . '</str>'."\n");
                                fwrite($handleSascar, '        <date name="DataEmissao">' . $row->nfldt_emissao . '</date>'."\n");
                                fwrite($handleSascar, '        <str name="Impressao">ANALITICO</str>'."\n");
                                fwrite($handleSascar, '        <str name="EnviaCliente">SIM</str>'."\n");
                                fwrite($handleSascar, '        <date name="Vencimento">' . $row->titdt_vencimento . '</date>'."\n");
                                fwrite($handleSascar, '        <str name="NossoNumero">' . $voBoleto->nossoNumeroDv . '</str>'."\n");
                                fwrite($handleSascar, '        <str name="NumeroDocumento">' . $row->titoid . '</str>'."\n");
                                fwrite($handleSascar, '        <dec name="ValorPagar">' . $row->titvl_titulo . '</dec>'."\n");
                                fwrite($handleSascar, '        <int name="TipoMensagem">0</int>'."\n");
                                fwrite($handleSascar, '        <dec name="ValorFatura">' . $row->titvl_titulo . '</dec>'."\n");
                                fwrite($handleSascar, '        <dec name="ValorTitulo">' . $row->titvl_titulo . '</dec>'."\n");
                                fwrite($handleSascar, '        <dec name="ValorDesconto">' . $row->nflvl_desconto . '</dec>'."\n");
                                fwrite($handleSascar, '        <dec name="ValorDescontoNF">' . $row->nflvl_desconto . '</dec>'."\n");
                                fwrite($handleSascar, '        <dec name="ValorOutrasDeducoes">0</dec>'."\n");
                                fwrite($handleSascar, '        <str name="InformacaoImposto">Valor Aproximado dos Tributos: R$' . number_format($row->nflvlr_imposto, 2, ',', '.') . ' (' . number_format($row->nflaliquota_imposto, 2, ',', '.') . '%) Fonte: IBPT</str>'."\n"); //STI 83807
                                fwrite($handleSascar, '        <str name="Boleto">SIM</str>'."\n");
                                fwrite($handleSascar, '        <str name="LinhaDigitavel">' . $voBoleto->linhaDigitavel . '</str>'."\n");
                                fwrite($handleSascar, '        <str name="CodigoBarras">' . $voBoleto->codigoBarras . '</str>'."\n");
                                fwrite($handleSascar, '        <str name="TipoEmpenho">N</str>'."\n");
                                fwrite($handleSascar, '        <str name="Empenho"></str>'."\n");
                                fwrite($handleSascar, '        <str name="FormaPagamento">'.$resultadoValidacao['item']->dados_forma_cobranca.'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem1">'.$mensagens['Mensagem1'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem2">'.$mensagens['Mensagem2'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem3">'.$mensagens['Mensagem3'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem4">'.$mensagens['Mensagem4'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem5">'.$mensagens['Mensagem5'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem6">'.$mensagens['Mensagem6'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem7">'.$mensagens['Mensagem7'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem8">'.$mensagens['Mensagem8'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem9">'.$mensagens['Mensagem9'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem10">'.$mensagens['Mensagem10'].'</str>'."\n");
                                fwrite($handleSascar, '        <str name="Mensagem11">'.$mensagens['Mensagem11'].'</str>'."\n");
                            
                                // Abertura de novo Item
                                fwrite($handleSascar, '        <arr name="historico">'."\n");

                                // Item
                                fwrite($handleSascar, '            <row>'."\n");
                                fwrite($handleSascar, '                <str name="Qtde">1</str>'."\n");
                                fwrite($handleSascar, '                <str name="Placa">'.$row->veiplaca.'</str>'."\n");
                                fwrite($handleSascar, '                <str name="Descricao">'.$row->obrobrigacao.'</str>'."\n");
                                fwrite($handleSascar, '                <dec name="Valor">'.$row->nfivl_item.'</dec>'."\n");
                                fwrite($handleSascar, '            </row>'."\n");
                                
                                $ultimo_item_escrito = 'Sas';
                            }   
                        } else {
                            $nota_validada = 0;
                        }
                    } else {
                         // Verificar se sé Varejo ou Sascar
                            if($ultimo_item_escrito == 'Sig' && $nota_validada) {
                                // Item
                                fwrite($handleSiggo, '            <row>'."\n");
                                fwrite($handleSiggo, '                <str name="Qtde">1</str>'."\n");
                                fwrite($handleSiggo, '                <str name="Placa">'.$row->veiplaca.'</str>'."\n");
                                fwrite($handleSiggo, '                <str name="Descricao">'.$row->obrobrigacao.'</str>'."\n");
                                fwrite($handleSiggo, '                <dec name="Valor">'.$row->nfivl_item.'</dec>'."\n");
                                fwrite($handleSiggo, '            </row>'."\n");
                                
                                
                            } else if ($nota_validada) {
                                // Item
                                fwrite($handleSascar, '           <row>'."\n");
                                fwrite($handleSascar, '               <str name="Qtde">1</str>'."\n");
                                fwrite($handleSascar, '               <str name="Placa">'.$row->veiplaca.'</str>'."\n");
                                fwrite($handleSascar, '               <str name="Descricao">'.$row->obrobrigacao.'</str>'."\n");
                                fwrite($handleSascar, '               <dec name="Valor">'.$row->nfivl_item.'</dec>'."\n");
                                fwrite($handleSascar, '           </row>'."\n");
                            }
                    }
                    
                    $numero_ultima_nota = $row->nflno_numero;
                    
                }
                

            
                if (count($notasAtualizarData) > 0){
                    $this->dao->atualizarStatusArquivoGerado($notasAtualizarData);
                }
                
                if ($handleSascar) {
                    // Fechamento de Arquivo
                    fwrite($handleSascar, '</job>'."\n");
                    fclose($handleSascar);
                    
                }
                
                if ($handleSiggo) {
                    // Fechamento de Arquivo
                    fwrite($handleSiggo, '</job>'."\n");
                    fclose($handleSiggo);
                }
                
                
                // zipar e colocar em $this->view->arquivo
                $arquivoZip = self::FILE_NAME_PREFIX . date('Y-m-dHis');
                $dir = $caminho;
          

                // Apaga o arquivo .zip caso exita para criar novo
                if (file_exists($dir . $arquivoZip . '.zip')) {
                    unlink($dir . $arquivoZip . '.zip');  //apaga o arquivo varejo
                }

                //GERA O ZIP DO ARQUIVO
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive();
                    $zip->open($dir . $arquivoZip . '.zip', ZIPARCHIVE::CREATE);
                    
                    if ($pathSig) {
                        $zip->addFile($pathSig, str_replace($dir, '', $pathSig));
                    }
                    
                    if ($pathSas) {
                        $zip->addFile($pathSas, str_replace($dir, '', $pathSas));
                    }
                    $zip->close();
                } elseif (!$exec_zip = shell_exec("cd " . $dir . " && zip {$arquivoZip}.zip {$pathSig}")
                        && !$exec_zip = shell_exec("cd " . $dir . " && zip {$arquivoZip}.zip {$pathSas}")) {
                    throw new Exception('Erro ao gerar arquivo.');
                   $retornoGrafica = false;
                }

                // Apaga os arquivos .xml e deixa apenas o ZIP na pasta
                if (file_exists($pathSig)) {
                    unlink($pathSig);  //apaga o arquivo varejo
                }
                if (file_exists($pathSas)) {
                    unlink($pathSas);  //apaga o arquivo sascar
                }

                $this->view->arquivo = $caminho . $arquivoZip . '.zip';

            /*    if (trim($this->view->arquivo) != '') {
                    if (is_file($this->view->arquivo) || is_file($dir . $arquivoZip)) {
                        $this->view->msg = "Arquivo gerado com sucesso. ";
                    } else {
                        $this->view->msg = "Falha ao gerar arquivo. ";
                    }
                }*/
         
    
                $this->dao->transactionCommit();
                
                /**
                 * FIM
                 */
                if (isset($this->dao->log) && !empty($this->dao->log)) {


                    $this->view->arquivo_log = $caminho . 'LOG_PENDENCIAS_' . date('Y-m-d') . '.csv';

                    if (is_file($this->view->arquivo_log)) {
                        unlink($this->view->arquivo_log);
                    }

                    //cria o arquivo CSV
                    $csvWriter = new CsvWriter($this->view->arquivo_log, ';', '', true);

                    //Seta os cabeÃ§alhos
                    $cabecalho = array(
                        "ID Cliente",
                        "Cliente",
                        "Nota Fiscal",
                        "Descrição de Erro"
                    );

                    //Adiciona o CabeÃ§alho
                    $csvWriter->addLine($cabecalho);

                    foreach ($this->dao->log as $log) {
                        // Corpo do CSV
                        $csvWriter->addLine(
                                array(
                                    $log['cliente_id'],
                                    $log['cliente'],
                                    $log['nota'],
                                    $log['msg']
                                )
                        );
                    }
                }
            } catch (ExceptionValidation $e) {
                $this->view->msg = $e->getMessage();
                $this->dao->transactionRollback();
            } catch (Exception $e) {
                $this->view->msg = $e->getMessage(); // self::MSG_FALHA_GERAR_ARQUIVO;
                $this->dao->transactionRollback();
            }
        

        if (isset($this->view->arquivo_log) && !empty($this->view->arquivo_log)) {

            $this->view->msg .= "Log de pendências: <a href='download.php?arquivo=" . $this->view->arquivo_log . "'>LOG_PENDENCIAS_" . date('Y-m-d') . ".csv</a>";
        } elseif (!isset($this->view->arquivo_log) || empty($this->view->arquivo_log)) {
            $this->view->msg .= "Nenhuma pendÊncia encontrada";
        }

        $this->voPesquisa = new FinGeraNfBoletoGraficaVo(array());
        $this->view->voPesquisa = $this->voPesquisa;

        return $retornoGrafica;
        
    }

    /**
     * Validação da geração de arquivo
     *
     * @return void
     */
    private function validarGerar() {

        $this->validarPesquisa();
    }

    /**
     * Verifica se a nota fiscal é Sascar ou NovaMarca
     *
     * @var integer $nf
     * @return integer
     */
    public function qtdeNfVarejo($nf) {

        $contaVarejo = 0;
        foreach ($nf as $item) {
            if ($item->Tipo == 12 || $item->SuperTipo == 12) {
                $contaVarejo++;
            }
        }

        // se todos os itens da nf forem varejo retorna true    	
        return ( count($nf) == $contaVarejo ) ? true : false;
    }

    private function gravarNoArquivoXML($file, $dados) {
        $handleFile = fopen($file, 'a');
        $dados .= "\n";
        fwrite($handleFile, $dados);
        fclose($handleFile);
    }

    /**
     * O sistema deve gerar um arquivo com extensão exemplo
     * 
     * @param array $notas Notas fiscais
     * @param array $itens Itens das Notas fiscais
     * write($handleFile, $dados);
     * fclose($handleFile);* @param array $numRows Número de itens das notas
     * @return void
     */
    private function gerarArquivo($notas, $itens, $numRows, $tipo) {
    	$caminho = _SITEDIR_ .'faturamento/arquivo_grafica/';
        if (empty($notas)) {
            throw new ExceptionValidation(self::MSG_NENHUMA_NF_SELECIONADA);
        }

        $path = $caminho  . $tipo . '-' . self::FILE_NAME_PREFIX . date('Y-m-d') . '.' . self::FILE_EXTENSION;

        //$this->file = new FinGeraNfBoletoGraficaArquivoXml();
        //$job = $this->file->xml->createElement('job');
        //$job->setAttribute('cliente', 'dd');
        //$job->setAttribute('template', 'dd');
        //$job->setAttribute('dataGeracao', date('Y-m-d'));
        //$job->setAttribute('numRows', $numRows);
        //Abre tag do XML
        $this->gravarNoArquivoXML($path, '<?xml version="1.0" encoding="ISO-8859-1"?>');

        //Abre tag do Job
        $this->gravarNoArquivoXML($path, '<job cliente="dd" template="dd" dataGeracao="' . date('Y-m-d') . '" numRows="' . $numRows . '">');


        $id = 1;
        $typeNotaFiscal = new FinGeraNfBoletoGraficaNotaFiscalVoType();
        $typeNotaFiscalItem = new FinGeraNfBoletoGraficaNotaFiscalItemVoType();

        foreach ($notas as $NumeroNF => $nota) {

            //$rowNf = $this->file->xml->createElement('row');
            //$rowNf->setAttribute('id', $id);
            //Abre a Tag ROW
            $this->gravarNoArquivoXML($path, '<row id="' . $id . '">');

            foreach ($nota as $name => $value) {
                $type = $typeNotaFiscal->$name;

                if ($name == 'NossoNumeroSemDv' || $name == 'IdNF' || $name == 'SerieNF') {
                    continue;
                }

                $rowNfValue = $this->createRowValues($name, $type, $value);

                //Adiciona a tag
                $this->gravarNoArquivoXML($path, $rowNfValue);

                //$rowNf->appendChild($rowNfValue);
            }

            //$rowNfItens = $this->file->xml->createElement('arr');
            //$rowNfItens->setAttribute('name', 'historico');
            //Abre a tag arr
            $this->gravarNoArquivoXML($path, '<arr name="historico">');

            foreach ($itens[$NumeroNF] as $item) {

                //$row = $this->file->xml->createElement('row');
                //Abre a tag row do historico
                $this->gravarNoArquivoXML($path, '<row>');

                foreach ($item as $name => $value) {

                    $type = $typeNotaFiscalItem->$name;

                    $rowNfItemValue = $this->createRowValues($name, $type, $value);

                    //$row->appendChild($rowNfItemValue);
                    //Adiciona a tag
                    $this->gravarNoArquivoXML($path, $rowNfItemValue);
                }

                //$rowNfItens->appendChild($row);

                $this->gravarNoArquivoXML($path, '</row>');
            }

            //fecha a tag arr
            $this->gravarNoArquivoXML($path, '</arr>');

            //$rowNf->appendChild($rowNfItens);
            //$job->appendChild($rowNf);
            //Fecha a tag row
            $this->gravarNoArquivoXML($path, '</row>');

            //$this->dao->atualizarNossoNumero($nota->NossoNumeroSemDv, $nota->NumeroDocumento);
            $this->dao->atualizarStatusArquivoGerado($NumeroNF);

            $id++;
        }

        //$root = $this->file->xml->appendChild($job);
        //fecha a tag job
        $this->gravarNoArquivoXML($path, '</job>');


        //$this->file->save($path);

        return $path;
    }

    /**
     * @return DOMElement
     */
    private function createRowValues($name, $type, $value) {
        //$value = utf8_encode($value);

        if ($type == 'dec') {
            $value = number_format((double) $value, 2, ',', '');
        }
        if ($name == 'Referencia') {
            $meses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

            $time = strtotime($value);
            $ano = date('y', $time);
            $mes = (int) date('m', $time);
            //$mes = utf8_encode(strtoupper($meses[$mes]));
            $mes = strtoupper($meses[$mes]);

            $value = $mes . '/' . $ano;
        }

        return "<" . $type . " name=\"" . $name . "\">" . $value . "</" . $type . ">";
    }
    
    

}

class FinGeraNfBoletoGraficaArquivoXml implements FinGeraNfBoletoGraficaArquivoInterface {

    /**
     * @var DOMDocument
     */
    public $xml;

    public function __construct() {
        libxml_use_internal_errors(true);

        $this->xml = new DOMDocument('1.0', 'ISO-8859-1');
        $this->xml->formatOutput = true;
    }

    public function __destruct() {
        libxml_clear_errors();
    }

    public function save($path) {

        $pathDir = dirname($path);
        if (!file_exists($pathDir)) {
            if (!mkdir($pathDir, 0777)) {
                throw new Exception('Não foi possível criar o diretório: ' . $pathDir);
            }
        }
        $this->xml->save($path);
    }

    public static function errors() {
        $error = '';
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $error = self::xmlError($error, $xml);
        }
        throw new Exception($error);
    }

    private function xmlError(libXMLError $error, $xml) {
        $return = $xml[$error->line - 1] . "\n";
        $return .= str_repeat('-', $error->column) . "^\n";

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }

        $return .= trim($error->message) .
                "\n  Line: $error->line" .
                "\n  Column: $error->column";

        if ($error->file) {
            $return .= "\n  File: $error->file";
        }

        return "$return\n\n--------------------------------------------\n\n";
    }

}

interface FinGeraNfBoletoGraficaArquivoInterface {

    public function __construct();

    public function __destruct();

    /**
     * Salva o arquivo no path informado
     * 
     * @param string $path
     */
    public function save($path);

    /**
     * Erros na geração do arquivo
     * 
     * @return string
     */
    public static function errors();
}