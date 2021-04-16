<div class="separador"></div>

<div class="bloco_titulo">Dados Cliente</div>
<div class="resultado bloco_conteudo">
    <div class="listagem cabecalho_fixo">
        <table>
            <tbody>
                <tr class="impar">
                    <td width="34%"><b>Cliente: </b><?php echo $this->view->dados->dadosGerais->clinome; ?></td>
                    <td width="33%"><b>Placa: </b><?php echo $this->view->dados->dadosGerais->veiplaca; ?></td>
                    <td width="33%"><b>Equipamento: </b><?php echo $this->view->dados->dadosGerais->eveversao; ?></td>
                </tr>
                <tr class="impar">
                    <td width="34%"><b>Base: </b><?php echo $this->view->dados->dadosGerais->baseCliente; ?></td>
                    <td width="33%"><b>Contrato: </b><?php echo $this->view->dados->dadosGerais->contrato; ?></td>
                    <td width="33%"><b>Período: </b><?php echo $this->view->dados->dadosGerais->periodo; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
