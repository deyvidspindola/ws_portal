<?php
require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';

class CronAtendimentoSolicitacaoCentralDistribuicaoDAO extends CronDAO {

	public function recuperaRepresentantePadrao()
	{
		$sqlRep = " SELECT
						pcsidescricao
					FROM
						parametros_configuracoes_sistemas_itens
					WHERE
						pcsipcsoid = 'SMART_AGENDA'
						AND pcsioid = 'REPOID_SOLICITACAO_FALSA';";

		$resRep = $this->query($sqlRep);

		if(pg_num_rows($resRep) <= 0) {
			throw new Exception('Nao foi encontrado o representante.');
		}

		$idRep = pg_fetch_result($resRep, 0, 'pcsidescricao');

		return (int)$idRep;
	}

	public function recuperaSolicitacoes($idRep) {
		$sqlSol = " SELECT DISTINCT
						sagoid
					FROM
						solicitacao_agendamento
						INNER JOIN solicitacao_agendamento_item_status ON
							sagsaisoid = saisoid
					WHERE
						saisoid = 1
						AND sagfalta_critica IS TRUE
						AND sagrepoid = $idRep";

		$resSol = pg_query($sqlSol);

		if(pg_num_rows($resSol) <= 0) {
			throw new Exception('Nao foi encontrada nenhuma solicitacao.');
		}

		return $resSol;
	}

	public function recuperaItensSolicitacao($idSol) {
		$sqlItemSol = " SELECT
							saioid,
							saiprdoid,
							saiqtde_solicitacao
						FROM
							solicitacao_agendamento_item
							INNER JOIN solicitacao_agendamento_item_status ON
								saisaisoid = saisoid
						WHERE
							saisoid = 1
							AND saisagoid = $idSol";

		$resItemSol = pg_query($sqlItemSol);
		return $resItemSol;
	}

	public function recuperaProdutoEstoque($idRep, $idPrd) {
		$sqlProdEstoque = " SELECT
								qtdeEstoque
							FROM (
								SELECT
									espqtde AS qtdeEstoque
								FROM
									relacionamento_representante
									INNER JOIN estoque_produto ON
										relroid = esprelroid
								WHERE
									relrrepoid = $idRep
									AND espprdoid = $idPrd
								UNION
								SELECT
									COUNT(imoboid)
								FROM
									relacionamento_representante
									INNER JOIN imobilizado ON
										relroid = imobrelroid
									INNER JOIN imobilizado_status ON
										imobimsoid = imsoid
								WHERE
									relrrepoid = $idRep
									AND imobprdoid = $idPrd
									AND imsoid = 3
							) AS Estoque
							WHERE
								qtdeEstoque > 0";

		$resProdEstoque = $this->query($sqlProdEstoque);

		if(pg_num_rows($resProdEstoque) <= 0) {
			return -1;
		}

		return pg_fetch_result($resProdEstoque, 0, 'qtdeEstoque');
	}

	public function executaBaixa($itens, $idSol) {

		$sqlBaixaItem = "	UPDATE
								solicitacao_agendamento_item
							SET
								saisaisoid = 7
							WHERE
								saioid IN $itens";

		if(!$this->query($sqlBaixaItem)) {
			return 0;
		}

		$sqlBaixaSolicitacao = "UPDATE
									solicitacao_agendamento
								SET
									sagsaisoid = 7
								WHERE
									sagoid = $idSol";

		if(!$this->query($sqlBaixaSolicitacao)) {
			return 0;
		}

		return 1;
	}

    /**
     * Geava historico na OS
     * @param  [type] $ordoid [description]
     * @param  [type] $msg    [description]
     * @return [type]         [description]
     */
    public function gravarHistoricoOrdemServico($ordoid, $msg) {

        $ordoid = intval($ordoid);

         $sql = "INSERT INTO
                    ordem_situacao
                (
                    orsordoid,
                    orsusuoid,
                    orssituacao,
                    orsstatus,
                    orsdt_agenda,
                    orshr_agenda
                )
                VALUES
                (
                    $ordoid,
                    2750,
                    '$msg',
                    (SELECT mhcoid FROM motivo_hist_corretora WHERE mhcdescricao ILIKE 'Solicita% distribui%'),
                    (SELECT osadata FROM ordem_servico_agenda WHERE osaordoid = $ordoid ORDER BY osaoid DESC LIMIT 1),
                    (SELECT osahora FROM ordem_servico_agenda WHERE osaordoid = $ordoid ORDER BY osaoid DESC LIMIT 1)

                )";

        $rs = $this->query($sql);

}


    /**
     * REcupera o numero da OS a partir da solicitacao
     * @param  [int] $sagoid [ID da solcitacao]
     * @return [int]
     */
    public function recuperarNumeroOrdemServico($sagoid){

        $sql = "SELECT
                sagordoid
            FROM
                solicitacao_agendamento
            WHERE
                sagoid = ".intval($sagoid)."
            ";

        $rs = $this->query($sql);
        $retorno = pg_fetch_object($rs);

        $ordoid = $retorno->sagordoid;

        return $ordoid;
    }

    /**
     * Recupera uma lista de produtos solicitados
     * @param  [int] $sagoid [ID da solicitacao]
     * @return [string]
     */
    public function getListaProdutosSolicitados($sagoid) {

        $lista = '';

        $sql = "SELECT
                    STRING_AGG(prdproduto, ', ') AS lista_produtos
                FROM
                    solicitacao_agendamento_item
                INNER JOIN
                    produto ON (prdoid = saiprdoid)
                WHERE
                    saisagoid  = " . intval($sagoid);

        $rs = $this->query($sql);

        $registro = pg_fetch_object($rs);

        $lista = isset($registro->lista_produtos) ? $registro->lista_produtos : '';

        return $lista;

    }


}

?>