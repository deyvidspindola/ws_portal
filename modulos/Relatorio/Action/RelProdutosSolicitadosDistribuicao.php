<?php

include "lib/Components/PHPExcel/PHPExcel.php";
include "lib/phpMailer/class.phpmailer.php";

require_once _MODULEDIR_ . 'SmartAgenda/Action/ReservaProduto.php';

/**
 * Produtos Solicitados.
 *
 * @package Relatório
 * @author  João Paulo Tavares da Silva <joao.silva@meta.com.br>
 */

class RelProdutosSolicitadosDistribuicao{

    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_INFO_CAMPO_OBRIGATORIO = "Campos com * são obrigatorios.";
    const MENSAGEM_ALERTA_SEM_REGISTRO = "Nenhum registro encontrado.";
    const DIRETORIO_PRODUTOS_SOLICITADOS = '/var/www/docs_temporario/';


	public function __construct(RelProdutosSolicitadosDAO $dao){

		$this->dao = $dao;
        $this->view = new stdClass();
        $this->view->dados    = new stdClass();
        $this->view->mensagem = new stdClass();
		$this->param = new stdClass();
		$this->tratarParametros();
    	$this->view->status = true;
    	$this->view->campos = array();
		$this->view->mensagem->info    = self::MENSAGEM_INFO_CAMPO_OBRIGATORIO;
 		$this->view->caminho = _MODULEDIR_ . 'Relatorio/View/rel_produtos_solicitados_distribuicao/';

	}


	public function index(){
		$this->view->dados->status 			= $this->dao->buscarStatusSolicitacao();
		$this->view->dados->tipo 			= $this->dao->buscarOsTipo();
		$this->view->dados->estados    			= $this->dao->buscarEstados();
		$this->view->dados->representantes 	= $this->dao->buscarRepresentantes();
		$this->view->dados->classe = $this->dao->buscarEquipamentoClasse();
		$this->view->dados->cidades        = array();

        if (isset($this->param->ufuf)) {
                $this->view->dados->cidades = $this->dao->buscarCidade($this->param);
         }

        if (isset($this->param->acao) && $this->param->acao != 'index') {
            $this->validarParametros();

            if ($this->view->status) {
                switch ($this->param->acao) {
                    case 'exportar' :
                        $this->view->dados->arquivo = $this->pesquisar(true);

                        if (!$this->view->dados->arquivo) {
                            $this->view->status           = false;
                            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
                        }
                        break;
                    case 'pesquisar' :
                        $this->view->dados->pesquisa = $this->pesquisar(false);

                        if (!$this->view->dados->pesquisa) {
                            $this->view->status           = false;
                            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
                        }
                        break;
                }
            }
        }

		include $this->view->caminho . 'index.php';
	}

	private function pesquisar($exportar = false){

		if($exportar){
            $retorno = $this->dao->buscarSolicitacoes($this->param, false);
			$arquivo = 'rel_produtos_solicitados_'.date('Y-m-d').'_'.date('H-i-s').'.xlsx';

            $phpExcelReader = PHPExcel_IOFactory::createReader("Excel2007");
            $phpExcel       = $phpExcelReader->load($this->view->caminho.'modelo.xlsx');

            foreach ($retorno as $indice => $registro) {
                $linha = $indice + 8;

                $phpExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($registro->dt_solicitacao ? date('d/m/Y H:i', strtotime($registro->dt_solicitacao)) : ''));
                $phpExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($registro->status_solicitacao));
                $phpExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($registro->status_item));
                $phpExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($registro->recusa));
                $phpExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode($registro->num_os));

                if($registro->repoid == 1624){
                    $phpExcel->getActiveSheet()->setCellValue('F'.$linha, '');
                } else {
                    $phpExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($registro->dt_agendamento));
                }

                $phpExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($registro->tipo_os));
                $phpExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($registro->estado));
                $phpExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($registro->cidade));
                $phpExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($registro->representante));
                $phpExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($registro->produto));
                $phpExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($registro->permite_similar));
                $phpExcel->getActiveSheet()->setCellValue('M'.$linha, utf8_encode($registro->classe_cliente));
                $phpExcel->getActiveSheet()->setCellValue('N'.$linha, utf8_encode($registro->usuario));
                $phpExcel->getActiveSheet()->setCellValue('O'.$linha, utf8_encode($registro->nr_remessa));
            }

            $phpExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

            $phpExcelWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
            $phpExcelWriter->setPreCalculateFormulas(false);
            $phpExcelWriter->save(self::DIRETORIO_PRODUTOS_SOLICITADOS . $arquivo);

            return $arquivo;
		}elseif($this->param->tprel == 'A'){
            $retorno = $this->dao->buscarSolicitacoes($this->param, true);
			return $retorno;
		}else{
            $retorno = $this->dao->buscarSolicitacoesSintetico($this->param, true);
            return $retorno;
        }
	}

	private function validarParametros(){

        $camposDestacados = array();


        if (empty($this->param->tprel)) {
            $camposDestacados[] = array(
                'campo'    => 'tprel',
                'mensagem' => ''
            );
            $this->view->status   = false;
        } elseif ($this->param->tprel == 'S' and empty($this->param->saisoid)) {
            $camposDestacados[] = array(
                'campo'    => 'saisoid',
                'mensagem' => ''
            );
            $this->view->status   = false;
        }

        if (empty($this->param->sagdt_cadastro_inicial)) {
            $camposDestacados[] = array(
                'campo'    => 'sagdt_cadastro_inicial',
                'mensagem' => ''
            );
            $this->view->status   = false;
        }

        if (empty($this->param->sagdt_cadastro_final)) {
            $camposDestacados[] = array(
                'campo'    => 'sagdt_cadastro_final',
                'mensagem' => ''
            );
            $this->view->status   = false;
        }

        //Se todos os cmapos obrigatorios foram preenchidos, valida o periodo
        if($this->view->status) {

             if (!empty($this->param->sagdt_cadastro_inicial) and !empty($this->param->sagdt_cadastro_final)) {
                $dt_inicial = strtotime(str_replace('/', '-', $this->param->sagdt_cadastro_inicial));
                $dt_final   = strtotime(str_replace('/', '-', $this->param->sagdt_cadastro_final));

                if ($dt_inicial > $dt_final) {
                    $camposDestacados[] = array(
                        'campo'    => 'sagdt_cadastro_inicial',
                        'mensagem' => 'Período inválido'
                    );
                    $camposDestacados[] = array(
                        'campo'    => 'sagdt_cadastro_final',
                        'mensagem' => 'Período inválido'
                    );
                    $this->view->status   = false;
                } elseif (strtotime('+3 months', $dt_inicial) < $dt_final) {
                    $camposDestacados[] = array(
                        'campo'    => 'sagdt_cadastro_inicial',
                        'mensagem' => 'O período deve ser de no máximo 3(três) meses'
                    );
                    $camposDestacados[] = array(
                        'campo'    => 'sagdt_cadastro_final',
                        'mensagem' => 'O período deve ser de no máximo 3(três) meses'
                    );
                    $this->view->status   = false;
                }
            }
        }

        $this->view->destaque = $camposDestacados;

        if (!$this->view->status) {

            if($camposDestacados[0]['mensagem'] == '') {
                $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
            } else {
                $this->view->mensagem->alerta = $camposDestacados[0]['mensagem'];
            }
        }
	}

	 /**
     * Método que instância os dados do $_POST e $_GET.
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
     * Método que é chamado por AJAX para montar o modal.
     */
    public function modalGerenciarSolicitacao(){
        $dados = new stdClass();
        $dados->html = null;

        try{
            $representante = $this->dao->buscarRepresentante($this->param->repoid);
            $itens         = $this->dao->buscarItemAgendamento($this->param->sagoid);
            $solicitacao   = $this->dao->getSolicitacaoInfo($this->param->sagoid);

            require_once $this->view->caminho.'modal_gerenciar_solicitacao.php';

            exit;

        }catch (ErrorException $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'erro';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        }

        echo json_encode($dados);
    }

    /**
     * Método Chamado por ajax quando clicado no botão ATENDER no modal.
     */
    public function atender(){

        $dados = new stdClass();
        try{
            $this->dao->begin();
            $this->dao->atualizarStatusItem($this->param->itens, 7);

            $itensSolicitacao      = $this->dao->buscarItemAgendamento($this->param->sagoid);
            $num_itens             = count($itensSolicitacao);
            $num_itens_atendidos   = $this->dao->qtdItensAtendidos($this->param->sagoid);
            $solicitacaoInfo = $this->dao->getSolicitacaoInfo($this->param->sagoid);
            $listaProdutos = $this->dao->getListaProdutosSolicitados($this->param->sagoid);


            if($num_itens_atendidos == $num_itens){
                $this->dao->atualizarStatusSolicitacao($this->param->sagoid, 7);

                 $descricaoStatus = $this->dao->buscarStatusSolicitacao(7);

                 $dados->statusSolicitacao = $descricaoStatus[0]->descricao;


                if(!empty($solicitacaoInfo->sagordoid)) {

                    $NovoStatus = ($solicitacaoInfo->sagfalta_critica == 'f') ?  $dados->statusSolicitacao : 'Estoque CD reabastecido';

                    $msg = "Produto solicitado para a distribuição " . $listaProdutos . ": ". $NovoStatus . ".";

                    $dados->statusSolicitacao = $NovoStatus;
                }

            }else{
                $this->dao->atualizarStatusSolicitacao($this->param->sagoid, 6);

                 $descricaoStatus = $this->dao->buscarStatusSolicitacao(6);

                 $dados->statusSolicitacao = $descricaoStatus[0]->descricao;

                $msg = "Status da Solicitação de Produtos para Distribuição alterado para " .  $dados->statusSolicitacao . ".";
            }

            // Registra histórico da OS
            $this->dao->registraHistoricoOS($solicitacaoInfo->sagordoid, $msg);

            // Envia email para o Solicitante
            $msgEmail = "<p>Prezado " . $solicitacaoInfo->nm_usuario . ",</p>";
            $msgEmail .= "<p>O status de sua Solicitação de Produtos para a O.S " . $solicitacaoInfo->sagordoid . " foi Alterado para " .  $dados->statusSolicitacao . ".</p>";
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->From = "sascar@sascar.com.br";
            $mail->FromName = "Sascar";
            $mail->ClearAllRecipients();
            $mail->AddAddress($solicitacaoInfo->usuemail);
            $mail->Subject = "Solicitação para Distribuição (O.S Nº " . $solicitacaoInfo->sagordoid . ")";
            $mail->MsgHTML($msgEmail);
            $mail->Send();

            $dados->status = true;

        } catch (ErrorException $e) {
            $this->dao->rollback();
            $dados->status          = false;
            $dados->mensagem->tipo  = 'erro';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $this->dao->rollback();
            $dados->status          = false;
            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        }
        $this->dao->commit();
        echo json_encode($dados);
    }

    /**
     * Método Chamado por ajax quando clicado no botão RECUSAR no modal.
     */
    public function recusar(){

        $dados = new stdClass();
        try{
            $this->dao->begin();
            $this->dao->atualizarStatusItem($this->param->itens, 9, utf8_decode($this->param->justificativa));

            $itensSolicitacao      = $this->dao->buscarItemAgendamento($this->param->sagoid);
            $num_itens             = count($itensSolicitacao);
            $num_itens_recusados   = $this->dao->qtdItensRecusados($this->param->sagoid);
            $solicitacaoInfo = $this->dao->getSolicitacaoInfo($this->param->sagoid);
            $listaProdutos = $this->dao->getListaProdutosSolicitados($this->param->sagoid);

            if($num_itens_recusados == $num_itens){
                $this->dao->atualizarStatusSolicitacao($this->param->sagoid, 9);

                $descricaoStatus = $this->dao->buscarStatusSolicitacao(9);

                $dados->statusSolicitacao = $descricaoStatus[0]->descricao;

                if(!empty($solicitacaoInfo->sagordoid)) {

                    $msg = "Produto solicitado para a distribuição " . $listaProdutos . ": ". $dados->statusSolicitacao . ".";
                }

            }else{
                $this->dao->atualizarStatusSolicitacao($this->param->sagoid, 6);

                $descricaoStatus = $this->dao->buscarStatusSolicitacao(6);

                $dados->statusSolicitacao = $descricaoStatus[0]->descricao;

                $msg = "Status da Solicitação de Produtos para Distribuição alterado para " . $dados->statusSolicitacao . ".";
            }

            $msgEmail2 = "";

            /**
             * STI-86783
             * No caso de recusa da solicitação o sistema verifica se a solicitação que está sendo recusada 
             * é de origem de um agendamento, Cancela as Reservas e adiciona a justificativa da Recusa no Histórico e no E-mail.
             */
            if ($solicitacaoInfo->flag_agendamento == "t"){

                $reservaProduto = new ReservaProduto();
                $reservaProduto->setNumeroOrdemServico($solicitacaoInfo->sagordoid);
                $reservaProduto->setCodigoAgendamento($solicitacaoInfo->sagosaoid);
                $reservaProduto->setCodigoPrestador(1624); //Cod do CD-campinas
                $reservaProduto->setCancelarReserva("Solicitação Recusada pelo CD pelo seguinte motivo: " . utf8_decode($this->param->justificativa));
                                $msg .= " Pelo seguinte motivo: " . utf8_decode($this->param->justificativa);

                $msgEmail2 = "<p>Pelo seguinte motivo: " . utf8_decode($this->param->justificativa) . "</p>";
            }

            // Registra histórico da OS
            $this->dao->registraHistoricoOS($solicitacaoInfo->sagordoid, $msg);

            // Envia email para o Solicitante
            $msgEmail = "<p>Prezado " . $solicitacaoInfo->nm_usuario . ",</p>";
            $msgEmail .= "<p>O status de sua Solicitação de Produtos para a O.S " . $solicitacaoInfo->sagordoid . " foi Alterado para " . $dados->statusSolicitacao . ".</p>";
            $msgEmail .= $msgEmail2;
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->From = "sascar@sascar.com.br";
            $mail->FromName = "Sascar";
            $mail->ClearAllRecipients();
            $mail->AddAddress($solicitacaoInfo->usuemail);
            $mail->Subject = "Solicitação para Distribuição (O.S Nº " . $solicitacaoInfo->sagordoid . ")";
            $mail->MsgHTML($msgEmail);
            $mail->Send();

            $dados->status = true;

        } catch (ErrorException $e) {
            $this->dao->rollback();
            $dados->status          = false;
            $dados->mensagem->tipo  = 'erro';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $this->dao->rollback();
            $dados->status          = false;
            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        }
        $this->dao->commit();
        echo json_encode($dados);
    }


	  /**
     * Método que retorna as Cidades por Ajax.
     *
     * @return Void
     */
    public function buscarCidade() {
        $dados = new stdClass();
        $dados->status          = true;
        $dados->html            = null;
        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;

        try {
            $this->view->dados->cidades = $this->dao->buscarCidade($this->param);

            ob_start();

            require_once $this->view->caminho.'ajax_cidades.php';

            $dados->html = utf8_encode(ob_get_clean());
        } catch (ErrorException $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'erro';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        }

        echo json_encode($dados);
    }

    public function validarPesquisa() {

        $dados = new stdClass();

        $dados->status   = true;
        $dados->campos   = array();
        $dados->mensagem = new stdClass();

        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;

        $this->validarParametros();

        if (!$this->view->status) {
            $dados->status = false;

            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($this->view->mensagem->alerta);

            foreach ($this->view->destaque as $chave => $valor) {
                $dados->campos[] = array(
                    'campo'    => $valor['campo'],
                    'mensagem' => utf8_encode($valor['mensagem'])
                );
            }
        }

        echo json_encode($dados);
    }

}