<?php

use BWS\Sanitizer\Laravel\Factory;
use BWS\Sanitizer\Sanitizer;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function sanitize($data, $rules)
    {
        $sanitizer = new Sanitizer($data, $rules, [
            'capitalize'  => \BWS\Sanitizer\Filters\Capitalize::class,
            'escape'      => \BWS\Sanitizer\Filters\EscapeHTML::class,
            'format_date' => \BWS\Sanitizer\Filters\FormatDate::class,
            'lowercase'   => \BWS\Sanitizer\Filters\Lowercase::class,
            'uppercase'   => \BWS\Sanitizer\Filters\Uppercase::class,
            'trim'        => \BWS\Sanitizer\Filters\Trim::class,
        ]);

        return $sanitizer->sanitize();
    }

    public function test_custom_closure_filter()
    {
        $factory = new Factory;
        $factory->extend('hash', function ($value) {
            return sha1($value);
        });

        $data = [
            'name' => 'TEST',
        ];
        $rules = [
            'name' => 'hash',
        ];

        $newData = $factory->make($data, $rules)->sanitize();
        $this->assertEquals(sha1('TEST'), $newData['name']);
    }

    public function test_custom_class_filter()
    {
        $factory = new Factory;
        $factory->extend('custom', CustomFilter::class);

        $data = [
            'name' => 'TEST',
        ];
        $rules = [
            'name' => 'custom',
        ];

        $newData = $factory->make($data, $rules)->sanitize();
        $this->assertEquals('TESTTEST', $newData['name']);
    }

    public function test_replace_filter()
    {
        $factory = new Factory;
        $factory->extend('trim', function ($value) {
            return sha1($value);
        });

        $data = [
            'name' => 'TEST',
        ];
        $rules = [
            'name' => 'trim',
        ];

        $newData = $factory->make($data, $rules)->sanitize();
        $this->assertEquals(sha1('TEST'), $newData['name']);
    }
}
