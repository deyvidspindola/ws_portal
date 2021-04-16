<?php
require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';

/**
 * Camada Business para tratamento de vinculos representante / intalador vs atendente
 *
 * @author 	Andre Luiz Zilz <andre.zilz@sascar.com.br>
 * @version 02/10/2014
 * @package Cron
 */
class CronVinculoPerfilPortal extends CronAction {


	/**
	* Inicia o processo do CRON
	*
	* @param CronVinculoPerfilPortalDAO $dao
	*/
	public function executar($dao) {

		try{

			$dao->transactionBegin();

			//Inativar vinculos vencidos
			$inativados = $dao->inativarVinculos();

			//Gravar LOG
			if(!empty($inativados)) {
				$this->gravarLog($inativados);
			}

			$dao->transactionCommit();

		} catch(Exception $e){

			$dao->transactionRollback();

		}

		return $inativados;

	}

	/**
	 * Grava o arquivo de Log dos vinculos inativados
	 * @param stdClass $idsInativados
	 */
	public function gravarLog($idsInativados){

		$caminho = "/var/www/docs_temporario/";

		$nomeArquivo = "crn_atendimento_vinculo_perfil_portal_" . date("Y_m_d") . ".txt";

		//Grava Arquivo
		try{

			if(is_writable($caminho)){

                $fp = fopen($caminho . $nomeArquivo, "a+");

				if($fp){

					fwrite($fp, "\n\n-- Inicio do processo as: " . date("H:i"). "\n");
                    fwrite($fp, "IDs de vinculos Inativos:" . "\n");

                    $ids = '';

                    foreach ($idsInativados as $value) {
						$ids .= $value->aproid . ";";
					}

                    fwrite($fp, $ids);
                    fclose($fp);

				} else {
                    echo "Falha ao abrir o arquivo: " . $nomeArquivo;
                }

			}else{
				echo "Permisãoo negada para gravar o arquivo de log no diretório: " . $diretorio;
			}

		}catch(Exception $e){
            echo "Erro ao gravar arquivo de Log.";
		}
	}


}