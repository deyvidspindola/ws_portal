<?php

require _MODULEDIR_ . 'Principal/DAO/PrnContratoServicosDAO.php';

/**
 * Camada Action do Contrato Serviços
 * @author rafael.dias
 *
 */
class PrnContratoServicos 
{
	private $dao;
	private $conn;
	
	/**
	 * Método construtor
	 */
	public function PrnContratoServicos() {
		
		global $conn;		
		$this->conn = $conn;
		$this->dao = new PrnContratoServicosDAO($conn);		
		
	}
	
	public function index() {
		return true;
	}
	
	/**
	 * Carrega Ordem serviço para exclusão de equipamento do contrato
	 * STI 
	 */
	public function carregaOSExclusao($connumero)
	{
		$dadosOS = $this->dao->carregaDadosOS($connumero);
		
		$ordoid = ($dadosOS == 0) ? 0 : pg_fetch_result($dadosOS, 0, "ordoid");
		
		return $ordoid;		
	}
	
	/**
	 * Excluir Equipamento Retirada e Assistência
	 * STI 80898
	 */
	public function excluirEquipamento($DADOS, $dadosContrato){
		
		$retorno['FecharOS'] = false;
		$retorno['AtualizaContrato']    = false;
		$retorno['AlteraVigenciaCon']   = false;
		$retorno['StatusEquipamento']   = 0; // Conforme Status do equipamento ele atualiza o estoque do representante
		$retorno['MotivoAssistencia']   = false;
		$retorno['EquipamentoNovo']	    = '';
		$retorno['MotivoReinstalacao']  = 'NULL';
		$retorno['SairDoProcesso']		= false;

		$DC = $dadosContrato;
		
		// Trata datas
		$dataAtual	  = mktime();
		$dtVigencia   = explode('-', $DC['DtVigencia']);
		$dataVigencia = 0;

		if(count($dtVigencia) > 1){
			$dataVigencia = mktime(0,0,0,$dtVigencia[1],$dtVigencia[2],$dtVigencia[0]);
		}
		
		// Se Modalidade Revenda
		if($DC['Modalidade'] == 'V'){

			$dadosOS = $this->dao->carregaDadosOS($DADOS['connumero']);

			if($dadosOS != 0){

				$ordoid	   = pg_fetch_result($dadosOS, 0, "ordoid");
				$ordstatus = pg_fetch_result($dadosOS, 0, "ordstatus");
				$otiostoid = pg_fetch_result($dadosOS, 0, "otiostoid");
				$aceitaCob = pg_fetch_result($dadosOS, 0, "ordaceita_cobranca");

				// RETIRADA e ASSITÊNCIA
				if (($otiostoid == 3) || ($otiostoid == 4)){
				
					// Equipamento Funcionando
					if($DADOS['testeFuncional'] == 'S'){

						$retorno['FecharOS'] = true;
						$retorno['AtualizaContrato']  = true;
						$retorno['StatusEquipamento'] = 13;
						
						// OS DE RETIRADA = 3 e ASSISTENCIA = 4
						if ($otiostoid == 3) {
							//Troca de Veículo - Não Fatura Cliente
							$retorno['MotivoReinstalacao'] = 893 ;
							// Mensagem para o Atendente/Técnico
							$retorno['EquipamentoNovo'] = 'N';
						} else {
							//Fecha a OS com o motivo que foi aberta
							$retorno['MotivoAssistencia'] = false;
							$retorno['MotivoReinstalacao'] = 'NULL';
							// Mensagem para o Atendente/Técnico
							$retorno['EquipamentoNovo'] = '';
						}
						
						// É mau uso Checklist INSPEÇÃO VISUAL ou LACRE DE SEGURANÇA
						if(($DADOS['inspecaoVisual'] == 'N') || ($DADOS['lacreSeguranca'] == 'S')){
							$retorno['AlteraVigenciaCon'] = true;
						}
						
					 // Equipamento não funciona
					}else{

						// Não é mau uso Checklist INSPEÇÃO VISUAL ou LACRE DE SEGURANÇA
						if(($DADOS['inspecaoVisual'] == 'S') && ($DADOS['lacreSeguranca'] == 'N')){
							
							// É Garantia, Sistema verifica se o contrato/equipamento está em período de garantia
							if($dataAtual < $dataVigencia){
								
								$retorno['FecharOS'] = true;							
								// Salvar o equipamento como RETIRADO
								$retorno['StatusEquipamento'] = 10;
								$retorno['AtualizaContrato']  = true;

								// OS DE RETIRADA = 3 e ASSISTENCIA = 4
								if ($otiostoid == 3) {
									//Troca de Veículo - Não Fatura Cliente
									$retorno['MotivoReinstalacao'] = 893 ;
									// Mensagem para o Atendente/Técnico
									$retorno['EquipamentoNovo'] = 'G';
								} else {
									//Troca de Equipamento - Não Fatura Cliente
									$retorno['MotivoAssistencia'] = 891;
									$retorno['MotivoReinstalacao'] = 'NULL';
									// Mensagem para o Atendente/Técnico
									$retorno['EquipamentoNovo'] = 'G'; //validar se precisa estar como G
								}
								
							 // Não é Garantia
							}else{
								
								if($aceitaCob == 't'){
								
									$retorno['FecharOS'] = true;								
									$retorno['StatusEquipamento'] = 13;
									$retorno['AtualizaContrato']  = true;

									// OS DE RETIRADA = 3 e ASSISTENCIA = 4
									if ($otiostoid == 3) {
										//Troca de Veículo - Faturar Cliente
										$retorno['MotivoReinstalacao'] = 894 ;
										// Mensagem para o Atendente/Técnico
										$retorno['EquipamentoNovo'] = 'S';
									} else {
										//Troca de Equipamento - Faturar Cliente
										$retorno['MotivoAssistencia'] = 892;
										$retorno['MotivoReinstalacao'] = 'NULL';
										// Mensagem para o Atendente/Técnico
										$retorno['EquipamentoNovo'] = 'S'; //validar se precisa estar como S
									}

								}else{
									// Motivo - DEVOLUÇÃO - CLIENTE RECUSA COMPRA
									$retorno['MotivoAssistencia'] = ($otiostoid == 3) ? 896 : 895;
									
									// Fechar O.S. com o motivo acima
									$retorno['FecharOS'] = true;
									
									// Sair do processo de exclusão do equipamento
									$retorno['SairDoProcesso'] = true;
								}
							}
						
						 // É Mau Uso
						}else{

							if($aceitaCob == 't'){

								$retorno['FecharOS'] = true;
								$retorno['StatusEquipamento'] = 13;							
								$retorno['AtualizaContrato']  = true;

								// OS DE RETIRADA = 3 e ASSISTENCIA = 4
								if ($otiostoid == 3) {
									//Troca de Veículo - Faturar Cliente
									$retorno['MotivoReinstalacao'] = 894 ;
									// Mensagem para o Atendente/Técnico
									$retorno['EquipamentoNovo'] = 'S';
								} else {
									//Troca de Equipamento - Faturar Cliente
									$retorno['MotivoAssistencia'] = 892;
									$retorno['MotivoReinstalacao'] = 'NULL';
									// Mensagem para o Atendente/Técnico
									$retorno['EquipamentoNovo'] = 'S'; //validar se precisa estar com S
								}

							}else{
								// Motivo - DEVOLUÇÃO - CLIENTE RECUSA COMPRA
								$retorno['MotivoAssistencia'] = ($otiostoid == 3) ? 896 : 895;
								
								// Fechar O.S. com o motivo acima
								$retorno['FecharOS'] = true;
								
								// Sair do processo de exclusão do equipamento
								$retorno['SairDoProcesso'] = true;
							}

						}
					}
				}
			}
		}

		return $retorno;
	}

	
	public function fecharOsEquip($excluirEquip, $dadosContrato){
		
		// Alimenta Variáveis
		$OSExcluir	  = $dadosContrato['OSExcluir'];
		$idInstalador = $_POST['itloid'];
		$cliente	  = $_POST['clioid'];
		$dadosCD['connumero'] = $_POST['connumero'];
		
		// Carrega dados da OS
		$servicosOS = $this->dao->carregaServicosOS($OSExcluir);
		
		while($lista = pg_fetch_array($servicosOS)){

			$dadosCD['ositoid']	= $lista['ositoid'];
			$dadosCD['ositordoid'] = $lista['ositordoid'];
			$dadosCD['ositotioid'] = $lista['ositotioid'];
			$dadosCD['ositstatus'] = $lista['ositstatus'];
			$dadosCD['pontoFora']  = $lista['ositatend_ponto_fora'];
			$dadosCD['dtVigencia'] = $lista['condt_ini_vigencia'];
			$dadosCD['conveioid']  = $lista['conveioid'];
			$dadosCD['conveioid']  = $lista['conveioid'];
			$dadosCD['ositosdfoid_alegado']	  = $lista['ositosdfoid_alegado'];
			$dadosCD['ositosdfoid_analisado'] = $lista['ositosdfoid_analisado'];

			// Atualizar a OS com o representante informado no contrato
			$this->dao->atualizaOrdemServico($idInstalador, $OSExcluir);

			// Alterar o contrato adicionando o veiculo e salvar o historico
			$this->dao->contratoHistorico($dadosCD['conveioid'], $dadosCD['connumero'], $dadosContrato['UsuarioID']);
			
			// Verificar se o servico possui defeito alegado, e se o defeito analisado está em branco
			$this->dao->confereDefeito($dadosCD);

			IF(($dadosCD['ositosdfoid_alegado'] != "") && ($dadosCD['ositosdfoid_analisado'] == "")){
				throw new exception ("Preencha o campo: \"Motivo analisado\" dos serviços de assistência.");
			}

			if($dadosCD['ositotioid'] == 245){
				throw new exception ("Favor fechar o serviço 'TRAVA DE BAÚ' na tela de Ordem de Serviço.");
			}

			// Atualiza o Status do servico da OS
			$statusOSI = $this->dao->atualizaStatusServico($dadosCD['ositoid'], $excluirEquip['MotivoAssistencia']);

			if(!$statusOSI){
				throw new exception ("O serviço não pode ser alterado.");
			}

			// Função para pagar comissão
			$resultComissao = $this->pagarComissao($dadosCD['ositoid'], $OSExcluir, $cliente, $dadosContrato['UsuarioID']);
			
			if($resultComissao){
				throw new exception ($resultComissao);
			}
			// Atualizar garantia do contrato
			if($excluirEquip['AlteraVigenciaCon'] == FALSE){
				$this->calculaNovaGarantia($dadosContrato, $dadosCD);
			}

			// Adequações na ordem de serviço para atender ao portal de atendimento
			$this->dao->adequacoesPortal($OSExcluir, $dadosCD['connumero']);
		}
		
		// Fechar Ordem Serviço
		if($excluirEquip['FecharOS'] == true){
			$this->dao->fecharOrdemServico($OSExcluir);
		}
	}

	
	function calculaNovaGarantia($dadosContrato, $dadosCD)
	{
		$dadosOS = $this->dao->carregaDadosOS($dadosCD['connumero']);
		
		if($dadosContrato['conmodalidade'] == 'V'){
		
			$descricao_motivo = $dadosOS['otidescricao'];
			
			$calcula_nova_garantia = 
				(stristr($descricao_motivo, 'troca') && stristr($descricao_motivo, 'equipamento') && stristr($descricao_motivo, 'faturar'))
				||
				(stristr($descricao_motivo, 'troca') && stristr($descricao_motivo, 'veiculo') && stristr($descricao_motivo, 'faturar') && stristr($descricao_motivo, 'equipamento'));

			if($calcula_nova_garantia){
				
				if(!empty($dadosCD['dtVigencia'])) {
					
					$mes_e_ano_vigencia = date('m/Y', strtotime($dadosCD['dtVigencia']));
					$mes_e_ano_atual = date('m/Y');

					list($mes_vigencia, $ano_vigencia) = explode('/', $mes_e_ano_vigencia);
					list($mes_atual, $ano_atual) = explode('/', $mes_e_ano_atual);

					$diff_meses = (($ano_atual - $ano_vigencia)*12) + (($mes_atual - $mes_vigencia)+1);
					
					$nova_garantia = 12 + $diff_meses;
					
					$this->dao->atualizaGarantia($nova_garantia, $dadosCD['connumero']);
				}

			}
		}
	}


	function pagarComissao($item, $ordem, $cliente, $cd_usuario)
	{
		$parametros = '"'.$item.'" "'.$ordem.'" "'.$cliente.'" "'.$cd_usuario.'" "SERVIÇO CONCLUÍDO"';

		// Gerar comissao do servico
		$comissao = $this->dao->comissaoTecnica($parametros);

		$count = pg_num_rows($comissao);
		
		if($count > 0){
			$result = pg_fetch_result($comissao, 0, 'retorno');

			switch($result){
				case 0 :
					$retorno = false;
					break;
				case 1:
					$retorno = false;
					break;
				case 998:
					$retorno = "Não foi gerada a comissão. Verificar o cadastro de valores de comisisonamento deste serviço!";
					break;
				case 999:
					$retorno = "Não foi gerada a comissão. Já existe comissão gerada para o mesmo serviço.";
					break;
			}
		}

		return $retorno;
	}

	// Envia e-mail conforme alteração no status do cliente
	function emailAlteracaoStatus($connumero, $cd_usuario, $concsioid){

		// Caso status 'Pré-Recisão' envia e-mail de notificação de retenção
		if($concsioid == 12)
		{
			require("modulos/Principal/Action/ServicoEnvioEmail.php");
		
			$ServicoEmail = new ServicoEnvioEmail();
			$SendLayoutEmails = new SendLayoutEmails();

			// Busca dados do contrato
			$getDadosContrato = $this->dao->getDadosContrato($connumero);
		
			// Mensagens para registro de histórico
			$msgHist = "Envio de notificação de entrada de retenção realizado com sucesso.";
			$msgErro = "Falha no envio de notificação de entrada de retenção.";

			// Busca layout de e-mail
			$titulo = $this->dao->getTituloParamSiggo('TITULO_LAYOUT_ENTRADA_RETENCAO');

			// Consulta se e-mail já foi enviado
			$getEmailRetencao = $this->dao->getNotificacaoEnviada($connumero, $msgHist);

			if($getEmailRetencao > 0){
				throw new exception('Aviso: O e-mail para notificação de entrada de retenção já foi enviado.');
			}

			// Guarda dados do layout de e-mail
			$dadosLayout = $SendLayoutEmails->getTituloFuncionalidade($titulo);

			$dadosEmail['seeseefoid'] = $dadosLayout[0]['funcionalidade_id'];
			$dadosEmail['seeseetoid'] = $dadosLayout[0]['titulo_id'];

			$codigoLayout = $SendLayoutEmails->buscaLayoutEmail($dadosEmail);
			$layout = $SendLayoutEmails->getLayoutEmailPorId($codigoLayout['seeoid']);
		
			$servidor = $layout['seesrvoid'];
			$corpo_envio = $layout['seecorpo'];

			// Envia e-mail
			$envio = $ServicoEmail->enviarEmail($getDadosContrato['cliemail'],
												$layout['seecabecalho'],
												$corpo_envio,
												'',
												'',
												'',
												$servidor,
												'teste_desenv@sascar.com.br');

			if($envio['erro']){
				$this->dao->setHistoricoEmailRetencao($connumero, $cd_usuario, $msgErro);
				throw new exception('Erro: '.$msgErro);
			}

			// Registra histórico
			$this->dao->setHistoricoEmailRetencao($connumero, $cd_usuario, $msgHist);
		}
	}

}