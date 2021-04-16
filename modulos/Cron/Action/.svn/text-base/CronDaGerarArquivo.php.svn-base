<?php


/**
 * CronDaGerarArquivo.php
 *
 * Script responsavel por enviar e-mail e criar arquivo de parametros 
 *
 *  @package CronDaGerarArquivoDAO
 *  @author  ernando de castro <ernandocs@brq.com>
 *  @since    23/10/2013 09:16
 *  @version 1.0
 */
   
require_once(_MODULEDIR_.'Cron/DAO/CronDaGerarArquivoDAO.php');
require_once(_MODULEDIR_.'Principal/Action/ServicoEnvioEmail.php');
require_once(_MODULEDIR_.'Cadastro/DAO/CadLayoutEmailsDAO.php');
require_once(_MODULEDIR_.'Cron/Lib/FuncoesBancos.php');

class CronDaGerarArquivo  {
		
	private $dao;
	private $conn;	
	private $tituloFuncionalidade;
	private $medodos;
	/**
	 * Construtor
	 */
	public function __construct() {
	
		global $conn;
	
		$this->conn = $conn;
	    $this->tituloFuncionalidade = "Arquivo de Remessa";
		/**
		 * Objeto - DAO
		 */
		$this->dao = new CronDaGerarArquivoDAO($conn);
		$this->medodos = new FuncoesBancos();		   
	}
	
	public function dadosBancos($value=''){
		
		return $this->dao->bancos();
	}
	
	public function dadosParametrosDebitos($value=''){
		
		return $this->dao->parametrosDebitos();
		
	}
	
	public function dadosConsultaExtraida($pdadt_inicio_faturamento, $pdadt_fim_faturamento, $pdames_referencia, $forcoid){
				
		return $this->dao->consultaExtraida($pdadt_inicio_faturamento, $pdadt_fim_faturamento, $pdames_referencia, $forcoid);
	}
	/*
	 * lay-out santander s/a
	 */
	public function banco_santander_sa($cfbremessa,$cfbconvenio){
		
		    pg_query($this->conn, "BEGIN");
		    
			$A01 = "A"; //CÃ³digo do registro 
			$A02 = 1; //CÃ³digo de remessa   1 - REMESSA  - Enviado pela Empresa para o Banco. 
			$totalconvenio =  20 - strlen('00'.$cfbconvenio);			
			$A03  = '00'.$cfbconvenio.$this->medodos->complementoRegistro($totalconvenio,'brancos'); //CÃ³digo do ConvÃªnio 
			$A04 = "SASCAR Tec Seg Ltda "; // Nome da Empresa
			$Banco = '33'; 
			$totalBanco =  3 - strlen($Banco);
			$A05 = $this->medodos->complementoRegistro($totalBanco,'zeros').$Banco; //CÃ³digo do Banco 
			$nomeBanco = 20 - strlen("SANTANDER");			
			$A06 = "SANTANDER".$this->medodos->complementoRegistro($nomeBanco,'brancos'); // Nome do Banco 
			$data = 8 - strlen(date("Ymd"));
			$A07 = $this->medodos->complementoRegistro($data,'zeros').date("Ymd"); //Data de geraÃ§Ã£o do arquivo (AAAAMMDD);
		    $totalremessa = 6 - strlen($cfbremessa);			
			$A08 = $this->medodos->complementoRegistro($totalremessa,'zeros').$cfbremessa; //NÃºmero sequencial do arquivo 		
			$A09 =  '04'; //VersÃ£o do lay-out
			$debito = 17 - strlen("DEBITO AUTOMATICO");
			$A10 = "DEBITO AUTOMATICO".''.$this->medodos->complementoRegistro($debito,'brancos');  //IdentificaÃ§Ã£o do ServiÃ§o =
			$A11 = $this->medodos->complementoRegistro(52,'brancos'); //Reservado para o futuro 	
					
		       /*$valueLogDebitoAutomatico = array(
							        				'ldaanumero_remessa' =>$cfbremessa , 
							        				'ldaadt_remessa' => $A07
												);		
				
				$this->logDebitoAutomatico($valueLogDebitoAutomatico);
				*/
				
				$dadosAtualizaBancos = array(
										'cfbremessa' => $A08,
										'banco'=> $A05
										);
								
			$this->atualizaBancos($dadosAtualizaBancos);
			
		    $dadoBanco['linha_a']   = $A01.''.$A02.''.$A03.''.$A04.''.$A05.''.$A06.''.$A07.''.$A08.''.$A09.''.$A10.''.$A11;
		    $dadoBanco['num_banco'] = $A05;
		
		return $dadoBanco;
		
	}
     /*
	 * lay-out itau s/a
	 */
	public function banco_itau_sa($cfbremessa,$cfbconvenio){
		$medodos = new FuncoesBancos();
		pg_query($this->conn, "BEGIN");
		
		    $A01 = "A"; //Código do registro 
			$A02 = 1; //Código de remessa   1 - REMESSA  - Enviado pela Empresa para o Banco. 
			$totalconvenio =  20 - strlen($cfbconvenio);			
			$A03  = $cfbconvenio.''.$this->medodos->complementoRegistro($totalconvenio,'brancos'); //Código do Convênio 
			$A04 = "SASCAR Tec Seg Ltda "; // Nome da Empresa
			$Banco = '341'; 
			$totalBanco =  3 - strlen($Banco);
			$A05 = $this->medodos->complementoRegistro($totalBanco,'zeros').$Banco; //Código do Banco 
			$nomeBanco = 20 - strlen("BANCO ITAU");			
			$A06 = "BANCO ITAU".$this->medodos->complementoRegistro($nomeBanco,'brancos'); // Nome do Banco 
			$data = 8 - strlen(date("Ymd"));
			$A07 = $this->medodos->complementoRegistro($data,'zeros').date("Ymd"); //Data de geraçõo do arquivo (AAAAMMDD);
		    $totalremessa = 6 - strlen($cfbremessa);			
			$A08 = $this->medodos->complementoRegistro($totalremessa,'zeros').$cfbremessa; //Número sequencial do arquivo 		
			$A09 =  '04'; //VersÃ£o do lay-out
			$debito = 17 - strlen("DEBITO AUTOMATICO");
			$A10 = "DEBITO AUTOMATICO".''.$this->medodos->complementoRegistro($debito,'brancos');  //Identificação do Serviço =
			$A11 = $this->medodos->complementoRegistro(52,'brancos'); //Reservado para o futuro 	
					
			$dadosAtualizaBancos = array(
					'cfbremessa' => $A08,
					'banco'=> $A05
			);

			$this->atualizaBancos($dadosAtualizaBancos);
				
			$dadoBanco['linha_a']   = $A01.''.$A02.''.$A03.''.$A04.''.$A05.''.$A06.''.$A07.''.$A08.''.$A09.''.$A10.''.$A11;
			$dadoBanco['num_banco'] = $A05;

		return $dadoBanco;
	}
	/*
	 * lay-out brasil s/a
	 */
	public function banco_do_brasil_sa($cfbremessa,$cfbconvenio){
		
		pg_query($this->conn, "BEGIN");
		
		    $A01 = "A"; //Código do registro 
			$A02 = 1; //Código de remessa   1 - REMESSA  - Enviado pela Empresa para o Banco. 
			$totalconvenio =  20 - strlen($cfbconvenio);			
			$A03  = $cfbconvenio.''.$this->medodos->complementoRegistro($totalconvenio,'brancos'); //Código do Convênio 
			$A04 = "SASCAR Tec Seg Ltda "; // Nome da Empresa
			$Banco = '1'; 
			$totalBanco =  3 - strlen($Banco);
			$A05 = $this->medodos->complementoRegistro($totalBanco,'zeros').$Banco; //Código do Banco 
			$nomeBanco = 20 - strlen("BANCO DO BRASIL S/A");			
			$A06 = "BANCO DO BRASIL S/A".$this->medodos->complementoRegistro($nomeBanco,'brancos'); // Nome do Banco 
			$data = 8 - strlen(date("Ymd"));
			$A07 = $this->medodos->complementoRegistro($data,'zeros').date("Ymd"); //Data de geraçõo do arquivo (AAAAMMDD);
		    $totalremessa = 6 - strlen($cfbremessa);			
			$A08 = $this->medodos->complementoRegistro($totalremessa,'zeros').$cfbremessa; //Número sequencial do arquivo 		
			$A09 =  '05'; //VersÃ£o do lay-out
			$debito = 17 - strlen("DEBITO AUTOMATICO");
			$A10 = "DEBITO AUTOMATICO".''.$this->medodos->complementoRegistro($debito,'brancos');  //Identificação do Serviço =
			$A11 = $this->medodos->complementoRegistro(52,'brancos'); //Reservado para o futuro 	
					
			$dadosAtualizaBancos = array(
					'cfbremessa' => $A08,
					'banco'=> $A05
			);

			$this->atualizaBancos($dadosAtualizaBancos);
				
			$dadoBanco['linha_a']   = $A01.''.$A02.''.$A03.''.$A04.''.$A05.''.$A06.''.$A07.''.$A08.''.$A09.''.$A10.''.$A11;
			$dadoBanco['num_banco'] = $A05;
			 
		return $dadoBanco;

	}
     /*
	 * lay-out hsbc s/a
	 */
	public function hsbc_bank_brasil_sa($cfbremessa,$cfbconvenio)
	{
	  pg_query($this->conn, "BEGIN");
	  
		$A01 = "A"; //Código do registro 
		$A02 = 1; //Código de remessa   1 - REMESSA  - Enviado pela Empresa para o Banco.				
	    $cod = '0000'.$cfbconvenio;	
		$ultimoDigito = substr($cod, -1);
		$digitos = substr($cod, 0, -1);	
		//$result = $this->medodos->validarAC($digitos);		
		$totalconvenio =  20 - strlen($cod);			
		$A03  = $cod.''.$this->medodos->complementoRegistro($totalconvenio,'brancos'); //Código do Convênio 
		$A04 = "SASCAR Tec Seg Ltda "; // Nome da Empresa
		$Banco = '399'; 
		$totalBanco =  3 - strlen($Banco);
		$A05 = $this->medodos->complementoRegistro($totalBanco,'zeros').$Banco; //Código do Banco 
		$nomeBanco = 20 - strlen("HSBC BANK BRASIL SA");			
		$A06 = "HSBC BANK BRASIL SA".$this->medodos->complementoRegistro($nomeBanco,'brancos'); // Nome do Banco 
		$data = 8 - strlen(date("Ymd"));
		$A07 = $this->medodos->complementoRegistro($data,'zeros').date("Ymd"); //Data de geraçõo do arquivo (AAAAMMDD);
	    $totalremessa = 6 - strlen($cfbremessa);			
		$A08 = $this->medodos->complementoRegistro($totalremessa,'zeros').$cfbremessa; //Número sequencial do arquivo 		
		$A09 =  '04'; //VersÃ£o do lay-out
		$debito = 17 - strlen("DEBITO AUTOMATICO");
		$A10 = "DEBITO AUTOMATICO".''.$this->medodos->complementoRegistro($debito,'brancos');  //Identificação do Serviço =
		$A11 = $this->medodos->complementoRegistro(52,'brancos'); //Reservado para o futuro 	
			
		$dadosAtualizaBancos = array(
				'cfbremessa' => $A08,
				'banco'=> $A05
		);
			
		$this->atualizaBancos($dadosAtualizaBancos);

		$dadoBanco['linha_a']   = $A01.''.$A02.''.$A03.''.$A04.''.$A05.''.$A06.''.$A07.''.$A08.''.$A09.''.$A10.''.$A11;
		$dadoBanco['num_banco'] = $A05;
	     	     
	  //if($result == $ultimoDigito){			
			return $dadoBanco;			
		//}else{
			//return false;
		//}
	
	
	}
	/*
	 * lay-out bradesco s/a
	 */
	public function bradesco_sa($cfbremessa,$cfbconvenio){
		
		pg_query($this->conn, "BEGIN");
		
		$A01 = "A"; //Código do registro 
		$A02 = 1; //Código de remessa   1 - REMESSA  - Enviado pela Empresa para o Banco. 
		$totalconvenio =  19 - strlen($cfbconvenio);			
		$A03  = "0".$cfbconvenio.''.$this->medodos->complementoRegistro($totalconvenio,'brancos'); //Código do Convênio 
		$A04 = "SASCAR Tec Seg Ltda "; // Nome da Empresa
		$Banco = '237'; 
		$totalBanco =  3 - strlen($Banco);
		$A05 = $this->medodos->complementoRegistro($totalBanco,'zeros').$Banco; //Código do Banco 
		$nomeBanco = 20 - strlen("BANCO BRADESCO S.A");			
		$A06 = "BANCO BRADESCO S.A".$this->medodos->complementoRegistro($nomeBanco,'brancos'); // Nome do Banco 
		$data = 8 - strlen(date("Ymd"));
		$A07 = $this->medodos->complementoRegistro($data,'zeros').date("Ymd"); //Data de geraçõo do arquivo (AAAAMMDD);
	    $totalremessa = 6 - strlen($cfbremessa);			
		$A08 = $this->medodos->complementoRegistro($totalremessa,'zeros').$cfbremessa; //Número sequencial do arquivo 		
		$A09 =  '05'; //Versão do lay-out
		$debito = 17 - strlen("DEBITO AUTOMATICO");
		$A10 = "DEBITO AUTOMATICO".''.$this->medodos->complementoRegistro($debito,'brancos');  //Identificação do Serviço =
		$A11 = $this->medodos->complementoRegistro(52,'brancos'); //Reservado para o futuro 	
			
		$dadosAtualizaBancos = array(
				'cfbremessa' => $A08,
				'banco'=> $A05
		);
			
		$this->atualizaBancos($dadosAtualizaBancos);

		$dadoBanco['linha_a']   = $A01.''.$A02.''.$A03.''.$A04.''.$A05.''.$A06.''.$A07.''.$A08.''.$A09.''.$A10.''.$A11;
		$dadoBanco['num_banco'] =  $A05;
	    
	   return $dadoBanco;
	}
	
    public function logDebitoAutomatico($value)	{
    	
		try{			
		  return  $this->dao->logDebitoAutomatico($value);
		  
		}catch(Exception $e){
			pg_query($this->conn, "ROLLBACK");
			return FALSE;
		}
	}
	
	public function atualizaBancos($value){
		try{			
			return $this->dao->atualizaBancos($value);
			
		}catch(Exception $e){
			pg_query($this->conn, "ROLLBACK");
			return FALSE;
		}
	}
	
	public function titulo($value){
		try{
		return $this->dao->tituloDao($value);
		
		}catch(Exception $e){
			pg_query($this->conn, "ROLLBACK");
			return FALSE;
		}
	}
		
	protected function diretorio($cfbbanco){
		
		return $this->dao->diretorioDao($cfbbanco);
	}
	
	
	
	
	
	/*
	 * Medodo pra geração do arquivo
	 */
	public function gerarArquivo($dadosBancos){
		
		if($dadosBancos){
			// inicio de bancos ativos
		   foreach ($dadosBancos as $valor) {		
	 
					$forcoid = $valor->forcoid;
					$cfbconvenio = $valor->cfbconvenio;
					$cfbnomebase = $valor->cfbnome;
					$cfbremessa = $valor->cfbremessa;
					$cfbbanco = $valor->cfbbanco;
					
					$datahoras = date('d/m/Y H:m:s');					
					
					$bancoAtivo = $this->medodos->BancosAtivos($cfbbanco);			
					
					$cfbnome = $bancoAtivo['cfbnome'];
					$nomeArq = $bancoAtivo['nomeArq'];
										
				//print "<pre>";	print_r($bancoAtivo); 
		         if($nomeArq !=""){
		         	
				    $cfbnome = $this->medodos->tiraAcento($cfbnome);
					 
					$metodosBancos = strtolower(str_replace('/','',str_replace(" ", "_",$cfbnome)));
					//echo $metodosBancos."<br>";
					if ($metodosBancos == "banco_santander_sa" || $metodosBancos == "banco_itau_sa" || $metodosBancos == "banco_do_brasil_sa" || $metodosBancos == "hsbc_bank_brasil_sa" || $metodosBancos == "bradesco_sa") {	
						
					
						//  busca todos os dados da tabela paramtros
						   $dadosParametrosDebitos = $this->dadosParametrosDebitos();
						   
						$r = 1;	
						foreach ($dadosParametrosDebitos as $value) {				
							// metodo que busca o cabeçalho do arquivo
							$cfbremessa = $cfbremessa + $r;					   
							$dadoBancoA = $this->$metodosBancos($cfbremessa,$cfbconvenio);	
												
						if ($dadoBancoA != FALSE) {		
						
									$pdadt_inicio_faturamento = $value['pdadt_inicio_faturamento'];
									$pdadt_fim_faturamento = $value['pdadt_fim_faturamento'];
									$pdadt_email_aviso = $value['pdadt_email_aviso'];
									$pdames_referencia = $value['pdames_referencia'];
									$pdadt_envio_arquivo = $value['pdadt_envio_arquivo'];					
									//print $pdadt_envio_arquivo."<br>";
									// verificar se esta dentro da data de envio.	
												
									if($pdadt_envio_arquivo == Date('d')){
										// verifica se se esta no desenvolvimento de teste
										// if($_SESSION['servidor_teste']){
										// 	if($pdadt_email_aviso !='')
										// 	  $pdadt_email_aviso = _EMAIL_TESTE_;
										// }
										
									  	// busca o diretorio pelo codigo do banco
									 	$result =  $this->diretorio($cfbbanco);	
										$arquivo = $result[0]->laycaminho;
										
										if($_SESSION['servidor_local'] == 2){					
											$arquivo = _SITEDIR_.'arq_financeiro/'; // arquivo local para teste	
										}	
										
										//exit;
										if(is_dir($arquivo)){											
											  
								         //  busca todos os dados de importação para o outro banco
										$consultaExtraida = $this->dadosConsultaExtraida($pdadt_inicio_faturamento, $pdadt_fim_faturamento,$pdames_referencia, $forcoid);
											
										$dado ='';	
										$dadoE = '';
										$erro =  0;	
										$dadoz= '';				
										//verificar se tem algum consulta e ser gerada no arquivo
										if($consultaExtraida){		
									
											//$titoidArray  = array('' => , );
															
											// criação dos dados de importação E																		
												switch ($metodosBancos) {
													case 'banco_santander_sa':														
														$result = $this->medodos->Santader($consultaExtraida);														
														if($result['erro'] != 1){
															$dadoE = $result['dadoE'];
														    $totalRegistro = $result['totalRegistro'];	
															$titoidArray = $result['titoidArray'];
															$valor_corrigido = $result['totalDebito'];
														}else{
															$erro = $result['erro'];
															$dados = $result['registro'];															
														}														
													break;
													case 'banco_itau_sa':
														$result = $this->medodos->itau($consultaExtraida);
														if($result['erro'] != 1){
															$dadoE = $result['dadoE'];
														    $totalRegistro = $result['totalRegistro'];	
															$titoidArray = $result['titoidArray'];
															$valor_corrigido = $result['totalDebito'];
														}else{
															$erro = $result['erro'];
															$dados = $result['registro'];															
														}											
													break;
													case 'hsbc_bank_brasil_sa':
														$result = $this->medodos->hsbc($consultaExtraida);
														if($result['erro'] != 1){
															$dadoE = $result['dadoE'];
														    $totalRegistro = $result['totalRegistro'];	
															$titoidArray = $result['titoidArray'];
															$valor_corrigido = $result['totalDebito'];
														}else{
															$erro = $result['erro'];
															$dados = $result['registro'];															
														}													
													break;
													case 'bradesco_sa':
														$result = $this->medodos->bradesco($consultaExtraida);
														$dadoE = $result['dadoE'];
														$totalRegistro = $result['totalRegistro'];	
														$titoidArray = $result['titoidArray'];
														$valor_corrigido = $result['totalDebito'];
													break;
													case 'banco_do_brasil_sa':
														$result = $this->medodos->banco_do_brasil($consultaExtraida);
														$dadoE = $result['dadoE'];
														$totalRegistro = $result['totalRegistro'];	
														$titoidArray = $result['titoidArray'];
														$valor_corrigido = $result['totalDebito'];
													break;										
												}			
											
											  // cria os conteudo do email
											$diaFaturamento = 	$this->medodos->diaFaturamento($pdames_referencia, $pdadt_inicio_faturamento, $pdadt_fim_faturamento);
												
											if($erro == 0){
													
												$dadosEmail.= "<tr>
														          <td>".$cfbnome."</td>
														          <td>Sucesso</td>
														          <td>Total: ".$totalRegistro." Titulos</td>
													          </tr><br>";	
															
												$msgtotal = "<br> Foi gerado o arquivo com Sucesso para o banco <b>".$cfbnome."</b> no total de ".$totalRegistro." Titulos";													

												
												//grava log 
												$valueLogDebitoAutomatico = array(
														'ldaacfbbanco' =>  $dadoBancoA['num_banco'],
														'ldaanumero_remessa' => $cfbremessa ,
														'ldaadt_remessa' => date("Ymd"),
														'ldaaobs' => "Foi gerado o arquivo com Sucesso para o banco ".$cfbnome." no total de ".$totalRegistro." Titulos"
												);
													
												$this->logDebitoAutomatico($valueLogDebitoAutomatico);
												
												
												// faz as contagem  de espaço e zero no Z
												$cod = strlen($totalRegistro+2);	
												$totalReg = 6 - $cod; 	
												
												$cod = strlen($valor_corrigido);	
												$corrigido = 17 - $cod; 	       
												   
											    $dadoz = "\r\n".'Z'.$this->medodos->complementoRegistro($totalReg,'zeros').''.($totalRegistro + 2).''.$this->medodos->complementoRegistro($corrigido,'zeros').''.$valor_corrigido.''.$this->medodos->complementoRegistro(126,'brancos')."\r\n";
												//montado todos os registro para ser colocando dentro do arquivo
												$dado = $dadoBancoA['linha_a'].''.$dadoE.''.$dadoz;											
																	
												$TotalZeroBanco = 6 - strlen($cfbremessa);						
										    	
									       		$arquivogerador = $arquivo.''.$nomeArq.''.$this->medodos->complementoRegistro($TotalZeroBanco,'zeros').$cfbremessa.'.rem';  // $dia = date('d-m');	 $cfbconvenio.'_'.strtolower(str_replace('/','',str_replace(" ", "_",$metodosBancos.' '.$dia))).'.rem';
											    $r = $r+1;
													
													$criar = fopen($arquivogerador, "w+");
													
														if($criar == false){
														    $mensagem = 'Impossível criar o arquivo';	
															pg_query($this->conn, "ROLLBACK");															
																if($pdadt_email_aviso !=""){											   													  	
																   
																	$diaFaturamento = 	$this->medodos->diaFaturamento($pdames_referencia, $pdadt_inicio_faturamento, $pdadt_fim_faturamento);
																
																	$dadosEmail.= "<tr>
																			          <td>".$cfbnome."</td>
																			          <td>Erro</td>
																			          <td>".$mensagem."</td>
																			        </tr><br>"; 
																
															         $dados = array(
																			'dtahoras' => $datahoras,
																			'diaFaturamento' => $diaFaturamento,
																			'email' => $pdadt_email_aviso,
																			'banco' => $cfbnome,
																			'tituloFuncionalidade' => $this->tituloFuncionalidade, 
																			'dadosEmail'=> $dadosEmail);
																								
																	
																	$resultado = $this->enviarEmail($dados);
																
																	print($dadosEmail);
															  }											
													}
													// abre o arquivo colocando o ponteiro de escrita no final
													$arquivogerador = fopen($arquivogerador,'a+');
													if ($arquivogerador) {
														// move o ponteiro para o inicio do arquivo
														rewind($arquivogerador);
														if (!fwrite($arquivogerador, $dado)){ 
																
															$mensagem ='Não foi possível atualizar o arquivo.';	
															pg_query($this->conn, "ROLLBACK");
															    if($pdadt_email_aviso !=""){
																		  	
																        $diaFaturamento = 	$this->medodos->diaFaturamento($pdames_referencia, $pdadt_inicio_faturamento, $pdadt_fim_faturamento);
																	   
																		$dadosEmail.= "<tr>
																				          <td>".$cfbnome."</td>
																				          <td>Erro</td>
																				          <td>".$mensagem."</td>
																				        </tr>"; 
																	
																	         $dados = array(
																					'dtahoras' => $datahoras,
																					'diaFaturamento' => $diaFaturamento,
																					'email' => $pdadt_email_aviso,
																					'banco' => $cfbnome,
																					'tituloFuncionalidade' => $this->tituloFuncionalidade, 
																					'dadosEmail'=> $dadosEmail
																			);
																								
																	$resultado = $this->enviarEmail($dados);
																
																	print($dadosEmail);
															  }							
														}		
														//echo 'Arquivo atualizado com sucesso';
														fclose($arquivogerador);
														
														pg_query($this->conn, "COMMIT");
													}	
											   }elseif ($erro == 1) {
													
													pg_query($this->conn, "ROLLBACK");
													
													$dadosEmail.= "<tr>
															          <td>".$cfbnome."</td>
															          <td>Erro: Numero da Conta incorreta.</td>
															          <td>".$dados."</td>
															        </tr><br>"; 
															        
													$msgtotal = '<br>Erro: Numero da Conta incorreta. Para o banco:<b> '.$cfbnome.'</b>';  
											   } 
											
											}else{
												
												if($pdadt_email_aviso !=""){
													  	
													$diaFaturamento = 	$this->medodos->diaFaturamento($pdames_referencia, $pdadt_inicio_faturamento, $pdadt_fim_faturamento);	
																								
													$msg = utf8_decode("<br /> Não há títulos para enviar do banco:<b> ".$cfbnome.'</b>.');
													
													$dadosEmail.= utf8_decode("<tr>
																          <td>".$cfbnome."</td>
																          <td>Não Foi Gerado</td>
																          <td>Não há títulos para enviar</td>
																        </tr>");  
												
												         $dados = array(
																'dtahoras' => $datahoras,
																'diaFaturamento' => $diaFaturamento,
																'email' => $pdadt_email_aviso,
																'banco' => $cfbnome,
																'tituloFuncionalidade' => $this->tituloFuncionalidade, 
																'dadosEmail'=> $dadosEmail
														);
														
													$resultado = $this->enviarEmail($dados);
													 								 	
													print($msg); 
												}
											}

											if($titoidArray){
											   foreach ($titoidArray as $titoid) {												
												$dadosAtualizaTitulos = array(
																	'cfbremessa' => $cfbremessa,
																	'titoid'=> $titoid
																	   );
											
											     $this->titulo($dadosAtualizaTitulos);
												 
											  }
											}
											
										   print(utf8_decode($msgtotal));
										
										}else{
											 							
										  if($pdadt_email_aviso !=""){
										  	
										  		$mensagem = utf8_decode("<br>Diretório não encontrado para o banco: <b>".$cfbnome.'</b>');
											    $dadosEmail ='';
												
												$diaFaturamento = 	$this->medodos->diaFaturamento($pdames_referencia, $pdadt_inicio_faturamento, $pdadt_fim_faturamento);
												
												$dadosEmail.= "<tr>
														          <td style='color: #A60000'>".$cfbnome."</td>
														          <td style='color: #A60000'>Erro</td>
														          <td style='color: #A60000'>".$mensagem."</td>
														        </tr><br>"; 
															
											
											         $dados = array(
															'dtahoras' => $datahoras,
															'diaFaturamento' => $diaFaturamento,
															'email' => $pdadt_email_aviso,
															'banco' => $cfbnome,
															'tituloFuncionalidade' => $this->tituloFuncionalidade, 
															'dadosEmail'=> $dadosEmail
													);
																		
													$resultado = $this->enviarEmail($dados);
													 								 	
												print(utf8_decode("<br>ERRO: Diretório não encontrado!")); 
												die();
													 
											}
													
								}
						 }else{			 	
								 	if($msg ==''){
								 	    $msg =  "<br />Não há data de envio configurada para o <b>".$cfbnome."</b> no dia ".date('d/m/Y');
								 	    print utf8_decode($msg);
								 	}
						 }
					   }else{			  	           
							$dadosEmail.= utf8_decode("
							                    <tr>
										          <td>".$cfbnome."</td>
										          <td>Erro</td>
										          <td> Convênio inválido!</td>
										        </tr><br>"); 
										        
								$msgtotal = utf8_decode('<br>Erro: Convênio inválido! Para o banco: <b>'.$cfbnome.'</b>'); 
								
								print $msgtotal;
								
					  }
				   }
			 	}	 	
			 	}else{
			 		print utf8_decode($cfbnome);
			 	}
			 	$dadosEmail = "";
			 	$msgtotal = "";	 	
	      }	//fim foreach
	 	// fim de banco ativo
	 	 
	 	 if($pdadt_email_aviso !="" and $dadosEmail !=""){						
	         $dados = array(
					'dtahoras' => $datahoras,
					'diaFaturamento' => $diaFaturamento,
					'email' => $pdadt_email_aviso,
					'banco' => $cfbnome,
					'tituloFuncionalidade' => $this->tituloFuncionalidade, 
					'dadosEmail'=> $dadosEmail
			);
									
			$resultado = $this->enviarEmail($dados);
		}									
	 	  
		}else{
			print utf8_decode("<br>O Arquivo não foi gerado. Favor ativa um banco!");
		}
	}
	
	
	  protected function enviarEmail($value){	 	
	
		$dtahoras = $value['dtahoras'];
		$diaFaturamento = $value['diaFaturamento'];
		$pdadt_email_aviso = $value['email'];
		$dadosEmail = $value['dadosEmail'];
		$tituloFuncionalidade = $value['tituloFuncionalidade'];
		$banco = $value['banco'];	
	
        $cadLayoutEmails = new CadLayoutEmailsDAO();
		$servicoEnvioEmail = new ServicoEnvioEmail();
		$tp = $cadLayoutEmails->getTituloFuncionalidade(utf8_decode($tituloFuncionalidade));
		
		$dados = array(
					'seeseetoid' => $tp[0]['titulo_id'],
					'seeseefoid' => $tp[0]['funcionalidade_id']
				 );
	    
		$html_email = $cadLayoutEmails->getLayoutEmails($dados);
		
		$seecabecalho = $html_email[0]['seecabecalho'];
		$seecorpo = $html_email[0]['seecorpo'];
		$seesrvoid = $html_email[0]['seesrvoid'];

		$html_email = str_replace('[dtahoras]', $dtahoras, $html_email[0]['seecorpo']);
		$html_email = str_replace('[diaFaturamento]', $diaFaturamento, $html_email);

		$dadosEmail = 
			"<table>
			<tr>
			  <th>Banco</th>
			  <th>Status</th>
			  <th>Obs</th>
			</tr>
			".$dadosEmail."
		    </table>";

		$html_email = str_replace('[dadosEmail]', $dadosEmail, $html_email);
		
			//die(print $html_email);		
		$envio_email = $servicoEnvioEmail->enviarEmail(
						$pdadt_email_aviso,
						$seecabecalho,
						$html_email,
						$diretorio = NULL,
						$email_copia = null,
						$email_copia_oculta = null,
						$seesrvoid,							
						_EMAIL_TESTE_,
						$tituloFuncionalidade
				     );

		return $envio_email;

		//	print_r($envio_email);
		// if(!empty($envio_email['erro'])){
		// 	$msgEmail = $envio_email['msg'].' - '.$pdadt_email_aviso;
		//     echo $msgEmail;
		// }
	
	}


}


