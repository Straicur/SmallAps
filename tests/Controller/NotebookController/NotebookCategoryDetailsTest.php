<?php

namespace App\Tests\Controller\NotebookController;

use App\Tests\AbstractWebTest;

/**
 * NotebookCategoryDetailsTest
 */
class NotebookCategoryDetailsTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Sending Request
     * step 3 - Checking response
     * step 4 - Checking response if note returned data is correct
     * @return void
     */
    public function test_notebookCategoryDetailsCorrect(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test","text");

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 2
        $crawler = self::$webClient->request("POST", "/api/notebook/category/".$notebookCategory->getId()->__toString(), server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 3
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 4
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey("notebookNoteModels",$responseContent);
        $this->assertCount(1,$responseContent["notebookNoteModels"]);
        $this->assertArrayHasKey("id",$responseContent["notebookNoteModels"][0]);
        $this->assertArrayHasKey("title",$responseContent["notebookNoteModels"][0]);
        $this->assertArrayHasKey("dateAdd",$responseContent["notebookNoteModels"][0]);

        $this->assertSame($responseContent["notebookNoteModels"][0]["id"],$note->getId()->__toString());
        $this->assertSame($responseContent["notebookNoteModels"][0]["title"],$note->getTitle());
        $this->assertSame($responseContent["notebookNoteModels"][0]["dateAdd"],strval($note->getDateAdd()->getTimestamp()));
    }
    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_notebookCategoryDetailsIncorrectPermission(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test","text");

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 2
        $crawler = self::$webClient->request("POST", "/api/notebook/category/".$notebookCategory->getId()->__toString(), server: [
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
     * step 2 - Sending Request as a bad user
     * step 3 - Checking response
     * @return void
     */
    public function test_notebookCategoryDetailsIncorrectCredentials(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test1@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        /// step 1
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user2);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test","text");

        $token = $this->databaseMockManager->testFunc_loginUser($user1);
        /// step 2
        $crawler = self::$webClient->request("POST", "/api/notebook/category/".$notebookCategory->getId()->__toString(), server: [
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
    public function test_notebookCategoryDetailsEmptyRequest()
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test","text");

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 2
        $crawler = self::$webClient->request("POST", "/api/notebook/category/", server: [
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
    public function test_notebookCategoryDetailsLogOut(): void
    {
        //// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test","text");

        /// step 2
        $crawler = self::$webClient->request("POST", "/api/notebook/category/".$notebookCategory->getId()->__toString());
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