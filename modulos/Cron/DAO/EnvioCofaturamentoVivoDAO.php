<?php


/**
 * Classe de persistência em banco
 *
 * @author andre.zilz <andre.zilz@meta.com.br>
 * @package Cron
 * @since 23/12/2013
 */
class EnvioCofaturamentoVivoDAO {

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
     * Seleciona notas que serão faturadas pela VIVO
     * @return array
     */
    public function pesquisarTitulosVencer() {

        $retorno = array();

        $sql = "
           -------------------------------------------------
           -------- SERIE SL
           -------------------------------------------------
           SELECT * FROM (
               SELECT
                   vppasubscription AS subscription
                   ,nfiobroid AS obroid
                   ,TO_CHAR(condt_ini_vigencia, 'YYYYmmdd') AS data_vigencia
                   ,(CASE WHEN (nflserie = 'A' OR nflserie = 'SL')
                   THEN titno_parcela
                   ELSE 0
                   END) AS nr_parcela
                   ,(CASE WHEN (nflserie = 'A' OR nflserie = 'SL')
                   THEN MAX(titno_parcela) OVER (PARTITION BY titnfloid)
                   ELSE 0
                   END) AS total_parcelas
                   ,COALESCE(TO_CHAR(nfidt_referencia, 'YYYYmm'),'' )as data_referencia
                   ,COALESCE(nfivl_item,0) AS nfivl_liquido
                   ,COALESCE(nfivl_item,0) AS nfivl_servico
                   ,(MIN(nfldt_vencimento) OVER (PARTITION BY nficonoid) ) as vencimento
                   ,nfldt_vencimento
                   ,nfloid
                   ,connumero
                   ,nfioid
               FROM
                   nota_fiscal
               INNER JOIN
                   nota_fiscal_item ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
               INNER JOIN
                   contrato ON connumero = nficonoid
               INNER JOIN
                   tipo_contrato ON tpcoid = conno_tipo
               INNER JOIN
                   veiculo_pedido_parceiro ON (vppaconoid = connumero AND vppaveioid = conveioid AND vppasubscription IS NOT NULL)
               INNER JOIN
                   titulo ON titnfloid = nfloid
               WHERE
                   nflserie = 'SL'
               AND
                   nfldt_faturamento IS NULL
               AND
                   condt_exclusao IS NULL
               AND
                   conmodalidade = 'L'
               AND
                   tpcdescricao ILIKE 'vivo'
               AND NOT EXISTS (
                       SELECT
                           1
                       FROM
                           historico_nf_envio_vivo
                       WHERE
                           henvconnumero = connumero
                       AND
                           henvnfloid = nfloid
                       )
           ) AS FOO
           WHERE
                nfldt_vencimento = vencimento

           UNION ALL
           -------------------------------------------------
           -------- SERIE A
           -------------------------------------------------

           SELECT
               vppasubscription AS subscription
               ,nfiobroid AS obroid
               ,TO_CHAR(condt_ini_vigencia, 'YYYYmmdd') AS data_vigencia
               ,(CASE WHEN (nflserie = 'A' OR nflserie = 'SL')
               THEN titno_parcela
               ELSE 0
               END) AS nr_parcela
               ,(CASE WHEN (nflserie = 'A' OR nflserie = 'SL')
               THEN MAX(titno_parcela) OVER (PARTITION BY titnfloid)
               ELSE 0
               END) AS total_parcelas
               ,COALESCE(TO_CHAR(nfidt_referencia, 'YYYYmm'),'' )as data_referencia
               ,COALESCE(nfivl_item,0) AS nfivl_liquido
               ,COALESCE(nfivl_item,0) AS nfivl_servico
               ,(MIN(nfldt_vencimento) OVER (PARTITION BY nficonoid) ) as vencimento
               ,nfldt_vencimento
               ,nfloid
               ,connumero
               ,nfioid

           FROM
               nota_fiscal
           INNER JOIN
               nota_fiscal_item ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
           INNER JOIN
               contrato ON connumero = nficonoid
           INNER JOIN
               tipo_contrato ON tpcoid = conno_tipo
           INNER JOIN
               veiculo_pedido_parceiro ON (vppaconoid = connumero AND vppaveioid = conveioid AND vppasubscription IS NOT NULL)
           INNER JOIN
               titulo ON titnfloid = nfloid
           WHERE
               nflserie = 'A'
          AND
              nfldt_faturamento IS NULL
           AND
               TRIM(nflnatureza) NOT ILIKE 'VENDA DE EQUIPAMENTO'
           AND
               condt_exclusao IS NULL
           AND
               tpcdescricao ILIKE 'vivo'

           AND NOT EXISTS (
                   SELECT
                       1
                   FROM
                       historico_nf_envio_vivo
                   WHERE
                       henvconnumero = connumero
                    AND
                       henvnfloid = nfloid
                   )

           UNION ALL
           -------------------------------------------------
           -------- SERIE <> SL e A
           -------------------------------------------------
           SELECT
               vppasubscription AS subscription
               ,nfiobroid AS obroid
               ,TO_CHAR(condt_ini_vigencia, 'YYYYmmdd') AS data_vigencia
               ,0 AS nr_parcela
               ,(CASE WHEN (nflserie = 'A' OR nflserie = 'SL')
               THEN MAX(titno_parcela) OVER (PARTITION BY titnfloid)
               ELSE 0
               END) AS total_parcelas
               ,COALESCE(TO_CHAR(nfidt_referencia, 'YYYYmm'),'' )as data_referencia
               ,COALESCE(nfivl_item,0) AS nfivl_liquido
               ,COALESCE(nfivl_item,0) AS nfivl_servico
               ,(MIN(nfldt_vencimento) OVER (PARTITION BY nficonoid) ) as vencimento
               ,nfldt_vencimento
               ,nfloid
               ,connumero
               ,nfioid

           FROM
               nota_fiscal
           INNER JOIN
               nota_fiscal_item ON (nfino_numero = nflno_numero AND nfiserie = nflserie)
           INNER JOIN
               contrato ON connumero = nficonoid
           INNER JOIN
               tipo_contrato ON tpcoid = conno_tipo
           INNER JOIN
               veiculo_pedido_parceiro ON (vppaconoid = connumero AND vppaveioid = conveioid AND vppasubscription IS NOT NULL)
           INNER JOIN
               titulo ON titnfloid = nfloid
           WHERE
               nflserie != 'SL'
           AND
            nflserie != 'A'
           AND
               nfldt_faturamento IS NULL
           AND
               condt_exclusao IS NULL
           AND
               tpcdescricao ILIKE 'vivo'
           AND NOT EXISTS (
                   SELECT
                       1
                   FROM
                       historico_nf_envio_vivo
                   WHERE
                       henvconnumero = connumero
                   AND
                       henvnfloid = nfloid
                   )
            ";
//echo "<pre>";
//die($sql);
        if (!$rs = pg_query($this->conn,$sql)) {
              throw new Exception("Erro ao executar a query <pre>" . $sql);
        }

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[] = $tupla;
        }

        return $retorno;

    }

    /**
     * Busca as informações financeiras utilizadas pela VIVO
     * Tratadas conforme descricao da VIVO
     * @return array
     */
    public function buscarDadosObrigacao() {

        $descIBotton = 'i%botton';

        $retorno = array();

        $sql = "
           SELECT
               obroid,
               (CASE WHEN
                   obrobrigacao ILIKE '".$descIBotton."'
               THEN
                   'ACESSASIBT'
               ELSE
                   'EQUSASSIMP'
               END) AS codigo_plano_vivo,
               (CASE WHEN
                   obrobrigacao ILIKE '".$descIBotton."'
               THEN
                   'ACESSORIO SASCAR IBOTTON'
               ELSE
                   'EQUIPAMENTO SASCAR SIMPLES'
               END) AS desc_plano_vivo
           FROM
               obrigacao_financeira
           WHERE
               obrobrigacao ILIKE '".$descIBotton."'
           OR
               obroid = 39; -- LOCAÇÃO SASCAR (GSM/GPS)
            ";

        if (!$rs = pg_query($this->conn,$sql)) {
             throw new Exception("Erro ao executar a query <pre>" . $sql);
        }

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[$tupla->obroid][0] = $tupla->codigo_plano_vivo;
            $retorno[$tupla->obroid][1] = $tupla->desc_plano_vivo;
        }

        return $retorno;

    }

    /**
     * Grava o histórico de notas fiscais enviadas
     *
     * @param stdClass $dados
     * @throws Exception
     */
    public function inserirHistorico($dados) {

        $sql = "
            PREPARE insert_historico AS
               INSERT INTO
                   historico_nf_envio_vivo
               (
                   henvdt_envio,
                   henvnfloid,
                   henvregistro_lote,
                   henvconnumero,
                   henvnome_arquivo,
                   henvnfioid,
                   henvsubscription
               )
               VALUES (NOW(), $1,$2,$3,$4,$5,$6)
            ";

        if (!pg_query($this->conn,$sql)) {
            throw new Exception("Erro ao executar a query <pre>" . $sql);
        }

        foreach ($dados as $dado) {

           $sql = "
               EXECUTE
                  insert_historico
              (
               ".intval($dado->nfloid).",
               ".intval($dado->lote).",
               ".intval($dado->connumero).",
               '".$dado->arquivo."',
               '".$dado->nfioid."',
               '".$dado->subscription."'
              )
               ";

           if (!pg_query($this->conn,$sql)) {
               throw new Exception("Erro ao executar a query <pre>" . $sql);
           }
        }
     }

}

?>
