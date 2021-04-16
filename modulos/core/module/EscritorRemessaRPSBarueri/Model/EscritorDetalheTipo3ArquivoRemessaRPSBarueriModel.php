<?php

namespace module\EscritorRemessaRPSBarueri;

use module\EscritorRemessaRPSBarueri\EscritorComumRPSBarueriModel;

class EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel extends EscritorComumRPSBarueriModel{

    const CODIGO_IRRF = "01";
    const CODIGO_PIS_PASEP = "02";
    const CODIGO_COFINS = "03";
    const CODIGO_CSLL = "04";
    const CODIGO_VALOR_NAO_INCLUSO = "VN";

    private $tipoRegistro;
    private $codigoDeOutrosValores; 
    private $valor;

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
    
    public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
        $linha .= $this->formatAlphanumeric($this->codigoDeOutrosValores, 2);
        $linha .= $this->formatNumeric($this->valor, 15, 2);

		return $linha;

	}

}