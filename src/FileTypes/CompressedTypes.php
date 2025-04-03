<?php 

declare(strict_types=1);

namespace SecureUpload\FileTypes;

class CompressedTypes
{
    private const COMP = [
        ['mime' => 'application/zip', 'ext' => 'zip'],
        ['mime' => 'application/x-rar', 'ext' => 'rar'],
    ];

    public static function getAllExtensions(): array
    {
        $extensions = [];
        foreach (self::COMP as $item) {
            $extensions[] = $item['ext'];
        }
        return array_unique($extensions);
    }

    public static function getAllMimes(): array
    {
        $mimes = [];
        foreach (self::COMP as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    public static function getAllTypesExcept(array $extensions): array
    {
        $types = [];
        foreach (self::COMP as $item) {
            if (!in_array($item['ext'], $extensions, true)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}
