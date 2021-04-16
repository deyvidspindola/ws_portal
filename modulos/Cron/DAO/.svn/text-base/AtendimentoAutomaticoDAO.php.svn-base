<?php
require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';
require_once _MODULEDIR_ . 'Cron/VO/AtendimentoAutomaticoContatoVO.php';
require_once _MODULEDIR_ . 'Cron/VO/AtendimentoAutomaticoContratoVO.php';

/**
 * Classe abstrata de persistência dos dados de AtendimentoAutomaticoDAO, somente executa, não possui regras.
 * 
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @version 18/03/2013
 * @since   18/03/2013
 * @package Cron
 */
abstract class AtendimentoAutomaticoDAO extends CronDAO {
	 	
	const OID_MOTIVO_INICIAL 	= 1; //atcatmoid = 1 -> Atendimento inicial

	protected $usuoid;
	protected $depoid;
	protected $login;
	protected $ramal;
	protected $log = array();
	protected $param;
	/**
	 * @var AtendimentoAutomaticoContratoVO
	 */
	protected $contrato;
			
    public function __construct($conn) {
        parent::__construct($conn);
        
        $this->carregarInformacoesUsuario();

        $this->param = $this->getParametros();
        
		foreach ($this->param as $key => $value) {
			// se for um array do postgres
			if (substr($value, 0, 1) == '{') {
				$this->param->$key = $this->buildArray($value);
			}
			else if ($value == 'f') {
				$this->param->$key = false;
			}
			else if ($value == 't') {
				$this->param->$key = true;
			}
		}
    }
    
    private function carregarInformacoesUsuario() {
    	
		$sql = "
			SELECT 	cd_usuario, ds_login, usudepoid, nm_usuario, 0 AS ramal 
			FROM 	usuarios 
			WHERE 	nm_usuario ILIKE 'Ura%'  
		";
		
		$rs = $this->query($sql);
		
		if (pg_num_rows($rs) == 0) {
			throw new Exception('Usuário para Ura não identificado.');
		}
		
    	$rowUsuario = pg_fetch_object($rs);
    	
    	$this->usuoid 	= $rowUsuario->cd_usuario;
    	$this->depoid 	= $rowUsuario->usudepoid;
    	$this->login 	= $rowUsuario->ds_login;
    	$this->ramal 	= $rowUsuario->ramal;
    }

    /**
     * Retorna os parametros especificos do tipo de atendimento
     * @return stdClass
     */
	public abstract function getParametros();
	
	/**
	 * QUERY sql para buscar os contatos pendentes de cada processo
	 * @return string $sql QUERY sql para busca dos contatos pendentes
	 */
	protected abstract function buscarContatosPendentes();
	
	/**
	 * Realiza processo de descarte de contatos pendentes
	 * @param AtendimentoAutomaticoContratoVO $contrato
	 * @return boolean
	 */
	protected abstract function descartar(AtendimentoAutomaticoContratoVO $contrato);
	
	/**
	 * Realiza tratamentos necessários para cada processo
	 * @param AtendimentoAutomaticoContratoVO $contrato
	 * @return void
	 */
	protected abstract function tratar(AtendimentoAutomaticoContratoVO $contrato);
	
	/**
	 * Busca os telefones para contato com o cliente de cada processo
	 * @param AtendimentoAutomaticoContratoVO $contrato
	 * @return array:AtendimentoAutomaticoContatoVO
	 */
	protected abstract function buscarTelefones(AtendimentoAutomaticoContratoVO $contrato);
		
	/**
	 * Busca os contatos para envio
	 * @return array:AtendimentoAutomaticoContatoVO
	 */
	public function buscarContatos() {

		$rows = array();
		$descartados = array();
		
		$sql = $this->buscarContatosPendentes();
		
		$rs = $this->query($sql);
		
		while($row = pg_fetch_object($rs)) {
			
			$contrato = new AtendimentoAutomaticoContratoVO($row);
			
			if ($this->descartar($contrato)) {
				$descartados[] = $row->codigo;
				continue;
			}
			
			$this->tratar($contrato);
			
			$contatos = $this->buscarTelefones($contrato); // @TODO O que deve acontecer quando não achar telefones ?
			
			$rows[$contrato->codigo] = $contatos;
		}
		
		echo '<pre>';
		echo 'DESCARTADOS INI: <hr>';
		print_r($descartados);
		echo 'DESCARTADOS FIM: <hr>';
		
		return $rows;
	}
	
	/**
	 * Busca um contrato pelo número
	 * @param int $connumero
	 * @return AtendimentoAutomaticoContratoVO
	 */
	public function getContrato($connumero) {
				
		$sql = "
			SELECT 	connumero, conno_tipo, concsioid, conclioid 
			FROM 	contrato 
			WHERE 	connumero = ".$connumero." 
		";
		
		$rs = $this->query($sql);
		
    	$row = pg_fetch_object($rs);
    	
    	return new AtendimentoAutomaticoContratoVO($row);
	}
	
	/**
	 * Busca o departamento do usuário
	 * @param int $usuoid
	 * @return CronVO
	 */
	public function getDepartamento($usuoid) {
				
		$sql = "
			SELECT 		prhdepoid
			FROM 		usuarios
			INNER JOIN 	perfil_rh ON prhoid = usucargooid
			WHERE 		cd_usuario = ".$usuoid." 
		";
		
		$rs = $this->query($sql);
		
    	return $this->fetchObject($rs);
	}
	
	/**
	 * Insere histórico para o contrato
	 * @param int $connumero
	 * @param string $obs
	 * @return boolean
	 */
	protected function inserirHistoricoContrato($connumero, $obs) {
		
		$obs = pg_escape_string($obs);
		
		$sql = "SELECT historico_termo_i(".$connumero.", ".$this->usuoid.", '".$obs."') AS retorno;";
		
		$rs = $this->query($sql);
		
		$row = $this->fetchObject($rs);
		
		$retorno = isset($row->retorno) ? $row->retorno : 0;
		
		return ($retorno == 1);
	}
	
	protected function buscarDescricaoMotivoPorId($atmoid){
		
		$sql = "
			SELECT 	atmdescricao
			FROM 	atendimento_motivo
			WHERE 	atmoid = '".$atmoid."'					
		";			
	
		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$atmdescricao = isset($row->atmdescricao) ? $row->atmdescricao : '';
	
		return $atmdescricao;
	}
	
	/**
	 * Consultar o ID do Motivo do Atendimento
	 * @author André L. Zilz
	 * @since 04/04/2013
	 * @param string $atmdescricao
	 * @return int
	 */
	protected function buscarMotivoAtendimento($atmdescricao){
	
		$atmdescricao = pg_escape_string($atmdescricao);
		
		$sql = "
			SELECT 	atmoid
			FROM 	atendimento_motivo
			WHERE 	atmdescricao = '".$atmdescricao."'					
			";			
	
		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$atmoid = isset($row->atmoid) ? $row->atmoid : 0;
	
		return $atmoid;
	}
	
	/**
	 * Abre um atendimento
	 * @param int $clioid
	 * @param int $depoid
	 * @param int $atmoid
	 * @return int $atcoid
	 */
	protected function abrirAtendimento($clioid, $depoid, $atmoid) {
		
		$sql = "
			SELECT atendimento_cliente_i(
				  ".$this->usuoid." 								-- atcusuoid 
				, ".$clioid." 										-- atcclioid 
				, 0 												-- atcprotoid - não tem protocolo
				, '' 												-- atcprotocolo - não tem protocolo 
				, ".$depoid." 										-- atcdepoid 
				, ".$atmoid." 										-- atcatmoid 
		) AS atcoid;";
		
		$rs = $this->query($sql);
		
		$row = $this->fetchObject($rs);
		
		$atcoid = isset($row->atcoid) ? $row->atcoid : null;
		
		return $atcoid;
	}
	
	/**
	 * Insere um acesso para um atendimento
	 * @param AtendimentoAutomaticoContratoVO $contrato
	 * @param int $atcoid
	 * @param int $atmoid
	 * @param int $tipo_ligacao [0]Sem ligação, [1]Ligação Ativa, [2]Ligação Receptiva, [3]Retorno, [4]outros canais de comunicação(Nextel, Email)
	 */
	protected function inserirAcesso(AtendimentoAutomaticoContratoVO $contrato, $atcoid, $atmoid, $tipo_ligacao=0) {
		
		$usuoid 		= $this->usuoid;
		$clioid 		= $contrato->conclioid;
		$veioid 		= $contrato->conveioid;
		$equoid 		= $contrato->conequoid;
		$conoid 		= $contrato->connumero;
		
		$sql = "
			SELECT atendimento_acesso_i(
				  ".$atcoid." 
				, ".$usuoid."  
				, ".$atmoid."  
				, ".$clioid."  
				, ".$veioid." 
				, ".$equoid."  
				, ".$tipo_ligacao."  
				, ".$conoid."  
		) AS ataoid;";
		
		$rs = $this->query($sql);
		
		$row = $this->fetchObject($rs);
		
		$ataoid = isset($row->ataoid) ? $row->ataoid : null;
		
		return $ataoid;
	}
	
	/**
	 * Fecha um atendimento
	 * @param AtendimentoAutomaticoContratoVO $contrato
	 * @param int $atcoid
	 * @param int $atmoid
	 * @param int $tipo_ligacao [0]Sem ligação, [1]Ligação Ativa, [2]Ligação Receptiva, [3]Retorno, [4]outros canais de comunicação(Nextel, Email)
	 * @param boolean $isGeraCobranca
	 * @return void
	 */
	public function concluirAtendimento(AtendimentoAutomaticoContratoVO $contrato, $atcoid, $atmoid, $tipo_ligacao=0, $isGeraCobranca=false) {
		
		$usuoid 		= $this->usuoid;
		$veioid 		= $contrato->conveioid;
		$equoid 		= $contrato->conequoid;
		$clioid 		= $contrato->conclioid;
		$conoid 		= $contrato->connumero;
		$atmdescricao	= $this->buscarDescricaoMotivoPorId($atmoid);
		$geraCobranca 	= ($isGeraCobranca) ? 'TRUE' : 'FALSE';
		
		$sql = "
			SELECT atendimento_cliente_concluir(
				  ".$atcoid." 
				, ".$usuoid."  
				, ".$atmoid."  
				, ".$veioid." 
				, ".$equoid."  
				, ".$tipo_ligacao."  
				, ".$clioid."  
				, '".$atmdescricao."'  
				, ".$geraCobranca."  
				, ".$conoid."  
		) AS retorno;";
		
		$rs = $this->query($sql);
	}
	
	/**
	 * Envia os contatos para o discador
	 * @param array:AtendimentoAutomaticoContatoVO
	 * @return boolean
	 */
	public function enviarDiscador($contatos) {
		
		foreach ($contatos as $codigo => $contato) {
			
			$contrato = $this->getContrato($contato->connumero);
			
			$this->abrirAtendimento(
					$contrato->conclioid,
					$this->depoid,
					AtendimentoAutomaticoDAO::OID_MOTIVO_INICIAL,
					$codigo // somente para processo com sobrecarga especifica
			);
			
			$this->inserirDiscador($contato); //@TODO Verificar se todos os campso necessários estão disponíveis
		}
	}
	
	/**
	 * Insere o contato na base do discador
	 * @param AtendimentoAutomaticoContatoVO $contato
	 * @return boolean
	 */
	protected function inserirDiscador(AtendimentoAutomaticoContatoVO $contato) {
		return false;
	}
    
    /**
	 * Desconsiderar Pânicos por Status de Ocorrência 
     * @param int $connumero
     * @param array $paramStatusOcorrencia
     * @return string|boolean
     */
	protected function isDescartaStatusOcorrencia($connumero, $paramStatusOcorrencia) {
		
		if (!count($paramStatusOcorrencia)) {
			return false;
		}
		
		$sql = " 
			SELECT 	ocostatus 
			FROM 	ocorrencia 
			WHERE 	ococonnumero = ".$connumero." 
			AND 	ococancelado IS NULL 
			AND 	ococoncluido = FALSE 
			AND 	ocostatus IN (".$this->buildInSQL($paramStatusOcorrencia).") 
		";
		
		$rs = $this->query($sql);
		
		$row = $this->fetchObject($rs);
		
		$ocostatus = isset($row->ocostatus) ? $row->ocostatus : false;
		
		return $ocostatus;		
	}
	
	/**
	 * Desconsiderar clientes com pendência financeira maior que (N) dias
	 * @param AtendimentoAutomaticoContratoVO $contrato
	 * @param int $paramPendenciaFinanceira
	 * @return boolean
	 */
	protected function isDescartaPendenciaFinanceira(AtendimentoAutomaticoContratoVO $contrato, $paramPendenciaFinanceira) {
		
		if (empty($paramPendenciaFinanceira)) {
			return false;
		}
				
		$conclioid = $contrato->conclioid;
		$connumero = $contrato->connumero;
		$conveioid = $contrato->conveioid;
		
		$sql = "
			SELECT 		COUNT(1) as total 
			FROM 		contrato con
			INNER JOIN 	cliente_inadimplentes_sascar_sbtec_view civ ON civ.clioid = con.conclioid 
			WHERE 		con.conclioid= ".$conclioid. "
			AND			con.connumero = ".$connumero. " 
			AND 		civ.dias > ".$paramPendenciaFinanceira. " 
			AND 		con.conveioid = ".$conveioid. " 
		"; 
		
		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$total = isset($row->total) ? $row->total : 0;	
		
		return (boolean) $total;		
	}

	/**
	 * Desconsiderar clientes caso parâmetro bloqueio web marcado 
	 * @author André L. Zilz
	 * @since 05/04/2013
	 * @param int $connumero
	 * @param int $conclioid
	 * @return boolean
	 */
	protected function isDescartaBloqueioWeb($connumero, $conclioid) {
		
			
		if (empty($connumero) || (empty($conclioid))) {
			return false;
		}	
		
		$sql = "
			SELECT 		COUNT(1) as total
			FROM 		contrato con			
			INNER JOIN 	cliente_cobranca cob ON cob.clicclioid = con.conclioid
			WHERE 		con.conclioid= ".$conclioid. "
			AND			con.connumero = ".$connumero. "
			AND 		cob.clicvisualizacao_web = false			
			";
	
		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$total = isset($row->total) ? $row->total : 0;
	
		return (boolean) $total;
	}
	
	/**
	 * Desconsiderar pelo tipo de contrato
	 * @param int $connumero
	 * @param array $paramTiposContrato
	 * @return boolean
	 */
	protected function isDescartaTipoContrato($conno_tipo, $paramTiposContrato) {
		
		if ($conno_tipo == '' || empty($paramTiposContrato)) {
			return false;
		}
		
		return in_array($conno_tipo, $paramTiposContrato);
	}
	
	/**
	 * Desconsiderar contratos com Ordens de Serviço
	 * @param int $connumero
	 * @param array $paramTiposOS
	 * @param array $paramItensOS
	 * @param array $paramStatusOS
	 * @param array $paramDefeitosAlegados
	 * @return boolean
	 */
	protected function isDescartaOrdemServicoContrato($connumero, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados=array()) {
		
		if ((!count($paramTiposOS)) && (!count($paramItensOS)) && (!count($paramStatusOS))) {
			return false;
		}
		
		$listaOrdemServico = array();
		
		// Busca ordens de serviço do contrato
		$sql = "
			SELECT
					ordoid
			FROM
					ordem_servico
			WHERE
					ordconnumero = ".$connumero."
		";
		$rs = $this->query($sql);
		$listaOrdemServico = $this->fetchObjects($rs);
		
		// Verifica se alguma das ordens de serviço ocasionará o descarte
		foreach($listaOrdemServico as $ordemServico) {
			
			if ($this->isDescartaOrdemServico($ordemServico->ordoid, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Desconsiderar por Ordens de Serviço
	 * @param int $connumero
	 * @param array $paramTiposOS
	 * @param array $paramItensOS
	 * @param array $paramStatusOS
	 * @param array $paramDefeitosAlegados
	 * @return boolean
	 */
	protected function isDescartaOrdemServico($ordoid, $paramTiposOS, $paramItensOS, $paramStatusOS, $paramDefeitosAlegados=array()) {
		
		if ((!count($paramTiposOS)) && (!count($paramItensOS)) && (!count($paramStatusOS))) {
			return false;
		}
		
		//Verificar a quantidade de itens da ordem de serviço
		$sql = "
				SELECT 	COUNT(1) as qtd
				FROM 	ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item	ON otioid =  ositotioid
				INNER JOIN os_tipo ON otiostoid = ostoid
				LEFT JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
				LEFT JOIN os_tipo_defeito ON otdoid= osdfotdoid
				WHERE 	ordoid = ".$ordoid."
			";
			
		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$qtdItens = isset($row->qtd) ? $row->qtd : 0;
			
		//Verificar a quantidade de itens que devem ser descartados
		$sql = "
				SELECT 	COUNT(1) as qtd
				FROM 	ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item	ON otioid =  ositotioid
				INNER JOIN os_tipo ON otiostoid = ostoid
				LEFT JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado
				LEFT JOIN os_tipo_defeito ON otdoid= osdfotdoid
				WHERE 	ordoid = ".$ordoid. "
			";
			
		if (count($paramTiposOS)) {
			$sql .= " AND (ostoid IS NULL OR ostoid IN (".implode(',', $paramTiposOS).")) ";
		}
		if (count($paramItensOS)) {
			$sql .= " AND (otitipo IS NULL OR otitipo IN (".$this->buildInSQL($paramItensOS).")) ";
		}
		if (count($paramStatusOS)) {
			$sql .= " AND (ordstatus IS NULL OR ordstatus IN (".implode(',', $paramStatusOS)."))";
		}
		if (count($paramDefeitosAlegados)) {
			$sql .= " AND (otdoid IS NULL OR otdoid IN (".implode(',', $paramDefeitosAlegados)."))";
		}
			
		$rs = $this->query($sql);
		$row = $this->fetchObject($rs);
		$qtdItensDescartar = isset($row->qtd) ? $row->qtd : 0;
		
		$total = (int) ($qtdItens - $qtdItensDescartar);
		
		if ($total == 0 && $qtdItens > 0 && $qtdItensDescartar > 0) {
			return true;
		}
		
		return false;
	}
	
    /**
     * Transforma um array do Postgres em um array do PHP
     * @param string
     * @return mixed
     */
    public function buildArray($string) {
        
    	if (strlen($string)) {
            return explode(',', preg_replace('/\{|\}/', '', $string));
        }
        
        return array();
    }
	
    /**
     * Transforma um array do PHP em uma string para utilizar na clausula IN
     * @param array
     * @return string
     */
    public function buildInSQL($values) {
    	
    	foreach ($values as $i => &$value) {
    		$value = "'".$value."'";
    	}
    	 
    	return implode(',', $values);
    }

	public function tratarNumeroTelefone($telefone) {
		return preg_replace('/[^0-9]/', '', $telefone); // somente números
	}
    
    public function log($msg, $tipo='INFO') {
    	$this->log[] = date('d/m/Y H:i:s').' - '.$tipo.' - '.$msg;
    }
    
    public function showLog() {
    	foreach ($this->log as $value) {
    		echo '<hr>'.$value.'<hr>';
    	}
    }
}