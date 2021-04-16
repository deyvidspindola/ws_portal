<?php

namespace module\LeitorRetornoErroRPSBarueri;

class DetalheTipo3ArquivoErroRPSBarueriModel{
    private $tipoRegistro;
    private $codigoDeOutrosValores; 
	private $valor;
	private $codigoErro;

    public function __construct(){

		$this->tipoRegistro = 3;

	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
    }
    
	public function getCodigoDeOutrosValores(){
		return $this->codigoDeOutrosValores;
	}

	public function setCodigoDeOutrosValores($codigoDeOutrosValores){
		$this->codigoDeOutrosValores = $codigoDeOutrosValores;
	}

	public function getValor(){
		return $this->valor;
	}

	public function setValor($valor){
		$this->valor = $valor;
	}
	
	public function getCodigoErro(){
		return $this->codigoErro;
	}

	public function setCodigoErro($codigoErro){
		$this->codigoErro = $codigoErro;
	}
}
    
?>