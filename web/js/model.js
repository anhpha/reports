/**
 * 
 */
(function( $ ) {
 
	var Document = Backbone.Model.extend({
		urlRoot: 'http://localhost/reports/web/app_dev.php/api/document',
		url: function(){
			return this.urlRoot + '/' + this.id;
		}
	});
 
}( jQuery ));