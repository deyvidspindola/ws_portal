<?php

/**
 * Camada de persistência - Empresas do grupo Sascar
 * 
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 26/12/2012
 */
class EmpresaSascarDAO {
	
	private $conn;
	
	public function EmpresaSascarDAO() 
	{
		global $conn;
		$this->conn = $conn;
	}
	
	/**
	 * Listar empresas do grupo Sascar em pares <tecoid, tecrazao>
	 * @since 23-01-2013
	 * @return array
	 */
	public function listarEmPares()
	{
		$sql = '
		SELECT
			tecoid, tecrazao
	 	FROM
			tectran
		WHERE
			tecexclusao IS NULL
		ORDER BY
			tecrazao';
	
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar empresas');
	
		return pg_fetch_all($result);
	}	
	
	/**
	 * Listar empresas do grupo Sascar em pares <tecoid, tecrazao>
	 * @return array 
	 * @deprecated (23-01-2013) usar o método listarEmPares();
	 */
	public function listarEmpresasEmPares() 
	{	
		$sql = '
		SELECT
			tecoid, tecrazao
	 	FROM
			tectran
		WHERE
			tecexclusao IS NULL
		ORDER BY
			tecrazao';
		
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar empresas');
		
		return pg_fetch_all($result);
	}

	/**
	 * Recuperar todos os dados de um determinado registro pelo ID <tecoid>
	 * @since 23-01-2013
	 * @return array
	 */
	public function obterPorId($tecoid) {
	
        // Não se usa mais o tecoid porque a busca é efetuada de acordo com o 
        // departamento do usuário logado. Solicitado por Murilo Pedroso em 25/04/2013.
        $cdUsuario = $_SESSION['usuario']['oid'];
        
		$sql = "
		SELECT
			*
		FROM
			tectran
		WHERE
			tecoid = (
                SELECT
                    deptecoid
                FROM
                    departamento
                INNER JOIN usuarios ON depoid = usudepoid
                WHERE
                    cd_usuario = {$cdUsuario}
            )
        ORDER BY
            tecrazao ASC";
            
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar empresas');
	
		return pg_fetch_all($result);
	}
	
	/**
	 * Listar empresas do grupo Sascar em pares <tecoid, tecrazao>
	 * @return array
	 * @deprecated (23-01-2013) usar o método obterPorId();
	 */
	public function obterEmpresa($tecoid) {
		
		$sql = "
		SELECT
			*
	 	FROM
			tectran
		WHERE
			tecoid = $tecoid";
		
		$result = pg_query($this->conn, $sql);
		if(!$result) throw new Exception('Falha ao consultar empresas');
		
		return pg_fetch_all($result);
	}
}