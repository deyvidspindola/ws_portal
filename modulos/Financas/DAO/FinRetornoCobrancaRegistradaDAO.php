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

	const BANCO__SANTANDER = 33;
	const SANTANDER_REJEICAO_DETALHE__OUTROS = 99;
	
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn){
		
		$this->conn = $conn;
		if(empty($this->usuarioID)){
		   $this->usuarioID = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid']: 2750;
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
				    AND forcoid <> 85 ";
			
		if(!$res = pg_query($this->conn,$sql)){
			throw new exception('Falha ao busca forma de cobrança.');
		}
			
		if(pg_num_rows($res) > 0){
			return pg_fetch_all($res);
		}
		
		return false;
				
	}
	
	public function getRemessa($tituloID){
		
		$sql = "SELECT rtcroid FROM titulo
				INNER JOIN remessa_titulo_cobr_reg on rtcroid = titrtcroid 
				WHERE 
					rtcrdt_exclusao IS NULL
					AND rtcrcfbbanco = 33
					AND titoid = $tituloID 
				LIMIT 1";
		
		if(!$res = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possível o id da remessa ' . $tituloID);
		}
		
		if(pg_num_rows($res) > 0){
			$remessaID = pg_fetch_result($res,0,'rtcroid');
			return $remessaID;
		}
		
		return false;
		
	}


	/**
	 * @throws exception
	 * @return object|boolean
	 */
	public function totalRegistrosPorRemessa($tituloID){
		
		$sql = " 	
			SELECT
				COUNT(1) as TOTAL 
			FROM (
				SELECT 
					tittpetoid, titrtcroid 
				FROM titulo 
				WHERE tittpetoid IN (
							SELECT tpetoid FROM tipo_evento_titulo 
							WHERE tpetcodigo IN (
										SELECT UNNEST(ARRAY[string_to_array(pcsidescricao, ',')])::INT 
										FROM parametros_configuracoes_sistemas 
										INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
										WHERE 
											pcsipcsoid = 'COBRANCA_REGISTRADA' 
											AND pcsioid = 'COD_MOVIMENTO_REMESSA_ENVIO'
									    ) 				    
							AND tpetcfbbanco = 33 
							AND tpettipo_evento = 'Remessa'
						    )
				AND titrtcroid IN   (			
							SELECT rtcroid FROM titulo
							INNER JOIN remessa_titulo_cobr_reg on rtcroid = titrtcroid 
							WHERE 
								rtcrdt_exclusao IS NULL
								AND rtcrcfbbanco = 33
								AND titoid = $tituloID 
						    )
				UNION ALL
				SELECT 
					tittpetoid, titrtcroid 
				FROM titulo_retencao 
				WHERE tittpetoid IN (
							SELECT tpetoid FROM tipo_evento_titulo 
							WHERE tpetcodigo IN (
										SELECT UNNEST(ARRAY[string_to_array(pcsidescricao, ',')])::INT 
										FROM parametros_configuracoes_sistemas 
										INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
										WHERE 
											pcsipcsoid = 'COBRANCA_REGISTRADA' 
											AND pcsioid = 'COD_MOVIMENTO_REMESSA_ENVIO'
									    ) 				    
							AND tpetcfbbanco = 33 
							AND tpettipo_evento = 'Remessa'
						    )
				AND titrtcroid IN   (			
							SELECT rtcroid FROM titulo
							INNER JOIN remessa_titulo_cobr_reg on rtcroid = titrtcroid 
							WHERE 
								rtcrdt_exclusao IS NULL
								AND rtcrcfbbanco = 33
								AND titoid = $tituloID 
						    )
				UNION ALL
				SELECT 
					titctpetoid, titcrtcroid 
				FROM titulo_consolidado
				WHERE titctpetoid IN (
							SELECT tpetoid FROM tipo_evento_titulo 
							WHERE tpetcodigo IN (
										SELECT UNNEST(ARRAY[string_to_array(pcsidescricao, ',')])::INT 
										FROM parametros_configuracoes_sistemas 
										INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
										WHERE 
											pcsipcsoid = 'COBRANCA_REGISTRADA' 
											AND pcsioid = 'COD_MOVIMENTO_REMESSA_ENVIO'
									    ) 				    
							AND tpetcfbbanco = 33 
							AND tpettipo_evento = 'Remessa'
						    )
				AND titcrtcroid IN   (			
							SELECT rtcroid FROM titulo
							inner JOIN remessa_titulo_cobr_reg on rtcroid = titrtcroid 
							WHERE 
								rtcrdt_exclusao IS NULL
								AND rtcrcfbbanco = 33
								AND titoid = $tituloID
						     )
			) as t
		";
			
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
	 * Obtém nome do pagador (cliente) e valor do título
	 * @author Marcelo Burkard <marcelo.burkard@meta.com.br>
	 * @since 16/11/2017
	 */
	public function getTituloPagadorValor($titoid) {

		$sql = "(select titvl_titulo valor, clinome from titulo t "
			 . "inner join clientes c on (c.clioid = t.titclioid) "
			 . "where titoid = '{$titoid}' "
			 . "limit 1) "

			 . " union all "

			 . "(select titcvl_titulo valor, clinome from titulo_consolidado t "
			 . "inner join clientes c on (c.clioid = t.titcclioid) "
			 . "where titcoid = '{$titoid}' "
			 . "limit 1) "

			 . " union all "

			 . "(select titvl_titulo_retencao valor, clinome from titulo_retencao t "
			 . "inner join clientes c on (c.clioid = t.titclioid) "
			 . "where titoid = '{$titoid}' "
			 . "limit 1)";

		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao pesquisar dados do titulo registro banco.');
		}

		if (pg_num_rows($result) > 0) {
			return pg_fetch_object($result);
		}

		return false;
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
				       --AND tc.titcformacobranca = 63 --titulo avulso  ";

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
	
	//header do lote retorno e atualizar para alterar o status da remessa como Processada
	public function headerStatuRemessa($remessaID, $forccfbbanco){
		$sql = "UPDATE remessa_titulo_cobr_reg SET rtcrdt_ultimo_envio = NOW() where rtcroid = $remessaID and rtcrcfbbanco = $forccfbbanco; ";
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar status da remessa.');
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

	public function verificaRemessaProcessada($idRemesssa, $forccfbbanco){

		$sql = "SELECT
						1
						FROM remessa_titulo_cobr_reg
						WHERE rtcrnumero_remessa = $idRemesssa
						AND rtcrcfbbanco = $forccfbbanco
						AND rtcrdt_ultimo_envio IS NOT NULL";

		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do titulo pai consolidado.');
		}

		return pg_num_rows($result) > 0;

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

			//$sql .= " , titmsg_retorno_cobr_reg = '$cod_rejeicao'
           	//		  , titrtcroid = NULL  ";
				//alterado pela  STI 86719
           		$sql .= " , titmsg_retorno_cobr_reg = '$cod_rejeicao' ";
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
		             AND evtitpetoid::TEXT IN (SELECT DISTINCT string_agg(tpetoid::TEXT, ',')
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
		
		$sql = "SELECT titpref_protheus, titformacobranca, titdt_credito,
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
				 WHERE titoid = $titoid ";
		
		if(!$result = pg_query($this->conn, $sql)){
			return 'Não foi possível executar verificar a baixa do título -> '. $titoid;
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
	
	//Atualiza o status do título:
	public function updateTitulo($titulo,$tpetoid){
	
		$sql = " UPDATE titulo SET tittpetoid = $tpetoid WHERE titoid = $titulo";
 		
		$result = pg_query($this->conn, $sql);

		if(!$result){
			echo $sql;
			throw new Exception("Falha ao atualizar o processo titulo(titulo $titulo).");
		}

		return pg_affected_rows($result) > 0;
		

	}
	
	//Atualiza o status do título:
	public function updateTituloRetencao($titulo,$tpetoid){
	
		$sql = " UPDATE titulo_retencao SET tittpetoid = $tpetoid WHERE titoid IN ($titulo)";
		$result = pg_query($this->conn, $sql);

		if(!$result){
			echo $sql;
			throw new Exception("Falha ao atualizar o processo titulo retenção(titulo $titulo).");
		}
	
		return pg_affected_rows($result) > 0;
		

	}
	
	//Atualiza o status do título:
	public function updateTituloConsolidado($titulo,$tpetoid){
	
			$sql = " UPDATE titulo_consolidado SET titctpetoid = $tpetoid WHERE titcoid IN ($titulo)";
	
		$result = pg_query($this->conn, $sql);

		if(!$result){
			echo $sql;
			throw new Exception("Falha ao atualizar o processo titulo consolidado(titulo $titulo).");
		}
	
		return pg_affected_rows($result) > 0;
		

	}

	//Grava log na tabela evento_titulo:
	public function logEventoTitulo($titulo,$tpetoid,$identRejeicao,$tipoTitulos){
			
		if($tipoTitulos == "S"){
			$sql = "INSERT INTO evento_titulo (evtititoid,evtitpetoid,evtidt_geracao,evticod_retorno_cobr_reg)
			VALUES($titulo, $tpetoid, NOW(), $identRejeicao); "; 
		}else if($tipoTitulos == "R"){
			$sql = "INSERT INTO evento_titulo (evtititoid,evtitpetoid,evtirtcroid,evtidt_geracao,evticod_rejeicao)
			VALUES($titulo, $tpetoid, NULL, NOW(), $identRejeicao);";
		}else if($tipoTitulos == "L"){
			$sql = "INSERT INTO evento_titulo (evtititoid,evtitpetoid,evtirtcroid,evtidt_geracao,evticod_retorno_cobr_reg)
			VALUES($titulo, $tpetoid, NULL, NOW(), $identRejeicao);";
		}else{
			$sql = "INSERT INTO evento_titulo (evtititoid,evtitpetoid,evtirtcroid,evtidt_geracao,evticod_retorno_cobr_reg)
			VALUES($titulo, $tpetoid, NULL, NOW(), $identRejeicao);";
		}	

		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao inserir dados ao evento_titulo. titoid=' . $titulo);
		}

		return true;

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
				
			$sql .=" AND earstatus = 'f'
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
	
	//Retorna código que deve ser inserido nos campos : evento_titulo.evticod_retorno_cobr_reg e titulo.tittpetoid
	public function getTipoEvento($id,$tipoTitulos, $tipo_evento = NULL){
	
		
		$sql = "SELECT tpetoid 
				FROM tipo_evento_titulo
				WHERE tpetcodigo = $id ";

		if($tipoTitulos == 'S'){
		
			$sql .= "AND tpettipo_evento = 'Retorno' ";
		
		}else if($tipoTitulos == 'R'){
		
			if($tipo_evento == NULL){
				$TPevento = 'Retorno';
			}else{
				$TPevento= $tipo_evento;
			}
			
			$sql .= "AND tpettipo_evento = '$TPevento' ";
			
			
		}else if($tipoTitulos == 'L'){
			
			$sql .= "AND tpettipo_evento = 'Liquidacao' ";
			
		}elseif($tipoTitulos == 'B'){

			$tipo_evento = is_null($tipo_evento) ? 'Baixa' : $tipo_evento;
			
			$sql .= " AND tpettipo_evento = '$tipo_evento' ";
		}
		
		$sql .= " AND tpetcfbbanco = ". self::BANCO__SANTANDER;
		$sql .= " AND tpetcob_registrada IS TRUE";
		
		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao recuperar tipo evento ");
		}

		if(pg_num_rows($result) > 0){
			return pg_fetch_result($result, 0, 'tpetoid');
		}

		$sqlCodigoPadrao = "
			SELECT
				tpetoid
			FROM
				tipo_evento_titulo
			WHERE
				tpetcodigo = ". self::SANTANDER_REJEICAO_DETALHE__OUTROS;

		$sqlCodigoPadrao .= " AND tpetcfbbanco = ". self::BANCO__SANTANDER;
		$sqlCodigoPadrao .= " AND tpetcob_registrada IS TRUE";
			
		if (!$resultCodigoPadrao = pg_query($this->conn, $sqlCodigoPadrao)) {
			throw new Exception ("Falha ao recuperar tipo evento ");	
		}

		return pg_fetch_result($resultCodigoPadrao, 0, 'tpetoid');
	
	}
	
	//Titulos com sucesso e rejeitados
	public function getCodigoSucessRejeitadoConsolidados($tipoTitulos){
		
			$sql = "SELECT pcsidescricao
					FROM parametros_configuracoes_sistemas
					INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
					WHERE pcsipcsoid = 'COBRANCA_REGISTRADA'";
			
		if($tipoTitulos == 'S'){
			$sql.= " AND pcsioid = 'COD_MOVIMENTO_REGISTRADO';";
		}else if($tipoTitulos == 'R'){
			$sql .= " AND pcsioid = 'COD_MOVIMENTO_RETORNO_REJEITADO';";
		}else if($tipoTitulos == 'L'){
			$sql .= " AND pcsioid = 'COD_MOVIMENTO_RETORNO_LIQUIDACAO';";
		}elseif($tipoTitulos == 'B'){
			$sql .= " AND pcsioid = 'COD_MOVIMENTO_RETORNO_BAIXA';";
		}

		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao recuperar codigo de titulos com sucesso ");
		}

		if(count($result) > 0){
			return pg_fetch_object($result);
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
        
        //Titulos com sucesso e rejeitados
	public function getNumeroNota($titoid){
		
			$sql = "SELECT CONCAT  (nf.nflno_numero, '-', nf.nflserie) AS numero_nota
					FROM titulo
					INNER JOIN nota_fiscal nf ON nf.nfloid = titnfloid
					WHERE titoid = $titoid";
			
		if (!$result = pg_query($this->conn, $sql)) {
			throw new Exception ("Falha ao recuperar  de titulos com sucesso ");
		}

		if(count($result) > 0){
			$resultado = pg_fetch_row($result);
			return $numero_nota = $resultado[0];
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