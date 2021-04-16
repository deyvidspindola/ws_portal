<?php

/**
 * Classe CadSubTipoVeiculoObrigacaoFinanceiraDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   ANDRE LUIZ ZILZ <andre.zilz@sascar.com.br>
 *
 */
class CadSubTipoVeiculoObrigacaoFinanceiraDAO extends DAO {

	public function recuperarObrigacaoFinanceira() {

        $retorno = array();

        $tipoObrigacaoFinanceira = array(
                    'Locacao_revenda_baixa_serial' => 3,
                    'Locacao_revenda_baixa_quantidade' => 4,
                    'Locacao_revenda_sem_baixa' => 5
                );

        $sql = "SELECT
                    obroid,
                    obrobrigacao
                FROM
                    obrigacao_financeira
                WHERE
                    obrdt_exclusao IS NULL
                AND
                    obroftoid IN (". implode(',', $tipoObrigacaoFinanceira) .")
                ORDER BY
                    obrobrigacao
                ";

        $rs = $this->executarQuery($sql);

        while( $registro = pg_fetch_object($rs) ){
            $retorno[] = $registro;
        }

        return $retorno;
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
                ORDER BY tipvdescricao";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarListaTipoNovo(){

        $retorno = array();

        $sql = "SELECT DISTINCT
                  tipvoid,
                  tipvdescricao
                FROM
                   tipo_veiculo
                INNER JOIN
                    veiculo_tipo_subtipo ON (tipvoid = vtstipvoid)
                WHERE
                    tipvexclusao IS NULL
                AND vtsvstoid NOT IN (
                    SELECT DISTINCT
                        vtsvstoid
                    FROM
                        veiculo_subtipo_obrigacao_financeira
                    INNER JOIN
                        veiculo_tipo_subtipo ON (vtsvstoid = vstovstoid)
                    WHERE
                        vstodt_exclusao IS NULL
				    )
				ORDER BY tipvdescricao";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarListaSubTipo( $idTipo ) {

        $retorno = array();

        $sql = "SELECT
                    vstoid,
                    vstdescricao
                FROM
                    veiculo_subtipo
                INNER JOIN
                    veiculo_tipo_subtipo ON (vtsvstoid = vstoid)
                INNER JOIN
                    tipo_veiculo ON (vtstipvoid = tipvoid)
                WHERE
                    vtstipvoid =" . $idTipo . "";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function recuperarListaSubTipoNovo( $idTipo ) {

    	$retorno = array();

        $sql = "SELECT
					vstoid,
					vstdescricao
				FROM
                    veiculo_subtipo
                INNER JOIN
                    veiculo_tipo_subtipo ON (vtsvstoid = vstoid)
                INNER JOIN
                    tipo_veiculo ON (vtstipvoid = tipvoid)
				WHERE
					vtstipvoid =" . $idTipo . "
                AND
                    vtsvstoid NOT IN (
                                SELECT DISTINCT
                                    vtsvstoid
                                FROM
                                    veiculo_subtipo_obrigacao_financeira
                                INNER JOIN
                                    veiculo_tipo_subtipo ON (vtsvstoid = vstovstoid)
                                WHERE
                                    vstodt_exclusao IS NULL
                                AND vtstipvoid =" . $idTipo . "
                                )
                ORDER BY vstdescricao";

        $rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)){
            $retorno[] = $registro;
        }

        return $retorno;
    }

	public function pesquisar(stdClass $parametros, $paginacao = NULL, $ordenacao = NULL){

		$retorno = array();

		$sql = ' SELECT ';

        if (is_null($paginacao)) {
            $sql .= " COUNT(DISTINCT vstovstoid) as total ";
        } else {
			 $sql .=" DISTINCT
			 			COUNT(1) over(PARTITION BY vstovstoid) as total,
						vstovstoid,
						STRING_AGG(obrobrigacao, ',') over(PARTITION BY vstovstoid) as lista_obrigacao,
						tipvdescricao,
						vstdescricao ";
        }

        $sql .= "
				FROM
					veiculo_subtipo_obrigacao_financeira
				INNER JOIN
					obrigacao_financeira ON (obroid = vstoobroid)
				INNER JOIN
					veiculo_tipo_subtipo ON (vtsvstoid = vstovstoid)
				INNER JOIN
					veiculo_subtipo ON (vstoid = vtsvstoid)
				INNER JOIN
					tipo_veiculo ON (tipvoid = vtstipvoid)
				WHERE
					vstodt_exclusao IS NULL
				";

        if ( isset($parametros->tipvoid) && trim($parametros->tipvoid) != '' ) {

            $sql .= "AND
                        tipvoid = " . intval( $parametros->tipvoid ) . "";

        }

        if ( isset($parametros->vstoid) && trim($parametros->vstoid) != '' ) {

            $sql .= "AND
                        vstovstoid = " . intval( $parametros->vstoid ) . "";

        }

        if ( isset($parametros->obroid) && trim($parametros->obroid) != '' ) {

            $sql .= "AND
                        vstoobroid = " . intval( $parametros->obroid ) . "";

        }

        if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            } else {
                 $sql .=  ' ORDER BY tipvdescricao';
            }

            $sql .= " LIMIT " . $paginacao->limite . " OFFSET " . $paginacao->offset;
        }

		$rs = $this->executarQuery($sql);

		if (is_null($paginacao)) {
            return pg_fetch_object($rs);
        } else {
            while($registro = pg_fetch_object($rs)){
               $registro->lista_obrigacao = explode(',', $registro->lista_obrigacao);
			   $retorno[] = $registro;
            }

            return $retorno;
        }
	}

	public function pesquisarPorID( $id ){

		$retorno = new stdClass();

		$sql = "SELECT DISTINCT
                    tipvoid,
					vstoid,
					STRING_AGG(vstoobroid::VARCHAR, ',') over(PARTITION BY vstovstoid) as lista_obrigacao,
                    vstdescricao,
                    tipvdescricao
				FROM
					veiculo_subtipo_obrigacao_financeira
                INNER JOIN
                    veiculo_tipo_subtipo ON (vtsvstoid = vstovstoid)
                INNER JOIN
                    veiculo_subtipo ON (vstoid = vtsvstoid)
                INNER JOIN
                    tipo_veiculo ON (tipvoid = vtstipvoid)
				WHERE
					vstovstoid =" . $id ."
                AND
                    vstodt_exclusao IS NULL";

		$rs = $this->executarQuery($sql);

        if ( pg_num_rows($rs) > 0 ){
            $registro = pg_fetch_object($rs);
            $registro->lista_obrigacao = explode(',', $registro->lista_obrigacao);

            $retorno = $registro;
        }

		return $retorno;
	}

	public function inserir(stdClass $dados){

        $sql = "PREPARE prepared_statement AS INSERT INTO veiculo_subtipo_obrigacao_financeira
                (vstovstoid, vstoobroid, vstousuoid_cadastro) VALUES ($1, $2, $3)";

        $rs = $this->executarQuery($sql);

        foreach ($dados->vstoid as $subtipo) {

            foreach ($dados->obroid as $obrigacao) {

                $sql = "EXECUTE prepared_statement
                        (
                            ". $subtipo .",
                            ". $obrigacao .",
                            ". $this->usuarioLogado ."
                        )";

                $rs = $this->executarQuery($sql);
            }
        }

		return TRUE;
	}

	public function atualizar(stdClass $dados){

        //Exclusao logica das obrigacoes removidas na edicao
        $sql = "UPDATE
                    veiculo_subtipo_obrigacao_financeira
                SET
                    vstodt_exclusao = NOW()
                WHERE
                    vstovstoid  = " . $dados->vstoid[0] ."
                AND
                    vstodt_exclusao IS NULL
                AND
                   vstoobroid NOT IN (". implode(',', $dados->obroid) .")";

        $rs = $this->executarQuery($sql);

		return TRUE;
	}

	public function excluirRegistro( $id ){

		$sql = "UPDATE
					veiculo_subtipo_obrigacao_financeira
				SET
					vstodt_exclusao = NOW()
				WHERE
					vstovstoid = " . $id ;

		$rs = $this->executarQuery($sql);

		return TRUE;
	}

}

