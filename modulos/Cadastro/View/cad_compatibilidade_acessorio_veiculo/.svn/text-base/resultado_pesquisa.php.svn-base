		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
	<div class="conteudo">
		<fieldset>
			<legend>Legenda</legend>
			<ul class="legenda">
				<li><img alt="Item" src="images/apr_neutro.gif"> Aguardando Homologação</li>
				<li><img alt="Item" src="images/apr_bom.gif"> Compatível</li>
				<li><img alt="Item" src="images/apr_ruim.gif"> Incompatível</li>
			</ul>
		</fieldset>
	</div>
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="medio centro">Data cadastro</th>
					<th class="medio centro">Marca Veículo</th>
					<th class="maior centro">Modelo Veículo</th>
					<th class="centro">Ano</th>
					<th class="medio centro">Modelo Acessório</th>
					<th class="centro">Status</th>
					<?php if ($_SESSION['funcao']['manter_compatibilidade_cav'] == 1): ?>
					<th class="acao">Ação</th>
	                <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->view->dados) > 0): ?>
                <?php $classeLinha = "par"; ?>
            	<?php $tabindex = 7; ?>
                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
								<td class="centro"><?php echo date("d/m/Y", strtotime($resultado->cavdt_cadastro)); ?></td>
								<td class="centro"><?php echo $resultado->mcamarca; ?></td>
								<td class="centro"><?php echo $resultado->mlomodelo; ?></td>
								<td class="centro"><?php echo $resultado->cavano; ?></td>
								<td class="centro"><?php echo $resultado->cbmodescricao; ?></td>
								<td class="centro"><img alt="Item" src="images/<?php echo $resultado->cavstatus; ?>.gif"></td>
								<?php if ($_SESSION['funcao']['manter_compatibilidade_cav'] == 1): ?>
		                    	<td class="acao centro">
			                   		<a href="javascript:return false;" class="editar" title="Editar" cavoid="<?php echo $resultado->cavoid; ?>" >
			                   			<img alt="Editar" src="images/edit.png" class="icone" tabindex="<?php echo $tabindex++; ?>" />
			                   		</a>
			                   		<a href="javascript:return false;" class="excluir" title="Excluir" cavoid="<?php echo $resultado->cavoid; ?>" >
			                   			<img alt="Excluir" src="images/icon_error.png" class="icone" tabindex="<?php echo $tabindex++; ?>" />
			                   		</a>
	                    		</td>
	                    		<?php endif; ?>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>