<div class="formulario">

    <div class="campo datas">
        <label>Data Emissão *</label>
        <input class="campo" readonly="readonly" type="text" name="dt_emi" id="dt_emi" maxlength="10" value="<?= date("d/m/Y") ?>" size="12" />
     <!--    <img src="images/calendar_cal.gif" alt="Calendário" title="Calendário">--> 
    </div>

    <div class="campo data">
        <label>Data Vencimento *</label>
        <input class="campo"  type="text" name="dt_venc" id="dt_venc" maxlength="10" value="<?php echo $_POST['dt_venc']; ?>" />
    </div>

    <div class="campo data">
     <!--    <label>Data Referência</label>--> 
        <input class="campo" readonly="readonly" disabled="disabled" type="hidden" name="dt_ref" id="dt_ref" maxlength="10" value="<?= date("01/m/Y") ?>" />
       <!--  <img src="images/calendar_cal.gif" alt="Calendário" title="Calendário">--> 
    </div>
    <div class="clear"></div>

    <div class="campo maior">
        <label for="forcoid">Forma de Pagamento *</label>
        <select id="forcoid" name="forcoid">
            <option value="">Escolha</option>
            <?php foreach ($formasCobranca as $forma): ?>
                <option value="<?= $forma['forcoid'] ?>" <?= ($forma['forcoid'] == $_POST['forcoid'] ? 'selected="selected"' : "") ?>><?= $forma['forcnome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="clear"></div>

    <div class="campo menor">
        <label for="parc">Qtde. Parcelas *</label>
        <input  id="parc" name="parc" type="text" maxlength="3" value="<?php echo $_POST['parc']; ?>" class="campo" onKeyup="formatar(this, '@');" onBlur="revalidar(this, '@', '');" />
    </div>

    <div class="campo menor">
        <label for="nflserie">Série *</label>
        <select id="nflserie" name="nflserie">
            <option value="">Escolha</option>
            <?php foreach ($this->dao->getClassesSeries() as $classe): ?>
                <option value="<?= $classe['nfsserie'] ?>"><?= $classe['nfsserie'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="clear"></div>

    <div class="campo">
        <label for="infCompNfe">Informações Complementares na NF-e</label>
        <input type="text" id="infCompNfe" name="infCompNfe" value="" size="90" maxlength="80" tabindex="1">
    </div>
    <div class="clear"></div>

</div>