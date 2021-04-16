<?php

/**
 * Classe RelFotosOrdemServicoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   Vinicius Senna <teste_desenv@sascar.com.br>
 * 
 */

class RelFotosOrdemServicoDAO {

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
	public function pesquisar(stdClass $parametros, $paginacao = null){

		$retorno = array();

		$seguradora = $this->buscaSeguradora();

		$select = '';

		if(!isset($paginacao)) {
			$select = " COUNT(ord.ordoid) AS total_registros ";
		} else {

			$select = "
						ord.ordoid, 
						ord.ordconnumero,
						cli.clinome,
						mod.mlomodelo,
						vei.veiplaca,
						(
							SELECT 
								inst.itlnome
							FROM 
								instalador AS inst
							WHERE ord.orditloid = itloid
						) AS nome_instalador,
						(
							CASE WHEN (
								SELECT
								1 
								FROM
									siggo_seguro_imagens_os
								WHERE
									ssioordoid = ord.ordoid 
								AND
									ssiodt_exclusao IS NULL 
							) IS NULL THEN 'Não'
							ELSE
								'Sim'
							END
						) AS possui_fotografia,
						(
							SELECT 
								endvuf
						   	FROM 
						   		endereco_representante
						   	WHERE 
					     		rr.relrrep_terceirooid = endvrepoid
				     	) AS uf_representante,
						rep.repnome
						";

		}

		$sql = " SELECT 
					". $select ."
				FROM 
					ordem_servico AS ord
				INNER JOIN
					clientes AS cli
					ON cli.clioid = ord.ordclioid 
				INNER JOIN
					equipamento_classe AS eqc
					ON eqc.eqcoid = ord.ordeqcoid
				INNER JOIN
					veiculo AS vei 
					ON ord.ordveioid = vei.veioid
				INNER JOIN
					modelo AS mod 
					ON mod.mlooid = vei.veimlooid
				INNER JOIN
					marca mar 
					ON mar.mcaoid = mod.mlomcaoid
				INNER JOIN 
					relacionamento_representante AS rr
					ON rr.relroid = ord.ordrelroid
				INNER JOIN 
					representante AS rep
			        ON rep.repoid = rr.relrrepoid 
			    WHERE ";

		if($parametros->combo_visualizar == 'O') {
			// OS concluida
			$sql .= " ord.ordstatus = 3 ";
		} else if($parametros->combo_visualizar == 'C') {
			// Cadastro de equipamento e status Autorizado
			$sql .= " EXISTS (SELECT 1 FROM contrato WHERE connumero = ord.ordconnumero AND conequoid IS NOT NULL)
						AND ord.ordstatus = 4 ";
		} else {
			// Status Concluído e Autorizado
			$sql .= " ord.ordstatus IN (3,4) ";
		}

		// Numero OS
		if ( isset($parametros->ordoid) && trim($parametros->ordoid) != '' ) {
	        $sql .= " AND ord.ordoid = " . intval( $parametros->ordoid ) . " ";
	    }

	    // Periodo
	    if( isset($parametros->data_inicial) && trim($parametros->data_inicial) != ''
	    	&& isset($parametros->data_final) && trim($parametros->data_final) != '' ) {

	    	$sql .= " AND ord.orddt_ordem BETWEEN '" . $parametros->data_inicial . " 00:00:00' 
	    				AND '" . $parametros->data_final . " 23:59:59' ";
	    }

	    // OS sem foto
		if($parametros->os_sem_foto == 'on') {
			$sql .= " AND  NOT EXISTS (SELECT 1 FROM siggo_seguro_imagens_os WHERE ssioordoid = ord.ordoid AND ssiodt_exclusao IS NULL AND ssiousuoid_exclusao IS NULL) ";
		}

		$sql .= " AND EXISTS (SELECT 1 FROM equipamento_classe_beneficio AS ecb
					WHERE (ecb.eqcbeqcoid= eqc.eqcoid AND ecb.eqcbebtoid = ". intval($seguradora->ebtoid)  ." AND ecb.eqcbdt_exclusao IS NULL)) ";

		$sql .= " AND EXISTS(
						SELECT
						1
						FROM
							ordem_servico_item AS osi 
							INNER JOIN 
							os_tipo_item AS oti 
							ON oti.otioid = osi.ositotioid
						INNER JOIN
							os_tipo AS ot 
							ON ot.ostoid = oti.otiostoid
						WHERE
						 ot.ostoid IN (1,2,9)
						 AND (ord.ordoid = osi.ositordoid AND ositexclusao IS NULL AND otidt_exclusao IS NULL AND otioid IN(3,361))
					) ";

	    if (isset($paginacao->limite) && isset($paginacao->offset)) {
	    	
            $sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";

        }
        
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}


		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}

	/**
	 * Realiza consulta utilizada para gerar o arquivo CSV
	 * @param  stdClass $parametros Filtros da pesquisa
	 * @return [type]               [description]
	 */
	public function pesquisaCSV($parametros) {

		$retorno = array();

		$seguradora = $this->buscaSeguradora();


		$sql = "SELECT
					ord.ordoid, 
					ord.ordconnumero,
					ord.orddt_ordem AS dt_criacao_os,
					cli.clinome,
					mod.mlomodelo,
					mar.mcamarca AS marca,
					vei.veiplaca AS placa,
					vei.veichassi AS chassi,
					vei.veino_ano AS ano_veiculo,
					rep.repnome,
					rep.repoid,
					eqc.eqcdescricao AS classe,
					(
						SELECT 
							inst.itlnome
						FROM 
							instalador AS inst
						WHERE ord.orditloid = itloid
					) AS nome_instalador,
					(
						CASE WHEN (
							SELECT
							1 
							FROM
								siggo_seguro_imagens_os
							WHERE
								ssioordoid = ord.ordoid 
							AND
								ssiodt_exclusao IS NULL 
						) IS NULL THEN 'Não'
						ELSE
							'Sim'
						END
					) AS possui_fotografia,
					(
						SELECT endvuf
					   FROM endereco_representante,
					        relacionamento_representante
					   WHERE relroid=ordrelroid
					     AND relrrep_terceirooid=endvrepoid
			     	) AS uf_representante,
					(SELECT eprnome
					   FROM equipamento_projeto,
					        equipamento,
					        equipamento_versao
					   WHERE equoid=ordequoid
					     AND equeveoid=eveoid
					     AND eveprojeto=eproid) AS modelo_equipamento,
					(SELECT tipvdescricao
					   FROM tipo_veiculo,
					        modelo
					   WHERE mlotipveioid=tipvoid
					     AND mlooid=veimlooid) AS tipo_veiculo,
					(
		                SELECT 
		                    TO_CHAR(orsdt_situacao,'dd/mm/yyyy') 
		                FROM 
		                    ordem_situacao 
		                WHERE 
		                    orsordoid = ordoid 
		                AND 
		                	ordstatus = 3
		                ORDER BY 
		                    orsdt_situacao DESC LIMIT 1
		            ) AS data_conclusao
				FROM 
					ordem_servico AS ord
				INNER JOIN
					clientes AS cli
					ON cli.clioid = ord.ordclioid 
				INNER JOIN
					equipamento_classe AS eqc
					ON eqc.eqcoid = ord.ordeqcoid
				INNER JOIN
					veiculo AS vei 
					ON ord.ordveioid = vei.veioid
				INNER JOIN
					modelo AS mod 
					ON mod.mlooid = vei.veimlooid
				INNER JOIN
					marca mar 
					ON mar.mcaoid = mod.mlomcaoid
				INNER JOIN 
					relacionamento_representante AS rr
					ON rr.relroid = ord.ordrelroid
				INNER JOIN 
					representante AS rep
			        ON rep.repoid = rr.relrrepoid
				WHERE ";

        if($parametros->combo_visualizar == 'O') {
			// OS concluida
			$sql .= " ord.ordstatus = 3 ";
		} else if($parametros->combo_visualizar == 'C') {
			// Cadastro de equipamento e status Autorizado
			$sql .= " EXISTS (SELECT 1 FROM contrato WHERE connumero = ord.ordconnumero AND conequoid IS NOT NULL)
						AND ord.ordstatus = 4 ";
		} else {
			// Status Concluído e Autorizado
			$sql .= " ord.ordstatus IN (3,4) ";
		}

		//$sql .= " AND ord.ordmtioid IN (2,4) ";
		// Motivo instalacao/reinstalacao/reinstalacao nao cobrar
		//$sql .= " AND ot.ostoid IN (1,2,9) ";

		// Numero OS
		if ( isset($parametros->ordoid) && trim($parametros->ordoid) != '' ) {
	        $sql .= " AND ord.ordoid = " . intval( $parametros->ordoid ) . " ";
	    }

	    // Periodo
	    if( isset($parametros->data_inicial) && trim($parametros->data_inicial) != ''
	    	&& isset($parametros->data_final) && trim($parametros->data_final) != '' ) {

	    	$sql .= " AND ord.orddt_ordem BETWEEN '" . $parametros->data_inicial . " 00:00:00' 
	    				AND '" . $parametros->data_final . " 23:59:59' ";
	    }

		// Condição OS sem foto
		if($parametros->os_sem_foto == 'on') {
			$sql .= " AND  NOT EXISTS (SELECT 1 FROM siggo_seguro_imagens_os WHERE ssioordoid = ord.ordoid AND ssiodt_exclusao IS NULL AND ssiousuoid_exclusao IS NULL) ";
		}

		$sql .= " AND EXISTS (SELECT 1 FROM equipamento_classe_beneficio AS ecb
					WHERE (ecb.eqcbeqcoid= eqc.eqcoid AND ecb.eqcbebtoid = ". intval($seguradora->ebtoid)  ." AND ecb.eqcbdt_exclusao IS NULL)) ";
		
		$sql .= " AND EXISTS(
						SELECT
						1
						FROM
							ordem_servico_item AS osi 
							INNER JOIN 
							os_tipo_item AS oti 
							ON oti.otioid = osi.ositotioid
						INNER JOIN
							os_tipo AS ot 
							ON ot.ostoid = oti.otiostoid
						WHERE
						 ot.ostoid IN (1,2,9)
						 AND (ord.ordoid = osi.ositordoid AND ositexclusao IS NULL AND otidt_exclusao IS NULL AND otioid IN(3,361))
					) ";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}


		while($row = pg_fetch_object($rs)) {
			$retorno[] = $row; 
		}

		return $retorno;
	}

	/**
	 * Busca id da seguradora
	 * @return [type] [description]
	 */
	public function buscaSeguradora() {

		$retorno = new stdClass();

		$sql = "SELECT 
                	emboid,
                	ebtoid
	            FROM 
	                empresa_beneficio
	            INNER JOIN empresa_beneficio_tipo ON ebtemboid = emboid
	            WHERE 
	                ebtdescricao = 'SEGURO'
	            AND 
	                embdt_exclusao IS NULL
	            AND 
	                ebtdt_exclusao IS NULL";

        if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
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
?>
