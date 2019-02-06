jQuery(document).ready(function(){
	 var term_id = '<? $_POST?>';
	 var id;
	jQuery("#submit").on("click",function(){

		var data = {
		        action: 'save_taxonomy_custom_meta_field',
		        RequestType: 'myselect',
		        myselect:  jQuery('#myselect option:selected').val(),
		        term_id: term_id
	    };

		
		var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
	    jQuery.ajax({
	        type: 'POST',
	        dataType: "json",
	        url: ajax_object.ajaxurl,
	        data,
	    
	        success: function(response) {
	        	console.log(response);
	            jQuery(".target option:selected").remove();
	            return false;
	        }
	    });

	});

});