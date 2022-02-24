# POST /api/employees/endedWork

Creates new ending cooperation entity.

```
curl --request POST \
  --url 'URL/api/employees/endedWork' \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"email": "jsmith@example.com",
    "nextCompany": "Company Name",
    "whoEndedCooperation": "Employee",
    "exitInterview": true,
    "checklist": false,
    "comment": "Text comment",
    "dismiss": "2019-12-31"
  }'
```

### Security requirements
`ROLE_HR` role or above.

## Request

JSON object:
```json
{
  "email": "jsmith@example.com",
  "nextCompany": "Company Name",
  "whoEndedCooperation": "Employee",
  "exitInterview": true,
  "checklist": false,
  "comment": "Text comment",
  "dismiss": "2019-12-31"
}
```

* `email` - Employee's email
* `nextCompany` - name of Employee's new employer
* `whoEndedCooperation` - either "Employee" or "Company"
* `exitInterview` - `true` if exit interview was held
* `checklist` - `true` if leave checklist was done
* `comment` - HR comment
* `dismiss` - date in YYYY-MM-DD format.

## Result

Empty JSON object.
