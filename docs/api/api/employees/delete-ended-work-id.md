# DELETE /api/employees/endedWork/{id}

Deletes end cooperation entry by it's ID.

```
curl --request DELETE \
  --url 'URL/api/employees/endedWork/5 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_HR` role or higher.

## Result

Empty JSON object.
