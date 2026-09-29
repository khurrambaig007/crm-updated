---
paths:
  - bootstrap/app.php
---

# Bootstrap

## Auth-adjacent middleware needs the 'authenticated' group, never an 'auth' alias/group
Middleware that must run right after Authenticate is registered via `$middleware->appendToGroup('authenticated', [Authenticate::class, ...])`, and routes/web.php uses `middleware('authenticated')`. Two traps: (1) `appendToGroup('auth', ...)` creates a group that shadows the built-in `auth` ALIAS, because MiddlewareNameResolver checks the group map (line 35) before the alias map (line 44) - Authenticate would never run and every route would be publicly reachable. (2) `alias(['auth' => [ ... ]])` throws "Array to string conversion": the alias map is only read at line 44 via string concatenation, so array values are unsupported despite the resolver's `@return \Closure|string|array` docblock. Only Closure aliases short-circuit (line 28).
