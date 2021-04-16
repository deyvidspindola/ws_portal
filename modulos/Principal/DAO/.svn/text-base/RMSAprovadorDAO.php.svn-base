<?php

/**
 * Camada de persistência - RMS_Aprovador
*
* @author Dyorg Almeida <dyorg.almeida@meta.com.br>
* @since 17/01/2013
*/
class RMSAprovadorDAO {

	private $conn;

	public function __construct()
	{
		global $conn;
		$this->conn = $conn;
	}

	/**
	 * Listar aprovadores de requisição de material/serviço em pares <cd_usuario, nm_usuario>
	 *
	 * @param $cntoid - ID do centro de custo
	 * @return array
	 */
	public function listarAprovadoresPorCentroCusto($cntoid)
	{
		if (!is_numeric($cntoid)) throw new Exception("Código cntoid <$cntoid> deve ser numérico");
		
		$sql = "
		SELECT
			DISTINCT(cd_usuario) AS id, 
			funnome AS nome 
		FROM 
			funcionario
		INNER JOIN 
			rms_aprovador ON rmsafunoid = funoid
		INNER JOIN 
			rms_aprovador_item ON rmsaoid = rmsairmsaoid
		INNER JOIN
			usuarios ON usufunoid = funoid
		WHERE 
			rmsatipo = 'G'
		AND 
			rmsadt_exclusao IS NULL
		AND 
			rmsaidt_exclusao IS NULL
		AND
			funexclusao IS NULL
		AND
			usuarios.dt_exclusao IS NULL	
		AND 
			rmsaicntoid = $cntoid
		ORDER BY 
			funnome";
		
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar aprovadores');

		return pg_fetch_all($result);
	}

}