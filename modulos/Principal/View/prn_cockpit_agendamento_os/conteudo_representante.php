<?php
if (isset($this->view->parametros->representante)) {
    $representante = $this->view->parametros->representante;
} else {
    $representante = new stdClass();
}
?>

<div class="bloco_titulo">Dados do Cadastro</div>
<div class="bloco_conteudo">
    <div class="conteudo">
        <table width="90%">
            <tbody>
                <tr>
                    <td id="td_nome" class="label menor">Nome</td>
                    <td colspan="3"><?php echo isset($representante->nome) ? $representante->nome : ''; ?></td>
                    <td id="td_documento" class="label menor">CNPJ</td>
                    <td><?php echo empty($representante->cnpj) ? '' : formata_cgc_cpf($representante->cnpj); ?></td>
                </tr>
                <tr>
                    <td id="td_endereco" class="label menor">Endereço</td>
                    <td colspan="5">
                        <?php
                        echo isset($representante->endereco) ? $representante->endereco : '';
                        echo empty($representante->enderecoNumero) ? '' : ', '.$representante->enderecoNumero;
                        echo empty($representante->enderecoComplemento) ? '' : ' - '.$representante->enderecoComplemento;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="td_cidade" class="label">Cidade</td>
                    <td><?php echo isset($representante->cidade) ? $representante->cidade : ''; ?></td>
                    <td id="td_estado" class="label menor">Estado</td>
                    <td class="menor"><?php echo isset($representante->estado) ? $representante->estado : ''; ?></td>
                    <td id="td_telefone_cliente" class="label menor">Telefone</td>
                    <td class="medio"><?php echo isset($representante->telefone) ? $representante->telefone : ''; ?></td>
                </tr>
                <tr>
                    <td id="td_contato" class="label">Contato</td>
                    <td><?php echo isset($representante->contato) ? $representante->contato : ''; ?></td>
                    <td id="td_telefone_contato" class="label">Telefone</td>
                    <td><?php echo isset($representante->contatoTelefone) ? $representante->contatoTelefone : ''; ?></td>
                    <td id="td_email" class="label">E-mail</td>
                    <td><?php echo isset($representante->email) ? $representante->email : ''; ?></td>
                </tr>
                <tr>
                    <td id="td_funcoes" class="label">Funções</td>
                    <td colspan="2"><?php echo isset($representante->funcao) ? $representante->funcao : ''; ?></td>
                    <td class="label"></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="bloco_acoes">
    <button id="btn_fechar_janela" type="button">Fechar Janela</button>
</div>