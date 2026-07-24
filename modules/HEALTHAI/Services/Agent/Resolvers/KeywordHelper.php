<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

/**
 * Shared keyword utilities for all DataResolvers.
 * Extracted as a class to avoid namespace-level function collisions
 * when multiple files share the same namespace.
 */
final class KeywordHelper
{
    /**
     * Terms that mean "show me everything" — both English and Bengali.
     */
    private const GENERIC_TERMS = [
        // English
        'list', 'all', 'shob', 'ki ki', 'sob', 'everything', 'show all',
        // Bengali — "show list / all / everything"
        'তালিকা', 'সব', 'সকল', 'লিস্ট', 'সবাই', 'সবগুলো', 'সমস্ত',
        'দেখুন', 'দাও', 'দিতে', 'চাই',
        // Combined phrases (after stripping common words these may remain)
        'দেখান', 'বলুন', 'জানান',
    ];

    /**
     * Common Bengali filler words to strip from search keywords.
     */
    public const BENGALI_STRIP = [
        // Pronouns / particles
        'আমার', 'আমি', 'আমাকে', 'আপনার', 'আপনি',
        'এর', 'এই', 'ওই', 'সেই', 'তা', 'এটা', 'ওটা',
        // Verbs (request forms)
        'দেখুন', 'দেখান', 'দেখতে', 'দেখাও', 'দেখো',
        'চাই', 'চাচ্ছি', 'দরকার', 'লাগবে', 'লাগবে',
        'বলুন', 'বলো', 'বলতে', 'জানান', 'জানাতে', 'জানতে',
        'দিতে', 'দিন', 'দাও', 'করুন', 'করতে',
        // Question words
        'কি', 'কী', 'কোন', 'কেমন', 'কোথায়', 'কখন',
        // Common filler
        'আছে', 'আসে', 'নাকি', 'please', 'pls', 'plz',
        'একটু', 'একটু', 'হলে', 'পারবেন',
        // Punctuation
        '?', '।', '!', '"', "'",
    ];

    /**
     * Lowercase + strip the given words from keyword.
     */
    public static function clean(string $keyword, array $stripWords): string
    {
        $lower = mb_strtolower($keyword);
        // Merge caller's strip words with Bengali common words
        $allStrip = array_unique(array_merge($stripWords, self::BENGALI_STRIP));

        foreach ($allStrip as $word) {
            $lower = str_ireplace($word, '', $lower);
        }

        // Collapse multiple spaces
        return trim(preg_replace('/\s+/u', ' ', $lower));
    }

    /**
     * Returns true if keyword is empty or a generic catch-all term.
     */
    public static function isGeneric(string $keyword): bool
    {
        $clean = mb_strtolower(trim($keyword));

        if (empty($clean)) return true;

        // Exact match
        if (in_array($clean, self::GENERIC_TERMS, true)) return true;

        // If after removing generic terms the string is nearly empty, treat as generic
        $stripped = $clean;
        foreach (self::GENERIC_TERMS as $term) {
            $stripped = str_replace($term, '', $stripped);
        }
        $stripped = trim(preg_replace('/\s+/u', ' ', $stripped));

        // If what remains is very short (<=2 chars), it's generic
        if (mb_strlen($stripped) <= 2) return true;

        return false;
    }

    /**
     * Safely unwrap a Laravel Paginator or plain array/collection.
     *
     * @return array<mixed>
     */
    public static function unwrapPaginator(mixed $result): array
    {
        if (is_object($result) && method_exists($result, 'items')) {
            return $result->items();
        }
        if (is_array($result)) {
            return $result;
        }
        return iterator_to_array($result);
    }

    /**
     * Unwrap a Laravel JsonResponse OR plain array from MIS service calls.
     *
     * @return array<mixed>
     */
    public static function unwrapJsonResponse(mixed $response): array
    {
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response->getData(true);
        }
        return (array) $response;
    }
}
