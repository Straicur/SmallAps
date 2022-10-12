<?php

namespace App\Tests\Controller\TenziesController;

use App\Repository\TenzieResultRepository;
use App\Tests\AbstractWebTest;

/**
 * LoginTest
 *
 */
class TenzieAllTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if responseContent has key token
     * @return void
     */
    public function test_tenzieAllCorrect(): void
    {
        $tenzieResultRepository = $this->getService(TenzieResultRepository::class);

        $this->assertInstanceOf(TenzieResultRepository::class, $tenzieResultRepository);
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",'1665405291');
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",'1665405291');
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",'1665405291');

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/all", server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey("tenzieAllModels", $responseContent);
        $this->assertCount(2,$responseContent["tenzieAllModels"]);
    }
}