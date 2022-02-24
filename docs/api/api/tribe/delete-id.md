# DELETE /api/tribe/{id}

Deletes existing tribe.

```
curl --request DELETE \
  --url 'URL/api/tribe/1 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_TRIBE_MASTER` role or above.

## Result
Empty JSON object.