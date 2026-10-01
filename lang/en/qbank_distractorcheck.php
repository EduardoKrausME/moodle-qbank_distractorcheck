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

/**
 * English strings for qbank_distractorcheck.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['aierror'] = 'The semantic AI review could not be validated or completed. Deterministic findings are still shown.';
$string['alternative'] = 'Alternative';
$string['backtoquestionbank'] = 'Back to question bank';
$string['bridgeunavailable'] = 'The required AI bridge is unavailable.';
$string['choice'] = '#';
$string['choices'] = 'choices';
$string['confidence'] = 'Confidence';
$string['confidence_high'] = 'High';
$string['confidence_low'] = 'Low';
$string['confidence_medium'] = 'Medium';
$string['distractorcheck:review'] = 'Review multiple-choice distractors';
$string['finding_choicecount'] = 'The question has {$a} alternatives; a multiple-choice question should have at least two.';
$string['finding_duplicate'] = 'This alternative is literally duplicated by alternative(s) {$a} after case and whitespace normalization.';
$string['finding_emptychoice'] = 'This alternative is empty after text normalization.';
$string['finding_fractionsum'] = 'For this multiple-answer question, the positive fractions sum to {$a} instead of 1.0000.';
$string['finding_lengthoutlier'] = 'This alternative has {$a->length} characters while the median alternative length is about {$a->median}, which may create a visual clue.';
$string['finding_nocorrect'] = 'No alternative has a positive fraction, so no answer is currently marked correct.';
$string['finding_singlecorrectcount'] = 'This is a single-answer question but {$a} alternatives have a positive fraction.';
$string['findings'] = 'Findings and justification';
$string['findingtype_absurd_choice'] = 'Implausible or absurd alternative';
$string['findingtype_all_none_of_above'] = 'All/none of the above';
$string['findingtype_also_correct'] = 'Possibly also correct';
$string['findingtype_empty_choice'] = 'Empty alternative';
$string['findingtype_grammatical_clue'] = 'Grammatical clue';
$string['findingtype_invalid_choice_count'] = 'Invalid number of alternatives';
$string['findingtype_length_outlier'] = 'Length discrepancy';
$string['findingtype_literal_duplicate'] = 'Literal duplicate';
$string['findingtype_no_correct_choice'] = 'No correct alternative';
$string['findingtype_other'] = 'Other semantic issue';
$string['findingtype_plausibility'] = 'Plausibility';
$string['findingtype_positive_fraction_sum'] = 'Fraction sum';
$string['findingtype_relation_to_stem'] = 'Relation to the stem';
$string['findingtype_semantic_duplicate'] = 'Semantic duplicate';
$string['findingtype_single_correct_count'] = 'Single-answer grading conflict';
$string['findingtype_wording_clue'] = 'Wording clue';
$string['grading'] = 'Current grading';
$string['markedcorrect'] = 'Marked correct';
$string['markedcorrectcount'] = 'marked correct';
$string['markedincorrect'] = 'Marked incorrect';
$string['multipleanswer'] = 'Multiple-answer question';
$string['neversavedautomatically'] = 'This suggestion has not been written to the question bank.';
$string['newdistractor'] = 'New distractor suggestion';
$string['newdistractor_help'] = 'Request one additional plausible distractor. It is displayed for teacher review and is never saved automatically.';
$string['nofindings'] = 'No finding';
$string['pagetitle'] = 'Distractor quality review';
$string['partialcredit'] = 'Partial credit';
$string['pluginname'] = 'Distractor quality check';
$string['privacy:metadata'] = 'The Distractor quality check plugin stores no personal data.';
$string['quality_good'] = 'Good';
$string['quality_localonly'] = 'No local problem detected';
$string['quality_problematic'] = 'Problematic';
$string['quality_weak'] = 'Weak';
$string['reviewdistractors'] = 'Review distractors';
$string['singleanswer'] = 'Single-answer question';
$string['source_ai'] = 'AI';
$string['source_local'] = 'local rule';
$string['status'] = 'Status';
$string['structuralfindings'] = 'Deterministic findings';
$string['suggestion'] = 'Suggested improvement';
$string['suggestionerror'] = 'A new distractor could not be generated or validated.';
$string['suggestnewdistractor'] = 'Suggest a new distractor';
$string['unsupportedqtype'] = 'This tool only supports multiple-choice questions.';
