<?php

/**
 * Classe CadCompatibilidadeAcessorioVeiculoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   MARCELLO BORRMANN <marcello.b.ext@sascar.com.br>
 *
 */
class CadCompatibilidadeAcessorioVeiculoDAO {

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
	 * Consulta as marcas de veículos.
	 * @param 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getMarcaList(){
		$retorno  =  array();
	
		$sql = "SELECT
					mcaoid,
					mcamarca
				FROM
					marca
				WHERE 
					mcadt_exclusao IS NULL
				ORDER BY
					mcamarca;";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar marcas de veículos.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['mcaoid']		= $row->mcaoid;
			$retorno[$i]['mcamarca']	= $row->mcamarca;
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta os modelos de acessórios.
	 * @param 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getModeloCBList(){
		$retorno  =  array();
	
		$sql = "SELECT
					cbmooid,
					cbmodescricao
				FROM
					computador_bordo_modelo
				WHERE
					cbmoexclusao IS NULL
					AND cbmodescricao ILIKE 'ISV%'
				ORDER BY
					cbmodescricao;";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar modelos de acessórios.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['cbmooid']			= $row->cbmooid;
			$retorno[$i]['cbmodescricao'] 	= $row->cbmodescricao;
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta modelos de veículos a partir de uma marca selecionada.
	 * @param Integer mcaoid_busca
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getModeloList($mcaoid = ''){
		$retorno  =  array();
		
		if (!empty($mcaoid)) {
			$filtro .= " AND mlomcaoid = ".trim($mcaoid)." ";  
		}
		
		$sql = "SELECT 
					mlooid,
					mlomodelo 
				FROM 
					modelo 
				WHERE 
					mlodt_exclusao IS NULL 
					$filtro 
				ORDER BY 
					mlomodelo;";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar modelos de veículos.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['mlooid'] = $row->mlooid; 
			$retorno[$i]['mlomodelo'] = utf8_encode($row->mlomodelo);
			$i++;
		}

		return $retorno;
		
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT 
					cavoid, 
					cavdt_cadastro, 
					mcamarca, 
					mlomodelo,  
					cavano, 
					cbmodescricao,
					CASE 
						WHEN cavstatus IS TRUE THEN 'apr_bom' 
						WHEN cavstatus IS FALSE THEN 'apr_ruim' 
						ELSE 'apr_neutro' 
					END AS cavstatus
				FROM 
					compatibilidade_acessorio_veiculo 
					INNER JOIN modelo ON mlooid = cavmlooid 
					INNER JOIN marca ON mcaoid = mlomcaoid 
					LEFT JOIN computador_bordo_modelo ON cbmooid = cavcbmooid 
				WHERE 
					cavdt_exclusao IS NULL "; 
	
		if (isset($parametros->cavmcaoid_busca) && !empty($parametros->cavmcaoid_busca)) {
			$sql .= "
					AND mlomcaoid = " . intval($parametros->cavmcaoid_busca) . " ";
		}
		
		if (isset($parametros->cavmlooid_busca) && trim($parametros->cavmlooid_busca) != '') {
		
			$sql .= "
					AND mlooid = " . intval($parametros->cavmlooid_busca) . "";
		}
		
		if (isset($parametros->cavano_busca) && trim($parametros->cavano_busca) != '') {
			$sql .= "
					AND cavano = " . intval($parametros->cavano_busca) . "";
		}
		
		if (isset($parametros->cavcbmooid_busca) && trim($parametros->cavcbmooid_busca) != '') {
			$sql .= "
					AND cavcbmooid = " . intval($parametros->cavcbmooid_busca) . "";
		}
		
		if (isset($parametros->cavstatus_busca) && trim($parametros->cavstatus_busca) != '') {
			$sql .= "
					AND cavstatus IS " . pg_escape_string($parametros->cavstatus_busca) . "";
		}
		$sql .= "
				ORDER BY 
					mcamarca, 
					mlomodelo,
					cavano ";
		
		//echo $sql."</br>";
		//exit;
		$rs = pg_query($this->conn,$sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){

		$retorno = new stdClass();

		$sql = "SELECT 
					cavoid, 
					CASE 
						WHEN cavstatus IS TRUE THEN 'TRUE' 
						WHEN cavstatus IS FALSE THEN 'FALSE' 
						ELSE 'NULL' 
					END AS cavstatus, 
					cavano, 
					cavmlooid, 
					(SELECT mlomcaoid FROM modelo WHERE mlooid = cavmlooid LIMIT 1) AS cavmcaoid,
					cavcbmooid
				FROM 
					compatibilidade_acessorio_veiculo
				WHERE 
					cavoid =" . intval( $id ) . "";
		
		//echo $sql."</br>";
		//exit;
		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	 * Método para verifcar se o registro está sendo inserido em duplicidade.
	 *
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function pesquisarDuplicidade(stdClass $dados){
		
		$sql = "SELECT 
					COUNT(cavoid) AS qtde
				FROM 
					compatibilidade_acessorio_veiculo
				WHERE 
					cavdt_exclusao IS NULL 
					AND cavano =" . intval( $dados->cavano ) . "
					AND cavmlooid =" . intval( $dados->cavmlooid ) . "
					AND cavcbmooid =" . intval( $dados->cavcbmooid ) . "";

		if ($dados->cavoid > 0) {
			$sql.= "
					AND cavoid <>" . intval( $dados->cavoid ) . "";
		}
		
		//echo $sql."</br>";
		//exit;
		$rs = pg_query($this->conn, $sql);

		if (pg_num_rows($rs) > 0 && pg_fetch_result($rs,0,0) > 0){
			throw new Exception("O registro já se encontra cadastrado.");
		}

		return true;		
		
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserir(stdClass $dados){
		
		$sql = "INSERT INTO
					compatibilidade_acessorio_veiculo
					(
					cavstatus,
					cavusuoid_cadastro,
					cavano,
					cavmlooid,
					cavcbmooid
					)
				VALUES
					(
					" . pg_escape_string( $dados->cavstatus ) . ",
					" .	intval( $dados->usuoid ) . ",
					" . intval( $dados->cavano ) . ",
					" . intval( $dados->cavmlooid ) . ",
					" . intval( $dados->cavcbmooid ) . "
				)";

		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao inserir registro.");
		}

		return true;
	}

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizar(stdClass $dados){

		// Verificando se o status foi alterado
		$dataAlteracaoStatus = ($dados->cavstatus != 'NULL' ? 'NOW()' : $dados->cavstatus);

		$sql = "UPDATE
					compatibilidade_acessorio_veiculo
				SET
					cavstatus = " . pg_escape_string( $dados->cavstatus ) . ",
					cavano = " . intval( $dados->cavano ) . ",
					cavmlooid = " . intval( $dados->cavmlooid ) . ",
					cavcbmooid = " . intval( $dados->cavcbmooid ) . ",
					cavdt_alteracao_status = " . $dataAlteracaoStatus . "
				WHERE 
					cavoid = " . $dados->cavoid . "";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao atualizar registro.");
		}
		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir(stdClass $dados){

		$sql = "UPDATE
					compatibilidade_acessorio_veiculo
				SET
					cavdt_exclusao = NOW(), 
					cavusuoid_exclusao = " . intval( $dados->usuoid ) . "
				WHERE
					cavoid = " . $dados->cavoid . "";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao excluir registro.");
		}
		return true;
	}


	/**
	* Verifica se há histórico de notificação de homologação para avisar o usuário/executivo
	**/
	public function verificaNotificacaoHomologacao($obj)
	{
		# buscando notificação
		$ssql = "SELECT cahnoid, cahndt_cadastro, cahnusuoid_cadastro, cahnexecutivo, cahncavoid, cahnmlooid, cahnano 
				FROM compatibilidade_acessorio_historico_notificacao 
				WHERE cahnmlooid = {$obj->cavmlooid} 
				AND cahnano = {$obj->cavano};";

		$rs = $this->executarQuery($ssql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_all($rs);
		}else{
			$retorno = false;
		}

		return $retorno;

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
