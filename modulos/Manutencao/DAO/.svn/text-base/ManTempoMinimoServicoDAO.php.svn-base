<?php

/**
 * Classe ManTempoMinimoServicoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Manutencao
 * @author   ANDRE LUIZ ZILZ <andre.zilz@sascar.com.br>
 *
 */
class ManTempoMinimoServicoDAO {

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	private $conn;
	private $usuarioLogado;

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
	}

	public function pesquisar(stdClass $parametros, $paginacao = NULL, $ordenacao = NULL){

		$retorno = array();
        $sql = ' SELECT ';

        if (is_null($paginacao)) {
            $sql .= " COUNT(stmoid) as total ";
        } else {

            $sql .= "stmoid,
                    stmchave,
                    (CASE WHEN stmponto = 'F' THEN 'FIXO' ELSE 'MOVEL' END) as stmponto_legenda,
                    stmponto,
                    stmrepoid,
                    stmtempo_minimo,
                    stmtempo_ofsc,
                    repnome ";
        }

		$sql .= " FROM
					smartagenda_tempo_minimo
                INNER JOIN representante ON (repoid = stmrepoid)
				WHERE
					stmdt_exclusao IS NULL
               ";


        if ( isset($parametros->stmchave) && !empty($parametros->stmchave) ) {

            $sql .= "AND
                        stmchave ILIKE '%" . pg_escape_string( $parametros->stmchave ) . "%'";

        }

        if ( isset($parametros->stmponto) && trim($parametros->stmponto) != '' && $parametros->stmponto != 'A' ) {

            $sql .= "AND
                        stmponto = '" . $parametros->stmponto . "'";

        }

        if ( isset($parametros->stmrepoid) && trim($parametros->stmrepoid) != 0 ) {

            $sql .= "AND
                        stmrepoid = " . intval( $parametros->stmrepoid ) . "";

        }

        if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            }

            $sql .= " LIMIT " . $paginacao->limite . " OFFSET " . $paginacao->offset;
        }

		$rs = $this->executarQuery($sql);

        if (is_null($paginacao)) {
            return pg_fetch_object($rs);
        } else {
            while($registro = pg_fetch_object($rs)){
                $retorno[] = $registro;
            }

            return $retorno;
        }

	}

    public function pesquisarPorID( $stmoid ){

        $retorno = new stdClass();

        $sql = "SELECT
                    stmoid,
                    stmchave,
                    stmponto,
                    stmrepoid,
                    stmtempo_minimo
                FROM
                    smartagenda_tempo_minimo
                INNER JOIN
                    representante ON (repoid = stmrepoid)
                WHERE
                    stmoid = ". intval($stmoid) ."";

        $rs = $this->executarQuery($sql);

        if( pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

	public function pesquisarPrestador( $isPrestadorOFSC ){

		$retorno = array();

		$sql = "SELECT
					repoid,
                    repnome
				FROM
					representante
				WHERE
					repexclusao IS NULL
                AND
                    repstatus = 'A'";
        $sql .= ($isPrestadorOFSC) ? ' AND repofsc IS TRUE ' : '';
        $sql .= ' ORDER BY repnome';

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

		return $retorno;
	}

    public function pesquisarLog( $listaIDs ) {

        $retorno = array();

        $sql = "SELECT
                    stmlstmoid,
                    TO_CHAR(stmldt_alteracao, 'DD/MM/YYYY HH24:MM') AS stmldt_alteracao,
                    nm_usuario as stmusuario,
                    stmltempo_original,
                    stmltempo_novo
                FROM
                    smartagenda_tempo_minimo_log
                INNER JOIN usuarios ON (cd_usuario = stmlusuoid_alteracao)
                WHERE
                    stmlstmoid IN ( ". implode(',', $listaIDs) ." )";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function montarChaveTempo( $numeroOrdemServico, $tipoPonto ) {

        $sql = "SELECT (ostgrupo || '".$tipoPonto ."' || agccodigo || (sum(otipeso))::varchar) as chave
                 FROM ordem_servico
                  INNER JOIN ordem_servico_item ON (ositordoid = ordoid)
                  INNER JOIN os_tipo_item       ON (otioid = ositotioid)
                  INNER JOIN contrato           ON (ordconnumero = connumero)
                  INNER JOIN equipamento_classe ON (coneqcoid = eqcoid)
                  INNER JOIN os_tipo            ON (ordostoid = ostoid)
                  INNER JOIN agrupamento_classe ON (eqcagcoid = agcoid)
                WHERE ordoid = ".$numeroOrdemServico."
                AND ositexclusao IS NULL
                AND ositstatus != 'X'
                AND otitipo != 'K'
                GROUP BY ordoid, ostgrupo, agccodigo";

        $rs = $this->executarQuery($sql);

        $registro = pg_fetch_object($rs);
        $retorno = isset($registro->chave) ? $registro->chave : '';
        return $retorno;
    }

	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					smartagenda_tempo_minimo
					(
					stmchave,
                    stmponto,
					stmrepoid,
					stmtempo_minimo,
					stmtempo_ofsc,
					stmusuoid_cadastro
					)
				VALUES
					(
					'" . $dados->stmchave . "',
                    '" . $dados->stmponto . "',
					" . intval( $dados->stmrepoid ) . ",
					" . intval( $dados->stmtempo_minimo ) . ",
					" . intval( $dados->stmtempo_ofsc ) . ",
					" . $this->usuarioLogado . "
				)";

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function atualizar(stdClass $dados){

		$sql = "UPDATE
					smartagenda_tempo_minimo
				SET
					stmchave = '" . $dados->stmchave . "',
					stmrepoid = " . intval( $dados->stmrepoid ) . ",
					stmtempo_minimo = " . intval( $dados->stmtempo_minimo ) . "
				WHERE
					stmoid = " . $dados->stmoid . "";

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function inativarRegistro( $stmoid ) {

		$sql = "UPDATE
					smartagenda_tempo_minimo
				SET
					stmdt_exclusao = NOW(),
                    stmusuoid_exclusao = ". $this->usuarioLogado ."
				WHERE
					stmoid = " . intval( $stmoid ) . "";

		$rs = $this->executarQuery($sql);

		return true;
	}

    public function gravarLog(stdClass $dados){

        $sql = "INSERT INTO
                    smartagenda_tempo_minimo_log
                    (
                        stmlstmoid,
                        stmlusuoid_alteracao,
                        stmltempo_novo,
                        stmltempo_original
                    )
                VALUES
                    (
                    " . intval( $dados->stmoid) . ",
                    " . intval( $this->usuarioLogado ) . ",
                    " . intval( $dados->stmtempo_minimo ) . ",
                    " . intval( $dados->stmtempo_minimo_original ) . "
                )";

        $rs = $this->executarQuery($sql);

        return true;
    }

    public function recuperarTipoOrdemServico() {

        $retorno = array();

        $sql = "SELECT ostgrupo,ostdescricao
                FROM os_tipo
                WHERE ostdt_exclusao IS NULL";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

     public function recuperarAgrupamentoClasse() {

        $retorno = array();

        $sql = "SELECT agccodigo, agcdescricao
                FROM agrupamento_classe
                WHERE agcdt_exclusao IS NULL";

       $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }


    public function existeRegistroCadastrado( $parametros ) {

        if( ! empty($parametros->stmoid) ) {
            $where = ' AND stmoid != ' . $parametros->stmoid;
        }

        $sql = "SELECT EXISTS(
                        SELECT 1
                        FROM smartagenda_tempo_minimo
                        WHERE stmchave = '".$parametros->stmchave."'
                        AND stmdt_exclusao IS NULL
                        AND stmrepoid = ". $parametros->stmrepoid ."
                        " . $where . "
                ) AS existe";

       $rs = $this->executarQuery($sql);

       $registro = pg_fetch_object($rs);
       $retorno = ($registro->existe == 'f') ? FALSE : TRUE;

        return $retorno;
    }



	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

	private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
            }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }
}
?>
