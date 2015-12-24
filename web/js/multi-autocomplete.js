(function( $ ) {
    $.widget( "custom.multi_autocomplete", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createSelectedList();
        this._createInputLabel();
        this._createAutocomplete();
      },
      _createSelectedList: function() {
    	  this.selectedDiv = $('<div class="members-list col-md-12"></div>').appendTo( this.wrapper );
      },
      _createInputLabel: function() {
    	  this.inputLabel = $('<div class="col-md-12 no-padding"><label>' + this.options.inputLabel + '</label></div>').appendTo( this.wrapper );
      },
      _addSelectedItem: function(selectedItem){
    	  var that = this;
      	  var outWarapper = $('<div class="col-md-6 member-tag-wrapper"></div>');
      	  var nameWrapper = $('<div class="member-tag col-md-10"></div>');
      	  nameWrapper.html(selectedItem.text);
      	  outWarapper.append(nameWrapper);
      	  var actionWrapper = $('<div class="member-button col-md-2"></div>');
      	  var deleteButton = $('<a class="btn-xs btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>');
      	  deleteButton.click(function(){
      		  var value = $(this).parent().parent().find('input:hidden').val(); 
      		  that.selectedIDs.splice( $.inArray(value, that.selectedIDs), 1 );
      		  $(this).parent().parent().remove();
      		  //Unselect 
      		that.element.find('option').each(function(){
      			if (this.value == value){
      				this.selected = false;
      			}
      		});
      	  });
      	  actionWrapper.append(deleteButton);
      	  outWarapper.append(actionWrapper);
      	  var hiddendId = $('<input type="hidden">').val(selectedItem.value);
      	  outWarapper.append(hiddendId);
      	  this.selectedDiv.append(outWarapper);
    	  this.selectedIDs.push(selectedItem.value);
      },
      selectedIDs: [],
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" );
        var that = this;
        $.each(selected, function (index, selectedItem){
        	that._addSelectedItem(selectedItem);
        });
        
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( '' )
          .attr( "title", "" )
          .addClass( "form-control custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" ),
            close: function(){
            	that.input.val("");
            }
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
        var that = this;
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            if ($.inArray(ui.item.option.value, that.selectedIDs) == -1) {
            	that._addSelectedItem(ui.item.option);
            } else {
            	alert(that.options.duplicatedError);
            }
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
        	return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
        
        // Found a match, nothing to do
        if ( valid ) {
        	return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " " + this.options.notFoundError )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
       this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );