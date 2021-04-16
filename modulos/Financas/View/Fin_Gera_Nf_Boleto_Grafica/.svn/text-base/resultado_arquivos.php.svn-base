 <?php require_once '_header.php'; 
 $arquivos = $control->listaArquivos();
  $numerosRegistro = 0;
 ?>
<link type="text/css" rel="stylesheet" href="lib/css/style.css"/>
    <link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css"/>
    <div class="bloco_titulo">Arquivos Gerados</div>

    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                    <th class="menor centro">Arquivo</th>
                    <th class="menor centro" style="width: 100px;">Tipo</th>
                    <th class="centro" style="width: 300px;">Data</th>
                    <th class="centro" style="width: 300px;">Tamanho</th>
                    <TH class="centro" style="width: 100px;">Ação</TH></TR>
                </thead>
                <tbody>
                <?php 
                $count = 1;

                if(count($arquivos) || !empty($arquivos)) {
                $caminho = $control->RetornaCaminhoServidor();
                foreach ($arquivos as $row=>$key) {
					$infFile = pathinfo($caminho."/".$key['nome']);
					$tamanhoarquivo = $caminho."/".$infFile['basename'];
					$tipoArquivo = substr($key['nome'],0,6);
				if($infFile['extension'] != "svn" && $infFile['filename'] != 'sbtec'){
					
				if($count%2==0){
				?>
						<tr class="impar">
						<td class="centro">
           				 <?php 
           				 
           				 echo $infFile['filename']; ?>
           				</td>
		           		<td class="centro">
		           		 <?php 
		           		 	echo $infFile['extension']; 
		           		 ?>
		           		</td>
		           		<td class="centro">
		           		<?php 
						echo	date ("d/m/Y H:i:s.", filectime($caminho."/".$infFile['basename']));	
		           		?>
		           		</td>
		           		<td class="centro">
		           		<?php echo $control->tamanhoArquivo($tamanhoarquivo);?>
		           		</td>
		           		<td class="acao centro">
		           		<a title=Excluir rel="<?php  echo $key['nome']; ?>" id="btn_excluir" href="javascript:void(0);">
		           		<IMG class=icone alt=Cancelar src="images/icon_error.png"></a>
		           		<?php 
		           		if($tipoArquivo != "PREVIA") {
		           		?>
		           		<a title=FTP rel="<?php  echo $key['nome']; ?>" id="ftp" href="javascript:void(0);">
		           		<IMG class=icone alt=Editar src="images/ftp.png"></a>
		           		 <?php }?>
		           		<a title=Downloads target="_blank" href="download.php?arquivo=<?php echo $caminho.$key['nome']; ?>">
		           		<IMG class=icone alt=Editar src="images/download.png"></a>
		           		
		           		</td>
           				</tr>
					<?php 
					}
				else{ ?>
				
								<tr class="par">
								<td class="centro">
           				 <?php echo $infFile['filename']; ?>
           				</td>
		           		<td class="centro">
		           		 <?php 
		           		 	echo $infFile['extension']; 
		           		 ?>
		           		</td>
		           		<td class="centro">
		           		<?php 
						echo	date ("d/m/Y H:i:s.", filectime($caminho."/".$infFile['basename']));	
		           		?>
		           		</td>
		           		<td class="centro">
		           		<?php echo $control->tamanhoArquivo($tamanhoarquivo);?>
		           		</td>
		           		<td class="acao centro">
		           		<a rel="<?php  echo $key['nome']; ?>" id="btn_excluir" href="javascript:void(0);">
		           		<IMG  title=Excluir class=icone alt=Cancelar src="images/icon_error.png"></a>
		           		<?php 
		           		$tipoArquivo = substr($key['nome'],0,6);
		           		
		           		if($tipoArquivo != "PREVIA") {
		           		?>
		           		<a  rel="<?php  echo $key['nome']; ?>" id="ftp" href="javascript:void(0);">
		           		<IMG title=FTP class=icone alt=Editar src="images/ftp.png"></a>
		           		 <?php }?>
		           		<a  target="_blank" href="download.php?arquivo=<?php echo $caminho.$key['nome']; ?>">
		           		<IMG title=Download class=icone alt=Editar src="images/download.png"></a>
		           		
		           		</td>
           				</tr>
			<?php 
				}
					$numerosRegistro++;
					$count++;
				}
				
	            
                }
              }else {
                ?>
 
					 <td align="center" colspan="5">Não existe arquivos para listar.</td>
 
 				<?php }?>
                </tbody>
                <tfoot>
        
                    <tr class="center">
                        <td align="center" colspan="5">
                                 <?php
                       echo ($numerosRegistro > 0) ? $numerosRegistro . ' registros encontrados.' : '0 registro encontrado.';
                        ?>
                        </td>
                    </tr>
                     
                </tfoot>
            </table>
        </div>
    </div>
