<?php

namespace module\EscritorRetornoCNABSantander;

class EscritorComumCNABSantanderModel {

    const TIPO_INSCRICAO_EMPRESA_CPF = 1;
    const TIPO_INSCRICAO_EMPRESA_CNPJ = 2;
    const TIPO_REGISTRO_HEADER_ARQUIVO = 0;
    const TIPO_REGISTRO_HEADER_LOTE = 1;
    const TIPO_REGISTRO_DETALHE = 3;
    const TIPO_REGISTRO_TRAILER_LOTE = 5;
    const TIPO_REGISTRO_TRAILER_ARQUIVO = 9;
    const LOTE_SERVICO = '0000';
    const TIPO_OPERACAO_RETORNO = 'T';
    const NOME_EMPRESA = 'SASCAR TECNOLOGIA E SEGURANCA';
    const NUMERO_INSCRICAO_EMPRESA = '003112879000151';
    const NOME_BANCO = 'BANCO SANTANDER';
    const CODIGO_BANCO_COMPENSACAO = '033';
    const CODIGO_BENEFICIARIO = '008144958';
    const AGENCIA_BENEFICIARIO = '2102';
    const DIGITO_AGENCIA_BENEFICIARIO = '4';
    const NUMERO_CONTA_CORRENTE = '013000855';
    const DIGITO_VERIFICADOR_CONTA_CORRENTE = '1';

    public function formatNumeric($value, $size, $decimals = null) {

        if (!is_null($decimals)) {
            $value = number_format($value, $decimals, "", "");
        }

        $pattern = '/' . 'RPS' . '/';
        if (preg_match($pattern, $value)) {
            return str_pad($value, $size, " ", STR_PAD_RIGHT);
        }

        return str_pad($value, $size, "0", STR_PAD_LEFT);
    }

    public function formatAlphanumeric($value, $size) {

        $value = preg_replace("/[^a-zA-Z0-9 ]/", "", strtr($value, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
        $value = strtoupper($value);

        return str_pad(substr($value, 0, $size), $size);
    }

    public function formatDate($value) {
        return $this->formatNumeric(date('dmY', strtotime($value)), 8);
    }

}
