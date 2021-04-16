<?php

namespace module\LeitorRetornoRPSBarueri;

class DetalheTipo2ArquivoRPSBarueriModel{

    private $tipoRegistro;
    private $serieNfe;
    private $numeroNfe;
    private $dataNfe;
    private $horaNfe;
    private $codigoAutenticidade;
    private $serieRPS;
    private $numeroRPS;
    private $tributacao;
    private $ISSRetido;
    private $situacaoNfe;
    private $dataCancelamentoNfe;
    private $numeroGuia;
    private $dataPagamentoGuia;
    private $numeroDocumento;
    private $nomeTomador;
    private $endereco;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $UF;
    private $CEP;
    private $pais;
    private $email;
    private $discriminacaoServico;

    public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getSerieNfe(){
		return $this->serieNfe;
	}

	public function setSerieNfe($serieNfe){
		$this->serieNfe = $serieNfe;
	}

	public function getNumeroNfe(){
		return $this->numeroNfe;
	}

	public function setNumeroNfe($numeroNfe){
		$this->numeroNfe = $numeroNfe;
	}

	public function getDataNfe(){
		return $this->dataNfe;
	}

	public function setDataNfe($dataNfe){
		$this->dataNfe = $dataNfe;
	}

	public function getHoraNfe(){
		return $this->horaNfe;
	}

	public function setHoraNfe($horaNfe){
		$this->horaNfe = $horaNfe;
	}

	public function getCodigoAutenticidade(){
		return $this->codigoAutenticidade;
	}

	public function setCodigoAutenticidade($codigoAutenticidade){
		$this->codigoAutenticidade = $codigoAutenticidade;
	}

	public function getSerieRPS(){
		return $this->serieRPS;
	}

	public function setSerieRPS($serieRPS){
		$this->serieRPS = $serieRPS;
	}

	public function getNumeroRPS(){
		return $this->numeroRPS;
	}

	public function setNumeroRPS($numeroRPS){
		$this->numeroRPS = $numeroRPS;
	}

	public function getTributacao(){
		return $this->tributacao;
	}

	public function setTributacao($tributacao){
		$this->tributacao = $tributacao;
	}

	public function getISSRetido(){
		return $this->ISSRetido;
	}

	public function setISSRetido($ISSRetido){
		$this->ISSRetido = $ISSRetido;
	}

	public function getSituacaoNfe(){
		return $this->situacaoNfe;
	}

	public function setSituacaoNfe($situacaoNfe){
		$this->situacaoNfe = $situacaoNfe;
	}

	public function getDataCancelamentoNfe(){
		return $this->dataCancelamentoNfe;
	}

	public function setDataCancelamentoNfe($dataCancelamentoNfe){
		$this->dataCancelamentoNfe = $dataCancelamentoNfe;
	}

	public function getNumeroGuia(){
		return $this->numeroGuia;
	}

	public function setNumeroGuia($numeroGuia){
		$this->numeroGuia = $numeroGuia;
	}

	public function getDataPagamentoGuia(){
		return $this->dataPagamentoGuia;
	}

	public function setDataPagamentoGuia($dataPagamentoGuia){
		$this->dataPagamentoGuia = $dataPagamentoGuia;
	}

	public function getNumeroDocumento(){
		return $this->numeroDocumento;
	}

	public function setNumeroDocumento($numeroDocumento){
		$this->numeroDocumento = $numeroDocumento;
	}

	public function getNomeTomador(){
		return $this->nomeTomador;
	}

	public function setNomeTomador($nomeTomador){
		$this->nomeTomador = $nomeTomador;
	}

	public function getEndereco(){
		return $this->endereco;
	}

	public function setEndereco($endereco){
		$this->endereco = $endereco;
	}

	public function getNumero(){
		return $this->numero;
	}

	public function setNumero($numero){
		$this->numero = $numero;
	}

	public function getComplemento(){
		return $this->complemento;
	}

	public function setComplemento($complemento){
		$this->complemento = $complemento;
	}

	public function getBairro(){
		return $this->bairro;
	}

	public function setBairro($bairro){
		$this->bairro = $bairro;
	}

	public function getCidade(){
		return $this->cidade;
	}

	public function setCidade($cidade){
		$this->cidade = $cidade;
	}

	public function getUF(){
		return $this->UF;
	}

	public function setUF($UF){
		$this->UF = $UF;
	}

	public function getCEP(){
		return $this->CEP;
	}

	public function setCEP($CEP){
		$this->CEP = $CEP;
	}

	public function getPais(){
		return $this->pais;
	}

	public function setPais($pais){
		$this->pais = $pais;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getDiscriminacaoServico(){
		return $this->discriminacaoServico;
	}

	public function setDiscriminacaoServico($discriminacaoServico){
		$this->discriminacaoServico = $discriminacaoServico;
	}
}

?>