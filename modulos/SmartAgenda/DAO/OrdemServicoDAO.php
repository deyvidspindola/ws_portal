<?php

 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class OrdemServicoDAO extends DAO {

	//Metodo para gravar o historico de email e sms
	public function gravarHistorico($ordem,$usuario,$msg,$dataAgenda = null,$horaAgenda = null,$status = null){

        // Trata campos não obrigatórios
        $dataAgenda = is_null($dataAgenda) ? "NULL" : "'{$dataAgenda}'";
        $horaAgenda = is_null($horaAgenda) ? "NULL" : "'{$horaAgenda}'";
        $status = is_null($status) ? "NULL" : "{$status}";

			$sql = "INSERT INTO
					ordem_situacao (orsordoid, orsusuoid, orssituacao, orsdt_agenda, orshr_agenda, orsstatus)
				VALUES ($ordem, $usuario, '$msg', $dataAgenda, $horaAgenda, $status)";

			$rs = $this->executarQuery($sql);
			$retorno = (!$rs) ? false : true;

		return $retorno;
	}

	//Retorna o id do motivo do historico da corretora
	public function motivoHistoricoCorretora($descricaoMotivo){


			$sql = "SELECT
						mhcoid
					FROM
                        motivo_hist_corretora
					WHERE
                        mhcdescricao = '$descricaoMotivo'
                    AND
                        mhcexclusao IS NULL
                    LIMIT 1";

			$rs = $this->executarQuery($sql);

            $retorno = pg_fetch_object($rs);

            $mhcoid = isset($retorno->mhcoid) ? $retorno->mhcoid : null;

            return $mhcoid;

	}

	public function dadosRemetente($servidor) {

					$sql = "
							SELECT
							*
							FROM
							servidor_email
							WHERE srvoid = $servidor
							";

			$rs = $this->executarQuery($sql);
			$retorno = pg_fetch_all($rs);


		return $retorno[0];
	}

     public function recuperarDadosOrdemServico($campos, $filtros) {

            $sql = "SELECT " . implode(',', $campos) . " FROM ordem_servico " . $filtros;

            $rs = $this->executarQuery($sql);

            if(pg_num_rows($rs) > 0){
                return pg_fetch_all($rs);
            }else{
                return false;
            }

        }

    public function atualizarRepresentante($ordoid, $repoid) {

        $retorno = 0;

        if(empty($repoid)){
            $valor = 'NULL';
        } else {
            $valor = "(SELECT relroid FROM relacionamento_representante WHERE relrrepoid = ".intval($repoid)." LIMIT 1)";
        }

        $sql = "
                UPDATE
                    ordem_servico
                SET
                    ordrelroid = $valor
                WHERE
                    ordoid = ".intval($ordoid)."
                ";

        $rs = $this->executarQuery($sql);

        $retorno = pg_affected_rows($rs);

        return $retorno;
    }

    public function atualizarInstalador($ordoid, $itloid) {

        $retorno = 0;

        if(empty($itloid)){
            $valor = 'NULL';
        } else {
            $valor = "(SELECT itloid FROM instalador WHERE itloid = ".intval($itloid).")";
        }

        $sql = "
                UPDATE
                    ordem_servico
                SET
                    orditloid = $valor
                WHERE
                    ordoid = ".intval($ordoid)."
                ";

        $rs = $this->executarQuery($sql);

        $retorno = pg_affected_rows($rs);

        return $retorno;
    }

    public function excluirLocalInstalacao($ordoid) {

        $retorno = 0;

        $sql = "DELETE FROM ordem_servico_inst  WHERE osiordoid = ".intval($ordoid)."";

        $rs = $this->executarQuery($sql);

        $retorno = pg_affected_rows($rs);

        return $retorno;
    }

    public function getTiposServicos(){
        $retorno = array();
        $sql = "SELECT
                  ostoid,
                  ostdescricao
                FROM
                  os_tipo
                WHERE
                  ostdt_exclusao IS NULL
                ORDER BY
                  ostdescricao";

        $rs = $this->executarQuery($sql);

        while($linha = pg_fetch_object($rs)){
            $retorno[$linha->ostoid] = $linha->ostdescricao;
        }
        return $retorno;
    }

    public function atualizarDirecionamento($ordoid) {

        $retorno = 0;

        $sql = "
                UPDATE
                    ordem_servico
                SET
                    orddt_asso_rep = NULL
                WHERE
                    ordoid = ".intval($ordoid)."
                ";

        $rs = $this->executarQuery($sql);

        $retorno = pg_affected_rows($rs);

        return $retorno;
    }

}