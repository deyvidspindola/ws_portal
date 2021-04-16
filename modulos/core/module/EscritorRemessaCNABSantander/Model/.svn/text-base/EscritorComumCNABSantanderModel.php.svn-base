<?php

namespace module\EscritorRemessaCNABSantander;

class EscritorComumCNABSantanderModel {

	const TIPO_INSCRICAO_EMPRESA_CPF = 1;
	const TIPO_INSCRICAO_EMPRESA_CNPJ = 2;

	const TIPO_REGISTRO_HEADER_ARQUIVO = 0;
	const TIPO_REGISTRO_HEADER_LOTE = 1;
	const TIPO_REGISTRO_DETALHE = 3;
	const TIPO_REGISTRO_TRAILER_LOTE = 5;
	const TIPO_REGISTRO_TRAILER_ARQUIVO = 9;

	const TIPO_OPERACAO_REMESSA = 'R';

	public function formatNumeric($value, $size, $decimals = null){

		if(!is_null($decimals)){
			$value = number_format($value, $decimals, "", "");
		}

		return str_pad($value, $size, "0", STR_PAD_LEFT);

	}

	public function formatAlphanumeric($value, $size){

		$value = preg_replace("/[^a-zA-Z0-9 ]/", "", strtr($value, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
		$value = strtoupper($value);

		return str_pad(substr($value, 0, $size), $size);
	}

	public function formatDate($value){
		return $this->formatNumeric(date('dmY', strtotime($value)), 8);
	}

}