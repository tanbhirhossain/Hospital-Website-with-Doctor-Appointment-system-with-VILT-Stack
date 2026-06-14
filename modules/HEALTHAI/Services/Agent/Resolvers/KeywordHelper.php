<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

/**
 * Shared keyword utilities for all DataResolvers.
 * Extracted as a class to avoid namespace-level function collisions
 * when multiple files share the same namespace.
 */
final class KeywordHelper
{
    private const GENERIC_TERMS = ['list', 'all', 'shob', 'ki ki', 'sob', 'everything'];

    /**
     * Lowercase + strip the given words from keyword.
     */
    public static function clean(string $keyword, array $stripWords): string
    {
        return trim(str_ireplace($stripWords, '', mb_strtolower($keyword)));
    }

    /**
     * Returns true if keyword is empty or a generic catch-all term.
     */
    public static function isGeneric(string $keyword): bool
    {
        return empty($keyword) || in_array(mb_strtolower(trim($keyword)), self::GENERIC_TERMS, true);
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
