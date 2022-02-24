# DELETE /api/employees/workLocation/{id}
Deletes work location by ID.

```
curl --request GET \
  --url 'URL/api/employees/workLocation/5 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_USER` role or above.

## Result
Empty JSON object.