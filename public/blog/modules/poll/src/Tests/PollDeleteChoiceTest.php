<?php

namespace Drupal\poll\Tests;

/**
 * Tests the removal of poll choices.
 *
 * @group poll
 */
class PollDeleteChoiceTest extends PollTestBase {

  /**
   * Tests removing a choice from a poll.
   */
  function testChoiceRemoval() {
    // Set up a poll with three choices.
    $this->assertTrue($this->poll->id(), 'Poll for choice deletion logic test created.');

    $ids = \Drupal::entityQuery('poll_choice')
      ->condition('choice', $this->poll->choice[0]->entity->label())
      ->execute();
    $this->assertEqual(count($ids), 1, 'Choice 1 exists in the database');

    // Record a vote for the second choice.
    $edit = array(
      'choice' => $this->poll->choice[1]->target_id,
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));

    // Assert the selected option.
    $xml = $this->xpath('//dt[text()=:choice]/following-sibling::dd[1]/div', [':choice' => $this->poll->choice[1]->entity->label()]);
    $this->assertEqual(1, $xml[0]['data-value']);

    // Edit the poll, and try to delete first poll choice.
    $this->drupalGet("poll/" . $this->poll->id() . "/edit");
    $edit = ['choice[0][choice]' => ''];
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Click on the poll title to go to poll page.
    $this->drupalGet('admin/content/poll');
    $this->clickLink($this->poll->label());

    // Check the first poll choice is deleted, while the others remain.
    $this->assertNoText($this->poll->choice[0]->entity->label(), 'First choice removed.');
    $this->assertText($this->poll->choice[1]->entity->label(), 'Second choice remains.');
    $this->assertText($this->poll->choice[2]->entity->label(), 'Third choice remains.');

    $ids = \Drupal::entityQuery('poll_choice')
      ->condition('choice', $this->poll->choice[0]->entity->label())
      ->execute();
    $this->assertEqual(count($ids), 0, 'Choice 1 has been deleted in the database');

    // Ensure that the existing vote still shows.
    $this->drupalGet('poll/' . $this->poll->id());
    $vote = $this->poll->choice[1]->target_id;
    $vote_recorded = db_query('SELECT chid FROM {poll_vote} WHERE chid = :chid', array(':chid' => $vote))->fetch();
    $this->assertFalse(empty($vote_recorded), 'Vote in Choice 2 still in the database');

    // Assert the selected option.
    $xml = $this->xpath('//dt[text()=:choice]/following-sibling::dd[1]/div', [':choice' => $this->poll->choice[1]->entity->label()]);
    $this->assertEqual(1, $xml[0]['data-value']);

    // Edit the poll, and try to delete first poll choice.
    $this->drupalGet("poll/" . $this->poll->id() . "/edit");
    $edit = [
      'choice[0][choice]' => '',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Click on the poll title to go to poll page.
    $this->drupalGet('admin/content/poll');
    $this->clickLink($this->poll->label());

    // Check the poll choice (which had a vote) is deleted.
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(isset($elements[0]), "vote deleted successfully");

    // Assert that the existing vote has been deleted from the database.
    $vote_deleted = db_query('SELECT chid FROM {poll_vote} WHERE chid = :chid', array(':chid' => $vote))->fetch();
    $this->assertTrue(empty($vote_deleted), 'Vote in Choice 2 has been deleted from the database');
  }

}
