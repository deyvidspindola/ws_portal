<?php

/**
 * Classe RelInstalacaoEquipamentoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Manutencao
 * @author   Ricardo Bonfim <ricardo.bonfim@meta.com.br>
 *
 */
class RelInstalacaoEquipamentoDAO {

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

    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }

    /**
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function buscarDadosInstalacaoEquipamento(stdClass $parametros) {

        $retorno = array();

        $sql = "
            select * from (
                select
                    entnota as nota,
                    to_char(entdt_entrada,'dd/mm/yyyy') as dt_entrada,
                    entserie as serie_nota,
                    to_char(entdt_emissao,'dd/mm/yyyy') as dt_emissao,
                    clinome as cliente, 
                    itlnome as instalador,
                    repnome as representante, 
                    repcgc as cnpj,
					equno_serie::varchar as nr_serie,
                    equpatrimonio::varchar as nr_patrimonio,
                    cmiord_serv::varchar as nr_os,
                    connumero::varchar as nr_contrato,
                    equprdoid::varchar as cd_produto,
                    prdproduto as ds_produto,
                    COALESCE( (SELECT count(*) FROM ordem_servico_visita_improdutiva WHERE oviordoid = cmiord_serv), 0 )::varchar AS visitas_improdutivas,
                    cmiobs_motivo as motivo,
                    to_char(cmipagamento,'dd/mm/yyyy') as pagamento,
                    to_char(cmidata,'dd/mm/yyyy')  as dt_conclusao_os,
                    tpcdescricao as tipo_contrato,
                    eqcdescricao as classe,
                    to_char(enttotal, '999999999D99')  as valor_total_nota,          
                    COALESCE(cmideslocamento, 0) AS qtd_deslocamento,
                    to_char(COALESCE(cmivl_unit_deslocamento, 0), '999999999D99') AS valor_deslocamento,
                    to_char(COALESCE(cmivl_unit_deslocamento, 0) * COALESCE(cmideslocamento, 0), '999999999D99') AS valor_total_deslocamento,
                    to_char(COALESCE(cmidesconto, 0), '999999999D99') AS valor_desconto,
                    to_char(COALESCE(cmivalor_pedagio, 0), '999999999D99') AS valor_pedagio,
                    to_char(( COALESCE(cmicomissao, 0) - COALESCE(cmidesconto, 0) ), '999999999D99') AS valor_comissao,
                    to_char(( COALESCE(cmicomissao, 0) + COALESCE(cmivalor_pedagio, 0) + ( COALESCE(cmivl_unit_deslocamento, 0) * COALESCE(cmideslocamento, 0) ) ), '999999999D99') AS valor_total_servico,
                    ostdescricao  as tipo_os
                from
                    clientes
                join
                    contrato on conclioid=clioid
                join
                    tipo_contrato on conno_tipo=tpcoid 
                join
                    comissao_instalacao ci on ci.cmiconoid=connumero 
                join
                    instalador on ci.cmiitloid=itloid 
                join
                    representante on itlrepoid=repoid 
                join
                    ordem_servico on ci.cmiord_serv = ordoid
                join
                    os_tipo_item on cmiotioid=otioid 
                join
                    os_tipo on ostoid = otiostoid 
                join
                    fornecedores on fordocto = repcgc
                join
                    entrada e on entforoid = foroid and entexclusao is null
                and
                    entdt_emissao = (select e_.entdt_emissao from entrada e_ where e_.entforoid = e.entforoid and e_.entexclusao is null and e_.entdt_emissao >= ci.cmipagamento order by 1 limit 1) 
                join
                    equipamento_classe on coneqcoid=eqcoid 
                left join
                    equipamento on equoid = ordequoid
                left join
                    produto on equprdoid = prdoid
                left join
                    veiculo on veioid = conveioid
                WHERE
                    1 = 1";
                
                if(isset($parametros->tipoPesquisa) && trim($parametros->tipoPesquisa) != '') {
                    $sql .= " AND
                                " . pg_escape_string($parametros->tipoPesquisa) . " BETWEEN '" . pg_escape_string($parametros->dataInicial) . "' AND '" . pg_escape_string($parametros->dataFinal) . "'";
                }

                if(isset($parametros->nomeFornecedor) && trim($parametros->nomeFornecedor) != '') {
                    $sql .= " AND
                                forfornecedor ilike '%" . pg_escape_string($parametros->nomeFornecedor) . "%'";
                }

                if(isset($parametros->tipoServico) && trim($parametros->tipoServico) != '') {
                    $sql .= " AND
                                ostoid = " . intval($parametros->tipoServico);
                }

                if(isset($parametros->tipoInstalacao) && trim($parametros->tipoInstalacao) != '') {
                    $sql .= " AND
                                tpcoid = " . intval($parametros->tipoInstalacao);
                }
                         
                        
                $sql .=" UNION ALL

                SELECT 
                    entnota as nota,
                    to_char(entdt_entrada,'dd/mm/yyyy') as dt_entrada,
                    entserie as serie_nota,
                    to_char(entdt_emissao,'dd/mm/yyyy') as dt_emissao,
                    '' as cliente, 
                    '' as instalador,
                    repnome as representante,
                    repcgc as cnpj,
                    '' as nr_serie,
                    '' as nr_patrimonio,
                    '' as nr_os,
                    '' as nr_contrato,
                    '' as cd_produto,
                    'Pagamento para testes realizados' as ds_produto,
                    '' as visitas_improdutivas,
                    cmiobs_motivo as motivo_visita,
                    to_char(cmipagamento,'dd/mm/yyyy') as pagamento,
                    ''  as dt_conclusao_os,
                    '' as tipo_contrato,
                    '' as classe,
                    to_char(enttotal, '999999999D99')  as valor_total_nota, 
                    COALESCE(cmideslocamento, 0) AS qtd_deslocamento,
                    to_char(COALESCE(cmivl_unit_deslocamento, 0), '999999999D99') AS valor_deslocamento,
                    to_char(COALESCE(cmivl_unit_deslocamento, 0) * COALESCE(cmideslocamento, 0), '999999999D99') AS valor_total_deslocamento,
                    to_char(COALESCE(cmidesconto, 0), '999999999D99') AS valor_desconto,
                    to_char(COALESCE(cmivalor_pedagio, 0), '999999999D99') AS valor_pedagio,
                    to_char(( COALESCE(coalesce(sum(ci.cmicomissao), 0), 0) - COALESCE(cmidesconto, 0) ), '999999999D99') AS valor_comissao,
                    to_char(( COALESCE(coalesce(sum(ci.cmicomissao), 0), 0) + COALESCE(cmivalor_pedagio, 0) + ( COALESCE(cmivl_unit_deslocamento, 0) * COALESCE(cmideslocamento, 0) ) ), '999999999D99') AS valor_total_servico,
                    ostdescricao  as tipo_os
                FROM
                    comissao_instalacao ci
                join
                    representante on cmirepoid=repoid 
                join
                    fornecedores on fordocto = repcgc
                join
                    entrada e on entforoid = foroid 
                and
                    entexclusao is null
                and
                    entdt_emissao = (select e_.entdt_emissao from entrada e_ 
                            where e_.entforoid = e.entforoid
                            and e_.entexclusao is null 
                            and e_.entdt_emissao >= ci.cmipagamento 
                            order by 1 limit 1)
                join
                    os_tipo_item on cmiotioid=otioid 
                join
                    os_tipo on ostoid = otiostoid               
                WHERE
                    1 = 1
                and
                    ci.cmipagamento is not null 
                and
                    ci.cmiexclusao is null";

                if (isset($parametros->tipoPesquisa) && trim($parametros->tipoPesquisa) != '') {
                    $sql .= " AND
                                " . pg_escape_string($parametros->tipoPesquisa) . " BETWEEN '" . pg_escape_string($parametros->dataInicial) . "' AND '" . pg_escape_string($parametros->dataFinal) . "'";
                }

                if (isset($parametros->nomeFornecedor) && trim($parametros->nomeFornecedor) != '') {
                    $sql .= " AND
                                forfornecedor ilike '%" . pg_escape_string($parametros->nomeFornecedor) . "%'";
                }

                if (isset($parametros->tipoServico) && trim($parametros->tipoServico) != '') {
                    $sql .= " AND
                                ostoid = " . intval($parametros->tipoServico);
                }

                $sql .= " group by 
                    entnota,
                    entdt_entrada,
                    entserie,
                    entdt_emissao,
                    repcgc,
                    cmiobs_motivo,
                    cmipagamento,
                    enttotal,
                    ci.cmideslocamento,
                    ci.cmivl_unit_deslocamento,
                    ci.cmidesconto,
                    ci.cmivalor_pedagio,
                    os_tipo.ostdescricao,
                    repnome) as q
                order by 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_assoc($rs)){
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarTiposServico() {

        $retorno = array();

        $sql = "
            SELECT
                *
            FROM
                os_tipo
            WHERE
                ostdt_exclusao IS NULL
            ORDER BY
                ostdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            while ($linha = pg_fetch_object($rs)) {
                array_push($retorno, $linha);
            }
        }

        return $retorno;
    }

    public function buscarTiposInstalacao() {

        $retorno = array();

        $sql = "
            SELECT
                tpcoid,
                tpcdescricao
            FROM
                tipo_contrato
            ORDER BY
                tpcdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            while ($linha = pg_fetch_object($rs)) {
                array_push($retorno, $linha);
            }
        }

        return $retorno;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}