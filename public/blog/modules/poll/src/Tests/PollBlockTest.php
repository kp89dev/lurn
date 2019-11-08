<?php

namespace Drupal\poll\Tests;

/**
 * Tests the recent poll block.
 *
 * @group poll
 */
class PollBlockTest extends PollTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('block');

  function setUp() {
    parent::setUp();

    // Enable the recent poll block.
    $this->drupalPlaceBlock('poll_recent_block');
  }

  /**
   * Tests creating, viewing, voting on recent poll block.
   */
  function testRecentBlock() {

    $poll = $this->poll;
    $user = $this->web_user;

    // Verify poll appears in a block.
    $this->drupalLogin($user);
    $this->drupalGet('user');

    // If a 'block' view not generated, this title would not appear even though
    // the choices might.
    $this->assertText($poll->label());
    $options = $poll->getOptions();
    foreach ($options as $option) {
      $this->assertText($option, 'Poll option appears in block.');
    }

    // Verify we can vote via the block.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostForm('user/' . $this->web_user->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote has been recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');

    // Close the poll and verify block doesn't appear.
    $poll->close();
    $poll->save();
    $this->drupalGet('user/' . $user->id());
    $this->assertNoText($poll->label(), 'Poll no longer appears in block.');
  }
}
