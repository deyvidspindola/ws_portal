<?php
/**
 * @file SeguroCotacaoDAO.class.php
 * @author marcioferreira
 * @version 31/10/2013 11:23:46
 * @since 31/10/2013 11:23:46
 * @package SASCAR SeguroCotacaoDAO.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

class SeguroCotacaoDAO{

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $connCotacao;


	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($connSiggo){

		$this->connCotacao = $connSiggo;

	}

	
	
	/**
	 * Recuperar dados da categoria tarifária de acordo o código Fipe informado
	 * 
	 * @param string $codFipe
	 * @throws Exception
	 * @return object
	 */
	public function getCategoriaTarifariaCodFipe($codFipe){
		
		try{
			
			if(empty($codFipe)){
				throw new Exception('O código fipe não pode ser nulo para recuperar a categoria tarifária');
			}
				
			$sql = " SELECT mlocatbase_codigo    AS tarifa_codigo,
							mloprocedencia       AS tarifa_procedencia
					   FROM modelo
				   	  WHERE mlofipe_codigo = '$codFipe' --<codFipe> (recebido como parâmetro de entrada)
						AND mlodt_exclusao IS NULL
						AND mlostatus = 't' ";

			if (!$result = pg_query($this->connCotacao, $sql)) {
				throw new Exception("Falha ao pesquisar categoria tarifária por código Fipe");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);		
			}
			
		}catch (Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Recuperar dados da categoria tarifária de acordo a finalidade e a procedência informados
	 * 
	 * @param int $finalidadeUso
	 * @param string $procedencia
	 * @throws Exception
	 * @return object
	 */
	public function getCategoriaTarifariaDadosModelo($finalidadeUso, $procedencia){
		
		try{

			if(empty($finalidadeUso)){
				throw new Exception('A finalidade de uso deve ser informada para recuperar a categoria tarifária');
			}

			if(empty($procedencia)){
				throw new Exception('A procedência do veículo deve ser informada para recuperar a categoria tarifária');
			}
				
			$sql = "  SELECT psctcodigo AS tarifa_codigo
					    FROM produto_seguro_categoriatarifaria
					   WHERE psctfinalidade = $finalidadeUso  --<finalidade_uso_veiculo> (recebido como parâmetro de entrada)
						 AND psctprocedencia = '$procedencia' --<mloprocedencia> (tabela modelo)
						 AND psctdt_exclusao IS NULL  ";

			if (!$result = pg_query($this->connCotacao, $sql)) {
				throw new Exception("Falha ao pesquisar categoria tarifária por finalidade e procedência do veículo");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

		}catch (Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	/**
	 * Seta dados de envio para WS 
	 * 
	 * @param object $dadosEnvio
	 * @throws Exception
	 * @return object
	 */
	public function setDadosEnvioWs($dadosEnvioWs){

		try{

			if(!is_object($dadosEnvioWs)){
				throw new Exception('Os dados de envio devem ser informados');
			}

			$sql = " INSERT INTO 
					        produto_seguro_cotacao ( pscemboid,
					                                 pscenvrevenda,
					                                 pscenvusuario,
					                                 pscenvtipopessoa, 
												     pscenvcpfcnpj, 
												     pscenvnumcep, 
												     pscenvveicodfipe, 
												     pscenvveiano, 
												     pscenvveizero, 
												     pscenvveicombustivel,
												     pscenvcategtarif,
													 pscdt_cadastro,
                                                     pscid_produto_cobertura,
                                                     pscvl_lmi_cobertura,
                                                     pscvl_franquia_cobertura
                                                    )
										    
					                        VALUES ( $dadosEnvioWs->id_seguradora,
					                                 $dadosEnvioWs->corretor,
					                                 '$dadosEnvioWs->nm_usuario',
					                                 $dadosEnvioWs->tipo_pessoa,
					                                 '$dadosEnvioWs->cpf_cgc',
					                                 $dadosEnvioWs->cep,
					                                 '$dadosEnvioWs->cod_fipe',
					                                 $dadosEnvioWs->ano_veiculo,
					                                 $dadosEnvioWs->carro_zero,
					                                 $dadosEnvioWs->tipo_combustivel,
					                                 '$dadosEnvioWs->categoria_tarifaria',
					                                 'NOW',
                                                     '$dadosEnvioWs->id_produto_cobertura',
                                                     '$dadosEnvioWs->vl_lmi_cobertura',
                                                     '$dadosEnvioWs->vl_franquia_cobertura'
					                               ) RETURNING pscoid ";
			
			if(!$result = pg_query($this->connCotacao, $sql)){
				throw new Exception('Falha ao inserir dados de envio para Web Service.');
			}

			return pg_fetch_result($result, 0, "pscoid");

		}catch (Exception $e){
			echo $e->getMessage();
			exit();
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
				throw new Exception('Os dados de retorno devem ser informados para gravar');
			}

			$sql = " UPDATE produto_seguro_cotacao
						SET pscdt_cadastro  = NOW() ";
			
			if($dadosRetornoWs->id_revenda != NULL){			
				   $sql .=" ,pscretrevenda = $dadosRetornoWs->id_revenda ";
			}   

			/*if($dadosRetornoWs->corretor != NULL){			
				   $sql .=" ,pscretrevenda = '$dadosRetornoWs->corretor' ";
			}*/

			if($dadosRetornoWs->nm_usuario != NULL){
				   $sql .=" ,pscretusuario = '$dadosRetornoWs->nm_usuario' ";
			}

			if($dadosRetornoWs->nr_cotacao_i4pro != NULL){
				   $sql .=" ,pscretcotacao = $dadosRetornoWs->nr_cotacao_i4pro ";
			}
					
			if($dadosRetornoWs->vl_premio_tarifario != NULL){
				   $sql .=" ,pscretvlpremio = $dadosRetornoWs->vl_premio_tarifario ";
			}
					
		    if($dadosRetornoWs->cd_retorno != NULL || $dadosRetornoWs->cd_retorno === 0){	   
				   $sql .=" ,pscretcodigo = ".(int)$dadosRetornoWs->cd_retorno." ";
		    }
					
			if($dadosRetornoWs->nm_retorno != NULL){	   
				   $sql .=" ,pscretdescricao = '$dadosRetornoWs->nm_retorno' ";
			}

			if($dadosRetornoWs->xml_envio != NULL){
				    $sql .=",pscxmlenvio = '$dadosRetornoWs->xml_envio' ";
			}
			
		    if($dadosRetornoWs->xml_retorno != NULL || $dadosRetornoWs->xml_retorno != ""){
				    $sql .=",pscxmlretorno = '$dadosRetornoWs->xml_retorno' ";
			}
						   
			$sql .=" WHERE pscoid = $id_envio_dados ";

			if(!pg_query($this->connCotacao, $sql)){
				throw new Exception('Falha ao atualizar dados da cotação');
			}

			return true;

		}catch (Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * inicia transação com o BD
	 */
	public function begin()	{
		$rs = pg_query($this->connCotacao, "BEGIN;");
	}
	
	/**
	 * confirma alterações no BD
	 */
	public function commit(){
		$rs = pg_query($this->connCotacao, "COMMIT;");
	}
	
	/**
	 * desfaz alterações no BD
	 */
	public function rollback(){
		$rs = pg_query($this->connCotacao, "ROLLBACK;");
	}
	

	
}