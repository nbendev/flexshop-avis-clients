<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReviewApiTest extends WebTestCase
{
    public function testGetReviewsReturnsOk(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/reviews');
        $this->assertResponseIsSuccessful();
    }

    public function testGetNonExistentReviewReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/reviews/99999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreateReviewWithInvalidRatingFails(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/reviews', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => 1, 'authorName' => 'Test',
                'rating' => 10, 'comment' => 'invalid',
            ])
        );
        $this->assertResponseStatusCodeSame(422);
    }
}
