<?php

namespace App\Tests\Controller\TenziesController;

use App\Repository\TenzieResultRepository;
use App\Tests\AbstractWebTest;

/**
 * TenzieBestTest
 */
class TenzieBestTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if reutned data is correct
     * @return void
     */
    public function test_tenzieBestCorrect(): void
    {
        $tenzieResultRepository = $this->getService(TenzieResultRepository::class);

        $this->assertInstanceOf(TenzieResultRepository::class, $tenzieResultRepository);
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test3",1669405292);
        $tenzie4 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test4",1675405290);
        $tenzie5 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test5",1685405291);
        $tenzie6 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test6",1669405292);
        $tenzie7 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test7",1675405290);
        $tenzie8 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test8",1685405291);
        $tenzie9 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test9",1669405292);
        $tenzie10 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test10",1675405290);
        $tenzie11 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test11",1685405291);
        $tenzie12 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test12",1669405292);
        $tenzie13 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test13",1675405290);
        $tenzie14 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test14",1685405291);
        $tenzie15 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test15",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,3,"test3",1669405292);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        $content = [
            "levels"=>[
                "level"=>
                [1,2,3,4]
            ]
        ];
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/best", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("tenzieBestModels",$responseContent);
        $this->assertCount(4,$responseContent["tenzieBestModels"]);
        $this->assertArrayHasKey("tenzieAllModels",$responseContent["tenzieBestModels"][0]);
        $this->assertCount(10,$responseContent["tenzieBestModels"][0]["tenzieAllModels"]);

    }
    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_tenzieBestIncorrectPermission(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest"], true, "zaq12wsx");
        /// step 2
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,3,"test3",1669405292);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        $content = [
            "levels"=>[
                "level"=>
                    [1,2,3,4]
            ]
        ];
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/best", server: [
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
    public function test_tenzieBestEmptyRequest()
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,3,"test3",1669405292);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/best", server: [
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
    public function test_tenzieBestLogOut(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,1,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,1,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test3",1669405292);

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test",1675405290);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,3,"test2",1685405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,3,"test3",1669405292);

        $content = [
            "levels"=>[
                "level"=>
                    [1,2,3,4]
            ]
        ];
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/tenzie/best", content: json_encode($content));
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