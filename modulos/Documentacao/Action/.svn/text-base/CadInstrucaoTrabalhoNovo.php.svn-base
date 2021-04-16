<?php

require _SITEDIR_ . 'lib/Atom/Model/BO/InstrucaoTrabalhoBO.php';
require _MODULEDIR_ . 'Documentacao/DAO/CadInstrucaoTrabalhoNovoDAO.php';

class CadInstrucaoTrabalhoNovo {

    /**
     * Instancia a classe que será usada no DAO
     * @global connection $conn
     * @param string $classe
     */
    private function instanciarDAO($classe) {
        global $conn;
        $this->dao = new $classe($conn);
    }

	// ID Departamento 'Gerência de Gestão e Qualidade'
    public $deptoGestao = '34';

    public function index() {
        
    }

    public function cadastrar() {

        $this->gestoresQualidade = UsuarioDAO::getCoordenadoresQualidade();
    }

    /**
     * Substitui caracteres especiais e espaços de forma a adequar para a busca em banco
     * @param string $descricao
     * @return string
     */
    public function tratarDescricaoPesquisa($descricao) {

        $descricao = trim($descricao);

        $texto = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($descricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ', 'aaaaeeiiooouucAAAAEEIIOOUUC'));

        $texto = str_replace(' ', '%', $texto);

        return $texto;
    }
     /**
     * Pesquisar função Gravar
     * @param string $descricao
     * @return string
     */
    public function aprovar() {

        if ($_POST['itoid'] != '') {
            $_POST['docs_selecionados'][0] = $_POST['itoid'];
        }

        try {
            $docs_selecionados = isset($_POST['docs_selecionados']) ? $_POST['docs_selecionados'] : array();

            if (empty($docs_selecionados)) {
                throw new Exception('Selecione os documentos que deseja aprovar.');
            }

            $instrucao_trabalho_bo = new InstrucaoTrabalhoBO();
            $data_return = $instrucao_trabalho_bo->changeStatus($docs_selecionados, 'aprovacao');

            if (empty($data_return)) {
                throw new Exception('Nenhum dos documentos selecionados está pendente.');
            }

            echo json_encode($data_return);
        } catch (Exception $e) {

            echo json_encode(
                    array(
                        'error' => true,
                        'message' => utf8_encode($e->getMessage())
                    )
            );
        }

        /*
         * Requisição ajax, temos que parar o processamento para o html da pagina
         * não ir junto com a resposta
         */
        exit;
    }

    public function excluir($itoid = false) {

// Carrega DAO
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

        $loop = array($itoid);

        if ($_POST['docs_selecionados']) {
            $loop = $_POST['docs_selecionados'];
        }

        foreach ($loop as $key => $val) {

            $itoid = $val;

			// Busca ID do anexo para ser excluido
            $anexoDoc = $cadInstrucaoTrabalho->getAnexosDocumento($itoid);
            $numRow = pg_num_rows($anexoDoc);

			// Deleta Anexo se Existir
            if ($numRow > 0) {
                while ($fetch = pg_fetch_array($anexoDoc)) {
                    $excAnexo = $this->excluirAnexo($fetch['itaoid']);

				// if(!$excAnexo){ return false; }
                }
            }

			//deleta acessos do documento se existir
            $cadInstrucaoTrabalho->delAcesso($itoid);
            $DelDoc = $cadInstrucaoTrabalho->deletarDocumento($itoid);

            if (!$DelDoc) {
                return false;
            }
        }

        if ($_POST['docs_selecionados']) {
            echo json_encode(
                    array(
                        'error' => true,
                        'message' => utf8_encode('Documento excluído com sucesso!')
                    )
            );

            exit;
        }

        return true;
    }

    public function excluirAnexo($itaoid, $cd_usuario) {

		// Carrega DAO
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

        $file = _ABSFILEDIR_ . 'anexos_instrucao/';

        $nomeArquivo = $cadInstrucaoTrabalho->getNomeAnexo($itaoid);

        $file .= $nomeArquivo;

		// Deleta registro do anexo da base
        $excluiranexo = $cadInstrucaoTrabalho->excluiAnexo($itaoid, $cd_usuario);

        if (!$excluiranexo) {
            return false;
        } else {
            if ($nomeArquivo != '') {
				// Deletar Arquivo
                if (is_file("$file")) {
                    unlink("$file");
                    return true;
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Cria nova versão de documento
     * @param string $itoid - ID do documento
     * @param string $cd_usuario - Id do usuário logado
     * @return bolean false em caso de erro ou $array 
     */
    public function novaVersao($itoid, $cd_usuario) {

		// Carrega DAO
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

		// Confere é a ultima versão
        $getUltimaVersao = $this->getUltimaVersao($itoid);

		// Erro retorna FALSE para throw
        if ($getUltimaVersao != null) {
            return false;
        }

		// Cria nova versão
        $novaVersao = $cadInstrucaoTrabalho->setNovaVersao($itoid, $cd_usuario);

		// Erro retorna FALSE para throw
        if (!$novaVersao) {
            return false;
        }

        $dados = pg_fetch_array($novaVersao);

		// Busca anexo e cria outro para nova versão
        $versaoAnexo = $cadInstrucaoTrabalho->setAnexoVersao($itoid, $dados["itoid"], $cd_usuario);

		// Busca permissões e cria outras para nova versão
        $permissaoVersao = $cadInstrucaoTrabalho->permissaoVersao($itoid, $dados["itoid"]);

		// Erro retorna FALSE para throw
        if (!$permissaoVersao) {
            return false;
        }

		// Erro retorna FALSE para throw
        if (!$versaoAnexo) {
            return false;
        }

        $retorno['itoid'] = $dados["itoid"];
        $retorno['itversao'] = $dados["itversao"];

        return $retorno;
    }

    /**
     * Inativa documento e anexos caso existir
     * @param string $itoid - ID do documento
     * @param string $cd_usuario - Id do usuário logado
     * @return bolean false em caso de erro ou $array 
     */
    public function inativarDocumento($itoid, $cd_usuario) {

        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

		// Busca anexos do documento
        $anexoDoc = $cadInstrucaoTrabalho->getAnexosDocumento($itoid);

		// Inativa anexos se existir
        if (pg_num_rows($anexoDoc) > 0) {

            while ($fetch = pg_fetch_array($anexoDoc)) {

                $inativaAnexo = $cadInstrucaoTrabalho->inativaAnexo($fetch['itaoid'], $cd_usuario);

                if (!$inativaAnexo) {
                    return false;
                }
            }
        }

		// Inativa Documento
        $inativaDocumento = $cadInstrucaoTrabalho->inativaDocumento($itoid);

        if (!$inativaDocumento) {
            return false;
        }

        return true;
    }
    
    /**
     * Busca Departamento para select box
     * @return	query Resource
     */
    public function getDepartamentosSelect() {
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

        return $cadInstrucaoTrabalho->departamentosSelect();
    }

    /**
     * Busca Segmentos para select box
     * @return	query Resource
     */
    public function getSegmentossSelect() {
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

        return $cadInstrucaoTrabalho->segmentosSelect();
    }

    /**
     * Monta lista Status conforme depoid
     * @param type $depoid - ID do Departamento
     * @param string $status_busca - Tipo de busca
     * @return string - select <option>
     */
    public function getListaStatus($depoid, $status_busca) {
        $option = $selectedA = $selectedP = $selectedI = $selectedE = '';

        $aprovador = $this->permiteAprovacao(true, $_SESSION['usuario']);

        if (($aprovador) || ($depoid == $this->deptoGestao)) {

            if ($status_busca == "A") {
                $selectedA = "SELECTED";
            }
            if ($status_busca == "P") {
                $selectedP = "SELECTED";
            }
            if ($status_busca == "I") {
                $selectedI = "SELECTED";
            }
            if ($status_busca == "E") {
                $selectedE = "SELECTED";
            }

            $option .= '<option value="">Todos</option>';
            $option .= '<option value="A" ' . $selectedA . '>Ativas</option>';
            $option .= '<option value="P" ' . $selectedP . '>Pendentes</option>';
            $option .= '<option value="I" ' . $selectedI . '>Inativas</option>';
            $option .= '<option value="E" ' . $selectedE . '>Exclu&iacute;das</option>';

            $html = '<tr>
						<td width="15%"><label for="status_busca">Status:</label></td>
						<td width="85%" colspan="2">
							<select name="status_busca">
								' . $option . '
							</select>
						</td>
					</tr>
					
					<tr>
						<td width="15%"><label for="status_busca">Exibir registros excluídos:</label></td>
						<td width="85%" colspan="2">
							<input type="checkbox" class="checkbox" id="pesq_excluidos" name="pesq_excluidos" value="t" />
						</td>
					</tr>';
        }

        return $html;
    }

    /**
     * Monta where da consulta para pesquisa de documento
     * @param type $depoid - ID do Departamento
     * @param array $dados 
     * @param array $dadosUsuario - Dados do usuario da sessão
     * @return $query Resource
     */
    public function getDocumentos($Dados, $dadosUsuario) {
				
        $cd_usuario = $Dados['cd_usuario'];
        $depoid = $Dados['depoid'];
        $tipo_busca = $Dados['tipo_busca'];
        $orderBy = $this->getOrderDoc($Dados['orderBy']);
        $usuario_autorizado = $Dados['usuario_autorizado'];
        $pesq_excluidos = $Dados['pesq_excluidos'];
        $descricao= $Dados['descricao'];

        $addquery = "";
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

        if ($tipo_busca) {
            $addquery .= " and ittipo = '$tipo_busca' ";
        }

        if ($depoid != '') {
            $addquery .= " and itdepoid = $depoid";
        }

        if (!$usuario_autorizado) {
            $addquery .= " and itexclusao is null ";
            $addquery .= " and itstatus = 'R' ";
        } else {
            // Monta o status da pesquisa
            if ($Dados['status_busca'] != '') {
                $addquery .= " and itstatus = '" . $Dados['status_busca'] . "' ";
            } else {
                if ($dadosUsuario['depoid'] == $this->deptoGestao) {
                    $addquery .= " and (itstatus = 'I' or itstatus = 'E' or itstatus = 'A' or itstatus = 'P')";
                } else {
                    $addquery .= " and (itstatus = 'A' or itstatus = 'P' or itstatus = 'I') ";
                }
            }
        }

        if ($pesq_excluidos != "t") {
            $addquery .= " AND itexclusao IS NULL ";
        }

        if (($Dados['dataDe']) && ($Dados['dataAte'])) {
            $addquery .= " AND itdt_aprovacao between '" . $Dados['dataDe'] . " 00:00:00' AND '" . $Dados['dataAte'] . " 23:59:59' ";
        }
		
        $descricao = isset($_POST['itdescricao']) ? $_POST['itdescricao'] : null;

        $palavra_chave = isset($_POST['palavra_chave']) ? $_POST['palavra_chave'] : '';

        if (trim($palavra_chave) != "") {

            $addquery .= " AND LOWER(TRANSLATE(Itdescricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ','aaaaeeiiooouucAAAAEEIIOOUUC'))  ILIKE '%" . pg_escape_string($palavra_chave) . "%'";
        }

        $segmento = isset($_POST['itseoid']) ? $_POST['itseoid'] : null;

         if (($segmento) != "") {
            $addquery .= " AND itseoid = " . intval($segmento) . "";
        }
       
        $addquery .= " and ittipo != 'ES' ";

        $query = $cadInstrucaoTrabalho->getDocumentos($addquery, $cd_usuario, $orderBy, $Dados['orderAsc']);

        return $query;
    }

	
	public function permissoes($dados, $dadosUser, $aprovador){
		
		$retorno['view']	= false;
		$retorno['editar']	= false;
		$retorno['versao']	= false;	
		$retorno['excluir']	= false;
		$retorno['inativa']	= false;

		// Carrega permissão por cargos
		$cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();
        $permissaoCargos = $cadInstrucaoTrabalho->getPermissaoCargos($dados['it_id'], $dadosUser['cargo']);
		
		// Confere se documento restrito
		$getDocRestrito = $cadInstrucaoTrabalho->getDocRestrito($dados['it_id']);

		// Usuário Gestor
		if($dadosUser['depoid'] == $this->deptoGestao){
			$retorno['view']	= true;
			$retorno['editar']	= true;
			$retorno['versao']	= true;	
			$retorno['excluir']	= true;
			$retorno['inativa']	= true;
		
			return $retorno;
		}

		// Usuário Aprovador
		if($aprovador){
			return $this->permissoesAprovador($dados, $dadosUser, $permissaoCargos, $getDocRestrito, $retorno);			
		}

		// Usuário Comum
		switch($dados['itstatus']){
			case 'A':

				// Confere Departamento
				if ($dados['itdepoid'] == $dadosUser['depoid']){
					
					if($dados['itusuoid_incl'] == $dadosUser['oid']){
						$retorno['view']   = true;
						$retorno['editar'] = true;
						$retorno['versao'] = true;

					}elseif($permissaoCargos){

						foreach ($permissaoCargos as $key => $val){

							if (($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'V')) {
								$retorno['view'] = true;
							}

							if(($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'E')) {
								$retorno['view']   = true;
								$retorno['editar'] = true;
								$retorno['versao'] = true;
							}

						}

					}else{					
						if($getDocRestrito == 'f'){
							$retorno['view'] = true;
						}
					}

				}else{

					if($permissaoCargos){

						foreach ($permissaoCargos as $key => $val) {
							if (($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'V')) {
								$retorno['view'] = true;
								// $retorno['editar'] = true; Outro Dpto Só pode visualizar não criar Versão
							}
							
							if(($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'E')) {
								$retorno['view']   = true;
								$retorno['editar'] = true;
								$retorno['versao'] = true;
							}
						}

					}elseif($dados['itusuoid_incl'] == $dadosUser['oid']){
						$retorno['view'] = true;
						$retorno['versao'] = true;
					}else{
						if($getDocRestrito == 'f'){
							$retorno['view'] = true;
						}
					}
				}

			break;
			case 'P':
				// Confere Criador
				if ($dados['itusuoid_incl'] == $dadosUser['oid']){
					$retorno['view']	= true;
					$retorno['editar']	= true;
					$retorno['excluir']	= true;
				
				 // Confere Permissão
				}else if($permissaoCargos){

					foreach ($permissaoCargos as $key => $val) {
						if (($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'V')) {
							$retorno['view'] = true;
						}
						
						if(($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'E')) {
							$retorno['view']   = true;
							$retorno['editar'] = true;
						}
					}
				}

			break;
		}

		return $retorno;
	}
	
	public function permissoesAprovador($dados, $dadosUser, $permissaoCargos, $getDocRestrito, $retorno){
	
		switch($dados['itstatus']){
			case 'A':
			
				// Confere Departamento
				if ($dados['itdepoid'] == $dadosUser['depoid']){
					$retorno['view']	= true;
					$retorno['editar']	= true;					
					$retorno['versao']	= true;
					$retorno['inativa']	= true;					
				}else{
				
					if($permissaoCargos){

						foreach ($permissaoCargos as $key => $val) {							
							if (($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'V')) {
								$retorno['view'] = true;
							}
							
							if(($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'E')) {
								$retorno['view'] = true;
								$retorno['editar']  = true;
								$retorno['versao']  = true;
								$retorno['inativa'] = true;
							}
						}

					}else{
						if($getDocRestrito == 'f'){
							$retorno['view'] = true;
						}
					}
				}

			break;
			case 'P':

				if ($dados['itdepoid'] == $dadosUser['depoid']){
					$retorno['view']	= true;
					$retorno['editar']	= true;
					$retorno['excluir']	= true;

				}else if($permissaoCargos){

					foreach ($permissaoCargos as $key => $val) {
						if (($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'V')) {
							$retorno['view'] = true;
						}
						
						if(($val['itaprhoid'] == $dadosUser['cargo']) && ($val['itatipo_acesso'] == 'E')) {
							$retorno['view']   = true;
							$retorno['editar'] = true;
							$retorno['excluir'] = true;
						}
					}

				}elseif($dados['itusuoid_incl'] == $dadosUser['oid']){
					$retorno['view'] = true;
					$retorno['editar'] = true;
					$retorno['excluir']	= true;
				}

			break;
			case 'I':

				if ($dados['itdepoid'] == $dadosUser['depoid']){
					$retorno['view'] = true;
				}
				
			break;
		}

		return $retorno;
	}
	
    /**
     * Confere se usuário tem permissão de aprovação
     * @param bolean $usuario_autorizado - Função 'aprovacao_controle_docto'
     * @param array $dadosUser - Dados do usuário da sessão
     * @return	bolean
     */
    public function permiteAprovacao($usuario_autorizado, $dadosUser) {

        $retorno = false;

		// Busca usuario aprovador
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();
        $aprovador = $cadInstrucaoTrabalho->getAprovador($dadosUser['oid']);

		// Carrega permissão por cargos
		// $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();
        // $permissaoCargos = $cadInstrucaoTrabalho->getPermissaoCargos($dados['it_id'], $dadosUser['cargo']);

		// Confere se usuário tem a função 'aprovacao_controle_docto'
        if ($usuario_autorizado) {
			// Confere departamento 'Gerência de Gestão e Qualidade'
            if ($dadosUser['depoid'] == $this->deptoGestao) {
                $retorno = true;
            }

            if ($aprovador > 0) {
				$retorno = true;
            }
        }

        return $retorno;
    }

    /**
     * Confere se usuário tem permissão para excluir
     * @param bolean $usuario_autorizado - Função 'aprovacao_controle_docto'
     * @param array $dadosUser - Dados do usuário da sessão
     * @param string $itoid - ID do documento
     * @return	string $html - Botão Excluir
     */
    public function permiteExcluir($aprovador, $dadosUser, $itoid) {

        $html = '';
		
		$cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();		
		$getDados = $cadInstrucaoTrabalho->getDadosDocumento($itoid);

        $dados = $getDados;
        $dados['it_id'] = $itoid;
		
		// Confere se permite excluir - Mesma regra para edição
        $permiteExcluir = $this->permissoes($dados, $dadosUser, $aprovador);

        if ($permiteExcluir['excluir']) {
            $html = '<input type="button" class="botao" name="btn_excluir" id="btn_excluir" value="Excluir" style="width:90px;" OnClick="excluir()">';
        }

        return $html;
    }

    public function getOrderDoc($orderby) {

        switch ($orderby) {
            case '1': $order = ' ittipo ';
                break;
            case '2': $order = ' itdescricao ';
                break;
            case '3': $order = ' departamento ';
                break;
            case '4': $order = ' itelaborado ';
                break;
            case '5': $order = ' dt_elaboracao ';
                break;
            case '6': $order = ' aprovador ';
                break;
            case '7': $order = ' dt_aprovacao ';
                break;
            case '8': $order = ' itstatus ';
                break;
            default : $order = ' itdescricao ';
        }

        return $order;
    }
	
	// Combo Area conforme acesso usuário
	public function selectBoxArea($dadosUsuario, $itoid, $itdepoid, $permiteAprovacao){

		$cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();		

		// Caso aprovador ou Gestor Lista todas as áreas
		if($permiteAprovacao){
			$html = '<option value="">--Escolha--</option>';
			$getArea = $cadInstrucaoTrabalho->getArea();

		}else{

			$html = '';

			// Mostra departamento do Usuário
			if($itoid == ''){
				$getArea = $cadInstrucaoTrabalho->getArea($dadosUsuario['oid']);
			}else{
				// Confere permissão para Departamento/Cargo
				$getArea = $cadInstrucaoTrabalho->getArea($dadosUsuario['oid'], $itoid, $dadosUsuario['cargo']);

				// Mostra Departamento do usuário 
				if(!$getArea){
					$getArea = $cadInstrucaoTrabalho->getArea($dadosUsuario['oid']);
				}
			}

		}

		foreach($getArea as $val){
			$selected = ($itdepoid == $val['depoid']) ? 'SELECTED' : '';

			$html .= '<option value="'.$val['depoid'].'" '.$selected.'>'.$val['depdescricao'].'</option>';
		}

		return $html;
	}

    public function getUltimaVersao($itoid) {

        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

		// Busca Versões dos documentos para guardar a ultima
        $getVersoes = $cadInstrucaoTrabalho->getVersoes($itoid);

        $retorno = ($getVersoes == true) ? true : false;

        return $retorno;
    }

    /* Confere versão dos anexos para excluir ou inativar
     * @param string $itoid - ID Documento
     * @param string $itaoid - ID Anexo
     * @param string $caminho - Caminho Arquivo
     * @return string $value - (E)Excluir - (I)Inativar
     */

    public function botaoAnexo($itoid, $itaoid, $caminho, $itstatus) {

		// Carrega DAO
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

		// Carrega dados do anexo
        $dadosAnexo = $cadInstrucaoTrabalho->getDadosAnexo($itoid, $itaoid);

        $html = '';

        if ($itstatus == 'P') {
            if ($dadosAnexo['itaitoid'] == $dadosAnexo['itaoid_doc']) {
                $html = '<a href="#" onclick="excluir_anexo(' . $itaoid . ');"><img src="images/icones/t' . $caminho . '1/x.jpg"></a>';
            } elseif ($dadosAnexo['itainativacao'] != '') {
                $html = '';
            } else {
                $html = '<a href="#" onclick="inativar_anexo(' . $itaoid . ');"><img src="images/icones/t' . $caminho . '1/exclamation.jpg"></a>';
            }
        }

        return $html;
    }

    /* Criar documento para poder vincular um anexo
     * @param string $cd_usuario - Código do usuário
     * @return string ID do Documento
     */

    public function setDocumentoAnexo($cd_usuario) {

		// Carrega DAO
        $cadInstrucaoTrabalho = new CadInstrucaoTrabalhoNovoDAO();

        $depoid = $_SESSION['usuario']['depoid'];
        $dados['ittipo'] = $_POST['ittipo'];

        $dados['itidentificacao'] = ($_POST['itidentificacao'] == '') ? '-- preencher --' : $_POST['itidentificacao'];
        $dados['itdescricao'] = ($_POST['itdescricao'] == '') ? '-- preencher --' : $_POST['itdescricao'];
        $dados['itelaborado'] = ($_POST['itelaborado'] == '') ? '-- preencher --' : $_POST['itelaborado'];
        $dados['itdepoid'] = ($_POST['itdepoid'] == '') ? $depoid : $_POST['itdepoid'];
        $dados['itdt_elaboracao'] = ($_POST['itdt_elaboracao'] == '') ? date('d/m/Y') : $_POST['itdt_elaboracao'];
		$dados['itrestrito'] = ($_POST['itrestrito'] == 't') ? 'true' : 'false';

        $dados['itrecursos'] = $_POST['itrecursos'];
        $dados['itobjetivo'] = $_POST['itobjetivo'];
        $dados['itusuarios'] = $_POST['itusuarios'];
        $dados['itdoc_ref'] = $_POST['itdoc_ref'];
        $dados['itacao'] = $_POST['itacao'];
        $dados['itregistro'] = $_POST['itregistro'];
        $dados['itadescricao'] = $_POST['itadescricao'];
        $dados['itresponsabilidade'] = $_POST['itresponsabilidade'];
        $dados['itmetodologia'] = $_POST['itmetodologia'];
        $dados['ititem_verificacao'] = $_POST['ititem_verificacao'];
        $dados['ithistorico_revisao'] = $_POST['ithistorico_revisao'];

		// Cadastra documento para anexo
        $itoid = $cadInstrucaoTrabalho->setDocumentoAnexo($dados, $cd_usuario);
        $itoid = ($itoid) ? $itoid : false;

        return $itoid;
    }

    public function __set($var, $value) {
        return $this->$var = $value;
    }

    public function __get($var) {
        return $this->$var;
    }

}