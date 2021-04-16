<?php

 /**
 * STI - 85394 Relatório Posição Estoque - CLASSE GERA POSIÇÃO ESTOQUE DIÁRIA
 * @author Bruno Luiz Kumagai Aldana - <bruno.aldana.ext@sascar.com.br>
 * @since 09/06/2015
 * @category Class
 * @package CronPosicaoEstoqueDAO
 */

class CronPosicaoEstoqueDAO {   
	
    private $conn;
  
    /**
     * MÉTODO APAGA POSIÇÃO ESTOQUE 3 MESES ANTERIORES
     * @return boolean
     * @throws ErrorException
     */
    public function deletePosicaoEstoqueTrimestral() {

	    try {
	    	//$data_trimestre_anterior = date('Y-m-d');
    		$data_trimestre_anterior = date('Y-m-d', strtotime("-90 days")); 
    		$where = " WHERE petdt_posicao::date < '".$data_trimestre_anterior."' "; 
    		$sql_delete = "DELETE from posicao_estoque_trimestral $where ";
    		 
    		$result_delete = pg_query($this->conn, $sql_delete);
    		if( !$result_delete = pg_query($this->conn, $sql_delete)){
    			throw new Exception( "Erro ao Remover Registros dos 3 meses anteriores da tabela posicao_estoque_trimestral" );
    		}
    		 
    		return true;
	    		
	    } catch (Exception $e) { 
	    	return false;
	    }
    }
	/**
	 * MÉTODO QUE GERA POSIÇÃO ESTOQUE DIÁRIA
	 * @return boolean
	 * @throws ErrorException
	*/
	public function insertPosicaoEstoqueDaria() {
	
	try {
	
	$sql ="
	DROP TABLE IF EXISTS posicao_disponivel; 
		
	SELECT
	'M' AS tp_estoque,
	repoid,
	prdoid,
	coalesce(espqtde,0) AS qtd_disponivel,
	0 AS qtd_instalador,
	0 AS qtd_retirada,
	0 AS qtd_retornado,
	0 AS qtd_recall,
	0 AS qtd_recall_disponivel,
	0 AS qtd_manutencao_fornecedor,
	0 AS qtd_conferencia_IF,
	0 AS qtd_manutencao_interna,
	0 AS qtd_aguardando_manutencao
		INTO TEMP
			posicao_disponivel
		FROM
	produto
	JOIN estoque_produto ON espprdoid = prdoid
	JOIN relacionamento_representante ON relroid = esprelroid
	JOIN representante ON repoid = relrrep_terceirooid
		WHERE
	prddt_exclusao IS NULL
	AND prdtp_cadastro='P'
	AND prdptioid IS NOT NULL
	AND prdstatus = 'A'
	AND (prdenvia_repr = 'S' OR prdconsta_pedido='S')
	AND espqtde > 0
	AND repexclusao IS NULL

	UNION

	SELECT
	'I' AS tp_estoque,
	repoid,
	imobprdoid,
	SUM(CASE WHEN imstcodigo = 'DSP' THEN 1 ELSE 0 END) AS qtd_disponivel,
	SUM(CASE WHEN imstcodigo = 'ITL' THEN 1 ELSE 0 END) AS qtd_instalador,
	SUM(CASE WHEN imstcodigo = 'RTI' THEN 1 ELSE 0 END) AS qtd_retirada,
	SUM(CASE WHEN imstcodigo = 'RTO' THEN 1 ELSE 0 END) AS qtd_retornado,
	SUM(CASE WHEN imstcodigo = 'ERL' THEN 1 ELSE 0 END) AS qtd_recall,
	SUM(CASE WHEN imstcodigo = 'ERD' THEN 1 ELSE 0 END) AS qtd_recall_disponivel,
	SUM(CASE WHEN imstcodigo = 'MUF' THEN 1 ELSE 0 END) AS qtd_manutencao_fornecedor,
	SUM(CASE WHEN imstcodigo = 'CFN' THEN 1 ELSE 0 END) AS qtd_conferencia_IF,
	SUM(CASE WHEN imstcodigo = 'MUI' THEN 1 ELSE 0 END) AS qtd_manutencao_interna,
	SUM(CASE WHEN imstcodigo = 'AMU' THEN 1 ELSE 0 END) AS qtd_aguardando_manutencao
	FROM
	produto
	JOIN imobilizado ON imobprdoid = prdoid
	JOIN relacionamento_representante ON relroid = imobrelroid
	JOIN representante ON repoid = relrrep_terceirooid
	JOIN imobilizado_status ON imsoid = imobimsoid
	JOIN imobilizado_status_tipo ON imstoid = imsimstoid
	WHERE
	prddt_exclusao IS NULL
	AND prdtp_cadastro='P'
	AND prdptioid IS NOT NULL
	AND prdstatus = 'A'
	AND (prdenvia_repr = 'S' OR prdconsta_pedido='S')
	AND imobexclusao IS NULL
	AND repexclusao IS NULL
	AND imstuso = 'RPE'
	GROUP BY
	tp_estoque,
	repoid,
	imobprdoid;
	---------------------------------------------------------------------------------------------------
	DROP TABLE IF EXISTS posicao_transito;

	SELECT
	'M' AS tp_estoque,
	repoid,
	prdoid,
	SUM(esriqtde) AS qtd_transito
	INTO TEMP
	posicao_transito
	FROM
	produto
	JOIN estoque_remessa_item ON esrirefoid = prdoid
	JOIN estoque_remessa ON esroid = esrioid
	JOIN relacionamento_representante ON relroid = esrrelroid
	JOIN representante ON repoid = relrrepoid
	WHERE
	esrersoid = 1
	AND esrdt_exclusao IS NULL
	AND esripatrimonio IS NULL
	AND esriqtde > 0
	GROUP BY
	repoid,
	prdoid

	UNION

	SELECT
	'I' AS tp_estoque,
	repoid,
	prdoid,
	COUNT(*) AS qtd_transito
	FROM
	produto
	JOIN imobilizado ON imobprdoid = prdoid
	JOIN estoque_remessa_item ON (esripatrimonio = imobpatrimonio AND esriimotoid = imobimotoid)
	JOIN estoque_remessa ON esroid = esrioid
	JOIN relacionamento_representante ON relroid = esrrelroid
	JOIN representante ON repoid = relrrepoid
	WHERE
	esrersoid = 1
	AND esrdt_exclusao IS NULL
	AND esripatrimonio IS NOT NULL
	GROUP BY
	repoid,
	prdoid;
	---------------------------------------------------------------------------------------------------
			DROP TABLE IF EXISTS posicao_reserva;

	SELECT
	'M' AS tp_estoque,
	repoid,
	prdoid,
				coalesce(SUM(raiqtde_estoque),0) AS qtd_reserva,
				coalesce(SUM(raiqtde_transito),0) AS qtd_res_transito
	INTO TEMP
	posicao_reserva
	FROM
	produto
	JOIN reserva_agendamento_item ON raiprdoid = prdoid
	JOIN reserva_agendamento ON ragoid = rairagoid
	JOIN representante ON repoid = ragrepoid
	JOIN reserva_agendamento_status ON rasoid = ragrasoid
	JOIN produto_tipo ON ptioid = prdptioid
	WHERE
	prddt_exclusao IS NULL
	AND prdtp_cadastro='P'
	AND prdptioid IS NOT NULL
	AND prdstatus = 'A'
	AND (prdenvia_repr = 'S' OR prdconsta_pedido='S')
	AND repexclusao IS NULL
	AND raidt_exclusao IS NULL
	AND (rasdescricao ILIKE 'Pr%Reserva' OR rasdescricao ILIKE 'Reservado')
	AND ptidescricao <> 'Imobilizado'
	GROUP BY
	repoid,
	prdoid

	UNION

	SELECT
	'I' AS tp_estoque,
	repoid,
	prdoid,
				coalesce(SUM(raiqtde_estoque),0) AS qtd_reserva,
				coalesce(SUM(raiqtde_transito),0) As qtd_reserva_transito
	FROM
	produto
	JOIN reserva_agendamento_item ON raiprdoid = prdoid
	JOIN reserva_agendamento ON ragoid = rairagoid
	JOIN representante ON repoid = ragrepoid
	JOIN reserva_agendamento_status ON rasoid = ragrasoid
	JOIN produto_tipo ON ptioid = prdptioid
	WHERE
	prddt_exclusao IS NULL
	AND prdtp_cadastro='P'
	AND prdptioid IS NOT NULL
	AND prdstatus = 'A'
	AND (prdenvia_repr = 'S' OR prdconsta_pedido='S')
	AND raidt_exclusao IS NULL
	AND (rasdescricao ILIKE 'Pr%Reserva' OR rasdescricao ILIKE 'Reservado')
	AND repexclusao IS NULL
	AND ptidescricao = 'Imobilizado'
	GROUP BY
	repoid,
	prdoid;
	---------------------------------------------------------------------------------------------------
		
			INSERT 
			INTO
				posicao_estoque_trimestral(
					pettp_item,
		petrepoid, 
		petprdoid, 
		petqtd_disponivel, 
		petqtd_instalador, 
		petqtd_retirada, 
		petqtd_retornado, 
		petqtd_recall, 
		petqtd_recall_disponivel, 
		petqtd_manutencao_fornecedor, 
		petqtd_conferencia_if, 
		petqtd_manutencao_interna, 
		petqtd_aguardando_manutencao, 
		petqtd_transito, 
		petqtd_reserva, 
					petpcmoid,
					petqtd_reserva_transito
				)
			SELECT DISTINCT
				pettp_item,
				petrepoid,
				petprdoid,
				SUM(petqtd_disponivel) AS petqtd_disponivel,
				SUM(petqtd_instalador) AS petqtd_instalador,
				SUM(petqtd_retirada) AS petqtd_retirada,
				SUM(petqtd_retornado) AS petqtd_retornado,
				SUM(petqtd_recall) AS petqtd_recall,
				SUM(petqtd_recall_disponivel) AS petqtd_recall_disponivel,
				SUM(petqtd_manutencao_fornecedor) AS petqtd_manutencao_fornecedor,
				SUM(petqtd_conferencia_if) AS petqtd_conferencia_if,
				SUM(petqtd_manutencao_interna) AS petqtd_manutencao_interna,
				SUM(petqtd_aguardando_manutencao) AS petqtd_aguardando_manutencao,
				SUM(petqtd_transito) AS petqtd_transito,
				SUM(petqtd_reserva) AS petqtd_reserva,
				petpcmoid,
				SUM(petqtd_reserva_transito) AS petqtd_reserva_transito
	FROM 
				(
				SELECT
					d.tp_estoque AS pettp_item,
					d.repoid AS petrepoid,
					d.prdoid AS petprdoid,
					d.qtd_disponivel AS petqtd_disponivel,
					d.qtd_instalador AS petqtd_instalador,
					d.qtd_retirada AS petqtd_retirada,
					d.qtd_retornado AS petqtd_retornado,
					d.qtd_recall AS petqtd_recall,
					d.qtd_recall_disponivel AS petqtd_recall_disponivel,
					d.qtd_manutencao_fornecedor AS petqtd_manutencao_fornecedor,
					d.qtd_conferencia_IF AS petqtd_conferencia_if,
					d.qtd_manutencao_interna AS petqtd_manutencao_interna,
					d.qtd_aguardando_manutencao AS petqtd_aguardando_manutencao,
					0 AS petqtd_transito,
					0 AS petqtd_reserva,
					(SELECT pcmoid FROM produto_custo_medio WHERE pcmprdoid = d.prdoid  AND pcmdt_exclusao IS NULL AND pcmusuoid_exclusao IS NULL ORDER BY pcmdt_cadastro DESC LIMIT 1) AS petpcmoid,
					0 AS petqtd_reserva_transito
				FROM
	posicao_disponivel d

	UNION

	SELECT 
					t.tp_estoque AS pettp_item,
					t.repoid AS petrepoid,
					t.prdoid AS petprdoid,
					0 AS petqtd_disponivel,
					0 AS petqtd_instalador,
					0 AS petqtd_retirada,
					0 AS petqtd_retornado,
					0 AS petqtd_recall,
					0 AS petqtd_recall_disponivel,
					0 AS petqtd_manutencao_fornecedor,
					0 AS petqtd_conferencia_if,
					0 AS petqtd_manutencao_interna,
					0 AS petqtd_aguardando_manutencao,
					t.qtd_transito AS petqtd_transito,
					0 AS petqtd_reserva,
					(SELECT pcmoid FROM produto_custo_medio WHERE pcmprdoid = t.prdoid  AND pcmdt_exclusao IS NULL AND pcmusuoid_exclusao IS NULL ORDER BY pcmdt_cadastro DESC LIMIT 1) AS petpcmoid,
					0 AS petqtd_reserva_transito
	FROM 
	posicao_transito t 

	UNION

	SELECT 
					r.tp_estoque AS pettp_item,
					r.repoid AS petrepoid,
					r.prdoid AS petprdoid,
					0 AS petqtd_disponivel,
					0 AS petqtd_instalador,
					0 AS petqtd_retirada,
					0 AS petqtd_retornado,
					0 AS petqtd_recall,
					0 AS petqtd_recall_disponivel,
					0 AS petqtd_manutencao_fornecedor,
					0 AS petqtd_conferencia_if,
					0 AS petqtd_manutencao_interna,
					0 AS petqtd_aguardando_manutencao,
					0 AS petqtd_transito,
					r.qtd_reserva AS petqtd_reserva,
					(SELECT pcmoid FROM produto_custo_medio WHERE pcmprdoid = r.prdoid  AND pcmdt_exclusao IS NULL AND pcmusuoid_exclusao IS NULL ORDER BY pcmdt_cadastro DESC LIMIT 1) AS petpcmoid,
					r.qtd_res_transito AS petqtd_reserva_transito
	FROM 
	posicao_reserva r 
	
	ORDER BY 
					pettp_item,
					petrepoid) tmp
			GROUP BY
				pettp_item,
				petrepoid,
				petprdoid,
				petpcmoid; ";
 
	$rs = pg_query($this->conn, $sql);
 
	return true;
	
	} catch (Exception $e) {
		return false;
	}
	if (!$rs = pg_query($this->conn,$sql)){
		throw new Exception("Houve um erro ao gerar posição de estoque diário tabela posicao_estoque_trimestral.");
	} 
	
	}
	/**
	 * Consulta data posição
	 * @throws Exception
	 * @return boolean
	 */
	public function getDataPosicaoEstoque($filtros = '') {
	
		if (!empty($filtros['data_posicao'])) {
			 $campos = " DISTINCT(petdt_posicao) ";
			 $filtro = " WHERE petdt_posicao::date = '".$filtros['data_posicao']."' ";
		} 
		
		$sql = "
		SELECT
		$campos
		FROM
		posicao_estoque_trimestral
		$filtro
		ORDER BY
		petdt_posicao DESC ";
 
		if (!$rs = pg_query($this->conn, $sql)){
		throw new ErrorException("Erro ao retornar data posição estoque ");
		}
		
		$row = pg_fetch_object($rs);
 
		if($row){
			$retorno = true;
		}else{
			$retorno = false;
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