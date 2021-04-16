<?php
require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';
require_once _MODULEDIR_ . 'Cron/DAO/EmailCarenciaReinstalacaoDAO.php';
include _SITEDIR_ .'lib/phpMailer/class.phpmailer.php';

class EmailCarenciaReinstalacao extends CronAction {
	/**
	 * @property EmailCarenciaReinstalacaoDAO
	 */
	protected $dao;
		
	/**
	 * Executa as regras
	 * @return CronView
	 */
	public function executar(EmailCarenciaReinstalacaoDAO $dao) {
	
		$this->dao = $dao;
	
		$this->dao->transactionBegin();
	
		try {
			$resultado = $this->dao->buscarEmailsModelos();
			while ($res = pg_fetch_array($resultado)) {
				$id_cliente = $res[0];
				$cliente = $res[1];
				$veiculo = $res[2];
				$email = $res[3];
				$contrato = $res[4];
				$assunto = $res[5];
				$mensagem = $res[6];
				$email_id = $res[7];
			
				$mensagem = str_replace('[CLIENTE]', $cliente, $mensagem);
				$mensagem = str_replace('[VEICULO]', $veiculo, $mensagem);
			
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->From = "sascar@sascar.com.br";
				$mail->FromName = "Sistema - Sascar";
				$mail->Subject = $assunto;
				$mail->MsgHTML($mensagem);
				$mail->ClearAllRecipients();
				/**
				 * Mudar para $email após testes.
				 */
				$mail->AddAddress($email);
				if(!$mail->Send()){
					//throw new exception('Erro ao enviar email de período de carência');
				} else {				
					$this->dao->salvarEnvioEmailCarencia($contrato, $email_id);
				}
			}	
			$this->dao->transactionCommit();
		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();
			$this->dao->transactionRollback();
		}
		return $this->view;
	}
}