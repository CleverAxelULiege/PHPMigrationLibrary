<?php

namespace Seeds\Utilities;

use DateTime;
use Normalizer;

class Random{
    public static function password(int $length = 10){
        $length = floor($length / 2);

        return bin2hex(random_bytes($length));
    }

    public static function email(){
        $extensionList = ["be", "fr", "com", "net", "org", "tv"];
        $domainList = ["mail", "outlook", "gmail", "hotmail", "oof"];
        $randomExtension = $extensionList[rand(0, count($extensionList)-1)];
        $randomDomain = $domainList[rand(0, count($domainList)-1)];
        return self::pickRandomWordFromDictionnary() . (rand(0, 3) == 3 ? "." . self::pickRandomWordFromDictionnary() : "") . "@". $randomDomain . "." . $randomExtension;
    }

    public static function name(){
        return self::pickRandomNameFromDictionnary();
    }

    public static function birthdate(string $format = "Y-m-d"){
        $currentYear = (int)date("Y");
        $year = rand($currentYear - 100, $currentYear);
        $month = rand(1, 12);
        $day = rand(1, 31);
        $date = new DateTime();
        $date->setDate($year, $month, $day);
        return $date->format($format);
    }

    private static function pickRandomNameFromDictionnary(){
        $randomLine = rand(0, 1425);
        $i = 0;
        $randomName = "";
        try{
            $fstream = fopen(__DIR__ . "/dictionnary/name_dictionnary_s.txt", "r");
            while (($randomName = fgets($fstream, 4096)) !== false && $i < $randomLine) {
                $i++;
            }
        }finally{
            fclose($fstream);
        }

        return str_replace(["\r", "\n"], '', $randomName);;
    }

    private static function pickRandomWordFromDictionnary(){
        $randomLine = rand(0, 22730);
        $i = 0;
        $randomWord = "";
        try{
            $fstream = fopen(__DIR__ . "/dictionnary/word_dictionnary_s.txt", "r");
            while (($randomWord = fgets($fstream, 4096)) !== false && $i < $randomLine) {
                $i++;
            }
        }finally{
            fclose($fstream);
        }

        $randomWord = str_replace(["\r", "\n", "-", " "], '', $randomWord);
        $randomWord = strtolower(preg_replace('/[\x{0300}-\x{036f}]/u', "", normalizer_normalize($randomWord, Normalizer::FORM_D)));

        if(strlen($randomWord) <= 3){
            self::pickRandomWordFromDictionnary();
        }

        return $randomWord;
    }
}