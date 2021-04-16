<?php

require 'modulos/Cadastro/DAO/CadMotivosMateriaisDAO.php';

class CadMotivosMateriais {

    private $dao;
    private $motivo;
    private $produto;
    private $material;
    private $relacao;
    public $msg;

    public function CadMotivosMateriais() {
        global $conn;

        $this->dao = new CadMotivosMateriaisDAO($conn);
        $this->tipo = (!empty($_POST['tipo'])) ? $_POST['tipo'] : 0;
        $this->motivo = (!empty($_POST['motivo'])) ? $_POST['motivo'] : 0;
        $this->produto = (!empty($_POST['produto'])) ? $_POST['produto'] : 0;
        $this->material = (!empty($_POST['material'])) ? $_POST['material'] : 0;
        $this->relacao = (!empty($_POST['relacao'])) ? $_POST['relacao'] : 0;
        $this->essencial = (!empty($_POST['essencial'])) ? $_POST['essencial'] : 0;
        $this->material_busca   = (!empty($_POST['material_busca']))    ? $_POST['material_busca']  : 0;
    }

    public function index() {
        
    }
    
    public function buscarMotivos() {
        $motivos = $this->getMotivos($this->tipo);
        echo json_encode($motivos);
        exit();
    }
        
    public function pesquisar() {
    	
    	// Verificar foi selecionado algum produto no formulário de pesquisa
    	if (!empty($this->produto)){
    		// Buscar os materiais referentes aos motivos e produtos
    		$relatorio = $this->getMateriaisMotivoProduto($this->motivo, $this->produto); 
    	}else{
    		// Buscar os materiais referentes ao motivo
    		$relatorio = $this->getMateriaisMotivo($this->motivo);
    	}    
    	    	
    	//$relatorio = $this->getMateriaisMotivo($this->motivo);
        echo json_encode($relatorio);
        exit();
    }

    public function adicionar() {
        echo json_encode($this->setMaterial($this->motivo, $this->produto ,$this->material, $this->essencial));
        exit();
    }

    public function excluir() {
        echo json_encode($this->delMaterial($this->motivo, $this->produto, $this->relacao));
        exit();
    }

    public function getMotivos($tipo=null) {
        $retorno = "";
        $motivos = $this->dao->getMotivos($tipo);

        foreach ($motivos AS $key => $value) {
            $retorno .= "<option value=\"$key\">$value</option>\n";
        }

        echo $retorno;
    }
    
    public function getTipos() {
        $retorno = "";
        $tipos = $this->dao->getTipos();

        foreach ($tipos AS $key => $value) {
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

        return $retorno;
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
    /**
     * Função: Buscar na base de dados os produtos
     */
    public function getProdutos() {
    
    	$retorno = "";
        $produtos = $this->dao->getProdutos();
        
        echo json_encode($produtos);
        exit();
    }
    
    private function getMateriaisMotivo($motivo) {
        $arrRetorno = array();

        try {
            if (!$motivo) {
                $arrRetorno['retorno'] = array(
                    "error" => 1,
                    "msg" => utf8_encode("Falha ao pesquisar materiais.")
                );
            }
            $arrRetorno['retorno'] = array(
                "error" => 0,
                "msg" => ""
            );
            $arrRetorno['relatorio'] = $this->dao->getMateriaisMotivo($motivo);
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => $e->getMessage()
            );
        }

        return $arrRetorno;
    }

    private function getMateriaisMotivoProduto($motivo, $produto) {
    	$arrRetorno = array();
    	
    	try {
    		if (!$motivo || !$produto) {
    			$arrRetorno['retorno'] = array(
    					"error" => 1,
    					"msg" => utf8_encode("Falha ao pesquisar sdfdfgddf.")
    			);
    		}
    	
    		$arrRetorno['retorno'] = array(
    				"error" => 0,
    				"msg" => ""
    		);
    		$arrRetorno['relatorio'] = $this->dao->getMateriaisMotivoProduto($motivo, $produto);
    	} catch (Exception $e) {
    		$arrRetorno['retorno'] = array(
    				"error" => $e->getCode(),
    				"msg" => $e->getMessage()
    		);
    	}
    
    	return $arrRetorno;
    }
    
    private function setMaterial($motivo, $produto, $material, $essencial) {
        $arrRetorno = array();

        try {
            $essencial = $essencial ? 'TRUE' : 'FALSE';

        	// Se foi selecionado um produto -> Deve inserir o Material relacionado ao Motivo/Produto
        	if (!empty($produto)){
        		$this->dao->setMaterialMotivoProduto($motivo, $produto, $material, $essencial);
        	}else{
        		$this->dao->setMaterial($motivo, $material, $essencial);
        	}
            
            $arrRetorno['retorno'] = array(
                "error" => 0,
                "msg" => utf8_encode("Material cadastrado com sucesso.")
            );
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        }
        return $arrRetorno;
    }

    private function delMaterial($motivo, $produto, $relacao) {

        $arrRetorno = array();

        try {
        	// Se foi selecionado algum produto - Deve exluir o material realcionado ao Motivo/Produto
        	if (!empty($produto)){
        		$this->dao->delMaterialMotivoProduto($motivo, $produto, $relacao);
        	}else{
        		$this->dao->delMaterial($motivo, $relacao);
        	}

            $arrRetorno['retorno'] = array(
                "error" => 0,
                "msg" => utf8_encode("Material excluído com sucesso.")
            );
        } catch (Exception $e) {
            $arrRetorno['retorno'] = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        }
        return $arrRetorno;
    }

}

?>