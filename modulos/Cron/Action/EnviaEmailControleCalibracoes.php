<?php

/**
 * Classe para persistência de dados deste modulo
 */
require _MODULEDIR_ . 'Cron/DAO/EnviaEmailControleCalibracoesDAO.php';

/**
 * Classe padrão para envio de emails
 */
require _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * @class EnviaEmailControleCalibracoes
 * @author Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 * @since 02/05/2013
 * Camada de regras de negócio.
 */
class EnviaEmailControleCalibracoes {

    private $dao;
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * Lista os emails dos destinatarios constantes na base sascar
     */
    public function verificaListaEmail(){
    	return $this->dao->getDestinatariosEmail('LAB');
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * Lista equipamentos vencidos e a vencer constante do cadastro de controle de calibração
     * - Equipamentos a vencer em até 7 dias com periodidicade Mensal ou Semanal
     * - Equipamentos a vencer em até 30 dias com periodidicade Anual
     * - Todos os equipamentos vencidos 
     */
    public function listaEquipamentosPorVencto($tipo_identificacao) {
        
		try{
		
			if ($tipo_identificacao == 'NOT_ENG') {
				$total_equipamentos = $this->dao->getEquipamentosCalibracaoNotEng();
			} else {  //$tipo_identificacao == 'ENG'
				$total_equipamentos = $this->dao->getEquipamentosCalibracaoEng();
			}
	    	
	    	$equipamentos_vencidos = array();

	    	$equipamentos_avencer = array();
	    	
	    	foreach ($total_equipamentos as $equipamentos) {
	    		
	    		if (($equipamentos['mqhproxima']) && ($equipamentos['maqperiodicidade'])) {
	    			
	    			if ( $this->date_diff_($equipamentos['mqhproxima'],date("d/m/y"),false) < 0 ){
			   				$equipamentos_vencidos[] = $equipamentos;
		    		} else {
	    			
		    			if ((($equipamentos['maqperiodicidade'] == 'M' )||($equipamentos['maqperiodicidade'] == 'S' )) 
			    			&& ( $this->date_diff_($equipamentos['mqhproxima'],date("d/m/y"),false) <= 7 )){
				   				$equipamentos_avencer[] = $equipamentos;
			    		}
			    		if (($equipamentos['maqperiodicidade'] == 'A' ) 
			    			&& ( $this->date_diff_($equipamentos['mqhproxima'],date("d/m/y"),false) <= 30 )){
				   				$equipamentos_avencer[] = $equipamentos;
			    		}
		    		}
	    		}
	    	}
	    	
	        return array('vencidos' => $equipamentos_vencidos, 'vencer' => $equipamentos_avencer);
	        
		} catch (Exception $e) {
        	
			return array('message' => $e);
        }
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param   $lista_equipamentos => Array contendo lista de equipamentos para envio email
     * @param   $status_calib => String contendo status da calibração do email (VC-vencido ou AV-a vencer) 
     * @param 	$tipo_lista_email => String contendo tipo lista de email (grupo ENG ou NOT_ENG)
     * Função que envia email para os destinatarios selecionados na base da Sascar    
     */
    public function sendEmailCalibracao($lista_equipamentos, $status_calib, $tipo_lista_email) {

    	/*
    	 * Prepara conteúdo para o email e coloca em $mensagem
    	 */
        
    	ob_start();
    	
        include _MODULEDIR_ . 'Cron/View/envia_email_controle_calibracao/layout_email_controle_calibracao.php';
        
        $mensagem = ob_get_contents();
        
        ob_end_clean();
        
        /*
         * Define cabeçalhos do email
         */ 
        if ($status_calib == 'VC') {
	        $assunto="Calibração Vencida";
	        $titulo_mensagem="Atenção para os equipamentos com calibração VENCIDA.";
	        
        } else {  // $status_calib == 'AV'         
        	$assunto="Vencimento de Calibração";
        	$titulo_mensagem="Atenção para os equipamentos com calibração A VENCER nos próximos dias.";
        	
        }
        
        /*
         * Monta email
         */
        $mail = new PHPMailer();
        $mail->ClearAllRecipients();
        $mail->IsSMTP();
        $mail->From = "sistema@sascar.com.br";
        $mail->FromName = "Intranet SASCAR - E-mail automático";
        $mail->Subject = "$assunto";

        $mail->MsgHTML("
                <b>$titulo_mensagem</b><br /><br />
                $mensagem
                ");

        /*
         * Adiciona Destinatarios ao email de acordo com tipo da lista de email
         */
        
    	if ($_SESSION['servidor_teste'] == 1) {
            $lista_email = array("angelo.frizzo@meta.com.br");
            
        } else {
        	if ($tipo_lista_email == 'NOT_ENG') {
        		$lista_email = $this->dao->getDestinatariosEmail('NOT_ENG');
            	 
           	} else { // $tipo_lista_email == 'ENG'
           		$lista_email = $this->dao->getDestinatariosEmail('ENG');
           	}
        }
        
        if ($lista_email) {
        	if (is_array($lista_email)){
		        foreach ($lista_email as $destinatarios) {
		        	//echo $destinatarios;
		        	$mail->AddAddress($destinatarios);
		        }
        	} else {
        		$mail->AddAddress($lista_email);
        	}				     
	        
	        /*
	         * Envia Email
	        */
        	return $mail->Send();
	        
        } else {
        	return 1;
        }
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param   $data => Data 
     * @param   $to_american => seta se formato é americano ou não
     * Função que converte Data para uso na função date_diff_     
     */
    public function date_to($data,$to_american = false)
    {
    	if ($to_american){
    		$data = str_replace("/","-",$data);
    		$dia = substr($data,0,strpos($data,"-"));
    		$data = substr($data,strpos($data,"-")+1);
    		$mes = substr($data,0,strpos($data,"-"));
    		$data = substr($data,strpos($data,"-")+1);
    		if (strpos($data," ")) $ano = substr($data,0,strpos($data," "));else $ano = $data;
    		if (!(@checkdate($mes,$dia,$ano))) $data = false; else $data = $ano."-".$mes."-".$dia;
    	}
    	else
    	{
    		$data = str_replace("/","-",$data);
    		$ano = substr($data,0,strpos($data,"-"));
    		$data = substr($data,strpos($data,"-")+1);
    		$mes = substr($data,0,strpos($data,"-"));
    		$data = substr($data,strpos($data,"-")+1);
    		if (strpos($data," ")) $dia = substr($data,0,strpos($data," "));else $dia = $data;
    		if (!(@checkdate($mes,$dia,$ano))) $data = false; else $data = $dia."/".$mes."/".$ano;
    	}
    	return $data;
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param   $data1 => Data Inicial
     * @param   $data2 => Data Final
     * @param   $to_american => seta se formato é americano ou não
     * Função que calcula a diferença de dias entre duas datas     
     */
    public function date_diff_($data1,$data2,$americano=false)
    {
    	if(!$americano)
    	{
    		$data1=$this->date_to($data1,true);
    		$data2=$this->date_to($data2,true);
    	}
    	if($data1 && $data2)
    	{
    		$data1 = str_replace("/","-",$data1);
    		$ano1 = substr($data1,0,strpos($data1,"-"));
    		$data1 = substr($data1,strpos($data1,"-")+1);
    		$mes1 = substr($data1,0,strpos($data1,"-"));
    		$data1 = substr($data1,strpos($data1,"-")+1);
    		if (strpos($data1," ")) $dia1 = substr($data1,0,strpos($data," ")); else $dia1 = $data1;
    
    		$data2 = str_replace("/","-",$data2);
    		$ano2 = substr($data2,0,strpos($data2,"-"));
    		$data2 = substr($data2,strpos($data2,"-")+1);
    		$mes2 = substr($data2,0,strpos($data2,"-"));
    		$data2 = substr($data2,strpos($data2,"-")+1);
    		if (strpos($data2," ")) $dia2 = substr($data2,0,strpos($data," ")); else $dia2 = $data2;
    		if(@checkdate($mes1,$dia1,$ano1) && @checkdate($mes2,$dia2,$ano2))
    		{
    			$data1 = mktime(0,0,0,$mes1,$dia1,$ano1);
    			$data2 = mktime(0,0,0,$mes2,$dia2,$ano2);
    			return ($data1-$data2)/86400;
    		}
    		else return false;
    	}
    	else return false;
    }
    
    public function __construct() {
        $this->dao = new EnviaEmailControleCalibracoesDAO();
    }

}