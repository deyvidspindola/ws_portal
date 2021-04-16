<?php include 'header_view.php';?>

<div class="modulo_titulo">Política de Descontos</div>      

<div class="modulo_conteudo">

    <?php 
        // Mensagens de alerta
        if(!empty($response)) {
            echo '<div class="' . $response['class'] . '">'; 
            echo $response['message'];
            echo "</div>";
        }
    ?>          
           
    <div class="bloco_titulo">Editar Política de Desconto</div>
    
    <form method="post" action="man_politica_desconto.php">

        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="podoid" value="<?=$politicaDesconto['podoid']?>">

        <div class="bloco_conteudo">
            
            <div class="formulario">
                
                <div class="campo medio">
                    <label for="poddescricao_atraso">Atraso</label>
                    <input readonly disabled type="text" id="poddescricao_atraso" name="poddescricao_atraso" class="campo" value="<?=$politicaDesconto['poddescricao_atraso']?>"/>
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="podvlr_desconto">Desconto *</label>
                    <input type="text" id="podvlr_desconto" name="podvlr_desconto" class="campo" maxlength="6" value="<?=$politicaDesconto['podvlr_desconto']?>"/>
                </div> 

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="podaplicacao">Aplicação *</label>
                    <select id="podaplicacao" name="podaplicacao">
                        <?php foreach($aplicacaoList as $index => $aplicacao) : ?>
                            <?php 
                                $politicaDesconto['podaplicacao'] == $aplicacao ? $selected = "selected" : $selected = '';
                            ?>
                            <option <?=$selected?> value="<?=$index?>">
                                <?=$aplicacao?>
                            </option>
                        <?php endforeach;?> 
                    </select>
                </div> 

                <div class="clear"></div>
                <div class="separador"></div>

            </div>

        </div>

        <div class="bloco_acoes">
            <button type="submit" name="submit">
                Confirmar
            </button>
 
            <button type="button" onclick="window.location.href='man_politica_desconto.php'">
                Voltar
            </button>
          
        </div>

    </form>

</div>  
<div class="separador"></div>

<script>
    $(function() {
        $('#podvlr_desconto').maskMoney({allowZero:true, decimal:','});
    });
</script>