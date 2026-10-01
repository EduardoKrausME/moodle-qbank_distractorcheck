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

use Throwable;

/**
 * Coordinates deterministic and semantic review.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class review_service {
    /**
     * Review a question.
     *
     * @param int $questionid Question id.
     * @return array
     */
    public function review_question(int $questionid): array {
        $repository = new question_repository();
        $question = $repository->load($questionid);
        $deterministic = (new deterministic_analyzer())->analyze($question);

        $ai = null;
        $aierror = null;
        try {
            $ai = (new ai_reviewer())->review($question);
        } catch (Throwable $exception) {
            $aierror = get_string('aierror', 'qbank_distractorcheck');
            debugging('qbank_distractorcheck AI review failed: ' . $exception->getMessage(), DEBUG_DEVELOPER);
        }

        $aibyindex = [];
        foreach ($ai ?? [] as $item) {
            $aibyindex[(int)$item['index']] = $item;
        }

        $choices = [];
        foreach ($question['choices'] as $choice) {
            $index = (int)$choice['index'];
            $localfindings = $deterministic['choicefindings'][$index] ?? [];
            $aitem = $aibyindex[$index] ?? null;
            $quality = $aitem['quality'] ?? 'good';
            foreach ($localfindings as $finding) {
                $quality = $this->worst_quality($quality, (string)$finding['status']);
            }
            $choices[] = [
                'index' => $index,
                'number' => $index + 1,
                'text' => (string)$choice['text'],
                'fraction' => format_float((float)$choice['fraction'] * 100, 2) . '%',
                'iscorrect' => !empty($choice['iscorrect']),
                'ispartial' => !empty($choice['ispartial']),
                'quality' => $quality,
                'qualitylabel' => $aitem === null && empty($localfindings)
                    ? get_string('quality_localonly', 'qbank_distractorcheck')
                    : get_string('quality_' . $quality, 'qbank_distractorcheck'),
                'confidence' => $aitem['confidence'] ?? null,
                'confidencelabel' => $aitem ? get_string('confidence_' . $aitem['confidence'], 'qbank_distractorcheck') : null,
                'findings' => array_merge($localfindings, $aitem['findings'] ?? []),
                'hasfindings' => !empty($localfindings) || !empty($aitem['findings']),
                'suggestion' => $aitem['suggestion'] ?? null,
                'hassuggestion' => !empty($aitem['suggestion']),
            ];
        }

        return [
            'question' => $question,
            'deterministic' => $deterministic,
            'choices' => $choices,
            'aierror' => $aierror,
        ];
    }

    /**
     * Suggest a new distractor through the bridge.
     *
     * @param int $questionid Question id.
     * @return array|null
     */
    public function suggest_new_distractor(int $questionid): ?array {
        $question = (new question_repository())->load($questionid);
        try {
            return (new ai_reviewer())->suggest_new_distractor($question);
        } catch (Throwable $exception) {
            debugging('qbank_distractorcheck distractor suggestion failed: ' . $exception->getMessage(), DEBUG_DEVELOPER);
            return null;
        }
    }

    /**
     * Return the most severe quality.
     *
     * @param string $left Quality.
     * @param string $right Quality.
     * @return string
     */
    private function worst_quality(string $left, string $right): string {
        $rank = ['good' => 0, 'weak' => 1, 'problematic' => 2];
        return ($rank[$right] ?? 0) > ($rank[$left] ?? 0) ? $right : $left;
    }
}
