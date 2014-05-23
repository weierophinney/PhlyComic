<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use PhlyComic\ComicFactory;
use Zend\Filter\Callback as CallbackFilter;
use Zend\Validator\Callback as CallbackValidator;

$outputValidator = new CallbackValidator(array(
    'callback' => function ($value) {
        if (! is_dir(dirname($value))) {
            printf("Output directory '%s' does not exist%s", dirname($value), str_repeat(PHP_EOL, 2));
            return false;
        }

        if (! is_writable(dirname($value))) {
            printf("Output directory '%s' is not writable%s", dirname($value), str_repeat(PHP_EOL, 2));
            return false;
        }

        return true;
    },
    'message' => 'Output path does not exist or is not writable.',
));

$comicValidator = new CallbackValidator(array(
    'callback' => function ($value) {
        $comics = ComicFactory::getSupported();
        $supported = in_array($value, array_keys($comics));
        if (! $supported) {
            printf("Comic '%s' is not supported%s", $value, str_repeat(PHP_EOL, 2));
        }
        return $supported;
    },
    'message' => 'Requested comic is not supported.',
));

return array(
    array(
        'name' => 'list',
        'route' => 'list',
        'description' => 'Lists all comics that PhlyComic is capable of fetching, providing both the short name (used to fetch individual comics) and the full name.',
        'short_description' => 'List all available comics',
    ),
    array(
        'name' => 'fetch-all',
        'route' => 'fetch-all [--output=]',
        'description' => 'Fetches the named <comic> and writes an HTML file to the provided path; defaults to data/<comic>.html',
        'short_description' => 'Fetch all comics',
        'options_descriptions' => array(
            '--output' => 'Path to which the HTML for the list of comic should be written',
        ),
        'defaults' => array(
            'output' => 'data/comics/comics.html',
        ),
        'filters' => array(
            'output' => new CallbackFilter(function ($value) {
                if (! $value) {
                    return 'data/comics/comics.html';
                }
                return $value;
            }),
        ),
        'validators' => array(
            'output' => $outputValidator,
        ),
    ),
    array(
        'name' => 'fetch',
        'route' => 'fetch <comic> [--output=]',
        'description' => 'Fetches the named <comic> and writes an HTML file to the provided path; defaults to data/comics/<comic>.html',
        'short_description' => 'Fetch a single comic',
        'options_descriptions' => array(
            '<comic>' => 'Name (alias) of the comic to fetch',
            '--output' => 'Path to which the HTML for the comic should be written',
        ),
        'constraints' => array(
            'comic' => '/^[a-z0-9_-]+$/i',
        ),
        'validators' => array(
            'comic'  => $comicValidator,
            'output' => $outputValidator,
        ),
    ),
);
