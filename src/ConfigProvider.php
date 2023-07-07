<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Hyperf;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                //                \Kkguan\PHPMapstruct\Mapper::class => \Kkguan\PHPMapstruct\Hyperf\Mapper::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'mapstruct config file.',
                    'source' => __DIR__ . '/../publish/mapstruct.php',
                    'destination' => BASE_PATH . '/config/autoload/mapstruct.php',
                ],
            ],
        ];
    }
}
