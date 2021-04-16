<?php
/**
 * @file FinBoletagemMassivaDAO.php
 * @author marcio.ferreira
 * @version 29/07/2015 14:18:14
 * @since 29/07/2015 14:18:14
 * @package SASCAR FinBoletagemMassivaDAO.php 
 */


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/boletagem_massiva_'.date('d-m-Y').'.txt');



class FinBoletagemMassivaDAO{
	
	/**
	 * Link de conexão com o banco
	 *
	 * @property resource
	 */
	private $conn;
	
	public $usuarioID;
	
	/**
	 * Construtor
	 *
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn) {
	
		$this->conn = $conn;
		
		if(empty($this->usuarioID)){
			$this->usuarioID = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid']: NULL;
		}
	}
	
	
	
	/**
	 * Retorna as campanhas geradas de acordo o filtro informado na tela montando a paginação
	 * 
	 * @param object $dados
	 */
	public function getCampanhas($pesquisa, $paginacao = null, $ordenacao = null){
		
		
		if(!isset($paginacao)) {
			$select = "COUNT(abooid) AS total_registros";
			$orderBy = "";
		
		}else{
		
			$select =  " abooid,
						 aboarquivo,
			             abonm_campanha AS nome_campanha, 
					     nm_usuario AS nome_usuario,
					     abodt_envio AS data_envio, 
					     TO_CHAR(abovl_divida_inicial, 'L9G999G990D99') || ' a '  ||  TO_CHAR(abovl_divida_final, 'L9G999G990D99') AS valor_divida, 
					     poddescricao_atraso AS aging_divida,
					     TO_CHAR(abodt_vencimento, 'DD/MM/YYYY') AS data_vencimento, 
					     CASE  
					        WHEN aboformato_envio = 'G' THEN 'Gráfica'
					        WHEN aboformato_envio = 'E' THEN 'E-mail'
					     ELSE 
					        'Formato inválido'
					     END AS formato_envio ";
			
			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "abooid, nome_campanha, abodt_vencimento";
			
		}
		
		$sql =" SELECT 
		               ".$select."   
				      FROM arquivo_boletagem 
				INNER JOIN usuarios ON cd_usuario = abousuoid_cadastro
				INNER JOIN politica_desconto ON podoid = abopodoid
				WHERE abodt_cadastro::DATE BETWEEN '$pesquisa->data_ini' AND '$pesquisa->data_fim' ";
		
		
		if($pesquisa->nome_campanha != 'NULL'){
			$sql .=" AND abonm_campanha ilike '%$pesquisa->nome_campanha%'";
		}
			
		if($pesquisa->data_vencimento  != 'NULL'){
			$sql .=" AND abodt_vencimento  = '$pesquisa->data_vencimento' ";
		}

		$sql .= $orderBy;
		
		
		if (isset($paginacao->limite) && isset($paginacao->offset)) {
			$sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
		}
		
			
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar campanhas.');
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
		
		return false;
		
		
	}
	
	
	
	/**
	 * Grava os dados da campanha informados na tela e retorna o id gerada para gravar em titulo_consolidado.titcaboid
	 * 
	 * @param object $dados
	 * @throws Exception
	 * @return boolean
	 */
	public function gravarDadosCampanha($dados){
		
		
		$sql = "INSERT INTO arquivo_boletagem(
							  abousuoid_cadastro,
							  abonm_campanha,
							  abovl_divida_inicial, 
							  abovl_divida_final,
							  abopodoid,
							  abodt_vencimento,
							  aboformato_envio,
				 			  abotipo_pessoa,
  							  abotipo_cliente,
							  abouf_cliente,
							  abocod_cliente
							  )
					   VALUES ( 
							  	$this->usuarioID,
							  	'$dados->cad_nome_campanha',
							  	$dados->valor_divida_ini,
							  	$dados->valor_divida_fim,
							  	$dados->aging_divida,
							  	'$dados->cad_vencimento',
							  	'$dados->formato_envio',
							  	'$dados->tipo_pessoa',
							  	'$dados->tipo_cliente',
							  	'$dados->uf_cli',
							  	'$dados->cod_cli' ) 
		         RETURNING abooid ; ";
		
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao inserir dados para gerar campanha.');
		}
		
		if (pg_num_rows($rs) > 0) {
			
			$abooid = pg_fetch_result($rs, 0, 'abooid');
			return  $abooid;
		}
		
		return false;
		
	}
	
	
	
	/**
	 * Recupera dados gravados na tabela arquivo_boletagem para gerar a campanha
	 *
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getDadosGerarCampanha($id_campanha){
	
		$sql =" SELECT abonm_campanha,
					   aboformato_envio,	
					   abovl_divida_inicial,
				       abovl_divida_final,
				       abopodoid,
				       abotipo_pessoa,
				       abotipo_pessoa,
				       abotipo_cliente,
				       abouf_cliente,
				       abocod_cliente,
				       TO_CHAR(abodt_vencimento, 'DD/MM/YYYY') AS abodt_vencimento
				  FROM arquivo_boletagem 
				 WHERE abooid =  $id_campanha ";
			
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados para gerar campanha.');
		}
	
		if(pg_num_rows($result) > 0){
			return pg_fetch_object($result);
		}
	
		return false;
	
	}
	
	
	/**
	 * Pesquisa dados do cliente pelo id informado
	 * 
	 * @param int $id_cliente
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getDadosCliente($id_cliente){
		
	
		$sql ="  SELECT
				       clinome,
				       cliemail_nfe,
				       clitipo,
				       clino_doc,
				       COALESCE(clirua_fiscal,'') || COALESCE(', ' || clino_fiscal,'') || COALESCE(' - ' || clicompl_fiscal,'') as log_fiscal,
				       COALESCE(clibairro_fiscal,'') as bairro_fiscal, 
				       COALESCE(clicep_fiscal,'') as cep_fiscal, 
				       COALESCE(clicidade_fiscal,'') || COALESCE(' - ' || cliuf_fiscal,'') as cidade_fiscal,
				       cliend_cobr as end_cor,  
				       COALESCE(endlogradouro,'') || COALESCE(', ' || endno_numero,'') || COALESCE(' - ' || endcomplemento,'') as log_cor,
				       COALESCE(endbairro,'') as bairro_cor, 
					   COALESCE(endcep,'') as cep_cor, 
				       COALESCE(endcidade,'') || COALESCE(' - ' || enduf,'') as cidade_cor 
				  FROM nota_fiscal_item
 			INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nflserie = nfiserie
 			INNER JOIN ( SELECT cliend_cobr,
 			        			cliemail_nfe,
					            clinome,
					            clioid,
					            clitipo,
					            clirua_res AS clirua_fiscal,
					            clino_res AS clino_fiscal,
					            clicompl_res AS clicompl_fiscal,
					            clibairro_res AS clibairro_fiscal,
					            COALESCE ( clicep_res , clino_cep_res::text) AS clicep_fiscal,
					            clicidade_res AS clicidade_fiscal,
					            cliuf_res AS cliuf_fiscal,
					            LPAD(clino_cpf::text, 11, '0') AS clino_doc,
					            clienvio_grafica
					       FROM clientes
					      WHERE clitipo = 'F'
				        
				          UNION ALL
				
				        SELECT cliend_cobr,
				               cliemail_nfe,
				               clinome,
				      		   clioid,
				          	   clitipo,
				          	   clirua_com AS clirua_fiscal,
				               clino_com AS clino_fiscal,
				               clicompl_com AS clicompl_fiscal,      
				               clibairro_com AS clibairro_fiscal,
				               clicep_com AS clicep_fiscal,
				               clicidade_com AS clicidade_fiscal,
				               cliuf_com AS cliuf_fiscal,
				               LPAD(clino_cgc::text, 14, '0')  AS clino_doc,
				               clienvio_grafica      
				          FROM clientes
				         WHERE clitipo = 'J') clientes ON clioid = nflclioid
                     LEFT JOIN endereco ON endoid = cliend_cobr
						 WHERE clioid = $id_cliente 
						 LIMIT 1    ";
			
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do cliente.');
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_object($result);
		}
		
		return false;
		
	}
	
	
	/**
	 * Atualiza os dados da campanha iformada
	 * 
	 * @param Object $dados
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarDadosCampanha($dados){
		
		if(!is_object($dados)){
			throw new Exception('Dados inválidos para atualizar os dados da campanha gerada.');
		}
		
		
		if(empty($dados->abooid)){
			throw new Exception('O ID da campanha deve ser informado.');
		}

		$sql = " UPDATE arquivo_boletagem
					SET abnm_politica = '$dados->abnm_politica '
	                    ,aboprc_desconto = $dados->aboprc_desconto ";
			$sql .= "WHERE abooid = $dados->abooid    ";
					
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar dados da campanha informada.');
		}
		
		return  true;
		
	}
	
	/**
	 * 
	 * 
	 * @param object $dados
	 * @throws Exception
	 * @return boolean
	 */
	public function setArquivoBoletagem($dados){
		
		if(!is_object($dados)){
			throw new Exception('Dados inválidos para atualizar o nome do arquivo da campanha gerada.');
		}
		
		if(empty($dados->abooid)){
			throw new Exception('O ID da campanha deve ser informado.');
		}
		
		$sql = " UPDATE arquivo_boletagem
					SET aboarquivo = '$dados->aboarquivo'  ";
		$sql .= " WHERE abooid = $dados->abooid    ";
			
			
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar nome do arquivo gerado da campanha informada.');
		}
		
		return  true;
		
	}
	
	
	/**
	 *
	 *
	 * @param object $dados
	 * @throws Exception
	 * @return boolean
	 */
	public function setDadosEnvioArquivo($id_campanha){
	
		if(empty($id_campanha)){
			throw new Exception('O ID da campanha deve ser informado.');
		}
		
		$sql = " UPDATE arquivo_boletagem
		            SET abodt_envio = NOW()
				        ,abousuoid_envio = $this->usuarioID ";
		$sql .= " WHERE abooid = $id_campanha   ";
			
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar dados de envio do arquivo.');
		}
	
		return  true;
	
	}
	
	
	/**
	 * Retorna o nome do arquivo 
	 * 
	 * @param int $id_campanha
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getNomeArquivo($id_campanha){
		
		$sql =" SELECT aboarquivo
				  FROM arquivo_boletagem
				 WHERE abooid =  $id_campanha ";
			
		if(!$result = pg_query($this->conn, $sql)){
		throw new Exception('Falha retornar nome do arquivo.');
		}
		
		if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
		}
		
		return false;
		
	}
	
	
	/**
	 * Retorna as informações de senha usuario e o ftp
	 * @throws Exception
	 * @return array
	 */
	public function getInformacoesFPT(){
	
		$sql = " SELECT
					    valvalor
				 FROM
					    dominio
				 INNER JOIN registro ON domoid = regdomoid
				 INNER JOIN valor ON valregoid = regoid
				 WHERE
					   valoid in (45,47,49,52,54)";
	
		$inf = array();
		if(!$res = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao recuperar informaçoes do ftp arquivos da gráfica'." ".$sql);
		}
		
		while ($row = pg_fetch_object($res)) {
			$inf[]= $row->valvalor;
		}
		return $inf;
	}
	
	
	 /**
	  * Verifica se o cliente possui um titulo consolidado por alguma campanha, se está vencido e não pago
	  * 
	  * @param int $cliod
	  * @throws Exception
	  * @return object|boolean
	  */
	public function getTituloConsolidado($cliod){
		
		
		$sql =" SELECT titcoid
				  FROM titulo_consolidado
		    INNER JOIN arquivo_boletagem ON abooid = titcabooid 
				 WHERE titcclioid = $cliod
		 	       AND titcdt_vencimento::DATE > CURRENT_TIMESTAMP(0)::DATE
				   AND titcdt_pagamento IS NULL ";
			
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do título consolidado.');
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_object($result);
		}
		
		return false;
	}
	
	
	/**
	 * Pesquisa os títulos consolidados gerados pela campanha
	 * 
	 * @param int $id_campanha
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getTituloConsolidadoCampanha($id_campanha){
		
		$sql ="  SELECT titcclioid,
						titcoid AS titulo
						,titcvl_titulo AS valor_titulo
						,titcvl_multa AS valor_multa
						,titcvl_juros AS valor_juros
						,titcvl_desconto AS valor_desconto
						,titcvl_recalculado AS valor_pagar
						,titcvl_desc_cobranca AS valor_desconto_cobranca
						,TO_CHAR(titcdt_vencimento, 'DD/MM/YYYY') AS data_vencimento
						,CAST(aboprc_desconto AS INT) AS perc_desconto
				   FROM titulo_consolidado
		 	 INNER JOIN arquivo_boletagem ON abooid = titcabooid
			   	  WHERE abooid = $id_campanha ";
		
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do título consolidado pela campanha.');
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
		
		return false;
		
	}
	
	
	/**
	 * Atualiza o titulo consolidado criado com o id da campanha gerada
	 * 
	 * @param int $id_campanha
	 * @param int $titulo_consolidado
	 * @throws Exception
	 * @return boolean
	 */
	public function setDadosTituloConsolidado($id_campanha, $titulo_consolidado){
		
		if(empty($id_campanha)){
			throw new Exception('O ID da campanha deve ser informado para atualizar titulo consolidado.');
		}
		
		if(empty($titulo_consolidado)){
			throw new Exception('O ID do titulo deve ser informado para atualizar titulo consolidado.');
		}
		
		$sql = " UPDATE titulo_consolidado
					SET titcabooid = $id_campanha
				  WHERE titcoid = $titulo_consolidado ";
			
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao atualizar dados do titulo consolidado.');
		}
		
		return  true;
		
	}
	
	
	
	
	/**
	 * Grava histório do título consolidado gerado
	 * 
	 * @param object $dados
	 */
	public function setAcionamentoTituloConsolidado($dados){
		
		
		$sql = " INSERT INTO titulo_acionamento (tiaacionamento, 
				                                 tiausuoid, 
				                                 tiamotivo, 
				                                 tiaclioid)
			                              VALUES ('$dados->tiaacionamento',
			                                       $dados->tiausuoid,
			                                      '$dados->tiamotivo',
			                                       $dados->tiaclioid ); ";
		
		if(!pg_query($this->conn, $sql)){
			throw new Exception('Falha ao inserir log de acionamento do titulo consolidado.');
		}
		
		return  true;
		
	}
	
	
	
	/**
	 * Recupera os clientes para gerar a campanha 
	 *
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getClientesCampanha($dados){
	
		$sql =" SELECT titclioid
	    		  	FROM titulo
            		INNER JOIN clientes ON clioid = titclioid
			 		LEFT JOIN nota_fiscal ON titnfloid = nfloid
             		LEFT JOIN contrato on conclioid = clioid AND connumero = nflno_numero 
	        		INNER JOIN forma_cobranca ON titformacobranca = forcoid
					LEFT JOIN cliente_cobranca ON clicclioid = clioid AND clicexclusao IS NULL
			     	WHERE titdt_vencimento < CURRENT_TIMESTAMP(0)::DATE
	    		  	AND titdt_cancelamento IS NULL
	    		   	AND titdt_pagamento IS NULL
				   	AND (titformacobranca = 51 AND titdt_credito IS NOT NULL OR (titdt_credito IS NULL))
				   	AND titnao_cobravel IS NOT TRUE 
					AND (forccobranca IS TRUE OR forcnome = 'Título Avulso' or forcnome = 'Baixa como Perda') 
				   	AND titformacobranca=forcoid
					AND clicsituacao_cobranca NOT ILIKE 'S'
					";
		
		       //tipo de pessoa 
               if(!empty($dados->abotipo_pessoa)){
               	   $sql .= " AND clitipo = '$dados->abotipo_pessoa' "; 
               }
               
               //busca clientes por uf residencial ou comercial
               if(!empty($dados->abouf_cliente)){
	               	$ufs_cli = explode('|', $dados->abouf_cliente);
	                $sql .= $this->montaPesquisaUf($ufs_cli, $dados->abotipo_pessoa).' ';
               }
               
               //busca pelo tipo de contrato do cliente
               if(!empty($dados->abotipo_cliente)){
               		if($dados->abotipo_cliente == 'siggo'){
               			$sql .= " AND conno_tipo IN (905) ";
               		}
               }
               
               //busca pelo último dígito do código do cliente 
               if(!empty($dados->abocod_cliente)){
               	    $cods_cli = explode('|', $dados->abocod_cliente);
               	    $sql .= $this->montaPesquisaLIKE($cods_cli);
               }
               	
				
    		    $sql .=" GROUP BY titclioid ";

		       if (!empty($dados->abovl_divida_inicial) && !empty($dados->abovl_divida_final)){
		       	
		       	   $sql .=" HAVING SUM (titvl_titulo) BETWEEN $dados->abovl_divida_inicial AND  $dados->abovl_divida_final ";
		       }
		
		file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", $sql . "\n", FILE_APPEND);
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados de clientes para gerar a campanha.');
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
	
		return false;
	
	}
	
	
	/**
	 * Monta um parte da query de acordo a opção do tipo de cliente informado na tela
	 * 
	 * @param array $ufs_cli 
	 * @param string $tipo_pessoa
	 * @return string
	 */
	private function montaPesquisaUf($ufs_cli, $tipo_pessoa){
		
		$query = '';
		
		if($tipo_pessoa == 'F' || $tipo_pessoa == 'J'){
			$condi = ' AND ';
		}
		
		if($tipo_pessoa == '' && count($ufs_cli) > 0){
			$condi = ' OR ';
			$parenteses_F = ' )';
			$parenteses_J = ' (';
		}
		
		if($tipo_pessoa == 'J' || (count($ufs_cli) > 0 && $tipo_pessoa == '')){
				
			//endereço comercial
			$query .="AND $parenteses_J cliuf_com ";
			$query .= $this->montaPesquisaIN($ufs_cli);
		}
		
		if($tipo_pessoa == 'F' || (count($ufs_cli) > 0 && $tipo_pessoa == '')){
			
			//endereço residencial
			$query .=" $condi cliuf_res ";
			$query .= $this->montaPesquisaIN($ufs_cli);
			$query .= $parenteses_F;
		}
		
		return $query;
		
	}
	
	
	
	
	private function montaPesquisaIN($dados){
	
		$query =" IN (";
	
		foreach ($dados AS $key => $dados_){
			//apaga posição vazia
			if(empty($dados_)){
				unset($dados_);
			}else{
					
				if(count($dados) > 1 ){
					//tratamento da separação por vírgula
					if($key == 0){
						$vir = '';
					}else{
						$vir = ',';
					}
					$query .= "$vir'$dados_'";
				}else{
					$query .= "'$dados_'";
				}
			}
		}
		$query .= ") ";
		
		return $query;
	
	}
	
	
	
	/**
	 * Retorna parte da query da montagem do comando LIKE
	 * 
	 * @param ARRAY $dados
	 * @return string
	 */
	private function montaPesquisaLIKE($dados){
	
		$query ="";
	
		foreach ($dados AS $key => $dados_){
			//apaga posição vazia
			if($dados_ == ''){
				unset($dados_);
			}else{
				if(count($dados) > 1 ){
					//tratamento da separação por AND ou OR
					if($key == 0){
						$query .= " AND ( clioid::TEXT LIKE '%$dados_' ";
					}else{
						$query .= " OR  clioid::TEXT LIKE '%$dados_' ";
					}
				}else{
					$query .= " AND ( clioid::TEXT LIKE '%$dados_' ";
				}
			}
		}
	
		$query .= " ) ";
		
		return $query;
		
	}
	
	
	
	/**
	 * Retorna dados do usuário que iniciou o processo
	 *
	 * @param int $id_usuario
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getDadosUsuarioProcesso($id_usuario){
	
		$sql = "SELECT nm_usuario, usuemail
				  FROM usuarios
				 WHERE cd_usuario = $id_usuario  ";
	
		if (! $result = pg_query ( $this->conn, $sql )) {
			throw new Exception ( "Falha ao recuperar email do usuario que iniciou o processo " );
		}
	
		if (count ( $result ) > 0) {
			return pg_fetch_all ( $result );
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
	 * Grava dados de  inicio no processo de geração de campanha,
	 * esses dados são parâmetrizados para serem usados no momento de rodar em background
	 *
	 * @param string $file_name
	 * @throws Exception
	 * @return boolean
	 */
	public function iniciarProcesso($param = NULL){
			
	
			$sql = " INSERT INTO execucao_arquivo( 
							                     earusuoid,
												 eardt_inicio,
												 eartipo_processo,
												 eardesc_status,
												 earparametros 
												 )
										  VALUES( 
										         $this->usuarioID,
												 NOW(),
												 50, --PROCESSO BOLETAGEM MASSIVA
												 '".$param['msg']."',
												 '".$param['id_campanha']."|".$param['tipo_processo']."|'
									  			
										  	     ); ";
			
			if(!pg_query($this->conn, $sql)){
				throw new Exception('Falha ao inserir dados ao iniciar processo.');
			}
	
			return  true;
	
	}
	
	
	
	/**
	 * Recupera dados de um processo em andamento que ainda não foi concluído
	 *
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarProcessoAndamento(){
	
		$sql = " SELECT earoid, 
				        TO_CHAR(eardt_inicio, 'DD/MM/YYYY HH24:MI:SS') as eardt_inicio,
					    TO_CHAR(eardt_inicio, 'DD/MM/YYYY ') as data_inicio,
						TO_CHAR(eardt_inicio, 'HH24:MI:SS ') as hora_inicio,
					    eardt_termino,
						earstatus,
					    earusuoid,
						nm_usuario,
				        earparametros
    			   FROM execucao_arquivo
			 INNER JOIN usuarios ON cd_usuario = earusuoid
			      WHERE eardt_termino IS NULL
				    AND earstatus = 'f'
				    AND eartipo_processo = 50 --PROCESSO BOLETAGEM MASSIVA
			   ORDER BY earoid DESC
				  LIMIT 1 ";
			
		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar dados do processo de gerar campanha.');
		}
	
		if(pg_num_rows($result) > 0){
			return pg_fetch_object($result);
		}
	
		return false;
	
	}
	
	
	/**
	 * Atualiza a tabela com os dados de finalização (Sucesso ou Falha) do processo de geração de campanha,
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
					 AND eartipo_processo = 50 -- PROCESSO BOLETAGEM MASSIVA
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
	
		    $sql .= " AND eartipo_processo = 50 -- PROCESSO BOLETAGEM MASSIVA
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
	
	
	
	
	
	/**
	 * Recupera todos as UF's e nome dos etados brasileiros
	 * @throws Exception
	 */
	public function getUf(){
		
		$sql = " SELECT estuf AS uf, 
				        estuf || ' - ' || estnome AS estado
				   FROM estado
				  WHERE estexclusao IS NULL
				    AND estnome IS NOT NULL ";
		
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception('ERRO: <b>Falha ao buscar UFs.</b>');
		}
		
		return pg_fetch_all($rs);
		
		
	}
	
	
	/**
	 * Recupera os dados parametrizadoS no banco pelo dominio
	 * 
	 * @param integer $id
	 */
	public function getParamentos($id){
		
		$sql = " SELECT v.valvalor 
				   FROM dominio d 
			 INNER JOIN registro r ON r.regdomoid = d.domoid
			 INNER JOIN valor v ON  v.valregoid = r.regoid
			  	  WHERE d.domoid = 21  --VARIÁVEIS BOLETAGEM MASSIVA
					AND v.valoid = $id ";
		
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception('ERRO: <b>Falha ao buscar parametro.</b>');
		}
		
		return pg_fetch_all($rs);
		
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