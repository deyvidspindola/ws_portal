<?php

require 'modulos/Financas/DAO/FinParametrosComissaoIndicadorDAO.class.php';

class FinParametrosComissaoIndicador
{
	private $dao;
	
	public function FinParametrosComissaoIndicador() {
		global $conn;
		
		$this->dao = new FinParametrosComissaoIndicadorDAO($conn);
	}
	
	public function index() {
		
	}
	
	public function pesquisar() {				
		echo json_encode($this->getParametrosClasse($_POST['pcieqcoid']));
		exit();
	}
	
	public function salvar() {
		$arr1 = $this->setParametrosClasse();
		$arr2 = $this->getParametrosClasse($_POST['pcieqcoid']);
		if (is_array($arr1) && is_array($arr2)) {
			$arr3 = array_merge($arr1,$arr2);
		}
		echo json_encode($arr3);		
		exit();
	}

	public function getClassesEquipamentos() {
		
		$arrClassesEquipamentos = $this->dao->getClassesEquipamentos();
		
		$html = "";
		foreach ($arrClassesEquipamentos AS $option) {
			$value = $option['value'];
			$text = $option['text'];
			$html .= "<option value=\"$value\">$text</option>\n";
		}
		
		echo $html;
	}
	
	public function getParametrosClasse($pcieqcoid) {

		if (!$pcieqcoid) {
			return false;
		}
		$arrParametrosClasse = $this->dao->getParametrosClasse($pcieqcoid);
		return $arrParametrosClasse;
	}
	
	public function setParametrosClasse() {
		
		$pcieqcoid 				= (!empty($_POST['pcieqcoid']) 				 )	? $_POST['pcieqcoid'] 				: 0;
		$pciitem_comissao 		= (!empty($_POST['pciitem_comissao'])		 )	? $_POST['pciitem_comissao']		: 'f';
		$pcivl_comissao 		= (!empty($_POST['pcivl_comissao'])			 )	? $_POST['pcivl_comissao'] 			: 0;
		$pcivl_perc_comissao 	= (!empty($_POST['pcivl_perc_comissao'])	 )	? $_POST['pcivl_perc_comissao'] 	: 0;
		$pcitipo_comissao 		= (!empty($_POST['pcitipo_comissao']) != ''	 ) ? $_POST['pcitipo_comissao'] 		: '';
		$pcivl_minimo_comissao 	= (!empty($_POST['pcivl_minimo_comissao']) 	 )	? $_POST['pcivl_minimo_comissao'] 	: 0;
		$pcivl_maximo_comissao 	= (!empty($_POST['pcivl_maximo_comissao']) 	 )	? $_POST['pcivl_maximo_comissao'] 	: 0;
		
		$pcivl_comissao = str_replace(".","",$pcivl_comissao);
		$pcivl_comissao = str_replace(",",".",$pcivl_comissao);
		$pcivl_perc_comissao = str_replace(".","",$pcivl_perc_comissao);
		$pcivl_perc_comissao = str_replace(",",".",$pcivl_perc_comissao);
		$pcivl_minimo_comissao = str_replace(".","",$pcivl_minimo_comissao);
		$pcivl_minimo_comissao = str_replace(",",".",$pcivl_minimo_comissao);
		$pcivl_maximo_comissao = str_replace(".","",$pcivl_maximo_comissao);
		$pcivl_maximo_comissao = str_replace(",",".",$pcivl_maximo_comissao);
		
		$setParametrosClasse = $this->dao->setParametrosClasse(
				$pcieqcoid,
				$pciitem_comissao,
				$pcivl_comissao,
				$pcivl_perc_comissao,
				$pcitipo_comissao,
				$pcivl_minimo_comissao,
				$pcivl_maximo_comissao
				);
		
		return $setParametrosClasse;
	}
}