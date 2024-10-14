Blocks
######

.. highlight:: php

Classes
=======

There are 4 classes representing blocks:

``Arokettu\IP\IPv4Block``
    IPv4 block
``Arokettu\IP\IPv6Block``
    IPv6 block
``Arokettu\IP\IPBlock``
    Common factory methods with version autodetection
``Arokettu\IP\AnyIPBlock``
    An interface meaning both ``IPv4Block`` and ``IPv6Block``

Factories
=========

``fromBytes()``
---------------

* ``Arokettu\IP\IPv4Block::fromBytes($bytes, $prefix, $strict = false)``
* ``Arokettu\IP\IPv6Block::fromBytes($bytes, $prefix, $strict = false)``
* ``Arokettu\IP\IPBlock::fromBytes($bytes, $prefix, $strict = false)``

Creates an object from a byte representation of the base address (such as created by the ``inet_pton()`` function)
and a prefix value.
Non-strict mode allows non-normalized base address and negative prefixes
(for IPv4 -1 equals to 32 and -32 equals to 1)::

    <?php

    use Arokettu\IP\IPBlock;

    $block = IPBlock::fromBytes("\x7f\0\0\0", 8); // 127.0.0.0/8

    // denormalized base:
    $block = IPBlock::fromBytes("\x7f\0\0\1", 8); // 127.0.0.0/8
    $block = IPBlock::fromBytes("\x7f\0\0\1", 8, strict: true); // UnexpectedValueException

    // negative prefix:
    $block = IPBlock::fromBytes("\x7f\0\0\1", -25); // 127.0.0.0/8
    $block = IPBlock::fromBytes("\x7f\0\0\1", -25, strict: true); // UnexpectedValueException
    // mostly useful for single IP blocks in autodetect factories:
    $block = IPBlock::fromBytes("\x7f\0\0\1", -1); // 127.0.0.1/32
    $block = IPBlock::fromBytes("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\1", -1); // ::1/128

``fromString()``
----------------

* ``Arokettu\IP\IPv4Block::fromString($string, $prefix, $strict = false)``
* ``Arokettu\IP\IPv6Block::fromString($string, $prefix, $strict = false)``
* ``Arokettu\IP\IPBlock::fromString($string, $prefix, $strict = false)``

Creates an object from a string representation of the base address (same valid values as for the ``inet_pton()`` function)
and a prefix value.
Prefix may be a part of the string (CIDR notation) or a separate parameter.
Non-strict mode allows non-normalized base addresses, negative prefixes, and prefix present in both string and $prefix::

    <?php

    use Arokettu\IP\IPBlock;

    // CIDR notation
    $block = IPBlock::fromString("127.0.0.0/8");
    // Separate prefix
    $block = IPBlock::fromString("127.0.0.0", 8); // 127.0.0.0/8
    // String value takes precedence
    $block = IPBlock::fromString("127.0.0.0/8", 16); // 127.0.0.0/8
    // Strict mode disallows double prefix
    $block = IPBlock::fromString("127.0.0.0/8", 16, strict: true); // BadMethodCallException

    // Negative prefixes, like in fromBytes
    $block = IPBlock::fromString("127.0.0.1", -1); // 127.0.0.1/32
    $block = IPBlock::fromString("::1", -1); // ::1/128
    // Strict mode disallows that
    $block = IPBlock::fromString("::1", -1, strict: true); // UnexpectedValueException
    // Negative prefixes can only be in a separate parameter
    $block = IPBlock::fromString("127.0.0.1/-1"); // UnexpectedValueException

    // Denormalized base:
    $block = IPBlock::fromString("127.0.0.1/8"); // 127.0.0.0/8
    $block = IPBlock::fromString("127.0.0.1/8", strict: true); // UnexpectedValueException

Methods
=======

Containment
-----------

Exists in 3 versions:

* ``strictContains($addressOrBlock)`` does not allow mixing of IP versions
* ``nonStrictContains($addressOrBlock)`` allows mixing of IP versions, blocks never contain a "wrong" type of address
* ``contains($addressOrBlock, $strict = false)`` calls one of the above depending on $strict

A method to check if an address or a smaller block belongs to the given block::

    <?php

    use Arokettu\IP\IPAddress;
    use Arokettu\IP\IPBlock;

    $block1 = IPBlock::fromString('127.0.0.0/8');
    $block2 = IPBlock::fromString('127.0.0.0/16');

    $ip1 = IPAddress::fromString('127.0.0.1');
    $ip2 = IPAddress::fromString('fc80::abcd');

    $block1->contains($ip1); // true
    $block1->contains($ip2); // false
    $block1->contains($ip2, strict: true); // TypeError
    $block1->contains($block2); // true

Comparison
----------

.. note:: See :ref:`compare-helper`

Also exists in 3 versions:

* ``strictCompare($address)`` does not allow mixing of IP versions
* ``nonStrictCompare($address)`` allows mixing of IP versions, IPv4 blocks are "smaller" than IPv6 versions
* ``compare($address, $strict = false)`` calls one of the above depending on $strict

Blocks are compared first by base addresses, then by prefix lengths in natural order.

``127.0.0.0/8 < 192.168.0.0/16 < 192.168.0.0/24 < 192.168.1.0/24 < 255.0.0.0/8``

Returns one of ``[-1, 0, 1]`` like ``strcmp()`` or ``<=>``.

::

    <?php

    use Arokettu\IP\IPBlock;

    $block1 = IPBlock::fromString("127.0.0.0/16");
    $block2 = IPBlock::fromString("127.1.0.0/16");

    $block2->compare($block1) > 0; // $block2 > $block1; true

Equality
--------

Also exists in 3 versions:

* ``strictEquals($address)`` does not allow mixing of IP versions
* ``nonStrictEquals($address)`` allows mixing of IP versions, IPv4 and IPv6 are never equal to each other
* ``equals($address, $strict = false)`` calls one of the above depending on $strict

Returns ``boolean``.

::

    <?php

    use Arokettu\IP\IPBlock;

    $block1 = IPBlock::fromString("127.0.0.0/16");
    $block2 = IPBlock::fromString("127.1.0.0/16");

    $block1->equals($block2); // $block1 == $block2; false

``toString()``
--------------
Returns the canonical string representation of the IP block in CIDR notation::

    <?php

    use Arokettu\IP\IPBlock;

    $block = IPBlock::fromString("127.0.0.0/8");

    echo $block->toString(); // 127.0.0.0/8

Other getters
-----------------

``getBytes()``
    Byte representation of the base address
``getPrefix()``
    Prefix length
``getMaskBytes()``
    Byte representation of the mask
``getMaskString()``
    Mask value in the IP notation
``getFirstAddress()``
    The first IP in the block, also its base address
``getLastAddress()``
    The last IP in the block, the multicast address for the IPv4
