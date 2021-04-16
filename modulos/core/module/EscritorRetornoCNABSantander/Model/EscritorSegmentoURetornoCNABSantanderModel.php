<?php

namespace module\EscritorRetornoCNABSantander;

use module\EscritorRetornoCNABSantander\EscritorComumCNABSantanderModel;

class EscritorSegmentoURetornoCNABSantanderModel extends EscritorComumCNABSantanderModel {

    const NUMERO_LOTE_RETORNO = '1083';
    const CODIGO_SEGMENTO_REGISTRO_DETALHE = 'U';
    const CODIGO_BANCO_CORRESPONDENTE = '000';
    const CODIGO_OCORRENCIA_PAGADOR = '0000';

    private $codigoBancoCompensacao;
    private $loteServico;
    private $tipoRegistro;
    private $numeroSequencialRegistroLote;
    private $codigoSegmentoRegistroDetalhe;
    private $codigoMovimento;
    private $valorJurosMultaEncargos;
    private $valorDescontoConcedido;
    private $valorAbatimentoConcedido;
    private $valorIOF;
    private $valorPagoPagador;
    private $valorLiquidoCreditado;
    private $valorOutrasDespesas;
    private $valorOutrosCreditos;
    private $dataOcorrencia;
    private $dataEfetivacaoCredito;
    private $codigoOcorrenciaPagador;
    private $dataOcorrenciaPagador;
    private $valorOcorrenciaPagador;
    private $complementoOcorrenciaPagador;
    private $codigoBancoCompens;

    public function __construct() {

        $this->codigoBancoCompensacao = self::CODIGO_BANCO_COMPENSACAO;
        $this->loteServico = self::NUMERO_LOTE_RETORNO;
        $this->tipoRegistro = self::TIPO_REGISTRO_DETALHE;
        $this->codigoSegmentoRegistroDetalhe = self::CODIGO_SEGMENTO_REGISTRO_DETALHE;
        $this->codigoOcorrenciaPagador = self::CODIGO_OCORRENCIA_PAGADOR;
        $this->dataOcorrencia = date('dmY');
        $this->dataOcorrenciaPagador = date('dmY');
        $this->dataEfetivacaoCredito = date('dmY');
        $this->codigoBancoCompens = self::CODIGO_BANCO_CORRESPONDENTE;
    }

    public function getCodigoBancoCompensacao() {
        return $this->codigoBancoCompensacao;
    }

    public function setCodigoBancoCompensacao($codigoBancoCompensacao) {
        $this->codigoBancoCompensacao = $codigoBancoCompensacao;
    }

    public function getLoteServico() {
        return $this->loteServico;
    }

    public function setLoteServico($loteServico) {
        $this->loteServico = $loteServico;
    }

    public function getTipoRegistro() {
        return (int) $this->tipoRegistro;
    }

    public function setTipoRegistro($tipoRegistro) {
        $this->tipoRegistro = $tipoRegistro;
    }

    public function getNumeroSequencialRegistroLote() {
        return $this->numeroSequencialRegistroLote;
    }

    public function setNumeroSequencialRegistroLote($numeroSequencialRegistroLote) {
        $this->numeroSequencialRegistroLote = $numeroSequencialRegistroLote;
    }

    public function getCodigoSegmentoRegistroDetalhe() {
        return $this->codigoSegmentoRegistroDetalhe;
    }

    public function setCodigoSegmentoRegistroDetalhe($codigoSegmentoRegistroDetalhe) {
        $this->codigoSegmentoRegistroDetalhe;
    }

    public function getCodigoMovimento() {
        return $this->codigoMovimento;
    }

    public function setCodigoMovimento($codigoMovimento) {
        $this->codigoMovimento = $codigoMovimento;
    }

    public function getValorJurosMultaEncargos() {
        return $this->valorJurosMultaEncargos;
    }

    public function setValorJurosMultaEncargos($valorJurosMultaEncargos) {
        $this->valorJurosMultaEncargos = (float) $valorJurosMultaEncargos;
    }

    public function getValorDescontoConcedido() {
        return $this->valorDescontoConcedido;
    }

    public function setValorDescontoConcedido($valorDescontoConcedido) {
        $this->valorDescontoConcedido = (float) $valorDescontoConcedido;
    }

    public function getValorAbatimentoConcedido() {
        return $this->valorAbatimentoConcedido;
    }

    public function setValorAbatimentoConcedido($valorAbatimentoConcedido) {
        $this->valorAbatimentoConcedido = $valorAbatimentoConcedido;
    }

    public function getValorIOF() {
        return $this->valorIOF;
    }

    public function setValorIOF($valorIOF) {
        $this->valorIOF = (float) $valorIOF;
    }

    public function getValorPagoPagador() {
        return $this->valorPagoPagador;
    }

    public function setValorPagoPagador($valorPagoPagador) {
        $this->valorPagoPagador = (float) $valorPagoPagador;
    }

    public function getValorLiquidoCreditado() {
        return $this->valorLiquidoCreditado;
    }

    public function setValorLiquidoCreditado($valorLiquidoCreditado) {
        $this->valorLiquidoCreditado = (float) $valorLiquidoCreditado;
    }

    public function getValorOutrasDespesas() {
        return $this->valorOutrasDespesas;
    }

    public function setValorOutrasDespesas($valorOutrasDespesas) {
        $this->valorOutrasDespesas = (float) $valorOutrasDespesas;
    }

    public function getValorOutrosCreditos() {
        return $this->valorOutrosCreditos;
    }

    public function setValorOutrosCreditos($valorOutrosCreditos) {
        $this->valorOutrosCreditos = (float) $valorOutrosCreditos;
    }

    public function getDataOcorrencia() {
        return $this->dataOcorrencia;
    }

    public function setDataOcorrencia($dataOcorrencia) {
        $this->dataOcorrencia = $dataOcorrencia;
    }

    public function getDataEfetivacaoCredito() {
        return $this->dataEfetivacaoCredito;
    }

    public function setDataEfetivacaoCredito($dataEfetivacaoCredito) {
        $this->dataEfetivacaoCredito = $dataEfetivacaoCredito;
    }

    public function getCodigoOcorrenciaPagador() {
        return $this->codigoOcorrenciaPagador;
    }

    public function setCodigoOcorrenciaPagador($codigoOcorrenciaPagador) {
        $this->codigoOcorrenciaPagador = (int) $codigoOcorrenciaPagador;
    }

    public function getDataOcorrenciaPagador() {
        return $this->dataOcorrenciaPagador;
    }

    public function setDataOcorrenciaPagador($dataOcorrenciaPagador) {
        $this->dataOcorrenciaPagador = $dataOcorrenciaPagador;
    }

    public function getValorOcorrenciaPagador() {
        return $this->valorOcorrenciaPagador;
    }

    public function setValorOcorrenciaPagador($valorOcorrenciaPagador) {
        $this->valorOcorrenciaPagador = (float) $valorOcorrenciaPagador;
    }

    public function getComplementoOcorrenciaPagador() {
        return $this->complementoOcorrenciaPagador;
    }

    public function setComplementoOcorrenciaPagador($complementoOcorrenciaPagador) {
        $this->complementoOcorrenciaPagador = $complementoOcorrenciaPagador;
    }

    public function getCodigoBancoCompens() {
        return $this->codigoBancoCompens;
    }

    public function setCodigoBancoCompens($codigoBancoCompens) {
        $this->codigoBancoCompens = $codigoBancoCompens;
    }

    public function getRegistro() {

        $linha = '';
        $linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
        $linha .= $this->formatNumeric($this->loteServico, 4);
        $linha .= $this->formatNumeric($this->tipoRegistro, 1);
        $linha .= $this->formatNumeric($this->numeroSequencialRegistroLote, 5);
        $linha .= $this->formatAlphanumeric($this->codigoSegmentoRegistroDetalhe, 1);
        $linha .= $this->formatAlphanumeric("", 1);
        $linha .= $this->formatNumeric($this->codigoMovimento, 2);
        $linha .= $this->formatNumeric($this->valorJurosMultaEncargos, 15, 2);
        $linha .= $this->formatNumeric($this->valorDescontoConcedido, 15, 2);
        $linha .= $this->formatNumeric($this->valorAbatimentoConcedido, 15, 2);
        $linha .= $this->formatNumeric($this->valorIOF, 15, 2);
        $linha .= $this->formatNumeric($this->valorPagoPagador, 15, 2);
        $linha .= $this->formatNumeric($this->valorLiquidoCreditado, 15, 2);
        $linha .= $this->formatNumeric($this->valorOutrasDespesas, 15, 2);
        $linha .= $this->formatNumeric($this->valorOutrosCreditos, 15, 2);
        $linha .= $this->formatNumeric($this->dataOcorrencia, 8);
        $linha .= $this->formatNumeric($this->dataEfetivacaoCredito, 8);
        $linha .= $this->formatNumeric($this->codigoOcorrenciaPagador, 4);
        $linha .= $this->formatNumeric($this->dataOcorrenciaPagador, 8);
        $linha .= $this->formatNumeric($this->valorOcorrenciaPagador, 15, 2);
        $linha .= $this->formatAlphanumeric($this->complementoOcorrenciaPagador, 30);
        $linha .= $this->formatAlphanumeric($this->codigoBancoCompens, 3);
        $linha .= $this->formatAlphanumeric("", 27);
        $linha .= "\r\n";

        return $linha;
    }

}
