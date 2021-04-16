<table id="lista_clientes" width="98%" class="tableMoldura">
	<tbody>
    	<tr class="tableSubTitulo">
        	<td align="center">
        		<h2>Cliente(s) encontrado(s)</h2>
        	</td>
        </tr>
        <?php $linhas = count($resultado);?>
        <?php if ($linhas > 0 && $resultado != false): ?>
			<?php foreach($resultado as $cliente): ?>
			<?php $zebra = $zebra == 'tdc' ? 'tde' : 'tdc'; ?>
			<tr class="<?php echo $zebra; ?>">
				<td>
					<a class="link_nome_cliente" href="#" data-id="<?php echo $cliente['id'] ?>" data-nome="<?php echo $cliente['nome']?>" data-tipopessoa="<?php echo $cliente['tipo_pessoa']?>" data-cpfcnpj="<?php echo str_pad($cliente['cpf_cnpj'], ($cliente['tipo_pessoa']=='J'? 14 : 11), '0', STR_PAD_LEFT) ;?>"><?php echo $cliente['nome']?></a>
				</td>
			</tr>
			<?php endforeach;?>        
			<tr class="tableRodapeModelo1">
				<td align="center">
					<b><?php echo $linhas?> registro(s) encontrado(s).</b>
				</td>
			</tr>
        <?php else:?>
         <tr>
        	<td align="center">
        		&nbsp;
        	</td>
        </tr>
        <tr>
        	<td align="center">
        		<b>N&atilde;o foi encontrado nenhum cliente.</b>
        	</td>
        </tr>
         <tr>
        	<td align="center">
        		&nbsp;
        	</td>
        </tr>
        <?php endif;?>
    </tbody>
</table>