<?php
/**
 * Esta rotina é responsável por:
 * Verificar se o veículo que está agendado para voltar a aparecer no relatório de Estatística GSM 
 * voltou a posicionar antes da data agendada.
 * 
 * @file VerificaPosicionamentoVeiculoAgendado.php
 * @author Paulo Henrique da Silva Junior
 * @version 22/08/2013
 * @since 22/08/2013
 * @package SASCAR VerificaPosicionamentoVeiculoAgendado.php
*/

// INCLUDES
require_once 'lib/config.php';

require_once _CRONDIR_ .'lib/validaCronProcess.php';

//classe responsável em processar dados das pesquisas no bd
require_once _MODULEDIR_ . 'Cron/DAO/VerificaPosicionamentoVeiculoAgendadoDAO.php';


class VerificaPosicionamentoVeiculoAgendado{
	
	//atributos
	private $conn;
	
	// Construtor
	public function __construct() {
	
		global $conn;
	
		//seta variável de conexão
		$this->conn = $conn;
	
		// Objeto  - DAO
		$this->dao = new VerificaPosicionamentoVeiculoAgendadoDAO($conn);
	}
	
	
	public function verificarVeiculosAgendados(){
		$dia = date('d');
		$mes = date('m');
		$ano = date('Y');
		$hora = date('H:i');
		$diaSemana = date('w',mktime(0,0,0,$mes,$dia,$ano));

		echo  '[Dia->' . $dia . '/' . $mes . '/' . $ano . ' ' . $hora . '] [Weekday->' . $diaSemana . '] LOG { ';

		try{

			$nomeProcesso = 'verifica_posicionamento_veiculo_agendado.php';

			if(burnCronProcess($nomeProcesso) === true){
				throw new Exception (" O processo [$nomeProcesso] ainda está em processamento.");
			}

			if(!$this->conn){
				throw new Exception (" Erro ao conectar-se no banco de dados.");
			}

			//inicia transação no bd
			if (!$this->dao->verificarVeiculosAgendados())
			{
				throw new Exception (" Erro ao consultar veiculos agendados.");
			}
			echo " Verificação de Posicionamento Realizada com sucesso ";
		}catch (Exception $e){			
			echo "<font color='red'>".$e->getMessage()." </font>";
		}
		echo "}\n\n";
		exit;
	}
	
	
}


?>