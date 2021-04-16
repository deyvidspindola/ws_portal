<?php

/**
 * Classe CadAcaoOSDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   FABIO ANDREI LORENTZ <fabio.lorentz@sascar.com.br>
 *
 */
class CadAcaoOSDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros = null){

		$retorno = array();

		$sql = "SELECT 
					mhcoid,
					mhcdescricao
				FROM 
					motivo_hist_corretora
				WHERE 
					mhcexclusao IS NULL
				ORDER BY mhcdescricao";

		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarDepartamentos(stdClass $parametros = null){

		$retorno = array();

		$sql = "SELECT 
					depoid,
					depdescricao
				FROM 
					departamento
				WHERE 
					depexclusao IS NULL
				ORDER BY depdescricao";

		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param int $depoid ID do Departamento
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarVinculos($depoid){

		$retorno = array();

		$retorno['acoes_nao_vinc'] = array();
		$retorno['acoes_vinc'] = array();

		// Ações não vinculadas
		$sql = "SELECT 
					c.mhcoid,
					c.mhcdescricao
				FROM 
					motivo_hist_corretora c
				LEFT JOIN
					motivo_hist_corretora_departamento cd
				ON
					c.mhcoid = cd.mhcdmhcoid
				AND
					cd.mhcddepoid = $depoid
				WHERE 
					c.mhcexclusao IS NULL
				AND
					cd.mhcdmhcoid IS NULL					
				ORDER BY c.mhcdescricao";

		$rs = pg_query($this->conn,$sql);

		$i = 0;
		while($registro = pg_fetch_object($rs)){
			$retorno['acoes_nao_vinc'][$i]['mhcoid'] = $registro->mhcoid;
			$retorno['acoes_nao_vinc'][$i]['mhcdescricao'] = utf8_encode($registro->mhcdescricao);
			$i++;
		}

		// Ações vinculadas
		$sql = "SELECT 
					c.mhcoid,
					c.mhcdescricao
				FROM 
					motivo_hist_corretora c
				INNER JOIN
					motivo_hist_corretora_departamento cd
				ON
					c.mhcoid = cd.mhcdmhcoid
				AND
					cd.mhcddepoid = $depoid
				WHERE 
					c.mhcexclusao IS NULL
				ORDER BY c.mhcdescricao";

		$rs = pg_query($this->conn,$sql);

		$i = 0;
		while($registro = pg_fetch_object($rs)){
			$retorno['acoes_vinc'][$i]['mhcoid'] = $registro->mhcoid;
			$retorno['acoes_vinc'][$i]['mhcdescricao'] = utf8_encode($registro->mhcdescricao);
			$i++;
		}

		return $retorno;
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){

		$sql = "INSERT INTO
					motivo_hist_corretora
					(
					mhcdescricao
					)
				VALUES
					(
					'" . pg_escape_string( $dados->mhcdescricao ) . "'
				)";

		$rs = pg_query($this->conn,$sql);

		return true;
	}

	/**
	 * Responsável para inserir um registro no banco de dados na tabela motivo_hist_corretora_departamento.
	 * @param integer $mhcdmhcoid Id do motivo histórico corretora
	 * @param integer $mhcddepoid Id do departamento
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirVinculo($mhcdmhcoid, $mhcddepoid){

		$sql = "INSERT INTO
					motivo_hist_corretora_departamento
					(
						mhcdmhcoid, 
						mhcddepoid
					)
				VALUES
					(
					" . intval( $mhcdmhcoid ) . ",
					" . intval( $mhcddepoid ) . "
				)";

		return $this->executarQuery($sql);
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir($id){

		$sql = "UPDATE
					motivo_hist_corretora
				SET
					mhcexclusao = NOW() 
				WHERE
					mhcoid = " . intval( $id ) . "";

		$rs = pg_query($this->conn,$sql);

		return true;
	}

	/**
	 * Exclui um ou mais registros da base de dados na tabela motivo_hist_corretora_departamento.
	 * @param integer $mhcddepoid Id do departamento
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluirVinculos($mhcddepoid){

		$sql = "DELETE FROM
					motivo_hist_corretora_departamento
				WHERE
					mhcddepoid = " . intval( $mhcddepoid ) . "";

		return $this->executarQuery($sql);
	}

	/**
     * Método para realizar a pesquisa de duplicidade antes de cadastrar/editar
     * @param stdClass $parametros
     * @return boolean
     * @throws ErrorException
     */
    public function verificaDuplicidade(stdClass $parametros){

        $sql = "SELECT 
                    1
                FROM 
                    motivo_hist_corretora
                WHERE 
                    mhcexclusao IS NULL
                AND
                    TRANSLATE(mhcdescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC') ILIKE '" . $parametros->mhcdescricaoConsulta . "' 
                LIMIT 1";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }
    }

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

	/** Submete uma query a execucao do SGBD */
	private function executarQuery($query) {

        if(!$rs = pg_query($query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }

    /**
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

     /**
     * Aborta ações dentro de um bloco de ponto de salvamento
     * @param  $nome [alias para do savepoint]
     */
    public function rollbackPoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }
}
?>
