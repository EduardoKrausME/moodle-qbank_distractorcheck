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
 * Strings em português do Brasil para qbank_distractorcheck.
 *
 * @package    qbank_distractorcheck
 * @copyright  2026 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['aierror'] = 'A revisão semântica por IA não pôde ser validada ou concluída. Os achados determinísticos continuam sendo exibidos.';
$string['alternative'] = 'Alternativa';
$string['backtoquestionbank'] = 'Voltar ao banco de questões';
$string['bridgeunavailable'] = 'O bridge de IA obrigatório não está disponível.';
$string['choice'] = '#';
$string['choices'] = 'alternativas';
$string['confidence'] = 'Confiança';
$string['confidence_high'] = 'Alta';
$string['confidence_low'] = 'Baixa';
$string['confidence_medium'] = 'Média';
$string['distractorcheck:review'] = 'Revisar distratores de questões de múltipla escolha';
$string['finding_choicecount'] = 'A questão possui {$a} alternativas; uma questão de múltipla escolha deve ter pelo menos duas.';
$string['finding_duplicate'] = 'Esta alternativa é uma duplicata literal da(s) alternativa(s) {$a}, após normalizar maiúsculas/minúsculas e espaços.';
$string['finding_emptychoice'] = 'Esta alternativa fica vazia após a normalização do texto.';
$string['finding_fractionsum'] = 'Nesta questão de múltiplas respostas, a soma das frações positivas é {$a}, em vez de 1,0000.';
$string['finding_lengthoutlier'] = 'Esta alternativa possui {$a->length} caracteres, enquanto a mediana das alternativas é aproximadamente {$a->median}, o que pode criar uma pista visual.';
$string['finding_nocorrect'] = 'Nenhuma alternativa possui fração positiva, portanto nenhuma resposta está marcada atualmente como correta.';
$string['finding_singlecorrectcount'] = 'Esta é uma questão de resposta única, mas {$a} alternativas possuem fração positiva.';
$string['findings'] = 'Achados e justificativa';
$string['findingtype_absurd_choice'] = 'Alternativa implausível ou absurda';
$string['findingtype_all_none_of_above'] = 'Todas/nenhuma das anteriores';
$string['findingtype_also_correct'] = 'Possivelmente também correta';
$string['findingtype_empty_choice'] = 'Alternativa vazia';
$string['findingtype_grammatical_clue'] = 'Pista gramatical';
$string['findingtype_invalid_choice_count'] = 'Quantidade inválida de alternativas';
$string['findingtype_length_outlier'] = 'Tamanho discrepante';
$string['findingtype_literal_duplicate'] = 'Duplicidade literal';
$string['findingtype_no_correct_choice'] = 'Sem alternativa correta';
$string['findingtype_other'] = 'Outro problema semântico';
$string['findingtype_plausibility'] = 'Plausibilidade';
$string['findingtype_positive_fraction_sum'] = 'Soma das frações';
$string['findingtype_relation_to_stem'] = 'Relação com o enunciado';
$string['findingtype_semantic_duplicate'] = 'Duplicidade semântica';
$string['findingtype_single_correct_count'] = 'Conflito na correção de resposta única';
$string['findingtype_wording_clue'] = 'Pista causada pela redação';
$string['grading'] = 'Correção atual';
$string['markedcorrect'] = 'Marcada como correta';
$string['markedcorrectcount'] = 'marcadas como corretas';
$string['markedincorrect'] = 'Marcada como incorreta';
$string['multipleanswer'] = 'Questão de múltiplas respostas';
$string['neversavedautomatically'] = 'Esta sugestão não foi gravada no banco de questões.';
$string['newdistractor'] = 'Sugestão de novo distrator';
$string['newdistractor_help'] = 'Solicita um novo distrator plausível. Ele é exibido para revisão do professor e nunca é salvo automaticamente.';
$string['nofindings'] = 'Nenhum achado';
$string['pagetitle'] = 'Revisão da qualidade dos distratores';
$string['partialcredit'] = 'Crédito parcial';
$string['pluginname'] = 'Verificação da qualidade dos distratores';
$string['privacy:metadata'] = 'O plugin Verificação da qualidade dos distratores não armazena dados pessoais.';
$string['quality_good'] = 'Bom';
$string['quality_localonly'] = 'Nenhum problema local detectado';
$string['quality_problematic'] = 'Problemático';
$string['quality_weak'] = 'Fraco';
$string['reviewdistractors'] = 'Revisar distratores';
$string['singleanswer'] = 'Questão de resposta única';
$string['source_ai'] = 'IA';
$string['source_local'] = 'regra local';
$string['status'] = 'Status';
$string['structuralfindings'] = 'Achados determinísticos';
$string['suggestion'] = 'Sugestão de melhoria';
$string['suggestionerror'] = 'Não foi possível gerar ou validar um novo distrator.';
$string['suggestnewdistractor'] = 'Sugerir novo distrator';
$string['unsupportedqtype'] = 'Esta ferramenta suporta apenas questões de múltipla escolha.';
