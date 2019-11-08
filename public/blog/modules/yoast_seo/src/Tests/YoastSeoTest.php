<?php

namespace Drupal\yoast_seo\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Ensures that the Yoast Seo works correctly.
 *
 * @group YoastSeo
 */
class YoastSeoTest extends WebTestBase {

  /**
   * Profile to use.
   */
  protected $profile = 'testing';

  /**
   * Admin user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field_ui',
    'metatag',
    'yoast_seo',
    'entity_test',
    'node',
  ];

  /**
   * Permissions to grant admin user.
   *
   * @var array
   */
  protected $permissions = [
    'access administration pages',
    'administer content types',
    'administer nodes',
    'administer meta tags',
    'administer yoast seo',
    'view test entity',
    'access content',
  ];

  /**
   * Sets the test up.
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->entityManager = \Drupal::entityManager();
  }

  /**
   * Enable Yoast SEO for a given bundle.
   */
  protected function enableYoastSeo($entity_type, $bundle) {
    // Configure yoast seo for the given bundle.
    $this->drupalGet('admin/config/yoast_seo');
    $edit = array($entity_type . '[' . $bundle . ']' => $bundle);
    json_decode($this->drupalPostForm(NULL, $edit, t('Save')));
    $this->assertFieldChecked('edit-node-page');
  }

  /**
   * Disable Yoast SEO for a given bundle.
   */
  protected function disableYoastSeo($entity_type, $bundle) {
    // Configure yoast seo for the given bundle.
    $this->drupalGet('admin/config/yoast_seo');
    $edit = array($entity_type . '[' . $bundle . ']' => FALSE);
    json_decode($this->drupalPostForm(NULL, $edit, t('Save')));
    $this->assertNoFieldChecked('edit-node-page');
  }

  /**
   * Only available when it has been previously enabled on the content type.
   *
   * Given    I am logged in as admin
   * When     I am adding a content on a content type which doesn't have a Meta
   * Tag field
   * Then     Then I should not see the Yoast SEO section active
   * When     I am adding a content on a content type which have a Meta Tag
   * field.
   */
  public function testYoastSeoEnabledDisabled() {
    // Given I am logged in as admin.
    $this->drupalLogin($this->adminUser);
    // Create a page node type.
    $this->entityManager->getStorage('node_type')->create(array(
      'type' => 'page',
      'name' => 'page',
    ))->save();

    // When I am adding an Entity Test content.
    $this->drupalGet('node/add/page');
    // Then I should not see the Yoast SEO section active.
    $this->assertNoText('Yoast SEO for drupal');

    // When I enable Yoast SEO for the page bundle.
    $this->enableYoastSeo('node', 'page');
    // And I am adding an Entity Test content.
    $this->drupalGet('node/add/page');
    // Then I should see the Yoast SEO section active.
    $this->assertText('Real-time SEO for drupal');

    // When I disable Yoast SEO for the page bundle.
    $this->disableYoastSeo('node', 'page');
    // And I am adding an Entity Test content.
    $this->drupalGet('node/add/page');
    // Then I should not see the Yoast SEO section active.
    $this->assertNoText('Real-time SEO for drupal');
  }

}
