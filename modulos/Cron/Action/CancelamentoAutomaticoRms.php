<?php
require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';

/**
*
 */
class CancelamentoAutomaticoRms extends CronAction {
	
	/**
	* @property CancelamentoAutomaticoRmsDAO
	*/
	protected $dao;
	
	/**
	 * Executa as regras
	 * @return CronView
	 */
	public function executar(CancelamentoAutomaticoRmsDAO $dao) {
	
		$this->dao = $dao;
		$requisicoes = array();
	
		$this->dao->transactionBegin();
	
		try {
	
			$diasCancelamento = $this->dao->buscarDiasCancelamentoSemAprovacao();
			
			if(!empty($diasCancelamento) && ($diasCancelamento > 0)){
				
				$requisicoes = $this->dao->buscarRequisicoesCancelamentoSemAprovacao($diasCancelamento);
			
				foreach($requisicoes as $row){
					
					$this->dao->atualizarRmsSemAprovacao($row, $diasCancelamento);
					
					$this->enviarEmailCancelamento($row, $diasCancelamento, 1);
					
				}
				
			}
			
			$diasCancelamento2 = $this->dao->buscarDiasCancelamentoSemAprovador();
			
			if(!empty($diasCancelamento2) && ($diasCancelamento2 > 0)){
				
				$requisicoes2 = $this->dao->buscarRequisicoesCancelamentoSemAprovador($diasCancelamento2);
			
				foreach($requisicoes2 as $row){
					
					$this->dao->atualizarRmsSemAprovacao($row, $diasCancelamento2);
					
					$this->enviarEmailCancelamento($row, $diasCancelamento2, 2 );
					
				}
				
			}
			
			$this->dao->transactionCommit();
			
			echo "Requisições Canceladas SEM APROVAÇÃO: <br />";
			print_r($requisicoes);
			
			echo "<br /> Requisições Canceladas SEM APROVADOR: <br />";
			print_r($requisicoes2);
				
		} catch (Exception $e) {
			
			$this->view->msg = $e->getMessage();				
			$this->dao->transactionRollback();
		}
	
		return $this->view;
	}
	
	

	
	
	 /**
	   *  Função que envia email para o destinatario selecionado na base da Sascar
	   * @param  $rms => Numero da RMS	
	   * @return $dias => Qtde dias parametrizados para cancelamento de requisição sem aprovador/aprovação
	 */
	
	
	public function enviarEmailCancelamento($rms, $dias, $sem ) {
				
		/*
		 * Define conteúdo do email
		 */
		$assunto="RMS $rms Cancelada";
		if( $sem == 1 ) {
			$mensagem="Informamos que a RMS $rms foi cancelada pois não foi aprovada pelo responsável dentro do prazo de $dias dias úteis.";
		} else {
			$mensagem="Informamos que a RMS $rms foi cancelada pois não foi enviada para o gestor dentro do prazo de $dias dias úteis.";
		}
		
		/*
		 * Monta email
		 */
		$mail = new PHPMailer();
		$mail->ClearAllRecipients();
		$mail->IsSMTP();
		$mail->From = "sistema@sascar.com.br";
		$mail->FromName = "Intranet SASCAR - E-mail automático";
		$mail->Subject = "$assunto";
	
		$mail->MsgHTML(" $mensagem ");
	
		/*
		 * Adiciona Destinatario ao email
		 */
		 if ($_SESSION['servidor_teste'] == 1) {
		 	$lista_email = "angelo.frizzo@meta.com.br";
		 } else {
		 	$lista_email = $this->dao->buscarDestinatarioEmail($rms);
		 }
	
		/*
		 * Envia Email
		 */
		if ($lista_email) {
			
			$mail->AddAddress($lista_email);
			
			return $mail->Send();
		 } 
	}
	
}

?>