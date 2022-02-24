# POST /api/projects/{id}criterium

Adds new criterium to project.

```
curl --request POST \
  --url 'URL/api/projects/15/criterium\
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"criterionId": 30
  }'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Request
JSON object containing `criterionId` field with ID of created criterion.
```json
{
  "criterionId": 30
}
```

## Result
Empty JSON object.
