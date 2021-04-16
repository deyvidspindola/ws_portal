/**
 * Helpers
 *
 * @file util.js
 * @author Alex Sandro Médice
 * @version 24/10/2012
 * @since 24/10/2012
 */
function Util() {
	
	/**
	 * Alterna marcando ou desmarcando todos os checkbox
	 * 
	 * @see jQuery Selector válido http://api.jquery.com/category/selectors/
	 * @emxample var objUtil = new Util(); objUtil.toggleChecked($("#checked_all"), $(".toggle_checkbox"));
	 * @var Selector elementCheckedAll ID do checkbox que marca ou desmarca todos os checkbox
	 * @var Selector elementsToggleChecked Classe dos checkbox que serão marcados ou desmarcados
	 * @return void
	 */
	this.toggleChecked = function(elementCheckedAll, elementsToggleChecked) {
		
		elementCheckedAll.click(function(){
			var isCheckedAll = $(this).is(':checked');
			
			elementsToggleChecked.each(function(){
				
				var isDisabled = $(this).attr('disabled');
				
				if (isDisabled != 'disabled') {
					$(this).attr('checked', isCheckedAll);
				}
			});
		});
		
		elementsToggleChecked.click(function(){
			
			var isCheckedAll = true;
			
			elementsToggleChecked.each(function(){
				
				var isDisabled = $(this).attr('disabled');
				
				if (isDisabled != 'disabled') {
					isChecked = $(this).is(':checked');
					
					if (!isChecked) {
						isCheckedAll = false;
					}
				}
			});
			
			elementCheckedAll.attr('checked', isCheckedAll);
		});
	};
	
}