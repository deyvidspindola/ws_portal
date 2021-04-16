<div id="tabela_creditos_concedidos" style="display:none">
    <div class="separador"></div>
    <div class="bloco_titulo">Nota Fiscal com Desconto</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <tbody id="conteudo_creditos_concedidos"></tbody>
            </table>
        </div>
    </div>

</div>



<div id="parametrizacao_parcelas" style="display:none">
    <div class="separador"></div>

    <div id="alertaParcelasValor" class="mensagem alerta invisivel"></div>

    <div id="alertaParcelasData" class="mensagem alerta invisivel"></div>

    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th  style="text-align: center; width: 10%;">Parcela</th>
                        <th  style="text-align: center; width: 45%;">Data Vencimento</th>
                        <th  style="text-align: center; width: 45%;">Valor</th>
                    </tr>
                </thead>

                <tbody id="conteudo_parcelas"></tbody>
                
                <tfoot id="conteudo_footer"></tfoot>

            </table>
        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="bt_gerar_nf_nova">Gerar nota fiscal</button>
    </div>
</div>