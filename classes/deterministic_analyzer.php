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

use core_text;

/**
 * Deterministic checks that do not need AI.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deterministic_analyzer {
    /**
     * Analyse normalized multichoice data.
     *
     * @param array $question Normalized question.
     * @return array
     */
    public function analyze(array $question): array {
        $choices = $question['choices'] ?? [];
        $choicefindings = [];
        foreach ($choices as $choice) {
            $choicefindings[(int)$choice['index']] = [];
        }

        $questionfindings = [];
        $count = count($choices);
        $single = !empty($question['single']);
        $correctcount = 0;
        foreach ($choices as $choice) {
            $fraction = (float)($choice['fraction'] ?? 0.0);
            $iscorrect = $single ? abs($fraction - 1.0) < 0.0000001 : $fraction > 0.0;
            if ($iscorrect) {
                $correctcount++;
            }
        }

        if ($count < 2) {
            $questionfindings[] = $this->finding('invalid_choice_count', 'problematic',
                get_string('finding_choicecount', 'qbank_distractorcheck', $count));
        }
        if ($correctcount === 0) {
            $questionfindings[] = $this->finding('no_correct_choice', 'problematic',
                get_string('finding_nocorrect', 'qbank_distractorcheck'));
        }
        if ($single && $correctcount !== 1) {
            $questionfindings[] = $this->finding('single_correct_count', 'problematic',
                get_string('finding_singlecorrectcount', 'qbank_distractorcheck', $correctcount));
        }

        $positivefraction = 0.0;
        foreach ($choices as $choice) {
            if ((float)$choice['fraction'] > 0) {
                $positivefraction += (float)$choice['fraction'];
            }
        }
        if (!$single && $correctcount > 0 && abs($positivefraction - 1.0) > 0.0001) {
            $questionfindings[] = $this->finding('positive_fraction_sum', 'problematic',
                get_string('finding_fractionsum', 'qbank_distractorcheck', format_float($positivefraction, 4)));
        }

        $normalized = [];
        $lengths = [];
        foreach ($choices as $choice) {
            $index = (int)$choice['index'];
            $text = trim((string)$choice['text']);
            if ($text === '') {
                $choicefindings[$index][] = $this->finding('empty_choice', 'problematic',
                    get_string('finding_emptychoice', 'qbank_distractorcheck'));
                continue;
            }
            $key = $this->normalize($text);
            $normalized[$key][] = $index;
            $lengths[$index] = core_text::strlen($text);
        }

        foreach ($normalized as $indexes) {
            if (count($indexes) < 2) {
                continue;
            }
            foreach ($indexes as $index) {
                $others = array_values(array_diff($indexes, [$index]));
                $choicefindings[$index][] = $this->finding('literal_duplicate', 'problematic',
                    get_string('finding_duplicate', 'qbank_distractorcheck', implode(', ', array_map(
                        static fn(int $i): string => (string)($i + 1), $others
                    ))));
            }
        }

        if (count($lengths) >= 3) {
            $median = $this->median(array_values($lengths));
            if ($median > 0) {
                foreach ($lengths as $index => $length) {
                    $toolong = $length >= ($median * 2.5) && ($length - $median) >= 30;
                    $tooshort = $length <= ($median * 0.4) && ($median - $length) >= 20;
                    if ($toolong || $tooshort) {
                        $choicefindings[$index][] = $this->finding('length_outlier', 'weak',
                            get_string('finding_lengthoutlier', 'qbank_distractorcheck', [
                                'length' => $length,
                                'median' => (int)round($median),
                            ]));
                    }
                }
            }
        }

        return [
            'choicecount' => $count,
            'correctcount' => $correctcount,
            'single' => $single,
            'positivefractionsum' => $positivefraction,
            'questionfindings' => $questionfindings,
            'choicefindings' => $choicefindings,
        ];
    }

    /**
     * Normalize text for literal duplicate checks.
     *
     * @param string $text Text.
     * @return string
     */
    private function normalize(string $text): string {
        $text = core_text::strtolower(trim($text));
        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }

    /**
     * Calculate median.
     *
     * @param array $values Numeric values.
     * @return float
     */
    private function median(array $values): float {
        sort($values, SORT_NUMERIC);
        $count = count($values);
        $middle = intdiv($count, 2);
        if ($count % 2) {
            return (float)$values[$middle];
        }
        return ((float)$values[$middle - 1] + (float)$values[$middle]) / 2;
    }

    /**
     * Create a deterministic finding.
     *
     * @param string $code Code.
     * @param string $status Status.
     * @param string $justification Explanation.
     * @return array
     */
    private function finding(string $code, string $status, string $justification): array {
        return [
            'source' => 'deterministic',
            'code' => $code,
            'status' => $status,
            'justification' => $justification,
        ];
    }
}
