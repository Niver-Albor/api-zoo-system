<?php

namespace App\Http\Controllers;

use App\Business\DTOs\IndexMedicalRecordData;
use App\Business\DTOs\SaveMedicalRecordData;
use App\Business\Services\AmendMedicalRecordService;
use App\Business\Services\CreateMedicalRecordService;
use App\Business\Services\IndexMedicalRecordService;
use App\Http\Requests\AmendMedicalRecordRequest;
use App\Http\Requests\IndexMedicalRecordRequest;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicalRecordController extends Controller
{
    use ApiResponse;

    function __construct(
        protected CreateMedicalRecordService $createMedicalRecordService,
        protected AmendMedicalRecordService $amendMedicalRecordService,
        protected IndexMedicalRecordService $indexMedicalRecordService
    ) {}

    public function store(StoreMedicalRecordRequest $request)
    {
        $this->authorize('create', MedicalRecord::class);

        $dto = SaveMedicalRecordData::fromRequest($request);

        $response = $this->createMedicalRecordService->createMedicalRecord($dto);

        if ($response)
            {
                return $this->successResponse(
                    new MedicalRecordResource($response),
                    "Registro médico creado correctamente",
                    Response::HTTP_CREATED
                );
            }

        return $this->errorResponse();
    }

    public function createAmendment(AmendMedicalRecordRequest $request, MedicalRecord $medical_record)
    {
        $this->authorize('update', $medical_record);

        $dto = SaveMedicalRecordData::fromRequest($request);

        $response = $this->amendMedicalRecordService->amendRecord($medical_record, $dto);

        if ($response)
            {
                return $this->successResponse(
                    new MedicalRecordResource($response),
                    'Registro médico corregido correctamente',
                    Response::HTTP_OK
                );
            }
        
        return $this->errorResponse();
    }

    public function index(IndexMedicalRecordRequest $request)
    {
        $this->authorize('view', MedicalRecord::class);

        $dto = IndexMedicalRecordData::fromRequest($request);

        $records = $this->indexMedicalRecordService->indexRecords($dto);

        return MedicalRecordResource::collection($records);
    }
}
