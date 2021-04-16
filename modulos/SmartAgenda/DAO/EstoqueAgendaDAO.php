<?php
/**
 * Classe de persistencia de dados
 */


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/estoque_agenda_'.date('d-m-Y').'.txt');

require_once _MODULEDIR_ ."/SmartAgenda/DAO/DAO.php";

class EstoqueAgendaDAO extends DAO{

    const STATUS_IMOBILIZADO_TRANSITO   = 2;
    const STATUS_IMOBILIZADO_DISPONIVEL = 3;
    const STATUS_RESERVA_RESERVADO      = 1;
    const STATUS_RESERVA_PRE_RESERVADO  = 3;
    const STATUS_REMESSA_ENVIADO        = 1;
    const ESTOQUE_RESERVADO     = 1;
    const ESTOQUE_EM_TRANSITO   = 2;
    const ESTOQUE_EM_TRANSITO_RESERVADO = 3;

    public function getDadosContrato($ordoid) {

        $retorno = array();

        $sql = " SELECT
							connumero,
							coalesce(conequoid,0) as conequoid,
							conveioid
						FROM
							contrato
						INNER JOIN
							ordem_servico ON ordconnumero = connumero
						WHERE
                    ordoid = ". intval($ordoid);

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getDadosOS ($ordoid, $isPossuiEquipamento, $idClasseMigracao = 0) {

        $retorno = array();

        $coneqcoid = $idClasseMigracao > 0 ? $idClasseMigracao : 'coneqcoid';
        
        /**
         * ASM-4493
         * junho/2019
         * Corre??o quantidade de produtos reservados, quando item ? inclu?do em multiplicidade na OS
         * Adicionado na consulta abaixo, um COUNT para identificar a quantidade que cada item/grupo foi adicinado na OS
         */

        if($isPossuiEquipamento) {
            $innerEquipamento = 'INNER JOIN equipamento ON (equoid = conequoid)';
            $innerVersao = 'INNER JOIN equipamento_classe_instalacao ON (eqcoid = eqcieqcoid AND eqciexclusao IS NULL)
                            INNER JOIN equipamento_versao ON (eveoid = equeveoid AND evedt_exclusao IS NULL)
                            INNER JOIN equipamento_projeto ON (eproid = eveprojeto)';
            $select = 'eproid AS chave_equipamento,
                       eveoid AS chave_versao';
            $group = 'chave_tipo, otioid, eqcoid, eproid, eveoid, conclioid, mcaoid, mlooid';

        } else {
            $innerEquipamento = '';
            $innerVersao = '';
            $select = 'NULL AS chave_equipamento,
                       NULL AS chave_versao';
            $group = 'chave_tipo, otioid, eqcoid, conclioid, mcaoid, mlooid';
        }

        $sql = "SELECT DISTINCT
                        ostoid AS chave_tipo,
                        otioid AS chave_motivo,
                        otidescricao AS descricao,
                        eqcoid AS chave_classe_equipamento,
                        ".$select.",
                        conclioid,
                        mcaoid AS chave_marca,
                        mlooid AS chave_modelo,
                        COUNT(1) AS quantidade
				      FROM ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item ON ositotioid = otioid
				INNER JOIN os_tipo ON otiostoid = ostoid
				INNER JOIN contrato ON ordconnumero = connumero
				INNER JOIN veiculo ON conveioid = veioid
				INNER JOIN modelo ON veimlooid = mlooid
				INNER JOIN marca ON mlomcaoid = mcaoid
                ".$innerEquipamento."
                        INNER JOIN equipamento_classe ON eqcoid = ".$coneqcoid." ".$innerVersao."
                WHERE ordoid = ". intval($ordoid)."
				       AND eqcinativo IS NULL
				       AND ositstatus NOT IN ('X', 'C')
                       AND ositexclusao IS NULL
				GROUP BY ".$group."
                    ORDER BY chave_tipo, chave_motivo";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getItemEssencial($dadosFiltro){

        $retorno  = array();

        //gera objeto dos filtros
        $dadosFiltro = (Object) $dadosFiltro;

        $sql = "SELECT
                    iesoid, iesostoid, iesotioid, iesmcaoid, iesmlooid, ieseqcoid, ieseproid, ieseveoid
			    FROM
			    	item_essencial_servico
				WHERE
                    iesostoid = " . $dadosFiltro->iesostoid . "
                AND
                	iesotioid = " . $dadosFiltro->iesotioid . "
                AND
                    (ieseqcoid IS NULL OR ieseqcoid = " . $dadosFiltro->ieseqcoid . ")
                AND
                    ( ieseproid IS NULL OR ieseproid = " . $dadosFiltro->ieseproid . ")
                AND
                    ( ieseveoid IS NULL OR ieseveoid = " . $dadosFiltro->ieseveoid . ")
                AND
                    ( iesmcaoid IS NULL OR iesmcaoid = " . $dadosFiltro->iesmcaoid . ")
                AND
                    ( iesmlooid IS NULL OR iesmlooid = " . $dadosFiltro->iesmlooid . ")";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getMateriaisItemEssencial( $iesoid, $isClientePremium ){

        $retorno = array();
        $filtro = '';

        $filtro = ( $isClientePremium === TRUE) ? ' AND prdpremium IS TRUE ' : '';

        $sql = "SELECT
                    iespprdoid,
                    iespquantidade,
                    prdproduto
                FROM
                    item_essencial_servico_produto
                INNER JOIN
                    produto ON prdoid = iespprdoid
                WHERE
                    iespiesoid = ".$iesoid."
                ". $filtro ."
                ORDER BY
                    prdproduto ASC
                ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $retorno =  pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getEstoque($repoid, $prdoid){

        $retorno = array(
            "tipo" => "",
            "prdoid" => $prdoid,
            "quantidade_estoque" => 0
        );

        //Estoque Produto
        $sqlProduto = " SELECT
							'produto' as tipo,
							prdoid,
							coalesce(espqtde,0) AS quantidade_estoque
						FROM
							estoque_produto
						INNER JOIN
							relacionamento_representante ON esprelroid=relroid
						INNER JOIN
							produto ON prdoid = espprdoid
						WHERE
							prddt_exclusao IS NULL
						AND
							relrrep_terceirooid = ".intval($repoid)."
						AND
							espprdoid = ".intval($prdoid);

        $rs = $this->executarQuery($sqlProduto);

        if (pg_num_rows ($rs) > 0) {
            $retorno = pg_fetch_all($rs);
            if($retorno[0]['quantidade_estoque'] > 0){
                return $retorno[0];
            }
        }

        //Estoque Imobilizado
        $sqlImobilizado = " SELECT
								'imobilizado' as tipo,
								prdoid,
								imobserial
							FROM
								imobilizado
							INNER JOIN
								relacionamento_representante ON imobrelroid = relroid
							INNER JOIN
								produto ON prdoid = imobprdoid
							WHERE
								prddt_exclusao IS NULL
							AND
								imobimsoid = ".self::STATUS_IMOBILIZADO_DISPONIVEL."
							AND
								imobexclusao IS NULL
							AND
								relrrep_terceirooid = ".intval($repoid)."
							AND
								imobprdoid = ".intval($prdoid);

        $rs = $this->executarQuery($sqlImobilizado);

        if (pg_num_rows ($rs) > 0) {
            $retorno = pg_fetch_all($rs);
            $retorno[0]['quantidade_estoque'] = pg_num_rows($rs);
            return $retorno[0];
        }

        return $retorno;
    }

    public function getReservaEstoque($repoid, $prdoid, $idRemessa = NULL, $dataAgendamento = NULL, $dataEntregaCd = NULL){

        $retorno = array();
        $where = '';

        if( ! empty($idRemessa) ) {
            $where = ' AND raiesroid = ' . $idRemessa;
        }

        if($dataAgendamento != null){
            $dataConvertida =  date('Y-m-d', strtotime($dataAgendamento));
            $where .= " AND ordagenda > '" . $dataConvertida . "'";
        }

        if($dataEntregaCd != null){
            $dataConvertida =  date('Y-m-d', strtotime($dataEntregaCd));
            $where .= " AND ordagenda > '" . $dataConvertida . "'";
        }

        $sql =" SELECT raiprdoid,
		               SUM(raiqtde_estoque )AS qtde_reserva_estoque,
        			   SUM(raiqtde_transito) AS qtde_reserva_transito
			      FROM ordem_servico
			INNER JOIN reserva_agendamento ON ordoid = ragordoid
			INNER JOIN reserva_agendamento_item ON ragoid = rairagoid
			INNER JOIN reserva_agendamento_status ON rasoid = ragrasoid
			     WHERE ragrepoid = $repoid  --representante
			       AND raiprdoid = $prdoid  --produto
			       AND ragrasoid IN (
                                        ".self::STATUS_RESERVA_RESERVADO.",
                                        ".self::STATUS_RESERVA_PRE_RESERVADO."
                                    )
			       AND ragdt_cancelamento IS NULL
			       AND raidt_exclusao IS NULL
                   ". $where ."
			  GROUP BY raiprdoid";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getMaiorDataDoPrestador($repoid, $prdoid, $transito = false, $dataEntregaCd = null) {
        $retorno = array();

        if($transito){
            $and = 'AND raiqtde_transito > 0';
        }else{
            $and = 'AND raiqtde_estoque > 0';
        }

        $whereDataCd = '';
        if($dataEntregaCd != null){
            $dataConvertida =  date('Y-m-d', strtotime($dataEntregaCd));
            $whereDataCd = " AND osa.osadata > '" . $dataConvertida . "'";
        }

       $sql = "SELECT max(TO_CHAR(osadata, 'YYYY-MM-DD' )) as osadata
                FROM ordem_servico os
                INNER JOIN reserva_agendamento ra ON os.ordoid = ra.ragordoid
                INNER JOIN reserva_agendamento_item rai on rai.rairagoid = ra.ragoid
                INNER JOIN ordem_servico_agenda osa on osa.osaordoid = os.ordoid
                INNER JOIN produto p on p.prdoid = rai.raiprdoid
                LEFT JOIN estoque_remessa er on esroid = rai.raiesroid
                WHERE ragrasoid IN (
                                        ".self::STATUS_RESERVA_RESERVADO.",
                                        ".self::STATUS_RESERVA_PRE_RESERVADO."
                                    )
                AND ra.ragrepoid = $repoid
                AND raiprdoid = $prdoid
                AND ragdt_cancelamento IS NULL
                AND raidt_exclusao IS NULL
                AND osa.osadata > NOW()
                AND osaexclusao IS NULL
                $and
                $whereDataCd
                GROUP BY osa.osadata
                ORDER BY osa.osadata desc
               limit 1 ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    /**
     * Pega o estoque que está reservado do prestador
     */
    public function getEstoqueReservadoDoPrestador($repoid, $prdoid, $transito = false, $dataEntregaCd = null, $dataAgendamento = null){

        $retorno = array();

        //$osadata = $this->getMaiorDataDoPrestador($repoid, $prdoid, $transito, $dataEntregaCd);

        $and = '';
        if($transito){
            $and .= ' AND rai.raiqtde_transito > 0';
        }else{
            $and .= ' AND rai.raiqtde_estoque > 0';
        }

        // if(!empty($osadata)) {
        //     $and .= " AND osa.osadata = '".$osadata[0]['osadata']."'";
        // }
        if($dataEntregaCd != null){
            $dataConvertida =  date('Y-m-d', strtotime($dataEntregaCd));
            $and .= " AND osa.osadata > '" . $dataConvertida . "'";
        }

        if($dataAgendamento != null){
            $dataConvertida =  date('Y-m-d', strtotime($dataAgendamento));
            $and .= " AND osa.osadata > '" . $dataConvertida . "'";
        }

        $sql =" SELECT 
                    ra.ragosaoid, 
                    rai.raioid, 
                    rai.raiprdoid, 
                    os.ordoid, 
                    rai.rairagoid, 
                    ra.ragordoid, 
                    TO_CHAR(osa.osadata, 'DD-MM-YYYY' ) as osadata,
                    rai.raiqtde_estoque as qtde_reserva_estoque, 
                    rai.raiqtde_transito as qtde_reserva_transito, 
                    p.prdproduto,
                    TO_CHAR(er.esrdata, 'DD-MM-YYYY' ) as esrdata
                FROM ordem_servico os
                INNER JOIN reserva_agendamento ra ON os.ordoid = ra.ragordoid
                INNER JOIN reserva_agendamento_item rai on rai.rairagoid = ra.ragoid
                INNER JOIN ordem_servico_agenda osa on osa.osaordoid = os.ordoid
                INNER JOIN produto p on p.prdoid = rai.raiprdoid
                LEFT JOIN estoque_remessa er on esroid = rai.raiesroid
                WHERE ra.ragrasoid IN (
                                        ".self::STATUS_RESERVA_RESERVADO.",
                                        ".self::STATUS_RESERVA_PRE_RESERVADO."
                                    )
                AND ra.ragrepoid = $repoid
                AND rai.raiprdoid = $prdoid
                AND ra.ragdt_cancelamento IS NULL
                AND rai.raidt_exclusao IS NULL
                AND osa.osadata > NOW()
                AND osa.osaexclusao IS NULL
                AND ragrepoid != (
                    SELECT 
                        repoid 
                    FROM 
                        representante 
                    WHERE 
                        repcentral = true 
                        AND repcont_estoque_adm = true 
                    LIMIT 1
                    )
                $and
                GROUP BY 
                    ra.ragosaoid, 
                    rai.raioid, 
                    rai.raiprdoid, 
                    os.ordoid, 
                    rai.rairagoid, 
                    ra.ragordoid, 
                    rai.raiqtde_estoque, 
                    rai.raiqtde_transito, 
                    osa.osadata, 
                    p.prdproduto, 
                    er.esrdata
                ORDER BY osa.osadata DESC";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getEstoqueDisponivelTransito($repoid, $listaProdutos){

        $retorno = array();
        $sqlProdutos = implode(',', $listaProdutos);

        //SERIALIZADOS / IMOBILIZADOS
        $sql = "SELECT
					esroid,
                    esrioid,
					count(esroid) as quantidade_transito,
					TO_CHAR(esrdata, 'DD-MM-YYYY' ) AS esrdata,
                    prdoid,
                    prdproduto
				FROM
					estoque_remessa
				INNER JOIN
					estoque_remessa_item ON esroid = esrioid
				INNER JOIN
					relacionamento_representante ON esrrelroid = relroid
				INNER JOIN
					imobilizado ON (imobserial = esrinumero_serie AND imobpatrimonio = esripatrimonio AND esrrelroid = imobrelroid)
                INNER JOIN
                    produto ON (prdoid = imobprdoid)
				WHERE
					relrrep_terceirooid = ".intval($repoid)."
				AND
					imobprdoid IN (". $sqlProdutos .")
				AND
					imobimsoid  = ".self::STATUS_IMOBILIZADO_TRANSITO."
				AND
					(esritipo != 'M' OR esritipo IS NULL)
				AND
                    esrdt_exclusao IS NULL
				AND 
                    esrersoid = 1
				GROUP BY
					esroid, esrioid, esrdata, prdoid, prdproduto
				ORDER BY
					esroid ASC";
        
        $rs = $this->executarQuery($sql);

        if ( pg_num_rows($rs) > 0 ) {
            $validaRetorno = pg_fetch_all($rs);
            foreach ($validaRetorno as $valida) {
                if($valida['quantidade_transito'] > 0){
                    $retorno[] = $valida;
                }
            }
        }else{

            $sql_mat = " SELECT
							esroid,
                            esrioid,
							esriqtde as quantidade_transito,
							TO_CHAR(esrdata, 'DD-MM-YYYY' ) AS esrdata,
                            prdoid,
                            prdproduto
						FROM
							estoque_remessa
						INNER JOIN
							estoque_remessa_item ON esroid = esrioid
						INNER JOIN
							relacionamento_representante ON esrrelroid=relroid
                        INNER JOIN
                            produto ON (prdoid = esrirefoid)
						WHERE
							esrersoid = ".self::STATUS_REMESSA_ENVIADO."
						AND
							relrrep_terceirooid = ".intval($repoid)."
						AND
							esrirefoid IN (". $sqlProdutos .")
						AND
							esritipo = 'M'
						AND
							esrdt_exclusao IS NULL
						ORDER BY
							esroid ASC ";

            $rs = $this->executarQuery($sql_mat);

            if (pg_num_rows ($rs) > 0) {
                $validaRetorno = pg_fetch_all($rs);
                foreach ($validaRetorno as $valida) {
                    if($valida['quantidade_transito'] > 0){
                        $retorno[] = $valida;
                    }
                }
            }

        }

        return $retorno;
    }

    public function getOrdemServicoAgenda($ordoid){

        $sql = " SELECT osaoid
			       FROM ordem_servico_agenda
			      WHERE osaordoid = $ordoid
			        AND osaexclusao IS NULL
			        AND osamotivo_cancelamento IS NULL ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return false;
    }

    public function getTempoModal($repoid){

        if(empty($repoid)){
            throw new Exception ("Informe o ID do representante para recuperar o tempo modal.");
        }

        $sql = " SELECT modttempotransportado
				   FROM modal_transporte
				  WHERE modtrepoid = $repoid
				    AND modtorigem = 'S'
				    AND	modtpadrao IS TRUE
				    AND modtdt_exclusao IS NULL ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            return pg_fetch_result($rs, 0, 'modttempotransportado');
        }

        return 0;
    }

    public function getParametros($modulo, $campo){

        if(empty($modulo)){
            throw new Exception ("Informe o modulo para buscar o valor do par?metro. ");
        }
        if(empty($campo)){
            throw new Exception ("Informe o campo para buscar o valor do par?metro. ");
        }

        $sql = "SELECT pcsidescricao AS valor
		          FROM parametros_configuracoes_sistemas
			INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
				 WHERE pcsipcsoid = '$modulo'
				   AND pcsioid = '$campo' " ;

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return false;
    }

    public function getStatusAgendaHistorico(){

        $sql = "	SELECT
						mhcoid
					FROM
						motivo_hist_corretora
					WHERE
						mhcdescricao ILIKE 'Solicita% para a distribui%o'";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return false;
    }

    public function isClientePremium( $codigoCliente ) {

        $retorno = FALSE;

        $sql = "SELECT clipremium
                FROM clientes
                WHERE clioid =" . $codigoCliente ;

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $tupla =  pg_fetch_object($rs);
            $retorno =  $tupla->clipremium == 't' ? TRUE : FALSE;
        }

        return $retorno;

    }

    public function retiraMateriaisDoPrestador($codigoPrestador, $dados, $tipo){

        $retorno = array();
        $idProduto = $dados['prdoid'];
        $ordemServico = $dados['ordoid'];
        $quantidade = $dados['quantidade'];
        $dataAgendamento = $dados['dataAgendamento'];
        $idReservaAgendamentoItem = $dados['raioid'];
        $idReservaAgendamento = $dados['rairagoid'];
        $idOrdemServicoAgenda = $dados['ragosaoid'];

        if($tipo == self::ESTOQUE_RESERVADO) {

            $sql = " UPDATE
                        reserva_agendamento_item
                     SET 
                        raiqtde_estoque = raiqtde_estoque - $quantidade
                     FROM
                         reserva_agendamento, ordem_servico
                    WHERE 
                        rairagoid = ragoid
                    AND 
                        ordoid = ragordoid
                    AND
                        raioid = $idReservaAgendamentoItem
                    AND
                        rairagoid = $idReservaAgendamento
                    AND
                       raiprdoid = $idProduto
                    AND 
                       ragrepoid = $codigoPrestador 
                    AND
                        raidt_exclusao IS NULL
                    AND 
                        raiqtde_estoque > 0
                    RETURNING raioid, rairagoid, raiprdoid, ragrepoid, ordoid ";

            $rs = $this->executarQuery($sql);

            if (pg_affected_rows($rs) > 0) {
                return pg_fetch_all($rs);
            }
        }else if(self::ESTOQUE_EM_TRANSITO_RESERVADO){
            $sql = " UPDATE
                        reserva_agendamento_item
                     SET 
                        raiqtde_transito = raiqtde_transito - $quantidade
                     FROM
                         reserva_agendamento, ordem_servico
                    WHERE 
                        rairagoid = ragoid
                    AND 
                        ordoid = ragordoid
                    AND
                        raioid = $idReservaAgendamentoItem
                    AND
                        rairagoid = $idReservaAgendamento
                    AND
                       raiprdoid = $idProduto
                    AND 
                       ragrepoid = $codigoPrestador 
                    AND
                        raidt_exclusao IS NULL
                    AND 
                        raiqtde_transito > 0
                    RETURNING raioid, rairagoid, raiprdoid, ragrepoid, ordoid";

            $rs = $this->executarQuery($sql);

            if (pg_affected_rows($rs) > 0) {
                return pg_fetch_all($rs);
            }

        }

        return false;
    }

    public function getDadosDaAgenda($ordoid){

        $sql = " SELECT osaoid, osadata, osahora
			       FROM ordem_servico_agenda
			      WHERE osaordoid = $ordoid
			        AND osaexclusao IS NULL
			        AND osamotivo_cancelamento IS NULL ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return false;
    }

    public function getEstoqueDisponivel($repoid, $prdoid){

        $retorno = array();

        $sql =" select
                    prdoid,
                    sum((SELECT COUNT(1) as qtde_disponivel WHERE equeqsoid=3 AND equeqsoid=eqsoid)) as quantidade_estoque
                    from equipamento,
                        produto,
                        equipamento_versao,
                        equipamento_classe,
                        equipamento_status,
                        relacionamento_representante,
                        equipamento_projeto
                        WHERE
                            equrelroid=relroid
                        AND equeveoid=eveoid
                        AND equprdoid = prdoid
                        AND eveprojeto=eproid
                        AND eveeqcoid=eqcoid
                        AND equeqsoid=eqsoid
                        and prdoid = $prdoid
                        AND prdptioid in (1, 2)
                        AND equeqsoid in (3)
                        AND equdt_exclusao is null
                        AND relrrep_terceirooid = $repoid
                    group by prdoid ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;
    }

    public function getFlagAntecipacaoReservaMateriais(){

        $sql = " SELECT 
                    pcsidescricao
                FROM
                    parametros_configuracoes_sistemas_itens
                WHERE 
                    pcsipcsoid = 'SMART_AGENDA' 
                AND 
                    pcsioid = 'ANTECIPACAO_RESERVA_MATERIAL' 
                AND 
                    pcsidt_exclusao IS null ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows ($rs) > 0) {
            $flag = pg_fetch_object($rs);

            $retorno = $flag->pcsidescricao == '1' ? TRUE : FALSE;
        }

        return $retorno;
    }
}

?>