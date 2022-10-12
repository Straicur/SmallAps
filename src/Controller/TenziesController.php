<?php

namespace App\Controller;

use App\Annotation\AuthValidation;
use App\Entity\TenzieResult;
use App\Exception\DataNotFoundException;
use App\Exception\InvalidJsonDataException;
use App\Model\AuthorizationSuccessModel;
use App\Model\DataNotFoundModel;
use App\Model\JsonDataInvalidModel;
use App\Model\NotAuthorizeModel;
use App\Model\PermissionNotGrantedModel;
use App\Model\TenzieAllModel;
use App\Model\TenzieAllSuccessModel;
use App\Model\TenzieBestModel;
use App\Model\TenzieBestSuccessModel;
use App\Query\TenzieAddQuery;
use App\Query\TenzieAllQuery;
use App\Query\TenzieBestQuery;
use App\Repository\AuthenticationTokenRepository;
use App\Repository\TenzieResultRepository;
use App\Service\AuthorizedUserServiceInterface;
use App\Service\RequestServiceInterface;
use App\Tool\ResponseTool;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TenziesController
 *
 */
#[OA\Response(
    response: 400,
    description: "JSON Data Invalid",
    content: new Model(type: JsonDataInvalidModel::class)
)]
#[OA\Response(
    response: 404,
    description: "Data not found",
    content: new Model(type: DataNotFoundModel::class)
)]
#[OA\Response(
    response: 401,
    description: "User not authorized",
    content: new Model(type: NotAuthorizeModel::class)
)]
#[OA\Response(
    response: 403,
    description: "User have no permission",
    content: new Model(type: PermissionNotGrantedModel::class)
)]
#[OA\Tag(name: "Tenzies")]
class TenziesController extends AbstractController
{
    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param TenzieResultRepository $tenzieResultRepository
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route("/api/tenzie/add", name: "apiTenzieAdd", methods: ["PUT"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Post(
        description: "Method used to add tenzie game result",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: TenzieAddQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: AuthorizationSuccessModel::class)
            ),
        ]
    )]
    public function tenzieAdd(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        TenzieResultRepository         $tenzieResultRepository
    ): Response
    {
        $tenzieAddQuery = $requestServiceInterface->getRequestBodyContent($request, TenzieAddQuery::class);

        if ($tenzieAddQuery instanceof TenzieAddQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            $duplicateTenzie = $tenzieResultRepository->findOneBy([
                "title" => $tenzieAddQuery->getTitle()
            ]);

            if ($duplicateTenzie != null) {
                $endpointLogger->error("Tenzie result title already exists");
                throw new DataNotFoundException(["tenzie.add.invalid.title"]);
            }

            $newTenzieResult = new TenzieResult(
                $user,
                $tenzieAddQuery->getTitle(),
                $tenzieAddQuery->getLevel(),
                $tenzieAddQuery->getTime()
            );

            $tenzieResultRepository->add($newTenzieResult);

            return ResponseTool::getResponse();

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("tenzie.add.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param TenzieResultRepository $tenzieResultRepository
     * @return Response
     * @throws InvalidJsonDataException
     */
    #[Route("/api/tenzie/all", name: "apiTenzieAll", methods: ["POST"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Post(
        description: "Method return all user results",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: TenzieAllQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: TenzieAllSuccessModel::class)
            ),
        ]
    )]
    public function tenzieAll(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        TenzieResultRepository         $tenzieResultRepository,
    ): Response
    {
        $tenzieAllQuery = $requestServiceInterface->getRequestBodyContent($request, TenzieAllQuery::class);

        if ($tenzieAllQuery instanceof TenzieAllQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            $tenzieResults = $tenzieResultRepository->getActiveUserTenzieResults($user);

            $successModel = new TenzieAllSuccessModel();

            $maxLikeResult = $tenzieAllQuery->getPage() * $tenzieAllQuery->getLimit();

            foreach ($tenzieResults as $index => $tenzieResult) {
                if ($index < $maxLikeResult) {
                    $successModel->addTenzieAllModel(new TenzieAllModel(
                        $tenzieResult->getLevel(),
                        $tenzieResult->getTitle(),
                        $tenzieResult->getTime(),
                        $tenzieResult->getDateAdd()
                    ));
                } else {
                    break;
                }
            }
            $successModel->setPage($tenzieAllQuery->getPage());
            $successModel->setLimit($tenzieAllQuery->getLimit());

            $successModel->setMaxPage(count($tenzieResults) / $tenzieAllQuery->getLimit());

            return ResponseTool::getResponse($successModel);

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("tenzie.all.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param LoggerInterface $endpointLogger
     * @param TenzieResult $id
     * @param TenzieResultRepository $tenzieResultRepository
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @return Response
     * @throws DataNotFoundException
     */
    #[Route("/api/tenzie/{id}", name: "apiTenzieDelete", methods: ["DELETE"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Delete(
        description: "Method delete given result",
        security: [],
        requestBody: new OA\RequestBody(),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
            ),
        ]
    )]
    public function tenzieDelete(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        LoggerInterface                $endpointLogger,
        TenzieResult                   $id,
        TenzieResultRepository         $tenzieResultRepository,
        AuthorizedUserServiceInterface $authorizedUserService,
    ): Response
    {
        $user = $authorizedUserService->getAuthorizedUser();

        if ($user !== $id->getUser()) {
            $endpointLogger->error("Invalid user permission");
            throw new DataNotFoundException(["tenzie.delete.invalid.permission"]);
        }

        $id->setDeleted(true);
        $tenzieResultRepository->add($id);

        return ResponseTool::getResponse();
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param LoggerInterface $usersLogger
     * @param AuthenticationTokenRepository $authenticationTokenRepository
     * @param LoggerInterface $endpointLogger
     * @param TenzieResultRepository $tenzieResultRepository
     * @return Response
     * @throws InvalidJsonDataException
     */
    #[Route("/api/tenzie/best", name: "apiTenzieBest", methods: ["POST"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Post(
        description: "Method returns best results in system",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: TenzieBestQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: TenzieBestSuccessModel::class)
            ),
        ]
    )]
    public function tenzieBest(
        Request                       $request,
        RequestServiceInterface       $requestServiceInterface,
        LoggerInterface               $usersLogger,
        AuthenticationTokenRepository $authenticationTokenRepository,
        LoggerInterface               $endpointLogger,
        TenzieResultRepository        $tenzieResultRepository,
    ): Response
    {
        $tenzieBestQuery = $requestServiceInterface->getRequestBodyContent($request, TenzieBestQuery::class);

        if ($tenzieBestQuery instanceof TenzieBestQuery) {

            $successModel = new TenzieBestSuccessModel();

            foreach ($tenzieBestQuery->getLevels()["level"] as $level) {

                $tenzieBestModel = new TenzieBestModel($level);

                $tenzieResults = $tenzieResultRepository->getBestTenzieResults($level);

                foreach ($tenzieResults as $tenzieResult) {
                    $tenzieBestModel->addTenzieAllModel(new TenzieAllModel(
                        $tenzieResult->getLevel(),
                        $tenzieResult->getTitle(),
                        $tenzieResult->getTime(),
                        $tenzieResult->getDateAdd()
                    ));
                }
                $successModel->addTenzieBestModel($tenzieBestModel);
            }

            return ResponseTool::getResponse($successModel);

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("tenzie.all.invalid.query");
        }
    }
}