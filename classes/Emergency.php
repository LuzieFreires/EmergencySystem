<?php
class Emergency {
    private $id;
    private $type;
    private $location;
    private $description;
    private $reported_by;
    private $status;
    private $created_at;
    private $updated_at;

    public function __construct($type, $location, $description, $reported_by) {
        $this->type = $type;
        $this->location = $location;
        $this->description = $description;
        $this->reported_by = $reported_by;
        $this->status = 'pending';
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function updateStatus($status) {
        $this->status = $status;
        $this->updated_at = date('Y-m-d H:i:s');
    }
}