<?php

namespace App\Tests\Controller\TenziesController;

use App\Repository\TenzieResultRepository;
use App\Tests\AbstractWebTest;

/**
 * TenzieDeleteTest
 */
class TenzieDeleteTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if result was deleted
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

        $token = $this->databaseMockManager->testFunc_loginUser($user2);

        /// step 3
        $crawler = self::$webClient->request("DELETE", "/api/tenzie/".$tenzie3->getId()->__toString(), server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $tenzie3After = $tenzieResultRepository->findOneBy([
            "id"=>$tenzie3->getId()
        ]);

        $this->assertTrue($tenzie3After->getDeleted());
    }
    /**
     * step 1 - Creating normal Tenant
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_tenzieDeleteIncorrectPermission(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $token = $this->databaseMockManager->testFunc_loginUser($user2);

        /// step 3
        $crawler = self::$webClient->request("DELETE", "/api/tenzie/".$tenzie3->getId()->__toString(), server: [
            "HTTP_authorization" => $token->getToken()
        ]);
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
     * step 1 - Preparing JsonBodyContent
     * step 2 - Sending Request as a wrong user
     * step 3 - Checking response
     * @return void
     */
    public function test_tenzieDeleteIncorrectCredentials(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);

        /// step 3
        $crawler = self::$webClient->request("DELETE", "/api/tenzie/".$tenzie3->getId()->__toString(), server: [
            "HTTP_authorization" => $token->getToken()
        ]);
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
    public function test_tenzieDeleteEmptyRequest()
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        $token = $this->databaseMockManager->testFunc_loginUser($user2);

        /// step 3
        $crawler = self::$webClient->request("DELETE", "/api/tenzie/", server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 2
        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }
    /**
     * step 1 - Preparing data
     * step 2 - Sending Request without token
     * step 3 - Checking response
     *
     * @return void
     */
    public function test_tenzieDeleteLogOut(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $tenzie1 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test",1665405291);
        $tenzie2 = $this->databaseMockManager->testFunc_addTenzieResult($user1,2,"test2",1665405291);
        $tenzie3 = $this->databaseMockManager->testFunc_addTenzieResult($user2,2,"test2",1665405291);

        /// step 3
        $crawler = self::$webClient->request("DELETE", "/api/tenzie/".$tenzie3->getId()->__toString());
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