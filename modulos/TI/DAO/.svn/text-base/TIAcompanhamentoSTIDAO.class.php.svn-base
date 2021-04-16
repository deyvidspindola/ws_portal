<?php
/**
 * SASCAR (http://www.sascar.com.br/)
 *
 * Módulo para Acompanhamento de STI - DAO (Data Access Object)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Módulo para Acompanhamento de STI - DAO
 * @version 10/10/2012 [0.0.1]
 * @package SASCAR Intranet
*/

class TIAcompanhamentoSTIDAO {
	private $conn;
	/**
	  * __construct()
	  *
	  * @param none
	  * @return none
	  * @description	Método construtor da classe
	*/
	public function __construct(){
		global $conn;
		$this->conn = $conn;
	}

	/**
	 * getPesquisaResult()
	 *
	 * @param none
	 * @return array $resultado
	 */
	public function getPesquisaResult() {
		
		$vResultado = array();
		$vResultado['action_msg'] = '';
		$vFormPesquisaData = array();
		$rs = null; 
		$i= 0;
		$totalResgistros = 0;
		
		$sqlQuery = '';
		// Dados do FILTRO para montar a query
		// Data (período)
		$vFormPesquisaData['pesq_periodo_inicial'] = (isset($_POST['pesq_periodo_inicial'])) ? $_POST['pesq_periodo_inicial'] : '';
		$vFormPesquisaData['pesq_periodo_final'] = (isset($_POST['pesq_periodo_final'])) ? $_POST['pesq_periodo_final'] : '';
		// Num solicitação
		$vFormPesquisaData['reqioid'] = (isset($_POST['reqioid'])) ? (int) $_POST['reqioid'] : '';
		// Num externo
		$vFormPesquisaData['reqioid_externo'] = (isset($_POST['reqioid_externo'])) ? (int) $_POST['reqioid_externo'] : '';
		// Tipo de Solicitação
		$vFormPesquisaData['sti_tipo'] = (isset($_POST['sti_tipo'])) ? (int) $_POST['sti_tipo'] : '';
		// Subtipo de Solicitação
		$vFormPesquisaData['sti_subtipo'] = (isset($_POST['sti_subtipo'])) ? (int) $_POST['sti_subtipo'] : '';
		// Solicitante 
		$vFormPesquisaData['sti_solicitante'] = (isset($_POST['sti_solicitante'])) ? (int) $_POST['sti_solicitante'] : '';
		$vFormPesquisaData['cd_usuario_solicitante'] = (isset($_POST['cd_usuario_solicitante'])) ? (int) $_POST['cd_usuario_solicitante'] : '';
		// Função
		$vFormPesquisaData['sti_funcao'] = (isset($_POST['sti_funcao'])) ? (int) $_POST['sti_funcao'] : '';
		// Usuário
		$vFormPesquisaData['sti_usuario'] = (isset($_POST['sti_usuario'])) ? (int) $_POST['sti_usuario'] : '';
		// Natureza
		$vFormPesquisaData['sti_natureza'] = (isset($_POST['sti_natureza'])) ? (int) $_POST['sti_natureza'] : '';
		// Fluxo
		$vFormPesquisaData['sti_fluxo'] = (isset($_POST['sti_fluxo'])) ? (int) $_POST['sti_fluxo'] : '';
		// Projeto
		$vFormPesquisaData['sti_projeto'] = (isset($_POST['sti_projeto'])) ? (int) $_POST['sti_projeto'] : '';
		// Assunto
		$vFormPesquisaData['sti_assunto'] = (isset($_POST['sti_assunto'])) ? $_POST['sti_assunto'] : '';
		// Demanda própria
		$vFormPesquisaData['sti_demanda_propria'] = (isset($_POST['sti_demanda_propria'])) ? (int) $_POST['sti_demanda_propria'] : '';
		// Demanda atraso
		$vFormPesquisaData['sti_demanda_atraso'] = (isset($_POST['sti_demanda_atraso'])) ? (int) $_POST['sti_demanda_atraso'] : '';
		
		$sqlQuery = "SELECT 
           to_char(reqicadastro,'dd/mm/yyyy hh24:mi') as data,
           reqioid as sti,
           (select nm_usuario from usuarios where reqisolicitante=cd_usuario) as solicitante,
           (select rprnome from req_projeto where reqirproid=rproid) as projeto,
           (select nm_usuario from usuarios,req_informatica_fluxo where reqifoid=reqireqifoid and reqifusuoid_responsavel=cd_usuario) as responsavel,
           (select reqifdescricao from req_informatica_fluxo where reqifoid=reqireqifoid) as fluxo,
           (select reqifsdescricao from req_informatica_fase, req_informatica_execucao, req_informatica_execucao_recurso where reqiexreqifsoid = reqifsoid AND reqiexreqioid=reqioid and reqierdt_previsao_inicio<=now()::date and reqierdt_previsao_fim >=now()::date order by reqiexlancamento,reqiexordem limit 1 ) as fase_atual,          
           to_char(reqiprevisao,'dd/mm/yyyy') as previsao_entrega,
           (SELECT reqindescricao from req_informatica_natureza WHERE reqireqinoid=reqinoid) as natureza 
		FROM 
		     req_informatica,
		     req_informatica_tipo
		WHERE
		     reqireqtoid=reqtoid
		     AND reqtcontrole_sti is true
		     AND reqiexclusao is null ";
		
		// Caso tenha informado N° STI ou N° Externo -> desconsidere demais campos
		if(($vFormPesquisaData['reqioid'] != '') || ($vFormPesquisaData['reqioid_externo'] != '')){
			if($vFormPesquisaData['reqioid'] != ''){// Filtra Número da Solicitação
				$sqlQuery .= " AND reqioid  = " . $vFormPesquisaData['reqioid'];
			}
			if($vFormPesquisaData['reqioid_externo'] != ''){// Número Solicitação externo
				$sqlQuery .= " AND reqioid_externo  = " . $vFormPesquisaData['reqioid_externo'];
			}
		}else{
			// filtra datas
			$sqlQuery .= " AND reqicadastro BETWEEN ";
			$sqlQuery .= " '" . $vFormPesquisaData['pesq_periodo_inicial'] . " 00:00:00'";
			$sqlQuery .= " AND '" . $vFormPesquisaData['pesq_periodo_final'] . " 23:59:59'";
			// Tipo de solicitação
			if($vFormPesquisaData['sti_tipo'] != ''){
				$sqlQuery .= " AND reqireqtoid  = " . $vFormPesquisaData['sti_tipo'];
			}
			// Subtipo de solicitação
			if($vFormPesquisaData['sti_subtipo'] != ''){
				$sqlQuery .= " AND reqirqstoid = " . $vFormPesquisaData['sti_subtipo'];
			}
			// Solicitante
			if($vFormPesquisaData['cd_usuario_solicitante'] != ''){
				$sqlQuery .= " AND reqisolicitante  = " . $vFormPesquisaData['cd_usuario_solicitante'];
			}
			// Função
			if($vFormPesquisaData['sti_funcao'] != ''){
				$sqlQuery .= " AND reqioid IN (SELECT reqiexreqioid FROM 
						req_informatica_execucao, 
						req_informatica_execucao_recurso, 
						req_informatica_funcao_usuario
					WHERE 
						reqiexreqioid=reqioid 
						AND reqierreqiexoid=reqiexoid
						AND reqierusuoid_executor= reqifuusuoid 
						AND reqierdt_exclusao is null 
						AND reqifudt_exclusao is null
						AND reqifureqifcoid = " . $vFormPesquisaData['sti_funcao'] . ")
				";
			}
			// Usuário
			if($vFormPesquisaData['sti_usuario'] != ''){
				$sqlQuery .= " AND reqioid IN (
					SELECT reqiexreqioid FROM 
						req_informatica_execucao,
						req_informatica_execucao_recurso,
						req_informatica_funcao_usuario
					WHERE 
						reqiexreqioid=reqioid 
						AND reqierreqiexoid = reqiexoid
						AND reqierusuoid_executor=reqifuusuoid 
						AND reqierdt_exclusao is null 
						AND reqifudt_exclusao is null
						AND reqifuusuoid = " . $vFormPesquisaData['sti_usuario'] . ")
				";
			}
			// Natureza
			if($vFormPesquisaData['sti_natureza'] != ''){
				$sqlQuery .= " AND reqireqinoid  = " . $vFormPesquisaData['sti_natureza'];
			}
			// Fluxo
			if($vFormPesquisaData['sti_fluxo'] != ''){
				$sqlQuery .= " AND reqireqifoid  = " . $vFormPesquisaData['sti_fluxo'];
			}
			// Projeto
			if($vFormPesquisaData['sti_projeto'] != ''){
				$sqlQuery .= " AND reqirproid  = " . $vFormPesquisaData['sti_projeto'];
			}
			// Assunto
			if($vFormPesquisaData['sti_assunto'] != ''){
				$sqlQuery .= " AND reqiassunto  ilike '%" . $vFormPesquisaData['sti_assunto'] . "%' ";
			}
			// Demanda própria
			if($vFormPesquisaData['sti_demanda_propria'] == 1){
				
				$cd_usuario = (int) $_SESSION['usuario']['oid'];
				
				$sqlQuery .= " AND ((reqipara = " . $cd_usuario . ") OR (reqioid IN ( 
				SELECT reqiexreqioid FROM 
					req_informatica_execucao, 
					req_informatica_execucao_recurso, 
					req_informatica_funcao_usuario
				WHERE 
				    reqiexreqioid = reqioid 
				    AND reqierreqiexoid = reqiexoid 
				    AND reqierusuoid_executor = reqifuusuoid 
				    AND reqifudt_exclusao is null 
				    AND reqierdt_exclusao is null 
				    AND reqifuusuoid = " . $cd_usuario . ")))";
			}
			// Demanda em atraso
			if($vFormPesquisaData['sti_demanda_atraso'] == 1){
				$sqlQuery .= " AND reqiconclusao is null and reqiprevisao < now()::date";
			}
		}
		$sqlQuery .= " ORDER BY reqioid LIMIT 1000 "; 
		
		$rs = pg_query($this->conn, $sqlQuery);
		$totalResgistros = pg_num_rows($rs);
		if($totalResgistros > 0){
			for($i = 0; $i < $totalResgistros; $i++){
				$vResultado['lista'][$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
			$vResultado['action_msg'] = '<br> Sua pesquisa encontrou ' . $totalResgistros . ' registro(s). <br><br>';
		}else{
			$vResultado['action_msg'] = '<br> Sua pesquisa não encontrou registros. <br><br>';
		}
		return $vResultado;
	}
	
	/**
	 * getComboBoxListSubtipo()
	 *
	 * @param $reqioid (ID do tipo de solicitação)
	 * @return $retorno (array com dados da consulta para montar select)
	 */
	public function getDetalheSTIRelacao($reqioid=0) {
		$retorno = array();
		
        $sql = "SELECT  
                    reqifsdescricao AS fase,
                    to_char(reqierdt_previsao_inicio,'dd/mm/yyyy') AS inicio_previsto,
                    to_char(reqierdt_inicio,'dd/mm/yyyy') AS inicio_realizado,
                    to_char(reqierdt_previsao_fim,'dd/mm/yyyy') AS temino_previsto,                    
                    to_char(reqierdt_conclusao,'dd/mm/yyyy') AS conclusao,
                    reqierhoras_estimadas AS horas_estimadas,
                    CASE
                        WHEN (SELECT TRUE FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL LIMIT 1) IS TRUE THEN
                            TO_CHAR((SELECT SUM(ahrqtde_hora) FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL ), 'HH24:MI')
                        ELSE 
                            TO_CHAR(CAST(reqierhoras_realizado || ' hours' AS INTERVAL), 'HH24:MI')
                    END AS horas_utilizadas,
                    reqierprogresso AS progresso,
                    (SELECT nm_usuario FROM usuarios WHERE reqierusuoid_executor = cd_usuario) AS recurso,
                    (SELECT reqiedescricao FROM req_informatica_empresa, req_informatica_funcao_usuario WHERE reqierusuoid_executor = reqifuusuoid AND reqifureqieoid = reqieoid AND reqifudt_exclusao IS NULL LIMIT 1) AS empresa,
                    (SELECT COUNT(*) FROM req_informatica_defeito WHERE reqidreqieroid = reqieroid) AS total_def_exec
                FROM
                    req_informatica
                    INNER JOIN req_informatica_execucao ON reqiexreqioid = reqioid
                    INNER JOIN req_informatica_fase ON reqiexreqifsoid = reqifsoid
                    INNER JOIN req_informatica_execucao_recurso ON reqierreqiexoid = reqiexoid
                WHERE 
                    reqierdt_exclusao IS NULL
                    AND reqiexreqioid = " . $reqioid . "
                ORDER BY
                    reqiexordem,
                    reqiexlancamento";

		$rs = pg_query($this->conn, $sql);

		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$retorno[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
		}

		return $retorno;
	}
	
	/**
	 * Totaliza defeitos por execução
	 * @param  integer $reqieroid [description]
	 * @return [type]             [description]
	 */
	public function getDefeitosExecucao($reqieroid=0) {
		$retorno = array();

		$sql = "SELECT COUNT(*) AS total_def_exec FROM req_informatica_defeito WHERE reqidreqieroid = ".$reqieroid; 

		$rs = pg_query($this->conn, $sql);

		if(pg_num_rows($rs) > 0) {
			return pg_fetch_array($rs, 0, PGSQL_ASSOC);
		}
	}
	
	
	/**
	 * getComboBoxListSubtipo()
	 *
	 * @param $reqtoid (ID do tipo de solicitação)
	 * @return $vOptions (array com dados da consulta para montar select)
	 */ 
	public function getComboBoxListSubtipo($reqtoid=0) {
        $vOptions = array();
		$sqlQuery = "";
		$sqlQuery .= " SELECT rqstoid, rqsttipo FROM req_informatica_subtipo ";
		$sqlQuery .= " WHERE rqstdt_exclusao IS NULL ";
		$sqlQuery .= "   AND rqstreqtoid = " . $reqtoid;
		$sqlQuery .= " ORDER BY rqsttipo";
		$sqlQuery .= " LIMIT 2000 ";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$vOptions[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
		}
		return $vOptions;
	}

	/**
	 * getComboBoxUsuario()
	 *
	 * @param $reqifureqifcoid (ID da função do usuário)
	 * @return $vOptions (array com dados da consulta para montar select)
	 */
	public function getComboBoxUsuario($reqifureqifcoid=0) {
		$vOptions = array();
		$sqlQuery = "SELECT DISTINCT cd_usuario, nm_usuario
			FROM
			  usuarios,
			  req_informatica_funcao_usuario
			WHERE
			  reqifudt_exclusao IS NULL
			  and reqifureqifcoid = " . $reqifureqifcoid . "
			  and dt_exclusao IS NULL
			  and reqifuusuoid = cd_usuario
			  ORDER BY nm_usuario LIMIT 2000
		";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$vOptions[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
		}
		return $vOptions;
	}
	
	
	/**
	 * getComboBoxListRecursoFase()
	 *
	 * @param $reqiexoid (ID do tipo da fase)
	 * @return $vOptions (array com dados da consulta para montar select)
	 */
	public function getComboBoxListRecursoFase($reqiexoid=0) {
		$vOptions = array();
		$sqlQuery = "SELECT cd_usuario, nm_usuario
			FROM
			  usuarios,
			  req_informatica_funcao_usuario,
			  req_informatica_fluxo_fase,
			  req_informatica_execucao
			WHERE
			  reqiffoid = reqiexreqiffoid
			  and reqiexoid = " . $reqiexoid . "
			  and reqiffreqifcoid=reqifureqifcoid
			  and reqifuusuoid=cd_usuario
			  and reqifudt_exclusao is null
			  and dt_exclusao is null
			  ORDER BY nm_usuario LIMIT 2000
		";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$vOptions[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
		}
		return $vOptions;
	}

	
	/**
	 * getListRecursoFase()
	 *
	 * @param $reqiexoid (ID do tipo da fase)
	 * @return $vOptions (array com dados da consulta para montar a lista)
	 */
	public function getListRecursoFase($reqiexoid=0) {
		$vOptions = array();
		$sqlQuery = "
			SELECT R.reqieroid, 
				U.nm_usuario, 
				R.reqierreqiexoid, 
				to_char(R.reqierdt_inicio,'dd/mm/yyyy') as inicio_exec, 
				R.reqierusuoid_planejamento, 
				R.reqierusuoid_executor, 
	            to_char(R.reqierdt_previsao_inicio,'dd/mm/yyyy') as inicio,
    	        to_char(R.reqierdt_previsao_fim,'dd/mm/yyyy') as final,
				R.reqierhoras_estimadas 
			FROM req_informatica_execucao_recurso AS R LEFT JOIN usuarios AS U
				ON (U.cd_usuario = R.reqierusuoid_executor)
			WHERE R.reqierreqiexoid = " . $reqiexoid . "
			AND reqierdt_exclusao IS NULL 
			ORDER BY R.reqierreqiexoid 
			LIMIT 2000
		";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$vOptions[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
		}
		$vOptions['qtd_itens'] = pg_num_rows($rs);
		return $vOptions;
	}
	
	
	/**
	 * getDadosSTI()
	 *
	 * @param $reqioid (ID da STI)
	 * @return $vData (array com dados da consulta de uma determinada STI)
	 */ 
	public function getDadosSTI($reqioid) {
        $vData = array();
		$sqlQuery = " 
		 SELECT 
           reqioid as sti,
           reqioid_externo as numero_externo,
           to_char(reqiconclusao,'dd/mm/yyyy') as data_conclusao,
           to_char(reqiinicio_previsto,'dd/mm/yyyy') as prev_inicio,
           to_char(reqiconclusao_prevista,'dd/mm/yyyy') as prev_termino,
           reqireqtoid as tipo_sti,
           reqireqifoid as fluxo,
           reqisolicitante as solicitante,
           reqicntoid as centro_custo,
           reqireqinoid as natureza,
           reqipara as responsavel,
           reqiassunto as assunto,
           reqirproid as projeto,
           reqisuspensa as sti_suspender,
           reqidescricao as descricao,
           reqipontos_sascar as pontos_sascar,
           reqidefeito_fase_teste as defeito_testes
		FROM
           req_informatica 
		WHERE reqioid=$reqioid
		LIMIT 1
		";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			$vData = pg_fetch_array($rs, 0, PGSQL_ASSOC);
		}
		// Ajustes de dados/tipos
		$vData['sti_suspender'] = ($vData['sti_suspender'] == 't') ? '1' : '0';
		return $vData;
	}
	
	
	/**
	 * setDadosSTI()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function setDadosSTI() {
		$vData = array();
		$vData['action_st'] = 'nok';
		$vData['action_msg'] = 'Dados da STI atualizados com sucesso!';
		$vData = array_merge($vData, $_POST);
		$reqioid = (int) $_POST['sti'];
		$numero_externo = (int) $_POST['numero_externo'];
		$sti_concluida = (int) trim($_POST['sti_concluida']); // checkbox
		$data_conclusao = trim($_POST['data_conclusao']);
		$prev_inicio = trim($_POST['prev_inicio']);
		$prev_termino = trim($_POST['prev_termino']);
		$tipo_sti = (int) $_POST['tipo_sti'];
		$fluxo = (int) $_POST['fluxo'];
		$solicitante = (int) $_POST['solicitante'];
		$centro_custo = (int) $_POST['centro_custo'];
		$natureza = (int) $_POST['natureza'];
		$responsavel = (int) $_POST['responsavel'];
		$assunto = trim($_POST['assunto']);
		$projeto = (int) $_POST['projeto'];
		$descricao = trim($_POST['descricao']);
		$justificativa = trim($_POST['justificativa']);
		$sti_suspender = (int) trim($_POST['sti_suspender']); // checkbox
		$sti_novo_fluxo = (int) trim($_POST['sti_novo_fluxo']); // checkbox
		$pontos_sascar = (int) $_POST['pontos_sascar'];
		$defeito_testes = ($_POST['defeito_testes'] != '') ? (int) $_POST['defeito_testes'] : NULL;
		$defeito_testes_anterior = ($_POST['defeito_testes_anterior'] != '') ? (int) $_POST['defeito_testes_anterior'] : NULL;
		
		$modHistorico = $reqioid % 10;
		
		
		// Inicia Transação
		$stQuery = pg_query($this->conn, "BEGIN");
		if(!$stQuery){
			$vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Erro ao iniciar transação! <br>';
			return $vData;
		}
		// Monta a query
		$sqlUpdate = " UPDATE req_informatica SET ";
		if($numero_externo > 0){
			$sqlUpdate .= " reqioid_externo = $numero_externo, ";
		}
		if($data_conclusao != ''){
			$sqlUpdate .= " reqiconclusao = '$data_conclusao', ";
		}else{
			// reqiconclusao
			if($sti_concluida == 1){
				$sqlUpdate .= " reqiconclusao = now(), ";
			}else{
				$sqlUpdate .= " reqiconclusao = NULL, ";
			}
		}
		if($prev_inicio != ''){
			$sqlUpdate .= " reqiinicio_previsto = '$prev_inicio', ";
		}else{
			$sqlUpdate .= " reqiinicio_previsto = NULL, ";
		}
		if($prev_termino != ''){
			$sqlUpdate .= " reqiconclusao_prevista = '$prev_termino', ";
		}else{
			$sqlUpdate .= " reqiconclusao_prevista = NULL, ";
		}
				
		$sqlUpdate .= "
			reqireqtoid = $tipo_sti,
			reqireqifoid = $fluxo,
			reqisolicitante = $solicitante,
			reqicntoid = $centro_custo,
			reqireqinoid = $natureza,
		";
		if($projeto > 0){
			$sqlUpdate .= " reqirproid = $projeto, ";
		}
		// caso seja suspensa
		if($sti_suspender == 1){
			$sqlUpdate .= " reqisuspensa  = 't', ";
		}else{
			$sqlUpdate .= " reqisuspensa  = 'f', ";
		}
		// pontos sascar
		if($pontos_sascar > 0){
			$sqlUpdate .= " reqipontos_sascar = " . $pontos_sascar . ", ";
		}
		// defeito testes
		if($defeito_testes >= 0 && $_POST['defeito_testes'] != ''){
			$sqlUpdate .= " reqidefeito_fase_teste = " . $defeito_testes . ", ";
		}
		$sqlUpdate .= "
			reqipara = $responsavel
			WHERE reqioid = $reqioid";
		
		$stQuery = pg_query($this->conn, $sqlUpdate);
		
		// Testa resultado
		if(!$stQuery){
			// Commit Transação
			pg_query($this->conn, "ROLLBACK");
			$vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Erro ao gravar dados principais! <br>';
			return $vData;
		}
		// verifica se deve gravar histórico
		if($sti_suspender == 1){
			$sqlInsert = "
			INSERT INTO req_informatica_historico" . $modHistorico . "(
			  rihcadastro,
			  rihstatus,
			  rihobs,
			  rihreqioid,
			  rihusuoid
 			) VALUES (now(),
			 'AN',
			 'STI suspensa, justificativa: $justificativa',
			 $reqioid, " . 
			 $_SESSION['usuario']['oid'] . "
			);
			";
			
			$stQuery = pg_query($this->conn, $sqlInsert);
			// Testa resultado
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao gravar histórico! <br>';
				return $vData;
			}
		}else{
			$sqlInsert = "
			INSERT INTO req_informatica_historico" . $modHistorico . "(
						rihcadastro,
						rihstatus,
						rihobs,
						rihreqioid,
						rihusuoid
						) VALUES (now(),
						'AN',
						'Dados da STI alterados',
						$reqioid, " .
						$_SESSION['usuario']['oid'] . "
			);
			";
				
			$stQuery = pg_query($this->conn, $sqlInsert);
			// Testa resultado
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao gravar histórico! <br>';
				return $vData;
			}
		}
		
		// verifica se deve gravar histórico
		if(($defeito_testes !== $defeito_testes_anterior) && $_POST['defeito_testes'] != ''){
			$label_defeito_testes_anterior = ($_POST['defeito_testes_anterior'] == '') ? 'NULO' : $defeito_testes_anterior;
			$msg = "O Número de Defeitos em Fase de Testes foi editado manualmente, de " . $label_defeito_testes_anterior . " para " . $defeito_testes . ".";
			$sqlInsert = sprintf("INSERT INTO req_informatica_historico" . $modHistorico . " (rihstatus, rihobs, rihreqioid, rihusuoid) 
									VALUES ('%s', '%s', %d, %d)",
									'AN',
									$msg,
									$reqioid,
									$_SESSION['usuario']['oid']);

			$stQuery = pg_query($this->conn, $sqlInsert);
			// Testa resultado
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao gravar histórico! <br>';
				return $vData;
			}
		}

		// verifica se deve gravar histórico
		if($sti_novo_fluxo == 1){
			// ALTERA data de entrada das fases anteriores:
			
			$sqlUpdate = " UPDATE req_informatica_execucao SET reqiexnovo_fluxo = NOW()
			WHERE reqiexreqioid = $reqioid;
			";
			$stQuery = pg_query($this->conn, $sqlUpdate);
			// Testa resultado
			if(!$stQuery){
			// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao atualizar fases anteriores! <br>';
				return $vData;
			}
			
			$vLancamento = array();
			$novo_lancamento = 0;
			// Verifica se já possui algum registro (req_informatica_execucao)
			$sqlQuery = "SELECT reqiexoid, reqiexlancamento FROM req_informatica_execucao WHERE reqiexreqioid=$reqioid;";
			$rs = pg_query($this->conn, $sqlQuery);
			if(pg_num_rows($rs) > 0) {
				$vLancamento = pg_fetch_array($rs, 0, PGSQL_ASSOC);
				$novo_lancamento = $vLancamento['reqiexlancamento'] + 1;
			}
			
			// Insere o fluxo
			$sqlInsert = "INSERT INTO req_informatica_execucao
				(reqiexreqifsoid, 
				reqiexreqifoid, 
				reqiexordem, 
				reqiexlancamento, 
				reqiexreqioid, 
				reqiexreqiffoid) 
				(SELECT reqiffreqifsoid, 
				reqiffreqifoid, 
				reqiffordem, 
				$novo_lancamento, 
				$reqioid, 
				reqiffoid 
				FROM req_informatica_fluxo_fase 
				WHERE reqiffdt_exclusao is null
 				and reqiffreqifoid = $fluxo);
			";
			$stQuery = pg_query($this->conn, $sqlInsert);
			
			// Testa resultado
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao gravar dados de fluxo! <br>';
				return $vData;
			}
			
			$sqlInsert = "
			INSERT INTO req_informatica_historico" . $modHistorico . "(
				rihcadastro,
				rihstatus,
				rihobs,
				rihreqioid,
				rihusuoid
				) VALUES (now(),
				'AN',
				'Incluso Novo Fluxo',
				$reqioid, " .
				$_SESSION['usuario']['oid'] . "
			);
			";
				
			$stQuery = pg_query($this->conn, $sqlInsert);
			// Testa resultado
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao gravar histórico! <br>';
				return $vData;
			}
				
			
		}else{// atualização normal
			// Verifica se já possui algum registro (req_informatica_execucao)
			$sqlQuery = "SELECT reqiexoid FROM req_informatica_execucao WHERE reqiexreqioid=$reqioid;";
			$rs = pg_query($this->conn, $sqlQuery);
			if(pg_num_rows($rs) == 0) {
				// Insere o fluxo
				$sqlInsert = "INSERT INTO req_informatica_execucao
					(reqiexreqifsoid, 
					reqiexreqifoid, 
					reqiexordem, 
					reqiexlancamento, 
					reqiexreqioid, 
					reqiexreqiffoid) 
					(SELECT reqiffreqifsoid, 
					reqiffreqifoid, 
					reqiffordem, 
					1, 
					$reqioid, 
					reqiffoid 
					FROM req_informatica_fluxo_fase 
					WHERE reqiffdt_exclusao is null
	 				and reqiffreqifoid = $fluxo);
				";
				$stQuery = pg_query($this->conn, $sqlInsert);
				// Testa resultado
				if(!$stQuery){
					// Commit Transação
					pg_query($this->conn, "ROLLBACK");
					$vData['action_st'] = 'nok';
					$vData['action_msg'] = 'Erro ao gravar dados de fluxo! <br>';
					return $vData;
				}
			}
		}
		// Nenhum erro no processo
		// Commit Transação
		pg_query($this->conn, "COMMIT");
		$vData['action_st'] = 'ok';
		return $vData;
	}
	


	/**
	 * setPlanejamentoFase()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function setPlanejamentoFase() {
		$vData = array();
		$vData['action_st'] = 'nok';
		$vData['action_msg_pfase'] = 'Planejamento de fase realizado com sucesso!';
		$vData = array_merge($vData, $_POST);
		$reqioid = (int) $_POST['sti'];
		$modHistorico = $reqioid % 10;
		
		$sti_pfase = (int) $_POST['sti_pfase'];
		$sti_recurso = (int) $_POST['sti_recurso'];
		$sti_fase_inicio = trim($_POST['sti_fase_inicio']);
		$sti_fase_final = trim($_POST['sti_fase_final']);
		$sti_fase_horas = (int) $_POST['sti_fase_horas'];
	
		// Verifica se já não incluiu registro com os mesmos dados
        $sql = "SELECT
                    'dados repetidos' AS erro
                FROM
                    req_informatica_execucao_recurso 
                WHERE
                    reqierreqiexoid = $sti_pfase 
                    AND reqierusuoid_executor = $sti_recurso 
                    AND reqierdt_previsao_inicio = '$sti_fase_inicio' 
                    AND reqierdt_previsao_fim = '$sti_fase_final' 
                    AND reqierhoras_estimadas = $sti_fase_horas
                    AND reqierdt_exclusao IS NULL

                UNION

                SELECT
                    'recurso nao concluido'
                FROM
                    req_informatica_execucao_recurso
                WHERE
                    reqierreqiexoid = $sti_pfase 
                    AND reqierusuoid_executor = $sti_recurso 
                    AND reqierdt_conclusao IS NULL
                    AND reqierdt_exclusao IS NULL";
		$rs = pg_query($this->conn, $sql);
		if(pg_num_rows($rs) == 0) {
			// Inicia Transação
			$stQuery = pg_query($this->conn, "BEGIN");
			if(!$stQuery){
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao inciar a transação! <br>';
				return $vData;
			}
			// Insert da Fase
			$sqlInsert = "
				INSERT INTO req_informatica_execucao_recurso (
					reqierreqiexoid, 
					reqierusuoid_planejamento, 
					reqierusuoid_executor, 
					reqierdt_previsao_inicio, 
					reqierdt_previsao_fim, 
					reqierhoras_estimadas 
				) VALUES (
				$sti_pfase, " . 
				$_SESSION['usuario']['oid'] . ",
				$sti_recurso,
				'$sti_fase_inicio', 
				'$sti_fase_final', 
				$sti_fase_horas
				);
			";
			$stQuery = pg_query($this->conn, $sqlInsert);
			// Testa resultado
			if(!$stQuery){
			// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao gravar planejamento de fase! <br>';
				return $vData;
			}
			$sqlInsert = "
				INSERT INTO req_informatica_historico" . $modHistorico . "(
					rihcadastro,
					rihstatus,
					rihobs,
					rihreqioid,
					rihusuoid
					) VALUES (now(),
					'AN',
					'Programação de fase inserida.',
					$reqioid, " .
					$_SESSION['usuario']['oid'] . "
				);
			";
			$stQuery = pg_query($this->conn, $sqlInsert);
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao gravar Histórico! <br>';
				return $vData;
			}
			// Nenhum erro no processo
			// Commit Transação
			pg_query($this->conn, "COMMIT");
			$vData['action_st'] = 'ok';
			return $vData;
		}else{
            $erro = pg_fetch_result($rs, 0, 'erro');

			$vData['action_st'] = 'nok';

            switch ($erro) {
                case 'recurso nao concluido':
                    $vData['action_msg_pfase'] = 'Já existe um registro deste recurso não concluído para esta fase! <br>';
                    break;
                case 'dados repetidos':
                default:
                    $vData['action_msg_pfase'] = 'Já existe uma fase planejada com os mesmos dados! <br>';                    
                    break;
            }
			
			return $vData;
		}
	}
	

	/**
	 * unsetPlanejamentoFase()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function unsetPlanejamentoFase() {
		$vData = array();
		$vData = array_merge($vData, $_POST);
		$vData['action_st'] = 'nok';
		$vData['action_msg_pfase'] = 'Planejamento de fase excluído com sucesso!';
		$reqioid = (int) $_POST['sti'];
		$modHistorico = $reqioid % 10;
		$reqieroid = (int) $_POST['reqieroid'];
		
		// DELETA planejamento Fase
		// Verifica se já iniciou a execução da fase
		$sqlQuery = "SELECT reqierdt_inicio FROM req_informatica_execucao_recurso
			WHERE reqieroid=$reqieroid	
		";
		$vTeste['reqierdt_inicio'] = '';
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			$vTeste = pg_fetch_array($rs, 0, PGSQL_ASSOC);
		}
		if(strlen(trim($vTeste['reqierdt_inicio'])) == 0) {
			// Inicia Transação
			$stQuery = pg_query($this->conn, "BEGIN");
			if(!$stQuery){
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação! <br>';
				return $vData;
			}
			$sqlDelete = "
				UPDATE req_informatica_execucao_recurso 
					SET reqierdt_exclusao=now() 
				WHERE reqieroid=$reqieroid	
			";
			$stQuery = pg_query($this->conn, $sqlDelete);
			// Testa resultado
			if(!$stQuery){
			// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao deletar planejamento de fase! <br>';
				return $vData;
			}
			
			$sqlInsert = "
				INSERT INTO req_informatica_historico" . $modHistorico . "(
						rihcadastro,
						rihstatus,
						rihobs,
						rihreqioid,
						rihusuoid
						) VALUES (now(),
						'AN',
						'Programação de fase deletada',
						$reqioid, " .
						$_SESSION['usuario']['oid'] . "
				);
			";
			$stQuery = pg_query($this->conn, $sqlInsert);
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao gravar Histórico! <br>';
				return $vData;
			}
			
			// Nenhum erro no processo
			// Commit Transação
			pg_query($this->conn, "COMMIT");
			$vData['action_st'] = 'ok';
			return $vData;

		}else{
			$vData['action_st'] = 'nok';
			$vData['action_msg_pfase'] = 'Este planejamento não pode ser excluído pois sua execução já foi iniciada! <br>';
			return $vData;
		}
	}


	/**
	 * updatePlanejamentoFase()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function updatePlanejamentoFase() {
		$vData = array();
		$vData['action_st'] = 'nok';
		$vData['action_msg_pfase'] = 'Planejamento de fase alterado com sucesso!';
		$vData = array_merge($vData, $_POST);
		$reqioid = (int) $_POST['sti'];
		$modHistorico = $reqioid % 10;
		$reqieroid = (int) $_POST['reqieroid'];
		
		$sti_pfase = (int) $_POST['sti_pfase'];
		$sti_recurso = (int) $_POST['sti_recurso'];
		$sti_fase_inicio = trim($_POST['sti_fase_inicio']);
		$sti_fase_final = trim($_POST['sti_fase_final']);
		$sti_fase_horas = (int) $_POST['sti_fase_horas'];
	
		// Verifica se já não incluiu registro com os mesmos dados
		$sqlQuery = "SELECT
                        reqieroid
                    FROM
                        req_informatica_execucao_recurso
                    WHERE
                        reqierreqiexoid = $sti_pfase
                        AND reqieroid <> $reqieroid
                        AND reqierusuoid_executor = $sti_recurso
                        AND reqierdt_previsao_inicio = '$sti_fase_inicio'
                        AND reqierdt_previsao_fim = '$sti_fase_final'
                        AND reqierhoras_estimadas = $sti_fase_horas
                        AND reqierdt_exclusao IS NULL";

		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) == 0) {
			// Inicia Transação
			$stQuery = pg_query($this->conn, "BEGIN");
			if(!$stQuery){
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação! <br>';
				return $vData;
			}
			// Monta a query
			$sqlUpdate = " UPDATE req_informatica_execucao_recurso SET
				reqierreqiexoid = $sti_pfase,
				reqierusuoid_planejamento = " . $_SESSION['usuario']['oid'] . ",
				reqierusuoid_executor = $sti_recurso,
				reqierdt_previsao_inicio = '$sti_fase_inicio',
				reqierdt_previsao_fim = '$sti_fase_final',
				reqierhoras_estimadas = $sti_fase_horas
			WHERE reqieroid = $reqieroid";
			
			$stQuery = pg_query($this->conn, $sqlUpdate);
			// Testa resultado
			if(!$stQuery){
			// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao gravar planejamento de fase! <br>';
				return $vData;
			}
			// Gravar histórico
			$sqlInsert = "
				INSERT INTO req_informatica_historico" . $modHistorico . "(
						rihcadastro,
						rihstatus,
						rihobs,
						rihreqioid,
						rihusuoid,
						rihreqieroid
						) VALUES (now(),
						'AN',
						'Programação de fase alterada.',
						$reqioid, " .
						$_SESSION['usuario']['oid'] . ",
						$reqieroid 
			    );
			";
			$stQuery = pg_query($this->conn, $sqlInsert);
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg_pfase'] = 'Erro ao gravar Histórico! <br>';
				return $vData;
			}
			// Nenhum erro no processo
			// Commit Transação
			pg_query($this->conn, "COMMIT");
			$vData['action_st'] = 'ok';
			return $vData;
		}else{
			$vData['action_st'] = 'nok';
			$vData['action_msg_pfase'] = 'Existe outra fase planejada com os mesmos dados! <br>';
			return $vData;
		}
	}
	

	/**
	 * getDadosRecursoFase()
	 *
	 * @param $reqieroid (ID da STI)
	 * @return $vData (array com dados da consulta de uma determinada STI)
	 */
	public function getDadosRecursoFase($reqieroid) {
		$vData = array();
		$sqlQuery = "
			SELECT 
				reqieroid, 
				reqierreqiexoid,
				reqierusuoid_planejamento, 
				reqierusuoid_executor, 
				to_char(reqierdt_previsao_inicio,'dd/mm/yyyy') as reqierdt_previsao_inicio, 
				to_char(reqierdt_previsao_fim,'dd/mm/yyyy') as reqierdt_previsao_fim, 
				to_char(reqierdt_inicio,'dd/mm/yyyy') as reqierdt_inicio, 
				reqierhoras_estimadas
			FROM req_informatica_execucao_recurso
			WHERE reqieroid = " . $reqieroid . "
			LIMIT 1
		";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) > 0) {
			$vData = pg_fetch_array($rs, 0, PGSQL_ASSOC);
		}
		return $vData;
	}

	/**
	 * getFasesAbas()
	 *
	 * @param $fluxo_fase_lancamento (ID do fluxo + ID lançamento)
	 * @return $vData (array com dados de fases para criar abas)
	 */
	public function getFasesAbas($fluxo_fase_lancamento = '') {
		$vData = array();
		$vDataAbas = array();
		$vFaseFluxo = array();
		$vDataAbas['qtd_itens'] = 0;
		if($fluxo_fase_lancamento != ''){
			$vFaseFluxo = explode('-', $fluxo_fase_lancamento);
			
			$vData['fluxo_fases'] = (int) $vFaseFluxo[0];
			$vData['fluxo_fases_lancamento'] = (int) $vFaseFluxo[1];
			$vData['sti'] = (int) $vFaseFluxo[2];

            $sql = "SELECT DISTINCT
                        reqifsoid,
                        reqifsdescricao,
                        reqiffreqifsoid,
                        reqiexordem
                    FROM
                        req_informatica_execucao
                        INNER JOIN req_informatica_execucao_recurso ON reqierreqiexoid = reqiexoid
                        INNER JOIN req_informatica_fase ON reqiexreqifsoid = reqifsoid
                        INNER JOIN req_informatica_fluxo_fase ON reqiffreqifsoid = reqifsoid
                    WHERE
                        reqiexreqioid = " . $vData['sti'] . "
                        AND reqiexreqifoid = " . $vData['fluxo_fases'] . "
                        AND reqiexlancamento = " . $vData['fluxo_fases_lancamento'] . "
                    ORDER BY
                        reqiexordem";
		
			$rs = pg_query($this->conn, $sql);

			$vDataAbas['qtd_itens'] = (int) pg_num_rows($rs);
			if($vDataAbas['qtd_itens'] > 0) {
				for($i = 0; $i < pg_num_rows($rs); $i++) {
					$vDataAbas['relacao'][$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
				}
			}
		}
		$vDataAbas['filtro'] = $vData;
		return $vDataAbas;
	}

	/**
	 * getFasesAbasForm()
	 *
	 * @param $fase_lancamento_sti (fase+lancamento+sti)
	 * @return $vData (array com dados de fases para criar abas)
	 */
	public function getFasesAbasForm($fase_lancamento_sti = '') {
		$vDataAba = array();
		$vDataAba['qtd_itens'] = 0;	
		$vFase = explode('-', $fase_lancamento_sti);
			
		$vData['fase'] = (int) $vFase[0];
		$vData['lancamento'] = (int) $vFase[1];
		$vData['sti'] = (int) $vFase[2];
		
		$sql = "SELECT 
                    reqieroid,
                    reqierusuoid_executor AS executor,
                    reqierusuoid_planejamento AS planejamento,
                    nm_usuario AS recurso,
                    TO_CHAR(reqierdt_previsao_inicio,'dd/mm/yyyy') AS periodo_prev_ini,
                    TO_CHAR(reqierdt_previsao_fim,'dd/mm/yyyy') AS periodo_prev_fim,
                    TO_CHAR(reqierdt_inicio,'dd/mm/yyyy') AS periodo_realizado_ini,
                    TO_CHAR(reqierdt_conclusao,'dd/mm/yyyy') AS periodo_realizado_fim,
                    reqierprogresso AS progresso,
                    reqierdefeito, 
                    reqierdescricao_defeito AS descricao,
                    CASE
                        WHEN (SELECT TRUE FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL LIMIT 1) IS TRUE THEN
                            TO_CHAR((SELECT SUM(ahrqtde_hora) FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL ), 'HH24:MI')
                        ELSE 
                            TO_CHAR(CAST(reqierhoras_realizado || ' hours' AS INTERVAL), 'HH24:MI')
                    END AS horas_utilizadas,
                    TO_CHAR(SUM(
                        CASE
                            WHEN (SELECT TRUE FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL LIMIT 1) IS TRUE THEN
                                (SELECT SUM(ahrqtde_hora) FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL )
                            ELSE 
                                CAST(reqierhoras_realizado || ' hours' AS INTERVAL)
                        END
                    ) OVER(PARTITION BY reqiexoid), 'HH24:MI') AS total_horas_utilizadas
                FROM 
                    req_informatica_execucao
                    INNER JOIN req_informatica_execucao_recurso ON reqierreqiexoid = reqiexoid
                    INNER JOIN usuarios ON reqierusuoid_executor = cd_usuario
                WHERE 
                    reqiexreqifsoid = " . $vData['fase'] . "
                    AND reqiexlancamento = " . $vData['lancamento'] . "
                    AND reqiexreqioid = " . $vData['sti'] . "
                    AND reqierdt_exclusao is null
                ORDER BY recurso;";

		$rs = pg_query($this->conn, $sql);
		$vDataAba['qtd_itens'] = (int) pg_num_rows($rs);
		if($vDataAba['qtd_itens'] > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$vDataAba['relacao'][$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
            $vDataAba['total_horas_utilizadas'] = $vDataAba['relacao'][0]['total_horas_utilizadas'];
		}
		$vDataAba['filtro'] = $vData;
		return $vDataAba;
	}

	
	/**
	 * getFasesAbasFormRecurso()
	 *
	 * @param $reqiexoid (ID do recurso fase)
	 * @return $vData (array com dados de fases para criar abas)
	 */
	public function getFasesAbasFormRecurso($reqieroid = 0) {
	    $vDataAba = array();
	    $vDataAba['qtd_itens'] = 0;
        $reqieroid = (int) $reqieroid;	
	    $sql = "SELECT
                    reqieroid,
                    reqierusuoid_executor as executor,
                    reqierusuoid_planejamento as planejamento,
                    to_char(reqierdt_previsao_inicio,'dd/mm/yyyy') as periodo_prev_ini,
                    to_char(reqierdt_previsao_fim,'dd/mm/yyyy') as periodo_prev_fim,
                    to_char(reqierdt_inicio,'dd/mm/yyyy') as periodo_realizado_ini,
                    to_char(reqierdt_conclusao,'dd/mm/yyyy') as periodo_realizado_fim,
                    reqierprogresso as progresso,
                    reqierdefeito,
                    reqierdescricao_defeito as descricao,
                    CASE
                        WHEN (SELECT TRUE FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL LIMIT 1) IS TRUE THEN
                            TO_CHAR((SELECT SUM(ahrqtde_hora) FROM apontamento_hora WHERE ahrusuoid_colaborador = reqierusuoid_executor AND ahrreqieroid = reqieroid AND ahrexclusao IS NULL ), 'HH24:MI')
                        ELSE 
                            TO_CHAR(CAST(reqierhoras_realizado || ' hours' AS INTERVAL), 'HH24:MI')
                    END AS horas_utilizadas
                FROM
                    req_informatica_execucao_recurso
                    INNER JOIN req_informatica_execucao ON reqierreqiexoid = reqiexoid
                WHERE
                    reqieroid = $reqieroid";
	    
	    // reqierusuoid_executor
	    $rs = pg_query($this->conn, $sql);
	    $vDataAba['qtd_itens'] = (int) pg_num_rows($rs);
	    if($vDataAba['qtd_itens'] > 0) {
	        $vDataAba = array_merge($vDataAba, pg_fetch_array($rs, 0, PGSQL_ASSOC));
	    }
	    return $vDataAba;
	}
	
	
	
	/**
	 * setFaseExecucaoRecurso()
	 *
	 * @param $vFormData
	 * @return $vOper (array com dados do resultado da operação)
	 */
	
	public function setFaseExecucaoRecurso($vFormData) {
		$vOper = array();
		$vOper['action_st'] = 'nok';
		$vOper['action_msg'] = '<span class="msg">Dados atualizados com sucesso!</span>';
		$vOper['conclusao_st'] = 'N';
		$vOper['inicio_st'] = 'N';
		$rihobs = 'Dados de execução alterados';
		$stQuery = pg_query($this->conn, "BEGIN");
		if(!$stQuery){
			$vOper['action_st'] = 'nok';
			$vOper['action_msg'] = 'Erro ao iniciar a transação! <br>';
			return $vOper;
		}
		// Tratamento de dados e regras		
		$reqioid = (int) $vFormData['sti'];
		$reqieroid = (int) $vFormData['reqieroid_sel'];
		$modHistorico = $reqioid % 10;
		$cbx_iniciar_execucao = (int) $vFormData['cbx_iniciar_execucao'];
		$cbx_concluir_execucao = (int) $vFormData['cbx_concluir_execucao'];
		$reqierprogresso = (int) $vFormData['reqierprogresso'];
		$reqierprogresso = ($reqierprogresso > 100)? 100 : $reqierprogresso;
		$reqierprogresso = ($reqierprogresso < 0)? 0 : $reqierprogresso;
		$reqiexreqifsoid = $this->getFaseByReqieroid($reqieroid);
		
		if($reqierprogresso == 100){
			$cbx_concluir_execucao = 1;
		}
		if($cbx_concluir_execucao == 1){
			$reqierprogresso = 100;
		}
		$reqierdescricao_defeito = trim($vFormData['reqierdescricao_defeito']);
		// Monta a query
		$sqlUpdate = " UPDATE req_informatica_execucao_recurso SET
				reqierdt_previsao_inicio = '" . $vFormData['reqierdt_previsao_inicio'] . "',
				reqierdt_previsao_fim = '" . $vFormData['reqierdt_previsao_fim'] . "',";
		if($cbx_iniciar_execucao == 1){
		    if(($vFormData['reqierdt_inicio']) == ''){
			    $sqlUpdate .= " reqierdt_inicio = now(), ";
			    $vOper['inicio_st'] = 'S';
			    $vOper['reqierdt_inicio'] = date('d/m/Y');
		    }else{
		        $sqlUpdate .= " reqierdt_inicio = '" . $vFormData['reqierdt_inicio'] . "',";
		        $vOper['reqierdt_inicio'] = $vFormData['reqierdt_inicio'];
		    }
			$rihobs = 'Execução Iniciada';
			
		}else{
		    if(($vFormData['reqierdt_inicio']) == ''){
			    $sqlUpdate .= " reqierdt_inicio = null,";
			    $vOper['reqierdt_inicio'] = '';
		    }else{
		        $sqlUpdate .= " reqierdt_inicio = '" . $vFormData['reqierdt_inicio'] . "',";
			    $vOper['inicio_st'] = 'S';
		        $vOper['reqierdt_inicio'] = $vFormData['reqierdt_inicio'];
		    }
		}
		
		if($cbx_concluir_execucao == 1){
			$vOper['conclusao_st'] = 'S';
			$rihobs = 'Execução Concluída';
			if(trim($vFormData['reqierdt_conclusao']) != ''){
    			if(compara_duas_datas(date('d/m/Y'), $vFormData['reqierdt_conclusao'], 1)){
        			$sqlUpdate .= " reqierdt_conclusao = now(), ";
        			$vOper['reqierdt_conclusao'] = date('d/m/Y');
    			}else{
    			    $sqlUpdate .= " reqierdt_conclusao = '" . $vFormData['reqierdt_conclusao'] . "',";
    			    $vOper['reqierdt_conclusao'] = $vFormData['reqierdt_conclusao'];
    		    }
			}else{
			    $sqlUpdate .= " reqierdt_conclusao = now(), ";
			    $vOper['reqierdt_conclusao'] = date('d/m/Y');
			}
			$reqierprogresso = 100;
		}else{
		    if(($vFormData['reqierdt_conclusao']) == ''){
			    $sqlUpdate .= " reqierdt_conclusao = null,";
		    }else{
		        $sqlUpdate .= " reqierdt_conclusao = '" . $vFormData['reqierdt_conclusao'] . "',";
		        if(compara_duas_datas($vFormData['reqierdt_conclusao'], date('d/m/Y'), 1)){
			        $vOper['conclusao_st'] = 'S';
			        $reqierprogresso = 100;
		        }
		    }
		    $vOper['reqierdt_conclusao'] = $vFormData['reqierdt_conclusao'];
		}
		$vOper['reqierprogresso'] = $reqierprogresso;
		
		$sqlUpdate .= "
			reqierprogresso = $reqierprogresso,
			reqierdescricao_defeito = '$reqierdescricao_defeito'
		WHERE reqieroid = $reqieroid";
		$stQuery = pg_query($this->conn, $sqlUpdate);
		
		// Testa resultado
		if(!$stQuery){
			// Rollback Transação
			pg_query($this->conn, "ROLLBACK");
			$vOper['action_st'] = 'nok';
			$vOper['action_msg'] = 'Erro ao atualizar dados! <br>';
			return $vOper;
		}
		
		// Gravar histórico 
		$sqlInsert = "
			INSERT INTO req_informatica_historico" . $modHistorico . "(
			rihcadastro,
			rihstatus,
			rihobs,
			rihreqioid,
			rihusuoid,
			rihreqieroid
			) VALUES (now(),
			'AN',
			'" . $rihobs . "',
			$reqioid, " .
			$_SESSION['usuario']['oid'] . ", " .
			$reqieroid . "
			);
		";
		
		$stQuery = pg_query($this->conn, $sqlInsert);
		if(!$stQuery){
			// Commit Transação
			pg_query($this->conn, "ROLLBACK");
			$vOper['action_st'] = 'nok';
			return $vOper;
		}
		// Nenhum erro no processo
		// Commit Transação
		$stQuery = pg_query($this->conn, "COMMIT");  
		
		$queryMantis = "select sissti_defeito_fase_teste from sistema";  

		$resMantis = pg_query($queryMantis);  

		$idMantis = 0;  

		if(pg_num_rows($resMantis)) {  
			$idMantis = pg_fetch_result($resMantis, 0, 'sissti_defeito_fase_teste');  
		} 

		$arrMantis = explode(',', $idMantis);  
		
		if($stQuery) {  
			// Busca de bugs no Mantis
			if($cbx_concluir_execucao == 1 && (in_array(23, $arrMantis) || in_array(5, $arrMantis))) {   
				$rihusuoid = 2750;
				$rihstatus = 'AN';
				$bugs_mantis = $this->getDefeitosMantis($reqioid);
				if(!$bugs_mantis && !is_array($bugs_mantis)) { // Não conseguiu conectar
					$rihobs = "Não foi possível estabelecer conexão com a Base Mantis.";
					$qtd_bug_mantis = null;
				} elseif (count($bugs_mantis) == 0) { // Não encontrou bugs no Mantis
					$rihobs = "Nenhum Mantis gerado em Fase de Testes para a STI.";
					$qtd_bug_mantis = 0;
				} else {
					$plural = (count($bugs_mantis) > 1) ? "(s)" : "";
					$rihobs = "Mantis registrado" . $plural . " em fase de Teste: " . implode(',', $bugs_mantis);
					$qtd_bug_mantis = count($bugs_mantis);
				}

				$vOper['qtd_bugs_mantis'] = $qtd_bug_mantis;

				if (!is_null($qtd_bug_mantis)) {
					$sqlUpdate = "UPDATE req_informatica SET reqidefeito_fase_teste = " . $qtd_bug_mantis . " WHERE reqioid = " . $reqioid;
					$stQueryUpdate = pg_query($this->conn, $sqlUpdate);
				}

				$sqlHistorico = sprintf("INSERT INTO req_informatica_historico (rihstatus, rihobs, rihreqioid, rihusuoid, rihreqieroid) 
										VALUES ('%s', '%s', %d, %d, %d)",
										$rihstatus,
										$rihobs,
										$reqioid,
										$rihusuoid,
										$reqieroid);
				$stQueryHistorico = pg_query($this->conn, $sqlHistorico);

				if(!$stQueryUpdate || !$stQueryHistorico) {
					$vOper['action_st'] = 'nok';
					$vOper['action_msg'] = 'Erro ao gravar a quantidade de bugs encontrados no Mantis! <br>';
					return $vOper;
				}
			}
		} else {
			$vOper['action_st'] = 'nok';
			$vOper['action_msg'] = 'Erro ao finalizar a transação! <br>';
			return $vOper;
		}
		
    	$vOper['action_st'] = 'ok';
		return $vOper;
		
	}
	

	/**
	 * setArquivoEvidencia()
	 *
	 * @param none
	 * @return none
	 */
	public function setArquivoAnexo() {
		
		$vData = array();
		$vData['action_st'] = 'nok';
		$vData['action_msg'] = 'Arquivo anexado com sucesso!';
		
		$reqioid = (int) $_POST['sti'];
		$modHistorico = $reqioid % 10;
		$reqieroid = (int) $_POST['reqieroid_sel'];
		$vData['reqieroid'] = $reqieroid;
		// Tratamento efetivo do arquivo
		$arquivoAnexoDescricao = trim($_POST['arquivoAnexoDescricao']);
		try{
			if(trim($_FILES['arquivoAnexo']['name']) ==''){
				throw new exception ('Necessário selecionar o Arquivo de Evidência / Anexo!');
			}
			if($_FILES['arquivoAnexo']['error'] != 0){
				throw new exception ('Erro ao anexar arquivo!');
			}
			if($_FILES['arquivoAnexo']['size'] > 3000000){
				throw new exception ('Arquivo anexo muito grande!');
			}
			$arquivoNome = $_FILES['arquivoAnexo']['name'];
			
			$arquivoNome = substr_replace($arquivoNome, '#', strrpos($arquivoNome, '.'), 1);
			$arquivoNome = str_replace('.', '', $arquivoNome);
			$arquivoNome = str_replace(' ', '_', $arquivoNome);
			// Nome do arquivo STI_Exec-recurso_Nome-arquivo_Date('mmss')_.Extensão
			$arquivoNome = str_replace('#', '.', $arquivoNome);
			$vCacento  = array('ç', 'á', 'é', 'í', 'ó', 'ú', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û','Ç', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ã', 'Õ', 'Â', 'Ê', 'Î', 'Ô', 'Û');
			$sSacento  = array('c', 'a', 'e', 'i', 'o', 'u', 'a', 'o', 'a', 'e', 'i', 'o', 'u','C', 'A', 'E', 'I', 'O', 'U', 'A', 'O', 'A', 'E', 'I', 'O', 'U');
			$arquivoNome = str_replace( $vCacento, $sSacento, $arquivoNome);
				
			$vArquivo = explode('.', $arquivoNome);
			$arquivoNome = $reqioid . '_' . $reqieroid . '_' . $vArquivo[0] . '_' . date('si') . '.' .  $vArquivo[1];
			
			if(!in_array($vArquivo[1], array('txt', 'doc', 'sql','pdf', 'rar', 'zip', 'php', 'js', 'png', 'jpg', 'gif'))){
				throw new exception ('Extensão ' . $vArquivo[1] . ' não é permitida!');
			}
			$arquivoNomeCaminho = 'docs_rec_informatica/'.$arquivoNome;
			if(!move_uploaded_file($_FILES['arquivoAnexo']['tmp_name'], $arquivoNomeCaminho)){
				throw new exception ('Erro ao mover arquivo para pasta destino!');
			}else{
				// Grava anexo
				// Grava Histórico
				$stQuery = pg_query($this->conn, "BEGIN");
				if(!$stQuery){
					throw new exception ('Erro ao inciar a transação!');
				}
				// Monta a query de insert do anexo
				$sqlInsert = "INSERT INTO req_informatica_anexo(riareqioid, riaarquivo, riadescricao, riatipo, riausuoid, riareqieroid) 
						VALUES($reqioid, '$arquivoNome', '$arquivoAnexoDescricao', 'P', " . $_SESSION['usuario']['oid'] . ", $reqieroid);";

				$stQuery = pg_query($this->conn, $sqlInsert);
				if(!$stQuery){
					// Commit Transação
					pg_query($this->conn, "ROLLBACK");
					throw new exception ('Erro ao gravar dados do anexo!');
				}
				// Gravar histórico
				$sqlInsert = "
				INSERT INTO req_informatica_historico" . $modHistorico . "(
					rihcadastro,
					rihstatus,
					rihobs,
					rihreqioid,
					rihusuoid,
			        rihreqieroid
					) VALUES (now(),
					'AN',
					'Inclusão de Arquivo Anexo.',
					$reqioid, " .
        			$_SESSION['usuario']['oid'] . ", " .
        			$reqieroid . "
       			);
				";
				$stQuery = pg_query($this->conn, $sqlInsert);
				if(!$stQuery){
					// Commit Transação
					throw new exception ('Erro ao gravar Histórico!');
				}
				// Nenhum erro no processo
				// Commit Transação
				pg_query($this->conn, "COMMIT");
				$vData['action_st'] = 'ok';
								
			}
        }catch(exception $e){
        	// Retorna transação DB
        	$vErro = array();
			pg_query($this->conn, "ROLLBACK");
			$vData['action_msg'] = $e->getMessage();
		}
		return $vData;
	}
	

	/**
	 * getFasesAnexos()
	 *
	 * @param $reqieroid (ID do tipo da fase)
	 * @return $vOptions (array com dados da consulta para montar a lista)
	 */
	public function getFasesAnexos($reqieroid = 0) {
		$vOptions = array();
		$sqlQuery = "
			SELECT riaoid as id, 
  			    TO_CHAR(riacadastro,'dd/mm/yyyy')as data, 
                riaarquivo as arquivo, 
		        riadescricao as descricao, 
                nm_usuario as usuario 
            FROM 
                req_informatica_anexo, usuarios 
            WHERE 
                riausuoid=cd_usuario 
                and riareqieroid = " . $reqieroid . "
            ORDER BY riacadastro DESC
			LIMIT 100;
		";
		
		$rs = pg_query($this->conn, $sqlQuery);
		$vOptions['qtd_itens'] = pg_num_rows($rs);
		if($vOptions['qtd_itens'] > 0) {
			for($i = 0; $i < $vOptions['qtd_itens']; $i++) {
				$vOptions[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
			}
		}
		return $vOptions;
	}


	/**
	 * getFasesHistorico()
	 *
	 * @param $reqieroid (ID do tipo da fase)
	 * @return $vOptions (array com dados da consulta para montar a lista)
	 */
	public function getFasesHistorico($reqieroid = 0) {
	    $vOptions = array();
	    $sqlQuery = "
			SELECT 
              TO_CHAR(rihcadastro,'dd/mm/yyyy hh24:mi') as data, 
              rihobs  as observacao,
              nm_usuario as usuario
            FROM       
              req_informatica_historico,
              usuarios
            WHERE
              rihusuoid=cd_usuario
              and rihreqieroid = " . $reqieroid . "
            ORDER BY rihcadastro DESC
			LIMIT 100;
		";
	    $rs = pg_query($this->conn, $sqlQuery);
	    $vOptions['qtd_itens'] = pg_num_rows($rs);
	    if($vOptions['qtd_itens'] > 0) {
	        for($i = 0; $i < $vOptions['qtd_itens']; $i++) {
	            $vOptions[$i] = pg_fetch_array($rs, $i, PGSQL_ASSOC);
	        }
	    }
	    return $vOptions;
	}
	
	/**
	 * excluiAnexo()
	 *
	 * @param  $reqioid (ID_sti), $reqieroid (ID_execucao_recurso), $riaoid (ID_anexo)
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function excluiAnexo($reqioid = 0, $reqieroid = 0, $riaoid = 0) {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Anexo excluído com sucesso!';
	    
	    $reqioid = (int) $reqioid;
	    $modHistorico = $reqioid % 10;
	    $reqieroid = (int) $reqieroid;
	    $riaoid = (int) $riaoid;
	     
	    // verifica se o arquivo existe em req_informatica_anexo 
		$sqlQuery = "SELECT riaarquivo FROM req_informatica_anexo WHERE riaoid=$riaoid";

		$vTeste['riaarquivo'] = '';
		$rs = pg_query($this->conn, $sqlQuery);

		if(pg_num_rows($rs) > 0) {
			$vTeste = pg_fetch_array($rs, 0, PGSQL_ASSOC);
		}

		if(trim($vTeste['riaarquivo']) != '') {
			// Inicia Transação
			$stQuery = pg_query($this->conn, "BEGIN");
			if(!$stQuery){
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação!';
				return $vData;
			}
			$sqlDelete = " DELETE FROM req_informatica_anexo WHERE riaoid=$riaoid";
			$stQuery = pg_query($this->conn, $sqlDelete);
	       // Testa resultado
			if(!$stQuery){
	            // Commit Transação
				pg_query($this->conn, "ROLLBACK");
	            $vData['action_st'] = 'nok';
	            $vData['action_msg'] = 'Erro ao deletar arquivo anexo!>';
	            return $vData;
	        }else{
	            //Renomeia arquivo para sufixo '_REMOVIDO'
	            $vArquivoNomeCaminho = array();
	            $arquivoNomeCaminho = 'docs_rec_informatica/'.$vTeste['riaarquivo'];
	            $vArquivoNomeCaminho = explode('.', $arquivoNomeCaminho);
	            rename('./' . $arquivoNomeCaminho, './' . $vArquivoNomeCaminho[0] . '_REMOVIDO.' . $vArquivoNomeCaminho[1]);
	        }
	        	
	        $sqlInsert = "
	        INSERT INTO req_informatica_historico" . $modHistorico . "(
    	        rihcadastro,
    	        rihstatus,
    	        rihobs,
    			rihreqioid,
    			rihreqieroid,
    			rihusuoid
    			) VALUES (now(),
    			'AN',
    			'Anexo excluído com sucesso',
    			$reqioid, 
    			$reqieroid, " .
    			$_SESSION['usuario']['oid'] . "
			);
			";

	        $stQuery = pg_query($this->conn, $sqlInsert);
			if(!$stQuery){
				// Commit Transação
				pg_query($this->conn, "ROLLBACK");
				$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao gravar Histórico!';
				return $vData;
			}
			// Commit Transação
			pg_query($this->conn, "COMMIT");
			$vData['action_st'] = 'ok';
			return $vData;
		}else{
			$vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Arquivo anexo não localizado!';
			return $vData;
		}
	}
	
	
	/**
	 * getDadosFluxo()
	 *
	 * @param $reqifoid (ID do fluxo)
	 * @return $vData (array com dados da consulta de uma determinada STI)
	 */
	public function getDadosFluxo($reqifoid = 0) {
	    $vData = array();
	    $reqifoid = (int) $reqifoid;
	    $sqlQuery = "
			SELECT
				reqifoid,
				reqifdescricao,
				reqifusuoid_responsavel
	        FROM req_informatica_fluxo
			WHERE reqifoid = " . $reqifoid . "
		";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) > 0) {
	        $vData = pg_fetch_array($rs, 0, PGSQL_ASSOC);
	    }
	    return $vData;
	}
	

	/**
	 * confirmarNovoFluxo()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function confirmarNovoFluxo() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fluxo incluído com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $reqifoid = (int) $_POST['reqifoid'];
	    $reqifdescricao = trim($_POST['reqifdescricao']);
	    $reqifusuoid_responsavel = (int) $_POST['reqifusuoid_responsavel'];
	
	    // Verifica se já não incluiu registro com os mesmos dados
	    $sqlQuery = "SELECT reqifoid FROM req_informatica_fluxo
	        WHERE reqifdescricao = '$reqifdescricao'
    	    AND reqifdt_exclusao is null
	    ";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) == 0) {
	        // Inicia Transação
	        $stQuery = pg_query($this->conn, "BEGIN");
	        if(!$stQuery){
	            $vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação!';
	            return $vData;
    	    }
    	    // Monta a query
    	    $sqlUpdate = " INSERT INTO req_informatica_fluxo (
    	        reqifdescricao,
        	    reqifdt_cadastro,
         	    reqifusuoid_responsavel) VALUES (
         	    '$reqifdescricao', 
         	    now(), 
         	    $reqifusuoid_responsavel)
    	    ";
    	    
    	    $stQuery = pg_query($this->conn, $sqlUpdate);
    	    // Testa resultado
    	    if(!$stQuery){
    	        // Commit Transação
    	        pg_query($this->conn, "ROLLBACK");
    		    $vData['action_st'] = 'nok';
    	        $vData['action_msg'] = 'Erro ao gravar novo fluxo!';
    		    return $vData;
    	    }
			// Nenhum erro no processo
			// Commit Transação
			pg_query($this->conn, "COMMIT");
			$vData['action_st'] = 'ok';
			return $vData;
		}else{
			$vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Existe outro fluxo com os mesmos dados!';
			return $vData;
		}
	}


	/**
	 * excluirFluxo()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function excluirFluxo() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fluxo excluído com sucesso!';
	    $reqifoid = (int) $_POST['reqifoid'];
	    	
	    // DELETA Fluxo
	    // Inicia Transação
	    $stQuery = pg_query($this->conn, "BEGIN");
	    if(!$stQuery){
	        $vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Erro ao inciar a transação!';
			return $vData;
	    }
	    $sqlDelete = "
	        UPDATE req_informatica_fluxo
	        SET reqifdt_exclusao=now()
	        WHERE reqifoid=$reqifoid
	    ";
	    $stQuery = pg_query($this->conn, $sqlDelete);
	    // Testa resultado
		if(!$stQuery){
	        // Commit Transação
		    pg_query($this->conn, "ROLLBACK");
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Erro ao deletar fluxo!';
	        return $vData;
	    }
		// Nenhum erro no processo
		// Commit Transação
		pg_query($this->conn, "COMMIT");
		$vData['action_st'] = 'ok';
		return $vData;
	}

	/**
	 * adicionarFaseFluxo()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function adicionarFaseFluxo() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fluxo incluído com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $reqiffreqifoid = (int) $_POST['reqifoid'];         // fluxo
	    $reqiffreqifsoid = (int) $_POST['reqiffreqifsoid']; // fase
   	    $reqiffordem = (int) $_POST['reqiffordem'];         // ordem
   	    $reqiffreqifcoid = (int) $_POST['reqiffreqifcoid']; // função
	
	    // Verifica se já não incluiu registro com os mesmos dados
	    $sqlQuery = "SELECT reqiffoid FROM req_informatica_fluxo_fase
	    WHERE reqiffreqifoid = $reqiffreqifoid
	        AND reqiffreqifsoid = $reqiffreqifsoid
	        AND reqiffreqifcoid = $reqiffreqifcoid
	        AND reqiffordem = $reqiffordem
	        AND reqiffdt_exclusao is null
	    ";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) == 0) {
	        // Inicia Transação
	        $stQuery = pg_query($this->conn, "BEGIN");
	        if(!$stQuery){
	        $vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação!';
	            return $vData;
	        }
	        // Monta a query
	        $sqlUpdate = " INSERT INTO req_informatica_fluxo_fase (
	        reqiffreqifoid,
	        reqiffreqifsoid,
	        reqiffreqifcoid,
	        reqiffordem,
	        reqiffdt_cadastro) VALUES (
	        $reqiffreqifoid,
	        $reqiffreqifsoid,
	        $reqiffreqifcoid,
	        $reqiffordem,
	        now())
	        ";
	        $stQuery = pg_query($this->conn, $sqlUpdate);
	        // Testa resultado
	        if(!$stQuery){
    	        // Commit Transação
    	        pg_query($this->conn, "ROLLBACK");
	            $vData['action_st'] = 'nok';
	            $vData['action_msg'] = 'Erro ao gravar configuração de Fluxo/Fase!';
	            return $vData;
    	    }
			// Nenhum erro no processo
			// Commit Transação
	        pg_query($this->conn, "COMMIT");
	        $vData['action_st'] = 'ok';
	        return $vData;
	    }else{
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Existe outro registro com a mesma configuração!';
			return $vData;
	    }
	}
	
	    /**
	    * excluirFaseFluxo()
	    *
	    * @param none
	    * @return $vData (array com dados do resultado da operação)
	    */
	
	public function excluirFaseFluxo() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fluxo excluído com sucesso!';
	    $reqiffoid = (int) $_POST['reqiffoid'];
	
	    // DELETA Fluxo
	    // Inicia Transação
	    $stQuery = pg_query($this->conn, "BEGIN");
	    if(!$stQuery){
	    $vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Erro ao inciar a transação!';
			return $vData;
	    }
	    $sqlDelete = "
	    UPDATE req_informatica_fluxo_fase
	        SET reqiffdt_exclusao=now()
	    WHERE reqiffoid=$reqiffoid
	    ";
	    $stQuery = pg_query($this->conn, $sqlDelete);
	    // Testa resultado
	    if(!$stQuery){
	        // Commit Transação
	        pg_query($this->conn, "ROLLBACK");
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Erro ao deletar configuração!';
	        return $vData;
	    }
	    // Nenhum erro no processo
	    // Commit Transação
	    pg_query($this->conn, "COMMIT");
	    $vData['action_st'] = 'ok';
	    return $vData;
	}


	/**
	 * getDadosFase()
	 *
	 * @param $reqifsoid (ID da Fase)
	 * @return $vData (array com dados da consulta de uma determinada STI)
	 */
	public function getDadosFase($reqifsoid = 0) {
	    $vData = array();
	    $reqifsoid = (int) $reqifsoid;
	    $sqlQuery = "
			SELECT
				reqifsoid,
				reqifsdescricao,
				reqifsdt_exclusao,
				reqifsdt_cadastro,
				reqifsreqifodoid
	        FROM req_informatica_fase
			WHERE reqifsoid = " . $reqifsoid . "
		";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) > 0){
	        $vData = pg_fetch_array($rs, 0, PGSQL_ASSOC);
	    }
	    return $vData;
	}
	
	
	/**
	 * confirmarNovaFase()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function confirmarNovaFase() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fase incluída com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $reqifsdescricao = trim($_POST['reqifsdescricao']);
	    $reqifodoid = strlen($_POST['reqifodoid']) > 0 ? $_POST['reqifodoid'] : "NULL";
	
	    // Verifica se já não incluiu registro com os mesmos dados
	    $sqlQuery = "SELECT reqifsoid FROM req_informatica_fase
	    WHERE reqifsdescricao = '$reqifsdescricao'
	    AND reqifsdt_exclusao is null
	    ";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) == 0) {
	    // Inicia Transação
	        $stQuery = pg_query($this->conn, "BEGIN");
	        if(!$stQuery){
	        $vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação!';
	            return $vData;
	        }
	        // Monta a query
	        $sqlUpdate = " INSERT INTO req_informatica_fase (
	        reqifsdescricao,
	        reqifsdt_cadastro,reqifsreqifodoid) VALUES (
	        '$reqifsdescricao',
	        now(),".$reqifodoid.")
	        ";
	        $stQuery = pg_query($this->conn, $sqlUpdate);
	        // Testa resultado
	        if(!$stQuery){
	        // Commit Transação
	        pg_query($this->conn, "ROLLBACK");
	            $vData['action_st'] = 'nok';
	            $vData['action_msg'] = 'Erro ao gravar nova fase!';
	            return $vData;
    	    }
			// Nenhum erro no processo
			// Commit Transação
	        pg_query($this->conn, "COMMIT");
	        $vData['action_st'] = 'ok';
	        return $vData;
	    }else{
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Existe outra fase com os mesmos dados!';
			return $vData;
	    }
	}
	
	
	/**
	* excluirFase()
	*
	* @param none
	* @return $vData (array com dados do resultado da operação)
	*/
	
	public function excluirFase() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fase excluída com sucesso!';
	    $reqifsoid = (int) $_POST['reqifsoid'];
	    
	    // DELETA Fase
	    $sqlQuery = "SELECT reqiffoid FROM req_informatica_fluxo_fase
	    WHERE reqiffreqifsoid = $reqifsoid
	        AND reqiffdt_exclusao IS NULL
	    ";
	    
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) == 0) {
	        // Inicia Transação
    	    $stQuery = pg_query($this->conn, "BEGIN");
    	    if(!$stQuery){
    	        $vData['action_st'] = 'nok';
    			$vData['action_msg'] = 'Erro ao inciar a transação!';
    			return $vData;
    	    }
    	    $sqlDelete = "
    	    UPDATE req_informatica_fase
    	        SET reqifsdt_exclusao=now()
    	    WHERE reqifsoid=$reqifsoid
    	    ";
    	    $stQuery = pg_query($this->conn, $sqlDelete);
    	    // Testa resultado
    	    if(!$stQuery){
    	        // Commit Transação
    	        pg_query($this->conn, "ROLLBACK");
    	        $vData['action_st'] = 'nok';
    	        $vData['action_msg'] = 'Erro ao deletar Fase!';
    	        return $vData;
    	    }
	    }else{
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Fase está sendo utilizada em um Fluxo Ativo!';
	        $vData = array_merge($vData, $_POST);
	        return $vData;
	    }
	    // Nenhum erro no processo
	    // Commit Transação
	    pg_query($this->conn, "COMMIT");
	    $vData['action_st'] = 'ok';
		return $vData;
	}

	/**
	 * Atualiza a origem do defeito da fase
	 * @return [type] [description]
	 */
	public function atualizarFase() {
		$vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Fase atualizada com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $reqifsoid = (int) $_POST['reqifsoid'];
	    $reqifodoid = strlen($_POST['reqifodoid']) > 0 ? $_POST['reqifodoid'] : 'NULL';
	
		if($reqifsoid > 0) {
		    // Inicia Transação
	        $stQuery = pg_query($this->conn, "BEGIN");

	        if(!$stQuery){
	        	$vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao atualizar a transação!';
	            return $vData;
	        }

	        // Monta a query
	        $sqlUpdate = " UPDATE 
	        					req_informatica_fase 
	    					SET 
	    						reqifsreqifodoid =".$reqifodoid."
							WHERE 
								reqifsoid = ".$reqifsoid."
	        ";
	        $stQuery = pg_query($this->conn, $sqlUpdate);

	        // Testa resultado
	        if(!$stQuery){
	        	// Commit Transação
	        	pg_query($this->conn, "ROLLBACK");
	            $vData['action_st'] = 'nok';
	            $vData['action_msg'] = 'Erro ao atualizar a fase!';
	            return $vData;
		    }

			// Nenhum erro no processo
			// Commit Transação
	        pg_query($this->conn, "COMMIT");
	        $vData['action_st'] = 'ok';
	        return $vData;
        } else {
        	$vData['action_st'] = 'nok';
            $vData['action_msg'] = 'Fase não informada!';
            return $vData;
        }
	}
	
// =============================================================================


	/**
	 * getDadosFuncao()
	 *
	 * @param $reqifcoid (ID do Funcao)
	 * @return $vData (array com dados da consulta de uma determinada STI)
	 */
	public function getDadosFuncao($reqifcoid = 0) {
	    $vData = array();
	    $reqifcoid = (int) $reqifcoid;
	    $sqlQuery = "
			SELECT
				reqifcoid,
				reqifcdescricao
	        FROM req_informatica_funcao
			WHERE reqifcoid = " . $reqifcoid . "
		";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) > 0) {
	        $vData = pg_fetch_array($rs, 0, PGSQL_ASSOC);
	    }
	    return $vData;
	}
	
	
	/**
	 * confirmarNovaFuncao()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function confirmarNovaFuncao() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Função incluída com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $reqifcdescricao = trim($_POST['reqifcdescricao']);
	
	    // Verifica se já não incluiu registro com os mesmos dados
	    $sqlQuery = "SELECT reqifcoid FROM req_informatica_funcao
	    WHERE reqifcdescricao = '$reqifcdescricao'
	        AND reqifcdt_exclusao is null
	    ";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) == 0) {
	        // Inicia Transação
	        $stQuery = pg_query($this->conn, "BEGIN");
	        if(!$stQuery){
	            $vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação!';
	            return $vData;
	        }
	        // Monta a query
	        $sqlUpdate = " INSERT INTO req_informatica_funcao (
	        reqifcdescricao,
	        reqifcdt_cadastro) VALUES (
	        '$reqifcdescricao',
	        now())
	        ";
	        	
	        $stQuery = pg_query($this->conn, $sqlUpdate);
	        // Testa resultado
	        if(!$stQuery){
    	        // Commit Transação
    	        pg_query($this->conn, "ROLLBACK");
	            $vData['action_st'] = 'nok';
	            $vData['action_msg'] = 'Erro ao gravar nova Funcao!';
	            return $vData;
    	    }
			// Nenhum erro no processo
			// Commit Transação
	        pg_query($this->conn, "COMMIT");
	        $vData['action_st'] = 'ok';
	        return $vData;
	    }else{
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Existe outra Função com os mesmos dados!';
			return $vData;
	    }
	}
	
	
	/**
	* excluirFuncao()
	*
	* @param none
	* @return $vData (array com dados do resultado da operação)
	*/
	
	public function excluirFuncao() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Função excluída com sucesso!';
	    $reqifcoid = (int) $_POST['reqifcoid'];
	
	    // DELETA Função
	    // Inicia Transação
	    $stQuery = pg_query($this->conn, "BEGIN");
	    if(!$stQuery){
	    $vData['action_st'] = 'nok';
			$vData['action_msg'] = 'Erro ao inciar a transação!';
			return $vData;
	    }
	    $sqlDelete = "
	    UPDATE req_informatica_Funcao
	        SET reqifcdt_exclusao=now()
	    WHERE reqifcoid=$reqifcoid
	    ";
	    $stQuery = pg_query($this->conn, $sqlDelete);
	    // Testa resultado
	    if(!$stQuery){
	    // Commit Transação
	        pg_query($this->conn, "ROLLBACK");
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Erro ao deletar Função!';
	        return $vData;
	    }
	    // Nenhum erro no processo
	    // Commit Transação
	    pg_query($this->conn, "COMMIT");
	    $vData['action_st'] = 'ok';
	    return $vData;
	}
	
	/**
	* adicionarFuncaoUsuario()
	*
	* @param none
	* @return $vData (array com dados do resultado da operação)
	*/
	public function adicionarFuncaoUsuario() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Programação de Função incluída com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $reqifcoid = (int) $_POST['reqifcoid'];            // Função
	    $reqifuusuoid = (int) $_POST['reqifuusuoid'];      // Usuário
	    $reqifureqieoid = (int) $_POST['reqifureqieoid'];  // Empresa
	
	    // Verifica se já não incluiu registro com os mesmos dados
	    $sqlQuery = "SELECT reqifuoid FROM req_informatica_funcao_usuario
	        WHERE reqifureqifcoid = $reqifcoid
	            AND reqifuusuoid = $reqifuusuoid
		        AND reqifureqieoid = $reqifureqieoid
		        AND reqifudt_exclusao is null
		";
		$rs = pg_query($this->conn, $sqlQuery);
		if(pg_num_rows($rs) == 0) {
		    // Inicia Transação
		    $stQuery = pg_query($this->conn, "BEGIN");
		    if(!$stQuery){
		        $vData['action_st'] = 'nok';
		        $vData['action_msg'] = 'Erro ao inciar a transação!';
		        return $vData;
	        }
	        // Monta a query
	        $sqlUpdate = " INSERT INTO req_informatica_funcao_usuario (
	            reqifureqifcoid,
	            reqifuusuoid,
		        reqifureqieoid,
		        reqifudt_cadastro) VALUES (
		        $reqifcoid,
		        $reqifuusuoid,
		        $reqifureqieoid,
		        now())
		    ";
		    $stQuery = pg_query($this->conn, $sqlUpdate);
		    // Testa resultado
	        if(!$stQuery){
		        // Commit Transação
		        pg_query($this->conn, "ROLLBACK");
		        $vData['action_st'] = 'nok';
		        $vData['action_msg'] = 'Erro ao gravar configuração de Função/Usuário!';
		        return $vData;
	        }
	        // Nenhum erro no processo
	        // Commit Transação
	        pg_query($this->conn, "COMMIT");
	        $vData['action_st'] = 'ok';
	        return $vData;
    	}else{
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Existe outro registro com a mesma configuração!';
	        return $vData;
	    }
	}
	
    /**
    * excluirFuncaoUsuario()
    *
    * @param none
    * @return $vData (array com dados do resultado da operação)
    */
    public function excluirFuncaoUsuario() {
        $vData = array();
        $vData['action_st'] = 'nok';
        $vData['action_msg'] = 'Função excluída com sucesso!';
    	$reqifuoid = (int) $_POST['reqifuoid'];
    	
        // DELETA Função
        // Inicia Transação
    	$stQuery = pg_query($this->conn, "BEGIN");
    	if(!$stQuery){
    	    $vData['action_st'] = 'nok';
    		$vData['action_msg'] = 'Erro ao inciar a transação!';
        	return $vData;
    	}
    	$sqlDelete = "
    	    UPDATE req_informatica_funcao_usuario
    	        SET reqifudt_exclusao=now()
    	    WHERE reqifuoid=$reqifuoid
    	";
    	$stQuery = pg_query($this->conn, $sqlDelete);
    	// Testa resultado
    	if(!$stQuery){
    	    // Commit Transação
    	    pg_query($this->conn, "ROLLBACK");
    	    $vData['action_st'] = 'nok';
    	    $vData['action_msg'] = 'Erro ao deletar configuração!';
    	    return $vData;
    	}
    	// Nenhum erro no processo
    	// Commit Transação
    	pg_query($this->conn, "COMMIT");
    	$vData['action_st'] = 'ok';
    	return $vData;
    }
	
	/**
	 * Retorna a fase de acordo com o reqieroid
	 * @param  int $reqieroid oid da tabela req_informatica_execucao_recurso
	 * @return int
	 */
	public function getFaseByReqieroid($reqieroid) {
		$fase = 0;
	
		$sql = "SELECT 
					reqiexreqifsoid 
				FROM req_informatica_execucao_recurso a
				INNER JOIN req_informatica_execucao b
				ON a.reqierreqiexoid = b.reqiexoid
				AND a.reqieroid = " . (int) $reqieroid;

		$rst = pg_query($this->conn, $sql);

		if($rst){
			$row = pg_fetch_assoc($rst,0);
			$fase = $row["reqiexreqifsoid"];
		}

		return $fase;
	}

	/**
	 * Busca os bugs gerados em fase de Testes na base do Mantis
	 * @param  int $reqioid id da tabela req_informatica (numero da STI)
	 * @return mixed (boolean|array)
	 */
	public function getDefeitosMantis($reqioid) {
		$defeitos = array();

		// Conexão Banco Mantis
		$hostname = "172.16.2.50";
		$database = "bugtracker";
		$username = "analista";
		$password = "4n4l1st4";

		$conn_mantis = @mysql_connect($hostname, $username, $password);

		if (!$conn_mantis) return false;

		$sel_db = mysql_select_db($database);

		if (!$sel_db) return false;

		$regex = '([^0-9]|^)' . $reqioid . '([^0-9]|$)';

		$sql_mantis = "SELECT
				           bug.id AS id
	                    FROM                     
	                        mantis_bug_table AS bug,
	                        mantis_custom_field_string_table AS cf                          
	                    WHERE                

	                        bug.project_id IN (25, 26, 52, 79, 46, 77, 75, 6, 12 ,13, 23, 28, 30, 31, 35, 40,
												42 ,43, 45, 50, 51, 53, 55, 56, 57 ,58, 60, 61, 62, 64, 65, 69, 71 ,72, 76, 73) 

	                        AND bug.id = cf.bug_id
	                        AND cf.field_id = 5
	                        AND bug.status NOT IN(40)
	                        AND 
	                        (SELECT mcf.value
	                            FROM mantis_custom_field_string_table AS mcf
	                            WHERE mcf.bug_id = bug.id
	                        AND  mcf.field_id = 7) REGEXP '$regex'
	                        AND  trim(cf.value) = 'Testes'                  
				  		ORDER BY bug.id";

		$rst = mysql_query($sql_mantis);

		while ($row = mysql_fetch_assoc($rst)) {
			$defeitos[] = $row["id"];
		}

		mysql_close($conn_mantis);

		return $defeitos;
	}

    public function getDetalhesApontamentos( $sti, $fase, $lancamento ) {
        $retorno = array();

        $sql = "SELECT
                    ahrreqioid AS sti,
                    nm_usuario AS usuario,
                    ahtdescricao AS tipo,
                    ahrqtde_hora AS horas,
                    TO_CHAR(ahrdata, 'DD/MM/YYYY') AS data
                FROM
                    apontamento_hora
                    INNER JOIN apontamento_hora_tipo ON ahrahtoid = ahtoid
                    INNER JOIN usuarios ON ahrusuoid_colaborador = cd_usuario
                    INNER JOIN req_informatica_execucao_recurso ON ahrreqieroid = reqieroid
                    INNER JOIN req_informatica_execucao ON reqierreqiexoid = reqiexoid
                WHERE
                    ahrusuoid_exclusao IS NULL
                    AND ahrreqioid = " . $sti . "
                    AND reqiexreqifsoid = " . $fase . "
                    AND reqiexlancamento = " . $lancamento . "
                    AND reqierdt_exclusao IS NULL
                ORDER BY
                    ahrdata DESC,
                    ahtdescricao";
        
        $resultado = pg_query($this->conn, $sql);

        if ( $resultado && pg_num_rows($resultado)) {
            while ( $row = pg_fetch_object($resultado) ) {
                $retorno[] = $row;
            }
        }

        return $retorno;
    }

    public function atualizarDefeitosAbaFase($vFormData) {
		$vOper = array();
		$vOper['action_st'] = 'nok';
		$vOper['action_msg'] = '<span class="msg">Dados atualizados com sucesso!</span>';

	    $reqioid = (int) $vFormData['sti'];

		$hostname = "172.16.2.50";
		$database = "bugtracker";
		$username = "analista";
		$password = "4n4l1st4";

		$conn_mantis = @mysql_connect($hostname, $username, $password);

		if (!$conn_mantis) {
			$vOper['action_st'] = 'nok';
			$vOper['action_msg'] = '<span class="msg">Não foi possível conectar ao banco de dados!</span>';
			return $vOper;
		}

		$sel_db = mysql_select_db($database);

		if (!$sel_db) {
			$vOper['action_st'] = 'nok';
			$vOper['action_msg'] = '<span class="msg">Não foi possível conectar ao banco de dados!</span>';
			return $vOper;
		}

		/*$sel_db = mysql_select_db($database);

		if (!$sel_db) {
			$vOper['action_st'] = 'nok';
			$vOper['action_msg'] = '<span class="msg">Não foi possível conectar ao banco de dados!</span>';
			return $vOper;
		}*/

		$sqlSelect = "SELECT
		               bug.id as id,
		               trim(cf.value) as ambiente_defeito,
		               (
		                              SELECT
		                                            mcf.value
		                              FROM
		                                            mantis_custom_field_string_table as mcf
		                              WHERE
		                                            mcf.bug_id = bug.id
		                                            AND mcf.field_id = 7
		               ) as sti,
		               CASE WHEN 
		                              (
		                                            SELECT
		                                                           TRIM(LOWER(SPLIT_STR(mcf.value, '.',1)))
		                                            FROM
		                                                           mantis_custom_field_string_table AS mcf
		                                            WHERE
		                                                           mcf.bug_id = bug.id
		                                                           AND mcf.field_id = 18
		                              ) <> '' THEN 
		                                                           (
		                                                                          SELECT
		                                                                                         TRIM(LOWER(SPLIT_STR(mcf.value, '.',1)))
		                                                                          FROM
		                                                                                         mantis_custom_field_string_table AS mcf
		                                                                          WHERE
		                                                                                         mcf.bug_id = bug.id
		                                                                          AND
		                                                                                         mcf.field_id = 18
		                                                           )
		                              ELSE
		                              (
		                                            SELECT
		                                                           TRIM(LOWER(SPLIT_STR(mcf.value, '.',1)))
		                                            FROM
		                                                           mantis_custom_field_string_table AS mcf
		                                            WHERE
		                                                           mcf.bug_id = bug.id
		                                                           AND mcf.field_id = 19
		                              )
		               END AS resp_erro,
		               (
		                              SELECT
		                                            TRIM(mcf.value)
		                              FROM
		                                            mantis_custom_field_string_table AS mcf
		                              WHERE
		                                            mcf.bug_id = bug.id
		                                            AND mcf.field_id = 6
		               ) AS origem_defeito,
		               (
		                              SELECT
		                                            mcf.value
		                              FROM
		                                            mantis_custom_field_string_table AS mcf
		                              WHERE 
		                                            mcf.bug_id = bug.id
		                                            AND mcf.field_id = 3
		               ) AS asm,
		               (
		                              SELECT
		                                            DATE_FORMAT(CAST(FROM_UNIXTIME(date_modified) AS DATE),'%d/%m/%Y') AS dt_alteracao
		                   FROM
		                              mantis_bug_history_table
		                   WHERE
		                              bug_id = bug.id
		                              AND field_name = 'status'
		                              AND new_value = 85
		                   ORDER BY
		                              date_modified 
		                   DESC LIMIT 1
		               ) as data_homologado,
		               CAST(FROM_UNIXTIME(bug.date_submitted) AS DATE) AS data_abertura 
		FROM 
		               mantis_bug_table AS bug, 
		               mantis_custom_field_string_table AS cf   
		WHERE 
		               bug.id = cf.bug_id 
		               #AND bug.id = 85399 
		   AND (SELECT mcf.value  
		       FROM mantis_custom_field_string_table AS mcf 
		       WHERE bug.id = mcf.bug_id  
		       AND mcf.field_id = 7 ) = '$reqioid'              
		               AND bug.status NOT IN(40) 
		               AND cf.field_id = 5 
		ORDER BY 
		               bug.date_submitted"; 
		$rs = mysql_query($sqlSelect); 

		if (mysql_num_rows($rs) <= 0 ) {		
			$vOper['action_st'] = 'ok';
			$vOper['action_msg'] = '<span class="msg">Não foi encontrado nenhum defeito.</span>';
			return $vOper;
		}

		while ($row = mysql_fetch_assoc($rs)) { 
			$mantis = addslashes($row["id"]); 
			$data_abertura = addslashes($row["data_abertura"]); 
			$data_homologacao = addslashes($row["data_homologado"]); 
			$asm = addslashes($row["asm"]); 
			$origem_defeito = addslashes($row["origem_defeito"]); 
			$resp_erro = addslashes($row["resp_erro"]); 
			$sti = addslashes($row["sti"]); 
 
			if(empty($mantis)) { 
				$mantis = 'null'; 
			} 
			if(empty($data_abertura)) { 
				$data_abertura = 'null'; 
			} else { 
				$data_abertura = "'".$data_abertura."'"; 
			} 
			if(empty($data_homologacao)) { 
				$data_homologacao = 'null'; 
			} else { 
				$data_homologacao = "'".$data_homologacao."'"; 
			} 
			if(empty($asm)) { 
				$asm = 'null'; 
			} 
			if(empty($origem_defeito)) { 
				$origem_defeito = 'null'; 
			} else { 
				$origem_defeito = "'".$origem_defeito."'"; 
			} 
			if(empty($resp_erro)) { 
				$resp_erro = 'null'; 
			} else { 
				$resp_erro = "'".$resp_erro."'"; 
			} 
			if(!is_numeric($asm)) { 
				$asm = 'null'; 
			} 
			if(empty($sti)) { 
				$sti = 'null'; 
			} 

			$sqlInsert = "INSERT INTO req_informatica_defeito( 
				reqidmantis, 
				reqidusuoid, 
				reqidreqioid, 
				reqidreqieroid, 
				reqidreqifsoid, 
				reqiddt_abertura_mantis, 
				reqiddt_homologacao_mantis, 
				reqidasm) 
			(SELECT  
			        $mantis,  
			        cd_usuario, 
			        reqiexreqioid, 
			        reqieroid as id_execucao, 
			        reqifsoid, 
			        $data_abertura, 
			        $data_homologacao, 
			        $asm 
			FROM  
				req_informatica_execucao LEFT OUTER JOIN req_informatica_execucao_recurso ON reqierreqiexoid=reqiexoid, 
				req_informatica_fase, 
				req_informatica_fase_origem_defeito, 
				usuarios  
			WHERE 
			        reqiexreqifsoid=reqifsoid  
			        AND reqifsreqifodoid = reqifodoid  
			        AND reqierusuoid_executor=cd_usuario  
			        AND reqierdt_exclusao IS NULL 
			        AND reqifoddescricao = $origem_defeito 
			        AND reqiexreqioid= $sti 
			        AND (SELECT COUNT(*) FROM req_informatica_defeito WHERE reqidmantis = $mantis ) = 0 
			        AND TRIM(LOWER(SPLIT_PART(nm_usuario,' ', 1))) = $resp_erro 
			ORDER BY 
				reqieroid desc LIMIT 1)"; 
			pg_query($sqlInsert); 
		}

		mysql_close($conn_mantis);

		$vOper['action_st'] = 'ok';
		$vOper['action_msg'] = '<span class="msg">Dados atualizados com sucesso.</span>';
		return $vOper;
    }

}