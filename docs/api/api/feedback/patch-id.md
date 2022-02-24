# PATCH /api/feedback/{id}

Updates feedback data.

```
curl --request PATCH \
  --url 'URL/api/feedback/15\
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
    "feedback": "John did a very great job"
  }'
```

### Security requirements
* `ROLE_USER` or above - updating feedback for his current padawan
* `ROLE_TRIBE_MASTER` or above - updating feedback for any member of same tribe/department
* `ROLE_SUPER_ADMIN` - updating feedback for anyone

## Request
JSON object containing following fields:
```json
{
  "feedback": "John did a very great job"
}
```
* `feedback` - Feedback text

## Result
Empty JSON object.
