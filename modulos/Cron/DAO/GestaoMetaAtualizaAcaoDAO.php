<?php


/**
 * Classe de persistência em banco
 *
 * @author andre.zilz <andre.zilz@meta.com.br>
 * @package Cron
 * @since 11/02/2014
 */
class GestaoMetaAtualizaAcaoDAO {

    /**
     * conector do banco
     */
     private $conn;

    /**
     * Construtor da classe
     * @param resource $conn
     */
    public function __construct($conn) {

        $this->conn = $conn;
    }

    /**
     * Inicia uma transação
     */
    public function abrirTransacao() {
        pg_query($this->conn, "BEGIN;");
    }

     /**
      * Encerra uma transação
      */
     public function encerrarTransacao() {
         pg_query($this->conn, "COMMIT;");
     }

     /**
      * Aborta uma transação
      */
     public function abortarTransacao() {
         pg_query($this->conn, "ROLLBACK;");
     }

     /**
      * Atualiza o status das çãoes getsão meta
      *
      * @return int
      * @throws Exception
      */
     public function atualizarAcaoMeta () {

         $sql = "
            UPDATE
                gestao_meta_acao
            SET
                gmastatus =
                    (CASE WHEN (gmadt_inicio_realizado IS NOT NULL AND gmadt_fim_realizado IS NOT NULL)
                    THEN 'C'
                    WHEN ((NOW() > gmadt_inicio_previsto) AND gmadt_inicio_realizado IS NULL)
                    THEN 'T'
                    WHEN gmadt_inicio_realizado IS NOT NULL
                    THEN 'A'
                    ELSE gmastatus
                    END),
                gmapercentual =
                    (CASE WHEN (gmadt_inicio_realizado IS NOT NULL AND gmadt_fim_realizado IS NOT NULL)
                    THEN 100
                    ELSE gmapercentual
                    END),
                gmaandamento =
                    (CASE WHEN (gmadt_inicio_realizado IS NOT NULL AND gmadt_fim_realizado IS NOT NULL)
                    THEN 'F'
                    ELSE gmaandamento
                    END)
            WHERE
                gmastatus != 'C'
            AND
                gmastatus != 'N'
             ";

        $rs = pg_query($this->conn,$sql);

        if (!$rs = pg_query($this->conn,$sql)) {
              throw new Exception("Erro ao executar a query <pre>" . $sql);
        }

        return pg_affected_rows($rs);

     }

}