<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<style>

.header {
    -moz-border-bottom-colors: none;
    -moz-border-image: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background: none repeat scroll 0 0 #E6EAEE;
    border-bottom: medium none !important;
    border-left: 1px solid #94ADC2;
    border-right: 1px solid #94ADC2;
    border-top: 1px solid #94ADC2;
    font-size: 12px;
    font-weight: bold;
    height: 25px;
    line-height: 25px;
    margin: 0 20px;
    padding: 0 10px;
    vertical-align: middle;
}

</style>
<div class="resultado bloco_conteudo" style="padding: 8px;">                            
    <table width="100%" cellpadding='5'>
    <?php
    foreach($this->view->dados as $id => $dados){ ?>
        
        <tr>
            <td>
                <center>
                    <table width="100%" style="border: 1px solid #94ADC2;">
                        <tr class="header">
                            <td colspan="2">
                                <label><b>Motivo Geral: <?=strtoupper($dados['descricao']);?></b></label>
                            </td>
                        </tr>
                        <tr>
                            <td width="15%" rowspan="<?php echo sizeof($dados['motivos'])+1; ?>"><label>Motivos Detalhados:</label></td>
                        </tr>
                        
                        <?php 
                        foreach($dados['motivos'] as $id2 => $motivos){ ?>
                            
                            <tr height="17">
                                <td width="85%">
                                    <label><?=ucwords(strtolower($motivos));?></label>
                                </td>
                            </tr>
                            
                            <?php
                        } ?>
                        
                    </table>
                </center>
            </td>
        </tr>
        <?php
    } ?>
    </table>
</div>