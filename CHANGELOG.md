# Changelog

## 2.x

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
