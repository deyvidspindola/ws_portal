<?php
/**
 * description: Gerenciamento das views
 * 
 * @author leandro.rodrigues
 *
 */
class MANInsereTextoContratoView {
	
	public function receberContratosView($resultado = null) {
		require _MODULEDIR_ . 'Manutencao/View/man_insere_texto_contrato/receber_contratos.php';
	}

	public function pesquisarContratosView($resultado = array()) {
		require _MODULEDIR_ . 'Manutencao/View/man_insere_texto_contrato/pesquisar_contratos_result.php';
	}

}

