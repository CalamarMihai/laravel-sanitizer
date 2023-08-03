<?php

namespace calamar-mihai\Sanitizer\Filters;

use calamar-mihai\Sanitizer\Contracts\Filter;

class Trim implements Filter
{
    /**
     *  Trims the given string.
     *
     *  @param  string  $value
     *  @return string
     */
    public function apply($value, $options = [])
    {
        return is_string($value) ? trim($value) : $value;
    }
}
