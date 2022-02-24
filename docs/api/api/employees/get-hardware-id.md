# GET /api/employees/hardware/{id}

Return list of hardware assignments for employee with ID `{id}`, synchronized from Snipe IT.

```
curl --request GET \
  --url 'URL/api/employees/hardware/200 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result

JSON array containing Hardware objects.

Sample result:

```json
[
  {
    "category": "Laptop",
    "manufacturer": "Dell",
    "model": "Latitude E5450",
    "serialNumber": "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
  },
  ...
]
```

* `category` - hardware category in SnipeIT
* `manufacturer` - hardware brand
* `model` - hardware model
* `serialNumber` - hardware serial number