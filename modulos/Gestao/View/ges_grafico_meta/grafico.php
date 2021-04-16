<script src="lib/js/highcharts/highcharts.js"></script>
<script src="lib/js/highcharts/modules/exporting.js"></script>

<script type="text/javascript">

    jQuery(document).ready(function() {

        var dados = <?php echo $this->view->graficoEvolucao ?>;

        var chartEvolucao;

        var optionsEvolucao = {
            chart: {
                zoomType: 'x',
                renderTo: 'evolucao',
                height: 435
            },
            title: {
                text: 'Evolução'
            },
            xAxis: {
                categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
            },
            yAxis: {
                title: {
                    text: dados.lblMetrica
                }
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                column: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        y: -20,
                        x: -6,
                        color: '#006699',
                        zIndex: 10,
                        formatter: function() {
                            return (this.y % 1 != 0) ? this.y.toFixed(dados.precisao) : this.y;
                        }
                    }
                },
                spline: {
                    dashStyle: 'LongDashDot'
                }
            },
            series: [{
                    type: 'column',
                    name: 'Valor Realizado',
                    data: dados.mesValor,
                    zIndex: 3
                }, {
                    type: 'spline',
                    name: 'Target',
                    data: dados.limite,
                    lineColor: 'black',
                    dashStyle: 'Solid',
                    dataLabels: {
                        enabled: true,
                        x: 20,
                        color: '#000000',
                        rotation: -90,
                        y: -20,
                        formatter: function() {
                            return (this.y % 1 != 0) ? this.y.toFixed(dados.precisao) : this.y;
                        }
                    },
                    marker: {
                        lineWidth: 1,
                        lineColor: 'black',
                        fillColor: 'black',
                        radius: 3,
                        symbol: 'square'
                    },
                    zIndex: 4
                }, {
                    type: 'spline',
                    name: 'Desafio',
                    data: dados.limiteSuperior,
                    lineColor: 'green',
                    marker: {
                        lineWidth: 1,
                        radius: 0
                    },
                    zIndex: 2
                }, {
                    type: 'spline',
                    name: 'Limite Inferior',
                    data: dados.limiteInferior,
                    lineColor: 'red',
                    marker: {
                        lineWidth: 1,
                        radius: 0
                    },
                    zIndex: 2
                }]
        };

        var chartMedia;

        var optionsMedia = {
            chart: {
                renderTo: 'media',
                height: 400
            },
            title: {
                text: 'Consolidado'
            },
            xAxis: {
                categories: [dados.tipo + ' (' + dados.lblMetrica + ')']
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            credits: {
                enabled: false
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                column: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        y: -25,
                        color: '#006699',
                        zIndex: 10,
                        formatter: function() {
                            return (this.y % 1 != 0) ? this.y.toFixed(dados.precisao) : this.y;
                        }
                    }
                },
                spline: {
                    dashStyle: 'LongDashDot'
                }
            },
            colorByPoint: true,
            colors: ['#000000', '#2F7ED8'],
            series: [{
                    type: 'column',
                    data: [dados.projeto]
                }, {
                    type: 'column',
                    data: [dados.resultado]
                }]
        }

        setTimeout(function() {
            
            jQuery('#loader').hide();
            
            //Create the chart
            chartEvolucao = new Highcharts.Chart(optionsEvolucao, function(chart) {
                // on complete
                chart.renderer.image(dados.imgDirecao, 100, 0, 30, 40).add();
            });

            chartMedia = new Highcharts.Chart(optionsMedia);
        }, 1000);

    });
</script>

<div class ="area_grafico">

    <div class="grafico_meta">
        <style>

            .cabecalho {
                text-align: center;
                color: #2F7ED8;
            }

            .cabecalho .titulo {
                font-size: 14px;   
                font-weight: bold;
            }

            .cabecalho .subtitulo {
                font-size: 12px;            
            }

            #evolucao {
                width: 75%;
                margin-right: 3%;
                float: left;                
            }

            #media {
                width: 20%;                
                float: left;                
            }

        </style>


        <div class="cabecalho">
            <img src="<?php echo _PROTOCOLO_ . _SITEURL_ ?>images/logo_sascar_nova.jpg" width="300" />
            <p class="titulo"><?php echo $this->view->titulo; ?></p>
            <p class="subtitulo"><?php echo $this->view->subtitulo; ?></p>
        </div>

        <div style="clear: both"></div>
        <div id="loader">
            <div class="separador"></div>
            <div class="carregando"></div>
            <div class="separador"></div>
        </div>
        <div id="evolucao"></div>     
        <div id="media"></div>   

        <div style="clear: both"></div>
    </div>
    <div class="bloco_acoes" style="border-top: 1px solid #94ADC2 !important;">
        <button type="button" id="bt_imprimir">Imprimir</button>
    </div>
    <div class="separador"></div>

    <div class="bloco_titulo"><?php echo $this->view->complementares->gmenome; ?></div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <table class="tabela">
                <tr>
                    <td class="coluna_label coluna_label">Limite Inferior:</td>
                    <td><?php echo $this->view->complementares->gmelimite_inferior; ?> %</td>
                </tr>

                <tr>
                    <td class="coluna_label">Target:</td>
                    <td><?php echo $this->view->complementares->gmelimite; ?> %</td>
                </tr>
                <tr>
                    <td class="coluna_label">Desafio:</td>
                    <td><?php echo $this->view->complementares->gmelimite_superior; ?> %</td>
                </tr>
            </table>

        </div>
    </div>
    <div class="separador"></div>

    <div class="bloco_titulo">Propriedades</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <table class="tabela">
                <tr>
                    <td class="coluna_label">Compartilhado:</td>
                    <td><?php echo $this->view->complementares->compartilhado; ?></td>
                </tr>
                <tr>
                    <td class="coluna_label">Respons&aacute;vel:</td>
                    <td><?php echo $this->view->complementares->funnome; ?></td>
                </tr>
                <tr>
                    <td class="coluna_label">&Uacute;ltima atualiza&ccedil;&atilde;o:</td>
                    <td>N/D</td>
                </tr>
                <tr>
                    <td class="coluna_label">Per&iacute;odo:</td>
                    <td><?php echo $this->view->tipos[$this->view->complementares->periodo]; ?></td>
                </tr>
                <tr>
                    <td class="coluna_label">Precis&atilde;o:</td>
                    <td>
                        <?php
                        echo $this->view->complementares->gmeprecisao;
                        echo " (" . $this->view->complementares->direcao . ")";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="coluna_label">Unidade:</td>
                    <td>
                        <?php
                        echo $this->view->metricas[$this->view->complementares->gmemetrica][1];
                        echo " - " . $this->view->metricas[$this->view->complementares->gmemetrica][0]
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="coluna_label">C&oacute;digo:</td>
                    <td><?php echo $this->view->complementares->gmecodigo; ?></td>
                </tr>
                <tr>
                    <td class="coluna_label">C&aacute;lculo:</td>
                    <td><?php echo $this->view->complementares->gmeformula; ?></td>
                </tr>
                <tr>
                    <td class="coluna_label">Peso:</td>
                    <td><?php echo $this->view->complementares->gmepeso; ?></td>
                </tr>
            </table>

        </div>
    </div>
    <div class="separador"></div>

    <div class="bloco_titulo">Planos de A&ccedil;&atilde;o</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <table class="tabela">
                <tr>
                    <td class="coluna_label">Quantidade Total:</td>
                    <td><?php echo $this->view->complementares->qtde_plano_acao; ?></td>
                </tr>
            </table>

        </div>
    </div>
    <div class="separador"></div>
</div>
