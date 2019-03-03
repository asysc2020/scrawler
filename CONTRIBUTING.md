# Contributing
Contributions are **welcome** and will be fully **credited**.
For code contributions please use pull requests on the
[GitHub repository](https://github.com/Sobak/scrawler).

## Issues
Reporting an issue is absolutely fine if you don't have time or
will to solve the problem yourself. Just please remember about
providing all potentially helpful information when creating an
issue. I _highly_ recommend stickin to the following template:

```md
Describe what is wrong, include piece of code or screenshot if needed.

# Steps to reproduce
...

# What was expected result?
...

Version: Version number or commit hash if using develop - preferably `git describe --all --long` output.
```

## Pull Requests
- **[PSR-2 Coding Standard][psr-2]** - The easiest way to apply the conventions
  is to install [PHP Code Sniffer][php-cs]
- **Add tests!** - Plese try to provide at least basic code coverage for your
  change, don't forget about checking if already existing tests still pass
  with your changes applied
- **Document any changes in behaviour** - Make sure that `README.md` and the
  `docs/` are kept up-to-date. If you submit new code within `\Scrawler\Blocks`
  having proper documentation is a must
- **Consider our release cycle** - The project follows [SemVer v2.0.0][semver].
  Having random breaks in public APIs is not an option
- **Create feature branches** - Don't submit changes from your `master` branch
- **One pull request per feature** - If you want to do more than one thing, send
  multiple pull requests.
- **Send coherent history** - Make sure each individual commit in your pull request
  is meaningful. If you had to make multiple intermediate commits while developing,
  please [squash them][squashing] before submitting.

## Running Tests
Please don't forget to run the test suite before submitting a pull request.

``` bash
$ ./vendor/bin/phpunit
```

**Happy tinkering**, I appreciate your help!

[php-cs]: https://github.com/squizlabs/PHP_CodeSniffer
[psr-2]: https://www.php-fig.org/psr/psr-2/
[semver]: https://semver.org
[squashing]: https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#_changing_multiple