<?php

/**
 * Classe FinIsencaoFaturamentoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Financas
 * @author   MARCELLO BORRMANN <marcello.borrmann@meta.com.br>
 *
 */
class FinIsencaoFaturamentoDAO {

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
	 * Método para pesquisar os tipos de contrato para 
	 * os quais é possível realizar a paralisação
	 * 
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return string
	 * @throws ErrorException
	 */
	public function pesquisarTipoContrato(){
	
		$retorno = array();
	
		$sql = "
				SELECT 
					valvalor 
				FROM 
					valor 
					INNER JOIN registro ON regoid = valregoid 
					INNER JOIN dominio ON domoid = regdomoid 
				WHERE 
					domnome = 'TIPO CONTRATO ISENCAO FATURAMENTO'; ";

        //echo $sql;
		if ($rs = pg_query($this->conn,$sql)) {
			if (pg_num_rows($rs) > 0) {
				$retorno = pg_fetch_result($rs, 0, 'valvalor');
			}
			else {
				$retorno = "";
			}
		}
		
		return $retorno;		
		
	}
	
	

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @param string $tipoContratos
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros, $tipoContratos){

		$retorno = array();

		$sql = "
				SELECT DISTINCT
					CASE
						WHEN p.parfdt_exclusao IS NOT NULL THEN NULL 																-- Parâmetro Excluído
						WHEN (p.parfdt_ini_cobranca <= (NOW() - INTERVAL '12 months')) THEN NULL  									-- >= 12 meses 
						ELSE p.parfoid
					END AS parfoid, 
					CASE
						WHEN (NOW() < p.parfdt_ini_cobranca AND p.parfativo IS TRUE) THEN 'ap02' 									-- Isenção Programada 
						WHEN (NOW() BETWEEN p.parfdt_ini_cobranca AND p.parfdt_fin_cobranca AND p.parfativo IS TRUE) THEN 'ap03' 	-- Em Isenção 
						WHEN (p.parfdt_ini_cobranca > (NOW() - INTERVAL '12 months') AND p.parfativo IS TRUE) THEN 'ap04' 			-- < 12 meses 
						WHEN concsioid <> 1 THEN 'ap05' 																			-- Contrato ñ ativo
						WHEN conequoid IS NULL THEN 'ap13' 																			-- Eqpto. ñ instalado
						ELSE 'ap01' 																								-- Isentável
					END AS status,
					c.connumero, 
					s.csidescricao, 
					v.veiplaca, 
					l.clinome,
					CASE
						WHEN p.parfdt_exclusao IS NOT NULL THEN '' 																	-- Parâmetro Excluído
						WHEN (p.parfdt_ini_cobranca <= (NOW() - INTERVAL '12 months')) THEN '' 										-- >= 12 meses
						ELSE TO_CHAR(p.parfdt_ini_cobranca, 'DD/MM/YYYY') || ' a '  || TO_CHAR(p.parfdt_fin_cobranca, 'DD/MM/YYYY') 
					END AS periodo,
					(SELECT eqsdescricao FROM equipamento_status WHERE eqsoid=q.equeqsoid) AS eqsdescricao,
					(SELECT SUM(nfivl_item) FROM nota_fiscal_item WHERE nfinfloid=i.nfinfloid AND nficonoid=c.connumero AND nfiobroid=e.eqcobroid) AS vlr_equip,
					(SELECT SUM(nfivl_item) FROM nota_fiscal_item WHERE nfinfloid=i.nfinfloid AND nficonoid=c.connumero AND nfitipo='M') AS vlr_monit,
					(SELECT SUM(nfivl_item) FROM nota_fiscal_item WHERE nfinfloid=i.nfinfloid AND nficonoid=c.connumero AND nfiobroid IN
							(SELECT consobroid FROM contrato_servico WHERE consconoid=c.connumero AND conssituacao='L' AND consiexclusao IS NULL)) AS vlr_acess
				FROM 
					contrato c
					LEFT OUTER JOIN parametros_faturamento p ON (p.parfconoid = c.connumero AND p.parfativo IS TRUE AND p.parftipo = 'IS') 
					INNER JOIN veiculo v ON v.veioid = c.conveioid
					INNER JOIN clientes l ON l.clioid = c.conclioid
					INNER JOIN contrato_situacao s ON s.csioid = c.concsioid
					INNER JOIN nota_fiscal_item i ON i.nficonoid = c.connumero 
					LEFT OUTER JOIN equipamento q ON q.equoid = c.conequoid
					LEFT OUTER JOIN equipamento_classe e ON e.eqcoid = c.coneqcoid
				WHERE
		 			c.condt_exclusao IS NULL
		 			AND v.veidt_exclusao IS NULL
		 			AND l.clidt_exclusao IS NULL
					AND i.nfinfloid = (SELECT MAX(nfinfloid) FROM nota_fiscal_item WHERE nficonoid = c.connumero)
					";

		if ( isset($tipoContratos) && trim($tipoContratos) != '' ) {
		
			$sql .= "AND
                        c.conno_tipo IN (" . $tipoContratos . ") ";
		
		}
				
        if ( isset($parametros->placa_busca) && trim($parametros->placa_busca) != '' ) {

            $sql .= "AND
                        v.veiplaca = '" . ($parametros->placa_busca) . "'";
            
        }

        if ( isset($parametros->conoid_busca) && trim($parametros->conoid_busca) != '' ) {

            $sql .= "AND
                        c.connumero = " . intval($parametros->conoid_busca) . "";
            
        }

        if ( isset($parametros->cliente_busca) && trim($parametros->cliente_busca) != '' ) {

            $sql .= "AND
                        l.clinome ilike '" . ($parametros->cliente_busca) . "%'";
            
        }

        if ( isset($parametros->docto_busca) && trim($parametros->docto_busca) != '' ) {

            if ($parametros->tipo_cliente_busca == 'F'){
        	
	        	$sql .= "AND
	                        l.clino_cpf = " . ($parametros->docto_busca) . "";
            }
            else {
        	
	        	$sql .= "AND
	                        l.clino_cgc = " . ($parametros->docto_busca) . "";
            	
            }
            
        }
        $sql .= "
        		ORDER BY
					c.connumero; ";
        

        //echo $sql;
		$rs = pg_query($this->conn,$sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}


	/**
	 * Método para realizar a pesquisa de dados do contrato
	 *
	 * @param int $id Identificador contrato
	 * 		 date $dt Data de referência
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarContrato($id){

		$retorno = array();
	
		$sql = "
				SELECT DISTINCT
					conclioid, 
					conno_tipo, 
					coneqcoid,  
					nfiobroid,
					cliemail,
					clinome, 
					veiplaca
				FROM
					contrato
					LEFT OUTER JOIN contrato_servico ON (consconoid = connumero AND conssituacao = 'L' AND consiexclusao IS NULL)
					LEFT OUTER JOIN equipamento_classe ON eqcoid = coneqcoid
					LEFT OUTER JOIN nota_fiscal_item ON nficonoid = connumero
					LEFT OUTER JOIN clientes ON clioid = conclioid
					LEFT OUTER JOIN veiculo ON veioid = conveioid
				WHERE
					 connumero =" . intval( $id ) . "
					 AND nfiobroid NOT IN (SELECT obroid FROM obrigacao_financeira WHERE obrtipo_obrigacao = 'T')
				;";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}


	/**
	 * Método para realizar a pesquisa de dados da 
	 * Obrigação Financeira relacionada a taxa de 
	 * paralisação do faturamento
	 *
	 * @param
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarTaxa(){

		$retorno = new stdClass();
	
		$sql = " SELECT
					obroid,
					obrvl_obrigacao
				FROM
					dominio
					INNER JOIN registro ON regdomoid = domoid
					INNER JOIN valor ON valregoid = regoid
					INNER JOIN obrigacao_financeira ON obroid = valvalor::INTEGER
				WHERE
					domnome = 'TAXA DE PARALISACAO DE FATURAMENTO'
					AND domativo = 1
					AND obrdt_exclusao IS NULL";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}


	/**
	 * Método para realizar a pesquisa de dados de 
	 * E-mail de Isenção
	 *
	 * @param text $assunto Assunto do email
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarEmail($assunto){

		$retorno = new stdClass();
	
		$sql = " SELECT
					seecabecalho,
					seecorpo,
					seeimagem,
					seeimagem_anexo,
					seeremetente
				FROM
					servico_envio_email
				WHERE
					seecabecalho ILIKE '" . $assunto . "'
					AND seedt_exclusao IS NULL
				;";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}


	/**
	 * Método para realizar a pesquisa de dados de 
	 * Parâmero cadastrado
	 *
	 * @param int $id código do parâmetro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarParametro($id){

		$retorno = new stdClass();
	
		$sql = " 
				SELECT
					parfconoid, 
					parfemail_contato,  
					TO_CHAR(parfdt_ini_cobranca,'DD/MM/YYYY') AS parfdt_ini_cobranca, 
					TO_CHAR(parfdt_ini_cobranca, 'DD/MM/YYYY') || ' a ' || TO_CHAR(parfdt_fin_cobranca, 'DD/MM/YYYY') AS periodo,
					CASE 
						WHEN (NOW() < parfdt_ini_cobranca AND parfativo IS TRUE) THEN 'IP' 
						WHEN (NOW() BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca AND parfativo IS TRUE) THEN 'EI' 
						WHEN (parfdt_ini_cobranca > (NOW() - INTERVAL '12 months') AND parfativo IS TRUE) THEN 'NI' 
						ELSE 'IS' 
					END AS status, 
					(SELECT hpfprazo FROM historico_paralisacao_faturamento WHERE hpfparfoid = parfoid ORDER BY hpfoid DESC LIMIT 1) AS periodo_isencao 
				FROM
					parametros_faturamento
				WHERE
					parfoid = " . intval( $id ) . "
				;";

		//echo $sql;
		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}
	

	/**
	 * Responsável para inserir um registro no banco de dados.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirParametro(stdClass $dados){

		$sql = "INSERT INTO
					parametros_faturamento
						(
						parfusuoid_cadastro, 
						parfativo, 
						parfnivel,
						parfconoid,
						parfclioid,
						parftpcoid,
						parfeqcoid,
						parfisento,
						parfobservacao,
						parfdt_validade,
						parfdt_ini_cobranca, 
						parfdt_fin_cobranca,
						parfobroid_multiplo, 
						parfemail_contato,
						parftipo
					)
				VALUES
					(
					" . intval( $dados->parfusuoid_cadastro ) . ", 
					TRUE, 
					1, 
					" . intval( $dados->parfconoid ) . ", 
					" . intval( $dados->parfclioid ) . ",  
					" . intval( $dados->parftpcoid ) . ",  
					" . intval( $dados->parfeqcoid ) . ",  
					TRUE, 
					'Isencao Safra', 
					'" .$dados->parfdt_validade. "', 
					'" .$dados->parfdt_ini_cobranca. "', 
					'" .$dados->parfdt_fin_cobranca. "',
					'" .$dados->parfobroid. "',					 
					'" .$dados->parfemail_contato. "',
					'IS'
				)
				RETURNING parfoid; ";
		
		//echo $sql;
		if ($rs = pg_query($this->conn,$sql)) {
			if (pg_num_rows($rs) > 0) {
				$parfoid = pg_fetch_result($rs, 0, 'parfoid');
			}
		}
		else {
			throw new Exception("Houve um erro ao inserir Parametros do Faturamento.");
		}

		return $parfoid;
	}
	

	/**
	 * Responsável por inserir a taxa de Paralisação 
	 * de faturamento.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirTaxa(stdClass $dados){

		$sql = "INSERT INTO
					faturamento_unificado_taxas
						(
						futdt_referencia, 
						futclioid, 
						futobroid,
						futvalor,
						futconnumero,
						futstatus
					)
				VALUES
					(
					'" . $dados->futdt_referencia . "',
					" . intval( $dados->futclioid ) . ", 
					" . intval( $dados->obroid ) . ", 
					" . $dados->obrvl_obrigacao . ", 
					" . intval( $dados->futconnumero ) . ", 
					'P'
				); ";
		
		//echo $sql;
		if (! pg_query($this->conn,$sql)){
			throw new Exception("Houve um erro ao inserir Taxa de Paralisacao.");
		} 

		return true;
	}
	

	/**
	 * Responsável por inserir LOG de Paralisação 
	 * de faturamento.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirLog(stdClass $dados){

		$sql = "INSERT INTO
					historico_paralisacao_faturamento
						(
						hpfparfoid, 
						hpfconoid, 
						hpfusuoid,
						hpfdt_cadastro,
						hpfacao,
						hpfprazo
					)
				VALUES
					(
					" . intval( $dados->hpfparfoid ) . ",
					" . intval( $dados->hpfconoid ) . ",
					" . intval( $dados->hpfusuoid ) . ",  
					NOW(), 
					'" . $dados->hpfacao . "',
					" . intval( $dados->hpfprazo ) . "
				); ";
		
		//echo $sql;
		if (! pg_query($this->conn,$sql)){
			throw new Exception("Houve um erro ao inserir LOG.");
		} 

		return true;
	}
	

	/**
	 * Responsável por inserir Histórico de Paralisação 
	 * de faturamento, no contrato.
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function inserirHistoricoTermo(stdClass $dados){

		$sql = "SELECT 
					historico_termo_i(
						". $dados->hitconnumero .",
						". $dados->hitusuoid .",
						'". $dados->hitobs ."'
					); ";
		
		//echo $sql;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new Exception("Houve um erro ao inserir Historico do Termo.");
		} 

		return true;
	}
	

	/**
	 * Responsável por excluir a taxa de Paralisação 
	 * de faturamento.
	 * @param stdClass $dados Dados para identificar a tx
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluirTaxa(stdClass $dados){

		$sql = "DELETE
				FROM
					faturamento_unificado_taxas
				WHERE
					futconnumero = " . intval( $dados->futconnumero ) . "
					AND futobroid = " . intval( $dados->obroid ) . "
					AND futdt_referencia = '" . $dados->futdt_referencia . "'
				; ";
		
		//echo $sql;
		if (! pg_query($this->conn,$sql)){
			throw new Exception("Houve um erro ao excluir Taxa de Paralisacao.");
		} 

		return true;
	}
	

	/**
	 * Responsável por excluir o Parâmetro de faturamento.
	 * @param int $id código do parâmetro
	 *        int $usuoid código do usuário
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluirParametro($id, $usuoid){

		$sql = "UPDATE
					parametros_faturamento
				SET
					parfdt_exclusao = NOW(),
					parfativo = FALSE,
					parfusuoid_cadastro_alteracao = " . intval( $usuoid ) . ",
					parfusuoid_alteracao =	" . intval( $usuoid ) . "
				WHERE
					parfoid = " . intval( $id ) . " 
				; ";
		
		//echo $sql;
		if (! pg_query($this->conn,$sql)){
	    	throw new Exception("Houve um erro ao excluir Parametros do Faturamento.");
		}
		
		return true;
	}
	

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizarParametro(stdClass $dados){

		$sql = "UPDATE
					parametros_faturamento
				SET 
					parfdt_alteracao = NOW(),
					parfemail_contato = '" . $dados->parfemail_contato . "',
					parfdt_validade = '" . $dados->parfdt_fin_cobranca . "',
					parfdt_fin_cobranca = '" . $dados->parfdt_fin_cobranca . "', 
					parfusuoid_cadastro_alteracao = " . intval( $dados->parfusuoid_alteracao ) . ",
					parfusuoid_alteracao =	" . intval( $dados->parfusuoid_alteracao ) . "
				WHERE 
					parfoid = " . $dados->parfoid . "";

		if (! $rs = pg_query($this->conn,$sql)) {
			return false;
		}

		return true;
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
}
?>
