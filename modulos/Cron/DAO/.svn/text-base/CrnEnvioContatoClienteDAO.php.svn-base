<?php

/** 
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 */
 
class CrnEnvioContatoClienteDAO {
    
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function buscarContatoCliente(){
        
        $resultado = array();
		
		/**
		 * Método que localiza dados de todos os contatos de clientes, efetuados 
		 * através dos portais, do tipo solicitação e que ainda não foram enviados 
		 * para o grupo de atendimento.
		 */
		$sql = "
			SELECT
				csugoid,
				TO_CHAR(csugcadastro,'dd/mm/yyyy') AS datacad,
				csugstatus,
				CASE csugstatus WHEN 'P' THEN (SELECT (date_part('day',(now()-csugcadastro)))::text) ELSE 'Não está Pendente' END AS pendencia,
				csugcliente,
				CASE csugcliente WHEN 't' THEN (SELECT clinome FROM clientes WHERE clioid=csugclioid) ELSE csugnome END AS cliente,
				csugconoid AS termo,
				(SELECT veiplaca FROM veiculo WHERE csugveioid=veioid) AS placa,
				trsdescricao AS tipo,
				(SELECT mtrdescricao FROM motivo_reclamacao WHERE mtroid=csumtroid) AS motivo,
				csugdescricao AS descricao
			FROM 
				cliente_sugestao  
				INNER JOIN tipo_recl_sugestao ON trsoid=csugtrsoid
			WHERE 
				csugexclusao IS NULL
				AND trsdescricao ILIKE 'Solicita%'
				AND csugdt_email_atendimento IS NULL;
		";
		
        $rs = pg_query($this->conn, $sql);
        
        if(pg_num_rows($rs) > 0) {
            $resultado = pg_fetch_all($rs);
        }
        
        return $resultado;
        
    }
	
    public function atualizarDataEnvio($csugoid) {
        
        $sql = "
            UPDATE 
                cliente_sugestao
            SET
                csugdt_email_atendimento = NOW()
            WHERE
                csugoid = ". $csugoid .";
        ";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
}

?>


