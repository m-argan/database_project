# Field Specifications

## General Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Field Name            | time_id                           |
| Parent Table          | Times                             |
| Alias(es)             |                                   |
| Specification Type    | [X] Unique                        |
|                       | [ ] Generic                       |
|                       | [ ] Replica                       |
|                       |                                   |
| Source Specification  |                                   |
| Shared By             | Times, Schedules                  |
| Description           | Primary key for Times entity.     |
|                       | Used to identify a single time    |
|                       | slot.                             |              



## Physical Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Data Type             | INT                               |
| Length                |                                   |
| Decimal Places        |                                   |
| Character Support     | [ ] Letters (A-Z)                 |
|                       | [X] Numbers (0-9)                 |
|                       | [ ] Keyboard (.,/$#%)             |
|                       | [ ] Special (©®™Σπ)               |


## Logical Elements

| Field                 | Value                             |
|-----------------------|-----------------------------------|
| Key Type              | [ ] Non                           |
|                       | [X] Primary                       |   
|                       | [ ] Foreign                       |
|                       | [ ] Alternate                     |
|                       |                                   |
| Key Structure         | [X] Simple                        |
|                       | [ ] Composite                     |
|                       |                                   |
| Uniqueness            | [ ] Non-unique                    |
|                       | [X] Unique                        |
|                       |                                   |
| Null Support          | [ ] Nulls OK                      |
|                       | [X] No nulls                      |
|                       |                                   |
| Values Entered By     | [ ] User                          |
|                       | [X] System                        |
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

## Notes