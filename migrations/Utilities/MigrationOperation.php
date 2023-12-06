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

    public function doStep(int $step, string &$type)
    {
        if ($step == 0) {
            throw new Exception("The step given can not be at 0.");
        }

        if ($step < 0) {
            $step = -$step;
            if($this->MIGRATIONS_DONE == []){
                $type = "i";
                return "----------Nothing to rollback ¯\\_(ツ)_/¯----------";
            }
            $type = "s";
            return $this->rollback($step);
        } else {
            return $this->migrate($step, $type);
        }
    }

    private function rollback(int $step)
    {

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
        return "----------Successfully rollbacked " . $step . " migration(s) \\^o^/ ----------";
    }

    public function migrate(?int $step, string &$type)
    {
        $newMigrationsCount = count($this->MIGRATIONS_FILES) - count($this->MIGRATIONS_DONE);

        if ($newMigrationsCount == 0) {
            $type = "i";
            return "----------No new migration added ¯\\_(ツ)_/¯----------";
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
            $type = "s";
            return "----------Migration successfull \\^o^/ ----------";
        }

        $type = "s";
        return "----------Successfully advanced " . $newMigrationsCount . " migration(s) \\^o^/ ----------";
    }

    public function testForNewMigrations()
    {
        $newMigrationExists = false;
        foreach ($this->MIGRATIONS_FILES as $file) {
            $migrationFileWithoutExtension =  str_replace(".php", "", $file);
            $creationDate = (int)substr($migrationFileWithoutExtension, 10, 14);

            if ($newMigrationExists == false && !in_array($creationDate, $this->MIGRATIONS_DONE)) {
                $newMigrationExists = true;
                break;
            }
        }
        return $newMigrationExists;
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

    public function rollbackAll(string &$type)
    {
        
        $type = "i";
        if($this->MIGRATIONS_DONE == [])
            return "----------Nothing to rollback ¯\\_(ツ)_/¯----------";

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
        $type = "s";
        return "----------Reset successfull \\^o^/ ----------";
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
}
