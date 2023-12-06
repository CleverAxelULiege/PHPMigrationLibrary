<?php

namespace Migrations\Utilities;

use Exception;
use Migrations\Utilities\Schema;
use Migrations\Utilities\Database\Database;

class MigrationOperation
{
    public array $MIGRATIONS_DONE = [];
    public array $MIGRATIONS_FILES = [];

    public function __construct(public Database $db, private string $HISTORIC_PATH)
    {
        $this->setMigrationsDone();
        $this->setMigrationsFiles();
        $this->requireAllMigrations();
    }

    public function doStep(int $step)
    {
        $this->clearLog();
        if ($step == 0) {
            $this->colorLog("The step given can not be at 0, YOU FOOL (╯°□°）╯︵ ┻━┻", "w");
            return;
        }

        if ($step < 0) {
            $step = -$step;
            if ($this->MIGRATIONS_DONE == []) {
                $this->colorLog("----------Nothing to rollback ¯\\_(ツ)_/¯----------", "i");
                return;
            }
            $this->rollback($step);
        } else {
            $this->migrate($step);
        }
    }

    private function rollback(int $step)
    {

        $this->clearLog();
        if ($step > count($this->MIGRATIONS_DONE)) {
            $step = count($this->MIGRATIONS_DONE);
        }

        sort($this->MIGRATIONS_DONE);
        $this->MIGRATIONS_DONE = array_reverse($this->MIGRATIONS_DONE);
        for ($i = 0; $i < $step; $i++) {
            $className = $this->retrieveClassnameFromFile($this->retrieveFilenameFromDate($this->MIGRATIONS_DONE[$i]));
            /**
             * @var \Migrations\Utilities\Migration
             */
            $migration = new $className();
            $schema = new Schema();
            $migration->down($schema);
            $queries = $migration->createInstructions($schema)->getQueries();
            foreach ($queries as $query) {
                $this->db->run($query);
            }
            $this->MIGRATIONS_DONE = array_filter($this->MIGRATIONS_DONE, fn ($m) => $m != $this->MIGRATIONS_DONE[$i]);
        }
        $this->updateHistoric();
        $this->colorLog("----------Successfully rollbacked " . $step . " migration(s) \\^o^/ ----------", "s");
    }

    public function migrate(?int $step)
    {
        
        $newMigrationsCount = count($this->MIGRATIONS_FILES) - count($this->MIGRATIONS_DONE);

        if ($newMigrationsCount == 0) {
            $this->colorLog("----------No new migration added ¯\\_(ツ)_/¯----------", "i");
            return;
        }

        $i = 0;

        foreach ($this->MIGRATIONS_FILES as $migrationFile) {

            if ($step != null && $i >= $step) {
                break;
            }

            $migrationFileWithoutExtension =  str_replace(".php", "", $migrationFile);
            $className = $this->retrieveClassnameFromFile($migrationFile);
            $creationDate = (int)substr($migrationFileWithoutExtension, 10, 14);
            if (!in_array($creationDate, $this->MIGRATIONS_DONE)) {
                /**
                 * @var \Migrations\Utilities\Migration
                 */
                $migration = new $className();
                $schema = new Schema();
                $migration->up($schema);
                $queries = $migration->createInstructions($schema)->getQueries();

                foreach ($queries as $query) {
                    $this->db->run($query);
                }
                array_push($this->MIGRATIONS_DONE, $creationDate);

                $i++;
            }
        }

        $this->updateHistoric();
        if ($step === null) {
            $this->clearLog();
            $this->colorLog("----------Migration successfull \\^o^/ ----------", "s");
            return;
        }

        $this->colorLog("----------Successfully advanced " . $step . " migration(s) \\^o^/ ----------", "s");
    }



    private function retrieveClassnameFromFile(string $file)
    {
        $migrationFileWithoutExtension =  str_replace(".php", "", $file);
        return "\\Migrations\\" . $migrationFileWithoutExtension;
    }

    private function retrieveFilenameFromDate(string $date)
    {
        foreach ($this->MIGRATIONS_FILES as $file) {
            if (str_contains($file, $date)) {
                return $file;
            }
        }

        return "";
    }

    public function rollbackAll()
    {

        $this->clearLog();
        if ($this->MIGRATIONS_DONE == []) {
            $this->colorLog("----------Nothing to rollback ¯\\_(ツ)_/¯----------", "i");
            return;
        }

        sort($this->MIGRATIONS_DONE);
        $this->MIGRATIONS_DONE = array_reverse($this->MIGRATIONS_DONE);

        foreach ($this->MIGRATIONS_DONE as $migrationDone) {
            $className = $this->retrieveClassnameFromFile($this->retrieveFilenameFromDate($migrationDone));
            /**
             * @var \Migrations\Utilities\Migration
             */
            $migration = new $className();
            $schema = new Schema();
            $migration->down($schema);
            $queries = $migration->createInstructions($schema)->getQueries();
            foreach ($queries as $query) {
                $this->db->run($query);
            }
            array_pop($this->MIGRATIONS_DONE);
        }

        $this->updateHistoric();
        $this->colorLog("----------Reset successfull \\^o^/ ----------", "s");
    }

    public function updateHistoric()
    {
        try {
            sort($this->MIGRATIONS_DONE);
            $fstream = fopen($this->HISTORIC_PATH, "w");
            fwrite($fstream, json_encode($this->MIGRATIONS_DONE));
        } finally {
            fclose($fstream);
        }
    }

    public function setMigrationsDone()
    {
        if (file_exists($this->HISTORIC_PATH)) {
            $this->MIGRATIONS_DONE = json_decode(file_get_contents($this->HISTORIC_PATH)) ?? [];
        }
    }

    public function requireAllMigrations()
    {
        foreach ($this->MIGRATIONS_FILES as $file) {
            require(__DIR__ . "/../" . $file);
        }
    }

    public function setMigrationsFiles()
    {
        $this->MIGRATIONS_FILES = array_filter(scandir(__DIR__ . "/../"), fn ($f) => is_file(__DIR__ . "/../" . $f));
    }

    public function colorLog($str, $type = 'i')
    {
        switch ($type) {
            case 'e': //error
                echo "\033[31m$str \033[0m\n";
                break;
            case 's': //success
                echo "\033[32m$str \033[0m\n";
                break;
            case 'w': //warning
                echo "\033[33m$str \033[0m\n";
                break;
            case 'i': //info
                echo "\033[36m$str \033[0m\n";
                break;
        }
    }

    public function clearLog()
    {
        for ($i = 0; $i < 25; $i++) {
            echo "\n\r";
        }
    }
}
