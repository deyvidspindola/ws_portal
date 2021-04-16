<?php
/**
 * description: Persistir altera??o de senha e buscar o usu?rio.
 * @author denilson.sousa
 *
 */

class MANInsereTextoContratoDAO {
	/** @var string conx?o com o banco  */
	private $conn;
	
	/**
	 * Injeta a string de conex?o da intranet
	 * do config.php
	 */
	public function __construct() {
		global $conn;
		global $cd_usuario;
		$this->conn = $conn;
		$this->cd_usuario_intra = $cd_usuario;
	}
	
	/**
	 * 
	 * @param string $connumero - numero do contrato 
	 * @throws Exception
	 * @return resource
	 */
	
	public function pesquisarContratos($contratos = '') {
		$contratos = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", trim($contratos)); // retira linhas em branco e espaÃ§s
		$ult_caracter = substr($contratos,-1);
		if ($ult_caracter == ",") {
		   $contratos = substr($contratos,0,-1); // gera uma nova string sem o ultimo caracter (",")
		}
		$arr_contratos = explode(',',$contratos);
		$qtde_contratos = count($arr_contratos);
	
		try {
			$contratos_nao_encontrados = "";
			$sql = "SELECT count(*) as totcontratos from contrato 
			        where connumero in ($contratos)
			";
			
			$result = pg_query($this->conn, $sql);
			
			if (! $result) {
				throw new Exception ( 'ERRO: <b>Falha ao consultar contratos.</b>' );
			} else {
				$qtde_contratos_sql = pg_fetch_array($result);
				
				if ($qtde_contratos <> $qtde_contratos_sql['totcontratos']) {
				   foreach($arr_contratos as $key => $connumero) {
					$sql = "SELECT count(*) as totcontratos from contrato 
					        where connumero = $connumero
					";
					
					$result = pg_query($this->conn, $sql);
         				$qtde_contratos_erro = pg_fetch_array($result);
					
					if  (empty($qtde_contratos_erro['totcontratos']) ||  $qtde_contratos_erro['totcontratos'] == 0 )   {
					    $contratos_nao_encontrados .= $connumero . " ";
					}
				   } // foreach
				} // if ($qtde_contratos <> $qtde_contratos_sql {
			} // else --> if (! $result) {
			if (!empty($contratos_nao_encontrados)) {
			    return "Contratos não cadastrados: " . $contratos_nao_encontrados . "</b>";
			} else {
		    		return $result;
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		} // catch
	}// pesquisarContratos

	public function inserirHistorico($contratos = '',$texto_historico = '') {
		$erro = "N";
		$texto_historico = str_replace("'","",$texto_historico); // retira aspas simples, do contrario ocorrera erro no update

		$result = pg_query($this->conn, "BEGIN");
		
		try {
			$arr_contratos = explode(',',$contratos);

			foreach($arr_contratos as $key => $connumero) {
				$sql = "SELECT historico_termo_i ($connumero,$this->cd_usuario_intra,'$texto_historico'); ";
					
				$result = pg_query($this->conn, $sql);

	    			if (!$result) {
		    		    $erro = "S";
		    		    break;
	    			}
				
			} // foreach

			if ($erro == "S") {
			    $result = pg_query($this->conn, "ROLLBACK");
			    throw new Exception ( 'ERRO: <b>Falha ao gravar os históricos.</b>' );
			}
			else {
			    $result = pg_query($this->conn, "COMMIT");
			    return "ok";
			}
		} // try
		catch ( Exception $e ) {
			return $e->getMessage ();
		} // catch

	
	}// inserirHistorico
	
}
