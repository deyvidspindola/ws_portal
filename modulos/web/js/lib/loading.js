$(window).load( function () {
//	$('body').removeClass("loading"); // fecha loading ao terminar de carregar a tela
});

$(document).ready(function(){
	var objLoading = new Loading();
	
	objLoading.append('body');
//	objLoading.show(); // abre loading ao iniciar a tela
	
//	objLoading.ajax();
	objLoading.click();
//	objLoading.testAjax();
	
//	objLoading.appendFotter('#wrapper');
//	$("#loading").hide();
});

function Loading() {
	
	__construct = function(handle) {
//		alert(handle);
	}(this);
	
	this.append = function(handle) {
		$(handle).prepend('<div class="loading-box"><img src="modulos/web/images/loading.gif" alt="Carregando..." /></div>');
	};
	
	this.appendFotter = function(handle) {
		$(handle).append('<div id="loading" class="loading" style="display:block;"><img src="images/loading.gif" alt="Loading" /></div>');
	};
	
	this.show = function() {
        
        //armazena a largura e a altura da janela
        var winH = $(window).height();
        var winW = $(document).width();
        var scrollTop = $(window).scrollTop()
        
        $('.loading-box').css({
            height: $(document).innerHeight() + 'px'
        });
        
        //centraliza na tela a imagem de loading
        $('.loading-box img').css('top',  (winH/2 - (65)) + scrollTop);
        $('.loading-box img').css('left', winW/2 - (65 / 2));
        
		$('body').addClass("loading");
	};
	
	this.hide = function() {
		$('body').removeClass("loading"); 
	};
	
	this.ajax = function() {
		var objLoading = new Loading();
		
		$("body").on({
		    ajaxStart: function() {
		    	objLoading.show();
		    },
		    ajaxStop: function() {
		    	objLoading.hide();
		    }    
		});
	};
	
	this.click = function() {
		var objLoading = new Loading();
		
		$(".loading-show-click").bind('click', function(){
	    	objLoading.show();
		});
		
		$(".loading-hide-click").bind('click', function(){
	    	objLoading.hide();
		});
	};
		
	this.testAjax = function() {
		$("body").click(function(){
			$.get("/sistemaWeb");     
		});
	};
}