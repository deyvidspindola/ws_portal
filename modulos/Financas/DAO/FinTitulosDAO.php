<?php

class FinTitulosDAO {

	public $conn;

	public function __construct($conn) {
		$this->conn = $conn;
	}

	public function getInformacoesTitulos($arrTitulos){

		// utilizar query anterior

		// 'Seu Numero' titulo id
		// 'Nosso Numero' titulo id
		// 'Parcela' tabela titulo titno_parcela / titcno_parcela
		// 'Data Registro' evento
		// 'Situação do Título' vencido ou a vencer
		// 'Valor Titulo' valor
		// 'Vencimento' ok
		// 'Nome Pagador' ok

	}

	public function getTitulosSantanderRegistradosPorPeriodo($dataInicial, $dataFinal = null){

		$dataFinal = empty($dataFinal) ? $dataInicial : $dataFinal;

		$bancoSatander = 33;

		$sql = "SELECT * FROM (
				SELECT DISTINCT ON (evtititoid)
					evtititoid,
					evtitpetoid,
					evtidt_geracao,
					tittpetoid,
					titno_parcela,
					titdt_vencimento,
					titvl_titulo,
					clinome
				FROM
					evento_titulo
				JOIN
					titulo ON titoid = evtititoid
				JOIN
					clientes ON clioid = titclioid
				WHERE 
					titdt_credito IS NULL
					AND titdt_cancelamento IS NULL
					AND titdt_pagamento IS NULL
					AND evtidt_geracao BETWEEN DATE('$dataInicial') AND DATE('$dataFinal')

				UNION ALL

				SELECT DISTINCT ON (evtititoid)
					evtititoid,
					evtitpetoid,
					evtidt_geracao,
					tittpetoid,
					titno_parcela,
					titdt_vencimento,
					titvl_titulo_retencao as titvl_titulo,
					clinome
				FROM
					evento_titulo
				JOIN
					titulo_retencao ON titoid = evtititoid
				JOIN
					clientes ON clioid = titclioid
				WHERE 
					titdt_credito IS NULL
					AND titdt_cancelamento IS NULL
					AND titdt_pagamento IS NULL
					AND evtidt_geracao BETWEEN DATE('$dataInicial') AND DATE('$dataFinal')

				UNION ALL

				SELECT DISTINCT ON (evtititoid)
					evtititoid,
					evtitpetoid,
					evtidt_geracao,
					titctpetoid as tittpetoid,
					NULL as titno_parcela,
					titcdt_vencimento as titdt_vencimento,
					titcvl_titulo as titvl_titulo,
					clinome
				FROM
					evento_titulo
				JOIN
					titulo_consolidado ON titctpetoid = evtititoid
				JOIN
					clientes ON clioid = titcclioid
				WHERE 
					titcdt_credito IS NULL
					AND titcdt_cancelamento IS NULL
					AND titcdt_pagamento IS NULL
					AND evtidt_geracao BETWEEN DATE('$dataInicial') AND DATE('$dataFinal')

				ORDER BY
					evtititoid,
					evtidt_geracao DESC	
			) AS titulos_intervalo
			WHERE
				tittpetoid IN (
					SELECT tpetoid
					FROM tipo_evento_titulo
					WHERE tpetcodigo IN (
						SELECT
							CAST(regexp_split_to_table(pcsidescricao, E',') as INT)
						FROM
							parametros_configuracoes_sistemas
						INNER JOIN
							parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
						WHERE
							pcsipcsoid = 'COBRANCA_REGISTRADA'
							AND pcsioid = 'COD_MOVIMENTO_REGISTRADO'
					)
					AND tpettipo_evento = 'Retorno'
					AND tpetcfbbanco = $bancoSatander
					AND tpetcob_registrada IS TRUE 
				)		
				AND evtitpetoid IN (
					SELECT tpetoid
					FROM tipo_evento_titulo
					WHERE tpetcodigo IN (
						SELECT
							CAST(regexp_split_to_table(pcsidescricao, E',') as INT)
						FROM
							parametros_configuracoes_sistemas
						INNER JOIN
							parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
						WHERE
							pcsipcsoid = 'COBRANCA_REGISTRADA'
							AND pcsioid = 'COD_MOVIMENTO_REGISTRADO'
					)
					AND tpettipo_evento = 'Retorno'
					AND tpetcfbbanco = $bancoSatander
					AND tpetcob_registrada IS TRUE 
				)
			ORDER BY 
				evtititoid ASC";

		$result = pg_query($this->conn, $sql);

		$data = array();

		while($row = pg_fetch_array($result)){
			$data[] = $row;
		}

		return $data;

	}

}