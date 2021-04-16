<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>
<script type="text/javascript" src="modulos/web/js/prn_gestao_frota_contrato_vivo.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" /> 

<div class="modulo_titulo">Histórico/Contatos</div>
<div class="modulo_conteudo">
    
    <?php if(!empty($this->msgErro)): ?>
    <div class="mensagem alerta"><?php echo $this->msgErro ?></div>
    <?php endif; ?>    
    
    <div class="bloco_titulo">Pessoas Autorizadas</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>RG</th>
                        <th>Telefones</th>
                        <th>ID Nextel</th>                        
                    </tr>
                </thead>
                <tbody id="conteudo_historico_pessoas_autorizadas">
                    <?php foreach($this->dadosHistoricoContatos->contatosAssist as $contatoAssistencia): ?>
                    <tr>
                        <td><?php echo $contatoAssistencia->tctcontato ?></td>
                        <td class="direita"><?php echo $contatoAssistencia->tctcpf ?></td>
                        <td class="direita"><?php echo $contatoAssistencia->tctcrg ?></td>
                        <td>
                            <?php echo !empty($contatoAssistencia->fone_res) ? '<div style="float: left; width: 40px">Res.:</div> ' . formatar_fone_nono_digito($contatoAssistencia->fone_res) . '<br />' : '' ?>
                            <?php echo !empty($contatoAssistencia->fone_com) ? '<div style="float: left; width: 40px">Com.:</div> ' . formatar_fone_nono_digito($contatoAssistencia->fone_com) . '<br />' : '' ?>
                            <?php echo !empty($contatoAssistencia->fone_cel) ? '<div style="float: left; width: 40px">Cel.:</div> ' . formatar_fone_nono_digito($contatoAssistencia->fone_cel) . '<br />' : '' ?>
                        </td>
                        <td class="direita"><?php echo $contatoAssistencia->tctid_nextel ?></td>                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="separador"></div>
    
    <div class="bloco_titulo">Em Caso de Emergência Avisar</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Telefones</th>
                        <th>ID Nextel</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody id="conteudo_historico_emergencia_avisar">
                    <?php foreach($this->dadosHistoricoContatos->contatosEmerg as $contatoEmergencia): ?>
                    <tr>
                        <td><?php echo $contatoEmergencia->tctcontato ?></td>                        
                        <td>
                            <?php echo !empty($contatoEmergencia->fone_res) ? '<div style="float: left; width: 40px">Res.:</div> ' . formatar_fone_nono_digito($contatoEmergencia->fone_res) . '<br />' : '' ?>
                            <?php echo !empty($contatoEmergencia->fone_com) ? '<div style="float: left; width: 40px">Com.:</div> ' . formatar_fone_nono_digito($contatoEmergencia->fone_com) . '<br />' : '' ?>
                            <?php echo !empty($contatoEmergencia->fone_cel) ? '<div style="float: left; width: 40px">Cel.:</div> ' . formatar_fone_nono_digito($contatoEmergencia->fone_cel) . '<br />' : '' ?>
                        </td>
                        <td class="direita"><?php echo $contatoEmergencia->tctid_nextel ?></td>
                        <td><?php echo $contatoEmergencia->tctobs ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="separador"></div>
    
    <div class="bloco_titulo">Histórico do Contrato <?php echo !empty($this->dadosHistoricoContatos->historico) ? '- '. $this->dadosHistoricoContatos->historico[0]->nome_cliente : '' ?> </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Data</th>
                        <th>Observação</th>
                        <th>Atendente</th>                        
                    </tr>
                </thead>
                <tbody id="conteudo_historico_contrato">
                    <?php foreach($this->dadosHistoricoContatos->historico as $historico): ?>
                    <tr>
                        <td class="direita"><?php echo $historico->hitprotprotocolo ?></td>
                        <td class="centro"><?php echo date('d/m/Y', strtotime($historico->hitdt_acionamento)) ?></td>
                        <td><?php echo $historico->hitobs ?></td>
                        <td><?php echo $historico->nm_usuario ?></td>                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>