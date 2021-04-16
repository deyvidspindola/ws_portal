<?php

/**
 * Classe CadMotivoDetalhadoReclamacaoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Ricardo Bonfim <renato.bueno@meta.com.br>
 *
 */
class CadMotivoDetalhadoReclamacaoDAO {

    /**
     * Conexão com o banco de dados
     * @var resource
     */
    private $conn;

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }

    /*
     * Busca todos os motivos detalhados cadastrados
     */
    public function buscarMotivosDetalhado() {

        $retorno = array();

        $sql = "
            SELECT
                mdroid,
                mdrdescricao
            FROM
                motivo_detalhado_reclamacao
            WHERE
                mdrdt_exclusao IS NULL
            ORDER BY
                mdrdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /*
     * Busca todos os motivos geral cadastrados
     */
    public function buscarMotivosGeral() {

        $retorno = array();

        $sql = "
            SELECT
                mtroid,
                mtrdescricao
            FROM
                motivo_reclamacao
            WHERE
                mtrexclusao IS NULL
            ORDER BY
                mtrdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;

    }

    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarPorMotivoGeral(stdClass $parametros, $pesquisaGeral = true) {

        $retorno = array();

        $sql = "
            SELECT 
                mdroid,
                mdrdescricao,
                mrmdmtroid,
                mrmdstatus
            FROM
                motivo_detalhado_reclamacao
            LEFT JOIN
                motivo_reclamacao_motivo_detalhado ON mdroid = mrmdmdroid AND mrmdmtroid = " . $parametros->motivo_geral ."
            WHERE
                mdrdt_exclusao IS NULL
            ORDER BY
                mdrdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            if (!$pesquisaGeral) {

                if ($row->mrmdstatus == 't') {
                    $retorno[] = $row->mdroid;    
                }
            } else {
                $retorno[] = $row;
            }
        }

        return $retorno;
    }

    public function pesquisarPorMotivoDetalhado(stdClass $parametros, $pesquisaGeral = true) {

        $retorno = array();

        $sql = "
            SELECT 
                mtroid, mtrdescricao, mrmdmtroid, mrmdstatus 
            FROM 
                motivo_reclamacao 
            LEFT JOIN 
                motivo_reclamacao_motivo_detalhado ON mtroid = mrmdmtroid AND mrmdmdroid = " . $parametros->detalhamento_motivo . "
            WHERE 
                mtrexclusao IS NULL 
            ORDER BY 
                mtrdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            if (!$pesquisaGeral) {

                if ($row->mrmdstatus == 't') {
                    $retorno[] = $row->mtroid;    
                }
            } else {
                $retorno[] = $row;
            }
        }

        return $retorno;
    }


    public function atualizarVinculo($motivo_detalhado, $motivo_geral, $status = true){

        $status = ($status) ? 't' : 'f';

        $sql = "UPDATE 
                    motivo_reclamacao_motivo_detalhado
                SET 
                    mrmddt_atualizacao = NOW(),
                    mrmdusuoid_atualizacao = " . $_SESSION['usuario']['oid'] . ",
                    mrmdstatus = '" . $status . "'
                WHERE
                    mrmdmtroid = " . $motivo_geral ." AND
                    mrmdmdroid = " . $motivo_detalhado;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;

    }

    public function inserirVinculo($motivo_detalhado, $motivo_geral) {

        $sql = "INSERT INTO 
                    motivo_reclamacao_motivo_detalhado (
                        mrmdmtroid,
                        mrmdmdroid,
                        mrmddt_cadastro,
                        mrmdusuoid_cadastro,
                        mrmddt_atualizacao,
                        mrmdusuoid_atualizacao,
                        mrmdstatus
                    ) VALUES (
                        " . $motivo_geral . ",
                        " . $motivo_detalhado . ",
                        NOW(),
                        " . $_SESSION['usuario']['oid'] . ",
                        NOW(),
                        " . $_SESSION['usuario']['oid'] . ",
                        't'
                    )";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;

    }

    public function verificarVinculo($motivo_detalhado, $motivo_geral) {

        $sql = "SELECT
                    mrmdmtroid,
                    mrmdmdroid,
                    mrmdstatus
                FROM
                    motivo_reclamacao_motivo_detalhado
                WHERE
                    mrmdmtroid = " . $motivo_geral . " AND
                    mrmdmdroid = " . $motivo_detalhado;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            return true;
        }

        return false;

    }

    public function buscarMotivosDetalhadosSemVinculo(){

        $retorno = array();

        $sql = "SELECT
                    mdroid,
                    mdrdescricao
                FROM
                    motivo_detalhado_reclamacao
                WHERE
                    mdrdt_exclusao IS NULL AND
                    mdroid NOT IN (
                        SELECT
                            mrmdmdroid
                        FROM
                            motivo_reclamacao_motivo_detalhado
                    )
                ORDER BY
                    mdrdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            $retorno[] = $row;

        }

        return $retorno;
    }

    public function excluir($idExclusao){

        $sql = "UPDATE 
                    motivo_detalhado_reclamacao
                SET
                    mdrdt_exclusao = NOW(),
                    mdrusuoid_exclusao = " . $_SESSION['usuario']['oid'] . "
                WHERE
                    mdroid = " . $idExclusao;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function cadastrar($descricao){

        $descricao = pg_escape_string($descricao);

        $sql = "INSERT INTO
                    motivo_detalhado_reclamacao (
                        mdrdescricao,
                        mdrdt_cadastro,
                        mdrusuoid_cadastro
                    )
                VALUES (
                    '" . $descricao . "',
                    NOW(),
                    " . $_SESSION['usuario']['oid'] . " 
                )";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

         return true;

    }

    public function pesquisaSemFiltro(){

        $retorno = array();

        $sql = "SELECT 
                    mtroid, mtrdescricao, mdrdescricao
                FROM
                    motivo_detalhado_reclamacao
                INNER JOIN
                    motivo_reclamacao_motivo_detalhado ON mrmdmdroid = mdroid
                INNER JOIN
                    motivo_reclamacao ON mrmdmtroid = mtroid
                WHERE
                    mrmdstatus = 't'
                ORDER BY
                    mtrdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

         while ($row = pg_fetch_object($rs)) {

            $retorno[$row->mtroid]['descricao'] = $row->mtrdescricao;
            $retorno[$row->mtroid]['motivos'][] = $row->mdrdescricao;

        }

        return $retorno;

    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

?>
