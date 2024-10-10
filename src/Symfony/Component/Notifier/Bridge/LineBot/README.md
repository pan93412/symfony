LINE Bot Bridge
=============

Provides [LINE Bot (Messaging API)](https://developers.line.biz/en/docs/messaging-api/sending-messages/) integration for Symfony Notifier.

DSN example
-----------

```
linebot://TOKEN@default?receiver=RECEIVER
```

`RECEIVER` can be retrieved from <https://developers.line.biz/en/docs/messaging-api/getting-user-ids/#getting-user-ids>.

`TOKEN` should be encoded in URL format. Encode it with this command:

```
php -r 'echo urlencode("your+token///");'
```

Resources
---------

 * [Contributing](https://symfony.com/doc/current/contributing/index.html)
 * [Report issues](https://github.com/symfony/symfony/issues) and
   [send Pull Requests](https://github.com/symfony/symfony/pulls)
   in the [main Symfony repository](https://github.com/symfony/symfony)
