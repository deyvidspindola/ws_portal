<?php

/**
 * Classe RelObrigacaoFinanceiraReajusteHistoricoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 *
 */
class RelObrigacaoFinanceiraReajusteHistoricoDAO {

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
	public function pesquisar(stdClass $parametros){

		$retorno = array();
		$whereEqpto = '';
		$whereAcessorio = '';

		$sql = "SELECT
					clinome,
					clioid,
					CASE WHEN ofrhtipo_reajuste = '1' THEN 'IGPM' ELSE 'INPC' END AS tp_reajuste,
     				--nflno_numero,
     				--nflserie,
					ofrhconnumero,
					to_char(condt_ini_vigencia, 'dd-mm-yyyy') as condt_ini_vigencia,
					to_char(ofrhdt_referencia, 'mm-yyyy') as ofrhdt_referencia,
					ofrhvalor_anterior,
					ofrhvalor_reajustado,
					obrobrigacao,
					CASE 
						WHEN ofrhnfloid is null THEN 'NAO' 
						ELSE 'SIM' 
					END AS ofrhnfloid,
					csidescricao,
					tpcdescricao,
					coneqcoid
				FROM
				    obrigacao_financeira_reajuste_historico
		 		INNER JOIN clientes ON ofrhclioid = clioid
		 		INNER JOIN contrato ON ofrhconnumero = connumero
		  		LEFT JOIN obrigacao_financeira ON obroid = ofrhobroid
		  		LEFT JOIN nota_fiscal ON nfloid = ofrhnfloid
		  		INNER JOIN contrato_situacao ON csioid = concsioid
		  		INNER JOIN tipo_contrato ON tpcoid = conno_tipo
			  	WHERE 1 = 1 ";

		if (isset($parametros->ofrhoid) && trim($parametros->ofrhoid) != '' ) {
			$sql .= " AND ofrhoid = " . intval( $parametros->ofrhoid ) . "";
		}

		if (isset($parametros->dt_ini) && trim($parametros->dt_fim) != '' ) {
			
			if ($parametros->dt_ini > $parametros->dt_fim){
				throw new RuntimeException('O período final não pode ser menor que o período inicial.');
			}
			
			$sql .= " AND ofrhdt_referencia BETWEEN '" . $parametros->dt_ini . " 00:00:00' AND '" . $parametros->dt_fim . " 23:59:59'";

			$whereEqpto .= " AND cprhdt_referencia BETWEEN '" . $parametros->dt_ini . " 00:00:00' AND '" . $parametros->dt_fim . " 23:59:59'";

			$whereAcessorio .= " AND csrhdt_referencia BETWEEN '" . $parametros->dt_ini . " 00:00:00' AND '" . $parametros->dt_fim . " 23:59:59'";

		}

		 
		if (isset($parametros->clinome) && trim($parametros->clinome) != '' ) {
			$sql .= " AND clinome ILIKE '" . pg_escape_string( $parametros->clinome) . "%' ";

			$whereEqpto .= " AND clinome ILIKE '" . pg_escape_string( $parametros->clinome) . "%' ";

			$whereAcessorio .= " AND clinome ILIKE '" . pg_escape_string( $parametros->clinome) . "%' ";
		}

		 
		if (isset($parametros->ofrhconnumero) && trim($parametros->ofrhconnumero) != '' ) {
			
			if (!is_numeric($parametros->ofrhconnumero)){
				throw new RuntimeException('O campo Contrato deve ser númerico');
			}
				
			$sql .= " AND ofrhconnumero = " . intval( $parametros->ofrhconnumero ) . "";

			$whereEqpto .= " AND connumero = " . intval( $parametros->ofrhconnumero ) . "";

			$whereAcessorio .= " AND connumero = " . intval( $parametros->ofrhconnumero ) . "";
		}


		if (isset($parametros->ofrhtipo_reajuste) && trim($parametros->ofrhtipo_reajuste) != '' ) {
			$sql .= " AND ofrhtipo_reajuste = '" . intval( $parametros->ofrhtipo_reajuste ) . "' ";

			$whereEqpto .= " AND cprhtipo_reajuste = '" . intval( $parametros->ofrhtipo_reajuste ) . "' ";

			$whereAcessorio .= " AND csrhtipo_reajuste = '" . intval( $parametros->ofrhtipo_reajuste ) . "' ";

		}

		if (isset($parametros->ofrhnfloid) && trim($parametros->ofrhnfloid) != '' ) {

			if ($parametros->ofrhnfloid == "1"){
				$sql .= " AND ofrhnfloid is not null";
				$whereEqpto .= " AND cprhnfloid is not null";
				$whereAcessorio .= " AND csrhnfloid is not null";
			} else if ($parametros->ofrhnfloid == "2"){
				$sql .=  " AND ofrhnfloid is null";
				$whereEqpto .=  " AND cprhnfloid is null";
				$whereAcessorio .=  " AND csrhnfloid is null";
			}
		}

		 
		if (isset($parametros->tipo_contrato) && trim($parametros->tipo_contrato) != '' ) {
			$sql .= " AND conno_tipo = '" . intval( $parametros->tipo_contrato) . "' ";
			$whereEqpto .= " AND conno_tipo = '" . intval( $parametros->tipo_contrato) . "' ";

			$whereAcessorio .= " AND conno_tipo = '" . intval( $parametros->tipo_contrato) . "' ";
		}

		if ( isset($parametros->dt_ini) && trim($parametros->dt_fim) == ''
				&&  isset($parametros->clinome) && trim($parametros->clinome) == ''
				&&  isset($parametros->ofrhconnumero) && trim($parametros->ofrhconnumero) == ''
				&&  isset($parametros->ofrhtipo_reajuste) && trim($parametros->ofrhtipo_reajuste)== ''
				&&	isset($parametros->ofrhnfloid) && trim($parametros->ofrhnfloid) == ''
				&&  isset($parametros->tipo_contrato) && trim($parametros->tipo_contrato) == ''
		){
			throw new RuntimeException('Pelo menos um filtro deve ser preenchido.');
		}


	//echo '<pre>';
	//print_r($sql); die;
		
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		//equipamento
		$sqlEqpto = " SELECT 
						clinome,
						clioid,
						CASE WHEN cprhtipo_reajuste = '1' THEN 'IGPM' ELSE 'INPC' END AS tp_reajuste,
						connumero AS ofrhconnumero,
						to_char(condt_ini_vigencia, 'dd-mm-yyyy') as condt_ini_vigencia,
						to_char(cprhdt_referencia, 'mm-yyyy') as ofrhdt_referencia,
						cprhvalor_anterior AS ofrhvalor_anterior,
						cprhvalor_reajustado AS ofrhvalor_reajustado,
						obrobrigacao,
						CASE 
							WHEN cprhnfloid is null THEN 'NAO' 
							ELSE 'SIM' 
						END AS ofrhnfloid,
						csidescricao,
						tpcdescricao,
						coneqcoid
					FROM
						contrato_pagamento_reajuste_historico
					INNER JOIN
						contrato_pagamento ON cprhcpagoid = cpagoid
					INNER JOIN 
						contrato ON cpagconoid = connumero
					INNER JOIN
						clientes ON clioid = conclioid
					LEFT JOIN 
						obrigacao_financeira ON cpagobroid_servico = obroid
					INNER JOIN 
						contrato_situacao ON csioid = concsioid
					INNER JOIN 
						tipo_contrato ON tpcoid = conno_tipo
					WHERE 1=1 " . $whereEqpto;

		if (!$rsEqpto = pg_query($this->conn, $sqlEqpto)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($row = pg_fetch_object($rsEqpto)){
			$retorno[] = $row;
		}

		// Acessorios
		$sqlAcessorios = " SELECT 
							clinome,
							clioid,
							CASE WHEN csrhtipo_reajuste = '1' THEN 'IGPM' ELSE 'INPC' END AS tp_reajuste,
							connumero AS ofrhconnumero,
							to_char(condt_ini_vigencia, 'dd-mm-yyyy') as condt_ini_vigencia,
							to_char(csrhdt_referencia, 'mm-yyyy') as ofrhdt_referencia,
							csrhvalor_anterior AS ofrhvalor_anterior,
							csrhvalor_reajustado AS ofrhvalor_reajustado,
							obrobrigacao,
							CASE 
								WHEN csrhnfloid is null THEN 'NAO' 
								ELSE 'SIM' 
							END AS ofrhnfloid,
							csidescricao,
							tpcdescricao,
							coneqcoid
						FROM
							contrato_servico_reajuste_historico
						INNER JOIN
							contrato_servico ON csrhconsoid = consoid
						INNER JOIN 
							contrato ON consconoid = connumero
						INNER JOIN
							clientes ON clioid = conclioid
						LEFT JOIN 
							obrigacao_financeira ON consobroid = obroid
						INNER JOIN 
							contrato_situacao ON csioid = concsioid
						INNER JOIN 
							tipo_contrato ON tpcoid = conno_tipo
						WHERE 1=1 " . $whereAcessorio;

		if (!$rsAcessorio = pg_query($this->conn, $sqlAcessorios)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($row = pg_fetch_object($rsAcessorio)){
			$retorno[] = $row;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa combo Tipo Registro
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function buscarTipoContrato() {

		$sql = " SELECT	tpcoid,
					    tpcdescricao
				   FROM	tipo_contrato
				  WHERE	tpcdescricao IS NOT NULL
		       ORDER BY tpcdescricao";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while ($row = pg_fetch_object($rs)) {
			$retorno[] = $row;
		}

		return $retorno;
	}
	 


	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir($id){

		$sql = "UPDATE obrigacao_financeira_reajuste_historico
				   SET = NOW()
				 WHERE ofrhoid = " . intval( $id ) . "";

		if (!pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
		return true;
	}

	public function buscarClienteNome($parametros) {

		$retorno = array();

		if (trim($parametros->nome) === '') {
			echo json_encode($retorno);
			exit;
		}

		$sql = "SELECT clioid, clinome
				  FROM clientes
				 WHERE clidt_exclusao IS NULL
				   AND clinome ILIKE '" . pg_escape_string($parametros->nome) . "%'
     		  ORDER BY clinome
				 LIMIT 10";

		if ($rs = pg_query($this->conn, $sql)) {
			if (pg_num_rows($rs) > 0) {
				$i = 0;
				while ($objeto = pg_fetch_object($rs)) {
					$retorno[$i]['id'] = $objeto->clioid;
					$retorno[$i]['label'] = utf8_encode($objeto->clinome);
					$retorno[$i]['value'] = utf8_encode($objeto->clinome);
					$i++;
				}
			}
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
