# GET /api/projects/sendEmail/{id}

Sends e-mail to all employees currently working in project with list of types of data processed in project.

```
curl --request GET \
  --url 'URL/api/projects/sendEmail/15 \
  --header 'authorization: Bearer TOKEN'
```

### Security requirements
`ROLE_MANAGER` role or above.

## Result
Empty JSON object.
