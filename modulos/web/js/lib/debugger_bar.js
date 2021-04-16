jQuery(document).ready(function(){
    
    div_anterior = "";
    
    jQuery('#metaFW_debugger ul li').click(function(){
    	
    	DebuggerEvents.debuggerInit(jQuery(this));
    	
    });
        
    jQuery('#debugger_detail ol li a').click(function(){
        
    	DebuggerEvents.debuggerDetailInit(jQuery(this));
        
    });
        
    //Verificar se foi ajax
    jQuery(document).ajaxSend(function(e, xhr, opt){
    	    	    	
    	jQuery('#debugger_detail').animate(
            {
                top: '-54px',
                height: 0
            },
            500,
            function(){
                jQuery('#show_config, #show_session, #show_scripts, #show_log').hide();
            }
        );
    	
    	jQuery('#metaFW_debugger').html('<img class="img-loader" src="modulos/web/images/ajax-loader.gif" />');
            	
    	xhr.done(function(data){
    	
    		var reg 		= data;        		
    		var degubber 	= reg.match(/DEBUGGER BAR START \-\-\>([^<]*(?:(?!<\/?body)<[^<]*)*)\<\!\-\- DEBUGGER BAR END/i);
    		var detail 		= reg.match(/DEBUGGER DETAIL START \-\-\>([^<]*(?:(?!<\/?body)<[^<]*)*)\<\!\-\- DEBUGGER DETAIL END/i);
    		div_anterior = "";
    		
    		if (degubber) {
    			jQuery('#metaFW_debugger').eq(0).remove();
    			jQuery('#metaFW_debugger').html(degubber[1]);	
    		}
    		
    		if(detail){    			
    			jQuery('#debugger_detail').eq(0).remove();
    			jQuery('#debugger_detail').html(detail[1]);        			
    		}
    		
    		jQuery('#metaFW_debugger ul li').bind('click', function(){
    			DebuggerEvents.debuggerInit(jQuery(this));
		    });
    		
    		jQuery('#debugger_detail ol li a').bind('click', function(){
    			DebuggerEvents.debuggerDetailInit(jQuery(this));
    		});
    		
    	});       	
    	
    });
});

var DebuggerEvents = {
    debuggerInit: function(element) {
    	
		jQuery('#debugger_detail').stop(true, true);
		
        div = element.attr('class');
	        
        if(div != div_anterior) {
        	
            jQuery('#' + div_anterior).fadeOut();
            
            jQuery('#debugger_detail').animate(
                {
                    top: 0,
                    height: jQuery('#' + div).innerHeight()
                },
                500,
                function(){
                    jQuery('#' + div).fadeIn();
                }
            );
                    
            div_anterior = element.attr('class');
            
        }else{
            
            jQuery('#debugger_detail').animate(
                {
                    top: '-54px',
                    height: 0
                },
                500,
                function(){
                    jQuery('#' + div).hide();
                }
            );
            div_anterior = "";
        }	    
	},
	
	debuggerDetailInit: function(element) {
		element.next().slideToggle();
        jQuery('#debugger_detail').css('height', 'auto');
	}
}


