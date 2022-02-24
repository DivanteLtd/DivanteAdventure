# DELETE /api/projects/{projectId}/criterium/{criteriumId}

Deletes criterium from project.

```
curl --request DELETE \
  --url 'URL/api/projects/1/criterium/20 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Result
Empty JSON object.