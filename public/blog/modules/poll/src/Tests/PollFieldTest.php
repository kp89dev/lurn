<?php

namespace Drupal\poll\Tests;

use Drupal\field_ui\Tests\FieldUiTestTrait;

/**
 * Tests the poll fields.
 *
 * @group poll
 */
class PollFieldTest extends PollTestBase {
  use FieldUiTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field_ui',
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
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Add breadcrumb block.
    $this->drupalPlaceBlock('system_breadcrumb_block');
  }

  /**
   * Test poll entity fields.
   */
  protected function testPollFields() {
    $poll = $this->poll;
    $this->drupalLogin($this->admin_user);
    // Add some fields.
    $this->fieldUIAddNewField('admin/config/content/poll', 'number', 'Number field', 'integer');
    $this->fieldUIAddNewField('admin/config/content/poll', 'text', 'Text field', 'string');
    // Test field form display.
    $this->drupalGet('admin/config/content/poll/form-display');
    $this->assertText('Number field');
    $this->assertText('Text field');
    // Test edit poll form.
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $this->assertText('Number field');
    $this->assertText('Text field');
    $edit = array(
      'field_number[0][value]' => random_int(10, 1000),
      'field_text[0][value]' => $this->randomString(),
    );
    $this->drupalPostForm(NULL, $edit, 'Save');
    // Test view poll form.
    $this->drupalGet('poll/' . $poll->id());
    $this->assertText('Number field');
    $this->assertText('Text field');
  }
}
