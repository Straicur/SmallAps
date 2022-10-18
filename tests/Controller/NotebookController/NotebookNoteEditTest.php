<?php

namespace App\Tests\Controller\NotebookController;

use App\Repository\NotebookNoteRepository;
use App\Tests\AbstractWebTest;

/**
 * NotebookNoteEditTest
 */
class NotebookNoteEditTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if note is edited
     * @return void
     */
    public function test_notebookNoteEditCorrect(): void
    {
        $notebookNoteRepository = $this->getService(NotebookNoteRepository::class);

        $this->assertInstanceOf(NotebookNoteRepository::class, $notebookNoteRepository);
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test", $user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory, "test", "text");

        /// step 2
        $content = [
            "title" => "test1",
            "text" => "text1",
            "noteId" => $note->getId(),
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/notebook/note", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $noteAfter = $notebookNoteRepository->findOneBy([
            "id" => $note->getId()
        ]);
        /// step 5
        $this->assertSame($noteAfter->getTitle(), $content["title"]);
        $this->assertSame($noteAfter->getText(), $content["text"]);
        $this->assertNotNull($noteAfter->getDateEdit());
    }

    /**
     * step 1 - Creating guest
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_notebookNoteEditIncorrectPermission(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest"], true, "zaq12wsx");

        /// step 2
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test", $user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory, "test", "text");

        $content = [
            "title" => "test1",
            "text" => "text1",
            "noteId" => $note->getId(),
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/notebook/note", server: [
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
    public function test_notebookNoteEditIncorrectCredentials(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test", $user1);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory, "test", "text");

        /// step 2
        $content = [
            "title" => "test1",
            "text" => "text1",
            "noteId" => $note->getId(),
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user2);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/notebook/note", server: [
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
     * step 2 - Sending Request as a bad noteId
     * step 3 - Checking response
     * @return void
     */
    public function test_notebookNoteEditIncorrectNoteCredentials(): void
    {
        /// step 1
        $user1 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $user2 = $this->databaseMockManager->testFunc_addUser("User", "Test", "test2@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test", $user1);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory, "test", "text");

        /// step 2
        $content = [
            "title" => "test1",
            "text" => "text1",
            "noteId" => "66666c4e-16e6-1ecc-9890-a7e8b0073d3b",
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user1);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/notebook/note", server: [
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
    public function test_notebookNoteEditEmptyRequest()
    {
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test", $user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory, "test", "text");

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 1
        $crawler = self::$webClient->request("PATCH", "/api/notebook/note", server: [
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
    public function test_notebookNoteEditLogOut(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $notebookCategory = $this->databaseMockManager->testFunc_addNotebookCategory("test", $user);
        $note = $this->databaseMockManager->testFunc_addNotebookNote($notebookCategory, "test", "text");

        $content = [
            "title" => "test1",
            "text" => "text1",
            "noteId" => $note->getId(),
        ];

        /// step 2
        $crawler = self::$webClient->request("PATCH", "/api/notebook/note", content: json_encode($content));
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