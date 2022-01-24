<?php
class DBO_Group_Metadata_Model {
    public $id;
    public $group_id;
    public $meta_name;
    public $meta_value;
    public $title;
    public $is_deleted;

    public function group() {
        return DBO_Group::getOneById($this->group_id);
    }
}
?>