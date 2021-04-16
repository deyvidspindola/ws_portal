<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Fabio Andrei Lorentz
 * @version 25/04/2014
 * @since 25/04/2014
 * @package Core
 * @subpackage Html
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra\Helper;

class Html{
	
	/**
	 * Gera um comboBox (select tag) de acordo com os parâmetros informados
	 * 
	 * @author Fabio Andrei Lorentz [fabio.lorentz@ewave.com.br]
	 * @param  string 	$name 		atributo name (o id fica igual ao name)
	 * @param  array 	$options 	opções do combo box
	 * @param  mixed 	$selected 	opção selecionada
	 * @param  string 	$extra 		atributo extras, ex: "class='myclass' disabled='disabled'"
	 * @return string
	 */
	public static function comboBox($name, array $options, $selected = null, $extra = null){
		$html = "<select name='".$name."' id='".$name."' ".$extra.">";
		if (is_null($selected) && !isset($_POST[$name])) {
			$html .= "	<option value='' selected='selected'>Escolha</option>";
		} else {
			$selected = (isset($_POST[$name]) && is_null($selected)) ? $_POST[$name] : $selected;
			$html .= "	<option value=''>Escolha</option>";
		}
		foreach ($options as $value => $label) {
			$isSelected = ((string)$value == (string)$selected) ? "selected='selected'" : "";
			$html .= "<option value='".$value."' ".$isSelected.">".$label."</option>";
		}
		$html .= "<select>";

		return $html;
	}

	/**
	 * Gera um comboBox múltiplo (select tag) de acordo com os parâmetros informados
	 * 
	 * @author Fabio Andrei Lorentz [fabio.lorentz@ewave.com.br]
	 * @param  string 	$name 		atributo name (o id fica igual ao name)
	 * @param  array 	$options 	opções do combo box
	 * @param  mixed 	$selected 	opção(ões) selecionada(s)
	 * @param  string 	$extra 		atributo extras, ex: "class='myclass' disabled='disabled'"
	 * @return string
	 */
	public static function comboBoxMultiple($name, array $options, $selected = null, $extra = null){
		$html = "<select name='".$name."' id='".$name."' multiple='multiple' ".$extra.">";
		$name = str_replace("[]", "", $name);
		$selected = (isset($_POST[$name]) && is_null($selected)) ? $_POST[$name] : $selected;
		
		if (!is_array($selected))
		{
			$selected = array($selected);
		}
		foreach ($options as $value => $label) {
			$isSelected = (in_array((string)$value, $selected)) ? "selected='selected'" : "";
			$html .= "<option value='".$value."' ".$isSelected.">".$label."</option>";
		}
		$html .= "<select>";

		return $html;
	}

	/**
	 * Gera uma barra de navegação
	 *
	 * @author Fabio Andrei Lorentz [fabio.lorentz@ewave.com.br]
	 * @param  int 		$totalRegistros 	total de registros
	 * @param  int  	$totalPorPagina 	total de registros por página
	 * @param  string 	$funcaoJS			nome da função javascript que será chamadada ao clicar em um item da paginação (passa o numero da página por parâmetro)
	 * @param  int 		$paginaAtual    	numero da página atual, que está sendo acessada
	 * @return string
	 */
	public static function paginacao($totalRegistros, $totalPorPagina, $funcaoJS, $paginaAtual = 1) {
		$limitePaginas = 15;

		$html = "";

		if ($totalRegistros < $totalPorPagina) {
			return $html;
		}

		$totalPaginas = ceil($totalRegistros/$totalPorPagina);

		$html .= "<div class='paginacao'>\n<ul>\n";

		if ($totalPaginas > $limitePaginas) {
			$limit_min = ($limitePaginas % 2 == 0) ? ($limitePaginas / 2) - 1 : ($limitePaginas - 1) / 2;
            $limitePaginas_max = ($limitePaginas % 2 == 0) ? $limit_min + 1 : $limit_min;
            $startPage = $paginaAtual - $limit_min;
            $endPage = $paginaAtual + $limitePaginas_max;

            $startPage = ($startPage < 1) ? 1 : $startPage;
            $endPage = ($endPage < ($startPage + $limitePaginas - 1)) ? $startPage + $limitePaginas - 1 : $endPage;
            if ($endPage > $totalPaginas) {
                $startPage = ($startPage > 1) ? $totalPaginas - $limitePaginas + 1 : 1;
                $endPage = $totalPaginas;
            }
		} else {
			$startPage = 1;
			$endPage = $totalPaginas;
		}

		if ($startPage > 1) {
			$html .= "<li id='P1'>\n<a href='javascript:".$funcaoJS."(1)' title='Página 1'>Primeira</a>\n</li>\n";
			$html .= "<li class='texto'>\n&nbsp;\n</li>\n";
		}

		if ($paginaAtual > 1) {
			$html .= "<li>\n<a href='javascript:".$funcaoJS."(".($paginaAtual - 1).")' title='Página Anterior'><</a>\n</li>\n";
		}

        for ($i=$startPage; $i <= $endPage; $i++) {
        	$classe = ($paginaAtual == $i) ? "class='atual'" : "";
        	$href = ($paginaAtual == $i) ? "javascript:void(0)" : "javascript:".$funcaoJS."(".$i.")";
            $html .= "<li ".$classe." id='P".$i."'>\n<a href='".$href ."' title='Página ".$i."'>".$i."</a>\n</li>\n";
        }

		if ($paginaAtual < $totalPaginas) {
       		$html .= "<li>\n<a href='javascript:".$funcaoJS."(".($paginaAtual + 1).")' title='Próxima Página'>></a>\n</li>\n";
		}

        if ($totalPaginas > $endPage) {
        	$html .= "<li class='texto'>\n&nbsp;\n</li>\n";
			$html .= "<li id='P".$totalPaginas."'>\n<a href='javascript:".$funcaoJS."(".$totalPaginas.")' title='Página ".$totalPaginas."'>Última</a>\n</li>\n";
		}
                       
        $html .= "</ul>\n</div>";

		return $html;
	}
	
}