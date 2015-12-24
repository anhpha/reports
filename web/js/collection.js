/**
 * 
 */
(function( $ ) {
 
	var DocumentList = Backbone.Collection.extend({
		model: Document,
		url: function(){
			if (this.options.catId != 'undefined' && this.options.catId != ''){
				return 'http://localhost/reports/web/app_dev.php/api/document' +
					'/' + this.options.catId;
			} else {
				return 'http://localhost/reports/web/app_dev.php/api/document';
			}
		}
	});
 
}( jQuery ));