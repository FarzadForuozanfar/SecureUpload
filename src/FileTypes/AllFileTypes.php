<?php 

declare(strict_types=1);

namespace SecureUpload\FileTypes;

class AllFileTypes
{
    private array $allTypes;

    public function __construct()
    {
        $this->allTypes = array_merge(
            ImageTypes::getAllTypesExcept([]),
            TxtDocTypes::getAllTypesExcept([]),
            CompressedTypes::getAllTypesExcept([])
        );
    }

    public function getAllExtensions(): array
    {
        $extensions = [];
        foreach ($this->allTypes as $item) {
            $extensions[] = $item['ext'];
        }
        return array_unique($extensions);
    }

    public function getAllMimes(): array
    {
        $mimes = [];
        foreach ($this->allTypes as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    public function checkExtensionWithMime(string $extension): ?string
    {
        foreach ($this->allTypes as $item) {
            if ($item['ext'] === $extension) {
                return $item['mime'];
            }
        }
        return null;
    }

    public function getSpecificFileType(string $extension): ?array
    {
        foreach ($this->allTypes as $item) {
            if ($item['ext'] === $extension) {
                return $item;
            }
        }
        return null;
    }

    public function getAllTypesExcept(array $extensions): array
    {
        $types = [];
        foreach ($this->allTypes as $item) {
            if (!in_array($item['ext'], $extensions, true)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}