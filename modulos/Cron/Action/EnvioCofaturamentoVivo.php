<?php

/**
 * Classe responsável pelo tratamento dos arquivos de Remessa Vivo
 *
 * @author 	André L. Zilz <andre.zilz@meta.com.br>
 * @package Cron
 * @since 23/12/2013
 */
class EnvioCofaturamentoVivo {

    /**
     * Referencia da DAO
     */
    private $dao;

    /*
     * Mensagens do processo
     */
    public $msg;

    /**
     * Construtor da Classe
     */
    public function __construct(EnvioCofaturamentoVivoDAO $dao) {

        $this->dao = $dao;
    }


    /**
     * Executa os métodos inerentes à geração de arquivo de remessa VIVO
     */
    public function iniciarProcesso() {

        $titulos = $this->dao->pesquisarTitulosVencer();

        if (count($titulos) > 0) {

            $dadosArquivo = $this->gerarArquivoRemessa($titulos);
            $this->dao->inserirHistorico($dadosArquivo);
            $this->msg = "Arquivo Remessa de Parceiros concluído: " . $dadosArquivo[0]->arquivo;

        } else {
             $this->msg = "Não há dados disponíveis para gerar o Arquivo Remessa de Parceiros";
        }
    }


    /**
     * Cria o arquivo de Remessa VIVO
     *
     * @param stdClass $dados
     * @return String
     * @throws Exception
     */
    private function gerarArquivoRemessa($dados) {

        require_once _SITEDIR_ . 'lib/Components/CsvWriter.php';
        $arquivo = "ESERVICOS.011.".date('dmy.His').".IE";
		$diretorio = '/vivo/saida/';
        $identificadorRegistro = 1;
        $totalBruto = 0;
        $obrigacoes = $this->dao->buscarDadosObrigacao();

        try {
            /*
             * Se o diretório não existir, tenta criá-lo
             */
            if (!is_dir($diretorio)) {

                if (!mkdir($diretorio, 0777, true)) {
                    throw new Exception("Diretório " . $diretorio . " não existe e não foi possível criá-lo.");
                }

            }

            /*
             * Se o diretório tem permissão de escrita, grava o arquivo
             */
            if (is_writable($diretorio)) {

                $fp = fopen($diretorio . $arquivo, "a+");

                if ($fp) {
                    $header = "0|" .$arquivo. "|011|" . date('YmdHis') . "||";
                    fwrite($fp, $header . "\n");

                    foreach ($dados as $dado) {

                        //Busca a descricao e código VIVO para a obrigacao
                        if (array_key_exists($dado->obroid, $obrigacoes)) {
                            $codigoPlano = $obrigacoes[$dado->obroid][0];
                            $descPlano = $obrigacoes[$dado->obroid][1];
                        } else {
                            $codigoPlano = '';
                            $descPlano = '';
                        }

                        $lote = $this->aplicarMascara("#######", strval($identificadorRegistro));

                        $detail = "1|" . $dado->subscription . "|" . $codigoPlano . "|";
                        $detail .=  $descPlano . "|" . $dado->data_vigencia . "||";
                        $detail .= $dado->nr_parcela . "|" . $dado->total_parcelas ."|";
                        $detail .=  $dado->data_referencia . "|";
                        $detail .= number_format($dado->nfivl_liquido,2,',','') ."|" . number_format($dado->nfivl_servico,2,',','') . "||";
                        $detail .= $lote . "|||||";

                        $dado->lote = $lote;
                        $dado->arquivo = $arquivo;

                        $identificadorRegistro++;
                        $totalBruto += $dado->nfivl_servico;

                        fwrite($fp, $detail . "\n");
                    }

                    $trailler = "9|" . count($dados) . "|" . number_format($totalBruto,2,',','') . "||";
                    fwrite($fp, $trailler . "\n");

                    fclose($fp);

                    return $dados;

                } else {
                     throw new Exception("Não foi possível criar o arquivo:" . $arquivo);
                }
            } else {
                  throw new Exception("Diretório " . $diretorio . " não tem permissão para escrita.");
            }

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }

    }

    /**
     * Aplica máscara em um determinado dado
     * @param string $mascara
     * @param int $codigo
     * @return string
     */
    private function aplicarMascara($mascara,$codigo) {

        $codigo = str_replace(" ","",$codigo);


        for ($i=strlen($codigo);$i>0;$i--) {

            $mascara[strrpos($mascara,"#")] = $codigo[$i-1];
        }

		$mascara = str_replace("#", "0", $mascara);

        return $mascara;
    }


}

?>
