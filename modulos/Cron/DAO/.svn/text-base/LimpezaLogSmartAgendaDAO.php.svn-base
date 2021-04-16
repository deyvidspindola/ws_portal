<?php

class LimpezaLogSmartAgendaDAO {

	private $conn;

    public function __construct($conn) {
    	$this->conn = $conn;
    }

    /**
     * Busca quantidade de dias parametrizados para fazer a limpeza do log
     * @return [type] [description]
     */
    public function quantidadeDias() {

    	$retorno = new stdClass();

    	try {

	    	$sql = "SELECT 
		                pcsidescricao AS qtd
				   	FROM 
				   		parametros_configuracoes_sistemas_itens    
			     	INNER JOIN 
			     		parametros_configuracoes_sistemas ON pcsoid = pcsipcsoid  
				  	WHERE pcsipcsoid = 'SMART_AGENDA' 
					AND pcsioid = 'LIMPEZA_LOG' 
				    AND pcsidt_exclusao IS NULL
					AND pcsdt_exclusao IS NULL LIMIT 1";

			$retorno->sql = $sql;

			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception("Erro ao realizar a busca da parametrizacao de registros.");
			} else if(pg_num_rows($rs) == 0) {
				throw new Exception("A consulta da parametrizacao nao retornou registros.");
			}

			$retorno->resultado = pg_fetch_object($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage();
		}

		return $retorno;
    }

    /**
     * Realiza limpeza de log
     * @param  [string] $data [Data a partir da qual os registros serão removidos]
     * @return [type]       [description]
     */
    public function limpaLog($data) {

    	$retorno = new stdClass();

    	try {

	    	$sql = "DELETE FROM smartagenda_log_comunicacao WHERE slcdt_inclusao::DATE <= '" . $data ."'";

	    	$retorno->sql = $sql;
			
			if(!pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao realizar limpeza do log.");
			}

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage();
		}

		return $retorno;

    }
}