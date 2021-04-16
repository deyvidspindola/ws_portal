<?php
require_once '_abas.php'; 
ob_start();
ini_set("display_errors", 1);
ini_set('session.bug_compat_warn', 'off');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//INCLUDES
include 'lib/config.php';
include 'lib/funcoes.php';
//include 'lib/init.php';
//A cobrança do título da taxa de instalação é cobrada na classe FinFaturamentoCartaoCredito
require_once _MODULEDIR_ . 'Financas/Action/FinFaturamentoCartaoCredito.class.php';
require_once 'xajax/xajax.inc.php';
$finFaturamentoCartaoCredito = new FinFaturamentoCartaoCredito();
$xajax = new xajax();
$xajax->setCharEncoding("ISO-8859-1");
include 'includes/ajax/pre_cadastro.xajax.php';
/** Importante para formatar os campos de CNPJ e CPF
 * Formata campos vindos do HTML para salvamento em BD
 */
function salvar($tipo, $valor) {

    $search = array(".", "/", "-", "(", ")", " ");
    $replace = array("", "", "", "", "", "");
    $tipos_limpar = array("cpf", "cnpj", "fone");

    $valor = str_replace($search, $replace, $valor);

    if ($tipo == 'cpf' || $tipo == 'cnpj') {
        $tam = strlen($valor);
        if ($tam > 0) {
            for ($t = 0; $t < $tam; $t++) {
                if (substr($valor, 0, 1) == "0") {
                    $valor = substr($valor, 1, (strlen($valor) - 1));
                }
            }
        }
    }

    return $valor;
}
/** Importante para aparecer os campos
 * Formata campos vindos do BD para exibição na tela
 */
function exibir($tipo, $valor) {
    if ($tipo == "cpf" && $valor != "") {
        $valor = str_pad($valor, 11, "0", STR_PAD_LEFT);
        return $valor = substr($valor, 0, 3) . "." . substr($valor, 3, 3) . "." . substr($valor, 6, 3) . "-" . substr($valor, 9, 2);
    }
    if ($tipo == "cnpj" && $valor != "") {
        $valor = str_pad($valor, 14, "0", STR_PAD_LEFT);
        return $valor = substr($valor, 0, 2) . "." . substr($valor, 2, 3) . "." . substr($valor, 5, 3) . "/" . substr($valor, 8, 4) . "-" . substr($valor, 12, 2);
    }
}
$msg = '';
//sql para verificação de existencia de campanha
$cfp_query = "SELECT
                    cfcpoid
                FROM
                    credito_futuro_campanha_promocional
                INNER JOIN
                    credito_futuro_motivo_credito ON cfmcoid = cfcpcfmccoid AND cfmctipo = 2
                WHERE
                    TRUE
					AND NOW()::timestamptz >= ((cfcpdt_inicio_vigencia)::text || ' 00:00:00')::timestamptz
					AND NOW()::timestamptz <= ((cfcpdt_fim_vigencia)::text || ' 23:59:59')::timestamptz
					AND cfcpdt_exclusao IS NULL";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>SASCAR Tecnologia e Segurança Automotiva</title>
    <link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css"/>
    <link type="text/css" rel="stylesheet" href="includes/css/calendar.css"/>
    <link type="text/css" rel="stylesheet" href="lib/css/style.css"/>
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js" ></script>
    <script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
    <script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
    <script type="text/javascript" src="lib/js/bootstrap.js"></script>
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/calendar.js"></script>
    <script type="text/javascript" src="modulos/web/js/fin_nfe_kernel.js?v=1.0.1"></script>
    <style type="text/css">
        .formulario div.mensagem { margin-left:0; margin-right:0; }
    </style>
    <style type="text/css">
        <!--
        @import url("includes/css/base_form.css");
        -->
    </style>
    <script language="javascript" type="text/javascript" src="includes/js/mascaras.js"></script>
    <script language="javascript" type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script language="Javascript" type="text/javascript" src="modulos/web/js/pre_cadastro.js"></script>
    <?php $xajax->printJavascript(); ?>
    <!-- MOmento de importação do pre_cadastro.js -->
    <script type="text/javascript">
        <?php
        include_once 'includes/js/pre_cadastro.js';
        ?>
    </script>
    <style type="text/css">
        label {
            white-space:nowrap;
        }
        td {
            height:18px;
        }
    </style>
</head>
<div class="bloco_titulo">Dados Pesquisa</div>

<form id="form" name="form" action="corrige_valor_item.php?acao=pesquisar" method="post">
    <input type="hidden" name="acao" id="form_acao" value="pesquisar">

    <div class="bloco_conteudo">
        <div class="formulario">
            <?php if($this->acao == 'pesquisar'): ?>
                <?php if(!empty($this->msgInfo)): ?><div class="mensagem info"><?php echo $this->msgInfo; ?></div><?php endif; ?>
                <?php if(!empty($this->msgAlerta)): ?><div class="mensagem alerta"><?php echo $this->msgAlerta; ?></div><?php endif; ?>
                <?php if(!empty($this->msgSucesso)): ?><div class="mensagem sucesso"><?php echo $this->msgSucesso; ?></div><?php endif; ?>
                <?php if(!empty($this->msgErro)): ?><div class="mensagem erro"><?php echo $this->msgErro; ?></div><?php endif; ?>
            <?php endif; ?>

            <!--
                Caixa onde Apresenta o CPF e CNPJ e faz a busca se tem o Cliente Cadastrado no PréCadastro
            -->
            <div class="campo maior">
                <label for="numero_cpf_cnpj" style="margin-left: 1px;">CPF/CNPJ (*)</label>
                <input style="width:190px;" type="text" name="numero_cpf_cnpj" id="numero_cpf_cnpj" value="<?php echo !empty($this->numeroCpfCnpj) ? $this->numeroCpfCnpj : null; ?>" class="campo" />
            </div>
            <div class="campo maior">
                <label style="margin-left: -184px !important;" for="atual_titular">Atual Titular</label>
                <input style="margin-left: -184px !important;" type="text" name="atual_titular" id="atual_titular" value="<?php echo !empty($this->atualTitular) ? $this->atualTitular : null; ?>" class="campo" />
            </div>
            <div class="campo menor">
                <label style="margin-left: -175px !important;" for="numero_termo_contrato">Nº Termo/Contrato</label>
                <input style="margin-left: -175px !important;" type="text" name="numero_termo_contrato" id="numero_termo_contrato" value="<?php echo !empty($this->numeroTermoContrato) ? $this->numeroTermoContrato : null; ?>" class="campo" />
            </div>
            <div class="campo menor">
                <label style="margin-left: -178px !important;" for="numero_placa">Placa</label>
                <input style="margin-left: -178px !important;" type="text" name="numero_placa" id="numero_placa" value="<?php echo !empty($this->numeroPlaca) ? $this->numeroPlaca : null; ?>" class="campo" />
            </div>

            <div class="clear"></div>

            <div class="campo maior">
                <label for="classe_contrato" style="margin-left: 1px;">Classe do Contrato:</label>
                <select style=" width:300px;" name="classe_contrato" id="classe_contrato">
                    <option value="todos">Escolha</option>
                    <option value="todos" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Todos</option>
                    <option value="sascar_full" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Sascar Full</option>
                    <option value="sascar_full_sat" <?php echo !empty($this->classeContrato) && $this->classeContrato == "1" ? 'selected' : null; ?>>Sascar Full SAT 1000</option>
                </select>
            </div>
            <div class="campo menor">
                <label for="numero_resultados" style="margin-left: -70px;">Mostrar:</label>
                <select name="numero_resultados" id="numero_resultados" style="margin-left: -70px; width:100px;">
                    <option value="all">Escolha</option>
                    <option value="10" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "10" ? 'selected' : null; ?>>10 registros</option>
                    <option value="25" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "25" ? 'selected' : null; ?>>25 registros</option>
                    <option value="50" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "50" ? 'selected' : null; ?>>50 registros</option>
                    <option value="100" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "100" ? 'selected' : null; ?>>100 registros</option>
                    <option value="300" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "300" ? 'selected' : null; ?>>300 registros</option>
                    <option value="500" <?php echo !empty($this->numeroResultados) && $this->numeroResultados == "500" ? 'selected' : null; ?>>500 registros</option>
                </select>
            </div>
            <div class="campo menor">
                <label for="ordena_resultados" style="margin-left: -80px;">Ordenar:</label>
                <select name="ordena_resultados" id="ordena_resultados" style="margin-left: -80px; width:146px;">
                    <option value="contrato">Escolha</option>
                    <option value="inicio_vigencia" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "vigencia" ? 'selected' : null; ?>>Data de Vigência</option>
                    <option value="contrato" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "contrato" ? 'selected' : null; ?>>Nº Termo/Contrato</option>
                    <option value="placa" <?php echo !empty($this->ordenaResultados) && $this->ordenaResultados == "placa" ? 'selected' : null; ?>>Placa</option>
                </select>
            </div>
            <div class="campo menor">
                <label for="classifica_resultados" style="margin-left: -44px;">Classificar:</label>
                <select name="classifica_resultados" id="classifica_resultados" style="margin-left: -45px; width:100px;">
                    <option value="asc">Escolha</option>
                    <option value="asc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "asc" ? 'selected' : null; ?>>Ascendente</option>
                    <option value="desc" <?php echo !empty($this->situacaoRps) && $this->situacaoRps == "desc" ? 'selected' : null; ?>>Descendente</option>
                </select>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="bloco_acoes">
        <button type="submit">Pesquisar</button>
    </div>
</form>
<br><br>ANOR FABIO DA CUNHA
<br>63638053920
<br><br>GRYCAMP
<br>38769002000112
<br><br>IBANOR JOSE DESCONSI
<br>10653520930
<br><br>ACCESS TRANSPORTADORA LTDA - ME
<br>08519414000133
<div class="separador"></div>

<?php if($this->acao == 'pesquisar'): ?>
<div class="bloco_titulo">Resultados Encontrados</div>
<div class="bloco_conteudo">
    <div class="listagem">

        <form id="form_listagem" name="form_listagem" action="corrige_valor_item.php?acao=novoTitular" method="post">
            <table>
                <?php #if($listContratos && count($listContratos)): ?>
                <thead>
                <tr>
                    <th style="text-align: center;"></th>
                    <th style="text-align: center;">Inicio de Vigência</th>
                    <th style="text-align: center;">Nº Termo/Contrato</th>
                    <th style="text-align: center;">Placa</th>
                    <th style="text-align: center;">Tipo do Contrato</th>
                    <th style="text-align: center;">Classe do Contrato</th>
                    <th style="text-align: center;">Locação</th>
                    <th style="text-align: center;">Acessórios</th>
                    <th style="text-align: center;">Monitoramento</th>
                    <th style="text-align: center;">Valor Total</th>
                    <th style="text-align: center;">Status</th>
                </tr>
                </thead>
                <?php if(!empty($this->contratos)): ?>
                    <tbody>
                    <?php foreach($this->contratos as $contrato): ?>
                        <tr class="par">
                            <td align="center">
                                <input type="checkbox" class="checkbox_nf" name="notas_fiscais[]" value="<?php echo $nf['id_nf']; ?>">
                            </td>

                            <td style="text-align: center;"><?php echo $contrato['inicio_vigencia']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['numero_contrato']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['placa_veiculo']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['tipo_contrato']; ?></td>
                            <td><?php echo $contrato['classe_contrato']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['status_contrato']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['status_contrato']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['status_contrato']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['status_contrato']; ?></td>
                            <td style="text-align: center;"><?php echo $contrato['status_contrato']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>

                <tfoot>
                <?php if(!empty($this->contratos)): ?>

                    <tr class="tableRodapeModelo3">
                        <td align="left" colspan="11">
                            <input type="checkbox" name="selecionarTodasNFs" id="selecionarTodasNFs">
                        </td>
                    </tr>

                    <tr class="tableRodapeModelo3">
                        <td colspan="100%" align="center">
                            <input type="submit" value="Salvar" class="botao" style="width:100px;">
                        </td>
                    </tr>

                    <tr><td colspan="11" style="text-align: center;"><?php echo count($this->contratos); ?> registro(s) encontrado(s)</td></tr>
                <?php else: ?>
                    <tr><td colspan="11" style="text-align: center;">Nenhum resultado encontrado.</td></tr>
                <?php endif; ?>
                </tfoot>
            </table>
        </form>

    </div>

</div>
<?php endif; ?>