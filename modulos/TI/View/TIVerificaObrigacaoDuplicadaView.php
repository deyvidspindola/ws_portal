<?php

/**
 * description: Gerencia o apontamento para a view da tela
 * 
 * @author denilson.sousa
 *
 */
class TIVerificaObrigacaoDuplicadaView {
	
	public function pesquisarForm($resultado = null) {
		require _MODULEDIR_ . 'TI/View/ti_verifica_obrigacao_duplicada/pesquisa_form.php';
	}
	
	public function pesquisaResult($resultado = array()) {
		require _MODULEDIR_ . 'TI/View/ti_verifica_obrigacao_duplicada/pesquisa_result.php';
	}
	
	public function mostrarCorrecao($resultado = array()) {
		require _MODULEDIR_ . 'TI/View/ti_verifica_obrigacao_duplicada/correcao_result.php';
	}
		
}
