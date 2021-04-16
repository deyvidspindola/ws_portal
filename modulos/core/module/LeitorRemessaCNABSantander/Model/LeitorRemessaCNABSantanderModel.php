<?php

namespace module\LeitorRemessaCNABSantander;

require(_MODULEDIR_ . "core/module/LeitorRemessaCNABSantander/Model/HeaderRemessaCNABSantanderModel.php");
require(_MODULEDIR_ . "core/module/LeitorRemessaCNABSantander/Model/DetalheRemessaCNABSantanderModel.php");

class LeitorRemessaCNABSantanderModel {

    private $headerRemessa;
    private $detalheRemessa;

    public function __construct() {

        $this->headerRemessa = new HeaderRemessaCNABSantanderModel();
        $this->detalheRemessa = new DetalheRemessaCNABSantanderModel();
    }

    public function processarArquivo($arquivo) {
        
    }

    public function lerRegistro($linhaA, $linhaB) {
        
        // não vamos utilizar o segmento R e S do arquivo de remessa
        if (substr($linhaA, 13, 1) == 'R' || substr($linhaA, 13, 1) == 'S') {
            return null;
        }
        
        $tipoRegistro = (int) substr($linhaA, 7, 1);

        $this->lerLinha($linhaA);
        $this->lerLinha($linhaB);

        if ($tipoRegistro === 0 || $tipoRegistro === 1) {
            return $this->headerRemessa;
        } elseif ($tipoRegistro === 3) {
            return $this->detalheRemessa;
        } else {
            return null;
        }
    }

    public function lerLinha($linha) {

        $tipoRegistro = (int) substr($linha, 7, 1);
         
        if ($tipoRegistro === 0) {

            $this->headerRemessa->setCodigoBancoCompensacao(substr($linha, 0, 3));
            $this->headerRemessa->setLoteServico(substr($linha, 3, 4));
            $this->headerRemessa->setTipoRegistro($tipoRegistro);
            $this->headerRemessa->setTipoInscricaoEmpresa(substr($linha, 16, 1));
            $this->headerRemessa->setNumeroInscricaoEmpresa(substr($linha, 17, 15));
            $this->headerRemessa->setCodigoTransmissao(substr($linha, 32, 15));
            $this->headerRemessa->setNomeEmpresa(substr($linha, 72, 30));
            $this->headerRemessa->setNomeBanco(substr($linha, 102, 30));
            $this->headerRemessa->setCodigoRemessa(substr($linha, 142, 1));
            $this->headerRemessa->setDataGeracaoArquivo(substr($linha, 143, 8));
            $this->headerRemessa->setNumeroSequencialArquivo(substr($linha, 157, 6));
            $this->headerRemessa->setNumeroVersaoLayoutArquivo(substr($linha, 163, 3));
        } elseif ($tipoRegistro === 1) {

            $this->headerRemessa->setCodigoBancoCompensacao(substr($linha, 0, 3));
            $this->headerRemessa->setNumeroLoteRemessa(substr($linha, 3, 4));
            $this->headerRemessa->setTipoRegistro($tipoRegistro);
            $this->headerRemessa->setTipoOperacao(substr($linha, 8, 1));
            $this->headerRemessa->setTipoServico(substr($linha, 9, 2));
            $this->headerRemessa->setNumeroVersaoLayoutLote(substr($linha, 13, 3));
            $this->headerRemessa->setTipoInscricaoEmpresa(substr($linha, 17, 1));
            $this->headerRemessa->setNumeroInscricaoEmpresa(substr($linha, 18, 15));
            $this->headerRemessa->setCodigoTransmissao(substr($linha, 53, 15));
            $this->headerRemessa->setNomeBeneficiario(substr($linha, 73, 30));
            $this->headerRemessa->setMensagem1(substr($linha, 103, 40));
            $this->headerRemessa->setMensagem1(substr($linha, 143, 40));
            $this->headerRemessa->setDataGravacaoRemessaRetorno(substr($linha, 191, 8));
        } elseif ($tipoRegistro === 3) {

            $codigoSegmentoRegistroDetalhe = substr($linha, 13, 1);

            if ($codigoSegmentoRegistroDetalhe === 'P') {

                $this->detalheRemessa->setCodigoBancoCompensacao(substr($linha, 0, 3));
                $this->detalheRemessa->setNumeroLoteRemessa(substr($linha, 3, 4));
                $this->detalheRemessa->setTipoRegistro($tipoRegistro);
                $this->detalheRemessa->setNumeroSequencialRegistroLote(substr($linha, 8, 5));
                $this->detalheRemessa->setCodigoSegmentoRegistroDetalhe(substr($linha, 13, 1));
                $this->detalheRemessa->setCodigoMovimento((int) substr($linha, 15, 2));
                $this->detalheRemessa->setAgenciaDestinatario(substr($linha, 17, 4));
                $this->detalheRemessa->setDigitoAgenciaDestinatario(substr($linha, 21, 1));
                $this->detalheRemessa->setNumeroContaCorrente(substr($linha, 22, 9));
                $this->detalheRemessa->setDigitoContaCorrente(substr($linha, 31, 1));
                $this->detalheRemessa->setContaCobrancaDestinataria(substr($linha, 32, 9));
                $this->detalheRemessa->setDigitoContaCobrancaDestinataria(substr($linha, 41, 1));
                $this->detalheRemessa->setNossoNumero(substr($linha, 44, 13));
                $this->detalheRemessa->setTipoCobranca(substr($linha, 57, 1));
                $this->detalheRemessa->setFormaCadastramento(substr($linha, 58, 1));
                $this->detalheRemessa->setTipoDocumento(substr($linha, 59, 1));
                $this->detalheRemessa->setSeuNumero(substr($linha, 62, 15));
                $this->detalheRemessa->setDataVencimentoTitulo(substr($linha, 77, 8));
                $this->detalheRemessa->setDataVencimentoT(substr($linha, 77, 8));

                $this->detalheRemessa->setValorNominalTitulo($this->formatarStringValor(substr($linha, 85, 15)));
                $this->detalheRemessa->setAgenciaEncarregadaCobranca(substr($linha, 100, 4));
                $this->detalheRemessa->setDigitoAgenciaBeneficiario(substr($linha, 100, 4));
                $this->detalheRemessa->setEspecieTitulo(substr($linha, 106, 2));
                $this->detalheRemessa->setIdentificacaoTituloAceite(substr($linha, 108, 1));
                $this->detalheRemessa->setDataEmissaoTitulo(substr($linha, 109, 8));
                $this->detalheRemessa->setCodigoJurosMora(substr($linha, 117, 1));
                $this->detalheRemessa->setDataJurosMora(substr($linha, 118, 8));
                $this->detalheRemessa->setValorMora($this->formatarStringValor(substr($linha, 126, 15)));
                $this->detalheRemessa->setCodigoDesconto1(substr($linha, 141, 1));
                $this->detalheRemessa->setDataDesconto1(substr($linha, 142, 8));
                $this->detalheRemessa->setValorDescontoConcedido($this->formatarStringValor(substr($linha, 150, 15)));
                $this->detalheRemessa->setValorIOF($this->formatarStringValor(substr($linha, 165, 15)));
                $this->detalheRemessa->setValorAbatimento($this->formatarStringValor(substr($linha, 180, 15)));
                $this->detalheRemessa->setIdentificacaoTituloEmpresa(substr($linha, 195, 25));
                $this->detalheRemessa->setCodigoProtesto(substr($linha, 220, 1));
                $this->detalheRemessa->setNumeroDiasProtesto(substr($linha, 222, 2));
                $this->detalheRemessa->setCodigoBaixaDevolucao(substr($linha, 223, 1));
                $this->detalheRemessa->setNumeroDiasBaixaDevolucao(substr($linha, 225, 2));
                $this->detalheRemessa->setCodigoMoeda(substr($linha, 227, 2));
            } elseif ($codigoSegmentoRegistroDetalhe === 'Q') {

                $this->detalheRemessa->setCodigoBancoCompensacao(substr($linha, 0, 3));
                $this->detalheRemessa->setNumeroLoteRemessa(substr($linha, 3, 4));
                $this->detalheRemessa->setTipoRegistro($tipoRegistro);
                $this->detalheRemessa->setNumeroSequencialRegistroLote(substr($linha, 8, 5));
                $this->detalheRemessa->setCodigoSegmentoRegistroDetalhe(substr($linha, 13, 1));
                $this->detalheRemessa->setCodigoMovimento((int) substr($linha, 15, 2));
                $this->detalheRemessa->setTipoInscricaoPagador(substr($linha, 17, 4));
                $this->detalheRemessa->setNumeroInscricaoPagador(substr($linha, 18, 15));
                $this->detalheRemessa->setNomePagador(substr($linha, 33, 40));
                $this->detalheRemessa->setEnderecoPagador(substr($linha, 73, 40));
                $this->detalheRemessa->setBairroPagador(substr($linha, 113, 15));
                $this->detalheRemessa->setCepPagador(substr($linha, 128, 5));
                $this->detalheRemessa->setSulfixoCepPagador(substr($linha, 133, 3));
                $this->detalheRemessa->setCidadePagador(substr($linha, 136, 15));
                $this->detalheRemessa->setUnidadeFederacaoPagador(substr($linha, 151, 2));
                $this->detalheRemessa->setTipoInscricaoSacadorAvalista(substr($linha, 153, 1));
                $this->detalheRemessa->setNumeroInscricaoSacadorAvalista(substr($linha, 154, 15));
                $this->detalheRemessa->setNomeSacadorAvalista(substr($linha, 169, 40));
                $this->detalheRemessa->setIdentificadorCarne(substr($linha, 209, 3));
                $this->detalheRemessa->setSequencialParcela(substr($linha, 212, 3));
                $this->detalheRemessa->setQuantidadeTotalParcela(substr($linha, 215, 3));
                $this->detalheRemessa->setNumeroPlano(substr($linha, 218, 3));
            } elseif ($codigoSegmentoRegistroDetalhe === 'R') {

                $this->detalheRemessa->setCodigoBancoCompensacao(substr($linha, 0, 3));
                $this->detalheRemessa->setNumeroLoteRemessa(substr($linha, 3, 4));
                $this->detalheRemessa->setTipoRegistro($tipoRegistro);
                $this->detalheRemessa->setNumeroSequencialRegistroLote(substr($linha, 8, 5));
                $this->detalheRemessa->setCodigoSegmentoRegistroDetalhe(substr($linha, 13, 1));
                $this->detalheRemessa->setCodigoMovimento((int) substr($linha, 15, 2));
                $this->detalheRemessa->setCodigoDesconto2(substr($linha, 17, 1));
                $this->detalheRemessa->setDataDesconto2(substr($linha, 18, 8));
                $this->detalheRemessa->setValorConcedido($this->formatarStringValor(substr($linha, 26, 15)));
                $this->detalheRemessa->setCodigoMulta(substr($linha, 65, 1));
                $this->detalheRemessa->setDataMulta(substr($linha, 66, 8));
                $this->detalheRemessa->setValorAplicado($this->formatarStringValor(substr($linha, 74, 15)));
                $this->detalheRemessa->setMensagem3(substr($linha, 99, 40));
                $this->detalheRemessa->setMensagem4(substr($linha, 179, 40));
            }
        }
    }

    public function formatarStringData($string) {

        if ($string === '00000000') {
            return null;
        }

        $dia = substr($string, 0, 2);
        $mes = substr($string, 2, 2);
        $ano = substr($string, 4, 4);

        return "{$ano}-{$mes}-{$dia}";
    }

    public function formatarStringValor($string) {

        $valor = substr($string, 0, 13) . "." . substr($string, -2);

        return (float) $valor;
    }

    public function formatarIdentificacaoOcorrencia($string) {
        return array_filter(array_map('intval', str_split($string, 2)));
    }

}
