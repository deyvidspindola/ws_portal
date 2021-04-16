<?php

require_once _MODULEDIR_ . 'Atendimento/DAO/UraAtivaDAO.php';
require_once _MODULEDIR_ . 'Manutencao/DAO/ParametrizacaoUraDAO.php';

/**
 * Regras para atendimento automatico de estatisticas GSM
 *
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @version 18/03/2013
 * @since   18/03/2013
 * @package Atendimento
 */
class UraAtivaEstatisticaDAO extends UraAtivaDAO {

	const DIAS_PENDENTES 			= 60;
	const MOTIVO_VEICULO_PARADO		= 'Veículo Parado / Manutenção';
	const STATUS_ANDAMENTO			= 'A';
	const STATUS_PENDENTE			= 'P';
	const STATUS_CONCLUIDO			= 'C';


	/**
	 * ID da campanha no discador
	 * @var int
	 */
	protected $campanha;
	protected $tabela_auxiliar_discador = 'contato_discador_ura_estatistica';

	public function __construct($conn) {
		parent::__construct($conn);


		$this->setCampanha(3);
	}

	/**
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::getParametros()
	 */
	public function getParametros() {

		$ParametrizacaoUraDAO = new ParametrizacaoUraDAO();

		$params = (object) $ParametrizacaoUraDAO->findLastEst();

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

			if($this->desconsiderar($contrato->conveioid)){
				continue;
			}

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


			//Array de Log do atendimento
			$arrLog['envio'][] = $row->connumero . " | " . $row->conclioid . " | " . $clinome;

			$this->tratar($contrato);

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
	 * QUERY sql para buscar todas as estatisticas pendentes
	 * @return string QUERY sql para busca dos contatos pendentes
	 */
	protected function buscarContatosPendentes() {

		$paramPeriodoAtualizacao 	= (int) $this->param->pueperiodo_atualizacao;
		$paramAcoes					= (array) $this->param->pueegaoid;
		$sqlDrop = '';
		$sql = '';

		$buscaVeiculoPosicao = array(
				'dias_pendentes'	=> self::DIAS_PENDENTES
		);

		if ($paramPeriodoAtualizacao > 0) {
			$buscaVeiculoPosicao['periodo_atualizacao'] = $paramPeriodoAtualizacao;
		}

		$veiculoPosicaoTmp	= (array) $this->buscarVeiculoPosicao($buscaVeiculoPosicao);

		// Cria tabela temporaria com as posições dos veiculos
		$sql = "
			DROP TABLE IF EXISTS veiculo_posicao_tmp;
			CREATE TEMPORARY TABLE veiculo_posicao_tmp (
				veipveioid INTEGER,
				veipdata DATE,
				veiptimestamp BIGINT,
				veiplocalizacao TEXT,
				veipx BIGINT,
				veipy BIGINT
			);
		";
		$rs =  $this->query($sql);

		// Insere a posição atual dos veiculos na temporaria
		for($i = 0 ; $i < count($veiculoPosicaoTmp); $i++) {
			$sql = "
				INSERT INTO
					veiculo_posicao_tmp
				(
					veipveioid,
					veipdata,
					veiptimestamp,
					veiplocalizacao,
					veipx,
					veipy
				)
				VALUES (
					".$veiculoPosicaoTmp[$i]->veioid.",
					'".$veiculoPosicaoTmp[$i]->data_hora."',
					".$veiculoPosicaoTmp[$i]->data_hora_timestamp.",
					'".$veiculoPosicaoTmp[$i]->localizacao."',
					".$veiculoPosicaoTmp[$i]->X.",
					".$veiculoPosicaoTmp[$i]->Y."
				)
			";

			$rs =  $this->query($sql);
		}


		unset($veiculoPosicaoTmp);

		$sql = "
				SELECT 		vegoid AS codigo,
							connumero,
							conclioid,
							conequoid,
							veipdata,
							conveioid,
							conno_tipo,
							concsioid,
							vegstatus
				FROM 		veiculo_posicao_tmp
				INNER JOIN 	veiculo_estatistica_gsm ON vegveioid = veipveioid
				INNER JOIN 	veiculo 				ON veioid = veipveioid
				INNER JOIN 	contrato 				ON conveioid 	= veioid
				WHERE 		(vegmanutencao IS NULL OR vegmanutencao <= NOW())
				AND 		vegusuoid_atendimento IS NULL
				AND 		concsioid <> 9
			";

		//Desconsiderar estatísticas GSM com ações parametrizadas
		if (count($paramAcoes)) {
			$sql .= " AND (conegaoid IS NULL OR conegaoid NOT IN (".implode(',', $paramAcoes)."))";
		}else{
			$sql .= " AND conegaoid IS NULL";
		}

		$sql .= " ORDER BY vegoid DESC";


		return $sql;

	}

	/**
	 * Realiza processo de descarte de contatos pendentes
	 * @param UraAtivaContratoVO $contrato
	 * @return boolean
	 */
	protected function descartar(UraAtivaContratoVO $contrato) {

		$paramClientesFrota 		= (array) $this->param->puecliente_frota;
		$paramStatusOcorrencia 		= (array) $this->param->pueocostatus;
		$paramPendenciaFinanceira	= (int) $this->param->puependencia_financeira;
		$paramTiposOS				= (array) $this->param->pueostoid;
		$paramItensOS 				= (array) $this->param->pueitem;
		$paramStatusOS 				= (array) $this->param->pueossoid;
		$paramLedBloqueio 			= (boolean) $this->param->pueled_bloqueio;
		$paramTiposContrato 		= (array) $this->param->puetpcoid;
		$paramStatusContrato 		= (array) $this->param->puecsioid;
		$paramStatusVeiculo			= (array) $this->param->puestatus;

		$obsHistorico =  "Ura Ativa Estatística GSM,\n";
		$obsHistorico .= "Data/hora: " . date("d/m/Y H:i") . ",\n";
		$obsHistorico .= "Data/hora última posição: " . date('d/m/Y H:i', strtotime($contrato->veipdata)) . ",\n";
		$obsHistorico .= "Motivo: ";


		if ($this->isDescartaTipoContrato($contrato->conno_tipo, $paramTiposContrato)){

			$this->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $contrato->codigo, false);

			$this->motivoLog = "Tipo de contrato";

			return true;

		}

		if($this->IsDescartaStatusContrato($contrato->concsioid, $paramStatusContrato)){

			$this->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $contrato->codigo, false);

			$this->motivoLog = "Status de contrato";

			return true;
		}

		if ($this->isDescartaStatusEstatisticaGsm($contrato->vegstatus, $paramStatusVeiculo)){

			$this->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $contrato->codigo, false);

			$this->motivoLog = "Estatística GSM com status parametrizado";

			return true;

		}

		if ($this->isDescartaClienteFrota($contrato->conclioid, $paramClientesFrota)) {

			$this->atualizarEstatisticaGsm(self::STATUS_ANDAMENTO, $contrato->codigo, false);

			$obs =  $obsHistorico . "Cliente Frota";

			$this->motivoLog = "Cliente Frota";

			$this->inserirHistoricoContrato($contrato->connumero, $obs);

			return true;
		}

		if ($this->isDescartaPendenciaFinanceira($contrato, $paramPendenciaFinanceira)){

			$this->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $contrato->codigo, false);

			$this->motivoLog = "Pendência Financeira";

			return true;
		}

		if ($paramLedBloqueio && $this->isDescartaBloqueioWeb($contrato->connumero, $contrato->conclioid)){

			$this->motivoLog = "Bloqueio Web";

			return true;
		}

		$status = $this->isDescartaStatusOcorrencia($contrato->connumero, $paramStatusOcorrencia);

		if (!empty($status)) {

			switch ($status){
				case 'A':
					$status = "Em andamento.";
					break;
				case 'R':
					$status = "Recuperado.";
					break;
				case 'N':
					$status = "Não Recuperado.";
					break;
				case 'C':
					$status = "Sem Contato.";
					break;
				default:
					$status = "";
					break;
			}

			$obs =  $obsHistorico . "Ocorrência " . $status;

			$this->inserirHistoricoContrato($contrato->connumero, $obs);

			$this->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $contrato->codigo, false);

			$this->motivoLog = "Status ocorrência " . $status;

			return true;
		}

		if ($this->isDescartaManutencaoLavacar($contrato->conveioid)) {

			$motivo = $this->tratarDescricaoPesquisa(self::MOTIVO_VEICULO_PARADO);

			$atmoid = $this->buscarMotivoAtendimento($motivo);

			$atcoid = $this->abrirAtendimento($contrato->conclioid, $this->depoid, $atmoid);
			$ataoid = $this->inserirAcesso($contrato, $atcoid, $atmoid);
			$this->concluirAtendimento($contrato, $atcoid, $atmoid);

			$egaoid = $this->buscarAcaoEstatisticaGsm($motivo);

			$this->atualizarAcaoContrato($contrato->connumero, $egaoid);

			$this->atualizarVeiculoEstatisticaGsm($contrato->connumero, $contrato->conveioid);

			$obs =  $obsHistorico . self::MOTIVO_VEICULO_PARADO;

			$this->inserirHistoricoContrato($contrato->connumero, $obs);

			$this->motivoLog = self::MOTIVO_VEICULO_PARADO;

			return true;
		}

		if ($this->isDescartaOrdemServicoContrato($contrato->connumero, $paramTiposOS, $paramItensOS, $paramStatusOS)) {

			$this->atualizarEstatisticaGsm(self::STATUS_CONCLUIDO, $contrato->codigo, false);

			$this->motivoLog = "Ordem de Serviço";

			return true;
		}

		return false;
	}

	/**
	 * Descarta estatística GSM com status parametrizado
	 * @param string $vegstatus
	 * @param array $paramStatusVeiculo
	 * @return boolean
	 */
	protected function isDescartaStatusEstatisticaGsm($vegstatus, $paramStatusVeiculo){

		if (!count($paramStatusVeiculo)) {
			return false;
		}

		return in_array($vegstatus, $paramStatusVeiculo);

	}

	/**
	 * Descarta pelo Status do Contrato
	 * @param int $concsioid
	 * @param array $paramStatusContrato
	 * @return boolean
	 */
	protected function IsDescartaStatusContrato($concsioid, $paramStatusContrato){

		if (!count($paramStatusContrato)) {
			return false;
		}

		return in_array($concsioid, $paramStatusContrato);

	}

	/**
	* Desconsiderar os clientes frotas que optaram não receber dados de estatística
	* @param int $clioid
	* @param array $paramClientesFrota
	* @return boolean
	*/
	private function isDescartaClienteFrota($clioid, $paramClientesFrota) {

		if (!count($paramClientesFrota)) {
			return false;
		}

		return in_array($clioid, $paramClientesFrota);
	}

	/**
	 * Desconsiderar estatísticas GSM que estivem no período de Manutenção/Lavacar
	 * @author André L. Zilz
	 * @since 04/04/2013
	 * @param int $conveioid
	 * @return boolean
	 */
	private function isDescartaManutencaoLavacar($conveioid){


		if (empty($conveioid)) {
			return false;
		}

		$sql = "
			SELECT 	COUNT(1) as total
			FROM 	ignora_panico
			WHERE 	igpveioid = ".$conveioid."
			AND 	igptipo_descarte = 2
			AND 	NOW() BETWEEN igpdt_inicio_lavacar AND igpdt_fim_lavacar
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$total = isset($row->total) ? $row->total : false;

		return (boolean) $total;

	}

	/**
	 * Consultar a ação Veículo Parado/Manutenção
	 * @author André L. Zilz
	 * @since 04/04/2013
	 * @return int
	 */
	public function buscarAcaoEstatisticaGsm($descricao){

		$descricao = trim($descricao);

		$sql = "
			SELECT 	egaoid
			FROM 	estatistica_gsm_acao
			WHERE 	TRIM(egadescricao) ILIKE '$descricao'";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$egaoid = isset($row->egaoid) ? $row->egaoid : 0;

		return $egaoid;
	}

	/**
	 * Atribuir ação ao contrato
	 * @author André L. Zilz
	 * @since 04/04/2013
	 * @param int $connumero, $conveioid
	 * @return void
	 */
	public function atualizarAcaoContrato($connumero, $egaoid){

		if(($egaoid >= 0) && (!empty($connumero))){

			$egaoid = (int) $egaoid;
			$connumero = (int) $connumero;

			$sql = "
				UPDATE	contrato
				SET		conegaoid = $egaoid
				WHERE	 connumero = $connumero";

			$rs = $this->query($sql);
		}
	}

	/**
	 * Alterar o status (Em Andamento) e data de veiculo_estatistica_gsm
	 * @author André L. Zilz
	 * @since 04/04/2013
	 * @param int $connumero
	 * @param int $conveioid
	 */
	private function atualizarVeiculoEstatisticaGsm($connumero, $conveioid){

		if(!empty($connumero) && !empty($conveioid)){

			$sql = "
					UPDATE	veiculo_estatistica_gsm
					SET		vegstatus = 'A'
							,vegmanutencao = (
							(
							SELECT
								CASE
									WHEN vegmanutencao IS NULL THEN
										NOW()
									WHEN (vegmanutencao + interval '48 HOURS') <= NOW() THEN
										NOW()
									ELSE
										vegmanutencao
								END
							FROM veiculo_estatistica_gsm
							WHERE vegveioid = ".$conveioid. "
							LIMIT 1
							) +  INTERVAL '48 HOURS')
					WHERE	 vegveioid = ".$conveioid. "
					";

			$rs = $this->query($sql);
		}
	}

	/**
	 * Realiza tratamentos necessários para a estatistica
	 * @param UraAtivaContratoVO $contrato
	 * @return void
	 */
	protected function tratar(UraAtivaContratoVO $contrato) {
		//@TODO Verificar se existe alguma tratativa necessária
	}

	/**
	 * Busca os telefones para contato com o cliente
	 * @param UraAtivaContratoVO $contrato
	 * @return array:UraAtivaContatoVo
	 */
	public function buscarTelefones(UraAtivaContratoVO $contrato) {

		$connumero 		= isset($contrato->connumero) ? $contrato->connumero : 0;
		$contratante 	= 1;
		$autorizada 	= 2;

		$sql = "
			SELECT 		'$autorizada' AS tipo,
						tctconnumero AS connumero,
						tctcontato AS nome,
						tctdt_cadastro AS cadastro,
						(tctno_ddd_res || tctno_fone_res) AS res,
						(tctno_ddd_com || tctno_fone_com) AS com,
						(tctno_ddd_cel || tctno_fone_cel) AS cel,
						'' AS fone2,
						'' AS fone3,
						'' AS fone4,
						CONCAT($autorizada , tctoid) AS id_telefone_externo,
						conveioid AS id_contato_externo
			FROM 		telefone_contato
			INNER JOIN	contrato ON connumero = tctconnumero
			INNER JOIN	veiculo_estatistica_gsm ON conveioid = vegveioid
			WHERE  		tctorigem = 'A'
			AND 		tctconnumero = ".$connumero."
			UNION ALL
			SELECT 		'$contratante' AS tipo,
						connumero,
						clinome AS nome,
						NOW() AS cadastro,
						clifone_res AS res,
						clifone_com AS com,
						clifone_cel AS cel,
						clifone2_com AS fone2,
						clifone3_com AS fone3,
						clifone4_com AS fone4,
						clioid::text AS id_telefone_externo,
						conveioid AS id_contato_externo
			FROM 		contrato
			INNER JOIN 	clientes ON clioid = conclioid
			INNER JOIN	veiculo_estatistica_gsm ON conveioid = vegveioid
			WHERE 		connumero = ".$connumero."
			ORDER BY 	cadastro
		";

		$rs = $this->query($sql);

		$telefones 			= array();
		$telefonesDoContato = array();
		$telefonesInclusos 	= array();

		while ($row = pg_fetch_object($rs)) {

			$id_telefone = 1;
			$id_telefone_ext_original = $row->id_telefone_externo;

			//alert: Não alterar a ordem das linhas abaixo!
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->res);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->com);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->fone2);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->fone3);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->fone4);
			$telefonesDoContato[] = $this->tratarNumeroTelefone($row->cel);

			unset($row->res);
			unset($row->com);
			unset($row->cel);
			unset($row->fone2);
			unset($row->fone3);
			unset($row->fone4);

			foreach ($telefonesDoContato as $telefone) {

				// se não é vazio e ainda não foi incluso para o mesmo contato
				if (!empty($telefone) and !in_array($telefone, $telefonesInclusos)) {

					$row->telefone = '0' .  $telefone;

					/*
					 * montar id_contato_externo de acordo com telefone
					 * ID_TELEFONE:
					 * 1 para clifone_res,
					 * 2 para clifone_com,
					 * 3 para clifone2_com,
					 * 4 para clifone3_com,
					 * 5 para clifone4_com,
					 * 6 para clifone_cel
					 */
					if($row->tipo == $contratante){
						$row->id_telefone_externo =  $contratante . $id_telefone . $id_telefone_ext_original;

					}

					$telefonesInclusos[] = $telefone;

					$telefones[] = new UraAtivaContatoVO($row);
				}

				$id_telefone++;
			}

			unset($telefonesDoContato);
			unset($id_telefone_ext_original);
			unset($id_telefone);
		}

		unset($telefonesInclusos);

		return $telefones;
	}

	/**
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::tratarInsucessos()
	 */
    public function tratarInsucessos($insucessos) {
    	//Implementado na Action.
    	return array();
    }

    /**
     * Prepara
     * @param UraAtivaContatoVO $contato
     * @param UraAtivaContratoVO $contrato
     * @param int $id_contato_discador
     * @param int $codigoIdentificador
     */
    public function afterInserirDiscador($contato, $contrato, $id_contato_discador, $codigoIdentificador){

    	$vegoid = isset($contrato->codigo) ? $contrato->codigo : 0;

    	if(!$this->verificaRegistroTabelaAuxiliar($vegoid)){
    		$this->inserirRegistroAuxiliarDiscador($contato, $contrato, $id_contato_discador, $codigoIdentificador);
    	}
    }

    /**
     * Valida se o registro já existe na tabela auxiliar
     * @param int $vegoid
     * @return boolean
     */
    public function verificaRegistroTabelaAuxiliar($vegoid){

    	if(empty($vegoid)){
    		return false;
    	}

    	$sql = "SELECT count(1) as total FROM contato_discador_ura_estatistica WHERE cduevegoid = $vegoid";

    	$rs = $this->query($sql);

    	$row = $this->fetchObject($rs);

    	$retorno = isset($row->total) ? $row->total : 0;

    	return (boolean)$retorno;
    }

    /**
     * Busca contato específico na tabela auxiliar do discador
     * @param integer $idregistro
     * @return integer
     */
    public function buscarContatoDiscadorEspecifico($idregistro){
    	//@TODO implementar

    	return 0;
    }

    /**
     * Excluir registro da tabela auxiliar de contatos estatistica
     * @param int $atcoid
     */
    public function excluirEstatisticaAuxiliar($vegoid){

    	if(!empty($vegoid)){

    		$vegoid = (int) $vegoid;

	    	$sql = "DELETE FROM contato_discador_ura_estatistica WHERE cduevegoid = $vegoid";

	    	$this->query($sql);
    	}
    }

    /**
     * Verifica a existência do veículo na tabela de estatística
     * @param int $vegoid
     * @return int
     */
    public function getVeiculoEstatisticaGsm($vegoid){

    	if(!empty($vegoid)){

	    	$sql = "SELECT vegveioid as veioid
	    			FROM veiculo_estatistica_gsm
	    			WHERE vegoid = $vegoid";

	    	$rs = $this->query($sql);

	    	$row = $this->fetchObject($rs);

	    	$veioid = isset($row->veioid) ? $row->veioid : 0;

	    	return $veioid;

    	}else{
    		return 0;
    	}
    }

    /**
     * Atribuir ação ao registro de estatística no contrato
     * @param int $connumero
     * @param int $conegaoid
     * @return int
     */
    public function atribuirAcaoContrato ($connumero, $conegaoid){

    	if(!empty($connumero) && !empty($conegaoid)){

	    	$sql = "UPDATE contrato SET conegaoid = $conegaoid WHERE connumero = $connumero";

	    	$rs = $this->query($sql);
    	}
    }

    /**
     * Atualiza Status, Data Manutenção e Usuário da tabela de Estatistica GSM     *
     * @param string $vegstatus
     * @param int $vegoid
     * @param string | Boolean $atualizaData
     */
    public function atualizarEstatisticaGsm ($vegstatus, $vegoid, $atualizaData='', $quantidadeDias = 0){


    	if(!empty($vegoid) && !empty($vegstatus)){

	    	$sql = "UPDATE veiculo_estatistica_gsm
	    			SET vegstatus = '" . $vegstatus . "'";

	    	if(!empty($atualizaData)){

	    		$sql .= ",vegusuoid_atendimento = NULL, ";

                if($quantidadeDias > 0){
                   $sql .= "vegmanutencao = (NOW() + '$quantidadeDias DAYS'::INTERVAL)::DATE";
                } else {
                   $sql .= "vegmanutencao = '$atualizaData'";
                }

	    	}

	    	$sql .=" WHERE vegoid = $vegoid";

	    	$rs = $this->query($sql);
    	}
    }

    /**
     * Verifica se o tipo de contrato é Seguradora
     * @param int $connumero
     * @return boolean
     */
    public function isContratoSeguradora($connumero){

    	if(empty($connumero)){
    		return false;
    	}

    	$sql = "SELECT tpcseguradora::INT AS retorno
    			FROM contrato
    			INNER JOIN tipo_contrato ON tpcoid = conno_tipo
    			WHERE connumero = $connumero
    			";

    	$rs = $this->query($sql);

    	$row = $this->fetchObject($rs);

    	$retorno = isset($row->retorno) ? $row->retorno : 0;

    	return $retorno;

    }

    /**
     * Busca um contrato pelo número (método pai sobrescrito)
     * @param int $connumero
     * @return UraAtivaContratoVO
     */
    public function getContrato($connumero) {

    	$connumero = !empty($connumero) ? $connumero : 0;

    	$sql = "SELECT		connumero, conegaoid, conno_tipo, concsioid, conclioid, conveioid, conequoid, vegoid AS codigo
    			FROM 		contrato
    			INNER JOIN 	veiculo_estatistica_gsm ON vegveioid = conveioid
    			WHERE 		connumero = $connumero";

    	$rs = $this->query($sql);

    	$row = pg_fetch_object($rs);

    	return new UraAtivaContratoVO($row);
    }

    /**
     * Busca o id da acao por descricao
     */
    public function buscarAcaoPorDescricao($descricao){

    	$sql = "
	    	SELECT
	    		egaoid
	    	FROM
	    		estatistica_gsm_acao
	    	WHERE
	    		egadescricao ILIKE '$descricao'
	    	ORDER BY
	    		egadescricao";

    	$rs = $this->query($sql);

    	$arrIdMotivos = array();

    	while($result = pg_fetch_object($rs)){

  		  	array_push($arrIdMotivos, $result->egaoid);

   		}

    	return $arrIdMotivos;
   }

    /**
     * Busca vegoid pelo veioid
     */
    public function buscarIdRegistroApagar($veioid){

    	$sql = "
    		SELECT
    			vegoid
    		FROM
    			veiculo_estatistica_gsm
    		WHERE
    			vegveioid = $veioid";

    	$rs = $this->query($sql);

   		$row = pg_fetch_object($rs);

    	return $row;

    }

    /**
     * Busca dados dos contatos de panico na tabela temporaria
     * @return array $contatos
     */
    public function buscarContatosDiscadorEstatistica(){

    	$contatos = array();
    	$k = 0;

    	$sql ="
    		SELECT
                cduevegoid AS vegoid,
                cdueid_contato_discador AS id_contato
            FROM
                contato_discador_ura_estatistica";

    	$rs = $this->query($sql);

    	while($row = pg_fetch_object($rs)){

    		$contatos[$k]['vegoid'] 	= isset($row->vegoid) ? $row->vegoid : 0;
    		$contatos[$k]['id_contato'] = isset($row->id_contato) ? $row->id_contato : '';
    		$k++;
    	}

    	return $contatos;

	}


	/**
	 * Verifica se o contato já esta na tabela de controle de Insucessos
	 * Implementação da classe abstrata
	 * @see UraAtivaDAO::verificarInsucessoContato()
	 */
	public function verificarInsucessoContato($codigo){
		return false;
	}

	/**
	 * Insere um novo contato na tabela de controle de insucessos
	 * Implementação da classe abstrata
	 * @see UraAtivaDAO::inserirInsucessoContato()
	 */
	public function inserirInsucessoContato($codigoIdentificador, UraAtivaContatoVO $contato){
		return true;
	}

	/**
	 * Atualiza o numero de tentativas em um contato na tabela de controle de insucessos
	 * Implementação da classe abstrata
	 * @see UraAtivaDAO::atualizarInsucessoContato()
	 */
	public function atualizarTentativaInsucessoContato ($codigoIdentificador, $telefone){
		return true;
	}

	/**
	 * Deleta o contato na tabela de controle de insucessos
	 * Implementação da classe abstrata
	 * @see UraAtivaDAO::removerInsucessoContato()
	 */
	public function removerInsucessoContato ($codigo){
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::verificarTentativasInsucessoContato()
	 */
	public function verificarTentativasInsucessoContato($telefone){
		return 0;
	}

	/**
	 * Busca na tabela auxiliar os contatos que deverão ser reenviados
	 */
	public function buscarContatosReenvio(){
		return '';
	}

	/**
	 *  Atualiza a data de envio do contato na tabela de controle de insucessos
	 */
	public function atualizarDataInsucessoContato($codigoIdentificador, $telefone){
		return true;
	}

    /**
     * Busca por contratos do mesmo cliente, tipo, status e modalidade
     * @param int $connumero
     * @return array
     */
    public function buscarContratosSimilares($connumero){

        $contratos = array();

        /*
         * conmodalidade: L = Liocação / V = Revenda
         */

        $sql ="
            --remove tabela temporaria caso ja exista
                DROP TABLE IF EXISTS contrato_temp;

             --cria tabela temporaria
                CREATE TEMP TABLE
                    contrato_temp AS
                                    SELECT
                                         conno_tipo AS tipo,
                                         conclioid AS cliente,
                                         constatus AS status,
                                         conmodalidade AS modalidade
                                    FROM
                                        contrato
                                    WHERE
                                        connumero = ". $connumero ."
                                    AND
                                        (conmodalidade = 'L' OR conmodalidade = 'V');
               --realiza a busca
                SELECT
                    connumero
                FROM
                    contrato
                INNER JOIN
                    contrato_temp
                                ON (tipo = conno_tipo
                                AND cliente = conclioid
                                AND status = constatus)
            ";

        $recordset = $this->query($sql);

   		while($resultado = pg_fetch_object($recordset)){
            $contratos[] = $resultado->connumero;
         }

         return $contratos;

    }
}