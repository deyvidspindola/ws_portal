<?php
require_once _MODULEDIR_ . 'Cron/Action/AtendimentoAutomatico.php';
require_once _MODULEDIR_ . 'Atendimento/Action/UraAtivaPanico.php';


/**
 * Serviço automático de tratamento de panicos pendentes.
 *
 * @package Modulos\Cron\Action\AtendimentoAutomatico
 * @author 	Alex S. Médice <alex.medice@meta.com.br>
 * @since   18/03/2013
 */
class AtendimentoAutomaticoPanico extends AtendimentoAutomatico {

	const MOTIVO_CONTATO_SEM_SUCESSO = 'Contato sem sucesso';
	const MOTIVO_GRUPO_DISCADOR      = 'Atendimento%P_nico%Alerta%Cerca';

	/**
	 * Verifica insucessos do discador
	 * @param UraAtivaPanicoDAO $dao
	 * @see AtendimentoAutomatico::insucessos()
	 * @return void
	 */
	public function insucessos(UraAtivaPanicoDAO $dao) {

		$this->dao       = $dao;
		$arrLogInsucesso1 = array();
        $arrLogInsucesso2 = array();
        $arrLogInsucesso3 = array();
		$arrLog = array();

		$this->dao->transactionBegin();

		try {

			$arrContatos = $this->dao->buscarContatosDiscadorPanico();

			foreach ($arrContatos as $contato) {

				$veioid 	= (int)$contato['veioid'];
				$id_contato	= (int)$contato['id_contato'];
				$insucessos = array();
				$clinome	= '';
				$contrato	= 0;
				$codcliente	= 0;

				$idsTelefoneExterno = $this->dao->buscarInsucessosContato($veioid);

				if (!empty($idsTelefoneExterno)) {
					$listaTelefones  = implode(',', $idsTelefoneExterno);
					$listaInsucessos = $this->dao->buscarInsucessoEspecifico($contato['veioid'], $listaTelefones);

					if ($listaInsucessos) {
						$this->dao->atualizarTentativaInsucessoContato($veioid, '', $listaInsucessos);
					}
				}

				$insucessos = $this->dao->buscarTentativasInsucessoContato($veioid);


				$UraAtivaPanico = new UraAtivaPanico($this->conn);
				$UraAtivaPanico->tratarInsucessos($insucessos);

				if(!empty($insucessos)){

                    if(($this->dao->isGravaLogAtendimento) && (!empty($UraAtivaPanico->logAtendimento))) {

                        $linhaLog = $UraAtivaPanico->logAtendimento['connumero'] . " | ";
                        $linhaLog .= $UraAtivaPanico->logAtendimento['conclioid'] . " | ";
                        $linhaLog .= $UraAtivaPanico->logAtendimento['conveioid'] . " | ";
                        $linhaLog .= $UraAtivaPanico->logAtendimento['panico'] . " | ";
                        $linhaLog .= $UraAtivaPanico->logAtendimento['motivo'];
                        $arrLogInsucesso1[]	= $linhaLog;
                        echo "INSUCESSOS:<hr>" . $linhaLog . "<br>";
                    }
				}


				unset($UraAtivaPanico);
				unset($insucessos);
				unset($clinome);
				unset($contrato);
				unset($codcliente);

			}

			/*
             *  Panicos pendentes a amis de uma hora: precisa verificar e retorna
             */
            $UraAtivaPanico = new UraAtivaPanico($this->conn);
			$UraAtivaPanico->tratarAtendimentosPendentes();

             if(($this->dao->isGravaLogAtendimento) && (!empty($UraAtivaPanico->logAtendimento))) {

                      $linhaLog = $UraAtivaPanico->logAtendimento['connumero'] . " | ";
                      $linhaLog .= $UraAtivaPanico->logAtendimento['conclioid'] . " | ";
                      $linhaLog .= $UraAtivaPanico->logAtendimento['conveioid'] . " | ";
                      $linhaLog .= $UraAtivaPanico->logAtendimento['panico'] . " | ";
                      $linhaLog .= $UraAtivaPanico->logAtendimento['motivo'];
                      $arrLogInsucesso2[]	= $linhaLog;
                      echo "PENDETES 1 HORA:<hr>" . $linhaLog . "<br>";
              }


			/*
			 * Processo que ira tratar os atendimentos do tipo PANICO_FALAR_ATENDENTE
			 */
			$atmoid	= $this->dao->buscarMotivoPorGrupo('P_nico Ura ativa', 'Atendimento%P_nico%Alerta%Cerca');
			$contatos = $this->dao->buscarContatosFalarAtendente($atmoid);

			if(!empty($contatos)){
                $this->dao->setFalarAtendente(true);
                $this->dao->enviarDiscador($contatos);
				$this->notificarDiscador();

                if($this->dao->isGravaLogAtendimento){

                    for($i=0; $i < count($this->dao->logEnviados); $i++){
                        $this->dao->logEnviados[$i] .= " | Panico Falar Atendente 20 minutos";
                        echo "PENDETES 20 MINUTOS:<hr>" . $this->dao->logEnviados[$i] . "<br>";
                    }
                    $this->gravarLogAtendimento($this->dao->nomeCampanha, "_reenvio_", $this->dao->logEnviados);
                }

			}

			if($this->dao->isGravaLogAtendimento){
				$arrLogInsucesso3 = array_merge($arrLogInsucesso1, $arrLogInsucesso2);
                if(!empty($arrLogInsucesso3)) {
                    $this->gravarLogAtendimento("panico", "_insucesso_", $arrLogInsucesso3);
                }
			}

			$this->dao->transactionCommit();

		} catch (Exception $e) {
			$this->view->msg = $e->getMessage();

			$this->dao->transactionRollback();
		}

		return $this->view;
	}


}
