<?php

declare(strict_types=1);

namespace PhlyComic;

use PhlyComic\ComicSource\BasicInstructions;
use PhlyComic\ComicSource\BloomCounty;
use PhlyComic\ComicSource\CalvinAndHobbes;
use PhlyComic\ComicSource\CloseToHome;
use PhlyComic\ComicSource\CommitStrip;
use PhlyComic\ComicSource\CtrlAltDel;
use PhlyComic\ComicSource\CulDeSac;
use PhlyComic\ComicSource\DorkTower;
use PhlyComic\ComicSource\Drive;
use PhlyComic\ComicSource\FMinus;
use PhlyComic\ComicSource\ForBetterOrForWorse;
use PhlyComic\ComicSource\FoxTrot;
use PhlyComic\ComicSource\GarfieldMinusGarfield;
use PhlyComic\ComicSource\Goats;
use PhlyComic\ComicSource\LakeGary;
use PhlyComic\ComicSource\ListenToMe;
use PhlyComic\ComicSource\LunarBaboon;
use PhlyComic\ComicSource\NonSequitur;
use PhlyComic\ComicSource\NotInventedHere;
use PhlyComic\ComicSource\Oatmeal;
use PhlyComic\ComicSource\Peanuts;
use PhlyComic\ComicSource\PennyArcade;
use PhlyComic\ComicSource\PhDComics;
use PhlyComic\ComicSource\Pickles;
use PhlyComic\ComicSource\SaturdayMorningBreakfastCereal;
use PhlyComic\ComicSource\ScenesFromAMultiverse;
use PhlyComic\ComicSource\Sheldon;
use PhlyComic\ComicSource\Xkcd;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function array_key_exists;

final class ComicFactory implements ContainerInterface
{
    public function __construct()
    {
        $aliasMap  = [];
        $supported = [];
        foreach (self::COMIC_SOURCES as $class) {
            $comic                  = $class::provides();
            $aliasMap[$comic->name] = $class;
            $supported[$class]      = $comic;
        }

        $this->aliasMap  = $aliasMap;
        $this->supported = $supported;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->aliasMap);
    }

    public function get(string $name): ComicSource
    {
        if (! array_key_exists($name, $this->aliasMap)) {
            throw new class ($name) extends RuntimeException implements ContainerExceptionInterface {
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
        BasicInstructions::class,
        BloomCounty::class,
        CalvinAndHobbes::class,
        CloseToHome::class,
        CommitStrip::class,
        CtrlAltDel::class,
        CulDeSac::class,
        DorkTower::class,
        Drive::class,
        FMinus::class,
        ForBetterOrForWorse::class,
        FoxTrot::class,
        GarfieldMinusGarfield::class,
        Goats::class,
        LakeGary::class,
        ListenToMe::class,
        LunarBaboon::class,
        NonSequitur::class,
        NotInventedHere::class,
        Oatmeal::class,
        Peanuts::class,
        PennyArcade::class,
        PhDComics::class,
        Pickles::class,
        SaturdayMorningBreakfastCereal::class,
        ScenesFromAMultiverse::class,
        Sheldon::class,
        Xkcd::class,
    ];

    /** @var array<non-empty-string, class-string> */
    private readonly array $aliasMap;

    /** @var array<class-string, Comic> */
    private readonly array $supported;
}
