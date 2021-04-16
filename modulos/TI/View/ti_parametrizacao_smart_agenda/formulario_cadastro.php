
<div class="bloco_titulo">Parâmetros Gerais</div>
<div class="bloco_conteudo">
    <div class="formulario">


        <div class="campo medio">
            <label id="lbl_tamanho_time_slot" for="tamanho_time_slot">
                Tamanho do Time-Slot
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['TAMANHO_TIME_SLOT']['legenda']; ?>','D' , '');">
            </label>
            <input id="tamanho_time_slot" class="campo numero obrigatorio" type="text" name="tamanho_time_slot"  maxlength="3"
                    value="<?php echo $this->view->dados['TAMANHO_TIME_SLOT']['valor']; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_limpeza_log" for="limpeza_log">
                Tempo Limpeza de Log
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['LIMPEZA_LOG']['legenda']; ?>','D' , '');">
            </label>
            <input id="limpeza_log" class="campo numero obrigatorio" type="text" name="limpeza_log"  maxlength="3"
                    value="<?php echo $this->view->dados['LIMPEZA_LOG']['valor']; ?>">
        </div>

        <div class="campo medio">
            <label id="lbl_limite_registros_fila_mensageria" for="limite_registros_fila_mensageria">
                Registros Processados na Fila
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['LIMITE_REGISTROS_FILA_MENSAGERIA']['legenda']; ?>','D' , '');">
            </label>
            <input id="limite_registros_fila_mensageria" class="campo numero obrigatorio" type="text" name="limite_registros_fila_mensageria"  maxlength="2"
                    value="<?php echo $this->view->dados['LIMITE_REGISTROS_FILA_MENSAGERIA']['valor']; ?>">
        </div>
        <div class="clear"></div>

   </div>
</div>
<div class="separador"></div>


<div class="bloco_titulo">Parâmetros de Autenticação</div>
<div class="bloco_conteudo">
    <div class="formulario">


        <div class="campo medio">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_company">
                Company APIs SOAP
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_COMPANY']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_company" class="campo obrigatorio" type="text"
                    name="<?=$this->ambiente['low_case'];?>_company"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_COMPANY']['valor']; ?>">
        </div>
        <div class="campo medio">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_login">
                Login APIs SOAP
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_LOGIN']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_login" class="campo obrigatorio" type="text" 
                    name="<?=$this->ambiente['low_case'];?>_login"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_LOGIN']['valor']; ?>">
        </div>

        <div class="campo medio">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_password">
                Senha APIs SOAP
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_PASSWORD']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_password" class="campo obrigatorio" type="text" 
                    name="<?=$this->ambiente['low_case'];?>_password"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_PASSWORD']['valor']; ?>">
        </div>
        <div class="clear"></div>

         <div class="campo medio">
            <label id="lbl_usuario_outbound">
                Login Outbound
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['USUARIO_OUTBOUND']['legenda']; ?>','D' , '');">
            </label>
            <input id="usuario_outbound" class="campo obrigatorio" type="text" name="usuario_outbound"
                    value="<?php echo $this->view->dados['USUARIO_OUTBOUND']['valor']; ?>">
        </div>
        <div class="campo medio">
            <label id="lbl_senha_outbound">
                Senha Outbound
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['SENHA_OUTBOUND']['legenda']; ?>','D' , '');">
            </label>
            <input id="senha_outbound" class="campo obrigatorio" type="password" name="senha_outbound"
                    value="<?php echo $this->view->dados['SENHA_OUTBOUND']['valor']; ?>">
        </div>

        <div class="campo medio">
            <label id="lbl_validade_senha_segundos">
                Validade da Senha Outbound
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['VALIDADE_SENHA_SEGUNDOS']['legenda']; ?>','D' , '');">
            </label>
            <input id="validade_senha_segundos" class="campo numero obrigatorio" type="text" name="validade_senha_segundos"  maxlength="3"
                    value="<?php echo $this->view->dados['VALIDADE_SENHA_SEGUNDOS']['valor']; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_rest_get_token">
                Segmento URI - GET Token
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_TOKEN']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_token" class="campo obrigatorio" type="text" name="rest_get_token"
                    value="<?php echo $this->view->dados['REST_GET_TOKEN']['valor']; ?>">
        </div>
        <div class="campo medio">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_client_id">
                ID CLIENTE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_CLIENT_ID']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_client_id" class="campo obrigatorio" type="text" 
                    name="<?=$this->ambiente['low_case'];?>_client_id"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_CLIENT_ID']['valor']; ?>">
        </div>
        <div class="campo medio">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_client_secret">
                HASH Cliente
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_CLIENT_SECRET']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_client_secret" class="campo obrigatorio" type="text" 
                    name="<?=$this->ambiente['low_case'];?>_client_secret"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_CLIENT_SECRET']['valor']; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_user_type_local">
                Perfil de Usuário Padrão
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['USER_TYPE_LOCAL']['legenda']; ?>','D' , '');">
            </label>
            <input id="user_type_local" class="campo obrigatorio" type="text" name="user_type_local"
                    value="<?php echo $this->view->dados['USER_TYPE_LOCAL']['valor']; ?>">
        </div>
         <div class="campo maior">
            <label id="lbl_<?=$this->ambiente['low_user_type'];?>">
                Perfil de Usuário para Técnicos e Prestadores
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[ $this->ambiente['up_user_type']]['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_user_type'];?>" class="campo obrigatorio" type="text" name="<?=$this->ambiente['low_user_type'];?>"
                    value="<?php echo $this->view->dados[$this->ambiente['up_user_type']]['valor']; ?>">
        </div>
        <div class="clear"></div>

   </div>
</div>

<div class="separador"></div>

<div class="bloco_titulo">Parâmetros APIs</div>
<div class="bloco_conteudo">
    <div class="formulario">


        <div class="campo maior">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_capacity">
                URI API Capacity
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_CAPACITY']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_capacity" class="campo obrigatorio" type="text" name="<?=$this->ambiente['low_case'];?>_capacity"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_CAPACITY']['valor']; ?>">
        </div>


        <div class="campo maior">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_inbound">
                URI API Inbound
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_INBOUND']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_inbound" class="campo obrigatorio" type="text" name="<?=$this->ambiente['low_case'];?>_inbound"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_INBOUND']['valor']; ?>">
        </div>

        <div class="campo maior">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_outbound">
                URI API Outbound
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_OUTBOUND']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_outbound" class="campo obrigatorio" type="text" name="<?=$this->ambiente['low_case'];?>_outbound"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_OUTBOUND']['valor']; ?>">
        </div>
        <div class="clear"></div>

         <div class="campo maior">
            <label id="lbl_<?=$this->ambiente['low_case'];?>_ofsc_url">
                URI Base APIs REST
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados[$this->ambiente[up_case].'_OFSC_URL']['legenda']; ?>','D' , '');">
            </label>
            <input id="<?=$this->ambiente['low_case'];?>_ofsc_url" class="campo obrigatorio" type="text" name="<?=$this->ambiente['low_case'];?>_ofsc_url"
                    value="<?php echo $this->view->dados[$this->ambiente[up_case].'_OFSC_URL']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_delete_link">
                Segmento URI API Activy - DELETE Link
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_DELETE_LINK']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_delete_link" class="campo obrigatorio" type="text" name="rest_delete_link"
                    value="<?php echo $this->view->dados['REST_DELETE_LINK']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_get_file">
                Segmento URI API Activy - GET File
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_FILE']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_file" class="campo obrigatorio" type="text" name="rest_get_file"
                    value="<?php echo $this->view->dados['REST_GET_FILE']['valor']; ?>">
        </div>
        <div class="clear"></div>

         <div class="campo maior">
            <label id="lbl_rest_get_activity">
                Segmento URI API Activy - GET
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_ACTIVITY']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_activity" class="campo obrigatorio" type="text" name="rest_get_activity"
                    value="<?php echo $this->view->dados['REST_GET_ACTIVITY']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_update_activity">
                Segmento URI API Activy - UPDATE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_UPDATE_ACTIVITY']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_update_activity" class="campo obrigatorio" type="text" name="rest_update_activity"
                    value="<?php echo $this->view->dados['REST_UPDATE_ACTIVITY']['valor']; ?>">
        </div>
         <div class="campo maior">
            <label id="lbl_rest_cancel_activity">
                Segmento URI API Activy - CANCEL
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_CANCEL_ACTIVITY']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_cancel_activity" class="campo obrigatorio" type="text" name="rest_cancel_activity"
                    value="<?php echo $this->view->dados['REST_CANCEL_ACTIVITY']['valor']; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_rest_get_resource">
                Segmento URI API Resource - GET
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_RESOURCE']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_resource" class="campo obrigatorio" type="text" name="rest_get_resource"
                    value="<?php echo $this->view->dados['REST_GET_RESOURCE']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_create_resource">
                Segmento URI API Resource - CREATE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_CREATE_RESOURCE']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_create_resource" class="campo obrigatorio" type="text" name="rest_create_resource"
                    value="<?php echo $this->view->dados['REST_CREATE_RESOURCE']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_update_resource">
                Segmento URI API Resource - UPDATE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_UPDATE_RESOURCE']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_update_resource" class="campo obrigatorio" type="text" name="rest_update_resource"
                    value="<?php echo $this->view->dados['REST_UPDATE_RESOURCE']['valor']; ?>">
        </div>
        <div class="clear"></div>

         <div class="campo maior">
            <label id="lbl_rest_get_location">
                Segmento URI API Resource - Location
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_LOCATION']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_location" class="campo obrigatorio" type="text" name="rest_get_location"
                    value="<?php echo $this->view->dados['REST_GET_LOCATION']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_get_assigned_locations">
                Segmento URI API Resource - Assigned Locations
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_ASSIGNED_LOCATIONS']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_assigned_locations" class="campo obrigatorio" type="text" name="rest_get_assigned_locations"
                    value="<?php echo $this->view->dados['REST_GET_ASSIGNED_LOCATIONS']['valor']; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_rest_create_user">
                Segmento URI API User - CREATE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_CREATE_USER']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_create_user" class="campo obrigatorio" type="text" name="rest_create_user"
                    value="<?php echo $this->view->dados['REST_CREATE_USER']['valor']; ?>">
        </div>
        <div class="campo maior">   
            <label id="lbl_rest_get_user">
                Segmento URI API User - GET
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_GET_USER']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_get_user" class="campo obrigatorio" type="text" name="rest_get_user"
                    value="<?php echo $this->view->dados['REST_GET_USER']['valor']; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_rest_update_user">
                Segmento URI API User - UPDATE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_UPDATE_USER']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_update_user" class="campo obrigatorio" type="text" name="rest_update_user"
                    value="<?php echo $this->view->dados['REST_UPDATE_USER']['valor']; ?>">
        </div>
        <div class="campo maior">
            <label id="lbl_rest_delete_user">
                Segmento URI API User - DELETE
                <img class="btn-help" src="images/help10.gif" style="cursor: pointer"
                    onclick="mostrarHelpComment(this,'<?php echo $this->view->dados['REST_DELETE_USER']['legenda']; ?>','D' , '');">
            </label>
            <input id="rest_delete_user" class="campo obrigatorio" type="text" name="rest_delete_user"
                    value="<?php echo $this->view->dados['REST_DELETE_USER']['valor']; ?>">
        </div>
        <div class="clear"></div>

   </div>
</div>
<div class="separador"></div>

<div class="bloco_acoes">
    <button type="button" id="bt_gravar" name="bt_gravar" value="gravar">Salvar</button>
</div>