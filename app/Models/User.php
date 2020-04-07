<?php

namespace App\Models;

class User
{
    public int $id;
    public $type;

    public function __construct(int $id, $type)
    {
        $this->id = $id;
        $this->type = $type;
    }
}
