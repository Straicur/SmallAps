<?php

namespace App\Tests\Controller\RegisterController;

use App\Repository\RegisterCodeRepository;
use App\Repository\UserRepository;
use App\Tests\AbstractWebTest;

/**
 * RegisterConfirmTest
 */
class RegisterConfirmTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if user is active
     * @return void
     */
    public function test_registerConfirmCorrect(): void
    {
        $userRepository = $this->getService(UserRepository::class);
        $registerCodeRepository = $this->getService(RegisterCodeRepository::class);

        $this->assertInstanceOf(RegisterCodeRepository::class, $registerCodeRepository);
        $this->assertInstanceOf(UserRepository::class, $userRepository);
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $registerCode = $this->databaseMockManager->testFunc_addRegisterCode($user);
        /// step 2

        $content = [
            "registerCode" => $registerCode->getCode(),
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/register/code", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $userAfter = $userRepository->findOneBy([
            "id"=>$user->getId()
        ]);

        $hasRole = false;

        foreach ($userAfter->getRoles() as $role)
        {
            if($role->getName() == "User"){
                $hasRole = true;
            }
        }

        /// step 5
        $this->assertTrue($hasRole);
        $this->assertTrue($userAfter->isActive());

        $codeAfter = $registerCodeRepository->findOneBy([
            "id"=>$registerCode->getId()
        ]);
        $this->assertNotNull($codeAfter->getDateAccept());
        $this->assertTrue($codeAfter->getUsed());
    }

    /**
     * step 1 - Creating normal
     * step 2 - Preparing data
     * step 3 - Sending Request with bad permission
     * step 4 - Checking response
     *
     * @return void
     */
    public function test_registerConfirmIncorrectPermission(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", [], true, "zaq12wsx");
        $registerCode = $this->databaseMockManager->testFunc_addRegisterCode($user);
        /// step 2

        $content = [
            "registerCode" => $registerCode->getCode(),
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/register/code", server: [
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
     * step 1 - Preparing JsonBodyContent with bad registerCode
     * step 2 - Sending Request
     * step 3 - Checking response
     * @return void
     */
    public function test_registerConfirmIncorrectCredentials(): void
    {

        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $registerCode = $this->databaseMockManager->testFunc_addRegisterCode($user);
        /// step 1

        $content = [
            "registerCode" => "UPqFDj",
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PATCH", "/api/register/code", server: [
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
    public function test_registerConfirmEmptyRequest()
    {
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $registerCode = $this->databaseMockManager->testFunc_addRegisterCode($user);

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 1
        $crawler = self::$webClient->request("PATCH", "/api/register/code", server: [
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
    public function test_registerConfirmLogOut(): void
    {
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@cos.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        $registerCode = $this->databaseMockManager->testFunc_addRegisterCode($user);

        $content = [
            "registerCode" => $registerCode->getCode(),
        ];

        /// step 2
        $crawler = self::$webClient->request("PATCH", "/api/register/code", content: json_encode($content));
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