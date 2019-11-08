<?php

namespace Drupal\poll\Tests;

use Drupal\Core\Session\AccountInterface;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\poll\Entity\Poll;


/**
 * Tests multilingual voting on a poll.
 *
 * @group poll
 */
class PollVoteMultilingualTest extends PollTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'language',
    'content_translation',
  ];

  /**
   * {@inheritdoc}
   */
  protected $adminPermissions = [
    'administer content translation',
    'administer languages',
    'create content translations',
    'update content translations',
    'translate any entity',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Allow anonymous users to vote on polls.
    user_role_change_permissions(AccountInterface::ANONYMOUS_ROLE, array(
      'cancel own vote' => TRUE,
      'access polls' => TRUE,
    ));

    $this->poll = $this->pollCreate(3);

    $this->poll->setAnonymousVoteAllow(TRUE)->save();
  }

  /**
   * Tests multilingual voting on a poll.
   */
  protected function testPollVoteMultilingual() {

    $this->drupalLogin($this->admin_user);

    // Add another language.
    $language = ConfigurableLanguage::createFromLangcode('ca');
    $language->save();

    // Make poll translatable.
    $this->drupalGet('admin/config/regional/content-language');
    $edit = array(
      'entity_types[poll]' => TRUE,
      'entity_types[poll_choice]' => TRUE,
      'settings[poll][poll][translatable]' => TRUE,
      'settings[poll_choice][poll_choice][translatable]' => TRUE,
    );
    $this->drupalPostForm(NULL, $edit, t('Save configuration'));
    \Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();

    // Translate a poll.
    $this->drupalGet('poll/' . $this->poll->id() . '/translations');
    $this->clickLink(t('Add'));
    $edit = array(
      'question[0][value]' => 'ca question',
      'choice[0][choice]' => 'ca choice 1',
      'choice[1][choice]' => 'ca choice 2',
      'choice[2][choice]' => 'ca choice 3',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->drupalGet('ca/poll/' . $this->poll->id());
    $this->assertText('ca choice 1');

    \Drupal::entityTypeManager()->getStorage('poll')->resetCache();
    \Drupal::entityTypeManager()->getStorage('poll_choice')->resetCache();
    $this->poll = Poll::load($this->poll->id());

    // Login as web user.
    $this->drupalLogin($this->web_user);

    // Record a vote.
    $edit = array(
      'choice' => $this->getChoiceId($this->poll, 2),
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');

    $this->drupalGet('ca/poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");

    // Cancel a vote.
    $this->drupalPostForm('poll/' . $this->poll->id(), array(), t('Cancel vote'));
    $this->assertText('Your vote was cancelled.', 'Your vote was cancelled.');
    $this->assertNoText('Cancel your vote', "Cancel vote button doesn't appear.");

    // Vote again in reverse order.
    $edit = array(
      'choice' => $this->getChoiceIdByLabel($this->poll->getTranslation('ca'), 'ca choice 2'),
    );
    $this->drupalPostForm('ca/poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');

    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");

    // Edit the original poll.
    $this->drupalLogin($this->admin_user);
    $this->drupalGet('poll/' . $this->poll->id() . '/edit');
    $edit = array(
      'choice[0][choice]' => '',
      'choice[1][choice]' => 'choice 2',
      'choice[2][choice]' => 'choice 3',
      'choice[3][choice]' => 'choice 4',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Translate the new label.
    $this->drupalGet('ca/poll/' . $this->poll->id() . '/edit');
    $edit = array(
      'choice[2][choice]' => 'ca choice 4',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    \Drupal::entityTypeManager()->getStorage('poll')->resetCache();
    \Drupal::entityTypeManager()->getStorage('poll_choice')->resetCache();
    $this->poll = Poll::load($this->poll->id());

    // Vote as anonymous user.
    $this->drupalLogout();
    $edit = array(
      'choice' => $this->getChoiceIdByLabel($this->poll->getTranslation('ca'), 'ca choice 4'),
    );
    $this->drupalPostForm('ca/poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  2', 'Vote count updated correctly.');
    $this->assertNoText('ca choice 1');
    $this->assertText('ca choice 4');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[1]')[0];
    $this->assertEqual($elements->div[1], '50% (1 vote)');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[3]')[0];
    $this->assertEqual($elements->div[1], '50% (1 vote)');

    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");
    $this->assertText('Total votes:  2', 'Vote count updated correctly.');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[1]')[0];
    $this->assertEqual($elements->div[1], '50% (1 vote)');
    $elements = $this->xpath('//*[@id="poll-view-form-2"]/div[1]/dl/dd[3]')[0];
    $this->assertEqual($elements->div[1], '50% (1 vote)');
  }

}
