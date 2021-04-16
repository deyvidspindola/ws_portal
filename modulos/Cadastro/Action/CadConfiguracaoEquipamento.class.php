<?php
/**
 * @file CadConfiguracaoEquipamento.class.php
 * @author Leandro Alves Ivanaga - leandroivanaga@brq.com
 * @version 07/08/2013
 * @since 07/08/2013
 * @package SASCAR CadConfiguracaoEquipamento.class.php  
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadConfiguracaoEquipamentoDAO.class.php");
require_once(_MODULEDIR_."Cadastro/DAO/CadValidadesEquipamentosDAO.class.php");
require_once(_MODULEDIR_."Cadastro/DAO/CadIntervPosicionamentoEquipamentosDAO.class.php");
require_once(_MODULEDIR_."Cadastro/DAO/CadEmergenciaCTDAO.class.php");
require_once(_MODULEDIR_."Cadastro/DAO/CadCicloEquipamentoCTDAO.class.php");

/**
 * Action do Cadastro de Configuração de Equipamento
 */
class CadConfiguracaoEquipamento {
	
	private $dao;
	private $dao_validade;
	private $dao_intervado;
	private $dao_emergencia;
	private $dao_ciclo;
	
	private $combos;
	
	public function __construct() {		

		global $conn;
		$this->dao = new CadConfiguracaoEquipamentoDAO($conn);		
	}
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }	

	public function index($acao = 'index', $resultadoPesquisa = array(), $mensagem = '') {			
		// exibe mensagem quando existir
		if($mensagem != '')
			$this->retorno = $mensagem;
		
		// Se cadastro ocorreu com sucesso, limpa o Post para não preencher automaticamente as combos
		if ($mensagem['status'] == "sucesso") {
			unset($_POST);
			$resultadoPesquisa = array();
		}
						
		// passa variaveis para a view
		$this->acao = $acao;
		$this->resultadoPesquisa = $resultadoPesquisa;
		
		$this->preencheCombos();
		
		// chama a view
		$this->view($acao);
	}
	
	public function pesquisarEquipamento() {	

		if($mensagem != '')
			$this->retorno = $mensagem;
				
		// Pega valores do Post
		$params = $this->populaValoresPost();
		
		$equipamentos = $this->dao->getEquipamentos($params);		
		
		// Retorna Json com os equipamentos encontrados
		echo json_encode($equipamentos);
		exit;
		
	}

	public function preencheCombos() {
		// DAO's auxiliares para preenchimento das combos
		$this->dao_validade = 		new CadValidadesEquipamentosDAO($conn);
		$this->dao_intervalo = 		new CadIntervPosicionamentoEquipamentosDAO($conn);
		$this->dao_emergencia = 	new CadEmergenciaCTDAO($conn);
		$this->dao_ciclo = 			new CadCicloEquipamentoCTDAO($conn);
		
		// Busca valores das combos
		$this->combos['validade'] = 	        $this->dao_validade->pesquisa();
		$this->combos['intervalo'] = 	        $this->dao_intervalo->pesquisa();
		$this->combos['emergencia'] = 	        $this->dao_emergencia->pesquisar();
		$this->combos['ciclo'] = 		        $this->dao_ciclo->pesquisar();
		$this->combos['obrigacaofinanceira'] = 	$this->dao->getObrigacaoFinanceira();
		$this->combos['valor_faturamento'] = 	$this->dao->getObrigacaoFinanceira(1);	//combo Valor de Faturamento
	}

	/**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @return array $retorno
     */
    public function buscarClienteNome() {

    	$retorno = '';
    
        $params = $this->populaValoresPost();
        
        $params['tipo'] = trim($params['filtro']) != '' ? trim($params['filtro']) : '';
        $params['nome'] = trim($params['nome']) != '' ? utf8_decode(trim($params['nome'])) : '';
    
        if(!empty($params['tipo']) || !empty($params['nome'])) {        
        	$retorno = $this->dao->buscarClienteNome($params);

        	foreach($retorno as $key => $valor) {
        		
        		if($valor->tipo == 'J') {
        			$valor->doc = $this->aplicarMascara('##.###.###/####-##',$valor->doc);
        		} else {
        			$valor->doc = $this->aplicarMascara('###.###.###-##',$valor->doc);
        		}     

        		$valor->clinome = utf8_encode($valor->clinome);
        	}
        }
    
        echo json_encode($retorno);
        exit();
    } 


    public function editar($acao = 'editar', $mensagem = '') {

		$this->tituloTela = 'Editar';
		$this->acaoTela   = 'atualizar';
		$this->clientes = array();
		$this->vendaRestrita = false;
		$this->listaClientes = '';
		
		if($mensagem != '')
			$this->retorno = $mensagem;
		
		// Pega valores do Post
		$params = $this->populaValoresPost();
	
		// Busca os dados da configuração
		if($params['acao'] == 'editar') {
			$result = $this->dao->getConfiguracaoEquipamento($params['ceqpoid']);
			$this->taxas = $this->dao->getConfiguracaoEquipamentoTaxa($params['ceqpoid']);
			$this->clientes = $this->dao->buscarConfiguracaoEquipamentoCliente($params['ceqpoid']);

			//Formata CNPJ ou CPF
			if(sizeof($this->clientes) > 0){

				$this->vendaRestrita = true;

				foreach($this->clientes as $key => $valor) {
        		
	        		if($valor->tipo == 'J') {
	        			$valor->doc = $this->aplicarMascara('##.###.###/####-##',$valor->doc);
	        		} else {
	        			$valor->doc = $this->aplicarMascara('###.###.###-##',$valor->doc);
	        		} 

	        		$this->listaClientes .= ',' .  $valor->clioid;  
        		}
			}

			$_SESSION['total_conf_equ_taxa'] = sizeof($this->taxas);
		}
		else{
			$result = $params;
		}
		
		// limpa campos e seta os novos valores do form
		$this->populaValoresPost(true);
		$this->populaValoresPost(false, $result);
		
		// Chama a função para buscar as opções das combos
		$this->preencheCombos();
		
		// chama a view
		$this->view('form');		
	}

	public function view($action, $layoutCompleto = true) {	
		
		if($layoutCompleto)
			include _MODULEDIR_.'Cadastro/View/cad_configuracao_equipamento/header.php';
		
		include _MODULEDIR_.'Cadastro/View/cad_configuracao_equipamento/'.$action.'.php';
		
		if($layoutCompleto)
			include _MODULEDIR_.'Cadastro/View/cad_configuracao_equipamento/footer.php';
	}

	public function atualizar($acao, $mensagem = '') {
			
		$params    = $this->populaValoresPost();

		$resultado = $this->dao->atualizaDados($params);
		$this->populaValoresPost(true);	

		if ($resultado['acao'] == 'index') {
			$this->index('index', '', $resultado);
		} else {
			$this->editar('editar', $resultado);
		}
	}

	public function populaValoresPost($clearPost = false, $params = null) {	
		if(!is_null($params)):
			$data = $params;
		else:
			$data = $_POST;
		endif;
		
		foreach($data as $key => $value):
			if($clearPost === false) {
				// TODO: alterar strtoupper para mb_strtoupper assim que estiver habilitado o mb_string
				$this->$key = (is_string($value))?strtoupper($value):$value;				
			} else
				unset($this->$key);
		endforeach;		
		//echo '<pre>';print_r($data);echo '</pre>';
		
		return $data;		
	}

	public function pesquisar() {
		// para manter os valores após a busca
		$params = $this->populaValoresPost();
		
		$resultadoPesquisa = $this->dao->pesquisa($params);	
		$this->index('pesquisar', $resultadoPesquisa);
	}	

    
    function buscarClienteTipoEquipamento($param) {

        $params = $this->populaValoresPost();
		
		$resultadoPesquisa = $this->dao->buscarClienteTipoEquipamento($params);	

		echo json_encode($resultadoPesquisa);
		exit();
    } 

	/**
     * mensagem de total de registros apresentados em grid
     */
    public function getMensagemTotalRegistros($total){
    	$arrMsg = array("0"=>"Nenhum registro encontrado", "1"=>"registro encontrado", "2"=>"registros encontrados");
    	if($total == 0){
    		return $arrMsg['0'];
    	}elseif($total == 1){
    		return $total." ".$arrMsg['1'];
    	}elseif($total > 1){
			return $total." ".$arrMsg['2'];
    	}
    }	

    public function excluir($acao, $mensagem = '') {
		$resultado = array();
		
		$this->populaValoresPost();
		$resultado = $this->dao->excluirDados($this->ceqpoid);
				
		$this->populaValoresPost(true);
		$this->index('index', '', $resultado);		
	}

    /**
    * Aplica mascara em um numero
    */
    function aplicarMascara($mascara,$codigo) {

        $codigo = str_replace(" ","",$codigo);


        for ($i=strlen($codigo);$i>0;$i--) {

            $mascara[strrpos($mascara,"#")] = $codigo[$i-1];

        }

        $mascara = str_replace("#", "0", $mascara);

        return $mascara;
    }

    public function adicionarTaxa(){
		
		$params = $this->populaValoresPost();
		
		if(!$_SESSION['conf_equ_taxa'][$_POST['ceqpobroid_taxa']] && $this->dao->existeTaxa($params) == false){
			
			if(isset($_SESSION['total_conf_equ_taxa']) && !isset($_SESSION['conf_equ_taxa'])){
				$x = $_SESSION['total_conf_equ_taxa']+1;
			}elseif(!isset($_SESSION['conf_equ_taxa'])){
				$x = 1;
			}else{
				$x = sizeof($_SESSION['conf_equ_taxa']) + 1;
			}
			
			$_SESSION['conf_equ_taxa'][$_POST['ceqpobroid_taxa']] = $_POST['ceqpincidencia_taxa'];
			
			
			$array['total'] = $x;
			$_SESSION['total_conf_equ_taxa'] = $x;
			
			$array['status'] = 'sucesso';	
		}else{
			$array['status'] = 'existe';	
		}
		
		echo json_encode($array);
		exit();
	}


	public function cancelar($acao) {
		// se cancelar, volta para a listagem
		if ($acao == 'cancelar') {
			unset($_POST);
			$this->index('cancelar', array(), array('status' => 'alerta', 'mensagem' => 'Operação Cancelada'));
		}
	}
	
	public function novo($acao, $mensagem = '') {		
		if($mensagem != '') {
			$this->retorno = $mensagem;
		} else {
			// Limpa possivel parametros que foram usados na pesquisa
			unset($_POST);
		}

		$this->tituloTela = 'Novo';
		$this->acaoTela   = 'salvar';

		// Chama a função para buscar as opções das combos
		$this->preencheCombos();

		// chama a view
		$this->view('form');
	}
	
	public function salvar($acao) {

		// para manter os valores após a tentativa errada de cadastro
		$params = $this->populaValoresPost();

		$resultado = $this->dao->inserirDados($params);		

		if ($resultado['acao'] == 'index') {
			// limpa os campos para não aparecer os valores na pesquisa
			$this->populaValoresPost(true);
			$this->index('index', '', $resultado);
		} else {
			$this->novo('novo', $resultado);
		}
	}
	
	public function deletarTaxa(){
		$_SESSION['total_conf_equ_taxa'] = $_SESSION['total_conf_equ_taxa'] - 1;

		$params = $this->populaValoresPost();
		$retorno = $this->dao->excluirTaxa($params['ceqptxoid']);

		echo json_encode($retorno);
		exit();
	}
	
	public function removeSessionTaxa(){
	
		unset($_SESSION['conf_equ_taxa'][$_POST['ceqpobroid_taxa']]);
		$_SESSION['total_conf_equ_taxa'] = $_SESSION['total_conf_equ_taxa'] - 1;

		if(!$_SESSION['conf_equ_taxa'][$_POST['ceqpobroid_taxa']]){
			$array['status'] = 'sucesso';
		}else{
			$array['status'] = 'existe';
		}

		echo json_encode($array);
		exit();
	}
   
}