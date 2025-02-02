<?php

namespace BWS\Sanitizer\Filters;

use BWS\Sanitizer\Contracts\Filter;

class FilterIf implements Filter
{
    /**
     *  Checks if filters should run if there is value passed that matches.
     *
     *  @param  array   $values
     *  @param  array   $options
     *  @return bool
     */
    public function apply($values, $options = [])
    {
        return array_key_exists($options[0], $values) && $values[$options[0]] === $options[1];
    }
}
