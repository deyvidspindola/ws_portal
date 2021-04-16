<?php

/**
 * Camada de negócio para a produção de gráfico e resultados em tela
 *
 * @package Gestao
 * @author andre.zilz <andre.zilz@meta.com.br>
 */
class GesGraficoMeta {
    /*
     * Objeto de referência para a classe DAO
     */

    private $dao;

    /*
     * Objeto de referência para o layout filho
     */
    private $view;
    private $consolidado;
    private $layout;

    /**
     * Construtor da Classe
     * @param GesGraficoMetaDAO $dao
     * @param type $layout
     */
    public function __construct(GesGraficoMetaDAO $dao, GesLayout $layout) {

        $this->dao = $dao;
        $this->view = new stdClass();
        $this->view->mensagem = new stdClass();
        $this->param = new stdClass();
        $this->consolidado = new stdClass();
        $this->view->dados = null;
        $this->view->metricas = array(
            'V' => array('Valor', 'Vlr'),
            'P' => array('Percentual', '%'),
            'M' => array('Moeda', '$')
        );
        $this->view->tipos = array(
            'D' => 'Diário',
            'M' => 'Mensal',
            'B' => 'Bimestral',
            'T' => 'Trimestal',
            'Q' => 'Quadrimestral',
            'S' => 'Semestral',
            'A' => 'Anual'
        );

        $this->view->caminho = _MODULEDIR_ . 'Gestao/View/ges_grafico_meta/';

        $this->layout = $layout;

        $this->tratarParametros();
    }

    /**
     * Recupera os dados enviados pelo AJAX
     *
     * @return Void
     */
    private function tratarParametros() {

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->param->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (!isset($this->param->$key)) {
                    $this->param->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
    }

    /**
     * Trata os parâmetros utilizados em tela
     *
     * @return void
     */
    private function inicializarParametros() {

        $this->param->acao = isset($this->param->acao) ? $this->param->acao : '';
        $this->param->metaid = isset($this->param->metaid) ? $this->param->metaid : '';
        $this->param->ano = isset($this->param->ano) ? $this->param->ano : '';
    }

    /**
     * Executa os métodos responsáveis pelo processo
     */
    public function iniciarProcesso() {

        try {
            $this->inicializarParametros();

            $this->view->complementares = $this->dao->buscarDadosComplementares($this->param->metaid, $this->param->ano);
            $indicadores = $this->dao->buscarDadosIndicadores($this->param);
            $this->view->indicadores = $indicadores['formatado'];
            $this->view->indicadoresVerificacaoFormula = $indicadores['verificacaoFormula'];
            $this->view->dados = $this->dao->buscarDadosGrafico($this->param);
            $this->calcularFormula();

            if (empty($this->view->dados)) {
                $this->view->mensagem->alerta = "Não há dados suficientes para o gráfico desta meta.";
            } else {
                $this->view->graficoEvolucao = $this->gerarGrafico($this->view->dados);
            }
        } catch (Exception $e) {
            $this->view->mensagem->erro = $e->getMessage();
        }
       
        include $this->view->caminho . 'index.php';
    }

    /**
     * Valida e calcula o resultado da formula
     */
    private function calcularFormula() {

        foreach ($this->view->dados as $dado) {

            $formula = $dado->gmeformula;
            $this->view->direcao = $dado->gmedirecao;
            $formulaValida = true;

            //Verifica se indicadores existem na fórmula
            foreach ($this->view->indicadoresVerificacaoFormula as $nomeIndicador => $tipoIndicador) {

                $existeIndicador = strpos($dado->gmeformula, $nomeIndicador);

                if (gettype($existeIndicador) == 'boolean' && $tipoIndicador == 'I') {
                    $formulaValida = false;
                    break;
                }
            }

            if ($formulaValida) {
                //substitui valores na fórmula
                foreach ($this->view->indicadores['indicadores'] as $indicador) {

                    if (!empty($this->view->indicadores[$dado->mes][$indicador])) {
                        $formula = preg_replace("/\[$indicador\]/", ($this->view->indicadores[$dado->mes][$indicador] == 0 ? 1 : $this->view->indicadores[$dado->mes][$indicador]), $formula);
                    } else {
                        $formulaValida = false;
                    }
                }
            }

            //Executa a fórmula
            if ($formulaValida) {
                $formula = preg_replace("/[^0-9\/*+-,.()%]/", "", $formula);
                eval('$dado->valorMes=' . $formula . ';');
            } else {
                $dado->valorMes = 0;
            }
        }
    }

    /**
     * Renderiza o grafico para exibição em tela
     *
     * @param stdClass $dados
     */
    private function gerarGrafico($dados) {
        
        $mesValor = array();
        $limiteSupValor = array();
        $limiteInfValor = array();
        $limiteValor = array();
        $totalLimite = 0.0;
        $totaMesValor = 0.0;
        $totalMeses = 0;

        //dados barras e linhas
        foreach ($dados as $chave => $dado) {

            //$mesValor[] = number_format($dado->valorMes, intval($dado->gmeprecisao), '.', '');
            
            $mesValor[intval($dado->mes)] = $dado->valorMes;

            //Se for a primeira iteração atribui valores à  variáis específicas
            if ($chave == 0) {
                $this->view->titulo = $dado->gmenome . " em " . $dado->gmeano;
                $this->view->subtitulo = $dado->funnome;
                //$limiteAnterior = ( $dado->gmemetrica == 'P') ? floatval($dado->gmelimite) : floatval($dado->previsto);
                $metrica = $dado->gmemetrica;
                $precisao = $dado->gmeprecisao;
            }
            $limiteAnterior = floatval($dado->previsto);
            $previstoAnterior = floatval($dado->previsto);
            //Calculo da linha Limite            
            if($previstoAnterior > 0) {
                $limiteAnterior = ((floatval($dado->previsto) * $limiteAnterior) / $previstoAnterior);
            } else {
                $limiteAnterior = 0;
            }
            
            $limiteValor[intval($dado->mes)] = $limiteAnterior;

            $limiteSupValor[intval($dado->mes)] = ($limiteAnterior * floatval($dado->gmelimite_superior) ) / (floatval($dado->gmelimite) == 0 ? 1 : floatval($dado->gmelimite));

            $limiteInfValor[intval($dado->mes)] = ($limiteAnterior * floatval($dado->gmelimite_inferior) ) / (floatval($dado->gmelimite) == 0 ? 1 : floatval($dado->gmelimite));

            $previstoAnterior = floatval($dado->previsto);

            if ($dado->valorMes > 0) {
                $totalLimite += $limiteAnterior;
            }

            $totaMesValor += $dado->valorMes;

            if ($dado->valorMes > 0) {
                $totalMeses++;
            }
        }

        //Valores para o uso do gráfico Consolidado
        $totalMeses = ($totalMeses == 0) ? 1 : $totalMeses;

        if ($dados[0]->gmemetrica != 'P') {
            $this->consolidado->projeto = $totalLimite;
            $this->consolidado->resultado = $totaMesValor;
            $this->consolidado->tipo = "Soma";
        } else {
            $this->consolidado->projeto = ($totalLimite / $totalMeses);
            $this->consolidado->resultado = ($totaMesValor / $totalMeses);
            $this->consolidado->tipo = utf8_encode("Média");
        }

        //Trata meses inexistentes
        for ($i = 1; $i < 12; $i++) {
            $mesValor[$i] = isset($mesValor[$i]) ? $mesValor[$i] : 0;
            $limiteSupValor[$i] = isset($limiteSupValor[$i]) ? $limiteSupValor[$i] : 0;
            $limiteInfValor[$i] = isset($limiteInfValor[$i]) ? $limiteInfValor[$i] : 0;
            $limiteValor[$i] = isset($limiteValor[$i]) ? $limiteValor[$i] : 0;
        }

        $mesValor = array(
            $mesValor[1],
            $mesValor[2],
            $mesValor[3],
            $mesValor[4],
            $mesValor[5],
            $mesValor[6],
            $mesValor[7],
            $mesValor[8],
            $mesValor[9],
            $mesValor[10],
            $mesValor[11],
            $mesValor[12]
        );
        
        //Dados linha Limite Superior
        $limiteSuperior = array(
            $limiteSupValor[1],
            $limiteSupValor[2],
            $limiteSupValor[3],
            $limiteSupValor[4],
            $limiteSupValor[5],
            $limiteSupValor[6],
            $limiteSupValor[7],
            $limiteSupValor[8],
            $limiteSupValor[9],
            $limiteSupValor[10],
            $limiteSupValor[11],
            $limiteSupValor[12]
        );

        //Dados linha Limite Inferior
        $limiteInferior = array(
            $limiteInfValor[1],
            $limiteInfValor[2],
            $limiteInfValor[3],
            $limiteInfValor[4],
            $limiteInfValor[5],
            $limiteInfValor[6],
            $limiteInfValor[7],
            $limiteInfValor[8],
            $limiteInfValor[9],
            $limiteInfValor[10],
            $limiteInfValor[11],
            $limiteInfValor[12]
        );

        /*if ($dados[0]->gmedirecao == 'D') {

            //Dados linha Limite Superior
            $limiteSuperior = array(
                $limiteSupValor[1],
                $limiteSupValor[2],
                $limiteSupValor[3],
                $limiteSupValor[4],
                $limiteSupValor[5],
                $limiteSupValor[6],
                $limiteSupValor[7],
                $limiteSupValor[8],
                $limiteSupValor[9],
                $limiteSupValor[10],
                $limiteSupValor[11],
                $limiteSupValor[12]
            );

            //Dados linha Limite Inferior
            $limiteInferior = array(
                $limiteInfValor[1],
                $limiteInfValor[2],
                $limiteInfValor[3],
                $limiteInfValor[4],
                $limiteInfValor[5],
                $limiteInfValor[6],
                $limiteInfValor[7],
                $limiteInfValor[8],
                $limiteInfValor[9],
                $limiteInfValor[10],
                $limiteInfValor[11],
                $limiteInfValor[12]
            );
        } else { // inversamente proporcional
            //Dados linha Limite Superior
            $limiteSuperior = array(
                $limiteInfValor[1],
                $limiteInfValor[2],
                $limiteInfValor[3],
                $limiteInfValor[4],
                $limiteInfValor[5],
                $limiteInfValor[6],
                $limiteInfValor[7],
                $limiteInfValor[8],
                $limiteInfValor[9],
                $limiteInfValor[10],
                $limiteInfValor[11],
                $limiteInfValor[12]
            );

            //Dados linha Limite Inferior
            $limiteInferior = array(
                $limiteSupValor[1],
                $limiteSupValor[2],
                $limiteSupValor[3],
                $limiteSupValor[4],
                $limiteSupValor[5],
                $limiteSupValor[6],
                $limiteSupValor[7],
                $limiteSupValor[8],
                $limiteSupValor[9],
                $limiteSupValor[10],
                $limiteSupValor[11],
                $limiteSupValor[12]
            );
        }*/


        //Dados da linha Limite
        $limite = array(
            $limiteValor[1],
            $limiteValor[2],
            $limiteValor[3],
            $limiteValor[4],
            $limiteValor[5],
            $limiteValor[6],
            $limiteValor[7],
            $limiteValor[8],
            $limiteValor[9],
            $limiteValor[10],
            $limiteValor[11],
            $limiteValor[12]
        );

        $lblMetrica = '';

        switch ($metrica) {
            case 'M':
                $lblMetrica = '$';
                break;
            case 'P':
                $lblMetrica = '%';
                break;
            default:
                $lblMetrica = 'Vlr';
        }

        return json_encode(array(
            'limiteSuperior' => $limiteSuperior,
            'limiteInferior' => $limiteInferior,
            'limite' => $limite,
            'mesValor' => $mesValor,
            'projeto' => $this->consolidado->projeto,
            'resultado' => $this->consolidado->resultado,
            'tipo' => $this->consolidado->tipo,
            'lblMetrica' => $lblMetrica,
            'imgDirecao' => _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/' . (($this->view->direcao == "D") ? 'diretamente' : 'inversamente') . '.png',
            'precisao' => (int) $precisao
        ));
    }

    public function gerarGraficosUsuario($idUsuario) {
        try {
            $graficos = array();

            $dadosFormatados = array();

            $parametros = new stdClass();

            $parametros->idFuncionario = $this->dao->buscarIdFuncionario($idUsuario);

            $parametros->ano = date('Y');

            $dados = $this->dao->buscarDadosGrafico($parametros);

            foreach ($dados as $dadosMeta) {
                $dadosFormatados[$dadosMeta->gmeoid][] = $dadosMeta;
            }


            foreach ($dadosFormatados as $key => $dadosMeta) {
                $this->view->dados = $dadosMeta;
                $parametros->metaid = $key;

                $indicadores = $this->dao->buscarDadosIndicadores($parametros);
                $this->view->indicadores = $indicadores['formatado'];
                $this->view->indicadoresVerificacaoFormula = $indicadores['verificacaoFormula'];

                $this->calcularFormula();

                $graficos[] = $this->gerarGrafico($this->view->dados);

                $this->view->titles[] = array(
                    'title' => $this->view->titulo,
                    'subtitle' => $this->view->subtitulo,
                );
            }

            $this->view->graficos = $graficos;

            require_once _MODULEDIR_ . 'Gestao/View/ges_grafico_meta/graficos_metas_usuario.php';
        } catch (Exception $e) {
            
        }
    }

}
