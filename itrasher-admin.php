<?php

require_once('lib/scan-dir.php');
require_once('config.php');
require_once('itrasher_scanner.php');


$dir = wp_upload_dir()['basedir'];
$url = wp_upload_dir()['baseurl'];

//var_dump(scanDir::scan($dir, 'jpg', true));

//initialize scanner and run scan method
$itrasher = new ITrasher_Scanner($config);

$images = $itrasher->scan();
?>

<div class="es_wrapper itrasher">

    <div class="es_header clearFix">
        <h2><?php _e( "Delete Unused Images", "es-plugin" ); ?></h2>
    </div>

	<div class="es_all_listing_search clearFix">
    	        
        <div class="es_manage_listing clearFix">
        	<label><?php _e( "Actions", "es-plugin" ); ?>:</label>
            <ul>
                <li><a href="javascript:void(0)" id="es_listing_select_all"><?php _e( "Select all", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_undo_selection"><?php _e( "Undo selection", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_del"><?php _e( "Delete", "es-plugin" ); ?></a></li>
            </ul>
            <ul class="feedback">
                <li><a target="_blank" href="http://boolex.com/contact-us/" id="es_listing_custom">We Customize for $30/hr</a></li>
                <li><a target="_blank" href="http://boolex.com/contact-us/" id="es_listing_suggest">Send Suggestions</a></li>
            </ul>
        </div>
        
    </div>

    <div class="itrasher-waiting"><p>Deleting unused images, please wait...</p></div>

	<div class="es_content_in clearFix">
	 		
        <div class="es_all_listing_head clearFix">
        	<div>
            	<input type="checkbox" value=""  />
            </div>
            <div class="hide-ipad hide-phone">
            	<?php _e( "Image", "es-plugin" ); ?>
            </div>
            <div class="itrasher-path">
            	<?php _e( "Path", "es-plugin" ); ?>
            </div>
            <div class="hide-phone">
            	<?php _e( "Status", "es-plugin" ); ?>
            </div>
        </div>

		<div class="es_all_listing clearFix">
        	<form id="listing_actions" action="" method="post">
                
                <ul>

                <?php if(!empty($images)) : ?>

    				<?php foreach($images as $image) : ?>
    				
	                    <li class="clearFix es_publish">
	                        <div>
	                            <p><input type="checkbox" id="images" name="images[]" value="<?= $image ?>"  /></p>
	                        </div>
	                        <div class="hide-ipad hide-phone">
									<img src="<?= $url ?>/<?= $image ?>" alt="" />
	                        </div>
	                        <div class="itrasher-path">
	                            <p><?= $image ?></p>
	                        </div>  
	                        <div>
	                            <p>unused</p>
	                        </div>                    
	                    </li>

	                <?php endforeach; ?>

	            <?php else : ?>

	            	<div>&nbsp;</div>
	            	<div class="itrasher-path"><p>No unused images found...</p></div>

	        	<?php endif; ?>

                </ul>
            
            </form>
        </div>

	</div>

</div>