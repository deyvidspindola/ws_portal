<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
/**
 * Módulo Principal
 *
 * SASCAR (http://www.sascar.com.br/)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	View de Abas do sistema
 * @version 10/03/2008 [0.0.1]
 * @package SASCAR Intranet
 */
/*
$_SESSION['funcao']['sti_edicao'] = 1;				// provisório
$_SESSION['funcao']['sti_alteracao'] = 1;			// provisório
$_SESSION['funcao']['sti_aba_fluxo'] = 1;			// provisório
$_SESSION['funcao']['sti_aba_fase'] = 1;			// provisório
$_SESSION['funcao']['sti_aba_funcao'] = 1;			// provisório
$_SESSION['funcao']['sti_aba_acompanhamento'] = 1;	// provisório
$_SESSION['funcao']['sti_planejamento_fase'] = 1;	// provisório
*/
// Ativação de aba
$aba_ativa = (int) $_SESSION['aba_ativa'];
$aba_ativa = ($aba_ativa > 0) ? $aba_ativa : 1;
?>
<tr>
  <td align="center"><table width="98%">
      <tr>
        <td align="left" id="navPrincipal"><table>
            <tr>
              <td align="center" id="tabnav"><a href="javascript:void(null);" onclick="javascript:abreAbas('pesquisar');"
					<?php if($aba_ativa == 1){ echo 'class="active"'; }?>>Principal </a></td>
              <?php if($_SESSION['funcao']['sti_aba_fluxo']==1){ ?>
              <td align="center" id="tabnav"><a href="javascript:void(null);" onclick="javascript:abreAbas('gerenciar-fluxos');"
					<?php if($aba_ativa == 2){ echo 'class="active"'; } ?>>Fluxos</a></td>
              <?php } if($_SESSION['funcao']['sti_aba_fase']==1){ ?>
              <td align="center" id="tabnav"><a href="javascript:void(null);" onclick="javascript:abreAbas('gerenciar-fases');"
					<?php if($aba_ativa == 3){ echo 'class="active"'; }?>>Fases</a></td>
              <?php } if($_SESSION['funcao']['sti_aba_funcao']==1){ ?>
              <td align="center" id="tabnav"><a href="javascript:void(null);" onclick="javascript:abreAbas('gerenciar-funcoes');"
					<?php if($aba_ativa == 4){ echo 'class="active"'; }?>>Funções</a></td>
              <?php } if($_SESSION['funcao']['sti_aba_acompanhamento']==99){ ?>
              <td align="center" id="tabnav"><a href="javascript:void(null);" onclick="javascript:abreAbas('acompanhamento');"
					<?php if($aba_ativa == 5){ echo 'class="active"'; }?>>Acompanhamento</a></td>
              <?php } ?>
            </tr>
          </table></td>
      </tr>
    </table></td>
</tr>
<?php unset($_SESSION['aba_ativa']); ?>