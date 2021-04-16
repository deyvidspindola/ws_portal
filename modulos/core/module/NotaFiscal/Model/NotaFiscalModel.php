<?php
namespace module\NotaFiscal;

class NotaFiscalModel
{
	const INDICADOR_FRETE_SEM_FRETE = 0;
	const INDICADOR_FRETE_DESTINATARIO = 1;
	const INDICADOR_FRETE_EMITENTE = 2;

	private $id;
	private $dataInclusao;
	private $dataNota;
	private $dataEmissao;
	private $natureza;
	private $transporte;
	private $clienteId;
	private $numero;
	private $serie;
	private $valorTotal;
	private $valorDesconto;
	private $dataReferencia;
	private $dataVencimento;
	private $dataFaturamento;
	private $observacao;
	private $dataCancelamento;
	private $observacaoCancelamento;
	private $usuarioId;
	private $clienteIdFatura;
	private $notaAnterior;
	private $valorPisCofins;
	private $valorImpostoRenda;
	private $empenho;
	private $exSeguradora;
	private $valorIss;
	private $volume;
	private $peso;
	private $fretePor;
	private $dataArquivo;
	private $tipo;
	private $remessaFiscal;
	private $remessaFiscalRetorno;
	private $remessaNfe;
	private $dataEnvioGrafica;
	private $valorPis;
	private $valorCofins;
	private $valorCsll;
	private $numeroPedido;
	private $dataEnvioFox;
	private $dataRecebimentoFox;
	private $indicadorFrete;
	private $transportadoraId;
	private $pesoLiquido;
	private $valorFrete;
	private $valorSeguro;
	private $valorDespesas;
	private $valorMercadorias;
	private $valorBasePis;
	private $valorBaseCofins;
	private $valorBaseCsll;
	private $valorIssRet;
	private $valorImposto;
	private $aliquotaImposto;
	private $codigoServico;
	private $monitoramentoDiferido;
	private $informacoesComplementares;

	public function __construct($id = null)
	{
		$this->dao = new NotaFiscalDAO();
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setDataInclusao($dataInclusao)
	{
		$this->dataInclusao = $dataInclusao;
	}

	public function getDataInclusao()
	{
		return $this->dataInclusao;
	}

	public function setDataNota($dataNota)
	{
		$this->dataNota = $dataNota;
	}

	public function getDataNota()
	{
		return $this->dataNota;
	}

	public function setDataEmissao($dataEmissao)
	{
		$this->dataEmissao = $dataEmissao;
	}

	public function getDataEmissao()
	{
		return $this->dataEmissao;
	}

	public function setNatureza($natureza)
	{
		$this->natureza = $natureza;
	}

	public function getNatureza()
	{
		return $this->natureza;
	}

	public function setTransporte($transporte)
	{
		$this->transporte = $transporte;
	}

	public function getTransporte()
	{
		return $this->transporte;
	}

	public function setClienteId($clienteId)
	{
		$this->clienteId = $clienteId;
	}

	public function getClienteId()
	{
		return $this->clienteId;
	}

	public function setNumero($numero)
	{
		$this->numero = $numero;
	}

	public function getNumero()
	{
		return $this->numero;
	}

	public function setSerie($serie)
	{
		$this->serie = $serie;
	}

	public function getSerie()
	{
		return $this->serie;
	}

	public function setValorTotal($valorTotal)
	{
		$this->valorTotal = $valorTotal;
	}

	public function getValorTotal()
	{
		return $this->valorTotal;
	}

	public function setValorDesconto($valorDesconto)
	{
		$this->valorDesconto = $valorDesconto;
	}

	public function getValorDesconto()
	{
		return $this->valorDesconto;
	}

	public function setDataReferencia($dataReferencia)
	{
		$this->dataReferencia = $dataReferencia;
	}

	public function getDataReferencia()
	{
		return $this->dataReferencia;
	}

	public function setDataVencimento($dataVencimento)
	{
		$this->dataVencimento = $dataVencimento;
	}

	public function getDataVencimento()
	{
		return $this->dataVencimento;
	}

	public function setDataFaturamento($dataFaturamento)
	{
		$this->dataFaturamento = $dataFaturamento;
	}

	public function getDataFaturamento()
	{
		return $this->dataFaturamento;
	}

	public function setObservacao($observacao)
	{
		$this->observacao = $observacao;
	}

	public function getObservacao()
	{
		return $this->observacao;
	}

	public function setDataCancelamento($dataCancelamento)
	{
		$this->dataCancelamento = $dataCancelamento;
	}

	public function getDataCancelamento()
	{
		return $this->dataCancelamento;
	}

	public function setObservacaoCancelamento($observacaoCancelamento)
	{
		$this->observacaoCancelamento = $observacaoCancelamento;
	}

	public function getObservacaoCancelamento()
	{
		return $this->observacaoCancelamento;
	}

	public function setUsuarioId($usuarioId)
	{
		$this->usuarioId = $usuarioId;
	}

	public function getUsuarioId()
	{
		return $this->usuarioId;
	}

	public function setClienteIdFatura($clienteIdFatura)
	{
		$this->clienteIdFatura = $clienteIdFatura;
	}

	public function getClienteIdFatura()
	{
		return $this->clienteIdFatura;
	}

	public function setNotaAnterior($notaAnterior)
	{
		$this->notaAnterior = $notaAnterior;
	}

	public function getNotaAnterior()
	{
		return $this->notaAnterior;
	}

	public function setValorPisCofins($valorPisCofins)
	{
		$this->valorPisCofins = $valorPisCofins;
	}

	public function getValorPisCofins()
	{
		return $this->valorPisCofins;
	}

	public function setValorImpostoRenda($valorImpostoRenda)
	{
		$this->valorImpostoRenda = $valorImpostoRenda;
	}

	public function getValorImpostoRenda()
	{
		return $this->valorImpostoRenda;
	}

	public function setEmpenho($empenho)
	{
		$this->empenho = $empenho;
	}

	public function getEmpenho()
	{
		return $this->empenho;
	}

	public function setExSeguradora($exSeguradora)
	{
		$this->exSeguradora = $exSeguradora;
	}

	public function getExSeguradora()
	{
		return $this->exSeguradora;
	}

	public function setValorIss($valorIss)
	{
		$this->valorIss = $valorIss;
	}

	public function getValorIss()
	{
		return $this->valorIss;
	}

	public function setVolume($volume)
	{
		$this->volume = $volume;
	}

	public function getVolume()
	{
		return $this->volume;
	}

	public function setPeso($peso)
	{
		$this->peso = $peso;
	}

	public function getPeso()
	{
		return $this->peso;
	}

	public function setFretePor($fretePor)
	{
		$this->fretePor = $fretePor;
	}

	public function getFretePor()
	{
		return $this->fretePor;
	}

	public function setDataArquivo($dataArquivo)
	{
		$this->dataArquivo = $dataArquivo;
	}

	public function getDataArquivo()
	{
		return $this->dataArquivo;
	}

	public function setTipo($tipo)
	{
		$this->tipo = $tipo;
	}

	public function getTipo()
	{
		return $this->tipo;
	}

	public function setRemessaFiscal($remessaFiscal)
	{
		$this->remessaFiscal = $remessaFiscal;
	}

	public function getRemessaFiscal()
	{
		return $this->remessaFiscal;
	}

	public function setRemessaFiscalRetorno($remessaFiscalRetorno)
	{
		$this->remessaFiscalRetorno = $remessaFiscalRetorno;
	}

	public function getRemessaFiscalRetorno()
	{
		return $this->remessaFiscalRetorno;
	}

	public function setRemessaNfe($remessaNfe)
	{
		$this->remessaNfe = $remessaNfe;
	}

	public function getRemessaNfe()
	{
		return $this->remessaNfe;
	}

	public function setDataEnvioGrafica($dataEnvioGrafica)
	{
		$this->dataEnvioGrafica = $dataEnvioGrafica;
	}

	public function getDataEnvioGrafica()
	{
		return $this->dataEnvioGrafica;
	}

	public function setValorPis($valorPis)
	{
		$this->valorPis = $valorPis;
	}

	public function getValorPis()
	{
		return $this->valorPis;
	}

	public function setValorCofins($valorCofins)
	{
		$this->valorCofins = $valorCofins;
	}

	public function getValorCofins()
	{
		return $this->valorCofins;
	}

	public function setValorCsll($valorCsll)
	{
		$this->valorCsll = $valorCsll;
	}

	public function getValorCsll()
	{
		return $this->valorCsll;
	}

	public function setNumeroPedido($numeroPedido)
	{
		$this->numeroPedido = $numeroPedido;
	}

	public function getNumeroPedido()
	{
		return $this->numeroPedido;
	}

	public function setDataEnvioFox($dataEnvioFox)
	{
		$this->dataEnvioFox = $dataEnvioFox;
	}

	public function getDataEnvioFox()
	{
		return $this->dataEnvioFox;
	}

	public function setDataRecebimentoFox($dataRecebimentoFox)
	{
		$this->dataRecebimentoFox = $dataRecebimentoFox;
	}

	public function getDataRecebimentoFox()
	{
		return $this->dataRecebimentoFox;
	}

	public function setIndicadorFrete($indicadorFrete)
	{
		$this->indicadorFrete = $indicadorFrete;
	}

	public function getIndicadorFrete()
	{
		return $this->indicadorFrete;
	}

	public function setTransportadoraId($transportadoraId)
	{
		$this->transportadoraId = $transportadoraId;
	}

	public function getTransportadoraId()
	{
		return $this->transportadoraId;
	}

	public function setPesoLiquido($pesoLiquido)
	{
		$this->pesoLiquido = $pesoLiquido;
	}

	public function getPesoLiquido()
	{
		return $this->pesoLiquido;
	}

	public function setValorFrete($valorFrete)
	{
		$this->valorFrete = $valorFrete;
	}

	public function getValorFrete()
	{
		return $this->valorFrete;
	}

	public function setValorSeguro($valorSeguro)
	{
		$this->valorSeguro = $valorSeguro;
	}

	public function getValorSeguro()
	{
		return $this->valorSeguro;
	}

	public function setValorDespesas($valorDespesas)
	{
		$this->valorDespesas = $valorDespesas;
	}

	public function getValorDespesas()
	{
		return $this->valorDespesas;
	}

	public function setValorMercadorias($valorMercadorias)
	{
		$this->valorMercadorias = $valorMercadorias;
	}

	public function getValorMercadorias()
	{
		return $this->valorMercadorias;
	}

	public function setValorBasePis($valorBasePis)
	{
		$this->valorBasePis = $valorBasePis;
	}

	public function getValorBasePis()
	{
		return $this->valorBasePis;
	}

	public function setValorBaseCofins($valorBaseCofins)
	{
		$this->valorBaseCofins = $valorBaseCofins;
	}

	public function getValorBaseCofins()
	{
		return $this->valorBaseCofins;
	}

	public function setValorBaseCsll($valorBaseCsll)
	{
		$this->valorBaseCsll = $valorBaseCsll;
	}

	public function getValorBaseCsll()
	{
		return $this->valorBaseCsll;
	}

	public function setValorIssRet($valorIssRet)
	{
		$this->valorIssRet = $valorIssRet;
	}

	public function getValorIssRet()
	{
		return $this->valorIssRet;
	}

	public function setValorImposto($valorImposto)
	{
		$this->valorImposto = $valorImposto;
	}

	public function getValorImposto()
	{
		return $this->valorImposto;
	}

	public function setAliquotaImposto($aliquotaImposto)
	{
		$this->aliquotaImposto = $aliquotaImposto;
	}

	public function getAliquotaImposto()
	{
		return $this->aliquotaImposto;
	}

	public function setCodigoServico($codigoServico)
	{
		$this->codigoServico = $codigoServico;
	}

	public function getCodigoServico()
	{
		return $this->codigoServico;
	}

	public function setMonitoramentoDiferido($monitoramentoDiferido)
	{
		$this->monitoramentoDiferido = $monitoramentoDiferido;
	}

	public function getMonitoramentoDiferido()
	{
		return $this->monitoramentoDiferido;
	}

	public function setInformacoesComplementares($informacoesComplementares)
	{
		$this->informacoesComplementares = $informacoesComplementares;
	}

	public function getInformacoesComplementares()
	{
		return $this->informacoesComplementares;
	}
}