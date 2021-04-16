<?php

/**
 * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 */
class RelAtendimentoFrontEndDAO {

    private $conn;

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param array $options Dados postados através do formulário
     */
    public function pesquisar($options) {

        foreach ($options as $key => $option) {
            $options[$key] = urldecode($option);
        }

        $this->options = $options;

        /**
         * Precisamos de mais um BETWEEN para a hora pois o filtro deve ser feito
         * dia a dia, ou seja, se o usuário postar das 00:00 até às 12:00, ele quer
         * apenas a manhã de cada dia.
         * 
         */
        if (!empty($options['hora_ini']) and !empty($options['hora_fim'])) {

            $options['hora_ini'] .= ':00';
            $options['hora_fim'] .= ':59';

            $sql_add = " WHERE atcdt_inicio BETWEEN '{$options['dt_ini']} 00:00:00' AND '{$options['dt_fim']} 23:59:59'";
            $sql_add .= " AND atcdt_inicio BETWEEN to_char(atcdt_inicio, 'DD/MM/YYYY {$options['hora_ini']}')::timestamp AND to_char(atcdt_inicio, 'DD/MM/YYYY {$options['hora_fim']}')::timestamp";
        } else {
            $sql_add = " WHERE atcdt_inicio BETWEEN '{$options['dt_ini']} 00:00:00' AND '{$options['dt_fim']} 23:59:59'";
        }

        if ($options['status_protocolo'] == 'C') {
            $sql_add .= " AND (pr.protoid IS NOT NULL AND COALESCE(t.tab_qtde, 0) = 0) ";
        } elseif ($options['status_protocolo'] == 'P') {
            $sql_add .= " AND (pr.protoid IS NOT NULL AND COALESCE(t.tab_qtde, 0) > 0) ";
        }

        if (!empty($options['nome_cliente'])) {
            $sql_add .= " AND clinome ILIKE '%{$options['nome_cliente']}%'";
        }

        if (!empty($options['placa'])) {
            $sql_add .= " AND veiplaca ILIKE '%{$options['placa']}%' ";
        }

        if (!empty($options['protocolo_sascar'])) {
            $sql_add .= " AND protprotocolo = '{$options['protocolo_sascar']}'";
        }

        if (!empty($options['protocolo_vivo'])) {
            $sql_add .= " AND protprotocolo_vivo = '{$options['protocolo_vivo']}'";
        }

        if (trim($options['tipo_ligacao']) != "") {
            $sql_add .= " AND atatipo_ligacao = {$options['tipo_ligacao']}";
        }

        if (!empty($options['atendente'])) {
            $sql_add .= " AND cd_usuario = {$options['atendente']}";
        }

        if (!empty($options['classe_cliente'])) {
            $sql_add .= " AND clicloid = {$options['classe_cliente']}";
        }

        if (!empty($options['motivo_nivel_1'])) {
            $sql_add .= " AND (ag.agroid = {$options['motivo_nivel_1']}) AND (ag.agrexclusao IS NULL)";
        }

        if (!empty($options['motivo_nivel_2'])) {
            $sql_add .= " 
                AND (
                (
                    atm.atmoid = {$options['motivo_nivel_2']} 
                    AND 
                        atm.atmoid_pai IS NULL 
                    AND 
                        atm.atmagroid IS NOT NULL 
                    AND 
                        atm.atmexclusao IS NULL
                )
                OR
                (
                    atm_pai.atmoid = {$options['motivo_nivel_2']} 
                    AND 
                        atm_pai.atmoid_pai IS NULL 
                    AND 
                        atm_pai.atmagroid IS NOT NULL 
                    AND 
                        atm_pai.atmexclusao IS NULL
                ))";
        }

        if (!empty($options['motivo_nivel_3'])) {
            $sql_add .= " 
                AND
                (
                    atm.atmoid = {$options['motivo_nivel_3']} 
                AND 
                    atm.atmoid_pai IS NOT NULL 
                AND 
                    atm.atmexclusao IS NULL
                )";
        }

        if ($options['status_aten_mot'] == 'C') {
            $sql_add .= " AND atcdt_fim  IS NOT NULL";
        } elseif ($options['status_aten_mot'] == 'P') {
            $sql_add .= " AND atcdt_fim  IS NULL";
        }

        if (trim($options['tipo_contrato']) != "") {
            switch ($options['tipo_contrato']) {
                case "C":
                    $sql_add .= " AND (tpcseguradora = false AND tpcdescricao NOT ILIKE 'Ex-%') ";
                    break;
                case "S":
                    $sql_add .= " 
                        AND (tpcseguradora = true AND tpcdescricao NOT ILIKE 'Ex-%') ";
                    break;
                default:
                    $sql_add .= " 
                        AND conno_tipo = {$options['tipo_contrato']} ";
                    break;
            }
        }

        if (trim($options['pessoa_autorizada']) != "") {
            $sql_add .= " AND protpessoa_autorizada ILIKE '%" . $options['pessoa_autorizada'] . "%' ";                    
        }

        $limit_numero_resultados = "";

        if (!empty($options['numero_resultados'])) {
            $limit_numero_resultados = " LIMIT {$options['numero_resultados']}";
        }

        if ($options['tipo_relatorio'] == 'analitico') {
            return $this->getResultAnalitico($sql_add, $limit_numero_resultados, $options);
        }

        /**
         * O relatório sintético e data_hora usam o mesmo método e o mesmo layout,
         * porém o data_hora tem o detalhamento das horas.
         */
        if ($options['tipo_relatorio'] == 'sintetico' || $options['tipo_relatorio'] == 'data_hora') {
            return $this->getResultSintetico($sql_add, $limit_numero_resultados, $options['atendente']);
        }
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param string $filtro Cláusulas SQL
     * @param string $limit_numero_resultados limitador da consulta
     * @param int $id_usuario Utilizado para buscar o nome do usuário
     */
    public function getResultSintetico($filtro, $limit_numero_resultados, $id_usuario) {

        $sql = "
				SELECT      
                    atc.atcoid as id_atendimento,
                    atc.atcdt_inicio as data_hora,
                    atc.atcdt_fim as data_hora_fim,
                    pr.protoid AS id_protocolo,
                    pr.protprotocolo as protocolo_sascar,
                    pr.protprotocolo_vivo as protocolo_vivo, 
                    c.clioid AS id_cliente,
                    c.clinome as nome_cliente,
                    clic.clicldescricao as classe_cliente,
                    (
                        SELECT 
                            CASE
                                WHEN atatipo_ligacao = 0
                                    THEN 'Sem ligação'
                                WHEN atatipo_ligacao = 1
                                    THEN 'Ativa'
                                WHEN atatipo_ligacao = 2
                                    THEN 'Receptiva'
                                WHEN atatipo_ligacao = 3
                                    THEN 'Retorno'
                                WHEN atatipo_ligacao = 4
                                    THEN 'Outros Canais'
                            END
                        FROM
                            atendimento_acesso
                        WHERE
                            ataatcoid = atc.atcoid
                        ORDER BY 
                            ataoid DESC
                        LIMIT 1
                    ) AS tipo_ligacao,  
                    EXTRACT (EPOCH FROM AGE(TO_CHAR(COALESCE(atcdt_fim, atcdt_inicio), 'YYYY-MM-DD HH24:MI:SS')::timestamp, TO_CHAR(atcdt_inicio, 'YYYY-MM-DD HH24:MI:SS')::timestamp)) AS tempo_atendimento,
                    ag.agrdescricao AS motivo_nivel1,                                
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm_pai.atmdescricao
                    ELSE
                        atm.atmdescricao 
                    END AS motivo_nivel2,
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm.atmdescricao 
                    END AS motivo_nivel3,                          
                    CASE 
                        WHEN atc.atcdt_fim IS NULL THEN 
                            'Pendente'
                        ELSE 
                            'Concluído'
                    END AS status_atendimento,
                    u.ds_login AS atendente,
                    tc.tpcdescricao as tipo_contrato,
                    v.veiplaca as placa,
                    COALESCE(t.tab_qtde, 0) as total_atendimento_cliente,
                    CASE WHEN pr.protoid IS NULL  
                                                           THEN FALSE
                                WHEN COALESCE(t.tab_qtde, 0) > 0
                                                           THEN FALSE
                               ELSE TRUE
                                   END as protocolo_concluido,
                    ac.atatipo_ligacao tipo_ligacao_id,
                    tc.tpcseguradora,
                    atc.atcdt_inicio dt_inicio_protocolo,
                    atc.atcdt_fim dt_fim_protocolo, 
                    ec.eqcdescricao classe_equipamento,
                    ec.eqcoid id_classe_equipamento,
                    eveversao versao_equipamento,
                    ag.agroid id_motivo_nivel1,                                
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm_pai.atmoid
                    ELSE
                        atm.atmoid 
                    END AS id_motivo_nivel2,
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm.atmoid
                    END AS id_motivo_nivel3,
                    ag.agrdescricao motivo_nivel1,
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm_pai.atmoid
                    ELSE
                        atm.atmoid 
                    END AS id_motivo_nivel2,
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm.atmoid
                    END AS id_motivo_nivel3,
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm_pai.atmdescricao
                    ELSE
                        atm.atmdescricao 
                    END AS motivo_nivel2,
                    CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                        atm.atmdescricao 
                    END AS motivo_nivel3,
                    atc.atcdt_inicio as data_hora_inicio_atendimento,
                    atc.atcdt_fim as data_hora_fim_atendimento	
                FROM atendimento_cliente atc
                LEFT JOIN 
                   (
                    SELECT ac.atcprotoid as tab_protoid, ac.atcclioid as tab_clioid, COUNT(*) as tab_qtde
                                   FROM atendimento_cliente ac
                                   WHERE ac.atcdt_fim IS NULL                                     
                                     AND ac.atcprotoid IS NOT NULL
                                   GROUP BY ac.atcprotoid, ac.atcclioid
                                   ) t ON (t.tab_protoid = atc.atcprotoid AND t.tab_clioid = atc.atcclioid)                  
                               INNER JOIN atendimento_motivo atm ON atm.atmoid = atc.atcatmoid
                               LEFT JOIN atendimento_motivo atm_pai ON atm_pai.atmoid = atm.atmoid_pai
                               LEFT JOIN atendimento_grupo ag ON atm.atmagroid = ag.agroid OR atm_pai.atmagroid = ag.agroid           
                               INNER JOIN usuarios u ON atc.atcusuoid = u.cd_usuario            
                               LEFT JOIN clientes c ON atc.atcclioid = c.clioid
                               LEFT JOIN cliente_classe clic ON c.cliclicloid = clic.clicloid   
                               LEFT JOIN protocolo_ura pr ON atc.atcprotoid = pr.protoid  
                               LEFT JOIN atendimento_acesso ac ON (ac.ataatcoid = atc.atcoid)
                               LEFT JOIN contrato co ON (ac.ataconoid = co.connumero)
                               LEFT JOIN tipo_contrato tc ON (tc.tpcoid = co.conno_tipo)
                               LEFT JOIN veiculo v ON (v.veioid = ac.ataveioid)
                               LEFT JOIN equipamento e ON conequoid = equoid 
                               LEFT JOIN equipamento_versao ev ON equeveoid = eveoid 
                               LEFT JOIN equipamento_classe ec ON eqcoid = coneqcoid

                    $filtro
                GROUP BY atc.atcoid, pr.protoid, c.clioid, clic.clicldescricao, ag.agrdescricao, atm.atmoid_pai
                , atm_pai.atmdescricao, atm.atmdescricao, u.ds_login, tc.tpcdescricao, v.veiplaca, t.tab_qtde
                ,ac.atatipo_ligacao, tc.tpcseguradora, ec.eqcdescricao,ec.eqcoid,ev.eveversao,ag.agroid,atm_pai.atmoid,atm.atmoid
                ORDER BY pr.protoid, c.clioid, atc.atcdt_inicio;
                $limit_numero_resultados
        ";

        $rs = pg_query($this->conn, $sql);

        $resultado = array();

        $contador_ativas = 0;
        $contador_retorno = 0;
        $contador_receptivas = 0;
        $contador_outros = 0;
        $contador = 0;

        $contador_clientes = 0;
        $contador_seguradora = 0;

        $total_versao = 0;
        $total_equipamento = 0;
        $contator_protocolos_tratados = 0;
        $contador_protocolos_pendentes = 0;
        $contador_protocolos_pendentes_ate_3_dias = 0;
        $contador_protocolos_pendentes_entre_3_6_dias = 0;
        $contador_protocolos_pendentes_mais_6_dias = 0;
        $contador_protocolos_concluidos = 0;
        $contador_protocolos_concluidos_ate_3_dias = 0;
        $contador_protocolos_concluidos_entre_3_6_dias = 0;
        $contador_protocolos_concluidos_mais_6_dias = 0;

        $resultado['tipo_ligacao'] = array();
        $resultado['classe_equipamento'] = array();
        $resultado['versao_equipamento'] = array();
        $resultado['motivos'] = array();
        $resultado['resultado_sintetico'] = array();

        /**
         * Os campos abaixo estão recebendo valor default, pois devem aparecer mesmo
         * que não haja resultados de retorno. 
         */
        $resultado['resultado_sintetico'][0]['label'] = 'Total de Protocolos Sascar Tratados';
        $resultado['resultado_sintetico'][0]['value'] = 0;

        $resultado['resultado_sintetico'][1]['label'] = 'Quantidade de Protocolos Pendentes';
        $resultado['resultado_sintetico'][1]['value'] = 0;

        $resultado['resultado_sintetico'][2]['label'] = 'Pendentes até 3 dias';
        $resultado['resultado_sintetico'][2]['value'] = 0;

        $resultado['resultado_sintetico'][3]['label'] = 'Pendentes entre 3 e 6 dias';
        $resultado['resultado_sintetico'][3]['value'] = 0;

        $resultado['resultado_sintetico'][4]['label'] = 'Pendentes a 6 ou mais dias';
        $resultado['resultado_sintetico'][4]['value'] = 0;

        $resultado['resultado_sintetico'][5]['label'] = 'Quantidade Protocolos Concluídos';
        $resultado['resultado_sintetico'][5]['value'] = 0;

        $resultado['resultado_sintetico'][6]['label'] = 'Concluídos até 3 dias';
        $resultado['resultado_sintetico'][6]['value'] = 0;

        $resultado['resultado_sintetico'][7]['label'] = 'Concluídos entre 3 e 6 dias';
        $resultado['resultado_sintetico'][7]['value'] = 0;

        $resultado['resultado_sintetico'][8]['label'] = 'Concluídos a 6 ou mais dias';
        $resultado['resultado_sintetico'][8]['value'] = 0;

        $resultado['total_ligacoes']['total'] = 0;

        $resultado['tipo_ligacao'][0]['label'] = 'Ligações Ativas';
        $resultado['tipo_ligacao'][0]['value'] = 0;

        $resultado['tipo_ligacao'][1]['label'] = 'Ligações Receptivas';
        $resultado['tipo_ligacao'][1]['value'] = 0;

        $resultado['tipo_ligacao'][2]['label'] = 'Ligações Retorno';
        $resultado['tipo_ligacao'][2]['value'] = 0;

        $resultado['tipo_ligacao'][3]['label'] = 'Outros canais de comunicação (Nextel, Email)';
        $resultado['tipo_ligacao'][3]['value'] = 0;

        $resultado['tipo_ligacao'][4]['label'] = 'Ligações Tipo de Contrato Clientes (Todos)';
        $resultado['tipo_ligacao'][4]['value'] = 0;

        $resultado['tipo_ligacao'][5]['label'] = 'Ligações Tipo de Contrato Seguradora (Todos)';
        $resultado['tipo_ligacao'][5]['value'] = 0;

        $resultado['clientes'] = array();

        $resultado['nome_usuario'] = $this->getNomeUsuario($id_usuario);
        $protocolo_atual = 0;
        $protocolo_anterior = 0;

        for ($i = 0; $i < pg_num_rows($rs); $i++) {

            $contador++;

            $nome_cliente = pg_fetch_result($rs, $i, 'nome_cliente');
            $id_cliente = pg_fetch_result($rs, $i, 'id_cliente');

            $resultado['clientes'][$id_cliente] = $nome_cliente;

            if (pg_fetch_result($rs, $i, 'tipo_ligacao_id') == 1) {
                $contador_ativas++;
            }

            if (pg_fetch_result($rs, $i, 'tipo_ligacao_id') == 2) {
                $contador_receptivas++;
            }

            if (pg_fetch_result($rs, $i, 'tipo_ligacao_id') == 3) {
                $contador_retorno++;
            }

            if (pg_fetch_result($rs, $i, 'tipo_ligacao_id') == 4) {
                $contador_outros++;
            }

            if (pg_fetch_result($rs, $i, 'tpcseguradora') == 'f') {
                $contador_clientes++;
            }

            if (pg_fetch_result($rs, $i, 'tpcseguradora') == 't') {
                $contador_seguradora++;
            }

            $protocolo_concluido = pg_fetch_result($rs, $i, 'protocolo_concluido') == 't' ? true : false;
            $id_protocolo = pg_fetch_result($rs, $i, 'id_protocolo');
            $dt_inicio_protocolo = pg_fetch_result($rs, $i, 'dt_inicio_protocolo');
            $dt_fim_protocolo = pg_fetch_result($rs, $i, 'dt_fim_protocolo');
            $protocolo_atual = $id_protocolo;


            $resultado['total_ligacoes']['total'] = $contador;
            $resultado['tipo_ligacao'][0]['value'] = $contador_ativas;
            $resultado['tipo_ligacao'][1]['value'] = $contador_receptivas;
            $resultado['tipo_ligacao'][2]['value'] = $contador_retorno;
            $resultado['tipo_ligacao'][3]['value'] = $contador_outros;
            $resultado['tipo_ligacao'][4]['value'] = $contador_clientes;
            $resultado['tipo_ligacao'][5]['value'] = $contador_seguradora;

            if (pg_fetch_result($rs, $i, 'classe_equipamento')) {
                $total_equipamento++;
            }

            $classe_equipamento = pg_fetch_result($rs, $i, 'classe_equipamento');
            $id_classe_equipamento = pg_fetch_result($rs, $i, 'id_classe_equipamento');

            if (!empty($id_classe_equipamento)) {
                $resultado['classe_equipamento'][$classe_equipamento][] = pg_fetch_result($rs, $i, 'id_atendimento');
            }

            $resultado['total_classe_equipamento']['total'] = $total_equipamento;

            $versao = pg_fetch_result($rs, $i, 'versao_equipamento');

            if (!empty($versao)) {
                $total_versao++;
                $resultado['versao_equipamento'][pg_fetch_result($rs, $i, 'versao_equipamento')][] = pg_fetch_result($rs, $i, 'id_atendimento');
                $resultado['total_versao_equipamento']['total'] = $total_versao;
            }

            /**
             * Aqui temos um agrupamento de motivos, precisamos saber quantas ocorrencias
             * aconteceram para o mesmo grupo de motivos, exemplo:
             * 
             * ATENDIMENTO DE PÂNICO, ALERTA E CERCA / Acionamento desconsiderado não tarifar Veículo / Abertura de Ordem de Serviço
             * 
             * Para isso cria uma chave composta pelos IDs dos motivo, ou seja:
             * 
             * 1248358 --- Junção dos IDs
             * 
             * Com isso colocamos todas as ocorrencias agrupadas por chave de array
             * assim quando contarmos a chave saberemos quantas ocorrencias os motivos 
             * tiveram.
             * 
             */
            $id_motivo_nivel1 = pg_fetch_result($rs, $i, 'id_motivo_nivel1');
            $id_motivo_nivel2 = pg_fetch_result($rs, $i, 'id_motivo_nivel2');
            $id_motivo_nivel3 = pg_fetch_result($rs, $i, 'id_motivo_nivel3');

            $motivo_nivel1 = pg_fetch_result($rs, $i, 'motivo_nivel1');
            $motivo_nivel2 = pg_fetch_result($rs, $i, 'motivo_nivel2');
            $motivo_nivel3 = pg_fetch_result($rs, $i, 'motivo_nivel3');

            $chave_motivo['id_motivo'] = $id_motivo_nivel1;
            $chave_motivo['id_motivo'] .= (!empty($id_motivo_nivel2)) ? $id_motivo_nivel2 : '';
            $chave_motivo['id_motivo'] .= (!empty($id_motivo_nivel3)) ? $id_motivo_nivel3 : '';

            $chave_motivo['motivo'] = (!empty($motivo_nivel1)) ? $motivo_nivel1 . (!empty($motivo_nivel2) ? ' / ' : '') : '';
            $chave_motivo['motivo'] .= (!empty($motivo_nivel2)) ? $motivo_nivel2 . (!empty($motivo_nivel3) ? ' / ' : '') : '';
            $chave_motivo['motivo'] .= (!empty($motivo_nivel3)) ? $motivo_nivel3 : '';

            $resultado['motivos'][$chave_motivo['id_motivo']][] = $chave_motivo['motivo'];

            //PROTOCOLOS TRATADOS
            if (!empty($id_protocolo) && $protocolo_anterior != $protocolo_atual) {
                $contator_protocolos_tratados++;
            }

            //PROTOCOLOS PENDENTES
            if (!empty($id_protocolo) && $protocolo_anterior != $protocolo_atual && !$protocolo_concluido) {
                $contador_protocolos_pendentes++;
            }

            /*
             * Cálculo para saber a diferença em dias entre a data de hoje e a data de inicio do atendimento
             */
            if (!empty($id_protocolo)) {
                $diferenca_entre_hoje_e_inicio_atendimento = (((strtotime(date('Y-m-d')) - strtotime($dt_inicio_protocolo)) / 3600) / 24);
            }

            //PROTOCOLOS ATE 3 DIAS PENDENTES
            if (!empty($id_protocolo) && $protocolo_anterior != $protocolo_atual && !$protocolo_concluido) {
                if ($diferenca_entre_hoje_e_inicio_atendimento <= 3) {
                    $contador_protocolos_pendentes_ate_3_dias++;
                }
            }

            //PROTOCOLOS ENTRE 3 E 6 DIAS PENDENTES
            if (!empty($id_protocolo) && $protocolo_anterior != $protocolo_atual && !$protocolo_concluido) {
                if ($diferenca_entre_hoje_e_inicio_atendimento > 3 && $diferenca_entre_hoje_e_inicio_atendimento <= 6) {
                    $contador_protocolos_pendentes_entre_3_6_dias++;
                }
            }

            //PROTOCOLOS MAIS DE 6 DIAS PENDENTES
            if (!empty($id_protocolo) && $protocolo_anterior != $protocolo_atual && !$protocolo_concluido) {
                if ($diferenca_entre_hoje_e_inicio_atendimento > 6) {
                    $contador_protocolos_pendentes_mais_6_dias++;
                }
            }

            //PROTOCOLOS CONCLUIDOS
            if ($protocolo_concluido && $protocolo_anterior != $protocolo_atual) {
                $contador_protocolos_concluidos++;
            }

            /*
             * Cálculo para saber a diferença em dias entre a data do fim do atendimento e a data de inicio do atendimento
             */
            if (!empty($id_protocolo) && $protocolo_concluido) {
                $diferenca_entre_inicio_e_fim_atendimento = (((strtotime($dt_fim_protocolo) - strtotime($dt_inicio_protocolo)) / 3600) / 24);
            }

            //PROTOCOLOS CONCLUIDOS ATE 3 DIAS
            if ($protocolo_concluido && $protocolo_anterior != $protocolo_atual) {
                if ($diferenca_entre_inicio_e_fim_atendimento <= 3) {
                    $contador_protocolos_concluidos_ate_3_dias++;
                }
            }


            //PROTOCOLOS CONCLUIDOS ENTRE 3 E 6 DIAS
            if ($protocolo_concluido && $protocolo_anterior != $protocolo_atual) {
                if ($diferenca_entre_inicio_e_fim_atendimento > 3 && $diferenca_entre_inicio_e_fim_atendimento <= 6) {
                    $contador_protocolos_concluidos_entre_3_6_dias++;
                }
            }

            //PROTOCOLOS CONCLUIDOS MAIS 6 DIAS
            if ($protocolo_concluido && $protocolo_anterior != $protocolo_atual) {
                if ($diferenca_entre_inicio_e_fim_atendimento > 6) {
                    $contador_protocolos_concluidos_mais_6_dias++;
                }
            }

            $protocolo_anterior = $id_protocolo;

            $resultado['resultado_sintetico'][0]['value'] = $contator_protocolos_tratados;
            $resultado['resultado_sintetico'][1]['value'] = $contador_protocolos_pendentes;
            $resultado['resultado_sintetico'][2]['value'] = $contador_protocolos_pendentes_ate_3_dias;
            $resultado['resultado_sintetico'][3]['value'] = $contador_protocolos_pendentes_entre_3_6_dias;
            $resultado['resultado_sintetico'][4]['value'] = $contador_protocolos_pendentes_mais_6_dias;
            $resultado['resultado_sintetico'][5]['value'] = $contador_protocolos_concluidos;
            $resultado['resultado_sintetico'][6]['value'] = $contador_protocolos_concluidos_ate_3_dias;
            $resultado['resultado_sintetico'][7]['value'] = $contador_protocolos_concluidos_entre_3_6_dias;
            $resultado['resultado_sintetico'][8]['value'] = $contador_protocolos_concluidos_mais_6_dias;

            /**
             * O bloco abaixo é utilizado apenas para o relatório data_hora
             */
            $data_inicio_atendimento = date('d/m/Y', strtotime(pg_fetch_result($rs, $i, 'data_hora_inicio_atendimento')));
            $data_inicio_atendimento_chave = date('Ymd', strtotime(pg_fetch_result($rs, $i, 'data_hora_inicio_atendimento')));
            $hora_inicio_atendimento = date('H', strtotime(pg_fetch_result($rs, $i, 'data_hora_inicio_atendimento')));

            /**
             * Agrupamos o array pelas data, hora e chave do agrupamento de motivos.
             * Exemplo:
             * 
             * $resultado['resultado_data_hora']['20120901']['horas']['15'][1248358] 
             */
            $resultado['resultado_data_hora'][$data_inicio_atendimento_chave]['data'] = $data_inicio_atendimento;
            $resultado['resultado_data_hora'][$data_inicio_atendimento_chave]['horas'][$hora_inicio_atendimento][$chave_motivo['id_motivo']]['label'] = $chave_motivo['motivo'];
            $resultado['resultado_data_hora'][$data_inicio_atendimento_chave]['horas'][$hora_inicio_atendimento][$chave_motivo['id_motivo']]['registros'][] = $chave_motivo['motivo'];

            $resultado['resultado_data_hora'][$data_inicio_atendimento_chave]['motivos'][$chave_motivo['id_motivo']]['label'] = $chave_motivo['motivo'];
            $resultado['resultado_data_hora'][$data_inicio_atendimento_chave]['motivos'][$chave_motivo['id_motivo']]['registros'][] = $chave_motivo['motivo'];
        }

        return $resultado;
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param string $filtro Cláusulas SQL
     * @param string $limit_numero_resultados limitador da consulta
     */
    public function getResultAnalitico($filtro, $limit_numero_resultados, $options) {

        $sql = "
			SELECT      
                atc.atcoid as id_atendimento,
                atc.atcdt_inicio as data_hora,
                atc.atcdt_fim as data_hora_fim,
                pr.protoid AS id_protocolo,
                pr.protprotocolo as protocolo_sascar,
                pr.protprotocolo_vivo as protocolo_vivo, 
                pr.protopcao_selecionada_ura as opcao_selecionada_ura, 
                pr.protdescricao_retorno_ura as retorno_ura,
                pr.protsegmento_cliente as segmento_cliente,
                pr.protsituacao_financeira as situacao_financeira,
                c.clioid AS id_cliente,
                c.clinome as nome_cliente,
                clic.clicldescricao as classe_cliente,
                (
                    SELECT 
                        CASE
                            WHEN atatipo_ligacao = 0
                                THEN 'Sem ligação'
                            WHEN atatipo_ligacao = 1
                                THEN 'Ativa'
                            WHEN atatipo_ligacao = 2
                                THEN 'Receptiva'
                            WHEN atatipo_ligacao = 3
                                THEN 'Retorno'
                            WHEN atatipo_ligacao = 4
                                THEN 'Outros Canais'
                        END
                    FROM
                        atendimento_acesso
                    WHERE
                        ataatcoid = atc.atcoid
                    ORDER BY 
                        ataoid DESC
                    LIMIT 1
                ) AS tipo_ligacao,  
                EXTRACT (EPOCH FROM AGE(TO_CHAR(COALESCE(atcdt_fim, atcdt_inicio), 'YYYY-MM-DD HH24:MI:SS')::timestamp, TO_CHAR(atcdt_inicio, 'YYYY-MM-DD HH24:MI:SS')::timestamp)) AS tempo_atendimento,
                ag.agrdescricao AS motivo_nivel1,                                
                CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                    atm_pai.atmdescricao
                ELSE
                    atm.atmdescricao 
                END AS motivo_nivel2,
                CASE WHEN atm.atmoid_pai IS NOT NULL THEN 
                    atm.atmdescricao 
                END AS motivo_nivel3,                          
                CASE 
                    WHEN atc.atcdt_fim IS NULL THEN 
                        'Pendente'
                    ELSE 
                        'Concluído'
                END AS status_atendimento,
                u.ds_login AS atendente,
                tc.tpcdescricao as tipo_contrato,
                v.veiplaca as placa,
                COALESCE(t.tab_qtde, 0) as total_atendimento_cliente,
                CASE WHEN pr.protoid IS NULL  
                                                       THEN ''
                            WHEN COALESCE(t.tab_qtde, 0) > 0
                                                       THEN 'PENDENTE'
                           ELSE 'CONCLUÍDO'
                               END as status_protocolo_sascar,
                               CASE  
                                 WHEN pr.protoid IS NULL OR COALESCE(t.tab_qtde, 0) > 0
                                   THEN 0
                                 WHEN COALESCE(t.tab_qtde, 0) = 0
                                   THEN (EXTRACT (EPOCH FROM SUM(age(TO_CHAR(atc.atcdt_fim, 'YYYY-MM-DD HH24:MI:SS')::timestamp, TO_CHAR(atc.atcdt_inicio, 'YYYY-MM-DD HH24:MI:SS')::timestamp)) OVER (PARTITION BY pr.protoid) )) 
                               END as tempo_resolucao                                                                                                                 
            FROM atendimento_cliente atc
            LEFT JOIN 
               (
                SELECT ac.atcprotoid as tab_protoid, ac.atcclioid as tab_clioid, COUNT(*) as tab_qtde
                               FROM atendimento_cliente ac
                               WHERE ac.atcdt_fim IS NULL                                     
                                 AND ac.atcprotoid IS NOT NULL
                               GROUP BY ac.atcprotoid, ac.atcclioid
                               ) t ON (t.tab_protoid = atc.atcprotoid AND t.tab_clioid = atc.atcclioid)                  
                           INNER JOIN atendimento_motivo atm ON atm.atmoid = atc.atcatmoid
                           LEFT JOIN atendimento_motivo atm_pai ON atm_pai.atmoid = atm.atmoid_pai
                           LEFT JOIN atendimento_grupo ag ON atm.atmagroid = ag.agroid OR atm_pai.atmagroid = ag.agroid           
                           INNER JOIN usuarios u ON atc.atcusuoid = u.cd_usuario            
                           LEFT JOIN clientes c ON atc.atcclioid = c.clioid
                           LEFT JOIN cliente_classe clic ON c.cliclicloid = clic.clicloid   
                           LEFT JOIN protocolo_ura pr ON atc.atcprotoid = pr.protoid  
                           LEFT JOIN atendimento_acesso ac ON (ac.ataatcoid = atc.atcoid)
                           LEFT JOIN contrato co ON (ac.ataconoid = co.connumero)
                           LEFT JOIN tipo_contrato tc ON (tc.tpcoid = co.conno_tipo)
                           LEFT JOIN veiculo v ON (v.veioid = ac.ataveioid)

                $filtro
            GROUP BY atc.atcoid, pr.protoid, c.clioid, clic.clicldescricao, ag.agrdescricao, atm.atmoid_pai
            , atm_pai.atmdescricao, atm.atmdescricao, u.ds_login, tc.tpcdescricao, v.veiplaca, t.tab_qtde
            ORDER BY pr.protoid, c.clioid, atc.atcdt_inicio;
            $limit_numero_resultados
		";

        $rs = pg_query($this->conn, $sql);
        $resultado = array();
        $tempoTotalResolucaoTimestamp = array();
        $tempoTotalAtendimentoTimestamp = array();

        for ($i = 0; $i < pg_num_rows($rs); $i++) {

            $id_atendimento = pg_fetch_result($rs, $i, 'id_atendimento');
            $id_cliente = pg_fetch_result($rs, $i, 'id_cliente');
            $protocolo_sascar = pg_fetch_result($rs, $i, 'protocolo_sascar');
            $id_protocolo = pg_fetch_result($rs, $i, 'id_protocolo');
            $tempo_atendimento = pg_fetch_result($rs, $i, 'tempo_atendimento');
            $tempo_resolucao = pg_fetch_result($rs, $i, 'tempo_resolucao');

            $chave = !empty($id_protocolo) ? $id_protocolo : $id_cliente;

            $resultado[$chave][$id_atendimento]['id_atendimento'] = $id_atendimento;
            $resultado[$chave][$id_atendimento]['data_hora'] = date('d/m/Y H:i', strtotime(pg_fetch_result($rs, $i, 'data_hora')));
            $resultado[$chave][$id_atendimento]['data_inicio_atendimento'] = date('Y-m-d H:i:s', strtotime(pg_fetch_result($rs, $i, 'data_hora')));
            $resultado[$chave][$id_atendimento]['data_fim_atendimento'] = date('Y-m-d H:i:s', strtotime(pg_fetch_result($rs, $i, 'data_hora_fim')));
            $resultado[$chave][$id_atendimento]['protocolo_sascar'] = $protocolo_sascar;
            $resultado[$chave][$id_atendimento]['protocolo_vivo'] = pg_fetch_result($rs, $i, 'protocolo_vivo');
            $resultado[$chave][$id_atendimento]['status_protocolo_sascar'] = pg_fetch_result($rs, $i, 'status_protocolo_sascar');
            $resultado[$chave][$id_atendimento]['nome_cliente'] = pg_fetch_result($rs, $i, 'nome_cliente');
            $resultado[$chave][$id_atendimento]['classe_cliente'] = pg_fetch_result($rs, $i, 'classe_cliente');
            $resultado[$chave][$id_atendimento]['tipo_ligacao'] = pg_fetch_result($rs, $i, 'tipo_ligacao');
            $resultado[$chave][$id_atendimento]['motivo_nivel1'] = pg_fetch_result($rs, $i, 'motivo_nivel1');
            $resultado[$chave][$id_atendimento]['motivo_nivel2'] = pg_fetch_result($rs, $i, 'motivo_nivel2');
            $resultado[$chave][$id_atendimento]['motivo_nivel3'] = pg_fetch_result($rs, $i, 'motivo_nivel3');
            $resultado[$chave][$id_atendimento]['status_atendimento'] = pg_fetch_result($rs, $i, 'status_atendimento');
            $resultado[$chave][$id_atendimento]['atendente'] = pg_fetch_result($rs, $i, 'atendente');
            $resultado[$chave][$id_atendimento]['placa'] = pg_fetch_result($rs, $i, 'placa');
            $resultado[$chave][$id_atendimento]['tipo_contrato'] = pg_fetch_result($rs, $i, 'tipo_contrato');
            $resultado[$chave][$id_atendimento]['tempo_atendimento'] = $this->timestampToMinutes($tempo_atendimento);
            $resultado[$chave][$id_atendimento]['tempo_resolucao'] = $this->timestampToHours($tempo_resolucao);
            $resultado[$chave][$id_atendimento]['opcao_selecionada_ura'] = pg_fetch_result($rs, $i, 'opcao_selecionada_ura');
            $resultado[$chave][$id_atendimento]['retorno_ura'] = pg_fetch_result($rs, $i, 'retorno_ura');
            $resultado[$chave][$id_atendimento]['segmento_cliente'] = pg_fetch_result($rs, $i, 'segmento_cliente');
            $resultado[$chave][$id_atendimento]['situacao_financeira'] = pg_fetch_result($rs, $i, 'situacao_financeira');

            if (!isset($tempoTotalAtendimentoTimestamp[$chave])) {
                $tempoTotalAtendimentoTimestamp[$chave] = 0;
            }

            $tempoTotalResolucaoTimestamp[$chave] = $tempo_resolucao;
            $tempoTotalAtendimentoTimestamp[$chave] += $tempo_atendimento;
        }

        $sumTempoTotalResolucaoTimestamp = array_sum(array_values($tempoTotalResolucaoTimestamp));
        $sumTempoTotalAtendimentoTimestamp = array_sum(array_values($tempoTotalAtendimentoTimestamp));

        $total_em_minutos = $this->timestampToMinutes($sumTempoTotalAtendimentoTimestamp);
        $total_em_horas = $this->timestampToHours($sumTempoTotalResolucaoTimestamp);

        foreach ($tempoTotalAtendimentoTimestamp as $chave => $tempo) {
            $tempoTotalProtocoloMinutos[$chave] = str_pad(floor($tempo / 60), 2, '0', STR_PAD_LEFT) . ':';
            $tempoTotalProtocoloMinutos[$chave] .= str_pad(round($tempo % 60), 2, '0', STR_PAD_LEFT);
        }

        return array(
            'resultados' => $resultado,
            'total_em_horas' => $total_em_horas,
            'total_em_minutos' => $total_em_minutos,
            'tempo_total_protocolo_minutos' => $tempoTotalProtocoloMinutos
        );
    }

    private function timestampToMinutes($timestamp) {
        $minutes = str_pad(floor($timestamp / 60), 2, '0', STR_PAD_LEFT) . ':';
        $minutes .= str_pad(round($timestamp % 60), 2, '0', STR_PAD_LEFT);

        return $minutes;
    }

    private function timestampToHours($timestamp) {
        $hours = str_pad(floor($timestamp / 3600), 2, '0', STR_PAD_LEFT) . ':';
        $hours .= str_pad(round(($timestamp % 3600) / 60), 2, '0', STR_PAD_LEFT);

        return $hours;
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param int $id_usuario ID do usuário a ser filtrado
     */
    private function getNomeUsuario($id_usuario) {

        $sql = "
			SELECT
				nm_usuario
			FROM
				usuarios
			WHERE
				cd_usuario = $id_usuario";
        $rs = pg_query($this->conn, $sql);

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'nm_usuario');
        }

        return null;
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Retorna um array para preencher a combo de atendentes
     */
    public function getComboAtendentes() {

        $sql = "
            SELECT 
                cd_usuario, 
                nm_usuario 
            FROM 
                usuarios 
            WHERE 
                dt_exclusao IS NULL 
            AND 
                usudepoid IN (8,20,26,36,59,182,453,454,455,457,458,459,545,553,554,544) 
            ORDER BY 
                nm_usuario";

        $rs = pg_query($this->conn, $sql);

        return pg_fetch_all($rs);
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Retorna um array para preencher a combo Classe Cliente
     */
    public function getComboClienteClasse() {
        $sql = "
            SELECT 
                clicloid, 
                clicldescricao 
            FROM 
                cliente_classe 
            WHERE
                clicldt_exclusao IS NULL
            ORDER BY 
                clicldescricao";

        $rs = pg_query($this->conn, $sql);

        return pg_fetch_all($rs);
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Retorna um array para preencher a combo Tipo Contrato
     */
    public function getComboTipoContrato() {
        $sql = "
            SELECT 
                tpcoid, 
                tpcdescricao 
            FROM 
                tipo_contrato 
            WHERE 
                tpcativo = 't' 
            ORDER BY 
                tpcdescricao
            ";

        $rs = pg_query($this->conn, $sql);

        return pg_fetch_all($rs);
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Retorna um array para preencher a combo Motivo Nível 1
     */
    public function getComboMotivoNivel1() {
        $sql = "
            SELECT 
                agroid, 
                agrdescricao 
            FROM 
                atendimento_grupo 
            WHERE 
                agrexclusao IS NULL 
            ORDER BY 
                agrdescricao
            ";

        $rs = pg_query($this->conn, $sql);

        return pg_fetch_all($rs);
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Retorna um array para preencher a combo Motivo Nível 2
     */
    public function getComboMotivoNivel2($grupo_id) {
        $sql = "
            SELECT 
                atmoid, 
                atmdescricao 
            FROM 
                atendimento_motivo
            INNER JOIN
                atendimento_grupo ON agroid = atmagroid
            WHERE 
                atmoid_pai IS NULL 
            AND 
                atmagroid IS NOT NULL 
            AND
                atmexclusao IS NULL
            AND 
                atmagroid = $grupo_id
            ORDER BY 
                atmdescricao
            ";

        $rs = pg_query($this->conn, $sql);

        $result = array();

        for ($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['atmoid'] = pg_fetch_result($rs, $i, 'atmoid');
            $result[$i]['atmdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'atmdescricao'));
        }

        return $result;
    }

    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Retorna um array para preencher a combo Motivo Nível 3
     */
    public function getComboMotivoNivel3($motivo_pai_id) {
        $sql = "
            SELECT 
                atmoid, 
                atmdescricao 
            FROM 
                atendimento_motivo 
            WHERE 
                atmoid_pai IS NOT NULL 
            AND 
                atmexclusao IS NULL
            AND
                atmoid_pai = $motivo_pai_id
            ORDER BY 
                atmdescricao";

        $rs = pg_query($this->conn, $sql);

        $result = array();

        for ($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['atmoid'] = pg_fetch_result($rs, $i, 'atmoid');
            $result[$i]['atmdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'atmdescricao'));
        }

        return $result;
    }

    public function RelAtendimentoFrontEndDAO() {

        global $conn;

        $this->conn = $conn;
    }

}
