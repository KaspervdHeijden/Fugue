<?php

declare(strict_types=1);

namespace Fugue\Localization\Language;

interface LanguageRepository
{
    /**
     * Gets all installed languages
     *
     * @return Language[]
     */
    public function getLanguages(): array;

    /**
     * Gets a language by code.
     *
     * @param string $code   The code to get the language for.
     * @return Language|null The language or NULL if not found.
     */
    public function getLanguageByCode(string $code): ?Language;
}
