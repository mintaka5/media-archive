<?php
switch(Ode_Manager::getInstance()->getMode()) {
	default:
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$items = Ode_DBO::getInstance()->query("
					SELECT " . DBO_Order_LineItem::COLUMNS . "
					FROM " . DBO_Order_LineItem::TABLE_NAME . " AS a
					WHERE a.order_id = " . Ode_DBO::getInstance()->quote(Order::getInstance()->getOrderId(), PDO::PARAM_STR) . "
					ORDER BY a.created
					DESC
				")->fetchAll(PDO::FETCH_CLASS, DBO_Order_LineItem::MODEL_NAME);
			
				Ode_View::getInstance()->assign("items", $items);
				break;
		}
		break;
	case 'request':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
				$orderId = Order::getInstance()->getOrderId();
				
				/**
				 * Update order date to now!
				 */
				$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Order::TABLE_NAME . " SET created = NOW() WHERE id = :id");
				$sth->bindParam(":id", $orderId, PDO::PARAM_STR, 50);
				
				try {
					$sth->execute();
				} catch(PDOException $e) {
					error_log($e->getMessage(), 1, APP_ADMIN_EMAIL);
				} catch(Exception $e) {
					error_log($e->getMessage(), 1, APP_ADMIN_EMAIL);
				}
				
				/**
				 * Set order to non-active
				 * 
				 */
				DBO_Order::deactivate($orderId);
				
				/**
				 * Clear order from session
				 * 
				 */
				Order::getInstance()->removeFromSession();
				
				/**
				 * Send admins an email for the request
				 */
				//$admin = DBO_User::getOneByUsername("walshcj");
				
				$admins = DBO_User::getAllByTypeName(DBO_User_Model::ADMIN_TYPE);
				
				foreach($admins as $admin) {
					$mail = new UCI_Mailer();
					$mail->setFrom(Ode_Auth::getInstance()->getSession()->email, Ode_Auth::getInstance()->getSession()->fullname());
					$mail->addTo($admin->email, $admin->fullname());
					$mail->setSubject("Images request from " . Ode_Auth::getInstance()->getSession()->fullname());
					
					Ode_View::getInstance()->assign("user", Ode_Auth::getInstance()->getSession());
					Ode_View::getInstance()->assign("uri", BASE_URL);
					Ode_View::getInstance()->assign("order", DBO_Order::getOneById($orderId));
					$mail->setHTMLBody(Ode_View::getInstance()->fetch("mail/adminImageRequest.tpl.php"));
					
					try {
						$mail->send();
					} catch (Exception $e) {
						//Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
					}
				}
				
				/**
				 * Redirect to user's order history, and supply a switch for successful request
				 */
				header("Location: ".Ode_Manager::getInstance()->action("account", "orders", null, array("from_req", 1)));
				exit();
				break;
		}
		break;
	case 'download':
		switch(Ode_Manager::getInstance()->getTask()) {
			default:
			case false:
				$order = Ode_DBO::getInstance()->query("
						SELECT " . DBO_Order::COLUMNS . "
						FROM " . DBO_Order::TABLE_NAME . " AS a
						WHERE a.id = " . Ode_DBO::getInstance()->quote(trim($_GET['id']), PDO::PARAM_STR) . "
						AND a.is_deleted = 0
						")->fetchObject(DBO_Order::MODEL_NAME);
				
				/**
				 * Update order date to now!
				 */
				$sth = Ode_DBO::getInstance()->prepare("UPDATE " . DBO_Order::TABLE_NAME . " SET created = NOW() WHERE id = :id");
				$sth->bindParam(":id", $order->id, PDO::PARAM_STR, 50);
				
				/**
				 * create zip filename
				 * @var string
				 */
				$zipFilename = APP_UPLOAD_TMP_PATH . DIRECTORY_SEPARATOR . $order->order_id . ".zip";
				
				$zipper = new Archive_Zip($zipFilename);
				$items = $order->lineitems();
				$zipItems = array();
				foreach ($items as $item) {
					$zipItems[] = IMAGE_STORAGE_PATH . $item->asset()->filename;
				}
				
				/**
				 * Set order to non-active
				 *
				 */
				DBO_Order::deactivate($order->id);
					
				/**
				 * Clear order from session
				 *
				 */
				Order::getInstance()->removeFromSession();
				
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
					error_log("Zip archive creation failed: " . $zipper->errorInfo(true), 1, APP_ADMIN_EMAIL);
				}
				break;
		}
		break;
}
?>