Tools
#####

.. highlight:: php

Range Optimizer
===============

* ``Arokettu\IP\Tools\RangeOptimizer::optimizeV4(...$ranges)``
* ``Arokettu\IP\Tools\RangeOptimizer::optimizeV6(...$ranges)``

Range optimizer allows you to transform a set of IP ranges into an equivalent smaller set
by discarding overlapping ranges and gluing adjacent ranges. Example::

   <?php

    use Arokettu\IP\IPv4Range;
    use Arokettu\IP\Tools\RangeOptimizer;

    $ranges = [
        // ranges that are contained in the first one
        IPv4Range::fromString('127.0.0.0/8'),
        IPv4Range::fromString('127.0.0.0/16'),
        IPv4Range::fromString('127.0.64.0/24'),
        // adjacent ranges that can be combined
        IPv4Range::fromString('192.168.0.0/24'),
        IPv4Range::fromString('192.168.1.0/24'),
        IPv4Range::fromString('192.168.2.0/23'),
    ];

    $optimized = RangeOptimizer::optimizeV4(...$ranges); // 127.0.0.0/8, 192.168.0.0/22

.. _compare-helper:

Compare Helper
==============

* ``Arokettu\IP\Tools\CompareHelper::compare($left, $right, $strict = false)``
* ``Arokettu\IP\Tools\CompareHelper::strictCompare($left, $right)``
* ``Arokettu\IP\Tools\CompareHelper::nonStrictCompare($left, $right)``

Just a wrapper that executes ``$left->compare($right)``.
A useful shortcut for sorting::

    <?php

    use Arokettu\IP\Tools\CompareHelper;

    $addresses = [/* IP addresses */];
    usort($addresses, CompareHelper::nonStrictCompare(...));

    // or

    $ranges = [/* IP ranges */];
    usort($ranges, CompareHelper::nonStrictCompare(...));

* ``Arokettu\IP\Tools\CompareHelper::sort(&$array, $strict = false)``
* ``Arokettu\IP\Tools\CompareHelper::asort(&$array, $strict = false)``

Uses the helper above to call ``usort`` or ``uasort``
