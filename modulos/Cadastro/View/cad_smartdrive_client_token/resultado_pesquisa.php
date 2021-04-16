<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Cliente</th>
					<th class="menor">Token</th>
					<th class="menor">Site Name</th>
					<th class="menor">Dt Efetivação</th>
					<th class="menor">Dt Expiração</th>
					<th class="menor">Ação</th>

                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        	<tr id="linha_<?php echo  $resultado->id; ?>" class="<?php echo $classeLinha; ?>">
								<td class="esquerda"><?php echo $resultado->cliente; ?></td>
								<td class="esquerda"><?php echo wordwrap($resultado->token,30,"<br />", true); ?></td>
								<td class="esquerda"><?php echo $resultado->siteName; ?></td>
								<td class="centro"><?php echo $resultado->dataEfetivacao; ?></td>
								<td class="centro"><?php echo $resultado->dataExpiracao; ?></td>
								<td class="centro">
									<img alt="Editar" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/edit.png" class="icone editar" data-tokenid="<?php echo $resultado->id; ?>">
									<img alt="Excluir" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" class="icone excluir" data-tokenid="<?php echo $resultado->id; ?>">
								</td>	
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ' ';
                        //echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>