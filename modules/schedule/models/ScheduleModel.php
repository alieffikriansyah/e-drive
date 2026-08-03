<?php

class ScheduleModel {
    public $id;
    public $user_id;
    public $attachment_document_id;
    public $title;
    public $description;
    public $location;
    public $category;
    public $priority;
    public $start_date;
    public $end_date;
    public $start_time;
    public $end_time;
    public $all_day;
    public $color;
    public $reminder_minutes;
    public $repeat_type;
    public $repeat_until;
    public $is_completed;
    public $completed_at;
    public $visibility;
    public $status;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    // Optional relation properties
    public $owner_name;
    public $attachment_name;

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
