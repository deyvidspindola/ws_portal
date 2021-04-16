<?php
/**
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 */
 
/**
 * Classe padrão para envio de emails
 */
require _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
require _SITEDIR_ . 'lib/Atom/Config/Sistema.php';


class CrnEnvioContatoCliente {

    private $dao;

    public function __construct($conn) {
        $this->dao = new CrnEnvioContatoClienteDAO($conn);
    }

    /**
     * Busca dados de contatos de clientes
     */
    public function buscarContatoCliente() {

        return $this->dao->buscarContatoCliente();
    }

    public function enviarEmail($contato) {

        //destinatarios do email
        if ($_SESSION['servidor_teste'] == 1) {
            $addAddress = _EMAIL_TESTE_;
        } else {
            $addAddress ='atendimentoaocliente@sascar.com.br';
        }

        $mail = new PHPMailer();
        $mail->ClearAllRecipients();
        $mail->IsSMTP();
        $mail->From 	= "sistema@sascar.com.br";
        $mail->FromName = "Intranet SASCAR - E-mail automático";
        $mail->Subject 	= "Solitação Fale Conosco"; 

	$descricao = $contato['descricao'];
	if(strstr($descricao,chr(196).chr(177)) ||  strstr($descricao,chr(196).chr(176)) || strstr($descricao,chr(195).chr(182)) || strstr($descricao,chr(195).chr(150)) || strstr($descricao,chr(195).chr(167)) || strstr($descricao,chr(195).chr(135))  ||  strstr($descricao,chr(197).chr(159)) || strstr($descricao,chr(197).chr(158)) || strstr($descricao,chr(196).chr(159)) || strstr($descricao,chr(196).chr(158)) || strstr($descricao,chr(195).chr(188)) || strstr($descricao,chr(195).chr(156)) ){ 
		$descricao = strtoupper(utf8_decode($descricao));
	}else{
		$descricao = strtoupper($descricao);
	}
	
	$msgHTML ="
		<html>
			<head>
				<style>
					.content {
						width: 680px;
						margin: 30px;
						font-size: 13px;
						font-family: Times New Roman, Times, Serif;
						line-height: 100%;
						border: none;
					}
					.content p {
						line-height: 120%;
						margin: 5px;
					}
					.margin-paragraph {
						margin-left: 20px !important;
					}
				</style>
			</head>
			<body>
				<div class=\"content\">
					<br/>
					<br/>
					<br/>
					<p>Segue solitação recebida via Fale Conosco:</p>
					<br/>
					<p class=\"margin-paragraph\"><strong>Data Cadastro:</strong> ".$contato['datacad']."</p>
					<p class=\"margin-paragraph\"><strong>Código:</strong> ".$contato['csugoid']."</p>
					<p class=\"margin-paragraph\"><strong>Pendência:</strong> ".$contato['pendencia']."</p>
					<p class=\"margin-paragraph\"><strong>Cliente/Contato:</strong> ".$contato['cliente']."</p>
					<p class=\"margin-paragraph\"><strong>Termo:</strong> ".$contato['termo']."</p>
					<p class=\"margin-paragraph\"><strong>Veículo:</strong> ".$contato['placa']."</p>
					<p class=\"margin-paragraph\"><strong>Tipo:</strong> ".$contato['tipo']."</p>
					<p class=\"margin-paragraph\"><strong>Motivo:</strong> ".$contato['motivo']."</p>
					<p class=\"margin-paragraph\"><strong>Descrição:</strong> ".$descricao."</p>
					<br/>
					<br/>
					<p>Sascar</p>
					<br/>
				</div>
			</body>
		</html>";
			
        $mail->MsgHTML($msgHTML);
	$mail->AddAddress($addAddress);

        return $mail->Send();
    }
 
    public function atualizarDataEnvio($csugoid) {
       $this->dao->atualizarDataEnvio($csugoid);
    }

}

?>