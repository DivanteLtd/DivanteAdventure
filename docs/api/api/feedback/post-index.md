# POST /api/feedback

Assign new feedback to employee.

```
curl --request POST \
  --url 'URL/api/feedback \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
    "employee": 123,
    "feedback": "John did a great job",
    "type": 1
  }'
```

### Security requirements
* `ROLE_USER` or above - crating feedback for his current padawan
* `ROLE_TRIBE_MASTER` or above - crating feedback for any member of same tribe/department
* `ROLE_SUPER_ADMIN` - crating feedback for anyone

## Request
JSON object containing three fields:

```json
{
  "employee": 123,
  "feedback": "John did a great job",
  "type": 1
}
``` 
* `employee` - Employee's ID
* `feedback` - Feedback text
* `type` - 1 if technical feedback, 2 if feedback from tribe master

## Result
Empty JSON object.
