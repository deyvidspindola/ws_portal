<?php

namespace module\EscritorRetornoCNABSantander;

use module\EscritorRetornoCNABSantander\EscritorComumCNABSantanderModel;

class EscritorHeaderArquivoRetornoCNABSantanderModel extends EscritorComumCNABSantanderModel {

	const CODIGO_RETORNO = 2;
	const VERSAO_LAYOUT = 40;
        
        
	private $codigoBancoCompensacao;
	private $loteServico;
	private $tipoRegistro;
	private $tipoInscricaoEmpresa;
	private $numeroInscricaoEmpresa;
        private $agenciaBeneficiario;
        private $digitoAgenciaBeneficiario;
        private $codigoBeneficiario;
        private $numeroContaCorrente;
        private $digitoVerificadorConta;
        private $nomeEmpresa;
	private $nomeBanco;
	private $codigoRetorno;
	private $dataGeracaoArquivo;
	private $numeroSequencialArquivo;
	private $numeroVersaoLayoutArquivo;

	public function __construct(){

		$this->codigoBancoCompensacao = self::CODIGO_BANCO_COMPENSACAO;
		$this->loteServico = self::LOTE_SERVICO;
		$this->tipoRegistro = self::TIPO_REGISTRO_HEADER_ARQUIVO;
		$this->tipoInscricaoEmpresa = self::TIPO_INSCRICAO_EMPRESA_CNPJ;
                $this->numeroInscricaoEmpresa = self::NUMERO_INSCRICAO_EMPRESA;
                $this->agenciaBeneficiario = self::AGENCIA_BENEFICIARIO;
                $this->digitoAgenciaBeneficiario = self::DIGITO_AGENCIA_BENEFICIARIO;
                $this->numeroContaCorrente = self::NUMERO_CONTA_CORRENTE;
                $this->digitoVerificadorConta = self::DIGITO_VERIFICADOR_CONTA_CORRENTE;
                $this->codigoBeneficiario = self::CODIGO_BENEFICIARIO;
                $this->nomeEmpresa = self::NOME_EMPRESA;
		$this->nomeBanco = self::NOME_BANCO;
		$this->codigoRetorno = self::CODIGO_RETORNO;
                $this->dataGeracaoArquivo = date('dmY');
		$this->numeroVersaoLayoutArquivo = self::VERSAO_LAYOUT;

	}

	public function setCodigoBancoCompensacao($codigoBancoCompensacao){
		$this->codigoBancoCompensacao = $codigoBancoCompensacao;
	}

	public function getCodigoBancoCompensacao(){
		return $this->codigoBancoCompensacao;
	}

	public function setLoteServico($loteServico){
		$this->loteServico = $loteServico;
	}

	public function getLoteServico(){
		return $this->loteServico;
	}

	public function setTipoRegistro($tipoRegistro){
		$this->tipoRegistro = $tipoRegistro;
	}

	public function getTipoRegistro(){
		return $this->tipoRegistro;
	}

	public function setTipoInscricaoEmpresa($tipoInscricaoEmpresa){
		$this->tipoInscricaoEmpresa = $tipoInscricaoEmpresa;
	}

	public function getTipoInscricaoEmpresa(){
		return $this->tipoInscricaoEmpresa;
	}

	public function setNumeroInscricaoEmpresa($numeroInscricaoEmpresa){
		$this->numeroInscricaoEmpresa = $numeroInscricaoEmpresa;
	}

	public function getNumeroInscricaoEmpresa(){
		return $this->numeroInscricaoEmpresa;
	}

	public function setNomeEmpresa($nomeEmpresa){
		$this->nomeEmpresa = $nomeEmpresa;
	}

	public function getNomeEmpresa(){
		return $this->nomeEmpresa;
	}

	public function setNomeBanco($nomeBanco){
		$this->nomeBanco = $nomeBanco;
	}

	public function getNomeBanco(){
		return $this->nomeBanco;
	}

	public function setCodigoRetorno($codigoRetorno){
		$this->codigoRetorno = $codigoRetorno;
	}

	public function getCodigoRetorno(){
		return $this->codigoRetorno;
	}

	public function setDataGeracaoArquivo($dataGeracaoArquivo){
		$this->dataGeracaoArquivo = $dataGeracaoArquivo;
	}

	public function getDataGeracaoArquivo(){
		return $this->dataGeracaoArquivo;
	}

	public function setNumeroSequencialArquivo($numeroSequencialArquivo){
		$this->numeroSequencialArquivo = $numeroSequencialArquivo;
	}

	public function getNumeroSequencialArquivo(){
		return $this->numeroSequencialArquivo;
	}

	public function setNumeroVersaoLayoutArquivo($numeroVersaoLayoutArquivo){
		$this->numeroVersaoLayoutArquivo = $numeroVersaoLayoutArquivo;
	}

	public function getNumeroVersaoLayoutArquivo(){
		return $this->numeroVersaoLayoutArquivo;
	}
        public function getAgenciaBeneficiario() {
            return $this->agenciaBeneficiario;
        }

        public function getCodigoBeneficiario() {
            return $this->codigoBeneficiario;
        }

        public function getDigitoAgenciaBeneficiario() {
            return $this->digitoAgenciaBeneficiario;
        }

        public function getNumeroContaCorrente() {
            return $this->numeroContaCorrente;
        }

        public function getDigitoVerificadorConta() {
            return $this->digitoVerificadorConta;
        }

        public function setAgenciaBeneficiario($agenciaBeneficiario) {
            $this->agenciaBeneficiario = $agenciaBeneficiario;
        }

        public function setCodigoBeneficiario($codigoBeneficiario) {
            $this->codigoBeneficiario = $codigoBeneficiario;
        }

        public function setDigitoAgenciaBeneficiario($digitoAgenciaBeneficiario) {
            $this->digitoAgenciaBeneficiario = $digitoAgenciaBeneficiario;
        }

        public function setNumeroContaCorrente($numeroContaCorrente) {
            $this->numeroContaCorrente = $numeroContaCorrente;
        }

        public function setDigitoVerificadorConta($digitoVerificadorConta) {
            $this->digitoVerificadorConta = $digitoVerificadorConta;
        }

        
	public function getRegistro(){

		$linha = '';

		$linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
		$linha .= $this->formatNumeric($this->loteServico, 4);
		$linha .= $this->formatNumeric($this->tipoRegistro, 1);
		$linha .= $this->formatAlphanumeric("", 8);
		$linha .= $this->formatNumeric($this->tipoInscricaoEmpresa, 1);
		$linha .= $this->formatNumeric($this->numeroInscricaoEmpresa, 15);
                $linha .= $this->formatNumeric($this->agenciaBeneficiario, 4);
                $linha .= $this->formatNumeric($this->digitoAgenciaBeneficiario, 1);
                $linha .= $this->formatNumeric($this->numeroContaCorrente, 9);
                $linha .= $this->formatNumeric($this->digitoVerificadorConta, 1);
                $linha .= $this->formatAlphanumeric("", 5);
                $linha .= $this->formatNumeric($this->codigoBeneficiario, 9);
                $linha .= $this->formatAlphanumeric("", 11);
		$linha .= $this->formatAlphanumeric($this->nomeEmpresa, 30);
		$linha .= $this->formatAlphanumeric($this->nomeBanco, 30);
		$linha .= $this->formatAlphanumeric("", 10);
		$linha .= $this->formatNumeric($this->codigoRetorno, 1);
		$linha .= $this->formatNumeric($this->dataGeracaoArquivo, 8);
		$linha .= $this->formatAlphanumeric("", 6);
		$linha .= $this->formatNumeric($this->numeroSequencialArquivo, 6);
		$linha .= $this->formatNumeric($this->numeroVersaoLayoutArquivo, 3);
		$linha .= $this->formatAlphanumeric("", 74);
                $linha .= "\r\n";

		return $linha;

	}


}