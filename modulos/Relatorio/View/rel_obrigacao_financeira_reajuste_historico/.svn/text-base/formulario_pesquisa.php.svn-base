<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

       <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $this->view->parametros->cliente_id ?>">
     
        <div class="campo data periodo">
            <div class="inicial">
                <label for="lbl_ofrhdt_referencia">Período Reajuste</label>
                <input type="text" value="<?php echo $this->view->parametros->dt_ini; ?>" maxlength="10" id="dt_ini" name="dt_ini" class="campo">
            </div>                          
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="">&nbsp;</label>
                 <input type="text" value="<?php echo $this->view->parametros->dt_fim; ?>" maxlength="10" id="dt_fim" name="dt_fim" class="campo">
            </div>                            
        </div>


        <div class="clear"></div>

        
        <div class="campo maior">
            <label id="lbl_clinome" for="clinome">Cliente</label> 
            <input type="text" id="clinome" maxlength="255" name="clinome" value="<?php echo trim($this->view->parametros->clinome); ?>" class="campo razao_social limpar_campos" />
        </div>
        <div class="campo menor">
            <label for="combo">Tipo Reajuste</label>
            <select id="ofrhtipo_reajuste" class="small" name="ofrhtipo_reajuste">
                  <option selected="selected" value="">Escolha</option>
                  <option value="1">IGPM</option>
                  <option value="2">INPC</option>
             </select>
        </div>
               
        <div class="clear"></div>

        <div class="campo menor">
            <label id="lbl_ofrhconnumero" for="ofrhconnumero">Contrato</label>
            <input id="ofrhconnumero" class="campo" type="text" value="<?php echo $this->view->parametros->ofrhconnumero ?>" name="ofrhconnumero">
        </div>

       
        <div class="campo medio">                        
             <label for="tipo_contrato">Tipo de Contrato </label>    
               <select id="tipo_contrato" name="tipo_contrato">
                  <option value="">Escolha</option>
                  <?php foreach ($tipoContrato as $tipoContrato) : ?>
                      <option value="<?php echo $tipoContrato->tpcoid ?>" <?php echo (!empty($this->view->parametros->tpcoid) && ($this->view->parametros->tpcoid == $tipoContrato->tpcoid) ? 'selected=selected' : '') ?>><?php echo $tipoContrato->tpcdescricao ?></option>
                  <?php endforeach; ?>
              </select>
         </div>
  


        <div class="campo menor">
            <label for="combo">Faturado</label>
            <select id="ofrhnfloid" class="small" name="ofrhnfloid">
                  <option selected="selected" value="">Todos</option>
                  <option value="1">Sim </option>
                  <option value="2">Não</option>
             </select>
        </div>


		<div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
     <button type="button" id="gerar_csv">Gerar CSV</button>   
</div>





