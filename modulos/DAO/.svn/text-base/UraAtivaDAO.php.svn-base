<?php
require_once _MODULEDIR_ . 'Atendimento/DAO/AtendimentoDAO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaContatoVO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaContratoVO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaClienteVO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/UraAtivaInsucessoVO.php';

/**
 * Classe abstrata de persistência dos dados de UraAtivaDAO, somente executa, não possui regras.
 *
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @version 18/03/2013
 * @since   18/03/2013
 * @package Atendimento
 */
abstract class UraAtivaDAO extends AtendimentoDAO {

	const OID_MOTIVO_INICIAL 	= 63; //atcatmoid = 1 -> Atendimento inicial

	protected $usuoid;
	protected $depoid;
	protected $login;
	protected $ramal;
	protected $osCliente = array();
	protected $log = array();
	protected $param;
	protected $inseridosTabelaAuxiliar = array();
	protected $sequencialTelefone = 1;

	/**
	 * @var UraAtivaContratoVO
	 */
	protected $contrato;

	/**
	 * Propriedade que dientifica se o processo foi executado pelo Crond e Reenvio
	 * @var unknown
	 */
	public $cronReenvio = false;

	/**
	 * ID da campanha no discador
	 * @var int
	 */
	protected $campanha;
	public $nomeCampanha = '';

	/**
	 * Nome da tabela auxiliar para integração com o discador
	 * @var string
	 */
	protected $tabela_auxiliar_discador;

	/**
	 * Motivo para gravar o log dos atendimentos
	 * @var string
	 */
	public $motivoLog = '';

	/*
	 * Propriedade que define se deverá ser gravado o Log do Atendimento
	 */
	public $isGravaLogAtendimento = true;

	/**
	 * Torna público um array com as informações para gravar o log de atendimento;
	 */
	public $logAtendimento = array();

    public function __construct($conn) {

        parent::__construct($conn);

        $this->carregarInformacoesUsuario();

        $this->param = $this->getParametros();

		foreach ($this->param as $key => $value) {
			// se for um array do postgres
			if (substr($value, 0, 1) == '{') {
				$this->param->$key = $this->buildArray($value);
			}
			else if ($value == 'f') {
				$this->param->$key = false;
			}
			else if ($value == 't') {
				$this->param->$key = true;
			}
		}
    }

    private function carregarInformacoesUsuario() {

		$sql = "
			SELECT 	cd_usuario, ds_login, usudepoid, nm_usuario, 0 AS ramal
			FROM 	usuarios
			WHERE 	ds_login = 'URA_ATIVA'
		";

		$rs = $this->query($sql);

		if (pg_num_rows($rs) == 0) {
			throw new Exception('Usuário para Ura não identificado.');
		}

    	$rowUsuario = pg_fetch_object($rs);

    	$this->usuoid 	= $rowUsuario->cd_usuario;
    	$this->depoid 	= $rowUsuario->usudepoid;
    	$this->login 	= $rowUsuario->ds_login;
    	$this->ramal 	= $rowUsuario->ramal;
    }

    /**
     * Retorna os parametros especificos do tipo de atendimento
     * @return stdClass
     */
	public abstract function getParametros();

	/**
	 * QUERY sql para buscar os contatos pendentes de cada processo
	 * @return string $sql QUERY sql para busca dos contatos pendentes
	 */
	protected abstract function buscarContatosPendentes();

	/**
	 * Realiza processo de descarte de contatos pendentes
	 * @param UraAtivaContratoVO $contrato
	 * @return boolean
	 */
	protected abstract function descartar(UraAtivaContratoVO $contrato);

	/**
	 * Realiza tratamentos necessários para cada processo
	 * @param UraAtivaContratoVO $contrato
	 * @return void
	 */
	protected abstract function tratar(UraAtivaContratoVO $contrato);

	/**
	 * Busca os telefones para contato com o cliente de cada processo
	 * @param UraAtivaContratoVO $contrato
	 * @return array:UraAtivaContatoVO
	 */
	protected abstract function buscarTelefones(UraAtivaContratoVO $contrato);

	/**
	 * Busca e trata os insucessos no discador
	 * @return void
	 */
	public abstract function tratarInsucessos($insucessos);

	/**
	 *
	 * @param unknown_type $contato
	 * @param unknown_type $contrato
	 * @param unknown_type $idDiscador
	 * @param unknown_type $codigoIdentificador
	 */
	protected function afterInserirDiscador($contato, $contrato, $idDiscador, $codigoIdentificador){
		#TODO implementar nas classes especialistas
		return true;
	}


	/**
	 * Busca os contatos para envio
	 * @return array:UraAtivaContatoVO
	 */
	public abstract function buscarContatos();


	/**
	 * Busca um contrato pelo número
	 * @param int $connumero
	 * @return UraAtivaContratoVO
	 */
	public function getContrato($connumero) {

		$sql = "
			SELECT 	connumero, conno_tipo, concsioid, conclioid, conveioid, conequoid, conegaoid
			FROM 	contrato
			WHERE 	connumero = $connumero";

		$rs = $this->query($sql);

    	$row = pg_fetch_object($rs);

    	return new UraAtivaContratoVO($row);
	}

	/**
	 * Busca o cliente pelo numero de contrato
	 */
	/**
	 * Busca o email do cliente pelo contrato
	 * @param contrato
	 * @return email
	 */
	public function getClientePorContrato($contrato){

		$sql = "
			SELECT
				clinome AS nome,
				cliemail AS email,
				cliemail_nfe as email_nfe
			FROM
				clientes
			INNER JOIN
				contrato ON conclioid = clioid
			WHERE
				connumero = $contrato";

		$rs = $this->query($sql);

    	$row = pg_fetch_object($rs);

    	return new UraAtivaClienteVO($row);

	}

	/**
	 * Busca o departamento do usuário
	 * @param int $usuoid
	 * @return AtendimentoVO
	 */
	public function getDepartamento($usuoid) {

		$sql = "
			SELECT 		prhdepoid
			FROM 		usuarios
			INNER JOIN 	perfil_rh ON prhoid = usucargooid
			WHERE 		cd_usuario = ".$usuoid."
		";

		$rs = $this->query($sql);

    	return $this->fetchObject($rs);
	}

	/**
	 * Insere histórico para o contrato
	 * @param int $connumero
	 * @param string $obs
	 * @return boolean
	 */
	public function inserirHistoricoContrato($connumero, $obs) {

		$obs = pg_escape_string($obs);

		$sql = "SELECT historico_termo_i($connumero, $this->usuoid, '".$obs."') AS retorno;";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$retorno = isset($row->retorno) ? $row->retorno : 0;

		return ($retorno == 1);
	}

	/**
	 * Inserir histórico para ordem de serviço diferente da parametrizada
	 * @param int $ordoid
	 * @param string $obs
	 * @param int $status Status da OS
	 * @param date $dt_agenda Data da próxima agenda
	 * @return void
	 */
	protected function inserirHistoricoOS($ordoid, $obs, $status='', $dt_agenda='', $hr_agenda='') {
		$obs 		= pg_escape_string($obs);
		$status 	= !empty($status) 		? $status 				: 'NULL';
		$dt_agenda 	= !empty($dt_agenda) 	? "'".$dt_agenda."'" 	: 'NULL';
		$hr_agenda 	= !empty($hr_agenda)	? "'".$hr_agenda."'"	: 'NULL';

		$sql = "
			INSERT INTO ordem_situacao
    			(orsordoid, orsusuoid, orssituacao, orsdt_situacao, orsstatus, orsdt_agenda, orshr_agenda)
    		VALUES
    			(".$ordoid.", ".$this->usuoid.", '".$obs."', NOW(), ".$status.", ".$dt_agenda.", ".$hr_agenda.")
    	";

		$this->query($sql);
	}

	public function buscarDescricaoMotivoPorId($atmoid){

		$sql = "
			SELECT 	atmdescricao
			FROM 	atendimento_motivo
			WHERE 	atmoid = '".$atmoid."'
		";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$atmdescricao = isset($row->atmdescricao) ? $row->atmdescricao : '';

		return $atmdescricao;
	}

	/**
	 * Consultar o ID do Motivo do Atendimento
	 * @author André L. Zilz
	 * @since 04/04/2013
	 * @param string $atmdescricao
	 * @return int
	 */
	protected function buscarMotivoAtendimento($atmdescricao){

		$atmdescricao = pg_escape_string($atmdescricao);

		$sql = "
			SELECT 	atmoid
			FROM 	atendimento_motivo
			WHERE 	TRIM(atmdescricao) ILIKE '".$atmdescricao."'
			";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$atmoid = isset($row->atmoid) ? $row->atmoid : 0;

		return $atmoid;
	}

	/**
	 * Abre um atendimento
	 * @param int $clioid
	 * @param int $depoid
	 * @param int $atmoid
	 * @return int $atcoid
	 */
	public function abrirAtendimento($clioid, $depoid, $atmoid) {

		$depoid = (int)$depoid;

		if((empty($depoid)) || ($depoid == 0)){
			$depoid = $this->depoid;
		}

		$sql = "
			SELECT atendimento_cliente_i(
				  ".$this->usuoid." 								-- atcusuoid
				, ".$clioid." 										-- atcclioid
				, 0 												-- atcprotoid - não tem protocolo
				, '' 												-- atcprotocolo - não tem protocolo
				, ".$depoid." 										-- atcdepoid
				, ".$atmoid." 										-- atcatmoid
		) AS atcoid;";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$atcoid = isset($row->atcoid) ? $row->atcoid : 0;

		return $atcoid;
	}

	/**
	 * Insere um acesso para um atendimento
	 * @param UraAtivaContratoVO $contrato
	 * @param int $atcoid
	 * @param int $atmoid
	 * @param int $tipo_ligacao [0]Sem ligação, [1]Ligação Ativa, [2]Ligação Receptiva, [3]Retorno, [4]outros canais de comunicação(Nextel, Email)
	 */
	public function inserirAcesso(UraAtivaContratoVO $contrato, $atcoid, $atmoid, $tipo_ligacao=0) {

		$usuoid 		= $this->usuoid;
		$clioid 		= $contrato->conclioid;
		$veioid 		= $contrato->conveioid;
		$equoid 		= $contrato->conequoid;
		$conoid 		= $contrato->connumero;

		$sql = "
			SELECT atendimento_acesso_i(
				  ".$atcoid."
				, ".$usuoid."
				, ".$atmoid."
				, ".$clioid."
				, ".$veioid."
				, ".$equoid."
				, ".$tipo_ligacao."
				, ".$conoid."
		) AS ataoid;";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		$ataoid = isset($row->ataoid) ? $row->ataoid : null;

		return $ataoid;
	}

	/**
	 * Atualiza o motivo do atendimento
	 * @param ataoid
	 * @param ataatmoid
	 * @param atatipo_ligacao
	 */
	public function concluirAcesso(UraAtivaContratoVO $contrato, $atcoid, $atmoid, $tipo_ligacao=0){

		$usuoid	= $this->usuoid;
		$veioid	= $contrato->conveioid;
		$equoid	= $contrato->conequoid;
		$clioid	= $contrato->conclioid;
		$conoid	= $contrato->connumero;

		$sql = "
			SELECT atendimento_acesso_u(
				  ".$atcoid."
				, ".$usuoid."
				, ".$atmoid."
				, ".$veioid."
				, ".$equoid."
				, ".$tipo_ligacao."
				, ".$clioid."
				, ".$conoid."
		) AS retorno;";

		$rs = $this->query($sql);
	}

	/**
	 * Fecha um atendimento
	 * @param UraAtivaContratoVO $contrato
	 * @param int $atcoid
	 * @param int $atmoid
	 * @param int $tipo_ligacao [0]Sem ligação, [1]Ligação Ativa, [2]Ligação Receptiva, [3]Retorno, [4]outros canais de comunicação(Nextel, Email)
	 * @param boolean $isGeraCobranca
	 * @return void
	 */
	public function concluirAtendimento(UraAtivaContratoVO $contrato, $atcoid, $atmoid, $tipo_ligacao=0, $isGeraCobranca=false) {

		$usuoid 		= $this->usuoid;
		$veioid 		= $contrato->conveioid;
		$equoid 		= $contrato->conequoid;
		$clioid 		= $contrato->conclioid;
		$conoid 		= $contrato->connumero;
		$atmdescricao	= $this->buscarDescricaoMotivoPorId($atmoid);
		$geraCobranca 	= ($isGeraCobranca) ? 'TRUE' : 'FALSE';

		$sql = "
			SELECT atendimento_cliente_concluir(
				  ".$atcoid."
				, ".$usuoid."
				, ".$atmoid."
				, ".$veioid."
				, ".$equoid."
				, ".$tipo_ligacao."
				, ".$clioid."
				, '".$atmdescricao."'
				, ".$geraCobranca."
				, ".$conoid."
		) AS retorno;";

		$rs = $this->query($sql);
	}

	/**
	 * Envia os contatos para o discador
	 * @param array:UraAtivaContatoVO
	 * @return boolean
	 */
	public function enviarDiscador($contatos) {

		$this->inseridosTabelaAuxiliar = array();

		foreach ($contatos as $codigo => $telefones) {

			foreach ($telefones as $contato) {

				if(($this->cronReenvio) && ($this->nomeCampanha == 'assistencia')){

						$contrato = new UraAtivaContratoVO();
						$contrato->conclioid = $contato->id_contato_externo;

				}else{
					$contrato = $this->getContrato($contato->connumero);
				}

				$this->inserirDiscador($contato, $codigo, $contrato);

			}
		}
	}


	/**
	 * Insere o contato na base do discador
	 * @param UraAtivaContatoVO $contato
	 * @param int $codigoIdenficador
	 * @return void
	 */
	protected function inserirDiscador(UraAtivaContatoVO $contato, $codigoIdentificador, UraAtivaContratoVO $Contrato) {


		$id_campanha			= (int) $this->campanha;
		$nomeCampanha			=  $this->nomeCampanha;
		$campanhaPanico			= 'panico';
		$campanhaAssistencia	= 'assistencia';

		if ($nomeCampanha == $campanhaPanico) {
			$cliente = $this->buscaClienteByVeioid((int) $contato->id_contato_externo);
		}
		else if ($nomeCampanha == $campanhaAssistencia) {
			$cliente = $this->buscaClienteByClioid((int) $contato->id_contato_externo);
		}else{
            $cliente = '';
        }

		$nome 					= $contato->nome;
		$id_contato_externo 	= (int) $contato->id_contato_externo;
		$data_agendamento 		= 'NULL';
		$hora_ini_agendamento 	= '';
		$hora_fim_agendamento 	= '';
		$ramal_conta 			= '';

		$id_telefone_externo 	= (int) $contato->id_telefone_externo;
		$telefone 				= $contato->telefone;
		$tipo 					= (int) $contato->tipo;
		$num_prioridade 		= (int) $contato->num_prioridade;

		$complemento 			= $codigoIdentificador. '#' . $id_contato_externo;

		if (!empty($cliente)) {
			$nome = $cliente->clinome;
		}

		// PID_CONTATO - Resultado da procedure
		$id_contato_discador 	= 0;

		$sql = "CALL BXS.V29_INSERE_CONTATO(".$id_campanha.",'".$complemento."','".$nome."',BIGINT(".$id_contato_externo."),".$data_agendamento.",'".$hora_ini_agendamento."','".$hora_fim_agendamento."','".$ramal_conta."',".$id_telefone_externo.",'".$telefone."',".$tipo.",".$num_prioridade.",?);";
		echo $sql, "<br />";

		$conn_db2 = $this->conectarDB2();

    	$stmt = db2_prepare($conn_db2, $sql);
    	if (!$stmt) {
    		throw new Exception('Falha db2_prepare: '  . db2_stmt_errormsg() . $sql);
    	}

		$stbp = db2_bind_param($stmt, 1, "id_contato_discador", DB2_PARAM_OUT, DB2_LONG);
		if(!$stbp) {
			throw new Exception('Falha db2_bind_param: '  . db2_stmt_errormsg() . $sql);
		}

		$stex = db2_execute($stmt);
		if (!$stex) {
			throw new Exception('Falha db2_execute: ' . db2_stmt_errormsg() . $sql);
		}

		$db2Close = db2_close($conn_db2);

		$this->afterInserirDiscador($contato, $Contrato, $id_contato_discador, $codigoIdentificador);

	}

	/**
	 * Insere o registro de envio na tabela auxiliar
	 * @param UraAtivaContatoVO $contato
	 * @param UraAtivaContratoVO $Contrato
	 * @param int $id_contato_discador
	 * @param int $codigoIdentificador
	 * @throws Exception
	 */
	public function inserirRegistroAuxiliarDiscador(UraAtivaContatoVO $contato, UraAtivaContratoVO $Contrato, $id_contato_discador, $codigoIdentificador) {

		if (empty($this->tabela_auxiliar_discador)) {
			throw new Exception('Tabela auxiliar não informada.');
		}

		$sql = '';

		if($this->tabela_auxiliar_discador == 'contato_discador_ura_assistencia'){

			/*
			 * Verifica se já foi inserido um registro de envio
			 */
			$sql = "
				SELECT
						*
				FROM
					".$this->tabela_auxiliar_discador."
				WHERE
					cduaclioid = ".$Contrato->conclioid."
			";

			$rs = $this->query($sql);
			if ($rs && $this->numRows($rs) > 0) {

				$sql = "
					UPDATE
						".$this->tabela_auxiliar_discador."
					SET
						cdua_os = cdua_os||','||'".implode(",", $this->osCliente[$Contrato->conclioid])."'
					WHERE
						cduaclioid = ".$Contrato->conclioid."
				";
			} else {

				$sql = "
				INSERT INTO ".$this->tabela_auxiliar_discador."
	    			(cduaid_contato_discador, cduaclioid, cduadt_cadastro, cdua_os)
	    		VALUES
	    			(".$id_contato_discador.", ".$Contrato->conclioid.", NOW(), '".implode(",", $this->osCliente[$Contrato->conclioid])."')";
			}


		} else if($this->tabela_auxiliar_discador == 'contato_discador_ura_panico'){


			$atcoid = $this->buscarAtendimentoCliente($codigoIdentificador);

			if (empty($atcoid)) {
				$atcoid = $this->buscarAtendimentoVeiculo($Contrato->conveioid);
			}

			$sql = "
			INSERT INTO ".$this->tabela_auxiliar_discador."
				(cdupid_panico, cdupveioid, cdupconnumero, cdupatcoid, cdupid_contato_discador)
			VALUES
				($codigoIdentificador, $Contrato->conveioid, $Contrato->connumero, $atcoid, $id_contato_discador)";



		} else if ($this->tabela_auxiliar_discador == 'contato_discador_ura_estatistica'){

			$sql = "
				INSERT INTO ".$this->tabela_auxiliar_discador."
	    			(cduevegoid, cdueveioid, cdueconnumero, cdueid_contato_discador)
	    		VALUES
	    			($codigoIdentificador,  $Contrato->conveioid, $Contrato->connumero, $id_contato_discador)";

		}

		if($sql != '' ){
				$this->query($sql);
		}

	}

	/**
	 * Busca o id do atendimento_cliente relacionado ao panico
	 * @param int $oid
	 * @return int $atcoid
	 */
	public function buscarAtendimentoCliente($oid){

		$sql = "
			SELECT
				pxa_oidatpen AS atcoid
			FROM
				panicoxatend
			WHERE
				pxa_oidpanico = $oid";

		$rs = $this->query($sql);

		$row = pg_fetch_object($rs);

		$atcoid = "";

		if($row){
			$atcoid = $row->atcoid;
		}

		return $atcoid;
	}

	/**
	 * Busca o id do atendimento_cliente relacionado ao veiculo/panico
	 * @param int $oid
	 * @return int $atcoid
	 */
	public function buscarAtendimentoVeiculo($veioid){

		$sql = "
		SELECT
			pxa_oidatpen AS atcoid
		FROM
			panicoxatend
			INNER JOIN panicos_pendentes ON panicos_pendentes.oid::int4 = pxa_oidpanico
		WHERE
			papveioid = $veioid";

		$rs = $this->query($sql);

		$row = pg_fetch_object($rs);

		$atcoid = "";

		if($row){
			$atcoid = $row->atcoid;
		}

		return $atcoid;
	}

	/**
	 * Notifica o discador que foi inserido novos contatos
	 * @param UraAtivaContatoVO $contato
	 * @return boolean
	 */
	protected function notificarDiscador() {
		return false;
	}

    /**
	 * Desconsiderar Pânicos por Status de Ocorrência
     * @param int $connumero
     * @param array $paramStatusOcorrencia
     * @return string|boolean
     */
	protected function isDescartaStatusOcorrencia($connumero, $paramStatusOcorrencia, &$status = '') {

		if (!count($paramStatusOcorrencia)) {
			return false;
		}

		$filtro = "";

		foreach ($paramStatusOcorrencia as $statusAtual) {

			if($statusAtual=="A"){ $filtro.=" AND ocostatus='A' AND ococancelado IS NULL"; }
			if($statusAtual=="S"){ $filtro.=" AND ocostatus='C' AND ococancelado IS NULL"; }
			if($statusAtual=="P"){ $filtro.=" AND ococoncluido IS NULL AND ococancelado IS NULL"; }
			if($statusAtual=="R"){ $filtro.=" AND ocostatus='R' AND ococancelado IS NULL"; }
			if($statusAtual=="N"){ $filtro.=" AND ocostatus='N' AND ococancelado IS NULL"; }
			if($statusAtual=="C"){ $filtro.=" AND ococoncluido=true AND ococancelado IS NULL "; }
			if($statusAtual=="L"){ $filtro.=" AND ococancelado IS NOT NULL"; }
			if($statusAtual=="O"){ $filtro.=" AND ococoncluido=false AND ococancelado IS NULL AND ocostatus IN ('R','N')"; }
		}

		$sql = "
			SELECT 	ocostatus, ococoncluido, ococancelado
			FROM 	ocorrencia
			WHERE 	ococonnumero = ".$connumero."
			AND 	ococancelado IS NULL
			AND 	ococoncluido = FALSE
			$filtro
		";

		$rs = $this->query($sql);

		$row = $this->fetchObject($rs);

		if ($this->numRows($rs) > 0) {

			switch ($row->ocostatus) {
				case 'A':
					$status = "Em Andamento";
					break;

				case 'C':
					$status = "Sem contato";
					break;

				case 'R':
					$status = "Recuperado";
					break;

				case 'N':
					$status = "Não recuperado";
					break;

				case 'N':
					$status = "Não recuperado";
					break;
			}

			if ($row->ococoncluido == 't') {
				$status = " Concluída ";
			}

			if (strlen($row->ococancelado) > 0) {
				$status = " Cancelada ";
			}
		}

		$ocostatus = isset($row->ocostatus) ? $row->ocostatus : false;

		return $ocostatus;
	}

	/**
	 * Desconsiderar clientes com pendência financeira maior que (N) dias
	 * @param UraAtivaContratoVO $contrato
	 * @param int $paramPendenciaFinanceira
	 * @return boolean
	 */
	protected function isDescartaPendenciaFinanceira(UraAtivaContratoVO $contrato, $paramPendenciaFinanceira) {

		if (!count($paramPendenciaFinanceira)) {
			return false;
		}

		$conclioid = $contrato->conclioid;
		$connumero = $contrato->connumero;
		$conveioid = $contrato->conveioid;

		$sql = "
			SELECT 		COUNT(1) as total
			FROM 		contrato con
			INNER JOIN 	cliente_inadimplentes_sascar_ura_view civ ON civ.clioid = con.conclioid
			WHERE 		con.conclioid= ".$conclioid. "
			AND			con.connumero = ".$connumero. "
			AND 		civ.dias > ".$paramPendenciaFinanceira. "
			AND 		con.conveioid = ".$conveioid. "
		";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$total = isset($row->total) ? $row->total : 0;

		return (boolean) $total;
	}

	/**
	 * Desconsiderar clientes caso parâmetro bloqueio web marcado
	 * @author André L. Zilz
	 * @since 05/04/2013
	 * @param int $connumero
	 * @param int $conclioid
	 * @return boolean
	 */
	protected function isDescartaBloqueioWeb($connumero, $conclioid) {


		if (empty($connumero) || (empty($conclioid))) {
			return false;
		}

		$sql = "
			SELECT 		COUNT(1) as total
			FROM 		contrato con
			INNER JOIN 	cliente_cobranca cob ON cob.clicclioid = con.conclioid
			WHERE 		con.conclioid= ".$conclioid. "
			AND			con.connumero = ".$connumero. "
			AND 		cob.clicvisualizacao_web = false
			";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$total = isset($row->total) ? $row->total : 0;

		return (boolean) $total;
	}

	/**
	 * Desconsiderar pelo tipo de contrato
	 * @param int $connumero
	 * @param array $paramTiposContrato
	 * @return boolean
	 */
	protected function isDescartaTipoContrato($conno_tipo, $paramTiposContrato) {

		if ($conno_tipo == '' || (!count($paramTiposContrato))) {
			return false;
		}

		return in_array($conno_tipo, $paramTiposContrato);
	}

	/**
	 * Desconsiderar contratos com Ordens de Serviço
	 * @param int $connumero
	 * @param array $paramTiposOS
	 * @param array $paramItensOS
	 * @param array $paramStatusOS
	 * @param array $paramDefeitosAlegados
	 * @return boolean
	 */
	protected function isDescartaOrdemServicoContrato($connumero, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados=array()) {

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

			if ($this->isDescartaOrdemServico($ordemServico->ordoid, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Desconsiderar por Ordens de Serviço
	 * @param int $connumero
	 * @param array $paramTiposOS
	 * @param array $paramItensOS
	 * @param array $paramStatusOS
	 * @param array $paramDefeitosAlegados
	 * @return boolean
	 */
	protected function isDescartaOrdemServico($ordoid, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados=array()) {

		if ((!count($paramTiposOS)) && (!count($paramItensOS)) && (!count($paramStatusOS)) && (!count($paramDefeitosAlegados))) {
			return false;
		}

		//Filtra primeiro por Status da OS
		if (count($paramStatusOS)){

			$sql = "
					SELECT 	COUNT(1) as qtd
					FROM 	ordem_servico
					INNER JOIN ordem_servico_item ON ositordoid = ordoid
					INNER JOIN os_tipo_item	ON otioid =  ositotioid
					INNER JOIN os_tipo ON otiostoid = ostoid
					LEFT JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
					LEFT JOIN os_tipo_defeito ON otdoid= osdfotdoid
					WHERE 	ordoid = ".$ordoid."
					AND ordstatus IN (".implode(',', $paramStatusOS).")
				";

			$rs = $this->query($sql);
			$row = $this->fetchObject($rs);
			$qtdItens = isset($row->qtd) ? $row->qtd : 0;

			//Se a OS estiver enquadrada no filtro de status descarta
			if($qtdItens > 0){
				return true;
			}

		}

		if ((count($paramTiposOS)) || (count($paramItensOS)) || (count($paramDefeitosAlegados))){

			$sqlWhere 	= "";

			//Verificar a quantidade de itens da ordem de serviço
			$sql = "
					SELECT 	COUNT(1) as qtd
					FROM 	ordem_servico
					INNER JOIN ordem_servico_item ON ositordoid = ordoid
					INNER JOIN os_tipo_item	ON otioid =  ositotioid
					INNER JOIN os_tipo ON otiostoid = ostoid
					LEFT JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
					LEFT JOIN os_tipo_defeito ON otdoid= osdfotdoid
					WHERE 	ordoid = ".$ordoid."
				";

			$rs = $this->query($sql);
			$row = $this->fetchObject($rs);
			$qtdItens = isset($row->qtd) ? $row->qtd : 0;

			//Verificar a quantidade de itens que devem ser descartados
			$sql = "
					SELECT 	COUNT(1) as qtd
					FROM 	ordem_servico
					INNER JOIN ordem_servico_item ON ositordoid = ordoid
					INNER JOIN os_tipo_item	ON otioid =  ositotioid
					INNER JOIN os_tipo ON otiostoid = ostoid
					LEFT JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
					LEFT JOIN os_tipo_defeito ON otdoid= osdfotdoid
					WHERE 	ordoid = ".$ordoid."
				";

			if (count($paramTiposOS)) {
					$sqlWhere .= " OR (ostoid IS NOT NULL AND ostoid IN (".implode(',', $paramTiposOS).")) ";
			}
			if (count($paramItensOS)) {
				$sqlWhere .= " OR (otitipo IS NOT NULL AND otitipo IN (".$this->buildInSQL($paramItensOS).")) ";
			}
			if (count($paramDefeitosAlegados)) {
				$sqlWhere .= " OR (otdoid IS NOT NULL AND otdoid IN (".implode(',', $paramDefeitosAlegados)."))";
			}

			if ((count($paramTiposOS)) ||  (count($paramItensOS)) || (count($paramDefeitosAlegados))){

				$sql .= " AND (";

				//Remove o primeiro 'OR'
				$sqlWhere = substr(ltrim($sqlWhere), 2);

				$sql .= $sqlWhere . ")";

			}

			$rs = $this->query($sql);
			$row = $this->fetchObject($rs);
			$qtdItensDescartar = isset($row->qtd) ? $row->qtd : 0;

			$total = (int) ($qtdItens - $qtdItensDescartar);

			if ($total == 0 && $qtdItens > 0 && $qtdItensDescartar > 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Identifica se necessita atualização da placa
	 * @param string $placas
	 * @return boolean $total
	 */
	public function isAtualizaPlaca($placas){

		$placas = str_replace( ";", "','", $placas );

		$sql = "
				SELECT COUNT(1) AS total
				FROM veiculo_placa
				WHERE vplplaca IN('".$placas."')
				OR vplplaca_tra IN('".$placas."')
				OR vplplaca_maq IN('".$placas."')
			";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$total = isset($row->total) ? $row->total : false;

		return (boolean)$total;

	}

    /**
     * Transforma um array do Postgres em um array do PHP
     * @param string
     * @return mixed
     */
    public function buildArray($string) {

    	if (strlen($string)) {
            return explode(',', preg_replace('/\{|\}/', '', $string));
        }

        return array();
    }

    /**
     * Transforma um array do PHP em uma string para utilizar na clausula IN
     * @param array
     * @return string
     */
    public function buildInSQL($values) {

    	foreach ($values as $i => &$value) {
    		$value = "'".$value."'";
    	}

    	return implode(',', $values);
    }

    /**
     * Trata o numero de telefone antes de enviar para a URA
     * @param string $telefone
     * @return string || empty
     */
	public function tratarNumeroTelefone($telefone) {

		trim($telefone);
		$tamanho = 0;

		$telefone = preg_replace('/[^0-9]/', '', $telefone); // somente números
		$telefone = preg_replace('/^[0]+/', '', $telefone); // Remove os primeiros zeros da string

		$tamanho = strlen($telefone);

		$telefone = (($tamanho > 9) && ($tamanho < 12)) ? $telefone : '';

		return $telefone;

	}

    public function log($msg, $tipo='INFO') {
    	$this->log[] = date('d/m/Y H:i:s').' - '.$tipo.' - '.$msg;
    }

    public function showLog() {
    	foreach ($this->log as $value) {
    		echo '<hr>'.$value.'<hr>';
    	}
    }

	function queryOdbc($sql) {
    	global $ura_db2_user;
    	global $ura_db2_pass;
    	global $ura_db2_host;
    	global $ura_db2_port;
    	global $ura_db2_db;

	    $conn_string = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$ura_db2_db;HOSTNAME=$ura_db2_host;PORT=$ura_db2_port;PROTOCOL=TCPIP;UID=$ura_db2_user;PWD=$ura_db2_pass;";

	    $conn_db2 = odbc_connect($conn_string, $ura_db2_user, $ura_db2_pass);

	    $rs = odbc_exec($conn_db2, $sql);
	    if (!$rs) {
    		throw new Exception('Falha odbc_exec: ' . odbc_errormsg($conn_db2) .'( '. $sql .' )');
	    }

	    return $rs;
	}

    protected function conectarDB2() {

    	global $ura_db2_user;
    	global $ura_db2_pass;
    	global $ura_db2_host;
    	global $ura_db2_port;
    	global $ura_db2_db;

    	$conn_string = "DATABASE=$ura_db2_db;HOSTNAME=$ura_db2_host;PORT=$ura_db2_port;PROTOCOL=TCPIP;UID=$ura_db2_user;PWD=$ura_db2_pass;";

    	$conn_db2 = db2_connect($conn_string, '', '');
    	if(!$conn_db2) {
    		throw new Exception(db2_conn_errormsg());
    	}

    	return $conn_db2;
    }

    protected function queryDB2($sql) {

    	global $ura_db2_user;
    	global $ura_db2_pass;
    	global $ura_db2_host;
    	global $ura_db2_port;
    	global $ura_db2_db;

    	$conn_string = "DATABASE=$ura_db2_db;HOSTNAME=$ura_db2_host;PORT=$ura_db2_port;PROTOCOL=TCPIP;UID=$ura_db2_user;PWD=$ura_db2_pass;";

    	$conn_db2 = db2_connect($conn_string, '', '');
    	if(!$conn_db2) {
    		throw new Exception(db2_conn_errormsg());
    	}

    	$stmt = db2_prepare($conn_db2, $sql);
    	if (!$stmt) {
    		throw new Exception('Falha db2_prepare: ' . $sql);
    	}

    	return $stmt;
    }

    protected function fetchObjectDB2($sql) {
		$conn_db2 = $this->conectarDB2();

    	$stmt = db2_prepare($conn_db2, $sql);
    	if (!$stmt) {
    		throw new Exception('Falha db2_prepare: ' . $sql);
    	}

    	$stex = db2_execute($stmt);
    	if (!$stex) {
    		throw new Exception('Falha db2_execute: ' . $sql);
    	}

    	$row = ($stmt);

    	db2_close($conn_db2);

    	return $row;
    }

    protected function fetchObjectsDB2($sql) {
		$conn_db2 = $this->conectarDB2();

    	$stmt = db2_prepare($conn_db2, $sql);
    	if (!$stmt) {
    		throw new Exception('Falha db2_prepare: ' . $sql);
    	}

    	$stex = db2_execute($stmt);
    	if (!$stex) {
    		throw new Exception('Falha db2_execute: ' . $sql);
    	}

    	$rows = array();
    	while ($row = ($stmt)) {
    		$rows[] = $row;
    	}
    	db2_close($conn_db2);
    	return $rows;
    }

    /**
     * Busca o nuemro de OSs que necessitam de Reagendamento
     * @param int $clioid
     * @param array $os
     * @return int
     */
    public function buscarTempoReagendamentoOS($clioid, $os) {

    	$sql = "SELECT
    					COUNT(orsdt_situacao) AS total
    			FROM
    					ordem_situacao
				INNER JOIN
						ordem_servico AS os ON os.ordoid = orsordoid AND ordclioid = $clioid
				INNER JOIN
						motivo_hist_corretora ON mhcoid = orsstatus
				WHERE
						ordoid IN (".$os.")
    			AND
    				orsdt_situacao BETWEEN (NOW() - INTERVAL '12 HOURS') AND NOW()
				AND
					mhcdescricao ILIKE 'Cliente n_o Localizado'
    			";

    	$query = $this->query($sql);
    	$row = $this->fetchObject($query);
    	$total = isset($row->total) ? $row->total : 0;
    	return $total;
    }

    public function buscarInsucessos($idContato='', $contatoExterno = '', $campanha = '') {
    	$id_campanha			= (int) $this->campanha;
    	$id_contato				= (int) $idContato;
    	$contatoExterno			= (int) $contatoExterno;
    	$resultado = "";
        $retorno = array();

    	if($id_campanha > 0 && $contatoExterno > 0 && $id_contato > 0){

	    	$sql = "CALL bxs.V30_ESTADO_CONTATO(".$id_campanha.", BIGINT(".$contatoExterno."), ".$id_contato.", ?);";

	    	$conn_db2 = $this->conectarDB2();

	    	$stmt = db2_prepare($conn_db2, $sql);
	    	if (!$stmt) {
	    		throw new Exception('Falha db2_prepare: '  . db2_stmt_errormsg() . $sql);
	    	}
	    	$stbp = db2_bind_param($stmt, 1, "resultado", DB2_PARAM_OUT);
	    	if(!$stbp) {
	    		throw new Exception('Falha db2_bind_param: '  . db2_stmt_errormsg() . $sql);
	    	}
	    	$stex = db2_execute($stmt);
	    	if (!$stex) {
	    		throw new Exception('Falha db2_execute: ' . db2_stmt_errormsg() . $sql);
	    	}
echo "Retorno da busca no discador: " ;
var_dump($resultado);
	    	if ($resultado != "") {

	    		$res = explode("|", $resultado);

                //Validar se o retorno do banco esta no formato correto (4 pipes)
                if(count($res) > 3){

                    if ($res[1] == 3) {
                        $retorno = array(	"nome" => $res[0],
                                            "estado" => $res[1],
                                            "telefones" => $res[2],
                                            "agendamento" => $res[3],
                                            "chamada" => $res[4],
                                            "contatoExterno" => $contatoExterno);
                    }
                }
	    	}

    	}

        return $retorno;

    }

	/**
	 * Seta a campanha
	 * @param unknown $cuaoid
	 */
    public function setCampanha($cuaoid){

    	$sql = "
    	SELECT cuaidcampanha, cuadescricao
    	FROM campanhas_ura_ativa
    	WHERE cuaoid = $cuaoid
    	";

    	$rs = $this->query($sql);
    	$row = $this->fetchObject($rs);

    	$this->campanha 	= isset($row->cuaidcampanha) ? $row->cuaidcampanha : 0;
    	$campanha = isset($row->cuadescricao)  ? $row->cuadescricao  : 'semdescricao';

    	$campanha = trim($campanha);
    	$campanha = strtolower($campanha);
    	$campanha = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($campanha, "áàãâéêíóôõúüç", "aaaaeeiooouuc"));

    	$this->nomeCampanha = $campanha;

    }

    /**
     *
     */
    public function buscarContatos1Hora(){

    	$rows = array();

		$sql = "
				SELECT
						panicos_pendentes.oid::int4 AS codigo,
						papconnumero AS connumero,
						papveioid,
						papveiplaca,
						paptipo,
						COALESCE(papinstalacao, 0) AS papinstalacao,
						papequoid,
						paphorario,
						papconclioid,
						conno_tipo,
						concsioid,
						conclioid,
						conveioid
			FROM 		panicos_pendentes
			INNER JOIN  panicoxatend ON pxa_oidpanico = panicos_pendentes.oid::int4
			INNER JOIN 	contrato ON (connumero = papconnumero AND conveioid = papveioid)
			WHERE 		(NOW() - pxa_dtinsert)::INTERVAL > INTERVAL '1 HOUR'";

		$rs = $this->query($sql);

		while($row = pg_fetch_object($rs)) {

			$contrato = new UraAtivaContratoVO($row);

			$contatos = $this->buscarTelefones($contrato);

			$rows[$contrato->codigo] = $contatos;
		}

		return $rows;
    }

    public function getCampanha() {
        return $this->campanha;

    }

    public function desconsiderar($panico_pendente_oid){
    	#TODO implementar nas classes especialistas
    	return false;
    }

	/**
	 * Retorna o nome do cliente a partir do código
	 * @param int $clioid
	 * @return string $nome
	 */
    public function getNomeCliente($clioid){

    	$clioid = (int)$clioid;

    	$sql = "
		    	SELECT 	clinome
		    	FROM 	clientes
		    	WHERE  	clioid = $clioid";

    	$rs = $this->query($sql);

    	$row = pg_fetch_object($rs);

    	$nome = isset($row->clinome) ? $row->clinome : '';

    	return $nome;

    }

    /**
     *  Busca contato específico na tabela auxiliar do discador
     * @param integer $idregistro
     */
    public abstract function buscarContatoDiscadorEspecifico($idregistro);

    /**
     * Busca informações do contrato pelo código do Veículo
     * @param int $veioid
     * @return UraAtivaContratoVO
     */
    public function buscarInformacoesContrato($veioid){

    	$sql = "
			SELECT 	connumero, conclioid, conequoid, conveioid, conegaoid
			FROM 	contrato
			WHERE 	conveioid = ".$veioid."
			LIMIT 1
		";

    	$rs = $this->query($sql);

    	$row = pg_fetch_object($rs);

    	return new UraAtivaContratoVO($row);

    }

    /**
     * Buscar OID do motivo
     * @param string $motivo, $motivoPai
     * @return int
     */
    public function buscarMotivoPorGrupo($motivo, $motivoGrupo){

    	$motivo 	 = pg_escape_string($motivo);
    	$motivo 	 = trim($motivo);
    	$motivoGrupo = trim($motivoGrupo);

    	$sql = "
			SELECT 	atmoid
			FROM	atendimento_motivo
			WHERE 	atmexclusao IS NULL
			AND 	trim(atmdescricao) ILIKE '". $motivo ."'
			AND (
				atmagroid = (
					SELECT agroid
					FROM atendimento_grupo
					WHERE trim(agrdescricao) ILIKE  '". $motivoGrupo ."'
					AND   agrexclusao IS NULL
					LIMIT 1)
				OR atmoid_pai IN (
					SELECT atmoid
					FROM
						atendimento_motivo
						JOIN atendimento_grupo ON agroid = atmagroid
					WHERE trim(agrdescricao) ILIKE  '". $motivoGrupo ."'
				)
			)
			LIMIT 1
			";

    	$rs = $this->query($sql);

    	$row = $this->fetchObject($rs);

    	$atmoid = isset($row->atmoid) ? $row->atmoid : 0;

    	return $atmoid;

    }

    /**
     * Busca a última localização do veículo
     *
     * @see buscarUltimaLocalizaoDataVeiculo
     * @param int $veioid
     * @return string
     */
    public function buscarUltimaLocalizaoVeiculo($veioid){

    	if (empty($veioid)) {
			throw new Exception("Veículo não informado ao buscar localização no Oracle.");
		}

		$localizacao = '';

		$sql = "
			SELECT
			  (s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.X) AS X,
			  (s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.Y) AS Y
			FROM
			  sascar.ultposicao s
			WHERE
			  s.UPOSVEIOID = ". $veioid ."
		";

		$row = $this->fetchObjectOci($sql);

		if (!empty($row->X) && !empty($row->Y)) {

			$localizacao = str_replace(',', '.', $row->X) .", ". str_replace(',', '.', $row->Y);
		} else {

			$localizacao = "Localização não encontrada na base";
		}
		return $localizacao;
    }

    /**
     * Busca a ultima posição dos veículos
     * @param array $filtros
     * @throws Exception
     * @return multitype:object
     */
    public function buscarVeiculoPosicao($filtros) {

    	// Conexão Oracle
    	global $ura_oci_user;
    	global $ura_oci_pass;
    	global $ura_oci_bd;

    	$filtro = ''; // Filtros por array
    	$retorno = array(); // Linhas de retorno

    	// Conecta no banco de dados
    	$conn_oracle = oci_connect($ura_oci_user, $ura_oci_pass, $ura_oci_bd);
    	if(!$conn_oracle) {
    		$e = oci_error();

    		throw new Exception($e['message']);
    	}

    	// Estrutura filtro
    	if (isset($filtros['dias_pendentes']) && isset($filtros['periodo_atualizacao'])) {
    		$filtro .= "  AND s.uposdatahora BETWEEN (SYSDATE - INTERVAL '".$filtros['dias_pendentes']."' DAY) AND (SYSDATE - INTERVAL '".$filtros['periodo_atualizacao']."' HOUR) ";
    	}

    	if (isset($filtros['dias_pendentes']) && !isset($filtros['periodo_atualizacao'])) {
    		$filtro .= "  AND s.uposdatahora BETWEEN (SYSDATE - INTERVAL '".$filtros['dias_pendentes']."' DAY) AND (SYSDATE) ";
    	}

    	if (isset($filtros['pos_data_atual_ini'])) {
    		$filtro .= " AND s.uposdatahora >= '".date("d/m/Y", strtotime($filtros['pos_data_ini']))."' ";
    	}
    	if (isset($filtros['pos_data_atual_fim'])) {
    		$filtro .= " AND s.uposdatahora <= '".date("d/m/Y", strtotime($filtros['pos_data_fim']))."' ";
    	}

    	// Busca pelas informações
    	$sql =  "
    		SELECT
				  s.uposveioid AS VEIOID,
				  s.uposdatahora AS DATA_HORA,
				  s.uposdatahoraint AS DATA_HORA_TIMESTAMP,
				  '{'||(s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.X)||';'||(s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.Y)||'}' AS LOCALIZACAO,
				  (s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.X) AS X,
				  (s.UPOSCOORDENADA_LAT_LONG.SDO_POINT.Y) AS Y
			FROM
					sascar.ultposicao s
			WHERE
				  1 = 1
				 $filtro
			ORDER BY
			 	 s.uposveioid
    	";

    	$stid = oci_parse($conn_oracle, $sql);
    	if (!$stid) {
    		throw new Exception('Falha oci_parse: ' . $sql);
    	}

    	$stex = oci_execute($stid);
    	if (!$stex) {
    		throw new Exception('Falha oci_execute: ' . $sql);
    	}


    	while ($row = oci_fetch_object($stid)) {

			$registros = new stdClass();

    		$registros->veioid 				= isset($row->VEIOID) 				? $row->VEIOID 				: 0;
    		$registros->data_hora 			= isset($row->DATA_HORA) 			? $row->DATA_HORA 			: '';
    		$registros->data_hora_timestamp = isset($row->DATA_HORA_TIMESTAMP) 	? $row->DATA_HORA_TIMESTAMP : 0;
    		$registros->localizacao 		= isset($row->LOCALIZACAO) 			? $row->LOCALIZACAO 		: '';
    		$registros->X 					= isset($row->X) 					? $row->X 					: 0;
    		$registros->Y 					= isset($row->Y) 					? $row->Y 					: 0;

    		$retorno[] = $registros;

    		unset($registros);

    	}

    	oci_free_statement($stid);
    	oci_close($conn_oracle);

    	return $retorno;
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
     * Buscar chassi e placa do veiculo
     * @param int $veioid
     * @return object
     */
    public function getVeiculo($veioid){

    	$veioid = isset($veioid) ? $veioid : 0;

    	$sql = "SELECT
    				veichassi as chassi,
    				veiplaca as placa
    			FROM veiculo
    			WHERE veioid = $veioid";

    	$rs = $this->query($sql);

    	$row = pg_fetch_object($rs);

    	return $row;

    }

    /**
     * Recupera o nome da Seguradora associada a um contrato
     * @param int $connumero
     * @param int $veioid
     * @return string
     */
    public function getNomeSeguradora($veioid, $connumero = ''){

    	$veioid 	= isset($veioid) 	? $veioid 		: 0;
    	$connumero 	= isset($connumero) ? $connumero 	: 0;

    	$sql = "SELECT prpseguradora AS nome
    			FROM proposta
    			INNER JOIN contrato on prptermo = connumero
    			WHERE conveioid = $veioid";

    	if($connumero != ''){
    		$sql .= " AND connumero = $connumero";
    	}

    	$rs = $this->query($sql);

    	$row = $this->fetchObject($rs);

    	$nome = isset($row->nome) ? $row->nome : '';

    	return $nome;

    }

    /**
     * Insere registro no Log de envio de email
     */
    public function inserirLogEnvioEmail($connumero, $tipo, $obs, $tipoenvio = 1){

    	$sql = "INSERT INTO
		    	log_envio_email (
		    	leeordoid,
		    	leedt_envio,
		    	leetipo_log,
		    	leeconnumero,
		    	leetipo_email,
		    	leeobs
		    	)
		    	VALUES (
		    	NULL,
		    	NOW(),
		    	'$tipo',
		    	$connumero,
		    	$tipoenvio,
		    	'$obs'
		    	)";

    	$this->query($sql);

    }

    /**
     * Substitui caracteres especiais e espaçosde forma a adequar para a busca em banco
     * @param string $descricao
     * @return string
     */
    public function tratarDescricaoPesquisa($descricao){

    	$descricao = trim($descricao);

    	$texto = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($descricao, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "__________________________"));

    	$texto = str_replace(' ', '%', $texto);

    	return $texto;

    }

    /**
     * Retorna a tupla do tipo de contrato do contrato informado
     * @param integer $connumero
     * @return AtendimentoVO
     */
    public function buscaTipoContrato($connumero) {

    	$sql =  "
    		SELECT
    			tipo_contrato.*
    		FROM
    			contrato
    			INNER JOIN tipo_contrato ON conno_tipo = tpcoid
    		WHERE
    			connumero = ".$connumero."
    	";

    	$rs = $this->query($sql);
    	$row = $this->fetchObject($rs);

    	return $row;
    }


    /**
     * Enviar um e-mail informativo
     * @param unknown $destinatarioEmail
     * @param unknown $titulo
     * @param unknown $texto
     * @return boolean
     */
    public function enviarEmail($destinatario, $titulo, $texto){

    	$destinatario = trim($destinatario);

    	$mail = new PHPMailer();

    	$mail->ClearAllRecipients();

    	$mail->IsSMTP();
    	$mail->From = "sascar@sascar.com.br";
    	$mail->FromName = "Sascar";

    	$mail->Subject = $titulo;

    	$mail->MsgHTML($texto);

    	if ($_SESSION['servidor_teste'] == 1) {
    		$destinatario = "rodrigo.alasino@meta.com.br";
    	}

    	$mail->AddAddress($destinatario);

    	return $mail->Send();

    }

    /**
     * Busca o cliente pelo veiculo
     * @param integer $veioid
     * @throws Exception
     * @return Ambigous <boolean, AtendimentoVO>
     */
    public function buscaClienteByVeioid($veioid) {

    	if (empty($veioid)) {
    		throw new Exception("Veículo não informado");
    	}

    	$retorno = false;

    	$sql = "
    		SELECT
    			clioid,
    			clinome
    		FROM
    			clientes
    			INNER JOIN contrato ON conclioid = clioid
    		WHERE
    			conveioid = $veioid
    	";

    	$rs = $this->query($sql);

    	if ($rs) {

    		if ($this->numRows($rs) > 0) {

    			$retorno = $this->fetchObject($rs);
    		}
    	}

    	return $retorno;
    }

    /**
     * Busca cliente pelo OID
     * @param integer $clioid
     * @throws Exception
     * @return Ambigous <boolean, AtendimentoVO>
     */
    public function buscaClienteByClioid ($clioid) {

    	if (empty($clioid)) {
    		throw new Exception("Cliente não informado");
    	}

    	$retorno = false;

    	$sql = "
    		SELECT
    			clioid,
    			clinome
    		FROM
    			clientes
    		WHERE
    			clioid = $clioid
    	";

    	$rs = $this->query($sql);

    	if ($rs) {

    		if ($this->numRows($rs) > 0) {

    			$retorno = $this->fetchObject($rs);
    		}
    	}

    	return $retorno;
    }

    /**
     * Verifica o status da campanha para acionamento do CRON
     * @param int $idCampanha
     * @return stdClass
     */
    public function verificarAtivacaoCronCampanha($idCampanha){

    	$statusCron = new stdClass();
    	$idCampanha = (int)$idCampanha;

    	if(empty($idCampanha)){
    		return $statusCron;
    	}

    	$sql = "
    			SELECT
    					cuacronenvio,
    					cuacroninsucesso,
    					cuacronadicional,
    					cuacronreenvio
    			FROM
    					campanhas_ura_ativa
    			WHERE
    					cuaoid = ". $idCampanha ."

    			";

    	$rs = $this->query($sql);

    	if ($rs && ($this->numRows($rs) > 0)) {

    		$row = $this->fetchObject($rs);

    		$statusCron->envio 		= isset($row->cuacronenvio) ? $row->cuacronenvio : 'I';
    		$statusCron->insucesso 	= isset($row->cuacroninsucesso) ? $row->cuacroninsucesso : 'I';
    		$statusCron->adicional 	= isset($row->cuacronadicional) ? $row->cuacronadicional : 'I';
    		$statusCron->reenvio 	= isset($row->cuacronreenvio) ? $row->cuacronreenvio : 'I';
    	}

    	return $statusCron;

    }

    /**
     * Seta para ELIMINADO o contato pendente para que não seja mais discado.
     * @param int $idCampanha
     * @param int $idContatoExterno
     * @throws Exception
     * @return boolean
     */
    public function eliminarContatoDiscador($idCampanha, $idContatoExterno){

    	$idCampanha = (int)$idCampanha;
    	$idContatoExterno = (int)$idContatoExterno;

    	if(empty($idCampanha) || empty($idContatoExterno)){
    		return false;
    	}

    	$sql = "CALL BXS.V28_APAGA_CONTRATO(".$idCampanha.",BIGINT(".$idContatoExterno."));";

    	$conn_db2 = $this->conectarDB2();

    	$stmt = db2_prepare($conn_db2, $sql);
    	if (!$stmt) {
    		throw new Exception('Falha db2_prepare: '  . db2_stmt_errormsg() . $sql);
    	}

    	$stex = db2_execute($stmt);
    	if (!$stex) {
    		throw new Exception('Falha db2_execute: ' . db2_stmt_errormsg() . $sql);
    	}

    	return true;

    }

    /**
     * Verifica se o contato já esta na tabela de controle de Insucessos
     */
    public abstract function verificarInsucessoContato($codigo);

    /**
     * Insere um novo contato na tabela de controle de insucessos
     */
    public abstract function inserirInsucessoContato($codigoIdentificador, UraAtivaContatoVO $contato);

    /**
     * Atualiza o numero de tentativas em um contato na tabela de controle de insucessos
     */
    public abstract function atualizarTentativaInsucessoContato($codigoIdentificador, $telefone);

    /**
     * Deleta o contato na tabela de controle de insucessos
     */
    public abstract function removerInsucessoContato ($codigo);

    /**
     * verifica a quantidade de tentativas do discador com insucesso
     */
    public abstract function verificarTentativasInsucessoContato($telefone);

    /**
     * Busca na tabela auxiliar os contatos que deverão ser reenviados
     */
    public abstract function buscarContatosReenvio();

    /**
     *  Atualiza a data de envio do contato na tabela de controle de insucessos
     */
    public abstract function atualizarDataInsucessoContato($codigoIdentificador, $telefone);

    /**
     * busca os contato específico do cliente que tiveram insucesso no discador
     * @param int $idContatoExterno
     * @param string $idTelefoneExterno
     * @return array();
     */
    public function buscarInsucessoEspecifico($idContatoExterno, $idTelefoneExterno = '') {

    	$idCampanha			= (int) $this->campanha;
    	$idContatoExterno	= (int) $idContatoExterno;
    	$resultados 		= array();


    	if(empty($idCampanha) || empty($idContatoExterno)){

    		return '';
    	}

    	$sql = "
    			SELECT
    					ID_TELEFONE_EXTERNO
    			FROM
						bxs.VW_ESTADO_CONTATOS
				WHERE
    					id_campanha = ". $idCampanha ."
    			AND
    					id_contato_externo = ". $idContatoExterno ."
				AND
    					motivo_encerram != 'Atendida'
    		";

    	if($idTelefoneExterno) {
    		$sql.= "
    				AND
    					id_telefone_externo IN (".$idTelefoneExterno.")
    			";
    	}

    	$sql.= ";";

    	$conn_db2 = $this->conectarDB2();

    	$stmt = db2_prepare($conn_db2, $sql);
    	if (!$stmt) {
    		throw new Exception('Falha db2_prepare: ' . $sql);
    	}

    	$stex = db2_execute($stmt);
    	if (!$stex) {
    		throw new Exception('Falha db2_execute: ' . $sql);
    	}

    	while ($row = db2_fetch_object($stmt)) {
    		$resultados[] = isset($row->ID_TELEFONE_EXTERNO) ? $row->ID_TELEFONE_EXTERNO : 0;
    	}

    	db2_close($conn_db2);

    	$idTelefones = implode(',',$resultados);

		return $idTelefones;
    }

}
