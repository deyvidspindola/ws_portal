<?php

namespace module\BoletoRegistrado;

use module\BoletoRegistrado\BoletoRegistradoDAO;
use module\BoletoRegistrado\BoletoRegistradoSantander;
use module\Parametro\ParametroCobrancaRegistrada;
use module\Boleto\Agente;
use module\Boleto\BoletoModel;
use module\Boleto\BoletoController;
use module\Boleto\DateTime;
use module\Cliente\ClienteModel;
use module\EventoBoletoRegistro\EventoBoletoRegistroModel;
use module\RegistroOnline\RegistrarBoletoSantander;
use module\TituloCobranca\TituloCobrancaModel;
use infra\Helper\Response;
use infra\Helper\Mascara;
use module\Parametro\ParametroIntegracaoTotvs;

class BoletoRegistradoModel {

	const CODIGO_BANCO_SANTANDER = 33;
	const TIPO_DOCUMENTO_CPF = 1;
	const TIPO_DOCUMENTO_CNPJ = 2;
	const CODIGO_ESPECIE_DM = 2;
	const TIPO_DESCONTO_ISENTO = 0;
	const TIPO_DESCONTO_FIXO = 1;
	const TIPO_DESCONTO_PERCENTUAL = 2;
	const TIPO_DESCONTO_ANTECIPACAO = 3;
	const TIPO_PROTESTO_NAO = 0;
	const TIPO_PROTESTO_DIAS_CORRIDOS = 1;
	const TIPO_PROTESTO_DIAS_UTEIS = 2;
	const TIPO_PROTESTO_PERFIL_CEDENTE = 3;

	const CODIGO_ORIGEM_CNAB = 1;
	const CODIGO_ORIGEM_NEGOCIACAO = 2;
	const CODIGO_ORIGEM_URA_FAX = 3;
	const CODIGO_ORIGEM_PORTAL_SIGGO = 4;
	const CODIGO_ORIGEM_PORTAL_SERVICOS = 5;
	const CODIGO_ORIGEM_PRE_CADASTRO = 6;
	const CODIGO_ORIGEM_SEGUNDA_VIA_SIGGO = 7;
	const CODIGO_ORIGEM_LINK_HISTORICO_CLIENTE = 8;
	const CODIGO_ORIGEM_URA_CONFIRMA_EMAIL = 9;
	const CODIGO_ORIGEM_URA_ENVIA_CODIGO_BARRAS = 10;
	const CODIGO_ORIGEM_CRON_PARCELAS_SIGGO = 11;
	const CODIGO_ORIGEM_RESCISAO = 12;
	const CODIGO_ORIGEM_SALESFORCE = 13;
        const CODIGO_ORIGEM_INTEGRACAO_PROTHEUS = 14;
	const CODIGO_ORIGEM_SUBSTITUICAO = 15;
        
	const FORMA_COBRANCA_REGISTRADA_SANTANDER = 84;
	const CARTEIRA_XML = 101;
	const CARTEIRA_CNAB = 104;
	const IOS = 0;
	const CODIGO_MOVIMENTO_ENTRADA_CONFIRMADA = 2;
	const CODIGO_MOVIMENTO_ENTRADA_REJEITADA = 3;
	const CODIGO_MOVIMENTO_LIQUIDACAO_TITULO_NAO_REGISTRADO = 17;
	const CODIGO_MOVIMENTO_LIQUIDACAO = 06;

	private $dao;

	private $id;
	private $tituloId;
	private $codigoBanco;
	private $codigoConvenio;
	private $tipoDocumento;
	private $cpf;
	private $cnpj;
	private $nome;
	private $endereco;
	private $bairro;
	private $cidade;
	private $uf;
	private $cep;
	private $nossoNumero;
	private $seuNumero;
	private $dataVencimento;
	private $dataEmissao;
	private $codigoEspecie;
	private $valorNominal;
	private $percentualMulta;
	private $quantidadeDiasMulta;
	private $percentualJuros;
	private $tipoDesconto = 0;
	private $valorDesconto = 0;
	private $dataLimiteDesconto = null;
	private $valorAbatimento = 0;
	private $tipoProtesto = 0;
	private $quantidadeDiasProtesto = 0;
	private $quantidadeDiasBaixa;
	private $mensagem;
	private $codigoMovimento;
	private $valorEncargosRetorno;
	private $valorDescontoRetorno;
	private $valorAbatimentoRetorno;
	private $valorIOFRetorno;
	private $valorPago;
	private $valorLiquidoCreditado;
	private $valorOutrasDespesas;
	private $valorOutrosCreditos;
	private $dataOcorrencia;
	private $dataEfetivacaoCredito;
	private $codigoOcorrenciaPagador;
	private $dataOcorrenciaPagador;
	private $valorOcorrenciaPagador;
	private $complementoOcorrenciaPagador;
	private $valorTarifas;
	private $identificadorRetorno;
	private $nsu;
	private $codigoOrigem;
	private $codigoBarras;
	private $linhaDigitavel;
	private $criadoEm;
	private $valorFace;
	private $valorDescontoNegociadoDescritivo;
	private $valorAbatimentoDescritivo;
	private $valorMoraNegociadaDescritivo;
	private $valorOutrosAcrescimosDescritivo;
	private $envioCancelamento;
	private $solicitacaoCancelamento;

	public $boletoRegistradoSantander;

	public $response;

	public function __construct($id = null){

		$this->dao = new BoletoRegistradoDAO();

		if(!empty($id)){
			
			$boleto = self::getById($id);
			
			if(!$boleto){
				throw new \Exception("Boleto não encontrado");				
			}

			return $boleto;

		}else{

			$this->setDataEmissao(date('Y-m-d'));
			$this->setCodigoBanco(self::CODIGO_BANCO_SANTANDER);
			$this->setCodigoEspecie(self::CODIGO_ESPECIE_DM);
			$this->setQuantidadeDiasBaixa(ParametroCobrancaRegistrada::getNumeroDiasBaixaDevolucao());
			$this->setPercentualMulta(ParametroCobrancaRegistrada::getPercentoMultaAposVencer());
			$this->setPercentualJuros(ParametroCobrancaRegistrada::getPercentoJurosAoMes());

		}

	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getTituloId(){
		return $this->tituloId;
	}

	public function setTituloId($tituloId){
		$this->tituloId = $tituloId;
	}

	public function getCodigoBanco(){
		return $this->codigoBanco;
	}

	public function setCodigoBanco($codigoBanco){
		$this->codigoBanco = $codigoBanco;
	}

	public function getCodigoConvenio(){
		return $this->codigoConvenio;
	}

	public function setCodigoConvenio($codigoConvenio){
		$this->codigoConvenio = $codigoConvenio;
	}

	public function getTipoDocumento(){
		return $this->tipoDocumento;
	}

	public function setTipoDocumento($tipoDocumento){
		$this->tipoDocumento = (int)$tipoDocumento;
	}

	public function getCpf(){
		return $this->cpf;
	}

	public function setCpf($cpf){
		$this->cpf = $cpf;
	}

	public function getCnpj(){
		return $this->cnpj;
	}

	public function setCnpj($cnpj){
		$this->cnpj = $cnpj;
	}

	public function getNome(){
		return $this->nome;
	}

	public function setNome($nome){
		$this->nome = $nome;
	}

	public function getEndereco(){
		return $this->endereco;
	}

	public function setEndereco($endereco){
		$this->endereco = $endereco;
	}

	public function getBairro(){
		return $this->bairro;
	}

	public function setBairro($bairro){
		$this->bairro = $bairro;
	}

	public function getCidade(){
		return $this->cidade;
	}

	public function setCidade($cidade){
		$this->cidade = $cidade;
	}

	public function getUf(){
		return $this->uf;
	}

	public function setUf($uf){
		$this->uf = $uf;
	}

	public function getCep(){
		return $this->cep;
	}

	public function setCep($cep){
		$this->cep = $cep;
	}

	public function getNossoNumero(){
		return $this->nossoNumero;
	}

	public function setNossoNumero($nossoNumero){
		$this->nossoNumero = $nossoNumero;
	}

	public function getSeuNumero(){
		return $this->seuNumero;
	}

	public function setSeuNumero($seuNumero){
		$this->seuNumero = $seuNumero;
	}

	public function getDataVencimento($formato = 'Y-m-d'){
		return !is_null($this->dataVencimento) ? date($formato, strtotime($this->dataVencimento)) : null;
	}

	public function setDataVencimento($dataVencimento){
		$this->dataVencimento = $dataVencimento;
	}

	public function getDataEmissao($formato = 'Y-m-d'){
		return !is_null($this->dataEmissao) ? date($formato, strtotime($this->dataEmissao)) : null;
	}

	public function setDataEmissao($dataEmissao){
		$this->dataEmissao = $dataEmissao;
	}

	public function getCodigoEspecie(){
		return $this->codigoEspecie;
	}

	public function setCodigoEspecie($codigoEspecie){
		$this->codigoEspecie = $codigoEspecie;
	}

	public function getValorNominal(){
		return $this->valorNominal;
	}

	public function setValorNominal($valorNominal){
		$this->valorNominal = (float)$valorNominal;
	}

	public function getPercentualMulta(){
		return $this->percentualMulta;
	}

	public function setPercentualMulta($percentualMulta){
		$this->percentualMulta = $percentualMulta;
	}

	public function getQuantidadeDiasMulta(){
		return $this->quantidadeDiasMulta;
	}

	public function setQuantidadeDiasMulta($quantidadeDiasMulta){
		$this->quantidadeDiasMulta = $quantidadeDiasMulta;
	}

	public function getPercentualJuros(){
		return $this->percentualJuros;
	}

	public function setPercentualJuros($percentualJuros){
		$this->percentualJuros = $percentualJuros;
	}

	public function getTipoDesconto(){
		return $this->tipoDesconto;
	}

	public function setTipoDesconto($tipoDesconto){
		$this->tipoDesconto = $tipoDesconto;
	}

	public function getValorDesconto(){
		return $this->valorDesconto;
	}

	public function setValorDesconto($valorDesconto){
		$this->valorDesconto = (float)$valorDesconto;
	}

	public function getDataLimiteDesconto($formato = 'Y-m-d'){
		return !is_null($this->dataLimiteDesconto) ? date($formato, strtotime($this->dataLimiteDesconto)) : null;
	}

	public function setDataLimiteDesconto($dataLimiteDesconto){
		$this->dataLimiteDesconto = $dataLimiteDesconto;
	}

	public function getValorAbatimento(){
		return $this->valorAbatimento;
	}

	public function setValorAbatimento($valorAbatimento){
		$this->valorAbatimento = (float)$valorAbatimento;
	}

	public function getTipoProtesto(){
		return $this->tipoProtesto;
	}

	public function setTipoProtesto($tipoProtesto){
		$this->tipoProtesto = $tipoProtesto;
	}

	public function getQuantidadeDiasProtesto(){
		return $this->quantidadeDiasProtesto;
	}

	public function setQuantidadeDiasProtesto($quantidadeDiasProtesto){
		$this->quantidadeDiasProtesto = $quantidadeDiasProtesto;
	}

	public function getQuantidadeDiasBaixa(){
		return $this->quantidadeDiasBaixa;
	}

	public function setQuantidadeDiasBaixa($quantidadeDiasBaixa){
		$this->quantidadeDiasBaixa = $quantidadeDiasBaixa;
	}

	public function getMensagem(){
		return $this->mensagem;
	}

	public function setMensagem($mensagem){
		$this->mensagem = $mensagem;
	}

	public function getCodigoMovimento(){
		return $this->codigoMovimento;
	}

	public function setCodigoMovimento($codigoMovimento){
		$this->codigoMovimento = $codigoMovimento;
	}

	public function getValorEncargosRetorno(){
		return $this->valorEncargosRetorno;
	}

	public function setValorEncargosRetorno($valorEncargosRetorno){
		$this->valorEncargosRetorno = (float)$valorEncargosRetorno;
	}

	public function getValorDescontoRetorno(){
		return $this->valorDescontoRetorno;
	}

	public function setValorDescontoRetorno($valorDescontoRetorno){
		$this->valorDescontoRetorno = (float)$valorDescontoRetorno;
	}

	public function getValorAbatimentoRetorno(){
		return $this->valorAbatimentoRetorno;
	}

	public function setValorAbatimentoRetorno($valorAbatimentoRetorno){
		$this->valorAbatimentoRetorno = (float)$valorAbatimentoRetorno;
	}

	public function getValorIOFRetorno(){
		return $this->valorIOFRetorno;
	}

	public function setValorIOFRetorno($valorIOFRetorno){
		$this->valorIOFRetorno = (float)$valorIOFRetorno;
	}

	public function getValorPago(){
		return $this->valorPago;
	}

	public function setValorPago($valorPago){
		$this->valorPago = (float)$valorPago;
	}

	public function getValorLiquidoCreditado(){
		return $this->valorLiquidoCreditado;
	}

	public function setValorLiquidoCreditado($valorLiquidoCreditado){
		$this->valorLiquidoCreditado = (float)$valorLiquidoCreditado;
	}

	public function getValorOutrasDespesas(){
		return $this->valorOutrasDespesas;
	}

	public function setValorOutrasDespesas($valorOutrasDespesas){
		$this->valorOutrasDespesas = (float)$valorOutrasDespesas;
	}

	public function getValorOutrosCreditos(){
		return $this->valorOutrosCreditos;
	}

	public function setValorOutrosCreditos($valorOutrosCreditos){
		$this->valorOutrosCreditos = (float)$valorOutrosCreditos;
	}

	public function getDataOcorrencia(){
		return $this->dataOcorrencia;
	}

	public function setDataOcorrencia($dataOcorrencia){
		$this->dataOcorrencia = $dataOcorrencia;
	}

	public function getDataEfetivacaoCredito(){
		return $this->dataEfetivacaoCredito;
	}

	public function setDataEfetivacaoCredito($dataEfetivacaoCredito){
		$this->dataEfetivacaoCredito = $dataEfetivacaoCredito;
	}

	public function getCodigoOcorrenciaPagador(){
		return $this->codigoOcorrenciaPagador;
	}

	public function setCodigoOcorrenciaPagador($codigoOcorrenciaPagador){
		$this->codigoOcorrenciaPagador = $codigoOcorrenciaPagador;
	}

	public function getDataOcorrenciaPagador(){
		return $this->dataOcorrenciaPagador;
	}

	public function setDataOcorrenciaPagador($dataOcorrenciaPagador){
		$this->dataOcorrenciaPagador = $dataOcorrenciaPagador;
	}

	public function getValorOcorrenciaPagador(){
		return $this->valorOcorrenciaPagador;
	}

	public function setValorOcorrenciaPagador($valorOcorrenciaPagador){
		$this->valorOcorrenciaPagador = (float)$valorOcorrenciaPagador;
	}

	public function getComplementoOcorrenciaPagador(){
		return $this->complementoOcorrenciaPagador;
	}

	public function setComplementoOcorrenciaPagador($complementoOcorrenciaPagador){
		$this->complementoOcorrenciaPagador = $complementoOcorrenciaPagador;
	}

	public function getValorTarifas(){
		return $this->valorTarifas;
	}

	public function setValorTarifas($valorTarifas){
		$this->valorTarifas = (float)$valorTarifas;
	}

	public function getIdentificadorRetorno(){
		return $this->identificadorRetorno;
	}

	public function setIdentificadorRetorno($identificadorRetorno){
		$this->identificadorRetorno = $identificadorRetorno;
	}

	public function getNsu(){
		return $this->nsu;
	}

	public function setNsu($nsu){
		$this->nsu = $nsu;
	}

	public function getCodigoOrigem(){
		return $this->codigoOrigem;
	}

	public function setCodigoOrigem($codigoOrigem){
		$this->codigoOrigem = $codigoOrigem;
	}

	public function getCodigoBarras(){
		return $this->codigoBarras;
	}

	public function setCodigoBarras($codigoBarras){
		$this->codigoBarras = $codigoBarras;
	}

	public function getLinhaDigitavel(){
		return $this->linhaDigitavel;
	}

	public function setLinhaDigitavel($linhaDigitavel){
		$this->linhaDigitavel = $linhaDigitavel;
	}

	public function getCriadoEm(){
		return $this->criadoEm;
	}

	public function setCriadoEm($criadoEm){
		$this->criadoEm = $criadoEm;
	}

	public function getValorFace(){
		return $this->valorFace;
	}

	public function setValorFace($valorFace){
		$this->valorFace = $valorFace;
	}

	public function getValorDescontoNegociadoDescritivo(){
		return $this->valorDescontoNegociadoDescritivo;
	}

	public function setValorDescontoNegociadoDescritivo($valorDescontoNegociadoDescritivo){
		$this->valorDescontoNegociadoDescritivo = $valorDescontoNegociadoDescritivo;
	}

	public function getValorAbatimentoDescritivo(){
		return $this->valorAbatimentoDescritivo;
	}

	public function setValorAbatimentoDescritivo($valorAbatimentoDescritivo){
		$this->valorAbatimentoDescritivo = $valorAbatimentoDescritivo;
	}

	public function getValorMoraNegociadaDescritivo(){
		return $this->valorMoraNegociadaDescritivo;
	}

	public function setValorMoraNegociadaDescritivo($valorMoraNegociadaDescritivo){
		$this->valorMoraNegociadaDescritivo = $valorMoraNegociadaDescritivo;
	}

	public function getValorOutrosAcrescimosDescritivo(){
		return $this->valorOutrosAcrescimosDescritivo;
	}

	public function setValorOutrosAcrescimosDescritivo($valorOutrosAcrescimosDescritivo){
		$this->valorOutrosAcrescimosDescritivo = $valorOutrosAcrescimosDescritivo;
	}


	public function get($id){

		$resBoleto = $this->dao->getById($id);

		if(!$resBoleto){
			return null;
		}

		$this->setId($resBoleto['tbreoid']);
		$this->setTituloId($resBoleto['tbretitoid']);
		$this->setCodigoBanco($resBoleto['tbrecod_banco']);
		$this->setCodigoConvenio($resBoleto['tbrecod_convenio']);
		$this->setTipoDocumento($resBoleto['tbretipo_documento']);
		$this->setCpf($resBoleto['tbrecpf']);
		$this->setCnpj($resBoleto['tbrecnpj']);
		$this->setNome($resBoleto['tbrenome']);
		$this->setEndereco($resBoleto['tbreendereco']);
		$this->setBairro($resBoleto['tbrebairro']);
		$this->setCidade($resBoleto['tbrecidade']);
		$this->setUf($resBoleto['tbreuf']);
		$this->setCep($resBoleto['tbrecep']);
		$this->setNossoNumero($resBoleto['tbrenosso_numero']);
		$this->setSeuNumero($resBoleto['tbreseu_numero']);
		$this->setDataVencimento($resBoleto['tbredt_vencimento']);
		$this->setDataEmissao($resBoleto['tbredt_emissao']);
		$this->setCodigoEspecie($resBoleto['tbrecd_especie']);
		$this->setValorNominal($resBoleto['tbrevl_nominal']);
		$this->setPercentualMulta($resBoleto['tbrepct_multa']);
		$this->setQuantidadeDiasMulta($resBoleto['tbreqtd_dias_multa']);
		$this->setPercentualJuros($resBoleto['tbrepct_juros']);
		$this->setTipoDesconto($resBoleto['tbretp_desconto']);
		$this->setValorDesconto($resBoleto['tbrevl_desconto']);
		$this->setDataLimiteDesconto($resBoleto['tbredt_limite_desconto']);
		$this->setValorAbatimento($resBoleto['tbrevl_abatimento']);
		$this->setTipoProtesto($resBoleto['tbretp_protesto']);
		$this->setQuantidadeDiasProtesto($resBoleto['tbreqtd_dias_protesto']);
		$this->setQuantidadeDiasBaixa($resBoleto['tbreqtd_dias_baixa']);
		$this->setMensagem($resBoleto['tbremensagem']);
		$this->setCodigoMovimento($resBoleto['tbrecd_movimento']);
		$this->setValorEncargosRetorno($resBoleto['tbrevl_encargos_retorno']);
		$this->setValorDescontoRetorno($resBoleto['tbrevl_desconto_retorno']);
		$this->setValorAbatimentoRetorno($resBoleto['tbrevl_abatimento_retorno']);
		$this->setValorIOFRetorno($resBoleto['tbrevl_iof_retorno']);
		$this->setValorPago($resBoleto['tbrevl_pago']);
		$this->setValorLiquidoCreditado($resBoleto['tbrevl_liquido_creditado']);
		$this->setValorOutrasDespesas($resBoleto['tbrevl_outras_despesas']);
		$this->setValorOutrosCreditos($resBoleto['tbrevl_outros_creditos']);
		$this->setDataOcorrencia($resBoleto['tbredt_ocorrencia']);
		$this->setDataEfetivacaoCredito($resBoleto['tbredt_efetivacao_credito']);
		$this->setCodigoOcorrenciaPagador($resBoleto['tbrecd_ocorrencia_pagador']);
		$this->setDataOcorrenciaPagador($resBoleto['tbredt_ocorrencia_pagador']);
		$this->setValorOcorrenciaPagador($resBoleto['tbrevl_ocorrencia_pagador']);
		$this->setComplementoOcorrenciaPagador($resBoleto['tbrecomplemento_ocorrencia_pagador']);
		$this->setValorTarifas($resBoleto['tbrevl_tarifas']);
		$this->setIdentificadorRetorno($resBoleto['tbreidentificador_retorno']);
		$this->setNsu($resBoleto['tbrensu']);
		$this->setCodigoOrigem($resBoleto['tbrecd_origem']);
		$this->setCodigoBarras($resBoleto['tbrecd_barras']);
		$this->setLinhaDigitavel($resBoleto['tbrelinha_digitavel']);
		$this->setCriadoEm($resBoleto['tbrecriado_em']);
		$this->setValorFace($resBoleto['tbrevl_face']);
		$this->setValorDescontoNegociadoDescritivo($resBoleto['tbrevl_desconto_negociado_descritivo']);
		$this->setValorAbatimentoDescritivo($resBoleto['tbrevl_abatimento_descritivo']);
		$this->setValorMoraNegociadaDescritivo($resBoleto['tbrevl_mora_negociada_descritivo']);
		$this->setValorOutrosAcrescimosDescritivo($resBoleto['tbrevl_outros_acrescimos_descritivo']);		

		return true;

	}

	public static function getById($id){

		if(empty($id)){
			return null;
		}

		$boleto = new BoletoRegistradoModel();
		return $boleto->get($id) ? $boleto : null;

	}
	
	public function gerarBoletoRegistro(){
		
		$id = $this->dao->inserir(
			$this->getTituloId(),
			$this->getCodigoOrigem(),
			$this->getCodigoBanco(),
			$this->getTipoDocumento(),
			$this->getCpf(),
			$this->getCnpj(),
			$this->getNome(),
			$this->getEndereco(),
			$this->getBairro(),
			$this->getCidade(),
			$this->getUf(),
			$this->getCep(),
			$this->getDataVencimento(),
			$this->getDataEmissao(),
			$this->getCodigoEspecie(),
			$this->getValorNominal(),
			$this->getPercentualMulta(),
			$this->getQuantidadeDiasMulta(),
			$this->getPercentualJuros(),
			$this->getTipoDesconto(),
			$this->getValorDesconto(),
			$this->getDataLimiteDesconto(),
			$this->getValorAbatimento(),
			$this->getTipoProtesto(),
			$this->getQuantidadeDiasProtesto(),
			$this->getQuantidadeDiasBaixa(),
			$this->getMensagem(),
			$this->getValorFace(),
			$this->getValorDescontoNegociadoDescritivo(),
			$this->getValorAbatimentoDescritivo(),
			$this->getValorMoraNegociadaDescritivo(),
			$this->getValorOutrosAcrescimosDescritivo()
		);

		$this->get($id);

		return $id;

	}

	public function atualizarInformacoesCNAB(){
		
		return $this->dao->atualizarInformacoesCNAB(
			$this->getId(),
			$this->getNossoNumero(),
			$this->getCodigoConvenio(),
			$this->getCodigoMovimento(),
			$this->getValorEncargosRetorno(),
			$this->getValorDescontoRetorno(),
			$this->getValorAbatimentoRetorno(),
			$this->getValorIOFRetorno(),
			$this->getValorPago(),
			$this->getValorLiquidoCreditado(),
			$this->getValorOutrasDespesas(),
			$this->getValorOutrosCreditos(),
			$this->getDataEfetivacaoCredito(),
			$this->getDataOcorrencia(),
			$this->getCodigoOcorrenciaPagador(),
			$this->getDataOcorrenciaPagador(),
			$this->getValorOcorrenciaPagador(),
			$this->getComplementoOcorrenciaPagador(),
			$this->getValorTarifas(),
			$this->getCodigoBarras(),
			$this->getLinhaDigitavel()
		);
	}

	public function atualizarInformacoesXML(){
		
		return $this->dao->atualizarInformacoesXML(
			$this->getId(),
			$this->getNossoNumero(),
			$this->getCodigoConvenio(),
			$this->getCodigoMovimento(),
			$this->getCodigoBarras(),
			$this->getLinhaDigitavel(),
			$this->getNsu()
		);

	}

	public static function getUltimoBoletoValido($tituloId){
		$dao = new BoletoRegistradoDAO();
		$boletoId = $dao->getUltimoBoletoValido($tituloId);
		if($boletoId){
			return self::getById($boletoId);
		}else{
			return null;
		}
	}
	
	public static function getStatusIntegracaoTotvsAtiva(){
		$dao = new BoletoRegistradoDAO();
		return $dao->getStatusIntegracaoTotvsAtiva();
	}	

	public function gerarBoletoRegistradoSantander(){
		
		try{

			$cpf_cnpj = $this->getCpf() ? Mascara::mascaraCpf($this->getCpf()) : Mascara::mascaraCnpj($this->getCnpj());
			
			$sacado = new Agente(
				Mascara::removeAcento($this->getNome()),
				$cpf_cnpj, 
				Mascara::removeAcento($this->getEndereco()),
				$this->getCep(), 
				Mascara::removeAcento($this->getCidade()),
				$this->getUf()
			);
		
			$cedente = new Agente(
				'Sascar - Tecnologia e Seguran&ccedil;a Automotiva S/A',
				'03.112.879/0001-51',
				' Alameda Araguaia, 2.104-11&#186; andar - Alphaville Comercial',
				'Barueri',
				'SP',
				'CEP 06455-000'
			);

			$boletoController = new BoletoController();

			$dadosBancarios = $boletoController->getDadosBancarios(
				self::FORMA_COBRANCA_REGISTRADA_SANTANDER,
				$this->getCodigoBanco()
			);

			$formaRegistro = $boletoController->getformaRegistro($this->getTituloId());
			if (!isset($this->codigoConvenio)) {
				if ($formaRegistro == 'CNAB') {
					$this->setCodigoConvenio($dadosBancarios->dados['cfbcodigo_cedente']);
				} elseif ($formaRegistro == 'XML'){
					$this->setCodigoConvenio(ParametroCobrancaRegistrada::getWebserviceCodigoConvenio());
				}
			}

			$boletoModel = new BoletoModel();

			if(is_array($dadosBancarios->dados) && !empty($dadosBancarios->dados)){
				$agencia   = explode("-", $dadosBancarios->dados['cfbagencia']);
				$agencia   = $agencia[0];
				$conta   = explode("-", $dadosBancarios->dados['cfbconta_corrente']);
				$conta   = $conta[0];
			}else{
				return false;
			}

			$titulo = TituloCobrancaModel::getTituloById($this->getTituloId());
			$numeroNotaFiscal = TituloCobrancaModel::getNumeroNotaFiscalByNotaFiscalId($titulo->notaFiscal);
			
			$INTEGRACAO_TOTVS_ATIVA = ParametroIntegracaoTotvs::getIntegracaoTotvsAtiva();
                         
			if($INTEGRACAO_TOTVS_ATIVA && $formaRegistro == 'CNAB'){
                            
                           	$boleto = self::getUltimoBoletoValido($this->getTituloId());				
				$this->setCodigoConvenio($boleto->getCodigoConvenio());
//                                if(isset($boleto->getCodigoConvenio())){
//                                    $this->setCodigoConvenio($boleto->getCodigoConvenio());
//                                }else{
//                                    echo 'Falha de retorno de convenio: '.$this->getTituloId();
//                                    return false;
//                                }                                
			}				

			if($this->getCodigoBanco()){
				switch((int) $this->getCodigoBanco()){
					case self::CODIGO_BANCO_SANTANDER:
						$this->boletoRegistradoSantander = new BoletoRegistradoSantander(
							array(
								'sacado' => $sacado,
								'dataVencimento' => new DateTime($this->getDataVencimento()),
								'valor' => $this->getValorFace(),
								'sequencial' => $this->getTituloId(),
								'carteira' => ($formaRegistro == 'CNAB') ? self::CARTEIRA_CNAB : self::CARTEIRA_XML,
								'ios' => self::IOS,
								'numeroDocumento' => $numeroNotaFiscal,
								'cedente'   => $cedente,
								'agencia'   => $agencia,
								'conta'     => $this->getCodigoConvenio(),
								'instrucoes' => $this->gerarInstrucoes(),
								'descontosAbatimentos' => $this->getValorDescontoNegociadoDescritivo(),
								'outrasDeducoes' => $this->getValorAbatimentoDescritivo(),
								'moraMulta' => $this->getValorMoraNegociadaDescritivo(),
								'outrosAcrescimos' =>  $this->getValorOutrosAcrescimosDescritivo(),
								'valorCobrado' => $this->getValorNominal(),
								'nossoNumero' => str_pad($this->getNossoNumero(),13,'0',STR_PAD_LEFT)
							)
						);	
						return true;
					break;
				}
			} else{
				return false;
			}
		} catch(Exception $e){
			return false;
    	}
	}

	public function imprimir()
	{
		$response = new Response();

		if ($this->gerarBoletoRegistradoSantander()) {
			$response->setResult($this->boletoRegistradoSantander->getOutput(), '0');
		}
		return $response;
	}

	public static function getLinkExibirBoleto($tituloId, $codigoOrigem = null,$operacao = null){
            $rsbb = fopen(_SITEDIR_ . 'arq_financeiro/log_boleto.txt', 'w+');
            fwrite($rsbb,  'titulo:'.$tituloId.' codigo_origem:'.$codigoOrigem.' operacao:'.$operacao.'\r\n'  );
            fclose($rsbb);            

            $boletoParams = base64_encode(serialize(array('titulo' => $tituloId,
                    'codigo_origem' => $codigoOrigem,
                    'operacao' => $operacao)));
                return "verificar_registro_boleto.php?a=$boletoParams";
	}


	public function registrarBoletoOnline(){

		if(empty($this->tituloId)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir um título definido.");
		}

		if(empty($this->codigoOrigem)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir o código de origem definido.");
		}

		if(empty($this->dataVencimento)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir uma data de vencimento definida.");
		}

		if(empty($this->valorFace)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir um valor de face definido.");
		}

		if(empty($this->valorNominal)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir um valor nominal definido.");
		}

		self::solicitarCancelamentoBoletosByTitulo($this->getTituloId());
		
		$tituloObj = TituloCobrancaModel::getTituloById($this->getTituloId());
		
		$clienteModel = new ClienteModel();
		$clienteObj = $clienteModel->getById($tituloObj->clienteId);

		if(!empty($clienteObj->cliente_tipo) && $clienteObj->cliente_tipo === 'F'){
			
			$this->setTipoDocumento(self::TIPO_DOCUMENTO_CPF);

			if(!empty($clienteObj->cliente_cpf)){
				$this->setCpf($clienteObj->cliente_cpf);
			}

		}elseif(!empty($clienteObj->cliente_tipo) && $clienteObj->cliente_tipo === 'J'){

			$this->setTipoDocumento(self::TIPO_DOCUMENTO_CNPJ);

			if(!empty($clienteObj->cliente_cnpj)){
				$this->setCnpj($clienteObj->cliente_cnpj);
			}

		}

		if(!empty($clienteObj->cliente_nome)){
			$this->setNome($clienteObj->cliente_nome);
		}

		if(!empty($clienteObj->endereco_cobranca_logradouro)){
			$enderecoRegistro = "{$clienteObj->endereco_cobranca_logradouro}";
			if (!empty($clienteObj->endereco_cobranca_numero)) {
				$enderecoRegistro .= " {$clienteObj->endereco_cobranca_numero}";
			}
			$this->setEndereco($enderecoRegistro);
		}

		if(!empty($clienteObj->endereco_cobranca_bairro)){
			$this->setBairro($clienteObj->endereco_cobranca_bairro);
		}

		if(!empty($clienteObj->endereco_cobranca_cidade)){
			$this->setCidade($clienteObj->endereco_cobranca_cidade);
		}

		if(!empty($clienteObj->endereco_cobranca_uf)){
			$this->setUf($clienteObj->endereco_cobranca_uf);
		}

		if(!empty($clienteObj->endereco_cobranca_cep)){
			$this->setCep($clienteObj->endereco_cobranca_cep);
		}

		if(empty($this->nome)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir o nome do cliente definido.");
		}

		if(empty($this->endereco)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir o endereÃ§o do cliente definido.");
		}

		if(empty($this->bairro)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir o bairro do cliente definido.");
		}

		if(empty($this->cidade)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir a cidade do cliente definida.");
		}

		if(empty($this->uf)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir o estado do cliente definido.");
		}

		if(empty($this->cep)){
			throw new \Exception("Para efetuar o registro o boleto deve possuir o cep do cliente definido.");
		}

		$this->gerarBoletoRegistro();

		$eventoBoletoRegistro = new EventoBoletoRegistroModel();

		try {

			$registrarBoletoSantander = new RegistrarBoletoSantander();
			$registroSantander = $registrarBoletoSantander->registrarBoleto($this);
		
			$nossoNumero = ltrim($registroSantander->titulo->nossoNumero, '0');
			$codigoConvenio = (int)$registroSantander->convenio->codConv;
			$codigoBarras = $registroSantander->titulo->cdBarra;
			$linhaDigitavel = $registroSantander->titulo->linDig;
			$nsu = $registroSantander->nsu;
            
			$this->setNossoNumero($nossoNumero);
			$this->setCodigoConvenio($codigoConvenio);
			$this->setCodigoMovimento(self::CODIGO_MOVIMENTO_ENTRADA_CONFIRMADA);
			$this->setCodigoBarras($codigoBarras);
			$this->setLinhaDigitavel($linhaDigitavel);
			$this->setNsu($nsu);

			if($this->atualizarInformacoesXML()){

		  		$tipoEventoBoleto = EventoBoletoRegistroModel::getTipoEventoByCodigoRetornoXML(RegistrarBoletoSantander::CODIGO_INCLUSAO_TITULO_OK);

				if(!empty($tipoEventoBoleto)){
					$eventoBoletoRegistro->setBoletoId($this->getId());
					$eventoBoletoRegistro->setCodigoMovimento($tipoEventoBoleto);
					$eventoBoletoRegistro->inserir();
				}

				if(!TituloCobrancaModel::atualizarStatusTitulo($this->getTituloId(), TituloCobrancaModel::TIPO_EVENTO_ENTRADA_CONFIRMADA) || !TituloCobrancaModel::atualizarNumeroRegistroBanco($this->getTituloId(), $this->getNossoNumero())){
					throw new \Exception("Falha ao atualizar informações do título.");
				}

				TituloCobrancaModel::registrarEventoTituloXML($this->getTituloId(), RegistrarBoletoSantander::CODIGO_RETORNO_TICKET_OK);
				
				return true;
					
			}else{
				throw new \Exception("Falha ao atualizar informações do boleto.");
			}

		}catch(\Exception $e){

			if($e->getCode()){

				$tipoEventoBoleto = EventoBoletoRegistroModel::getTipoEventoByCodigoRetornoXML($e->getCode());

				if(!empty($tipoEventoBoleto)){
					$eventoBoletoRegistro->setBoletoId($this->getId());
					$eventoBoletoRegistro->setCodigoMovimento($tipoEventoBoleto);
					$eventoBoletoRegistro->inserir();
					TituloCobrancaModel::registrarEventoTituloXML($this->getTituloId(), $e->getCode());
				}

			}

			$this->setCodigoMovimento(self::CODIGO_MOVIMENTO_ENTRADA_REJEITADA);
			$this->atualizarInformacoesXML();

			throw new \Exception("Não foi possível registrar um boleto para o título ". $this->getTituloId() .". Erro: ". $e->getMessage());

		}

	}

	public static function recalcularValores($dataVencimentoOriginal, $dataVencimentoNova, $valorTitulo, $valorDesconto, $valorAbatimento){

		$dataVencimentoOriginal = new DateTime($dataVencimentoOriginal);
		$dataVencimentoNova  = new DateTime($dataVencimentoNova);
		$intervaloDias = $dataVencimentoOriginal->diff($dataVencimentoNova)->days;

		$valorMulta = 0;
		$valorJuros = 0;
		if ($intervaloDias > 0){
			$valorMulta = round((ParametroCobrancaRegistrada::getPercentoMultaAposVencer() * (float) $valorTitulo) / 100, 2);
			$valorJuros = round((ParametroCobrancaRegistrada::getPercentoJurosAoDia() * (float) $valorTitulo / 100 ) * $intervaloDias , 2);
			if($valorDesconto > 0){
				$valorDesconto = round(($valorMulta + $valorJuros) * $valorDesconto / 100, 2);
			}

			$valorDescontoNegociado = 0;
			$valorMoraNegociada = 0;
			$valorRecalculado = $valorTitulo + $valorMulta + $valorJuros - $valorDesconto - $valorAbatimento;

		}else{
			$valorRecalculado = $valorTitulo;
		}

		$arrayRetorno['valorTitulo'] = $valorTitulo;
		$arrayRetorno['valorRecalculado'] = $valorRecalculado;
		$arrayRetorno['valorDesconto'] = $valorDesconto;
		$arrayRetorno['valorMulta'] = $valorMulta;
		$arrayRetorno['valorJuros'] = $valorJuros;
		$arrayRetorno['valorAbatimento'] = $valorAbatimento;

		return (object) $arrayRetorno;
	}

	/**
	 * Retorna um objeto boleto para tÃ­tulos legados registrados
	 */
	public static function getBoletoByTitulo($tituloId) {

		$titulo = TituloCobrancaModel::getTituloById($tituloId);
        $cliente = new ClienteModel();
        $cliente = (object) $cliente->getById($titulo->clienteId);

        $clienteEnderecoCompleto = $cliente->endereco_cobranca_logradouro . ', ';
        $clienteEnderecoCompleto .= $cliente->endereco_cobranca_numero . ', ';
        $clienteEnderecoCompleto .= $cliente->endereco_cobranca_bairro;

        $abatimentos = $titulo->valorIr + $titulo->valorPiscofins + $titulo->valorIss;
        $outrosAcrescimos = $titulo->valorAcrescimo > 0 ? $titulo->valorAcrescimo : $titulo->valorJuros;

        $valorCobrado = $titulo->valorTitulo;
        $valorCobrado -= $titulo->valorDesconto;
        $valorCobrado -= $abatimentos;
        $valorCobrado += $titulo->valorMulta;
        $valorCobrado += $outrosAcrescimos;

        $boleto = new BoletoRegistradoModel();
        $boleto->setTituloId($titulo->tituloId);

        if (!empty($titulo->configuracaoBanco)) {
            $boleto->setCodigoBanco($titulo->configuracaoBanco);
        } else {
            $boleto->setCodigoBanco(BoletoRegistradoModel::CODIGO_BANCO_SANTANDER);
        }

        $boleto->setDataVencimento($titulo->dataVencimento);
        $boleto->setDataEmissao($titulo->emissao);
        $boleto->setValorNominal($valorCobrado);
        $boleto->setNossoNumero($titulo->tituloId);
        $boleto->setValorDescontoNegociadoDescritivo($titulo->valorDesconto);
        $boleto->setValorAbatimentoDescritivo($abatimentos);
        $boleto->setValorMoraNegociadaDescritivo($titulo->valorMulta);
        $boleto->setValorOutrosAcrescimosDescritivo($outrosAcrescimos);
        $boleto->setValorFace($titulo->valorTitulo);
        $boleto->setCodigoConvenio($codigoConvenio);
        $boleto->setNome($cliente->cliente_nome);
        $boleto->setEndereco($clienteEnderecoCompleto);
        $boleto->setCep($cliente->endereco_cobranca_cep);
        $boleto->setCidade($cliente->endereco_cobranca_cidade);
        $boleto->setUf($cliente->endereco_cobranca_uf);

        if (!empty($cliente->cliente_cpf)) {
            $boleto->setCpf(str_pad($cliente->cliente_cpf, 11, "0", STR_PAD_LEFT));
        } else if (!empty($cliente->cliente_cnpj)) {
            $boleto->setCnpj(str_pad($cliente->cliente_cnpj, 14, "0", STR_PAD_LEFT));
		}
		
		return $boleto;
	}

	public static function buscarBoletosFilaCancelamento($numRows = null, $origem = null){

		$dao = new BoletoRegistradoDAO();

		return $dao->buscarBoletosFilaCancelamento($numRows, $origem);

	}

	public static function solicitarCancelamentoBoletosByTitulo($tituloId){

		$dao = new BoletoRegistradoDAO();

		$boletosAdicionadosFila = $dao->solicitarCancelamentoBoletosByTitulo($tituloId);


		if(!empty($boletosAdicionadosFila)){

			TituloCobrancaModel::atualizarStatusTitulo($tituloId, TituloCobrancaModel::TIPO_EVENTO_PEDIDO_BAIXA);

			foreach($boletosAdicionadosFila as $boleto){

				$boletoId = $boleto['tbreoid'];

				$eventoBoletoRegistro = new EventoBoletoRegistroModel();
				$eventoBoletoRegistro->setBoletoId($boletoId);
				$eventoBoletoRegistro->setTipoEventoBoletoId(EventoBoletoRegistroModel::EVENTO_BOLETO_ADICIONADO_FILA_CANCELAMENTO);
				$eventoBoletoRegistro->inserir();

			}

		}

		return true;

	}

	public static function atualizarEnviadoParaCancelamento($boletoId){

		$dao = new BoletoRegistradoDAO();

		$boletosCancelados = $dao->atualizarEnviadoParaCancelamento($boletoId);

		if($boletosCancelados){

			$eventoBoletoRegistro = new EventoBoletoRegistroModel();
			$eventoBoletoRegistro->setBoletoId($boletoId);
			$eventoBoletoRegistro->setTipoEventoBoletoId(EventoBoletoRegistroModel::EVENTO_BOLETO_PROCESSADO_FILA_CANCELAMENTO);
			$eventoBoletoRegistro->inserir();

		}

		return true;

	}

	private function getBoletoRegistradoSantander(){

		$sacado = new Agente(
			Mascara::removeAcento($this->getNome()),
			$cpf_cnpj, 
			Mascara::removeAcento($this->getEndereco()),
			$this->getCep(), 
			Mascara::removeAcento($this->getCidade()),
			$this->getUf()
		);

		$cedente = new Agente(
			'Sascar - Tecnologia e Seguran&ccedil;a Automotiva S/A',
			'03.112.879/0001-51',
			' Alameda Araguaia, 2.104-11&#186; andar - Alphaville Comercial',
			'Barueri',
			'SP',
			'CEP 06455-000'
		);

		$boletoRegistradoSantander = new BoletoRegistradoSantander(
			array(
				'dataVencimento' => new DateTime($this->dataVencimento),
				'valor' => $this->valorNominal,
				'sequencial' => $this->tituloId,
				'carteira' => in_array($this->codigoOrigem, array(self::CODIGO_ORIGEM_CNAB, self::CODIGO_ORIGEM_INTEGRACAO_PROTHEUS)) ? self::CARTEIRA_CNAB : self::CARTEIRA_XML,
				'ios' => self::IOS,
				'conta' => ltrim($this->codigoConvenio, "0"),
				'cedente' => $cedente,
				'sacado' => $sacado,
				'nossoNumero' => str_pad($this->nossoNumero,13,'0',STR_PAD_LEFT)
			)
		);

		return $boletoRegistradoSantander;


	}

	public function gerarLinhaDigitavel(){

		if(empty($this->codigoOrigem)){
			throw new Exception("Não é possível gerar a linha digitável para boletos sem o nosso número definido.");
		}

		$linhaDigitavel = str_replace(array(" ", "."), "", $this->getBoletoRegistradoSantander()->getLinhaDigitavel());

		$this->setLinhaDigitavel($linhaDigitavel);

		return $linhaDigitavel;

	}

	public function gerarCodigoDeBarras(){

		if(empty($this->codigoOrigem)){
			throw new Exception("Não é possível gerar o código de barras para boletos sem o nosso número definido.");
		}

		$codigoDeBarras = $this->getBoletoRegistradoSantander()->getNumeroFebraban();

		$this->setCodigoBarras($codigoDeBarras);

		return $codigoDeBarras;

	}

	public function gerarInstrucoes(){

		$html = '';

		$percentualMulta = $this->getPercentualMulta();
		$percentualJuros = $this->getPercentualJuros();

		if($this->getValorNominal() < ParametroCobrancaRegistrada::getValorMinimoRegistroBoletoFebraban()){
			$html .= ParametroCobrancaRegistrada::getInstrucaoBoletoVencimentoSantander();			
		}else{
			$html .= ParametroCobrancaRegistrada::getInstrucaoBoletoVencimentoOutrosBancos();			
		}

		$html .= '<br><br>';

		if($percentualMulta != 0){
			$html .= ParametroCobrancaRegistrada::getInstrucaoBoletoMoraJuros();
		}else{
			$html .= ParametroCobrancaRegistrada::getInstrucaoBoletoJuros();
		}

		$html .= '<br>';
		$html .= ParametroCobrancaRegistrada::getInstrucaoBoletoSegundaVia();

		if(!empty($percentualMulta)){
			$percentualMultaFormatado = (float)$percentualMulta;
			$html = str_replace('PCT_MULTA', $percentualMultaFormatado, $html);
		}

		if(!empty($percentualJuros)){
			$percentualJurosFormatado = number_format(($percentualJuros / 30), 3, '.', '');
			$html = str_replace('PCT_JUROS', $percentualJurosFormatado, $html);
		}

		return utf8_encode($html);

	}

}
