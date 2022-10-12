<?php

namespace App\Tests\Controller\TenziesController;

use App\Repository\TenzieResultRepository;
use App\Tests\AbstractWebTest;

/**
 * LoginTest
 *
 */
class TenzieAddTest extends AbstractWebTest
{
    /**
     * step 1 - Preparing data
     * step 2 - Preparing JsonBodyContent
     * step 3 - Sending Request
     * step 4 - Checking response
     * step 5 - Checking response if responseContent has key token
     * @return void
     */
    public function test_tenzieAddCorrect(): void
    {
        $tenzieResultRepository = $this->getService(TenzieResultRepository::class);

        $this->assertInstanceOf(TenzieResultRepository::class, $tenzieResultRepository);
        /// step 1
        $user = $this->databaseMockManager->testFunc_addUser("User", "Test", "test@asuri.pl", "+48123123123", ["Guest", "User"], true, "zaq12wsx");
        /// step 2

        $content = [
            "title" => "test",
            "level" => 1,
            "time" => '1665405291',
            "dateAdd" => "05.04.2022"
        ];

        $token = $this->databaseMockManager->testFunc_loginUser($user);
        /// step 3
        $crawler = self::$webClient->request("PUT", "/api/tenzie/add", server: [
            "HTTP_authorization" => $token->getToken()
        ], content: json_encode($content));
        /// step 4
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $tenzieResults = $tenzieResultRepository->findAll();

        $this->assertCount(1,$tenzieResults);
        $this->assertSame($tenzieResults[0]->getUser()->getId()->__toString(),$user->getId()->__toString());

    }
}