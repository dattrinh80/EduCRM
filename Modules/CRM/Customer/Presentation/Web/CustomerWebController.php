<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;

class CustomerWebController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function index()
    {
        $customers = $this->customerRepository->getAll();
        return view('customer::index', compact('customers'));
    }
}
