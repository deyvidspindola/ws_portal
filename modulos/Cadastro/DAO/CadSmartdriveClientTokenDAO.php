<?php

/**
 * Classe CadSmartdriveClientTokenDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 *
 */
class CadSmartdriveClientTokenDAO extends DAO {

	public function pesquisarCliente($clioid){

		$retorno = new stdClass();

		$sql = "SELECT 
					clioid,	
					clitipo,	
					clinome,	
					clino_cpf,	
					clino_cgc
				FROM 
					clientes
				WHERE 
					clioid =" . intval( $clioid ) . "";

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	public function begin(){
        pg_query($this->conn, 'BEGIN');
    }

    public function commit(){
        pg_query($this->conn, 'COMMIT');
    }

    public function rollback(){
        pg_query($this->conn, 'ROLLBACK');
    }

    public function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
            }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }

}
?>
