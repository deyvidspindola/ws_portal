<?php
/**
 * SASCAR (http://www.sascar.com.br/)
 * 
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Módulo para Cadastro de Planos Satelitais - Classe View
 * @version 28/03/2013 [1.0]
 * @package SASCAR Intranet
*/

class CadPlanoSatelitalView {
	public $pgTitulo;
	/**
	 * __construct()
	 *
	 * @param none
	 * @return none
	 * @description	Método construtor da classe
	 */
	public function __construct(){
		$this->pgTitulo = 'Intranet Sascar - Cadastro de Planos Satelitais';
	}
	
	/**
	 * getTitulo()
	 *
	 * @param none
	 * @return string $titulo
	 * @description	Método para pegar título
	 */
	public function getTitulo(){
		return $this->pgTitulo;
	}
	
	/**
	 * setTitulo()
	 *
	 * @param string $titulo
	 * @return none
	 * @description	Método para setar título
	 */
	public function setTitulo($titulo=''){
		$this->pgTitulo = $titulo;
	}
	
	/**
	 * header()
	 *
	 * @param none
	 * @return none
	 * @description	renderiza a view do header de página
	 */
	public function header() {
		require 'modulos/Cadastro/View/cad_plano_satelital/header.view.php';
	}
	
	/**
	 * getDadosPlano()
	 *
	 * @param array $vData
	 * @return none
	 * @description	renderiza a view de planos
	 */
	public function getDadosPlano($vData = array()) {
	    require 'modulos/Cadastro/View/cad_plano_satelital/plano_form.view.php';
	}
	
}