<?php
/**
 * @file
 * Contains \Drupal\ckeditor_uploadimage\Controller\CKEditorUploadImageController.
 */

namespace Drupal\ckeditor_uploadimage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Bytes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CKEditorUploadImageController extends ControllerBase {
  /**
   * Save uploaded file via CKEditor uploadimage plugin.
   */
  public function saveFile(Request $request) {
    $status   = TRUE;
    $errorMsg = '';
    $defaultResponsiveImageStyle = '';
    $filterFormatId = $request->query->get('filterFormatId');
    $editor = editor_load($filterFormatId);
    // Construct strings to use in the upload validators.
    $imageUpload = $editor->getImageUploadSettings();
    if (!empty($imageUpload['max_dimensions']['width']) || !empty($imageUpload['max_dimensions']['height'])) {
      $maxDimensions = $imageUpload['max_dimensions']['width'] . 'x' . $imageUpload['max_dimensions']['height'];
    }
    else {
      $maxDimensions = 0;
    }
    $maxFilesize = min(Bytes::toInt($imageUpload['max_size']), file_upload_max_size());
    $destination = $imageUpload['scheme'] . '://' . $imageUpload['directory'];
    if (isset($destination) && !file_prepare_directory($destination, FILE_CREATE_DIRECTORY)) {
      \Drupal::logger('ckeditor_uploadimage')->notice(
        'The upload directory %directory for the file field %name could not be 
        created or is not accessible. A newly uploaded file could not be saved 
        in this directory as a consequence, and the upload was canceled.', [
          '%directory' => $destination,
          '%name' => 'fid',
      ]);
      $errorMsg = $this->t('The file could not be uploaded.');
      $status  = FALSE;
    }
    else {
      $validators = [
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => [$maxFilesize],
        'file_validate_image_resolution' => [$maxDimensions],
      ];
      $files = $request->files->get('files', array());
      if (!isset($files['fid'])) {
        $files = $request->files->all();
        $request->files->set('files', ['fid' => $files['upload']]);
      }
      $file = file_save_upload('fid', $validators, $destination);
      $messages = drupal_get_messages();
      if (isset($messages['error'])) {
        /** @var \Drupal\Core\Render\Markup $message */
        foreach ($messages['error'] as $message) {
          $errorMsg = '<div>' . $message->jsonSerialize() . '</div>';
        }
      }
      if (isset($messages['warning'])) {
        /** @var \Drupal\Core\Render\Markup $message */
        foreach ($messages['warning'] as $message) {
          $errorMsg .= '<div>' . $message->jsonSerialize() . '</div>';
        }
      }
      if (isset($messages['status'])) {
        /** @var \Drupal\Core\Render\Markup $message */
        foreach ($messages['status'] as $message) {
          $errorMsg .= '<div>' . $message->jsonSerialize() . '</div>';
        }
      }
      if (!empty($errorMsg)) {
        $errorMsg = "<div style='text-align: left;'>$errorMsg</div>";
      }
      if (!empty($file[0])) {
        $origFileName = $file[0]->getFilename();
        $alt  = pathinfo($origFileName, PATHINFO_FILENAME);
        $alt  = str_replace('_', ' ', $alt);
        $uri  = $file[0]->getFileUri();
        $uuid = $file[0]->uuid();
        $fileName = \Drupal::service('file_system')->basename($uri);
        $url = file_url_transform_relative(file_create_url($uri));
        $entityType = $file[0]->getEntityTypeId();
        if (\Drupal::moduleHandler()->moduleExists('inline_responsive_images')) {
          // Get a responsive image style.
          $responsiveImage = $editor->getFilterFormat()->filters('filter_responsive_image_style');
          $responsiveImageSettings = $responsiveImage->getConfiguration();
          foreach ($responsiveImageSettings['settings'] as $responsiveImageStyle => $enabled) {
            if ($enabled == '1') {
              // Make the first responsive image style as default.
              $defaultResponsiveImageStyle = str_replace('responsive_style_', '', $responsiveImageStyle);
              break;
            }
          }
        }
      }
      else {
        $status  = FALSE;
      }
    }
    if (!$status) {
      $json = [
        'uploaded' => $status,
        'error' => [
          'message' => $errorMsg,
        ],
      ];
    }
    else {
      $json = [
        'uploaded' => $status,
        'fileName' => $fileName,
        'url' => $url,
        'alt' => $alt,
        'entityUuid' => $uuid,
        'entityType' => $entityType,
        'responsiveImageStyle' => $defaultResponsiveImageStyle,
        'error' => [
          'message' => $errorMsg,
        ],
      ];
    }

    return new JsonResponse($json);
  }
}