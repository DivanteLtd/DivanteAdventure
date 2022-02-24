# POST /api/employees/verifyPin

Checks if PIN is valid.

```
curl --request POST \
  --url 'URL/api/employees/verifyPin' \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"pin": "1234"
  }'
```

### Security requirements
`ROLE_USER`

## Request

```json
{
  "pin": "1234"
}
```

* `pin` - PIN number entered by user

## Result
If PIN is correct, returns empty JSON object, otherwise return object with `message` field containg error description.

```json
{
  "message": "account blocked"
}
```