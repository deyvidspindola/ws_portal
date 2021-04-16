<div class="bloco_titulo">Histórico</div>
<div class="bloco_conteudo">   
    <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Contrato</th>
                        <th>Status</th>
                        <th>Observação</th>
                        <th>Responsável</th>
                        <th>Env. Retirada</th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                
                <tbody>
                <? foreach ($listaHistorico as $historico): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($historico['reschdata'])) ?></td>
                        <td><?= $historico['rescconoid'] ?></td>
                        <td>
                            <? if ($historico['reschstatus']     == 'R'): ?>
                                Enviado para Retirada
                            <? elseif ($historico['reschstatus'] == 'D'): ?>
                                Aguardando Depósito
                            <? elseif ($historico['reschstatus'] == 'A'): ?>
                                Arquivado
                            <? elseif ($historico['reschstatus'] == 'E'): ?>
                                Recup Equip. Obs.
                            <? endif ?>
                        </td>
                        <td><?= $historico['reschobservacao'] ?></td>
                        <td><?= $historico['ds_login'] ?></td>
                        <td>
                            <? if ($historico['rescenv_retirada']): ?>
                                <?= date('d/m/Y', strtotime($historico['rescenv_retirada'])) ?>
                            <? endif ?>
                        </td>
                        <td><?= $historico['usuario'] ?></td>
                    </tr>
                <? endforeach ?>
                </tbody>
            </table>
        </div>
</div>
<div class="bloco_acoes">
    <p><?= count($listaHistorico) ?> registro(s) encontrado(s)</p>
</div>