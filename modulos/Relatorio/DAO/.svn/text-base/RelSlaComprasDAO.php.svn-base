<?php

class RelSlaComprasDAO {

	/**
	 * Conn
	 * @var connection
	 */
	private $conn;
	
	/**
	 * Metodo Construtor
	 * @param $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}
	
	/**
	 * Buscar Cotações Por Período
	 * @param string $dataInicio
	 * @param string $dataFim
	 * @return array
	 */
	public function buscarCotacoesPorPeriodo($parametros) {
		$resultado = array();
		
        $sql = "SELECT  cotoid, 
                        reqmoid, 
                        to_char(reqmcadastro, 'DD/MM/YYYY') as reqmcadastro,  	
                        LAST_VALUE(nm_usuario) OVER(PARTITION BY cotoid) AS nm_usuario,
                        to_char(cotcadastro, 'DD/MM/YYYY') as cotcadastro, 
                        AVG(cotcadastro::date - max(rmapdt_aprovacao)::date) OVER(PARTITION BY cotoid) AS media_por_cotacao ,
                        cotstatus_compras,
                        to_char(max(rmapdt_aprovacao), 'DD/MM/YYYY') as rmapdt_aprovacao
				FROM 
						req_material
				INNER JOIN
						req_material_item ON reqmoid = rmireqmoid
				INNER JOIN
						req_material_aprovacao ON rmapreqmoid = reqmoid
				INNER JOIN
						usuarios ON cd_usuario = reqmusuoid
				LEFT JOIN
						cotacao_material ON cotoid = rmicotoid 
				WHERE
						cotstatus_compras = 'AA'
				AND 
						(
							cotcadastro BETWEEN '".$parametros->data_inicio." 00:00:00' AND '".$parametros->data_fim." 23:59:59'
						OR		
							reqmcadastro BETWEEN '".$parametros->data_inicio." 00:00:00' AND '".$parametros->data_fim." 23:59:59'
						OR
							rmapdt_aprovacao BETWEEN '".$parametros->data_inicio." 00:00:00' AND '".$parametros->data_fim." 23:59:59'
						)
				AND
						(
								((".$parametros->rms." = 0)AND(".$parametros->cotacao." = 0))
						OR 
								(cotoid = ".$parametros->cotacao.")
						OR
								(reqmoid = ".$parametros->rms.")
						)
                GROUP BY    cotoid, 
                            reqmoid,
                            nm_usuario,
                            cotcadastro,
                            cotstatus_compras
		
                ORDER BY    cotoid ASC, 
                            reqmoid ASC
					";
		
		if ($query = pg_query($this->conn, $sql)) {
			if (pg_num_rows($query) > 0) {
				while ($linha = pg_fetch_object($query)) {
					array_push($resultado, $linha);
				}
				return $resultado;
			}	
		} else {
			return $resultado;
		}		
	}
	
}