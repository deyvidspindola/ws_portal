<?php

/**
 *
 *
 * @file FinRetornoCobrancaRegistrada.php
 * @author marcioferreira
 * @version 19/09/2014 15:27:30
 * @since 19/09/2014 15:27:30
 * @package SASCAR FinRetornoCobrancaRegistrada.php
 */

ini_set('memory_limit', '640M');
ini_set('max_execution_time', 0);
set_time_limit(0);

require_once _SITEDIR_ . "includes/php/auxiliares.php";
require _SITEDIR_ . "includes/php/cliente_funcoes.php";
include _SITEDIR_ . 'includes/classes/UploadFile.class.php';
require _MODULEDIR_ . "Financas/DAO/FinRetornoCobrancaRegistradaDAO.php";
require_once _SITEDIR_.'lib/phpMailer/class.phpmailer.php';

require_once _MODULEDIR_ . "core/infra/Model/ComumDAO.php";
require_once _MODULEDIR_ . "core/infra/Model/ParametroDAO.php";
require_once _MODULEDIR_ . "core/module/Parametro/ParametroCobrancaRegistrada.php";
require_once _MODULEDIR_ . "core/module/BoletoRegistrado/Model/DAO/BoletoRegistradoDAO.php";
require_once _MODULEDIR_ . "core/module/BoletoRegistrado/Model/BoletoRegistradoModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoCNABSantander/Model/HeaderRetornoCNABSantanderModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoCNABSantander/Model/DetalheRetornoCNABSantanderModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoCNABSantander/Model/LeitorRetornoCNABSantanderModel.php";
require_once _MODULEDIR_ . "core/module/EventoBoletoRegistro/Model/DAO/EventoBoletoRegistroDAO.php";
require_once _MODULEDIR_ . "core/module/EventoBoletoRegistro/Model/EventoBoletoRegistroModel.php";
require_once _MODULEDIR_ . "core/module/TituloCobranca/Model/DAO/TituloCobrancaDAO.php";
require_once _MODULEDIR_ . "core/module/TituloCobranca/Model/TituloCobrancaModel.php";
require_once _MODULEDIR_ . "core/module/Boleto/Controller/BoletoController.php";


require_once _SITEDIR_ . 'modulos/core/infra/autoload.php';

use module\Parametro\ParametroCobrancaRegistrada as ParametroCobrancaRegistrada;
use module\BoletoRegistrado\BoletoRegistradoModel as BoletoRegistradoModel;
use module\LeitorRetornoCNABSantander\HeaderRetornoCNABSantanderModel as HeaderRetornoCNABSantanderModel;
use module\LeitorRetornoCNABSantander\DetalheRetornoCNABSantanderModel as DetalheRetornoCNABSantanderModel;
use module\LeitorRetornoCNABSantander\LeitorRetornoCNABSantanderModel as LeitorRetornoCNABSantanderModel;
use module\EventoBoletoRegistro\EventoBoletoRegistroModel as EventoBoletoRegistroModel;
use module\TituloCobranca\TituloCobrancaDAO as TituloCobrancaDAO;
use module\TituloCobranca\TituloCobrancaModel as TituloCobrancaModel;
use module\Boleto\BoletoController;
//[ORGMKTOTVS-1090] - ERP - Inativar o CRON que envia arquivos de remessa do ERP para o AFT do banco Santander (Boletos)
use module\Parametro\ParametroIntegracaoTotvs;
use module\WSProtheus\IntegracaoProtheusTotvs;
define('INTEGRACAO_TOTVS_ATIVA', ParametroIntegracaoTotvs::getIntegracaoTotvsAtiva());
// [ORGMKTOTVS-1092] finretornocobrancaregistrada
define('INTEGRACAO_BOLAUTOM', ParametroIntegracaoTotvs::getIntegracao('INTEGRACAO_BOLAUTOM')); 
//FIM - [ORGMKTOTVS-1090]
// [ORGMKTOTVS-1092]
define('CODIGO_BAIXAONLINE', '008528748');
//FIM - [ORGMKTOTVS-1092]

class FinRetornoCobrancaRegistrada {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property FinRetornoCobrancaRegistradaDAO
	 */
	private $dao;
	
	/**
	 * Path da pasta onde a aplicação gerencia os aquivos de importação
	 * @var string
	 */
	private $caminhoArquivo;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
	
	/**
	 * Data atual (d/m/Y)
	 * @var date
	 */
	public $dataAtual;
	
	
	/**
	 * Id da tabela execucao arquivo
	 * @var int
	 */
	public $earoid;
	
	
	/**
	 * Nome do sistema em que o usuário está logado
	 * @var string
	 */
	public $sistema;
	
	
	/**
	 * String com os dados de conexão ao schema SBTEC no banco 
	 * @var string
	 */
	public $sbtec_conexao_string;
	
	public function __construct(){

		global $conn;

		$this->dao  = new FinRetornoCobrancaRegistradaDAO($conn);
		$this->caminhoArquivo = _SITEDIR_ . "processar_cobr_registrada";
		$this->data = date('d/m/Y');
		
	}


	/**
   * Cria um arquivo(caso não exista) para saída de dados,
   * chama o arquivo que contém a classe e o método responsável em efetuar a importação
   * dos dados do arquivo em background
   * 
   * @throws Exception
   * @return multitype:number string |multitype:number NULL
   */ 
	public function prepararImportacaoDados($cod_tipo = NULL, $origem = NULL){

		try {
    		
			if (!is_dir(_SITEDIR_."processar_cobr_registrada_log")){
				if (!mkdir(_SITEDIR_."processar_cobr_registrada_log", 0777)) {
					throw new Exception('Falha ao criar pasta -> processar_cobr_registrada  de log.');
				}
			}
    		
			chmod(_SITEDIR_."processar_cobr_registrada_log", 0777);
	    		
			if (!$handle = fopen(_SITEDIR_."processar_cobr_registrada_log/importacao_cobranca_retorno", "w")) {
				throw new Exception('Falha ao criar arquivo de log.');
			}

			fputs($handle, "Processo Iniciado \r\n");
			fclose($handle);
			chmod(_SITEDIR_."processar_cobr_registrada_log/importacao_cobranca_retorno", 0777);
	    		
			// Se for chamada do AFT não chama o outro CRON, executa direto o método importarDados
			if($origem == 'AFT'){
				$this->importarDados($origem);
			}else{
				$this->importarDados();
				// Processa o arquivo em background
				passthru("/usr/bin/php "._SITEDIR_."CronProcess/fin_retorno_cobranca_registrada_upload.php >> "._SITEDIR_."processar_cobr_registrada_log/importacao_cobranca_retorno 2>&1 &");
				//passthru("C:\\xampp\\\php\\php.exe " . _SITEDIR_ . "CronProcess\\fin_retorno_cobranca_registrada_upload.php >> " . _SITEDIR_ . "processar_cobr_registrada_log\\importacao_cobranca_retorno");
			}

			return true;
    		 
		}catch(Exception $e){

			exit($e->getMessage());
    		
			$this->dao->finalizarProcesso(false, $e->getMessage());
			return false;
    }
    	
	}
    
	/**
	 * Retoran dados de um processo de importação em andamento
	 * 
	 * @return Ambigous <object, boolean>
	 */
	public function verificarProcessoAndamento(){
		$processo = $this->dao->verificarProcessoAndamento();
		return $processo;
	}    
    
	public function getFormaCobranca(){
		$formas = $this->dao->getFormaCobranca();
		return $formas;
	}
    
    
    
    /**
     * Faz as validações e o upload do arquivo de retorno
     *
     * @throws Exception
     * @return number|multitype:number string
     */
    public function upload($tipo, $file = '', $forma_cobranca = '', $origem = ''){
    
    	try{
    		
    		$msgErro = array();
			$cod_tipo = "";
			
			if (empty($forma_cobranca)) {
				$forma_cobranca = (isset($_POST['busca_forma_cobranca']{0})) ? trim($_POST['busca_forma_cobranca']) : '';
			}

    		
    		// Verificando variáveis obrigatórias
    		if($forma_cobranca == ''){
    			throw new exception('Informe a forma de cobrança');
    		}
    		
    		//validação para importação 
    		if($tipo == 'dadosRetornoCobranca'){
    			//cod do tipo de processo que está sem execução (Cobrança Registrada Retorno)
    			$cod_tipo = 15;
    			
    			//recebe o nome do  arquivo via post
				if (empty($file)) {
					$file = $_POST['arquivo_importar'];
				}
    		}
    		
    		if(empty($file)){
    			throw new Exception('Falha: O arquivo para importação não foi informado.',0);
    		}
    		
    		//verifica se existe dados de inicio de processo na tabela
    		$processo = $this->dao->verificarProcessoAndamento();
    		
    		
    		if(is_object($processo)){
    			throw new Exception('Processo de importação já foi iniciado por:  '.$processo->nm_usuario.' em :  '.$processo->data_inicio.' às '.$processo->hora_inicio ,3);
    		}
    		
    		list($nome, $ext) = explode(".",$file);
    		
			//valida extensão do arquivo
			if($ext != 'ret' && $ext != 'RET' && $ext != 'DAT' && $ext != "txt" && $ext != "TXT") {
    			return 2;
    		}
    		
    		## valida tipo de arquivo com a forma de cobrança escolhida
			if(($ext == 'RET' && $forma_cobranca != 84 /*Santander*/ && $forma_cobranca != 73/*Itaú*/  && $forma_cobranca != 85/*Bradesco*/) 
			    || ($ext == 'ret' && $forma_cobranca != 84 && $forma_cobranca != 73 && $forma_cobranca != 85)
				|| ($ext == 'DAT' && $forma_cobranca != 74/*HSBC*/ )) {
    			
    			$retorno['cod'] = 3;
    			$retorno['msg'] = 'A forma de cobrança informada não corresponde com o tipo de arquivo para importação.';
    			return $retorno;
    		}else if((strtoupper($ext) == 'TXT') && $forma_cobranca != 84 /*Santander*/)  {
    			/* caso a extensão do arquivo seja txt e a forma de cobrança seja diferente do santander, deve-se parar e exibir msg de erro */ 
				$retorno['cod'] = 3;
    			$retorno['msg'] = 'A forma de cobrança informada não corresponde com o tipo de arquivo para importação.';
    			return $retorno;
    		}
    		
    		$arquivoPath = $this->caminhoArquivo.'/'.$file;
    		
    		
    		if(!is_readable($arquivoPath)){
    			throw new exception('O arquivo não pode ser lido.');
    		}
       
    		//grava inicio do processo para controlar a importação do arquivo
    		$resUpload = $this->dao->iniciarProcesso($file, $cod_tipo, $forma_cobranca, $this->sistema);

    		if($resUpload != 1){
    			throw new Exception($resUpload,0);
    		}

    		//inicia o processo de importação em background
    		$this->prepararImportacaoDados($cod_tipo, $origem);

    		return 1;
		}
		catch(Exception $e) {

    		$msgErro['msg'] =  json_encode(utf8_encode($e->getMessage()));
    		$msgErro['cod'] =  $e->getCode();
    		
    		return $msgErro;
    	}
    }
    

    /**
     * Para um processo de importação no banco pelo número do PID
     * 
     * */
    public function pararImportacao() {

    	$pid = $this->dao->getPidArquivoTxt();
		
		if($pid > 0) {
        // mata processo pelo pid no banco de dados
	   		$retornoKill = $this->dao->killProcessDB($pid);
        // Cancela processo controlado pela tabela execucao_arquivo
			$finalizarProcesso = $this->dao->finalizarProcesso(false, 'Processo cancelado.', '');
			if($finalizarProcesso) {
				$dadosProcesso = new stdClass();
				//recupera os dados do processo cancelado
				$dadosProcesso = $this->dao->verificarProcessoFinalizado('f', 'Processo cancelado.');
				$dadosProcesso->earoid     = $this->earoid;
				$dadosProcesso->id_usuario = $this->dao->usuarioID;
				$msg_email = '';
				//$this->enviarEmail($msg_email, false, $dadosProcesso, 'pararExecucao');
				$msg = "Importação cancelada com sucesso.";
			} else {
				$msg = "Não foi possível gravar log de cancelamento.";
			}
		} else {
			$msg = "Não existe um arquivo sendo processado atualmente.";
		}
		
    	return array (
			"codigo" => 0,
			"msg" => $msg,
			"retorno" => array()
  		);

    }
    
    /**
     * Este método altera a string removendo acentos e caracteres especiais
     * Para que a string fique igual ao que o Santander retornaria
     * Para fins de verificação são utilizados apenas os dois primeiros caracteres 
     */
	function strSantander($str) {
		$str = htmlentities($str);
		$str = strtoupper($str);
		$str = str_replace("Á", "A", $str);
		$str = str_replace("Ó", "O", $str);
		$str = str_replace("É", "E", $str);
		$str = str_replace("Ã", "A", $str);
		$str = str_replace(".", "", $str);
		$str = str_replace("-", "", $str);
		$str = str_replace("/", "", $str);
		$str = str_replace(":", "", $str);
		$str = str_replace("&", "", $str);
		$str = str_replace(" & ", "", $str);
		$str = trim($str);
		if ($str != '') {
			$str = substr($str, 0, 2);
		}
		return $str;
	}

	/**
		* Este método é consumido em backgound chamado pelo arquivo fin_retorno_cobranca_registrada_upload.php
		* Efetua a validação dos dados que serão importados do arquivo em uma pasta
		* Verifica os parâmetros de início de importação do BD, ou seja, os dados que precisa estão
		* no BD e no arquivo que será baixado
	*/
	public function importarDados($origem =''){
    			
		try{
	 	    	
			$cod_ocorrencia = null;
	 	    	
			if($origem != 'AFT'){
					
				$nomeProcesso = 'fin_retorno_cobranca_registrada_upload.php';

				if(function_exists('burnCronProcess') && burnCronProcess($nomeProcesso) === true){
					echo " O processo [$nomeProcesso] está em processamento.";
					return;
				}

			}

			$processo = $this->dao->verificarProcessoAndamento();
		    	 
			if(!is_object($processo)){
				throw new Exception('Não foi possível processar a importação de dados, processo no banco não iniciado.');
			}
		    	
			// Pesquisa os parâmetros para importação no bd
			$dadosImportacao = $this->dao->consultarDadosImportacao();
		    	
			if(!is_object($dadosImportacao)){
				throw new Exception('Dados para importação não encontrados');
			}
		    	
			$this->earoid = $dadosImportacao->earoid;
		    	   	
			if($dadosImportacao->eartipo_processo == 15){
				$tipo = 'dadosRetornoCobranca';
			}
		    	
			// Recupera dados para importação
			$eaparametros = $dadosImportacao->earparametros;
		    	
			$param = explode("|", $eaparametros);
		    	
			$arquivo = $dadosImportacao->earnomearquivo;
			$forma_cobranca = $param[1];
		    	
			$linha = 1;
			$qtde_atualizado_cod = 0;
		    	
			// Verifica o usuário que iniciou o processo
			if(empty($this->dao->usuarioID)){
				$this->dao->usuarioID = $dadosImportacao->earusuoid;
			}
		    	
			$cd_usuario = $this->dao->usuarioID;
			$nomeArquivo = $arquivo;
			$caminhoArquivo = $this->caminhoArquivo.'/'.$arquivo;
		    	
			// Verifica se o aquivo existe na pasta
			if(file_exists($caminhoArquivo)){
				$arquivo = fopen($caminhoArquivo,'r');
			}else{
				throw new Exception('Falha-> arquivo para importação não encontrado.');
			}
		    	
			// Array de identificações de bancos com layout cadastrados
			$arrBancos = array('033','399','341','664'); //Santander - HSBC - Itaú - Bradesco (237)
			$forccfbbanco = $this->dao->getLayoutBanco($forma_cobranca);
		    	
			// Se a descrição da forma de cobrança não estiver cadastrada no array de layouts, informa ao usuário
			if(!in_array($forccfbbanco,$arrBancos)){
				throw new exception('Não foi possível gerar arquivo. Layout não cadastrado para forma de cobrança');
			}
		    								
			// Se o processo foi realizado no sistema SBTEC, faz outra conexão para acessar o schema SBTEC no banco
			if($dadosImportacao->earsistema == 'SBTEC'){
		    		
				// Fecha a conexão atual
				pg_close($this->dao->conn);
					
				// Faz uma nova conexão para acessar ao sistema SBTEC
				$sbtec_conn = pg_connect($this->sbtec_conexao_string);
					
				// Redefine nova variável de conexão	
				global $conn;
								
				$conn = $sbtec_conn;
				$this->dao  = new FinRetornoCobrancaRegistradaDAO($conn);
					  			
			}
							
			$this->dao->begin();
				    	
			// Recupera o pid do processo no BD para gravar na tabela execucao_arquivo
			$pid_processo = $this->dao->getPidProcessoDB();
		    	
			// Atualiza o processo com o número do PID
			$this->dao->atualizarProcesso($pid_processo, $this->earoid);
                      
                        // [ORGMKTOTVS-1092]
                        $dadosTitoid = array();
                        $cont = 0;
                        // FIM - [ORGMKTOTVS-1092]
			
                        // Lendo o arquivo
			while(!feof($arquivo)){
                           
				// Banco Santander				
				if($forccfbbanco == 33){
					
					$arrCodBaixa = array('06','17');

					$linhaA = stream_get_line($arquivo, (1027 * 60), "\n");
                                        $linhaB = stream_get_line($arquivo, (1027 * 60), "\n");
                                      
                                        if($cont == 0){
                                            // pegar o código para ver se é baixa ON LINE
                                            $codigobaixaonline = substr($linhaA, 52, 9);
                                        }
                                        
					$leitorRetornoCNABSantanderModel = new LeitorRetornoCNABSantanderModel();

					$registro = $leitorRetornoCNABSantanderModel->lerRegistro($linhaA, $linhaB);

					if($registro){
                                            	if($registro->getTipoRegistro() == 0){
                                                // [ORGMKTOTVS-1092]
							$numeroRetorno = $registro->getNumeroRetorno();
							$codigoBeneficiario = $registro->getCodigoBeneficiario();
							$dataGravacaoRemessaRetorno = $registro->getDataGravacaoRemessaRetorno();
                                               //   END [ORGMKTOTVS-1092]
							$tit_numero_registro_banco = 0;
							$nome_cliente = "";

						}elseif($registro->getTipoRegistro() == 3){
                                                       
                                                   	$codigoMovimento = $registro->getCodigoMovimento();
							$nossoNumero = $registro->getNossoNumero();
							$seuNumero = $registro->getSeuNumero();
							$arrIdentificacaoOcorrencia = $registro->getIdentificacaoOcorrencia();
							$valorJurosMultaEncargos = $registro->getValorJurosMultaEncargos();
							$valorDescontoConcedido = $registro->getValorDescontoConcedido();
							$valorPago = $registro->getValorPago();
							$valorLiquidoCreditado = $registro->getValorLiquidoCreditado();
							$dataEfetivacaoCredito = $registro->getDataEfetivacaoCredito();
							$codigoOcorrenciaPagador = $registro->getCodigoOcorrenciaPagador();
							$dataOcorrenciaPagador = $registro->getDataOcorrenciaPagador();
							$valorNominalTitulo = $registro->getValorNominalTitulo();
							$nomePagador = $registro->getNomePagador();

							// Legado
							if(empty($seuNumero) || ($seuNumero == $nossoNumero)){
								$seuNumero = $codigoMovimento == BoletoRegistradoModel::CODIGO_MOVIMENTO_LIQUIDACAO_TITULO_NAO_REGISTRADO ? $nossoNumero : (int)substr((string)$nossoNumero, 0, -1);								
							}

							$boletoRegistradoModel = BoletoRegistradoModel::getById($seuNumero);

							if(!empty($boletoRegistradoModel)){
                                                        	$identificacaoTituloEmpresa = $boletoRegistradoModel->getTituloId();

								$boletoRegistradoModel->setNossoNumero($registro->getNossoNumero());
								$boletoRegistradoModel->setCodigoConvenio($codigoBeneficiario);
								$boletoRegistradoModel->setCodigoMovimento($registro->getCodigoMovimento());
								$boletoRegistradoModel->setValorEncargosRetorno($registro->getValorJurosMultaEncargos());
								$boletoRegistradoModel->setValorDescontoRetorno($registro->getValorDescontoConcedido());
								$boletoRegistradoModel->setValorAbatimentoRetorno($registro->getValorAbatimento());
								$boletoRegistradoModel->setValorIOFRetorno($registro->getValorIOF());
								$boletoRegistradoModel->setValorPago($registro->getValorPago());
								$boletoRegistradoModel->setValorLiquidoCreditado($registro->getValorLiquidoCreditado());
								$boletoRegistradoModel->setValorOutrasDespesas($registro->getValorOutrasDespesas());
								$boletoRegistradoModel->setValorOutrosCreditos($registro->getValorOutrosCreditos());
								$boletoRegistradoModel->setValorTarifas($registro->getValorTarifaCustas());
								$boletoRegistradoModel->setDataEfetivacaoCredito($registro->getDataEfetivacaoCredito());
								$boletoRegistradoModel->setDataOcorrencia($registro->getDataOcorrencia());
								$boletoRegistradoModel->setCodigoOcorrenciaPagador($registro->getCodigoOcorrenciaPagador());
								$boletoRegistradoModel->setDataOcorrenciaPagador($registro->getDataOcorrenciaPagador());
								$boletoRegistradoModel->setValorOcorrenciaPagador($registro->getValorOcorrenciaPagador());
								$boletoRegistradoModel->setComplementoOcorrenciaPagador($registro->getComplementoOcorrenciaPagador());

								if(
									$boletoRegistradoModel->getCodigoOrigem() == BoletoRegistradoModel::CODIGO_ORIGEM_CNAB &&
									$boletoRegistradoModel->getCodigoMovimento() == BoletoRegistradoModel::CODIGO_MOVIMENTO_ENTRADA_CONFIRMADA
								){
									try {
										$boletoRegistradoModel->gerarLinhaDigitavel();
										$boletoRegistradoModel->gerarCodigoDeBarras();
									}catch(Exception $e){
										$mErroProcessamento[] = "Erro linha $linha : ". $e->getMessage() .".";
									}
								}

								if(!$boletoRegistradoModel->atualizarInformacoesCNAB()){
									$mErroProcessamento[] = "Erro linha $linha : Não foi possível atualizar o boleto {$seuNumero}.";
									$linha++;
									continue;							
								}
                                                                
                                                                if($valorNominalTitulo !== $boletoRegistradoModel->getValorNominal()){
                                                                        $mTitulosNaobaixadosValoresDiferentes[] = $identificacaoTituloEmpresa . ',,' . $this->dao->recuperarNomeCliente($identificacaoTituloEmpresa) . ',,' . $this->dao->getNumeroNota($identificacaoTituloEmpresa) . ',,' . $registro->getValorLiquidoCreditado() . ',,' . $registro->getDataOcorrencia('dd/mm/yyyy') . ',,' . $registro->getValorDescontoConcedido() . ',,' . $registro->getValorJurosMultaEncargos() . ',,' . $registro->getDataVencimentoTitulo('dd/mm/yyyy') . ',,' . $registro->getValorNominalTitulo();
                                                                        $nTotal_ValoresDiferentes += $registro->getValorLiquidoCreditado();
                                                                        $linha++;
									continue;
								}

								echo "Tentando baixa no título {$identificacaoTituloEmpresa}, boleto {$seuNumero}.\n";

								$eventoBoletoRegistroModel = new EventoBoletoRegistroModel();
								$eventoBoletoRegistroModel->setBoletoId($seuNumero);
								$eventoBoletoRegistroModel->setCodigoMovimento($codigoMovimento);
								$eventoBoletoRegistroModel->setCodigoCnab($numeroRetorno);
								$eventoBoletoRegistroModel->setDataCnab($dataGravacaoRemessaRetorno);
								$eventoBoletoRegistroModel->inserir();

							}else{
                                                        	$tituloCobrancaModel = TituloCobrancaModel::getTituloById($seuNumero);

								if(!empty($tituloCobrancaModel)){

									$identificacaoTituloEmpresa = $tituloCobrancaModel->tituloId;

									if((float)$valorNominalTitulo !== (float)$tituloCobrancaModel->valorTitulo){
                                                                                $mTitulosNaobaixadosOutrasDivergencias[] = $identificacaoTituloEmpresa . ',,' . $this->dao->recuperarNomeCliente($identificacaoTituloEmpresa) . ',,' . $this->dao->getNumeroNota($identificacaoTituloEmpresa) . ',,' . $registro->getValorLiquidoCreditado() . ',,' . $registro->getDataOcorrencia('dd/mm/yyyy') . ',,' . $registro->getValorDescontoConcedido() . ',,' . $registro->getValorJurosMultaEncargos() . ',,' . $registro->getDataVencimentoTitulo('dd/mm/yyyy') . ',,' . $registro->getValorNominalTitulo();
                                                                                $nTotal_OutrasDivergencias += $registro->getValorLiquidoCreditado();
                                                                                $linha++;
										continue;
									}

									echo "Tentando baixa no título {$identificacaoTituloEmpresa}.\n";

								}else{

									$mErroProcessamento[] = "Erro linha $linha : Não foi possível encontrar o boleto $seuNumero, prosseguir com processamento manual.";
									$linha++;
									continue;

								}

							}

							echo "Tentando baixa no título {$identificacaoTituloEmpresa}, boleto {$seuNumero}.\n";

							$boletoController = new BoletoController();
							$formaRegistro = $boletoController->getformaRegistro($identificacaoTituloEmpresa);
							$codigoLiquidacao = ParametroCobrancaRegistrada::getCodigosMovimentoRetornoLiquidacao();
							$codigoBaixa = ParametroCobrancaRegistrada::getCodigosMovimentoRetornoBaixa();
							$codigosMovimentoRegistrado = ParametroCobrancaRegistrada::getCodigosMovimentoRegistrado();
							$codigosMovimentoRetornoRejeitado = ParametroCobrancaRegistrada::getCodigosMovimentoRetornoRejeitado();
							$codigosMovimentoRetornoLiquidacao = ParametroCobrancaRegistrada::getCodigosMovimentoRetornoLiquidacao();
							$codigosMovimentoRetornoBaixa = ParametroCobrancaRegistrada::getCodigosMovimentoRetornoBaixa();

							if(in_array($codigoMovimento, $codigosMovimentoRetornoLiquidacao)){
								
								$tipoEvento = "L";
								$descricaoEvento = null;
								$titoid = $identificacaoTituloEmpresa; // Define $titoid para dar baixar no título no ERP

							}else{

								if(in_array($codigoMovimento, $codigosMovimentoRegistrado)){
									$tipoEvento = "S";
									$descricaoEvento = null;
								}elseif(in_array($codigoMovimento, $codigosMovimentoRetornoRejeitado)){
									$tipoEvento = "R";
									$descricaoEvento = "Registro_detalhe";
								}elseif(in_array($codigoMovimento, $codigosMovimentoRetornoBaixa)){
									$tipoEvento = "B";
									$descricaoEvento = "Baixa_detalhe";
								}

								$qtde_atualizado_cod++;

							}

							$idTipoEventoTitulo = $this->dao->getTipoEvento($codigoMovimento, $tipoEvento);
							
							if(!empty($arrIdentificacaoOcorrencia)){
								foreach($arrIdentificacaoOcorrencia as $codigoIdentificacaoOcorrencia){
									$idTipoEventoOcorrencia = $this->dao->getTipoEvento($codigoIdentificacaoOcorrencia, $tipoEvento, $descricaoEvento);
									$this->dao->logEventoTitulo($identificacaoTituloEmpresa, $idTipoEventoOcorrencia, $codigoIdentificacaoOcorrencia, $tipoEvento);
								}
							}else{
								$this->dao->logEventoTitulo($identificacaoTituloEmpresa, $idTipoEventoTitulo, 'NULL', $tipoEvento);
							}

							$tituloCobrancaModel = new TituloCobrancaModel();

							switch($tituloCobrancaModel->getTipoTitulo($identificacaoTituloEmpresa)){
								case TituloCobrancaModel::TIPO_TITULO:
									$this->dao->updateTitulo($identificacaoTituloEmpresa, $idTipoEventoTitulo);
									break;
								case TituloCobrancaModel::TIPO_TITULO_RETENCAO:
									$this->dao->updateTituloRetencao($identificacaoTituloEmpresa, $idTipoEventoTitulo);
									break;
								case TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO:
									$this->dao->updateTituloConsolidado($identificacaoTituloEmpresa, $idTipoEventoTitulo);
									break;
							}

							$num_titulo = $identificacaoTituloEmpresa;
							$tit_numero_registro_banco = $nossoNumero;
							$nome_cliente = $this->dao->recuperarNomeCliente($num_titulo);
							$cod_ocorrencia = $codigoMovimento;
							$data_ocorrencia = $registro->getDataOcorrencia('d/m/y');
							$data_vencimento = $registro->getDataVencimentoTitulo('d/m/y');
							$valor_titulo = $registro->getValorNominalTitulo();
							$valor_desconto_titulo = $registro->getValorDescontoConcedido();
							$valor_juros_titulo = $registro->getValorJurosMultaEncargos();
							$valor_credito_titulo = $registro->getValorLiquidoCreditado();
							$data_credito = $registro->getDataEfetivacaoCredito('d/m/y');
							$data_credito_header = $data_credito;

							// VERIFICAR REMOÇÃO DE CODIGO
							$remessaID = $this->dao->getRemessa($seuNumero); //// VERIFICAR FUNÇÃO
							$quantidadeTotal[$seuNumero] = $this->dao->totalRegistrosPorRemessa($seuNumero);

							if(!empty($remessaID)){
								if($quantidadeTotal[$seuNumero][0]['total'] == 0){ 
									$retornoOK = $this->dao->headerStatuRemessa($remessaID, $forccfbbanco);
								}
							}
							// END VERIFICAR REMOÇÃO DE CODIGO
                                                       
						}

					}
                  
				}else{

					$buffer = stream_get_line($arquivo, (1027*60), "\n");
					$cod_registro = substr($buffer,0,1);
					 
					if($cod_registro == '0' && $buffer != ''){
						$data_credito_header = substr($buffer,113,6);
						$data_credito_header = substr($data_credito_header,0,2).'/'.substr($data_credito_header,2,2).'/'.substr($data_credito_header,4,2);
					}

					$tit_numero_registro_banco = 0;
					$nome_cliente = "";

				}
						
				// HSBC
				if( $forccfbbanco == 399 ){

					// Array de configuração de códigos que dão baixa nos titulos
					$arrCodBaixa = array('06','07','08');
		           			 
					// Código do OID
					$cod_oid  = trim(substr($buffer,37,16));

					//Numero do título (Será usado nas consultas, ver $titoid)
					$num_titulo = substr($cod_oid,6,7);
					$num_titulo =  validaVar($num_titulo,'integer');


					// Código de identificação do banco (Nosso Número)
					$tit_numero_registro_banco = substr($buffer,67,11);

					//Tipo do Registro
					$tipo_registro  = substr($cod_oid,0,1);
           			 
					// Nome do Sacado / Nome do Cliente
					if(!empty($cod_oid) && is_numeric($num_titulo)){
						$nome_cliente = $this->dao->recuperarNomeCliente($num_titulo);
					}else{
						$nome_cliente   = " ";
					}
           			 
					// Identificação da Ocorrência
					$cod_ocorrencia = validaVar(substr($buffer,108,2),'integer');

					// Data da ocorrência
					$data_ocorrencia = substr($buffer,110,6);
					$data_ocorrencia = substr($data_ocorrencia,0,2).'/'.substr($data_ocorrencia,2,2).'/'.substr($data_ocorrencia,4,2);

					//  Data De Vencimento
					$data_vencimento = substr($buffer,146,6);
					$data_vencimento = substr($data_vencimento,0,2).'/'.substr($data_vencimento,2,2).'/'.substr($data_vencimento,4,2);

					// Valor Nominal do Título
					$valor_titulo = substr($buffer,152,11).'.'.substr($buffer,163,2);

					// Valor Desconto do Título
					$valor_desconto_titulo = substr($buffer,240,11).'.'.substr($buffer,251,2);

					// Valor Juros do Título
					$valor_juros_titulo = substr($buffer,266,11).'.'.substr($buffer,277,2);

					// Valor Creditado do Título
					$valor_credito_titulo = substr($buffer,253,11).'.'.substr($buffer,264,2);

					// Data do credito
					$data_credito = substr($buffer,82,6);
					$data_credito = substr($data_credito,0,2).'/'.substr($data_credito,2,2).'/'.substr($data_credito,4,2);

					// Data Crédito Header
					$data_credito_header = $data_credito;

				}
           		 
				// Itau
				if( $forccfbbanco == 341 ){

					// Array de configuração de códigos que dão baixa nos titulos
					$arrCodBaixa = array('06','07','08');
           			 
					// Código de Inscrição / Identificação do tipo de inscrição/empresa / 01=CPF 02=CNPJ
					$codigo_inscricao = substr($buffer,1,2);

					// Codigo de referencia enviado ao banco na remessa.
					$cod_oid = trim(substr($buffer,37,25));

					// Código do OID, podendo ser titoid ou evtioid
					$tipo_registro = substr($cod_oid,0,1);
           			 
					// Identificação da Ocorrência
					$cod_ocorrencia = validaVar(substr($buffer,108,2),'integer');
           			 
					// Data de ocorrência no Banco
					$data_ocorrencia = substr($buffer,110,6);
					$data_ocorrencia = substr($data_ocorrencia,0,2).'/'.substr($data_ocorrencia,2,2).'/'.substr($data_ocorrencia,4,2);

					// NOSSO NUMERO, Confirmação do número do título no banco
					$tit_numero_registro_banco = substr($buffer,126,8);

					//-- Data De Vencimento
					$data_vencimento = substr($buffer,146,6);
					$data_vencimento = substr($data_vencimento,0,2).'/'.substr($data_vencimento,2,2).'/'.substr($data_vencimento,4,2);

					// Valor Nominal do Título
					$valor_titulo = substr($buffer,152,11).'.'.substr($buffer,163,2);

					// Valor Juros do Título
					$valor_juros_titulo = substr($buffer,266,11).'.'.substr($buffer,277,2);

					// Valor Desconto do Título
					$valor_desconto_titulo = substr($buffer,240,11).'.'.substr($buffer,251,2);

					// Valor Creditado do Título
					$valor_credito_titulo = substr($buffer,253,11).'.'.substr($buffer,264,2);

					// Data do credito
					$data_credito = trim(substr($buffer,295,6));
					if (!empty($data_credito)){
						$data_credito = substr($data_credito,0,2).'/'.substr($data_credito,2,2).'/'.substr($data_credito,4,2);
					}else{
						$data_credito = $data_credito_header;
					}
           			 
					// Nome do Sacado / Nome do Cliente
					$nome_cliente = substr($buffer,324,29);

					if(trim($nome_cliente) == '' || trim($nome_cliente) == NULL){
						
						$num_titulo = trim(substr($buffer,63,19));
						
						if(!empty($num_titulo)){
							$nome_cliente = $this->dao->recuperarNomeCliente($num_titulo);
						}else{
							$nome_cliente = " ";
						}

					}
           			 
					// Registros rejeitados ou alegação do sacado
					$cod_rejeicao = validaVar(substr($buffer,377,8),'string');

				}
           			 
				// Bradesco
				if( $forccfbbanco == 664 ){
       			
					// Array de configuração de códigos que dão baixa nos titulos
					$arrCodBaixa = array('06','09','10','17');

					// Código do OID
					$num_controle_participante = trim(substr($buffer,37,25));

					//Numero do título (Será usado nas consultas, ver $titoid)
					$num_titulo = substr($num_controle_participante,13,7);
					$num_titulo = validaVar($num_titulo,'integer');

					// Código de identificação do banco (Nosso Número)
					$tit_numero_registro_banco = substr($buffer,70,12);
       			
					// Nome do Sacado / Nome do Cliente
					if($num_titulo != '' &&  $num_titulo != NULL){
						$nome_cliente = $this->dao->recuperarNomeCliente($num_titulo);
					}else{
						$nome_cliente   = " ";
					}
          		 
					// Identificação da Ocorrência
					$cod_ocorrencia = validaVar(substr($buffer,108,2),'integer');

					// Data da ocorrência
					$data_ocorrencia = substr($buffer,110,6);
					$data_ocorrencia = substr($data_ocorrencia,0,2).'/'.substr($data_ocorrencia,2,2).'/'.substr($data_ocorrencia,4,2);

					//-- Data De Vencimento
					$data_vencimento = substr($buffer,146,6);
					$data_vencimento = substr($data_vencimento,0,2).'/'.substr($data_vencimento,2,2).'/'.substr($data_vencimento,4,2);

					// Valor Nominal do Título
					$valor_titulo = substr($buffer,152,11).'.'.substr($buffer,163,2);

					// Valor Desconto do Título
					$valor_desconto_titulo = substr($buffer,240,11).'.'.substr($buffer,251,2);

					// Valor Creditado do Título
					$valor_credito_titulo = substr($buffer,253,11).'.'.substr($buffer,264,2);

					// Valor Juros do Título
					$valor_juros_titulo = substr($buffer,266,11).'.'.substr($buffer,277,2);

					// Data do credito
					$data_credito = substr($buffer,295,6);
					$data_credito = substr($data_credito,0,2).'/'.substr($data_credito,2,2).'/'.substr($data_credito,4,2);

					//Data Crédito Header
					$data_credito_header = $data_credito;

				}

				if(trim($cod_ocorrencia) < 0){
					throw new exception('Erro linha '.$linha.': Não foi possível verificar codigo de retorno');
				}
           		 
				// Início de tratamento geral					
           		 
				if( $forccfbbanco == 399 ){
					// HSBC
					$titoid = $num_titulo;
				}elseif( $forccfbbanco == 341){
					// Itau
					$titoid = validaVar(substr($cod_oid,1,strlen($cod_oid)),'integer');
				}elseif( $forccfbbanco == 664){
					// Bradesco
					$titoid = $num_titulo;
				}
           		 
				$result = 0;
				$emite_boleto_recup_ativo = false;
				$pagamento_titulo_consolidado = false;

				if(trim($titoid) != ''){
					$result = $this->dao->getDadosTitulo($titoid,'',true);
				}
           		 
				if(!is_array($result)){

					// Se não achar na tabela titulo, pesquisa na tabela titulo_retencao
					if(trim($titoid) != ''){
						$result = $this->dao->getTituloRetencao($titoid,'');
					}
           			 
					if(!is_array($result)) {
           				 
						// Se Banco HSBC
						if( $forccfbbanco == 399 ){
							$titoid =  substr($buffer,68,7);
						}elseif($forccfbbanco == 341){
							$titoid = trim(substr($buffer,63,19));
						}
           				 
						//7 dígitos do Nosso Número
						if(trim($titoid) != '') {
           					
							$result = $this->dao->getTituloKernel($titoid);
           					
							// Se achou pega o título correspondente e o valor
							if ($result > 0) {
           						
								$resultado_kernel = 0;
								$registros_kernel = 0;
           						
								foreach ($result as $resu){

									$titkoid = $resu['titkoid'];
									$retorno = $this->dao->getDadosTitulo($titkoid, $valor_titulo);

									if(is_array($retorno)){

										$titoid = $retorno[0]['titoid'];
										$titdt_credito = $retorno[0]['titdt_credito'];
										$tit_numero_registro_banco = $retorno[0]['titnumero_registro_banco'];

										$resultado_kernel = 1;
										$registros_kernel = 1;

										break;

									}

								}
           						
								$resu = $resultado_kernel;
								$result = $registros_kernel;

							}else{
           						
								$ret_dados_reg = $this->dao->getDadosTituloRegistroBanco($titoid);
           						 
								$result = 0;

								if(is_array($ret_dados_reg)){
									$titoid = $ret_dados_reg[0]['titoid'];
									$titdt_credito = $ret_dados_reg[0]['titdt_credito'];
									$tit_numero_registro_banco = $ret_dados_reg[0]['titnumero_registro_banco'];
									$result = 1;
								}
           						
							}
           					
							// Senão, tenta buscar no boleto seco
							if($result == 0){
           						
								$result = $this->dao->getTituloRetencao('',$titoid);
           						 
								// Se não achou, tenta localizar com os 8 dígitos do Nosso Número
								if (!is_array($result)){
           							 
									//Se Banco HSBC
									if( $forccfbbanco == 399 ){
										// Tamanho 11- com digito verificador
										// Tamanho 10- ignora o digito verificador - caso nao encontre pesquisa novamente caso haja segunda via.
										$titoid = trim(substr($buffer,42,11));
									}elseif($forccfbbanco == 341){
										$titoid = trim(substr($buffer,62,20)); // Títulos não registrados pelo banco. Tenta buscar o número do banco na posição 63 do arquivo.
									}

									// Primeiro no título
									if(trim($titoid) != ''){
										$result = $this->dao->getDadosTituloRegistroBanco($titoid, true);
									}
           							 
									// Senão achou procura no boleto seco
									if (!is_array($result)){
           								
										$result = $this->dao->getTituloRetencao('',$titoid);
           								 
										// Se ainda não achou, tenta no título pelo ID (7 dígitos) combinado com valor no título
										if (!is_array($result)) {
           									
											//Se Banco HSBC
											if( $forccfbbanco == 399 ){
												$titoid =  substr($buffer,68,7);
											}elseif($forccfbbanco == 341){
												$titoid = trim(substr($buffer,63,19));
											}

											$result = $this->dao->getDadosTitulo($titoid,'', false);
           									
											// Ou no boleto seco, pelo ID combinado com valor no título
											if (!is_array($result)) {
	           										
												// Verificar se é um título pai que foi unificado pela politica de desconto
												$titulos_filhos = $this->dao->getTituloFilhoPolitica($titoid);
           										
												if(is_array($titulos_filhos)){
           											
													// Se o título não estiver pago efetua as baixas
													if($titulos_filhos[0]['titcdt_pagamento'] == ''){
           												
														// Baixa o título pai na tabela titulo_consolidado
														$retorno_baixas = $this->setBaixarTituloPaiFilho($titoid, $titulos_filhos, $forccfbbanco, $cd_usuario, $data_credito, $forma_cobranca, $data_ocorrencia, $valor_credito_titulo, $tit_numero_registro_banco, $linha, $cod_ocorrencia, $arrCodBaixa);
	           										
														if($retorno_baixas['status']){
	           										
															$array_filhos = array();
	           													           												
															for($i=0; $i < count($titulos_filhos); $i++){
																$array_filhos[] = $titulos_filhos[$i]['titulo_filho'];
															}

															$_filhos = implode(', ', $array_filhos);

															$mErroProcessamento[] = 'O título pai '.$titoid.' , foi baixado juntamente com os titulos filhos: '.$_filhos.PHP_EOL.PHP_EOL;

															$pagamento_titulo_consolidado = 1;

															$quant_filhos_baixados = count($array_filhos);

															$mTitulosBaixados_filhos[] = $retorno_baixas['dados_titulos_baixados'] ;

															$mTitulosJaBaixados = is_array($mTitulosJaBaixados) ? $mTitulosJaBaixados : array();
															$mTitulosJaBaixados = array_merge($mTitulosJaBaixados, $retorno_baixas['dados_titulos_ja_baixados']);

															$nTotal_Baixados_filhos += $retorno_baixas['vlr_total_titulos_baixados'];
															$nTotal_Ja_Baixados += $retorno_baixas['vlr_total_titulos_ja_baixados'];
	           												
														}else{
															$mErroProcessamento[] = $retorno_baixas['msg'];
														}

													// Se já tiver pago, exibe no relatório que já possui baixa
													}else{
														$mErroProcessamento[] = 'O título pai '.$titoid.' , não pode ser baixado pois já possui data de baixa.'.PHP_EOL;
													}

													// Limpa id do título pai para não tentar baixar novamente, e continua com o restante da leitura do arquivo
													$titoid = '';

												}else{
           										
													$result = $this->dao->getTituloRetencao($titoid, '', false);
           										
													if(is_array($result)) {

														$titoid = $result[0]['titoid'];
														$titdt_credito = $result[0]['titdt_credito'];
														$titnumero_registro_banco = $result[0]['titnumero_registro_banco'];
														$emite_boleto_recup_ativo = true;

													}else{

														$titoid = trim(substr($buffer_T, 54, 15)); // Títulos não registrados pelo banco. Tenta buscar o número do banco na posição 63 do arquivo.
														$titoid = validaVar($titoid, 'integer');

														if(empty($titoid)){
															$mErroProcessamento[] = 'Erro linha '.$linha.': titulo esta em branco no arquivo ';
														}else{
															$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível encontrar o titulo'.$titoid;
														}

													}

											}
           										 
										}else{

											$titoid = $result[0]['titoid'];
											$titdt_credito = $result[0]['titdt_credito'];
											$titnumero_registro_banco = $result[0]['titnumero_registro_banco'];

										}
           									
									}else{

										$titoid = $result[0]['titoid'];
										$titdt_credito = $result[0]['titdt_credito'];
										$titnumero_registro_banco = $result[0]['titnumero_registro_banco']; 
										$emite_boleto_recup_ativo = true;

									}
           								 
								}else{
           								
									$titoid = $result[0]['titoid'];
									$titdt_credito = $result[0]['titdt_credito'];
									$titnumero_registro_banco = $result[0]['titnumero_registro_banco']; 
           								
								}

							}else{

								$titoid = $result[0]['titoid']; 
								$titdt_credito = $result[0]['titdt_credito']; 
								$tit_numero_registro_banco_local = $result[0]['titnumero_registro_banco']; 
           							
								if( in_array($cod_ocorrencia,$arrCodBaixa) and !empty($titoid)) {
								
									/*
									* Gera nota fiscal de pagamento do boleto seco, quando o retorno do banco indicar uma baixa de título
									* antes de executar verifica qual esquema de banco de dados será usado para a função
									*/
									$resul = $this->dao->setEmiteBoletoRecuperaAtivo($tit_numero_registro_banco_local, $valor_credito_titulo, $data_ocorrencia);

									if($resul == 2){
										$mErroProcessamento[] = 'Erro linha ' . $linha . ': Falha ao executar função ->  emite_boleto_recup_ativo: <br/>' . 'Título = ' . $tit_numero_registro_banco_local . ' <br/>' . 'Valor = ' . number_format($valor_credito_titulo, 2, ',', '.') . ' <br/>' . 'Data do Pagamento = ' . $data_ocorrencia . ' <br/> <br/>';
									}

								}

							}
           						 
						}else{

							// Verifica se o título existe em titulo_venda
							$result = $this->dao->getDadosTituloVenda($titoid);
           						           							
							if(is_array($result)){

								$titdt_credito = $result[0]['titdt_credito'];

								if( in_array($cod_ocorrencia,$arrCodBaixa) and !empty($titoid)) {

									$resuNN = $this->dao->getDadosTitulo($titoid,'');
									$titnumero_registro_bancoNN = $resuNN[0]['titnumero_registro_banco'];

									/*
									* Gera nota fiscal de pagamento do boleto Venda Ou Vivo, quando o retorno do banco indicar uma baixa de título
									* antes de executar verifica qual esquema de banco de dados será usado para a função
									*/
									$resul  = $this->dao->setEmiteBoletoRecuperaAtivo($titnumero_registro_bancoNN, $valor_credito_titulo, $data_ocorrencia);

									if($resul == 2){
										$mErroProcessamento[] = 'Erro linha ' . $linha . ': Falha ao executar função ->  emite_boleto_recup_ativo: <br/>' . 'Título = ' . $tit_numero_registro_banco_local . ' <br/>' . 'Valor = ' . number_format($valor_credito_titulo, 2, ',', '.') . ' <br/>' . 'Data do Pagamento = ' . $data_ocorrencia . ' <br/> <br/>';
									}

								}

							}

						}
					
					}
           				
				}else{
        	$emite_boleto_recup_ativo = true;
				}

			}else{
				$emite_boleto_recup_ativo = true;	
			}
           		 
			$titoid = trim($titoid);

			if(!empty($titoid) && is_numeric($titoid)){
				$nome_cliente =  $this->dao->recuperarNomeCliente($titoid);
			}
           		 
			if($emite_boleto_recup_ativo){

				$titdt_credito = $result[0]['titdt_credito'];   
				$tit_numero_registro_banco_local = $result[0]['titnumero_registro_banco']; 

				if(in_array($cod_ocorrencia,$arrCodBaixa) and !empty($titoid)){

					/*
					* Gera nota fiscal de pagamento do boleto seco, quando o retorno do banco indicar uma baixa de título
					* antes de executar verifica qual esquema de banco de dados será usado para a função
					*/
					$resul  = $this->dao->setEmiteBoletoRecuperaAtivo($tit_numero_registro_banco_local, $valor_credito_titulo, $data_ocorrencia);

					if($resul == 2){
						$mErroProcessamento[] = 'Erro linha ' . $linha . ': Falha ao executar função ->  emite_boleto_recup_ativo: <br/>' . 'Título = ' . $tit_numero_registro_banco_local . ' <br/>' . 'Valor = ' . number_format($valor_credito_titulo, 2, ',', '.') . ' <br/>' . 'Data do Pagamento = ' . $data_ocorrencia . ' <br/> <br/>';
					}

				}

			}
           		
			// Atualizando titulo ou evento titulo
			if ($cod_ocorrencia == '02' && $forccfbbanco != 33){
				
				$retorno = $this->dao->setDadosTitulo($cod_ocorrencia, $tit_numero_registro_banco, $titoid, '');

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar titulo com código de ocorrência 02';
				}

				$qtde_atualizado_cod ++;

			}elseif($cod_ocorrencia == '03'  && $forccfbbanco != 33){

				$retorno = $this->dao->setDadosTitulo($cod_ocorrencia, $tit_numero_registro_banco, $titoid, $cod_rejeicao);

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar titulo com codigo da ocorrência 03';
				}

				$qtde_atualizado_cod ++;


			}elseif($cod_ocorrencia == '04' && $forccfbbanco != 33){

				$tipo_codigo = 4;
				$retorno = $this->dao->setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo);

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar evento do titulo ocorrência 04';
				}

				$qtde_atualizado_cod ++;

			}elseif($cod_ocorrencia == '12' && $forccfbbanco != 33){

				$tipo_codigo = 4;
				$retorno = $this->dao->setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo);

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar evento do titulo ocorrência 12';
				}

				$qtde_atualizado_cod ++;

			}elseif($cod_ocorrencia == '14' && $forccfbbanco != 33){

					$tipo_codigo = 6;
					$retorno = $this->dao->setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo);

					if(!$retorno){
						$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar evento do titulo ocorrência 14';
					}

					$qtde_atualizado_cod ++;

			}elseif($cod_ocorrencia == '15' && $forccfbbanco != 33){

				$tipo_codigo = 2;
				$retorno = $this->dao->setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo, $cod_rejeicao, $titdt_credito);

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar evento do titulo ocorrência 15';
				}

				$qtde_atualizado_cod ++;

			}elseif(($cod_ocorrencia == '16' || $cod_ocorrencia == '17') && $forccfbbanco != 33){

				$tipo_codigo = 31;
				$retorno = $this->dao->setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo, $cod_rejeicao, $titdt_credito);

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar evento do titulo ocorrência 16 ou 17';
				}

				$qtde_atualizado_cod ++;

			}elseif($cod_ocorrencia == '18' && $forccfbbanco != 33){

				$tipo_codigo = '2,4';
				$retorno = $this->dao->setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo, $cod_rejeicao, $titdt_credito, $evticod_retorno_cobr_reg = 1);

				if(!$retorno){
					$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível atualizar evento do titulo ocorrência 18';
				}

				$qtde_atualizado_cod ++;

			}
           		
                       
			// Processo de baixa de título
			// Se o codigo de ocorrencia está setado como um codigo de baixa de titulo no layout executa a baixa
                        if( in_array($cod_ocorrencia,$arrCodBaixa) && $titoid > 0) {
                            // Verificar se o titulo já não esta baixado
				// Verificando se data de pagamento já não foi setada
				$res = $this->dao->executarVerificaBaixaTitulo($titoid);
           			
				if(!is_array($res)){
					$mErroProcessamento[] = 'Erro: Linha: '.$linha.'Msg-> '. $res;
				}
           			  			
				if (is_array($res)) {

					$dt_credito = !empty($res[0]['titdt_credito']) ? $res[0]['titdt_credito'] : null; 
					$numero_nota_fiscal = !empty($res[0]['numero_nota_fiscal']) ? $res[0]['numero_nota_fiscal'] : null; 
					$titclioid = !empty($res[0]['titclioid']) ? $res[0]['titclioid'] : null; 
					$titvl_pagamento = !empty($res[0]['titvl_pagamento']) ? $res[0]['titvl_pagamento'] : null; 
                    $titpref_protheus = !empty($res[0]['titpref_protheus']) ? $res[0]['titpref_protheus'] : null; 
                    $titformacobranca = !empty($res[0]['titformacobranca']) ? $res[0]['titformacobranca'] : null; 
					$dt_pagamento = !empty($res[0]['titdt_pagamento']) ? $res[0]['titdt_pagamento'] : null; 
                    					
					$retorno = retirarClienteOperacoesNovoRetorno($titoid, $titclioid, $valor_credito_titulo, $cd_usuario, "Baixa cobrança registrada");
                                       
					if(!$retorno) {
						$this->dao->rollback();
						echo "erro --> retirarClienteOperacoes ! titoid: ". $titoid . "clioid: ". $titclioid;
						$mErroProcessamento[] = 'Erro linha '.$linha.': '.$retorno;
					}

				}
                                
            // verificar nota cancelada
            if(!empty($dt_credito) && $titformacobranca == 21){
				$mTitulosNaobaixadosNotaCancelada[] = $titoid . ',,' . $nome_cliente . ',,' . $numero_nota_fiscal . ',,' . $valor_credito_titulo . ',,' . $data_ocorrencia . ',,' . $valor_desconto_titulo . ',,' . $valor_juros_titulo . ',,' . $data_vencimento . ',,' . $valor_titulo;
				$nTotal_NotaCancelada += $valor_credito_titulo;
                $linha ++;
                continue;
			}
                                
            // pagamento em duplicidade
            if(!empty($dt_credito) && !empty($dt_pagamento) && (!empty($titvl_pagamento) || $titvl_pagamento == "" || $titvl_pagamento > 0)){
				$mTitulosNaobaixadosDuplicidade[] = $titoid . ',,' . $nome_cliente . ',,' . $numero_nota_fiscal . ',,' . $valor_credito_titulo . ',,' . $data_ocorrencia . ',,' . $valor_desconto_titulo . ',,' . $valor_juros_titulo . ',,' . $data_vencimento . ',,' . $valor_titulo;
				$nTotal_Duplicidade += $valor_credito_titulo;
                $linha ++;
                continue;
            }
            
			// baixar  títulos baixado como perda que foram negociados
			if(empty($dt_credito) || (!empty($dt_credito) && $titformacobranca == 51 && strstr($titpref_protheus,"P")) && empty($dt_pagamento)){

					// Verificando variáveis
					if($forccfbbanco == ''){
						$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível selecionar banco';
					}

					if($data_ocorrencia == ''){
						$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível selecionar data de ocorrencia';
					}
           				
                                         
					//Calcula multa e juros e atualiza o titulo
					$this->dao->calcularAtualizarMultaJuros($titoid, $valor_credito_titulo, $valor_desconto_titulo);

					$aux_campos = $forccfbbanco." ".$cd_usuario." ".$data_credito." ".$forma_cobranca." ".$data_ocorrencia;
					// Monta sql invocando a função para realizar a baixa dos títulos
					$ret_baixa =  $this->dao->setBaixaContasReceber($titoid, $aux_campos, $cd_usuario);
                                        // [ORGMKTOTVS-1092]
                                        if($forccfbbanco == 33 && $ret_baixa){
                                            $dadosTitoid[] = $titoid;
                                        }
                                         // end [ORGMKTOTVS-1092]
					
                                        // Desvincula titulo filho do titulo pai (para caso o cliente pague um titulo filho que foi unificado sem pagar o titulo pai)
					$this->dao->desvincularTituloConsolidado($titoid);

					if(!$ret_baixa){
						$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível baixar título';
					}
           		           				
					$mTitulosBaixados[] = $titoid . ',,' . $nome_cliente . ',,' . $numero_nota_fiscal . ',,' . $valor_credito_titulo . ',,' . $data_ocorrencia . ',,' . $valor_desconto_titulo . ',,' . $valor_juros_titulo . ',,' . $data_vencimento . ',,' . $valor_titulo;
					$nTotal_Baixados += $valor_credito_titulo;
                                         

                         	}else{

					$mTitulosJaBaixados[] = $titoid . ',,' . $nome_cliente . ',,' . $numero_nota_fiscal . ',,' . $valor_credito_titulo . ',,' . $data_ocorrencia . ',,' . $valor_desconto_titulo . ',,' . $valor_juros_titulo . ',,' . $data_vencimento . ',,' . $valor_titulo;
					$nTotal_Ja_Baixados += $valor_credito_titulo;
                                        
				}
                                
                            }
                            $linha ++;
                            $cont++;
                } // end while
                
		fclose($arquivo);
        
		$msg = 'Arquivo processado com sucesso.';

                if($qtde_atualizado_cod > 0){
			$msg_aux .= ' Total de <b>' . $qtde_atualizado_cod . '</b> registros atualizados com código de retorno</br>';
		}
            
		if(count($mErroProcessamento) > 0){
			foreach($mErroProcessamento as $cErro){
				$msg_aux .= "<font color=red>".$cErro."<br>";
			}
			$msg_aux .= '</font><br>';
		}
            
		if(count($mTitulosBaixados) > 0 || $quant_filhos_baixados > 0){

			// Junta os dados dos títulos consolidados com os titulos comuns
			if($quant_filhos_baixados > 0){
	           		
				if(is_array($mTitulosBaixados) && is_array($mTitulosBaixados_filhos) && count($mTitulosBaixados_filhos) > 0){

					foreach($mTitulosBaixados_filhos as $dados){
						$mTitulosBaixados = array_merge($mTitulosBaixados, $dados);
					}

				}else{

					if(count($mTitulosBaixados_filhos) > 0){
						
						$mTitulosBaixados = Array();
						
						foreach($mTitulosBaixados_filhos as $key => $dados){
							$mTitulosBaixados = array_merge($mTitulosBaixados, $dados);
						}

					}

				}

				// Soma valores dos títulos consolidados com os demais títulos
				$nTotal_Baixados = $nTotal_Baixados + $nTotal_Baixados_filhos;
			
			}

			$retorno_html = $this->montarHtmlTitulosBaixados($mTitulosBaixados, $nTotal_Baixados);          		
			$msg_aux .= $retorno_html['msg_aux'];
			$mlista_titulo_html = $retorno_html['mlista_titulo'];
			$resu = $this->dao->getTipoMovimentacaoBancaria();
	           	
			if(is_array($resu)){
				$historico = $resu[0]['tmbhistorico'];
				$tipo_movim = $resu[0]['tmbtipo'];
				$conta_contab = $resu[0]['tmbplcoid'];
				$cod_movim = $resu[0]['tmboid'];
			}
	           	 
	           	
			if($quant_filhos_baixados > 0 || $mlista_titulo_html > 0){

				if(is_array($mlista_titulo_html && is_array($array_filhos))){
					// Junta os arrays com os títulos normais com os filhos
					$mlista_titulo = array_merge($mlista_titulo_html, $array_filhos);
				}elseif(is_array($array_filhos)){
					$mlista_titulo = $array_filhos;
				}elseif(is_array($mlista_titulo_html)){
					$mlista_titulo = $mlista_titulo_html;
				}

			}
	           	
			$parametros =" \"$forccfbbanco\"
											\"$data_credito_header\"
											\"$tipo_movim\"
											\"NULL\"
											\"$historico\"
											\"$nTotal_Baixados\"
											\"$conta_contab\"
											\"NULL\"
											\"NULL\"
											\"NULL\"
											\"NULL\"
											\"$cod_movim\"
											\"$cd_usuario\"
											\"NULL\"
											\"\"
											\"\"
											\"NULL\"
											\"NULL\"
											\"NULL\"
											\"NULL\" ";
	           	
			// Insere movimentação bancária e atualiza os títulos baixados
			$ret_movimento =  $this->dao->setMovimentacaoBancaria($parametros, $mlista_titulo);
	           
			if(!$ret_movimento){
				throw new Exception($ret_movimento);
			}
	           	
		}

	        if( count($mTitulosNaobaixadosNotaCancelada) > 0 ){
                     $msg  = 'Titulos não baixado devido a nota cancelada.';
                     $msg_aux .= $this->montarHtmlTitulosComBaixa($mTitulosNaobaixadosNotaCancelada,$nTotal_NotaCancelada, $msg );
		}
                
                if(count($mTitulosNaobaixadosDuplicidade) > 0){
                     $msg  = 'Titulos não baixado devido nota paga em duplicidade.';
                     $msg_aux .= $this->montarHtmlTitulosComBaixa($mTitulosNaobaixadosDuplicidade,$nTotal_Duplicidade, $msg );
	
                }
             
                if( count($mTitulosNaobaixadosValoresDiferentes) > 0 ){
                     $msg  = 'Titulos não baixado devido a valor pago divergente da nota.';
                     $msg_aux .= $this->montarHtmlTitulosComBaixa($mTitulosNaobaixadosValoresDiferentes,$nTotal_ValoresDiferentes, $msg );
		}
                
                if( count($mTitulosJaBaixados) > 0 ){
                    $msg  = 'Os seguintes títulos não puderam ser baixados pois já possuem data de baixa.';
                    $msg_aux .= $this->montarHtmlTitulosComBaixa($mTitulosJaBaixados,$nTotal_Ja_Baixados, $msg);
		}
                
                if( count($mTitulosNaobaixadosOutrasDivergencias) > 0 ){
                    $msg  = 'Titulos não baixado devido a outros divergencias.';
                    $msg_aux .= $this->montarHtmlTitulosComBaixa($mTitulosNaobaixadosOutrasDivergencias,$nTotal_OutrasDivergencias, $msg);
		}
                
                $this->dao->commit();
                
                // [ORGMKTOTVS-1092]
                //INÍCIO INTEGRAÇÃO TOTVS
                $dadosIntegracao = array();
                $dadosIntegracao["operation"] = "bolautom";
                $dadosIntegracao["strOrigem"] = "FinRetornoCobrancaRegistrada.php";
                $dadosIntegracao["idTitle"] = $dadosTitoid;
		
                if (INTEGRACAO_TOTVS_ATIVA && INTEGRACAO_BOLAUTOM) {

                    if ($codigobaixaonline == CODIGO_BAIXAONLINE && $forccfbbanco == 33) {
                        //INTEGRAÇÃO TOTVS
                        $dadosIntegracao["integration"] = true;
                        IntegracaoProtheusTotvs::integraProtheusTotvs($dadosIntegracao); 
                    }
                } else {
                // montar o json em caso da integraçao nao estar ativa para colocar na fila e enviar depois.
                    $dadosIntegracao["integration"] = false;
                    IntegracaoProtheusTotvs::integraProtheusTotvs($dadosIntegracao);
                    echo "<script> alert('" . _MSG_INTEGRACAO_ . "');</script>";
                }
                // FIM INTEGRACAO TOTVS
                // FIM - [ORGMKTOTVS-1092]

                // Gera o arquivo CSV
		$arquivo_csv = $this->gerarArquivoCsv($msg, $qtde_atualizado_cod, $mErroProcessamento , $mTitulosBaixados, $nTotal_Baixados, $mTitulosJaBaixados, $nTotal_Ja_Baixados, $mTitulosNaobaixadosNotaCancelada, $nTotal_NotaCancelada, $mTitulosNaobaixadosDuplicidade, $nTotal_Duplicidade, $mTitulosNaobaixadosValoresDiferentes, $nTotal_ValoresDiferentes, $mTitulosNaobaixadosOutrasDivergencias, $nTotal_OutrasDivergencias, $dadosImportacao->earnomearquivo, $mIntegracao);

		if($arquivo_csv['status']){
			$planilhaRelatorio = $arquivo_csv['arquivo_gerado'];
		}else{
			throw new Exception($arquivo_csv['msg_erro']);
		}
                
              $msg_sucesso = 'Processo de importação finalizado com sucesso.';
                // Finaliza processo com sucesso
		$finalizarProcesso = $this->dao->finalizarProcesso(true, $msg);

		// Recupera os dados do processo finalizado com sucesso para enviar por e-mail
		$dadosProcesso = $this->dao->verificarProcessoFinalizado('t','');
		$dadosProcesso->earoid = $this->earoid;
		$dadosProcesso->msg = $msg_sucesso; 

		// Envia email de sucesso
		$enviarEnmail = $this->enviarEmail($msg_aux, true, $dadosProcesso, $tipo, $planilhaRelatorio);
	    	
		}catch(Exception $e) {

			exit($e->getMessage());
			    		    	
			$this->dao->rollback();
			// Finaliza processo com erro 
			$finalizarProcesso = $this->dao->finalizarProcesso(false, $e->getMessage());
			// Recupera os dados do processo finalizado com erro para enviar por e-mail
			$dadosProcesso = $this->dao->verificarProcessoFinalizado('f','');
			$enviarEnmail = $this->enviarEmail($e->getMessage(), false, $dadosProcesso, 'erro');
			return $e->getMessage();

		}

	}

    
    /**
    * 
    * 
    * @param unknown $titoid_pai
    * @param unknown $titulos_filhos
    * @param unknown $forccfbbanco
    * @param unknown $cd_usuario
    * @param unknown $data_credito
    * @param unknown $forma_cobranca
    * @param unknown $data_ocorrencia
    * @param unknown $valor_pago
    * @param unknown $tit_numero_registro_banco
    * @param unknown $linha
    * @return Ambigous <boolean, multitype:>
    */
    public function setBaixarTituloPaiFilho($titoid_pai, $titulos_filhos, $forccfbbanco, $cd_usuario, $data_credito, $forma_cobranca, $data_ocorrencia, $valor_pago, $tit_numero_registro_banco, $linha, $cod_ocorrencia, $arrCodBaixa){
    	
        $ret_baixa_pai = false;
        $mTitulosJaBaixados = array();
        $nTotal_Ja_Baixados = 0;
    	
    	//veifica se o código da ocorrêndia pode dar baixa no título
    	if(in_array($cod_ocorrencia, $arrCodBaixa) && !empty($titoid_pai)) {
    	
	    	//baixa o titulo pai na tabela titulo_consolidado
	    	$ret_baixa_pai = $this->dao->setBaixaTituloPai($titoid_pai, $cd_usuario, $valor_pago, $tit_numero_registro_banco, $forccfbbanco);
    	
    	}else{
			//INSERT INTO evento_titulo (evtititoid,evtitpetoid,evtirtcroid,evtidt_geracao,evticod_retorno_cobr_reg)
			//VALUES(4820239,29/*id da tabela tpetoid.tipo_evento_titulo*/,NULL,NOW(),1/*código do detalhe da liquidação que veio no arquivo*/);
			// UPDATE titulo_cosolidado SET titctpetoid = 25 /*id da tabela tipo_evento_titulo.tpetoid*/ WHERE titcoid = $titulo;
    		$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível baixar o título pai '.$titoid_pai.' ->  ocorrência = '. $cod_ocorrencia;
    	}
    	
    	//baixa os títulos filhos
    	if($ret_baixa_pai){
    		
    		$nome_cliente =  $this->dao->recuperarNomeCliente($titulos_filhos[0]['titulo_filho']);
    		
    		$aux_campos = $forccfbbanco." ".$cd_usuario." ".$data_credito." ".$forma_cobranca." ".$data_ocorrencia;
    		 
    		//baixa os títulos filhos um a um
    		for($i=0 ; $i < count($titulos_filhos); $i++){
    			
    			
    			// Verifica a baixa do título
    			$res = $this->dao->executarVerificaBaixaTitulo($titulos_filhos[$i]['titulo_filho']);
    		
    			if(!$res){
    				$mErroProcessamento[] = 'Erro: Linha: '.$linha.' Não foi possível verificar titulo baixado';
    			}
    			
    			if (is_array($res)) {
    				
                    $titoid_filho       = $titulos_filhos[$i]['titulo_filho'];
    				$dt_credito         = $res[0]['titdt_credito'];
    				$titclioid          = $res[0]['titclioid'];
    				$titvl_pagamento    = $res[0]['titvl_pagamento'];
    				$titdt_vencimento   = $res[0]['titdt_vencimento'];
    				$titvl_titulo       = $res[0]['titvl_titulo']; 
    				$titvl_juros        = $res[0]['titvl_juros'];
    				$titvl_multa        = $res[0]['titvl_multa'];
    				$titvl_desconto     = $res[0]['titvl_desconto'];
    			    $titdt_pagamento    = $res[0]['titdt_pagamento'];
                    $numero_nota_fiscal = $res[0]['numero_nota_fiscal'];
    		
    				$retorno = retirarClienteOperacoes($titulos_filhos[$i]['titulo_filho'], $titclioid, $titvl_pagamento, $cd_usuario, "Baixa cobrança registrada");
    			}
    			 
    			if ($retorno != "Operação efetuada com sucesso") {
    				 
    				//$this->dao->rollback();
    				//pg_query($this->conn, "ROLLBACK;");
    				$msg_err_opera = "erro ao retirar cliente operações! titoid: ". $titulos_filhos[$i]['titulo_filho'] . "clioid: ". $titclioid;
    				
    				echo $msg_err_opera;
    				
    				$mErroProcessamento[] =  $msg_err_opera .'<br/>Erro linha '.$linha.': '.$retorno;
    			}
			
    			if($dt_credito == ''){	
    				//atualiza os valores dos títulos filhos, Valor do juros pago e multa, e desconto se houver
        			$atualiza_valor_filho = $this->dao->atualizarValoresTituloFilho($titulos_filhos[$i]['titulo_filho']);
        			
        			// Montando baixa dos titulos
        			// Monta sql invocando a função para realizar a baixa dos títulos
        			$ret_baixa =  $this->dao->setBaixaContasReceber($titulos_filhos[$i]['titulo_filho'], $aux_campos, $cd_usuario);
        		
        			if(!$ret_baixa){
        				
        				$mErroProcessamento[] = 'Erro linha '.$linha.': Não foi possível baixar título';
        			
        			}else{
        			
	        			$valoresTitulosFilhos = $this->dao->getValoresTitulosFilhos($titulos_filhos[$i]['titulo_filho']);
	        			
	        			$valorTitulo     = $valoresTitulosFilhos[0]['titvl_titulo'];
	        			$titvl_pagamento = $valoresTitulosFilhos[0]['titvl_pagamento'];
	        			$juros_multa     = $valoresTitulosFilhos[0]['titvl_juros_multa'];
	        			$titvl_desconto  = $valoresTitulosFilhos[0]['titvl_desconto']; 
	        			
	        			//dados dos títulos filhos baixados
						$mTitulosBaixados[] = $titoid_filho . ',,' . $nome_cliente . ',,' . $numero_nota_fiscal . ',,' . $titvl_pagamento . ',,' . $data_ocorrencia . ',,' . $titvl_desconto . ',,' . $juros_multa . ',,' . $titdt_vencimento . ',,' . $valorTitulo;
	        			$nTotal_Baixados += $titvl_titulo;
        			
        			}
        		
    			}else{
    				
                    $juros_multa = $titvl_juros + $titvl_multa;

					$mTitulosJaBaixados[] = $titoid_filho . ',,' . $nome_cliente . ',,' . $numero_nota_fiscal . ',,' . $res[0]['valor_titulo_baixado'] . ',,' . $data_credito . ',,' . $titvl_desconto . ',,' . $juros_multa . ',,' . $titdt_vencimento . ',,' . $titvl_titulo;

                    $nTotal_Ja_Baixados += $titvl_titulo;
				}
    		}//fim for	
    		
    	}//fim $ret_baixa_pai
    	

    	$ret_pro['status']                        = true;
    	$ret_pro['dados_titulos_baixados']        = $mTitulosBaixados;
    	$ret_pro['dados_titulos_ja_baixados']     = $mTitulosJaBaixados;
    	$ret_pro['vlr_total_titulos_baixados']    = $nTotal_Baixados;
    	$ret_pro['vlr_total_titulos_ja_baixados'] = $nTotal_Ja_Baixados;
    	
    	if(count($mErroProcessamento) > 0){
    	
    		for($i=0; $i < count($mErroProcessamento);$i++){
    			$msg_err_processamento  .= $mErroProcessamento[$i].'<br/>';
    		}
    	
    		$ret_pro['msg'] = $msg_err_processamento;
    	
    	}else{
    		$ret_pro['msg']    = '';
    	}
    	
    	return $ret_pro;
    }
    
    
    
    /**
     * Monta html para enviar por e-mail com os título que foram baixados
     * 
     * @param ARRAY $mTitulosBaixados
     * @param INT $nTotal_Baixados
     */
    public function montarHtmlTitulosBaixados($mTitulosBaixados, $nTotal_Baixados){
    	
    	
    	$msg_aux .= '<table width="100%" class="tableMoldura">';
    	$msg_aux .= '<tr><td align=left colspan=5>Titulos baixados como pago</td></tr>';
    	$msg_aux .= '<tr class="tableSubTitulo">';
    	$msg_aux .= '<td align=left><b>Cliente</b></td>';
    	$msg_aux .= '<td align=center><b>Cód. Título</b></td>';
    	$msg_aux .= '<td align=center><b>NF</b></td>';
    	$msg_aux .= '<td align=right width=100><b>Valor Titulo</b></td>';
    	$msg_aux .= '<td align=center width=120><b>Data Pagamento</b></td>';
    	$msg_aux .= '<td align=center><b>Data Vencimento</b></td>';
    	$msg_aux .= '<td align=right><b>Juros+Multa</b></td>';
    	$msg_aux .= '<td align=right><b>Desconto</b></td>';
    	$msg_aux .= '<td align=right><b>Valor Pago</b></td>';
    	$msg_aux .= '</tr>';
    	
    	foreach($mTitulosBaixados as $cConteudo){
    		 
			list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudo);
    	
    		$msg_aux .= '<tr>';
    		$msg_aux .= "<td align=left>$cNome</td>";
    		$msg_aux .= "<td align=center>$nId</td>";
    		$msg_aux .= "<td align=center>$cNF</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_titulo,2,',','.')."</td>";
    		$msg_aux .= "<td align=center>".$cDataPagamento."</td>";
    		$msg_aux .= "<td align=center>".$data_vencimento."</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_juros_titulo,2,',','.')."</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_desconto_titulo,2,',','.')."</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_credito_titulo,2,',','.')."</td>";
    		$msg_aux .= '</tr>';
    		$mlista_titulo[]=$nId;
    	}
    	 
    	$msg_aux .= '<tr>';
    	$msg_aux .= "<td align=left colspan=3></td>";
    	$msg_aux .= "<td align=right><b>".number_format($nTotal_Baixados,2,',','.')."</b></td>";
    	$msg_aux .= "<td align=center></td>";
    	$msg_aux .= '</tr>';
    	
    	$msg_aux .= '</table>';
    	
    	$dados['msg_aux'] = $msg_aux;
    	$dados['mlista_titulo'] = $mlista_titulo;
    	
    	return $dados;
    	
    }
    
    
    /** Monta html  para enviar por e-mail com os títulos que não puderam ser baixados, pois já constam com baixa no sistema
     * 
     * @param ARRAY $mTitulosJaBaixados
     * @param INT $nTotal_Ja_Baixados
     * @return string
     */
    public function montarHtmlTitulosComBaixa($mTitulosJaBaixados,$nTotal_Ja_Baixados, $msg){
    	
    	$msg_aux .= '<table width="100%" class="tableMoldura">';
    	$msg_aux .= '<tr><td align=left colspan=5>'.$msg.'</td></tr>';
    	$msg_aux .= '<tr class="tableSubTitulo">';
    	$msg_aux .= '<td align=left><b>Cliente</b></td>';
    	$msg_aux .= '<td align=center><b>Cód. Título</b></td>';
    	$msg_aux .= '<td align=center><b>NF</b></td>';
    	$msg_aux .= '<td align=right width=100><b>Valor Titulo</b></td>';
    	$msg_aux .= '<td align=center width=120><b>Data Pagamento</b></td>';
    	$msg_aux .= '<td align=center><b>Data Vencimento</b></td>';
    	$msg_aux .= '<td align=right><b>Juros+Multa</b></td>';
    	$msg_aux .= '<td align=right><b>Desconto</b></td>';
    	$msg_aux .= '<td align=right><b>Valor Pago</b></td>';
    	$msg_aux .= '</tr>';
    	
    	foreach($mTitulosJaBaixados as $cConteudo){
			list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudo);
    		 
    		$msg_aux .= '<tr>';
    		$msg_aux .= "<td align=left>$cNome</td>";
    		$msg_aux .= "<td align=center>$nId</td>";
    		$msg_aux .= "<td align=center>$cNF</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_titulo,2,',','.')."</td>";
    		$msg_aux .= "<td align=center>".$cDataPagamento."</td>";
    		$msg_aux .= "<td align=center>".$data_vencimento."</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_juros_titulo,2,',','.')."</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_desconto_titulo,2,',','.')."</td>";
    		$msg_aux .= "<td align=right>".number_format($valor_credito_titulo,2,',','.')."</td>";
    		$msg_aux .= '</tr>';
    	}
    	$msg_aux .= '<tr>';
    	$msg_aux .= "<td align=left colspan=3></td>";
    	$msg_aux .= "<td align=right><b>".number_format($nTotal_Ja_Baixados,2,',','.')."</b></td>";
    	$msg_aux .= "<td align=center></td>";
    	$msg_aux .= '</tr>';
    	
    	$msg_aux .= '</table>';
    	
    	
    	return $msg_aux;
    	
    }
    
    
    
    /**
     * 
     * 
     * @param unknown $msg
     * @param unknown $qtde_atualizado_cod
     * @param unknown $mErroProcessamento
     * @param unknown $mTitulosBaixados
     * @param unknown $nTotal_Baixados
     * @param unknown $mTitulosJaBaixados
     * @param unknown $nTotal_Ja_Baixados
     * @param unknown $mTitulosNaobaixadosNotaCancelada
     * @param unknown $mTitulosNaobaixadosDuplicidade
     * @param unknown $mTitulosNaobaixadosValoresDiferentes
     * @param unknown $mTitulosNaobaixadosOutrasDivergencias
     * @throws Exception
     * @return string|unknown
     */
    public function gerarArquivoCsv($msg, $qtde_atualizado_cod, $mErroProcessamento , $mTitulosBaixados, $nTotal_Baixados, $mTitulosJaBaixados, $nTotal_Ja_Baixados, $mTitulosNaobaixadosNotaCancelada, $nTotal_NotaCancelada, $mTitulosNaobaixadosDuplicidade, $nTotal_Duplicidade, $mTitulosNaobaixadosValoresDiferentes, $nTotal_ValoresDiferentes, $mTitulosNaobaixadosOutrasDivergencias, $nTotal_OutrasDivergencias, $nome_arquivo, $mIntegracao){
    	
    	try {
    	
    		$nome_arquivo = explode(".",$nome_arquivo);
    		
    		$nomeArquivo =  $nome_arquivo[0] . "_" . date('dmy_Hi') . ".csv";
    		$planilhaRelatorio = "/var/www/docs_temporario/" . $nomeArquivo;
    		$handle	= fopen($planilhaRelatorio, "w");
    		 
    		if(!$handle){
    			throw new Exception('Erro ao gerar o arquivo csv.');
    		}
    		 
    		//Registros com cód de Retorno Atualizados
    		if($qtde_atualizado_cod > 0){
    			$linha	= "";
    			$linha .= 'Total de '.$qtde_atualizado_cod .' registros atualizados com código de retorno'. ";";
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    		}
    		 
    		//Mensagens de Erro
    		if(count($mErroProcessamento) > 0){
    			foreach($mErroProcessamento as $cErro){
    				$linha	= "";
    				$linha .= $cErro . ";";
    				$linha .= "\r\n";
    				fwrite($handle, $linha);
    			}
    		}
    		 
    		//Titulos nota cancelada
    		if(count($mTitulosNaobaixadosNotaCancelada) > 0 ){
    			//Cabeçalho principal dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Titulo não baixado devido a nota Cancelada";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Cabeçalho das colunas dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Cliente";';
    			$linha .= '"Cód. Título";';
    			$linha .= '"NF";';
    			$linha .= '"Valor Titulo";';
    			$linha .= '"Data Pagamento";';
    			$linha .= '"Data Vencimento";';
    			$linha .= '"Juros+Multa";';
    			$linha .= '"Desconto";';
    			$linha .= '"Valor Pago";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Detalhe dos Títulos 
    			foreach($mTitulosNaobaixadosNotaCancelada as $cConteudoNotaCancelada){
					list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudoNotaCancelada);
    	
    				$linha	= "";
    				$linha .= $cNome . ';';
    				$linha .= $nId . ';';
    				$linha .= $cNF . ';';
    				$linha .= number_format($valor_titulo,2,',','.') . ';';
    				$linha .= $cDataPagamento . ';';
    				$linha .= $data_vencimento . ';';
    				$linha .= number_format($valor_juros_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_desconto_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_credito_titulo,2,',','.') . ';';
    				$linha .= "\r\n";
    				fwrite($handle, $linha);
    	
    			}
    			//Rodapé dos Títulos nota cancelada
    			$linha	= "";
    			$linha .= ";;;";
    			$linha .= number_format($nTotal_NotaCancelada,2,',','.');
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    		}//FIM: if(count($mTitulosNaobaixadosNotaCancelada) > 0 )
    	
    		//Titulos duplicidade
    		if(count($mTitulosNaobaixadosDuplicidade) > 0 ){
    			//Cabeçalho principal dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Titulo não baixado devido nota paga em duplicidade";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Cabeçalho das colunas dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Cliente";';
    			$linha .= '"Cód. Título";';
    			$linha .= '"NF";';
    			$linha .= '"Valor Titulo";';
    			$linha .= '"Data Pagamento";';
    			$linha .= '"Data Vencimento";';
    			$linha .= '"Juros+Multa";';
    			$linha .= '"Desconto";';
    			$linha .= '"Valor Pago";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Detalhe dos Títulos 
    			foreach($mTitulosNaobaixadosDuplicidade as $cConteudoDuplicidade){
					list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudoDuplicidade);
    	
    				$linha	= "";
    				$linha .= $cNome . ';';
    				$linha .= $nId . ';';
    				$linha .= $cNF . ';';
    				$linha .= number_format($valor_titulo,2,',','.') . ';';
    				$linha .= $cDataPagamento . ';';
    				$linha .= $data_vencimento . ';';
    				$linha .= number_format($valor_juros_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_desconto_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_credito_titulo,2,',','.') . ';';
    				$linha .= "\r\n";
    				fwrite($handle, $linha);
    	
    			}
    			//Rodapé dos Títulos duplicidade
    			$linha	= "";
    			$linha .= ";;;";
    			$linha .= number_format($nTotal_Duplicidade,2,',','.');
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    		}//FIM: if(count($mTitulosNaobaixadosDuplicidade) > 0 )
    	
                //Titulos valores divergentes
    		if(count($mTitulosNaobaixadosValoresDiferentes) > 0 ){
    			//Cabeçalho principal dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Titulo não baixado devido a valor pago divergente da nota";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Cabeçalho das colunas dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Cliente";';
    			$linha .= '"Cód. Título";';
    			$linha .= '"NF";';
    			$linha .= '"Valor Titulo";';
    			$linha .= '"Data Pagamento";';
    			$linha .= '"Data Vencimento";';
    			$linha .= '"Juros+Multa";';
    			$linha .= '"Desconto";';
    			$linha .= '"Valor Pago";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Detalhe dos Títulos 
    			foreach($mTitulosNaobaixadosValoresDiferentes as $cConteudoValorDiferente){
					list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudoValorDiferente);
    	
    				$linha	= "";
    				$linha .= $cNome . ';';
    				$linha .= $nId . ';';
    				$linha .= $cNF . ';';
    				$linha .= number_format($valor_titulo,2,',','.') . ';';
    				$linha .= $cDataPagamento . ';';
    				$linha .= $data_vencimento . ';';
    				$linha .= number_format($valor_juros_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_desconto_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_credito_titulo,2,',','.') . ';';
    				$linha .= "\r\n";
    				fwrite($handle, $linha);
    	
    			}
    			//Rodapé dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= ";;;";
    			$linha .= number_format($nTotal_ValoresDiferentes,2,',','.');
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    		}//FIM: if(count($mTitulosNaobaixadosValoresDiferentes) > 0 )
    	
                //Titulos já baixados
    		if(count($mTitulosJaBaixados) > 0 ){
    			//Cabeçalho principal dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Os seguintes títulos não puderam ser baixados pois já possuem data de baixa.";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Cabeçalho das colunas dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Cliente";';
    			$linha .= '"Cód. Título";';
    			$linha .= '"NF";';
    			$linha .= '"Valor Titulo";';
    			$linha .= '"Data Pagamento";';
    			$linha .= '"Data Vencimento";';
    			$linha .= '"Juros+Multa";';
    			$linha .= '"Desconto";';
    			$linha .= '"Valor Pago";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Detalhe dos Títulos Já Baixados
    			 
    			foreach($mTitulosJaBaixados as $cConteudoBaixados){
					list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudoBaixados);
    	
    				$linha	= "";
    				$linha .= $cNome . ';';
    				$linha .= $nId . ';';
    				$linha .= $cNF . ';';
    				$linha .= number_format($valor_titulo,2,',','.') . ';';
    				$linha .= $cDataPagamento . ';';
    				$linha .= $data_vencimento . ';';
    				$linha .= number_format($valor_juros_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_desconto_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_credito_titulo,2,',','.') . ';';
    				$linha .= "\r\n";
    				fwrite($handle, $linha);
    	
    			}
    			//Rodapé dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= ";;;";
    			$linha .= number_format($nTotal_Ja_Baixados,2,',','.');
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    		}//FIM: if(count($mTitulosJaBaixados) > 0 )
             
                //Titulos outras divergencias
    		if(count($mTitulosNaobaixadosOutrasDivergencias) > 0 ){
    			//Cabeçalho principal dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Titulos não baixado devido a outros divergencias";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Cabeçalho das colunas dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= '"Cliente";';
    			$linha .= '"Cód. Título";';
    			$linha .= '"NF";';
    			$linha .= '"Valor Titulo";';
    			$linha .= '"Data Pagamento";';
    			$linha .= '"Data Vencimento";';
    			$linha .= '"Juros+Multa";';
    			$linha .= '"Desconto";';
    			$linha .= '"Valor Pago";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Detalhe dos Títulos 
    			foreach($mTitulosNaobaixadosOutrasDivergencias as $cConteudoOutrasDivergencias){
					list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudoOutrasDivergencias);
    	
    				$linha	= "";
    				$linha .= $cNome . ';';
    				$linha .= $nId . ';';
    				$linha .= $cNF . ';';
    				$linha .= number_format($valor_titulo,2,',','.') . ';';
    				$linha .= $cDataPagamento . ';';
    				$linha .= $data_vencimento . ';';
    				$linha .= number_format($valor_juros_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_desconto_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_credito_titulo,2,',','.') . ';';
    				$linha .= "\r\n";
    				fwrite($handle, $linha);
    	
    			}
    			//Rodapé dos Títulos Já Baixados
    			$linha	= "";
    			$linha .= ";;;";
    			$linha .= number_format($nTotal_OutrasDivergencias,2,',','.');
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    		}//FIM: if(count($mTitulosNaobaixadosOutrasDivergencias) > 0 )
                
                //Titulo Baixados
    		if(count($mTitulosBaixados) > 0 ){
    			//Cabeçalho principal dos Títulos Baixados
    			$linha	= "";
    			$linha .= '"Titulos baixados como pago.";';
    			$linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Cabeçalho das colunas dos Títulos Baixados
    			$linha	= "";
    			$linha .= '"Cliente";';
    			$linha .= '"Cód. Título";';
    			$linha .= '"NF";';
    			$linha .= '"Valor Titulo";';
    			$linha .= '"Data Pagamento";';
    			$linha .= '"Data Vencimento";';
    			$linha .= '"Juros+Multa";';
    			$linha .= '"Desconto";';
    			$linha .= '"Valor Pago";';
                        $linha .= "\r\n";
    			fwrite($handle, $linha);
    			 
    			//Detalhe dos Títulos Baixados
    			 
    			foreach($mTitulosBaixados as $cConteudo){
					list($nId, $cNome, $cNF, $valor_credito_titulo, $cDataPagamento, $valor_desconto_titulo, $valor_juros_titulo, $data_vencimento, $valor_titulo) = explode(",,", $cConteudo);
    				 
    				$linha	= "";
    				$linha .= $cNome . ';';
    				$linha .= $nId . ';';
    				$linha .= $cNF . ';';
    				$linha .= number_format($valor_titulo,2,',','.') . ';';
    				$linha .= $cDataPagamento . ';';
    				$linha .= $data_vencimento . ';';
    				$linha .= number_format($valor_juros_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_desconto_titulo,2,',','.') . ';';
    				$linha .= number_format($valor_credito_titulo,2,',','.') . ';';
                                $linha .= "\r\n";
    				fwrite($handle, $linha);
    				 
    			}
    			//Rodapé dos Títulos Baixados
    			$linha	= "";
    			$linha .= ";;;";
    			$linha .= number_format($nTotal_Baixados,2,',','.');
    			$linha .= "\r\n\r\n";
    			$linha .= 'Resultado da integração com o Protheus: '.$mIntegracao . ';';
                        fwrite($handle, $linha);
    			 
    		}//FIM: if(count($mTitulosBaixados) > 0 )
    		
    		fclose($handle);
    		
    		$dados['status'] = true;
    		$dados['arquivo_gerado'] = $planilhaRelatorio;
    		
    		return 	$dados;
    	
		}
		catch(Exception $e) {
    		
    		fclose($handle);
    		unlink($planilhaRelatorio);
    		
    		$dados['status'] = false;
    		$dados['msg_erro'] = $e->getMessage();
    		
    		return $dados;
    	}
    	
    }
    
    
    /**
     * Envia e-mail previamente parâmetrizado no BD com o resultado da importação do arquivo
     * 
     * @param string $msg
     * @param boolean $status
     * @param object $dadosProcesso
     * @param string $tipo
     * @throws exception
     */
    
    private function enviarEmail($msg, $status, $dadosProcesso, $tipo, $anexo = NULL){
    	
    	$dadosEmail = Array();
    	
    	//instância de classe de configurações de servidores para envio de email
    	//$servicoEnvioEmail = new ServicoEnvioEmail();
    	
    	//usuário AUTOMATICO -> contasareceber@sascar.com.br
        $dadosProcesso->id_usuario='2750';
      	$emailUsuarioProcesso = $this->dao->getDadosUsuarioProcesso($dadosProcesso->id_usuario);

      	if(is_array($emailUsuarioProcesso)){
      		
      		$nomeUsuarioProcesso = $emailUsuarioProcesso[0]['nm_usuario'];
      		
      		//verifica se o usário possui email cadastrado
      		if(empty($emailUsuarioProcesso[0]['usuemail'])){
      			
      			$msg_erro_email = 'Falha ao enviar e-mail : Usuário [ '.$this->dao->usuarioID.' ] que iniciou o processo, não possui e-mail cadastrado.';
      			
      			//finaliza processo com sucesso mas com mensagem de erro de envio de email
      			$finalizarProcesso = $this->dao->finalizarProcesso(true, $msg_erro_email, $dadosProcesso->earoid);
      			
      			return true;
      		
      		}else{
      			
      			$assunto = 'Cobrança Registrada - Retorno';
      			
      			if($status){
      				
      				$corpo_email = 'Sr(a). '.$emailUsuarioProcesso[0]['nm_usuario'].' o processamento do arquivo de retorno foi concluído, segue anexo o relatório de processamento. <br/><br/>';
      				$corpo_email .= $msg;
      				
      			}else{
      				
      				if($tipo == 'pararExecucao'){
      					
      					$assunto = 'Cobrança Registrada - Retorno Relatório Processamento';
      					
						$corpo_email = " Prezado usuário o processamento do arquivo de retorno do banco que você iniciou às  $dadosProcesso->inicio_hora do dia  $dadosProcesso->inicio_data, foi parado pelo usuário  [ $nomeUsuarioProcesso ] e o arquivo foi disponibilizado para processamento novamente. ";
      				
      				}elseif($tipo == 'erro'){
      					
      					$corpo_email = 'Erro no processamento : '.$msg ;
      					
      				}
      				
      			}
      			
				//recupera e-mail de testes
				if($_SESSION['servidor_teste'] == 1){

					//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
					$emailTeste = $this->dao->getEmailTeste();

					if(!is_object($emailTeste)){
						$emailUsuarioProcesso[0]['usuemail'] = 'teste_desenv@sascar.com.br';
						//throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
					}else{
						$emailUsuarioProcesso[0]['usuemail'] = $emailTeste->pcsidescricao;
					}

				}      			
      			
      			$mail = new PHPMailer();
      			$mail->isSMTP();
      			$mail->From = "sascar@sascar.com.br";
      			$mail->FromName = "sistema@sascar.com.br";
      			$mail->Subject = $assunto;
      			$mail->MsgHTML($corpo_email);
      			$mail->ClearAllRecipients();
      			$mail->AddAddress($emailUsuarioProcesso[0]['usuemail']);
      			$mail->AddAttachment($anexo);

      			if(!$mail->Send()) {
      				 
      				$msg_erro_email = $dadosProcesso->msg.' - Falha ao enviar e-mail -';

      				//atualiza o processo com mensagem de erro de envio de email
     				$this->dao->finalizarProcesso(true, $msg_erro_email, $dadosProcesso->earoid);
     					 
      			}
      			
      			return true;
      			
      		}
      	
      	}
    	
    	return false;
    }
	
}
