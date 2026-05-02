<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

//use Laravel\Mcp\Facades\Mcp;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Tool to read a specific file
/* Mcp::tool('read_file', 'Read the content of a file in the project')
    ->parameter('path', 'The relative path to the file')
    ->handle(fn ($path) => file_get_contents(base_path($path))); */

// Tool to write/fix a file
/* Mcp::tool('update_file', 'Overwrites a file with new code')
    ->parameter('path', 'The relative path')
    ->parameter('content', 'The full new content of the file')
    ->handle(fn ($path, $content) => file_put_contents(base_path($path), $content)); */

// Tool to run tests
/* Mcp::tool('run_tests', 'Runs phpunit or pest to check for bugs')
    ->handle(fn () => shell_exec('php artisan test --json')); */
