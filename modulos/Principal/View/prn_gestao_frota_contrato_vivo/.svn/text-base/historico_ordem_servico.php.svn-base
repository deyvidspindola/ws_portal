<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>
<script type="text/javascript" src="modulos/web/js/prn_gestao_frota_contrato_vivo.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" /> 

<div class="modulo_titulo">Histórico/Ordem de Serviço</div>
<div class="modulo_conteudo">
    
    <?php if(!empty($this->msgErro)): ?>
    <div class="mensagem alerta"><?php echo $this->msgErro ?></div>
    <?php endif; ?>    
    
    <div class="bloco_titulo">Histórico da Ordem de Serviço <?php echo !empty($ordoid) ? '- '. $ordoid : '' ?> </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro">Status</th>
                        <th class="centro">Data Agenda</th>
                        <th class="centro">Observação</th>
                        <th class="centro">Data</th>
                        <th class="centro">Usuário</th>                        
                    </tr>
                </thead>
                <tbody id="conteudo_historico_contrato">
                    <?php foreach($this->dadosHistoricoOrdemServico as $historico): ?>
                    <?php 
                        if($historico->orssituacao == "" || $historico->orssituacao == "NULL"){
                            $obs = "";
                        }else{
                            $obs = $historico->orssituacao;
                        }
                
                        if(!empty($historico->dt_agenda) && !empty($historico->hr_agenda)){
                            $dtAgenda = $historico->dt_agenda." - ".$historico->hr_agenda;
                        }else{
                            $dtAgenda = $historico->dt_agenda;
                        }
                        
                        //if($obs=='Comunicação Nível 1 Enviada' || $obs=='Comunicação Nível 2 Enviada' || ){
                        if(in_array($historico->orsstatus, array(82,83))){
                            $obs = "<a href='lista_indisp_contato_os.php?ordoid=$ordoid' target='_blank'>".$obs."</a>";
                        }
                    ?>
                    <tr>
                        <td class="esquerda"><?php echo $historico->orsstatusi ?></td>
                        <td class="centro" width="10%"><?php echo $dtAgenda ?></td>
                        <td class="esquerda"><?php echo nl2br($obs) ?></td>
                        <td class="centro" width="10%"><?php echo $historico->dt_situacao ?></td>
                        <td class="esquerda" width="10%"><?php echo $historico->nm_usuario ?></td>                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>
