<?php

namespace App\Service;

use App\Repository\CompanyRepository;
use App\Entity\Company;

class CurrentCompanyService
{
    private $companyRepository;
    private $defaultCompanyId;

    public function __construct(CompanyRepository $companyRepository, int $defaultCompanyId)
    {
        $this->companyRepository = $companyRepository;
        $this->defaultCompanyId = $defaultCompanyId;
    }

    public function getCurrentCompany(): ?Company
    {
        return $this->companyRepository->findById($this->defaultCompanyId);
    }
}
