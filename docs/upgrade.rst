Upgrade
#######

.. highlight:: php

1.x to 2.0
==========

* All range references renamed to blocks

  * ``*Range`` -> ``*Block``
  * ``toRange()`` -> ``toBlock()``
  * ``RangeOptimizer`` -> ``BlockOptimizer``

  1.x::

        <?php

        use Arokettu\IP\IPAddress;
        use Arokettu\IP\IPv6Range;

        var_dump(IPAddress::fromString('::1')->toRange() instanceof IPv6Range);

  2.x::

        <?php

        <?php

        use Arokettu\IP\IPAddress;
        use Arokettu\IP\IPv6Block;

        var_dump(IPAddress::fromString('::1')->toBlock() instanceof IPv6Block);
