<?php

/**
 * @tutorial : Class que faz toda integração com banco de dados 
 * @author Ernando de castro <ernandocs@brq.com>
 * @version 1.0
 * @since 11:44 9/11/2013
 * @package SASCAR RelDaTransacoesDAO.php
 */
 
class RelDaTransacoesDAO {
	
	private $conn;
	
	/*
	 * Construtor
	*/
	public function RelDaTransacoesDAO($conn) {
		$this->conn = $conn;
	}
	/**
	 * @tutorial : lista todos tipo de combrança que esta com status True.
	 * @return:array
	 */
	 public function GetClassesParametros_banco(){
			
	 	try{
				if(!$this->conn){
					return 'ERRO. Conexão com banco de dados!';
				}else{
					$sql = "SELECT 
								cfbbanco, 
								cfbremessa, 
								cfbconvenio, 
								cfbformacobranca, 
								cfbnome, 
								cfbagencia, 
								cfbconta_corrente, 
								cfbtipo, 
								cfbagencia_convenio, 
								cfbconta_corrente_convenio, 
								forcoid,
								forcnome
							FROM 
								config_banco 
							INNER JOIN 
								forma_cobranca ON cfbformacobranca = forcoid
							WHERE 
								cfcarquivo_remessa = true
							AND 
								forcdebito_conta = true";							
					
					$rs = pg_query($this->conn, $sql);	
								
					$result = pg_fetch_all($rs);
					
				return $result;
				
			   }
			}catch (Exception $e) {
							
			  $mensagem = $e->getMessage();
			  return $mensagem;
			  
		    }
		 
	 }
	 
	 public function GetClassesPesquisarDados($dados){
	 	
	try{
		
		 	if($dados['clinome']){
		 		
				$where.= " AND c.clinome like '%".$dados['clinome']."%'";
			}
			if($dados['clitipo'] != 'todos'){		     
				 
				$where.= " AND c.clitipo ='".$dados['clitipo']."'";
			}
			if($dados['dataInicial_v']){
									
				$where.= " AND titdt_vencimento >='".$dados['dataInicial_v']."' AND titdt_vencimento <= '".$dados['dataFinal_v']."'";
			}
			if($dados['dataInicial_p']){
						  		   
				$where.= " AND titdt_pagamento >='".$dados['dataInicial_p']."' AND titdt_pagamento <= '". $dados['dataFinal_p']."'";			
			}
			if($dados['dataFinal_c']){		 
			  
			   $where.= " AND cliccadastro >= '".$dados['dataInicial_c']."' AND cliccadastro <= '".$dados['dataFinal_c'] ."'";
			}
			if($dados['banco'] != 'todos'){
				
				$where.= " AND  titformacobranca='".$dados['banco']."'";
			}
			if($dados['status'] == 1){				
				
				$where.= " AND t.titvl_pagamento IS NOT NULL AND t.titdt_pagamento IS NOT NULL ";
			}
			if($dados['status'] == 2){	      
					
				$where.= " AND (t.titvl_pagamento IS NULL or t.titvl_pagamento = 0) AND  t.titdt_pagamento IS NULL AND t.titno_remessa IS NOT NULL";
			}
			if($dados['status'] == 3)	{
				
				$where.= " AND (t.titvl_pagamento IS NULL or t.titvl_pagamento = 0) AND t.titdt_pagamento IS NULL AND t.titno_remessa IS NULL ";
			}
			
		$sql = "SELECT
					    c.clinome
					  , c.clitipo
					  , t.titdt_vencimento
					  , t.titdt_pagamento
					  , cc.cliccadastro
					  , t.titformacobranca 
					  , t.titvl_titulo
					  , t.titcod_retorno_deb_automatico
					  , CASE 
					      WHEN mre.mrdamensagem LIKE '%DÉBITO EFETUADO%' THEN ''
					      ELSE mre.mrdamensagem
				        END AS mrdamensagem	
					  , t.titvl_pagamento
					  , t.titdt_pagamento
					  , t.titno_remessa
					  , t.titoid
					  , CASE 
					     WHEN (titcod_retorno_deb_automatico = '00' OR titcod_retorno_deb_automatico = '31') AND titdt_pagamento IS NOT NULL THEN titdt_credito
					      ELSE
						  (SELECT max(l.ldaadt_inclusao) 
						       FROM log_debito_automatico_arquivo l 
						      WHERE l.ldaacodigo_retorno = t.titcod_retorno_deb_automatico 
							AND l.ldaatitoid = t.titoid)    
					    END::DATE AS dt_status				 
		         FROM titulo as t 
		   INNER JOIN clientes c ON c.clioid = t.titclioid
		   INNER JOIN cliente_cobranca cc ON cc.clicclioid = c.clioid
		   INNER JOIN forma_cobranca fc ON fc.forcoid =  t.titformacobranca
		    LEFT JOIN mensagem_retorno_debito_automatico mre ON	mre.mrdacodigo = t.titcod_retorno_deb_automatico	
			    WHERE 1=1
			          $where 
		          AND clicexclusao IS NULL 
		          AND forcdebito_conta = TRUE ";
		
			$qry = pg_query($this->conn, $sql);	
						
			$rows = array();
			while ($fch = pg_fetch_object($qry)) {
				$rows[] = $fch;
			}
				
			if($rows)	
			 	return $rows;
			else {
				return FALSE;
			}
			
		}catch (Exception $e) {						
		  $mensagem = $e->getMessage();
		  return $mensagem;		  
	    }  
	 }

	 
	 public function getDadosHistoricoDa($array_dados){
	 	
	 	try {
	 		
	 		$sql = "   SELECT  ldaatitoid
			                  ,ldaaobs 
			                  ,ldaacfbbanco
	 				          ,CASE  
					   		     WHEN ldaacfbbanco = 1 THEN 'Débito Automático Banco do Brasil'
							     WHEN ldaacfbbanco = 33 THEN 'Débito Automático Santander'
							     WHEN ldaacfbbanco = 237 THEN 'Débito Automático Bradesco'
							     WHEN ldaacfbbanco = 341 THEN 'Débito Automático Itaú'
							     WHEN ldaacfbbanco = 399 THEN 'Débito Automático HSBC'
							   ELSE ' '
							   END AS forma_cobranca	
			                  ,ldaadt_inclusao::DATE
			                  ,c.clinome
			                  ,c.clitipo
			                  ,cliccadastro::DATE
			                  ,t.titcod_retorno_deb_automatico
			             FROM log_debito_automatico_arquivo
			  LEFT OUTER JOIN titulo t ON t.titoid = ldaatitoid
			  LEFT OUTER JOIN clientes c ON c.clioid = t.titclioid
			  LEFT OUTER JOIN cliente_cobranca cc ON cc.clicclioid = c.clioid AND cc.clicexclusao IS NULL
			            WHERE ldaadt_inclusao::DATE >= '".$array_dados['dataInicial_ret']."'
			              AND ldaadt_inclusao::DATE <= '".$array_dados['dataFinal_ret']."' 
			              AND ldaaobs IS NOT NULL 
			              AND ldaaobs NOT LIKE '%Foi gerado o arquivo com Sucesso%' ";
	 		
	 		if($array_dados['formaCobranca'] != 'todos'){
	 			
	 		    $sql .=" AND ldaacfbbanco IN (SELECT forccfbbanco 
				                                FROM forma_cobranca 
				                               WHERE forcoid = ".$array_dados['formaCobranca']."  
				                                 AND forcdebito_conta IS TRUE )	";
	 		}
	 		
	 		$qry = pg_query($this->conn, $sql);
		 		
	 		$rows = array();
	 		while ($fch = pg_fetch_object($qry)) {
	 			$rows[] = $fch;
	 		}
	 		
	 		if($rows)
	 			return $rows;
	 		else {
	 			return FALSE;
	 		}
	 		
	 	} catch (Exception $e) {
	 		$mensagem = $e->getMessage();
	 		return $mensagem;
	 	}
	 }
	 
	 
	public function titformacobrancaBd($forcoid){
			
		$sql = "SELECT * FROM 
					forma_cobranca 
				WHERE 
					forcoid = $forcoid";		
		
		$qry = pg_query($this->conn, $sql);	
						
			$rows = array();
			while ($fch = pg_fetch_object($qry)) {
				$rows[] = $fch;
			}
				
			if($rows)	
			 	return $rows;
			else {
				return FALSE;
			}
	}
}
