<?php
/**
 * 内容质量评分类
 *
 * 评估生成内容的质量（SEO、可读性等）
 *
 * @package AI_Auto_Content_Generator
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Content_Quality {

    /**
     * 评估内容质量
     *
     * @param string $title 标题
     * @param string $content 内容
     * @return array {
     *     @type int $overall_score 总分 (0-100)
     *     @type array $seo SEO评分详情
     *     @type array $readability 可读性评分详情
     *     @type array $structure 结构评分详情
     *     @type array $suggestions 改进建议
     * }
     */
    public static function evaluate($title, $content) {
        $seo_score = self::evaluate_seo($title, $content);
        $readability_score = self::evaluate_readability($content);
        $structure_score = self::evaluate_structure($content);

        // 计算总分（加权平均）
        $overall_score = round(
            ($seo_score['score'] * 0.4) +
            ($readability_score['score'] * 0.3) +
            ($structure_score['score'] * 0.3)
        );

        // 收集改进建议
        $suggestions = array_merge(
            $seo_score['suggestions'],
            $readability_score['suggestions'],
            $structure_score['suggestions']
        );

        return array(
            'overall_score' => $overall_score,
            'seo' => $seo_score,
            'readability' => $readability_score,
            'structure' => $structure_score,
            'suggestions' => $suggestions,
        );
    }

    /**
     * SEO评分
     *
     * @param string $title 标题
     * @param string $content 内容
     * @return array
     */
    private static function evaluate_seo($title, $content) {
        $score = 0;
        $max_score = 100;
        $suggestions = array();

        // 1. 标题长度 (30分)
        $title_length = mb_strlen($title);
        if ($title_length >= 30 && $title_length <= 60) {
            $score += 30;
        } elseif ($title_length > 60) {
            $score += 20;
            $suggestions[] = __('Title is too long. Recommended: 30-60 characters.', 'ai-auto-content-generator');
        } else {
            $score += 15;
            $suggestions[] = __('Title is too short. Recommended: 30-60 characters.', 'ai-auto-content-generator');
        }

        // 2. 内容长度 (20分)
        $word_count = str_word_count(strip_tags($content));
        if ($word_count >= 500) {
            $score += 20;
        } elseif ($word_count >= 300) {
            $score += 15;
        } else {
            $score += 5;
            $suggestions[] = __('Content is too short. Recommended: at least 500 words for better SEO.', 'ai-auto-content-generator');
        }

        // 3. 标题标签使用 (20分)
        $h2_count = substr_count($content, '<h2>');
        $h3_count = substr_count($content, '<h3>');
        $heading_count = $h2_count + $h3_count;

        if ($heading_count >= 3) {
            $score += 20;
        } elseif ($heading_count >= 1) {
            $score += 10;
            $suggestions[] = __('Add more headings (H2, H3) to improve content structure.', 'ai-auto-content-generator');
        } else {
            $suggestions[] = __('No headings found. Add H2 and H3 tags to structure your content.', 'ai-auto-content-generator');
        }

        // 4. 段落数量 (15分)
        $paragraphs = explode('</p>', $content);
        $paragraph_count = count($paragraphs) - 1;

        if ($paragraph_count >= 5) {
            $score += 15;
        } elseif ($paragraph_count >= 3) {
            $score += 10;
        } else {
            $score += 5;
            $suggestions[] = __('Add more paragraphs to break up content.', 'ai-auto-content-generator');
        }

        // 5. 列表使用 (15分)
        $has_ul = strpos($content, '<ul>') !== false;
        $has_ol = strpos($content, '<ol>') !== false;

        if ($has_ul || $has_ol) {
            $score += 15;
        } else {
            $suggestions[] = __('Consider adding bullet points or numbered lists to improve scannability.', 'ai-auto-content-generator');
        }

        return array(
            'score' => min($score, $max_score),
            'max_score' => $max_score,
            'title_length' => $title_length,
            'word_count' => $word_count,
            'heading_count' => $heading_count,
            'paragraph_count' => $paragraph_count,
            'suggestions' => $suggestions,
        );
    }

    /**
     * 可读性评分
     *
     * @param string $content 内容
     * @return array
     */
    private static function evaluate_readability($content) {
        $score = 0;
        $max_score = 100;
        $suggestions = array();

        $plain_text = wp_strip_all_tags($content);
        $sentences = preg_split('/[.!?]+/', $plain_text, -1, PREG_SPLIT_NO_EMPTY);
        $sentence_count = count($sentences);

        if ($sentence_count === 0) {
            return array(
                'score' => 0,
                'max_score' => $max_score,
                'suggestions' => array(__('Content appears to be empty or invalid.', 'ai-auto-content-generator')),
            );
        }

        $words = str_word_count($plain_text);
        $avg_sentence_length = $words / $sentence_count;

        // 1. 平均句子长度 (40分)
        if ($avg_sentence_length >= 10 && $avg_sentence_length <= 20) {
            $score += 40;
        } elseif ($avg_sentence_length <= 25) {
            $score += 30;
        } else {
            $score += 15;
            $suggestions[] = __('Sentences are too long. Consider breaking them into shorter ones.', 'ai-auto-content-generator');
        }

        // 2. 段落长度 (30分)
        $paragraphs = array_filter(explode('</p>', $content));
        $long_paragraphs = 0;

        foreach ($paragraphs as $p) {
            $p_words = str_word_count(wp_strip_all_tags($p));
            if ($p_words > 150) {
                $long_paragraphs++;
            }
        }

        if ($long_paragraphs === 0) {
            $score += 30;
        } elseif ($long_paragraphs <= 2) {
            $score += 20;
        } else {
            $score += 10;
            $suggestions[] = __('Some paragraphs are too long. Keep paragraphs under 150 words.', 'ai-auto-content-generator');
        }

        // 3. 过渡词使用 (30分)
        $transition_words = array(
            'however', 'therefore', 'furthermore', 'moreover', 'additionally',
            'consequently', 'meanwhile', 'similarly', 'likewise', 'thus',
            'hence', 'nevertheless', 'nonetheless', 'indeed', 'in fact',
        );

        $transition_count = 0;
        $text_lower = strtolower($plain_text);
        foreach ($transition_words as $word) {
            $transition_count += substr_count($text_lower, $word);
        }

        $transition_ratio = $transition_count / max($sentence_count, 1);

        if ($transition_ratio >= 0.2) {
            $score += 30;
        } elseif ($transition_ratio >= 0.1) {
            $score += 20;
        } else {
            $score += 10;
            $suggestions[] = __('Add more transition words to improve flow.', 'ai-auto-content-generator');
        }

        return array(
            'score' => min($score, $max_score),
            'max_score' => $max_score,
            'avg_sentence_length' => round($avg_sentence_length, 1),
            'sentence_count' => $sentence_count,
            'long_paragraphs' => $long_paragraphs,
            'suggestions' => $suggestions,
        );
    }

    /**
     * 结构评分
     *
     * @param string $content 内容
     * @return array
     */
    private static function evaluate_structure($content) {
        $score = 0;
        $max_score = 100;
        $suggestions = array();

        // 1. 有引言段落 (25分)
        $paragraphs = explode('</p>', $content);
        if (count($paragraphs) > 0) {
            $first_paragraph = wp_strip_all_tags($paragraphs[0]);
            $first_p_words = str_word_count($first_paragraph);

            if ($first_p_words >= 30 && $first_p_words <= 100) {
                $score += 25;
            } elseif ($first_p_words > 0) {
                $score += 15;
                $suggestions[] = __('Introduction paragraph should be 30-100 words.', 'ai-auto-content-generator');
            }
        }

        // 2. 标题层级正确 (25分)
        $h2_count = substr_count($content, '<h2>');
        $h3_count = substr_count($content, '<h3>');
        $h4_count = substr_count($content, '<h4>');

        if ($h2_count > 0) {
            $score += 15;
            if ($h3_count > 0) {
                $score += 10;
            } else {
                $suggestions[] = __('Consider using H3 subheadings under H2 headings.', 'ai-auto-content-generator');
            }
        } else {
            $suggestions[] = __('Add H2 headings to create main sections.', 'ai-auto-content-generator');
        }

        // 3. 内容分布均匀 (25分)
        if ($h2_count > 0) {
            $sections = preg_split('/<h2>/', $content);
            $section_lengths = array();

            foreach ($sections as $section) {
                $section_words = str_word_count(wp_strip_all_tags($section));
                if ($section_words > 0) {
                    $section_lengths[] = $section_words;
                }
            }

            if (count($section_lengths) > 1) {
                $avg_section = array_sum($section_lengths) / count($section_lengths);
                $variance = 0;

                foreach ($section_lengths as $length) {
                    $variance += pow($length - $avg_section, 2);
                }

                $std_dev = sqrt($variance / count($section_lengths));
                $coefficient = $std_dev / $avg_section;

                if ($coefficient < 0.5) {
                    $score += 25;
                } elseif ($coefficient < 1) {
                    $score += 15;
                } else {
                    $score += 5;
                    $suggestions[] = __('Content sections vary significantly in length. Try to balance them.', 'ai-auto-content-generator');
                }
            }
        }

        // 4. 有结论段落 (25分)
        if (count($paragraphs) > 2) {
            $last_paragraph = wp_strip_all_tags($paragraphs[count($paragraphs) - 2]);
            $last_p_words = str_word_count($last_paragraph);

            if ($last_p_words >= 30) {
                $score += 25;
            } elseif ($last_p_words > 0) {
                $score += 15;
                $suggestions[] = __('Add a stronger conclusion paragraph (at least 30 words).', 'ai-auto-content-generator');
            }
        }

        return array(
            'score' => min($score, $max_score),
            'max_score' => $max_score,
            'suggestions' => $suggestions,
        );
    }

    /**
     * 获取质量等级
     *
     * @param int $score 分数
     * @return string
     */
    public static function get_quality_grade($score) {
        if ($score >= 90) {
            return __('Excellent', 'ai-auto-content-generator');
        } elseif ($score >= 80) {
            return __('Good', 'ai-auto-content-generator');
        } elseif ($score >= 70) {
            return __('Fair', 'ai-auto-content-generator');
        } elseif ($score >= 60) {
            return __('Poor', 'ai-auto-content-generator');
        } else {
            return __('Very Poor', 'ai-auto-content-generator');
        }
    }
}
