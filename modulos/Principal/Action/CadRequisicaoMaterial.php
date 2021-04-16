<?php

/**
 * Camada Action - CadRequisicaoMaterial
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 16/01/2013
 */
class CadRequisicaoMaterial {
		
	/**
	 * Listar empresas para preenchimento da combobox Empresas
	 * @return array
	 */
	public function listarEmpresas($tela = null) 
	{
		//$_SESSION['funcao']['rms_todas_empresas'] = 1;
		
		try	{
			
			require_once 'modulos/Cadastro/DAO/EmpresaSascarDAO.php';
			$empresaSascarDAO = new EmpresaSascarDAO();
			
			$default = array(array('tecoid' => '', 'tecrazao' => '--Escolha--'));
			
			/*
			 * verifica se a tela for nova cadastro e usuário possui permissão para vesualizar todas as empresas
			 * caso não possua permissão lista somente a empresa que o funcionário esta cadastrado.
			 */
			if ($_SESSION['funcao']['rms_todas_empresas'] != '1') {
				$empresas = $empresaSascarDAO->obterPorId($_SESSION['usuario']['tecoid']);
				if($tela == 'novo_cadastro') $empresas = array_merge($default, $empresas); 
			}
			else {									
				$empresas = $empresaSascarDAO->listarEmPares();
				if($tela == 'novo_cadastro') $empresas = array_merge($default, $empresas);
			}
			
			if(!is_array($empresas)) throw new Exception('tipo da variável inválido');
			
			return $empresas;
			
		} catch (Exception $e) {
			return array();	
		}
	}	

	/**
	 * Listar departamentos para preenchimento da combobox Departamentos
	 * @return array
	 */
	public function listarDepartamentos($tecoid = null)
	{
		try	{
			
			if (empty($tecoid)){
				$tecoid = isset($_POST['empresa_requisicao_id']) ? $_POST['empresa_requisicao_id'] : NULL;
			}
			
			if($tecoid == null) throw new Exception('Não foi informado a empresa');
			
			require_once 'modulos/RH/DAO/DepartamentoDAO.php';
			$departamentoDAO = new DepartamentoDAO();
			
			$departamentos = $departamentoDAO->listarEmPares($tecoid);
			if(!is_array($departamentos)) throw new Exception('tipo da variável inválido');
				
			return $departamentos;
				
		} catch (Exception $e) {
			return array();
		}
	}
	
	/**
	 * Listar departamentos para preenchimento da combobox Departamentos no formarto json
	 * @return string <json>
	 */
	public function listarDepartamentos_ajax() {
		$retorno = $this->listarDepartamentos();
		foreach ($retorno as &$array) {
			foreach ($array as &$subarray) {
				$subarray = utf8_encode($subarray);
			}
		}
		echo json_encode($retorno);
		exit;
	}
	
	/**
	 * Listar centros de custos para preenchimento da combobox Centros de Custos
	 * @return array
	 */
	public function listarCentrosCustos($tecoid = null) {
	    global $conn;
	    try {
	    	if (empty($tecoid)){
				$tecoid = isset($_POST['empresa_requisicao_id']) ? $_POST['empresa_requisicao_id'] : null;
			}
			
			require_once 'includes/classes/CentroCusto.class.php';
			$centros = CentroCusto::geraComboEmpresa($conn, null, 'max', null, null, $tecoid);
			
			return $centros;
			
		} catch (Exception $e) {
			return array();
		}
	}
	
	/**
	 * Listar centros de custos para preenchimento da combobox Centros de Custos no formato json
	 * @return string <json>
	 */
	public function listarCentrosCustos_ajax() {
	    $str_list_CC_sel = '';
	    $str_list_CC = '<option value="">--Escolha --</option>';
	    $retorno = $this->listarCentrosCustos();
	    if (is_array($retorno)) {
            foreach ($retorno as $id => $desc) {
                $str_list_CC .= '<option value="' . $id . '">' . $desc . '</option>';
            }
     	}
	    echo utf8_encode($str_list_CC);
		exit;
	}
	
	/**
	 * Listar aprovadores para preenchimento da combobox Solicitar autorização para
	 * @return array
	 */
	public function listarAprovadores($cntoid = null)
	{
		try	{
				
			if (empty($cntoid)){
				$cntoid = isset($_POST['centro_custo_requisicao_id']) ? $_POST['centro_custo_requisicao_id'] : NULL;
			}
				
			if($cntoid == null) throw new Exception('Não foi informado o centro de custo');
				
			require_once 'modulos/Principal/DAO/RMSAprovadorDAO.php';
			$rmsAprovadorDAO = new RMSAprovadorDAO();
			
			$aprovadores = $rmsAprovadorDAO->listarAprovadoresPorCentroCusto($cntoid);
			
			if(!is_array($aprovadores)) throw new Exception('tipo da variável inválido');
	
			return $aprovadores;
	
		} catch (Exception $e) {
			return array();
		}
	}
	
	/**
	 * Listar aprovadores para preenchimento da combobox Solicitar autorização para no formarto json
	 * @return string <json>
	 */
	public function listarAprovadores_ajax() {
		$retorno = $this->listarAprovadores();
	
		foreach ($retorno as &$array) {
			foreach ($array as &$subarray) {
				$subarray = utf8_encode($subarray);
			}
		}
	
		echo json_encode($retorno);
		exit;
	}	
}