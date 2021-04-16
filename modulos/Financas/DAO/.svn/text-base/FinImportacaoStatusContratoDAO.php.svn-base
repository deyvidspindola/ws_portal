<?php

/**
 * Classe de persistência de dados
 * 
 * @author Willian Ouchi <willian.ouchi@meta.com.br>
 * 
 */
class FinImportacaoStatusContratoDAO {

    private $conn;
    
    
    function __construct($conn) {

        $this->conn = $conn;
    }
    
    
    public function begin() {
    	pg_query($this->conn, "BEGIN;");
    }
    
    
    public function commit() {
    	pg_query($this->conn, "COMMIT");
    }
    
    
    public function rollback() {
    	pg_query($this->conn, "ROLLBACK;");
    }    
    
    
    function buscaContrato($numero_contrato){
        
        $sql = "
            SELECT
                condt_exclusao 
            FROM
                contrato 
            WHERE
                connumero = $numero_contrato
        ";  

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao buscar o contrato.');
        }
        
        return $rs;        
    }    
    
    
    function buscaContratoObrigacaoFinanceira($numero_contrato, $id_obrigacao_financeira){
        
        $sql = "
            SELECT
                cofoid,                
                cofdt_termino,
                cofvl_obrigacao
            FROM
                contrato_obrigacao_financeira
            WHERE
                cofdt_termino IS NULL
                AND cofconoid = $numero_contrato
                AND cofobroid = $id_obrigacao_financeira
        ";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao buscar a obrigação financeira do contrato.');
        }
        
        return $rs;
    }
    
    
    public function buscaObrigacaoFinanceira($obroid=null){
        
        $where = "";
        
        if ($obroid){
            $where .= " AND obroid = $obroid ";
        }
        
        $sql = "
            SELECT
                obroid
            FROM
                obrigacao_financeira
            WHERE
                obrdt_exclusao IS NULL
                $where
        ";
       
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao buscar a obrigação financeira do contrato.');
        }
        
        return $rs;
    }
    
    
    /*
     * Atualiza o valor de uma obrigação financeira em um contrato a 
     * partir do número do contrato e da obrigação financeira informados.
     * Retorna o ID atualizado
     */
    public function atualizaValor(
        $valor_obrigacao,
        $numero_contrato,
        $id_obrigacao_financeira,
        $base_dados
    ){
        
        try{
            
            $sql = "
                UPDATE 
                    ".$base_dados."contrato_obrigacao_financeira 
                SET 
                    cofvl_obrigacao = $valor_obrigacao,
                    cofalterado_importacao = true
                WHERE 
                    cofdt_termino IS NULL
                    AND cofconoid = $numero_contrato  
                    AND cofobroid = $id_obrigacao_financeira
                RETURNING
                    cofoid
            ";
            if (!$rs = pg_query($this->conn, $sql)) {
                throw new Exception('Erro na atualização dos registros verifique o arquivo!');
            }

            return array(
                'erro'                              => false,
                'quantidade_linhas'                 => pg_affected_rows($rs)
            );
            
        }catch (Exception $e){
            
            return array(
                'erro'      => true,
                'mensagem'  => $e->getMessage()
            );
            
        }
        
    }
    
    
    public function atualizaStatus(
        $numero_contrato,
        $status_contrato,
        $base_dados
    ){
        /*
         * Caso o usuário não esteja logado define o usuário "AUTOMATICO".
         */
        $conusualteracaooid = empty($_SESSION['usuario']['oid']) ? 2750 : $_SESSION['usuario']['oid'];
        
        try{
            
            $sql = "
                UPDATE 
                    ".$base_dados."contrato  
                SET 
                    concsioid          = $status_contrato,
                    conusualteracaooid = $conusualteracaooid
                WHERE 
                    connumero = $numero_contrato  
            ";

            if (!$rs = pg_query($this->conn, $sql)) {
                throw new Exception('Erro na atualização dos registros verifique o arquivo!');
            }

            return array(
                'erro'              => false,
                'quantidade_linhas' => pg_affected_rows($rs)
            );
            
        }catch (Exception $e){
            
            return array(
                'erro'      => true,
                'mensagem'  => $e->getMessage()
            );            
        }       
    }
    
    
    public function insereHistoricoContratoFinanceiro(
        $numero_contrato,
        $id_obrigacao_financeira,
        $id_usuario,
        $acao,
        $valor_item,
        $descricao_alteracao,
        $id_contrato_financeiro
    ){
        
        $sql = "
            INSERT INTO
                historico_contrato_financeiro
            (
                hcfconnumero,
                hcfobroid,
                hcfusuoid,
                hcfdt_alteracao,
                hcfacao,
                hcfvl_item,
                hcfalteracao,
                hcfcofoid
            )
            VALUES
            (
                $numero_contrato,
                $id_obrigacao_financeira,
                $id_usuario,
                NOW(),
                '$acao',
                $valor_item,
                '$descricao_alteracao',    
                $id_contrato_financeiro
            )
        ";
        pg_query($this->conn, $sql);
        
    }
    
    
    public function insereHistoricoContrato(
        $numero_contrato,
        $id_usuario,
        $descricao_operacao,
        $numero_protocolo,
        $id_atendimento
    ){
        
        $sql = "
            SELECT historico_termo_i(
                $numero_contrato,
                $id_usuario,
                '$descricao_operacao'
            )
        ";
        pg_query($this->conn, $sql);
        
    }
    
    
    public function insereLogErroImportacaoContrato(
        $numero_contrato,
        $id_obrigacao,
        $id_usuario,
        $descricao_operacao,
        $erro,
        $valor_obrigacao
    ){
        $sql = "
            INSERT INTO 
                log_importacao_contrato
            (
                licconoid,
                liccofoid,
                licusuoid,
                licdt_cadastro,
                licdescricao,
                licobs,
                licvl_alterado
            )
            VALUES
            (
                $numero_contrato,
                $id_obrigacao,
                $id_usuario,
                NOW(),
                '$descricao_operacao',
                '$erro',
                $valor_obrigacao
            )
        ";
        pg_query($this->conn, $sql);
        
    }
}

?>
