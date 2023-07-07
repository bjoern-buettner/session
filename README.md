# session

A memcached and file session handler taking care of IP-Locking as well. Simply call the Factory and have it handle all the checks.

## Configuration via ENV

- SESSION_DURATION defaults to 7200 and is the session duration in seconds
- ENABLE_MEMCACHED must be set to true if memcached should be supported
- SESSION_PATH must be set if the sessions should be stored outside the system temp directory
