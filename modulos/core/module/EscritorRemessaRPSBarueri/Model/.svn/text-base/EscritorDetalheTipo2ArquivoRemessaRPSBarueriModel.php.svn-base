<?php

namespace module\EscritorRemessaRPSBarueri;

use module\EscritorRemessaRPSBarueri\EscritorComumRPSBarueriModel;

class EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel extends EscritorComumRPSBarueriModel{

	const SITUACAO_ENVIADO = "E";
	const SITUACAO_CANCELADO = "C";

	const CODIGO_MOTIVO_CANCELAMENTO_SERVICO = "01";
	const CODIGO_MOTIVO_CANCELAMENTO_DADOS_INCORRETOS = "02";
	const CODIGO_MOTIVO_CANCELAMENTO_SUBSTITUICAO = "03";

	const LOCAL_PRESTACAO_SERVICO_NO_MUNICIPIO = "1";
	const LOCAL_PRESTACAO_SERVICO_FORA_MUNICIPIO = "2";

	const LOCAL_PRESTACAO_SERVICO_VIAS_PUBLICAS = "1";
	const LOCAL_PRESTACAO_SERVICO_VIAS_NAO_PUBLICAS = "2";

	const TIPO_ESTRANGEIRO = 1;
	const TIPO_BRASILEIRO = 2;

	const SERVICO_EXPORTADO = 1;
	const SERVICO_NAO_EXPORTADO = 2;

	const TIPO_CPF = 1;
	const TIPO_CNPJ = 2;

	private $tipoRegistro;
	private $tipoRPS;
    private $serieRPS;
    private $serieNfe;
    private $numeroRPS;
    private $dataRPS;
    private $horaRPS;
    private $situacaoRPS;
    private $codigoMotivoCancelamento;
	private $numeroNfeCanceladaOuSubstituida;
	private $serieNfeCanceladaOuSubstituida;
    private $dataEmissaoNfeASerCanceladaOuSubstituida;
    private $descricaoCancelamento;
    private $codigoServicoPrestado;
    private $localPrestacaoServico;
    private $servicoPrestadoViasPublicas;
    private $enderecoServicoPrestado;
    private $numeroServicoPrestado;
    private $complementoServicoPrestado;
    private $bairroServicoPrestado;
    private $cidadeServicoPrestado;
	private $UFServicoPrestado;
	private $CEPServicoPrestado;
    private $quantidadeServico;
    private $valorServico;
    private $reservado;
	private $valorTotalRetencoes;
	private $tipoTomador;
    private $paisNacionalidade;
    private $servicoPrestadoExportacao;
    private $tipoDocumento;
    private $numeroDocumento;
    private $nomeTomador;
    private $endereco;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $UF;
    private $CEP;
    private $email;
    private $fatura;
    private $valorFatura;
    private $formaPagamento;
	private $discriminacaoServico;

	public function __construct(){

		$this->tipoRegistro = 2;
		$this->tipoRPS = 'RPS';

	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getTipoRPS(){
		return $this->tipoRPS;
	}

	public function setTipoRPS($tipoRPS){
		$this->tipoRPS = $tipoRPS;
	}

	public function getSerieRPS(){
		return $this->serieRPS;
	}

	public function setSerieRPS($serieRPS){
		$this->serieRPS = $serieRPS;
	}

	public function getSerieNfe(){
		return $this->serieNfe;
	}

	public function setSerieNfe($serieNfe){
		$this->serieNfe = $serieNfe;
	}

	public function getNumeroRPS(){
		return $this->numeroRPS;
	}

	public function setNumeroRPS($numeroRPS){
		$this->numeroRPS = $numeroRPS;
	}

	public function getDataRPS(){
		return $this->dataRPS;
	}

	public function setDataRPS($dataRPS){
		$this->dataRPS = $dataRPS;
	}

	public function getHoraRPS(){
		return $this->horaRPS;
	}

	public function setHoraRPS($horaRPS){
		$this->horaRPS = $horaRPS;
	}

	public function getSituacaoRPS(){
		return $this->situacaoRPS;
	}

	public function setSituacaoRPS($situacaoRPS){
		$this->situacaoRPS = $situacaoRPS;
	}

	public function getCodigoMotivoCancelamento(){
		return $this->codigoMotivoCancelamento;
	}

	public function setCodigoMotivoCancelamento($codigoMotivoCancelamento){
		$this->codigoMotivoCancelamento = $codigoMotivoCancelamento;
	}

	public function getNumeroNfeCanceladaOuSubstituida(){
		return $this->numeroNfeCanceladaOuSubstituida;
	}

	public function setNumeroNfeCanceladaOuSubstituida($numeroNfeCanceladaOuSubstituida){
		$this->numeroNfeCanceladaOuSubstituida = $numeroNfeCanceladaOuSubstituida;
	}

	public function getSerieNfeCanceladaOuSubstituida(){
		return $this->serieNfeCanceladaOuSubstituida;
	}

	public function setSerieNfeCanceladaOuSubstituida($serieNfeCanceladaOuSubstituida){
		$this->serieNfeCanceladaOuSubstituida = $serieNfeCanceladaOuSubstituida;
	}

	public function getDataEmissaoNfeASerCanceladaOuSubstituida(){
		return $this->dataEmissaoNfeASerCanceladaOuSubstituida;
	}

	public function setDataEmissaoNfeASerCanceladaOuSubstituida($dataEmissaoNfeASerCanceladaOuSubstituida){
		$this->dataEmissaoNfeASerCanceladaOuSubstituida = $dataEmissaoNfeASerCanceladaOuSubstituida;
	}

	public function getDescricaoCancelamento(){
		return $this->descricaoCancelamento;
	}

	public function setDescricaoCancelamento($descricaoCancelamento){
		$this->descricaoCancelamento = $descricaoCancelamento;
	}

	public function getCodigoServicoPrestado(){
		return $this->codigoServicoPrestado;
	}

	public function setCodigoServicoPrestado($codigoServicoPrestado){
		$this->codigoServicoPrestado = $codigoServicoPrestado;
	}

	public function getLocalPrestacaoServico(){
		return $this->localPrestacaoServico;
	}

	public function setLocalPrestacaoServico($localPrestacaoServico){
		$this->localPrestacaoServico = $localPrestacaoServico;
	}

	public function getServicoPrestadoViasPublicas(){
		return $this->servicoPrestadoViasPublicas;
	}

	public function setServicoPrestadoViasPublicas($servicoPrestadoViasPublicas){
		$this->servicoPrestadoViasPublicas = $servicoPrestadoViasPublicas;
	}

	public function getEnderecoServicoPrestado(){
		return $this->enderecoServicoPrestado;
	}

	public function setEnderecoServicoPrestado($enderecoServicoPrestado){
		$this->enderecoServicoPrestado = $enderecoServicoPrestado;
	}

	public function getNumeroServicoPrestado(){
		return $this->numeroServicoPrestado;
	}

	public function setNumeroServicoPrestado($numeroServicoPrestado){
		$this->numeroServicoPrestado = $numeroServicoPrestado;
	}

	public function getComplementoServicoPrestado(){
		return $this->complementoServicoPrestado;
	}

	public function setComplementoServicoPrestado($complementoServicoPrestado){
		$this->complementoServicoPrestado = $complementoServicoPrestado;
	}

	public function getBairroServicoPrestado(){
		return $this->bairroServicoPrestado;
	}

	public function setBairroServicoPrestado($bairroServicoPrestado){
		$this->bairroServicoPrestado = $bairroServicoPrestado;
	}

	public function getCidadeServicoPrestado(){
		return $this->cidadeServicoPrestado;
	}

	public function setCidadeServicoPrestado($cidadeServicoPrestado){
		$this->cidadeServicoPrestado = $cidadeServicoPrestado;
	}

	public function getUFServicoPrestado(){
		return $this->UFServicoPrestado;
	}

	public function setUFServicoPrestado($UFServicoPrestado){
		$this->UFServicoPrestado = $UFServicoPrestado;
	}

	public function getCEPServicoPrestado(){
		return $this->CEPServicoPrestado;
	}

	public function setCEPServicoPrestado($CEPServicoPrestado){
		$this->CEPServicoPrestado = $CEPServicoPrestado;
	}

	public function getQuantidadeServico(){
		return $this->quantidadeServico;
	}

	public function setQuantidadeServico($quantidadeServico){
		$this->quantidadeServico = $quantidadeServico;
	}

	public function getValorServico(){
		return $this->valorServico;
	}

	public function setValorServico($valorServico){
		$this->valorServico = (float)$valorServico;
	}

	public function getReservado(){
		return $this->reservado;
	}

	public function setReservado($reservado){
		$this->reservado = $reservado;
	}

	public function getTipoTomador(){
		return $this->tipoTomador;
	}

	public function setTipoTomador($tipoTomador){
		$this->tipoTomador = $tipoTomador;
	}

	public function getValorTotalRetencoes(){
		return $this->valorTotalRetencoes;
	}

	public function setValorTotalRetencoes($valorTotalRetencoes){
		$this->valorTotalRetencoes = (float)$valorTotalRetencoes;
	}


	public function getPaisNacionalidade(){
		return $this->paisNacionalidade;
	}

	public function setPaisNacionalidade($paisNacionalidade){
		$this->paisNacionalidade = $paisNacionalidade;
	}

	public function getServicoPrestadoExportacao(){
		return $this->servicoPrestadoExportacao;
	}

	public function setServicoPrestadoExportacao($servicoPrestadoExportacao){
		$this->servicoPrestadoExportacao = $servicoPrestadoExportacao;
	}

	public function getTipoDocumento(){
		return $this->tipoDocumento;
	}

	public function setTipoDocumento($tipoDocumento){
		$this->tipoDocumento = $tipoDocumento;
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
		$this->nomeTomador = trim($nomeTomador);
	}

	public function getEndereco(){
		return $this->endereco;
	}

	public function setEndereco($endereco){
		$this->endereco = trim($endereco);
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
		$this->complemento = trim($complemento);
	}

	public function getBairro(){
		return $this->bairro;
	}

	public function setBairro($bairro){
		$this->bairro = trim($bairro);
	}

	public function getCidade(){
		return $this->cidade;
	}

	public function setCidade($cidade){
		$this->cidade = trim($cidade);
	}

	public function getUF(){
		return $this->UF;
	}

	public function setUF($UF){
		$this->UF = trim($UF);
	}

	public function getCEP(){
		return $this->CEP;
	}

	public function setCEP($CEP){
		$this->CEP = $CEP;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getFatura(){
		return $this->fatura;
	}

	public function setFatura($fatura){
		$this->fatura = $fatura;
	}

	public function getValorFatura(){
		return $this->valorFatura;
	}

	public function setValorFatura($valorFatura){
		$this->valorFatura = (float)$valorFatura;
	}

	public function getFormaPagamento(){
		return $this->formaPagamento;
	}

	public function setFormaPagamento($formaPagamento){
		$this->formaPagamento = $formaPagamento;
	}

	public function getDiscriminacaoServico(){
		return $this->discriminacaoServico;
	}

	public function setDiscriminacaoServico($discriminacaoServico){
		$this->discriminacaoServico = $discriminacaoServico;
	}

	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric($this->tipoRPS, 5);
		$linha .= $this->formatAlphanumeric($this->serieRPS, 4);
		$linha .= $this->formatAlphanumeric($this->serieNfe, 5);
		$linha .= $this->formatNumeric($this->numeroRPS, 10);
		$linha .= $this->formatDate($this->dataRPS);
		$linha .= $this->formatTime($this->horaRPS, 6);
		$linha .= $this->formatAlphanumeric($this->situacaoRPS, 1);
		$linha .= $this->formatAlphanumeric($this->codigoMotivoCancelamento, 2);
		$linha .= $this->formatNumeric($this->numeroNfeCanceladaOuSubstituida, 7, null, false);
		$linha .= $this->formatAlphanumeric($this->serieNfeCanceladaOuSubstituida, 5);
		$linha .= $this->formatDate($this->dataEmissaoNfeASerCanceladaOuSubstituida);
		$linha .= $this->formatAlphanumeric($this->descricaoCancelamento, 180);
		$linha .= $this->formatNumeric($this->codigoServicoPrestado, 9);
		$linha .= $this->formatAlphanumeric($this->localPrestacaoServico, 1);
		$linha .= $this->formatAlphanumeric($this->servicoPrestadoViasPublicas, 1);
		$linha .= $this->formatAlphanumeric($this->enderecoServicoPrestado, 75);
		$linha .= $this->formatAlphanumeric($this->numeroServicoPrestado, 9);
		$linha .= $this->formatAlphanumeric($this->complementoServicoPrestado, 30);
		$linha .= $this->formatAlphanumeric($this->bairroServicoPrestado, 40);
		$linha .= $this->formatAlphanumeric($this->cidadeServicoPrestado, 40);
		$linha .= $this->formatAlphanumeric($this->UFServicoPrestado, 2);
		$linha .= $this->formatAlphanumeric($this->CEPServicoPrestado, 8);
		$linha .= $this->formatNumeric($this->quantidadeServico, 6);
		$linha .= $this->formatNumeric($this->valorServico, 15, 2);
		$linha .= $this->formatAlphanumeric($this->reservado, 5);
		$linha .= $this->formatNumeric($this->valorTotalRetencoes, 15, 2);
		$linha .= $this->formatNumeric($this->tipoTomador, 1);
		$linha .= $this->formatNumeric($this->paisNacionalidade, 3, null, false);
		$linha .= $this->formatNumeric($this->servicoPrestadoExportacao, 1, null, false);
		$linha .= $this->formatNumeric($this->tipoDocumento, 1);
		$linha .= $this->formatNumeric($this->numeroDocumento, 14);
		$linha .= $this->formatAlphanumeric($this->nomeTomador, 60);
		$linha .= $this->formatAlphanumeric($this->endereco, 75);
		$linha .= $this->formatAlphanumeric($this->numero, 9);
		$linha .= $this->formatAlphanumeric($this->complemento, 30);
		$linha .= $this->formatAlphanumeric($this->bairro, 40);
		$linha .= $this->formatAlphanumeric($this->cidade, 40);
		$linha .= $this->formatAlphanumeric($this->UF, 2);
		$linha .= $this->formatAlphanumeric($this->CEP, 8);
		$linha .= $this->formatString($this->email, 152);
		$linha .= $this->formatNumeric($this->fatura, 6, null, false);
		$linha .= $this->formatNumeric($this->valorFatura, 15, 2, null, false);
		$linha .= $this->formatAlphanumeric($this->formaPagamento, 15);
		$linha .= $this->formatString($this->discriminacaoServico, 1000);

		return $linha;
	}

}