<?php
/**
 * Sascar - Tecnologia e Seguranca Automotiva
 *
 * Classe de acesso ao banco de dados para a regra Controle de Consumo
 */

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class ControleConsumoDAO extends DAO{

	public function __construct($conn) {
		$this->conn = $conn;
	}

    public function verificaCapacidadeConsumoData($data, $prestador, $categoria, $hora) {
        $sql = "SELECT
                  *
                FROM
                  ofsc_capacidade_consumo AS occ
                WHERE
                  occ.occdt_agenda = '{$data}' AND
                  occ.occbucket = '{$prestador}' AND
                  occ.occoccoid = {$categoria} AND
                  occ.occtime_slot = '{$hora}'
                LIMIT
                  1";
        $rs = $this->executarQuery($sql);

        return pg_num_rows($rs) ? pg_fetch_array($rs, 0, PGSQL_ASSOC) : array();
		}

    public function getCategoriaPorNome($nome) {
        $sql = "SELECT
                  *
                FROM
                  ofsc_capacidade_categoria AS occ
                WHERE
                  occ.occdescricao = '{$nome}'
                LIMIT
                  1";

        $rs = $this->executarQuery($sql);

        return pg_num_rows($rs) ? pg_fetch_array($rs, 0, PGSQL_ASSOC) : array();
		}


    public function getConsumoPorOrdemServico($idOrdemServico) {
        $sql = "SELECT
                  osac.asacoid,
                  occ.occoid,
                  occ.occ_tempo_herdado - osac.asacvalor_herdado AS tempo_herdado,
                  occ.occ_tempo_distribuido - osac.asacvalor_distribuido AS tempo_distribuido
                FROM
                  ordem_servico_agenda AS osa
                  INNER JOIN ordem_servico_agenda_consumo AS osac ON (osa.osaoid = osac.asacosaoid)
                  INNER JOIN ofsc_capacidade_consumo AS occ ON (osac.asacoccoid = occ.occoid)
                WHERE
                  osaordoid = {$idOrdemServico}";

        $rs = $this->executarQuery($sql);

        return pg_num_rows($rs) ? pg_fetch_all($rs) : array();
    }

    public function removerConsumoGeral() {

        $sql = "DELETE FROM ofsc_capacidade_consumo
                WHERE occoid IN (SELECT occoid FROM ofsc_capacidade_consumo WHERE occdt_agenda < NOW() LIMIT 100)";
        $rs = $this->executarQuery($sql);
    }
}