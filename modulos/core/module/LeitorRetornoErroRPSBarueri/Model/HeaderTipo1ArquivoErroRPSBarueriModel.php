<?php

namespace module\LeitorRetornoErroRPSBarueri;

class HeaderTipo1ArquivoErroRPSBarueriModel{

    private $tipoRegistro;
    private $inscricaoContribuinte;
    private $versaoLayOut;
	private $identificacaoRemessaContribuinte;
	private $codigoErro;

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

	public function getCodigoErro(){
		return $this->codigoErro;
	}

	public function setCodigoErro($codigoErro){
		$this->codigoErro = $codigoErro;
	}

}


?>