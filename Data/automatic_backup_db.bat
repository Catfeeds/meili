@echo off
rem 自动备份
echo 数据库正在备份...
rem 日期设置
rem set Ymd=%date:~,4%%date:~5,2%%date:~8,2%
rem 时间设置  注：当“小时”小于10的时候 获取的“小时”为 空格+小时数 可以用%time:~1,1%获取去掉空格的字符 也可用下面的方式 在前面加上 0
rem set his=%time:~0,2%%time:~3,2%%time:~6,2%

rem 设置文件名称 以时间来命名 小于0的小时数前面加上0的方法
set h=%time:~0,2%
set h=%h: =0%
set Ymdhis=%date:~0,4%%date:~5,2%%date:~8,2%-%h%%time:~3,2%%time:~6,2%

rem 账号密码端口 写在my.ini里 [mysqldump下边]
D:\wamp\bin\mysql\mysql5.6.12\bin\mysqldump --default-character-set=utf8 --opt --extended-insert=false -R --hex-blob -x -q wash > D:\wamp\www\wash.toocms-project.com\Data\data\%Ymdhis%.sql
rem 压缩命令 执行压缩
makecab D:\wamp\www\wash.toocms-project.com\Data\data\%Ymdhis%.sql D:\wamp\www\wash.toocms-project.com\Data\data\%Ymdhis%.sql.gz
rem 删除原.sql文件
del/f/s/q D:\wamp\www\wash.toocms-project.com\Data\data\%Ymdhis%.sql
@echo on