/**
 * 
 */
function setWaitingStatus(el){
	var button = $(el).find('button[type="submit"]');
	$(button).prop('disabled', 'disabled');
	$(button).html('Please wait while uploading...');
	return true;
}
function confirmDelete(form, text, itemName)
{
	bootbox.confirm({ 
	    size: 'small',
	    message: text + ' "' + itemName + '" ?',
	    callback: function(result){ 
	    	if (result){
				  $(form).submit();
			  } 
	    },
	    buttons: {
	        cancel: {   
	          label: "Cancel",
	          className: "btn-primary",
	          callback: function() {}
	        },
	        
	        confirm: {
	          className: "btn-danger",
	          label: "Delete!",
	          callback: function() {
	        	  //return true;
	          }
	        },
	      }
	})
}
function addFormItem(collectionHolder, itemTitle) {
    var removeEl ='<div class="col-md-2 actions-col no-padding"><a class="btn-sm btn-danger" href="#" name="remove_docs_btn">\
                        	<i class="glyphicon glyphicon-remove" aria-hidden="true"></i>\
                        </a></div>';
	// Get the data-prototype explained earlier
    var prototype = $(collectionHolder).data('prototype');

    // get the new index
    var index = $(collectionHolder).data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__label__/g, itemTitle + ' ' + index);
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $(collectionHolder).data('index', index + 1);
    // Display the form in the page in an li, before the "Add a tag" link li
    var newFormEl = $('<div class="form-group col-md-10 no-padding"></div>').append(newForm);
    var wraper = $('<div class="col-md-12 doc-row no-padding"></div>').append(newFormEl);
    $(collectionHolder).append($(wraper).append(removeEl));
    $('[name="remove_docs_btn"]').on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        $(this).parent().parent().remove();
    });
}

function initCollectionData(collectionEl, addEl, itemTitle){
	$(collectionEl).data('index', $(collectionEl).find('.doc-row').length);
	$(addEl).click(function(e){
		e.preventDefault();
		addFormItem(collectionEl, itemTitle);
	});
}

function newDocumentCategory(catId, title, nameLabel, descriptionLabel){
	var dialogHTML = '<form action="/reports/web/app_dev.php/document/create?catId=" method="post"> \
		<input type="hidden" id="cpse_api_projectbundle_documenttype_parent"\
		name="cpse_api_projectbundle_documenttype[parent]" required="required" maxlength="255" class="form-control" \
		 value= "' + catId + '" >\
		<div class="form-group">\
	        <label for="cpse_api_projectbundle_documenttype_name" class="required">' + nameLabel + '</label>\
	        <input type="text" id="cpse_api_projectbundle_documenttype_name" name="cpse_api_projectbundle_documenttype[name]" required="required" maxlength="255" class="form-control">\
	    </div>\
	    <div class="form-group">\
			<label for="cpse_api_projectbundle_documenttype_description">' + descriptionLabel + '</label>\
			<textarea id="cpse_api_projectbundle_documenttype_description" name="cpse_api_projectbundle_documenttype[description]" cols="10" rows="5" maxlength="10024" class="form-control"></textarea>\
	    </div>\
	    <input type="hidden" id="cpse_api_projectbundle_documenttype_type" name="cpse_api_projectbundle_documenttype[type]" value="1">\
	</form>';
	var spin  = $('<div class="col-md-12 text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i></div>')
		var newDialog = $(dialogHTML).dialog({
		      autoOpen: true,
		      title: title,
		      width: 750,
		      modal: true,
		      buttons: {
		        Cancel: function() {
		        	newDialog.dialog( "close" );
		        },
		        OK: {
		        	click: function(){
		        		newDialog.find('[id$="_parent"]').val(catId);
		        		newDialog.hide();
		        		newDialog.parent().find('.ui-dialog-buttonpane').hide();
		        		newDialog.parent().find('.ui-dialog-titlebar').hide();
		        		newDialog.parent().append(spin);
		        		var submitData = newDialog.serialize();
		        		$.ajax({
		        	         url: "http://localhost/reports/web/app_dev.php/api/category",
		        	         data: submitData,
		        	         type: "POST",
		        	         headers: {"Accept": "json"},
		        	         success: function(data) { 
		        	        	 newDialog.dialog( "close" ); 
		        	         },
		        	         error: function (data){
		        	        	 alert('There is error, please try later or contact your admin!');
		        	         }
		        	      });
		        	},
		        	class: 'btn btn btn-success',
		        	text: 'OK'
		        }
		      },
		      close: function() {
		      }
		    });
}
