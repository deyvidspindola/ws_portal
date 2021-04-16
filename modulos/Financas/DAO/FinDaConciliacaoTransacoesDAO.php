<?php
  /**
 * FinDaConciliacaoTransacoesDAO.php
 *
 * Script responsavel pra fazer a validação dos dados com banco de dados
 *
 *  @package FinDaConciliacaoTransacoesDAO
 *  @author  ernando de castro <ernandocs@brq.com>
 *  @since    23/10/2013 09:16
 *  @version 1.0
 */
   class FinDaConciliacaoTransacoesDAO {
       
	    private $conn;
		
		/*
		 * Construtor
		*/
		public function FinDaConciliacaoTransacoesDAO($conn) {
			$this->conn = $conn;
		}
		
	   /**
	    * Verificar se existe aquele banco cadastrado na base.
		* @var: codigo do banco
		* @return:true/false
	   */
	   public function verificaBanco($banco) {
		   		   	
			   	$sql = "SELECT 
							*
						FROM 
							config_banco 
						INNER JOIN 
							forma_cobranca ON cfbformacobranca = forcoid
						WHERE 
						   cfbbanco =  $banco";							
			 		
			    	$qry = pg_query($this->conn, $sql);
						
			    $rows = array();
				while ($fch = pg_fetch_object($qry)) {
					$rows[] = $fch;
				}
			
	 		if($rows){
	 			 return TRUE;
			}else{
				return FALSE;
			}
		  
	   }
	   
	   /**
	    * verifica se o banco esta ativo
	    * @return:true/false
	    * @var: codigo do banco
	    */
	   public function validarBancoAtivo($banco){
	   			   	
		   	$sql = "SELECT 
						*
					FROM 
						config_banco 
					INNER JOIN 
						forma_cobranca ON cfbformacobranca = forcoid
					WHERE 
						cfcarquivo_remessa = true
					AND 
						forcdebito_conta = true
					AND cfbbanco =  $banco";							
					
		    $qry = pg_query($this->conn, $sql);
					
		    $rows = array();
			while ($fch = pg_fetch_object($qry)) {
				$rows[] = $fch;
			}
			
	 		if($rows){
	 			 return TRUE;
			}else{
				return FALSE;
			}
	   }
	   
	   
	   /**
	    * Verifica se o cpf/cnpj informado no parâmetro está cadastrado na base de clientes
	    * retornado verdadeiro caso sim, e falso caso não. 
	    * 
	    * @param string $cpf_cnpj
	    * @throws Exception
	    * @return boolean
	    */
	   public function getCliente($cpf_cnpj){
	   		
	   	  try {
	   			
	   			if(empty($cpf_cnpj)){
	   				throw new Exception('O CPF/CNPJ para pesquisar o cliente deve ser informado.');
	   			}
	   			
	   			$cpf_cnpj = trim($cpf_cnpj);
	   			
	   			$sql = " SELECT clinome 
						   FROM clientes 
						  WHERE clino_cgc = '$cpf_cnpj'  OR clino_cpf = '$cpf_cnpj' OR clino_rg = '$cpf_cnpj' 
	   			            AND clidt_exclusao IS NULL ";
	   			   			
		   		if(!$rs = pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao efetuar a pesquisa do cliente pelo numero do CPF/CNPJ.');
		   		}
	   			
		   		if(pg_num_rows($rs) > 0) {
	   				return pg_fetch_all($rs);
	   			}
	   			
	   			return false;
	   			
	   		} catch (Exception $e) {
	   			return $e;
	   		}
	   }
	   
	   
	   /**
	    * Grava log conm os dados lidos do arquivo de retorno do banco
	    * 
	    * @param object $dadosLog
	    * @throws Exception
	    * @return boolean|Exception
	    */
	   public function setLogDebitoAutomatico($dadosLog){
			
	   		try {
		   			
	   			if(!is_object($dadosLog)){
	   				throw new Exception('Os dados para inserção de log do débito automático devem ser informados.');
	   			}
	  
	   			$campo = "";
	   			$valor = "";
	   			
	   			//inclui campos adicionais
	   			if($dadosLog->titoid != NULL){
	   				$campo .= ", ldaatitoid";
	   				$valor .= ','.$dadosLog->titoid;
	   			}
	   			
	   			if($dadosLog->nsa != NULL){
	   				$campo .= ", ldaansa";
	   				$valor .= ','.$dadosLog->nsa;
	   			}
	   			
	   			if($dadosLog->cod_retorno != NULL){
	   				$campo .= ", ldaacodigo_retorno";
	   				$valor .= ','."'$dadosLog->cod_retorno'";
	   			}
	   			
	   			if($dadosLog->observacao != NULL){
	   				$campo .= ", ldaaobs";
	   				$valor .= ','."'$dadosLog->observacao'";
	   			}
	   			
	   			$sql = " INSERT  INTO log_debito_automatico_arquivo (
									       ldaadt_arquivo
									     , ldaadt_operacao
									     , ldaacfbbanco
									      $campo
								       ) 
								     
								VALUES (
									      '$dadosLog->data_arquivo'
									     ,'$dadosLog->data_operacao' 
									     , $dadosLog->num_banco
									      $valor
								       ) ";
	
	   			
	   			if(!pg_query($this->conn, $sql)){
	   				throw new Exception('Falha ao inserir log do debito automatico.');
	   			}
	   			
	   			return true;
	   			
	   		} catch (Exception $e) {
	   			return $e;
	   		}

	   }
	   
	   /**
	    * Recupera dados do título 
	    * 
	    * @param int $titoid
	    * @throws Exception
	    * @return multitype:|boolean|Exception
	    */
	   public function getDadosTitulo($titoid){

		   	try {
	
		   		if(empty($titoid)){
		   			throw new Exception('O número do título deve ser informado para recuperar os dados.');
		   		}
	
		   		$sql = "  SELECT titvl_titulo
		   						, tO_CHAR(titdt_credito,'dd/mm/yyyy') titdt_credito
		   					FROM titulo
		   				   WHERE titoid = $titoid ";
	
		   		if(!$rs = pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao efetuar a pesquisa dos dados do título.');
		   		}
	
		   		if(pg_num_rows($rs) > 0) {
		   			return pg_fetch_all($rs);
		   		}
	
		   		return false;
	
		   	} catch (Exception $e) {
		   		return $e;
		   	}
	   }
	   
	   
	   /**
	    * Caso o código de retorno seja diferente de 00 e 31
	    * atualiza o título com o código do arquivo
	    *
	    * @param int $codRetorno
	    * @return unknown
	    */
	   public function setTituloCodRetorno($codRetorno, $titoid){
	   		
		   	try {
		   	
		   		if($codRetorno == NULL){
		   			throw new Exception('O código de retorno deve informado para inserir no titulo.');
		   		}
		   		
		   		if(empty($titoid)){
		   			throw new Exception('O código de retorno deve informado para inserir no titulo.');
		   		}
	
		   		$sql = " UPDATE titulo 
						 SET titcod_retorno_deb_automatico = '".$codRetorno."'
						 WHERE titoid = $titoid ";
		   	
		   		 
		   		if(!pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao inserir código do retorno no título.');
		   		}
	
		   		return true;
		   		 
		   	} catch (Exception $e) {
		   		return $e;
		   	}
	   }
	   
	   /**
	    * Efetua a baixa do título de acordo as regras na Action
	    * 
	    * @param object $dadosBaixa
	    * @throws Exception
	    * @return boolean|Exception
	    */
	   public function setBaixarTitulo($dadosBaixa){

		   	try {
		   		
		   		if(!is_object($dadosBaixa)){
		   			throw new Exception('O dados para baixar o título devem ser informados.');
		   		}
	
		   		$sql =" UPDATE titulo 
						   SET titdt_pagamento         = '$dadosBaixa->data_operacao'
						     , titdt_credito           = '$dadosBaixa->data_credito_cc'
						     , titvl_pagamento         = $dadosBaixa->total_recebido
						     , titrecebimento          = 'BANCO'
						     , titobs_recebimento      = '$dadosBaixa->obs_recebimento'
						     , titcfbbanco             = $dadosBaixa->cod_banco
						     , titvl_juros             = $dadosBaixa->vl_encargo
						     , titcod_retorno_deb_automatico = '".$dadosBaixa->cod_retorno."'
						 WHERE titoid = $dadosBaixa->titoid ";
		   		 
		   		if(!pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao baixar título.');
		   		}
		   		 
		   		return true;
		   		 
		   	} catch (Exception $e) {
		   		return $e;
		   	}
	   	 
	   }
	   
	   /**
	    * Retorna dados de movimentação bancária do dia
	    * 
	    * @throws Exception
	    * @return multitype:|boolean|Exception
	    */
	   public function getMovimentacaoBancaria($num_banco, $data_credito_cc){
	   	
		   	try {
		   	
			   		$sql = "    SELECT tp.tmboid
					                  , tp.tmbplcoid
					                  , tp.tmbhistorico
					                  , mv.mbcooid
					             FROM tp_movim_banco  tp
					  LEFT OUTER JOIN movim_banco mv ON mv.mbcotmboid = tp.tmboid  AND mv.mbcodata::DATE = '$data_credito_cc'::DATE AND mv.mbcocfboid = $num_banco AND mv.mbcotipo = 'E'
					            WHERE tp.tmbcodigo  = 'C_DEBITO'
					              AND mv.mbcooid IS NULL  ";
		   	
		   		if(!$rs = pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao pesquisar movimentação bancária.');
		   		}
		   	
		   		if(pg_num_rows($rs) > 0) {
		   			return pg_fetch_all($rs);
		   		}
		   	
		   		return false;
		   	
		   	} catch (Exception $e) {
		   		return $e;
		   	}
	   }
	   
	   
	   public function setMovimentacaoBancaria($dados){
	   	
		   	try {
		   	
		   		if(!is_object($dados)){
		   			throw new Exception('Os dados para inserir a movimentação bancária devem ser informados.');
		   		}
		   		
		   		$valores= " 
			   		\"$dados->cod_banco\" 
			   		\"$dados->data_credito_cc\" 
			   		\"$dados->tipo_movi\" 
			   		\"NULL\" 
			   		\"$dados->historico\" 
			   		\"$dados->valor_total\" 
			   		\"$dados->plano_contabil\" 
			   		\"NULL\" 
			   		\"NULL\" 
			   		\"NULL\" 
			   		\"NULL\" 
			   		\"$dados->forma_cobranca\" 
			   		\"$dados->cod_usuario\" 
			   		\"NULL\" 
			   		\"$dados->mbcotecoid\" 
			   		\"$dados->mbcoftcoid\" 
			   		\"$dados->mbcodepoid\" 
			   		\"\" 
			   		\"\" 
			   		\"\" 
		   		";
		   		
		   		$sql = "SELECT movim_banco_i('$valores');";
		   		
		   		if(!$rs = pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao inserir movimentação bancária.');
		   		}
		   	
		   		if(pg_num_rows($rs) > 0) {
		   			return true;
		   		}
		   	
		   		return false;
		   	
		   	} catch (Exception $e) {
		   		return $e;
		   	}
	   	
	   }
	   
	   /**
	    * Atualiza com o último nsa de retorno nas configurações do bamco 
	    * 
	    * @param int $banco
	    * @param int $nsa
	    * @throws Exception
	    * @return boolean|Exception
	    */
	   public function atualizarRetornoConfigBanco($banco, $nsa){
	   	
		   	try {
		   		 
		   		if(empty($banco)){
		   			throw new Exception('O número do banco deve ser informado para atualizar o retorno da configuração do banco.');
		   		}
		   		
		   		if(empty($nsa)){
		   			throw new Exception('O nsa deve ser informado para atualizar o retorno da configuração do banco.');
		   		}
		   	
		   		$sql ="  UPDATE config_banco 
						    SET cfbretorno = $nsa
						  WHERE cfbbanco   = $banco ";
		   	
		   		if(!pg_query($this->conn, $sql)){
		   			throw new Exception('Falha ao atualizar número de retorno da configuração do banco.');
		   		}
	
		   		return true;

		   	} catch (Exception $e) {
		   		return $e;
		   	}
	   }
	   
	
	 /**
	  * Pega o diretorio
	  */
	 public function diretorioDao($cfbbanco){
	 	
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
		
		if ($rows[0]->laycaminho !="") {
			
			return $rows[0]->laycaminho;
		}else{
		   return '';	
		}
			
		
	 }

   }
   