<?php

/**
 * Classe RelOcorrenciaNovoIrvCongeladoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 */
class RelOcorrenciaNovoIrvCongeladoDAO {

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


	$this->isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;

	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

	$retorno = NULL;

	$sql = "SELECT
				ococdoid,
				ococdtipo_relatorio AS tipo_sigla,
				CASE	WHEN  ococdtipo_relatorio = 'A' THEN 'Analítico'
					WHEN  ococdtipo_relatorio = 'P' THEN 'Apoio'
					WHEN  ococdtipo_relatorio = 'D' THEN 'Apoio Detalhado'
					WHEN  ococdtipo_relatorio = 'M' THEN 'Macro'
					WHEN  ococdtipo_relatorio = 'S' THEN 'Sintético'
					WHEN  ococdtipo_relatorio = 'R' THEN 'Sintético Resumido'
				END AS ococdtipo_relatorio,
				TO_CHAR(ococdperiodo_inicial,'DD/MM/YYYY') AS ococdperiodo_inicial,
				TO_CHAR(ococdperiodo_final,'DD/MM/YYYY') AS ococdperiodo_final,
				ococdusuoid_congelamento,
				TO_CHAR(ococddata_congelamento,'DD/MM/YYYY HH24:MI:SS') AS ococddata_congelamento,
				ococdusuoid_exclusao,
				TO_CHAR(ococddata_exclusao,'DD/MM/YYYY HH24:MI:SS') AS ococddata_exclusao,
				exc.nm_usuario AS usuario_exclusao,
				inc.nm_usuario AS usuario_inclusao
			FROM
				ocorrencia_congelamento_dados
			LEFT JOIN
				usuarios AS inc ON (inc.cd_usuario  = ococdusuoid_congelamento)
			LEFT JOIN
				usuarios AS exc ON (exc.cd_usuario  = ococdusuoid_exclusao)
			WHERE
				1 = 1 ";

        if ( isset($parametros->ococdtipo_relatorio) && !empty($parametros->ococdtipo_relatorio) && trim($parametros->ococdtipo_relatorio) != 'EX') {

            $sql .= "AND
                        ococdtipo_relatorio = '" . pg_escape_string( $parametros->ococdtipo_relatorio ) . "'
                    AND
                    	ococddata_exclusao IS NULL ";

        }

        if ( isset($parametros->ococdtipo_relatorio) && !empty($parametros->ococdtipo_relatorio) && trim($parametros->ococdtipo_relatorio) == 'EX') {

            $sql .= "AND
                        ococddata_exclusao IS NOT NULL ";

        }

        if ( isset($parametros->ococdperiodo_inicial) && !empty($parametros->ococdperiodo_inicial) && isset($parametros->ococdperiodo_final) && !empty($parametros->ococdperiodo_final) ) {

            $sql .= " AND
							(
								(
									(ococdperiodo_inicial >= '" . pg_escape_string( $parametros->ococdperiodo_inicial ) . "' AND ococdperiodo_inicial <= '" . pg_escape_string( $parametros->ococdperiodo_final ) . "')
									OR
									(ococdperiodo_final >= '" . pg_escape_string( $parametros->ococdperiodo_inicial ) . "' AND ococdperiodo_final <= '" . pg_escape_string( $parametros->ococdperiodo_final ) . "')
								)
								OR
								(
									ococdperiodo_inicial <= '" . pg_escape_string( $parametros->ococdperiodo_inicial ) . "' AND ococdperiodo_final >= '" . pg_escape_string( $parametros->ococdperiodo_final ) . "'
								)
				            ) ";

        }


	    $sql .= "ORDER BY ococdoid DESC ";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if(pg_num_rows($rs) > 0){
			$retorno = array();
			while($row = pg_fetch_object($rs)){
				$retorno[] = $row;
			}
		}

		return $retorno;
	}



	public function buscarMotivos($relatorioMacro) {

		$retorno = array();

		$sql = "SELECT
					ocmoid,
					ocmdescricao
		        FROM
		        	ocorrencia_motivo
		        WHERE
		        	ocmdt_exclusao IS NULL ";

		if ($relatorioMacro === true) {
			$sql.= "AND ocmdescricao IN ('Roubo','Suspeita','Furto') ";
		}

		$sql.= " ORDER BY
		        	ocmdescricao";


		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->ocmoid;
			$retorno[$i]['label'] = $this->isAjax ? utf8_encode($row->ocmdescricao) : $row->ocmdescricao;
			$i++;
		}

		if($this->isAjax) {
			return json_encode($retorno);
		} else {
			return $retorno;
		}

	}

	public function buscarMarcas(){

		$retorno  =  array();

		$sql = "SELECT
                    DISTINCT ON (UPPER(mcamarca)) UPPER(mcamarca) AS label,
                    mcaoid AS id
                FROM
                    marca
                WHERE
                    mcadt_exclusao is null
                ORDER BY
                UPPER(mcamarca)";


	    if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while ($row = pg_fetch_object($rs)) {
			$retorno[] = $row;
		}


		return $retorno;

	}


	public function buscarModelos($marcaId){

		$retorno  =  array();

		$sql = "SELECT
					mlooid,
					mlomodelo
	            FROM
	            	modelo
	            WHERE
	            	mlomcaoid = " . intval($marcaId) . "
	            AND
	            	mlodt_exclusao IS NULL
	            ORDER BY
	            	mlomodelo";

	    if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

	    $i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->mlooid;
			$retorno[$i]['label'] = $this->isAjax ? utf8_encode($row->mlomodelo) : $row->mlomodelo;
			$i++;
		}

		if($this->isAjax) {
			return json_encode($retorno);
		} else {
			return $retorno;
		}

	}

	public function buscarCidades($uf = '') {

		$retorno  =  array();

		$sql = "SELECT
					cidoid,
				    ciddescricao
				FROM
					cidade
				WHERE
					cidexclusao IS NULL
		";
		if (!empty($uf)) {
			$sql .= " AND ciduf = '$uf' ";
		}

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

	    $i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->cidoid;
			$retorno[$i]['label'] = $this->isAjax ? utf8_encode($row->ciddescricao) : $row->ciddescricao;
			$i++;
		}

		if($this->isAjax) {
			return json_encode($retorno);
		} else {
			return $retorno;
		}
	}


	public function buscarTipoPropostas() {

		$retorno  =  array();

		$tipoContrato = null;
		$indice = '';

		$sql ="SELECT
					tppoid, tppdescricao
				FROM
					tipo_proposta
				WHERE
					tppoid_supertipo IS NULL
				ORDER BY
					tppdescricao
                ";
        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){

			while ($tipoProposta = pg_fetch_object($rs)) {

				$indice = $tipoProposta->tppoid;
				$retorno[$indice] = $tipoProposta->tppdescricao;
			}
		}

		return $retorno;

	}


	public function buscarTipoContratos() {

		$retorno  =  array();

		$tipoContrato = null;
		$indice = '';

		$sql ="SELECT
						tpcoid,
						tpcdescricao
				FROM
						tipo_contrato
				WHERE
						tpcativo = 't'
			   	ORDER BY
			   			tpcdescricao
                ";
        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){

			while ($tipoContrato = pg_fetch_object($rs)) {

				$indice = $tipoContrato->tpcoid;
				$retorno[$indice] = $tipoContrato->tpcdescricao;
			}
		}

		return $retorno;

	}


	public function buscarSeguradorasTipoContrato() {

		$retorno  =  array();

		$tipoContrato = null;
		$indice = '';

		$sql ="SELECT
					tpcoid,
					tpcdescricao
				FROM
					tipo_contrato
                WHERE
                	tpcseguradora = true
                	AND tpcativo = true
                ORDER BY
                	tpcdescricao
                ";
        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){

			while ($tipoContrato = pg_fetch_object($rs)) {

				$indice = $tipoContrato->tpcoid;
				$retorno[$indice] = $tipoContrato->tpcdescricao;
			}
		}

		return $retorno;
	}

	public function buscarSeguradorasSeguradora() {

		$retorno  =  array();

		$seguradora = null;

		$sql = "SELECT
					segoid,
					segseguradora
                FROM
                	seguradora
                WHERE
                	segdt_exclusao IS NULL
				ORDER BY
					segseguradora
                ";

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($seguradora = pg_fetch_object($rs)) {
				$retorno[$seguradora->segoid] = $seguradora->segseguradora;
			}
		}

		return $retorno;
	}

	public function buscarClassesEquipamento() {

		$retorno = array();
		$classe = null;

		$sql = "SELECT
					eqcoid,
					eqcdescricao
				FROM
					equipamento_classe
				ORDER BY
					eqcdescricao
				";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($classe = pg_fetch_object($rs)) {
				$retorno[$classe->eqcoid] = $classe->eqcdescricao;
			}
		}

		return $retorno;
	}

    /**
     * Obtem o total de ocorrencias em andamento
     *
     * @param int $id
     * @param stdClass $parametros
     * @return int
     * @throws ErrorException
     */
    private function buscarOcorrenciasAndamento ($id,$parametros) {

        $total = 0;

        $sql = "SELECT
                    COUNT(1) AS total_andamento
                FROM
					ocorrencia_congelamento
				WHERE
					ococococdoid IN ($id)
                AND
                    ococstatus = 'Em Andamento'
                ";


        $sql .= $this->montarFiltroQuery($parametros, true);

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

        $total = pg_fetch_result($rs, 0, "total_andamento");

        return $total;

    }


	public function pesquisarCongelados($id, $parametros, $retorna_result = false, $usarSubQuery = true, $rastreador = false) {

		if (empty($id)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO, 1100);
		}


        $sql = "";

        /*
         * Cabecalho da Query -Inicio
         */
        if (($parametros->ococdtipo_relatorio == 'S') || ($parametros->ococdtipo_relatorio == 'R')) {

            $sql .= "
                DROP TABLE IF EXISTS sintetico_temp;
                CREATE TEMP TABLE sintetico_temp AS SELECT * FROM ( ";

        }


       /* $sql .= "
            SELECT
                COALESCE(
                        TO_CHAR(
                                (EXTRACT
                                        (EPOCH FROM
                                                (MAX(data_chegada) OVER (PARTITION BY ococtelefone_emergencia, ococcliente)
                                                - MAX(data_inicio) OVER (PARTITION BY ococtelefone_emergencia, ococcliente))
                                        )  * INTERVAL '1 second'
                                ), 'HH24:MI:SS') , '00:00:00'
                        ) AS tempo_apoio,
                        *
            FROM (
            ";*/


        /*
         * Query Principal
         */
		$sql .= "SELECT
                    ococoid,
                    ococococdoid,
                    ocococooid,
                    TO_CHAR(ococdata_comunicacao , 'DD/MM/YYYY HH24:MI') AS data_comunicacao,
                    ococmotivo,
                    ococtipo_veiculo,
                    ococmodelo_veiculo,
                    ococmarca_veiculo,
                    ococseguradora,
                    ococuf,
                    ococclasse_equipamento,
                    ococecclasse_termo,
                    ococclasse_equipamento_grupo,
                    ococcorretora,
                    ococtipo_ocorrencia,
                    ocococorrencia_forma_notificacao,
                    ococmodalidade_contrato,
                    ococtipo_proposta,
                    ococsub_tipo_proposta,
                    ococtipo_contrato,
                    ococcliente,
                    ococplaca,
                    ococregiao,
                    ococatendente,
                    ococequipamento_projeto,
                    ocococorrencia_motivo_equip_sem_contato,
                    ococusuario,
                    ococcontato,
                    ococcidade,
                    ococrecuperado,
                    ococddd,
                    ococfone,
                    ococvalor_veiculo,
                    ococtempo_aviso,
                    ococnumero_bo,
                    ococconcluido,
                    ococtipo_carga,
                    ococstatus,
                    ococtelefone_emergencia,
                    ococveiculo_chassi,
                    ococveiculo_cor,
                    ococveiculo_ano,
                    ococvalor_fipe,
                    ococvalor_carga,
                    ococcarga,
                    ococcnpj_cpf,
                    ococserial_cargo_track,
                    ococequipamento,
                    ococlocal_instalacao_equipamento,
                    ococtecnico_instalacao,
                    ococforma_recuperacao,
                    ococlatitude_longitude_ultima_posicao,
                    TO_CHAR(ococdata_roubo , 'DD/MM/YYYY HH24:MI') AS ococdata_roubo,
                    TO_CHAR(ococdata_recuperacao , 'DD/MM/YYYY HH24:MI') AS ococdata_recuperacao,
                    ococlocal_evento,
                    ococbairro_evento,
                    ococzona_evento,
                    ococcidade_evento,
                    ococuf_evento,
                    ococlatitude_longitude_evento,
                    ococlocal_recuperado,
                    ococzona_recuperado,
                    ococcidade_recuperado,
                    ococuf_recuperado,
                    ococlatitude_longitude_recuperado,
                    ococequipe_apoio,
                    ococrecuperado_apoio,
                    ococtempo_chegada_apoio,
                    ococtipo_filtro,
                    ococperiodo_ini_filtro,
                    ococperiodo_fim_filtro,
                    ococmotivo_filtro,
                    ococstatus_filtro,
                    ococtipo_veiculo_filtro,
                    ococmodelo_filtro,
                    ococseguradora_filtro,
                    ococtipo_ocorrencia_filtro,
                    ococtipo_cidade_uf_filtro,
                    ococcpf_cnpj_filtro,
                    ococinstalador_cargo_track_filtro,
                    ococmodalidade_contrato_filtro,
                    ococtipo_periodo_filtro,
                    ococtipo_pessoa_filtro,
                    ococnumero_bo_filtro,
                    ococbairro_recuperado,
                    ococrastreador,

                    (CASE
                        WHEN ococinstalado_cargo_track = 't'
                        THEN 'Sim'
                        WHEN ococinstalado_cargo_track = 'f'
                        THEN 'Não'
                        ELSE  ''
                    END) AS ococinstalado_cargo_track,

                    (CASE
                        WHEN  ococtipo_pessoa = 'J'
                        THEN 'PJ'
                    ELSE
                        'PF'
                    END) AS ococtipo_pessoa,

                    (CASE
                        WHEN ococcarregado = 't'
                        THEN 'Sim'
                        WHEN ococcarregado = 'f'
                        THEN 'Não'
                        ELSE  ''
                    END) AS ococcarregado,

                    (CASE
                        WHEN ococstatus = 'Chegada'
                        THEN ococdata_comunicacao
                    END) as data_chegada,

                    (CASE
                        WHEN ococstatus = 'Início'
                        THEN ococdata_comunicacao
                    END) as data_inicio,
					ococembarcador,
					ococseguradora_carga,
					condt_exclusao
				FROM
					ocorrencia_congelamento
				LEFT JOIN
					ocorrencia ON (ocococooid = ocooid)
				LEFT JOIN 
					contrato ON (ococonnumero = connumero)
				WHERE
					ococococdoid IN ($id)
                ";


        $sql .= $this->montarFiltroQuery($parametros, false, $rastreador);

        /*
         * Cabecalho da Query  - Fim
         */
       // $sql .= ") AS tabela_final";

        switch ($parametros->ococdtipo_relatorio) {

            case 'P':
                $sql .= " ORDER BY ococtelefone_emergencia,ococcliente,ococplaca, ococdata_comunicacao::TIMESTAMP ASC ";
                break;
            case 'D':
                $sql .= " ORDER BY ococtelefone_emergencia ASC, ococdata_comunicacao::TIMESTAMP ASC";
                 break;
            case 'A':
            	 $sql .= " ORDER BY ococdata_comunicacao::TIMESTAMP";
                break;
            case 'M':
                $sql .= " ORDER BY ococveiculo_ano::INT ASC, ococplaca DESC";
                break;
            case 'S':
            case 'R':
                $sql .= ") AS FOO;";

                if($usarSubQuery) {
                    $sql .= $this->pesquisarSintetico($parametros);
                }
                break;

        }


		//echo "<pre>"; print_r($sql); exit;

        if(!$usarSubQuery) {
            return $sql;
        }

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			if ($retorna_result) {
				$retorno = $rs;
			} else {
				$retorno = pg_fetch_all($rs);
			}
		}

		return $retorno;
	}


    /**
     * Montar as cláusulas de condições comuns para execução de Query específicas
     *
     * @param stdClass $parametros
     * @return string
     * @throws ErrorException
     */
    private function montarFiltroQuery($parametros, $statusAndamento = false, $rastreador = false) {

        $sql = "";

        if ((($parametros->ococdtipo_relatorio == 'S') || ($parametros->ococdtipo_relatorio == 'R')) && (!$statusAndamento)) {

            if(!$rastreador) {
                $sql .= "AND
                        ococstatus LIKE '%Recuperado'";
            }
        }

		if ($parametros->ococdtipo_relatorio == 'M' && (isset($parametros->filtrar_tipo_periodo) && !empty($parametros->filtrar_tipo_periodo)) ) {

			if($parametros->filtrar_tipo_periodo === 'E') {

		        $sql .= " AND
							ococdata_roubo BETWEEN '" . $parametros->ococdperiodo_inicial . " 00:00:00' AND '" . $parametros->ococdperiodo_final . " 23:59:59' ";

		    } else if ($parametros->filtrar_tipo_periodo === 'C') {

		        $sql .= " AND
							ococdata_comunicacao BETWEEN '" . $parametros->ococdperiodo_inicial . " 00:00:00' AND '" . $parametros->ococdperiodo_final . " 23:59:59' ";

		    } else {

		    	$sql .= " AND
							ococdata_recuperacao BETWEEN '" . $parametros->ococdperiodo_inicial . " 00:00:00' AND '" . $parametros->ococdperiodo_final . " 23:59:59' ";

		    }


		} else if (($parametros->ococdtipo_relatorio == 'R') 
					|| ($parametros->ococdtipo_relatorio == 'S') 
					|| ($parametros->ococdtipo_relatorio == 'A')) {

            if(!$statusAndamento) {

                $sql .= " AND
                            (
                                ococdata_comunicacao BETWEEN '" . $parametros->ococdperiodo_inicial . " 00:00:00'
                                    AND '" . $parametros->ococdperiodo_final . " 23:59:59'
                                OR  (ococdata_recuperacao >= '" . $parametros->ococdperiodo_inicial . " 00:00:00'
                                    AND ococdata_recuperacao <= '" . $parametros->ococdperiodo_final . " 23:59:59')
                            )
                            ";
            }

        } else {

			$sql .= " AND
							ococdata_comunicacao BETWEEN '" . $parametros->ococdperiodo_inicial . " 00:00:00' AND '" . $parametros->ococdperiodo_final . " 23:59:59' ";
		}


		//filtro por motivo
		if (isset($parametros->filtrar_motivo) && !empty($parametros->filtrar_motivo)) {

			$motivos = $this->buscarMotivos(false);

			$motivoLabel = '';

			foreach ($motivos as $motivo) {
				if ($motivo['id'] == $parametros->filtrar_motivo) {
					$motivoLabel = $motivo['label'];
				}
			}

			$sql .= " AND
							(
									TRIM(ococmotivo) LIKE '%" . $motivoLabel . "%'
								OR
									TRIM(ococmotivo_filtro) = '" . intval($parametros->filtrar_motivo) . "'
							) ";

		}

		//filtro por status
		if (isset($parametros->filtrar_status) && !empty($parametros->filtrar_status)) {

			$status = array(
						"A" => "Em Andamento",
						"S" => "Sem Contato",
						"P" => "Pendente",
						"R" => "Recuperado",
						"N" => "Não Recuperado",
						"C" => "Concluído",
						"L" => "Cancelado"
						);

			$sql .= " AND
							(
									TRIM(ococstatus) = '" . trim($status[$parametros->filtrar_status]) . "'

						 	)";

		}

		//filtro por tipo de veiculo
		if (isset($parametros->filtrar_tipo_veiculo) && !empty($parametros->filtrar_tipo_veiculo)) {
			

				if ($parametros->filtrar_tipo_veiculo == 'L') {

					$condicoes  =  " mlotipveioid NOT IN (1,2,7) OR mlotipveioid IS NULL ";

				} else if ($parametros->filtrar_tipo_veiculo == 'P') {

					$condicoes  =  " mlotipveioid IN (1,2) ";

				} else if ($parametros->filtrar_tipo_veiculo == 'M') {

					$condicoes  =  " mlotipveioid = 7 ";

				}

				$sqlFiltro = "SELECT
									DISTINCT
									tipvdescricao
								FROM
									modelo
								LEFT JOIN
									tipo_veiculo ON (tipvoid = mlotipveioid)
								WHERE " . $condicoes;

				if (!$rsFiltro = pg_query($this->conn, $sqlFiltro)){
					throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
				}

				$filtro = "";
				while ($tipo = pg_fetch_object($rsFiltro)) {
					$filtro .= "'" . trim($tipo->tipvdescricao) . "',";
				}

				$filtro = rtrim($filtro,',');

				$sql .= " AND
								TRIM(ococtipo_veiculo) IN (" . $filtro . ")";

			}

		//}

		//filtro por UF de evento
		if (isset($parametros->filtrar_estado) && !empty($parametros->filtrar_estado)) {

			$sql .= " AND
							TRIM(ococuf_evento)  = '" . pg_escape_string(trim($parametros->filtrar_estado)) . "' ";

		}

		//filtro por UF de recuperação
		if (isset($parametros->filtrar_estado_recuperacao) && !empty($parametros->filtrar_estado_recuperacao)) {

			$sql .= " AND
							TRIM(ococuf_recuperado)  = '" . pg_escape_string(trim($parametros->filtrar_estado_recuperacao)) . "' ";

		}

		//filtro por marca de veiculo
		if (isset($parametros->filtrar_marca) && !empty($parametros->filtrar_marca)) {

			$sqlFiltro = "SELECT
							mcamarca
						FROM
							marca
						WHERE
							mcaoid = " . intval($parametros->filtrar_marca) . "
						LIMIT 1";

			if (!$rsFiltro = pg_query($this->conn, $sqlFiltro)){
					throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
			}

			$filtro = pg_fetch_result($rsFiltro, 0, 'mcamarca');

			$sql .= " AND
							TRIM(ococmarca_veiculo)  = '" . pg_escape_string(trim($filtro)) . "' ";

		}

		//filtro por marca de modelo
		if (isset($parametros->filtrar_modelo) && !empty($parametros->filtrar_modelo)) {

			$sql .= " AND
							TRIM(ococmodelo_veiculo)  = '" . pg_escape_string(trim($parametros->filtrar_modelo)) . "' ";

		}

		//filtro por seguradora
		if (isset($parametros->filtrar_seguradora) && !empty($parametros->filtrar_seguradora)) {


			if($parametros->filtrar_seguradora == "ROD") { // SE FOR "RODOBENS - TODOS "

				$sql .= " AND
				 				TRIM(ococtipo_contrato) ILIKE '%RODOBENS%' AND TRIM(ococtipo_contrato) NOT ILIKE 'Ex%'";

			} elseif($parametros->filtrar_seguradora == "RODS") { // SE FOR "RODOBENS - ESPECIAL "

				$sql .= " AND
				 				TRIM(ococtipo_contrato) ILIKE '%Bradesco Rodobens%' ||  TRIM(ococtipo_contrato) ILIKE '%Bradesco Rodobens Carga%' ||  TRIM(ococtipo_contrato) ILIKE '%Rodobens Corretora%' ||  TRIM(ococtipo_contrato) ILIKE '%SulAmerica ODO%' ||  TRIM(ococtipo_contrato) ILIKE '%Sulamerica Sob Medida%' ";

			} elseif($parametros->filtrar_seguradora == "BRAT") { // SE FOR "BRADESCO - TODOS "			

				$sql .= " AND
				 				TRIM(ococtipo_contrato) ILIKE '%BRADESCO%' AND TRIM(ococtipo_contrato) NOT ILIKE 'Ex%'";

			} elseif($parametros->filtrar_seguradora == "CLI") { // SE FOR "CLIENTES "

				$sqlFiltro = "SELECT DISTINCT tpcdescricao FROM tipo_contrato WHERE tpcseguradora IS FALSE AND tpcdescricao NOT ILIKE 'Ex%'";

				if (!$rsFiltro = pg_query($this->conn, $sqlFiltro)){
					throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
				}

				$filtro = "";
				while ($tipo = pg_fetch_object($rsFiltro)) {
					$filtro .= "'" . trim($tipo->tpcdescricao) . "',";
				}

				$filtro = rtrim($filtro,',');

				$sql .= " AND
				 				TRIM(ococtipo_contrato) IN (" . $filtro . ")";

			} elseif($parametros->filtrar_seguradora == "SEG") { // SE FOR "SEGURADORA - TODOS"				

				$sqlFiltro = "SELECT DISTINCT segseguradora FROM seguradora WHERE segoid <> 2 ";

				if (!$rsFiltro = pg_query($this->conn, $sqlFiltro)){
					throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
				}

				$filtro = "";
				while ($tipo = pg_fetch_object($rsFiltro)) {
					$filtro .= "'" . trim($tipo->segseguradora) . "',";
				}

				$filtro = rtrim($filtro,',');

				$sql .= " AND
				 				TRIM(ococseguradora) IS NOT NULL AND TRIM(ococseguradora) NOT IN (" . $filtro . ") ";



			} elseif($parametros->filtrar_seguradora == "UNBT") { // SE FOR "UNIBANCO - TODOS "

				$sql .= " AND
				 				TRIM(ococtipo_contrato) ILIKE '%UNIBANCO%' AND TRIM(ococtipo_contrato) NOT ILIKE 'Ex%' ";

			} elseif($parametros->filtrar_seguradora == "SULT") { // SE FOR "SULAMERICA - TODOS "

				$sql .= " AND
				 				(
				 					TRIM(ococtipo_contrato) ILIKE '%SULAMERICA%' 
				 					OR 
				 					TRIM(ococtipo_contrato) ILIKE '%SUL AMERICA%' 
				 				)
				 		 AND TRIM(ococtipo_contrato) NOT ILIKE 'Ex%' ";

			} else {
				//default

				$parametros->filtrar_seguradora = trim($parametros->filtrar_seguradora);

				$sql .= " AND
				 				TRIM(ococseguradora) = '" . pg_escape_string(trim($parametros->filtrar_seguradora)) . "' ";
			}

		}

		//filtro por classe de equipamento
		if (isset($parametros->filtrar_classe_equipamento) && !empty($parametros->filtrar_classe_equipamento)) {

			$sql .= " AND
				 			TRIM(ococclasse_equipamento) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe_equipamento)) . "%' ";

		}


		//filtrar pelo campo classe
		if (isset($parametros->filtrar_classe) && !empty($parametros->filtrar_classe)) {

			$sql .= " AND
				 			TRIM(ococclasse_equipamento) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe)) . "%' ";

		}

		//filtro por classe de contrato
		if (isset($parametros->filtrar_classe_contrato) && !empty($parametros->filtrar_classe_contrato)) {

			$sql .= " AND
				 			TRIM(ococecclasse_termo) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe_contrato)) . "%' ";

		}


		//filtro por classe de grupo
		if (isset($parametros->filtrar_classe_grupo) && !empty($parametros->filtrar_classe_grupo)) {

			$sql .= " AND
				 			TRIM(ococclasse_equipamento_grupo) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe_grupo)) . "%' ";

		}

		//filtro por tipo de ocorrencia
		if (isset($parametros->filtrar_tipo_ocorrencia) && !empty($parametros->filtrar_tipo_ocorrencia)) {

			$sql .= " AND
				 			TRIM(ococtipo_ocorrencia) = '" . pg_escape_string(trim($parametros->filtrar_tipo_ocorrencia)) . "' ";

		}


		//filtro por  forma de notificação
		if (isset($parametros->filtrar_forma_notificacao) && !empty($parametros->filtrar_forma_notificacao)) {

			$sql .= " AND
				 			TRIM(ocococorrencia_forma_notificacao) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_forma_notificacao)) . "%' ";

		}

		//filtrar por modalidade de contrato
		if (isset($parametros->filtrar_modalidade_contrato) && !empty($parametros->filtrar_modalidade_contrato)) {

			$sql .= " AND
				 			TRIM(ococmodalidade_contrato) = '" . pg_escape_string(trim($parametros->filtrar_modalidade_contrato)) . "' ";

		}

		//filtrar por cliente
		if (isset($parametros->filtrar_cliente) && !empty($parametros->filtrar_cliente)) {

			$sql .= " AND
				 			TRIM(ococcliente) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_cliente)) . "%' ";

		}

		//filtrar pela placa
		if (isset($parametros->filtrar_placa) && !empty($parametros->filtrar_placa)) {

			$sql .= " AND
				 			TRIM(ococplaca) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_placa)) . "%' ";

		}


		//filtrar por corretora
		if (isset($parametros->filtrar_corretora) && !empty($parametros->filtrar_corretora)) {

			$sql .= " AND
				 			TRIM(ococcorretora) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_corretora)) . "%' ";

		}

		//filtrar por região
		if (isset($parametros->filtrar_regiao) && !empty($parametros->filtrar_regiao)) {

			$sql .= " AND
							(select regiao_uf_geo(ococuf_evento) = '" . pg_escape_string(trim($parametros->filtrar_regiao)) . "') ";

		}


		//filtro por atendente
		if (isset($parametros->filtrar_atendente) && !empty($parametros->filtrar_atendente)) {

			$sql .= " AND
							TRIM(ococatendente) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_atendente)) . "%' ";

		}


		//filtro por projeto
		if (isset($parametros->filtrar_equipamento_projeto) && !empty($parametros->filtrar_equipamento_projeto)) {

			$sql .= " AND
							TRIM(ococequipamento_projeto) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_equipamento_projeto)) . "%' ";

		}

		//filtro por motivo eqp sem contato
		if (isset($parametros->filtrar_motivo_equ_sem_contato) && !empty($parametros->filtrar_motivo_equ_sem_contato)) {

			$sql .= " AND
							TRIM(ocococorrencia_motivo_equip_sem_contato) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_motivo_equ_sem_contato)) . "%' ";

		}

		//filtrar por filtrar por apoio
		if (isset($parametros->filtrar_apoio) && !empty($parametros->filtrar_apoio)) {

			$sql .= " AND
							TRIM(TRANSLATE(ococtelefone_emergencia, 'áéíóúàèìòùãõâêîôôäëïöüçÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','aeiouaeiouaoaeiooaeioucAEIOUAEIOUAOAEIOOAEIOUC')) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_apoio)) . "%' ";

		}


		//filtrar por Tipo cidade/uf
		if (isset($parametros->filtrar_tipo_cidade) && !empty($parametros->filtrar_tipo_cidade)) {

			switch ($parametros->filtrar_tipo_cidade) {
				case 'RC':

					if (isset($parametros->filtrar_estado) && !empty($parametros->filtrar_estado)) {

						$sql .= " AND
										TRIM(ococuf) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_estado)) . "%' ";

					}

					if (isset($parametros->filtrar_cidade) && !empty($parametros->filtrar_cidade)) {

						$sql .= " AND
										TRIM(ococcidade) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_cidade)) . "%' ";

					}

					break;

				case 'E':

					if (isset($parametros->filtrar_estado) && !empty($parametros->filtrar_estado)) {

						$sql .= " AND
										TRIM(ococuf_evento) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_estado)) . "%' ";

					}

					if (isset($parametros->filtrar_cidade) && !empty($parametros->filtrar_cidade)) {

						$sql .= " AND
										TRIM(ococcidade_evento) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_cidade)) . "%' ";

					}

					break;

				case 'F':

					if (isset($parametros->filtrar_estado) && !empty($parametros->filtrar_estado)) {

						$sql .= " AND
										TRIM(ococuf_recuperado) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_estado)) . "%' ";

					}

					if (isset($parametros->filtrar_cidade) && !empty($parametros->filtrar_cidade)) {

						$sql .= " AND
										TRIM(ococcidade_recuperado) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_cidade)) . "%' ";

					}

					break;
			}

		}

		//filtro por chassi
		if (isset($parametros->filtrar_chassi) && !empty($parametros->filtrar_chassi)) {

			$sql .= " AND
							TRIM(ococveiculo_chassi) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_chassi)) . "%' ";

		}

		//filtro por veiculo carregado
		if (isset($parametros->filtrar_veiculo_carregado) && !empty($parametros->filtrar_veiculo_carregado)) {


			$sql .= " AND
							TRIM(ococcarregado) = '" . pg_escape_string(trim($parametros->filtrar_veiculo_carregado)) . "' ";

		}


		//filtro por valor de carga
		if ( (isset($parametros->filtrar_valor_carga_condicao) && !empty($parametros->filtrar_valor_carga_condicao) ) && ( isset($parametros->filtrar_valor_carga) && !empty($parametros->filtrar_valor_carga) ) ) {


			$parametros->filtrar_valor_carga = str_replace('.', '', $parametros->filtrar_valor_carga);
			$parametros->filtrar_valor_carga = str_replace(',', '.', $parametros->filtrar_valor_carga);


			$sql .= " AND
						  ococvalor_carga " . $parametros->filtrar_valor_carga_condicao . " " . $parametros->filtrar_valor_carga;

		}

		//filtro por instalado cargo track
		if (isset($parametros->filtrar_instalado_cargo_track) && !empty($parametros->filtrar_instalado_cargo_track)) {


			$sql .= " AND
							ococinstalado_cargo_track = '" . pg_escape_string(trim($parametros->filtrar_instalado_cargo_track)) . "' ";

		}

		//filtro por recuperação por apoio
		if (isset($parametros->filtrar_recuperado_apoio) && !empty($parametros->filtrar_recuperado_apoio)) {

			$condicao = "Sim";
			if (trim($parametros->filtrar_recuperado_apoio) !== 't') {
				$condicao = "Não";
			}

			$sql .= " AND
							TRIM(ococrecuperado_apoio) = '" . pg_escape_string(trim($condicao)) . "' ";

		}

		//filtrar por tipo pessoa
		if (isset($parametros->filtrar_tipo_pessoa) && !empty($parametros->filtrar_tipo_pessoa)) {

			$sql .= " AND
							TRIM(ococtipo_pessoa) = '" . pg_escape_string(trim($parametros->filtrar_tipo_pessoa)) . "' ";

		}

		//filtrar por BO
		if (isset($parametros->filtrar_bo) && !empty($parametros->filtrar_bo)) {


			$sql .= " AND
							TRIM(ococnumero_bo) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_bo)) . "%' ";

		}

		//filtrar por tipo proposta
		if (isset($parametros->filtrar_tipo_proposta) && !empty($parametros->filtrar_tipo_proposta)) {

			$sql .= " AND
							TRIM(ococtipo_proposta) = '" . pg_escape_string(trim($parametros->filtrar_tipo_proposta)) . "' ";

		}

		//filtrar por tipo contrato
		if (isset($parametros->filtrar_tipo_contrato) && !empty($parametros->filtrar_tipo_contrato)) {

			$sql .= " AND
							TRIM(ococtipo_contrato) = '" . pg_escape_string(trim($parametros->filtrar_tipo_contrato)) . "' ";

		}

        return $sql;

    }

   /**
    * Query específica para o relatório Sintético
    *
    * @param int $ococococdoid
    * @return string
    */
    private function pesquisarSintetico($parametros) {


        $sql = "
            -------------------------------------------
            -- Por Classe de Equipamento
            -------------------------------------------
            (
            SELECT
                'por_equipamento'::TEXT AS tipo,
                classe AS coluna1,
                '' AS coluna2,
                '' AS coluna3,
                '' AS coluna4,
                '' AS coluna5,
                '' AS coluna6,
                '' AS coluna7,
                '' AS coluna8,
                '' AS coluna9,
                '' AS coluna10,
                '' AS coluna11,
                SUM(recuperados) as recuperados,
                SUM(nao_recuperados) as nao_recuperados,
                ococtipo_filtro
            FROM (
                    SELECT
                        (SELECT
                            eqcdescricao
                        FROM
                            equipamento_classe
                        INNER JOIN
                            equipamento_versao ON (eveoid=ocoeveoid AND eqcoid=eveeqcoid)
                        ) AS classe,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Recuperado'
                                    AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                                THEN 1
                                END),0) AS recuperados,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Não Recuperado'
                                THEN 1
                                END),0) AS nao_recuperados,
                        ococtipo_filtro
                    FROM
                        sintetico_temp
                    INNER JOIN
                        ocorrencia ON ocooid = ocococooid
                    WHERE
                        ococstatus LIKE '%Recuperado'

                ) AS por_equipamento
            GROUP BY
                classe,
                ococtipo_filtro
            ORDER BY
                coluna1
            )

            UNION ALL
            -------------------------------------------
            -- Por Modelo de Veículo
            -------------------------------------------
            (
            SELECT
                'por_modelo_veiculo'::TEXT AS tipo,
                marca_modelo AS coluna1,
                '' AS coluna2,
                '' AS coluna3,
                '' AS coluna4,
                '' AS coluna5,
                '' AS coluna6,
                '' AS coluna7,
                '' AS coluna8,
                '' AS coluna9,
                '' AS coluna10,
                '' AS coluna11,
                SUM(recuperados) as recuperados,
                SUM(nao_recuperados) as nao_recuperados,
                ococtipo_filtro
            FROM (
                    SELECT
                        (ococmarca_veiculo ||'/'|| ococmodelo_veiculo) AS marca_modelo,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Recuperado'
                                     AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                                THEN 1
                                END),0) as recuperados,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Não Recuperado'
                                THEN 1
                                END),0) as nao_recuperados,
                        ococtipo_filtro
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus LIKE '%Recuperado'
                ) AS por_modelo_veiculo
            GROUP BY
                marca_modelo,
                ococtipo_filtro
            ORDER BY
               coluna1
            )

            UNION ALL
            -------------------------------------------
            -- Por Estado
            -------------------------------------------
            (
            SELECT
                'por_estado'::TEXT AS tipo,
                ococuf_evento AS coluna1,
                '' AS coluna2,
                '' AS coluna3,
                '' AS coluna4,
                '' AS coluna5,
                '' AS coluna6,
                '' AS coluna7,
                '' AS coluna8,
                '' AS coluna9,
                '' AS coluna10,
                '' AS coluna11,
                SUM(recuperados) as recuperados,
                SUM(nao_recuperados) as nao_recuperados,
                ococtipo_filtro
            FROM (
                    SELECT
                        ococuf_evento,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Recuperado'
                                     AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                                THEN 1
                                END),0) as recuperados,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Não Recuperado'
                                THEN 1
                                END),0) as nao_recuperados,
                        ococtipo_filtro
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus LIKE '%Recuperado'
                ) AS por_estado
            GROUP BY
                ococuf_evento,
                ococtipo_filtro
            ORDER BY
            	ococuf_evento
            )

            UNION ALL
            -------------------------------------------
            -- Por Estado / Horário
            -------------------------------------------
            (
            SELECT
                'por_estado_horario'::TEXT AS tipo,
                ococuf_evento AS coluna1,
                horario AS coluna2,
                '' AS coluna3,
                '' AS coluna4,
                '' AS coluna5,
                '' AS coluna6,
                '' AS coluna7,
                '' AS coluna8,
                '' AS coluna9,
                '' AS coluna10,
                '' AS coluna11,
                SUM(recuperados) as recuperados,
                SUM(nao_recuperados) as nao_recuperados,
                ococtipo_filtro
            FROM (
                    SELECT
                        ococuf_evento,
                        TO_CHAR(DATE_TRUNC('HOUR',TO_CHAR(ococdata_roubo::timestamp, 'HH24:MI')::TIME), 'HH24:MI' ) AS horario,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Recuperado'
                                     AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                                THEN 1
                                END),0) as recuperados,
                        COALESCE ((CASE
                                WHEN ococstatus = 'Não Recuperado'
                                THEN 1
                                END),0) as nao_recuperados,
                        ococtipo_filtro
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus LIKE '%Recuperado'
                ) AS por_estado_horario
            GROUP BY
                ococuf_evento,
                horario,
                ococtipo_filtro
            ORDER BY
                ococuf_evento,
                horario
            )

            UNION ALL
            -------------------------------------------
            -- Detalhado estado_dia_Semana
            -------------------------------------------
            (
	            SELECT
	                'por_estado_dia_semana'::TEXT AS tipo,
	                ococuf_evento AS coluna1,
	                (CASE dia_semana WHEN  0
	                    THEN 'Domingo'
	                    WHEN 1
	                        THEN 'Segunda-Feira'
	                    WHEN 2
	                        THEN 'Terça-Feira'
	                    WHEN  3
	                        THEN 'Quarta-Feira'
	                    WHEN  4
	                        THEN 'Quinta-Feira'
	                    WHEN 5
	                        THEN 'Sexta-Feira'
	                    WHEN  6
	                        THEN 'Sábado'
	                END) as coluna2,
                    '' AS coluna3,
                    '' AS coluna4,
                    '' AS coluna5,
                    '' AS coluna6,
                    '' AS coluna7,
                    '' AS coluna8,
                    '' AS coluna9,
                    '' AS coluna10,
                    '' AS coluna11,
	                SUM(recuperados) as recuperados,
	                SUM(nao_recuperados) as nao_recuperados,
                    ococtipo_filtro
	            FROM (
	                    SELECT
	                        ococuf_evento,
	                        (ococmarca_veiculo ||'/'|| ococmodelo_veiculo) AS marca_modelo,
	                        EXTRACT (DOW FROM ococdata_roubo::TIMESTAMP) AS dia_semana,
	                        COALESCE ((CASE
	                                WHEN ococstatus = 'Recuperado'
                                         AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
	                                THEN 1
	                                END),0) AS recuperados,
	                        COALESCE ((CASE
	                            WHEN ococstatus = 'Não Recuperado'
	                            THEN 1
	                            END),0) AS nao_recuperados,
                            ococtipo_filtro
	                    FROM
	                        sintetico_temp
	                    WHERE
	                        ococstatus LIKE '%Recuperado'
	                ) AS estado_dia_Semana
	            GROUP BY
	                ococuf_evento,
	                dia_semana,
                    ococtipo_filtro
	            ORDER BY
	                ococuf_evento,
	                dia_semana
            )

            UNION ALL
            -------------------------------------------
            -- Detalhado Estado Veiculo Tipo
            -------------------------------------------
            (
            SELECT
                'por_estado_veiculo_tipo'::TEXT AS tipo,
                ococuf_evento AS coluna1,
                (CASE
                    WHEN mlotipveioid IN (1,2) THEN 'PESADO'
                    ELSE 'LEVE'
                    END
                ) AS coluna2,
                '' AS coluna3,
                '' AS coluna4,
                '' AS coluna5,
                '' AS coluna6,
                '' AS coluna7,
                '' AS coluna8,
                '' AS coluna9,
                '' AS coluna10,
                '' AS coluna11,
                SUM(recuperados) as recuperados,
                SUM(nao_recuperados) as nao_recuperados,
                ococtipo_filtro
            FROM (
                SELECT
                    ococuf_evento,
                    EXTRACT (DOW FROM ococdata_roubo::TIMESTAMP) AS dia_semana,
                    COALESCE ((CASE
                            WHEN ococstatus = 'Recuperado'
                                 AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                            THEN 1
                            END),0) as recuperados,
                    COALESCE ((CASE
                            WHEN ococstatus = 'Não Recuperado'
                            THEN 1
                            END),0) as nao_recuperados,
                    (SELECT
                        mlotipveioid
                    FROM
                        modelo
                    LEFT JOIN
                        tipo_veiculo ON (tipvoid = mlotipveioid)
                    WHERE
                        TRIM(tipvdescricao) ILIKE TRIM(ococtipo_veiculo)
                    LIMIT 1) AS mlotipveioid,
                    ococtipo_filtro
                FROM
                    sintetico_temp
                WHERE
                    ococstatus LIKE '%Recuperado'
                ) AS estado_veiculo_tipo
            GROUP BY
                ococuf_evento,
                coluna2,
                ococtipo_filtro
            ORDER BY
                ococuf_evento,
                coluna2
            )

            UNION ALL
            -------------------------------------------
            -- Detalhado Estado/Cidade/Marca/Modelo/Veiculo Tipo
            -------------------------------------------
            (
            SELECT
                'por_est_cidade_modelo_tipo'::TEXT AS tipo,
                ococuf_evento AS coluna1,
                (CASE
                    WHEN mlotipveioid IN (1,2) THEN 'PESADO'
                    ELSE 'LEVE'
                    END
                ) AS coluna2,
                '' AS coluna3,
                '' AS coluna4,
                (ococmarca_veiculo ||'/'|| ococmodelo_veiculo) AS coluna5,
                '' AS coluna6,
                '' AS coluna7,
                '' AS coluna8,
                ococcidade_evento AS coluna9,
                '' AS coluna10,
                '' AS coluna11,
                SUM(recuperados) as recuperados,
                SUM(nao_recuperados) as nao_recuperados,
                ococtipo_filtro
            FROM (
                SELECT
                    ococuf_evento,
                    ococcidade_evento,
                    ococmarca_veiculo,
                    ococmodelo_veiculo,
                    EXTRACT (DOW FROM ococdata_roubo::TIMESTAMP) AS dia_semana,
                    COALESCE ((CASE
                            WHEN ococstatus = 'Recuperado'
                                 AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                            THEN 1
                            END),0) as recuperados,
                    COALESCE ((CASE
                            WHEN ococstatus = 'Não Recuperado'
                            THEN 1
                            END),0) as nao_recuperados,
                    (SELECT
                        mlotipveioid
                    FROM
                        modelo
                    LEFT JOIN
                        tipo_veiculo ON (tipvoid = mlotipveioid)
                    WHERE
                        TRIM(tipvdescricao) ILIKE TRIM(ococtipo_veiculo)
                    LIMIT 1) AS mlotipveioid,
                    ococtipo_filtro
                FROM
                    sintetico_temp
                WHERE
                    ococstatus LIKE '%Recuperado'
                ) AS estado_veiculo_tipo
            GROUP BY
                ococuf_evento,
                coluna2,
                ococcidade_evento,
                ococmarca_veiculo,
                ococmodelo_veiculo,
                ococtipo_filtro

            ORDER BY
                ococuf_evento,
                coluna2,
                ococcidade_evento,
                ococmarca_veiculo,
                ococmodelo_veiculo
            )

            UNION ALL
            -------------------------------------------
            -- Detalhado Recuperações
            -------------------------------------------
            (
            SELECT
                'detalhado'::text AS tipo,
                ococcliente AS coluna1,
                ococplaca AS coluna2,
                ococveiculo_ano AS coluna3,
                ococveiculo_chassi AS coluna4,
                (ococmarca_veiculo ||'/'|| ococmodelo_veiculo) AS coluna5,
                ococcnpj_cpf AS coluna6,
                ococdata_roubo AS coluna7,
                data_comunicacao AS coluna8,
                ococcidade_evento AS coluna9,
                ococdata_recuperacao AS coluna10,
                ococcidade_recuperado AS coluna11,
                COALESCE ((CASE
                        WHEN ococstatus = 'Recuperado'
                             AND ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                        THEN 1
                        END),0) AS recuperados,
                COALESCE ((CASE
                        WHEN ococstatus = 'Não Recuperado'
                        THEN 1
                        END),0) AS nao_recuperados,
                ococtipo_filtro
            FROM
                sintetico_temp
            WHERE
                ococstatus LIKE '%Recuperado'
             ORDER BY
                ococdata_roubo::TIMESTAMP,
                ococcliente
            )
            ";

        return $sql;
    }

    public function buscarDadosSinteticoResumido($parametros) {

        if (empty($parametros->congeladosID)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO, 1100);
		}

        $dados = new stdClass();

        //Somente status em andamento
        $totalOcorrencias = $this->buscarOcorrenciasAndamento($parametros->congeladosID, $parametros);

        //Equipamentos instalados
        $totalEquipamentos = $this-> totalizarOcorrenciasEquipamento($parametros);

        //Somente dados Principais
        $sql = $this->pesquisarCongelados($parametros->congeladosID, $parametros, false, false, true);

        /*
         * Totalizadores complementares
         */
        $sql .= "
            SELECT
                ((total_veiculos_recuperados + total_veiculos_nao_recuperados) - recuperadas_anterior) AS atendidas_periodo,
                (total_ocorrencias_rec_nrec - recuperadas_anterior) AS total_ocorrencias,
                recuperadas_anterior,
                rastreador,
                total_veiculos_recuperados,
                total_veiculos_nao_recuperados
            FROM (
                SELECT
                (
                    SELECT COUNT(1)
                    FROM sintetico_temp
                    WHERE ococstatus LIKE '%Recuperado'

                ) AS total_ocorrencias_rec_nrec,
                (
                    SELECT COUNT(1)
                    FROM sintetico_temp
                    WHERE data_comunicacao::DATE < '".$parametros->ococdperiodo_inicial."'
                    AND ococstatus = 'Recuperado'

                ) AS recuperadas_anterior,
                (
                    SELECT
                        COUNT(ococrastreador)
                    FROM
                        sintetico_temp
                    WHERE
                        ococrastreador='S'
                ) AS rastreador,
                (
                    SELECT
                        COUNT(ocococorrencia_motivo_equip_sem_contato)
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus = 'Recuperado'
                    AND
                        ococdata_recuperacao IS NOT NULL

                )AS total_veiculos_recuperados
                ,(
                    SELECT
                        COUNT(ocococorrencia_motivo_equip_sem_contato)
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus LIKE 'N_o Recuperado'

                )AS total_veiculos_nao_recuperados
            ) AS totais_complemento;
            ";

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

       while ($tupla = pg_fetch_object($rs)) {
            $dados->total_andamento = $totalOcorrencias;
            $dados->total_equipamentos = $totalEquipamentos;
            $dados->atendidas_periodo = $tupla->atendidas_periodo;
            $dados->total_ocorrencias = $tupla->total_ocorrencias;
            $dados->recuperadas_anterior = $tupla->recuperadas_anterior ;
            $dados->total_rastreador = $tupla->rastreador ;
            $dados->total_veiculos_recuperados = $tupla->total_veiculos_recuperados;
            $dados->total_veiculos_nao_recuperados = $tupla->total_veiculos_nao_recuperados;
       }

       /*
        * Totalizadores recuperados / Não recuperados
        */
       $sql = "
           (
                SELECT
                 DISTINCT ON (ocococorrencia_motivo_equip_sem_contato)ocococorrencia_motivo_equip_sem_contato AS motivo_ocorrencia,
                 COUNT(ocococorrencia_motivo_equip_sem_contato) OVER (PARTITION BY ocococorrencia_motivo_equip_sem_contato) AS veiculos,
                 'recuperados'::VARCHAR AS tipo
                 FROM
                    sintetico_temp
                 WHERE
                    ococstatus = 'Recuperado'
                 ORDER BY
                    motivo_ocorrencia
              )UNION ALL (

                SELECT
                    DISTINCT ON (ocococorrencia_motivo_equip_sem_contato)ocococorrencia_motivo_equip_sem_contato AS motivo_ocorrencia,
                    COUNT(ocococorrencia_motivo_equip_sem_contato) OVER (PARTITION BY ocococorrencia_motivo_equip_sem_contato) AS veiculos,
                    'nao_recuperados'::VARCHAR AS tipo
                FROM
                   sintetico_temp
                WHERE
                    ococstatus ILIKE 'N%Recuperado'
                ORDER BY
                    motivo_ocorrencia
             )
           ";

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

       while ($tupla = pg_fetch_object($rs)) {
            $dados->recuperacoes[] = $tupla;
       }

       /*
        * Totais por tipo de veículos
        */
        $sql = "
            SELECT
                tipo_veiculo,
                (COUNT(tipo_veiculo))::varchar AS total
            FROM
                (
                SELECT
                       (SELECT
                            (
                                CASE WHEN mlotipveioid NOT IN (1,2,7)
                                THEN 'Leve'
                                WHEN mlotipveioid IN (1,2)
                                THEN 'Pesado'
                                WHEN mlotipveioid = 7
                                THEN 'Moto'
                                END
                            ) AS tipo_veiculo
                        FROM
                            modelo
                        LEFT JOIN
                            tipo_veiculo ON (tipvoid = mlotipveioid)
                        WHERE
                            TRIM(tipvdescricao) ILIKE TRIM(ococtipo_veiculo)
                        LIMIT 1
                        )AS tipo_veiculo
                FROM
                    sintetico_temp
                WHERE
                    ococstatus = 'Recuperado'
                ) AS FOO
             GROUP BY
                tipo_veiculo
            ";

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

       while ($tupla = pg_fetch_object($rs)) {
            $dados->tipo_veiculo[] = $tupla;
       }

       /*
        * Resumo Mensal
        */
        $sql = "
           SELECT
                data_comunic
                ,ococseguradora
                ,SUM(qtd_recup) AS total_recup
                ,SUM(qtd_nao_recup) AS total_nao_recup
           FROM
                (
                    (SELECT
                        TO_CHAR(data_comunicacao::DATE, 'mm/YYYY') AS data_comunic
                        ,ococseguradora
                        ,count(ococstatus) AS qtd_recup
                        ,0 AS qtd_nao_recup
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus = 'Recuperado'
                    AND (
                            (
                                (ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."')
                                AND (data_comunicacao::DATE  >= '".$parametros->ococdperiodo_inicial."'
                                AND data_comunicacao::DATE  <= '".$parametros->ococdperiodo_final."')
                            )
                            OR
                            (
                                (data_comunicacao::DATE  <= '".$parametros->ococdperiodo_final."')
                                AND (ococdata_recuperacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                                AND ococdata_recuperacao::DATE <= '".$parametros->ococdperiodo_final."')
                            )
                        )
                    GROUP BY
                        data_comunic,
                        ococseguradora
                    ) UNION ALL
                    (SELECT
                        TO_CHAR(data_comunicacao::DATE, 'mm/YYYY') AS data_comunic
                        ,ococseguradora
                        ,0 AS qtd_recup
                        ,count(ococstatus) AS qtd_nao_recup
                    FROM
                        sintetico_temp
                    WHERE
                        ococstatus = 'Não Recuperado'
                    AND
                        data_comunicacao::DATE >= '".$parametros->ococdperiodo_inicial."'
                    AND
                        data_comunicacao::DATE <= '".$parametros->ococdperiodo_final."'
                    GROUP BY
                        data_comunic,
                        ococseguradora
                    )
            )AS meses
            GROUP BY
                data_comunic,
                ococseguradora
            ORDER BY
                ococseguradora
           ";
        //echo "<pre>".$sql;exit;
        if (!$rs = pg_query($this->conn, $sql)){
             throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $arrayResumoMensal = array();

        if(pg_num_rows($rs) > 0) {

            $mesInicial = intval(substr(pg_fetch_result($rs,0,"data_comunic"),0,2));

            if($mesInicial != "1") {
                $anoInicial = intval(substr(pg_fetch_result($rs,0,"data_comunic"),3,4));
                $mesInicial = $mesInicial - 1;
            }else {
                $mesInicial = 12;
                $anoInicial = intval(substr(pg_fetch_result($rs,0,"data_comunic"),3,4)-1);
            }

            $mesFinal = intval(substr($parametros->ococdperiodo_final,3,2));
            $anoFinal = intval(substr($parametros->ococdperiodo_final,6,4));

            $dataInicial = date('Y/m/d',mktime(0,0,0,$mesInicial,1,$anoInicial));
            $dataFinal = date('Y/m/d',mktime(0,0,0,$mesFinal,1,$anoFinal));

            $mesApoio = $mesInicial;
            $anoApoio = $anoInicial;
            $colunas = 0;
            $vetorData = array();

            $dataMutante = $dataInicial;

             while($dataFinal > $dataMutante) {

                    $mesApoio++;

                    if($mesApoio == 13) {
                        $anoApoio++;
                        $mesApoio = 1;
                    }

                    $dataMutante = date('Y/m/d',mktime(0,0,0,$mesApoio,1,$anoApoio));

                    $vetorData[$colunas] = ($mesApoio < 9)  ? "0" . $mesApoio . "/". $anoApoio : $mesApoio ."/". $anoApoio;

                    $colunas++;
             }

            $meses = count($vetorData);

            while ($row = pg_fetch_object($rs)) {

                for($i = 0; $i < $meses; $i++) {

                    $recups = 0;
                    $nrecups = 0;

                    if($vetorData[$i] == $row->data_comunic) {
                        $recups = $row->total_recup;
                        $nrecups = $row->total_nao_recup;
                    }

                   $mesData = substr($vetorData[$i], 0, 2);
				   $anoData = substr($vetorData[$i], 3, 4);
                   $mesIndice =  $anoData ."-". $mesData ."-01";

                    $arrayResumoMensal[$row->ococseguradora][$mesIndice]->total_recup += $recups;
                    $arrayResumoMensal[$row->ococseguradora][$mesIndice]->total_nao_recup += $nrecups;

                }
            }

        }

        $dados->resumo_mensal = $arrayResumoMensal;
        return $dados;

    }


    /**
     * Totaliza as ocorrencias com Equipamentos Instalados
     *
     * @param stdClass $parametros
     * @return int
     * @throws ErrorException
     */
    private function totalizarOcorrenciasEquipamento($parametros) {

         if (empty($parametros->congeladosID)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO, 1100);
		}

        $retorno = 0;

        $sql = "
            SELECT
               COUNT(1) AS total
            FROM
                ocorrencia_congelamento_equipamento
            WHERE
                ococeococdoid IN (".$parametros->congeladosID.")
            ";

        //filtro por classe de equipamento
		if (isset($parametros->filtrar_classe_equipamento) && !empty($parametros->filtrar_classe_equipamento)) {

			$sql .= " AND
				 			TRIM(ococeclasse_equipamento) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe_equipamento)) . "%' ";

		}

		//filtro por classe de contrato
		if (isset($parametros->filtrar_classe_contrato) && !empty($parametros->filtrar_classe_contrato)) {

			$sql .= " AND
				 			TRIM(ococeclasse_contrato) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe_contrato)) . "%' ";

		}

		//filtro por classe de grupo
		if (isset($parametros->filtrar_classe_grupo) && !empty($parametros->filtrar_classe_grupo)) {

			$sql .= " AND
				 			TRIM(ococeclasse_grupo) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_classe_grupo)) . "%' ";

		}

		//filtro por projeto
		if (isset($parametros->filtrar_equipamento_projeto) && !empty($parametros->filtrar_equipamento_projeto)) {

			$sql .= " AND
							TRIM(ococeprojeto_equipamento) ILIKE '%" . pg_escape_string(trim($parametros->filtrar_equipamento_projeto)) . "%' ";

		}

		//filtrar por tipo proposta
		if (isset($parametros->filtrar_tipo_proposta) && !empty($parametros->filtrar_tipo_proposta)) {

			$sql .= " AND
							(TRIM(ococetipo_proposta) = '" . pg_escape_string(trim($parametros->filtrar_tipo_proposta)) . "'
                                OR TRIM(ococesupertipo_proposta) = '" . pg_escape_string(trim($parametros->filtrar_tipo_proposta)) . "'
                            )
                    ";

		}

		//filtrar por tipo contrato
		if (isset($parametros->filtrar_tipo_contrato) && !empty($parametros->filtrar_tipo_contrato)) {

			$sql .= " AND
							TRIM(ococetipo_contrato) = '" . pg_escape_string(trim($parametros->filtrar_tipo_contrato)) . "' ";

		}

		//filtrar por modalidade de contrato
		if (isset($parametros->filtrar_modalidade_contrato) && !empty($parametros->filtrar_modalidade_contrato)) {

			$sql .= " AND
				 			TRIM(ococecontrato_modalidade) = '" . pg_escape_string(trim($parametros->filtrar_modalidade_contrato)) . "' ";

		}

        //filtro por seguradora
		if (isset($parametros->filtrar_seguradora) && !empty($parametros->filtrar_seguradora)) {


			if($parametros->filtrar_seguradora == "ROD") { // SE FOR "RODOBENS - TODOS "

				$sql .= " AND
				 				TRIM(ococetipo_contrato) ILIKE '%RODOBENS%' AND TRIM(ococetipo_contrato) NOT ILIKE 'Ex%'";

			} elseif($parametros->filtrar_seguradora == "RODS") { // SE FOR "RODOBENS - ESPECIAL "

				//$sql.=" and ocotpcoid in (7,64,69,75,90) "; // 7|Bradesco Rodobens, 64|Bradesco Rodobens Carga, 69|Rodobens Corretora, 75|SulAmerica ODO, 90|Sulamerica Sob Medida

				$sql .= " AND
				 				TRIM(ococetipo_contrato) ILIKE '%Bradesco Rodobens%' ||  TRIM(ococetipo_contrato) ILIKE '%Bradesco Rodobens Carga%' ||  TRIM(ococetipo_contrato) ILIKE '%Rodobens Corretora%' ||  TRIM(ococetipo_contrato) ILIKE '%SulAmerica ODO%' ||  TRIM(ococetipo_contrato) ILIKE '%Sulamerica Sob Medida%' ";

			} elseif($parametros->filtrar_seguradora == "BRAT") { // SE FOR "BRADESCO - TODOS "

				//$sql.=" and ocotpcoid in (select tpcoid from tipo_contrato where tpcdescricao ilike 'BRADESCO%' and tpcdescricao not ilike 'Ex%' and tpcoid=ocotpcoid) ";

				$sql .= " AND
				 				TRIM(ococetipo_contrato) ILIKE '%BRADESCO%' AND TRIM(ococetipo_contrato) NOT ILIKE 'Ex%'";

			} elseif($parametros->filtrar_seguradora == "CLI") { // SE FOR "CLIENTES "

				$sqlFiltro = "SELECT DISTINCT tpcdescricao FROM tipo_contrato WHERE tpcseguradora IS FALSE AND tpcdescricao NOT ILIKE 'Ex%'";

				if (!$rsFiltro = pg_query($this->conn, $sqlFiltro)){
					throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
				}

				$filtro = "";
				while ($tipo = pg_fetch_object($rsFiltro)) {
					$filtro .= "'" . trim($tipo->tpcdescricao) . "',";
				}

				$filtro = rtrim($filtro,',');

				$sql .= " AND
				 				TRIM(ococetipo_contrato) IN (" . $filtro . ")";

			} elseif($parametros->filtrar_seguradora == "SEG") { // SE FOR "SEGURADORA - TODOS"

				//        $str.=" and ocotpcoid in (select tpcoid from tipo_contrato where tpcseguradora is true and tpcdescricao not ilike 'Ex%' and tpcoid=ocotpcoid) ";
				//$sql.=" and veisegoid IS NOT NULL and veisegoid != 2 ";

				$sqlFiltro = "SELECT DISTINCT segseguradora FROM seguradora WHERE segoid <> 2 ";

				if (!$rsFiltro = pg_query($this->conn, $sqlFiltro)){
					throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
				}

				$filtro = "";
				while ($tipo = pg_fetch_object($rsFiltro)) {
					$filtro .= "'" . trim($tipo->segseguradora) . "',";
				}

				$filtro = rtrim($filtro,',');

				$sql .= " AND
				 				TRIM(ococeseguradora) IS NOT NULL AND TRIM(ococeseguradora) NOT IN (" . $filtro . ") ";



			} elseif($parametros->filtrar_seguradora == "UNBT") { // SE FOR "UNIBANCO - TODOS "

				//$sql.=" and ocotpcoid in (select tpcoid from tipo_contrato where tpcdescricao ilike 'UNIBANCO%' and tpcdescricao not ilike 'Ex%' and tpcoid=ocotpcoid) ";

				$sql .= " AND
				 				TRIM(ococetipo_contrato) ILIKE '%UNIBANCO%' AND TRIM(ococetipo_contrato) NOT ILIKE 'Ex%' ";

			} elseif($parametros->filtrar_seguradora == "SULT") { // SE FOR "SULAMERICA - TODOS "

				//$sql.=" and ocotpcoid in (select tpcoid from tipo_contrato where tpcdescricao ilike 'SULAMERICA%' and tpcdescricao not ilike 'Ex%' and tpcoid=ocotpcoid) ";



				$sql .= " AND
				 				(
				 					TRIM(ococetipo_contrato) ILIKE '%SULAMERICA%'
				 					OR
				 					TRIM(ococetipo_contrato) ILIKE '%SUL AMERICA%'
				 				)
				 		 AND TRIM(ococetipo_contrato) NOT ILIKE 'Ex%' ";

			} else {
				//default

				$parametros->filtrar_seguradora = trim($parametros->filtrar_seguradora);

				$sql .= " AND
				 				TRIM(ococeseguradora) = '" . pg_escape_string(trim($parametros->filtrar_seguradora)) . "' ";
			}

		}

        //echo "<pre>". $sql;exit;
        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

        $retorno = pg_fetch_result($rs, 0, "total");

        return $retorno;

    }

	public function buscarFormaNotificacao() {

		$retorno = array();

		$sql = "SELECT
					ofnoid,
					ofndescricao
                FROM
                	ocorrencia_forma_notificacao
                WHERE
                	ofndt_exclusao IS NULL
                ORDER BY
                	ofndescricao;
		";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($formaNotif = pg_fetch_object($rs)) {
				$retorno[$formaNotif->ofnoid] = $formaNotif->ofndescricao;
			}
		}

		return $retorno;
	}

	public function buscarClasseGrupo() {

		$retorno = array();

		$sql = "SELECT
					ecgoid,
					ecgdescricao
                FROM
                	equipamento_classe_grupo
                WHERE
                	ecgexclusao IS NULL
		";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($classGrupo = pg_fetch_object($rs)) {
				$retorno[$classGrupo->ecgoid] = $classGrupo->ecgdescricao;
			}
		}

		return $retorno;
	}

	public function buscarAtendentes() {

		$retorno = array();

		$sql = "SELECT
					cd_usuario,
					ds_login
                FROM
                	usuarios
                WHERE
                	usudepoid=8
                    AND dt_exclusao IS NULL
                ORDER BY
                	ds_login
        ";

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($atendente = pg_fetch_object($rs)) {
				$retorno[$atendente->cd_usuario] = $atendente->ds_login;
			}
		}

		return $retorno;
	}

	public function buscarEquipamentoProjetos() {

		$retorno = array();

		$sql = "SELECT
					eproid,
					eprnome
                FROM
                	equipamento_projeto
		";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($projeto = pg_fetch_object($rs)) {
				$retorno[$projeto->eproid] = $projeto->eprnome;
			}
		}

		return $retorno;
	}

	public function buscarMotivoEquipSemContato() {

		$retorno = array();

		$sql = "SELECT
					omecoid,
					omecdescricao
                FROM
                	ocorrencia_motivo_equip_sem_contato
                WHERE
                	omecdt_exclusao IS NULL
                ORDER BY
                	omecdescricao
		";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($motivo = pg_fetch_object($rs)) {
				$retorno[$motivo->omecoid] = $motivo->omecdescricao;
			}
		}

		return $retorno;
	}

	public function buscarListaApoio() {

		$retorno = array();

		$sql = "SELECT
					tetoid,
					formata_str(tetdescricao) AS tetdescricao
				FROM
					telefone_emergencia_tp
                WHERE
                	tetexclusao IS NULL
                    AND tetgrupo_apoio IS TRUE
                ORDER BY
                	tetdescricao
		";
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			while ($apoio = pg_fetch_object($rs)) {
				$retorno[$apoio->tetoid] = $apoio->tetdescricao;
			}
		}

		return $retorno;
	}


	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){

		$retorno = new stdClass();

		$sql = "SELECT
					ococdoid,
					ococdtipo_relatorio,
					ococdperiodo_inicial,
					ococdperiodo_final,
					ococdusuoid_congelamento,
					ococddata_congelamento,
					ococdusuoid_exclusao,
					ococddata_exclusao
				FROM
					ocorrencia_congelamento_dados
				WHERE
					ococdoid =" . intval( $id ) . "";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}


	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPeriodoAgrupado($ids){

		$retorno = new stdClass();

		$sql = "SELECT
					ococdtipo_relatorio,
					to_char(MIN(ococdperiodo_inicial) , 'DD/MM/YYYY') AS ococdperiodo_inicial,
					to_char(MAX(ococdperiodo_final) , 'DD/MM/YYYY') AS ococdperiodo_final
				FROM
					ocorrencia_congelamento_dados
				WHERE
					ococdoid in (".$ids.")
				GROUP BY ococdtipo_relatorio";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}



	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					ocorrencia_congelamento_dados
					(
						ococdoid,
						ococdtipo_relatorio,
						ococdperiodo_inicial,
						ococdperiodo_final,
						ococdusuoid_congelamento,
						ococddata_congelamento,
						ococdusuoid_exclusao
					)
				VALUES
					(
					" . intval( $dados->ococdoid ) . ",
					'" . pg_escape_string( $dados->ococdtipo_relatorio ) . "',
					'" . $dados->ococdperiodo_inicial . "',
					'" . $dados->ococdperiodo_final . "',
					" . intval( $dados->ococdusuoid_congelamento ) . ",
					'" . $dados->ococddata_congelamento . "',
					" . intval( $dados->ococdusuoid_exclusao ) . "
				)";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizar(stdClass $dados){

		$sql = "UPDATE
					ocorrencia_congelamento_dados
				SET
					ococdoid = " . intval( $dados->ococdoid ) . ",
					ococdtipo_relatorio = '" . pg_escape_string( $dados->ococdtipo_relatorio ) . "',
					ococdperiodo_inicial = '" . $dados->ococdperiodo_inicial . "',
					ococdperiodo_final = '" . $dados->ococdperiodo_final . "',
					ococdusuoid_congelamento = " . intval( $dados->ococdusuoid_congelamento ) . ",
					ococddata_congelamento = '" . $dados->ococddata_congelamento . "',
					ococdusuoid_exclusao = " . intval( $dados->ococdusuoid_exclusao ) . "
				WHERE
					 = " . $dados->ococdoid . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir($id){

		$usuarioSessao = isset($_SESSION['usuario']['oid']) && trim($_SESSION['usuario']['oid']) != '' ? $_SESSION['usuario']['oid'] : 0;

		$sql = "UPDATE
							ocorrencia_congelamento_dados
						SET
							ococddata_exclusao = NOW(),
							ococdusuoid_exclusao = " . intval($usuarioSessao) . "
						WHERE
							ococdoid = " . intval( $id ) . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}

	/**
	 * Reativa (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function reativar($id) {

		$sql = "UPDATE
							ocorrencia_congelamento_dados
						SET
							ococddata_exclusao = NULL,
							ococdusuoid_exclusao = NULL
						WHERE
							ococdoid = " . intval( $id ) . "";

		if (!pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return true;
	}

	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transação
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transação
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}
}