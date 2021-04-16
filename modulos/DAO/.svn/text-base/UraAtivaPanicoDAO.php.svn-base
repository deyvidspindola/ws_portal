<?php

require_once _MODULEDIR_ . 'Atendimento/DAO/UraAtivaDAO.php';
require_once _MODULEDIR_ . 'Manutencao/DAO/ParametrizacaoUraDAO.php';

/**
 * Regras para atendimento automatico de panicos pendentes
 *
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @version 18/03/2013
 * @since   18/03/2013
 * @package Atendimento
 */
class UraAtivaPanicoDAO extends UraAtivaDAO {


 	const MOTIVO_STATUS_CONTRATO   			= 'Desconsiderado pelo status de contrato';
 	const MOTIVO_PENDENCIA_FINANCEIRA 		= 'Cliente Inadimplente';
 	const MOTIVO_SUSPENSAO_PANICO_GSM 		= 'Suspensão de pânico GSM';
 	const MOTIVO_STATUS_OCORRENCIA  		= 'Ocorrência em Andamento';
 	const MOTIVO_TIPO_CONTRATO   			= 'Desconsiderado pelo tipo de contrato';
 	const MOTIVO_NAO_INSTALADO 				= 'Pânico não instalado';
 	const MOTIVO_PANICO_INDEVIDO 			= 'O.S. Chamada de pânico indevido';
 	const MOTIVO_INCONVENIENTE_BOTAO_PANICO = 'O.S. Inconveniente com botão de pânico';
 	const MOTIVO_MANUTENCAO_LAVACAR 		= 'Veículo em Manutenção/Lava-Car';
 	//const MOTIVO_TESTE_PANICO_SASGC 		= 'MOTIVO_TESTE_PANICO_SASGC';
 	const MOTIVO_TESTE_CLIENTE 				= 'Teste Cliente';
 	const MOTIVO_NAO_EXCLUI_PANICO			= 'NAO_EXCLUI_PANICO';
 	const MOTIVO_INTERVALO_SATELITAL		= 'Pânicos inferior a 40 minutos';
 	const MINUTOS_LIMPA_TESTE 				= 15;
 	const VELOCIDADE_MAXIMA_LAVACAR 		= 15;
 	const INTERVALO_REENVIO_CONTATO			= 20;

 	private $defeitoChamadaPanico;
 	private $defeitosInconveniente;
 	private $tipoAssistenciaOS;
 	private $statusAutorizadaOS;

 	/**
	 * ID da campanha no discador
	 * @var int
	 */
	public $campanha;

	protected $tabela_auxiliar_discador = 'contato_discador_ura_panico';

 	public function __construct($conn, $cronReenvio = false) {

 		parent::__construct($conn);

 		$this->defeitoChamadaPanico		= $this->buscarDefeitoID("Chamadas%de%p_nico%indevidas%");
 		$this->defeitosInconveniente	= $this->buscarDefeitoID("Incoveniente%com%Bot_o%de%P_nico");
 		$this->tipoAssistenciaOS		= array(4);
 		$this->statusAutorizadaOS		= array(4);
 		$this->setCampanha(1);

 		$this->cronReenvio = $cronReenvio;

 	}

	/**
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::getParametros()
	 */
	public function getParametros() {

		$ParametrizacaoUraDAO = new ParametrizacaoUraDAO();

		$params = (object) $ParametrizacaoUraDAO->findLast();

		return $params;
	}

	/**
	 * Busca os contatos para envio
	 * @return array:UraAtivaContatoVO
	 */
	public function buscarContatos($CronParcial = 'A') {

		$rows = array();
		$descartados = array();
		$arrLog = array();
		$clinome = '';

		$sql = $this->buscarContatosPendentes();

		$rs = $this->query($sql);

		while($row = pg_fetch_object($rs)) {

			$contrato = new UraAtivaContratoVO($row);

			/*if($this->desconsiderar($contrato->conveioid)){
				continue;
			}*/

			$clinome = $this->getNomeCliente($row->conclioid);

			if ($this->descartar($contrato)) {

				$descartados[] = $row->codigo;

				//Array de Log do atendimento
				$arrLog['descarte'][] = $row->connumero . " | " . $row->conclioid . " | " . $clinome . " | " . $this->motivoLog;

				continue;
			}

			//Verifica se  Não deve enviar ao discador. Apenas aplicar regras de descarte.
			if($CronParcial == 'P'){
				continue;
			}

			//Se existir na tabela <contato_discador_ura_panico_aux> não envia. Será tratado pelo Cron de Reenvio.
			if($this->verificarInsucessoContato($contrato->conveioid)){
				continue;
			}

			//Array de Log do atendimento
			$arrLog['envio'][] = $row->connumero . " | " . $row->conclioid . " | " . $clinome;

			//$this->tratar($contrato);  //movido para o método afterInserirDiscador

			$this->tratarAtendimentoPanico($contrato, $contrato->codigo);

			$contatos = $this->buscarTelefones($contrato);

			$rows[$contrato->codigo] = $contatos;
		}

		$this->logAtendimento = $arrLog;

		echo '<br />';
		echo '<pre>';
		echo 'DESCARTADOS INI: <hr>';
		print_r($descartados);
		echo 'DESCARTADOS FIM: <hr>';

		return $rows;
	}


	/**
	 * QUERY sql para buscar todos os panicos pendentes
	 * @return string QUERY sql para busca dos contatos pendentes
	 */
	protected function buscarContatosPendentes() {

		$paramTiposPanico = (array) $this->param->puppantoid;

		$sql = "
			SELECT 		panicos_pendentes.oid::int4 AS codigo,
						papveioid,
						papveiplaca,
						paptipo,
						COALESCE(papinstalacao, 0) AS papinstalacao,
						papequoid,
						paphorario,
						papconclioid,
						connumero,
						conequoid,
						conno_tipo,
						concsioid,
						conclioid,
						conveioid
			FROM 		panicos_pendentes
			INNER JOIN 	contrato ON (connumero = papconnumero AND conveioid = papveioid)
			WHERE 		papoperadoroid IS NULL
			AND 		papramal IS NULL
		";

		if (count($paramTiposPanico)) {
			$sql .= " AND paptipo NOT IN (".implode(',', $paramTiposPanico).")";
		}

		$sql .= " ORDER BY papveioid, paphorario ASC" ;
		echo "<br />QUERY: ";
		echo $sql;
		echo "<br />FIM QUERY";
		
		return $sql;

	}

	/**
	 * Realiza processo de descarte de contatos pendentes
	 * @param UraAtivaContratoVO $contrato
	 * @return boolean
	 */
	protected function descartar(UraAtivaContratoVO $panico) {

		$motivo 			= '';
		$obsHistorico 		= '';
		$this->motivoLog 	= '';
		$obsLog 			= '';
		$motivoTipoContrato = '';
		$statusOcorrencia 	= '';

		$paramStatusOcorrencia 		= (array) $this->param->pupocostatus;
		$paramPendenciaFinanceira	= (int) $this->param->puppendencia_financeira;
		$paramTiposContrato 		= (array) $this->param->puptpcoid;

		$paramTiposOS				= (array) array(); // $this->param->pupostoid;
		$paramItensOS 				= (array) array(); // $this->param->pupitem;
		$paramStatusOS 				= (array) $this->param->pupossoid;
		$paramDefeitosOS			= (array) $this->param->pupotdoid;



		if($this->isDescartaTesteCliente($panico->connumero, $panico->papveioid)){

			$motivo = UraAtivaPanicoDAO::MOTIVO_TESTE_CLIENTE;
			$this->removeTestePanico($panico->connumero, $panico->papveioid);

		}
		/* else if ($this->isDescartaIntervaloAcionamento($panico->papveioid, $panico->paptipo, $panico->codigo)) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_INTERVALO_SATELITAL;

			if ($panico->paptipo == 127){

				$motivo = UraAtivaPanicoDAO::MOTIVO_INTERVALO_SATELITAL;

			}else{

				$motivo = 'Pânicos inferior a ' .  $this->param->pupacionamento . ' minutos';
			}

		} */
		else if($this->isDescartaManutencaoLavacar($panico->connumero, $panico->papveioid)){

			$motivo = UraAtivaPanicoDAO::MOTIVO_MANUTENCAO_LAVACAR;

		}
		else if ($this->isDescartaInstalacao($panico->papinstalacao)) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_NAO_EXCLUI_PANICO;
			$obsLog = 'Descarte Instalação';
		}
		else if ($this->isDescartaTipoContrato($panico->conno_tipo, $paramTiposContrato)) {

				$motivo = UraAtivaPanicoDAO::MOTIVO_TIPO_CONTRATO;
				$obsLog = 'Descarte Tipo Contrato';

				$tipoContrato = $this->buscaTipoContrato($panico->connumero);
				$motivoTipoContrato = $tipoContrato->tpcdescricao;

		}
		else if ($this->isDescartaStatusContrato($panico->concsioid)) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_STATUS_CONTRATO;

		}
		else if ($this->isDescartaPendenciaFinanceira($panico, $paramPendenciaFinanceira) && $this->isDescartaBloqueioWeb($panico->connumero, $panico->papconclioid)) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_PENDENCIA_FINANCEIRA;
		}
		else if ($this->isDescartaSuspensaoPanicoGsm($panico->papequoid)) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_SUSPENSAO_PANICO_GSM;

		}
		else if ($this->isDescartaStatusOcorrencia($panico->connumero, $paramStatusOcorrencia, $statusOcorrencia)) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_STATUS_OCORRENCIA;

			$panicoObj = $this->buscaPanicoById($panico->codigo);

			if ($panicoObj) {

				$placaOcorrencia = $panicoObj->papveiplaca;
				$dataHoraAcionamento = $panicoObj->papdatapacote;

				$texto_email = "Acionamento recebido da placa ".$placaOcorrencia." acionado em ".date("d/m/Y H:n", strtotime($dataHoraAcionamento))."  no qual consta ocorrência ".$statusOcorrencia;

				$enviou = $this->enviarEmail("ocorrencia@sascar.com.br", 'Pânico', $texto_email);

				if(!$enviou){

					$falha_enviar_email = true;
				}

				$log_envio_email = true;

				if($log_envio_email){

					$tipo_log = ($falha_enviar_email) ? 'I' : 'S';
					$obs_log  = ($falha_enviar_email) ? 'Insucesso de Envio' : 'Sucesso de Envio';

					$this->inserirLogEnvioEmail($panico->connumero, $tipo_log, $obs_log);
				}
			}

		}
		else if($this->isDescartaBotaoDePanico($panico->connumero, $panico->papveioid)){

			$motivo = UraAtivaPanicoDAO::MOTIVO_NAO_INSTALADO;

		}
		 else if ($this->isDescartaOrdemServicoFixo($panico->connumero, $this->tipoAssistenciaOS, $paramItensOS, $this->statusAutorizadaOS, array($this->defeitoChamadaPanico))) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_PANICO_INDEVIDO;
		}
		else if ($this->isDescartaOrdemServicoFixo($panico->connumero, $this->tipoAssistenciaOS, $paramItensOS, $this->statusAutorizadaOS, array($this->defeitosInconveniente))) {

			$motivo = UraAtivaPanicoDAO::MOTIVO_INCONVENIENTE_BOTAO_PANICO;
		}
		/*
		else if($this->verificarContratoGerenciadora($panico->connumero)){

			$motivo = $this::MOTIVO_NAO_EXCLUI_PANICO;

		}
		*/

		if (!empty($motivo) && $motivo != 'NAO_EXCLUI_PANICO') {

			$motivoBusca = $this->tratarDescricaoPesquisa($motivo);

			$atmoid = $this->buscarMotivosDescarte($motivoBusca);

echo $panico->codigo.' - '.$atmoid.' - '.$motivo.'<hr>';

			$this->assumirPanico($panico->codigo);

 			$this->excluirPanico($panico->codigo, $atmoid);

 			if (!empty($motivoTipoContrato)) {

 				$motivo = $motivoTipoContrato;
 			}

			$obs = $this->montarObservacaoHistorico($panico, $motivo);

			if (!empty($obs)) {

				$this->inserirHistoricoContrato($panico->connumero, $obs);

			}

			$this->motivoLog = $motivo;

		}else{

			$this->motivoLog = $obsLog;
		}

		return (boolean) $motivo;
	}

	private function isDescartaInstalacao($isInstalacao) {

		$paramInstalacao = (boolean) $this->param->pupinstalado;
		$isInstalacao = (boolean) $isInstalacao;

		return ($paramInstalacao and $isInstalacao);
	}

	/**
	 * Valida se a tabela panicoYYYYMM existe
	 * @param string $panicoYYYYMM
	 * @return boolean
	 */
	protected function isExisteTabelaPanico($panicoYYYYMM){

		//Verifica existência da tabela
		$sql = "
			SELECT 	COUNT(1) as total
			FROM 	pg_tables
			WHERE 	tablename = '". $panicoYYYYMM ."'
			";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$isExisteTabela = isset($row->total) ? $row->total : 0;

		return (boolean)$isExisteTabela;

	}

	/**
	 * Recupera a data do pacote da tabela panicoYYYYMM
	 * @param string $panicoYYYYMM
	 * @param int $veioid
	 * @return string
	 */
	protected function getDataPacote($panicoYYYYMM, $veioid, $satelital = false){

		$pandatapacote = '';

		if(empty($panicoYYYYMM) || empty($veioid)){
			return $pandatapacote;
		}

		$sql = "
			SELECT
					pandatapacote
			FROM
					". $panicoYYYYMM ."
			WHERE
					panveioid = ". $veioid ;

		if($satelital){
			$sql .= " AND pantipo = 127";
		}

		$sql .= " ORDER BY pandatapacote DESC LIMIT 1";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$pandatapacote = isset($row->pandatapacote) ? $row->pandatapacote : '';

		return $pandatapacote;
	}

	/**
	 * Desconsiderar quando a mesma placa estiver com acionamento de pânicos menor que (N) minutos
	 * @param int $veioid
	 * @param int $satelital;
	 * @return boolean
	 */
	private function isDescartaIntervaloAcionamento($veioid, $satelital, $oidPanico) {


		if(empty($veioid)){
			return false;
		}

		$tipoSatelital = false;


		$paramAcionamento = (int) $this->param->pupacionamento;

		if($satelital == 127){

			$tipoSatelital = true;
			$paramAcionamento = 40;

		}else if(empty($paramAcionamento) || $paramAcionamento == 0 ){

			return false;
		}

		//Atribui nome de tabela PanicoYYYYMM
		$panicoYYYYMM = "panico" . date('Ym');

		if(!$this->isExisteTabelaPanico($panicoYYYYMM)){
			return false;
		}

		$pandatapacote = $this->getDataPacote($panicoYYYYMM, $veioid, $tipoSatelital);

		if(empty($pandatapacote)){
			return false;
		}

		$sql = "
			SELECT
						(
							CASE WHEN (paphorario < '".$pandatapacote ."'::timestamp) THEN
								((ROUND((EXTRACT(EPOCH FROM ('". $pandatapacote ."'::timestamp - paphorario)))/60))< ". $paramAcionamento .")
							ELSE
								((ROUND((EXTRACT(EPOCH FROM (paphorario - '". $pandatapacote ."'::timestamp)))/60))< ". $paramAcionamento .")
							END
						) AS intervalo_acionamento
			FROM
						panicos_pendentes
			WHERE
						panicos_pendentes.oid = ".$oidPanico."
		";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);

		if (pg_num_rows($rs) > 0) {

			$isAcionado = ($row->intervalo_acionamento == 't') ? true : false;
		} else {

			$isAcionado = false;
		}

		return (boolean) $isAcionado;
	}

	/**
	 * Desconsiderar Pânicos por Status de Ocorrência
	 * @param int $connumero
	 * @param int $veioid
	 * @return boolean
	 */
	private function isDescartaExSegurado($connumero, $veioid) {

		$sql = "
			SELECT	 	COUNT(1) AS total
			FROM 		contrato
			INNER JOIN 	tipo_contrato ON tpcoid = conno_tipo
			WHERE 		tpcdescricao ILIKE 'Ex%'
			AND 		connumero = ".$connumero."
			AND 		conveioid = ".$veioid."
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$total = isset($row->total) ? $row->total : 0;

		return (boolean) $total;
	}

	/**
	 * Remove a marcação de Pânico de Teste
	 * @param integer $connumero
	 * @param integer $veioid
	 * @return boolean
	 */
	private function removeTestePanico($connumero, $veioid) {

		$sql = "
			DELETE FROM
				ignora_panico
			WHERE
				igpconoid = ".$connumero."
				AND igpveioid = ".$veioid."
		";

		$rs = $this->query($sql);

		if ($rs) {
			return true;
		}

		return false;
	}

	/**
	 * Desconsiderar pelo status do contrato
	 * @param int $concsioid
	 * @return boolean
	 */
	private function isDescartaStatusContrato($concsioid) {

		$paramStatusContrato = (array) $this->param->pupcsioid;

		if ($concsioid == '' || !count($paramStatusContrato)) {
			return false;
		}

		return in_array($concsioid, $paramStatusContrato);
	}

	/**
	 * Desconsiderar Pânicos com suspensão de Pânico GSM
	 * @param int $equoid
	 * @return boolean
	 */
	private function isDescartaSuspensaoPanicoGsm($equoid) {

		$paramPossuiSuspensao = (boolean) $this->param->puppossui_suspensao;

		if (!$paramPossuiSuspensao) {
			return false;
		}

		$sql = "
			SELECT 	COUNT(1) AS total
			FROM 	suspensao_panico
			WHERE 	spaequoid = ".$equoid."
			AND  	spaexclusao IS NULL
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$total = isset($row->total) ? $row->total : 0;

		return (boolean) $total;
	}

	/**
	 * Descartar Pânicos onde o contrato não possui botão de pânico instalado
	 * @param int $connumero
	 * @param int $veioid
	 * @return boolean
	 */
	private function isDescartaPanicoNaoInstalado($connumero, $veioid){

		$sql = "
			SELECT
				CASE
					WHEN conporta_panico IS NULL THEN
						0
					ELSE
						1
				END	AS conporta_panico
			FROM	contrato
			WHERE 	connumero = ".$connumero."
			AND 	conveioid = ".$veioid."
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$conporta_panico = isset($row->conporta_panico) ? $row->conporta_panico : 0;

		$conporta_panico = (boolean) $conporta_panico;
		if ($conporta_panico) {
			return false;
		}

		return true;
	}

	/**
	 * Descarte pela porta do botão do Pânico
	 * @param integer $connumero
	 * @param integer $veioid
	 * @return boolean
	 */
	private function isDescartaBotaoDePanico($connumero, $veioid) {

		$paramBotaoPanico = (array) $this->param->pupporta_panico;

		$sql = "
			SELECT
				conporta_panico AS porta_panico
			FROM
				contrato
			WHERE
				connumero =  ".$connumero."
				AND conveioid = ".$veioid."
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		if ($row->porta_panico === '' && in_array(99, $paramBotaoPanico)) {
			return true;
		}

		if (!in_array($row->porta_panico, $paramBotaoPanico)) {
			return false;
		}
		return true;
	}

	/**
	 * Descarte de Pânico de Teste Cliente
	 * @param int $connumero
	 * @param int $veioid
	 * @return boolean
	 */
	private function isDescartaTesteCliente($connumero, $veioid){

		$sql = "
			SELECT	 	COUNT(1) AS total
			FROM 		panicos_pendentes pp
			INNER JOIN 	ignora_panico ip ON pp.papconnumero = ip.igpconoid
										AND pp.papveioid = ip.igpveioid
			WHERE 		ip.igpconoid = ". $connumero ."
			AND			ip.igpveioid = ". $veioid ."
			AND 		ip.igptipo_descarte = 1
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$total = isset($row->total) ? $row->total : 0;

		return (boolean) $total;
	}

	/**
	 * Descarte por período de manutenção/lava-car
	 * @param int $connumero
	 * @param int $veioid
	 * @return boolean
	 */
	private function isDescartaManutencaoLavacar($connumero, $veioid) {

		$sql = "
			SELECT	 	igpdt_inicio_lavacar AS ini, igpdt_fim_lavacar AS fim
			FROM 		panicos_pendentes pp
			INNER JOIN 	ignora_panico ip ON pp.papconnumero = ip.igpconoid
										AND pp.papveioid = ip.igpveioid
			WHERE 		ip.igpconoid = ". $connumero ."
			AND			ip.igpveioid = ". $veioid ."
			AND 		ip.igptipo_descarte = 2
		";
		$rs = $this->query($sql);

		if (pg_num_rows($rs) > 0) {

			$rowLavacar = $this->fetchObject($rs);

			if ($rowLavacar->ini == '' || $rowLavacar->fim == '') {
				return false;
			}

			$ini = date('YmdHis', strtotime($rowLavacar->ini));
			$fim = date('YmdHis', strtotime($rowLavacar->fim));

			try {

				$sqlPosicoes = "
					SELECT	 	COUNT(1) AS TOTAL
					FROM 		SASCAR.posicao
					WHERE 		posicao.POSVEIOID = ". $veioid ."
					AND 		TO_CHAR(posicao.POSDATAHORA,'YYYYMMDDHH24MISS') BETWEEN '". $ini ."' AND '". $fim ."'
				";

				$rowPosicoes = $this->fetchObjectOci($sqlPosicoes);
				$totalPosicoesVelocidade = isset($rowPosicoes->TOTAL) ? $rowPosicoes->TOTAL : 0;

				if ($totalPosicoesVelocidade > 0) {

					$sqlVelocidade = "
						SELECT	 	COUNT(1) AS TOTAL
						FROM 		SASCAR.posicao
						WHERE 		posicao.POSVEIOID = ". $veioid ."
						AND 		TO_CHAR(posicao.POSDATAHORA,'YYYYMMDDHH24MISS') BETWEEN '". $ini ."' AND '". $fim ."'
						AND 		posicao.POSGPS_VELOCIDADE > ".self::VELOCIDADE_MAXIMA_LAVACAR."
					";

					$rowVelocidade = $this->fetchObjectOci($sqlVelocidade);
					$totalVelocidadeAcimaPermitido = isset($rowVelocidade->TOTAL) ? $rowVelocidade->TOTAL : 0;

					if ($totalVelocidadeAcimaPermitido == 0) {
						return true;
					}
				}

			} catch (Exception $e) {
				throw new Exception('DescartaManutencaoLavacar: (' . $e->getMessage() . ')');
			}
		}

		return false;
	}

	private function fetchObjectOci($sql) {

		global $ura_oci_user;
		global $ura_oci_pass;
		global $ura_oci_bd;

		$conn_oracle = oci_connect($ura_oci_user, $ura_oci_pass, $ura_oci_bd);
		if(!$conn_oracle) {
			$e = oci_error();

			throw new Exception($e['message']);
		}

		$stid = oci_parse($conn_oracle, $sql);
		if (!$stid) {
			throw new Exception('Falha oci_parse: ' . $sql);
		}

		$stex = oci_execute($stid);
		if (!$stex) {
			throw new Exception('Falha oci_execute: ' . $sql);
		}

		$row = oci_fetch_object($stid);

		oci_free_statement($stid);
		oci_close($conn_oracle);

		return $row;
	}

	/**
	 * Monta a observação para histórico
	 * @param UraAtivaContratoVO $panico
	 * @param string $motivo
	 * @return string
	 */
	private function montarObservacaoHistorico(UraAtivaContratoVO $panico, $motivo){

		$paphorario 	= $panico->paphorario;
		$papveiplaca 	= $panico->papveiplaca;
		$papveioid 		= $panico->papveioid;

		$ultimaLocalizacao = $this->buscarUltimaLocalizaoDataVeiculo($papveioid);

		$coordX = $ultimaLocalizacao['coordenada_x'];
		$coordY = $ultimaLocalizacao['coordenada_y'];

		$localizacao = $this->buscarLocalizacaoCoordenadas($coordX, $coordY);

		$obs =  "Atendimento automático de Pânicos\n";
		$obs .= "Placa: ".$papveiplaca."\n";
		$obs .= "Data/hora pânico: ".date('d/m/Y H:i', strtotime($paphorario))."\n";
		$obs .= "Pânico desconsiderado - Motivo: ". $motivo ."\n";
		$obs .= "Data/Hora posição equipamento: ". substr($ultimaLocalizacao['data_hora'], 0, 17) . "\n";
		$obs .= "Última localização: " . $localizacao;

		return $obs;
	}

	/**
	 * Buscar OID do motivo
	 * @param string $motivo
	 * @return int
	 */
	private function buscarMotivosDescarte($motivo){

		$motivo = pg_escape_string($motivo);
		$motivo_pai = 'P_nico%Descarte';

		$sql = "
			SELECT 	atmoid
			FROM	atendimento_motivo
			WHERE	atmtipo_motivo = 1
			AND 	atmexclusao IS NULL
			AND 	atmoid_pai IN (
							SELECT atmoid
							FROM 	atendimento_motivo
							WHERE 	TRIM(atmdescricao) ILIKE '$motivo_pai'
							AND atmtipo_motivo = 1
							AND atmexclusao IS NULL
							LIMIT 1)
			AND 	TRIM(atmdescricao) ILIKE '$motivo'
			LIMIT 1
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$atmoid = isset($row->atmoid) ? $row->atmoid : 0;

		return $atmoid;

	}

	/**
	 * Busca o ID do defeito alegado a partir da descrição.
	 * @param string $descricao Descrição do defeito alegado que será buscado na base.
	 * @return number
	 */
	private function buscarDefeitoID($descricao){

		$motivo = pg_escape_string($descricao);

		$sql = "
			SELECT
				*
			FROM
				os_tipo_defeito
			WHERE
				otddescricao ILIKE '$descricao'
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$otdoid = isset($row->otdoid) ? $row->otdoid : 0;

		return $otdoid;

	}

	/**
	 * Realiza tratamentos necessários para o Panico
	 * @param UraAtivaContratoVO $contrato
	 * @return void
	 */
	protected function tratar(UraAtivaContratoVO $panico) {

 		$this->assumirPanico($panico->codigo);

	}

	/**
	 * Busca os telefones para contato com o cliente
	 * @param UraAtivaContratoVO $contrato
	 * @return array:UraAtivaContatoVo
	 */
	public function buscarTelefones(UraAtivaContratoVO $contrato) {

		$telefones = $this->buscarTelefonesGerenciadora($contrato->connumero);

		if (!count($telefones)) {
			$telefones = $this->buscarTelefonesEmergencia($contrato->connumero);
		}

		return $telefones;
	}

	/**
	 * Ligar para gerenciadora nos telefones cadastrados no cadastro gerenciadora quando o contrato possua gerenciadora,
	 * seguindo a ordem de cadastro do primeiro para o último (Gerenciadora 1, Gerenciadora 2 e Gerenciadora 3)
	 * @param int $connumero
	 * @return array:UraAtivaContatoVo
	 */
	public function buscarTelefonesGerenciadora($connumero) {

		$sql = "
			SELECT 		'1' AS tipo,
						CONCAT(1, geroid) AS id_telefone_externo,
						conveioid AS id_contato_externo,
						congconnumero AS connumero,
						gernome AS nome,
						gerfone AS telefone,
						gerfone2 AS telefone2,
						gerfone3 AS telefone3
			FROM 		contrato_gerenciadora
			INNER JOIN  contrato ON congconnumero = connumero
			LEFT JOIN 	gerenciadora ON ((conggeroid1 = geroid) or (conggeroid2 = geroid) or (conggeroid3 = geroid))
			WHERE 		gerexclusao IS NULL
			AND 		(conggeroid1 IS NOT NULL OR conggeroid2 IS NOT NULL OR conggeroid3 IS NOT NULL)
			AND 		congconnumero = $connumero
			ORDER BY 	gercadastro";

		$rs = $this->query($sql);

		$telefones = array();
		$telefonesDoContato = array();
		$telefonesInclusos = array();
		while ($row = pg_fetch_object($rs)) {

			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->telefone);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->telefone2);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->telefone3);

			unset($row->telefone);
			unset($row->telefone2);
			unset($row->telefone3);

			foreach ($telefonesDoContato as $telefone) {

				// se não é vazio e ainda não foi incluso para o mesmo contato
				if (!empty($telefone) and !in_array($telefone, $telefonesInclusos)) {

					$row->telefone = '0' . $telefone;
					$row->id_telefone_externo = $this->sequencialTelefone;//.$row->id_telefone_externo;
					$telefonesInclusos[] = $telefone;

					$telefones[] = new UraAtivaContatoVO($row);
					$this->sequencialTelefone++;
				}
			}

			unset($telefonesDoContato);
		}

		unset($telefonesInclusos);

		return $telefones;
	}

	/**
	 * Ligar para o cliente somente nos telefones cadastrados no campo de contato de emergência quando o contrato não possua gerenciadora,
	 * seguindo a ordem de cadastro do primeiro para o último
	 * @param int $connumero
	 * @return array:UraAtivaContatoVo
	 */
	public function buscarTelefonesEmergencia($connumero) {

		$sql = "
			SELECT 		'2' AS tipo, tctconnumero AS connumero, tctcontato AS nome,
						CONCAT(2, tctoid) AS id_telefone_externo,
						conveioid AS id_contato_externo,
						(tctno_ddd_res || tctno_fone_res) AS res,
						(tctno_ddd_com || tctno_fone_com) AS com,
						(tctno_ddd_cel || tctno_fone_cel) AS cel
			FROM 		telefone_contato
			INNER JOIN  contrato ON tctconnumero = connumero
			WHERE  		tctorigem = 'E'
			AND 		tctconnumero = $connumero
			ORDER BY 	tctdt_cadastro";

		$rs = $this->query($sql);

		$telefones = array();
		$telefonesDoContato = array();
		$telefonesInclusos = array();
		while ($row = pg_fetch_object($rs)) {

			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->res);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->com);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->cel);

			unset($row->res);
			unset($row->com);
			unset($row->cel);

			foreach ($telefonesDoContato as $telefone) {

				// se não é vazio e ainda não foi incluso para o mesmo contato
				if (!empty($telefone) and !in_array($telefone, $telefonesInclusos)) {

					$row->telefone = '0' . $telefone;
					$row->id_telefone_externo = $this->sequencialTelefone;//.$row->id_telefone_externo;
					$telefonesInclusos[] = $telefone;

					$telefones[] = new UraAtivaContatoVO($row);
					$this->sequencialTelefone++;
				}
			}

			unset($telefonesDoContato);
		}

		unset($telefonesInclusos);

		return $telefones;
	}

	/**
	 * Busca os telefones celular do cliente
	 */
	public function buscarTelefonesCelularEmergencia($connumero) {

		$sql = "
			SELECT
				REGEXP_REPLACE((tctno_ddd_cel || tctno_fone_cel), '[^0-9]', '', 'gi') AS celular
			FROM
				telefone_contato
			WHERE
				tctorigem = 'E'
			AND
				tctconnumero = $connumero
			AND
				tctno_ddd_cel IS NOT NULL AND tctno_fone_cel IS NOT NULL
			AND
				trim(tctno_ddd_cel) <> '' AND trim(tctno_fone_cel) <> ''
			AND
				REGEXP_REPLACE(tctno_ddd_cel, '[^0-9]', '', 'gi')::INT > 0
			AND
				REGEXP_REPLACE(tctno_fone_cel, '[^0-9]', '', 'gi')::BIGINT > 0
			ORDER BY
				telefone_contato.oid";

		$rs = $this->query($sql);

		$row = array();

		if(pg_num_rows($rs) > 0){

			$row = pg_fetch_all($rs);

		}

		return $row;
	}

	/**
	 * Assume a reponsabilidade pelo tratamento do panico
	 * @param int $papoid OID do panico
	 * @return boolean
	 */
	private function assumirPanico($papoid) {

		$usuoid = $this->usuoid;
		$login = $this->login;
		$ramal = $this->ramal;

		$sql = "SELECT central_atende(".$usuoid.", '".$login."', ".$ramal.", ".$papoid.") AS retorno;";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$retorno = isset($row->retorno) ? $row->retorno : 0;

		return (boolean) $retorno;
	}

	/**
	 * Exclui um panico já tratado
	 * @param int $papoid OID do panico
	 * @param int $motivo
	 * @return boolean
	 */
	public function excluirPanico($papoid, $atmoid) {

		$usuoid = $this->usuoid;
		$ramal = $this->ramal;

		$sql = "SELECT panico_d(".$papoid.",".$usuoid.",".$ramal.",".$atmoid.") AS retorno;";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$retorno = isset($row->retorno) ? $row->retorno : 0;

		return ($retorno == 1);
	}

	/**
	 * Abre um atendimento, sobrescreve o método principal porque precisa do OID do panico
	 * @param int $clioid
	 * @param int $depoid
	 * @param int $atmoid
	 * @param int $panoid
	 * @return boolean
	 */
	public function abrirAtendimento($clioid, $depoid, $atmoid, $panoid) {

		$sql = "
			SELECT atendimento_cliente_i(
				  ".$this->usuoid." 								-- atcusuoid
				, ".$clioid." 										-- atcclioid
				, 0 												-- atcprotoid - não tem protocolo
				, '' 												-- atcprotocolo - não tem protocolo
				, ".$depoid." 										-- atcdepoid
				, ".$atmoid." 										-- atcatmoid
				, ".$panoid." 										-- panoid
		) AS retorno;";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$retorno = isset($row->retorno) ? $row->retorno : 0;

		return $retorno;


	}

	public function limpar() {
		return $this->limparIgnoraPanico();
	}

	/**
	 * Deleta os panicos já vencidos
	 * @return boolean
	 */
	public function limparIgnoraPanico() {

		$sql = "
			DELETE FROM ignora_panico
			WHERE
			(
				(
					(igptipo_descarte = 1)  -- panico teste
					AND ((NOW() - igpdt_insercao) > INTERVAL ' ".UraAtivaPanicoDAO::MINUTOS_LIMPA_TESTE." MINUTES')
				)
				OR
				(
					(igptipo_descarte = 2) -- panico lavacar
					AND igpdt_fim_lavacar <= NOW()::timestamp
          			AND NOT EXISTS (SELECT papveioid FROM panicos_pendentes WHERE papveioid = ignora_panico.igpveioid AND papconnumero = ignora_panico.igpconoid)
				)
			)
		";

		$rs = $this->query($sql);

		if ($rs) {
			return true;
		}

		return false;
	}

	/**
	 * Busca informações complementares para envio para a URA
	 * @param float $oid
	 * @return objet $row
	 */
 	public function buscarPanicoPendente($oid){

 		$oid = !empty($oid) ? (int) $oid : 0;

 		$sql = "
 				SELECT
 						panicos_pendentes.oid::int4 as panico_pendente_oid,
 						papveiplaca AS placa,
						papclinome AS contratante,
						papconclioid as clioid,
						TO_CHAR(paphorario, 'HH24:MI') AS horario,
						papconnumero as contrato,
						papdatapacote AS data_pacote ,
						papveiplaca AS placa,
						papveioid as veioid
				FROM
						panicos_pendentes
				WHERE
						panicos_pendentes.oid::int4 =  $oid
				LIMIT 1
			";

 		$rs = $this->query($sql);

 		$row = pg_fetch_object($rs);

 		return $row;

 	}


 	/**
 	 * Excluir registro de panico auxiliar
 	 */
 	public function excluirPanicoAuxiliar($atcoid){

 		$sql = "
				DELETE FROM
					contato_discador_ura_panico
				WHERE
					cdupatcoid = $atcoid";

		$this->query($sql);

 	}

 	/**
 	 * Excluir registro de panico auxiliar pelo veioid
 	 */
 	public function excluirPanicoAuxiliarByPapoid($papoid){

 		$sql = "
	 		DELETE FROM
	 			contato_discador_ura_panico
	 		WHERE
	 			cdupid_panico = $papoid";

 		$this->query($sql);

 	}

 	/**
 	 * Excluir registro de panico auxiliar pelo veioid
 	 */
 	public function excluirPanicoAuxiliarByVeioid($veioid){

 		$sql = "
 		DELETE FROM
 			contato_discador_ura_panico
 		WHERE
 			cdupveioid = $veioid
 		";

 		$this->query($sql);
 	}

 	/**
 	 * Inserir log envio de SMS
 	 */
 	public function inserirLogEnvioSMS($connumero, $clioid, $veioid){

 		$sql = "
	 		INSERT INTO
		 		ligacoes_discador (
			 		ligddt_cadastro,
			 		ligdtipo,
			 		ligdconnumero,
			 		ligdclioid,
			 		ligdveioid,
		 			ligdordoid,
		 			ligddt_ligacao,
		 			ligdldsoid,
		 			ligdcampanha,
		 			ligddt_envioemail,
		 			ligdtipo_envioemail,
		 			ligddt_enviosms,
		 			ligdtipo_enviosms,
		 			ligdenvio_atendimento
	 		)
	 		VALUES (
		 		NOW(),
		 		'C',
		 		$connumero,
		 		$clioid,
		 		$veioid,
		 		NULL,
		 		NOW(),
		 		7,
		 		'Pânico',
		 		NULL,
		 		NULL,
		 		NOW(),
		 		'SMS',
		 		TRUE
	 		)";

 		$this->query($sql);
 	}

	/**
	 * Busca o motivo do atendimento vinculado ao atendimento cliente
	 * @param int $atcoid
	 * @return int $atmoid
	 */
	public function buscarMotivoAtendimento($atcoid){

		$sql = "
			SELECT
				atcatmoid AS atmoid
			FROM
				atendimento_cliente
			WHERE
				atcoid = $atcoid";

		$rs = $this->query($sql);

		$row = pg_fetch_object($rs);

		$atmoid = "";

		if($row){
			$atmoid = $row->atmoid;
		}

		return $atmoid;
	}


	/**
	 * Desconsiderar por Ordens de Serviço com parametros fixos.
	 * @param int $connumero
	 * @param array $paramTiposOS
	 * @param array $paramItensOS
	 * @param array $paramStatusOS
	 * @param array $paramDefeitosAlegados
	 * @return boolean
	 */
	protected function isDescartaOrdemServicoFixo($connumero, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados=array()) {

		if ((!count($paramTiposOS)) && (!count($paramItensOS)) && (!count($paramStatusOS)) && (!count($paramDefeitosAlegados))) {
			return false;
		}

		$listaOrdemServico = array();

		// Busca ordens de serviço do contrato
		$sql = "
			SELECT
					ordoid
			FROM
					ordem_servico
			WHERE
					ordconnumero = ".$connumero."
		";

		$rs = $this->query($sql);
		$listaOrdemServico = $this->fetchObjects($rs);

		// Verifica se alguma das ordens de serviço ocasionará o descarte
		foreach($listaOrdemServico as $ordemServico) {

			//Verificar a quantidade de itens que devem ser descartados
			$sql = "
				SELECT 	COUNT(1) as qtd
				FROM 	ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item	ON otioid =  ositotioid
				INNER JOIN os_tipo ON otiostoid = ostoid
				LEFT JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
				LEFT JOIN os_tipo_defeito ON otdoid= osdfotdoid
				WHERE 	ordoid = ".$ordemServico->ordoid. "
			";

			if (count($paramTiposOS)) {
				$sql .= " AND (ostoid IS NOT NULL AND ostoid IN (".implode(',', $paramTiposOS).")) ";
			}
			if (count($paramItensOS)) {
				$sql .= " AND (otitipo IS NOT NULL AND otitipo IN (".$this->buildInSQL($paramItensOS).")) ";
			}
			if (count($paramDefeitosAlegados)) {
				$sql .= " AND (otdoid IS NOT NULL AND otdoid IN (".implode(',', $paramDefeitosAlegados)."))";
			}
			if (count($paramStatusOS)){
				$sql .= "AND ordstatus IN (".implode(',', $paramStatusOS).")";
			}

			$rs = $this->query($sql);
			$row = $this->fetchObject($rs);
			$qtdItensDescartar = isset($row->qtd) ? $row->qtd : 0;

			if ($qtdItensDescartar > 0) {
				return true;
			}
		}

		return false;
	}

	public function tratarInsucessos($insucessos){

		//mudado para a Action. Mantido aqui para compatibilidade com método abstrato.

	}

    /**
     * Busca o código do atendimento do pânico
     * @param int $veioid
     * @return  objeto $row
     */
	public function buscarAtendimentoPanico($veioid){

		$sql = "
			SELECT 		pxa_oidatpen AS atcoid
						,panicos_pendentes.oid::int4 AS papoid
						,SUBSTRING(papdatapacote FROM 0 FOR 17) as data_pacote
						,papveiplaca AS placa
						,papveioid AS veioid
						,papconclioid AS clioid
			FROM 		panicoxatend
			INNER JOIN	panicos_pendentes ON panicos_pendentes.oid::int4 = pxa_oidpanico
			WHERE		papveioid =  ".$veioid."
			LIMIT 1
		";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);

		return $row;

	}

	/**
	 * Busca por atendimentos de Pânico pendentes a mais de uma hora
	 * @param int $atmoid
	 * @return objeto $atendimentos
	 */
	public function buscarAtendimentoPendente($atmoid){

		$atendimentos = array();
		$k = 0;

		 $sql = "
    			SELECT 		pxa_oidatpen AS atcoid
			    			,panicos_pendentes.oid::int4 as papoid
							,papveioid AS veioid
    			FROM 		panicoxatend
    			INNER JOIN	panicos_pendentes ON panicos_pendentes.oid::int4 = pxa_oidpanico
    			INNER JOIN	atendimento_cliente ON atcoid = pxa_oidatpen
    			INNER JOIN	atendimento_acesso ON ataatcoid = atcoid
    			WHERE		ataatmoid = ".$atmoid."
    			AND			atadt_fim is null
    			AND 		(NOW() - atadt_inicio)::interval > (interval '1 HOURS')
    	";

		$rs = $this->query($sql);

		while($row = pg_fetch_object($rs)){

			$atendimentos[$k]['atcoid'] = isset($row->atcoid) ? $row->atcoid : 0;
			$atendimentos[$k]['papoid'] = isset($row->papoid) ? $row->papoid : 0;
			$atendimentos[$k]['veioid'] = isset($row->veioid) ? $row->veioid : 0;
			$k++;
		}

		return $atendimentos;

	}

	/**
	 * Tratamentos antes do envio ao discador
	 * @param UraAtivaContratoVO $Contrato
	 * @param int $codigoIdentificador
	 */
	private function tratarAtendimentoPanico(UraAtivaContratoVO $Contrato, $codigoIdentificador){

		$isAbriuAtendimento = (boolean) $this->verificaExistenciaAtendimentoPanico($Contrato->conveioid);


		if (!$isAbriuAtendimento) {

			$atcoid = $this->abrirAtendimento(
					$Contrato->conclioid,
					$this->depoid,
					UraAtivaDAO::OID_MOTIVO_INICIAL,
					$codigoIdentificador // somente para processo com sobrecarga especifica
			);


			$this->inserirAcesso($Contrato, $atcoid, UraAtivaDAO::OID_MOTIVO_INICIAL);
		}

	}

	/**
	 * Busca dados dos contatos de panico na tabela temporaria
	 * @return array $contatos
	 */
	public function buscarContatosDiscadorPanico(){

		$contatos = array();
		$k = 0;

		$sql ="
    			SELECT DISTINCT ON (cdupveioid) cdupveioid AS veioid
    			,cdupid_contato_discador AS id_contato
    			FROM  contato_discador_ura_panico
    			";

		$rs = $this->query($sql);

		while($row = pg_fetch_object($rs)){

			$contatos[$k]['veioid'] 	= isset($row->veioid) ? $row->veioid : 0;
			$contatos[$k]['id_contato'] = isset($row->id_contato) ? $row->id_contato : '';
			$k++;
		}

		return $contatos;

	}

	/**
	 * Verificar se já existe atendimento pendente para o panico
	 * @param int $veioid
	 * @return boolean
	 */
	public function verificaExistenciaAtendimentoPanico($veioid){

		$veioid = (int)$veioid;

		if(empty($veioid)){
			return false;
		}

		$sql = "
				SELECT EXISTS
						(
							SELECT
									1
							FROM
									panicoxatend
							INNER JOIN
									panicos_pendentes ON panicoxatend.pxa_oidpanico = panicos_pendentes.oid::int4
							INNER JOIN
									atendimento_cliente ON atcoid = pxa_oidatpen
							WHERE
									papveioid = ".  $veioid ."
							AND
									atcdt_fim IS NULL
							AND
								atcatmoid IN (
												SELECT
													atmoid
												FROM
													atendimento_motivo
												WHERE
													atmexclusao IS NULL
												AND
													(
														TRIM(atmdescricao) ILIKE 'Pendente%(%P_nico%)'
														OR TRIM(atmdescricao) ILIKE 'P_nico%Ura%ativa'
														OR TRIM(atmdescricao) ILIKE 'Acionamento%Veículo%Roubado'
														OR TRIM(atmdescricao) ILIKE 'Precisa%verificar%retorna%contato'

													)
												AND (
													atmagroid = (
																	SELECT
																			agroid
																	FROM
																			atendimento_grupo
																	WHERE
																			TRIM(agrdescricao) ILIKE  'Atendimento%P_nico%Alerta%Cerca'
																	AND
																			agrexclusao IS NULL
																	LIMIT 1
																)
													OR
														atmoid_pai IN (
																		SELECT
																				atmoid
																		FROM
																				atendimento_motivo
																		INNER JOIN
																				atendimento_grupo ON agroid = atmagroid
																		WHERE
																				TRIM(agrdescricao) ILIKE 'Atendimento%P_nico%Alerta%Cerca'
																		)
													)
												OR TRIM(atmdescricao) ILIKE 'In_cio%Atendimento%P_nico'
											)
					) AS existe";



		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$existe = false;

		if (isset($row->existe) && $row->existe == 't') {
			$existe = true;
		}

		return $existe;
	}

	/**
	 *
	 */
	protected function afterInserirDiscador($contato, $contrato, $idDiscador, $codigoIdentificador){

		 if(!$this->verificaRegistroTabelaAuxiliar($contrato->conveioid)){

	    	$this->inserirRegistroAuxiliarDiscador($contato, $contrato, $idDiscador, $codigoIdentificador);

	    	$contrato->codigo = $codigoIdentificador;

	    	$this->tratar($contrato);

		}

		if($this->cronReenvio){

			$contrato->codigo = $codigoIdentificador;

			$this->tratar($contrato);

			$this->atualizarPanicoAuxiliar($contrato->conveioid, $idDiscador);

			$this->atualizarDataInsucessoContato($contrato->conveioid, $contato);

			$this->tratarDataTentativaInsucessoContato( $contrato->conveioid);

		}
		else if(!$this->verificarExistenciaTelefoneContato($contato->telefone,  $contrato->conveioid)){

			$this->inserirInsucessoContato( $contrato->conveioid, $contato);
		}

	}

	/**
	 * Atualiza a id do contado no discador do veículo
	 * @param int $veioid
	 * @param int $idDiscador
	 * @return boolean
	 */
	private function atualizarPanicoAuxiliar($veioid, $idDiscador) {

		$veioid = (int)$veioid;
		$idDiscador = (int)$idDiscador;

		$sql = "
				UPDATE contato_discador_ura_panico
				SET
					cdupid_contato_discador = ".$idDiscador."
				WHERE
					cdupveioid = ".$veioid."
		";

		$rs = $this->query($sql);

		if(!pg_affected_rows($rs)) {
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	public function desconsiderar($veioid){

		return false; // Removido por solicitação funcional

		$existe_atendimento = $this->verificaExistenciaAtendimentoPanico($veioid);

		return $existe_atendimento;
	}

	public function verificaRegistroTabelaAuxiliar($veioid){

		$veioid = isset($veioid) ? $veioid : 0;

		$sql = "SELECT * FROM contato_discador_ura_panico WHERE cdupveioid = $veioid";
		$rs = $this->query($sql);

		if(pg_num_rows($rs) > 0){
			return true;
		}

		return false;
	}

	/**
	 * Busca contato específico na tabela auxiliar do discador
	 * @param integer $idregistro
	 * @return integer
	 */
	public function buscarContatoDiscadorEspecifico($idregistro){

		$idregistro = (int)$idregistro;

		$sql = "SELECT cdupid_contato_discador AS id_contato
				FROM  contato_discador_ura_panico
				WHERE cdupoid = $idregistro
				LIMIT 1";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);

		$id_contato = isset($row->id_contato) ? $row->id_contato : 0;

		return $id_contato;
	}

	/**
	 * Busca o número de tentativas para contatos pânicos
	 * @param integer $idPanico
	 * @throws Exception
	 * @return number
	 */
	public  function buscarNumeroTentativas($idPanico) {

		if (empty($idPanico)) {
			throw new Exception("Pânico não informado para buscar o número de tentativas");
		}

		$sql = "
		SELECT
			cdupnum_tentativas AS num_tentativas
		FROM
			contato_discador_ura_panico
		WHERE
			cdupid_panico = $idPanico
		";

		$rs = $this->query($sql);

		if (!$rs) {
			throw new Exception("Erro ao buscar o número de tentativas de contato para Pânico");
		}

		if ($this->numRows($rs) > 0) {

			$row = $this->fetchObject($rs);

			return $row->num_tentativas;

		} else {
			return 0;
		}
	}

	/**
	 *
	 * @param integer $idContato
	 * @throws Exception
	 * @return AtendimentoVO|boolean
	 */
	public function buscaPanicoByIdContato($idContato) {

		if (empty($idContato)) {
			throw new Exception("Id do contato não informado para a busca do Pânico");
		}

		$sql = "
		SELECT
			panicos_pendentes.oid 		AS id_bigint,
			panicos_pendentes.oid::int4 AS id_int4,
			panicos_pendentes.*
		FROM
			contato_discador_ura_panico
			INNER JOIN panicos_pendentes ON panicos_pendentes.oid::int4 = cdupid_panico
		WHERE
			cdupid_contato_discador = $idContato
		";

		$rs = $this->query($sql);

		if (!$rs) {
			throw new Exception("Erro ao buscar Pânico");
		}

		if ($this->numRows($rs) > 0) {

			$row = $this->fetchObject($rs);

			return $row;

		} else {
			return false;
		}
	}

	/**
	 *
	 * @param integer $idContato
	 * @throws Exception
	 * @return boolean
	 */
	public function buscaPanicoById($id) {

		if (empty($id)) {
			throw new Exception("Id não informado para a busca do Pânico");
		}

		$sql = "
		SELECT
			panicos_pendentes.oid 		AS id_bigint,
			panicos_pendentes.oid::int4 AS id_int4,
			panicos_pendentes.*
		FROM
			panicos_pendentes
		WHERE
			panicos_pendentes.oid = $id
		";

		$rs = $this->query($sql);

		if (!$rs) {
			throw new Exception("Erro ao buscar Pânico");
		}

		if ($this->numRows($rs) > 0) {

			$row = $this->fetchObject($rs);

			return $row;

		} else {
			return false;
		}
	}


	/**
	 * Busca a última localização e data do veículo
	 * @see buscarUltimaLocalizaoVeiculo
	 * @param int $veioid
	 * @return array
	 */
	private function buscarUltimaLocalizaoDataVeiculo($veioid){

		$retorno = array();

		if (empty($veioid)) {
			throw new Exception("Veículo não informado ao buscar localização no Oracle.");
		}

		$localizacao = '';

		//Obs: Subtrai o intervalo de 03 horas devido a ajuste do GMT.
		$sql = "
			SELECT
			  		(s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.X) AS X,
			  		(s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.Y) AS Y,
					TO_CHAR(s.UPOSDATAHORA - (INTERVAL '3' HOUR),'dd/mm/YYYY hh24:mi:ss' ) AS DATA_HORA
			FROM
			 		sascar.ultposicao s
			WHERE
			  		s.UPOSVEIOID = ". $veioid ."
		";

		$row = $this->fetchObjectOci($sql);

		$retorno['coordenada_x'] 	= isset($row->X) ? str_replace(',', '.', $row->X) : '';
		$retorno['coordenada_y']	= isset($row->Y) ? str_replace(',', '.', $row->Y) : '';
		$retorno['data_hora'] 		= isset($row->DATA_HORA) ? $row->DATA_HORA : '';

		return $retorno;
	}


	/**
	 * Busca o endereço da localização do veículo pelas coordenadas
	 * @param string $coordX
	 * @param string $coordY
	 * @throws Exception
	 * @return string
	 */
	private function buscarLocalizacaoCoordenadas($coordX, $coordY){

		$coordX = trim($coordX);
		$coordY = trim($coordY);

		$urlConsulta = _URL_GOOGLE_MAPS_ . "?x=$coordX&y=$coordY&type=xml";

		$retornoUrl = file_get_contents($urlConsulta);

        if(!$retornoUrl) {
        	throw new Exception('Falha ao realizar consulta da localização.');
		}

		$doc = new DOMDocument();

		if(!$doc->loadXML($retornoUrl, LIBXML_NOERROR)){

			$localizacao = "Localização não encontrada com as coordenadas: " . $coordX . " / " . $coordY;

			return $localizacao;
		}

		$AddressLocation = $doc->getElementsByTagName( "AddressLocation" );

		foreach($AddressLocation as $nodoAddressLocation){

			$address = $nodoAddressLocation->getElementsByTagName('address');

			foreach ($address as $nodoAddress){
				$street = $nodoAddress->getElementsByTagName('street');
				$rua = utf8_decode($street->item(0)->nodeValue);

				$name = $nodoAddress->getElementsByTagName('name');
				$cidade = utf8_decode($name->item(0)->nodeValue);

				$state = $nodoAddress->getElementsByTagName('state');
				$estado = $state->item(0)->nodeValue;

				$country = $nodoAddress->getElementsByTagName('country');
				$pais = $country->item(0)->nodeValue;

			}

		}

		$localizacao = ($rua . " - " . $cidade . " - " . $estado  . " - " .  $pais);

		return $localizacao;

	}

	/**
	 * Verifica se o contrato possui Gerenciadora
	 * @param int $connumero
	 * @return boolean
	 */
	private function verificarContratoGerenciadora($connumero){

		$paramPossuiGerenciadora = (boolean) $this->param->puppossui_gerenciadora;

		if (!$paramPossuiGerenciadora) {
			return false;
		}

		if (empty($connumero)) {
			return false;
		}

		$sql = "
			SELECT EXISTS(
							SELECT
									1
							FROM
									contrato_gerenciadora
							WHERE
									congconnumero = ".$connumero."
							AND
									(conggeroid1 IS NOT NULL OR conggeroid2 IS NOT NULL OR conggeroid3 IS NOT NULL)
							) AS existe
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$existe = false;

		if (isset($row->existe) && $row->existe == 't') {
			$existe = true;
		}

		return $existe;

	}

	/**
	 * Verifica se o contato já esta na tabela de controle de Insucessos
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::verificarInsucessoContato()
	 */
	public function verificarInsucessoContato($veioid){

		$existe = false;
    	$veioid = (int)$veioid;

    	$sql = "
    			SELECT EXISTS
    						(
			    			SELECT
			    					cdupaveioid
			    			FROM
			    					contato_discador_ura_panico_aux
			    			WHERE
			    					cdupaveioid = ". $veioid ."
			    			) AS existe
    			";

    	$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		if (isset($row->existe) && $row->existe == 't') {
			$existe = true;
		}

		return $existe;
	}

	/**
	 * Insere um novo contato na tabela de controle de insucessos
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::inserirInsucessoContato()
	 */
	public function inserirInsucessoContato($veioid, UraAtivaContatoVO $contato){

		$veioid = (int)$veioid;
		$retorno = false;

    	if (empty($veioid)) {
    		return $retorno;
    	}

    	$sql = "INSERT INTO
    					contato_discador_ura_panico_aux
    					(
    						cdupaveioid,
    						cdupatelefone,
    						cdupatentativas,
    			 			cdupaid_telefone_externo,
    						cdupatipo_contato
    					)
    			VALUES
    					(
    						". $veioid .",
    						'". $contato->telefone ."',
    						0,
    						" . $contato->id_telefone_externo .",
    						" . $contato->tipo . "
    					)
    			";

    	$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
	}

	/**
	 * Atualiza o numero de tentativas em um contato na tabela de controle de insucessos
	 * @param int $codigoIdentificador
	 * @param string $telefone
	 * @param string $idsTelefone
	 * @return boolean
	 */
	public function atualizarTentativaInsucessoContato($codigoIdentificador, $telefone, $idsTelefone = ''){

		$codigoIdentificador = (int)$codigoIdentificador;
		$retorno = false;

		if (empty($codigoIdentificador)) {
			return $retorno;
		}


		$sql = "UPDATE
    					contato_discador_ura_panico_aux
    			SET
    					cdupatentativas = (cdupatentativas + 1),
						cdupadt_ultima_tentativa = NOW(),
						cdupatratar_insucesso = FALSE,
                        cdupadt_reenvio = NULL
    			WHERE
    					cdupaveioid = ". $codigoIdentificador ."
 			";

		if (!empty($telefone)) {
			$sql.= "
					AND
    					cdupatelefone = '". $telefone ."'
				";
		}
		elseif (!empty($idsTelefone)) {
			$sql.= "
					AND
    					cdupaid_telefone_externo IN (". $idsTelefone .")
				";
		}

		$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;

	}

	/**
	 * Deleta o contato na tabela de controle de insucessos
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::removerInsucessoContato()
	 */
	public function removerInsucessoContato ($codigoIdentificador){

		$retorno = false;
    	$codigoIdentificador = (int)$codigoIdentificador;

    	if (empty($codigoIdentificador)) {
    		return $retorno;
    	}

    	$sql = "DELETE FROM
    					contato_discador_ura_panico_aux
    			WHERE
    					cdupaveioid = ". $codigoIdentificador ."
    			";

    	$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
	}

	/**
	 * verifica a quantidade de tentativas do discador com insucesso
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::verificarTentativasInsucessoContato()
	 */
	public function verificarTentativasInsucessoContato($telefone){
		return true;
	}

	/**
	 *  Busca na tabela auxiliar os contatos que deverão ser reenviados
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::buscarContatosReenvio()
	 */
	public function buscarContatosReenvio(){

		$contatos = array();

		$sql = "
		-----Tabela temporaria
			DROP TABLE IF EXISTS cdupa;

			CREATE TEMP TABLE cdupa AS
			SELECT
				total_tentativas,
				min_tentativas,
				max_tentativas,
				min_dt_ultima_tentativa,
				total_registros,
				cdupaveioid
			FROM
				(SELECT
					SUM(cdupatentativas)  AS total_tentativas,
					MIN(cdupatentativas) AS min_tentativas,
					MAX(cdupatentativas) AS max_tentativas,
					MIN(cdupadt_ultima_tentativa)  AS min_dt_ultima_tentativa,
					COUNT(cdupaveioid) as total_registros,
					cdupaveioid
				FROM contato_discador_ura_panico_aux
				GROUP BY cdupaveioid
				) AS foo;


		---Query principal
			SELECT
				--Calcula o próximo id_telefone_externo
				MAX(cdupa1.cdupaid_telefone_externo) OVER w_cdupa1 + ROW_NUMBER() OVER w_cdupa1 AS id_telefone_externo,
				cdupa1.cdupaveioid AS id_contato_externo,
				cdupa1.cdupatelefone AS telefone,
				cdupa1.cdupatipo_contato AS tipo,
				cdupconnumero AS connumero,
                cdupid_panico
			FROM
				contato_discador_ura_panico_aux cdupa1
			INNER JOIN
				contato_discador_ura_panico ON cdupveioid = cdupa1.cdupaveioid
			INNER JOIN
				cdupa ON cdupa.cdupaveioid =  cdupa1.cdupaveioid
			WHERE
				cdupatentativas <= 2
		-- Não pode reenviar se todos os contatos tem tentativa = 0
			AND
				total_tentativas > 0

		-- verifica se o contato é o menor numero de tentativas
			AND
				cdupa1.cdupatentativas = min_tentativas
            AND
				cdupa1.cdupadt_reenvio IS NULL
			AND
				(
					(
						cdupa1.cdupadt_ultima_tentativa IS NULL
					AND
						cdupa1.cdupadt_reenvio IS NULL
					)
				OR
					(

						CASE WHEN

						(total_registros =

							(SELECT
								count(cdupa2.cdupaveioid)

							FROM
								contato_discador_ura_panico_aux cdupa2
							WHERE
								cdupa2.cdupaveioid = cdupa1.cdupaveioid
							AND
								cdupatentativas = max_tentativas
							))


						THEN
							ROUND((EXTRACT(EPOCH FROM (NOW() - min_dt_ultima_tentativa)))/60)> ". $this::INTERVALO_REENVIO_CONTATO ."

						ELSE
							TRUE

						END
					)
				)
			WINDOW w_cdupa1 AS (PARTITION BY cdupa1.cdupaveioid)
		";

		$rs = $this->query($sql);

		while ($tupla = pg_fetch_object($rs)) {

			$contatos[$tupla->cdupid_panico][] = new UraAtivaContatoVO($tupla);

		}
		//Fim: $tupla

		return $contatos;

	}

	/**
	 * Atualiza a data de envio do contato na tabela de controle de insucessos
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::atualizarDataInsucessoContato()
	 */
	public function atualizarDataInsucessoContato($veioid, $contato){

		$retorno = false;

		if(empty($veioid)) {
			return $retorno;
		}

		$sql = "UPDATE
    					contato_discador_ura_panico_aux
    			SET
    					cdupadt_reenvio = NOW(),
						cdupatratar_insucesso = TRUE,
						cdupaid_telefone_externo = ". $contato->id_telefone_externo ."
    			WHERE
    					cdupaveioid = ".  $veioid ."
    			AND
    					cdupatelefone = '". $contato->telefone ."'
    			";

		$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
	}


	/**
	 * Verifica se o contato já esta na tabela de controle de Insucessos
	 * @param string $telefone
	 * @param int $veioid
	 * @return boolean
	 */
	private function verificarExistenciaTelefoneContato($telefone, $veioid){

		$existe = false;
		$veioid = intval($veioid);

		$sql = "
    			SELECT EXISTS
    						(
			    			SELECT
			    					cdupaveioid
			    			FROM
			    					contato_discador_ura_panico_aux
			    			WHERE
			    					cdupatelefone = '". $telefone ."'
			    			AND
			    					cdupaveioid = ". $veioid ."
			    			) AS existe
    			";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		if (isset($row->existe) && $row->existe == 't') {
			$existe = true;
		}

		return $existe;
	}


	/**
	 * Busca os dados dos contatos na tabela de controle de Insucessos
	 * @param int $veioid
	 * @return array
	 */
	public function buscarInsucessosContato($veioid){

		$retorno = array();
		$veioid  = (int)$veioid;

		if (!$veioid) {
			return $retorno;
		}

		$sql = "
    			SELECT
    					cdupaid_telefone_externo
    			FROM
    					contato_discador_ura_panico_aux
    			WHERE
    					cdupaveioid = ". $veioid ."
			    AND
    					cdupatratar_insucesso = TRUE
    			";

		$rs = $this->query($sql);

		while($row = pg_fetch_object($rs)) {

			$retorno[] = isset($row->cdupaid_telefone_externo) ? $row->cdupaid_telefone_externo : 0;
		}

		return $retorno;
	}


	/**
	 * Seta para NULL a data de reenvio
	 * @param int $veioid
	 * @return boolean
	 */
	public function tratarDataReeenvioInsucessoContato($veioid){

		$retorno = false;
		$veioid  = intval($veioid);

		if(empty($veioid)) {
			return $retorno;
		}


		$sql = "
				UPDATE
					contato_discador_ura_panico_aux cdupa_1
				SET
					cdupadt_reenvio = NULL
				WHERE
					cdupa_1.cdupaoid IN (
											SELECT
												cdupa_2.cdupaoid
											FROM
												(
													SELECT
														cdupa_3.cdupaoid,
														cdupa_3.cdupatentativas,
														MIN(cdupa_3.cdupatentativas) OVER () AS cdupatentativa_minima,
														MAX(cdupa_3.cdupatentativas) OVER () AS cdupatentativa_maxima
													FROM
														contato_discador_ura_panico_aux AS cdupa_3
													WHERE
														cdupa_3.cdupaveioid = ". $veioid ."
												) AS cdupa_2
											WHERE
												cdupa_2.cdupatentativa_minima != cdupa_2.cdupatentativa_maxima
											AND
												cdupa_2.cdupatentativas = cdupa_2.cdupatentativa_minima
										)
    			";

		$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
	}

	/**
	 * Seta para NULL a data de ultima tentativa
	 * @param int $veioid
	 * @return boolean
	 */
	public function tratarDataTentativaInsucessoContato($veioid){

		$retorno = false;
		$veioid  = intval($veioid);

		if(empty($veioid)) {
			return $retorno;
		}


		$sql = "
			UPDATE
				contato_discador_ura_panico_aux cdupa_1
			SET
				cdupadt_ultima_tentativa = NULL
			WHERE
				cdupa_1.cdupaoid IN (
										SELECT
											cdupa_2.cdupaoid
										FROM
											(
												SELECT
													cdupa_3.cdupaoid,
													cdupa_3.cdupatentativas,
													MIN(cdupa_3.cdupatentativas) OVER () AS cdupatentativa_minima,
													MAX(cdupa_3.cdupatentativas) OVER () AS cdupatentativa_maxima
												FROM
													contato_discador_ura_panico_aux AS cdupa_3
												WHERE
													cdupa_3.cdupaveioid = ". $veioid ."
											) AS cdupa_2
										WHERE
											cdupa_2.cdupatentativa_minima = cdupa_2.cdupatentativa_maxima
				)
    			";

		$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
	}


	/**
	 * Busca as informações relativas aos contatos que tiveram insucessos (tentativas=3) de um determinado veiculo
	 * @param int $veiod
	 * @return array
	 */
	public function buscarTentativasInsucessoContato ($veiod){

		$retorno = array();
		$veiod = (int)$veiod;

		if (empty($veiod)) {

		}

		$sql = "
				SELECT
					cdupaveioid AS contatoexterno,
					STRING_AGG(cdupatelefone, ',') AS telefones,
					chamada
				FROM
					(
						SELECT
							cdupaveioid,
							cdupatelefone,
							MAX(cdupadt_ultima_tentativa) OVER() AS chamada,
							MIN(cdupatentativas) OVER () AS cdupatentativa_minima
						FROM
							contato_discador_ura_panico_aux
						WHERE
							cdupaveioid = ". $veiod ."
					) AS cdupa_2
				WHERE
					cdupa_2.cdupatentativa_minima = 3
				GROUP BY cdupaveioid,chamada
				";

		$rs = $this->query($sql);

		if ( pg_num_rows($rs) ) {

			$linha = pg_fetch_object($rs);

			$retorno['contatoExterno'] 	= isset($linha->contatoexterno) ? $linha->contatoexterno : 0;
			$retorno['telefones'] 		= isset($linha->telefones) ? $linha->telefones : '';
			$retorno['chamada'] 		= isset($linha->chamada) ? $linha->chamada : '';

		}

		return $retorno;

	}

    /**
     *
     * busca dados relativo a um panico especifico na tabela auxiliar
     *
     * @param int $panicoOid
     * @return int
     */
    public function buscarDadosTabelaAuxiliar($panicoOid){

        $retorno = array();

        $sql = "SELECT
                    cdupveioid,
                    cdupid_contato_discador
                FROM
                    contato_discador_ura_panico
                WHERE
                    cdupid_panico = ".$panicoOid."
                ";

        $rs = $this->query($sql);

        if ( pg_num_rows($rs) > 0 ) {

           $retorno = pg_fetch_object($rs);

        }

		return $retorno;

    }

}