<?php

namespace src\Services;

class Helper
{
    public function generateSlug(string $string): string
    {
        // Remove accents and special characters
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}
