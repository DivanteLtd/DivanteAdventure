# GET /api/config

Returns current system configuration.

```
curl --request GET \
  --url 'URL/api/config \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_SUPER_ADMIN` role.

## Result
JSON array containing config entries.

```json
[
  {
    "key": "foo.bar_baz",
    "group": "foo",
    "name": "bar_baz",
    "value": "config value",
    "createdAt": 1234567890,
    "responsible": {
      "id": 15,
      "name": "John",
      "lastName": "Smith",
      "photo": "http://example.tld/photo.png"
    }
  },
  ...
]
```
