<?php include 'header_view.php';?>

<div class="modulo_titulo"><?=$moduleTitle?></div>      

<div class="modulo_conteudo">

    <?php 
        if(!empty($response)) {

            foreach($response as $resp) {
                echo '<div class="' . $resp['class'] . '">'; 
                echo $resp['message'];
                echo "</div>";
            }
        }
    ?>          
           
    <div class="bloco_titulo"><?=$pageTitle?></div>
    
    <form id="form" enctype="multipart/form-data" method="post" action="prn_upload_fotografias_os.php">

        <input type="hidden" name="action" value="create">
        <input type="hidden" name="ssio_foto_d" value="DIREITA">
        <input type="hidden" name="ssio_foto_e" value="ESQUERDA">

        <div class="bloco_conteudo">
            
            <div class="formulario">
                
                <div class="campo medio">
                    <label for="ssioordoid">Número O.S. *</label>
                    <input 
                        type="text" 
                        id="ssioordoid" 
                        name="ssioordoid" 
                        class="campo numeric <?=!empty($response) && $model->myInArray("ssioordoid", $response) ? 'erro' : '' ?>" 
                        value="<?=isset($_POST['ssioordoid']) ? $_POST['ssioordoid'] : ''?>"
                        maxlength ="10"
                    />
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="ssio_foto_d">Imagem Direita *</label>
                    <input 
                        type="file" 
                        name="ssio_foto_d_file" 
                        class="<?=!empty($response) && $model->myInArray("ssio_foto_d_file", $response) ? 'erro' : '' ?>"
                        accept="image/*"
                    />
                </div>

                <div class="clear"></div>

                <div class="campo medio">
                    <label for="ssio_foto_e">Imagem Esquerda *</label>
                    <input 
                        type="file" 
                        name="ssio_foto_e_file" 
                        id="ssio_foto_e_file"
                        class="<?=!empty($response) && $model->myInArray("ssio_foto_e_file", $response) ? 'erro' : '' ?>"
                        accept="image/*"
                    />
                </div>

                <div class="clear"></div>
                <div class="separador"></div>

            </div>

        </div>

        <div class="bloco_acoes">
 
            <button type="button" id="reset">
                Limpar
            </button>

             <button type="submit" name="submit">
                Upload
            </button>
        </div>

    </form>

</div>  
<div class="separador"></div>

<?php include 'footer_view.php';?>

<script>
    jQuery(function() {
        
        jQuery(".numeric").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        jQuery('.numeric').bind('paste', function(e) {
            e.preventDefault();
        });

        jQuery('#reset').on('click', function() {
            clearFormElements(jQuery('#form'));
        });
    });

    function clearFormElements(element) {

        jQuery(element).find(':input').each(function() {
            switch(this.type) {
                case 'password':
                case 'select-multiple':
                case 'select-one':
                case 'text':
                case 'textarea':
                    jQuery(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    this.checked = false;
            }
        });

        jQuery('input[type=file]').each(function(){
            //jQuery(this).after(jQuery(this).clone(true)).remove();
            jQuery(this).val('');
        });
    }

</script>