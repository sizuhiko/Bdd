Bdd
===

BDD(Behavior Driven Development) integration plugin for CakePHP2

Copyright(c) 2012 Sizuhiko. Licensed under the MIT license.

## Overview

This plugin focuses on BDD for CakePHP application development.
Like a Ruby on Rails, we can use 2 frameworks that are Story and Spec on CakePHP2.
This plugin integrates to followings:

* Spec framework uses Spec for PHP.
* Story framework uses Behat.

Spec for PHP(https://github.com/drslump/Spec-PHP) was inspired on RSpec from the Ruby world.
Behat(http://behat.org/) is PHP version clone of famous BDD framework cucumber in Ruby on Rails. 

Why these framework  was chosen ?
Because I think good that these are able to use natural language syntax to express expectations used by DSL.
It is easy and reader-friendly !!

Story framework is used to only the acceptance test.
Spec framework focuses on unit or functional test in piece of the test senario.

Then, the shell task of executing it by "cake Bdd.story" or "cake Bdd.spec" like "rake cucumber" or "rake spec" was made. 
It is BddPlugin for CakePHP2 !!

## Usage

### Require

* PHPUnit 3.6 or later
* CakePHP 2.0 or later
* The data base such as MySQL must be installed, and the data base for the test must be prepared. 
* PHP 5.3.2 or later
* Composer

Followings dependency installed by Composer.
* Behat and Mink (through MinkExtention)
* Mink goutte driver
* Mink selenium driver
* Spec for PHP
  * Object_Freezer
  * Console_CommandLine
  * Hamcrest

### Install

Under the plugins directory of CakePHP root, execute the following commands. 

```sh
cd plugins
git clone git@github.com:sizuhiko/Bdd.git
cd Bdd
curl -s https://getcomposer.org/installer | php
php composer.phar install --dev
cd ..
```

#### With A Multi-Framework Composer Library Installer 

If you want to use 'A Multi-Framework Composer Library Installer'(https://github.com/composer/installers), then you should add repositories section in composer.json of your application.

```json
  "repositories": [
    {
        "type": "pear",
        "url": "http://pear.phpunit.de"
    },
    {
        "type": "vcs",
        "url": "https://github.com/sizuhiko/CommonContexts.git"
    },
    {
        "type": "vcs",
        "url": "https://github.com/sizuhiko/Spec-PHP.git"
    },
    {
        "type": "vcs",
        "url": "git://github.com/sizuhiko/Bdd.git"
    }    
    # .... your application required repositories...
  ],    

  "require-dev": { 
    "require-dev": {
      "sizuhiko/Bdd": "dev-master",
      # ... your application required dependencies.
    }
  }
```

### Configuration

#### Plugin load setting

Add the following code in app/Config/bootstrap.php

```php
CakePlugin::load('Bdd');
```

#### Bake initial templates

Next, the following commands are executed. (on root of CakePHP)
```sh
ls
  app  index.php  lib  plugins  vendors

lib/Cake/Console/cake Bdd.init
```

#### Application root URL setting

Setting your application root url into app/Config/behat.yml.
```yaml
base_url: http://test.localhost:8888/application-name/
```

This specifies the host name, the port number, and the application name, etc. by the one to set passing the route of the application. Setting it to the host name that can be identified to the test environment as much as possible is recommended. 

#### Do you end by this?

A basic environmental setting ends by this. Bdd plugin is an executable situation.

### Your application database settings.

An initial value of config/database.php is $default. 
When UnitTest is executed, $test might already been defined. 
Story framework is executed by the browser access. Data base ($test) for the test cannot access to without the change in the setting.

When test step registers the test data, Bdd plugin uses $test. Therefore, the definition is needed without fail. My recommendation is that the data base setting is switched by the environment like "Easy peasy database config" of Bakery. 
http://bakery.cakephp.org/articles/joelmoss/2006/11/16/easy-peasy-database-config

In sample application (sample) of the inclusion, it switch based on the accessed server name.
When the accessed host name is test.localhost, the data base for test is used. 
When it is not so, the data base for development is used.
It will become possible to use test.localhost if it set  "127.0.0.1 localhost and test.localhost" into the hosts file. It is easy. 

### How to insert test data

#### Into the step file on story framework

Bdd.story has two methods that can be used with the step file. 

* truncateModel() : Method for deletion of test data. 
* getModel() Method of getting model to register test data. 

```php
$steps->Given('/^there is a post:$/', function($world, $table) {
  $hash = $table->getHash();
  $world->truncateModel('Post');
  $post = $world->getModel('Post');
  foreach ($hash as $row) {
	$post->create(array('Post'=>array('title'=>$row['Title'], 'body'=>$row['Body'])));
	$post->save();
  }
});
```

#### Into the spec file on spec framework

Bdd.spec can use fixture of CakePHP.
In before block, can use `$W->fixtures = array(....)` looks like `$fixtures = array(...)` on your CakeTestCase.

```php
describe "Post"
  context "with fixture"
    before
      $W->fixtures = array('app.post');
    end
    ....
```

### Code Coverage. 

We can output code coverage report.
The feature can do only specs.

`lib/Cake/Console/cake Bdd.spec --coverage-html report`

write some html to 'report' directory.

#### Stories code coverage

If you want output coverage report with stories, may be able to use Console/CodeCoverageManager.
Example for using CodeCoverageManager is included in Console/Command/SpecShell.php
And, in your webserver's php.ini configuration file, configure the auto_prepend_file and auto_append_file, respectively.
Please refer http://www.phpunit.de/manual/current/en/selenium.html

### Original i18n files for stories

We can replace mink-extention i18n files.
If you add or edit transration files, followings:

* make directory `/CAKEPHP_ROOT/features/steps/i18n`.
* copy from mink-extention/i18n/*.xliff or create original xliff file.

### Let's execute it. 

Move to the directory CakePHP root(including lib, app, and plugins), and next, execute the following commands.

`lib/Cake/Console/cake Bdd.spec`
`lib/Cake/Console/cake Bdd.story`

You can use any of original framework options.
Please check 
`lib/Cake/Console/cake Bdd.story --help`
`lib/Cake/Console/cake Bdd.spec --help`

The command operates only by the CakePHP root directory.
Please note it.

## Sample Appliacation and Test code, Tutorial

https://github.com/sizuhiko/BddExampleApp

It include English and Japanese features and some specs.
And the README.md is Bdd plugin tutorial !!

