<?php

require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';
require_once _MODULEDIR_ . 'Cron/DAO/AgendamentoDAO.php';
require_once _MODULEDIR_ . 'Cron/VO/OrdemSituacaoVO.php';
require_once _MODULEDIR_ . 'Cron/VO/LogEnvioEmailVO.php';
require_once _MODULEDIR_ . 'Cron/VO/OrdemServicoInsucessoVO.php';

// Busca o layout referente à proposta do cliente
//require_once _MODULEDIR_ . 'Cadastro/DAO/CadLayoutEmailsDAO.php';
require_once _MODULEDIR_ . 'Cadastro/Action/SendLayoutEmails.php';

// Servico de envio de email
require_once _MODULEDIR_ . 'Principal/Action/ServicoEnvioEmail.php';

//require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * @EnviaEmailInsucessosAgendamento.php
 * 
 * Serviço automático de envio de e-mails para insucessos de agendamento.
 * 
 * @author 	Alex S. Médice
 * @email   alex.medice@meta.com.br
 * @version 20/11/2012
 * @since   20/11/2012
 */
class EnviaEmailInsucessosAgendamento extends CronAction {

	const HORA 					= '21:00';
	const NOME_PROCESSO 		= 'envia_email_insucessos_agendamento.php';
	const FROM 					= 'mensageiro@sascar.com.br';		// NÃO UTILIZADO - STI 81463
	const FROM_NAME 			= 'Sascar';							// NÃO UTILIZADO - STI 81463
	const TIPO_LOG_SUCESSO 		= 'S';
	const TIPO_LOG_INSUCESSO 	= 'I';
	const MSG_ENVIO_SUCESSO 	= 'Sucesso de Envio';
	const MSG_ENVIO_INSUCESSO 	= 'Insucesso de Envio';
	

	const TITULO1				= 'Insucesso de Agendamento - 1';			// STI 81463 - BUSCA LAYOUT - ENVIO EMAIL
	const TITULO2				= 'Insucesso de Agendamento - 2';			// STI 81463 - BUSCA LAYOUT - ENVIO EMAIL
	const TITULO3				= 'Insucesso de Agendamento - 3';			// STI 81463 - BUSCA LAYOUT - ENVIO EMAIL
	
	
	private $titulo;			// Nome do titulo de acorco com a quantidade de tentativas
	private $titulo_id;			// ID TITULO			// STI 81463 - BUSCA LAYOUT - ENVIO EMAIL
	private $funcionalidade_id;	// ID FUNCIONALIDADE	// STI 81463 - BUSCA LAYOUT - ENVIO EMAIL
	
	
	/**
	 * Data de referencia para consulta das OS
	 * 
	 * @var date
	 */
	private $date;
	
	// DAO cadastro de layout
	private $layout;
	
	// Serviço de envio de email
	private $servicoEmail;
	
	/**
	 * @var array $request
	 * @return void
	 */
	public function __construct($request) {
		 
		parent::__construct($request);
		
		$this->date = date('Y-m-d');
		
		try {
			$this->dao = new AgendamentoDAO($this->conn);
			
			$this->layout = new SendLayoutEmails();
			$this->servicoEmail = new ServicoEnvioEmail();
			
		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();
		}
	}
	
	/**
	 * Envia e-mail para insucessos de agendamento das Ordens de Serviço.
	 *
	 * @return CronView
	 */
	public function enviaEmailInsucessos() {
		$this->dao->transactionBegin();
		
		try {
			$this->verificarProcesso(self::NOME_PROCESSO);
			
			// Carrega insucessos não instalção
			$contatosI = $this->dao->contatosComInsucessos($this->date);
			$this->atualizarSituacao($contatosI);
			
			// Carrega insucessos Instalação SIGGO
			$contatosS = $this->dao->contatosComInsucessosSiggo($this->date);
			$this->atualizarSituacao($contatosS);
			
			$contatos = count($contatosI) + count($contatosS);

			$this->dao->transactionCommit();		
			
			$this->view->msg = 'Total de contatos: ' . $contatos;
			
		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();
			
			$this->dao->transactionRollback();
		}
		
		return $this->view;
	}
	
	/**
	 * Verifica se o processo não está travado ou se ainda está rodando
	 * 
	 * @param string $nomeProcesso
	 * @throws CronException
	 * @return void
	 */
	private function verificarProcesso($nomeProcesso) {
		if (burnCronProcess($nomeProcesso) === true) {
			throw new CronException("ERRO: Processo [" . $nomeProcesso . "] ainda está rodando.");
		}
	}
	
	private function atualizarSituacao($clientes) {
		
		foreach ($clientes as $clioid => $contatos) {
			$qtdOrdem 			= count($clientes[$clioid]);
			$subject 			= '';
			$layoutEmail 		= '';
			$layoutEmailInfo 	= '';
			$infos 				= array();
			
			$tipo_proposta		= '';
			$subtipo_proposta	= '';
			$tipo_contrato		= '';

			foreach ($contatos as $contato) {
				$ordoid = $contato->ordem;
				
				switch ($contato->qtdtentativas) {
					case 3:
						$tipoEmail = 1;
						$situacaoStatus = AgendamentoDAO::STATUS_EMAIL_INSUCESSO_1;
						break;
					case 4:
						$tipoEmail = 2;
						$situacaoStatus = AgendamentoDAO::STATUS_EMAIL_INSUCESSO_2;
						break;
					case 5:
						$tipoEmail = 3;
						$situacaoStatus = AgendamentoDAO::STATUS_EMAIL_INSUCESSO_3;
						break;
					default:
						continue; // @TODO não envia
						break;
				}
								
				// Tipo contrato, proposta e subtipo proposta do cliente
				if (!empty($contato->proposta_supertipo)){
					$tipo_proposta = $contato->proposta_supertipo;
					$subtipo_proposta = $contato->proposta_tipo;
				}else{
					$tipo_proposta = $contato->proposta_tipo;
					$subtipo_proposta = '';
				}
					
				$tipo_contrato = $contato->contrato_tipo;
				
				// Pega o titulo de acordo com a quantidade de tentativas para o cliente
				$this->titulo = $this->getTitulo($contato->qtdtentativas);
				
				// BUSCA O CODIGO DO TITULO E FUNCIONALIDADE DE ACORDO COM O TITULO ENCONTRADO
				$config = $this->dao->getTituloFuncionalidade($this->titulo);

				if (!empty($config)){
					$this->titulo_id = $config->titulo_id;
					$this->funcionalidade_id = $config->funcionalidade_id;
				}
						
				//if (empty($layoutEmail)) {
				//$idTipoEmail 	= $this->getIdTipoEmail($tipoEmail, $qtdOrdem);
				//$layout 			= $this->dao->getLayoutEmail($idTipoEmail);
				//}
				
				
				/** BUSCAR LAYOUT **/
				$getEmail = array(
						'seeseefoid'		=> $this->funcionalidade_id,
						'seeseetoid'		=> $this->titulo_id,
							
						'supertipo'			=> $tipo_proposta,
						'prptppoid'			=> $subtipo_proposta,
						'prptpcoid'			=> $tipo_contrato,
				);

				$layout_id = $this->layout->buscaLayoutEmail($getEmail);
			

				if($layout_id['seeoid']){
									
					$layout = $this->dao->getLayoutEmail($layout_id['seeoid']);
					
					$servidor 			= $layout->seesrvoid;
					$subject 			= $layout->seecabecalho;
					$layoutEmail 		= $layout->seecorpo;
					$layoutEmail		= str_replace('""','"',$layoutEmail);
					
					$len = strlen('[LAYOUT_INFOS]');
					$layoutEmailIni 	= substr($layoutEmail, 0, strpos($layoutEmail, '[LAYOUT_INFOS]'));
					$layoutEmailFim 	= substr($layoutEmail, (stripos($layoutEmail, '[/LAYOUT_INFOS]') + ($len + 1)));
					$layoutEmailInfo 	= substr($layoutEmail, strpos($layoutEmail, '[LAYOUT_INFOS]'));
					$layoutEmailInfo 	= substr($layoutEmailInfo, $len, (stripos($layoutEmailInfo, '[/LAYOUT_INFOS]') - $len));
					
					$htmlEmailInfo = $layoutEmailInfo;
					$ordemServicoInsucessoVO = new OrdemServicoInsucessoVO($contato);
					foreach ($ordemServicoInsucessoVO as $key => $value) {

						$key = strtoupper($key);

						$htmlEmailInfo = str_ireplace('['.$key.']', $value, $htmlEmailInfo);
					}
					
					$infos[] = $htmlEmailInfo;

				}
			}


			$layoutEmail  = $layoutEmailIni;
			$layoutEmail .= implode('', $infos);
			$layoutEmail .= $layoutEmailFim;		
			
			$htmlEmail 		= $this->gerarHtmlEmail($ordemServicoInsucessoVO, $layoutEmail, $servidor);
			
			$destinatarios 	= array($contato->email1, $contato->email2);

			$this->gravarSituacaoOs($ordoid, $situacaoStatus, $htmlEmail);
			
			$tipoLog = self::TIPO_LOG_SUCESSO;
				
			try {
				error_reporting(E_ALL || ~E_NOTICE || ~E_DEPRECATED); // @TODO para testes sem E_DEPRECATED
					
				// UTILIZAÇÃO DO SERVIÇO DE ENVIO DE EMAIL - STI 81463
				$email_teste 			= "";
				$arquivo_anexo			= "";
				$email_copia			= "";
				$email_copia_oculta 	= "";
				

				if($_SESSION['servidor_teste'] == 1){
	            
		            //recupera email de testes da tabela parametros_configuracoes_sistemas_itens
		            $emailTeste = $this->dao->getEmailTeste();
		                        
		            if(!is_object($emailTeste)){
		                throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
		            }

		            $destinatarios 	= array($emailTeste->pcsidescricao);
		            $email_teste    = $emailTeste->pcsidescricao;
	  
		        }
						
				$returno_email = $this->servicoEmail->enviarEmail(  $destinatarios, 
																	$subject, 
																	$htmlEmail, 
																	$arquivo_anexo, 
																	$email_copia, 
																	$email_copia_oculta, 
																	$servidor, 
																	$email_teste 
				);
							

				//STI - 82914	
								
				//recupera o id da proposta para inserir o histórico				
                $id_proposta = $this->getIdPropostaOS($ordoid);

                //usuário automático do CRON
                $id_usuario = 2750;

                //se erro ao enviar e-mail
                if ($returno_email['erro'] > 0){
					
					echo $retorno_email['msg'];

					//texto de não envio que será exibido na histórico da proposta
				    $msg = "E-mail ".$this->titulo." não enviado. ";

					//inserir histórico de não envio na proposta	
            	    $historicoProposta = $this->dao->registrarHistoricoProposta($msg, $id_proposta->prpoid, $id_usuario);
					
					throw new CronException($retorno_email['msg']);
				}

				//texto que será exibido na histórico da proposta
				$msg = "E-mail ".$this->titulo."  enviado. ";

				//inserir histórico de envio na proposta	
            	$historicoProposta = $this->dao->registrarHistoricoProposta($msg, $id_proposta->prpoid, $id_usuario);	

				//FIM STI - 82914
				
				
				//$this->enviarEmailNovaSituacao($destinatario, $subject, $htmlEmail);
				// FIM STI 81463
					
			} catch (Exception $e) {
				$tipoLog = self::TIPO_LOG_INSUCESSO;
			}
							
			$this->gravarLog($ordoid, $tipoLog, $tipoEmail);
		
		}
	}
	

	private function getIdPropostaOS($ordoid){

		return $this->dao->getIdPropostaOS($ordoid);		
	}


	private function getTitulo($qtdTentativas) {			
		if ($qtdTentativas > 1) {
			switch ($qtdTentativas) {
				case 3:
					$titulo = self::TITULO1;
					break;
				case 4:
					$titulo = self::TITULO2;
					break;
				case 5:
					$titulo = self::TITULO3;
					break;
			}
		}

		return $titulo;
	}	
	
    private function getIdTipoEmail($tipoEmail, $qtdOrdem) {
    	$id = $tipoEmail;
    	
    	if ($qtdOrdem > 1) {
    		switch ($tipoEmail) {
    			case 1:
    				$id = 4;
    			break;
    			case 2:
    				$id = 5;
    			break;
    			case 3:
    				$id = 6;
    			break;
    		}
    	}
    	
    	return $id;
    }
	
    private function getLayoutEmail($tipoEmail) {
		$layout = $this->dao->getLayoutEmail($tipoEmail);
		
		if (!$layout) {
			throw new CronException('Layout não existe: ' . $layout);
		}
    	
		return $layout;
    }
	
    private function gerarHtmlEmail(OrdemServicoInsucessoVO $ordemServicoInsucessoVO, $html, $servidor = null) {
    	
    	// Busca a logo de acordo com o localizador do servidor
    	$logo = "";
    	
    	// Busca o numero localizador do servidor
    	$localizador = $this->dao->getLocalizadorServidor($servidor); 	
    	
    	switch ($localizador){
    		case '1':
    			$logo = "logotipo_sascar_email.png";
    			break;
    			
    		case '2':
    			$logo = "logotipo_VAREJO.png";
    			break;

    		//case '3':	
    		//	break;
    			
    		default:
    			$logo = "logotipo_sascar_email.png";
    			break;	
    		
    	}

    	if ($logo != ""){
    		
    		//$caminho = _PROTOCOLO_ . "desenvolvimento.sascar.com.br/sistemaWeb/";
    		//$caminho = _PROTOCOLO_ . "desenvolvimento.sascar.com.br/sistemaWeb/modulos/web/images/" . $logo;
    		//$img = "<img src='$caminho' /> ";
    		
    		//$html = str_ireplace('[PATH_LOGO]', "$img" , $html);
    		$html = str_ireplace('[PATH_LOGO]', "" , $html);
    	}
    	    	    	
    	foreach ($ordemServicoInsucessoVO as $key => $value) {
    		
    		$key = strtoupper($key);

    		$html = str_ireplace('['.$key.']', $value, $html);
    	}
    	    	    	
    	return $html;
    }
	
    /** FUNÇÃO DE ENVIO SUBISTITUIDA - STI 81463 **/
	private function enviarEmailNovaSituacao($destinatario, $subject, $html) {
		
// 		$this->travarResponder();

		$mail = new PHPMailer();
		$mail->ClearAllRecipients();
		$mail->isSmtp();
		$mail->IsHTML(true);
		 
		$mail->From 		= self::FROM;
		$mail->FromName 	= self::FROM_NAME;
		$mail->Subject 		= $subject;
		
		$mail->MsgHTML($html);
		if( $_SESSION['servidor_teste'] == 1 ){
			$mail->AddAddress("josiane.paula@sascar.com.br");
		} else {
			$mail->AddAddress($destinatario);
		}
		
		if(!$mail->Send()){
			throw new CronException('Não foi possível enviar email para destinatário(s): '.$mail->ErrorInfo.'.');
		}
	}
	
	/**
	 * TODOS os e-mails deverão conter uma trava, para que os clientes sejam impedidos de responder tais e-mails
	 * 
	 * @return void
	 */
	private function travarResponder() {
    	// @TODO
		throw new CronException("not implemented travaResponder");
	}
	
	/**
	 * Insere a situação atual da OS
	 * 
	 * @param int $ordoid
	 * @param 1|2|3 $status
	 * @param text $obs
	 * @throws CronException
	 * @return void
	 */
	private function gravarSituacaoOs($ordoid, $status, $obs) {
		$ordemSituacaoVO = new OrdemSituacaoVO();

		$ordemSituacaoVO->orsordoid 	= $ordoid;
		$ordemSituacaoVO->orsstatus 	= $status;
		$ordemSituacaoVO->orssituacao 	= $obs;
	
		$this->dao->novaSituacaoOs($ordemSituacaoVO);
	}
	
	/**
	 * Com base na geração de logs será desenvolvido o relatório “Envio de e-mail automático”.
	 * 
	 * @param int $leeordoid
	 * @param S|I $leetipo_log
	 * @param int $leesseoid
	 * @throws CronException
	 * @return void
	 */
	private function gravarLog($ordoid, $tipoLog, $tipoEmail) {
		$logEnvioEmailVO = new LogEnvioEmailVO();

		$logEnvioEmailVO->leeordoid 	= $ordoid;
		$logEnvioEmailVO->leesseoid 	= $tipoEmail;
		$logEnvioEmailVO->leetipo_log 	= $tipoLog;
		$logEnvioEmailVO->leeobs 		= ($tipoLog == self::TIPO_LOG_SUCESSO) ? self::MSG_ENVIO_SUCESSO : self::MSG_ENVIO_INSUCESSO;
		
		$this->dao->novoLogEnvioEmail($logEnvioEmailVO);
	}
	
	private function insetLayouts() {
		exit('insetLayouts');
		$corpo = '';
		$numLayout = 6;
		
		$corpo = pg_escape_string($corpo);
		$corpo = utf8_encode($corpo);
	
		$sql = "UPDATE servico_envio_email 
				SET 
				seecabecalho = 'Insucesso de agendamento ".$numLayout."', 
				seedescricao = 'Layout ".$numLayout." para mais de uma placa corresponde ao registro', 
				seecorpo = '".$corpo."' 
				WHERE seeoid = ".$numLayout.";";
		
		$rs = pg_query($sql);
		echo '<pre>';
		print_r($rs);
		exit;
	}
}