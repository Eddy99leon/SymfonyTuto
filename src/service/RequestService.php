<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestService
{
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFromRequestBody(...$fieldNames)
    {
        $requestData = $this->request->request->all();
        $fileData = $this->request->files->all();
        $request = array_merge($requestData, $fileData);

        $data = [];
        foreach ($fieldNames as $fieldName) $data[] = $request[$fieldName] ?? null;
        return $data;
    }

    public function getFromDataRequestBody(...$fieldNames)
    {
        $data = [];
        foreach ($fieldNames as $fieldName) $data[] = $this->request->request->get($fieldName, null);
        return $data;
    }

    public function getFromFilesRequestBody(...$fieldNames)
    {
        $data = [];
        foreach ($fieldNames as $fieldName) $data[] = $this->request->files->get($fieldName, null);
        return $data;
    }

    public function getFromAttributes(...$fieldNames)
    {
        $data = [];
        foreach ($fieldNames as $fieldName) $data[] = $this->request->attributes->get($fieldName, null);
        return $data;
    }

}
