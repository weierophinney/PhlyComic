PhlyComic: retrieve comic sources
====

This module/library is intended for retrieving URLs to comics found on the web
for purposes of linking and/or viewing. The comics remain the property of
respective copyright holders; I do not endorse retrieving the comics for
local storage, and request that if you create markup for displaying them
that you link to the originals.

Requirements
----

* PHP >= 5.3.3
* Zend Framework 2 >= 2.0.0beta1, specifically: 
  * Zend\Console\Getopt, which is used for the console scripts
  * Zend\Dom\Query, used for some web scraping
  * Zend\Module, but only if you want to integrate this into a ZF2 MVC
    application

Usage
----

Typical usage will be to simply use the `get_comics.php` and `get_one_comic.php`
scripts. They make the following assumptions:

* They are being called by another script that sets up one or more autoloaders,
  including functionality to autoload the code in this library, and to autoload
  Zend\Console\Getopt.

A sample script might look like this:

    <?php
    require_once 'Zend_Console-2.0.0beta3.phar';
    require_once 'Zend_Dom-2.0.0beta3.phar';
    include 'path/to/PhlyComic/autoload_register.php';
    include 'path/to/PhlyComic/bin/get_comics.php';
    
### get_one_comic.php

    Usage: get_one_comic.php [ options ]
    --help|-h Get a usage message
    --comic|-c [<string>] Comic to retrieve
    --list|-l  List comics available

This will spit out HTML markup for the comic, including a link to the comic
homepage, and a linked image for the current comic available.

### get_comics.php

    Usage: get_comics.php

This will spit out HTML markup for all available comics, including a link to
each comic's homepage, and a linked image for the current comic available.

License
----

Copyright (c) 2012, Matthew Weier O'Phinney
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice, this
  list of conditions and the following disclaimer in the documentation and/or
  other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
