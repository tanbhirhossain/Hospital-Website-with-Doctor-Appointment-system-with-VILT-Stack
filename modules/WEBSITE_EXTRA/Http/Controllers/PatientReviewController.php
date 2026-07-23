<?php
namespace Modules\WEBSITE_EXTRA\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\PatientReviewRequest;
use Modules\WEBSITE_EXTRA\Services\PatientReviewService;
use Modules\WEBSITE\Models\Doctor;
class PatientReviewController extends Controller
{
    public function __construct(protected PatientReviewService $service) {}
    public function index(Request $request): Response {
        return Inertia::render('WEBSITE_EXTRA::PatientReview/Index', [
            'reviews' => $this->service->paginate($request->only(['search','status','doctor_id'])),
            'filters' => $request->only(['search','status','doctor_id']),
            'doctors' => Doctor::select('id','name')->where('is_active',true)->orderBy('name')->get(),
        ]);
    }
    public function store(PatientReviewRequest $request): RedirectResponse {
        $this->service->store($request->validated(), Auth::id());
        return back()->with('success','Patient review created successfully.');
    }
    public function update(PatientReviewRequest $request, int $id): RedirectResponse {
        $this->service->update($id, $request->validated(), Auth::id());
        return back()->with('success','Patient review updated successfully.');
    }
    public function approve(int $id): RedirectResponse {
        $this->service->approve($id, Auth::id());
        return back()->with('success','Review approved.');
    }
    public function reject(Request $request, int $id): RedirectResponse {
        $this->service->reject($id, $request->input('admin_note',''), Auth::id());
        return back()->with('success','Review rejected.');
    }
    public function destroy(int $id): RedirectResponse {
        $this->service->destroy($id);
        return back()->with('success','Patient review deleted successfully.');
    }
}
