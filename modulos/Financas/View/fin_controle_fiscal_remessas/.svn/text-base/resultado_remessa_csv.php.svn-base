<?php

include("../../../../lib/config.php");
include("../../../../lib/init.php");

require (_MODULEDIR_ . "Financas/Action/FinControleFiscalRemessas.php");

$remessa=new FinControleFiscalRemessas();

$conteudo=$remessa->gerarRemessaCSV();


if ($conteudo) {

    $nome_arquivo = "arquivo_remessa_estoque_" . date("YmdHis") . ".csv";

    Header('Content-Description: File Transfer');
    Header('Content-Type: application/force-download');
    Header("Content-Disposition: attachment; filename=$nome_arquivo");
    echo $conteudo;
}
