<?php

/**
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 01/02/2013
 */
class CadPedidoAquisicao {
		
	/**
	 * Listar empresas para preenchimento da combobox Empresas
	 * @return array
	 */
	public function listarEmpresas($tela = null) 
	{		
		try	{
			
			require_once 'modulos/Cadastro/DAO/EmpresaSascarDAO.php';
			$empresaSascarDAO = new EmpresaSascarDAO();
			
			$default = array(array('tecoid' => '', 'tecrazao' => 'Escolha'));
			
			/*
			 * verifica se a tela for nova cadastro e usuário possui permissão para vesualizar todas as empresas
			 * caso não possua permissão lista somente a empresa que o funcionário esta cadastrado.
			 */
			if ($_SESSION['funcao']['pedido_todas_empresas'] != '1') {
				$empresas = $empresaSascarDAO->obterPorId($_SESSION['usuario']['tecoid']);
				if($tela == 'novo_cadastro') $empresas = array_merge($default, $empresas); 
			}
			else {									
				$empresas = $empresaSascarDAO->listarEmPares();
				$empresas = array_merge($default, $empresas);
			}
			
			if(!is_array($empresas)) throw new Exception('tipo da variável inválido');
			
			return $empresas;
			
		} catch (Exception $e) {
			return array();	
		}
	}	
}