<?php
class CadImportacaoCorreiosDAO {
	
	private $conn;
	
	/**
	 * Construtor 
	 * @param object $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}
	
	/**
	 * Pesquisa de bairro por cbaoid
	 * @param unknown_type $chave
	 * @return boolean|multitype:
	 */
	public function getBairro($chave) {
		if(empty($chave)) {
			return false;
		}
		
		$resultado = pg_query($this->conn, "
		SELECT
			correios_bairros.cbaoid AS chave,
			correios_bairros.cbanome AS nome
		FROM correios_bairros
		WHERE correios_bairros.cbaoid = $chave
		");
		
		if(!$resultado) {
			return false;
		} elseif(!pg_num_rows($resultado)) {
			return false;
		}
		
		return pg_fetch_array($resultado, 0, PGSQL_ASSOC);
	}
	
	/**
	 * Pesquisa Localidade clcoid
	 * @param unknown_type $chave
	 * @return boolean|multitype:
	 */
	public function getLocalidade($chave) {
		if(empty($chave)) {
			return false;
		}
		
		$resultado = pg_query($this->conn, "
		SELECT
			correios_localidades.clcoid AS chave,
			correios_localidades.clcnome AS nome,
			correios_localidades.clctipo AS tipo
		FROM correios_localidades
		WHERE correios_localidades.clcoid = $chave
		");
		
		if(!$resultado) {
			return false;
		} elseif(!pg_num_rows($resultado)) {
			return false;
		}
		
		return pg_fetch_array($resultado, 0, PGSQL_ASSOC);
	}
	
	/**
	 * Insere registro de histórioc de atualizacao.
	 * @param integer $id_usuario - informar id do usuario logado
	 * @param char(1) $status
	 * @param string $msg
	 * @throws Exception
	 * @return boolean
	 */
	public function gravarHistorico($id_usuario, $status, $msg = ""){
		$sql="insert into historico_atualizacao_correios(hacdt_atualizacao, hacusuoid, hacstatus,hacobservacao) 
		                                         values (now(), $id_usuario,'$status','$msg')";
		$rs = pg_query($this->conn, $sql);
		if (!is_resource($rs)) {
			throw new Exception('Houve um erro ao gravar o hist&oacute;rico de atualiza&ccedil;&atilde;o.');
		}
				
		return true;
	}
	
	/**
	 * Consulta o histórico de atualizações.
	 * 
	 * @param integer $limit - limita a consulta nos primeiros $limit registros
	 * @throws Exception
	 * @return boolean|multitype:
	 */
	public function getHistorico($limit=null){		
		$sql="select 
				TO_CHAR(hacdt_atualizacao,'DD/MM/YYYY') AS hacdt_atualizacao,
				hacusuoid,
				usuarios.nm_usuario,
				hacstatus,
				hacobservacao
			from historico_atualizacao_correios 
				join usuarios on cd_usuario = hacusuoid
			order by historico_atualizacao_correios.hacdt_atualizacao desc ".( $limit!=null ? "limit ".intval($limit) : "" );
		$rs = pg_query($this->conn, $sql);
		if (!is_resource($rs))
			throw new Exception('Houve um erro ao gravar o hist&oacute;rico de atualiza&ccedil;&atilde;o.');
		
		if(pg_num_rows($rs)==0) return false;
		
		return pg_fetch_all($rs);
	}	
	
	
	
	/**
	 * STI 85377
	 * 
	 * @param int $valoid - campo que contém o valor a ser recuperado
	 * @return boolean|multitype:
	 */
	public function getParametrosDownload($valoid){
		
		$resultado = pg_query($this->conn, " 
				
				  SELECT v.valvalor AS valor
					FROM dominio d 
			  INNER JOIN registro r ON r.regdomoid = d.domoid
			  INNER JOIN valor v ON  v.valregoid = r.regoid
				   WHERE d.domoid = 19 --PARAMETROS DOWNLOAD ARQUIVOS CORREIOS
					 AND v.valoid = $valoid
					 
				");
		
		if(!$resultado) {
			return false;
		} elseif(!pg_num_rows($resultado)) {
			return false;
		}
		
		return pg_fetch_array($resultado, 0, PGSQL_ASSOC);
		
	}
	
	
}