<?php

set_time_limit(0);

/**
 * Classe para persistência de dados deste modulo
 */
require _MODULEDIR_ . 'Cron/DAO/AtualizacaoBaseCorreiosDAO.php';

/**
 * Classe padrão para envio de emails
 */
require _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * @class AtualizacaoBaseCorreios
 * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
 * @since 08/05/2013
 * Camada de regras de negócio.
 */
class AtualizacaoBaseCorreios {
    
    private $dao;    
    
    /**
     * Busca os arquivos abaixo no diretório "\var\www\arquivos_correios\", 
     * caso existam todos os arquivos nesse diretório deve iniciar a atualização.
     * DNE_GU_BAIRROS, DNE_GU_LOCALIDADES, DNE_GU_AC_LOGRADOUROS, DNE_GU_AL_LOGRADOUROS
     * DNE_GU_AM_LOGRADOUROS, DNE_GU_AP_LOGRADOUROS, DNE_GU_BA_LOGRADOUROS
     * DNE_GU_CE_LOGRADOUROS, DNE_GU_DF_LOGRADOUROS, DNE_GU_ES_LOGRADOUROS
     * DNE_GU_GO_LOGRADOUROS, DNE_GU_MA_LOGRADOUROS, DNE_GU_MG_LOGRADOUROS
     * DNE_GU_MS_LOGRADOUROS, DNE_GU_MT_LOGRADOUROS, DNE_GU_PA_LOGRADOUROS
     * DNE_GU_PB_LOGRADOUROS, DNE_GU_PE_LOGRADOUROS, DNE_GU_PI_LOGRADOUROS
     * DNE_GU_PR_LOGRADOUROS, DNE_GU_RJ_LOGRADOUROS, DNE_GU_RN_LOGRADOUROS
     * DNE_GU_RO_LOGRADOUROS, DNE_GU_RR_LOGRADOUROS, DNE_GU_RS_LOGRADOUROS
     * DNE_GU_SC_LOGRADOUROS, DNE_GU_SE_LOGRADOUROS, DNE_GU_SP_LOGRADOUROS
     * DNE_GU_TO_LOGRADOUROS.
     * 
     * Todos os arquivos devem ser do tipo TXT.
     */    
    public function verificaArquivosServidor() {
        
        $caminho = "/var/www/arquivos_correios/";
        $diretorio = dir($caminho);
        
        $arquivos_a_validar = array(
	    'DNE_GU_GRANDES_USUARIOS.TXT', 'DNE_GU_UNIDADES_OPERACIONAIS.TXT',
            'DNE_GU_BAIRROS.TXT', 'DNE_GU_LOCALIDADES.TXT', 'DNE_GU_AC_LOGRADOUROS.TXT', 
            'DNE_GU_AL_LOGRADOUROS.TXT', 'DNE_GU_AM_LOGRADOUROS.TXT', 'DNE_GU_AP_LOGRADOUROS.TXT', 
            'DNE_GU_BA_LOGRADOUROS.TXT', 'DNE_GU_CE_LOGRADOUROS.TXT', 'DNE_GU_DF_LOGRADOUROS.TXT', 
            'DNE_GU_ES_LOGRADOUROS.TXT', 'DNE_GU_GO_LOGRADOUROS.TXT', 'DNE_GU_MA_LOGRADOUROS.TXT', 
            'DNE_GU_MG_LOGRADOUROS.TXT', 'DNE_GU_MS_LOGRADOUROS.TXT', 'DNE_GU_MT_LOGRADOUROS.TXT', 
            'DNE_GU_PA_LOGRADOUROS.TXT', 'DNE_GU_PB_LOGRADOUROS.TXT', 'DNE_GU_PE_LOGRADOUROS.TXT', 
            'DNE_GU_PI_LOGRADOUROS.TXT', 'DNE_GU_PR_LOGRADOUROS.TXT', 'DNE_GU_RJ_LOGRADOUROS.TXT', 
            'DNE_GU_RN_LOGRADOUROS.TXT', 'DNE_GU_RO_LOGRADOUROS.TXT', 'DNE_GU_RR_LOGRADOUROS.TXT', 
            'DNE_GU_RS_LOGRADOUROS.TXT', 'DNE_GU_SC_LOGRADOUROS.TXT', 'DNE_GU_SE_LOGRADOUROS.TXT', 
            'DNE_GU_SP_LOGRADOUROS.TXT', 'DNE_GU_TO_LOGRADOUROS.TXT'
        );
        
        //lendo os arquivos do diretório
        while($arquivo = $diretorio->read()) {
            
            //recupera a extensão do arquivo em letras maiusculas            
            $extensao = strtoupper(end(explode(".", $arquivo)));
            
            /**
             * Se o arquivo existir o removemos do array
             */
            if($extensao == 'TXT' && in_array($arquivo, $arquivos_a_validar)) {
                $posicao = array_search($arquivo, $arquivos_a_validar);
                
                unset($arquivos_a_validar[$posicao]);
                sort($arquivos_a_validar);
            }            
            
        }
        
        /**
         * Caso ainda contenha algum arquivo no array é sinal que está faltando 
         * algum arquivo na pasta.
         */        
        return $arquivos_a_validar;
        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Grava log de sucesso e erro
     */
    public function gravaLog($mensagem, $tipo) {        
        $this->dao->gravaLog($mensagem, $tipo);        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Retorna os IDs das localidades
     */
    public function getIdsLocalidades() {        
        return $this->dao->getIdsLocalidades();        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Lê o arquivo "DNE_GU_LOCALIDADES.TXT" e para cada linha, exceto a primeira 
     * e a última, insere um registro na tabela de localidades.
     */
    public function importaLocalidadesDNE($ids_localidades) {        
        $caminho_arquivo = $caminho = "/var/www/arquivos_correios/DNE_GU_LOCALIDADES.TXT";
        
        $ponteiro = fopen($caminho_arquivo, "r");
        $i = 0;
        //$localidades = array();
        $localidade = null;
        
        while(!feof ($ponteiro)) {            
            $linha = fgets($ponteiro, 4096);
            
            //não le caso seja a última ou a primeira linha do arquivo
            if(trim($linha) != "" && $linha[0] != '#' && $i > 0) {
                $localidade = array(
                    'localidade_DNE' => (int)trim(substr($linha, 11, 8)),
                    'uf' => trim(substr($linha, 3, 2)),
                    'nome_oficial' => pg_escape_string(trim(substr($linha, 19, 72))),
                    'cep' => trim(substr($linha, 91, 8)),
                    'situacao' => trim(substr($linha, 136, 1)),
                    'tipo' => trim(substr($linha, 135, 1)),
                    'chave_subordinacao' => trim(substr($linha, 143, 8)),
                    'codigo_ibge_municipio' => trim(substr($linha, 154, 7))
                );

                //Se o valor for "N" insere 0 Se o valor for "C" insere 1
                $localidade['situacao'] = $localidade['situacao'] == 'N' ? 0 : 1;                
                $localidade['chave_subordinacao'] = empty($localidade['chave_subordinacao']) ? 0 : $localidade['chave_subordinacao'];
                $localidade['codigo_ibge_municipio'] = empty($localidade['codigo_ibge_municipio']) ? 0 : $localidade['codigo_ibge_municipio'];
                
                //Caso a localidade já exista na base, apenas atualizamos. Senão
                //inserimos.
                if(in_array($localidade['localidade_DNE'], $ids_localidades)) {
                	$atualizou = $this->dao->atualizaLocalidade($localidade);                
                	if(!$atualizou) {
                		return array('success' => false, 'message' => 'Ocorreu uma falha ao atualizar um registro.', 'linha' => $i+2);
                	}
                
                } else {
                	$inseriu = $this->dao->insereLocalidade($localidade);                
                	if(!$inseriu) {
                		return array('success' => false, 'message' => 'Ocorreu uma falha ao inserir um registro.', 'linha' => $i+2);
                	}
                	
                }
            } 
            
            $i++;
        }        
        fclose ($ponteiro);
        
        return array('success' => true);
        
    }

    /**
     * @author Thiago Leal <thiago.brunetti.ext@sascar.com.br>
     * @since 02/09/2016
     * Retorna os IDs dos bairros
     */
    public function getIdsBairros() {        
        return $this->dao->getIdsBairros();        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Limpa a tabela correios_bairros
     */
    public function limpaTabelaBairros() {
        return $this->dao->limpaTabelaBairros();
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Lê o arquivo DNE_GU_BAIRROS.TXT e faz a importação dos bairros
     */
    public function importaBairros($idBairros) {
        
        $caminho_arquivo = $caminho = "/var/www/arquivos_correios/DNE_GU_BAIRROS.TXT";
        
        $ponteiro = fopen($caminho_arquivo, "r");
        $i = 0;
        //$bairros = array();
        $bairro = null;
        
        while(!feof ($ponteiro)) {
            
            $linha = fgets($ponteiro, 4096);
            
            //não le caso seja a última ou a primeira linha do arquivo
            if(trim($linha) != "" && $linha[0] != '#' && $i > 0) {
                $bairro = array(
                    'id_bairro' => (int)trim(substr($linha, 94, 8)),
                    'uf' => trim(substr($linha, 1, 2)),
                    'id_DNE' => (int)trim(substr($linha, 9, 8)),
                    'nome_oficial' => pg_escape_string(trim(substr($linha, 102, 72)))
                );    
                
                //Caso o bairro já exista na base, apenas atualizamos. Senão
                //inserimos.
                if(in_array($bairro['id_bairro'], $idBairros)) {
                    $atualizou = $this->dao->atualizaBairro($bairro);                
                    if(!$atualizou) {
                        return array('success' => false, 'message' => 'Ocorreu uma falha ao atualizar um registro.', 'linha' => $i+2);
                    }
                
                } else {
                    $inseriu = $this->dao->insereBairro($bairro);                
                    if(!$inseriu) {
                        return array('success' => false, 'message' => 'Ocorreu uma falha ao inserir um registro.', 'linha' => $i+2);
                    }
                    
                }          
            } 
            
            $i++;
        }        
        fclose ($ponteiro);
        
        return array('success' => true);
        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Limpa a tabela correios_logradouros
     */
    public function deletaLogradouros() {
        return $this->dao->deletaLogradouros();
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Envia email com o último histórico
     */
    public function enviaEmail($corpo, $sucesso) {
        
        $dados_ultimo_historico = $this->getDadosUltimoHistorico();
        
        $servidorTeste = (isset($_SESSION['servidor_teste'])) ? $_SESSION['servidor_teste'] : 1;
        
        if($servidorTeste == 1) {
            $dados_ultimo_historico['usuemail'] = _EMAIL_TESTE_;
        }
        
        $assunto = $sucesso ? 'Atualização da base dos correios' : 'Falha na atualização da base dos correios';
        
        $mail = new PHPMailer();
        $mail->ClearAllRecipients();

        $mail->IsSMTP();
        $mail->From = 'sistema@sascar.com.br';
        $mail->FromName = 'Intranet SASCAR - E-mail automático';
        $mail->Subject = $assunto;            

        $mail->MsgHTML($corpo);

        $mail->AddAddress($dados_ultimo_historico['usuemail']); 
        $mail->AddBCC('wagner.pereira@sascar.com.br'); 

        $mail->Send();
        echo $mail->ErrorInfo;
        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Recupera os dados do último histórico
     */
    public function getDadosUltimoHistorico() {        
        return $this->dao->getDadosUltimoHistorico();        
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Reinsere os registros inseridos pela sascar
     */
    public function reinsereRegistrosSascar() {
        return $this->dao->reinsereRegistrosSascar();
    }
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @since 08/05/2013
     * Importa os logradouros
     */
    public function importaLogradouros() {
        
        $arquivos = array(        
	    "DNE_GU_GRANDES_USUARIOS.TXT", "DNE_GU_UNIDADES_OPERACIONAIS.TXT",
            "DNE_GU_AC_LOGRADOUROS.TXT", "DNE_GU_AL_LOGRADOUROS.TXT", 
            "DNE_GU_AM_LOGRADOUROS.TXT", "DNE_GU_AP_LOGRADOUROS.TXT", 
            "DNE_GU_BA_LOGRADOUROS.TXT", "DNE_GU_CE_LOGRADOUROS.TXT", 
            "DNE_GU_DF_LOGRADOUROS.TXT", "DNE_GU_ES_LOGRADOUROS.TXT", 
            "DNE_GU_GO_LOGRADOUROS.TXT", "DNE_GU_MA_LOGRADOUROS.TXT", 
            "DNE_GU_MG_LOGRADOUROS.TXT", "DNE_GU_MS_LOGRADOUROS.TXT", 
            "DNE_GU_MT_LOGRADOUROS.TXT", "DNE_GU_PA_LOGRADOUROS.TXT", 
            "DNE_GU_PB_LOGRADOUROS.TXT", "DNE_GU_PE_LOGRADOUROS.TXT", 
            "DNE_GU_PI_LOGRADOUROS.TXT", "DNE_GU_PR_LOGRADOUROS.TXT", 
            "DNE_GU_RJ_LOGRADOUROS.TXT", "DNE_GU_RN_LOGRADOUROS.TXT", 
            "DNE_GU_RO_LOGRADOUROS.TXT", "DNE_GU_RR_LOGRADOUROS.TXT", 
            "DNE_GU_RS_LOGRADOUROS.TXT", "DNE_GU_SC_LOGRADOUROS.TXT", 
            "DNE_GU_SE_LOGRADOUROS.TXT", "DNE_GU_SP_LOGRADOUROS.TXT", 
            "DNE_GU_TO_LOGRADOUROS.TXT"
            );
        
        $ceps = $this->dao->getCepsInseridos();
        $registros = 0;
                
        foreach($arquivos as $arquivo) {
            
            $caminho_arquivo = $caminho = "/var/www/arquivos_correios/$arquivo";
            echo date('d/m/Y H:i:s')."- Processando Arquivo: $arquivo\n";

            $ponteiro = fopen($caminho_arquivo, "r");
            $i = 0;
            //$logradouros = array();
            $logradouro=null;
            
            $ultimo_id_logradouro = (int) $this->getUltimoIdLogradouro();
            
            while(!feof ($ponteiro)) {

                $linha = fgets($ponteiro, 4096);
                //não le caso seja a última ou a primeira linha do arquivo
                if(trim($linha) != "" && $linha[0] != '#' && $i > 0) {
                    
                    $nome_logradouro = trim(trim(substr($linha, 288, 72)).' '.trim(substr($linha, 374, 72)));
                    $nome_logradouro = pg_escape_string(trim(trim(substr($linha, 285, 3)).' '.$nome_logradouro));

			
		if ($arquivo != "DNE_GU_GRANDES_USUARIOS.TXT" && $arquivo != "DNE_GU_UNIDADES_OPERACIONAIS.TXT"){

                    $logradouro = array(
                        'clgoid' => $ultimo_id_logradouro + $i,
                        'clgclcoid' => (int)trim(substr($linha, 9, 8)),
                        'clguf_sg' => trim(substr($linha, 1, 2)),
                        'clgcbaoid_ini' => (int)trim(substr($linha, 94, 8)),
                        'clgcbaoid_fim' => (int)trim(substr($linha, 179, 8)),
                        'clgnome' => $nome_logradouro,                        
                        'clgcep' => trim(substr($linha, 518, 8)),
                        'clgtipo' => trim(substr($linha, 259, 26)),
                        'clgsta_tipo' => trim(substr($linha, 527, 1)),
                        'clgreg_sascar' => 'FALSE'
                    );
		}
		else{

                    $logradouro = array(
                        'clgoid' => $ultimo_id_logradouro + $i,
                        'clgclcoid' => (int)trim(substr($linha, 9, 8)),
                        'clguf_sg' => trim(substr($linha, 1, 2)),
                        'clgcbaoid_ini' => (int)trim(substr($linha, 94, 8)),
                        'clgcbaoid_fim' => (int)trim(substr($linha, 179, 8)),
                        'clgnome' => $nome_logradouro,
                        'clgcep' => trim(substr($linha, 260, 8)),
                        'clgtipo' => trim(substr($linha, 259, 26)),
                        'clgsta_tipo' => trim(substr($linha, 270, 1)),
                        'clgreg_sascar' => 'FALSE'			
		    );
			if ( $arquivo == "DNE_GU_UNIDADES_OPERACIONAIS.TXT" ){
				$logradouro['clgcep'] = trim(substr($linha, 246, 8));
			}

			$linha = fgets($ponteiro, 4096);	
  	            	$logradouro['clgnome'] = trim(trim(substr($linha, 90, 72)).' '.trim(substr($linha, 176, 72)));
			$logradouro['clgtipo'] = trim(substr($linha, 15, 26));
		}
                
	 
                    // Antes de inserir um registro na tabela logradouro, deve ser
                    // validado que o CEP do logradouro a ser inserido não exista na
                    // tabela logradouro. Se existir o logradouro não deve ser inserido.
                    if(!in_array($logradouro['clgcep'], $ceps)) {                    
                    	$inseriu = $this->dao->insereLogradouro($logradouro);                    
                    	if(!$inseriu) {
                    		return array(
                    				'success' => false,
                    				'linha' => $i+2,
                    				'arquivo' => $arquivo);
                    	}                    
                    	$registros++;
                    }
                } 

                $i++;
            }           
            fclose ($ponteiro);          
            
        }        
        return array('success' => true, 'registros_novos' => $registros);
                
    }
    
    /**
     * Apaga o diretório da base correios para nova importação
     */
    public function limparDiretorio(){
    	$caminho = "/var/www/arquivos_correios/";    	
    	if(is_dir($caminho))
    	{
    		$d = dir($caminho);    
    		while (false !== ($entry = $d->read()))
    		{
    			if(file_exists($d->path.$entry) && $entry != '.' && $entry != '..')
    			{
    				unlink($d->path.$entry);
    			}
    		}
    		$d->close();
    	}
    }
    
    public function getUltimoIdLogradouro() {
        return $this->dao->getUltimoIdLogradouro();
    }
    
    public function __construct() {
         $this->dao = new AtualizacaoBaseCorreiosDAO();
    }
    
}
