<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit094984e79b09ec423855064dfcb97f21
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Softonic\\Gigya\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Softonic\\Gigya\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit094984e79b09ec423855064dfcb97f21::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit094984e79b09ec423855064dfcb97f21::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
