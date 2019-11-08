<?php

namespace Drupal\poll\Tests;

/**
 * Tests the poll admin view.
 *
 * @group poll
 */
class PollViewTest extends PollTestBase {

  /**
   * Test with/without permission and with/without a poll.
   */
  function testAdminView() {
    $this->drupalGet('admin/content/poll');
    $this->assertResponse(200);
    $this->assertText($this->poll->label());

    // Delete all polls, make sure that we get an empty message.
    $this->poll->delete();

    $this->drupalGet('admin/content/poll');
    $this->assertText('No polls are available.');

    $test_user = $this->createUser(['access administration pages']);
    $this->drupalLogin($test_user);
    $this->drupalGet('admin/content/poll');
    $this->assertResponse(403);


  }
}
