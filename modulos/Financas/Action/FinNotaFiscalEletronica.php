<?php

require_once _MODULEDIR_ . "/Financas/DAO/FinFaturamentoUnificadoDAO.php";
require_once _SITEDIR_ . "/lib/phpMailer/class.phpmailer.php";

use module\TituloCobranca\TituloCobrancaDAO;
use module\NotaFiscal\NotaFiscalModel;
use module\NotaFiscalEletronica\NotaFiscalEletronicaModel;
use module\Parametro\ParametroNotaFiscalEletronica;
use module\Parametro\ParametroCobrancaRegistrada;
use module\EscritorRemessaRPSBarueri\EscritorHeaderTipo1ArquivoRemessaRPSBarueriModel;
use module\EscritorRemessaRPSBarueri\EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel;
use module\EscritorRemessaRPSBarueri\EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel;
use module\EscritorRemessaRPSBarueri\EscritorTrailerTipo9ArquivoRemessaRPSBarueriModel;
use module\LeitorRetornoRPSBarueri\LeitorRetornoRPSBarueriModel;
use module\LeitorRetornoErroRPSBarueri\LeitorRetornoErroRPSBarueriModel;

class FinNotaFiscalEletronica
{
	const VIEW_DIR = 'Financas/View/fin_nota_fiscal_eletronica/';
	const CODIGO_ORIGEM_OCORRENCIA_VALIDADOR = 1;
	const CODIGO_ORIGEM_OCORRENCIA_ARQUIVO_RETORNO = 2;

	private $conn;
	private $dao;

	public function __construct()
	{
		global $conn;
		$this->conn = $conn;
		$this->dao  = new FinNotaFiscalEletronicaDAO($conn);
	}

	private function view($viewName)
	{
		include _MODULEDIR_ . self::VIEW_DIR . $viewName;
	}

	private function resJson($data)
	{
		header('Content-Type: application/json');
		$data = array_map("utf8_encode", $data);
		// JSON_UNESCAPED_UNICODE = 256
		echo json_encode($data, 256);
		exit;
	}

	public function index()
	{
		$this->view('index.php');
	}

	public function acompanhamento()
	{
		$this->action = 'acompanhamento';
		$this->view('acompanhamento.php');
	}

	public function pesquisar()
	{

		try {

			$this->acao = "pesquisar";

			$periodoFaturamentoInicial = !empty($_POST['periodo_faturamento_inicial']) ? $_POST['periodo_faturamento_inicial'] : null;
			$periodoFaturamentoFinal = !empty($_POST['periodo_faturamento_final']) ? $_POST['periodo_faturamento_final'] : null;
			$periodoCancelamentoInicial = !empty($_POST['periodo_cancelamento_inicial']) ? $_POST['periodo_cancelamento_inicial'] : null;
			$periodoCancelamentoFinal = !empty($_POST['periodo_cancelamento_final']) ? $_POST['periodo_cancelamento_final'] : null;
			$intervaloNotasInicial = !empty($_POST['intervalo_notas_inicial']) ? $_POST['intervalo_notas_inicial'] : null;
			$intervaloNotasFinal = !empty($_POST['intervalo_notas_final']) ? $_POST['intervalo_notas_final'] : null;
			$situacaoRps = !empty($_POST['situacao_rps']) ? $_POST['situacao_rps'] : null;
			$numeroCpfCnpj = !empty($_POST['numero_cpf_cnpj']) ? $_POST['numero_cpf_cnpj'] : null;
			$numeroNfe = !empty($_POST['numero_nfe']) ? $_POST['numero_nfe'] : null;
			$numeroResultados = !empty($_POST['numero_resultados']) ? $_POST['numero_resultados'] : null;
			$numeroNf = !empty($_POST['numero_nf']) ? $_POST['numero_nf'] : null;
			$somenteNaoEnviadas = isset($_POST['somente_nao_enviadas']) ? (int)$_POST['somente_nao_enviadas'] : 1;
			$layout = !empty($_POST['layout']) ? $_POST['layout'] : null;

			$this->periodoFaturamentoInicial = $periodoFaturamentoInicial;
			$this->periodoFaturamentoFinal = $periodoFaturamentoFinal;
			$this->periodoCancelamentoInicial = $periodoCancelamentoInicial;
			$this->periodoCancelamentoFinal = $periodoCancelamentoFinal;
			$this->intervaloNotasInicial = $intervaloNotasInicial;
			$this->intervaloNotasFinal = $intervaloNotasFinal;
			$this->situacaoRps = $situacaoRps;
			$this->numeroCpfCnpj = $numeroCpfCnpj;
			$this->numeroNfe = $numeroNfe;
			$this->numeroResultados = $numeroResultados;
			$this->numeroNf = $numeroNf;
			$this->somenteNaoEnviadas = $somenteNaoEnviadas;
			$this->layout = $layout;

			if((!empty($periodoFaturamentoInicial) && empty($periodoFaturamentoFinal)) || (empty($periodoFaturamentoInicial) && !empty($periodoFaturamentoFinal))){
				throw new Exception("Informe o início e o fim do faturamento");
			}

			if((!empty($periodoCancelamentoInicial) && empty($periodoCancelamentoFinal)) || (empty($periodoCancelamentoInicial) && !empty($periodoCancelamentoFinal))){
				throw new Exception("Informe o início e o fim do cancelamento");
			}

			if((!empty($intervaloNotasInicial) && empty($intervaloNotasFinal)) || (empty($intervaloNotasInicial) && !empty($intervaloNotasFinal))){
				throw new Exception("Informe o início e o fim do intervalo");
			}

			if(empty($periodoFaturamentoInicial) && empty($periodoFaturamentoFinal) && empty($periodoCancelamentoInicial) && empty($periodoCancelamentoFinal) && empty($intervaloNotasInicial) && empty($intervaloNotasFinal)){
				throw new Exception("Informe ao menos um filtro");
			}

			if(!empty($periodoFaturamentoInicial)){
				$periodoFaturamentoInicial = explode("/", $periodoFaturamentoInicial);
				$periodoFaturamentoInicial = implode("-", array_reverse($periodoFaturamentoInicial));
			}

			if(!empty($periodoFaturamentoFinal)){
				$periodoFaturamentoFinal = explode("/", $periodoFaturamentoFinal);
				$periodoFaturamentoFinal = implode("-", array_reverse($periodoFaturamentoFinal));
			}

			if(!empty($periodoCancelamentoInicial)){
				$periodoCancelamentoInicial = explode("/", $periodoCancelamentoInicial);
				$periodoCancelamentoInicial = implode("-", array_reverse($periodoCancelamentoInicial));
			}

			if(!empty($periodoCancelamentoFinal)){
				$periodoCancelamentoFinal = explode("/", $periodoCancelamentoFinal);
				$periodoCancelamentoFinal = implode("-", array_reverse($periodoCancelamentoFinal));
			}

			$this->ultimaDataEmissao = $this->dao->getUltimaDataEmissao();
			$this->ultimaDataEmissao = implode("/", array_reverse(explode("-", $this->ultimaDataEmissao)));

			$this->notas = $this->dao->pesquisar(
				$periodoFaturamentoInicial,
				$periodoFaturamentoFinal,
				$periodoCancelamentoInicial,
				$periodoCancelamentoFinal,
				$intervaloNotasInicial,
				$intervaloNotasFinal,
				$situacaoRps,
				$numeroCpfCnpj,
				$numeroNfe,
				$numeroNf,
				$somenteNaoEnviadas,
				$numeroResultados
			);

		}catch(Exception $e){
			$this->msgErro = $e->getMessage();
		}

		$this->view('index.php');

	}

	public function gerarRPS(){

		try{

			$this->dao->begin();
			$this->dao->atualizarSequenciaNumeroRemessa();

			$novaDataEmissao = !empty($_POST['nfldt_emissao']) ? implode("-", array_reverse(explode('/', $_POST['nfldt_emissao']))) : null;
			$notasFiscais = $_POST['notas_fiscais'];
			
			if(count($notasFiscais) > 1000){
				throw new Exception("O número de notas selecionadas não pode ultrapassar 1000.");
			}

			global $autenticacaoSistemaUsuarios;

			$arquivo = '';
			$registros = '';
			$contadorLinhas = 0;
			$valorTotalNotas = 0;
			$valorTotalNaoTributados = 0;
			$codigoMovimentoRegistrado = ParametroCobrancaRegistrada::getCodigosMovimentoRegistrado();
			$inscricaoMunicipal = ParametroNotaFiscalEletronica::getCodigoInscricaoContribuinte();
			$numeroRemessaRPS = $this->dao->getSequenciaNumeroRemessa();
			$codigoUsuario = $_SESSION["usuario"]["oid"];
			$notasEnviadasAnteriormente = array();
			$titulosSemBoletoRegistrado = array();

			// Header
			$escritorHeaderRPS = new EscritorHeaderTipo1ArquivoRemessaRPSBarueriModel();
			$escritorHeaderRPS->setInscricaoContribuinte($inscricaoMunicipal);
			$escritorHeaderRPS->setIdentificacaoRemessaContribuinte($numeroRemessaRPS);

			if(!empty($novaDataEmissao)){
				// Se última data de emissao for maior que a data de emissao informada, não pode atualizar.
				$atualizarDataEmissao = !$this->dao->isUltimaDataEmissaoMaiorQueDataEmissaoInformada($novaDataEmissao);
			}

			foreach ($notasFiscais as $notaFiscalId){
				$contadorLinhas++;

				if(!empty($novaDataEmissao)){
					if($atualizarDataEmissao){
						$this->dao->atualizarDataEmissao($notaFiscalId, $novaDataEmissao);
					}else{
						$ultimaDataEmissao = $this->dao->getUltimaDataEmissao();
						$ultimaDataEmissao = implode('/', array_reverse(explode('-', $ultimaDataEmissao)));
						throw new Exception("Não é permitido a geração de notas fiscais com data inferior a de uma nota já enviada. Última da de emissão: {$ultimaDataEmissao}.");
					}
				}else{
					$this->dao->atualizarDataEmissao($notaFiscalId);
				}
				
				\FinFaturamentoUnificadoDAO::calcularImposto($notaFiscalId, $this->conn);

				// $notaFiscalModel = new NotaFiscalModel($notaFiscalId);
				$notaFiscalEletronicaModel = new NotaFiscalEletronicaModel($notaFiscalId);

				$escritorDetalheRPSTipo2 = new EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel();
				$escritorDetalheRPSTipo3 = new EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel();

				$informacoesNotaFiscal = $this->dao->getInformacoesNotaFiscal($notaFiscalId);
				
				if(empty($informacoesNotaFiscal->data_transmissao_rps)){

					$situacaoEnviado = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::SITUACAO_ENVIADO;
					$localPrestacaoNoMunicipio = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::LOCAL_PRESTACAO_SERVICO_NO_MUNICIPIO;
					$localPrestacaoViasNaoPublicas = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::LOCAL_PRESTACAO_SERVICO_VIAS_NAO_PUBLICAS;
					$tipoTomadorBrasileiro = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::TIPO_BRASILEIRO;
					$servicoNaoExportado = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::SERVICO_NAO_EXPORTADO;
					$codigoValorNaoIncluso = EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel::CODIGO_VALOR_NAO_INCLUSO;
					$tipoDocumento = $informacoesNotaFiscal->tipo_documento == 'F' ? EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::TIPO_CPF : EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::TIPO_CNPJ;
					
					$informacoesObrigacaoFinanceira = $this->dao->getInformacoesObrigacaoFinanceira($notaFiscalId);

					$discriminacaoServico = '';

					if(isset($informacoesNotaFiscal->nflinfcomp)){
						$discriminacaoServico .= "Inf Complementares: ". $informacoesNotaFiscal->nflinfcomp."|";
					}
					
					if(!empty($informacoesObrigacaoFinanceira)){
						foreach($informacoesObrigacaoFinanceira as $informacoesItem){
							$discriminacaoServico.= substr($informacoesItem['descricao_sub_grupo'], 0, 84);
							$discriminacaoServico.= " R$ ". substr((number_format($informacoesItem['valor_item'], 2, ",", ".")), 0, 10);
							$discriminacaoServico.= "|";
						}
					}

					// Definir Sascar = 0 / Siggo = 1
					$tipoContrato = $this->dao->buscarTipoContrato($informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);

					$numeroContratosTipoVarejo = count(array_filter($tipoContrato, function($contrato){
						return $contrato->tipo_proposta == 12 || $contrato->super_tipo_contrato == 12;
					}));

					$subCodigoCliente = count($tipoContrato) == $numeroContratosTipoVarejo ? 1 : 0;

					$tituloCobrancaDAO = new TituloCobrancaDAO();

					$nossoNumero = $this->dao->getNossoNumero($informacoesNotaFiscal->titulo_id);

					if($tituloCobrancaDAO->isFormaCobrancaBoleto($informacoesNotaFiscal->titulo_id) && empty($nossoNumero)){
						$titulosSemBoletoRegistrado[] = "O título {$informacoesNotaFiscal->titulo_id} no valor de R$ ". number_format($informacoesNotaFiscal->valor_nf, 2, ",", ".") ." referente a nota {$informacoesNotaFiscal->numero_rps}-{$informacoesNotaFiscal->serie_rps} está sem registro.";
					}

					$banco = $tituloCobrancaDAO->isFormaCobrancaBoleto($informacoesNotaFiscal->titulo_id) && !empty($nossoNumero) ? 9 : 'A';
					$boleto = $informacoesNotaFiscal->numero_parcelas > 1 ? '0' : $subCodigoCliente . $banco . date('Ymd', strtotime($informacoesNotaFiscal->data_vencimento_titulo)) . str_pad($nossoNumero, 13, "0", STR_PAD_LEFT);

					$discriminacaoServico.= "Boleto: ". $boleto ."|"; 
					$discriminacaoServico.= "Valor aproximado dos tributos (Lei 12.741/12): R$ ". number_format($informacoesNotaFiscal->valor_tributos, 2, ",", ".") ."|"; 
					$discriminacaoServico.= "Para mais informações, acesse o portal através do link: www.sascar.com.br/portal"; 		

					// Tipo 2
					$escritorDetalheRPSTipo2->setSerieRPS($informacoesNotaFiscal->serie_rps);
					$escritorDetalheRPSTipo2->setNumeroRPS($informacoesNotaFiscal->numero_rps);
					$escritorDetalheRPSTipo2->setDataRPS($informacoesNotaFiscal->data_rps);
					$escritorDetalheRPSTipo2->setSituacaoRPS($situacaoEnviado);
					$escritorDetalheRPSTipo2->setCodigoServicoPrestado($informacoesNotaFiscal->codigo_servico_prestado);
					$escritorDetalheRPSTipo2->setLocalPrestacaoServico($localPrestacaoNoMunicipio);
					$escritorDetalheRPSTipo2->setServicoPrestadoViasPublicas($localPrestacaoViasNaoPublicas);
					$escritorDetalheRPSTipo2->setQuantidadeServico(1);
					$escritorDetalheRPSTipo2->setValorServico($informacoesNotaFiscal->total_tributados);
					$escritorDetalheRPSTipo2->setTipoTomador($tipoTomadorBrasileiro);
					// $escritorDetalheRPSTipo2->setServicoPrestadoExportacao($servicoNaoExportado);
					$escritorDetalheRPSTipo2->setTipoDocumento($tipoDocumento);
					$escritorDetalheRPSTipo2->setNumeroDocumento($informacoesNotaFiscal->numero_documento);
					$escritorDetalheRPSTipo2->setNomeTomador($informacoesNotaFiscal->nome_tomador);
					$escritorDetalheRPSTipo2->setEndereco($informacoesNotaFiscal->endereco_logradouro);
					$escritorDetalheRPSTipo2->setNumero($informacoesNotaFiscal->endereco_numero);
					$escritorDetalheRPSTipo2->setComplemento($informacoesNotaFiscal->endereco_complemento);
					$escritorDetalheRPSTipo2->setBairro($informacoesNotaFiscal->endereco_bairro);
					$escritorDetalheRPSTipo2->setCidade($informacoesNotaFiscal->endereco_cidade);
					$escritorDetalheRPSTipo2->setUF($informacoesNotaFiscal->endereco_uf);
					$escritorDetalheRPSTipo2->setCEP($informacoesNotaFiscal->endereco_cep);
					$escritorDetalheRPSTipo2->setEmail($this->validarEnvioEmailNfe($informacoesNotaFiscal));
					$escritorDetalheRPSTipo2->setDiscriminacaoServico($discriminacaoServico);

					$registros .= $escritorDetalheRPSTipo2->getRegistro() . "\r\n";

					// Tipo 3
					if($informacoesNotaFiscal->total_nao_tributados > 0){
						$contadorLinhas++;
						$escritorDetalheRPSTipo3->setCodigoDeOutrosValores($codigoValorNaoIncluso);
						$escritorDetalheRPSTipo3->setValor($informacoesNotaFiscal->total_nao_tributados);
						$registros .= $escritorDetalheRPSTipo3->getRegistro() . "\r\n";
					}

					$valorTotalNotas += $escritorDetalheRPSTipo2->getValorServico();
					$valorTotalNaoTributados += $informacoesNotaFiscal->total_nao_tributados;

					$existeNotaFiscalEletronica = $this->dao->verificarExistenciaNotaFiscalEletronicaKernel($informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);

					$idEmpresaAutenticada = $this->dao->getIdEmpresaAutenticada($autenticacaoSistemaUsuarios);

					if($existeNotaFiscalEletronica){
						$this->dao->atualizarNotaFiscalEletronicaKernel($idEmpresaAutenticada, $codigoUsuario, $informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);
					}else{
						$this->dao->inserirNotaFiscalEletronicaKernel($informacoesNotaFiscal->nf_id, $idEmpresaAutenticada, $codigoUsuario, $informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);
					}

				}else{
					$notasEnviadasAnteriormente[] = "{$informacoesNotaFiscal->numero_rps}-{$informacoesNotaFiscal->serie_rps}";
				}

			}

			if(!empty($notasEnviadasAnteriormente)){
				throw new Exception("As notas a seguir já foram enviadas:<br><br>" . implode("<br>", $notasEnviadasAnteriormente));
			}

			$escritorTrailerRPS = new EscritorTrailerTipo9ArquivoRemessaRPSBarueriModel();

			$escritorTrailerRPS->setNumeroTotalLinhasDoArquivo($contadorLinhas + 2);
			$escritorTrailerRPS->setValorTotalServicosContidosNoArquivo($valorTotalNotas);
			$escritorTrailerRPS->setValorTotalRetencoesEOutrosValores($valorTotalNaoTributados);

			$arquivo .= $escritorHeaderRPS->getRegistro() . "\r\n";
			$arquivo .= $registros;
			$arquivo .= $escritorTrailerRPS->getRegistro() . "\r\n";

			// EXEMPLO
			$dataEmissao = !empty($novaDataEmissao) ? $novaDataEmissao : date('Y-m-d');
			$rand = rand(0000, 9999);

			$nomeArquivo = "SASCAR_nfe_kernel_{$dataEmissao}_{$rand}.txt";
			$diretorioArquivo = '/var/www/Kernel/Entrada/';

			$fp = fopen($diretorioArquivo . $nomeArquivo, "w");

			if(!$fp){
				throw new Exception("Não foi possível gerar o arquivo por falta de permissão.");
			}

			fwrite($fp, $arquivo);
			fclose($fp);

			$emailDestino = $_SESSION['servidor_teste'] == 1 ? 'teste_desenv@sascar.com.br' : 'fat-kernel@sascar.com.br';

    		$phpMailer = new PHPMailer();
    		$phpMailer->ClearAllRecipients();
    		$phpMailer->From = "sistema@sascar.com.br";
    		$phpMailer->FromName = "Intranet SASCAR - Email automático";
    		$phpMailer->Subject = "Arquivos para geracao de NFe - Arquivo[$nomeArquivo]";
    		$phpMailer->MsgHTML("Segue anexo.");
    		$phpMailer->AddAddress($emailDestino);
    		$phpMailer->AddAttachment($diretorioArquivo . $nomeArquivo);
    		$phpMailer->Send();

			$this->dao->commit();

			$msgSucesso = "";

			if(!empty($titulosSemBoletoRegistrado)){
				$msgSucesso .= "Algum título relacionado à NF selecionada não possui boleto registrado.<br>";
				$msgSucesso .= implode("<br>", $titulosSemBoletoRegistrado) . "<br>";
			}

			$msgSucesso .= "Arquivo gerado com sucesso.";

			$this->resJson(array(
				'urlArquivo' => $diretorioArquivo . $nomeArquivo,
				'msgSucesso' => $msgSucesso
			));


		}catch(Exception $e){
			$this->dao->rollback();
			$this->resJson(array('msgErro' => $e->getMessage()));
		}

	}
	
	public function processarRetornoSucessoArquivoRPS(){
		
		$this->action='processarRetornoSucessoArquivoRPS';

		try {
			$this->dao->begin();
			if($_FILES["arq_retorno"]["size"] == 0){
				throw new exception('Informe um arquivo');
			}elseif(pathinfo(strtolower($_FILES["arq_retorno"]["name"]), PATHINFO_EXTENSION) != "txt" || $_FILES["arq_retorno"]["type"] != "text/plain" && $_FILES["arq_retorno"]["type"] != "application/octet-stream"){
				throw new exception('Erro: Formato de arquivo deve ser txt');
			}
			$arquivo = fopen($_FILES["arq_retorno"]["tmp_name"], 'r+');

			$contador = 0;
			while(!feof($arquivo)){
			
				$linhaA = stream_get_line($arquivo, (1027 * 60), "\n");
				$tipoRegistro = (int)substr($linhaA, 0, 1);
				$leitorRetornoRPSBarueriModel = new LeitorRetornoRPSBarueriModel();
				
				if($tipoRegistro == 1){
					$registroTipo1 = $leitorRetornoRPSBarueriModel->lerRegistro($linhaA);
					
					if($registroTipo1->getVersaoLayOut() != "PMB002"){
						throw new Exception("O arquivo de retorno informado não possui layout da Prefeitura de Barueri!");
					}
					
				}elseif($tipoRegistro == 2){

					$registroTipo2 = $leitorRetornoRPSBarueriModel->lerRegistro($linhaA);

					$serieNfe = $registroTipo2->getSerieNfe();
					$numeroNfe = $registroTipo2->getNumeroNfe();
					if(empty($numeroNfe)){
						throw new Exception("Erro ao validar. O número da NF-e deve ser informado!");
					}

					$serieRPS = $registroTipo2->getSerieRPS();
					if(empty($serieRPS)){
						throw new Exception("Erro ao validar. O número de Série do RPS deve ser informado!");
					}

					$numeroRPS = $registroTipo2->getNumeroRPS();
					if(empty($numeroRPS)){
						throw new Exception("Erro ao validar. O número do RPS deve ser informado!");
					}
					
					$codigoAutenticidade = $registroTipo2->getCodigoAutenticidade();

					if (!empty($codigoAutenticidade)){
						$link_nfe = 'http://www.barueri.sp.gov.br/nfe/wfimagemNota.aspx?CODIGOAUTENTICIDADE='.$codigoAutenticidade.'&NUMDOC='.$registroTipo2->getNumeroDocumento();
					}else{
						$link_nfe = '';
					}

					$isNfProcessada = $this->dao->isNFProcessada($numeroRPS, $serieRPS);
					if(!$isNfProcessada){
						$this->dao->atualizarCamposNfe($numeroNfe, $link_nfe, $numeroRPS, $serieRPS);
					}else{
						if ($msg_aux_retorno == "") {
							$msg_aux_retorno = "As seguintes NF já foram processadas (Já possuem número NFe):<br>-> $numeroRPS-$serieRPS";
						} else {
							$msg_aux_retorno .= "<br>-> $numeroRPS-$serieRPS";
						}
					}

					$msg = "Arquivo processado com sucesso!$msg_aux_retorno";
					$this->mensagemRetornoSucesso = "Arquivo processado com sucesso!";

					$positionBack = ftell($arquivo);
					$linhaB = stream_get_line($arquivo, (1027 * 60), "\n");    
					$tipoRegistroB = (int)substr($linhaB, 0, 1);
					
				
					if($tipoRegistroB == 3){

						$registroTipo3 = $leitorRetornoRPSBarueriModel->lerRegistro($linhaB);
				
						$linhaC = stream_get_line($arquivo, (1027 * 60), "\n");
						$tipoRegistroC = (int)substr($linhaC, 0, 1);
				
						if($tipoRegistroC == 4){
							$registroTipo4 = $leitorRetornoRPSBarueriModel->lerRegistro($linhaC);
						}else{
							$contador += 1;
							fseek($arquivo, $positionBack);
						}
					}else{
						$contador += 1;
						fseek($arquivo, $positionBack);
					}
				}elseif($tipoRegistro == 9){
					$registroTipo9 = $leitorRetornoRPSBarueriModel->lerRegistro($linhaA);
				}

			}
			$arquivosEncontrados = $contador. " Registro(s) encontrado(s)";
			if(isset($msg_aux_retorno)){
				$this->mensagemRetornoInformativa = $msg_aux_retorno." <br><br>".$arquivosEncontrados;
			}else{
				$this->mensagemRetornoInformativa = $arquivosEncontrados;
			}

			$nome_arquivo_completo = $this->nomearArquivo('retorno', $_FILES["arq_retorno"]["name"]);
			move_uploaded_file($_FILES["arq_retorno"]["tmp_name"], $nome_arquivo_completo);

			$this->dao->commit();
		} catch (Exception $e) {
			$this->dao->rollback();
			$this->mensagemRetornoErro = $e->getMessage();
		}
		
		$this->view('acompanhamento.php');
	}

	public function processarRetornoErroArquivoRPS(){
		
		$this->action='processarRetornoErroArquivoRPS';
		
		try {
			$this->dao->begin();
			if($_FILES["arq_retorno_erro"]["size"] == 0){
				throw new exception('Informe um arquivo');
			}elseif($_FILES["arq_retorno_erro"]["type"] != "text/plain" && $_FILES["arq_retorno_erro"]["type"] != "application/octet-stream"){
				throw new exception('Erro: Arquivo inválido');
			}
			if(pathinfo(strtolower($_FILES["arq_retorno_erro"]["name"]), PATHINFO_EXTENSION) !== "err"){
				throw new exception('Erro: Formato de arquivo deve ser ERR');
			}
			
			$arquivo = fopen($_FILES["arq_retorno_erro"]["tmp_name"], 'r+');

			$arrayLastLine = array_filter(explode("\r\n", file_get_contents($_FILES["arq_retorno_erro"]["tmp_name"])));
			$lastLine = $arrayLastLine[sizeof($arrayLastLine)-1];
			$leitorRetornoErroRPSBarueriModel = new LeitorRetornoErroRPSBarueriModel();
			$registro = $leitorRetornoErroRPSBarueriModel->lerRegistro($lastLine);
			
			if($registro->getTipoRegistro() == 9){
				$codigoErro = $registro->getCodigoErro();
				if(!empty($codigoErro)){
					$mensagem = "Não foi possível processar o arquivo, pois existe(m) o(s) seguinte(s) erro(s):<br>";
					foreach($codigoErro as $erro){
						$mensagemErro = $this->dao->getDescricaoErroByCodigoErro((int)$erro);
						if(isset($mensagemErro)){
							$mensagem .= $mensagemErro."<br>";
						}
					}
					throw new Exception($mensagem);
				}	
			}

			rewind($arquivo);

			$contadorRegistrosProcessados = 0;
			$contadorRegistrosComErros = 0;
			$contadorRegistrosComErrosFile = 0;
			$this->notas_file = array();

			while(!feof($arquivo)){
				
				$linhaA = stream_get_line($arquivo, (1027 * 60), "\n");
				$tipoRegistro = (int)substr($linhaA, 0, 1);
				

				if($tipoRegistro == 1){

					$registroTipo1 = $leitorRetornoErroRPSBarueriModel->lerRegistro($linhaA);
					
					$codigoErro = $registroTipo1->getCodigoErro();
					
					if(!empty($codigoErro)){

						$mensagem = "Não foi possível processar o arquivo, pois existe(m) o(s) seguinte(s) erro(s):<br>";
						foreach($codigoErro as $erro){
							$mensagemErro = $this->dao->getDescricaoErroByCodigoErro((int)$erro);
						
							if(isset($mensagemErro)){
						
								$mensagem .= $mensagemErro."<br>";
							}
						}
						
						throw new Exception($mensagem);
					}
					
				}elseif($tipoRegistro == 2){

					$registroTipo2 = $leitorRetornoErroRPSBarueriModel->lerRegistro($linhaA);
					
					//Inicio - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri
					if(INTEGRACAO_TOTVS_ATIVA == true){

						$codigoErro = $registroTipo2->getCodigoErro();

						foreach($codigoErro as $erro){

							$mensagemErro = $this->dao->getDescricaoErroByCodigoErro((int)$erro);
							
							if(isset($mensagemErro)){
							
								$ocorrencia = $mensagemErro;

							} else {

								throw new exception('Codigo do Erro nao encontrado.');
							}

								$numeroCpfCnpj = $registroTipo2->getnumeroDocumento();
								$tipoDocumento = $registroTipo2->gettipoDocumento();
								
								if (!empty($tipoDocumento)){

									if($tipoDocumento == '1'){ $tipoDocumento = 'F'; }
									if($tipoDocumento == '2'){ $tipoDocumento = 'J'; }

									$sql = "select clioid from clientes where clitipo = '". $tipoDocumento ."' AND (clino_cpf = $numeroCpfCnpj OR clino_cgc = $numeroCpfCnpj) LIMIT 1;";

								} else {
								
									$sql = "select clioid from clientes where (clino_cpf = $numeroCpfCnpj OR clino_cgc = $numeroCpfCnpj) LIMIT 1;";
								}

								if($query = pg_query($this->conn, $sql)) {

									$result = pg_fetch_array($query);
									
									if($result) {

										$clioid = $result['clioid'];
									
									} else {

										//throw new exception('Erro ao consultar CpfCnpj');
										$clioid = "Null";
									}

								} else {

									//throw new exception('Erro ao consultar CpfCnpj');
									$clioid = "Null";
								}

								$data_file = array(
									'numeroRPS' => $registroTipo2->getNumeroRPS(),
									'serieNfe' => $registroTipo2->getSerieRPS(),
									'clioid' => $clioid,
									'nomeTomador' => $registroTipo2->getNomeTomador(),
									'numeroDocumento' => $registroTipo2->getnumeroDocumento(),
									'valorFatura' => $registroTipo2->getvalorFatura(),
									'ocorrencias' => $ocorrencia
								);


							$contadorRegistrosComErrosFile ++;


							$this->notas_file[$contadorRegistrosComErrosFile] = $data_file;

						}
					}
					//Fim - ORGMKTOTVS-826 - ERP - Alterar tela de geração de remessa para a Prefeitura de Barueri


					$arrayIdTabelaCodigoErros = array();
					//$idNotaFiscal = $this->dao->getIdNotaFiscal($registroTipo2->getNumeroRPS(), $registroTipo2->getSerieRPS());

					$codigoErro = $registroTipo2->getCodigoErro();

					if(!empty($codigoErro)){
						//$this->registrarOcorrencia($codigoErro, $idNotaFiscal, $registroTipo2->getNumeroRPS(), $registroTipo2->getSerieRPS());
					}else{
						$this->dao->liberarRegistroParaRPS($registroTipo2->getNumeroRPS(), $registroTipo2->getSerieRPS());
					}

					$positionBack = ftell($arquivo);
					$linhaB = stream_get_line($arquivo, (1027 * 60), "\n");
					$tipoRegistroB = (int)substr($linhaB, 0, 1);
					
					if($tipoRegistroB == 3){
						$registroTipo3 = $leitorRetornoErroRPSBarueriModel->lerRegistro($linhaB);
						
						$codigoErro = $registroTipo3->getCodigoErro();
						if(!empty($codigoErro)){
							
							$contadorRegistrosComErros += 1;
							//$this->registrarOcorrencia($codigoErro, $idNotaFiscal, $registroTipo2->getNumeroRPS(), $registroTipo2->getSerieRPS());
						}else{
							$this->dao->liberarRegistroParaRPS($registroTipo2->getNumeroRPS(), $registroTipo2->getSerieRPS());
						}
						
						$contadorRegistrosProcessados += 1;
				
					}else{

						if(!empty($codigoErro)){
							$contadorRegistrosComErros += 1;
						}

						$contadorRegistrosProcessados += 1;
						fseek($arquivo, $positionBack);
					}

				}elseif($tipoRegistro == 9){

					$registroTipo9 = $leitorRetornoErroRPSBarueriModel->lerRegistro($linhaA);

				}
			}

			$this->contadorRegistrosProcessados = $contadorRegistrosProcessados;
			$this->contadorRegistrosComErros = $contadorRegistrosComErros;
			$this->mensagemRetornoInformativa = $contadorRegistrosProcessados ." Registro(s) encontrado(s) / ". $contadorRegistrosComErrosFile." Erros / ".($contadorRegistrosProcessados - $contadorRegistrosComErrosFile)." processados";
			$this->dao->commit();

		} catch (Exception $e) {
			$this->dao->rollback();
			$this->mensagemRetornoErro = $e->getMessage();
		}

		$this->view('acompanhamento.php');
	}

	public function registrarOcorrencia($arrayCodigoErro, $idNotaFiscal, $numeroRPS, $serieRPS){
		foreach($arrayCodigoErro as $erro){
			$idCodigoErro = $this->dao->getIdTabelaErroByCodigoErro((int)$erro);
			$this->dao->registrarOcorrencia($idCodigoErro, $idNotaFiscal, self::CODIGO_ORIGEM_OCORRENCIA_ARQUIVO_RETORNO);
			$this->dao->atualizarDataRetornoRps($numeroRPS, $serieRPS);
		}
	}

	public function nomearArquivo($tipo, $arquivo) {

		$dir = $tipo == "retorno" ? '/var/www/Kernel/Retorno/' : '/var/www/Kernel/Entrada/';

		$abrir = opendir($dir);

		$files = 0;
		while (false != ($file = readdir($abrir))) {
			if (($file != ".") && ($file != "..") && (!is_dir($file))) {
				$files++;
			}
		}
		$files++;

		return $tipo == "retorno" ? $dir . $files . "_" . $arquivo : $files;

	}

	public function ocorrencias()
	{
		$this->action = 'ocorrencias';

		try{
			if ($_POST['action'] === 'liberarRPS') {
				$this->liberarRPS();
			}
			if (!empty($_POST)) {
				$this->pesquisaOcorrencias();
			}			
		}catch(Exception $e){
			$this->msgErro = $e->getMessage();
		}

		$this->view('ocorrencias.php');
	}

	public function pesquisaOcorrencias()
	{
		try {
			$this->acao = "pesquisaOcorrencias";

			$periodoFaturamentoInicial = !empty($_POST['periodo_faturamento_inicial']) ? $_POST['periodo_faturamento_inicial'] : null;
			$periodoFaturamentoFinal = !empty($_POST['periodo_faturamento_final']) ? $_POST['periodo_faturamento_final'] : null;
			$clienteNome = !empty($_POST['cliente_nome']) ? $_POST['cliente_nome'] : null;
			$numeroCpfCnpj = !empty($_POST['numero_cpf_cnpj']) ? $_POST['numero_cpf_cnpj'] : null;
			$numeroNf = !empty($_POST['numero_nf']) ? $_POST['numero_nf'] : null;
			
			$this->periodoFaturamentoInicial = $periodoFaturamentoInicial;
			$this->periodoFaturamentoFinal = $periodoFaturamentoFinal;
			$this->clienteNome = $clienteNome;
			$this->numeroCpfCnpj = $numeroCpfCnpj;
			$numeroCpfCnpj = !empty($numeroCpfCnpj) ? str_replace(array('-', '.', '/'), '', $numeroCpfCnpj) : null;

			$this->numeroNf = $numeroNf;

			if((!empty($periodoFaturamentoInicial) && empty($periodoFaturamentoFinal)) || (empty($periodoFaturamentoInicial) && !empty($periodoFaturamentoFinal))){
				throw new Exception("Informe o início e o fim do intervalo");
			}

			if(empty($periodoFaturamentoInicial) && empty($periodoFaturamentoFinal) && empty($clienteNome) && empty($numeroCpfCnpj) && empty($numeroNf)){
				throw new Exception("Informe ao menos um filtro");
			}

			if(!empty($numeroNf) && !is_numeric($numeroNf)){
				throw new Exception("Número NF incorreto");
			}

			if(!empty($numeroCpfCnpj) && !is_numeric($numeroCpfCnpj)){
				throw new Exception("Número CPF/CNPJ incorreto");
			}

			if(!empty($periodoFaturamentoInicial)){
				$periodoFaturamentoInicial = explode("/", $periodoFaturamentoInicial);
				$periodoFaturamentoInicial = implode("-", array_reverse($periodoFaturamentoInicial));
			}

			if(!empty($periodoFaturamentoFinal)){
				$periodoFaturamentoFinal = explode("/", $periodoFaturamentoFinal);
				$periodoFaturamentoFinal = implode("-", array_reverse($periodoFaturamentoFinal));
			}

			$this->notas = $this->dao->pesquisarOcorrencias(
				$periodoFaturamentoInicial,
				$periodoFaturamentoFinal,
				$clienteNome,
				$numeroCpfCnpj,
				$numeroNf
			);

		}catch(Exception $e){
			$this->msgErro = $e->getMessage();
		}
	}

	public function liberarRPS()
	{
		try{
			$notasFiscais = $_POST['notas_fiscais'];

			if(empty($notasFiscais)){
				throw new Exception("Selecione uma nota fiscal ou mais.");
			}

			$this->dao->begin();
			$this->dao->liberarRPS($notasFiscais);
			$this->dao->removerOcorrencias($notasFiscais);
			$this->dao->commit();
			$this->msgSucesso = "Registros processados com sucesso!";
			
		} catch (Exception $e) {
			$this->dao->rollback();
			$this->msgErro =  $e->getMessage();
		}
	}
	
	/***
	 * Realiza a validação dos emails para envio de NF-e, agrupa em 3 posições no layout da remessa
	 * Valida tamanho de todos o caracteres que compõe os emails para envio dos e-mails na remessa
	 * 
	 * @param object $emails
	 * @throws Exception
	 */
	private function validarEnvioEmailNfe($emails){
		
		//$emailTeste = $_SESSION['servidor_teste'] == 1 ? 'teste_desenv@sascar.com.br' : NULL;
		//$emails =  new stdClass();
		//$emails->cliemail_nfe = 'tm.brteste_desenesenv@sasteste_debrcom.brtesttfjghjfj';
		//$emails->cliemail_nfe1 = 'tm.brteste_desenesenv@sasteste_debrcom.brtest';
		//$emails->cliemail_nfe2 = 'tm.brteste_desenesenv@sasteste_debrcom.brteste_desenvteste_desenv@s12';
		
		$email = '';
		$pipe = '|';
		$tamMaximo = 150;
		$qtd_nfe = 0;
		$qtd_nfe1 = 0;
		$qtd_nfe2 = 0;
		
		try {
			
			if(!is_object($emails)){
				throw new Exception("Objeto e-mail NF-e inválido.");
			}
			
			if(empty($emails->cliemail_nfe) && empty($emails->cliemail_nfe1) && empty($emails->cliemail_nfe2)){
				throw new Exception("Informar ao menos um e-mail para envio da NF-e.");
			}
			
			if(!empty($emails->cliemail_nfe)){
				//retira os pipes, pois é usado como separador de email
				$emailNfe = str_replace("|","",$emails->cliemail_nfe);
				$email0 = $emailNfe;
			}
			
			if(!empty($emails->cliemail_nfe1)){
				//retira os pipes, pois é usado como separador de email
				$emailNfe1 = str_replace("|","",$emails->cliemail_nfe1);
				$email1 = $emailNfe1;
			}
			
			if(!empty($emails->cliemail_nfe2)){
				//retira os pipes, pois é usado como separador de email
				$emailNfe2 = str_replace("|","",$emails->cliemail_nfe2);
				$email2 = $emailNfe2;
			}
			
			
			if(!empty($email0) && !empty($email1) &&  !empty($email2)){
				$email = $email0.$pipe.$email1.$pipe.$email2;
			}elseif(!empty($email0) && !empty($email1)){
				$email = $email0.$pipe.$email1;
			}elseif (!empty($email0) && !empty($email2)){
				$email = $email0.$pipe.$email2;
			}elseif(!empty($email1) && !empty($email2)){
				$email = $email1.$pipe.$email2;
			}else{
				
				if(!empty($email0)){
					$email = $email0;
				}elseif (!empty($email1)){
					$email = $email1;
				}elseif (!empty($email2)){
					$email = $email2;
				}
			}
			
			//verifica o tamanho total de todos os emails, não pode ultrapassar o tamanho máximo,
			//escolhe os emails que ocupam o espaço disponível, caso não caiba, não vai para o arquivo
			if(strlen($email) > $tamMaximo){
				
				//remonta os emails de acordo com a quantidade de caracteres
				$email = '';
				
				$qtd_nfe  = strlen($email0);
				$qtd_nfe1 = strlen($email1);
				$qtd_nfe2 = strlen($email2);
				
				if($qtd_nfe > 0 && ($qtd_nfe <= $tamMaximo)){
					$email = $emailNfe;
					if($qtd_nfe1 > 0 && ($qtd_nfe + $qtd_nfe1) <= $tamMaximo){
						$email = $emailNfe.$pipe.$emailNfe1;
					}
					
				}elseif($qtd_nfe1 > 0 && ($qtd_nfe1 <= $tamMaximo)){
					$email = $emailNfe1;
					if($qtd_nfe2 > 0 && ($qtd_nfe1 + $qtd_nfe2) <= $tamMaximo){
						$email = $emailNfe1.$pipe.$emailNfe2;
					}
					
				}elseif($qtd_nfe2 > 0 && ($qtd_nfe2 <= $tamMaximo)){
					$email = $emailNfe2;
					if($qtd_nfe > 0 && ($qtd_nfe2 + $qtd_nfe) <= $tamMaximo){
						$email = $emailNfe2.$pipe.$emailNfe;
					}
				}
			}
			
			
			if($emailTeste != NULL){
				$email = $emailTeste;
			}
			
			return $email;
			
		} catch (Exception $e) {
			$this->msgErro = $e->getMessage(); 
		}
		
	}
	
	

}
