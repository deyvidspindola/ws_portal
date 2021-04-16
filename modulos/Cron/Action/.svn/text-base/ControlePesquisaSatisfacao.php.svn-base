<?php
/**
 * Rotina responsável por:
 *  Gerar as pesquisas para os clientes ou gerenciadoras;
 *  Enviar e-mail contendo o link das pesquisas;
 *  Concluir as pesquisas que estão há 15 dias sem respostas.
 *  Reenviar pesquisas
 * 
 * @file ControlePesquisaSatisfacao.php
 * @author marcioferreira
 * @version 01/07/2013 10:08:06
 * @since 01/07/2013 10:08:06
 * @package SASCAR ControlePesquisaSatisfacao.php 
 */

// INCLUDES

//classe reponsável em enviar os e-mails 
require_once _SITEDIR_ .'modulos/Principal/Action/ServicoEnvioEmail.php';

require_once _CRONDIR_ .'lib/validaCronProcess.php';

//classe responsável em processar dados das pesquisas no bd
require_once _MODULEDIR_ . 'Cron/DAO/ControlePesquisaSatisfacaoDAO.php';


class ControlePesquisaSatisfacao{
	
	//atributos
	private $conn;
	private $tipoPesquisa;
	
	// Construtor
	public function __construct() {
	
		global $conn;
	
		//seta variável de conexão
		$this->conn = $conn;
	
		// Objeto  - DAO
		$this->dao = new ControlePesquisaSatisfacaoDAO($conn);
	}
	
	
	public function verificarPesquisaSatisfacao(){
		
		try{
			$nomeProcesso = 'controle_pesquisa_satisfacao.php';

			if(burnCronProcess($nomeProcesso) === true){
				throw new Exception (" O processo [$nomeProcesso] ainda está em processamento.");
			}

			if(!$this->conn){
				throw new Exception (" Erro ao conectar-se no banco de dados.");
			}

			//inicia transação no bd
			$this->dao->begin();
			
				//executa os métodos
				$this->pesquisarPosVendaAreaTecnica();
				
				$this->pesquisarOrdemServicoInstalacao();
				
				$this->pesquisarOrdemServicoManutencao();
				
				$this->selecionarReenvioPesquisas();
				
				$this->atualizarPesquisas();
			
			//efetua as alterações no bd
			$this->dao->commit();
			
		}catch (Exception $e){
			
			//desfaz todas as alterações no bd em caso de erro
			$this->dao->rollback();
			echo "<font color='red'>".$e->getMessage()." </font>";
			exit;
		}
	}
	

	/**
	 * Recupera clientes de Visita Pós Venda e Área Técnica
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	private function pesquisarPosVendaAreaTecnica(){

		try{
			$this->setTipoPesquisa(8);

			//recupera o layout de e-mail
			$dadosEmail = $this->dao->getDadosCorpoEmail('SASCAR');

			//se encontrou layout dos e-mails, então, inicia o processo
			if (is_object($dadosEmail)){

				//recupera dados do questionário por tipo de pesquisa informada
				$dadosQuestionario = $this->getDadosQuestionario($this->getTipoPesquisa());

				//verifica se houve retorno de questionários ativos 
				if(is_object($dadosQuestionario)){

					$resultadoPesquisa = $this->dao->pesquisarPosVendaAreaTecnica();

					//pega os dados pesquisados para enviar o email
					if(is_array($resultadoPesquisa) > 0){

						foreach ($resultadoPesquisa as $dadosPesquisa){

							$retorno = $this->controlarEmails($dadosPesquisa, $dadosQuestionario, $dadosEmail);

						}

						echo '<br/><b><font color="blue">Processo do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b>, finalizado com sucesso.</font></b><br/><br/>';
							
					}else{
						throw new Exception('<br/><br/>Nao ha clientes do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para processar o envio de e-mail.');
					}

				}else{
					throw new Exception('<br/><br/>Nao existe questionario ativo do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para processar. Acao cancelada.');
				}

			}else{
				echo '<br/><br/><font color="red">Nao foi possivel enviar a pesquisa do tipo <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para o e-mail   <b>'.$dadosPesquisa['cliemail'].'</b>  , layout de e-mail nao encontrado.</font>';
			}
				
		    return true;

		}catch (Exception $e){
			echo "<font color='red'>".$e->getMessage()." </font>";
			return false;
		}
	}


	/**
	 * Recupera clientes Ordem de Serviço - Instalação
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	private function pesquisarOrdemServicoInstalacao(){
		
		try{
			$this->setTipoPesquisa(9);
		
			//recupera dados do questionário por tipo de pesquisa informada
			$dadosQuestionario = $this->getDadosQuestionario($this->getTipoPesquisa());
			
			//verifica se houve retorno de questionários ativos
			if(is_object($dadosQuestionario)){
			
				$resultadoPesquisa = $this->dao->pesquisarOrdemServicoInstalacao();
				
				//pega os dados pesquisados para enviar o email
				if(is_array($resultadoPesquisa) > 0){

					foreach ($resultadoPesquisa as $dadosPesquisa){

						//recupera o layout de e-mail
						$dadosEmail = $this->dao->getDadosCorpoEmail($dadosPesquisa['tipo_layout']);
							
						//se encontrou layout do e-mail, então, inicia o processo
						if (is_object($dadosEmail)){

							$retorno = $this->controlarEmails($dadosPesquisa, $dadosQuestionario, $dadosEmail);

						}else{
							echo '<br/><br/><font color="red">Nao foi possivel enviar a pesquisa do tipo <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para o e-mail   <b>'.$dadosPesquisa['cliemail'].'</b> , tipo layout de e-mail ( '.$dadosPesquisa['tipo_layout'].' )  nao encontrado.</font>';
						}
					}
					
					echo '<br/><b><font color="blue">Processo do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b>, finalizado com sucesso.</font></b><br/><br/>';

				}else{
					throw new Exception('<br/><br/>Nao ha clientes do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para processar o envio de e-mail.');
				}
				
			}else{
				throw new Exception('<br/><br/>Nao existe questionario ativo do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para processar. Acao cancelada.');
			}
		
		}catch (Exception $e){
			echo "<font color='red'>".$e->getMessage()." </font>";
			return false;
		}
	}
	
	
	/**
	 * Recupera clientes Ordem de Serviço - Manutenção
	 */
	private function pesquisarOrdemServicoManutencao(){

		try{
			
			$this->setTipoPesquisa(10);
			
			//recupera dados do questionário por tipo de pesquisa informada
			$dadosQuestionario = $this->getDadosQuestionario($this->getTipoPesquisa());
				
			//verifica se houve retorno de questionários ativos
			if(is_object($dadosQuestionario)){
					
				$resultadoPesquisa = $this->dao->pesquisarOrdemServicoManutencao();
				
				//pega os dados pesquisados para enviar o email
				if(is_array($resultadoPesquisa) > 0){
			
					foreach ($resultadoPesquisa as $dadosPesquisa){
			
						//regras implementadas de acordo doc Técnico STI - 82557
						if($resultadoPesquisa['tipo_proposta'] === 'SIGGO'){
							
							$dadosPesquisa['tipo_layout'] = $resultadoPesquisa['tipo_proposta'];
						
						}elseif($resultadoPesquisa['tipo_contrato'] === 'VIVO'){
							
							$dadosPesquisa['tipo_layout'] = $resultadoPesquisa['tipo_contrato'];
						
						}else{
							$dadosPesquisa['tipo_layout'] = 'SASCAR';
						}
						// fim regras
						
						//recupera o layout de e-mail
						$dadosEmail = $this->dao->getDadosCorpoEmail($dadosPesquisa['tipo_layout']);
						
						//se encontrou layout do e-mail, então, inicia o processo
						if (is_object($dadosEmail)){
			
							$retorno = $this->controlarEmails($dadosPesquisa, $dadosQuestionario, $dadosEmail);
			
						}else{
							echo '<br/><br/><font color="red">Nao foi possivel enviar a pesquisa do tipo <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para o e-mail   <b>'.$dadosPesquisa['cliemail'].'</b>   , tipo layout de e-mail ( '.$dadosPesquisa['tipo_layout'].' )  nao encontrado.</font>';
						}
					}
						
					echo '<br/><b><font color="blue">Processo do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b>, finalizado com sucesso.</font></b><br/><br/>';
			
				}else{
					throw new Exception('<br/><br/>Nao ha clientes do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para processar o envio de e-mail.');
				}
			
			}else{
				throw new Exception('<br/><br/>Nao existe questionario ativo do tipo de pesquisa <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para processar. Acao cancelada.');
			}
			
		}catch (Exception $e){
			echo "<font color='red'>".$e->getMessage()." </font>";
			return false;
		}
	}
	
	
	/**
	 * Recupera clientes com perguntas respondidas e que possuem reenvio de email
	 */
	private function selecionarReenvioPesquisas(){
		
		try{
			
			$this->setTipoPesquisa('reenvio');
				
			$resultadoPesquisa = $this->dao->selecionarReenvioPesquisas();
			
			//pega os dados pesquisados para enviar o email
			if(is_array($resultadoPesquisa) > 0){
					
				foreach ($resultadoPesquisa as $dadosPesquisa){
						
					//recupera o layout de e-mail
					$dadosEmail = $this->dao->getDadosCorpoEmail($dadosPesquisa['tipo_layout']);
					
					//se encontrou layout do e-mail, então, inicia o processo
					if (is_object($dadosEmail)){
							
						$retorno = $this->controlarEmails($dadosPesquisa, "", $dadosEmail);
							
					}else{
						echo '<br/><br/><font color="red">Nao foi possivel enviar a pesquisa do tipo <b>'.$this->getMsgProcesso($this->getTipoPesquisa()).'</b> para o e-mail   <b>'.$dadosPesquisa['cliemail'].'</b>   , tipo layout de e-mail ( '.$dadosPesquisa['tipo_layout'].' )  nao encontrado.</font>';
					}
				}
			}

		}catch (Exception $e){
			echo "<font color='red'>".$e->getMessage()." </font>";
			return false;
		}
	}

	/**
	 * Efetua as validações antes de enviar o e-mail
	 * 
	 * @param array $resultadoPesquisa
	 * @param int $tipoPesquisa
	 * @throws Exception
	 * @return boolean
	 */
	private function controlarEmails($dados, $dadosQuestionario = NULL, $dadosEmail){
	
		try{
			
			//Inclui registro na tabela de Controle de E-mail (posvenda_controle_questionario) retornando o id do controle
			$id_controle_questionario = $this->setControleEnvioEmail($dados, $dadosQuestionario);

			//valida se possuiu e-mail para efetuar o envio
			if(!empty($dados['cliemail'])){

				//verifica se o cliente possui mais de um e-mail cadastrado
				$listaDeEmails = $dados['cliemail'];

				//separa os e-mails
				$listaEmail = explode(';', $listaDeEmails);

				//envia o email para a lista
				foreach ($listaEmail as $email){

					$dados['cliemail'] = $email;

					//verifica se é um email válido
					if($this->validarEmail(trim($dados['cliemail']))){

						//envia email para os clientes
						$envia_email = $this->enviaEmailClientes($dadosEmail, $dados, $dadosQuestionario, $id_controle_questionario);

						if($envia_email == 1){

							$status = 1; //envio realizado com sucesso
							$obs_envio = $this->getMsgProcesso('sucesso');

						}else{
							$status = 0; //envio não realizado
							$obs_envio = $this->getMsgProcesso('erro', $envia_email);
						}

					}else{
						$status = 0; //envio não realizado
						$obs_envio = $this->getMsgProcesso('email_invalido');
					}

					echo "<br/><font color='blue'>".$dados['cliemail']." -> ".$obs_envio."</font>";
				}

			}else{
				$status = 0; //envio não realizado
				$obs_envio = $this->getMsgProcesso('email_vazio');
			}
				
			//atualiza a tabela posvenda_controle_questionario com novos dados
			$this->dao->atualizarControleEnvioEmail($id_controle_questionario, $status, $obs_envio);

			return true;

		}catch(Exception $e){
	
			echo "<font color='red'>".$e->getMessage()." </font>";
			return false;
		}
	}
	
	
	/**
	 * Seta mensagem de observação de acordo o erro encontrado no envio de e-mail
	 * 
	 * @param string $envia_email
	 * @return string
	 */
	private function getMsgProcesso($param = NULL, $msg_retorno_servidor = NULL){
		
		if($param === 'erro'){
		
			$msg = "E-mail não enviado. $msg_retorno_servidor ";
		
		}else if($param === 'email_vazio'){
		
			$msg = "E-mail não enviado. Endereço de email não encontrado ";
		
		}elseif($param === 'email_invalido'){
			
			$msg = "E-mail não enviado. Formato de e-mail inválido";
			
		}elseif($param === 'sucesso'){
			
			$msg = "E-mail enviado com sucesso";
		
		}elseif($this->getTipoPesquisa() === 8){
			
			$msg = "Pos-venda area tecnica";
		
		}elseif($this->getTipoPesquisa() === 9){
			
			$msg = "Servico Instalacao";
		
		}elseif($this->getTipoPesquisa() === 10){
			
			$msg = "Servico Manutencao";
		
		}elseif($this->getTipoPesquisa() === 'reenvio'){
			
			$msg = "Reenvio Pesquisa";
		
		}else{
			$msg = "E-mail não enviado. Erro desconhecido";
		}
				
		return $msg;
		
	}
	
	
	/**
	 * Recupera questionários Ativos dos tipos: Pesquisa Pós Venda, Instalação e Manutenção.
	 *
	 * @param int $param
	 * @return  object
	 */
	private function getDadosQuestionario($param = NULL){
	
		return $this->dao->getDadosQuestionario($param);
	
	}
	
	/**
	 * Controla o envio de e-mail
	 *  
	 * @param int $clioid           --Código do Cliente ou da Gerenciadora vinculado a visita;
	 * @param int $cod_questionario --Código do Questionário selecionado;
	 * @param int $tipo_pesquisa    --Tipo de Pesquisa = Pós Venda, Instalação e Manutenção ##valores possíveis : 8,9,10
	 * @param int $id_visita        --Id da vista selecionada;
	 * @param int $status           --Status (de acordo com o retorno da tentativa de envio de e-mail);
	 *
	 * @throws Exception
	 * @return boolean
	 */
	private function setControleEnvioEmail($dados, $cod_questionario){
		
		$clioid           = $dados['clioid'];
		$gerenciadora     = $dados['gerenciadora'];
		$email_cliente    = $dados['cliemail'];
		$tipo_pesquisa    = $this->getTipoPesquisa();
		
		//Pós Venda
		if($tipo_pesquisa == 8){

			$id_visita = $dados['id_visita'];
			$ordoid = 'NULL';
			
		//Instalação (9) ou Manutenção (10) ou para reenvio de emails
		}else{
				
			$id_visita = 'NULL'; //não é visita
			$ordoid = $dados['ordem'];//ordem de serviço
			
			//verifica se é para reenvio de e-mail, então, seta as variáveis com o resultado da query (que são dados diferentes)
			if($dados['reenvio'] === '1'){
				$cod_questionario->psvoid = $dados['id_questio'];
				$tipo_pesquisa    = $dados['tipo_pesquisa'];
			}
		}
		
		$id_controle_questionario = $this->dao->setControleEnvioEmail($clioid, $gerenciadora, $ordoid, $cod_questionario->psvoid, $tipo_pesquisa, $id_visita, $email_cliente); 
		
		return $id_controle_questionario;
	}
	
	
	/**
	 * Filtra todas as pesquisas que estão sem resposta (status = 0 ou 1) a 15 dias (a partir da data de envio
	 * do e-mail) e atualiza para  Status = 3 (Concluída automaticamente (Expirada)).
	 * Atualiza também a data do status para a data atual.
	 */
	private function atualizarPesquisas(){

		$retorno = $this->dao->atualizarPesquisas();

		if($retorno == 1){

			echo "<br/><br/><font color='blue'>Pesquisas atualizadas com sucesso </font><br/><br/>";
				
		}else{
			throw new Exception($retorno);
		}

		return true;
	}
	
	/**
	 * Envia os emails de acordo com os dados passados via parâmetro
	 * @author Márcio Sampaio ferreira
	 *
	 * @param array $dadosEmail
	 * @param array $dados
	 */
	private function enviaEmailClientes($dadosEmail, $dados, $dadosQuestionario, $id_controle_questionario){
	
		try{
			
			//instância de classe de configurações de servidores para envio de email
			$servicoEnvioEmail = new ServicoEnvioEmail();

			$email_cliente   = $dados['cliemail'];
					
			$clioid = $dados['clioid'];
				
			//verifica se é cliente ou gerenciadora para pós-venda
			if($this->getTipoPesquisa() == 8){//Pós Venda
				//verifica se é cliente ou gerenciadora para pegar o id
				if(empty($dados['clioid'])){
					$clioid = $dados['gerenciadora'];//gerenciadora
				}
			}
			
			//id do questionário
			$htmlEmail->corpo_email = str_replace('$codControleQuestionario', $id_controle_questionario, $dadosEmail->corpo_email);
			
			//id do cliente ou gerenciadora
			$htmlEmail->corpo_email = str_replace('$codCliente', $clioid, $htmlEmail->corpo_email);
			
			//neste caso a origem sempre será 'L'
			$htmlEmail->corpo_email = str_replace('$origem', 'L', $htmlEmail->corpo_email);
			
			//recupera e-mail de testes
			if($_SESSION['servidor_teste'] == 1){
				
				$email_cliente = "";
				//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
				$emailTeste = $this->dao->getEmailTeste();
							
				if(!is_object($emailTeste)){
					throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
				}
				
				$email_cliente = $emailTeste->pcsidescricao;
				
			}
	
			//envia o email
			$envio_email = $servicoEnvioEmail->enviarEmail(	
					
					$email_cliente, 
					$dadosEmail->assunto_email, 
					$htmlEmail->corpo_email, 
					$arquivo_anexo = null,
					$email_copia = null,
					$email_copia_oculta = null,
					$dadosEmail->servidor,
					$emailTeste->pcsidescricao//$email_desenvolvedor = null
			);
			
			if(!empty($envio_email['erro'])){
				throw new exception($envio_email['msg']);
			}
			
			//imprime email que será enviado para o cliente em ambiente de testes
			if($_SESSION['servidor_teste'] == 1){
				print($htmlEmail->corpo_email);
				print('<br/><br/>');
			}
			
			return true;
		
		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	/**
	 * Verifica se o e-mail é válido
	 * @param string $email
	 * @return boolean
	 */
	private function validarEmail($email){
		
		$valida = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
		
		if(empty($email)){

			return false;
			
		}elseif (preg_match($valida, $email)){
			
			return true;
			
		} else {
			
			return false;
		}
	}
	
	//gets e sets
	private function setTipoPesquisa($valor){
		$this->tipoPesquisa = $valor;
	}
	
	private function getTipoPesquisa(){
		return $this->tipoPesquisa;
	}
	
}


?>