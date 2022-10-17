<?php

namespace App\Tests\Controller\NotebookController;

use App\Entity\NotebookCategory;
use App\Repository\NotebookCategoryRepository;
use App\Repository\NotebookNoteRepository;
use App\Tests\AbstractWebTest;

/**
 * NotebookCategoryDeleteTest
 */
class NotebookCategoryDeleteTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Sending Request
     * step 3 - Checking response
     * step 4 - Checking response if note returned data is correct
     * @return void
     */
    public function test_notebookCategoryDeleteCorrect(): void
    {
        $notebookNoteRepository = $this->getService(NotebookNoteRepository::class);
        $notebookCategoryRepository = $this->getService(NotebookCategoryRepository::class);

        $this->assertInstanceOf(NotebookCategoryRepository::class, $notebookCategoryRepository);
        $this->assertInstanceOf(NotebookNoteRepository::class, $notebookNoteRepository);
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test","text");

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 2
        $crawler = self::$webClient->request("DELETE", "/api/notebook/category/".$notebookCategory->getId()->__toString(), server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 3
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(0,$notebookNoteRepository->findAll());
        $this->assertCount(0,$notebookCategoryRepository->findAll());
    }
    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_notebookCategoryDeleteIncorrectPermission(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test1@cos.pl", "+48123123123", ["Guest"], true, "zaq12wsx");
        /// step 2
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user2);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);
        /// step 3
        $crawler = self::$webClient->request("DELETE", "/api/notebook/category/".$notebookCategory->getId()->__toString(), server: [
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
     * step 1 - Sending Request as a bad user
     * step 2 - Checking response
     * @return void
     */
    public function test_notebookCategoryDeleteIncorrectCredentials(): void
    {
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user2);

        $token = $this->databaseMockManager->testFunc_loginUser($user1);
        /// step 1
        $crawler = self::$webClient->request("DELETE", "/api/notebook/category/".$notebookCategory->getId()->__toString(), server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 2
        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }
    /**
     * step 1 - Sending Request with bad id
     * step 2 - Checking response
     * @return void
     */
    public function test_notebookCategoryDeleteIncorrectCategoryCredentials(): void
    {
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 1
        $crawler = self::$webClient->request("DELETE", "/api/notebook/category/66666c4e-16e6-1ecc-9890-a7e8b0073d3b", server: [
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
    public function test_notebookCategoryDeleteEmptyRequest()
    {
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user2);

        $token = $this->databaseMockManager->testFunc_loginUser($user2);
        /// step 1
        $crawler = self::$webClient->request("DELETE", "/api/notebook/category/", server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 2
        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

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
    public function test_notebookCategoryDeleteLogOut(): void
    {
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user2);

        /// step 1
        $crawler = self::$webClient->request("DELETE", "/api/notebook/category/".$notebookCategory->getId()->__toString());
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