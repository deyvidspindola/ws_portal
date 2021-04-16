
<style>

    td.td-sublistagem table.sub-listagem{

    }
    table.sub-listagem tr {

    }

    table.sub-listagem tr td{
        border: 0px;
        padding: 9px;
    }

    .td-sublistagem{
        padding: 0px !important;
    }

    .important-style{
        height: 20px !important;
        width: 20px !important;
    }

    #selecionar-tipo-motivos .label_modal{
        color: gray;
        font-size: 11px;
        text-align: center;
    }

    #selecionar-tipo-motivos .info-usuario{
        margin-left: 17px;
    }

    #selecionar-tipo-motivos .info-usuario #nome label, #selecionar-tipo-motivos .info-usuario #email label{
        font-size: 12px;
        color: #000;
        display: inline-block;
    }

    #selecionar-tipo-motivos .info-usuario #nome span, #selecionar-tipo-motivos .info-usuario #email span{
        font-weight: bold;
    }

    #selecionar-tipo-motivos .bloco_conteudo .listagem table tr td label{
        cursor: pointer;
        font-size: 12px;
    }

    .erroField{
        color: #A47E3C !important;
        border-color:  #A47E3C !important;
    }
</style>
<div class="mensagem info">Campos com * são obrigatórios.</div>
<div id="msg_responsavel" class="mensagem invisivel"></div>

<div class="bloco_titulo">Colaborador</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <div id="campo_nome" class="campo maior">
            <label for="nome">Nome *</label>
            <input id="nome" tabindex="1" class="campo" maxlength="80" type="text" value="">
        </div>
        <div class="clear"></div>
    </div>
</div>

<div class="separador"></div>

<div id="loader_responsavel" class="carregando invisivel"></div>

<div id="separador_loader_responsavel" class="separador invisivel"></div>

<div class="bloco_titulo">Lista de Responsáveis</div>

<div class="bloco_conteudo" id="listagem-responsaveis">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Tipo(s) de Motivo de Crédito</th>
                    <th class="centro">Excluir</th>
                </tr>
            </thead>
            <tbody id="conteudo_responsaveis">
                <?php if (is_array($parametros->responsaveis)): ?>
                    <?php foreach ($parametros->responsaveis as $data) : ?>                
                        <tr>
                            <td style="background:#BAD0E5"><?php echo $data['usuario']->nm_usuario ?></td>
                            <td style="background:#BAD0E5"><?php echo $data['usuario']->usuemail ?></td>
                            <td class="td-sublistagem">
                                <table class="sub-listagem">  
                                    <?php foreach ($data['motivo'] as $motivo) : ?>    
                                        <?php $class = $class == 'par' ? 'impar' : 'par'; ?>
                                        <tr class="<?php echo $class ?>" ><td><?php echo $motivo->motivo_credito; ?></td></tr>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td style="background:#BAD0E5" class="centro">
                                <a class="excluir_reponsavel" data-usuario="<?php echo $data['usuario']->cd_usuario ?>" tabindex="2" id="excluir_reponsavel_<?php echo $data['usuario']->cferoid ?>" href="javascript:void(0);"> 
                                    <img src="images/icon_error.png" class="icone" />
                                </a>
                            </td>                   
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr style="background:#BAD0E5">
                        <td  ><?php echo $parametros->responsaveis->usuario->nm_usuario ?></td>
                        <td ><?php echo $parametros->responsaveis->usuario->usuemail ?></td>
                        <td class="td-sublistagem">
                            <table class="sub-listagem">
                                <?php foreach ($parametros->responsaveis->motivo as $motivo) : ?>
                                    <?php $class = $class == 'par' ? 'impar' : 'par'; ?>
                                    <tr class="<?php echo $class ?>" ><td><?php echo $motivo->motivo_credito; ?></td></tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                        <td  class="centro">
                            <a class="excluir_reponsavel" data-usuario="<?php echo $parametros->responsaveis->usuario->cd_usuario ?>"  id="excluir_reponsavel_<?php echo $parametros->responsaveis->usuario->cferoid ?>" href="javascript:void(0);"> 
                                <img src="images/icon_error.png" class="icone" />
                            </a>
                        </td>                   
                    </tr>
                <?php endif; ?>
            <tbody>
        </table>
    </div>
</div>


<div id="selecionar-tipo-motivos" style="display:none" title="Selecione os tipos de motivo">
    <div class="separador alertaCampos" style="display: none"></div>
    <div class="mensagem alerta alertaCampos" style="display: none">É obrigatório marcar pelo menos um tipo de motivo de crédito.</div>

    <div class="separador alertaSeparador"></div>

    <div class="label_modal">Maque os tipos de <strong>motivo de crédito</strong> aos quais o usuário irá aprovar.</div>

    <div class="separador"></div>

    <div class="info-usuario">
        <div id="nome"><label>Usuário: </label> <span></span></div>
        <div id="email"><label>E-mail: </label> <span></span></div>
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo" id="bloco_titulo_motivo">Tipos de Motivo de Crédito *</div>
    <div class="bloco_conteudo" id="bloco_conteudo_motivo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro"><input type="checkbox" id="selecionar_todos"></th>
                        <th class="centro">Descrição</th>
                    </tr>
                </thead>
                <tbody id="conteudo_responsaveis">
                    <tr>
                        <td class="centro"><input type="checkbox" class="parametrizar_motivo_credito_item" id="motivo_outros" name="motivo_outros" value="0"></td>
                        <td><label for="motivo_outros">Outros</label></td>
                    </tr>
                    <tr>
                        <td class="centro"><input type="checkbox" class="parametrizar_motivo_credito_item" id="motivo_contestacao" name="motivo_contestacao" value="1"></td>
                        <td><label for="motivo_contestacao">Constestação</label></td>
                    </tr>
                    <tr>
                        <td class="centro"><input type="checkbox" class="parametrizar_motivo_credito_item" id="motivo_indicacao_amigo" name="motivo_indicacao_amigo" value="2"></td>
                        <td><label for="motivo_indicacao_amigo">Indicação de Amigo</label></td>
                    </tr>
                    <tr>
                        <td class="centro"><input type="checkbox" class="parametrizar_motivo_credito_item" id="motivo_insencao_monitoramento" name="motivo_insencao_monitoramento" value="3"></td>
                        <td><label for="motivo_insencao_monitoramento">Isenção de Monitoramento</label></td>
                    </tr>
                    <tr>
                        <td class="centro"><input type="checkbox" class="parametrizar_motivo_credito_item" id="motivo_debito_automatico" name="motivo_debito_automatico" value="4"></td>
                        <td><label for="motivo_debito_automatico">Débito Automático</label></td>
                    </tr>
                    <tr>
                        <td class="centro"><input type="checkbox" class="parametrizar_motivo_credito_item" id="motivo_cartao_credito" name="motivo_cartao_credito" value="5"></td>
                        <td><label for="motivo_cartao_credito">Cartão de Crédito</labe></td>
                    </tr>
                <tbody>
            </table>
        </div>
    </div>
    <div class="separador"></div>
</div>

