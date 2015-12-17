### Future changes
 
- Empty (null) values will be deleted from database.
- Check if there is any way to reduce the number of updates when saving. It is currently performing an update query every saved field.
- Optimize property savings. Currently, every property value created or updated will execute a SQL query. This could be optimized creating an array of inserts/updates?
- Could reduce number of queries by caching the ChoiceProperty checks. As an idea, they could be cached every time a new choice is added, this way it will never execute any query when selecting.
- Could also cache the properties related to entity, this will reduce a lot the queris as it is something unlikely to change.

### Caution

- Do not use with eager loading. It already uses it internally. Adding it manually to the entity might cause unexpected behaviour.

### Docs

- Include an example of caching the $modelAttributes variable.