
<?php 
use module\Parametro\ParametroIntegracaoTotvs;

//echo "<pre>";var_dump($this->view->parametros);echo "</pre>"; 
?>

<div class="bloco_titulo">Empresa</div>
<div class="bloco_conteudo">
    <div class="formulario">            
        <div class="campo maior">
            <select class="campo empresa" id="tecoid" name="tecoid" disabled='disabled'>
                <option value="">Selecione</option>
                <?php foreach ($this->view->empresas as $empresaId => $empresa) :?>
                <option value="<?php echo $empresa->tecoid; ?>" <?php echo ($this->view->filtros->tecoid == $empresa->tecoid ? 'selected="selected"' : "" );?> 
                    <?=(($empresa->tecoid == $this->view->parametros->apgtecoid) ? 'selected = "selected"' : ''); ?> >
                    <?php echo $empresa->tecrazao; ?>
                </option>
                <?php endforeach;?>
            </select>
            <input type="hidden" name="limpaTecoid" id="limpaTecoid" value="" />
        </div>
        <div class="clear"></div>
    </div>
</div>

<div class="separador"></div>

<div class="bloco_titulo">Dados da Fatura</div>

<div class="bloco_conteudo">
    <div class="formulario">        
        
        <div class="clear"></div>           
        <div class="campo">
            <label for="fordocto">Docto. Fornec:</label>          
            <input type="text" name="fordocto" id="fordocto" class="campo somenteNumero" value="<?=((isset($this->view->parametros->fordocto)) ? $this->view->parametros->fordocto : '') ?>" style="width: 135px;" />
        </div>        
        <div class="campo maior">
            <label id="lbl_fornecedor" for="cmp_cliente">Fornecedor <img class="btn-help" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'<strong>Mínimo 03 letras</strong> para a auto pesquisa de fornecedores.','D' , '');"></label>
            <input id="cmp_fornecedor_autocomplete" class="campo" type="text" placeholder="Informe o fornecedor" value="<?=((isset($this->view->parametros->forfornecedor)) ? $this->view->parametros->forfornecedor : '') ?>" name="cmp_fornecedor_autocomplete">
            <input id="cmp_fornecedor" type="hidden" value="<?php echo $this->view->parametros->foroid; ?>" class="validar" name="cmp_fornecedor">
        </div>
        <div class="campo menor" >
            <label for="apgno_notafiscal">N.F. (Documento):</label>          
            <input type="text" name="apgno_notafiscal" id="apgno_notafiscal" class="campo" disabled="disabled" value="<?=$this->view->parametros->apgno_notafiscal?>" />
        </div>
        <div class="campo maior">
            <label id="apgtnfoid" for="apgtnfoid">Tipo Documento:*</label>
            <select id="apgtnfoid" name="apgtnfoid" disabled="disabled">
                <option value="">Escolha</option>
                <?php                    
                    foreach($this->view->tiposDocumentos as $tipoID => $tipoDocumento) :?>
                        <option value="<?php echo $tipoDocumento->tnfoid; ?>" <?php echo (($this->view->parametros->apgtnfoid == $tipoDocumento->tnfoid) ? 'selected="selected"' : "" );?> ><?php echo $tipoDocumento->tnfdescricao; ?></option>
                <?php endforeach;?>                    
            </select>
        </div>                    

        <div class="clear"></div>
        <div class="campo">
            <label for="cntoidbusca">Cód. Centro de Custos:</label>          
            <input type="text" name="cntoidbusca" id="cntoidbusca" class="campo" value="<?=((isset($this->view->parametros->apgcntoid)) ? $this->view->parametros->apgcntoid : '') ?>"  disabled="disabled" style="width: 135px;" />
        </div>        
        <div class="campo maior">
            <label for="centro_custo">Centro de Custos:</label>          
            <input type="text" name="centro_custo" id="centro_custo" class="campo" value="<?=((isset($this->view->parametros->cntconta)) ? $this->view->parametros->cntno_centro ." - ". $this->view->parametros->cntconta : '') ?>" disabled="disabled" />
        </div>
        <div class="campo maior">
            <label id="apgplcoid" for="apgplcoid">Conta Contábil:</label>
            <select id="apgplcoid" name="apgplcoid">
                <option value="">Escolha</option>
                <?php                    
                    foreach($this->view->planoContabil as $tipoID => $planoContabil) :?>
                        <option value="<?=$planoContabil->plcoid; ?>" <?=($this->view->parametros->apgplcoid == $planoContabil->plcoid ? 'selected="selected"' : "" );?> ><?=$planoContabil->plcconta . " - ". $planoContabil->plcdescricao; ?></option>
                <?php endforeach;?>               
            </select>
        </div>

        <div class="clear"></div>
        <div class="campo">
            <label id="apgtipo_gasto" for="apgtipo_gasto">Tipo de Gasto:</label>
            <select id="apgtipo_gasto" name="apgtipo_gasto" disabled="disabled" style="width: 135px;">                
                <option value="">Escolha</option>
                <option value="C" <?=(($this->view->parametros->apgtipo_gasto == "C") ? 'select="selected"' : '')?>>Custos</option>
                <option value="D" <?=(($this->view->parametros->apgtipo_gasto == "D") ? 'select="selected"' : '')?>>Despesas</option>
                <option value="R" <?=(($this->view->parametros->apgtipo_gasto == "R") ? 'select="selected"' : '')?>>Resultado</option>
                <option value="T" <?=(($this->view->parametros->apgtipo_gasto == "T") ? 'select="selected"' : '')?>>Rateio</option>
            </select>
        </div>
        <div class="campo maior">
            <label id="apgtctoid" for="apgtctoid">Tipo de Conta a Pagar:</label>
            <select id="apgtctoid" name="apgtctoid" disabled="disabled">
                <option value="">Escolha</option>
                <?php                    
                    foreach($this->view->tiposContasPagar as $tipoID => $tiposContasPagar) :?>
                        <option value="<?php echo $tiposContasPagar->tctoid; ?>" <?php echo ($this->view->parametros->apgtctoid == $tiposContasPagar->tctoid ? 'selected="selected"' : "" );?> ><?php echo $tiposContasPagar->tctdescricao; ?></option>
                <?php endforeach;?>                
            </select>
        </div>
        <div class="campo" >
            <label for="apgmbcooid">Código de Movimentação Bancária:</label>          
            <input type="text" name="apgmbcooid" id="apgmbcooid"  class="campo" value="<?=((isset($this->view->parametros->apgmbcooid)) ? $this->view->parametros->apgmbcooid : '') ?>" style='width: 210px;' disabled="disabled" />
        </div>
        <div class="campo" >
            <label for="apgautorizado">Autorizado:</label>
            <?=($this->view->parametros->apgautorizado == "1" || $this->view->parametros->apgautorizado == "t" ? "Sim" : "Não")?>
        </div>
        <div class="campo" >
            <label for="apgprevisao">Previsão:</label>            
            <?=($this->view->parametros->apgprevisao == "1" || $this->view->parametros->apgprevisao == "t" ? "Sim" : "Não")?>
            <input type="hidden" name="apgprevisao" id="apgprevisao" class="campo" value="<?=($this->view->parametros->apgprevisao == "1" || $this->view->parametros->apgprevisao == "t" ? "t" : "")?>" />            
        </div>

        <div class="clear"></div>        
        <div class="campo" >
            <div class="inicial">
                <label for="apgdt_entrada">Data de entrada* </label>
                <input type="text" id="apgdt_entrada" name="apgdt_entrada" class="campo" disabled="disabled" value="<?=((isset($this->view->parametros->dt_entrada)) ? $this->view->parametros->dt_entrada : '') ?>" style="width: 135px;" />
            </div>
        </div>
        <div class="campo" >
            <div class="inicial">
                <label for="apgdt_pagamento">Data de pagamento* </label>

                <? // [START][ORGMKTOTVS-1184] - Leandro Corso ?>
                <input 
                type="text" 
                id="apgdt_pagamento" 
                name="apgdt_pagamento" 
                class="campo" 
                value="<?=((isset($this->view->parametros->apgdt_pagamento2)) 
                        ? $this->view->parametros->apgdt_pagamento2 
                        : $this->view->parametros->dt_vencimento ) ?>"
                    <? if (INTEGRACAO) echo 'disabled readonly'; ?>
                    />
                <? // [END][ORGMKTOTVS-1184] - Leandro Corso ?>

            </div>
        </div>
        <div class="campo" >
            <div class="inicial">
                <label for="apgdt_vencimento">Vencimento:* </label>

                <? // [START][ORGMKTOTVS-1184] - Leandro Corso ?>
                <input 
                    type="text" 
                    id="apgdt_vencimento" 
                    name="apgdt_vencimento" 
                    class="campo" 
                    value="<?=$this->view->parametros->dt_vencimento?>"
                    <? if (INTEGRACAO) echo 'disabled readonly'; ?>
                />
                <? // [END][ORGMKTOTVS-1184] - Leandro Corso ?>

            </div>
        </div>
                        
        <div class="clear"></div>

<div class="campo medio">
    <label id="apgforma_recebimento1" for="apgforma_recebimento1">Forma de Pagamento:</label>
            
            <? // [START][ORGMKTOTVS-1184] - Leandro Corso ?>
            <select 
                id="apgforma_recebimento" 
                name="apgforma_recebimento"
                <? if (INTEGRACAO) echo 'disabled readonly' ?>
            >
            <? // [END][ORGMKTOTVS-1184] - Leandro Corso ?>

                <option value=""   <?=(($this->view->parametros->apgforma_recebimento =='' ) ? 'selected = "selected"' : '');?> >Escolha</option>
                <option value="31" <?=(($this->view->parametros->apgforma_recebimento == 31) ? 'selected = "selected"' : '');?> >Boleto</option>
                <option value="1"  <?=(($this->view->parametros->apgforma_recebimento == 1 ) ? 'selected = "selected"' : '');?> >Crédito em C/C</option>
                <option value="2"  <?=(($this->view->parametros->apgforma_recebimento == 2 ) ? 'selected = "selected"' : '');?> >Crédito Conta / Salário</option>
                <option value="0"  <?=(($this->view->parametros->apgforma_recebimento == 0 && is_numeric($this->view->parametros->apgforma_recebimento)) ? 'selected = "selected"' : '');?> >Cheque</option>
                <option value="4"  <?=(($this->view->parametros->apgforma_recebimento == 4 ) ? 'selected = "selected"' : '');?> >Dinheiro</option>
            </select>
        </div>
        <div class="campo menor campoCheque">
            <label for="apgno_cheque campoCheque">No. Cheque:</label>
            <input type="text" name="apgno_cheque" id="apgno_cheque" class="campo campoCheque" value="<?=$this->view->parametros->apgno_cheque?>" />
        </div>
        
        <div class="campo medio">
            <label id="apgtipo_docto1" for="apgtipo_docto1">Tipo de Pagamento:</label>
            <select id="apgtipo_docto" name="apgtipo_docto">
                <option value="">Escolha</option>                                
                <option value="11" <?=(($this->view->parametros->apgtipo_docto=="11") ? 'selected="selected"' : '') ?> >Concessionária</option>
                <option value="06" <?=(($this->view->parametros->apgtipo_docto=="06") ? 'selected="selected"' : '') ?> >DARF Normal</option>
                <option value="12" <?=(($this->view->parametros->apgtipo_docto=="12") ? 'selected="selected"' : '') ?> >DARF Simples</option>
                <option value="04" <?=(($this->view->parametros->apgtipo_docto=="04") ? 'selected="selected"' : '') ?> >Duplicata</option>
                <option value="02" <?=(($this->view->parametros->apgtipo_docto=="02") ? 'selected="selected"' : '') ?> >Fatura</option>
                <option value="09" <?=(($this->view->parametros->apgtipo_docto=="09") ? 'selected="selected"' : '') ?> >FGTS</option>
                <option value="13" <?=(($this->view->parametros->apgtipo_docto=="13") ? 'selected="selected"' : '') ?> >GARE – SP ICMS</option>
                <option value="10" <?=(($this->view->parametros->apgtipo_docto=="10") ? 'selected="selected"' : '') ?> >GNRE</option>
                <option value="07" <?=(($this->view->parametros->apgtipo_docto=="07") ? 'selected="selected"' : '') ?> >GPS</option>
                <option value="08" <?=(($this->view->parametros->apgtipo_docto=="08") ? 'selected="selected"' : '') ?> >Guia Recolhimento</option>
                <option value="03" <?=(($this->view->parametros->apgtipo_docto=="03") ? 'selected="selected"' : '') ?> >Nota Fiscal</option>
                <option value="01" <?=(($this->view->parametros->apgtipo_docto=="01") ? 'selected="selected"' : '') ?> >Nota Fiscal/Fatura</option>
                <option value="05" <?=(($this->view->parametros->apgtipo_docto=="05") ? 'selected="selected"' : '') ?> >Outros</option>
            </select>
        </div>
        <input type="hidden" name="apgtipo_docto_hidden" id="apgtipo_docto1_hidden" value="<?=$this->view->parametros->apgtipo_docto?>" />
        
        <?
        // [START][ORGMKTOTVS-1184] - Leandro Corso
        if (INTEGRACAO) echo '<div class="clear"></div>' . ParametroIntegracaoTotvs::message(array('Os campos "data de pagamento"', '"vencimento"', '"forma de pagamento"'));
        // [END][ORGMKTOTVS-1184] - Leandro Corso
        ?>

        <div class="campo medio oculta_apgvalor_entidades">
            <label for="apgvalor_entidades">Valor Outras Entidades:</label>        
            <input type="text" name="apgvalor_entidades" id="apgvalor_entidades" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvalor_entidades2, 2, ",", ".")?>" onblur="calculaValor_liquido_total();" />
        </div>
        <div class="campo oculta_apginscricao_estadual" style="width: 120px;" >
            <label for="apginscricao_estadual">Inscrição Estadual:</label>
            <input type="text" name="apginscricao_estadual" id="apginscricao_estadual" maxlength="12" class="campo somenteNumero" value="<?=$this->view->parametros->apginscricao_estadual?>" style="width: 119px;" />
        </div>
        <div class="campo medio oculta_apgcnpj_contribuinte" style="width: 150px;">
            <label for="apgcnpj_contribuinte">CNPJ do Contribuinte:</label>
            <input type="text" name="apgcnpj_contribuinte" id="apgcnpj_contribuinte" maxlength="14" class="campo somenteNumero" value="<?=$this->view->parametros->apgcnpj_contribuinte?>" style="width: 130px;" />
        </div>
        

        <!-- CAMPOS NOVOS -->
        <div class="clear"></div>        
        <div class="campo menor oculta_apgcodigo_receita">
            <label for="apgcodigo_receita">Código da Receita:</label>
            <input type="text" name="apgcodigo_receita" id="apgcodigo_receita" class="campo somenteNumero" maxlength="4" value="<?=$this->view->parametros->apgcodigo_receita?>" />
        </div>
        
        <!-- tipo de datas -->        
        <div class="campo mes_ano oculta_apgperiodo_referencia1">
            <label for="data">Período de Apuração</label>    
            <input id="apgperiodo_referencia1" type="text" name="apgperiodo_referencia1" maxlength="10" value="<?=$this->view->parametros->apgperiodo_referencia1?>" class="campo" />
        </div>

        <div class="campo data oculta_apgperiodo_referencia2">
            <label for="data">Período de Apuração</label>    
            <input id="apgperiodo_referencia2" type="text" name="apgperiodo_referencia2" maxlength="10" value="<?=$this->view->parametros->apgperiodo_referencia2?>" class="campo" />
        </div>        
        <!-- fim de datas -->


        <div class="campo menor oculta_apgnumero_referencia">
            <label for="apgno_cheque campoCheque">Número Referência:</label>
            <input type="text" name="apgnumero_referencia" id="apgnumero_referencia" maxlength="17" class="campo somenteNumero" value="<?=$this->view->parametros->apgnumero_referencia?>" />
        </div>        
        <div class="campo medio oculta_apgvalor_receita_bruta">
            <label for="apgvalor_receita_bruta">Valor da Receita Bruta Acumulada:</label>        
            <input type="text" name="apgvalor_receita_bruta" id="apgvalor_receita_bruta" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvalor_receita_bruta2, 2, ",", ".")?>" />
        </div>        
        <div class="campo menor oculta_apgpercentual_receita_bruta">
            <label for="apgpercentual_receita_bruta">Percentual:</label>        
            <input type="text" name="apgpercentual_receita_bruta" id="apgpercentual_receita_bruta" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgpercentual_receita_bruta2, 2, ",", ".")?>" maxlength="5" />
        </div>
        <div class="campo medio oculta_apgidentificador_fgts">
            <label for="apgidentificador_fgts">Identificador do FGTS:</label>
            <input type="text" name="apgidentificador_fgts" id="apgidentificador_fgts" maxlength="16" class="campo somenteNumero" value="<?=$this->view->parametros->apgidentificador_fgts?>" />
        </div>        
        <div class="campo medio oculta_apgdivida_ativa">
            <label for="apgdivida_ativa">Dívida Ativa ou Nº Etiqueta:</label>
            <input type="text" name="apgdivida_ativa" id="apgdivida_ativa" class="campo somenteNumero" maxlength="13" value="<?=$this->view->parametros->apgdivida_ativa?>" />
        </div>
        <div class="campo medio oculta_apgnum_parcela">
            <label for="apgnum_parcela">Número Parcela/Notificação:</label>
            <input type="text" name="apgnum_parcela" id="apgnum_parcela" class="campo somenteNumero" maxlength="13" value="<?=$this->view->parametros->apgnum_parcela?>" />
        </div>        
        <div class="campo oculta_apgidentificador_gps">
            <label for="apgidentificador_gps">Identificador:</label>
            <input type="text" name="apgidentificador_gps" id="apgidentificador_gps" maxlength="18" class="campo somenteNumero" value="<?=$this->view->parametros->apgidentificador_gps?>" />
        </div>
        <div class="campo maior oculta_apgidentificador_gps">
            <label for="apgidentificador_gps">Nome ou Razão Social:</label>
            <input type="text" name="apgidentificador_gps_nome" id="apgidentificador_gps_nome" disabled="disabled" class="campo" value="<?=$this->view->parametros->apgidentificador_gps_nome?>" />
        </div>        

        <div class="clear"></div>
                
        <!-- FIM CAMPOS NOVOS -->


        <div class="clear"></div>        
        <label class='campoboleto'>Linha Digitável:</label>        
        <div class="clear campoboleto"></div>
        <div class="campo campoboleto">            
            <input type="text" name="apglinha_digitavel1" id="apglinha_digitavel1" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel1?>" maxlength="5" onkeyup="if(this.value.length >= 5) { apglinha_digitavel2.focus(); }" style="width:55px;" />
        </div>
        <div class="campo campoboleto">           
            <input type="text" name="apglinha_digitavel2" id="apglinha_digitavel2" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel2?>" maxlength="5" onkeyup="if(this.value.length >= 5) { apglinha_digitavel3.focus(); }" style="width:55px;" />
        </div>
        <div class="campo campoboleto">            
            <input type="text" name="apglinha_digitavel3" id="apglinha_digitavel3" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel3?>" maxlength="5" onkeyup="if(this.value.length >= 5) { apglinha_digitavel4.focus(); }" style="width:55px;" />
        </div>
        <div class="campo campoboleto">            
            <input type="text" name="apglinha_digitavel4" id="apglinha_digitavel4" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel4?>" maxlength="6" onkeyup="if(this.value.length >= 6) { apglinha_digitavel5.focus(); }" style="width:60px;" />
        </div>
        <div class="campo campoboleto">            
            <input type="text" name="apglinha_digitavel5" id="apglinha_digitavel5" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel5?>" maxlength="5" onkeyup="if(this.value.length >= 5) { apglinha_digitavel6.focus(); }" style="width:55px;" />
        </div>
        <div class="campo campoboleto">
            <input type="text" name="apglinha_digitavel6" id="apglinha_digitavel6" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel6?>" maxlength="6" onkeyup="if(this.value.length >= 6) { apglinha_digitavel7.focus(); }" style="width:60px;" />
        </div>
        <div class="campo campoboleto">
            <input type="text" name="apglinha_digitavel7" id="apglinha_digitavel7" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel7?>" maxlength="1" onkeyup="if(this.value.length >= 1) { apglinha_digitavel8.focus(); }" style="width:25px;" />
        </div>
        <div class="campo menor campoboleto">
            <input type="text" name="apglinha_digitavel8" id="apglinha_digitavel8" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel8?>" maxlength="14"  style="width:125px;" />
        </div>
        <div class="campo medio campoboleto">
            <button type="button" id="bt_gerar_codigo_barras" name="bt_gerar_codigo_barras" class="botao_gerar">Gerar Código de Barras</button>
        </div>

        <div class="clear"></div>        
        <label class='campoboletoconcessionaria'>Linha Digitável:</label>        
        <div class="clear campoboletoconcessionaria"></div>
        <div class="campo campoboletoconcessionaria">            
            <input type="text" name="apglinha_digitavel_conc1" id="apglinha_digitavel_conc1" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel_conc1?>" maxlength="12" onkeyup="if(this.value.length >= 12) { apglinha_digitavel_conc2.focus(); }" style="width:112px;" />
        </div>
        <div class="campo campoboletoconcessionaria">           
            <input type="text" name="apglinha_digitavel_conc2" id="apglinha_digitavel_conc2" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel_conc2?>" maxlength="12" onkeyup="if(this.value.length >= 12) { apglinha_digitavel_conc3.focus(); }" style="width:112px;" />
        </div>
        <div class="campo campoboletoconcessionaria">            
            <input type="text" name="apglinha_digitavel_conc3" id="apglinha_digitavel_conc3" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel_conc3?>" maxlength="12" onkeyup="if(this.value.length >= 12) { apglinha_digitavel_conc4.focus(); }" style="width:112px;" />
        </div>
        <div class="campo campoboletoconcessionaria">            
            <input type="text" name="apglinha_digitavel_conc4" id="apglinha_digitavel_conc4" class="campo somenteNumero" value="<?=$this->view->parametros->apglinha_digitavel_conc4?>" maxlength="12" style="width:112px;" />
        </div>        
        <div class="campo medio campoboletoconcessionaria">
            <button type="button" id="bt_gerar_codigo_barras_concessionaria" name="bt_gerar_codigo_barras_concessionaria" class="botao_gerar" >Gerar Código de Barras</button>
        </div>

        <div class="clear campocodigodebarras"></div>
        <div class="campo maior campocodigodebarras">
            <label for="apgcodigo_barras campocodigodebarras">Código de Barras:</label>
            <input type="text" name="apgcodigo_barras" id="apgcodigo_barras" class="campo campocodigodebarras somenteNumero" value="<?=$this->view->parametros->apgcodigo_barras?>" style="width:545px;" />
        </div>

                <div class="clear"></div>
        <fieldset class="">
            <legend>Descontos</legend>
            <div class="campo menor">
                <label for="apgvl_desconto">Desconto: <img class="btn-help" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'<ul><li>Para títulos de <strong>Concessionárias</strong> ou <strong>GNRE</strong>, é aplicado sobre o <strong>Valor Bruto</strong> para calcular o <strong>Valor Pago/Título</strong>.</li><br /><li>Para Boletos de <strong>fornecedores</strong> ou <strong>FGTS</strong>, é aplicado sobre o <strong>Valor Pago/Título</strong> para calcular o <strong>Valor Total</strong>.</li><br /><li>Para <strong>demais formas</strong> de pagamento, este campo é <strong>bloqueado</strong>.</li></ul>','D' , '');"></label>
                <input type="text" name="apgvl_desconto" id="apgvl_desconto" class="campo campo_valor_direita" onblur="calculaValor_liquido_total();" value="<?=number_format($this->view->parametros->apgvl_desconto2, 2, ",", ".")?>" />
            </div>            
            <div class="campo menor">
                <label for="apgvl_ir">Imposto de Renda:</label>        
                <input type="text" name="apgvl_ir" id="apgvl_ir" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_ir2, 2, ",", ".")?>" />
            </div>
            <div class="campo menor">
                <label for="apgcod_ir">Código do IR:</label>        
                <input type="text" name="apgcod_ir" id="apgcod_ir" class="campo somenteNumero campo_valor_direita" value="<?=$this->view->parametros->apgcod_ir?>" />
            </div>

            <div class="clear"></div>
            <div class="campo menor">
                <label for="apgvl_pis">Pis:</label>        
                <input type="text" name="apgvl_pis" id="apgvl_pis" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_pis2, 2, ",", ".")?>" />
            </div>
            
            <div class="campo menor">
                <label for="apgvl_cofins">Cofins:</label>        
                <input type="text" name="apgvl_cofins" id="apgvl_cofins" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_cofins2, 2, ",", ".")?>" />
            </div>
            <div class="campo menor">
                <label for="apgvl_csll">Csll:</label>        
                <input type="text" name="apgvl_csll" id="apgvl_csll" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_csll2, 2, ",", ".")?>" />
            </div>

            <div class="clear"></div>
            <div class="campo menor">
                <label for="apgvl_inss">INSS:</label>        
                <input type="text" name="apgvl_inss" id="apgvl_inss" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_inss2, 2, ",", ".")?>" />
            </div>
            <div class="campo menor">
                <label for="apgvl_iss">ISS:</label>        
                <input type="text" name="apgvl_iss" id="apgvl_iss" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_iss2, 2, ",", ".")?>" />
            </div>
            <div class="campo menor">
                <label for="apgcsrf">CSRF:</label>        
                <input type="text" name="apgcsrf" id="apgcsrf" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgcsrf2, 2, ",", ".")?>" />
            </div>
        </fieldset>

        <fieldset class="">
            <legend>Acréscimos</legend>
            <div class="campo menor">
                <label for="apgvl_juros">Juros:</label>          
                <input type="text" name="apgvl_juros" id="apgvl_juros" class="campo campo_valor_direita" onblur="calculaValor_liquido_total();" value="<?=number_format($this->view->parametros->apgvl_juros2, 2, ",", ".")?>" />
            </div>
            <div class="clear"></div>
            <div class="campo menor">
                <label for="apgvl_multa">Multa:</label>          
                <input type="text" name="apgvl_multa" id="apgvl_multa" class="campo campo_valor_direita" onblur="calculaValor_liquido_total();" value="<?=number_format($this->view->parametros->apgvl_multa2, 2, ",", ".")?>" />
            </div>
            <div class="clear"></div>
            <div class="campo menor">
                <label for="apgvl_tarifa_bancaria">Tarifas Bancárias:</label>        
                <input type="text" name="apgvl_tarifa_bancaria" id="apgvl_tarifa_bancaria" onblur="calculaValor_liquido_total();" class="campo campo_valor_direita" value="<?=number_format($this->view->parametros->apgvl_tarifa_bancaria2, 2, ",", ".")?>" />
            </div>            
        </fieldset>

        <fieldset class="">
            <legend>Totais</legend>
            <div class="campo menor">
                <label for="apgvl_apagar">Valor Bruto:</label>          
                <input type="text" name="apgvl_apagar" id="apgvl_apagar" class="campo campo_valor_direita" disabled="disabled" value="<?=number_format($this->view->parametros->apgvl_apagar2, 2, ",", ".")?>" />
            </div>
            <div class="clear"></div>
            <div class="campo menor">
                <label for="apgvl_pago">Valor Pago / Título: <img class="btn-help" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Valor impresso no código de barras do título a pagar .','D' , '');"</label>          
                <input type="text" name="apgvl_pago" id="apgvl_pago" class="campo campo_valor_direita" disabled="disabled" value="<?=number_format(0.00, 2, ",", ".")?>" />
            </div>
            <div class="clear"></div>
            <div class="campo menor">
                <label for="apgvl_total">Valor Total:</label>          
                <input type="text" name="apgvl_total" id="apgvl_total" class="campo campo_valor_direita" disabled="disabled" value="<?=number_format($this->view->parametros->apgvl_total2, 2, ",", ".")?>" />
            </div>

        </fieldset>

        <div class="clear"></div>
        <div class="campo menor">
            <label for="apgobs">Observação:</label>
            <textarea name="apgobs" style="width: 545px; height: 80px;"><?=$this->view->parametros->apgobs?></textarea>
        </div>

		<div class="clear"></div>
    </div>
</div>
<div class="bloco_acoes">

    <?
    // [START][ORGMKTOTVS-1184] - Leandro Corso
    if ($_SESSION['funcao']['altera_contas'] == 1){ ?>
    <input
        type="button" 
        value="Atualizar"
        <?= INTEGRACAO 
            ? 'disabled readonly'
            : 'id="bt_gravar" name="bt_gravar"' ?>
    />
    <? }

    if($_SESSION['funcao']['excluir_apagar'] == 1) {?>
    <input 
        type="button" 
        value="Excluir"
        <?= INTEGRACAO 
            ? 'disabled readonly'
            : 'class="excluir" data-apgoid="<?=$this->view->parametros->apgoid;?>"' ?>
    />
    <? } ?>
    
    <? if (INTEGRACAO) echo ParametroIntegracaoTotvs::message(array('Os botões "atualizar"', '"excluir"'));
    // [START][ORGMKTOTVS-1184] - Leandro Corso
    ?>

</div>
<script type="text/javascript">
    calculaValor_liquido_total();
</script>