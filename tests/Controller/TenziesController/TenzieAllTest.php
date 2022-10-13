<?php

namespace App\Tests\Controller\TenziesController;

use App\Repository\TenzieResultRepository;
use App\Tests\AbstractWebTest;

/**
 * TenzieAllTest
 */
class TenzieAllTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if responseContent is correct
     * @return void
     */
    public function test_tenzieAllCorrect(): void
    {
        $tenzieResultRepository = $this->getService(TenzieResultRepository::class);

        $this->assertInstanceOf(TenzieResultRepository::class, $tenzieResultRepository);
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        $content = [
            "page"=> 1,
            "limit"=> 10
        ];
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/all", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));

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
    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_tenzieAllIncorrectPermission(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        $content = [
            "page"=> 1,
            "limit"=> 10
        ];
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/all", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));

        /// step 4
        $this->assertResponseStatusCodeSame(403);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);

        $responseContent = json_decode($responseContent, true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
    }
    /**
     * step 1 - Sending Request without content
     * step 2 - Checking response
     * @return void
     */
    public function test_tenzieAllEmptyRequest()
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        $crawler = self::$webClient->request("POST", "/api/tenzie/all", server: [
            "HTTP_authorization" => $token->getToken()
        ]);

        /// step 2
        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }
    /**
     * step 1 - Preparing data
     * step 2 - Sending Request without token
     * step 3 - Checking response
     *
     * @return void
     */
    public function test_tenzieAllLogOut(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $content = [
            "page"=> 1,
            "limit"=> 10
        ];
        /// step 2
        $crawler = self::$webClient->request("POST", "/api/tenzie/all", content: json_encode($content));

        /// step 3
        $this->assertResponseStatusCodeSame(401);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);

        $responseContent = json_decode($responseContent, true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
    }
}