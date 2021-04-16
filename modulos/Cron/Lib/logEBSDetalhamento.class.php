<?php


/*
Classe responsável por salvar o detalhamento de cada log
*/

class logEBSDetalhamento
{

    const NOTA_ENTRADA = 0;
    const NOTA_SAIDA = 1;
    const CANCELAMENTO_NOTA_ENTRADA = 2;
    const CANCELAMENTO_NOTA_SAIDA = 3;

    const SUCESSO = 1;
    const ERRO = 0;

    public $tipoNota;
    public $statusProcessamento;
    public $numeroNotaFiscal;
    public $seriaNotaFiscal;
    public $detalhamento;
}
?>