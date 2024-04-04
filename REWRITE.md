# Rewrite

## Goals

- Should be able to get information on each comic WITHOUT requiring a fetch.
- Should use PSR-17 clients, not straight stream access.
  - Should _require_ an implementation, but not _depend_ on a specific implementation.
  - Tests should use a curl or stream implementation (when written).
- Use immutable structs wherever possible.
