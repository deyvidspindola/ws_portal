<?php

class RelInsucessoDiscadorDAO {
	private $conn;
	
	public function RelInsucessoDiscadorDAO($conn) {
		$this->conn = $conn;
	}
	
	public function insereHistorico($ligacaoTipo,$contratoNumero,$cd_usuario,$mensagemHistorico,$osID) {
		if ($ligacaoTipo == "C"){
			$sqlHistorico = "
			INSERT INTO
				historico_termo".($contratoNumero % 10)."
			(
				hitconnumero,
				hitusuoid,
				hitobs,
				hitdt_acionamento
			)
			VALUES
			(
				".$contratoNumero.",
				".$cd_usuario.",
				'".$mensagemHistorico."',
				NOW()
			)
		";
		}
		elseif ($ligacaoTipo == "S"){
			$sqlHistorico = "
			INSERT INTO
				ordem_situacao
			(
				orsordoid,
				orsusuoid,
				orssituacao,
				orsdt_situacao,
				orsstatus
			)
			VALUES
			(
				".$osID.",
				".$cd_usuario.",
				'".$mensagemHistorico."',
				NOW(),
				16
			)
		";
		}
		//echo "<pre>sqlHistorico: ".$sqlHistorico."</pre>";
		if ($sqlHistorico){
			if (!$qrHistorico = pg_query($this->conn, $sqlHistorico)) {
				throw new Exception("Erro ao inserir histórico");
			}
		}
	}
	
	public function atualizaLigacoesDiscador($updateCampos,$ligacaoID) {
		$sqlLigacoes = "
						UPDATE
							ligacoes_discador
						SET
							$updateCampos
						WHERE
							ligdoid = ".$ligacaoID;
		
		if (!$qrLigacoes = pg_query($this->conn, $sqlLigacoes)) {
			throw new Exception("Erro ao atualizar ligações discador");
		}
	}
	
	
	public function getTipoContrato(){

		try{
			
			$tipoContrato = array();

			$sql = " SELECT tpcoid, tpcdescricao
					   FROM tipo_contrato
					  WHERE tpcativo = 't'
				   ORDER BY tpcdescricao ";

			if (!$tipos = pg_query($this->conn, $sql)){
				throw new Exception("Erro ao pesquisar tipo de contrato");
			}

			if (pg_num_rows($tipos) > 0) {
				$tipoContrato = pg_fetch_all($tipos);
			}

			return $tipoContrato;
			
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	public function getTipoProposta(){
	
		try{
	
			$tipoProposta = array();
			
			$sql = " SELECT tppoid, tppdescricao, tppcodigo
					   FROM tipo_proposta
					  WHERE tppoid_supertipo IS NULL
				   ORDER BY tppdescricao ";
	
			if (!$tipos = pg_query($this->conn, $sql)){
				throw new Exception("Erro ao pesquisar tipo de proposta");
			}
	
			if (pg_num_rows($tipos) > 0) {
				$tipoProposta = pg_fetch_all($tipos);
			}
			
			return $tipoProposta;
				
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	public function getSubTipoProposta($tipoProposta){
		
		try{
			
			if ($tipoProposta == ""){
				throw new Exception("O tipo de proposta deve ser informado");
			}
			
			$tipoSubProposta = array();

			$sql = "  SELECT tppoid, tppdescricao, tppcodigo
					    FROM tipo_proposta
					   WHERE tppoid_supertipo IS NOT NULL
					     AND tppoid_supertipo = $tipoProposta
					ORDER BY tppdescricao ";

			if (!$tipos = pg_query($this->conn, $sql)){
				throw new Exception("Erro ao pesquisar tipo de proposta");
			}

			if (pg_num_rows($tipos) > 0) {
				$tipoSubProposta = pg_fetch_all($tipos);
			}

			return $tipoSubProposta;

		}catch(Exception $e){
			return $e->getMessage();
		}
	
	}
	
	
	public function gerarListagem($where, $count) {
		
		$insucessos = array();
		
		$sqlInsucessos = "
			SELECT
				ligdoid,
				CASE
					WHEN ligdtipo = 'C' THEN ligdconnumero
					WHEN ligdtipo = 'S' THEN ligdordoid
				END as ligdtipo,
				clinome,
				TO_CHAR(ligddt_ligacao,'DD/MM/YY') AS ligddt_ligacao,
				ldsdescricao,
				ligdcampanha,
				TO_CHAR(ligddt_envioemail,'DD/MM/YY') AS ligddt_envioemail,
				ligdtipo_envioemail,
				TO_CHAR(ligddt_enviosms,'DD/MM/YY') AS ligddt_enviosms,
				ligdtipo_enviosms,
				ligdconnumero,
				CASE
				   WHEN t2.tppoid_supertipo is null THEN t2.tppdescricao
				ELSE
				   (SELECT t1.tppdescricao
				    FROM tipo_proposta AS t1
				    WHERE t1.tppoid = t2.tppoid_supertipo)
				END AS tipo_proposta
				
			FROM
				ligacoes_discador AS ld
				INNER JOIN clientes ON ligdclioid = clioid
				INNER JOIN ligacoes_discador_status ON ligdldsoid = ldsoid
				INNER JOIN proposta ON prptermo = ligdconnumero
				INNER JOIN contrato ON connumero = ligdconnumero
				INNER JOIN tipo_contrato ON conno_tipo = tpcoid
				INNER JOIN tipo_proposta AS t2 ON t2.tppoid = prptppoid
				
			WHERE
				ligdoid IN (
					SELECT
						MAX(ligdoid)
					FROM
						ligacoes_discador AS ld
						INNER JOIN clientes ON ligdclioid = clioid
						INNER JOIN ligacoes_discador_status ON ligdldsoid = ldsoid
						INNER JOIN proposta ON prptermo = ligdconnumero
						INNER JOIN contrato ON connumero = ligdconnumero
						INNER JOIN tipo_contrato ON conno_tipo = tpcoid
						INNER JOIN tipo_proposta AS t2 ON t2.tppoid = prptppoid
					WHERE
						ldsdt_exclusao IS NULL
						AND ldsinsucesso = TRUE
						".$where."
					GROUP BY
						ligdconnumero
				)
			ORDER BY
				ligddt_ligacao, ligdoid
			--LIMIT 10
			--OFFSET $count
		";
		
		//echo "<pre>sqlInsucessos: ".$sqlInsucessos."</pre>";
			
		if (!$qrInsucessos = pg_query($this->conn, $sqlInsucessos)){
			throw new Exception("Erro ao pesquisar");
		}
		
		if (pg_num_rows($qrInsucessos)>0) $insucessos = pg_fetch_all($qrInsucessos);
		
		return $insucessos;
		
	}
	
	public function gerarContagem($where) {
		$contagem = array();
		
		$sqlContagem = "
				SELECT 
                	COUNT(ligdoid) AS contador, 
                	ligdconnumero 
				FROM 
                	ligacoes_discador AS ld
        			INNER JOIN clientes ON ligdclioid = clioid
                	INNER JOIN ligacoes_discador_status AS lds ON ligdldsoid = ldsoid
                	INNER JOIN proposta ON prptermo = ligdconnumero
					INNER JOIN contrato ON connumero = ligdconnumero
					INNER JOIN tipo_contrato ON conno_tipo = tpcoid
					INNER JOIN tipo_proposta AS t2 ON t2.tppoid = prptppoid
				WHERE  
                	ldsdt_exclusao IS NULL 
                	AND ldsinsucesso = TRUE 
                	$where               
				GROUP BY 
       				ligdconnumero;
				";
		
		//echo "<pre>sqlContagem: ".$sqlContagem."</pre>";
		
		if (!$qrContagem = pg_query($this->conn, $sqlContagem)){
			throw new Exception("Erro ao pesquisar contadores");
		}
		
		while ($arrcontagem = pg_fetch_assoc($qrContagem)) {
			$contagem[$arrcontagem['ligdconnumero']] = $arrcontagem['contador'];
		}	
		
		//echo "<pre>".$sqlContagem."</pre>";
		
		return $contagem;
	}
	
	public function retornaNumeroRegistros($where) {
			
		$sqlContagem = "
		SELECT
			COUNT(ligdoid) AS contador,
			ligdconnumero
		FROM
			ligacoes_discador AS ld
			INNER JOIN clientes ON ligdclioid = clioid
			INNER JOIN ligacoes_discador_status AS lds ON ligdldsoid = ldsoid
			INNER JOIN proposta ON prptermo = ligdconnumero
			INNER JOIN contrato ON connumero = ligdconnumero
			INNER JOIN tipo_contrato ON conno_tipo = tpcoid
			INNER JOIN tipo_proposta AS t2 ON t2.tppoid = prptppoid
		WHERE
			ldsdt_exclusao IS NULL
			AND ldsinsucesso = TRUE
			$where
		GROUP BY
			ligdconnumero;
		";
	
		//echo "<pre>sqlContagem: ".$sqlContagem."</pre>";
	
		if (!$qrContagem = pg_query($this->conn, $sqlContagem)){
			throw new Exception("Erro ao retornar numero registros");
		}
	
		$contagem = pg_num_rows($qrContagem);
	
		//echo "<pre>".$sqlContagem."</pre>";
	
		return $contagem;
	}

	public function atribTelefone($id) {
		
		$sqlTelefone = "
				SELECT
					clinome,
					ligdtipo,
					ligdconnumero,
					ligdordoid,
					CASE
						WHEN clitipo = 'F' THEN clifone_cel
						WHEN clitipo = 'J' THEN tctno_ddd_cel||tctno_fone_cel
					END AS telefone_celular,
					(SELECT veiplaca FROM veiculo INNER JOIN contrato ON veioid=conveioid WHERE connumero = ligdconnumero) AS placa
				FROM
					ligacoes_discador
					INNER JOIN clientes ON ligdclioid = clioid
					LEFT JOIN contrato ON conclioid = clioid
					LEFT JOIN telefone_contato ON tctconnumero = connumero
				WHERE
					ligdoid = '".$id."'
					AND
					(
						(
						clitipo = 'J'
						AND tctno_fone_cel IS NOT NULL
						AND char_length(tctno_ddd_cel||tctno_fone_cel) >= 10
						)
					OR
						(
						clitipo = 'F'
						AND clifone_cel IS NOT NULL
						AND char_length(clifone_cel) >= 10
						)
					)
				ORDER BY
					tctdt_cadastro DESC
				LIMIT 1
			";
		if (!$qrTelefone = pg_query($this->conn, $sqlTelefone)) {
			throw new Exception ("Erro ao consultar telefone: $i");
		}
		
		$rsTelefone = pg_fetch_assoc($qrTelefone);
		
		return $rsTelefone;
	}

	public function atribCliente($id) {
		
		$sqlCliente = "
				SELECT
					clinome,
					cliemail,
					ligdtipo,
					ligdconnumero,
					ligdordoid,
					(SELECT veiplaca FROM veiculo INNER JOIN contrato ON veioid=conveioid WHERE connumero = ligdconnumero) AS placa
				FROM
					clientes
					INNER JOIN ligacoes_discador ON ligdclioid = clioid
				WHERE
					ligdoid = ".$id;
		
		if (!$qrCliente = pg_query($this->conn, $sqlCliente)) {
			throw new Exception ("Erro ao consultar cliente: $i");
		}
		
		$rsCliente = pg_fetch_assoc($qrCliente);
		
		return $rsCliente;
	}
		
	
	public function getCelularTesteSms(){
	
		try{
	
			$sql = "SELECT pcsidescricao, pcsioid
	  				FROM 
						parametros_configuracoes_sistemas, 
						parametros_configuracoes_sistemas_itens  
	 				WHERE 
						pcsoid = pcsipcsoid
				    AND pcsdt_exclusao is null
					AND pcsidt_exclusao is null
					AND pcsipcsoid = 'PARAMETROSAMBIENTETESTE'
					AND pcsioid = 'CELULAR' 
					LIMIT 1 ";
		
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception ("Falha ao recuperar celular de teste ");
			}
		
			if(count($result) > 0){
				return pg_fetch_object($result);
			}
				
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	public function getEmailTeste(){
	
		try{
	
			$sql = "SELECT pcsidescricao, pcsioid
	  				FROM
						parametros_configuracoes_sistemas,
						parametros_configuracoes_sistemas_itens
	 				WHERE
						pcsoid = pcsipcsoid
				    AND pcsdt_exclusao is null
					AND pcsidt_exclusao is null
					AND pcsipcsoid = 'PARAMETROSAMBIENTETESTE'
					AND pcsioid = 'EMAIL'
					LIMIT 1 ";
	
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception ("Falha ao recuperar email de teste ");
			}
	
			if(count($result) > 0){
				return pg_fetch_object($result);
			}
	
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera o endereço de email do remetente de acordo a proposta
	 * 
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 13/06/2013
	 * 
	 * @param int $tipoProposta
	 * @return Object
	 */
	public function getDadosEmpresaProposta($tipoProposta){
	
		try{
	
			$sql =" SELECT stpsrvlocalizador AS servidor, 
						   srvremetente_email AS email_remetente, 
						   UPPER(srvremetente_nome) AS nome_remetente,
						   srvcopia_oculta_email AS copia_oculta
					  FROM servidor_tipo_proposta, servidor_email
					 WHERE stpsrvlocalizador = srvlocalizador
					   AND stptppoid = $tipoProposta 
					 LIMIT 1";
	
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception ("Falha ao recuperar email do servidor ");
			}
			
			if(count($result) > 0){
				return pg_fetch_object($result);
			}
			
			return false;
			
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera o endereço de email padrao 
	 *
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 14/06/2013
	 *
	 * @return Object
	 */
	public function getDadosEmpresaPadrao(){

		try{
	
			$sqlPadrao="SELECT srvlocalizador AS servidor,
							   srvremetente_email AS email_remetente,
							   UPPER(srvremetente_nome) AS nome_remetente,
							   srvcopia_oculta_email AS copia_oculta
						  FROM servidor_email
						 WHERE srvpadrao IS TRUE 
						 LIMIT 1";


			if (!$resultPadrao = pg_query($this->conn, $sqlPadrao)) {
				throw new Exception ("Falha ao recuperar email padrao do servidor ");
			}
				
			if(count($resultPadrao) > 0){
				return pg_fetch_object($resultPadrao);
			}
				
			return false;

		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	public function buscaDadosInsucesso($contrato) {
		$sql = "SELECT MAX(TO_CHAR(ligddt_ligacao,'DD/MM/YY')) AS ultimo_contato,
                       count(ligdconnumero) AS qtd_insucessos 
                    FROM ligacoes_discador
            	 	INNER JOIN ligacoes_discador_status
                    	ON ligdldsoid = ldsoid
                 	WHERE ldsdt_exclusao IS NULL 
                   	AND ldsinsucesso = TRUE 
                    AND ligdconnumero = '".$contrato."'";

        if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao recuperar dados de insucesso ");
		}
			
		if(count($result) > 0){
			return pg_fetch_array($result);
		}
			
		return false;
	}
}