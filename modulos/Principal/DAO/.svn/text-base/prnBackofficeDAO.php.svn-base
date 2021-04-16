<?php

/**
 * Classe prnBackofficeDAO.
 * Camada de modelagem de dados.
 *
 * @package  Principal
 * @author   Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 *
 */
class prnBackofficeDAO {

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
    public function pesquisar(stdClass $parametros) {



        $retorno = array();

        $sql = "
        	SELECT
				bacoid,
				TO_CHAR(bacdt_solicitacao,'DD/MM/YYYY HH24:MI') as bacdt_solicitacao,
				clinome,
				bacplaca,
				tpcdescricao,
				bmsdescricao,
				CASE WHEN bacstatus = 'C' THEN 'Concluido'
				WHEN bacstatus = 'A' THEN 'Em Andamento'
				WHEN bacstatus = 'P' THEN 'Pendente'
				END as status,
				bactpcoid,
				TO_CHAR( (EXTRACT (EPOCH FROM (bacdt_conclusao - bacdt_solicitacao))* INTERVAL '1 second') ,'HH24:MI')as data,
				nm_usuario,
        		clcuf_sg,
        		clcnome
			FROM
				backoffice
				INNER JOIN clientes on bacclioid = clioid
				INNER JOIN tipo_contrato on tpcoid = bactpcoid
				INNER JOIN backoffice_motivo_solicitacao on bmsoid = bacbmsoid
				INNER JOIN usuarios on bacusuoid_atendente = cd_usuario
        		LEFT JOIN correios_localidades ON clcoid = bacclcoid
			WHERE
				TRUE ";

        //Filtro por periodo (Obrigatório)
        if ((isset($parametros->dt_evento_de) && trim($parametros->dt_evento_de) != '') && (isset($parametros->dt_evento_ate) && trim($parametros->dt_evento_ate) != '')) {

            $sql .= "AND
                          bacdt_solicitacao BETWEEN  '" . $parametros->dt_evento_de . " 00:00:01'  AND '" . $parametros->dt_evento_ate . " 23:59:59' ";
        }

        //Filtro por status (Não obrigatório)
        if (isset($parametros->status) && trim($parametros->status) != '') {
            $sql .= "AND bacstatus = '" . $parametros->status . "' ";
        }

        //Filtro por cliente (Não obrigatório)
        if (isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%" . trim($parametros->cliente) . "%' ";
        }

        //Filtro por placa (Não obrigatório)
        if (isset($parametros->placa) && trim($parametros->placa) != '') {
            $sql .= "AND upper(bacplaca) = '" . strtoupper($parametros->placa) . "' ";
        }

        //Filtro por tipo contrato (Não obrigatório)
        if (isset($parametros->tipo_contrato) && trim($parametros->tipo_contrato) != '') {
            $sql .= "AND bactpcoid = '" . $parametros->tipo_contrato . "' ";
        }

        //Filtro por tipo atendente (Não obrigatório)
        if (isset($parametros->tipo_atendente) && trim($parametros->tipo_atendente) != '') {
            $sql .= "AND bacusuoid_atendente = '" . $parametros->tipo_atendente . "' ";
        }

        //Filtro por Motivo (Não obrigatório)
        if (isset($parametros->tipo_motivo) && trim($parametros->tipo_motivo) != '') {
            $sql .= "AND bacbmsoid = '" . $parametros->tipo_motivo . "' ";
        }

        //Filtro por UF (Não obrigatório)
        if (isset($parametros->uf) && trim($parametros->uf) != '') {
            $sql .= "AND bacestoid = " . $parametros->uf . " ";
        }

        //Filtro por Cidade (Não obrigatório)
        if (isset($parametros->cidade) && trim($parametros->cidade) != '') {
            $sql .= "AND bacclcoid = " . $parametros->cidade . " ";
        }

        $sql .= " ORDER BY bacoid ASC";

		//echo $sql;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa sintetica
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarSinteticoPorMotivo(stdClass $parametros) {
        $retorno = array();

        $sql = "SELECT count(bacoid) AS solicitacoes,
			       bmsdescricao AS motivo
			FROM backoffice
			INNER JOIN clientes ON bacclioid = clioid
			INNER JOIN tipo_contrato ON tpcoid = bactpcoid
			INNER JOIN backoffice_motivo_solicitacao ON bmsoid = bacbmsoid
			INNER JOIN usuarios ON bacusuoid_atendente = cd_usuario
			WHERE 1 = 1 \n";

        //Filtro por periodo (Obrigatório)
        if ((isset($parametros->dt_evento_de) && trim($parametros->dt_evento_de) != '') && (isset($parametros->dt_evento_ate) && trim($parametros->dt_evento_ate) != '')) {
            $sql .= "AND bacdt_solicitacao BETWEEN  '" . $parametros->dt_evento_de . " 00:00:01'  AND '" . $parametros->dt_evento_ate . " 23:59:59' \n";
        }

        //Filtro por status (Não obrigatório)
        if (isset($parametros->status) && trim($parametros->status) != '') {
            $sql .= "AND bacstatus = '" . $parametros->status . "' \n";
        }

        //Filtro por cliente (Não obrigatório)
        if (isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%" . trim($parametros->cliente) . "%' \n";
        }

        //Filtro por placa (Não obrigatório)
        if (isset($parametros->placa) && trim($parametros->placa) != '') {
            $sql .= "AND upper(bacplaca) = '" . strtoupper($parametros->placa) . "' \n";
        }

        //Filtro por tipo contrato (Não obrigatório)
        if (isset($parametros->tipo_contrato) && trim($parametros->tipo_contrato) != '') {
            $sql .= "AND bactpcoid = '" . $parametros->tipo_contrato . "' \n";
        }

        //Filtro por tipo atendente (Não obrigatório)
        if (isset($parametros->tipo_atendente) && trim($parametros->tipo_atendente) != '') {
            $sql .= "AND bacusuoid_atendente = '" . $parametros->tipo_atendente . "' \n";
        }

        //Filtro por Motivo (Não obrigatório)
        if (isset($parametros->tipo_motivo) && trim($parametros->tipo_motivo) != '') {
            $sql .= "AND bacbmsoid = '" . $parametros->tipo_motivo . "' \n";
        }

        //Filtro por UF (Não obrigatório)
        if (isset($parametros->uf) && trim($parametros->uf) != '') {
            $sql .= "AND bacestoid = " . $parametros->uf . " ";
        }

        //Filtro por Cidade (Não obrigatório)
        if (isset($parametros->cidade) && trim($parametros->cidade) != '') {
            $sql .= "AND bacclcoid = " . $parametros->cidade . " ";
        }

        $sql .= "GROUP BY bmsdescricao " .
                "ORDER BY bmsdescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa sintetica, agrupado por atendente
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarSinteticoPorAtendente(stdClass $parametros) {
        $retorno = array();

        $sql = "SELECT cd_usuario,
                 nm_usuario,
                 count(CASE WHEN bacstatus='C' THEN 1 ELSE NULL END) AS concluido,
                 count(CASE WHEN bacstatus='A' THEN 1 ELSE NULL END) AS andamento,
                 count(CASE WHEN bacstatus='P' THEN 1 ELSE NULL END) AS pendente
          FROM backoffice
          INNER JOIN clientes ON bacclioid = clioid
          INNER JOIN tipo_contrato ON tpcoid = bactpcoid
          INNER JOIN backoffice_motivo_solicitacao ON bmsoid = bacbmsoid
          INNER JOIN usuarios ON bacusuoid_atendente = cd_usuario
          WHERE 1 = 1 \n";

        //Filtro por periodo (Obrigatório)
        if ((isset($parametros->dt_evento_de) && trim($parametros->dt_evento_de) != '') && (isset($parametros->dt_evento_ate) && trim($parametros->dt_evento_ate) != '')) {
            $sql .= "AND bacdt_solicitacao BETWEEN  '" . $parametros->dt_evento_de . " 00:00:01'  AND '" . $parametros->dt_evento_ate . " 23:59:59' \n";
        }

        //Filtro por status (Não obrigatório)
        if (isset($parametros->status) && trim($parametros->status) != '') {
            $sql .= "AND bacstatus = '" . $parametros->status . "' \n";
        }

        //Filtro por cliente (Não obrigatório)
        if (isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%" . trim($parametros->cliente) . "%' \n";
        }

        //Filtro por placa (Não obrigatório)
        if (isset($parametros->placa) && trim($parametros->placa) != '') {
            $sql .= "AND upper(bacplaca) = '" . strtoupper($parametros->placa) . "' \n";
        }

        //Filtro por tipo contrato (Não obrigatório)
        if (isset($parametros->tipo_contrato) && trim($parametros->tipo_contrato) != '') {
            $sql .= "AND bactpcoid = '" . $parametros->tipo_contrato . "' \n";
        }

        //Filtro por tipo atendente (Não obrigatório)
        if (isset($parametros->tipo_atendente) && trim($parametros->tipo_atendente) != '') {
            $sql .= "AND bacusuoid_atendente = '" . $parametros->tipo_atendente . "' \n";
        }

        //Filtro por Motivo (Não obrigatório)
        if (isset($parametros->tipo_motivo) && trim($parametros->tipo_motivo) != '') {
            $sql .= "AND bacbmsoid = '" . $parametros->tipo_motivo . "' \n";
        }

        //Filtro por UF (Não obrigatório)
        if (isset($parametros->uf) && trim($parametros->uf) != '') {
            $sql .= "AND bacestoid = " . $parametros->uf . " ";
        }

        //Filtro por Cidade (Não obrigatório)
        if (isset($parametros->cidade) && trim($parametros->cidade) != '') {
            $sql .= "AND bacclcoid = " . $parametros->cidade . " ";
        }

        $sql .= "GROUP BY cd_usuario, nm_usuario
          ORDER BY nm_usuario";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa sintetica, tempo e status
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisarSinteticoPorTempo(stdClass $parametros) {
        $retorno = array();

        $sql = "SELECT bacoid,
               bacdt_solicitacao,
               CASE
                   WHEN bacdt_conclusao IS NULL THEN now()
                   ELSE bacdt_conclusao
               END AS dt_conclusao,
               bacstatus,
               (CASE
                   WHEN bacdt_conclusao IS NULL THEN now()
                   ELSE bacdt_conclusao
               END)-bacdt_solicitacao as diffe,
               CASE
                   WHEN bacstatus = 'C' THEN 'Concluido'
                   WHEN bacstatus = 'A' THEN 'Em Andamento'
                   WHEN bacstatus = 'P' THEN 'Pendente'
               END AS status
        FROM backoffice
        INNER JOIN clientes ON bacclioid = clioid
        INNER JOIN tipo_contrato ON tpcoid = bactpcoid
        INNER JOIN backoffice_motivo_solicitacao ON bmsoid = bacbmsoid
        INNER JOIN usuarios ON bacusuoid_atendente = cd_usuario
        WHERE 1 = 1 \n";

        //Filtro por periodo (Obrigatório)
        if ((isset($parametros->dt_evento_de) && trim($parametros->dt_evento_de) != '') && (isset($parametros->dt_evento_ate) && trim($parametros->dt_evento_ate) != '')) {
            $sql .= "AND bacdt_solicitacao BETWEEN  '" . $parametros->dt_evento_de . " 00:00:01'  AND '" . $parametros->dt_evento_ate . " 23:59:59' \n";
        }

        //Filtro por status (Não obrigatório)
        if (isset($parametros->status) && trim($parametros->status) != '') {
            $sql .= "AND bacstatus = '" . $parametros->status . "' \n";
        }

        //Filtro por cliente (Não obrigatório)
        if (isset($parametros->cliente) && trim($parametros->cliente) != '') {
            $sql .= "AND clinome ILIKE '%" . trim($parametros->cliente) . "%' \n";
        }

        //Filtro por placa (Não obrigatório)
        if (isset($parametros->placa) && trim($parametros->placa) != '') {
            $sql .= "AND upper(bacplaca) = '" . strtoupper($parametros->placa) . "' \n";
        }

        //Filtro por tipo contrato (Não obrigatório)
        if (isset($parametros->tipo_contrato) && trim($parametros->tipo_contrato) != '') {
            $sql .= "AND bactpcoid = '" . $parametros->tipo_contrato . "' \n";
        }

        //Filtro por tipo atendente (Não obrigatório)
        if (isset($parametros->tipo_atendente) && trim($parametros->tipo_atendente) != '') {
            $sql .= "AND bacusuoid_atendente = '" . $parametros->tipo_atendente . "' \n";
        }

        //Filtro por Motivo (Não obrigatório)
        if (isset($parametros->tipo_motivo) && trim($parametros->tipo_motivo) != '') {
            $sql .= "AND bacbmsoid = '" . $parametros->tipo_motivo . "' \n";
        }

        //Filtro por UF (Não obrigatório)
        if (isset($parametros->uf) && trim($parametros->uf) != '') {
            $sql .= "AND bacestoid = " . $parametros->uf . " ";
        }

        //Filtro por Cidade (Não obrigatório)
        if (isset($parametros->cidade) && trim($parametros->cidade) != '') {
            $sql .= "AND bacclcoid = " . $parametros->cidade . " ";
        }

        $sql .= "ORDER BY bacoid,
             bacdt_solicitacao,
             dt_conclusao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $minuto = 60;
        $hora = $minuto * 60;
        $dia = $hora * 24;
        $mes = $dia * 30;
        while ($row = pg_fetch_object($rs)) {
            $bacstatus = $row->bacstatus;
            $dt_solicitacao = $row->bacdt_solicitacao;
            $dt_conclusao = $row->dt_conclusao;
            $diff = strtotime($dt_conclusao) - strtotime($dt_solicitacao);

            $chave = '';
            if ($diff >= $mes)
                $chave = '9_maior1mes';
            if ($diff < $mes)
                $chave = '8_menor1mes';
            if ($diff < 5 * $dia)
                $chave = '7_menor5dias';
            if ($diff < 3 * $dia)
                $chave = '6_menor3dias';
            if ($diff < 2 * $dia)
                $chave = '5_menor2dias';
            if ($diff < $dia)
                $chave = '4_menor1dia';
            if ($diff < 12 * $hora)
                $chave = '3_menor12horas';
            if ($diff < 6 * $hora)
                $chave = '2_menor6horas';
            if ($diff < 2 * $hora)
                $chave = '1_menor2horas';

            if (!isset($retorno[$chave][$bacstatus]))
                $retorno[$chave][$bacstatus] = 0;

            $retorno[$chave][$bacstatus] ++;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa de apenas um registro de Tipo de Contrato.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarTipoContrato() {

        $sql = "SELECT
                    tpcoid,
                    tpcdescricao
                FROM
                    tipo_contrato
                WHERE
                    tpcativo ='t'
                ORDER BY
                    tpcdescricao";

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
     * Método para realizar a pesquisa de apenas um registro de atendente.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarAtendente() {

        $sql = "SELECT
                    cd_usuario,
                    nm_usuario
                FROM
                    usuarios
                WHERE
                    usudepoid =8
                    AND usucargooid = 24
                ORDER BY
                    nm_usuario";

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
     * Método para realizar a pesquisa de apenas um registro de atendente.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarAtendenteLogado() {

        $this->cd_usuario = $_SESSION['usuario']['oid'];

        $sql = "SELECT
                    cd_usuario,
                    nm_usuario
                FROM
                    usuarios
                WHERE
                    cd_usuario = $this->cd_usuario ";


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
     * Método para realizar a pesquisa de apenas um registro de Motivo.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarMotivo($id = null) {

        if ($id != null) {
            $where = "AND bmsoid = {$id}";
        }

        $sql = "SELECT
                    bmsoid,
                    bmsdescricao
                FROM
                    backoffice_motivo_solicitacao
                WHERE
                    bmsusuoid_exclusao is null
                    $where
                ORDER BY
                    bmsdescricao";

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
     * Realiza pesquisa dinamica por nome / razao socail
     * @param string $filtro
     * @param string $parametro
     * @return string
     */
    public function retornarPesquisaDinamicaNome($filtro, $parametro) {

        $retorno = array();
        $tipo = '';
        $chave = 0;
        $parametro = pg_escape_string($parametro);

        if (empty($filtro) || empty($parametro)) {

            return $retorno;
        }


        $sql = "
                SELECT
                    DISTINCT clinome AS nome,
                    clioid,
                    (
                        CASE WHEN clitipo = 'F' THEN
                           clifone_res
                        ELSE
                            clifone_com
                        END
                    ) AS telefone,
                    (
                        CASE WHEN clitipo = 'F' THEN
                           clino_cpf
                        ELSE
                            clino_cgc
                        END
                    ) AS cpf_cgc,
                    clitipo
                FROM
                    clientes
                WHERE
                    clidt_exclusao IS NULL
                AND
                    clinome ILIKE '" . $parametro . "%'
                ORDER BY clinome
                LIMIT 100
                ";

        //echo '<pre>', $sql, '</pre>';

        $rs = pg_query($sql);

        while ($row = pg_fetch_object($rs)) {

            $retorno[$chave]['label'] = utf8_encode($row->nome);
            $retorno[$chave]['value'] = utf8_encode($row->nome);
            $retorno[$chave]['clitipo'] = utf8_encode($row->clitipo);
            $retorno[$chave]['telefone'] = utf8_encode($row->telefone);
            $retorno[$chave]['cpf_cgc'] = utf8_encode($row->cpf_cgc);
            $retorno[$chave]['id'] = $row->clioid;

            $chave++;
        }

        return $retorno;
    }

    /**
     * Realiza pesquisa dinamica por placa
     * @param string $filtro
     * @param string $parametro
     * @param boolean $validaExclusao Se vier true valida data de exclusão
     * @return string
     */
    public function retornarPesquisaDinamicaPlaca($parametro, $clioid, $validaExclusao) {
        global $conn;
        $retorno = array();
        $tipo = '';
        $chave = 0;
        $parametro = pg_escape_string($parametro);

        if (empty($parametro)) {
            return $retorno;
        }
        
        $condicaoContrato 	= "";
        $condicaoCliente 	= "";
        if($validaExclusao){
        	$condicaoContrato = "AND condt_exclusao IS NULL";
        	$condicaoCliente = "AND clidt_exclusao IS NULL";
        }
        

        $sql = "SELECT
                    veiplaca,
                    tpcdescricao,
                    tpcoid,
                    clinome,
                    connumero,
                    (
                        CASE WHEN clitipo = 'F' THEN
                            clifone_res
                        ELSE
                            clifone_com
                        END
                    ) AS telefone,
                    (
                        CASE WHEN clitipo = 'F' THEN
                           clino_cpf
                        ELSE
                            clino_cgc
                        END
                    ) AS cpf_cgc,
                    clitipo,
                    clioid
                    FROM
                        veiculo
                    INNER JOIN
                        contrato ON conveioid = veioid $condicaoContrato
                    INNER JOIN
                        clientes ON clioid = conclioid  $condicaoCliente      
                    INNER JOIN
                        tipo_contrato ON conno_tipo = tpcoid 
                    WHERE veidt_exclusao IS NULL AND veiplaca ILIKE '" . $parametro . "%'
                    ";
        if (!empty($clioid)) {
            $sql .= " AND conclioid = " . $clioid . "";
        }
        $sql .= " ORDER BY veiplaca ASC LIMIT 100 ";

        $rs = pg_query($conn, $sql);
        while ($row = pg_fetch_object($rs)) {
            $retorno[$chave]['label'] = utf8_encode($row->veiplaca);
            $retorno[$chave]['value'] = utf8_encode($row->veiplaca);
            $retorno[$chave]['tpcdescricao'] = utf8_encode($row->tpcdescricao);
            $retorno[$chave]['tpcoid'] = utf8_encode($row->tpcoid);
            $retorno[$chave]['id'] = utf8_encode($row->veiplaca);
            $retorno[$chave]['telefone'] = utf8_encode($row->telefone);
            $retorno[$chave]['cpf_cgc'] = utf8_encode($row->cpf_cgc);
            $retorno[$chave]['clinome'] = utf8_encode($row->clinome);
            $retorno[$chave]['connumero'] = $row->connumero;
            $retorno[$chave]['clioid'] = $row->clioid;
            $chave++;
        }
        return $retorno;
    }

    /**
     * Busca placas vinculadas ao cliente
     * @param bacoid ID cliente
     * @return string array
     * @param ErrorException
     */
    public function buscarPlacasCliente($clioid, $acao){
    	
     	$condicaoContrato 	= "";
     	$condicaoCliente 	= "";
     	if($acao != 'editar'){
     		$condicaoContrato = "AND condt_exclusao IS NULL";
     		$condicaoCliente = "AND clidt_exclusao IS NULL";
     	}
    	
        $sql = "SELECT
                    veiplaca,
                    tpcoid,
                    tpcdescricao
                    FROM
                        veiculo
                    INNER JOIN
                        contrato ON conveioid = veioid $condicaoContrato
                    INNER JOIN
                        clientes ON clioid = conclioid $condicaoCliente      
                    INNER JOIN
                        tipo_contrato ON conno_tipo = tpcoid 
                    WHERE veidt_exclusao IS NULL
                    ";
        if (!empty($clioid)) {
            $sql .= " AND conclioid = " . $clioid . "";
        }
        $sql .= " ORDER BY veiplaca ASC ";

        if (!$rs = pg_query($this->conn, $sql))
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        if (pg_num_rows($rs) > 0){
            $i = 0;
            while ($row = pg_fetch_object($rs)) {
                $retorno[$i]['veiplaca'] = $row->veiplaca;
                $retorno[$i]['tpcoid'] = $row->tpcoid;
                $retorno[$i]['tpcdescricao'] = utf8_encode($row->tpcdescricao);
                $i++;
            }
        }
        return $retorno;
    }

    /**
     * Método para realizar a pesquisa de UF.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarUF() {

        $sql = "SELECT
                    estoid,
                    estuf
                FROM
                    estado
                WHERE
                    estexclusao IS NULL
                ORDER BY
                    estuf";

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
     * Método para realizar a pesquisa de Cidades.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function buscarCidade($id = null) {

        if ($id != null) {
            $where = "AND clcestoid = {$id}";
        }

        $sql = "SELECT
                    clcoid,
                    clcnome
                FROM
                    correios_localidades
                WHERE
                    TRUE
                    $where
                ORDER BY
                    clcnome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
        


        $i = 0;
        while ($row = pg_fetch_object($rs)) {
        	$retorno[$i]['id'] = $row->clcoid;
        	$retorno[$i]['cidade'] = utf8_encode($row->clcnome);
        	$i++;
        }
        
        return $retorno;
    }



    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserir(stdClass $parametros) {


        $sql = "INSERT INTO
               backoffice
                  (

                          bacdt_solicitacao,
                          bacstatus,
                          bacclioid,
                          bacplaca,
                          bacusuoid_atendente,
                          bactpcoid,
                          bacfone,
                          baccpf_cnpj,
                          bacbmsoid,
                          bacdetalhamento_solicitacao
                  )
              VALUES
                  (
                     '" . $parametros->data_confirmar . "',
                     '" . $parametros->bacstatus . "',
                     " . $parametros->clioid . ",
                     '" . $parametros->bacplaca . "',
                      " . $this->cd_usuario . ",
                      " . $parametros->tpcoid . ",
                      '" . $parametros->bacfone . "',
                      " . $parametros->baccpf_cnpj . ",
                      " . $parametros->bacbmsoid . ",
                      '" . pg_escape_string($parametros->bacdetalhamento_solicitacao) . "'


                   )

      ";


        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados Historico
     * @return boolean
     * @throws ErrorException
     */
    public function inserirHistorico($id, $bachtratativa) {


        $this->cd_usuario = $_SESSION['usuario']['oid'];


        $sql = "INSERT INTO
               backoffice_historico
                  (
                    bachbacoid,
                    bachdt_cadastro,
                    bachtratativa,
                    bachusuoid_cadastro
                  )
              VALUES
                  (
                    '" . $id . "',
                      NOW(),
                      '" . pg_escape_string($bachtratativa) . "',
                      " . $this->cd_usuario . "

                   )
      ";


        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Responsável para inserir um registro no banco de dados tabela historico.
     * @param connumero - numero de contrato
     * @param obsHistorico - observação do registro no histórico
     * @return boolean
     * @throws ErrorException
     */
    public function inserirHistoricoContrato($connumero, $obsHistorico) {

        $this->cd_usuario = $_SESSION['usuario']['oid'];
		
        $obsHistorico = str_replace("'",'/"',$obsHistorico);
        
        $sql = "SELECT historico_termo_i({$connumero}, {$this->cd_usuario}, '$obsHistorico')";
		
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Método para realizar a pesquisa de apenas um registro.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarPorID($id) {

        $retorno = new stdClass();

        $sql = "SELECT
                    TO_CHAR(bacdt_solicitacao,'DD/MM/YYYY HH24:MI') as bacdt_solicitacao,
                    bacstatus,
                    bacclioid,
                    bacplaca,
                    bacusuoid_atendente,
                    bactpcoid,
                    bacfone,
                    baccpf_cnpj,
                    bacbmsoid,
                    bacdetalhamento_solicitacao,
                    clinome,
                    clioid,
                    clitipo,
                    bacoid,
                    tpcoid,
                    tpcdescricao,
                    bmsdescricao,
                    bmsoid,
        			bacestoid, 
        			bacclcoid 
                FROM
                    backoffice
                    INNER JOIN clientes on bacclioid = clioid
                    INNER JOIN tipo_contrato on tpcoid = bactpcoid
                    INNER JOIN backoffice_motivo_solicitacao on bmsoid = bacbmsoid
                WHERE
                    bacoid = " . intval($id);
        
        if (!$rs = pg_query($this->conn, $sql))
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        if (pg_num_rows($rs) > 0)
            $retorno = pg_fetch_object($rs);
        return $retorno;
    }

    /**
     * Método para realizar a pesquisa de apenas um registro.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarHistico($id) {

        $retorno = array();


        $sql = "SELECT
                        TO_CHAR(bachdt_cadastro,'DD/MM/YYYY HH24:MI') as bachdt_cadastro,
                        bachtratativa,
                        bachusuoid_cadastro,
                        nm_usuario
                FROM
                    backoffice_historico
                    INNER JOIN usuarios  on bachusuoid_cadastro = cd_usuario
                WHERE
                   bachbacoid = " . intval($id) . "
                ORDER BY bachdt_cadastro DESC";




        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Responsável por atualizar os registros
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizar(stdClass $parametros) {
        $bacstatus = $parametros->bacstatus;
        $bacdt_conclusao = ($bacstatus == 'C') ? 'NOW()' : 'NULL';

        $sql = "UPDATE backoffice SET
                    bacstatus = '{$bacstatus}',
                    bacclioid = " . intval($parametros->clioid) . ",
                    bacplaca  = '" . pg_escape_string($parametros->bacplaca) . "',
                    bacusuoid_atendente  = " . $this->cd_usuario . ",
                    bactpcoid = " . intval($parametros->tpcoid) . ",
                    bacfone = '" . pg_escape_string($parametros->bacfone) . "',
                    baccpf_cnpj = " . $parametros->baccpf_cnpj . ",
                    bacbmsoid = " . intval($parametros->bacbmsoid) . ",
                    bacdetalhamento_solicitacao = '" . pg_escape_string($parametros->bacdetalhamento_solicitacao) . "',
                    bacdt_conclusao = $bacdt_conclusao
    		WHERE
                    bacoid = " . $parametros->bacoid . "";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }
    
    /**
     *
     * @param string $veiplaca
     *
     * @throws ErrorException
     * @return array $retorno
     */
    public function buscarPorPlaca($veiplaca){
    	$retorno = array();
    	 
    	if (empty($veiplaca)) {
    		return $retorno;
    	}
    	 
    	$sql = "SELECT     veioid, 
				           clioid, 
				           clinome, 
				           veiplaca, 
				           tpcoid, 
				           clifone_com, 
				           tpcdescricao, 
				           connumero, ( 
				           CASE 
				                      WHEN clitipo = 'F' THEN clifone_res 
				                      ELSE clifone_com 
				           END ) AS telefone, ( 
				           CASE 
				                      WHEN clitipo = 'F' THEN clino_cpf 
				                      ELSE clino_cgc 
				           END ) AS cpf_cgc, 
				           tpcdescricao 
				FROM       veiculo 
				INNER JOIN contrato 
				ON         conveioid = veioid 
				AND        condt_exclusao IS NULL 
				INNER JOIN clientes 
				ON         clioid = conclioid 
				AND        clidt_exclusao IS NULL 
				INNER JOIN tipo_contrato 
				ON         conno_tipo = tpcoid 
				WHERE      veidt_exclusao IS NULL 
				AND        veiplaca != '' 
				AND        veiplaca ilike '$veiplaca%' 
				ORDER BY   veiplaca ASC";
    	 
    	if (!$rs = pg_query($this->conn, $sql)){
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}
    	
		if (pg_num_rows($rs) > 0){
			$i = 0;
    		while ($row = pg_fetch_object($rs)) {
				$retorno[$i]['label'] = $row->veiplaca;
				$retorno[$i]['value'] = $row->veiplaca;
				$retorno[$i]['veioid'] = $row->veioid;
				$retorno[$i]['clioid'] = $row->clioid;
				$retorno[$i]['clinome'] = $row->clinome;
				$retorno[$i]['tpcoid'] = $row->tpcoid;
				$retorno[$i]['clifone'] = $row->telefone;
				$retorno[$i]['cpf_cgc'] = $row->cpf_cgc;
		   		$retorno[$i]['tpcdescricao'] = utf8_encode($row->tpcdescricao);
				$retorno[$i]['telefone'] = utf8_encode($row->telefone);
				$i++;
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

?>
