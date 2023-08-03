<?php

namespace calamar-mihai\Sanitizer\Filters;

use calamar-mihai\Sanitizer\Contracts\Filter;

class Uppercase implements Filter
{
    /**
     *  Lowercase the given string.
     *
     *  @param  string  $value
     *  @return string
     */
    public function apply($value, $options = [])
    {
        return is_string($value) ? mb_strtoupper($value) : $value;
    }
}
