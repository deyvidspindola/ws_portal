<?php

/**
 * Acesso a dados para o módulo Relatório de envio de emails em lote
 */
class RelEnvioEmailsEmLoteDAO {
    
    /**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;
	
	
	/**
	 * Construtor, recebe a conexão com o banco
	 * @param resource $connection
	 * @throws Exception
	 */
	public function __construct($connection) {
		
		if (!$connection) {
			throw new Exception("Link de conexão com o banco não informada.");
		}
		
		$this->conn = $connection;
	}
    
    /**
	 * Gera a massa do relatório
	 * 
	 * @param array $filtros
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getRelatorio($filtros) {
		
		$filtro 			= "";
		$verSucessosEnvio	= null;		
		
		// Valida período
		if (empty($filtros['data_inicial'])) {
			throw new Exception("Data inicial não informada.");
		}
		
		if (empty($filtros['data_final'])) {
			throw new Exception("Data final não informada.");
		}
		
		// Monta filtro
		if (!empty($filtros['placa'])) {
			$filtro = " AND veiplaca = '".$filtros['placa']."' ";
		}
	
		if (strlen($filtros['sucesso_envio']) != 0) {
			
			if ($filtros['sucesso_envio'] == '1') {
				$verSucessosEnvio = 'TRUE';
			}
			else {
				$verSucessosEnvio = 'FALSE';
			}
			
			$filtro .= " AND leeosucesso_envio = $verSucessosEnvio";
		}
		
		if (!empty($filtros['chassi'])) {
			$filtro .= " AND veichassi = '".$filtros['chassi']."'  ";
		}
		
		if (!empty($filtros['nome_cliente'])) {
			$filtro .= " AND clinome ILIKE '".$filtros['nome_cliente']."%'  ";
		}
		
		$sql = "
		SELECT 
            ococonnumero AS contrato,
            TO_CHAR(leeodt_cadastro, 'DD/MM/YYYY') AS data_notificacao,
            clinome AS cliente,
            CASE 
                WHEN clitipo = 'F' THEN
                    clino_cpf
                ELSE
                    clino_cgc
            END AS cpf_cnpj,
            CASE 
                WHEN clitipo = 'F' THEN
                    cliuf_res
                ELSE
                    cliuf_com
            END AS uf,
            CASE 
                WHEN clitipo = 'F' THEN
                    clicidade_res
                ELSE
                    clicidade_com
            END AS cidade,
            veiplaca AS placa,
            veichassi AS chassi,
            mlomodelo AS modelo,
            CASE
                WHEN leeosucesso_envio = TRUE THEN
                    'Sim'
                ELSE
                    'Não'
            END AS sucesso_envio
        FROM
            ocorrencia
        LEFT JOIN
            log_envio_email_ocorrencia ON leeoocooid = ocooid
        INNER JOIN
            veiculo ON veioid = ocoveioid
        INNER JOIN
            clientes ON clioid = ococlioid
        LEFT JOIN
            modelo ON mlooid = veimlooid
        WHERE            
            leeodt_cadastro::date BETWEEN '{$filtros['data_inicial']}'::date AND '{$filtros['data_final']}'::date               
			$filtro
		";
        
		/*echo "<pre>";
		var_dump($sql);
		echo "</pre>";*/
        
        ob_start();        
		$result = pg_query($this->conn, $sql);
        ob_end_clean();
        
		if(!$result) {
			throw new Exception("Houve um erro ao realizar a pesquisa.");
		}
		
		if (pg_num_rows($result) > 0) {		
			return $result;
		}
		else {
			return null;
		}
	}
    
}