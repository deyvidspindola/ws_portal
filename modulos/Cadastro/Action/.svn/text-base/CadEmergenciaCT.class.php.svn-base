<?php
/**
 * @file CadEmergenciaCT.class.php
 * @author Rafael B. Silva - rafaelbarbetasilva@brq.com
 * @version 05/08/2013
 * @since 05/08/2013
 */

require_once(_MODULEDIR_."Cadastro/DAO/CadEmergenciaCTDAO.class.php");

/**
 * Action do Cadastro de Emergencia
 */
class CadEmergenciaCT {
	
	private $dao;
	private $oid;

	public function __construct() {		

		global $conn;
		$this->dao = new CadEmergenciaCTDAO($conn);		
		cabecalho();
		
		$this->oid = $_SESSION['usuario']['oid'];
	}	

	public function index($acao = 'index', $resultado = '', $status=''){
	
		if($resultado['msgErro']){
			$msgErro = $this->mensagem(true, $resultado['msgErro'], $status);
		}else{
			$msgErro = $this->mensagem(false);
		}
		
		$resultado['msgErro'] = $msgErro;
		
		include _MODULEDIR_.'Cadastro/View/cad_emergenciaCT/index.php';
	}
	
	public function pesquisar() {
	
		$resultado['msgErro'] = false;
		
		// para manter os valores após a busca
		$dados = $this->populaValoresPost();

		$resultadoPesquisa = $this->dao->pesquisar($dados['emeeqpdescricao']);

		// View
		$resultado['emeeqpdescricao'] = $dados['emeeqpdescricao'];
		$resultado['pesquisa'] = $resultadoPesquisa;
		
		$this->index('pesquisar', $resultado);
	}

	public function novo($acao = 'index', $resultado = '', $status = ''){

		if($resultado['msgErro']){
			$msgErro = $this->mensagem(true, $resultado['msgErro'], $status);
		}else{
			$msgErro = $this->mensagem(false);
		}
		
		$params = $this->populaValoresPost();

		// View
		$resultado['emeeqpoid'] = $params['emeeqpoid'];
		$resultado['emeeqpdescricao'] = $params['emeeqpdescricao'];
		$resultado['msgErro'] = $msgErro;
		
		include _MODULEDIR_.'Cadastro/View/cad_emergenciaCT/novo.php';
	}
	
	public function cadastrar() {

		$dados = $this->populaValoresPost();
		$cadastro = $this->dao->novo($dados['emeeqpdescricao'], $this->oid,'');

		$resultado['msgErro'] = $cadastro['mensagem'];
		
		if($cadastro['acao'] == 'index'){
			$this->index($cadastro['acao'], $resultado,$cadastro['status']);
		}else{
			$this->novo($cadastro['acao'], $resultado,$cadastro['status']);
		}
	}

	public function editar($acao = 'index', $resultado = '') {
		
		$params = $this->populaValoresPost();
		$pesquisa = $this->dao->pesquisar(false, $params['emeeqpoid']);
	
		// View
		$resultado['emeeqpoid'] = $params['emeeqpoid'];
		$resultado['emeeqpdescricao'] = $pesquisa[0]['emeeqpdescricao'];

		$resultado['msgErro'] = ($resultado == '') ? $this->mensagem(false) : $resultado['msgErro'];
		
		include _MODULEDIR_.'Cadastro/View/cad_emergenciaCT/editar.php';
	}
	
	public function salvar(){
		
		$dados = $this->populaValoresPost();
		
		$editar = $this->dao->editar($dados, $this->oid);
		
		$resultado['msgErro'] = 'Registro alterado com sucesso!';
		
		$this->index('pesquisa', $resultado);
	}
	
	public function excluir() {
		
		$dados = $this->populaValoresPost();
		
		$editar = $this->dao->excluir($dados['emeeqpoid'],$this->oid);
		
		
		$resultado['msgErro'] = 'Registro excluído com sucesso';
		$this->index('pesquisa', $resultado);
	}
	
	public function cancelar() {
	
		$resultado['msgErro'] = 'Operação Cancelada!';
		
		$this->index('pesquisa', $resultado, 'alerta');
	}
	
	public function mensagem($tipo, $msg='', $status=''){
	
		if(!$status) $status = "sucesso";
		if($tipo){		
			$msgErro = '<div id="mensagem" class="mensagem '.$status.'" style="display: block;">'.$msg.'</div>';			
		}else{
			$msgErro = '<div id="mensagem" class="mensagem " style="display: none;"></div>';
		}
		
		return $msgErro;
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

		return $data;		
	}

}