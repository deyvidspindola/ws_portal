<?php

/**
 * Classe RelProdutosSolicitadosDAO.
 * Camada de modelagem de dados.
 *
 * @package Relatorio
 * @author  Joใo Paulo Tavares da Silva <joao.silva@meta.com.br>
 *
 */
class RelProdutosSolicitadosDAO{


    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	private $conn;
    private $usuarioLogado;

	public function __construct(){
		global $conn;
        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->usuarioLogado = empty($this->usuarioLogado) ? 2750 : intval($this->usuarioLogado);
	}

	public function buscarStatusSolicitacao($saisoid = null){

       $where = (!empty($saisoid)) ? "saisoid = $saisoid" : 'TRUE';

		$sql = "SELECT
					saisoid,
					saisdescricao AS descricao
		 		FROM
		 			solicitacao_agendamento_item_status
                WHERE
                    $where
                ORDER BY
		 			saisdescricao";

		$rs = pg_query($this->conn, $sql);

		$listaItens = array();
		while($row = pg_fetch_object($rs)) {

			$listaItens[] = $row;

			$i++;
		}

		return $listaItens;
	}

    public function buscarOsTipo() {
        $retorno = array();
        $sql     = "
            SELECT
                os_tipo.ostoid AS oid,
                os_tipo.ostdescricao AS descricao
            FROM
                os_tipo
            WHERE
                os_tipo.ostdt_exclusao IS NULL
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarEstados() {
        $retorno = array();
        $sql     = "
            SELECT
                uf.ufoid AS oid,
                uf.ufuf AS descricao
            FROM
                uf
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarCidade(stdClass $param) {
        $retorno = array();
        $sql     = "
            SELECT
                cidade.cidoid AS oid,
                cidade.ciddescricao AS descricao
            FROM
                cidade
            WHERE
                cidade.cidexclusao IS NULL
        ";

        if (isset($param->ufuf)) {
            $sql.= "
                AND
                    cidade.ciduf = '".$param->ufuf."'
            ";
        }

        $sql.= "
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarRepresentantes() {
        $retorno = array();
        $sql     = "
            SELECT
                representante.repoid AS oid,
                representante.reprazao AS descricao
            FROM
                representante
            WHERE
                representante.repexclusao IS NULL
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarEquipamentoClasse() {
        $retorno = array();
        $sql     = "
            SELECT
                equipamento_classe.eqcoid AS oid,
                equipamento_classe.eqcdescricao AS descricao
            FROM
                equipamento_classe
            ORDER BY
                descricao
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarProdutos() {
    	$retorno = array();
    	$sql     = "SELECT
					    prdoid,
					    prdproduto,
                        prdpremium
				    FROM
				    	produto
				    ORDER BY
				    	prdproduto";


    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}

    	while ($registro = pg_fetch_object($rs)) {
    		$retorno[] = $registro;
    	}

    	return $retorno;
    }

	public function buscarSolicitacoes(stdClass $param, $distinctRepoid){
		$retorno = array();

        if($distinctRepoid){
            $distinct = 'DISTINCT ON (sagoid)';
        }else{
            $distinct = '';
        }
	    $sql = "
			 SELECT $distinct
                repoid,
                sagoid,
                solicitacao_agendamento.sagdt_cadastro AS dt_solicitacao, --OK
                sa2.saisdescricao AS status_solicitacao, --OK
                sais_item.saisdescricao AS status_item,
                ordem_servico.ordoid   AS num_os, -- ok

                (CASE WHEN sagosaoid IS NULL THEN
                        osadata
                ELSE
                        (SELECT osadata FROM ordem_servico_agenda osa WHERE osaordoid = ordoid and osaoid = sagosaoid)
                END) AS dt_agendamento,

                (SELECT
                        array_to_string(array_agg(DISTINCT ostdescricao), ', ')
                FROM
                        ordem_servico_item
                INNER JOIN
                        os_tipo_item ON
                            (
                                ordem_servico_item.ositotioid = os_tipo_item.otioid
                            AND
                                os_tipo_item.otidt_exclusao IS NULL
                            )
                INNER JOIN
                        os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
                               WHERE
                            (
                                ordem_servico.ordoid = ordem_servico_item.ositordoid
                            AND
                                ordem_servico_item.ositexclusao IS NULL
                            )
                ) AS tipo_os,
                equipamento_classe.eqcdescricao AS classe_cliente, --ok
                representante.reprazao            AS representante, --ok
                usuarios.nm_usuario             AS usuario, --ok
                endereco_representante.endvcidade AS cidade,
                endereco_representante.endvuf AS estado,
                produto.prdproduto AS produto,
                (CASE WHEN clientes.clipremium IS TRUE THEN 'NรO' ELSE 'SIM' END) AS permite_similar,
                solicitacao_agendamento_item.saijustificativa_recusa AS recusa,
                (SELECT raiesroid
                FROM reserva_agendamento
                JOIN reserva_agendamento_item ON rairagoid = ragoid
                WHERE ragsagoid = sagoid
                ORDER BY raioid DESC
                LIMIT 1) AS nr_remessa,
                CASE
                    WHEN (sagordoid IS NOT NULL AND sagosaoid IS NOT NULL AND sagfalta_critica = false)
                    THEN true
                    ELSE false
                END AS flag_agendamento
            FROM solicitacao_agendamento

            INNER JOIN solicitacao_agendamento_item ON solicitacao_agendamento_item.saisagoid = solicitacao_agendamento.sagoid

            INNER JOIN solicitacao_agendamento_item_status AS sais_item ON sais_item.saisoid = solicitacao_agendamento_item.saisaisoid

            INNER JOIN solicitacao_agendamento_item_status AS sa2 ON sa2.saisoid = solicitacao_agendamento.sagsaisoid

            INNER JOIN ordem_servico ON ordem_servico.ordoid =  solicitacao_agendamento.sagordoid

            INNER JOIN clientes ON clientes.clioid = ordem_servico.ordclioid

            LEFT JOIN ordem_servico_agenda ON ordem_servico_agenda.osaordoid = ordem_servico.ordoid

            INNER JOIN contrato ON contrato.connumero = ordem_servico.ordconnumero

            LEFT JOIN equipamento_classe ON contrato.coneqcoid = equipamento_classe.eqcoid

            INNER JOIN representante ON representante.repoid = sagrepoid

            INNER JOIN usuarios ON usuarios.cd_usuario = solicitacao_agendamento.sagusuoid

            LEFT JOIN endereco_representante ON representante.repoid = endereco_representante.endvrepoid

            INNER JOIN produto ON produto.prdoid = solicitacao_agendamento_item.saiprdoid

            INNER JOIN ordem_servico_item ON ositordoid = ordoid

            INNER JOIN os_tipo_item ON ositotioid = otioid

            INNER JOIN os_tipo ON os_tipo.ostoid = os_tipo_item.otiostoid

            WHERE
                0 = 0
                --AND osaexclusao IS NULL ";


		if(!empty($param->sagdt_cadastro_inicial) AND !empty($param->sagdt_cadastro_final)){
			 $sql.= "
                    AND
                        solicitacao_agendamento.sagdt_cadastro BETWEEN '".$param->sagdt_cadastro_inicial." 00:00:00' AND '".$param->sagdt_cadastro_final." 23:59:59'
                    ";
		}

		if (!empty($param->ostoid)) {
            $sql.= "
                AND
                     otiostoid = ".intval($param->ostoid)."
            ";
        }

        if(!empty($param->saisoid)){
        	$sql.= "
        		AND
        		   solicitacao_agendamento.sagsaisoid = ".intval($param->saisoid)."
        	";
        }
        if (!empty($param->ufuf)) {
            $sql.= "
                AND
                    endereco_representante.endvuf = '".$param->ufuf."'
            ";
        }

        if (!empty($param->ciddescricao)) {
            $sql.= "
                AND
                    endereco_representante.endvcidade = '".$param->ciddescricao."'
            ";
        }

        if (!empty($param->repoid)) {
            $sql.= "
                AND
                    representante.repoid = ".intval($param->repoid)."
            ";
        }

        if (!empty($param->eqcoid)) {
            $sql.= "
                AND
                    equipamento_classe.eqcoid = ".intval($param->eqcoid)."
            ";
        }

        if (!empty($param->prdoid)) {
            $sql.= "
                AND
                    produto.prdoid = ".intval($param->prdoid)."
            ";
        }



        $sql.="GROUP BY
        			repoid,
					sagoid,
					saioid,
					dt_solicitacao,
					status_solicitacao,
					num_os,
					dt_agendamento,
					tipo_os,
					classe_cliente,
					representante,
					usuario,
					cidade,
					estado,
					produto,
                    permite_similar,
                    status_item";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }


        return $retorno;
	}

    public function buscarSolicitacoesSintetico(stdClass $param, $distinct){
        $distinct = '';
        $retorno  = array();

        if ($distinct) {
            $distinct = 'DISTINCT ON (sagoid)';
        }

        $sql = "    SELECT
                        sub_1.*,
                        COUNT(sub_1.sagtotal) OVER (
                            PARTITION BY
                                sub_1.repoid
                        ) AS replinha
                    FROM (
                        SELECT
                            SUM(sub_2.saiqtde_solicitacao) AS sagtotal,
                            sub_2.prdoid,
                            sub_2.prdproduto,
                            sub_2.permite_similar,
                            sub_2.eqcoid,
                            sub_2.eqcdescricao,
                            sub_2.repoid,
                            sub_2.reprazao
                        FROM (
                            SELECT DISTINCT
                                solicitacao_agendamento.sagoid,
                                solicitacao_agendamento_item.saiqtde_solicitacao,
                                produto.prdoid,
                                produto.prdproduto,
                                (CASE WHEN clientes.clipremium IS TRUE THEN 'NรO' ELSE 'SIM' END) AS permite_similar,
                                equipamento_classe.eqcoid,
                                equipamento_classe.eqcdescricao,
                                representante.repoid,
                                representante.reprazao
                            FROM solicitacao_agendamento
                            INNER JOIN solicitacao_agendamento_item_status AS solicitacao_agendamento_status
                                ON solicitacao_agendamento.sagsaisoid = solicitacao_agendamento_status.saisoid
                            INNER JOIN solicitacao_agendamento_item
                                ON solicitacao_agendamento.sagoid = solicitacao_agendamento_item.saisagoid
                            INNER JOIN solicitacao_agendamento_item_status
                                ON solicitacao_agendamento_item.saisaisoid = solicitacao_agendamento_item_status.saisoid
                            INNER JOIN produto
                                ON solicitacao_agendamento_item.saiprdoid = produto.prdoid
                            INNER JOIN ordem_servico
                                ON solicitacao_agendamento.sagordoid = ordem_servico.ordoid
                            INNER JOIN clientes 
                                ON clientes.clioid = ordem_servico.ordclioid
                            LEFT JOIN ordem_servico_agenda
                                ON ordem_servico.ordoid = ordem_servico_agenda.osaordoid
                            INNER JOIN contrato
                                ON ordem_servico.ordconnumero = contrato.connumero
                            LEFT JOIN equipamento_classe
                                ON equipamento_classe.eqcoid = contrato.coneqcoid
                            INNER JOIN ordem_servico_item
                                ON ordem_servico.ordoid = ordem_servico_item.ositordoid
                            INNER JOIN os_tipo_item
                                ON ordem_servico_item.ositotioid = os_tipo_item.otioid
                            INNER JOIN os_tipo
                                ON os_tipo_item.otiostoid = os_tipo.ostoid
                            INNER JOIN representante
                                ON solicitacao_agendamento.sagrepoid = representante.repoid
                            LEFT JOIN endereco_representante
                                ON endereco_representante.endvrepoid = representante.repoid
                            INNER JOIN usuarios
                                ON solicitacao_agendamento.sagusuoid = usuarios.cd_usuario
                            WHERE 0 = 0
        ";

        if (!empty($param->sagdt_cadastro_inicial) and !empty($param->sagdt_cadastro_final)) {
            $sql.= "        AND solicitacao_agendamento.sagdt_cadastro BETWEEN '".$param->sagdt_cadastro_inicial." 00:00:00' AND '".$param->sagdt_cadastro_final." 23:59:59'
            ";
        }

        if (!empty($param->ostoid)) {
            $sql.= "        AND otiostoid = ".intval($param->ostoid)."
            ";
        }

        if (!empty($param->saisoid)) {
            $sql.= "        AND solicitacao_agendamento.sagsaisoid = ".intval($param->saisoid)."
            ";
        }

        if (!empty($param->ufuf)) {
            $sql.= "        AND endereco_representante.endvuf = '".$param->ufuf."'
            ";
        }

        if (!empty($param->ciddescricao)) {
            $sql.= "        AND endereco_representante.endvcidade = '".$param->ciddescricao."'
            ";
        }

        if (!empty($param->repoid)) {
            $sql.= "        AND representante.repoid = ".intval($param->repoid)."
            ";
        }

        if (!empty($param->eqcoid)) {
            $sql.= "        AND equipamento_classe.eqcoid = ".intval($param->eqcoid)."
            ";
        }

        if (!empty($param->prdoid)) {
            $sql.= "        AND produto.prdoid = ".intval($param->prdoid)."
            ";
        }

        $sql.= "        ) AS sub_2
                        GROUP BY
                            sub_2.prdoid,
                            sub_2.prdproduto,
                            sub_2.permite_similar,
                            sub_2.eqcoid,
                            sub_2.eqcdescricao,
                            sub_2.repoid,
                            sub_2.reprazao
                    ) AS sub_1
                    ORDER BY
                        reprazao,
                        repoid,
                        eqcdescricao,
                        eqcoid,
                        prdproduto,
                        prdoid
        ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $representante = 0;

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function buscarRepresentante($repoid) {
        $retorno = new stdClass();

        $sql = "
            SELECT
                representante.reprazao AS nome,
                representante.repnome AS nomeFantasia,
                LPAD(representante.repcgc::VARCHAR, 14, '0') AS cnpj,
                endereco_representante.endvrua AS endereco,
                endereco_representante.endvnumero AS enderecoNumero,
                endereco_representante.endvcomplemento AS enderecoComplemento,
                endereco_representante.endvcidade AS cidade,
                endereco_representante.endvuf AS estado,
                endereco_representante.endvddd || endereco_representante.endvfone AS telefone,
                representante.repcontato AS contato,
                representante.repcontato_ddd || representante.repcontato_fone AS contatoTelefone,
                representante.repe_mail AS email,
                CASE
                    WHEN representante.reprevenda THEN
                        'REVENDA'
                    WHEN representante.repinstalacao THEN
                        'INSTALAวรO'
                    WHEN representante.repassistencia THEN
                        'ASSISTสNCIA'
                    WHEN representante.repcentral THEN
                        'CENTRAL'
                    ELSE
                        ''
                END AS funcao
            FROM
                representante
            INNER JOIN
                endereco_representante ON representante.repoid = endereco_representante.endvrepoid
            WHERE
                representante.repexclusao IS NULL
        ";

        if (!empty($repoid)) {
            $sql.= "
                AND
                    representante.repoid = ".$repoid."
            ";
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno = $row;
        }

        return $retorno;
    }

    public function buscarItemAgendamento($sagoid){

        $retorno = array();

        $sql = "SELECT
                    solicitacao_agendamento_item.saiqtde_solicitacao AS qtd,
                    solicitacao_agendamento_item.saioid AS saioid,
                    solicitacao_agendamento_item.saijustificativa_recusa AS recusa,
                    solicitacao_agendamento_item_status.saisdescricao AS status,
                    produto.prdproduto AS produto,
                    (CASE WHEN clientes.clipremium IS TRUE THEN 'NรO' ELSE 'SIM' END) AS permite_similar
                FROM solicitacao_agendamento_item
                INNER JOIN
                    solicitacao_agendamento_item_status ON solicitacao_agendamento_item_status.saisoid = solicitacao_agendamento_item.saisaisoid
                INNER JOIN
                    produto ON produto.prdoid = solicitacao_agendamento_item.saiprdoid
                INNER JOIN
                    solicitacao_agendamento ON solicitacao_agendamento.sagoid = solicitacao_agendamento_item.saisagoid
                INNER JOIN 
                    ordem_servico ON solicitacao_agendamento.sagordoid = ordem_servico.ordoid
                INNER JOIN 
                    clientes ON clientes.clioid = ordem_servico.ordclioid
                WHERE ";
        if (!empty($sagoid)) {
            $sql.= "
                solicitacao_agendamento_item.saisagoid = ".$sagoid."
            ";
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;
    }

    public function qtdItensAtendidos($sagoid){
         $sql = "SELECT
                    solicitacao_agendamento_item.saioid AS saioid
                FROM solicitacao_agendamento_item
                WHERE saisaisoid = 7 ";
        if(!empty($sagoid)){
            $sql.= 'AND saisagoid = '.$sagoid;
        }
        if($rs = pg_query($this->conn, $sql)){
            $rs = pg_num_rows($rs);
            return $rs;
        }else{
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function qtdItensRecusados($sagoid){
         $sql = "SELECT
                    solicitacao_agendamento_item.saioid AS saioid
                FROM solicitacao_agendamento_item
                WHERE saisaisoid = 9 ";
        if(!empty($sagoid)){
            $sql.= 'AND saisagoid = '.$sagoid;
        }
        if($rs = pg_query($this->conn, $sql)){
            $rs = pg_num_rows($rs);
            return $rs;
        }else{
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function atualizarStatusItem($saioid, $status, $justificativa = null){

        if(!empty($saioid) and !empty($status)){
            $sql = "UPDATE solicitacao_agendamento_item
                    SET saisaisoid=".$status;

            if(!is_null($justificativa)){
                $sql.= ', saijustificativa_recusa = '. "'".$justificativa."'";
            }
            $sql.=" WHERE ";

            if(is_array($saioid)){
                $sql.= "saioid in (". implode(',',$saioid) . ")";
            }else{
                $sql.= "saioid = ". $saioid;
            }

        }else{
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!pg_query($this->conn, $sql)) {

            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function atualizarStatusSolicitacao($sagoid, $status){

        if(!empty($sagoid) and !empty($status)){
            $sql = "UPDATE solicitacao_agendamento
                    SET sagsaisoid=".$status."
                    WHERE ";
            $sql.= "sagoid = ". $sagoid;

        }else{
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (!pg_query($this->conn, $sql)) {

            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function getSolicitacaoInfo($sagoid) {
        if(!empty($sagoid)) {
            $sql = "SELECT
                        sagordoid,
                        sagosaoid,
                        sagobservacao,
                        usuemail,
                        nm_usuario,
                        saisdescricao,
                        sagfalta_critica,
                        CASE
                            WHEN (sagordoid IS NOT NULL AND sagosaoid IS NOT NULL AND sagfalta_critica = false)
                            THEN true
                            ELSE false
                        END AS flag_agendamento
                    FROM usuarios
                    INNER JOIN solicitacao_agendamento
                    ON sagusuoid=cd_usuario
                    INNER JOIN solicitacao_agendamento_item_status
                    ON sagsaisoid=saisoid
                    WHERE sagoid=".$sagoid;
            if (!$rs = pg_query($this->conn, $sql)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $registro = pg_fetch_object($rs);

            return $registro;
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function getListaProdutosSolicitados($sagoid) {

        $lista = '';

        $sql = "SELECT
                    STRING_AGG(prdproduto, ', ') AS lista_produtos
                FROM
                    solicitacao_agendamento_item
                INNER JOIN
                    produto ON (prdoid = saiprdoid)
                WHERE
                    saisagoid  = " . intval($sagoid);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $registro = pg_fetch_object($rs);

        $lista = isset($registro->lista_produtos) ? $registro->lista_produtos : '';

        return $lista;

    }

    public function registraHistoricoOS($ordoid, $msg){

        $ordoid = intval($ordoid);


       $sql = "INSERT INTO ordem_situacao
                (orsordoid, orsusuoid, orssituacao, orsstatus, orsdt_agenda, orshr_agenda)
                VALUES
                (
                    $ordoid,
                    ". $this->usuarioLogado . ",
                    '$msg',
                    (SELECT mhcoid FROM motivo_hist_corretora WHERE mhcdescricao ILIKE 'Solicita__o para a distribui__o'),
                    (SELECT osadata FROM ordem_servico_agenda WHERE osaordoid = $ordoid ORDER BY osaoid DESC LIMIT 1),
                    (SELECT osahora FROM ordem_servico_agenda WHERE osaordoid = $ordoid ORDER BY osaoid DESC LIMIT 1)

                )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function begin(){
        $sql = 'BEGIN;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function rollback(){
        $sql = 'ROLLBACK;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
    public function commit(){
        $sql = 'COMMIT;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
}
?>