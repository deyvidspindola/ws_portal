<?php

 // INCLUDES
require_once _SITEDIR_ .'lib/config.php';
require_once _SITEDIR_ .'lib/phpMailer/class.phpmailer.php';

require_once _CRONDIR_ .'lib/validaCronProcess.php';

//classe responsável para processar a alteração da forma de pagamento do cliente 
require_once _MODULEDIR_ . 'Principal/Action/PrnManutencaoFormaCobrancaCliente.php';

//classe responsável para recuperar dados dos cartões de crédito
require_once _MODULEDIR_ . 'Cron/DAO/VerificaValidadeCartaoCreditoDAO.php';



/**
 * Rotina do cron para solicitar uma nova forma de pagamento, quando o cartão de crédito do usuário estiver próxima do vencimento,
 * enviando um email ao cliente solicitando para que o mesmo entre em contato com a Sascar para informar uma nova forma de pagamento.
 *
 * @file VerificaValidadeCartaoCredito.php
 * @author marcioferreira
 * @version 15/05/2013 15:20:31
 * @since 15/05/2013 15:20:31
 * @package SASCAR VerificaValidadeCartaoCredito.php
 */

class VerificaValidadeCartaoCredito{

	//atributos
	private $conn;
	private $limiteDiasVencimento;
	private $array60dias;
    private $array30dias;
	private	$arrayHoje;
	private $mesVencimento;
	private $cartoesIncluidosMesVencimento;
	
	 // Construtor
	public function __construct() {
		
		global $conn;
		
		//seta variável de conexão
		$this->conn = $conn;
		
		// Objeto  - DAO
		$this->dao = new VerificaValidadeCartaoCreditoDAO($conn);
				
		//parâmetro em dias que faltam para o vencimento do cartão que o sistema irá buscar 
		$this->limiteDiasVencimento = array(60,30,'hoje');

	}
	
	/**
	 * Recupera dados dos clientes que estão com cartão a vencer ou vencidos e envia e-mail informando
	 * @author Márcio Sampaio ferreira 
	 */
	public function verificarVencimentoCartoes(){ 
		
		try{

			$nomeProcesso = 'verifica_validade_cartao_credito.php';

			if(burnCronProcess($nomeProcesso) === true){
				throw new Exception (" O processo [$nomeProcesso] ainda está em processamento.");
			}

			if(!$this->conn){
				throw new Exception (" Erro ao conectar-se no banco de dados.");
			}

			//busca os clientes de acordo com a quantidade de dias limite, e com pelo menos um contrato ativo
			foreach ($this->limiteDiasVencimento as $key => $dias) {

				//busca os cartões que irão vencer
				$cartaosVencendo = $this->dao->getCartoesVencendo($dias);
				
				if (count($cartaosVencendo) > 0) {
					
					$this->setArrayDadosCartoes($tipo="", $dias, $cartaosVencendo);
					
					//exibe os dados encontrados no browser para efeito de teste
					echo $this->montaHtmlApresentacaoTela($tipo="", $dias, $cartaosVencendo);
											
				} else {

					if($dias == 'hoje'){
						echo "Não há cartões vencendo <b> hoje </b>  para processar<br/><br/>";
					}else{
						echo "Não há cartões com vencimento em <b>" . $dias . "</b> dias  para processar<br/><br/>";
					}
				}
			}//fim da busca

			
			//pesquisa cartões que foram incluídos no mês do vencimento
			$this->cartoesIncluidosMesVencimento = $this->dao->getCartoesIncluidosMesVecimento();
			
			if(count($this->cartoesIncluidosMesVencimento) > 0){
			
				//seta o array com os dados dos cartões
				$this->setArrayDadosCartoes($tipo = 'MV', "", $this->cartoesIncluidosMesVencimento);
				
				//exibe os dados encontrados no browser para efeito de teste
				echo $this->montaHtmlApresentacaoTela($tipo = 'MV', null, $this->cartoesIncluidosMesVencimento );
			
			}else{
				echo "Não há cartões  <b> com inclusão e vecimento neste mês </b>  para processar <br/><br/>";
			}
		

			//efetua a consulta dos dados dos emails para envio, somente se foram encontrados cartões vencendo
			if (count($this->array60dias) > 0 || count($this->array30dias) > 0 || count($this->arrayHoje) > 0 || count($this->mesVencimento) > 0) {

				//Ao teminar de efetuar a busca todos os clientes com o cartões que irão vencer, recupera dados do banco para montar e enviar e-mails
				$dadosEmail = $this->dao->getDadosCorpoEmailCartoesVencendo();
				
				//se encontrou conteúdo para enviar os e-mails, então, inicia o processo
				if (count($dadosEmail) > 0) {

					//envia email para clientes com vencimento em 60 dias
					if (count($this->array60dias) > 0) {

						$htmlRelatorio = "";

						foreach ($this->array60dias as $dados60dias) {
							//envia e-mail para o cliente
							$this->enviaEmailClienteCartaoVencimento($dadosEmail, $dados60dias);
						}

						$htmlRelatorio = $this->montaRelatorioEnvioEmail($this->array60dias);
						//envia e-mail para contasareceber@sascar.com.br com os cartões que irão vencer em 60 dias
						$this->enviaEmailRelatorio($paramDia = 60, $htmlRelatorio );
					}
					
					//envia email para clientes com vencimento em 30 dias
					if (count($this->array30dias) > 0) {

						$htmlRelatorio = "";

						foreach ($this->array30dias as $dados30dias) {
							//envia e-mail para o cliente
							$this->enviaEmailClienteCartaoVencimento($dadosEmail, $dados30dias);
						}

						$htmlRelatorio = $this->montaRelatorioEnvioEmail($this->array30dias);
						//envia e-mail para contasareceber@sascar.com.br com os cartões que irão vencer em 30 dias
						$this->enviaEmailRelatorio($paramDia = 30, $htmlRelatorio );
					}
					
					//envia email para clientes com cartões vencendo no mês da inclusão, informando a quantidade
					//de dias que faltam para o vencimento
					if(count($this->mesVencimento) > 0){
							
						$htmlRelatorio = "";
							
						foreach ($this->mesVencimento as $dadosCartoes) {
								
							//envia email para clientes com vencimento de cartões na data corrente
							$this->enviaEmailClienteCartaoVencimento($dadosEmail, $dadosCartoes);
						}
							
						$htmlRelatorio = $this->montaRelatorioEnvioEmail($this->mesVencimento, $tipo = 'MV');
						//envia e-mail para contasareceber@sascar.com.br com os cartões que estão vencendo na data corrente
						$this->enviaEmailRelatorio($paramDia = 'MV', $htmlRelatorio);
					}

				} else {
					echo 'Não foi possível continuar com o processo de cartões vencendo, layout de e-mail não encontrado.<br/>';
					exit;
				}
				
				//se o vencimento é na data corrente, troca a forma de pagamento dos clientes para boleto e envia email informando
				if (count($this->arrayHoje) > 0) {

					//recupera corpo do email					
					$dadosEmailHoje = $this->dao->getDadosCorpoEmailCartoesVencidos();

					//se encontrou conteúdo para enviar os e-mails, então, inicia o processo
					if (count($dadosEmailHoje) > 0) {

						$htmlRelatorio = "";
						
						foreach ($this->arrayHoje as $dadosHoje) {

							//efetua a alteração da forma de pagamento do cliente para BOLETO
							$this->alteraFormaPagamentoCliente($dadosHoje);
							
							//envia email para clientes com vencimento de cartões na data corrente
							$this->enviaEmailClienteCartaoVencimento($dadosEmailHoje, $dadosHoje);
						}

						$htmlRelatorio = $this->montaRelatorioEnvioEmail($this->arrayHoje);
						//envia e-mail para contasareceber@sascar.com.br com os cartões que estão vencendo na data corrente
						$this->enviaEmailRelatorio($paramDia = 'hoje', $htmlRelatorio);

					}else{
						echo 'Não foi possível continuar com o processo de cartões vencendo hoje, layout de e-mail não encontrado.<br/>';
						exit;
					}
				}

					
				//fim do processo					
				echo 'Processo finalizado.</br></br>';
				return true;

			} else {
				echo 'Não existem dados para processar. Ação cancelada. <br/><br/> Os e-mails para os clientes não foram enviados.<br/>';
				exit;
			}


		}catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
	}

	
	/**
	 * Método resonsável por setar os atributos com os dados dos cartões que foram econtrados
	 * @author Márcio Sampaio ferreira
	 *
	 * @param string $tipo  // MV - Mês do vencimento
	 * @param int    $dias
	 * @param array  $dados
	 */
	private function setArrayDadosCartoes($tipo, $dias, $dados){
		
		if(count($dados) > 0){

			for($i = 0; $i < count($dados); $i++){

				//popula os arrays com os dados dos clientes de acordo a quantidade de dias do vencimento para enviar os e-mails posteriormente
				if (trim($dias) == 60) {
					$this->array60dias[$i]->data_validade = $dados[$i]['data_validade'];
					$this->array60dias[$i]->dias_vencer   = $dados[$i]['dias_vencer'];
					$this->array60dias[$i]->id_cliente    = $dados[$i]['id_cliente'];
					$this->array60dias[$i]->nome_cliente  = $dados[$i]['nome_cliente'];
					$this->array60dias[$i]->email_cliente = $dados[$i]['email_cliente'];

				} elseif (trim($dias) == 30) {
					$this->array30dias[$i]->data_validade = $dados[$i]['data_validade'];
					$this->array30dias[$i]->dias_vencer   = $dados[$i]['dias_vencer'];
					$this->array30dias[$i]->id_cliente    = $dados[$i]['id_cliente'];
					$this->array30dias[$i]->nome_cliente  = $dados[$i]['nome_cliente'];
					$this->array30dias[$i]->email_cliente = $dados[$i]['email_cliente'];

				} elseif (trim($dias) == 'hoje') {
					$this->arrayHoje[$i]->data_validade = $dados[$i]['data_validade'];
					$this->arrayHoje[$i]->dias_vencer   = $dados[$i]['dias_vencer'];
					$this->arrayHoje[$i]->id_cliente    = $dados[$i]['id_cliente'];
					$this->arrayHoje[$i]->nome_cliente  = $dados[$i]['nome_cliente'];
					$this->arrayHoje[$i]->email_cliente = $dados[$i]['email_cliente'];

				}elseif (trim($tipo) == 'MV'){
					$this->mesVencimento[$i]->data_validade = $dados[$i]['data_validade'];
					$this->mesVencimento[$i]->dias_vencer   = $dados[$i]['dias_vencer'];
					$this->mesVencimento[$i]->data_inclusao  = $dados[$i]['data_inclusao'];
					$this->mesVencimento[$i]->tempo_inclusao_cartao   = $dados[$i]['tempo_inclusao_cartao'];
					$this->mesVencimento[$i]->id_cliente    = $dados[$i]['id_cliente'];
					$this->mesVencimento[$i]->nome_cliente  = $dados[$i]['nome_cliente'];
					$this->mesVencimento[$i]->email_cliente = $dados[$i]['email_cliente'];
				}
			}
			
			return true;

		}else{
				
			echo 'Não há dados para exibir';
			return false;
		}
	}
	
	/**
	 * Monta o html na tela em tempo de execução para fins de testes e verificação dos dados recuperados
	 * @author Márcio Sampaio ferreira
	 * 
	 * @param string $tipo  // MV - Mês do vencimento 
	 * @param int    $dias
	 * @param array  $dados
	 */
	private function montaHtmlApresentacaoTela($tipo, $dias, $dados){
		
		$html= "";

		if (count($dados) > 0) {

			$html ="<style>
							table{
								border-collapse: collapse; font-family: Arial;
								margin-top: 10px;
							}
		
							.tituloTd{
								border: 1px #000 solid;
								font-weight: bolder;
								font-size: 12px;
							}
		
							.dadosTd{
								border: 1px #000 solid;
								font-size: 11px;
							}
		
							</style>";
		
			if($tipo == 'MV'){
				$html .= "Vencendo <b>este mês</b> ";
				
			}elseif($dias == 'hoje'){
				$html .= "Vencendo <b>hoje</b> ";
				
			}else{
				$html .= "Vencimentos em <b>$dias </b> dias ";
			}
		
			$html .= "<table>";
			$html .="<tr>";
			$html .="<td class='tituloTd' width=150 align=center> Data da Validade</td>";
			$html .="<td class='tituloTd' width=100 align=center> Qtde de dias para vencer</td>";
			if($tipo == 'MV'){
				$html .="<td class='tituloTd' width=100 align=center> Data Inclusão</td>";
				$html .="<td class='tituloTd' width=100 align=center> Qtde de dias Incluído</td>";
			}
			$html .="<td class='tituloTd' width=100 align=center> ID Cliente</td>";
			$html .="<td class='tituloTd' width=300 align=left>   Nome Cliente</td>";
			$html .="<td class='tituloTd' width=300 align=left>   E-mail Cliente</td>";
			$html .="</tr>";
				
			for($i = 0; $i < count($dados); $i++){
		
				$html .="<tr>";
				$html .="<td class='dadosTd' align=center> " . $dados[$i]['data_validade'] . " </td>";
				$html .="<td class='dadosTd' align=center> " . $dados[$i]['dias_vencer'] . " </td>";
				if($tipo == 'MV'){
					$html .="<td class='dadosTd' align=center> " . $dados[$i]['data_inclusao'] . " </td>";
					$html .="<td class='dadosTd' align=center> " . $dados[$i]['tempo_inclusao_cartao'] . " </td>";
				}
				$html .="<td class='dadosTd' align=center> " . $dados[$i]['id_cliente'] . " </td>";
				$html .="<td class='dadosTd' align=left>   " . $dados[$i]['nome_cliente'] . " </td>";
				$html .="<td class='dadosTd' align=left>   " . $dados[$i]['email_cliente'] . " </td>";
				$html .="</tr>";
				
			}
		
			$html .="</table> ";
			$html .="<br/><br/> ";
		
			return $html;
			
		}else{
			echo 'Dados não encontrados para exibir.<br/><br/>';
			return $html;
		}		
		
	}

	/**
	 * Monta o relatório como os clientes que estão com cartão a vencer ou vencidos
	 * @author Márcio Sampaio ferreira
	 *
	 * @param array $arrayDados
	 * @return string $html
	 */
	private function montaRelatorioEnvioEmail($arrayDados, $tipo = null) {
		
	    $html = "";
	
	    $html .="<br/><br/>";
	    
	    if ($arrayDados[0]->dias_vencer == 0) {
	        $html .= "Relação dos Cartões de Crédito Vencendo <b>hoje</b> ( ".date('d-m-Y')." ) ";
	    } else {
	        $html .= "Relação dos Cartões de Crédito vencendo ";
	    }
	
	    $html .= "<table style='border-collapse: collapse; font-family: Arial; margin-top: 10px;'>";
	    $html .="<tr>";
	        $html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=150 align=center> Data da Validade</td>";
	        $html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=100 align=center> Qtde de dias Vencer</td>";
	        if($tipo == 'MV'){
	        	$html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=150 align=center> Data Inclusão</td>";
	        	$html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=100 align=center> Qtde de dias Incluído</td>";
	        }
	        $html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=100 align=center> ID Cliente</td>";
	        $html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=300 align=left>   Nome Cliente</td>";
	        $html .="<td style='border: 1px #CCC solid; font-weight: bolder; font-size: 12px;' width=300 align=left>  E-mail Cliente</td>";
	    $html .="</tr>";
	
	    foreach ($arrayDados as $dados) {
	        
	        $html .="<tr>";
	            $html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=center> " . $dados->data_validade. " </td>";
	            $html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=center> " . $dados->dias_vencer . " </td>";
	            if($tipo == 'MV'){
	            	$html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=center> " . $dados->data_inclusao . " </td>";
	            	$html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=center> " . $dados->tempo_inclusao_cartao . " </td>";
	            }
	            $html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=center> " . $dados->id_cliente . " </td>";
	            $html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=left>   " . $dados->nome_cliente . " </td>";
	            $html .="<td style='border: 1px #CCC solid; font-size: 11px;' align=left>   " . $dados->email_cliente . " </td>";
	        $html .="</tr>";
	    }
	
	    $html .="</table> ";
	    
	    return $html;
	}


	/**
	 * Altera a forma de pagamento do cliente para boleto, caso o cartão de crédito seja recusado
	 *
	 * @author Márcio Sampaio Ferreira
	 *
	 * @param  $dados array
	 * @return boolean
	 */
	private function alteraFormaPagamentoCliente($dados){
	
		try{
	
			if(count($dados) <= 0){
				throw new Exception(' ERRO - Faltam dados para efetuar a alteracao da forma de cobranca do cliente.');
			}
	
			//instância da classe de mannutenção da forma de cobraça de clientes
			$prnManutencaoFormaCobrancaCliente = new PrnManutencaoFormaCobrancaCliente();
	
			//instancia da classe de dados de cobrança
			$prnDadosCobranca = new PrnDadosCobranca();
	
			//recupera a data de cobrança do cliente da tabela clientes
			$dataCobrancaCliente = $prnDadosCobranca->getDataCobrancaCliente($dados->id_cliente);
				
			//verifica o código referente a data
			$codigoDataCobrancaCliente = $prnDadosCobranca->getDiaCobranca(null,null,$dataCobrancaCliente->clidia_vcto);
				
			//data de vencimento do cliente
			$_POST['forma_pagamento_clidia_vcto'] = $codigoDataCobrancaCliente[0]['codigo'];
	
			//envia os dados da forma de pagamento via post
			$_POST['clioid'] = $dados->id_cliente;
	
			//Forma de cobrança Boleto[1]
			//Alterado para CobranÃ§a registrada santande[84] em 04-12-2018 ASM 248246
			$_POST['forcoid'] = 84;
	
			//Canal de Entrada do histórico: I = Intranet; P = Portal
			$_POST['entrada'] = 'I';
	
			// VC - Validade Cartão Crédito
			$_POST['origem_chamada'] = 'VC';
	
			//usuário AUTOMATICO para processos onde não existe autenticação
			$_SESSION['usuario']['oid'] = 2750;
	
			//altera a forma de cobrança do cliente
			$alteraFormaCobranca = $prnManutencaoFormaCobrancaCliente->confirmarFormaPagamento();
	
			if($alteraFormaCobranca['error'] === false){
				return true;
			}else{
				return false;
			}
	
		}catch (Exception $e){
			echo $e->getMessage();
			return false;
		}
	}
	
	
	/**
	 * Envia os emails de acordo com os dados passados via parâmetro
	 * @author Márcio Sampaio ferreira
	 * 
	 * @param array $dadosEmail
	 * @param array $dados 
	 */
	private function enviaEmailClienteCartaoVencimento($dadosEmail, $dados) {
	
	    $htmlEmail->assunto_email = $dadosEmail->assunto_email;
	    //efetua a troca dos parâmetros com os dados do cliente vindos do banco
	    $htmlEmail->corpo_email = str_replace('[NOME_CLIENTE]', $dados->nome_cliente, $dadosEmail->corpo_email);
	    
	    //apenas exibirá a quantidade de dias que faltam para o vencimento do cartão
	    if(trim($dados->dias_vencer) != 0){
	        $htmlEmail->corpo_email = str_replace('[QTDE_DIAS]', $dados->dias_vencer, $htmlEmail->corpo_email);
	    }
	    
	    $mail = new PHPMailer();
	
	    $mail->IsSMTP();
	    $mail->From = "sascar@sascar.com.br";
	    $mail->FromName = "Sistema - Sascar";
	    $mail->Subject = $htmlEmail->assunto_email;
	    $mail->MsgHTML($htmlEmail->corpo_email);
	    
	
	    ###ENVIA EMAIL PARA CLIENTE
	    //produção
	    if ($_SESSION['servidor_teste'] == 0) {
	
	        $mail->AddAddress($dados->email_cliente);
	
	        //outros locais
	    } else {
	        //usuários de teste
	        $mail->AddAddress(_EMAIL_TESTE_);
	        //$mail->AddAddress('dcribeiro@brq.com');
	        
	        print($htmlEmail->corpo_email);
	        print('<br/><br/>');
	    }
	
	    $mail->Send();
	
	    $mail->ClearAllRecipients();
	    
	    return true;
	}


	/**
	 * Envia e-mail para contasareceber@sascar.com.br  com informações dos cliente dos cartões que estão vencendo, 
	 * de acordo os dados passados via parâmetro
	 * 
	 * @param inteiro $paramDia
	 * @param string $html
	 * @return boolean 
	 */
	private function enviaEmailRelatorio($paramDia, $html){
	    
	    $mail = new PHPMailer();
	
	    $mail->IsSMTP();
	    $mail->From = "sascar@sascar.com.br";
	    $mail->FromName = "Sistema - Sascar";
	    
	    if($paramDia == 'MV'){
	    	$mail->Subject = "Cartões de crédito vencendo em menos de 30 dias.";
	    	
	    } elseif($paramDia == 0){
	        $mail->Subject = "Cartões de crédito vencendo hoje ( ".date('d-m-Y')." ) ";
	        
	    }else{
	        $mail->Subject = "Cartões de crédito com vencimento em $paramDia dias";
	    }
	    
	    $mail->MsgHTML($html);
	    
	    //produção
	    if ($_SESSION['servidor_teste'] == 0) {
	
	        $mail->AddAddress('contasareceber@sascar.com.br');
	
	        //outros locais
	    } else {
	        //usuários de teste
	        $mail->AddAddress(_EMAIL_TESTE_);
	        //$mail->AddAddress('dcribeiro@brq.com');
	        
	        print($html);
	        print('<br/><br/>');
	    }
	
	    $mail->Send();
	    
	    $mail->ClearAllRecipients();
	
	    return true;
	    
	}


}

?>
