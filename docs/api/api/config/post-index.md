# POST /api/config

Updates config entries.

```
curl --request POST \
  --url 'URL/api/config \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
    "entries": {
	   "foo.bar_baz": "New value"
    }
  }'
```

### Security requirements
`ROLE_SUPER_ADMIN` role.

## Request
Object containing configuration in key-value pairs. If key already exist and it's
value is different than sent, than it will be replaced and correct historical entry will
be created.

```json
{
  "entries": {
    "foo.bar_baz": "New value"
  }
}
```

## Result
Empty JSON object.