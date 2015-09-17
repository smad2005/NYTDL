[![Build status](https://ci.appveyor.com/api/projects/status/mek1qv1la1de3411/branch/master?svg=true)](https://ci.appveyor.com/project/smad2005/nytdl/branch/master) [![Code Climate](https://codeclimate.com/github/smad2005/NYTDL/badges/gpa.svg)](https://codeclimate.com/github/smad2005/NYTDL) 
[![Coverage Status](https://coveralls.io/repos/smad2005/NYTDL/badge.svg?branch=master&service=github)](https://coveralls.io/github/smad2005/NYTDL?branch=master)
# NYTDL
NyaaTorrents Torrent Downloader (Автор vknkk)

Данный PHP-скрипт предназначен для автоматизации поиска и загрузки аниме с сайта nyaa.se по **всем** имеющимся в папке субтитрам . 

## Требования
Windows Xp+, uTorrent

# Инструкция:

## 1 способ:
 - Скачать из Release архив
 - Запустите AddToSendTo.bat
 - Кликните правой кнопкой мыши по субтитрам, пункт отправить, подпункт NYTDL

## 2 способ через php (необходим php 5.6):
- Редактируем searchload.bat и указываем путь к php.exe
- В config.json указан путь к uTorrent.exe
- Кидаем в проводнике на searchload.bat субтитры (субтитры нужны только для указания каталога в котором будет произведен поиск всех остальных субтитров с расширением с  ass|ssa|srt 

## 3 способ:
 - (опционально) Компилируем searchload.exe 
   
```
bamcompile.exe -i:icon.ico searchload.php
```
> http://www.bambalam.se/bamcompile

- В config.json указан путь к uTorrent.exe
- Кидаем в проводнике на searchload.exe субтитры (субтитры нужны только для указания каталога в котором будет произведен поиск всех остальных субтитров с расширением с  ass|ssa|srt


## Регистрация в контексном меню "Отправить"
 -Запустите AddToSendTo.bat

## Удаление из контексного меню "Отправить"
- Win+R
- Введите
```
shell:sendto
```
- Удалите ярлык NYTDL

---
# Обсуждение:

http://fansubs.ru/forum/viewtopic.php?p=672783#672783

