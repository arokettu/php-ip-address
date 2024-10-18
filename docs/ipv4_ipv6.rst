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
