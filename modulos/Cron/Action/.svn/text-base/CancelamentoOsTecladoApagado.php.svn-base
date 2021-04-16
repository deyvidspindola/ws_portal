<?php

/**
 * Classe responsável por cancelar OSs 
 * de assistência para teclado, quando
 * o defeito alegado é "teclado apagado".
 *
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 13/05/2016
 * @category Class
 * @package CancelamentoOsTecladoApagado
 *
 */

require_once _MODULEDIR_ . 'Cron/DAO/CancelamentoOsTecladoApagadoDAO.php';

class CancelamentoOsTecladoApagado {

    private $dao;
	
	/**
	 * Realiza as ações de busca de dados 
	 * de OS com baixas de estoque incorretas
	 * 
	 * @param
	 * @return boolean
	 */	
	public function cancelarOsTecladoApagado() {
		try{
			$listaOs = null;
			$retorno = 0;
			
			// Inicia transação
			$this->dao->begin();
			
			// RT002 - Pesquisar parâmetros para consulta.
			if (! $parametros = $this->dao->buscarDadosDominio()) {
				throw new Exception('Erro ao buscar dados de domínio.');
			}
			// Atribui parâmetros para consulta.
			$parametro1 = null;
			$parametro2 = null;			
			foreach ($parametros as $resultado){
				$parametro1 = ($resultado->ossoid != '') ? $resultado->ossoid : $parametro1;
				$parametro2 = ($resultado->clicloid != '') ? $resultado->clicloid : $parametro2;
			}
			
			// RT001 - Pesquisar
			if (! $dados = $this->dao->buscarDadosOS($parametro1,$parametro2)) {
				throw new Exception('Erro ao buscar dados de OSs.');
			}
			
			//RT004 - Buscar dados de e-mail
			if (! $dadosEmail = $this->dao->buscarDadosEmail()){
				throw new Exception('Erro ao buscar dados de e-mail.');				
			}			
			foreach ($dadosEmail as $resultado){
				$seeremetente 	= $resultado->seeremetente;
				$seecabecalho 	= $resultado->seecabecalho;
				$seecorpo 		= $resultado->seecorpo;
			}
			
			// Percorre os resultados da consulta
			foreach ($dados as $resultado){
				
				$ordoid 					= $resultado->ordoid;
				$orddt_ordem 				= $resultado->orddt_ordem;
				$ositoid 					= $resultado->ositoid;
				$osaoid 					= $resultado->osaoid;
				$data_ultimo_agendamento 	= $resultado->data_ultimo_agendamento;
				$cliemail 					= $resultado->cliemail;
				$clinome 					= $resultado->clinome;
				$condt_exclusao 			= $resultado->condt_exclusao;
				$veioid 					= $resultado->conveioid;
				$email_os 					= $resultado->email_os;
				$clioid_os					= $resultado->clioid;
				
				$resultado->seeremetente 	= $seeremetente;
				$resultado->seecabecalho 	= $seecabecalho;
				$resultado->seecorpo 		= $seecorpo;
				
				// RT003 - Buscar ultima mensagem.
				// O sistema deverá buscar no banco gerenciadora2, a ultima mensagem recebida para cada 
				// item da regra RT001, e caso não exista alguma mensagem recebida e lida ou enviada, 
				// no período entre abertura da ordem de servico e a data de agendamento, o item deverá 
				// ser excluído do array.				
				if ($this->dao->buscarDadosMSG($veioid,$orddt_ordem,$data_ultimo_agendamento, $clioid_os ) > 0) {
					
					//RT004 - Cancelar OS
					if (! $this->dao->cancelarOS($ordoid)) {
						throw new Exception('Erro ao cancelar OS->'. $ordoid .'.');
					}
					
					//RT004 - Criar histórico de cancelamento da OS
					if (! $this->dao->inserirHistoricoOS($ordoid)) {
						throw new Exception('Erro ao inserir histórico da OS->'. $ordoid .'.');
					}
					
					//RT004 - Cancelar agendamento da OS
					if ($osaoid != '') {
						if (! $this->dao->cancelarAgendamentoOS($osaoid)) {
							throw new Exception('Erro ao cancelar agendamento->'. $osaoid .' da OS.');
						}
					}
					
					//RT004 - Cancelar os itens da OS
					if (! $this->dao->cancelarItemOS($ositoid)) {
						throw new Exception('Erro ao cancelar item->'. $ositoid .' da OS.');
					}
					
					//RT004 - Enviar e-mail
					if (($cliemail != '' || $email_os != '') && $condt_exclusao == '' && $veioid != '') {

						// Atribui destinatário conforme ambiente
						if ($_SESSION["servidor_teste"] == 1) {
							$resultado->destinatario = _EMAIL_TESTE_;
						}
						else{
							
							if($cliemail != ''){
								$resultado->destinatario = $cliemail;
							}
							if($cliemail != '' && $email_os != ''){
								$resultado->destinatario.= ";";
							}
							if($email_os != ''){
								$resultado->destinatario.= $email_os;
							}
							
						}
						
						if (! $this->enviarEmail($resultado)) {
							throw new Exception('Erro ao enviar e-mail.');
						}
					}
					
					$listaOs.= $ordoid.';';
					$retorno++;
					
				}
				
			}
			
			// Finaliza transação
			$this->dao->commit();
			
			// Gera LOG txt
			if ($listaOs != '') {
				
				$this->gerarLOG($listaOs);
				
			}
		}
		
		catch(Exception $e) {
			// Reverte ações na transação
    		$this->dao->rollback();
            echo $e->getMessage();
			$retorno = null;

    	}
		
		return $retorno;
		
	}

	/**
	 * Envio do email
	 *
	 * @param stdClass $resultado
	 * @return boolean
	 */
	private function enviarEmail(stdClass $resultado) {
	
		// Substitui as TAGs definidas pelos valores referentes ao registro
		$resultado->seecorpo = str_replace('[cliente]', $resultado->clinome, $resultado->seecorpo);
		$resultado->seecorpo = str_replace('[numeroordem]', $resultado->ordoid, $resultado->seecorpo);
		$resultado->seecorpo = str_replace('[dataordem]', $resultado->dt_ordem, $resultado->seecorpo);
		$resultado->seecorpo = str_replace('[veiculoplaca]', $resultado->veiplaca, $resultado->seecorpo);
	
		// Atribui as variáveis para envio
		$phpmailer = new PHPMailer();
		$phpmailer->isSmtp();
		$phpmailer->From = $resultado->seeremetente;
		$phpmailer->FromName = "Sascar";
		$phpmailer->ClearAllRecipients();
		$phpmailer->AddAddress($resultado->destinatario);
		$phpmailer->Subject = $resultado->seecabecalho;
		$phpmailer->MsgHTML($resultado->seecorpo);
		
		if (!$phpmailer->Send()) {
			return false;
		}
	
		return true;
	}

	/**
	 * Gera log de OSs canceladas
	 *
	 * @param stdClass $listaOs
	 * @return boolean
	 */
	private function gerarLOG(stdClass $listaOs) {
		
		$fp = fopen("/tmp/OsCanceladasTecladoApagado.txt", 'w+');
		fwrite($fp, $listaOs);
		fclose($fp);
			
	}	
	
    /**
     * Metodo Construtor
     */
    public function __construct() {
        $this->dao = new CancelamentoOsTecladoApagadoDAO();
    }

}
