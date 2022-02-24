# PATCH /api/employees/endedWork/{id}

Creates new ending cooperation entity by it's ID.

```
curl --request PATCH \
  --url 'URL/api/employees/endedWork/50' \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
    "comment": "Text comment",
  }'
```

### Security requirements
`ROLE_HR` role or above.

## Request

JSON object:
```json
{
  "employeeId": 200,
  "nextCompany": "Company Name",
  "whoEndedCooperation": "Employee",
  "exitInterview": true,
  "checklist": false,
  "comment": "Text comment",
  "dismiss": "2019-12-31"
}
```

* `employeeId` - Employee's ID
* `nextCompany` - name of Employee's new employer
* `whoEndedCooperation` - either "Employee" or "Company"
* `exitInterview` - `true` if exit interview was held
* `checklist` - `true` if leave checklist was done
* `comment` - HR comment
* `dismiss` - date in YYYY-MM-DD format.

## Result

Empty JSON object.
