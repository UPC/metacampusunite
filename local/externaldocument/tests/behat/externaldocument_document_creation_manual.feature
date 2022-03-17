@ulearn @externaldocument @local_externaldocument
Feature: Documents creation, modification and deletion
    In order to add and manage external documents
    As an external documents manager
    I need to be able to create, modify and delete through a user interface
# ----------------- INDEX -----------------
# - ADD
# - DELETE

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | 1        |
      | searchengine       | solr     |
    And the following config values are set as admin:
      | indexname | moodle_behat | search_solr |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | summary | format |
      | Course 1 | C1 | <p>Course summary</p> | itinerary |
    And I log in as "admin"


  #     ---------------     ADD     ---------------

  @externaldocument_create @javascript
  Scenario: When an external document is added. Then the external document must be shown in the global search results.
    Given I am on "Course 1 Itinerary" course homepage

    And I navigate to "Development > Purge caches" in site administration

    When I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    And I click on "Student 1 (student1@example.com) (0)" "option" in the "select#addselect" "css_element"
    And I click on "Add" "button"
    Then the "removeselect" select box should contain "Student 1 (student1@example.com)"

    When I am on "Module 1" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group2 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    Then the "removeselect" select box should not contain "Student 1 (student1@example.com)"

    When I am on "Module 2 Optional" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    Then the "removeselect" select box should not contain "Student 1 (student1@example.com)"

  @group_user_add @javascript
  Scenario: When user is added to itinerary course group. Then user should be added to non optional course modules linked groups.
    Given the following "groups" exist:
      | name | course | idnumber |
      | Test Group1 | C1 | G1 |
      | Test Group1 | M2 | GM2 |
    And I log in as "admin"
    And I am on "Course 1 Itinerary" course homepage

    When I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    And I click on "Student 1 (student1@example.com) (0)" "option" in the "select#addselect" "css_element"
    And I click on "Add" "button"
    Then the "removeselect" select box should contain "Student 1 (student1@example.com)"

    When I am on "Module 1" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (1)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    Then the "removeselect" select box should contain "Student 1 (student1@example.com)"

    When I am on "Module 2 Optional" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    Then the "removeselect" select box should not contain "Student 1 (student1@example.com)"

  #     ---------------     END     ---------------

  #     ---------------     REMOVE     ---------------

  @group_user_remove @javascript
  Scenario: When user is removed from itinerary course group. Then user should be removed from non optional course modules linked groups.
    Given the following "groups" exist:
      | name | course | idnumber |
      | Test Group1 | C1 | G1 |
      | Test Group1 | M2 | GM2 |
    And the following "group members" exist:
      | user     | group |
      | student1 | GM2   |
    And I log in as "admin"
    And I am on "Course 1 Itinerary" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    And I click on "Student 1 (student1@example.com) (0)" "option" in the "select#addselect" "css_element"
    And I click on "Add" "button"

    When I click on "Student 1 (student1@example.com)" "option" in the "select#removeselect" "css_element"
    And I click on "Remove" "button"

    And I am on "Module 1" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (0)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    Then the "removeselect" select box should not contain "Student 1 (student1@example.com)"

    When I am on "Module 2 Optional" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I click on "Test Group1 (1)" "option" in the "select#groups" "css_element"
    And I click on "Add/remove users" "button"
    Then the "removeselect" select box should contain "Student 1 (student1@example.com)"

  #     ---------------     END     ---------------
