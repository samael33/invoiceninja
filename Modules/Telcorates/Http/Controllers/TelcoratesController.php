<?php

namespace Modules\Telcorates\Http\Controllers;

use Auth;
use App\Http\Controllers\BaseController;
use App\Services\DatatableService;
use Modules\Telcorates\Datatables\TelcoratesDatatable;
use Modules\Telcorates\Repositories\TelcoratesRepository;
use Modules\Telcorates\Http\Requests\TelcoratesRequest;
use Modules\Telcorates\Http\Requests\CreateTelcoratesRequest;
use Modules\Telcorates\Http\Requests\UpdateTelcoratesRequest;

class TelcoratesController extends BaseController
{
    protected $TelcoratesRepo;
    //protected $entityType = 'telcorates';

    public function __construct(TelcoratesRepository $telcoratesRepo)
    {
        //parent::__construct();

        $this->telcoratesRepo = $telcoratesRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('list_wrapper', [
            'entityType' => 'telcorates',
            'datatable' => new TelcoratesDatatable(),
            'title' => mtrans('telcorates', 'telcorates_list'),
        ]);
    }

    public function datatable(DatatableService $datatableService)
    {
        $search = request()->input('sSearch');
        $userId = Auth::user()->filterId();

        $datatable = new TelcoratesDatatable();
        $query = $this->telcoratesRepo->find($search, $userId);

        return $datatableService->createDatatable($datatable, $query);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(TelcoratesRequest $request)
    {
        $data = [
            'telcorates' => null,
            'method' => 'POST',
            'url' => 'telcorates',
            'title' => mtrans('telcorates', 'new_telcorates'),
        ];

        return view('telcorates::edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(CreateTelcoratesRequest $request)
    {
        $telcorates = $this->telcoratesRepo->save($request->input());

        return redirect()->to($telcorates->present()->editUrl)
            ->with('message', mtrans('telcorates', 'created_telcorates'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit(TelcoratesRequest $request)
    {
        $telcorates = $request->entity();

        $data = [
            'telcorates' => $telcorates,
            'method' => 'PUT',
            'url' => 'telcorates/' . $telcorates->public_id,
            'title' => mtrans('telcorates', 'edit_telcorates'),
        ];

        return view('telcorates::edit', $data);
    }

    /**
     * Show the form for editing a resource.
     * @return Response
     */
    public function show(TelcoratesRequest $request)
    {
        return redirect()->to("telcorates/{$request->telcorates}/edit");
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(UpdateTelcoratesRequest $request)
    {
        $telcorates = $this->telcoratesRepo->save($request->input(), $request->entity());

        return redirect()->to($telcorates->present()->editUrl)
            ->with('message', mtrans('telcorates', 'updated_telcorates'));
    }

    /**
     * Update multiple resources
     */
    public function bulk()
    {
        $action = request()->input('action');
        $ids = request()->input('public_id') ?: request()->input('ids');
        $count = $this->telcoratesRepo->bulk($ids, $action);

        return redirect()->to('telcorates')
            ->with('message', mtrans('telcorates', $action . '_telcorates_complete'));
    }
}
