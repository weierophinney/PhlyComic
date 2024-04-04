<?php

namespace PhlyComic;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class ComicFactory implements ContainerInterface
{
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->aliasMap);
    }

    public function get(string $name): ComicSource
    {
        if (! array_key_exists($name, $this->aliasMap)) {
            throw new class($name) extends RuntimeException implements ContainerExceptionInterface {
                public function __construct(string $name)
                {
                    parent::__construct("Comic source by name '$name' does not exist");
                }
            };
        }

        $class = $this->aliasMap[$name];
        return new $class();
    }

    public function getSupported(): array
    {
        return $this->supported;
    }

    /** @var list<class-string<ComicSource>> List of comic source classes */
    private const COMIC_SOURCES = [
        'PhlyComic\ComicSource\BasicInstructions',
        'PhlyComic\ComicSource\CommitStrip',
        'PhlyComic\ComicSource\CtrlAltDel',
        'PhlyComic\ComicSource\Dilbert',
        'PhlyComic\ComicSource\DorkTower',
        'PhlyComic\ComicSource\Drive',
        'PhlyComic\ComicSource\ForBetterOrForWorse',
        'PhlyComic\ComicSource\FoxTrot',
        'PhlyComic\ComicSource\GarfieldMinusGarfield',
        'PhlyComic\ComicSource\GoComics',
        'PhlyComic\ComicSource\LakeGary',
        'PhlyComic\ComicSource\ListenToMe',
        'PhlyComic\ComicSource\LunarBaboon',
        'PhlyComic\ComicSource\NotInventedHere',
        'PhlyComic\ComicSource\Oatmeal',
        'PhlyComic\ComicSource\PennyArcade',
        'PhlyComic\ComicSource\PhDComics',
        'PhlyComic\ComicSource\ReptilisRex',
        'PhlyComic\ComicSource\SaturdayMorningBreakfastCereal',
        'PhlyComic\ComicSource\Sheldon',
        'PhlyComic\ComicSource\ScenesFromAMultiverse',
        'PhlyComic\ComicSource\UserFriendly',
        'PhlyComic\ComicSource\Xkcd',
    ];

    /** @var array<non-empty-string, class-string> */
    private readonly array $aliasMap;

    /** @var array<class-string, Comic> */
    private readonly array $supported;

    private function __construct()
    {
        $aliasMap  = [];
        $supported = [];
        foreach (self::COMIC_SOURCES as $class) {
            $comic                  = $class::provides();
            $aliasMap[$comic->name] = $class;
            $supported[$class]      = $comic;
        }

        $this->aliasMap = $aliasMap;
        $this->supported = $supported;
    }
}
