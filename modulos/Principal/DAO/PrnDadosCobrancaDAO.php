<?php
/**
 * 
 * Classe de persistência dos dados de cobrança do cliente
 * 
 * @file PrnDadosCobrancaDAO.php
 * @author marcioferreira
 * @version 05/03/2013 10:56:02
 * @since 05/03/2013 10:56:02
 * @package SASCAR PrnDadosCobrancaDAO.php 
 */
class PrnDadosCobrancaDAO {

	private $conn;
	
	
	/**
	 * Construtor
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function __construct($conn) {
	
		$this->conn = $conn;
	}


	/**
	* Atualiza o endereço de cobranca relacionado ao cliente
	*
	* @autor Renato Teixeira Bueno
	* @email renato.bueno@meta.com.br
	*/
	public function atualizarEnderecoCobranca($id_cliente, $campos) {

		try{
			$sql =" UPDATE
						endereco
					SET
						$campos
					WHERE
					endoid  = (	SELECT
									cliend_cobr
								FROM endereco
								INNER JOIN clientes on endoid = cliend_cobr
								WHERE clioid = $id_cliente ) ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao atualizar endereço de cobrança.</b>');
			}
			
			return true;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	
	/**
	 * @see Inseri o endereço de cobranca e relaciona ao cliente
	 *
	 * @autor Alexandre Marcelo Reczcki
	 * @email alexandre.reczcki@sascar.com.br
	 *
	 * @example $prnDadosCobrancaDAO->inserirEnderecoCobranca($dados->id_cliente, $campos, $values)
	 * 
	 * @param int $clioid
	 * @param string $campos
	 * A String devem seguir o seguinte formato:
	 * endno_numero, endno_cep, endcomplemento, endlogradouro, endcidade, endbairro, endemail, endpaisoid, endestoid, enduf, endddd, endfone
	 * 
	 * @param string $values
	 * A String devem seguir o seguinte formato:
	 * '12', '83050638', 'CASA', 'VINTE E CINCO DE DEZEMBRO', 'SAO JOSE DOS PINHAIS', 'PARQUE DA FONTE', 'teste@teste.com.br', '1', '16', 'PR', '41', '123456' 
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function inserirEnderecoCobranca($clioid, $campos, $values) {
	
		try{
			
			$sqlInsert = "INSERT INTO endereco (
								$campos
							)
							VALUES (
								$values
							)
						RETURNING endoid; ";
			;
			if (!$res_end = pg_query($this->conn, $sqlInsert)) {
				throw new Exception('ERRO: <b>Falha ao inserir endereço de cobrança.</b>');
			}
						
			if($res_end){
				$res_end = pg_fetch_result($res_end,0,'endoid');
				 
				/** Vincular o Endereço com o Cliente */
				$sqlUpdateCliente = "UPDATE clientes SET cliend_cobr = $res_end WHERE clioid = $clioid;";
				 
				if (!pg_query($this->conn, $sqlUpdateCliente)){
					throw new Exception('ERRO: <b>Falha ao inserir endereço de cobrança.</b>');
				}
			}
			
			return true;
	
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	public function getEnderecoCobrancaPorCliente($clioid){
		try{
		
			$sqlSelect = "SELECT cliend_cobr FROM clientes WHERE clioid = $clioid;";
			
			$rs = pg_query($this->conn, $sqlSelect);
			
			$rs = pg_query($this->conn, $sqlSelect);
			$cliend_cobr = pg_fetch_result($rs,0,'cliend_cobr');
			
			/** SE EXISTIR ENDEREÇO DE COBRANÇA - FAZ O UPDATE */
			if (isset($cliend_cobr)){
				return $cliend_cobr;
			}
			
			return null;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
		
	}
	
	
	/**
	* Método que busca e popula a combo de países
	*
	* @autor Willian Ouchi
	*/
	public function getDadosPaises() {

		try{
			$sql =" SELECT paisoid, paisnome
					FROM paises
					WHERE paisexclusao IS NULL ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar lista de países.</b>');
			}
	
			$resultado = array();
	
			$cont = 0;
			while ($rpaises = pg_fetch_assoc($rs)) {
	
				$resultado[$cont]['paisoid'] = $rpaises['paisoid'];
				$resultado[$cont]['paisnome'] = utf8_encode($rpaises['paisnome']);
				$cont++;
			}
	
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Método que retorna um estado ou uma listagem de estados
	 *
	 * @autor Willian Ouchi
	 * */
	public function getDadosEstados($pais = null, $estado = null) {

		try{
			$resultado = "";
			$where = "";
	
			if ($pais || $estado) {
	
				if ($estado){
					$where .= " AND estoid = $estado ";
				}
	
				$sql =" SELECT	estoid,	estuf
						FROM estado
						WHERE estpaisoid = $pais
						$where ";
	
				if (!$rs = pg_query($this->conn, $sql)) {
					throw new Exception('ERRO: <b>Falha ao retornar estado(s).</b>');
				}
	
				$resultado = array();
	
				$cont = 0;
				while ($rEstados = pg_fetch_assoc($rs)) {
	
					$resultado[$cont]['estoid'] = $rEstados['estoid'];
					$resultado[$cont]['estuf']  = utf8_encode($rEstados['estuf']);
					$cont++;
				}
			}
	
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Método que retorna uma cidade ou uma listagem de cidades
	 * @autor Willian Ouchi
	 * */
	public function getDadosCidades($estado = null){

		try{
			$resultado = "";
			$where = "";
	
			if ($estado){
				$where .= " AND clcestoid = $estado";
			}
	
			$sql =" SELECT clcnome
					FROM correios_localidades
					WHERE clcnome IS NOT NULL
					$where
					ORDER BY clcnome ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar cidades.</b>');
			}
	
			$resultado = array();
	
			$cont = 0;
			while ($rCidades = pg_fetch_assoc($rs)) {
	
				$resultado[$cont]['clcnome']  = utf8_encode($rCidades['clcnome']);
				$cont++;
			}
	
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Método que retorna uma cidade ou uma listagem de cidades
	 * @autor Willian Ouchi
	 * */
	public function getDadosBairros($estado = null, $cidade = null){

		try{
			$resultado = "";
			$where = "";
	
			if ($estado){
				$where .= " AND clcestoid = $estado";
			}
	
			if ($cidade){
				$where .= " AND clcnome = '$cidade'";
			}
	
			$sql =" SELECT cbaoid, cbanome
					FROM correios_localidades
					INNER JOIN correios_bairros ON clcoid = cbaclcoid
					WHERE cbanome IS NOT NULL
					$where
					ORDER BY cbanome ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar bairro(s).</b>');
			}
	
			$resultado = array();
	
			$cont = 0;
			while ($rBairros = pg_fetch_assoc($rs)) {
	
				$resultado[$cont]['cbaoid'] = $rBairros['cbaoid'];
				$resultado[$cont]['cbanome']  = utf8_encode($rBairros['cbanome']);
				$cont++;
			}
	
			return $resultado;
			
		}catch (Exception $e){
			return $e->getMessage();
		}
	}


	/**
	 * Método que retorna um bairro informado
	 * @autor Willian Ouchi
	 * */
	public function getBairro($bairro){

		try{
			if ($bairro){
	
				$sql =" SELECT upper(cbanome) AS cbanome
						FROM correios_bairros
						WHERE cbaoid = $bairro ";
	
				if (!$rs = pg_query($this->conn, $sql)) {
					throw new Exception('ERRO: <b>Falha ao retornar bairro informado.</b>');
				}
				$rBairro = pg_fetch_assoc($rs);
			}
				
			return $rBairro['cbanome'];
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Método que retorna dados de endereço do cep informado
	 * @autor Willian Ouchi
	 * */
	public function getDadosEndereco($cep){

		try{		
			unset($resultado);
			unset($where);
	
			if ($cep){
	
				$sql =" SELECT
							clcestoid AS uf,
							upper(clcuf_sg) AS uf_sigla,
							upper(clcnome) AS cidade,
							upper(clgnome) AS logradouro,
							clgcbaoid_ini AS bairro_ini,
							clgcbaoid_fim AS bairro_fim
						FROM correios_logradouros
						INNER JOIN correios_localidades ON clgclcoid = clcoid
						WHERE clgcep = '$cep'	";
	
				if (!$rs = pg_query($this->conn, $sql)) {
					throw new Exception('ERRO: <b>Falha ao consultar endereço do cep informado.</b>');
				}
	
				if (pg_num_rows($rs) > 0){
	
					$resultado = array();
	
					$rBairros = pg_fetch_assoc($rs);
	
					$resultado['uf'] = $rBairros['uf'];
					$resultado['uf_sigla'] = $rBairros['uf_sigla'];
					$resultado['cidade']  = utf8_encode(strtoupper($rBairros['cidade']));
					$resultado['bairro_ini'] = utf8_encode(strtoupper($this->getBairro($rBairros['bairro_ini'])));
					$resultado['bairro_fim']  = utf8_encode(strtoupper($this->getBairro($rBairros['bairro_fim'])));
					$resultado['logradouro']  = utf8_encode(strtoupper($rBairros['logradouro']));
	
				}else{
					$resultado = false;
	
				}
			}
				
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Método que efetua a busca a forma de cobranca, banco, agencia e conta corrente anteriores do cliente
	*
	* @autor Renato Teixeira Bueno
	* @email renato.bueno@meta.com.br
	*/
	public function getFormaCobrancaAnterior($id_cliente) {

		try{
			$sql = "SELECT
						clicformacobranca as forma_cobranca,
						forcnome as descricao_forma_cobranca,
						forccfbbanco as banco,
						bannome as nome_banco,
						clicagencia as agencia,
						clicconta as conta_corrente,
						forcdebito_conta as debito_em_conta,
						clicsituacao_visualizacao as situacao_visualizacao
					FROM cliente_cobranca
					INNER JOIN forma_cobranca ON forcoid = clicformacobranca
					LEFT JOIN banco ON bancodigo = forccfbbanco
					WHERE 
					clicclioid = $id_cliente
					AND clicexclusao IS NULL 
					ORDER BY clicoid DESC
					LIMIT 1	";
				
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao consultar forma de cobrança anterior do cliente.</b>');
			}
	
			if (pg_num_rows($rs) > 0) {
				return pg_fetch_object($rs);
			}
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}

	
	/**
	 * Busca banco por forma de cobrança
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function getBancoPorFormaCobranca($forcoid) {

		try{
			$sql =" SELECT
						bancodigo as id_banco,
						bannome as banco
					FROM forma_cobranca
					INNER JOIN banco ON bancodigo = forccfbbanco
					WHERE forcoid = $forcoid ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar bancos da forma de cobrança informada.</b>');
			}
	
			$resultado = array();
	
			if(pg_num_rows($rs) > 0){
	
				$cont = 0;
				while ($rbanco = pg_fetch_assoc($rs)) {
					$resultado[$cont]['id_banco'] = utf8_encode($rbanco['id_banco']);
					$resultado[$cont]['nome_banco'] = utf8_encode($rbanco['banco']);
					$cont++;
				}
			}
			
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Busca os dados da forma de cobranca
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function getDadosFormaCobranca($pforma_cobranca = null, $pid_proposta = null) {

		try{		
			if ($pforma_cobranca) {
				$where = " AND forcoid = $pforma_cobranca";
			}
	
			$order = " ORDER BY descricao_forma_cobranca ";
	
			if($pid_proposta){
				$union .= " UNION
							SELECT
								ppagforcoid as forcoid,
								null as debito_em_conta,
								'' as descricao_forma_cobranca,
								ppagbancodigo as banco
							FROM proposta_pagamento
							WHERE ppagprpoid = $pid_proposta";
				$order = "" ;
			}
	
			$sql = "SELECT
						forcoid,
						forcdebito_conta as debito_em_conta,
						forcnome as descricao_forma_cobranca,
						forccfbbanco as banco
					FROM forma_cobranca
					WHERE forcexclusao IS NULL
					$where
					$order
					$union ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar dados da forma de cobrança informada.</b>');
			}
	
			if (pg_num_rows($rs) > 0) {
				return pg_fetch_object($rs);
			}
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}

	
	/**
	 * Retorna a forma de cobrança atual, se houver, do cliente selecionado
	 * @return array $formaPagamentoAtual
	 */
	public function getFormaCobrancaAtual($clioid){

		if($clioid > 0){
			try{
				$sql =" SELECT forcoid, forcnome, forccobranca_cartao_credito, forcdebito_conta, forccobranca_registrada,
							CASE WHEN cccativo = 'f' THEN ''
							ELSE cccsufixo
							END as cccsufixo,
							cccativo,
							cccnome_cartao
						FROM cliente_cobranca
						LEFT JOIN forma_cobranca ON forcoid = clicformacobranca
						LEFT JOIN cliente_cobranca_credito ON cccclioid = clicclioid
						WHERE clicclioid = $clioid
						AND clicexclusao IS NULL
						ORDER BY cccoid DESC
						LIMIT 1 ";
		
				if (!$rs = pg_query($this->conn, $sql)) {
					throw new Exception('ERRO: <b>Falha ao retornar forma de cobrança atual.</b>');
				}
		
				if (pg_num_rows($rs) > 0) {
					return pg_fetch_object($rs);
				}

			}catch (Exception $e){
				return $e->getMessage();
			}
		}else{
			echo 'ERRO: <b>Falta iformar o código do cliente.</b>';
		}	
	}
	
	
	/**
	 * Retorna a data de cobrança atual do cliente 
	 * @return object
	 */
	public function getDataCobrancaCliente($clioid){

		try{
			$sql =" SELECT clidia_vcto
					FROM clientes 
					WHERE clioid = $clioid	";

			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar data de cobranca do cliente.</b>');
			}

			if (pg_num_rows($rs) > 0) {
				return pg_fetch_object($rs);
			}

		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Retorna as formas de pagamento disponíveis
	 * @return Ambigous <multitype:, string>
	 */
	public function getFormaCobranca()
	{
		try{
			$sql =" SELECT forcoid, forcnome, 0 AS accrecodautorizadora
					FROM forma_cobranca
					WHERE forcvenda IS TRUE
					AND forcexclusao IS NULL
					AND forccobranca_cartao_credito IS FALSE
					
					UNION
					
					SELECT forcoid, forcnome, forcaccoid
					FROM forma_cobranca
					WHERE forcvenda IS TRUE
					AND forcexclusao IS NULL
					AND forccobranca_cartao_credito IS TRUE
					AND forcaccoid IS NOT NULL
					ORDER BY forcnome; ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar formas de cobrança.</b>');
			}
	
			$formaPagamento = array();
	
			$cont = 0;
			while ($rsForma = pg_fetch_assoc($rs)) {
	
				$formaPagamento[$cont]['forcoid']              = utf8_encode($rsForma['forcoid']);
				$formaPagamento[$cont]['forcnome']             = utf8_encode($rsForma['forcnome']);
				$formaPagamento[$cont]['accrecodautorizadora'] = utf8_encode($rsForma['accrecodautorizadora']);
	
				$cont++;
			}
				
			return $formaPagamento;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}

	/**
	* Atualiza dados da cobrança relacionada ao cliente
	*
	* @autor Renato Teixeira Bueno
	* @email renato.bueno@meta.com.br
	*/
	public function atualizarCobranca($dadosConfirma, $agencia, $conta_corrente) {
		
		try{
		
			$possui_servico_sasgc = false;
			
			$sql_verifica_sasgc = "	SELECT clicoid
									FROM cliente_cobranca 
									WHERE clicclioid=$dadosConfirma->id_cliente
									AND clicvisualizacao_sasgc IS TRUE;";
			$res_verifica_sasgc = pg_query ($this->conn,$sql_verifica_sasgc);	

			$lin_verifica_sasgc = pg_num_rows($res_verifica_sasgc);
			
			if($lin_verifica_sasgc>0){
				$possui_servico_sasgc = true;
			}		
		
			//seta data da exclusão(exclui) da forma de pagamento atual
			$sqlUp =" UPDATE
					  	cliente_cobranca
					  SET
					  	clicexclusao = NOW()
					  WHERE  clicclioid = $dadosConfirma->id_cliente
					  AND clicexclusao IS NULL ";
				
			if (!pg_query ($this->conn, $sqlUp)) {
				throw new Exception('ERRO: <b>Falha ao excluir forma de cobrança do cliente.</b>');
			}
	
			if($possui_servico_sasgc == true){
	
				//insere um novo registro com a nova forma de cobrança
				$sqlInsert = " INSERT INTO
									cliente_cobranca
										(clicclioid,
										cliccadastro,
										clicusuoid,
										clicformacobranca,
										clicagencia,
										clicconta,
										clicvisualizacao_sasgc,
										clictipo,
										clicdia_mes, 
										clicdia_semana,
										clicdias_uteis,
										clictitular_conta,
										clicdias_prazo,
										clicsituacao_visualizacao )
								VALUES(
										$dadosConfirma->id_cliente,
										'NOW()',
										$dadosConfirma->id_usuario,
										$dadosConfirma->forma_cobranca_posterior,
										$agencia,
										$conta_corrente,
										't',
										'{$dadosConfirma->tipoConta}',
										$dadosConfirma->diaMes,
										'{$dadosConfirma->diaSemana}',
										'{$dadosConfirma->diasUteis}',
										'{$dadosConfirma->nomeTitular}',
										$dadosConfirma->diasPrazo,
										'{$dadosConfirma->visualizacao_anterior}') ";
									
			}else{
									
				//insere um novo registro com a nova forma de cobrança
				$sqlInsert = " INSERT INTO
									cliente_cobranca
										(clicclioid,
										cliccadastro,
										clicusuoid,
										clicformacobranca,
										clicagencia,
										clicconta,
										clictipo,
										clicdia_mes, 
										clicdia_semana,
										clicdias_uteis,
										clictitular_conta,
										clicdias_prazo,
										clicsituacao_visualizacao )
								VALUES(
										$dadosConfirma->id_cliente,
										'NOW()',
										$dadosConfirma->id_usuario,
										$dadosConfirma->forma_cobranca_posterior,
										$agencia,
										$conta_corrente,
										'{$dadosConfirma->tipoConta}',
										$dadosConfirma->diaMes,
										'{$dadosConfirma->diaSemana}',
										'{$dadosConfirma->diasUteis}',
										'{$dadosConfirma->nomeTitular}',
										$dadosConfirma->diasPrazo,
										'{$dadosConfirma->visualizacao_anterior}' ) ";									
									
									
			}

			if (!pg_query ($this->conn, $sqlInsert)) {
				throw new Exception('ERRO: <b>Falha ao inserir forma de cobrança do cliente.</b>');
			}
			
			//busca a data pelo id informado pelo usuário
			$diaCobranca = $this->getDiaCobranca("", $dadosConfirma->idDataVencimento);
			
			//altera a forma de pagamento e a data de cobrança do cliente
			$sqlCli = " UPDATE
						  clientes
						SET
						  cliformacobranca = $dadosConfirma->forma_cobranca_posterior,
						  clidia_vcto = ".trim($diaCobranca[0]['dia_pagamento']).",
						  cliusuoid_alteracao = ".$dadosConfirma->id_usuario."
						WHERE  clioid = $dadosConfirma->id_cliente ";
			
			if (!pg_query ($this->conn, $sqlCli)) {
				throw new Exception('ERRO: <b>Falha ao atualizar dados de cobranca do cliente.</b>');
			}

			/* BEGIN DUM 81608 - 002556*/
			if (isset($dadosConfirma->conreajuste) && trim($dadosConfirma->conreajuste) != '') {

				$sqlUpdateContrato  =  "UPDATE
											contrato
								  SET 
								  			conreajuste = " . $dadosConfirma->conreajuste . "
								  WHERE
								  			connumero = " . $dadosConfirma->contrato;

				if (!pg_query ($this->conn, $sqlUpdateContrato)) {
					throw new Exception('ERRO: <b>Falha ao atualizar reajuste do contrato.</b>');
				}				
			}
			/* END DUM 81608 - 002556*/
			
			
			return true;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Retorna os dias disponíveis para pagamento
	 * @return array - Array de datas
	 */
	public function getDiaCobranca($exibeDataVencimento = null, $codDiaVencimento = null, $dataVencimento = null){

		try{
		
			$formaCobranca = array();
	
			$sql = "SELECT cdvoid AS codigo, cdvdia AS dia_pagamento
					FROM cliente_dia_vcto
					WHERE cdvdt_exclusao IS NULL ";
	
			if($exibeDataVencimento->tipo === 'credito'){
				$sql .= " AND cdvdia = 16 ";
			}
	
			if($exibeDataVencimento->tipo === 'debito'){
				$sql .= " AND cdvdia = 7 ";
			}
			
			if($codDiaVencimento != 'null' && !empty($codDiaVencimento)){
				$sql .= " AND cdvoid = $codDiaVencimento ";
			}
			
			if($dataVencimento != 'null' && !empty($dataVencimento)){
				$sql .= " AND cdvdia = ".trim($dataVencimento)." ";
			}
	
			$sql .= " ORDER BY cdvdia";
				
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao retornar dias disponíveis para pagamento.</b>');
			}
	
			$count = 0;
			while ($row = pg_fetch_assoc($rs)){
				$formaCobranca[$count]['codigo']        = $row['codigo'];
				$formaCobranca[$count]['dia_pagamento'] = $row['dia_pagamento'];
				$count++;
			}
	
			return $formaCobranca;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	

	/**
	 * retorna dados tela cadastro cliente cargo tracck
	 */
	public function getDadosClienteCargoTracck($dados){
		try{
			$sql = "SELECT
    					clictipo,
    					clicdia_mes,
    					clicdia_semana,
    					clicdias_uteis,
    					clictitular_conta,
    					clicdias_prazo
    				FROM
    					cliente_cobranca
    				WHERE
    					clicclioid = ".$dados->id_cliente."
    					and clicexclusao is null
					";
			 
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao buscar dados do cliente.</b>');
			}
			 
			return pg_fetch_object($rs);
			 
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
}
//fim arquivo
?>