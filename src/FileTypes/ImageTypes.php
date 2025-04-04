<?php

declare(strict_types=1);

namespace SecureUpload\FileTypes;

class ImageTypes
{
    private const TYPES = [
        ['mime' => 'image/jpeg', 'ext' => 'jpg'],
        ['mime' => 'image/jpg',  'ext' => 'jpg'],
        ['mime' => 'image/png',  'ext' => 'png'],
        ['mime' => 'image/gif',  'ext' => 'gif'],
        ['mime' => 'image/tiff', 'ext' => 'tiff'],
        ['mime' => 'image/svg+xml', 'ext' => 'svg'],
        ['mime' => 'image/x-ms-bmp',  'ext' => 'bmp'],
        ['mime' => 'application/octet-stream', 'ext' => 'psd'],
    ];

    public static function getAllExtensions(): array
    {
        $extensions = [];
        foreach (self::TYPES as $item) {
            $extensions[] = $item['ext'];
        }
        return array_unique($extensions);
    }

    public static function getAllMimes(): array
    {
        $mimes = [];
        foreach (self::TYPES as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    public static function getAllTypesExcept(array $extensions): array
    {
        $types = [];
        foreach (self::TYPES as $item) {
            if (!in_array($item['ext'], $extensions, true)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}