<script src="lib/js/highcharts/highcharts.js"></script>
<script src="lib/js/highcharts/modules/exporting.js"></script>

<div style='width: 100%; margin: 0 auto; text-align: center;'>
    <table style='margin: 0 auto;'>
        <tr>
            <?php foreach ($this->view->graficos as $key => $dados) : ?>

                <?php if (empty($dados)) continue; ?>

                <td style='border: thin solid;'>

                    <script type="text/javascript">

                        jQuery(function() {

                            var dados = <?php echo $dados ?>;
                            var chartEvolucao;

                            var optionsEvolucao = {
                                chart: {
                                    zoomType: 'x',
                                    renderTo: 'grafico<?php echo $key ?>',
                                    height: 437,
                                    width: 500
                                },
                                title: {
                                    text: '<?php echo $this->view->titles[$key]['title'] ?>'
                                },
                                subtitle: {
                                    text: '<?php echo $this->view->titles[$key]['subtitle'] ?>'
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

                            //Create the chart
                            chartEvolucao = new Highcharts.Chart(optionsEvolucao, function(chart) {
                                // on complete
                                chart.renderer.image(dados.imgDirecao, 65, 30, 20, 30).add();
                            });

                        });
                    </script>

                    <div id="grafico<?php echo $key ?>"></div>


                </td>
                <?php if (( ($key + 1) % 2 ) == 0) : ?>
                </tr>
                <tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    </table>
</div>