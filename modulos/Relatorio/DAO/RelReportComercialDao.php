<?php
/**
 * Report Comercial
 *
 * @package Relatório
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class RelReportComercialDao {

    /**
     * Conexão com o banco de dados.
     *
     * @var Resource
     */
    private $conn;

    /**
     * Mensagem de erro padrão.
     *
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     *
     * @param resource $conn conexão
     *
     * @return Void
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Método que atualiza o Report Comercial.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Boolean
     * @throws ErrorException
     */
    public function atualizarReportComercial(stdClass $param) {
        $info = array();

        if (isset($param->rpcarquivo) && trim($param->rpcarquivo) != '') {
            $info[] = "rpcarquivo = '".$param->rpcarquivo."'";
        }

        if (isset($param->rpcprocessando) && is_bool($param->rpcprocessando)) {
            $info[] = "rpcprocessando = ".($param->rpcprocessando ? 'TRUE' : 'FALSE');
        }

        $sql = "
            UPDATE
                report_comercial
            SET
                ".implode(',', $info)."
            WHERE
                0 = 0
        ";

        if (isset($param->rpcoid)) {
            $sql.= "
                AND
                    rpcoid = ".intval($param->rpcoid)."
            ";
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs)) {
            return true;
        }

        return false;
    }

    /**
     * Método que busca os Clientes.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarCliente($param) {
        $retorno = array();
        $sql = "
            SELECT
                clientes.clioid,
                clientes.clinome,
                CASE
                    WHEN clientes.clitipo = 'F' THEN
                        LPAD(CAST(clientes.clino_cpf AS VARCHAR), 11, '0')
                    WHEN clientes.clitipo = 'J' THEN
                        LPAD(CAST(clientes.clino_cgc AS VARCHAR), 14, '0')
                END clidocumento
            FROM
                clientes
            WHERE
                clientes.clidt_exclusao IS NULL
        ";

        if (isset($param->term)) {
            $sql.= "
                AND
                    clientes.clinome ILIKE '" . $param->term . "%'
            ";
        }

        $sql.= "
            ORDER BY
                clientes.clinome
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca as Regiões Comerciais.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarRegiaoComercialZona() {
        $retorno = array();
        $sql = "
            SELECT
                regiao_comercial_zona.rczoid,
                regiao_comercial_zona.rczcd_zona
            FROM
                regiao_comercial_zona
            WHERE
                regiao_comercial_zona.rczexclusao IS NULL
            ORDER BY
                rczcd_zona
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Reports Comerciais.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarReportComercial(stdClass $param = null) {
        $retorno = array();
        $sql = "
            SELECT
                report_comercial.rpcoid,
                report_comercial.rpcarquivo,
                report_comercial.rpcdt_referencia,
                report_comercial.rcpdt_solicitacao,
                report_comercial.rpcprocessando,
                usuarios.cd_usuario,
                usuarios.nm_usuario
            FROM
                report_comercial
                    INNER JOIN
                        usuarios ON report_comercial.rpcusuoid = usuarios.cd_usuario
            WHERE
                report_comercial.rcpdt_exclusao IS NULL
        ";

        if (is_object($param)) {
            if (isset($param->rpcoid) && is_array($param->rpcoid)) {
                $sql.= "
                    AND
                        report_comercial.rpcoid IN (".implode(',', $param->rpcoid).")
                ";
            }
        }

        $sql.= "
            ORDER BY
                rcpdt_solicitacao DESC,
                rpcoid DESC
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que busca os Reports Comerciais.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Mixed
     * @throws ErrorException
     */
    public function buscarReportComercialCron(stdClass $param) {
        $sql = "
            SELECT
                report_comercial.rpcoid,
                report_comercial.rpcarquivo,
                report_comercial.rpcdt_referencia,
                report_comercial.rcpdt_solicitacao,
                report_comercial.rpcprocessando,
                report_comercial.rpcusuoid,
                clientes.clioid,
                clientes.clinome
            FROM
                report_comercial
                    LEFT JOIN clientes ON report_comercial.rpcclioid = clientes.clioid
            WHERE
                report_comercial.rcpdt_exclusao IS NULL
        ";

        if (is_object($param)) {
            if (isset($param->rpcarquivo)) {
                if (trim($param->rpcarquivo) == '') {
                    $sql.= "
                        AND
                            report_comercial.rpcarquivo IS NULL
                    ";
                }
            }

            if (isset($param->rpcprocessando) && is_bool($param->rpcprocessando)) {
                $sql.= "
                    AND
                        report_comercial.rpcprocessando = ".($param->rpcprocessando ? 'TRUE' : 'FALSE')."
                ";
            }
        }

        $sql.= "
            ORDER BY
                rcpdt_solicitacao ASC,
                rpcoid ASC
            LIMIT
                1
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs)) {
            return pg_fetch_object($rs);
        }

        return false;
    }

    /**
     * Método que busca as Regiões Comerciais de um Report Comercial.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarReportComercialDmvCron(stdClass $param) {
        $retorno = array();
        $sql     = "
            SELECT
                regiao_comercial_zona.rczoid,
                regiao_comercial_zona.rczcd_zona
            FROM
                report_comercial_dmv
                    INNER JOIN
                        regiao_comercial_zona ON report_comercial_dmv.rpcdrczoid = regiao_comercial_zona.rczoid
        ";

        if (isset($param->rpcoid)) {
            $sql.= "
                WHERE
                    report_comercial_dmv.rpcdrpcoid = ".intval($param->rpcoid)."
            ";
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro->rczcd_zona;
        }

        return $retorno;
    }

    /**
     * Método que busca os dados do Report Comercial.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Array
     * @throws ErrorException
     */
    public function buscarTmpReportComercial(stdClass $param) {
        $retorno = array();

        if (!$this->verificarTabela('tb_tmp_report_comercial')) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $sql = "
            SELECT
                tb_tmp_report_comercial.*
            FROM
                tb_tmp_report_comercial
        ";

        if (isset($param->rpcdmv) && is_array($param->rpcdmv)) {
            foreach ($param->rpcdmv as $chave => $valor) {
                $param->rpcdmv[$chave] = "'".$valor."'";
            }

            $sql.= "
                WHERE
                    tb_tmp_report_comercial.dmv IN (".implode(', ', $param->rpcdmv).")
            ";
        }

        $sql.= "
            ORDER BY
                tb_tmp_report_comercial.dmv,
                tb_tmp_report_comercial.cliente
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    /**
     * Método que cria as tabelas temporárias.
     *
     * @param String $sql Comando de criação.
     *
     * @return Boolean
     * @throws ErrorException
     */
    public function criarTabelaTemporaria($sql) {
        if (strstr($sql, 'DROP TABLE IF EXISTS') === false) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        } elseif (strstr($sql, 'CREATE TEMPORARY TABLE') === false) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Método que exclui o Report Comercial.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Boolean
     * @throws ErrorException
     */
    public function excluirReportComercial(stdClass $param) {
        $sql = "
            UPDATE
                report_comercial
            SET
                rcpdt_exclusao = NOW()
            WHERE
                0 = 0
        ";

        if (isset($param->rpcoid) && is_array($param->rpcoid)) {
            $sql.= "
                AND
                    rpcoid IN (".implode(',', $param->rpcoid).")
            ";
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs)) {
            return true;
        }

        return false;
    }

    /**
     * Método que insere o Report Comercial.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Integer
     * @throws ErrorException
     */
    public function inserirReportComercial($param) {
        $sql = "
            INSERT INTO
                report_comercial
                    (
                        rpcdt_referencia,
                        rcpdt_solicitacao,
                        rpcusuoid,
                        rpcclioid
                    )
                VALUES
                    (
                        '01/" . $param->rpcdt_referencia . "'::DATE,
                        NOW(),
                        " . intval($_SESSION['usuario']['oid']) . ",
                        " . (trim($param->rpcclioid) ? intval($param->rpcclioid) : 'NULL') . "
                    )
            RETURNING
                rpcoid
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs)) {
            return pg_fetch_result($rs, 0, 'rpcoid');
        }

        return 0;
    }

    /**
     * Método que insere o Report Comercial DMV.
     *
     * @param stdClass $param Parâmetros.
     *
     * @return Boolean
     * @throws ErrorException
     */
    public function inserirReportComercialDmv($param) {
        $sql = "
            INSERT INTO
                report_comercial_dmv
                    (
                        rpcdrpcoid,
                        rpcdrczoid
                    )
                VALUES
        ";

        if (!isset($param->rpcdrczoid) || !is_array($param->rpcdrczoid)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        foreach ($param->rpcdrczoid as $indice => $rpcdrczoid) {
            $sql.= "
                (
                    " . intval($param->rpcoid) . ",
                    " . intval($rpcdrczoid) . "
                )" . ($indice + 1 == count($param->rpcdrczoid) ? '' : ',') . "
            ";
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs)) {
            return true;
        }

        return false;
    }

    /**
     * Método que verifica a existência de uma tabela.
     *
     * @param String $tabela Nome da tabela.
     *
     * @return Boolean
     * @throws ErrorException
     */
    private function verificarTabela($tabela = '') {
        $sql = "
            SELECT
                EXISTS (
                    SELECT
                        1
                    FROM
                        pg_class
                    WHERE
                        pg_class.relname = '$tabela'
                ) AS existe
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $registro = pg_fetch_object($rs);

        if ($registro->existe != 't') {
            return false;
        }

        return true;
    }

    /**
     * Método que abre uma transação.
     *
     * @return void
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Método que finaliza uma transação.
     *
     * @return void
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Método que aborta uma transação.
     *
     * @return void
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}