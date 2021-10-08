@block @block_course_activities
Feature: The course activities block displays student completion status of all the activities in a course
  In order to display course activities block in a course
  As a manager
  I can add course activities block in a course and add activities

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name           | externalurl                 |
      | url     | C1     | url1    | Test url name | https://moodle.org |

  Scenario: Add the course activities block to a the course and add course completion items
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
	  | Name | External URL |
      | Activity completion | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "cmdid : 1 | name : Test url name | createddate : 08-Oct-2021 Status: - " in the "Course activities" block

  Scenario: Add the block to a the course and add course completion items
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
	  | Assignment name | Test assignment |
      | Activity completion | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I check "Mark as completed" checkbox in  "Test url name" activity
    And I reload the page
    And I should see "cmdid : 1 | name : Test url name | createddate : 08-Oct-2021 Status: - Completed" in the "Course activities" block