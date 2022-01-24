<?php
require_once './init.php';

switch (Ode_Manager::getInstance()->getMode()) {
    default:

        break;
    case 'rights':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                break;
            case 'change':
                $asset_id = $_POST['asset_id'];
                $rights_id = $_POST['rights_id'];

                DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_RIGHTS, $rights_id, $asset_id, true);

                Util::json($_POST);
                exit();
                break;
            case 'batch':
                $asset_ids = $_POST['asset_ids'];
                $rights_id = $_POST['rights_id'];

                foreach ($asset_ids as $aid) {
                    DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_RIGHTS, $rights_id, $aid, true);
                }

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'org':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $asset = DBO_Asset::getOneById($_POST['asset_id']);
                $orgs = $asset->organizations();

                Ode_View::getInstance()->assign("asset_orgs", $orgs->getArrayCopy());

                echo Ode_View::getInstance()->fetch("ajax/assets/assign_org_list.tpl.php");
                exit();
                break;
            case 'add':
                DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_ORG_ID_NAME, $_POST['org_id'], $_POST['asset_id']);

                Util::json($_POST);
                exit();
                break;
            case 'str':
                $asset = DBO_Asset::getOneById($_POST['asset_id']);

                Util::json($asset->organizations("No organizations assigned."));
                exit();
                break;
            case 'rmv':
                DBO_Asset_Metadata::removeByValue(DBO_Asset_Metadata::META_ORG_ID_NAME, $_POST['asset_id'], $_POST['org_id']);

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'meta':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                break;
            case 'batch':
                $asset_ids = $_POST['asset_ids'];

                foreach ($asset_ids as $aid) {
                    $asset = DBO_Asset::getOneById($aid);

                    try {
                        $metadata = new Metadata_XMP(IMAGE_STORAGE_PATH . $asset->filename);
                        $written = $metadata->write($asset);
                    } catch (Exception $e) {
                        error_log($e->getTraceAsString(), 0);
                    }
                }

                Util::json($_POST);
                exit();
                break;
            case 'update':
                $asset = DBO_Asset::getOneById($_POST['asset']);

                try {
                    $metadata = new Metadata_XMP(IMAGE_STORAGE_PATH . $asset->filename);
                    $written = $metadata->write($asset);

                    Util::json($written);
                } catch (Exception $e) {
                    error_log($e->getMessage(), 0);
                    Util::json($e->getMessage());
                }
                exit();
                break;
            case 'count':
                $asset_id = $_POST['asset_id'];

                $meta_id = DBO_Asset_Metadata::exists(DBO_Asset_Metadata::META_NUM_VIEWS, $asset_id);

                if ($meta_id != false) {
                    // update
                    $cur_count = DBO_Asset_Metadata::get(DBO_Asset_Metadata::META_NUM_VIEWS, $asset_id, true);
                    $cur_count = (int)$cur_count->metadata_value;
                    $cur_count++;

                    $edit = DBO_Asset_Metadata::edit($meta_id, strval($cur_count));
                } else {
                    // create new metadata entry
                    $add = DBO_Asset_Metadata::add(DBO_Asset_Metadata::META_NUM_VIEWS, 1, $asset_id, true);
                }

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'tooltip':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $asset = DBO_Asset::getOneById($_POST['aid']);

                Ode_View::getInstance()->assign("asset", $asset);

                echo Ode_View::getInstance()->fetch("ajax/assetTooltip.tpl.php");
                exit();
                break;
        }
        break;
    case 'del':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                DBO_Asset::delete($_POST['aid']);

                Util::json($_POST);
                exit();
                break;
            case 'batch':
                $asset_ids = $_POST['asset_ids'];

                foreach ($asset_ids as $aid) {
                    DBO_Asset::delete($aid);
                }

                // don't forget to clear session's batch edit id's
                AssetManager::getInstance()->clear();

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'outtake':
        $json = new Services_JSON();

        $asset = DBO_Asset::getOneById($_POST['id']);

        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                if ($asset->isOuttake() == true) {
                    /**
                     * remove from outtake table
                     */
                    $sth = Ode_DBO::getInstance()->prepare("DELETE FROM asset_outtakes WHERE asset_id = :id");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();

                        $newAppr = 0;
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                } else {
                    /**
                     * add to outtake table
                     */
                    $sth = Ode_DBO::getInstance()->prepare("INSERT IGNORE INTO asset_outtakes (asset_id) VALUES (:asset_id)");
                    $sth->bindValue(":asset_id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();

                        $newAppr = 1;
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                }
                break;
            case 'batch':
                $asset_ids = $_POST['asset_ids'];
                $is_on = intval($_POST['is_on']);

                foreach ($asset_ids as $aid) {
                    if ($is_on === 1) {
                        DBO_Asset_Outtake::set($aid);
                    } else {
                        DBO_Asset_Outtake::un_set($aid);
                    }
                }

                Util::json($_POST);
                exit();
                break;
            case 'yes':
                /**
                 * add to outtake table
                 */
                $sth = Ode_DBO::getInstance()->prepare("INSERT IGNORE INTO asset_outtakes (asset_id) VALUES (:asset_id)");
                $sth->bindValue(":asset_id", $_POST['id'], PDO::PARAM_STR);

                try {
                    $sth->execute();

                    $newAppr = 1;
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }
                break;
            case 'no':
                /**
                 * remove from outtake table
                 */
                $sth = Ode_DBO::getInstance()->prepare("DELETE FROM asset_outtakes WHERE asset_id = :id");
                $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                try {
                    $sth->execute();

                    $newAppr = 0;
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }
                break;
        }

        header("Content-Type: application/json");
        echo $json->encode($newAppr);
        exit();
        break;
    case 'selects':
        $json = new Services_JSON();

        $asset = DBO_Asset::getOneById($_POST['id']);

        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                if ($asset->isSelect() == true) {
                    /**
                     * remove from outtake table
                     */
                    $sth = Ode_DBO::getInstance()->prepare("DELETE FROM asset_selects WHERE asset_id = :id");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();

                        $newAppr = 0;
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                } else {
                    /**
                     * add to outtake table
                     */
                    $sth = Ode_DBO::getInstance()->prepare("INSERT IGNORE INTO asset_selects (asset_id) VALUES (:asset_id)");
                    $sth->bindValue(":asset_id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();

                        $newAppr = 1;
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                }
                break;
            case 'yes':
                /**
                 * add to outtake table
                 */
                $sth = Ode_DBO::getInstance()->prepare("INSERT IGNORE INTO asset_selects (asset_id) VALUES (:asset_id)");
                $sth->bindValue(":asset_id", $_POST['id'], PDO::PARAM_STR);

                try {
                    $sth->execute();

                    $newAppr = 1;
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }
                break;
            case 'no':
                /**
                 * remove from outtake table
                 */
                $sth = Ode_DBO::getInstance()->prepare("DELETE FROM asset_selects WHERE asset_id = :id");
                $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                try {
                    $sth->execute();

                    $newAppr = 0;
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }
                break;
            case 'batch':
                $asset_ids = $_POST['asset_ids'];
                $is_on = intval($_POST['is_on']);

                foreach ($asset_ids as $aid) {
                    if ($is_on === 1) {
                        DBO_Asset_Select::set($aid);
                    } else {
                        DBO_Asset_Select::un_set($aid);
                    }
                }

                Util::json($_POST);
                exit();
                break;
        }

        header("Content-Type: application/json");
        echo $json->encode($newAppr);
        exit();
        break;
    case 'int':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $internal = DBO_Asset_Restriction_Internal::getOneByAsset($_POST['id']);

                if (empty($_POST['rsn']) && $internal != false) {
                    // restriction exists but no reason was supplied, so delete it
                    $sth = Ode_DBO::getInstance()->prepare("
                                                                            DELETE FROM " . DBO_Asset_Restriction_Internal::TABLE_NAME . " 
                                                                            WHERE asset_id = :id
                                                                    ");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 0;
                } else if (!empty($_POST['rsn']) && $internal != false) {
                    // update reson for existing restriction
                    $sth = Ode_DBO::getInstance()->prepare("
                                            UPDATE " . DBO_Asset_Restriction_Internal::TABLE_NAME . "
                                            SET
                                                    description = :desc,
                                                    user_id = :user,
                                                    created = NOW()
                                            WHERE asset_id = :asset
                                    ");
                    $sth->bindValue(":desc", trim($_POST['rsn']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 1;
                } else if (!empty($_POST['rsn']) && $internal == false) {
                    // create new restriction
                    $sth = Ode_DBO::getInstance()->prepare("
                                            INSERT INTO " . DBO_Asset_Restriction_Internal::TABLE_NAME . " (asset_id, description, user_id, created)
                                            VALUES (:asset, :desc, :user, NOW())
                                    ");
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);
                    $sth->bindValue(":desc", trim($_POST['rsn']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 1;
                }

                Util::json(array("formdata" => $_POST, "activity" => $activity));
                break;
        }
        break;
    case 'ext':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $external = DBO_Asset_Restriction_External::getOneByAsset($_POST['id']);

                if (empty($_POST['rsn']) && $external != false) {
                    // restriction exists but no reason was supplied, so delete it
                    $sth = Ode_DBO::getInstance()->prepare("
                                                                                    DELETE FROM " . DBO_Asset_Restriction_External::TABLE_NAME . " 
                                                                                    WHERE asset_id = :id
                                                                            ");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 0;
                } else if (!empty($_POST['rsn']) && $external != false) {
                    // update reson for existing restriction
                    $sth = Ode_DBO::getInstance()->prepare("
                                                    UPDATE " . DBO_Asset_Restriction_External::TABLE_NAME . "
                                                    SET
                                                            description = :desc,
                                                            user_id = :user,
                                                            created = NOW()
                                                    WHERE asset_id = :asset
                                            ");
                    $sth->bindValue(":desc", trim($_POST['rsn']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 1;
                } else if (!empty($_POST['rsn']) && $external == false) {
                    // create new restriction
                    $sth = Ode_DBO::getInstance()->prepare("
                                                    INSERT INTO " . DBO_Asset_Restriction_External::TABLE_NAME . " (asset_id, description, user_id, created)
                                                    VALUES (:asset, :desc, :user, NOW())
                                            ");
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);
                    $sth->bindValue(":desc", trim($_POST['rsn']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 1;
                }

                Util::json(array("formdata" => $_POST, "activity" => $activity));
                break;
        }
        break;
    case 'hippa':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $hippa = DBO_Asset_Restriction_Hippa::getOneByAsset($_POST['id']);

                if ($hippa == false) {
                    $sth = Ode_DBO::getInstance()->prepare("INSERT INTO " . DBO_Asset_Restriction_Hippa::TABLE_NAME . " (asset_id, user_id, created) VALUES (:asset, :user, NOW())");
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    Util::json(1);
                } else {
                    $sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . DBO_Asset_Restriction_Hippa::TABLE_NAME . " WHERE asset_id = :id");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                    Util::json(0);
                }
                exit();
                break;
        }
        break;
    case 'ncaa':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $ncaa = DBO_Asset_Restriction_NCAA::getOneByAsset($_POST['id']);

                if ($ncaa == false) {
                    $sth = Ode_DBO::getInstance()->prepare("INSERT INTO " . DBO_Asset_Restriction_NCAA::TABLE_NAME . " (asset_id, user_id, created) VALUES (:asset, :user, NOW())");
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    Util::json(1);
                } else {
                    $sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . DBO_Asset_Restriction_NCAA::TABLE_NAME . " WHERE asset_id = :id");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    Util::json(0);
                }

                exit();
                break;
        }
        break;
    case 'subj':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $subj = DBO_Asset_Restriction_Subject::getOneByAsset($_POST['id']);

                if (empty($_POST['rsn']) && $subj != false) {
                    // restriction exists but no reason was supplied, so delete it
                    $sth = Ode_DBO::getInstance()->prepare("
                                            DELETE FROM " . DBO_Asset_Restriction_Subject::TABLE_NAME . " 
                                            WHERE asset_id = :id
                                    ");
                    $sth->bindValue(":id", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 0;
                } else if (!empty($_POST['rsn']) && $subj != false) {
                    // update reson for existing restriction
                    $sth = Ode_DBO::getInstance()->prepare("
                                            UPDATE " . DBO_Asset_Restriction_Subject::TABLE_NAME . "
                                            SET
                                                    description = :desc,
                                                    user_id = :user,
                                                    created = NOW()
                                            WHERE asset_id = :asset
                                    ");
                    $sth->bindValue(":desc", trim($_POST['rsn']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 1;
                } else if (!empty($_POST['rsn']) && $subj == false) {
                    // create new restriction
                    $sth = Ode_DBO::getInstance()->prepare("
                                            INSERT INTO " . DBO_Asset_Restriction_Subject::TABLE_NAME . " (asset_id, description, user_id, created)
                                            VALUES (:asset, :desc, :user, NOW())
                                    ");
                    $sth->bindValue(":asset", $_POST['id'], PDO::PARAM_STR);
                    $sth->bindValue(":desc", trim($_POST['rsn']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $activity = 1;
                }

                Util::json(array("formdata" => $_POST, "activity" => $activity));
                break;
        }
        break;
    case 'embargo':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $embgo = DBO_Asset_Restriction_Embargo::getOneByAsset($_POST['id']);

                if (empty($_POST['_date'])) {
                    /**
                     * delete from embargo table
                     */
                    $sth = Ode_DBO::getInstance()->prepare("DELETE FROM " . DBO_Asset_Restriction_Embargo::TABLE_NAME . " WHERE id = :a");
                    $sth->bindValue(":a", $embgo->id, PDO::PARAM_INT);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                } else {
                    if ($embgo != false) {
                        /**
                         * update
                         */
                        $sth = Ode_DBO::getInstance()->prepare("
                                                    UPDATE " . DBO_Asset_Restriction_Embargo::TABLE_NAME . "
                                                    SET
                                                            start_date = :start_date,
                                                            created_by = :created_by,
                                                            created = NOW()
                                                    WHERE asset_id = :asset_id
                                            ");
                        $sth->bindValue(":start_date", date("Y-m-d H:i:s", strtotime(trim($_POST['_date']))), PDO::PARAM_STR);
                        $sth->bindValue(":created_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                        $sth->bindValue(":asset_id", $_POST['id'], PDO::PARAM_STR);

                        try {
                            $sth->execute();
                        } catch (PDOException $e) {
                            //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                            error_log($e->getMessage(), 0);
                        }
                    } else {
                        $sth = Ode_DBO::getInstance()->prepare("
                                                    INSERT INTO " . DBO_Asset_Restriction_Embargo::TABLE_NAME . " (asset_id, start_date, created_by)
                                                    VALUES (:a, :b, :c)
                                            ");
                        $sth->bindValue(":a", $_POST['id'], PDO::PARAM_STR);
                        $sth->bindValue(":b", date("Y-m-d H:i:s", strtotime(trim($_POST['_date']))), PDO::PARAM_STR);
                        $sth->bindValue(":c", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                        try {
                            $sth->execute();
                        } catch (PDOException $e) {
                            //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                            error_log($e->getMessage(), 0);
                        }
                    }
                }

                Util::json($embgo);
                exit();
                break;
            case 'del':

                break;
        }
        break;
    case 'pub':
        $json = new Services_JSON();

        $asset = DBO_Asset::getOneById($_POST['aid']);

        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                if ($asset->isPublished() == true) {
                    $sth = Ode_DBO::getInstance()->prepare("DELETE FROM asset_published WHERE asset_id = :id");
                    $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                    try {
                        $sth->execute();

                        $newAppr = 0;
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                } else {
                    $sth = Ode_DBO::getInstance()->prepare("INSERT IGNORE INTO asset_published (asset_id) VALUES (:asset_id)");
                    $sth->bindValue(":asset_id", $_POST['aid'], PDO::PARAM_STR);

                    try {
                        $sth->execute();

                        $newAppr = 1;
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                }
                break;
            case 'yes':
                DBO_Asset_Published::set($_POST['aid'], $_POST['pub_name'], $_POST['date']);

                Util::json($_POST);
                exit();
                break;
            case 'no':
                DBO_Asset_Published::un_set($_POST['aid']);

                Util::json($_POST);
                exit();
                break;
        }

        header("Content-Type: application/json");
        echo $json->encode($newAppr);
        exit();
        break;
    case 'edit':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                break;

            case 'copyright':
                DBO_Asset_Metadata::add(META_COPYRIGHT_NAME, $_POST['value'], $_POST['aid'], true);

                echo trim($_POST['value']);
                exit();
                break;

            case 'fname':
                $asset = DBO_Asset::getOneById($_POST['aid']);

                $newFilenameDB = $asset->filename;

                if (preg_match("/(\.\w{3,4})$/", trim($_POST['value']))) { // make sure there is an extension
                    $oldFilename = IMAGE_STORAGE_PATH . $asset->filename;

                    $oldPath = pathinfo($oldFilename);

                    $newFilename = IMAGE_STORAGE_PATH . trim($_POST['value']);

                    $newPath = pathinfo($newFilename);

                    $newFilename = str_replace($newPath['extension'], $oldPath['extension'], $newFilename);
                    $newFilenameDB = str_replace($newPath['extension'], $oldPath['extension'], trim($_POST['value']));

                    if (!file_exists($newFilename)) {
                        if (rename($oldFilename, $newFilename)) {
                            $sth = Ode_DBO::getInstance()->prepare("
                                                                                            UPDATE assets
                                                                                            SET
                                                                                                    filename = :filename,
                                                                                                    modified = NOW(),
                                                                                                    modified_by = :user
                                                                                            WHERE id = :id
                                                                                    ");
                            $sth->bindValue(":filename", $newFilenameDB, PDO::PARAM_STR);
                            $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                            $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                            try {
                                $sth->execute();
                            } catch (PDOException $e) {
                                //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                                error_log($e->getMessage(), 0);
                            }
                        }
                    }
                }

                echo $newFilenameDB;
                exit();

                break;
            case 'credit':
                $sth = Ode_DBO::getInstance()->prepare("
                            UPDATE " . DBO_Asset::TABLE_NAME . "
                            SET
                                credit = :credit,
                                modified_by = :user,
                                modified = NOW()
                            WHERE id = :id
                        ");
                $sth->bindValue(":credit", trim($_POST['value'], PDO::PARAM_STR));
                $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                echo trim($_POST['value']);
                exit();
                break;
            case 'created':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE assets
                                    SET
                                            created = :created,
                                            modified_by = :user,
                                            modified = NOW()
                                    WHERE id = :id
                            ");
                $sth->bindValue(":created", date("Y-m-d H:i:s", strtotime($_POST['_date'])), PDO::PARAM_STR);
                $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['_id'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                Util::json($_POST);
                exit();
                break;
            case 'caption':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE assets
                                    SET
                                            caption = :caption,
                                            modified = NOW(),
                                            modified_by = :modified_by
                                    WHERE id = :id
                            ");
                $sth->bindValue(":caption", $_POST['value'], PDO::PARAM_STR);
                $sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                echo trim($_POST['value']);
                break;
            case 'title':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE assets
                                    SET
                                            title = :title,
                                            modified = NOW(),
                                            modified_by = :modified_by
                                    WHERE id = :id
                            ");
                $sth->bindValue(":title", $_POST['value'], PDO::PARAM_STR);
                $sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                echo $_POST['value'];
                break;
            case 'desc':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE assets
                                    SET
                                            description = :desc,
                                            modified = NOW(),
                                            modified_by = :modified_by
                                    WHERE id = :id
                            ");
                $sth->bindValue(":desc", $_POST['value'], PDO::PARAM_STR);
                $sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                echo $_POST['value'];
                break;
            case 'credit':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE assets
                                    SET
                                            credit = :credit,
                                            modified = NOW(),
                                            modified_by = :modified_by
                                    WHERE id = :id
                            ");
                $sth->bindValue(":credit", $_POST['value'], PDO::PARAM_STR);
                $sth->bindValue(":modified_by", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                echo $_POST['value'];
                break;
            case 'phot':

                $photog = Ode_DBO::getInstance()->query("
                                    SELECT " . DBO_Photographer::SELECT_COLUMNS . "
                                    FROM " . DBO_Photographer::TABLE_NAME . " AS a
                                    WHERE a.firstname LIKE " . Ode_DBO::getInstance()->quote(trim($_POST['phFName']), PDO::PARAM_STR) . "
                                    AND a.lastname LIKE " . Ode_DBO::getInstance()->quote(trim($_POST['phLName']), PDO::PARAM_STR) . "
                                    LIMIT 0,1
                            ")->fetchObject(DBO_Photographer::MODEL_NAME);

                Ode_DBO::getInstance()->beginTransaction();

                if ($photog == false) { // add non-existent photographer to DB
                    $sth = Ode_DBO::getInstance()->prepare("
                                            INSERT INTO " . DBO_Photographer::TABLE_NAME . " (firstname, lastname, modified_by)
                                            VALUES (:fname, :lname, :user)
                                    ");
                    $sth->bindValue(":fname", trim($_POST['phFName']), PDO::PARAM_STR);
                    $sth->bindValue(":lname", trim($_POST['phLName']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }

                    $photogId = Ode_DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
                } else {
                    $photogId = $photog->id;
                }

                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE " . DBO_Asset::TABLE_NAME . "
                                    SET
                                            photographer_id = :pid,
                                            modified = NOW(),
                                            modified_by = :user
                                    WHERE id = :id
                            ");
                $sth->bindValue(":pid", $photogId, PDO::PARAM_INT);
                $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                $sth->bindValue(":id", $_POST['_id'], PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (PDOException $e) {
                    //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                    error_log($e->getMessage(), 0);
                }

                Ode_DBO::getInstance()->commit();

                $photog = DBO_Photographer::getOneById($photogId);

                Util::json(array("formdata" => $_POST, "photog_name" => $photog->fullname()));
                exit();
                break;
            case 'capn':
                $asset = DBO_Asset::getOneById($_POST['aid']);
                $caption = DBO_Caption::getOneByTypeAndAsset($_POST['aid'], $_POST['_type']);

                if ($caption != false) {
                    // update
                    $sth = Ode_DBO::getInstance()->prepare("
                                            UPDATE " . DBO_Caption::TABLE_NAME . "
                                            SET
                                                    caption = :caption,
                                                    modified_by = :user,
                                                    modified = NOW()
                                            WHERE id = :id
                                    ");
                    $sth->bindValue(":caption", trim($_POST['value']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);
                    $sth->bindValue(":id", $caption->id, PDO::PARAM_INT);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                } else {
                    $sth = Ode_DBO::getInstance()->prepare("
                                            INSERT INTO " . DBO_Caption::TABLE_NAME . " (type_id, asset_id, caption, modified_by, is_active, created, modified)
                                            VALUES (:type, :asset, :caption, :user, 1, NOW(), NOW())
                                    ");
                    $sth->bindValue(":type", DBO_Caption_Type::getIdFromName($_POST['_type']), PDO::PARAM_INT);
                    $sth->bindValue(":asset", $asset->id, PDO::PARAM_STR);
                    $sth->bindValue(":caption", trim($_POST['value']), PDO::PARAM_STR);
                    $sth->bindValue(":user", Ode_Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                }

                echo (!empty($_POST['value'])) ? trim($_POST['value']) : "No caption";
                exit();
                break;
        }
        break;
    case 'approval':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
            case 'yes':
                $approved = DBO_Asset::approve($_POST['asset']);

                Util::json(array("formadata" => $_POST, "approved" => $approved));
                exit();
                break;
            case 'no':
                $approved = DBO_Asset::approve($_POST['asset'], false);

                /**
                 * Reset lineitem approval, since the asset's publicity has been revoked
                 */
                DBO_Order_LineItem::approveByAsset($_POST['asset'], 0);

                Util::json(array("formadata" => $_POST, "approved" => $approved));
                exit();
                break;
            case 'batch':
                $asset_ids = $_POST['asset_ids'];
                $is_public = $_POST['is_public'];

                foreach ($asset_ids as $aid) {
                    if ($is_public == 1) {
                        DBO_Asset::approve($aid);
                    } else {
                        DBO_Asset::approve($aid, false);
                    }
                }

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'feature':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $asset = DBO_Asset::getOneById($_POST['aid']);

                /**
                 * Only if asset is public, then make it featured.
                 * We don't want admins mistakenly featuring non-public assets
                 */
                if ($asset->is_active == 1) {
                    $sth = Ode_DBO::getInstance()->prepare("
                                            UPDATE properties
                                            SET
                                                    value = :id,
                                                    modified = NOW()
                                            WHERE machine_name = 'featured_image'
                                    ");
                    $sth->bindValue(":id", $_POST['aid'], PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
                        error_log($e->getMessage(), 0);
                    }
                }

                Util::json(array("postdata" => $_POST, "isactive" => $asset->is_active));
                exit();
                break;
        }
        break;
    case 'loc':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $loc = Ode_DBO::getInstance()->query("
                                    SELECT a.lat, a.lng, a.location
                                    FROM " . DBO_Asset::TABLE_NAME . " AS a
                                    WHERE a.id = " . Ode_DBO::getInstance()->quote($_POST['asset_id'], PDO::PARAM_STR) . "
                                    LIMIT 0,1
                            ")->fetch(PDO::FETCH_ASSOC);

                if (!is_null($loc['lat'])) {
                    Util::json($loc);
                } else {
                    Util::json(false);
                }
                exit();
                break;
            case 'rmv':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE " . DBO_Asset::TABLE_NAME . "
                                    SET
                                            location = NULL,
                                            lat = NULL,
                                            lng = NULL
                                    WHERE id = :id
                            ");
                $sth->bindParam(":id", $_POST['asset_id'], PDO::PARAM_STR, 50);

                try {
                    $sth->execute();
                } catch (PDOExcpetion $e) {
                    //Ode_Error::getInstance()->mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
                    error_log($e->getMessage(), 0);
                } catch (Exception $e) {
                    //Ode_Error::getInstance()->mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
                    error_log($e->getMessage(), 0);
                }

                Util::json($_POST);
                exit();
                break;
            case 'edit':
                $sth = Ode_DBO::getInstance()->prepare("
                                    UPDATE " . DBO_Asset::TABLE_NAME . " 
                                    SET 
                                            location = :loc,
                                            lat = :lat,
                                            lng = :lng
                                    WHERE id = :id
                            ");
                $sth->bindParam(":loc", $_POST['loc'], PDO::PARAM_STR, 255);
                $sth->bindParam(":lat", $_POST['lat'], PDO::PARAM_STR, 25);
                $sth->bindParam(":lng", $_POST['lon'], PDO::PARAM_STR, 25);
                $sth->bindParam(":id", $_POST['asset_id'], PDO::PARAM_STR, 50);

                try {
                    $sth->execute();
                } catch (PDOExcpetion $e) {
                    //Ode_Error::getInstance()->mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
                    error_log($e->getMessage(), 0);
                } catch (Exception $e) {
                    Ode_Error::getInstance()->mail($e->getMessage(), __LINE__, __FILE__, APP_ADMIN_EMAIL);
                }

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'batch':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                break;
            case 'pub':
                $is_on = $_POST['is_on'];
                $asset_ids = $_POST['asset_ids'];

                if ($is_on == 1) {
                    $title = trim($_POST['title']);
                    $date = trim($_POST['date']);

                    foreach ($asset_ids as $asset_id) {
                        DBO_Asset_Published::set($asset_id, $title, $date);
                    }
                } else {
                    foreach ($asset_ids as $asset_id) {
                        DBO_Asset_Published::un_set($asset_id);
                    }
                }
                break;
            case 'pubbyid':
                $asset_ids = $_POST['asset_ids'];
                $pub_id = $_POST['pub_id'];

                foreach ($asset_ids as $asset_id) {
                    $sth = Ode_DBO::getInstance()->prepare("
                                            UPDATE " . DBO_Asset_Published::TABLE_NAME . "
                                            SET
                                                    pub_id = :pub_id
                                            WHERE asset_id = :asset_id
                                    ");
                    $sth->bindParam(":pub_id", $pub_id, PDO::PARAM_INT, 11);
                    $sth->bindParam(":asset_id", $asset_id, PDO::PARAM_STR, 50);

                    try {
                        $sth->execute();
                    } catch (PDOException $e) {
                        error_log($e->getTraceAsString(), 0);
                    } catch (Exception $e) {
                        error_log($e->getTraceAsString(), 0);
                    }
                }

                Util::json($_POST);
                exit();
                break;
        }
        break;
    case 'sess':
        switch (Ode_Manager::getInstance()->getTask()) {
            default:
                $asset_id = $_POST['asset_id'];
                $is_edit = intval($_POST['is_edit']);

                if ($is_edit > 0) {
                    $group_id = AssetManager::getInstance()->addToSession($asset_id);
                    // add metadata to flag group as being a batch and not a standard set
                    DBO_Group_Metadata::add(DBO_Group_Metadata::META_IS_BATCH_GROUP, 1, $group_id, true);
                } else {
                    $group_id = AssetManager::getInstance()->removeFromSession($asset_id);
                    // add metadata to flag group as being a batch and not a standard set
                    DBO_Group_Metadata::add(DBO_Group_Metadata::META_IS_BATCH_GROUP, 0, $group_id, true);
                }


                Util::json(array('_POST' => $_POST, 'temp_group' => AssetManager::getInstance()->getGroup()->id, 'cur_assets' => array_values(AssetManager::getInstance()->getEdits())));
                exit();
                break;
            case 'clear':
                AssetManager::getInstance()->clear();

                Util::json($_POST);
                exit();
                break;
        }
        break;
}
?>