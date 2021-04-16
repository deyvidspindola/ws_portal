<?php
/**
 * @file CadCicloEquipamentoCT.class.php
 * @author Rafael B. Silva - rafaelbarbetasilva@brq.com
 * @version 05/08/2013
 * @since 05/08/2013
 */
 
require_once(_MODULEDIR_."Cadastro/DAO/CadCicloEquipamentoCTDAO.class.php");

/**
 * Action do Cadastro de Emergencia
 */
class CadCicloEquipamentoCT {
	
	private $dao;
	private $oid;

	public function __construct() {		

		global $conn;
		$this->dao = new CadCicloEquipamentoCTDAO($conn);		
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
		
		include _MODULEDIR_.'Cadastro/View/cad_cicloequipamentoCT/index.php';
	}
	
	public function pesquisar() {
	
		$resultado['msgErro'] = false;
		
		// para manter os valores após a busca
		$dados = $this->populaValoresPost();
		
		$resultadoPesquisa = $this->dao->pesquisar($dados['ciceqpdescricao']);
		
		// View
		$resultado['ciceqpdescricao'] = $dados['ciceqpdescricao'];
		$resultado['pesquisa'] = $resultadoPesquisa;
		
		$this->index('pesquisar', $resultado);
	}

	public function novo($acao = 'index', $resultado = '') {
		
		$dados = $this->populaValoresPost();
		
		// View
		$resultado['ciceqpoid'] = $dados['ciceqpoid'];
		$resultado['ciceqpdescricao'] = $dados['ciceqpdescricao'];
		if($resultado['msgErro']){
			$resultado['msgErro'] = $this->mensagem(true, $resultado['msgErro'],$resultado['status']);		
		}else{
			$resultado['msgErro'] = $this->mensagem(false);
		}
		
		include _MODULEDIR_.'Cadastro/View/cad_cicloequipamentoCT/novo.php';
	}

	
	public function cadastrar() {
		
		$dados = $this->populaValoresPost();
		$resultado = $this->dao->novo($dados['ciceqpdescricao'], $this->oid);
		
		$status = $resultado['status'];
		$resultado['msgErro'] = $resultado['mensagem'];
		
		if($resultado['acao'] == 'index'){
			//$resultado['msgErro'] = $this->mensagem(true, $resultado['msgErro'], $status);
			$this->index('novo', $resultado, $status);
		}else{
			$this->novo('index', $resultado, $status);
		}
		
	}
	
	public function editar($acao = 'index', $resultado = '') {
		
		$dados = $this->populaValoresPost();
		$pesquisa = $this->dao->pesquisar(false, $dados['ciceqpoid']);
		
		// View
		$resultado['ciceqpoid'] = $dados['ciceqpoid'];
		$resultado['ciceqpdescricao'] = $pesquisa[0]['ciceqpdescricao'];

		$resultado['msgErro'] = ($resultado == '') ? $this->mensagem(false) : $resultado['msgErro'];

		include _MODULEDIR_.'Cadastro/View/cad_cicloequipamentoCT/editar.php';
	}

	public function salvar(){
		
		$dados = $this->populaValoresPost();
		
		$editar = $this->dao->editar($dados, $this->oid);
		
		$resultado['msgErro'] = 'Registro alterado com sucesso!';
		
		$this->index('pesquisa', $resultado);
	}

	public function excluir() {
		
		$dados = $this->populaValoresPost();

		$editar = $this->dao->excluir($dados['ciceqpoid'],$this->oid);

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