<?php
class DBO_Organization_Metadata_Model {
    public $id;
    public $org_id;
    public $meta_name;
    public $meta_value;
    public $is_deleted;
    
    public function organization() {
        return DBO_Organization::getOneById($this->org_id);
    }
}
?>
