# GET /api/period/report

Returns free days report.

```
curl --request GET \
  --url 'URL/api/period/report \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_SUPER_ADMIN` role.

## Result
JSON array containing report objects:

```json
[
  {
    "employeeName": "John Smith",
    "contractName": "CoE",
    "periodFrom": "2020-01-01",
    "periodTo": "2020-12-31",
    "freedaysOwed": 24,
    "freeDaysPaidUsed": 12,
    "freeDaysUnpaidUsed": 10,
    "freeDaysRequest": 2,
    "freeDaysOccasional": 3,
    "freeDaysCare": 1,
    "sickLeaveDaysUsed": 10
  },
  ...
]
```
