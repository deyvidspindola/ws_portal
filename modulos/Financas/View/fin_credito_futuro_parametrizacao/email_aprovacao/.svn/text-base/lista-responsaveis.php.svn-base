<div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th><?php echo utf8_encode("Tipo(s) de Motivo de Crédito"); ?></th>
                    <th class="centro">Excluir</th>
                </tr>
            </thead>
            <tbody id="conteudo_responsaveis">
                <?php if (is_array($parametros->responsaveis)): ?>
                    <?php foreach ($parametros->responsaveis as $data) : ?>                
                        <tr>
                            <td style="background:#BAD0E5"><?php echo utf8_encode($data['usuario']->nm_usuario) ?></td>
                            <td style="background:#BAD0E5"><?php echo utf8_encode($data['usuario']->usuemail) ?></td>
                            <td class="td-sublistagem">
                                <table class="sub-listagem">  
                                    <?php foreach ($data['motivo'] as $motivo) : ?>    
                                        <?php $class = $class == 'par' ? 'impar' : 'par'; ?>
                                        <tr class="<?php echo $class ?>" ><td><?php echo utf8_encode($motivo->motivo_credito); ?></td></tr>
                                    <?php endforeach;  ?>
                                </table>
                            </td>
                            <td style="background:#BAD0E5" class="centro">
                                <a class="excluir_reponsavel" data-usuario="<?php echo $data['usuario']->cd_usuario ?>" tabindex="2" id="excluir_reponsavel_<?php echo $data['usuario']->cferoid ?>" href="javascript:void(0);"> 
                                    <img src="images/icon_error.png" class="icone" />
                                </a>
                            </td>                   
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr style="background:#BAD0E5">
                        <td  ><?php echo utf8_encode($parametros->responsaveis->usuario->nm_usuario) ?></td>
                        <td ><?php echo utf8_encode($parametros->responsaveis->usuario->usuemail) ?></td>
                        <td class="td-sublistagem">
                            <table class="sub-listagem">
                                <?php foreach ($parametros->responsaveis->motivo as $motivo) : ?>
                                    <?php $class = $class == 'par' ? 'impar' : 'par'; ?>
                                    <tr class="<?php echo $class ?>" ><td><?php echo utf8_encode($motivo->motivo_credito); ?></td></tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                        <td  class="centro">
                            <a class="excluir_reponsavel" data-usuario="<?php echo $parametros->responsaveis->usuario->cd_usuario ?>"  id="excluir_reponsavel_<?php echo $parametros->responsaveis->usuario->cferoid ?>" href="javascript:void(0);"> 
                                <img src="images/icon_error.png" class="icone" />
                            </a>
                        </td>                   
                    </tr>
                <?php endif; ?>
            <tbody>
        </table>
    </div>