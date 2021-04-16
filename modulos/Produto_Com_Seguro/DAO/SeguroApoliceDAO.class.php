<?php
/**
 * @file SeguroApoliceDAO.class.php
 * @author marcioferreira
 * @version 31/10/2013 11:23:31
 * @since 31/10/2013 11:23:31
 * @package SASCAR SeguroApoliceDAO.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

class SeguroApoliceDAO{

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $connApolice;


	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($connSiggo){

		$this->connApolice = $connSiggo;

	}
	
	/**
	 * Pesquisa se o usuário informado existe na base
	 * 
	 * @param int $id_usuario
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarUsuarioLogado($id_usuario){
		
		try {
			
			if($id_usuario){
		
				$sql = " SELECT cd_usuario
						   FROM usuarios
						  WHERE cd_usuario = $id_usuario  ";
			
				if (!$result = pg_query($this->connApolice, $sql)) {
					throw new Exception("Falha ao pesquisar usuário");
				}
			
				if (pg_num_rows($result) > 0) {
					return pg_fetch_object($result);
				}
			}
		
			return false;
		
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
	}
	
	
	/**
	 * Pesquisa se o cod de representante informado existe na base
	 *
	 * @param int $cod_representante
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarCodRepresentante($cod_representante){
	
		try {
				
			if($cod_representante){
	
				$sql = " SELECT repoid
						   FROM representante
					      WHERE repoid = $cod_representante ";
					
				if (!$result = pg_query($this->connApolice, $sql)) {
					throw new Exception("Falha ao pesquisar código de representante");
				}
					
				if (pg_num_rows($result) > 0) {
					return pg_fetch_object($result);
				}
			}
	
			return false;
	
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	
	}
	
	/**
	 * Pesquisa se o número da ordem de serviço informado existe na base
	 *
	 * @param int $num_orderm_servico
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarNumeroOrdermServico($num_orderm_servico){
	
		try {
	
			if($num_orderm_servico){
	
				$sql = " SELECT ordoid
                           FROM ordem_servico
                          WHERE ordoid = $num_orderm_servico ";
					
				if (!$result = pg_query($this->connApolice, $sql)) {
					throw new Exception("Falha ao pesquisar o número da ordem de serviço");
				}
					
				if (pg_num_rows($result) > 0) {
					return pg_fetch_object($result);
				}
			}
	
			return false;
	
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	
	}
	
	
	/**
	 * Recupera os dados do cliente do número de contrato informado no parâmetro
	 * 
	 * @param int $numeroContrato
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarContrato($numeroContrato){

		try {

			if(empty($numeroContrato)){
				throw new Exception('O número do contrato para pesquisa dos dados do cliente deve ser informado');
			}

			$sql = " SELECT clinome AS nome_cliente,
			                cliemail AS email_cliente
					   FROM contrato
			     INNER JOIN clientes ON conclioid = clioid
					  WHERE connumero = $numeroContrato ";

			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar dados do cliente pelo número contrato");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}
	

	/**
	 * Pesquisa proposta existente pelo número do contrato informado no parâmetro
	 * 
	 * @param int $num_contrato
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getPropostaContrato($num_contrato){
		
		try {
				
			if(empty($num_contrato)){
				throw new Exception('O número do contrato para pesquisa da proposta deve ser informado');
			}
				
			$sql = " SELECT pscoid AS cod_cotacao,
					        pscretcotacao AS numero_cotacao,
					        pspoid AS cod_proposta,
					        pspretproposta AS numero_proposta, 
					        pspdt_cadastro::DATE AS data_cadastro_proposta,
					        pscenvrevenda AS corretor_indicador
					   FROM produto_seguro_cotacao
				 INNER JOIN produto_seguro_proposta ON pscoid = psppscoid
					  WHERE pspconnumero = ".trim($num_contrato)." 
					    AND (pspretproposta IS NOT NULL AND pspretproposta <> 0) 		
						AND pspdt_exclusao IS NULL
				   ORDER BY pscoid DESC
					  LIMIT 1 ";
				
			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar proposta existente pelo número do contrato");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

			return false;
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

	}	

	/**
	 * Retorna contrato se o mesmo possuir status (0,10,11,1) 
	 * 
	 * @param int $num_contrato
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getApoliceStatus($num_contrato){
		
		try {
		
			if(empty($num_contrato)){
				throw new Exception('O número do contrato para pesquisar o status da apólice deve ser informado');
			}
		
			$sql = " SELECT psaconnumero
				       FROM produto_seguro_apolice 
				 INNER JOIN produto_seguro_status ON psapssoid = pssoid
				      WHERE psaconnumero = ".trim($num_contrato)."
				        AND psscodigo IN (0,10,11,1) 
				      LIMIT 1 ";
		
			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar status da apólice pelo número do contrato");
			}
		
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
		
			return false;
		
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	 * Retorna id do status 
	 * 
	 * @param int $cod_status
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getStatus($cod_status){
	
		try {
	
			if(!is_int($cod_status)){
				throw new Exception('O código do status deve ser informado');
			}
	
			$sql = "SELECT pssoid       AS id_status, 
					       psscodigo    AS cod_status, 
					       pssdescricao AS nome_status
					  FROM produto_seguro_status
					 WHERE pssdt_exclusao IS NULL 
					   AND psscodigo = $cod_status ";
	
			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar código do status");
			}
			
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	
	/**
	 * Recupera a quantidade de dias para estabelecer prazo de validade da proposta 
	 * 
	 * @param string $filtro_ambiente
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getDiasValidadeProposta($filtro_ambiente){
		
		try {
		
			if(empty($filtro_ambiente)){
				throw new Exception('Parâmetro para pesquisa de dias da validade da proposta não pode ser vazio');
			}
		
		    $sql = "SELECT pcsidescricao
					  FROM parametros_configuracoes_sistemas_itens
					 WHERE pcsipcsoid = '$filtro_ambiente'
					   AND pcsioid = 'prazo_validade_proposta' ";
		
			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar quantidade de dias da validade da proposta");
			}
		
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
		
			return false;
		
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * Insere dados da tentativa de envio de dados para gerar apólice,
	 * retornando o id para fazer update do retorno do WS ou em caso de erros
	 *
	 * @param Object $dadosEnvioWs
	 * @throws Exception
	 * @return string
	 */
	public function setDadosEnvioWs($dadosEnvioWs){
	
		try{
	
			if(!is_object($dadosEnvioWs)){
				throw new Exception('Os dados de envio para gerar apólice devem ser informados');
			}
			
			$sql = "INSERT INTO produto_seguro_apolice(
											  psaconnumero,
											  psaenvdt_instalacao,
											  psaenvdt_ativacao,
											  psaenvdt_inicomodato,
											  psaenvdt_fimcomodato,
											  psarepoid,
											  psausrlogado,
											  psaordoid,
											  psadt_cadastro,
											  psaorigemchamada,
											  psaorigemsistema  )
					                 VALUES (
					                          $dadosEnvioWs->num_contrato ";
					                          
					                    if(!empty($dadosEnvioWs->data_instalacao)){
					                    	$sql .= ",'$dadosEnvioWs->data_instalacao'";
					                    }else{
					                    	$sql .= ", NULL";
					                    }
					                   
					                    if(!empty($dadosEnvioWs->data_ativacao)){
					                    	$sql .= ",'$dadosEnvioWs->data_ativacao'";
					                    }else{
					                    	$sql .= ", NULL";
					                    }
					                    
					                    if(!empty($dadosEnvioWs->data_ini_comodato)){
					                    	$sql .= ",'$dadosEnvioWs->data_ini_comodato'";
					                    }else{
					                    	$sql .= ", NULL";
					                    }
					                    
					                    if(!empty($dadosEnvioWs->data_fim_comodato)){
					                    	$sql .= ",'$dadosEnvioWs->data_fim_comodato'";
					                    }else{
					                    	$sql .= ", NULL";
					                    }
					                          
					                  $sql .= ", $dadosEnvioWs->cod_representante
					                          , $dadosEnvioWs->usuario_logado
					                          , $dadosEnvioWs->ordem_servico
					                          , NOW()
					                          , '$dadosEnvioWs->origem_chamada'
					                          , '$dadosEnvioWs->origem_sistema'
								             
						
								) RETURNING psaoid ";
					                  
			if(!$result = pg_query($this->connApolice, $sql)){
				throw new Exception('Falha ao inserir dados de envio para gerar apólice no Web Service.');
			}

			return pg_fetch_result($result, 0, "psaoid");

		}catch (Exception $e){
			echo $e->getMessage();
		}
	}

	
	/**
	 * Atualiza com os dados de envio para Ws
	 *
	 * @param object $dadosEnvioWs
	 * @param int $id_envio_dados
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarDadosEnvioWs($dadosEnvioWs, $id_envio_dados){
	
		try{
	
			if(!is_object($dadosEnvioWs) && empty($id_envio_dados)){
				throw new Exception('Os dados de envio da apólice devem ser informados');
			}
	
			$sql = " UPDATE produto_seguro_apolice
						SET psadt_cadastro  = NOW() ";
		
			if($dadosEnvioWs->seguradora != NULL){
				$sql .=" ,psaemboid = '$dadosEnvioWs->seguradora' ";
			}
			
			if($dadosEnvioWs->cod_cotacao != NULL){
				$sql .=" ,psapscoid = '$dadosEnvioWs->cod_cotacao' ";
			}
			
			if($dadosEnvioWs->cod_proposta != NULL){
				$sql .=" ,psapspoid = '$dadosEnvioWs->cod_proposta' ";
			}
			
			if($dadosEnvioWs->id_revenda != NULL){
				$sql .=" ,psaenvrevenda = $dadosEnvioWs->corretor_indicador ";
			}
	
			if($dadosEnvioWs->nm_usuario != NULL){
				$sql .=" ,psaenvusuario = '$dadosEnvioWs->nm_usuario' ";
			}

			$sql .=" WHERE psaoid = $id_envio_dados ";
	
			if(!pg_query($this->connApolice, $sql)){
				throw new Exception('Falha ao atualizar dados de envio da apólice');
			}
	
			return true;
	
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	
   /**
    * Insere mensagens de erro ou sucesso após finalizar o processo de efetivação da apólice 
    * 
    * @param int $cod_mensagem
    * @param int $id_envio_dados
    * @throws Exception
    */
	public function setMensagensProcesso($cod_mensagem, $id_envio_dados, $dados_adicionais = NULL){
		
		try{

			if(empty($cod_mensagem)){
				throw new Exception('O ID da mensagem deve ser informado para inserir mensagens do processo');
			}
				
			if(empty($id_envio_dados)){
				throw new Exception('O ID dos dados de envio para o WS deve ser informado');
			}

			$sql = " INSERT INTO produto_seguro_apolice_processo 
					             ( psappsaoid, 
					               psappsmoid, 
					               psapinfoadicionais,
					               psapdt_cadastro)
						  VALUES ( $id_envio_dados, 
					               $cod_mensagem,
					               '$dados_adicionais',
					               NOW() ) ";
			
			if(!pg_query($this->connApolice, $sql)){
				throw new Exception('Falha ao inserir mensagens do processo');
			}
			
			return true;

		}catch (Exception $e){
			echo $e->getMessage();
		}
		
	}
	
	
	/**
	 * Atualiza com os dados de retorno do Ws
	 *
	 * @param object $dadosRetornoWs
	 * @param int $id_envio_dados
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarDadosRetornoWs($dadosRetornoWs, $id_envio_dados){
	
		try{
	
			if(!is_object($dadosRetornoWs) && empty($id_envio_dados)){
				throw new Exception('Os dados de retorno da apólice devem ser informados');
			}
	
			$sql = " UPDATE produto_seguro_apolice
						SET psadt_cadastro  = NOW() ";
	
			if($dadosRetornoWs->id_status != NULL){
				$sql .=" ,psapssoid = $dadosRetornoWs->id_status ";
			}
							
			if($dadosRetornoWs->id_revenda != NULL){
				$sql .=" ,psaretrevenda = $dadosRetornoWs->id_revenda ";
			}
				
			if($dadosRetornoWs->nm_usuario != NULL){
				$sql .=" ,psaretusuario = '$dadosRetornoWs->nm_usuario' ";
			}
			
			if($dadosRetornoWs->id_apolice != NULL){
				$sql .=" ,psaretapolice = $dadosRetornoWs->id_apolice ";
			}
			
			if($dadosRetornoWs->cd_apolice != NULL){
				$sql .=" ,psaretapolicecd = '$dadosRetornoWs->cd_apolice' ";
			}
			
			if($dadosRetornoWs->id_endosso != NULL){
				$sql .=" ,psaretendosso = $dadosRetornoWs->id_endosso ";
			}
	
			if($dadosRetornoWs->xml_envio != NULL || $dadosRetornoWs->xml_envio != ""){
				$sql .=" ,psaxmlenvio = '$dadosRetornoWs->xml_envio' ";
			}
			
			if($dadosRetornoWs->xml_retorno != NULL || $dadosRetornoWs->xml_retorno != ""){
				$sql .=" ,psaxmlretorno = '$dadosRetornoWs->xml_retorno' ";
			}
			
			$sql .=" WHERE psaoid = $id_envio_dados ";
	
			if(!pg_query($this->connApolice, $sql)){
				throw new Exception('Falha ao atualizar dados de retorno da apólice');
			}
	
			return true;
	
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	/**
	 * 
	 * 
	 * @param int $numeroContrato
	 * @param int $id_envio_dados
	 * @throws Exception
	 * @return boolean
	 */
	public function setDataVigenciaInstalacao($numeroContrato, $id_envio_dados,$flagVigencia){
		
		try{
		
			if(empty($numeroContrato)){
				throw new Exception('O número do contrato deve ser informado para atualizar a data de vigência e instalação');
			}
			
			if($flagVigencia === true) {
				//atualiza o contrato
				$sql_contrato = " UPDATE contrato 
						             SET condt_ini_vigencia = NOW()::DATE, 
						                 condt_instalacao = NOW()::DATE
						           WHERE connumero = $numeroContrato ";
			
				if(!pg_query($this->connApolice, $sql_contrato)){
					throw new Exception('Falha ao atualizar data de vigência e instalação do contrato');
				}
			}
			
			//atualiza a apólice
			$sql_apolice = " UPDATE produto_seguro_apolice
							    SET psadt_inivigencia = NOW()::DATE, 
							        psadt_fimvigencia = NOW()::DATE + interval '1 year' 
						      WHERE psaoid = $id_envio_dados ";
			
			if(!pg_query($this->connApolice, $sql_apolice)){
				throw new Exception('Falha ao atualizar data de vigência e instalação da apólice');
			}
		
			return true;
		
		}catch (Exception $e){
			echo $e->getMessage();
		}
		
	}
	
	
	
	/**
	 * 
	 * @param int $seguradora
	 * @param sring $filtro_ambiente
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getEmailEnvioErro($seguradora, $filtro_ambiente){
		
		try {
		
			if(empty($filtro_ambiente)){
				throw new Exception('Parâmetro de filtro para pesquisa de e-mails para envio de erros não pode ser vazio');
			}
			
			if(empty($seguradora)){
				throw new Exception('A seguradora deve ser informada para pesquisar e-mails para envio de erros');
			}
			
			$sql = " SELECT pcsioid, 
			                pcsidescricao AS email
					   FROM parametros_configuracoes_sistemas_itens    
			     INNER JOIN parametros_configuracoes_sistemas on pcsoid = pcsipcsoid  
					  WHERE pcsipcsoid = '$filtro_ambiente'
					    AND pcsivinculo = '$seguradora'
					    AND pcsioid ILIKE 'email_apolice%'
					    AND pcsidt_exclusao IS NULL
					    AND pcsdt_exclusao IS NULL";
		
			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar e-mails para envio de erros");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	 * Recupera layout de e-mail
	 * 
	 * @param string $tituloLayout
	 * @param string $cabecalho
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getLayoutEmail($tituloLayout, $cabecalho){

		try {

			if(empty($tituloLayout)){
				throw new Exception('Título do layout do e-mail para envio de erros não pode ser vazio');
			}

			if(empty($cabecalho)){
				throw new Exception('Cabeçalho do layout do e-mail para envio de erros não pode ser vazio');
			}
			
			$sql = " SELECT sf.seefdescricao    AS funcionalidade
				            , se.seecorpo       AS corpo_email
				            , se.seecabecalho   AS assunto_email
				            , sv.srvlocalizador AS servidor
  					   FROM servico_envio_email se
				 INNER JOIN servico_envio_email_funcionalidade sf ON sf.seefoid = se.seeseefoid
				 INNER JOIN servico_envio_email_titulo st ON st.seetoid = se.seeseetoid
				 INNER JOIN servidor_email sv ON seesrvoid = srvoid
 					  WHERE sf.seefdescricao = 'Produto com Seguro'
     				    AND st.seetdescricao = '$tituloLayout' 
     					AND se.seecabecalho = '$cabecalho'
     					AND se.seedt_exclusao IS NULL
     					AND sf.seefdt_exclusao IS NULL
   					  LIMIT 1 ";

			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar e-mails para envio de erros");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	 * 
	 * @param int $connumero
	 * @throws Exception
	 * @return boolean
	 */
	public function getProdutoSeguro($connumero){
		
		try {
		
			if(empty($connumero)){
				throw new Exception('Obrigatório informar numero O.S.');
			}
			
			$sql = "SELECT 
						ordeqcoid AS id_classe,
						ordconnumero AS id_contrato,
						TO_CHAR(condt_ini_vigencia,'dd-mm-yyyy') AS dt_instalacao,
						TO_CHAR(condt_instalacao,'dd-mm-yyyy') AS dt_ativacao
					FROM ordem_servico
					INNER JOIN equipamento_classe_beneficio  ON  ordeqcoid = eqcbeqcoid
					INNER JOIN empresa_beneficio_tipo ON eqcbebtoid = ebtoid
					INNER JOIN empresa_beneficio ON emboid = ebtemboid
					INNER JOIN contrato ON ordconnumero = connumero
					WHERE connumero = $connumero
					AND ebtdescricao = 'SEGURO'
					AND embdt_exclusao IS NULL
					AND embdt_exclusao IS NULL
					AND eqcbdt_exclusao IS NULL";
		
			if (!$result = pg_query($this->connApolice, $sql)) {
				throw new Exception("Falha ao pesquisar apolice");
			}

			if (pg_num_rows($result) > 0) {
				return true;
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return false;
	}
	

	/**
	 * inicia transação com o BD
	 */
	public function begin()	{
		$rs = pg_query($this->connApolice, "BEGIN;");
	}
	
	/**
	 * confirma alterações no BD
	 */
	public function commit(){
		$rs = pg_query($this->connApolice, "COMMIT;");
	}
	
	/**
	 * desfaz alterações no BD
	 */
	public function rollback(){
		$rs = pg_query($this->connApolice, "ROLLBACK;");
	}
	
}