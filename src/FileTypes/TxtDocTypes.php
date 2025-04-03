<?php 

declare(strict_types=1);

namespace SecureUpload\FileTypes;

class TxtDocTypes
{
    private const DOCS = [
        ['mime' => 'text/csv', 'ext' => 'csv'],
        ['mime' => 'text/plain', 'ext' => 'txt'],
        ['mime' => 'application/msword', 'ext' => 'doc'],
        ['mime' => 'application/vnd.ms-excel', 'ext' => 'xls'],
        ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'ext' => 'xlsx'],
        ['mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ext' => 'docx'],
        ['mime' => 'application/pdf', 'ext' => 'pdf'],
        ['mime' => 'text/rtf', 'ext' => 'rtf'],
    ];

    public static function getAllExtensions(): array
    {
        $extensions = [];
        foreach (self::DOCS as $item) {
            $extensions[] = $item['ext'];
        }
        return array_unique($extensions);
    }

    public static function getAllMimes(): array
    {
        $mimes = [];
        foreach (self::DOCS as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    public static function getAllTypesExcept(array $extensions): array
    {
        $types = [];
        foreach (self::DOCS as $item) {
            if (!in_array($item['ext'], $extensions, true)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}