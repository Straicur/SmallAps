<?php

namespace App\Tests\Controller\AuthorizationController;

use App\Tests\AbstractWebTest;

/**
 * LoginTest
 *
 */
class LoginTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if responseContent has key token
     * @return void
     */
    public function test_loginCorrect(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        /// step 2
        $content = [
            "email" => "test@cos.pl",
            "password" => "zaq12wsx"
        ];
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/authorize", content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("token", $responseContent);
    }
    /**
     * step 1 - Preparing JsonBodyContent where there is no email
     * step 2 - Sending Request
     * step 3 - Checking response
     * @return void
     */
    public function test_loginIncorrectCredentials(): void
    {
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        /// step 1
        $content = [
            "email" => "tester@cos.pl",
            "password" => "zaq12wsx"
        ];
        /// step 2
        $crawler = self::$webClient->request("POST", "/api/authorize", content: json_encode($content));
        /// step 3
        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }
    /**
     * step 1 - Sending Request without content
     * step 2 - Checking response
     * @return void
     */
    public function test_loginEmptyRequest()
    {
        /// step 1
        $crawler = self::$webClient->request("POST", "/api/authorize");
        /// step 2
        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }
}
