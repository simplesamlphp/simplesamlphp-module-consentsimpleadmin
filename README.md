![Build Status](https://github.com/simplesamlphp/simplesamlphp-module-consentsimpleadmin/workflows/CI/badge.svg?branch=master)
[![Coverage Status](https://codecov.io/gh/simplesamlphp/simplesamlphp-module-consentsimpleadmin/branch/master/graph/badge.svg)](https://codecov.io/gh/simplesamlphp/simplesamlphp-module-consentsimpleadmin)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/simplesamlphp/simplesamlphp-module-consentsimpleadmin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/simplesamlphp/simplesamlphp-module-consentsimpleadmin/?branch=master)

Consent Simple Admin module
===========================

A SimpleSAMLphp module implementing a very simple user interface for managing consent.

Installation
------------

Once you have installed SimpleSAMLphp, installing this module is very simple. Just execute the following
command in the root of your SimpleSAMLphp installation:

```
composer.phar require simplesamlphp/simplesamlphp-module-consentsimpleadmin:dev-master
```

where `dev-master` instructs Composer to install the `master` branch from the Git repository. See the
[releases](https://github.com/simplesamlphp/simplesamlphp-module-consentsimpleadmin/releases) available if you
want to use a stable version of the module.

The module is enabled by default. If you want to disable the module once installed, you just need to create a file named
`disable` in the `modules/consentSimpleAdmin/` directory inside your SimpleSAMLphp installation.
