# Field Specifications

## General Elements

### __*Slots.subject_code*__

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Field Name            | subject_code                      |
| Parent Table          | Slots                             |
| Alias(es)             |                                   |
| Specification Type    | [ ] Unique                        |
|                       | [ ] Generic                       |
|                       | [X] Replica                       |
|                       |                                   |
| Source Specification  | Foreign key from the Classes table|
| Shared By             |                                   |
| Description           | Code representing the subject being tutored|


## Physical Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Data Type             | Char                              |
| Length                | 3                                 |
| Decimal Places        | N/A                               |
| Character Support     | [ ] Letters (A-Z)                 |
|                       | [X] Numbers (0-9)                 |
|                       | [ ] Keyboard (.,/$#%)             |
|                       | [ ] Special (©®™Σπ)               |


## Logical Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Key Type              | [ ] Non                           |
|                       | [ ] Primary                       |   
|                       | [X] Foreign                       |
|                       | [ ] Alternate                     |
|                       |                                   |
| Key Structure         | [ ] Simple                        |
|                       | [ ] Composite                     |
|                       |                                   |
| Uniqueness            | [X] Non-unique                    |
|                       | [ ] Unique                        |
|                       |                                   |
| Null Support          | [ ] Nulls OK                      |
|                       | [X] No nulls                      |
|                       |                                   |
| Values Entered By     | [X] User                          |
|                       | [ ] System                        |
|                       |                                   |
| Required Value        | [ ] No                            |
|                       | [X] Yes                           |
|                       |                                   |
| Range of Values       |                                   |
| Edit Rule             | [ ] Enter now, edits allowed      |
|                       | [X] Enter now, edits not allowed  |
|                       | [ ] Enter later, edits allowed    |
|                       | [ ] Enter later, edits not allowed|
|                       | [ ] Not determined at this time   |
