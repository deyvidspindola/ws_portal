<?php

namespace module\LeitorRetornoRPSBarueri;

require_once _MODULEDIR_ . "core/module/LeitorRetornoRPSBarueri/Model/HeaderTipo1ArquivoRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoRPSBarueri/Model/DetalheTipo2ArquivoRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoRPSBarueri/Model/DetalheTipo3ArquivoRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoRPSBarueri/Model/DetalheTipo4ArquivoRPSBarueriModel.php";
require_once _MODULEDIR_ . "core/module/LeitorRetornoRPSBarueri/Model/TrailerTipo9ArquivoRPSBarueriModel.php";

use module\LeitorRetornoRPSBarueri\HeaderTipo1ArquivoRPSBarueriModel;
use module\LeitorRetornoRPSBarueri\DetalheTipo2ArquivoRPSBarueriModel;
use module\LeitorRetornoRPSBarueri\DetalheTipo3ArquivoRPSBarueriModel;
use module\LeitorRetornoRPSBarueri\DetalheTipo4ArquivoRPSBarueriModel;
use module\LeitorRetornoRPSBarueri\TrailerTipo9ArquivoRPSBarueriModel;

class LeitorRetornoRPSBarueriModel{

    private $headerRetorno;
    private $detalhe1Retorno;
    private $detalhe2Retorno;
    private $trailerRetorno;

	public function __construct(){

		$this->headerRetorno = new HeaderTipo1ArquivoRPSBarueriModel();
        $this->detalheRetornoTipo2 = new DetalheTipo2ArquivoRPSBarueriModel();
        $this->detalheRetornoTipo3 = new DetalheTipo3ArquivoRPSBarueriModel();
        $this->detalheRetornoTipo4 = new DetalheTipo4ArquivoRPSBarueriModel();
        $this->trailerRetorno = new TrailerTipo9ArquivoRPSBarueriModel();

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
        }elseif($tipoRegistro === 4){
            return $this->detalheRetornoTipo4;
        }elseif($tipoRegistro === 9){
            return $this->trailerRetorno;
        }
        
    }
    

    public function lerLinha($linha){

		$tipoRegistro = (int)substr($linha, 0, 1);

		if($tipoRegistro === 1){
            $this->headerRetorno->setTipoRegistro($tipoRegistro);
            $this->headerRetorno->setInscricaoContribuinte(substr($linha, 1, 7));
            $this->headerRetorno->setInicioPeriodoTransferencia(substr($linha, 8, 8));
            $this->headerRetorno->setTerminoPeriodoTransferencia(substr($linha, 16, 8));
            $this->headerRetorno->setVersaoLayOut(substr($linha, 24, 6));
            $this->headerRetorno->setIdRemessaContribuinte(substr($linha, 30, 11));

		}elseif($tipoRegistro === 2){
            $this->detalheRetornoTipo2->setTipoRegistro($tipoRegistro);
            $this->detalheRetornoTipo2->setSerieNfe(substr($linha, 1, 5));
            $this->detalheRetornoTipo2->setNumeroNfe(substr($linha, 6, 6));
            $this->detalheRetornoTipo2->setDataNfe($this->formatarStringData(substr($linha, 12, 8)));
            $this->detalheRetornoTipo2->setHoraNfe(substr($linha, 20, 6));
            $this->detalheRetornoTipo2->setCodigoAutenticidade(substr($linha, 26, 24));
            $this->detalheRetornoTipo2->setSerieRPS(substr($linha, 50, 4));
            $this->detalheRetornoTipo2->setNumeroRPS(substr($linha, 54, 10));
            $this->detalheRetornoTipo2->setTributacao(substr($linha, 64, 1));
            $this->detalheRetornoTipo2->setISSRetido(substr($linha, 65, 1));
            $this->detalheRetornoTipo2->setSituacaoNfe(substr($linha, 66, 1));
            $this->detalheRetornoTipo2->setDataCancelamentoNfe($this->formatarStringData(substr($linha, 67, 8)));
            $this->detalheRetornoTipo2->setNumeroGuia(substr($linha, 75, 10));
            $this->detalheRetornoTipo2->setDataPagamentoGuia($this->formatarStringData(substr($linha, 85, 8)));
            $this->detalheRetornoTipo2->setNumeroDocumento(substr($linha, 93, 14));
            $this->detalheRetornoTipo2->setNomeTomador(substr($linha, 107, 100));
            $this->detalheRetornoTipo2->setEndereco(substr($linha, 207, 100));
            $this->detalheRetornoTipo2->setNumero(substr($linha, 307, 9));
            $this->detalheRetornoTipo2->setComplemento(substr($linha, 316, 20));
            $this->detalheRetornoTipo2->setBairro(substr($linha, 336, 40));
            $this->detalheRetornoTipo2->setCidade(substr($linha, 336, 40));
            $this->detalheRetornoTipo2->setUF(substr($linha, 416, 2));
            $this->detalheRetornoTipo2->setCEP(substr($linha, 418, 8));
            $this->detalheRetornoTipo2->setPais(substr($linha, 426, 50));
            $this->detalheRetornoTipo2->setEmail(substr($linha, 476, 152));
            $this->detalheRetornoTipo2->setDiscriminacaoServico(substr($linha, 628, 1000));

		}elseif($tipoRegistro === 3){
            $this->detalheRetornoTipo3->setTipoRegistro($tipoRegistro);
            $this->detalheRetornoTipo3->setQuantidadeServico(substr($linha, 1, 6));
            $this->detalheRetornoTipo3->setDescricaoServico(substr($linha, 7, 60));
            $this->detalheRetornoTipo3->setCodigoServico(substr($linha, 67, 9));
            $this->detalheRetornoTipo3->setValorUnitarioServico($this->formatarStringValor(substr($linha, 76, 15)));
            $this->detalheRetornoTipo3->setAliquotaServico(substr($linha, 91, 4));

        }elseif($tipoRegistro === 4){
            $this->detalheRetornoTipo4->setTipoRegistro($tipoRegistro);
            $this->detalheRetornoTipo4->setCodigoOutrosValores($this->formatarStringValor(substr($linha, 1, 2)));
            $this->detalheRetornoTipo4->setValor($this->formatarStringValor(substr($linha, 3, 15)));

        }elseif($tipoRegistro === 9){
            $this->trailerRetorno->setTipoRegistro($tipoRegistro);
            $this->trailerRetorno->setNumeroTotalLinhasDoArquivo(substr($linha, 1, 7));
            $this->trailerRetorno->setValorTotalServicosContidosNoArquivo($this->formatarStringValor(substr($linha, 8, 15)));
            $this->trailerRetorno->setValorTotalRetencoesEOutrosValores($this->formatarStringValor(substr($linha, 23, 15)));

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