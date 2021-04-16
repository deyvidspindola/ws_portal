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
	public function gravarLogAtendimento($campanha, $processo, $conteudo, $erro = false){

		$campanha = trim($campanha);
		$campanha = strtolower($campanha);
		$campanha = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($campanha, "áàãâéêíóôõúüç", "aaaaeeiooouuc"));
        $caminho = "/var/www/logs_ura/";

		$nomeArquivo = $campanha . $processo . date("Y_m_d") . ".txt";

        //WS informação
		if($processo == '_ws_consulta_'){

			if($campanha == 'assistencia'){
                 if($erro) {
                    $cabecalho = "PARAMETROS ENTRADA | EXCEPTION";
                }else {
                    $cabecalho = "COD CLIENTE | NOME | PARAMETROS ENTRADA |  PARAMETROS SAIDA | ORDENS SERVICO";
                }
			}
			else{
                if($erro) {
                    $cabecalho = "PARAMETROS ENTRADA | EXCEPTION";
                }else {
                    $cabecalho = "CONTRATO | COD CLIENTE | PARAMETROS ENTRADA |  PARAMETROS SAIDA";
                }

			}
		}
        //WS navegacao
		else{

			if($campanha == 'assistencia'){
                if($erro){
                    $cabecalho = "PARAMETROS ENTRADA | OPCAO SELECIONADA | EXCEPTION";
                }else{
                    $cabecalho = "ORDEM SERVICO | COD CLIENTE | NOME | OPCAO SELECIONADA | TEL. CONTATADO | HORA CONTATO | PARAMETROS ENTRADA";
                }
			}
			else{
                if($erro){
                    $cabecalho = "PARAMETROS ENTRADA | OPCAO SELECIONADA | EXCEPTION";
                }else{
                    $cabecalho = "CONTRATO | COD CLIENTE | OPCAO SELECIONADA | TEL. CONTATADO | DATA/HORA CONTATO | PARAMETROS ENTRADA ";
                }

			}
		}

		//Cria o diretorio caso não exista
        if(!is_dir($caminho)) {
            mkdir($caminho, 0700);
        }

		//Grava Arquivo
		if(is_writable($caminho)){

            $fp = fopen($caminho . $nomeArquivo, "a+");

            if($fp){

                if(!empty($cabecalho)){
                    fwrite($fp, "=================================================================================================\n");
                    fwrite($fp, "--" . date("H:i"). "--" . "\n\n");
                    fwrite($fp, $cabecalho . "\n\n");
                }

                foreach($conteudo as $linha) {
                    fwrite($fp, $linha . "\n");
                }

                fclose($fp);
            }
        }
	}

	/**
	 * formata a data retornada pelo DB2
	 * @param string $dataContatoDB2
	 * @return string
	 */
	public function formatarDataContatoDB2($dataContatoDB2){

		if(!empty($dataContatoDB2)){

			$hora = substr($dataContatoDB2, 11, 5);
			$hora = str_replace('.', ':', $hora);

			$dataContatoDB2	= substr($dataContatoDB2, 0, 10);
			$dataPart 		= explode('-',substr($dataContatoDB2, 0, 10));
			$dataContatoDB2 = $dataPart[2] . "/" . $dataPart[1] . "/" . $dataPart[0] . " " . $hora;

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
			$this->enviarEmail("uraativaalertas@sascar.com.br", 'Sascar informa', $msgHtml);
		}
	}

	/**
	 * Grava Log e Notificação ao Discador
	 * @param string $arquivo
	 * @param string $texto
	 */
	protected function gravaLog($arquivo, $texto) {

		try{

			$caminho = "/var/www/docs_temporario/";

			if(is_writable($caminho)){

				$fp = fopen($caminho . $arquivo, "a+");

				if ($fp){
					$data = date('d/m/Y H:i:s');

					fwrite($fp, "::: Data - " . $data . "\n");
						fwrite($fp, $texto . "\n");

					}
	            	fclose($fp);
			}

		}catch (Exception $e){
			//
		}
	}

}