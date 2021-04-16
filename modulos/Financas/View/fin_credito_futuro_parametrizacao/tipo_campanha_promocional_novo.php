<div class="modulo_titulo">Tipo de Campanha Promocional</div>
<div class="modulo_conteudo">

    <?php echo $this->exibirMensagem(); ?>
    
    <div class="bloco_titulo">Dados Principais</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo maior">
                <label for="descricao">Descrição *</label>
                <input type="text" id="descricao" value="" name="descricao" class="campo" maxlength="80"  />
            </div>     

            <div class="clear"></div>

        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="cadastrarTipoCampanhaPromocional">Cadastrar</button>
        <button type="button" id="retornarTipoCampanhaPromocional">Retornar</button>
    </div>

    <div class="separador"></div>
</div>