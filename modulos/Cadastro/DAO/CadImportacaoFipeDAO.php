<?php
/**
 * Gerencia os dados a serem importados no BD tabela Fipe
 * 
 * @file CadImportacaoFipeDAO.php
 * @author marcioferreira
 * @version 01/10/2013 11:31:30
 * @since 01/10/2013 11:31:30
 * @package SASCAR CadImportacaoFipeDAO.php
  */

class CadImportacaoFipeDAO{
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $conn;
	
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
	}
    
	/**
	 * Grava dados de  inicio no processo de importação,
	 * esses dados são parâmetrizados para serem usados no momento de rodar em background
	 * 
	 * @param string $tipo_veiculo
	 * @param string $file_name
	 * @throws Exception
	 * @return boolean
	 */
	public function iniciarProcesso(/*$tipo_veiculo = NULL,*/ $file_name, $tipo_import){
	
		try{
			
			if(!empty($file_name) && !empty($tipo_import)){
					
				if($tipo_import == 'dadosFipe'){
					$str_tipo_import = 'Tabela FIPE';
					
				}elseif($tipo_import == 'tarifa'){
					$str_tipo_import = 'Categoria Tarifaria';
				}
				
				$sql = " INSERT INTO execucao_import_fipe ( eifusuoid,
						                                    eifret_mensagem,
						                                    --eiftipo_veiculo,
						                                    eifarquivo,
						                                    eiftipo_importacao )
			                                        VALUES( $this->usuarioID,
				                                            'Processo iniciado',
				                                            --'".trim($tipo_veiculo)."',
				                                            '".trim($file_name)."',
															'$str_tipo_import' ) ";
					
				if(!pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir dados ao iniciar processo.');
				}
				
				return true;
			}
		
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Atualiza a tabela com os dados de finalização (Sucesso ou Falha) do processo de importação,
	 * são recuperados mais tarde e enviados por e-mail.
	 * Libera o envio de outro arquivo para importação
	 * 
	 * @param bolean $param
	 * @param string $msg
	 * @throws Exception
	 * @return boolean
	 */
	public function finalizarProcesso($param, $msg){
		
		try{
				
			if($param){
				$eifconclusao = 'S';
				$eifret_mensagem = 'Processo finalizado com sucesso';
			}else{
				$eifconclusao = 'N';
				$eifret_mensagem = $msg;
			}
					
			$sql = " UPDATE execucao_import_fipe
						SET eifdt_termino = NOW(), 
					        eifconclusao = '$eifconclusao', 
					        eifret_mensagem = '$eifret_mensagem'
					  WHERE eifdt_inicio::DATE = NOW()::DATE 
			            AND eifconclusao = 'N' 
			            AND eifdt_termino IS NULL  ";
				
			if(!pg_query($this->conn, $sql)){
				throw new Exception('Falha ao finalizar processo.');
			}
	
			return true;
			
		
		}catch(Exception $e){
			return $e->getMessage();
		}
		
	}
	
	/**
	 * Verifica se existe um processo que foi iniciado e não terminado 
	 * 
	 * @throws Exception
	 * @return object|boolean
	 */
	public function consultarDadosImportacao(){
		
		try{
		
			$sql = " SELECT eifoid,
					        eifusuoid,
					        --eiftipo_veiculo AS eiftipo_veiculo, 
					        eifarquivo AS eifarquivo,
					        eiftipo_importacao AS eiftipo_importacao	
					   FROM execucao_import_fipe
					  WHERE eifdt_termino IS NULL 
					    AND eifret_mensagem = 'Processo iniciado'
				   ORDER BY eifoid DESC
					  LIMIT 1";
				
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados de importação.');
			}
				
			if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
			}
	
			return false;
		
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
		
	}
	
	/**
	 * Recupera dados de um processo em andamento que ainda não foi concluído
	 * 
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarProcessoAndamento(){
		
		try{
				
			$sql = " SELECT TO_CHAR(eifdt_inicio, 'DD/MM/YYYY HH24:MI:SS') as eifdt_inicio,
					        eifdt_termino,
							eifconclusao
					   FROM execucao_import_fipe
					  WHERE eifdt_termino IS NULL 
					    AND eifconclusao = 'N'
				   ORDER BY eifoid DESC
					LIMIT 1";
				
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados de importação.');
			}
				
			if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
			}
		
			return false;
		
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
		
	}
	
	/**
	 * Retornar dados de um processo já finalizado
	 * 
	 * @throws Exception
	 * @return object|boolean
	 */
	public function verificarProcessoFinalizado(){
		
		try{
			
			$sql = " SELECT eifusuoid AS id_usuario,
					        TO_CHAR(eifdt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS inicio,
                		    TO_CHAR(eifdt_termino, 'DD/MM/YYYY HH24:MI:SS') AS termino
    				   FROM execucao_import_fipe
				   ORDER BY eifoid DESC
   					  LIMIT 1  ";
		
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar dados de processo finalizado.');
			}
		
			if(pg_num_rows($result) > 0){
				return pg_fetch_object($result);
			}
		
			return false;
		
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Verifica se a marca de veículo existe na tabela -> marca
	 * 
	 * @param string $marca
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getMarca($marca){

		try{

			if(!empty($marca)){
					
				$sql = " SELECT mcaoid 
						   FROM marca
						  WHERE mcamarca = '".trim($marca)."'
						 	AND mcastatus = 't' ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao pesquisar marca.');
				}
					
				if(pg_num_rows($result) > 0){
					return pg_fetch_object($result);
				}
				
				return false;
			}

		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Insere marca na tabela marca retornando o id
	 * 
	 * @param string $novaMarca
	 * @throws Exception
	 * @return boolean
	 */
	public function setMarca($novaMarca){

		try{

			if(!empty($novaMarca)){
					
				$sql = "  INSERT INTO marca ( 
						                       mcamarca
						                     , mcadt_cadastro
						                     , mcastatus
						                     , mcausuoid_inclusao ) 
                                     VALUES (
						                      '".trim($novaMarca)."'
											 , NOW()
											 , 't'
						                     , $this->usuarioID ) 
				                   RETURNING mcaoid ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir nova marca.');
				}
	
				return pg_fetch_object($result);
			}

		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Verifica se o código fipe já existe na tabela -> modelo
	 *
	 * @param string $novoCodFipe
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodFipe($codFipe){
	
		try{
	
			if(!empty($codFipe)){
					
				$sql = " SELECT mlooid, mlofipe_codigo
						   FROM modelo 
						  WHERE mlofipe_codigo = '".trim($codFipe)."' 
							AND mlostatus = 't' ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao pesquisar código Fipe.');
				}
					
				if(pg_num_rows($result) > 0){
					return pg_fetch_object($result);
				}
	
				return false;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Verifica se o modelo de veículo existe na tabela -> modelo
	 *
	 * @param string $modelo
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getModelo($modelo){

		try{
	
			if(!empty($modelo)){
					
				$sql = " SELECT mlooid
						   FROM modelo 
						  WHERE mlomodelo = '".trim($modelo->descricao)."'
							AND mlostatus = 't'  
						    AND mlodt_exclusao IS NULL 
						  	AND mlofipe_codigo IS NOT NULL	";
				
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao pesquisar modelo.');
				}
					
				if(pg_num_rows($result) > 0){
					return pg_fetch_object($result);
				}
	
				return false;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Insere o modelo na tabela modelo
	 *
	 * @param string $novoModelo
	 * @throws Exception
	 * @return boolean
	 */
	public function setModelo($novoModelo){
		
		try{

			if(!empty($novoModelo)){
					
				$sql = "  INSERT INTO modelo (
											  mlomodelo
											, mlomcaoid
											, mlodt_cadastro
											--, mlotipveioid
											, mlostatus
											, mlofipe_codigo
											, mlousuoid_inclusao )
									VALUES (
											 '".trim($novoModelo->descricao)."'
											, $novoModelo->cod_marca
											, NOW()
											--, $novoModelo->cod_tipo_veiculo
											, 't'
											, '".trim($novoModelo->cod_fipe)."'
											, $this->usuarioID ) 

				                  RETURNING mlooid ";
							
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir modelo.');
				}

				return pg_fetch_object($result);
			}

		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Recupera o id do ano da tabela modelo_ano
	 *
	 * @param string $ano
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodAno($ano){
	
		try{
	
			if(!empty($ano)){
					
				$sql = " SELECT mdaoid
						   FROM modelo_ano
						  WHERE mdadescricao = '".trim($ano)."'
							AND mdadt_exclusao IS NULL ";
						
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao pesquisar código do ano.');
				}
					
				if(pg_num_rows($result) > 0){
					return pg_fetch_object($result);
				}
	
				return false;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Insere um ano caso não existe
	 * 
	 * @param string $ano
	 * @throws Exception
	 * @return object
	 */
	public function setCodAno($ano){

		try{

			if(!empty($ano)){
				
				$sql = " INSERT INTO modelo_ano ( mdadescricao
						                          ,mdadt_cadastro
						                          ,mdausuoid_inclusao )
                                         VALUES ( '".trim($ano)."'
                                         		  ,NOW()
                                         		  ,$this->usuarioID )
                                       RETURNING mdaoid	 ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir dados do modelo ano combustível.');
				}
					
				return pg_fetch_object($result);
			}

		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}

	
	/**
	 * Recupera o id do combustível da tabela modelo_combustivel
	 *
	 * @param string $combustivel
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodCombustivel($combustivel){
	
		try{
	
			if(!empty($combustivel)){
					
				$sql = " SELECT mdcoid
						   FROM modelo_combustivel
						  WHERE mdcdescricao =  '".trim($combustivel)."'
							AND mdcdt_exclusao IS NULL ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao pesquisar código do combustível.');
				}
					
				if(pg_num_rows($result) > 0){
					return pg_fetch_object($result);
				}
	
				return false;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Verifica se já exites um cadastro para um modelo ano e valor encontrados no arquivo
	 * 
	 * @param object $dadosModelo
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodModeloAnoCombustivel($dadosModelo){
		
		try{
	
			if(is_object($dadosModelo)){
				
				$sql = " SELECT mdacoid 
						   FROM modelo_ano_combustivel
						  WHERE mdacmlooid   = $dadosModelo->cod_modelo             
						    AND mdacmdaoid   = $dadosModelo->cod_modelo_ano          
						    AND mdacmdcoid   = $dadosModelo->cod_modelo_combustivel 
							AND mdacvalor    = '".trim($dadosModelo->valor_modelo)."' 
							AND mdacdt_exclusao IS NULL ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao pesquisar código do modelo ano combustível.');
				}
					
				if(pg_num_rows($result) > 0){
					return pg_fetch_object($result);
				}
	
				return false;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Insere um cadastro de valor médio encontrado na tabela Fipe
	 * 
	 * $dadosModelo->cod_modelo             -- FK da tabela modelo
	 * $dadosModelo->cod_modelo_ano         -- FK da tabela modelo_ano
	 * $dadosModelo->cod_modelo_combustivel -- FK da tabela modelo_combustivel 
	 *
	 * @param object $dadosModelo
	 * @throws Exception
	 * @return object
	 */
	public function setCodModeloAnoCombustivel($dadosModelo){
		
		try{

			if(is_object($dadosModelo)){

				$sql = " INSERT INTO modelo_ano_combustivel ( 
							                                 mdacmlooid, 
							                                 mdacmdaoid, 
							                                 mdacmdcoid, 
							                                 mdacvalor,
							                                 mdacdt_cadastro, 
							                                 mdacusuoid_inclusao )
	                                                VALUES ( $dadosModelo->cod_modelo
							                                 ,$dadosModelo->cod_modelo_ano
							                                 ,$dadosModelo->cod_modelo_combustivel
							                                 ,".trim($dadosModelo->valor_modelo)."
							                                 ,NOW()
							                                 ,$this->usuarioID )
					                            
					                                RETURNING mdacoid ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir dados do modelo ano combustível.');
				}
					
				return pg_fetch_object($result);
			}

		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Insere número de passageiros se houver, na linha e coluna correspondente do arquivo
	 * 
	 * @param object $dadosPassag
	 * @throws Exception
	 * @return object
	 */
	public function setNumPassageiros($dadosPassag){

		try{

			if(is_object($dadosPassag)){
				
				$sql = " UPDATE modelo
							SET mlonumpassag = ".trim($dadosPassag->quant_passageiros)."
						  WHERE mlooid   = ".trim($dadosPassag->cod_modelo)."  ";
				
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir quantidade de passageiros.');
				}
					
				return true;
			}

		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Insere o tipo de veículo, da linha e coluna correspondente do arquivo
	 *
	 * @param object $dadosTipoVeiculo
	 * @throws Exception
	 * @return object
	 */
	public function setTipoVeiculo($dadosTipoVeiculo){
	
		try{
	
			if(is_object($dadosTipoVeiculo)){
	
				$sql = " UPDATE modelo
							SET mlotipveioid = ".trim($dadosTipoVeiculo->cod_tipo_veiculo)."
						  WHERE mlooid   = ".trim($dadosTipoVeiculo->cod_modelo)."  ";
	
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao inserir tipo de veiculo.');
				}
					
				return true;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Atualiza os modelos com os dados da categoria tarifaria
	 *
	 * @param object $dadosProcedencia
	 * @throws Exception
	 * @return object
	 */
	public function setDadosCategoriaTarifaria($dados){
	
		try{
	
			if(is_object($dados)){
	
				if(!empty($dados->nome_procedencia)){
					$coluna = ' mloprocedencia';
					$valor = "'$dados->nome_procedencia'";
				}
				
				if(!empty($dados->nome_categoria_base)){
					$coluna = ' mlocatbase_descricao';
					$valor = "'$dados->nome_categoria_base'";
				}
				
				if(!empty($dados->cod_categoria_base)){
					$coluna = ' mlocatbase_codigo';
					$valor = "'$dados->cod_categoria_base'";
				}
				
				$sql = " UPDATE modelo
							SET $coluna = ".trim($valor)."
						  WHERE mlooid   = ".trim($dados->cod_modelo)." ";

				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Falha ao atualizar categoria tarifaria do modelo.');
				}
					
				return true;
			}
	
		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Retorna o id do tipo de veículo informado
	 * 
	 * @param object $cod_tipo
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getTipoVeiculo($cod_tipo){
		
		try{
				
			$sql = " SELECT tipvoid AS cod_tipo_veiculo
					   FROM tipo_veiculo 
					  WHERE tipvoid = ".trim($cod_tipo)."
					    AND tipvexclusao IS NULL";
				
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Falha ao pesquisar codigo do tipo de veiculo.');
			}
				
			if(pg_num_rows($result) > 0){

				return pg_fetch_object($result);
			}

			return false;


		}catch(Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	public function getEmailUsuarioParametro(){
		
		try{
		
			$sql = "SELECT pcsidescricao AS usuemail, 
					       pcsioid
	  				FROM
						parametros_configuracoes_sistemas,
						parametros_configuracoes_sistemas_itens
	 				WHERE
						pcsoid = pcsipcsoid
				    AND pcsdt_exclusao is null
					AND pcsidt_exclusao is null
					AND pcsipcsoid = 'IMPORT_TABELA_FIPE_EMAIL'  ";
		
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception ("Falha ao recuperar email do usuario tabela parametros ");
			}
		
			if(count($result) > 0){
				return pg_fetch_all($result);
			}
		
		}catch(Exception $e){
			return $e->getMessage();
		}
		
	}
	
	
	public function getDadosUsuarioProcesso($id_usuario){
		
		try{
		
			$sql = "SELECT nm_usuario, usuemail 
					  FROM usuarios 
					 WHERE cd_usuario = $id_usuario  ";
		
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception ("Falha ao recuperar email do usuario dio processo ");
			}
		
			if(count($result) > 0){
				return pg_fetch_all($result);
			}
		
		}catch(Exception $e){
			return $e->getMessage();
		}
		
		
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
					AND pcsioid = 'EMAIL' ";
	
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