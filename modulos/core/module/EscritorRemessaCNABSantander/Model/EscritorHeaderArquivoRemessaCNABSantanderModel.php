<?php

namespace module\EscritorRemessaCNABSantander;

use module\EscritorRemessaCNABSantander\EscritorComumCNABSantanderModel;

class EscritorHeaderArquivoRemessaCNABSantanderModel extends EscritorComumCNABSantanderModel {

	const CODIGO_REMESSA = 1;
	const VERSAO_LAYOUT = 40;

	private $codigoBancoCompensacao;
	private $loteServico;
	private $tipoRegistro;
	private $tipoInscricaoEmpresa;
	private $numeroInscricaoEmpresa;
	private $codigoTransmissao;
	private $nomeEmpresa;
	private $nomeBanco;
	private $codigoRemessa;
	private $dataGeracaoArquivo;
	private $numeroSequencialArquivo;
	private $numeroVersaoLayoutArquivo;

	public function __construct(){

		$this->codigoBancoCompensacao = 33;
		$this->loteServico = '0000';
		$this->tipoRegistro = self::TIPO_REGISTRO_HEADER_ARQUIVO;
		$this->tipoInscricaoEmpresa = self::TIPO_INSCRICAO_EMPRESA_CNPJ;
		$this->nomeBanco = 'Banco Santander';
		$this->codigoRemessa = self::CODIGO_REMESSA;
		$this->numeroVersaoLayoutArquivo = self::VERSAO_LAYOUT;

	}

	public function setCodigoBancoCompensacao($codigoBancoCompensacao){
		$this->codigoBancoCompensacao = $codigoBancoCompensacao;
	}

	public function getCodigoBancoCompensacao(){
		return $this->codigoBancoCompensacao;
	}

	public function setLoteServico($loteServico){
		$this->loteServico = $loteServico;
	}

	public function getLoteServico(){
		return $this->loteServico;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
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

	public function setNomeEmpresa($nomeEmpresa){
		$this->nomeEmpresa = $nomeEmpresa;
	}

	public function getNomeEmpresa(){
		return $this->nomeEmpresa;
	}

	public function setNomeBanco($nomeBanco){
		$this->nomeBanco = $nomeBanco;
	}

	public function getNomeBanco(){
		return $this->nomeBanco;
	}

	public function setCodigoRemessa($codigoRemessa){
		$this->codigoRemessa = $codigoRemessa;
	}

	public function getCodigoRemessa(){
		return $this->codigoRemessa;
	}

	public function setDataGeracaoArquivo($dataGeracaoArquivo){
		$this->dataGeracaoArquivo = $dataGeracaoArquivo;
	}

	public function getDataGeracaoArquivo(){
		return $this->dataGeracaoArquivo;
	}

	public function setNumeroSequencialArquivo($numeroSequencialArquivo){
		$this->numeroSequencialArquivo = $numeroSequencialArquivo;
	}

	public function getNumeroSequencialArquivo(){
		return $this->numeroSequencialArquivo;
	}

	public function setNumeroVersaoLayoutArquivo($numeroVersaoLayoutArquivo){
		$this->numeroVersaoLayoutArquivo = $numeroVersaoLayoutArquivo;
	}

	public function getNumeroVersaoLayoutArquivo(){
		return $this->numeroVersaoLayoutArquivo;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
		$linha .= $this->formatNumeric($this->loteServico, 4);
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric("", 8);
		$linha .= $this->formatNumeric($this->tipoInscricaoEmpresa, 1);
		$linha .= $this->formatNumeric($this->numeroInscricaoEmpresa, 15);
		$linha .= $this->formatNumeric($this->codigoTransmissao, 15);
		$linha .= $this->formatAlphanumeric("", 25);
		$linha .= $this->formatAlphanumeric($this->nomeEmpresa, 30);
		$linha .= $this->formatAlphanumeric($this->nomeBanco, 30);
		$linha .= $this->formatAlphanumeric("", 10);
		$linha .= $this->formatNumeric($this->codigoRemessa, 1);
		$linha .= $this->formatDate($this->dataGeracaoArquivo);
		$linha .= $this->formatAlphanumeric("", 6);
		$linha .= $this->formatNumeric($this->numeroSequencialArquivo, 6);
		$linha .= $this->formatNumeric($this->numeroVersaoLayoutArquivo, 3);
		$linha .= $this->formatAlphanumeric("", 74);

		return $linha;

	}


}