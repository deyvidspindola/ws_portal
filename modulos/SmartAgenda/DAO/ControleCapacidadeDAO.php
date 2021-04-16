<?php
/**
 * Sascar - Tecnologia e Seguranca Automotiva
 *
 * Classe de acesso ao banco de dados para a regra Controle de Capacidade
 */

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class ControleCapacidadeDAO extends DAO{

    /**
     * Retorna os registros de agendamentos distribuidos para o periodo de datas
     *
     * @param string $dataInicial
     * @param string $dataFinal
     * @return array
     * @throws Exception
     */
	public function getCapacidadeConsumo($dataInicial, $dataFinal)
    {
        $sql = "SELECT
                  occ.*,
                  occa.occdescricao
                FROM
                  ofsc_capacidade_consumo AS occ
                  INNER JOIN ofsc_capacidade_categoria AS occa ON (occ.occoccoid = occa.occoid)
                WHERE
                  occ.occdt_agenda >= '{$dataInicial}' AND
                  occ.occdt_agenda <= '{$dataFinal}'";
        $rs = $this->executarQuery($sql);
        return pg_num_rows($rs) ? pg_fetch_all($rs) : array();
		}

    public function getParametro($parametro) {


        $sql = "SELECT
                    pcsidescricao
                FROM
                    parametros_configuracoes_sistemas_itens
                WHERE
                    pcsipcsoid  = 'SMART_AGENDA'
                AND
                    pcsioid = '".$parametro."'";

        $rs = $this->executarQuery($sql);

        $row = pg_fetch_object($rs);

        $retorno = isset($row->pcsidescricao) ? $row->pcsidescricao : '';

        return $retorno;


    }
}