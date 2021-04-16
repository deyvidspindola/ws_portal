<?php
/**
 * Classe padrão para DAO
 *
 * @author andre.zilz <andre.zilz@meta.com.br>
 */
class CadCondicaoPagamentoDAO {
    
    /**
     * Conexão com o banco de dados
     * @var resource
     */
    private $conn;
    
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    
    /**
     * Cosntrutora da Classe
     * 
     * @param object $conn
     */
    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }
    
    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros
     * @return array
     * @throws ErrorException
     */
    public function pesquisar(stdClass $parametros){
        
        $retorno = array();
        
        $sql = "SELECT 
					cpgoid, 
					cpgdescricao, 
					array_to_string(cpgvencimentos, ';') AS cpgvencimentos,
        			ARRAY_LENGTH(cpgvencimentos, 1) AS cpgparcelas
				FROM 
					condicao_pagamento
				WHERE 
					1 = 1
        		";
		
        //Número de parcelas
        if ( isset($parametros->cpgparcelas) && !empty($parametros->cpgparcelas) ) {
        
            $sql .= "AND
                        ARRAY_LENGTH(cpgvencimentos, 1) = " . intval( $parametros->cpgparcelas );
                
        }
        
        //Descrição
        if ( isset($parametros->cpgdescricao) && !empty($parametros->cpgdescricao) ) {
        
        	$sql .= "AND
                        cpgdescricao ILIKE '%" . pg_escape_string( $parametros->cpgdescricao ) . "%'";
        
        }
        
        $sql .= " ORDER BY cpgdescricao";

        
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
		while ($row = pg_fetch_object($rs)) {			 
			$retorno[] = $row;
		}
        
        return $retorno;
    }
    
    /**
     * Método para realizar a pesquisa de apenas um registro.
     * 
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarPorID($id) {
        
        $retorno = new stdClass();
        
        $sql = "SELECT 
					cpgoid, 
					cpgdescricao, 
					array_to_string(cpgvencimentos, ';') AS cpgvencimentos
				FROM 
					condicao_pagamento
				WHERE 
					cpgoid =" . $id . "";
  
        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
     
        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }
        
        return $retorno;
    }    
    
    /**
     * Veririca se já existe uma condiçãod e pagamento com os mesmos vencimentos
     * 
     * @param stdClass $dados
     * @throws ErrorException
     * @return boolean
     */
    public function verificarExistenciaVencimentos(stdClass $dados) {
    	
    	$sql = "
    			SELECT EXISTS(
					    	SELECT
					    		1
					    	FROM
					    		condicao_pagamento
					    	WHERE					    		
					    		cpgvencimentos = array [" . $dados->vencimentos ."]
					";
					    	
			
		if (isset($dados->cpgoid) && !empty($dados->cpgoid)) {
			
			$sql .= " AND cpgoid != ". $dados->cpgoid ."";
		
		}
		
		$sql .= "
					) AS existe
				";
 
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}
    	
    	$resultado = pg_fetch_object($rs);
    	
    	$retorno = ($resultado->existe == 'f') ? false : true;

    	return $retorno;
    	
    }
    
    
    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $dados){
        
        $sql = "INSERT INTO
					condicao_pagamento
					(
					cpgdescricao,
					cpgvencimentos
					)
				VALUES
					(
					'" . pg_escape_string( $dados->cpgdescricao ) . "',
					array [" . $dados->vencimentos ."]
				)";
  
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        return true;
    }
    
    /**
     * Responsável por atualizar os registros 
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(stdClass $dados) {
    	
        $sql = "UPDATE
					condicao_pagamento
				SET
					cpgdescricao = '" . pg_escape_string( $dados->cpgdescricao ) . "',							
					cpgvencimentos = array [" . $dados->vencimentos ."]
				WHERE 
					cpgoid = " . intval($dados->cpgoid) . "";
 
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        
        return true;
    }   
   
    /**
     * Exclui um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir($id) {
    
    	$sql = "DELETE FROM
					condicao_pagamento				
				WHERE
					cpgoid = " . $id . "";
  
    	if (!pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}
    
    	return true;
    }
    
    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }
    
    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }
    
    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }
    
    
}

?>
