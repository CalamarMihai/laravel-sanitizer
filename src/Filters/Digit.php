<?php

namespace calamar-mihai\Sanitizer\Filters;

use calamar-mihai\Sanitizer\Contracts\Filter;

class Digit implements Filter
{
    /**
     *  Get only digit characters from the string.
     *
     *  @param  string  $value
     *  @return string
     */
    public function apply($value, $options = [])
    {
        return preg_replace('/[^0-9]/si', '', $value);
    }
}
