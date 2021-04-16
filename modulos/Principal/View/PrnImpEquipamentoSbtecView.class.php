<?php
/**
 * View
 * @author Bruno Bonfim Affonso [bruno.bonfim@sascar.com.br]
 * @package Principal
 * @version 1.0
 * @since 22/11/2012
 */
class PrnImpEquipamentoSbtecView{
	/**
	 * 
	 */
	public function __construct(){
		
	}
	
	/**
	 * Renderiza a tela inicial.
	 * @param array $dados
	 */
	public function index($dados){
		require _SITEDIR_.'modulos/Principal/View/prn_imp_equipamento_sbtec/index.view.php';
	}
	
	/**
	 * Renderiza a tela com que contem o GRID dos seriais
	 * carregados do arquivo (.csv)
	 *
	 * @param array $dados (Contem dados do(s) equipamento(s))
	 */
	public function visualizarGrid($dados=array()){
		if(!empty($dados)){
			$resultSet = array();
			
			$html = '
					<div class="bloco_titulo">Resultado do Processamento</div>
					<div class="bloco_conteudo">
					    <div class="listagem">
					        <table>
					            <thead>
					                <tr>
					                    <th style="text-align:center;">Serial</th>
					                    <th style="text-align:center;">Status</th>
					                    <th style="text-align:center;">Linha</th>
					                    <th style="text-align:center;">ESN</th>
					                    <th style="text-align:center;">Versão</th>
					                    <th style="text-align:center;">Projeto</th>
										<th style="text-align:center;">Classe</th>
										<th style="text-align:center;">Contrato</th>
										<th style="text-align:center;">&nbsp;</th>
					                </tr>
					            </thead>
					            <tbody>';					            
					                $i = 0;
					                foreach($dados as $row){
					                	$class  = !($i % 2) ? "par" : "";
					                	$status = array(3,10,19,20,24);
					                	
					                	if(in_array($row['eqsoid'], $status)){
					                		$disabled = '';
					                	} elseif($row['contrato'] == ''){
					                		$disabled = '';
					                	} else{
					                		$disabled = 'disabled';
					                	}
					                				                	
					                    $html .= "<tr class='$class'>
					                            <td>".$row['equno_serie']."</td>
					                            <td>".$row['eqsdescricao']."</td>
					                            <td>(".$row['equno_ddd'].") ".$row['equno_fone']."</td>
					                            <td>".$row['equesn']."</td>
					                            <td>".$row['eveversao']."</td>
					                            <td align='right'>".$row['eprnome']."</td>
			                            		<td align='right'>".$row['eqcdescricao']."</td>
	                            				<td align='right'>".$row['contrato']."</td>
                            					<td align='center'>
					                            	<input type='checkbox' id='equ_".$row['equoid']."' value='".$row['equoid']."' $disabled/>
					                            </td>
					                          </tr>";
					                    $i++;
					                }	            
			$html .= '          </tbody>
					            <tfoot>							
					                <tr>
					                    <td style="text-align:center;" colspan="9">'.$i.' registro(s)</td>                
					                </tr>					
					            </tfoot>
					        </table>
					    </div>
					</div>
					<div class="bloco_acoes">
						<button type="button" id="importar" onclick="importarEquipamento();">Importar Equipamento</button>
					</div>';
			
			unset($dados);
			$dados['msgsucesso'] = 'Arquivo importado com sucesso.';
			$dados['html'] = $html;
			$this->index($dados);						
		} else{
			$dados['msginfo'] = 'Não há registros para esses seriais.';
			$this->index($dados);
		}
	}
}
?>