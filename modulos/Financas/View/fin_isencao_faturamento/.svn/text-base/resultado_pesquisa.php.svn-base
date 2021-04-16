

<div class="separador"></div>
<div class="bloco_titulo">Legenda Status:</div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <tbody>
                <tr>
                   	<td>&nbsp;<img src="images/indicadores/quadrados/ap/ap01.jpg">&nbsp; Isentável </td>
                   	<td>&nbsp;<img src="images/indicadores/quadrados/ap/ap02.jpg">&nbsp; Isenção Programada </td>
                   	<td>&nbsp;<img src="images/indicadores/quadrados/ap/ap03.jpg">&nbsp; Em Isenção </td>
                   	<td>&nbsp;<img src="images/indicadores/quadrados/ap/ap04.jpg">&nbsp; Não Isentável (< 12 meses)</td>
                   	<td>&nbsp;<img src="images/indicadores/quadrados/ap/ap05.jpg">&nbsp; Não Isentável (Contrato ñ ativo)</td>
                   	<td>&nbsp;<img src="images/indicadores/quadrados/ap/ap13.jpg">&nbsp; Não Isentável (Eqpto. ñ instalado)</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>		


<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th><input class="selecao" type="checkbox" id="marcar_todos" name="marcar_todos" title="Marcar Todos" /></th>
                  	<th>Status</th>
                   	<th class="menor centro">Contrato</th>
                   	<th class="menor centro">Status Contrato</th>
                   	<th class="menor centro">Placa</th>
                   	<th class="medio centro">Cliente</th>
                   	<th class="medio centro">Período Isenção</th>
                   	<th class="menor centro">Status Equip.</th>
                   	<th class="menor centro">Vlr. Equipamento</th>
                   	<th class="menor centro">Vlr. Monitoramento</th>
                   	<th class="menor centro">Vlr. Acessórios</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php 
                        $classeLinha = ($classeLinha == "") ? "par" : ""; 
                        
                        $resultado->vlr_equip = (trim($resultado->vlr_equip) != "") ? trim($resultado->vlr_equip) : 0;
                        $resultado->vlr_monit = (trim($resultado->vlr_monit) != "") ? trim($resultado->vlr_monit) : 0;
                        $resultado->vlr_acess = (trim($resultado->vlr_acess) != "") ? trim($resultado->vlr_acess) : 0;
                        ?>
						<tr class="<?php echo $classeLinha; ?>">       
                        	<td>
                        		<?php
                        		// Exibe checkbox (Isentável, Isenção Programada) 
                        		if ($resultado->status== 'ap01' || $resultado->status== 'ap02') {
									?>
                        			<input type="checkbox" class="contrato" name="contrato[]" title="Selecionar" value="<?php echo $resultado->connumero."#".$resultado->parfoid."#".$resultado->veiplaca; ?>" />
									<?php 
								}
								// Não exibe checkbox (Em Isenção, Não Isentável (< 12 meses), Não Isentável (Contrato ñ ativo), Não Isentável (Eqpto. ñ instalado))
								else {
									?>
									&nbsp;
									<?php 
								}
                        		?>
                        	</td>
                    		<td class="centro">&nbsp;<img src="images/indicadores/quadrados/ap/<?php echo $resultado->status; ?>.jpg">&nbsp;</td>
                    		<td class="direita">
                    			<?php 
                    			if ($resultado->parfoid != "") {
                    				?>
                    				<a id="<?php echo $resultado->parfoid; ?>" href="#void" title="Visualizar" class="link" status="<?php echo $resultado->status; ?>" ><?php echo $resultado->connumero; ?></a>
                    				<?php 
                    			}
                    			else {
	                    			echo $resultado->connumero; 
                    			}
                    			?>
                    		</td>
                    		<td><?php echo $resultado->csidescricao; ?></td>
                    		<td><?php echo $resultado->veiplaca; ?></td>
                    		<td><?php echo $resultado->clinome; ?></td>
                    		<td class="centro"><?php echo $resultado->periodo; ?></td>
                    		<td class="direita"><?php echo $resultado->eqsdescricao; ?></td>
                    		<td class="direita"><?php echo number_format($resultado->vlr_equip, 2, ",", "."); ?></td>
                    		<td class="direita"><?php echo number_format($resultado->vlr_monit, 2, ",", "."); ?></td>
                    		<td class="direita"><?php echo number_format($resultado->vlr_acess, 2, ",", "."); ?></td>								
						</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="11" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr class="bloco_acoes">
                    <td colspan="11">
			    		<div>
			    			<button type="button" id="bt_atualizar">Atualizar</button>
			    			<button type="button" id="bt_excluir">Excluir</button>
    						<button type="button" id="bt_limpar2">Limpar</button>
			    		</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


</div> 
<div id="solicitar-paralisacao-form" style="display:none" title="Paralisa&ccedil;&atilde;o de Faturamento por Safra">
</div>