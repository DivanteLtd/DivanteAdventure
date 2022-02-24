# POST /api/tribe/{tribeId}/position/{positionId}

Attaches position with given ID to selected tribe.

```
curl --request POST \
  --url 'URL/api/tribe/1/position/10 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_TRIBE_MASTER` role or above.

## Result
Empty JSON object.