# bitbucket-cli
Command line tool for interacting with your bitbucket

[![Maintainability](https://api.codeclimate.com/v1/badges/f9200996f2c3c2817c6c/maintainability)](https://codeclimate.com/github/martiis/bitbucket-cli/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/f9200996f2c3c2817c6c/test_coverage)](https://codeclimate.com/github/martiis/bitbucket-cli/test_coverage)
[![Build Status](https://travis-ci.org/martiis/bitbucket-cli.svg?branch=master)](https://travis-ci.org/martiis/bitbucket-cli)

## why

I'd like to keep my fingers on keyboard as much as possible, not reaching the trackpad or mouse to navigate through ui.

## setup

First create in your bitbucket account oauth2 tokens. Set *Callback Url* to `http://bitbucket.cli` and select permissions accordingly to your needs.

During instalation you will be asked for oauth2 key and token in the steps below.

### basic way
```
# git clone https://github.com/martiis/bitbucket-cli.git
# cd bitbucket-cli
# composer install
# vendor/bin/bitbucket
```

### composer way
```
# composer create-project martiis/bitbucket-cli:dev-master
# vendor/bin/bitbucket
```

Thats all.
