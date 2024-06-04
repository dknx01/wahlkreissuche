<?php

/*
 * This file is part of the SymfonyCasts VerifyEmailBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace App\Security;

class VerifyEmailQueryUtility
{
    public function getTokenFromQuery(string $uri): string
    {
        $params = $this->getQueryParams($uri);

        return $params['token'];
    }

    public function getExpiryTimestamp(string $uri): int
    {
        $params = $this->getQueryParams($uri);

        if (empty($params['expires'])) {
            return 0;
        }

        return (int) $params['expires'];
    }

    /**
     * @return array<string, string>
     */
    private function getQueryParams(string $uri): array
    {
        $params = [];
        $urlComponents = parse_url($uri);

        if (\array_key_exists('query', $urlComponents)) {
            parse_str($urlComponents['query'] ?? '', $params);
            if (array_key_exists('?expires', $params)) {
                $params['expires'] = $params['?expires'];
                unset($params['?expires']);
            }
        }

        return $params;
    }
}
