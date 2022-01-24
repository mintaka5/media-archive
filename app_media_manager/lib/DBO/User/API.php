<?php
class DBO_User_API {
    const TABLE_NAME = "user_api";
    const MODEL_NAME = "DBO_User_API_Model";
    const COLUMNS = "a.id, a.user_id, a.api_key, a.secret";
    
    public static function getOneByUser($user_id) {
        return Ode_DBO::getInstance()->query("
            SELECT " . self::COLUMNS . "
            FROM " . self::TABLE_NAME . " AS a
            WHERE a.user_id = " . Ode_DBO::getInstance()->quote($user_id, PDO::PARAM_STR) . "
            LIMIT 0,1
        ")->fetchObject(self::MODEL_NAME);
    }
    
    public static function getOneByUsername($username) {
        return Ode_DBO::getInstance()->query("
            SELECT " . self::COLUMNS . "
            FROM " . self::TABLE_NAME . " AS a
            LEFT JOIN " . DBO_User::TABLE_NAME . " AS b ON (b.id = a.user_id)
            WHERE b.username = " . Ode_DBO::getInstance()->quote($username, PDO::PARAM_STR) . "
            LIMIT 0,1
        ")->fetchObject(self::MODEL_NAME);
    }
    
    public static function create($user_id, $secret, $hash) {
        if(self::exists($user_id) == true) {
           self::update($user_id, $secret, $hash); 
        } else {
            self::insert($user_id, $secret, $hash);
        }
    }
    
    private static function insert($user_id, $secret, $hash) {
        $sth = Ode_DBO::getInstance()->prepare("
            INSERT INTO " . self::TABLE_NAME . " (user_id, api_key, secret)
            VALUES (:user, :key, :secret)
        ");
        $sth->bindValue(":user", $user_id, PDO::PARAM_STR);
        $sth->bindValue(":key", $secret, PDO::PARAM_STR);
        $sth->bindValue(":secret", $hash, PDO::PARAM_STR);
        
        try {
            $sth->execute();
        } catch(PDOException $e) {
            //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
        }
    }
    
    private static function update($user_id, $secret, $hash) {
        $sth = Ode_DBO::getInstance()->prepare("
            UPDATE " . self::TABLE_NAME . "
            SET
                api_key = :key,
                secret = :secret
            WHERE user_id = :user
        ");
        $sth->bindValue(":user", $user_id, PDO::PARAM_STR);
        $sth->bindValue(":key", $secret, PDO::PARAM_STR);
        $sth->bindValue(":secret", $hash, PDO::PARAM_STR);
        
        try {
            $sth->execute();
        } catch(PDOException $e) {
            //Ode_Log::getInstance()->log($e->getTraceAsString(), E_USER_ERROR);
            error_log($e->getMessage(), 0);
        }
    }


    private static function exists($user_id) {
        $api = self::getOneByUser($user_id);
        
        if($api != false) {
            return true;
        }
        
        return false;
    }
}
?>
