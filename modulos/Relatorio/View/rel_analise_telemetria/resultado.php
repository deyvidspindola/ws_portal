
<div id="menu" style="margin: 20px; margin-bottom: 0px;">
    <span><b>Menu</b></span>
    <a href="#dados_gerais"><button>Resultado Geral</button></a>
    <a href="#dados_telemetria"><button>Dados Telemetria</button></a>
    <a href="#dados_delta"><button>Dados Delta</button></a>
    <a href="#dados_motoristas"><button>Motoristas Logados</button></a>
    <a href="#resumo_mensal"><button>Resumo Mensal (Dados)</button></a>
    <a href="#resumo_mensal_eventos"><button>Resumo Mensal (Eventos)</button></a>
</div>

<div id="dados_gerais">
    <?php include('dados_gerais.php'); ?>
</div>

<div id="dados_telemetria">
    <?php include('dados_telemetria.php'); ?>
</div>

<div id="dados_delta">
    <?php include('dados_delta.php'); ?>
</div>

<div id="dados_motoristas">
    <?php include('dados_motoristas.php'); ?>
</div>

<div id="resumo_mensal">
    <?php include('resumo_mensal.php'); ?>
</div>

<div id="resumo_mensal_eventos">
    <?php include('resumo_mensal_eventos.php'); ?>
</div>