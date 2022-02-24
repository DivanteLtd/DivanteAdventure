# DELETE /api/news/{id}

Removes existing news

```
curl --request DELETE \
  --url 'URL/api/news/5 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_HR` role or above.

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