IPv4 Mapping in IPv6
####################

.. highlight:: php

These methods exist for both single addresses and blocks.

IPv4 to IPv6
============

Mapped IPv4
-----------

* ``IPv4Address::toMappedIPv6()``
* ``IPv4Block::toMappedIPv6()``

Converts IPv4 to a mapped IPv6, the preferred way to express IPv4 as IPv6::

    <?php

    use Arokettu\IP\IPv4Address;
    use Arokettu\IP\IPv4Block;

    $address = IPv4Address::fromString('192.168.0.1');
    $block   = IPv4Block::fromString('192.168.0.0/24');

    var_dump($address->toMappedIPv6()); // ::ffff:192.168.0.1
    var_dump($block->toMappedIPv6()); // ::ffff:192.168.0.0/120

Compatible IPv4
---------------

.. note::
    This is a legacy mechanism and this library does not treat the resulting address as an encoded IPv4 since 2.1.1.

* ``IPv4Address::toCompatibleIPv6()``
* ``IPv4Block::toCompatibleIPv6()``

Converts IPv4 to a compatible IPv6, the legacy way to express IPv4 as IPv6::

    <?php

    use Arokettu\IP\IPv4Address;
    use Arokettu\IP\IPv4Block;

    $address = IPv4Address::fromString('192.168.0.1');
    $block   = IPv4Block::fromString('192.168.0.0/24');

    var_dump($address->toCompatibleIPv6()); // ::192.168.0.1
    var_dump($block->toCompatibleIPv6()); // ::192.168.0.0/120

IPv6 to IPv4
============

Check if IPv6 encodes IPv4
--------------------------

.. versionchanged:: 2.1.1
    ``isIPv4()`` no longer returns true for "compatible" IPv4.
    Therefore it's effectively an alias of ``isMappedIPv4()``.

* ``IPv4Address::isMappedIPv4()`` /  ``IPv4Block::isMappedIPv4()``
* ``IPv4Address::isCompatibleIPv4()`` /  ``IPv4Block::isCompatibleIPv4()``
* ``IPv4Address::isIPv4()`` /  ``IPv4Block::isIPv4()``

Checks if the address encodes IPv6 as a mapped or compatible address or any of them::

    <?php

    use Arokettu\IP\IPv6Address;
    use Arokettu\IP\IPv6Block;

    $address = IPv6Address::fromString('::ffff:192.168.0.1');
    $block   = IPv6Block::fromString('::ffff:192.168.0.0/120');

    $address->isMappedIPv4(); // true
    $address->isCompatibleIPv4(); // false
    $address->isIPv4(); // true

    $block->isMappedIPv4(); // true
    $block->isCompatibleIPv4(); // false
    $block->isIPv4(); // true

Get encoded IPv4
----------------

.. versionchanged:: 2.1.1
    "Compatible" IPv4 range is no longer considered being a representation of IPv4.

* ``IPv4Address::getIPv4()``
* ``IPv4Block::getIPv4()``

If IPv6 encodes IPv4, returns this address or block::

    <?php

    use Arokettu\IP\IPv6Address;
    use Arokettu\IP\IPv6Block;

    $address = IPv6Address::fromString('::ffff:192.168.0.1');
    $block   = IPv6Block::fromString('::ffff:192.168.0.0/120');

    var_dump((string)$address->getIPv4()); // 192.168.0.1
    var_dump((string)$block->getIPv4()); // 192.168.0.0/24
