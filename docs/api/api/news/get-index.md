# GET /api/news

Returns list of all news.

```
curl --request GET \
  --url 'URL/api/news \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result
JSON array containing news objects:

```json
[
  {
    "id": 15,
    "type": 0,
    "title": "Hello!",
    "banner": "http://image.tld/image.png",
    "desc": "Hello!"
  },
  ...
]
```

* `id` - news' ID
* `type` - `0` for Markdown, `1` for HTML
* `title` - news' title
* `banner` - URL to news' banner image
* `desc` - if type is Markdown, `desc` contains markdown string with news content.