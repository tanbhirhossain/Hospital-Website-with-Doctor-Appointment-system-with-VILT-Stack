<?php
namespace Modules\WEBSITE_EXTRA\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\ClientReviewRequest;
use Modules\WEBSITE_EXTRA\Services\ClientReviewService;
class ClientReviewController extends Controller
{
    public function __construct(protected ClientReviewService $service) {}
    public function index(Request $request): Response {
        return Inertia::render('WEBSITE_EXTRA::ClientReview/Index', [
            'reviews' => $this->service->paginate($request->only(['search'])),
            'filters' => $request->only(['search']),
        ]);
    }
    public function store(ClientReviewRequest $request): RedirectResponse {
        $this->service->store($request->validated(), Auth::id());
        return back()->with('success','Client review created successfully.');
    }
    public function update(ClientReviewRequest $request, int $id): RedirectResponse {
        $this->service->update($id, $request->validated(), Auth::id());
        return back()->with('success','Client review updated successfully.');
    }
    public function destroy(int $id): RedirectResponse {
        $this->service->destroy($id);
        return back()->with('success','Client review deleted successfully.');
    }
}
