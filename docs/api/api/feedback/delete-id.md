# DELETE /api/feedback/{id}

Deletes existing feedback.

```
curl --request DELETE \
  --url 'URL/api/feedback/1 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
* `ROLE_USER` or above - user can delete feedback for his current padawan and only if he created this feedback
* `ROLE_TRIBE_MASTER` or above - delete feedback for employee if is from your tribe and has status 2
* `ROLE_SUPER_ADMIN` - delete feedback for anyone

## Result
Empty JSON object.
