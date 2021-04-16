<?php

require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';

/**
 * Classe que realiza requisições ao banco de dados, para busca de informações da Action VerificaRegraTrc
 *
 * @author ricardo.bonfim
 */
class VerificaRegraTrcDAO extends CronDAO {

    /**
     * Busca Ordens de Servico que pertencem as seguintes condições:
     * - Tipo: Retirada;
     * - Status: Concluído;
     * - Item: Equipamento;
     * - Motivo: TROCA DE VEÍCULO (RETIRADA) e RETENCAO TROCA DE VEICULO;
     * - Sem Data de exclusão do item da OS;
     * E que não façam parte de um contrato, que contenha uma ordem de serviço com as condições abaixo:
     * - Tipo: Reinstalação;
     * - Status: Concluído;
     * - Item: Equipamento;
     * - Motivo: Troca de Veículo;
     *
     * @return array
     */
    public function buscarOrdensServicoValidas() {

        $query = "
            DROP TABLE IF EXISTS ordens_eliminadas;

            CREATE TEMP TABLE ordens_eliminadas AS
                SELECT
                    ordconnumero
                FROM
                (
                    SELECT
                        ordconnumero
                    FROM
                        ordem_servico
                        INNER JOIN ordem_servico_item ON ositordoid = ordoid
                        INNER JOIN os_tipo_item ON (otioid = ositotioid AND otitipo = 'E')
                        INNER JOIN os_tipo ON (ostoid = otiostoid AND ostoid = 2)
                        INNER JOIN ordem_servico_status ON (ossoid = ordstatus AND ossoid = 3)
                )  AS foo;

            SELECT DISTINCT
                ordoid
                ,ordclioid
                ,ordem_servico.ordconnumero
                ,tpcdescricao as tipo_contrato
                ,diasaposconclusao
            FROM
                ordem_servico
                INNER JOIN ordem_servico_item ON (ositordoid = ordoid AND ositexclusao IS NULL)
                INNER JOIN os_tipo_item ON (otioid = ositotioid  AND otitipo = 'E' AND otioid IN (316,11,946,3,10))
                INNER JOIN contrato ON connumero = ordconnumero AND condt_exclusao IS NULL
                INNER JOIN tipo_contrato ON tpcoid = conno_tipo
                LEFT JOIN ordens_eliminadas on (ordens_eliminadas.ordconnumero = ordem_servico.ordconnumero)
                INNER JOIN (
                    SELECT
                        DISTINCT orsordoid,
                        FIRST_VALUE (ABS ( orsdt_situacao::date  - DATE( NOW() ) ) ) OVER ( PARTITION BY orsordoid ORDER BY orsdt_situacao DESC ) as diasaposconclusao
                    FROM
                        ordem_situacao
                ) AS hist ON orsordoid = ordoid AND diasaposconclusao IN (15,30,40,50,60)
            WHERE
                ordens_eliminadas.ordconnumero IS NULL
            AND
                ordstatus = 3 -- Status concluído
            AND
                otiostoid = 3 --Tipo retirada
            ";

        $result = $this->query($query);

        $ordensServicoValidas = $this->fetchObjects($result);

        return $ordensServicoValidas;
    }

    /**
     * Busca os dados do tipo do contrato
     *
     * @param int $idContrato Id do contrato a buscar o tipo
     *
     * @return CronVO
     */
    public function buscarTipoContrato($idContrato) {

        $query = "
            SELECT
                tipo_contrato.tpcdescricao
            FROM
                contrato
                INNER JOIN tipo_contrato ON tpcoid = conno_tipo
            WHERE
                connumero = " . intval($idContrato);

        $result = $this->query($query);

        $tipoContrato = $this->fetchObject($result);

        return $tipoContrato;
    }

    /**
     * Metodo para buscar os telefones cadastrados para a ordem de servico
     * 
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param obj $ordemServico
     * @return array telefones
     */
    public function buscarTelefones($ordemServico) {

        $query = "	SELECT
                		oscccelular
                	FROM
             			ordem_servico_celular_contato
                	WHERE
                		osccordoid = " . intval($ordemServico->ordoid);

        $rs = pg_query($this->conn, $query);

        $telefones = array();

        if (pg_num_rows($rs) > 0) {
            while ($row = pg_fetch_object($rs)) {
                if($row->oscccelular != NULL) {
                    $telefone = preg_replace("/[^0-9]/", "", $row->oscccelular);
                    array_push($telefones, $telefone);
                }
            }
        }
        
        return $telefones;
    }

    /**
     * Metodo para buscar os emails cadastrados do cliente referente a ordem de servico
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param obj $ordemServico
     * @return array emails
     */
    public function buscarEmails($ordemServico) {

        $emails = array();

        if (!empty($ordemServico->ordclioid)) {

            $query = "
                SELECT
                    clientes.cliemail,
                    clientes.cliemail_nfe,
                    ordem_servico_email_contato.osecemail
                FROM
                    ordem_servico
                    LEFT JOIN clientes ON ordclioid = clioid
                    LEFT JOIN ordem_servico_email_contato ON osecordoid = ordoid
                WHERE
                    clioid = " . intval($ordemServico->ordclioid) . " AND
                    ordoid = " . intval($ordemServico->ordoid);

            $rs = pg_query($this->conn, $query);

            if (pg_num_rows($rs) > 0) {
                while ($row = pg_fetch_object($rs)) {
                    if($row->cliemail != NULL) {
                        array_push($emails, $row->cliemail);
                    }
                    if($row->cliemail_nfe != NULL) {
                        array_push($emails, $row->cliemail_nfe);
                    }
                    if($row->osecemail != NULL) {
                        array_push($emails, $row->osecemail);
                    }
                }
            }
        }
        
         if($_SESSION['servidor_teste']) {
            return array('ricardo.mota@meta.com.br');
        }

        return $emails;
    }

    /**
     * Busca o título do layout conforme a descrição fornecida
     *
     * @param string $tituloLayout descrição do título do layout
     * 
     * @return object
     */
    public function buscarIdTituloLayout($tituloLayout) {

        $query = "
            SELECT
                seetoid
            FROM
                servico_envio_email_titulo
            WHERE
                seetdescricao ilike '" . pg_escape_string($tituloLayout) . "'";


        $result = $this->query($query);

        $tituloLayout = $this->fetchObject($result);

        return $tituloLayout->seetoid;
    }

    /**
     * Busca id da funcionalidade do layout conforme descrição da funcionalidade fornecida
     * 
     * @param string $funcionalidade Descrição da funcionalidade
     * 
     * @return object
     */
    public function buscarIdFuncionalidadeLayout($funcionalidade) {

        $query = "
            SELECT
                *
            FROM
                servico_envio_email_funcionalidade
            WHERE
                seefdescricao ilike '" . pg_escape_string($funcionalidade) . "'";


        $result = $this->query($query);

        $tituloFuncionalidade = $this->fetchObject($result);

        return $tituloFuncionalidade->seefoid;
    }

    /**
     * Busca o template conforme parâmetros fornecidos
     *
     * @param int $idTituloLayout Id do título do layout
     * @param int $idFuncionalidadeLayout Id da funcionalidade do layout
     * @param string $tipoTemplate Tipo do template: 'sms' ou 'email'
     * 
     * @return object
     */
    public function buscarDadosTemplate($idTituloLayout, $idFuncionalidadeLayout, $tipoTemplate) {

        if (strtoupper($tipoTemplate) === "SMS") {
            $tipoEnvio = 'S';
        } else if (strtoupper($tipoTemplate) === "EMAIL") {
            $tipoEnvio = 'E';
        }

        $query = "
            SELECT
                *
            FROM
                servico_envio_email
            WHERE
                seedt_exclusao IS NULL AND
                seeseetoid = " . intval($idTituloLayout) . " AND
                seeseefoid = " . intval($idFuncionalidadeLayout) . " AND
                seetipo = '" . $tipoEnvio . "'";

        $result = $this->query($query);

        $template = $this->fetchObject($result);

        return $template;
    }

    /**
     * Salva histórico do envio da SMS
     *
     * @param CronVO $ordemServico Dados da Ordem de servico
     * @param CronVO $template     Template utilizado para o envio da SMS
     * @param char   $statusEnvio  Status do envio 'S' para Sucesso e 'I' para Insucesso
     *
     * @return int Quantidade de registros afetados
     */
    public function salvarHistoricoSmsEnvio($ordemServico, $template, $statusEnvio) {

        $sql = "
            INSERT INTO historico_sms_envio (
                hseconnumero,
                hseordoid,
                hsedata,
                hseusuoid_cadastro,
                hsetipo,
                hsetexto,
                " . (isset($template->seeoid) ? "hseseeoid, " : '' ) .  "
                hsestatus)
            VALUES (
                " . $ordemServico->ordconnumero . ",
                " . $ordemServico->ordoid . ",
                NOW(),
                " . $this->buscarCodigoUsuarioCron() . ",
                'TR',
                '" . $template->seecorpo . "',
                " . (isset($template->seeoid) ? ($template->seeoid . ',') : '' ) .  "
                '" . $statusEnvio . "')";

        $result = $this->query($sql);

        return pg_affected_rows($result);
    }

}
