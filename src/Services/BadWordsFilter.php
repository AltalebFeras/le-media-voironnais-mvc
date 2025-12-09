<?php
// filepath: e:\stage\le-media-voironnais\src\Services\BadWordsFilter.php

namespace src\Services;

class BadWordsFilter
{
    private static $badWords = [
        // Insultes courantes
        'connard', 'connasse', 'salaud', 'salope', 'enculé', 'enculée', 'enfoiré', 'enfoirée',
        'putain', 'pute', 'pétasse', 'chienne', 'salaud', 'ordure', 'fumier', 'pourriture',
        'merde', 'merdeux', 'merdeuse', 'chieur', 'chieuse', 'trou du cul', 'trouduc',
        
        // Insultes vulgaires
        'couille', 'couilles', 'bite', 'bites', 'queue', 'queues', 'chatte', 'chattes',
        'con', 'conne', 'cons', 'connes', 'cul', 'culs', 'nichons', 'seins',
        'foutre', 'foutoir', 'branler', 'branleur', 'branleuse', 'branlette',
        
        // Insultes racistes (à modérer)
        'nègre', 'négro', 'bamboula', 'bounty', 'bougnoule', 'crouille', 'raton',
        'youpin', 'youpine', 'feuj', 'bicot', 'macaque', 'singe',
        
        // Insultes sexistes
        'garce', 'gonzesse', 'poufiasse', 'pouffiasse', 'trainée', 'pétasse',
        'pute', 'putain', 'salope', 'connasse', 'chienne',
        
        // Insultes homophobes
        'pédé', 'tapette', 'tante', 'tantouze', 'tarlouze', 'tafiole', 'fiotte',
        'gouine', 'lesbienne', 'gougnotte', 'tribade',
        
        // Insultes diverses
        'débile', 'abruti', 'abrutie', 'crétin', 'crétine', 'idiot', 'idiote',
        'imbécile', 'mongol', 'mongolien', 'mongolienne', 'attardé', 'attardée',
        'taré', 'tarée', 'demeuré', 'demeurée', 'débile', 'débile mental',
        
        // Variantes avec accents et sans accents
        'encule', 'enculee', 'enfoire', 'enfoiree', 'petasse', 'batard', 'batarde',
        'salop', 'connard', 'conard', 'connasse', 'conasse',
        
        // Insultes familiales
        'bâtard', 'bâtarde', 'fils de pute', 'fdp', 'fds', 'nique ta mère', 'ntm',
        'ta mère', 'ta race', 'tes morts', 'tmtc', 'tg', 'ta gueule',
        
        // Abréviations vulgaires
        'fdp', 'fds', 'ntm', 'tmtc', 'tg', 'pd', 'pds', 'slt', 'slp',
        
        // Variations d'orthographe (langage SMS)
        'connar', 'konar', 'konard', 'konasse', 'konass', 'enkulé', 'enkuler',
        'putin', 'putain', 'pt1', 'salop', 'salo', 'merd', 'mrd', 'chier', 'chié',
        
        // Insultes religieuses
        'bordel de dieu', 'nom de dieu', 'putain de dieu', 'bon dieu',
        'sacré nom de dieu', 'ostie', 'câlice', 'tabarnak', 'sacrament',
        
        // Termes sexuels explicites
        'baiser', 'niquer', 'tringler', 'ken', 'kené', 'fourrer', 'défoncer',
        'sodomie', 'sodomiser', 'enculer', 'fellation', 'pipe', 'sucer',
        'ejaculer', 'éjaculer', 'jouir', 'orgasme', 'masturber', 'se branler',
        
        // Variations créatives
        'pd', 'pédé', 'pede', 'tapette', 'tarlouse', 'tarlouze', 'tarlouze',
        'conass', 'konass', 'salop', 'salo', 'salaud', 'salo',
        'encul', 'nculé', 'enfoiré', 'nfoiré', 
        
        // Insultes corporelles
        'gros', 'grosse', 'gras', 'grasse', 'obèse', 'baleine', 'hippopotame',
        'laid', 'laide', 'moche', 'tronche', 'gueule', 'face de rat', 'face de cochon',
        
        // Autres termes vulgaires
        'caca', 'pipi', 'crotte', 'pet', 'péter', 'roter', 'vomir', 'gerber',
        'dégueulasse', 'dégueu', 'dégeu', 'crade', 'cradingue', 'crasseux',
    ];

    /**
     * Check if text contains bad words
     */
    public static function containsBadWords(string $text): bool
    {
        $text = strtolower($text);
        $text = self::normalizeText($text);

        foreach (self::$badWords as $badWord) {
            $badWord = strtolower($badWord);
            // Check for exact word match with word boundaries
            if (preg_match('/\b' . preg_quote($badWord, '/') . '\b/iu', $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter bad words from text (replace with asterisks)
     */
    public static function filterBadWords(string $text): string
    {
        $filtered = $text;
        $textLower = strtolower($text);
        $textNormalized = self::normalizeText($textLower);

        foreach (self::$badWords as $badWord) {
            $badWord = strtolower($badWord);
            $replacement = str_repeat('*', mb_strlen($badWord));

            // Replace with case-insensitive match
            $filtered = preg_replace('/\b' . preg_quote($badWord, '/') . '\b/iu', $replacement, $filtered);
        }

        return $filtered;
    }

    /**
     * Get list of bad words found in text
     */
    public static function getBadWordsFound(string $text): array
    {
        $found = [];
        $text = strtolower($text);
        $text = self::normalizeText($text);

        foreach (self::$badWords as $badWord) {
            $badWord = strtolower($badWord);
            if (preg_match('/\b' . preg_quote($badWord, '/') . '\b/iu', $text)) {
                $found[] = $badWord;
            }
        }

        return array_unique($found);
    }

    /**
     * Normalize text to handle variations (remove accents, special chars, etc.)
     */
    private static function normalizeText(string $text): string
    {
        // Remove special characters used to bypass filters
        $text = str_replace(['@', '4', '3', '1', '0', '$', '€'], ['a', 'a', 'e', 'i', 'o', 's', 'e'], $text);

        // Remove spaces between letters (k o n a r d -> konard)
        $text = preg_replace('/(\w)\s+(?=\w)/u', '$1', $text);

        // Normalize accents
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return $text;
    }

    /**
     * Add custom bad words
     */
    public static function addBadWords(array $words): void
    {
        self::$badWords = array_merge(self::$badWords, $words);
        self::$badWords = array_unique(self::$badWords);
    }

    /**
     * Get all bad words
     */
    public static function getAllBadWords(): array
    {
        return self::$badWords;
    }
}
