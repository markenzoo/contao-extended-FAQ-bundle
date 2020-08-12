Contao Extended FAQ Bundle
===============================


[![Version](http://img.shields.io/packagist/v/markenzoo/contao-extended-faq-bundle.svg?style=for-the-badge&label=Latest)](http://packagist.org/packages/markenzoo/contao-extended-faq-bundle)
[![GitHub issues](https://img.shields.io/github/issues/markenzoo/contao-extended-faq-bundle?style=for-the-badge&logo=github)](https://github.com/markenzoo/contao-extended-faq-bundle/issues)
[![License](http://img.shields.io/packagist/l/markenzoo/contao-extended-faq-bundle?style=for-the-badge&label=License)](http://packagist.org/packages/markenzoo/contao-extended-faq-bundle)
[![Build Status](http://img.shields.io/travis/markenzoo/contao-extended-faq-bundle/master.svg?style=for-the-badge&logo=travis)](https://travis-ci.org/markenzoo/contao-extended-faq-bundle)
[![Downloads](http://img.shields.io/packagist/dt/markenzoo/contao-extended-faq-bundle?style=for-the-badge&label=Downloads)](http://packagist.org/packages/markenzoo/contao-extended-faq-bundle)

This extension provides an additional Front-End Module to show only selected questions from a FAQ category together with a link to the complete FAQ Page inside Contao CMS.


Features
--------

  - Show only selected FAQ's from a selected category as a teaser 
  - Add a link to view the complete FAQ Page

Requirements
------------

 - PHP >7.2
 - Contao ~4.9 LTS
 
 
Install
-------

### Managed edition

When using the managed edition it's pretty simple to install the package. Just search for the package in the
Contao Manager and install it. Alternatively you can use the CLI.  

```bash
# Using the contao manager
$ php contao-manager.phar.php composer require markenzoo/contao-extended-faq-bundle

# Using composer directly
$ php composer.phar require markenzoo/contao-extended-faq-bundle

# Using global composer installation
$ composer require markenzoo/contao-extended-faq-bundle
```

### Symfony application

If you use Contao in a symfony application without contao/manager-bundle, you have to register the bundle manually:

```php

class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Markenzoo\ContaoExtendedFaqBundle\ContaoExtendedFaqBundle()
        ];
    }
}

```

## Note to self

Run the PHP-CS-Fixer and the unit test before you release your bundle:

```bash
vendor/bin/php-cs-fixer fix -v
vendor/bin/phpunit
vendor/bin/psalm
vendor/bin/psalter --issues=all --dry-run
```