<?php

namespace App\Tests\Controller\NotebookController;

use App\Repository\NotebookNoteRepository;
use App\Tests\AbstractWebTest;

/**
 * NotebookNoteAddTest
 */
class NotebookNoteAddTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if note returned data is correct
     * @return void
     */
    public function test_notebookNoteAddCorrect(): void
    {
        $notebookNoteRepository = $this->getService(NotebookNoteRepository::class);

        $this->assertInstanceOf(NotebookNoteRepository::class, $notebookNoteRepository);
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test1","text1");

        $token = $this->databaseMockManager->testFunc_loginUser($user);

        $content = [
            "title"=> "test2",
            "text"=> "test2",
            "notebookCategoryId"=> $notebookCategory->getId()->__toString(),
        ];

        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/notebook/note/add", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey("categoryId",$responseContent);
        $this->assertArrayHasKey("text",$responseContent);
        $this->assertArrayHasKey("id",$responseContent);
        $this->assertArrayHasKey("title",$responseContent);
        $this->assertArrayHasKey("dateAdd",$responseContent);

        $this->assertCount(2,$notebookNoteRepository->findAll());
    }
    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_notebookNoteAddIncorrectPermission(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest"], true, "zaq12wsx");
        /// step 2
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test1","text1");

        $token = $this->databaseMockManager->testFunc_loginUser($user);

        $content = [
            "title"=> "test2",
            "text"=> "test2",
            "notebookCategoryId"=> $notebookCategory->getId()->__toString(),
        ];

        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/notebook/note/add", server: [
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
     * step 1 - Preparing JsonBodyContent
     * step 2 - Sending Request as a bad user
     * step 3 - Checking response
     * @return void
     */
    public function test_notebookNoteAddIncorrectCredentials(): void
    {
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test1@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test1","text1");

        $token = $this->databaseMockManager->testFunc_loginUser($user1);
        /// step 1
        $content = [
            "title"=> "test2",
            "text"=> "test2",
            "notebookCategoryId"=> $notebookCategory->getId()->__toString(),
        ];

        /// step 2
        $crawler = self::$webClient->request("PUT", "/api/notebook/note/add", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 3
        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }
    /**
     * step 1 - Preparing JsonBodyContent
     * step 2 - Sending Request as a bad user
     * step 3 - Checking response
     * @return void
     */
    public function test_notebookNoteAddIncorrectNoteCredentials(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test1","text1");

        $token = $this->databaseMockManager->testFunc_loginUser($user);

        $content = [
            "title"=> "test2",
            "text"=> "test2",
            "notebookCategoryId"=> "66666c4e-16e6-1ecc-9890-a7e8b0073d3b",
        ];

        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/notebook/note/add", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
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
    public function test_notebookNoteAddEmptyRequest()
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test1","text1");

        $token = $this->databaseMockManager->testFunc_loginUser($user);

        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/notebook/note/add", server: [
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
    public function test_notebookNoteAddLogOut(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test",$user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory,"test1","text1");

        $content = [
            "title"=> "test2",
            "text"=> "test2",
            "notebookCategoryId"=> $notebookCategory->getId()->__toString(),
        ];

        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/notebook/note/add", content: json_encode($content));
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