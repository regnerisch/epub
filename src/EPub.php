<?php


namespace Regnerisch\EPub;

use Symfony\Component\Finder\Finder;

class EPub
{
    private string $epub;
    private string $destination;

    public function __construct(string $epub)
    {
        $this->destination = sys_get_temp_dir() . '/' . sha1_file($epub);

        $zip = new \ZipArchive();
        $zip->open($epub);
        $zip->extractTo($this->destination);
    }

    public function getNCXFile(): ?string
    {
        $finder = new Finder();

        $finder
            ->files()
            ->in($this->destination)
            ->name('*.ncx');

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                return $file->getRealPath();
            }
        }

        return null;
    }

    public function getCSSFiles(): ?Finder
    {
        $finder = new Finder();

        $finder
            ->files()
            ->in($this->destination)
            ->name('*.css');

        if ($finder->hasResults()) {
            return $finder;
        }

        return null;
    }

    public function getCSSAsString(): string
    {
        $css = '';
        if ($finder = $this->getCSSFiles()) {
            foreach ($finder as $file) {
                $css .= file_get_contents($file->getRealPath());
            }
        }

        return $css;
    }

    public function getImages($formats = ['jpg', 'jpeg', 'png']): ?Finder
    {
        $formats = array_map(static function ($name) {
            return '*.' . $name;
        }, $formats);

        $finder = new Finder();

        $finder
            ->files()
            ->in($this->destination)
            ->name($formats);

        if ($finder->hasResults()) {
            return $finder;
        }

        return null;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }
}
