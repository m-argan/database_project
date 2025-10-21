# Field Specifications

## General Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Field Name            | subject_code                      |
| Parent Table          | Classes                           |
| Alias(es)             |                                   |
| Specification Type    | [X] Unique                        |
|                       | [ ] Generic                       |
|                       | [X] Replica                       |
|                       |                                   |
| Source Specification  | Subjects.subject_code             |
| Shared By             | Subjects                          |
| Description           | Represents the unique three-character subject code for a certain subject, which acts as the prefix for a course code. |

## Physical Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Data Type             | CHAR                              |
| Length                | 3                                 |
| Decimal Places        |                                   |
| Character Support     | [X] Letters (A-Z)                 |
|                       | [ ] Numbers (0-9)                 |
|                       | [ ] Keyboard (.,/$#%)             |
|                       | [ ] Special (©®™Σπ)               |


## Logical Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Key Type              | [ ] Non                           |
|                       | [X] Primary                       |   
|                       | [X] Foreign                       |
|                       | [ ] Alternate                     |
|                       |                                   |
| Key Structure         | [ ] Simple                        |
|                       | [X] Composite                     |
|                       |                                   |
| Uniqueness            | [X] Non-unique (i.e., multiple records in Classes can pertain to the same subject_code)                   |
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
| Range of Values       | Any permutation of letters A-Z in a 3-length character array.                                  |
| Edit Rule             | [ ] Enter now, edits allowed      |
|                       | [X] Enter now, edits not allowed  |
|                       | [ ] Enter later, edits allowed    |
|                       | [ ] Enter later, edits not allowed|
|                       | [ ] Not determined at this time   |

## Notes