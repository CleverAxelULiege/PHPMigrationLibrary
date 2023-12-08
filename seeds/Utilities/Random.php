<?php

namespace Seeds\Utilities;

use DateTime;
use Normalizer;

class Random
{
    public static function password(int $length = 10)
    {
        $length = floor($length / 2);

        return bin2hex(random_bytes($length));
    }

    public static function email()
    {
        $extensionList = ["be", "fr", "com", "net", "org", "tv"];
        $domainList = ["mail", "outlook", "gmail", "hotmail", "oof"];
        $randomExtension = $extensionList[rand(0, count($extensionList) - 1)];
        $randomDomain = $domainList[rand(0, count($domainList) - 1)];
        return self::pickRandomWordFromDictionnary() . (rand(0, 3) == 3 ? "." . self::pickRandomWordFromDictionnary() : "") . "@" . $randomDomain . "." . $randomExtension;
    }

    public static function name()
    {
        return self::pickRandomNameFromDictionnary();
    }

    public static function birthdate(string $format = "Y-m-d")
    {
        $currentYear = (int)date("Y");
        $year = rand($currentYear - 100, $currentYear);
        $month = rand(1, 12);
        $day = rand(1, 31);
        $date = new DateTime();
        $date->setDate($year, $month, $day);
        return $date->format($format);
    }

    public static function text()
    {
        return self::sentence();
    }

    public static function timestamp(string $format = "Y-m-d H:i:s")
    {
        $currentYear = (int)date("Y");
        $year = rand($currentYear - 3, $currentYear);
        $month = rand(1, 12);
        $day = rand(1, 31);
        $hour = rand(0, 23);
        $minute = rand(0, 59);
        $second = rand(0, 59);
        $date = new DateTime();
        $date->setDate($year, $month, $day)->setTime($hour, $minute, $second);
        return $date->format($format);
    }

    private static function pickRandomNameFromDictionnary()
    {
        $randomLine = rand(0, 1425);
        $i = 0;
        $randomName = "";
        try {
            $fstream = fopen(__DIR__ . "/dictionnary/name_dictionnary_s.txt", "r");
            while (($randomName = fgets($fstream, 4096)) !== false && $i < $randomLine) {
                $i++;
            }
        } finally {
            fclose($fstream);
        }

        return str_replace(["\r", "\n"], '', $randomName);;
    }

    private static function pickRandomWordFromDictionnary()
    {
        $randomLine = rand(0, 22730);
        $i = 0;
        $randomWord = "";
        try {
            $fstream = fopen(__DIR__ . "/dictionnary/word_dictionnary_s.txt", "r");
            while (($randomWord = fgets($fstream, 4096)) !== false && $i < $randomLine) {
                $i++;
            }
        } finally {
            fclose($fstream);
        }

        $randomWord = str_replace(["\r", "\n", "-", " "], '', $randomWord);
        $randomWord = strtolower(preg_replace('/[\x{0300}-\x{036f}]/u', "", normalizer_normalize($randomWord, Normalizer::FORM_D)));

        if (strlen($randomWord) <= 3) {
            self::pickRandomWordFromDictionnary();
        }

        return $randomWord;
    }

    public static function sentence(?int $wordsCount = null)
    {
        if ($wordsCount === null) {
            $wordsCount = rand(5, 50);
        }
        $punctuationsList = ["!", ".", "?", ",", ";"];
        $lengthPunctationsList = 4;
        $wordsList = [
            "lorem",
            "ipsum",
            "dolor",
            "sit",
            "amet",
            "consectetur",
            "adipisicing",
            "elit",
            "magni",
            "reiciendis",
            "ullam",
            "sapiente",
            "neque",
            "atquisquam",
            "eos",
            "aut",
            "expedita",
            "excepturi",
            "modi",
            "ad",
            "fugit",
            "sequi",
            "mollitia",
            "fugitmolestias",
            "quo",
            "voluptatemmodi",
            "maiores",
            "facere",
            "iusto",
            "laudantium",
            "minus",
            "provident",
            "alias",
            "dolores",
            "dolorum",
            "seditaque",
            "eaque",
            "doloribus",
            "pariatur",
            "dignissimos",
            "sint",
            "molestiaeodit",
            "enim",
            "ducimus",
            "hic",
            "in",
            "natus",
            "mollitiaillotemporibus",
            "exercitationemab",
            "id",
            "nobis",
            "impedit",
            "voluptates",
            "velit",
            "earum",
            "repellat",
            "consequatur",
            "praesentium",
            "perspiciatis",
            "ipsam",
            "facilis",
            "quas",
            "quia",
            "cum",
            "remmodi",
            "aspernatur",
            "placeat",
            "quis",
            "odit",
            "molestiae",
            "architecto",
            "assumenda",
            "veniam",
            "esse",
            "dolore",
            "magnam",
            "atque",
            "et",
            "vel",
            "animi",
            "nihilcumque",
            "aliquid",
            "fugiatillo",
            "saepe",
            "soluta",
            "distinctio",
            "eosexplicabo",
            "illum",
            "recusandae",
            "illo",
            "deleniti",
            "voluptatum",
            "quidem",
            "ut",
            "eveniet",
            "labore",
            "ratione",
            "exercitationem",
            "tenetur",
            "eligendi",
            "doloremmagnam",
            "tempore",
            "molestias",
            "nemo",
            "officia",
            "vitae",
            "quibusdam",
            "fugiatquaerat",
            "ex",
            "numquam",
            "incidunt",
            "delenitiet",
            "libero",
            "autem",
            "a",
            "suscipit",
            "odio",
            "beatae",
            "aperiam",
            "voluptas",
            "obcaecati",
            "quaerat",
            "praesentiumtempore",
            "accusamus",
            "sequiiure",
            "fugiat",
            "nesciunt",
            "inventore",
            "commodi",
            "possimus",
            "ipsa",
            "laborum",
            "debitis",
            "explicabo",
            "nostrum",
            "voluptatem",
            "ab",
            "iste",
            "tempora",
            "quidembeatae",
            "voluptatibus",
            "nemomolestias",
            "asperiores",
            "delectus",
            "officiis",
            "optio",
            "error",
            "accusamusnostrumfugit",
            "ea",
            "voluptate",
            "nihil",
            "non",
            "aperiameos",
            "culpa",
            "quibusdamperferendis",
            "necessitatibus",
            "laboriosam",
            "omnis",
            "eum",
            "veritatis",
            "essenemo",
            "sed",
            "itaquea",
            "reprehenderit",
            "corrupti",
            "optioexercitationem",
            "repellendus",
            "facilismodi",
            "nulla",
            "unde",
            "cumque",
            "repudiandae",
            "voluptatumvitae",
            "eius",
            "corporis",
            "doloremquae",
            "doloremque",
            "minima",
            "adipisci",
            "accusantium",
            "rem",
            "doloreimpedit",
            "cupiditatepossimus",
            "quisquam",
            "porro",
            "rerum",
            "totam",
            "idharum",
            "perferendis",
            "consequuntur",
            "dolorequis",
            "cupiditate",
            "praesentiumsed",
            "harum",
            "ducimuslaboriosam",
            "illodebitis",
            "hicreprehenderit",
            "quos",
            "dicta",
            "quasi",
            "obcaecatiblanditiis",
            "illumfugiat",
            "corruptiillo",
            "magnidolores",
            "iure",
            "fuga",
            "vero",
            "namaut",
            "dictavoluptatibus",
            "inciduntvelit",
            "similique",
            "numquamminima",
            "nam",
            "quam",
            "qui",
            "namexpedita",
            "iurebeatae",
            "quooptio",
            "aliquam",
            "fugadolorem",
            "itaque",
            "dignissimoscumque",
            "temporibus",
            "quiaccusantium",
            "dolorem",
            "quisquameaquearchitecto",
            "officiisdebitis",
            "repellatrerum",
            "voluptatenatus",
            "blanditiisomnis",
            "saepequia",
            "est",
            "maxime",
            "quidemid",
            "easequi",
            "eaquepossimus",
            "voluptatibusanimi",
            "blanditiis",
            "rempariatur",
            "temporamaxime",
            "earecusandae",
            "explicaboquasnihil",
            "quod",
            "liberoculpa",
            "architectominima",
            "cupiditatequas",
            "officiavero",
            "quae",
            "ipsamunde",
            "maximeeaque",
            "fugitvoluptate",
            "deserunt",
            "fugiatdicta",
            "doloreat",
            "suscipitharum",
            "inciduntnobis",
            "voluptatemsed",
            "cupiditateofficia",
            "aliasiure",
            "possimusdolorem",
            "doloresvoluptatibus",
            "sedminus",
            "quiaeum",
            "repellendustempore",
            "cumrecusandae",
            "solutaquae",
            "sunt",
            "totamplaceat",
            "nisi",
            "quisquamexpedita",
            "nesciuntnobis",
            "pariatursequi",
            "cumqueeaque",
            "magniipsa",
            "reiciendisnecessitatibus",
        ];
        $lengthWordsList = 274;

        $sentence = "";
        $hasPrevPunctation = false;
        $needsUppercase = true;
        for ($i = 0; $i < $wordsCount; $i++) {

            if($i != 0 && $i < $wordsCount){
                $sentence .= " ";
            }

            $randWord = $wordsList[rand(0, $lengthWordsList)];
            if ($needsUppercase) {
                $sentence .= ucfirst($randWord);
                $needsUppercase = false;
            } else {
                $sentence .= $randWord;
            }

            if (rand(0, 5) == 5) {
                $randPunctuation = $punctuationsList[rand(0, $lengthPunctationsList)];
                $hasPrevPunctation = true;

                if (!in_array($randPunctuation, [",", ";"])) {
                    $needsUppercase = true;
                }

                $sentence .= $randPunctuation;
            }

            
        }
        if($hasPrevPunctation)
            $sentence = substr($sentence, 0, -1);
            
        return $sentence . ". \n\r";
    }
}
