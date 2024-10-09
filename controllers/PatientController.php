<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token in the following format: `Bearer {your_token}`"
 * )
 */
class PatientController {
    private $patientRepository;

    public function __construct($patientRepository) {
        $this->patientRepository = $patientRepository;
    }

    /**
     * @OA\Get(
     *     path="/patients",
     *     summary="Get all patients",
     *     description="Returns a list of all patients",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="pageSize", in="query", required=false, description="Number of records per page", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="List of patients", 
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Patient")),
     *             @OA\Property(property="totalRecords", type="integer"),
     *             @OA\Property(property="page", type="integer"),
     *             @OA\Property(property="pageSize", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function getPatients($request) {
        $queryParams = $request->getQueryParams();
        $page = $queryParams['page'] ?? 1;
        $pageSize = $queryParams['pageSize'] ?? 10;
        return json_encode($this->patientRepository->getAllPatients($page, $pageSize));
    }

    /**
     * @OA\Get(
     *     path="/patients/{id}",
     *     summary="Get a patient by ID",
     *     description="Returns a single patient",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Patient ID", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Patient found",
     *         @OA\JsonContent(ref="#/components/schemas/Patient")
     *     ),
     *     @OA\Response(response="404", description="Patient not found")
     * )
     */
    public function getPatient($request, $id) {
        return json_encode($this->patientRepository->getPatientById($id));
    }

    /**
     * @OA\Post(
     *     path="/patients",
     *     summary="Create a new patient",
     *     description="Adds a new patient to the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="phoneNumber", type="string")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Patient created",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="400", description="Invalid input")
     * )
     */
    public function createPatient($request) {
        $data = json_decode($request->getBody()->getContents(), true);
        $patient = new Patient(null, $data['name'], $data['address'], $data['phoneNumber']);
        $patientId = $this->patientRepository->createPatient($patient);
        return json_encode(['id' => $patientId]);
    }

    /**
     * @OA\Put(
     *     path="/patients/{id}",
     *     summary="Update an existing patient",
     *     description="Updates a patient in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Patient ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="phoneNumber", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Patient updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="404", description="Patient not found"),
     *     @OA\Response(response="400", description="Invalid input")
     * )
     */
    public function updatePatient($request, $id) {
        $data = json_decode($request->getBody()->getContents(), true);
        $patient = new Patient(null, $data['name'], $data['address'], $data['phoneNumber']);
        $patient->id = $id;
        $this->patientRepository->updatePatient($patient);
        return json_encode(['success' => true]);
    }

    /**
     * @OA\Delete(
     *     path="/patients/{id}",
     *     summary="Delete a patient",
     *     description="Removes a patient from the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Patient ID", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Patient deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="204", description="Patient deleted"),
     *     @OA\Response(response="404", description="Patient not found")
     * )
     */
    public function deletePatient($request, $id) {
        $this->patientRepository->deletePatient($id);
        return json_encode(['success' => true]);
    }
}

/**
 * @OA\Schema(
 *     schema="Patient",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="phoneNumber", type="string")
 * )
 */
