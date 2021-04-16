<?php 
 

/*
 * Camada de persistência Solicitação Vivo
 *
 * @author Dyorg Almeida
 * @email dyorg.almeida@meta.com.br
 * @since 25/10/2012
 *
 */
class PrnSolicitacaoVivoDAO {
	
	private $conn;
	
	/*
	 * - - Campos da tabela solicitacao_parceria - -
	 * 
	 * @description Id da solicitação
	 */
	public $slpoid;
	
	/*
	 * @description Data do cadastro
	 */
	public $slpdt_cadastro; 
	
	/*
	 * @description Data da exclusão
	 */
	public $slpdt_exclusao;

	/*
	 * @description Data da conclusão
	 */
	public $slpdt_conclusao;
	
	/*
	 * @description Número do protocolo vivo
	 */
	public $slpprotocolo_vivo; 

	/*
	 * @description Código do cliente
	 */
	public $slpclioid;
	
	/*
	 * @description Código do departamento do usuário
	 */
	public $slpdepoid;
	
	/*
	 * @description Código do motivo
	 */
	public $slpslpmoid;

	/*
	 * @description Código do status
	 */
	public $slpslpsoid;
	
	/*
	 * @description Descrição da solicitação
	 */
	public $slpdescricao;	

	/*
	 * @description Assunto
	 */
	public $slpassunto;
	
	/*
	 * @description Código do usuário
	 */
	public $slpusuoid;
	
	/*
	 * - - Campos exclusivo para uso da pesquisa - -
	 * 
	 * @description Data inicial informada para pesquisa
	 */
	public $dt_inicial_informada;
	
	/*
	 * @description Data final informada para pesquisa
	 */
	public $dt_final_informada;
	
	/*
	 * @description CPF ou CNPJ para pesquisa
	 */
	public $cpf_cnpj;

	/*
	 * @description Telefone para pesquisa
	 */
	public $telefone;
	
	/*
	 * - - Campos da tabela solicitacao_parceria_detalhe
	 * 
	 * @description Id do detalhe
	 */
	public $slpdoid;
	
	/*
	 * @description Id da solicitação relacionda ao detalhe
	 */
	public $slpdslpoid;
	
	/*
	 * @description Descrição do detalhe
	 */
	public $slpddescricao;
	
	/*
	 * @descricao Data do cadastro do detalhe
	 */
	public $slpddt_cadastro;
	
	/*
	 * @descricao Código do usuário
	 */
	public $slpdusuoid;
	
	/*
	 * @descricao Código do status no detalhe
	 */
	public $slpdslpsoid;
	
		
	public function __construct() {
		global $conn;
		$this->conn = $conn;
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Buscar solicitacao pelo id
	 * @param $idsolicitacao
	 */	
	public function buscarSolicitacao($idsolicitacao) {
		
		if (empty($idsolicitacao) || !is_numeric($idsolicitacao)) throw new Exception('O id da solicitação informado é inválido');
		
		$retorno = array();
		
		$sql  = " SELECT slpoid,"; 
		$sql .= " TO_CHAR(slpdt_cadastro,'dd/mm/yyyy hh24:mi') as slpdt_cadastro, slpdt_conclusao, ";
		$sql .= " EXTRACT(DAYS FROM ( (CASE WHEN slpdt_conclusao IS NULL THEN NOW() ELSE slpdt_conclusao END) - slpdt_cadastro)) as tempo_dias,";
		$sql .= " EXTRACT(DAYS FROM ( (CASE WHEN slpdt_conclusao IS NULL THEN NOW() ELSE slpdt_conclusao END) - slpdt_cadastro)) * 24 + EXTRACT(HOURS FROM ((CASE WHEN slpdt_conclusao IS NULL THEN NOW() ELSE slpdt_conclusao END) - slpdt_cadastro)) as tempo_horas,";
		$sql .= " slpprotocolo_vivo, slpdescricao, slpslpmoid, slpslpsoid,";
		$sql .= " clioid, clinome, depdescricao ";
		$sql .= " FROM solicitacao_parceria";
		$sql .= " INNER JOIN clientes ON slpclioid = clioid";
		$sql .= " INNER JOIN usuarios ON slpusuoid = cd_usuario";
		$sql .= " INNER JOIN departamento ON usudepoid = depoid";
		$sql .= " WHERE slpoid = " . $idsolicitacao;
		$sql .= " LIMIT 1";
			
		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar Solicitação");
		}
		
		return $retorno = pg_fetch_assoc($res);
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Salvar solicitação
	 */	
	public function salvarSolicitacao() {
		
		/*
		 * validação para bigint
		 */
		if(strlen($this->slpprotocolo_vivo) > 18) {
			throw new Exception('Número protocolo vivo excede o limite do campo especificado');
		}
		
		if (empty($this->slpoid)) {
			
			$sql  = " INSERT INTO solicitacao_parceria ("; 
			$sql .= " slpdt_cadastro,";
			$sql .= " slpdt_conclusao,";
			$sql .= " slpprotocolo_vivo,";
			$sql .= " slpclioid,";
			$sql .= " slpslpmoid,";
			$sql .= " slpslpsoid,";
			$sql .= " slpdepoid,";
			$sql .= " slpdescricao,";
			$sql .= " slpassunto,";
			$sql .= " slpusuoid";
			$sql .= ") VALUES ( ";
			$sql .= "'".$this->slpdt_cadastro . "',";
			$sql .= $this->slpslpsoid == '3' ? " NOW(), " : "NULL,";
			$sql .= "'".$this->slpprotocolo_vivo . "',";
			$sql .= $this->slpclioid . ",";
			$sql .= $this->slpslpmoid . ",";
			$sql .= $this->slpslpsoid . ",";
			$sql .= $_SESSION['usuario']['depoid'] . ",";
			$sql .= "'".$this->slpdescricao . "',";
			$sql .= "'',";
			$sql .= $_SESSION['usuario']['oid'] . ")";
			$sql .= " RETURNING slpoid";
			
			if (!$r = pg_query($this->conn, $sql)) {
				throw new Exception('Falha ao salvar valores - ' .pg_last_error($r), 1);
			}
			
			/*
			 * recupera o id do registro inserido
			 */
			$row = pg_fetch_row($r);
			$this->slpoid = $row[0];
			
			/*
			 * Salva inclusão no histórico do cliente
			 */
			$descricao =  "Inclusa Solicitação via Módulo Offline: $this->slpdescricao";
			$this->salvarHistorico($this->slpclioid, $descricao, 'O', $this->slpprotocolo_vivo);
			
			
			
		} else {
			
			$sql  = " UPDATE solicitacao_parceria SET ";
			$sql .= " slpdt_cadastro = '" . $this->slpdt_cadastro ."',";
			$sql .= " slpprotocolo_vivo = '" . $this->slpprotocolo_vivo ."',";
			$sql .= " slpclioid = ". $this->slpclioid .",";
			$sql .= " slpslpmoid = ". $this->slpslpmoid .",";
			$sql .= " slpslpsoid = ". $this->slpslpsoid .",";
			if ($this->slpslpsoid == '3') $sql .= " slpdt_conclusao = NOW(), ";
			$sql .= " slpdescricao = '". $this->slpdescricao ."'";
			$sql .= " WHERE slpoid = " .$this->slpoid;

			if (!$r = pg_query($this->conn, $sql)) {
				throw new Exception('Falha ao atualizar valores'.pg_last_error($r), 1);
			}
		}
		
		return true;
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Pesquisar solicitações
	 */	
	public function pesquisarSolicitacoes() {
		
		$retorno = array();		
		
		$sql  = " SELECT slpoid, TO_CHAR(slpdt_cadastro,'dd/mm/yyyy hh24:mi') as slpdt_cadastro, slpprotocolo_vivo, slpsdescricao, slpmdescricao, clinome";
		$sql .= " FROM solicitacao_parceria";
		$sql .= " INNER JOIN solicitacao_parceria_motivo ON slpslpmoid = slpmoid";
		$sql .= " INNER JOIN solicitacao_parceria_status ON slpslpsoid = slpsoid";
		$sql .= " INNER JOIN clientes ON slpclioid = clioid";
		$sql .= " WHERE slpdt_exclusao IS NULL";
		
		if (!empty($this->dt_final_informada) && !empty($this->dt_inicial_informada)) {
			$sql .= " AND slpdt_cadastro >= '". $this->dt_inicial_informada . " 00:00:00'";
			$sql .= " AND slpdt_cadastro <= '". $this->dt_final_informada . " 23:59:59'";
		}
		
		if (!empty($this->slpprotocolo_vivo)) {
			$sql .= " AND slpprotocolo_vivo = '" . $this->slpprotocolo_vivo . "'";
		}
		
		if (!empty($this->telefone)) {
			$sql .= " AND ( clifone_res = '" . $this->telefone . "' OR clifone_com = '". $this->telefone. "')";
		}
		
		if (!empty($this->cpf_cnpj)) {
			$sql .= " AND ( clino_cpf = '" . $this->cpf_cnpj . "' OR clino_cgc = '". $this->cpf_cnpj. "')";
		}
		
		if (!empty($this->slpslpmoid)) {
			$sql .= " AND slpslpmoid = " . $this->slpslpmoid ;
		}
		
		if (!empty($this->slpslpsoid)) {
			$sql .= " AND slpslpsoid = " . $this->slpslpsoid ;
		}
		
		$sql .= " ORDER BY TO_CHAR(slpdt_cadastro,'yyyy-mm-dd hh24:mi')";
		
		if ($res = pg_query($this->conn, $sql)) {
			$retorno = pg_fetch_all($res);
		}
		
		return $retorno;		
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Listar status da solicitação
	 */
	public function listarStatus() {
	
		$retorno = array();
	
		$sql  = " SELECT slpsoid, slpsdescricao ";
		$sql .= " FROM solicitacao_parceria_status"; 
		$sql .= " WHERE slpsdt_exclusao IS NULL";
		$sql .= " ORDER BY slpsdescricao";
			
		if ($r = pg_query($this->conn, $sql)) {
			$retorno = pg_fetch_all($r);
		}
		
		return $retorno;
	
	}
		
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Listar motivos da solicitação
	 */	
	public function listarMotivos() {
	
		$retorno = array();
		
		$sql  = " SELECT slpmoid, slpmdescricao";
		$sql .= " FROM solicitacao_parceria_motivo"; 
		$sql .= " WHERE slpmdt_exclusao IS NULL";
		$sql .= " ORDER BY slpmdescricao";
		
		if ($r = pg_query($this->conn, $sql)) {
			$retorno = pg_fetch_all($r);
		}

		return $retorno;
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Buscar motivo ativo pela descrição para verificar existencia
	 * @param $descricao Descrição do motivo a ser pesquisado
	 * @return Caso seja encontrado o motivo retorna o resultset, caso contrário retorna falso
	 */
	public function buscarMotivoPorDescricao($descricao) {
	
		$sql  = " SELECT * ";
		$sql .= " FROM solicitacao_parceria_motivo ";
		$sql .= " WHERE slpmdescricao = '". $descricao . "'";
		$sql .= " AND slpmdt_exclusao IS NULL";
			
		if ($r = pg_query($this->conn, $sql)) {
			return pg_fetch_assoc($r);
		} else  {
			return false;
		}
	}
		
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Salvar motivos da solicitação
	 * @param $descricao Descrição do motivo a ser inserido 
	 */
	public function salvarMotivo($descricao) {
		
		$sql  = " INSERT INTO solicitacao_parceria_motivo (slpmdescricao)";
		$sql .= " VALUES ('" . $descricao . "')";
		
		if (!$r = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao salvar valores: '.pg_last_error($r), 1);
		}
		
		return true;
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Remover motivos da solicitação
	 * @param $descricao Id do motivo a ser removido
 	 */	
	public function removerMotivo($idmotivo) {
	
		if (empty($idmotivo) || !is_numeric($idmotivo)) throw new Exception('O id do motivo informado é inválido');
		
		$sql  = " UPDATE solicitacao_parceria_motivo ";
		$sql .= " SET slpmdt_exclusao = NOW()";
		$sql .= " WHERE slpmoid = ". $idmotivo;
		
		if (!$r = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao alterar valores: '.pg_last_error($r), 1);
		}
		
		return true;
	}	
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Buscar detalhes pelo id da solicitação
	 * @param $idsolicitacao
	 */
	public function buscarDetalhes($idsolicitacao) {
	
		if (empty($idsolicitacao) || !is_numeric($idsolicitacao)) throw new Exception('O id da solicitação informado é inválido');
		
		$retorno = array();
	
		$sql  = " SELECT TO_CHAR(slpddt_cadastro,'dd/mm/yyyy hh24:mi') AS slpddt_cadastro, slpddescricao, nm_usuario ";
		$sql .= " FROM solicitacao_parceria_detalhe";
		$sql .= " INNER JOIN usuarios ON slpdusuoid = cd_usuario";
		$sql .= " WHERE slpdslpoid = " . $idsolicitacao;
		$sql .= " ORDER BY TO_CHAR(slpddt_cadastro,'yyyy-mm-dd hh24:mi') DESC";
			
		if ($res = pg_query($this->conn,$sql)) {
			 $retorno = pg_fetch_all($res);
		}
	
		return $retorno;
	}
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Salvar detalhes da solicitação
	 * @param $descricao Descrição do detlhe a ser inserido
	 * @param $idsolicitacao Id da solicitacao a ser relacionada o detalhe
	 */
	public function salvarDetalhe($descricao, $idsolicitacao, $slpdslpsoid) {
	
		$sql  = " INSERT INTO solicitacao_parceria_detalhe (";
		$sql .= " slpdslpoid, ";
		$sql .= " slpddescricao, ";
		$sql .= " slpdslpsoid, ";
		$sql .= " slpddt_cadastro, ";
		$sql .= " slpdusuoid ";
		$sql .= ") VALUES (";
		$sql .= $idsolicitacao . ",";
		$sql .= "'" . $descricao . "',";
		$sql .= $slpdslpsoid . ",";
		$sql .= "NOW() ,";
		$sql .= $_SESSION['usuario']['oid'] . ")";
		
		if (!$r = pg_query($this->conn, $sql)) {
			throw new Exception('Falha ao salvar valores'.pg_last_error($r), 1);
		}
	
		/*
		 * busca dados da solicitação para gravar o histórico do cliente
		 */
		if ($solicitacao = $this->buscarSolicitacao($idsolicitacao)) {
			$descricao = "Tratativa de Solicitação via Módulo Offline: $descricao";
			$this->salvarHistorico($solicitacao['clioid'], $descricao, 'T', $solicitacao['slpprotocolo_vivo']);
		} else {
			throw new Exception('Falha ao gravar o histório do cliente');
		}
		
		return true;
	}	
	
	/*
	 * @author Dyorg Almeida
	 * @email dyorg.almeida@meta.com.br
	 * @description Salvar histórico do cliente
	 * @param $clioid Id do cliente
	 * @param $descricao Descrição da alteração realizada
	 * @param $tipo representado por uma letra ('O', 'T')
	 * @param $protocolo protocolo
	 */	
	private function salvarHistorico($clioid, $descricao, $tipo, $protocolo ) {
		
		$sql  = " INSERT INTO cliente_historico" . ($clioid % 10). "(";
		$sql .= " clihclioid, ";
		$sql .= " clihusuoid, ";
		$sql .= " clihalteracao, ";
		$sql .= " clihtipo, ";
		$sql .= " clihprotocolo ";
		$sql .= " ) VALUES (";
		$sql .= $clioid . ",";
		$sql .= $_SESSION['usuario']['oid'] . ",";
		$sql .= "'" . $descricao . "',";
		$sql .= "'" . $tipo . "',";
		$sql .= "'" . $protocolo . "')";
		
		$r = pg_query($this->conn, $sql);
		
		if (!$r) {
			throw new Exception('Falha ao salvar valores'.pg_last_error($r), 1);
		}
		
		return true;
	}
	
}

?>
