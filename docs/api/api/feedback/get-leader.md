# GET /api/feedback/leader

Returns object representing current user's leader and array containg objects representing
all of current user's padawans.

```
curl --request GET \
  --url 'URL/api/feedback/leader \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result
JSON object:

```json
{
  "leader": {
    "id": 150,
    "name": "John",
    "lastName": "Smith",
    "photo": "http://page.tld/image.png"
  },
  "padawans": [
    {
      "id": 213,
      "name": "Janine",
      "lastName": "Doe",
      "photo": "http://page.tld/image.png"
    },
    ...
  ]
}
```

