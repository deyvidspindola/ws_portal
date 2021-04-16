<?php

class RelSinalizacaoOsTecladoApagadoDAO {

	private $conn;
	private $conmensagem;

	public function __construct($conn , $conmensagem){
		$this->conn = $conn;
		$this->conmensagem = $conmensagem;
	}

	public function carregaClasses()
	{
		$sql = 'SELECT
 					clicloid idclasse,
 					clicldescricao descricaoclasse
 				FROM 
 					cliente_classe
 				WHERE 
 					clicldt_exclusao IS NULL
 				ORDER BY descricaoclasse';

		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao buscar as classes de cliente");
		}

		return pg_fetch_all($result);
	}

	public function carregaSituacaoOS()
	{
		$sql = 'SELECT
 					ossoid idsituacao,
 					ossdescricao descricaosituacao
 				FROM 
 					ordem_servico_status
 				WHERE 
 					ossexclusao IS NULL
 				ORDER BY descricaosituacao';

		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao buscar status de O.S.");
		}

		return pg_fetch_all($result);
	}

	public function buscarOS( $datainicial, $datafinal, $situacao, $classe, $cancelado, $cliente )
	{
		
 
		$sql = 'SELECT
					To_char(ordem_servico_agenda.osadata,\'dd/mm/yyyy\') AS data_ultimo_agendamento,
					To_char(orddt_ordem,\'dd/mm/yyyy\') AS data_ordem,
					ordem_servico.ordoid AS numero_os,
					clientes.clinome AS cliente,
					cliente_classe.clicldescricao AS classe_cliente ,
					equipamento_classe.eqcdescricao AS classe_contrato,
					ordem_servico_status.ossdescricao AS status_os,
					veiculo.veiplaca AS placa_veiculo,
					clientes.clioid,
					contrato.conveioid
				FROM
					ordem_servico
				JOIN ordem_servico_item   ON ositordoid = ordoid
				LEFT JOIN ordem_servico_defeito   ON osdfoid = ositosdfoid_alegado
				LEFT JOIN ordem_servico_agenda   ON osaordoid = ordoid
					 AND osaexclusao IS NULL
					 AND osausuoid_excl IS NULL 
					 AND osaoid = (SELECT osaoid FROM ordem_servico_agenda a1
 												 WHERE a1.osaordoid = ordoid
 												 ORDER BY osadata DESC,
 												 osahora ASC limit 1)
				LEFT JOIN instalador   ON itloid = osaitloid
				JOIN clientes ON clioid = ordclioid
				JOIN contrato   ON ordconnumero = connumero
				JOIN equipamento_classe   ON eqcoid = coneqcoid
				JOIN ordem_servico_status   ON ordstatus = ossoid
				JOIN veiculo   ON conveioid = veioid
				LEFT JOIN cliente_classe   ON clicloid = cliclicloid
				WHERE 1=1
					AND ositexclusao IS NULL
					AND ((select count(1) from ordem_servico_item osi where osi.ositordoid = ordoid) <= 1 )
					AND ordem_servico_defeito.osdfdescricao ilike \'%teclado%apagado%\'';

		if( ($datainicial != null) && ($datafinal != null) )
		{
			$datainicialScape = pg_escape_string( $this->conn, $datainicial );
			$datafinalScape = pg_escape_string( $this->conn, $datafinal );

			$sql .=  "\nAND orddt_ordem >= to_date('" . $datainicialScape . "', 'DD/MM/YYYY')";
			$sql .=	"\nAND orddt_ordem < (to_date('" . $datafinalScape . "', 'DD/MM/YYYY') + interval '1 day')";
		}

		if( $situacao != null )
		{
			$sql .= "\nAND ossoid = " .  intval( $situacao );
		}

		if( $classe != null )
		{
			$sql .= "\nAND clicloid = " . intval( $classe );
		}

		if( $cancelado !== null )
		{
			if( $cancelado == true )
			{
				 
				$sql .= "\nAND ordaoamoid = 18";
			}
			else
			{
				 
				$sql .= "\nAND (ordaoamoid  <> 18 or ordaoamoid  is null )  ";
			}
		}
		 
		if( $cliente != null )
		{
			$clienteScape = pg_escape_string( $this->conn, $cliente );
			$sql .= "\nAND clinome ilike '%" . $clienteScape . "%'";
		}

		$sql .= "\nORDER BY osadata DESC";
		 
		if ( !$result = pg_query( $this->conn, $sql ) ) {
			throw new Exception("Falha ao executar busca de O.S.");
		}

		return pg_fetch_all($result);
	}

	public function buscarUltimaMensagem( $idVeiculo, $dataOrdem, $dataAgendamento, $idCliente )
	{
		if( ($idVeiculo == null) || ($dataOrdem == null) || ($dataAgendamento == null) || ($idCliente == NULL) )
		{
			return "";
		}

		$idVeiculo = intval( $idVeiculo );
		$dataOrdem = pg_escape_string( $this->conmensagem, $dataOrdem );
		$dataAgendamento = pg_escape_string( $this->conmensagem, $dataAgendamento );


		$sql = "SELECT 
					to_char( greatest( max(mentdatapacote), max(mentdt_leitura) ), 'DD/MM/YYYY' ) AS  data_ultima
				FROM 
					mensagem_teclado_cli" . ( $idCliente % 10 ) .  " 
				WHERE
				 	mentdata >= to_date('" . $dataOrdem . "', 'DD/MM/YYYY')
				 	AND mentdata < (to_date('" .$dataAgendamento . "', 'DD/MM/YYYY') + INTERVAL '1 day' )
				 	AND mentveioid = ". $idVeiculo . "
				 	AND
					(
						( 	mentorigem = 'VS' 
							AND mentdatapacote >= to_date('" . $dataOrdem . "', 'DD/MM/YYYY')
							AND mentdatapacote < (to_date('" . $dataAgendamento . "', 'DD/MM/YYYY') + INTERVAL '1 day' )
						)
						OR
						(
							mentorigem IN ('LV', 'SV') 
							AND mentdt_leitura >= to_date('" . $dataOrdem . "', 'DD/MM/YYYY')
							AND mentdt_leitura < (to_date('" .$dataAgendamento . "', 'DD/MM/YYYY') + INTERVAL '1 day' )
						)
					)";

		if ( !$result = pg_query( $this->conmensagem, $sql ) ) {
			throw new Exception("Falha ao executar busca da ultima mensagem");
		}

		return pg_fetch_all($result);
	}
}
?>
