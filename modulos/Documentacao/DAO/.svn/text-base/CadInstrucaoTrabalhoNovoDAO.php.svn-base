<?php

class CadInstrucaoTrabalhoNovoDAO {

    private $conn;

    public function __construct() {
        global $conn;

        $this->conn = $conn;
    }

    // Carrega departamentos
    public function departamentosSelect() {

        $sql = "SELECT 
					depoid,
                    depdescricao 
				FROM 
					departamento 
				WHERE 
					depexclusao IS NULL 
				ORDER BY depdescricao";

        $result = pg_query($this->conn, $sql);

        return $result;
    }
	
	// Carrega Área
	public function getArea($usuario = false, $itoid = '', $prhoid = false){
		
		$sql = "SELECT 
					depoid, depdescricao 
				FROM departamento
					LEFT JOIN usuarios ON (depoid = usudepoid)
				WHERE depexclusao is null ";

		if($usuario){
			$sql .=	"AND cd_usuario = $usuario ";
		}

		if($itoid != ''){
			$sql = "SELECT 
						depoid, depdescricao  
					FROM instr_trabalho
						INNER JOIN departamento ON (depoid = itdepoid)
						INNER JOIN instr_trabalho_acesso ON (itaitoid = itoid)
					WHERE itoid = $itoid 
						AND itaprhoid = $prhoid ";
		}

		$sql.= "GROUP BY depoid, depdescricao 
				ORDER BY depdescricao";

		$query = pg_query($this->conn, $sql);
        $result = pg_fetch_all($query);

        return $result;
	}

    // Carrega Segmentos
    public function segmentosSelect() {

        $sql = "SELECT 
					itseoid,
                    itsedescricao 
				FROM 
					instr_trabalho_segmento 
				WHERE 
					itsedt_exclusao IS NULL
               ";
       if (($this->parametros->itseoid) != "") {
            $sql .= " AND itseoid = " . intval($this->parametros->itseoid) . "";
        }

        $sql .= " ORDER BY itsedescricao ASC";

        $result = pg_query($this->conn, $sql);

        return $result;
       
    }
        // Carrega documentos da pesquisa
    public function getDocumentos($addquery, $cd_usuario, $orderBy, $ASC) {

        $sql = "SELECT     
					i.itoid AS it_id, 
					itstatus,
					itidentificacao,
					itdepoid,
                    itdescricao,
                    itdt_aprovacao as dt_aprovacao,
                    to_char(itdt_aprovacao, 'dd/mm/yyyy') AS itdt_aprovacao,
					CASE WHEN itstatus in ('A','1') THEN 
						'ap15.jpg' 
					WHEN itstatus in ('P','3') THEN 
						'ap03.jpg' 
					WHEN itstatus in ('I','4') THEN 
						'ap02.jpg' 
					WHEN itstatus in ('E','13') THEN 
						'ap04.jpg' 
					ELSE 
						itstatus 
					END AS status,
					itusuoid_incl,
					itversao,
					itdepoid,
                    itelaborado,
					itaprovado,
					itexclusao,
					ittipo,
					itdt_elaboracao as dt_elaboracao,
					to_char(itdt_elaboracao, 'dd/mm/yyyy') AS itdt_elaboracao,
					(   SELECT ds_login 
						FROM usuarios 
						WHERE cd_usuario = itaprovado
						LIMIT 1) AS aprovador,
					CASE ittipo WHEN 'IT' THEN 
						'Instru&ccedil;&atilde;o de Trabalho'
					WHEN 'PG' THEN 
						'Procedimento Gerencial'
					WHEN 'PS' THEN 
						'Procedimento Sist&ecirc;mico'
					WHEN 'MA' THEN 
						'Manual'
					WHEN 'TA' THEN 
						'Tabela'
					WHEN 'ES' THEN 
						'Esquemático'
					WHEN 'SC' THEN 
						'Script'
					WHEN 'RC' THEN 
						'Riscos e Compliance'		
					ELSE 
						ittipo
					END AS instr_tipo,
					depdescricao as departamento
				FROM instr_trabalho i 
					inner join departamento t on (itdepoid = depoid)
                    left join instr_trabalho_segmento on (itsegmento = itseoid)
				WHERE (itoid in (   SELECT itsitoid 
										FROM instr_trab_sigilo_usu 
										WHERE itsusuoid= " . intval($cd_usuario) . "
										and itsitoid=itoid
										LIMIT 1) or not exists (SELECT * 
																FROM instr_trab_sigilo_usu 
																WHERE itsitoid=itoid
																LIMIT 1) )
				$addquery";

        $sql .= " ORDER BY ";
        $sql .= $orderBy;
        $sql .= $ASC . ', itdescricao ASC';

        $result = pg_query($this->conn, $sql);
        return $result;
    }

    // Carrega permissões dos cargos por documento
    public function getPermissaoCargos($itoid, $cargo){

        $sql = "select itaprhoid, itatipo_acesso from instr_trabalho_acesso 
				where itaitoid = $itoid and 
					  itaprhoid = $cargo ";

        /*if ($tipo == 'E') {
            $sql .= "and itatipo_acesso = '$tipo'";
        }
 */
        $query = pg_query($this->conn, $sql);
        $result = pg_fetch_all($query);

        return $result;
    }

    // Carrega dados do documento
    public function getDadosDocumento($itoid) {

        $sql = "select * from instr_trabalho where itoid = $itoid";

        $query = pg_query($this->conn, $sql);
        $result = pg_fetch_all($query);

        return $result[0];
    }
	
	// Confere se documento 
	public function getDocRestrito($itoid){

		$sql = "select itrestrito from instr_trabalho where itoid = $itoid";
		
		$query = pg_query($this->conn, $sql);
        $result = pg_fetch_all($query);

        return $result[0]['itrestrito'];
	}

    // Confere se usuário é aprovador
    public function getAprovador($ID) {

        $sql = "select funcao_permissao_cargo.*
				from usuarios  
					INNER JOIN  funcionario ON (funoid = usufunoid)
					INNER JOIN  funcao_permissao_depto ON (funcao_permissao_depto.fpddepoid = funcionario.fundepto)
					INNER JOIN funcao ON (funcao_permissao_depto.fpdfuncoid = funcao.funcoid AND 
										  funcao_permissao_depto.fpddepoid = funcionario.fundepto)
					INNER JOIN funcao_permissao_cargo ON (funcionario.funcargo = funcao_permissao_cargo.fpccargooid AND
														  funcao_permissao_cargo.fpcfuncoid = funcao. funcoid)
					INNER JOIN funcao_permissao_historico AS hist ON (hist.fphfuncoid = funcao_permissao_depto.fpdfuncoid AND 
																	  hist.fphdepoid = funcao_permissao_depto.fpddepoid) 
					LEFT JOIN ramal ON (ramal.ramfunoid = funcionario.funoid)
				WHERE usuarios.dt_exclusao IS NULL
					AND cd_usuario = " . $ID . "
					AND funcoid = 431
				LIMIT 1";

        $query = pg_query($this->conn, $sql);
        $result = pg_num_rows($query);

        // Artifício Técnico para buscar registros errados sem histórico na base
        if ($result == 0) {

            $sql = "select funcao_permissao_cargo.*
				from usuarios  
					INNER JOIN  funcionario ON (funoid = usufunoid)
					INNER JOIN  funcao_permissao_depto ON (funcao_permissao_depto.fpddepoid = funcionario.fundepto)
					INNER JOIN funcao ON (funcao_permissao_depto.fpdfuncoid = funcao.funcoid AND 
										  funcao_permissao_depto.fpddepoid = funcionario.fundepto)
					INNER JOIN funcao_permissao_cargo ON (funcionario.funcargo = funcao_permissao_cargo.fpccargooid AND
														  funcao_permissao_cargo.fpcfuncoid = funcao. funcoid)
				WHERE usuarios.dt_exclusao IS NULL
					AND cd_usuario = " . $ID . "
					AND funcoid = 431
				LIMIT 1";

            $query = pg_query($this->conn, $sql);
            $result = pg_num_rows($query);
        }

        return $result;
    }

    public function setNovaVersao($itoid, $cd_usuario) {

        /* JOGA OS DADOS EM UMA TEMPORARIA PARA GERAR A NOVA VERSAO */
        $sql = "SELECT 
					*  
				INTO TEMP 
					instr_trabalho_temp 
				FROM 
					instr_trabalho 
				WHERE 
					itoid = $itoid";

        $res = pg_query($this->conn, $sql);

        if (!$res) {
            return false;
        }

        /* INSERT PARA GERAR A NOVA VERSAO */
        $sql = "INSERT INTO instr_trabalho 
				(
                    itidentificacao,itdescricao,itdepoid,itelaborado,itdt_elaboracao,itaprovado,
                    itdt_aprovacao,itrecursos,itobjetivo,itusuarios,itdoc_ref,itmetodologia,
                    ititem_verificacao,itacao,itregistro,itstatus,itexclusao,ittipo,itresponsabilidade,
                    itanexos,itusuoid_incl,itoid_original,itversao, itsegmento, itrestrito
                ) values (
                    (SELECT itidentificacao FROM instr_trabalho_temp),
                    (SELECT itdescricao FROM instr_trabalho_temp),
                    (SELECT itdepoid FROM instr_trabalho_temp),
                    (SELECT itelaborado FROM instr_trabalho_temp),
                    now(),
                    null,
                    null,
                    (SELECT itrecursos FROM instr_trabalho_temp),
                    (SELECT itobjetivo FROM instr_trabalho_temp),
                    (SELECT itusuarios FROM instr_trabalho_temp),
                    (SELECT itdoc_ref FROM instr_trabalho_temp),
                    (SELECT itmetodologia FROM instr_trabalho_temp),
                    (SELECT ititem_verificacao FROM instr_trabalho_temp),
                    (SELECT itacao FROM instr_trabalho_temp),
                    (SELECT itregistro FROM instr_trabalho_temp),
                    ('P'),
                    NULL,
                    (SELECT ittipo FROM instr_trabalho_temp),
                    (SELECT itresponsabilidade FROM instr_trabalho_temp),
                    (SELECT itanexos FROM instr_trabalho_temp),
                    $cd_usuario,
                    $itoid,
                    (SELECT itversao FROM instr_trabalho_temp)+1,
                    (SELECT itsegmento FROM instr_trabalho_temp),
					(SELECT itrestrito FROM instr_trabalho_temp)
                ) RETURNING itoid, itversao ;";

        $res = pg_query($this->conn, $sql);

        if (!$res) {
            return false;
        }

        return $res;
    }

    public function inativaDocumento($itoid) {

        $sql = "UPDATE instr_trabalho SET 
					itstatus = 'I'
				WHERE 
					itoid = $itoid ";

        if (!pg_query($this->conn, $sql)) {
            return false;
        }

        return true;
    }

    public function deletarDocumento($itoid) {

        $sql = "UPDATE 
                    instr_trabalho 
                SET 
                    itexclusao = NOW()
                WHERE 
                    itoid = $itoid ";

        if (!pg_query($this->conn, $sql)) {
            return false;
        }

        return true;
    }

    public function inativaAnexo($itaoid, $cd_usuario) {

        $sql = "update instr_trabalho_anexo set 
					itainativacao = now(), 
					itausuoid_inativacao = $cd_usuario
				where itaoid = $itaoid";

        if (!pg_query($this->conn, $sql)) {
            return false;
        }

        return true;
    }

    public function excluiAnexo($itaoid, $cd_usuario) {

        $sql = "UPDATE 
                    instr_trabalho_anexo 
                SET
                    itaexclusao = NOW(), 
                    itausuoid_exclusao = $cd_usuario
                WHERE 
                    itaoid = $itaoid";

        if (!pg_query($this->conn, $sql)) {
            return false;
        }

        return true;
    }

    public function setAnexoVersao($itoid, $itoidNOVO, $cd_usuario) {
        /* BUSCA OS ANEXOS DO DOCUMENTO ATUAL PARA DUPLICAR PARA A NOVA VERSAO */
        $anexoDoc = $this->getAnexosDocumento($itoid);

        // Se existir faz insert dos anexos
        if (pg_num_rows($anexoDoc) > 0) {
            while ($fetch = pg_fetch_array($anexoDoc)) {
                $insert = "INSERT INTO instr_trabalho_anexo
							( itaitoid, itaarquivo, itadescricao, itacadastro, itausuoid, itaoid_doc) 
						   VALUES 
							( $itoidNOVO, '" . $fetch['itaarquivo'] . "', '" . $fetch['itadescricao'] . "', NOW(), $cd_usuario, $itoid);";

                if (!pg_query($this->conn, $insert)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function permissaoVersao($itoid, $itaitoidNOVO) {

        // Busca Permissões do documento atual para nova versão
        $sql = "select * from instr_trabalho_acesso where itaitoid = $itoid";
        $query = pg_query($this->conn, $sql);

        // Se existir faz insert das permissões
        if (pg_num_rows($query) > 0) {
            while ($fetch = pg_fetch_array($query)) {
                $insert = "INSERT INTO instr_trabalho_acesso
							(itaitoid, itaprhoid, itatipo_acesso) 
						   VALUES 
							(" . $itaitoidNOVO . ", " . $fetch['itaprhoid'] . ", '" . $fetch['itatipo_acesso'] . "');";

                if (!pg_query($this->conn, $insert)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function delAcesso($itoid) {

        $sqlAcesso = "select * from instr_trabalho_acesso WHERE itaitoid = $itoid";
        $queryAcesso = pg_query($this->conn, $sqlAcesso);
        $fetchAcesso = pg_fetch_all($queryAcesso);

        if ($fetchAcesso) {
            $sqlDel = "DELETE FROM instr_trabalho_acesso WHERE itaitoid = " . $itoid;
            pg_query($this->conn, $sqlDel);
        }
    }

    public function getVersoes($itoid) {
        $sql = "select 
					itoid, itoid_original, itversao, itusuoid_incl
				from instr_trabalho 
				where itoid_original = $itoid
					and itstatus in ('A')
					and itexclusao is null";

        $query = pg_query($this->conn, $sql);
        $retorno = pg_fetch_all($query);

        return $retorno[0];
    }

    public function getNomeAnexo($itaoid) {
        $sql = "SELECT itaarquivo FROM instr_trabalho_anexo WHERE itaexclusao IS NULL AND itaoid = $itaoid";

        $query = pg_query($this->conn, $sql);
        $retorno = pg_fetch_all($query);

        return $retorno[0]['itaarquivo'];
    }

    public function getDadosAnexo($itoid, $itaoid) {

        $sql = "select * from instr_trabalho_anexo 
				where itaexclusao is null
					and itaitoid = $itoid
					and itaoid = $itaoid";

        $query = pg_query($this->conn, $sql);
        $result = pg_fetch_all($query);

        return $result[0];
    }

    public function getAnexosDocumento($itoid) {

        $sql = "SELECT * FROM instr_trabalho_anexo WHERE itainativacao is null and itaitoid = $itoid";

        $query = pg_query($this->conn, $sql);

        return $query;
    }

    public function setDocumentoAnexo($dados, $cd_usuario) {

        $insert = "INSERT INTO instr_trabalho(
						ittipo,
						itstatus,
						itrecursos,
						itobjetivo,
						itusuarios,
						itdoc_ref,
						itresponsabilidade,
						itmetodologia,
						ititem_verificacao,
						itacao,
						ithistorico_revisao,
						itregistro,
						itidentificacao,
						itdescricao,						
						itelaborado,
						itdt_elaboracao, 
						itdepoid,
						itversao,
						itusuoid_incl,
						itrestrito
                ) VALUES (
                    '" . $dados['ittipo'] . "',
					'P',
					'" . addslashes($dados["itrecursos"]) . "',
					'" . addslashes($dados["itobjetivo"]) . "',
					'" . addslashes($dados["itusuarios"]) . "',
					'" . addslashes($dados["itdoc_ref"]) . "',
					'" . addslashes($dados["itresponsabilidade"]) . "',
					'" . addslashes($dados["itmetodologia"]) . "',
					'" . addslashes($dados["ititem_verificacao"]) . "',
					'" . addslashes($dados["itacao"]) . "',
					'" . addslashes($dados["ithistorico_revisao"]) . "',
					'" . addslashes($dados["itregistro"]) . "',
                    '" . $dados['itidentificacao'] . "',
                    '" . $dados['itdescricao'] . "',
                    '" . $dados['itelaborado'] . "',
                    '" . $dados['itdt_elaboracao'] . "', 
                    '" . $dados['itdepoid'] . "',
                    1,
                    $cd_usuario,
                    " . $dados['itrestrito'] . "
    			)";

				
        if (!pg_query($this->conn, $insert)) {
            return false;
        }

        $sql = "select itoid from instr_trabalho order by itoid desc limit 1";
        $query = pg_query($this->conn, $sql);

        $itoid = pg_fetch_all($query);

        return $itoid[0]['itoid'];
    }

}