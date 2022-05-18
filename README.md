<h1 align="center">chiron/devtools</h1>

<p align="center">
    <strong>A Composer plugin to aid PHP library and application development.</strong>
</p>

<p align="center">
    <a href="https://github.com/ncou/devtools"><img src="https://img.shields.io/badge/source-chiron/devtools-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/chiron/devtools"><img src="https://img.shields.io/packagist/v/chiron/devtools.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://github.com/ncou/devtools/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/chiron/devtools.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ncou/devtools/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/workflow/status/ncou/devtools/build/master?style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://app.codecov.io/gh/ncou/devtools"><img src="https://img.shields.io/codecov/c/gh/ncou/devtools?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ncou/devtools"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Fncou%2Fdevtools%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>

## About

The idea behind this package is to consolidate and simplify the use of 
development tools and scripts across all my repositories.

These tools might not be for you, and that's okay.

Maybe these tools help a lot, but you have different needs. That's also okay.
You may fork and modify to creating your own Composer plugin.

Of course, if you want to help improve these tools, I welcome your contributions.
Feel free to open issues, ask about or request features, and submit PRs. I can't
wait to see what you come up with.

This project adheres to a [code of conduct](./.github/CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

## Requirements

- PHP 8.0 or 8.1

## Installation

Install this package as a development dependency using
[Composer](https://getcomposer.org).

``` bash
composer require --dev chiron/devtools
```

## Usage

This package is a Composer plugin. This means Composer recognizes that it
provides custom functionality to your `composer` command. After installation,
type `composer list`, and you'll see a lot of new commands that this plugin
provides.

``` bash
composer list
```

### Add a Command Prefix

The commands this plugin provides are all intermingled with the rest of the
Composer commands, so it may be hard to find them all. We have a way to group
them by command namespace, though. Open `composer.json` and add a
`chiron/devtools.command-prefix` property to the `extra` section. You may use
any prefix you wish.

``` json
{
    "extra": {
        "chiron/devtools": {
            "command-prefix": "my-prefix"
        }
    }
}
```

Now, when you type `composer list` (or just `composer`), you'll see a section
of commands that looks like this:

```
 my-prefix
  my-prefix:analyze:all           Runs all static analysis checks.
  my-prefix:analyze:phpstan       Runs the PHPStan static analyzer.
  my-prefix:analyze:psalm         Runs the Psalm static analyzer.
  my-prefix:build:clean           Cleans the build/ directory.
  my-prefix:build:clean:all       Cleans the build/ directory.
  my-prefix:build:clean:cache     Cleans the build/cache/ directory.
  my-prefix:build:clean:coverage  Cleans the build/coverage/ directory.
  my-prefix:changelog             Support for working with Keep A Changelog.
  my-prefix:license               Checks dependency licenses.
  my-prefix:lint:all              Runs all linting checks.
  my-prefix:lint:fix              Auto-fixes coding standards issues, if possible.
  my-prefix:lint:pds              Validates project compliance with pds/skeleton.
  my-prefix:lint:style            Checks for coding standards issues.
  my-prefix:lint:syntax           Checks for syntax errors.
  my-prefix:test:all              Runs linting, static analysis, and unit tests.
  my-prefix:test:coverage:ci      Runs unit tests and generates CI coverage reports.
  my-prefix:test:coverage:html    Runs unit tests and generates HTML coverage report.
  my-prefix:test:unit             Runs unit tests.
```

You can also list commands by command prefix with `composer list my-prefix`.

### Extending or Overriding chiron/devtools Commands

Maybe the commands chiron/devtools provides don't do everything you need, or
maybe you want to replace them entirely. The configuration allows you to do
this!

Using the `chiron/devtools.commands` property in the `extra` section of
`composer.json`, you may specify any command (*without* your custom prefix, if
you've configured one) as having other scripts to run, in addition to the
command's default behavior, or you may override the default behavior entirely.

Specifying additional scripts works exactly like
[writing custom commands](https://getcomposer.org/doc/articles/scripts.md#writing-custom-commands)
in `composer.json`, but the location is different. Everything you can do with
a custom Composer command, you can do here because they're the same thing.

``` json
{
    "extra": {
        "chiron/devtools": {
            "command-prefix": "my-prefix",
            "commands": {
                "lint:all": {
                    "script": "@mylint"
                },
                "test:all": {
                    "script": [
                        "@mylint",
                        "@phpbench"
                    ]
                }
            }
        }
    },
    "scripts": {
        "mylint": "parallel-lint src tests",
        "phpbench": "phpbench run"
    }
}
```

In this way, when you run `composer my-prefix:lint:all` or `composer my-prefix:test:all`,
it will  execute the default behavior first and then run your additional commands.
To  override the default behavior so that it doesn't run at all and only your
scripts run, specify the `override` property and set it to `true`.

``` json
{
    "extra": {
        "chiron/devtools": {
            "commands": {
                "lint:all": {
                    "override": true,
                    "script": "parallel-lint src tests"
                }
            }
        }
    }
}
```

### Composer Command Autocompletion

Did you know you can set up your terminal to do Composer command autocompletion?

If you'd like to have Composer command autocompletion, you may use
[bamarni/symfony-console-autocomplete](https://github.com/bamarni/symfony-console-autocomplete).
Install it globally with Composer:

``` bash
composer global require bamarni/symfony-console-autocomplete
```

Then, in your shell configuration file — usually `~/.bash_profile` or `~/.zshrc`,
but it could be different depending on your settings — ensure that your global
Composer `bin` directory is in your `PATH`, and evaluate the
`symfony-autocomplete` command. This will look like this:

``` bash
export PATH="$(composer config home)/vendor/bin:$PATH"
eval "$(symfony-autocomplete)"
```

Now, you can use the `tab` key to auto-complete Composer commands:

``` bash
composer my-prefix:[TAB][TAB]
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](./.github/CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md](./.github/SECURITY.md) for instructions on submitting a vulnerability report.

## Credits
This composer plugin is based on a fork from the excellent ramsey/devtools-lib.

## License

MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.
