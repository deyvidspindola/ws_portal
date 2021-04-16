<?php cabecalho(); ?>

<!-- LINKS PARA CSS E JS -->
<?php require _MODULEDIR_ . 'Cadastro/View/cad_parametrizacao_consulta_gestor_credito/head.php' ?>

<div class="modulo_titulo">Parametrização de Consulta ao Gestor de Crédito</div>
<div class="modulo_conteudo">
            
    <div id="msg_erro" class="mensagem erro <?php if(empty($this->error_message)): ?>invisivel<?php endif; ?>"><?php if(!empty($this->error_message)): ?><?php echo $this->error_message ?><?php endif; ?></div>
    <div id="msg_alerta" class="mensagem alerta invisivel"></div>
    <div id="msg_sucesso" class="mensagem sucesso invisivel"></div>
    
    <form id="form"  method="post" action="" >
    	<input type="hidden" id="persistirDados" />
        <div class="bloco_titulo">Dados para Pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="tipoPessoa">Tipo Pessoa</label>
                    <select name="tipoPessoa" id="tipoPessoa">
                        <option value="">Escolha</option>
                        <option value="f" <?php if($filtroPesquisa->tipoPessoa == 'f'): ?> selected="selected"<?php endif; ?>>F&iacute;sica</option>
                        <option value="j" <?php if($filtroPesquisa->tipoPessoa == 'j'): ?> selected="selected"<?php endif; ?>>Jur&iacute;dica</option>
                    </select>
                </div>
                <div class="clear"></div>
                <div class="campo medio">
                    <label for="tipoProposta">Tipo Proposta</label>
                    <select name="tipoProposta" id="tipoProposta">
                        <option value="">Escolha</option>
                        <?php echo $tipoProposta; ?>
                    </select>
                </div>
                <div id="combo_subtipoProposta" class="campo medio"
                	<?php if(!$subtipoProposta || !$filtroPesquisa->tipoProposta) : ?>
                		style="display: none;"
                	<?php endif; ?>
                >
                    <label for="subtipoProposta">Subtipo Proposta</label>
                    <select name="subtipoProposta" id="subtipoProposta">
                        <option value="">Escolha</option>
                        <?php echo $subtipoProposta; ?>
                    </select>
                </div>
                <div class="clear"></div>
                <div class="campo medio">
                	<label for="tipoContrato">Tipo Contrato</label>
                    <select name="tipoContrato" id="tipoContrato">
                         <option value="">Escolha</option>
                        <?php echo $tipoContrato; ?>
                    </select>
                </div>   
                <div class="clear"></div>
                <div class="campo medio">
                	<label for="vaiGestor">Vai ao Gestor?</label>
                    <select name="vaiGestor" id="vaiGestor">
                        <option value="">Escolha</option>
                        <option value="t"<?php echo $filtroPesquisa->vaiGestor == 't' ? ' selected="selected"' : ''; ?>>Sim</option>
                        <option value="f"<?php echo $filtroPesquisa->vaiGestor == 'f' ? ' selected="selected"' : ''; ?>>Não</option>
                    </select>
                </div>   
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="bloco_acoes">
            <button type="button" id="pesquisar" name="pesquisar">Pesquisar</button>  
            <button id="btn_novo" type="button">Novo</button>
        </div>
        
        <div class="separador"></div>
        
        <div id="resultado_pesquisa" class="invisivel">
            <div class="bloco_titulo">Resultado da Pesquisa</div>
            <div class="bloco_conteudo">					
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>
                            	<th>Vai ao Gestor?</th>
                                <th>Tipo Pessoa</th>
                                <th>Tipo / Subtipo Proposta</th>
                                <th>Tipo Contrato</th>
                                <th>Limite</th>
                                <th class="centro">Ações</th>
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
        
        <div class="carregando invisivel"></div>
        
    </form>
</div>
<div class="separador"></div>
<?php include "lib/rodape.php"; ?>
