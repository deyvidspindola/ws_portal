<?php
/**
 * Classe consultas processos Cron de ordem de serviço
 * 
 * @author Rafael Barbeta Silva <rafaelbarbetasilva@brq.com>
 * @version 01/07/2013
 * @package Cron
 */
class ordemServicoDAO{

	private $conn;

	public function __construct($conn){
		$this->conn = $conn;
	}

	public function getOsCobranca(){

		$sql = "SELECT
					ordoid, ordclioid
				FROM ordem_servico
				WHERE 
					ordstatus = 10
				AND (orddt_ordem <= now()- interval '1:00')
				ORDER by ordoid
			";

		$query   = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);
		
		return $retorno;
	}

	
	public function getOsAgendamento(){

		$sql = "SELECT 
					ordoid, osadata, prptppoid,
					(select sub.osahora from ordem_servico_agenda as sub where sub.osaordoid = ordoid order by sub.osaoid asc limit 1) as hora, 
					conno_tipo, veiplaca, osaendereco||' '||osaobservacao as local, cliemail
				FROM 
					ordem_servico_agenda, ordem_servico, contrato, proposta, veiculo, clientes
				WHERE osaordoid = ordoid
					AND ordveioid = veioid
					AND connumero = ordconnumero
					AND connumero = prptermo
					AND conclioid = clioid
					AND osadata = now()::date+1
					AND osaexclusao IS NULL
					AND osausuoid_excl IS NULL
				GROUP BY ordoid, osadata, prptppoid, hora, conno_tipo, veiplaca, local, cliemail
				ORDER BY osadata DESC";

		$query   = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);
		
		return $retorno;
	}
	
	// Busca histórico de envio de email para OS
	public function getEmailOSAgenda($ordoid){
		
		$sql = "SELECT count(*) as hist 
				FROM ordem_situacao 
				WHERE
					orsordoid = $ordoid and 
					orssituacao ilike '%Envio do lembrete de agendamento enviado com sucesso%'";
					
		$query = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);

		return (int) $retorno[0]['hist'];
	}

	// carrega titulo do email
	public function getTituloParamSiggo($nome){
		
		$sql = "select parsvalor from parametros_siggo where parsnome = '$nome'";

		$query	 = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);

		return $retorno;
	}
	
	/**
	* STI 80439 - Processo de autorização de O.S. de Clientes Inadimplentes
	*/
	
	public function conferePagamento($clioid){

		/**
			Inadimplente quando:
            	Cliente tem Fatura Vencida a pelo menos 15 dias e;
            	Bloqueio Web Ativo e;
            	Qualquer Titulo com Motivo Inadimplente.
		 */
                	
		$sql = "
				SELECT
					clioid, titclioid, titmotioid, cchtipo
				FROM
					clientes
				JOIN
					titulo on titclioid = clioid
				JOIN
					cliente_cobranca_historico ON cchclioid = clioid
				LEFT JOIN
						motivo_inadimplente ON motioid = titmotioid
				WHERE
					clioid = $clioid
				AND cchoid = (SELECT MAX(cchoid) FROM cliente_cobranca_historico AS cch WHERE cch.cchclioid = clioid AND cchexclusao IS NULL)
				AND cchtipo = 'B'
				AND titdt_pagamento IS NULL
				AND titdt_cancelamento IS NULL
				AND titformacobranca IN (SELECT forcoid 
								FROM forma_cobranca 
								WHERE forcexclusao IS NULL 
								AND (forccobranca IS TRUE OR (forcoid = 51 AND titdt_credito IS NOT NULL AND titdt_pagamento IS NULL)) 
								AND titnao_cobravel IS FALSE
							)
				AND (
					(NOW()::date-titdt_vencimento) > 15
					AND motivo_inadimplente.moticlassifica_os = true
				);";
				
		
		$query = pg_query($this->conn, $sql);
		$count = pg_num_rows($query);
				
		return $count;
	}

	/**
	 * Confere se a Ordem de Serviço possui mais de 30 dias após a data de criação. 
	 */
	public function validadeOrdemServico($ordem){

		$sql = "
				SELECT 
					ordoid
				FROM 
					ordem_servico
				WHERE 
					ordoid = $ordem
				AND orddt_ordem < (NOW() - INTERVAL '30 DAY')";

		$query = pg_query($this->conn, $sql);
		$count = pg_num_rows($query);
						
		return $count;
	}

	
	public function atualizarOrdemServico($ordem, $ID, $msg){

		$sql = "
				UPDATE 
					ordem_servico 
				SET 
					ordstatus = $ID, orddescr_motivo='$msg' 
				WHERE ordoid = $ordem";

		$query   = pg_query($this->conn, $sql);
		$retorno = (!$query) ? false : true;
		
		return $retorno;
	}

	
	public function registraHistoricoOS($ordem, $msg){

		$sql = "
				INSERT INTO 
					ordem_situacao (orsordoid, orsusuoid, orssituacao, orsdt_situacao) 
				VALUES ($ordem, 4873, '$msg', now())";
		
		$query   = pg_query($this->conn, $sql);
		$retorno = (!$query) ? false : true;
		
		return $retorno;
	}
	
	
	public function registraHistoricoTermo($ordem){
		
		$sqlCon = "select ordconnumero from ordem_servico where ordoid = $ordem";

		$queryCon = pg_query($this->conn, $sqlCon);
		$termo = pg_fetch_assoc($queryCon);
		
		$sql = "SELECT historico_termo_i(".$termo['ordconnumero'].", 4873, 'E-mail enviado para o cliente!')";
		
		$query   = pg_query($this->conn, $sql);
		$retorno = (!$query) ? false : true;
		
		return $retorno;
	}
	
	
	public function dadosCliente($clioid){
		
		$sql = '
				SELECT 
					* 
				FROM 
					clientes 
				WHERE 
					clioid = '.$clioid;
		
		$query   = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);
		
		return $retorno[0];
	}
	
	public function dadosOS($clioid, $ordoid){
	
		$sql = "
				SELECT
					V.veiplaca, V.veichassi , OS.ordconnumero
				FROM 
					veiculo AS V
				JOIN 
					ordem_servico OS ON  OS.ordveioid = V.veioid
				WHERE 
					OS.ordclioid = $clioid
				AND Os.ordoid = $ordoid	
				";
	
		$query   = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($query);
	
		return $retorno[0];
	}
	
	public function dadosFatura($clioid) {
		$sql = "
				SELECT 
					titoid, titvl_titulo, to_char(titdt_vencimento,'dd/mm/yyyy') as vencimento
				FROM 
					clientes,titulo 
				WHERE titformacobranca IN (SELECT forcoid 
								FROM forma_cobranca 
								WHERE forcexclusao IS NULL 
								AND (forccobranca IS TRUE OR (forcoid = 51 AND titdt_credito IS NOT NULL AND titdt_pagamento IS NULL)) 
								AND titnao_cobravel IS FALSE
							)
				AND titclioid=clioid 
				AND clioid=$clioid 
				AND titdt_pagamento IS NULL 
				AND titdt_cancelamento IS NULL
				AND (NOW()::date-titdt_vencimento)>15
				ORDER BY titdt_vencimento DESC
				";
		
		$res   = pg_query($this->conn, $sql);
		return $retorno = pg_fetch_all($res);
	}
	
	public function dadosRemetente($servidor) {
		$sql = "
				SELECT
					*
				FROM
					servidor_email
				WHERE srvoid = $servidor
				";
				
		$res   = pg_query($this->conn, $sql);
		$retorno = pg_fetch_all($res);
		return $retorno[0];
	}
}