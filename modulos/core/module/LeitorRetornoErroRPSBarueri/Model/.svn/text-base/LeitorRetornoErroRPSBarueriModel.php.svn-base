<?php

namespace module\LeitorRetornoErroRPSBarueri;

require_once _MODULEDIR_ . "core/module/LeitorRetornoErroRPSBarueri/Model/HeaderTipo1ArquivoErroRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoErroRPSBarueri/Model/DetalheTipo2ArquivoErroRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoErroRPSBarueri/Model/DetalheTipo3ArquivoErroRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoErroRPSBarueri/Model/TrailerTipo9ArquivoErroRPSBarueriModel.php";

use module\LeitorRetornoErroRPSBarueri\HeaderTipo1ArquivoErroRPSBarueriModel;
use module\LeitorRetornoErroRPSBarueri\DetalheTipo2ArquivoErroRPSBarueriModel;
use module\LeitorRetornoErroRPSBarueri\DetalheTipo3ArquivoErroRPSBarueriModel;
use module\LeitorRetornoErroRPSBarueri\TrailerTipo9ArquivoErroRPSBarueriModel;


class LeitorRetornoErroRPSBarueriModel
{

    public function __construct()
    {
		$this->headerRetorno = new HeaderTipo1ArquivoErroRPSBarueriModel();
        $this->detalheRetornoTipo2 = new DetalheTipo2ArquivoErroRPSBarueriModel();
        $this->detalheRetornoTipo3 = new DetalheTipo3ArquivoErroRPSBarueriModel();
        $this->trailerRetorno = new TrailerTipo9ArquivoErroRPSBarueriModel();
    }

    public function lerRegistro($linha){

        $tipoRegistro = (int)substr($linha, 0, 1);
        $this->lerLinha($linha);
        
        if($tipoRegistro === 1){
            return $this->headerRetorno;
        }elseif($tipoRegistro === 2){
            return $this->detalheRetornoTipo2;
        }elseif($tipoRegistro === 3){
            return $this->detalheRetornoTipo3;
        }elseif($tipoRegistro === 9){
            return $this->trailerRetorno;
        }
        
    }
    

    public function lerLinha($linha){

		$tipoRegistro = (int)substr($linha, 0, 1);

		if($tipoRegistro === 1){
            $this->headerRetorno->setTipoRegistro($tipoRegistro);
            $this->headerRetorno->setInscricaoContribuinte(substr($linha, 1, 7));
            $this->headerRetorno->setVersaoLayOut(substr($linha, 8, 6));
            $this->headerRetorno->setIdentificacaoRemessaContribuinte(substr($linha, 14, 11));
            $this->headerRetorno->setCodigoErro(explode(';', rtrim(substr($linha, 1970), "\r"), -1));

		}elseif($tipoRegistro === 2){
            $this->detalheRetornoTipo2->setTipoRegistro($tipoRegistro);
            $this->detalheRetornoTipo2->setTipoRPS(substr($linha, 1, 5));
            $this->detalheRetornoTipo2->setSerieRPS(substr($linha, 6, 4));
            $this->detalheRetornoTipo2->setSerieNfe(substr($linha, 10, 5));
            $this->detalheRetornoTipo2->setNumeroRPS(substr($linha, 15, 10));
            $this->detalheRetornoTipo2->setDataRPS($this->formatarStringData(substr($linha, 25, 8)));
            $this->detalheRetornoTipo2->setHoraRPS(substr($linha, 33, 6));
            $this->detalheRetornoTipo2->setSituacaoRPS(substr($linha, 39, 1));
            $this->detalheRetornoTipo2->setCodigoMotivoCancelamento(substr($linha, 40, 2));
            $this->detalheRetornoTipo2->setNumeroNfeCanceladaOuSubstituida(substr($linha, 42, 7));
            $this->detalheRetornoTipo2->setSerieNfeCanceladaOuSubstituida(substr($linha, 49, 5));
            $this->detalheRetornoTipo2->setDataEmissaoNfeASerCanceladaOuSubstituida($this->formatarStringData(substr($linha, 54, 8)));
            $this->detalheRetornoTipo2->setDescricaoCancelamento(substr($linha, 62, 180));
            $this->detalheRetornoTipo2->setCodigoServicoPrestado(substr($linha, 242, 9));
            $this->detalheRetornoTipo2->setLocalPrestacaoServico(substr($linha, 251, 1));
            $this->detalheRetornoTipo2->setServicoPrestadoViasPublicas(substr($linha, 252, 1));
            $this->detalheRetornoTipo2->setEnderecoServicoPrestado(substr($linha, 253, 75));
            $this->detalheRetornoTipo2->setNumeroServicoPrestado(substr($linha, 328, 9));
            $this->detalheRetornoTipo2->setComplementoServicoPrestado(substr($linha, 338, 30));
            $this->detalheRetornoTipo2->setBairroServicoPrestado(substr($linha, 367, 40));
            $this->detalheRetornoTipo2->setCidadeServicoPrestado(substr($linha, 407, 40));
            $this->detalheRetornoTipo2->setUFServicoPrestado(substr($linha, 447, 2));
            $this->detalheRetornoTipo2->setCEPServicoPrestado(substr($linha, 449, 8));
            $this->detalheRetornoTipo2->setQuantidadeServico(substr($linha, 457, 6));
            $this->detalheRetornoTipo2->setValorServico($this->formatarStringValor(substr($linha, 463, 15)));
            $this->detalheRetornoTipo2->setReservado(substr($linha, 478, 5));
            $this->detalheRetornoTipo2->setValorTotalRetencoes($this->formatarStringValor(substr($linha, 483, 15)));
            $this->detalheRetornoTipo2->setTipoTomador(substr($linha, 498, 1));
            $this->detalheRetornoTipo2->setPaisNacionalidade(substr($linha, 499, 3));
            $this->detalheRetornoTipo2->setServicoPrestadoExportacao(substr($linha, 502, 1));
            $this->detalheRetornoTipo2->setTipoDocumento(substr($linha, 503, 1));
            $this->detalheRetornoTipo2->setNumeroDocumento(substr($linha, 504, 14));
            $this->detalheRetornoTipo2->setNomeTomador(substr($linha, 518, 60));
            $this->detalheRetornoTipo2->setEndereco(substr($linha, 578, 75));
            $this->detalheRetornoTipo2->setNumero(substr($linha, 653, 9));
            $this->detalheRetornoTipo2->setComplemento(substr($linha, 662, 30));
            $this->detalheRetornoTipo2->setBairro(substr($linha, 692, 40));
            $this->detalheRetornoTipo2->setCidade(substr($linha, 732, 40));
            $this->detalheRetornoTipo2->setUF(substr($linha, 772, 2));
            $this->detalheRetornoTipo2->setCEP(substr($linha, 774, 8));
            $this->detalheRetornoTipo2->setEmail(substr($linha, 782, 152));
            $this->detalheRetornoTipo2->setFatura(substr($linha, 934, 6));
            $this->detalheRetornoTipo2->setValorFatura($this->formatarStringValor(substr($linha, 940, 15)));
            $this->detalheRetornoTipo2->setFormaPagamento(substr($linha, 955, 15));
            $this->detalheRetornoTipo2->setDiscriminacaoServico(substr($linha, 970, 1000));
            $this->detalheRetornoTipo2->setCodigoErro(explode(';', rtrim(substr($linha, 1970), "\r"), -1));

		}elseif($tipoRegistro === 3){
            $this->detalheRetornoTipo3->setTipoRegistro($tipoRegistro);
            $this->detalheRetornoTipo3->setCodigoDeOutrosValores(substr($linha, 1, 2));
            $this->detalheRetornoTipo3->setValor($this->formatarStringValor(substr($linha, 3, 15)));
            $this->detalheRetornoTipo3->setCodigoErro(explode(';', rtrim(substr($linha, 1970), "\r"), -1));

        }elseif($tipoRegistro === 9){
            $this->trailerRetorno->setTipoRegistro($tipoRegistro);
            $this->trailerRetorno->setNumeroTotalLinhasDoArquivo(substr($linha, 1, 7));
            $this->trailerRetorno->setValorTotalServicosContidosNoArquivo($this->formatarStringValor(substr($linha, 8, 15)));
            $this->trailerRetorno->setValorTotalRetencoesEOutrosValores($this->formatarStringValor(substr($linha, 23, 15)));
            $this->trailerRetorno->setCodigoErro(explode(';', rtrim(substr($linha, 1970), "\r"), -1));

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

}


?>