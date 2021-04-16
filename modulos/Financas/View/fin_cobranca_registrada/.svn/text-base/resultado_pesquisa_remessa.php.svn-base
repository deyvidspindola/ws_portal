<div class="separador"></div>

<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr colspan="6">
					<th class="medio">Banco</th>
                    <th class="menor">Nº da Remessa</th>
                    <th class="menor">Dt Cadastro</th>
                    <th class="menor">Usuário</th>
                    <th class="menor">Tipo Evento</th>
					<th class="medio">Status</th>
					<th class="medio">Arquivo</th>
					<th class="menor">Ação</th>

                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados->resultadoPesquisaRemessa) > 0) {
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados->resultadoPesquisaRemessa as $resultado) {
                        $data = new DateTime($resultado->data_cadastro);

                        $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>" id="<?php echo $resultado->id_remessa; ?>">
								<td class="esquerda"><?php echo $resultado->nome_banco; ?></td>
                                <td class="direita"><?php echo $resultado->numero_remessa; ?></td>
                                <td class="centro"><?php echo  date_format($data, 'd/m/Y H:i:s'); ?></td>
                                <!-- STI 86972 -->
                                <td class="centro"><?php echo $resultado->nm_usuario; ?></td>
                                <!-- STI 86972 -->
                                <td class="centro"><?php echo $resultado->tipo_envio; ?></td>
								<td class="esquerda"><?php echo $resultado->status; ?></td>
								<td class="esquerda">
                                    <? if ($resultado->tipo_envio != 'AFT') { ?>
                                    <a href="download.php?arquivo=<?php echo $resultado->arquivo ?>" target="_blank">
                                        <?php echo substr(strrchr($resultado->arquivo, '/'), 1); ?>
                                    </a>
                                    <? } ?>
                                </td>
                                <td class="centro">
                                    <?php if($resultado->status !== 'Processada' && $resultado->status !== 'Enviado para cancelamento') { ?>
                                        <a class='bt_excluir excluir_listagem' href='javascript:void(0)'                                            
                                            <?
                                            if ($resultado->tipo_envio == 'AFT') { 
                                                echo "style='cursor: no-drop'";
                                            } 
                                            //STI 86972
                                            if ($resultado->tipo_envio != 'AFT') { 
                                                ?>
                                                onClick="excluirLinhaRemessa(this, <?php echo $resultado->numero_remessa.', '.$resultado->id_remessa; ?>)"
                                                <?php echo "title='Excluir'"; 
                                                } ?> >
                                            <img class='icone' id="<?php echo 'excluir_remessa_' . $resultado->id_remessa; ?>" alt='Excluir' src='images/icon_error.png'>
                                        </a>
                                    <?php } ?>
                                </td>
							</tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados->resultadoPesquisaRemessa);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
    </div>
</div>