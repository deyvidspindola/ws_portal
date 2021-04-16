

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">Data Instalação</th>
                    <th class="menor">Núm. Contrato</th>
                    <th class="menor">CPF/CNPJ</th>
                    <th class="maior">Nome do Cliente</th>
                    <th class="menor">Placa</th>
                    <th class="medio">Núm. Série Equipamento</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
								<td class="centro"><?php echo $resultado->data_instalacao ; ?></td>
                                <td class="direita"><?php echo $resultado->contrato; ?></td>
                                <td class="direita"><?php echo $resultado->cpf_cnpj; ?></td>
                                <td class="esquerda">
                                    <?php
                                        if(strlen($resultado->clinome) > 50) {
                                            echo wordwrap($resultado->clinome, 50, "<br>",true);
                                        } else {
                                            echo $resultado->clinome;
                                        }
                                    ?>
                                </td>
                                <td class="centro"><?php echo $resultado->placa; ?></td>
                                <td class="direita"><?php echo $resultado->serie; ?></td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="centro">
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
<?php if($totalRegistros > 1) { ?>
<div class="bloco_acoes">
     <button type="button" id="btn_gerar_arquivo" name="btn_gerar_arquivo">Gerar Arquivo</button>
</div>
<?php } ?>
