        <table width="100%" class="padrao">
            <tr>
                <td colspan="2" class="form_titulo">Dados para Pesquisa</td>
            </tr>

            <tr>
                <td colspan="2" class="form_label">Empresa *</td>
            </tr>
            <?php $empresas = buscarEmpresas(); ?>
            <tr>
                <td colspan="2" align="left" class="form_label">
                    <select id="adiempresa" name="adiempresa" class="form_field">
                        <option value="">- Selecione -</option>
                    <?php foreach($empresas as $empresa) : ?>
                        <option value="<?php echo $empresa['tecoid']; ?>" <?php echo ($_POST['adiempresa'] == $empresa['tecoid'] ? "selected=selected" : ""); ?>>
                            <?php echo $empresa['tecrazao']; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="form_label">Status da Solicitação</td>
                <td class="form_label centro_custo">Centro de Custo</td>
            </tr>

            <tr>
                <td width="40%" class="form_label">
                    <select id="adistatus_solicitacao" name="adistatus_solicitacao" class="form_field">
                        <option value="">- Selecione -</option>
                        <option value="P" <?php echo ($_POST['adistatus_solicitacao'] == 'P' ? "selected=selected" : ""); ?>>Pendente de aprovação</option>
                        <option value="C" <?php echo ($_POST['adistatus_solicitacao'] == 'C' ? "selected=selected" : ""); ?>>Requisição reprovada</option>
                        <option value="A" <?php echo ($_POST['adistatus_solicitacao'] == 'A' ? "selected=selected" : ""); ?>>Finalizada</option>
                        <option value="S" <?php echo ($_POST['adistatus_solicitacao'] == 'S' ? "selected=selected" : ""); ?>>Pendente de prestação de contas</option>
                        <option value="R" <?php echo ($_POST['adistatus_solicitacao'] == 'R' ? "selected=selected" : ""); ?>>Pendente aprovacao de reembolso</option>
                        <option value="F" <?php echo ($_POST['adistatus_solicitacao'] == 'F' ? "selected=selected" : ""); ?>>Pendente conferencia de prestacao de contas</option>
                        <option value="D" <?php echo ($_POST['adistatus_solicitacao'] == 'D' ? "selected=selected" : ""); ?>>Aguardando devolução</option>
                    </select>
                </td>

                <td class="form_label centro_custo">
                    <input type="hidden" id="edicaoCentroCusto" value="<?php echo (isset($_POST['adicntoid']) ? $_POST['adicntoid'] : ""); ?>">
                    <select id="adicntoid" name="adicntoid" class="form_field" style="width: 550px !important;">
                        <option value="">- Selecione -</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="form_label">Tipo de Requisição</td>
                <td class="form_label">Número da Requisição</td>
            </tr>

            <tr>
                <td class="form_label">
                    <select id="adittipo_solicitacao" name="adittipo_solicitacao" class="form_field" style="width: 252px !important;">
                        <option value="">- Selecione -</option>
                        <option value="C" <?php echo ($_POST['adittipo_solicitacao'] == 'C' ? "selected=selected" : ""); ?>>Combustível - ticket car</option>
                        <option value="A" <?php echo ($_POST['adittipo_solicitacao'] == 'A' ? "selected=selected" : ""); ?>>Adiantamento</option>
                    </select>
                </td>

                <td class="form_label">
                    <input type="text" size="15" maxlength="16" id="adioid" name="adioid" class="form_field" value="<?php echo (isset($_POST['adioid']) ? $_POST['adioid'] : ""); ?>">
                </td>
            </tr>

            <tr>
                <td colspan="2" class="form_label">Solicitante</td>
            </tr>

            <tr>
                <td colspan="2" class="form_label">
                    <input type="text" size="45" id="solicitante" name="forfornecedor" class="form_field" value="<?php echo (isset($_POST['forfornecedor']) ? $_POST['forfornecedor'] : ""); ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center" class="rodape">
                    <input type="button" id="bt_pesquisar" name="bt_pesquisar" value="Pesquisar" class="botao" style="width: 90px !important;" />
                    <input id="bt_limpar" type="button" value="Limpar" class="botao" style="width: 70px !important;" />
                    <input type="button" name="bt_novo" value="Novo" onclick="javascript:def_acao('novo');" class="botao" style="width: 60px !important;"/>
                </td>
            </tr>
        </table>