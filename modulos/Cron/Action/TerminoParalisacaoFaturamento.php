<?php

/**
 * Classe responsável pelas ações de término de paralisação de faturamento
 * @author Marcello Borrmann <marcello.borrmann@meta.com.br>
 * @since 30/03/2015
 * @category Class
 * @package TerminoParalisacaoFaturamento
 *
 */

require_once _MODULEDIR_ . 'Cron/DAO/TerminoParalisacaoFaturamentoDAO.php';

class TerminoParalisacaoFaturamento {

    private $dao;
	
	/**
	 * Realiza as ações referentes ao início da paralisação
	 * 
	 * @param
	 * @return boolean
	 */	
	public function terminarParalisacao() {
		try{
			// Inicia transação
			$this->dao->begin();
			// Atribui a qtd de dias parametrizada para envio
			$dias_env = $this->dao->BuscarDiasEnvio();
			// Atribui a data referente ao último dia do mês corrente
			$dt_final = $this->dao->BuscarUltimoDia();
			
			$dadosEnvio = new stdclass();
			$dadosEnvio->dias_env = $dias_env;
			$dadosEnvio->dt_final = $dt_final;
			// Atribui a data de envio do email			
			$dt_envio = $this->dao->BuscarDataEnvio($dadosEnvio);
			// Atribui a data atual
			$dt_atual = date('Y-m-d');
			//$dt_atual = '2015-04-30';
			
			// Verifica se, de acordo com a data atual, alguma ação deve ser tomada
			if ($dt_atual == $dt_envio || $dt_atual ==  $dt_final) {
				
				// Busca dados a serem enviados ao cliente
				$paralisacoes = $this->dao->pesquisarParametro($dt_final);
				if (!$paralisacoes) {
					throw new Exception('Nenhuma paralisação encontrada.') ;
				}
			
				// Percorre as paralisações
				foreach ($paralisacoes AS $paralisacao) {
					
					// Agrupa resultados por cliente 
					$dados[$paralisacao->clioid][$paralisacao->parfemail_contato][$paralisacao->periodo]['contrato'][]	= $paralisacao->connumero;
					$dados[$paralisacao->clioid][$paralisacao->parfemail_contato][$paralisacao->periodo]['veiculo'][]	= $paralisacao->veiplaca;
					$dados[$paralisacao->clioid][$paralisacao->parfemail_contato][$paralisacao->periodo]['cliente'][]	= $paralisacao->clinome; 

					$veículos[$paralisacao->connumero] = $paralisacao->veioid;
				}
				
				// Verifica se a data atual coincide com a data de envio, conforme a qtd de dias parametrizados
				if ($dt_atual == $dt_envio) { 


					// Busca dados de E-mail
					$assunto = "T%rmino do Per%odo de Paralisa%o";
					$dadosEmail = new stdClass();
					$dadosEmail = $this->dao->pesquisarEmail($assunto);
					/*
					$dadosEmail->seecabecalho;
					$dadosEmail->seecorpo;
					$dadosEmail->seeimagem;
					$dadosEmail->seeimagem_anexo;
					$dadosEmail->seeremetente;
					*/
					
					// Percorre os clientes
					foreach ($dados AS $clioid => $arrayContato) {
					
						// Percorre os emails de contato
						foreach ($arrayContato AS $email_contato => $arrayPeriodo){
								
							// Percorre os periodos
							foreach ($arrayPeriodo AS $periodo => $arrayDados){
					
								// Percorre os veiculos
								foreach ($arrayDados['veiculo'] AS $key => $placa) {
									// Atribui a string de placas
									$veiculos.= ", ".$placa;
								}
								// Retira a vírgula e o espaço, no início da string de placas
								$veiculos = substr($veiculos, 2);
					
								// Percorre os contratos
								foreach ($arrayDados['contrato'] AS $key => $contrato) {
									// Atribui a string de contratos
									$contratos.= ",".$contrato;
								}
								// Retira a vírgula, no início da string de contratos
								$contratos = substr($contratos, 1);
					
								// Atribui outros dados do email ao objeto
								$dadosEmail->cliente 			= $arrayDados['cliente'][0];
								$dadosEmail->parfemail_contato 	= $email_contato;
								$dadosEmail->periodo 			= $periodo;
								$dadosEmail->veiculos			= $veiculos;
								$dadosEmail->contratos			= $contratos;
					
								// Envia email ao contato cadastrado
								$this->enviarEmail($dadosEmail);
					
								// Limpa a string de placas
								unset($veiculos);
								// Limpa a string de contratos
								unset($contratos);
					
							}
						}
					} 
					$retorno = '<br><br>E-mail(s) enviado(s) com êxito.';									
				}
				// Verfica se a data atual coincide com a data do último dia do mês 
				if ($dt_atual ==  $dt_final) {
					foreach ($veículos AS $connumero => $veioid) {
						
						// Atualiza flag sasweb (TRUE) na tabela veiculos
						$this->dao->atualizarVeiculoSasweb($veioid);

						// Atribui dados do Histórico do Termo
						$dadosTermo = new stdClass();
						$dadosTermo->hitconnumero 	= $connumero;
						$dadosTermo->hitusuoid 		= 2750;
						$dadosTermo->hitobs 		= 'Reativada visualização SASWEB. Término da paralisação de faturamento.';
							
						// Gera histórico de permissão de visualização, no contrato
						$this->dao->inserirHistoricoTermo($dadosTermo);
						
					} 
					$retorno = '<br><br>Visualização SASWEB reativada com êxito.';
				}
			}
			
			// Finaliza transação
			$this->dao->commit();
		}
		
		catch(Exception $e) {
			// Reverte ações na transação
    		$this->dao->rollback();
            echo $e->getMessage();
			$retorno = '0';

    	}
		
		return $retorno;
		
	}
	
	/**
	 * Envia email ao contato
	 * 
	 * @param stdClass $email
	 * @return boolean
	 */
	private function enviarEmail(stdClass $email) {
		
		// Atribui destinatário conforme ambiente
		if ($_SESSION["servidor_teste"] == 1) {
			$email->destinatario = _EMAIL_TESTE_;
		} 
		else{
			$email->destinatario = $email->parfemail_contato;
		}
		
		// Substitui as TAGs definidas pelos valores referentes ao registro
		$email->corpo = str_replace('[CLIENTE]', $email->cliente, $email->seecorpo);
		$email->corpo = str_replace('[PLACA]', $email->veiculos, $email->corpo);
		$email->corpo = str_replace('[PERIODO]', $email->periodo, $email->corpo);
		
		// Atribui as variáveis para envio
		$phpmailer = new PHPMailer();
		$phpmailer->isSmtp();
		$phpmailer->From = $email->seeremetente;
		$phpmailer->FromName = "Sascar";
		$phpmailer->ClearAllRecipients();
		$phpmailer->AddAddress($email->destinatario);
		$phpmailer->Subject = $email->seecabecalho;
		$phpmailer->MsgHTML($email->corpo);
	
		if (!$phpmailer->Send()) {
			$email->sucesso = "E-mail não pôde ser enviado. ";
		}
		$email->sucesso = "E-mail enviado com sucesso.";
		
		$this->inserirHistoricoEnvio($email);
		
		return true; 
	}
	
	/**
	 * Inser histórico no termo  de envio do email
	 * 
	 * @param stdClass $email
	 * @return boolean
	 */
	private function inserirHistoricoEnvio(stdClass $email) {
		
		$observacao = "
		<p>". $email->sucesso ."</p>
		<p>&nbsp;</p>
		<p>De: " .$email->seeremetente. "</p>
		<p>Enviada em: " .date('l jS \of F Y h:i:s A'). "</p>
		<p>Para: " .$email->destinatario. "</p>
		<p>Assunto: " .$email->seecabecalho. "</p>
		<p>&nbsp;</p>
		<p>" .$email->corpo. "</p>
		<p>&nbsp;</p>
		";
		
		$arrayContratos = explode(",", $email->contratos);
		foreach ($arrayContratos AS $connumero) {
			
			// Atribui dados do Histórico do Termo
			$dadosTermo = new stdClass();
			$dadosTermo->hitconnumero 	= $connumero;
			$dadosTermo->hitusuoid 		= 2750;
			$dadosTermo->hitobs 		= $observacao;
			// Insere Histórico de Paralisação no Contrato
			$this->dao->inserirHistoricoTermo($dadosTermo);
			
		}
		
		return true; 
	}

    /**
     * Metodo Construtor
     */
    public function __construct() {
        $this->dao = new TerminoParalisacaoFaturamentoDAO();
    }

}