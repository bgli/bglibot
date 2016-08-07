# Bot API menggunaakan PHP

Berikut ini adalah kode sumber BGLI _bot_ yang dibuat dengan bahasa pemrograman `PHP`.
Adapun `telebot.php` merupakan `class` yang dibuat untuk mempermudah mengkomunikasikan antara sumber kode dengan API dari `Telegram`.

# Cara menggunakan

Gunakan PHP CLI untuk menjalankan _bot_ ini.

## Untuk Long Polling

```
php longpolling.php

```

## Untuk Web Hooks

Pertama-tama set dahulu webhooknya.
```
php set_webhooks.php
```

Kemudian buat kode untuk memanggilnya.
