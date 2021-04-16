<?php
/**
 * @file RelBoletagemMassiva.php
 * @author marcio.ferreira
 * @version 01/09/2015 10:15:07
 * @since 01/09/2015 10:15:07
 * @package SASCAR RelBoletagemMassiva.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/rel_boletagem_massiva_'.date('d-m-Y').'.txt');


class RelBoletagemMassivaDAO{

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $conn;

	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn){

		$this->conn = $conn;

	}

	
	/**
	 * 
	 * @param Object $dados
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getTitulosUnificados($pesquisa){
		
		
		if(!is_object($pesquisa)){
			throw new Exception('Dados inválidos para recuperar titulos unificados');
		}
		
		$sql = " SELECT abonm_campanha AS nome_campanha,clinome
						,poddescricao_atraso AS aging_divida
						,aboprc_desconto AS desconto
						,CASE 
						   WHEN clitipo = 'J' THEN 'JURÍDICA'
						   WHEN clitipo = 'F' THEN 'FÍSICA'    
						  ELSE ''
						   END AS tipo_pessoa
						,CASE 
						   WHEN (SELECT count(conno_tipo) FROM contrato WHERE conclioid = titcclioid AND conno_tipo = 905) > 0 THEN 'SIGGO'
						  ELSE
						   'SASCAR'
						  END AS tipo_cliente	
						,CASE 
						   WHEN clitipo = 'J' THEN 
						        (SELECT cufsigla FROM codigo_uf WHERE cufsigla = cliuf_com)
						   ELSE 
						        (SELECT cufsigla FROM codigo_uf WHERE cufsigla = cliuf_res)
						   END AS uf
						,titcvl_recalculado AS valor_divida
						,TO_CHAR(titcdt_vencimento, 'DD/MM/YYYY') AS data_vencimento
						,TO_CHAR(titcdt_pagamento, 'DD/MM/YYYY') AS data_pagamento
						,CASE
						   WHEN aboformato_envio = 'E' THEN 'E-mail'
						   WHEN aboformato_envio = 'G' THEN 'Arquivo Gráfica'
						 END AS forma_envio    
						
				    FROM arquivo_boletagem 
			  INNER JOIN politica_desconto ON podoid = abopodoid
			  INNER JOIN titulo_consolidado ON titcabooid = abooid 
			  INNER JOIN clientes ON clioid = titcclioid
				   WHERE abodt_cadastro::DATE BETWEEN '$pesquisa->data_ini' AND '$pesquisa->data_fim' ";
		
		if(!empty($pesquisa->data_vencimento)){
			$sql .= "AND abodt_vencimento::DATE = '$pesquisa->data_vencimento'";
		}
					   
		if(!empty($pesquisa->nome_campanha)){
			$sql .= "AND abonm_campanha ilike '%$pesquisa->nome_campanha%' ";
		}
		  
		if($pesquisa->apenas_pagos == 1){
			$sql .= "AND titcdt_pagamento IS NOT NULL ";
		}	     
		
		
		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao pesquisar titulos unificados");
		}
		
		if (pg_num_rows($result) > 0) {
			return pg_fetch_all($result);
		}
		
		return false;
		
	}


}
?>