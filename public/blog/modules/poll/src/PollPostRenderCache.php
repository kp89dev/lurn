<?php

namespace Drupal\poll;

use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Defines a service for poll post render cache callbacks.
 */
class PollPostRenderCache {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new PollPostRenderCache object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * Callback for #post_render_cache; replaces placeholder with poll view form.
   *
   * @param int $id
   *   The poll ID.
   * @param string $view_mode
   *   The view mode the poll should be rendered with.
   * @param string $langcode
   *   The langcode in which the poll should be rendered.
   *
   * @return array
   *   A renderable array containing the poll form.
   */
  public function renderViewForm($id, $view_mode, $langcode = NULL) {
    /** @var \Drupal\poll\PollInterface $poll */
    $poll = $this->entityManager->getStorage('poll')->load($id);

    if ($poll) {
      if ($langcode && $poll->hasTranslation($langcode)) {
        $poll = $poll->getTranslation($langcode);
      }
      /** @var \Drupal\poll\Form\PollViewForm $form_object */
      $form_object = \Drupal::service('class_resolver')->getInstanceFromDefinition('Drupal\poll\Form\PollViewForm');
      $form_object->setPoll($poll);
      return \Drupal::formBuilder()->getForm($form_object, \Drupal::request(), $view_mode);
    }
    else {
      return ['#markup' => ''];
    }
  }

}
