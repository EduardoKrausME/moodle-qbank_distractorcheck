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

use core_text;
use invalid_parameter_exception;

/**
 * Strict parser for untrusted AI responses.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_response_parser {
    /** @var string[] */
    private const QUALITIES = ['good', 'weak', 'problematic'];
    /** @var string[] */
    private const CONFIDENCES = ['low', 'medium', 'high'];
    /** @var string[] */
    private const FINDINGS = [
        'plausibility',
        'relation_to_stem',
        'grammatical_clue',
        'semantic_duplicate',
        'absurd_choice',
        'also_correct',
        'all_none_of_above',
        'wording_clue',
        'other',
    ];

    /**
     * Parse the per-choice review response.
     *
     * @param string $json Raw response.
     * @param int $choicecount Expected number of choices.
     * @return array
     */
    public function parse_review(string $json, int $choicecount): array {
        $decoded = json_decode(trim($json), true);
        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new invalid_parameter_exception('AI response is not strict JSON.');
        }
        if (array_keys($decoded) !== ['choices'] || !is_array($decoded['choices'])) {
            throw new invalid_parameter_exception('AI response does not match the required top-level schema.');
        }
        if (count($decoded['choices']) !== $choicecount) {
            throw new invalid_parameter_exception('AI response must contain every choice exactly once.');
        }

        $result = [];
        $seen = [];
        $required = ['confidence', 'findings', 'index', 'quality', 'suggestion'];
        sort($required);

        foreach ($decoded['choices'] as $choice) {
            if (!is_array($choice)) {
                throw new invalid_parameter_exception('AI choice review must be an object.');
            }
            $keys = array_keys($choice);
            sort($keys);
            if ($keys !== $required) {
                throw new invalid_parameter_exception('AI choice review does not match the required schema.');
            }
            if (!is_int($choice['index']) || $choice['index'] < 0 || $choice['index'] >= $choicecount) {
                throw new invalid_parameter_exception('AI returned an invalid choice index.');
            }
            if (isset($seen[$choice['index']])) {
                throw new invalid_parameter_exception('AI returned the same choice index more than once.');
            }
            $seen[$choice['index']] = true;

            if (!is_string($choice['quality']) || !in_array($choice['quality'], self::QUALITIES, true)) {
                throw new invalid_parameter_exception('AI returned an invalid quality.');
            }
            if (!is_string($choice['confidence']) || !in_array($choice['confidence'], self::CONFIDENCES, true)) {
                throw new invalid_parameter_exception('AI returned an invalid confidence.');
            }
            if (!is_array($choice['findings']) || count($choice['findings']) > 12) {
                throw new invalid_parameter_exception('AI returned invalid findings.');
            }
            if (!is_null($choice['suggestion']) && !is_string($choice['suggestion'])) {
                throw new invalid_parameter_exception('AI suggestion must be a string or null.');
            }
            if (is_string($choice['suggestion']) && core_text::strlen($choice['suggestion']) > 4000) {
                throw new invalid_parameter_exception('AI suggestion is too long.');
            }

            $findings = [];
            foreach ($choice['findings'] as $finding) {
                if (!is_array($finding)) {
                    throw new invalid_parameter_exception('AI finding must be an object.');
                }
                $findingkeys = array_keys($finding);
                sort($findingkeys);
                if ($findingkeys !== ['justification', 'type']) {
                    throw new invalid_parameter_exception('AI finding does not match the required schema.');
                }
                if (!is_string($finding['type']) || !in_array($finding['type'], self::FINDINGS, true)) {
                    throw new invalid_parameter_exception('AI returned an invalid finding type.');
                }
                if (!is_string($finding['justification']) || trim($finding['justification']) === '' ||
                    core_text::strlen($finding['justification']) > 6000) {
                    throw new invalid_parameter_exception('AI finding justification is invalid.');
                }
                $findings[] = [
                    'source' => 'ai',
                    'code' => $finding['type'],
                    'status' => $choice['quality'],
                    'justification' => trim($finding['justification']),
                ];
            }

            $result[$choice['index']] = [
                'index' => $choice['index'],
                'quality' => $choice['quality'],
                'confidence' => $choice['confidence'],
                'findings' => $findings,
                'suggestion' => is_string($choice['suggestion']) ? trim($choice['suggestion']) : null,
            ];
        }

        ksort($result, SORT_NUMERIC);
        return array_values($result);
    }

    /**
     * Parse a request for one new distractor.
     *
     * @param string $json Raw response.
     * @param array $existingchoices Existing normalized choices.
     * @return array
     */
    public function parse_new_distractor(string $json, array $existingchoices): array {
        $decoded = json_decode(trim($json), true);
        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new invalid_parameter_exception('AI distractor suggestion does not match the required schema.');
        }
        $keys = array_keys($decoded);
        sort($keys);
        if ($keys !== ['distractor', 'rationale']) {
            throw new invalid_parameter_exception('AI distractor suggestion does not match the required schema.');
        }
        if (!is_string($decoded['distractor']) || !is_string($decoded['rationale'])) {
            throw new invalid_parameter_exception('AI distractor suggestion fields must be strings.');
        }
        $distractor = trim($decoded['distractor']);
        $rationale = trim($decoded['rationale']);
        if ($distractor === '' || $rationale === '' || core_text::strlen($distractor) > 4000 ||
            core_text::strlen($rationale) > 6000) {
            throw new invalid_parameter_exception('AI distractor suggestion contains invalid text.');
        }

        $normalized = self::normalize($distractor);
        foreach ($existingchoices as $choice) {
            if ($normalized === self::normalize((string)($choice['text'] ?? ''))) {
                throw new invalid_parameter_exception('AI suggested a literal duplicate of an existing choice.');
            }
        }

        return ['distractor' => $distractor, 'rationale' => $rationale];
    }

    /**
     * Normalize for duplicate validation.
     *
     * @param string $text Text.
     * @return string
     */
    private static function normalize(string $text): string {
        $text = core_text::strtolower(trim($text));
        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }
}
