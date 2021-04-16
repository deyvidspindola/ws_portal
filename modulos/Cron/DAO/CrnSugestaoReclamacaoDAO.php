<?php

/** 
 * @author ricardo.marangoni
 */
class CrnSugestaoReclamacaoDAO {
    
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function buscarOcorrenciasOuvidoria(){
        
        $resultado = array();
        
        $sql = "
            SELECT
                csugoid,
                csugnome_contato,
                csugemail_contato,                
                csugdescricao,
                seecorpo,
                seecabecalho,
                seeobjetivo
            FROM
                cliente_sugestao            
            INNER JOIN
                servico_envio_email ON seeoid = csugseeoid AND seedt_exclusao IS NULL
            WHERE
                csugexclusao IS NULL
            AND
                csugstatus = 'C'
            AND
                csugdt_envio_email IS NULL            
        ";
        
        $rs = pg_query($this->conn, $sql);
        
        if(pg_num_rows($rs) > 0) {
            $resultado = pg_fetch_all($rs);
        }
        
        return $resultado;
        
    }    
    
    public function gravarLogEnvio($csugoid, $obs) {
        
        $sql = "
            INSERT INTO
                cliente_sugestao_historico
                (cshcsugoid, cshcadastro, cshusuoid, cshobs)
            VALUES
                (". $csugoid .", NOW(), 2750, '". $obs ."')
        ";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    public function atualizarDataEnvio($csugoid) {
        
        $sql = "
            UPDATE 
                cliente_sugestao
            SET
                csugdt_envio_email = NOW()
            WHERE
                csugoid = ". $csugoid ."
        ";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
}