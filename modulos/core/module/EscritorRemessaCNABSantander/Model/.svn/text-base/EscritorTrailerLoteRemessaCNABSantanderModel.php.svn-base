<?php

namespace module\EscritorRemessaCNABSantander;

use module\EscritorRemessaCNABSantander\EscritorComumCNABSantanderModel;

class EscritorTrailerLoteRemessaCNABSantanderModel extends EscritorComumCNABSantanderModel {

	private $codigoBancoCompensacao;
	private $numeroLoteRemessa;
	private $tipoRegistro;
	private $quantidadeRegistrosLote;

	public function __construct(){

		$this->codigoBancoCompensacao = '33';
		$this->numeroLoteRemessa = '0001';
		$this->tipoRegistro = 5;

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

	public function setQuantidadeRegistrosLote($quantidadeRegistrosLote){
		$this->quantidadeRegistrosLote = $quantidadeRegistrosLote;
	}

	public function getQuantidadeRegistrosLote(){
		return $this->quantidadeRegistrosLote;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
		$linha .= $this->formatNumeric($this->numeroLoteRemessa, 4);
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric("", 9);
		$linha .= $this->formatNumeric($this->quantidadeRegistrosLote, 6);
		$linha .= $this->formatAlphanumeric("", 217);

		return $linha;

	}



}