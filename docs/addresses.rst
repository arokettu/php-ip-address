Addresses
#########

.. highlight:: php

Classes
=======

There are 4 classes representing addresses:

``Arokettu\IP\IPv4Address``
    IPv4 address
``Arokettu\IP\IPv6Address``
    IPv6 address
``Arokettu\IP\IPAddress``
    Common factory methods with version autodetection
``Arokettu\IP\AnyIPAddress``
    An interface meaning both ``IPv4Address`` and ``IPv6Address``

Factories
=========

``fromBytes()``
---------------

* ``Arokettu\IP\IPv4Address::fromBytes($bytes)``
* ``Arokettu\IP\IPv6Address::fromBytes($bytes)``
* ``Arokettu\IP\IPAddress::fromBytes($bytes)``

Creates an object from a byte representation (such as created by the ``inet_pton()`` function)::

    <?php

    use Arokettu\IP\IPAddress;

    $ip = IPAddress::fromBytes("\x7f\0\0\1"); // 127.0.0.1

``fromString()``
----------------

* ``Arokettu\IP\IPv4Address::fromString($string)``
* ``Arokettu\IP\IPv6Address::fromString($string)``
* ``Arokettu\IP\IPAddress::fromString($string)``

Creates an object from a string representation (same valid values as for the ``inet_pton()`` function)::

    <?php

    use Arokettu\IP\IPAddress;

    $ip = IPAddress::fromString("::1");

Methods
=======

Comparison
----------

.. note:: See :ref:`compare-helper`

Exists in 3 versions:

* ``strictCompare($address)`` does not allow mixing of IP versions
* ``nonStrictCompare($address)`` allows mixing of IP versions, IPv4 addresses are "smaller" than IPv6 versions
* ``compare($address, $strict = false)`` calls one of the above depending on $strict

Returns one of ``[-1, 0, 1]`` like ``strcmp()`` or ``<=>``.

::

    <?php

    use Arokettu\IP\IPAddress;

    $ip1 = IPAddress::fromString("127.0.0.1");
    $ip2 = IPAddress::fromString("127.0.0.2");

    $ip2->compare($ip1) > 0; // $ip2 > $ip1; true

Equality
--------

Also exists in 3 versions:

* ``strictEquals($address)`` does not allow mixing of IP versions
* ``nonStrictEquals($address)`` allows mixing of IP versions, IPv4 and IPv6 are never equal to each other
* ``equals($address, $strict = false)`` calls one of the above depending on $strict

Returns ``boolean``.

::

    <?php

    use Arokettu\IP\IPAddress;

    $ip1 = IPAddress::fromString("127.0.0.1");
    $ip2 = IPAddress::fromString("127.0.0.2");

    $ip2->equals($ip1); // $ip2 == $ip1; false

``toString()``
--------------
Returns the canonical string representation of the IP::

    <?php

    use Arokettu\IP\IPAddress;

    $ip = IPAddress::fromString("127.0.0.1");

    echo $ip->toString(); // 127.0.0.1

``getBytes()``
--------------

Returns the byte representation of the IP::

    <?php

    use Arokettu\IP\IPAddress;

    $ip = IPAddress::fromString("127.0.0.1");

    echo bin2hex($ip->getBytes()); // 7f000001
