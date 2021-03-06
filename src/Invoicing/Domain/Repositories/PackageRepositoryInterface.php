<?php

namespace BestInvestments\Invoicing\Domain\Repositories;

use BestInvestments\Invoicing\Domain\Aggregates\Package;
use BestInvestments\Invoicing\Domain\ValueObjects\PackageReference;

interface PackageRepositoryInterface
{
    public function getByReference(PackageReference $packageReference): ?Package;
}
