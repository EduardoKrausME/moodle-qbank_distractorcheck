# qbank_distractorcheck

`qbank_distractorcheck` is a Moodle question bank plugin for Moodle that reviews the quality of alternatives and
distractors in existing `multichoice` questions.

It does not generate complete questions and it does not automatically edit the question bank. The teacher starts the
review from the action menu of a multiple-choice question and receives deterministic findings plus a semantic review
produced through `local_ai_bridge`.

## What is checked locally

Before the semantic AI review, PHP checks information that does not require a language model:

- number of alternatives;
- empty alternatives;
- current fractions;
- which alternatives are marked correct;
- invalid number of marked correct answers for a single-answer question;
- positive fraction sum for multiple-answer questions;
- literal duplicates after case/whitespace normalization;
- obvious alternative-length outliers that may create a visual clue.

These findings are not replaced by AI output.

## What is reviewed by AI

The bridge receives only the normalized question text, the alternative texts, the zero-based indexes used for the
current request, whether each choice is currently marked fully correct or receives partial credit, and whether the
question allows multiple correct answers.

It reviews semantic aspects such as:

- plausibility;
- relation to the stem;
- grammatical clues;
- semantic duplication;
- absurd alternatives;
- alternatives that may also be defensibly correct;
- inappropriate “all of the above” / “none of the above” constructions;
- clues created by wording.

No student, attempt, response, grade or user profile data is sent by this plugin.

## Strict response validation

The semantic review must return strict JSON:

```json
{
  "choices": [
    {
      "index": 0,
      "quality": "good|weak|problematic",
      "confidence": "low|medium|high",
      "findings": [
        {
          "type": "plausibility",
          "justification": "..."
        }
      ],
      "suggestion": null
    }
  ]
}
```

The plugin never trusts `index`. It validates the range, rejects repeated indexes, requires every submitted choice
exactly once, validates enum values and field types, limits returned text sizes, and rejects malformed JSON. If the AI
response cannot be validated, deterministic findings remain available and the semantic result is not used.

## Suggesting one new distractor

A teacher can explicitly request one new plausible distractor. This is a separate AI request using the
same `distractorcheck-review` purpose. The result contains only a proposed distractor and rationale, is checked against
existing alternatives for literal duplication, and is displayed for human review.

There is deliberately no database write path for this feature; the proposed distractor is never saved automatically.

## Permissions

The plugin adds `qbank/distractorcheck:review`, allowed by default for editing teachers and managers. A review also
requires the normal Moodle capability to view the selected question. `local_ai_bridge` independently enforces its
own `local/ai_bridge:use`, tenant, user, purpose, route and credit rules.

## Privacy

The plugin stores no personal data and declares a null privacy provider. AI usage/accounting is handled
by `local_ai_bridge` according to its own configuration.
