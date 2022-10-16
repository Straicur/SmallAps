<?php

namespace App\Tests\Controller\NotebookController;

use App\Tests\AbstractWebTest;

/**
 * NotebookNoteEditTest
 */
class NotebookNoteEditTest extends AbstractWebTest
{
    public function test_registerCodeCorrect(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");

        /// step 2

        $content = [
            "email" => $user->getUserInformation()->getEmail(),
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("POST", "/api/register/code/send", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}