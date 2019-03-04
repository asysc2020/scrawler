# Scrawler
Scrawler is a declarative, scriptable web robot (crawler) and scrapper which
you can easily configure to parse any website and process the information into
the desired format.

Configuration is based on the building _blocks_, for which you can provide your
own implementations allowing for further customization of the process.

## Usage
As usual, start by installing the library with Composer:

```bash
composer require sobak/scrawler
```

```php
<?php

use App\PostEntity;
use Sobak\Scrawler\Block\FieldDefinition\StringField;
use Sobak\Scrawler\Block\ResultWriter\FilenameProvider\EntityPropertyFilenameProvider;
use Sobak\Scrawler\Block\ResultWriter\JsonFileResultWriter;
use Sobak\Scrawler\Block\UrlListProvider\ArgumentAdvancerUrlListProvider;
use Sobak\Scrawler\Configuration\Configuration;
use Sobak\Scrawler\Configuration\ObjectConfiguration;
use Sobak\Scrawler\Matcher\CssSelectorListMatcher;
use Sobak\Scrawler\Matcher\CssSelectorTextMatcher;

require 'vendor/autoload.php';

$scrawler = new Configuration();

$scrawler
    ->setOperationName('Sobakowy Blog')
    ->setBaseUrl('htt://sobak.pl')
    ->addUrlListProvider(new ArgumentAdvancerUrlListProvider('http://sobak.pl/page/%u', 2, 1, 17))
    ->addObjectDefinition('post', new CssSelectorListMatcher('article.hentry'), function (ObjectConfiguration $object) {
        $object
            ->addFieldDefinition('date', new StringField(new CssSelectorTextMatcher('time.entry-date')))
            ->addFieldDefinition('content', new StringField(new CssSelectorTextMatcher('div.entry-content')))
            ->addFieldDefinition('title', new StringField(new CssSelectorTextMatcher('h1.entry-title')))
            ->addEntityMapping(PostEntity::class)
            ->addResultWriter(PostEntity::class, new JsonFileResultWriter([
                'directory' => 'posts/',
                'filename' => new EntityPropertyFilenameProvider([
                    'property' => 'slug',
                ]),
            ]))
        ;
        })
;

return $scrawler;
```

After saving the configuration file all you have to do is execute this command:

```bash
php vendor/bin/scrawler crawl
```

The example shown above will fetch [http://sobak.pl]() page, then it will iterate
over post pages starting from 2nd up to 17th, get all posts on each page, map them
to `App\PostEntity` objects and finally write the results down to individual JSON 
files using post slugs as filenames.

As you can see with this short code, almost half of it being the imports,
you can easily achieve quite tedious task for which you would otherwise need
to get a few libraries, define rules to follow, provide correct map to write
down the file... Scrawler does it all for you!

## Documentation

> **Note:** some parts of the documentation are still under construction.

For the detailed documentation please check the table of contents below.

- [Blocks](docs/blocks.md)
- [Changelog](CHANGELOG.md)
- [Roadmap](docs/roadmap.md)

The most interesting section is probably "Blocks". _Block_ in Scrawler is an
abstracted, swappable piece of logic defining the crawling, scrapping or result
processing operations which you can customize using one of many builtin classes
or even your own, tailored implementation. Looking at the example above, you
could provide custom logic for `UrlListProvider` or `ResultWriter` (just
examples for many of the available block types).

> **Note:** I have to admit I am not a fan of excessive DocBlocks usage.
> That's why documentation in the code is sparse and focuses mainly
> on interfaces, especially ones for creating custom implementation
> of blocks. Use the documentation linked above and obviously read the
> code.

## Don't be a dick
Before you start tinkering with a library, please remember: some people do not want
their websites to be scrapped by bots. With growing percentage of bandwidth being
caused by bots it might not only be considered problematic from the business
standpoint but also expensive to handle all that traffic. Please respect that.
Even though Scrawler provides implementations for some blocks, which might be useful
to mimic the actual internet user, you should not use them to bypass security
measures taken by website owners.

> **Note:** For the testing purposes you can freely crawl [my website](http://sobak.pl),
> _excluding_ its subdomains. Just please leave the default user agent.

## License
Scrawler is distributed under the MIT license. For the details please check the
dedicated [LICENSE](LICENSE.md) file.

## Contributing
For the details on how to contribute please check the dedicated
[CONTRIBUTING](CONTRIBUTING.md) file.
