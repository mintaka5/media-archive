<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		
		break;
        case 'settings':
            switch(Ode_Manager::getInstance()->getTask()) {
                default:
                    
                    break;
            }
            break;
	case 'orders':
		switch (Ode_Manager::getInstance()->getTask()) {
			default:
				$sql = "SELECT " . DBO_Order::COLUMNS . "
					FROM " . DBO_Order::TABLE_NAME . " AS a
					WHERE a.user_id = " . Ode_DBO::getInstance()->quote(Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR) . "
					AND a.is_active = 0
					
					ORDER BY a.created
					DESC";
				//echo $sql;
				$orders = Ode_DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, DBO_Order::MODEL_NAME);
			
				Ode_View::getInstance()->assign("orders", $orders);
				break;
			case 'view':
				/**
				 * 
				 * Make sure only non-deleted orders are viewable
				 * @var DBO_Order
				 */
				$order = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Order::COLUMNS . "
					FROM " . DBO_Order::TABLE_NAME . " AS a
					WHERE a.id = " . Ode_DBO::getInstance()->quote(trim($_GET['id']), PDO::PARAM_STR) . "
					AND a.is_deleted = 0
				")->fetchObject(DBO_Order::MODEL_NAME);
				
				Ode_View::getInstance()->assign("order", $order);
				break;
				
            	case 'download':
                            $order = Ode_DBO::getInstance()->query("
                                    SELECT " . DBO_Order::COLUMNS . "
                                    FROM " . DBO_Order::TABLE_NAME . " AS a
                                    WHERE a.id = " . Ode_DBO::getInstance()->quote(trim($_GET['id']), PDO::PARAM_STR) . "
                                    AND a.is_deleted = 0
                            ")->fetchObject(DBO_Order::MODEL_NAME);

                            $zipFilename = APP_UPLOAD_TMP_PATH . DIRECTORY_SEPARATOR . $order->order_id . ".zip";

                            $zipper = new Archive_Zip($zipFilename);
                            $items = $order->lineitems();
                            $zipItems = array();
                            foreach ($items as $item) {
                                if($item->is_approved == 1) {
                                   $zipItems[] = IMAGE_STORAGE_PATH . $item->asset()->filename; 
                                }
                            }

                            if($zipper->create($zipItems, array('remove_path' => IMAGE_STORAGE_PATH, 'remove_all_path' => true))) {
                                $download = new HTTP_Download();
                                $download->setFile($zipFilename);
                                $download->setBufferSize(1000 * 1024);
                                $download->setThrottleDelay(1); // 1 second
                                
                                $download->send();
                                
                                // delete temp zip file -
                                // uncommented because production server will not grant permissions to apache to delete image from /tmp directory, and causes
                                // script to freeze up.
                                //unlink($zipFilename);
                            } else {
                                error_log("Zip archive creation failed: " . $zipper->errorInfo(true), 0);
                            }
                            break;
				case 'dl':
					$order = Ode_DBO::getInstance()->query("
							SELECT " . DBO_Order::COLUMNS . "
							FROM " . DBO_Order::TABLE_NAME . " AS a
							WHERE a.id = " . Ode_DBO::getInstance()->quote(trim($_GET['id']), PDO::PARAM_STR) . "
							AND a.is_deleted = 0
							")->fetchObject(DBO_Order::MODEL_NAME);
					
					$zipFilename = APP_UPLOAD_TMP_PATH . DIRECTORY_SEPARATOR . $order->order_id . ".zip";
					
					$zipper = new Archive_Zip($zipFilename);
					$items = $order->lineitems();
					$zipItems = array();
					foreach ($items as $item) {
						$zipItems[] = IMAGE_STORAGE_PATH . $item->asset()->filename;
					}
					
					if($zipper->create($zipItems, array('remove_path' => IMAGE_STORAGE_PATH, 'remove_all_path' => true))) {
						$download = new HTTP_Download();
						$download->setFile($zipFilename);
						$download->setBufferSize(1000 * 1024);
						$download->setThrottleDelay(1); // 1 second
					
						$download->send();
					
						// delete temp zip file
						//unlink($zipFilename);
					} else {
						error_log("Zip archive creation failed: " . $zipper->errorInfo(true), 0);
					}
					break;
		}
		break;
	case 'img':
		switch(Ode_Manager::getInstance()->getTask()) {
			default: break;
			case 'down':
				$asset = DBO_Asset::getOneByPublicId(trim($_GET['id']));
				
				$fileName = IMAGE_STORAGE_PATH . $asset->filename;
				$fileSize = filesize($fileName);
				
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				
				header("Content-Disposition: attachment; filename=".$asset->filename);
				header("Content-Type: ".$asset->type()->mime_type);
				header("Content-Length: ".$fileSize+1);
				header("Content-Transfer-Encoding: binary");
				@readfile($fileName);
				exit(0);
				break;
		}
		break;
}
?>