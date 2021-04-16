<?php
/**
 * Classe que realiza limpeza de logs de comunicação com o OFSC
 * @since 01/03/2016
 * @author [Vinicius Senna] <[vsenna@brq.com]>
 */

require_once _MODULEDIR_ . 'Cron/DAO/LimpezaLogSmartAgendaDAO.php';

class LimpezaLogSmartAgenda {

    private $dao;
    private $nomeRotina;
    private $nomeArquivoLog;
    private $diretorioLog;
    private $erro;

    /**
     * [__construct Construtor da rotina]
     * @param string $nomeRotina [description]
     */
    public function __construct($conn, $nomeRotina = '') {
        $this->dao = new LimpezaLogSmartAgendaDAO($conn);
        $this->nomeRotina = $nomeRotina;
        $this->nomeArquivoLog = 'log_erro_cron_smartagenda_'.date('Ymd').'.txt';
        $this->diretorioLog = '/var/www/docs_temporario/';
        $this->erro = '';
    }

    /**
     * Seta erro
     * @param [string] $strErro [description]
     */
    public function setErro($strErro) {
        $this->erro = $strErro;
    }

    /**
     * Retorna erro
     * @return [type] [description]
     */
    public function getErro() {
        return $this->erro;
    }

    /**
     * Retorna o nome da rotina que 
     * @return [string] [description]
     */
    public function getNomeRotina() {
        return $this->nomeRotina;
    }

    /**
     * Retorna o nome do arquivo de log de erro
     * @return [string] [description]
     */
    public function getNomeArquivoLog() {
        return $this->nomeArquivoLog;
    }

    /**
     * Retorna caminho do diretorio de logs
     * @return [string] [description]
     */
    public function getDiretorioLog() {
        return $this->diretorioLog;
    }

    /**
     * Retorna data a partir da qual os registros serão removidos
     * @param  [type] $quantidadeDias [description]
     * @return [type]                 [description]
     */
    public function getDataRemocao($quantidadeDias) {
        $time = '-' . (int) $quantidadeDias . ' day';
        return date('Y-m-d', strtotime($time, strtotime('now')));
    }

    /**
     * Valida quantidade de dias
     * @param  [type] $dias [description]
     * @return [type]       [description]
     */
    public function validaQuantidadeDias($dias) {

        if(!is_numeric($dias) || (int) $dias < 0) {
            return false;
        }

        return (int) $dias;
    }

    /**
     * Executa cron de limpeza de base
     * @return [type] [description]
     */
    public function executaLimpezaLog() {

        try {
            // Pega quantidade de dias parametrizados
            $dias = $this->dao->quantidadeDias();

            if(isset($dias->erro)) {
                $this->gravaLogErro($dias);
                throw new Exception($dias->erro);
            }

            // Verifica se a quantidade de dias é válida
            $qtdDias = $this->validaQuantidadeDias($dias->resultado->qtd);

            if($qtdDias === false) {
                $msgErro = "Quantidade de dias invalida: " . (string) $dias->resultado->qtd;
                $this->setErro($msgErro);
                $this->gravaLogErro($dias->sql);
                throw new Exception($msgErro);
            }

            // Realiza limpeza do log
            $resLimpeza = $this->dao->limpaLog($this->getDataRemocao($qtdDias));

            if(isset($resLimpeza->erro)) {
                $this->gravaLogErro($resLimpeza);
                throw new Exception($resLimpeza->erro);
            }

        } catch(Exception $e) {
            echo $e->getMessage() . "\n";
        }
         
    }

    /**
     * Escreve no arquivo de log de erro
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    private function gravaLogErro($info) {

        $arquivo = $this->getDiretorioLog().$this->getNomeArquivoLog();

        $fp = fopen($arquivo, "a+");
        $conteudo = date("H:i:s") . "\n";
        $conteudo .= "CRON: " . $this->getNomeRotina() . "\n";
        $conteudo .= "ERRO: ";

        if(strlen($this->getErro()) > 0) {
            $conteudo .=  $this->getErro() . "\n";
        }

        if(isset($info->erro) && strlen($info->erro) > 0) {
            $conteudo .= (string) $info->erro ."\n";
        }

        if(isset($info->sql)) {
            $conteudo .= "QUERY: " . $info->sql . "\n";
        }

        $conteudo .= "_____________________________________________\n";

        fwrite($fp, $conteudo);

        fclose($fp);
    }

    /**
     * Error handler
     * @param  [type] $errno   [description]
     * @param  [type] $errstr  [description]
     * @param  [type] $errfile [description]
     * @param  [type] $errline [description]
     * @return [type]          [description]
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {

        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_WARNING:
            case E_USER_WARNING:
                $erro = "[$errno] $errstr<br />\n";
                $erro .= "Error on line $errline in file $errfile \n";
                $this->setErro($erro);
                break;

            case E_USER_NOTICE:
                echo "<b>NOTICE: </b> [$errno] $errstr<br />\n";
            break;

            default:
                echo "Unknown error type: [$errno] $errstr<br />\n";
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }
}