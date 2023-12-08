<?php

namespace Seeds\Utilities;

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