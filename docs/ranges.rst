Ranges
######

.. highlight:: php

Classes
=======

There are 4 classes representing ranges:

``Arokettu\IP\IPv4Range``
    IPv4 range
``Arokettu\IP\IPv6Range``
    IPv6 range
``Arokettu\IP\IPRange``
    Common factory methods with version autodetection
``Arokettu\IP\AnyIPRange``
    An interface meaning both ``IPv4Range`` and ``IPv6Range``

Factories
=========

``fromBytes()``
---------------

* ``Arokettu\IP\IPv4Range::fromBytes($bytes, $prefix, $strict = false)``
* ``Arokettu\IP\IPv6Range::fromBytes($bytes, $prefix, $strict = false)``
* ``Arokettu\IP\IPRange::fromBytes($bytes, $prefix, $strict = false)``

Creates an object from a byte representation of the base address (such as created by the ``inet_pton()`` function)
and a prefix value.
Non-strict mode allows non-normalized base address and negative prefixes
(for IPv4 -1 equals to 32 and -32 equals to 1)::

    <?php

    use Arokettu\IP\IPRange;

    $range = IPRange::fromBytes("\x7f\0\0\0", 8); // 127.0.0.0/8

    // denormalized base:
    $range = IPRange::fromBytes("\x7f\0\0\1", 8); // 127.0.0.0/8
    $range = IPRange::fromBytes("\x7f\0\0\1", 8, strict: true); // DomainException

    // negative prefix:
    $range = IPRange::fromBytes("\x7f\0\0\1", -25); // 127.0.0.0/8
    $range = IPRange::fromBytes("\x7f\0\0\1", -25, strict: true); // DomainException
    // mostly useful for single IP ranges in autodetect factories:
    $range = IPRange::fromBytes("\x7f\0\0\1", -1); // 127.0.0.1/32
    $range = IPRange::fromBytes("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\1", -1); // ::1/128

``fromString()``
----------------

* ``Arokettu\IP\IPv4Range::fromString($string, $prefix, $strict = false)``
* ``Arokettu\IP\IPv6Range::fromString($string, $prefix, $strict = false)``
* ``Arokettu\IP\IPRange::fromString($string, $prefix, $strict = false)``

Creates an object from a string representation of the base address (same valid values as for the ``inet_pton()`` function)
and a prefix value.
Prefix may be a part of the string (CIDR notation) or a separate parameter.
Non-strict mode allows non-normalized base addresses, negative prefixes, and prefix present in both string and $prefix::

    <?php

    use Arokettu\IP\IPRange;

    // CIDR notation
    $range = IPRange::fromString("127.0.0.0/8");
    // Separate prefix
    $range = IPRange::fromString("127.0.0.0", 8); // 127.0.0.0/8
    // String value takes precedence
    $range = IPRange::fromString("127.0.0.0/8", 16); // 127.0.0.0/8
    // Strict mode disallows double prefix
    $range = IPRange::fromString("127.0.0.0/8", 16, strict: true); // InvalidArgumentException

    // Negative prefixes, like in fromBytes
    $range = IPRange::fromString("127.0.0.1", -1); // 127.0.0.1/32
    $range = IPRange::fromString("::1", -1); // ::1/128
    // Strict mode disallows that
    $range = IPRange::fromString("::1", -1, strict: true); // DomainException
    // Negative prefixes can only be in a separate parameter
    $range = IPRange::fromString("127.0.0.1/-1"); // DomainException

    // Denormalized base:
    $range = IPRange::fromString("127.0.0.1/8"); // 127.0.0.0/8
    $range = IPRange::fromString("127.0.0.1/8", strict: true); // DomainException

Methods
=======

Containment
-----------

Exists in 3 versions:

* ``strictContains($addressOrRange)`` does not allow mixing of IP versions
* ``nonStrictContains($addressOrRange)`` allows mixing of IP versions, ranges never contain a "wrong" type of address
* ``contains($addressOrRange, $strict = false)`` calls one of the above depending on $strict

A method to check if an address or a smaller range belongs to the given range::

    <?php

    use Arokettu\IP\IPAddress;
    use Arokettu\IP\IPRange;

    $range1 = IPRange::fromString('127.0.0.0/8');
    $range2 = IPRange::fromString('127.0.0.0/16');

    $ip1 = IPAddress::fromString('127.0.0.1');
    $ip2 = IPAddress::fromString('fc80::abcd');

    $range1->contains($ip1); // true
    $range1->contains($ip2); // false
    $range1->contains($ip2, strict: true); // TypeError
    $range1->contains($range2); // true

Comparison
----------

.. note:: See :ref:`compare-helper`

Also exists in 3 versions:

* ``strictCompare($address)`` does not allow mixing of IP versions
* ``nonStrictCompare($address)`` allows mixing of IP versions, IPv4 ranges are "smaller" than IPv6 versions
* ``compare($address, $strict = false)`` calls one of the above depending on $strict

Ranges are compared first by base addresses, then by prefix lengths in natural order.

``192.168.0.0/16 < 192.168.0.0/24 < 192.168.1.0/24``

Returns one of ``[-1, 0, 1]`` like ``strcmp()`` or ``<=>``.

::

    <?php

    use Arokettu\IP\IPRange;

    $range1 = IPRange::fromString("127.0.0.0/16");
    $range2 = IPRange::fromString("127.1.0.0/16");

    $range2->compare($range1) > 0; // $range2 > $range1; true

Equality
--------

Also exists in 3 versions:

* ``strictEquals($address)`` does not allow mixing of IP versions
* ``nonStrictEquals($address)`` allows mixing of IP versions, IPv4 and IPv6 are never equal to each other
* ``equals($address, $strict = false)`` calls one of the above depending on $strict

Returns ``boolean``.

::

    <?php

    use Arokettu\IP\IPRange;

    $range1 = IPRange::fromString("127.0.0.0/16");
    $range2 = IPRange::fromString("127.1.0.0/16");

    $range1->equals($range2); // $range1 == $range2; false

``toString()``
--------------
Returns the canonical string representation of the IP range in CIDR notation::

    <?php

    use Arokettu\IP\IPRange;

    $range = IPRange::fromString("127.0.0.0/8");

    echo $range->toString(); // 127.0.0.0/8

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
    The first IP in the range, also its base address
``getLastAddress()``
    The last IP in the range, the multicast address for the IPv4
