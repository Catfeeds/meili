@echo off
echo 数据库正在还原...
rem 解压缩命令 解压成 .sql文件
expand D:\wamp\www\base.toocms-project.com\Data\data\{{file}}.sql.gz D:\wamp\www\base.toocms-project.com\Data\data\{{file}}.sql

rem 账号密码端口 写在my.ini里
D:\wamp\bin\mysql\mysql5.6.12\bin\mysql base < D:\wamp\www\base.toocms-project.com\Data\data\{{file}}.sql

rem 删除.sql文件
del/f/s/q D:\wamp\www\base.toocms-project.com\Data\data\{{file}}.sql
@echo on