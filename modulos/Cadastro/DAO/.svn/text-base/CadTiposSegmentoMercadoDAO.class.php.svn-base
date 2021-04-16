<?php

/**
 * @file CadTiposSegmentoMercadoDAO.class.php
 * @author Paulo Henrique da Silva Junior
 * @version 14/06/2013
 * @since 14/06/2013
 * @package SASCAR CadTiposSegmentoMercadoDAO.class.php
 */
/**
 * Acesso a dados para o módulo Tipos de Segmento de Mercado
 */
class CadTiposSegmentoMercadoDAO {
	
	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;
	

	/**
	 * Construtor, recebe a conexão com o banco
	 * @param resource $connection
	 * @throws Exception
	 */


	public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->usuoid = $_SESSION['usuario']['oid'];     
    }



    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }


	
    public function getTipos ($segdescricao = '', $segoid = '', $selected = null) {
    	$sql = "
            SELECT 
            	segoid, segdescricao 
            FROM 
            	segmento 
            ";

        if ($segdescricao != '') {
        $sql .= "WHERE
            	to_ascii(segdescricao) ilike to_ascii('%$segdescricao%')";
        } else if ($segoid != '') {
        $sql .= "WHERE
        			segoid = '$segoid'
        		";
        }

        $sql .= " ORDER BY 
            	segdescricao
			";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['segoid']       = pg_fetch_result($rs, $i, 'segoid');
            $result[$i]['segdescricao'] = pg_fetch_result($rs, $i, 'segdescricao');
            $result[$i]['selected']     = (pg_fetch_result($rs, $i, 'segoid') == $selected) ? true : false;
        }
        return $result;    	

    }

    public function excluirDados($segoid)
	{
		$resultado = array();
		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if(!$segoid){
	        	throw new Exception ("Erro ao Excluir.");
	        }
	        
	        $query_val = "  SELECT 
	                            COUNT(*) AS total 
	                        FROM 
	                            embarcador 
	                        WHERE 
	                            embsegoid = '$segoid'";
	        if(!$sql_val = pg_query($this->conn, $query_val)){
	        	throw new Exception ("Houve um erro ao excluir o registro.");
	        }
			
			if(pg_fetch_result($sql_val,0,'total') > 0){
				throw new Exception ("Operação cancelada: Tipo de segmento de mercado está vinculado a um ou mais embarcadores.");
			}

			$query = "  DELETE FROM
							segmento
						WHERE
							segoid = '$segoid'
					";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao excluir o registro.");
	        }
			

			$mensagem = "Registro excluído com sucesso.";
			$acao = "index";
			pg_query($this->conn, "END");
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	    }

	    $resultado['mensagem'] = $mensagem;
	    $resultado['acao'] = $acao;
	    return $resultado;

	}
    public function atualizaDados($segdescricao, $segoid)
	{

    	$resultado = array();

		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if(!$segdescricao){
	        	throw new Exception ("Preencher campos obrigatórios.");
	        }
	        
	        //verifica se a descição informada já esta cadastrada
	        $query_val = "  SELECT 
	                            COUNT(*) AS total 
	                        FROM 
	                            segmento 
	                        WHERE 
	                            segdescricao ILIKE upper('$segdescricao')";
	        if(!$sql_val = pg_query($this->conn, $query_val)){
	        	throw new Exception ("Houve um erro ao atualizar o registro.");
	        }
			
			if(pg_fetch_result($sql_val,0,'total') > 0){
				throw new Exception ("Registro já cadastrado.");
			}
			
			$query = "  UPDATE
							segmento
						SET
							segdescricao = upper('$segdescricao')
						WHERE
							segoid = '$segoid'
					";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao atualizar o registro.");
	        }
			

			$mensagem = "Registro atualizado com sucesso.";
			$acao = "index";
			pg_query($this->conn, "END");
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	    }

	    $resultado['mensagem'] = $mensagem;
	    $resultado['acao'] = $acao;
	    return $resultado;

	}


    public function inserirDados($segdescricao) {
        
    	$resultado = array();

		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if($segdescricao == ''){
	        	throw new Exception ("Preencher campos obrigatórios.");
	        }
	        
	        //verifica se a descição informada já esta cadastrada
	        $query_val = "  SELECT 
	                            COUNT(*) AS total 
	                        FROM 
	                            segmento 
	                        WHERE 
	                            segdescricao ILIKE upper('$segdescricao')";
	        if(!$sql_val = pg_query($this->conn, $query_val)){
	        	throw new Exception ("Houve um erro ao cadastrar o registro.");
	        }
			
			if(pg_fetch_result($sql_val,0,'total') > 0){
				throw new Exception ("Registro já cadastrado.");
			}
			
			$query = "  INSERT INTO segmento
									(segdescricao, segdt_cadastro, segusuoid_inclusao) 
								VALUES 
									(upper('$segdescricao'), NOW(), '$this->usuoid')";
			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao cadastrar o registro.");
	        }
			

			$mensagem = "Registro cadastrado com sucesso.";
			$acao = "index";
			pg_query($this->conn, "END");
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	    }

	    $resultado['mensagem'] = $mensagem;
	    $resultado['acao'] = $acao;
	    return $resultado;

        
    }
	
	
}