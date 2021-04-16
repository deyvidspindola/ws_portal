<?php

/**
 * description: Gerencia o apontamento para a view da tela
 * 
 * @author alexandre.reczcki
 *
 */
class TIResetSenhaView {
	
	public function pesquisarForm($resultado = null) {
		require _MODULEDIR_ . 'TI/View/ti_reset_senha/pesquisa_form.php';
	}
	
	public function pesquisaResult($resultado = array()) {
		require _MODULEDIR_ . 'TI/View/ti_reset_senha/pesquisa_result.php';
	}
	
	public function mostrarUsuario($usuario = array()) {
		require _MODULEDIR_ . 'TI/View/ti_reset_senha/usuario_form.php';
	}
		
}