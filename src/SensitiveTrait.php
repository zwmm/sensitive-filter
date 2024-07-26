<?php
namespace PhpSensitive\SensitiveFilter;

//敏感词过滤类DFA算法
trait SensitiveTrait{
    private static $instance = null;
    /*
    替换标志、符号
    @variable string
    */
    private static $replaceSymbol = "*";
    /*
    敏感词树
    @variable array
    */
    private static $sensitiveWordTree = [];

    /*
	获取实例
	*/
    /*public static function getInstance(string $file_path){
        if(self::$instance instanceof Sensitive){
            return self::$instance;
        }
        //实例化本类赋值到私有变量
        return self::$instance = new self($file_path);
    }*/

    /*
    @param $file_path 敏感词库文件的路径
    */
    private static function addSensitiveWords(string $file_path): void {
        foreach (self::readFile($file_path) as $words) {
            //mb_strlen 以UTF-8计算字符串长度
            $len = mb_strlen($words,'UTF-8');
            //把敏感词树复制到新的变量中
            // 1$treeArr = &self::$sensitiveWordTree;

            for ($i=0;$i<$len;$i++) {
                $word = mb_substr($words,$i,1,'UTF-8');
                //敏感词树结尾 记录状态为false
                // 2$treeArr = &$treeArr[$word]??$treeArr = false;
                    self::$sensitiveWordTree[$word]??self::$sensitiveWordTree[$word] = false;
            }
        }
    }

    /*
	读取文件内容
	@param string $file_path
	@return Generator
	*/
    private static function readfile(string $file_path): \Generator {
        // return [
        // 	'白' => [
        // 		'痴' => false
        // 	]
        // ];
        $handle = fopen($file_path, 'r');
        //判断文件是否已达到末尾
        while (!feof($handle)) {
            //fgets从打开文件中返回一行，会在到达指定长度(length-1)、碰到换行、读到EOF，停止返回一个新行
            yield trim(fgets($handle));
        }
        fclose($handle);

    }

    /*
    执行过滤敏感词
    @param string $phrase
    @return string
    */
    /*public static function execFilter(string $phrase,$matchOne = false): string {
        //过滤敏感词
        $wordList = self::searchWords($phrase,$matchOne);
        if (empty($wordList))
            return $phrase;
        return strtr($phrase,$wordList);
    }*/

    /*
    搜索敏感词
    @param string $phrase
    @return array
    $matchOne true 匹配一个字符
    */

    private static function searchWords(string $phrase,$matchOne=false): array {
        $phraseLength = mb_strlen($phrase,'UTF-8');
        $wordList = [];
        for ($i=0;$i<$phraseLength;$i++) {
            //检查字符是否存在敏感词树内，传入检查文本，搜索开始位置，文本长度
            $len = self::checkWordTree($phrase,$i,$phraseLength);
            //存在敏感词，进行字符替换。
            if ($len > 0) {
                //搜索出来的敏感词
                $word = mb_substr($phrase,$i,$len,'UTF-8');
                $wordList[$word] = str_repeat(self::$replaceSymbol, $len);
                if ($matchOne) {
                    break;
                }
                $i+=$len-1;
            }
        }
        return $wordList;
    }

    /*
    检查敏感词树是否合法
    @param string $phrase 检查文本
    @param int $index 搜索文本位置索引
    @param int $txtLength 文本长度
    @return int 返回不合法字符个数
    */
    private static function checkWordTree(string $phrase,int $index,int $phraseLength): int {
        $treeArr = &self::$sensitiveWordTree;
        $wordLength = 0;//敏感字符个数
        $flag = false;
        for ($i=$index;$i<$phraseLength;$i++) {
            $phraseWord = mb_substr($phrase,$i,1,'UTF-8');//截取需要检测的文本，和词库进行比对
            //如果搜索字符不存在词库直接停止循环。
            if(!isset($treeArr[$phraseWord])){
                break;
            }
            if ($treeArr[$phraseWord] !== false) {
                $treeArr = &$treeArr[$phraseWord];//继续搜索下一层tree
            }else{
                $flag = true;
            }
            $wordLength++;
        }
        //没有检测到敏感词，初始化字符长度
        $flag ?: $wordLength = 0;
        return $wordLength;
    }
    //防止克隆
    private function __clone() {
        throw new \Exception("clone instance failed!");
    }
    //反序列化操作 unserialize是调用__wakeup
    private function __wakeup() {
        throw new \Exception("UNSERIALIZE INSTANCE FAILED!");
    }
}