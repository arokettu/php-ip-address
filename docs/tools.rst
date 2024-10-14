Tools
#####

.. highlight:: php

Block Optimizer
===============

* ``Arokettu\IP\Tools\BlockOptimizer::optimizeV4(...$blocks)``
* ``Arokettu\IP\Tools\BlockOptimizer::optimizeV6(...$blocks)``

Block optimizer allows you to transform a set of IP blocks into an equivalent smaller set
by discarding overlapping blocks and gluing adjacent blocks. Example::

   <?php

    use Arokettu\IP\IPv4Block;
    use Arokettu\IP\Tools\BlockOptimizer;

    $blocks = [
        // blocks that are contained in the first one
        IPv4Block::fromString('127.0.0.0/8'),
        IPv4Block::fromString('127.0.0.0/16'),
        IPv4Block::fromString('127.0.64.0/24'),
        // adjacent blocks that can be combined
        IPv4Block::fromString('192.168.0.0/24'),
        IPv4Block::fromString('192.168.1.0/24'),
        IPv4Block::fromString('192.168.2.0/23'),
    ];

    $optimized = BlockOptimizer::optimizeV4(...$blocks); // 127.0.0.0/8, 192.168.0.0/22

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

    $blocks = [/* IP blocks */];
    usort($blocks, CompareHelper::nonStrictCompare(...));

* ``Arokettu\IP\Tools\CompareHelper::sort(&$array, $strict = false)``
* ``Arokettu\IP\Tools\CompareHelper::asort(&$array, $strict = false)``

Uses the helper above to call ``usort`` or ``uasort``
