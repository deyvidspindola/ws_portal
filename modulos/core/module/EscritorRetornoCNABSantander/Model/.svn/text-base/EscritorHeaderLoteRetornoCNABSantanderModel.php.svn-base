<?php

namespace module\EscritorRetornoCNABSantander;

use module\EscritorRetornoCNABSantander\EscritorComumCNABSantanderModel;

class EscritorHeaderLoteRetornoCNABSantanderModel extends EscritorComumCNABSantanderModel {

    const VERSAO_LAYOUT_LOTE = '040';
    const TIPO_SERVICO = '01';
    const NUMERO_LOTE_RETORNO = '0108';

    private $codigoBancoCompensacao;
    private $numeroLoteRetorno;
    private $tipoRegistro;
    private $tipoOperacao;
    private $tipoServico;
    private $numeroVersaoLayoutLote;
    private $tipoInscricaoEmpresa;
    private $numeroInscricaoEmpresa;
    private $codigoBeneficiario;
    private $agenciaBeneficiario;
    private $digitoAgenciaBeneficiario;
    private $numeroContaBeneficiario;
    private $digitoVerificadorConta;
    private $nomeEmpresa;
    private $numeroRetorno;
    private $dataGravacaoRetorno;

    public function __construct() {

        $this->codigoBancoCompensacao = self::CODIGO_BANCO_COMPENSACAO;
        $this->numeroLoteRetorno = self::NUMERO_LOTE_RETORNO;
        $this->tipoRegistro = self::TIPO_REGISTRO_HEADER_LOTE;
        $this->tipoOperacao = self::TIPO_OPERACAO_RETORNO;
        $this->tipoServico = self::TIPO_SERVICO;
        $this->numeroVersaoLayoutLote = self::VERSAO_LAYOUT_LOTE;
        $this->tipoInscricaoEmpresa = self::TIPO_INSCRICAO_EMPRESA_CNPJ;
        $this->numeroInscricaoEmpresa = self::NUMERO_INSCRICAO_EMPRESA;
        $this->codigoBeneficiario = self::CODIGO_BENEFICIARIO;
        $this->agenciaBeneficiario = self::AGENCIA_BENEFICIARIO;
        $this->digitoAgenciaBeneficiario = self::DIGITO_AGENCIA_BENEFICIARIO;
        $this->numeroContaCorrente = self::NUMERO_CONTA_CORRENTE;
        $this->digitoVerificadorConta = self::DIGITO_VERIFICADOR_CONTA_CORRENTE;
        $this->dataGravacaoRetorno = date('dmY');
        $this->nomeEmpresa = self::NOME_EMPRESA;
    }

    public function setCodigoBancoCompensacao($codigoBancoCompensacao) {
        $this->codigoBancoCompensacao = $codigoBancoCompensacao;
    }

    public function getCodigoBancoCompensacao() {
        return $this->codigoBancoCompensacao;
    }

    public function setNumeroLoteRetorno($numeroLoteRetorno) {
        $this->numeroLoteRetorno = $numeroLoteRetorno;
    }

    public function getNumeroLoteRetorno() {
        return $this->numeroLoteRetorno;
    }

    public function setTipoRegistro($tipoRegistro) {
        $this->tipoRegistro = $tipoRegistro;
    }

    public function getTipoRegistro() {
        return $this->tipoRegistro;
    }

    public function setTipoOperacao($tipoOperacao) {
        $this->tipoOperacao = $tipoOperacao;
    }

    public function getTipoOperacao() {
        return $this->tipoOperacao;
    }

    public function setTipoServico($tipoServico) {
        $this->tipoServico = $tipoServico;
    }

    public function getTipoServico() {
        return $this->tipoServico;
    }

    public function setNumeroVersaoLayoutLote($numeroVersaoLayoutLote) {
        $this->numeroVersaoLayoutLote = $numeroVersaoLayoutLote;
    }

    public function getNumeroVersaoLayoutLote() {
        return $this->numeroVersaoLayoutLote;
    }

    public function setTipoInscricaoEmpresa($tipoInscricaoEmpresa) {
        $this->tipoInscricaoEmpresa = $tipoInscricaoEmpresa;
    }

    public function getTipoInscricaoEmpresa() {
        return $this->tipoInscricaoEmpresa;
    }

    public function setNumeroInscricaoEmpresa($numeroInscricaoEmpresa) {
        $this->numeroInscricaoEmpresa = $numeroInscricaoEmpresa;
    }

    public function getNumeroInscricaoEmpresa() {
        return $this->numeroInscricaoEmpresa;
    }

    public function setNomeBeneficiario($nomeBeneficiario) {
        $this->nomeBeneficiario = $nomeBeneficiario;
    }

    public function getNomeBeneficiario() {
        return $this->nomeBeneficiario;
    }

    public function setNumeroRetorno($numeroRetorno) {
        $this->numeroRetorno = $numeroRetorno;
    }

    public function getNumeroRetorno() {
        return $this->numeroRetorno;
    }

    public function setDataGravacaoRetorno($dataGravacaoRetorno) {
        $this->dataGravacaoRetorno = $dataGravacaoRetorno;
    }

    public function getDataGravacaoRetorno() {
        return $this->dataGravacaoRetorno;
    }

    public function getCodigoBeneficiario() {
        return $this->codigoBeneficiario;
    }

    public function getAgenciaBeneficiario() {
        return $this->agenciaBeneficiario;
    }

    public function getDigitoAgenciaBeneficiario() {
        return $this->digitoAgenciaBeneficiario;
    }

    public function getNumeroContaBeneficiario() {
        return $this->numeroContaBeneficiario;
    }

    public function getDigitoVerificadorConta() {
        return $this->digitoVerificadorConta;
    }

    public function getNomeEmpresa() {
        return $this->nomeEmpresa;
    }

    public function setCodigoBeneficiario($codigoBeneficiario) {
        $this->codigoBeneficiario = $codigoBeneficiario;
    }

    public function setAgenciaBeneficiario($agenciaBeneficiario) {
        $this->agenciaBeneficiario = $agenciaBeneficiario;
    }

    public function setDigitoAgenciaBeneficiario($digitoAgenciaBeneficiario) {
        $this->digitoAgenciaBeneficiario = $digitoAgenciaBeneficiario;
    }

    public function setNumeroContaBeneficiario($numeroContaBeneficiario) {
        $this->numeroContaBeneficiario = $numeroContaBeneficiario;
    }

    public function setDigitoVerificadorConta($digitoVerificadorConta) {
        $this->digitoVerificadorConta = $digitoVerificadorConta;
    }

    public function setNomeEmpresa($nomeEmpresa) {
        $this->nomeEmpresa = $nomeEmpresa;
    }

    public function getRegistro() {

        $linha = '';

        $linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
        $linha .= $this->formatNumeric($this->numeroLoteRemessa, 4);
        $linha .= $this->formatNumeric($this->tipoRegistro, 1);
        $linha .= $this->formatAlphanumeric($this->tipoOperacao, 1);
        $linha .= $this->formatNumeric($this->tipoServico, 2);
        $linha .= $this->formatAlphanumeric("", 2);
        $linha .= $this->formatNumeric($this->numeroVersaoLayoutLote, 3);
        $linha .= $this->formatAlphanumeric("", 1);
        $linha .= $this->formatNumeric($this->tipoInscricaoEmpresa, 1);
        $linha .= $this->formatNumeric($this->numeroInscricaoEmpresa, 15);
        $linha .= $this->formatNumeric($this->codigoBeneficiario, 9);
        $linha .= $this->formatAlphanumeric("", 11);
        $linha .= $this->formatNumeric($this->agenciaBeneficiario, 4);
        $linha .= $this->formatNumeric($this->digitoAgenciaBeneficiario, 1);
        $linha .= $this->formatNumeric($this->numeroContaCorrente, 9);
        $linha .= $this->formatNumeric($this->digitoVerificadorConta, 1);
        $linha .= $this->formatAlphanumeric("", 5);
        $linha .= $this->formatAlphanumeric($this->nomeEmpresa, 30);
        $linha .= $this->formatAlphanumeric("", 80);
        $linha .= $this->formatNumeric($this->numeroRetorno, 8);
        $linha .= $this->formatNumeric($this->dataGravacaoRetorno, 8);
        $linha .= $this->formatAlphanumeric("", 41);
        $linha .= "\r\n";

        return $linha;
    }

}
