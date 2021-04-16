<?php

// PHPMailer - Classe PHP de envio de e-mail
//require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

require_once _MODULEDIR_ . 'Principal/DAO/ServicoEnvioEmailDAO.php';

// Busca informações do layout e servidor
require_once _MODULEDIR_ . 'Cadastro/Action/SendLayoutEmails.php';

/**
 * 
 * Classe de envio de e-mail.
 * 
 * @author 	Leandro Alves Ivanaga
 * @email   leandroivanaga@brq.com
 * @version 11/06/2012
 * @since   11/06/2012
 */
 
class ServicoEnvioEmail{
	
	private $erro;		// Controlador de erro
	private $msg;		// Mensagem de erro, quando houver
	private $mail;		
	
	private $dao;
	private $conn;
	private $layout;
	
	private $email_destinatario;
	private $assunto_email;
	private $corpo_email;
	private $nome_remetente;
	private $email_remetente;
	private $arquivo_anexo;
	private $email_copia;
	private $email_copia_oculta;
	
	private $servidor_email;
	private $titulo;
	private $contrato; 
	private $proposta; 
	private $sub_proposta;
	
	private $remetente;
	
	private $host;
	private $host_seguro;
	private $porta;
	private $autenticacao;
	private $usuario;
	private $senha;
	
	private $email_desenvolvedor;
		
	public function ServicoEnvioEmail(){
				
		global $conn;
		
		$this->conn = $conn;
		
		$this->dao = new ServicoEnvioEmailDAO($this->conn);
	}
	
	/**
	 * Função: enviarEmail
	 * 
	 * Realiza verificação se foram passados os parametros obrigatórios.
	 * Realiza envio do e-mail.
	 * Retorna sucesso/insucesso do envio.
	 * Em caso de insucesso, retorna msg com o motivo.
	 * 
	 * @param $email_destinatario	-> E-mail de destino (obrigatório)
	 * @param $assunto_email		-> Assunto do e-mail
	 * @param $corpo_email			-> Mensagem do e-mail (html)
	 * @param $arquivo_anexo		-> Arquivo de anexo (caminho do arquivo)
	 * @param $email_copia			-> E-mail para envio como cópia
	 * @param $email_copia_oculta	-> E-mail para envio como cópia oculta
	 * @param $servidor_email		-> Servidor de saída do e-mail (tabela servidor_email) (obrigátorio)
	 * @param $email_desenvolvedor	-> E-mail do desenvolvedor, envio em caso do servidor de teste
	 * @param $titulo				-> Titulo para busca de layout (Quando não informar o servidor)
	 * @param $contrato 			-> Contrato para busca de layout (Quando não informar o servidor)
	 * @param $proposta				-> Proposta para busca de layout (Quando não informar o servidor)
	 * @param $sub_proposta			-> Sub-Proposta para busca de layout (Quando não informar o servidor)
	 * @param $remetente			-> Endereço de remetente responsavel pelo envio
	 * 
	 */
	
	public function enviarEmail($email_destinatario = null, $assunto_email = null, $corpo_email = null, $arquivo_anexo = null, $email_copia = null, $email_copia_oculta = null, $servidor_email = null, $email_desenvolvedor = null, $titulo = null, $contrato = null, $proposta = null, $sub_proposta = null, $remetente = null){
		try{
			$this->erro = false;
			$this->msg = "";
									
			/** Verifica se foram passados os parâmetros obrigatórios **/
			$this->verificarParametros($email_destinatario, $email_copia, $email_copia_oculta);
			
			// Verifica a ocorrência de erro, na verificação dos parâmetros obrigatórios
			if ($this->erro == true){
				throw new Exception ($this->msg);
			}
			
			/** Seta variáveis com os parâmetros passados **/
				
			$this->email_destinatario 		= $email_destinatario;
			$this->assunto_email 			= $assunto_email;
			$this->corpo_email				= $corpo_email;
			$this->arquivo_anexo			= $arquivo_anexo;
			$this->email_copia				= $email_copia;
			$this->email_copia_oculta		= $email_copia_oculta;
			$this->servidor_email			= $servidor_email;
			$this->email_desenvolvedor		= $email_desenvolvedor;
			$this->titulo					= $titulo;
			$this->contrato					= $contrato;
			$this->proposta					= $proposta;
			$this->sub_proposta				= $sub_proposta;
			$this->remetente				= $remetente;
				
			/** Se não foi passado o servidor, busca de acordo com os parametros: titulo, contrato, proposta, sub_proposta **/
			
			if ($this->servidor_email == null){
				$this->getServidor();
			}			
			
			// Verifica a ocorrência de erro, para buscar o servidor a ser utilizado
			if ($this->erro == true){
				throw new Exception ($this->msg);
			}
			
			
			/** Busca as configurações do servidor **/ 
			$this->getConfigServidor();
			
			// Verifica a ocorrência de erro, na busca dos dados do remetente pelo servidor
			if ($this->erro == true){
				throw new Exception ($this->msg);
			}
			
			
			/** Montagem do e-mail para envio **/
			$this->montaEmail();
			
			// Verifica a ocorrência de erro, na montagem do e-mail
			if ($this->erro == true){				
				throw new Exception ($this->msg);
			}		
			
			/** Função para definir o remetente de envio **/
			$this->setRemetente();
			
			
			/** Realiza envio do email **/
			$sucesso = false;
			
			for ($tentativas = 0; $sucesso == false; $tentativas++){
								
				switch ($tentativas){
					case '0':
						$this->isSmtp();
						$sucesso = $this->Send();
						break;
						
					case '1':
						$this->isMail();
						$sucesso = $this->Send();
						break;
						
					case '2':
						$this->isSendmail();
						$sucesso = $this->Send();
						break;
						
					default:
						$sucesso = true;
						$erro_envio = true;
						break;
				}
				
			}
			
			if ($erro_envio == true){
				throw new Exception ( utf8_decode($this->ErrorInfo)  );
			}
			
			// Montagem do retorno
			$retorno = array(
					"erro"	=> false
			);
				
			// Destrói objeto $mail, evitar lixo em novas chamadas
			unset($this);
				
			return $retorno;
		
		}catch (Exception $e){

			// Destrói objeto $mail, evitar lixo em novas chamadas
			unset($this);
			
			// Ocorreu algum erro, montagem do retorno com o erro
			$retorno = array(
							"erro"	=> true,
							"msg"	=> $e->getMessage()
					);
			return $retorno;
		}
	}
	
	/**
	 * 
	 * Função para montar o e-mail.
	 */
	public function montaEmail(){
		$this->ClearAllRecipients();
		$this->ClearAttachments();
		
		//$this->SetLanguage("br");
			
		$this->IsHTML(true);
		$this->MsgHTML($this->corpo_email);
		
		$this->From 			= $this->email_remetente;
		$this->FromName 		= $this->nome_remetente;
		$this->Subject 			= $this->assunto_email;
				
		$this->Host				= $this->host;
		$this->SMTPSecure		= $this->host_seguro;
		$this->Port				= $this->porta;
		$this->SMTPAuth			= $this->autenticacao;
		$this->Username			= $this->usuario;
		$this->Password			= $this->senha;
		
		if (is_array($this->arquivo_anexo)){
			foreach ($this->arquivo_anexo AS $key => $arquivo){
				$this->AddAttachment($arquivo);
			}
		}else {
			// Verifica se arquivo existe no caminho informado, Se sim -> Adiciona Anexo
			if (file_exists($this->arquivo_anexo)){
				$this->AddAttachment($this->arquivo_anexo);
			}
		}
			
		// Chama função para adicionar os endereços de email
		$this->addEmails();
	}
	
	public function setRemetente(){
		// SE O SERVIDOR FOR DE PRODUÇÃO E
		// SE CONSEGUIU RESGATAR O REMETENTE PELO CADASTRO DE LAYOUT: 
		
		// UTILIZA O NOME REMETENTE COMO RESPONSAVEL PELO ENVIO
		// CASO CONTRÁRIO UTILIZARÁ O REMETENTE QUE ESTÁ CADASTRADO NA TABELA SERVIDOR_EMAIL
		
		if ($_SESSION['servidor_teste'] == 0 || strstr($_SERVER['HTTP_HOST'], 'intranet')){
			if ($this->remetente != null && strlen($this->remetente) > 0){
				$this->From 			= $this->remetente;
				$this->FromName 		= $this->remetente;
			}
		}
	}
	
	
	/**
	 * 
	 * Função que adiciona os endereços que receberão o e-mail.
	 * Adiciona: Endereços para destino, como cópia e como cópia oculta.
	 */
	public function addEmails(){
		try{						
			// Servidor de TESTE
			if ($_SESSION['servidor_teste'] == 1){
				
				// Verifica foi passado um array de endereços de e-mail
				if (is_array($this->email_desenvolvedor) == true){
					
					foreach ($this->email_desenvolvedor as $email){
						$this->AddAddress($email);
					}
				}else{
					$this->AddAddress($this->email_desenvolvedor);
				}
			} 
			
			/** INSERE TODOS OS ENDEREÇOS DE EMAILS **/
				
			// Verifica foi passado um array de endereços de e-mail
			if (is_array($this->email_destinatario) == true){
			
				foreach ($this->email_destinatario as $email){
					$this->AddAddress($email);
				}
			}else{
				$this->AddAddress($this->email_destinatario);
			}
							
			// ADICIONA endereço como cópia, se informado algum
			if (!empty($this->email_copia)){
				
				// Verifica foi passado um array de endereços de e-mail
				if (is_array($this->email_copia) == true){
				
					foreach ($this->email_copia as $email){
						$this->AddCC($email);
					}
				}else{
					$this->AddCC($this->email_copia);
				}
			}
				
			// ADICIONA endereço como cópia oculta, se informado algum
			if (!empty($this->email_copia_oculta)){
					
				// Verifica foi passado um array de endereços de e-mail
				if (is_array($this->email_copia_oculta) == true){
						
					foreach ($this->email_copia_oculta as $email){
						$this->AddBCC($email);
					}
				}else{
					$this->AddBCC($this->email_copia_oculta);
				}
			}
		}catch (Exception $e){
			
			// Ocorreu algum erro, no momento de adicionar os emails
			$this->erro = true;
			$this->msg = $e->getMessage();
		}
	}
	
	
	/**
	 * Verificar se os parametros obrigatórios foram passados
	 * 
	 */
	
	public function verificarParametros($email_destinatario = null, $email_copia = null, $email_copia_oculta = null){
		try{
			
			// Verifica se é ambiente de produção e não foi informado nenhum endereço de destino
			if (($email_destinatario == null && $email_copia == null && $email_copia_oculta == null) && $_SESSION['servidor_teste'] != 1){
				throw new Exception ("Deve ser informado algum e-mail de destino.");
			}
		
		}catch (Exception $e){
			
			$this->erro = true;
			$this->msg = $e->getMessage();
		}	
	}
	
	/**
	 * Busca dados referente ao servidor
	 */
	public function getConfigServidor(){
		try{
			$retorno_servidor = $this->dao->getDadosServidor($this->servidor_email);

			if ($retorno_servidor['erro'] == true){
				throw new Exception ($retorno_servidor['msg']);
			}
			
			// Armazena os dados de remetente cadastrados na base
			$this->nome_remetente 	= $retorno_servidor['srvremetente_nome'];
			$this->email_remetente 	= $retorno_servidor['srvremetente_email']; 
						
			// Armazena os dados do servidor cadastrados na base
			$this->host 			= $retorno_servidor['srvsmtphost'];
			$this->host_seguro 		= $retorno_servidor['srvsmtpseguro'];
			$this->porta 			= $retorno_servidor['srvsmtpporta'];
			$this->autenticacao 	= ($retorno_servidor['srvsmtpautenticacao'] == 't')?true:false;
			$this->usuario 			= $retorno_servidor['srvsmtpusuario'];
			$this->senha			= $retorno_servidor['srvsmtpsenha'];
						
		}catch (Exception $e){
			
			$this->erro = true;
			$this->msg = $e->getMessage();
		}
		
	}
	
	/**
	 * Busca informacoes sobre o layout e qual o servidor a ser utilizado
	 */
	public function getServidor(){

		$infoLayout = $this->dao->getTituloFuncionalidade($this->titulo);

		if (!empty($infoLayout)){	
			
			$this->layout = new SendLayoutEmails();
			
			/** Busca o informações sobre layout e servidor **/
			$informacoes = array(
					'seeseefoid'			=> $infoLayout->funcionalidade_id,
					'seeseetoid'			=> $infoLayout->titulo_id,
						
					'supertipo'				=> $this->proposta,
					'prptppoid'				=> $this->sub_proposta,
					'prptpcoid'				=> $this->contrato
			);			
		
			$condigoLayout = $this->layout->buscaLayoutEmail($informacoes);
			$layout = $this->dao->getLayoutEmail($condigoLayout['seeoid']);
			$infoServidor = $this->dao->getCodigoServidor($condigoLayout['seeoid']);		
			
 			if ($this->assunto_email == null) {
 				$this->assunto_email = $layout->seecabecalho;
 			}
				
			if ($this->corpo_email == null) {
				$this->corpo_email = $layout->seecorpo;
			}

			if ($layout->seeremetente != null && strlen($layout->seeremetente) > 0) {
				$this->remetente = $layout->seeremetente;
			}
			
			$this->servidor_email = $infoServidor->seesrvoid;
		} else {	
			$this->erro = true;
			$this->msg = "Nao foi encontrado nenhum servidor de envio, com o titulo, tipo de contrato, proposta e sub-proposta informados.";
			
			if ($this->titulo == null && $this->contrato == null && $this->proposta == null  && $this->sub_proposta == null ){
				$this->msg = "Informe o titulo, tipo de contrato, tipo de proposta e/ou sub-proposta.";
			}
		}	
	}
	
	
	
	
	
	/** CLASSE DO PHPMAILER **/
	
	/////////////////////////////////////////////////
	// PROPERTIES, PUBLIC
	/////////////////////////////////////////////////
	
	/**
	 * Email priority (1 = High, 3 = Normal, 5 = low).
	 * @var int
	 */
	public $Priority          = 3;
	
	/**
	 * Sets the CharSet of the message.
	 * @var string
	 */
	public $CharSet           = 'iso-8859-1';
	
	/**
	 * Sets the Content-type of the message.
	 * @var string
	 */
	public $ContentType       = 'text/plain';
	
	/**
	 * Sets the Encoding of the message. Options for this are "8bit",
	 * "7bit", "binary", "base64", and "quoted-printable".
	 * @var string
	 */
	public $Encoding          = '8bit';
	
	/**
	 * Holds the most recent mailer error message.
	 * @var string
	 */
	public $ErrorInfo         = '';
	
	/**
	 * Sets the From email address for the message.
	 * @var string
	 */
	public $From              = 'root@localhost';
	
	/**
	 * Sets the From name of the message.
	 * @var string
	 */
	public $FromName          = 'Root User';
	
	/**
	 * Sets the Sender email (Return-Path) of the message.  If not empty,
	 * will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
	 * @var string
	 */
	public $Sender            = '';
	
	/**
	 * Sets the Subject of the message.
	 * @var string
	 */
	public $Subject           = '';
	
	/**
	 * Sets the Body of the message.  This can be either an HTML or text body.
	 * If HTML then run IsHTML(true).
	 * @var string
	 */
	public $Body              = '';
	
	/**
	 * Sets the text-only body of the message.  This automatically sets the
	 * email to multipart/alternative.  This body can be read by mail
	 * clients that do not have HTML email capability such as mutt. Clients
	 * that can read HTML will view the normal Body.
	 * @var string
	 */
	public $AltBody           = '';
	
	/**
	 * Sets word wrapping on the body of the message to a given number of
	 * characters.
	 * @var int
	 */
	public $WordWrap          = 0;
	
	/**
	 * Method to send mail: ("mail", "sendmail", or "smtp").
	 * @var string
	 */
	//ALTERADO DE MAIL PARA SMTP.. DEPOIS TENTAR INSERIR USER E PASSW
	//  public $Mailer            = 'mail';
	//public $Mailer            = 'smtp';
	public $Mailer            = 'mail';
	
	
	/**
	 * Sets the path of the sendmail program.
	 * @var string
	 */
	public $Sendmail          = '/usr/sbin/sendmail';
	
	/**
	 * Path to PHPMailer plugins.  This is now only useful if the SMTP class
	 * is in a different directory than the PHP include path.
	 * @var string
	 */
	public $PluginDir         = '';
	
	/**
	 * Holds PHPMailer version.
	 * @var string
	 */
	public $Version           = "2.2";
	
	/**
	 * Sets the email address that a reading confirmation will be sent.
	 * @var string
	 */
	public $ConfirmReadingTo  = '';
	
	/**
	 * Sets the hostname to use in Message-Id and Received headers
	 * and as default HELO string. If empty, the value returned
	 * by SERVER_NAME is used or 'localhost.localdomain'.
	 * @var string
	 */
	public $Hostname          = '';
	
	/**
	 * Sets the message ID to be used in the Message-Id header.
	 * If empty, a unique id will be generated.
	 * @var string
	 */
	public $MessageID      = '';
	
	/////////////////////////////////////////////////
	// PROPERTIES FOR SMTP
	/////////////////////////////////////////////////
	
	/**
	 * Sets the SMTP hosts.  All hosts must be separated by a
	 * semicolon.  You can also specify a different port
	 * for each host by using this format: [hostname:port]
	 * (e.g. "smtp1.example.com:25;smtp2.example.com").
	 * Hosts will be tried in order.
	 * @var string
	 */
	//MUDOU O SMTP
	//  public $Host        = 'smtp.sascar.com.br';
	public $Host        = 'sascar-mx.sascar.com.br';
	//public $Host        = '172.16.19.27';
	
	/**
	 * Sets the default SMTP server port.
	 * @var int
	 */
	public $Port        = 587;
	
	/**
	 * Sets the SMTP HELO of the message (Default is $Hostname).
	 * @var string
	 */
	public $Helo        = '';
	
	/**
	 * Sets connection prefix.
	 * Options are "", "ssl" or "tls"
	 * @var string
	 */
	//public $SMTPSecure = "msstd:correio.sascar.com.br";
	public $SMTPSecure = "msstd:sascar-mx.sascar.com.br";
	
	
	/**
	 * Sets SMTP authentication. Utilizes the Username and Password variables.
	 * @var bool
	 */
	//public $SMTPAuth     = false;
	public $SMTPAuth       = true;
	
	/**
	 * Sets SMTP username.
	 * @var string
	 */
	//public $Username     = '';
	//public $Username     = 'homologacaosistemas';
	
	/**
	 * Sets SMTP password.
	 * @var string
	 */
	//SENHA ABAIXO É DO FELIPE.. NO DE PRODUÇÃO NAO TEM NADA...
	//public $Password     = 'fw1085sy';
	//public $Password     = '';
	//public $Password     = 'um38bj93.';
	
	/**
	 * Sets the SMTP server timeout in seconds. This function will not
	 * work with the win32 version.
	 * @var int
	 */
	public $Timeout      = 30;
	
	/**
	 * Sets SMTP class debugging on or off.
	 * @var bool
	 */
	public $SMTPDebug    = false;
	
	/**
	 * Prevents the SMTP connection from being closed after each mail
	 * sending.  If this is set to true then to close the connection
	 * requires an explicit call to SmtpClose().
	 * @var bool
	 */
	public $SMTPKeepAlive = false;
	
	/**
	 * Provides the ability to have the TO field process individual
	 * emails, instead of sending to entire TO addresses
	 * @var bool
	 */
	public $SingleTo = false;
	
	/////////////////////////////////////////////////
	// PROPERTIES, PRIVATE
	/////////////////////////////////////////////////
	
	private $smtp            = NULL;
	private $to              = array();
	private $cc              = array();
	private $bcc             = array();
	private $ReplyTo         = array();
	private $attachment      = array();
	private $CustomHeader    = array();
	private $message_type    = '';
	private $boundary        = array();
	private $language        = array();
	private $error_count     = 0;
	private $LE              = "\n";
	private $sign_cert_file  = "";
	private $sign_key_file   = "";
	private $sign_key_pass   = "";
	
	/////////////////////////////////////////////////
	// METHODS, VARIABLES
	/////////////////////////////////////////////////
	
	/**
	 * Sets message type to HTML.
	 * @param bool $bool
	 * @return void
	 */
	public function IsHTML($bool) {
		if($bool == true) {
			$this->ContentType = 'text/html';
		} else {
			$this->ContentType = 'text/plain';
		}
	}
	
	/**
	 * Sets Mailer to send message using SMTP.
	 * @return void
	 */
	public function IsSMTP() {
		$this->Mailer = 'smtp';
	}
	
	/**
	 * Sets Mailer to send message using PHP mail() function.
	 * @return void
	 */
	public function IsMail() {
		$this->Mailer = 'mail';
		//$this->Mailer = 'smtp';
	}
	
	/**
	 * Sets Mailer to send message using the $Sendmail program.
	 * @return void
	 */
	public function IsSendmail() {
		$this->Mailer = 'sendmail';
	}
	
	/**
	 * Sets Mailer to send message using the qmail MTA.
	 * @return void
	 */
	public function IsQmail() {
		$this->Sendmail = '/var/qmail/bin/sendmail';
		$this->Mailer   = 'sendmail';
	}
	
	/////////////////////////////////////////////////
	// METHODS, RECIPIENTS
	/////////////////////////////////////////////////
	
	/**
	 * Adds a "To" address.
	 * @param string $address
	 * @param string $name
	 * @return void
	 */
	public function AddAddress($address, $name = '') {
		$cur = count($this->to);
		$this->to[$cur][0] = trim($address);
		$this->to[$cur][1] = $name;
	}
	
	/**
	 * Adds a "Cc" address. Note: this function works
	 * with the SMTP mailer on win32, not with the "mail"
	 * mailer.
	 * @param string $address
	 * @param string $name
	 * @return void
	 */
	public function AddCC($address, $name = '') {
		$cur = count($this->cc);
		$this->cc[$cur][0] = trim($address);
		$this->cc[$cur][1] = $name;
	}
	
	/**
	 * Adds a "Bcc" address. Note: this function works
	 * with the SMTP mailer on win32, not with the "mail"
	 * mailer.
	 * @param string $address
	 * @param string $name
	 * @return void
	 */
	public function AddBCC($address, $name = '') {
		$cur = count($this->bcc);
		$this->bcc[$cur][0] = trim($address);
		$this->bcc[$cur][1] = $name;
	}
	
	/**
	 * Adds a "Reply-to" address.
	 * @param string $address
	 * @param string $name
	 * @return void
	 */
	public function AddReplyTo($address, $name = '') {
		$cur = count($this->ReplyTo);
		$this->ReplyTo[$cur][0] = trim($address);
		$this->ReplyTo[$cur][1] = $name;
	}
	
	/////////////////////////////////////////////////
	// METHODS, MAIL SENDING
	/////////////////////////////////////////////////
	
	/**
	 * Creates message and assigns Mailer. If the message is
	 * not sent successfully then it returns false.  Use the ErrorInfo
	 * variable to view description of the error.
	 * @return bool
	 */
	public function Send() {
		
		$header = '';
		$body = '';
		$result = true;
	
		$contador_to = count($this->to);
		$contador_cc = count($this->cc);
		$contador_bcc = count($this->bcc);
		
		// Se ambientes de DESENVOLVIMENTO / TESTE / HOMOLOGAÇÃO -> Não pode enviar para Dominio diferente de SASCAR 
		if ($_SESSION['servidor_teste'] == 1){
				
			if($contador_to > 0) {
				for($i = 0; $i < $contador_to; $i++) {
					if(!strstr($this->to[$i][0],'@sascar.com.br')){
						unset($this->to[$i]);
					}
					
					// Verifica se email está duplicado
					if ($i > 0 && !empty($this->to[$i][0])){
						//foreach ($this->to as $key => $email){
						for($j = 0; $j < $i; $j++) {
							if ($this->to[$j][0] == $this->to[$i][0]){
								//echo "       tirar";
								unset($this->to[$i]);
							}
						}
					}
				}
			}		
			if($contador_cc > 0) {
				for($i = 0; $i < $contador_cc; $i++) {
					if(!strstr($this->cc[$i][0],'@sascar.com.br')){
						unset($this->cc[$i]);
					}
					
					// Verifica se email está duplicado
					if ($i > 0 && !empty($this->cc[$i][0])){
						for($j = 0; $j < $i; $j++) {
							if ($this->cc[$j][0] == $this->to[$i][0]){
								unset($this->cc[$i]);
							}
						}
					}
				}
			}
			
			if($contador_bcc > 0) {
				for($i = 0; $i < $contador_bcc; $i++) {
					if(!strstr($this->bcc[$i][0],'@sascar.com.br')){
						unset($this->bcc[$i]);
					}
					
					// Verifica se email está duplicado
					if ($i > 0 && !empty($this->bcc[$i][0])){
						for($j = 0; $j < $i; $j++) {
							if ($this->bcc[$j][0] == $this->bcc[$i][0]){
								unset($this->bcc[$i]);
							}
						}
					}
				}
			}
		}
		
		sort($this->to);
		sort($this->cc);
		sort($this->bcc);		
	
		if((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
			$this->SetError($this->Lang('provide_address'));
			return false;
		}
	
		/* Set whether the message is multipart/alternative */
		if(!empty($this->AltBody)) {
			$this->ContentType = 'multipart/alternative';
		}
	
		$this->error_count = 0; // reset errors
		$this->SetMessageType();
		$header .= $this->CreateHeader();
		$body = $this->CreateBody();
	
		if($body == '') {
			return false;
		}
	
		//$result = $this->SmtpSend($header, $body);
		/* Choose the mailer */
		
		switch($this->Mailer) {
			case 'sendmail':
				$result = $this->SendmailSend($header, $body);
				break;
			case 'smtp':
				$result = $this->SmtpSend($header, $body);
				break;
			case 'mail':
				$result = $this->MailSend($header, $body);
				break;
			default:
				$result = $this->MailSend($header, $body);
			break;
		//$this->SetError($this->Mailer . $this->Lang('mailer_not_supported'));
		//$result = false;
		//break;
		}
		
	
		return $result;
	}
	
	/**
	 * Sends mail using the $Sendmail program.
	 * @access public
	 * @return bool
	 */
	public function SendmailSend($header, $body) {
		if ($this->Sender != '') {
			$sendmail = sprintf("%s -oi -f %s -t", escapeshellcmd($this->Sendmail), escapeshellarg($this->Sender));
		} else {
			$sendmail = sprintf("%s -oi -t", escapeshellcmd($this->Sendmail));
		}
	
		if(!@$mail = popen($sendmail, 'w')) {
			$this->SetError($this->Lang('execute') . $this->Sendmail);
			return false;
		}
	
		fputs($mail, $header);
		fputs($mail, $body);
	
		$result = pclose($mail);
		if (version_compare(phpversion(), '4.2.3') == -1) {
			$result = $result >> 8 & 0xFF;
		}
		if($result != 0) {
			$this->SetError($this->Lang('execute') . $this->Sendmail);
			return false;
		}
	
		return true;
	}
	
	/**
	 * Sends mail using the PHP mail() function.
	 * @access public
	 * @return bool
	 */
	public function MailSend($header, $body) {
	
		$to = '';
		for($i = 0; $i < count($this->to); $i++) {
			if($i != 0) { $to .= ', '; }
			$to .= $this->AddrFormat($this->to[$i]);
		}
	
		$toArr = explode(',', $to);
	
		$params = sprintf("-oi -f %s", $this->Sender);
		if ($this->Sender != '' && strlen(ini_get('safe_mode'))< 1) {
			$old_from = ini_get('sendmail_from');
			ini_set('sendmail_from', $this->Sender);
			if ($this->SingleTo === true && count($toArr) > 1) {
				foreach ($toArr as $key => $val) {
					$rt = @mail($val, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
				}
			} else {
				$rt = @mail($to, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
			}
		} else {
			if ($this->SingleTo === true && count($toArr) > 1) {
				foreach ($toArr as $key => $val) {
					$rt = @mail($val, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header, $params);
				}
			} else {
				$rt = @mail($to, $this->EncodeHeader($this->SecureHeader($this->Subject)), $body, $header);
			}
		}
	
		if (isset($old_from)) {
			ini_set('sendmail_from', $old_from);
		}
	
		if(!$rt) {
			$this->SetError($this->Lang('instantiate'));
			return false;
		}
	
		return true;
	}
	
	/**
	 * Sends mail via SMTP using PhpSMTP (Author:
	 * Chris Ryan).  Returns bool.  Returns false if there is a
	 * bad MAIL FROM, RCPT, or DATA input.
	 * @access public
	 * @return bool
	 */
	public function SmtpSend($header, $body) {
//		include_once($this->PluginDir . 'class.smtp.php');
		include_once(_SITEDIR_ . 'lib/phpMailer/class.smtp.php');
		
		$error = '';
		$bad_rcpt = array();
	
		if(!$this->SmtpConnect()) {
			return false;
		}
	
		$smtp_from = ($this->Sender == '') ? $this->From : $this->Sender;
	
		//$smtp_from = 'homologacaosistemas@sascar.com.br';
			
		if(!$this->smtp->Mail($smtp_from)) {
			$error = $this->Lang('from_failed') . $smtp_from;
			$this->SetError($error);
			$this->smtp->Reset();
			return false;
		}
	
		/* Attempt to send attach all recipients */
		for($i = 0; $i < count($this->to); $i++) {
			if(!$this->smtp->Recipient($this->to[$i][0])) {
				$bad_rcpt[] = $this->to[$i][0];
			}
		}
		for($i = 0; $i < count($this->cc); $i++) {
			if(!$this->smtp->Recipient($this->cc[$i][0])) {
				$bad_rcpt[] = $this->cc[$i][0];
			}
		}
		for($i = 0; $i < count($this->bcc); $i++) {
			if(!$this->smtp->Recipient($this->bcc[$i][0])) {
				$bad_rcpt[] = $this->bcc[$i][0];
			}
		}
	
		if(count($bad_rcpt) > 0) { // Create error message
			for($i = 0; $i < count($bad_rcpt); $i++) {
				if($i != 0) {
					$error .= ', ';
				}
				$error .= $bad_rcpt[$i];
			}
			$error = $this->Lang('recipients_failed') . $error;
			$this->SetError($error);
			$this->smtp->Reset();
			return false;
		}
	
		if(!$this->smtp->Data($header . $body)) {
			$this->SetError($this->Lang('data_not_accepted'));
			$this->smtp->Reset();
			return false;
		}
		if($this->SMTPKeepAlive == true) {
			$this->smtp->Reset();
		} else {
			$this->SmtpClose();
		}
	
		return true;
	}
	
	/**
	 * Initiates a connection to an SMTP server.  Returns false if the
	 * operation failed.
	 * @access public
	 * @return bool
	 */
	public function SmtpConnect() {
		if($this->smtp == NULL) {
			$this->smtp = new SMTP();
		}
	
		$this->smtp->do_debug = $this->SMTPDebug;
		$hosts = explode(';', $this->Host);
		$index = 0;
		$connection = ($this->smtp->Connected());
	
		/* Retry while there is no connection */
		while($index < count($hosts) && $connection == false) {
			$hostinfo = array();
			if(preg_match(  '/^(.+):([0-9]+)$/', $hosts[$index], $hostinfo)) {
	
				$host = $hostinfo[1];
				$port = $hostinfo[2];
			} else {
				$host = $hosts[$index];
				$port = $this->Port;
			}
						
			if ($this->autenticacao == true){
				$tls = true;//($this->SMTPSecure == 'tls');
				//$ssl = ($this->SMTPSecure == 'ssl');
			}else{
				$tls = false;
			}		
			
	
			if($this->smtp->Connect(($ssl ? 'ssl://':'').$host, $port, $this->Timeout)) {
					
				$hello = ($this->Helo != '' ? $this->Hello : $this->ServerHostname());
				$this->smtp->Hello($hello);
	
				if($tls) {
					if(!$this->smtp->StartTLS()) {
						$this->SetError($this->Lang("tls"));
						$this->smtp->Reset();
						$connection = false;
					}
	
					//We must resend HELLO after tls negociation
					$this->smtp->Hello($hello);
				}
	
				$connection = true;
				if($this->SMTPAuth) {
					if(!$this->smtp->Authenticate($this->Username, $this->Password)) {
						$this->SetError($this->Lang('authenticate'));
						$this->smtp->Reset();
						$connection = false;
					}
				}
			}
			$index++;
		}
		if(!$connection) {
			$this->SetError($this->Lang('connect_host'));
		}
	
		return $connection;
	}
	
	/**
	 * Closes the active SMTP session if one exists.
	 * @return void
	 */
	public function SmtpClose() {
		if($this->smtp != NULL) {
			if($this->smtp->Connected()) {
				$this->smtp->Quit();
				$this->smtp->Close();
			}
		}
	}
	
	/**
	 * Sets the language for all class error messages.  Returns false
	 * if it cannot load the language file.  The default language type
	 * is English.
	 * @param string $lang_type Type of language (e.g. Portuguese: "br")
	 * @param string $lang_path Path to the language file directory
	 * @access public
	 * @return bool
	 */
	function SetLanguage($lang_type = 'en', $lang_path = 'language/') {
		$lang_path = _SITEDIR_ . 'lib/phpMailer/' . $lang_path;
				
		if( !(@include $lang_path.'phpmailer.lang-'.$lang_type.'.php') ) {
			$this->SetError('Could not load language file');
			return false;
		}
		$this->language = $PHPMAILER_LANG;
		return true;
	}
	
	/////////////////////////////////////////////////
	// METHODS, MESSAGE CREATION
	/////////////////////////////////////////////////
	
	/**
	 * Creates recipient headers.
	 * @access public
	 * @return string
	 */
	public function AddrAppend($type, $addr) {
		$addr_str = $type . ': ';
		$addr_str .= $this->AddrFormat($addr[0]);
		if(count($addr) > 1) {
			for($i = 1; $i < count($addr); $i++) {
				$addr_str .= ', ' . $this->AddrFormat($addr[$i]);
			}
		}
		$addr_str .= $this->LE;
	
		return $addr_str;
	}
	
	/**
	 * Formats an address correctly.
	 * @access public
	 * @return string
	 */
	public function AddrFormat($addr) {
		if(empty($addr[1])) {
			$formatted = $this->SecureHeader($addr[0]);
		} else {
			$formatted = $this->EncodeHeader($this->SecureHeader($addr[1]), 'phrase') . " <" . $this->SecureHeader($addr[0]) . ">";
		}
	
		return $formatted;
	}
	
	/**
	 * Wraps message for use with mailers that do not
	 * automatically perform wrapping and for quoted-printable.
	 * Original written by philippe.
	 * @access public
	 * @return string
	 */
	public function WrapText($message, $length, $qp_mode = false) {
		$soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;
		// If utf-8 encoding is used, we will need to make sure we don't
		// split multibyte characters when we wrap
		$is_utf8 = (strtolower($this->CharSet) == "utf-8");
	
		$message = $this->FixEOL($message);
		if (substr($message, -1) == $this->LE) {
			$message = substr($message, 0, -1);
		}
	
		$line = explode($this->LE, $message);
		$message = '';
		for ($i=0 ;$i < count($line); $i++) {
			$line_part = explode(' ', $line[$i]);
			$buf = '';
			for ($e = 0; $e<count($line_part); $e++) {
				$word = $line_part[$e];
				if ($qp_mode and (strlen($word) > $length)) {
					$space_left = $length - strlen($buf) - 1;
					if ($e != 0) {
						if ($space_left > 20) {
							$len = $space_left;
							if ($is_utf8) {
								$len = $this->UTF8CharBoundary($word, $len);
							} elseif (substr($word, $len - 1, 1) == "=") {
								$len--;
							} elseif (substr($word, $len - 2, 1) == "=") {
								$len -= 2;
							}
							$part = substr($word, 0, $len);
							$word = substr($word, $len);
							$buf .= ' ' . $part;
							$message .= $buf . sprintf("=%s", $this->LE);
						} else {
							$message .= $buf . $soft_break;
						}
						$buf = '';
					}
					while (strlen($word) > 0) {
						$len = $length;
						if ($is_utf8) {
							$len = $this->UTF8CharBoundary($word, $len);
						} elseif (substr($word, $len - 1, 1) == "=") {
							$len--;
						} elseif (substr($word, $len - 2, 1) == "=") {
							$len -= 2;
						}
						$part = substr($word, 0, $len);
						$word = substr($word, $len);
	
						if (strlen($word) > 0) {
							$message .= $part . sprintf("=%s", $this->LE);
						} else {
							$buf = $part;
						}
					}
				} else {
					$buf_o = $buf;
					$buf .= ($e == 0) ? $word : (' ' . $word);
	
					if (strlen($buf) > $length and $buf_o != '') {
						$message .= $buf_o . $soft_break;
						$buf = $word;
					}
				}
			}
			$message .= $buf . $this->LE;
		}
	
		return $message;
	}
	
	/**
	 * Finds last character boundary prior to maxLength in a utf-8
	 * quoted (printable) encoded string.
	 * Original written by Colin Brown.
	 * @access public
	 * @param string $encodedText utf-8 QP text
	 * @param int    $maxLength   find last character boundary prior to this length
	 * @return int
	 */
	public function UTF8CharBoundary($encodedText, $maxLength) {
		$foundSplitPos = false;
		$lookBack = 3;
		while (!$foundSplitPos) {
			$lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
			$encodedCharPos = strpos($lastChunk, "=");
			if ($encodedCharPos !== false) {
				// Found start of encoded character byte within $lookBack block.
				// Check the encoded byte value (the 2 chars after the '=')
				$hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
				$dec = hexdec($hex);
				if ($dec < 128) { // Single byte character.
					// If the encoded char was found at pos 0, it will fit
					// otherwise reduce maxLength to start of the encoded char
					$maxLength = ($encodedCharPos == 0) ? $maxLength :
					$maxLength - ($lookBack - $encodedCharPos);
					$foundSplitPos = true;
				} elseif ($dec >= 192) { // First byte of a multi byte character
					// Reduce maxLength to split at start of character
					$maxLength = $maxLength - ($lookBack - $encodedCharPos);
					$foundSplitPos = true;
				} elseif ($dec < 192) { // Middle byte of a multi byte character, look further back
					$lookBack += 3;
				}
			} else {
				// No encoded character found
				$foundSplitPos = true;
			}
		}
		return $maxLength;
	}
	
	
	/**
	 * Set the body wrapping.
	 * @access public
	 * @return void
	 */
	public function SetWordWrap() {
		if($this->WordWrap < 1) {
			return;
		}
	
		switch($this->message_type) {
			case 'alt':
				/* fall through */
			case 'alt_attachments':
				$this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
				break;
			default:
				$this->Body = $this->WrapText($this->Body, $this->WordWrap);
				break;
		}
	}
	
	/**
	 * Assembles message header.
	 * @access public
	 * @return string
	 */
	public function CreateHeader() {
		$result = '';
	
		/* Set the boundaries */
		$uniq_id = md5(uniqid(time()));
		$this->boundary[1] = 'b1_' . $uniq_id;
		$this->boundary[2] = 'b2_' . $uniq_id;
	
		//Forcar envio pelo usuario de homologacao
		//$this->From =  'homologacaosistemas@sascar.com.br';
	
		$result .= $this->HeaderLine('Date', $this->RFCDate());
		if($this->Sender == '') {
			$result .= $this->HeaderLine('Return-Path', trim($this->From));
		} else {
			$result .= $this->HeaderLine('Return-Path', trim($this->Sender));
		}
	
		/* To be created automatically by mail() */
		if($this->Mailer != 'mail') {
			if(count($this->to) > 0) {
				$result .= $this->AddrAppend('To', $this->to);
			} elseif (count($this->cc) == 0) {
				$result .= $this->HeaderLine('To', 'undisclosed-recipients:;');
			}
			if(count($this->cc) > 0) {
				$result .= $this->AddrAppend('Cc', $this->cc);
			}
		}
	
		$from = array();
		$from[0][0] = trim($this->From);
		$from[0][1] = $this->FromName;
		$result .= $this->AddrAppend('From', $from);
	
		/* sendmail and mail() extract Cc from the header before sending */
		if((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->cc) > 0)) {
			$result .= $this->AddrAppend('Cc', $this->cc);
		}
	
		/* sendmail and mail() extract Bcc from the header before sending */
		if((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->bcc) > 0)) {
			$result .= $this->AddrAppend('Bcc', $this->bcc);
		}
	
		if(count($this->ReplyTo) > 0) {
			$result .= $this->AddrAppend('Reply-to', $this->ReplyTo);
		}
	
		/* mail() sets the subject itself */
		if($this->Mailer != 'mail') {
			$result .= $this->HeaderLine('Subject', $this->EncodeHeader($this->SecureHeader($this->Subject)));
		}
	
		if($this->MessageID != '') {
			$result .= $this->HeaderLine('Message-ID',$this->MessageID);
		} else {
			$result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
		}
		$result .= $this->HeaderLine('X-Priority', $this->Priority);
		$result .= $this->HeaderLine('X-Mailer', 'PHPMailer (phpmailer.codeworxtech.com) [version ' . $this->Version . ']');
	
		if($this->ConfirmReadingTo != '') {
			$result .= $this->HeaderLine('Disposition-Notification-To', '<' . trim($this->ConfirmReadingTo) . '>');
		}
	
		// Add custom headers
		for($index = 0; $index < count($this->CustomHeader); $index++) {
			$result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
		}
		if (!$this->sign_key_file) {
			$result .= $this->HeaderLine('MIME-Version', '1.0');
			$result .= $this->GetMailMIME();
		}
	
		return $result;
	}
	
	/**
	 * Returns the message MIME.
	 * @access public
	 * @return string
	 */
	public function GetMailMIME() {
		$result = '';
		switch($this->message_type) {
			case 'plain':
				$result .= $this->HeaderLine('Content-Transfer-Encoding', $this->Encoding);
				$result .= sprintf("Content-Type: %s; charset=\"%s\"", $this->ContentType, $this->CharSet);
				break;
			case 'attachments':
				/* fall through */
			case 'alt_attachments':
				if($this->InlineImageExists()){
					$result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 'multipart/related', $this->LE, $this->LE, $this->boundary[1], $this->LE);
				} else {
					$result .= $this->HeaderLine('Content-Type', 'multipart/mixed;');
					$result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
				}
				break;
			case 'alt':
				$result .= $this->HeaderLine('Content-Type', 'multipart/alternative;');
				$result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
				break;
		}
	
		if($this->Mailer != 'mail') {
			$result .= $this->LE.$this->LE;
		}
	
		return $result;
	}
	
	/**
	 * Assembles the message body.  Returns an empty string on failure.
	 * @access public
	 * @return string
	 */
	public function CreateBody() {
		$result = '';
		
		if ($this->sign_key_file) {
			$result .= $this->GetMailMIME();
		}
	
		$this->SetWordWrap();
	
		switch($this->message_type) {
			case 'alt':
				$result .= $this->GetBoundary($this->boundary[1], '', 'text/plain', '');
				$result .= $this->EncodeString($this->AltBody, $this->Encoding);
				$result .= $this->LE.$this->LE;
				$result .= $this->GetBoundary($this->boundary[1], '', 'text/html', '');
				$result .= $this->EncodeString($this->Body, $this->Encoding);
				$result .= $this->LE.$this->LE;
				$result .= $this->EndBoundary($this->boundary[1]);
				break;
			case 'plain':
				$result .= $this->EncodeString($this->Body, $this->Encoding);
				break;
			case 'attachments':
				$result .= $this->GetBoundary($this->boundary[1], '', '', '');
				$result .= $this->EncodeString($this->Body, $this->Encoding);
				$result .= $this->LE;
				$result .= $this->AttachAll();
				break;
			case 'alt_attachments':
				$result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
				$result .= sprintf("Content-Type: %s;%s" . "\tboundary=\"%s\"%s", 'multipart/alternative', $this->LE, $this->boundary[2], $this->LE.$this->LE);
				$result .= $this->GetBoundary($this->boundary[2], '', 'text/plain', '') . $this->LE; // Create text body
				$result .= $this->EncodeString($this->AltBody, $this->Encoding);
				$result .= $this->LE.$this->LE;
				$result .= $this->GetBoundary($this->boundary[2], '', 'text/html', '') . $this->LE; // Create the HTML body
				$result .= $this->EncodeString($this->Body, $this->Encoding);
				$result .= $this->LE.$this->LE;
				$result .= $this->EndBoundary($this->boundary[2]);
				$result .= $this->AttachAll();
				break;
		}
	
		if($this->IsError()) {
			$result = '';
		} else if ($this->sign_key_file) {
			$file = tempnam("", "mail");
			$fp = fopen($file, "w");
			fwrite($fp, $result);
			fclose($fp);
			$signed = tempnam("", "signed");
	
			if (@openssl_pkcs7_sign($file, $signed, "file://".$this->sign_cert_file, array("file://".$this->sign_key_file, $this->sign_key_pass), null)) {
				$fp = fopen($signed, "r");
				$result = '';
				while(!feof($fp)){
					$result = $result . fread($fp, 1024);
				}
				fclose($fp);
			} else {
				$this->SetError($this->Lang("signing").openssl_error_string());
				$result = '';
			}
	
			unlink($file);
			unlink($signed);
		}
	
		return $result;
	}
	
	/**
	 * Returns the start of a message boundary.
	 * @access public
	 */
	public function GetBoundary($boundary, $charSet, $contentType, $encoding) {
		$result = '';
		if($charSet == '') {
			$charSet = $this->CharSet;
		}
		if($contentType == '') {
			$contentType = $this->ContentType;
		}
		if($encoding == '') {
			$encoding = $this->Encoding;
		}
		$result .= $this->TextLine('--' . $boundary);
		$result .= sprintf("Content-Type: %s; charset = \"%s\"", $contentType, $charSet);
		$result .= $this->LE;
		$result .= $this->HeaderLine('Content-Transfer-Encoding', $encoding);
		$result .= $this->LE;
	
		return $result;
	}
	
	/**
	 * Returns the end of a message boundary.
	 * @access public
	 */
	public function EndBoundary($boundary) {
		return $this->LE . '--' . $boundary . '--' . $this->LE;
	}
	
	/**
	 * Sets the message type.
	 * @access public
	 * @return void
	 */
	public function SetMessageType() {
		if(count($this->attachment) < 1 && strlen($this->AltBody) < 1) {
			$this->message_type = 'plain';
		} else {
			if(count($this->attachment) > 0) {
				$this->message_type = 'attachments';
			}
			if(strlen($this->AltBody) > 0 && count($this->attachment) < 1) {
				$this->message_type = 'alt';
			}
			if(strlen($this->AltBody) > 0 && count($this->attachment) > 0) {
				$this->message_type = 'alt_attachments';
			}
		}
	}
	
	/* Returns a formatted header line.
	 * @access public
	* @return string
	*/
	public function HeaderLine($name, $value) {
		return $name . ': ' . $value . $this->LE;
	}
	
	/**
	 * Returns a formatted mail line.
	 * @access public
	 * @return string
	 */
	public function TextLine($value) {
		return $value . $this->LE;
	}
	
	/////////////////////////////////////////////////
	// CLASS METHODS, ATTACHMENTS
	/////////////////////////////////////////////////
	
	/**
	 * Adds an attachment from a path on the filesystem.
	 * Returns false if the file could not be found
	 * or accessed.
	 * @param string $path Path to the attachment.
	 * @param string $name Overrides the attachment name.
	 * @param string $encoding File encoding (see $Encoding).
	 * @param string $type File extension (MIME) type.
	 * @return bool
	 */
	public function AddAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
		if(!@is_file($path)) {
			$this->SetError($this->Lang('file_access') . $path);
			return false;
		}
	
		$filename = basename($path);
		if($name == '') {
			$name = $filename;
		}
	
		$cur = count($this->attachment);
		$this->attachment[$cur][0] = $path;
		$this->attachment[$cur][1] = $filename;
		$this->attachment[$cur][2] = $name;
		$this->attachment[$cur][3] = $encoding;
		$this->attachment[$cur][4] = $type;
		$this->attachment[$cur][5] = false; // isStringAttachment
		$this->attachment[$cur][6] = 'attachment';
		$this->attachment[$cur][7] = 0;
	
		return true;
	}
	
	/**
	 * Attaches all fs, string, and binary attachments to the message.
	 * Returns an empty string on failure.
	 * @access public
	 * @return string
	 */
	public function AttachAll() {
		/* Return text of body */
		$mime = array();
	
		/* Add all attachments */
		for($i = 0; $i < count($this->attachment); $i++) {
			/* Check for string attachment */
			$bString = $this->attachment[$i][5];
			if ($bString) {
				$string = $this->attachment[$i][0];
			} else {
				$path = $this->attachment[$i][0];
			}
	
			$filename    = $this->attachment[$i][1];
			$name        = $this->attachment[$i][2];
			$encoding    = $this->attachment[$i][3];
			$type        = $this->attachment[$i][4];
			$disposition = $this->attachment[$i][6];
			$cid         = $this->attachment[$i][7];
	
			$mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
			//$mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
			$mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $this->EncodeHeader($this->SecureHeader($name)), $this->LE);
			$mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);
	
			if($disposition == 'inline') {
				$mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
			}
	
			//$mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, $name, $this->LE.$this->LE);
			$mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, $this->EncodeHeader($this->SecureHeader($name)), $this->LE.$this->LE);
	
			/* Encode as string attachment */
			if($bString) {
				$mime[] = $this->EncodeString($string, $encoding);
				if($this->IsError()) {
					return '';
				}
				$mime[] = $this->LE.$this->LE;
			} else {
				$mime[] = $this->EncodeFile($path, $encoding);
				if($this->IsError()) {
					return '';
				}
				$mime[] = $this->LE.$this->LE;
			}
		}
	
		$mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);
	
		return join('', $mime);
	}
	
	/**
	 * Encodes attachment in requested format.  Returns an
	 * empty string on failure.
	 * @access public
	 * @return string
	 */
	public function EncodeFile ($path, $encoding = 'base64') {
		if(!@$fd = fopen($path, 'rb')) {
			$this->SetError($this->Lang('file_open') . $path);
			return '';
		}
		if (function_exists('get_magic_quotes')) {
			function get_magic_quotes() {
				return false;
			}
		}
		if (PHP_VERSION < '5.3.0') {
			$magic_quotes = get_magic_quotes_runtime();
			set_magic_quotes_runtime(0);
		}
		$file_buffer  = file_get_contents($path);
		$file_buffer  = $this->EncodeString($file_buffer, $encoding);
		fclose($fd);
		if (PHP_VERSION < '5.3.0') { set_magic_quotes_runtime($magic_quotes); }
		return $file_buffer;
	}
	
	/**
	 * Encodes string to requested format. Returns an
	 * empty string on failure.
	 * @access public
	 * @return string
	 */
	public function EncodeString ($str, $encoding = 'base64') {
		$encoded = '';
		switch(strtolower($encoding)) {
			case 'base64':
				$encoded = chunk_split(base64_encode($str), 76, $this->LE);
				break;
			case '7bit':
			case '8bit':
				$encoded = $this->FixEOL($str);
				if (substr($encoded, -(strlen($this->LE))) != $this->LE)
					$encoded .= $this->LE;
				break;
			case 'binary':
				$encoded = $str;
				break;
			case 'quoted-printable':
				$encoded = $this->EncodeQP($str);
				break;
			default:
				$this->SetError($this->Lang('encoding') . $encoding);
				break;
		}
		return $encoded;
	}
	
	/**
	 * Encode a header string to best of Q, B, quoted or none.
	 * @access public
	 * @return string
	 */
	public function EncodeHeader ($str, $position = 'text') {
		$x = 0;
	
		switch (strtolower($position)) {
			case 'phrase':
				if (!preg_match('/[\200-\377]/', $str)) {
					/* Can't use addslashes as we don't know what value has magic_quotes_sybase. */
					$encoded = addcslashes($str, "\0..\37\177\\\"");
					if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
						return ($encoded);
					} else {
						return ("\"$encoded\"");
					}
				}
				$x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
				break;
			case 'comment':
				$x = preg_match_all('/[()"]/', $str, $matches);
				/* Fall-through */
			case 'text':
			default:
				$x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
				break;
		}
	
		if ($x == 0) {
			return ($str);
		}
	
		$maxlen = 75 - 7 - strlen($this->CharSet);
		/* Try to select the encoding which should produce the shortest output */
		if (strlen($str)/3 < $x) {
			$encoding = 'B';
			if (function_exists('mb_strlen') && $this->HasMultiBytes($str)) {
				// Use a custom function which correctly encodes and wraps long
				// multibyte strings without breaking lines within a character
				$encoded = $this->Base64EncodeWrapMB($str);
			} else {
				$encoded = base64_encode($str);
				$maxlen -= $maxlen % 4;
				$encoded = trim(chunk_split($encoded, $maxlen, "\n"));
			}
		} else {
			$encoding = 'Q';
			$encoded = $this->EncodeQ($str, $position);
			$encoded = $this->WrapText($encoded, $maxlen, true);
			$encoded = str_replace('='.$this->LE, "\n", trim($encoded));
		}
	
		$encoded = preg_replace('/^(.*)$/m', " =?".$this->CharSet."?$encoding?\\1?=", $encoded);
		$encoded = trim(str_replace("\n", $this->LE, $encoded));
	
		return $encoded;
	}
	
	/**
	 * Checks if a string contains multibyte characters.
	 * @access public
	 * @param string $str multi-byte text to wrap encode
	 * @return bool
	 */
	public function HasMultiBytes($str) {
		if (function_exists('mb_strlen')) {
			return (strlen($str) > mb_strlen($str, $this->CharSet));
		} else { // Assume no multibytes (we can't handle without mbstring functions anyway)
			return False;
		}
	}
	
	/**
	 * Correctly encodes and wraps long multibyte strings for mail headers
	 * without breaking lines within a character.
	 * Adapted from a function by paravoid at http://uk.php.net/manual/en/function.mb-encode-mimeheader.php
	 * @access public
	 * @param string $str multi-byte text to wrap encode
	 * @return string
	 */
	public function Base64EncodeWrapMB($str) {
		$start = "=?".$this->CharSet."?B?";
		$end = "?=";
		$encoded = "";
	
		$mb_length = mb_strlen($str, $this->CharSet);
		// Each line must have length <= 75, including $start and $end
		$length = 75 - strlen($start) - strlen($end);
		// Average multi-byte ratio
		$ratio = $mb_length / strlen($str);
		// Base64 has a 4:3 ratio
		$offset = $avgLength = floor($length * $ratio * .75);
	
		for ($i = 0; $i < $mb_length; $i += $offset) {
			$lookBack = 0;
	
			do {
				$offset = $avgLength - $lookBack;
				$chunk = mb_substr($str, $i, $offset, $this->CharSet);
				$chunk = base64_encode($chunk);
				$lookBack++;
			}
			while (strlen($chunk) > $length);
	
			$encoded .= $chunk . $this->LE;
		}
	
		// Chomp the last linefeed
		$encoded = substr($encoded, 0, -strlen($this->LE));
		return $encoded;
	}
	
	/**
	 * Encode string to quoted-printable.
	 * @access public
	 * @param string $string the text to encode
	 * @param integer $line_max Number of chars allowed on a line before wrapping
	 * @return string
	 */
	public function EncodeQP( $input = '', $line_max = 76, $space_conv = false ) {
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines = preg_split('/(?:\r\n|\r|\n)/', $input);
		$eol = "\r\n";
		$escape = '=';
		$output = '';
		while( list(, $line) = each($lines) ) {
			$linlen = strlen($line);
			$newline = '';
			for($i = 0; $i < $linlen; $i++) {
				$c = substr( $line, $i, 1 );
				$dec = ord( $c );
				if ( ( $i == 0 ) && ( $dec == 46 ) ) { // convert first point in the line into =2E
					$c = '=2E';
				}
				if ( $dec == 32 ) {
					if ( $i == ( $linlen - 1 ) ) { // convert space at eol only
						$c = '=20';
					} else if ( $space_conv ) {
						$c = '=20';
					}
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16);
					$h1 = floor($dec%16);
					$c = $escape.$hex[$h2].$hex[$h1];
				}
				if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
					$output .= $newline.$escape.$eol; //  soft line break; " =\r\n" is okay
					$newline = '';
					// check if newline first character will be point or not
					if ( $dec == 46 ) {
						$c = '=2E';
					}
				}
				$newline .= $c;
			} // end of for
			$output .= $newline.$eol;
		} // end of while
		return trim($output);
	}
	
	/**
	 * Encode string to q encoding.
	 * @access public
	 * @return string
	 */
	public function EncodeQ ($str, $position = 'text') {
		/* There should not be any EOL in the string */
		$encoded = preg_replace("[\r\n]", '', $str);
	
		switch (strtolower($position)) {
			case 'phrase':
				$encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
				break;
			case 'comment':
				$encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
			case 'text':
			default:
				/* Replace every high ascii, control =, ? and _ characters */
				$encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e',
				"'='.sprintf('%02X', ord('\\1'))", $encoded);
				break;
		}
	
		/* Replace every spaces to _ (more readable than =20) */
		$encoded = str_replace(' ', '_', $encoded);
	
		return $encoded;
	}
	
	/**
	 * Adds a string or binary attachment (non-filesystem) to the list.
	 * This method can be used to attach ascii or binary data,
	 * such as a BLOB record from a database.
	 * @param string $string String attachment data.
	 * @param string $filename Name of the attachment.
	 * @param string $encoding File encoding (see $Encoding).
	 * @param string $type File extension (MIME) type.
	 * @return void
	 */
	public function AddStringAttachment($string, $filename, $encoding = 'base64', $type = 'application/octet-stream') {
		/* Append to $attachment array */
		$cur = count($this->attachment);
		$this->attachment[$cur][0] = $string;
		$this->attachment[$cur][1] = $filename;
		$this->attachment[$cur][2] = $filename;
		$this->attachment[$cur][3] = $encoding;
		$this->attachment[$cur][4] = $type;
		$this->attachment[$cur][5] = true; // isString
		$this->attachment[$cur][6] = 'attachment';
		$this->attachment[$cur][7] = 0;
	}
	
	/**
	 * Adds an embedded attachment.  This can include images, sounds, and
	 * just about any other document.  Make sure to set the $type to an
	 * image type.  For JPEG images use "image/jpeg" and for GIF images
	 * use "image/gif".
	 * @param string $path Path to the attachment.
	 * @param string $cid Content ID of the attachment.  Use this to identify
	 *        the Id for accessing the image in an HTML form.
	 * @param string $name Overrides the attachment name.
	 * @param string $encoding File encoding (see $Encoding).
	 * @param string $type File extension (MIME) type.
	 * @return bool
	 */
	public function AddEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream') {
	
		if(!@is_file($path)) {
			$this->SetError($this->Lang('file_access') . $path);
			return false;
		}
	
		$filename = basename($path);
		if($name == '') {
			$name = $filename;
		}
	
		/* Append to $attachment array */
		$cur = count($this->attachment);
		$this->attachment[$cur][0] = $path;
		$this->attachment[$cur][1] = $filename;
		$this->attachment[$cur][2] = $name;
		$this->attachment[$cur][3] = $encoding;
		$this->attachment[$cur][4] = $type;
		$this->attachment[$cur][5] = false;
		$this->attachment[$cur][6] = 'inline';
		$this->attachment[$cur][7] = $cid;
	
		return true;
	}
	
	/**
	 * Returns true if an inline attachment is present.
	 * @access public
	 * @return bool
	 */
	public function InlineImageExists() {
		$result = false;
		for($i = 0; $i < count($this->attachment); $i++) {
			if($this->attachment[$i][6] == 'inline') {
				$result = true;
				break;
			}
		}
	
		return $result;
	}
	
	/////////////////////////////////////////////////
	// CLASS METHODS, MESSAGE RESET
	/////////////////////////////////////////////////
	
	/**
	 * Clears all recipients assigned in the TO array.  Returns void.
	 * @return void
	 */
	public function ClearAddresses() {
		$this->to = array();
	}
	
	/**
	 * Clears all recipients assigned in the CC array.  Returns void.
	 * @return void
	 */
	public function ClearCCs() {
		$this->cc = array();
	}
	
	/**
	 * Clears all recipients assigned in the BCC array.  Returns void.
	 * @return void
	 */
	public function ClearBCCs() {
		$this->bcc = array();
	}
	
	/**
	 * Clears all recipients assigned in the ReplyTo array.  Returns void.
	 * @return void
	 */
	public function ClearReplyTos() {
		$this->ReplyTo = array();
	}
	
	/**
	 * Clears all recipients assigned in the TO, CC and BCC
	 * array.  Returns void.
	 * @return void
	 */
	public function ClearAllRecipients() {
		$this->to = array();
		$this->cc = array();
		$this->bcc = array();
	}
	
	/**
	 * Clears all previously set filesystem, string, and binary
	 * attachments.  Returns void.
	 * @return void
	 */
	public function ClearAttachments() {
		$this->attachment = array();
	}
	
	/**
	 * Clears all custom headers.  Returns void.
	 * @return void
	 */
	public function ClearCustomHeaders() {
		$this->CustomHeader = array();
	}
	
	/////////////////////////////////////////////////
	// CLASS METHODS, MISCELLANEOUS
	/////////////////////////////////////////////////
	
	/**
	 * Adds the error message to the error container.
	 * Returns void.
	 * @access private
	 * @return void
	 */
	private function SetError($msg) {
		$this->error_count++;
		$this->ErrorInfo = $msg;
	}
	
	/**
	 * Returns the proper RFC 822 formatted date.
	 * @access private
	 * @return string
	 */
	private static function RFCDate() {
		$tz = date('Z');
		$tzs = ($tz < 0) ? '-' : '+';
		$tz = abs($tz);
		$tz = (int)($tz/3600)*100 + ($tz%3600)/60;
		$result = sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);
	
		return $result;
	}
	
	/**
	 * Returns the server hostname or 'localhost.localdomain' if unknown.
	 * @access private
	 * @return string
	 */
	private function ServerHostname() {
		if (!empty($this->Hostname)) {
			$result = $this->Hostname;
		} elseif (isset($_SERVER['SERVER_NAME'])) {
			$result = $_SERVER['SERVER_NAME'];
		} else {
			$result = "localhost.localdomain";
		}
	
		return $result;
	}
	
	/**
	 * Returns a message in the appropriate language.
	 * @access private
	 * @return string
	 */
	private function Lang($key) {
		if(count($this->language) < 1) {
			$this->SetLanguage('en'); // set the default language
		}
	
		if(isset($this->language[$key])) {
			return $this->language[$key];
		} else {
			return 'Language string failed to load: ' . $key;
		}
	}
	
	/**
	 * Returns true if an error occurred.
	 * @access public
	 * @return bool
	 */
	public function IsError() {
		return ($this->error_count > 0);
	}
	
	/**
	 * Changes every end of line from CR or LF to CRLF.
	 * @access private
	 * @return string
	 */
	private function FixEOL($str) {
		$str = str_replace("\r\n", "\n", $str);
		$str = str_replace("\r", "\n", $str);
		$str = str_replace("\n", $this->LE, $str);
		return $str;
	}
	
	/**
	 * Adds a custom header.
	 * @access public
	 * @return void
	 */
	public function AddCustomHeader($custom_header) {
		$this->CustomHeader[] = explode(':', $custom_header, 2);
	}
	
	/**
	 * Evaluates the message and returns modifications for inline images and backgrounds
	 * @access public
	 * @return $message
	 */
	public function MsgHTML($message,$basedir='') {
		preg_match_all("/(src|background)=\"(.*)\"/Ui", $message, $images);
		if(isset($images[2])) {
			foreach($images[2] as $i => $url) {
				// do not change urls for absolute images (thanks to corvuscorax)
				if (!preg_match('/^[A-z][A-z]*:\/\//',$url)) {
					$filename = basename($url);
					$directory = dirname($url);
					($directory == '.')?$directory='':'';
					$cid = 'cid:' . md5($filename);
					$fileParts = split("\.", $filename);
					$ext = $fileParts[1];
					$mimeType = $this->_mime_types($ext);
					if ( strlen($basedir) > 1 && substr($basedir,-1) != '/') { $basedir .= '/'; }
					if ( strlen($directory) > 1 && substr($basedir,-1) != '/') { $directory .= '/'; }
					$this->AddEmbeddedImage($basedir.$directory.$filename, md5($filename), $filename, 'base64', $mimeType);
					if ( $this->AddEmbeddedImage($basedir.$directory.$filename, md5($filename), $filename, 'base64',$mimeType) ) {
						$message = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$cid."\"", $message);
					}
				}
			}
		}
		$this->IsHTML(true);
		$this->Body = $message;
		$textMsg = trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s','',$message)));
		if ( !empty($textMsg) && empty($this->AltBody) ) {
			$this->AltBody = $textMsg;
		}
		if ( empty($this->AltBody) ) {
			$this->AltBody = 'To view this email message, open the email in with HTML compatibility!' . "\n\n";
		}
	}
	
	/**
	 * Gets the mime type of the embedded or inline image
	 * @access public
	 * @return mime type of ext
	 */
	public function _mime_types($ext = '') {
		$mimes = array(
				'hqx'   =>  'application/mac-binhex40',
				'cpt'   =>  'application/mac-compactpro',
				'doc'   =>  'application/msword',
				'bin'   =>  'application/macbinary',
				'dms'   =>  'application/octet-stream',
				'lha'   =>  'application/octet-stream',
				'lzh'   =>  'application/octet-stream',
				'exe'   =>  'application/octet-stream',
				'class' =>  'application/octet-stream',
				'psd'   =>  'application/octet-stream',
				'so'    =>  'application/octet-stream',
				'sea'   =>  'application/octet-stream',
				'dll'   =>  'application/octet-stream',
				'oda'   =>  'application/oda',
				'pdf'   =>  'application/pdf',
				'ai'    =>  'application/postscript',
				'eps'   =>  'application/postscript',
				'ps'    =>  'application/postscript',
				'smi'   =>  'application/smil',
				'smil'  =>  'application/smil',
				'mif'   =>  'application/vnd.mif',
				'xls'   =>  'application/vnd.ms-excel',
				'ppt'   =>  'application/vnd.ms-powerpoint',
				'wbxml' =>  'application/vnd.wap.wbxml',
				'wmlc'  =>  'application/vnd.wap.wmlc',
				'dcr'   =>  'application/x-director',
				'dir'   =>  'application/x-director',
				'dxr'   =>  'application/x-director',
				'dvi'   =>  'application/x-dvi',
				'gtar'  =>  'application/x-gtar',
				'php'   =>  'application/x-httpd-php',
				'php4'  =>  'application/x-httpd-php',
				'php3'  =>  'application/x-httpd-php',
				'phtml' =>  'application/x-httpd-php',
				'phps'  =>  'application/x-httpd-php-source',
				'js'    =>  'application/x-javascript',
				'swf'   =>  'application/x-shockwave-flash',
				'sit'   =>  'application/x-stuffit',
				'tar'   =>  'application/x-tar',
				'tgz'   =>  'application/x-tar',
				'xhtml' =>  'application/xhtml+xml',
				'xht'   =>  'application/xhtml+xml',
				'zip'   =>  'application/zip',
				'mid'   =>  'audio/midi',
				'midi'  =>  'audio/midi',
				'mpga'  =>  'audio/mpeg',
				'mp2'   =>  'audio/mpeg',
				'mp3'   =>  'audio/mpeg',
				'aif'   =>  'audio/x-aiff',
				'aiff'  =>  'audio/x-aiff',
				'aifc'  =>  'audio/x-aiff',
				'ram'   =>  'audio/x-pn-realaudio',
				'rm'    =>  'audio/x-pn-realaudio',
				'rpm'   =>  'audio/x-pn-realaudio-plugin',
				'ra'    =>  'audio/x-realaudio',
				'rv'    =>  'video/vnd.rn-realvideo',
				'wav'   =>  'audio/x-wav',
				'bmp'   =>  'image/bmp',
				'gif'   =>  'image/gif',
				'jpeg'  =>  'image/jpeg',
				'jpg'   =>  'image/jpeg',
				'jpe'   =>  'image/jpeg',
				'png'   =>  'image/png',
				'tiff'  =>  'image/tiff',
				'tif'   =>  'image/tiff',
				'css'   =>  'text/css',
				'html'  =>  'text/html',
				'htm'   =>  'text/html',
				'shtml' =>  'text/html',
				'txt'   =>  'text/plain',
				'text'  =>  'text/plain',
				'log'   =>  'text/plain',
				'rtx'   =>  'text/richtext',
				'rtf'   =>  'text/rtf',
				'xml'   =>  'text/xml',
				'xsl'   =>  'text/xml',
				'mpeg'  =>  'video/mpeg',
				'mpg'   =>  'video/mpeg',
				'mpe'   =>  'video/mpeg',
				'qt'    =>  'video/quicktime',
				'mov'   =>  'video/quicktime',
				'avi'   =>  'video/x-msvideo',
				'movie' =>  'video/x-sgi-movie',
				'doc'   =>  'application/msword',
				'word'  =>  'application/msword',
				'xl'    =>  'application/excel',
				'eml'   =>  'message/rfc822'
		);
		return ( ! isset($mimes[strtolower($ext)])) ? 'application/octet-stream' : $mimes[strtolower($ext)];
	}
	
	/**
	 * Set (or reset) Class Objects (variables)
	 *
	 * Usage Example:
	 * $page->set('X-Priority', '3');
	 *
	 * @access public
	 * @param string $name Parameter Name
	 * @param mixed $value Parameter Value
	 * NOTE: will not work with arrays, there are no arrays to set/reset
	 */
	public function set ( $name, $value = '' ) {
		if ( isset($this->$name) ) {
			$this->$name = $value;
		} else {
			$this->SetError('Cannot set or reset variable ' . $name);
			return false;
		}
	}
	
	/**
	 * Read a file from a supplied filename and return it.
	 *
	 * @access public
	 * @param string $filename Parameter File Name
	 */
	public function getFile($filename) {
		$return = '';
		if ($fp = fopen($filename, 'rb')) {
			while (!feof($fp)) {
				$return .= fread($fp, 1024);
			}
			fclose($fp);
			return $return;
		} else {
			return false;
		}
	}
	
	/**
	 * Strips newlines to prevent header injection.
	 * @access public
	 * @param string $str String
	 * @return string
	 */
	public function SecureHeader($str) {
		$str = trim($str);
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "", $str);
		return $str;
	}
	
	/**
	 * Set the private key file and password to sign the message.
	 *
	 * @access public
	 * @param string $key_filename Parameter File Name
	 * @param string $key_pass Password for private key
	 */
	public function Sign($cert_filename, $key_filename, $key_pass) {
		$this->sign_cert_file = $cert_filename;
		$this->sign_key_file = $key_filename;
		$this->sign_key_pass = $key_pass;
	}
}
?>