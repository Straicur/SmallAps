<?php

namespace App\Tests\Controller\NotebookController;

use App\Tests\AbstractWebTest;

/**
 * NotebookCategoriesTest
 */
class NotebookCategoriesTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Sending Request
     * step 3 - Checking response
     * step 4 - Checking response if note returned data is correct
     * @return void
     */
    public function test_notebookCategoriesCorrect(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test1",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test2",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test3",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test4",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test5",$user);

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 2
        $crawler = self::$webClient->request("GET", "/api/notebook/categories", server: [
            "HTTP_authorization" => $token->getToken()
        ]);
        /// step 3
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 4
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("notebookCategoryModels",$responseContent);
        $this->assertCount(5,$responseContent["notebookCategoryModels"]);

        $this->assertArrayHasKey("id",$responseContent["notebookCategoryModels"][0]);
        $this->assertArrayHasKey("name",$responseContent["notebookCategoryModels"][0]);
    }
    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_notebookCategoriesIncorrectPermission(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest"], true, "zaq12wsx");

        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test1",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test2",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test3",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test4",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test5",$user);

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 2
        $crawler = self::$webClient->request("GET", "/api/notebook/categories", server: [
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
     * step 1 - Preparing data
     * step 2 - Sending Request without token
     * step 3 - Checking response
     *
     * @return void
     */
    public function test_notebookCategoriesLogOut(): void
    {
        //// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test1",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test2",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test3",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test4",$user);
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test5",$user);

        /// step 2
        $crawler = self::$webClient->request("GET", "/api/notebook/categories");
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