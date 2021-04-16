<?php cabecalho(); ?>

<!-- LINKS PARA CSS E JS -->
<?php require _MODULEDIR_ . 'Cadastro/View/cad_tipo_segmentacao/head.php' ?>

<div class="modulo_titulo">Cadastro de Tipos de Segmentação</div>
<div class="modulo_conteudo">
    <?php if(!empty($this->error_message)): ?>
    <div class="mensagem erro"><?php echo $this->error_message ?></div>
    <?php endif; ?>
    <form id="form"  method="post" action="cad_tipo_segmentacao.php">
        <div class="bloco_titulo">Dados para Pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="modulo_principal">Módulo Principal</label>
                    <select id="tipoSegmentacao" name="tpssegmentacao">
                       <option value="">Escolha</option>
                        <?php foreach($this->comboTiposSegmentacao as $tipoSegmentacao):  ?>
                        <option value="<?php echo $tipoSegmentacao['tpsoid'] ?>"><?php echo $tipoSegmentacao['tpsdescricao'] ?></option>  
                        <?php endforeach; ?>   
                      </select>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="tpsdescricao">Descrição</label>
                    <input type="text" id="tpsdescricao" name="tpsdescricao" value="" class="campo" />
                </div>   
                <div class="clear"></div>
            </div>            
        </div>        
        <div class="bloco_acoes">
            <button type="button" id="pesquisar" name="pesquisar">Pesquisar</button>  
            <button id="btn_novo" type="button">Novo</button>
        </div>
        <div class="separador"></div>
        <div class="div-loding">
            <img class="invisivel loading" src="modulos/web/images/loading.gif" />
        </div>
        <div id="msg_erro" class="mensagem erro invisivel"></div>
        <div id="msg_sucesso" class="mensagem sucesso invisivel"></div>        
        <div id="msg_alerta" class="mensagem alerta invisivel"></div>        
        <div id="resultado_pesquisa" class="invisivel">
            <div class="bloco_titulo">Tipos Cadastrados</div>
            <div class="bloco_conteudo">					
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Tipo Principal</th>
                                <th class="centro">Editar</th>
                                <th class="centro">Excluir</th>
                            </tr>
                        </thead>
                        <tbody id="conteudo_listagem">	
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bloco_acoes">
                <p id="total_registros"></p>
            </div>	
        </div>
    </form>
</div>
<div class="separador"></div>
<?php include "lib/rodape.php"; ?>