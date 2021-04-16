<?php

/**
 * Classe CadSubtipoVeiculoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Davi Junior <davi.junior.ext@sascar.com.br>
 *
 */
class CadSubtipoVeiculoDAO extends DAO {

    public function pesquisar(stdClass $parametros, $paginacao = NULL, $ordenacao = NULL){

        $retorno = array();

        $sql = ' SELECT ';

        if (is_null($paginacao)) {
            $sql .= " COUNT(vstoid) as total ";
        } else {
            $sql .=" vstoid,
                    vstdescricao,
                    tipvoid,
                    tipvdescricao ";
        }

        $sql .= "
                FROM
                    veiculo_subtipo
                LEFT JOIN
                    veiculo_tipo_subtipo ON (vtsvstoid = vstoid)
                LEFT JOIN
                    tipo_veiculo ON (vtstipvoid = tipvoid AND tipvexclusao IS NULL)
                WHERE
                    vstdt_exclusao IS NULL ";

        if ( isset($parametros->vstdescricao) && !empty($parametros->vstdescricao) ) {

            $sql .= " AND
                        vstdescricao ILIKE '%" . pg_escape_string( $parametros->vstdescricao ) . "%'";

        }

        if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            } else {
                $sql .=  ' ORDER BY vstdescricao, tipvdescricao';
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

    public function pesquisarPorID( $vstoid ){

        $retorno = new stdClass();

        $sql = "SELECT
					vstoid,
					vstdescricao,
                    tipvoid,
                    tipvdescricao
				FROM
					veiculo_subtipo
                LEFT JOIN
                    veiculo_tipo_subtipo ON (vtsvstoid = vstoid)
                LEFT JOIN
                    tipo_veiculo ON (vtstipvoid = tipvoid AND tipvexclusao IS NULL)
				WHERE
					vstoid =" . $vstoid . "";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function inserir(stdClass $dados){

        $sqlRetorno = new stdClass();
        $retorno = FALSE;

        $sql = "INSERT INTO
					veiculo_subtipo (vstdescricao, vstusuoid_cadastro)
				VALUES
					(
                        '" . pg_escape_string( trim($dados->vstdescricao) ) . "',
                        ". $this->usuarioLogado ."
                    ) RETURNING vstoid";

        $rs = $this->executarQuery($sql);

        if( pg_num_rows($rs) > 0 ) {

            $sqlRetorno = pg_fetch_object($rs);

            $retorno = $this->inserirRelacionamentoTipoSubTipo( $dados->tipvoid, $sqlRetorno->vstoid );

        } else {
            return $retorno;
        }

        return $retorno;
    }

    private function inserirRelacionamentoTipoSubTipo( $vtstipvoid, $vtsvstoid ) {

         $sql = "INSERT INTO
                        veiculo_tipo_subtipo (vtstipvoid, vtsvstoid)
                    VALUES
                    (
                        ". $vtstipvoid.",
                        ".$vtsvstoid."
                    )
                ";

            $rs = $this->executarQuery($sql);

        return TRUE;

    }

    public function verificarDuplicidade(stdClass $dados) {

        if( ! empty($dados->vstoid) ) {
            $where = ' AND vstoid != ' . $dados->vstoid;
        }

        $sql = "SELECT EXISTS (
                    SELECT 1
                    FROM veiculo_subtipo
                    WHERE vstdescricao = '". $dados->vstdescricao ."'
                    ". $where ."
                    AND vstdt_exclusao IS NULL
                    ) as existe";

        $rs = $this->executarQuery($sql);

        $registro = pg_fetch_object($rs);
        $retorno = ($registro->existe == 'f') ? FALSE : TRUE;

        return $retorno;

    }

    public function atualizar(stdClass $dados){

        $retorno = FALSE;

        $sql = "UPDATE
					veiculo_subtipo
				SET
					vstdescricao = '" . pg_escape_string( $dados->vstdescricao ) . "'
				WHERE
					vstoid = " . $dados->vstoid . "";

        $rs = $this->executarQuery($sql);

        $isExisteRelacioanmento = $this->isRelacionamentoTipoSubTipo(  $dados->vstoid );

        if( $isExisteRelacioanmento ) {
            $retorno = $this->atualizarRelacionamentoTipoSubTipo($dados);
        } else {
            $retorno = $this->inserirRelacionamentoTipoSubTipo( $dados->tipvoid, $dados->vstoid );
        }

        return TRUE;
    }

    private function isRelacionamentoTipoSubTipo( $idSubTipo ) {

        $sql = "SELECT EXISTS (
                    SELECT 1
                    FROM veiculo_tipo_subtipo
                    WHERE vtsvstoid = " . $idSubTipo . "
                    ) as existe";

        $rs = $this->executarQuery($sql);

        $registro = pg_fetch_object($rs);
        $retorno = ($registro->existe == 'f') ? FALSE : TRUE;

        return $retorno;

    }

    public function atualizarRelacionamentoTipoSubTipo(stdClass $dados){

        $sql = "UPDATE
                    veiculo_tipo_subtipo
                SET
                    vtstipvoid = " . $dados->tipvoid . "
                WHERE
                    vtsvstoid = " . $dados->vstoid;

        $rs = $this->executarQuery($sql);

        return TRUE;
    }

    public function excluir( $vstoid ){

        $sql = "UPDATE
					veiculo_subtipo
				SET
					vstdt_exclusao = NOW(),
                    vstusuoid_exclusao = ". $this->usuarioLogado ."
				WHERE
					vstoid = " . intval( $vstoid ) . "";

        $rs = $this->executarQuery($sql);

        $retorno = $this->excluirRelacionamentoTipoSubTipo( $vstoid );

        return $retorno;
    }

    private function excluirRelacionamentoTipoSubTipo( $vstoid ){

         $sql = "DELETE FROM
                    veiculo_tipo_subtipo
                WHERE
                    vtsvstoid = " . intval( $vstoid ) . "";

        $rs = $this->executarQuery($sql);

        return TRUE;

    }

    public function recuperarListaTipo(){

        $retorno = array();

        $sql = "SELECT
                  tipvoid,
                  tipvdescricao
                FROM
                   tipo_veiculo
                WHERE
                    tipvexclusao IS NULL
                ORDER BY
                    tipvdescricao";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

}
