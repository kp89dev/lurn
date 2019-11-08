<?php

namespace Drupal\poll\Tests;

/**
 * Tests the poll field UI.
 *
 * @group poll
 */
class PollFieldUITest extends PollTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field_ui',
    'help',
  ];

  /**
   * {@inheritdoc}
   */
  protected $adminPermissions = [
    'administer poll form display',
    'administer poll display',
    'administer poll fields',
    'administer polls',
    'access polls',
    'access administration pages',
    'administer blocks',
    'administer permissions',
  ];

  /**
   * Test if 'Manage fields' page is visible in the poll's settings UI.
   */
  protected function testPollFieldUI() {

    $this->drupalLogin($this->admin_user);
    $this->drupalGet('admin/config/content/poll');
    $this->assertResponse(200);

    // Check if 'Manage fields' tab appears in the poll's settings page.
    $this->assertUrl('admin/config/content/poll');
    $xpath = '///div/main/aside/div/div/nav/ul/li[2]/a';
    $this->assertFieldByXPath($xpath, 'Manage fields');

    // Ensure that the 'Manage display' page is visible.
    $this->clickLink('Manage display');
    $this->assertTitle('Manage display | Drupal');

    // Ensure vote results in List
    $element = $this->cssSelect('#poll-votes');
    $this->assertNotEqual($element, array(), '"Vote form/Results" field is available.');

    // Ensure that the 'Manage fields' page is visible.
    $this->clickLink('Manage fields');
    $this->assertTitle('Manage fields | Drupal');

    // Add a poll field.
    $this->clickLink('Add field');
    $edit = [
      'new_storage_type' => 'field_ui:entity_reference:user',
      'label' => 'poll',
      'field_name' => 'poll',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save and continue');

    $edit = [
      'settings[target_type]' => 'poll',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save field settings');
    $this->assertText('Updated field poll field settings.');

    $edit = [
      'label' => 'field_poll',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save settings');
    $this->assertText('Saved field_poll configuration.');

    // Ensure that the newly created field is listed.
    $this->assertText($edit['label']);
  }

  /**
   * Tests if the links on the Poll Help-page are working properly.
   */
  function testPollHelpLinks() {
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Poll module');
    $this->assertUrl('https://www.drupal.org/docs/8/modules/poll');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Add a poll');
    $this->assertUrl('poll/add');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Polls', 0);
    $this->assertUrl('admin/content/poll');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Polls', 1);
    $this->assertUrl('admin/content/poll');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Blocks administration page');
    $this->assertUrl('admin/structure/block');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Configure Poll permissions');
    $this->assertUrl('admin/people/permissions#module-poll');
  }
}
