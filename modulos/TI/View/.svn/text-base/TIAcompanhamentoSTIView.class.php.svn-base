<?php
/**
 * SASCAR (http://www.sascar.com.br/)
 * 
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Módulo para Acompanhamento de STI - Classe View
 * @version 10/10/2012 [0.0.1]
 * @package SASCAR Intranet
*/

class TIAcompanhamentoSTIView {
	public $pgTitulo;
	public $pgHeaderAjax;
	/**
	 * __construct()
	 *
	 * @param none
	 * @return none
	 * @description	Método construtor da classe
	 */
	public function __construct(){
		$this->pgTitulo = 'Intranet Sascar - TI Acompanhamento de STIs';
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
	public function header($strAjaxPrint='') {
		$this->pgHeaderAjax = $strAjaxPrint;
		require 'modulos/TI/View/ti_acompanhamento_sti/header.view.php';
	}
	
	/**
	 * pesquisaForm()
	 *
	 * @param none
	 * @return none
	 * @description	renderiza a view da página inicial
	 */
	public function pesquisaForm() {
		require 'modulos/TI/View/ti_acompanhamento_sti/pesquisa_form.view.php';
	}
	/**
	 * pesquisaFormResult()
	 *
	 * @param array $vPesquisa
	 * @return none
	 * @description	renderiza a view da página inicial
	 */
	public function pesquisaFormResult($vPesquisa = array()) {
		require 'modulos/TI/View/ti_acompanhamento_sti/pesquisa_form_result.view.php';
	}

	/**
	 * pesquisarSolicitante()
	 *
	 * @param none
	 * @return none
	 * @description	renderiza a view da página inicial
	 */
	public function pesquisarSolicitante() {
		require 'modulos/TI/View/ti_acompanhamento_sti/solicitante_popup.view.php';
	}
	

	/**
	 * dadosSTI()
	 *
	 * @param array $vData
	 * @return none
	 * @description	renderiza a view de detalhes da pagina
	 */
	public function dadosSTI($vData = array()) {
		require 'modulos/TI/View/ti_acompanhamento_sti/detalhe_form.view.php';
	}


	/**
	 * getDetalheSTIRelacao()
	 *
	 * @param $vOptions (Vetor com dados da lista)
	 * @return none
	 */
	public function getDetalheSTIRelacao($vOptions) {
		$strCombo = '<table width="100%" style="border: 1px solid gray;">
		  <tr class="tableSubTitulo">
              <td align="center" width="8%">Defeito</td>
              <td align="left" width="18%"><h4>Fase</h4></td>
              <td align="left" width="8%"><h4>Início Previsto</h4></td>
              <td align="left" width="8%"><h4>Início Realizado</h4></td>
              <td align="left" width="8%"><h4>Término Previsto</h4></td>
              <td align="left" width="8%"><h4>Conclusão</h4></td>
              <td align="left" width="8%"><h4>Total Horas Previsto</h4></td>
              <td align="left" width="8%"><h4>Total Horas Utilizado</h4></td>
              <td align="center" width="10%"><h4>Progresso [%]</h4></td>
              <td align="center" width="20%"><h4>Recurso</h4></td>
              <td align="center" width="12%"><h4>Empresa</h4></td>
		  </tr>
		';
		$classZebra = 'tdc';
		$datProgresso = '';
		$datProgressoPerentualTD0 = '';
		$datProgressoPerentualTD1 = '';
		$bgProgresso = '#1C86EE';
		$progressoRight = 0;
		$somaDefeitos = 0;

		if(count($vOptions) > 0){
			foreach ($vOptions as $option) {
				$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
				$progressoLeft = (int) $option['progresso'];
				$progressoLeft = ($progressoLeft <= 0)? 0 : $progressoLeft;
				$progressoLeft = ($progressoLeft > 100)? 100 : $progressoLeft;
				$progressoRight = 100 - $progressoLeft;
				$somaDefeitos += $option['total_def_exec'];
				if($progressoLeft == 0){
					$bgProgresso = '#FFFFFF';
				    $datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="100%" bgcolor="' . $bgProgresso . '" align="left"> 0 % </td></tr></table>';
				}else if($progressoLeft == 100){
					$bgProgresso = '#6CA6CD';
				    $datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="100%" bgcolor="' . $bgProgresso . '" align="left"> 100 % </td></tr></table>';
				}else if($progressoLeft < 10){
					$bgProgresso = '#6CA6CD';
    				$datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="' . $progressoLeft . '%" bgcolor="' . $bgProgresso . '" align="left"> &nbsp;</td><td width="' . $progressoRight . '%" align="left" bgcolor="#FFFFFF"> ' . $progressoLeft . ' % </td></tr></table>';
				}else if($progressoLeft < 30){
					$bgProgresso = '#6CA6CD';
    				$datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="' . $progressoLeft . '%" bgcolor="' . $bgProgresso . '" align="left"> &nbsp;</td><td width="' . $progressoRight . '%" align="left" bgcolor="#FFFFFF"> ' . $progressoLeft . ' % </td></tr></table>';
				}else if($progressoLeft < 50){
					$bgProgresso = '#7EC0EE';
    				$datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="' . $progressoLeft . '%" bgcolor="' . $bgProgresso . '" align="left"> &nbsp;</td><td width="' . $progressoRight . '%" align="left" bgcolor="#FFFFFF"> ' . $progressoLeft . ' % </td></tr></table>';
				}else if($progressoLeft < 80){
					$bgProgresso = '#87CEFF';
    				$datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="' . $progressoLeft . '%" bgcolor="' . $bgProgresso . '" align="left"> ' . $progressoLeft . ' % </td><td width="' . $progressoRight . '%" align="left" bgcolor="#FFFFFF"> &nbsp;</td></tr></table>';
				}else{
					$bgProgresso = '#B0E2FF';
    				$datProgresso = '<table width="100%" style="border:2px solid gray;"><tr><td width="' . $progressoLeft . '%" bgcolor="' . $bgProgresso . '" align="left"> ' . $progressoLeft . ' % </td><td width="' . $progressoRight . '%" align="left" bgcolor="#FFFFFF"> &nbsp;</td></tr></table>';
				}
				$strCombo .= '<tr align="center" class="' . $classZebra . '">
	              <td align="center">'.$option['total_def_exec'].'</td>
	              <td align="left">' . $option['fase'] . '</td>
	              <td align="center">' . $option['inicio_previsto'] . '</td>
	              <td align="center">' . $option['inicio_realizado'] . '</td>
	              <td align="center">' . $option['temino_previsto'] . '</td>
                  <td align="center">' . $option['conclusao'] . '</td>
                  <td align="center">' . $option['horas_estimadas'] . '</td>
	              <td align="center">' . $option['horas_utilizadas'] . '</td>
	              <td align="left">' . $datProgresso . '</td>
	              <td align="left">' . $option['recurso'] . '</td>
	              <td align="left">' . $option['empresa'] . '</td>
				</tr>';
			}

			$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
			$strCombo .= '<tr class="' . $classZebra . '"><td align="left">Total '.$somaDefeitos.'</td></tr>';
		}else{
			$strCombo .= '
			<tr align="center" class="' . $classZebra . '">
	          <td align="center" colspan="9">&nbsp; Nenhum registro de fase programada!</td>
			</tr>
			';
		}
		
		$strCombo .= '</table>';
		return $strCombo;
	}
	

	/**
	 * getComboBoxUsuario()
	 *
	 * @param $vOptions (Vetor com dados da lista)
	 * @return none
	 */
	public function getComboBoxUsuario($vOptions) {
		$strCombo = '<SELECT id="sti_usuario" name="sti_usuario">';
		$strCombo .= '  <option value=""> Escolha </option>';
		foreach ($vOptions as $option) {
			$strCombo .= '<option value="' . $option['cd_usuario'] . '">' . $option['nm_usuario'] . '</option>';
		}
		$strCombo .= '</SELECT>';
		return $strCombo;
	}
	
	
	/**
	 * getComboBoxListSubtipo()
	 *
	 * @param $vOptions (Vetor com dados da lista)
	 * @return none
	 */
	public function getComboBoxListSubtipo($vOptions) {
		$strCombo = '<SELECT id="sti_subtipo" name="sti_subtipo">';
		$strCombo .= '  <option value=""> Escolha </option>';
		foreach ($vOptions as $option) {
			$strCombo .= '<option value="' . $option['rqstoid'] . '">' . $option['rqsttipo'] . '</option>';
		}
		$strCombo .= '</SELECT>';
		return $strCombo;
	}
	


	/**
	 * getComboBoxListRecursoFase()
	 *
	 * @param $vOptions (Vetor com dados da lista)
	 * 		  $sti_recurso_sel = recurso pré-selecionado
	 * @return none 
	 */
	public function getComboBoxListRecursoFase($vOptions, $sti_recurso_sel='') {
		$strCombo = '<SELECT id="sti_recurso" name="sti_recurso">';
		$strCombo .= '  <option value=""> Escolha </option>';
		$pre_sel = '';
		foreach ($vOptions as $option) {
			$pre_sel = ($option['cd_usuario'] == $sti_recurso_sel) ? ' selected="selected"' : '';
			$strCombo .= '<option value="' . $option['cd_usuario'] . '" ' . $pre_sel . '>' . $option['nm_usuario'] . '</option>';
		}
		$strCombo .= '</SELECT>';
		return $strCombo;
	}
	

	/**
	 * getListRecursoFase()
	 *
	 * @param $vLista (Vetor com dados da lista)
	 * @return none
	 */
	public function getListRecursoFase($vLista) {
		$strHtml = '';
		$strHtml .= '<table class="tableMoldura">';
		$strHtml .= '<tr class="tableTituloColunas">';
		$strHtml .= '<td width="50%"><h3>Recurso</h3></td>';
		$strHtml .= '<td width="15%"><h3>Data Inicial</h3></td>';
		$strHtml .= '<td width="15%"><h3>Data Final</h3></td>';
		$strHtml .= '<td width="15%"><h3>Nº Horas</h3></td>';
		if($_SESSION['funcao']['sti_edicao'] == 1){
			$strHtml .= '<td width="5%"><h3>Excluir</h3></td>';
		}
		$strHtml .= '</tr>';
		$classZebra = 'tde';
		$fPodeAltExc = true;
		if($vLista['qtd_itens'] > 0){//qtd_itens
			for($i=0; $i < $vLista['qtd_itens']; $i++){
				$classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
				$imgX = ($imgX == 'images/icones/t1/x.jpg') ? 'images/icones/tf1/x.jpg' : 'images/icones/t1/x.jpg'; 
				$fPodeAltExc = ((strlen($vLista[$i]['inicio_exec']) == 0 || $_SESSION['funcao']['sti_acesso_full']==1) && ($_SESSION['funcao']['sti_edicao'] == 1)) ? true : false;  

				$strHtml .= '<tr class="' . $classZebra . '">';
				$strHtml .= '<td><a href="JavaScript:;"';
				if($fPodeAltExc){
					$strHtml .= ' onclick="Javascript:xajax_preparaFormAlteracaoRecursoFase(' . $vLista[$i]['reqieroid'] . ');"';
				}
				$strHtml .= '>' . $vLista[$i]['nm_usuario'] . '</td>';
				$strHtml .= '<td>' . $vLista[$i]['inicio']. '</td>';
				$strHtml .= '<td>' . $vLista[$i]['final']. '</td>';
				$strHtml .= '<td>' . $vLista[$i]['reqierhoras_estimadas']. '</td>';
				if($fPodeAltExc){
					$strHtml .= '<td><a href="JavaScript:;" onclick="Javascript:excluirPlanejamentoFase(' . $vLista[$i]['reqieroid'] . ');"><img src="' . $imgX . '"></a></td>'; 
				} else if ($_SESSION['funcao']['sti_edicao'] == 1) {
                    $strHtml .= '<td></td>';
                }
				$strHtml .= '</tr>';
			}
		}else{
			$strHtml .= '<tr>';
			$strHtml .= '<td colspan="5"><span class="msg">Não há recursos planejados </span></td>';
			$strHtml .= '</tr>';
		}
		$strHtml .= '</table>';
		return $strHtml;
	}
	
	

	/**
	 * getFasesAbas()
	 *
	 * @param $vAbas (Vetor com dados da abas-fases)
	 * @return none
	 */
	public function getFasesAbas($vAbas, $faseAtiva=0) {
		$vAba = array();
		$i= 0;
		$class = '';
		$strHtml = '<table width="98%">';
		$strHtml .= '  <tr>';
		$strHtml .= '	<td align="left" id="navAbasFases">';
		$strHtml .= '	  <table>';
		$strHtml .= '		<tr>';
		if($vAbas['qtd_itens'] > 0){
			foreach ($vAbas['relacao'] as $vAba) {
				$class = ($i == $faseAtiva)? 'class="active"' : '';
				$strHtml .= '<td align="center" id="tabnav">';
                $strHtml .= ' <a fase="' . $vAba['reqifsoid'] . '" lancamento="' . $vAbas['filtro']['fluxo_fases_lancamento'] . '" href="javascript:void(null);"';
				$strHtml .= ' onclick="javascript:xajax_getFasesAbas(document.solstiform.fluxo_fase_lancamento.value, ' . $i . '); ';
				$strHtml .= ' javascript:xajax_getFasesAbasForm(' . "'" . $vAba['reqifsoid'] . '-' . $vAbas['filtro']['fluxo_fases_lancamento'] . '-' . $vAbas['filtro']['sti'] . "'" . ');" ';
				$strHtml .= $class . '> ' . $vAba['reqifsdescricao'] . '</a>';
				$strHtml .= '</td>';
				$i++;
			}
		}else{
			$strHtml .= '<td align="left" height="35">&nbsp;&nbsp;&nbsp;&nbsp; Sem registro de fases programadas.</td>';
		}
		$strHtml .= '	</tr>';
		$strHtml .= '  </table>';
		$strHtml .= ' </td>';
		$strHtml .= '</tr>';
		$strHtml .= '</table>';
	
		return $strHtml;
	}
	

	/**
	 * getFasesAbasForm()
	 *
	 * @param $vForm (Vetor com dados da fase)
	 * @return none
	 */
	public function getFasesAbasForm($vForm) {
		$strHtml = '';
		$fUserPlanejamento = ($_SESSION['usuario']['oid'] == $vForm['relacao'][0]['planejamento']) ? true : false;
		$fUserExecucao = ($_SESSION['usuario']['oid'] == $vForm['relacao'][0]['executor']) ? true : false;
		$fUserAlteracao = ($_SESSION['funcao']['sti_alteracao'] == 1)? true : false;

		// Datas já alteradas
		$alterouPeriodoRealizadoIni = false;
		$alterouPeriodoRealizadoFim = false;
		if(strlen($vForm['relacao'][0]['periodo_realizado_ini']) > 0){
		    $alterouPeriodoRealizadoIni = true;
		}
		if(strlen($vForm['relacao'][0]['periodo_realizado_fim']) > 0){
		    $alterouPeriodoRealizadoFim = true;
		}

        $totalHorasUtilizadas = ( $vForm['total_horas_utilizadas'] == '') ? "00:00" : $vForm['total_horas_utilizadas'];
		$strHtml .= '
		<table class="tableMoldura" id="form_config_recurso" width="98%" align="center">
		  <tr>
			<td width="100%" align="left" colspan="2">
			  <br>
              <div id="fases_form_area_recurso_msg" align="left"> </div>
			</td>
		  </tr>
          <tr>
            <td width="15%" height="32" align="left">
              <label>Total Horas:</label>
            </td>
            <td width="85%" align="left">
                <div style="float: left;">' . $totalHorasUtilizadas . '</div>
              <a id="horas_utilizadas" href="javascript: void(0);" style="float: left; margin-left: 3px;">
                <img src="images/icon_info.png" valign="bottom"></img>
              </a>
            </td>
		  <tr>
			<td width="15%" height="32" align="left">
			  <label for="reqieroid_sel">Recurso *:</label>
			</td>
			<td width="85%" align="left">
			  <SELECT id="reqieroid_sel" name="reqieroid_sel" onChange="javascript:xajax_getFasesAbasFormRecurso(document.solstiform.reqieroid_sel.value);">
			';

		$recursoSelected = '';
		foreach ($vForm['relacao'] as $vrecurso) {
			$recursoSelected = ($vrecurso['executor'] == $vForm['relacao'][0]['executor']) ? 'selected="selected"' : '';
			$strHtml .= ' <option value="' . $vrecurso['reqieroid'] . '" ' . $recursoSelected . ' > ' . $vrecurso['recurso'] . ' </option> ';
		}

		$strHtml .= '
			  </SELECT>
            </td>
		  </tr>
		  <tr>
			<td width="100%" align="left" colspan="2">
			<div id="fases_form_area_recurso" align="left">
			<table width="98%" align="left">
			';

		if($fUserPlanejamento || $fUserAlteracao){
			$strHtml .= '
			      <tr>
					<td width="15%" align="left">
				      <label for="reqierdt_previsao_inicio">Período Previsto *:</label>
				    </td>
				    <td width="85%" align="left">
					  <input type="text" name="reqierdt_previsao_inicio" id="reqierdt_previsao_inicio" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_prev_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_previsao_inicio,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
					  &nbsp;&nbsp;
	       		      <input type="text" name="reqierdt_previsao_fim" id="reqierdt_previsao_fim" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_prev_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_previsao_fim,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
	       		    </td>
	              </tr>';
		} else {
			$strHtml .= '
			      <tr>
					<td width="15%" align="left">
				      <label for="reqierdt_previsao_inicio">Período Previsto *:</label>
				    </td>
				    <td width="85%" align="left">
					  <input type="text" name="reqierdt_previsao_inicio" id="reqierdt_previsao_inicio" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_prev_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
					  &nbsp;&nbsp;
	       		      <input type="text" name="reqierdt_previsao_fim" id="reqierdt_previsao_fim" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_prev_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
	       		    </td>
	              </tr>';
		}

		// Controle de datas realizado
		if($fUserExecucao || $fUserAlteracao){
			    
		    if($fUserAlteracao){
				$strHtml .= '
    	 		      <tr>
    					<td width="15%" align="left">
    				      <label for="reqierdt_inicio">Período Realizado *:</label>
    				    </td>
    				    <td width="85%" align="left">
    					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_inicio,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    					  &nbsp;&nbsp;
    	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_conclusao,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    	       		    </td>
    	              </tr>
    				';
		    }else{
		        $strHtml .= '
    	 		      <tr>
    					<td width="15%" align="left">
    				      <label for="reqierdt_inicio">Período Realizado *:</label>
    				    </td>';
		        if($alterouPeriodoRealizadoIni){
		            $strHtml .= '
    				    <td width="85%" align="left">
    					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');"  readonly="readonly">
    	       		      &nbsp;&nbsp;
    				';
		        }else{
		            $strHtml .= '
    				    <td width="85%" align="left">
    					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_inicio,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    					  &nbsp;&nbsp;
    				';
		        }

		        if($alterouPeriodoRealizadoFim){
		            $strHtml .= '
    	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');"  readonly="readonly">
    				';
		        }else{
		            $strHtml .= '
    	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_conclusao,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    				';
		        }
		        $strHtml .= '
    	       		    </td>
    	              </tr>
			        ';
		    }
		}else{
			$strHtml .= '
	 		      <tr>
					<td width="15%" align="left">
				      <label for="reqierdt_inicio">Período Realizado *:</label>
				    </td>
				    <td width="85%" align="left">
					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
					  &nbsp;&nbsp;
	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['relacao'][0]['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
	       		    </td>
	              </tr>';
		}

		$cbx_concluir_execucao_check = (strlen($vForm['relacao'][0]['periodo_realizado_fim']) > 0) ? 'CHECKED' : '';
		if($fUserAlteracao || $fUserExecucao){
		    $strHtml .= '
			  <tr>
				<td width="15%" align="left">
			      <label for="reqierprogresso">Progresso *:</label>
			    </td>
			    <td width="85%" align="left">
                  <input type="text" id="reqierprogresso" name="reqierprogresso" size="5" maxlength="3" value="' . $vForm['relacao'][0]['progresso'] . '"  onkeyup="formatar(this, ' . "'@@@@@@@@@@'" . ')"> %
			      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="cbx_concluir_execucao" type="checkbox" value="1" onClick="JavaScript:concluirExecucaoCbx();" name="cbx_concluir_execucao" ' . $cbx_concluir_execucao_check . '> Concluído
       		    </td>
              </tr>';
		}else{
			$strHtml .= '
			  <tr>
				<td width="15%" align="left">
			      <label for="reqierprogresso">Progresso *:</label>
			    </td>
			    <td width="85%" align="left">
                  <input type="text" id="reqierprogresso" name="reqierprogresso" size="5" maxlength="3" value="' . $vForm['relacao'][0]['progresso'] . '"  onkeyup="formatar(this, ' . "'@@@@@@@@@@'" . ')"   readonly="readonly"> %
			      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="cbx_concluir_execucao" type="checkbox" value="1" onClick="JavaScript:concluirExecucaoCbx();" name="cbx_concluir_execucao" ' . $cbx_concluir_execucao_check .' disabled="disabled"> Concluído
       		    </td>
              </tr>';
		}

        $horasUtilizadas = ($vForm['relacao'][0]['horas_utilizadas'] == '') ? "00:00" : $vForm['relacao'][0]['horas_utilizadas'];
		$strHtml .= '
			  <tr>
				<td width="15%" style="height: 26px;" align="left">
			      <label for="reqierhoras_realizado">Horas Utilizadas:</label>
			    </td>
			    <td width="85%" align="left">
                  ' . $horasUtilizadas . '
       		    </td>
              </tr>';

		$strHtml .= '
              <tr>
				<td width="15%" align="left">
			      <label for="reqierdescricao_defeito">Observação:</label>
			    </td>
			    <td width="85%" align="left">
                  <textarea name="reqierdescricao_defeito" id="reqierdescricao_defeito" rows="5" cols="35">' .	$vForm['relacao'][0]['descricao'] . '</textarea>
       		    </td>
              </tr>';

        if ( $fUserPlanejamento || $fUserAlteracao || $fUserExecucao ) {

            $strHtml .= '
                  <tr>
                    <td width="15%" align="left" height="35">
                      &nbsp;
                    </td>
                    <td width="85%" align="left">
                       <input type="button" name="bt_fase_aba_confirmar" id="bt_fase_aba_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarAbaFase();" />
                       <input type="button" name="bt_fase_aba_atualizar_defeitos" id="bt_fase_aba_atualizar_defeitos" class="botao" value="Atualizar Defeitos" onclick="javascript:atualizarDefeitosAbaFase();" />
                    </td>
                  </tr>';
        }

        $strHtml .= '        
             </table>
   			  <table width="98%" align="left">
                <tr>
				  <td width="100%" height="25" align="left" colspan="2"><label for="historico">Histórico:</label> </td>
	            </tr>
                <tr>
				  <td width="100%" align="left" colspan="2">
				    <div id="recurso_historico" align="center"> </div>
				  </td>
	            </tr>
              </table>
            </td>
		  </tr>
        </table>

        <div id="exibir_detalhes_apontamentos" style="display:none" title="Apontamento de Horas">

            <table class="tableMoldura">
                <thead>
                    <tr class="tableTituloColunas">
                      <td style="width: 10%;">STI</td>
                      <td style="width: 25%;">Usuario</td>
                      <td style="width: 15%;">Data</td>
                      <td style="width: 10%;">Horas</td>
                      <td style="width: 40%; border: 0;">Tipo</td>
                    </tr>
                </thead>
                <tbody id="tabela_detalhes_apontamentos"></tbody>
            </table>
                
        </div>
		';
		return $strHtml;
	}



	/**
	 * getFasesAbasFormRecurso()
	 *
	 * @param $vForm (Vetor com dados da fase)
	 * @return none
	 */
	public function getFasesAbasFormRecurso($vForm) {
	    $strHtml = '';
	    $fUserPlanejamento = ($_SESSION['usuario']['oid'] == $vForm['planejamento']) ? true : false;
	    $fUserExecucao = ($_SESSION['usuario']['oid'] == $vForm['executor']) ? true : false;
	    $fUserAlteracao = ($_SESSION['funcao']['sti_alteracao'] == 1)? true : false;

	    // Datas já alteradas
	    $alterouPeriodoRealizadoIni = false;
	    $alterouPeriodoRealizadoFim = false;
	    if(strlen($vForm['relacao']['periodo_realizado_ini']) > 0){
	        $alterouPeriodoRealizadoIni = true;
	    }
	    if(strlen($vForm['relacao']['periodo_realizado_fim']) > 0){
	        $alterouPeriodoRealizadoFim = true;
	    }
	    $strHtml .= '
	           <table width="98%" align="left">
			';
	    if($fUserPlanejamento || $fUserAlteracao){
	        $strHtml .= '
			      <tr>
					<td width="15%" align="left">
				      <label for="reqierdt_previsao_inicio">Período Previsto *:</label>
				    </td>
				    <td width="85%" align="left">
					  <input type="text" name="reqierdt_previsao_inicio" id="reqierdt_previsao_inicio" size="10" maxlength="10" value="' . $vForm['periodo_prev_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_previsao_inicio,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
					  &nbsp;&nbsp;
	       		      <input type="text" name="reqierdt_previsao_fim" id="reqierdt_previsao_fim" size="10" maxlength="10" value="' . $vForm['periodo_prev_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_previsao_fim,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
	       		    </td>
	              </tr>';
	    }else{
	        $strHtml .= '
			      <tr>
					<td width="15%" align="left">
				      <label for="reqierdt_previsao_inicio">Período Previsto *:</label>
				    </td>
				    <td width="85%" align="left">
					  <input type="text" name="reqierdt_previsao_inicio" id="reqierdt_previsao_inicio" size="10" maxlength="10" value="' . $vForm['periodo_prev_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
					  &nbsp;&nbsp;
	       		      <input type="text" name="reqierdt_previsao_fim" id="reqierdt_previsao_fim" size="10" maxlength="10" value="' . $vForm['periodo_prev_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
	       		    </td>
	              </tr>';
	    }

	    // Controle de datas realizado ======================
	    if($fUserExecucao || $fUserAlteracao){
	        if($fUserAlteracao){
	        	$strHtml .= '
    	 		      <tr>
    					<td width="15%" align="left">
    				      <label for="reqierdt_inicio">Período Realizado *:</label>
    				    </td>
    				    <td width="85%" align="left">
    					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_inicio,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    					  &nbsp;&nbsp;
    	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_conclusao,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    	       		    </td>
    	              </tr>
    				';
	        }else{
	            $strHtml .= '
    	 		      <tr>
    					<td width="15%" align="left">
    				      <label for="reqierdt_inicio">Período Realizado *:</label>
    				    </td>';
	            if($alterouPeriodoRealizadoIni){
	                $strHtml .= '
    				    <td width="85%" align="left">
    					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');"  readonly="readonly">
    					  &nbsp;&nbsp;
    			';
	            }else{
	                $strHtml .= '
    				    <td width="85%" align="left">
    					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_inicio,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    					  &nbsp;&nbsp;
    			';
	            }
	            if($alterouPeriodoRealizadoFim){
	                $strHtml .= '
    	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');"  readonly="readonly">
    			';
	            }else{
	                $strHtml .= '
    	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');">
    	       		      <img src="images/calendar_cal.gif" border="0" onclick="displayCalendar(document.solstiform.reqierdt_conclusao,' . "'dd/mm/yyyy'" . ',this)" align="top" alt="Calendário...">
    			';
	            }
	            $strHtml .= '
    	       		    </td>
    	              </tr>
			    ';
	        }
	    }else{
	        $strHtml .= '
	 		      <tr>
					<td width="15%" align="left">
				      <label for="reqierdt_inicio">Período Realizado *:</label>
				    </td>
				    <td width="85%" align="left">
					  <input type="text" name="reqierdt_inicio" id="reqierdt_inicio" size="10" maxlength="10" value="' . $vForm['periodo_realizado_ini'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
					  &nbsp;&nbsp;
	       		      <input type="text" name="reqierdt_conclusao" id="reqierdt_conclusao" size="10" maxlength="10" value="' . $vForm['periodo_realizado_fim'] . '" onkeyup="formatar(this, ' . "'@@/@@/@@@@'" . ')" onBlur="revalidar(this,' . "'@@/@@/@@@@'" . ',' . "'data'" . ');" readonly="readonly">
	       		    </td>
	              </tr>
	        ';
	    }
	    
	    $cbx_concluir_execucao_check = (strlen($vForm['periodo_realizado_fim']) > 0) ? 'CHECKED' : '';
	    if($fUserAlteracao || $fUserExecucao){
	        $strHtml .= '
			  <tr>
				<td width="15%" align="left">
			      <label for="reqierprogresso">Progresso *:</label>
			    </td>
			    <td width="85%" align="left">
                  <input type="text" id="reqierprogresso" name="reqierprogresso" size="5" maxlength="3" value="' . $vForm['progresso'] . '"  onkeyup="formatar(this, ' . "'@@@@@@@@@@'" . ')"> %
			      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="cbx_concluir_execucao" type="checkbox" value="1" onClick="JavaScript:concluirExecucaoCbx();" name="cbx_concluir_execucao" ' . $cbx_concluir_execucao_check . '> Concluído
       		    </td>
              </tr>';
	    }else{
	        $strHtml .= '
			  <tr>
				<td width="15%" align="left">
			      <label for="reqierprogresso">Progresso *:</label>
			    </td>
			    <td width="85%" align="left">
                  <input type="text" id="reqierprogresso" name="reqierprogresso" size="5" maxlength="3" value="' . $vForm['progresso'] . '"  onkeyup="formatar(this, ' . "'@@@@@@@@@@'" . ')" readonly="readonly"> %
			      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="cbx_concluir_execucao" type="checkbox" value="1" onClick="JavaScript:concluirExecucaoCbx();" name="cbx_concluir_execucao" ' . $cbx_concluir_execucao_check .' disabled="disabled"> Concluído
       		    </td>
              </tr>';
	    }

        $horasUtilizadas = ($vForm['horas_utilizadas'] == '') ? "00:00" : $vForm['horas_utilizadas'];
        $strHtml .= '
			  <tr>
				<td width="15%" style="height: 26px;" align="left">
			      <label for="reqierhoras_realizado">Horas Utilizadas:</label>
			    </td>
			    <td width="85%" align="left">
                  ' . $horasUtilizadas . '
       		    </td>
              </tr>';

	    $strHtml .= '
              <tr>
				<td width="15%" align="left">
			      <label for="reqierdescricao_defeito">Observação:</label>
			    </td>
			    <td width="85%" align="left">
                  <textarea name="reqierdescricao_defeito" id="reqierdescricao_defeito" rows="5" cols="35">' .	$vForm['descricao'] . '</textarea>
       		    </td>
              </tr>';

        if ( $fUserPlanejamento || $fUserAlteracao || $fUserExecucao ) {
            $strHtml .= '
                  <tr>
                    <td width="15%" align="left" height="35">
                      &nbsp;
                    </td>
                    <td width="85%" align="left">
                       <input type="button" name="bt_fase_aba_confirmar" id="bt_fase_aba_confirmar" class="botao" value="Confirmar" onclick="javascript:confirmarAbaFase();" />
                       <input type="button" name="bt_fase_aba_atualizar_defeitos" id="bt_fase_aba_atualizar_defeitos" class="botao" value="Atualizar Defeitos" onclick="javascript:atualizarDefeitosAbaFase();" />
                    </td>
                  </tr>';
        }

        $strHtml .= '</table>
              <table width="98%" align="left">
                <tr>
                  <td width="100%" height="25" align="left" colspan="2"><label for="historico">Histórico:</label> </td>
                </tr>
                <tr>
                  <td width="100%" align="left" colspan="2">
                    <div id="recurso_historico" align="center"> </div>
                  </td>
                </tr>
              </table>
        ';
	    return $strHtml;
	}
	
	
	/**
	 * getFasesAnexos()
	 *
	 * @param $vLista (Vetor com dados da lista)
	 * @return none
	 */
	public function getFasesAnexos($vLista) {
	    $strHtml = '';
	    $strHtml .= '<table class="tableMoldura" width="100%">';
	    $strHtml .= '<tr class="tableTituloColunas">';
	    $strHtml .= '<td width="10%"><h3>Data</h3></td>';
	    $strHtml .= '<td width="40%"><h3>Arquivo</h3></td>';
	    $strHtml .= '<td width="32%"><h3>Descrição</h3></td>';
	    $strHtml .= '<td width="15%"><h3>Usuário</h3></td>';
	    if($_SESSION['funcao']['sti_edicao'] == 1){
	        $strHtml .= '<td width="3%"><h3>Excluir</h3></td>';
	    }
	    $strHtml .= '</tr>';
	    $classZebra = 'tde';
	    $fPodeAltExc = true;
	    if($vLista['qtd_itens'] > 0){//qtd_itens
	        for($i=0; $i < $vLista['qtd_itens']; $i++){
	            $classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
	            $imgX = ($imgX == 'images/icones/t1/x.jpg') ? 'images/icones/tf1/x.jpg' : 'images/icones/t1/x.jpg';
	            $fPodeAltExc = ($_SESSION['funcao']['sti_edicao'] == 1) ? true : false;
	
	            $strHtml .= '<tr class="' . $classZebra . '">';
	            $strHtml .= '<td>' . $vLista[$i]['data']. '</td>';
	            $strHtml .= '<td><a href="JavaScript:;"';
	            if($fPodeAltExc){
	                $strHtml .= ' onclick="Javascript:downloadAnexo(' . "'" .  $vLista[$i]['arquivo'] . "'" . ');"';
	            }
	            $strHtml .= '>' . $vLista[$i]['arquivo'] . '</td>';
	            $strHtml .= '<td>' . $vLista[$i]['descricao']. '</td>';
	            $strHtml .= '<td>' . $vLista[$i]['usuario']. '</td>';
	            if($fPodeAltExc){
	                $strHtml .= '<td align="center"><a href="JavaScript:;" onclick="Javascript:validaExcluirAnexo(document.solstiform.sti.value, document.solstiform.reqieroid_sel.value, ' . $vLista[$i]['id'] . ');"><img src="' . $imgX . '"></a></td>';
	            }
	            $strHtml .= '</tr>';
	        }
	    }else{
	        $strHtml .= '<tr>';
	        $strHtml .= '<td colspan="5"><span class="msg">Não há anexos registrados </span></td>';
	        $strHtml .= '</tr>';
	    }
	    $strHtml .= '</table>';
	    return $strHtml;
	}
	

	/**
	 * getFasesHistorico()
	 *
	 * @param $vLista (Vetor com dados da lista)
	 * @return none
	 */
	public function getFasesHistorico($vLista) {
	    $strHtml = '';
	    $strHtml .= '<table class="tableMoldura" width="100%">';
	    $strHtml .= '<tr class="tableTituloColunas">';
	    $strHtml .= '<td width="15%"><h3>Data</h3></td>';
	    $strHtml .= '<td width="60%"><h3>Observação</h3></td>';
	    $strHtml .= '<td width="25%"><h3>Usuário</h3></td>';
	    $strHtml .= '</tr>';
	    $classZebra = 'tde';
	    $fPodeAltExc = true;
	    if($vLista['qtd_itens'] > 0){//qtd_itens
	        for($i=0; $i < $vLista['qtd_itens']; $i++){
	            $classZebra  = ($classZebra == 'tdc') ? 'tde' : 'tdc';
	            $strHtml .= '<tr class="' . $classZebra . '">';
	            $strHtml .= '<td>' . $vLista[$i]['data']. '</td>';
	            $strHtml .= '<td>' . $vLista[$i]['observacao']. '</td>';
	            $strHtml .= '<td>' . $vLista[$i]['usuario']. '</td>';
	            $strHtml .= '</tr>';
	        }
	    }else{
	        $strHtml .= '<tr>';
	        $strHtml .= '<td colspan="5"><span class="msg">Não há registro de histórico </span></td>';
	        $strHtml .= '</tr>';
	    }
	    $strHtml .= '</table>';
	    return $strHtml;
	}
	
	/**
	 * getDadosFluxo()
	 *
	 * @param array $vData
	 * @return none
	 * @description	renderiza a view de fluxos
	 */
	public function getDadosFluxo($vData = array()) {
	    require 'modulos/TI/View/ti_acompanhamento_sti/fluxo_form.view.php';
	}
	
	/**
	 * getDadosFase()
	 *
	 * @param array $vData
	 * @return none
	 * @description	renderiza a view de fases
	 */
	public function getDadosFase($vData = array()) {
	    require 'modulos/TI/View/ti_acompanhamento_sti/fase_form.view.php';
	}
	
	/**
	 * getDadosFuncao()
	 *
	 * @param array $vData
	 * @return none
	 * @description	renderiza a view de funcoes
	 */
	public function getDadosFuncao($vData = array()) {
	    require 'modulos/TI/View/ti_acompanhamento_sti/funcao_form.view.php';
	}
	
	/**
	 * acompanhamentoFormResult()
	 *
	 * @param none
	 * @return none
	 * @description	renderiza a view de acompanhamento
	 */
	public function acompanhamentoFormResult() {
	    require 'modulos/TI/View/ti_acompanhamento_sti/acompanhamento_form_result.view.php';
	}

    public function desenhaDetalhesApontamentos($apontamentos) {
        $html = '';
        $class = '';
                                          
        foreach ($apontamentos as $apontamento) {
            $class = $class == 'tdc' ? 'tde' : 'tdc';

            $html .= '<tr class="item ' . $class . '">';
            
            $html .= '  <td style="text-align: right; padding-right: 10px;>">';
            $html .=        $apontamento->sti;
            $html .= '  </td>';
            
            $html .= '  <td>';
            $html .=        $apontamento->usuario;
            $html .= '  </td>';
            
            $html .= '  <td style="text-align: center;">';
            $html .=        $apontamento->data;
            $html .= '  </td>';
            
            $html .= '  <td style="text-align: center;">';
            $html .=        $apontamento->horas;
            $html .= '  </td>';
            
            $html .= '  <td>';
            $html .=        $apontamento->tipo;
            $html .= '  </td>';

            $html .= '</tr>';
        }

        return $html;
    }
	
}