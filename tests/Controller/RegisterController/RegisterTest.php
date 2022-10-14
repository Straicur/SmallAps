<?php

namespace App\Tests\Controller\RegisterController;

use App\Repository\UserRepository;
use App\Tests\AbstractWebTest;

/**
 * RegisterTest
 */
class RegisterTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if user is registered
     * @return void
     */
    public function test_tenzieAddCorrect(): void
    {
        $userRepository = $this->getService(UserRepository::class);

        $this->assertInstanceOf(UserRepository::class, $userRepository);
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        /// step 2

        $content = [
            "email"=>"",
            "phoneNumber"=>"",
            "firstname"=>"",
            "lastname"=>"",
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/tenzie/add", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);


    }
}