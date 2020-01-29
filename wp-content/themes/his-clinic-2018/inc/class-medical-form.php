<?php
class MedicalForm
{
    private $fields = [
        'date-of-birth' => null,
        'gender' => null,
        'symptoms-of-ed' => null,
        'advised-not-to-use' => null,
        'do-you-get-angina' => null,
        'had-a-heart-attack' => null,
        'had-a-stroke-or-tia' => null,
        'taking-any-nitrate-medications' => null,
    ];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->fields)) {
                $this->fields[$key] = $value;
            }
        }
    }

    public function get_serialized_data() {
        return json_encode($this->fields);
    }
}