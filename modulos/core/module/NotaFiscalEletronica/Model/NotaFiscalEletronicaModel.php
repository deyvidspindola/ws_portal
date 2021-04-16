<?php
namespace module\NotaFiscalEletronica;

class NotaFiscalEletronicaModel
{
	private $notaFiscalId;
	private $codigoEmpresa;
	private $numero;
	private $dataTransmissaoRps;
	private $dataRetornoRps;
	private $mensagemRetornoRps;
	private $numeroLoteEnvio;
	private $usuarioEnvioRps;
	private $dataEmissaoPrefeitura;
	private $dataCompetenciaPrefeitura;
	private $codigoVerificador;
	private $nomeArquivoEnvioRps;
	private $nomeArquivoRetornoRps;
	private $dataEnvioCancelamento;
	private $dataRetornoCancelamento;
	private $mensagemRetornoCancelamento;
	private $usuarioEnvioCancelamento;
	private $codigoRetornoRps;
	private $codigoRetornoCancelamento;
	private $codigoErroRetornoRps;
	private $codigoErroRetornoCancelamento;
	private $notaManual;
	private $dataGeracaoManual;
	private $usuarioGeracaoManual;

	public function __construct($id = null)
	{
		$this->dao = new NotaFiscalEletronicaDAO();
	}

	public function setNotaFiscalId($notaFiscalId)
	{
		$this->notaFiscalId = $notaFiscalId;
	}

	public function getNotaFiscalId()
	{
		return $this->notaFiscalId;
	}

	public function setCodigoEmpresa($codigoEmpresa)
	{
		$this->codigoEmpresa = $codigoEmpresa;
	}

	public function getCodigoEmpresa()
	{
		return $this->codigoEmpresa;
	}

	public function setNumero($numero)
	{
		$this->numero = $numero;
	}

	public function getNumero()
	{
		return $this->numero;
	}

	public function setDataTransmissaoRps($dataTransmissaoRps)
	{
		$this->dataTransmissaoRps = $dataTransmissaoRps;
	}

	public function getDataTransmissaoRps()
	{
		return $this->dataTransmissaoRps;
	}

	public function setDataRetornoRps($dataRetornoRps)
	{
		$this->dataRetornoRps = $dataRetornoRps;
	}

	public function getDataRetornoRps()
	{
		return $this->dataRetornoRps;
	}

	public function setMensagemRetornoRps($mensagemRetornoRps)
	{
		$this->mensagemRetornoRps = $mensagemRetornoRps;
	}

	public function getMensagemRetornoRps()
	{
		return $this->mensagemRetornoRps;
	}

	public function setNumeroLoteEnvio($numeroLoteEnvio)
	{
		$this->numeroLoteEnvio = $numeroLoteEnvio;
	}

	public function getNumeroLoteEnvio()
	{
		return $this->numeroLoteEnvio;
	}

	public function setUsuarioEnvioRps($usuarioEnvioRps)
	{
		$this->usuarioEnvioRps = $usuarioEnvioRps;
	}

	public function getUsuarioEnvioRps()
	{
		return $this->usuarioEnvioRps;
	}

	public function setDataEmissaoPrefeitura($dataEmissaoPrefeitura)
	{
		$this->dataEmissaoPrefeitura = $dataEmissaoPrefeitura;
	}

	public function getDataEmissaoPrefeitura()
	{
		return $this->dataEmissaoPrefeitura;
	}

	public function setDataCompetenciaPrefeitura($dataCompetenciaPrefeitura)
	{
		$this->dataCompetenciaPrefeitura = $dataCompetenciaPrefeitura;
	}

	public function getDataCompetenciaPrefeitura()
	{
		return $this->dataCompetenciaPrefeitura;
	}

	public function setCodigoVerificador($codigoVerificador)
	{
		$this->codigoVerificador = $codigoVerificador;
	}

	public function getCodigoVerificador()
	{
		return $this->codigoVerificador;
	}

	public function setNomeArquivoEnvioRps($nomeArquivoEnvioRps)
	{
		$this->nomeArquivoEnvioRps = $nomeArquivoEnvioRps;
	}

	public function getNomeArquivoEnvioRps()
	{
		return $this->nomeArquivoEnvioRps;
	}

	public function setNomeArquivoRetornoRps($nomeArquivoRetornoRps)
	{
		$this->nomeArquivoRetornoRps = $nomeArquivoRetornoRps;
	}

	public function getNomeArquivoRetornoRps()
	{
		return $this->nomeArquivoRetornoRps;
	}

	public function setDataEnvioCancelamento($dataEnvioCancelamento)
	{
		$this->dataEnvioCancelamento = $dataEnvioCancelamento;
	}

	public function getDataEnvioCancelamento()
	{
		return $this->dataEnvioCancelamento;
	}

	public function setDataRetornoCancelamento($dataRetornoCancelamento)
	{
		$this->dataRetornoCancelamento = $dataRetornoCancelamento;
	}

	public function getDataRetornoCancelamento()
	{
		return $this->dataRetornoCancelamento;
	}

	public function setMensagemRetornoCancelamento($mensagemRetornoCancelamento)
	{
		$this->mensagemRetornoCancelamento = $mensagemRetornoCancelamento;
	}

	public function getMensagemRetornoCancelamento()
	{
		return $this->mensagemRetornoCancelamento;
	}

	public function setUsuarioEnvioCancelamento($usuarioEnvioCancelamento)
	{
		$this->usuarioEnvioCancelamento = $usuarioEnvioCancelamento;
	}

	public function getUsuarioEnvioCancelamento()
	{
		return $this->usuarioEnvioCancelamento;
	}

	public function setCodigoRetornoRps($codigoRetornoRps)
	{
		$this->codigoRetornoRps = $codigoRetornoRps;
	}

	public function getCodigoRetornoRps()
	{
		return $this->codigoRetornoRps;
	}

	public function setCodigoRetornoCancelamento($codigoRetornoCancelamento)
	{
		$this->codigoRetornoCancelamento = $codigoRetornoCancelamento;
	}

	public function getCodigoRetornoCancelamento()
	{
		return $this->codigoRetornoCancelamento;
	}

	public function setCodigoErroRetornoRps($codigoErroRetornoRps)
	{
		$this->codigoErroRetornoRps = $codigoErroRetornoRps;
	}

	public function getCodigoErroRetornoRps()
	{
		return $this->codigoErroRetornoRps;
	}

	public function setCodigoErroRetornoCancelamento($codigoErroRetornoCancelamento)
	{
		$this->codigoErroRetornoCancelamento = $codigoErroRetornoCancelamento;
	}

	public function getCodigoErroRetornoCancelamento()
	{
		return $this->codigoErroRetornoCancelamento;
	}

	public function setNotaManual($notaManual)
	{
		$this->notaManual = $notaManual;
	}

	public function getNotaManual()
	{
		return $this->notaManual;
	}

	public function setDataGeracaoManual($dataGeracaoManual)
	{
		$this->dataGeracaoManual = $dataGeracaoManual;
	}

	public function getDataGeracaoManual()
	{
		return $this->dataGeracaoManual;
	}

	public function setUsuarioGeracaoManual($usuarioGeracaoManual)
	{
		$this->usuarioGeracaoManual = $usuarioGeracaoManual;
	}

	public function getUsuarioGeracaoManual()
	{
		return $this->usuarioGeracaoManual;
	}

}