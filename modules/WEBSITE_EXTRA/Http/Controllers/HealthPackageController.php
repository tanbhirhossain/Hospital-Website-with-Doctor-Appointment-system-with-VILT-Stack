<?php
namespace Modules\WEBSITE_EXTRA\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\HealthPackageRequest;
use Modules\WEBSITE_EXTRA\Services\HealthPackageService;
class HealthPackageController extends Controller
{
    public function __construct(protected HealthPackageService $service) {}
    public function index(Request $request): Response {
        return Inertia::render('WEBSITE_EXTRA::HealthPackage/Index', [
            'packages' => $this->service->paginate($request->only(['search','category'])),
            'filters' => $request->only(['search','category']),
            'categories' => ['general','cardiac','diabetes','cancer','maternity','pediatric','executive','other'],
        ]);
    }
    public function create(): Response {
        return Inertia::render('WEBSITE_EXTRA::HealthPackage/Create',['categories'=>['general','cardiac','diabetes','cancer','maternity','pediatric','executive','other']]);
    }
    public function store(HealthPackageRequest $request): RedirectResponse {
        $this->service->store($request->validated(), Auth::id());
        return redirect()->route('admin.health-packages.index')->with('success','Health package created successfully.');
    }
    public function edit(int $id): Response {
        $pkg = $this->service->findById($id);
        return Inertia::render('WEBSITE_EXTRA::HealthPackage/Edit', [
            'package' => $pkg,
            'categories' => ['general','cardiac','diabetes','cancer','maternity','pediatric','executive','other'],
            'thumbnail' => $pkg->getFirstMedia('thumbnail') ? ['id'=>$pkg->getFirstMedia('thumbnail')->id,'url'=>$pkg->getFirstMediaUrl('thumbnail'),'name'=>$pkg->getFirstMedia('thumbnail')->file_name] : null,
        ]);
    }
    public function update(HealthPackageRequest $request, int $id): RedirectResponse {
        $this->service->update($id, $request->validated(), Auth::id());
        return redirect()->route('admin.health-packages.index')->with('success','Health package updated successfully.');
    }
    public function destroy(int $id): RedirectResponse {
        $this->service->destroy($id);
        return redirect()->route('admin.health-packages.index')->with('success','Health package deleted successfully.');
    }
}
