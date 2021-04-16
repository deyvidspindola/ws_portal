<?php 

/*
 * Classe para envio de emails
 */
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';


abstract class UraAtiva {
	/**
	 * @property AtendimentoAutomaticoDAO
	 */
	protected $dao;
	
	/**
	 * Trata a reposta da URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	 */
	abstract public function navegacao(UraAtivaParamVO $ParametrosUra);

	/**
	 * Busca informações adicional para URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	*/
	abstract public function informacoesAdicionais(UraAtivaParamVO $param);
	
	/**
	 * Enviar um e-mail informativo 
	 * @param unknown $destinatarioEmail
	 * @param unknown $titulo
	 * @param unknown $texto
	 * @return boolean
	 */
	public function enviarEmail($destinatario, $titulo, $texto){
		
		$destinatario = trim($destinatario);
		
		$mail = new PHPMailer();
	
		$mail->ClearAllRecipients();
	
		$mail->IsSMTP();
		$mail->From = "sascar@sascar.com.br";
		$mail->FromName = "Sascar";
	
		$mail->Subject = $titulo;
	
		$mail->MsgHTML($texto);
		
		if ($_SESSION['servidor_teste'] == 1) {
			$destinatario = "rodrigo.alasino@meta.com.br";			
		}		

		$mail->AddAddress($destinatario);		
		
		return $mail->Send();
	
	}
	
	/**
	 * Grava o arquivo de Log do Atendimento
	 * @param string $campanha
	 * @param string $processo
	 * @param array $conteudo
	 */
	public function gravarLogAtendimento($campanha, $processo, $conteudo){

		$campanha = trim($campanha);		
		$campanha = strtolower($campanha);
		$campanha = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($campanha, "áàãâéêíóôõúüç", "aaaaeeiooouuc"));		
		
		$nomeArquivo 	= $campanha . $processo . date("dmY") . ".txt";	
		$caminho 		= "/var/www/docs_temporario/";
		$conteudo		= utf8_encode($conteudo);
		
		if($processo == '_ws_consulta_'){
			
			if($campanha == 'assistencia'){			
				$cabecalho = "DATA/HORA | ORDENS SERVIÇO | COD CLIENTE | NOME | PARAMETROS ENTRADA |  PARAMETROS SAIDA \n\n";
			}
			else{
				$cabecalho = "DATA/HORA | CONTRATO | COD CLIENTE | NOME | PARAMETROS ENTRADA |  PARAMETROS SAIDA \n\n";
			}
		}
		else{//ws entrada
			
			if($campanha == 'assistencia'){
				$cabecalho = "DATA/HORA | ORDEM SERVIÇO | COD CLIENTE | NOME | OPÇAO SEL. | TEL. CONTATADO | HORA CONTATO | PARAMETROS ENTRADA \n\n";
			}
			else{
				$cabecalho = "DATA/HORA | CONTRATO | COD CLIENTE | NOME | OPÇAO SEL. | TEL. CONTATADO | HORA CONTATO | PARAMETROS ENTRADA \n\n";
			}			
		}
		
		if(file_exists($caminho . $nomeArquivo)){
			$cabecalho = '';
		}
		
		//Grava Arquivo
		try{
			$fp = fopen($caminho . $nomeArquivo, "a+");
	
			if ($fp){
				
				fwrite($fp, $cabecalho);
				fwrite($fp,  $conteudo . "\n");
							
			}
			
			fclose($fp);
			
		}catch(Exception $e){
	
			$titulo = "Erro no log do atendimento automatico";
			$texto = "Erro ao gravar o log: " . $nomeArquivo  . " | " . $e;
			$destinatarioEmail = "rodrigo.alasino@meta.com.br";
					
			
			self::enviarEmail($destinatarioEmail, $titulo, $texto);
				
		}
	}	

	/**
	 * formata a data retornada pelo DB2
	 * @param string $dataContatoDB2
	 * @return string
	 */
	public function formatarDataContatoDB2($dataContatoDB2){
		 
		if(!empty($dataContatoDB2)){
			 
			$hora 			= substr($dataContatoDB2, 11, 5);
			$hora			= str_replace('.', ':', $hora);
			 
			$dataContatoDB2	= substr($dataContatoDB2, 0, 10);
			$dataPart 		= explode('-',substr($dataContatoDB2, 0, 10));
			$dataContatoDB2 	= $dataPart[2] . "/" . $dataPart[1] . "/" . $dataPart[0] . " " . $hora;
			 
		}else{
			$dataContatoDB2 = '';
		}
		 
		return $dataContatoDB2;
		 
	}
	
	
	/**
	 * Notifica o discador que foi inserido novos contatos
	 * @return void
	 */
	protected function notificarDiscador(UraAtivaDAO $dao) {
	
		$campanha = $dao->getCampanha();
		$nomeCampanha = $dao->nomeCampanha;
		
		//Endereço do WS
		global $WS_XMLRPC;
			
		try{
			//Parametros do metodo
			$param = new stdClass();
			$param->Operacao = 'SincronizarDados';
	
			//Cria o XML
			$xml = xmlrpc_encode_request("Campanha{$campanha}.IOControl", $param);
	
			//Contexto
			$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml",
					'content' => $xml
			)));
	
			//Grava log do envio
			$this->gravaLog("ativadiscador_ura_xmlrpc.txt", "Efetuando chamada XMLRPC para {$WS_XMLRPC} - XML:\n{$xml}");
			//Efetua a chamada XMLRPC
			$file = file_get_contents($WS_XMLRPC, false, $context);
	
			if ($file === false){
				throw new Exception("Não foi possivel conectar com o destino - {$WS_XMLRPC}");
			} else{
				//Grava o log do envio concluido
				$this->gravaLog("ativadiscador_ura_xmlrpc.txt", "Chamada concluida.");
			}
			//Decodifica o xml
			$response = xmlrpc_decode($file);
	
			if (xmlrpc_is_fault($response)) {
				throw new Exception($response['faultString'], $response['faultCode']);
			} else {
				//Grava o log de recebimento
				$this->gravaLog("ativadiscador_ura_xmlrpc.txt", "Resposta de {$WS_XMLRPC} - XML:\n{$file}"); //
	
				if ($response['Resposta'] != 'OK'){
					throw new Exception($response['Resposta'] . ' - ' . $response['Comentario']);
				}
			}

	
		} catch (Exception $e){
	
			/**
			 * Dispara um email informando do erro na chamada XMLRPC
			 */
			//Data e hora
			$agora = date('d/m/Y H:i');
			//Mensagem em html
			$msgHtml = "
			<p>Erro na chamada XMLRPC de sincronia do discador para atendimentos de ". $nomeCampanha . ".<br />
			Data da chamada: {$agora} </p>
			<p>Retorno da chamada:<br />
			{$e->getMessage()}</p>";
	
			//Envia o email
			$this->enviarEmail("andre.zilz@meta.com.br", 'Sascar informa', $msgHtml);
			//$this->enviarEmail("uraativaalertas@sascar.com.br", 'Sascar informa', $msgHtml); //@TODO: Descomentar essa linha e remover a linha acima, no ambiente de produção
		}
	}
	
	/**
	 * Grava Log e Notificação ao Discador
	 * @param string $arquivo
	 * @param string $texto
	 */
	protected function gravaLog($arquivo, $texto) {
				
		try{
			
			$fp = fopen("/var/www/docs_temporario/" . $arquivo, "a+");
	
			if ($fp){
				$data = date('d/m/Y H:i:s');
	
				fwrite($fp, "::: Data - " . $data . "\n");
					fwrite($fp, $texto . "\n");
	
				}
            	fclose($fp);
	
			}catch (Exception $e){
				//
			}
	}
	
}