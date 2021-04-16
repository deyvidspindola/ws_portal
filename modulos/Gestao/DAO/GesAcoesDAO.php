<?php

/**
 * Classe GesAcoesDAO.
 * Camada de modelagem de dados.
 *
 * @package Relatorio
 * @author  Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 *
 */
class GesAcoesDAO{

	/**
     * Mensagem de erro padrão.
     *
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    private $conn;

	public function __construct(){
		global $conn;
        $this->conn = $conn;
	}

    public function buscarResponsaveis($meta, $ano, $responsavel = '') {

       $condicao = array('','','');

        if ($meta > 0) {
            $condicao[0] = "AND gmcgmeoid = $meta";
            $condicao[1] = "AND gmeoid = $meta";
        }

        if(!empty($responsavel)) {
            $condicao[2] = "AND funoid =" . intval($responsavel);
        }

        $sql = "SELECT
                    DISTINCT ON (funoid) funoid,
                    funnome,
                    gmefunoid_responsavel,
                    '".$responsavel."' AS responsavel
                FROM (
                        (
                        SELECT
                            funoid,
                            funnome,
                            gmefunoid_responsavel
                        FROM
                            gestao_meta
                        INNER JOIN
                            gestao_meta_compartilhada ON gmcgmeoid = gmeoid
                        INNER JOIN
                            funcionario ON (funoid = gmcfunoid OR funoid = gmefunoid_responsavel)
                        WHERE
                            gmedt_exclusao IS NULL
                        AND
                            gmeano = ".intval($ano)."
                        ".$condicao[0]."
                          )

                        UNION ALL

                        (
                        SELECT
                            funoid,
                            funnome,
                            gmefunoid_responsavel
                         FROM
                            funcionario
                         INNER JOIN
                            gestao_meta_arvore ON (gmafunoid = funoid)
                        INNER JOIN
                            gestao_meta ON (gmefunoid_responsavel = funoid)
                        WHERE
                            gmedt_exclusao IS NULL
                        AND
                            gmeano = ".intval($ano)."
                        ".$condicao[1]."
                        ".$condicao[2]."
                        )
                    ) AS foo
                    ";
//echo "<pre>" . $sql;exit;
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarResponsavelPlanoAcao($meta, $plano) {

        $sql = "
            SELECT
                gplfunoid_responsavel
            FROM
                gestao_meta_plano_acao
            WHERE
                gploid = " . intval($plano) ."
             LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $registro = pg_fetch_object($rs);

        if(pg_num_rows($rs) > 0) {
            return $registro->gplfunoid_responsavel;
        }

        return 0;
    }

    public function buscarPlanosAcao($ano) {

        $sql = "SELECT
                    gploid,
                    gplnome
                FROM
                    gestao_meta_plano_acao
                INNER JOIN
                    gestao_meta ON gmeoid = gplgmeoid
                WHERE
                    gmedt_exclusao IS NULL
                AND
                    gmeano = $ano
                ORDER BY
                    gplnome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarDepartamentos(){

        $retorno = array();

        $sql = "SELECT
                    depoid,
                    depdescricao,
                    depexclusao
                FROM
                    departamento
                WHERE
                    depexclusao = null
                ORDER BY depdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function inserirAcao($dados) {

        $andamento = ($dados->andamento) ? "'".$dados->andamento."'" : 'NULL';
        $compartilhar = ($dados->compartilhar == 1) ? 1 : 0;
        $inicio_realizado = ($dados->inicio_realizado) ? "'".$dados->inicio_realizado."'" : 'NULL';
        $fim_realizado = ($dados->fim_realizado) ? "'".$dados->fim_realizado."'" : 'NULL';

        $sql = "INSERT INTO
                    gestao_meta_acao (
                        gmagploid,
                        gmanome,
                        gmafunoid_responsavel,
                        gmatipo,
                        gmafato_causa,
                        gmadt_inicio_previsto,
                        gmadt_fim_previsto,
                        gmadt_inicio_realizado,
                        gmadt_fim_realizado,
                        gmapercentual,
                        gmaandamento,
                        gmamotivo_cancelamento,
                        gmacompartilhar,
                        gmastatus
                    ) VALUES (
                        " . $dados->plano . ",
                        '" . $dados->nome_acao . "',
                        " . $dados->responsavel . ",
                        '" . $dados->tipo . "',
                        '" . $dados->fato_causa . "',
                        '" . $dados->inicio_previsto . "',
                        '" . $dados->fim_previsto . "',
                        " . $inicio_realizado . ",
                        " . $fim_realizado . ",
                        " . $dados->percentual . ",
                        " . $andamento . ",
                        '" . $dados->motivo_cancelamento . "',
                        " . $compartilhar . ",
                        '" . $dados->status. "'
                    ) RETURNING gmaoid;";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return pg_fetch_object($rs);
    }

    public function editarAcao($dados) {

        $andamento = ($dados->andamento) ? "'".$dados->andamento."'" : 'NULL';
        $compartilhar = ($dados->compartilhar == 1) ? 1 : 0;
        $inicio_realizado = ($dados->inicio_realizado) ? "'".$dados->inicio_realizado."'" : 'NULL';
        $fim_realizado = ($dados->fim_realizado) ? "'".$dados->fim_realizado."'" : 'NULL';
        
        if ($dados->andamento == "F") {
            $dados->status = "C";
        } 

        if ($dados->andamento == "C") {
            $dados->status = "N";
        } 

        $sql = "UPDATE
                    gestao_meta_acao
                SET
                    gmagploid = " . $dados->plano . ",
                    gmanome = '" . $dados->nome_acao . "',
                    gmafunoid_responsavel = " . $dados->responsavel . ",
                    gmatipo = '" . $dados->tipo . "',
                    gmafato_causa = '" . $dados->fato_causa . "',
                    gmadt_inicio_previsto = '" . $dados->inicio_previsto . "',
                    gmadt_fim_previsto = '" . $dados->fim_previsto . "',
                    gmadt_inicio_realizado = " . $inicio_realizado . ",
                    gmadt_fim_realizado = " . $fim_realizado . ",
                    gmapercentual = " . $dados->percentual . ",
                    gmaandamento = " . $andamento . ",
                    gmamotivo_cancelamento = '" . $dados->motivo_cancelamento . "',
                    gmacompartilhar = " . $compartilhar . ",
                    gmastatus = '" . $dados->status. "'
                WHERE
                    gmaoid = " . $dados->id_acao;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function buscarAcaoPorId($acao) {

       $sql = "SELECT

                    gmaoid as id_acao,
                    gploid as id_plano_acao,
                    gmanome as nome,
                    gmafunoid_responsavel as responsavel,
                    gmatipo as tipo,
                    gmafato_causa as fato_causa,
                    to_char(gmadt_inicio_previsto, 'DD/MM/YYYY') as data_inicio_previsto,
                    to_char(gmadt_fim_previsto, 'DD/MM/YYYY') as data_fim_previsto,
                    to_char(gmadt_inicio_realizado, 'DD/MM/YYYY') as data_inicio_realizado,
                    to_char(gmadt_fim_realizado, 'DD/MM/YYYY') as data_fim_realizado,
                    gmapercentual as percentual,
                    gmaandamento as andamento,
                    gmamotivo_cancelamento as motivo_cancelamento,
                    gmacompartilhar as compartilhar,
                    gmastatus as status
                FROM
                    gestao_meta_plano_acao
                LEFT JOIN
                    gestao_meta_acao ON gmagploid = gploid
                LEFT JOIN
                    funcionario ON gmafunoid_responsavel = funoid
                WHERE
                    gmaoid = " . $acao;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function inserirItemAcao($dados) {

        $descricao = pg_escape_string($dados->descricao);

        $sql = "INSERT INTO
                    gestao_meta_acao_item (
                        gaigmaoid,
                        gaiusuoid_cadastro,
                        gaidescricao,
                        gaidt_cadastro
                    )
                    VALUES (
                        " . $dados->id_acao . ",
                        " . intval($_SESSION['usuario']['oid']) . ",
                        '" . $descricao . "',
                        NOW()
                    )";

        if (!$rs = pg_query($this->conn, $sql)) {
            return true;
        }

        return false;
    }

    public function buscarItemAcao($dados) {

         $sql = "SELECT
                    gaigmaoid as id_acao,
                    nm_usuario as usuario,
                    gaidescricao as descricao,
                    to_char(gaidt_cadastro, 'DD/MM/YYYY HH24:MM:SS') as data_cadastro
                FROM
                    gestao_meta_acao_item
                INNER JOIN
                    usuarios ON gaiusuoid_cadastro = cd_usuario
                WHERE
                    gaigmaoid = " . intval($dados->id_acao);

        if (!$rs = pg_query($this->conn, $sql)) {
            return array('erro' => 1);
        }

         while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return array('erro' => 0, 'dados' => $retorno);

    }

    public function buscarTotalizadoresTabelaStatus ($idFuncionario) {

        $retorno = array();

         $sql = "
             SELECT
                CASE
                    WHEN gmafunoid_responsavel = " . $idFuncionario . " THEN 'minhas'
                    ELSE 'subordinados'
                END AS responsavel
                ,CASE
                    WHEN gmastatus = 'I' THEN 'TOTAL DE AÇÕES A INICIAR'
                    WHEN gmastatus = 'C' THEN 'TOTAL DE AÇÕES CONCLUÍDAS'
                    WHEN gmastatus = 'A' THEN 'TOTAL DE AÇÕES ABERTAS'
                    WHEN gmastatus = 'T' THEN 'TOTAL DE AÇÕES ATRASADAS'
                END AS status
                ,COUNT(gmastatus) AS quantidade
            FROM
                gestao_meta_acao
            WHERE
                gmastatus IN ('I', 'C', 'A', 'T')
                AND (
                    gmafunoid_responsavel = " . $idFuncionario . "
                    OR gmafunoid_responsavel IN (
                                SELECT
                                    gmafunoid
                                FROM
                                    gestao_meta_arvore
                                WHERE
                                    gmafunoid_superior = " . $idFuncionario . "
                    )
                )
            GROUP BY
                responsavel,
                status
            ORDER BY
                status";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            while ($registro = pg_fetch_object($resultado)) {
                $retorno[] = $registro;
            }
        }

        return $retorno;
    }

    public function buscarIdFuncionario ($idUsuario) {

        $sql = "
            SELECT
                usufunoid
            FROM
                usuarios
            WHERE
                cd_usuario = " . $idUsuario;

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_result($resultado, 0, 0);
        } else {
            return 0;
        }
    }

    public function begin(){
        $sql = 'BEGIN;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function rollback(){
        $sql = 'ROLLBACK;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
    public function commit(){
        $sql = 'COMMIT;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
}