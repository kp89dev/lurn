<?php
/**
 * @file
 * Contains \Drupal\ckeditor_uploadimage\Plugin\CKEditorPlugin\FileTools.
 */

namespace Drupal\ckeditor_uploadimage\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "templates" plugin.
 *
 * @CKEditorPlugin(
 *   id = "filetools",
 *   label = @Translation("CKEditor File Tools"),
 *   module = "ckeditor_uploadimage"
 * )
 */
class FileTools extends PluginBase implements CKEditorPluginInterface {
  /**
   * {@inheritdoc}
   */
  function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  function getFile() {
    $path = 'libraries/ckeditor/plugins/' . $this->getPluginId() . '/plugin.js';
    if (file_exists('profiles/' . drupal_get_profile() . "/$path")) {
      return 'profiles/' . drupal_get_profile() . "/$path";
    }
    return $path;
  }

  /**
   * {@inheritdoc}
   */
  function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  function getLibraries(Editor $editor) {
    return [];
  }
}