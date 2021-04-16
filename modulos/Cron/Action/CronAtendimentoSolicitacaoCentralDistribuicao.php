<?php
require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';

class CronAtendimentoSolicitacaoCentralDistribuicao extends CronAction {

	public function executar($dao, $inicio) {

		try {
			$msg = '';
			$listaSolicitacoesAtendidas = '';

			// Recupera o representante padrão
			$idRep = $dao->recuperaRepresentantePadrao();


			// Recupera todas as solicitações pendentes sem data de agendamento do representante padrão
			$resSol = $dao->recuperaSolicitacoes($idRep);


			while($solicitacoes = pg_fetch_object($resSol)) {
				// Para cada solicitação, recupera os produtos e a quantidade
				$resItemSol = $dao->recuperaItensSolicitacao($solicitacoes->sagoid);

				$totalItens = pg_num_rows($resItemSol);
				$itensAtendidos = 0;
				$queryItensAtendidos = '(';

				if($totalItens <= 0) {
					continue;
				}

				while($itens = pg_fetch_object($resItemSol)) {
					// Recupera a quantidade em estoque dos produtos por representante
					$qtdeEstoque = $dao->recuperaProdutoEstoque($idRep, $itens->saiprdoid);

					if($qtdeEstoque == -1) {
						continue;
					}

					if($qtdeEstoque >= $itens->saiqtde_solicitacao) {
						$itensAtendidos++;
						$queryItensAtendidos .= $itens->saioid . ',';
					}
				}

				// Se todos os itens da solicitação foram atendidos, dá baixa nos itens e na solicitação
				if($itensAtendidos == $totalItens) {
					$queryItensAtendidos = rtrim($queryItensAtendidos, ',') . ')';

                    $dao->transactionBegin();

					$retornoBaixa = $dao->executaBaixa($queryItensAtendidos, $solicitacoes->sagoid);

                    $ordoid = $dao->recuperarNumeroOrdemServico($solicitacoes->sagoid);

                    if(!empty($ordoid)) {

                        $listaProdutos = $dao->getListaProdutosSolicitados($solicitacoes->sagoid);

                        $historico = "Produto solicitado para a distribuição ". $listaProdutos ." : Estoque CD reabastecido.";

                        $dao->gravarHistoricoOrdemServico($ordoid, $historico);
                    }

                    $dao->transactionCommit();


					if($retornoBaixa == 0) {
                        $dao->transactionRollback();
						continue;
					}

					$msg .= $solicitacoes->sagoid . ',';
				}
			}

			if($msg == '') {
				$msg = 'Nenhuma solicitacao atendida.';
			}

		} catch(Exception $e) {
			$msg = $e->getMessage();
		}

		$msg = rtrim($msg, ',');

		$this->gravarLog($msg, $inicio);

		return $msg;
	}

	function gravarLog($resultado, $inicio){

		$caminho = "/var/www/docs_temporario/";

		$nomeArquivo = "crn_atendimento_solicitacao_central_distribuicao_" . date("Y_m_d") . ".txt";

		//Grava Arquivo
		try{

			if(is_writable($caminho)){

                $fp = fopen($caminho . $nomeArquivo, "a+");

				if($fp){

					fwrite($fp, "\n\n-- Inicio do processo as: " . date("H:i"). "\n");
                    fwrite($fp, "Solicitacoes atendidas:" . "\n");
                    fwrite($fp, $resultado);
                    fclose($fp);

				} else {
                    echo "Falha ao abrir o arquivo: " . $nomeArquivo;
                }

			}else{
				echo "Permissao negada para gravar o arquivo de log no diretorio: " . $caminho;
			}

		}catch(Exception $e){
            echo "Erro ao gravar arquivo de Log.";
		}
	}

}

?>