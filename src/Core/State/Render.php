<?php

namespace App\Core\State;

use App\Core\State;

class Render extends State
{
    /**
     * @var mixed any data to display
     */
    public $data;

    public function __construct($data, $code = 200) {
        $this->data = $data;

        parent::__construct("Force display", $code);
    }
}
