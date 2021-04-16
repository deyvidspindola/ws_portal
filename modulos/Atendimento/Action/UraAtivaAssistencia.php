<?php

require_once _MODULEDIR_ . 'Atendimento/Action/UraAtiva.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaParamVO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaRetornoVO.php';
require_once _MODULEDIR_ . 'Atendimento/DAO/UraAtivaAssistenciaDAO.php';

class UraAtivaAssistencia extends UraAtiva {

	public function __construct($conn) {
		$this->dao = new UraAtivaAssistenciaDAO($conn);
	}

    /**
	 * Torna público um array com as informações para gravar o log de atendimento;
	 */
    public $logAtendimento = array();

	/**
	 * Trata a reposta da URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	 */
	public function navegacao(UraAtivaParamVO $param) {

		$retorno 		= new UraAtivaRetornoVO();
		$horaContato 	= '';
		$clinome 		= '';
        $arrLog         = array();

		$this->dao->transactionBegin();

		try {

			if (empty($param->opcaoSelecionada)) {
				throw new Exception('0160'); // Todos parâmetros de entrada são obrigatórios.
			}

			$osCliente = $this->dao->buscarEnviosDiscadorCliente($param->codigoIdentificador);

			switch ($param->opcaoSelecionada) {

				//Informe a melhor data para o próximo contato para agendamento deste serviço
				case 'ASSISTENCIA_DATA_AGENDAMENTO':

					if (empty($param->data)) {
						throw new Exception('0160'); // Todos parâmetros de entrada são obrigatórios.
					}

					// reagenda retorna OK
					$clioid = $param->codigoIdentificador;
					$this->dao->reagendarOSCliente($clioid, $param->telefoneContato, $param->data, '', $osCliente, $this->dao->getIdProximoContato());

                    //Remove das tabelas auxiliares
					$this->dao->excluirEnviosDiscadorPorCliente($param->codigoIdentificador);
					$this->dao->removerInsucessoContato($clioid);

				break;

				//Data não informada, aguarde novo contato.
				case 'ASSISTENCIA_AGUARDAR_CONTATO':
				//Não digitou nenhuma opção
				case 'ASSISTENCIA_NAO_RESPONDEU':
				//Cliente digitou uma opção inválida
				case 'ASSISTENCIA_OPCAO_INVALIDA':

					$clioid = $param->codigoIdentificador;
					$clitel = $param->telefoneContato;

					//Incrementar tentativa - atualizarTentativaInsucessoContato
					$this->dao->atualizarTentativaInsucessoContato($clioid, $clitel);

					//Incrementar tentativa dos insucessos no discador (DB2)
					$this->dao->atualizarTentativaInsucessoCliente($clioid);

				break;

				//Veículo não disponível para agendamento disque 2
				case 'ASSISTENCIA_AGENDAMENTO':

					// insere o histórico e libera para contato novamente e retorna OK
					$clioid = $param->codigoIdentificador;
					$data = date('Y-m-d');
					$hora = date('H:i:s');
					$this->dao->reagendarOSCliente($clioid, $param->telefoneContato, $data, $hora, $osCliente, $this->dao->getIdProximoContato());

                     //Remove das tabelas auxiliares
					$this->dao->excluirEnviosDiscadorPorCliente($param->codigoIdentificador);
					$this->dao->removerInsucessoContato($clioid);

				break;

				//Cliente digitou uma data inválida
				case 'ASSISTENCIA_DATA_INVALIDA':

					$clioid = $param->codigoIdentificador;
					$clitel = $param->telefoneContato;

					//Incrementar tentativa - atualizarTentativaInsucessoContato
					$this->dao->atualizarTentativaInsucessoContato($clioid, $clitel);

					//Incrementar tentativa dos insucessos no discador (DB2)
					$this->dao->atualizarTentativaInsucessoCliente($clioid);

					$this->dao->transactionCommit();

					throw new Exception('0220'); // Data inválida.

				break;

				//Para Agendar neste momento, disque 1
				case 'ASSISTENCIA_ATENDENTE':
					$clioid = $param->codigoIdentificador;

                     //Remove das tabelas auxiliares
					$this->dao->excluirEnviosDiscadorPorCliente($clioid);
					$this->dao->removerInsucessoContato($clioid);

				break;

				//Aguarde na ligação para falar com um dos nossos atendentes.
				//Aguarde estamos transferindo sua ligação para um dos nossos atendentes
				default:

					$clioid = $param->codigoIdentificador;
					$clitel = $param->telefoneContato;

					//Incrementar tentativa - atualizarTentativaInsucessoContato
					$this->dao->atualizarTentativaInsucessoContato($clioid, $clitel);

					//Incrementar tentativa dos insucessos no discador (DB2)
					$this->dao->atualizarTentativaInsucessoCliente($clioid);

				break;
			}

            $this->dao->transactionCommit();

            /*
             * Grava arquivo de Log
             */
			if($this->dao->isGravaLogAtendimento){

				$clioid = $param->codigoIdentificador;

				$clinome = $this->dao->getNomeCliente($clioid);

				$id_contato = $this->dao->buscarContatoDiscadorEspecifico($clioid);

				$retornoDB2 = $this->dao->buscarInsucessos($id_contato, $clioid);

				if(!empty($retornoDB2)){

					$horaContato 	= isset($retornoDB2['chamada']) ? trim($retornoDB2['chamada']) : '';

					if(!empty($horaContato)){

						$hora		 = substr($horaContato, 11, 5);
						$horaContato = str_replace('.', ':', $hora);

					}else{
						$horaContato = '';
					}
				}

                //ORDEM SERVICO | COD CLIENTE | NOME | OPCAO SELECIONADA | TEL. CONTATADO | HORA CONTATO | PARAMETROS ENTRADA
				$conteudoLog = 	$osCliente . " | " . $clioid . " | " . $clinome . " | ";
				$conteudoLog .= $param->opcaoSelecionada .  " | ". $param->telefoneContato . " | ". $horaContato . " | " ;
				$conteudoLog .= $param->codigoIdentificador . "#" . $param->idTelefoneContato . "#" . $param->telefoneContato;

                $arrLog[0] = $conteudoLog;
				$this->gravarLogAtendimento($this->dao->nomeCampanha, "_ws_entrada_", $arrLog);
			}

		} catch (ExceptionDAO $e) {

			$this->dao->transactionRollback();

			throw new Exception('0190'); // Falha na execução do banco de dados.

		} catch (Exception $e) {

			$this->dao->transactionRollback();

			throw new Exception($e->getMessage()); // Mensagem de erro especializada
		}

		return $retorno;
	}

	/**
	 * Busca informações adicional para URA
	 * @param UraAtivaParamVO $param
	 * @return UraAtivaRetornoVO
	 */
	public function informacoesAdicionais(UraAtivaParamVO $param) {

		$dados 			= array();
		$ordensServ 	= array();
        $arrLog         = array();
		$clinome	 	= '';
		$ordens 		= $this->dao->buscarInformacoesAssistencia($param->codigoIdentificador);
		$placas 		= array();
		$qtdeOS 		= count($ordens);
		$atualiza_placa = "N";

		if($qtdeOS > 0){

			foreach ($ordens as $ordem) {
				if ($ordem['veiplaca'] != "") {
					if (($ordem['veiplaca'] == $ordem['vplplaca'])||($ordem['veiplaca'] == $ordem['vplplaca_maq'])||
							($ordem['veiplaca'] == $ordem['vplplaca_tra']))
						$atualiza_placa = 'S';
					else
						$atualiza_placa = 'N';

					array_push($placas, $ordem['veiplaca']);
					array_push($ordensServ, $ordem['ordoid']);
				}
			}
		} else {
			throw new exception("0170");
		}

		$retorno = new UraAtivaRetornoVO();
		$retorno->body = array(
				'quantidade_OS_pendente' 	=> $qtdeOS,
				'placas'			 		=> implode("#", $placas),
				'atualiza_placa'			=> $atualiza_placa,
		);

        /*
         * Gravar Log
         */
		if($this->dao->isGravaLogAtendimento) {

			$clinome = $this->dao->getNomeCliente($param->codigoIdentificador);

            //COD CLIENTE | NOME | PARAMETROS ENTRADA |  PARAMETROS SAIDA | ORDENS SERVICO
			$conteudoLog =  $param->codigoIdentificador . " | " . $clinome . " | ";
            $conteudoLog .= $param->codigoIdentificador . "#" . $param->idTelefoneContato . "#" . $param->telefoneContato . " | ";
			$conteudoLog .= $qtdeOS . "#" . implode(",", $placas) . "#". $atualiza_placa . " | ";
            $conteudoLog .= implode(",", $ordensServ);

            $arrLog[0] = $conteudoLog;
			$this->gravarLogAtendimento($this->dao->nomeCampanha, "_ws_consulta_", $arrLog);
		}

		return $retorno;
	}

	/**
	 * Verifica e elimina as OSs que devem ser eliminadas do Discador
	 * @param int $idCampanha
	 * @return array;
	 */
	public function verificarOrdemServicoDiscador(){

        $idCampanha = $this->dao->campanha;
        $arrLogDescarte1    = array();
        $arrLogDescarte2    = array();

		//busca as OSs da tabela auxiliar
		$ordemServico = $this->dao->buscarEnviosDiscador();

		while($tupla = pg_fetch_object($ordemServico)) {

			$idContatoExterno 	= isset($tupla->cduaid_contato_discador) ? $tupla->cduaid_contato_discador : 0;
			$idCliente			= isset($tupla->cduaclioid) ? $tupla->cduaclioid : 0;
			$numeroOS			= isset($tupla->cdua_os) ? $tupla->cdua_os : '';

			if(empty($numeroOS) || empty($idContatoExterno)){
				continue;
			}

			//Busca os contatos pendentes por OS
			$contatosPendentes = $this->dao->buscarContatosPendentesOrdemServico($numeroOS);

			if( count($contatosPendentes) == 0 ){

				//Elimina os contatos do Discador
				if($this->dao->eliminarContatoDiscador($idCampanha, $idContatoExterno)){

					//Deleta OSs da tabela auxiliar (contato_discador_ura_assistencia)
					$this->dao->excluirEnviosDiscador($idContatoExterno);

                    //Remove da segunda tabela auxiliar (contato_discador_ura_assistencia_aux)
                    $this->dao->removerInsucessoContato($idCliente);

                   //Array de Log do atendimento
                    $clinome = $this->dao->getNomeCliente($idCliente);
                    $arrLogDescarte1[] = $numeroOS. " | " . $idCliente . " | " . $clinome . " | Nenhuma OS necessita atendimento.";

				}

			}else{

				foreach($contatosPendentes as $dados) {

					//Descarta as OS que não devem enviadas
					if ($this->dao->descartar($dados)) {

						//Limpa a data da agenda na OS para não enviar novamente na próxima execução
						$this->dao->limparDataAgendaOS($dados);

						//Elimina os contatos do Discador
						if($this->dao->eliminarContatoDiscador($idCampanha, $idContatoExterno)){

							//Deleta OSs da tabela auxiliar (contato_discador_ura_assistencia)
							$this->dao->excluirEnviosDiscador($idContatoExterno);

                            //Remove da segunda tabela auxiliar (contato_discador_ura_assistencia_aux)
                            $this->dao->removerInsucessoContato($idCliente);
						}
                        //Array de Log do atendimento
                        $clinome = $this->dao->getNomeCliente($idCliente);
                        $arrLogDescarte2[] = $numeroOS. " | " . $idCliente . " | " . $clinome . " | " . $this->dao->motivoLog;

					}
				}
				//FIM While $contato
			}
			//fim ELSE pg_num_rows
		}
		//FIM While $tupla

		return $this->logAtendimento = array_merge($arrLogDescarte1, $arrLogDescarte2);
	}

    /**
     * Trata os insucessos de contato
     * @return boolean
     */
    public function tratarInsucessos() {

        $arrInsucesos1 = array();
        $arrInsucesos2 = array();
        $insucessos = $this->dao->buscarTentativasInsucessoContato();

        while($insucesso = pg_fetch_object($insucessos)) {

            if($insucesso->qtd_contatos == $insucesso->qtd_tentativas) {

                $cliente       = $insucesso->cliente;
                $telefone      = $insucesso->telefone;
                $ordem_servico = $insucesso->ordem_servico;
                $historico     = $this->dao->buscarTempoReagendamentoOS($cliente, $ordem_servico);
                $data          = new stdClass();
                $dia           = date('d');
                $mes           = date('m');
                $ano           = date('Y');
                $hora          = date('H');
                $minuto        = date('i');
                $segundo       = date('s');

                if($historico < 2) {
                    $data->timestamp = mktime(($hora + 4), $minuto, $segundo, $mes, $dia, $ano);
                } elseif($historico == 2) {
                    $data->timestamp = mktime(($hora + 72), $minuto, $segundo, $mes, $dia, $ano);
                } else {
                    $data->timestamp = mktime(($hora + 4), $minuto, $segundo, $mes, $dia, $ano);
                }

                $data->data = date("Y-m-d", $data->timestamp);
                $data->hora = date("H:i:s", $data->timestamp);

                if(!empty($insucesso->ordem_servico)) {
                    $this->dao->reagendarOSCliente($cliente, $telefone, $data->data, $data->hora, $ordem_servico);
                }

                $this->dao->excluirEnviosDiscadorPorCliente($cliente);
                $this->dao->removerInsucessoContato($cliente);

                if($this->dao->isGravaLogAtendimento) {
					$clinome = $this->dao->getNomeCliente($cliente);
					$arrInsucesos1[] = $cliente . " | " . $clinome . " | " . $ordem_servico;
				}

            }
        }

        $envios = $this->dao->buscarEnviosDiscador();

		if(!empty($envios)) {
			while($envio = pg_fetch_object($envios)) {

				$insucesso = $this->dao->buscarInsucessos($envio->cduaid_contato_discador, $envio->cduaclioid);

				if(!empty($insucesso)) {

                   	$telefone  = $insucesso['telefones'];
					$cliente   = $insucesso['contatoExterno'];
					$historico = $this->dao->buscarTempoReagendamentoOS($cliente, $envio->cdua_os);
					$data      = new stdClass();
					$dia       = date('d');
					$mes       = date('m');
					$ano       = date('Y');
					$hora      = date('H');
					$minuto    = date('i');
					$segundo   = date('s');

					if($historico < 2) {
						$data->timestamp = mktime(($hora + 4), $minuto, $segundo, $mes, $dia, $ano);
					} elseif($historico == 2) {
						$data->timestamp = mktime(($hora + 72), $minuto, $segundo, $mes, $dia, $ano);
					} else {
						$data->timestamp = mktime(($hora + 4), $minuto, $segundo, $mes, $dia, $ano);
					}

					$data->data = date("Y-m-d", $data->timestamp);
					$data->hora = date("H:i:s", $data->timestamp);

					if(!empty($envio->cdua_os)) {
						$this->dao->reagendarOSCliente($cliente, $telefone, $data->data, $data->hora, $envio->cdua_os);
					}

					$this->dao->excluirEnviosDiscadorPorCliente($cliente);
					$this->dao->removerInsucessoContato($cliente);

                    if($this->dao->isGravaLogAtendimento) {
                        $clinome = $this->dao->getNomeCliente($cliente);
                        $arrInsucesos2[] = $cliente . " | " . $clinome . " | " . $envio->cdua_os;
                    }
				}
			}
		}

        if($this->dao->isGravaLogAtendimento) {
             $this->logAtendimento = array_merge($arrInsucesos1, $arrInsucesos2);
        }

	}

}