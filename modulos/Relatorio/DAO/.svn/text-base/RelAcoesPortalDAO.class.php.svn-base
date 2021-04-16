<?
/** Depêndencia de biblioteca externa: string de conexao com o banco */
include 'lib/config.php';

/**
 * RelAcoesPortalDAO.class.php
 *
 * Classe desenvolvida para acesso ao banco de dados
 *
 * @author Renato Teixeira Bueno
 * @copyright Copyright (c) 2012
 * @version 1.0
 * @package rel_acoes_portal
 * @date 28/06/2012T18:00:00
 */

class RelAcoesPortalDAO
{
	private $conn;
	
	public function RelAcoesPortalDAO(){
			
            global $conn;
            $this->conn = $conn;
            
	}
	
	public function getRegiaoDAO(){
		
            $sql = "SELECT 
                        estoid,estuf
                    FROM 
                        estado 
                    WHERE 
                        estexclusao IS NULL 
                    ORDER BY 
                        estuf";

            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0){
                    return $rs;
            }

            return null;
	}
	
	public function getGrupoMenuDAO(){
		
            $sql = "SELECT 
                            mpaoid,
                            mpadescricao
                    FROM
                            menus_portal_atendimento 
                    ORDER BY
                            mpadescricao ASC";
			
            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0){
                    return $rs;
            }

            return null;
			
	}
	
	public function getTipoContratoDAO(){
		
    	$sql = "-- tipo_contrato = Cliente
                    SELECT 
						tpcoid, tpcdescricao
                    FROM 
                        tipo_contrato 
                    WHERE 
                        tpcseguradora = false 

                    UNION

                 -- tipo_contrato = Seguradora
                    SELECT 
                         tpcoid, tpcdescricao
                    FROM 
                         tipo_contrato 
                    WHERE 
                         tpcseguradora = true 
                    AND 
                         tpcdescricao NOT ILIKE 'Ex-%' 

                    UNION

                 -- tipo_contrato = Ex-Seguradora
                    SELECT 
                         tpcoid, tpcdescricao
                    FROM 
                         tipo_contrato 
                    WHERE
                         tpcseguradora = true 
                    AND 
                        tpcdescricao ILIKE 'Ex-%' 

                    ORDER BY tpcdescricao ASC";
    	
            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0){
                    return $rs;
            }

            return null;
	}
	
	public function getCidadeDAO($regiao){
		
            $sql = "SELECT
                        cidoid,
                        ciddescricao 
                	FROM 
                        cidade 
                	WHERE 
                        cidexclusao IS NULL 
                	AND 
                        cidestoid=$regiao";
	     
            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0){
                    return $rs;
            }

            return null;
            }
	
	public function getItemMenuDAO($grupo_menu){
	
            $sql = "SELECT
                        impaoid,
                        impadescricao
                	FROM
                        itens_menus_portal_atendimento
                	WHERE
                        impadt_exclusao IS NULL
                	AND
                        impampaoid = $grupo_menu";
			
            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0){
                    return $rs;
            }

            return null;
         
	}
	
	public function getClienteDAO($nome){	    
	    
	    $sql = "SELECT 
	                clioid AS cliente_id, 
	                clinome AS cliente_nome 
	            FROM 
	                clientes
	            WHERE 
	                clinome ilike '%$nome%' 
	                AND clidt_exclusao IS NULL
	            ORDER BY 
	                clinome";
	    
	    
	    $rs = pg_query($this->conn,$sql);
	    
	    if(pg_num_rows($rs) > 0)
        {
        	return $rs;
        }
		
		return null;
	}
	
	/**
	 * Metodo que efetua a pesquisa do relatorio 
	 * para o tipo de relatorio sintetico
	 */
	public function pesquisaRelSintetico($filtro, $pesq_data_ini, $pesq_data_fim){
				
		/**
		 Alterações na busca de registros.
		 Mantis 3447
		
		 Em periodos com muitos registro estava gerando 'time-out' e não retornava resultado.
		
		 Alteração para que o sistema realize pequenas consultas dentro do periodo selecionado.
		
		 Leandro Ivanaga
		 leandroivanaga@brq.com
		 */
		
		$sql = "DROP TABLE IF EXISTS tmp_historico_acoes";
		$rs = pg_query($this->conn,$sql);
		
		$tabela_temporaria = " INTO TEMPORARY tmp_historico_acoes ";
		$insert_tabela = "";
		
		// Formata as datas
		$data_ini = array_reverse(explode("/", $pesq_data_ini));
		$data_fim = array_reverse(explode("/", $pesq_data_fim));
		
		$pesq_data_ini = implode("-", $data_ini);
		$pesq_data_fim = implode("-", $data_fim);
		
		$data_ini = $pesq_data_ini;
		
		$where_data = "";
		
		while ($data_ini <= $pesq_data_fim) {
				
			// Pega o intervalo das datas
			$data_fim = date("Y-m-d", (strtotime("$data_ini +2day")));
			if ($data_fim > $pesq_data_fim)
				$data_fim = $pesq_data_fim;
				
			$where_data = " hapadt_insercao BETWEEN '$data_ini 00:00:00' AND '$data_fim 23:59:59' ";
			$sql = "
					$insert_tabela
					SELECT DISTINCT
				tpcdescricao as tipo_contrato,
				enduf as regiao,
				endcidade as cidade,
				clinome as cliente,
				clioid as cliente_id,
				hapadt_insercao as data_hora,
				mpadescricao as grupo_menu, 
				impadescricao as item_acesso, 	
				impaoid as item_acesso_id,
				tpcoid as tipo_contrato_id
				$tabela_temporaria
			FROM
				historico_acoes_portal_atendimento
			INNER JOIN 
				itens_menus_portal_atendimento ON hapaimpaoid=impaoid
			INNER JOIN
				menus_portal_atendimento ON impampaoid=mpaoid
			INNER JOIN 
				clientes ON clioid = hapaclioid
			INNER JOIN
				contrato ON clioid=conclioid and conno_tipo = 0
			INNER JOIN
				tipo_contrato ON conno_tipo=tpcoid and tpcoid = 0
			INNER JOIN 
				endereco ON cliendoid=endoid
			WHERE
				hapatipo = 'C' 
			AND
					$where_data	
					$filtro
				";
		
			$insert_tabela = "INSERT INTO tmp_historico_acoes
						(tipo_contrato, regiao, cidade, cliente, cliente_id, data_hora, grupo_menu, item_acesso, item_acesso_id, tipo_contrato_id)
						";
			
			$tabela_temporaria = "";
			
			$data_ini = date("Y-m-d", (strtotime("$data_ini +3day")));
			
		$rs = pg_query($this->conn,$sql);
		}
		
		$sql =	"select 
					 tipo_contrato,
					 regiao,
					 cidade,
					 cliente,
					 cliente_id,
					 data_hora,
					 grupo_menu, 
					 item_acesso, 	
					 item_acesso_id,
					 tipo_contrato_id			
				from tmp_historico_acoes
				order by data_hora";
		
		$rs = pg_query($this->conn,$sql);
		
		
		$historico = array();
		
		if(pg_num_rows($rs) > 0)
		{
			return $rs;
		}
		
		return null;
	}
	
	/**
	 * Metodo que efetua a pesquisa para o relatorio do tipo analitico
	 */
	public function pesquisaRelAnalitico($filtro, $pesq_data_ini, $pesq_data_fim){
				
		/**
		Alterações na busca de registros.
		Mantis 3447
		
		Em periodos com muitos registro estava gerando 'time-out' e não retornava resultado.
		
		Alteração para que o sistema realize pequenas consultas dentro do periodo selecionado.
		
		Leandro Ivanaga
		leandroivanaga@brq.com
		 */

		$sql = "DROP TABLE IF EXISTS tmp_historico_acoes";
		$rs = pg_query($this->conn,$sql);

		$tabela_temporaria = " INTO TEMPORARY tmp_historico_acoes ";
		$insert_tabela = "";
		
		// Formata as datas
		$data_ini = array_reverse(explode("/", $pesq_data_ini));
		$data_fim = array_reverse(explode("/", $pesq_data_fim));
		
		$pesq_data_ini = implode("-", $data_ini);
		$pesq_data_fim = implode("-", $data_fim);
		
		$data_ini = $pesq_data_ini;
		
		$where_data = "";
		
		while ($data_ini <= $pesq_data_fim) {
			
			// Pega o intervalo das datas
			$data_fim = date("Y-m-d", (strtotime("$data_ini +2day")));
			if ($data_fim > $pesq_data_fim)
				$data_fim = $pesq_data_fim;
			
			$where_data = " hapadt_insercao BETWEEN '$data_ini 00:00:00' AND '$data_fim 23:59:59' ";
			
			$sql = "
					$insert_tabela
					SELECT DISTINCT
					tpcdescricao as tipo_contrato,
					enduf as regiao,
					endcidade as cidade,
					clinome as cliente,
					clioid as cliente_id,
					hapadt_insercao as data_hora,
					mpadescricao as grupo_menu, 
					impadescricao as item_acesso, 	
					impaoid as item_acesso_id,
					0 as tipo_contrato_id,
					hapaacao as acao
					$tabela_temporaria
				FROM
					historico_acoes_portal_atendimento
				INNER JOIN 
					itens_menus_portal_atendimento ON hapaimpaoid=impaoid
				INNER JOIN
					menus_portal_atendimento ON impampaoid=mpaoid
				INNER JOIN 
					clientes ON clioid = hapaclioid
				INNER JOIN
					contrato ON clioid=conclioid AND conno_tipo=0
				INNER JOIN
					tipo_contrato ON conno_tipo=tpcoid
				INNER JOIN 
					endereco ON cliendoid=endoid
				WHERE
					hapatipo = 'C' and tpcoid = 0
					
					AND $where_data	
					$filtro
			";
		
			$insert_tabela = "INSERT INTO tmp_historico_acoes
						(tipo_contrato, regiao, cidade, cliente, cliente_id, data_hora, grupo_menu, item_acesso, item_acesso_id, tipo_contrato_id,acao)
						";
			
			$tabela_temporaria = "";
			
			$data_ini = date("Y-m-d", (strtotime("$data_ini +3day")));
			
			$rs = pg_query($this->conn,$sql);
		}
		
		$sql =
		   "select 	acao, 
				tipo_contrato, 
				cidade, 
				regiao, 
				cliente, 
				cliente_id, 
				data_hora, 
				grupo_menu, 
				item_acesso, 
				item_acesso_id,
				tipo_contrato_id
			from tmp_historico_acoes 
			order by data_hora";
				
		$rs = pg_query($this->conn,$sql);
		
		$historico = array();
		
		if(pg_num_rows($rs) > 0)
		{
			return $rs;
		}
		
		return null;
	}
	
	/**
	 * Metodo para Inserir histórico de ações no portal de atendimento
	 * @param  array
	 * @return resource QUery
	 */
	public function Insere_HistoricoAcoesPortal($dados){

		$sql = "SELECT historico_acoes_portal_atendimento_i
					(".$dados['hapaimpaoid'].",
					 ".$dados['hapausuario_acao'].", 
					 NOW()::timestamp, 
					 '".$dados['hapaacao']."'::text, 
					 '".$dados['hapatipo']."'::text, 
					 ".$dados['hapaclioid'].")";
		
		$result = pg_query($this->conn, $sql);
		
		return $result;
	}
	
}