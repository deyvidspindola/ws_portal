<?php
/**
 * Ferramenta para correção de baixas de estoque incorretas
 * 
 * @author Marcello Borrmann
 * @since 28/08/2015
 */
class ManCorrecaoBaixaEstoqueDAO {

	/**
	 * Link de conexão
	 * @var resource
	 */
	private $conn;

	/**
	 * Construtor
	 * @param resource $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}     

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

	/**
	 * Consulta os representantes 
	 * @param String filtro
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getRepresentanteResponsavelList($filtro = ''){
		$retorno  =  array();
		
		$sql = "SELECT
        			repoid,
        			repnome
        		FROM 
					representante
        		WHERE 
					repexclusao IS NULL 
					$filtro
				ORDER BY 
					repnome ";
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar representantes.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->repoid; 
			$retorno[$i]['representante'] = $row->repnome;
			$i++;
		}

		return $retorno;
	}
	
	/**
	 * Consulta os instaladores a partir de um representante selecionado
	 * @param Integer repoid_busca
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getInstaladorList($repoid_busca = ''){
		$retorno  =  array();
		
		if (!empty($repoid_busca)) {
			$filtro .= " AND itlrepoid = '".trim($repoid_busca)."' ";  
		}
		
		$sql = "SELECT 
					itloid,
					itlnome 
				FROM 
					instalador 
				WHERE 
					itldt_exclusao IS NULL 
					$filtro 
				ORDER BY 
					itlnome";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar instaladores.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->itloid; 
			$retorno[$i]['instalador'] = utf8_encode($row->itlnome);
			$i++;
		}

		return $retorno;		
	}
	
	/**
	 * Consulta as regiões
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getRegiaoList(){
		$retorno  =  array();
	
		$sql = "SELECT
					ftcoid,
					ftcfilial 
				FROM
					filial_tectran
				WHERE 
					ftcexclusao IS NULL
					AND ftctecoid = 1
				ORDER BY
					ftcfilial";	
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar regiões.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->ftcoid;
			$retorno[$i]['regiao'] = $row->ftcfilial;
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta tipos de OS
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getTipoList(){
		$retorno  =  array();
		/* 
		3; "RETIRADA";
		4; "ASSISTÊNCIA"; 
		*/
	
		$sql = "SELECT
					ostoid,
					ostdescricao
				FROM
					os_tipo
				WHERE 
					ostoid NOT IN (3,4)
				ORDER BY
					ostdescricao; ";
			
	
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar tipos de OS.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->ostoid;
			$retorno[$i]['tipo'] = $row->ostdescricao;
			$i++;
		}
	
		return $retorno;
	}

	/**
	 * Consulta classes de contrato
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getClasseContratoList(){
		$retorno  =  array();
	
		$sql = "SELECT
					eqcoid,
					eqcdescricao
				FROM
					equipamento_classe
				ORDER BY
					eqcdescricao";
			
	
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar classes de contrato.");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->eqcoid;
			$retorno[$i]['classe'] = $row->eqcdescricao;
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta dados de Ordens de Serviço que 
	 * tiveram materiais baixados incorretamente
	 * 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function pesquisar($filtro){
	
		$retorno = array();
		
		$sql ="
				SELECT DISTINCT
					obiordoid AS ordoid,
					TO_CHAR(obidt_ordem, 'dd/mm/yyyy') AS dt_ordem,
					obiconoid AS connumero,
					clinome,
					repnome,
					eprnome,
					(
						SELECT
							concatena(ostdescricao||'-'||otidescricao)
						FROM
							ordem_servico_item osit
							INNER JOIN os_tipo_item oti ON oti.otioid = osit.ositotioid
							INNER JOIN os_tipo ost ON ost.ostoid = oti.otiostoid 
						WHERE
							osit.ositordoid = obiordoid
							AND osit.ositexclusao IS NULL
							AND osit.ositstatus NOT IN('X','N')
					) AS tipo_motivo,
					CASE
						WHEN tpcseguradora IS TRUE
						THEN tpcnome_cia
						ELSE ''
					END AS seguradora,
					tpcdescricao,
					obiprdoid AS prdoid,
					prdproduto,
					prdlimitador_minimo,
					prdlimitador_maximo,
					obiqtd_baixada,
					SUM(obiqtd_necessaria) AS obiqtd_necessaria,
					COALESCE(espqtde,0) AS espqtde,
					COALESCE(espqtde_trans,0) AS espqtde_trans
				FROM
					os_baixa_incorreta
					INNER JOIN contrato ON connumero = obiconoid
					INNER JOIN clientes ON clioid = conclioid
					INNER JOIN equipamento ON equoid = conequoid
					INNER JOIN equipamento_versao ON eveoid = equeveoid
					INNER JOIN equipamento_projeto ON eproid = eveprojeto
					INNER JOIN tipo_contrato ON tpcoid = conno_tipo
					INNER JOIN representante ON repoid = obirepoid
					INNER JOIN relacionamento_representante ON (relroid = obirelroid AND relrrepoid = obirepoid)
					INNER JOIN produto ON prdoid = obiprdoid
					LEFT JOIN estoque_produto ON (espprdoid = obiprdoid AND esprelroid = obirelroid)
				WHERE
					TRUE
					AND obiqtd_baixada <> obiqtd_necessaria
					$filtro 
				GROUP BY
					obiordoid,
					obidt_ordem,
					obiconoid,
					clinome,
					repnome,
					eprnome,
					tpcseguradora,
					tpcnome_cia,
					tpcdescricao,
					obiprdoid,
					prdproduto,
					prdlimitador_minimo,
					prdlimitador_maximo,
					obiqtd_baixada,
					espqtde,
					espqtde_trans
				HAVING 
						CASE 
							WHEN SUM(obiqtd_necessaria) > 0
								THEN
									(obiqtd_baixada < prdlimitador_minimo OR obiqtd_baixada > prdlimitador_maximo)
							ELSE TRUE
						END 
				ORDER BY
					obiordoid,
					obiprdoid; ";
		
		//echo $sql;
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao consultar Ordens de Serviço com baixas incorretas.");
		}
 		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}
	
	/**
	 * Insere dados indicando o início da  
	 * geração de relatório de Diferença 
	 * da baixa ou Origem da baixa.
	 * 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function inserirDadosExecucao(stdClass $dados) {
		
		$parametros = array(
			$dados->tipo_csv,
			$dados->dataInicial,
			$dados->dataFinal,
			$dados->nomeusuoid_concl_busca,
			$dados->repoid_busca,
			$dados->itloid_busca,
			$dados->ftcoid_busca,
			$dados->otioid_busca,
			$dados->eqcoid_busca,
			$dados->conmodalidade_busca,
			$dados->idUsuario
			);
		
		$parametrosFormatados = implode('|', $parametros);
		
		// Inicia controle de execução de relatório concorrente
		$sql = "INSERT INTO execucao_relatorio_baixa_incorreta (
                    erbiusuoid,
                    erbitipo_processo,
                    erbiporcentagem,
                    erbiparametros
                ) 
				VALUES (
                    " . $dados->idUsuario . ",
                    '" . $dados->tipo_csv . "',
                    " . "0" . ",
                    '" . $parametrosFormatados . "'
                )";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao inserir controle de execução de relatório concorrente.");
		}
		
		return true;
		
	}
	
	/**
	 * Verfica se existe uma geração de relatório
	 * de Diferença da baixa ou Origem da baixa,
	 * em andamento e retorna dados referentes.
	 * 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function recuperarParametros($finalizado) {
	
		// Recupera os parâmetros salvos pelo resumo
		$sql = "SELECT
					nm_usuario,
					usuemail,
					erbioid,
					erbiusuoid,
					TO_CHAR(erbidt_inicio, 'HH24:MI:SS') as inicio,
					TO_CHAR(erbidt_termino, 'HH24:MI:SS') as termino,
					TO_CHAR(erbidt_inicio, 'DD/MM/YYYY HH24:MI:SS') as data_inicio,
					TO_CHAR(erbidt_termino, 'DD/MM/YYYY HH24:MI:SS') as data_termino,
					erbitipo_processo,
					erbiporcentagem,
					erbiparametros
				FROM
					execucao_relatorio_baixa_incorreta 
                    INNER JOIN usuarios on cd_usuario = erbiusuoid";
	
		if ($finalizado) {
			$sql .= "
                ORDER BY
                    erbidt_termino DESC";
		} else {
			$sql .= "
                AND
                    erbidt_termino IS NULL";
		}
	
		$sql .= " LIMIT 1";
		
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao recuperar parâmetros.");
		}
		elseif (pg_num_rows($rs) == 0){
			throw new Exception('Parâmetros não encontrados.');
		}
		
		$retorno = pg_fetch_object($rs);
		return $retorno;
		
	}
	
	/**
	 * Insere dados indicando o término da  
	 * geração de relatório de Diferença da 
	 * baixa ou Origem da baixa.
	 * 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function finalizarProcesso($resultado) {
	
		$sql = "
				UPDATE 
					execucao_relatorio_baixa_incorreta 
				SET
                	erbiporcentagem = 100,
                	erbidt_termino = NOW(),
                	erbiresultado = '" . $resultado . "'
            	WHERE
                	erbidt_termino IS NULL";
	
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao finalizar o processamento concorrente. Contate o administrador de sistemas.");
		}
	
		return true;
	}
	
	/**
	 * Insere dados indicando o andamento (%)
	 * da geração de relatório de Diferença da 
	 * baixa ou Origem da baixa.
	 * 
	 * @throws Exception
	 * @return resource|NULL
	 *//* Porcentagem da Consulta???
	public function atualizarProcesso() {
	
		$sql = "
                UPDATE
                    execucao_relatorio_baixa_incorreta
                SET
                    erbiporcentagem = " . round(($totalRegistros / $registrosConsultados) * 100, 1) . " 
                WHERE
                    erbidt_termino IS NULL";
	
		if (!pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao preparar consulta (Inserção de itens da nota fiscal)");
		}
	} */
	
	/**
	 * Consulta e retorna dados referentes
	 * ao usuário logado.
	 * 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function buscarDadosEmail($cd_usuario) {
	
		// Recupera dados do usuário
		$sql = "SELECT
					nm_usuario,
					usuemail
				FROM
					usuarios 
				WHERE
					dt_exclusao IS NULL
                	AND cd_usuario = $cd_usuario";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao recuperar dados do usuário.");
		}
		
		$retorno = pg_fetch_object($rs);
		return $retorno;
		
	}
	
	/**
	 * Consulta e retorna dados necessários à geração 
	 * do arquivo de Diferença da Baixa.
	 * 
	 * @throws Exception
	 * @return array
	 */
	public function gerarDiferencaCsv($filtro){
		
		$retorno = array();
		
		$sql = "
				SELECT DISTINCT
					TO_CHAR(obidt_conclusao, 'dd/mm/yyyy') AS dt_ordem,
					obiordoid AS ordoid,
					obirepoid AS repoid,
					repnome,
					prdproduto,
					obiprdoid AS prdoid,
					COALESCE((SUM(obiqtd_baixada) - SUM(obiqtd_necessaria)),0) AS qtde_corrigir,
					(
					SELECT
						concatena(ostdescricao||'-'||otidescricao)
					FROM
						ordem_servico_item osit
						INNER JOIN os_tipo_item oti ON oti.otioid = osit.ositotioid
						INNER JOIN os_tipo ost ON ost.ostoid = oti.otiostoid
					WHERE
						osit.ositordoid = obiordoid
						AND osit.ositexclusao IS NULL
						AND osit.ositstatus NOT IN('X','N')
					) AS tipo_motivo,
					(SELECT eqcdescricao FROM equipamento_classe WHERE eqcoid = coneqcoid LIMIT 1) AS classe_os,
					eveversao,
					CASE 
						WHEN conmodalidade = 'V' THEN 'REVENDA' 
						ELSE 'LOCAÇÃO'
					END AS modalidade,
					(SELECT COALESCE(pcmcusto_medio,0) FROM produto_custo_medio WHERE pcmprdoid = obiprdoid  AND pcmdt_exclusao IS NULL AND pcmusuoid_exclusao IS NULL ORDER BY pcmdt_referencia DESC LIMIT 1) AS vlr_unitario 
				FROM
					os_baixa_incorreta
					INNER JOIN contrato ON connumero = obiconoid
					INNER JOIN equipamento ON equoid = conequoid
					INNER JOIN equipamento_versao ON eveoid = equeveoid
					INNER JOIN representante ON repoid = obirepoid
					INNER JOIN produto ON prdoid = obiprdoid
				WHERE
					TRUE
					$filtro 
				GROUP BY
					obidt_conclusao,
					obiordoid,
					obirepoid,
					repnome,
					prdproduto,
					obiprdoid,
					coneqcoid,
					eveversao,
					conmodalidade
				ORDER BY
					obiordoid,
					obiprdoid; ";
		
		//echo $sql;
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao consultar dados de Diferença da Baixa.");
		}
 		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
				
	}

    /**
     * Método para buscar dados necessários para correção 
     *
     * @param $ordoid
     * @return array
     * @throws
     */
    public function buscarDadosCorrecao($ordoid){
		
		$retorno = array();
    	
		$sql = "
				SELECT DISTINCT
					COALESCE (ABS(obiqtd_baixada - SUM(obiqtd_necessaria)),0) AS obiqtdcorrigir,
					ABS(espqtde) AS obiqtdestoque,
					obiprdoid,
					obirelroid,
					obiconoid,
					(SELECT gmsubdescricao FROM grupo_material_subgrupo WHERE gmsuboid = prdgmsuboid) AS obigrupo_material, 
					prdproduto AS obiproduto,
					CASE  
						WHEN obiqtd_baixada < SUM(obiqtd_necessaria) THEN 'B'
						WHEN obiqtd_baixada > SUM(obiqtd_necessaria) THEN 'E'
						ELSE 'X'
					END AS obitipo
				FROM
					os_baixa_incorreta
					INNER JOIN produto ON prdoid = obiprdoid
					INNER JOIN estoque_produto ON (espprdoid = obiprdoid AND esprelroid = obirelroid)
				WHERE
					obiordoid = ".$ordoid."
					AND obiqtd_baixada <> obiqtd_necessaria 
				GROUP BY
					obiqtd_baixada,
					espqtde,
					obiprdoid,
					obirelroid,
					obiconoid,
					prdgmsuboid, 
					prdproduto,
					prdlimitador_minimo,
					prdlimitador_maximo
				HAVING 
						CASE 
							WHEN SUM(obiqtd_necessaria) > 0
								THEN
									(obiqtd_baixada < prdlimitador_minimo OR obiqtd_baixada > prdlimitador_maximo)
							ELSE TRUE
						END 
				ORDER BY
					obigrupo_material, 
					obiprdoid; ";
		
		//echo $sql;
		//exit;
    	if (!$rs = pg_query($this->conn, $sql)){
    		return null;
    	}
    		
    	while($registro = pg_fetch_object($rs)){
    		$retorno[] = $registro;
    	}
    	
    	return $retorno;
    }

    /**
     * Método para inserir registro na ordem de serviço por produto 
     *
     * @param $ordoid, $prdoid, $qtdeCorrigir, $usuoid
     * @return boolean
     * @throws Exception
     */
    public function inserirOSProduto($ordoid, $prdoid, $qtdeCorrigir, $usuoid){
	
		$sql = "
				INSERT INTO ordem_servico_produto(
		     		ospordoid,
		     		ospprdoid,
		     		ospqtde,
		     		ospcadastro,
		     		ospusuoid
     			)
    			VALUES(
    				".$ordoid.",
    				".$prdoid.",
    				".$qtdeCorrigir.",
    				NOW(),
    				".$usuoid."); ";
		
		//echo $sql;
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao inserir ordem de serviço por produto.");
		}
	
		return true;
    }

    /**
     * Método para atualizar estoque do representante 
     *
     * @param $relroid, $prdoid, $qtdeCorrigir
     * @return boolean
     * @throws Exception
     */
    public function atualizarEstoqueRepresentante($relroid, $prdoid, $qtdeCorrigir){
	
		$sql = "
				UPDATE
    				estoque_produto
    			SET
    				espqtde = (espqtde + ".$qtdeCorrigir.")
    			WHERE
    				esprelroid = ".$relroid."
    				AND espprdoid = ".$prdoid." ;";
		
		//echo $sql;
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao atualizar estoque do representante.");
		}
	
		return true;
    }

    /**
     * Método para inserir registro de movimentação do estoque
     *
     * @param $tipo, $origem, $ordoid, $qtde, $emtoid, $prdoid, $origem
     * @return boolean
     * @throws Exception
     */
    public function inserirmovimentacaoEstoque($tipo, $origem, $ordoid, $qtde, $emtoid, $prdoid, $correcao){
	
		$sql = "
				SELECT estoque_movimentacao_i ('
					\"NOW()\" 
					\"$tipo\" 
					\"$origem\" 
					\"$ordoid\" 
					\"$qtde\" 
					\"null\" 
					\"$emtoid\" 
					\"$prdoid\" 
					\"null\" 
					\"null\" 
					\"$correcao\"') ;";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao inserir movimentação do estoque.");
		}
	
		return true;
    }

    /**
     * Método para inserir registro de histórico do contrato
     *
     * @param $conoid, $usuoid, $observ
     * @return boolean
     * @throws Exception
     */
    public function inserirHistoricoTermo($conoid, $usuoid, $observ){
	
		$sql = "
				SELECT historico_termo_i(
					".$conoid.",
					".$usuoid.", 
					'".$observ."') ;";

		//echo $sql;
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao inserir histórico do contrato.");
		}
	
		return true;
    }

    /**
     * Método para buscar dados dos produtos da OS 
     *
     * @param $ordoid, $prdoid
     * @return boolean
     * @throws Exception
     */
    public function buscarOSProduto($ordoid, $prdoid){
	
		$retorno = array();
	
		$sql = "
				SELECT
				 	ospoid,
					ospqtde
				FROM 
					ordem_servico_produto
    			WHERE
    				ospordoid = ".$ordoid."
    				AND ospprdoid = ".$prdoid." ;";
		
		//echo $sql;
		//exit;
    	if (!$rs = pg_query($this->conn, $sql)){
    		throw new ErrorException("Erro ao consultar dados dos produtos da OS.");
    	}
    		
    	while($registro = pg_fetch_object($rs)){
    		$retorno[] = $registro;
    	}
    	
    	return $retorno;
    }

    /**
     * Método para atualizar ordem de serviço por produto 
     *
     * @param $ospoid, $qtdeCorrigir
     * @return boolean
     * @throws Exception
     */
    public function atualizarOSProduto($ospoid, $qtdeCorrigir){
	
		$sql = "
				UPDATE
    				ordem_servico_produto
    			SET
    				ospqtde = (ospqtde - ".$qtdeCorrigir.")
    			WHERE
    				ospoid = ".$ospoid." ;";
		
		//echo $sql;	
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao atualizar ordem de serviço por produto.");
		}
	
		return true;
    }

    /**
     * Método para atualizar origem da baixa 
     *
     * @param $ordoid, $usuoid, $qtdeCorrigir
     * @return boolean
     * @throws Exception
     */
    public function atualizarOrigemBaixa($ordoid, $prdoid, $qtdeCorrigir, $usuoid){
    	
		$sql = "
				UPDATE
    				os_baixa_incorreta
    			SET
    				obiqtd_baixada = (obiqtd_baixada + ".$qtdeCorrigir."),
					obiusuoid_correcao = ".$usuoid.", 
  					obidt_correcao = NOW()
    			WHERE
    				obiordoid = ".$ordoid." 
    				AND obiprdoid = ".$prdoid.";";
		
		//echo $sql;
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao atualizar origem da baixa.");
		}
	
		return true;
    }
	
}