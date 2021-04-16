<div class="separador"></div>
<div class="bloco_titulo">Motivos detalhados sem vínculo</div>
<div class="bloco_conteudo">
	<input type="hidden" name="exclusao" id="exclusao" />
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="centro">Motivo Detalhado</th>
                	<th class="acao">Ação</th>
            	</tr>
            </thead>
            <tbody>
            	<?php

            	if ($this->view->parametros->motivosDetalhadosSemVinculo): 

            		foreach ($this->view->parametros->motivosDetalhadosSemVinculo as $motivoDetlahado): 

            			$class = ($class == 'impar') ? 'par' : 'impar'; ?>
		                
		                <tr class="<?php echo $class ?>">
		                    <td><?php echo $motivoDetlahado->mdrdescricao ?></td>
			                <td class="acao centro">
			                	<a href="javascript:void(0)" title="Excluir" class="excluir" id-motivo="<?php echo $motivoDetlahado->mdroid ?>">
			                		<img alt="Cancelar" src="images/icon_error.png" class="icone">
			                	</a>
			               	</td>
		                </tr>
		        <?php 
		        	endforeach;
		        endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><?php echo count($this->view->parametros->motivosDetalhadosSemVinculo) ?> registros encontrados.</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>