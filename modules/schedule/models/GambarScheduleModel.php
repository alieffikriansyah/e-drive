<?php

class GambarScheduleModel {
    public $id;
    public $schedule_calendars_id;
    public $nama_file;
    public $status;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray() {
        return get_object_vars($this);
    }
}
