<?php
/**
 * Classe para tela contrato Aba Serviços
 * 
 * @file PrnContratoServicosDAO.php
 * @author rafael.silva
 * @version 06/05/2013
 * @since 06/05/2013
 * @package SASCAR PrnContratoServicosDAO.php 
 */


class PrnContratoServicosDAO{

	private $conn;

	/**
	 * Construtor
	 */
	public function __construct($conn) {

		$this->conn = $conn;
	}
	
	/**
	 * Carrega dados do contrato
	 */
	public function getDadosContrato($connumero){
		
		$sql = "select conveioid, conclioid, cliemail from contrato, clientes
				where conclioid = clioid
					and connumero = $connumero";

		$query	 = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);

		return $retorno[0];
	}
	
	/**
	 * Carrega dados da Ordem de Serviço
	 *
	 * @autor Rafael Silva
	 * @email rafaelbarbetasilva@brq.com
	 */
	public function carregaDadosOS($connumero){

		$sql = "SELECT DISTINCT
					ordem_servico.ordoid,
					ordem_servico.ordstatus,
					os_tipo_item.otiostoid,
					os_tipo_item.otidescricao,
					ordem_servico.ordaceita_cobranca
				FROM ordem_servico
					JOIN ordem_servico_item ON ordem_servico_item.ositordoid = ordem_servico.ordoid
					JOIN os_tipo_item ON os_tipo_item.otioid = ordem_servico_item.ositotioid
				WHERE ordem_servico.ordconnumero = $connumero
					AND ordem_servico.ordstatus = 4 
					AND os_tipo_item.otiostoid in (3,4)";
					
		$retorno = pg_query($this->conn, $sql);
		$contOS  = pg_num_rows($retorno);
		
		$retorno = ($contOS == 0) ? 0 : $retorno;

		return $retorno;
	}
	
	public function carregaServicosOS($ordemID){
		
		$sql = "SELECT 
					ordstatus, ordrelroid, ordconnumero, coneqcoid, conequoid, condt_ini_vigencia,
					ositotioid, ositstatus, otiostoid, ositoid, ositordoid, ositatend_ponto_fora,
					ositosdfoid_alegado, ositosdfoid_analisado,
					(CASE WHEN conveioid IS NULL THEN ordveioid ELSE conveioid END) AS conveioid
				FROM ordem_servico 
					INNER JOIN contrato ON ordconnumero = connumero 
					inner join ordem_servico_item on ositordoid = ordoid
					inner join os_tipo_item on ositotioid = otioid 
				WHERE ordoid = " . $ordemID;
		
		$queryOS = pg_query($this->conn, $sql);
		
		return $queryOS;
	}
	
	// Atualizar a OS com o representante informado no contrato
	public function atualizaOrdemServico($idInstalador, $OSExcluir){
		
		$sql = "SELECT itlnome, 
					(SELECT relroid from relacionamento_representante where relrrepoid = itlrepoid limit 1 ) as relroid_principal, 
					(SELECT relroid from relacionamento_representante where  relrrep_terceirooid = itlrepoid limit 1) as relroid_terceiro 
				FROM instalador
					where itloid = $idInstalador";

		$query = pg_query($this->conn, $sql);

		$ordrelroid = (pg_fetch_result($query, 0, 'relroid_principal') != "") ? 
							pg_fetch_result($query, 0, 'relroid_terceiro') : 
							pg_fetch_result($query, 0, 'relroid_principal');
			
		$sql = "UPDATE ordem_servico 
					SET orditloid = $idInstalador, ordrelroid = $ordrelroid
				where ordoid = $OSExcluir";
				
		pg_query($this->conn, $sql);
	}
		
	// Verificar se o servico possui defeito alegado, e se o defeito analisado está em branco
	public function confereDefeito($dados){

		$sql = "SELECT ositoid FROM ordem_servico_item
				WHERE ositstatus IN('A','P')
					AND ositordoid = ".$dados['ositordoid']." 
					AND ositoid = ".$dados['ositoid']." 
					AND ositosdfoid_alegado IS NOT NULL 
					AND ositosdfoid_analisado IS NULL";

		$rs = pg_query($this->conn, $sql);
	}

	public function atualizaStatusServico($item, $motivo){
		
		$sqlMotivo = '';
		
		if($motivo){  $sqlMotivo = " ositotioid = ".$motivo.",";  }
		
		$sql  = "UPDATE ordem_servico_item SET ";
		$sql .= $sqlMotivo;
		$sql .= "	ositstatus = 'C', 
					ositatend_ponto_fora = null
				WHERE ositoid = " . $item . " AND ositstatus IN('A','P') AND ositexclusao IS NULL;";

        $query = pg_query($this->conn, $sql);
		
		return $query;
	}
	
	// Alterar o contrato adicionando o veiculo e salvar o historico
	public function contratoHistorico($veioid, $connumero, $cd_usuario){
	
		$obs = "Alteração cadastro do termo: Inclusão do veículo " . $veioid . " via ordem de serviço.";
		
		$sql = "UPDATE contrato SET conveioid=" . $veioid . " WHERE connumero = " . $connumero;
		pg_query($this->conn, $sql);
		
		$sql = "SELECT historico_termo_i(".$connumero.", ".$cd_usuario.", '$obs');";
		pg_query($this->conn, $sql);
	}

	public function atualizaGarantia($novaGarantia, $contrato){
		$sql = "UPDATE contrato SET congarantia = $nova_garantia
				WHERE connumero = ".$contrato;

		pg_query($this->conn, $sql);
	}
	
	
	/**
	 * REGRA:
	 * Ao concluir uma OS de retirada, se existir uma OS de reinstalação pendente para o mesmo contrato, 
	 * atualizar o veículo do contrato para a placa da que consta na OS de reinstalação.
	 */
	function adequacoesPortal($OSExcluir, $connumero){
		
		$sql = "SELECT count(*) as aux 
				 FROM ordem_servico_item, os_tipo_item, os_tipo, ordem_servico
				WHERE ositordoid = ordoid 
				  AND otioid = ositotioid 
				  AND ostoid = otiostoid 
				  AND otidescricao ILIKE '%RETIRADA%' 
				  AND ordoid=$OSExcluir";

		$rs = pg_query($this->conn, $sql);

		if (pg_fetch_result($rs, 0, 'aux') > 0 && $connumero > 0) {

			$sql2 = "SELECT ordveioid 
					  FROM ordem_servico_item, os_tipo_item, os_tipo, ordem_servico
					 WHERE ositordoid = ordoid 
					   AND otioid = ositotioid 
					   AND ostoid = otiostoid 
					   AND otidescricao ILIKE '%REINSTALAÇÃO%' 
					   AND ordstatus IN (1,4)
					   AND ordconnumero=$connumero";

			$rs2 = pg_query($this->conn, $sql2);

			if (pg_num_rows($rs2) > 0){

				$ordveioid = pg_fetch_result($rs2, 0, 'ordveioid');

				if ($ordveioid > 0){
					$sql3 = "UPDATE contrato SET conveioid=$ordveioid
							 WHERE connumero=$connumero";
					pg_query($this->conn, $sql3);
				}
			}
		}
	}


	function fecharOrdemServico($ordem){
		
		$sql = "UPDATE ordem_servico SET ordstatus = 3 WHERE ordoid = " . $ordem;
		
		pg_query($this->conn, $sql);
	}

	
	function comissaoTecnica($parametros){

		$sql = "SELECT comissao_tecnica_i('$parametros') as retorno;";
		$query = pg_query($this->conn, $sql);
		
		return $query;
	}
	
	function getNotificacaoEnviada($connumero, $msg){
	
		$sql = "SELECT count(*) as hist FROM historico_termo 
				WHERE hitconnumero = $connumero AND hitobs ILIKE '$msg'";

		$query = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);

		return (int) $retorno[0]['hist'];
}

	function setHistoricoEmailRetencao($connumero, $cd_usuario, $msg){

		$sql = "SELECT historico_termo_i($connumero, $cd_usuario, '$msg')";
		$query = pg_query($this->conn, $sql);

	}

	// carrega titulo do email
	public function getTituloParamSiggo($nome){
		
		$sql = "select parsvalor from parametros_siggo where parsnome = '$nome'";

		$query	 = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);

		return $retorno[0]['parsvalor'];
	}

}

//fim arquivos
?>