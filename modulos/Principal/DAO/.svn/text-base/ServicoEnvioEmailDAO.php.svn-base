<?php

/**
 * 
 * Classe padrão de envio de e-mail.
 * 
 * @author 	Leandro Alves Ivanaga
 * @email   leandroivanaga@brq.com
 * @version 11/06/2012
 * @since   11/06/2012
 */
 
class ServicoEnvioEmailDAO{

	private $conn;
	private $servidor;
	
	private $erro;
	private $msg;
	
	private $remetente;
	
	public function ServicoEnvioEmailDAO($conn){
		
		$this->conn = $conn;
	}
	
	/**
	 * Busca dados referente ao servidor
	 */
	public function getDadosServidor($servidor = null){
		try{
			
			$this->servidor = $servidor;
			$this->remetente = array();
			
			if (!empty($this->servidor)){
					
				// Busca dados de acordo com o servidor
				$this->getDadosRemetente();	
			}else{
				
				throw new Exception("Deve ser informado o servidor de envio.");
			}
			
			
			if (empty($this->remetente)){
			
				throw new Exception("Não foi encontrado os dados do servidor informado.");
			}
			
			// Verifica ocorrência de erro
			if ($this->erro == true){
				throw new Exception($this->msg);
			}
			
			// Monta array de retorno dos dados do remetente.
			$retorno = array();
			$retorno['erro'] = false;
			
			foreach ($this->remetente as $campo => $valor){
				
				$retorno[$campo] = $valor;
			}
						
			return $retorno;
			
		}catch (Exception $e){
			
			$retorno = array(
						"erro" 	=> true,
						"msg"	=> $e->getMessage()
					);
			
			return $retorno;
		}
	}
	
	/**
	 * Busca o codigo do titulo e da funcionalidade de acordo com o nome do titulo passado
	 */
	
	public function getTituloFuncionalidade($titulo){
		 
		$sql = "
		    	SELECT
		    		seetoid AS titulo_id, seetseefoid AS funcionalidade_id
		    	FROM
		    		servico_envio_email_titulo
		    	WHERE
		    		seetdescricao = '".$titulo."';
    			";
	
		
		$rs = pg_query($sql);
			
		return pg_fetch_object($rs);
	}
	
	public function getCodigoServidor($tipo) {
		$tipo = pg_escape_string($tipo);
		 
		$sql = "SELECT * FROM servico_envio_email WHERE seeoid = " . $tipo;
		 		
		$rs = pg_query($sql);
		 
		return pg_fetch_object($rs);
	}
	
	/**
	 * Busca o layout
	 */
	public function getLayoutEmail($condigoLayout) {
		$tipo = pg_escape_string($tipo);
		 
		$sql = "SELECT * FROM servico_envio_email WHERE seeoid = " . $condigoLayout;
		 
		$rs = pg_query($sql);
		 
		return pg_fetch_object($rs);
	}
	
	/**
	 * Busca dados referente ao servidor
	 */
	public function getDadosRemetente(){
		try{
			
			// Busca os dados do remetente de acordo com o servidor
			$sqlServidor = "
					SELECT
						*
					FROM
						servidor_email
					WHERE
						srvoid = $this->servidor;
					";
			
			if (!$resServidor = pg_query($this->conn, $sqlServidor)) {
				throw new Exception("Erro ao buscar dados do servidor.");
			}
				
			$numServidor = pg_num_rows($resServidor);
			
			if ($numServidor > 0){
				$this->remetente = pg_fetch_object($resServidor);
			}
			
		}catch (Exception $e){
			
			$this->erro = true;
			$this->msg = $e->getMessage();
		}
	}
	
	/**
	 * Busca dados referente ao servidor padrão
	 */
	public function getDadosRemetentePadrao(){
		try{
			
			$sqlServidor = "
				SELECT
					*
				FROM
					servidor_email
				WHERE
					srvpadrao = 'true';
					";
	
			if (!$resServidor = pg_query($this->conn, $sqlServidor)) {
				throw new Exception("Erro ao buscar dados do servidor padrão.");
			}
	
			$numServidor = pg_num_rows($resServidor);
				
			if ($numServidor > 0){
				$this->remetente = pg_fetch_object($resServidor);
			}
			
		}catch (Exception $e){
			
			$this->erro = true;
			$this->msg = $e->getMessage();
		}
	}	
}
?>