# DELETE /api/employees/{id}

Deletes employee by given ID.

```
curl --request DELETE \
  --url 'URL/api/employees/200 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_SUPER_ADMIN`

## Result

Empty JSON object.
