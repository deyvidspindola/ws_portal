<?php cabecalho(); ?>

<!-- LINKS PARA CSS E JS -->
<?php require _MODULEDIR_ . 'Cadastro/View/cad_tipo_segmentacao/head.php' ?>

<div class="modulo_titulo">Cadastro de Tipos de Segmentação</div>
<div class="modulo_conteudo">
    <div class="mensagem info">Os campos com * são obrigatórios.</div>
    <div id="msg_alerta" class="mensagem alerta invisivel"></div>				
    <div id="msg_sucesso" class="mensagem sucesso invisivel"></div>
    <div id="msg_erro" class="mensagem erro invisivel"></div>
    <form id="form_inserir"  method="post">
        <input type="hidden" name="acao" value="inserir" />        
        <div class="bloco_titulo">Dados Principais</div>        
        <div class="bloco_conteudo">
            <div class="formulario">                
                <div class="campo medio">
                    <label for="tpsdescricao">Descrição *</label>
                    <input type="text" id="tpsdescricao" name="tpsdescricao" value="" class="campo" />
                </div>   
                <div class="clear"></div>
                <fieldset class="medio">
                    <legend>Tipo Principal *</legend>
                    <input type="radio" id="tpsprincipal_sim" class="tpsprincipal" name="tpsprincipal" value="sim" />
                    <label for="tpsprincipal_sim">Sim</label>
                    <input type="radio" id="tpsprincipal_nao" class="tpsprincipal" name="tpsprincipal" value="nao" />
                    <label for="tpsprincipal_nao">Não</label>                    
                </fieldset>
                <div class="clear"></div>
                <div id="combo_principal_novo" class="campo medio invisivel">
                    <label for="modulo_principal">Módulo Principal *</label>
                    <select class="float-left" id="tpssegmentacao" name="tpssegmentacao">
                       <option value="">Escolha</option>
                            <?php foreach($this->comboTiposSegmentacao as $tipoSegmentacao):  ?>
                            <option value="<?php echo $tipoSegmentacao['tpsoid'] ?>"><?php echo $tipoSegmentacao['tpsdescricao'] ?></option>  
                            <?php endforeach; ?>   
                    </select>                    
                    <img class="loaging-circle float-left" src="modulos/web/images/ajax-loader-circle.gif" />
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="bloco_acoes">           
            <button id="btn_inserir" type="button">Inserir</button>
            <button id="btn_voltar" type="button">Voltar</button>
        </div>
    </form>
    <div class="separador"></div>
    <div class="div-loding">
        <img class="invisivel loading" src="modulos/web/images/loading.gif" />
    </div>
</div>
<div class="separador"></div>
<?php include "lib/rodape.php"; ?>