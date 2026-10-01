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
use moodle_exception;

/**
 * Read-only access to multichoice question data.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_repository {
    /**
     * Resolve the context owning a question version.
     *
     * @param int $questionid Question id.
     * @return context
     */
    public function context_for_question(int $questionid): context {
        global $DB;

        $sql = "SELECT qc.contextid
                  FROM {question_versions} qv
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE qv.questionid = :questionid";
        $contextid = $DB->get_field_sql($sql, ['questionid' => $questionid], MUST_EXIST);
        return context::instance_by_id((int)$contextid);
    }

    /**
     * Load and normalize a multichoice question without attempts or student data.
     *
     * @param int $questionid Question id.
     * @return array
     */
    public function load(int $questionid): array {
        global $DB;

        $question = $DB->get_record('question', ['id' => $questionid],
            'id,name,questiontext,questiontextformat,qtype', MUST_EXIST);
        if ($question->qtype !== 'multichoice') {
            throw new moodle_exception('unsupportedqtype', 'qbank_distractorcheck');
        }

        $options = $DB->get_record('qtype_multichoice_options', ['questionid' => $questionid],
            'questionid,single,shuffleanswers,answernumbering', MUST_EXIST);
        $answers = $DB->get_records('question_answers', ['question' => $questionid], 'id ASC',
            'id,answer,answerformat,fraction,feedback,feedbackformat');

        $choices = [];
        $index = 0;
        foreach ($answers as $answer) {
            $fraction = (float)$answer->fraction;
            $single = !empty($options->single);
            $iscorrect = $single ? abs($fraction - 1.0) < 0.0000001 : $fraction > 0.0;
            $ispartial = $single && $fraction > 0.0 && !$iscorrect;
            $choices[] = [
                'index' => $index,
                'text' => self::plain_text((string)$answer->answer),
                'fraction' => $fraction,
                'iscorrect' => $iscorrect,
                'ispartial' => $ispartial,
            ];
            $index++;
        }

        return [
            'id' => (int)$question->id,
            'name' => self::plain_text((string)$question->name),
            'questiontext' => self::plain_text((string)$question->questiontext),
            'single' => !empty($options->single),
            'shuffleanswers' => !empty($options->shuffleanswers),
            'answernumbering' => (string)$options->answernumbering,
            'choices' => $choices,
        ];
    }

    /**
     * Convert stored editor content to bounded plain text for analysis.
     *
     * @param string $text Stored text.
     * @return string
     */
    public static function plain_text(string $text): string {
        $text = preg_replace('/<(br|\/p|\/li|\/div|\/h[1-6])\b[^>]*>/iu', ' ', $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }
}
