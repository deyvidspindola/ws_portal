<?php

namespace module\EscritorRemessaCNABSantander;

use module\EscritorRemessaCNABSantander\EscritorComumCNABSantanderModel;

class EscritorTrailerArquivoRemessaCNABSantanderModel extends EscritorComumCNABSantanderModel {

	private $codigoBancoCompensacao;
	private $numeroLoteRemessa;
	private $tipoRegistro;
	private $quantidadeLotesArquivo;
	private $quantidadeRegistrosArquivo;

	public function __construct(){

		$this->codigoBancoCompensacao = '33';
		$this->numeroLoteRemessa = '9999';
		$this->tipoRegistro = 9;
		$this->quantidadeLotesArquivo = 1;

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

	public function setQuantidadeLotesArquivo($quantidadeLotesArquivo){
		$this->quantidadeLotesArquivo = $quantidadeLotesArquivo;
	}

	public function getQuantidadeLotesArquivo(){
		return $this->quantidadeLotesArquivo;
	}

	public function setQuantidadeRegistrosArquivo($quantidadeRegistrosArquivo){
		$this->quantidadeRegistrosArquivo = $quantidadeRegistrosArquivo;
	}

	public function getQuantidadeRegistrosArquivo(){
		return $this->quantidadeRegistrosArquivo;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
		$linha .= $this->formatNumeric($this->numeroLoteRemessa, 4);
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric("", 9);
		$linha .= $this->formatNumeric($this->quantidadeLotesArquivo, 6);
		$linha .= $this->formatNumeric($this->quantidadeRegistrosArquivo, 6);
		$linha .= $this->formatAlphanumeric("", 211);
		
		return $linha;

	}
	
}