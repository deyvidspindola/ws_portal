<? require_once '_header.php' ?>
<style type="text/css">
    div.listagem table td.agrupamento {
        font-weight: bold;
        text-align: center;
        background: #bad0e5;
    }
</style>
<div class="mensagem alerta invisivel" id="mensagem_alerta"></div>
<div class="bloco_titulo">Nova Rescisão</div>
<div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo pesquisaMaior">
                <form id="cliente_form" name="cliente_form">
                    <label for="cliente">Cliente</label>
                    <input type="text" id="cliente" name="cliente" value="" class="campo" readonly="readonly" />
                    <input type="hidden" id="clioid" name="clioid" value="" />
                    <button type="button" class="busca-cliente">Pesquisar</button>
                </form>
            </div>
            <div class="clear"></div>

            <div class="campo menor">
                <label for="campo">Contrato</label>
                <input type="text" class="campo" id="connumero" name="connumero" value="" />
            </div>
            <div class="clear"></div>

            <!-- div class="campo data">
                <label for="resmfax">Data da Solicitação</label>
                <input type="text" class="campo" id="resmfax" name="resmfax" value="" />
            </div -->
        </div>


</div>

<div class="bloco_acoes">
    <button type="submit" id="pesquisar-contratos">Pesquisar Contratos</button>
    <button type="button" id="retornar-novo-contrato">Retornar</button>
</div>

<div class="container-contratos-loader container-loader"><div class="loader"></div></div>
<div class="container-contratos"></div>

<div class="container-multas-loader container-loader"><div class="loader"></div></div>
<div class="container-multas"></div>

<div class="container-finalizacao-loader container-loader"><div class="loader"></div></div>

<? require_once '_footer.php' ?>