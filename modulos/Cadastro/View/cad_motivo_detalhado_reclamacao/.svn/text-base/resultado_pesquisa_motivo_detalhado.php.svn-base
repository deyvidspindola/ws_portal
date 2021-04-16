<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
<div class="separador"></div>

	<div class="resultado bloco_titulo filtroPesquisado"></div>
	<div class="bloco_conteudo">
	<div class="separador"></div>
	
		<div class="resultado bloco_titulo">Motivos Gerais:</div>
		<input type="hidden" name="id_motivo_geral" id="id_motivo_geral" value="" />
		<input type="hidden" name="id_detalhamento_motivo" id="id_detalhamento_motivo" value="" />
		<input type="hidden" name="marcados" id="marcados" value="" />
		<input type="hidden" name="desmarcados" id="desmarcados" value="" />
		<input type="hidden" name="vinculados" id="vinculados" value="<?php echo $this->view->idVinculados ?>" />
		<input type="hidden" name="tipo_pesquisa" id="tipo_pesquisa" value="<?php echo ($this->view->parametros->motivo_geral) ? 'motivo_geral' : 'motivo_detalhado'; ?>" />
		
		<div class="resultado bloco_conteudo">
		    <style>
		        tr:hover{
		            background: none !important;
		        }
		    </style>
		
		    <div class="listagem">
		        <table>
		            <tbody>
		                <?php
		                if (count($this->view->dados) > 0): ?>
		
		                    <?php foreach ($this->view->dados as $resultado) : ?>
		
		                            <?php $classeLinha = ($classeLinha == "") ? "par" : "";
		                       
		                            // Separando por grupos
		                            $primeiraLetra = strtoupper(substr($resultado->mtrdescricao,0,1));
		                          
		                            if($primeiraLetra != $primeiraLetraAnterior){
		                                                    
		                                $primeiraLetraAnterior = strtoupper(substr($resultado->mtrdescricao,0,1));
		                                
		                                if($j == 0){
		                                    echo "</tr>";
		                                }
		                                
		                                if($j == 1){
		                                    $j = 0;    
		                                } ?>
		
		                                <tr>
		                                    <td colspan="3" style="background: none repeat scroll 0 0 #BAD0E5;" align="center"><b><?=$primeiraLetra;?></b></td>
		                                </tr>
		                                
		                                <?php
		                                $i = 0;
		                                
		                            }
		                            
		                            if($i == 0){
		                                echo "<tr>";
		                            } ?>
		                            
		                            <td>
		                                <input class="checkResultado checkbox_<?php echo $resultado->mtroid?>" id="<?php echo $resultado->mtroid?>" type="checkbox" value="" name="checkbox_<?php echo $resultado->mtroid?>" <?php echo ($resultado->mrmdstatus == 't') ? 'checked' : '' ?>>
		                                <?=ucwords(strtolower(($resultado->mtrdescricao)));?>
		                            </td>
		                            
		                            <?php
		                            $i++;
		
		                            if($i == 3){
		                                echo "</tr>";
		                                $i = 0;
		                            }
		
		                        endforeach; ?>
		                <?php endif; ?>
		            </tbody>
		            <tfoot>
		                <tr>
		                    <td colspan="6" class="centro">
		                      <button type="submit" id="bt_atualizar">Atualizar</button>
		                </tr>
		            </tfoot>
		        </table>
		    </div>
		
		</div>
		<div class="separador"></div>
	</div>
	<div class="separador"></div>
</div>