<?php

namespace Drupal\poll\Tests;

use Drupal\Core\Database\Database;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * Tests voting on a poll.
 *
 * @group poll
 */
class PollVoteTest extends PollTestBase {

  /**
   * Tests voting on a poll.
   */
  function testPollVote() {

    $this->drupalLogin($this->web_user);

    // Record a vote for the first choice.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel your vote' button appears.");

//    $this->drupalGet('poll/' . $this->poll->id() . '/votes');
//    $this->assertText(t('This table lists all the recorded votes for this poll. If anonymous users are allowed to vote, they will be identified by the IP address of the computer they used when they voted.'), 'Vote table text.');
//    $options = $this->poll->getOptions();
//    debug($options);

   // $this->assertText($this->poll->getOptions()[0], 'Vote recorded');

    // Ensure poll listing page has correct number of votes.
//    $this->drupalGet('poll');
//    $this->assertText($this->poll->label(), 'Poll appears in poll list.');
//    $this->assertText('1 vote', 'Poll has 1 vote.');

    // Cancel a vote.
    $this->drupalPostForm('poll/' . $this->poll->id(), array(), t('Cancel vote'));
    $this->assertText('Your vote was cancelled.', 'Your vote was cancelled.');
    $this->assertNoText('Cancel your vote', "Cancel vote button doesn't appear.");

//    $this->drupalGet('poll/' . $this->poll->id() . '/votes');
//    $this->assertNoText($choices[0], 'Vote cancelled');

    // Ensure poll listing page has correct number of votes.
//    $this->drupalGet('poll');
//    $this->assertText($title, 'Poll appears in poll list.');
//    $this->assertText('0 votes', 'Poll has 0 votes.');

    // Log in as a user who can only vote on polls.
//    $this->drupalLogout();
//    $this->drupalLogin($restricted_vote_user);

    // Empty vote on a poll.
    $this->drupalPostForm('poll/' . $this->poll->id(), [], t('Vote'));
    $this->assertText('Your vote could not be recorded because you did not select any of the choices.');
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(isset($elements[0]), "'Vote' button appears.");

    // Vote on a poll.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel your vote"]');
    $this->assertTrue(empty($elements), "'Cancel your vote' button does not appear.");

    $this->drupalLogin($this->admin_user);

    $this->drupalGet('admin/content/poll');
    $this->assertText($this->poll->label());

    // Test for the overview page.
    $field_status = $this->xpath('//table/tbody/tr[1]');
    $active = (string) $field_status[0]->td[1];
    $this->assertEqual(trim($active), 'Yes');

    $anonymous_votes = trim((string) $field_status[0]->td[2]);
    $this->assertEqual($anonymous_votes, 'Off');

    // Edit the poll.
    $this->clickLink($this->poll->label());
    $this->clickLink('Edit');

    // Add the runtime date and allow anonymous to vote.
    $edit = array(
      'runtime' => 172800,
      'anonymous_vote_allow[value]' => TRUE,
    );

    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Assert that editing was successful.
    $this->assertText('The poll ' . $this->poll->label() . ' has been updated.');

    // Check if the active label is correct.
    $field_status = $this->xpath('//table/tbody/tr[1]');
    $active = trim((string) $field_status[0]->td[1]);
    $date = \Drupal::service('date.formatter')->format($this->poll->getCreated() + 172800, 'short');
    $output = 'Yes (until ' . rtrim(strstr($date, '-', TRUE)) . ')';
    $this->assertEqual($active, $output);

    // Check if allow anonymous voting is on.
    $anonymous_votes = trim((string) $field_status[0]->td[2]);
    $this->assertEqual($anonymous_votes, 'On');

    // Check the number of total votes.
    $total_votes = trim((string) $field_status[0]->td[4]);
    $this->assertEqual($total_votes, '1');

    // Add permissions to anonymous user to view polls.
    /** @var \Drupal\user\RoleInterface $anonymous_role */
    $anonymous_role = Role::load(RoleInterface::ANONYMOUS_ID);
    $anonymous_role->grantPermission('access polls');
    $anonymous_role->save();

    // Let the anonymous user to vote.
    $this->drupalLogout();
    $edit = ['choice' => '1'];
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));

    // Login as admin and check the number of total votes on the overview page.
    $this->drupalLogin($this->admin_user);
    $this->drupalGet('admin/content/poll');
    $xpath = "//tr[1]/td[@class='views-field views-field-votes']";
    $this->assertFieldByXPath($xpath, 2);

    // Cancel the vote from the user, ensure that backend updates.
    $this->drupalLogin($this->web_user);
    $this->drupalPostForm('poll/' . $this->poll->id(), [], t('Cancel vote'));
    $this->assertText(t('Your vote was cancelled.'));

    // Login as admin and check the number of total votes on the overview page.
    $this->drupalLogin($this->admin_user);
    $this->drupalGet('admin/content/poll');
    $xpath = "//tr[1]/td[@class='views-field views-field-votes']";
    $this->assertFieldByXPath($xpath, 1);
  }

  /**
   * Test that anonymous user just remove it's own vote.
   */
  protected function testAnonymousCancelVote() {
    // Now grant anonymous users permission to view the polls, vote and delete
    // it's own vote.
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, array('cancel own vote', 'access polls'));
    $this->poll->setAnonymousVoteAllow(TRUE)->save();
    $this->drupalLogout();
    // First anonymous user votes.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));

    // Change the IP of first user.
    Database::getConnection()->update('poll_vote')
      ->fields(array('hostname' => '240.0.0.1'))
      ->condition('uid', \Drupal::currentUser()->id())
      ->execute();

    // Logged user votes.
    $this->drupalLogin($this->web_user);
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText(t('Total votes:  @votes', array('@votes' => 2)), 'Vote did correctly.');

    // Second anonymous user votes from same IP than the logged.
    $this->drupalLogout();
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText(t('Total votes:  @votes', array('@votes' => 3)), 'Vote did correctly.');

    // Second anonymous user cancels own vote.
    $this->drupalPostForm(NULL, array(), t('Cancel vote'));

    // Vote again to see the results, resulting in three votes again.
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText(t('Total votes:  @votes', array('@votes' => 3)), 'Just your vote deleted.');
  }

}
