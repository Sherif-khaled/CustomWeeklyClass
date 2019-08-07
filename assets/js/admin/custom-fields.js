
jQuery(document).ready(function ($) {
        // $.ajax({
        //     url: "http://fajronline.dev.lab",
        //     success: function( data ) {
        //         alert( 'Your home page has ' + $(data).find('div').length + ' div elements.');
        //     }
        // })

	// let term_id = '<? $_POST?>';
	// jQuery("#submit").on("click",function(){
    //
	// 	var data = {
	// 		action: 'save_taxonomy_custom_meta_field',
	// 		RequestType: 'myselect',
	// 		myselect:  jQuery('#myselect option:selected').val(),
	// 		term_id: term_id
	// 	};
    //
    //
	// 	var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
	// 	jQuery.ajax({
	// 		type: 'POST',
	// 		dataType: "json",
	// 		url: ajax_object.ajaxurl,
	// 		data,
    //
	// 		success: function(response) {
    //
	// 			console.log(response);
	// 			jQuery(".target option:selected").remove();
	// 			return false;
	// 		}
	// 	});
    //
	// });

        $('select.tttt').on('change', function () {
        // let course_id = $(this).find('option:selected').val();
		// $.ajax({
        //     url: ajax_object.ajaxurl,
        //     type: 'POST',
        //     dataType: 'text',
        //     data: {
        //         action: 'get_course_batches',
        //         $course_id: course_id
        //     },
        //     success: function (data) {
        //         alert(data);
        //     },
        //     error: function (data) {
        //         //alert(data);
        //     }
        // });
        // return false;
	});
});