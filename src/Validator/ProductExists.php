<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ProductExists extends Constraint
{
    public string $message = 'Le produit {{ id }} n\'existe pas dans le catalogue.';
}
