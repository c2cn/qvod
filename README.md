环境说明
-----
* SAE: SaeFetchurl + SaeStorage
* PHP: curl + textfile

列表结构
-----
```
    list
    ┡ _newest
    ┡ imdb_top250
    ┡ douban_top250
    ┡ mtime_top100

    custom
    ┡ 科幻系列
    ┡ 结局反转系列
    ┡ ...
``` 

列表说明
-----
* 如使用SAE环境，SaeStorage不支持自动创建域，需手动创建list和custom域
* list通过cron.php自动更新
* custom为自定义列表：第一行标题，每行一个电影名

     



v3.1
-----
* 增加非SAE环境支持
* 优化列表管理

v3.0
-----
* bootstrap3.0
* data全json处理
* view全丢给前端
* 增加cron.php, 定时更新top list/new movie

