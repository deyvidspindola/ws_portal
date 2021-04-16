<?php

require_once(_MODULEDIR_.'Cadastro/DAO/CadLayoutEmailsDAO.php');

  /**
   * 
   */
  class FuncoesBancos{
      
      
	  public function tiraAcento($str) {
		
	      return strtr(utf8_decode($str),utf8_decode('Å Å’Å½Å¡Å“Å¾Å¸Â¥ÂµÃ€ÃÃ‚ÃƒÃ„Ã…Ã†Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃÃŽÃÃÃ‘Ã’Ã“Ã”Ã•Ã–Ã˜Ã™ÃšÃ›ÃœÃÃŸÃ Ã¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã³Ã´ÃµÃ¶Ã¸Ã¹ÃºÃ»Ã¼Ã½Ã¿-.'),'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy  ');
	  }
	  
	  /**
	   *  Função que retorna espaço e zero
	   */	   
	 
	public function complementoRegistro($int,$tipo)
	{
	    if($tipo == "zeros")
	    {
	        $space = '';
	        for($i = 1; $i <= $int; $i++)
	        {
	            $space .= '0';
	        }
	    }
	    else if($tipo == "brancos")
	    {
	        $space = '';
	        for($i = 1; $i <= $int; $i++)
	        {
	            $space .= ' ';
	        }
	    }
	    
	    return $space;
	}
	  /*
	 * Verifica se o banco esta ativo
	 */
	public function BancosAtivos($cfbbanco)	{			
			
		switch ($cfbbanco) {
			case 33:
				$cfbnome = 'BANCO SANTANDER S/A';
				$nomeArq= 'DASANTANDER';
			break;
			case 341:
				$cfbnome = 'BANCO ITAU S/A';
				$nomeArq = 'DI';
				break;
			case 1:
				$cfbnome = 'BANCO DO BRASIL S/A';
				$nomeArq = 'DABRASIL';
				break;
			case 399:
				$cfbnome = 'HSBC BANK BRASIL S/A';
				$nomeArq = 'DAHSBCBANK';
				break;
			case 409:
				$cfbnome = 'UNIBANCO S/A';
				$nomeArq = 'DAUNIBANCO';
				break;
			case 237:
				$cfbnome = 'BRADESCO S/A';
				$nomeArq = 'DABRADESCO';
				break;
			default:
				$cfbnome = 'O codigo: '.$cfbbanco.' me disse que o banco não esta ativo no momento.Tente mais Tarde. :(';
				$nomeArq = '';
				break;
		}
		
		$dados = array('cfbnome' =>$cfbnome ,
					   'nomeArq' =>$nomeArq
					  );
			
		return $dados;	
		
	}
	  
	   //função que verificar o ultimo digito do cartão
	    protected function validarAgencia($clicagencia,$clicc_bancaria){
					
			$clicagencia = "$clicagencia" . '00';
			//$clicc_bancaria =  str_replace("-","","$clicc_bancaria"); 
	
		    $novo = "$clicagencia$clicc_bancaria";
			$seg = '97310097131973';
			
			$total = 0;
			
			for ($i=0; $i < 14; $i++) {
								 			
				$total= $total + substr(($novo[$i] * $seg[$i]), (strlen(($novo[$i] * $seg[$i]))-1));		
			}							
			$total = 10 - substr($total, (strlen($total)-1));
			
			return $total;
				
	   }
		
		// função pra validar agencia  econvenio 
		public function validarAC($value){
		 	
			if (strlen($value) == 10) {
				
				$seg = '8923456789';
				$total = 0;
				
				for ($i=0; $i < 10; $i++) {
					
					$total= $total + substr(($seg[$i] * $value[$i]), (strlen(($seg[$i] * $value[$i]))-2));
					
				}
				$total = $total % 11;
				
					if ($total == 0 || $total == 10) {
										
						$total = 0;
					}
				
				return $total;
				
			}else{
				return false;
			}
			
		}
		
		public function Santader($consultaExtraida){
			
			$i = 0;
			//$erro = 0;			
			foreach ($consultaExtraida as $value) {
						
					    $totalAgencia = 4 - strlen($value->clicagencia);
						$clicagencia =	$this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia;
						
					    $totalDigitos = 9 - strlen($value->clicconta);
					    $clicconta = $this->complementoRegistro($totalDigitos,'zeros').$value->clicconta;
						
						//$digitoConta = substr($clicconta, 0, -1);						
						//$result = $this->validarAgencia($clicagencia, $clicconta);
						
						//$ultimoDigitoConta = substr($clicconta, -1);						
						
						//if($result != $ultimoDigitoConta){
						//	$erro = 1;							
						//	$Registro.= '<b>Nome: </b>'.$value->clinome.'<b><br/>CPF: </b>'.$value->clino_cpf.'<br/>';						
						//}			
						
						if($value->clitipo == 'F'){
							$documento = $value->clino_cpf;
						}elseif($value->clitipo == 'J'){
							$documento = $value->clino_cgc;
						}
						
						$E01 = "E"; //Código do registro
						$totalcorrigido = 14 - strlen($documento);
						$E02 = $this->complementoRegistro($totalcorrigido,'zeros').$documento.$this->complementoRegistro(11,'brancos');	//	(clientes.clioid);//Identificação do cliente na Empresa					
						
						$totalAgencia = 4 - strlen($value->clicagencia);
						$E03 = $this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia;//(cliente_cobranca.clicagencia); //Agência para débito 
						
					   	$Totalbancaria = 14 - strlen($value->clicconta);					
						$E04 = $this->complementoRegistro($Totalbancaria,'zeros').$value->clicconta; ///(cliente_cobranca. clicconta); //Identificação do cliente no Banco
					
						$Totaltitdt_vencimento = 8 - strlen(str_replace("-","",$value->titdt_vencimento));					
						$E05 = $this->complementoRegistro($Totaltitdt_vencimento,'zeros').str_replace("-","",$value->titdt_vencimento); //Data do vencimento
						
						$valor_corrigido = str_replace(".","",$value->valor_corrigido);
						$TotalCorrigido = 15 - strlen($valor_corrigido);
						$E06 = $this->complementoRegistro($TotalCorrigido,'zeros').$valor_corrigido;//"valor_corrigid"; //Valor do débito = Consulta da RN1, campo
						
						$E07 = '03'; //Código da moeda  = 03 (Neste caso, ler o valor com 2 decimais.);						
						
						$TotalTitulo = 12 - strlen($value->titoid);
						$totalNotaFiscal = 12 - strlen($value->nflno_numero);
						$E08 = $this->complementoRegistro($TotalTitulo,'zeros').$value->titoid.'-'.$this->complementoRegistro($totalNotaFiscal,'zeros').$value->nflno_numero; //(titulo.titoid) + (nota_fiscal.nflno_numero); //Uso da Empresa
						
						$TotalEmpresa = 60 - strlen($E08);
						$E09 = $this->complementoRegistro($TotalEmpresa,'brancos').$this->complementoRegistro(20,'brancos');//Reservado para o futuro (filler);	
						
						$E10 = 0;//Código do movimento = 0 - débito normal.
						
						$dadoE.= "\r\n".$E01.''.$E02.''.$E03.''.$E04.''.$E05.''.$E06.''.$E07.''.$E08.''.$E09.''.$E10;
						$i++;
						$titoidArray[$i].= $value->titoid;
						$totalDebito = $totalDebito + $valor_corrigido;
						
			}

           //if($erro == 0){  			 
			 $dados = array('dadoE' => $dadoE ,'totalRegistro'=> $i,'titoidArray'=>$titoidArray,'totalDebito'=> $totalDebito);
			 return $dados;	
			 
		   //}elseif($erro == 1){
		   //	  $dados = array('erro' =>$erro ,'registro'=>$Registro );
		   //	  return $dados;	
		   //}	
			
		}

		public function itau($consultaExtraida){
			
			$i = 0;	
			//$erro = 0;		
			foreach ($consultaExtraida as $value) {
				
				if($value->clitipo == 'F'){
					$documento = $value->clino_cpf;
				}elseif($value->clitipo == 'J'){
					$documento = $value->clino_cgc;
				}
				
				$E01 = "E"; //Código do registro
				$totalcorrigido = 14 - strlen($documento);
				
				$E02 = $this->complementoRegistro($totalcorrigido,'zeros').$documento.$this->complementoRegistro(11,'brancos'); //	(clientes.clioid);//Identificação do cliente na Empresa
				
				$totalAgencia = 4 - strlen($value->clicagencia);
				$E03 = $this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia.$this->complementoRegistro(8,'brancos');//(cliente_cobranca.clicagencia); //Agência para débito 
				
				$totalConta = 6 - strlen($value->clicconta);
				$E04 = $this->complementoRegistro($totalConta,'zeros').$value->clicconta;  ///(cliente_cobranca. clicconta); //Identificação do cliente no Banco, número da conta sem o dígito
			 				 			
				$Totaltitdt_vencimento = 8 - strlen(str_replace("-","",$value->titdt_vencimento));					
				$E06 = str_replace("-","",$value->titdt_vencimento).$this->complementoRegistro($Totaltitdt_vencimento,'zeros'); //Data do vencimento
				
				$valor_corrigido = str_replace(".","",$value->valor_corrigido);
				$TotalCorrigido = 15 - strlen($valor_corrigido);
				$E07 = $this->complementoRegistro($TotalCorrigido,'zeros').$valor_corrigido;//Valor do débito = Consulta da RN1, campo
				
				$E08 = '03'; //Código da moeda  = 03 (Neste caso, ler o valor com 2 decimais.);
				
				$TotalTitulo = 12 - strlen($value->titoid);
				$totalNotaFiscal = 12 - strlen($value->nflno_numero);
				$E09 = $this->complementoRegistro($TotalTitulo,'zeros').$value->titoid.'-'.$this->complementoRegistro($totalNotaFiscal,'zeros').$value->nflno_numero; //(titulo.titoid) + (nota_fiscal.nflno_numero); //Uso da Empresa
							
				$E10 = $this->complementoRegistro(15,'zeros'); //valor da mora	
				
				$E11 = $this->complementoRegistro(1,'brancos').'SASCAR'.$this->complementoRegistro(10,'brancos');//complemento	 INFORMAÇÃO COMPL. P/ HISTÓRICO DE C/C	
				
				$E12 = $this->complementoRegistro(9,'brancos');//complemento de registro 
				
				$totalCpf  = 14 - strlen($documento);
				$E13 = $this->complementoRegistro($totalCpf,'zeros').$documento;				
				
				$E14 = 0;//Código do movimento = 0 - débito normal.
				
				$dadoE.= "\r\n".$E01.''.$E02.''.$E03.''.$E04.''.$E06.''.$E07.''.$E08.''.$E09.''.$E10.''.$E11.''.$E12.''.$E13.''.$E14;
				$i++;
				$titoidArray[$i].= $value->titoid;
				
				$totalDebito = $totalDebito + $valor_corrigido;
			}
						 
		   //if($erro == 0){  			 
			 $dados = array('dadoE' => $dadoE ,'totalRegistro'=> $i,'titoidArray'=>$titoidArray,'totalDebito'=>$totalDebito);
			 return $dados;	
			 
		   //}elseif($erro == 1){
		   //	  $dados = array('erro' =>$erro ,'registro'=>$Registro );
		   //	  return $dados;	
		   //}
			
			
		}

		public function hsbc($consultaExtraida){
			
			$i = 0;	
			//$erro = 0;				
			foreach ($consultaExtraida as $value) {
				
				if($value->clitipo == 'F'){
					$documento = $value->clino_cpf;
				}elseif($value->clitipo == 'J'){
					$documento = $value->clino_cgc;
				}
				
				$E01 = "E"; //Código do registro
				$totalcorrigido = 14 - strlen($documento);
				$E02 = $this->complementoRegistro($totalcorrigido,'zeros').$documento.$this->complementoRegistro(11,'brancos'); //	(clientes.clioid);//Identificação do cliente na Empresa
				
				$totalAgencia = 4 - strlen($value->clicagencia);
				$E03 = $this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia;//(cliente_cobranca.clicagencia); //Agência para débito 					  	
				
				$ultimoDigito = substr($value->clicconta, -1);   
				$digitos = substr($value->clicconta, 0, -1);						
				$bancaria = $digitos.$ultimoDigito;							
				$Totalbancaria = 14 - strlen('399'.$this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia.$bancaria);					
				$E04 = '399'.$this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia.$this->complementoRegistro($Totalbancaria,'zeros').$bancaria; ///(cliente_cobranca. clicconta); //Identificação do cliente no Banco
			 	
				//$agencia = 	$this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia;	
			 	//$totalDigitos = 6 - strlen($digitos);
				//$digitos = $this->complementoRegistro($totalDigitos,'zeros').$digitos;
				//$dados = $agencia.$digitos;				
				//$result = $this->validarAC($dados);				
				//if($result != $digitos){
				//	$erro = 0;							
				//	$Registro.= '<b>Nome: </b>'.$value->clinome.'<b><br/>CPF: </b>'.$value->clino_cpf.'<br/>';	
				//}
			 				 
				$Totaltitdt_vencimento = 8 - strlen(str_replace("-","",$value->titdt_vencimento));					
				$E05 = $this->complementoRegistro($Totaltitdt_vencimento,'zeros').str_replace("-","",$value->titdt_vencimento); //Data do vencimento
				
				$valor_corrigido = str_replace(".","",$value->valor_corrigido);
				$TotalCorrigido = 15 - strlen($valor_corrigido);
				
				$E06 = $this->complementoRegistro($TotalCorrigido,'zeros').$valor_corrigido;//"valor_corrigid"; //Valor do débito = Consulta da RN1, campo				
				
				$E07 = '03'; //Código da moeda  = 03 (Neste caso, ler o valor com 2 decimais.);

				$TotalTitulo = 12 - strlen($value->titoid);
				$totalNotaFiscal = 12 - strlen($value->nflno_numero);
				$E08 = $this->complementoRegistro($TotalTitulo,'zeros').$value->titoid.'-'.$this->complementoRegistro($totalNotaFiscal,'zeros').$value->nflno_numero; //(titulo.titoid) + (nota_fiscal.nflno_numero); //Uso da Empresa
				
				$TotalEmpresa = 31 - strlen($E08);
				$E09 = $this->complementoRegistro($TotalEmpresa,'brancos');
					
				$totalCpf  = 14 - strlen($documento);
				$E10 = $this->complementoRegistro($totalCpf,'zeros').$documento;
				
				$E11 = $this->complementoRegistro(15,'brancos');
				
				$E12 = '37';//tipo de débito		
				
				$E13 = $this->complementoRegistro(7,'brancos');				
				
				$E14 = $this->complementoRegistro(11,'brancos');	
				
				$E15 = 0;//Código do movimento = 0 - débito normal.
				
				$dadoE.= "\r\n".$E01.''.$E02.''.$E03.''.$E04.''.$E05.''.$E06.''.$E07.''.$E08.''.$E09.''.$E10.''.$E11.''.$E12.''.$E13.''.$E14.''.$E15;
				$i++;
				$titoidArray[$i].= $value->titoid;
				
				$totalDebito = $totalDebito + $valor_corrigido;
			}
			
		   //if($erro == 0){  			 
			 $dados = array('dadoE' => $dadoE ,'totalRegistro'=> $i,'titoidArray'=>$titoidArray,'totalDebito' => $totalDebito);
			 return $dados;	
			 
		   //}elseif($erro == 1){
		   //	  $dados = array('erro' =>$erro ,'registro'=>$Registro );
		   //	  return $dados;	
		   //}
			
		}


		public function bradesco($consultaExtraida){
			
			$i = 0;			
			foreach ($consultaExtraida as $value) {
				
				if($value->clitipo == 'F'){
					$documento = $value->clino_cpf;
					$E09_indentificacao = 2; //tipo de identificação CPF
				}elseif($value->clitipo == 'J'){
					$documento = $value->clino_cgc;
					$E09_indentificacao = 1; //tipo de identificação CNPJ
				}
				
				$E01 = "E"; //Código do registro
				$totalcorrigido = 14 - strlen($documento);
				$E02 = $this->complementoRegistro($totalcorrigido,'zeros').$documento.$this->complementoRegistro(11,'brancos');//	(clientes.clioid);//Identificação do cliente na Empresa
						
				$totalAgencia = 4 - strlen($value->clicagencia);
				$E03 = $this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia;//(cliente_cobranca.clicagencia); //Agência para débito 
								  	
				$Totalbancaria = 14 - strlen($value->clicconta);					
				$E04 = $value->clicconta.$this->complementoRegistro($Totalbancaria,'brancos');

				$Totaltitdt_vencimento = 8 - strlen(str_replace("-","",$value->titdt_vencimento));					
				$E05 = $this->complementoRegistro($Totaltitdt_vencimento,'zeros').str_replace("-","",$value->titdt_vencimento); //Data do vencimento
				
				$valor_corrigido = str_replace(".","",$value->valor_corrigido);
				$TotalCorrigido = 15 - strlen($valor_corrigido);
				$E06 = $this->complementoRegistro($TotalCorrigido,'zeros').$valor_corrigido;//"valor_corrigid"; //Valor do débito = Consulta da RN1, campo
				
				$E07 = '03'; //Código da moeda  = 03 (Neste caso, ler o valor com 2 decimais.);
				
				$TotalTitulo = 12 - strlen($value->titoid);
				$totalNotaFiscal = 12 - strlen($value->nflno_numero);
				$E08 = $this->complementoRegistro($TotalTitulo,'zeros').$value->titoid.'-'.$this->complementoRegistro($totalNotaFiscal,'zeros').$value->nflno_numero; //(titulo.titoid) + (nota_fiscal.nflno_numero); //Uso da Empresa

				$TotalEmpresa = 60 - strlen($E08);
				$E09 = $this->complementoRegistro($TotalEmpresa,'brancos').$E09_indentificacao;
				
				$totalCpf  = 15 - strlen($documento);
				$E10 = $this->complementoRegistro($totalCpf,'zeros').$documento;
				$E11 = $this->complementoRegistro(4,'brancos');
				$E12 = 0;//Código do movimento = 0 - débito normal.
				
				$dadoE.= "\r\n".$E01.''.$E02.''.$E03.''.$E04.''.$E05.''.$E06.''.$E07.''.$E08.''.$E09.''.$E10.''.$E11.''.$E12;
				$i++;
				$titoidArray[$i].= $value->titoid;
				
				$totalDebito = $totalDebito + $valor_corrigido;
			}
			
		  		 
			 $dados = array('dadoE' => $dadoE ,'totalRegistro'=> $i,'titoidArray'=>$titoidArray,'totalDebito'=>$totalDebito);
			 
			 return $dados;	
			 
			
		}

        public function banco_do_brasil($consultaExtraida){
			$i = 0;
			
			foreach ($consultaExtraida as $value) {
				
				if($value->clitipo == 'F'){
					$documento = $value->clino_cpf;
					$E09_indentificacao = 2;
				}elseif($value->clitipo == 'J'){
					$documento = $value->clino_cgc;
					$E09_indentificacao = 1;
				}
				
				$E01 = "E"; //Código do registro
				$totalcorrigido = 14 - strlen($documento);
				$E02 = $this->complementoRegistro($totalcorrigido,'zeros').$documento.$this->complementoRegistro(11,'brancos');	//	(clientes.clioid);//Identificação do cliente na Empresa					
				
				$totalAgencia = 4 - strlen($value->clicagencia);
				$E03 = $this->complementoRegistro($totalAgencia,'zeros').$value->clicagencia;//(cliente_cobranca.clicagencia); //Agência para débito 
								  	
				$ultimoDigito = substr($value->clicconta, -1);  
				 
				$digitos = substr($value->clicconta, 0, -1);	
				$totalzero = 7 - strlen($digitos);					
				$bancaria = $this->complementoRegistro($totalzero,'zeros').$digitos;
			
				$Totalbancaria = 14 - strlen($bancaria);					
				$E04 = $this->complementoRegistro($Totalbancaria,'zeros').$bancaria; ///(cliente_cobranca. clicconta); //Identificação do cliente no Banco
			 				 
				$Totaltitdt_vencimento = 8 - strlen(str_replace("-","",$value->titdt_vencimento));					
				$E05 = $this->complementoRegistro($Totaltitdt_vencimento,'zeros').str_replace("-","",$value->titdt_vencimento); //Data do vencimento
				
				$valor_corrigido = str_replace(".","",$value->valor_corrigido);
				$TotalCorrigido = 15 - strlen($valor_corrigido);
				$E06 = $this->complementoRegistro($TotalCorrigido,'zeros').$valor_corrigido;//"valor_corrigid"; //Valor do débito = Consulta da RN1, campo
				
				$E07 = '03'; //Código da moeda  = 03 (Neste caso, ler o valor com 2 decimais.);
						
				$TotalTitulo = 12 - strlen($value->titoid);
				$totalNotaFiscal = 12 - strlen($value->nflno_numero);
				$E08 = $this->complementoRegistro($TotalTitulo,'zeros').$value->titoid.'-'.$this->complementoRegistro($totalNotaFiscal,'zeros').$value->nflno_numero; //(titulo.titoid) + (nota_fiscal.nflno_numero); //Uso da Empresa
				
				$TotalEmpresa = 60 - strlen($E08);
				$E09 = $this->complementoRegistro($TotalEmpresa,'brancos').$E09_indentificacao;
				
				$totalCpf  = 15 - strlen($documento);
				$E10 = $this->complementoRegistro($totalCpf,'zeros').$documento;
				$E11 = $this->complementoRegistro(4,'brancos');
				$E12 = 0;//Código do movimento = 0 - débito normal.
								
				$dadoE.= "\r\n".$E01.''.$E02.''.$E03.''.$E04.''.$E05.''.$E06.''.$E07.''.$E08.''.$E09.''.$E10.''.$E11.''.$E12;
				$i++;
				$titoidArray[$i].= $value->titoid;
				$totalDebito = $totalDebito + $valor_corrigido;
			}
					  		 
			 $dados = array('dadoE' => $dadoE ,'totalRegistro'=> $i, 'titoidArray'=>$titoidArray,'totalDebito' => $totalDebito);
			 return $dados;			
		}

		public function diaFaturamento($pdames_referencia,$pdadt_inicio_faturamento,$pdadt_fim_faturamento){
							
					$datahoras = date('d/m/Y H:m:s');
											  
					$mes = date('m');	
					$ano = date('Y');
					
					if($mes == 12){
						$mes == '01';
						$ano = $ano + 1;
					}
					
					if($pdames_referencia == 'S'){
						$mes = $mes + 1;
						$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
						//	print $ultimo_dia.'<br>'.$pdadt_inicio_faturamento.'<br>'.$ultimo_dia.'<br>';
							
						if($pdadt_inicio_faturamento > $ultimo_dia){							
							$pdadt_inicio_faturamento = $ultimo_dia;	
						}elseif($pdadt_fim_faturamento > $ultimo_dia){
							$pdadt_fim_faturamento = $ultimo_dia;
						}
					}elseif($pdames_referencia == 'A'){
						 $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
						//print $ultimo_dia.'<br>'.$pdadt_inicio_faturamento.'<br>'.$ultimo_dia.'<br>';
						if($pdadt_inicio_faturamento > $ultimo_dia){							
							$pdadt_inicio_faturamento = $ultimo_dia;	
						}elseif($pdadt_fim_faturamento > $ultimo_dia){
							$pdadt_fim_faturamento = $ultimo_dia;
						}
					}
					$diaFaturamento = $pdadt_inicio_faturamento.'/'.$mes.'/'.$ano.' a '.$pdadt_fim_faturamento.'/'.$mes.'/'.$ano;
					
			return $diaFaturamento;
		}
	 
	
  }
  
?>