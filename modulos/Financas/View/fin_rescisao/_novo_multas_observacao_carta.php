<? if ($retorno): ?>
    <div class="bloco_titulo">Observação da carta de rescisão</div>
    
    <div class="bloco_conteudo">  
        <div>
            <table>
                <tbody>
                    <tr>
                        <td><input type="radio" name="observacao_carta"
                                class="observacao_carta" id="observacao_carta_1" checked="checked"
                                value="Será reenviado novo carnê com os devidos descontos. 
                            Pedimos a gentileza de desconsiderar o carnê atual e 
                            efetuar os pagamentos somente com o novo carnê que será encaminhado." />
                        </td>
                        <td>
                            <label for="observacao_carta_1">
                                Será reenviado novo carnê com os devidos descontos. 
                                Pedimos a gentileza de desconsiderar o carnê atual e 
                                efetuar os pagamentos somente com o novo carnê que será encaminhado.
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><input type="radio" name="observacao_carta"
                                class="observacao_carta" id="observacao_carta_2"
                                value="Pedimos a gentileza de não mais realizar pagamentos referente 
                            as parcelas dos carnês acima mencionados visto que já foram 
                            baixadas pela Sascar." />
                        </td>
                        <td>
                            <label for="observacao_carta_2">
                                Pedimos a gentileza de não mais realizar pagamentos referente 
                                as parcelas dos carnês acima mencionados visto que já foram 
                                baixadas pela Sascar.
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="separador"></div>
<? endif ?> 