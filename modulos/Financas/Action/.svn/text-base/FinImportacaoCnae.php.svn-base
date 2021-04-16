<?php
/**
 * @autor   Paulo Sergio B Pinto
 * @versao   23/04/2020
 **/

require_once (_MODULEDIR_ . 'Financas/DAO/FinImportacaoCnaeDAO.php');

//INTEGRAÇÃO TOTVS
require _SITEDIR_.'modulos/core/infra/autoload.php'; //Attempt to load CORE class

/**
 * Trata requisições do módulo financeiro para importar registro de cnae 
 * gerados pelo protheus
 */
class FinImportacaoCnae {
    /**
     * Fornece acesso aos dados necessarios para o módulo
     * @property FinImportacaoCnaeDAO
     */
    private $dao;
    
    /**
     * Construtor, configura acesso a dados e parâmetros iniciais do módulo
     */
    public function __construct() 
    {
        global $conn;
        $this->dao  = new FinImportacaoCnaeDAO($conn);
    }
    
    /**
     * Função responsável por fazer o upload do arquivo
     * @return date
     */
    public function upload($cnfidescricao_motivo){   
        
        $msgErro = array();

        
        try{
            $file = $_FILES['arq_importacao'];

            list($nome, $ext) = explode(".",$file['name']);

            $tiposPermitidos = array('application/vnd.ms-excel','text/csv','application/force-download');

            if($ext != 'csv'){
                 return 2;
            }

            if(file_exists($file['tmp_name']))
            {
                $fh = fopen($file['tmp_name'], 'r');

                $this->dao->begin();
            
                // faz a contagem da linha
                $linha = 1;
                $idCnaeArquivo = 0;
                $arquivo_OK = false;
                
                while (($data = fgetcsv($fh, 100000, ";")) !== FALSE) {
                    
                    $arquivo_OK = true;
                    //Efetua a leitura do arquivo e aplica validacao de arquivo e registros
                    $confere = $this->confereArquivo($data, $linha, $idCnaeArquivo, $cnfidescricao_motivo);
                    if(($linha == 1) && ($confere != 0)){
                        $idCnaeArquivo = $confere;
                    }else{
                        if($confere == 0){
                            $arquivo_OK = false;
                            break;
                        }
                    }
                    $linha ++;
                }

                if(!$arquivo_OK){
                	 throw new Exception('Falha no processamento, verifique a formatação do arquivo.');
                }
                
                //fecha o arquivo
                fclose($fh);
                
                $this->dao->commit();

                return 1;

            } else {
                //Erro
                throw new Exception('Erro ao importar o arquivo.');
            }

        }catch(Exception $e){
        	
            $this->dao->rollback();
        	
            $msgErro['msg'] = $e->getMessage();
            $msgErro['cod'] = 0;
            
            return $msgErro;
        }
    }
    
    /**
    * Função que separa os dados conforme o tipo de registro existente no arquivo
    * @param array $data - Dados do registro
    * @return boolean - False quando registro diferente de venda e crédito
    */
    private function confereArquivo($data, $linha, $idCnaeArquivo, $cnfidescricao_motivo) {
    	$dados_arq = new StdClass();
        if($data[0] != 'codigo'){
            $dados_arq->cnaecodigo         = trim($data[0]);// Codigo Cnae
            $dados_arq->cnaedescricao      = trim($data[1]);// Descricao referente ao cnae
            $dados_arq->idCnae             = $idCnaeArquivo;// id gerado na inclusao de cabecalho
            if($dados_arq->idCnae == 0){
                return 0;
            }else{
                 return $this->dao->setLinhaCnae($dados_arq);
            }
            
        }else{
            if(($data[0] == 'codigo') && ($data[01] == 'descricao')){
               //consulta se existe outros dados ativos
               $cnae_gravados = $this->dao->getCnaeGravados($dados_arq);
               if($cnae_gravados){
               //inativa dados e realiza a gracavao de novos registros
                   $this->dao->setInativaCnae($cnae_gravados);
                }
                $idCnaeNovo = 0;
                $idCnaeNovo = $this->dao->setCabecalhoCnae($dados_arq,$cnfidescricao_motivo);
                return $idCnaeNovo;
            }else{
                return 0;
            }
        }
    }
    
    public function envioConcluido(){
        return 2;
    }
    
    public function getArquivosCnae($dtInicial, $dtFinal, $cnaecodigo, $cnaedescricao){
        $param_rel = new StdClass();

        $param_rel->dtInicial         = trim($dtInicial);// Codigo Cnae
        $param_rel->dtFinal           = trim($dtFinal);// Codigo Cnae
        $param_rel->cnaecodigo         = trim($cnaecodigo);// Codigo Cnae
        $param_rel->cnaedescricao         = trim($cnaedescricao);// Codigo Cnae

        return $this->dao->getRelatorioArquivoCnae($param_rel);
        
    }
    
    public function getPopupCnae($cnfioid){

        return $this->dao->getListaCnae($cnfioid);
        
    }
}
