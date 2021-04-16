<?php

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class AgendaDAO extends DAO {


	public function dadosAgendamento($id, $tipo){

        $dados = array();

		$sql = "SELECT
					osaoid,
					osaordoid,
					osaplaca,
					osadata,
					osahora,
					osahora_final ,
					osaitloid,
					osarepoid,
                    osaid_atividade,
					osatipo_atendimento
				FROM
					ordem_servico_agenda
                WHERE ";

        if($tipo == 'AGENDAMENTO') {
            $sql .= "  osaoid = " . intval($id);
        } else if($tipo == 'REAGENDAMENTO'){
             $sql .= "  osaordoid = " . intval($id) . " ORDER BY osaoid DESC LIMIT 1";
        } else {
            $sql .= "  osaordoid = " . intval($id) . " AND osaexclusao IS NULL ORDER BY osaoid DESC LIMIT 1";
        }

		$rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0) {
            $dados = pg_fetch_assoc($rs);
        }

		return $dados;
	}

    public function getDadosInstalacao($idOrdemServico)
    {
        $sql = "SELECT
                  osiordoid,
                  osiempresa,
                  osiender,
                  osiptref,
                  ositelefone_inst,
                  osiestoid,
                  osiclcoid,
                  osicbaoid,
                  osicep
                FROM
                  ordem_servico_inst
                WHERE
                  osiordoid = {$idOrdemServico}";
        $rs = $this->executarQuery($sql);
        return pg_num_rows($rs) ? pg_fetch_array($rs, 0, PGSQL_ASSOC) : array();
		}

    public function salvarContatos($contatos) {

        foreach ($contatos as $chave => $contato) {
            // salva celular do contato
            $qry = sprintf("UPDATE ordem_servico_celular_contato SET oscccelular = '%s', osccnome = '%s' WHERE osccordoid = '%s'",
                            $contato['celular'],
                            $contato['nome'],
                            $contato['id_os']);

            $result = $this->executarQuery($qry);
            if(pg_affected_rows($result) == 0) {
                $qry = sprintf("INSERT INTO ordem_servico_celular_contato (oscccelular, osccnome, osccordoid) VALUES ('%s','%s','%s')",
                            $contato['celular'],
                            $contato['nome'],
                            $contato['id_os']);

                $this->executarQuery($qry);
            }

            // salva email do contato
            $qry = sprintf("UPDATE ordem_servico_email_contato SET osecemail = '%s', osecnome = '%s' WHERE osecordoid = '%s'",
                            $contato['email'],
                            $contato['nome'],
                            $contato['id_os']);

            $result = $this->executarQuery($qry);
            if(pg_affected_rows($result) == 0) {
                $qry = sprintf("INSERT INTO ordem_servico_email_contato (osecemail, osecnome, osecordoid) VALUES ('%s','%s','%s')",
                            $contato['email'],
                            $contato['nome'],
                            $contato['id_os']);

                $this->executarQuery($qry);
            }
        }

    }


    public function setExcluirAgendamento($ordoid, $usuario, $obs){

        $sql =" UPDATE ordem_servico_agenda
                   SET osaexclusao = NOW(),
                       osausuoid_excl = $usuario,
                       osamotivo_excl = '$obs'
                 WHERE osaordoid  = $ordoid
           AND osaexclusao IS NULL
             RETURNING osaid_atividade ; ";

        $rs = $this->executarQuery($sql);

       return pg_num_rows($rs) ? pg_fetch_result($rs, 0, 'osaid_atividade') : array();

    }

}
