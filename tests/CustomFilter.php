<?php

use calamar-mihai\Sanitizer\Contracts\Filter;

class CustomFilter implements Filter
{
    public function apply($value, $options = [])
    {
        return $value.$value;
    }
}
