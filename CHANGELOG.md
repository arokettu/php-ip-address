# Changelog

## 2.x

### 2.3.1

*Oct 5, 2025*

* Fixed deprecation warning on PHP 8.5

### 2.3.0

*Feb 20, 2025*

* `BlockOptimizer::optimize()` that accepts mixed v4+v6 arrays

### 2.2.0

*Feb 7, 2025*

* IPv6 addresses and blocks:
  * `isCompatibleIPv4()` no longer returns true for `::` and `::1`
  * `getIPv4()` has new param `$allowCompatible` that allows to get "compatible" IPv4 addresses as well
  * `isIPv4()` is deprecated
  * `toFullHexString()` returns non-shortened hex representation of the address

### 2.1.1

*Oct 23, 2024*

* "Compatible" IPv4 range (all zeros prefix) for IPv6 is no longer considered encoded IPv4 addresses
  * This fixes `::` and `::1` being considered IPv4
  * `isIPv4()` is now an alias of `isMappedIPv4()`

### 2.1.0

*Oct 19, 2024*

* For both addresses and blocks, methods to work with IPv4 to IPv6 mapping:
  * IPv4:
    * `toMappedIPv6()`
    * `toCompatibleIPv6()`
  * IPv6:
    * `isMappedIPv4()`
    * `isCompatibleIPv4()`
    * `isIPv4()`
    * `getIPv4()`
* Blocks:
  * `isSingleAddress()`

### 2.0.0

*Oct 14, 2024*

Forked from 1.0.1

* Ranges renamed to Blocks for clarity and to free name for possible implementation of freeform ranges
  * All `*Range` classes renamed to `*Block`
  * `toRange()` renamed to `toBlock()`
  * `RangeOptimizer` renamed to `BlockOptimizer`

## 1.x

### 1.0.1

*Jul 28, 2024*

* Fixed throwing LogicExceptions for runtime cases
  * `DomainException` -> `UnexpectedValueException`

### 1.0.0

*Jul 28, 2024*

Initial release
