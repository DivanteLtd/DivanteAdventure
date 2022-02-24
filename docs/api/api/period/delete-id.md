# DELETE /api/period/{id}

Deletes period by ID.

```
curl --request DELETE \
  --url 'URL/api/period/150 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_SUPER_ADMIN` role.

## Result
Empty JSON object.