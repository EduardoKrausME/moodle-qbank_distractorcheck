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
 * Review multichoice alternatives and distractors.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');

use core_question\local\bank\helper;
use qbank_distractorcheck\access_manager;
use qbank_distractorcheck\review_service;

require_login();
helper::require_plugin_enabled('qbank_distractorcheck');
require_sesskey();

$questionid = required_param('id', PARAM_INT);
$returnurlparam = optional_param('returnurl', '', PARAM_LOCALURL);
$action = optional_param('action', '', PARAM_ALPHA);
$returnurl = $returnurlparam !== '' ? new moodle_url($returnurlparam) : new moodle_url('/');

$context = access_manager::require_review_question($questionid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/question/bank/distractorcheck/review.php', ['id' => $questionid]));
$PAGE->set_title(get_string('pagetitle', 'qbank_distractorcheck'));
$PAGE->set_heading(get_string('pagetitle', 'qbank_distractorcheck'));
$PAGE->activityheader->disable();

$service = new review_service();
$result = $service->review_question($questionid);
$newsuggestion = null;
$suggestionerror = null;
if ($action === 'suggest' && data_submitted()) {
    $newsuggestion = $service->suggest_new_distractor($questionid);
    if ($newsuggestion === null) {
        $suggestionerror = get_string('suggestionerror', 'qbank_distractorcheck');
    }
}

$questionfindings = [];
foreach ($result['deterministic']['questionfindings'] as $finding) {
    $finding['statuslabel'] = get_string('quality_' . $finding['status'], 'qbank_distractorcheck');
    $questionfindings[] = $finding;
}

$choices = [];
foreach ($result['choices'] as $choice) {
    $findings = [];
    foreach ($choice['findings'] as $finding) {
        $findings[] = [
            'code' => $finding['code'],
            'codelabel' => get_string('findingtype_' . $finding['code'], 'qbank_distractorcheck'),
            'justification' => $finding['justification'],
            'source' => $finding['source'],
            'sourcelabel' => $finding['source'] === 'ai'
                ? get_string('source_ai', 'qbank_distractorcheck')
                : get_string('source_local', 'qbank_distractorcheck'),
        ];
    }
    $choice['findings'] = $findings;
    if ($choice['iscorrect']) {
        $choice['correctlabel'] = get_string('markedcorrect', 'qbank_distractorcheck');
    } else if (!empty($choice['ispartial'])) {
        $choice['correctlabel'] = get_string('partialcredit', 'qbank_distractorcheck');
    } else {
        $choice['correctlabel'] = get_string('markedincorrect', 'qbank_distractorcheck');
    }
    $choices[] = $choice;
}

$template = [
    'questionname' => $result['question']['name'],
    'questiontext' => $result['question']['questiontext'],
    'answeringmode' => $result['question']['single']
        ? get_string('singleanswer', 'qbank_distractorcheck')
        : get_string('multipleanswer', 'qbank_distractorcheck'),
    'choicecount' => $result['deterministic']['choicecount'],
    'correctcount' => $result['deterministic']['correctcount'],
    'questionfindings' => $questionfindings,
    'hasquestionfindings' => !empty($questionfindings),
    'choices' => $choices,
    'aierror' => $result['aierror'],
    'hasaierror' => !empty($result['aierror']),
    'suggestion' => $newsuggestion,
    'hassuggestion' => !empty($newsuggestion),
    'suggestionerror' => $suggestionerror,
    'hassuggestionerror' => !empty($suggestionerror),
    'suggesturl' => (new moodle_url('/question/bank/distractorcheck/review.php'))->out(false),
    'questionid' => $questionid,
    'returnurl' => $returnurl->out(false),
    'sesskey' => sesskey(),
    'backurl' => $returnurl->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('qbank_distractorcheck/review', $template);
echo $OUTPUT->footer();
