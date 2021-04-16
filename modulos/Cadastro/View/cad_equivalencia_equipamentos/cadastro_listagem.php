<?php if (isset($this->dados) && !empty($this->dados)) : ?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        if(typeof jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip == 'function'){
            jQuery('[title]').not('a').not('.data img').not('.mes_ano img').tooltip({position: {my: 'left+5 center', at: 'right center'}});
        }         
    });
</script>
<form id="formExcluirProduto"  method="post" action="cad_tipo_segmentacao.php">
    <input name="acao" value="excluirProdutos" type="hidden">
    <input name="eeieeqoid" value="<?php echo isset($parametros->eeqoid) && $parametros->eeqoid != '' ? $parametros->eeqoid : "" ?>" type="hidden">
    <div id="mensagens_grid" class="mensagem invisivel"></div>    
    <div id="resultado_pesquisa">
        <div class="bloco_titulo">Equipamentos Equivalentes por Classe</div>
        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                        <?php if (isset($parametros->isAjax) && $parametros->isAjax = 1) :?>
                                <th class="centro" style="width: 200px !important;"><?php echo utf8_encode('Tipo do Produto'); ?></th>
                                <th class="centro" style="width: 450px !important;"><?php echo utf8_encode('Produto'); ?></th>
                                <th class="centro" ><?php echo utf8_encode('Versão'); ?></th>														
                                <th class="centro" style="width: 90px !important;"><?php echo utf8_encode('Data Inclusão'); ?></th>
                        <?php else: ?>
                                <th class="centro" style="width: 200px !important;">Tipo do Produto</th>
                                <th class="centro" style="width: 450px !important;">Produto</th>
                                <th class="centro" >Versão</th>														
                                <th class="centro" style="width: 90px !important;">Data Inclusão</th>	
                        <?php endif;?>
                                <th class="centro" ><input type="checkbox" id="marcar_todos" name="marcar_todos" title="Marcar Todos" /></th>	
                        </tr>
                    </thead>

                    <tbody id="conteudo_listagem">	
                        <?php foreach ($this->dados as $item) : ?>
                        <?php $class = $class == 'impar' ? 'par' : 'impar'; ?>
                        <tr class="<?php echo $class ?>">                    
                        <?php if (isset($parametros->isAjax) && $parametros->isAjax = 1) :?>
                            <td><?php echo utf8_encode($item->tipo); ?></td>
                            <td><?php echo utf8_encode($item->produto); ?></td>
                            <td><?php echo utf8_encode($item->versao); ?></td>
                            <td class="centro"><?php echo utf8_encode($item->data_inclusao); ?></td>

                        <?php else: ?>
                            <td><?php echo $item->tipo; ?></td>
                            <td><?php echo $item->produto; ?></td>
                            <td><?php echo $item->versao; ?></td>
                            <td class="centro"><?php echo $item->data_inclusao; ?></td>
                        <?php endif;?>
                        <td class="centro" >
                                <input type="checkbox" class="excluir_produto" name="excluir_produto[]" title="Excluir" value="<?php echo $item->id; ?>" />
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bloco_acoes">
            <button type="button" id="bt_excluir" disabled="disabled"  name="bt_excluir">Excluir</button>
        </div>
    </div>
</form>
<?php endif; ?>