<?php

namespace module\EscritorRemessaCNABSantander;

use module\EscritorRemessaCNABSantander\EscritorComumCNABSantanderModel;

class EscritorHeaderLoteRemessaCNABSantanderModel extends EscritorComumCNABSantanderModel {

	const VERSAO_LAYOUT_LOTE = '030';

	private $codigoBancoCompensacao;
	private $numeroLoteRemessa;
	private $tipoRegistro;
	private $tipoOperacao;
	private $tipoServico;
	private $numeroVersaoLayoutLote;
	private $tipoInscricaoEmpresa;
	private $numeroInscricaoEmpresa;
	private $codigoTransmissao;
	private $nomeBeneficiario;
	private $mensagem1;
	private $mensagem2;
	private $numeroRemessa;
	private $dataGravacaoRemessa;

	public function __construct(){

		$this->codigoBancoCompensacao = 33;
		$this->numeroLoteRemessa = '0001';
		$this->tipoRegistro = self::TIPO_REGISTRO_HEADER_LOTE;
		$this->tipoOperacao = self::TIPO_OPERACAO_REMESSA;
		$this->tipoServico = '01';
		$this->numeroVersaoLayoutLote = self::VERSAO_LAYOUT_LOTE;
		$this->tipoInscricaoEmpresa = self::TIPO_INSCRICAO_EMPRESA_CNPJ;

	}

	public function setCodigoBancoCompensacao($codigoBancoCompensacao){
		$this->codigoBancoCompensacao = $codigoBancoCompensacao;
	}

	public function getCodigoBancoCompensacao(){
		return $this->codigoBancoCompensacao;
	}

	public function setNumeroLoteRemessa($numeroLoteRemessa){
		$this->numeroLoteRemessa = $numeroLoteRemessa;
	}

	public function getNumeroLoteRemessa(){
		return $this->numeroLoteRemessa;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoOperacao($tipoOperacao){
		$this->tipoOperacao = $tipoOperacao;
	}

	public function getTipoOperacao(){
		return $this->tipoOperacao;
	}

	public function setTipoServico($tipoServico){
		$this->tipoServico = $tipoServico;
	}

	public function getTipoServico(){
		return $this->tipoServico;
	}

	public function setNumeroVersaoLayoutLote($numeroVersaoLayoutLote){
		$this->numeroVersaoLayoutLote = $numeroVersaoLayoutLote;
	}

	public function getNumeroVersaoLayoutLote(){
		return $this->numeroVersaoLayoutLote;
	}

	public function setTipoInscricaoEmpresa($tipoInscricaoEmpresa){
		$this->tipoInscricaoEmpresa = $tipoInscricaoEmpresa;
	}

	public function getTipoInscricaoEmpresa(){
		return $this->tipoInscricaoEmpresa;
	}

	public function setNumeroInscricaoEmpresa($numeroInscricaoEmpresa){
		$this->numeroInscricaoEmpresa = $numeroInscricaoEmpresa;
	}

	public function getNumeroInscricaoEmpresa(){
		return $this->numeroInscricaoEmpresa;
	}

	public function setCodigoTransmissao($codigoTransmissao){
		$this->codigoTransmissao = $codigoTransmissao;
	}

	public function getCodigoTransmissao(){
		return $this->codigoTransmissao;
	}

	public function setNomeBeneficiario($nomeBeneficiario){
		$this->nomeBeneficiario = $nomeBeneficiario;
	}

	public function getNomeBeneficiario(){
		return $this->nomeBeneficiario;
	}

	public function setMensagem1($mensagem1){
		$this->mensagem1 = $mensagem1;
	}

	public function getMensagem1(){
		return $this->mensagem1;
	}

	public function setMensagem2($mensagem2){
		$this->mensagem2 = $mensagem2;
	}

	public function getMensagem2(){
		return $this->mensagem2;
	}

	public function setNumeroRemessa($numeroRemessa){
		$this->numeroRemessa = $numeroRemessa;
	}

	public function getNumeroRemessa(){
		return $this->numeroRemessa;
	}

	public function setDataGravacaoRemessa($dataGravacaoRemessa){
		$this->dataGravacaoRemessa = $dataGravacaoRemessa;
	}

	public function getDataGravacaoRemessa(){
		return $this->dataGravacaoRemessa;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
		$linha .= $this->formatNumeric($this->numeroLoteRemessa, 4);
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric($this->tipoOperacao, 1);
		$linha .= $this->formatNumeric($this->tipoServico, 2);
		$linha .= $this->formatAlphanumeric("", 2);
		$linha .= $this->formatNumeric($this->numeroVersaoLayoutLote, 3);
		$linha .= $this->formatAlphanumeric("", 1);
		$linha .= $this->formatNumeric($this->tipoInscricaoEmpresa, 1);
		$linha .= $this->formatNumeric($this->numeroInscricaoEmpresa, 15);
		$linha .= $this->formatAlphanumeric("", 20);
		$linha .= $this->formatNumeric($this->codigoTransmissao, 15);
		$linha .= $this->formatAlphanumeric("", 5);
		$linha .= $this->formatAlphanumeric($this->nomeBeneficiario, 30);
		$linha .= $this->formatAlphanumeric($this->mensagem1, 40);
		$linha .= $this->formatAlphanumeric($this->mensagem2, 40);
		$linha .= $this->formatNumeric($this->numeroRemessa, 8);
		$linha .= $this->formatDate($this->dataGravacaoRemessa);
		$linha .= $this->formatAlphanumeric("", 41);

		return $linha;

	}

}