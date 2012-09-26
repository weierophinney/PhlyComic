<?php
return array(
    'phly-comic' => array(
        'output_path'     => 'data/comics/',
        'comic_file'      => '%s.phtml',
        'all_comics_file' => 'comics.phtml',
    ),
    'console' => array('router' => array('routes' => array(
        'phly-comic-fetch-all' => array(
            'type' => 'Simple',
            'options' => array(
                'route' => 'phlycomic fetch all',
                'defaults' => array(
                    'controller' => 'PhlyComic\Fetch',
                    'action'     => 'all',
                ),
            ),
        ),
    ))),
);
