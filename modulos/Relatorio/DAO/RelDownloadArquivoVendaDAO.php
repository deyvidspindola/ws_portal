<?php
/**
 *  Classe responsável em retornar dados de venda 
 * 
 * @file RelDownloadArquivoVendaDAO.php
 * 
 * @author Márcio Sampaio Ferreira
 * @version 16/05/2013 16:18:24
 * @since 16/05/2013 16:18:24
 * @package SASCAR RelDownloadArquivoVendaDAO.php 
 */


class RelDownloadArquivoVendaDAO{
	
	
	private $conn;
	
	// Construtor
	public function __construct($conn) {
	
		$this->conn = $conn;
	}
	
	/**
	 * Método responsável por executar a consulta no banco e retornar um array com os dados
	 * @author Márcio Sampaio Ferreira
	 * 
	 * @throws Exception
	 * @return array
	 */
	public function getDadosVendas($tipoRelatorio){

		try{

			$sql=" SELECT DISTINCT * FROM (
						SELECT  enduf uf,
							clino_cgc cnpj, 
							clinome razao_social, 
							rczdescricao gerente_contas,
							DATE(condt_cadastro) data_cadastro,
							nfldt_faturamento data_faturamento,
							titdt_pagamento data_pagamento,
							titdt_credito data_identificacao_pagamento,
					        veiplaca placa,
							osadata data_agendamento,
							DATE(condt_ini_vigencia) data_instalacao,
							concat(arano_ddd, linnumero) linha
						FROM contrato 
						JOIN clientes ON clioid = conclioid
						JOIN endereco ON endoid = cliendoid
						LEFT JOIN regiao_comercial_zona 
						     ON rczoid = conrczoid
						LEFT JOIN (nota_fiscal_item JOIN nota_fiscal ON nflno_numero =nfino_numero AND nflserie =nfiserie AND nfiserie = '1' JOIN titulo ON titnfloid = nfloid AND titno_parcela = 1) 
						     ON nficonoid = connumero
						LEFT JOIN veiculo 
						     ON veioid = conveioid AND veidt_exclusao IS NULL
						LEFT JOIN (ordem_servico JOIN ordem_servico_item ON ositordoid = ordoid JOIN os_tipo_item ON ositotioid = otioid JOIN os_tipo ON ostoid = otiostoid AND ostoid = 1) 
						     ON ordconnumero = connumero
						LEFT JOIN ordem_servico_agenda 
						     ON osaordoid = ordoid AND osaexclusao IS NULL
						LEFT JOIN equipamento 
						     ON equoid = conequoid
						LEFT JOIN linha 
						     ON linaraoid=equaraoid AND linnumero=equno_fone AND linexclusao IS NULL
						LEFT JOIN area 
						     ON linaraoid=araoid AND aradt_exclusao IS NULL
						WHERE contrato.conno_tipo = 844 
						AND condt_exclusao IS NULL
						AND conmodalidade = 'V' 
						AND concsioid=1 ";
			
						//busca dados do dia anterior até a data/limite para relatórios do tipo D-1
					    if($tipoRelatorio == 'd1'){
					    	//recupera dados do dia anterior a consulta
					    	$sql.=" AND condt_cadastro::timestamp::date < (now() - interval '1 DAY') ";
					    }
					    
					    //$sql .= " limit 1";
					    
				$sql.="	) AS a	";

			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('001');
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
				
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
}