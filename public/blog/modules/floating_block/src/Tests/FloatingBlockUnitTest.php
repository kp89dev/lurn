<?php

/**
 * @file
 * Definition of Drupal\floating_block\Tests\FloatingBlockUnitTest.
 */

namespace Drupal\floating_block\Tests;

use Drupal\simpletest\WebTestBase;

class FloatingBlockUnitTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('block', 'floating_block');

  public static function getInfo() {
    return array(
      'name' => 'Floating block settings',
      'description' => 'Test the _floating_block_admin_convert_array_to_text() and _floating_block_admin_convert_text_to_array() functions.',
      'group' => 'Floating block',
    );
  }

  /**
   * Enable modules and create user with specific permissions.
   */
  function setUp() {
    module_load_include('inc', 'floating_block', 'floating_block.admin');
    parent::setUp();
  }

  /**
   * Tests block_example functionality.
   */
  function testBlockExampleBasic() {
    // Test an empty string returns an empty array.
    $floating_block_text = '';
    $floating_block_array = _floating_block_admin_convert_text_to_array($floating_block_text);
    $this->assertEqual($floating_block_array, array());
    $this->assertEqual(_floating_block_admin_convert_array_to_text($floating_block_array), $floating_block_text);

    // Test a single line containing a class.
    $floating_block_text = '.block-1';
    $floating_block_array = _floating_block_admin_convert_text_to_array($floating_block_text);
    $this->assertEqual($floating_block_array, array('.block-1' => array()));
    $this->assertEqual(_floating_block_admin_convert_array_to_text($floating_block_array), $floating_block_text);

    // Test a single line containing a class and 1 extra setting.
    $floating_block_text = '.block-1|container=#main';
    $floating_block_array = _floating_block_admin_convert_text_to_array($floating_block_text);
    $this->assertEqual($floating_block_array, array('.block-1' => array('container' => '#main')));
    $this->assertEqual(_floating_block_admin_convert_array_to_text($floating_block_array), $floating_block_text);

    // Test a single line containing a class and multiple extra setting.
    $floating_block_text = '.block-1|container=#main,padding_top=8,padding_bottom=4';
    $floating_block_array = _floating_block_admin_convert_text_to_array($floating_block_text);
    $this->assertEqual($floating_block_array, array('.block-1' => array('container' => '#main', 'padding_top' => '8', 'padding_bottom' => '4')));
    $this->assertEqual(_floating_block_admin_convert_array_to_text($floating_block_array), $floating_block_text);

    // Test mutliple line configuration.
    $floating_block_text = ".block-1|container=#main,padding_top=8\n.block-2";
    $floating_block_array = _floating_block_admin_convert_text_to_array($floating_block_text);
    $this->assertEqual($floating_block_array, array('.block-1' => array('container' => '#main', 'padding_top' => '8'), '.block-2' => array()));
    $this->assertEqual(_floating_block_admin_convert_array_to_text($floating_block_array), $floating_block_text);
  }

}
