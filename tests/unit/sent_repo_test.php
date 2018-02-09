<?php
 
require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\repos\sent_repo;
use block_quickmail\persistents\message;

class block_quickmail_sent_repo_testcase extends advanced_testcase {
    
    use has_general_helpers,
        sets_up_courses;

    public function test_get_for_user()
    {
        $this->resetAfterTest(true);

        // create 3 sents for user id: 1
        $sent1 = $this->create_message();
        $sent2 = $this->create_message();
        $sent3 = $this->create_message();
        
        // create 2 sents for user id: 2
        $sent4 = $this->create_message();
        $sent4->set('user_id', 2);
        $sent4->update();
        $sent5 = $this->create_message();
        $sent5->set('user_id', 2);
        $sent5->update();

        // create a non-sent message for user id: 1
        $sent6 = $this->create_message(false);

        // create a message for user: 1, course: 2
        $sent7 = $this->create_message();
        $sent7->set('course_id', 2);
        $sent7->update();

        // get all sents for user: 1
        $sents = sent_repo::get_for_user(1);

        $this->assertCount(4, $sents);

        // get all sents for user: 1, course: 1
        $sents = sent_repo::get_for_user(1, 1);

        $this->assertCount(3, $sents);

        // get all sents for user: 1, course: 2
        $sents = sent_repo::get_for_user(1, 2);

        $this->assertCount(1, $sents);
    }

    public function test_sorts_get_for_user()
    {
        $this->resetAfterTest(true);

        $this->create_test_sents();

        // get all sents for user: 1
        $sents = sent_repo::get_for_user(1);

        $this->assertCount(7, $sents);
        $this->assertEquals('date', $sents[0]->get('subject'));

        // sort by id
        $sents = sent_repo::get_for_user(1, 0, 'id', 'asc');
        $this->assertEquals(142000, $sents[0]->get('id'));

        $sents = sent_repo::get_for_user(1, 0, 'id', 'desc');
        $this->assertEquals(142006, $sents[0]->get('id'));

        // sort by course
        $sents = sent_repo::get_for_user(1, 0, 'course', 'asc');
        $this->assertEquals(1, $sents[0]->get('course_id'));

        $sents = sent_repo::get_for_user(1, 0, 'course', 'desc');
        $this->assertEquals(5, $sents[0]->get('course_id'));

        // sort by subject
        $sents = sent_repo::get_for_user(1, 0, 'subject', 'asc');
        $this->assertEquals('apple', $sents[0]->get('subject'));

        $sents = sent_repo::get_for_user(1, 0, 'subject', 'desc');
        $this->assertEquals('grape', $sents[0]->get('subject'));

        // sort by (time) created
        $sents = sent_repo::get_for_user(1, 0, 'created', 'asc');
        $this->assertEquals(1111111111, $sents[0]->get('timecreated'));

        $sents = sent_repo::get_for_user(1, 0, 'created', 'desc');
        $this->assertEquals(8888888888, $sents[0]->get('timecreated'));

        // sort by (time) modified
        $sents = sent_repo::get_for_user(1, 0, 'modified', 'asc');
        $this->assertEquals(1010101010, $sents[0]->get('timemodified'));

        $sents = sent_repo::get_for_user(1, 0, 'modified', 'desc');
        $this->assertEquals(5454545454, $sents[0]->get('timemodified'));
    }

    public function test_sorts_get_for_user_and_course()
    {
        $this->resetAfterTest(true);

        $this->create_test_sents();

        // get all sents for user: 1, course: 1
        $sents = sent_repo::get_for_user(1, 1);
        $this->assertCount(4, $sents);
        $this->assertEquals('date', $sents[0]->get('subject'));

        // sort by id
        $sents = sent_repo::get_for_user(1, 1, 'id', 'asc');
        $this->assertEquals(142000, $sents[0]->get('id'));

        $sents = sent_repo::get_for_user(1, 1, 'id', 'desc');
        $this->assertEquals(142006, $sents[0]->get('id'));

        // sort by course
        $sents = sent_repo::get_for_user(1, 1, 'course', 'asc');
        $this->assertEquals(1, $sents[0]->get('course_id'));

        $sents = sent_repo::get_for_user(1, 1, 'course', 'desc');
        $this->assertEquals(1, $sents[0]->get('course_id'));

        // sort by subject
        $sents = sent_repo::get_for_user(1, 1, 'subject', 'asc');
        $this->assertEquals('apple', $sents[0]->get('subject'));

        $sents = sent_repo::get_for_user(1, 1, 'subject', 'desc');
        $this->assertEquals('fig', $sents[0]->get('subject'));

        // sort by (time) created
        $sents = sent_repo::get_for_user(1, 1, 'created', 'asc');
        $this->assertEquals(1111111111, $sents[0]->get('timecreated'));

        $sents = sent_repo::get_for_user(1, 1, 'created', 'desc');
        $this->assertEquals(8888888888, $sents[0]->get('timecreated'));

        // sort by (time) modified
        $sents = sent_repo::get_for_user(1, 1, 'modified', 'asc');
        $this->assertEquals(1010101010, $sents[0]->get('timemodified'));

        $sents = sent_repo::get_for_user(1, 1, 'modified', 'desc');
        $this->assertEquals(5454545454, $sents[0]->get('timemodified'));
    }

    ///////////////////////////////////////////////
    ///
    /// HELPERS
    /// 
    //////////////////////////////////////////////
    
    private function create_message($is_sent = true)
    {
        return message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'sent_at' => $is_sent ? time() : 0
        ]);
    }

    private function create_test_sents()
    {
        global $DB;

        // id: 142000
        $sent1 = $this->create_message();
        $sent1->set('course_id', 1);
        $sent1->set('subject', 'date');
        $sent1->update();
        $sent = $sent1->to_record();
        $sent->timecreated = 8888888888;
        $sent->timemodified = 3232323232;
        $DB->update_record('block_quickmail_messages', $sent);

        // id: 142001
        $sent2 = $this->create_message();
        $sent2->set('course_id', 5);
        $sent2->set('subject', 'elderberry');
        $sent2->update();
        $sent = $sent2->to_record();
        $sent->timecreated = 4444444444;
        $sent->timemodified = 5252525252;
        $DB->update_record('block_quickmail_messages', $sent);

        // id: 142002
        $sent3 = $this->create_message();
        $sent3->set('course_id', 3);
        $sent3->set('subject', 'coconut');
        $sent3->update();
        $sent = $sent3->to_record();
        $sent->timecreated = 7777777777;
        $sent->timemodified = 1919191919;
        $DB->update_record('block_quickmail_messages', $sent);

        // id: 142003
        $sent4 = $this->create_message();
        $sent4->set('course_id', 1);
        $sent4->set('subject', 'apple');
        $sent4->update();
        $sent = $sent4->to_record();
        $sent->timecreated = 1111111111;
        $sent->timemodified = 5454545454;
        $DB->update_record('block_quickmail_messages', $sent);

        // id: 142004
        $sent5 = $this->create_message();
        $sent5->set('course_id', 1);
        $sent5->set('subject', 'banana');
        $sent5->update();
        $sent = $sent5->to_record();
        $sent->timecreated = 2222222222;
        $sent->timemodified = 3333333333;
        $DB->update_record('block_quickmail_messages', $sent);

        // id: 142005
        $sent6 = $this->create_message();
        $sent6->set('course_id', 2);
        $sent6->set('subject', 'grape');
        $sent6->update();
        $sent = $sent6->to_record();
        $sent->timecreated = 1212121212;
        $sent->timemodified = 2525252525;
        $DB->update_record('block_quickmail_messages', $sent);

        // id: 142006
        $sent7 = $this->create_message();
        $sent7->set('course_id', 1);
        $sent7->set('subject', 'fig');
        $sent7->update();
        $sent = $sent7->to_record();
        $sent->timecreated = 3434343434;
        $sent->timemodified = 1010101010;
        $DB->update_record('block_quickmail_messages', $sent);
    }

}