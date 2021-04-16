<?php
/** 
 * @file SeguroPropostaDAO.class.php
 * @author marcioferreira
 * @version 31/10/2013 11:24:06
 * @since 31/10/2013 11:24:06
 * @package SASCAR SeguroPropostaDAO.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

class SeguroPropostaDAO{

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $connProposta;


	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($connSiggo){

		$this->connProposta = $connSiggo;

	}
	
	/**
	 * Verifica se já existe uma prosposta cadastrada para os dados passados no parâmetro 
	 * 
	 * @param int $seguradora
	 * @param int $num_contrato
	 * @param int $num_cotacao
	 * @param string $placa
	 * @param string $chassi
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function verificarPropostaExistente($dadosProposta){
		
		try {
			
			if(!is_object($dadosProposta)){
				throw new Exception('Os dados para pesquisar a proposta existente devem ser informados');
			}
			
			$sql = " SELECT pspretcodigo, pspretdescricao, pspretproposta, pspenvrevenda       
					   FROM produto_seguro_proposta 
					  WHERE pspemboid = $dadosProposta->seguradora
					    AND pspconnumero = $dadosProposta->num_contrato
					    AND psppscoid = $dadosProposta->num_cotacao
					    AND pspenvveichassi = '$dadosProposta->chassi'
					    AND pspenvveiplaca = '$dadosProposta->placa' 
					    AND pspretcodigo = 0
					    AND pspretproposta IS NOT NULL 
					    AND pspretproposta <> 0
					    AND pspdt_exclusao IS NULL  ";
				
			if (!$result = pg_query($this->connProposta, $sql)) {
				throw new Exception("Falha ao pesquisar proposta existente");
			}
				
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
				
			return false;
			
		} catch (Exception $e) {
			echo $e->getMessage();
			exit;			
		}
		
	}
	
	
	/**
	 * Verifica se o número do contrato passado por parâmetro existe
	 * 
	 * @param int $num_contrato
	 * @throws Exception
	 * @return object|boolean
	 */
	public function validarContratoExistente($num_contrato){

		try {

			if(empty($num_contrato)){
				throw new Exception('O número do contrato deve ser informado');
			}

			$sql = " SELECT connumero  
					   FROM contrato 
					  WHERE connumero = ".trim($num_contrato)." ";
			
			if (!$result = pg_query($this->connProposta, $sql)) {
				throw new Exception("Falha ao pesquisar número do contrato");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}
	}
	
	
	/**
	 * Insere dados da tentativa de envio de dados para gerar proposta,
	 * retornando o id para fazer update do retorno do WS
	 * 
	 * @param Object $dadosEnvioWs
	 * @throws Exception
	 * @return string
	 */
	public function setDadosEnvioWs($dadosEnvioWs){
		
		try{
		
			if(!is_object($dadosEnvioWs)){
				throw new Exception('Os dados de envio para gerar proposta devem ser informados');
			}
		
			$sql = "INSERT INTO produto_seguro_proposta(
								            psppscoid, 
								            pspemboid, 
								            pspconnumero, 
								            pspenvrevenda, 
								            pspenvusuario, 
								            pspenvclitipo, 
								            pspenvclinome, 
								            pspenvclisexo, 
								            pspenvcliestadocivil, 
								            pspenvclidtnascimento, 
								            pspenvclippenome1, 
								            pspenvclippenome2, 
								            pspenvcliresddd, 
								            pspenvclirestelefone, 
								            pspenvclicelddd, 
								            pspenvcliceltelefone, 
								            pspenvcliemail, 
								            pspenvcliendereco, 
								            pspenvcliendnumero, 
								            pspenvcliendcomplemento, 
								            pspenvcliendcidade, 
								            pspenvclienduf, 
								            pspenvcliprofissao, 
								            pspenvveiplaca, 
								            pspenvveichassi, 
								            pspenvveiutilizacao, 
								            pspenvformapagamento, 
								            pspdt_cadastro )
								 VALUES ( 
								  			$dadosEnvioWs->cod_cotacao
											,$dadosEnvioWs->seguradora 
											,$dadosEnvioWs->num_contrato 
											,$dadosEnvioWs->id_revenda   
											,'$dadosEnvioWs->nm_usuario'  
											,$dadosEnvioWs->tipo_seguro   
											,'$dadosEnvioWs->cliente_nome'  
											,$dadosEnvioWs->cliente_sexo  
											,$dadosEnvioWs->estado_civil  
											,'$dadosEnvioWs->dt_nasc'      
											,'$dadosEnvioWs->pep1'       
											,'$dadosEnvioWs->pep2'        
											,$dadosEnvioWs->ddd_fone_res 
											,$dadosEnvioWs->fone_res     
											,$dadosEnvioWs->ddd_celular  
											,$dadosEnvioWs->num_celular  
											,'$dadosEnvioWs->cliente_email' 
											,'$dadosEnvioWs->cliente_end'  
											,'$dadosEnvioWs->end_numero'
											,'$dadosEnvioWs->complemento'
											,'$dadosEnvioWs->cidade'       
											,$dadosEnvioWs->cd_uf        
											,$dadosEnvioWs->profissao    
											,'$dadosEnvioWs->placa'       
											,'$dadosEnvioWs->chassi'       
											,$dadosEnvioWs->veiculo_util  
											,$dadosEnvioWs->forma_pag    
								            ,NOW()
								            
								    ) RETURNING pspoid ";
			
			if(!$result = pg_query($this->connProposta, $sql)){
				throw new Exception('Falha ao inserir dados de envio para gerar proposta no Web Service.');
			}

			return pg_fetch_result($result, 0, "pspoid");

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
				throw new Exception('Os dados de retorno devem ser informados');
			}
	
			$sql = " UPDATE produto_seguro_proposta
						SET pspdt_cadastro  = NOW() ";

			if($dadosRetornoWs->cd_retorno != NULL || $dadosRetornoWs->cd_retorno === 0){
				$sql .=" ,pspretcodigo = ".(int)$dadosRetornoWs->cd_retorno." ";
			}

			if($dadosRetornoWs->nm_retorno != NULL){
                $dadosRetornoWs->nm_retorno = trim(str_replace("'", "", $dadosRetornoWs->nm_retorno));
				$sql .=" ,pspretdescricao = '$dadosRetornoWs->nm_retorno' ";
			}
			
			if($dadosRetornoWs->id_revenda != NULL){
				$sql .=" ,pspretrevenda = $dadosRetornoWs->id_revenda ";
			}
			
			if($dadosRetornoWs->nm_usuario != NULL){
				$sql .=" ,pspretusuario = '$dadosRetornoWs->nm_usuario' ";
			}
			
			if($dadosRetornoWs->id_proposta != NULL){
				$sql .=" ,pspretproposta = $dadosRetornoWs->id_proposta ";
			}
			
			if($dadosRetornoWs->id_endosso != NULL){
				$sql .=" ,pspretendosso = $dadosRetornoWs->id_endosso ";
			}
			
			if($dadosRetornoWs->xml_envio != NULL || $dadosRetornoWs->xml_envio != ""){
				$sql .=" ,pspxmlenvio = '$dadosRetornoWs->xml_envio' ";
			}
			
			if($dadosRetornoWs->xml_retorno != NULL || $dadosRetornoWs->v != ""){
				$sql .=" ,pspxmlretorno = '$dadosRetornoWs->xml_retorno' ";
			}
				
			$sql .=" WHERE pspoid = $id_envio_dados ";
            
            /*$rs = fopen('mantis_7693.txt', 'w+');
            fwrite($rs, $sql);
            fclose($rs);*/
			
			if(!pg_query($this->connProposta, $sql)){
				throw new Exception('Falha ao atualizar dados de retorno da proposta');
			}
	
			return true;
	
		}catch (Exception $e){
			echo $e->getMessage();
			exit();
		}
	}
	
	
	/**
	 * Recupera o código da UF informada no parâmetro
	 * 
	 * @param string $uf
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodigoUf($uf){

		try{

			if(empty($uf)){
				throw new Exception('A descrição da UF deve ser informada');
			}
				
			$sql = "SELECT psuufcodigo AS cod_uf
					  FROM produto_seguro_uf
					 WHERE psuufdescricao = '".trim($uf)."' ";

			if (!$result = pg_query($this->connProposta, $sql)) {
				throw new Exception("Falha ao pesquisar código da UF");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}
    }

    /**
     * Recupera o código da forma de pagamento informado no parâmetro
     * 
     * @param int $forma_pagamento
     * @throws Exception
     * @return object|boolean
     */
    public function getCodigoFormaPagamento($forma_pagamento){
    	
    	try{
    	
    		if(empty($forma_pagamento)){
    			throw new Exception('A forma de pagamento deve ser informada');
    		}
    	
    		$sql = " SELECT psfcseguradoracod AS cod_forma_seguradora
					   FROM produto_seguro_forma_cobranca
					  WHERE psfcsascarcod =  '".trim($forma_pagamento)."' ";
    	
    		if (!$result = pg_query($this->connProposta, $sql)) {
    			throw new Exception("Falha ao pesquisar código de pagamento");
    		}
    	
    		if (pg_num_rows($result) > 0) {
    			return pg_fetch_object($result);
    		}
    	
    		return false;
    	
    	} catch (Exception $e) {
    		echo $e->getMessage();
    		exit;
    	}
    }
    
    
    /**
     * inicia transação com o BD
     */
    public function begin()	{
    	$rs = pg_query($this->connProposta, "BEGIN;");
    }
    
    /**
     * confirma alterações no BD
     */
    public function commit(){
    	$rs = pg_query($this->connProposta, "COMMIT;");
    }
    
    /**
     * desfaz alterações no BD
     */
    public function rollback(){
    	$rs = pg_query($this->connProposta, "ROLLBACK;");
    }

}