<?php
namespace Modules\WEBSITE_EXTRA\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\HeroSliderRequest;
use Modules\WEBSITE_EXTRA\Services\HeroSliderService;
class HeroSliderController extends Controller
{
    public function __construct(protected HeroSliderService $service) {}
    public function index(Request $request): Response {
        return Inertia::render('WEBSITE_EXTRA::HeroSlider/Index', [
            'sliders' => $this->service->paginate($request->only(['search'])),
            'filters' => $request->only(['search']),
        ]);
    }
    public function store(HeroSliderRequest $request): RedirectResponse {
        $this->service->store($request->validated(), Auth::id());
        return back()->with('success','Hero slider created successfully.');
    }
    public function update(HeroSliderRequest $request, int $id): RedirectResponse {
        $this->service->update($id, $request->validated(), Auth::id());
        return back()->with('success','Hero slider updated successfully.');
    }
    public function destroy(int $id): RedirectResponse {
        $this->service->destroy($id);
        return back()->with('success','Hero slider deleted successfully.');
    }
}
