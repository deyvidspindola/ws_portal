<?php

/**
 * Classe RelDescontosConceder.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 * 
 */
class RelDescontosConceder {

    /**
     * Objeto DAO da classe.
     * 
     * @var RelDescontosConcederDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
    
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    /**
     * Contém dados a serem utilizados na View.
     * 
     * @var stdClass 
     */
    private $view;

    /**
     * Método construtor.
     * 
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        //Cria objeto da view
        $this->view = new stdClass();
        //Mensagem
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = null;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Status de uma transação 
        $this->view->status = false;
    }

    /**
     * Método padrão da classe. 
     * 
     * Reponsável também por realizar a pesquisa invocando o método privado
     * 
     * @return void
     */
    public function index() {
        try {
            
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            if(isset($_SESSION['flash_message'])) {                
                $this->view->dados = $_SESSION['flash_message']['dados'];
                unset($_SESSION['flash_message']['dados']);
            }

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'gerar_xls' ) {
                    
                $file = "descontos_a_conceder_".date('YmdHis').".xlsx";
                $dir = '/var/www/docs_temporario/';
                
                $this->geraXLS($this->view->parametros, $dir, $file);
                
                $this->view->arquivo = $file;

                $this->view->mensagemSucesso = 'Arquivo gerado com sucesso.';
        
            }

            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'enviar_email' ) {
                    
                $file = "descontos_a_conceder_".date('YmdHis').".xlsx";
                $dir = '/var/www/docs_temporario/';
                
                $this->geraXLS($this->view->parametros, $dir, $file);
                
                $this->envariRelatorioPorEmail($dir . $file);
                
                $_SESSION['flash_message']['tipo'] = 'sucesso';
                $_SESSION['flash_message']['mensagem'] = 'E-mail enviado com sucesso.';                
        
            }            

        } catch (ErrorException $e) {
		      
            $_SESSION['flash_message']['tipo'] = 'erro';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();  

            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {

            $_SESSION['flash_message']['tipo'] = 'alerta';
            $_SESSION['flash_message']['mensagem'] = $e->getMessage();  
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        
        if($this->view->parametros->acao !== 'enviar_email') {
            
            //Inclir a view padrão        
            require_once _MODULEDIR_ . "Relatorio/View/rel_descontos_conceder/index.php";
        }
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Parametros tradados
     * 
     * @retrun stdClass
     */
    private function tratarParametros() {
        $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        }
        
        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                
                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
        return $retorno;
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     * 
     * @return void
     */
    private function inicializarParametros() {
        
        //Verifica se os parametro existem, senão iniciliza todos
		$this->view->parametros->tipo_pessoa = isset($this->view->parametros->tipo_pessoa) ? $this->view->parametros->tipo_pessoa : "J" ; 

        $this->view->parametros->nm_usuario = $_SESSION['usuario']['nome'];

        //viariavel para popular combo de obrigação Financeira de Desconto
        $this->view->parametros->obrigacaoFinanceiraDesconto = $this->dao->buscarObrigacaoFinanceiraDesconto();

        //variavel que seta usuario que icluiu crédito futuro
        $this->view->parametros->usuarioInclusaoCreditoFuturo = $this->dao->buscarUsuarioInclusaoCreditoFuturo();

        $this->view->parametros->motivoDoCredito = $this->dao->buscarMotivoDoCredito();

        $this->view->parametros->usuariosAprovadores = $this->dao->obterUsuariosAprovacao($_SESSION['usuario']['depoid']);
        
    }
    

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {
        
        $this->validarCamposPesquisa($filtros);

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;
        
        return $resultadoPesquisa;
    }

    /**
     * Validar os campos obrigatórios da pesquisa.
     * 
     * @param stdClass $dados Dados a serem validados
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function validarCamposPesquisa(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $obrigatorios = true;
        $valorAteMenor = true;
        $valorMenorIgualZero = true;
        $valorMaiorIgualCem = true;
        $percentual = false;
        $monetario = false;

        /**
         * Verifica os campos obrigatórios
         */
        //verifico se o periodo de inclusao de inicio foi informado
        if (!isset($dados->periodo_inclusao_ini) || trim($dados->periodo_inclusao_ini) == '') {
            $camposDestaques[] = array(
                'campo' => 'periodo_inclusao_ini'
            );
            $obrigatorios = false;
        }

        //verifico se o periodo de fim de inclusao foi informado
        if (!isset($dados->periodo_inclusao_fim) || trim($dados->periodo_inclusao_fim) == '') {
            $camposDestaques[] = array(
                'campo' => 'periodo_inclusao_fim'
            );
            $obrigatorios = false;
        }

        //verifico se tipo de desconto foi informado
        if (!isset($dados->cfotipo_desconto) || trim($dados->cfotipo_desconto) == '') {
            $camposDestaques[] = array(
                'campo' => 'cfotipo_desconto'
            );
            $obrigatorios = false;
        } else {

            //no caso de percentual
            if ($dados->cfotipo_desconto == "1") {

                if ((isset($dados->cfopercentual_ate) && trim($dados->cfopercentual_ate) != '') && (isset($dados->cfopercentual_de) && trim($dados->cfopercentual_de) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfopercentual_de'
                    );
                    $obrigatorios = false;
                }

                if ((isset($dados->cfopercentual_de) && trim($dados->cfopercentual_de) != '') && (isset($dados->cfopercentual_ate) && trim($dados->cfopercentual_ate) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfopercentual_ate'
                    );
                    $obrigatorios = false;
                }

                $percentualDePreenchido = isset($dados->cfopercentual_de) && trim($dados->cfopercentual_de) != '';
                $percentualAtePreenchido = isset($dados->cfopercentual_ate) && trim($dados->cfopercentual_ate) != '';

                if (($percentualDePreenchido || $percentualAtePreenchido) && $obrigatorios) {

                    $percentualDe = str_replace(',', '.', $dados->cfopercentual_de);
                    $percentualAte = str_replace(',', '.', $dados->cfopercentual_ate);

                    //verifico se o campo ate é menor que o campo de
                    if (($percentualDePreenchido && $percentualAtePreenchido) && (floatval($percentualAte) < floatval($percentualDe))) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_ate'
                        );
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_de'
                        );
                        $valorAteMenor = false;
                    }


                    //verifico se o campo é menor ou igual a zero
                    if ($percentualDePreenchido && floatval($percentualDe) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_de'
                        );
                        $percentual = true;
                        $valorMenorIgualZero = false;
                    }

                    //verifico se o campo é menor ou igual a zero
                    if ($percentualAtePreenchido && floatval($percentualAte) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_ate'
                        );
                        $percentual = true;
                        $valorMenorIgualZero = false;
                    }

                    //verifico se o campo é maior que 100
                    if ($percentualDePreenchido && floatval($percentualDe) > 100 && $valorMenorIgualZero) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_de'
                        );
                        $percentual = true;
                        $valorMaiorIgualCem = false;
                    }

                    //verifico se o campo é maior que 100
                    if ($percentualAtePreenchido && floatval($percentualAte) > 100 && $valorMenorIgualZero) {
                        $camposDestaques[] = array(
                            'campo' => 'cfopercentual_ate'
                        );
                        $percentual = true;
                        $valorMaiorIgualCem = false;
                    }
                }
            }

            //no caso de valor monetario
            if ($dados->cfotipo_desconto == "2") {

                if ((isset($dados->cfovalor_ate) && trim($dados->cfovalor_ate) != '') && (isset($dados->cfovalor_de) && trim($dados->cfovalor_de) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfovalor_de'
                    );
                    $obrigatorios = false;
                }

                if ((isset($dados->cfovalor_de) && trim($dados->cfovalor_de) != '') && (isset($dados->cfovalor_ate) && trim($dados->cfovalor_ate) == '')) {
                    $camposDestaques[] = array(
                        'campo' => 'cfovalor_ate'
                    );
                    $obrigatorios = false;
                }

                $valorDePreenchido = isset($dados->cfovalor_de) && trim($dados->cfovalor_de) != '';
                $valorAtePreenchido = isset($dados->cfovalor_ate) && trim($dados->cfovalor_ate) != '';

                if (($valorDePreenchido || $valorAtePreenchido) && $obrigatorios) {

                    $valorDe = str_replace('R$', '', $dados->cfovalor_de);
                    $valorDe = str_replace('.', '', $valorDe);
                    $valorDe = str_replace(',', '.', $valorDe);
                    $valorDe = trim($valorDe);

                    $valorAte = str_replace('R$', '', $dados->cfovalor_ate);
                    $valorAte = str_replace('.', '', $valorAte);
                    $valorAte = str_replace(',', '.', $valorAte);
                    $valorAte = trim($valorAte);

                    //verifico se o campo ate é menor que o campo de
                    if (($valorDePreenchido && $valorAtePreenchido) && (floatval($valorAte) < floatval($valorDe))) {
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_ate'
                        );
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_de'
                        );
                        $valorAteMenor = false;
                    }

                    //verifico se o campo é menor ou igual a zero
                    if ($valorDePreenchido && floatval($valorDe) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_de'
                        );
                        $monetario = true;
                        $valorMenorIgualZero = false;
                    }

                    //verifico se o campo é menor ou igual a zero
                    if ($valorAtePreenchido && floatval($valorAte) <= 0 && $valorAteMenor) {
                        $camposDestaques[] = array(
                            'campo' => 'cfovalor_ate'
                        );
                        $monetario = true;
                        $valorMenorIgualZero = false;
                    }
                }
            }
        }


        if (!$obrigatorios) {
            $this->view->dados = $camposDestaques;
            $_SESSION['flash_message']['dados'] = $this->view->dados;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if (!$valorMenorIgualZero) {

            $this->view->dados = $camposDestaques;

            $_SESSION['flash_message']['dados'] = $this->view->dados;

            if ($percentual) {
                throw new Exception("O percentual do desconto não pode ser igual a 0%.");
            }

            if ($monetario) {
                throw new Exception("O valor do desconto não pode ser igual a 0.");
            }
        }

        if (!$valorMaiorIgualCem) {

            $this->view->dados = $camposDestaques;

            $_SESSION['flash_message']['dados'] = $this->view->dados;

            if ($percentual) {
                throw new Exception("O percentual do desconto não pode ser maior que 100%.");
            }
        }

        if (!$valorAteMenor) {
            $this->view->dados = $camposDestaques;

            $_SESSION['flash_message']['dados'] = $this->view->dados;
            

            throw new Exception("O campo Até não pode ser menor que o campo De.");
        }
    }

    /**
     * Gera o arquivo xls em disco
     * @param array $filtros
     */
    public function geraXLS($filtros, $path, $file) {
        
        // Arquivo modelo para gerar o XLS
        $arquivoModelo = _MODULEDIR_.'Relatorio/View/rel_descontos_conceder/template_relatorio_descontos_conceder.xlsx';
            
        try {
            
            // Instância PHPExcel
            $reader = PHPExcel_IOFactory::createReader("Excel2007");
                
            // Carrega o modelo
            $PHPExcel = $reader->load($arquivoModelo);
                
            // Processa o relatório
            $relatorio = $this->pesquisar($filtros);
            
            $obrigacao = empty($filtros->cfoobroid_desconto) ? '' : $this->dao->buscarObrigacaoFinanceiraPorId($filtros->cfoobroid_desconto);
            
            foreach($filtros->usuarioInclusaoCreditoFuturo as $usuarioInclusao) {
                if($usuarioInclusao->cd_usuario == $filtros->cfousuoid_inclusao) {
                    $usuario = $usuarioInclusao->nm_usuario;
                    break;
                }
            }

            if(is_array($filtros->cfocfmcoid) && in_array('-1', $filtros->cfocfmcoid)) {
                $motivos = 'Todos';
            } else if(is_array($filtros->cfocfmcoid)){

                foreach ($filtros->cfocfmcoid as $key) {
                    foreach($filtros->motivoDoCredito as $motivoDoCredito) {
                        if($motivoDoCredito->cfmcoid == $key) {
                            $motivos[] = $motivoDoCredito->cfmcdescricao;                        
                        }
                    }
                }

                $motivos = implode(', ', $motivos);

            } else {
                foreach($filtros->motivoDoCredito as $motivoDoCredito) {
                    if($motivoDoCredito->cfmcoid == $filtros->cfocfmcoid) {
                        $motivos = $motivoDoCredito->cfmcdescricao;
                        break;
                    }
                }
            }

            switch ($filtros->cfotipo_desconto) {
                case 1:
                    $tipo_desconto = 'Percentual';
                    break;

                case 2:
                    $tipo_desconto = 'Valor';
                    break;

                case 3:
                    $tipo_desconto = 'Todos';
                    break;                
               
            }

            switch ($filtros->cfoforma_aplicacao) {
                case 1:
                    $forma_aplicacao = 'Integral';
                    break;

                case 2:
                    $forma_aplicacao = 'Parcelas';
                    break;

                case 3:
                    $forma_aplicacao = 'Todos';
                    break;                
               
            }

            switch ($filtros->cfoforma_inclusao) {
                case 1:
                    $forma_inclusao = 'Manual';
                    break;

                case 2:
                    $forma_inclusao = 'Automático';
                    break;

                case 3:
                    $forma_inclusao = 'Todos';
                    break;                
               
            }              

            $PHPExcel->getActiveSheet()->setCellValue('E2', $filtros->periodo_inclusao_ini . ' a ' . $filtros->periodo_inclusao_fim);
            $PHPExcel->getActiveSheet()->setCellValue('E3', $filtros->tipo_pessoa == 'J' ? utf8_encode($filtros->razao_social) : utf8_encode($filtros->nome));
            $PHPExcel->getActiveSheet()->setCellValue('E4', trim($filtros->cfoancoid));
            $PHPExcel->getActiveSheet()->setCellValue('E5', utf8_encode($obrigacao));
            $PHPExcel->getActiveSheet()->setCellValue('E6', utf8_encode($usuario));
            $PHPExcel->getActiveSheet()->setCellValue('E7', $tipo_desconto);
            $PHPExcel->getActiveSheet()->setCellValue('E8', utf8_encode($forma_aplicacao));
            $PHPExcel->getActiveSheet()->setCellValue('E9', utf8_encode($forma_inclusao));
            $PHPExcel->getActiveSheet()->setCellValue('E10', utf8_encode($motivos));
                    
            $linha = 15;
            foreach ($relatorio as $row) {             
                
                $parcelas_label = $row->parcelas_ativas > 1 ? ' parcelas' : ' parcela';

                $data_aprovacao = $row->cfoforma_aplicacao == 1 ? date('m/Y', strtotime(''.$row->cfodt_avaliacao.' + 1 month')) : date('m/Y', strtotime(''.$row->cfodt_avaliacao.' + '.$row->cfpnumero.' month'));

                //$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, $row->cfooid);
                $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, $row->cfodt_inclusao);
                //$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, $data_aprovacao);
                $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->clinome));
                //$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, $row->clicpfcnpj);
                $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, $row->cfoancoid);                
                //$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->cfmcdescricao));
                $PHPExcel->getActiveSheet()->setCellValue('H'.$linha, $row->cfotipo_desconto);
                $PHPExcel->getActiveSheet()->setCellValue('I'.$linha, trim($row->valorDesconto));
                $PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->cfoforma_aplicacao));
                $PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($row->cfoforma_inclusao));                
                $PHPExcel->getActiveSheet()->setCellValue('L'.$linha, $row->cfoforma_aplicacao_id == '1' ? $row->cfosaldo : $row->cfpnumero . '/' . $row->parcelas_ativas . $parcelas_label);
                
                $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);                
                $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('L'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        
                $linha++;
            }
        
            $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);                
            $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);                        
        

            $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $writer->setPreCalculateFormulas(false);
            
            if(!file_exists($path) || !is_writable($path)) {
                throw new ErrorException('Houve um erro ao gerar o arquivo.');
            }
            
            $writer->save($path.$file);
            
            return true;
        }
        catch (Exception $e) {      
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Evia o relatório (excel) por email
     * @param string $dir
     * @param string $file
     */
    public function envariRelatorioPorEmail($file) {

        //array com os destinatarios do email
        $emails = explode(';', $this->view->parametros->email_para);
        
        if(!empty($emails[0])) {
            foreach ($emails as $email) {

                if(!$this->validarEmail($email)) {                    
                    throw new Exception("Endereços de e-mail no formato inválido.");
                    
                }

                $lista_email[] = $email;
            }
        }

        $emails_cc = explode(';', $this->view->parametros->email_cc);
        
        foreach ($emails_cc as $email) {

            if(!$this->validarEmail($email)) {
                throw new Exception("Endereços de e-mail no formato inválido.");
                
            }

            $lista_email_cc[] = $email;
        }       

        if($_SESSION['servidor_teste'] == 1){
            $lista_email = array(_EMAIL_TESTE_);
            $lista_email_cc = array(_EMAIL_TESTE_);
        }         
        
        
        $mail = new PHPMailer();
        $mail->ClearAllRecipients();

        $mail->IsSMTP();
        $mail->From = 'sistema@sascar.com.br';
        $mail->FromName = $_SESSION['usuario']['nome'];
        $mail->Subject = utf8_decode($this->view->parametros->email_assunto);
        
        $mail->MsgHTML(utf8_decode(nl2br($this->view->parametros->email_corpo)));

        //ANEXA O ARQUIVO ZIP NO EMAIL
        $mail->AddAttachment($file);

        //adiciona os destinatarios
        foreach( $lista_email as $destinatario ){
            $mail->AddAddress($destinatario);                
        }

        //adiciona os destinatarios
        foreach( $lista_email_cc as $destinatario ){
            $mail->AddCC($destinatario);                
        }
        
        $emailEnviado = $mail->Send();

        if(!$emailEnviado) {            
            throw new Exception("Houve uma falha no envio do email.");            
        }

    }

    private function validarEmail($email) {
        
        if (substr_count($email, "@") == 0) {
            // Verifica se o e-mail possui @
            return false;
        }

        $parseEmail = explode("@", $email);

        if (strlen($parseEmail[0]) < 1) {
            //Verifica se o email tem mais de 1 caracter antes do @
            return false;
        }

        if (!checkdnsrr($parseEmail[1], "MX")) {
            // Verificar se o domínio existe
            return false;
        }

        return true;
    }
    

}

