<?php 

require 'modulos/Cadastro/DAO/CadEquipamentosMateriaisDAO.php';

class CadEquipamentosMateriais {
	
	private $dao;
	private $equipamento;
	private $material;
	private $classe;
	private $relacao;
	
	public $msg;
	
	public function CadEquipamentosMateriais() {
		global $conn;
		
		$this->dao = new CadEquipamentosMateriaisDAO($conn);
		$this->equipamento 	= (!empty($_POST['equipamento'])) 	? $_POST['equipamento'] 	: 0;
		$this->material 	= (!empty($_POST['material'])) 		? $_POST['material'] 		: 0;
		$this->classe 		= (!empty($_POST['classe'])) 		? $_POST['classe'] 			: 'NULL';
		$this->relacao 		= (!empty($_POST['relacao'])) 		? $_POST['relacao'] 		: 0;
		$this->material_busca 	= (!empty($_POST['material_busca'])) 	? $_POST['material_busca'] 	: 0;
	}
	
	public function index() {
		
	}
	
	public function pesquisar() {
		$relatorio = $this->getMateriaisEquipamento($this->equipamento);		

        $filtros['equipamento'] = $this->equipamento;
        $filtros['classe'] = $this->classe;

        $relatorio = $this->getMateriaisEquipamento($filtros);

		echo json_encode($relatorio);
		exit();
	}
	
	public function adicionar() {
		if(!$this->classe){  // Evitar Classe NULL, esse campo só pode ser NULL para buscas
			echo json_encode(
				array('retorno' => array( "error"	=> 1,
					"msg" => utf8_encode("Classe inválida.")
				))
			);
			exit();
		}
		echo json_encode($this->setMaterial($this->equipamento,$this->material,$this->classe));
		exit();
	}
	
	public function excluir() {
		echo json_encode($this->delMaterial($this->equipamento,$this->relacao));
		exit();
	}
	
	public function getEquipamentos() {
		$retorno = "";
		$equipamentos = $this->dao->getEquipamentos();		
			
		foreach ($equipamentos AS $key => $value) {
			$retorno .= "<option value=\"$key\">$value</option>\n"; 
		}
		
		echo $retorno;
	}
	
	public function getClasses() {
		$retorno = "";
		$classes = $this->dao->getClasses();
			
		foreach ($classes AS $key => $value) {
			$retorno .= "<option value=\"$key\">$value</option>\n";
		}
	
		echo $retorno;
	}
	
	public function getMateriais() {
	
		$retorno = "";
		$materiais = $this->dao->getMateriais();
		
		foreach ($materiais AS $key => $value) {
			$retorno .= "<option value=\"$key\">$value</option>\n";
		}
		
		echo $retorno;
	}

	public function pesquisarMateriais() {
		$filtros = array();
		if(!empty($this->material_busca)) {
			$filtros['material'] = $this->material_busca;
		}

		$materiais = $this->dao->pesquisarMateriais($filtros);
		if(count($materiais) > 0) {
			echo json_encode($materiais, true);
			exit();
		} else {
			echo "";
			exit();
		}
	}
	
	private function getMateriaisEquipamento($equipamento) {
		$arrRetorno = array();
		
		try {

			if (!$equipamento) {
				$arrRetorno['retorno'] = array(
					"error"	=> 1,
					"msg" 	=> utf8_encode("Falha ao pesquisar materiais.")
					);
			}			
			$arrRetorno['retorno'] = array(
					"error"	=> 0,
					"msg" 	=> ""
					);

			if(isset($equipamento['classe']) && $equipamento['classe'] == null && $equipamento['equipamento'] == 0) {
                    $arrRetorno['retorno'] = array(
                    "error" => 0,
                    "msg"   => ""
            );

            }else{
			$arrRetorno['relatorio'] = $this->dao->getMateriaisEquipamento($equipamento);			
            }

		} catch (Exception $e) {
			$arrRetorno['retorno'] = array(
					"error"	=> $e->getCode(),
					"msg" 	=> $e->getMessage()
					);
		}
		
		return $arrRetorno;
	}
	
	private function setMaterial($equipamento,$material,$classe) {
		
		$arrRetorno = array();
		
		try {
			$this->dao->setMaterial($equipamento,$material,$classe);
			
			$arrRetorno['retorno'] = array(
				"error"	=> 0,
				"msg" 	=> utf8_encode("Material cadastrado com sucesso.")
				);
			//$arrRetorno['relatorio'] = $this->getMateriaisEquipamento($equipamento);
			
		} catch (Exception $e) {
			$arrRetorno['retorno'] = array(
				"error"	=> $e->getCode(),
				"msg" 	=> utf8_encode($e->getMessage())
				);		
		}
		return $arrRetorno;
	}
	
	private function delMaterial($equipamento,$relacao) {
	
		$arrRetorno = array();
	
		try {
			$this->dao->delMaterial($equipamento,$relacao);
				
			$arrRetorno['retorno'] = array(
				"error"	=> 0,
				"msg" 	=> utf8_encode("Material excluído com sucesso.")
				);
			//$arrRetorno['relatorio'] = $this->getMateriaisEquipamento($equipamento);
				
		} catch (Exception $e) {
			$arrRetorno['retorno'] = array(
					"error"	=> $e->getCode(),
					"msg" 	=> utf8_encode($e->getMessage())
			);
		}
		return $arrRetorno;
	}
}
?>