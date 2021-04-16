<?php


/**
 * GrfAnaliseControleFalhasDAO.php
 * 
 * Camada de dados dos Gráficos para Análise e Controle de Falhas
 * 
 */
class GrfAnaliseControleFalhasDAO {
	
    /**
     * Conexão com o banco de dados
     * @var resource  
     */
	private $conn;
    
    /**
     * Data inicial do filtro
     * @var String 
     */
	private $data_inicio;
    
    /**
     * Data final do filtro
     * @var string 
     */
	private $data_fim;
	
	/**
	 * Construtor
	 * 
	 * @param object $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "ENTRADA LAB"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarEntradaLab($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararEntradaLab();
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "ENTRADA LAB RETIRADA"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarEntradaLabRetirada($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararEntradaLab('RETIRADA');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "SEM DEFEITO LAB"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarSemDefeitoLab($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararDefeitoLab('SEM DEFEITO');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "SEM DEFEITO LAB RETIRADA"
	 *
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarSemDefeitoLabRetirada($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararDefeitoLab('SEM DEFEITO', 'RETIRADA');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "COM DEFEITO LAB"
	 *
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarComDefeitoLab($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararDefeitoLab('COM DEFEITO');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "COM DEFEITO LAB RETIRADA"
	 *
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarComDefeitoLabRetirada($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararDefeitoLab('COM DEFEITO', 'RETIRADA');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "SEM ATUALIZAÇÃO CAMPO"
	 *
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarSemAtualizacaoCampo($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararSemAtualizacaoCampo();
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "SEM ATUALIZAÇÃO CAMPO X SEM DEFEITO LAB"
	 *
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarSemAtualizacaoCampoSemDefeitoLab($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararSemAtualizacaoCampo('SEM DEFEITO LAB');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "COM DEFEITO LAB X BASE INSTALADA"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarComDefeitoLabBaseInstalada($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararComDefeitoLabBaseInstalada();
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "COM DEFEITO LAB RETIRADA X BASE INSTALADA"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarComDefeitoLabRetiradaBaseInstalada($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararComDefeitoLabBaseInstalada('RETIRADA');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "DEFEITO LAB RVS"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarDefeitoLabRvs($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararDefeitoLabRvs();
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "DEFEITO LAB RVS TOTAL"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarDefeitoLabRvs_Total($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararDefeitoLabRvs(true);
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "GRÁFICO MTBF (LOCAÇÃO)"
	 *
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarMtbfLocacao($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararMtbf('L');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "GRÁFICO MTBF (VENDA)"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarMtbfVenda($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararMtbf('V');
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Pesquisa as infos. do gráfico: "AGING"
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return array:
	 */
	public function recuperarAging($dados) {
		$this->checarDados($dados);
		
		$sql = $this->prepararAging();
		
		$rs = pg_query($this->conn, $sql);
		
		if(!$rs) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return pg_fetch_all($rs);
	}
	
	/**
	 * Valida os dados recebidos
	 * 
	 * @param array $dados
	 * @throws Exception
	 * @return boolean
	 */
	private function checarDados($dados) {
		$this->data_inicio = (empty($dados['data_inicio'])) ? null : $dados['data_inicio'];
		$this->data_fim    = (empty($dados['data_fim'])) ? null : $dados['data_fim'];
		
		if(empty($this->data_inicio) || empty($this->data_fim)) {
			throw new Exception('Erro ao gerar gráfico.');
		}
		
		return true;
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 * 
	 * @param string $tipo
	 * @return string
	 */
	private function prepararEntradaLab($tipo = 'ASSISTÊNCIA') {
		return "
SELECT
	equipamento_projeto.eprnome AS nome_projeto,
	historico.hiedt_historico AS dt_entrada_lab,
	COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico
		FROM (
			SELECT
				sub_historico_6_10.hieequoid,
				MAX(sub_historico_6_10.hieconnumero) AS hieconnumero,
				historico_19.hiedt_historico
			FROM historico_equipamento AS sub_historico_6_10
				INNER JOIN (
					SELECT
						sub_historico_19.hieequoid,
						sub_historico_19.hiedt_historico
					FROM historico_equipamento AS sub_historico_19
					WHERE sub_historico_19.hieeqsoid = 19
					AND sub_historico_19.hiedt_historico
						BETWEEN '$this->data_inicio'::DATE + '00:00:00'::TIME
						AND '$this->data_fim'::DATE + '23:59:59'::TIME
				) AS historico_19 ON sub_historico_6_10.hieequoid = historico_19.hieequoid
			AND sub_historico_6_10.hieeqsoid IN (6, 10)
			GROUP BY
				sub_historico_6_10.hieequoid,
				historico_19.hiedt_historico
		) AS historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
				AND ordem_servico.ordconnumero = historico_equipamento.hieconnumero
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE os_tipo_item.otitipo = 'E'
		AND os_tipo.ostdescricao = '$tipo'
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
GROUP BY
	equipamento_projeto.eprnome,
	historico.hiedt_historico
ORDER BY
	nome_projeto,
	dt_entrada_lab
		";
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 * 
	 * @param string $flag
	 * @param string $tipo
	 * @return string
	 */
	private function prepararDefeitoLab($flag, $tipo = 'ASSISTÊNCIA') {
		switch($flag) {
			case "COM DEFEITO" :
				$flag = "FALSE";
				break;
			case "SEM DEFEITO" :
				$flag = "TRUE";
				break;
			default :
				throw new Exception('Erro ao gerar gráfico.');
		}
		
		return "
SELECT
	equipamento_projeto.eprnome AS nome_projeto,
	historico.hiedt_historico AS dt_entrada_lab,
	COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico,
			ordem_servico_item.ositoid
		FROM historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE historico_equipamento.hieeqsoid = 19
		AND historico_equipamento.hiedt_historico
			BETWEEN '$this->data_inicio'::DATE + '00:00:00'::TIME
			AND '$this->data_fim'::DATE + '23:59:59'::TIME
		AND os_tipo_item.otitipo = 'E'
		AND os_tipo.ostdescricao = '$tipo'
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
	INNER JOIN controle_falha ON equipamento_projeto.eproid = controle_falha.ctfeproid
		AND historico.equno_serie = controle_falha.ctfno_serie
		AND historico.ositoid = controle_falha.ctfositoid
	INNER JOIN item_falha_defeito ON controle_falha.ctfifdoid = item_falha_defeito.ifdoid
WHERE item_falha_defeito.ifdflag = $flag
GROUP BY
	equipamento_projeto.eprnome,
	historico.hiedt_historico
ORDER BY
	nome_projeto,
	dt_entrada_lab
		";
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 *
	 * @param string $grafico
	 * @return string
	 */
	private function prepararSemAtualizacaoCampo($grafico = 'SEM ATUALIZAÇÃO') {
		switch($grafico) {
			case "SEM ATUALIZAÇÃO" :
				$from  = "";
				$where = "";
				break;
			case "SEM DEFEITO LAB" :
				$from  = "
INNER JOIN controle_falha ON equipamento_projeto.eproid = controle_falha.ctfeproid
	AND historico.equno_serie = controle_falha.ctfno_serie
	AND historico.ositoid = controle_falha.ctfositoid
INNER JOIN item_falha_defeito ON controle_falha.ctfifdoid = item_falha_defeito.ifdoid
				";
				$where = "
WHERE item_falha_defeito.ifdflag = TRUE
				";
				break;
			default :
				throw new Exception('Erro ao gerar gráfico.');
		}
		
		return "
SELECT
	equipamento_projeto.eprnome AS nome_projeto,
	historico.hiedt_historico AS dt_entrada_lab,
	COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico,
			ordem_servico_item.ositoid
		FROM historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN ordem_servico_defeito ON ordem_servico_item.ositosdfoid_analisado = ordem_servico_defeito.osdfoid
				AND ordem_servico_defeito.osdfexclusao IS NULL
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE historico_equipamento.hieeqsoid = 19
		AND historico_equipamento.hiedt_historico
			BETWEEN '$this->data_inicio'::DATE + '00:00:00'::TIME
			AND '$this->data_fim'::DATE + '23:59:59'::TIME
		AND os_tipo_item.otitipo = 'E'
		AND (
			os_tipo.ostdescricao = 'ASSISTÊNCIA'
			OR os_tipo.ostdescricao = 'RETIRADA'
		)
		AND (
			ordem_servico_defeito.osdfdescricao ILIKE 'Não atualiza localização'
			OR ordem_servico_defeito.osdfdescricao ILIKE 'NAO ATUALIZA LOCALIZACAO'
		)
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
	$from
$where
GROUP BY
	equipamento_projeto.eprnome,
	historico.hiedt_historico
ORDER BY
	nome_projeto,
	dt_entrada_lab
		";
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 * 
	 * @param string $tipo
	 * @return string
	 */
	private function prepararComDefeitoLabBaseInstalada($tipo = 'ASSISTÊNCIA') {
		return "
SELECT
	equipamento_projeto.eprnome AS nome_projeto,
	historico.hiedt_historico AS dt_entrada_lab,
	total.equtotal AS qtd_total,
	COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico,
			ordem_servico_item.ositoid
		FROM historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE historico_equipamento.hieeqsoid = 19
		AND historico_equipamento.hiedt_historico
			BETWEEN '$this->data_inicio'::DATE + '00:00:00'::TIME
			AND '$this->data_fim'::DATE + '23:59:59'::TIME
		AND os_tipo_item.otitipo = 'E'
		AND os_tipo.ostdescricao = '$tipo'
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
	INNER JOIN (
		SELECT
			total_equipamento_projeto.eproid,
			COUNT(total_equipamento.equno_serie) AS equtotal
		FROM equipamento AS total_equipamento
			INNER JOIN equipamento_status AS total_equipamento_status ON total_equipamento.equeqsoid = total_equipamento_status.eqsoid
			INNER JOIN equipamento_versao AS total_equipamento_versao ON total_equipamento.equeveoid = total_equipamento_versao.eveoid
			INNER JOIN equipamento_projeto AS total_equipamento_projeto ON total_equipamento_versao.eveprojeto = total_equipamento_projeto.eproid
		WHERE total_equipamento.equdt_exclusao IS NULL
		AND total_equipamento_status.eqsdescricao ILIKE 'INSTALADO'
		GROUP BY
			total_equipamento_projeto.eproid
	) AS total ON equipamento_projeto.eproid = total.eproid
	INNER JOIN controle_falha ON equipamento_projeto.eproid = controle_falha.ctfeproid
		AND historico.equno_serie = controle_falha.ctfno_serie
		AND historico.ositoid = controle_falha.ctfositoid
	INNER JOIN item_falha_defeito ON controle_falha.ctfifdoid = item_falha_defeito.ifdoid
WHERE item_falha_defeito.ifdflag = FALSE
GROUP BY
	equipamento_projeto.eprnome,
	historico.hiedt_historico,
	total.equtotal
ORDER BY
	nome_projeto,
	dt_entrada_lab
		";
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 * 
	 * @return string
	 */
	private function prepararDefeitoLabRvs($total = false) {
		$select = "
historico.hiedt_historico AS dt_entrada_lab,
item_falha_defeito.ifddescricao AS desc_defeito,
COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
		";
		$group_by = "
historico.hiedt_historico,
item_falha_defeito.ifddescricao
		";
		$order_by = "
-- dt_entrada_lab,
-- desc_defeito,
qtd_equipamento DESC
		";
		
		if($total) {
			$select = "
historico.hiedt_historico AS dt_entrada_lab,
COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
			";
			$group_by = "
historico.hiedt_historico
			";
			$order_by = "
dt_entrada_lab,
qtd_equipamento DESC
			";
		}
		
		return "
SELECT
	$select
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico,
			ordem_servico_item.ositoid
		FROM historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE historico_equipamento.hieeqsoid = 19
		AND historico_equipamento.hiedt_historico
			BETWEEN '$this->data_inicio'::DATE + '00:00:00'::TIME
			AND '$this->data_fim'::DATE + '23:59:59'::TIME
		AND os_tipo_item.otitipo = 'E'
		AND (
			os_tipo.ostdescricao = 'ASSISTÊNCIA'
			OR os_tipo.ostdescricao = 'RETIRADA'
		)
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
	INNER JOIN controle_falha ON equipamento_projeto.eproid = controle_falha.ctfeproid
		AND historico.equno_serie = controle_falha.ctfno_serie
		AND historico.ositoid = controle_falha.ctfositoid
	INNER JOIN item_falha_defeito ON controle_falha.ctfifdoid = item_falha_defeito.ifdoid
WHERE equipamento_projeto.eprnome ILIKE 'RVS%'
AND item_falha_defeito.ifdflag = FALSE
GROUP BY
	$group_by
ORDER BY
	$order_by
		";
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 * 
	 * @return string
	 */
	private function prepararMtbf($tipo) {
		return "
SELECT
	equipamento_projeto.eprnome AS nome_projeto,
	historico.hiedt_historico AS dt_entrada_lab,
	historico.qtd_dias,
	COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			EXTRACT(days from (ordem_servico.orddt_ordem - historico_equipamento.hiedt_historico_6)) AS qtd_dias,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico
		FROM (
			SELECT
				sub_historico_6_10.hieequoid,
				MAX(sub_historico_6_10.hieconnumero) AS hieconnumero,
				historico_19.hiedt_historico,
				sub_historico_6_10.hiedt_historico AS hiedt_historico_6
			FROM historico_equipamento AS sub_historico_6_10
				INNER JOIN contrato ON sub_historico_6_10.hieconnumero = contrato.connumero
				INNER JOIN (
					SELECT
						sub_historico_19.hieequoid,
						sub_historico_19.hiedt_historico
					FROM historico_equipamento AS sub_historico_19
					WHERE sub_historico_19.hieeqsoid = 19
					AND sub_historico_19.hiedt_historico
						BETWEEN '".$this->data_inicio."'::DATE + '00:00:00'::TIME
						AND '".$this->data_fim."'::DATE + '23:59:59'::TIME
				) AS historico_19 ON sub_historico_6_10.hieequoid = historico_19.hieequoid
			AND sub_historico_6_10.hieeqsoid = 6
			AND contrato.conmodalidade = '$tipo'
			GROUP BY
				sub_historico_6_10.hieequoid,
				sub_historico_6_10.hiedt_historico,
				historico_19.hiedt_historico
		) AS historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
				AND ordem_servico.ordconnumero = historico_equipamento.hieconnumero
				AND ordem_servico.ordoid IN  (
					SELECT MAX(sub_ordem_servico.ordoid)
					  FROM ordem_servico AS sub_ordem_servico
				     INNER JOIN ordem_servico_item as sub_ordem_servico_item 
                        ON sub_ordem_servico.ordoid = sub_ordem_servico_item.ositordoid
				     INNER JOIN os_tipo_item as sub_os_tipo_item 
                        ON sub_ordem_servico_item.ositotioid = sub_os_tipo_item.otioid		
					 INNER JOIN os_tipo as sub_os_tipo 
                        ON sub_os_tipo_item.otiostoid = sub_os_tipo.ostoid
					 WHERE sub_ordem_servico.ordconnumero = historico_equipamento.hieconnumero
					   AND sub_ordem_servico.ordequoid = ordem_servico.ordequoid
					   AND sub_os_tipo_item.otitipo = 'E'
					   AND sub_os_tipo.ostdescricao = 'ASSISTÊNCIA'
				)
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE os_tipo_item.otitipo = 'E'
		AND os_tipo.ostdescricao = 'ASSISTÊNCIA'
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
WHERE historico.qtd_dias > 0
GROUP BY
	equipamento_projeto.eprnome,
	historico.hiedt_historico,
	historico.qtd_dias
ORDER BY
	nome_projeto,
	dt_entrada_lab,
	qtd_dias
		";
	}
	
	/**
	 * Prepara o SQL para pesquisa
	 * 
	 * @return string
	 */
	private function prepararAging() {
		return "
SELECT
	equipamento_projeto.eprnome AS nome_projeto,
	historico.hiedt_historico AS dt_entrada_lab,
	CASE 
		WHEN historico.qtd_dia < 31 THEN '00-1 a 30'
		WHEN historico.qtd_dia > 30 AND historico.qtd_dia < 61 THEN '01-31 a 60'
		WHEN historico.qtd_dia > 60 AND historico.qtd_dia < 91 THEN '02-61 a 90'
		WHEN historico.qtd_dia > 90 AND historico.qtd_dia < 121 THEN '03-91 a 120'
		WHEN historico.qtd_dia > 120 AND historico.qtd_dia < 151 THEN '04-121 a 150'
		WHEN historico.qtd_dia > 150 AND historico.qtd_dia < 181 THEN '05-151 a 180'
		WHEN historico.qtd_dia > 180 AND historico.qtd_dia < 211 THEN '06-181 a 210'
		WHEN historico.qtd_dia > 210 AND historico.qtd_dia < 241 THEN '07-211 a 240'
		WHEN historico.qtd_dia > 240 AND historico.qtd_dia < 271 THEN '08-241 a 270'
		WHEN historico.qtd_dia > 270 AND historico.qtd_dia < 301 THEN '09-271 a 300'
		WHEN historico.qtd_dia > 300 AND historico.qtd_dia < 331 THEN '10-301 a 330'
		WHEN historico.qtd_dia > 330 AND historico.qtd_dia < 366 THEN '11-331 a 365'
		WHEN historico.qtd_dia > 365 THEN '12-+1 ano'
	END as qtd_dia,		
	COUNT(DISTINCT historico.equno_serie) AS qtd_equipamento
FROM (
		SELECT
			equipamento.equoid,
			equipamento.equeveoid,
			equipamento.equno_serie,
			EXTRACT(days from (ordem_servico.orddt_ordem - historico_equipamento.hiedt_historico_6)) AS qtd_dia,
			TO_CHAR(historico_equipamento.hiedt_historico, 'YYYY-MM') AS hiedt_historico
		FROM (
			SELECT
				sub_historico_6_10.hieequoid,
				MAX(sub_historico_6_10.hieconnumero) AS hieconnumero,
				historico_19.hiedt_historico,
				sub_historico_6_10.hiedt_historico AS hiedt_historico_6
			FROM historico_equipamento AS sub_historico_6_10
				INNER JOIN (
					SELECT
						sub_historico_19.hieequoid,
						sub_historico_19.hiedt_historico
					FROM historico_equipamento AS sub_historico_19
					WHERE sub_historico_19.hieeqsoid = 19
					AND sub_historico_19.hiedt_historico
						BETWEEN '".$this->data_inicio."'::DATE + '00:00:00'::TIME
						AND '".$this->data_fim."'::DATE + '23:59:59'::TIME
				) AS historico_19 ON sub_historico_6_10.hieequoid = historico_19.hieequoid
			AND sub_historico_6_10.hieeqsoid = 6
			GROUP BY
				sub_historico_6_10.hieequoid,
				sub_historico_6_10.hiedt_historico,
				historico_19.hiedt_historico
		) AS historico_equipamento
			INNER JOIN equipamento ON historico_equipamento.hieequoid = equipamento.equoid
			INNER JOIN ordem_servico ON equipamento.equoid = ordem_servico.ordequoid
				AND ordem_servico.ordconnumero = historico_equipamento.hieconnumero
				AND ordem_servico.ordoid IN  (
					SELECT MAX(sub_ordem_servico.ordoid)
					  FROM ordem_servico AS sub_ordem_servico
				     INNER JOIN ordem_servico_item as sub_ordem_servico_item 
                        ON sub_ordem_servico.ordoid = sub_ordem_servico_item.ositordoid
				     INNER JOIN os_tipo_item as sub_os_tipo_item 
                        ON sub_ordem_servico_item.ositotioid = sub_os_tipo_item.otioid		
					 INNER JOIN os_tipo as sub_os_tipo 
                        ON sub_os_tipo_item.otiostoid = sub_os_tipo.ostoid
					 WHERE sub_ordem_servico.ordconnumero = historico_equipamento.hieconnumero
					   AND sub_ordem_servico.ordequoid = ordem_servico.ordequoid
					   AND sub_os_tipo_item.otitipo = 'E'
					   AND sub_os_tipo.ostdescricao = 'ASSISTÊNCIA'
				)
			INNER JOIN ordem_servico_item ON ordem_servico.ordoid = ordem_servico_item.ositordoid
			INNER JOIN os_tipo_item ON ordem_servico_item.ositotioid = os_tipo_item.otioid
			INNER JOIN os_tipo ON os_tipo_item.otiostoid = os_tipo.ostoid
		WHERE os_tipo_item.otitipo = 'E'
		AND os_tipo.ostdescricao = 'ASSISTÊNCIA'
	) AS historico
	INNER JOIN equipamento_versao ON historico.equeveoid = equipamento_versao.eveoid
	INNER JOIN equipamento_projeto ON equipamento_versao.eveprojeto = equipamento_projeto.eproid
WHERE historico.qtd_dia > 0
GROUP BY
	nome_projeto,
	dt_entrada_lab,
	qtd_dia
ORDER BY
    qtd_dia,
	nome_projeto,
	dt_entrada_lab
	
		";
	}
	
}