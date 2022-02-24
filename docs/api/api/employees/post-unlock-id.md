# POST /api/employees/unlock/{id}

Unlocks locked employee by given ID.

```
curl --request POST \
  --url 'URL/api/employees/unlock/200' \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_SUPER_ADMIN`

## Result
Empty JSON object.
