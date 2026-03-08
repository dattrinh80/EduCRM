<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\CRM\Customer\Application\Queries\GetCustomersQuery;
use Modules\CRM\Customer\Application\Queries\GetCustomersHandler;
use Illuminate\Http\Request;

class CustomerWebController extends Controller
{
    public function index(Request $request, GetCustomersHandler $handler)
    {
        $search = $request->get('search');
        $customers = $handler->handle(new GetCustomersQuery($search));
        
        return view('customer::index', compact('customers', 'search'));
    }
}
