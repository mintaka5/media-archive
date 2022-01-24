<?php
class DBO_Group_Metadata {
    const TABLE_NAME = "group_meta";
    const MODEL_NAME = "DBO_Group_Metadata_Model";
    const META_ORG_ID_NAME = "org_id";
    const META_IS_BATCH_GROUP = 'is_batch_group';
    const COLUMNS = "a.id,a.group_id,a.meta_name,a.meta_value,a.is_deleted";

    public static function valueExists($meta_name, $meta_value, $group_id) {
            $metadata = Ode_DBO::getInstance()->query("
                    SELECT a.id
                    FROM " . self::TABLE_NAME . " AS a
                    WHERE a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
                    AND a.is_deleted = 0
                    AND a.meta_name = " . Ode_DBO::getInstance()->quote($meta_name, PDO::PARAM_STR). "
                    AND a.meta_value = " . Ode_DBO::getInstance()->quote($meta_value, PDO::PARAM_STR) . "
            ")->fetchColumn();

            if($metadata != false) {
                    return $metadata;
            }

            return false;
    }

    public static function add($meta_name, $meta_value, $group_id, $is_unique = false) {
            $exists = self::exists($meta_name, $group_id);

            if($exists != false && $is_unique == true) {
                    self::edit($exists, $meta_value);
            } else {
                    $sth = Ode_DBO::getInstance()->prepare("
                            INSERT INTO " . self::TABLE_NAME . " (group_id, meta_name, meta_value, is_deleted)
                            VALUES (:group_id, :meta_name, :meta_value, 0)
                    ");
                    $sth->bindParam(":group_id", $group_id, PDO::PARAM_STR, 50);
                    $sth->bindParam(":meta_name", $meta_name, PDO::PARAM_STR, 45);
                    $sth->bindParam(":meta_value", $meta_value, PDO::PARAM_STR, 255);

                    try {
                            $sth->execute();
                    } catch(PDOException $e) {
                            error_log($e->getMessage(), 0);
                    } catch(PDOException $e) {
                            error_log($e->getMessage(), 0);
                    }
            }

            return true;
    }

    public static function exists($name, $group_id) {
            $metadata = Ode_DBO::getInstance()->query("
                    SELECT a.id
                    FROM " . self::TABLE_NAME . " AS a
                    WHERE a.is_deleted = 0
                    AND a.meta_name = " . Ode_DBO::getInstance()->quote($name, PDO::PARAM_STR) . "
                    AND a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
            ")->fetchColumn();

            if($metadata != false) {
                    return $metadata;
            }

            return false;
    }

    public static function edit($id, $value) {
            $sth = Ode_DBO::getInstance()->prepare("UPDATE " . self::TABLE_NAME . " SET meta_value = :value WHERE id = :id");
            $sth->bindParam(":value", $value, PDO::PARAM_STR, 255);
            $sth->bindParam(":id", $id, PDO::PARAM_INT, 11);

            try {
                    $sth->execute();
            } catch(PDOException $e) {
                    error_log($e->getMessage(), 0);
            } catch(PDOException $e) {
                    error_log($e->getMessage(), 0);
            }

            return;
    }

    public static function getAll($group_id) {
            return Ode_DBO::getInstance()->query("
                    SELECT " . self::COLUMNS . "
                    FROM " . self::TABLE_NAME . " AS a
                    WHERE a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
            ")->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function get($meta_name, $group_id, $is_single = true) {
            $q = Ode_DBO::getInstance()->query("
                    SELECT " . self::COLUMNS . "
                    FROM " . self::TABLE_NAME . " AS a
                    WHERE a.meta_name = " . Ode_DBO::getInstance()->quote($meta_name, PDO::PARAM_STR) . "
                    AND a.group_id = " . Ode_DBO::getInstance()->quote($group_id, PDO::PARAM_STR) . "
                    AND a.is_deleted = 0
            ");

            if($is_single == true) {
                    return $q->fetchObject(self::MODEL_NAME);
            } else {
                    return $q->fetchAll(PDO::FETCH_CLASS, self::MODEL_NAME);
            }

            return false;
    }

    public static function delete($meta_name, $meta_value, $group_id) {
            $sth = Ode_DBO::getInstance()->prepare("
                    DELETE FROM " . self::TABLE_NAME . "
                    WHERE group_id = :group_id
                    AND meta_name = :meta_name
                    AND meta_value = :meta_value
            ");
            $sth->bindParam(":group_id", $group_id, PDO::PARAM_STR, 50);
            $sth->bindParam(":meta_name", $meta_name, PDO::PARAM_STR, 45);
            $sth->bindParam(":meta_value", $meta_value, PDO::PARAM_STR);

            try {
                    $sth->execute();
            } catch(PDOException $e) {
                    error_log($e->getMessage(), 0);
            } catch(PDOException $e) {
                    error_log($e->getMessage(), 0);
            }

            return true;
    }
}
?>