<?php

namespace module\LeitorRetornoRPSBarueri;

class DetalheTipo3ArquivoRPSBarueriModel{

    private $tipoRegistro;
    private $quantidadeServico;
    private $descricaoServico;
    private $codigoServico;
    private $valorUnitarioServico;
    private $aliquotaServico;

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getQuantidadeServico(){
		return $this->quantidadeServico;
	}

	public function setQuantidadeServico($quantidadeServico){
		$this->quantidadeServico = $quantidadeServico;
	}

	public function getDescricaoServico(){
		return $this->descricaoServico;
	}

	public function setDescricaoServico($descricaoServico){
		$this->descricaoServico = $descricaoServico;
	}

	public function getCodigoServico(){
		return $this->codigoServico;
	}

	public function setCodigoServico($codigoServico){
		$this->codigoServico = $codigoServico;
	}

	public function getValorUnitarioServico(){
		return $this->valorUnitarioServico;
	}

	public function setValorUnitarioServico($valorUnitarioServico){
		$this->valorUnitarioServico = $valorUnitarioServico;
	}

	public function getAliquotaServico(){
		return $this->aliquotaServico;
	}

	public function setAliquotaServico($aliquotaServico){
		$this->aliquotaServico = $aliquotaServico;
	}
}

?>