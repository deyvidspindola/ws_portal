<?php
require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';

/**
 * Classe responsável pelo tratamento dos arquivos de Log da URA
 *
 * @author 	André L. Zilz <andre.zilz@meta.com.br>
 * @package Cron
 * @since 28/11/2013
 */
class ManutencaoLogUra {

    private $diretorio;
    private $diretorioBackup;
    public $msg;
    private $startLog;

    /**
     * Construtor da Classe
     */
    public function __construct() {
        $this->diretorio        =  '/var/www/logs_ura/';
        $this->diretorioBackup  =  '/var/www/logs_ura/backup/';
        $this->msg = '';
        $this->startLog = true;
    }

    /**
     * Inicia o processo de manutenção dos arquivos de log
     */
	public function iniciarProcesso() {

        $listaArquivos = array();

        try {
            //Busca arquivos TXT
            $listaArquivos = $this->buscarArquivosElegiveis('txt');

            if(!empty($listaArquivos)) {

                /*
                * Compacta arquivos de log
                */
               foreach($listaArquivos as $campanha => $linha) {

                    foreach($linha as $processo => $arquivos) {

                        $retorno = $this->compactarArquivos($arquivos, $campanha, $processo);

                        if($retorno){

                            //Deleta arquivos que foram compactados
                            foreach($arquivos as $arq) {
                                unlink($this->diretorio . $arq);
                            }
                            //Grava arquivo de Log dos arquivos que foram eliminados e compactados
                            $this->gravaArquivoLog($arquivos, 'compactar');

                             echo "=============== COMPACTADOS =============== <br>";
                             print_r($arquivos);

                        }
                        else{
                          $compactados[] = "Falha na compactação";
                          //Grava arquivo de Log dos arquivos que foram eliminados e compactados
                          $this->gravaArquivoLog($arquivos, 'compactar', true);

                          echo "=============== FALHA AO COMPACTAR =============== <br>";
                          print_r($arquivos);
                        }
                    }
                }
            }

            //Busca arquivos ZIP
            $listaArqZip = $this->buscarArquivosElegiveis('zip');
            $excluidos = array();

            if(!empty($listaArqZip)) {

                /*
                 * Elimina arquivos Zipados inferiores há 90 dias
                 */
                 foreach($listaArqZip as $arquivo) {

                    /*
                     * Verifica a diferença entre a data de modificação do arquivo e a data atual
                     * formato do input DD/MM/AAAA
                     */
                    $dataArq = date("Y-m-d", filemtime($arquivo));
                    $timestamp1 = strtotime(date("Y-m-d"));
                    $timestamp2 = strtotime($dataArq);
                    $dataDif =  ($timestamp1 - $timestamp2);
                    $dias = ($dataDif / 86400);

                    if($dias >= 90) {
                        unlink($arquivo);
                        $nomeArquivo = str_replace($this->diretorioBackup, '', $arquivo);
                        $excluidos[] = $nomeArquivo;
                    }
                }

                if(!empty($excluidos)) {
                    $this->gravaArquivoLog($excluidos, 'excluir');
                    echo "=============== ZIP DELETADOS =============== <br>";
                    print_r($excluidos);
                }

            }
        } catch (Exception $e) {
            $this->msg =  $e->getMessage();
        }
    }

    /**
     * Compacta os arquivos passados como parametro
     *
     * @param array $arquivos
     * @param string $campanha
     * @param string $processo
     * @return boolean
     */
    private function compactarArquivos($arquivos, $campanha, $processo) {

        $this->validarDiretorio('backup');

        $zip = new ZipArchive();

        $zipName = $campanha . "_" . $processo . "_" . date("Y_m") . ".zip";
        $zipPath = $this->diretorioBackup . $zipName;

        //Verifica se é possível gravar arquivo ZIP
        if (!extension_loaded('zip')) {
            return false;
        }

        //Se o zip existir deleta
        if(file_exists($zipPath)) {
            unlink($zipPath);
        }

        //Cria o arquivo ZIP
        if (!$zip->open($zipPath, ZipArchive::CREATE)) {
            return false;
        }

        /*
         * Adiciona os arquivos no ZIP
         */
        foreach($arquivos as $arquivo) {

            $arquivoPath  = $this->diretorio . $arquivo;

            /*
             * Paramêtros de input:
             * 1 - Hierarquia de pastas que deseja que fiquem os arquivos no ZIP
             * 2- Conteúdo do Arquivo
             */
            $zip->addFromString('logs_ura/'. $arquivo, file_get_contents($arquivoPath));

        }

        //Salva e fecha o arquivo ZIP
        return $zip->close();

    }

    /**
     * Busca pelos arquivos elegíveis para o uso
     * @param string $extensao
     * @return array
     */
    private function buscarArquivosElegiveis($extensao) {

        $listaArquivos = array();

       if($extensao == 'zip') {
           $this->validarDiretorio('backup');
           $arquivos = glob($this->diretorioBackup . "*." . $extensao);
       }
       else {
           $this->validarDiretorio('log');
           $arquivos = glob($this->diretorio . "*." . $extensao);
       }

       foreach($arquivos as $arq) {

            if($extensao == 'zip') {
                 $listaArquivos[] = $arq;
            }
            else {

                $nomeArquivo = str_replace($this->diretorio, '', $arq);

                /*
                * Obtem o mês
                */
                $lenght =  stripos($nomeArquivo, '2');
                $mes = substr($nomeArquivo, ($lenght + 5),2);

                /*
                 * Comente ira usar arquivo de meses anteriores
                 */
                if(intval(date("m")) == $mes) {
                    continue;
                }

                /*
                 * Obtém o nome da campanha
                 */
                $lenght =  stripos($nomeArquivo, '_');
                $campanha = substr($nomeArquivo, 0, $lenght);

                /*
                 * Obtém o nome do processo
                 */
                $lenght =  stripos($nomeArquivo, '2');
                $lenghtCampanha = strlen($campanha);
                $processo = str_replace('_', '', substr($nomeArquivo,$lenghtCampanha, ($lenght - $lenghtCampanha)));

                /*
                 * Aramazena os arquivos da mesma campanha / memso processo
                 */
                $listaArquivos[$campanha][$processo][] = $nomeArquivo;
            }
       }

       return $listaArquivos;

    }

    /**
     * Gera um arquivo de Log com a ação executada
     * @param array $arquivos
     * @param string $acao
     */
    private function gravaArquivoLog($arquivos, $acao, $erro = false) {

        $this->validarDiretorio('log');

        $nomeArquivo = "cron_limpeza_logs_ura_" . date("Y_m") . '.txt';

        $fp = fopen($this->diretorio . $nomeArquivo, "a+");

        if($fp){

            if( $this->startLog) {
                fwrite($fp, "=================================================================================================\n");
                fwrite($fp, "Data da ação:" . date("d/m/Y H:i")."\n");
            }

            if($acao == 'compactar'){
                fwrite($fp, "\nARQUIVOS COMPACTADOS E EXCLUIDOS: " . "\n\n");
            }
            else{
                fwrite($fp, "\nARQUIVOS ZIP EXCLUIDOS: " . "\n\n");
            }

            foreach($arquivos as $linha) {
                if($erro) {
                   $linha = "FALHA AO COMPACTAR O ARQUIVO: " .$linha;
                }
                fwrite($fp, $linha . "\n");
            }
        }
        fclose($fp);

        $this->startLog = false;
    }

    /**
     * Verifica se o diretório é valido para manipulação dos arquivos
     *
     * @param string $diretorio
     * @return boolean
     * @throws Exception
     */
    private function validarDiretorio($diretorio) {

       if($diretorio == 'backup') {
           $dir = $this->diretorioBackup;
       }
       else {
           $dir = $this->diretorio;
       }

       if(!is_dir($dir)) {

           if(!mkdir($dir, 0777)){
               throw new Exception("Diretório " . $dir . " não existe e não foi possível criá-lo.");
           }
       }

       if(!is_writable($dir)) {
           throw new Exception("Diretório " . $dir . " não tem permissão para escrita.");
       }

       return true;

    }

}
