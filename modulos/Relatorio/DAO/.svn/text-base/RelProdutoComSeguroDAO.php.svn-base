<?php
/**
 * @file RelProdutoComSeguroDAO.php
 * @author marcioferreira
 * @version 11/12/2013 09:45:32
 * @since 11/12/2013 09:45:32
 * @package SASCAR RelProdutoComSeguroDAO.php 
 */


class RelProdutoComSeguroDAO{
	
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
	 * Retorma todos os status cadastrados 
	 * 
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getStatusApolice(){
		
		try{

			$sql = "SELECT pssoid       AS id_status, 
					       psscodigo    AS cod_status, 
					       pssdescricao AS nome_status
					  FROM produto_seguro_status
					  WHERE pssdt_exclusao IS NULL ";
		
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar status da apólice.');
			}
		
			if(pg_num_rows($result) > 0){
				return pg_fetch_all($result);
			}
		
			return false;
		
		}catch(Exception $e){
			return $e->getMessage();
		}
		
	}
	
	/**
	 * Recupera a quantidade de dias para estabelecer prazo de validade da proposta
	 *
	 * @param string $filtro_ambiente
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getDiasValidadeProposta($filtro_ambiente){
	
		try {
	
			if(empty($filtro_ambiente)){
				throw new Exception('Parâmetro para pesquisa de dias da validade da proposta não pode ser vazio');
			}
	
			$sql = "SELECT pcsidescricao
					  FROM parametros_configuracoes_sistemas_itens
			         WHERE pcsipcsoid = '$filtro_ambiente'
			           AND pcsioid = 'prazo_validade_proposta' ";
	
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception("Falha ao pesquisar quantidade de dias da validade da proposta");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}
	
	
	/**
	 * Retorna dados da pesquisa filtrados pelos parâmetros passados	 
	 * 	 
	 * Exibe todas as apólice ativadas e não ativadas  
	 *  
	 * @param objetct $dadosBusca
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function pesquisaDadosEnvio($dadosBusca, $dias_validade_proposta){
		
		try{
		
		    $sql = "   SELECT 'Apolice'                tipo,
						       psa.psaconnumero        num_contrato,
						       psa.psaordoid           numero_os,
						       c.coneqcoid             id_classe,
					           psa.psaoid              id_apolice,
					           psa.psaretapolicecd     numero_seguradora,  --número da proposta ou da apólice enviado pela seguradora
					           psc.pscenvcpfcnpj       CPF_CNPJ,
					           TO_CHAR(psa.psaenvdt_instalacao,'dd/mm/yyyy') dt_instalacao, 
						       TO_CHAR(psa.psaenvdt_ativacao,'dd/mm/yyyy')   dt_ativacao,
						       TO_CHAR(psa.psadt_cadastro,'dd/mm/yyyy')      dt_cadastro,
					           pss.pssdescricao        status,
					           psa.psaemboid           seguradora
					      FROM produto_seguro_apolice  psa
					INNER JOIN contrato                c   ON c.connumero   = psaconnumero
					INNER JOIN produto_seguro_cotacao  psc ON psa.psapscoid = psc.pscoid
					INNER JOIN produto_seguro_proposta psp ON psa.psapspoid = psp.pspoid
					INNER JOIN produto_seguro_status   pss ON psa.psapssoid = pss.pssoid
					     WHERE psa.psaoid = ( SELECT MAX(psaoid) 
					                            FROM produto_seguro_apolice 
					                      INNER JOIN produto_seguro_status ON psapssoid = pssoid 
					                            WHERE psaconnumero = psa.psaconnumero)  ";
			
			if($dadosBusca->data_ini != NULL && $dadosBusca->data_fim != null){
				$sql .= " AND psa.psadt_cadastro::DATE BETWEEN '$dadosBusca->data_ini' AND  '$dadosBusca->data_fim' ";
			}
				
			if($dadosBusca->cpf_cnpj != NULL){
				$sql .= " AND psc.pscenvcpfcnpj = '".trim($dadosBusca->cpf_cnpj)."' ";
			}
				
			if($dadosBusca->num_contrato != NULL){
				$sql .= " AND psa.psaconnumero = ".trim($dadosBusca->num_contrato)." ";
			}
				
			if($dadosBusca->id_status != NULL){
				$sql .= " AND psa.psapssoid = ".trim($dadosBusca->id_status)." ";
			}
				
			if($dadosBusca->vei_placa != NULL){
				$sql .= " AND psp.pspenvveiplaca = '".trim($dadosBusca->vei_placa)."' ";
			}
			
			$sql .= " UNION ALL ";
			
			$sql .= " SELECT  'Proposta'                tipo,
					           connumero                num_contrato,
					           os.ordoid                numero_os,
					           c.coneqcoid              id_classe,
					           psa.psaoid               id_apolice,
					           psp.pspretproposta || '' numero_seguradora, --número da proposta ou da apólice enviado pela seguradora
					           psc.pscenvcpfcnpj        CPF_CNPJ,
							   TO_CHAR(c.condt_ini_vigencia,'dd/mm/yyyy') dt_instalacao, 
						       TO_CHAR(c.condt_ini_vigencia,'dd/mm/yyyy')   dt_ativacao,
						       TO_CHAR(psp.pspdt_cadastro,'dd/mm/yyyy')      dt_cadastro,	
					           'Sem Apólice'            status,
					           psp.pspemboid            seguradora
					      FROM produto_seguro_proposta psp 
					INNER JOIN contrato c                       ON c.connumero = psp.pspconnumero
					INNER JOIN produto_seguro_cotacao  psc      ON psp.psppscoid = psc.pscoid
					INNER JOIN ordem_servico os                 ON os.ordconnumero = c.connumero
					INNER JOIN ordem_servico_item osi           ON osi.ositordoid = os.ordoid
					INNER JOIN os_tipo_item oti                 ON oti.otioid = osi.ositotioid
					INNER JOIN os_tipo ot                       ON ot.ostoid = oti.otiostoid
					INNER JOIN equipamento_classe_beneficio ecb ON os.ordeqcoid = ecb.eqcbeqcoid
					INNER JOIN empresa_beneficio_tipo ebt       ON ecb.eqcbebtoid = ebt.ebtoid
					INNER JOIN empresa_beneficio eb             ON eb.emboid = ebt.ebtemboid
					 LEFT JOIN produto_seguro_apolice psa       ON psa.psapspoid = psp.pspoid
					     WHERE psp.pspretcodigo = 0 
					       AND psp.pspoid = ( SELECT MAX(pspoid) 
					                            FROM produto_seguro_proposta 
					                           WHERE pspconnumero = psp.pspconnumero )  ";
					       					
			if($dadosBusca->data_ini != NULL && $dadosBusca->data_fim != null){
				$sql .= " AND psp.pspdt_cadastro::DATE BETWEEN '$dadosBusca->data_ini' AND  '$dadosBusca->data_fim' ";
			}
			
			if($dadosBusca->cpf_cnpj != NULL){
				$sql .= " AND psc.pscenvcpfcnpj = '".trim($dadosBusca->cpf_cnpj)."' ";
			}
			
			if($dadosBusca->num_contrato != NULL){
				$sql .= " AND psp.pspconnumero = ".trim($dadosBusca->num_contrato)." ";
			}
			
			if($dadosBusca->vei_placa != NULL){
				$sql .= " AND psp.pspenvveiplaca = '".trim($dadosBusca->vei_placa)."' ";
			}
			
		   $sql .=" AND (psp.pspretproposta IS NOT NULL AND psp.pspretproposta <> 0) 
         		    AND (current_date::DATE - psp.pspdt_cadastro::DATE) < $dias_validade_proposta 
		   			AND ot.ostoid = 1        -- OS do tipo INSTALACAO
			        AND oti.otioid = 3       -- Tipo de OS instalacao de equipamento
			        AND os.ordstatus = 3     -- OS concluída
			        AND osi.ositstatus = 'C' -- item da OS Concluída
			        AND ebt.ebtdescricao = 'SEGURO'
			        AND eb.embdt_exclusao IS NULL
			        AND eb.embdt_exclusao IS NULL
			        AND ecb.eqcbdt_exclusao IS NULL
			        AND psa.psaoid IS NULL   --não tem apólice
			        AND os.ordequoid is not null -- equipamento vinculado a OS 
		            ORDER  BY dt_cadastro ASC ";

			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados do seguro');
			}
		
			if(pg_num_rows($result) > 0){
				return pg_fetch_all($result);
			}
		
			return false;
		
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Retorna todos os erros gerados do id informado da apólice 
	 * 
	 * @param int $id_apolice
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function detalharDadosErro($id_apolice){
		
		try {
			
			$sql = "SELECT TO_CHAR(psadt_cadastro,'dd/mm/yyyy') AS dt_cad, 
				           psmretdescricaosascar AS descricao_interna, 
				           psmretdescricaoseguradora AS descricao_para_cliente, 
				           psapinfoadicionais AS info_adicionais,
				           psaxmlenvio AS xml_envio,
				           psaxmlretorno AS xml_retorno
				      FROM produto_seguro_apolice_processo
				INNER JOIN produto_seguro_apolice ON psappsaoid = psaoid
				INNER JOIN produto_seguro_mensagens ON psappsmoid = psmoid
				     WHERE psappsaoid = $id_apolice
				       AND psapdt_exclusao IS NULL ";
			
			
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar detalhes dos dados');
			}
			
			if(pg_num_rows($result) > 0){
				return pg_fetch_all($result);
			}
			
			return false;
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
	}
	
	
	/**
	 * Retorna dados de uma apólice ativada com sucesso
	 *
	 * @param int $id_apolice
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function detalharDadosSucesso($id_apolice){

		try {

			$sql = " SELECT TO_CHAR(psadt_cadastro,'DD/MM/YYYY HH24:MI') AS dt_cad
						           ,psaxmlenvio AS xml_envio
						           ,psaxmlretorno AS xml_retorno
						           ,psaorigemchamada AS origem_chamada
						           ,psaorigemsistema AS origem_sistema
						      FROM produto_seguro_apolice 
						INNER JOIN produto_seguro_status ON psapssoid = pssoid
						     WHERE psaoid = $id_apolice 
						       AND psscodigo in (0,10,11,1) ";

			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar detalhes dos dados');
			}

			if(pg_num_rows($result) > 0){
				return pg_fetch_all($result);
			}
				
			return false;
				
		} catch (Exception $e) {
			return $e->getMessage();
		}

	}
	
	
	
	/**
	 * Recupera dados da apólice a partir do numero de contrato informado para reenviar apólice
	 * 
	 * @param int $num_contrato
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getDadosEnvioApolice($num_contrato){
		
		try {
				
			$sql = " SELECT coneqcoid AS id_classe,
					        TO_CHAR(condt_ini_vigencia,'dd-mm-yyyy') AS dt_instalacao,
					        TO_CHAR(condt_instalacao,'dd-mm-yyyy') AS dt_ativacao
					   FROM contrato
			     INNER JOIN equipamento_classe_beneficio ON coneqcoid = eqcbeqcoid
				 INNER JOIN empresa_beneficio_tipo ON eqcbebtoid = ebtoid
				 INNER JOIN empresa_beneficio ON emboid = ebtemboid
				      WHERE connumero = $num_contrato
					    AND ebtdescricao = 'SEGURO'
					    AND embdt_exclusao IS NULL
					    AND embdt_exclusao IS NULL
					    AND eqcbdt_exclusao IS NULL	";
				
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao retornar dados de reenvio da apólice');
			}
				
			if(pg_num_rows($result) > 0){
				return pg_fetch_all($result);
			}

			return false;

		} catch (Exception $e) {
			return $e->getMessage();
		}

	}
	
	
}