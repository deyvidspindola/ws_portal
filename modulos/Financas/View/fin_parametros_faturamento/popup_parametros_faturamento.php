<?php

session_start();
/**
 * @file popup_parametros_faturamento.php
 * @author marcioferreira
 * @version 25/08/2014 14:38:18
 * @since 25/08/2014 14:38:18
 * @package SASCAR popup_parametros_faturamento.php
 */

$acao = $_POST['acao'];
$tipo = $_SESSION['tipo'] = $_POST['tipo'];

if($acao == 'insere_sessao'){

	if($tipo == 'obriga'){
		
		$_SESSION['dados_obr']     = $_POST['dados'];
		
	}else{
		unset($_SESSION['dados_obr']);
	}
	
	if($tipo == 'observa'){
		
		$_SESSION['dados_observa'] = $_POST['dados'];
		
	}else{
		unset($_SESSION['dados_observa']);
	}

	print(1);
	exit;
}


$desc_obr = explode(',', $_SESSION['dados_obr'] );

$obervacao = strip_tags(htmlentities($_SESSION['dados_observa'], ENT_QUOTES, "UTF-8"));

if($desc_obr[0] != ''){
		
	$title = 'Obrigação Financeira';
	$titulo = 'Descrição';
	
}elseif($obervacao != ''){
	
	$title = 'Observação';
	$titulo = 'Observação do parâmentro';

}else{
	
	$title = '';
	$titulo = '';
}


?>
		
		
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title><?php echo $title ;?></title>
		<style type="text/css">
           .titulo{
	           text-align: left;
	           font-size: 11px;
	           font-family: Verdana,Arial,Helvetica,sans-serif;
	           font-weight: bold;
	           background: #bad0e5;
	           padding: 10px;
           }
           
           .conteudo_obr_{
           	   text-align: left;
	           font-size: 11px;
	           font-family: Verdana,Arial,Helvetica,sans-serif;
	   		}  
			         
           .par{
           	   background: #dee6f6;
            }
           
           table{
           	  border-collapse: collapse;
              border: solid 1px #CCC;
           	  width: 100%;
           }
			
		</style>  
	</head>

	<body> 
	  <table >
	    <thead>
	      <tr><th class="titulo"><?php echo $titulo; ?></th></tr>  	
	    </thead>
	  	
	     <tbody>
	  	  <?php if(count($desc_obr) > 0):
			  	foreach ($desc_obr as $key=> $descObrigacao):?>
			  		<tr class="conteudo_obr_ <?php echo $key%2==0 ?  "" : "par"; ?>" >
			  		   <td valign="middle"><div style="padding-bottom: 1px;"><?php echo utf8_decode($descObrigacao); ?></div></td>
			  	    </tr>
		 	<?php endforeach;
	  		endif;?>
	  		
	  		
	  		 <?php if(count($obervacao) > 0):?>
			  		<tr class="conteudo_obr_" >
			  		   <td valign="middle"><div style="padding: 5px;"><?php echo trim(utf8_decode($obervacao)); ?></div></td>
			  	    </tr>
	  		<?php endif;?>
	  		
		 </tbody>
		 
	  </table>
     
     </body> 
</html>