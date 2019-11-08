CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration


INTRODUCTION
------------

Simple module that provides a block with the latest updates from Facebook for
the provided account. It is based on the likebox social plugin:
http://developers.facebook.com/docs/reference/plugins/like-box. The widget
settings are configurable directly in the block  and they are available for 
users with 'administer blocks' permission.


REQUIREMENTS
------------

Facebook Likebox has one dependency.

Drupal core modules
 * Block


INSTALLATION
------------

* Install as usual, see http://drupal.org/node/70151 for further information.


CONFIGURATION
-------------

* Go to Structure > Blocks
* The block will be called "'Your site name' on Facebook"
* Click on 'configure':
* Add the Facebook Page (i.e.: https://www.facebook.com/facebook) and
  configure the display and appearance settings.

 - Configuration examples:

 -- A) Faces and stream (default)
 --- Show Stream: Yes
 --- Show Faces: Yes
 --- Height: 556

 -- B) Without Faces
 --- Show Stream: Yes
 --- Show Faces: No
 --- Height: 292

 -- C) Without Stream and Faces
 --- Show Stream: No
 --- Show Faces: No
 --- Scrollling: Disabled
 --- Height: 63

 -- D) Only Faces
 --- Show Header: No
 --- Show Stream: No
 --- Show Faces: Yes
 --- Scrollling: Disabled
 --- Height: 330


Current Maintainers:

 * drozas https://www.drupal.org/u/drozas
 * baekelandt https://www.drupal.org/u/baekelandt
