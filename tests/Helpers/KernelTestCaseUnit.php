<?php

namespace App\Tests\Helpers;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class KernelTestCaseUnit extends KernelTestCase
{
    use ResetDatabase;
    use Factories;
}