# CKEditor Upload Image

Integrates CKEditor's Upload Image plugin to Drupal. This enables Drupal's
default WYSIWYG text editor capable of uploading images that were dropped or 
pasted from clipboard into the editor as inline image. The upload is 
implemented in a non-blocking way, so while the image is being uploaded the user 
may continue editing the content.
 
This module uses the settings and features of native DrupalImage CKEditor plugin
for the uploaded images made via drag and drop or clipboard paste.

## Requirements
* Drupal 8.x
* CKEditor module
* [Upload Image](http://ckeditor.com/addon/uploadimage)
* [Upload Widget](http://ckeditor.com/addon/uploadwidget)
* [File Tools](http://ckeditor.com/addon/filetools)
* [Notification](http://ckeditor.com/addon/notification)
* [Notification Aggregator](http://ckeditor.com/addon/notificationaggregator)

## Installation
1. Download the following CKEditor plugins:
  * [Upload Image](http://ckeditor.com/addon/uploadimage)
  * [Upload Widget](http://ckeditor.com/addon/uploadwidget)
  * [File Tools](http://ckeditor.com/addon/filetools)
  * [Notification](http://ckeditor.com/addon/notification)
  * [Notification Aggregator](http://ckeditor.com/addon/notificationaggregator)
2. Unzip and place the contents for each plugin in the the following directory:
  * `DRUPAL_ROOT/libraries/ckeditor/plugins/PLUGIN_NAME`
3. Install the module per normal 
https://www.drupal.org/documentation/install/modules-themes/modules-8.
4. Go to the 'Text formats and editors' configuration page: 
`/admin/config/content/formats`, and for each text format/editor combo under the
CKEditor plugin settings, select Image and there you can control the upload
directory, maximum file size, image dimension and turn on/off the image upload.
5. Under Enabled filters, "Restrict images to this site" must be disabled.
6. If "Limit allowed HTML tags and correct faulty HTML" is enabled, make sure 
the `<img>` tag is included in "Allowed HTML tags" with attributes 
`data-cke-saved-src data-cke-upload-id data-widget data-cke-widget-keep-attr 
data-cke-widget-data class` inside it.
7. Enable the 'Use the CKEditor Upload Image' permission to applicable Roles at 
'Permissions' page: `/admin/people/permissions`.

#### Support
Please use the issue queue for filing bugs with this module at
https://www.drupal.org/project/issues/ckeditor_uploadimage