# PATCH /api/period/{id}

Updates existing leave period.

```
curl --request PATCH \
  --url 'URL/api/period/50 \
  --header 'authorization: Bearer TOKEN' \
  --header 'content-type: application/json' \
  --data '{
	"dateFrom": "2020-01-01",
    "dateTo": "2020-12-31",
    "employeeId": 150
  }'
```

### Security requirements
`ROLE_SUPER_ADMIN` role.

## Request
JSON containing required data:

```json
{
  "employeeId": 150,
  "dateFrom": "2020-01-01",
  "dateTo": "2020-12-31",
  "freeDays": 20,
  "sickLeaveDays": 14,
}
```

* `employeeId` - owner's ID
* `dateFrom` - starting date in YYYY-MM-DD format
* `dateTo` - ending date in YYYY-MM-DD format
* `freeDays` - count of all available free days in period
* `sickLeaveDays` - count of all avaiable sick leave days in period


## Result
Empty JSON object.