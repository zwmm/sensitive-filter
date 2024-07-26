# sensitive-filter
DFA sensitive word filter

## 使用方法
```angular2html
composer require php-sensitive/sensitive-filter
```

---
```
include_once "./vendor/autoload.php";

#use \PhpSensitive\SensitiveFilter\Sensitive;

$filePath = __DIR__."/word.txt";
$instance =Sensitive::getInstance($filePath);

//仅匹配一个敏感词 true
$phrase = "你傻瓜,傻蛋,傻瓜子";
echo $instance::execFilter($phrase,true);
```
---
#### 添加敏感词，组成树结构
***
```
[
  [傻]=>[
    [子]=>[
      [是]=>[
        [傻]=>[
          [帽]=>[false]
        ]
      ]
    ],
    [蛋]=>[false]
  ],
  [白]=>[
    [痴]=>[false]
  ]
]
```
***
