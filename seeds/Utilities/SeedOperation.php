<?php

namespace Seeds\Utilities;

class SeedOperation
{
    /** @var \Seeds\Utilities\Seed[] */
    private array $seeds = [];

    private array $seedFiles = [];

    public function setAllSeedsFile()
    {
        $this->seedFiles = array_filter(scandir(__DIR__ . "/../"), fn ($f) => is_file(__DIR__ . "/../" . $f));
        $this->requireAllSeedFiles();
    }

    private function requireAllSeedFiles()
    {
        foreach ($this->seedFiles as $seedFile) {
            require(__DIR__ . "/../" . $seedFile);
        }
    }

    public function createSeeds()
    {
        
        foreach ($this->seedFiles as $file) {
            $seed = new Seed();
            array_push($this->seeds, $seed);
            $className = $this->getClassNameFromFile($file);
            /**
             * @var \Seeds\Utilities\AbstractSeed
             */
            $abstractSeed = new $className();
            $abstractSeed->seed($seed);
        }
    }

    private function getClassNameFromFile($file)
    {
        return "\\Seeds\\" . str_replace(".php", "", $file);
    }
}