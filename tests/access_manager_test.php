<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace qbank_distractorcheck;

use advanced_testcase;
use context_course;
use qbank_distractorcheck\access_manager;

/**
 * Capability tests.
 * @coversNothing

 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class access_manager_test extends advanced_testcase {
    /**
     * Editing teachers receive the review capability while students do not.
     */
    public function test_review_capability(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $teacher = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();
        $teacherrole = (int)$DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        $studentrole = (int)$DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        role_assign($teacherrole, $teacher->id, $context->id);
        role_assign($studentrole, $student->id, $context->id);

        $this->setUser($teacher);
        $this->assertTrue(access_manager::can_review_context($context));

        $this->setUser($student);
        $this->assertFalse(access_manager::can_review_context($context));
    }
}
