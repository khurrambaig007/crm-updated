---
paths:
  - 'app/Http/Requests/**/*.php'
---

# Requests

## Blank rows in repeatable inputs must be stripped in prepareForValidation
Repeatable "Add Email"/"Add Field" inputs always submit a spare blank row (a seeded first row on a new record, or an unused row the user opened and left alone). Validating it directly makes the form unsavable with "The emails.1 field is required" for an input the user has no reason to fill in. Strip blank rows in `prepareForValidation()` before `rules()` runs so the array-level `required|min:1` enforces "at least one real value". See SaveCompanyProfileRequest.
