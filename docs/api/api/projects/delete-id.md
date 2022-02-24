# DELETE /api/projects/{id}

Deletes existing project.

```
curl --request DELETE \
  --url 'URL/api/projects/1 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Result
Empty JSON object.