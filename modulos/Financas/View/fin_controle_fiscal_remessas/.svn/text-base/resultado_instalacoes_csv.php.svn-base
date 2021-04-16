<?php

/**
  $cnpj = "11222333000199";
  $cpf = "00100200300";
  $cep = "08665110";
  $data = "10102010";

  echo mask($cnpj,'##.###.###/####-##');
  echo mask($cpf,'###.###.###-##');
  echo mask($cep,'#####-###');
  echo mask($data,'##/##/####');
 */
function mask($val, $mask) {
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {
        if ($mask[$i] == '#') {
            if (isset($val[$k]))
                $maskared .= $val[$k++];
        }else {
            if (isset($mask[$i]))
                $maskared .= $mask[$i];
        }
    }
    return $maskared;
}

include("../../../../lib/config.php");

require_once "../../DAO/FinControleFiscalInstalacoesDAO.class.php";

global $conn;

$dao = new FinControleFiscalInstalacoesDAO($conn);

$dataInicio = $_POST['pesquisa_data_inicio'];
$dao->setDataInicio($dataInicio);

$dataFim = $_POST['pesquisa_data_fim'];
$dao->setDataFim($dataFim);

$contrato = $_POST['pesquisa_contrato'];
$dao->setContrato($contrato);

$serie = $_POST['pesquisa_n_serie'];
$dao->setSerie($serie);

$tipoRelatorio = $_POST['pesquisa_tipo_relatorio'];
$dao->setTipoRelatorio($tipoRelatorio);

$cliente = $_POST['pesquisa_id_cliente'];
$dao->setCliente($cliente);

$representante = $_POST['representante'];
$dao->setRepresentante($representante);


$possuiNFRetornoSimbolico = $_POST['pesquisa_possui_nf_retorno_simbolico'];
$dao->setPossuiNFRetornoSimbolico($possuiNFRetornoSimbolico);

$possuiNFRemessaSimbolico = $_POST['pesquisa_possui_nf_remessa_simbolico'];
$dao->setPossuiNFRemessaSimbolico($possuiNFRemessaSimbolico);

$nfRemessaSimbolico = $_POST['pesquisa_nf_remessa_simbolico'];
$dao->setNfRemessaSimbolico($nfRemessaSimbolico);

$nfRetornoSimbolico = $_POST['pesquisa_nf_retorno_simbolico'];
$dao->setNfRetornoSimbolico($nfRetornoSimbolico);

$dados = Array();

$campos = '';

if ($aba === 'envio') {

    $tipo = "Enviado";
} else {

    $tipo = "Retorno";
}

if ($tipoRelatorio === 'serial') {

    $campos = "Contrato;N° Série;";

    $dados = $dao->consultaTipoSerial();
} else {

    $dados = $dao->consultaTipoProduto();
}

$cabecalho = "$tipo;" . $campos . "Cód. Prod.;Descr. Produto;NCM;Qtde;Valor;Cliente;CPF/CNPJ;UF;NF Remessa;N° Pedido";

$data = $cabecalho . "\r";

$csv = '';

while ($row = pg_fetch_object($dados[0])) {

    $csv.=$row->data_envio . ";";

    if ($tipoRelatorio === 'serial') {

        $csv.=$row->contrato . ";";

        $csv.=$row->serie . ";";
    }

    $csv.=$row->codigo_produto . ";";
    $csv.=$row->descricao_produto . ";";
    $csv.=$row->codigo_ncm . ";";
    $csv.=$row->quantidade . ";";

    $precoUnitario = '';
    if ($row->preco_unitario_1) {
        $precoUnitario = $row->preco_unitario_1;
    } else if ($row->preco_unitario_2) {
        $precoUnitario = $row->preco_unitario_2;
    } else {
        $precoUnitario = $row->preco_unitario_3;
    }

    $preco = "R$" . number_format($precoUnitario, 2, ",", ".");

    $csv.=$preco . ";";
    $csv.=$row->nome_cliente . ";";


    if (strlen($row->cliente_cnpj) == 11) {
        $documento = mask($row->cliente_cnpj, '###.###.###-##'); // CPF
    } else {
        $documento = mask($row->cliente_cnpj, '##.###.###/####-##'); // CNPJ
    }

    $csv.=$documento . ";";
    $csv.=$row->cliente_uf . ";";
    $csv.=$row->nf_remessa . ";";
    $csv.=$row->numero_pedido . "\n\r";
}

$arquivo = $data . $csv;

if ($arquivo) {

    $nome_arquivo = "arquivo_remessa_estoque_" . date("YmdHis") . ".csv";

    Header('Content-Description: File Transfer');
    Header('Content-Type: application/force-download');
    Header("Content-Disposition: attachment; filename=$nome_arquivo.csv");
    echo $arquivo;
}
