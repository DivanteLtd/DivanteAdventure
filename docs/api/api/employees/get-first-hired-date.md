# GET /api/employees/firstHiredDate

Returns date when first employee was hired.

```
curl --request GET \
  --url 'URL/api/employees/firstHiredDate \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result

JSON array containing one object with key `hiredAt` with date in `YYYY-MM-DD` format.

Sample result:

```json
[
  {
    "hiredAt": "2008-07-31"
  }
]
```
