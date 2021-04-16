<div class="resultado bloco_titulo">Acessórios Para Instalação Cadastrados</div>
<div class="resultado bloco_conteudo">
        <div class="listagem" id="bloco_itens">
        <table>
            <thead>
                <tr>
                    <th class="maior">Acessório</th>
                    <th class="menor">Ano Inicial</th>
                    <th class="menor">Ano Final</th>
                    <th class="menor">Cliente</th>
                    <th class="menor">Segurado</th>
                    <th class="menor">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) == 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->dados_itens as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                            <tr data-acessorio="<?php echo $resultado->mlaiobroid; ?>" class="<?php echo $classeLinha; ?>">
                                <td class="esquerda">
                                    <?php echo $resultado->obrobrigacao; ?>
                                    <input id="mlaioid" name="mlaioid[]" type="hidden" value="<?php echo $resultado->mlaioid; ?>">
                                    <input id="mlaiobroid" name="mlaiobroid[]" type="hidden" value="<?php echo $resultado->mlaiobroid; ?>">
                                </td>
                                <td class="direita">
                                    <?php echo $resultado->mlaiano_inicial; ?>
                                    <input id="mlaiano_inicial" name="mlaiano_inicial[]" type="hidden" value="<?php echo $resultado->mlaiano_inicial; ?>">
                                </td>
                                <td class="direita">
                                     <?php echo $resultado->mlaiano_final; ?>
                                     <input id="mlaiano_final" name="mlaiano_final[]" type="hidden" value="<?php echo $resultado->mlaiano_final; ?>">
                                </td>
                                 <td class="centro">
                                     <?php if($resultado->mlaiinstala_cliente == 't'): ;?>
                                         <img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icones/t1/v.png" title="Sim">
                                    <?php endif; ?>
                                    <input id="mlaiinstala_cliente" name="mlaiinstala_cliente[]" type="hidden" value="<?php echo $resultado->mlaiinstala_cliente; ?>">
                                </td>
                                 <td class="centro">
                                     <?php if($resultado->mlaiinstala_seguradora == 't'): ;?>
                                         <img class="icone" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icones/t1/v.png" title="Sim">
                                    <?php endif; ?>
                                    <input id="mlaiinstala_seguradora" name="mlaiinstala_seguradora[]" type="hidden" value="<?php echo $resultado->mlaiinstala_seguradora; ?>">
                                </td>
                                <td class="centro">
                                    <img class="icone remover hand" data-mlaioid="<?php echo $resultado->mlaioid; ?>" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>images/icon_error.png" title="Excluir">
                                </td>
                            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
</div>
<div class="separador"></div>