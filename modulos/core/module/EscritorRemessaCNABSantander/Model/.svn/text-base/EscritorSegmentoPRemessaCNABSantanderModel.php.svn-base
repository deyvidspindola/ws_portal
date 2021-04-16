<?php

namespace module\EscritorRemessaCNABSantander;

use module\EscritorRemessaCNABSantander\EscritorComumCNABSantanderModel;

class EscritorSegmentoPRemessaCNABSantanderModel extends EscritorComumCNABSantanderModel {

	const TIPO_DOCUMENTO_TRADICIONAL = 1;
	const TIPO_DOCUMENTO_ESCRITURAL = 2;

	const TIPO_COBRANCA_SIMPLES = 1;

	const FORMA_CADASTRAMENTO_COBRANCA_REGISTRADA = 1;
	const FORMA_CADASTRAMENTO_COBRANCA_SEM_REGISTRO = 2;
	const FORMA_CADASTRAMENTO_COBRANCA_SIMPLES = 3;
	const ESPECIE_TITULO_DM = 2;
	const CODIGO_MORA_PERCENTUAL = 2;
	const CODIGO_MOVIMENTO_PEDIDO_BAIXA = 2;
	const CODIGO_DESCONTO_ISENTO = 0;
	const CODIGO_DESCONTO_VALOR_FIXO = 1;
	const CODIGO_PROTESTO_NAO_PROTESTAR = 0;
	const CODIGO_BAIXA_DEVOLUCAO_BAIXAR_DEVOLVER = 1;
	const CODIGO_MOEDA_REAL = "00";

	private $codigoBancoCompensacao = '033';
	private $numeroLoteRemessa;
	private $tipoRegistro = 3;
	private $numeroSequencialRegistroLote;
	private $codigoSegmentoRegistroDetalhe = 'P';
	private $codigoMovimentoRemessa;
	private $agenciaDestinataria;
	private $digitoAgenciaDestinataria;
	private $numeroContaCorrente;
	private $digitoVerificadorContaCorrente;
	private $contaCobrancaDestinatariaFIDC;
	private $digitoContaCobrancaDestinatariaFIDC;
	private $identificacaoTituloBanco;
	private $tipoCobranca;
	private $formaCadastramento;
	private $tipoDocumento;
	private $numeroDocumento;
	private $dataVencimentoTitulo;
	private $valorNominalTitulo;
	private $agenciaEncarregadaCobrancaFIDC;
	private $digitoAgenciaBeneficiarioFIDC;
	private $especieTitulo;
	private $identificacaoTituloAceitoNaoAceito = 'N';
	private $dataEmissaoTitulo;
	private $codigoJurosMora;
	private $dataJurosMora;
	private $valorMora;
	private $codigoDesconto;
	private $dataDesconto;
	private $valorDesconto;
	private $percentualIOFRecolhido;
	private $valorAbatimento;
	private $identificacaoTituloEmpresa;
	private $codigoProtesto;
	private $numeroDiasProtesto;
	private $codigoBaixaDevolucao;
	private $numeroDiasBaixaDevolucao;
	private $codigoMoeda;

	public function setNumeroLoteRemessa($numeroLoteRemessa){
		$this->numeroLoteRemessa = $numeroLoteRemessa;
	}

	public function getNumeroLoteRemessa(){
		return $this->numeroLoteRemessa;
	}

	public function setNumeroSequencialRegistroLote($numeroSequencialRegistroLote){
		$this->numeroSequencialRegistroLote = $numeroSequencialRegistroLote;
	}

	public function getNumeroSequencialRegistroLote(){
		return $this->numeroSequencialRegistroLote;
	}

	public function setCodigoMovimentoRemessa($codigoMovimentoRemessa){
		$this->codigoMovimentoRemessa = $codigoMovimentoRemessa;
	}

	public function getCodigoMovimentoRemessa(){
		return $this->codigoMovimentoRemessa;
	}

	public function setAgenciaDestinataria($agenciaDestinataria){
		$this->agenciaDestinataria = $agenciaDestinataria;
	}

	public function getAgenciaDestinataria(){
		return $this->agenciaDestinataria;
	}

	public function setDigitoAgenciaDestinataria($digitoAgenciaDestinataria){
		$this->digitoAgenciaDestinataria = $digitoAgenciaDestinataria;
	}

	public function getDigitoAgenciaDestinataria(){
		return $this->digitoAgenciaDestinataria;
	}

	public function setNumeroContaCorrente($numeroContaCorrente){
		$this->numeroContaCorrente = $numeroContaCorrente;
	}

	public function getNumeroContaCorrente(){
		return $this->numeroContaCorrente;
	}

	public function setDigitoVerificadorContaCorrente($digitoVerificadorContaCorrente){
		$this->digitoVerificadorContaCorrente = $digitoVerificadorContaCorrente;
	}

	public function getDigitoVerificadorContaCorrente(){
		return $this->digitoVerificadorContaCorrente;
	}

	public function setContaCobrancaDestinatariaFIDC($contaCobrancaDestinatariaFIDC){
		$this->contaCobrancaDestinatariaFIDC = $contaCobrancaDestinatariaFIDC;
	}

	public function getContaCobrancaDestinatariaFIDC(){
		return $this->contaCobrancaDestinatariaFIDC;
	}

	public function setDigitoContaCobrancaDestinatariaFIDC($digitoContaCobrancaDestinatariaFIDC){
		$this->digitoContaCobrancaDestinatariaFIDC = $digitoContaCobrancaDestinatariaFIDC;
	}

	public function getDigitoContaCobrancaDestinatariaFIDC(){
		return $this->digitoContaCobrancaDestinatariaFIDC;
	}

	public function setIdentificacaoTituloBanco($identificacaoTituloBanco){
		$this->identificacaoTituloBanco = $identificacaoTituloBanco;
	}

	public function getIdentificacaoTituloBanco(){
		return $this->identificacaoTituloBanco;
	}

	public function setTipoCobranca($tipoCobranca){
		$this->tipoCobranca = $tipoCobranca;
	}

	public function getTipoCobranca(){
		return $this->tipoCobranca;
	}

	public function setFormaCadastramento($formaCadastramento){
		$this->formaCadastramento = $formaCadastramento;
	}

	public function getFormaCadastramento(){
		return $this->formaCadastramento;
	}

	public function setTipoDocumento($tipoDocumento){
		$this->tipoDocumento = $tipoDocumento;
	}

	public function getTipoDocumento(){
		return $this->tipoDocumento;
	}

	public function setNumeroDocumento($numeroDocumento){
		$this->numeroDocumento = $numeroDocumento;
	}

	public function getNumeroDocumento(){
		return $this->numeroDocumento;
	}

	public function setDataVencimentoTitulo($dataVencimentoTitulo){
		$this->dataVencimentoTitulo = $dataVencimentoTitulo;
	}

	public function getDataVencimentoTitulo(){
		return $this->dataVencimentoTitulo;
	}

	public function setValorNominalTitulo($valorNominalTitulo){
		$this->valorNominalTitulo = $valorNominalTitulo;
	}

	public function getValorNominalTitulo(){
		return $this->valorNominalTitulo;
	}

	public function setAgenciaEncarregadaCobrancaFIDC($agenciaEncarregadaCobrancaFIDC){
		$this->agenciaEncarregadaCobrancaFIDC = $agenciaEncarregadaCobrancaFIDC;
	}

	public function getAgenciaEncarregadaCobrancaFIDC(){
		return $this->agenciaEncarregadaCobrancaFIDC;
	}

	public function setDigitoAgenciaBeneficiarioFIDC($digitoAgenciaBeneficiarioFIDC){
		$this->digitoAgenciaBeneficiarioFIDC = $digitoAgenciaBeneficiarioFIDC;
	}

	public function getDigitoAgenciaBeneficiarioFIDC(){
		return $this->digitoAgenciaBeneficiarioFIDC;
	}

	public function setEspecieTitulo($especieTitulo){
		$this->especieTitulo = $especieTitulo;
	}

	public function getEspecieTitulo(){
		return $this->especieTitulo;
	}

	public function setDataEmissaoTitulo($dataEmissaoTitulo){
		$this->dataEmissaoTitulo = $dataEmissaoTitulo;
	}

	public function getDataEmissaoTitulo(){
		return $this->dataEmissaoTitulo;
	}

	public function setCodigoJurosMora($codigoJurosMora){
		$this->codigoJurosMora = $codigoJurosMora;
	}

	public function getCodigoJurosMora(){
		return $this->codigoJurosMora;
	}

	public function setDataJurosMora($dataJurosMora){
		$this->dataJurosMora = $dataJurosMora;
	}

	public function getDataJurosMora(){
		return $this->dataJurosMora;
	}

	public function setValorMora($valorMora){
		$this->valorMora = $valorMora;
	}

	public function getValorMora(){
		return $this->valorMora;
	}

	public function setCodigoDesconto($codigoDesconto){
		$this->codigoDesconto = $codigoDesconto;
	}

	public function getCodigoDesconto(){
		return $this->codigoDesconto;
	}

	public function setDataDesconto($dataDesconto){
		$this->dataDesconto = $dataDesconto;
	}

	public function getDataDesconto(){
		return $this->dataDesconto;
	}

	public function setValorDesconto($valorDesconto){
		$this->valorDesconto = $valorDesconto;
	}

	public function getValorDesconto(){
		return $this->valorDesconto;
	}

	public function setPercentualIOFRecolhido($percentualIOFRecolhido){
		$this->percentualIOFRecolhido = $percentualIOFRecolhido;
	}

	public function getPercentualIOFRecolhido(){
		return $this->percentualIOFRecolhido;
	}

	public function setValorAbatimento($valorAbatimento){
		$this->valorAbatimento = $valorAbatimento;
	}

	public function getValorAbatimento(){
		return $this->valorAbatimento;
	}

	public function setIdentificacaoTituloEmpresa($identificacaoTituloEmpresa){
		$this->identificacaoTituloEmpresa = $identificacaoTituloEmpresa;
	}

	public function getIdentificacaoTituloEmpresa(){
		return $this->identificacaoTituloEmpresa;
	}

	public function setCodigoProtesto($codigoProtesto){
		$this->codigoProtesto = $codigoProtesto;
	}

	public function getCodigoProtesto(){
		return $this->codigoProtesto;
	}

	public function setNumeroDiasProtesto($numeroDiasProtesto){
		$this->numeroDiasProtesto = $numeroDiasProtesto;
	}

	public function getNumeroDiasProtesto(){
		return $this->numeroDiasProtesto;
	}

	public function setCodigoBaixaDevolucao($codigoBaixaDevolucao){
		$this->codigoBaixaDevolucao = $codigoBaixaDevolucao;
	}

	public function getCodigoBaixaDevolucao(){
		return $this->codigoBaixaDevolucao;
	}

	public function setNumeroDiasBaixaDevolucao($numeroDiasBaixaDevolucao){
		$this->numeroDiasBaixaDevolucao = $numeroDiasBaixaDevolucao;
	}

	public function getNumeroDiasBaixaDevolucao(){
		return $this->numeroDiasBaixaDevolucao;
	}

	public function setCodigoMoeda($codigoMoeda){
		$this->codigoMoeda = $codigoMoeda;
	}

	public function getCodigoMoeda(){
		return $this->codigoMoeda;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
		$linha .= $this->formatNumeric($this->numeroLoteRemessa, 4);
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatNumeric($this->numeroSequencialRegistroLote, 5);
		$linha .= $this->formatAlphanumeric($this->codigoSegmentoRegistroDetalhe, 1);
		$linha .= $this->formatAlphanumeric("", 1);
		$linha .= $this->formatNumeric($this->codigoMovimentoRemessa, 2);
		$linha .= $this->formatNumeric($this->agenciaDestinataria, 4);
		$linha .= $this->formatNumeric($this->digitoAgenciaDestinataria, 1);
		$linha .= $this->formatNumeric($this->numeroContaCorrente, 9);
		$linha .= $this->formatNumeric($this->digitoVerificadorContaCorrente, 1);
		$linha .= $this->formatNumeric($this->contaCobrancaDestinatariaFIDC, 9);
		$linha .= $this->formatNumeric($this->digitoContaCobrancaDestinatariaFIDC, 1);
		$linha .= $this->formatAlphanumeric("", 2);
		$linha .= $this->formatNumeric($this->identificacaoTituloBanco, 13);
		$linha .= $this->formatAlphanumeric($this->tipoCobranca, 1);
		$linha .= $this->formatNumeric($this->formaCadastramento, 1);
		$linha .= $this->formatNumeric($this->tipoDocumento, 1);
		$linha .= $this->formatAlphanumeric("", 1);
		$linha .= $this->formatAlphanumeric("", 1);
		$linha .= $this->formatAlphanumeric($this->numeroDocumento, 15);
		$linha .= $this->formatDate($this->dataVencimentoTitulo, 8);
		$linha .= $this->formatNumeric($this->valorNominalTitulo, 15, 2);
		$linha .= $this->formatNumeric($this->agenciaEncarregadaCobrancaFIDC, 4);
		$linha .= $this->formatNumeric($this->digitoAgenciaBeneficiarioFIDC, 1);
		$linha .= $this->formatAlphanumeric("", 1);
		$linha .= $this->formatNumeric($this->especieTitulo, 2);
		$linha .= $this->formatAlphanumeric($this->identificacaoTituloAceitoNaoAceito, 1);
		$linha .= $this->formatDate($this->dataEmissaoTitulo, 8);
		$linha .= $this->formatNumeric($this->codigoJurosMora, 1);
		$linha .= $this->formatDate($this->dataJurosMora, 8);
		$linha .= $this->formatNumeric($this->valorMora, 15, 2);
		$linha .= $this->formatNumeric($this->codigoDesconto, 1);
		$linha .= $this->formatDate($this->dataDesconto, 8);
		$linha .= $this->formatNumeric($this->valorDesconto, 15, 2);
		$linha .= $this->formatNumeric($this->percentualIOFRecolhido, 15, 5);
		$linha .= $this->formatNumeric($this->valorAbatimento, 15, 2);
		$linha .= $this->formatAlphanumeric($this->identificacaoTituloEmpresa, 25);
		$linha .= $this->formatNumeric($this->codigoProtesto, 1);
		$linha .= $this->formatNumeric($this->numeroDiasProtesto, 2);
		$linha .= $this->formatNumeric($this->codigoBaixaDevolucao, 1);
		$linha .= $this->formatNumeric("", 1);
		$linha .= $this->formatNumeric($this->numeroDiasBaixaDevolucao, 2);
		$linha .= $this->formatNumeric($this->codigoMoeda, 2);
		$linha .= $this->formatAlphanumeric("", 11);

		return $linha;

	}

}