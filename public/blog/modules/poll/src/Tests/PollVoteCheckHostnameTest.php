<?php

namespace Drupal\poll\Tests;

use Drupal\Core\Session\AccountInterface;

/**
 * Check that users and anonymous users from specified ip-address can only vote once.
 *
 * @group poll
 */
class PollVoteCheckHostnameTest extends PollTestBase {

  function setUp() {
    parent::setUp();

    // Allow anonymous users to vote on polls.
    user_role_change_permissions(AccountInterface::ANONYMOUS_ROLE, array(
      // 'vote on polls' => TRUE,
      'cancel own vote' => TRUE,
      'access polls' => TRUE,
    ));

    $this->poll->setAnonymousVoteAllow(TRUE)->save();
  }

  /**
   * Checks that anonymous users with the same IP address can only vote once.
   *
   * Also checks that authenticated users can only vote once, even when the
   * user's IP address has changed.
   */
  function testHostnamePollVote() {

    $web_user2 = $this->drupalCreateUser(array('access polls'));
    // Login User1.
    $this->drupalLogin($this->web_user);

    $edit = array(
      'choice' => '1',
    );

    //  $this->web_user->getUserName();
    // User1 vote on Poll.
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText(t('Your vote has been recorded.'), format_string('%user vote was recorded.', array('%user' => $this->web_user->getUserName())));
    $this->assertText(t('Total votes:  @votes', array('@votes' => 1)), 'Vote count updated correctly.');

    // Check to make sure User1 cannot vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), format_string("%user is not able to vote again.", array('%user' => $this->web_user->getUserName())));
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Logout User1.
    $this->drupalLogout();

    // Fill the page cache by requesting the poll.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertEqual($this->drupalGetHeader('x-drupal-cache'), 'MISS', 'Page was cacheable but was not in the cache.');
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertEqual($this->drupalGetHeader('x-drupal-cache'), 'HIT', 'Page was cached.');

    // Anonymous user vote on Poll.
    $this->drupalPostForm(NULL, $edit, t('Vote'));
    $this->assertText(t('Your vote has been recorded.'), 'Anonymous vote was recorded.');
    $this->assertText(t('Total votes:  @votes', array('@votes' => 2)), 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Check to make sure Anonymous user cannot vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertFalse($this->drupalGetHeader('x-drupal-cache'), 'Page was not cacheable.');
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), "Anonymous is not able to vote again.");
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Login User2.
    $this->drupalLogin($web_user2);

    // User2 vote on poll.
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText(t('Your vote has been recorded.'), format_string('%user vote was recorded.', array('%user' => $web_user2->getUserName())));
    $this->assertText(t('Total votes:  @votes', array('@votes' => 3)), 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(empty($elements), "'Cancel vote' button does not appear.");

    // Logout User2.
    $this->drupalLogout();

    // Change host name for anonymous users.
    db_update('poll_vote')
      ->fields(array(
        'hostname' => '123.456.789.1',
      ))
      ->condition('hostname', '', '<>')
      ->execute();

    // Check to make sure Anonymous user can vote again with a new session after
    // a hostname change.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertEqual($this->drupalGetHeader('x-drupal-cache'), 'HIT', 'Cached page return.');
    $this->drupalPostForm(NULL, $edit, t('Vote'));
    $this->assertText(t('Your vote has been recorded.'), format_string('%user vote was recorded.', array('%user' => $web_user2->getUserName())));
    $this->assertText(t('Total votes:  @votes', array('@votes' => 4)), 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Check to make sure Anonymous user cannot vote again with a new session,
    // and that the vote from the previous session cannot be cancelled.
    $this->curlClose();
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertEqual($this->drupalGetHeader('x-drupal-cache'), 'HIT', 'Page was cacheable but was not in the cache.');
    $this->drupalPostForm(NULL, $edit, t('Vote'));
    $this->assertText(t('Your vote for this poll has already been submitted.'));
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(empty($elements), "'Cancel vote' button does not appear.");

    // Login User1.
    $this->drupalLogin($this->web_user);

    // Check to make sure User1 still cannot vote even after hostname changed.
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), format_string("%user is not able to vote again.", array('%user' => $this->web_user->getUserName())));
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");
  }
}
