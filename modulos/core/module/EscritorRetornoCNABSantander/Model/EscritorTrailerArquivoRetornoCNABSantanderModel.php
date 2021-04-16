<?php

namespace module\EscritorRetornoCNABSantander;

use module\EscritorRetornoCNABSantander\EscritorComumCNABSantanderModel;

class EscritorTrailerArquivoRetornoCNABSantanderModel extends EscritorComumCNABSantanderModel {

    const NUMERO_LOTE_REMESSA = '0004';
    const QUANTIDADE_LOTE_ARQUIVO = '000006';
    const QUANTIDADE_REGISTROS_ARQUIVO = '000006';
    
    private $codigoBancoCompensacao;
    private $numeroLoteRemessa;
    private $tipoRegistro;
    private $quantidadeLoteArquivo;
    private $quantidadeRegistrosArquivo;
   

    public function __construct() {

        $this->codigoBancoCompensacao = self::CODIGO_BANCO_COMPENSACAO;
        $this->tipoRegistro = self::TIPO_REGISTRO_TRAILER_ARQUIVO;
    }

    public function getCodigoBancoCompensacao() {
        return $this->codigoBancoCompensacao;
    }

    public function getNumeroLoteRemessa() {
        return $this->numeroLoteRemessa;
    }

    public function getTipoRegistro() {
        return $this->tipoRegistro;
    }

    public function getQuantidadeLoteArquivo() {
        return $this->quantidadeLoteArquivo;
    }

    public function getQuantidadeRegistrosArquivo() {
        return $this->quantidadeRegistrosArquivo;
    }

    public function setCodigoBancoCompensacao($codigoBancoCompensacao) {
        $this->codigoBancoCompensacao = $codigoBancoCompensacao;
    }

    public function setNumeroLoteRemessa($numeroLoteRemessa) {
        $this->numeroLoteRemessa = $numeroLoteRemessa;
    }

    public function setTipoRegistro($tipoRegistro) {
        $this->tipoRegistro = $tipoRegistro;
    }

    public function setQuantidadeLoteArquivo($quantidadeLoteArquivo) {
        $this->quantidadeLoteArquivo = $quantidadeLoteArquivo;
    }

    public function setQuantidadeRegistrosArquivo($quantidadeRegistrosArquivo) {
        $this->quantidadeRegistrosArquivo = $quantidadeRegistrosArquivo;
    }

        public function getRegistro() {

        $linha = '';

        $linha .= $this->formatNumeric($this->codigoBancoCompensacao, 3);
        $linha .= $this->formatNumeric($this->numeroLoteRemessa, 4);
        $linha .= $this->formatNumeric($this->tipoRegistro, 1);
        $linha .= $this->formatAlphanumeric("", 9);
        $linha .= $this->formatNumeric($this->quantidadeLoteArquivo, 6);
        $linha .= $this->formatNumeric($this->quantidadeRegistrosArquivo, 6);
        $linha .= $this->formatAlphanumeric("", 211);
        $linha .= "\r\n";

        return $linha;
    }

}
