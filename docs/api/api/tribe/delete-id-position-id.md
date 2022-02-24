# DELETE /api/tribe/{tribeId}/position/{positionId}

Detaches position with given ID from selected tribe.

```
curl --request DELETE \
  --url 'URL/api/tribe/1/position/10 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_TRIBE_MASTER` role or above.

## Result
Empty JSON object.