<?php

class CronEnviarArquivoAFTDAO {
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	public $conn;
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn){
		
		$this->conn = $conn;
	}
	

    public function buscarParametrosAFT($ambiente) {
    	
    	$sql= "SELECT pcsidescricao FROM parametros_configuracoes_sistemas
                    INNER JOIN parametros_configuracoes_sistemas_itens ON pcsoid = pcsipcsoid
                    WHERE pcsipcsoid = '".$ambiente['pcsipcsoid']."' AND pcsioid = '".$ambiente['pcsioid']."'";
        
        if(!$res = pg_query($this->conn,$sql)){
        	throw new exception('Falha ao busca forma de cobrança.');
        }
        
        if(pg_num_rows($res) > 0){
        	return pg_fetch_assoc($res);
        }
        return false;

    }

}
?>