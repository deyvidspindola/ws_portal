<?php 

$cadCidadeMapeadaBairro = new CadCidadeMapeadaBairro();

?>

<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo medio">
         
            <label id="lbl_cmbestoid" for="cmbestoid">UF</label>
            <select id="cmbestoid" name="cmbestoid">
            <option value="">Escolha</option>
            <option value="t">Todos</option>
            <?php 
            
            	foreach ($cadCidadeMapeadaBairro->getEstados() as $ufs){
            ?>
           <option value="<?php echo $ufs->estoid;?>"  <?php  if ($this->view->parametros->cmbestoid == $ufs->estoid && $populaFiltro) {  echo 'selected=selected ';}  ?>      ><?php echo $ufs->estuf;?></option>		
            <?php 	
            	} 
            ?>
            </select>
        </div>

        <div class="campo medio">
            <label id="lbl_cmbclcoid" for="cmbclcoid">Cidade</label>
            <select id="cmbclcoid" name="cmbclcoid">
                <option value="">Escolha</option>
                  <?php 
                  
                  if($populaFiltro){
                	foreach ($cadCidadeMapeadaBairro->getCidades($this->view->parametros->cmbestoid) as $cidades){
                ?>
                 <option value="<?php echo $cidades['clcoid'];?>" <?php  if ($this->view->parametros->cmbclcoid == $cidades['clcoid']) {  echo 'selected=selected ';}  ?> ><?php echo $cidades['clcnome'];?></option>		
              <?php   
                } }?>
            </select>
            <span class='carregando_cidades'></span>
        </div>

        <div class="campo medio">
            <label id="lbl_cmbcbaoid" for="cmbcbaoid">Bairro</label>
            <select id="cmbcbaoid" name="cmbcbaoid">
                <option value="">Escolha</option>
                 <?php 
                 
                 if($populaFiltro){
                	foreach ($cadCidadeMapeadaBairro->getBairros($this->view->parametros->cmbclcoid) as $cidades){
                ?>
                 <option value="<?php echo $cidades['cbaoid'];?>" <?php  if ($this->view->parametros->cmbcbaoid == $cidades['cbaoid']) {  echo 'selected=selected ';}  ?> ><?php echo $cidades['cbanome'];?></option>		
              <?php
                } }?>
            </select>
            <span class='carregando_bairros'></span>
        </div>

		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_novo">Novo</button>
</div>







