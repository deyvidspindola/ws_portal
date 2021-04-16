<?php
require_once 'modulos/Cadastro/DAO/EmpresaSascarDAO.php';
require_once 'modulos/RH/DAO/DepartamentoDAO.php';

/**
 * Camada Action - DocPerfilRH
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 26/12/2012
 */
class MigrarCargo {
	
	private $empresaSascarDAO;
	private $departamentoDAO;
	
	public function __construct() 
	{
		$this->empresaSascarDAO = new EmpresaSascarDAO();
		$this->departamentoDAO = new DepartamentoDAO();
	}
	
	/**
	 * Listar empresas para preenchimento da combobox Empresas
	 * @return array
	 */
	public function listarEmpresas() 
	{
		try	{
			
			$empresas = $this->empresaSascarDAO->listarEmpresasEmPares();
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
				$tecoid = isset($_POST['tecoid']) ? $_POST['tecoid'] : NULL;
			}
			
			if($tecoid == null) throw new Exception('Não foi informado a empresa');
			
			$departamentos = $this->departamentoDAO->listarDepartamentosPorEmpresaComPermissaoEmPares($tecoid);
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
}