/**
 * 
 */
(function( $ ) {
 
    var ReferenceItemField = function (el, options){
    	var that = this;
    	this.options = options;
    	this.el = $(el);
    	this.itemWrapper = $('<div class="col-md-12 no-padding"></div>').insertAfter(this.el);
    	this.input = $('<input readonly class="link-field" id="input_' + this.el.id + '">').appendTo(this.itemWrapper);
    	if ($(el).find('option:selected').val() != '') {
    		this.input.val($(el).find('option:selected').text());
    	}
    	if ($(el).find('option').length == 0 || $(el).find('option')[0].value == ''){
    		this.input.attr('required', false);
    	} else {
    		this.input.attr('required', true);
    	}
    	this.seclectButton = $('<a class="btn btn-default"><i class="fa fa-link"></i></a>').appendTo(this.itemWrapper);
    	this.source = $(el).data('source');
    	this.el.hide();
    	this.seclectButton.on('click', function(){
    		that._showDialog();
    	});
    	
    }
    
    ReferenceItemField.prototype = {
    	constructor: ReferenceItemField,
    	_showDialog: function(){
    		var that = this;
    		var dialogWrap = $('<div></div>');
    		this.contentDiv = $('<div class="col-md-12"><div>');
    		var title = this.options.title;
    		dialogWrap.append(this.contentDiv);
    		this.dialog = $(dialogWrap).dialog({
    		      autoOpen: true,
    		      height: 300,
    		      title: title,
    		      width: 350,
    		      modal: true,
    		      buttons: {
    		        Cancel: function() {
    		         that.dialog.dialog( "close" );
    		        }
    		      },
    		      close: function() {
    		      }
    		    });
    		this._loadData('',this.contentDiv);
    	},
    	_loadData: function(id, parentEl){
    		parentEl.append('<i class="fa fa-spinner fa-pulse fa-fw margin-bottom"></i>');
    		var that = this;
    		var Folder = Backbone.Model.extend();
    		var apiUrl = this.options.dataSource;
    		var rooCss = ''; 
    		if (id != ''){
    			apiUrl += '/' + id;
    			//Root directory
    		} 
    		var FolderList = Backbone.Collection.extend({
    			model: Folder,
    			url: apiUrl,
    			parse: function (response, options) {
    	            return response.subCats;
    	        }
    		});
    		
    		var folders = new FolderList();
    		folders.fetch({
    			headers: {'Accept': 'json'},
    			success: function(collection, response){
    				$(parentEl).find('.fa-spinner').remove();
    				$.each(collection.models, function(index, value){
    					var wrapper = $('<ul class=""></ul>')
    									.appendTo(parentEl);
    					//Root ul
    					if (id == ''){
    						wrapper.addClass('no-padding');
    					}
    					var item = $('<li style="display: block;"></li>').appendTo(wrapper);
    					var itemLink = $('<a href="javascript:void(0)">' + value.get('name') +'</a>').appendTo(item);
    					itemLink.on('click', function(){
    						that._chooseItem(value.get('id'), value.get('name'));
    					});
    					var itemButton = $('<a href="javascript:void(0)" id=' + value.get('id') + ' class = "btn-xs pull-left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>')
    									.prependTo(item);
    					itemButton.on('click', function(){
    						that._loadData(value.get('id'), item);
    						$(item).find('#' + value.get('id')).remove();
    						var removeChildrenButton = $('<a href="javascript:void(0)" id=' + value.get('id') + ' class = "btn-xs pull-left"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a>')
    													.prependTo(item);
    					});
    				});
    			}
    		});
    	},
    	//todo
    	_removeChildren: function(el, id){
    		$(item).find('#' + id).remove();
    		$(el).find('ul').remove();
    	},
    	_chooseItem: function(id, text){
    		this.el.val(id);
    		this.input.val(text);
    		this.dialog.dialog("close");
    	}
    }
    
	$.fn.referenceItemField = function(options) {
        this.each(function(){
        	var field = new ReferenceItemField(this, options);
        });
    	return this;
 
    };
    
 
}( jQuery ));