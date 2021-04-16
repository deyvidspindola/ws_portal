
<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">Chave de Serviço</th>
                    <th class="medio">Tipo de Ponto</th>
					<th class="maior">Prestador de Serviço</th>
					<th class="menor">Duração Mínima (min)</th>
					<th class="menor">Duração Sugerida OFSC (min)</th>
					<th class="menor">Ação</th>
					<th class="menor">Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr id="linha_<?php echo $resultado->stmoid; ?>" class="<?php echo $classeLinha; ?>">
								<td class="centro"><?php echo $resultado->stmchave; ?></td>
								<td class=""><?php echo $resultado->stmponto_legenda; ?></td>
                                <td class=""><?php echo $resultado->repnome; ?></td>
								<td class="direita"><?php echo $resultado->stmtempo_minimo; ?></td>
								<td class="direita"><?php echo $resultado->stmtempo_ofsc; ?></td>
								<td class="centro">
                                    <img class="icone editar hand" data-stmoid="<?php echo $resultado->stmoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/edit.png" title="Editar">
                                    <img class="icone excluir hand" data-stmoid="<?php echo $resultado->stmoid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Excluir">
                                </td>
								<td class="centro">
                                     <?php if( count($resultado->dados_log) > 0 ) : ?>
                                        <div item-id="<?php echo $resultado->stmoid; ?>" class="bt_detalhes">
                                            <b>[</b>
                                                <img id="mais" class="mais_<?php echo $resultado->stmoid; ?> hand" valign="absmoddle" src="images/icones/maisTransparente.gif">
                                                <img id="menos" class="menos_<?php echo $resultado->stmoid; ?> hand invisivel" valign="absmoddle" src="images/icones/menosTransparente.gif">
                                            <b>]</b>
                                        </div>
                                    <?php endif; ?>
                                </td>
							</tr>
                            <?php if( count($resultado->dados_log) > 0 ) : ?>
                            <tr id="det_<?php echo $resultado->stmoid; ?>" class="detalhes">
                                <td colspan="7">
                                    <div class="lista-log">
                                        <table class="tabela-log">
                                            <thead>
                                                <tr class="par">
                                                    <th class="menor">Data / Hora</th>
                                                    <th class="Maior">Usuário</th>
                                                    <th class="menor">Alterado De:<br/>Duração Mínima (min)</th>
                                                    <th class="menor">Alterado Para:<br/>Duração Mínima (min)</th>
                                                </tr>
                                            </thead>
                                            <?php foreach ($resultado->dados_log as $valor) :?>
                                            <tr class="impar">
                                                <td class="centro"><?php echo $valor->stmldt_alteracao; ?></td>
                                                <td class=""><?php echo $valor->stmusuario; ?></td>
                                                <td class="direita"><?php echo $valor->stmltempo_original; ?></td>
                                                <td class="direita"><?php echo $valor->stmltempo_novo; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
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
    <?php echo $this->view->paginacao; ?>
</div>