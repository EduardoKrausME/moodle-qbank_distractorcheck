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

use core_question\local\bank\question_action_base;
use moodle_url;
use question_bank;
use stdClass;

/**
 * Per-question distractor review action.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class review_action extends question_action_base {
    /** @var string Cached label. */
    protected string $label;

    /**
     * Initialise cached strings.
     */
    public function init(): void {
        parent::init();
        $this->label = get_string('reviewdistractors', 'qbank_distractorcheck');
    }

    /**
     * Position after standard edit and preview actions.
     *
     * @return int
     */
    public function get_menu_position(): int {
        return 360;
    }

    /**
     * Build the action only for accessible multichoice questions.
     *
     * @param stdClass $question Question bank row.
     * @return array
     */
    protected function get_url_icon_and_label(stdClass $question): array {
        if ($question->qtype !== 'multichoice' || !question_bank::is_qtype_installed($question->qtype)) {
            return [null, null, null];
        }
        if (!question_has_capability_on($question, 'view')) {
            return [null, null, null];
        }
        if (!has_capability('qbank/distractorcheck:review', $this->qbank->get_most_specific_context())) {
            return [null, null, null];
        }

        $url = new moodle_url('/question/bank/distractorcheck/review.php', [
            'id' => $question->id,
            'returnurl' => $this->qbank->returnurl,
            'sesskey' => sesskey(),
        ]);

        return [$url, 'i/report', $this->label];
    }
}
