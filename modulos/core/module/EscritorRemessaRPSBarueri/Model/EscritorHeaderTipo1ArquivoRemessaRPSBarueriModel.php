<?php

namespace module\EscritorRemessaRPSBarueri;

use module\EscritorRemessaRPSBarueri\EscritorComumRPSBarueriModel;

class EscritorHeaderTipo1ArquivoRemessaRPSBarueriModel extends EscritorComumRPSBarueriModel{

	private $tipoRegistro;
    private $inscricaoContribuinte;
    private $versaoLayOut;
	private $identificacaoRemessaContribuinte;

	public function __construct(){

		$this->tipoRegistro = 1;
		$this->versaoLayOut = 'PMB002';

	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getInscricaoContribuinte(){
		return $this->inscricaoContribuinte;
	}

	public function setInscricaoContribuinte($inscricaoContribuinte){
		$this->inscricaoContribuinte = $inscricaoContribuinte;
	}

	public function getVersaoLayOut(){
		return $this->versaoLayOut;
	}

	public function setVersaoLayOut($versaoLayOut){
		$this->versaoLayOut = $versaoLayOut;
	}

	public function getIdentificacaoRemessaContribuinte(){
		return $this->identificacaoRemessaContribuinte;
	}

	public function setIdentificacaoRemessaContribuinte($identificacaoRemessaContribuinte){
		$this->identificacaoRemessaContribuinte = $identificacaoRemessaContribuinte;
	}

	public function getRegistro(){
		$linha = '';
		
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric($this->inscricaoContribuinte, 7);
		$linha .= $this->formatAlphanumeric($this->versaoLayOut, 6);
		$linha .= $this->formatNumeric($this->identificacaoRemessaContribuinte, 11);

		return $linha;
	}

}

?>