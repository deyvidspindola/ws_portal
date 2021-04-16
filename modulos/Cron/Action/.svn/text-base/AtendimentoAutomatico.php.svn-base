<?php
require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * Serviço automático de tratamento de panicos pendentes.
 *
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @version 18/03/2013
 * @since   18/03/2013
 */
class AtendimentoAutomatico extends CronAction {
	/**
	 * @property UraAtivaDAO
	 */
	protected $dao;

	/**
 	 * Executa as regras
	 * @return CronView
	 */
	public function executar(UraAtivaDAO $dao) {

		$this->dao = $dao;

		$this->dao->transactionBegin();

		try {

			$contatos = $this->dao->buscarContatos($CronParcial);

			//Verifica se  Não deve enviar ao discador. Apenas aplicar regras de descarte.
			if($CronParcial != 'P'){

				$this->dao->enviarDiscador($contatos);

				if(!empty($contatos)){
					$this->notificarDiscador();

                    if($this->dao->isGravaLogAtendimento){
                        $this->gravarLogAtendimento($this->dao->nomeCampanha, "_envio_", $this->dao->logEnviados);

                        echo "ENVIADOS:<br>";
                        foreach($this->dao->logEnviados as $linha) {
                            echo $linha . "<br>";
                        }
                    }
				}
			}

            if(($this->dao->isGravaLogAtendimento) && (!empty($this->dao->logAtendimento))) {
                $this->gravarLogAtendimento($this->dao->nomeCampanha, "_descarte_", $this->dao->logAtendimento);

                echo "DESCARTADOS:<br>";
                foreach($this->dao->logAtendimento as $linha) {
                    echo $linha . "<br>";
                }

            }

			$this->dao->transactionCommit();

		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();

			$this->dao->transactionRollback();
		}

		return $this->view;
	}

	/**
	 * Notifica o discador que foi inserido novos contatos
	 * @return void
	 */
	protected function notificarDiscador() {

            $campanha = $this->dao->getCampanha();

            //Endereço do WS
           global $WS_XMLRPC;

            try{
                //Parametros do metodo
                $param = new stdClass();
                $param->Operacao = 'SincronizarDados';

                //Cria o XML
                $xml = xmlrpc_encode_request("Campanha{$campanha}.IOControl", $param);
                echo "<br/>Chamada de sincronização: Campanha{$campanha}.IOControl<br/>";
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
                    <p>Erro na chamada XMLRPC de sincronia do discador para atendimentos de ". $this->dao->nomeCampanha . ".<br />
                    Data da chamada: {$agora} </p>
                    <p>Retorno da chamada:<br />
                    {$e->getMessage()}</p>";

                //Envia o email
                //$this->enviarEmail("wendricson.castro@meta.com.br", 'Sascar informa', $msgHtml);
                $this->enviarEmail("uraativaalertas@sascar.com.br", 'Sascar informa', $msgHtml);
            }
	}

    protected function gravaLog($arquivo, $texto) {
        try{
            //Arquivo de log
        	$diretorio = "/var/www/docs_temporario/";
        	if(is_writable($diretorio)){

	            $fp = fopen($diretorio . $arquivo, "a+");

	            if ($fp){
	                $data = date('d/m/Y H:i:s');

	                fwrite($fp, "::: Data - " . $data . "\n");
	                fwrite($fp, $texto . "\n");

                     fclose($fp);
	            }
        	}

        } catch (Exception $e){
        	$this->view->msg = $e->getMessage();
        }
    }

        /**
         * Enviar um e-mail informativo
         * @param type $destinatarioEmail Email do destinatario
         * @param type $texto Corpo do email
         * @return void
         */
	protected function enviarEmail($destinatarioEmail, $titulo, $texto){

		$mail = new PHPMailer();

		$mail->ClearAllRecipients();

		$mail->IsSMTP();
		$mail->From = "sascar@sascar.com.br";
		$mail->FromName = "Sascar";

		$mail->Subject = $titulo;

		$mail->MsgHTML($texto);

		$mail->AddAddress($destinatarioEmail);

		return $mail->Send();

	}


        /**
 	 * Verifica insucessos do discador
	 * @return CronView
	 */
	public function insucessos(UraAtivaDAO $dao) {

		$this->dao 	= $dao;

        /*
         * Instancia a Action conforme campanha
         */
        if($this->dao->nomeCampanha == "assistencia") {

            require_once _MODULEDIR_ . 'Atendimento/Action/UraAtivaAssistencia.php';
            $action = new UraAtivaAssistencia($this->conn);

        }
        else if($this->dao->nomeCampanha == "estatistica") {

            require_once _MODULEDIR_ . 'Atendimento/Action/UraAtivaEstatistica.php';
            $action = new UraAtivaEstatistica($this->conn);
        }


		$this->dao->transactionBegin();

		try {

			$action->tratarInsucessos();

			if(($this->dao->isGravaLogAtendimento) && (!empty($action->logAtendimento))){
				$this->gravarLogAtendimento($this->dao->nomeCampanha, "_insucesso_", $action->logAtendimento);

                echo "INSUCESSOS:<br>";
                foreach($action->logAtendimento as $linha) {
                    echo $linha . "<br>";
                }

			}

			$this->dao->transactionCommit();

		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();

			$this->dao->transactionRollback();
		}
		return $this->view;
	}

	/**
	 * Executa as regras para limpar os registros que não devem ser mais considerados
	 * @param UraAtivaDAO $dao
	 * @return CronView
	 */
	public function limpar(UraAtivaDAO $dao) {

		$this->dao = $dao;
        $arrDeletados = array();
        $arrDeletados1 = array();
        $arrDeletados2 = array();

		$this->dao->transactionBegin();

		try {

            $deletados = $this->dao->limpar();

            echo "DELETADOS:<br>";

            foreach($deletados as $chave => $items) {

               if ($chave == 'ignorar') {

                   foreach ($items as $item) {
                        $linha = $item->igpveioid . " | " . $item->igpconoid . " | Ignora Panico";
                        $arrDeletados1[] = $linha;
                        echo $linha . "<br>";
                   }
               } else {
                   foreach ($items as $item) {
                        $linha = $item;
                        $arrDeletados2[] = $linha;
                        echo $linha . "<br>";
                   }
               }
           }

           $arrDeletados = array_merge($arrDeletados1,$arrDeletados2);

           /*
            * Grava arquivo de log
            */
           if(!empty($deletados)) {
               if($this->dao->isGravaLogAtendimento) {
                    $this->gravarLogAtendimento($this->dao->nomeCampanha, "_ignora_panico_", $arrDeletados);
                }
           }
           else {
               echo "Não há pânicos há ignorar.";
           }

            $this->dao->transactionCommit();

		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();

			$this->dao->transactionRollback();
		}

		return $this->view;
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
		$caminho = "/var/www/logs_ura/";

		$nomeArquivo = $campanha . $processo . date("Y_m_d") . ".txt";

        switch ($processo) {
            case '_insucesso_':
                if($campanha == 'assistencia'){
                    $cabecalho = "COD CLIENTE | NOME | ORDENS SERVICO";
                }
                else if($campanha == 'panico'){
                    $cabecalho = "CONTRATO | COD CLIENTE | COD VEICULO | PANICO | MOTIVO";
                }
                else {
                    $cabecalho = "CONTRATO | COD CLIENTE | COD VEICULO | MOTIVO";
                }

                break;

            case '_descarte_':
            case '_verificar_os_':
                if($campanha == 'assistencia'){
                    $cabecalho = "ORDEM SERVICO | COD CLIENTE | NOME CLIENTE | MOTIVO DESCARTE";
                }
                else{
                    $cabecalho = "CONTRATO | COD CLIENTE | NOME CLIENTE | MOTIVO DESCARTE";
                }
                break;

             case '_ignora_panico_':
                $cabecalho = "COD VEICULO | CONTRATO | MOTIVO";
                break;

            default:
                 //Envio / Reenvio
                if($campanha == 'assistencia'){
                    $cabecalho = "COD CLIENTE | NOME CLIENTE | PARAMETROS INSERT DISCADOR";
                }
                else {
                    $cabecalho = "CONTRATO | COD CLIENTE | NOME CLIENTE | PARAMETROS INSERT DISCADOR";
                }
                break;
        }

        //Cria o diretorio caso não exista
        if(!is_dir($caminho)) {
            mkdir($caminho, 0700);
        }

		//Grava Arquivo
		try{

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
				} else {
                    echo "Falha ao abrir o arquivo: " . $nomeArquivo;
                }

			}else{
				echo "Permisão negada para gravar o arquivo de log no diretório: " . $diretorio;
			}

		}catch(Exception $e){
            echo "Erro ao gravar arquivo de Log.";
		}
	}

	/**
	 * Inicia o processod e verificação das OS que devem ter status de eliminado no discador.
	 * @param UraAtivaAssistenciaDAO $dao
	 * @param UraAtivaAssistencia $assistencia
	 * @return CronView
	 */
	public function verificarDiscador(UraAtivaAssistenciaDAO $dao, UraAtivaAssistencia $assistencia){

		$this->dao = $dao;

		try {

			$this->dao->transactionBegin();

			$logEliminados = $assistencia->verificarOrdemServicoDiscador();

            if(!empty($logEliminados)) {

                if($this->dao->isGravaLogAtendimento) {
                    $this->gravarLogAtendimento($this->dao->nomeCampanha, "_verificar_os_", $logEliminados);
                }

                echo "ELIMINADOS:<br>";
                foreach($logEliminados as $linha) {
                    echo $linha . "<br>";
                }

            } else {

                echo "Nenhuma OS foi Eliminada.";
            }

			$this->dao->transactionCommit();


		} catch (Exception $e) {

			$this->view->msg = $e->getMessage();
			$this->dao->transactionRollback();
		}

		return $this->view;
	}

	/**
	 * Inicia o processo de Reenvio dos contatos para o Discador
	 * @param UraAtivaDAO $dao
	 * @return CronView
	 */
	public function reenviarContatos(UraAtivaDAO $dao){

		$this->dao = $dao;

		$this->dao->transactionBegin();

		try {

			$contatos = $this->dao->buscarContatosReenvio();

			$this->dao->enviarDiscador($contatos);

			if(!empty($contatos)){

				$this->notificarDiscador();

                if($this->dao->isGravaLogAtendimento){
                    $this->gravarLogAtendimento($this->dao->nomeCampanha, "_reenvio_", $this->dao->logEnviados);

                    echo "REENVIADOS:<br>";
                    foreach($this->dao->logEnviados as $linha) {
                        echo $linha . "<br>";
                    }
                }
			}else {
                echo "Nenhum contato reenviado.";
            }

			$this->dao->transactionCommit();

		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();

			$this->dao->transactionRollback();
		}

		return $this->view;

	}

}
