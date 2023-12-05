<?php

use Migrations\Utilities\Schema;

define("HISTORIC_PATH", __DIR__ . "/../history/historic.json");

require(__DIR__ . "/../Utilities/Column/ColumnBaseInterface.php");
require(__DIR__ . "/../Utilities/Column/ColumnUpdateInterface.php");
require(__DIR__ . "/../Utilities/Column/ColumnCreateInterface.php");
require(__DIR__ . "/../Utilities/Column/ColumnBase.php");
require(__DIR__ . "/../Utilities/Column/ColumnCreate.php");
require(__DIR__ . "/../Utilities/Column/ColumnUpdate.php");

require(__DIR__ . "/../Utilities/TableCreateInterface.php");
require(__DIR__ . "/../Utilities/TableUpdateInterface.php");

require(__DIR__ . "/../Utilities/ColumnType.php");

require(__DIR__ . "/../Utilities/Instruction.php");

require(__DIR__ . "/../Utilities/Migration.php");
require(__DIR__ . "/../Utilities/Schema.php");
require(__DIR__ . "/../Utilities/Table.php");

$migrationOperation = $argv[1] ?? null;

class Database
{
    private PDO $pdo;
    protected string $driver = "pgsql";
    public function __construct(string $host = "localhost", string $dbName = "migration", string $port = "5432", string $user = "postgres", string $password = "admin")
    {
        try {
            $dsn = "$this->driver:host=$host;port=$port;dbname=$dbName;";
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function run(string $query, $args = null)
    {
        if (is_null($args)) {
            return $this->pdo->query($query);
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($args);
        return $stmt;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->pdo->commit();
    }

    public function rollbackTransaction()
    {
        $this->pdo->rollBack();
    }
}

class MigrationOperation
{
    public static $MIGRATIONS_DONE = [];
    public static $MIGRATIONS_FILES = [];

    public static function migrate(Database $db)
    {
        foreach (self::$MIGRATIONS_FILES as $migrationFile) {
            $migrationFileWithoutExtension =  str_replace(".php", "", $migrationFile);
            $className = self::retrieveClassnameFromFile($migrationFile);
            $creationDate = (int)substr($migrationFileWithoutExtension, 10, 14);
            if (!in_array($creationDate, self::$MIGRATIONS_DONE)) {
                /**
                 * @var \Migrations\Utilities\Migration
                 */
                $migration = new $className();
                $schema = new Schema();
                $migration->up($schema);
                $queries = $migration->createInstructions($schema)->getQueries();

                foreach ($queries as $query) {
                    $db->run($query);
                }
                array_push(self::$MIGRATIONS_DONE, $creationDate);
            }
        }

        self::updateHistoric();
    }

    public static function testForNewMigrations()
    {
        $newMigrationExists = false;
        foreach (self::$MIGRATIONS_FILES as $file) {
            $migrationFileWithoutExtension =  str_replace(".php", "", $file);
            $creationDate = (int)substr($migrationFileWithoutExtension, 10, 14);

            if ($newMigrationExists == false && !in_array($creationDate, self::$MIGRATIONS_DONE)) {
                $newMigrationExists = true;
                break;
            }
        }
        return $newMigrationExists;
        // if (!$newMigrationExists) {
        //     colorLog("\n\n\n------No new migration added ¯\\_(ツ)_/¯------");
        //     die();
        // }
    }

    private static function retrieveClassnameFromFile(string $file)
    {
        $migrationFileWithoutExtension =  str_replace(".php", "", $file);
        return "\\Migrations\\" . $migrationFileWithoutExtension;
    }

    private static function retrieveFilenameFromDate(string $date)
    {
        foreach (self::$MIGRATIONS_FILES as $file) {
            if (str_contains($file, $date)) {
                return $file;
            }
        }

        return "";
    }

    public static function rollbackAll(Database $db)
    {
        sort(self::$MIGRATIONS_DONE);
        self::$MIGRATIONS_DONE = array_reverse(self::$MIGRATIONS_DONE);

        foreach (self::$MIGRATIONS_DONE as $migrationDone) {
            $className = self::retrieveClassnameFromFile(self::retrieveFilenameFromDate($migrationDone));
            /**
             * @var \Migrations\Utilities\Migration
             */
            $migration = new $className();
            $schema = new Schema();
            $migration->down($schema);
            $queries = $migration->createInstructions($schema)->getQueries();
            foreach ($queries as $query) {
                $db->run($query);
            }
            array_pop(self::$MIGRATIONS_DONE);
        }

        self::updateHistoric();
    }

    public static function updateHistoric()
    {
        try {
            sort(self::$MIGRATIONS_DONE);
            $fstream = fopen(HISTORIC_PATH, "w");
            fwrite($fstream, json_encode(self::$MIGRATIONS_DONE));
        } finally {
            fclose($fstream);
        }
    }

    public static function setMigrationsDone()
    {
        if (file_exists(HISTORIC_PATH)) {
            self::$MIGRATIONS_DONE = json_decode(file_get_contents(HISTORIC_PATH)) ?? [];
        }
    }

    public static function requireAllMigrations()
    {
        foreach (self::$MIGRATIONS_FILES as $file) {
            require(__DIR__ . "/../" . $file);
        }
    }

    public static function setMigrationsFiles()
    {
        self::$MIGRATIONS_FILES = array_filter(scandir(__DIR__ . "/../"), fn ($f) => is_file(__DIR__ . "/../" . $f));
    }
}

function colorLog($str, $type = 'i')
{
    clearLog();
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

function clearLog(){
    for($i = 0; $i < 50; $i++){
        echo "\n\r";
    }
}

MigrationOperation::setMigrationsDone();
MigrationOperation::setMigrationsFiles();
MigrationOperation::requireAllMigrations();

// var_dump($db->run("select 1")->fetchAll());
$db = new Database();
$db->beginTransaction();
try {
    if ($migrationOperation == null) {
        
        if(MigrationOperation::testForNewMigrations() == false){
            colorLog("----------No new migration added ¯\\_(ツ)_/¯----------");
            die();
        }

        MigrationOperation::migrate($db);
        colorLog("\n\n\n----------Migration successfull \\^o^/ ----------", "s");
    } else if ($migrationOperation == "--reset") {

        if (MigrationOperation::$MIGRATIONS_DONE == []) {
            colorLog("\n\n\n----------Nothing to rollback ¯\\_(ツ)_/¯----------");
            die();
        }
        MigrationOperation::rollbackAll($db);
        colorLog("\n\n\n----------Reset successfull \\^o^/ ----------", "s");
    }
    $db->commitTransaction();
} catch (Exception $e) {
    colorLog("\n\n\n----------An error occured rolling back transaction (っ °Д °;)っ----------", "e");
    // echo "\n\n---------------AN ERROR OCCURED ROLLING BACK TRANSACTION---------------\n\n";
    colorLog($e->getMessage(), "w");
    $db->rollbackTransaction();
}
