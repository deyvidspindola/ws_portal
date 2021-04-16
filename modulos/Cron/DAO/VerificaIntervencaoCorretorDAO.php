<?php

/**
 * Classe padrão para DAO
 *
 * @package Financas
 * @author  robson.silva <robson.silva@meta.com.br>
 */
class VerificarIntervencaoCorretorDAO {

    /**
     * Conexão com o banco de dados.
     *
     * @var resource
     */
    private $conn;

    public function buscarEmailSeguradoraBradesco() {

         $sql = "
            SELECT
               tpcenvia_email_intervencao,
               tpccopia_email_intervencao
            FROM
                tipo_contrato
            WHERE
                tpcoid = 39";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Houve um erro ao buscar o e-mail da seguradora Bradesco.');
        }

        if (pg_num_rows($rs)) {
            return pg_fetch_object($rs);
        }

        return false;
    }


    public function buscarEmailSucursal($proposta) {
        $email = '';

        if (empty($proposta)) {
            return '';
        }

        if (intval($proposta) == 0) {
            return '';
        }

        $sql = "
            SELECT
                tpsemail
            FROM
                proposta_seguradora
            INNER JOIN 
                tipo_contrato_sucursal ON tpstpcoid=tpstpcoid AND tpssucursal=prpscod_unid_emis
            WHERE
                prpsproposta = ". $proposta ."
            AND
                tpstpcoid = 39 -- BRADESCO
            LIMIT
                1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Houve um erro ao buscar o e-mail da sucursal.');
        }

        if (pg_num_rows($rs)) {
            $registro = pg_fetch_object($rs);

            $email = $registro->tpsemail;
        }

        return $email;
    }

    public function buscarOS () {

        $sql = "SELECT
                    veioid,
                    connumero,
                    ordoid,                    
                    CASE
                        WHEN
                            (
                                SELECT 
                                    pcoremail_corretor
                                FROM 
                                    proposta_corretora
                                INNER JOIN
                                    tipo_contrato ON tpcoid = pcortpcoid
                                WHERE 
                                    pcorplaca = veiplaca
                                AND 
                                    pcorchassi = veichassi
                                AND 
                                    pcorexclusao IS NULL
                                LIMIT 1
                             ) <> ''
                        THEN (
                                SELECT 
                                    pcoremail_corretor
                                FROM 
                                    proposta_corretora
                                INNER JOIN
                                    tipo_contrato ON tpcoid = pcortpcoid
                                WHERE 
                                    pcorplaca = veiplaca
                                AND 
                                    pcorchassi = veichassi
                                AND 
                                    pcorexclusao IS NULL
                                LIMIT 1
                             )
                        ELSE
                            (
                                SELECT 
                                    prpsemail_corretor 
                                FROM 
                                    proposta_seguradora 
                                INNER JOIN
                                    tipo_contrato ON tpcoid = prpstpcoid
                                WHERE 
                                    prpsplaca = veiplaca 
                                AND 
                                    prpschassi = veichassi 
                                AND 
                                    prpsemail_corretor <> '' 
                                AND
                                    prpsdt_exclusao IS NULL
                                LIMIT 1
                            )
                    END AS email_corretor,
                    tpccopia_email_intervencao,
                    tpcenvia_email_intervencao,
                    tpcdescricao,
                    tpcoid AS tipo_contrato_id,

                    clinome AS segurado,
                    mcamarca ||' / '||  mlomodelo AS veiculo,
                    veiplaca AS placa,
                    veichassi AS chassi,
                    veino_proposta AS proposta,
                    veicod_unid_emis AS sucursal,
                    veiapolice AS apolice,
                    veino_item AS item_apolice,
                    (SELECT mhcdescricao FROM ordem_situacao, motivo_hist_corretora WHERE orsordoid=ordoid AND orsstatus=mhcoid AND mhchabilita_corretora=TRUE ORDER BY mhcoid DESC LIMIT 1) AS motivo

                FROM
                    ordem_servico, -- 0 ordoid
                    ordem_servico_item, -- 1 ositordoid
                    os_tipo_item, -- 2 otioid
                    os_tipo, -- 3 ostoid
                    contrato, -- 4 connumero
                    tipo_contrato, -- 5 tpcoid
                    veiculo, -- 6 veioid
                    modelo, -- 7 mlooid
                    marca, -- 8 mcaoid
                    clientes -- 9 clioid

                WHERE
                    ositordoid = ordoid --1
                    AND ositotioid = otioid --2
                    AND otiostoid = ostoid --3
                    AND ordconnumero=connumero --4
                    AND conno_tipo=tpcoid --5
                    AND ordveioid=veioid --6
                    AND mlooid=veimlooid --7
                    AND mcaoid=mlomcaoid --8
                    AND clioid=conclioid --9

                    --Parâmetros Seguradora com campo 'Recebe Email de Intervenção' marcado;
                        AND tpcenvia_email_intervencao = 't' -- recebe e-mail intervençaõ

                    --Status OS: Autorizado, código 4;
                        AND ordstatus= 4 -- (AUTORIZADA) -- status OS autorizada

                    --Tipo OS: Retirada, código 3;
                        AND ostoid = 3 -- TIPO: RETIRADA

                    --Motivo: Diferente de TROCA DE VEÍCULO (RETIRADA), ou seja, diferente dos códigos 316, 11 e 946
                        AND otioid NOT IN (316, 11, 946)

                    --Tipo de contrato: Ex-[XXXXXXXXXXXX], ou seja, buscar pelos tipos de contrato que iniciarem com ex
                        AND tpcdescricao ILIKE 'Ex-%'

                    --Sem Data de exclusão do item da OS
                        AND ositexclusao IS NULL

                    --Com mais de 10 dias pendente agendamento
                    AND ordoid NOT IN (SELECT osaordoid FROM ordem_servico_agenda
                                WHERE osaordoid=ordoid
                                AND osaexclusao IS NULL
                                AND osadata >= NOW()
                                AND osacadastro <= (current_date - integer '10')-- SE inclusa a mais de 10 dias
                    )

                    --Se não houver intervenção registrada nos últimos 7 dias
                    AND ordoid NOT IN (SELECT orsordoid FROM ordem_situacao
                                WHERE orsordoid=ordoid AND orsstatus=66
                                AND orsdt_situacao>=(current_date - integer '7'))

                    -- SEM AGENDAMENTO FUTURO
                    AND ordoid NOT IN(
                        SELECT
                            osaordoid
                        FROM
                            ordem_servico_agenda
                        WHERE
                            osaordoid = ordoid
                            AND osaexclusao IS NULL
                            AND osadata >= orddt_ordem
                    )

                    -- CASO tenham sido feitas pelo menos 3 tentativas de contat
                    AND (
                        SELECT
                                COUNT(orsordoid) as total
                            FROM
                                ordem_situacao
                            WHERE
                                orsordoid = ordoid
                            AND
                                orsstatus IN (101, 16, 70, 10, 105, 96, 97, 98)
                            GROUP BY orsordoid)>=3";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Houve um erro ao buscar as OS.');
        }

        $retorno = array();

        while($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;


    }

    public function registrarHistoricoOs(stdClass $parametros, $ordoid) {

        $sql = "INSERT INTO
                    ordem_situacao
                    (orsordoid, orsstatus, orssituacao, orsusuoid, orsdt_situacao)
                VALUES
                    ($ordoid, " . $parametros->orsstatus . ",'". $parametros->orssituacao . "'," . $parametros->orsusuoid .",NOW())";


        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Houve um erro ao inserir o histórico da OS.');
        }

        if (pg_affected_rows($rs) > 0) {
            return true;
        } else {
            return false;
        }

    }



    /**
     * Método construtor.
     *
     * @param resource $conn conexão
     */
    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }

    /**
     * Abre a transação
     *
     * @return void
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     *
     * @return void
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     *
     * @return void
     *
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

?>
