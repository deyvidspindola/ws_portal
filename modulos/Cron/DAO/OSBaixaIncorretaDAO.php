<?php

/**
 * Classe responsável pela persistência de dados
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 25/09/2015
 * @category Class
 * @package OSBaixaIncorretaDAO
 */

class OSBaixaIncorretaDAO { 
	
    private $conn;	
    /**
     * Método para buscar dados da quantidade 
     * baixada
     *
     * @param $dt_base
     * @return int
     * @throws
     */
    public function gerarDadosQtdBaixada($dt_base){
    	
    	$sql = "
				DROP TABLE if exists tmp_qtd_baixada_osp;
 				
    			SELECT DISTINCT
					cmidata::date AS cmidata,
					ordoid,
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,
					(SELECT SUM(osp.ospqtde) FROM ordem_servico_produto osp WHERE osp.ospordoid = ordoid AND osp.ospprdoid = prdoid GROUP BY osp.ospprdoid) AS qtd_baixada,
					0 AS qtd_necessaria,
					prdoid,
					prdgrsoid
				INTO TEMP
					tmp_qtd_baixada_osp
				FROM 
					comissao_instalacao  
					INNER JOIN ordem_servico ON ordoid = cmiord_serv
					INNER JOIN instalador ON itloid = cmiitloid  
					INNER JOIN os_tipo_item ON otioid = cmiotioid
					INNER JOIN os_tipo ON (ostoid = otiostoid AND ostoid IN (1,2,9))
					INNER JOIN ordem_servico_produto ON (ospordoid = cmiord_serv AND ospqtde > 0) 
					INNER JOIN produto ON prdoid = ospprdoid
				WHERE 
					TRUE 
					AND cmidata IS NOT NULL 
					AND cmiexclusao IS NULL 
					AND (
						cmidata > (SELECT invdt_ajuste FROM inventario WHERE invdt_ajuste IS NOT NULL AND invrepoid = itlrepoid AND invvaloid = 97 ORDER BY invdt_ajuste DESC LIMIT 1) 
						OR 
						NOT EXISTS (SELECT invoid FROM inventario WHERE invrepoid = itlrepoid AND invvaloid = 97)) 
					AND cmidata BETWEEN '".$dt_base." 00:00:00' AND '".$dt_base." 23:59:59' 
					AND ordstatus = 3
				GROUP BY 
					cmidata,
					ordoid,
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,
					prdoid, 
					prdgrsoid; ";

		//echo $sql;
		//exit;
		if ($rs = pg_query($this->conn,$sql)){
			$retorno = 1;
		} 
		return $retorno;
    	    	 
    }
	
    /**
     * Método para buscar dados da quantidade 
     * necessária de acordo com o projeto
     *
     * @param $dt_base
     * @return int
     * @throws
     */
    public function gerarDadosQtdNecessariaEpp($dt_base){
    	
		$sql = "
				DROP TABLE if exists tmp_qtd_necessaria_epp;
				
				SELECT  
					cmidata,
					ordoid, 
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,  
					0 AS qtd_baixada,
					COUNT(eppoid) AS qtd_necessaria,
					prdoid,
					prdgrsoid
				INTO TEMP
					tmp_qtd_necessaria_epp
				FROM
				(
					SELECT DISTINCT
						cmidata::date AS cmidata, 
						ordoid, 
						orddt_ordem, 
						ordconnumero,
						ordusuoid_concl,
						ordrelroid,
						itloid,
						itlrepoid,
						eppoid,
						prdoid,
						prdgrsoid 
					FROM 
						comissao_instalacao 
						INNER JOIN ordem_servico ON ordoid = cmiord_serv 
						INNER JOIN instalador ON itloid = cmiitloid 
						INNER JOIN os_tipo_item ON otioid = cmiotioid
						INNER JOIN os_tipo ON (ostoid = otiostoid AND ostoid IN (1,2,9)) 
						INNER JOIN contrato ON connumero = ordconnumero 
						INNER JOIN equipamento ON equoid = conequoid 
						INNER JOIN equipamento_versao ON eveoid = equeveoid 
						INNER JOIN equipamento_projeto_produto ON (eppeproid = eveprojeto AND eppeqcoid = coneqcoid) 
						INNER JOIN produto ON prdoid = eppprdoid  
					WHERE 
						TRUE 
						AND cmidata IS NOT NULL 
						AND cmiexclusao IS NULL 
						AND (
							cmidata > (SELECT invdt_ajuste FROM inventario WHERE invdt_ajuste IS NOT NULL AND invrepoid = itlrepoid AND invvaloid = 97 ORDER BY invdt_ajuste DESC LIMIT 1) 
							OR 
							NOT EXISTS (SELECT invoid FROM inventario WHERE invrepoid = itlrepoid AND invvaloid = 97))
						AND cmidata BETWEEN '".$dt_base." 00:00:00' AND '".$dt_base." 23:59:59'
						AND ordstatus = 3
				) EPP
				GROUP BY 
					cmidata,
					ordoid, 
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,
					eppoid,
					prdoid,
					prdgrsoid; ";

		//echo $sql;
		//exit;
		if ($rs = pg_query($this->conn,$sql)){
			$retorno = 1;
		}
		return $retorno;
    	    	 
    }
	
    /**
     * Método para buscar dados das quantidades 
     * necessárias 1 e 3 de acordo com o motivo
     *
     * @param $dt_base
     * @return array
     * @throws
     */
    public function buscarQtdNecessariaMpm1_3($dt_base){
		
    	$retorno = array();
		
		$sql = "
			SELECT DISTINCT
				cmidata::date AS cmidata,
				cmiotioid,
				ordoid, 
				orddt_ordem,
				ordconnumero,
				ordusuoid_concl,
				ordrelroid,
				itloid,
				itlrepoid, 
				otiobroid,
				0 AS qtd_baixada, 
				COALESCE((SELECT COUNT(mpr.mproid) FROM motivo_produto mpr WHERE mpr.mprotioid = cmiotioid AND mpr.mprprdoid = prdoid GROUP BY mpr.mprprdoid),0) AS qtd_necessaria1,
				0 qtd_necessaria2,
				COALESCE((SELECT COUNT(mpm.mpmmatoid) FROM motivo_produto_material mpm WHERE mpm.mpmotioid = cmiotioid AND mpm.mpmmatoid = prdoid GROUP BY mpm.mpmmatoid),0) AS qtd_necessaria3,
				prdoid,
				prdgrsoid, 
				oftcprefixo,
				oftctabela
			FROM 
				comissao_instalacao 
				INNER JOIN ordem_servico ON ordoid = cmiord_serv 
				INNER JOIN contrato ON connumero = ordconnumero 
				INNER JOIN instalador ON itloid = cmiitloid 
				INNER JOIN os_tipo_item ON otioid = cmiotioid
				INNER JOIN os_tipo ON (ostoid = otiostoid AND ostoid IN (1,2,9))	 
				LEFT JOIN motivo_produto ON mprotioid = cmiotioid 
				LEFT JOIN motivo_produto_material ON mpmotioid = cmiotioid 
				INNER JOIN produto ON (prdoid = mprprdoid OR prdoid = mpmmatoid)
				LEFT JOIN obrigacao_financeira_tecnica ON (oftcobroid = otiobroid AND oftcexclusao IS NULL)
			WHERE 
				TRUE 
				AND cmidata IS NOT NULL 
				AND cmiexclusao IS NULL 
				AND (
					cmidata > (SELECT invdt_ajuste FROM inventario WHERE invdt_ajuste IS NOT NULL AND invrepoid = itlrepoid AND invvaloid = 97 ORDER BY invdt_ajuste DESC LIMIT 1) 
					OR 
					NOT EXISTS (SELECT invoid FROM inventario WHERE invrepoid = itlrepoid AND invvaloid = 97))
				AND cmidata BETWEEN '".$dt_base." 00:00:00' AND '".$dt_base." 23:59:59' 
				AND ordstatus = 3
			GROUP BY 
				cmidata,
				cmiotioid,
				ordoid, 
				orddt_ordem,
				ordconnumero,
				ordusuoid_concl,
				ordrelroid,
				itloid,
				itlrepoid, 
				otiobroid,
				prdoid,
				prdgrsoid, 
				oftcprefixo,
				oftctabela; ";
		
		//echo $sql;
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao consultar dados das quantidades necessárias 1 e 3 de acordo com o motivo.");
		}
 		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}
	
    /**
     * Método para buscar dados das quantidades 
     * necessárias 2 de acordo com o motivo, 
     * produto e material
     *
     * @param $otioid, $prdoid, $prefixo, $tabela, $conoid, $obroid
     * @return int
     * @throws
     */
    public function buscarQtdNecessariaMpm2($otioid,$prdoid,$prefixo,$tabela,$conoid,$obroid) {

    	$retorno = 0;
    	
    	$sql = "
    		SELECT 
				COUNT(mpmoid) AS qtd_necessaria2
			FROM 
				motivo_produto_material 
			WHERE 
				mpmotioid = ".$otioid." 
				AND mpmmatoid = ".$prdoid." 
				AND mpmprdoid IN (
					SELECT 
						".$prefixo."prdoid
					FROM 
						".$tabela."
					WHERE
						".$prefixo."oid IN (
						SELECT 
							consrefioid 
						FROM 
							contrato_servico 
						WHERE 
							consconoid = ".$conoid."  
							AND consobroid = ".$obroid.")); ";

		//echo $sql;
		//exit;
		if (($rs = pg_query($this->conn,$sql)) && pg_num_rows($rs)>0){
			$retorno = pg_fetch_result($rs, 0, 'qtd_necessaria2');
		} 
		return $retorno;
    }
	
    /**
     * Método para verificar se o motivo possui 
     * algum material relacionado
     *
     * @param $otioid
     * @return int
     * @throws
     */
    public function verificarQtdeNecessaria1($otioid) {

    	$retorno = 0;
    	
    	$sql = "
    		SELECT 
				COUNT(mproid) AS qtd
			FROM 
				motivo_produto 
			WHERE 
				mprotioid = ".$otioid."
    		GROUP BY
    			mprprdoid; ";

		//echo $sql;
		//exit;
		if (($rs = pg_query($this->conn,$sql)) && pg_num_rows($rs)>0){
			$retorno = pg_fetch_result($rs, 0, 'qtd');
		} 
		return $retorno;
    } 
	
    /**
     * Método para dropar a tabela temporária de quantidades 
     * necessárias de acordo com o motivo e criar uma tabela 
     * temporária nova
     *
     * @param 
     * @return int
     * @throws
     */
    public function criarTabQtdNecessariaMpm(){
    	$sql = " 
    		DROP TABLE if exists tmp_qtd_necessaria_mpm;
    			
    		CREATE TEMP TABLE tmp_qtd_necessaria_mpm(
				cmidata timestamp without time zone,
				ordoid integer, 
				orddt_ordem timestamp without time zone,
				ordconnumero integer,
				ordusuoid_concl integer,
				ordrelroid integer,
				itloid integer,
				itlrepoid integer,
				prdoid integer, 
				prdgrsoid integer,
				qtd_baixada integer,
				qtd_necessaria integer)
			WITH (
			  OIDS=FALSE
			); ";

    	//echo $sql;
    	//exit;
    	if ($rs = pg_query($this->conn,$sql)){
    		$retorno = 1;
    	}
    	return $retorno;
    }
    
    /**
     * Método para buscar dados da quantidade necessária 
     * de acordo com o motivo
     *
     * @param $dt_base
     * @return int
     * @throws
     */
    public function gerarDadosQtdNecessariaMpm($cmidata,$ordoid,$orddt_ordem,$ordconnumero,$ordusuoid_concl,$ordrelroid,$itloid,$itlrepoid,$prdoid,$prdgrsoid,$qtd_baixada,$qtd_necessaria){
		
		$sql = "
			INSERT INTO tmp_qtd_necessaria_mpm(
				cmidata,
				ordoid, 
				orddt_ordem,
				ordconnumero,
				ordusuoid_concl,
				ordrelroid,
				itloid,
				itlrepoid, 
				prdoid, 
				prdgrsoid,
				qtd_baixada,
				qtd_necessaria)
			VALUES (
				'".$cmidata."',
				".$ordoid.",
				'".$orddt_ordem."',
				".$ordconnumero.",
				".$ordusuoid_concl.",
				".$ordrelroid.",
				".$itloid.",
				".$itlrepoid.",
				".$prdoid.", 
				".$prdgrsoid.",
				".$qtd_baixada.",
				".$qtd_necessaria."); ";

		//echo $sql;
		//exit;
		if ($rs = pg_query($this->conn,$sql)){
			$retorno = 1;
		} 
		return $retorno;
    	    	 
    }
	
    /**
     * Método para consolidar os dados de qtd baixada 
     * e qtd necessária
     *
     * @param 
     * @return int
     * @throws
     */
    public function consolidarDadosOS(){
		
		$sql = "
				DROP TABLE if exists tmp_dados_os_consolidados; 

				SELECT DISTINCT
					cmidata,
					ordoid,
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,  
					SUM(qtd_baixada) AS total_baixada, 
					SUM(qtd_necessaria) AS total_necessaria,
					prdoid,
					prdgrsoid 
				INTO TEMP 
					tmp_dados_os_consolidados
				FROM
					(
						SELECT
							cmidata,
							ordoid,
							orddt_ordem,
							ordconnumero,
							ordusuoid_concl,
							ordrelroid,
							itloid,
							itlrepoid,  
							qtd_baixada, 
							qtd_necessaria,
							prdoid,
							prdgrsoid 
						FROM
							tmp_qtd_baixada_osp
						WHERE 
							prdoid NOT IN (SELECT obiprdoid FROM os_baixa_incorreta obi WHERE obi.obiprdoid = prdoid AND obi.obiordoid = ordoid)
				
						UNION ALL
				
						SELECT 
							cmidata,
							ordoid,
							orddt_ordem,
							ordconnumero,
							ordusuoid_concl,
							ordrelroid,
							itloid,
							itlrepoid,  
							qtd_baixada, 
							qtd_necessaria,
							prdoid,
							prdgrsoid 
						FROM
							tmp_qtd_necessaria_epp
						WHERE 
							prdoid NOT IN (SELECT obiprdoid FROM os_baixa_incorreta obi WHERE obi.obiprdoid = prdoid AND obi.obiordoid = ordoid)
				
						UNION ALL
				
						SELECT 
							cmidata,
							ordoid,
							orddt_ordem,
							ordconnumero,
							ordusuoid_concl,
							ordrelroid,
							itloid,
							itlrepoid,  
							qtd_baixada, 
							qtd_necessaria,
							prdoid,
							prdgrsoid 
						FROM
							tmp_qtd_necessaria_mpm
						WHERE 
							prdoid NOT IN (SELECT obiprdoid FROM os_baixa_incorreta obi WHERE obi.obiprdoid = prdoid AND obi.obiordoid = ordoid)
				
					) os_incorreta 
				GROUP BY
					cmidata,
					ordoid,
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,
					prdoid,
					prdgrsoid; ";

		//echo $sql;
		//exit;
		if ($rs = pg_query($this->conn,$sql)){
			$retorno = 1;
		} 
		return $retorno;
    	    	 
    }
	
    /**
     * Método para alimentar a tab tmp_os_baixa_incorreta, 
     * buscando os registros cujas OSs tiveram baixa de 
     * material consideradas incorretas
     *
     * @param 
     * @return int
     * @throws
     */
    public function gerarDadosOSIncorreta(){
    	
		$sql = "
				DROP TABLE if exists tmp_os_baixa_incorreta;
				
				SELECT DISTINCT
					cmidata,
					ordoid,
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid, 
					SUM(total_baixada) AS total_baixada, 
					SUM(total_necessaria) AS total_necessaria,				
					tdoc.prdoid,
					tdoc.prdgrsoid
				INTO TEMP
					tmp_os_baixa_incorreta
				FROM
					tmp_dados_os_consolidados tdoc 
					INNER JOIN produto prd ON prd.prdoid =  tdoc.prdoid
				WHERE
					TRUE
					AND total_baixada <> total_necessaria
				GROUP BY
					cmidata,
					ordoid,
					orddt_ordem,
					ordconnumero,
					ordusuoid_concl,
					ordrelroid,
					itloid,
					itlrepoid,	
					tdoc.prdoid,
					tdoc.prdgrsoid, 
					prdlimitador_minimo, 
					prdlimitador_maximo	
				HAVING 
					CASE 
						WHEN SUM(total_necessaria) > 0
							THEN
								(SUM(total_baixada) < prdlimitador_minimo OR SUM(total_baixada) > prdlimitador_maximo)
						ELSE TRUE
					END; ";
    
		//echo $sql;
	    //exit;
	    if ($rs = pg_query($this->conn,$sql)){
	    	$retorno = 1;
	    }
	    return $retorno;
     
    }
	
    /**
     * Método para alimentar a tab os_baixa_incorreta, 
     * buscando os registros cujas OSs tiveram baixa de 
     * material consideradas incorretas, aplicando a 
     * regra de similaridade entre materiais
     *
     * @param 
     * @return int
     * @throws
     */
    public function gerarDadosOSIncorretaSemSimilares(){
    	
    	$retorno = null;
    	
		$sql = "
    		INSERT INTO
		    	os_baixa_incorreta(
		    	obiconoid,
		    	obiitloid,
		    	obiordoid,
		    	obiprdoid,
		    	obirelroid,
		    	obirepoid,
		    	obiqtd_baixada,
		    	obiqtd_necessaria,
		    	obidt_ordem,
		    	obiusuoid_conclusao,
		    	obidt_conclusao
		    	)
				(
					SELECT DISTINCT 
						tbi.ordconnumero, 
						tbi.itloid, 
						tbi.ordoid, 
						tbi.prdoid, 
						tbi.ordrelroid, 
						tbi.itlrepoid, 
						SUM(tbi.total_baixada) AS total_baixada, 
						SUM(tbi.total_necessaria) AS total_necessaria,
						tbi.orddt_ordem, 
						tbi.ordusuoid_concl, 
						tbi.cmidata 
					FROM
						tmp_os_baixa_incorreta tbi
						INNER JOIN produto prd ON prd.prdoid = tbi.prdoid 
					WHERE
						TRUE 
					GROUP BY
						tbi.ordconnumero, 
						tbi.itloid, 
						tbi.ordoid, 
						tbi.prdoid, 
						tbi.prdgrsoid,
						tbi.ordrelroid, 
						tbi.itlrepoid, 
						tbi.orddt_ordem, 
						tbi.ordusuoid_concl, 
						tbi.cmidata, 
						prd.prdlimitador_minimo, 
						prd.prdlimitador_maximo
					HAVING 
						CASE 
							WHEN (SUM(tbi.total_baixada) > 0 AND SUM(tbi.total_baixada) > SUM(tbi.total_necessaria) AND tbi.prdgrsoid IS NOT NULL) 
								THEN
								(
									tbi.prdgrsoid NOT IN (
										SELECT 
											epp.prdgrsoid  
										FROM 
											tmp_qtd_necessaria_epp epp	
										WHERE
											epp.prdgrsoid IS NOT NULL
											AND epp.ordoid = tbi.ordoid
											AND epp.prdoid <> tbi.prdoid
									)
									AND
									tbi.prdgrsoid NOT IN (
										SELECT 
											mpm.prdgrsoid  
										FROM 
											tmp_qtd_necessaria_mpm mpm	
										WHERE
											mpm.prdgrsoid IS NOT NULL
											AND mpm.ordoid = tbi.ordoid
											AND mpm.prdoid <> tbi.prdoid
									)
									OR (
										SUM(tbi.total_baixada)< prdlimitador_minimo 
										OR 
										SUM(tbi.total_baixada) > prdlimitador_maximo
									)
								)
							WHEN (SUM(tbi.total_baixada) = 0 AND SUM(tbi.total_necessaria) > 0 AND tbi.prdgrsoid IS NOT NULL) 
								THEN
								tbi.prdgrsoid NOT IN (
									SELECT 
										osp.prdgrsoid  
									FROM 
										tmp_qtd_baixada_osp osp	
									WHERE
										osp.prdgrsoid IS NOT NULL
										AND osp.ordoid = tbi.ordoid
										AND osp.prdoid <> tbi.prdoid
								)
							ELSE TRUE
						END
				); ";
    
		//echo $sql;
	    //exit;
	    if ($rs = pg_query($this->conn,$sql)){
	    	$retorno = pg_affected_rows($rs);
	    }
	    
	    return $retorno;
    }
	
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function __get($var) {
        return $this->$var;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}