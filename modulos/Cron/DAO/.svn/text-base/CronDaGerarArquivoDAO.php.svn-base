<?php
  /**
   * 
   */
  class CronDaGerarArquivoDAO {
      
    private $conn;
	
	/*
	 * Construtor
	*/
	public function CronDaGerarArquivoDAO($conn) {
		$this->conn = $conn;
	}
	
	/**
	 * @tutorial : pega todos os bancos ativando pelo admin
	 * @return:array
	 */
	 
	 public function bancos() {		 	
			
			try{
		 		$sql = "SELECT cfbbanco
							 , cfbremessa
							 , cfbconvenio
							 , cfbformacobranca
							 , cfbnome
							 , cfbagencia
							 , cfbconta_corrente
							 , cfbtipo
							 , cfbagencia_convenio
							 , cfbconta_corrente_convenio
							 , forcoid
							 , forcnome
						  FROM config_banco 
						 INNER JOIN forma_cobranca ON cfbformacobranca = forcoid
						 WHERE cfcarquivo_remessa = true
						   AND forcdebito_conta = true";							
			
				$qry = pg_query($this->conn, $sql);	
							
				$rows = array();
				while ($fch = pg_fetch_object($qry)) {
					$rows[] = $fch;
				}
			
				return $rows;					
			   
			}catch (Exception $e) {
							
			  $mensagem = $e->getMessage();
			  return $mensagem;
			  
		    }
	  }
	  
	 /**
	  * @tutorial : pega todos parametros da base parametros_debito_automatico	
	  * 
	  * @return:array
	  */	 
     public function parametrosDebitos(){
 	
		try{
			$sql = "SELECT 
							pdaoid,
							date_part('day',pdadt_envio_arquivo) as pdadt_envio_arquivo,
							date_part('day',pdadt_inicio_faturamento) AS pdadt_inicio_faturamento,
							date_part('day',pdadt_fim_faturamento) AS pdadt_fim_faturamento,
							pdames_referencia,
							pdadt_email_aviso		
					 FROM 
							parametros_debito_automatico";
			
			$rs = pg_query($this->conn, $sql);				
			$result = pg_fetch_all($rs);
				
				return $result;
			
			}catch (Exception $e) {
							
			  $mensagem = $e->getMessage();
			  return $mensagem;
		    } 
	 }
	 
	 /**
	  * @tutorial :Consulta extraída do Delphi e adaptada
	  */
	public function consultaExtraida($pdadt_inicio_faturamento ,$pdadt_fim_faturamento,$pdames_referencia,$forcoid)	{
				
		$data = Date('Y-m-d');
		$arrayData = explode('-', $data);
		
		$ano = $arrayData['0']; 
		$mes = $arrayData['1'];
		$dia = $arrayData['2'];
			
		if($pdames_referencia == "A"){
			
			$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));		
		
			if($pdadt_inicio_faturamento > $ultimo_dia){
				$pdadt_inicio_faturamento = $ultimo_dia;
			}
			
			if($pdadt_fim_faturamento > $ultimo_dia){
				$pdadt_fim_faturamento = $ultimo_dia;
			}
		
			$pdadt_inicio = $pdadt_inicio_faturamento . '/' . $mes . '/' . $ano;		
		    $pdadt_fim    = $pdadt_fim_faturamento    . '/' . $mes . '/' . $ano;
		
		}elseif ($pdames_referencia == "S") {	
		
			if($mes == 12){
				$mes = '01';
				$ano = $ano + 1;
			} else {
				$mes = $mes + 1;
			}

			$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));				
			
			if($pdadt_inicio_faturamento > $ultimo_dia){
				$pdadt_inicio_faturamento = $ultimo_dia;
			}
			
			if($pdadt_fim_faturamento > $ultimo_dia){
				$pdadt_fim_faturamento =$ultimo_dia;
			}
								
			$pdadt_inicio = $pdadt_inicio_faturamento . '/' . $mes . '/' . $ano;		
		    $pdadt_fim    = $pdadt_fim_faturamento    . '/' . $mes . '/' . $ano;
		}
		
		try{
			
		$sql = "SELECT clioid
					 , clinome
					 , titdt_vencimento
					 , titvl_titulo
					 , ( titvl_titulo 
					     + coalesce(titvl_multa, 0) 
					     + coalesce(titvl_juros, 0) 
					     - coalesce(titvl_desconto, 0) 
					     - coalesce(titvl_ir, 0) 
					     - coalesce(titvl_iss, 0) 
					     - coalesce(titvl_piscofins, 0 ) ) as valor_corrigido
					 , nflno_numero
					 , nflserie
					 , clicc_bancaria
					 , clitipo
					 , titvl_acrescimo
					 , titemissao
					 , clicagencia
					 , clicconta::text
					 , clino_cpf
					 , clino_cgc
					 , titoid
					 , titno_remessa
					 , titvl_desconto
					 , clino_rg
					 , titvl_ir
					 , titvl_piscofins
					 , titvl_iss
					 , case when clitipo='F' then clirua_res else clirua_com end as clirua
					 , case when clitipo='F' then clino_res else clino_com end as clino
					 , case when clitipo='F' then cliuf_res else cliuf_com end as cliuf
					 , case when clitipo='F' then clicompl_res else clicompl_com end as clicompl
					 , case when clitipo='F' then clicidade_res else clicidade_com end as clicidade
					 , case when clitipo='F' then clino_cep_res else clino_cep_com end as clino_cep
					 , case when clitipo='F' then clibairro_res else clibairro_com end as clibairro
				  FROM titulo
				 INNER JOIN clientes ON titclioid = clioid
				 INNER JOIN forma_cobranca ON titformacobranca = forcoid
				 INNER JOIN cliente_cobranca ON	clicclioid = clioid
				  LEFT JOIN nota_fiscal ON titnfloid = nfloid 
				 WHERE nfldt_cancelamento is null
				   AND titdt_pagamento is null
				   AND titdt_vencimento >= '$pdadt_inicio' 
				   AND titdt_vencimento <= '$pdadt_fim'
				   AND titformacobranca IN ($forcoid)
				   AND forcdebito_conta = true 
				   AND clicexclusao is null
				   AND titdt_cancelamento is null 
				   AND (titvl_titulo+coalesce(titvl_acrescimo,0)-coalesce(titvl_desconto,0)) > 0
				   AND titno_remessa IS NULL";
				   
		$qry = pg_query($this->conn, $sql);	
					
		$rows = array();
			while ($fch = pg_fetch_object($qry)) {
				$rows[] = $fch;
			}
			
		return $rows;
			
		}catch (Exception $e) {
						
		  $mensagem = $e->getMessage();
		  return $mensagem;
		  
	    } 
	 }
	 
	 /**
	  * receber um array de valor.
	  */
	 public function logDebitoAutomatico($value){	 	
	 	
	 	    $ldaacfbbanco = $value['ldaacfbbanco']; 
			$cfbremessa = $value['ldaanumero_remessa']; 
        	$ldaadt_remessa = $value['ldaadt_remessa'];
        	$ldaaobs =  $value['ldaaobs'];
        	
        	$sql = "INSERT INTO 
        				log_debito_automatico_arquivo
		 			    	(ldaansa, ldaadt_arquivo, ldaaobs, ldaacfbbanco)
		  		 	 VALUES
		  		 	 		($cfbremessa,'$ldaadt_remessa','$ldaaobs', $ldaacfbbanco)";
		  
			$rs = pg_query($this->conn, $sql);	
				
			if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao Insert operação.Metodo: logDebitoAutomatico");
            }		          
			  					
			return TRUE;
	    }
		
	 // atualiza a tabela de bacos
	 public function atualizaBancos($value){		 
	 			
			$cfbremessa = $value['cfbremessa']; 
	    	$cfbbanco = $value['banco'];
	    	
	    	$sql ="UPDATE 
						config_banco
					SET
						cfbremessa = $cfbremessa
					WHERE
						cfbbanco = $cfbbanco";
		  			
			$rs = pg_query($this->conn, $sql);	
				
			if(pg_affected_rows($rs) == 0) {
	            throw new Exception ("Erro ao Insert operação.Metodo: logDebitoAutomatico");
	        }					  					
			return TRUE;	 			
	 	
	 }
	 
	 public function diretorioDao($cfbbanco){
	 	try{
	 		$sql = "SELECT 
	 					laycaminho 
	 				FROM 
	 					layout_arquivo 
	 				WHERE 
	 					laypseudonimo = '$cfbbanco'";
			
		 $qry = pg_query($this->conn, $sql);	
					
		$rows = array();
			while ($fch = pg_fetch_object($qry)) {
				$rows[] = $fch;
			}
			
		return $rows;
			
		}catch (Exception $e) {
						
		  $mensagem = $e->getMessage();
		  return $mensagem;
		  
	    } 
	 }
	 public function tituloDao($value=''){
	 	
  		$cfbremessa = $value['cfbremessa']; 
        	$titoid = $value['titoid'];
        	
        	$sql ="UPDATE 
						titulo
					SET
						titno_remessa = $cfbremessa,
						titemissao = now()
					WHERE
						titoid = $titoid";
			
			$rs = pg_query($this->conn, $sql);	
				
			if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao Insert operação.Metodo: logDebitoAutomatico");
            }	
              					
		return TRUE;
		
	 }
  }
?>