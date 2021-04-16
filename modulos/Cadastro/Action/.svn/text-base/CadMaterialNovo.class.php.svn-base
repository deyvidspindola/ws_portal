<?php

require_once(_MODULEDIR_."Cadastro/DAO/CadMaterialNovoDAO.class.php");

class CadMaterialNovo {
	
	private $dao;

	public function __construct() {        
		$this->dao = new CadMaterialNovoDAO();
	}

	public function consultaGrupoSimilar(){
		return $this->dao->consultaGrupoSimilar();
	}

	public function salvarGrupoSimilar($params){
		return $this->dao->salvarGrupoSimilar($params);
	}

	public function excluirGrupoSimilar($grsoid){
		return $this->dao->excluirGrupoSimilar($grsoid);
	}
}