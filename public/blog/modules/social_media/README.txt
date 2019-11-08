The social media share module allows the user share
current page to different social media.

Social media share is rendered as block and as field type
as well and you can customize as much
as you can in Configuration page and settings page.

Event subscriber

Module provides an event so that others can develop an event subscriber to modify.
This are the event Subscriber name :-

  social_media.add_more_social_media
  -----------------------------------
  Used for adding your own social media or extend more.
 
  social_media.pre_execute
  -----------------------------------
  Used for modify the configuration array before executing.
 
  social_media.pre_render
  -----------------------------------
  Used for modify element just before rendered.

Check the social_media.api file to see implementation of event subscriber.

Installation
------------
Standard module installation applies.


Configuration
-------------
Configuration page(admin/config/services/social-media)
where you need to set variables for your need of social media.
