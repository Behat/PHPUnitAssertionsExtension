# Behat/PHPUnitAssertionsExtension

This extension for provides support for using PHPUnit assertions within your [Behat](https://behat.org)
step definitions. Since Behat 4.0, Behat core does not provide any built-in support for PHPUnit assertions.

> [!CAUTION]
> PHPUnit's author has [explicitly stated](https://github.com/sebastianbergmann/phpunit/issues/6175#issuecomment-2782498694)
> that using PHPUnit assertions outside of PHPUnit itself is not supported or covered by any backwards
> compatibility promise. Therefore we **strongly recommend** that you
> [use a different assertion tool]([url](https://docs.behat.org/en/latest/useful_resources.html#assertion-tools))
> in new projects. Read the important information below before using in an existing project.

[![License](https://poser.pugx.org/behat/PHPUnitAssertionsExtension/license.svg)](https://packagist.org/packages/behat/phpunitassertionsextension)
[![Build Status](https://github.com/Behat/PHPUnitAssertionsExtension/workflows/Build/badge.svg)](https://github.com/Behat/PHPUnitAssertionsExtension/actions?query=workflow%3ABuild)

## What does the extension do?

### Bootstraps PHPUnit (in versions 11+)

Since v11.3.0, PHPUnit expects various internal global state to be initialised before making
assertions. A PHP Error may be triggered if this has not happened - either during the `Assert::`
call itself, or when reading the failure details. The type and timing of the Error depends on
the PHP configuration, the type of assertion, and the arguments that were asserted.

We fake just enough of PHPUnit's bootstrapping to allow the assertion methods to work. We do this
in the first `BeforeSuite` event, to cover assertions made anywhere in end-user code.

### Renders details of failing assertions

PHPUnit assertion exceptions do not include detailed expected / observed info in the exception
message itself. Instead, test result printers within PHPUnit are expected to format and present
that information separately.

We make a best effort to render as much detail of a PHPUnit assertion failure as we can, so that
the Behat failure report includes e.g. the diff between expected and actual values.

## What do I need to be aware of?

As above, **PHPUnit explicitly does not support using PHPUnit assertions outside their own runner**. 

This extension depends on internal implementation details that are not covered by PHPUnit's
backwards compatibility promise.

Therefore:

* We cannot guarantee that this will work, or produce the same output, even across minor
  PHPUnit versions.
* We will do our best to update the extension to support future PHPUnit releases, but we
  cannot guarantee that this will be possible.

That said, historically PHPUnit's internals have been relatively stable for a given major
version series.

### Potential failure modes

There are three ways your build could break with a new PHPUnit release:

* This extension could fail to bootstrap PHPUnit, causing the BeforeSuite hook (and therefore
  the build) to fail without running scenarios.
* An `Assert::something` call could throw an exception even if the assertion should have passed.
* An `Assert::something` call could fail correctly, but show as an internal / generic error in
  the build output without the actual detail of which assertion failed, or why.

We think the first two failure cases (build fails when it should have passed) are relatively 
unlikely, and you will see them as soon as you bump to the new release.

The third case is potentially more common, and you probably won't see it as soon as you upgrade
PHPUnit, unless that build actually has a failing assertion. However, the impact is much smaller
- the build should genuinely have failed, it may just be harder to debug why that happened.

We **strongly recommend** that you pin to a specific version of phpunit - by committing 
`composer.lock`, or by requiring e.g. `"phpunit/phpunit": "13.3.4"` in your `composer.json`.
This will let you check & control any issues on a PHPUnit update, instead of breaking 
unrelated builds.

If you encounter a problem with PHPUnit assertions in your project, you have three options:
        
* Roll back to a PHPUnit minor / patch version that you know works for you. You can check our CI
  to see which versions are currently tested with this extension.
* Catch the failures within your Context classes and format them yourself. For example, you could implement
  a generic wrapper to call like `MyClass::formatFailure(fn () => Assert::assertSame(1, 2, 'Uh-oh'))`.
* Contribute a PR to this extension to add support for a newer PHPUnit version

### Migrating away from PHPUnit

Even with this extension, we recommend that you migrate away from using PHPUnit assertions in your
Behat steps. The extension is designed to provide support for legacy projects for as long as this
is feasible, but it is not a long-term solution.

As a minimum, consider using a different assertion tool any time that you implement new Steps or
refactor existing ones. Behat counts any `Exception` as a step failure, so you can easily use
more than one assertion library (or no library, for simple assertions).

## Installing and enabling the extension

Install it with [Composer](https://getcomposer.org):

```bash
composer require --dev behat/phpunit-assertions-extension
```

> [!WARNING]
> Your project should pin an explicit version of PHPUnit to avoid unexpected build failures on
> new releases (see above).

And then add it to your `behat.php` configuration file:

```php
<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\PHPUnitAssertionsExtension\BehatPHPUnitAssertionsExtension;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            // As with all Behat extensions, if you add to the `default` profile it will be inherited
            // by all other profiles.
            ->withExtension(new Extension(BehatPHPUnitAssertionsExtension::class)),
    );
```

## Contributing

Contributions are welcome, but please take a look at the [CONTRIBUTING.md](CONTRIBUTING.md) document
before you start.

## Versioning versions 

This package follows [Semantic Versioning v2.0.0](https://semver.org/spec/v2.0.0.html) for its own
code - but note the caveats above about support for PHPUnit versions.

## Useful Links

- Information about alternative assertion libraries is at [https://docs.behat.org/en/latest/useful_resources.html#assertion-tools](https://docs.behat.org/en/latest/useful_resources.html#assertion-tools)
- The main website is at [https://behat.org](https://behat.org)
- The documentation is at [https://docs.behat.org/en/latest/](https://docs.behat.org/en/latest/)
- [Note on Patches/Pull Requests](CONTRIBUTING.md)

## Contributors

- Konstantin Kudryashov [everzet](https://github.com/everzet) [original developer]
- Andrew Coulton [acoulton](https://github.com/acoulton) [current maintainer]
- Carlos Granados [carlos-granados](https://github.com/carlos-granados) [current maintainer]
- Christophe Coevoet [stof](https://github.com/stof) [current maintainer]
- Other [awesome developers](https://github.com/Behat/PHPUnitAssertionsExtension/graphs/contributors)

## Support the project

Behat is free software, maintained by volunteers as a gift for users. If you'd like to see
the project continue to thrive, and particularly if you use it for work, we'd encourage you
to contribute.

Contributions of time - whether code, documentation, or support reviewing PRs and triaging
issues - are very welcome and valued by the maintainers and the wider Behat community.

But we also believe that [financial sponsorship is an important part of a healthy Open Source
ecosystem](https://opensourcepledge.com/about/). Maintaining a project like Behat requires a
significant commitment from the core team: your support will help us to keep making that time
available over the long term. Even small contributions make a big difference.

You can support [@acoulton](https://github.com/acoulton), [@carlos-granados](https://github.com/carlos-granados) and
[@stof](https://github.com/stof) on GitHub sponsors. If you'd like to discuss supporting us in a different way, please
get in touch!


