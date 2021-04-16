<?php
/** Depêndencia de biblioteca externa: string de conexao com o banco */
//require _SITEDIR_ . 'lib/config.php';

/*
 * RelAgingEquipamentosDAO.class.php
 *
 * Classe desenvolvida para acesso ao banco de dados
 *
 * @author Rafael Dias
 * @copyright Copyright (c) 2012
 * @version 1.0
 * @package rel_aging_equipamentos
 * @date 31/08/2012T11:00:00
 */

class RelAgingEquipamentosDAO 
{
	private $conn;
	
	public function RelAgingEquipamentosDAO(){
	
		global $conn;
		$this->conn = $conn;
	}
	
	public function getResultsEquipamentos($agingDataInicial,$agingDataFinal,$tipo_visao,$representantes,$uf,$implodeEquipamentos,$relatorio) {	
		
		/*
		 * recuperacao - TEMPO RECUPERAÇÃO DE EQUIPAMENTOS - (Data Disponível ou Data Sucata - Data Retirada)
		 * laboratorio_total - TEMPO LABORATÓRIO (TOTAL) - (Data Disponível ou Data Sucata - Data Retorno)
		 * transito - TEMPO TRANSITO - (Data Retorno - Data Retirada)
		 * laboratorio_externo - TEMPO LABORATÓRIO EXTERNO - (Data Disponível ou Data Sucata - Data Manutenção Fornecedor)
		*/
		
		if ($tipo_visao==1) {
			$filtro1 = "hieeqsoid in (3,18,10)";
			$filtro2 = "(status = 3 OR status = 18) AND proximo_status = 10";
		}
		if ($tipo_visao==2) {
			$filtro1 = "hieeqsoid in (3,18,19)";
			$filtro2 = "(status = 3 OR status = 18) AND proximo_status = 19";
		}
		if ($tipo_visao==3) {
			$filtro1 = "hieeqsoid in (19,10)";
			$filtro2 = "status = 19 AND proximo_status = 10";
		}
		if ($tipo_visao==4) {
			$filtro1 = "hieeqsoid in (3,18,31)";
			$filtro2 = "(status = 3 OR status = 18) AND proximo_status = 31";
		}
		//Where
		if ($uf!="'todos'") {
			$where = "AND endvuf IN ($uf)";
		} else {
			$where = "";
		}
		
		if ($relatorio=='sintetico') {
			
			
			$sqlSinteticoEquipamento = "
			      SELECT
			        count(*) as total,
					eprnome as modelo,
					round(avg((dt_disponivel::date - dt_retirada_proximo::date))) AS tempo,
					substring(dt_disponivel,4,8) as disponivel
				FROM (
					SELECT
						hieeqsoid as status,
						lead(hieeqsoid) OVER (window_retirada) As proximo_status,
						to_char(hiedt_historico, 'DD/MM/YYYY') dt_disponivel,
						lead(to_char(hiedt_historico, 'DD/MM/YYYY'))  OVER (window_retirada) As dt_retirada_proximo,
						hieequoid
					FROM (SELECT hieequoid, hiedt_historico, hieeqsoid
					      FROM historico_equipamento 
					      WHERE $filtro1 
					      ORDER BY hieequoid,hiedt_historico DESC) as n 
					WINDOW window_retirada AS (PARTITION BY n.hieequoid ORDER BY n.hiedt_historico DESC)
					ORDER BY n.hiedt_historico DESC
				) as aux1
				INNER JOIN equipamento on (hieequoid = equoid)
				INNER JOIN relacionamento_representante on (equrelroid = relroid)
				INNER JOIN representante on (relrrepoid = repoid)
				INNER JOIN endereco_representante on (endvrepoid = relrrep_terceirooid)
				INNER JOIN equipamento_versao on (equeveoid = eveoid)
				INNER JOIN equipamento_projeto on (eproid = eveprojeto)     
				WHERE $filtro2
				  AND repoid IN ($representantes)
				  AND eproid IN ($implodeEquipamentos)
				  $where
				  AND to_date(aux1.dt_disponivel, 'DD/MM/YYYY') BETWEEN to_date('$agingDataInicial', 'DD/MM/YYYY') AND to_date('$agingDataFinal', 'DD/MM/YYYY')
				GROUP BY eprnome, substring(dt_disponivel,4,8)
				ORDER BY disponivel;";
			return $rsSinteticoEquipamento = pg_query($this->conn, $sqlSinteticoEquipamento);
		}
		
		if ($relatorio=='analitico') {
			
			$sqlAnaliticoEquipamento = "
				SELECT
					eprnome as modelo,
					representante_instalador.repnome as nome_representante,	
					endvuf as uf,
					equno_serie as serial,
					eveversao as versao,
					(dt_disponivel::date - dt_retirada_proximo::date) AS tempo,
					dt_disponivel AS disponivel,
					dt_retirada_proximo AS retirada
					
				FROM (
					SELECT
						hieeqsoid as status,
						lead(hieeqsoid) OVER (window_retirada) As proximo_status,
						LEAD(hieitloid) OVER window_retirada AS instalador_retirada, --ID do instalador que fez a retirada
						to_char(hiedt_historico, 'DD/MM/YYYY') dt_disponivel,
						lead(to_char(hiedt_historico, 'DD/MM/YYYY'))  OVER (window_retirada) As dt_retirada_proximo,
						hieequoid
					FROM (SELECT hieequoid, hiedt_historico, hieeqsoid, hieitloid, hierelroid
					      FROM historico_equipamento  
					      WHERE $filtro1  
					      ORDER BY hieequoid,hiedt_historico DESC) as n 
					WINDOW window_retirada AS (PARTITION BY n.hieequoid ORDER BY n.hiedt_historico DESC)
					ORDER BY n.hiedt_historico DESC
				) as aux1
				INNER JOIN equipamento on (hieequoid = equoid)
				INNER JOIN relacionamento_representante on (equrelroid = relroid)
				
				LEFT JOIN instalador AS hist_instalador ON instalador_retirada = itloid
				LEFT JOIN representante AS representante_instalador ON (itlrepoid = representante_instalador.repoid)
				
				INNER JOIN representante ON (relrrepoid = representante.repoid)
				INNER JOIN endereco_representante on (endvrepoid = relrrep_terceirooid)
				INNER JOIN equipamento_versao on (equeveoid = eveoid)
				INNER JOIN equipamento_projeto on (eproid = eveprojeto)     
				
				WHERE $filtro2
				  AND representante.repoid IN ($representantes)
				  AND eproid IN ($implodeEquipamentos)
				  $where
				  AND to_date(aux1.dt_disponivel, 'DD/MM/YYYY') BETWEEN to_date('$agingDataInicial', 'DD/MM/YYYY') AND to_date('$agingDataFinal', 'DD/MM/YYYY')
				ORDER BY
					eprnome,
					dt_disponivel";

					return $rsAnaliticoEquipamento = pg_query($this->conn, $sqlAnaliticoEquipamento);
		}
		
	}
	
	public function getResultsAntenas($agingDataInicial,$agingDataFinal,$tipo_visao,$representantes,$uf,$implodeAntenas,$relatorio) {
		
		/*
		 * recuperacao - TEMPO RECUPERAÇÃO DE EQUIPAMENTOS - (Data Disponível ou Data Sucata - Data Retirada)
		* laboratorio_total - TEMPO LABORATÓRIO (TOTAL) - (Data Disponível ou Data Sucata - Data Retorno)
		* transito - TEMPO TRANSITO - (Data Retorno - Data Retirada)
		* laboratorio_externo - TEMPO LABORATÓRIO EXTERNO - (Data Disponível ou Data Sucata - Data Manutenção Fornecedor)
		*/
		
		if ($tipo_visao==1) {
			$filtro1 = "ashassoid in (3,12,8)";
			$filtro2 = "(status = 3 OR status = 12) AND proximo_status = 8";
		}
		if ($tipo_visao==2) {
			$filtro1 = "ashassoid in (3,12,51)";
			$filtro2 = "(status = 3 OR status = 12) AND proximo_status = 51";
		}
		if ($tipo_visao==3) {
			$filtro1 = "ashassoid in (51,8)";
			$filtro2 = "status = 51 AND proximo_status = 8";
		}
		if ($tipo_visao==4) {
			$filtro1 = "ashassoid in (3,12,10)";
			$filtro2 = "(status = 3 OR status = 12) AND proximo_status = 10";
		}
		
		//Where
		if ($uf!="'todos'") {
			$where = "AND endvuf IN ($uf)";
		} else {
			$where = "";
		}
		
		if ($relatorio=='sintetico') {
			
			$sqlSinteticoAntena = "
			SELECT
				count(*) as total,
				asmdescricao as modelo,
				round(avg((dt_disponivel::date - dt_retirada_proximo::date))) AS tempo,	
        		substring(dt_disponivel,4,8) as disponivel
			FROM (
				SELECT
					ashassoid as status,
					lead(ashassoid) OVER (window_retirada) As proximo_status,
					to_char(ashdt_historico, 'DD/MM/YYYY') dt_disponivel,
					lead(to_char(ashdt_historico, 'DD/MM/YYYY'))  OVER (window_retirada) As dt_retirada_proximo,
					ashasatoid
				FROM (SELECT ashasatoid, ashdt_historico, ashassoid
						FROM antena_satelital_historico
						WHERE $filtro1
						ORDER BY ashasatoid,ashdt_historico DESC) as n
				WINDOW window_retirada AS (PARTITION BY n.ashasatoid ORDER BY n.ashdt_historico DESC)
				ORDER BY n.ashdt_historico DESC
			) as aux1
			INNER JOIN antena_satelital on (ashasatoid = asatoid)
			INNER JOIN relacionamento_representante on (asatrelroid = relroid)
			INNER JOIN representante on (relrrepoid = repoid)
			INNER JOIN endereco_representante on (endvrepoid = relrrep_terceirooid)
			INNER JOIN antena_satelital_versao on (asatasvoid = asvoid)
			INNER JOIN antena_satelital_modelo on (asmoid = asatmooid)
			WHERE $filtro2
			AND repoid IN ($representantes)
			AND asatmooid IN ($implodeAntenas)
			AND to_date(aux1.dt_disponivel, 'DD/MM/YYYY') BETWEEN to_date('$agingDataInicial', 'DD/MM/YYYY') AND to_date('$agingDataFinal', 'DD/MM/YYYY')
			$where
			GROUP BY asmdescricao, substring(dt_disponivel,4,8)
			ORDER BY disponivel;
			";	
			
			return $rsSinteticoAntena = pg_query($this->conn, $sqlSinteticoAntena);
		}
		
		if ($relatorio=='analitico') {
				
			$sqlAnaliticoAntena = "
				SELECT DISTINCT
					first_value(asmdescricao) OVER window_antena as modelo,
					first_value(rephist.repnome) OVER window_antena as nome_representante,
					first_value(endrephist.endvuf) OVER window_antena as uf,
					first_value(asatno_serie) OVER window_antena as serial,
					first_value(asvdescricao) OVER window_antena as versao,
					first_value(dt_disponivel::date - dt_retirada_proximo::date) OVER window_antena AS tempo,
					first_value(dt_disponivel) OVER window_antena AS disponivel,
					first_value(dt_retirada_proximo) OVER window_antena AS retirada
				FROM (
					SELECT
						ashassoid as status,
						lag(ashassoid) OVER (window_retirada) As proximo_status,
						to_char(ashdt_historico, 'DD/MM/YYYY') dt_disponivel,
						lag(to_char(ashdt_historico, 'DD/MM/YYYY'))  OVER (window_retirada) As dt_retirada_proximo,
						ashasatoid,
						
						CASE
							WHEN LAG(ashrelroid) OVER window_retirada IS NULL THEN
								NULL
							ELSE LAG(ashitloid) OVER window_retirada 				
						END AS instalador_retirada --ID do instalador que fez a retirada
						
					FROM (SELECT ashasatoid, ashdt_historico, ashassoid, ashrelroid, ashitloid
					      FROM antena_satelital_historico 
					      WHERE $filtro1  
					      ORDER BY ashasatoid,ashdt_historico DESC) as n 
					WINDOW window_retirada AS (PARTITION BY n.ashasatoid ORDER BY n.ashdt_historico DESC)
					ORDER BY n.ashdt_historico DESC
				) as aux1
				INNER JOIN antena_satelital on (ashasatoid = asatoid)
				
				LEFT JOIN instalador AS hist_instalador ON instalador_retirada = itloid
				LEFT JOIN representante AS rephist on (hist_instalador.itlrepoid = rephist.repoid)
				LEFT JOIN endereco_representante AS endrephist on (endrephist.endvrepoid = rephist.repoid)
				
				INNER JOIN relacionamento_representante on (asatrelroid = relacionamento_representante.relroid)
				INNER JOIN representante on (relacionamento_representante.relrrepoid = representante.repoid)
				INNER JOIN endereco_representante on (endereco_representante.endvrepoid = relacionamento_representante.relrrep_terceirooid)
				INNER JOIN antena_satelital_versao on (asatasvoid = asvoid)
				INNER JOIN antena_satelital_modelo on (asmoid = asatmooid)     
				WHERE $filtro2
				  AND representante.repoid IN ($representantes)
				  AND asatmooid IN ($implodeAntenas)
				  AND to_date(aux1.dt_disponivel, 'DD/MM/YYYY') BETWEEN to_date('$agingDataInicial', 'DD/MM/YYYY') AND to_date('$agingDataFinal', 'DD/MM/YYYY')
				  $where
				WINDOW window_antena AS (PARTITION BY asatno_serie ORDER BY dt_disponivel DESC)
				ORDER BY 1, 7;
		";
		
		return $rsAnaliticoAntena = pg_query($this->conn, $sqlAnaliticoAntena);
		}		
		
	}
	
	
}