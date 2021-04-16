<?php require_once 'header.php';?>

<div class="modulo_titulo">Cadastro de Operadora do Dia</div>
<div class="modulo_conteudo">

<div id="info_principal" class="mensagem info">Campos com * são obrigatórios.</div>
<?php echo $this->exibirMensagens(); ?>
<div class="bloco_titulo">Operadora do Dia</div>

    <form id="form_exemplo" method="POST" action="cad_operadora_dia.php">
        <input type="hidden" name="acao" id="acao" value="cadastrar" />
        <input type="hidden" name="opdoid" id="opdoid" value="<?php echo isset($this->parametros->opdoid) ? $this->parametros->opdoid : ''; ?>" />
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="opeoperadora">Operadora *</label>
                    <select name="opdopeoid" id="opdopeoid">
                        <option value="">Escolha</option>
                        <?php if (isset($this->comboOperadoras) && !empty($this->comboOperadoras)) : ?>
                            <?php foreach ($this->comboOperadoras as $option) : ?>
                                <option value="<?php echo $option->id;?>" <?php echo (isset($this->parametros->opdopeoid) && $this->parametros->opdopeoid == $option->id) ? ' selected="selected"' : ''; ?>><?php echo $option->operadora;?></option>
                            <?php endforeach; ?>
                        <?php endif ?>
                    </select>
                </div>

                <div class="clear"></div>

                <div class="campo data">
                    <label for="opddt_inivigencia">Período de Vigência *</label>
                    <input type="text" id="opddt_inivigencia" name="opddt_inivigencia" value="<?php echo isset($this->parametros->opddt_inivigencia) ? $this->parametros->opddt_inivigencia : date('d/m/Y');; ?>" class="campo" />
                    </div>
                    <p class="campo label-periodo">a</p>
                    <div class="campo data">
                    <label for="opddt_fimvigencia">&nbsp;</label>
                    <input type="text" id="opddt_fimvigencia" name="opddt_fimvigencia" value="<?php echo isset($this->parametros->opddt_fimvigencia) ? $this->parametros->opddt_fimvigencia : date('d/m/Y');; ?>" class="campo"/>
                 </div>

                <div class="clear"></div>
            </div>
        </div>
        <div class="bloco_acoes">
            <button type="submit" id="confirmar">Confirmar</button>
            <button type="button" id="voltar">Voltar</button>
        </div>

    </form>
</div>

<?php if (isset($this->bloqueio) && $this->bloqueio ) : ?>
<style>
    .none{
        display: none !important;
    }
</style>
<script>
    jQuery(document).ready(function(){
        jQuery('input,select, #confirmar ').attr('disabled','disabled');

        jQuery('.ui-datepicker-trigger').click(function(){
            jQuery('#ui-datepicker-div').addClass('none');
        });
    });
</script>
<?php endif; ?>

<div class="separador"></div>

<?php require 'lib/rodape.php'; ?>