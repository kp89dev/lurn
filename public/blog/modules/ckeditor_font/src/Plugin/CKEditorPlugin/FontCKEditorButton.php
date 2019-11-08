<?php

/**
 * @file
 * Contains \Drupal\ckeditor_font\Plugin\CKEditorPlugin\FontCKEditorButton.
 */

namespace Drupal\ckeditor_font\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "font" plugin.
 *
 * NOTE: The plugin ID ('id' key) corresponds to the CKEditor plugin name.
 * It is the first argument of the CKEDITOR.plugins.add() function in the
 * plugin.js file.
 *
 * @CKEditorPlugin(
 *   id = "font",
 *   label = @Translation("Font ckeditor button")
 * )
 */
class FontCKEditorButton extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   *
   * NOTE: The keys of the returned array corresponds to the CKEditor button
   * names. They are the first argument of the editor.ui.addButton() or
   * editor.ui.addRichCombo() functions in the plugin.js file.
   */
  public function getButtons() {
    // Make sure that the path to the image matches the file structure of
    // the CKEditor plugin you are implementing.
    $modulePath = drupal_get_path('module', 'ckeditor_font');
    return array(
      'Font' => array(
        'label' => t('Font ckeditor button'),
        'image' => $modulePath . '/icons/font.png',
      ),
      'FontSize' => array(
        'label' => t('Font ckeditor button'),
        'image' => $modulePath . '/icons/fontsize.png',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    // Make sure that the path to the plugin.js matches the file structure of
    // the CKEditor plugin you are implementing.
    //$path = '/libraries/font';
    //return $path . '/plugin.js';
    return 'libraries/font/plugin.js';
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
  function getDependencies(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  function getLibraries(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array();
  }

}
