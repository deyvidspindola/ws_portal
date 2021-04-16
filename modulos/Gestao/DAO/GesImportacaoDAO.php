<?php

/**
 * Classe GesImportacaoDAO.
 * Camada de modelagem de dados.
 *
 * @package Gestao
 * @author  João Paulo Tavares da Silva <joao.silva@meta.com.br>
 *
 */
class GesImportacaoDAO{

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

    public function buscarPermissoesUsuario($funoid){

        $sql = "SELECT
                    gmpimportacao AS importacao,
                    gmpsuper_usuario AS super_usuario
                FROM
                    gestao_meta_permissao
                WHERE
                    gmpfunoid = " . intval($funoid) . "
                LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return pg_fetch_object($rs);
    }

    public function buscarIndicadorMeta(stdClass $parametros){

        $sql = "SELECT
                    gimgmioid
                FROM
                    gestao_meta_indicadores_meta
                WHERE
                    gimgmeoid = " . $parametros->idMeta . "
                    AND gimgmioid = " . $parametros->idIndicador . "
                    AND gimdata = '" . $parametros->data . "'
                LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return pg_fetch_object($rs);
    }

    public function buscarIndicador(stdClass $parametros){

        $sql = "SELECT
                    gmioid
                FROM
                    gestao_meta_indicadores
                WHERE
                    gmicodigo = '" . $parametros->codIndicador . "'
                LIMIT 1;";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return pg_fetch_object($rs);
    }

    public function buscarMeta(stdClass $parametros){

        $sql = "SELECT
                    gmeoid
                FROM
                    gestao_meta
                WHERE
                    gmecodigo = '" . $parametros->codMeta . "'
                LIMIT 1;";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return pg_fetch_object($rs);
    }

    public function gravarIndicadorMeta(stdClass $parametros){

        $sql = "INSERT INTO gestao_meta_indicadores_meta (
                   gimusuoid_cadastro,
                   gimdt_cadastro,
                   gimdata,
                   gimvalor_previsto,
                   gimvalor_realizado,
                   gimgmeoid,
                   gimgmioid
                ) VALUES (
                    " . $parametros->usuario . ",
                    NOW(),
                    '" . $parametros->data . "',
                    " . $parametros->valorPrevisto . ",
                    " . $parametros->valorRealizado . ",
                    " . $parametros->idMeta . ",
                    " . $parametros->idIndicador . "
                )";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

    }

    public function atualizarIndicadorMeta(stdClass $parametros){

        $sql = "
            UPDATE gestao_meta_indicadores_meta SET
                    gimdt_cadastro = NOW(),";

        if(isset($parametros->valorPrevisto) && !empty($parametros->valorPrevisto)){
            $sql .= "
                gimvalor_previsto = " . $parametros->valorPrevisto . ",";
        }

        if(isset($parametros->valorRealizado) && !empty($parametros->valorRealizado)){
            $sql .= "
                gimvalor_realizado = " . $parametros->valorRealizado . ",";
        }

        $sql .= "
                gimusuoid_cadastro = " . $parametros->usuario . "
            WHERE
                gimgmioid = " . $parametros->idIndicador . "
                AND gimgmeoid = " . $parametros->idMeta . "
                AND gimdata = '" . $parametros->data . "'";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

    }

    public function buscarPlanoDeAcao(stdClass $parametros){
        $sql = "SELECT
                    gploid,
                    gplfunoid_responsavel
                FROM
                    gestao_meta_plano_acao
                WHERE
                    gploid = " . $parametros->idPlanoDeAcao;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        return pg_fetch_object($rs);
    }

    public function gravarAcao(stdClass $parametros){

        $sql = "INSERT INTO gestao_meta_acao (
                    gmagploid,
                    gmanome,
                    gmadt_inicio_previsto,
                    gmadt_fim_previsto,
                    gmapercentual,
                    gmastatus,
                    gmatipo,
                    gmafunoid_responsavel,
                    gmausuoid_importacao,
                    gmadt_importacao
                ) VALUES (
                    " . $parametros->idPlanoDeAcao . ",
                    '" . $parametros->nomeAcao . "',
                    '" . $parametros->dataInicio . "',
                    '" . $parametros->dataFim . "',
                    0,
                    'I',
                    'P',
                    " . $parametros->funoid . ",
                    " . $parametros->usuario . ",
                    NOW()
                )";

        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        $_SESSION['cache_arvore']['atualizado'] = strtotime(date('Y-m-d H:i:s'));
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