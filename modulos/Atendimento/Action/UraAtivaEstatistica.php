<?php

require_once _MODULEDIR_ . 'Atendimento/Action/UraAtiva.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaParamVO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaRetornoVO.php';
require_once _MODULEDIR_ . 'Atendimento/DAO/UraAtivaEstatisticaDAO.php';
require_once _SITEDIR_ . 'lib/Components/Data.php';


class UraAtivaEstatistica extends UraAtiva {

	const ESTATISTICA_MANUTENCAO				= 'ESTATISTICA_MANUTENCAO';
	const ESTATISTICA_VENDIDO					= 'ESTATISTICA_VENDIDO';
	const ESTATISTICA_PERDA_TOTAL				= 'ESTATISTICA_PERDA_TOTAL';
	const ESTATISTICA_SEGURO_CANCELADO			= 'ESTATISTICA_SEGURO_CANCELADO';
	const ESTATISTICA_TRAFEGANDO_ATENDIMENTO	= 'ESTATISTICA_TRAFEGANDO_ATENDIMENTO';
	const ESTATISTICA_OPCAO_INVALIDA			= 'ESTATISTICA_OPCAO_INVALIDA';
	const ESTATISTICA_NAO_RESPONDEU				= 'ESTATISTICA_NAO_RESPONDEU';
	const ESTATISTICA_PT_DESLIGA				= 'ESTATISTICA_PT_DESLIGA';
	const MOTIVO_GRUPO_ESTATISTICA				= 'Estatística';
	const MOTIVO_ESTATISTICA_URA_ATIVA			= 'Estatística URA Ativa';
	const MOTIVO_VEICULO_PARADO					= 'Veículo Parado / Manutenção';
	const MOTIVO_VEICULO_VENDIDO				= 'Veículo Vendido';
	const MOTIVO_SINISTRO						= 'Sinistro com PT';
	const MOTIVO_SEGURO_CANCELADO				= 'Seguro Cancelado';
	const STATUS_ANDAMENTO						= 'A';
	const STATUS_PENDENTE						= 'P';
	const STATUS_CONCLUIDO						= 'C';


	/**
	 * Torna público um array com as informações para gravar o log de atendimento;
	 */
	public $logAtendimento = array();

	public function __construct($conn) {

		try {

			$this->dao = new UraAtivaEstatisticaDAO($conn);

		} catch (Exception $e) {

			throw new Exception('0190'); // Banco de dados indisponível.
		}
	}

	/**
	 * Trata a reposta da URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	 */
	public function navegacao(UraAtivaParamVO $param) {

		$vegoid		 	= isset($param->codigoIdentificador) 	? $param->codigoIdentificador 	: 0 ;
		$idContato 		= isset($param->idTelefoneContato)		? $param->idTelefoneContato		: 0 ;
		$foneContato	= isset($param->telefoneContato)		? $param->telefoneContato		: '';
		$opcao			= isset($param->opcaoSelecionada)		? $param->opcaoSelecionada		: '';
		$dtAgendamento	= isset($param->data)					? $param->data					: '';

		$falha_enviar_email = false;
		$retornoDB2 = array();
		$destinatario = 'atenseg@sascar.com.br';

		$this->dao->transactionBegin();

		try{

			//verifica se o veículo existe
			$veioid = $this->dao->getVeiculoEstatisticaGsm($vegoid);

			if(!$veioid){
				throw new Exception('0221'); //Nenhuma informação encontrada referente ao veículo informado
			}

			//Recupera a data do chamado do DB2, antes da exclusão.
			$retornoDB2 = $this->dao->buscarInsucessos($idContato, $vegoid);
			$dataContatoDB2 = isset($retornoDB2['chamada']) ? trim($retornoDB2['chamada']) : '';

			//Informações do contrato
			$contrato 	= $this->dao->buscarInformacoesContrato($veioid);
			$connumero	= isset($contrato->connumero) ? $contrato->connumero : 0 ;
			$clioid	 	= isset($contrato->conclioid) ? $contrato->conclioid : 0 ;
			$equoid	 	= isset($contrato->conequoid) ? $contrato->conequoid : 0 ;

			$grupo = $this->dao->tratarDescricaoPesquisa(self::MOTIVO_GRUPO_ESTATISTICA);

			//verifica a opção selecionada
			switch ($opcao){
				//Se o veículo esta trafegando ou para falar com atendimento pessoal disque 5
				case self::ESTATISTICA_TRAFEGANDO_ATENDIMENTO:

					//Busca oid do motivo
					$motivo 	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_ESTATISTICA_URA_ATIVA);
					$atmoid 	= $this->dao->buscarMotivoPorGrupo($motivo, $grupo);
					$atcoid 	= $this->dao->abrirAtendimento($clioid,0, $atmoid);

                    //remove das tabelas auxiliares
                    $this->dao->excluirEstatisticaAuxiliar($vegoid);
                    $this->dao->removerInsucessoContato($vegoid);

                    break;

				//Veículo parado ou manutenção disque 1
				case self::ESTATISTICA_MANUTENCAO :

					//Busca oid do motivo
					$motivo 	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_VEICULO_PARADO);
					$atmoid 	= $this->dao->buscarMotivoPorGrupo($motivo, $grupo);

					//Abre atendimento, Acesso  e conclui atendimento
					$atcoid = $this->dao->abrirAtendimento($clioid,0, $atmoid);
					$this->dao->inserirAcesso($contrato, $atcoid, $atmoid, 1);
					$this->dao->concluirAtendimento($contrato, $atcoid, $atmoid, 1, false);

					//Grava histórico contrato
					$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_VEICULO_PARADO);
					$this->dao->inserirHistoricoContrato($connumero, $obs);

					//Atualiza Ação do Contrato
					$descricao 	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_VEICULO_PARADO);
					$egaoid = $this->dao->buscarAcaoEstatisticaGsm($motivo);
					$this->dao->atualizarAcaoContrato($connumero, $egaoid);

					//Atualiza Status do Estatistica e data manutenção
					$this->dao->atualizarEstatisticaGsm(self::STATUS_ANDAMENTO, $vegoid, $dtAgendamento);

                    //remove das tabelas auxiliares
                    $this->dao->excluirEstatisticaAuxiliar($vegoid);
                    $this->dao->removerInsucessoContato($vegoid);

					break;

				//Veículo Vendido disque 2
				case self::ESTATISTICA_VENDIDO :

					//Busca oid do motivo
					$motivo 	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_VEICULO_VENDIDO);
					$atmoid = $this->dao->buscarMotivoPorGrupo($motivo, $grupo);

					//Abre atendimento, Acesso  e conclui atendimento
					$atcoid = $this->dao->abrirAtendimento($clioid,0, $atmoid);
					$this->dao->inserirAcesso($contrato, $atcoid, $atmoid, 1);
					$this->dao->concluirAtendimento($contrato, $atcoid, $atmoid, 1, false);

					//Atualiza a Ação do contrato
					$motivo	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_VEICULO_VENDIDO);
					$egaoid = $this->dao->buscarAcaoEstatisticaGsm($motivo);
					$this->dao->atualizarAcaoContrato($connumero, $egaoid);

					//Atualiza Status do Estatistica
					$this->dao->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $vegoid);

					//Verifica se contrato é do tipo seguradora
					$retorno = $this->dao->isContratoSeguradora($connumero);

					if($retorno){//Tipo seguradora

						//Envio do Email e gravação de log
						$texto = $this->montarCorpoEmailNavegacao(self::MOTIVO_VEICULO_VENDIDO, $clioid, $veioid, $connumero);
						$assunto = 'URA Ativa Estatística ' . self::MOTIVO_VEICULO_VENDIDO;
						$enviou = $this->enviarEmail($destinatario, $assunto, $texto);

						$falha_enviar_email = (boolean)$enviou;

						$tipo_log = (!$falha_enviar_email) ? 'I' : 'S';
						$obs_log  = (!$falha_enviar_email) ? 'Insucesso de Envio' : 'Sucesso de Envio';

						$this->dao->inserirLogEnvioEmail($connumero, $tipo_log, $obs_log, 2);

						//Grava histórico contrato
						if(!$falha_enviar_email){
							$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_VEICULO_VENDIDO);
						}else{
							$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_VEICULO_VENDIDO, $texto);
						}

						$this->dao->inserirHistoricoContrato($connumero, $obs);


					}else{ //Não é do Tipo seguradora

						//Grava histórico contrato
						$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_VEICULO_VENDIDO);
						$this->dao->inserirHistoricoContrato($connumero, $obs);
					}

                    //remove das tabelas auxiliares
                    $this->dao->excluirEstatisticaAuxiliar($vegoid);
                    $this->dao->removerInsucessoContato($vegoid);

					break;

				//Sinistro com perda total disque 3
				case self::ESTATISTICA_PERDA_TOTAL :

					//Busca oid do motivo
					$motivo 	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_SINISTRO);
					$atmoid = $this->dao->buscarMotivoPorGrupo($motivo, $grupo);

					//Verifica se contrato é do tipo seguradora
					$retorno = $this->dao->isContratoSeguradora($connumero);

					if($retorno){//Contrato do tipo seguradora

						//Envio do Email e gravaçãod e log
						$texto = $this->montarCorpoEmailNavegacao(self::MOTIVO_SINISTRO, $clioid, $veioid, $connumero);
						$assunto = 'URA Ativa Estatística ' . self::MOTIVO_SINISTRO;
						$enviou = $this->enviarEmail($destinatario, $assunto, $texto);

						$falha_enviar_email = (boolean)$enviou;

						$tipo_log = (!$falha_enviar_email) ? 'I' : 'S';
						$obs_log  = (!$falha_enviar_email) ? 'Insucesso de Envio' : 'Sucesso de Envio';

						$this->dao->inserirLogEnvioEmail($connumero, $tipo_log, $obs_log, 2);

						//Abre atendimento, Acesso  e conclui atendimento
						$atcoid = $this->dao->abrirAtendimento($clioid,0, $atmoid);
						$this->dao->inserirAcesso($contrato, $atcoid, $atmoid, 1);
						$this->dao->concluirAtendimento($contrato, $atcoid, $atmoid, 1, false);

						//Grava histórico contrato
						if(!$falha_enviar_email){
							$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_SINISTRO);
						}else{
							$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_SINISTRO, $texto);
						}
						$this->dao->inserirHistoricoContrato($connumero, $obs);

						//Atualiza a Ação do contrato
						$motivo	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_SINISTRO);
						$egaoid = $this->dao->buscarAcaoEstatisticaGsm($motivo);
						$this->dao->atualizarAcaoContrato($connumero, $egaoid);

						//Atualiza Satatus do Estatística
						$this->dao->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $vegoid);

					}

                    //remove das tabelas auxiliares
                    $this->dao->excluirEstatisticaAuxiliar($vegoid);
                    $this->dao->removerInsucessoContato($vegoid);

					break;

                //Cliente disca 3 e desliga o telefone
				case self::ESTATISTICA_PT_DESLIGA :

					//Verifica se contrato é do tipo seguradora
					$retorno = $this->dao->isContratoSeguradora($connumero);

					if(!$retorno){//Contrato NÃO é do tipo seguradora

						//Busca oid do motivo
						$motivo = $this->dao->tratarDescricaoPesquisa(self::MOTIVO_SINISTRO);
						$atmoid = $this->dao->buscarMotivoPorGrupo($motivo, $grupo);

						//Abre atendimento, Acesso  e conclui atendimento
						$atcoid = $this->dao->abrirAtendimento($clioid,0, $atmoid);
						$this->dao->inserirAcesso($contrato, $atcoid, $atmoid, 1);
						$this->dao->concluirAtendimento($contrato, $atcoid, $atmoid, 1, false);

						//Grava histórico contrato
						$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_SINISTRO);
						$this->dao->inserirHistoricoContrato($connumero, $obs);

						//Atualiza a Ação do contrato
						$motivo	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_SINISTRO);
						$egaoid = $this->dao->buscarAcaoEstatisticaGsm($motivo);
						$this->dao->atualizarAcaoContrato($connumero, $egaoid);
					}

                    //remove das tabelas auxiliares
                    $this->dao->excluirEstatisticaAuxiliar($vegoid);
                    $this->dao->removerInsucessoContato($vegoid);

					break;

				//Seguro Cancelado disque 4
				case self::ESTATISTICA_SEGURO_CANCELADO :

					//Verifica se contrato é do tipo seguradora
					$retorno = $this->dao->isContratoSeguradora($connumero);

					if($retorno){

						//Busca oid do motivo
						$motivo = $this->dao->tratarDescricaoPesquisa(self::MOTIVO_SEGURO_CANCELADO);
						$atmoid = $this->dao->buscarMotivoPorGrupo($motivo, $grupo);

						//Abre atendimento, Acesso  e conclui atendimento
						$atcoid = $this->dao->abrirAtendimento($clioid,0, $atmoid);
						$this->dao->inserirAcesso($contrato, $atcoid, $atmoid, 1);
						$this->dao->concluirAtendimento($contrato, $atcoid, $atmoid, 1, false);

						//Atualiza a Ação do contrato
						$motivo	= $this->dao->tratarDescricaoPesquisa(self::MOTIVO_SEGURO_CANCELADO);
						$egaoid = $this->dao->buscarAcaoEstatisticaGsm($motivo);
						$this->dao->atualizarAcaoContrato($connumero, $egaoid);

						//Atualiza Status de Estatística
						$this->dao->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $vegoid);

						//Envio do Email e gravação de log
						$texto = $this->montarCorpoEmailNavegacao(self::MOTIVO_SEGURO_CANCELADO, $clioid, $veioid, $connumero);
						$assunto = 'URA Ativa Estatística ' . self::MOTIVO_SEGURO_CANCELADO;
						$enviou = $this->enviarEmail($destinatario, $assunto, $texto);

						$falha_enviar_email = (boolean)$enviou;

						$tipo_log = (!$falha_enviar_email) ? 'I' : 'S';
						$obs_log  = (!$falha_enviar_email) ? 'Insucesso de Envio' : 'Sucesso de Envio';

						$this->dao->inserirLogEnvioEmail($connumero, $tipo_log, $obs_log, 2);

						//Grava histórico contrato
						if(!$falha_enviar_email){
							$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_SEGURO_CANCELADO);
						}else{
							$obs = $this->montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, self::MOTIVO_SEGURO_CANCELADO, $texto);
						}
						$this->dao->inserirHistoricoContrato($connumero, $obs);

					}

                    //remove das tabelas auxiliares
                    $this->dao->excluirEstatisticaAuxiliar($vegoid);
                    $this->dao->removerInsucessoContato($vegoid);

					break;

				//Cliente não digitou nenhuma opção
				case self::ESTATISTICA_NAO_RESPONDEU :

                    //Incrementa tentativa no telefone em questão
					$this->dao->atualizarTentativaInsucessoContato($vegoid, $foneContato);

					/*
                     * Verifica e incrementar tentativa nos demais contatos caso tenham sido insucessos no discador
                     */
					$this->dao->atualizarTentativaInsucessoCliente($vegoid);


					break;
				//Cliente digitou uma opção inválida
				case self::ESTATISTICA_OPCAO_INVALIDA :

					//O WS não faz nada

					break;
				//Opção não identificada
				default:

					throw new Exception('0210'); //Opção de menu da URA não encontrada

					break;
			}

            $this->dao->transactionCommit();

            /*
             * Grava log
             */
			if($this->dao->isGravaLogAtendimento){

				$clinome = $this->dao->getNomeCliente($contrato->conclioid);

				if($retornoDB2){
					$retornoDB2 = (array)$retornoDB2;

					$horaContato 	= isset($retornoDB2['chamada']) ? trim($retornoDB2['chamada']) : '';

					if(!empty($horaContato)){

						$hora		 = substr($horaContato, 11, 5);
						$horaContato = str_replace('.', ':', $hora);

					}else{
						$horaContato = '';
					}
				}
                //CONTRATO | COD CLIENTE | OPCAO SELECIONADA | TEL. CONTATADO | DATA/HORA CONTATO | PARAMETROS ENTRADA
				$conteudoLog = 	$contrato->connumero . " | " . $contrato->conclioid . " | " . $clinome . " | ";
				$conteudoLog .= $param->opcaoSelecionada .  " | ". $param->telefoneContato . " | ". $horaContato . " | " ;
				$conteudoLog .=  $param->codigoIdentificador . "#" . $param->idTelefoneContato . "#" . $param->telefoneContato;

                $arrLog[0] = $conteudoLog;
				$this->gravarLogAtendimento($this->dao->nomeCampanha, "_ws_entrada_", $arrLog);
			}

		}catch(Exception $e){

			$this->dao->transactionRollback();
			throw new Exception($e->getMessage());

		}

	}

	/**
	 * Busca informações adicional para URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	 */
	public function informacoesAdicionais(UraAtivaParamVO $param) {

		$vegoid		 	= isset($param->codigoIdentificador) ? $param->codigoIdentificador : 0 ;
		$retorno 		= new UraAtivaRetornoVO();

		//verifica se o veículo existe
		$veioid 	= $this->dao->getVeiculoEstatisticaGsm($vegoid);

		if(!$veioid){
			return array();
		}

		//Informações do contrato
		$contrato 		= $this->dao->buscarInformacoesContrato($veioid);
		$connumero		= isset($contrato->connumero) ? $contrato->connumero : 0;
		$conclioid		= isset($contrato->conclioid) ? $contrato->conclioid : 0;

		$tipoContrato 	= ($this->dao->isContratoSeguradora($connumero)) ? 'Seguradora' : 'Cliente';

		//Informações do cliente
		$cliente 	= $this->dao->getClientePorContrato($connumero);
		$cliNome 	= isset($cliente->nome) 	? $cliente->nome 	: '';
		$cliEmail 	= isset($cliente->email) 	? $cliente->email 	: '';

		//informações do veiculo
		$veiculo 		 = $this->dao->getVeiculo($veioid);
		$placa	 		 = isset($veiculo->placa) 	? $veiculo->placa 	: '';
		$isAtualizaPlaca = ($this->dao->isAtualizaPlaca($placa)) ? 'S' : 'N';

		$retorno->body = array(
				'placas' 			=> $placa,
				'atualiza_placa'	=> $isAtualizaPlaca,
				'contratante'		=> utf8_encode($cliNome),
				'tipo_contrato'		=> utf8_encode($tipoContrato),
				'email'				=> $cliEmail
		);

        /*
         * Gravar Log
         */
		if($this->dao->isGravaLogAtendimento){

            //CONTRATO | COD CLIENTE | PARAMETROS ENTRADA |  PARAMETROS SAIDA
			$conteudoLog = 	$connumero . " | " . $conclioid . " | " . $cliNome . " | ";
			$conteudoLog .= $param->codigoIdentificador . "#" . $param->idTelefoneContato . "#" . $param->telefoneContato . " | ";
			$conteudoLog .= $placa . "#" . $isAtualizaPlaca .'#'. $cliNome . "#" . $tipoContrato . "#". $cliEmail;

            $arrLog[0] = $conteudoLog;
			$this->gravarLogAtendimento($this->dao->nomeCampanha, "_ws_consulta_", $arrLog);
		}

		return $retorno;

	}

	/**
	 * Monta um histórico de contrato
	 *
	 * @param int $vegoid
	 * @param int $veioid
	 * @param string $dataContatoDB2
	 * @param string $motivo
	 * @param string $email
	 * @return string
	 */
	public function montarHistoricoEstatisticaGsm($vegoid, $veioid, $dataContatoDB2, $motivo, $email=''){

		$dataContatoDB2 = $this->formatarDataContatoDB2($dataContatoDB2);

		$localizacao = $this->dao->buscarUltimaLocalizaoVeiculo($veioid);

		$obs =	"Ura Ativa Estatística GSM \n\n";
		$obs .=  "Data/Hora do contato: " . $dataContatoDB2 ."\n";
		$obs .= "Última localização: " . $localizacao . "\n";
		$obs .= "Motivo: " . $motivo;

		if($email != ''){
			$obs .= "\n" . $email;
		}

		return $obs;

	}

	/**
	 * Monta o corpo do email a ser enviado.
	 * @param string $motivo
	 * @param int $clioid
	 * @param int $veioid
	 * @return string
	 */
	protected function montarCorpoEmailNavegacao($motivo, $clioid, $veioid, $connumero){

		$clioid = isset($clioid) ? $clioid : 0;
		$veioid = isset($veioid) ? $veioid : 0;

		$clinome = $this->dao->getNomeCliente($clioid);
		$veiculo = $this->dao->getVeiculo($veioid);

		$veiculo = isset($veiculo->placa) ? $veiculo->placa : '';

		$seguradora = $this->dao->getNomeSeguradora($veioid, $connumero);

		$bodyMail = "Nome do cliente: $clinome <br />";
		$bodyMail .= "Placa: $veiculo <br />";
		$bodyMail .= "Nome da Seguradora: $seguradora <br />";
		$bodyMail .= "Motivo: $motivo";

		return $bodyMail;

	}

	/**
	 * Historico Insucesso
	 */
	public function montarHistoricoInsucesso($insucesso, $motivo){

		/*
		 * Data / Hora
		*/
		$dataContatoDB2 = $this->formatarDataContatoDB2($insucesso['chamada']);

		/*
		 * Localizacao
		*/
		$localizacao = $this->dao->buscarUltimaLocalizaoVeiculo($insucesso['veioid']);

		$obs  =	"Ura Ativa Estatística GSM\n";
		$obs .= "Data/Hora do contato: " . $dataContatoDB2 ."\n";
		$obs .= "Última localização: " . $localizacao . "\n";
		$obs .= "Telefones Contatados: " . $insucesso['telefones'] . "\n";
		$obs .= "Motivo: " . $motivo . "\n";

		return $obs;
	}

	/**
	 * Valida email
	 */
	public function validarEmail($email){

		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

			$host = end(explode('@', $email, 2));

			if(!checkdnsrr($host)){
				return false;
			}

			return true;

		} else {
			return false;
		}

	}

	/**
	 * Metodo que monta o texto do email
	 */
	public function montarTextoEmail($arrParams){

		ob_start();

		require _MODULEDIR_ . 'Atendimento/View/Email/ESTATISTICA_INSUCESSO.php';

		$texto = ob_get_contents();

		ob_end_clean();

		return $texto;

	}

	/**
	 *
	 * @param Obj Contrato
	 * @param informações panicos_pendentes $row
	 * @return array
	 */
	public function buscarInformacoesEnvioEmail($Contrato, $Cliente){

		$arrRetorno = array();

		/*
		 * Busca o chassi do veiculo pelo veioid
		*/
		$Veiculo = $this->dao->getVeiculo($Contrato->conveioid);

		/*
		 * Informacoes de data para envio de email
		*/
		$arrDataEnvioEmail = array(
				'dia' => date('d'),
				'mes' => Data::getTraducaoMes(date('m')),
				'ano' => date('Y')
		);

		/*
		 * Informacoes veiculo
		*/
		$arrVeiculo = array(
				'placa' => str_replace('-', '', $Veiculo->placa),
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
				'veiculo'		=> $arrVeiculo
		);


		return $arrRetorno;
	}

	/**
	 * Trata os insucessos
	 *
	 */
	public function tratarInsucessos() {

        $arrInsucesos1  = array();
        $arrInsucesos2  = array();
        $insucessos     = $this->dao->buscarTentativasInsucessoContato();

        while($linha = pg_fetch_object($insucessos)) {

             if($linha->qtd_contatos == $linha->qtd_tentativas) {

                 $insucesso['telefones'] = $linha->telefones;
                 $insucesso['chamada'] = $linha->chamada;
                 $insucesso['contatoExterno'] = $linha->contato_externo;
                 $insucesso['veioid'] = $linha->veioid;

                 $dadosLog = $this->tratarInsucessosFase2($insucesso);

                 /*
                  * preparar massa log
                  */
                 if($this->dao->isGravaLogAtendimento) {
                    $contrato 	= $this->dao->buscarInformacoesContrato($insucesso['veioid']);
					$dadosCliente = $this->dao->buscaClienteByVeioid($insucesso['veioid']);
                    //CONTRATO | COD CLIENTE | COD VEICULO | MOTIVO
					$arrInsucesos1[] = $contrato->connumero . " | " . $dadosCliente->clioid . " | " . $insucesso['veioid'] . " | Contato sem sucesso" ;
				}

            }
        }

        /*
         * Trata os insucessos registrados no discador (DB2)
         */
         $contatos = $this->dao->buscarContatosDiscadorEstatistica();

			foreach($contatos as $k => $v){

				$insucesso = $this->dao->buscarInsucessos($contatos[$k]['id_contato'], $contatos[$k]['vegoid']);

                if(!empty($insucesso)) {

                     $insucesso['veioid'] = $this->dao->getVeiculoEstatisticaGsm($insucesso['contatoExterno']);

                     $this->tratarInsucessosFase2($insucesso);

                    /*
                    * preparar massa log
                    */
                     if($this->dao->isGravaLogAtendimento) {
                        $contrato 	= $this->dao->buscarInformacoesContrato($insucesso['veioid']);
                        $dadosCliente = $this->dao->buscaClienteByVeioid($insucesso['veioid']);
                        //CONTRATO | COD CLIENTE | COD VEICULO | MOTIVO
                        $arrInsucesos2[] = $contrato->connumero . " | " . $dadosCliente->clioid . " | " . $insucesso['veioid'] . " | Contato sem sucesso" ;
                    }
                }
			}

           $this->logAtendimento = array_merge($arrInsucesos1,$arrInsucesos2);

    }

    private function tratarInsucessosFase2($insucessos) {

        $vegoid = $insucessos['contatoExterno'];
        $veioid = $insucessos['veioid'];

		/*
		 * Busca od ids das acoes referentes a notificacao por Email
		* $arrAcao[0] == Cliente notificado via E-mail 1
		* $arrAcao[1] == Cliente notificado via E-mail 2
		* $arrAcao[2] == Cliente notificado via E-mail 3
		*/
		$arrAcao = $this->dao->buscarAcaoPorDescricao('%Cliente notificado via E-mail%');

        # Busca o objeto Contrato
        $Contrato 	= $this->dao->buscarInformacoesContrato($veioid);

		# Busca o objeto Cliente
		$Cliente = $this->dao->getClientePorContrato($Contrato->connumero);

		/*
		* Acao do contrato é diferente de Cliente notificado via E-mail (N)
		* Fluxo Principal
		*/
		if(!in_array($Contrato->conegaoid, $arrAcao)){

			#atualizar motivo para  Cliente notificado via E-mail 1
			$this->dao->atualizarAcaoContrato($Contrato->connumero, $arrAcao[0]);

			#Altera o status do registro de estatistica - parametro Status A (Em Andamento)
			#Atualiza a data de agendamento - Parametro veiculo e quantidade de dias para agendamento posterior
            $this->dao->atualizarEstatisticaGsm(self::STATUS_ANDAMENTO, $vegoid, true, 10);

            $this->tratarInsucessosFase3($insucessos, $Cliente, $Contrato, $vegoid);

		}
		/*
		* Acao do contrato é igual a Cliente notificado via E-mail 1
		*/
		else if ($Contrato->conegaoid == $arrAcao[0]){

		#atualizar motivo para  Cliente notificado via E-mail 2
			$this->dao->atualizarAcaoContrato($Contrato->connumero, $arrAcao[1]);

			#Altera o status do registro de estatistica - parametro Status A (Em Andamento)
			#Atualiza a data de agendamento - quantidade de dias para agendamento posterior
            $this->dao->atualizarEstatisticaGsm(self::STATUS_ANDAMENTO, $vegoid, true, 20);

            $this->tratarInsucessosFase3($insucessos, $Cliente, $Contrato, $vegoid);

		}
		/*
		* Acao do contrato é igual a Cliente notificado via E-mail 2
		*/
		else if ($Contrato->conegaoid == $arrAcao[1]){

            #atualizar motivo para  Cliente notificado via E-mail 3
			$this->dao->atualizarAcaoContrato($Contrato->connumero, $arrAcao[2]);

			#Altera o status do registro de estatistica - parametro Status C (Concluido)
            $this->dao->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $vegoid);

            $this->tratarInsucessosFase3($insucessos, $Cliente, $Contrato, $vegoid);
		}

	}

	/**
	 * Metodo para realizar as tarefas que são comum entre as validacoes do metodo tratarInsucessos()
	 */
	public function tratarInsucessosFase3($insucessos, $Cliente, $Contrato, $vegoid){

		$enviou = false;

		#Historico contrato
		$observacao = $this->montarHistoricoInsucesso($insucessos, 'Contato sem sucesso');

        $this->dao->inserirHistoricoContrato($Contrato->connumero, $observacao);

		# Envia email
		$emailCliente = (!empty($Cliente->email)) ? trim($Cliente->email) : (!empty($Cliente->email_nfe)) ? trim($Cliente->email_nfe) : '';

		if(empty($emailCliente)){ //[EF51]

			$observacao = $this->montarHistoricoInsucesso($insucessos, 'E-mail não enviado por falta de e-mail cadastrado.');

			$this->dao->inserirHistoricoContrato($Contrato->connumero, $observacao);

		} else if(!$this->validarEmail($emailCliente)){ //[EF53]

            $observacao = $this->montarHistoricoInsucesso($insucessos, 'Endereço(s) de e-mail cadastrado(s) inválido(s).');

			$this->dao->inserirHistoricoContrato($Contrato->connumero, $observacao);

		} else {

            $arrParams = $this->buscarInformacoesEnvioEmail($Contrato, $Cliente);

            $textoEmail = $this->montarTextoEmail($arrParams);

            $enviou = $this->enviarEmail($emailCliente, 'Sascar informa', $textoEmail);

            //[EF52]
            if(!$enviou){

                $observacao = $this->montarHistoricoInsucesso($insucessos, 'Erro ao enviar email.');

                $this->dao->inserirHistoricoContrato($Contrato->connumero, $observacao);
            }

            //[RN6.6]
            $falha_enviar_email = (boolean)$enviou;

            $tipo_log = (!$falha_enviar_email) ? 'I' : 'S';
            $obs_log  = (!$falha_enviar_email) ? 'Insucesso de Envio' : 'Sucesso de Envio';

            $this->dao->inserirLogEnvioEmail($Contrato->connumero, $tipo_log, $obs_log, 2);

		}

        //remove das tabelas auxiliares
        $this->dao->excluirEstatisticaAuxiliar($vegoid);
        $this->dao->removerInsucessoContato($vegoid);
	}


}