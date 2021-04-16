<?php
/**
 * @file VerificaPosicionamentoVeiculoAgendadoDAO.php
 * @author Paulo Henrique da Silva Junior
 * @version 22/08/2013
 * @since 22/08/2013
 * @package SASCAR VerificaPosicionamentoVeiculoAgendadoDAO.php
 */

class VerificaPosicionamentoVeiculoAgendadoDAO{
	
	
	private $conn;
	
	// Construtor
	public function __construct($conn) {
	
		$this->conn = $conn;

	}
	
	public function verificarVeiculosAgendados(){

		try{
			
		    $sql =  "SELECT
		                vegoid, vegveioid, vegdt_ultposicao
		            FROM
		                veiculo_estatistica_gsm
		            WHERE 
		                vegstatus = 'A'
		                AND vegdt_ultposicao IS NOT NULL
		                AND vegmanutencao > now()
		            ORDER BY 
		                vegveioid DESC
		            ";
		    if (!$rs = pg_query($this->conn, $sql))
		    {
				throw new Exception (" Erro ao selecionar estatisticas do veiculo.");
		    }


		    require_once (_MODULEDIR_.'Relatorio/Action/RelEstatisticasGsm.class.php');
		    $Action = new RelEstatisticasGsm();
		    
        	if (pg_num_rows($rs) > 0){
			    while ($row = pg_fetch_object($rs)) {
			        $dataHoraUltimaPosicao = $Action->getDataHora($row->vegveioid);
			        $dataHoraUltimaPosicao = explode(' ', $dataHoraUltimaPosicao);
			        $dataUltimaPosicao = $dataHoraUltimaPosicao[0];
					$data_banco = implode('/', array_reverse(explode('-', $row->vegdt_ultposicao)));
			        if ($data_banco != $dataUltimaPosicao) {
			             $sql = "UPDATE 
			                            veiculo_estatistica_gsm 
			                        SET 
			                            vegmanutencao   = NULL,
			                            vegstatus       = 'P',
			                            vegusuoid       = '".$this->buscarCodigoUsuarioCron()."',
			                            vegdata='now()', 
			                            vegdt_ultposicao = '".$dataUltimaPosicao."'
			                        WHERE 
			                            vegveioid = ".$row->vegveioid;
			            if (!pg_query($this->conn, $sql))
					    {
							throw new Exception (" Erro ao alterar Data de Ultima Posição.");
					    }
			       	}
			    }
        	}
						
			return true;
			
		}catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
		
	}

	public function buscarCodigoUsuarioCron() {
    		
    	$sql = "
			SELECT 	cd_usuario
			FROM 	usuarios
			WHERE 	nm_usuario ILIKE 'automatico%'
			AND		dt_exclusao IS NULL
			LIMIT 1
		";
    
    	$rs =  pg_query($this->conn, $sql);
    
    	$row = pg_fetch_object($rs);
    		
    	$cd_usuario = isset($row->cd_usuario) ? $row->cd_usuario : 0;
    
    	return $cd_usuario;
    }

}