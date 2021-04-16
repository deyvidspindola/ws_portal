<?php

namespace module\EscritorRetornoCNABSantander;

use module\EscritorRetornoCNABSantander\EscritorComumCNABSantanderModel;

class EscritorTrailerLoteRetornoCNABSantanderModel extends EscritorComumCNABSantanderModel {

    const LOTE_SERVICO = '0100';
    const QUANTIDADE_REGISTROS_LOTE = '000100';
    const QUANTIDADE_TITULOS_COBRANCA_SIMPLES = '000006';
    const QUANTIDADE_TITULOS_COBRANCA_VINCULADA = '000006';
    const QUANTIDADE_TITULOS_COBRANCA_CAUCIONADA = '000006';
    const QUANTIDADE_TITULOS_COBRANCA_DESCONTADA = '000006';
    const VALOR_TOTAL_TITULOS_COBRANCA_SIMPLES = '00000000000000017';
    const VALOR_TOTAL_TITULOS_COBRANCA_VINCULADA = '00000000000000017';
    const VALOR_TOTAL_TITULOS_COBRANCA_CAUCIONADA = '00000000000000017';
    const VALOR_TOTAL_TITULOS_COBRANCA_DESCONTADA = '00000000000000017';
    const NUMERO_AVISO_LANCAMENTO = '00000000';

    private $codigoBancoCompensacao;
    private $loteServico;
    private $tipoRegistro;
    private $quantidadeRegistrosLote;
    private $quantidadeTitulosCobrancaSimples;
    private $valorTotalTitulosCobrancaSimples;
    private $quantidadeTitulosCobrancaVinculada;
    private $valorTotalTitulosCobrancaVinculada;
    private $quantidadeTitulosCobrancaCaucionada;
    private $valorTotalTitulosCobrancaCaucionada;
    private $quantidadeTitulosCobrancaDescontada;
    private $valorTotalTitulosCobrancaDescontada;
    private $numeroAvisoLancamento;

    public function __construct() {

        $this->codigoBancoCompensacao = self::CODIGO_BANCO_COMPENSACAO;
        $this->loteServico = self::LOTE_SERVICO;
        $this->tipoRegistro = self::TIPO_REGISTRO_TRAILER_LOTE;
        $this->quantidadeRegistrosLote = self::QUANTIDADE_REGISTROS_LOTE;
        $this->quantidadeTitulosCobrancaSimples = self::QUANTIDADE_TITULOS_COBRANCA_SIMPLES;
        $this->quantidadeTitulosCobrancaVinculada = self::QUANTIDADE_TITULOS_COBRANCA_VINCULADA;
        $this->quantidadeTitulosCobrancaDescontada = self::QUANTIDADE_TITULOS_COBRANCA_DESCONTADA;
        $this->quantidadeTitulosCobrancaCaucionada = self::QUANTIDADE_TITULOS_COBRANCA_CAUCIONADA;
        $this->valorTotalTitulosCobrancaSimples = self::VALOR_TOTAL_TITULOS_COBRANCA_SIMPLES;
        $this->valorTotalTitulosCobrancaDescontada = self::VALOR_TOTAL_TITULOS_COBRANCA_DESCONTADA;
        $this->valorTotalTitulosCobrancaVinculada = self::VALOR_TOTAL_TITULOS_COBRANCA_VINCULADA;
        $this->valorTotalTitulosCobrancaCaucionada = self::VALOR_TOTAL_TITULOS_COBRANCA_CAUCIONADA;
        $this->numeroAvisoLancamento = self::NUMERO_AVISO_LANCAMENTO;
    }

    public function getCodigoBancoCompensacao() {
        return $this->codigoBancoCompensacao;
    }

    public function getLoteServico() {
        return $this->loteServico;
    }

    public function getTipoRegistro() {
        return $this->tipoRegistro;
    }

    public function getQuantidadeRegistrosLote() {
        return $this->quantidadeRegistrosLote;
    }

    public function getQuantidadeTitulosCobrancaSimples() {
        return $this->quantidadeTitulosCobrancaSimples;
    }

    public function getQuantidadeTitulosCobrancaVinculada() {
        return $this->quantidadeTitulosCobrancaVinculada;
    }

    public function getValorTotalTitulosCobrancaVinculada() {
        return $this->valorTotalTitulosCobrancaVinculada;
    }

    public function getQuantidadeTitulosCobrancaCaucionada() {
        return $this->quantidadeTitulosCobrancaCaucionada;
    }

    public function getValorTotalTitulosCobrancaCaucionada() {
        return $this->valorTotalTitulosCobrancaCaucionada;
    }

    public function getQuantidadeTitulosCobrancaDescontada() {
        return $this->quantidadeTitulosCobrancaDescontada;
    }

    public function getValorTotalTitulosCobrancaDescontada() {
        return $this->valorTotalTitulosCobrancaDescontada;
    }

    public function getNumeroAvisoLancamento() {
        return $this->numeroAvisoLancamento;
    }

    public function setCodigoBancoCompensacao($codigoBancoCompensacao) {
        $this->codigoBancoCompensacao = $codigoBancoCompensacao;
    }

    public function setLoteServico($loteServico) {
        $this->loteServico = $loteServico;
    }

    public function setTipoRegistro($tipoRegistro) {
        $this->tipoRegistro = $tipoRegistro;
    }

    public function setQuantidadeRegistrosLote($quantidadeRegistrosLote) {
        $this->quantidadeRegistrosLote = $quantidadeRegistrosLote;
    }

    public function setQuantidadeTitulosCobrancaSimples($quantidadeTitulosCobrancaSimples) {
        $this->quantidadeTitulosCobrancaSimples = $quantidadeTitulosCobrancaSimples;
    }

    public function setQuantidadeTitulosCobrancaVinculada($quantidadeTitulosCobrancaVinculada) {
        $this->quantidadeTitulosCobrancaVinculada = $quantidadeTitulosCobrancaVinculada;
    }

    public function setValorTotalTitulosCobrancaVinculada($valorTotalTitulosCobrancaVinculada) {
        $this->valorTotalTitulosCobrancaVinculada = $valorTotalTitulosCobrancaVinculada;
    }

    public function setQuantidadeTitulosCobrancaCaucionada($quantidadeTitulosCobrancaCaucionada) {
        $this->quantidadeTitulosCobrancaCaucionada = $quantidadeTitulosCobrancaCaucionada;
    }

    public function setValorTotalTitulosCobrancaCaucionada($valorTotalTitulosCobrancaCaucionada) {
        $this->valorTotalTitulosCobrancaCaucionada = $valorTotalTitulosCobrancaCaucionada;
    }

    public function setQuantidadeTitulosCobrancaDescontada($quantidadeTitulosCobrancaDescontada) {
        $this->quantidadeTitulosCobrancaDescontada = $quantidadeTitulosCobrancaDescontada;
    }

    public function setValorTotalTitulosCobrancaDescontada($valorTotalTitulosCobrancaDescontada) {
        $this->valorTotalTitulosCobrancaDescontada = $valorTotalTitulosCobrancaDescontada;
    }

    public function setNumeroAvisoLancamento($numeroAvisoLancamento) {
        $this->numeroAvisoLancamento = $numeroAvisoLancamento;
    }

    public function getValorTotalTitulosCobrancaSimples() {
        return $this->valorTotalTitulosCobrancaSimples;
    }

    public function setValorTotalTitulosCobrancaSimples($valorTotalTitulosCobrancaSimples) {
        $this->valorTotalTitulosCobrancaSimples = $valorTotalTitulosCobrancaSimples;
    }

    public function getRegistro() {

        $linha = '';

        $linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
        $linha .= $this->formatNumeric($this->loteServico, 4);
        $linha .= $this->formatNumeric($this->tipoRegistro, 1);
        $linha .= $this->formatAlphanumeric("", 9);
        $linha .= $this->formatNumeric($this->quantidadeRegistrosLote, 6);
        $linha .= $this->formatNumeric($this->quantidadeTitulosCobrancaSimples, 6);
        $linha .= $this->formatNumeric($this->valorTotalTitulosCobrancaSimples, 17);
        $linha .= $this->formatNumeric($this->quantidadeTitulosCobrancaVinculada, 6);
        $linha .= $this->formatNumeric($this->valorTotalTitulosCobrancaVinculada, 17);
        $linha .= $this->formatNumeric($this->quantidadeTitulosCobrancaCaucionada, 6);
        $linha .= $this->formatNumeric($this->valorTotalTitulosCobrancaCaucionada, 17);
        $linha .= $this->formatNumeric($this->quantidadeTitulosCobrancaDescontada, 6);
        $linha .= $this->formatNumeric($this->valorTotalTitulosCobrancaDescontada, 17);
        $linha .= $this->formatNumeric($this->numeroAvisoLancamento, 8);
        $linha .= $this->formatAlphanumeric("", 117);
        $linha .= "\r\n";

        return $linha;
    }

}
