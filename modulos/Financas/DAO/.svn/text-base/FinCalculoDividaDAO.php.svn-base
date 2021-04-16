<?php
/**
 * @file FinCalculoDividaDAO.php
 * @author marcio.ferreira
 * @version 06/07/2015 15:05:17
 * @since 06/07/2015 15:05:17
 * @package SASCAR FinCalculoDividaDAO.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_calculo_divida_'.date('d-m-Y').'.txt');


class FinCalculoDividaDAO {
	
	/**
	 * Link de conexão com o banco
	 * 
	 * @property resource
	 */
	private $conn;
	
	/**
	 * Construtor
	 * 
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn) {
		
		$this->conn = $conn;
		
	}

	
	/**
	 * Pesquisa os dados e valores do(s) títulos informados
	 * 
	 * @param int $id_titulo - Quando mais de um título separa por ',' (vírgula)
	 * @throws Exception
	 * @return object
	 */
	public function pesquisarDadosTitulo($id_titulo){

		
			if(empty($id_titulo)){
				throw new Exception('ERRO: <b>Informe o título para pesquisar os dados.</b>');
			}
			
			
			$sql = " SELECT  titoid, 
			                 titvl_titulo, 
   							 TO_CHAR(titdt_vencimento, 'DD/MM/YYYY' ) AS titdt_vencimento, 
   							 TO_CHAR(titdt_vencimento, 'MM/DD/YYYY' ) AS titdt_vencimento_calc,
  							 TO_CHAR(titdt_vencimento, 'YYYY/MM/DD' ) AS titdt_vencimento_poli, 
   						     titdt_pagamento, 
							 COALESCE(titvl_desconto,0) AS titvl_desconto, 
							 COALESCE(titvl_multa,0) AS titvl_multa, 
							 COALESCE(titvl_juros,0) AS titvl_juros, 
							 COALESCE(titvl_ir,0) AS titvl_ir, 
							 COALESCE(titvl_iss,0) AS titvl_iss, 
							 COALESCE(titvl_piscofins,0) AS titvl_piscofins,
							 CASE WHEN titnfloid IS NOT NULL THEN (SELECT nflno_numero||' '||nflserie AS nota FROM nota_fiscal WHERE nfloid=titnfloid)
    						 ELSE 
    						 	CASE WHEN titno_cheque IS NOT NULL THEN titno_cheque||'/CH'
    								 WHEN titno_cartao IS NOT NULL AND titno_cartao<>'' THEN 'CART&Atilde;O'
    								 WHEN titnota_promissoria IS NOT NULL THEN titnota_promissoria||'/NP'
    								 WHEN titno_avulso IS NOT NULL THEN titno_avulso||'/AV' 
    							END 
    						 END AS nota 
					    FROM titulo 
				   LEFT JOIN nota_fiscal ON titnfloid = nfloid 
					   WHERE titoid IN ($id_titulo) 
					ORDER BY titdt_vencimento    ";
		
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao buscar dados do(s) título(s).</b>');
			}
			
			return pg_fetch_all($rs);
		
	}
	
	
	
	
	/**
	 * Busca todos os titulos vencidos na tabela titulo do cliente pesquisado ou por um título informado
	 * 
	 * @param Object $dados
	 * @throws Exception
	 */
	public function pesquisarTitulosVencidosCliente($dados){
	
		
		if(!is_object($dados)) {
			throw new Exception('Objeto inválido para pesquisar os títulos vencidos do cliente.');
		}

		$sql = " SELECT titoid,
	    				titclioid,
						titvl_titulo,
				        titdt_vencimento,
				        TO_CHAR(titdt_vencimento, 'YYYY/MM/DD' ) AS titdt_vencimento_poli,
				        'VENCIDO' AS status
				    FROM titulo
	    	   LEFT JOIN nota_fiscal ON titnfloid = nfloid
	          INNER JOIN forma_cobranca ON titformacobranca = forcoid
				   WHERE titdt_vencimento < CURRENT_TIMESTAMP(0)::DATE
	    			 AND titdt_cancelamento IS NULL
	    			 AND titdt_pagamento IS NULL
					 AND (titformacobranca = 51 AND titdt_credito IS NOT NULL OR ( titdt_credito IS NULL  ))
					 AND titnao_cobravel IS NOT TRUE
					 AND titformacobranca=forcoid 
					 AND (forccobranca IS TRUE OR forcnome = 'Título Avulso' or forcnome = 'Baixa como Perda')"; //MANTIS 8198 (Trazer somente o que é elegível para cobrança)
	
		if($dados->clioid != ''){
			$sql .= " AND titclioid = $dados->clioid ";
		}
	
		if($dados->titoid != ''){
			$sql .= " AND titoid IN($dados->titoid) ";
		}
	
		$sql .= "  ORDER BY titdt_vencimento ";
	
		//para cosultar o id do cliente pelo num do titulo, traz somente 1 registro
		if($dados->limit){
			$sql .= " LIMIT 1 ";
		}
	
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception('ERRO: <b>Falha ao buscar o(s) título(s) vencidos do cliente.</b>');
		}	
		
		return pg_fetch_all($rs);
	
	}
	
	
	
	/**
	 * Consulta em qual politica de desconto se enquadra a quantidade de dias em atraso
	 *
	 * @param INT $qtde_dias
	 * @return multitype:|boolean
	 */
	public function verificarAplicacaoPoliticaDesconto($qtde_dias, $podoid = ''){
	
		if(empty($qtde_dias)){
			throw new Exception('ERRO: <b>Informe a quantidade de dias vencido para pesquisar o tipo de política de desconto.</b>');
		}
		
		$sql = " SELECT podvlr_desconto,
						poddescricao_atraso,
						CASE
							WHEN podaplicacao = 'J'   THEN 'Juros'
							WHEN podaplicacao = 'M'   THEN 'Multa'
							WHEN podaplicacao = 'JM'  THEN 'Juros e Multa'
							WHEN podaplicacao = 'TJM' THEN 'Total, Juros e Multa'
						 ELSE 'Não aplicável'
						END as podaplicacao_desc,
						podaplicacao
		           FROM politica_desconto
		          WHERE $qtde_dias BETWEEN poddias_atraso_ini AND poddias_atraso_fim ";
		
		         if ($podoid != ''){
		         	$sql .= " AND podoid =  $podoid ";
		         }
		
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception('ERRO: <b>Falha ao buscar o tipo de política de desconto.</b>');
		}
		
		return pg_fetch_all($rs);
	
	}
	
	
	/**
	 * Recupera o id do motivo de desconto para aplicar nos títulos filhos 
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function getMotivoDescontoTituloFilho(){
		
		$sql = " SELECT mdescoid
		           FROM motivo_desconto
		          WHERE mdescdescricao ilike 'Política de Desconto'
				    AND mdescexclusao IS NULL  ";
		
		if (! $resul = pg_query ( $this->conn, $sql )) {
			throw new Exception ( 'Falha ao recuperar id do motivo do desconto.' );
		}
		
		if (pg_num_rows ( $resul ) > 0) {
			
			$res = pg_fetch_all ( $resul );
			
			return $res [0] ['mdescoid'];
		}
		
		return false;
		
	}
	
	
	/**
	 * Atualiza os valores dos títulos filhos na tabela título
	 * 
	 * @param object $dados
	 * @throws Exception
	 * @return boolean
	 */
	public function setValoresTituloFilho($dados){
	
		if(empty($dados)){
			throw new Exception('O titulo filho deve ser informado para atualizar valor.');
		}
	
		$sql = " UPDATE titulo
				    SET titvl_juros_desc_cobranca = $dados->valor_juros ,
				        titvl_multa_desc_cobranca = $dados->valor_multa ,
				        titvl_desc_cobranca       = $dados->valor_desconto_banco,
				        titmdescoid               = $dados->id_motivo_desconto
				  WHERE titoid = $dados->titoid   ";
	
		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar valor do titulo filho ->'. $dados->titoid);
		}
	
		return true;
	}
	
	
	
	/**
	 * atualiza os título filhos referenciando o titulo pai na coluna tittitcoid
	 *
	 * @param ARRAY $titulo_filho
	 * @param INT $titulo_pai
	 * @param STRING $pesq_titobshistorico
	 */
	public function atualizarTituloFilho($titulo_filho, $titulo_pai, $pesq_titobshistorico){
	
		if(empty($titulo_filho) || !is_array($titulo_filho)){
			throw new Exception('O titulo filho deve ser informado.');
		}
	
		if(empty($titulo_pai)){
			throw new Exception('O titulo pai deve ser informado.');
		}
	
		foreach ($titulo_filho as $titulo){
	
			$sql = " UPDATE titulo
						SET tittitcoid =  $titulo_pai,
							titobs_historico = '$pesq_titobshistorico'
					  WHERE titoid = $titulo   ";
	
			if(!$resul = pg_query($sql)){
				throw new Exception('Falha ao atualizar titulo filho ->'. $titulo);
			}
		}
	
		return true;
	
	}
	
	
	
	/**
	 * Retorna o id do tipo do titulo para inserir na tabela titulo
	 *
	 * @return multitype:|boolean
	 */
	public function getTipoTitulo($dados){
		
		if(!is_object($dados)){
			throw new Exception('Objeto inválido para verificar o tipo de título.');
		}
		
		$sql = " SELECT tittoid
				   FROM titulo_tipo
		          WHERE titttipo = '$dados->tipo'
		            AND tittdescricao = '$dados->descricao'  ";
	
		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao recuperar id do tipo titulo');
		}
	
		if(pg_num_rows($resul) > 0 ){
	
			$res = pg_fetch_all($resul);
	
			return $res[0]['tittoid'];
		}
	
		return false;
	
	}
	
	
	/**
	 *
	 * @param string $titulos_filhos --separados por ','
	 */
	public function getTituloConsolidado($titulos_filhos){
	
		if(empty($titulos_filhos)){
			throw new Exception('O titulo filho deve ser informado para recuperar o título pai.');
		}
	
		$sql = "  SELECT 	t.tittitcoid,
											tc.titcformacobranca,
											tc.titcdt_vencimento,
											tc.titcvl_recalculado,
											tc.titcvl_desconto,
											tc.titcvl_titulo,
											tc.titcvl_juros,
											tc.titcvl_multa,
											tc.titcemissao
		            FROM titulo t
		      INNER JOIN titulo_consolidado tc ON tc.titcoid = t.tittitcoid
		      INNER JOIN titulo_tipo tpt ON tpt.tittoid = tc.titctittoid
		           WHERE t.titoid IN ($titulos_filhos)
		             AND tpt.titttipo = 'PD' --Tipo do tipo politica de desconto
		             AND tc.titcformacobranca IN (63,84) --titulo avulso ";
	
		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao recuperar título consolidado');
		}
	
		if(pg_num_rows($resul) > 0 ){
			return  pg_fetch_all($resul);
		}
	
		return false;
	
	}
	
	
	/**
	 * 
	 * 
	 * @param Object $dados_titulo_pai
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarTituloPai($dados_titulo_pai){
	
		if(!is_object($dados_titulo_pai)){
			throw new Exception('Objeto iválido para atualizar o título pai.');
		}
	
		$sql = " UPDATE titulo_consolidado
		            SET titcvl_titulo          = $dados_titulo_pai->titcvl_titulo ,
		                titcvl_desconto        = $dados_titulo_pai->titcvl_desconto,
		                titcvl_juros           = $dados_titulo_pai->titcvl_juros,
		                titcvl_multa           = $dados_titulo_pai->titcvl_multa,
		                titcvl_recalculado      = $dados_titulo_pai->titcvl_recalculado,
		                titcvl_desc_cobranca    = $dados_titulo_pai->titcvl_desc_cobranca,
		                titcemissao            = NOW(),
		                titcdt_vencimento      = '$dados_titulo_pai->titcdt_vencimento',
		                titcobs_historico      = '$dados_titulo_pai->titcobs_historico',
		                titcusuoid_alteracao   = $dados_titulo_pai->titcusuoid
		          WHERE titcoid = $dados_titulo_pai->titulo_pai	  ";
			
		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar titulo pai.');
		}
	
		return true;
	
	}
	
	
	
	/**
	 * Insere um novo título pai com novos dados na tabela titulo_consolidado
	 *
	 * @param object $dados_titulo
	 * @return boolean
	 */
	public function setTituloPai($dados_titulo){
	
		if(!is_object($dados_titulo)){
			throw new Exception('Os dados para inserir o titulo pai do desconto da politica devem ser informados.');
		}
	
		$sql = "  INSERT INTO titulo_consolidado (
							titcclioid,
							titcvl_titulo,
							titcvl_desconto,
							titcvl_juros,
							titcvl_multa,
							titcvl_recalculado,
							titcvl_desc_cobranca,       
							titcformacobranca,
							titcemissao,
							titcdt_vencimento,
							titctittoid,
							titcobs_historico,
							titcusuoid_inclusao
						 )
						VALUES(
							$dados_titulo->titcclioid,
							$dados_titulo->titcvl_titulo ,
							$dados_titulo->titcvl_desconto,
							$dados_titulo->titcvl_juros,
							$dados_titulo->titcvl_multa,
							$dados_titulo->titcvl_recalculado,
							$dados_titulo->titcvl_desc_cobranca,
							$dados_titulo->titcformacobranca,
							NOW(),
							'$dados_titulo->titcdt_vencimento',
							$dados_titulo->titctittoid,
							'$dados_titulo->titcobs_historico',
							$dados_titulo->titcusuoid
								
							) RETURNING titcoid   ";
	
		
		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao inserir titulo pai do desconto da politica.');
		}
	
		if(pg_num_rows($resul) > 0 ){
	
			$res = pg_fetch_all($resul);
	
			return $res[0]['titcoid'];
		}
	
		return false;
	
	}
	
	
	/**
	 * Insere log da politica de desconto aplicada
	 *
	 * @param OBJECT $dados_log
	 * @throws Exception
	 * @return boolean
	 */
	function setLogPoliticaDesconto($dados_log){
	
		if(!is_object($dados_log)){
			throw new Exception('O dados para inserir o log da politica de desconto devem ser informados.');
		}
	
		$sql = "  INSERT INTO titulo_politica_desconto(
							  tpdusuoid_cadastro,
							  tpdtitcoid,
							  tpddt_vencimento,
							  tpdvlr_cobrado,
							  tpddesc_aplicado
							)
							VALUES(
							$dados_log->tpdusuoid_cadastro,
							$dados_log->tpdtitcoid,
							'$dados_log->tpddt_vencimento',
							$dados_log->tpdvlr_cobrado,
							$dados_log->tpddesc_aplicado
							)  ";
	
		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao inserir log da politica de desconto');
		}
	
		return true;
	
	}
	
	
	public function pesquisarHistoricoRecalculo($id_titulo){
		
		$sql = "SELECT tpddt_vencimento, tpddesc_aplicado, titcformacobranca FROM titulo_politica_desconto
						INNER JOIN titulo ON tpdtitcoid = tittitcoid
						INNER JOIN titulo_consolidado ON titcoid = tpdtitcoid
						WHERE titoid IN ($id_titulo)
						LIMIT 1";
		
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception('ERRO: <b>Falha ao buscar dados do(s) título(s).</b>');
		}
		
		$rs = pg_fetch_all($rs);
		return $rs[0];
	}
	
	/**
	 * inicia transação com o BD
	 */
	public function begin()	{
		$rs = pg_query($this->conn, "BEGIN;");
	}
	
	/**
	 * confirma alterações no BD
	 */
	public function commit(){
		$rs = pg_query($this->conn, "COMMIT;");
	}
	
	/**
	 * desfaz alterações no BD
	 */
	public function rollback(){
		$rs = pg_query($this->conn, "ROLLBACK;");
	}
	

}


?>