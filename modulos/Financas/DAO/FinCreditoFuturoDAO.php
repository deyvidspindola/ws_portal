<?php

/**
 * Classe padrão para DAO
 *
 * @package Financas
 * @author  robson.silva <robson.silva@meta.com.br>
 */
class FinCreditoFuturoDAO {

    /**
     * Conexão com o banco de dados.
     *
     * @var resource
     */
    public $conn;

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

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
     * Método para realizar a pesquisa de varios registros
     *
     * @param stdClass $parametros Filtros da pesquisa
     *
     * @throws ErrorException
     * @return array
     */
    public function pesquisar(stdClass $parametros) {
        $retorno = array();

        $sql = "SELECT DISTINCT ON (credito_futuro.cfooid)
                credito_futuro.cfooid,
                credito_futuro.cfoclioid,
                credito_futuro.cfousuoid_inclusao,
                credito_futuro.cfousuoid_exclusao,
                credito_futuro.cfousuoid_encerramento,
                credito_futuro.cfousuoid_avaliador,
                TO_CHAR(credito_futuro.cfodt_inclusao,'DD/MM/YYYY') AS cfodt_inclusao,
                credito_futuro.cfodt_exclusao,
                credito_futuro.cfodt_encerramento,
                credito_futuro.cfodt_avaliacao,
                credito_futuro.cfoconnum_indicado,
                credito_futuro.cfocfcpoid,
                credito_futuro.cfocfmcoid,
                credito_futuro.cfoancoid,
                credito_futuro.cfoobroid_desconto,
                credito_futuro.cfostatus,
                credito_futuro.cfotipo_desconto,
                credito_futuro.cfovalor,
                credito_futuro.cfoforma_aplicacao,
                credito_futuro.cfoforma_inclusao,
                credito_futuro.cfosaldo,
                credito_futuro.cfoobservacao,
                credito_futuro.cfoaplicar_desconto,
                clientes.clinome,
                clientes.clitipo,
                CASE
                    WHEN clientes.clitipo = 'F' THEN
                        clientes.clino_cpf
                    ELSE
                        clientes.clino_cgc
                END AS clicpfcnpj,
                obrigacao_financeira.obrobrigacao,
                credito_futuro_motivo_credito.cfmcdescricao,
                usuarios.nm_usuario,
                cfmoid AS credito_futuro_movimentacao_ativa,
                (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) AS parcelas_ativas
            FROM
                credito_futuro
                    INNER JOIN
                        clientes ON credito_futuro.cfoclioid = clientes.clioid
                    INNER JOIN
                        obrigacao_financeira ON credito_futuro.cfoobroid_desconto = obrigacao_financeira.obroid
                    INNER JOIN
                        credito_futuro_motivo_credito ON credito_futuro.cfocfmcoid = credito_futuro_motivo_credito.cfmcoid
                    INNER JOIN
                        usuarios ON credito_futuro.cfousuoid_inclusao = usuarios.cd_usuario
                    LEFT JOIN
                        credito_futuro_movimento ON (cfmcfooid = cfooid AND cfmdt_exclusao IS NULL)
            WHERE
                1 = 1
        ";

        if (!empty($parametros->cfooid)) {
            $sql.= "
                AND
                    credito_futuro.cfooid = " . intval($parametros->cfooid) . "
            ";
        }

        if (!empty($parametros->cfoclioid)) {
            $sql.= "
                AND
                    credito_futuro.cfoclioid = " . intval($parametros->cfoclioid) . "
            ";
        }

        if (!empty($parametros->periodo_inclusao_ini) && !empty($parametros->periodo_inclusao_fim)) {
            $sql.= "
                AND
                    credito_futuro.cfodt_inclusao
                        BETWEEN '" . $parametros->periodo_inclusao_ini . " 00:00:01'
                            AND '" . $parametros->periodo_inclusao_fim . " 23:59:59'
            ";
        }

        if (!empty($parametros->cfoconnum_indicado)) {
            $sql.= "
                AND
                    credito_futuro.cfoconnum_indicado = " . intval($parametros->cfoconnum_indicado) . "
            ";
        }

        if (!empty($parametros->cfoancoid)) {
            $sql.= "
                AND
                    credito_futuro.cfoancoid = " . intval($parametros->cfoancoid) . "
            ";
        }

        if (!empty($parametros->cfocfcpoid)) {
            $sql.= "
                AND
                    credito_futuro.cfocfcpoid = " . intval($parametros->cfocfcpoid) . "
            ";
        }

        if (!empty($parametros->cfoobroid_desconto)) {
            $sql.= "
                AND
                    credito_futuro.cfoobroid_desconto = " . intval($parametros->cfoobroid_desconto) . "
            ";
        }

        if (!empty($parametros->cfoforma_inclusao) && intval($parametros->cfoforma_inclusao) <= 2) {
            $sql.= "
                AND
                    credito_futuro.cfoforma_inclusao = " . intval($parametros->cfoforma_inclusao) . "
            ";
        }

        if (!empty($parametros->cfostatus)) {
            $sql.= "
                AND
                    credito_futuro.cfostatus = " . intval($parametros->cfostatus) . "
            ";
        }

        if (is_array($parametros->registros_apenas) && count($parametros->registros_apenas)) {

            //ser for apenas por exclusão
            if (in_array('1', $parametros->registros_apenas) && !in_array('2', $parametros->registros_apenas)) {

                $sql.= "
                        AND
                            credito_futuro.cfodt_exclusao IS NOT NULL
                    ";
            }

            //ser for apenas por encerramento
            if (!in_array('1', $parametros->registros_apenas) && in_array('2', $parametros->registros_apenas)) {

                $sql.= "
                        AND
                            credito_futuro.cfodt_encerramento IS NOT NULL
                    ";
            }

            //ser for por encerramento ou exclusao
            if (in_array('1', $parametros->registros_apenas) && in_array('2', $parametros->registros_apenas)) {

                $sql.= "
                        AND
                            ( credito_futuro.cfodt_exclusao IS NOT NULL OR credito_futuro.cfodt_encerramento IS NOT NULL)
                    ";
            }
        }

        if (!empty($parametros->cfousuoid_inclusao)) {
            $sql.= "
                AND
                    credito_futuro.cfousuoid_inclusao = " . intval($parametros->cfousuoid_inclusao) . "
            ";
        }

        if (!empty($parametros->cfotipo_desconto) && intval($parametros->cfotipo_desconto) <= 2) {
            $sql.= "
                AND
                    credito_futuro.cfotipo_desconto = " . intval($parametros->cfotipo_desconto) . "
            ";
        }

        if (!empty($parametros->cfopercentual_de) && !empty($parametros->cfopercentual_ate)) {
            $parametros->cfopercentualde = str_replace(',', '.', $parametros->cfopercentual_de);
            $parametros->cfopercentualate = str_replace(',', '.', $parametros->cfopercentual_ate);

            $sql.= "
                AND
                    credito_futuro.cfovalor
                        BETWEEN " . floatval($parametros->cfopercentualde) . "
                            AND " . floatval($parametros->cfopercentualate) . "
            ";
        }

        if (!empty($parametros->cfovalor_de) && !empty($parametros->cfovalor_ate)) {
            $parametros->cfovalorde = str_replace('R$ ', '', $parametros->cfovalor_de);
            $parametros->cfovalorde = str_replace('.', '', $parametros->cfovalorde);
            $parametros->cfovalorde = str_replace(',', '.', $parametros->cfovalorde);

            $parametros->cfovalorate = str_replace('R$ ', '', $parametros->cfovalor_ate);
            $parametros->cfovalorate = str_replace('.', '', $parametros->cfovalorate);
            $parametros->cfovalorate = str_replace(',', '.', $parametros->cfovalorate);

            $sql.= "
                AND
                    credito_futuro.cfovalor
                        BETWEEN " . floatval($parametros->cfovalorde) . "
                            AND " . floatval($parametros->cfovalorate) . "
            ";
        }

        if (!empty($parametros->cfosaldo)) {
            switch ($parametros->cfosaldo) {
                case 1:
                    $sql.= "
                        AND
                            (credito_futuro.cfosaldo > 0 OR (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) > 0 )
                    ";
                    break;
                case 2:
                    $sql.= "
                        AND
                            credito_futuro.cfosaldo = 0
                    ";
                    break;
            }
        }

        if (!empty($parametros->cfoforma_aplicacao) && intval($parametros->cfoforma_aplicacao) <= 2) {
            $sql.= "
                AND
                    credito_futuro.cfoforma_aplicacao = " . intval($parametros->cfoforma_aplicacao) . "
            ";
        }

        $usuarioMotivoCreditoRestrito = isset($_SESSION['funcao']['credito_futuro_motivo_credito_restrito']) && $_SESSION['funcao']['credito_futuro_motivo_credito_restrito'] ? true : false;

        if (!empty($parametros->cfocfmcoid)) {

            if (!in_array('-1', $parametros->cfocfmcoid)) {

                $filtro_motivo_credito = implode(', ', $parametros->cfocfmcoid);

                $sql.= " AND
                            credito_futuro.cfocfmcoid IN (" . $filtro_motivo_credito . ")";

            } elseif ( in_array('-1', $parametros->cfocfmcoid) && $usuarioMotivoCreditoRestrito) {


                $sql .= "AND
                             credito_futuro_motivo_credito.cfmctipo = 3 ";

            }

        } elseif ($usuarioMotivoCreditoRestrito) {

            $sql .= "AND
                             credito_futuro_motivo_credito.cfmctipo = 3 ";

        }


        $sql .= " ORDER BY
                            cfooid";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {

            if ($row->clitipo == "J") {
                $row->clicpfcnpj = $this->formatarDados('cnpj', $row->clicpfcnpj);
            } else if ($row->clitipo == "F") {
                $row->clicpfcnpj = $this->formatarDados('cpf', $row->clicpfcnpj);
            }

            $row->cfotipo_desconto_id =  $row->cfotipo_desconto;

            if ($row->cfotipo_desconto == '1') {
                $row->cfotipo_desconto = "Percentual";

                if (trim($row->cfovalor) != '') {
                    $row->valorDesconto = number_format($row->cfovalor, 2, ',', '.') . ' %';
                }

                if (trim($row->cfosaldo) != '') {
                    $row->cfosaldo = number_format($row->cfosaldo, 2, ',', '.') . ' %';
                }

            } elseif ($row->cfotipo_desconto == '2') {
                $row->cfotipo_desconto = "Valor";

                if (trim($row->cfovalor) != '') {
                    $row->valorDesconto = 'R$ ' . number_format($row->cfovalor, 2, ',', '.');
                }

                if (trim($row->cfosaldo) != '') {
                    $row->cfosaldo = 'R$ ' . number_format($row->cfosaldo, 2, ',', '.');
                }

            } else {
                $row->cfotipo_desconto = "Todos";
                $row->valorDesconto = '';
            }



            if ($row->cfoforma_aplicacao == '1') {
                $row->cfoforma_aplicacao = "Integral";
            } elseif ($row->cfoforma_aplicacao == '2') {
                $row->cfoforma_aplicacao = "Parcelas";
            } else {
                $row->cfoforma_aplicacao = "Todos";
            }

            $row->cfoforma_inclusao_id = $row->cfoforma_inclusao;

            if ($row->cfoforma_inclusao == "1") {
                $row->cfoforma_inclusao = "Manual";
            } elseif ($row->cfoforma_inclusao == "2") {
                $row->cfoforma_inclusao = "Automático";
            } else {
                $row->cfoforma_inclusao = "Todos";
            }


            //bloqueio0 do botão excluir  na listagemn
            //O crédito futuro não possuir movimentações ativas registradas, forma de inclusão Manual e deve ter o status de Aprovado ou Pendente.
            $row->bloqueiaExcluir = true;


            if (is_null($row->credito_futuro_movimentacao_ativa) && $row->cfoforma_inclusao_id == "1" && in_array($row->cfostatus, array('1', '3')) && is_null($row->cfodt_exclusao) && is_null($row->cfodt_encerramento)) {
                $row->bloqueiaExcluir = false;
            }


            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa de apenas um registro.
     *
     * @param int $id Identificador único do registro
     *
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarPorID($id) {

        $retorno = new stdClass();

        $sql = "SELECT
					cfooid,
					cfoclioid,
					cfousuoid_inclusao,
					cfousuoid_exclusao,
					cfousuoid_encerramento,
					cfousuoid_avaliador,
					cfodt_inclusao,
					cfodt_exclusao,
					cfodt_encerramento,
					cfodt_avaliacao,
					cfoconnum_indicado,
					cfocfcpoid,
					cfocfmcoid,
                    cfmcdescricao AS motivo_credito_descricao,
					cfoancoid,
					cfoobroid_desconto,
					cfostatus,
					cfotipo_desconto,
					cfovalor,
					cfoforma_aplicacao,
					cfoforma_inclusao,
					cfosaldo,
					cfoobservacao,
					cfoaplicar_desconto,
                    clinome,

                    CASE WHEN clitipo = 'J' THEN
                        clino_cgc
                    ELSE
                        clino_cpf
                    END AS doc,
                    clitipo AS tipo,

                    CASE WHEN cfoforma_inclusao = 1 THEN 'Manual'
                         WHEN cfoforma_inclusao = 2 THEN 'Automático'
                    END AS forma_inclusao,

                    --1 - Aprovado; 2 - Concluído; 3 - Pendente; 4 - Reprovado.
                    cfostatus  AS status,

                    CASE WHEN cfostatus = 1 THEN 'Aprovado'
                         WHEN cfostatus = 2 THEN 'Concluído'
                         WHEN cfostatus = 3 THEN 'Pendente'
                         WHEN cfostatus = 4 THEN 'Reprovado'
                    END AS status_descricao,

                    -- valor total de notas contestadas
                    ancvlr_total AS valor_total_contestadas,

                    cfmctipo AS tipo_motivo_credito,

                    cfmoid AS credito_futuro_movimentacao_ativa,

                    --Data de avaliação (Para condicional)
                    CASE
                        WHEN
                            cfodt_avaliacao IS NULL
                        AND
                            cfostatus = 3
                        AND
                            cfodt_exclusao IS NULL
                        AND
                            cfodt_encerramento IS NULL
                        THEN '1'
                        ELSE '0'
                        END AS pode_avalidar,

                    --(SELECT COUNT(cfpoid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = 't' ) AS cfoqtde_parcelas
                    (SELECT COUNT(cfpoid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid ) AS cfoqtde_parcelas,

                    cftpdescricao AS campanha
				FROM
					credito_futuro
                INNER JOIN
                    clientes ON (cfoclioid = clioid)
                INNER JOIN
                    credito_futuro_motivo_credito ON (cfmcoid = cfocfmcoid)
                LEFT JOIN
                    analise_contas ON (cfoancoid = ancoid)
                LEFT JOIN
                    credito_futuro_movimento ON (cfmcfooid = cfooid AND cfmdt_exclusao IS NULL)
                LEFT JOIN
                    credito_futuro_campanha_promocional ON (cfcpoid = cfocfcpoid)
                LEFT JOIN
                    credito_futuro_tipo_campanha ON (cfcpcftpoid = cftpoid)
				WHERE
					cfooid =" . intval($id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_assoc($rs);

            //formata valor para view
            $retorno['valor']    = trim($retorno['cfovalor']) != '' ? $retorno['cfovalor'] : '';
            $retorno['cfovalor'] = trim($retorno['cfovalor']) != '' ? number_format($retorno['cfovalor'], 2, ',', '.') : '';
            $retorno['cfosaldo'] = trim($retorno['cfosaldo']) != '' ? number_format($retorno['cfosaldo'], 2, ',', '.') : '';
            $retorno['valor_total_contestadas'] = trim($retorno['valor_total_contestadas']) != '' ? 'R$ ' . number_format($retorno['valor_total_contestadas'], 2, ',', '.') : '';

            $retorno['saldo_literal'] = $this->buscarParcelasCreditoFuturo($retorno['cfooid'],$retorno['cfoforma_aplicacao'],$retorno['cfotipo_desconto']);

            //formata o documente conforme o tipo de cliente PJ/PF
            if ($retorno['tipo'] == 'J') {
                $retorno['doc'] = $this->formatarDados('cnpj', $retorno['doc']);
            } else if ($retorno['tipo'] == 'F') {
                $retorno['doc'] = $this->formatarDados('cpf', $retorno['doc']);
            }

            $retorno['bloqueia_campos_edicao'] = false;
            //verifico se exist movimentação ativa desse credito futuro ou se ja foi avaliado

            if (!is_null($retorno['credito_futuro_movimentacao_ativa'])
                    || !is_null($retorno['cfodt_avaliacao'])
                    || !is_null($retorno['cfodt_exclusao'])
                    || !is_null($retorno['cfodt_encerramento'])
                    || ($retorno['cfoforma_inclusao'] == '2')) {

                $retorno['bloqueia_campos_edicao'] = true;
            }
        }

        return $retorno;
    }


    public function buscarHistoricoCreditoFuturo($creditoFuturoId) {

        $sql = "SELECT

                    cfhoid,

                    --operação
                    CASE WHEN cfhoperacao = 1 THEN 'Inclusão de crédito'
                         WHEN cfhoperacao = 2 THEN 'Alteração do crédito'
                         WHEN cfhoperacao = 3 THEN 'Exclusão do crédito'
                         WHEN cfhoperacao = 4 THEN 'Encerramento do crédito'
                         WHEN cfhoperacao = 5 THEN 'Desconto concedido'
                         WHEN cfhoperacao = 6 THEN 'Desconto cancelado'
                         WHEN cfhoperacao = 7 THEN 'Credito Aprovado'
                         WHEN cfhoperacao = 8 THEN 'Credito Reprovado'
                         WHEN cfhoperacao = 9 THEN 'Desconto descartado'
                         WHEN cfhoperacao = 10 THEN 'Cancelamento de NF'
                    END AS operacao,

                    TO_CHAR(cfhdt_inclusao,'DD/MM/YYYY HH24:MI') AS data_hora,

                    nm_usuario AS usuario,

                    CASE WHEN cfhorigem = 1 THEN 'Credito Futuro'
                         WHEN cfhorigem = 2 THEN 'Faturamento'
                         WHEN cfhorigem = 3 THEN 'Faturamento Manual'
                         WHEN cfhorigem = 4 THEN 'Forma de cobrança cliente'
                         WHEN cfhorigem = 5 THEN 'Encerramento de ordem de serviço'
                         WHEN cfhorigem = 6 THEN 'Cancelamento de NF'
                    END AS origem,

                    cfhobservacao AS observacao,
                    cfhjustificativa AS justificativa

                FROM
                    credito_futuro_historico
                LEFT JOIN
                    usuarios ON (cfhusuoid = cd_usuario)
                WHERE
                    cfhcfooid = " . intval($creditoFuturoId) . "
                ORDER BY
                    cfhoid DESC, 
                    cfhdt_inclusao DESC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = array();

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }


        return $retorno;
    }

    public function buscarHistoricoPorId($creditoFuturoHistoricoId) {

        $sql = "SELECT

                    --credito futuro
                    cfhcfooid,

                    --data de criação
                    TO_CHAR(cfhdt_inclusao,'DD/MM/YYYY HH24:MI') AS data_hora,

                    --usuario
                    nm_usuario AS usuario,

                    --origem
                    CASE WHEN cfhorigem = 1 THEN 'Crédito Futuro'
                         WHEN cfhorigem = 2 THEN 'Faturamento'
                         WHEN cfhorigem = 3 THEN 'Faturamento Manual'
                         WHEN cfhorigem = 4 THEN 'Forma de cobrança cliente'
                         WHEN cfhorigem = 5 THEN 'Encerramento de ordem de serviço'
                         WHEN cfhorigem = 6 THEN 'Cancelamento de NF'
                    END AS origem,
                    --operação
                    CASE WHEN cfhoperacao = 1 THEN 'Inclusão de crédito'
                         WHEN cfhoperacao = 2 THEN 'Alteração do crédito'
                         WHEN cfhoperacao = 3 THEN 'Exclusão do crédito'
                         WHEN cfhoperacao = 4 THEN 'Encerramento do crédito'
                         WHEN cfhoperacao = 5 THEN 'Desconto concedido'
                         WHEN cfhoperacao = 6 THEN 'Desconto cancelado'
                         WHEN cfhoperacao = 7 THEN 'Credito Aprovado'
                         WHEN cfhoperacao = 8 THEN 'Credito Reprovado'
                         WHEN cfhoperacao = 9 THEN 'Desconto descartado'
                         WHEN cfhoperacao = 10 THEN 'Cancelamento de NF'
                    END AS operacao,

                    --status
                    CASE WHEN cfhstatus = 1 THEN 'Aprovado'
                         WHEN cfhstatus = 2 THEN 'Concluído'
                         WHEN cfhstatus = 3 THEN 'Pendente'
                         WHEN cfhstatus = 4 THEN 'Reprovado'
                    END AS status,

                    --tipo de desconto
                    CASE WHEN cfhtipo_desconto = 1 THEN 'Percentual'
                         WHEN cfhtipo_desconto = 2 THEN 'Valor'
                    END AS tipo_desconto_descricao,

                    cfhtipo_desconto AS tipo_desconto,

                    --forma de aplicação
                    CASE WHEN cfhforma_aplicacao = 1 THEN 'Integral'
                         WHEN cfhforma_aplicacao = 2 THEN 'Parcelado'
                    END AS forma_aplicacao_descricao,

                    --aplicar desconto sobre
                    CASE WHEN cfhaplicar_desconto = 1 THEN 'Monitoramento'
                         WHEN cfhaplicar_desconto = 2 THEN 'Locação'
                    END AS aplicar_desconto_descricao,

                    --obrigacao financeira de desconto
                    obrobrigacao AS obrigacao_fincanceira_desconto_descricao,

                    --quantidade parcelas
                    cfhqtde_parcelas AS qtd_parcelas,

                    --valor
                    cfhvalor AS valor,

                    --saldo
                    cfhsaldo AS saldo,

                    --observacao
                    cfhobservacao AS observacao,

                    --justificativa
                    cfhjustificativa AS justificativa,

                    -- Dados de NF (No lançamento de crédito futuro) --

                    --Numero nota fiscal
                    cfhnf_numero AS nota_fiscal_numero,

                    --Serie nota fiscal
                    cfhnf_serie AS nota_fical_serie,

                    --Data emissão nota fical
                    TO_CHAR(cfhdt_emissao_nf,'DD/MM/YYYY HH24:MI') AS nota_fiscal_data_emissao,

                    --Valor Total nota fiscal
                    cfhvalor_total_nf AS nota_fiscal_valor_total,

                    --Valor total itens nota fical
                    cfhvl_total_itens_nf AS nota_fiscal_valor_total_item,

                    --Valor Desconto aplicado nota fiscal
                    cfhvalor_aplicado_desconto AS nota_fiscal_desconto_aplicado,

                    --Nota fiscal numero da parcela aplicada
                    cfhnum_parcela_aplicada AS nota_fiscal_numero_parcela_aplicada,

                    --tipo do motivo de credito do credito futuro
                    cfmctipo AS tipo_motivo_credito,

                    cfhsaldo_parcelas AS saldo_descricao

                FROM
                    credito_futuro_historico
                LEFT JOIN
                    usuarios ON (cfhusuoid = cd_usuario)
                LEFT JOIN
                    credito_futuro ON (cfooid = cfhcfooid)
                LEFT JOIN
                    credito_futuro_motivo_credito ON (cfocfmcoid = cfmcoid)
                LEFT JOIN
                    obrigacao_financeira ON (cfhobrigacao_desconto = obroid)
                WHERE
                    cfhoid = " . intval($creditoFuturoHistoricoId) ."
                LIMIT 1";


        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {

            $retorno  = pg_fetch_assoc($rs);

            $retorno['nota_fiscal'] = '';

            if (trim($retorno['nota_fiscal_numero']) != '') {
                $retorno['nota_fiscal'] = $retorno['nota_fiscal_numero'] . '/' . $retorno['nota_fical_serie'];
            }



            if (trim($retorno['nota_fiscal_valor_total']) != '') {
                $retorno['nota_fiscal_valor_total'] = number_format($retorno['nota_fiscal_valor_total'], 2, ',', '.');
            }

            if (trim($retorno['nota_fiscal_valor_total_item']) != '') {
                $retorno['nota_fiscal_valor_total_item'] = number_format($retorno['nota_fiscal_valor_total_item'], 2, ',', '.');
            }

            if (trim($retorno['nota_fiscal_desconto_aplicado']) != '') {
                $retorno['nota_fiscal_desconto_aplicado'] = number_format($retorno['nota_fiscal_desconto_aplicado'], 2, ',', '.');
            }

            foreach ($retorno as $key => $value) {

                if (trim($retorno[$key]) == '') {
                    $retorno[$key] = '-';
                } else {
                    $retorno[$key] = utf8_encode($retorno[$key]);
                }
            }

            $saldo = floatval($retorno['saldo']);
            $valor = floatval($retorno['valor']);

            $parcelas_restantes = $saldo / $valor;
            $parcelas_restantes_trucado = explode('.',$parcelas_restantes);

            if (count($parcelas_restantes_trucado) > 1) {
                $parcelas_restantes = $parcelas_restantes_trucado[0];
            }

            $retorno['saldo'] = $retorno['saldo_descricao'];

           return $retorno;


        } else {
            return "";
        }
    }

    /**
     * Busca parcelas pelo id do crédito futuro
     *
     * @param string $creditoFuturoId
     */
    public function buscarParcelasCreditoFuturo($creditoFuturoId, $formaAplicacao, $tipoDesconto) {

        $sql = "";

        if ($tipoDesconto == null || $tipoDesconto == "") {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO, 990);
        }

        if ($tipoDesconto == '1') {

            $sql = "SELECT
                        COUNT(1) AS qtde,
                         A.cfpvalor AS valor
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = " . intval($creditoFuturoId) . "
                        AND A.cfpativo = true
                    GROUP BY
                        valor";
        } else {

            $sql = "SELECT
                    COUNT(1) AS qtde,
                    valor_deferido AS valor
                FROM
                (
                    SELECT
                         A.cfpvalor AS valor,
                         A.cfpvalor - (SELECT COALESCE(SUM(cfmvalor),SUM(cfmvalor), 0) FROM credito_futuro_movimento cfm WHERE cfm.cfmcfpoid = A.cfpoid AND cfm.cfmdt_exclusao IS NULL) AS valor_deferido,
                         A.cfpativo AS ativo
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = " . intval($creditoFuturoId) . "
                        AND A.cfpativo = true

                ) sub
                WHERE
                    valor_deferido > 0
                    AND ativo = true
                GROUP BY
                    valor_deferido,
                    ativo";
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }


        if (pg_num_rows($rs) > 0) {

            while ($row = pg_fetch_object($rs)) {
                $parcelas[] = $row;
            }


            if (count($parcelas) > 1) {
                if (floatval($parcelas[0]->valor) < floatval($parcelas[1]->valor)) {
                    $parcelas = $parcelas[0]->qtde . ' x ' . $this->formatarValorTipoDesconto($tipoDesconto, number_format($parcelas[0]->valor, 2, ',', '.')) . ' e ' . $parcelas[1]->qtde . ' x ' . $this->formatarValorTipoDesconto($tipoDesconto, number_format($parcelas[1]->valor, 2, ',', '.'));
                } else {
                    $parcelas = $parcelas[1]->qtde . ' x ' . $this->formatarValorTipoDesconto($tipoDesconto, number_format($parcelas[1]->valor, 2, ',', '.')) . ' e ' . $parcelas[0]->qtde . ' x ' . $this->formatarValorTipoDesconto($tipoDesconto, number_format($parcelas[0]->valor, 2, ',', '.'));
                }
            } else {

                if ($formaAplicacao == '1') {
                    $parcelas = '1 x ' . $this->formatarValorTipoDesconto($tipoDesconto, number_format($parcelas[0]->valor, 2, ',', '.'));
                } else {
                    $parcelas = $parcelas[0]->qtde . ' x ' . $this->formatarValorTipoDesconto($tipoDesconto, number_format($parcelas[0]->valor, 2, ',', '.'));
                }

            }
        } else {
            $parcelas = '0,00';
        }
        return $parcelas;
    }

    public function formatarValorTipoDesconto($tipoDesconto,$valor) {

        if ($tipoDesconto == '1') {
            return $valor . ' %';
        } else {
            return 'R$ ' . $valor;
        }

    }


    /**
     * Responsável para inserir um registro no banco de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     *
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $dados) {

        $sql = "INSERT INTO
					credito_futuro
					(
					cfoclioid,
					cfousuoid_inclusao,
					cfousuoid_exclusao,
					cfousuoid_encerramento,
					cfousuoid_avaliador,
					cfoconnum_indicado,
					cfocfcpoid,
					cfocfmcoid,
					cfoancoid,
					cfoobroid_desconto,
					cfostatus,
					cfotipo_desconto,
					cfovalor,
					cfoforma_aplicacao,
					cfoforma_inclusao,
					cfosaldo,
					cfoobservacao,
					cfoaplicar_desconto
					)
				VALUES
					(
					" . intval($dados->cfoclioid) . ",
					" . intval($dados->cfousuoid_inclusao) . ",
					" . intval($dados->cfousuoid_exclusao) . ",
					" . intval($dados->cfousuoid_encerramento) . ",
					" . intval($dados->cfousuoid_avaliador) . ",
					" . intval($dados->cfoconnum_indicado) . ",
					" . intval($dados->cfocfcpoid) . ",
					" . intval($dados->cfocfmcoid) . ",
					" . intval($dados->cfoancoid) . ",
					" . intval($dados->cfoobroid_desconto) . ",
					" . intval($dados->cfostatus) . ",
					" . intval($dados->cfotipo_desconto) . ",
					" . intval($dados->cfovalor) . ",
					" . intval($dados->cfoforma_aplicacao) . ",
					" . intval($dados->cfoforma_inclusao) . ",
					" . intval($dados->cfosaldo) . ",
					'" . pg_escape_string($dados->cfoobservacao) . "',
					" . intval($dados->cfoaplicar_desconto) . "
				)";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Responsável por atualizar os registros
     *
     * @param CreditoFuturoVO $dados Dados a serem gravados.
     *
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(CreditoFuturoVO $dados) {

        $sql = "UPDATE
					credito_futuro
				SET
					--cfoclioid = " . intval($dados->cfoclioid) . ",
					--cfousuoid_inclusao = " . intval($dados->cfousuoid_inclusao) . ",
					--cfousuoid_exclusao = " . intval($dados->cfousuoid_exclusao) . ",
					--cfousuoid_encerramento = " . intval($dados->cfousuoid_encerramento) . ",
					--cfousuoid_avaliador = " . intval($dados->cfousuoid_avaliador) . ",
					--cfoconnum_indicado = " . intval($dados->cfoconnum_indicado) . ",
					--cfocfcpoid = " . intval($dados->cfocfcpoid) . ",
					--cfocfmcoid = " . intval($dados->cfocfmcoid) . ",
					--cfoancoid = " . intval($dados->cfoancoid) . ",
					cfoobroid_desconto = " . intval($dados->obrigacaoFinanceiraDesconto) . ",
					cfostatus = " . intval($dados->status) . ",
					cfotipo_desconto = " . intval($dados->tipoDesconto) . ",
					cfovalor = " . $dados->valor . ",
					cfoforma_aplicacao = " . intval($dados->formaAplicacao) . ",
					cfosaldo = " . $dados->saldo . ",
					cfoobservacao = '" . pg_escape_string($dados->observacao) . "',
					cfoaplicar_desconto = " . intval($dados->aplicarDescontoSobre) . "
				WHERE
					cfooid = " . $dados->id . "";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Exclui (UPDATE) um registro da base de dados.
     *
     * @param int $id Identificador do registro.
     *
     * @return boolean
     * @throws ErrorException
     */
    public function excluir(CreditoFuturoVO $dados) {

        $sql = "UPDATE
					credito_futuro
				SET
					cfodt_exclusao = NOW(),
                    cfousuoid_exclusao = " . intval($dados->usuarioExclusao) . "
				WHERE
					cfooid = " . intval($dados->id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs) == 0) {
            return false;
        }

        return true;
    }

    public function encerrar(CreditoFuturoVO $dados) {

        $sql = "UPDATE
					credito_futuro
				SET
					cfodt_encerramento = NOW(),
                    cfousuoid_encerramento = " . intval($dados->usuarioEncerramento) . ",
                    cfostatus = " . intval($dados->status) . "
				WHERE
					cfooid = " . intval($dados->id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs) == 0) {
            return false;
        }

        return true;
    }

    public function aprovar(CreditoFuturoVO $dados) {

        $sql = "UPDATE
                    credito_futuro
                SET
                    cfodt_avaliacao = NOW(),
                    cfousuoid_avaliador = " . intval($dados->usuarioAvaliador) . ",
                    cfostatus = " . intval($dados->status) . "
                WHERE
                    cfooid = " . intval($dados->id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }


        if (pg_affected_rows($rs) == 0) {
            return false;
        }

        return true;
    }

    public function reprovar(CreditoFuturoVO $dados) {

        $sql = "UPDATE
                    credito_futuro
                SET
                    cfodt_avaliacao = NOW(),
                    cfousuoid_avaliador = " . intval($dados->usuarioAvaliador) . ",
                    cfostatus = " . intval($dados->status) . "
                WHERE
                    cfooid = " . intval($dados->id) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_affected_rows($rs) == 0) {
            return false;
        }

        return true;
    }

    public function buscarUsuarioPorId($usuarioId) {

        $sql = "SELECT
                    cd_usuario AS usuario_id,
                    nm_usuario AS usuario_nome,
                    usuemail AS usuario_email,
                    depdescricao AS usuario_departamento
                FROM
                    usuarios
                LEFT JOIN
                    departamento ON (depoid = usudepoid)
                WHERE
                    cd_usuario = " . intval($usuarioId) . "
                LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO . ' - Carregar dados de usuarios no sistema.');
        }

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_object($rs);
        }

        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO . ' - Carregar dados de usuarios no sistema.');

    }


    /**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @param stdClass $parametros parametros para busca.
     *
     * @return array $retorno
     */
    public function buscarClienteNome($parametros) {

        $retorno = array();

        $parametros->nome = html_entity_decode($parametros->nome);

        $parametros->nome = urldecode($parametros->nome);

        $parametros->nome = utf8_decode($parametros->nome);


        if (trim($parametros->nome) === '') {
            echo json_encode($retorno);
            exit;
        }

        $sql = "SELECT

                        clioid,
                        clinome,
                        CASE WHEN clitipo = 'J' THEN
                            clino_cgc
                        ELSE
                            clino_cpf
                        END AS doc,
                        clitipo AS tipo

               FROM
                        clientes
               WHERE
                        clidt_exclusao IS NULL ";

               if (trim($parametros->tipo) != '') {
                   $sql  .= " AND
                        clitipo = '" . pg_escape_string($parametros->tipo) . "' ";
               }


               $sql .= " AND
                        clinome ILIKE '" . pg_escape_string($parametros->nome) . "%'

               ORDER BY
                        clinome
               LIMIT 100";



        if ($rs = pg_query($this->conn, $sql)) {
            if (pg_num_rows($rs) > 0) {
                $i = 0;
                while ($objeto = pg_fetch_object($rs)) {
                    $retorno[$i]['id'] = $objeto->clioid;
                    $retorno[$i]['label'] = utf8_encode($objeto->clinome);
                    $retorno[$i]['value'] = utf8_encode($objeto->clinome);
                    $retorno[$i]['tipo'] = utf8_encode($objeto->tipo);
                    if ($objeto->tipo == 'J') {
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                    } else if ($objeto->tipo == 'F') {
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                    }
                    $i++;
                }
            }
        }

        return $retorno;
    }

    /**
     * Buscar cliente por documento (CPF/CNPJ)
     *
     * @param stdClass $parametros tipo de cliente e numero de documento
     *
     * @return array $retorno
     */
    public function buscarClienteDoc($parametros) {

        $retorno = array();



        if (trim($parametros->documento) === '') {
            echo json_encode($retorno);
            exit;
        }

        $sql = "SELECT

                        clioid,
                        clinome,
                        CASE WHEN clitipo = 'J' THEN
                            clino_cgc
                        ELSE
                            clino_cpf
                        END AS doc

               FROM
                        clientes
               WHERE
                        clidt_exclusao IS NULL
               AND
                        clitipo = '" . pg_escape_string($parametros->tipo) . "' ";

        if ($parametros->tipo == 'J') {
            $sql .= " AND
                                lpad(clino_cgc::TEXT, 14,'0') LIKE '" . pg_escape_string($parametros->documento) . "%' ";
        } else if ($parametros->tipo == 'F') {
            $sql .= " AND
                                lpad(clino_cpf::TEXT, 11,'0') LIKE '" . pg_escape_string($parametros->documento) . "%' ";
        }

        $sql .= "
                  ORDER BY
                        clinome
                    LIMIT 100 ";

        if ($rs = pg_query($this->conn, $sql)) {
            if (pg_num_rows($rs) > 0) {
                $i = 0;
                while ($objeto = pg_fetch_object($rs)) {
                    $retorno[$i]['id'] = $objeto->clioid;
                    $retorno[$i]['nome'] = utf8_encode($objeto->clinome);
                    if ($parametros->tipo == 'J') {
                        $retorno[$i]['label'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                        $retorno[$i]['value'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                    } else if ($parametros->tipo == 'F') {
                        $retorno[$i]['label'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                        $retorno[$i]['value'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                    }
                    $i++;
                }
            }
        }

        return $retorno;
    }

    /**
     * Buscar clientes por numero de contrato
     *
     * @return array $retorno
     */
    public function buscarClienteContrato($parametros) {

        $retorno = array();

        $sql = "SELECT
                    connumero,
                    clioid,
                    clinome,
                    clitipo,
                    CASE WHEN clitipo = 'J' THEN
                             clino_cgc
                        ELSE
                             clino_cpf
                        END AS doc
                FROM
                    contrato
                INNER JOIN
                    clientes ON (clientes.clioid = contrato.conclioid)
                WHERE
                    connumero::TEXT ILIKE '" . $parametros->term . "%'
                AND
                    clidt_exclusao IS NULL
                AND
                    condt_ini_vigencia IS NOT NULL
                AND
                    condt_exclusao IS NULL
                LIMIT 100";


        if ($rs = pg_query($this->conn, $sql)) {
            $i = 0;
            while ($objeto = pg_fetch_object($rs)) {
                //label & value obrigatorios
                $retorno[$i]['id'] = $objeto->clioid;
                $retorno[$i]['nome'] = utf8_encode($objeto->clinome);
                $retorno[$i]['label'] = $objeto->connumero;
                $retorno[$i]['value'] = $objeto->connumero;
                $retorno[$i]['tipo'] = $objeto->clitipo;
                if ($objeto->clitipo == 'J') {
                    $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                } else if ($objeto->clitipo == 'F') {
                    $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                }
                $i++;
            }
        }

        return $retorno;
    }

    /**
     * Método que realizar busca de Obrigação Financeira de Desconto
     *
     * @return array $retorno Array de objetos para popular combo na view.
     */
    public function buscarObrigacaoFinanceiraDesconto() {

        $sql = "SELECT
					obroid,
					obrobrigacao
				FROM
					obrigacao_financeira
				JOIN
					obrigacao_financeira_grupo
						ON ofgoid = obrofgoid
				WHERE
					obrdt_exclusao IS NULL
				AND
					ofgdescricao ILIKE '%Desconto%'
				ORDER BY
					obrobrigacao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = array();
        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Buscar usuarios inclusao credito futuro
     *
     * @return array $retorno
     */
    public function buscarUsuarioInclusaoCreditoFuturo() {

        $sql = "SELECT
                    DISTINCT cd_usuario,
                    nm_usuario
                FROM
                    credito_futuro_cliente_indicador
                INNER JOIN
                    usuarios ON cd_usuario = cfciusuoid_inclusao
                ORDER BY
                    nm_usuario ASC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = array();
        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método que busca motivos do crédito conforme o usuarios
     *
     * @return array $retorno Motivos de créditos
     */
    public function buscarMotivoDoCredito() {

        //verifica se o usuario logado tem a função motivo de credito restrito
        $usuarioMotivoCreditoRestrito = isset($_SESSION['funcao']['credito_futuro_motivo_credito_restrito']) && $_SESSION['funcao']['credito_futuro_motivo_credito_restrito'] ? true : false;

        $sql = "SELECT
                    cfmcoid,
                    cfmcdescricao
                FROM
                    credito_futuro_motivo_credito
                WHERE
                    cfmcdt_exclusao IS NULL ";

        if ($usuarioMotivoCreditoRestrito) {

            $sql .= "AND
                    cfmctipo = 3 ";
        }

        $sql .= " ORDER BY
                    cfmcdescricao ASC";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $retorno = array();
        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Buscar motivo de crédito pela sessão criada no cadastro, step 2
     */
    public function buscarMotivoCreditoPorSessionId() {

        $motivoCreditoId = isset($_SESSION['credito_futuro']['step_2']['cfocfmcoid']) && trim($_SESSION['credito_futuro']['step_2']['cfocfmcoid']) != '' ? trim($_SESSION['credito_futuro']['step_2']['cfocfmcoid']) : '';

        if ($motivoCreditoId == '') {
            return '';
        }

        $sql = "SELECT
                        cfmcdescricao
                   FROM
                        credito_futuro_motivo_credito
                   WHERE
                        cfmcoid =" . $motivoCreditoId . "
                   LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'cfmcdescricao');
        }

        return '';
    }


    public function buscarMotivoCreditoFormaPgtCliente () {

        $retorno = array();

        //verifica se o usuario logado tem a função motivo de credito restrito
        $usuarioMotivoCreditoRestrito = isset($_SESSION['funcao']['credito_futuro_motivo_credito_restrito']) && $_SESSION['funcao']['credito_futuro_motivo_credito_restrito'] ? true : false;

        $clienteID = isset($_SESSION['credito_futuro']['step_1']['cfoclioid']) && trim($_SESSION['credito_futuro']['step_1']['cfoclioid']) != '' ? trim($_SESSION['credito_futuro']['step_1']['cfoclioid']) : '';

        if (trim($clienteID) == '') {
            return $retorno;
        }

        $sqlVerifica = " SELECT
                            forcdebito_conta AS debito,
                            forccobranca_cartao_credito AS credito
                         FROM
                            clientes
                         INNER JOIN
                            forma_cobranca ON (cliformacobranca = forcoid)
                         WHERE
                            clioid = " . $clienteID . "
                         LIMIT 1";

        if (!$rsVerifica = pg_query($this->conn, $sqlVerifica)){
             throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $clienteDebitoConta = ''; //débito de conta
        $clienteCobrancaCD = ''; //cobrança cartão de crédito

        if (pg_num_rows($rsVerifica) > 0) {
            $clienteDebitoConta = pg_fetch_result($rsVerifica, 0, 'debito') == 't' ? true : false;
            $clienteCobrancaCD = pg_fetch_result($rsVerifica, 0, 'credito') == 't' ? true : false;
        }

        $sqlMotivo = "SELECT
                        cfmcoid,
                        cfmcdescricao,
                        cfmctipo
                     FROM
                        credito_futuro_motivo_credito
                     WHERE
                        cfmcdt_exclusao IS NULL ";

        if (!$clienteDebitoConta) {
            $sqlMotivo .= "AND
                            cfmctipo <> 4 ";
        }

        if (!$clienteCobrancaCD) {
            $sqlMotivo .= "AND
                            cfmctipo <> 5 ";
        }

        if ($usuarioMotivoCreditoRestrito) {

             $sqlMotivo .= "AND
                                cfmctipo = 3 ";
        }

        $sqlMotivo .= "ORDER BY
                                cfmcdescricao ASC";

        if (!$rsMotivo = pg_query($this->conn, $sqlMotivo)){
             throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rsMotivo)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Formatar dados (CPF||CNPJ)
     *
     * @param string $tipo  tipo doc
     * @param string $valor valor do doc
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

    /**
     * Método verificarCadastroEmailAprovacao()
     * RN001 - Deve existir pelo menos um e-mail cadastrado no cadastro E-mail
     * para Aprovação de Crédito Futuro encontrado em
     * Finanças > Crédito Futuro - Parametrização.
     *
     * @return boolean
     */
    public function verificarCadastroEmailAprovacao() {

        $retorno = false;

        $sql = "SELECT cferoid FROM credito_futuro_email_responsavel";
        $sqlParam = "SELECT cfeaoid FROM credito_futuro_email_aprovacao WHERE cfeausuoid_exclusao IS NULL";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!$rsParam = pg_query($this->conn, $sqlParam)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0 && pg_num_rows($rsParam) > 0) {
            $retorno = true;
        }

        return $retorno;
    }

    /**
     * Método verificarProtocolo()
     *
     * JU -  Já Utilizado.
     * NC - Não concluido.
     * NE - Não encontrado.
     * NP - Não procede.
     * CD - Cliente do protocolo difere do informado.
     *
     * @param object $parametros
     * @return string|boolean
     */
    public function verificarProtocolo($parametros) {

        $clienteIndicado = $_SESSION['credito_futuro']['step_1']['cfoclioid'];

        $sql = "SELECT
                    ancvlr_total AS valor,
                    cfooid AS ja_utilizado,
                    anccontestacao_procedente AS concluido,
                    ancancsoid AS status,
                    ancclioid AS cliente
                FROM
                    analise_contas
                LEFT JOIN
                    credito_futuro ON (cfoancoid = ancoid AND cfodt_exclusao IS NULL)
                WHERE
                    ancoid = " . $parametros->protocolo . "
                AND
                    ancdt_exclusao IS NULL
                LIMIT 1";

        //print_r($sql);



        if ($rs = pg_query($this->conn, $sql)) {

            if (pg_num_rows($rs) > 0) {

                if (!is_null(pg_fetch_result($rs, 0, 'ja_utilizado'))) {
                    return 'JU';
                }

                if (pg_fetch_result($rs, 0, 'status') != '4') {
                    return 'NC';
                }

                if (pg_fetch_result($rs, 0, 'concluido') == 'false') {
                    return 'NP';
                }

                if (pg_fetch_result($rs, 0, 'cliente') != $clienteIndicado) {
                    return 'CD';
                }

                return number_format(pg_fetch_result($rs, 0, 'valor'), 2,',','.');

            } else {
                return 'NE';
            }

        } else {
           return false;
        }

    }


    /**
     * Método verificarContrato()
     *
     * JU -  Já Utilizado
     * MC - Cliente do contrado indicado é o mesmo do cadastrado no crédito futuro
     * SE - Contrato sem equipamento instalado
     * SI - Sem data de inicio de vigencia
     * NE - Não encontrado
     *
     * @param object $parametros
     * @return string|boolean
     */
    public function verificarContrato($parametros) {

        $clienteCreditoFuturo = isset($_SESSION['credito_futuro']['step_1']['cfoclioid']) && trim($_SESSION['credito_futuro']['step_1']['cfoclioid']) != '' ? trim($_SESSION['credito_futuro']['step_1']['cfoclioid']) : '';

        if (trim($clienteCreditoFuturo) == '' || trim($parametros->contrato) == '') {
            return false;
        }


        $sql = "SELECT
                    cfoconnum_indicado AS ja_utilizado, --se for diferente de null já esta sendo ultilizado
                    conclioid AS cliente_contrato_indicado, -- para verificar se o cliente do contrato  é o mesmo do credito futuro
                    conequoid AS equipamento_instalado, -- se for null não possui equipamento instalado
                    condt_ini_vigencia AS inicio_vigencia -- se for  null contrato sem data de incio de vigencia
                FROM
                    contrato
                LEFT JOIN
                    credito_futuro ON (cfoconnum_indicado = connumero) AND cfodt_exclusao IS NULL
                WHERE
                    connumero = " . trim($parametros->contrato) . "
                AND
                    condt_exclusao IS NULL
                LIMIT 1";


        if ($rs = pg_query($this->conn, $sql)) {

            if (pg_num_rows($rs) > 0) {

                if (!is_null(pg_fetch_result($rs, 0, 'ja_utilizado'))) {
                    return 'JU';//contrato indicado ja utilizado
                }

                if ( trim(pg_fetch_result($rs, 0, 'cliente_contrato_indicado')) == $clienteCreditoFuturo) {
                    return 'MC'; //cliente indicador e cliente do credito futuro são os mesmos
                }

                if (is_null(pg_fetch_result($rs, 0, 'equipamento_instalado'))) {
                    return 'SE';
                }

                if (is_null(pg_fetch_result($rs, 0, 'inicio_vigencia'))) {
                    return 'SI';
                }

                return 'OK';

            } else {
                return 'NE';
            }

        } else {
           return false;
        }

    }

    /**
     * Classe para persistir o objeto Credito Fututo
     */
    public function salvar(CreditoFuturoVO $CreditoFuturo) {

    	//$this->begin();
    	 $sql = "
    			INSERT INTO credito_futuro (
    				cfousuoid_inclusao,
    				cfodt_inclusao,
    				cfoclioid,
    				cfoconnum_indicado,
    				cfocfcpoid,
    				cfocfmcoid,
    				cfoancoid,
    				cfoobroid_desconto,
    				cfostatus,
    				cfotipo_desconto,
    				cfovalor,
    				cfoforma_aplicacao,
    				cfoforma_inclusao,
    				cfosaldo,
    				cfoobservacao,
    				cfoaplicar_desconto

    			) VALUES (
    				" . $CreditoFuturo->usuarioInclusao . ",
    				NOW(),
    				" . $CreditoFuturo->cliente . ",
    				" . $CreditoFuturo->contratoIndicado . ",
    				" . $CreditoFuturo->CampanhaPromocional->cfcpoid . ",
    				" . $CreditoFuturo->MotivoCredito->id . ",
    				" . $CreditoFuturo->protocolo . ",
    				" . $CreditoFuturo->obrigacaoFinanceiraDesconto . ",
    				" . $CreditoFuturo->status . ",
    				" . $CreditoFuturo->tipoDesconto . ",
    				" . $CreditoFuturo->valor . ",
    				" . $CreditoFuturo->formaAplicacao . ",
    				" . $CreditoFuturo->formaInclusao . ",
    				" . $CreditoFuturo->saldo . ",
    				'" . $CreditoFuturo->observacao . "',
    				" . $CreditoFuturo->aplicarDescontoSobre . "
    			) RETURNING cfooid;";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		//$this->rollback();
    		throw new Exception('Falha ao tentar inserir o crédito futuro.');
    	}

    	$idCreditoFuturo = 0;

    	if (pg_num_rows($rs) > 0) {
    		$idCreditoFuturo = pg_fetch_result($rs, 0, 'cfooid');
    	}

    	//$this->commit();
    	return $idCreditoFuturo;

    }

    /**
     * Metodo para buscar os dados de parametrizacao de credito futuro
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @return Object
     */
    public function obterParametrosCreditoFuturo() {

    	$sql = "
    			SELECT
    				cfeaoid,
					cfeavalor_credito_futuro AS valorCredito,
					cfeavalor_percentual_desconto AS porcentagem,
					cfeaparcelas AS numeroParcelas,
					cfeaobroid_contestacao,
					cfeaobroid_contas,
					cfeaobroid_campanha,
					cfeacabecalho AS assuntoEmail,
					cfeacorpo AS corpoEmail,
					cfeadt_exclusao,
					cfeausuoid_exclusao,
					cfeadt_inclusao,
					cfeausuoid_inclusao
    			FROM
    				credito_futuro_email_aprovacao
    			WHERE
    				cfeadt_exclusao IS NULL";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar obter os parametros de credito futuro.');
    	}

    	$parametros = new stdClass();

    	if (pg_num_rows($rs) > 0) {
    		$parametros = pg_fetch_object($rs);
    	}

    	return $parametros;
    }

    /**
     * Metodo para buscar a descricao da obrigacao financeira
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param int id obrigacao
     */
    public function obterDescricaoObrigacaoFinanceiraPorId($obrigacaoId) {

    	 $sql = "
    			SELECT
    				obrobrigacao
    			FROM
    				obrigacao_financeira
    			WHERE
    				obroid = $obrigacaoId";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar obter a descrição da obrigação financeira.');
    	}

    	$descricao = null;

    	if (pg_num_rows($rs) > 0) {
    		$descricao = pg_fetch_result($rs, 0, 'obrobrigacao');
    	}

    	return $descricao;

    }

    /**
     * Metodo para buscar os dados do cliente por id
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param int idCliente
     * @return array dadosCliente
     */
    public function obterDadosClientePorId($clienteId) {

    	$sql = "
	    	SELECT
	    		clinome as nome,
	    		cliemail as email,
	    		(CASE WHEN
	    			clitipo = 'F'
	    		THEN
	    			clino_cpf
	    		ELSE
	    			clino_cgc
	    		END) AS numerodocumento,
                clitipo as tipo
	    	FROM
	    		clientes
	    	WHERE
	    		clioid = $clienteId";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar obter dados do cliente.');
    	}

    	$cliente = array();

    	if (pg_num_rows($rs) > 0) {
    		$cliente = pg_fetch_assoc($rs);

            if ($cliente['tipo'] == 'J') {
                $cliente['numerodocumento'] = $this->formatarDados('cnpj', $cliente['numerodocumento']);
            } else {
                $cliente['numerodocumento'] = $this->formatarDados('cpf', $cliente['numerodocumento']);
            }
    	}

    	return $cliente;
    }

    /**
     * Metodo para inserir historico no cliente
     * invoca uma funcao do banco de dados que faz um calculo para saber em qual tabela inserir o historico
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param object CreditoFuturo
     * @param string textoObservacao
     * @return void
     *
     */
    public function incluirHistoricoCliente($CreditoFuturo, $textoObservacao) {

    	/*
    	 * Parametros da funcao
    	 * - clioid
    	 * - usuoid
    	 * - obs
    	 * - tipo => A - Alteração cadastrar (NOME, CPF, CNPJ)
    	 * - protocolo
    	 * - id_atendimento
    	 */
    	$cliente = ($CreditoFuturo->cliente > 0) ? $CreditoFuturo->cliente : 0;
    	$usuario = ($CreditoFuturo->usuarioInclusao > 0) ? $CreditoFuturo->usuarioInclusao : 0;
    	$textoObservacao = (strlen($textoObservacao) > 0) ? $textoObservacao : ' ';
    	
    	 $sql = "SELECT cliente_historico_i(
    				$cliente,
    				$usuario,
    				'" . pg_escape_string($textoObservacao) . "',
    				'A',
    				'',
    				''
    			)";




    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar inserir histórico do cliente.');
    	}
    }

    /**
     * Metodo para inserir historico do credito futuro
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @param object CreditoFuturo
     */
    public function incluirHistoricoCreditoFuturo($CreditoFuturoHistoricoVO) {


      $sql = "INSERT INTO
                    credito_futuro_historico
                    (
                        cfhdt_inclusao,
                        cfhusuoid,
                        cfhoperacao,
                        cfhorigem,
                        cfhcfooid,
                        cfhstatus,
                        cfhtipo_desconto,
                        cfhforma_aplicacao,
                        cfhaplicar_desconto,
                        cfhqtde_parcelas,
                        cfhvalor,
                        cfhsaldo,
                        cfhobservacao,
                        cfhjustificativa,
                        cfhobrigacao_desconto,
                        cfhsaldo_parcelas,


                        cfhnf_numero,
                        cfhnf_serie,
                        cfhdt_emissao_nf,
                        cfhvalor_total_nf,
                        cfhvl_total_itens_nf,
                        cfhvalor_aplicado_desconto,
                        cfhnum_parcela_aplicada
                    )
                    VALUES
                    (
                        NOW(),
                        " . $CreditoFuturoHistoricoVO->usuarioInclusao . ",
                        " . $CreditoFuturoHistoricoVO->operacao . ",
                        " . $CreditoFuturoHistoricoVO->origem . ",
                        " . $CreditoFuturoHistoricoVO->creditoFuturoId . ",
                        " . $CreditoFuturoHistoricoVO->status . ",
                        " . $CreditoFuturoHistoricoVO->tipoDesconto . ",
                        " . $CreditoFuturoHistoricoVO->formaAplicacao . ",
                        " . $CreditoFuturoHistoricoVO->aplicarDescontoSobre . ",
                        " . $CreditoFuturoHistoricoVO->qtdParcelas . ",
                        " . $CreditoFuturoHistoricoVO->valor . ",
                        " . $CreditoFuturoHistoricoVO->saldo . ",
                        '" . pg_escape_string($CreditoFuturoHistoricoVO->observacao) . "',
                        '" . pg_escape_string($CreditoFuturoHistoricoVO->justificativa) . "',
                        " . $CreditoFuturoHistoricoVO->obrigacaoFinanceiraDesconto . ",
                        '" . pg_escape_string($CreditoFuturoHistoricoVO->cfhsaldo_parcelas) . "',

                        " . $CreditoFuturoHistoricoVO->nf_numero . ",
                        '" . pg_escape_string($CreditoFuturoHistoricoVO->nf_serie) . "',
                        " . $CreditoFuturoHistoricoVO->dt_emissao_nf . ",
                        " . $CreditoFuturoHistoricoVO->valor_total_nf . ",
                        " . $CreditoFuturoHistoricoVO->vl_total_itens_nf . ",
                        " . $CreditoFuturoHistoricoVO->valor_aplicado_desconto . ",
                        " . $CreditoFuturoHistoricoVO->num_parcela_aplicada . "
                    )";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar inserir histórico do crédito futuro.');
    	}




    }

    /**
     * Metodo para obter os usuarios de aprovacao
     *
     * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
     * @return Object
     */
    public function obterUsuariosAprovacao($tipoMotivo){

    	$sql = "
    			SELECT
    				cfmpcfeusuoid AS usuarioId,
    				usuemail as email
    			FROM
    				credito_futuro_motivo_responsavel
    			INNER JOIN
					usuarios ON cfmpcfeusuoid = cd_usuario
    			WHERE
    				cfmptipomotivo = " . $tipoMotivo . "";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar buscar os usuários de aprovação.');
    	}

    	$usuarios = array();

    	if (pg_num_rows($rs) > 0) {

    		while ($row = pg_fetch_object($rs)) {
    			$usuarios[] = array (
    				'id' => $row->usuarioId,
    				'email' => $row->email
    			);
    		}
    	}

    	return $usuarios;
    }

    /**
     * Metodo para adicionar parcelas referente ao credito futuro
     *
     * @param creditoFuturoId
     * @param numeroParcela
     * @param valor
     */
    public function adicionarParcela($creditoFuturoId, $numeroParcela, $valor) {

    	$sql = "
    			INSERT INTO
    				credito_futuro_parcela (
    					cfpcfooid,
    					cfpnumero,
    					cfpvalor,
    					cfpativo
    			) VALUES (
    				$creditoFuturoId,
    				$numeroParcela,
    				$valor,
    				't'
    			);";
    	
		if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar incluir parcela.');
    	}
    }


    public function deletarParcelas($creditoFuturoId) {

        $sql = "DELETE FROM
                    credito_futuro_parcela
                WHERE
                    cfpcfooid = " . $creditoFuturoId . "";

        if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar excluir parcelas.');
    	}

        if (pg_affected_rows($rs) == 0) {
            return false;
        }

        return true;

    }

    /**
     * metodo para incluir historico no contrato do cliente indicador
     */
    public function incluirHistoricoContratoIndicador($contrato, $usuario, $observacao) {

    	$sql = "
    			SELECT historico_termo_i(
    				" . $contrato . ",
    				" . $usuario . ",
    				'" . $observacao . "'
    			);";
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar incluir historico de termo.');
    	}
    }

    /**
     *
     */
    public function incluirHistoricoContestacao($descricao, $protocolo, $protocoloStatus, $usuario) {

    	$sql = "
    		INSERT INTO
    			analise_contas_historico (
    				anchdescricao,
    				anchancoid,
    				anchancsoid,
    				anchusuoid,
    				anchdt_cadastro
    		) VALUES ('" . $descricao . "',
    					" . $protocolo . ",
    					" . $protocoloStatus . ",
    					" . $usuario . ",
    					NOW()
    				 )";

    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar incluir historico contestação.');
    	}
    }

    /**
     *
     *
     */
    public function buscarStatusAnaliseContas($protocolo) {

    	$sql = "SELECT
    				ancancsoid as statusId
    			FROM
    				analise_contas
    			WHERE
    				ancoid = " . $protocolo;
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar obter o status da análise de contas.');
    	}

    	$status = null;

    	if (pg_num_rows($rs) > 0) {
    		$status = pg_fetch_result($rs, 0, 'statusId');
    	}

    	return $status;

    }

    /**
     * Método salvarMovimentacao()
     * Realiza inserção de movimento ao aplicar credito futuro.
     *
     * @param int   $credito_id
     * @param int   $nota_fiscal_id
     * @param int   $parcela_id
     * @param float $valor
     * @return boolean
     */
    public function salvarMovimentacao($credito_id, $nota_fiscal_id, $parcela_id, $valor) {

        $sql = "INSERT INTO
                    credito_futuro_movimento
                    (cfmcfooid, cfmnfloid, cfmcfpoid, cfmvalor, cfmdt_inclusao)
                VALUES
                    (". intval($credito_id) .",". intval($nota_fiscal_id) .",". intval($parcela_id) .",". $valor .", NOW())";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha ao tentar registrar movimentação de credito futuro.');
        }

        if (pg_affected_rows($rs) > 0) {
            return true;
        }

        return false;

    }


    /**
     * Método buscarSaldoAtual()
     * Retorna saldo atual do credito futuro.
     *
     * @param int $credito_id
     * @return float $saldo
     */
    public function buscarSaldoAtual($credito_id) {

        $sql = "   		
			SELECT
			(
				(
					SELECT COALESCE(SUM(cfp.cfpvalor),0) 
					FROM credito_futuro_parcela AS cfp 
					WHERE cfp.cfpcfooid = " . $credito_id . "
					AND cfp.cfpativo = true
				) - (
					SELECT COALESCE(SUM(cfm.cfmvalor),0) 
					FROM credito_futuro_movimento AS cfm 
					WHERE cfm.cfmcfooid = " . $credito_id . "
					AND cfm.cfmdt_exclusao IS NULL
				)
			) AS saldo";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha ao tentar recuperar saldo do crédito futuro.');
        }

        return pg_fetch_result($rs, 0, 'saldo');
    }

    /**
     * Método atualizarSaldo()
     * Método responsável poe realizar update do saldo do crédito futuro.
     *
     * @param int   $credito_id
     * @param float $valor
     *
     * @return boolean
     */
    public function atualizarSaldo($credito_id, $valor) {

        $sql = "UPDATE
                        credito_futuro
                SET
                        cfosaldo = " . $valor . "
                WHERE
                        cfooid = " . intval($credito_id);


        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha ao tentar atualizar saldo do crédito futuro.');
        }

        if (pg_affected_rows($rs) > 0) {
            return true;
        }

        return false;

    }

    /**
     * Método verificarParcelasAtivasCreditoFuturo();
     * Verifica se há parcelas ativas para o crédito futuro informado no parâmetro,
     * se houver retorna a quantidade, se não houver retorna false.
     *
     * @param int $credito_id
     *
     * @return int     $parcelas_ativas
     * @return boolean
     */
    public function verificarParcelasAtivasCreditoFuturo($credito_id) {

        $sql= "SELECT
                    COUNT(cfpcfooid) AS qtd
                FROM
                    credito_futuro_parcela
                WHERE
                    cfpativo = true
                AND
                    cfpcfooid = " . intval($credito_id);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha ao tentar verificar quantidade de parcelas ativas do crédito futuro.');
        }

        $parcelas_ativas = pg_fetch_result($rs, 0, 'qtd');

        if ($parcelas_ativas > 0) {
            return $parcelas_ativas;
        }

        return false;
    }

    /**
     * Método inativarParcelaCredito()
     * Método responsável por inativar parcela do crédito futuro.
     *
     * @param int $credito_id
     * @param int $parcela_id
     *
     * @return boolean
     */
    public function inativarParcelaCredito($credito_id, $parcela_id) {

        $sql = "UPDATE
                    credito_futuro_parcela
                SET
                    cfpativo = false
                WHERE
                    cfpoid = " . intval($parcela_id) . "
                AND
                    cfpcfooid = " . intval($credito_id);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha ao tentar inativar parcela do crédito futuro.');
        }

        if (pg_affected_rows($rs) > 0) {
            return true;
        }

        return false;
    }

    public function encerrarCredito($credito_id, $usuarioEncerramento) {

        $sql = "UPDATE
                    credito_futuro
                SET
                    cfodt_encerramento = NOW(),
                    cfousuoid_encerramento = " . intval($usuarioEncerramento) . ",
                    --status de concluido
                    cfostatus = 2
                WHERE
                    cfooid = " . intval($credito_id);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha ao tentar encerrar credito futuro.');
        }


        if (pg_affected_rows($rs) > 0) {
            return true;
        }

        return false;
    }

    /**
     * 
     * Retorna o tipo de campanha promocional
     *
     * @author Rafael Dias <rafael.dias@sascar.com.br>
     * @version 07/10/2014
     * @param unknown_type $cfcpoid
     */
    public function retornaTipoCampanhaPromocional($cfcpoid){
    	
    	$cfcpoid = ($cfcpoid > 0) ? $cfcpoid : 'NULL';

    	$sql = "
    			SELECT 
					cftpdescricao
				FROM 
					credito_futuro_tipo_campanha
					INNER JOIN credito_futuro_campanha_promocional ON cfcpcftpoid = cftpoid
				WHERE cfcpoid = " . $cfcpoid . "
				AND cftpdt_exclusao IS NULL";
    	
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao tentar retornar o tipo de campanha promocional.');
    	}
    	
    	if (is_resource($rs)){
	    	if (pg_num_rows($rs) > 0){
	    		return pg_fetch_result($rs, 0, 'cftpdescricao');	
	    	} else {
	    		return '';
	    	}
    	} else{
    		return '';
    	}
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
