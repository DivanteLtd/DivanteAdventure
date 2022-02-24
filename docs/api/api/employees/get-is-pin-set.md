# GET /api/employees/isPinSet

Checks if current user has set PIN.

```
curl --request GET \
  --url 'URL/api/employees/isPinSet' \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above

### Result
JSON object containing one key `hasSetPin` with value of either `true` or `false`.

```json
{
  "hasSetPin": true
}
```
