<?php

/**
 * Classe GesAcoesDAO.
 * Camada de modelagem de dados.
 *
 * @package Relatorio
 * @author  Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 *
 */
class GesPlanoAcaoDAO{

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

    public function buscarMetas($ano) {

        $sql = "SELECT
                    *
                FROM
                    gestao_meta
                WHERE
                    gmedt_exclusao IS NULL
                AND
                    gmeano = $ano
                ORDER BY
                    gmenome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarResponsaveis($meta, $ano) {

        $condicao = array();

        if ($meta > 0) {
            $condicao[0] = "AND gmcgmeoid = $meta";
            $condicao[1] = "AND gmeoid = $meta";
        }

        $sql = "SELECT
                    DISTINCT ON (funoid) funoid,
                    funnome,
                    gmefunoid_responsavel
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
                        )
                    ) AS foo
                    ";
    
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

    public function inserirPlanoAcao($dados) {

        $compartilhar = (isset($dados->compartilhar)) ? $dados->compartilhar : 0;
        $titulo = pg_escape_string($dados->titulo);

        $sql = "INSERT INTO
                    gestao_meta_plano_acao (
                        gplgmeoid,
                        gplfunoid_responsavel,
                        gplnome,
                        gpldt_inicio,
                        gpldt_fim,
                        gplstatus,
                        gplcompartilhar
                    )
                VALUES (
                        ". $dados->meta.",
                        ". $dados->responsavel.",
                        '".$titulo."',
                        '". $dados->data_inicio."',
                        '". $dados->data_fim."',
                        '". $dados->status."',
                        ". $compartilhar ."
                );";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function alterarPlanoAcao($dados) {

        $compartilhar = (isset($dados->compartilhar)) ? $dados->compartilhar : 0;
        $titulo = pg_escape_string($dados->titulo);

        $sql = "UPDATE
                    gestao_meta_plano_acao
                SET
                    gplgmeoid = " . $dados->meta . ",
                    gplfunoid_responsavel = " . $dados->responsavel . ",
                    gplnome = '".$titulo."',
                    gpldt_inicio = '" . $dados->data_inicio . "',
                    gpldt_fim = '" . $dados->data_fim . "',
                    gplstatus = '". $dados->status."',
                    gplcompartilhar = ". $compartilhar ."
                WHERE
                    gploid = " . $dados->codigo;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

    }

    public function buscarPlanoPorId($plano) {

        $sql = "SELECT
                    gploid as codigo,
                    gplgmeoid as meta,
                    gplfunoid_responsavel as responsavel,
                    gplnome as nome,
                    to_char(gpldt_inicio, 'DD/MM/YYYY') as data_inicio,
                    to_char(gpldt_fim, 'DD/MM/YYYY') as data_fim,
                    gplstatus as status,
                    gplcompartilhar as compartilhar
                FROM
                    gestao_meta_plano_acao
                WHERE
                    gploid = " . $plano;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarAcoes($planoid) {

        $sql = "SELECT
                    gmaoid as id_acao,
                    gploid as id_plano_acao,
                    gplnome as plano_acao,
                    gmanome as descricao,
                    to_char(gmadt_inicio_previsto, 'DD/MM/YYYY') as data_inicio_previsto,
                    to_char(gmadt_fim_previsto, 'DD/MM/YYYY') as data_fim_previsto,
                    to_char(gmadt_fim_realizado, 'DD/MM/YYYY') as data_fim_realizado,
                    gmastatus as status,
                    gmapercentual as porcentagem,
                    funnome as responsavel
                FROM
                    gestao_meta_plano_acao
                LEFT JOIN
                    gestao_meta_acao ON gmagploid = gploid
                LEFT JOIN
                    funcionario ON gmafunoid_responsavel = funoid
                WHERE
                    gploid = " . $planoid;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
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