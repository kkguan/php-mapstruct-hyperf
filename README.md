# php-mapstruct-hyperf


## 使用教程


> 执行 php bin/hyperf.php vendor:publish kkguan/php-mapstruct-hyperf 将配置文件发布到项目内

```php
return [
    // 如果设置为 true，则会将一些重要的生成信息打印出来
    'verbose' => true,
    // mapstruct 代码生成目录
    'generated_dir' => BASE_PATH . '/runtime/mapstruct',
    // TODO: 生成过的不再生成
    'enable_cache' => false,
];
```

## 使用文档

https://mapstruct.org/documentation/stable/reference/html/


## 当前版本支持功能

- 通过 #[HyperfMapper] 注解自动生成对象转换的映射关系
- 通过 #[Mpaping(target: ‘’, source: ‘’)] 注解定义对象转换的目标字段与源头字段
- 通过 #[Mpaping(ignore:true)] 来忽略某些不需要转换的字段
