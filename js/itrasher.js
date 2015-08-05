jQuery(document).ready(function(e) { 

	jQuery('.es_all_listing input[type="checkbox"], .es_all_listing_head input[type="checkbox"]').prop("checked",false)
	 	
	jQuery('.es_all_listing_head input[type="checkbox"]').click(function(){
		
		if(jQuery(this).prop("checked")==true){
			
			jQuery('.es_all_listing input[type="checkbox"]').prop("checked",true)
			jQuery('.es_all_listing li').addClass('active');
			
		} else {
			
			jQuery('.es_all_listing input[type="checkbox"]').prop("checked",false)
			jQuery('.es_all_listing li').removeClass('active');
				
		}
	});	
	
	jQuery('.es_all_listing input[type="checkbox"]').click(function(){
		
		if(jQuery(this).prop("checked")==true){
		
			jQuery(this).parents('.es_all_listing li').addClass('active');
			
		} else {
			
			jQuery(this).parents('.es_all_listing li').removeClass('active');
				
		}
	});	

	jQuery('#es_listing_select_all').click(function(){
		
		jQuery('.es_all_listing_head input[type="checkbox"], .es_all_listing input[type="checkbox"]').prop("checked",true)
		jQuery('.es_all_listing li').addClass('active');
		
	});
	
	jQuery('#es_listing_undo_selection').click(function(){
		
		jQuery('.es_all_listing_head input[type="checkbox"], .es_all_listing input[type="checkbox"]').prop("checked",false)
		jQuery('.es_all_listing li').removeClass('active');
		
	});

	jQuery('#es_listing_del').click(function(){
		 
		//jQuery("#listing_actions").submit();
		
		var images = [];

		jQuery("#listing_actions input[name='images[]']:checked:enabled").each(function() {
		    images.push(jQuery(this).val());
		});

		var $message = jQuery('.itrasher-waiting');
		$message.hide();

		if(images.length < 1) {
			$message.css('background-color', 'red');
			$message.html('<p>Oops! Please select an image.</p>');
			$message.fadeIn('slow');
			return false;
		}

		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'itrasher_trash',
				'data': images
			},
			success: function(response) {

				if(response)
				{
					$message.css('background-color', 'green');
					$message.html('<p>Complete! Redirecting...</p>');

					$message.fadeOut('slow', function()
					{
						location.reload();
					});
				}
			},
			beforeSend: function() {
				$message.css('background-color', '#0073aa');
				$message.html('<p>Deleting unused images...</p>');
				$message.fadeIn('slow');
			}

		});
	});

});