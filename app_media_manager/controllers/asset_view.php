<?php
/**
 * If user does not come from group_view, set current group ID to null
 */
if(!stristr($_SERVER['HTTP_REFERER'], "group_view")) {
	$_SESSION[CURRENT_GROUP_VAR] = null;
}

switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$asset = DBO_Asset::getOneByPublicId($_GET['id']);
				
				$group = DBO_Group::getOneById($_SESSION[CURRENT_GROUP_VAR]);
				if(isset($_GET['gid'])) {
					$group = DBO_Group::getOneById($_GET['gid']);
				}
				
				$collection = false;
				if(isset($_GET['cid'])) {
					$collection = DBO_Container::getOneById($_GET['cid']);
				}
								
				$filename = IMAGE_STORAGE_PATH . $asset->filename;
				$filesize = Util::filesize($filename);
				$meta = new Metadata_XMP($filename);
				$resolution = (is_object($meta->dpi())) ? $meta->dpi()->resolution : 0;
				$dimensions = getimagesize($filename);
				$widInches = ($resolution > 0) ? round((int)$dimensions[0] / (int)$resolution, 1) : "?";
				$heiInches = ($resolution > 0) ? round((int)$dimensions[1] / (int)$resolution, 1) : "?";
                                $resolution = ($resolution > 0) ? $resolution : "?";
			
				Ode_View::getInstance()->assign("imgWidth", $dimensions[0]);
				Ode_View::getInstance()->assign("imgHeight", $dimensions[1]);
				Ode_View::getInstance()->assign("imgWidthIn", $widInches);
				Ode_View::getInstance()->assign("imgHeightIn", $heiInches);
				Ode_View::getInstance()->assign("resolution", $resolution);
				Ode_View::getInstance()->assign("filesize", $filesize);
				Ode_View::getInstance()->assign("group", $group);
				Ode_View::getInstance()->assign("asset", $asset);
				Ode_View::getInstance()->assign("collection", $collection);
				break;
		}
		break;
	case 'preview':
            switch(Ode_Manager::getInstance()->getTask()) {
                default:
                    $asset = DBO_Asset::getOneByPublicId(trim($_GET['id']));
                    // grab the actual filename
                    $storageName = IMAGE_STORAGE_PATH.$asset->filename;

                    // set the copied filename, for watermarking
                    // get the basename for /tmp/ folder copying
                    $pathinfo = pathinfo($asset->filename);
                    $basename = $pathinfo['basename'];

                    $previewName = APP_UPLOAD_TMP_PATH.DIRECTORY_SEPARATOR.$basename;

                    //$execStr = stripslashes(APP_IMAGEMAGICK_PATH . DIRECTORY_SEPARATOR . 'convert "' . $storageName . '" -resize 1600x -resize "x1600<" -resize 50% -gravity center -crop 1000x1000+0+0 +repage "' . $previewName . '"');
                    $execStr = stripslashes(APP_IMAGEMAGICK_PATH . DIRECTORY_SEPARATOR . 'mogrify -resize 1024x1024 -gravity center -format jpg -quality 80 -path ' . APP_UPLOAD_TMP_PATH . ' ' . $storageName);

                    exec($execStr, $output);

                    try {
                        $download = new HTTP_Download();
                        $download->setFile($previewName, true);
                        $download->setBufferSize(1000 * 1024);
                        $download->setContentType("image/jpeg");
                        $download->setThrottleDelay(1); // 1 second
                        $download->send(true);
                    } catch(Exception $e) {
                        error_log($e->getTraceAsString(), 0);
                        header("Location: " . Ode_Manager::getInstance()->friendlyAction("asset_view", "preview", "failed"));
                        exit();
                    }

                    /**
                     * delete preview file from server
                     */
                    // uncommented because production server will not grant permissions to apache to delete image from /tmp directory, and causes
                    // script to freeze up.
                    unlink($previewName);
                    exit();
                    break;
                case 'failed':
                    
                    break;
		break;
            }
}
?>