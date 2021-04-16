<?php

/**
 * Camada de persistência - Departamentos
 * 
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 08/01/2013
 */
class DepartamentoDAO {
	
	private $conn;
	
	public function __construct() 
	{
		global $conn;
		$this->conn = $conn;
	}
	
	/**
	 * Listar departamentos em pares <depoid, depdescricao>
	 *
	 * @param $deptecoid - ID da tabela tectran (ref. empresas sascar)
	 * @return array
	 */
	public function listarEmPares($deptecoid = null)
	{
		$sql = '
		SELECT
			depoid, depdescricao
	 	FROM
			departamento
		WHERE
			depexclusao IS NULL';
	
		if (!empty($deptecoid)) {
			if (!is_numeric($deptecoid)) throw new Exception("Código deptecoid <$deptecoid> deve ser numérico");
			$sql .= ' AND deptecoid = '. $deptecoid;
		}
	
		$sql .= '
		ORDER BY
			depdescricao';
	
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar departamentos');
	
		return pg_fetch_all($result);
	}
	
	/**
	 * Listar departamentos em pares <depoid, depdescricao>
	 * REGRA ELIMINADA EM 16/04/2013 --->>> Regra "depoid diferente de 13 e 51" extraída do arquivo doc_perfil_rh.php - ln 555 - rev 47519
	 * 
	 * @param $deptecoid - ID da tabela tectran (ref. empresas sascar)
	 * @return array
	 */
	public function listarDepartamentosPorEmpresaEmPares($deptecoid = null)
	{
		$sql = '
		SELECT
			depoid, depdescricao
	 	FROM
			departamento
		WHERE
			depexclusao IS NULL ';
		/*AND 
			depoid NOT IN (13, 51)';
		*/
		if (!empty($deptecoid)) {
			if (!is_numeric($deptecoid)) throw new Exception("Código deptecoid <$deptecoid> deve ser numérico");
			$sql .= ' AND deptecoid = '. $deptecoid; 
		}
		
		$sql .= '
		ORDER BY
			depdescricao';
	
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar departamentos');
	
		return pg_fetch_all($result);
	}
	
	/**
	 * Listar departamentos em pares restrito por permissão de usuário <depoid, depdescricao>
	 * REGRA ELIMINADA EM 16/04/2013 --->>>Regra de permissões extraída do arquivo migrar_cargo.php - ln 145 - rev 47519
	 *
	 * @param $deptecoid - ID da tabela tectran (ref. empresas sascar)
	 * @return array
	 */
	public function listarDepartamentosPorEmpresaComPermissaoEmPares($deptecoid = null)
	{
		$sql = '
		SELECT
			depoid, depdescricao
	 	FROM
			departamento';
		
		/*
		if ($_SESSION['usuario']['depoid'] != 34 && $_SESSION['usuario']['depoid'] != 206){
			$sql .= '
			INNER JOIN 
				permissao_departamento ON depoid = perddepoid 
			AND 
				perddt_exclusao IS NULL 
			AND 
				perdusuoid = '.$_SESSION['usuario']['oid'];
		}
		*/
		
		$sql .= '
		WHERE
			depexclusao IS NULL';
	
		if (!empty($deptecoid)) {
			if (!is_numeric($deptecoid)) throw new Exception("Código deptecoid <$deptecoid> deve ser numérico");
			$sql .= ' AND deptecoid = '. $deptecoid;
		}
	
		$sql .= '
		ORDER BY
			depdescricao';

		//echo $sql;
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar departamentos');
	
		return pg_fetch_all($result);
	}	
	
}