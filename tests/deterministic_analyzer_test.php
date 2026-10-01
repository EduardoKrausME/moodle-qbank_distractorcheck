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
use qbank_distractorcheck\deterministic_analyzer;

/**
 * Tests for deterministic distractor checks.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class deterministic_analyzer_test extends advanced_testcase {
    /**
     * A valid single-answer question is recognized correctly.
     */
    public function test_single_answer_question(): void {
        $result = (new deterministic_analyzer())->analyze($this->question(true, [1.0, 0.5, 0.0]));
        $this->assertSame(3, $result['choicecount']);
        $this->assertSame(1, $result['correctcount']);
        $this->assertTrue($result['single']);
        $this->assertEmpty($result['questionfindings']);
    }

    /**
     * Multiple-answer positive fractions are checked locally.
     */
    public function test_multiple_answer_question(): void {
        $result = (new deterministic_analyzer())->analyze($this->question(false, [0.5, 0.5, 0.0]));
        $this->assertSame(2, $result['correctcount']);
        $this->assertFalse($result['single']);
        $this->assertEqualsWithDelta(1.0, $result['positivefractionsum'], 0.00001);
        $this->assertEmpty($result['questionfindings']);
    }

    /**
     * Literal duplicates are detected before any AI call.
     */
    public function test_literal_duplicate(): void {
        $question = $this->question(true, [1.0, 0.0, 0.0]);
        $question['choices'][1]['text'] = '  PARIS ';
        $question['choices'][2]['text'] = 'paris';
        $result = (new deterministic_analyzer())->analyze($question);

        $codes1 = array_column($result['choicefindings'][1], 'code');
        $codes2 = array_column($result['choicefindings'][2], 'code');
        $this->assertContains('literal_duplicate', $codes1);
        $this->assertContains('literal_duplicate', $codes2);
    }

    /**
     * Build normalized test data.
     *
     * @param bool $single Single-answer flag.
     * @param array $fractions Fractions.
     * @return array
     */
    private function question(bool $single, array $fractions): array {
        $texts = ['Brasilia', 'Paris', 'Rome'];
        $choices = [];
        foreach ($fractions as $index => $fraction) {
            $choices[] = [
                'index' => $index,
                'text' => $texts[$index],
                'fraction' => $fraction,
                'iscorrect' => $fraction > 0,
            ];
        }
        return [
            'id' => 10,
            'name' => 'Capital',
            'questiontext' => 'What is the capital of Brazil?',
            'single' => $single,
            'choices' => $choices,
        ];
    }
}
