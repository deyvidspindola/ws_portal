<?php

/**
 * Classe CadGruposDeTestesDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   MARCELLO BORRMANN <marcello.b.ext@sascar.com.br>
 *
 */
class CadGruposDeTestesDAO {

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
	 * Consulta os projetos de equipamentos.
	 * @param 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getEquipamentoProjetoList(){
		$retorno  =  array();
	
		$sql = "SELECT
					eproid,
					eprnome
				FROM
					equipamento_projeto
				ORDER BY
					eprnome;";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar projetos de equipamento.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['eproid'] 	= $row->eproid;
			$retorno[$i]['eprnome'] = $row->eprnome;
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta os classes de equipamentos.
	 * @param 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getEquipamentoClasseList(){
		$retorno  =  array();
	
		$sql = "SELECT
					eqcoid,
					eqcdescricao
				FROM
					equipamento_classe
				WHERE
					eqcinativo IS NULL
				ORDER BY
					eqcdescricao;";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar classes de equipamento.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['eqcoid'] 			= $row->eqcoid;
			$retorno[$i]['eqcdescricao'] 	= $row->eqcdescricao;
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta versões de equipamentos a partir de um projeto selecionado.
	 * @param Integer eproid_busca
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getEquipamentoVersaoList($eproid_busca = ''){
		$retorno  =  array();
		
		if (!empty($eproid_busca)) {
			$filtro .= " AND eveprojeto = '".trim($eproid_busca)."' ";  
		}
		
		$sql = "SELECT 
					eveoid,
					eveversao 
				FROM 
					equipamento_versao 
				WHERE 
					evedt_exclusao IS NULL 
					$filtro 
				ORDER BY 
					eveversao;";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar versões de equipamento.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['eveoid'] = $row->eveoid; 
			$retorno[$i]['eveversao'] = utf8_encode($row->eveversao);
			$i++;
		}

		return $retorno;
		
	}
	
	/**
	 * Retorna as versoes de equipamento de acordo com o projeto.
	 */
	public function buscarInstaladores() {
		
		$repoid = $_POST['repoid_busca'];
		$instaladorList	= $this->dao->getInstaladorList($repoid);
	
		return $retorno;
	}
	

	/**
	 * Método para realizar a pesquisa de varios registros.
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();  

        if ( isset($parametros->eproid_busca) && trim($parametros->eproid_busca) != '' ) {

            $where .= "
            		AND eproid = " . intval( $parametros->eproid_busca ) . "";
            
        }

        if ( isset($parametros->eqcoid_busca) && !empty($parametros->eqcoid_busca) ) {
        
            $where .= "
            		AND eqcoid = '" . intval( $parametros->eqcoid_busca ) . "'";
                
        }

        if ( isset($parametros->eveoid_busca) && !empty($parametros->eveoid_busca) ) {
        
            $where .= "
            		AND eveoid = '" . intval( $parametros->eveoid_busca ) . "'";
                
        }

		$sql = "SELECT 
					epcvoid, 
					eprnome, 
					eqcdescricao, 
					eveversao 
				FROM 
					equipamento_projeto_classe_versao
					LEFT JOIN equipamento_projeto ON epcveproid = eproid  
					LEFT JOIN equipamento_classe ON epcveqcoid = eqcoid
					LEFT JOIN equipamento_versao ON epcveveoid = eveoid 
				WHERE 
					epcvdt_exclusao IS NULL
					$where 
				ORDER BY 
					eprnome,
					eqcdescricao,
					eveversao;";

		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa por Identificador.
	 * @param int $epcvoid 
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarPorID(stdClass $parametros){

		$retorno = array();
		
		// Verifica se a ação é editarGrupo e ajusta a pesquisa
		if ($parametros->acao == 'editarGrupo') {
			
			$selecaoEditarGrupo = " ,
					(
					SELECT 
						COUNT(eptgoid) 
					FROM 
						equipamento_projeto_classe_versao_teste_grupo
					WHERE 
						eptgepcvoid = epvc.epcvoid
						AND eptgeptpoid = eptp.eptpoid
						AND eptgegtoid = " . intval( $parametros->egtoid ) . ") AS checked, 
					(SELECT egtnome FROM equipamento_grupo_teste WHERE egtoid = " . intval( $parametros->egtoid ) . ") AS egtnome ";
			
			$condicaoEditarGrupo= "
					AND ( (
							SELECT 
								COUNT(eptgoid)
							FROM 
								equipamento_projeto_classe_versao_teste_grupo
							WHERE 
								eptgepcvoid = epvc.epcvoid
								AND eptgeptpoid = eptp.eptpoid) = 0
						OR (
						 	SELECT 
								COUNT(eptgoid)
							FROM 
								equipamento_projeto_classe_versao_teste_grupo
							WHERE 
								eptgepcvoid = epvc.epcvoid
								AND eptgeptpoid = eptp.eptpoid
								AND eptgegtoid = " . intval( $parametros->egtoid ) . ") > 0
						) ";
			
		} else {
			
			$selecaoEditarGrupo = "";
			
			$condicaoEditarGrupo= " 
					AND (
							SELECT 
								COUNT(eptgoid)
							FROM 
								equipamento_projeto_classe_versao_teste_grupo
							WHERE 
								eptgepcvoid = epvc.epcvoid
								AND eptgeptpoid = eptp.eptpoid) = 0 ";
			
		}
		
		$sql = "SELECT 
					epvc.epcveqcoid as eqcoid_busca,
					epvc.epcveproid as eproid_busca,
					epvc.epcveveoid as eveoid_busca,
					ecmt.ecmtoid,
					cmd.cmdoid,
					cmd.cmdcomando || '('||cmd.cmdmeio||') ' || cmd.cmddescricao  as comando,
					eptp.eptpoid,
					eptt.epttoid,
					eptt.epttdescricao || '('||eptp.eptpinstrucao||')' as instrucao,
					ecmt.ecmteptpoid_antecessor,
					eptt2.epttdescricao || '('||eptp2.eptpinstrucao||')' as depende
					" . $selecaoEditarGrupo . "
				FROM 
					equipamento_comandos_testes as ecmt
					JOIN equipamento_comandos as ecm ON ecmt.ecmtecmoid = ecm.ecmoid
					JOIN equipamento_projeto_teste_planejado as eptp ON ecmt.ecmteptpoid = eptp.eptpoid
					JOIN equipamento_projeto_classe_versao as epvc ON ecm.ecmepcvoid = epvc.epcvoid
					JOIN comandos as cmd ON ecm.ecmcmdoid = cmd.cmdoid
					JOIN equipamento_projeto_tipo_teste_planejado as eptt ON eptp.eptpepttoid = eptt.epttoid
					LEFT JOIN equipamento_projeto_teste_planejado as eptp2 ON eptp2.eptpoid = ecmt.ecmteptpoid_antecessor
					LEFT JOIN equipamento_projeto_classe_versao as epvc2 ON ecm.ecmepcvoid = epvc2.epcvoid
					LEFT JOIN comandos as cmd2 ON ecm.ecmcmdoid = cmd2.cmdoid
					LEFT JOIN equipamento_projeto_tipo_teste_planejado as eptt2 ON eptp2.eptpepttoid = eptt2.epttoid
				WHERE 
					epvc.epcvoid =" . intval( $parametros->epcvoid ) . "
					AND ecmt.ecmtdt_exclusao IS NULL
					AND ecm.ecmdt_exclusao IS NULL
					" . $condicaoEditarGrupo . "
					-- AND eptp.eptpepgtpoid NOT IN (2,5)
				ORDER BY
					comando, 
					instrucao, 
					depende;";		
	
		//echo $sql."<br>";
		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
		
	}

	/**
	 * Método para realizar a pesquisa de grupos.
	 * @param int $epcvoid 
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarGrupo($epcvoid){
	
		$retorno = array();
	
		$sql = "SELECT
					epvc.epcveqcoid as eqcoid_busca,
					epvc.epcveproid as eproid_busca,
					epvc.epcveveoid as eveoid_busca,
					ecmt.ecmtoid,
					ecmt.ecmteptpoid_antecessor,
					cmd.cmdoid,
					cmd.cmdcomando || '('||cmd.cmdmeio||') ' || cmd.cmddescricao  as comando,
					eptp.eptpoid,
					eptt.epttoid,
					eptt.epttdescricao || '('||eptp.eptpinstrucao||')' as instrucao,
					eptt2.epttdescricao || '('||eptp2.eptpinstrucao||')' as depende,
					egt.egtoid, 
					egt.egtnome as grupo
				FROM
					equipamento_grupo_teste as egt
					JOIN equipamento_projeto_classe_versao_teste_grupo as eptg ON eptg.eptgegtoid = egt.egtoid
					JOIN equipamento_projeto_teste_planejado as eptp ON eptp.eptpoid = eptg.eptgeptpoid
					JOIN equipamento_projeto_tipo_teste_planejado as eptt ON eptp.eptpepttoid = eptt.epttoid
					JOIN equipamento_comandos_testes as ecmt ON ecmt.ecmteptpoid = eptp.eptpoid		
					JOIN equipamento_comandos as ecm ON ecmt.ecmtecmoid = ecm.ecmoid
					JOIN equipamento_projeto_classe_versao as epvc ON ecm.ecmepcvoid = epvc.epcvoid AND eptg.eptgepcvoid = epvc.epcvoid
					JOIN comandos as cmd ON ecm.ecmcmdoid = cmd.cmdoid
					LEFT JOIN equipamento_projeto_teste_planejado as eptp2 ON eptp2.eptpoid = ecmt.ecmteptpoid_antecessor
					LEFT JOIN equipamento_projeto_classe_versao as epvc2 ON ecm.ecmepcvoid = epvc2.epcvoid
					LEFT JOIN comandos as cmd2 ON ecm.ecmcmdoid = cmd2.cmdoid
					LEFT JOIN equipamento_projeto_tipo_teste_planejado as eptt2 ON eptp2.eptpepttoid = eptt2.epttoid
				WHERE
					epvc.epcvoid =" . intval( $epcvoid ) . "
					AND ecmt.ecmtdt_exclusao IS NULL
					AND ecm.ecmdt_exclusao IS NULL
					AND egt.egtdt_exclusao IS NULL
					--AND eptp.eptpepgtpoid NOT IN (2,5)
				ORDER BY
					grupo,
					egt.egtoid,
					comando,
					instrucao,
					depende;";		
	
		//echo $sql."<br>";
		$rs = pg_query($this->conn,$sql);
	
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
	
		return $retorno;
	
	}

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return integer
	 * @throws ErrorException
	 */
	public function inserirGrupo(stdClass $dados){

		$sql = "INSERT INTO
					equipamento_grupo_teste(
						egtnome,
						egtusuoid_cadastro
					)
				VALUES(
					'" . pg_escape_string(trim( $dados->egtnome )) . "',
					" . intval( $dados->egtusuoid_cadastro ) . "
				)
				RETURNING egtoid;";
		
		//echo $sql."<br>";
		if($rs = pg_query($this->conn,$sql)){
			$retorno = pg_fetch_result($rs , 0, 'egtoid');
		}
		else{
			$retorno = 0;
		}
		
		return $retorno;
	}

	/**
	 * Responsável por atualizar os registros.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizarGrupo(stdClass $dados){

		$sql = "UPDATE
					equipamento_grupo_teste
				SET
					egtnome = '" . pg_escape_string(trim( $dados->egtnome )) . "'
				WHERE 
					egtoid = " . $dados->egtoid . ";";
		
		//echo $sql."<br>";
		$rs = pg_query($this->conn,$sql);

		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluirGrupo(stdClass $dados){

		$sql = "UPDATE
					equipamento_grupo_teste
				SET
					egtdt_exclusao = NOW(),
					egtusuoid_exclusao = " . intval( $dados->egtusuoid_exclusao ) . "
				WHERE
					egtoid = " . $dados->egtoid . ";";
		
		//echo $sql."<br>";
		$rs = pg_query($this->conn,$sql);

		return true;
	}

	/**
	 * Responsável por inserir um registro no banco de dados 
	 * relacionando projeto X classe X versao, teste planejado 
	 * e grupo. 
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirProjetoClasseVersaoTesteGrupo(stdClass $dados){

		$sql = "INSERT INTO
					equipamento_projeto_classe_versao_teste_grupo(
						eptgepcvoid,
						eptgeptpoid,
						eptgegtoid
					)
				VALUES(
					" . intval( $dados->epcvoid ) . ",
					" . intval( $dados->eptpoid ) . ",
					" . intval( $dados->egtoid ) . "
				);";
		
		//echo $sql."<br>";
		if(! $rs = pg_query($this->conn,$sql)){
			$retorno = false;
		}
		else{
			$retorno = true;
		}
		
		return $retorno;
	}

	/**
	 * Responsável por excluir um registro no banco de dados 
	 * desvinculando projeto X classe X versao, teste planejado 
	 * e grupo.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluirProjetoClasseVersaoTesteGrupo(stdClass $dados){
		
		$sql = "DELETE 
					
				FROM 
					equipamento_projeto_classe_versao_teste_grupo
				WHERE
					eptgepcvoid = " . intval( $dados->epcvoid ) . "
					AND eptgegtoid = " . intval( $dados->egtoid ) . ";";
		
		//echo $sql."<br>";
		//exit;
		if(! $rs = pg_query($this->conn,$sql)){
			$retorno = false;
		}
		else{
			$retorno = true;
		}
		
		return $retorno;
	}

	/**
	 * Responsável por vincular o grupo aos testes planejados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 *//* 
	public function atualizarTestePlanejado(stdClass $dados){
		
		$sql = "UPDATE 
					equipamento_projeto_teste_planejado
				SET 
					eptpegtoid = " . intval( $dados->egtoid ) . "
				WHERE
					eptpoid IN (" . pg_escape_string( $dados->arrayEptpoid ) . ");";
		
		//echo $sql."<br>";
		if(! $rs = pg_query($this->conn,$sql)){
			$retorno = false;
		}
		else{
			$retorno = true;
		}
		
		return $retorno;
	} */

	/**
	 * Responsável por desvincular os testes planejados do grupo.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 *//* 
	public function excluirTestePlanejado(stdClass $dados){
		
		$sql = "UPDATE 
					equipamento_projeto_teste_planejado
				SET 
					eptpegtoid = null
				WHERE
					eptpegtoid = " . intval( $dados->egtoid ) . ";";
		
		//echo $sql."<br>";
		if(! $rs = pg_query($this->conn,$sql)){
			$retorno = false;
		}
		else{
			$retorno = true;
		}
		
		return $retorno;
	} */
	
	/**
	 * Método para validar se em um mesmo grupo 
	 * existem testes dependentes entre si.
	 * @param stdClass $dados Dados a serem gravados 
	 * @return array
	 * @throws ErrorException
	 */
	public function validarTestesDependentes(stdClass $dados){
		
		$sql = "SELECT DISTINCT
					ecmteptpoid AS id_teste,
					(SELECT
						epttdescricao
					FROM 
						equipamento_projeto_tipo_teste_planejado
						INNER JOIN equipamento_projeto_teste_planejado ON eptpepttoid = epttoid
					WHERE 
						eptpoid = ecmteptpoid) AS descricao_teste,
					ecmteptpoid_antecessor AS id_dependente,
					(SELECT
						epttdescricao
					FROM 
						equipamento_projeto_tipo_teste_planejado
						INNER JOIN equipamento_projeto_teste_planejado ON eptpepttoid = epttoid
					WHERE 
						eptpoid = ecmteptpoid_antecessor) AS descricao_dependente
				 FROM 
					equipamento_comandos_testes
					INNER JOIN equipamento_comandos ON ecmtecmoid = ecmoid
					INNER JOIN equipamento_projeto_classe_versao ON ecmepcvoid = epcvoid
				 WHERE
					ecmteptpoid_antecessor IS NOT NULL
					AND epcvoid =" . intval( $dados->epcvoid ) . "
					AND ecmteptpoid IN
						(SELECT 
							ecmteptpoid AS eptpoid
						FROM 
							equipamento_comandos_testes
						WHERE 
							ecmteptpoid_antecessor IN (" . pg_escape_string( $dados->arrayEptpoid ) . ")
							
						INTERSECT
							
						SELECT 
							eptpoid
						FROM 
							equipamento_projeto_teste_planejado
						WHERE 
							eptpoid IN (" . pg_escape_string( $dados->arrayEptpoid ) . ")
						);";
		
		//echo $sql."<br>";	
		$rs = pg_query($this->conn,$sql);
	
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
	
		return $retorno; 
	}
	
	/**
	 * Método para validar se o nome do grupo 
	 * já é utilizado para o Projeto X Classe 
	 * X Versão.
	 * @param stdClass $dados Dados a serem gravados 
	 * @return integer
	 * @throws ErrorException
	 */
	public function validarNomeDuplicado(stdClass $dados){
		
		$condicao = "";
		if ($dados->egtoid != '') {		
			$condicao = "
					AND egtoid <> ". intval( $dados->egtoid );
		}
		
		$sql = "SELECT 
					COUNT(egtoid) AS qtde
				FROM 
					equipamento_grupo_teste
					INNER JOIN equipamento_projeto_classe_versao_teste_grupo ON eptgegtoid = egtoid
				WHERE 
					eptgepcvoid = ". intval( $dados->epcvoid ) ."
					AND egtnome ILIKE '" . pg_escape_string(trim( $dados->egtnome )) . "'
					$condicao
					AND egtdt_exclusao IS NULL;";
		
		//echo $sql."<br>";
		if($rs = pg_query($this->conn,$sql)){
			$retorno = pg_fetch_result($rs , 0, 'qtde');
		}
		else{
			$retorno = 0;
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
