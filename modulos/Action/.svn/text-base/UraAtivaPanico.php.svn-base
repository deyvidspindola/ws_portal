<?php

require_once _MODULEDIR_ . 'Atendimento/Action/UraAtiva.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaParamVO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaRetornoVO.php';
require_once _MODULEDIR_ . 'Atendimento/DAO/UraAtivaPanicoDAO.php';
require_once _SITEDIR_ . 'lib/Components/Data.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

class UraAtivaPanico extends UraAtiva {
	
	const MOTIVO_CONTATO_SEM_SUCESSO	= 'Contato sem sucesso';
	const MOTIVO_PRECISA_VERIFICAR_SQL	= 'Precisa verificar e retorna contato';
	const MOTIVO_PRECISA_VERIFICAR 		= 'Cliente irá verificar e retorna';
	const MOTIVO_ACIONAMENTO_INDEVIDO	= 'Acionamento Indevido';	
	const MOTIVO_GRUPO_DISCADOR			= 'Atendimento%P_nico%Alerta%Cerca';
	const MOTIVO_VEICULO_ROUBADO		= 'Acionamento Veículo roubado';
	const MOTIVO_PANICO_URA_ATIVA		= 'P_nico URA ativa';
	const MOTIVO_PRECISA_VERIFICAR_SEMI	= 'Precisa verificar e retorna o contato';
	
	/**
	 * Torna público um array com as informações para gravar o log de atendimento;
	 */
	public $logAtendimento = array();
	
	public function __construct($conn) {
		
		try {
			$this->dao = new UraAtivaPanicoDAO($conn);
		} catch (Exception $e) {
			throw new Exception('0190'); // Falha na execução do banco
		}
	}

	/**
	 * Trata a reposta da URA
	 * @param $parametros
	 * @return UraAtivaRetornoVO
	 */
	public function navegacao(UraAtivaParamVO $param) {
		
		$horaContato = '';
		$clinome = '';
		
		$this->dao->transactionBegin();
		
		try {
			
			/*
			 * Busca informacoes referente ao panico informado
			 */
			$panico_pendente = $this->dao->buscarPanicoPendente($param->codigoIdentificador);	
			
			/*
			 * Verifica se o panico existe na tabela
			*/
			if(!$panico_pendente){

                $veiculo = $this->dao->buscarDadosTabelaAuxiliar($param->codigoIdentificador);

                if(!empty($veiculo)){

                    $this->dao->excluirPanicoAuxiliarByPapoid($param->codigoIdentificador);

                    $this->dao->removerInsucessoContato($veiculo->cdupveioid);

                    $this->dao->eliminarContatoDiscador($this->dao->campanha, $veiculo->cdupid_contato_discador);

                }

				throw new Exception('0180'); // Nenhuma informacao encontrada referente ao panico informado
			}
			
			/*
			 * VO Contrato
			 */
			$Contrato = $this->dao->getContrato($panico_pendente->contrato);
						
			/*
			 * Busca o atendimento cliente relacionado ao panico
			*/
			$atcoid = $this->dao->buscarAtendimentoCliente($panico_pendente->panico_pendente_oid);
			
			if (empty($atcoid)) {
				throw new Exception('0230'); // Nenhuma informacao encontrada referente ao panico informado
			}
			/*
			 * Dados para os historicos
			 */
			$data_hora = substr($panico_pendente->data_pacote, 0, 16);
			
			$placa = str_replace('-', '', $panico_pendente->placa);
			
			$ultima_localizacao = $this->dao->buscarUltimaLocalizaoVeiculo($panico_pendente->veioid);
			
			/*
			 * Verifica a opcao selecionada
			 */
			switch ($param->opcaoSelecionada) {
				
				# Para roubo disque 1
				case 'PANICO_ROUBO':
				
					/*
					 * Excluir registros auxiliares de atendimento
					 */
					$this->dao->excluirPanicoAuxiliar($atcoid);
					
					/*
					 * Excluir registros auxiliares de atendimento (nova tabela)
					 */
					$this->dao->removerInsucessoContato($panico_pendente->veioid);
					
					/*
					 * Buscar id do motivo para concluir o acesso com o motivo especificado
					 */
					$atmoid = $this->dao->buscarMotivoPorGrupo(self::MOTIVO_VEICULO_ROUBADO, self::MOTIVO_GRUPO_DISCADOR);
	
					/*
					 * Atualiza o motivo do atendimento para Atendimento de Pânico, Alerta e Cerca => Acionamento Veículo roubado [RN62]
					 */
					$this->dao->concluirAcesso($Contrato, $atcoid, $atmoid, 1);
					
					#TODO retorna mensagem 0000
				
				break;
				
				# Se o pânico foi acionado acidentalmente disque 2
				case 'PANICO_ACIDENTAL':
				
					/*
					 * Excluir registros auxiliares de atendimento
					 */
					$this->dao->excluirPanicoAuxiliar($atcoid);

					/*
					 * Excluir registros auxiliares de atendimento (nova tabela)
					 */
					$this->dao->removerInsucessoContato($panico_pendente->veioid);
						
					/*
					 * Buscar id do motivo para concluir o acesso com o motivo especificado
					 */
					$atmoid = $this->dao->buscarMotivoPorGrupo(self::MOTIVO_ACIONAMENTO_INDEVIDO,self::MOTIVO_GRUPO_DISCADOR);
				
					/*
					 * Conclui o atendimento
					 * Gera cobrança [true]
					 */
					$this->dao->concluirAtendimento($Contrato, $atcoid, $atmoid, 1, true);
					
					/*
					 * Exclui o panico da lista de pendentes
					 */
					$this->dao->excluirPanico($panico_pendente->panico_pendente_oid, $atmoid);
					
					/*
					 * Insere historico para o contrato
					 */
					$motivo = $this->dao->buscarDescricaoMotivoPorId($atmoid);
					$this->inserirHistoricoContrato($panico_pendente->contrato, $data_hora, $placa, $ultima_localizacao, $motivo);
					
					#TODO retorna mensagem 0000
					
				break;
				
				# Se precisa verificar e retornar contato disque 3
				case 'PANICO_PRECISA_VERIFICAR':
				
					/*
					 * Excluir registros auxiliares de atendimento
					 */
					$this->dao->excluirPanicoAuxiliar($atcoid);

					/*
					 * Excluir registros auxiliares de atendimento (nova tabela)
					 */
					$this->dao->removerInsucessoContato($panico_pendente->veioid);
						
					/*
					 * Buscar id do motivo para concluir o acesso com o motivo especificado
					 */
					$atmoid = $this->dao->buscarMotivoPorGrupo(self::MOTIVO_PRECISA_VERIFICAR_SQL,self::MOTIVO_GRUPO_DISCADOR);
				
					/*
					 * Atualiza o motivo do atendimento para Atendimento de Pânico, Alerta e Cerca => Precisa verificar e retorna contato
					 */
					$this->dao->concluirAcesso($Contrato, $atcoid, $atmoid, 1);
					
					/*
					 * Cria um acesso pendente
					 */
					$this->dao->inserirAcesso($Contrato, $atcoid, $atmoid, 1);
					
					/*
					 * Insere historico para o contrato
					 */
					$motivo = self::MOTIVO_PRECISA_VERIFICAR . '.';
					$this->inserirHistoricoContrato($Contrato->connumero, $data_hora, $placa, $ultima_localizacao, $motivo);
					
					#TODO retorna mensagem 0000
					
					
				break;
				
				# Para falar com um de nossos atendentes disque 4
				case 'PANICO_FALAR_ATENDENTE':
				
					/*
					 * Excluir registros auxiliares de atendimento
					 */
					$this->dao->excluirPanicoAuxiliar($atcoid);

					/*
					 * Excluir registros auxiliares de atendimento (nova tabela)
					 */
					$this->dao->removerInsucessoContato($panico_pendente->veioid);
						
					/*
					 * Buscar id do motivo para concluir o acesso com o motivo especificado
					 */
					$atmoid = $this->dao->buscarMotivoPorGrupo(self::MOTIVO_PANICO_URA_ATIVA,self::MOTIVO_GRUPO_DISCADOR);
						
					/*
					 * Atualiza o motivo do atendimento para Atendimento de Pânico, Alerta e Cerca => Precisa verificar e retorna contato
					 */
					$this->dao->concluirAcesso($Contrato, $atcoid, $atmoid, 1);
					
					#TODO retorna mensagem 0000
					
					
				break;
				
				# Cliente digitou uma opção inválida
				case 'PANICO_OPCAO_INVALIDA':
				# Cliente não digitou nenhuma opção
				case 'PANICO_NAO_RESPONDEU':
					
						$this->dao->atualizarTentativaInsucessoContato($panico_pendente->veioid, $param->telefoneContato);
						
						$idsTelefoneExterno = $this->dao->buscarInsucessosContato($panico_pendente->veioid);
						
						if(is_array($idsTelefoneExterno) && count($idsTelefoneExterno)) {
							$listaTelefones  = implode(',', $idsTelefoneExterno);
							$listaInsucessos = $this->dao->buscarInsucessoEspecifico($panico_pendente->veioid, $listaTelefones);							

							if($listaInsucessos) {
								$this->dao->atualizarTentativaInsucessoContato($panico_pendente->veioid, '', $listaInsucessos);
							}
						}

                         $this->dao->tratarDataReeenvioInsucessoContato($panico_pendente->veioid);

				break;				
				
				# OPCAO DE RETORNO INVALIDA
				default:
					throw new Exception('0210'); # Opção de menu da URA não encontrada. Verifique os parâmetros informados para WebService
				
			}
			
			$this->dao->transactionCommit();
			
			if($this->dao->isGravaLogAtendimento){
			
				$clinome = $this->dao->getNomeCliente($Contrato->conclioid);
				
				$id_contato = $this->dao->buscarContatoDiscadorEspecifico($atcoid);
				
				$retornoDB2 = $this->dao->buscarInsucessos($id_contato, $panico_pendente->veioid);
				
				if(!empty($retornoDB2)){

					$horaContato 	= isset($retornoDB2['chamada']) ? trim($retornoDB2['chamada']) : '';				
	
					if(!empty($horaContato)){
					
						$hora		 = substr($horaContato, 11, 5);
						$horaContato = str_replace('.', ':', $hora);
							
					}else{
						$horaContato = '';
					}
				}
							
				$conteudoLog = 	date("d/m/Y H:i") . " | "  .  $Contrato->connumero . " | " . $Contrato->conclioid . " | " . $clinome;
				$conteudoLog .=  " | ". $param->opcaoSelecionada .  " | ". $param->telefoneContato . " | ". $horaContato . " | " ;
				$conteudoLog .=  $param->codigoIdentificador . "#" . $param->idTelefoneContato . "#" . $param->telefoneContato;
				
				$this->gravarLogAtendimento($this->dao->nomeCampanha, "_ws_entrada_", $conteudoLog);
			}
			
			
		} catch (ExceptionDAO $e) {
			
			$this->dao->transactionRollback();			
			throw new Exception('0190'); // Falha na execução do banco de dados.
			
		} catch (Exception $e) {
			
			$this->dao->transactionRollback();
			
			throw new Exception($e->getMessage()); // Mensagem de erro especializada
		}
		
	}
	
	/**
	 * Processa as informacoes para envio de SMS
	 * @param UraAtivaContratoVO Contrato
	 * @param obj informacoes panicos pendentes
	 */
	public function processarEnvioSMS(UraAtivaContratoVO $Contrato, $panicos_pendentes){
				
		# Data
		$data_acionamento = substr($panicos_pendentes->data_pacote, 0, 10); # Ex: [07/04/2013] 19:56:29
			
		# Hora
		$hora_acionamento = substr($panicos_pendentes->data_pacote, 11, 2); # Ex: 07/04/2013 [19]:56:29
			
		# Minuto
		$minuto_acionamento = substr($panicos_pendentes->data_pacote, 14, 2); # Ex: 07/04/2013 19:[56]:29
		
		# Mensagem
		$mensagem = "Sascar informa acionamento do botao de panico veiculo placa: {$panicos_pendentes->placa} as {$hora_acionamento}h{$minuto_acionamento}m de {$data_acionamento}. Retornar 40026004.";
			
		/*
		 * Busca os telefones de contatos de emergencia
		 */
		$telefones = $this->dao->buscarTelefonesCelularEmergencia($Contrato->connumero);
		 
		$motivo = null;
		 	
		if(count($telefones) > 0){
		
			$qtd_telefones_nao_enviados = 0;
			$telefones_nao_enviados = array();
		
			foreach($telefones as $telefone){
				
				if($_SESSION['servidor_teste'] == 1){
					$telefone['celular'] = '4198607906';
				}
								
				$retorno = $this->enviarSMS($telefone['celular'], $mensagem);
								
				if($retorno == 'OK'){
					
					$this->dao->inserirLogEnvioSMS($Contrato->connumero, $panicos_pendentes->clioid, $panicos_pendentes->veioid);
				
					break;
				}
						
				$qtd_telefones_nao_enviados++;
				array_push($telefones_nao_enviados, $telefone['celular']);
		
			}
		
			if($qtd_telefones_nao_enviados == count($telefones)){
			
				if(count($telefones_nao_enviados) > 0){
					$telefones_nao_contatados = implode(', ', $telefones_nao_enviados);
				}
		
				$motivo = "Falha ao enviar SMS para os telefones $telefones_nao_contatados.";
			}
		
		} else {
			
			$motivo = 'SMS não enviado por falta de telefone celular cadastrado.';
		}
		
		return $motivo;
		
	}
	
	/**
	 * Processa as informacoes para envio de email
	 * @param UraAtivaContratoVO Contrato
	 * @param obj informacoes panicos pendentes
	 */

	public function processarEnvioEmail(UraAtivaContratoVO $Contrato, $panicos_pendentes){

		$email_cliente 		= '';
		$motivo 			= null;
		$log_envio_email 	= true;
		$falha_enviar_email = false;
		$arrParamsTextoEmail = array();
		
		/**
		 * Envio de Email
		 */		
		$Cliente = $this->dao->getClientePorContrato($Contrato->connumero);
		
		//Usa ou cliemail ou cliemail_nfe
		if(isset($Cliente->email) &&  !empty($Cliente->email)){
			
			$email_cliente = $Cliente->email;			
		}
		else if(isset($Cliente->email_nfe) &&  !empty($Cliente->email_nfe)){
			
			$email_cliente = $Cliente->email_nfe;
		}    
		

		if (!empty($email_cliente)){
			
			$arrParamsTextoEmail = $this->buscarInformacoesEnvioEmail($Contrato, $panicos_pendentes,$Cliente);	

			/*
			 * Monta o texto do email
			* Recebe um array com parametros
			*/
			$texto_email = $this->montarTextoEmail($arrParamsTextoEmail);
			
			$enviou = $this->enviarEmail($email_cliente, 'Sascar informa', $texto_email);
				
			if(!$enviou){
			
				$falha_enviar_email = true;
			
				$motivo = "Falha ao enviar email para {$email_cliente}.";
			}
				
			$log_envio_email = true;
			
		}else{
			
			$log_envio_email = false;
				
			$motivo = 'E-mail não enviado por falta de E-mail cadastrado.';			
			
		}
		
		if($log_envio_email){
			
			$tipo_log = ($falha_enviar_email) ? 'I' : 'S';
			$obs_log  = ($falha_enviar_email) ? 'Insucesso de Envio' : 'Sucesso de Envio';
			
			$this->dao->inserirLogEnvioEmail($Contrato->connumero, $tipo_log, $obs_log);
			
		}
		
		return $motivo;
		
	}
	
	/**
	 * 
	 * @param Obj Contrato
	 * @param informações panicos_pendentes $row
	 * @return array
	 */
	public function buscarInformacoesEnvioEmail($Contrato, $panicos_pendentes, $Cliente){

		$arrRetorno = array();
		
		# Data
		$data_acionamento = substr($panicos_pendentes->data_pacote, 0, 10); # Ex: [07/04/2013] 19:56:29
				
		/*
		 * Busca o chassi do veiculo pelo veioid
		*/
		$Veiculo = $this->dao->getVeiculo($panicos_pendentes->veioid);
	
		/*
		 * Informacoes de data para envio de email
		*/
		$arrDataEnvioEmail = array(
				'dia' => date('d'),
				'mes' => Data::getTraducaoMes(date('m')),
				'ano' => date('Y')
		);
	
		/*
		 * Informacoes relacionadas ao acionamento
		*/
		$arrDataAcionamento = array(
				'data' => $data_acionamento,
				'hora_minuto_segundo' => substr($panicos_pendentes->data_pacote, 11, 8)
		);
	
		/*
		 * Informacoes veiculo
		*/
		$arrVeiculo = array(
				'placa' => str_replace('-', '', $panicos_pendentes->placa),
				'chassi' => $Veiculo->chassi
		);
	
		/*
		 * Informacoes cliente
		*/
		$arrCliente = array(
				'nome_cliente' => $Cliente->nome,
				'contrato' => $Contrato->connumero
		);
	
		/**
		 * Parametros para o texto do email
		*/
		$arrRetorno = array(
				'data' 			=> $arrDataEnvioEmail,
				'cliente'		=> $arrCliente,
				'acionamento' 	=> $arrDataAcionamento,
				'veiculo'		=> $arrVeiculo					
		);
						
		return $arrRetorno;
	}
		
	/**
	 * Metodo que monta o texto do email
	 */
	public function montarTextoEmail($arrParams){		
		
		ob_start();
		
		require _MODULEDIR_ . 'Atendimento/View/Email/PANICO_OPCAO_INVALIDA.php';
		
		$texto = ob_get_contents();
		
		ob_end_clean();
	
		return $texto;
		
	}	
	
	/**
	 * Inserir historico no contrato
	 */
	public function inserirHistoricoContrato($contrato, $data_hora, $placa, $ultima_localizacao, $motivo){
		
		$observacao  = "Atendimento automático para Pânico\n";
		$observacao .= "Data/Hora : " . $data_hora ."\n";
		$observacao .= "PLACA: " . $placa . "\n";
		$observacao .= "Ultima localização:" . $ultima_localizacao . "\n";
		$observacao .= "Motivo: " . $motivo . "\n";
			
		$this->dao->inserirHistoricoContrato($contrato, $observacao);
	}

	/**
	 * Busca informações adicional para URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	 */
	public function informacoesAdicionais(UraAtivaParamVO $param) {
		
		$oid 			= $param->codigoIdentificador;
		$arrPanico 		= array();
		$clinome		= '';
		$conteudoLog 	= '';

		try {		
			$row = $this->dao->buscarPanicoPendente($oid);	
		}catch (Exception $e) {
			throw new Exception('0190'); // Falha na execução do banco
		}
			
		$placa				 = isset($row->placa) 		? $row->placa 		: '';
		$contratante 		 = isset($row->contratante) ? $row->contratante : '';
		$horario_acionamento = isset($row->horario) 	? $row->horario 	: '';
		$connumero			 = isset($row->contrato) 	? $row->contrato 	: 0;
		$clioid				 = isset($row->clioid) 		? $row->clioid	 	: 0;
		
		if($placa != ''){
			
			$placa				 = $row->placa;
			$contratante 		 = $row->contratante;
			$horario_acionamento = $row->horario;

			try {
				$atualiza_placa = $this->dao->isAtualizaPlaca($placa);
			}catch (Exception $e) {
				throw new Exception('0190'); // Falha na execução do banco
			}
			
			if($atualiza_placa){
				$atualiza_placa = 'S';
			}
			else{
				$atualiza_placa = 'N';
			}
			
		}
		else{
			
			throw new Exception('0180');
		}			
		
		
		$retorno = new UraAtivaRetornoVO();
		$retorno->body = array(
				'placa' 					=> utf8_encode($placa),
				'contratante'			 	=> utf8_encode($contratante),
				'horario_acionamento'		=> utf8_encode($horario_acionamento),
				'atualiza_placa'			=> utf8_encode($atualiza_placa),
		);
		
		if($this->dao->isGravaLogAtendimento){	
			
			$clinome = $this->dao->getNomeCliente($clioid);	
				
			$conteudoLog = 	date("d/m/Y H:i") . " | "  . $connumero . " | " . $clioid . " | " . $clinome . " | "; 
			$conteudoLog .= $param->codigoIdentificador . "#" . $param->idTelefoneContato . "#" . $param->telefoneContato . " | ";
			$conteudoLog .= $placa . "#" . $contratante . "#" . $horario_acionamento ."#". $atualiza_placa;
					
			$this->gravarLogAtendimento($this->dao->nomeCampanha, "_ws_consulta_", $conteudoLog);
		}
		
		return $retorno;
			
	}
	
	/**
	 * Trata os insucessos do Pânico
	 * @param array $insucessos
	 */
	public function tratarInsucessos($insucessos) {
		
		$atmdescricao 	= self::MOTIVO_CONTATO_SEM_SUCESSO;
		$atmoid 		= $this->dao->buscarMotivoPorGrupo($atmdescricao,self::MOTIVO_GRUPO_DISCADOR);
		if (!$insucessos) {
			$this->logAtendimento = array(	
						'connumero' => '',
						'conclioid' => ''
				);
		}
		if (!$insucessos) {
			
			$this->logAtendimento = array('connumero' => '', 'conclioid' => '');
		}
		
		if($insucessos){
			
			$insucessos = (array)$insucessos;			
			
			$veioid	= (int) $insucessos['contatoExterno'];

			$contrato = $this->dao->buscarInformacoesContrato($veioid);
			
			$contrato->connumero = isset($contrato->connumero) ? $contrato->connumero : 0;
			$contrato->conclioid = isset($contrato->conclioid) ? $contrato->conclioid : 0;
			$contrato->conequoid = isset($contrato->conequoid) ? $contrato->conequoid : 0;
			$contrato->conveioid = isset($contrato->conveioid) ? $contrato->conveioid : $veioid;			
			
			if($contrato->connumero > 0){ 
				
				$this->logAtendimento = array(	'connumero' => $contrato->connumero,
												'conclioid' => $contrato->conclioid
											);
				
				
				$row = $this->dao->buscarAtendimentoPanico($veioid);
				
				$row->atcoid 		= isset($row->atcoid) 		? $row->atcoid 		:0;
				$row->papoid 		= isset($row->papoid) 		? $row->papoid 		:0;
				$row->data_pacote 	= isset($row->data_pacote) 	? $row->data_pacote :'';
				$row->placa 		= isset($row->placa) 		? $row->placa 		:'';
				$row->veioid 		= isset($row->veioid) 		? $row->veioid 		:$veioid;			
				$row->clioid 		= isset($row->clioid) 		? $row->clioid 		:0;
		
				if($row->atcoid > 0){
					
					$this->dao->concluirAtendimento($contrato, $row->atcoid, $atmoid, 0, false);

					$this->dao->excluirPanicoAuxiliar($row->atcoid);
					$this->dao->removerInsucessoContato($veioid);
					
					$obs = $this->montarHistoricoInsucessos($row, $insucessos, $atmdescricao);
	
					$this->dao->inserirHistoricoContrato($contrato->connumero, $obs);
						
					$this->dao->excluirPanico($row->papoid, $atmoid);
					
					//Envio de SMS
					$motivo = null;
					$motivo = $this->processarEnvioSMS($contrato, $row);
					
					if(!empty($motivo)){
							
						$obs = $this->montarHistoricoInsucessos($row, $insucessos, $motivo);
							
						$this->dao->inserirHistoricoContrato($contrato->connumero, $obs);
							
					}
					
					//Envio de Email
					$motivo = null;
					$motivo = $this->processarEnvioEmail($contrato, $row);
					
					if(!empty($motivo)){
							
						$obs = $this->montarHistoricoInsucessos($row, $insucessos, $motivo);
						$this->dao->inserirHistoricoContrato($contrato->connumero, $obs);
					
					}		
										
					unset($row);
					unset($contrato);
				} else {
					
					$this->dao->excluirPanicoAuxiliarByVeioid($veioid);
					$this->dao->removerInsucessoContato($veioid);
					
					if ($row->papoid) {
						$this->dao->excluirPanico($row->papoid, $atmoid);
					}
				}
					
			}else{
				
				$this->logAtendimento = array(	
						'connumero' => '',
						'conclioid' => ''
				);
					
				unset($contrato);				
			}		
		}//FIM: if($insucessos)		
	}//FIM: tratarInsucessos
	
	/**
	 * Realiza o tratamento dos atendimentos de Pânico pendentes a mais de uma hora
	 */
	public function tratarAtendimentosPendentes(){
		
		$atmoid_verificar 	= $this->dao->buscarMotivoPorGrupo(self::MOTIVO_PRECISA_VERIFICAR_SQL,self::MOTIVO_GRUPO_DISCADOR);
		$atmoid_acionamento = $this->dao->buscarMotivoPorGrupo(self::MOTIVO_ACIONAMENTO_INDEVIDO,self::MOTIVO_GRUPO_DISCADOR);
		
		$atmoid_verificar_semi 	= $this->dao->buscarMotivoPorGrupo(self::MOTIVO_PRECISA_VERIFICAR_SEMI,self::MOTIVO_GRUPO_DISCADOR);
		
		$rows = $this->dao->buscarAtendimentoPendente($atmoid_verificar); // Busca atendimento modo automatico
		
		$rows_semi = $this->dao->buscarAtendimentoPendente($atmoid_verificar_semi); // Busca atendimento modo semiautomatico
		
		foreach ($rows_semi as $atendimento) {
			$rows[] = $atendimento;
		}
	
		// Processa atendimento automatico
		foreach($rows as $k => $v){

			$veioid 	= $rows[$k]['veioid'];
			$atcoid 	= $rows[$k]['atcoid'];
			$papoid		= $rows[$k]['papoid'];
			$clinome	= '';
			
			$contrato = $this->dao->buscarInformacoesContrato($veioid);
			
			$contrato->connumero = isset($contrato->connumero) ? $contrato->connumero : 0;
			$contrato->conclioid = isset($contrato->conclioid) ? $contrato->conclioid : 0;
			$contrato->conequoid = isset($contrato->conequoid) ? $contrato->conequoid : 0;
			$contrato->conveioid = isset($contrato->conveioid) ? $contrato->conveioid : $veioid;
				
			if($contrato->connumero > 0){
		
				$this->dao->concluirAtendimento($contrato, $atcoid, $atmoid_acionamento, 1, true);
		
				$this->dao->excluirPanico($papoid, $atmoid_verificar);
				
				if($this->dao->isGravaLogAtendimento){				
					$clinome = $this->dao->getNomeCliente($contrato->conclioid);
					$this->logAtendimento['precisa_verificar_e_retorna'][]	= $contrato->connumero . " | " . $contrato->conclioid . " | " . $clinome;
				}				
		
			}			
			
			unset($contrato);
			unset($clinome);
			unset($veioid);
			unset($atcoid);
			unset($papoid);		
		}
			
	}
	
	/**
	 * Monta a observação para gravar histórico do Insucesso
	 * @param array $panico
	 * @param array $insucesso
	 * @param string $motivo
	 * @return string $obs
	 */
	public function montarHistoricoInsucessos($panico, $insucesso, $motivo){
		
		$data_inicial 	= isset($insucesso['chamada']) 		? 	trim($insucesso['chamada']) : '';
		$telefones 		= isset($insucesso['telefones']) 	? 	trim($insucesso['telefones']) : '';
		
		$data_inicial = $this->formatarDataContatoDB2($data_inicial);

		//Removendo a ultima virgula da string de telefones
		$tamanho = strlen($telefones) -1;		
		if(strrpos($telefones, ',') == $tamanho){
			$telefones = substr_replace($telefones, '', $tamanho , 1);
		}
		
		$obs =	"Atendimento automático para Pânico\n";
		$obs .=  "Data/Hora Acionamento: " . $panico->data_pacote ."\n";
		$obs .= "Data/Hora Contato: "  .$data_inicial . "\n";
		$obs .= "PLACA: " . $panico->placa . "\n";
		$obs .= "Motivo: " . $motivo . "\n";
		$obs .= "Telefones de contato: ". $telefones;

		return $obs;
	
	}
	
	/**
	 *
	 */
	public function enviarSMS($telefone, $mensagem){
	
		try {			
			// Trata o conteúdo da mensagem para o webservice aceitar a mensagem
			$mensagem = nl2br($mensagem);
			$mensagem = strip_tags($mensagem);
			$mensagem = utf8_decode($mensagem);
			//Limpa o cache
			ini_set("soap.wsdl_cache_enabled", "0");
			$client = new SoapClient('https://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx?WSDL',
					array(  'trace'         => 1,
							'exceptions'    => 1,
							'soap_version'  => SOAP_1_1));
			$save_result = $client->EnviaSMS(array('NumUsu'=>'sascar2','Senha'=>'car666','SeuNum'=>'511023','Celular'=>'55'.$telefone,'Mensagem'=>$mensagem));
			$xmlres = $save_result->EnviaSMSResult;
			return $xmlres;
		} catch (SoapFault $e){
			return $e->getMessage();
		}
	
	}
	
}