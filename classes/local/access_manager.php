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

namespace qbank_distractorcheck\local;

use context;

/**
 * Capability checks for distractor review.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_manager {
    /**
     * Check the plugin capability in a question bank context.
     *
     * @param context $context Context.
     * @return bool
     */
    public static function can_review_context(context $context): bool {
        return has_capability('qbank/distractorcheck:review', $context);
    }

    /**
     * Require both core question access and the plugin review capability.
     *
     * @param int $questionid Question id.
     * @return context
     */
    public static function require_review_question(int $questionid): context {
        question_require_capability_on($questionid, 'view');
        $repository = new question_repository();
        $context = $repository->context_for_question($questionid);
        require_capability('qbank/distractorcheck:review', $context);
        return $context;
    }
}
