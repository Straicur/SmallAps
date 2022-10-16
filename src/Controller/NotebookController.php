<?php

namespace App\Controller;

use App\Annotation\AuthValidation;
use App\Entity\NotebookCategory;
use App\Entity\NotebookNote;
use App\Exception\DataNotFoundException;
use App\Exception\InvalidJsonDataException;
use App\Model\DataNotFoundModel;
use App\Model\JsonDataInvalidModel;
use App\Model\NotAuthorizeModel;
use App\Model\NotebookCategoriesSuccessModel;
use App\Model\NotebookCategoryAddSuccessModel;
use App\Model\NotebookCategoryDetailsSuccessModel;
use App\Model\NotebookCategoryEditSuccessModel;
use App\Model\NotebookCategoryModel;
use App\Model\NotebookNoteAddSuccessModel;
use App\Model\NotebookNoteDetailsSuccessModel;
use App\Model\NotebookNoteEditSuccessModel;
use App\Model\NotebookNoteModel;
use App\Model\PermissionNotGrantedModel;
use App\Query\NotebookCategoryAddQuery;
use App\Query\NotebookCategoryEditQuery;
use App\Query\NotebookNoteAddQuery;
use App\Query\NotebookNoteDetailsQuery;
use App\Query\NotebookNoteEditQuery;
use App\Repository\NotebookCategoryRepository;
use App\Repository\NotebookNoteRepository;
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
 * NotebookController
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
#[OA\Tag(name: "Notebook")]
class NotebookController extends AbstractController
{
    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @return Response
     */
    #[Route("/api/notebook/categories", name: "apiNotebookCategories", methods: ["GET"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Get(
        description: "Method used to get all user notebook categories",
        security: [],
        requestBody: new OA\RequestBody(),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookCategoriesSuccessModel::class)
            ),
        ]
    )]
    public function notebookCategories(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
    ): Response
    {
        $user = $authorizedUserService->getAuthorizedUser();

        $userNotebookCategories = $user->getNotebookCategories();

        $successModel = new NotebookCategoriesSuccessModel();

        foreach ($userNotebookCategories as $category) {
            $successModel->addNotebookCategoryModel(new NotebookCategoryModel(
                $category->getId(),
                $category->getName()
            ));
        }

        return ResponseTool::getResponse($successModel);
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookCategoryRepository $notebookCategoryRepository
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route("/api/notebook/category/add", name: "apiNotebookCategoryAdd", methods: ["PUT"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Put(
        description: "Method used to add notebook category",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: NotebookCategoryAddQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookCategoryAddSuccessModel::class)
            ),

        ]
    )]
    public function notebookCategoryAdd(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookCategoryRepository     $notebookCategoryRepository
    ): Response
    {
        $notebookCategoryAddQuery = $requestServiceInterface->getRequestBodyContent($request, NotebookCategoryAddQuery::class);

        if ($notebookCategoryAddQuery instanceof NotebookCategoryAddQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            $duplicateNoteCategory = $notebookCategoryRepository->findOneBy([
                "name" => $notebookCategoryAddQuery->getName()
            ]);

            if ($duplicateNoteCategory != null) {
                $endpointLogger->error("NotebookCategory name already exists");
                throw new DataNotFoundException(["noteCategory.add.invalid.name"]);
            }

            $newCategory = new NotebookCategory($notebookCategoryAddQuery->getName(), $user);

            $notebookCategoryRepository->add($newCategory);

            return ResponseTool::getResponse();

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("noteCategory.add.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookCategoryRepository $notebookCategoryRepository
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route("/api/notebook/category", name: "apiNotebookCategoryEdit", methods: ["PATCH"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Patch(
        description: "Method used to edit name of category",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: NotebookCategoryEditQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookCategoryEditSuccessModel::class)
            ),

        ]
    )]
    public function notebookCategoryEdit(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookCategoryRepository     $notebookCategoryRepository
    ): Response
    {
        $notebookCategoryEditQuery = $requestServiceInterface->getRequestBodyContent($request, NotebookCategoryEditQuery::class);

        if ($notebookCategoryEditQuery instanceof NotebookCategoryEditQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            $category = $notebookCategoryRepository->findOneBy([
                "id" => $notebookCategoryEditQuery->getNotebookCategoryId()
            ]);

            if ($category != null || $category->getUser() !== $user) {
                $endpointLogger->error("Cant find category");
                throw new DataNotFoundException(["noteCategory.edit.invalid.credentials"]);
            }

            $duplicateNoteCategory = $notebookCategoryRepository->findOneBy([
                "name" => $notebookCategoryEditQuery->getName()
            ]);

            if ($duplicateNoteCategory != null) {
                $endpointLogger->error("NotebookCategory name already exists");
                throw new DataNotFoundException(["noteCategory.edit.invalid.name"]);
            }

            $category->setName($notebookCategoryEditQuery->getName());

            $notebookCategoryRepository->add($category);

            $successModel = new NotebookCategoryEditSuccessModel($category->getId(), $category->getName());

            return ResponseTool::getResponse($successModel);

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("noteCategory.edit.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookCategory $id
     * @param NotebookCategoryRepository $notebookCategoryRepository
     * @return Response
     * @throws DataNotFoundException
     */
    #[Route("/api/notebook/category/{id}", name: "apiNotebookCategoryDelete", methods: ["DELETE"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Delete(
        description: "Method used to delete given category",
        security: [],
        requestBody: new OA\RequestBody(),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
            )
        ]
    )]
    public function notebookCategoryDelete(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookCategory               $id,
        NotebookCategoryRepository     $notebookCategoryRepository
    ): Response
    {
        $user = $authorizedUserService->getAuthorizedUser();

        if ($id->getUser() !== $user) {
            $endpointLogger->error("Cant find category");
            throw new DataNotFoundException(["noteCategory.edit.invalid.credentials"]);
        }

        $notebookCategoryRepository->remove($id);

        return ResponseTool::getResponse();
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookCategory $id
     * @return Response
     * @throws DataNotFoundException
     */
    #[Route("/api/notebook/category/{id}", name: "apiNotebookCategoryDetails", methods: ["POST"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Post(
        description: "Method used to get all notes of given category",
        security: [],
        requestBody: new OA\RequestBody(),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookCategoryDetailsSuccessModel::class)
            ),

        ]
    )]
    public function notebookCategoryDetails(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookCategory               $id,
    ): Response
    {
        $user = $authorizedUserService->getAuthorizedUser();

        if ($id->getUser() !== $user) {
            $endpointLogger->error("Cant find category");
            throw new DataNotFoundException(["noteCategory.edit.invalid.credentials"]);
        }

        $notebookNotes = $id->getNotebookNotes();

        $successModel = new NotebookCategoryDetailsSuccessModel();

        foreach ($notebookNotes as $note) {
            $successModel->addNotebookNoteModel(new NotebookNoteModel(
                $note->getId(),
                $note->getTitle(),
                $note->getDateAdd()
            ));
        }

        return ResponseTool::getResponse();
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookCategoryRepository $notebookCategoryRepository
     * @param NotebookNoteRepository $notebookNoteRepository
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route("/api/notebook/note/add", name: "apiNotebookNoteAdd", methods: ["PUT"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Put(
        description: "Method used to add note to category",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: NotebookNoteAddQuery::class),
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookNoteAddSuccessModel::class)
            )
        ]
    )]
    public function notebookNoteAdd(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookCategoryRepository     $notebookCategoryRepository,
        NotebookNoteRepository         $notebookNoteRepository,
    ): Response
    {
        $notebookNoteAddQuery = $requestServiceInterface->getRequestBodyContent($request, NotebookNoteAddQuery::class);

        if ($notebookNoteAddQuery instanceof NotebookNoteAddQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            $notebookCategory = $notebookCategoryRepository->findOneBy([
                "id" => $notebookNoteAddQuery->getNotebookCategoryId()
            ]);

            if ($notebookCategory == null || $notebookCategory->getUser() !== $user) {
                $endpointLogger->error("Cant find category");
                throw new DataNotFoundException(["note.add.invalid.credentials"]);
            }

            $newNote = new NotebookNote($notebookCategory, $notebookNoteAddQuery->getTitle(), $notebookNoteAddQuery->getText());

            $notebookNoteRepository->add($newNote);

            $successModel = new  NotebookNoteAddSuccessModel(
                $newNote->getId(),
                $newNote->getTitle(),
                $newNote->getText(),
                $newNote->getDateAdd(),
                $newNote->getDateEdit(),
                $notebookCategory->getId()
            );

            return ResponseTool::getResponse($successModel);

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("note.add.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookNoteRepository $notebookNoteRepository
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route("/api/notebook/note", name: "apiNotebookNoteEdit", methods: ["PATCH"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Patch(
        description: "Method used to edit given note",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: NotebookNoteEditQuery::class),
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookNoteEditSuccessModel::class)
            )
        ]
    )]
    public function notebookNoteEdit(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookNoteRepository         $notebookNoteRepository
    ): Response
    {
        $notebookNoteEditQuery = $requestServiceInterface->getRequestBodyContent($request, NotebookNoteEditQuery::class);

        if ($notebookNoteEditQuery instanceof NotebookNoteEditQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            $note = $notebookNoteRepository->findOneBy([
                "id" => $notebookNoteEditQuery->getNoteId()
            ]);

            if ($note == null || $note->getCategory()->getUser() !== $user) {
                $endpointLogger->error("Cant find note");
                throw new DataNotFoundException(["note.edit.invalid.credentials"]);
            }

            $note->setTitle($notebookNoteEditQuery->getTitle());
            $note->setText($notebookNoteEditQuery->getText());

            $notebookNoteRepository->add($note);

            $successModel = new  NotebookNoteEditSuccessModel(
                $note->getId(),
                $note->getTitle(),
                $note->getText(),
                $note->getDateAdd(),
                $note->getDateEdit(),
                $note->getCategory()->getId()
            );

            return ResponseTool::getResponse($successModel);

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("note.edit.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookNote $id
     * @param NotebookNoteRepository $notebookNoteRepository
     * @return Response
     * @throws DataNotFoundException
     */
    #[Route("/api/notebook/note/{id}", name: "apiNotebookNoteDelete", methods: ["DELETE"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Delete(
        description: "Method used to delete given note",
        security: [],
        requestBody: new OA\RequestBody(),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
            )
        ]
    )]
    public function notebookNoteDelete(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookNote                   $id,
        NotebookNoteRepository         $notebookNoteRepository
    ): Response
    {
        $user = $authorizedUserService->getAuthorizedUser();

        if ($id->getCategory()->getUser() !== $user) {
            $endpointLogger->error("Cant find note");
            throw new DataNotFoundException(["note.delete.invalid.credentials"]);
        }

        $notebookNoteRepository->remove($id);

        return ResponseTool::getResponse();
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param NotebookNoteRepository $notebookNoteRepository
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route("/api/notebook/note/{id}", name: "apiNotebookNoteDetails", methods: ["POST"])]
    #[AuthValidation(checkAuthToken: true, roles: ["User"])]
    #[OA\Post(
        description: "Method used to get note details",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: NotebookNoteDetailsQuery::class),
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: NotebookNoteDetailsSuccessModel::class)
            )
        ]
    )]
    public function notebookNoteDetails(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        NotebookNote                   $id
    ): Response
    {
        $notebookNoteDetailsQuery = $requestServiceInterface->getRequestBodyContent($request, NotebookNoteDetailsQuery::class);

        if ($notebookNoteDetailsQuery instanceof NotebookNoteDetailsQuery) {

            $user = $authorizedUserService->getAuthorizedUser();

            if ($id->getCategory()->getUser() !== $user) {
                $endpointLogger->error("Cant find note");
                throw new DataNotFoundException(["note.details.invalid.credentials"]);
            }

            $successModel = new  NotebookNoteAddSuccessModel(
                $id->getId(),
                $id->getTitle(),
                $id->getText(),
                $id->getDateAdd(),
                $id->getDateEdit(),
                $id->getCategory()->getId()
            );

            return ResponseTool::getResponse($successModel);

        } else {
            $endpointLogger->error("Invalid given Query");
            throw new InvalidJsonDataException("note.details.invalid.query");
        }
    }
}