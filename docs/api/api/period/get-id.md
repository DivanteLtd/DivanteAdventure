# GET /api/period/{id}

Returns list of employee's leave periods. Employee is identified by ID supplied in URL.

```
curl --request GET \
  --url 'URL/api/period/150 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Result
JSON array containing period objects:

```json
[
  {
    "id": 15,
    "employee": { ... },
    "dateFrom": "2020-01-01",
    "dateTo": "2020-12-31",
    "freeDays": 20,
    "sickLeaveDays": 14,
    "requests": [ ... ],
  },
  ...
]
```

* `id` - period's ID
* `employee` - mapped object represting owner of that period
* `dateFrom` - starting date in YYYY-MM-DD format
* `dateTo` - ending date in YYYY-MM-DD format
* `freeDays` - count of all available free days in period, including used
* `sickLeaveDays` - count of all avaiable sick leave days in period, including used
* `requests` - array of Request objects.
