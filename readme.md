# NYTDL
NyaaTorrents Torrent Downloader (Автор vknkk)

Данный PHP-скрипт предназначен для автоматизации поиска и загрузки аниме с сайта nyaa.se по **всем** имеющимся в папке субтитрам . 

# Инструкция:

## 1 способ (необходим php 5.6):
- Редактируем searchload.bat и указываем путь к php.exe
- В config.json указан путь к uTorrent.exe
- Кидаем в проводнике на searchload.bat субтитры (субтитры нужны только для указания каталога в котором будет произведен поиск всех остальных субтитров с расширением с  ass|ssa|srt 

## 2 способ:
 - (опционально) Компилируем searchload.exe 
   
```
bamcompile.exe searchload.php 
```
>> http://www.bambalam.se/bamcompile

- В config.json указан путь к uTorrent.exe
- Кидаем в проводнике на searchload.exe субтитры (субтитры нужны только для указания каталога в котором будет произведен поиск всех остальных субтитров с расширением с  ass|ssa|srt

---
# Обсуждение:

http://fansubs.ru/forum/viewtopic.php?p=672783#672783

