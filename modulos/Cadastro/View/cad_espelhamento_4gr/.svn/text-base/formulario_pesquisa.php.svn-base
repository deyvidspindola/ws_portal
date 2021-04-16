<div class="bloco_titulo">Cadastro de 4° GR</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <!-- Gerenciadora -->
        <div class="campo grande">
            <label for="gerenciadora">Gerenciadora</label>
            <select id="gerenciadora" name="gerenciadora">
                <option value="0">Selecione a gerenciadora</option>
                <?php foreach($this->view->gerenciadoras as $gerenciadora): ?>
                <option value="<?php echo $gerenciadora->geroid; ?>" <?php echo isset($this->param->gerenciadora) && $this->param->gerenciadora == $gerenciadora->geroid ? 'selected' : ''; ?>><?php echo $gerenciadora->descricao; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo grande">
            <label id="lbl_name_4gr" for="name_4gr">Nome da 4° GR</label>
            <input size="150" id="name_4gr" name="name_4gr" value="" class="campo" type="text">
            <input id="id_4gr" name="id_4gr" value="" type="text" hidden>
        </div>
        <div class="clear"></div>
        
        <div class="campo medio">
            <label id="lbl_user_4gr" for="user_4gr">Usuário</label>
            <input id="user_4gr" name="user_4gr" value="" class="campo" type="text">
        </div>
        
        <div class="campo medio">
            <label id="lbl_password_4gr" for="user_4gr">Senha</label>
            <input id="password_4gr" name="password_4gr" value="" class="campo" type="text">
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label>Tipo</label>
            <select id="tipo" name="tipo">
                <option value="0">Selecione o tipo</option>
                <option value="4">4°GR</option>
                <option value="5">4°GR - FULL</option>
            </select>
        </div>
        <div class="clear"></div>
       
        <div class="campo maior" id="cli_input">
            <label id="lbl_search" for="search">Pesquisar Clientes</label>
            <input id="search" name="search" value="" class="campo" type="text">
        </div>
        <div class="campo maior" style="margin-top: 21px" id="cli_bt">
            <button type="button" name="bt_pesquisar" id="bt_pesquisar">Pesquisar</button>
        </div>
        <div class="clear"></div>

        <!-- Cliente -->
        <div id="clientes">
            <label>Cliente</label>
            <select id="cliente" name="cliente"></select>
            <button type="button" name="bt_add" id="bt_add">Adicionar</button>
        </div>
        <div class="clear"></div>
        <br>
        
        <div id="listagem" class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro" style="width: 2%"></th>
                        <th class="maior centro" style="width: 80%">Cliente</th>
                        <th class="maior centro">Tipo</th>
                        <th class="maior centro">Documento</th>
                        <th class="maior centro">Remover</th>
                    </tr>
                </thead>
                <tbody id="tb_cliente"></tbody>
            </table>
        </div>

        <div id="list_consulta" class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="centro" style="width: 2%"></th>
                        <th class="maior centro" style="width: 80%">Cliente</th>
                        <th class="maior centro">Tipo</th>
                        <th class="maior centro">Usuário</th>
                    </tr>
                </thead>
                <tbody id="tb_consulta"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_cliente">Clientes</button>
    <button type="button" id="bt_cadastrar">Cadastrar</button>
    <button type="button" id="bt_consulta">Consultar</button>
    <button type="button" id="bt_salvar">Salvar</button>
    <button type="button" id="bt_limpar">Limpar</button>
    <button type="button" id="bt_remover">Remover</button>
</div>