<?php

require_once _MODULEDIR_ . 'core/infra/autoload.php';

use module\RoteadorBoleto\RoteadorBoletoService as RoteadorBoleto;
use module\BoletoRegistrado\BoletoRegistradoModel as BoletoRegistradoModel;
use module\TituloCobranca\TituloCobrancaModel as TituloCobrancaModel;
use infra\Helper\Response;

class FinRescisaoDAO2
{

    public $erro_registro = null;
    protected $_adapter;
    public $titulosBaixaParcial = array();

    /**
     * "Constrói" o objeto DAO.
     *
     * @return Void
     */

    public function __construct()
    {
        global $conn;
        $this->_adapter = $conn;
    }

    /**
     * Executa uma query
     * @param   string      $sql        SQL a ser executado
     * @return  resource
     */
    public function _query($sql)
    {
        // echo $sql;
        return pg_query($this->_adapter, $sql);
    }

    /**
     * Conta os resultados de uma consulta
     * @param   resource    $results
     * @return  int
     */
    protected function _count($results)
    {
        return pg_num_rows($results);
    }

    /**
     * Retorna os resultados de uma consulta num array associativo (hash-like)
     * @param   resource    $results
     * @return  array
     */
    protected function _fetchAll($results)
    {
        return pg_fetch_all($results);
    }

    /**
     * Retorna o resultado de uma coluna num array associativo (hash-like)
     * @param   resource    $results
     * @return  array
     */
    protected function _fetchAssoc($result)
    {
        return pg_fetch_assoc($result);
    }

    /**
     * Insere valores numa tabela
     * @param   string  $table
     * @param   array   $values
     * @return  boolean
     */
    protected function _insert($table, $arr)
    {
        return pg_insert($this->_adapter, $table, $arr);
    }

    /**
     * Escapa os elementos de um vetor
     * @param   array   $arr
     * @return  array
     */
    protected function _escapeArray($arr)
    {
        array_walk($arr, function(&$item, $key) {
            $item = pg_escape_string($item);
        });

        return $arr;
    }

    /**
     * Constrói um vetor do Postgres para inserção
     */
    protected function _buildPgArray($arr, $index)
    {
        if (isset($arr[$index]))
        {
            $elm = $arr[$index];

            if (isset($elm) && is_array($elm))
            {
                return "'{" . implode(',', $elm) . "}'";
            }
            elseif (strlen($elm))
            {
                return "'{" . $elm . "}'";
            }
        }

        return 'NULL';
    }

    /**
     * Remove valores "vazios" dos filtros (0).
     * @param   array   $filters
     * @return  array
     */
    protected function _clearFilters($filters)
    {
        $clearedFilters = array();
        foreach ($filters as $key => $value)
        {
            if (!$value || $value == '0')
            {
                continue;
            }

            $clearedFilters[$key] = $value;
        }

        return $clearedFilters;
    }

    /**
     * Busca as rescisões com base nos filtros informados.
     * É a mesma consulta presente na antiga tela.
     * @TODO: otimizar consulta
     *
     * @param   array   $filters
     * @return  array
     */
    public function buscarRescisoes($filters)
    {
        // Remove filtros com valor 0
        $filters = $this->_clearFilters($filters);

        // Fix para status inexistente
        $filters['status'] = (isset($filters['status'])) ? $filters['status'] : null;

        $sql = "SELECT
                    resmoid
                  , rescoid
                  , TO_CHAR(resmcadastro,'dd/mm/yyyy') AS rescisao
                  , clinome
                  , connumero
                  , mrescdescricao
                  , conno_tipo
                  , conveioid AS veioid
                  , condt_exclusao
                  , rescstatus
                  , conrepcomissaooid
                  , usurefoid
                  , clioid
                  , ds_login
                  , CASE WHEN clitipo = 'F' THEN
                        cliuf_res
                    ELSE
                        cliuf_com
                    END AS uf
                  , (   CASE WHEN ( SELECT nfloid
                                    FROM nota_fiscal
                                    INNER JOIN nota_fiscal_item ON nfino_numero = nflno_numero AND nfiserie=nflserie
                                    -- [START]['ORGMKTOTVS-1986'] - Leandro Corso
                                    LEFT JOIN titulo ON titnfloid = nfloid
                                    -- [END]['ORGMKTOTVS-1986'] - Leandro Corso
                                    JOIN forma_cobranca ON titformacobranca = forcoid
                                    WHERE nficonoid=rescconoid
                                    AND titdt_vencimento < NOW()::date
                                    AND titdt_pagamento IS NULL
                                    AND titdt_cancelamento IS NULL
                                    AND forccobranca IS TRUE
                                    AND nfiobroid<>26
                                    LIMIT 1)>0 THEN
                            'X'
                        END) AS pendente
                    , (   CASE WHEN ( SELECT nfloid
                                    FROM nota_fiscal
                                    INNER JOIN nota_fiscal_item ON nfino_numero = nflno_numero AND nfiserie=nflserie
                                    -- [START]['ORGMKTOTVS-1986'] - Leandro Corso
                                    LEFT JOIN titulo ON titnfloid = nfloid
                                    -- [END]['ORGMKTOTVS-1986'] - Leandro Corso
                                    JOIN forma_cobranca ON titformacobranca = forcoid
                                    WHERE nficonoid=rescconoid
                                    AND titdt_vencimento < NOW()::date
                                    AND titdt_pagamento IS NOT NULL
                                    AND titdt_cancelamento IS NULL
                                    AND forccobranca IS TRUE
                                    AND nfiobroid=26
                                    LIMIT 1)>0 THEN
                            'PAGO'
                        ELSE
                            'PENDENTE'
                        END) AS retirada
                    , (   SELECT veiplaca
                        FROM veiculo
                        WHERE conveioid = veioid) AS veiplaca
                    , CASE WHEN clitipo = 'F' THEN
                        clicidade_res
                      ELSE
                        clicidade_com
                      END AS cidade
                    , CASE WHEN clitipo = 'F' THEN
                        clifone_res
                      ELSE
                        clifone_com
                      END AS fone
                    , (   SELECT equno_serie
                        FROM equipamento
                        WHERE equoid = conequoid) AS equno_serie
                    , (   SELECT ordoid
                        FROM ordem_servico
                        WHERE ordconnumero = connumero
                        AND (ordmtioid = 5 OR ( SELECT count(*)
                                                FROM ordem_servico_item
                                                WHERE ositotioid = 8
                                                AND ositexclusao IS NULL)>0)
                        AND orddt_ordem>=resmcadastro
                        ORDER BY orddt_ordem
                        LIMIT 1) AS ord_serv
                    , (   SELECT
                        TO_CHAR( MAX(coddt_cadastro),'dd/mm/yyyy')
                        FROM contrato_declaracao
                        WHERE codconoid = connumero
                        OR codveioid=conveioid ) AS declaracao
                    FROM rescisao
                    INNER JOIN rescisao_mae    ON rescresmoid = resmoid
                    INNER JOIN motivo_rescisao ON mrescoid    = resmmrescoid
                    INNER JOIN usuarios        ON cd_usuario  = resmusuoid
                    INNER JOIN clientes        ON clioid      = resmclioid
                    INNER JOIN contrato        ON connumero   = rescconoid";


                    // Filtra pela data de rescisão
                    if (isset($filters['data_inicial'])
                        && isset($filters['data_final'])
                        && in_array($filters['status'], array('R', 'C', 'S')))
                    {
                        $sql .= " , rescisao_historico ";
                    }

                    // Filtra pela placa do veículo
                    if (isset($filters['placa']))
                    {
                        $sql .= " , veiculo ";
                    }

                    $sql .= " WHERE resmexclusao IS NULL ";

                    // Filtra pelo nome do cliente
                    if (isset($filters['cliente']))
                    {
                        $sql .= " AND clinome ILIKE '%{$filters['cliente']}%' ";
                    }

                    // Filtra pelo número do termo (contrato)
                    if (isset($filters['termo']))
                    {
                        $sql .= " AND connumero = {$filters['termo']} ";
                    }

                    // Filtra pela classe do termo
                    if (isset($filters['classe_termo']))
                    {
                        $sql .= " AND coneqcoid = {$filters['classe_termo']} ";
                    }

                    // Filtra pela placa
                    if (isset($filters['placa']))
                    {
                        $sql .= "    AND conveioid = veioid
                                     AND veiplaca  = '{$filters['placa']}' ";
                    }

                    // Filtra pelo tipo do motivo
                    if (isset($filters['motivo']))
                    {
                        $sql .= " AND mrescoid = {$filters['motivo']} ";
                    }

                    // Filtra pelo status da rescisão
                    if (isset($filters['status'])
                        && !in_array($filters['status'], array('R', 'C', 'S')))
                    {
                        $sql .= " AND rescstatus = '{$filters['status']}' ";
                    }

                    if (isset($filters['data_inicial'])
                        && isset($filters['data_final'])
                        && !in_array($filters['status'], array('R', 'C', 'S')))
                    {
                        $sql .= " AND resmcadastro BETWEEN '{$filters['data_inicial']} 00:00:00' AND '{$filters['data_final']} 23:59:59' ";
                    }

                    if (isset($filters['data_inicial'])
                        && isset($filters['data_final'])
                        && $filters['status'] == 'R')
                    {
                        $sql .= "    AND reschoid=rescoid
                                        AND rescstatus='R'
                                        AND rescenv_retirada BETWEEN '{$filters['data_inicial']}
                                        00:00:00' AND '{$filters['data_final']} 23:59:59' ";
                    }

                     if (isset($filters['data_inicial'])
                        && isset($filters['data_final'])
                        && $filters['status'] == 'C')
                    {
                        $sql .= "    AND reschoid=rescoid
                                        AND rescstatus='R'
                                        AND rescenv_retirada BETWEEN '{$filters['data_inicial']}
                                        00:00:00' AND '{$filters['data_final']} 23:59:59'
                                        AND connumero IN (  SELECT ordconnumero
                                                            FROM ordem_servico
                                                            WHERE ordconnumero=connumero
                                                            AND (ordmtioid=5 OR (   SELECT count(*)
                                                                                    FROM ordem_servico_item
                                                                                    WHERE ositotioid = 8
                                                                                    AND ositexclusao IS NULL)>0)
                                                            ORDER BY orddt_ordem DESC
                                                            LIMIT 1) ";
                    }

                    if (isset($filters['data_inicial'])
                        && isset($filters['data_final'])
                        && $filters['status'] == 'S')
                    {
                        $sql .= "    AND reschoid=rescoid
                                        AND rescstatus='R'
                                        AND rescenv_retirada BETWEEN '{$filters['data_inicial']}
                                        00:00:00' AND '{$filters['data_final']} 23:59:59'
                                        AND connumero NOT IN (  SELECT ordconnumero
                                                                FROM ordem_servico
                                                                WHERE ordconnumero=connumero
                                                                AND (ordmtioid=5 OR (   SELECT count(*)
                                                                                        FROM ordem_servico_item
                                                                                        WHERE ositotioid = 8
                                                                                        AND ositexclusao IS NULL)>0)
                                                                ORDER BY orddt_ordem DESC
                                                                LIMIT 1)  ";
                    }

                    // Filtra pelo valor total da rescisão
                    if (isset($filters['valor_inicial'])
                        && isset($filters['valor_final'])
                        && intval($filters['valor_final']) > 0)
                    {
                        // Corrige notação de exibição para notação do PHP/PG
                        $valorInicial = str_replace(',', '.', str_replace('.', '', $filters['valor_inicial']));
                        $valorFinal = str_replace(',', '.', str_replace('.', '', $filters['valor_final']));

                        $sql .= " AND resmvl_total BETWEEN '{$valorInicial}' AND '{$valorFinal}' ";
                    }

                    // Filtra pelo tipo de contrato
                    if (isset($filters['contratos']))
                    {
                        if ($filters['contratos'] == 'C')
                        {
                            $sql .= " AND (condt_exclusao IS NOT NULL OR conno_tipo = 14) ";
                        }
                        elseif ($filters['contratos'] == 'A')
                        {
                            $sql .= " AND (condt_exclusao IS NULL AND conno_tipo <> 14) ";
                        }
                    }

                    // Filtra pela UF
                    if (isset($filters['uf']))
                    {
                        $sql .= " AND (cliuf_res = '{$filters['uf']}' OR cliuf_com = '{$filters['uf']}') ";
                    }

                    $sql .= " ORDER BY uf, cidade, clinome"; 

        $query = $this->_query($sql);

        if ($query)
        {
            return $this->_fetchAll($query);
        }

        return false;
    }

    /**
     * Retorna a marca e modelo de um veículo com base em seu ID
     * @param   int     $veioid
     * @return  string
     */
    public function getNomeVeiculo($veioid)
    {
        if (!$veioid)
        {
            return '';
        }

        $sql = "SELECT
                    mcamarca
                  , mlomodelo
                FROM
                    veiculo
                INNER JOIN modelo ON veimlooid = mlooid
                INNER JOIN marca  ON mlomcaoid = mcaoid
                WHERE
                    veioid = {$veioid}";

        $result = $this->_fetchAssoc($this->_query($sql));

        if ($result)
        {
            return $result['mcamarca'] . ' - ' . $result['mlomodelo'];
        }

        return '';
    }

    /**
     * Retorna o login do último responsável da rescisão
     * @param   int     $resmoid
     * @return  string
     */
    public function getNomeResponsavel($resmoid)
    {
        $sql = "SELECT
                    ds_login
                FROM
                    usuarios
                INNER JOIN rescisao_historico ON reschoid = {$resmoid}
                WHERE
                    reschusuoid = cd_usuario
                ORDER BY
                    reschdata DESC
                LIMIT 1";

        $result = $this->_fetchAssoc($this->_query($sql));

        if ($result)
        {
            return $result['ds_login'];
        }

        return '';
    }

    /**
     * Busca usuários responsáveis
     * @return  array
     */
    public function findResponsaveis()
    {
        $sql = "SELECT
                    cd_usuario, ds_login
                FROM
                    usuarios
                WHERE
                    dt_exclusao IS NULL
                    AND usutipo <> 1
                ORDER BY
                    ds_login ASC";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca motivos de rescisão
     * @return  array
     */
    public function findMotivos()
    {
        $sql = "SELECT
                    mrescoid, mrescdescricao
                FROM
                    motivo_rescisao
                WHERE
                    mrescexclusao IS NULL
                ORDER BY
                    mrescdescricao";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca status de rescisão (estático)
     * @return  array
     */
    public function findStatusRescisao()
    {
        return array(
            'D' => 'Aguardando Depósito'
          , 'R' => 'Enviado para Retirada (Geral)'
          //, 'C' => 'Enviado para Retirada (c/ O.S.)'
          //, 'S' => 'Enviado para Retirada (s/ O.S.)'
          , 'A' => 'Arquivado'
          , 'E' => 'Recup. Equip. Obs.'
        );
    }

    /**
     * Busca tipos de contrato (estático)
     * @return  array
     */
    public function findTiposContrato()
    {
        return array(
            'A' => 'Ativos'
          , 'C' => 'Cancelados'
        );
    }

    /**
     * Busca UFs
     * @return  array
     */
    public function findUfs()
    {
        $sql = "SELECT
                    endvuf
                FROM
                    representante
                LEFT JOIN
                    endereco_representante ON endvrepoid = repoid
                WHERE
                    LENGTH(endvuf) = 2
                GROUP BY
                    endvuf
                ORDER BY
                    endvuf";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca classes de equipamento
     * @return  array
     */
    public function findClasseTermos()
    {
        $sql = "SELECT
                    eqcoid, eqcdescricao
                FROM
                    equipamento_classe
                ORDER BY
                    eqcdescricao";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca os dados de uma rescisão para exibição
     * @param   int     $resmoid
     * @return  array
     */
    public function findRescisao($resmoid)
    {
        $sql = "SELECT
                    resmcadastro
                  , resmvalidade
                  , (SELECT
                        rescvl_locacao
                     FROM rescisao
                     WHERE rescresmoid = resmoid
                     ORDER BY rescoid DESC
                     LIMIT 1) AS rescvl_locacao
                  , CASE WHEN resmstatus = 'A' THEN
                      'Arquivado'
                    WHEN resmstatus = 'D' THEN
                      'Aguardando Depósito'
                    WHEN resmstatus = 'E' THEN
                      'Recup Equip Obs.'
                    WHEN resmstatus = 'R' THEN
                      'Enviado para Retirada'
                    ELSE
                      ' '
                    END AS resmstatus
                  , clinome
                  , clirua_res 
                  , clino_res
                  , clicompl_res
                  , clibairro_res
                  , clicidade_res
                  , cliuf_res
                  , nm_usuario
                  , mrescdescricao
                  , resmatt
                  , resmvl_total
                  , resmfax
                  , resmcarta
                  , resmvl_remocao
                  , resmvl_multa
                FROM rescisao_mae
                INNER JOIN motivo_rescisao ON resmmrescoid = mrescoid
                INNER JOIN usuarios        ON resmusuoid   = cd_usuario
                INNER JOIN clientes        ON resmclioid   = clioid
                WHERE resmoid = {$resmoid}";
                
                //echo $sql;
                //die();
                return $this->_fetchAssoc($this->_query($sql));
    }

    /**
     * Busca os contratos de uma rescisão
     * @param   int     $resmoid
     * @return  array
     */
    public function findContratosRescisao($resmoid)
    {
        
        
        $sql = "SELECT
                    rescoid
                  , connumero
                  , rescmeses
                  , rescperc_multa
                  , veiplaca
                  , rescvl_monitoramento
                  , rescvl_locacao
                  , rescperc_multa_locacao
                  , rescfax
                  , resmobs_carta
                  , rescvl_desconto_monitoramento
                  , rescvl_desconto_locacao
                FROM
                    rescisao
                INNER JOIN rescisao_mae ON rescresmoid = resmoid
                INNER JOIN contrato c   ON rescconoid  = connumero
                INNER JOIN veiculo      ON conveioid   = veioid
                WHERE
                    rescresmoid = {$resmoid}
                GROUP BY
                    rescoid
                  , connumero
                  , rescmeses
                  , rescperc_multa
                  , veiplaca
                  , rescvl_monitoramento
                  , rescvl_locacao
                  , rescperc_multa_locacao
                  , rescfax
                  , resmobs_carta
                  , rescvl_desconto_monitoramento
                  , rescvl_desconto_locacao";
        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Executa a exclusão lógica de uma rescisão
     * @param   int     $resmoid
     * @return  boolean
     */
    public function excluirRescisao($resmoid)
    {
        $codigoUsuario = $_SESSION['usuario']['oid'];

        $sql = "UPDATE
                    rescisao_mae
                SET
                    resmusuoid_exclusao = {$codigoUsuario}
                  , resmexclusao        = NOW()
                WHERE
                    resmoid = {$resmoid}";

        return $this->_query($sql);
    }

    /**
     * Busca as faturas dos contratos da rescisão
     * @param   int     $resmoid
     * @return  array
     */
    public function findFaturasContratosRecisao($resmoid)
    {
        
        $sql = "SELECT
                    titoid
                  , nflno_numero
                  , nflserie
                  , titvl_titulo
                  , titvl_desc_rescisao
                  , resciobservacao
                  , resmatt
                  , titdt_vencimento
                  , resmfax
                  , MAX(resmcarta) as resmcarta
                FROM titulo
                INNER JOIN forma_cobranca   ON titformacobranca = forcoid
                INNER JOIN nota_fiscal      ON titnfloid        = nfloid
                INNER JOIN nota_fiscal_item ON nflno_numero     = nfino_numero
                                               AND nflserie     = nfiserie
                INNER JOIN contrato         ON nficonoid        = connumero
                INNER JOIN clientes         ON nflclioid        = conclioid
                                               AND clioid       = conclioid
                INNER JOIN rescisao_item    ON rescititoid      = titoid
                INNER JOIN rescisao_mae     ON resmoid          = resciresmoid
                WHERE connumero IN (
                        SELECT
                            rescconoid
                        FROM
                            rescisao
                        WHERE
                            rescresmoid = {$resmoid}
                    )
                    AND nfldt_cancelamento IS NULL
                    AND titdt_cancelamento IS NULL
                    AND (forccobranca IS TRUE
                         OR (forcoid = 51
                             AND titdt_credito IS NOT NULL
                             AND titdt_pagamento IS NULL))
                    AND titclioid NOT IN ( 8374, 11842, 10625, 10797 )
                    AND nflserie IN ('A', 'F1', 'F2', 'SL', 'SM', 'F', 'SP', 'SB', 'FF', 'G', 'PF', 'SP')
                GROUP BY
                    titoid
                  , titdt_vencimento
                  , titvl_titulo
                  , clioid
                  , titvl_desc_rescisao
                  , resmatt
                  , resmfax
                  , resciobservacao
                  , nflno_numero
                  , nflserie
                ORDER BY
                    resciobservacao ASC";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca as multas de locação da rescisão
     * @param   int     $resmoid
     * @return  array
     */
    public function findMultasLocacaoRescisao($resmoid)
    {
        $sql = "SELECT
                    nflno_numero
                  , nflserie
                  , titvl_titulo
                  , resciobservacao
                  , MIN(titdt_vencimento) AS vencimento_inicial
                  , MAX(titdt_vencimento) AS vencimento_final
                  , titvl_desc_rescisao
                FROM rescisao_item
                INNER JOIN titulo       ON rescititoid = titoid
                INNER JOIN nota_fiscal  ON titnfloid   = nfloid
                WHERE
                    resciresmoid = $resmoid
                    AND nflserie IN ('A', 'F1', 'F2', 'SL', 'SM', 'F', 'SP', 'SB', 'FF', 'G', 'PF', 'SP')
                    AND nfldt_cancelamento is null
                GROUP BY
                    nflno_numero
                  , nflserie
                  , titvl_titulo
                  , titvl_desc_rescisao
                  , resciobservacao";

        return $this->_fetchAll($this->_query($sql));
    }

    // ORGMKTOTVS-3674] - CRIS
     /**
     * Busca os valores de monitoramento e locação + acessórios do contrato
     * @param   int     $connumero
     * @return  array
     */
    public function findValoresMonitoramentoLocacaoAcessorios($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0)
        {
            $where .= "AND connumero = {$filters['connumero']}";
        }

        $sql = "SELECT 
            connumero 
            , (SELECT cofvl_obrigacao FROM contrato_obrigacao_financeira WHERE cofconoid= connumero AND cofobroid = 1 AND cofdt_termino IS NULL) AS valor_monitoramento
            , cpagvl_servico AS valor_modulo
            , (SELECT SUM(consvalor) FROM contrato_servico WHERE consconoid = CONNUMERO AND (conssituacao = 'L' OR  conssituacao = 'C') AND consiexclusao IS NULL) AS valor_acessorio 
            FROM contrato 
            LEFT JOIN clientes ON clioid = conclioid 
            LEFT JOIN contrato_pagamento ON cpagconoid = connumero 
            LEFT JOIN cond_pgto_venda ON cpvoid = cpagcpvoid 
            LEFT JOIN contrato_servico ON consconoid = CONNUMERO 
            WHERE 
            connumero IN ({$filters['connumero']}) 
            group by 
            connumero, 
            clioid, 
            clinome,
            valor_monitoramento,
            valor_modulo,
            cpvparcela";

        return $this->_fetchAll($this->_query($sql));
    }
    
    // ORGMKTOTVS [3595] - Cris
     /**
     * Busca os valores de locação + acessórios faturados na ultima nota fiscal
     * @param   int     $connumero
     * @param   string  $dataPre
     * @return  array
     */
    public function buscarvaloresLocacaoUltimaNotaFaturada($filters, $dataPre) {

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0) {
            
            $whereObroid .= "AND consconoid = {$filters['connumero']}";
            $where .= "AND connumero = {$filters['connumero']}";
        }

        $obroids = array();
        
        if (!empty($filters['eqcobroid'])) {
            $obroids[] = $filters['eqcobroid']; // equipamento principal
        }

        // buscar as obrigações da locação de acessórios
        $sqlObroid = "SELECT "
                . "consobroid FROM contrato_servico "
                . "WHERE true "
                . " {$whereObroid}"
                . " AND (conssituacao = 'L' OR  conssituacao = 'C') AND consiexclusao IS null;";

        $result = $this->_fetchAll($this->_query($sqlObroid));

        if (!empty($result)) {
            foreach ($result as $obroid) {
                $obroids[] = $obroid['consobroid'];
            }
        }

        // ORGMKTOTVS [3863] - Cris
        // buscar a obrigação em caso de substituicao de contrato
        $sqlObroidSubs = "SELECT "
                        . "CASE WHEN "
                        . "( (SELECT COUNT(msuboid) as COUNT "
                        . "FROM motivo_substituicao "
                        . "INNER JOIN  obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig AND msubeqcoid = obreqcoid "
                        . "WHERE msubeqcoid IS NOT NULL AND  obrdt_exclusao IS NULL AND msuboid = con.conmsuboid limit 1) ) > 0 "
                        . "THEN "
                        . "(SELECT obroid "
                        . "FROM motivo_substituicao "
                        . "INNER JOIN obrigacao_financeira ON msubeqcoid_orig = obreqcoid_orig AND msubeqcoid = obreqcoid "
                        . "WHERE msubeqcoid is not null and obrdt_exclusao is null and msuboid = con.conmsuboid limit 1) "
                        . "ELSE CASE "
                        . "WHEN ( (SELECT COUNT(msuboid) as COUNT "
                        . "FROM motivo_substituicao  "
                        . "WHERE msuboid = con.conmsuboid AND msubeqcoid is null AND msubtrans_titularidade IS TRUE LIMIT 1) ) > 0 "
                        . "THEN 25 "
                        . "ELSE eqc.eqcobroid "
                        . "END "
                        . "END as obroid "
                        . "FROM contrato con "
                        . "INNER JOIN equipamento_classe eqc ON eqc.eqcoid = con.coneqcoid WHERE TRUE "
                        . $where ;
               
        $resultSubs = $this->_fetchAll($this->_query($sqlObroidSubs));

        if (!empty($resultSubs)) {
            foreach ($resultSubs as $obroidSub) {
                $obroids[] = $obroidSub['obroid'];
            }
        }

        $obroids = implode(', ', $obroids);
        
        $sql = "SELECT 
                    sum(nfivl_item) vl_locacao_faturado, 
                    nflno_numero 
                FROM
                    nota_fiscal 
                    INNER JOIN clientes ON clioid = nflclioid 
                    INNER JOIN contrato ON conclioid = clioid
                    INNER JOIN nota_fiscal_item on nfinfloid = nfloid AND connumero = nficonoid
                WHERE 
                    1 = 1
                    AND nfldt_cancelamento IS NULL
                    AND nfitipo = 'L'
                    AND nfiserie = 'A'
                    AND  nfiobroid in ({$obroids})
                    AND ((extract(month from nfldt_emissao) >= extract(month from '$dataPre'::date) and extract(year from nfldt_emissao) >= extract(year from '$dataPre'::date))
                    or ( (extract(month from nfldt_emissao) <= extract(month from '$dataPre'::date)and extract(year from nfldt_emissao) > extract(year from '$dataPre'::date))))
                    $where
                GROUP BY
                    nflno_numero,
                    nfldt_inclusao
                ORDER BY nfldt_inclusao DESC LIMIT 1;";
                
        return $this->_fetchAll($this->_query($sql));
    }
    
    // ORGMKTOTVS [3595] - Cris
     /**
     * Busca os valores de monitoramento faturados na ultima nota fiscal
     * @param   int     $connumero
     * @param   string  $dataPre
     * @return  array
     */
    public function buscarvaloresMonitoramentoUltimaNotaFaturada($filters, $dataPre) {

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0) {
         
            $where .= "AND connumero = {$filters['connumero']}";
        }

        $obroid = 1; // obrigacao de monitoramento (conforme o faturamento)
        
        $sql = "SELECT 
                    sum(nfivl_item) vl_monitoramento_faturado, 
                    nflno_numero 
                FROM
                    nota_fiscal 
                    INNER JOIN clientes ON clioid = nflclioid 
                    INNER JOIN contrato ON conclioid = clioid
                    INNER JOIN nota_fiscal_item on nfinfloid = nfloid AND connumero = nficonoid
                WHERE 
                    1 = 1
                    AND nfldt_cancelamento IS NULL
                    AND nfitipo = 'M'
                    AND nfiserie = 'A'
                    AND  nfiobroid = $obroid
                    AND ((extract(month from nfldt_emissao) >= extract(month from '$dataPre'::date) and extract(year from nfldt_emissao) >= extract(year from '$dataPre'::date))
                    or ( (extract(month from nfldt_emissao) <= extract(month from '$dataPre'::date)and extract(year from nfldt_emissao) > extract(year from '$dataPre'::date))))
                    $where
                GROUP BY
                    nflno_numero,
                    nfldt_inclusao
                ORDER BY nfldt_inclusao DESC LIMIT 1;";
                
        return $this->_fetchAll($this->_query($sql));
    }

    // ORGMKTOTVS [3903] - Cris
     /**
     * Busca os valores faturados na ultima nota fiscal
     * @param   int     $connumero
     * @return  array
     */
    public function buscarUltimaNotaFaturada($filters) {

        $where = "";

        // Filtra por número do contrato
        if (!empty($filters['connumero']) && $filters['connumero'] > 0) {
            
            $where .= "AND connumero = {$filters['connumero']}";
        }

        $sql = "SELECT
                    nficonoid,
                    nflno_numero,
                    (SELECT count (nfitipo) as faturado FROM nota_fiscal_item nfi WHERE nfi.nfino_numero=nflno_numero and nfi.nficonoid= ". $filters['connumero'] ." and nfitipo ='L') as faturado_locacao,
                    (SELECT count (nfitipo) as faturado FROM nota_fiscal_item nfi WHERE nfi.nfino_numero=nflno_numero and nfi.nficonoid= ". $filters['connumero'] ." and nfitipo ='M') as faturado_monitoramento
                FROM
                    nota_fiscal 
                    INNER JOIN clientes ON clioid = nflclioid 
                    INNER JOIN contrato ON conclioid = clioid
                    INNER JOIN nota_fiscal_item on nfinfloid = nfloid AND connumero = nficonoid
                WHERE 
                    1 = 1
                    AND nfldt_cancelamento IS NULL
                    AND nfiserie = 'A'
                    $where
                GROUP by
                        nficonoid,
                        nflno_numero,
                        nfldt_inclusao
                ORDER BY nfldt_inclusao DESC limit 1";

            return $this->_fetchAssoc($this->_query($sql));

      }

    /**
     * Busca as multas dos serviços da rescisão
     * @param   int     $resmoid
     * @return  array|boolean
     */
    public function findMultasServicosRescisao($resmoid)
    {
        $sql = "SELECT
                    veiplaca
                  , risdescr
                  , risqtde
                  , risvalor
                FROM
                    rescisao_item_serv
                INNER JOIN veiculo ON risveioid = veioid
                WHERE
                    risresmoid = $resmoid
                ORDER BY
                    veiplaca
                  , risdescr";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca as taxas de retirada de equipamento da rescisão
     * @param   int     $resmoid
     * @return  array|boolean
     */
    public function findTaxasRetiradaRescisao($resmoid)
    {
        $sql = "SELECT
                    obrobrigacao
                  , rescrvl_retirada
                  , rescrconoid
                FROM rescisao_retirada
                INNER JOIN obrigacao_financeira ON rescrobr_servico=obroid
                WHERE
                    rescrresmoid    = {$resmoid}
                    AND rescrcobrar = 't'
                GROUP BY
                    obrobrigacao
                  , rescrvl_retirada
                  , rescrconoid
                ORDER BY
                    obrobrigacao
                  , rescrconoid";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca o histórico de uma rescisão
     * A query é estranhamente complicada, e não descobri o porque. @_@
     * @param   int     $resmoid
     * @return  array|boolean
     */
    public function findHistoricoRescisao($resmoid)
    {
        $sql = "SELECT
                    rescconoid
                  , reschdata
                  , reschobservacao
                  , ds_login
                  , reschstatus
                  , rescenv_retirada
                  , reschdata
                  , reschusuoid_inc
                  , (SELECT
                        ds_login
                    FROM usuarios
                    WHERE
                        cd_usuario = reschusuoid_inc) AS usuario
                FROM rescisao
                INNER JOIN rescisao_historico ON rescoid     = reschoid
                LEFT JOIN usuarios            ON reschusuoid = cd_usuario
                WHERE
                    rescoid IN (
                        SELECT
                            rescoid
                        FROM rescisao
                        INNER JOIN contrato ON rescconoid = connumero
                        WHERE connumero IN (
                            SELECT
                                rescconoid
                            FROM rescisao
                            WHERE
                                rescresmoid = {$resmoid}
                        )
                    )
                ORDER BY
                    reschdata DESC";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca a data da última pré-rescisão
     * @param   int     $clioid
     * @return  array|boolean
     */
    public function findDataSolicitacao($clioid)
    {
        $sql = "SELECT
                    prescadastro
                FROM
                    pre_rescisao
                WHERE
                    presclioid     = {$clioid}
                    AND presstatus = 'E'
                ORDER BY
                    prescadastro
                LIMIT 1";

        return $this->_fetchAssoc($this->_query($sql));
    }

    /**
     * Busca a data da última pré-rescisão de um contrato
     * @param   int     $connumero
     * @return  array|boolean
     */
    public function findDataSolicitacaoContrato($connumero)
    {
        $sql = "SELECT
                    prescadastro
                FROM
                    pre_rescisao
                WHERE
                    presconoid     = {$connumero}
                    AND presstatus = 'E'
                ORDER BY
                    prescadastro
                LIMIT 1";

        return $this->_fetchAssoc($this->_query($sql));
    }

    /**
     * Busca os contratos de um cliente para criação de nova rescisão
     * @TODO: otimizar consulta
     * @param   array   $filters
     * @return  array|boolean
     */
    public function findContratosNovaRescisao($filters)
    {
        $where = '';

        // Filtra por ID do cliente
        if (isset($filters['clioid']) && $filters['clioid'] > 0)
        {
            $where .= " AND clioid = {$filters['clioid']}";
        }

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0 && !is_array($filters['connumero']))
        {
            $where .= " AND connumero = {$filters['connumero']}";
            
        }
        // ORGMKTOTVS[3674] - Cris
        if (is_array($filters['connumero']))
        {
            $filters['connumero'] = implode(', ', $filters['connumero']);
             $where .= " AND connumero IN ({$filters['connumero']})";
        }
        
        $sql = "SELECT
                    connumero
                  , convl_modelo_contrato
                  , veiplaca
                  , tpcseguradora
                  , eqcdescricao
                  , eqcobroid
                  , condt_ini_vigencia
                  , conmsuboid
                  , msubdescricao
                  , connumero_antigo
                  , coneqcoid
                  , (SELECT
                        gctmeses
                    FROM
                        contrato_aditivo_item
                    INNER JOIN contrato_aditivo       ON cadioid = caicadioid
                    INNER JOIN gerador_contrato_texto ON gctoid  = cadigctoid
                    WHERE
                        cadidata_via IS NOT NULL
                        AND ( cadiassinado = 't'
                            OR cadiviaoriginal = 't'
                            OR cadiviafax = 't' )
                        AND gctmeses IS NOT NULL
                        AND caiconoid = connumero
                    ORDER BY
                        cadidata_via DESC
                    LIMIT 1) AS meses_aditivo
                  , conprazo_contrato
                  , TO_CHAR(prescadastro, 'DD/MM/YYYY') AS prescadastro
                FROM
                    contrato
                INNER JOIN clientes                      ON clioid     = conclioid
                INNER JOIN veiculo                       ON veioid     = conveioid
                INNER JOIN tipo_contrato                 ON tpcoid     = conno_tipo
                INNER JOIN equipamento_classe            ON coneqcoid  = eqcoid
                INNER JOIN contrato_obrigacao_financeira ON cofconoid  = connumero
                LEFT JOIN motivo_substituicao            ON msuboid    = conmsuboid
                LEFT JOIN pre_rescisao                   ON (
                        presconoid = connumero
                        AND presexclusao IS NULL
                        AND presstatus = 'E'
                )
                WHERE
                    condt_exclusao IS NULL
                    AND condt_ini_vigencia IS NOT NULL
                    AND conmodalidade = 'L'
                    AND cofobroid IN (1)
                    AND cofdt_termino IS NULL
                    AND eqcproj_245 IS NOT TRUE
                    AND NOT EXISTS (
                        SELECT
                            1
                        FROM
                            rescisao
                        INNER JOIN rescisao_mae ON resmoid = rescresmoid
                        WHERE
                            rescconoid = connumero
                            AND resmexclusao IS NULL
                    )
                    {$where}
                ORDER BY
                    connumero";
        return $this->_fetchAll($this->_query($sql));
    }


    // [ORGMKTOTVS-3675] - Cris
    public function verificaFidelizacao($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0)
        {
            $where .= "AND hfcconnumero = {$filters['connumero']}";
        }

        $sql = "SELECT  
                    hfcprazo, 
                    hfcdt_fidelizacao,
                    gctdescricao
                FROM  
                    historico_fidelizacao_contrato 
                    INNER JOIN  gerador_contrato_texto ON hfcgctoid = gctoid
                WHERE 
                    0 = 0 
                    {$where}
                ORDER BY 
                    hfcdt_fidelizacao DESC
                LIMIT 1 ";

        $retorno = $this->_fetchAll($this->_query($sql));

        return $retorno;
    }
    
    // [ORGMKTOTVS-3675] - Cris
    public function verificaTranferencia($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0)
        {
            $where .= "AND prptermo = {$filters['connumero']}";
        }

        $sql = "SELECT prpdt_aprovacao_fin as dt_vigencia_ultimo_contrato FROM proposta WHERE true {$where}";
                    
        $retorno = $this->_fetchAll($this->_query($sql));

        return $retorno;
    }
    
    // [ORGMKTOTVS-3675] - Cris
    public function verificaUpDown($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0)
        {
            $where .= "AND ordconnumero = {$filters['connumero']}";
        }

        $sql = "SELECT(SELECT orsdt_situacao              FROM
                        ordem_situacao
                    WHERE
                        orsordoid = ordoid
                    ORDER BY
                        orsdt_situacao DESC LIMIT 1) as dt_vigencia_ultimo_contrato
                FROM
                    ordem_servico
                INNER JOIN ordem_servico_status ON ordstatus = ossoid
                INNER JOIN contrato ON connumero = ordconnumero
                WHERE
                    ordstatus = 3 --CONCLUÍDO
                    AND ordmtioid in (2,10) and ordostoid = 1 --ordmtioid (motivo INSTALACAO OU UPGRADE/DOWNGRADE) e ordostoid (tipo-instalacao)
                    {$where}
                ";
         
        $retorno = $this->_fetchAll($this->_query($sql));

        return $retorno;
    }

    // [ORGMKTOTVS-3718] - Cris
    public function buscavalorTaxaRetirada($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0)
        {
            $where .= "AND c.connumero = {$filters['connumero']}";
        }

        $sql = "SELECT c.connumero,
                    ob.obrobroid_retirada,
                    (SELECT obrobrigacao FROM
                        obrigacao_financeira
                    WHERE
                        obroid = ob.obrobroid_retirada
                   ) as descricao_obrigacao,
                   (SELECT obrvl_obrigacao FROM
                        obrigacao_financeira
                    WHERE
                        obroid = ob.obrobroid_retirada
                   ) as taxa_retirada
                FROM contrato c
                INNER JOIN equipamento_classe eq on c.coneqcoid= eq.eqcoid
                INNER JOIN obrigacao_financeira  ob on eq.eqcobroid = ob.obroid
                WHERE true
                    {$where}
                ";
         
        $retorno = $this->_fetchAll($this->_query($sql));

        return $retorno;
    }

    // [ORGMKTOTVS-3718] - Cris
    public function buscaOFContratoTrcOsConcluido($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0)
        {
            $where .= "AND con.connumero = {$filters['connumero']}";
        }

            $sql = "SELECT cpt.cptobroid as nfiobroid
                        FROM 
                        comissao_instalacao cmi 
                        INNER JOIN contrato con ON cmi.cmiconoid = con.connumero 
                        INNER JOIN ordem_servico ord ON	ord.ordconnumero = con.connumero AND ord.ordstatus = 3 AND ord.ordostoid = 3 
                        INNER JOIN comissao_padrao_tecnica cpt ON cpt.cptotioid = cmi.cmiotioid and cpt.cpteqcoid = con.coneqcoid 
                        INNER JOIN obrigacao_financeira obr ON obr.obroid = cpt.cptobroid 
                        WHERE true
                        {$where}
                        AND cmi.cmiotioid = 11 
                        AND cpt.cptdt_exclusao IS NULL 
                        ORDER BY cmi.cmidata DESC
                        LIMIT 1;";
        $retorno = $this->_fetchAssoc($this->_query($sql));

        return $retorno;
    }
    
    // [ORGMKTOTVS-3718] - Cris
    public function buscaUltimoRPSContratoTRC($filters)
    {
        $retorno = array();
        $where = '';

        // Filtra por número do contrato
        if (isset($filters['connumero']) && $filters['connumero'] > 0 )
        {
            $where .= "AND nfi.nficonoid = {$filters['connumero']} ";
        }

        $sql = "SELECT nf.nfloid, nfiobroid, ob.obrobrigacao
                FROM
                    nota_fiscal nf
                INNER JOIN nota_fiscal_item nfi ON nfi.nfinfloid = nf.nfloid
                INNER JOIN obrigacao_financeira ob ON ob.obroid = nfi.nfiobroid
                WHERE true
                    AND nf.nfldt_cancelamento IS NULL
                    {$where}
                    AND nf.nfloid = (SELECT max(nfi2.nfinfloid) FROM nota_fiscal_item nfi2 WHERE nfi.nficonoid = nfi2.nficonoid);";
                 
        $retorno = $this->_fetchAll($this->_query($sql));

        return $retorno;
    }

    // [ORGMKTOTVS-3718] - Cris
    public function buscaIdTaxadeExtensao(){
        
        $sql = "SELECT
			pcsidescricao as nfiobroid
		FROM
			parametros_configuracoes_sistemas_itens
		WHERE
			pcsipcsoid = 'FATURAMENTO_TAXA_EXTENSAO'";

        $result = $this->_fetchAll($this->_query($sql));

        return $result;
    }

    /**
     * Busca título vigente referente a última cobrança de monitoramento
     * @param   string  $resmfax
     * @param   int     $connumero
     * @return  array
     */
    public function findTituloMonitoramentoContrato($resmfax, $connumero)
    {
        $sql = "SELECT
                   connumero
                 , (nflno_numero::text ||' - '|| nflserie) AS nota
                 , titdt_vencimento AS data_vencimento
                 , titdt_inclusao   AS data_inclusao
                 , nfivl_item       AS valor_monitoramento
                 , titdt_pagamento  AS data_pagamento
                 , CASE
                     WHEN
                       (titdt_pagamento IS NOT NULL) -- AND (titdt_pagamento >= '{$resmfax}'::date)
                     THEN
                       ROUND(((extract(day from '{$resmfax}'::date) / 30) * nfivl_item)::numeric , 2)
                     ELSE
                       0
                   END AS desconto_multa_monitoramento

                FROM contrato

                INNER JOIN
                   nota_fiscal_item
                ON nficonoid = connumero

                INNER JOIN
                   nota_fiscal
                ON nflno_numero = nfino_numero
                AND nflserie = nfiserie

                INNER JOIN
                   titulo
                ON titnfloid = nfloid

                WHERE
                    connumero IN ({$connumero})
                    --AND titdt_vencimento > '{$resmfax}'::date
                    AND titdt_cancelamento IS NULL
                    AND nfldt_cancelamento IS NULL
                    --AND titdt_inclusao <= '{$resmfax}'::date
                    AND nfiobroid = 1
                ORDER BY
                   titdt_inclusao DESC
                LIMIT 1";

        $result = $this->_fetchAssoc($this->_query($sql));

        if (!$result)
        {
            $sql = "SELECT
                       connumero
                     , (nflno_numero::text ||' - '|| nflserie) AS nota
                     , titdt_vencimento AS data_vencimento
                     , titdt_inclusao   AS data_inclusao
                     , nfivl_item       AS valor_monitoramento
                     , titdt_pagamento  AS data_pagamento
                     , CASE
                         WHEN
                           (titdt_pagamento IS NOT NULL) --AND (titdt_pagamento >= '{$resmfax}'::date)
                         THEN
                           ROUND(((extract(day from '{$resmfax}'::date) / 30) * nfivl_item)::numeric , 2)
                         ELSE
                           0
                       END AS desconto_multa_monitoramento

                    FROM contrato

                    INNER JOIN
                       nota_fiscal_item
                    ON nficonoid = connumero

                    INNER JOIN
                       nota_fiscal
                    ON nflno_numero = nfino_numero
                    AND nflserie = nfiserie

                    INNER JOIN
                       titulo
                    ON titnfloid = nfloid

                    WHERE
                        connumero IN ({$connumero})
                        AND titdt_cancelamento IS NULL
                        AND nfldt_cancelamento IS NULL
                        --AND titdt_inclusao <= '{$resmfax}'::date
                        AND nfiobroid = 1
                    ORDER BY
                       titdt_inclusao DESC
                    LIMIT 1";

            $result = $this->_fetchAssoc($this->_query($sql));
        }

        return $result;
    }

    /**
     * Busca as multas de locação associadas aos contratos
     * @TODO: otimizar consulta
     * @param   array   $filters
     * @return  array|boolean
     */
    public function findMultasLocacaoContratos($filters)
    {
        if (is_array($filters['connumero']))
        {
            $filters['connumero'] = implode(', ', $filters['connumero']);
        }

        $sql = "DROP TABLE IF EXISTS aux1;
                SELECT
                    nfloid
                  , nficonoid
                  , nfino_numero
                  , nfiserie
                  , nflvl_total
                  , ((SUM(nfivl_item) / nflvl_total ) * 100 )::numeric(10,2) AS nfivl_item
                  , conprazo_contrato
                INTO TEMP aux1
                FROM contrato
                INNER JOIN clientes         ON conclioid = clioid
                INNER JOIN nota_fiscal_item ON connumero = nficonoid
                INNER JOIN nota_fiscal      ON nflno_numero = nfino_numero AND nflserie = nfiserie
                WHERE
                    connumero IN ({$filters['connumero']})
                    AND nflserie IN ('F', 'SL')
                    AND nfldt_cancelamento IS NULL
                GROUP BY
                    nfloid
                  , nficonoid
                  , nfino_numero
                  , nfiserie
                  , nflvl_total
                  , conprazo_contrato
                ORDER BY
                    nficonoid;

                SELECT
                    nfloid
                  , nficonoid
                  , nfino_numero
                  , nfiserie
                  , nflvl_total
                  , nfivl_item
                  , titvl_titulo
                  , titdt_vencimento
                  , ((( nfivl_item/100 ) * nflvl_total ) / (
                        SELECT
                        CASE WHEN max(titno_parcela) > 0 THEN
                            MAX(titno_parcela)
                        ELSE
                            1
                        END AS titno_parcela
                        FROM titulo WHERE titnfloid = nfloid))::numeric(10,2) AS valor
                  , (SELECT
                        COALESCE(gctmeses, 0)
                    FROM
                        contrato_aditivo_item
                    INNER JOIN contrato_aditivo       ON cadioid = caicadioid
                    INNER JOIN gerador_contrato_texto ON gctoid  = cadigctoid
                    WHERE
                        cadidata_via IS NOT NULL
                        AND ( cadiassinado = 't'
                            OR cadiviaoriginal = 't'
                            OR cadiviafax = 't' )
                        AND gctmeses IS NOT NULL
                        AND caiconoid = nficonoid
                    ORDER BY
                        cadidata_via DESC
                    LIMIT 1 ) AS cond_pagamento1
                  , conprazo_contrato AS cond_pagamento2
                  , titoid
                FROM aux1
                INNER JOIN titulo         ON nfloid  = titnfloid
                INNER JOIN forma_cobranca ON forcoid = titformacobranca
                WHERE
                    titdt_cancelamento IS NULL
                    AND (forccobranca IS TRUE
                         OR (titformacobranca = 51
                             AND titdt_credito IS NOT NULL))
                    AND titnao_cobravel IS NOT TRUE
                    AND (titdt_pagamento IS NULL
                         OR (titdt_pagamento IS NOT NULL
                             AND titdt_vencimento > '{$filters['resmfax']}'::DATE))
                GROUP BY
                    nfloid
                  , nficonoid
                  , nfino_numero
                  , nfiserie
                  , nflvl_total
                  , nfivl_item
                  , titvl_titulo
                  , valor
                  , cond_pagamento1
                  , cond_pagamento2
                  , titdt_vencimento
                  , titoid
                ORDER BY
                    nficonoid
                  , nfino_numero
                  , nfiserie
                  , titdt_vencimento;";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca as faturas associadas aos contratos
     * @TODO: otimizar consulta
     * @param   array   $filters
     * @return  array|boolean
     */
    public function findFaturasContratos($filters)
    {
        if (is_array($filters['connumero']))
        {
            $filters['connumero'] = implode(', ', $filters['connumero']);
        }

        $sql = "SELECT
                subq_x.clioid,
                CASE
                    WHEN (subq_x.finalidade = 'Monitoramento' AND (TO_CHAR(subq_x.nfldt_emissao::DATE,'YYYYMM') < TO_CHAR(subq_x.titdt_vencimento,'YYYYMM')))
                    THEN
                        TO_CHAR(subq_x.nfldt_emissao::DATE, 'DD/MM/YYYY')
                    ELSE
                        TO_CHAR(subq_x.titdt_vencimento, 'DD/MM/YYYY')
                END AS titdt_vencimento,
                subq_x.fatura,
                subq_x.finalidade,
                subq_x.titvl_titulo,
                subq_x.soma_itens,
                subq_x.VlrDescContr,
                subq_x.cobrar,
                subq_x.observacao,
                subq_x.atraso,
                subq_x.nota,
                subq_x.forcnome,
                subq_x.titoid,
                subq_x.titnfloid,

                COALESCE(subq_x.titvl_desc_rescisao,0)::NUMERIC(10,2) as titvl_desc_rescisao,
                subq_x.terceirizada,

                (
                    CASE
                        WHEN subq_x.titvl_desc_rescisao > 0 THEN
                            subq_x.titvl_desc_rescisao
                        ELSE
                            (
                            CASE
                                WHEN (subq_x.finalidade IN ('Monitoramento')) AND TO_CHAR(titdt_vencimento, 'MM/YYYY') = TO_CHAR('{$filters['resmfax']}'::date, 'MM/YYYY') THEN
                                    (
                                        CASE
                                            WHEN subq_x.titno_parcela <> 0 THEN
                                                ((subq_x.soma_itens / (select max(titno_parcela) from titulo where titnfloid = subq_x.titnfloid)) / 30) * (CASE WHEN TO_CHAR('{$filters['resmfax']}'::date, 'DD')::INTEGER > 30 THEN 30 ELSE TO_CHAR('{$filters['resmfax']}'::date, 'DD')::INTEGER END)

                                            ELSE
                                                ((subq_x.soma_itens) - (((subq_x.soma_itens) / 30) * CASE WHEN TO_CHAR('{$filters['resmfax']}'::date, 'DD')::INTEGER > 30 THEN 30 ELSE TO_CHAR('{$filters['resmfax']}'::date, 'DD')::INTEGER END  ))
                                        END
                                    )

                                WHEN (subq_x.finalidade IN ('Locação', 'Monitoramento')) AND (titdt_vencimento > to_date('{$filters['resmfax']}', 'DD/MM/YYYY')) THEN
                                    (
                                    CASE
                                        WHEN subq_x.titno_parcela <> 0 THEN
                                            (subq_x.soma_itens / (select max(titno_parcela) from titulo where titnfloid = subq_x.titnfloid))
                                        ELSE
                                            subq_x.soma_itens
                                    END
                                    )
                                WHEN (subq_x.finalidade = 'Tx. Renovação Anual') THEN
                                    (
                                        SELECT
                                            CASE WHEN (subq_y.meses_utilizados > 0 AND subq_y.meses_utilizados < 12) THEN
                                                (subq_x.titvl_titulo) - ((subq_x.titvl_titulo / 12) * subq_y.meses_utilizados)
                                            ELSE
                                                0
                                            END AS desconto
                                        FROM (
                                                SELECT
                                                    ((DATE_PART('year',subq_w.intervalo) * 12) + DATE_PART('month',subq_w.intervalo)) AS meses_utilizados
                                                FROM (
                                                        SELECT AGE('{$filters['resmfax']}'::DATE,subq_x.nfldt_emissao::DATE) + (interval '1 month') AS intervalo
                                                ) subq_w
                                        ) subq_y
                                    )


                                ELSE
                                    0
                                END
                            )
                       END
                )::NUMERIC(10,2) AS desconto_automatico
            FROM (
                        SELECT
                        clioid,
                        titdt_vencimento,
                        titvl_acrescimo,
                        titvl_desconto,
                        nfldt_emissao,
                        titnfloid,

                        CASE WHEN ( nflserie IN ( 'A', 'FF', 'G', 'F', 'F1', 'F2', 'SL', 'SM', 'SP' ) ) THEN
                            'Mensalidade'
                        ELSE
                            'Inst./Hab.'
                        END AS Fatura,

                        CASE WHEN (nfiobroid = 20) THEN
                            'Tx. Renovação Anual'
                        WHEN ((nfiobroid <> 20) AND (nflserie IN ('SL', 'F'))) THEN
                            'Locação'
                        ELSE
                            'Monitoramento'
                        END AS Finalidade,

                        (titvl_titulo + titvl_acrescimo - titvl_desconto) AS titvl_titulo,

                        CASE WHEN sum(nfivl_item) < titvl_titulo THEN
                            SUM( nfivl_item )
                        ELSE
                            0
                        END AS VlrDescContr,

                        'S' AS cobrar,

                        CASE WHEN ('{$filters['resmfax']}'::date -  titdt_vencimento::date)  > 0 THEN
                            'Vencida'
                        WHEN ( nflserie IN ( 'A', 'FF', 'G', 'F', 'F1', 'F2', 'SL', 'SM', 'SP', 'SB', 'PF' ) ) THEN
                            'Será baixada após a retirada do equipamento'
                        WHEN titformacobranca = 1 THEN
                            'A Vencer (Pagável através de boleto bancário)'
                        ELSE
                            'A Vencer'
                        END AS observacao,

                        CASE WHEN ('{$filters['resmfax']}'::date - titdt_vencimento::date) < 0 THEN 0
                        ELSE ('{$filters['resmfax']}'::date - titdt_vencimento::date)
                        END AS atraso,

                        (nflno_numero::text ||' - '|| nflserie) AS nota,

                        forcnome, titoid, titvl_desc_rescisao,

                        (   SELECT cternome
                            FROM cobranca_terceirizada
                            WHERE titcteroid = cteroid) AS terceirizada,

                        (SELECT SUM(nfivl_item)
                          FROM nota_fiscal, nota_fiscal_item
                          WHERE nflno_numero=nfino_numero
                            AND nfloid=titnfloid
                            AND nficonoid IN ({$filters['connumero']})
                            ) AS soma_itens,
                            titno_parcela


                        FROM titulo
                        INNER JOIN forma_cobranca ON titformacobranca=forcoid
                        INNER JOIN nota_fiscal ON titnfloid=nfloid
                        INNER JOIN nota_fiscal_item ON nflno_numero = nfino_numero AND nflserie = nfiserie
                        INNER JOIN contrato ON nficonoid = connumero
                        INNER JOIN clientes ON nflclioid = conclioid AND clioid = conclioid
                        WHERE connumero IN ({$filters['connumero']})
                        AND titdt_pagamento IS NULL
                        AND nfldt_cancelamento IS NULL
                        AND titdt_cancelamento IS NULL
                        AND (   forccobranca IS TRUE OR (   forcoid = 51
                                                            AND titdt_credito IS NOT NULL
                                                            AND titdt_pagamento IS NULL ) )
                        AND nflserie IN ('A', 'F', 'F2', 'SL', 'F1', 'SM', 'SP', 'SB', 'FF', 'G', 'PF')
                        AND titclioid NOT IN ( 8374, 11842, 10625, 10797 )
                        GROUP BY titoid, clioid, titdt_vencimento, fatura, finalidade,
                        titvl_titulo, titvl_acrescimo, titvl_desconto, nfldt_emissao,
                        cobrar, observacao, atraso, nflno_numero, nflserie,
                        forcnome, titvl_desc_rescisao, titcteroid, titnfloid, titno_parcela
                        ORDER BY nota ASC, DATE(titdt_vencimento) ASC, atraso ASC
            ) subq_x";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca as formas de cobrança disponíveis
     * @return  array
     */
    public function findFormasCobranca()
    {
        $sql = "SELECT
                    forcoid
                  , forcnome
                FROM
                    forma_cobranca
                WHERE
                    forccobranca IS TRUE
                    AND LENGTH(forcnome) > 1
                ORDER BY
                    forcnome ASC";

        return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca multas de serviços relacionados aos contratos
     * @param   array   $filters
     * @return  array|boolean
     */
    public function findMultasServicos($filters)
    {
        if (is_array($filters['connumero']))
        {
            $filters['connumero'] = implode(', ', $filters['connumero']);
        }

        $mesAnoSolicitacao = substr($filters['resmfax'], -8);

        $sql = "SELECT
                    veiplaca
                  , coaocorrencia
                  , coavl_ocorrencia
                  , veioid
                  , connumero
                FROM contrato
                INNER JOIN contrato_atendimento ON connumero = coaconoid
                INNER JOIN veiculo              ON conveioid = veioid
                WHERE
                    connumero IN ({$filters['connumero']})
                    AND coadt_ocorrencia::date
                        BETWEEN '01/{$mesAnoSolicitacao} 00:00:00'
                        AND '{$filters['resmfax']} 23:59:59'";

        return $this->_fetchAll($this->_query($sql));
    }
    
    public function findNfloid($notas) {

        $retorno = array();

        if($notas[0] != "") {

            foreach ($notas as $nota) {

                $paramsNota = explode('/', $nota);

                $sql = "
                    SELECT
                        nfloid 
                    FROM
                        nota_fiscal
                    WHERE   nfldt_cancelamento is null AND
                        nflno_numero = ". $paramsNota[0] ." AND nflserie = '". $paramsNota[1] ."'
                ";

                $rs = $this->_query($sql);

                if(pg_num_rows($rs) > 0) {
                    $retorno[] = pg_fetch_result($rs, 0, 'nfloid');
                }
            }
        }

        return $retorno;

    }   

    /**
     * Busca as taxas de retirada de uma nova rescisão
     * @TODO: otimizar consulta
     * @param   array   $filters
     * @return  array|boolean
     */
    public function findMultasTaxaRetirada($filters)
    {
        
        $return = array();

        $notas = $this->findNfloid($filters['notas']);

        if(!empty($notas)) {

        $sql = "
            SELECT 
                obrobrigacao AS obrigacao_servico,    
                nficonoid AS connumero,
                obroid AS cod_obrigacao ,
                obrobroid_retirada AS cod_retirada ,
                (
                    SELECT 
                        veiplaca
                    FROM 
                        veiculo,
                        contrato
                    WHERE 
                        connumero = nficonoid 
                    AND 
                        conveioid = veioid
                ) AS veiplaca,
                (
                    SELECT 
                        veioid
                    FROM 
                        veiculo,
                        contrato
                    WHERE 
                        connumero = nficonoid 
                    AND 
                        conveioid = veioid
                ) AS veioid,
                (
                    SELECT
                        obrobrigacao
                    FROM
                        obrigacao_financeira
                    WHERE
                        obroid = nfiobroid
                ) AS obrigacao_retirada  
            FROM 
                nota_fiscal
            INNER JOIN
                nota_fiscal_item ON nflno_numero = nfino_numero AND nflserie = nfiserie 
            INNER JOIN 
                obrigacao_financeira f ON nfiobroid = obroid 
            WHERE       
                nfloid IN (
                    ". implode(',', $notas) ."
                )
            AND nfldt_cancelamento is null   ";
            $return = $this->_fetchAll($this->_query($sql));
        }

        return $return;
    }

    /**
     * Finalização de rescisão
     * @params array $arr
     * @return mixed
     */
    public function finalizarRescisao($arr)
    {

        try
        {           
            require_once 'includes/php/titulo_funcoes.php';
            require_once 'includes/php/cliente_funcoes.php';

            $cdUsuario = $_SESSION['usuario']['oid'];

            // Busca ID do cliente se a busca foi feita por número do contrato
            if (!strlen($arr['clioid']))
            {
                $sql = "SELECT
                            conclioid
                        FROM
                            contrato
                        WHERE
                            connumero = {$arr['contratos'][0]['connumero']}";

                $result = $this->_fetchAssoc($this->_query($sql));
                $arr['clioid'] = $result['conclioid'];
            }

            // Cria uma nova rescisão
            $this->_insertRescisaoMae($arr);
            $resmoid = $this->_findIdUltimaRescisao();

           // Cria rescisão para cada contrato e insere histórico
            if(!empty($arr['contratos'])){

                foreach($arr['contratos'] as $contrato) {
                    $rescconoid             = $contrato['connumero'];
                    $rescmeses              = $contrato['meses'];
                    $resmstatus             = $_POST['resmstatus'];
                    $rescperc_multa         = $contrato['porcentagem'];
                    $rescperc_multa_locacao = $contrato['porcentagem'];
                    $rescfax                = $contrato['resmfax'];
                    
                    $rescvl_locacao = 0;
                    $rescvl_monitoramento = 0;
                    $rescvl_desconto_monitoramento = 0;
                    $rescvl_desconto_locacao = 0;

                    if (!empty($arr['multas_locacao'])) {
                        foreach ($arr['multas_locacao'] as $contratoMultaLocacao) {

                            if ($contrato['connumero'] == $contratoMultaLocacao['contrato']) {
                                $vl_locacao = floatval($contratoMultaLocacao['totalLocacaoeAcessorios']) - floatval($contratoMultaLocacao['descontoProRataLocacao']);
                                $rescvl_locacao = $vl_locacao > 0 ? $vl_locacao : 0; 
                                $rescvl_desconto_locacao = floatval($contratoMultaLocacao['descontoProRataLocacao']);
                            }
                        }
                    }
                    
                    if (!empty($arr['multas_monitoramento'])) {
                        foreach ($arr['multas_monitoramento'] as $contratoMultaMonitoramento) {

                            if ($contrato['connumero'] == $contratoMultaMonitoramento['contrato']) {
                                $vl_monitoramento = floatval($contratoMultaMonitoramento['totalMonitoramento']) - floatval($contratoMultaMonitoramento['descontoProRataMonitoramento']);
                                $rescvl_monitoramento = $vl_monitoramento > 0 ? $vl_monitoramento : 0;
                                $rescvl_desconto_monitoramento = floatval($contratoMultaMonitoramento['descontoProRataMonitoramento']);
                            }
                        }
                    }
                    $sql = "INSERT INTO rescisao (
                                rescconoid
                              , rescusuoid
                              , rescresmoid
                              , rescmeses
                              , rescperc_multa
                              , rescvl_monitoramento
                              , rescvl_locacao
                              , rescstatus
                              , rescperc_multa_locacao
                              , rescfax
                              ,rescvl_desconto_monitoramento
                              ,rescvl_desconto_locacao
                            ) VALUES (
                                {$rescconoid}
                              , {$cdUsuario}
                              , {$resmoid}
                              , {$rescmeses}
                              , {$rescperc_multa}
                              , {$rescvl_monitoramento}
                              , {$rescvl_locacao}
                              , '{$resmstatus}'
                              , {$rescperc_multa_locacao}
                              , ".(trim($rescfax) ? "'{$rescfax}'::DATE" : "NULL")."
                              , {$rescvl_desconto_monitoramento}
                              , {$rescvl_desconto_locacao}
                              
                            )";

                    pg_query($this->_adapter, $sql);

                    $sql = "INSERT INTO rescisao_historico (
                                reschoid
                              , reschdata
                              , reschstatus
                              , reschobservacao
                              , reschusuoid_inc
                              , reschresmoid
                            ) VALUES (
                                (SELECT MAX(rescoid) FROM rescisao)
                              , NOW()
                              , '{$resmstatus}'
                              , 'Enviado carta de rescisão'
                              , {$cdUsuario}
                              , {$resmoid}
                            )";

                    pg_query($this->_adapter, $sql);
              
                }

            }
           
            // Inserir as faturas/títulos na tabela rescisao_item
            if (isset($arr['faturas']))
            {
                foreach ($arr['faturas'] as $fatura)
                {
                    $observacao = utf8_decode($fatura['observacao']);
                    $titoid = (trim($fatura['titoid']) != '') ? $fatura['titoid'] : 0;

                    $sql = "INSERT INTO rescisao_item (
                                rescoid
                              , rescititoid
                              , resciobservacao
                              , rescicobrar
                              , resciresmoid
                            ) VALUES (
                                (SELECT MAX(rescoid) FROM rescisao)
                              , {$titoid}
                              , '{$observacao}'
                              , '{$fatura['cobravel']}'
                              , {$resmoid}
                            )";

                    pg_query($this->_adapter, $sql);
                }
            }

            // Insere os serviços na tabela rescisao_item_serv
            $idContratos = array();
            foreach ($arr['contratos'] as $contrato)
            {
                $idsContratos[] = $contrato['connumero'];
            }
            $idsContratos = implode(', ', $idsContratos);

            $sql = "SELECT
                        veiplaca
                      , coaocorrencia
                      , coavl_ocorrencia
                      , veioid
                      , connumero
                    FROM
                        contrato_atendimento
                      , contrato
                      , veiculo
                    WHERE
                        coadt_ocorrencia::date BETWEEN TO_CHAR(NOW()::date, '01/MM/YYYY')::date AND TO_CHAR(NOW()::date, 'DD/MM/YYYY')::date
                        AND connumero = coaconoid
                        AND conveioid = veioid
                        AND connumero IN ({$idsContratos})";


            $results = $this->_fetchAll($this->_query($sql));
            

            if ($results)
            {
                foreach ($results as $result)
                {
                    $risveioid = $result['veioid'];
                    $risconoid = $result['connumero'];
                    $risdescr  = $result['coaocorrencia'];
                    $risvalor  = $result['coavl_ocorrencia'];

                    //Insere na tabela rescisao_item_serv
                    $sql = "INSERT INTO rescisao_item_serv (
                                risresmoid
                              , risveioid
                              , risconoid
                              , risdescr
                              , risqtde
                              , risvalor
                            ) VALUES (
                                $resmoid
                              , {$risveioid}
                              , {$risconoid}
                              , {$risdescr}
                              , 1
                              , {$risvalor}
                            )";

                    pg_query($this->_adapter, $sql);
                }
            }

            if(!empty($arr['multas_retirada'])) {

                foreach($arr['multas_retirada'] as $retiradaEquipamento) {
                    $rescrcobrar = "'t'";
                    $rescrconoid = $retiradaEquipamento['contrato'];
                    $rescrobr_servico = $retiradaEquipamento['eqcobroid'];
                    $rescrobroid_retirada = $retiradaEquipamento['obroidretirada'];
                    $rescrveioid = $this->getVeioidByContrato($retiradaEquipamento['contrato']);
                    $rescrvl_retirada = $retiradaEquipamento['valor'];
                   
                    $sql = "
                    INSERT INTO rescisao_retirada (
                        rescrcadastro,
                        rescrcobrar,
                        rescrresmoid, 
                        rescrconoid, 
                        rescrobr_servico,
                        rescrobroid_retirada,
                        rescrveioid,
                        rescrvl_retirada
                    ) 
                    VALUES (
                        NOW(),
                        $rescrcobrar,
                        $resmoid,
                        $rescrconoid,
                        $rescrobr_servico,
                        $rescrobroid_retirada,
                        $rescrveioid,
                        $rescrvl_retirada
                    )";

               
                    pg_query($this->_adapter, $sql);
                
                }
            }

            if(!empty($arr['multas_nao_retirada'])) {

                foreach($arr['multas_nao_retirada'] as $naoRetiradaEquipamento) {
                    $rescrcobrar = "'t'";
                    $rescrconoid = $naoRetiradaEquipamento['contrato'];
                    $rescrobr_servico = $naoRetiradaEquipamento['eqcobroid'];
                    $rescrobroid_retirada = $naoRetiradaEquipamento['obroidretirada'];
                    $rescrveioid = $this->getVeioidByContrato($naoRetiradaEquipamento['contrato']);
                    $rescrvl_nao_retirada = $naoRetiradaEquipamento['valor'];

                    $sql = "
                    INSERT INTO rescisao_retirada (
                        rescrcadastro,
                        rescrcobrar,
                        rescrresmoid, 
                        rescrconoid, 
                        rescrobr_servico,
                        rescrobroid_retirada,
                        rescrveioid,
                        rescrvl_nao_retirada
                    ) 
                    VALUES (
                        NOW(),
                        $rescrcobrar,
                        $resmoid,
                        $rescrconoid,
                        $rescrobr_servico,
                        $rescrobroid_retirada,
                        $rescrveioid,
                        $rescrvl_nao_retirada
                    )";
                  
                    pg_query($this->_adapter, $sql);
                }
            }
            
            return $resmoid;
        }
        catch (Exception $e) {
            var_dump($e->getMessage());
            var_dump(pg_last_error($this->_adapter));
            return false;
        }
    }

    public function _insertRescisaoMae($arr)
    {
        //Variáveis para as inserções
        $resmclioid            = $arr['clioid'];
        $resmusuoid            = $_SESSION['usuario']['oid'];;
        $resmvl_total_rescisao = $arr['total_rescisao'];
        $resmvl_remocao        = $arr['restaxa_remocao'];
        $resmvl_nao_devolucao  = $arr['resmvl_nao_devolucao'];
        $resmvl_multa          = $arr['resmultaLocacao'];
        $resmfax               = $arr['contratos'][0]['resmfax'];
        $resmmrescoid          = $arr['resmmrescoid'];
        $resmstatus            = $arr['resmstatus'];
        $resmvalidade          = $arr['vencimento'];
        $obs_carta             = (isset($arr['observacao_carta']))
                                    ? utf8_decode($arr['observacao_carta']) : "";
        
        $sql = "INSERT INTO 
                    rescisao_mae (
                        resmcadastro,
                        resmvalidade,
                        resmstatus,
                        resmclioid,
                        resmusuoid,
                        resmmrescoid,
                        resmfax,
                        resmvl_total,
                        resmvl_remocao,
                        resmvl_nao_devolucao,
                        resmvl_multa,
                        resmcarta,
                        resmobs_carta
                    )
                VALUES (
                    NOW(),
                    '$resmvalidade',
                    '$resmstatus',
                    $resmclioid,
                    $resmusuoid,
                    $resmmrescoid,
                    '$resmfax',
                    $resmvl_total_rescisao,
                    $resmvl_remocao,
                    $resmvl_nao_devolucao,
                    $resmvl_multa,
                    NOW(),
                    '$obs_carta'
                )";

        if (!$rs = $this->_query($sql)) {
            return false;
        }

        if (!pg_num_rows($rs)) {
            return false;
        }
        return True;
    }

    /**
     * Busca a última rescisão inserida, que _talvez_ seja a inserida agora
     */
    public function _findIdUltimaRescisao()
    {
        $sql = "SELECT
                    MAX(resmoid) AS resmoid
                FROM
                    rescisao_mae";

        $result = $this->_fetchAssoc($this->_query($sql));

        return $result['resmoid'];
    }

    /**
     * Busca os títulos que serão baixados na rescisão
     */
    protected function _findTitulosLocacaoRescisao($filters)
    {
        $ids = implode(', ', $filters['faturas_baixadas']);

        $sql = "SELECT
                    titoid
                  , titclioid
                  , ( COALESCE(titvl_titulo,    0)
                    - COALESCE(titvl_desconto,  0)
                    + COALESCE(titvl_multa,     0)
                    + COALESCE(titvl_juros,     0)
                    - COALESCE(titvl_ir,        0)
                    - COALESCE(titvl_iss,       0)
                    - COALESCE(titvl_piscofins, 0)
                    - COALESCE(tittaxa_administrativa, 0)) AS valor_corrigido
                FROM
                    titulo
                WHERE
                    titoid IN ({$ids})
                    AND titdt_vencimento >= '{$filters['resmfax']}'::date
                    AND (((titvl_pagamento IS NULL OR titvl_pagamento = 0)
                                AND titdt_credito IS NULL)
                            OR (titformacobranca = 51
                                AND titdt_credito IS NOT NULL
                                AND titdt_pagamento IS NULL ))";

        return $this->_fetchAll($this->_query($sql));
    }

    public function findContratoIsSeguradora($resmoid)
    {
        $sql = "SELECT
                    tpcseguradora
                FROM
                    rescisao
                    INNER JOIN rescisao_mae ON rescresmoid = resmoid
                    INNER JOIN contrato c ON rescconoid = connumero
                    INNER JOIN tipo_contrato ON tpcoid = conno_tipo
                    INNER JOIN veiculo ON conveioid = veioid
                WHERE
                    connumero IN (
                        SELECT
                            rescconoid
                        FROM
                            rescisao
                        WHERE
                            resmoid = {$resmoid}
                    )";

        $count = $this->_count($this->_query($sql));

        if ($count > 0)
        {
            return true;
        }

        return false;
    }

    /**
     * Insere no histórico impressão de segunda via (extremamente útil e crítico)
     * @param   int     $resmoid
     * @return  boolean
     */
    public function insertHistoricoSegundaVia($resmoid)
    {
        $codigoUsuario = $_SESSION['usuario']['oid'];

        $sql = "INSERT INTO rescisao_historico (
                    reschoid
                  , reschdata
                  , reschstatus
                  , reschobservacao
                  , reschusuoid_inc
                ) VALUES (
                    {$resmoid}
                  , NOW()
                  , 'D'
                  , 'Enviado 2ª via da carta de rescisão'
                  , {$codigoUsuario}
                )";

        return $this->_query($sql);
    }

    public function findCartaTaxasRetirada($resmoid)
    {
        $sql = "SELECT
                    (SELECT obrobrigacao
                     FROM obrigacao_financeira
                     WHERE rescrobr_servico=obroid) AS obr_servico
                  , rescrvl_retirada AS rescrvl_retirada
                  , rescrconoid AS contrato
                FROM
                    rescisao_retirada
                WHERE
                    rescrresmoid = {$resmoid}
                    AND rescrcobrar = 't'
                    AND rescrvl_retirada IS NOT NULL
                GROUP BY
                    obr_servico
                  , rescrvl_retirada
                  , rescrconoid
                ORDER BY
                    obr_servico
                  , rescrconoid";

        return $this->_fetchAll($this->_query($sql));
    }

    public function findCartaTaxasValorNaoRetirada($resmoid)
    {
        $sql = "SELECT
                    (SELECT obrobrigacao
                     FROM obrigacao_financeira
                     WHERE rescrobr_servico=obroid) AS obr_servico
                  , rescrvl_nao_retirada AS rescrvl_nao_retirada
                  , rescrconoid AS contrato
                FROM
                    rescisao_retirada
                WHERE
                    rescrresmoid = {$resmoid}
                    AND rescrcobrar = 't'
                    AND rescrvl_nao_retirada IS NOT NULL
                GROUP BY
                    obr_servico
                  , rescrvl_nao_retirada
                  , rescrconoid
                ORDER BY
                    obr_servico
                  , rescrconoid";

        return $this->_fetchAll($this->_query($sql));
    }

    public function getValorTotalRescisao($resmoid){
        
        $sql = "SELECT resmvl_total AS valor_total FROM rescisao_mae WHERE resmoid = {$resmoid};";

        $result = $this->_fetchAssoc($this->_query($sql));

        return $result['valor_total'];

    }

    public function findCartaServicos($resmoid)
    {
        $sql = "SELECT
                    veiplaca,
                    risdescr AS descr,
                    risqtde,
                    risvalor,
                    (risqtde * risvalor) as ristot
                FROM
                    rescisao_item_serv
                INNER JOIN veiculo ON  risveioid = veioid
                WHERE
                    risresmoid = {$resmoid}
                ORDER BY
                    veiplaca
                  , risdescr";

        return $this->_fetchAll($this->_query($sql));
    }

    public function findCartaFaturas($resmoid)
    {
        $sql = " SELECT
                DISTINCT titoid,
                (nflno_numero::text ||'/'|| nflserie) as fatura,
                titvl_titulo as valor,
                TO_CHAR(titdt_vencimento, 'DD/MM/YYYY') as vencimento,
                titvl_desc_rescisao as desconto,
                resciobservacao,

                CASE WHEN resciobservacao ILIKE 'Vencida' THEN
                    1
                WHEN resciobservacao ILIKE 'A Vencer' THEN
                    2
                WHEN resciobservacao ILIKE 'Nota baixada pela Sascar' THEN
                    3
                WHEN resciobservacao ILIKE 'Baixado' THEN
                    4
                ELSE
                    5
                END as obs,

                resmatt,

                --TO_CHAR(rescfax, 'DD/MM/YYYY') as rescfax,

                TO_CHAR(resmfax, 'DD/MM/YYYY') as resmfax,
                TO_CHAR(resmcarta, 'DD/MM/YYYY') as resmcarta

                FROM titulo
                INNER JOIN forma_cobranca ON titformacobranca=forcoid
                INNER JOIN nota_fiscal ON titnfloid=nfloid
                INNER JOIN nota_fiscal_item ON nflno_numero = nfino_numero and nflserie = nfiserie
                INNER JOIN contrato ON nficonoid = connumero
                INNER JOIN clientes ON nflclioid = conclioid AND clioid = conclioid
                INNER JOIN rescisao_item ON rescititoid = titoid
                INNER JOIN rescisao_mae ON resmoid = resciresmoid
                --INNER JOIN rescisao ON resmoid = rescresmoid

                WHERE connumero IN ( SELECT rescconoid FROM rescisao WHERE resmoid = {$resmoid} )
                AND nfldt_cancelamento IS NULL
                AND titdt_cancelamento IS NULL
                AND ( forccobranca IS TRUE OR ( forcoid = 51
                                                AND titdt_credito IS NOT NULL
                                                AND titdt_pagamento IS NULL ) )
                AND nflserie IN ('A', 'F1', 'F2', 'SL', 'SM', 'F', 'SP', 'SB', 'FF', 'G', 'PF', 'SP')
                --AND titclioid NOT IN ( 8374,11842 ,10625, 10797 )
                AND titclioid IN (0)

                GROUP BY titoid, titdt_vencimento, fatura, titvl_titulo,
                clioid, titvl_desc_rescisao, resmatt,
                --rescfax,
                resmfax,
                resciobservacao, resmcarta
                ORDER BY obs";

        return $this->_fetchAll($this->_query($sql));
    }

    public function buscarIdTitulo( $nflno_numero, $nflserie, $titdt_vencimento){


        //Busca o ID da nota
        $sqlNF = "
            SELECT
                nfloid
            FROM
                nota_fiscal
            WHERE
                nflno_numero = '" . $nflno_numero . "'
            AND
                nflserie = '" . $nflserie . "'
            AND nfldt_cancelamento is null 
            LIMIT 1
        ";

        $nota_fiscal = $this->_fetchAssoc($this->_query($sqlNF));
        if (!is_null($nota_fiscal['nfloid'])){

            //Trata a data de vencimento
            $vencimento = explode("/", $titdt_vencimento);
            $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];

            //Busca o id do titulo
            $sqlTitulo = "
                SELECT
                    titoid
                FROM
                    titulo
                WHERE
                    titnfloid = " . $nota_fiscal['nfloid'] . "
                AND
                    titdt_vencimento = '" . $vencimento . "'
            ";
            $titulo = $this->_fetchAssoc($this->_query($sqlTitulo));
            return $titulo['titoid'];
        } else {
            return null;
        }


    }

    /**
     * Busca as informações de um título específico.
     *
     * @param String $nflno_numero
     * @param String $nflserie
     * @param String $titdt_vencimento
     *
     * @return Mixed
     */
    public function buscarNotaFiscalTitulo($nflno_numero, $nflserie, $titdt_vencimento) {
        $retorno = new stdClass();
        $sql     = "
            SELECT
                nota_fiscal.nfloid,
                CASE
                    WHEN nota_fiscal.nfldt_cancelamento IS NULL THEN
                        TRUE
                    ELSE
                        FALSE
                END AS nflstatus
            FROM
                nota_fiscal
            WHERE
                nota_fiscal.nflno_numero = '" . $nflno_numero . "'
            AND
                nota_fiscal.nflserie = '" . $nflserie . "'
            AND 
                nfldt_cancelamento is null 
            LIMIT
                1
        ";

        if (!$rs = $this->_query($sql)) {
            return false;
        }

        if (!pg_num_rows($rs)) {
            return false;
        }

        $notaFiscal = pg_fetch_object($rs);

        $sql = "
            SELECT
                titulo.titoid,
                CASE
                    WHEN titulo.titdt_cancelamento IS NULL THEN
                        TRUE
                    ELSE
                        FALSE
                END AS titstatus
            FROM
                titulo
            WHERE
                titulo.titnfloid = '" . $notaFiscal->nfloid . "'
            AND
                titulo.titdt_vencimento = '" . $titdt_vencimento . "'::DATE
            LIMIT
                1
        ";

        if (!$rs = $this->_query($sql)) {
            return false;
        }

        if (!pg_num_rows($rs)) {
            return false;
        }

        $titulo = pg_fetch_object($rs);

        $retorno->notaFiscalStatus = $notaFiscal->nflstatus == 't' ? true : false;
        $retorno->tituloNumero     = $titulo->titoid;
        $retorno->tituloStatus     = $titulo->titstatus == 't' ? true : false;

        return $retorno;
    }

     /**
     * STI 84189
     * Retorna o email de um cliente com base em seu ID
     * @param   int $clioid
     * @return  string
     */
    public function getEmailCliente($clioid) {

        $clioid = $clioid ? $clioid : "NULL";

        $sql = "SELECT Coalesce(prehemail_cliente, cliemail) AS prehemail_cliente
                FROM   clientes
                       left  JOIN  pre_rescisao
                               ON  presclioid = clioid 
                       LEFT JOIN pre_rescisao_hist 
                              ON prehpresoid = presoid 
                WHERE  clioid =  $clioid 
                ORDER  BY prehdata DESC 
            LIMIT 1";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return $result ? $result['prehemail_cliente'] : ''; 
    }

     /**
     * STI 84189
     * Faz o agrupamento dos títulos retornados pelo webservice para fazer a baixa
     * @return boolean
     */
    public function agruparTitulosWebService() {

        $arrBaixas = array();
        $arrRepetidos = array();
         
        if(!empty($_POST['notasFiscaisBaixa'])) {

            foreach($_POST['notasFiscaisBaixa'] as $notaFiscal) {
                            
                $i = 0;
                
                foreach($notaFiscal['contratos'] as $contrato) {

                    foreach($contrato['titulos'] as $titulo) {

                        if( !empty($arrBaixas[$notaFiscal['numero']]) && in_array($titulo, $arrBaixas[$notaFiscal['numero']]) ) {
                            $arrRepetidos[$notaFiscal['numero']][] = $titulo;
                        }
                        else {
                            $arrBaixas[$notaFiscal['numero']][$i] = $titulo;                        
                        }

                        $i++;
                    }
                }
            }

            if( !empty($arrBaixas) && !empty($arrRepetidos) ) {

                foreach($arrBaixas as $notaFiscal => $titulosBaixa) {

                    $i = 0;

                    foreach($titulosBaixa as $tituloBaixa) {
                    
                        foreach($arrRepetidos as $nf => $tituloRepetido) {

                            foreach($tituloRepetido as $tituloRepetido) {
                                
                                if($tituloBaixa['codigo'] == $tituloRepetido['codigo']) {

                                    $arrBaixas[$notaFiscal][$i]['valorTotal'] += $tituloRepetido['valorTotal'];
                                }
                            }
                        }
                        $i++;
                    }
                }
            }
        }

        return $arrBaixas;
    }

    /**
     * STI 84189
     * Faz a baixa dos títulos de acordo com o retorno do webservice
     * @return array
     */
    public function baixarTitulos($arrBaixasWebService) {

        $arrRescisoesBaixa = array();
        $arrContratosBaixados = array();
        $arrTitulos = array();

        // echo "\n<pre>arrBaixasWebService:: "; print_r($arrBaixasWebService); "</pre>\n\n";
         
        if( !empty($arrBaixasWebService) ) {

            foreach($arrBaixasWebService as $notaFiscal => $titulosBaixa) {

                $notaFiscal = explode('/', $notaFiscal);

                $arrRescisoesBaixa[] = $this->efetuarBaixa($titulosBaixa, $notaFiscal[1], true); 
            }
        }
        
        if( !empty($arrRescisoesBaixa) ) {
            
            foreach($arrRescisoesBaixa as $key => $arrRescisaoBaixa) {
                
                foreach($arrRescisaoBaixa as $rescisaoBaixa) {
                    
                    if(isset($rescisaoBaixa['connumero'])) {
                        $arrContratosBaixados[] = $rescisaoBaixa['connumero'];    
                    }
                }
            }
        }
        // echo "\n<pre>arrRescisoesBaixa:: "; print_r($arrRescisoesBaixa); "</pre>\n\n";
        // echo "\n<pre>arrContratosBaixados:: "; print_r($arrContratosBaixados); "</pre>\n\n";
        if( !empty($_POST['arrContratos']) ) {
            
            foreach($_POST['arrContratos'] as $contrato) {

                if( !in_array($contrato['termo'], $arrContratosBaixados))  {
                            
                    $titulosBaixa = $this->getTitulosSerieA($contrato['termo'], implode("-",array_reverse(explode("/", $contrato['dataRescisao']))));

                    if( !empty($titulosBaixa) ) {
                        
                        for($i = 0; $i < count($titulosBaixa); $i++) {
                            $titulosBaixa[$i]['dataRescisao'] = $contrato['dataRescisao'];
                        }

                        array_push($arrTitulos, $titulosBaixa);
                        // $arrRescisoesBaixa[] = $this->efetuarBaixa($titulosBaixa, 'A', false);                    
                    }
                }
            }
            //echo "\n<pre>arrTitulos:: "; print_r($arrTitulos); "</pre>\n\n";
            $titulosBaixa = $this->agruparTitulosSerieA($arrTitulos);
            array_push($arrRescisoesBaixa, $this->efetuarBaixa($titulosBaixa, 'A', false));           
            
        }
        // echo "\n<pre>arrRescisoesBaixaDepois:: "; print_r($arrRescisoesBaixa); "</pre>\n\n";
        return $arrRescisoesBaixa;
    }

    /**
     * STI 84189
     * Gera a nota fiscal de rescisão de acordo com o retorno do webservice
     * @return boolean
     */
    public function gerarNotaFiscalRescisao() {

        require_once _SITEDIR_ . 'boleto_funcoes.php';

        $notaFiscalID             = $this->getNextSequenceVal('nota_fiscal_nfloid_seq');
        $notaFiscalDataInclusao   = date("'Y-m-d'"); 
        $notaFiscalDataNota       = date("'Y-m-d'"); 
        $notaFiscalDataEmissao    = date("'Y-m-d'"); 
        $notaFiscalNatureza       = "'PRESTACAO DE SERVICOS'"; 
        $notaFiscalTransporte     = "'RODOVIARIO'"; 
        $notaFiscalClienteID      = $_POST['clioid'] ? $_POST['clioid'] : "NULL"; 
        $notaFiscalNumero         = $this->getMaxNotaFiscalNumero();
        $notaFiscalSerie          = "'A'";
        $notaFiscalValorTotal     = $_POST['total_rescisao']; 
        $notaFiscalValorDesconto  = 0;
        $dataVencimento           = explode("/", $_POST['vencimento']);
        $notaFiscalDataReferencia = "'" . $dataVencimento[2] . "-" . $dataVencimento[1] ."-01'";
        $notaFiscalDataVencimento = "'" . implode("-",array_reverse(explode("/", $_POST['vencimento']))) . "'";
        $notaFiscalUsuarioID      = $_SESSION['usuario']['oid'];
        $notaFiscalNotaAnt        = $notaFiscalNumero;
        $notaFiscalValorPisCofins = 0;
        $notaFiscalValorIR        = 0;
        $notaFiscalValorISS       = 0;

        $sqlInsertNotaFiscal = "
        INSERT INTO nota_fiscal (
            nfloid,
            nfldt_inclusao,
            nfldt_nota,
            nfldt_emissao,
            nflnatureza,
            nfltransporte,
            nflclioid,
            nflno_numero,
            nflserie,   
            nflvl_total,
            nflvl_desconto,
            nfldt_referencia,
            nfldt_vencimento,
            nflusuoid,
            nflnota_ant,
            nflvlr_piscofins,
            nflvlr_ir,
            nflvlr_iss
        ) 
        VALUES (
            $notaFiscalID,
            $notaFiscalDataInclusao,
            $notaFiscalDataNota,
            $notaFiscalDataEmissao,
            $notaFiscalNatureza,
            $notaFiscalTransporte,
            $notaFiscalClienteID,
            $notaFiscalNumero,
            $notaFiscalSerie,
            $notaFiscalValorTotal,
            $notaFiscalValorDesconto,
            $notaFiscalDataReferencia,
            $notaFiscalDataVencimento,
            $notaFiscalUsuarioID,
            $notaFiscalNotaAnt,
            $notaFiscalValorPisCofins,
            $notaFiscalValorIR,
            $notaFiscalValorISS
        )";

        $resultInsertNotaFiscal = pg_query($this->_adapter, $sqlInsertNotaFiscal);

        $notaFiscalItemParams = array(
            'nfino_numero'   => $notaFiscalNumero,
            'nfiserie'       => $notaFiscalSerie,
            'nfidt_inclusao' => date("'Y-m-d'"), 
            'nfidesconto'    => 0,
            'nfinota_ant'    => $notaFiscalNumero
        );

        // Retirada Equipamentos
        if(!empty($_POST['multas_retirada'])) {

            foreach($_POST['multas_retirada'] as $retiradaEquipamento) {
                $notaFiscalItemParams['nficonoid']  = $retiradaEquipamento['contrato'];
                $notaFiscalItemParams['nfivl_item'] = $retiradaEquipamento['valor'];
                $notaFiscalItemParams['nfioid']     = $this->getNextSequenceVal('nota_fiscal_item_nfioid_seq');
                $notaFiscalItemParams['nfiobroid']  = $retiradaEquipamento['obroidretirada']; 
                $notaFiscalItemParams['nfids_item'] = "'" . $this->getDescricaoObroid($notaFiscalItemParams['nfiobroid']) . "'"; 
                $this->insertNotaFiscalItem($notaFiscalItemParams);
            }
        }

        // multa por nao devolução
        if(!empty($_POST['multas_nao_retirada'])) {

            foreach($_POST['multas_nao_retirada'] as $multaNaoDevolucao) {
                $notaFiscalItemParams['nficonoid']  = $multaNaoDevolucao['contrato'];
                $notaFiscalItemParams['nfivl_item'] = $multaNaoDevolucao['valor'];
                $notaFiscalItemParams['nfioid']     = $this->getNextSequenceVal('nota_fiscal_item_nfioid_seq');
                $notaFiscalItemParams['nfiobroid']  = $multaNaoDevolucao['obroidretirada']; 
                $notaFiscalItemParams['nfids_item'] = "'" . $this->getDescricaoObroid($notaFiscalItemParams['nfiobroid']) . "'"; 
                $this->insertNotaFiscalItem($notaFiscalItemParams);
            }
        }

         // Contratos
        if(!empty($_POST['contratos'])) {
        
            $obroidLocacao = $this->getObroid(array('valtpvoid' => 1, 'valregoid' => 16)); 
            $descricaoObroidLocacao = "'" . $this->getDescricaoObroid($obroidLocacao) . "'"; 

            $obroidMensalidade = $this->getObroid(array('valtpvoid' => 1, 'valregoid' => 17)); 
            $descricaoObroidMensalidade = "'" . $this->getDescricaoObroid($obroidMensalidade) . "'";

            $obroidMensalidadeIndevida = $this->getObroid(array('valtpvoid' => 1, 'valregoid' => 18)); 
            $descricaoObroidMensalidadeIndevida = "'" . $this->getDescricaoObroid($obroidMensalidadeIndevida) . "'";
            
            $descontos = array();

            if (!empty($_POST['multas_locacao'])) {

                foreach ($_POST['multas_locacao'] as $contratoLocacao) {
                    // Multas sobre valor locação
                    $notaFiscalItemParams['nficonoid'] = $contratoLocacao['contrato'];
                    $notaFiscalItemParams['nfiobroid'] = $obroidLocacao;
                    $notaFiscalItemParams['nfids_item'] = $descricaoObroidLocacao;
                    $notaFiscalItemParams['nfivl_item'] = $contratoLocacao['totalLocacaoeAcessorios'];
                    $notaFiscalItemParams['nfioid'] = $this->getNextSequenceVal('nota_fiscal_item_nfioid_seq');
                    $this->insertNotaFiscalItem($notaFiscalItemParams);

                    // guardar valor de desconto locacao
                    $descontos[$contratoLocacao['contrato']] =  floatval($contratoLocacao['descontoProRataLocacao']);
                }  
            }
 
            if (!empty($_POST['multas_monitoramento'])) {

                foreach ($_POST['multas_monitoramento'] as $contratoMonitoramento) {

                    // Valor Multa Mensalidade
                    $notaFiscalItemParams['nficonoid'] = $contratoMonitoramento['contrato'];
                    $notaFiscalItemParams['nfiobroid'] = $obroidMensalidade;
                    $notaFiscalItemParams['nfids_item'] = $descricaoObroidMensalidade;
                    $notaFiscalItemParams['nfivl_item'] = $contratoMonitoramento['totalMonitoramento'];
                    $notaFiscalItemParams['nfioid'] = $this->getNextSequenceVal('nota_fiscal_item_nfioid_seq');
                    $this->insertNotaFiscalItem($notaFiscalItemParams);

                    // somnar os valores de desconto locacao caso seja o mesmo contrato, senao guardar o valor de desconto monitoramento
                    if(!empty($descontos[$contratoMonitoramento['contrato']])){
                        $descontos[$contratoMonitoramento['contrato']] +=  floatval($contratoMonitoramento['descontoProRataMonitoramento']);
                    }else{
                        $descontos[$contratoMonitoramento['contrato']] = floatval($contratoMonitoramento['descontoProRataMonitoramento']);
                    }
                }
            }
            
            foreach($descontos as $key => $desconto){

                // Total Mensalidade Indevido 
                $notaFiscalItemParams['nficonoid'] = $key;
                $notaFiscalItemParams['nfiobroid'] = $obroidMensalidadeIndevida;
                $notaFiscalItemParams['nfids_item'] = $descricaoObroidMensalidadeIndevida;
                $notaFiscalItemParams['nfivl_item'] = $desconto;
                $notaFiscalItemParams['nfioid'] = $this->getNextSequenceVal('nota_fiscal_item_nfioid_seq');
                $this->insertNotaFiscalItem($notaFiscalItemParams);
            }
        }
        $arrReturn = array(
            'email'  => $_POST['email'],
            'titven' => $_POST['vencimento'],
        );

        return $arrReturn;
    }

    public function insertNotaFiscalItem($params) {
        
        if($params['nfivl_item'] > 0) {
            
            $params['nfivl_item'] = $params['nfiobroid'] == '8' ? "-{$params['nfivl_item']}" : $params['nfivl_item'];

            //verifica o tipo do item
            $tipoItem = $this->getTipoItemObrigacao($params['nfiobroid']);
            
            $sqlInsertNotaFiscalItem = "
            INSERT INTO nota_fiscal_item (
                nfino_numero,
                nfiserie,
                nficonoid,
                nfiobroid,
                nfids_item,
                nfivl_item,
                nfidt_inclusao,   
                nfidesconto,
                nfinota_ant,
                nfioid,
                nfitipo
            ) 
            VALUES (
                {$params['nfino_numero']},
                {$params['nfiserie']},
                {$params['nficonoid']},
                {$params['nfiobroid']},
                {$params['nfids_item']},
                {$params['nfivl_item']},
                {$params['nfidt_inclusao']},
                {$params['nfidesconto']},
                {$params['nfinota_ant']},
                {$params['nfioid']},
                '$tipoItem'
            )"; 
            pg_query($this->_adapter, $sqlInsertNotaFiscalItem);
        }
    }

    /**
     * Retorna a classificação da obrigação financeira (Locação ou Monitoramento), que determina se o item
     * da nota fiscal será classificado com 'L' ou 'M'
     * 
     * @param integer $obroid
     * @return array
     */
    private function getTipoItemObrigacao($obroid){
        
        $sql = "SELECT obrmonitoramento, 
                       obrhabilitacao
                  FROM obrigacao_financeira
                 WHERE obroid = $obroid ";
        
        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));
        
        //monitoramento 
        if($result['obrmonitoramento'] == 't'){
            $tipo = 'M';
        }
        
        //locação
        if($result['obrhabilitacao'] == 't'){
            $tipo = 'L';
        }
        
        return $tipo;
    }
    
    
     /**
     * STI 84189
     * Retorna a descricao da obrigação informada
     * @param int $obroid 
     * @return  int
     */
    public function getDescricaoObroid($obroid) {

        $sql = "SELECT TO_ASCII(obrobrigacao) as obrobrigacao FROM obrigacao_financeira WHERE obroid = $obroid";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return isset($result['obrobrigacao']) ? $result['obrobrigacao'] : ''; 
    }

     /**
     * STI 84189
     * Retorna o obroid
     * @return  int
     */
    public function getObroid($params) {

        $sql = "
        SELECT 
            valvalor 
        FROM 
            valor 
        WHERE 
            valtpvoid = " . $params['valtpvoid'] . "
        AND 
            valregoid = " . $params['valregoid'];

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return isset($result['valvalor']) ? $result['valvalor'] : 0; 
    }


     /**
     * STI 84189
     * Retorna o maior nflno_numero
     * @return  int
     */
    public function getMaxNotaFiscalNumero() {

        $sql = "SELECT MAX(nflno_numero) + 1 as nflno_numero FROM nota_fiscal WHERE nflserie = 'A'";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return $result['nflno_numero']; 
    }

    /**
     * STI 84189
     * Retorna o próximo valor da sequence informada
     * @return  int
     */
    public function getNextSequenceVal($sequence) {

        $sql = "SELECT NEXTVAL('$sequence')";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return $result['nextval']; 
    }

    /**
     * STI 84189
     * Retorna um titulo através de seu ID
     * @param $titoid
     * @return  int
     */
    public function getTituloById($titoid) {

        $sql = "
        SELECT
            titoid, 
            titdt_vencimento,
            nfloid,
            titvl_titulo, 
            titnfloid,
            titdt_referencia,
            titno_parcela,
            titclioid,
            titdt_pagamento
        FROM
            titulo
        JOIN
            nota_fiscal
        ON
            titnfloid = nfloid
        WHERE
            titoid = $titoid
            AND nfldt_cancelamento is null ";

        return $this->_fetchAssoc(pg_query($this->_adapter, $sql));
    }

     /**
     * STI 84189
     * Retorna a URL do servidor de webservice de acordo com o ambiente atual
     * @return string
     */
    public function getUrlWebService() {

        if(strstr($_SERVER['HTTP_HOST'], 'hom1')) {
            $valregoid = 6;
            $valtpvoid = 3;
        }
        elseif(strstr($_SERVER['HTTP_HOST'], 'intranet')) {
            $valregoid = 7;
            $valtpvoid = 3;
        }
        else {
            $valregoid = 6;
            $valtpvoid = 3;
        }

        $sql = "
        SELECT 
            valvalor
        FROM
            valor
        WHERE
            valregoid = $valregoid
        AND 
            valtpvoid = $valtpvoid";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return $result['valvalor']; 
    }

    /**
     * STI 84189
     * Faz a geração do boleto e carta de rescisão e envia para o email do cliente
     * @return array
     */
    public function enviarEmail() {
        require_once _SITEDIR_ . 'boleto_rescisao.php';
        require_once _SITEDIR_ . 'lib/MPDF/mpdf.php'; 
        require_once _SITEDIR_ . 'lib/tcpdf_php4/tcpdf.php';
        require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

        $cartaHTML = $_POST['carta'];
        $emailCliente = $_POST['email'];
        $strContratos = $_POST['strContratos'];
        $attachments = array();
        $filenames = array();

        // // INICIO PDF CARTA
        $mpdf = new MPDF();
        $filenames['cartaPDF'] = 'carta.pdf';
        $tst = $mpdf->WriteHTML($cartaHTML);
        $attachments['cartaPDF'] = $mpdf->Output('', 'S');
        // // FIM PDF CARTA
        
        try {

            // INICIO EMAIL
            $mail = new PHPMailer();
            $mail->isSMTP();
            
            if ($_SESSION['servidor_teste'] == 1) {
                $mail->SMTPSecure = '';
                $mail->SMTPAuth   = false;
                $mail->Username   = '';
                $mail->Password   = '';
                $mail->Host = 'correio.sascar.com.br';
                $mail->Mailer = 'smtp';
                $mail->Port = 25;
                $mail->AddAddress("teste_desenv@sascar.com.br");
            } else {
                $mail->AddAddress($emailCliente);
            }
           
            $mail->From = 'sascar@sascar.com.br';
            $mail->FromName = 'SASCAR';
            $mail->Subject = 'Rescisão Contratual SASCAR';
            $mailMessage = "Prezado cliente em anexo estão a carta e boleto da rescisão do(s) contrato(s): $strContratos.<br><br>Atenciosamente,<br><br>SASCAR TECNOLOGIA E SEGURANÇA AUTOMOTIVA S.A.";
            $mail->MsgHTML($mailMessage);
            $mail->AddStringAttachment($attachments['cartaPDF'], $filenames['cartaPDF'], 'base64', 'application/pdf');
            
            //nao deve enviar os boletos para o cliente apos implantacao do protheus, titulos serao gerados no totvs
            $attachmentFile = null;
            if(!empty($arrAttachment)){

                foreach($arrAttachment as $attachmentItem){
                    $mail->AddStringAttachment($attachmentItem['file'], $attachmentItem['filename'], 'base64', 'application/pdf');
                }
            }

            if (!$mail->Send()) {
                return $mail->ErrorInfo;
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
        return $filenames;
    }

      /**
     * STI 84189
     * Converte para formato extenso um valor em formato moeda
     * @param $valor
     * @return string
     */
    function valorPorExtenso($valor = 0) {
    
        $valor = str_replace(".", "", $valor);
        
        $valor = str_replace(",", ".", $valor);
        
        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        
        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
        
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
        
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
        
        $z = 0;
        
        $valor = number_format($valor, 2, ".", ".");
        
        $inteiro = explode(".", $valor);
        
        for($i = 0; $i < count($inteiro); $i++) {
        
            for($ii = strlen($inteiro[$i]); $ii < 3; $ii++) {
        
                $inteiro[$i] = "0".$inteiro[$i];
            }
        }
        
        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        
        for ($i = 0; $i < count($inteiro); $i++) {
            
            $valor = $inteiro[$i];
            
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
            
            $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
            
            $t = count($inteiro) - 1 - $i;
            
            $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            
            if ($valor == "000") 
                $z++; 
            elseif ($z > 0) 
                $z--;
            
            if (($t==1) && ($z>0) && ($inteiro[0] > 0)) 
                $r .= (($z>1) ? " de " : "").$plural[$t];
            
            if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        return $rt ? trim($rt) : "zero";
    }

    /**
     * STI 84189
     * Retorna as mensalidades vencidas
     * @param string $strContratos
     * @param string $maiorDataRescisao 
     * @return array
     */
    public function getMensalidadesVencidas($strContratos, $maiorDataRescisao) {

        $sql = "
        SELECT distinct 
            CASE 
                WHEN titnfloid IS NOT NULL THEN
                (
                    SELECT
                        nflno_numero || '/' || nflserie AS nota
                    FROM
                        nota_fiscal
                    WHERE
                        nfloid = titnfloid
                )
            ELSE
            CASE
                WHEN titno_cheque IS NOT NULL THEN
                    titno_cheque || '/CH'
                WHEN titno_cartao IS NOT NULL AND titno_cartao <> '' THEN
                    'CARTÃO'
                WHEN titnota_promissoria IS NOT NULL THEN
                    titnota_promissoria || '/NP'
                WHEN titno_avulso IS NOT NULL THEN
                    titno_avulso || '/AV'
            END
        END AS nota,
        to_char(titdt_vencimento, 'dd/mm/yyyy') AS titdt_vencimento,
        titvl_titulo,
        titdt_vencimento AS VCT
        FROM
            clientes
        JOIN titulo ON titclioid = clioid
        JOIN contrato ON conclioid = clioid
        JOIN nota_fiscal on nfloid = titnfloid
        JOIN nota_fiscal_item on nfino_numero = nflno_numero and nfiserie = nflserie AND connumero =  nficonoid
        WHERE 
            connumero IN ($strContratos)
        AND 
            nfldt_cancelamento is null  
        AND 
            titdt_pagamento IS NULL
        AND 
            titdt_cancelamento IS NULL
        AND (
            titformacobranca = 51
            AND titdt_credito IS NOT NULL
            OR (titdt_credito IS NULL)
        )
        AND titnao_cobravel IS NOT TRUE
        AND ( 
            ( 
                Extract (YEAR FROM titdt_vencimento) < Extract ( YEAR FROM '$maiorDataRescisao' :: DATE) 
            ) OR ( 
                Extract (YEAR FROM titdt_vencimento) = Extract ( YEAR FROM '$maiorDataRescisao' :: DATE)
                AND 
                Extract (MONTH FROM titdt_vencimento) < Extract ( MONTH FROM '$maiorDataRescisao' :: DATE) 
            )
        )
        ORDER BY
            VCT";

        $result = $this->_fetchAll(pg_query($this->_adapter, $sql));

        return $result; 
    }

     /**
     * STI 84189
     * Retorna o id do cliente de acordo com o contrato informado
     * @param int $connumero 
     * @return int
     */
    public function getClioidByContrato($connumero) {

        $sql = "SELECT conclioid FROM contrato WHERE connumero = $connumero";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return isset($result['conclioid']) ? $result['conclioid'] : ''; 
    }

     /**
     * STI 84189
     * Retorna o id do veículo de acordo com o contrato informado
     * @param int $connumero 
     * @return int
     */
    public function getVeioidByContrato($connumero) {

        $sql = "SELECT conveioid FROM contrato WHERE connumero = $connumero";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return isset($result['conveioid']) ? $result['conveioid'] : ''; 
    }

     /**
     * STI 84189
     * Retorna o id da obrigação financeira de acordo com a descrição da obrigação
     * @param int $connumero 
     * @return int
     */
    public function getObroidByObrobrigacao($descricao) {

        $sql = "SELECT obroid FROM obrigacao_financeira WHERE obrobrigacao = '$descricao'";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return isset($result['obroid']) ? $result['obroid'] : "''"; 
    }

    /**
     * STI 84189
     * Retorna as baixas da rescisão
     * @param int $resbairesmoid 
     * @param string $resbaitipo
     * @return array
     */
    public function getRescisaoBaixa($resbairesmoid, $resbaitipo) {

        $result = array();
        
        $sql = "
        SELECT 
            nf.nflno_numero || '/' || nf.nflserie as nota, 
            rb.resbaivl_titulo as valor, 
            ti.titdt_vencimento as vencimento
        FROM    
            rescisao_baixa rb 
        INNER JOIN 
            titulo ti ON ti.titoid = rb.resbaititoid 
        INNER JOIN 
            nota_fiscal nf ON nf.nfloid = ti.titnfloid 
        WHERE  
            rb.resbairesmoid = $resbairesmoid
        AND
            rb.resbaitipo = '$resbaitipo'
        AND 
            nf.nfldt_cancelamento is null 
        ORDER BY
            nota,
            ti.titdt_vencimento";

        $result = $this->_fetchAll(pg_query($this->_adapter, $sql));

        return $result; 
    }

    /**
     * STI 84189
     * Retorna os titulos para baixa, conforme contrato e data informados
     * @param int $connumero 
     * @param string $dataRescisao
     * @return array
     */
    public function getTitulosSerieA($connumero, $dataRescisao) {

        $sql = "
        SELECT 
            SUM(nfivl_item) as valorTotal, 
            nfloid, 
            titoid as codigo,
            COALESCE(to_char(titdt_pagamento,'dd/mm/yyyy'), '') as titdt_pagamento, 
            to_char(titdt_vencimento,'dd/mm/yyyy') as titdt_vencimento,
            ('now'::date-titdt_vencimento) as atraso,
            connumero 
        FROM 
            nota_fiscal 
        INNER JOIN 
            clientes ON clioid = nflclioid 
        INNER JOIN
            contrato ON conclioid = clioid 
        INNER JOIN 
            nota_fiscal_item ON nfinfloid = nfloid AND connumero = nficonoid 
        INNER JOIN
            obrigacao_financeira ON nfiobroid = obroid  
        LEFT JOIN 
            titulo ON titnfloid = nfloid 
        LEFT JOIN 
            forma_cobranca ON titformacobranca = forcoid 
        WHERE  
            connumero = $connumero 
        AND 
            forcoid <> 62 
        AND 
            nfiserie = 'A' 
        AND 
            ((EXTRACT(MONTH FROM nfldt_referencia) >= EXTRACT(MONTH FROM '$dataRescisao'::date) 
        AND  
            EXTRACT(YEAR FROM nfldt_referencia) >= EXTRACT(YEAR FROM '$dataRescisao'::date)) 
        OR 
            ((EXTRACT(MONTH FROM nfldt_referencia) <= EXTRACT(MONTH FROM '$dataRescisao'::date) 
        AND 
            EXTRACT(YEAR FROM nfldt_referencia) > EXTRACT(YEAR FROM '$dataRescisao'::date))))                 
        AND 
            obroftoid <> 6
        AND
            obrofgoid <> 17
        AND 
            nfldt_cancelamento is null  
        GROUP BY 
            nfloid,
            titdt_pagamento,
            titdt_vencimento,
            titoid,
            connumero";
        
        $result = $this->_fetchAll(pg_query($this->_adapter, $sql));

        return $result; 
    }

    /**
     * STI 84189
     * Retorna o titulo para baixa de notas seria A, conforme titoid informado
     * @param int $titoid
     * @return array
     */
    public function getTituloSerieA($titulo) {

        $sql = "
        SELECT 
            SUM(nfivl_item) as titvl_titulo, 
            nfloid as titnfloid, 
            titclioid,
            titno_parcela,
            titoid as codigo,
            COALESCE(to_char(titdt_pagamento,'dd/mm/yyyy'), '') as titdt_pagamento, 
            to_char(nfldt_referencia ,'dd/mm/yyyy') as titdt_vencimento,
            ('now'::date-titdt_vencimento) as atraso 
        FROM 
            nota_fiscal 
        INNER JOIN 
            clientes ON clioid = nflclioid 
        INNER JOIN
            contrato ON conclioid = clioid 
        INNER JOIN 
            nota_fiscal_item ON nfinfloid = nfloid AND connumero = nficonoid 
        LEFT JOIN 
            titulo ON titnfloid = nfloid 
        LEFT JOIN 
            forma_cobranca ON titformacobranca = forcoid 
        WHERE 
            titoid = {$titulo['codigo']}             
            AND nfldt_cancelamento is null  
        GROUP BY 
            nfloid,
            titdt_pagamento,
            titdt_vencimento,
            titoid";

        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));

        return $result; 
    }

    /**
     * STI 86968 
     * Retorna a observação de alteração do título 
     * @param int $titoid 
     * @return int 
     */
    public function getTitobs_historicoByTitoid($codigo) {
    
        $sql = "SELECT titobs_historico FROM titulo WHERE titoid = $codigo;";
    
        $result = $this->_fetchAssoc(pg_query($this->_adapter, $sql));
    
        return isset($result['titobs_historico']) ? $result['titobs_historico'] : "";
    }

    /**
     * STI 84189
     * Faz a baixa dos títulos de acordo com a série da nota fiscal
     * @param array $titulosBaixa
     * @param string $notaFiscalSerie
     * @param boolean $isWebService
     * @return array
     */
    public function efetuarBaixa($titulosBaixa, $notaFiscalSerie = '', $isWebService = true) {
        
        $arrRescisaoBaixa           = array();
        $arrRescisoesBaixa          = array();
        $tituloFormaCobranca        = 62;
        $tituloValorPagamento       = 0;
        $tituloValorDesconto        = 0;
        $tituloValorAcrescimo       = 0;
        $tituloValorJuros           = 0;
        $tituloValorMulta           = 0;
        $tituloValorTarifaBanco     = 0;
        $tituloTaxaAdministrativa   = 0;
        $tituloValorIR              = 0;
        $tituloValorPisCofins       = 0;
        $tituloValorISS             = 0;
        $tituloUsuarioAlteracao     = $_SESSION['usuario']['oid'];
        $tituloImpresso             = "'f'";
        $tituloNaoCobravel          = "'f'";
        $tituloFaturamentoVariavel  = "'f'";
        $tituloBaixaAutomaticaBanco = "'f'";
        $tituloTransacaoCartao      = "'f'";
        $tituloFaturaUnica          = "'f'";
        $tituloDataAlteracao        = date("'Y-m-d'");
        $tituloDataPagamento        = date("'Y-m-d'");
        $tituloDataCredito          = date("'Y-m-d'");
        $tituloDataInclusao         = date("'Y-m-d'");

        foreach($titulosBaixa as $tituloBaixa) {

            // echo "\n<pre>notaFiscalSerie:: "; print_r($notaFiscalSerie); "</pre>\n\n";
            // echo "\n<pre>isWebService:: "; print_r($isWebService); "</pre>\n\n";
            
            if($notaFiscalSerie != 'A' || !$isWebService) {
                
                if($notaFiscalSerie == 'A') {
                    $tituloBD = $this->getTituloSerieA($tituloBaixa);
                }
                else {
                    $tituloBD = $this->getTituloById($tituloBaixa['codigo']);
                }

                // echo "\n<pre>tituloBaixa:: "; print_r($tituloBaixa); "</pre>\n\n";

                // echo "\n<pre>tituloBD:: "; print_r($tituloBD); "</pre>\n\n";
               
                if(!empty($tituloBD)) {

                    if(empty($tituloBD['titdt_pagamento'])) {

                        $tituloBaixa['valorTotal'] = $tituloBaixa['valorTotal'] ? $tituloBaixa['valorTotal'] : $tituloBaixa['valortotal'];

                        if($notaFiscalSerie == 'A') {

                            if (!is_array($tituloBaixa['valorTotal'])) {
                                $mesAnoVencimento = date('Y-m', strtotime(implode("-",array_reverse(explode("/", $tituloBD['titdt_vencimento'])))));
                                $timeMesAnoVencimento = strtotime($mesAnoVencimento);
                                
                                $dtmRescisao = strtotime(implode("-",array_reverse(explode("/", $tituloBaixa['dataRescisao']))));
                                $mesAnoRescisao = date('Y-m', $dtmRescisao);
                                $timeMesAnoRescisao = strtotime($mesAnoRescisao);
                                
                                if($timeMesAnoVencimento == $timeMesAnoRescisao) {
                                    
                                    $ultimoDiaMesRescisao = date('t', $timeMesAnoRescisao);
                                    $diaRescisao = date('d', strtotime(implode("-",array_reverse(explode("/", $tituloBaixa['dataRescisao'])))));
                                    // echo "\nvalorTotal = " . $tituloBaixa['valorTotal'] . " - (" . $tituloBaixa['valorTotal'] . " - ((" . $tituloBaixa['valorTotal'] . "/" . $ultimoDiaMesRescisao . ") * (" . $ultimoDiaMesRescisao . " - " . $diaRescisao . ")))\n";
                                    $tituloBaixa['valorTotal'] = round( $tituloBaixa['valorTotal'] - ($tituloBaixa['valorTotal'] - (($tituloBaixa['valorTotal'] / $ultimoDiaMesRescisao) * ($ultimoDiaMesRescisao - $diaRescisao))), 2);                      
                                }

                                $arrRescisaoBaixa['connumero'] = $tituloBaixa['connumero'];
                            } else {
                                $countTitulosBaixa = count($tituloBaixa['valorTotal']);
                                $valorTotal = 0;
                                for ($i=0; $i < $countTitulosBaixa; $i++) { 
                                    $mesAnoVencimento = date('Y-m', strtotime(implode("-",array_reverse(explode("/", $tituloBD['titdt_vencimento'])))));
                                    $timeMesAnoVencimento = strtotime($mesAnoVencimento);
                                    
                                    $dtmRescisao = strtotime(implode("-",array_reverse(explode("/", $tituloBaixa['dataRescisao'][$i]))));
                                    $mesAnoRescisao = date('Y-m', $dtmRescisao);
                                    $timeMesAnoRescisao = strtotime($mesAnoRescisao);

                                    if($timeMesAnoVencimento == $timeMesAnoRescisao) {
                                        $ultimoDiaMesRescisao = date('t', $timeMesAnoRescisao);
                                        $diaRescisao = date('d', strtotime(implode("-",array_reverse(explode("/", $tituloBaixa['dataRescisao'][$i])))));
                                        //echo "\nvalorTotal = " . $tituloBaixa['valorTotal'][$i] . " - (" . $tituloBaixa['valorTotal'][$i] . " - ((" . $tituloBaixa['valorTotal'][$i] . "/" . $ultimoDiaMesRescisao . ") * (" . $ultimoDiaMesRescisao . " - " . $diaRescisao . ")))\n";
                                        $valorTotal += round( $tituloBaixa['valorTotal'][$i] - ($tituloBaixa['valorTotal'][$i] - (($tituloBaixa['valorTotal'][$i] / $ultimoDiaMesRescisao) * ($ultimoDiaMesRescisao - $diaRescisao))), 2);                      
                                    } else {
                                        $valorTotal += round( $tituloBaixa['valorTotal'][$i], 2 );
                                    }
                                }
                                $tituloBaixa['valorTotal'] = $valorTotal;
                            }
                        }

                        if($tituloBD['titvl_titulo'] == $tituloBaixa['valorTotal'] || ($notaFiscalSerie == 'A' && $timeMesAnoVencimento > $timeMesAnoRescisao && $tituloBD['titvl_titulo'] == $tituloBaixa['valorTotal'])) {
                            
                            // Baixa integral
                            $sqlBaixaIntegral = "
                            UPDATE 
                                titulo 
                            SET
                                titdt_credito       = $tituloDataCredito,
                                titdt_pagamento     = $tituloDataPagamento,
                                titdt_alteracao     = $tituloDataAlteracao, 
                                titformacobranca    = $tituloFormaCobranca,
                                titusuoid_alteracao = $tituloUsuarioAlteracao 
                            WHERE
                                titoid = {$tituloBaixa['codigo']}";

                            pg_query($this->_adapter, $sqlBaixaIntegral);

                            // Efetuar cancelamento
                            
                            $arrRescisaoBaixa['resbaititoid'] = $tituloBaixa['codigo'];
                            $arrRescisaoBaixa['resbaitipo'] = 'I';
                            $arrRescisaoBaixa['resbaivl_titulo'] = $tituloBaixa['valorTotal'];
                        }
                        else {
                            // Baixa parcial

                            $this->titulosBaixaParcial[] = $tituloBaixa['codigo'];

                            $tituloDataVencimento = $notaFiscalSerie == 'A' ? implode("-",array_reverse(explode("/", $_POST['vencimento']))) : $tituloBD['titdt_vencimento'];
                            $tituloDataVencimento = $tituloDataVencimento ? "'" . $tituloDataVencimento . "'" : 'NULL';

                            if (date('d', $dtmRescisao) != date('t', $dtmRescisao)) 
                            {
                                $titobs_historico_original = trim($this->getTitobs_historicoByTitoid($tituloBaixa['codigo']));
                                $titobs_historico = "Vencimento alterado por conta do processo de rescisão contratual. (".$_SESSION['usuario']['nome_completo']." ".date('d/m/Y H:i:s').") \n".$titobs_historico_original;
                                
                                $sqlBaixaParcial = "
                                UPDATE 
                                    titulo 
                                SET
                                    titvl_titulo        =  titvl_titulo - {$tituloBaixa['valorTotal']},
                                    titdt_vencimento    =  $tituloDataVencimento, 
                                    titdt_alteracao     =  $tituloDataAlteracao,
                                    titusuoid_alteracao =  $tituloUsuarioAlteracao,
                                    tittmavoid          = 3,
                                    titobs_historico    = '$titobs_historico',
                                    titformacobranca    = 84
                                WHERE 
                                    titoid = {$tituloBaixa['codigo']}";

                                pg_query($this->_adapter, $sqlBaixaParcial);

                                $tituloObj = TituloCobrancaModel::getTituloById($tituloBaixa['codigo']);

                                $boleto = new BoletoRegistradoModel();

                                $boleto->setTituloId($tituloBaixa['codigo']);
                                $boleto->setCodigoOrigem(BoletoRegistradoModel::CODIGO_ORIGEM_RESCISAO);
                                $boleto->setDataVencimento(str_replace("'", "", $tituloDataVencimento));
                                $boleto->setValorFace($tituloObj->valorTitulo);
                                $boleto->setValorNominal($tituloObj->valorTitulo);

                                $tipoEvento = TituloCobrancaModel::TIPO_EVENTO_ENTRADA_CONFIRMADA;

                                try {

                                    $boleto->registrarBoletoOnline();

                                    $sqlRegistrado = "
                                        UPDATE
                                            titulo
                                        SET
                                            tittpetoid = $tipoEvento
                                        WHERE
                                            titoid = {$tituloBaixa['codigo']}
                                    ";

                                    pg_query($this->_adapter, $sqlRegistrado);

                                }catch(\Exception $e){
                                    $this->erro_registro = $e->getMessage();
                                }

                                $arrRescisaoBaixa['resbaititoid'] = $tituloBaixa['codigo'];
                                $arrRescisaoBaixa['resbaitipo'] = 'P';
                                $arrRescisaoBaixa['resbaivl_titulo'] = $tituloBD['titvl_titulo'];

                                $tituloDataReferencia = $tituloBD['titdt_referencia'] ? "'" . $tituloBD['titdt_referencia'] . "'": 'NULL';

                                // Inserir um novo título
                                // [START][ORGMKTOTVS-1986] - Leandro Corso 
                                if (!INTEGRACAO) {
                                    $sqlInsertTitulo = "
                                    INSERT INTO titulo (
                                        titnfloid,
                                        titclioid,
                                        titno_parcela,
                                        titvl_titulo,
                                        titvl_pagamento,
                                        titvl_desconto,
                                        titvl_acrescimo,   
                                        titvl_juros,
                                        titvl_multa,
                                        titvl_tarifa_banco,
                                        titvl_ir,
                                        titvl_piscofins,
                                        titvl_iss,
                                        tittaxa_administrativa,
                                        titformacobranca,
                                        titusuoid_alteracao,
                                        titimpresso,
                                        titnao_cobravel,
                                        titfaturamento_variavel,
                                        titbaixa_automatica_banco,
                                        tittransacao_cartao,
                                        titfatura_unica,
                                        titdt_referencia,
                                        titdt_vencimento,
                                        titdt_credito,
                                        titdt_pagamento,
                                        titdt_inclusao,
                                        titdt_alteracao
                                    ) 
                                    VALUES (
                                        {$tituloBD['titnfloid']},
                                        {$tituloBD['titclioid']},
                                        {$tituloBD['titno_parcela']},
                                        {$tituloBaixa['valorTotal']},
                                        $tituloValorPagamento,
                                        $tituloValorDesconto,
                                        $tituloValorAcrescimo,
                                        $tituloValorJuros,
                                        $tituloValorMulta,
                                        $tituloValorTarifaBanco,
                                        $tituloValorIR,
                                        $tituloValorPisCofins,
                                        $tituloValorISS,
                                        $tituloTaxaAdministrativa,
                                        $tituloFormaCobranca,
                                        $tituloUsuarioAlteracao,
                                        $tituloImpresso,
                                        $tituloNaoCobravel,
                                        $tituloFaturamentoVariavel,
                                        $tituloBaixaAutomaticaBanco,
                                        $tituloTransacaoCartao,
                                        $tituloFaturaUnica,
                                        $tituloDataReferencia,
                                        $tituloDataVencimento,
                                        $tituloDataCredito,
                                        $tituloDataPagamento,
                                        $tituloDataInclusao,
                                        $tituloDataAlteracao          
                                    )";

                                    pg_query($this->_adapter, $sqlInsertTitulo);
                                }
                                // [END][ORGMKTOTVS-1986] - Leandro Corso 
                            }
                        }
                    }
                }

                if (date('d', $dtmRescisao) != date('t', $dtmRescisao)) 
                {
                    $arrRescisoesBaixa[] = $arrRescisaoBaixa;
                }
            }
        }

        return $arrRescisoesBaixa;
    }

    /**
     * STI 84189
     * Faz o agrupamento dos títulos serie A retornados pela RN39
     * @param array $titulosSerieA
     * @return boolean
     */
    public function agruparTitulosSerieA($titulosSerieA) {

        $arrTitulosBaixa = array();
        $arrTitulosRepetidos = array();
         
        if( !empty($titulosSerieA) ) {

            foreach($titulosSerieA as $titulos) {
                
                foreach($titulos as $titulo) {

                    if( !empty($arrTitulosBaixa) && $this->my_in_array($titulo['codigo'], $arrTitulosBaixa) ) {
                        array_push($arrTitulosRepetidos, $titulo);
                    }
                    else {
                        array_push($arrTitulosBaixa, $titulo);                        
                    }
                }
            }

            if( !empty($arrTitulosBaixa) && !empty($arrTitulosRepetidos) ) {

                foreach($arrTitulosBaixa as $tituloBaixa) {

                    $i = 0;
                    
                    foreach($arrTitulosRepetidos as $tituloRepetido) {
                            
                        if($tituloBaixa['codigo'] == $tituloRepetido['codigo']) {

                            if (is_array($arrTitulosBaixa[$i]['valortotal'])) {
                                $arrTitulosBaixa[$i]['valortotal'][] = $tituloRepetido['valortotal'];
                            } else {
                                $arrTitulosBaixa[$i]['valortotal'] = array($arrTitulosBaixa[$i]['valortotal'],$tituloRepetido['valortotal']);
                            }

                            if (is_array($arrTitulosBaixa[$i]['dataRescisao'])) {
                                $arrTitulosBaixa[$i]['dataRescisao'][] = $tituloRepetido['dataRescisao'];
                            } else {
                                $arrTitulosBaixa[$i]['dataRescisao'] = array($arrTitulosBaixa[$i]['dataRescisao'],$tituloRepetido['dataRescisao']);
                            }
                        }
                    }

                    $i++;
                }
            }
        }

        return $arrTitulosBaixa;
    }

    /**
     * STI 84189
     * Verifica se um determinado valor está dentro de um array
     * @param array $search
     * @param array $array
     * @return boolean
     */
    public function my_in_array($search, $array) {
        
        $in_keys = array();
        
        foreach($array as $key => $value) {
            
            if(in_array($search, $value)) {
                $in_keys[] = $key;
            }
        }

        if(count($in_keys) > 0) {
            return $in_keys;
        }
        else {
            return false;
        }
    }

    /**
     * STI 84189
     * Transforma um array de contratos em uma string separado por vírgula
     * @param array $contratos
     * @return string
     */
    public function getStrContratos($contratos) {

        if( !empty($contratos) ) {
            
            $strContratos = ''; 
            
            foreach($contratos as $contrato) {

                $strContratos .= $contrato['connumero'];

                 if($contrato != end($contratos)) {
                    $strContratos .= ', ';
                }
            }

            return $strContratos;
        }

        return false;
    }

     /**
     * STI 84189
     * Retorna a maior data de rescisão dentre os contratos informados
     * @param array $contratos
     * @return string
     */
    public function getMaiorDataRescisao($contratos) {

        if( !empty($contratos) ) {

            $dataRescisaoAtual = $contratos[0]['rescfax'];
            
            foreach($contratos as $contrato) {
                
                if(strtotime($dataRescisaoAtual) < strtotime(implode("-",array_reverse(explode("/", $contrato['rescfax']))))) {
                    $dataRescisaoAtual = implode("-",array_reverse(explode("/", $contrato['rescfax'])));
                }
            }

            $maiorDataRescisao = implode("/",array_reverse(explode("-", $dataRescisaoAtual)));

            return $maiorDataRescisao;
        }
        
        return false;
    }

    /**
     * Em virtude da descontinuidade do WebService de Rescisão, viu-se a necessidade
     * de implementar esse método para retornar os equipamentos de clientes SIGGO.
     */
    public function getEquipamentosSiggo($contrato) {
        if( !empty($contrato) ) {

            $sql = "select distinct * from ( 
                select 
                ec.eqcobroid as item, 
                obrec.obrobrigacao,  
                CASE  
                    WHEN cpvparcela = 999 THEN COALESCE((conprazo_contrato * cpagvl_servico),0)  
                    ELSE COALESCE((cpvparcela * cpagvl_servico),0)  
                END AS valor_servico,  
                obrrec.obrvl_obrigacao as retirada, 
                obrrec.obroid 
            from contrato con 
                inner join contrato_pagamento cp on cp.cpagconoid = con.connumero 
                inner join equipamento_classe ec on ec.eqcoid = con.coneqcoid 
                inner join obrigacao_financeira obrec on obrec.obroid = ec.eqcobroid 
                inner join obrigacao_financeira obrrec on obrrec.obroid = obrec.obrobroid_retirada 
                left join cond_pgto_venda cpv on cpv.cpvoid =  cpagcpvoid 
            where  
                con.connumero =  $contrato 
                and con.conno_tipo = 905   
                and con.coneqcoid is not null 
            union all 
            select  
                cv.consobroid, 
                obrec.obrobrigacao, 
                obrec.obrvl_obrigacao valor_servico,  
                COALESCE(obrrec.obrvl_obrigacao,0) as retirada,  
                obrrec.obroid 
            from contrato con 
                inner join contrato_servico cv on cv.consconoid = con.connumero 
                inner join obrigacao_financeira obrec on obrec.obroid = cv.consobroid 
                left join obrigacao_financeira obrrec on obrrec.obroid = obrec.obrobroid_retirada 
            where  
                con.connumero =  $contrato
                and con.conno_tipo = 905
                and consiexclusao is null 
                ) as q";

            $query = $this->_query($sql);

            if ($query) {
                $results = $this->_fetchAll($query);
                $retorno = array();
                if (!empty($results)) {
                    foreach ($results as $result) {
                        $retorno[] = (object) array(
                            'item' => $result['obrobrigacao'],
                            'obroidretirada' => $result['item'],
                            'valor' => $result['valor_servico'],
                            'valorRetirada' => $result['retirada']
                        );
                    }
                    return $retorno;
                }
            }
        }
        
        return false;

    }
}