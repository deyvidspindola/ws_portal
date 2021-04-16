<?php

include("../../../../lib/config.php");
include("../../../../lib/init.php");

require (_MODULEDIR_ . "Financas/Action/FinControleFiscalRemessas.php");

$contrato=new FinControleFiscalRemessas();

$conteudo=$contrato->gerarContratoControleCSV();


if ($conteudo) {

    $nome_arquivo = "arquivo_equipamento_movel_" . date("YmdHis") . ".csv";

    Header('Content-Description: File Transfer');
    Header('Content-Type: application/force-download');
    Header("Content-Disposition: attachment; filename=$nome_arquivo");
    echo $conteudo;
}
