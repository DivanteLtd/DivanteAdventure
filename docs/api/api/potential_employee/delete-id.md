# DELETE /api/potential_employee/{id}

Deletes potential employee.

```
curl --request DELETE \
  --url 'URL/api/potential_employee/15 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_HR` role or above.

## Result
Empty JSON object.
