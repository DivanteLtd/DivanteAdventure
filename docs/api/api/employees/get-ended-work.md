# GET /api/employees/endedWork

Returns data about employees that have already ended cooperation with company or are planning to do so.

```
curl --request GET \
  --url 'URL/api/employees/endedWork \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_HR` role or above.

## Result

JSON array containing object entries:

```json
[
  {
    "id": 1,
    "employee_id": 200,
    "name": "John",
    "last_name": "Smith",
    "contract": "CoE",
    "next_company": "Other Company",
    "who_ended_cooperation": "Employee",
    "exit_interview": "1",
    "checklist": "0",
    "comment": null,
    "dismissDate": "2019-12-31",
    "email": "jsmith@example.com"
  },
  ...
]
```

* `id` - entry ID
* `employee_id` - Employee's ID
* `name` - Employee's name
* `last_name` - Employee's last name
* `contract` - Employee's contract ("CoE", "CLC LUMP SUM", "CLC HOURLY", "B2B LUMP SUM" or "B2B HOURLY")
* `next_company` - name of Employee's new employer
* `who_ended_cooperation` - either "Employee" or "Company"
* `exit_interview` - boolean marking if exit interview was held
* `checklist` - boolean marking if leave checklist was done
* `comment` - HR's comment
* `dismissDate` - dismissal date in YYYY-MM-DD format
* `email` - Employee's email
