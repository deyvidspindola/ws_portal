<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- jQuery -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>

<!-- Arquivos básicos de javascript -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/rel_analise_tratamento_os.js"></script>


<div id="mensagem_info" class="mensagem info">
    Os campos com * são obrigatórios.
</div>

<div id="mensagem_alerta" class="mensagem alerta invisivel">
        
</div>


<form id="form_cadastrar"  method="post" action="rel_analise_tratamento_os.php">
<input type="hidden" id="acao" name="acao" value="cadastrar"/>
<input type="hidden" id="aotordoid" name="aotordoid" value="<?php echo $this->view->parametros->ordoid; ?>"/>
<input type="hidden" id="aoteproid" name="aoteproid" value="<?php echo $this->view->parametros->eproid; ?>"/>
<input type="hidden" id="aotveipdata" name="aotveipdata" value="<?php echo $this->view->parametros->veipdata; ?>"/>
<input type="hidden" id="site_dir" value="<?php echo _PROTOCOLO_ . _SITEURL_;?>" />
   
<div class="bloco_titulo">Dados Principais</div>
<div class="bloco_conteudo">
   <div class="formulario">
       <div class="campo maior">
           <label for="aoamoid_acao" id="lbl_acao">Ação *</label>
           <select id="aoamoid_acao" name="aoamoid_acao">
               <option value="">Escolha</option>
               <?php if (count($this->view->parametros->acoes) > 0) : ?>
                   <?php foreach ($this->view->parametros->acoes as $acao): ?>
                       <option <?php echo ($this->view->parametros->aoamoid == $acao->aoamoid) ? 'selected="selected"' : '' ?> value="<?php echo $acao->aoamoid; ?> "><?php echo $acao->aoamdescricao; ?></option>
                   <?php endforeach; ?>
               <?php endif; ?>
           </select>
       </div>
        <div class="campo maior">
           <label for="aoamoid_motivo" id="lbl_motivo">Motivo *</label>
           <select id="aoamoid_motivo" name="aoamoid_motivo">
               <option value="">Escolha</option>
               <?php if (count($this->view->parametros->motivos) > 0) : ?>
                   <?php foreach ($this->view->parametros->motivos as $motivo): ?>
                       <option <?php echo (intval($this->view->parametros->aoamoid_motivo) == $motivo->aoamoid) ? 'selected="selected"' : '' ?> value="<?php echo $motivo->aoamoid; ?> "><?php echo $motivo->aoamdescricao; ?></option>
                   <?php endforeach; ?>
               <?php endif; ?>
           </select>
           <img src="modulos/web/images/ajax-loader-circle.gif" style="display: none;" class="carregando" />
       </div>
        <div class="clear"></div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_incluir">Confirmar</button>
    <button type="button" id="bt_cancelar">Cancelar</button>
</div>

</form>




