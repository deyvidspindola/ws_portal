<?php

/**
 * @author  Andre Zilz <andre.zilz@sascar.com.br>
 *
 */
class ManCobrarProdutosDivergentesDAO {


    private $conn;
    private $usuoid;

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Construtor da Classe
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @param ManCobrarProdutosDivergentesDAO $dao
     */
    public function __construct($conn) {
        //Seta a conexao na classe
        $this->conn = $conn;
        $this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
    }

    /** submete uma query a execucao do SGDB */
    private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            //echo"<pre>";var_dump($query);echo"</pre>";
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }

    /** Abre a transação  */
    public function begin(){
        pg_query($this->conn, 'BEGIN');
    }

    /** Finaliza um transacao   */
    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }

    /** Aborta uma transacao */
    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
    }

    /**
     * Pesquisa os produtos do representante com divergencia de estoque
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @param  INT $invoid | ID do inventario
     * @return Array
     */
    public function pesquisarEstoqueDivergente($invoid) {

        $retorno = array();

        $sql = "
            SELECT
                *,
                @(estoque - contagem) AS divergente,
                @((estoque - contagem) * custo_medio) AS valor_cobrar,
                (@custo_medio > 0) AS possui_custo,
                SUM(@((estoque - contagem)* custo_medio)) OVER() AS total_cobrar
            FROM
                (
                    SELECT
                        invpprdoid AS prdoid,
                        TO_CHAR(invdt_ajuste, 'DD/MM/YYYY HH24:MI') AS data_ajuste,
                        COALESCE(invpestoque_atual::INTEGER,0) AS estoque,
                        invpinvoid AS invoid,
                        invcobranca_divergente AS cobranca_divergente,
                        COALESCE(
                            (
                                SELECT
                                    pcmcusto_medio
                                FROM
                                    produto_custo_medio
                                WHERE
                                    pcmprdoid = invpprdoid
                                    AND pcmdt_exclusao IS NULL 
                                    AND pcmusuoid_exclusao IS NULL
                                ORDER BY
                                    pcmdt_referencia
                                DESC LIMIT 1
                            )
                        ,0.00) AS custo_medio,
                       (
                        CASE WHEN invpquarta_contagem IS NOT NULL THEN
                            invpquarta_contagem
                        WHEN invpterceira_contagem IS NOT NULL THEN
                            invpterceira_contagem
                        WHEN invpsegunda_contagem IS NOT NULL THEN
                            invpsegunda_contagem
                        ELSE
                            invpprimeira_contagem
                        END
                        )::INTEGER contagem,
                        prdproduto AS produto,
                        repoid,
                        repnome,
                        (SELECT itloid FROM instalador WHERE itlrepoid = invrepoid ORDER BY itlnome ASC LIMIT 1) AS itloid
                 FROM
                    inventario_produto
                 INNER JOIN
                    inventario ON (invpinvoid = invoid)
                 INNER JOIN
                    produto ON (prdoid = invpprdoid)
                 INNER JOIN
                    representante ON (repoid = invrepoid)
                WHERE
                    invpinvoid = ".intval($invoid)."
                AND
                    prdgrmoid IN (7,10)
                AND
                    prdptioid != 1 --imobilizado
                 ) AS FOO
            WHERE
                contagem < estoque";

        //echo"<pre>";var_dump($sql);echo"</pre>";exit();

        $recordset = $this->executarQuery($sql);

        while($registro = pg_fetch_object($recordset)) {
            $retorno[] = $registro;
        }

        return $retorno;

    }

    /**
     * Recupera dados dos produtos passiveis de cobranca
     * @author   André Zilz <andre.zilz@sascar.com.br>
     * @param  INT $invoid | ID do inventario
     * @param  string $produtos | ID de produtos separados por virgula
     * @return Array
     */
    public function recuperarDadosCobranca($invoid, $produtos) {

        $retorno = array();

        $sql = "
            SELECT
                *,
                @(estoque - contagem) AS divergente,
                @((estoque - contagem) * custo_medio) AS valor_cobrar,
                SUM(@((estoque - contagem)* custo_medio)) OVER() AS total_cobrar
            FROM
                (
                    SELECT
                        invpprdoid AS prdoid,
                        TO_CHAR(invdt_ajuste, 'DD/MM/YYYY HH24:MI') AS data_ajuste,
                        COALESCE(invpestoque_atual::INTEGER,0) AS estoque,
                        invpinvoid AS invoid,
                        invcobranca_divergente AS cobranca_divergente,
                        COALESCE(
                            (
                                SELECT
                                    pcmcusto_medio
                                FROM
                                    produto_custo_medio
                                WHERE
                                    pcmprdoid = invpprdoid
                                    AND pcmdt_exclusao IS NULL 
                                    AND pcmusuoid_exclusao IS NULL
                                ORDER BY
                                    pcmdt_referencia
                                DESC LIMIT 1
                            )
                        ,0.00) AS custo_medio,
                       (
                        CASE WHEN invpquarta_contagem IS NOT NULL THEN
                            invpquarta_contagem
                        WHEN invpterceira_contagem IS NOT NULL THEN
                            invpterceira_contagem
                        WHEN invpsegunda_contagem IS NOT NULL THEN
                            invpsegunda_contagem
                        ELSE
                            invpprimeira_contagem
                        END
                        )::INTEGER contagem,
                        prdproduto AS produto,
                        repoid,
                        repnome,
                        (SELECT itloid FROM instalador WHERE itlrepoid = invrepoid ORDER BY itlnome ASC LIMIT 1) AS itloid
                 FROM
                    inventario_produto
                 INNER JOIN
                    inventario ON (invpinvoid = invoid)
                 INNER JOIN
                    produto ON (prdoid = invpprdoid)
                 INNER JOIN
                    representante ON (repoid = invrepoid)
                WHERE
                    invpinvoid = ".intval($invoid)."
                AND NOT
                    invcobranca_divergente IS TRUE
                AND
                    invpprdoid IN (". $produtos.")
                AND
                    prdgrmoid IN (7,10)
                AND
                    prdptioid != 1 --imobilizado
                 ) AS FOO
            WHERE
                contagem < estoque";

        //echo"<pre>";var_dump($sql);echo"</pre>";exit();

        $recordset = $this->executarQuery($sql);

        while($registro = pg_fetch_object($recordset)) {
            $retorno[] = $registro;
        }

        return $retorno;

    }


    /**
     * Insere um registro na tabela [representante_desconto] e [representante_desconto_parcelas]
     * @author André Zilz <andre.zilz@sascar.com.br>
     * @param  stdClass $dados | objeto com os dados
     * @param  string $vencimento | Data de vencimento: YYYY-M-D
     * @param  string $obs | observacao
     * @return void
     */
    public function inserirDesconto($dados, $vencimento, $obs) {

        $sqlPrepareRepDesc = "
            PREPARE
                insert_representante_desconto AS
            INSERT INTO
                representante_desconto
            (
                redprdoid,
                redrepoid,
                reditloid,
                redusuoid,
                reddesc_produto,
                redvl_produto,
                redobservacao
            )
            VALUES
            ($1, $2, $3, $4, $5, $6, $7)
            RETURNING  redoid;
            ";

        //echo "<pre>";var_dump($sqlPrepareRepDesc);echo "</pre>";exit();

        $this->executarQuery($sqlPrepareRepDesc);

        $sqlPrepareRepDescParcela = "
            PREPARE
                insert_representante_desconto_parcelas AS
            INSERT INTO
                representante_desconto_parcelas
            (
                redpredoid,
                redpvencimento_desc,
                redpvl_parcela,
                redpnum_parcela
            )
            VALUES
            ($1, $2, $3, $4);
            ";

        //echo "<pre>";var_dump($sqlPrepare);echo "</pre>";exit();

       $this->executarQuery($sqlPrepareRepDescParcela);

        foreach ($dados as $dado) {

            $sql = "
                    EXECUTE
                        insert_representante_desconto
                        (
                            ".intval($dado->prdoid).",
                            ".intval($dado->repoid).",
                            ".intval($dado->itloid).",
                            ".$this->usuoid.",
                            '".$dado->produto."',
                            ".floatval($dado->valor_cobrar).",
                            '".$obs."'
                         )";

            //echo "<pre>";var_dump($sql);echo "</pre>";exit();

            $rs = $this->executarQuery($sql);
            if(pg_affected_rows($rs) > 0) {
                $registro = pg_fetch_object($rs);
                $redoid =  isset($registro->redoid) ? $registro->redoid : 0;
            } else {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $sql = "
                    EXECUTE
                        insert_representante_desconto_parcelas
                       (
                            ".$redoid.",
                            '".$vencimento."',
                            ".floatval($dado->valor_cobrar).",
                            1
                        )";

            //echo "<pre>";var_dump($sql);echo "</pre>";exit();

            $this->executarQuery($sql);
        }

        unset($dados);

    }


    /**
     * Atualiza o registro do inventario com dados da cobranca
     * @author André Zilz <andre.zilz@sascar.com.br>
     * @param  int $invoid | ID do inventario
     * @param  float $valorCobranca | valor totald a cobranca
     * @return void
     */
    public function atualizarInventario($invoid, $valorCobranca) {

        $sql = "
            UPDATE
                inventario
            SET
                invcobranca_divergente  = TRUE,
                invvalor_cobranca = ".floatval($valorCobranca)."
            WHERE
                invoid = ".intval($invoid)."";

        //echo "<pre>";var_dump($sql);echo "</pre>";exit();

        $this->executarQuery($sql);

    }


}