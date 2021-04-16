<?php

/**
 * Classe Cadmotivo_teste_parcialDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 *
 */
class CadMotivoTesteParcialDAO {


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
            $sql .= " COUNT(mtpoid) as total ";
        } else {
			 $sql .=" mtpoid, mtpdescricao ";
        }

        $sql .= "
                FROM
                    motivo_teste_parcial
                WHERE
                    mtpdt_exclusao IS NULL ";

        if ( isset($parametros->mtpdescricao) && !empty($parametros->mtpdescricao) ) {

            $sql .= " AND
                        mtpdescricao ILIKE '%" . pg_escape_string( $parametros->mtpdescricao ) . "%'";

        }

        if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            } else {
                 $sql .=  ' ORDER BY mtpdescricao';
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

	public function pesquisarPorID( $mtpoid ){

		$retorno = new stdClass();

		$sql = "SELECT
					mtpoid,
					mtpdescricao
				FROM
					motivo_teste_parcial
				WHERE
					mtpoid =" . $mtpoid . "";

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					motivo_teste_parcial (mtpdescricao, mtpusuoid_cadastro)
				VALUES
					(
                        '" . pg_escape_string( trim($dados->mtpdescricao) ) . "',
                        ". $this->usuarioLogado ."
                    )";

		$rs = $this->executarQuery($sql);

		return TRUE;
	}

    public function verificarDuplicidade(stdClass $dados) {

        if( ! empty($dados->mtpoid) ) {
            $where = ' AND mtpoid != ' . $dados->mtpoid;
        }

        $sql = "SELECT EXISTS (
                    SELECT 1
                    FROM motivo_teste_parcial
                    WHERE mtpdescricao = '". $dados->mtpdescricao ."'
                    ". $where ."
                    AND mtpdt_exclusao IS NULL
                    ) as existe";

       $rs = $this->executarQuery($sql);

       $registro = pg_fetch_object($rs);
       $retorno = ($registro->existe == 'f') ? FALSE : TRUE;

        return $retorno;

    }

	public function atualizar(stdClass $dados){

		$sql = "UPDATE
					motivo_teste_parcial
				SET
					mtpdescricao = '" . pg_escape_string( $dados->mtpdescricao ) . "'
				WHERE
					mtpoid = " . $dados->mtpoid . "";

		$rs = $this->executarQuery($sql);

		return TRUE;
	}

	public function excluir( $mtpoid ){

		$sql = "UPDATE
					motivo_teste_parcial
				SET
					mtpdt_exclusao = NOW(),
                    mtpusuoid_exclusao = ". $this->usuarioLogado ."
				WHERE
					mtpoid = " . intval( $mtpoid ) . "";

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
