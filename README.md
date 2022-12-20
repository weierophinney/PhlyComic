# PhlyComic: retrieve comic sources

This module/library is intended for retrieving URLs to comics found on the web
for purposes of linking and/or viewing. The comics remain the property of
respective copyright holders; I do not endorse retrieving the comics for
local storage, and request that if you create markup for displaying them
that you link to the originals.

## Requirements

- PHP >= 7.4.0
- symfony/console 5 or 6
- carica/phpcss 2
- spatie/async 1.5

## Usage

This library exposes the command phly-comic.php. It defines the following commands:

- `list-comics`, which will list all available comics
- `fetch comic [name]`, which allows you to fetch a single comic by name
- `fetch-all`, which will fetch all comics at once and write to a single file

Comic files are written by default to `data/comics/` of your application; you
can change this via the `-o|--output` option of each command.

Typical usage will look like this from your application:

```bash
% ./vendor/bin/phly-comic.php list-comics
```

```bash
% ./vendor/bin/phly-comic.php fetch nih
```

```bash
% ./vendor/bin/phly-comic.php fetch-all
```

## License

Copyright (c) , Matthew Weier O'Phinney
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
