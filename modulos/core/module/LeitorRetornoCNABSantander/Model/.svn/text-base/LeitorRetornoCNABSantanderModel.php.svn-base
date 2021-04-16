<?php

namespace module\LeitorRetornoCNABSantander;

class LeitorRetornoCNABSantanderModel {

	private $headerRetorno;
	private $detalheRetorno;

	public function __construct(){

		$this->headerRetorno = new HeaderRetornoCNABSantanderModel();
		$this->detalheRetorno = new DetalheRetornoCNABSantanderModel();

	}

	public function processarArquivo($arquivo){}

	public function lerRegistro($linhaA, $linhaB){

		$this->lerLinha($linhaA);
		$this->lerLinha($linhaB);

		$tipoRegistro = (int)substr($linhaA, 7, 1);

		if($tipoRegistro === 0 || $tipoRegistro === 1){
			return $this->headerRetorno;
		}elseif($tipoRegistro === 3){
			return $this->detalheRetorno;
		}else{
			return null;
		}

	}

	public function lerLinha($linha){

		$tipoRegistro = (int)substr($linha, 7, 1);

		if($tipoRegistro === 0){

			$this->headerRetorno->setCodigoBancoCompensacao(substr($linha, 0, 3));
			$this->headerRetorno->setLoteServico(substr($linha, 3, 4));
			$this->headerRetorno->setTipoRegistro($tipoRegistro);
			$this->headerRetorno->setTipoInscricaoEmpresa(substr($linha, 16, 1));
			$this->headerRetorno->setNumeroInscricaoEmpresa(substr($linha, 17, 15));
			$this->headerRetorno->setAgenciaBeneficiario(substr($linha, 32, 4));
			$this->headerRetorno->setDigitoAgenciaBeneficiario(substr($linha, 36, 1));
			$this->headerRetorno->setNumeroContaCorrente(substr($linha, 37, 9));
			$this->headerRetorno->setDigitoVerificadorContaCorrente(substr($linha, 46, 1));
			$this->headerRetorno->setCodigoBeneficiario(substr($linha, 52, 9));
			$this->headerRetorno->setNomeEmpresa(substr($linha, 72, 30));
			$this->headerRetorno->setNomeBanco(substr($linha, 102, 30));
			$this->headerRetorno->setCodigoRemessaRetorno(substr($linha, 142, 1));
			$this->headerRetorno->setDataGeracaoArquivo($this->formatarStringData(substr($linha, 143, 8)));
			$this->headerRetorno->setNumeroSequencialArquivo(substr($linha, 157, 6));
			$this->headerRetorno->setNumeroVersaoLayoutArquivo(substr($linha, 163, 3));

		}elseif($tipoRegistro === 1){

			$this->headerRetorno->setNumeroLoteRetorno(substr($linha, 3, 4));
			$this->headerRetorno->setTipoOperacao(substr($linha, 8, 1));
			$this->headerRetorno->setTipoServico(substr($linha, 9, 2));
			$this->headerRetorno->setNumeroVersaoLayoutLote(substr($linha, 13, 3));
			$this->headerRetorno->setNumeroRetorno(substr($linha, 183, 8));
			$this->headerRetorno->setDataGravacaoRemessaRetorno($this->formatarStringData(substr($linha, 191, 8)));

		}elseif($tipoRegistro === 3){

			$codigoSegmentoRegistroDetalhe = substr($linha, 13, 1);

			if($codigoSegmentoRegistroDetalhe === 'T'){

				$this->detalheRetorno->setCodigoBancoCompensacao(substr($linha, 0, 3));
				$this->detalheRetorno->setNumeroLoteRetorno(substr($linha, 3, 4));
				$this->detalheRetorno->setTipoRegistro($tipoRegistro);
				$this->detalheRetorno->setNumeroSequencialRegistroLote(substr($linha, 8, 5));
				$this->detalheRetorno->setCodigoSegmentoRegistroDetalhe(substr($linha, 13, 1));
				$this->detalheRetorno->setCodigoMovimento((int)substr($linha, 15, 2));
				$this->detalheRetorno->setAgenciaBeneficiario(substr($linha, 17, 4));
				$this->detalheRetorno->setDigitoAgenciaBeneficiario(substr($linha, 21, 1));
				$this->detalheRetorno->setNumeroContaCorrente(substr($linha, 22, 9));
				$this->detalheRetorno->setDigitoContaCorrente(substr($linha, 31, 1));
				$this->detalheRetorno->setNossoNumero(substr($linha, 40, 13));
				$this->detalheRetorno->setCodigoCarteira(substr($linha, 53, 1));
				$this->detalheRetorno->setSeuNumero(substr($linha, 54, 15));
				$this->detalheRetorno->setDataVencimentoTitulo($this->formatarStringData(substr($linha, 69, 8)));
				$this->detalheRetorno->setValorNominalTitulo($this->formatarStringValor(substr($linha, 77, 15)));
				$this->detalheRetorno->setNumeroBancoCobradorRecebedor(substr($linha, 92, 3));
				$this->detalheRetorno->setAgenciaCobradoraRecebedora(substr($linha, 95, 4));
				$this->detalheRetorno->setDigitoAgenciaBeneficiario(substr($linha, 99, 1));
				$this->detalheRetorno->setIdentificacaoTituloEmpresa(substr($linha, 100, 25));
				$this->detalheRetorno->setCodigoMoeda(substr($linha, 125, 2));
				$this->detalheRetorno->setTipoInscricaoPagador(substr($linha, 127, 1));
				$this->detalheRetorno->setNumeroInscricaoPagador(substr($linha, 128, 15));
				$this->detalheRetorno->setNomePagador(trim(substr($linha, 143, 40)));
				$this->detalheRetorno->setContaCobranca(substr($linha, 183, 10));
				$this->detalheRetorno->setValorTarifaCustas($this->formatarStringValor(substr($linha, 193, 15)));
				$this->detalheRetorno->setIdentificacaoOcorrencia($this->formatarIdentificacaoOcorrencia(substr($linha, 208, 10)));

			}elseif($codigoSegmentoRegistroDetalhe === 'U'){

				$this->detalheRetorno->setValorJurosMultaEncargos($this->formatarStringValor(substr($linha, 17, 15)));
				$this->detalheRetorno->setValorDescontoConcedido($this->formatarStringValor(substr($linha, 32, 15)));
				$this->detalheRetorno->setValorAbatimento($this->formatarStringValor(substr($linha, 47, 15)));
				$this->detalheRetorno->setValorIOF($this->formatarStringValor(substr($linha, 62, 15)));
				$this->detalheRetorno->setValorPago($this->formatarStringValor(substr($linha, 77, 15)));
				$this->detalheRetorno->setValorLiquidoCreditado($this->formatarStringValor(substr($linha, 92, 15)));
				$this->detalheRetorno->setValorOutrasDespesas($this->formatarStringValor(substr($linha, 107, 15)));
				$this->detalheRetorno->setValorOutrosCreditos($this->formatarStringValor(substr($linha, 122, 15)));
				$this->detalheRetorno->setDataOcorrencia($this->formatarStringData(substr($linha, 137, 8)));
				$this->detalheRetorno->setDataEfetivacaoCredito($this->formatarStringData(substr($linha, 145, 8)));
				$this->detalheRetorno->setCodigoOcorrenciaPagador(substr($linha, 153, 4));
				$this->detalheRetorno->setDataOcorrenciaPagador($this->formatarStringData(substr($linha, 157, 8)));
				$this->detalheRetorno->setValorOcorrenciaPagador($this->formatarStringValor(substr($linha, 165, 15)));
				$this->detalheRetorno->setComplementoOcorrenciaPagador(substr($linha, 180, 30));

			}

		}

	}

	public function formatarStringData($string){
		
		if($string === '00000000'){
			return null;
		}

		$dia = substr($string, 0, 2);
		$mes = substr($string, 2, 2);
		$ano = substr($string, 4, 4);
		
		return "{$ano}-{$mes}-{$dia}";

	}

	public function formatarStringValor($string){

		$valor = substr($string, 0, 13) . "." . substr($string, -2);

		return (float)$valor;

	}

	public function formatarIdentificacaoOcorrencia($string){
		return array_filter(array_map('intval', str_split($string, 2)));
	}

}