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
use invalid_parameter_exception;
use qbank_distractorcheck\ai_response_parser;

/**
 * Tests strict validation of AI output.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \qbank_distractorcheck\ai_response_parser
 */
final class ai_response_parser_test extends advanced_testcase {
    /**
     * Valid output is accepted and ordered by index.
     */
    public function test_valid_review(): void {
        $json = json_encode([
            'choices' => [
                [
                    'index' => 1,
                    'quality' => 'weak',
                    'confidence' => 'medium',
                    'findings' => [[
                        'type' => 'plausibility',
                        'justification' => 'This distractor is easy to eliminate.',
                    ]],
                    'suggestion' => 'A more plausible misconception',
                ],
                [
                    'index' => 0,
                    'quality' => 'good',
                    'confidence' => 'high',
                    'findings' => [],
                    'suggestion' => null,
                ],
            ],
        ]);

        $result = (new ai_response_parser())->parse_review($json, 2);
        $this->assertSame(0, $result[0]['index']);
        $this->assertSame(1, $result[1]['index']);
        $this->assertSame('weak', $result[1]['quality']);
    }

    /**
     * An out-of-range AI index is never trusted.
     */
    public function test_invalid_ai_index_is_rejected(): void {
        $json = json_encode([
            'choices' => [[
                'index' => 7,
                'quality' => 'good',
                'confidence' => 'high',
                'findings' => [],
                'suggestion' => null,
            ]],
        ]);

        $this->expectException(invalid_parameter_exception::class);
        (new ai_response_parser())->parse_review($json, 1);
    }

    /**
     * Malformed JSON is rejected before any value is used.
     */
    public function test_malformed_json_is_rejected(): void {
        $this->expectException(invalid_parameter_exception::class);
        (new ai_response_parser())->parse_review('{"choices": [', 1);
    }

    /**
     * Duplicate indices are rejected even when the item count matches.
     */
    public function test_duplicate_ai_indices_are_rejected(): void {
        $item = [
            'index' => 0,
            'quality' => 'good',
            'confidence' => 'high',
            'findings' => [],
            'suggestion' => null,
        ];
        $json = json_encode(['choices' => [$item, $item]]);

        $this->expectException(invalid_parameter_exception::class);
        (new ai_response_parser())->parse_review($json, 2);
    }

    /**
     * A new distractor cannot be a literal duplicate.
     */
    public function test_new_distractor_duplicate_is_rejected(): void {
        $json = json_encode(['distractor' => 'Paris', 'rationale' => 'A common confusion.']);
        $existing = [['text' => '  paris  ']];

        $this->expectException(invalid_parameter_exception::class);
        (new ai_response_parser())->parse_new_distractor($json, $existing);
    }
}
