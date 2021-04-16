<?php


class FinCreditoFuturoParametrizacaoTipoCampanhaDAO{
    
    /**
     * Conexão com o 
     * @var connection  
     */
    private $conn;
    
    private $parametros;
    
    public function __construct($conn) {
    	$this->parametros = new stdClass();
        $this->conn = $conn;        
    }
    
    /**
     * Método Cadastrar
     * @param stdClass $parametros
     * @throws Exception
     * @return boolean
     */
    public function cadastrar(stdClass $parametros) {
    	
    	$this->parametros->descricao = isset($parametros->descricao) ? $parametros->descricao : "";
    	
    	if (empty($this->parametros->descricao)) {
    		throw new Exception('Existem campos obrigatórios não preenchidos.');
    	}
    	
    	$sql = "INSERT INTO 
    						credito_futuro_tipo_campanha
    						(cftpdescricao)
    			VALUES
    						('".$this->parametros->descricao."')";
    	
    	if ($resultado = pg_query($sql)) {
    		if (pg_affected_rows($resultado) > 0) {
    			return true;
    		}
    	}
    	return false;
    }
    
    /**
     * Método Pesquisar
     * @param stdClass $parametros
     * @return array:object
     */
    public function pesquisar(stdClass $parametros) {
    	$resultadoPesquisa = array();
    	$this->parametros->descricao = isset($parametros->descricao) ? $parametros->descricao : "";
    	$sql = "SELECT
    					*
    			FROM
    					credito_futuro_tipo_campanha
                WHERE
                        cftpdt_exclusao IS NULL";
    	if (!empty($this->parametros->descricao)) {
    		$sql .= " AND LOWER(TRANSLATE(cftpdescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC')) ILIKE '%".$this->parametros->descricao."%'";
    	}
    	$sql .= " ORDER BY cftpdescricao ASC";
    	if ($resultado = pg_query($sql)) {
    		if (pg_num_rows($resultado) > 0) {
    			while ($objeto = pg_fetch_object($resultado)) {
    				$resultadoPesquisa[] = $objeto;
    			}
    		}
    	}
    	return $resultadoPesquisa;
    }
    
    /**
     * Verificar Existência Cadastro
     * Verifica se a descrição já está cadastrada
     * @param stdClass $parametros
     * @return number
     */
    public function verificarExistenciaDescricao(stdClass $parametros) {
    	$this->parametros->descricao = isset($parametros->descricao) ? strtolower($parametros->descricao) : "";
    	
    	$sql = "SELECT 
    					COUNT(1) as total
    			FROM
    					credito_futuro_tipo_campanha
    			WHERE
                        cftpdt_exclusao IS NULL
                AND
    					LOWER(TRANSLATE(cftpdescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC')) ILIKE '".$this->parametros->descricao."'";
    	if ($resultado = pg_query($sql)) {
    		$res = pg_fetch_object($resultado);
    		return $res->total;
    	}
    	return 0;
    }
    
    /**
     * Verificar se a campanha está sendo utilizada
     * @param stdClass $parametros
     * @return number
     */
    public function verificarCampanha(stdClass $parametros) {
    	$this->parametros->id = isset($parametros->id) ? $parametros->id : "";
    	
    	$sql = "SELECT 
    					COUNT(1) as total
    			FROM
    					credito_futuro_campanha_promocional
    			WHERE
                        cfcpdt_exclusao IS NULL
                AND
    					cfcpcftpoid = ".$this->parametros->id;
    	if ($resultado = pg_query($sql)) {
    		$res = pg_fetch_object($resultado);
    		return $res->total;
    	}
    	return 0;
    }
    
    /**
     * Excluir
     * @param int $id
     * @return boolean
     */
    public function excluir(stdClass $parametros) {
    	$this->parametros->id = isset($parametros->id) ? $parametros->id : "";
    	$this->parametros->usuario = isset($parametros->usuario) ? $parametros->usuario : "";
    	
    	if (empty($this->parametros->id) || empty($this->parametros->usuario)) {
    		throw new Exception('Existem campos obrigatórios não preenchidos.');
    	} else {
    		$sql = "UPDATE
    							credito_futuro_tipo_campanha
    				SET
    							cftpdt_exclusao = NOW(),
    							cftpusuoid_exclusao = " . $this->parametros->usuario . "
    				WHERE
    							cftpoid = " . $this->parametros->id;
            if ($resultado = pg_query($sql)) {
                return true;
            }
    		return false;
    	}
    }
    
}
        
