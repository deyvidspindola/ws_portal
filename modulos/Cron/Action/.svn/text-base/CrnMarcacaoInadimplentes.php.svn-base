<?php
/**
 * @author Paulo Sergio Bernardo Pinto
 */
 
/**
 * Classe padrão para envio de emails
 */
require _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';


class CrnMarcacaoInadimplente{

    private $dao;

    public function __construct($conn) {
        $this->dao = new CrnMarcacaoInadimplenteDAO($conn);
        $this->diretorio = _SITEDIR_.'arq_financeiro/inadimplentes/';
        $this->diasremocaoarquivolog = 30;
        if(is_dir($this->diretorio) === false) {
            mkdir($this->diretorio, 0777, true); 
        }        
    }

    public function buscarParametroDiasAtraso() {
        return $this->dao->buscarParametroDiasAtraso();
    }
    /**
     * Busca dados de clientes
     */
    public function buscarClientesInadimplentes($diasAtraso) {
        return $this->dao->buscarClientesInadimplentes($diasAtraso);
    }

    public function buscarTitulosClientesInadimplentes($clioid, $diasAtraso) {
        return $this->dao->buscarTitulosClientesInadimplentes($clioid, $diasAtraso);
    }

    public function atualizarInadimplentes($titoid,$cd_usuario) {
        return $this->dao->atualizarInadimplentes($titoid, $cd_usuario);
    }

    public function atualizarDataEnvio($csugoid) {
       $this->dao->atualizarDataEnvio($csugoid);
    }
    
    public function adicionarHistoricoInadimplentes($titoid, $cd_usuario, $clioid, $diasAtraso) {
       $this->dao->adicionarHistoricoInadimplentes($titoid, $cd_usuario, $clioid, $diasAtraso );
    }

    public function getRelatorio($dataMaxima) {
        $arquivosPasta = array();
        foreach (new DirectoryIterator($this->diretorio) as $fileInfo) {
            if($fileInfo->isDot()) continue;
                $timestamp = $fileInfo->getMTime();
                $datetimeFormat = 'd/m/Y';
                $date = new \DateTime();
                $date->setTimestamp($timestamp);

                $data = explode('/',substr($dataMaxima, 0,10));
                $Str_data_inicio = $data[2].'-'.$data[1].'-'.$data[0];
                $Str_data_fim    = $date->format('Y-m-d');
                $data_inicio     = new DateTime($Str_data_inicio);
                $data_fim        = new DateTime($Str_data_fim);

                // Resgata diferença entre as datas
                $dateInterval = $data_inicio->diff($data_fim);
                if ($dateInterval->days >= $this->diasremocaoarquivolog){
                    $excluido = unlink($this->diretorio.$fileInfo->getFilename());  
                }else{
                    if ($date->format($datetimeFormat) == $data_inicio->format($datetimeFormat)){
                        $arquivosPasta[] = array (
                            'data_hora' => $fileInfo->getMTime(),
                            'titulo' => $fileInfo->getFilename(),
                            'ext' => pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION)
                        );
                    }
                }
            }
            rsort($arquivosPasta);
        return $arquivosPasta;
    }
    
}

?>