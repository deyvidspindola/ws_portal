<?php
// require para persistência de dados - classe DAO
require _MODULEDIR_ . 'Financas/DAO/FinDaParamentrosDAO.php';


class FinDaParamentros {
	
	/**
	 * Atributo para acesso a persistência de dados
	 */
	private $dao;
	private $conn;	
		
	/**
	 * Construtor
	 */
	public function __construct() {
	
		global $conn;
	
		$this->conn = $conn;
	
		/**
		 * Objeto - DAO
		 */
		$this->dao = new FinDaParamentrosDAO($conn);
	}
		
	 public function view($action, $resultadoPesquisa = array(),$layoutCompleto = true) {
        
		/** / print_r(); */
		if (!empty($_POST)) {			
			$retorno = $this->validarDados($_POST);	
			
			if ($retorno['tipo'] == "sucesso") {
				//echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=fin_da_parametros.php'>";
			}
			
		}
		if (!empty($_GET['excluir'])) {
						
			$result = $this->limpaDadosdel($_GET['excluir']);
			
			if ($result) {				
				$retorno = array('msg' =>"Registro excluido com sucesso.",'tipo' => 'sucesso');
			    echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=fin_da_parametros.php'>";				
			 }else{
			 	$retorno = array('msg' =>"Erro ao incluído o Registro.",'tipo' => 'Erro');
				echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=fin_da_parametros.php'>";				
			}			
		}		
		
        if($action != 'cadastro' && $action != 'index' && $action != 'principal'){
        	header('Location: ?acao=principal');
            exit;    	   
        }else{

        	if($layoutCompleto)
        		include _MODULEDIR_.'Financas/View/fin_da_paramentros/header.php';        	
        	
    
        	if($action == 'cadastro')
        		include _MODULEDIR_.'Financas/View/fin_da_paramentros/index.php';
        
        	
        	    include _MODULEDIR_.'Financas/View/fin_da_paramentros/'.$action.'.php';
        
        	if($layoutCompleto)
        		include _MODULEDIR_.'Financas/View/fin_da_paramentros/footer.php';
    	}
    }
	 
	public function index() {
    	
		$param['parametros_debito'] = $this->GetClassesParametros_debito();
    	$param['comboClassesFormaCobranca'] = $this->getClassesFormaCobranca();		
		
    	
    	$this->view('index', $param, true, false);
    }
	  
	public function getClassesFormaCobranca() {
    	
        return $this->dao->forma_cobranca();
        
    }
	
	public function GetClassesRemessa(){
		
		return $this->dao->fetch_all_parametros_debito();
	}
	
	public function GetClasseDel($id){
		
		return $this->dao->del($id);
	}
	
	public function validarDados($dados='')	{		 
		     
			    $dataenvio = $dados['parametro_select_dataenvio'];
				$datainicial = $dados['parametro_select_datainicial'];
				$datafinal = $dados['parametro_select_datafinal'];
				$mes = $dados['parametro_select_mes'];
				$emaildados = $dados['email'];

				$dataenvio1 =  $dados['parametro_select_dataenvio_1'];
				$datainicial1 = $dados['parametro_select_diainicial_1'];
				$datafinal1 = $dados['parametro_select_diaFinal_1'];
				$mes1 = $dados['parametro_select_mes_1'];
				$emaildados1 = $dados['email'];	
				
			    $dataenviosegunda = $dados['parametro_select_dataenvio_1'];

				 if($dados['banco'] !=""){
					  foreach ($dados['banco'] as $key) {				  		
					  		$bancos.= $key.',';	 
						}
					 }
	
			     $banco = substr($bancos, 0, -1);
				 				 
				 if($dataenvio =='' AND $banco == '' AND $dataenviosegunda == ''){
				 	
				      $this->limpaDadosdel();	
					  $cfcarquivo_remessa = 'f';
				      $this->dao->update_forma_cobranca(null, $cfcarquivo_remessa);
					  
					$msg = array('msg' =>"Registro incluído/alterado com sucesso.",'tipo' => 'sucesso','dados' =>'apagar');
					return $msg; 
				}
				 
	           if ($emaildados != "") {											
					 $email = explode(';', $emaildados);		
					 foreach ($email as $emailipo) {
						if (!$this->validaEmail($emailipo)) {		
							$msg = array('msg' =>$emailipo." é inválido!",'tipo' => 'erro','email'=>'erro');
						  return $msg;					
						}
					 }
					
				}

			   				 
						//print $dados['parametro_select_dataenvio_1'];

		    if($banco != "" AND $dataenviosegunda ==""){

                if($dataenvio !=""){	

		    				  $this->limpaDadosdel();	
							  $cfcarquivo_remessa = 'f';
						      $this->dao->update_forma_cobranca(null, $cfcarquivo_remessa);

		    				
		    				//print "primeira =>True - segunda=>False - banco=> True";  	 
		    					 
			  			$resul = $this->dao->fetch_all_parametros_debito($mes,$datafinal,$datainicial,$emaildados);			
		
				    	 if(!$resul){
				     	  
							if ($dataenvio =="") {
								$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio'=>'erro');
								 return $msg;	
							}
							if($datainicial == ""){
								$msg = array('msg' =>"Favor selecionar a data inicial do Período de Faturamento.",'tipo' => 'erro','parametro_select_inicial'=>'erro');
								 return $msg;	
							}elseif($datafinal == ""){
								 $msg = array('msg' =>"Favor selecionar a data final do Período de Faturamento.",'tipo' => 'erro','parametro_select_final'=>'erro');
								 return $msg;
							}elseif($datainicial > $datafinal){
								$msg = array('msg' =>"Data inicial do Período de Faturamento menor que data final.",'tipo' => 'erro','parametro_select'=>'erro');
								return $msg;
							}
							if ($mes == "") {
								$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes'=>'erro');
								return $msg;
							}							
							
		                    if($dados['banco'] !=""){
							  foreach ($dados['banco'] as $key) {				  		
							  		$bancos.= $key.',';	 
								    $this->dao->update_forma_cobranca($key, 't');	
								}
							 }			    
						   
					      $dado = $this->dao->insert($banco,$dataenvio, $datainicial, $datafinal,$mes,$emaildados);
					      
					       if ($dado['result']) {
								$msg = array('msg' =>"Registro incluído com sucesso.",'tipo' => 'sucesso','dados' =>$dado);
								return $msg; 
					 		}else{
					 			$msg = array('msg' =>"Não foi possivel gravar os dados.",'tipo' => 'erro','dados' =>$dado);
						        return $msg; 
					 		}
					  }	
					}else{
							$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio'=>'erro','dados' =>'apagar');
							 return $msg;	
					}				
						 
			 }else if($dataenvio != "" AND $dataenviosegunda =="" AND $banco == ""){
			 		 //  print "primeira =>True - segunda=>False - banco=> False";  

				        $this->limpaDadosdel();	

			       		$resul = $this->dao->fetch_all_parametros_debito($mes,$datafinal,$datainicial,$emaildados);			
		
					     if(!$resul){
					     	  
								if ($dataenvio =="") {
									$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio'=>'erro', 'dados' =>'apagar');
									 return $msg;	
								}
								if($datainicial == ""){
									$msg = array('msg' =>"Favor selecionar a data inicial do Período de Faturamento.",'tipo' => 'erro','parametro_select_inicial'=>'erro', 'dados' =>'apagar');
									 return $msg;	
								}elseif($datafinal == ""){
									 $msg = array('msg' =>"Favor selecionar a data final do Período de Faturamento.",'tipo' => 'erro','parametro_select_final'=>'erro', 'dados' =>'apagar');
									 return $msg;
								}elseif($datainicial > $datafinal){
									$msg = array('msg' =>"Data inicial do Período de Faturamento menor que data final.",'tipo' => 'erro','parametro_select'=>'erro', 'dados' =>'apagar');
									return $msg;
								}
								if ($mes == "") {
									$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes'=>'erro', 'dados' =>'apagar');
									return $msg;
								}								
							 						 			        
							 
						      $dado = $this->dao->insert($banco,$dataenvio, $datainicial, $datafinal,$mes,$emaildados);
							  
							   if ($dado['result']) {
									$msg = array('msg' =>"Registro incluído com sucesso.",'tipo' => 'sucesso','dados' =>$dado);
									return $msg; 
						 		}else{
						 			$msg = array('msg' =>"Não foi possivel gravar os dados.",'tipo' => 'erro','dados' =>$dado);
							        return $msg; 
						 		}
							
                         }
			 }


			 if($banco != "" AND $dataenviosegunda !="" AND $dataenvio == ""){
		    				//print "primeira =>False - segunda=>True - banco=> True";  		 
		    				
    				  $this->limpaDadosdel();	
			   $this->dao->update_forma_cobranca(null, 'f');
			   
			// grava todos os banco ativos	
		                    if($dados['banco'] !=""){
							  foreach ($dados['banco'] as $key) {				  		
							  		$bancos.= $key.',';	 
								    $this->dao->update_forma_cobranca($key, 't');	
								}
							 }			    
						   
					      $dado = $this->dao->insert($banco1,$dataenvio1, $datainicial1, $datafinal1,$mes1,$emaildados1);
					      
					       if ($dado['result']) {
								$msg = array('msg' =>"Registro incluído com sucesso.",'tipo' => 'sucesso','dados' =>$dado,'dados' =>'apagar');
								return $msg; 
					 		}else{
					 			$msg = array('msg' =>"Não foi possivel gravar os dados.",'tipo' => 'erro','dados' =>$dado);
						        return $msg; 
					 		}
										
						 
			 }else if($dataenvio == "" AND $dataenviosegunda !="" ){				  
				   //	print "primeira =>False - segunda=>True - banco=> False";  

				   	    $this->limpaDadosdel();		
			       		$resul = $this->dao->fetch_all_parametros_debito($mes1,$datafinal1,$datainicial1,$emaildados1);			
		
					     if(!$resul){
					     	  
								if ($dataenvio1 =="") {
									$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio_1'=>'erro', 'dados' =>'apagar');
									 return $msg;	
								}
								if($datainicial1 == ""){
									$msg = array('msg' =>"Favor selecionar a data inicial do Período de Faturamento.",'tipo' => 'erro','parametro_select_inicial'=>'erro', 'dados' =>'apagar');
									 return $msg;	
								}elseif($datafinal1 == ""){
									 $msg = array('msg' =>"Favor selecionar a data final do Período de Faturamento.",'tipo' => 'erro','parametro_select_final'=>'erro', 'dados' =>'apagar');
									 return $msg;
								}elseif($datainicial1 > $datafinal1){
									$msg = array('msg' =>"Data inicial do Período de Faturamento menor que data final.",'tipo' => 'erro','parametro_select'=>'erro', 'dados' =>'apagar');
									return $msg;
								}
								if ($mes1 == "") {
									$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes'=>'erro', 'dados' =>'apagar');
									return $msg;
								}
										 			        
							 
						      $dado = $this->dao->insert($banco1,$dataenvio1, $datainicial1, $datafinal1,$mes1,$emaildados1);
							  
							   if ($dado['result']) {
									$msg = array('msg' =>"Registro incluído com sucesso.",'tipo' => 'sucesso','dados' =>$dado);
									return $msg; 
						 		}else{
						 			$msg = array('msg' =>"Não foi possivel gravar os dados.",'tipo' => 'erro','dados' =>$dado);
							        return $msg; 
						 		}
							
                         }
			 }

			 if($dataenviosegunda != "" and $dataenvio != "" AND $banco ==""){
			 			//print "primeira =>True - segunda=>True - banco=> False";  
						 
				   if($dataenvio !=""){				   	
					
						if ($dataenvio =="" and $dataenvio1=="") {
							$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio_1'=>'erro', 'dados' =>'apagar');
							 return $msg;	
						}
						if($datainicial == "" and $datainicial1 ==""){
							$msg = array('msg' =>"Favor selecionar a data inicial do Período de Faturamento.",'tipo' => 'erro','parametro_select_diainicial_1'=>'erro', 'dados' =>'apagar');
							 return $msg;	
						}elseif($datafinal == "" and $datafinal1 ==""){
							 $msg = array('msg' =>"Favor selecionar a data final do Período de Faturamento.",'tipo' => 'erro','parametro_select_diaFinal_1'=>'erro', 'dados' =>'apagar');
							 return $msg;
						}elseif($datainicial > $datafinal){
							$msg = array('msg' =>"A data inicial do período de faturamento deve ser menor ou igual à data final.",'tipo' => 'erro','parametro_select_1'=>'erro', 'dados' =>'apagar');
							return $msg;
						}elseif($datainicial1 > $datafinal1){
							$msg = array('msg' =>"A data inicial do período de faturamento deve ser menor ou igual à data final.",'tipo' => 'erro','parametro_select_1'=>'erro', 'dados' =>'apagar');
							return $msg;
						}
						if ($mes == "") {
							$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes'=>'erro', 'dados' =>'apagar');
							return $msg;
						}

						if ($mes1 =="") {
							$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes_1'=>'erro', 'dados' =>'apagar');
							return $msg;
						}
						
					      $this->limpaDadosdel();	
						  $cfcarquivo_remessa = 'f';
					      $this->dao->update_forma_cobranca(null, $cfcarquivo_remessa);

                        $this->dao->insert($banco,$dataenvio1, $datainicial1, $datafinal1,$mes1,$emaildados1);																
						$dado = $this->dao->insert($banco,$dataenvio, $datainicial, $datafinal,$mes,$emaildados);
						
						if($dados['banco'] !=""){
							  foreach ($dados['banco'] as $key) {				  		
							  		$bancos.= $key.',';	 
								    $this->dao->update_forma_cobranca($key, 't');	
								}
							 }
						
						if ($dado['result']) {
							$msg = array('msg' =>"Registro incluído com sucesso.",'tipo' => 'sucesso','dados' =>$dado);
							return $msg; 
				 		}else{
				 			$msg = array('msg' =>"Não foi possivel gravar os dados.",'tipo' => 'erro','dados' =>$dado);
					        return $msg; 
				 		}
					  	
					}else{
						$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio_1'=>'erro','dados' =>'apagar');
						 return $msg;	
					}	
			   }elseif ($dataenviosegunda != "" and $dataenvio != "" AND $banco !="") {

             // print "primeira =>True - segunda=>True - banco=> True";

			      	if($dataenvio !=""){				   	
					
						if ($dataenvio =="" and $dataenvio1=="") {
							$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio_1'=>'erro');
							 return $msg;	
						}
						if($datainicial == "" and $datainicial1 ==""){
							$msg = array('msg' =>"Favor selecionar a data inicial do Período de Faturamento.",'tipo' => 'erro','parametro_select_diainicial_1'=>'erro', 'dados' =>'apagar');
							 return $msg;	
						}elseif($datafinal == "" and $datafinal1 ==""){
							 $msg = array('msg' =>"Favor selecionar a data final do Período de Faturamento.",'tipo' => 'erro','parametro_select_diaFinal_1'=>'erro', 'dados' =>'apagar');
							 return $msg;
						}elseif($datainicial > $datafinal){
							$msg = array('msg' =>"A data inicial do período de faturamento deve ser menor ou igual à data final.",'tipo' => 'erro','parametro_select'=>'erro', 'dados' =>'apagar');
							return $msg;
						}elseif($datainicial1 > $datafinal1){
							$msg = array('msg' =>"A data inicial do período de faturamento deve ser menor ou igual à data final.",'tipo' => 'erro','parametro_select_1'=>'erro', 'dados' =>'apagar');
							return $msg;
						}
						if ($mes == "") {
							$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes'=>'erro', 'dados' =>'apagar');
							return $msg;
						}

						if ($mes1 =="") {
							$msg = array('msg' =>"Favor selecionar o mês de referência.",'tipo' => 'erro','parametro_select_mes_1'=>'erro', 'dados' =>'apagar');
							return $msg;
						}
						
					      $this->limpaDadosdel();	
						  $cfcarquivo_remessa = 'f';
					      $this->dao->update_forma_cobranca(null, $cfcarquivo_remessa);

					      if($dados['banco'] !=""){
							  foreach ($dados['banco'] as $key) {				  		
							  		$bancos.= $key.',';	 
								    $this->dao->update_forma_cobranca($key, 't');	
								}
							 }	

                        $this->dao->insert($banco,$dataenvio1, $datainicial1, $datafinal1,$mes1,$emaildados1);																
						$dado = $this->dao->insert($banco,$dataenvio, $datainicial, $datafinal,$mes,$emaildados);
						
						if($dados['banco'] !=""){
							  foreach ($dados['banco'] as $key) {				  		
							  		$bancos.= $key.',';	 
								    $this->dao->update_forma_cobranca($key, 't');	
								}
							 }
						
						if ($dado['result']) {
							$msg = array('msg' =>"Registro incluído com sucesso.",'tipo' => 'sucesso','dados' =>$dado);
							return $msg; 
				 		}else{
				 			$msg = array('msg' =>"Não foi possivel gravar os dados.",'tipo' => 'erro','dados' =>$dado);
					        return $msg; 
				 		}
					  	
					}else{
						$msg = array('msg' =>"Favor selecionar pelo menos uma Data de envio.",'tipo' => 'erro','parametro_select_dataenvio_1'=>'erro','dados' =>'apagar');
						 return $msg;	
					}	
			   }
         }

    public function validaEmail($email) {
			  $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
			    if (preg_match($er, $email)){
			    		return true;
			    } else {
			   		 return false;
			    }
       }
	
	public function GetClassesParametros_debito(){
		
		return $this->dao->fetch_all_parametros_debito();
	}
	
	public function limpaDadosdel($dados = null){
							
			return $this->dao->del($dados);
			           	
	}
	protected function validoForm($value='')
	{
		
	}
}