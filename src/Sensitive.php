<?php
namespace PhpSensitive\SensitiveFilter;

//敏感词过滤类DFA算法
class Sensitive{
    use \PhpSensitive\SensitiveFilter\SensitiveTrait;
    private function __construct(string $file_path){
        try {
            if(file_exists($file_path)){
                self::addSensitiveWords($file_path);
            }
        } catch (\Exception $e){
            //error_log($e->getMessage());
            throw $e;
        }

    }

    /*
    获取实例
    */
    public static function getInstance(string $file_path){
        if(self::$instance instanceof Sensitive){
            return self::$instance;
        }
        //实例化本类赋值到私有变量
        return self::$instance = new self($file_path);
    }

    /*
    执行过滤敏感词
    @param string $phrase
    @return string
    */
    public static function execFilter(string $phrase,$matchOne = false): string {
        //过滤敏感词
        $wordList = self::searchWords($phrase,$matchOne);
        if (empty($wordList))
            return $phrase;
        return strtr($phrase,$wordList);
    }

}