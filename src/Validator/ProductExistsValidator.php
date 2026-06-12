<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductExistsValidator extends ConstraintValidator
{
    private string $catalogUrl;

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
        $this->catalogUrl = getenv('CATALOG_URL') ?: 'http://localhost:8000/api/v1';
    }

    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                $this->catalogUrl . '/products/' . $value . '/',
                ['timeout' => 3]
            );
            if ($response->getStatusCode() === 404) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', (string) $value)
                    ->addViolation();
            }
        } catch (\Exception $e) {
            $this->context->buildViolation('Catalogue indisponible')->addViolation();
        }
    }
}
