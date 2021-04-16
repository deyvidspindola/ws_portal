<?php

/**
 * Classe de persistência de dados  
 */
require (_MODULEDIR_ . "Financas/DAO/FinImportacaoStatusContratoDAO.php");

/**
 * FinImportacaoStatusContrato.php
 * 
 * Classe para importar e atualizar contratos em lote através 
 * de um arquivo CSV.
 * 
 * @author Willian Ouchi <willian.ouchi@meta.com.br>
 * @package Finanças
 * @since 07/12/2012
 * 
 */
class FinImportacaoStatusContrato {

    private $dao;
    private $path_file;
    
    function __construct() {
        
        global $conn;
        
        $this->path_file = '/var/www/docs_temporario/fin_importacao_status_contrato.csv';
        $this->id_usuario = $_SESSION['usuario']['oid'];
                
        $this->conn = $conn;
        $this->dao = new FinImportacaoStatusContratoDAO($conn);
    }
    
    
    /**
     * Método principal
     * @author Willian Ouchi <willian.ouchi@meta.com.br>
     */
    public function index() {
        
        cabecalho();
        
        include(_MODULEDIR_ . 'Financas/View/fin_importacao_status_contrato/index.php');
    }
    
    /*
     * 
     */
    public function importaCSV(){
        
        $csv = $_FILES['arquivo'];
        $this->par_operacao = (isset($_POST['par_operacao'])) ? $_POST['par_operacao'] : null;
        $this->par_base = (isset($_POST['par_base'])) ? $_POST['par_base'] : null;
   
        try {
            
            /*
             * Se a váriavel csv for um array então $_FILES
             * retornou um arquivo.
             */
            if ( is_array( $csv ) ){
                                
                /*
                 * Verifica se o arquivo é do tipo CSV.
                 */
                if ($this->buscaExtensao($csv['name']) != 'CSV'){
                    throw new Exception('Arquivo inválido deve ser do tipo csv.');
                }
                
                /*
                 * Faz o upload do arquivo para a pasta definida
                 * no contrutor da classe.
                 */
                $this->upload = $this->uploadAnexo($csv);
                if ($this->upload['erro'] == true){
                    throw new Exception($this->upload['mensagem']);
                }
                
                /*
                 * Lê o arquivo e retonar um array com os valores 
                 * do csv.
                 */
                $this->csv = $this->CSVtoArray();       
                if ($this->csv['erro'] == true){
                    throw new Exception($this->upload['mensagem']);
                }
                
                switch ($this->par_operacao) {
                    
                    case "valor_monitoramento":
                        $this->atualizaValorMonitoramento($this->csv['linhas']);
                    break;
                    
                    case "valor_outras":
                        $this->atualizaOutrasObrigacoes($this->csv['linhas']);
                    break;
                
                    case "status_stop":
                        $this->atualizaStatusStop($this->csv['linhas']);
                    break;
                    
                    case "status_ativo":
                        $this->atualizaStatusAtivo($this->csv['linhas']);
                    break;     

                    case "transf_titularidade":
                        $this->atualizaTransferenciaTitularidade($this->csv['linhas']);
                    break;       
                    
                }  
                
            }
            
        } catch(Exception $e) {
            
            $this->msg = $e->getMessage();
            
        }
        cabecalho();

        include(_MODULEDIR_ . 'Financas/View/fin_importacao_status_contrato/index.php');        
        
    }
    
    
    /*
     * 
     */
    public function uploadAnexo($csv) {
        
        try { 
            
            $enviado = move_uploaded_file($csv['tmp_name'], $this->path_file);

            if(!$enviado) {
                throw new Exception('Houve um erro no upload do arquivo!');                
            }
            
            return array(
                'erro'      => false,
                'mensagem'  => ''
            );        
        } catch(Exception $e) {
            
            return array(
                'erro'      => true,
                'mensagem'  => $e->getMessage()
            );
        }      
    } 
    
    
    /*
     * 
     */
    public function CSVtoArray(){
        
        $linha = array();
        $linhas = array();
        try{
            
            $leitura = fopen($this->path_file, 'r');
            
            if (!$leitura){
                throw new Exception('Erro na atualização dos registros verifique o arquivo!');
            }
            
            while ($linha = fgetcsv($leitura, 2048, ";")){          
                $linhas[] = $linha;        
            }
            
            return array(
                'erro'      => false,
                'mensagem'  => '',
                'linhas'    => $linhas 
            );
            
        }catch(Exception $e){
            
            return array(
                'erro'     => true,
                'mensagem' => $e->getMessage()
            );
            
        }
      
    }
    
    
    /*
     * Retorna a extensão de um arquivo.
     * @parameter string file_name
     */
    public function buscaExtensao($nome_arquivo){
        
        $extensao = explode('.', $nome_arquivo);
        
        return strtoupper(array_pop($extensao));        
    }
    
    
    /*
     * 
     */
    public function atualizaValor($linhas){
        
        $this->logCSV = array();
        $this->cancelaAtualizacao = false;
        
        $this->dao->begin();
        
        foreach ($linhas as $linha){
        
            try{
                $this->valor_obrigacao = str_replace(',', '.', $linha[1]);
                $this->numero_contrato = $linha[0];  
                $this->id_obrigacao_financeira =  $linha[2];
                $this->base_dados = $linha[3];

                
                
                /*
                 * Validação - Caso id da obrigação financeira,
                 * esteja com valor diferente de 1.
                */
                                 
                if( $this->par_operacao == "valor_monitoramento"  && $this->id_obrigacao_financeira != 1 ){
                	$this->cancelaAtualizacao = true;
                	throw new Exception('Arquivo selecionado não possui a obrigação financeira de monitoramento');
                }
                

                /*
                 * Validação - Caso arquivo, possua  colunas diferente de 4.
                */                
                if(count($linha) != 4){
                	$this->cancelaAtualizacao = true;
                	throw new Exception('Arquivo com layout inválido!');                	 
                }	
                
                
                /*
                 * Busca a obrigação financeira informada na linha retornada do CSV
                 */
                $rsObrigacaoFinanceira = $this->dao->buscaObrigacaoFinanceira($this->id_obrigacao_financeira);
                
                /*
                 * Validação - Caso a obrigação financeira informada não 
                 * exista na base de dados
                 */
                if (pg_num_rows($rsObrigacaoFinanceira) == 0){
                    $this->cancelaAtualizacao = true;
                    throw new Exception('Erro - Obrigação Financeira não encontrada na base de dados');            
                }
                
                
                /*
                 * Validação - Caso o valor da obrigação, coluna 2 do 
                 * arquivo, esteja com o valor 0 ou nulo.
                 */
                if (empty($this->valor_obrigacao) || $this->valor_obrigacao == 0){                    
                    $this->cancelaAtualizacao = true;
                    throw new Exception('Erro - Arquivo selecionado contém valor inválido'); 
                }
                
                
                /*
                 * Validação - Caso a base, coluna 4 do arquivo, esteja 
                 * diferente do combo selecionado em tela.
                 */
                if (strtolower($this->base_dados) != strtolower($this->par_base)){
                    $this->cancelaAtualizacao = true;
                    throw new Exception('Erro - Base de dados informada não é compatível com a base de dados do arquivo selecionado');
                }
                
                
                /*
                 * Busca o contrato informado na linha retornada do CSV
                 */
                $rsContrato = $this->dao->buscaContrato($this->numero_contrato);
                $this->contrato = pg_fetch_assoc($rsContrato);                
                
                /*
                 * Busca as obrigações financeiras do contrato informado 
                 * na linha retornada do CSV
                 */
                $rsContratoObrigacaoFinanceira = $this->dao->buscaContratoObrigacaoFinanceira($this->numero_contrato, $this->id_obrigacao_financeira);
                $this->contrato_obrigacao_financeira = pg_fetch_assoc($rsContratoObrigacaoFinanceira);
                
                
                /*
                 * Validação - Contrato inexistente.
                 */                
                if (pg_num_rows($rsContrato) == 0){
                    throw new Exception('Erro - Contrato não encontrado na base de dados');            
                }
                
                
                /*
                 * Validação - Contrato inexistente.
                 */                
                if (pg_num_rows($rsContratoObrigacaoFinanceira) == 0){
                    throw new Exception('Erro - Obrigação Financeira não encontrada na base de dados');            
                }
                
                
                /*
                 * Validação - Contrato desativado.
                 */
                if ($this->contrato['condt_exclusao']){
                    throw new Exception('Erro - Contrato desativado');                    
                }
                
                
                /*
                 * Validação - Valor, da obrigação financeira, atualizado
                 * igual ao cadastrado na base de dados.
                 */
                if ($this->contrato_obrigacao_financeira['cofvl_obrigacao'] == $this->valor_obrigacao){
                    throw new Exception('Erro - Valor informado no arquivo igual ao da obrigação financeira');                    
                }
                
                $this->base_dados = ($this->base_dados == 'sbtec') ? 'sbtec.' : '';
                
                $rs = $this->dao->atualizaValor(
                        $this->valor_obrigacao,
                        $this->numero_contrato,
                        $this->id_obrigacao_financeira,
                        $this->base_dados
                );

                /*
                 *  O valor foi atualizado com sucesso, grava o histórico
                 *  de contrato financeiro.
                 */
                if (!$rs['erro'] && $rs['quantidade_linhas'] > 0){

                    $this->dao->insereHistoricoContratoFinanceiro(
                        $this->numero_contrato,
                        $this->id_obrigacao_financeira,
                        $this->id_usuario,
                        'E',
                        $this->contrato_obrigacao_financeira['cofvl_obrigacao'],
                        $this->descricao_log,
                        $this->contrato_obrigacao_financeira['cofoid']                        
                    );
                    
                    $this->dao->insereHistoricoContratoFinanceiro(
                        $this->numero_contrato,
                        $this->id_obrigacao_financeira,
                        $this->id_usuario,
                        'I',
                        $this->valor_obrigacao,
                        $this->descricao_log,
                        $this->contrato_obrigacao_financeira['cofoid']                        
                    );
                    
                    $this->msg = 'Importação realizada com sucesso!';

                }
                else{
                    $this->msg = $rs['mensagem'];
                }

            }
            catch(Exception $e){

                $erro = $e->getMessage();
                
                if (!$this->cancelaAtualizacao){
                
                    $this->dao->insereLogErroImportacaoContrato(
                        $this->numero_contrato,
                        $this->id_obrigacao_financeira,
                        $this->id_usuario,
                        $this->descricao_log,
                        $erro,
                        $this->valor_obrigacao
                    );                        

                    $this->logCSV[] = $this->numero_contrato . ";" . $this->id_obrigacao_financeira . ";" . $this->valor_obrigacao . ";" . $erro . "; \n";                
                }
            }
        } 
        
        if (count($this->logCSV) > 0 && !$this->cancelaAtualizacao){

            array_unshift($this->logCSV, "número do contrato; cod da obrig.financ; valor; observação; \n");
            $this->nomeLogCsv = $this->gravaLogCSV($this->logCSV);
            $this->exibeLogCSV = true;
        }
        
        if ($this->cancelaAtualizacao){
            
            $this->msg = $erro;
            $this->dao->rollback();
        }
        else{
            
            $this->dao->commit();
        }
        
    }
        
    
    public function atualizaStatus($linhas){
        
        $this->logCSV = array();        
        $this->cancelaAtualizacao = false;
        
        $this->dao->begin();
        
        foreach ($linhas as $linha){
        
            try{
                $this->numero_contrato = $linha[0]; 
                $this->status_contrato = $linha[1];
                $this->base_dados = $linha[2];
                
                
                
                /*
                 * Validação - Caso arquivo, possua  colunas diferente de 3.
                */
                if(count($linha) != 3){
                	$this->cancelaAtualizacao = true;
                	throw new Exception('Arquivo com layout inválido!');
                }
                
                
                /*
                 * Busca o contrato informado na linha retornada do CSV
                 */
                $rsContrato = $this->dao->buscaContrato($this->numero_contrato);
                $this->contrato = pg_fetch_assoc($rsContrato);
                
                /*
                 * Validação - Status do contrato, coluna 1, diferente da 
                 * operação selecionada.
                 */
                if ($this->csioid != $this->status_contrato){
                    
                    $this->cancelaAtualizacao = true;
                    throw new Exception('Erro - Arquivo selecionado não contém a coluna com o conteúdo igual a '.$this->csioid);
                }
                
                /*
                 * Validação - Contrato inexistente.
                 */ 
                if (pg_num_rows($rsContrato) == 0){
                    throw new Exception('Erro - Contrato não encontrado!');            
                }
                
                /*
                 * Validação - Contrato desativado.
                 */
                if ($this->contrato['condt_exclusao']){
                    throw new Exception('Erro - Contrato desativado!');                    
                }
                                
                /*
                 * Validação - Caso a base, coluna 3 do arquivo, esteja 
                 * diferente do combo selecionado em tela.
                 */
                if (strtolower($this->base_dados) != strtolower($this->par_base)){
                    
                    $this->cancelaAtualizacao = true;                    
                    throw new Exception('Erro - Base de dados informada não é compatível com a base de dados do arquivo selecionado');
                }

                $this->base_dados = (strtolower($this->base_dados) == 'sbtec') ? 'sbtec.' : '';
                
                $rs = $this->dao->atualizaStatus(
                        $this->numero_contrato, 
                        $this->csioid,
                        $this->base_dados
                );
                
                /*
                 *  O status foi atualizado com sucesso, grava o histórico
                 *  de contrato.
                 */
                if (!$rs['erro'] && $rs['quantidade_linhas'] > 0){

                    $this->dao->insereHistoricoContrato(
                        $this->numero_contrato,
                        $this->id_usuario,
                        $this->descricao_operacao,
                        "null",
                        "null"
                    );
                    
                    $this->msg = 'Importação realizada com sucesso!';

                }
                else{
                    $this->msg = $rs['mensagem'];
                }

            }
            catch(Exception $e){
                
                $erro = $e->getMessage();

                if (!$this->cancelaAtualizacao){
                    
                    $this->dao->insereLogErroImportacaoContrato(
                        $this->numero_contrato,
                        'null',
                        $this->id_usuario,
                        $this->descricao_log,
                        $erro,
                        $this->csioid
                    );                        

                    $this->logCSV[] = $this->numero_contrato . ";" . $this->csioid . ";" . $erro . "; \n";
                }
            }                        
        }
        
        if (count($this->logCSV) > 0 && !$this->cancelaAtualizacao){
            
            array_unshift($this->logCSV, "número do contrato; status; observação; \n");    
            $this->nomeLogCsv = $this->gravaLogCSV($this->logCSV);
            $this->exibeLogCSV = true;
        }
        
        if ($this->cancelaAtualizacao){
            
            $this->msg = $erro;
            $this->dao->rollback();
        }
        else{
            
            $this->dao->commit();
        }
    }
        
    
    /*
     * 
     */
    public function gravaLogCSV($logCSV){
        
        $nomeLogCsv = "/var/www/docs_temporario/log_erro_" . date('Ymd') . ".csv";
        file_put_contents($nomeLogCsv, $logCSV);

        return $nomeLogCsv;
        
    }
    
    
    /*
     * 
     */
    public function atualizaValorMonitoramento($linhas){
        
        $this->descricao_log = "Altera o valor da obrigação financeira (Monitoramento)";
        $this->descricao_operacao = "Importação - Status Contrato - Alteração de Valor";
        $this->is_monitoramento = true;
        $this->atualizaValor($linhas);              
        
    }
    
    
    /*
     * 
     */
    public function atualizaOutrasObrigacoes($linhas){
        
        $this->descricao_log = "Altera o valor das demais obrigações financeiras";
        $this->descricao_operacao = "Importação - Status Contrato - Alteração de Valor";
        $this->is_monitoramento = false;
        $this->atualizaValor($linhas);
        
    }
    
    
    /*
     * 
     */
    public function atualizaStatusStop($linhas){
        
        $this->descricao_log = "Altera o status para STOP FATURAMENTO";
        $this->descricao_operacao = "Importação - Status Contrato - Alteração de Status para STOP FATURAMENTO";
        $this->csioid = 37;
        $this->atualizaStatus($linhas);

    }
    
    
    /*
     * 
     */
    public function atualizaStatusAtivo($linhas){
        
        $this->descricao_log = "Altera o status para ATIVO";
        $this->descricao_operacao = "Importação - Status Contrato - Alteração de Status para ATIVO";
        $this->csioid = 1;
        $this->atualizaStatus($linhas);
        
    }


    /*
     * 
     */
    public function atualizaTransferenciaTitularidade($linhas){
        
        $this->descricao_log = "Alterar o status para TRANSFERÊNCIA DE TITULARIDADE";
        $this->descricao_operacao = "Importação - Status Contrato - Alteração de Status para TRANSFERÊNCIA DE TITULARIDADE";
        $this->csioid = 13;
        $this->atualizaStatus($linhas);
        
    }

    
}
?>
