<?php

namespace module\LeitorRetornoCNABSantander;

class DetalheRetornoCNABSantanderModel {

	private $codigoBancoCompensacao;
	private $numeroLoteRetorno;
	private $tipoRegistro;
	private $numeroSequencialRegistroLote;
	private $codigoSegmentoRegistroDetalhe;
	private $codigoMovimento;
	private $agenciaBeneficiario;
	private $digitoAgenciaBeneficiario;
	private $numeroContaCorrente;
	private $digitoContaCorrente;
	private $nossoNumero;
	private $codigoCarteira;
	private $seuNumero;
	private $dataVencimentoTitulo;
	private $valorNominalTitulo;
	private $numeroBancoCobradorRecebedor;
	private $agenciaCobradoraRecebedora;
	private $digitoAgenciaCobradoraRecebedora;
	private $identificacaoTituloEmpresa;
	private $codigoMoeda;
	private $tipoInscricaoPagador;
	private $numeroInscricaoPagador;
	private $nomePagador;
	private $contaCobranca;
	private $valorTarifaCustas;
	private $identificacaoOcorrencia;
	private $valorJurosMultaEncargos;
	private $valorDescontoConcedido;
	private $valorAbatimento;
	private $valorIOF;
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

	public function getCodigoBancoCompensacao(){
		return $this->codigoBancoCompensacao;
	}

	public function setCodigoBancoCompensacao($codigoBancoCompensacao){
		$this->codigoBancoCompensacao = $codigoBancoCompensacao;
	}

	public function getNumeroLoteRetorno(){
		return $this->numeroLoteRetorno;
	}

	public function setNumeroLoteRetorno($numeroLoteRetorno){
		$this->numeroLoteRetorno = $numeroLoteRetorno;
	}

	public function getTipoRegistro(){
		return (int)$this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getNumeroSequencialRegistroLote(){
		return $this->numeroSequencialRegistroLote;
	}

	public function setNumeroSequencialRegistroLote($numeroSequencialRegistroLote){
		$this->numeroSequencialRegistroLote = $numeroSequencialRegistroLote;
	}

	public function getCodigoSegmentoRegistroDetalhe(){
		return $this->codigoSegmentoRegistroDetalhe;
	}

	public function setCodigoSegmentoRegistroDetalhe($codigoSegmentoRegistroDetalhe){
		$this->codigoSegmentoRegistroDetalhe = $codigoSegmentoRegistroDetalhe;
	}

	public function getCodigoMovimento(){
		return $this->codigoMovimento;
	}

	public function setCodigoMovimento($codigoMovimento){
		$this->codigoMovimento = (int)$codigoMovimento;
	}

	public function getAgenciaBeneficiario(){
		return $this->agenciaBeneficiario;
	}

	public function setAgenciaBeneficiario($agenciaBeneficiario){
		$this->agenciaBeneficiario = $agenciaBeneficiario;
	}

	public function getDigitoAgenciaBeneficiario(){
		return $this->digitoAgenciaBeneficiario;
	}

	public function setDigitoAgenciaBeneficiario($digitoAgenciaBeneficiario){
		$this->digitoAgenciaBeneficiario = $digitoAgenciaBeneficiario;
	}

	public function getNumeroContaCorrente(){
		return $this->numeroContaCorrente;
	}

	public function setNumeroContaCorrente($numeroContaCorrente){
		$this->numeroContaCorrente = $numeroContaCorrente;
	}

	public function getDigitoContaCorrente(){
		return $this->digitoContaCorrente;
	}

	public function setDigitoContaCorrente($digitoContaCorrente){
		$this->digitoContaCorrente = $digitoContaCorrente;
	}

	public function getNossoNumero(){
		return $this->nossoNumero;
	}

	public function setNossoNumero($nossoNumero){
		$this->nossoNumero = (int)$nossoNumero;
	}

	public function getCodigoCarteira(){
		return $this->codigoCarteira;
	}

	public function setCodigoCarteira($codigoCarteira){
		$this->codigoCarteira = $codigoCarteira;
	}

	public function getSeuNumero(){
		return $this->seuNumero;
	}

	public function setSeuNumero($seuNumero){
		$this->seuNumero = trim(ltrim($seuNumero, "0"));
	}

	public function getDataVencimentoTitulo($formato = 'Y-m-d'){
		return !is_null($this->dataVencimentoTitulo) ? date($formato, strtotime($this->dataVencimentoTitulo)) : null;
	}

	public function setDataVencimentoTitulo($dataVencimentoTitulo){
		$this->dataVencimentoTitulo = $dataVencimentoTitulo;
	}

	public function getValorNominalTitulo(){
		return $this->valorNominalTitulo;
	}

	public function setValorNominalTitulo($valorNominalTitulo){
		$this->valorNominalTitulo = (float)$valorNominalTitulo;
	}

	public function getNumeroBancoCobradorRecebedor(){
		return $this->numeroBancoCobradorRecebedor;
	}

	public function setNumeroBancoCobradorRecebedor($numeroBancoCobradorRecebedor){
		$this->numeroBancoCobradorRecebedor = $numeroBancoCobradorRecebedor;
	}

	public function getAgenciaCobradoraRecebedora(){
		return $this->agenciaCobradoraRecebedora;
	}

	public function setAgenciaCobradoraRecebedora($agenciaCobradoraRecebedora){
		$this->agenciaCobradoraRecebedora = $agenciaCobradoraRecebedora;
	}

	public function getDigitoAgenciaCobradoraRecebedora(){
		return $this->digitoAgenciaCobradoraRecebedora;
	}

	public function setDigitoAgenciaCobradoraRecebedora($digitoAgenciaCobradoraRecebedora){
		$this->digitoAgenciaCobradoraRecebedora = $digitoAgenciaCobradoraRecebedora;
	}

	public function getIdentificacaoTituloEmpresa(){
		return $this->identificacaoTituloEmpresa;
	}

	public function setIdentificacaoTituloEmpresa($identificacaoTituloEmpresa){
		$this->identificacaoTituloEmpresa = trim(ltrim($identificacaoTituloEmpresa, "0"));
	}

	public function getCodigoMoeda(){
		return $this->codigoMoeda;
	}

	public function setCodigoMoeda($codigoMoeda){
		$this->codigoMoeda = $codigoMoeda;
	}

	public function getTipoInscricaoPagador(){
		return $this->tipoInscricaoPagador;
	}

	public function setTipoInscricaoPagador($tipoInscricaoPagador){
		$this->tipoInscricaoPagador = $tipoInscricaoPagador;
	}

	public function getNumeroInscricaoPagador(){
		return $this->numeroInscricaoPagador;
	}

	public function setNumeroInscricaoPagador($numeroInscricaoPagador){
		$this->numeroInscricaoPagador = $numeroInscricaoPagador;
	}

	public function getNomePagador(){
		return $this->nomePagador;
	}

	public function setNomePagador($nomePagador){
		$this->nomePagador = $nomePagador;
	}

	public function getContaCobranca(){
		return $this->contaCobranca;
	}

	public function setContaCobranca($contaCobranca){
		$this->contaCobranca = $contaCobranca;
	}

	public function getValorTarifaCustas(){
		return $this->valorTarifaCustas;
	}

	public function setValorTarifaCustas($valorTarifaCustas){
		$this->valorTarifaCustas = (float)$valorTarifaCustas;
	}

	public function getIdentificacaoOcorrencia(){
		return $this->identificacaoOcorrencia;
	}

	public function setIdentificacaoOcorrencia($identificacaoOcorrencia){
		if(!is_array($identificacaoOcorrencia)){
			throw new Exception("Identificação da ocorrência no formato inválido.");
		}
		$this->identificacaoOcorrencia = $identificacaoOcorrencia;
	}

	public function getValorJurosMultaEncargos(){
		return $this->valorJurosMultaEncargos;
	}

	public function setValorJurosMultaEncargos($valorJurosMultaEncargos){
		$this->valorJurosMultaEncargos = (float)$valorJurosMultaEncargos;
	}

	public function getValorDescontoConcedido(){
		return $this->valorDescontoConcedido;
	}

	public function setValorDescontoConcedido($valorDescontoConcedido){
		$this->valorDescontoConcedido = (float)$valorDescontoConcedido;
	}

	public function getValorAbatimento(){
		return $this->valorAbatimento;
	}

	public function setValorAbatimento($valorAbatimento){
		$this->valorAbatimento = (float)$valorAbatimento;
	}

	public function getValorIOF(){
		return $this->valorIOF;
	}

	public function setValorIOF($valorIOF){
		$this->valorIOF = (float)$valorIOF;
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

	public function getDataOcorrencia($formato = 'Y-m-d'){
		return !is_null($this->dataOcorrencia) ? date($formato, strtotime($this->dataOcorrencia)) : null;
	}

	public function setDataOcorrencia($dataOcorrencia){
		$this->dataOcorrencia = $dataOcorrencia;
	}

	public function getDataEfetivacaoCredito($formato = 'Y-m-d'){
		return !is_null($this->dataEfetivacaoCredito) ? date($formato, strtotime($this->dataEfetivacaoCredito)) : null;
	}

	public function setDataEfetivacaoCredito($dataEfetivacaoCredito){
		$this->dataEfetivacaoCredito = $dataEfetivacaoCredito;
	}

	public function getCodigoOcorrenciaPagador(){
		return $this->codigoOcorrenciaPagador;
	}

	public function setCodigoOcorrenciaPagador($codigoOcorrenciaPagador){
		$this->codigoOcorrenciaPagador = (int)$codigoOcorrenciaPagador;
	}

	public function getDataOcorrenciaPagador($formato = 'Y-m-d'){
		return !is_null($this->dataOcorrenciaPagador) ? date($formato, strtotime($this->dataOcorrenciaPagador)) : null;
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


}