# PHP IP Library

[![Packagist]][Packagist Link]
[![PHP]][Packagist Link]
[![License]][License Link]
[![Gitlab CI]][Gitlab CI Link]
[![Codecov]][Codecov Link]

[Packagist]: https://img.shields.io/packagist/v/arokettu/ip-address.svg?style=flat-square
[PHP]: https://img.shields.io/packagist/php-v/arokettu/ip-address.svg?style=flat-square
[License]: https://img.shields.io/packagist/l/arokettu/ip-address.svg?style=flat-square
[Gitlab CI]: https://img.shields.io/gitlab/pipeline/sandfox/php-ip-address/master.svg?style=flat-square
[Codecov]: https://img.shields.io/codecov/c/gl/sandfox/php-ip-address?style=flat-square

[Packagist Link]: https://packagist.org/packages/arokettu/ip-address
[License Link]: LICENSE.md
[Gitlab CI Link]: https://gitlab.com/sandfox/php-ip-address/-/pipelines
[Codecov Link]: https://codecov.io/gl/sandfox/php-ip-address/

IP address and block classes for PHP.

## Installation

```bash
composer require arokettu/ip-address
```

## Usage

```php
<?php

use Arokettu\IP\IPAddress;
use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Block;
use Arokettu\IP\Tools\CompareHelper;
use Arokettu\IP\Tools\BlockOptimizer;

// IP address
$ip4 = IPv4Address::fromString('140.82.121.4');
$ip6 = IPv6Address::fromString('2606:4700:90:0:f22e:fbec:5bed:a9b9');

$ipAuto = IPAddress::fromString('172.65.251.78'); // IPv4Address in this case

// IP Block
$block4 = IPv4Block::fromString('140.82.112.0/20'); // CIDR string
$block6 = IPv6Block::fromString('2606:4700::', 32); // Base IP and prefix length

$blockAuto = IPBlock::fromString('fe80::/10'); // IPv6Block in this case

// Containment
// If block contains IP
$block4->contains($ip4); // true
// If block contains other block
$block6->contains($blockAuto); // false

// Sort helper
$ips = [/* IP Addresses */];
usort($ips, CompareHelper::nonStrictCompare(...)); // allows to mix v4 and v6

// Block collapser
$blocks = [
    IPv4Block::fromString('127.0.0.0/24'),
    IPv4Block::fromString('127.0.0.0/16'),
    IPv4Block::fromString('192.168.0.0/24'),
    IPv4Block::fromString('192.168.1.0/24'),
];
$optimized = BlockOptimizer::optimize(...$blocks); // [127.0.0.0/16, 192.168.0.0/23]
```

## Documentation

Read full documentation here: <https://sandfox.dev/php/ip-address.html>

Also on Read the Docs: <https://arokettu-ip-address.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/php-ip-address/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License][License Link].
