<?php

/**
 * Classe CadMarcaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 *
 */
class CadMarcaVeiculoDAO {


	private $conn;
	private $usuarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

	}

	public function pesquisar(stdClass $parametros, $paginacao = NULL, $ordenacao = NULL){

		$retorno = array();

		$sql = ' SELECT ';

        if (is_null($paginacao)) {
            $sql .= " COUNT(mcaoid) as total ";
        } else {
			 $sql .=" mcaoid, mcamarca ";
        }

        $sql .= "
                FROM
                    marca
                WHERE
                    mcadt_exclusao IS NULL ";

        if ( isset($parametros->mcamarca) && !empty($parametros->mcamarca) ) {

            $sql .= " AND
                        mcamarca ILIKE '%" . pg_escape_string( $parametros->mcamarca ) . "%'";

        }

        if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            } else {
                 $sql .=  ' ORDER BY mcamarca';
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


		return $retorno;
	}

	public function pesquisarPorID( $mcaoid ){

		$retorno = new stdClass();

		$sql = "SELECT
					mcaoid,
					mcamarca
				FROM
					marca
				WHERE
					mcaoid =" . $mcaoid . "";

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					marca (mcamarca, mcausuoid_inclusao)
				VALUES
					(
                        '" . pg_escape_string( trim($dados->mcamarca) ) . "',
                        ". $this->usuarioLogado ."

                    )";

		$rs = $this->executarQuery($sql);

		return TRUE;
	}

    public function verificarDuplicidade(stdClass $dados) {

        if( ! empty($dados->mcaoid) ) {
            $where = ' AND mcaoid != ' . $dados->mcaoid;
        }

        $sql = "SELECT EXISTS (
                    SELECT 1
                    FROM marca
                    WHERE mcamarca = '". $dados->mcamarca ."'
                    ". $where ."
                    AND mcadt_exclusao IS NULL
                    ) as existe";

       $rs = $this->executarQuery($sql);

       $registro = pg_fetch_object($rs);
       $retorno = ($registro->existe == 'f') ? FALSE : TRUE;

        return $retorno;

    }

	public function atualizar(stdClass $dados){

		$sql = "UPDATE
					marca
				SET
					mcamarca = '" . pg_escape_string( $dados->mcamarca ) . "',
                    mcadt_alteracao = NOW(),
                    mcausuoid_alteracao = ". $this->usuarioLogado ."
				WHERE
					mcaoid = " . $dados->mcaoid . "";

		$rs = $this->executarQuery($sql);

		return TRUE;
	}

	public function excluir( $mcaoid ){

		$sql = "UPDATE
					marca
				SET
					mcadt_exclusao = NOW(),
                    mcausuoid_exclusao = ". $this->usuarioLogado ."
				WHERE
					mcaoid = " . intval( $mcaoid ) . "";

		$rs = $this->executarQuery($sql);

		return TRUE;
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
