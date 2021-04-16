<?php
/**
 * View - <PrnTermoAditivoServicoView.class.php>
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 03/04/2012
 */
class FinTransferenciaTitularidadeView {
	function __construct() {
	}
	
	/**
	 * Renderiza o HTML com o resultado da pesquisa.
	 * 
	 * @param Array $dados        	
	 */
	public function getComponenteListaPessoaAutorizada($dados) {
		$html = "";
		$html .= "<table><thead><tr><th style='text-align: center';>Nome</th>
				<th style='text-align: center';>CPF</th>
				<th style='text-align: center';>RG</th>
				<th style='text-align: center';>Fone Residencial</th>
				<th style='text-align: center';>Fone Comercial</th>
				<th style='text-align: center';>Fone Celular</th>
				<th style='text-align: center';>ID Nextel</th>
				<th style='text-align: center';>Acoes</th></tr></thead><tbody>";
		
	
		foreach ( $dados as $row ) :
		$class = $class == '' ? 'par' : '';
			
		$html .= "<tr class=" . $class . ">
		<input type=hidden id=idContPessoaAut name=idContPessoaAut value=".$row[ptpaptraoid ]." />
		<td style=text-align: center;>$row[ptpanome]</td>
		<td style=text-align: center;>$row[ptpacpf]</td>
		<td style=text-align: center;>$row[ptparg]</td>
		<td style=text-align: center;>$row[ptpafone_residencial]</td>
		<td style=text-align: center;>$row[ptpafone_comercial]</td>
		<td style=text-align: center;>$row[ptpafone_celular]</td>
		<td style=text-align: center;>$row[ptpaidnextel]</td>
				<td class='acao centro'>
		             <a title=Excluir rel=".$row['ptpaoid']." id=btn_excluir_pessoas_aut href=javascript:void(0);>
		           		<IMG class=icone alt=Excluir src=images/icon_error.png></a>
		           		<a title=Editar rel=".$row['ptpaoid']." id=btn_editar_pessoas_aut href=javascript:void(0);>
		           		<IMG class=icone alt=Editar src=images/icon_editar.gif></a>
					</td>
		</tr>";
		endforeach;
		
		$html .= "</tbody><tfoot><tr class='center'><td align='center' colspan='8'></td></tr></tfoot>";
		
		echo json_encode ( array ("html" => $html) );
	}
	
	public function getComponenteListaContatosEmergencia($dados){
		$html = "";
		$html .= "<table><thead><tr><th style='text-align: center';>Nome</th>
				<th style='text-align: center';>Fone Residencial</th>
				<th style='text-align: center';>Fone Comercial</th>
				<th style='text-align: center';>Fone Celular</th>
				<th style='text-align: center';>ID Nextel</th>
				<th style='text-align: center';>Acoes</th></tr></thead><tbody>";
	
		foreach ( $dados as $row ) :
		$class = $class == '' ? 'par' : '';
			
		$html .= "<tr class=" . $class . ">
		<input type=hidden id=idContEmerg name=idContEmerg value=".$row[ptceptraoid ]." />
		<td style='text-align: center';>$row[ptcenome]</td>
		<td style='text-align: center';>$row[ptcefone_residencial]</td>
		<td style='text-align: center';>$row[ptcefone_comercial]</td>
		<td style='text-align: center';>$row[ptcefone_celular]</td>
		<td style='text-align: center';>$row[ptceidnextel]</td>
	   <td class='acao centro'>
		 <a title=Excluir rel=$row[ptceoid] id=btn_excluir_contemerg href=javascript:void(0);>
		 <IMG class=icone alt=Excluir src=images/icon_error.png></a>
		<a title=Editar rel=$row[ptceoid] id=btn_editar_contemerg href=javascript:void(0);>
		<IMG class=icone alt=Editar src=images/icon_editar.gif></a>
		</td>
		</tr>";
		endforeach;
		
		$html .= "</tbody><tfoot><tr class='center'><td align='center' colspan='6'></td></tr></tfoot>";
		
		echo json_encode ( array ("html" => $html) );
	}
	
	
	public function getComponenteListaContatoInstalacao($dados){
		$html = "";
		$html .= "<table><thead><tr><th style='text-align: center';>Nome</th>
				<th style='text-align: center';>Fone Residencial</th>
				<th style='text-align: center';>Fone Comercial</th>
				<th style='text-align: center';>Fone Celular</th>
				<th style='text-align: center';>ID Nextel</th>
				<th style='text-align: center';>Acoes</th></tr></thead><tbody>";
	
		foreach ( $dados as $row ) :
		$class = $class == '' ? 'par' : '';

							
		$html .= "<tr class=" . $class . ">
		<input type=hidden id=idInstalAssis name=idInstalAssis value=".$row[ptciptraoid ]." />
		<td style='text-align: center';>$row[ptcinome]</td>
		<td style='text-align: center';>$row[ptcifone_residencial]</td>
		<td style='text-align: center';>$row[ptcifone_comercial]</td>
		<td style='text-align: center';>$row[ptcifone_celular]</td>
		<td style='text-align: center';>$row[ptcidnextel]</td>
		<td class='acao centro'>
		 <a title=Excluir rel=$row[ptcioid] id=btn_excluir_instAssistencia href=javascript:void(0);>
		 <IMG class=icone alt=Excluir src=images/icon_error.png></a>
		<a title=Editar rel=$row[ptcioid] id=btn_editar_instAssistencia href=javascript:void(0);>
		<IMG class=icone alt=Editar src=images/icon_editar.gif></a>
		</td>
		</tr>";
		endforeach;
		$html .= "</tbody><tfoot><tr class='center'><td align='center' colspan='6'></td></tr></tfoot>";
	
		echo json_encode ( array ("html" => $html) );
	}
	
	
	public function getComponenteListaAnexosProposta($dados){
	
		if(count($dados) > 0 && !empty($dados) || $dados != null) {
		
		$html = "";
		$html .= "<table><thead><tr><th style='text-align: center';>Arquivo</th>
				<th style='text-align: center';>".utf8_encode(Descrição)."</th>
				<th style='text-align: center';>Data</th>
				<th style='text-align: center';>Usuario</th>
				<th style='text-align: center';>Acoes</th></tr></thead><tbody>";
	
		foreach ( $dados as $row ) :
		$class = $class == '' ? 'par' : '';

		
	
			
		$html .= "<tr class=" . $class . ">
		<input type=hidden id=idAnexo name=idAnexo value=".$row[ptaoid]." />
		<input type=hidden id=idpropAnexo name=idpropAnexo value=".$row[ptaptraoid]." />
		<td style='text-align: center';><a title=Downloads target=_blank href=download.php?arquivo="._SITEDIR_ ."faturamento/transferencia_titularidade/".utf8_encode($row['ptanm_arquivo'])." >".$row['ptanm_arquivo']."</a></td>
		<td style='text-align: center';>$row[ptadescricao]</td>
		<td style='text-align: center';>$row[data]</td>
		<td style='text-align: center';>$row[nm_usuario]</td>
		<td class='acao centro'>
		<a title=Excluir rel=$row[ptanm_arquivo] id=btn_excluir_arquivo href=javascript:void(0);>
		<IMG class=icone alt=Cancelar src=images/icon_error.png></a>
		</td>
		</tr>";
		endforeach;
	
		$html .= "</tbody><tfoot><tr class='center'><td align='center' colspan='5'></td></tr></tfoot>";

		$resultado = array ("html" => $html) ;

		echo json_encode ($resultado);
		}
	}
	
	public function getComponenteListaCarta($dados){
		if(count($dados) > 0 && !empty($dados) || $dados != null) {
		$html = "";
		$html .= "<table><thead><tr><th style='text-align: center';>Arquivo</th>
				<th style='text-align: center';>".utf8_encode(Descrição)."</th>
				<th style='text-align: center';>Data</th>
				<th style='text-align: center';>Usuario</th>
				<th style='text-align: center';>Acoes</th></tr></thead><tbody>";
		
		foreach ( $dados as $row ) :
		$class = $class == '' ? 'par' : '';
	
			
		$html .= "<tr class=" . $class . ">
		<input type=hidden id=idCarta name=idCarta value=".$row[ptaoid]." />
		<td style='text-align: center';><a title=Downloads target=_blank href=download.php?arquivo="._SITEDIR_ ."faturamento/transferencia_titularidade/".utf8_encode($row['ptanm_arquivo'])." >".$row['ptanm_arquivo']."</a></td>
		<td style='text-align: center';>$row[ptadescricao]</td>
		<td style='text-align: center';>$row[data]</td>
		<td style='text-align: center';>$row[nm_usuario]</td>
		<td class='acao centro'>
		<a title=Excluir rel=$row[ptanm_arquivo] id=btn_excluir_carta href=javascript:void(0);>
		<IMG class=icone alt=Cancelar src=images/icon_error.png></a>
		
		</td>
		</tr>";
		endforeach;
		
		$html .= "</tbody><tfoot><tr class='center'><td align='center' colspan='5'></td></tr></tfoot>";
		
		$resultado =  $html;
		
	
		return $resultado;
		}
	}
	
	
	public function getComponenteListaCidades($dados){

		$html = "";
		$resultado = "";
		
		$html .="<select name='prpend_cidade' id='prpend_cidade'>";
		$html .="<option value=''>--Escolha--</option>";
		foreach ($dados as $row) {
			
			$html .="<option value='$row[clcnome]'>$row[clcnome]</option>";
		
		}
		$html .="</select>";
		
		$resultado = array ("html" => utf8_encode($html)) ;

		
		echo json_encode ($resultado);
	}
	
	public function getComponenteListaBairros($dados){
		$html = "";
		$resultado = "";
		
		$html .="<select name='prpend_combobairro' id='prpend_combobairro'>";
		$html .="<option value=''>--Escolha--</option>";
		foreach ($dados as $row) {
				
			$html .="<option value='$row[cbanome]'>$row[cbanome]</option>";
		
		}
		$html .="</select>";
		
		$resultado = array ("html" => utf8_encode($html)) ;
		
		
		echo json_encode ($resultado);
	}
	
	public function getComponenteListaCidadesEnderecoCobranca($dados){
	
		$html = "";
		$resultado = "";
	
		$html .="<select name='prpendCob_cidade' id='prpendCob_cidade'>";
		$html .="<option value=''>--Escolha--</option>";
		foreach ($dados as $row) {
				
			$html .="<option value='$row[clcnome]'>$row[clcnome]</option>";
	
		}
		$html .="</select>";
	
		$resultado = array ("html" => utf8_encode($html)) ;
	
	
		echo json_encode ($resultado);
	}
	
	public function getComponenteListaBairrosEnderecoCobranca($dados){
		$html = "";
		$resultado = "";
	
		$html .="<select name='prpend_combobairrocobr' id='prpend_combobairrocobr'>";
		$html .="<option value=''>--Escolha--</option>";
		foreach ($dados as $row) {
	
			$html .="<option value='$row[cbanome]'>$row[cbanome]</option>";
	
		}
		$html .="</select>";
	
		$resultado = array ("html" => utf8_encode($html)) ;
	
	
		echo json_encode ($resultado);
	}
}
?>