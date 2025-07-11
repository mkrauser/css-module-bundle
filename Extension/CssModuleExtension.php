<?php

/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MaK\CssModuleBundle\Extension;

use MaK\CssModuleBundle\NodeVisitor\CssScopeNodeVisitor;
use MaK\CssModuleBundle\TokenParser\ImportModuleTokenParser;
use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;

final class CssModuleExtension extends AbstractExtension
{
    private readonly string $hashName;

    private readonly string $hashFunction;

    private readonly string $hashDigest;

    private readonly int $hashDigestLength;

    public function __construct(
        private readonly string $projectDir,
        private string $localIdentName = '[hash:base64:5]',
        private readonly ?string $localIdentHashSalt = null
    ) {
        preg_match("/\[(?:([^:\]]+):)?(?:(hash|contenthash|fullhash))(?::([a-z]+\d*))?(?::(\d+))?\]/i", $this->localIdentName, $matches);

        if ([] === $matches) {
            throw new RuntimeException('Invalid localIdentName given');
        }

        $this->hashName = $matches[2] ?? 'hash';
        $this->hashFunction = $matches[1] === "" ? 'md4' : $matches[1];
        $this->hashDigest = $matches[3] ?? 'base64';
        $this->hashDigestLength = isset($matches[4]) ? intval($matches[4]) : 5;

        $this->localIdentName = preg_replace_callback(
            '/\[(?:([^:\]]+):)?(?:hash|contenthash|fullhash)(?::([a-z]+\d*))?(?::(\d+))?\]/i',
            fn ($matches): string => 'fullhash' === $this->hashName ? '[fullhash]' : '[contenthash]',
            $this->localIdentName
        );
    }

    /** @return TwigFunction[] */
    public function getFunctions()
    {
        return [
            new TwigFunction('scope', $this->cssScope(...)),
        ];
    }

    /**
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [
            /*
             * {% importModule "css-file" %}
             */
            new ImportModuleTokenParser(),
        ];
    }

    public function getNodeVisitors(): array
    {
        return [
            new CssScopeNodeVisitor($this->projectDir),
        ];
    }

    /** @param string|string[] $scopes */
    public function cssScope(array|string $scopes, string $modulePath): string
    {
        if (!is_array($scopes)) {
            $scopes = [$scopes];
        }

        $hashedScopes = [];
        foreach ($scopes as $scope) {
            // @phpstan-ignore-next-line
            if (!is_string($scope)) {
                throw new RuntimeException('Scope must be a string');
            }

            $hashedScopes[] = $this->hash($modulePath, $scope);
        }

        return implode(' ', $hashedScopes);
    }

    private function hash(string $relativeModulePath, string $scope): string
    {
        $localIdentHash = '';

        for ($tier = 0; strlen($localIdentHash) < $this->hashDigestLength; ++$tier) {
            $contentToHash = '';
            if (isset($this->localIdentHashSalt)) {
                $contentToHash .= $this->localIdentHashSalt;
            }

            $tierSalt = pack('V', $tier); // Assuming $tier is already defined

            $contentToHash .= $tierSalt;

            $contentToHash .= $relativeModulePath.chr(0).$scope;

            if ('base64' === $this->hashDigest) {
                $localIdentHash = base64_encode($localIdentHash.hash($this->hashFunction, $contentToHash, true));
            } else {
                $localIdentHash = hash($this->hashFunction, $contentToHash);
            }

            // Remove all leading digits
            $localIdentHash = preg_replace("/^\d+/", '', $localIdentHash);
            // Replace all slashes with underscores (same as in base64url)
            $localIdentHash = str_replace('/', '_', $localIdentHash);
            // Remove everything that is not an alphanumeric or underscore
            $localIdentHash = preg_replace('/[^A-Za-z0-9_]+/', '', $localIdentHash);
            // Shorten to $hashDigestLength
            $localIdentHash = substr((string) $localIdentHash, 0, $this->hashDigestLength);
        }

        return str_replace('[contenthash]', $localIdentHash, $this->localIdentName);
    }
}
