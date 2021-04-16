<?php

/**
 * Classe SincronizacaoLegadoDAO.
 * Sincroniza agendamentos futuros feitos pelo modolu do agendamento antigo
 * Cria atividades no OFSC para agendamentos realizados no modulo antigo
 *
 * @package  SmartAgenda
 * @author   Luiz Pontara
 *
 */
 require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class SincronizacaoLegadoDAO extends DAO{

    /**
     * Consulta agendamentos pendentes que não possuem atividade no OFSC
     * @return array
     */
    public function pesquisar($filtros){

        $retorno = array();

        $prestadores = explode(',', $filtros->prestadores);
        foreach ($prestadores as $chave => $valor) {
            $aux[] = (int) $valor;
        }
        $prestadores = implode(",", $aux);


        $dataInicial = DateTime::createFromFormat('d/m/Y', $filtros->cmp_data_inicio);
        $dataFinal   = DateTime::createFromFormat('d/m/Y', $filtros->cmp_data_fim);

        $sql = " SELECT 
                    osa.osaoid,
                    osa.osaordoid,
                    ord.ordconnumero,
                    osa.osadata,
                    osa.osahora,
                    osa.osaitloid,
                    inst.itlrepoid,
                    --osa.osarepoid,
                    --osa.osaid_atividade,
                    --osa.osaemergencial,
                    agc.agccodigo,
                    --osa.osacep,
                    --osa.osaestoid,
                    --osa.osaclcoid,
                    --osa.osacbaoid,
                    --osa.osaend_complemento,
                    --osa.osaend_referencia,
                    osi.osiptref,
                    osi.osiresponsavel,
                    osa.osatipo_atendimento,
                    osa.osatipo_agendamento,
                    osa.osacadastro,
                    osa.osaobservacao
                FROM 
                    ordem_servico_agenda AS osa
                INNER JOIN
                    ordem_servico AS ord ON ord.ordoid = osa.osaordoid
                INNER JOIN
                    equipamento_classe AS eqc ON eqc.eqcoid = ord.ordeqcoid
                INNER JOIN
                    agrupamento_classe AS agc ON agc.agcoid = eqc.eqcagcoid
                INNER JOIN
                    contrato AS con ON ord.ordconnumero = con.connumero
                INNER JOIN
                    ordem_servico_inst AS osi ON osi.osiordoid = ord.ordoid
                INNER JOIN
                    instalador AS inst ON inst.itloid = osa.osaitloid
                WHERE 
                    EXISTS (SELECT 1 FROM ordem_servico_item AS osi WHERE osi.ositordoid = ord.ordoid AND osi.ositstatus != 'X')
                AND
                    con.condt_exclusao IS NULL
                AND
                    (ord.ordfrota IS NULL OR ord.ordfrota = FALSE)
                AND
                    ord.ordstatus = ANY( string_to_array((SELECT
                                                            pcsidescricao
                                                        FROM
                                                            parametros_configuracoes_sistemas_itens
                                                        WHERE
                                                            pcsipcsoid = 'SMART_AGENDA'
                                                        AND
                                                            pcsioid = 'STATUS_OS_PESQUISA'),',')::integer[])
                AND
                    osaexclusao IS NULL
                AND
                    osaordoid IS NOT NULL
                AND 
                    osaid_atividade IS NULL
                AND
                    osadata BETWEEN '" . $dataInicial->format('Y-m-d') . "' AND '" . $dataFinal->format('Y-m-d') . "'";

        // somente prestadores Hortosat e 
        if($prestadores){

            $sql .=" AND
                        inst.itlrepoid IN ($prestadores)";
        }else{

            $sql .=" AND
                        inst.itlrepoid NOT IN (217,1714)";
        }

        $sql .=" ORDER BY
                    osaordoid, osadata, osahora";

        //echo "<pre>";var_dump($sql);echo "</pre>";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs)){
            while($registro = pg_fetch_object($rs)){

                //retorna sem duplicar a OS
                if($os !== $registro->osaordoid){
                    $retorno[] = $registro;
                    $os = $registro->osaordoid;
                }
            }
        }
        return $retorno;
    }

    /**
     * Recuperar os dados para enviar email e SMS
     *
     * @param int $ordoid
     * @throws Exception
     * @return multitype:
     */
    public function getDadosEmailSms($ordoid){

        $sql = "SELECT 
                    osccnome, 
                    osecemail, 
                    oscccelular
                FROM 
                    ordem_servico_celular_contato
                INNER JOIN 
                    ordem_servico_email_contato ON osecordoid = osccordoid
                WHERE   
                    osccordoid = $ordoid 
                LIMIT 1" ;

        if (!$query = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao recuperar dados de Email e SMS.');
        }
        return pg_num_rows($query) ? pg_fetch_array($query, 0, PGSQL_ASSOC) : array();
    }


    /**
     * Utiliza mesma query feita na tela de Ordem Servico
     * @param  [type] $idRepresentante [description]
     * @return [type]               [description]
     */
    public function enderecoPontoFixo($idRepresentante){

        $sql = "SELECT
                    DISTINCT
                    repnome AS empresa,
                    endvrua,
                    endvnumero,
                    endvcomplemento,
                    clcuf_sg,
                    clcestoid,
                    clcoid,
                    clcnome,
                    cbaoid,
                    cbanome,
                    endvcep AS cep
                FROM
                    REPRESENTANTE
                INNER JOIN
                    ENDERECO_REPRESENTANTE ON endvrepoid = repoid
                INNER JOIN
                    correios_localidades ON UPPER(clcuf_sg) = UPPER(endvuf) AND UPPER(clcnome) = UPPER(endvcidade)
                INNER JOIN
                    correios_bairros ON UPPER(cbanome) ILIKE UPPER(endvbairro) AND clcoid = cbaclcoid
                WHERE repoid = " . (int) $idRepresentante;

        $rs = pg_query($this->conn, $sql);

        $retorno = array();
        if(pg_num_rows($rs)){
            while($registro = pg_fetch_object($rs)){
                $retorno[] = $registro;
            }
        }

        return $retorno;

    }

}
