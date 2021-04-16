<?php

namespace module\EscritorRemessaRPSBarueri;

use module\EscritorRemessaRPSBarueri\EscritorComumRPSBarueriModel;

class EscritorTrailerTipo9ArquivoRemessaRPSBarueriModel extends EscritorComumRPSBarueriModel{

	private $tipoRegistro;
    private $numeroTotalLinhasDoArquivo;
    private $valorTotalServicosContidosNoArquivo;
	private $valorTotalRetencoesEOutrosValores;
	
	public function __construct(){

		$this->tipoRegistro = 9;

	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getNumeroTotalLinhasDoArquivo(){
		return $this->numeroTotalLinhasDoArquivo;
	}

	public function setNumeroTotalLinhasDoArquivo($numeroTotalLinhasDoArquivo){
		$this->numeroTotalLinhasDoArquivo = $numeroTotalLinhasDoArquivo;
	}

	public function getValorTotalServicosContidosNoArquivo(){
		return $this->valorTotalServicosContidosNoArquivo;
	}

	public function setValorTotalServicosContidosNoArquivo($valorTotalServicosContidosNoArquivo){
		$this->valorTotalServicosContidosNoArquivo = (float)$valorTotalServicosContidosNoArquivo;
	}

	public function getValorTotalRetencoesEOutrosValores(){
		return $this->valorTotalRetencoesEOutrosValores;
	}

	public function setValorTotalRetencoesEOutrosValores($valorTotalRetencoesEOutrosValores){
		$this->valorTotalRetencoesEOutrosValores = (float)$valorTotalRetencoesEOutrosValores;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatNumeric($this->numeroTotalLinhasDoArquivo, 7);
        $linha .= $this->formatNumeric($this->valorTotalServicosContidosNoArquivo, 15, 2);
		$linha .= $this->formatNumeric($this->valorTotalRetencoesEOutrosValores, 15, 2);

		return $linha;

	}
}

?>