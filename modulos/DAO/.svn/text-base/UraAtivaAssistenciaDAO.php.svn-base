<?php

require_once _MODULEDIR_ . 'Atendimento/DAO/UraAtivaDAO.php';
require_once _MODULEDIR_ . 'Manutencao/DAO/ParametrizacaoUraDAO.php';

/**
 * Regras para atendimento automatico de assistencia
 *
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @version 18/03/2013
 * @since   18/03/2013
 * @package Atendimento
 */
class UraAtivaAssistenciaDAO extends UraAtivaDAO {

	/**
	 * Quantidade de clientes que serão atendidos por execução do CRON
	 *
	 * @var int
	 */
	const LIMITE_CLIENTES_TRATADOS = 30;

	/**
	 * ID da campanha no discador
	 * @var int
	 */
	public $campanha;

	protected $tabela_auxiliar_discador = 'contato_discador_ura_assistencia';

	public function __construct($conn, $cronReenvio = false) {

		parent::__construct($conn);

		$this->setCampanha(2);

		$this->cronReenvio = $cronReenvio;
	}

	/**
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::getParametros()
	 */
	public function getParametros() {

		$ParametrizacaoUraDAO = new ParametrizacaoUraDAO();

		$params = (object) $ParametrizacaoUraDAO->findLastAssistencia();

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

			$clinome = $this->getNomeCliente($row->conclioid);

			if ($this->descartar($contrato)) {

				$this->limparDataAgendaOS($contrato);

				$descartados[] = $row->codigo;

				$arrLog['descarte'][] = $row->connumero . " | " . $row->conclioid . " | " . $clinome . " | " . $this->motivoLog;

				continue;
			}

			//Verifica se Não deve enviar ao discador. Apenas aplicar regras de descarte.
			if($CronParcial == 'P'){
				continue;
			}

			//Se existir na tabela <contato_discador_ura_assistencia_aux> não envia. Será tratado pelo Cron de Reenvio.
			if($this->verificarInsucessoContato($row->conclioid)){
				continue;
			}

			$arrLog['envio'][] = $row->connumero . " | " . $row->conclioid . " | " . $clinome;

			$clioid = $contrato->conclioid;

			$this->osCliente[$clioid][] = $row->codigo;


			if (isset($rows[$clioid])) {
				continue;
			} else {
				$contatos = $this->buscarTelefones($contrato);

				$rows[$clioid] = $contatos;
			}

			//$this->tratar($contrato);  //movido para o método afterInserirDiscador

		}

		$this->logAtendimento = $arrLog;

		echo '<pre>';
		echo 'DESCARTADOS INI: <hr>';
		print_r($descartados);
		echo 'DESCARTADOS FIM: <hr>';

		return $rows;
	}

	/**
	 * Busca por númerod e OS, os contatos de OSs que necessitam de atendimento
	 * @param int $ordemServico
	 * @return array
	 */
	public function buscarContatosPendentesOrdemServico($ordemServico){


        $paramStatusOS	= (array) $this->param->puaossoid;
		$retorno = array();

		$sql = "
			SELECT 		ordoid AS codigo,
						conno_tipo,
						ordconnumero AS connumero,
						veiplaca,
						orddt_ordem AS data_ordem,
						tpcdescricao,
						conclioid,
						concsioid
			FROM
						ordem_servico
			INNER JOIN
						contrato ON connumero = ordconnumero
			INNER JOIN
						tipo_contrato ON conno_tipo = tpcoid
			INNER JOIN
						veiculo ON veioid = conveioid
			WHERE
						ordoid IN (". $ordemServico .")
			AND
						condt_exclusao IS NULL
			AND (
						(
							orddt_agenda_discador IS NOT NULL
							AND orddt_agenda_discador <= NOW()
						)
						OR (
	      					orddt_agenda_discador IS NULL
	      					--AND ordacomp_usuoid IS NULL
     					)
				)
			";

			//Desconsiderar pelo status da OS
			if (count($paramStatusOS)) {
				$sql .= " AND 	ordstatus NOT IN (".implode(',', $paramStatusOS).") ";
			}
			else {
				$sql .= " AND 	ordstatus NOT IN (3, 9) ";
			}

			$recordSet = $this->query($sql);

			while($contato = pg_fetch_object($recordSet)) {

				$contrato = new UraAtivaContratoVO($contato);

				$retorno[] = $contrato;

				unset($contrato);

			}

			return $retorno;
	}


	/**
	 * Busca todas as OSs pendentes dos clientes
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::buscarContatosPendentes()
	 */
	public function buscarContatosPendentes() {
        $sql = $this->filtrarContatosPendentesPorClientes();

        $sql.= "
            SELECT
                ordem_servico.ordoid AS codigo,
                ordem_servico.ordconnumero AS connumero,
                ordem_servico.orddt_ordem AS data_ordem,
                contrato.conclioid,
                contrato.concsioid,
                contrato.conno_tipo,
                veiculo.veiplaca,
                tipo_contrato.tpcdescricao
            FROM
                ordem_servico
                    INNER JOIN
                        contrato ON ordem_servico.ordconnumero = contrato.connumero
                    INNER JOIN
                        temp_contatos_pendentes_assistencia ON contrato.conclioid = temp_contatos_pendentes_assistencia.codigo_cliente
                    ".$this->buscarContatosPendentesRegras()."
            AND
                EXISTS
                    (
                        SELECT
                            1
                        FROM
                            ordem_servico_item
                                INNER JOIN
                                    os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
                                INNER JOIN
                                    os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
                        WHERE
                            ordem_servico_item.ositordoid  = ordem_servico.ordoid
                        AND
                            ordem_servico_item.ositexclusao IS NULL
                        AND
                            ordem_servico_item.ositstatus <> 'X'
                        AND
                            os_tipo.ostdescricao = 'ASSISTÊNCIA'
                    )
            ORDER BY
                ordoid DESC;
        ";

        return $sql;
	}

	/**
	 * Busca todos os clientes com OS pendentes e armazena em tabela temporaria
	 * @return string
	 */
	protected function filtrarContatosPendentesPorClientes() {

        $sql = "";

		$sql = "
            DROP TABLE
                IF EXISTS
                    temp_contatos_pendentes_assistencia;
            CREATE TEMPORARY TABLE
                temp_contatos_pendentes_assistencia
            AS (
                SELECT
                    contrato.conclioid AS codigo_cliente
                FROM
                    ordem_servico
                        INNER JOIN
                            contrato ON ordem_servico.ordconnumero = contrato.connumero
                        ".$sql.= $this->buscarContatosPendentesRegras()."
                -- O WHERE é criado na função.
                AND
                    EXISTS
                        (
                            SELECT
                                1
                            FROM
                                ordem_servico_item
                                    INNER JOIN
                                        os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
                                    INNER JOIN
                                        os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
                            WHERE
                                ordem_servico_item.ositordoid  = ordem_servico.ordoid
                            AND
                                ordem_servico_item.ositexclusao IS NULL
                            AND
                                ordem_servico_item.ositstatus <> 'X'
                            AND
                                os_tipo.ostdescricao = 'ASSISTÊNCIA'
                        )
                ORDER BY
                    ordoid DESC
                LIMIT
                    ".self::LIMITE_CLIENTES_TRATADOS."
            );
		";

		return $sql;
	}

	/**
	 * QUERY sql para buscar todas as estatisticas pendentes
	 * @return string QUERY sql para busca dos contatos pendentes
	 */
	protected function buscarContatosPendentesRegras() {

		$paramTiposOS 				= (array) $this->param->puaostoid;
		$paramItensOS 				= (array) $this->param->puaitem;
		$paramStatusOS 				= (array) $this->param->puaossoid;
		$paramDefeitosAlegados 		= (array) $this->param->puaotdoid;
		$paramStatusContrato 		= (array) $this->param->puacsioid;

		$sql = "
            INNER JOIN
                tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
            INNER JOIN
                veiculo ON contrato.conveioid = veiculo.veioid
            WHERE
                contrato.condt_exclusao IS NULL
            AND
                ordem_servico.orddt_envio_discador IS NULL
            AND
                (
                        (
                                ordem_servico.orddt_agenda_discador IS NOT NULL
                            AND
                                ordem_servico.orddt_agenda_discador <= NOW()
                        )
                    OR
                        (
                                ordem_servico.orddt_agenda_discador IS NULL
                            -- AND
                                -- ordem_servico.ordacomp_usuoid IS NULL
                        )
                )
            AND
                (
                        (
                                ordem_servico.orddt_autorizacao < (NOW() - INTERVAL '48 hours')
                            AND
                                contrato.conno_tipo <> 90
                        )
                    OR
                        (
                                ordem_servico.orddt_autorizacao < (NOW() - INTERVAL '24 hours')
                            AND
                                contrato.conno_tipo = 90
                        )
				)
		";

		//Desconsiderar pelo status da OS
		if (count($paramStatusOS)) {
			$sql.= "
                AND
                    ordem_servico.ordstatus NOT IN (".implode(',', $paramStatusOS).")
            ";
		}
		else {
			$sql.= "
                AND
                    ordem_servico.ordstatus NOT IN (3, 9)
            ";
		}

		return $sql;

	}

	/**
	 * Realiza processo de descarte de contatos pendentes
	 * @param UraAtivaContratoVO $contrato
	 * @return boolean
	 */
	public function descartar(UraAtivaContratoVO $contrato) {

		$paramTiposOS 				= (array) $this->param->puaostoid;
		$paramItensOS 				= (array) $this->param->puaitem;
		$paramStatusOS 				= (array) $this->param->puaossoid;
		$paramDefeitosAlegados 		= (array) $this->param->puaotdoid;
		$paramTiposContrato 		= (array) $this->param->puatpcoid;
		$paramStatusContrato 		= (array) $this->param->puacsioid;
		//Rescisão, Rescisão por inadimplência e Pré-Rescisão
		$statusCancelarAssistencia	= array(6, 38, 12);
		$this->motivoLog 			= '';


		$obsHistorico  = "Ura Ativa Assistência, \n";
		$obsHistorico .= "Data/hora cadastro O.S.: " . date("d/m/Y H:i", strtotime($contrato->data_ordem)) . ", \n";
		$obsHistorico .= "Placa: " . $contrato->veiplaca . ",\n";
		$obsHistorico .= "Motivo: ";

		$descricaoMotivoAcaoParametrizada = $this->motivoDescartaAcaoParametrizada($contrato);
		//verifica se o contrato está com status que deve cancelar OS de assistencia.
		//se não está, verifica se está com um status que deve ser descartado, se estiver, descarta.
		//se também não, verifica se deve descartar o contrato pelas regras da ordem de serviço
		if(in_array($contrato->concsioid, $statusCancelarAssistencia)) {
			$retorno = $this->isDescartaOrdemServico($contrato->codigo, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados);
		} else if (in_array($contrato->concsioid, $paramStatusContrato)) {
			echo "Situação 1 - ".$contrato->codigo." - (Descartado por status do contrato = ".$contrato->concsioid.") <br />";
			return true;
		} else {
			$retorno = $this->isDescartaOrdemServico($contrato->codigo, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados);
		}

		if ($retorno == 1) {
			echo "Situação 2 - ".$contrato->codigo." - (Descartado por itens OS)  <br />";
			return true;

		} else if ($retorno == 2) {

			$obs = $obsHistorico . "O.S. diferente de Assistência";

			$this->inserirHistoricoOS($contrato->codigo, $obs);

			$this->cancelarOSItens($contrato);

			$this->motivoLog = "O.S. diferente de Assistência";

			echo "Situação 3 - ".$contrato->codigo." - (Descartado por itens OS, gerado histórico e cancelado serviços)<br />";

			return true;
		} else if (in_array($contrato->concsioid, $statusCancelarAssistencia)) {
			echo "Situação 1 - ".$contrato->codigo." - (Descartado por status do contrato = ".$contrato->concsioid.") <br />";
			return true;
		}


		if ($descricaoMotivoAcaoParametrizada != '') {

			$obs = $obsHistorico . "Ordem de Serviço com Ação = " . $descricaoMotivoAcaoParametrizada;

			$this->inserirHistoricoOS($contrato->codigo, $obs);

			$this->motivoLog = "Ordem de Serviço com Ação = " . $descricaoMotivoAcaoParametrizada;

			echo "Situação 4 - ".$contrato->codigo." - (Descartado por ação parametrizada = ".$descricaoMotivoAcaoParametrizada.") <br />";

			return true;
		}

		if ($this->isDescartaTipoContrato($contrato->conno_tipo, $paramTiposContrato)) {

			$obs = $obsHistorico . "O.S. Tipo Contrato " . $contrato->tpcdescricao;

			$this->inserirHistoricoOS($contrato->codigo, $obs);

			$this->cancelarOSItens($contrato);

			$this->motivoLog = "O.S. Tipo Contrato " . $contrato->tpcdescricao;

			echo "Situação 4 - ".$contrato->codigo." - (Descartado por tipo contrato = ".$contrato->tpcdescricao.") <br />";

			if (in_array($contrato->conno_tipo, $this->recuperarContratosEx())) {
				$this->cancelarOSItens($contrato);
			}

			return true;
		}

		echo "Situação 5 - ".$contrato->codigo." - Enviado <br />";

		return false;
	}

	/**
	 * Desconsiderar ordem de serviço com ação parametrizada
	 * @param UraAtivaContratoVO $contrato
	 * @return string Descrição do motivo que está descartando
	 */
	private function motivoDescartaAcaoParametrizada(UraAtivaContratoVO $contrato) {
		$acoes = (array) $this->param->puaacao;

		if (!count($acoes)) {
			return '';
		}

		$sql = "
			SELECT 		orsstatus, mhcdescricao
			FROM 		ordem_servico
			INNER JOIN 	ordem_situacao ON orsordoid = ordoid
			INNER JOIN 	motivo_hist_corretora ON mhcoid = orsstatus
			WHERE 		ordoid = ".$contrato->codigo."
			ORDER BY 	orsdt_situacao DESC
			LIMIT 1
		";

		$rs = $this->query($sql);

		$status = '';
		$descricao = '';
		if (pg_num_rows($rs) > 0) {
			$row = $this->fetchObject($rs);

			$status = isset($row->orsstatus) ? $row->orsstatus : '';

			if (in_array($status, $acoes)) {
				$descricao = isset($row->mhcdescricao) ? $row->mhcdescricao : '';
			}
		}



		return $descricao;
	}

	/**
	 * Cancela Itens da Ordem de Serviço
	 * Se todos os itens da OS é do tipo Assistência, cancela tbm a OS e as agendas
	 * @param int $ordoid
	 * @return void
	 */
	private function cancelarOSItens(UraAtivaContratoVO $contrato) {

		$ordoid = $contrato->codigo;

		$sql = "SELECT
					ostoid AS tipo,
					ositoid AS id_item_os
				FROM
					ordem_servico
					JOIN ordem_servico_item ON ordoid=ositordoid
					JOIN os_tipo_item ON otioid=ositotioid
					JOIN os_tipo ON otiostoid=ostoid
				WHERE
					ordoid = ".$ordoid."
					AND ositexclusao IS NULL
		            AND ositstatus <> 'X'
		            AND ordstatus <> 9
		";
		$rs = $this->query($sql);

		$totalItens = 0;
		$itensAssistencia = array();
		$itensRetirada = array();

		while ($tipo = pg_fetch_object($rs)) {

			if ($tipo->tipo == "4") { // tipo Assistência
				$itensAssistencia[] = $tipo->id_item_os;
			}
			elseif ($tipo->tipo == "3") { // tipo Retirada
				$itensRetirada[] = $tipo->id_item_os;
			}

			$totalItens++;
		}

		$totalItensAssistencia = (count($itensAssistencia));
		$totalItensRetirada = (count($itensRetirada));
		$totalItensAssistenciaRetirada = ($totalItensAssistencia + $totalItensRetirada);

		if ($totalItens == $totalItensAssistencia) {
			// Cancela os itens de assistencia
			$this->cancelarItensOs($ordoid, $itensAssistencia);

			//Se todos os itens da OS é do tipo Assistência, cancela tbm a OS e as agendas
			$this->cancelarOrdemServico($ordoid);
			$this->cancelarAgendamentoOs($ordoid);
		}
		elseif ($totalItens == $totalItensAssistenciaRetirada) {
			// cancela os itens de assistencia somente se a situação do contrato for Rescisão, Rescisão por Inadimplência ou Pré-Rescisão
			if ($this->isDescartaSituacaoContrato($contrato->connumero)) {
				$this->cancelarItensOs($ordoid, $itensAssistencia);
			}
		}
	}

	/**
	 * RN 6.12
	 * Cancelar os itens da ordem de serviço
	 * @param int $ordoid
	 * @param array $itens Itens da OS que devem ser canceladas
	 * @return void
	 */
	private function cancelarItensOs($ordoid, $itens) {
		if (count($itens)) {
			$sql = "
				UPDATE 	ordem_servico_item
				SET 	ositstatus = 'X'
				WHERE 	ositordoid = ".$ordoid."
				AND		ositoid IN (".implode(',', $itens).")
			";

			$this->query($sql);
		}
	}

	/**
	 * RN 6.13
	 * Cancelar a Ordem de Serviço
	 * @param int $ordoid
	 * @return void
	 */
	private function cancelarOrdemServico($ordoid) {
		$sql = "UPDATE
					ordem_servico
				SET
					ordstatus = 9
				WHERE
					ordoid = ".$ordoid;
		$this->query($sql);
	}

	/**
	 * RN 6.14
	 * Cancelar Agendamento Ordem de serviço
	 * @param $ordoid
	 * @return void
	 */
	private function cancelarAgendamentoOs($ordoid) {
		$sql = "UPDATE
					ordem_servico_agenda
				SET
					osaexclusao = NOW()
				WHERE
					osaordoid = ".$ordoid;
		$this->query($sql);
	}

	/**
	 * RN 6.19
	 * Verificar Tipo de Contrato antes de cancelar Itens O.S do tipo Assistência.
	 * @param int $connumero
	 * @return boolean
	 */
	private function isDescartaSituacaoContrato($connumero) {
		$sql = "
			SELECT 	COUNT(1) AS total
			FROM 	contrato
			WHERE 	concsioid IN (6, 12, 38)
			AND 	connumero = ".$connumero;

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$total = isset($row->total) ? $row->total : false;

		return (boolean) $total;
	}

	/**
	 * Realiza tratamentos necessários para a assistencia após realizar todas as regras de descarte
	 * @param UraAtivaContratoVO $contrato
	 * @return void
	 */
	protected function tratar(UraAtivaContratoVO $contrato) {

		//Recupera as OS do cliente
		$osCliente = $this->osCliente[$contrato->conclioid];

		foreach($osCliente as $ordoid){

			$this->atualizarDataEnvioDiscador($ordoid);

			$this->setarUsuarioTratamento($ordoid);

		}
	}

	/**
	 * Atualiza a data de envio ao discador na OS
	 * @param int $ordoid
	 * @param boolean $isAtualizaData
	 */
	protected function atualizarDataEnvioDiscador($ordoid, $isAtualizaData=true) {

		$ordoid = (int)$ordoid;

		if ($isAtualizaData) {

			$sql = "
				UPDATE
						ordem_servico
				SET
						orddt_envio_discador = NOW()
				WHERE
						ordoid = $ordoid
			";

		}
		else {

			$sql = "
				UPDATE
						ordem_servico
				SET
						orddt_envio_discador = NULL
				WHERE
						ordoid = $ordoid
			";

		}

		$this->query($sql);
	}

	/**
	 * Atualiza o usuário URA ATIVA como atendente da OS
	 * @param int $ordoid
	 */
	protected function setarUsuarioTratamento($ordoid) {

		$ordoid = (int)$ordoid;
		$usuoid = $this->usuoid;

		$sql = "UPDATE
					ordem_servico
				SET
					ordacomp_usuoid = $usuoid
				WHERE
					ordoid = $ordoid";

		$this->query($sql);
	}

	/**
	 * Limpa a data da agenda na OS para não enviar novamente na próxima execução
	 * @param UraAtivaContratoVO $contrato
	 * @return void
	 */
	public function limparDataAgendaOS(UraAtivaContratoVO $contrato) {

		$ordoid = $contrato->codigo;

		$sql = "UPDATE
					ordem_servico
				SET
					orddt_agenda_discador = NULL, ordstatus_agenda_discador = NULL
				WHERE
					ordoid = $ordoid";

		$this->query($sql);
	}

	/**
	 * Busca os telefones para contato com o cliente
	 * @param UraAtivaContratoVO $contrato
	 * @return array:UraAtivaContatoVo
	 */
	protected function buscarTelefones(UraAtivaContratoVO $contrato) {

		$telefones1 = $this->buscarTelefonesInstalacaoAssistencia($contrato);
		$telefones2 = $this->buscarTelefonesAutorizado($contrato);

		return array_merge($telefones1, $telefones2);
	}

	protected function buscarTelefonesInstalacaoAssistencia(UraAtivaContratoVO $contrato) {

		$sql = "SELECT 		'3' as tipo,
							clicoid AS id_telefone_externo,
							clicclioid AS id_contato_externo,
							clicnome as nome,
							clicconnumero as connumero,
							clicfone,
							clicfone_array AS telefones
				FROM 		cliente_contato
				WHERE 		clicconnumero = ".$contrato->connumero."
				AND 		clicexclusao IS NULL
				ORDER BY 	clicdt_cadastro";

		$rs = $this->query($sql);

		$telefones = array();
		$telefonesDoContato = array();
		$telefonesInclusos = array();

		while ($row = pg_fetch_object($rs)) {

			$telefonesDoContato = $this->buildArray($row->telefones);
			$telefonesDoContato[] = $row->clicfone;

			unset($row->telefones);
			unset($row->clicfone);

			foreach ($telefonesDoContato as $telefone) {

				$telefone = $this->tratarNumeroTelefone($telefone);

				// se não é vazio e ainda não foi incluso para o mesmo contato
				if ($telefone != '' && (!in_array($telefone, $telefonesInclusos))) {

					$row->telefone = '0' . $telefone;
					$row->id_telefone_externo = $this->sequencialTelefone;
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

	protected function buscarTelefonesAutorizado(UraAtivaContratoVO $contrato) {

		$sql = "
			SELECT 		'3' AS tipo, tctconnumero AS connumero, tctcontato AS nome, tctdt_cadastro AS cadastro,
						tctoid AS id_telefone_externo,
						conclioid AS id_contato_externo,
						(tctno_ddd_res || tctno_fone_res) AS res,
						(tctno_ddd_com || tctno_fone_com) AS com,
						(tctno_ddd_cel || tctno_fone_cel) AS cel
			FROM 		telefone_contato
			INNER JOIN  contrato ON tctconnumero = connumero
			WHERE  		tctorigem = 'A'
			AND 		tctconnumero = ".$contrato->connumero."
		";


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
				if ($telefone != '' && (!in_array($telefone, $telefonesInclusos))) {
					$row->id_telefone_externo = $this->sequencialTelefone;
					$row->telefone = '0' . $telefone;
					$row->nome = utf8_encode($row->nome); //@TODO Acentos estão vindo com problema no nome
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

	public function buscarInformacoesAssistencia($clioid) {
		$clioid = (int)$clioid;
		$os = $this->buscarEnviosDiscadorCliente($clioid);
		$sql = "SELECT distinct ordoid,veiplaca,vplplaca,vplplaca_maq,vplplaca_tra
				FROM ordem_servico
				LEFT JOIN ordem_servico_item ON ordoid=ositordoid
				LEFT JOIN os_tipo_item ON otioid=ositotioid
				LEFT JOIN os_tipo ON otiostoid=ostoid
				LEFT JOIN veiculo v on v.veioid = ordveioid
				LEFT JOIN veiculo_placa vp on v.veiplaca = vp.vplplaca
				or v.veiplaca = vp.vplplaca_maq
				or v.veiplaca = vp.vplplaca_tra
				WHERE ordoid IN (".$os.")";
		$res = $this->query($sql);
		$resultado = array();
		if (pg_num_rows($res) > 0) {
			while($row = pg_fetch_array($res)) {
				array_push($resultado, $row);
			}
		}
		return $resultado;
	}

	/**
	 * Busca o total de ordens de serviços pendentes:
	 * @param int $clioid
	 * @return int $total
	 */
	public function buscarTotalOrdensServicos($clioid){

		$clioid = (int)$clioid;
		$StatusOrdemServico = array(1,4); //Autorizado , Aguardando Autorizacao
		$tipoAssistencia = 4;

		$sql = "SELECT ordoid, connumero, veiplaca
				FROM ordem_servico
				INNER JOIN ordem_servico_item ON ordoid=ositordoid
				INNER JOIN os_tipo_item ON otioid=ositotioid
				INNER JOIN os_tipo ON otiostoid=ostoid
				LEFT  JOIN veiculo ON veioid = ordveioid
				WHERE ordstatus in (".implode(',', $StatusOrdemServico).")
				AND orddt_envio_discador is not NULL
				AND ostoid = 4
				AND ordclioid = ".$clioid."
				ORDER BY ordoid";

		$rs = $this->query($sql);
		$total = pg_num_rows($rs);
		$row = $this->fetchObject($rs);

		return $total;
	}

	/**
	 * Busca todas as placas das O.S de assistência pendentes de agendamento do cliente.
	 * @param int $clioid
	 * @return string $placas
	 */
	public function buscarPlacas($clioid) {

		$clioid = (int)$clioid;
		$StatusOrdemServico = array(1,4); //Autorizado , Aguardando Autorizacao
		$tipoAssistencia = 4;

		$sql = "SELECT STRING_AGG(veiplaca, ';') AS placas
				FROM veiculo
				WHERE veioid IN (
					SELECT ordveioid FROM ordem_servico
					INNER JOIN ordem_servico_item ON ordoid=ositordoid
					INNER JOIN os_tipo_item ON otioid=ositotioid
					INNER JOIN os_tipo ON otiostoid=ostoid
					WHERE ordstatus IN (".implode(',', $StatusOrdemServico).")
					AND ostoid = ".$tipoAssistencia."
					AND ordclioid =  ".$clioid.")";

		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$placas = isset($row->placas) ? $row->placas : '';

		return $placas;

	}

	/**
	 * O sistema deve alterar a ação de todas as O.S. de assistência pendentes do cliente
	 * @param int $clioid
	 * @param string $telefoneContato
	 * @param date $data
	 * @param time $hora
	 * @param string $os
	 * @return void
	 */
	public function reagendarOSCliente($clioid, $telefoneContato, $data='', $hora='', $os='', $mhcoid='') {

        $listaOsSimilar = array();
        $listaOrdemServico = array();

		$listaOrdemServico = $this->buscarOSCliente($clioid, $os);
		$insucesso = false;

		if (empty($listaOrdemServico)) {
			throw new Exception("0170");
		}
		if ($mhcoid == '') {
			$mhcoid = $this->getIdClienteNaoLocalizado();
			$insucesso = true;
		}

        //Insere histórico para as OSs relativas aos contatos enviados ao discador e as similares [regra 6.6]
		foreach($listaOrdemServico as $ordemServico) {

			$obsHistorico  = "Ura Ativa,\n";
			$obsHistorico .= "Data/hora cadastro O.S.: " . date("d/m/Y H:i", strtotime($ordemServico->orddt_ordem)) . ",\n";
			$obsHistorico .= "Placa: " . $ordemServico->veiplaca . ",\n";
			$obsHistorico .= "Telefones de Contato: " . $telefoneContato . ",\n";
			$obsHistorico .= $insucesso ? "Motivo: Cliente não localizado, \n" : "Motivo: Próximo contato URA, \n";
  			$obsHistorico .= "Data agendamento: " . date("d/m/Y", strtotime($data));

            $this->atualizarDataEnvioDiscador($ordemServico->codigo, false);

            //Verificar se há OSs similares à OS do laço.
             $listaOsSimilar = $this->buscarOSsimilares($clioid, $ordemServico->ordstatus, $os, $ordemServico->ostoid);

             //Replica o histórico nas OS similares (mesmo cliente, status e tipo)
             foreach($listaOsSimilar as $OsSimilar){

                 $this->inserirHistoricoOS($OsSimilar->ordoid, $obsHistorico, $mhcoid, $data, $hora);
             }
		}
	}

    /**
     * Busca as Ordens de Serviços similares às enviadas ao discador: mesmo status e tipo
     * @param int $clioid
     * @param int $statusOs
     * @param string $ordemServico
     * @param int $tipoOs
     * @return array
     */
    private function buscarOSsimilares($clioid, $statusOs, $ordemServico, $tipoOs){

        $sql = "
            SELECT
                    DISTINCT ON(ordoid) ordoid
            FROM
                    ordem_servico
            INNER JOIN
                    ordem_servico_item ON ositordoid = ordoid
            INNER JOIN
                    os_tipo_item ON  ositotioid = otioid
            INNER JOIN
                    os_tipo ON  ostoid = otiostoid
            WHERE
                    ordclioid = ". intval($clioid) ."
            AND
                    ordstatus = ". intval($statusOs) . "
            AND
                    ostoid = ". intval($tipoOs) ;


		$rs = $this->query($sql);

		return $this->fetchObjects($rs);

    }

    /**
	 * Busca as OSs do cliente
	 * @param int $clioid
	 * @return array:AtendimentoVO
	 */
	private function buscarOSCliente($clioid, $os) {

		$sql = "
			SELECT
                    DISTINCT ON(ordoid) ordoid,
                    ordoid AS codigo,
                    ordconnumero AS connumero,
                    orddt_ordem,
                    veiplaca,
                    ordstatus,
                    ostoid
			FROM
                    ordem_servico
            INNER JOIN
                    ordem_servico_item ON ositordoid = ordoid
            INNER JOIN
                    os_tipo_item ON  ositotioid = otioid
            INNER JOIN
                    os_tipo ON  ostoid = otiostoid
			INNER JOIN
                    veiculo ON ordveioid = veioid
			WHERE
                    ordclioid = ". intval($clioid)."
			AND
                    ordoid IN (".$os.")
		";
		$rs = $this->query($sql);

		return $this->fetchObjects($rs);
	}

	/**
	 * Busca o OID do Agendamento Próximo Contato URA
	 * @return int
	 * @throws Exception
	 */
    public function getIdProximoContato() {

		$sql = "SELECT mhcoid FROM motivo_hist_corretora WHERE mhcdescricao ILIKE '%Agendamento_Pr%ximo_Contato_URA%'";

		$rs = $this->query($sql);

		if (pg_num_rows($rs) == 0) {
			throw new Exception('O status "Agendamento Próximo Contato URA" não existe.');
		}

    	$row = pg_fetch_object($rs);

    	return $row->mhcoid;
    }

    /**
     * Busca o OID do Inst/Assist. Agendada
     * @return int
     * @throws Exception
     */
    public function recuperarIdInstAssistAgendada() {
    	$sql = "SELECT mhcoid FROM motivo_hist_corretora WHERE mhcdescricao ILIKE 'Inst/Assist. Agendada'";
    	$rs = $this->query($sql);

    	if (pg_num_rows($rs) == 0) {
    		throw new Exception('O status "Inst/Assist. Agendada" não existe.');
    	}

    	$row = pg_fetch_object($rs);

    	return $row->mhcoid;
    }


    /**
     * Busca o OID do Agendamento Próximo Contato URA
     * @return int
     * @throws Exception
     */
    private function getIdClienteNaoLocalizado() {

    	$sql = "SELECT * FROM motivo_hist_corretora WHERE mhcdescricao ILIKE 'Cliente_n%o_localizado'";

    	$rs = $this->query($sql);

    	if (pg_num_rows($rs) == 0) {
    		throw new Exception('O status "Cliente Não Localizado" não existe.');
    	}

    	$row = pg_fetch_object($rs);

    	return $row->mhcoid;
    }

	public function tratarInsucessos($insucessos) {
		$status = true;

		if($insucessos = $this->buscarTentativasInsucessoContato()) {
			while($insucesso = pg_fetch_object($insucessos)) {

				if($insucesso->qtd_contatos == $insucesso->qtd_tentativas) {
					$cliente       = $insucesso->cliente;
					$contato       = $insucesso->contato;
					$telefone      = $insucesso->telefone;
					$ordem_servico = $insucesso->ordem_servico;
					$historico     = $this->buscarTempoReagendamentoOS($cliente, $ordem_servico);
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
						$this->reagendarOSCliente($cliente, $telefone, $data->data, $data->hora, $ordem_servico);
					}

					$this->excluirEnviosDiscador($contato);
					$this->removerInsucessoContato($cliente);
				}
			}
		} else {
			$status = false;
		}

		if($envios = $this->buscarEnviosDiscador()) {
			while($envio = pg_fetch_object($envios)) {

				$insucesso = $this->buscarInsucessos($envio->cduaid_contato_discador, $envio->cduaclioid);

				if(!empty($insucesso)) {
					$telefone  = $insucesso['telefones'];
					$cliente   = $insucesso['contatoExterno'];
					$historico = $this->buscarTempoReagendamentoOS($cliente, $envio->cdua_os);
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
						$this->reagendarOSCliente($cliente, $telefone, $data->data, $data->hora, $envio->cdua_os);
					}

					$this->excluirEnviosDiscador($envio->cduaid_contato_discador);
					$this->removerInsucessoContato($cliente);
				}

				if($this->isGravaLogAtendimento) {
					$clinome = $this->getNomeCliente($envio->cduaclioid);
					$this->logAtendimento['insucessos'][] = $envio->cdua_os . " | " . $envio->cduaclioid . " | " . $clinome;
				}
			}
		} else {
			$status = false;
		}

		return $status;
	}

    /**
     * Busca os contatos que foram enviados para o discador na tabela auxiliar
     * @return multitype:
     */
    public function buscarEnviosDiscador($clioid = '') {
    	$sql = "SELECT cduaid_contato_discador, cduaclioid, cdua_os
    			FROM contato_discador_ura_assistencia";

    	if (!empty($clioid)) {
    		$sql .= " WHERE cduaclioid = ".$clioid;
    	}

    	$query = $this->query($sql);

    	if ($query) {
    		if (pg_num_rows($query) > 0)
    			return $query;
    		else
    			return false;
    	} else {
    		return false;
    	}
    }

    public function buscarEnviosDiscadorCliente($clioid) {

    	$clioid = (int) $clioid;

    	$sql = "SELECT cdua_os
    			FROM contato_discador_ura_assistencia
    			WHERE cduaclioid = ".$clioid;

    	if ($query = $this->query($sql)) {
    		if (pg_num_rows($query) > 0) {
    			$row = $this->fetchObject($query);

    			$osCliente = $row->cdua_os;
    		}
    	}

    	if (empty($osCliente)) {
    		throw new Exception("0170");
    	}

    	return $osCliente;
    }


    public function excluirEnviosDiscador($idContatoDiscador) {
    	$id = (int)$idContatoDiscador;
    	$sql = "DELETE FROM contato_discador_ura_assistencia WHERE cduaid_contato_discador = ".$id;

    	$query = $this->query($sql);
    	if ($query)
    		return true;
    	return false;
    }

    public function excluirEnviosDiscadorPorCliente($clioid) {
    	$id = (int)$clioid;
    	$sql = "DELETE FROM contato_discador_ura_assistencia WHERE cduaclioid = ".$id;
    	$query = $this->query($sql);
    	if ($query)
    		return true;
    	return false;
    }


    protected function afterInserirDiscador($contato, $contrato, $idDiscador, $codigoIdentificador) {

    	if(!in_array($contrato->conclioid, $this->inseridosTabelaAuxiliar)){

	    	$this->inserirRegistroAuxiliarDiscador($contato, $contrato, $idDiscador, $codigoIdentificador);

	    	$this->tratar($contrato);

    		array_push($this->inseridosTabelaAuxiliar, $contrato->conclioid);

		}

		if($this->cronReenvio){

			$this->tratar($contrato);

			$this->atualizarDataInsucessoContato($codigoIdentificador, $contato->telefone);
		}
		else if(!$this->verificarTentativasInsucessoContato($contato->telefone)){

			 $this->inserirInsucessoContato($codigoIdentificador, $contato);

		}

    }

    /**
     * Busca contato específico na tabela auxiliar do discador
     * @param integer $idregistro
     * @return integer
     */
    public function buscarContatoDiscadorEspecifico($idregistro){

    	$idregistro = (int)$idregistro;

    	$sql = "SELECT cduaid_contato_discador AS id_contato
    	FROM  contato_discador_ura_assistencia
    	WHERE cduaclioid = $idregistro
    	LIMIT 1";

    	$rs = $this->query($sql);
    	$row = $this->fetchObject($rs);

    	$id_contato = isset($row->id_contato) ? $row->id_contato : 0;

    	return $id_contato;
    }

    /**
     * Desconsiderar por Ordens de Serviço
     * @param int $connumero
     * @param array $paramTiposOS
     * @param array $paramItensOS
     * @param array $paramStatusOS
     * @param array $paramDefeitosAlegados
     * @return int 1 - Ignorar OS; 2 - Retirada + Assistência;  3 - Assistência + Parametrizada ou apenas Assistência
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
    			return 1;
    		} else {
    			if ($this->verificarTiposOs($ordoid)) {
    				return 2;
    			} else {
    				return 3;
    			}
    		}
    	}
    }

    /**
     * Verifica se os tipos da OS são apenas Retirada e Assistência
     * @param int $ordoid
     * @return boolean
     */
    private function verificarTiposOs($ordoid) {
    	$retirada = 0;
    	$assistencia = 0;
    	$outros = 0;

    	$sql = 		"SELECT
    						ostoid
					FROM
    						ordem_servico
					INNER JOIN
    						ordem_servico_item ON ositordoid = ordoid
					INNER JOIN
    						os_tipo_item	ON otioid =  ositotioid
					INNER JOIN
    						os_tipo ON otiostoid = ostoid
					LEFT JOIN
    						ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
					LEFT JOIN
    						os_tipo_defeito ON otdoid= osdfotdoid
					WHERE
    						ordoid = ".$ordoid;
    	$query = $this->query($sql);

    	$tipos = $this->fetchObjects($query);

    	foreach ($tipos as $tipo) {
    		if ($tipo->ostoid == 4) {
    			$assistencia++;
    		} else if ($tipo->ostoid == 3) {
    			$retirada++;
    		} else {
    			$outros++;
    		}
    	}

    	if ($retirada > 0 && $assistencia > 0 && $outros == 0) {
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * Recupera o ID de todos os contratos com tipo 'Ex-XXXX'
     * @return array
     */
    private function recuperarContratosEx() {
    	$resultados = array();
    	$sql = "SELECT
    					tpcoid
    			FROM
    					tipo_contrato
    			WHERE
    					tpcdescricao ILIKE 'Ex-%'";
    	$query = $this->query($sql);
    	while ($row = pg_fetch_object($query)) {
    		array_push($resultados, $row->tpcoid);
    	}
    	return $resultados;
    }


    /**
     * Verifica se o contato já esta na tabela de controle de Insucessos
     * Implementação da classe abstrata
     * @see UraAtivaDAO::verificarInsucessoContato()
     */
    public function verificarInsucessoContato($clioid){

    	$existe = false;
    	$clioid = (int)$clioid;

    	$sql = "
    			SELECT EXISTS
    						(
			    			SELECT
			    					cduaaclioid
			    			FROM
			    					contato_discador_ura_assistencia_aux
			    			WHERE
			    					cduaaclioid = ". $clioid ."
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
     * Implementação da classe abstrata
     * @see UraAtivaDAO::inserirInsucessoContato()
     */
    public function inserirInsucessoContato($codigoIdentificador, UraAtivaContatoVO $contato){

    	$retorno = false;

    	$sql = "INSERT INTO
    					contato_discador_ura_assistencia_aux
    					(
    						cduaaclioid,
    						cduaatelefone,
    						cduaatentativas,
    						cduaadt_ultima_tentativa,
    			 			cduaaid_telefone_externo,
    						cduaatipo_contato
    					)
    			VALUES
    					(
    						". $codigoIdentificador .",
    						'". $contato->telefone ."',
    						0,
    						NOW(),
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
     * Implementação da classe abstrata
     * @see UraAtivaDAO::atualizarInsucessoContato()
     */
    public function atualizarTentativaInsucessoContato ($codigoIdentificador, $telefone){

    	$retorno = false;

    	$codigoIdentificador = (int)$codigoIdentificador;

    	if(empty($codigoIdentificador)){
    		return $retorno;
    	}

    	$sql = "UPDATE
    					contato_discador_ura_assistencia_aux
    			SET
    					cduaatentativas = 1
    			WHERE
    					cduaaclioid = ". $codigoIdentificador ."
    			AND
    					cduaatelefone = '". $telefone ."'
    			AND
    					cduaatentativas = 0
 			";

    	$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;

    }

    /**
     * Deleta o contato na tabela de controle de insucessos
     * Implementação da classe abstrata
     * @see UraAtivaDAO::removerInsucessoContato()
     */
    public function removerInsucessoContato ($codigoIdentificador){

    	$retorno = false;
    	$codigoIdentificador = (int)$codigoIdentificador;

    	if(empty($codigoIdentificador)){
    		return $retorno;
    	}

    	$sql = "DELETE FROM
    					contato_discador_ura_assistencia_aux
    			WHERE
    					cduaaclioid = ". $codigoIdentificador ."
    			";

    	$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
    }

    /**
     * Verifica se o contato já esta na tabela de controle de Insucessos
     * Implementação da classe abstrata
     * @see UraAtivaDAO::verificarTentativasInsucessoContato()
     */
    public function verificarTentativasInsucessoContato($telefone){

    	$sql = "
    			SELECT
    					cduaadt_ultima_tentativa
    			FROM
    					contato_discador_ura_assistencia_aux
    			WHERE
    					cduaatelefone = '". $telefone ."'
    			";

    	$recordSet = $this->query($sql);
    	$tupla = pg_fetch_object($recordSet);

    	$total = isset($tupla->cduaadt_ultima_tentativa) ? $tupla->cduaadt_ultima_tentativa : 0;

    	return $total;

    }

	public function buscarTentativasInsucessoContato() {
		$sql = "
			SELECT
				cduaaclioid AS cliente,
				cduaid_contato_discador AS contato,
				cdua_os AS ordem_servico,
				STRING_AGG(cduaatelefone, ',') AS telefone,
				COUNT(cduaaoid) AS qtd_contatos,
				SUM(cduaatentativas) AS qtd_tentativas
			FROM
				contato_discador_ura_assistencia_aux
					INNER JOIN
						contato_discador_ura_assistencia ON cduaaclioid = cduaclioid
			GROUP BY
				cduaaclioid,
				cduaid_contato_discador,
				cdua_os
		";

		return $this->query($sql);
	}



	/**
	 *  Atualiza a data de envio do contato na tabela de controle de insucessos
	 */
	public function atualizarDataInsucessoContato($codigoIdentificador, $telefone){

		$retorno = false;

		$codigoIdentificador = (int)$codigoIdentificador;

		if(empty($codigoIdentificador)){
			return $retorno;
		}

		$sql = "UPDATE
    					contato_discador_ura_assistencia_aux
    			SET
    					cduaadt_ultima_tentativa = NOW()
    			WHERE
    					cduaaclioid = ". $codigoIdentificador ."
    			AND
    					cduaatelefone = '". $telefone ."'
    			";

		$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;
	}

	/**
	 * (non-PHPdoc)
	 * @see UraAtivaDAO::buscarContatosReenvio()
	 */
	public function buscarContatosReenvio() {

		$contatos = array();
		$ContatosCiente = array();
		$ordemServico = array();

		$sql = "
			SELECT
				cliente AS id_contato_externo,
				telefone,
				id_telefone AS id_telefone_externo,
				tipo_contato AS tipo
			FROM
				(
					SELECT
						cduaaclioid AS cliente,
						cduaatelefone AS telefone,
						cduaaid_telefone_externo AS id_telefone,
						cduaatentativas AS tentativa,
						cduaadt_ultima_tentativa AS dt_tentativa,
						SUM(cduaatentativas) OVER w_cduaa AS qtd_tentativas,
						cduaatipo_contato AS tipo_contato,
						COUNT(cduaaoid) OVER w_cduaa AS qtd_contatos
					FROM
						contato_discador_ura_assistencia_aux
					WINDOW
						w_cduaa AS (
							PARTITION BY cduaaclioid
						)
				) AS cduaa
			WHERE
				tentativa = 0
			AND
				qtd_tentativas > 0
			AND
				qtd_tentativas != qtd_contatos
		";

		$rs = $this->query($sql);

		while($tupla = pg_fetch_object($rs)) {

			$contatos[$tupla->id_contato_externo][] = new UraAtivaContatoVO($tupla);

			//Cria um objeto com todas as OS por cliente ---- inicio
			$recordSet = $this->buscarEnviosDiscador($tupla->id_contato_externo);

			$tuplaOs = pg_fetch_object($recordSet);

			if($tuplaOs){

				$ordemServico = isset($tuplaOs->cdua_os) ? explode(',', $tuplaOs->cdua_os) : array();

				if(!empty($ordemServico)){

					foreach($ordemServico as $os){

						$this->osCliente[$tupla->id_contato_externo][] = $os;
					}
				}
			}
			//Cria um objeto com todas as OS por cliente ---- Fim
		}
		//Fim: $tupla

		return $contatos;
	}

	/**
	 * Atualiza o numero de tentativas em um contato na tabela de controle de insucessos
	 * @param int $codigoIdentificador
	 * @return boolean
	 */
	public function atualizarTentativaInsucessoCliente ($codigoIdentificador){

		$retorno = false;

		$idTelefoneExterno = $this->buscarInsucessoEspecifico($codigoIdentificador);

		$codigoIdentificador = (int)$codigoIdentificador;

		if(empty($codigoIdentificador) || empty($idTelefoneExterno)){
			return $retorno;
		}

		$sql = "UPDATE
    					contato_discador_ura_assistencia_aux
    			SET
    					cduaatentativas = 1
    			WHERE
    					cduaaclioid = ". $codigoIdentificador ."
    			AND
    					cduaaid_telefone_externo IN (". $idTelefoneExterno .")
				AND
    					cduaatentativas = 0
    			";

		$rs = $this->query($sql);
		$retorno = pg_affected_rows($rs);

		return (boolean)$retorno;

	}

}