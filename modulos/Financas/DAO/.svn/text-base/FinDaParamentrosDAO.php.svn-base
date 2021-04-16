<?php
/**
 * @tutorial : Class que faz toda integração com banco de dados 
 * @author Ernando de castro <ernandocs@brq.com>
 * @version 1.0
 * @since 18/10/2013 09:48
 * @package SASCAR FinDaParamentrosDAO.php
 */
 
class FinDaParamentrosDAO {	
	
	private $conn;
	
	/*
	 * Construtor
	*/
	public function FinDaParamentrosDAO($conn) {
		$this->conn = $conn;
	}
	/**
	 * @tutorial : lista todos tipo de combrança que esta com status True.
	 * @return:array
	 */
	public function forma_cobranca() {
		try {			
		
				$sql = "SELECT 
							cfbbanco,forcnome, cfcarquivo_remessa
						FROM 
							forma_cobranca
						LEFT JOIN 
							config_banco 
								ON 
								cfbformacobranca = forcoid
						WHERE 
							forcdebito_conta = TRUE 
						order by forcnome ASC ";
							
				$rs = pg_query($this->conn, $sql);				
				$result = pg_fetch_all($rs);
				
				return $result;
		
		
		} catch (Exception $e) {
			
			$mensagem = $e->getMessage();
		}
		
	}/**
	 * @tutorial : atualiza a formula de combrança 
	 * @param forcoid, tipo de remessa T = true, F = false
	 *  
	 */
	public function update_forma_cobranca($cfbbanco = null,$cfcarquivo_remessa = null){
		try{
									 
			$sql = "UPDATE
						config_banco
			       SET
			         cfcarquivo_remessa = '$cfcarquivo_remessa'";
			         if ($cfbbanco > 0) {
						$sql .= " WHERE
				            cfbbanco = $cfbbanco";
					  }
			//print $sql;						 		
		 $rs = pg_query($this->conn, $sql);
			
			if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao Update operação.");
            }
            
            return TRUE;
			
		}catch (Exception $e) {
			
			$mensagem = $e->getMessage();
		}
		
	}
	/**
	 * @tutorial:Pega data remessa disponivel na tabela parametros_debito_automatico
	 * @return:array
	 */
	public function fetch_all_parametros_debito($pdames_referencia = NULL, $pdadt_fim_faturamento = NULL,$pdadt_inicio_faturamento = NULL, $pdadt_email_aviso = NULL){
		
		//die(print 'metodo'.$pdames_referencia .','. $pdadt_fim_faturamento .','.$pdadt_inicio_faturamento );
		try{
			$sql ="SELECT  
							pdaoid,
							date_part('day',pdadt_envio_arquivo) as pdadt_envio_arquivo,
							date_part('day',pdadt_inicio_faturamento) AS pdadt_inicio_faturamento,
							date_part('day',pdadt_fim_faturamento) AS pdadt_fim_faturamento,
							pdames_referencia,
							pdadt_email_aviso
					FROM
						parametros_debito_automatico";
						
			if ($pdadt_fim_faturamento != NULL and $pdadt_inicio_faturamento != NULL) {
				$sql .= " WHERE 
							pdames_referencia = '$pdames_referencia' 
						AND 
							date_part('day', pdadt_fim_faturamento) = $pdadt_fim_faturamento
							
						AND 
							date_part('day', pdadt_inicio_faturamento) = $pdadt_inicio_faturamento
						AND 
							pdadt_email_aviso = '$pdadt_email_aviso'";
			}	
				//echo $sql."<br>";	
								
			$rs = pg_query($this->conn, $sql);			
			
			$rows = array();
			while ($fch = pg_fetch_object($rs)) {
				$rows[] = $fch;
			}
			
			//print "<pre>";
			//print_r($rows);
			
			//$result = pg_fetch_all($rs);				
			return $rows;
				
		} catch(Exception $e){
			
			$mensagem = $e->getMessage();
		}
	}

	/**
	 * @tutorial Metedo para deletar os parametros_debito_automatico
	 * @param id a ser deletado
	 */
	public function del($id = null){
		
		try{
				$sql ="DELETE FROM
						parametros_debito_automatico";
				if($id != null){
					$sql .=" WHERE
							  pdaoid = $id";
				}				
				
			$rs = pg_query($this->conn, $sql);	
			
			if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao Delete operação.Metodo: parametros_debito_automatico");
            }	
            					
			return TRUE;
				
		} catch(Exception $e){
			
			$mensagem = $e->getMessage();
		}
	}
	/**
	 * @tutorial Metodo pra atulizar os parametros_debito_automatico
	 * @param array de dados a ser atualizado
	 */
	public function update($id,$parametro_select_dataenvio,$parametro_select_datainicial,$parametro_select_datafinal,$parametro_select_mes){
				
	  try{
			$sql ="UPDATE 
						parametros_debito_automatico
					SET
						pdadt_envio_arquivo = to_date('$parametro_select_dataenvio', 'DD'),
						pdadt_inicio_faturamento  = to_date('$parametro_select_datainicial', 'DD'),
						pdadt_fim_faturamento = to_date('$parametro_select_datafinal', 'DD'),
						pdames_referencia = '$parametro_select_mes'
					WHERE
						pdaoid = $id";
				
			//$rs = pg_query($this->conn, $sql);	
			
			if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao Update operação. Metodo:parametros_debito_automatico");
            }  
									
			return TRUE;
				
		} catch(Exception $e){
			
			$mensagem = $e->getMessage();
		}
		
	}
	 /***
	  * @tutorial Metodo pra inserir parametros_debito_automatico do dados
	  * @param array de dados a sera inserido
	  */
	public function insert($bancos,$pdadt_envio_arquivo,$pdadt_inicio_faturamento,$pdadt_fim_faturamento,$pdames_referencia,$pdadt_email_aviso = null){
		try{
			
			$bancoarray = "{".$bancos."}";
			$this->LogParametros($bancoarray,$_SESSION['usuario']['oid'],$pdadt_envio_arquivo, $pdadt_inicio_faturamento, $pdadt_fim_faturamento, $pdames_referencia, $pdadt_email_aviso);
			
			$sql ="INSERT INTO parametros_debito_automatico
						(pdadt_envio_arquivo,pdadt_inicio_faturamento,pdadt_fim_faturamento,pdames_referencia,pdadt_email_aviso)
					VALUES
						(to_date('$pdadt_envio_arquivo', 'DD'),to_date('$pdadt_inicio_faturamento', 'DD'),to_date('$pdadt_fim_faturamento', 'DD'),'$pdames_referencia','$pdadt_email_aviso')";								
			//echo $sql;
            $rs = pg_query($this->conn, $sql);
            
            if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao Insert operação. metodo: insert.");
            }             
		    pg_query($this->conn, "COMMIT");	
			
			 $banco = $this->forma_cobranca();
			 $dados = $this->fetch_all_parametros_debito();	
			   
			 $result = array('banco' => $banco, 'parametros' =>$dados,'result' => TRUE);	
								
			return $result;
		    					
			//return TRUE;
				
		} catch(Exception $e){
			
			pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();           			
			return FALSE;
		}	
	}
	
	protected function LogParametros($bancos,$lpdapclioid_alteracao,$pdadt_envio_arquivo,$pdadt_inicio_faturamento,$pdadt_fim_faturamento,$pdames_referencia,$pdadt_email_aviso)
	{
					
	 	 try{
			$date = date("Y-m-d");		
			$sql = "INSERT INTO log_parametros_debito_automatico
						(lpdapclioid_alteracao, lpdapdt_alteracao,lpdapdt_envio,lpdapbancos_ativos, lpdapdt_inicio_faturamento, lpdapdt_fim_faturamento, lpdapmes_referencia, lpdaemail_aviso)
					VALUES
						($lpdapclioid_alteracao,'$date',to_date('$pdadt_envio_arquivo', 'DD'),'$bancos',to_date('$pdadt_inicio_faturamento', 'DD'),to_date('$pdadt_fim_faturamento', 'DD'),'$pdames_referencia','$pdadt_email_aviso')";
					//echo $sql;
						
				$rs = pg_query($this->conn,$sql);
	            
	            if(pg_affected_rows($rs) == 0) {
	                throw new Exception ("Erro ao Insert operação. metodo: LogParametros.");
	            }             
			    pg_query($this->conn, "COMMIT");	
			    					
				return TRUE;
			
		} catch(Exception $e){
			
			pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();           			
			return FALSE;
		}	
		 
	}
	
	
}