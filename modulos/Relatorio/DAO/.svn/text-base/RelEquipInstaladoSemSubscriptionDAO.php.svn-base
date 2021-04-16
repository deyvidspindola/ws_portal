<?php

/**
 * Classe RelEquipInstaladoSemSubscriptionDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   André Luiz Zilz <andre.zilz@meta.com.br>
 *
 */
class RelEquipInstaladoSemSubscriptionDAO {

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


    /**
     *  Construtor da Classe RelEquipInstaladoSemSubscriptionDAO
     * @param object $conn
     */
    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }

    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros
     * @return array
     * @throws ErrorException
     */
    public function pesquisar(stdClass $parametros){

        $retorno = array();

        $sql = "
            SELECT
                *
            FROM (
                    SELECT
                        DISTINCT connumero AS contrato,
                        veiplaca AS placa,
                        ordoid,
                        TO_CHAR(orsdt_situacao, 'dd/mm/YYYY') AS data_instalacao,
                        equno_serie AS serie,
                        conequoid,
                        clinome,
                        clitipo,
                        (CASE WHEN clitipo = 'J' THEN
                            clino_cgc
                        ELSE
                            clino_cpf
                        END) AS cpf_cnpj
                    FROM
                        ordem_servico
                    INNER JOIN
                        ordem_situacao ON orsordoid = ordoid
                    INNER JOIN
                        contrato ON connumero = ordconnumero
                    INNER JOIN
                        tipo_contrato ON tpcoid = conno_tipo AND tpcdescricao ILIKE 'Vivo'
                    INNER JOIN
                        veiculo ON veioid = ordveioid
                    INNER JOIN
                        Veiculo_Pedido_Parceiro ON (vppaveioid = ordveioid AND vppaconoid = connumero)
                    INNER JOIN
                        clientes ON clioid = ordclioid
                    INNER JOIN
                        equipamento ON equoid = conequoid
                    WHERE
                        ordstatus = 3 -- concluida
                    AND
                        vppasubscription IS NULL
                    AND
                        condt_exclusao IS NULL
                    AND
                        equno_serie IS NOT NULL
                    AND
                        orsdt_situacao = (
                                            SELECT
                                                orsdt_situacao
                                            FROM
                                                ordem_situacao
                                            WHERE
                                                orsordoid = ordoid
                                            ORDER BY
                                                orsdt_situacao DESC
                                            LIMIT 1
                                            )
                                    ";

        if ( (!empty($parametros->dataInicial)) && (!empty($parametros->dataFinal)) ) {
             $sql .=  "AND orsdt_situacao BETWEEN '".$parametros->dataInicial." 00:00:00' AND '".$parametros->dataFinal." 23:59:59'";
        } else if ( (empty($parametros->dataInicial)) && (!empty($parametros->dataFinal)) ) {
             $sql .=  "AND orsdt_situacao <= '".$parametros->dataFinal." 23:59:59'";
        } else if ( (empty($parametros->dataFinal)) && (!empty($parametros->dataInicial)) ) {
             $sql .=  "AND orsdt_situacao >= '".$parametros->dataInicial." 23:59:59'";
        }

        $sql .=  ") AS FOO
                    ORDER BY data_instalacao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            if ($row->clitipo == 'J') {
                $row->cpf_cnpj = $this->formatarDados('cnpj', $row->cpf_cnpj);

            } else {
                $dado->cpf_cnpj = $this->formatarDados('cpf', $row->cpf_cnpj);
            }

            $retorno[] = $row;
        }

        return $retorno;
    }


    /**
     * Abre a transação
     */
    public function begin(){
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
    }

    /**
     * Formatar dados (CPF||CNPJ)
     *
     * @param string $tipo
     * @param string $valor
     *
     * @return string $valor
     */
    public function formatarDados($tipo, $valor) {

        if ($tipo == "cpf" && $valor != "") {
            $valor = str_pad($valor, 11, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 3) . "." . substr($valor, 3, 3) . "." . substr($valor, 6, 3) . "-" . substr($valor, 9, 2);
        }

        if ($tipo == "cnpj" && $valor != "") {
            $valor = str_pad($valor, 14, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 2) . "." . substr($valor, 2, 3) . "." . substr($valor, 5, 3) . "/" . substr($valor, 8, 4) . "-" . substr($valor, 12, 2);
        }
    }

}
?>
