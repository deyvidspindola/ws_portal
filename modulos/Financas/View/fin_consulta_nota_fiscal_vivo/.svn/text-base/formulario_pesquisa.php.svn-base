
<style>
#ajuste_periodo{
    width: 280px;
}
#ajuste_cpfcnpj input{
    width: 280px !important;
}
.help{
    height: 16px !important;
    margin-top: 4px !important;
    width: 16px !important;
    cursor: pointer;
}
</style>

<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

         <fieldset class="maior opcoes-inline" id="ajuste_tipo_periodo">
            <legend id="ldg_tipo_periodo">Tipo Período *</legend>
            <input type="radio" id="selecao_geracao_faturamento" name="tipo_periodo"
                   value="geracao_faturamento"
                   <?php echo $this->view->parametros->tipo_periodo == 'geracao_faturamento' ? 'checked="checked"' : ($this->view->parametros->tipo_periodo == 'retorno_status' ? '' : 'checked="checked"'); ?>/>
            <label id="lbl_selecao_geracao_faturamento" for="selecao_geracao_faturamento">Geração Faturamento</label>
            <input type="radio" id="selecao_retorno_status" name="tipo_periodo"
                   value="retorno_status"
                   <?php echo $this->view->parametros->tipo_periodo == 'retorno_status' ? 'checked="checked"' : '' ?>/>
            <label id="lbl_selecao_retorno_status" for="selecao_retorno_status">Retorno Status VIVO</label>
        </fieldset>

        <div class="campo data periodo" id="ajuste_periodo">
            <div class="inicial">
                <label for="dt_evento_de">Período *</label>
                <input type="text" id="dt_evento_de" name="dt_evento_de" value="<?php echo $this->view->parametros->dt_evento_de ?>" class="campo" />
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="dt_evento_ate">&nbsp;</label>
                <input type="text" id="dt_evento_ate" name="dt_evento_ate" value="<?php echo $this->view->parametros->dt_evento_ate ?>" class="campo" />
            </div>
        </div>
         <div class="clear"></div>

        <!-- STATUS VIVO -->
        <div class="campo maior">
            <label id="lbl_status_vivo" for="status_vivo">Status VIVO</label>
            <select id="status_vivo" name="status_vivo[]" multiple="multiple">
                <option <?php echo is_array($this->view->parametros->status_vivo) && in_array('-1', $this->view->parametros->status_vivo) ? 'selected="selected"' : '' ?> value="-1">Escolha</option>
                <?php
                if (isset($this->view->parametros->buscarEventoStatus) && count($this->view->parametros->buscarEventoStatus) > 0) {
                    foreach ($this->view->parametros->buscarEventoStatus as $item) {
                        ?>
                        <option <?php echo is_array($this->view->parametros->status_vivo) && in_array($item->vpescodigo, $this->view->parametros->status_vivo) ? 'selected="selected"' : '' ?> value="<?php echo $item->vpescodigo ?>"><?php echo utf8_decode($item->vpesstatus) ?></option>
                        <?php
                    }
                } ?>
            </select>
        </div>

        <!-- STATUS SASCAR -->
        <div class="campo maior">
            <label id="lbl_status_sascar" for="status_sascar">Status SASCAR</label>
            <select id="status_sascar" name="status_sascar">
                <option <?php echo ($this->view->parametros->status_sascar == '') ? 'selected="selected"' : '' ?> value="">Escolha</option>
                <option <?php echo ($this->view->parametros->status_sascar == 'A') ? 'selected="selected"' : '' ?> value="A">À Vencer</option>
                <option <?php echo ($this->view->parametros->status_sascar == 'C') ? 'selected="selected"' : '' ?> value="C">Cancelada</option>
                <option <?php echo ($this->view->parametros->status_sascar == 'P') ? 'selected="selected"' : '' ?> value="P">Pago</option>
                <option <?php echo ($this->view->parametros->status_sascar == 'V') ? 'selected="selected"' : '' ?> value="V">Vencido</option>
            </select>
        </div>

        <div class="clear"></div>

        <!-- CLIENTE -->
        <div class="campo maior">
            <label id="lbl_cliente" for="cliente" >Cliente</label>
            <input type="text" id="cliente" maxlength="50" name="cliente" value="<?php echo trim($this->view->parametros->cliente); ?>" class="campo cliente limpar_campos" />
        </div>

        <!-- CPF / CNPJ -->
        <div class="campo medio" id="ajuste_cpfcnpj">
            <label id="lbl_cpfcnpj" for="cpfcnpj" >CPF/ CNPJ</label>
            <input type="text" id="cpfcnpj" maxlength="14" name="cpfcnpj" value="<?php echo trim($this->view->parametros->cpfcnpj); ?>" class="campo cpfcnpj limpar_campos" />
        </div>

        <div class="clear"></div>

        <!-- SELEÇÃO POR -->
        <fieldset class="maior opcoes-inline" id="ajuste_selecao_por">
            <legend id="ldg_selecao_por">Seleção por *</legend>
            <input type="radio" id="selecao_por_nota" name="selecao_por" value="N" <?php echo trim($this->view->parametros->selecao_por) != '' && $this->view->parametros->selecao_por == 'N' ? 'checked="checked"' : ($this->view->parametros->selecao_por != 'P') ? 'checked="checked"' : ''  ?> />
            <label id="lbl_selecao_por_nota" for="selecao_por_nota">Nota Fiscal</label>
            <input type="radio" id="selecao_por_placa" name="selecao_por" value="P" <?php echo trim($this->view->parametros->selecao_por) != '' && $this->view->parametros->selecao_por == 'P' ? 'checked="checked"' : '' ?>  />
            <label id="lbl_selecao_por_placa" for="selecao_por_placa">Placa</label>
        </fieldset>

        <!-- NOTA FISCAL -->
        <div class="campo menor">
            <label id="lbl_nota_fiscal" for="nota_fiscal" >Nota Fiscal</label>
            <input type="text" id="nota_fiscal" maxlength="9" name="nota_fiscal" value="<?php echo trim($this->view->parametros->nota_fiscal); ?>" class="campo nota_fiscal limpar_campos" />
        </div>

        <!-- SÉRIE -->
        <div class="campo menor">
            <label id="lbl_serie" for="serie">Série</label>
            <select id="serie" name="serie">
                <option value="">Escolha</option>
<?php
if (isset($this->view->parametros->buscarSerieNotaFiscal) && count($this->view->parametros->buscarSerieNotaFiscal) > 0) {
    foreach ($this->view->parametros->buscarSerieNotaFiscal as $item) {
        if (strtoupper(trim($this->view->parametros->serie)) == strtoupper(trim((string)$item->nfsserie))) {
                ?>
                <option selected="selected" value="<?php echo $item->nfsserie ?>"><?php echo $item->nfsserie ?></option>
                <?php
        } else {
                ?>
                <option value="<?php echo $item->nfsserie ?>"><?php echo $item->nfsserie ?></option>
                <?php
        }
    }
} ?>
            </select>
        </div>
        <!-- PLACA -->
        <div class="campo menor">
            <div id="div_placa" <?php echo (strtoupper(trim($this->view->parametros->selecao_por)) == 'P') ? 'class="visivel"' : 'class="invisivel"'; ?>>
                <label id="lbl_placa" for="placa">Placa</label>
                <input type="text" id="placa" maxlength="8" name="placa" value="<?php echo trim($this->view->parametros->placa); ?>" class="campo placa limpar_campos" />
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button id="btn_pesquisar" type="submit">Pesquisar</button>
</div>
