<?php

class CadLayoutEmailsDAO
{
    protected $_adapter;
    protected $_descLayoutsNaoEditaveis = array("Layout para ve%culos recuperados");

    public function __construct()
    {
        global $conn;
        $this->_adapter = $conn;
    }

    /**
     * Executa uma query
     * @param    string        $sql        SQL a ser executado
     * @return    resource
     */
    protected function _query($sql)
    {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = @pg_query($this->_adapter, $sql);

        if ($result === false)
        {
            throw new Exception(pg_last_error($this->_adapter));
        }

        return $result;
    }

    /**
     * Conta os resultados de uma consulta
     * @param    resource    $results
     * @return    int
     */
    protected function _count($results)
    {
        return pg_num_rows($results);
    }

    /**
     * Retorna os resultados de uma consulta num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    protected function _fetchAll($results)
    {
        return pg_fetch_all($results);
    }

    /**
     * Retorna o resultado de uma coluna num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    protected function _fetchAssoc($result)
    {
        return pg_fetch_assoc($result);
    }

    /**
      * Retorna o resultado como um vetor de objetos
      * @param  resource     $results
      * @return  array
      */
    public function _fetchObj($results)
    {
        $rows = array_map(function($item) {
            return (object) $item;
        }, $this->_fetchAll($results));

        return $rows;
    }

    /**
     * Insere valores numa tabela
     * @param    string    $table
     * @param    array    $values
     * @return    boolean
     */
    protected function _insert($table, $arr)
    {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = @pg_insert($this->_adapter, $table, $arr);

        if ($result === false)
        {
            throw new Exception(pg_last_error($this->_adapter));
        }

        return $result;
    }

    /**
     * Escapa os elementos de um vetor
     * @param    array    $arr
     * @return    array
     */
    protected function _escapeArray($arr)
    {
        array_walk($arr, function(&$item, $key) {
            $item = pg_escape_string($item);
        });

        return $arr;
    }

    /**
     * Não existe campo em banco de dados para verificar se layout é editavel ou nao.
     * Neste caso deve ser identificado o layout conforme a descricao do mesmo.
     * conforme documentação.
     * @return String condicao.
     */
    private function _getCondicaoEditavel(){
    	//condicao para editavel ou nao
    	$condicaoEditavel=array();
    	foreach ($this->_descLayoutsNaoEditaveis as $cabecalho){
    		$condicaoEditavel[] = "(seedescricao ilike '".pg_escape_string($cabecalho)."')";
    	}

    	return "case when (".implode(" or ", $condicaoEditavel).") then false else true end as editavel";
    }


    /**
     * Busca a lista de funcionalidades ativas
     * @return	array
     */
    public function getListaFuncionalidades()
    {
    	$sql = "SELECT
    				seefoid, seefdescricao
    			FROM servico_envio_email_funcionalidade
    			WHERE
    				servico_envio_email_funcionalidade.seefdt_exclusao IS NULL
    			ORDER BY
    				seefdescricao ASC";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca a lista de titulos já utilizados
     * @return	array
     */
    public function getListaTituloLayout($seefoid)
    {
    	/*
    	 * Mantis 8119 - Bug na tela de cadastro de Layout de E-mails
    	 */
    	pg_set_client_encoding($this->_adapter, "UNICODE");

    	$sql = "SELECT
    				seetoid, seetdescricao
				FROM
					servico_envio_email_titulo
				WHERE
    				seetseefoid = $seefoid
    			AND
    				seetdt_exclusao IS NULL
				ORDER BY
    				seetdescricao ASC";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca a lista de tipos de propostas
     * @return	array
     */
    public function getListaTipoProposta()
    {
    	$sql = "SELECT
    				tppoid, tppdescricao
				FROM
    				tipo_proposta
				WHERE
    				tppoid_supertipo IS NULL
				ORDER BY
    				tppdescricao ASC";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca a lista de tipos de contrato
     * @return	array
     */
    public function getListaTipoContrato()
    {
    	$sql = "SELECT
    				tpcoid, tpcdescricao
    			FROM
    				tipo_contrato
    			ORDER BY
    				tpcdescricao ASC;";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca a lista de servidores
     * @return	array
     */
    public function getListaServidores()
    {
    	$sql = "SELECT
    				srvoid, srvdescricao
				FROM
					servidor_email
				ORDER BY
    				srvdescricao ASC;";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Busca a lista de subtipo de proposta
     * @return	array
     */
    public function getListaSubtipoProposta($tppoid)
    {
    	$sql = "SELECT
    				tppoid, tppdescricao
    			FROM
    				tipo_proposta
    			WHERE
    				tppoid_supertipo = $tppoid
    			ORDER BY
    				tppdescricao ASC;";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Lista emails cadastrados
     * @param	array	$filters
     * @return 	array
     */
    public function getLayoutEmails($filters)
    {
    	$filters = $this->_escapeArray($filters);

    	$where = array();
    	$where[] = 'servico_envio_email.seedt_exclusao IS NULL';

    	$join = '';

    	// Filtra por envio
    	if (isset($filters['seetipo']) && strlen($filters['seetipo']))
    	{
    		$where[] = "seetipo ilike '" . $filters['seetipo'] . "'";
    	}

    	// Filtra por funcionalidade
    	if (isset($filters['seeseefoid']) && $filters['seeseefoid'] != 0)
    	{
    		$where[] = 'seeseefoid = ' . $filters['seeseefoid'];
    	}

    	// Filtra por título
    	if (isset($filters['seeseetoid']) && $filters['seeseetoid'] != 0)
    	{
    		$where[] = "seeseetoid = " . $filters['seeseetoid'] ;
    	}

    	// Filtra por usuário
    	if (isset($filters['usuario']) && strlen($filters['usuario']))
    	{
    		$where[] = "seeusuoid_cadastro IN (
    						SELECT usuarios.cd_usuario
    						FROM usuarios
    						WHERE
    							usuarios.nm_usuario ILIKE '%" . $filters['usuario'] . "%')";
    	}


    	// Filtra por tipo de proposta
    	if (isset($filters['tipoproposta']) && strlen($filters['tipoproposta']) && ($filters['tipoproposta'] != 'isnull'))
    	{
    		$where[] = "seetppoid = " . $filters['tipoproposta'];
    	} /* else {
    		$where[] = "seetppoid IS NULL ";
    	}

		if ($filters['tipoproposta'] == 'isnull')
    	{
    		$where[] = "seetppoid is null";
    	} */

    	// Filtra por sub tipo de proposta
    	if (isset($filters['subtipoproposta']) && strlen($filters['subtipoproposta']))
    	{
    		$where[] = "seetlconftppoid_sub = " . $filters['subtipoproposta'];
    	}


    	// Filtra por tipo de contrato
    	if (isset($filters['tipocontrato']) && strlen($filters['tipocontrato']) && ($filters['tipocontrato'] != 'isnull') )
    	{
    		$where[] = "seetpcoid = " . $filters['tipocontrato'];
    	}


		if ($filters['tipocontrato'] == 'isnull')
		{
			$where[] = "seetpcoid is null";
		}

    	// Filtra por servidor
    	if (isset($filters['servidor']) && strlen($filters['servidor']))
    	{
    		$where[] = "seesrvoid = " . $filters['servidor'];
    	}

    	// Filtra por objetivo
    	if (isset($filters['seeobjetivo']) && strlen($filters['seeobjetivo']))
    	{
    		$where[] = "seeobjetivo ilike '" . $filters['seeobjetivo'] . "'";
    	}

    	// Filtra por assunto
    	if (isset($filters['seecabecalho']) && strlen($filters['seecabecalho']))
    	{
    		$where[] = "seecabecalho ilike '" . $filters['seecabecalho'] . "'";
    	}

    	// Filtro evita comparação com o próprio registro (editar)
    	if (isset($filters['seeoid_editar']) && strlen($filters['seeoid_editar']))
    	{
    		$where[] = "seeoid != " . $filters['seeoid_editar'];
    	}

		if (isset($filters['padrao']) && ($filters['padrao'] == 'true'))
    	{
    		$where[] = "seepadrao = 't'";
    	}

    	$where = implode(' AND ', $where);

    	$sql = "SELECT
    				servico_envio_email.*,
					usuarios.*,
					tipo_contrato.*,
					tipo.tppoid as id_tipo_proposta,
					tipo.tppdescricao as tipo_proposta,
					subtipo.tppoid as id_subtipo_proposta,
					subtipo.tppdescricao as subtipo_proposta,


					servico_envio_email_titulo.seetdescricao,
					servico_envio_email_funcionalidade.seefdescricao as funcionalidade
    			FROM
    				servico_envio_email
    			$join
    			LEFT JOIN usuarios ON usuarios.cd_usuario = seeusuoid_cadastro
				LEFT JOIN tipo_contrato ON seetpcoid = tpcoid
				LEFT JOIN tipo_proposta tipo ON seetppoid = tipo.tppoid
				LEFT JOIN tipo_proposta subtipo ON seetlconftppoid_sub = subtipo.tppoid
				INNER JOIN servico_envio_email_titulo ON seeseetoid = seetoid
				INNER JOIN servico_envio_email_funcionalidade ON seeseefoid = seefoid



    			WHERE
    				{$where}
    			ORDER BY
    				seecabecalho ASC";

    	return $this->_fetchAll($this->_query($sql));
    }

    /**
     * Deleta um item
     * @param	int		$id
     * @return	boolean
     */
    public function deletarItem($id)
    {
    	$usuarioOid = $_SESSION['usuario']['oid'];

    	// Substitui variáveis na query
    	$sql = "UPDATE servico_envio_email
    			SET
    				seedt_exclusao = NOW(),
    				seeusuoid_exclusao = {$usuarioOid}
    			WHERE
    				seeoid = {$id}";

    	if ($this->_query($sql))
    	{
    		return true;
    	}
    	else
    	{
    		throw new Exception('Houve um erro ao excluir o registro.');
		}
	}

	/**
	 * Busca os dados de um layout por seu ID
	 * @param	int	$id
	 * @return 	array
	 */
	public function getLayoutEmailPorId($id)
	{
		$id = intval($id);

		$sql = "SELECT *,
					   ".$this->_getCondicaoEditavel()."
				FROM servico_envio_email
				WHERE seeoid = {$id}";

		return $this->_fetchAssoc($this->_query($sql));
	}



	/**
	 * Busca os dados de um layout por sua descricao
	 * @param	int		$id
	 * @return 	array
	 */
	public function getLayoutEmailPorDescricao($descricao)
	{
		$descricao = trim($descricao);

		$sql = "SELECT *
				FROM servico_envio_email
				WHERE seepadrao = TRUE
					  and seedt_exclusao IS NULL
					  and seedescricao ilike '%{$descricao}%' limit 1";

		return $this->_fetchAssoc($this->_query($sql));
	}

	/**
	 * Busca os dados de um layout por sua descricao
	 * @param	string	$descFuncionalidade
	 * @return 	array
	 */
	public function getLayoutEmailPorFuncionalidade($descFuncionalidade)
	{
		$descricao = trim($descFuncionalidade);
		$sql = "SELECT distinct s.seeoid, s.seecabecalho FROM servico_envio_email s
				left join servico_envio_email_funcionalidade sf on sf.seefoid = s.seeseefoid
				WHERE s.seedt_exclusao IS NULL
				and seefdescricao ilike '%".pg_escape_string($descricao)."%'
				order by seecabecalho";
		return $this->_fetchAll($this->_query($sql));
	}

	/**
	 * Busca os dados apenas do layout padrao especificado para a funcionalidade.
	 * @param	string	$descFuncionalidade
	 * @return 	array
	 */
	public function getLayoutEmailPadrao($descFuncionalidade)
	{
		$descricao = trim($descFuncionalidade);
		$sql = "SELECT distinct s.seeoid, s.seecabecalho FROM servico_envio_email s
		left join servico_envio_email_funcionalidade sf on sf.seefoid = s.seeseefoid
		WHERE s.seedt_exclusao IS NULL and s.seepadrao = 't'
		and seefdescricao ilike '%".pg_escape_string($descricao)."%'
		order by seecabecalho limit 1";
		return $this->_fetchAssoc($this->_query($sql));
	}

	/**
	 * Valida o preenchimento dos campos obrigatórios
	 * @param	array	$arr
	 * @throws  Exception
	 */
	protected function _validateCamposLayout($arr)
	{
	$msg = "";


	if ($arr['seetipo'] =='E'){
		if (!isset($arr['seecabecalho']) || !strlen($arr['seecabecalho'])
			|| !isset($arr['seecorpo']) 	|| !strlen($arr['seecorpo'])
			|| !isset($arr['seeseefoid']) 	|| $arr['seeseefoid'] <= 0
			|| !isset($arr['seeseetoid']) 	|| $arr['seeseetoid'] <= 0
			|| !isset($arr['seesrvoid']) 	|| $arr['seesrvoid'] <= 0
			|| !isset($arr['seeobjetivo']) 	|| !strlen($arr['seeobjetivo'])
			|| !isset($arr['seepadrao']) 	|| !in_array($arr['seepadrao'], array('f', 't', 1, 0)))
		{
			$msg = 'Existem campos obrigatórios não preenchidos. ';
		}

		// Validação remetente
		$validaEmail = "/^[\w-]+(\.[\w-]+)*@(([\w-]{2,63}\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/";
		$email = $arr['seeremetente'];

		if (!preg_match($validaEmail, $email) && strlen($email) > 0) {
			$msg .= "Endereço do remetente está incorreto.";
		}




		// Se tem mensagem de erro, retorna o erro
		if ($msg != ""){
			throw new Exception($msg);
		}

	if ($arr['seetipo'] =='S'){

		if ( !isset($arr['seecorpo']) 	|| !strlen($arr['seecorpo'])
			|| !isset($arr['seeseefoid']) 	|| $arr['seeseefoid'] <= 0
			|| !isset($arr['seeseetoid']) 	|| $arr['seeseetoid'] <= 0
			|| !isset($arr['seeobjetivo']) 	|| !strlen($arr['seeobjetivo'])
			|| !isset($arr['seepadrao']) 	|| !in_array($arr['seepadrao'], array('f', 't', 1, 0)))
			{
			$msg = 'Existem campos obrigatórios não preenchidos. ';
		}


		// Se tem mensagem de erro, retorna o erro
		if ($msg != ""){
			throw new Exception($msg);
		}


	}


}
}

/**
 * Valida a existencia de um e-mail padrão para funcionalidade / titulo
 * @param	int	seeseefoid
 * @param	int	seeseetoid
 * @return  int seeoid
 */
public function verificaExistePadrao($filtro, $tipo = null, $id = null)
{

		$sql = "SELECT
					seeoid as padrao
				FROM
					servico_envio_email
				WHERE
					seepadrao = 't'
				AND
					seedt_exclusao IS NULL
				AND
					seeseefoid = {$filtro['seeseefoid']}
				AND
					seeseetoid = {$filtro['seeseetoid']}

							$tipoLayout ";

							$rs =  $this->_fetchAssoc($this->_query($sql));

							if ($rs['padrao'] )
							{
							return $rs['padrao'];
			}
			else
				{
				return false;
				}

			}

	/**
	 * Valida a existencia de um e-mail padrão para funcionalidade / titulo
	 * @param	int	seeseefoid
	 * @param	int	seeseetoid
	 * @return  int seeoid
	 */
	public function verificaExistePadraoSMS($filtro, $tipo = null) {

		$sql = "SELECT
					seeoid as padrao
				FROM
					servico_envio_email
				WHERE
					seepadrao = 't'
				AND
					seedt_exclusao IS NULL ";

			if (!empty($filtro['seeseefoid'])) {
			$sql .="
				AND
					seeseefoid = " . intval($filtro['seeseefoid']) . "
				AND
					seeseetoid = " . intval($filtro['seeseetoid']);
			}

        if(isset($filtro['seetppoid']) && is_int($filtro['seetppoid'])) {
            $sql .= " AND
                          seetppoid = " . intval($filtro['seetppoid']);
			}

        if(isset($filtro['seetlconftppoid_sub']) && is_int($filtro['seetlconftppoid_sub'])) {
            $sql .= " AND
					      seetlconftppoid_sub = " . intval($filtro['seetlconftppoid_sub']);
			}

        if(isset($filtro['seetpcoid']) && is_int($filtro['seetpcoid'])){
            $sql .= " AND
					      seetpcoid = " . intval($filtro['seetpcoid']);
			}

        if (!empty($tipo)) {
			$sql .= " AND
                          seetipo = '" . pg_escape_string($tipo) . "'";
			}

		$rs =  $this->_fetchAssoc($this->_query($sql));

		if ($rs['padrao'] )
		{
			return $rs['padrao'];
		}
		else
		{
			return false;
		}

	}

	/**
	 * Atualiza o padrão da funcionalidade
	 * @param 	int	$seeseefoid
	 * @return 	resource
	 */
	public function atualizaPadraoFuncionalide($seeseefoid, $seeseetoid)
	{
		$sql = "UPDATE servico_envio_email
				SET
					seepadrao = 'f'
				WHERE
					seeseefoid = {$seeseefoid}
				AND
					seeseetoid = {$seeseetoid} ";

		return $this->_query($sql);
	}

	/**
	 * Atualiza os dados de um layout por seu ID
	 * @param	int		$id
	 * @param	array	$arr
	 * @return  boolean
	 */
	public function atualizaLayoutEmailPorId($arr)
	{
		$arr = $this->_escapeArray($arr);

		$this->_validateCamposLayout($arr);



		// Verifica se a combinação de paramentros já existe
		if( isset($arr['seetipo']) ){










			//se for e-mail
			if($arr['seetipo'] =='E'){
				$filtro['seetipo']           = $arr['seetipo'];
				$filtro['seeseefoid']		 = $arr['seeseefoid'];
				$filtro['seeseetoid']		 = $arr['seeseetoid'];
				$filtro['seeobjetivo'] 		 = $arr['seeobjetivo'];
				$filtro['seecabecalho'] 	 = $arr['seecabecalho'];
				$filtro['tipoproposta']		 = $arr['seetppoid'];
				$filtro['subtipoproposta']	 = $arr['seetlconftppoid_sub'];
				$filtro['tipocontrato'] 	 = $arr['seetpcoid'];
				$filtro['servidor']			 = $arr['seesrvoid'];
				$filtro['remetente']		 = $arr['seeremetente'];
			}

            //se for sms
            if($arr['seetipo'] =='S'){
                $filtro['seetipo']       	 = $arr['seetipo'];
                $filtro['seeseefoid']		 = $arr['seeseefoid'];
                $filtro['seeseetoid']		 = $arr['seeseetoid'];
                $filtro['tipoproposta']		 = $arr['seetppoid'];
                $filtro['subtipoproposta']	 = $arr['seetlconftppoid_sub'];
                $filtro['tipoproposta']		 = $arr['seetppoid'];
            }



            if ( $this->getLayoutEmails($filtro) ){
                if($arr['seetipo'] =='S'){


                    if ($arr['seepadrao'] == 't') {


                        $arrPadraoSms['seeseefoid'] = $arr['seeseefoid'];
                        $arrPadraoSms['seeseetoid'] = $arr['seeseetoid'];
                        $arrPadraoSms['seetppoid'] = $arr['seetppoid'];
                        $arrPadraoSms['seetlconftppoid_sub'] = $arr['seetlconftppoid_sub'];
                        $arrPadraoSms['seetpcoid'] = $arr['seetpcoid'];

                        $existePadrao = $this->verificaExistePadraoSMS($arrPadraoSms, 'S');



                        if($existePadrao && $existePadrao != $arr['seeoid']) {
                            throw new Exception('Já existe um layout com o mesmo título/funcionalidade.');
                        }
                    }




                }

            }

            if($arr['seetipo'] =='E'){

                // Atualiza o padrão da funcionalidade

                if (in_array($arr['seepadrao'], array('t', 1))) {
                    $this->atualizaPadraoFuncionalide($arr['seeseefoid'],$arr['seeseetoid']);
                } else {
                    $arrPadrao['seeseefoid'] = $arr['seeseefoid'];



                    $arrPadrao['seeseetoid'] = $arr['seeseetoid'];
                    $existePadrao = $this->verificaExistePadrao($arrPadrao, 'E');



                    if($existePadrao == false) {
                        $arr['seepadrao'] = 't';

                    } elseif ($arr['seeoid'] == $existePadrao) {
                        throw new Exception('Este layout é definido como padrão. Se deseja alterar, favor configurar outro layout como padrão.');
                    }

                }
            }
        }



		 $sql = "UPDATE servico_envio_email
				SET
					  seecabecalho    		= '{$arr['seecabecalho']}'
					, seecorpo       		= '{$arr['seecorpo']}'
					, seeseefoid     		= {$arr['seeseefoid']}
					, seeseetoid      		= {$arr['seeseetoid']}
					, seepadrao       		= '{$arr['seepadrao']}'
					, seeimagem       		= '{$arr['seeimagem']}'
					, seeimagem_anexo 		= '{$arr['seeimagem_anexo']}'
					, seeobjetivo	  		= '{$arr['seeobjetivo']}'
					, seetppoid       		= {$arr['seetppoid']}
					, seetlconftppoid_sub   = {$arr['seetlconftppoid_sub']}
					, seetpcoid 	  		= {$arr['seetpcoid']}
					, seesrvoid	 	  		= {$arr['seesrvoid']}
					, seeremetente	 	  	= '{$arr['seeremetente']}'
					, seetipo	 	  		= '{$arr['seetipo']}'
				WHERE
					seeoid = {$arr['seeoid']}";


		return $this->_query($sql);
	}

	/**
	 * Insere um novo layout
	 * @param	array	$arr
	 * @return  boolean
	 */
	public function insereLayoutEmailPorId($arr)
	{
		$arr = $this->_escapeArray($arr);

		$this->_validateCamposLayout($arr);


		// Verifica se a combinação de paramentros já existe
		if( isset($arr['seetipo']) ){

			//se for e-mail
			if($arr['seetipo'] =='E'){
				$filtro['seetipo']        = $arr['seetipo'];
				$filtro['seeseefoid']		 = $arr['seeseefoid'];
				$filtro['seeseetoid']		 = $arr['seeseetoid'];
				$filtro['seeobjetivo'] 		 = $arr['seeobjetivo'];
				$filtro['seecabecalho'] 	 = $arr['seecabecalho'];
				$filtro['tipoproposta']		 = $arr['seetppoid'];
				$filtro['subtipoproposta']	 = $arr['seetlconftppoid_sub'];
				$filtro['tipocontrato'] 	 = $arr['seetpcoid'];
				$filtro['servidor']			 = $arr['seesrvoid'];
				$filtro['remetente']		 = $arr['seeremetente'];
			}

			//se for sms
			if($arr['seetipo'] =='S'){
				$filtro['seetipo']       	 = $arr['seetipo'];
				$filtro['seeseefoid']		 = $arr['seeseefoid'];
				$filtro['seeseetoid']		 = $arr['seeseetoid'];
				$filtro['tipoproposta']		 = $arr['seetppoid'];
				$filtro['subtipoproposta']	 = $arr['seetlconftppoid_sub'];
				$filtro['tipoproposta']		 = $arr['seetppoid'];


			//	$filtro['tipoproposta']	 	<= 0	 	 && 	$arr['seetppoid'] <= 0;
			//	$filtro['subtipoproposta'] 	<= 0		 && 	$arr['seetlconftppoid_sub'] <= 0;
			//	$filtro['tipoproposta']	 	<= 0		 && 	$arr['seetppoid'] <= 0;
			 }

		if ( $this->getLayoutEmails($filtro) ){
			if($arr['seetipo'] =='S'){

				if ($arr['seepadrao'] == 't') {

					$arrPadraoSms['seeseefoid'] = $arr['seeseefoid'];
					$arrPadraoSms['seeseetoid'] = $arr['seeseetoid'];
					$arrPadraoSms['seetppoid'] = $arr['seetppoid'];
					$arrPadraoSms['seetlconftppoid_sub'] = $arr['seetlconftppoid_sub'];
					$arrPadraoSms['seetpcoid'] = $arr['seetpcoid'];



					$existePadraoSms = $this->verificaExistePadraoSMS($arrPadraoSms, 'S');


					if($existePadraoSms) {
						throw new Exception('Já existe um layout com o mesmo título/funcionalidade.');
					}
				}
			}

			if($arr['seetipo'] =='E'){

		// Atualiza o padrão da funcionalidade
		if (in_array($arr['seepadrao'], array('t', 1)))
		{
			$this->atualizaPadraoFuncionalide($arr['seeseefoid'],$arr['seeseetoid']);
		}
		}

		// Atualiza o padrão da funcionalidade
		if (in_array($arr['seepadrao'], array('t', 1)))
		{
			$this->atualizaPadraoFuncionalide($arr['seeseefoid'],$arr['seeseetoid']);
		}
		else
		{
			$arrPadrao['seeseefoid'] = $arr['seeseefoid'];
			$arrPadrao['seeseetoid'] = $arr['seeseetoid'];
			$existePadrao = $this->verificaExistePadrao($arrPadrao);

			if($existePadrao == false){
				$arr['seepadrao'] = 't';
			}

		}
			}
		}

	$sql = "INSERT INTO servico_envio_email	(
					  seeseetoid
					, seecabecalho
					, seecorpo
					, seedt_cadastro
					, seeusuoid_cadastro
					, seeseefoid
					, seeobjetivo
					, seepadrao
					, seeimagem
					, seeimagem_anexo
					, seetpcoid
					, seesrvoid
					, seetppoid
					,seetlconftppoid_sub
					, seeremetente
					, seetipo
				) VALUES (
					  '{$arr['seeseetoid']}'
					, '{$arr['seecabecalho']}'
					, '{$arr['seecorpo']}'
					, NOW()
					, {$arr['seeusuoid_cadastro']}
					, {$arr['seeseefoid']}
					, '{$arr['seeobjetivo']}'
					, '{$arr['seepadrao']}'
					, '{$arr['seeimagem']}'
					, '{$arr['seeimagem_anexo']}'
					, {$arr['seetpcoid']}
					, {$arr['seesrvoid']}
					, {$arr['seetppoid']}
					, {$arr['seetlconftppoid_sub']}
					, '{$arr['seeremetente']}'
					, '{$arr['seetipo']}'
				)";

		return $this->_query($sql);
	}

	/**
	 * Busca dados Tipo Proposta e Contrato por numero da ocorrência
	 * @param int $ocorrencia
	 * @return array
	 */
	public function getTipoIdByOcorrencia($ocorrencia)
	{
	 	$sql = "select
					prptppoid, prptpcoid,
					case
						when tppoid_supertipo is not null then tppoid_supertipo
						else null
					end supertipo
				from ocorrencia
					inner join proposta on (prptermo = ococonnumero)
					inner join tipo_proposta on (prptppoid = tppoid)
				where ocooid = $ocorrencia";

		$query = $this->_query($sql);
    	$retorno = $this->_fetchAll($query);

		return $retorno[0];
    }

	public function getTipoIdseetoid($like)
	{
		$sql = "select seetoid from servico_envio_email_titulo where seetdescricao like '$like'";

		$query = $this->_query($sql);
		$retorno = $this->_fetchAssoc($query);

		return $retorno['seetoid'];
	}

	public function getTipoIdseefoid($like)
	{
		$sql = "select seefoid from servico_envio_email_funcionalidade where seefdescricao like '$like'";

		$query = $this->_query($sql);
		$retorno = $this->_fetchAssoc($query);

		return $retorno['seefoid'];
	}
	/**
	 * Busca o codigo do titulo e da funcionalidade de acordo com o nome do titulo passado
	 */

	public function getTituloFuncionalidade($titulo){

		$sql = "
		    	SELECT
		    		seetoid AS titulo_id, seetseefoid AS funcionalidade_id
		    	FROM
		    		servico_envio_email_titulo
		    	WHERE
		    		seetdescricao ILIKE '".trim($titulo)."';
    			";

		$query = $this->_query($sql);
		return $retorno = $this->_fetchAll($query);
	}
}
