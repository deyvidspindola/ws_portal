<?php
/**
 * Classe que define as regras de ativação do segundo buzzer
 * STI 86396
 */

require_once _MODULEDIR_ . "Principal/DAO/AtivacaoSegundoBuzzerDAO.php";

class AtivacaoSegundoBuzzer {

	private $dao;
	private $versaoMinimaEqpto;

	public function __construct($conn, $versaoMinimaEqpto = 'J') {
		$this->dao = new AtivacaoSegundoBuzzerDAO($conn);
		$this->versaoMinimaEqpto = $versaoMinimaEqpto;
	}

	public function getVersaoMinimaEqpto() {
		return $this->versaoMinimaEqpto;
	}

	/**
	 * Retorna array associativo com os dados do equipamento
	 * @param  [array] $xmlBuffer [Array que contem os dados do xml retornado a partir do comando do setup]
	 * @return [array]            [Array associativo com os dados do equipamento]
	 */
	public function valoresConfigEqpto($xmlBuffer) {
		$dadosEqpto = array();

		if(isset($xmlBuffer['valor']) && is_string ($xmlBuffer['valor'])) {
			$xmlInfoEqpto = simplexml_load_string($xmlBuffer['valor']);
	        foreach ($xmlInfoEqpto->cabecalho->children() as $key => $value) {
	            $dadosEqpto[$key] = (string) $value->attributes()->val;
	        }
        }

        return $dadosEqpto;
	}

	/**
	 * Verifica se a OS possui obrigação financeira de locação de segundo buzzer
	 * @param  [integer] $ordoid [id da ordem de serviço]
	 * @return [boolean]         [description]
	 */
	public function verificaAtivacao($ordoid) {
		$retorno = false;
		// Consulta obrigacao financeira na tabela de parametros
		$resObroid = $this->dao->obrigacaoFinanceiraParametrizada();
		if(isset($resObroid->valvalor)) {
			// Verifica se algum servico da OS possui a obrigacao financeira parametrizada
			$servicos = $this->dao->verificaLocacaoBuzzer($ordoid, $resObroid->valvalor);

			if(is_object($servicos)) {
				$retorno = true;
			}
		}

		return $retorno;
	}

	/**
	 * Verifica obrigação financeira de um determinado serviço cadastrado
	 * @param  [integer] $consoid [id do serviço (tabela contrato_serviços)]
	 * @param  [integer] $obroid  [id da obrigação financeira]
	 * @return [type]          [description]
	 */
	public function verificaObrigacaoFinanceiraContrato($consoid) {
		$retorno = false;
		$resObroid = $this->dao->obrigacaoFinanceiraParametrizada();
		if(isset($resObroid->valvalor)) {
			// Verifica se o servico da contrato_servico possui a obrigacao financeira parametrizada
			$servico = $this->dao->verificaLocacaoBuzzerContrato($consoid,$resObroid->valvalor);

			if(is_object($servico)) {
				$retorno = true;
			}
		}

		return $retorno;
	}

	/**
	 * Retorna serial do equipamento a partir do numero da OS
	 * @param  [type] $ordoid [description]
	 * @return [type]         [description]
	 */
	public function serialEquipamento($ordoid) {
		$resSerial = $this->dao->serialEquipamento($ordoid);

		return isset($resSerial->equesn) ? $resSerial->equesn : false;
	}

	/**
	 * Valida equipamento a partir dos dados do setup
	 * @param  [array] $dadosEqpto [array contendo as informacoes do equipamento]
	 * @return [array]             [description]
	 */
	public function validaEquipamento($dadosEqpto) {
		$retorno = array(
			'libera_testes' => false,
			'enviar_comando_ativacao' => false,
			'msg_erro'	=> ''
		);
		
		$resValidacaoFirm = $this->validaScriptFirmware($dadosEqpto['versao_firmware']);
		$resValidacaoLua = $this->validaScriptLua($dadosEqpto['lua_versao_script']);

		if($resValidacaoFirm == false) {
			// Firmware incompativel
			$retorno['msg_erro'] = $this->mensagemErroValidacaoEqpto('FIRMWARE_INVALIDO');
			// Firmware e script lua incompativeis
			if($resValidacaoLua['versao_compativel'] == false && $resValidacaoLua['libera_testes'] == false) {
				$retorno['msg_erro'] = $this->mensagemErroValidacaoEqpto('SCRIPT_FIRMWARE_INVALIDO');
			}
		} else if ($resValidacaoFirm == true && ($resValidacaoLua['versao_compativel'] == false && $resValidacaoLua['libera_testes'] == false) ) {
			// script lua incompativel
			$retorno['msg_erro'] = $this->mensagemErroValidacaoEqpto('SCRIPT_INVALIDO');
		}

		// Versao firmware e script lua com versão compatível
		if($resValidacaoFirm == true && $resValidacaoLua['libera_testes'] == true) {
			if($resValidacaoLua['versao_compativel'] == true) {
				$retorno['enviar_comando_ativacao'] = true;
			} 
				
			$retorno['libera_testes'] = true;
		}

		return $retorno;
	}

	public function validaScriptLua($versaoScript) {
		$versaoScript = (float) $versaoScript;
		$retorno = array(
			'envia_comando' => false,
			'libera_testes' => false,
			'versao_compativel' => false
		);

		if($versaoScript >= 4.0 && $versaoScript <= 9.9) {
			$retorno['envia_comando'] = true;
			$retorno['libera_testes'] = true;
			$retorno['versao_compativel'] = true;
		} else if($this->scriptLuaSemEnvioComando($versaoScript) == true) {
			$retorno['libera_testes'] = true;
		}
		
		return $retorno;
	}

	/**
	 * Retorna se a versao libera continuidade dos testes
	 * @param  [float] $versao 	[versao do script lua]
	 * @return [bool]         	[true=libera testes/false=nao libera testes]
	 */
	private function scriptLuaSemEnvioComando($versao) {
		$versao = (string) $versao;
		$semEnvioComando = array(
			'3.2' => true,
			'3.4' => true,
			'3.6' => true
		);
		//3.06
		return isset($semEnvioComando[$versao]) ? true : false;
	}

	/**
	 * Valida se a versão do firmware é valida > 11J
	 * @param  [type] $versaoFirm [description]
	 * @return [type]             [description]
	 */
	private function validaScriptFirmware($versaoFirm) {
		
    	preg_match("/\[[^\]]*\]/", $versaoFirm, $matches);
    	
    	if(isset($matches[0])) {
    		$versao = str_replace('[', '', $matches[0]);
    		$versao = intval(str_replace(']', '', $versao));

    		//Firmware deve ter versão maior que 11J -> (int) 65866
    		if($versao < 65866) { //66372
    			return false;
    		}

			return true;
    	}
    	
    	return null;
	}

	public function mensagemErroValidacaoEqpto($tipoMsg) {
		$mensagens = array(
			'SCRIPT_FIRMWARE_INVALIDO' => '2050',
			'FIRMWARE_INVALIDO' => '2051',
			'SCRIPT_INVALIDO' => '2052',
			'VERSAO_EQUIPAMENTO_INTRANET' => '2053'
		);

		return isset($mensagens[$tipoMsg]) ? $mensagens[$tipoMsg] : '';
	}

	/**
	 * Retorna comando de ativação do segundo buzzer
	 * @param  [string] $esn [serial do equipamento]
	 * @return [string]      [comando]
	 */
	public function comandoAtivacao($esn) {
	 	$finalizador = "\r\n0\r\n | nc 10.1.110.20 8500";
		return trim($esn) . ' 1 1 1 GPRS ATIVAR_2BUZZER 0000 " "'.$finalizador;
	}

	/**
	 * Valida a versao do equipamento cadastrada na intranet 
	 * @param  [string] $eveversao [string da versao cadastrada na intranet(equipamento_versao.eveversao)]
	 * @return [boolean]            [description]
	 */
	public function comparaVersaoEquipamentoIntranet($eveversao) {

		$eveversao = trim($eveversao);

		if(isset($eveversao) && is_string($eveversao) && strlen($eveversao) > 0) {
			$arrVersoes = array();
			$versaoEquipamento = explode("_", $eveversao);
			$versaoEquipamento = end($versaoEquipamento);

			// Foi definido que a versao do equipamento seria validada de acordo com a letra no final
			// da string (separada por _), e também deve-se comparar casos do tipo MTC_700_AA > MTC_700_Z
			$count = 0;
			for($x = 'A'; $x < 'ZZ'; $x++) {
				$arrVersoes[$x] = $count++;
			}

			if(isset($arrVersoes[$versaoEquipamento]) && isset($arrVersoes[$this->getVersaoMinimaEqpto()])) {
				if($arrVersoes[$versaoEquipamento] >= $arrVersoes[$this->getVersaoMinimaEqpto()]) {
				 	return true;
				}
			}

		}

		return false;
	}

	/**
	 * Retorna se equipamento é do projeto MTC700
	 * @param  [integer] $consoid [id da tabela contrato_servico]
	 * @param  [integer] $ordoid  [id da tabela ordem_servico]
	 * @return [type]          [description]
	 */
	public function verificaEquipamentoMTC($consoid,$ordoid) {
		// Fazer consulta de busca de parametrização

		$eproid = 63;

		$resEquipamento = $this->dao->verificaEquipamentoMTC($eproid,$consoid,$ordoid);

		if(is_object($resEquipamento)) {
			return $resEquipamento;
		}

		return false;
	}

	public function insereMotivoOrdemServico() {
		
	}
}
