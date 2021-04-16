<?php

/**
 * Classe de persistência do banco de dados
 *
 * @package Gestão
 * @author  André Luiz Zilz <andre.zilz@meta.com.br>
 *
 */
class GesIndicadorDAO{

	/*
     * Mensagem de erro padrão.
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /*
     * Objeto de conexão com o banco
     */
    private $conn;

    /**
     * Contrutor da Classe
     * @param object $conn
     */
	public function __construct($conn){
        $this->conn = $conn;
	}

    /**
     * Busca pelos dados dos indicadores
     * @return type
     * @throws Exception
     */
    public function pesquisarDadosIndicadores($orderBy) {

        $retorno = array();

        $sql = "
            SELECT
                gmicodigo,
                gmioid,
                gminome
            FROM
                gestao_meta_indicadores
            WHERE
                gmistatus = 'A'
            ORDER BY
                ".$orderBy."
            ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($tuplas = pg_fetch_object($rs)) {
            $retorno[] = $tuplas;
        }

        return $retorno;

    }


    /**
     * Pesquisa pelos indicadores conforme filtros em tela.
     *
     * @param stdClass $filtro
     * @return array
     * @throws Exception
     */
    public function pesquisarIndicadores (stdClass $filtro) {

        $retorno = array();

        $sql = "
           SELECT
                gmioid,
                gmicodigo,
                gminome,
                gmiprecisao,
                gmimetrica,
                gmitipo,
                gmitipo_indicador
            FROM
                gestao_meta_indicadores
            WHERE
                1 = 1
            ";

        //Filtro Status
        if(!empty($filtro->gmistatus)) {
            $sql .=  "
                    AND gmistatus = '" . $filtro->gmistatus . "'" ;
        }

        //Filtro por ID ou Código Indicador
        if(!empty($filtro->gmioid)) {
             $sql .=  "
                    AND gmioid = " .  intval($filtro->gmioid) ;

        } else if(!empty($filtro->gmioid_nome) XOR !empty($filtro->gmicodigo)) {
            $sql .=  "
                    AND gmioid = " . intval($filtro->gmioid_nome . $filtro->gmicodigo);

        } else if (!empty($filtro->gmioid_nome) && !empty($filtro->gmicodigo)) {
            $sql .=  "
                    AND
                        (
                            gmioid = " .  intval($filtro->gmioid_nome) ."
                            OR
                            gmioid = " .  intval($filtro->gmicodigo) ."
                        ) ";
        }

        $sql .= "
            ORDER BY
                gminome
            ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($tuplas = pg_fetch_object($rs)) {
            $retorno[] = $tuplas;
        }

        return $retorno;

    }

    public function cadastrarIndicador(stdClass $dadosCadastro) {

        if (!isset($dadosCadastro->gmiprecisao) || trim($dadosCadastro->gmiprecisao) == '') {
            $dadosCadastro->gmiprecisao = 0;
        }

        $sql = "INSERT INTO
                    gestao_meta_indicadores (
                        gmicodigo,
                        gminome,
                        gmitipo,
                        gmimetrica,
                        gmiprecisao,
                        gmistatus,
                        gmitipo_indicador
                    ) VALUES (
                        '".$dadosCadastro->gmicodigo."',
                        '".$dadosCadastro->gminome."',
                        '".$dadosCadastro->gmitipo."',
                        '".$dadosCadastro->gmimetrica."',
                        ".$dadosCadastro->gmiprecisao.",
                        'A',
                        '".$dadosCadastro->gmitipo_indicador."'
                    );";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }


    public function atualizarIndicador(stdClass $dadosCadastro) {

        if (!isset($dadosCadastro->gmiprecisao) || trim($dadosCadastro->gmiprecisao) == '') {
            $dadosCadastro->gmiprecisao = 0;
        }

        $sql = "UPDATE gestao_meta_indicadores
                    SET
                        gminome = '".$dadosCadastro->gminome."',
                        gmimetrica = '".$dadosCadastro->gmimetrica."',
                        gmiprecisao = ".$dadosCadastro->gmiprecisao.",
                        gmitipo_indicador = '".$dadosCadastro->gmitipo_indicador."'
                    WHERE
                        gmioid = ".intval($dadosCadastro->gmioid).";
            ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }


    public function excluir(stdClass $filtros) {

        if (!isset($filtros->gmioid)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        //Verifica se existe alguma meta vinculada ao indicador e apenas o desativa
        $sql = "
            SELECT EXISTS(
                SELECT
                    1
                FROM
                    gestao_meta_indicadores_meta
                WHERE
                    gimgmioid = ". intval($filtros->gmioid) . "
            ) AS existe";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException (self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $verificaMeta = pg_fetch_object($rs);

        if ($verificaMeta->existe == 't'){
            $sql = "UPDATE gestao_meta_indicadores
                        SET
                            gmistatus = 'I'
                        WHERE
                            gmioid = ".intval($filtros->gmioid).";";
        } else {
            $sql = "DELETE FROM gestao_meta_indicadores WHERE gmioid = ".intval($filtros->gmioid).";";
        }

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }


    public function copiarIndicador($gmioid_origem){

        //Seleciona os dados de origem para cópia
        $sql = "
            SELECT
                gminome, gmicodigo
            FROM
                gestao_meta_indicadores
            WHERE
                gmioid = ". intval($gmioid_origem);
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) == 0){
            return false;
        }

        $dadosOrigem = pg_fetch_object($rs);
        //Remove a string "_cópia"
        $exgminome = explode("_cópia", $dadosOrigem->gminome);
        $dadosOrigem->gminome = $exgminome[0];


        //Pesquisa quantos registros foram copiados
        $sql = "
            SELECT
                COUNT(*) AS copiados
            FROM
                gestao_meta_indicadores
            WHERE
                gminome ILIKE '" . $dadosOrigem->gminome . "\_c_pia%'
            AND
                CHAR_LENGTH(gminome) <= " . strlen($dadosOrigem->gminome.'_cópia0')."
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $total = pg_fetch_object($rs);
        //Concatena as strings do nome e código
        if ($total->copiados > 0){
            $gminome = $dadosOrigem->gminome . '_cópia' . $total->copiados;
            $gmicodigo = $dadosOrigem->gmicodigo . '_' . ($total->copiados+1);
        } else {
            $gminome = $dadosOrigem->gminome . '_cópia';
            $gmicodigo = $dadosOrigem->gmicodigo . '_1';
        }

        //Copia os dados
        $sql = "INSERT INTO
                    gestao_meta_indicadores (
                    gmicodigo,
                    gminome,
                    gmitipo,
                    gmimetrica,
                    gmiprecisao,
                    gmistatus,
                    gmitipo_indicador)
                SELECT
                    '" . $gmicodigo . "',
                    '" . $gminome . "',
                    gmitipo,
                    gmimetrica,
                    gmiprecisao,
                    gmistatus,
                    gmitipo_indicador
                FROM
                    gestao_meta_indicadores
                WHERE
                    gmioid = ". intval($gmioid_origem);

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        return true;
    }

    public function validaCodigoExistente($dados) {

        $sql = "SELECT
                    COUNT(gmicodigo) AS existe
                FROM
                    gestao_meta_indicadores
                WHERE
                    gmicodigo = '$dados->gmicodigo'";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $codigo = pg_fetch_object($rs);

        return $codigo->existe;
    }


    /**
     * Inicia uma transação com o banco
     */
    public function iniciarTransacao(){
        pg_query($this->conn, 'BEGIN;');
    }

    /**
     * Reverte as ações de banco dentro da transação
     */
    public function reverterTransacao(){
        pg_query($this->conn, 'ROLLBACK;');
    }

    /**
     * Comita as ações de banco dentro da transação
     */
    public function comitarTransacao(){
        pg_query($this->conn, 'COMMIT;');
    }

}