<?php

/**
 * 
 * 
 * @file FinRetornoCobrancaRegistradaDAO.php
 * @author marcioferreira
 * @version 19/09/2014 15:28:16
 * @since 19/09/2014 15:28:16
 * @package SASCAR FinRetornoCobrancaRegistradaDAO.php 
 */


class FinRetornoCobrancaRegistradaDAO{
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	public $conn;
	
	public $usuarioID ;
	
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn){
		
		$this->conn = $conn;
		if(empty($this->usuarioID)){
		   $this->usuarioID = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid']: NULL;
		}
		
		$this->data = date('d-m-Y');
	}
	
	
	/**
	 * 
	 * 
	 * @throws exception
	 * @return object|boolean
	 */
	public function getFormaCobranca(){
		
		$sql = " SELECT forcoid,
				        forcnome 
				   FROM forma_cobranca 
				  WHERE forcexclusao IS NULL 
				    AND forccobranca_registrada IS TRUE 
				    AND forcoid NOT IN (84);";
			
		if(!$res = pg_query($this->conn,$sql)){
			throw new exception('Falha ao busca forma de cobrança.');
		}
			
		if(pg_num_rows($res) > 0){
			return pg_fetch_all($res);
		}
		
		return false;
				
	}
	
	
	/**
	 * 
	 * 
	 * @param unknown $forma_cobranca
	 * @throws exception
	 * @return string
	 */
	public function getLayoutBanco($forma_cobranca){
	
	
		if(empty($forma_cobranca)){
			throw new exception('A forma de cobrança deve ser informada.');
		}
		 
		// Buscando a descrição da forma de cobrança, em cima dessa descrição será feito o layout
		$sql = " SELECT forccfbbanco
				   FROM forma_cobranca
			 INNER JOIN config_banco ON cfbbanco=forccfbbanco
			  	  WHERE forcoid = $forma_cobranca ";

		
		if(!$res = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possível buscar código do banco');
		}
		 
		$forccfbbanco = pg_fetch_result($res,0,'forccfbbanco');
		 
		return $forccfbbanco;

	}
	
	/**
	 * 
	 * 
	 * @param int $num_titulo
	 * @throws exception
	 * @return string
	 */
	public function recuperarNomeCliente($num_titulo){
			
		//Busca Código do Cliente da tabela Titulo
		$sql = "SELECT cli.clinome FROM clientes cli
				  JOIN titulo tit ON tit.titclioid = cli.clioid
				 WHERE tit.titoid=" . $num_titulo;

		if(!$rs = pg_query($this->conn, $sql)){
			throw new exception('Erro: Não foi possível buscar código do cliente na tabela titulo');
		}
		
		
		//Se Titulo não Existe na tabela TITULO
		if(pg_num_rows($rs) <= 0 ){
			//Busca Código do Cliente da tabela Titulo_retencao
			$sql = " SELECT cli.clinome FROM clientes cli
		               JOIN titulo_retencao tit ON tit.titclioid = cli.clioid
					  WHERE tit.titoid=" . $num_titulo;
				
			if(!$rs = pg_query($this->conn, $sql)){
				throw new exception('Erro: Não foi possível buscar código do cliente na tabela titulo_retencao');
			}
			
			//Se Titulo não existe na tabela TITULO_RETENCAO
			if(pg_num_rows($rs) <= 0 ){
				$nome_cliente = "";
				//Se Titulo existe na tabela TITULO_RETENCAO
			}else{
				$resultado = pg_fetch_row($rs);
				$nome_cliente = utf8_encode($resultado[0]);
			}
			//Se Titulo existe na tabela TITULO
		}else{
			$resultado = pg_fetch_row($rs);
			$nome_cliente = utf8_encode($resultado[0]);
		}
		
		return $nome_cliente;
	
	}
	
	
	/**
	 * Pesquisa dados de um titulo na tabela titulo_retencao
	 * 
	 * @param INT $titoid
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getTituloRetencao($titoid = '', $num_registro = '', $num_reg_banco =  false){
			
		$sql = " SELECT titoid, 
		                titdt_credito, 
 		                titnumero_registro_banco 
		           FROM titulo_retencao 
		          WHERE true ";
		
		if($num_registro != ''){
			$sql .= " AND titnumero_registro_banco = $num_registro ";
		}
		
		if($titoid != ''){
			$sql .= " AND titoid = $titoid  ";
		}
		
		if($num_reg_banco){
			$sql .= " AND titnumero_registro_banco IS NOT NULL ";
		}
		
		
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo retenção.');
		}
	
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
	
		return 0;
	
	}
	
	
	/**
	 * Pesquisa dados de um titulo na tabela titulo_kernel
	 *
	 * @param INT $titoid
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getTituloKernel($titoid){
			
		$sql = " SELECT titkoid
				   FROM titulo_kernel
		          WHERE titknumero_registro_banco_kernel =  $titoid ";

		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo kernel.');
		}

		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}

		return 0;
	
	}
	
	
	/**
	 * Pesquisa dados de um titulo na tabela titulo
	 *
	 * @param INT $titoid
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getDadosTitulo($titoid, $valor_titulo = '', $num_reg_banco =  false){
			
		$sql = " SELECT titoid,
						titdt_credito,
		                titnumero_registro_banco
		           FROM titulo
		          WHERE titoid = $titoid ";
		
		if($valor_titulo != ''){
			$sql .= " AND titvl_titulo = $valor_titulo ";
		}
		
		if($num_reg_banco){
			$sql .= " AND titnumero_registro_banco IS NOT NULL ";
		}

		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo registro banco.');
		}

		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}

		return 0;

	}
	
	
	/**
	 *  Recupera os títulos filhos do título pai informado
	 *  
	 * @param INT $titoid
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getTituloFilhoPolitica($titoid){

		if(!is_numeric($titoid)){
			throw new Exception('O titulo filho deve ser informado para recuperar o título pai.');
		}

		$sql = "     SELECT t.titoid AS titulo_filho, 
		                    titcdt_pagamento
				      FROM titulo_consolidado tc
				INNER JOIN titulo t ON t.tittitcoid = tc.titcoid    
				INNER JOIN titulo_tipo tpt ON tpt.tittoid = tc.titctittoid
				     WHERE tc.titcoid  = $titoid
				       AND tpt.titttipo = 'PD' --Tipo do tipo politica de desconto
				      -- AND tc.titcformacobranca = 63 ";

		if(!$resul = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao recuperar título filho');
		}

		if(pg_num_rows($resul) > 0 ){
			return pg_fetch_all($resul);
		}

		return false;
	}

	
	/**
	 * 
	 * 
	 * @throws exception
	 * @return multitype:|boolean
	 */	
	public function getTipoMovimentacaoBancaria(){

		$sql = "SELECT tmbhistorico,
           			   tmbtipo,
           			   tmbplcoid,
           			   tmboid
           		  FROM tp_movim_banco
           		 WHERE tmboid=7
           		   AND tmbdt_exclusao IS NULL; ";
		
		if(!$res = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possível buscar código do banco');
		}
		
		if (pg_num_rows($res)>0){
			return pg_fetch_all($res);
		}
			
		return false;
	}
	
	
	/**
	 * 
	 * @param INT $num_registro
	 * @throws Exception
	 * @return multitype:|number
	 */
	public function getDadosTituloRegistroBanco($num_registro, $num_reg_banco =  false){

		$sql = " SELECT titoid,
						titdt_credito,
						titnumero_registro_banco
				   FROM titulo
		          WHERE titnumero_registro_banco = $num_registro ";
		
		if($num_reg_banco){
			$sql .= " AND titnumero_registro_banco IS NOT NULL ";
		}
		
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo pelo num registro banco.');
		}

		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}

		return 0;

	}
	
	/**
	*
	*
	* @param INT $titoid
	* @throws Exception
	* @return multitype:|number
	*/
	public function getDadosTituloVenda($titoid){

		$sql = "  SELECT titoid,
						 titdt_credito
		            FROM titulo_venda
		           WHERE titoid = $titoid  ";
		
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo venda.');
		}

		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}

		return 0;

	}
	
	
	/**
	 * 
	 * @param INT $titoid
	 * @param FLOAT $valor_credito_titulo
	 * @throws Exception
	 * @return boolean
	 */
	public function calcularAtualizarMultaJuros($titoid, $valor_credito_titulo){

		$multa 		= "";
		$desconto 	= "";
		
		// Somente atualiza o valor de desconto, caso o valor enviado pelo banco seja maior que zero.
		//REVISION 1872 sistemaWeb_old
		$valor_desconto_titulo = 0; 
		
		if($valor_desconto_titulo > 0){
			$desconto = " , titvl_desconto = '$valor_desconto_titulo' ";
		}
				
		$sql_tit = "SELECT tittaxa_administrativa,
						   titvl_ir,
						   titvl_iss,
						   titvl_piscofins,
						   titvl_titulo,
						   titvl_multa,
						   titvl_juros,
						   titvl_desconto
					  FROM titulo
					 WHERE titoid = $titoid ; ";
		
		if($res_tit = pg_query($this->conn,$sql_tit)){
		
			if(pg_num_rows($res_tit) > 0){
				
				$tittaxa_administrativa = pg_fetch_result($res_tit,0,'tittaxa_administrativa');
				$titvl_ir 				= pg_fetch_result($res_tit,0,'titvl_ir');
				$titvl_iss 				= pg_fetch_result($res_tit,0,'titvl_iss');
				$titvl_piscofins 		= pg_fetch_result($res_tit,0,'titvl_piscofins');
				$titvl_titulo 			= pg_fetch_result($res_tit,0,'titvl_titulo');
				$titvl_multa 			= pg_fetch_result($res_tit,0,'titvl_multa');
				$titvl_juros 			= pg_fetch_result($res_tit,0,'titvl_juros');
				$titvl_desconto 		= pg_fetch_result($res_tit,0,'titvl_desconto');
		
				if (($valor_credito_titulo + $tittaxa_administrativa + $titvl_ir + $titvl_iss + $titvl_piscofins) > $titvl_titulo
				&&
				($titvl_juros) != (($valor_credito_titulo + $tittaxa_administrativa + $titvl_ir + $titvl_iss + $titvl_piscofins) - $titvl_titulo)
				) {
					 
					$vl_juros = ($valor_credito_titulo + $tittaxa_administrativa + $titvl_ir + $titvl_iss + $titvl_piscofins) - $titvl_titulo;
					$multa = " , titvl_juros = '$vl_juros' ";
					 
				}
				if (($valor_credito_titulo + $tittaxa_administrativa + $titvl_ir + $titvl_iss + $titvl_piscofins) < $titvl_titulo
				&&
				$titvl_desconto != ($titvl_titulo - ($valor_credito_titulo + $tittaxa_administrativa + $titvl_ir + $titvl_iss + $titvl_piscofins))
				) {
					 
					$vl_desconto = $titvl_titulo - ($valor_credito_titulo + $tittaxa_administrativa + $titvl_ir + $titvl_iss + $titvl_piscofins);
					$desconto = " , titvl_desconto = '$vl_desconto' ";
					 
				}
				
				// Atualizando campo em titulo informando que foi realizada uma baixa automatica no retorno da cobrança registrada
				$sql = "UPDATE titulo
							SET titbaixa_automatica_banco = 't'
								$multa
								$desconto
						  WHERE titoid = $titoid  ";
				
				if(!pg_query($this->conn, $sql)){
					throw new Exception('Falha ao atualizar dados do título baixa automática.');
				}
				
			}
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * @param unknown $titoid
	 * @param unknown $cd_usuario
	 * @param unknown $valor_pago
	 * @param unknown $tit_numero_registro_banco
	 * @throws Exception
	 * @return boolean
	 */
	public function setBaixaTituloPai($titoid, $cd_usuario, $valor_pago, $tit_numero_registro_banco, $forccfbbanco){
		
		$sql = "UPDATE titulo_consolidado
				   SET titcdt_pagamento           = 'now()',
		               titcvl_pagamento           = ".$valor_pago.",
		               titcdt_credito             = 'now()',
        			   titcusuoid_alteracao       = ".$cd_usuario.",
        			   titcnumero_registro_banco  = $tit_numero_registro_banco ,
        			   titccfbbanco               = $forccfbbanco,
        			   titcbaixa_automatica_banco = 't'
                 WHERE titcoid = $titoid";
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar/baixar titulo pai.');
		}

		return true;
		
	}
	
	
	/**
	 * 
	 * @param INT $titoid
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getDadosTituloPai($titoid){

		$sql = "SELECT titcvl_titulo, 
				       titcvl_desconto, 
				       titcvl_juros, 
				       titcvl_multa,
				       TO_CHAR(titcdt_vencimento, 'DD/MM/YYYY') AS titcdt_vencimento,
				       titcvl_pagamento
				  FROM titulo_consolidado 
				 WHERE titcoid = $titoid  ";

		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo pai consolidado.');
		}

		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}

		return false;
	}
	
	
	
	/**
	 * 
	 * @param INT $titnumero_registro_banco
	 * @param FLOAT $valor_credito_titulo
	 * @param DATE $data_ocorrencia
	 * @throws Exception
	 * @return number
	 */
	public function setEmiteBoletoRecuperaAtivo($titnumero_registro_banco,$valor_credito_titulo,$data_ocorrencia){
		
		$sql = "SELECT emite_boleto_recup_ativo( '".trim($titnumero_registro_banco)."', '$valor_credito_titulo', '$data_ocorrencia' );";
		
		if(!$result = pg_query($this->conn, $sql)){
			
			return 2;//throw new Exception('Falha ao executar função ->  emite_boleto_recup_ativo ');
			
		}elseif(pg_num_rows($result) > 0){
			
			return 1;
			
		}else{
			
			return 0;
		}
		
	}

	
	/**
	 * 
	 * @param INT $titoid
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarValoresTituloFilho($titoid){
	
		if(empty($titoid)){
			throw new Exception('O titulo filho deve ser informado para atualizar os valores.');
		}
	
		$sql = " UPDATE titulo
				    SET titvl_juros    = titvl_juros_desc_cobranca ,
				        titvl_multa    = titvl_multa_desc_cobranca ,
				        titvl_desconto = coalesce(titvl_desconto,0) +  coalesce(titvl_desc_cobranca,0)  
				  WHERE titoid = $titoid  ";
	
		if(!$resul = pg_query($sql)){
			throw new Exception('Falha ao atualizar valor do titulo filho ->'. $titulo);
		}
	
		return true;
	}
	
	
	/**
	 * 
	 * @param int  $titoid
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getValoresTitulosFilhos($titoid){
		
		//retorna o valores copiados
		$sql = " SELECT titvl_titulo,
		                titvl_pagamento,
		                coalesce(titvl_juros,0) +  coalesce(titvl_multa,0) AS titvl_juros_multa,
						titvl_desconto 
				   FROM titulo
				  WHERE titoid = $titoid ";
		
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao retornar valores do título filho ->.'.$titoid);
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
		
		return false;
	}
	
	
	
	/**
	 * 
	 * 
	 * @param INT $titoid
	 * @param STRING $aux_campos
	 * @param INT $cd_usuario
	 * @throws Exception
	 */
	public function setBaixaContasReceber($titoid, $aux_campos, $cd_usuario){
		
		// Montando baixa dos titulos
		// Monta sql invocando a função para realizar a baixa dos títulos
		
		$sql = "SELECT baixa_contas_receber('".$titoid."', 1, '".$aux_campos."', $cd_usuario );";
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao executar função ->  baixa_contas_receber ');
		}
		
		return true;
	}
	
	
	/**
	 * Desvincula titulo filho do titulo pai
	 * 
	 * @param int $titoid
	 * @throws Exception
	 * @return boolean
	 */
	public function desvincularTituloConsolidado($titoid){
		
		$sql = "UPDATE titulo
		           SET tittitcoid = NULL
		         WHERE titoid = $titoid";
			
		if(!pg_query($this->conn,$sql)){
			throw new Exception("Erro ao desvincular titulo consolidado.");
		}
			
		return true;
	}
	
	/**
	 *  Insere movimentação bancária e atualiza o título
	 * 
	 * @param STRING $parametros
	 * @param ARRAY $mlista_titulo
	 * @throws Exception
	 * @return boolean
	 */
	public function setMovimentacaoBancaria($parametros, $mlista_titulo){
		
		try {
		
			$sql = "SELECT movim_banco_i ('$parametros') AS mbcooid; ";
			
			if(!$resu = pg_query($this->conn,$sql)) {
				throw new Exception("Erro ao inserir movimentação bancária.");
			}
			
			if(pg_num_rows($resu) == 0){
				throw new Exception("Erro ao recuperar código da movimentação bancária.");
			}
	
			//atualiza os titulos
			$mbcooid = pg_fetch_result($resu,0,"mbcooid");

			$sql = "UPDATE titulo
			           SET titmbcooid=$mbcooid
			         WHERE titoid IN (".implode(",",$mlista_titulo).");";
			
			if(!pg_query($this->conn,$sql)){
				throw new Exception("Erro ao atualizar movimentação bancária do(s) titulo(s).");
			}
			
	           		
	         return true;  

         } catch (Exception $e) {
         	return $e->getMessage();
         }
	}
	
	
	/**
	 * 
	 * 
	 * @param INT $cod_ocorrencia
	 * @param INT $tit_numero_registro_banco
	 * @param INT $titoid
	 * @throws Exception
	 * @return boolean
	 */
	public function setDadosTitulo($cod_ocorrencia, $tit_numero_registro_banco, $titoid , $cod_rejeicao = ''){
	
		$sql = " UPDATE titulo
           			SET	titcod_retorno_cobr_reg = $cod_ocorrencia,
           			    titnumero_registro_banco = $tit_numero_registro_banco ";
		
		if($cod_rejeicao != ''){
			$sql .= " , titmsg_retorno_cobr_reg = '$cod_rejeicao'
           			  , titrtcroid = NULL  ";
		}
		
           	$sql .= " WHERE titoid = $titoid   ";
	
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar dados do título.');
		}
	
		return true;
	
	}
	
	
	/**
	 * 
	 * @param INT $titoid
	 * @param INT $cod_ocorrencia
	 * @param INT $forccfbbanco
	 * @throws Exception
	 * @return boolean
	 */
	public function setDadosEventoTitulo($titoid, $cod_ocorrencia, $forccfbbanco, $tipo_codigo, $cod_rejeicao = '',  $titdt_credito = '', $evticod_retorno_cobr_reg = ''){

		
		$sql = " UPDATE evento_titulo
		            SET	evticod_retorno_cobr_reg = $cod_ocorrencia ";
		
		if($titdt_credito != '' && $cod_rejeicao = ! ''){
			$sql .= " , evtimsg_retorno_cobr_reg = '$cod_rejeicao'
           			  , evtirtcroid = NULL ";
		}
		
		$sql .= "  WHERE evtititoid = $titoid
		             AND evtitpetoid = (SELECT DISTINCT tpetoid 
		                                 FROM tipo_evento_titulo 
		                                WHERE tpetcfbbanco = $forccfbbanco 
		                                  AND tpetcodigo IN ($tipo_codigo))";
		
		if($evticod_retorno_cobr_reg == 1){
		  $sql .= "  AND evticod_retorno_cobr_reg IS NULL ";
		}
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar dados do evento do título.');
		}

		return true;

	}
	
	
	/**
	 * Grava em arquivo e retorna o número do processo inciado no banco de dados
	 * 
	 * @return boolean
	 */
	public function getPidProcessoDB(){
		 
		$sql = "select pg_backend_pid() AS pid";
		 
		$rs = pg_query($this->conn, $sql);
	
		if ($rs && pg_num_rows($rs)) {
	
			file_put_contents(_SITEDIR_ . 'processar_cobr_registrada_log/pidProcessoBD.txt', pg_fetch_result($rs, 0, 'pid'));
			
			return  pg_fetch_result($rs, 0, 'pid');
		}
			
		return false;
	}
	
	
	public function prepararVerificaBaixaTitulo(){
		
		// Verificando data de baixa do titulo
		$sql = "PREPARE verifica_baixa_titulo(integer) AS
                 SELECT titdt_credito,
                       (select nflno_numero||'-'||nflserie from nota_fiscal where nfloid = titnfloid) as numero_nota_fiscal,
                        titclioid,
				        titvl_titulo,
                        titvl_pagamento,
				        titvl_titulo - titvl_desconto + titvl_juros_desc_cobranca + titvl_multa_desc_cobranca - titvl_desc_cobranca as valor_titulo_baixado,
						coalesce(titvl_juros,0) + coalesce(titvl_juros_desc_cobranca,0) as titvl_juros,
                        coalesce(titvl_multa,0) + coalesce(titvl_multa_desc_cobranca,0) as titvl_multa,
                        coalesce(titvl_desconto,0) +  coalesce(titvl_desc_cobranca,0) as titvl_desconto,
				        TO_CHAR(titdt_vencimento, 'DD/MM/YYYY') AS titdt_vencimento,
				        TO_CHAR(titdt_pagamento, 'DD/MM/YYYY') AS titdt_pagamento
                   FROM titulo 
				  WHERE titoid = $1; ";
		
		if(!pg_query($this->conn, $sql)){
			throw new exception('Erro: Não foi possível executar prepare');
		}
		
		return true;
	}
	
	
	/**
	 * 
	 * @param INT $titoid
	 * @throws exception
	 * @return boolean
	 */
	public function executarVerificaBaixaTitulo($titoid){
		
		if(empty($titoid)){
			return 'Número do título vazio, não é possível verificar se o título foi baixado.';
		}
				
		$sql = "EXECUTE verifica_baixa_titulo($titoid); ";
		
		if(!$result = pg_query($this->conn, $sql)){
			return 'Não foi possível executar verifica_baixa_titulo -> '. $titoid;
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
		
		return false;
		
	}
	
	
	
	/**
	 *
	 * Mata processo do BD
	 *
	 * @author Rafael Dias <rafael.dias@sascar.com.br>
	 * @version 23/07/2014
	 * @param integer $pid
	 */
	public function killProcessDB($pid){
		 
		global $dbstring;
		 
		$conn2 = pg_connect($dbstring);
		 
		if ($pid > 0) {
	
			//se for ambiente de teste procura pela coluna pid, se em produção pela coluna procpid (versão de banco diferente em ambiente de teste)
			/*Problema 1963 - Erro ao tentar Parar o Processamento (ERRO: coluna "procpid" não existe)
				alterado o nome do campo quando a condição abaixo entrar no else
				De: $coluna_pid = 'procpid'; - Para: $coluna_pid = 'pid';
			*/
			if( $_SESSION['servidor_teste'] == 1 ){
				$coluna_pid = 'pid';
			}else{
				$coluna_pid = 'pid';
			}
	
			$sql = "SELECT count(1) FROM pg_stat_activity WHERE $coluna_pid = $pid ";
			$res = pg_query($conn2,$sql);
			$pid_exists = (pg_num_rows($res) > 0) ? true : false;
	
			if ($pid_exists){
				$sql = "SELECT mata_processo($pid);";
				$res = pg_query($conn2,$sql);
				$return = pg_fetch_result($res,0,0);
			} else{
				$return = 't';
			}
				
			if ($return != 't'){
				pg_close($conn2);
				return false;
			}
			pg_close($conn2);
			return true;
	
		} else{
			return false;
		}
	}
	
	
	public function getPidArquivoTxt(){
	
		$handle = fopen(_SITEDIR_.'processar_cobr_registrada_log/pidProcessoBD.txt', "rb");
		$PIDPROCESSO = fread($handle, 8192);
		fclose($handle);
	
		if($PIDPROCESSO > 0) {
			return $PIDPROCESSO;
		}
	
		return 0;
	}
	
    
	/**
	 * Grava dados de  inicio no processo de importação,
	 * esses dados são parâmetrizados para serem usados no momento de rodar em background
	 * 
	 * @param string $file_name
	 * @throws Exception
	 * @return boolean
	 */
	public function iniciarProcesso($file_name, $cod_tipo, $forma_cobranca, $sistema){
			
			if(!empty($file_name) && !empty($cod_tipo)){
				
				$sql = " INSERT INTO execucao_arquivo( earusuoid,
                                                       eardt_inicio,
                                                       eartipo_processo,
	                                                   eardesc_status,
	                                                   earparametros,
	                                                   earnomearquivo,
	                                                   earsistema)
		                                       VALUES( $this->usuarioID,
					                                   NOW(),
                                                       15,  
					  								   'Processo Iniciado', 
					  								   '".$this->data."|".$forma_cobranca."|',
					  								   	'".trim($file_name)."',
					  								   '$sistema'			
					  								   ) ";
				
				if(!$rs = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir dados ao iniciar processo.');
				}
				
				return  true;
				
			}else{
				throw new Exception('Deve ser informado o nome do arquivo e código do tipo para iniciar o processo de importação.');
			}
		
	}	
	
	
	
	public function atualizarProcesso($pid_processo, $eaoid){
		
		$sql = " UPDATE execucao_arquivo
					SET earpid_processo = $pid_processo
				  WHERE earoid = $eaoid ";
	
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar o processo.');
		}
	
		return true;

	}
	
	
	/**
	 * Atualiza a tabela com os dados de finalização (Sucesso ou Falha) do processo de importação,
	 * são recuperados mais tarde e enviados por e-mail.
	 * 
	 * @param bolean $param
	 * @param string $msg
	 * @param int $eaoid - id da tabela execucao_arquivo
	 * @throws Exception
	 * @return boolean
	 */
	public function finalizarProcesso($param, $msg, $earoid = "", $pid_processo = ""){
		
		if($param){
			$earstatus = 't';
			$eardesc_status = $msg;
		}else{
			$earstatus = 'f';
			$eardesc_status = $msg;
		}

		$sql = " UPDATE execucao_arquivo
				   SET eardt_termino = NOW(), 
				       earstatus = '$earstatus', 
				       eardesc_status = '$eardesc_status' ";
	    if($pid_processo != ""){
	        $sql .=" , earpid_processo = $pid_processo ";
				     
	    }			     
		  $sql .=" WHERE true ";

		if($earoid == ""){
				
			$sql .=" AND eardt_inicio::DATE = NOW()::DATE 
					 AND earstatus = 'f'
					 AND eartipo_processo = 15
					 AND eardt_termino IS NULL ";
		}else{
			$sql .=" AND earoid = $earoid	";
		}
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao finalizar processo.');
		}

		return true;
		
	}
	
	/**
	 * Verifica se existe um processo que foi iniciado e não terminado 
	 * 
	 * @throws Exception
	 * @return object|boolean
	 */
	public function consultarDadosImportacao(){
		
			$sql = " SELECT earoid,
					        earusuoid, 
						    earstatus,
					        eartipo_processo,
					        earparametros,
					        earnomearquivo,
					        earsistema
    				   FROM execucao_arquivo
					  WHERE eardt_termino IS NULL 
					    AND earstatus = 'f'
					    AND eartipo_processo = 15
				   ORDER BY earoid DESC
					  LIMIT 1  ";
			
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados de importação.');
			}
				
			if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
			}
	
			return false;
		
	}
	
	/**
	 * Recupera dados de um processo em andamento que ainda não foi concluído
	 * 
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarProcessoAndamento(){
				
			$sql = " SELECT TO_CHAR(eardt_inicio, 'DD/MM/YYYY HH24:MI:SS') as eardt_inicio,
					        TO_CHAR(eardt_inicio, 'DD/MM/YYYY ') as data_inicio,
							TO_CHAR(eardt_inicio, 'HH24:MI:SS ') as hora_inicio,
					    	eardt_termino,
						    earstatus,
					        earusuoid,
						    nm_usuario,
					        earpid_processo,
					        earsistema
    				   FROM execucao_arquivo
			     INNER JOIN usuarios ON cd_usuario = earusuoid 
					  WHERE eardt_termino IS NULL 
					    AND earstatus = 'f'
					    AND eartipo_processo = 15
				   ORDER BY earoid DESC
					  LIMIT 1 ";
			
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados do processo de importação.');
			}
				
			if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
			}
		
			return false;
		
	}
	
	/**
	 * Retornar dados de um processo finalizado/cancelado
	 * 
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarProcessoFinalizado($status, $situacao = ''){
			
			$sql = "   SELECT earusuoid AS id_usuario,
					          TO_CHAR(eardt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS inicio,
					          TO_CHAR(eardt_inicio, 'DD/MM/YYYY') AS inicio_data,
					          TO_CHAR(eardt_inicio, 'HH24:MI:SS') AS inicio_hora,
                		      TO_CHAR(eardt_termino, 'DD/MM/YYYY HH24:MI:SS') AS termino
    				     FROM execucao_arquivo
					    WHERE TRUE 
					      AND earstatus = '$status' " ;
			
			if($situacao != ''){
			
			   $sql .= " AND eardesc_status = '$situacao'
						 AND eardt_termino::DATE = NOW()::DATE ";
			}

			   $sql .= " AND eartipo_processo = 15
				         AND eardt_termino IS NOT NULL 
					ORDER BY earoid DESC
   				       LIMIT 1  ";
		
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados de processo finalizado/cancelado.');
			}
		
			if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
			}
		
			return false;
		
	}
	

	
	public function getDadosUsuarioProcesso($id_usuario){
		
		
		$sql = "SELECT nm_usuario, usuemail 
				  FROM usuarios 
				 WHERE cd_usuario = $id_usuario  ";
	
		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao recuperar email do usuario dio processo ");
		}
	
		if(count($result) > 0){
			return pg_fetch_all($result);
		}
		
		return false;
	}
	
	
	
	/**
	 * Recupera o email de testes
	 *
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 14/06/2013
	 *
	 * @return Object
	 */
	public function getEmailTeste(){
	
		$sql = "SELECT pcsidescricao, pcsioid
  				FROM
					parametros_configuracoes_sistemas,
					parametros_configuracoes_sistemas_itens
 				WHERE
					pcsoid = pcsipcsoid
			    AND pcsdt_exclusao is null
				AND pcsidt_exclusao is null
				AND pcsipcsoid = 'PARAMETROSAMBIENTETESTE'
				AND pcsioid = 'EMAIL' ";

		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao recuperar email de teste ");
		}

		if(count($result) > 0){
			return pg_fetch_object($result);
		}
		
		return false;
	
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