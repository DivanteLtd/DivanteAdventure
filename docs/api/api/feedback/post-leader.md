# POST /api/feedback/leader

Assign new leader to employee.

```
curl --request POST \
  --url 'URL/api/feedback/leader \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"leaderId": 150,
    "padawanId": 37
  }'
```

### Security requirements
* `ROLE_USER` or above - setting leader for user
* `ROLE_TRIBE_MASTER` or above - setting leader for any member of same tribe/department as user
* `ROLE_SUPER_ADMIN` - setting leader for anyone

## Request
JSON object containing two fields:

```json
{
  "padawanId": 37,
  "leaderId": 150
}
``` 

* `padawanId` - ID of padawan, required.
* `leaderId` - ID of leader. If null, padawan with given ID will not have any leader.

## Result
Empty JSON object.
