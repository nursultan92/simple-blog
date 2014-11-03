Simple Blog
========================

Welcome to simple but fully functional blog in symfony. This symfony project
has authentication and authorization system. Users can register then leave comment on posts.
There are three roles Administrator,Moderator,User,Anonymous
Administrator can create,edit,delete posts,users,comments,categories,user groups.
Moderator can create,edit,delete comments of everyone.
User can view posts, create comment,edit delete his/her comment
Anonymous can view posts.

This document contains information on how to download, install, and start
using Simple Blog.

1) Installing the Simple Blog
----------------------------------

When it comes to installing the Simple Blog, you have the
following options.

### Use Git Commandline (*recommended*)

    git clone https://github.com/nursultan92/simple-blog.git

### Download Zip archive file

    https://github.com/nursultan92/simple-blog/archive/master.zip

### Install Third Party libraries

As symfony uses composer as a default package management system. You need to
download **composer** first to your projects root directory using **curl**

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command in your projects root directory:

    curl -s http://getcomposer.org/installer | php

Install packages using following command

    php composer.phar install

2) Checking your System Configuration
-------------------------------------

Before starting coding, make sure that your local system is properly
configured for Symfony.

Execute the `check.php` script from the command line:

    php app/check.php

The script returns a status code of `0` if all mandatory requirements are met,
`1` otherwise.

Access the `config.php` script from a browser:

    http://localhost/path-to-project/web/config.php

If you get any warnings or recommendations, fix them before moving on.


3) Demo With Default Fixtures
-----------------------------

To make things even simple this project comes with example fixtures. You can configure
database running commands and install fixture.
First set your database name in `parameters.yml`

    php app/console propel:database:create
    php app/console propel:sql:insert --force
    php app/console propel:fixtures:load

Start php built in server:
   
    php app/console server:run
    
Go to *http://127.0.0.1:8000* and enjoy!


4) Users
---------
There are by default 3 users

   Role | Email | Password
   -----|-------|---------
 Administrator | admin@example.com    | password
 Moderator     | moderator@example.com | password
 User          | user@example.com     | password

What's inside?
---------------

The Simple Blog is configured with the following defaults:

  * Twig is the only configured template engine;

  * Annotations for everything are enabled.

It comes pre-configured with the following bundles:

  * **FrameworkBundle** - The core Symfony framework bundle

  * [**SensioFrameworkExtraBundle**][6] - Adds several enhancements, including
    template and routing annotation capability

  * [**PropelBundle**][7] - Adds support for the Propel ORM
  * [**TwigBundle**][8] - Adds support for the Twig templating engine

  * [**SecurityBundle**][9] - Adds security by integrating Symfony's security
    component

  * [**MonologBundle**][11] - Adds support for Monolog, a logging library

  * [**AsseticBundle**][12] - Adds support for Assetic, an asset processing
    library

  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar

  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for
    configuring and working with Symfony distributions

  * [**SensioGeneratorBundle**][13] (in dev/test env) - Adds code generation
    capabilities

All libraries and bundles included in the Simple Blog are
released under the MIT or BSD license.

Enjoy!

[1]:  http://symfony.com/doc/2.5/book/installation.html
[2]:  http://getcomposer.org/
[3]:  http://symfony.com/download
[4]:  http://symfony.com/doc/2.5/quick_tour/the_big_picture.html
[5]:  http://symfony.com/doc/2.5/index.html
[6]:  http://symfony.com/doc/2.5/bundles/SensioFrameworkExtraBundle/index.html
[7]:  https://github.com/propelorm/PropelBundle
[8]:  http://symfony.com/doc/2.5/book/templating.html
[9]:  http://symfony.com/doc/2.5/book/security.html
[11]: http://symfony.com/doc/2.5/cookbook/logging/monolog.html
[12]: http://symfony.com/doc/2.5/cookbook/assetic/asset_management.html
[13]: http://symfony.com/doc/2.5/bundles/SensioGeneratorBundle/index.html


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/nursultan92/simple-blog/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

